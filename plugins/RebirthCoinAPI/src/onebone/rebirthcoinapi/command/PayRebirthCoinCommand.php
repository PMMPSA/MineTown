<?php

namespace onebone\rebirthcoinapi\command;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;
use pocketmine\Player;

use onebone\rebirthcoinapi\RebirthCoinAPI;
use onebone\rebirthcoinapi\event\rebirthcoin\PayRebirthCoinEvent;

class PayRebirthCoinCommand extends Command{
	private $plugin;

	public function __construct(RebirthCoinAPI $plugin){
		$desc = $plugin->getCommandMessage("payrbcoin");
		parent::__construct("payrbcoin", $desc["description"], $desc["usage"]);

		$this->setPermission("pointapi.command.payrbcoin");

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

		$player = array_shift($params);
		$amount = array_shift($params);

		if(!is_numeric($amount)){
			$sender->sendMessage(TextFormat::RED . "Usage: " . $this->getUsage());
			return true;
		}

		if(($p = $this->plugin->getServer()->getPlayer($player)) instanceof Player){
			$player = $p->getName();
		}

		if(!$p instanceof Player and $this->plugin->getConfig()->get("allow-pay-offline", true) === false){
			$sender->sendMessage($this->plugin->getMessage("player-not-connected", [$player], $sender->getName()));
			return true;
		}

		if(!$this->plugin->accountExists($player)){
			$sender->sendMessage($this->plugin->getMessage("player-never-connected", [$player], $sender->getName()));
			return true;
		}

		$this->plugin->getServer()->getPluginManager()->callEvent($ev = new PayRebirthCoinEvent($this->plugin, $sender->getName(), $player, $amount));

		$result = RebirthCoinAPI::RET_CANCELLED;
		if(!$ev->isCancelled()){
			$result = $this->plugin->reduceRebirthCoin($sender, $amount);
		}

		if($result === RebirthCoinAPI::RET_SUCCESS){
			$this->plugin->addRebirthCoin($player, $amount, true);

			$sender->sendMessage($this->plugin->getMessage("pay-success", [$amount, $player], $sender->getName()));
			if($p instanceof Player){
				$p->sendMessage($this->plugin->getMessage("rebirthcoin-paid", [$sender->getName(), $amount], $sender->getName()));
			}
		}else{
			$sender->sendMessage($this->plugin->getMessage("pay-failed", [$player, $amount], $sender->getName()));
		}
		return true;
	}
}
