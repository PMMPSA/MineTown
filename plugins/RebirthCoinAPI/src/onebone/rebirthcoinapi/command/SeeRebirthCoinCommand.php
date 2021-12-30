<?php

namespace onebone\rebirthcoinapi\command;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;
use pocketmine\Player;

use onebone\rebirthcoinapi\RebirthCoinAPI;

class SeeRebirthCoinCommand extends Command{
	private $plugin;

	public function __construct(RebirthCoinAPI $plugin){
		$desc = $plugin->getCommandMessage("seerbcoin");
		parent::__construct("seerbcoin", $desc["description"], $desc["usage"]);

		$this->setPermission("rebirthcoinapi.command.seerbcoin");

		$this->plugin = $plugin;
	}

	public function execute(CommandSender $sender, string $label, array $params): bool{
		if(!$this->plugin->isEnabled()) return false;
		if(!$this->testPermission($sender)){
			return false;
		}

		$player = array_shift($params);
		if(trim($player) === ""){
			$sender->sendMessage(TextFormat::RED . "Usage: " . $this->getUsage());
			return true;
		}

		if(($p = $this->plugin->getServer()->getPlayer($player)) instanceof Player){
			$player = $p->getName();
		}

		$rebirthcoin = $this->plugin->myRebirthCoin($player);
		if($rebirthcoin !== false){
			$sender->sendMessage($this->plugin->getMessage("seerebirthcoin-seerebirthcoin", [$player, $rebirthcoin], $sender->getName()));
		}else{
			$sender->sendMessage($this->plugin->getMessage("player-never-connected", [$player], $sender->getName()));
		}
		return true;
	}
}
