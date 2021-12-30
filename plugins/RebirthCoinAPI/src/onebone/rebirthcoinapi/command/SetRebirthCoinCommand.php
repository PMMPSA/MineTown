<?php

namespace onebone\rebirthcoinapi\command;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;
use pocketmine\event\TranslationContainer;
use pocketmine\Player;

use onebone\rebirthcoinapi\RebirthCoinAPI;

class SetRebirthCoinCommand extends Command{
	private $plugin;

	public function __construct(RebirthCoinAPI $plugin){
		$desc = $plugin->getCommandMessage("setrbcoin");
		parent::__construct("setrbcoin", $desc["description"], $desc["usage"]);

		$this->setPermission("rebirthcointapi.command.setrbcoin");

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

		$result = $this->plugin->setRebirthCoin($player, $amount);
		switch($result){
			case RebirthCoinAPI::RET_INVALID:
			$sender->sendMessage($this->plugin->getMessage("setrebirthcoin-invalid-number", [$amount], $sender->getName()));
			break;
			case RebirthCoinAPI::RET_NO_ACCOUNT:
			$sender->sendMessage($this->plugin->getMessage("player-never-connected", [$player], $sender->getName()));
			break;
			case RebirthCoinAPI::RET_CANCELLED:
			$sender->sendMessage($this->plugin->getMessage("setrebirthcoin-failed", [], $sender->getName()));
			break;
			case RebirthCoinAPI::RET_SUCCESS:
			$sender->sendMessage($this->plugin->getMessage("setrebirthcoin-setrebirthcoin", [$player, $amount], $sender->getName()));

			if($p instanceof Player){
				$p->sendMessage($this->plugin->getMessage("setrebirthcoin-set", [$amount], $p->getName()));
			}
			break;
			default:
			$sender->sendMessage("WTF");
		}
		return true;
	}
}
