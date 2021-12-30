<?php

namespace onebone\rebirthcoinapi\command;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;
use pocketmine\Player;

use onebone\rebirthcoinapi\RebirthCoinAPI;

class GiveRebirthCoinCommand extends Command{
	private $plugin;

	public function __construct(RebirthCoinAPI $plugin){
		$desc = $plugin->getCommandMessage("giverbcoin");
		parent::__construct("giverbcoin", $desc["description"], $desc["usage"]);

		$this->setPermission("rebirthcoinapi.command.giverbcoin");

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

		$result = $this->plugin->addRebirthCoin($player, $amount);
		switch($result){
			case RebirthCoinAPI::RET_INVALID:
			$sender->sendMessage($this->plugin->getMessage("giverebirthcoin-invalid-number", [$amount], $sender->getName()));
			break;
			case RebirthCoinAPI::RET_SUCCESS:
			$sender->sendMessage($this->plugin->getMessage("giverebirthcoin-gave-rebirthcoin", [$amount, $player], $sender->getName()));

			if($p instanceof Player){
				$p->sendMessage($this->plugin->getMessage("giverebirthcoin-rebirthcoin-given", [$amount], $sender->getName()));
			}
			break;
			case RebirthCoinAPI::RET_CANCELLED:
			$sender->sendMessage($this->plugin->getMessage("request-cancelled", [], $sender->getName()));
			break;
			case RebirthCoinAPI::RET_NO_ACCOUNT:
			$sender->sendMessage($this->plugin->getMessage("player-never-connected", [$player], $sender->getName()));
			break;
		}
        return true;
	}
}
