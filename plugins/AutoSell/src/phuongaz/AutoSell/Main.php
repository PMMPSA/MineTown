<?php

namespace phuongaz\AutoSell;

use pocketmine\{Player, Server};
use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\command\{Command,CommandSender, CommandExecutor, ConsoleCommandSender};
use pocketmine\inventory\BaseInventory;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\utils\TextFormat;

class Main extends PluginBase implements Listener
{
	public $mode = [];
	public function onEnable()
	{
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
		
    }
	public function onDisable ()
	{
		$this->getLogger()->info("Plugin disabled");
	}
	public function onJoin (PlayerJoinEvent $j)
	{
	    $player = $j->getPlayer()->getName();
		$this->mode[$player] = "on";
	}
    public function onCommand(CommandSender $sender, Command $cmd, string $label, array $args): bool{
       if (strtolower($cmd->getName()) == "autosell") {
           if(!isset($args[0])){
               $sender->sendMessage("§l§b[§aMine§bTown§b]§a Sử dụng: /autosell <on|off|info>");
               return false;
           }
           switch ($args[0]) {
               case "on":
			       $sender->sendMessage("§l§b[§aMine§bTown§b]§a AutoSell đã được bật ");
				   $this->mode[$sender->getName()] = "on";
				   break;

               case "off":
			       $sender->sendMessage("§l§b[§aMine§bTown§b]§4 AutoSell đã được tắt "); 
                   $this->mode[$sender->getName()] = "off";
				   break;
				   
			   case "info":
			       $sender->sendMessage("§l§b[§aMine§bTown§b]§6 Plugin: AutoSell\nAuthor: phuongaz\nServer: §aMine§bTown§6");
				   break;
				   
               default:
                   $sender->sendMessage("§l§b[§aMine§bTown§b]§a Sử dụng: /autosell <on|off|info>");
                   break;
           }
       }

       return true;
   }
    public function onBreak(BlockBreakEvent $event) : void {
		$player = $event->getPlayer();
		foreach($event->getDrops() as $drop) {
			if(!$player->getInventory()->canAddItem($drop)) 
			{
				if ($this->mode[$player->getName()] == "on") 
				{
				$this->getServer()->dispatchCommand($player, "sell all");
				$player->sendMessage("§l§b[§aMine§bTown§b]§a Tự động bán đồ thành công!");
				}
				break;
			}
		}
	}
    public function get(){
	return $this;
    }
    public function onQuit(PlayerQuitEvent $e){
       $a = "autosell on";
       $this->getServer()->dispatchCommand($e->getPlayer(),$a);
    }
}
