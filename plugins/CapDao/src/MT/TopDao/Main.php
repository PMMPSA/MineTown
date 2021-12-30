<?php 

namespace MT\TopDao;

use pocketmine\Server;
use pocketmine\Player;

use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;

use pocketmine\event\player\PlayerChatEvent;
use pocketmine\event\player\PlayerJoinEvent;

use pocketmine\command\ConsoleCommandSender;
use pocketmine\command\CommandSender;
use pocketmine\command\Command;

use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\block\Block;
use pocketmine\item\Item;

use pocketmine\utils\Config;

Class Main extends PluginBase implements Listener{
	
	public $prefix = "§c[§fLevelIsland§c] ";
	
	public $cfg;
	public $config;
	public $level;
	public $exp;
	public $nextexp;
	
	public function onEnable(){
		$this->getServer()->getPluginManager()->registerEvents($this, $this);		
		$this->eco = $this->getServer()->getPluginManager()->getPlugin("EconomyAPI");
		
		@mkdir($this->getDataFolder());
		$this->level = new Config($this->getDataFolder() . "Level.yml", Config::YAML);
		$this->exp = new Config($this->getDataFolder() . "EXPLevel.yml", Config::YAML);
	    $this->nextexp = new Config($this->getDataFolder() . "NextEXPLevel.yml", Config::YAML);
	}
	
	public function onJoin(PlayerJoinEvent $ev){
		$p = $ev->getPlayer()->getName();
		if($this->nextexp->get($p) > 0){
		} else {
			$this->level->set($p, 1);
			$this->exp->set($p, 1);
			$this->nextexp->set($p, 100);
		}
	}
	
	public function onBlock(BlockPlaceEvent $ev){
		if ($ev->isCancelled()){
			return;
		}
		$p = $ev->getPlayer()->getName();
		$sender = $ev->getPlayer();
		if($this->exp->get($p) < $this->nextexp->get($p)){
			$this->exp->set($p, $this->exp->get($p) +0.5);
		} else {
			$this->level->set($p,$this->level->get($p) +1);
			$this->exp->set($p, 0);
			$this->nextexp->set($p, $this->nextexp->get($p) + 50);
			$this->level->save();
			$this->exp->save();
			$this->nextexp->save();
			
			$money = 1000;
			$this->eco->addMoney($sender, $money);
			
			$this->getServer()->broadcastMessage($this->prefix . "§l§eᴍɪɴᴇ§bᴛᴏᴡɴ §f→ §aCấp Đảo của người chơi §c".$p." §ađã lên cấp §c".$this->level->get($p));
			$sender->sendMessage($this->prefix . "§l§eᴍɪɴᴇ§bᴛᴏᴡɴ §f→ §aCấp Đảo của bạn đã được lên cấp §c".$this->level->get($p));
			$sender->sendMessage($this->prefix . "§l§eᴍɪɴᴇ§bᴛᴏᴡɴ §f→ §aBạn nhận được §c$money xu §ekhi lên level.");
		}
	}
	
	/*public function onChat(PlayerChatEvent $ev){
		$user = $ev->getPlayer()->getName();
		$p = $ev->getPlayer();
		$name = $p->getName();
		$p->setDisplayName("§b[§eCấp Đảo §c" . $this->level->get($user) . "§b]§r§f " . $name);
	}*/
	
	public function onCommand(CommandSender $sender, Command $cmd, string $label, array $args): bool{
		if($cmd->getName() == "topdao"){
			$this->MenuForm($sender);
		}
		return true;
	}
	
	public function MenuForm(Player $sender){
		$formapi = $this->getServer()->getPluginManager()->getPlugin("FormAPI");
		$form = $formapi->createSimpleForm(function (Player $sender, ?int $data = null){
			$result = $data;
			if($result === null){
				return;
			}
			switch($result){
				case 0:
				$this->HuongDanForm($sender);
				break;
				case 1:
				$this->TopPlotForm($sender);
				break;
				case 2:
				$this->ThongTinForm($sender);
				break;
			}
		});
		$p = $sender->getName();
		$level = $this->level->get($p);
		$form->setTitle("§l§a♦ §6TopIslandUI §a♦");
		#$form->setContent("§e• §cLevel của bạn: §e" . $level);
		$form->addButton("§l§e• §bHướng dẫn §l§e•");
		$form->addButton("§l§e• §bTop Đảo §l§e•");
		$form->addButton("§l§e• §bThông Tin §l§e•");
		$form->sendToPlayer($sender);
		return $form;
	}
	
	public function HuongDanForm(Player $sender){
		$formapi = $this->getServer()->getPluginManager()->getPlugin("FormAPI");
		$form = $formapi->createSimpleForm(function (Player $sender, ?int $data = null){
			$result = $data;
			if($result === null){
				$this->MenuForm($sender);
				return;
			}
			switch($result){
				case 0:
				$this->MenuForm($sender);
				break;
			}
		});
		$msg1 = "§c♦ §eCách sử dụng: Bạn chỉ cần đặt ra nhiều block xuống đất sẽ thu thập được XP sau khi đủ XP bạn đã được lên cấp\n\n";
		$msg2 = "§c♦ §eBấm /topdao để xem cấp đảo của bạn\n";
		$msg = $msg1 . $msg2;
		$form->setTitle("§l§a♦ §6TopIslandUI §a♦");
		$form->setContent($msg);
		$form->addButton("§l§e• §bSubmit §e•");
		$form->sendToPlayer($sender);
		return $form;
	}
	
	public function TopPlotForm(Player $sender){
		$levelplot = $this->level->getAll();
		$message = "";
		$message1 = "";
		if(count($levelplot) > 0){
			arsort($levelplot);
			$i = 1;
			foreach($levelplot as $name => $level){
				$message .= "§l§cTOP " . $i . ": §b" . $name . " §d→ §f" . $level . " §elevel\n\n";
				$message1 .= "§l§cTOP " . $i . ": §b" . $name . " §d→ §f" . $level . " §elevel\n";
				if($i >= 10){
					break;
				}
				++$i;
			}
		}
		
		$formapi = $this->getServer()->getPluginManager()->getPlugin("FormAPI");
		$form = $formapi->createSimpleForm(function (Player $sender, ?int $data = null){
			$result = $data;
			if($result === null){
				$this->MenuForm($sender);
				return;
			}
			switch($result){
				case 0:
				$this->MenuForm($sender);
				break;
			}
		});
		$form->setTitle("§l§a♦ §6TopIslandUI §a♦");
		$form->setContent($message);
		$form->addButton("§l§e• §bSibmit §e•");
		$form->sendToPlayer($sender);
		return $form;
	}
	
	public function ThongTinForm(Player $sender){
		$formapi = $this->getServer()->getPluginManager()->getPlugin("FormAPI");
		$form = $formapi->createSimpleForm(function (Player $sender, ?int $data = null){
			$result = $data;
			if($result === null){
				$this->MenuForm($sender);
				return;
			}
			switch($result){
				case 0:
				$this->MenuForm($sender);
				break;
			}
		});
		$p = $sender->getName();
		$msg1 = "§c♦ §eNgười chơi§f: " . $p . "\n\n";
		$msg2 = "§c♦ §eCấp đảo hiện tại§f: " . $this->level->get($p) . "\n\n";
		$msg3 = "§c♦ §eXP Đảo Hiện Tại§f: " . $this->exp->get($p) . "/" . $this->nextexp->get($p) . "\n";
		$msg = $msg1 . $msg2 . $msg3; 
		$form->setTitle("§l§a♦ §6TopIslandUI §a♦");
		$form->setContent($msg);
		$form->addButton("§l§e• §bSubmit §e•");
		$form->sendToPlayer($sender);
		return $form;
	}
	
	public function getLevelPlot($sender){
		if($sender instanceof Player){
			$name = $sender->getName();
			$levelplot = $this->level->get($name);
			return $levelplot;
		}
	}
}