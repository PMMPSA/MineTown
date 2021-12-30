<?php

namespace myval;

use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\event\server\QueryRegenerateEvent;

class infinityslots extends PluginBase implements Listener{

public function onEnable(){
$this->getServer()->getPluginManager()->registerEvents($this, $this);
$this->getLogger()->info("§aPlugin Activated");
}
public function onInrinitySlots(QueryRegenerateEvent $event){
             $event->setMaxPlayerCount($event->getPlayerCount()+1);
             }
}

