<?php

declare(strict_types=1);

namespace DaPigGuy\PiggyCrates\commands;

use CortexPE\Commando\args\IntegerArgument;
use CortexPE\Commando\args\RawStringArgument;
use CortexPE\Commando\BaseCommand;
use CortexPE\Commando\exception\ArgumentOrderException;
use DaPigGuy\PiggyCrates\PiggyCrates;
use pocketmine\command\CommandSender;

class KeyAllCommand extends BaseCommand
{
    /** @var PiggyCrates */
    private PiggyCrates $plugin;

    public function __construct(PiggyCrates $plugin) {
		parent::__construct($plugin, "keyall", "Give all online players a crate key");
        $this->plugin = $plugin;
		$this->setPermission("piggycrates.command.keyall");
		$this->setPermissionMessage("§cYou don't have permission to us this command!");
	}
    
    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void
    {
        if (!isset($args["type"])) {
            $sender->sendMessage("Usage: /keyall <type>");
            return;
        }
        /** @var int $amount */
        $amount = $args["amount"] ?? 1;
        if (!is_numeric($amount)) {
            $sender->sendMessage($this->plugin->getMessage("commands.keyall.error.not-numeric"));
            return;
        }
        $crate = $this->plugin->getCrate($args["type"]);
        if ($crate === null) {
            $sender->sendMessage($this->plugin->getMessage("commands.keyall.error.invalid-crate"));
            return;
        }
        foreach ($this->plugin->getServer()->getOnlinePlayers() as $player) {
            $crate->giveKey($player, $amount);
            $player->sendMessage($this->plugin->getMessage("commands.keyall.success.sender", ["{CRATE}" => $crate->getName()]));
        }
        $sender->sendMessage($this->plugin->getMessage("commands.keyall.success.target", ["{CRATE}" => $crate->getName()]));

    }

    /**
     * @throws ArgumentOrderException
     */
    public function prepare(): void
    {
        $this->registerArgument(0, new RawStringArgument("type"));
        $this->registerArgument(1, new IntegerArgument("amount", true));
    }
}