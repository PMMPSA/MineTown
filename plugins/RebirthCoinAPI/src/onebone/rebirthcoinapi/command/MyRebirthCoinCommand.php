<?php

namespace onebone\rebirthcoinapi\command;

use pocketmine\event\TranslationContainer;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;
use pocketmine\Player;

use onebone\rebirthcoinapi\RebirthCoinAPI;

class MyRebirthCoinCommand extends Command{
	private $plugin;

	public function __construct(RebirthCoinAPI $plugin){
		$desc = $plugin->getCommandMessage("myrbcoin");
		parent::__construct("myrbcoin", $desc["description"], $desc["usage"]);

		$this->setPermission("rebirthcoinapi.command.myrbcoin");

		$this->plugin = $plugin;
	}

	public function execute(CommandSender $sender, string $label, array $params): bool{
		if(!$this->plugin->isEnabled()) return false;
		if(!$this->testPermission($sender)){
			return false;
		}

		if($sender instanceof Player){
			$rebirthcoin = $this->plugin->myRebirthCoin($sender);
			$sender->sendMessage($this->plugin->getMessage("myrebirthcoin-myrebirthcoin", [$rebirthcoin]));
			return true;
		}
		$sender->sendMessage(TextFormat::RED."Please run this command in-game.");
		return true;
	}
}
