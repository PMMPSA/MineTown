<?php

namespace PickaxeLevel;

use pocketmine\scheduler\Task;

use pocketmine\Server;
use pocketmine\Player;

use PickaxeLevel\Main;

Class PopupTask extends Task{


    public function __construct(Main $plugin, Player $player){

        $this->plugin = $plugin;
        $this->player = $player;
    }

    public function onRun($currentTick){
        foreach ($this->plugin->getServer()->getOnlinePlayers() as $player) {
            $level = $this->plugin->getLevel($player);
            $exp = $this->plugin->getExp($player);
            $next = $this->plugin->getNextExp($player);
            $p = $player->getName();
            $pickaxename = $this->plugin->getNamePickaxe($player);
            $i = $player->getInventory()->getItemInHand();
            $hand = $i->getCustomName();
            $it = explode(" ", $hand);
            $damage = $i->getDamage();
            if ($it[0] == "§r§l§a⚒§6") {
                if ($damage > 50) {
                    $i->setDamage(0);
                    $player->getInventory()->setItemInHand($i);
                    $player->sendMessage("§l§a♥ §e[§aMine§bTown§e] §aCúp của bạn đã được sửa chữa thành công");
                }
                $player->sendPopup("    §l§c☭§d Pickaxe§6 | §bＭＩＮＥ§aＴＯＷＮ\n" . "§c⊱§b Kinh Nghiệm:§a " . $exp ."§l§3 /§a ".$next. "§c |§b Cấp Cúp: §a" . $level);
            } else {
                $this->plugin->getScheduler()->cancelTask($this->getTaskId());
            }
        }
    }
}