<?php

namespace ChangeBlockDrop;

use pocketmine\event\Listener;
use pocketmine\Server;
use pocketmine\plugin\PluginBase;
use pocketmine\item\Item;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\ConsoleCommandSender;
use pocketmine\item\ItemFactory;
use pocketmine\item\enchantment\Enchantment;
use pocketmine\item\enchantment\ProtectionEnchantment;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\block\Block;
use pocketmine\event\block\BlockBreakEvent;
class Main extends PluginBase implements Listener{
    
public function onEnable(){
			$this->getServer()->getPluginManager()->registerEvents($this, $this);
	}
public function onBreak(BlockBreakEvent $event) {
    if(!$event->isCancelled()){
        $id = mt_rand(0,1000000);
        if($id == 0){
    switch($event->getBlock()->getId()) {
        case Item::COBBLESTONE:
            $item = Item::get(433);
            $item->setCustomName("§l§e┃§a Mảnh Miner §e┃");
            $item->setLore(["§f-§e Loại:§f Mảnh Ghép -\n§c--------------------\n§fĐộ hiếm§l: §r§aCực hiếm\n§c--------------------\n§f-§d Tác dụng §f-\n§dDùng để Craft đồ trong §aCustomCraft"]);
            $enchantment = Enchantment::getEnchantment(17);
            $item->addEnchantment(new EnchantmentInstance($enchantment, 32767));
            $event->setDrops([$item]);
            Server::getInstance()->broadcastMessage(str_replace("{player}", $event->getPlayer()->getName(), "§l§b{player} §r§aVừa Mine Được §l§e┃§a Mảnh Miner §e┃"));
            break;
    }
}
}
}
public function onDisable(){
	}
}