<?php

namespace onebone\rebirthcoinapi\command;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;
use pocketmine\Player;

use onebone\rebirthcoinapi\RebirthCoinAPI;

class TakeRebirthCoinCommand extends Command{
	private $plugin;

	public function __construct(RebirthCoinAPI $plugin){
		$desc = $plugin->getCommandMessage("takerbcoin");
		parent::__construct("takerbcoin", $desc["description"], $desc["usage"]);

		$this->setPermission("rebirthcoinapi.command.takerbcoin");

		$this->plugin = $plugin;
	}

	public function execute(CommandSender $sender, string $label, array $params): bool{
		if(!$this->plugin->isEnabled()) return false;
		if(!$this->testPermission($sender)){
			return false;
		}

		$player = array_shift($params);
		$amount = array_shift($params);

		if(!is_numeric($amount)){
			$sender->sendMessage(TextFormat::RED . "Usage: " . $this->getUsage());
			return true;
		}

		if(($p = $this->plugin->getServer()->getPlayer($player)) instanceof Player){
			$player = $p->getName();
		}

		if($amount < 0){
			$sender->sendMessage($this->plugin->getMessage("takerebirthcoin-invalid-number", [$amount], $sender->getName()));
			return true;
		}

		$result = $this->plugin->reduceRebirthCoin($player, $amount);
		switch($result){
			case RebirthCoinAPI::RET_INVALID:
			$sender->sendMessage($this->plugin->getMessage("takerebirthcoin-player-lack-of-rebirthcoin", [$player, $amount, $this->plugin->myPoint($player)], $sender->getName()));
			break;
			case RebirthCoinAPI::RET_SUCCESS:
			$sender->sendMessage($this->plugin->getMessage("takerebirthcoin-took-rebirthcoin", [$player, $amount], $sender->getName()));

			if($p instanceof Player){
				$p->sendMessage($this->plugin->getMessage("takerebirthcoin-rebirthcoin-taken", [$amount], $sender->getName()));
			}
			break;
			case RebirthCoinAPI::RET_CANCELLED:
			$sender->sendMessage($this->plugin->getMessage("takerebirthcoin-failed", [], $sender->getName()));
			break;
			case RebirthCoinAPI::RET_NO_ACCOUNT:
			$sender->sendMessage($this->plugin->getMessage("player-never-connected", [$player], $sender->getName()));
			break;
		}

		return true;
	}
}
