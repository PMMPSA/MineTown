<?php

namespace GreenJajot\PointJoin;

use pocketmine\plugin\PluginBase;
use pocketmine\nbt\tag\{CompoundTag, IntTag, StringTag, IntArrayTag};
use pocketmine\utils\Config;
use pocketmine\Player; 
use pocketmine\Server;
use pocketmine\event\Listener;
use pocketmine\command\{Command,CommandSender, CommandExecutor, ConsoleCommandSender};
use pocketmine\event\Event;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\item\Item;
use pocketmine\block\Block;
use pocketmine\event\inventory\InventoryTransactionEvent;
use pocketmine\inventory\transaction\action\SlotChangeAction;
use pocketmine\scheduler\TaskScheduler;
class Main extends PluginBase implements Listener{

	public function onEnable(){
	    $this->getServer()->getPluginManager()->registerEvents($this, $this);
	    $this->joinlist = new Config($this->getDataFolder() . "joinlist.yml", Config::YAML);
		$this->getLogger()->info("PointJoin enable");
	}
	
	public function onJoin(PlayerJoinEvent $ev){
		$p = $ev->getPlayer()->getName();
		if(!($this->joinlist->exists($p))){
		    $this->joinlist->set($p, 0);
		    $api3 = $this->getServer()->getPluginManager()->getPlugin("PointAPI");
		    $api4 = $this->getServer()->getPluginManager()->getPlugin("RebirthCoinAPI");
		    $api5 = $this->getServer()->getPluginManager()->getPlugin("EconomyAPI");
$api3->addPoint($ev->getPlayer(),10);
$api4->addRebirthCoin($ev->getPlayer(),10);
$api5->addMoney($ev->getPlayer(),10000000);
            sleep(5);
            $ev->getPlayer()->sendMessage("§l§bHappy§d New§c Year\n§l§bBạn Nhận Được §e10 Mcoin\n§l§eBạn Nhận Được §b10 RebirthCoin\n§l§cBạn Nhận Được §d10 Triệu Xu");
	      	$this->joinlist->save();
		}
	}
	public function onDisable ()
	{
		$this->getLogger()->info("Plugin đã dừng !");
	}
}