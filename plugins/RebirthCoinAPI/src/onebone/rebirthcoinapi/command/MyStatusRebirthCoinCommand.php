<?php

namespace onebone\rebirthcoinapi\command;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;
use pocketmine\Player;

use onebone\rebirthcoinapi\RebirthCoinAPI;

class MyStatusRebirthCoinCommand extends Command{
	private $plugin;

	public function __construct(RebirthCoinAPI $plugin){
		$desc = $plugin->getCommandMessage("mystatusrbcoin");
		parent::__construct("mystatusrbcoin", $desc["description"], $desc["usage"]);

		$this->setPermission("rebirthcoinapi.command.mystatusrbcoin");

		$this->plugin = $plugin;
	}

	public function execute(CommandSender $sender, string $label, array $params): bool{
		if(!$this->plugin->isEnabled()) return false;
		if(!$this->testPermission($sender)){
			return false;
		}

		if(!$sender instanceof Player){
			$sender->sendMessage(TextFormat::RED . "Please run this command in-game.");
			return true;
		}

		$rebirthcoin = $this->plugin->getAllRebirthCoin();

		$allRebirthCoin = 0;
		foreach($rebirthcoin as $m){
			$allRebirthCoin += $m;
		}
		$topRebirthCoin = 0;
		if($allRebirthCoin > 0){
			$topRebirthCoin = round((($rebirthcoin[strtolower($sender->getName())] / $allRebirthCoin) * 100), 2);
		}

		$sender->sendMessage($this->plugin->getMessage("mystatuspp-show", [$topPoint], $sender->getName()));
		return true;
	}
}
