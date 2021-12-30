<?php

namespace SkillMiner;

use pocketmine\inventory\Inventory;
use pocketmine\item\Item;
use pocketmine\Player;
use pocketmine\utils\Config;
use pocketmine\command\{Command,CommandSender, CommandExecutor, ConsoleCommandSender};

class MuaSkillListener {

	public function __construct($plugin)
	{
		$this->plugin = $plugin;
		$this->rbcoin =  $this->plugin->getServer()->getPluginManager()->getPlugin("RebirthCoinAPI");
	}

	public function onTransaction(Player $player, Item $itemClickedOn, Item $itemClickedWith)
	{
	    if($itemClickedOn->getNamedTag()->hasTag("skillminer")){
		$menu = $itemClickedOn->getNamedTag()->getString("skillminer");
if($menu == "al"){
$player->sendMessage("§3Bạn Đã Mua Kĩ Năng Này Rồi");
$player->removeAllWindows();
}
if($menu == "fastmine"){
$player->removeAllWindows();
$price = 10;
$pmoney = ($this->rbcoin->myRebirthCoin($player)+1);
if($price < $pmoney){
$this->rbcoin->reduceRebirthCoin($player->getName(), $price);
$this->plugin->getServer()->dispatchCommand(new ConsoleCommandSender(),'setuperm '.$player->getName().' skillminer.fastmine');
}else{$player->sendMessage("§cBạn Không Đủ Rbcoin");
}
}
if($menu == "kingofblock"){
$player->removeAllWindows();
$price = 150;
$pmoney = ($this->rbcoin->myRebirthCoin($player)+1);
if($price < $pmoney){
$this->rbcoin->reduceRebirthCoin($player->getName(), $price);
$this->plugin->getServer()->dispatchCommand(new ConsoleCommandSender(),'setuperm '.$player->getName().' skillminer.kingofblock');
}else{$player->sendMessage("§cBạn Không Đủ Rbcoin");
}
}
if($menu == "pickaxeleveling"){
$player->removeAllWindows();
$price = 200;
$pmoney = ($this->rbcoin->myRebirthCoin($player)+1);
if($price < $pmoney){
$this->rbcoin->reduceRebirthCoin($player->getName(), $price);
$this->plugin->getServer()->dispatchCommand(new ConsoleCommandSender(),'setuperm '.$player->getName().' skillminer.pickaxeleveling');
}else{$player->sendMessage("§cBạn Không Đủ Rbcoin");
}
}
if($menu == "richdreamer"){
$player->removeAllWindows();
$price = 160;
$pmoney = ($this->rbcoin->myRebirthCoin($player)+1);
if($price < $pmoney){
$this->rbcoin->reduceRebirthCoin($player->getName(), $price);
$this->plugin->getServer()->dispatchCommand(new ConsoleCommandSender(),'setuperm '.$player->getName().' skillminer.richdreamer');
}else{$player->sendMessage("§cBạn Không Đủ Rbcoin");
}
}
if($menu == "rebirthminer"){
$player->removeAllWindows();
$price = 140;
$pmoney = ($this->rbcoin->myRebirthCoin($player)+1);
if($price < $pmoney){
$this->rbcoin->reduceRebirthCoin($player->getName(), $price);
$this->plugin->getServer()->dispatchCommand(new ConsoleCommandSender(),'setuperm '.$player->getName().' skillminer.rebirthminer');
}else{$player->sendMessage("§cBạn Không Đủ Rbcoin");
}
}
if($menu == "eternity"){
$player->removeAllWindows();
$price = 130;
$pmoney = ($this->rbcoin->myRebirthCoin($player)+1);
if($price < $pmoney){
$this->rbcoin->reduceRebirthCoin($player->getName(), $price);
$this->plugin->getServer()->dispatchCommand(new ConsoleCommandSender(),'setuperm '.$player->getName().' skillminer.eternity');
}else{$player->sendMessage("§cBạn Không Đủ Rbcoin");
}
}
}
}
}