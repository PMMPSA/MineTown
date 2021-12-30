<?php

namespace onebone\pointapi\command;

use pocketmine\event\TranslationContainer;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;
use pocketmine\Player;

use onebone\pointapi\PointAPI;

class MyMcoinCommand extends Command{
	private $plugin;

	public function __construct(PointAPI $plugin){
		$desc = $plugin->getCommandMessage("solomotooto1");
		parent::__construct("solomotooto1", $desc["description"], $desc["usage"]);

		$this->setPermission("pointapi.command.mykc");

		$this->plugin = $plugin;
	}

	public function execute(CommandSender $sender, string $label, array $params): bool{
		if(!$this->plugin->isEnabled()) return false;
		if(!$this->testPermission($sender)){
			return false;
		}

		if($sender instanceof Player){
			$point = $this->plugin->myPoint($sender);
			$sender->sendMessage($this->plugin->getMessage("mypoint-mypoint", [$point]));
			return true;
		}
		$sender->sendMessage(TextFormat::RED."Please run this command in-game.");
		return true;
	}
}
