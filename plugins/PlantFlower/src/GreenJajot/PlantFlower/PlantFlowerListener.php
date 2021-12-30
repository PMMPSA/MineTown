<?php

namespace GreenJajot\PlantFlower;

use pocketmine\inventory\Inventory;
use pocketmine\item\Item;
use pocketmine\Player;
use pocketmine\utils\Config;

class PlantFlowerListener {

	public function __construct($plugin, $money)
	{
		/**
		 * @param KitSystem $plugin
		 * @param Player $player
		 */
		 
		$this->plugin = $plugin;
		$this->money = $money;
	}

	public function onTransaction(Player $player, Item $itemClickedOn, Item $itemClickedWith)
	{
	    if($itemClickedOn->getNamedTag()->hasTag("plantflower")){
		$menu = $itemClickedOn->getNamedTag()->getString("plantflower");
if($menu == "bone"){
			$player->removeAllWindows();
			sleep(1);
			$this->plugin->PlantedFlower($player,$this->money);
		
}
}
}

}