<?php

namespace onebone\pointapi\command;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;
use pocketmine\Player;

use onebone\pointapi\PointAPI;

class GiveMcoinCommand extends Command{
	private $plugin;

	public function __construct(PointAPI $plugin){
		$desc = $plugin->getCommandMessage("solomotooto");
		parent::__construct("solomotooto", $desc["description"], $desc["usage"]);

		$this->setPermission("pointapi.command.givekc");

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

		$result = $this->plugin->addPoint($player, $amount);
		switch($result){
			case PointAPI::RET_INVALID:
			$sender->sendMessage($this->plugin->getMessage("givepoint-invalid-number", [$amount], $sender->getName()));
			break;
			case PointAPI::RET_SUCCESS:
			$sender->sendMessage($this->plugin->getMessage("givepoint-gave-point", [$amount, $player], $sender->getName()));

			if($p instanceof Player){
				$p->sendMessage($this->plugin->getMessage("givepoint-point-given", [$amount], $sender->getName()));
			}
			break;
			case PointAPI::RET_CANCELLED:
			$sender->sendMessage($this->plugin->getMessage("request-cancelled", [], $sender->getName()));
			break;
			case PointAPI::RET_NO_ACCOUNT:
			$sender->sendMessage($this->plugin->getMessage("player-never-connected", [$player], $sender->getName()));
			break;
		}
        return true;
	}
}
