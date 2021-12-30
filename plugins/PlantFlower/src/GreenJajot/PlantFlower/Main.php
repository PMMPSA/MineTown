<?php

namespace GreenJajot\PlantFlower;

use pocketmine\plugin\PluginBase;
use jojoe77777\FormAPI;
use muqsit\invmenu\inventories\BaseFakeInventory;
use pocketmine\nbt\tag\{CompoundTag, IntTag, StringTag, IntArrayTag};
use pocketmine\utils\Config;
use pocketmine\Player; 
use pocketmine\Server;
use pocketmine\event\Listener;
use pocketmine\command\{Command,CommandSender, CommandExecutor, ConsoleCommandSender};
use pocketmine\event\Event;
use pocketmine\event\player\PlayerJoinEvent;
use muqsit\invmenu\{InvMenu,InvMenuHandler};
use muqsit\invmenu\inventories\ChestInventory;
use pocketmine\item\Item;
use pocketmine\block\Block;
use pocketmine\event\inventory\InventoryTransactionEvent;
use pocketmine\inventory\transaction\action\SlotChangeAction;
use pocketmine\scheduler\TaskScheduler;
class Main extends PluginBase implements Listener{

	public function onEnable(){
		$this->getLogger()->info("PlantFlower enable");
		if (!InvMenuHandler::isRegistered()) {
			InvMenuHandler::register($this);
		}
	}
	public function onCommand(CommandSender $sender, Command $command, String $label, array $args) : bool {
		   switch($command->getName()){
               case "plantflower":
    if(!isset($args[0])){
        $sender->sendMessage("Thường Dùng: /plantflower <Số Tiền Muốn Cược>");
        return false;
    }
    if(!is_numeric($args[0])){
        $sender->sendMessage("Thường Dùng: /plantflower <Số Tiền Muốn Cược>");
        return false;
    }
    if($args[0] > 0){
        $int = $args[0];
        $api2 = $this->getServer()->getPluginManager()->getPlugin("EconomyAPI");
    if($api2->MyMoney($sender) >= $int){
        $api2->ReduceMoney($sender,$int);
        $this->PlantFlower($sender, $int);
        Server::getInstance()->broadcastMessage(str_replace("{player}", $sender->getName(), "§l§b✿ §e→ {player} §r§bĐang Cược ".$int." §aMoney Trong §f/plantflower"));
    }else{
        $sender->sendMessage("Bạn Không Đủ Tiền");
    }
        
    }else{
        $sender->sendMessage("Số Tiền Đặt Cược Phải Lớn Hơn 0");
    }
					break;
	}
	return true;
	}

    public function PlantFlower(Player $player1, $int) {
		$menu = InvMenu::create(InvMenu::TYPE_DOUBLE_CHEST);
		$menu->setName("§l§b✿ §e→ Số Tiền Bạn Cược Là: ".$int);
		$grass = Item::get(Item::GRASS);
		$bone = Item::get(351,15);
		$bone->setNamedTagEntry(new StringTag("plantflower", "bone"));
		$menu->readonly(true);
		$minv = $menu->getInventory();
		$air = Item::get(Item::AIR);
		$minv->setItem(30, $grass);
		$minv->setItem(31, $grass);
		$minv->setItem(32, $grass);
		$minv->setItem(53, $bone);
		$menu->send($player1);
		$menu->setListener([new PlantFlowerListener($this,$int),"onTransaction"]);
	}
	public function PlantedFlower(Player $player1, $int) {
	    $grass = Item::get(Item::GRASS);
		$grass2 = Item::get(31);
	    $rand1 = mt_rand(0,15);
	    $rand2 = mt_rand(0,15);
	    $rand3 = mt_rand(0,15);
	    $planted = 0;
	    $depth = 0;
	    $luck1 = $grass2;
	    $luck2 = $grass2;
	    $luck3 = $grass2;
	    if($rand1 == 9){
	        $luck1 = $this->randflow();
	        $planted = $planted + 1;
	    }
	        if($rand2 == 6){
	        $luck2 = $this->randflow();
	        $planted = $planted + 1;
	        }
	        if($rand3 == 3){
	        $luck3 = $this->randflow();
	        $planted = $planted + 1;
	        }
	        if($planted == 1){
	            $depth = 2;
	        }
	        if($planted == 2){
	            $depth = 3;
	        }
	        if($planted == 3){
	            $depth = 5;
	        }
	       $int2 = $int*$depth;
		$menu = InvMenu::create(InvMenu::TYPE_DOUBLE_CHEST);
		$menu->setName("§l§b✿ §e→ Số Tiền Bạn Trúng Là: ".$int2);
		$bone = Item::get(399)->setCustomName("§l§aNhận Tiền");
		$bone->setNamedTagEntry(new StringTag("plantflower", "take"));
		$menu->readonly(true);
		$minv = $menu->getInventory();
		$air = Item::get(Item::AIR);
		$minv->setItem(21, $luck1);
		$minv->setItem(22, $luck2);
		$minv->setItem(23, $luck3);
		$minv->setItem(30, $grass);
		$minv->setItem(31, $grass);
		$minv->setItem(32, $grass);
		$menu->send($player1);
		if($depth > 0){
    $api2 = $this->getServer()->getPluginManager()->getPlugin("EconomyAPI");
    $api2->addMoney($player1,$int2);
    Server::getInstance()->broadcastMessage(str_replace("{player}", $player1->getName(), "§l§b✿ §e→ {player} §r§aTrúng §bx".$depth." §cMoney Trong §f/plantflower §aTổng Giá Trị Nhận Được Là: §6".$int2));
    }else{
        Server::getInstance()->broadcastMessage(str_replace("{player}", $player1->getName(), "§l§b✿ §e→ {player} §r§aVừa Mất Trắng Money Trong §f/plantflower"));
    }
	}
	public function randflow() : Item
	{
	    $flower1 = Item::get(37);
		$flower2 = Item::get(38);
	if(mt_rand(1,2) == 1){
	    return $flower1;
	}else{
	    return $flower2;
	}
	}
	public function onDisable ()
	{
		$this->getLogger()->info("Bye :(()) !");
	}
}