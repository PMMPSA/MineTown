<?php

namespace SkillMiner;

use pocketmine\inventory\Inventory;
use pocketmine\item\Item;
use pocketmine\Player;
use pocketmine\utils\Config;
use pocketmine\command\{Command,CommandSender, CommandExecutor, ConsoleCommandSender};

class SkillListener {

	public function __construct($plugin)
	{
		$this->plugin = $plugin;
	}

	public function onTransaction(Player $player, Item $itemClickedOn, Item $itemClickedWith)
	{
	    if($itemClickedOn->getNamedTag()->hasTag("skillminer")){
		$menu = $itemClickedOn->getNamedTag()->getString("skillminer");
if($menu == "no"){
$player->sendMessage("§cBạn Chưa Sở Hữu Kĩ Năng Này");
$player->removeAllWindows();
}
if($menu == "cd"){
$player->sendMessage("§eKĩ Năng Này Đang Trong Thời Gian Hồi Chiêu");
$player->removeAllWindows();
}
if($menu == "al"){
$player->sendMessage("§eKhông Thể Sử Dụng 2 Kĩ Năng Cùng 1 Lúc");
$player->removeAllWindows();
}
if($menu == "fastmine"){
$player->removeAllWindows();
$this->plugin->FastMine($player);
}
if($menu == "kingofblock"){
$player->removeAllWindows();
$this->plugin->KingOfBlock($player);
}
if($menu == "pickaxeleveling"){
$player->removeAllWindows();
$this->plugin->PickaxeLeveling($player);
}
if($menu == "richdreamer"){
$player->removeAllWindows();
$this->plugin->RichDreamer($player);
}
if($menu == "rebirthminer"){
$player->removeAllWindows();
$this->plugin->RebirthMiner($player);
}
if($menu == "eternity"){
$player->removeAllWindows();
$this->plugin->Eternity($player);
}
}
}
}