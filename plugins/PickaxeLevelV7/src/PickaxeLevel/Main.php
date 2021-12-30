<?php

namespace PickaxeLevel;

use pocketmine\Server;
use pocketmine\Player;

use pocketmine\plugin\PluginBase;

use pocketmine\event\Listener;

use pocketmine\command\ConsoleCommandSender;
use pocketmine\command\CommandSender;
use pocketmine\command\Command;

use pocketmine\utils\TextFormat;

use pocketmine\event\block\BlockBreakEvent;
use pocketmine\block\Block;
use pocketmine\item\Item;

use pocketmine\event\player\{PlayerDropItemEvent, PlayerInteractEvent, PlayerItemHeldEvent, PlayerJoinEvent, PlayerChatEvent};

use pocketmine\utils\Config;

use pocketmine\entity\Effect;

use pocketmine\network\protocol\SetTitlePacket;

use DaPigGuy\PiggyCustomEnchants\CustomEnchants\CustomEnchants;
use pocketmine\item\enchantment\{Enchantment, EnchantmentInstance};
use DaPigGuy\PiggyCustomEnchants\Main as CE;

use onebone\economyapi\EconomyAPI;
use onebone\pointapi\PointAPI;

use PickaxeLevel\PopupTask;

class Main extends PluginBase implements Listener{
	
	public $prefix = "§l§a♥ §e[§aMine§bTown§e] ";

	public function onEnable(){
		
		$this->lv = new Config($this->getDataFolder() . "user.yml", Config::YAML);
		$this->level = new Config($this->getDataFolder() . "level.yml", Config::YAML);
		$this->rebirth = new Config($this->getDataFolder() . "rebirth.yml", Config::YAML);
		$this->rebirthu = new Config($this->getDataFolder() . "rebirthu.yml", Config::YAML);
		$this->saveDefaultConfig();
		$this->config = $this->getConfig();
		$this->config->save();
		
		$this->getServer()->getPluginManager()->registerEvents($this,$this);
        $this->getLogger()->info("PickaxeLevel");
        
		$this->eco =  $this->getServer()->getPluginManager()->getPlugin("EconomyAPI");
		$this->rbcoin =  $this->getServer()->getPluginManager()->getPlugin("RebirthCoinAPI");
        $this->pointapi =  $this->getServer()->getPluginManager()->getPlugin("PointAPI");
		$this->CE =  $this->getServer()->getPluginManager()->getPlugin("PiggyCustomEnchants");
        if(is_null($this->pointapi)){
            $this->getLogger()->warning("Hãy Tải PointAPI");
        }else{
            $this->getLogger()->notice("Loading PickaxeLEVEL by GreenJajot");
        }
       // $this->point =  $this->getServer()->getPluginManager()->getPlugin("PiggyCustomEnchantments");
       $this->skillminer = $this->getServer()->getPluginManager()->getPlugin("SkillMiner");
	}
	
	public function getNamePickaxe($player){
		if($player instanceof Player){
			$p = $player->getName();
		}
		$this->lv->load($this->getDataFolder() . "user.yml", Config::YAML);
			$pa = "§r§l§a⚒§6 MineTown §bᑭIᑕKᗩ᙭E§f [§cLevel: §b".$this->lv->get(strtolower($p))["Level"]."§f]§a ".$p;
		return $pa;
	}
	
	public function getLore(){
	    $lore = "\n§r§a[§c⚡§a] §ePickaxe Sẽ Được Cường Hóa Dần Theo Cấp Độ §a[§c⚡§a]\n\n§a[§c⚡§a] §ePickaxe Khi Đạt Đến Level 100 Thì Pickaxe Sẽ Cường Hòa Thành Pickaxe Diamond §a[§c⚡§a]\n\n§r§a[§c⚡§a] §ePickaxe Có Khả Năng Tự Động Sửa Chữa Khi Hư Hỏng §a[§c⚡§a]\n\n§r§a[§c⚡§a] §eBạn Có Thể Nhận Được Quà Khi Pickaxe Lên Cấp §a[§c⚡§a]";
	    return $lore;
    }
    
    public function onJoin(PlayerJoinEvent $ev){
		$p = $ev->getPlayer()->getName();
		if(!($this->rebirthu->exists(strtolower($p)))){
		    $this->rebirthu->set(strtolower($p), 200);
	      	$this->rebirthu->save();
		}
		if(!($this->lv->exists(strtolower($p)))){
			$this->getLogger()->notice(" Không tìm thấy dữ liệu $p ");
			$this->getLogger()->notice(" Tạo dữ liệu $p ");
			$this->lv->set(strtolower($p), ["Level" => 1, "exp" => 1, "nextexp" => 100]);
			$this->lv->save();
			$this->level->set(strtolower($p), 1);
	      	$this->level->save();
	      	$this->rebirth->set(strtolower($p), 1);
	      	$this->rebirth->save();
			$p1 = $ev->getPlayer();
			$player = $ev->getPlayer();
			$inv = $player->getInventory();  
			$item = Item::get(257, 0, 1);
			$item->setCustomName($this->getNamePickaxe($player));
			$item->setLore(array($this->getLore()));
			$inv->addItem($item);
			$player->sendMessage($this->prefix . "§aCúp đã được thêm vào túi đồ của bạn, hãy cùng đồng hành với nó lâu nhé");
			return true;
		}
	}

    public function onItemHeld(PlayerItemHeldEvent $ev){
        $task = new PopupTask($this, $ev->getPlayer());
        $this->tasks[$ev->getPlayer()->getId()] = $task;
        $this->getScheduler()->scheduleRepeatingTask($task, 20);

        $p = $ev->getPlayer();
        $contents = $p->getInventory()->getContents();
        $i = $p->getInventory()->getItemInHand();
        
        if(isset($this->need[$p->getName()])){
			$icn = $i->getCustomName();
			$i->setCustomName(str_replace("❤§6 ".($this->lv->get(strtolower($p->getName()))["Level"] - 1), "❤§6 ".$this->lv->get(strtolower($p->getName()))["Level"], $icn));
            if($this->getLevel($p) == 10){
                $i = Item::get(278,0,1);
                $i->setCustomName(str_replace("❤§6 ".($this->lv->get(strtolower($p->getName()))["Level"] - 1), "❤§6 ".$this->lv->get(strtolower($p->getName()))["Level"], $icn));
                $i->setLore(array($this->getLore()));
                $p->sendMessage($this->prefix . "§aCúp của bạn đã được nâng cấp thành cúp kim cương.");
			}
                $id = 15;
                $lv = $this->getLevel($p)/2.5;
                $i->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment($id), $lv));
                $p->sendMessage($this->prefix . "§aCúp của bạn đã được cường hóa: Hiệu xuất level ".$lv);
                    $id = 18;
                    $lv = $this->getLevel($p)/3;
                    $i->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment($id), $lv));
                    $p->sendMessage($this->prefix . "§aCúp của bạn đã được cường hóa: Gia tài level ".$lv);
				$id = 17;
                $lv = $this->getLevel($p)/3.5;
                $i->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment($id), $lv));
                $p->sendMessage($this->prefix . "§aCúp của bạn đã được cường hóa: Không bị phá vỡ level ".$lv);
            $p->getInventory()->setItemInHand($i);
			switch($this->getLevel($p)){
				case 50:
					$this->addCE(new ConsoleCommandSender(), "Energizing", 1, $p->getName());
				break;
				case 100:
					$this->addCE(new ConsoleCommandSender(), "Jackpot", 1, $p->getName());
				break;
				case 150:
					$this->addCE(new ConsoleCommandSender(), "Energizing", 2, $p->getName());
				break;
                case 200:
					$this->addCE(new ConsoleCommandSender(), "Jackpot", 2, $p->getName());
				break;	
                case 250:
					$this->addCE(new ConsoleCommandSender(), "Haste", 1, $p->getName());
				break;	
                case 300:
					$this->addCE(new ConsoleCommandSender(), "Jackpot", 3, $p->getName());
				break;
                case 350:
					$this->addCE(new ConsoleCommandSender(), "Haste", 2, $p->getName());
				break;	
                case 400:
					$this->addCE(new ConsoleCommandSender(), "Jackpot", 4, $p->getName());
				break;		
                case 450:
					$this->addCE(new ConsoleCommandSender(), "Haste", 3, $p->getName());
				break;
                case 500:
					$this->addCE(new ConsoleCommandSender(), "Jackpot", 5, $p->getName());
				break;				
			}			
            unset($this->need[$p->getName()]);
        }
    }
    
	public function onBreak(BlockBreakEvent $ev){
		$p = $ev->getPlayer();
		$amount = $this->getRebirth($p);
		$name = $p->getName();
		$i = $p->getInventory()->getItemInHand();
		$icn = $i->getCustomName();
		$pas = explode(" ", $icn);
		if($amount > 1){
		    $id = mt_rand(0, $this->getRebirthu($p));
		    if($id == 0){
	$money = 0;
	$money = $amount * 10000;
	if($this->skillminer->get()->rebirthminer[strtolower($p->getName())] === "on"){
	$money = $money * 5;
	}
	$this->eco->addMoney($p->getName(), $money);
	$p->sendMessage(str_replace("{MONEY}", $money,"§l§a♥ §e[§aMine§bTown§e] §aBạn Đã Nhận Được {MONEY} Xu Khi Đi Mine\n(Xu Này Từ Nguồn Rebirth Của Bạn)"));
		    }
		}
		if($pas[0] == "§r§l§a⚒§6"){
			if(strpos($icn, $name)  == false){
				$ev->setCancelled(true);
				$p->sendMessage($this->prefix . "§aCúp này không phải của bạn. Vì vậy bạn sẽ không đào được");
			}
		}

		if(!$ev->isCancelled()){
		    $nhan = 1;
		    if($pas[0] == "§r§l§a⚒§6"){
	if($this->skillminer->get()->pickaxeleveling[strtolower($p->getName())] === "on"){
	    $nhan = 5;
	}
	
				$block = $ev->getBlock();
				$name = strtolower($p->getName());
				$n = $this->lv->get($name);
				
               switch($block->getId()) {
                   case 56:// Kim Cương Ore
                       $this->addExp($p, 2*$nhan);
                       break;
                   case 14:// Vàng Ore
                       $this->addExp($p, 2*$nhan);
                       break;
                   case 15:// Sắt Ore
                       $this->addExp($p, 2*$nhan);
                       break;
                   case 16:// Than Ore
                       $this->addExp($p, 2*$nhan);
                       break;
                   case 129:// Emerald Ore
                       $this->addExp($p, 2*$nhan);
                       break;
                   case 21:// Lapis Lazuli Ore
                       $this->addExp($p, 2*$nhan);
                       break;
                   case 22:// Lapis Lazuli Block
                       $this->addExp($p, 3*$nhan);
                       break;
                   case 133:// Emerald Block
                       $this->addExp($p, 3*$nhan);
                       break;
                   case 57:// Kim Cương Block
                       $this->addExp($p, 3*$nhan);
					   break;
                   case 42:// Sắt Block
                       $this->addExp($p, 3*$nhan);
					   break;
                   case 41:// Vàng Block
                       $this->addExp($p, 3*$nhan);
                       break;
                   default:// All Khối
                       $this->addExp($p, 1*$nhan);
                       break;

                }
				if($this->getExp($p) >= $this->getNextExp($p)){
					$this->setLevel($p, $this->getLevel($p) +1);
					#$money = $this->getLevel($p) * 1000;
					$money = 1000;
					if(in_array($this->getLevel($p), array(100,200,300,400,500,600,700,800,900,1000,1100,1200,1300,1400,1500,1600,1700,1800,1900,2000 ))){
					    #$point = $this->getLevel($p)/2;
					    $point = 2;
                        $this->pointapi->addPoint($p->getName(), $point);
                        $p->sendMessage($this->prefix . "§aBạn đã nhận được §c" . $point . "point §atừ phần thưởng");
                    }
					$this->eco->addMoney($p->getName(), $money);
					$this->getServer()->broadcastMessage($this->prefix . "§aCúp của người chơi §c".$p->getName()."§a vừa lên cấp§c ".$this->getLevel($p));
					$p->sendMessage($this->prefix . "§aChúc mừng cúp của bạn đã đạt Level §c".$this->getLevel($p));
					$p->sendMessage($this->prefix . "§aHãy kiểm tra lại phần thưởng trong túi đồ nhé");
					$p->sendMessage($this->prefix . "§aBạn đã nhận được §c" . $money . " Xu §atừ phần thưởng");
					$this->need[$p->getName()] = true;
				}
		//	}
	  }
		}
	}
	/*   public function onChat(PlayerChatEvent $ev){
	$p = $ev->getPlayer();
	$name = $p->getName();
	$p->setDisplayName("§b[§eCấp §a⚒ §c".$this->getLevel($p)."§b]§r ".$p->getName());
	}*/
	
     public function onCommand(CommandSender $sender, Command $cmd, string $label, array $args):bool{
         if($cmd->getName() == "givecup"){
             if($sender->isOp()){
                if(!isset($args[0])){
                    $sender->sendMessage($this->prefix . "§aSử dụng §e/givecup §f<Tên Người Chơi> §ađể trao lại cúp pickaxe level");
                    return true;
                }else{
                    $player = $this->getServer()->getPlayer($args[0]);
                    if(!$player == null){
                        if($player->isOnline()) {
                            $p = $player;
                            $inv = $player->getInventory();
							 $cup = Item::get(257, 0, 1);
                            if ($this->getLevel($player) < 10){ # Nhỏ Hơn 10
                                $cup = Item::get(257, 0, 1);

                        }elseif($this->getLevel($player) >= 10 and $this->getLevel($player) < 10){
                                $cup = Item::get(257, 0, 1);
                            }elseif( $this->getLevel($player) > 10){
                                $cup = Item::get(278, 0, 1);
                            }
                            $pickname = $this->getNamePickaxe($player);
                            $cup->setCustomName($pickname);
                            $cup->setLore(array($this->getLore()));
                            $id = 15;
                $lv = $this->getLevel($p)/2.5;
                $cup->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment($id), $lv));
                    $id2 = 18;
                    $lv2 = $this->getLevel($p)/3;
                    $cup->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment($id2), $lv2));
                    $ce = $this->getServer()->getPluginManager()->getPlugin("PiggyCustomEnchants");
					if($ce instanceof CE){
			$cuplevel = $this->getLevel($p);
				if($cuplevel > 150){
				    $ename = "ENERGIZING";
					$cup = $ce->addEnchantment($cup, 202, 2);
				}elseif($cuplevel > 50){
				    $ename = "ENERGIZING";
					$cup = $ce->addEnchantment($cup, 202, 1);
				}
				if($cuplevel > 500){
				    $ename = "JACKPOT";
					$cup = $ce->addEnchantment($cup, 212, 5);
				}elseif($cuplevel > 400){
				    $ename = "JACKPOT";
					$cup = $ce->addEnchantment($cup, 212, 4);
				}elseif($cuplevel > 300){
				    $ename = "JACKPOT";
					$cup = $ce->addEnchantment($cup, 212, 3);
				}elseif($cuplevel > 200){
				    $ename = "JACKPOT";
					$cup = $ce->addEnchantment($cup, 212, 2);
				}elseif($cuplevel > 100){
				    $ename = "JACKPOT";
					$cup = $ce->addEnchantment($cup, 212, 1);
				}
				if($cuplevel > 450){
				    $ename = "HASTE";
					$cup = $ce->addEnchantment($cup, 207, 3);
				}elseif($cuplevel > 350){
				    $ename = "HASTE";
					$cup = $ce->addEnchantment($cup, 207, 2);
				}else if($cuplevel > 250){
				    $ename = "HASTE";
					$cup = $ce->addEnchantment($cup, 207, 1);
				}
			}
                            $inv->addItem($cup);
                            #$this->getServer()->broadcastMessage($this->prefix . "§aCúp của người chơi §c".$player->getName()."§a đã được hồi sinh thành công");
                            $player->sendMessage($this->prefix . "§aCúp của bạn đã hồi sinh thành công");
                        }
                    }
                }

             }else{
                 $sender->sendMessage("§cBạn không có quyền để sử dụng lệnh này");
                 return true;
             }
             
         }elseif($cmd->getName() == "topcup"){
		 $max = 0;
				 $c = $this->level->getAll();			
            $max = count($c);
				$max = ceil(($max / 5));
				$page = array_shift($args);
				$page = max(1, $page);
				$page = min($max, $page);
				$page = (int)$page;
			
				$aa = $this->level->getAll();
				arsort($aa);
				$i = 0;
			
					$sender->sendMessage("§¶§f-= §bTop pickaxe in §aMineTown §b(§a".$page."§f/§a".$max."§b) §f=-");

				
				foreach($aa as $b=>$a){
				if(($page - 1) * 5 <= $i && $i <= ($page - 1) * 5 + 4){
				$i1 = $i + 1;
				
				$message = "§¶§f[§c".$i1."§f] §b".$b.": §f".$a." §elevel\n";
			//	if(!$sender instanceof Player){
					 	$sender->sendMessage($message);
				}
				$i++;
				
				}
		 }elseif($cmd->getName() == "chuyensinh"){
		     $amount = $this->getRebirth($sender);
		     $price = $amount * 10000000;
		     $api = $this->getServer()->getPluginManager()->getPlugin("EconomyAPI");
					$pmoney = ($api->myMoney($sender)+1);
					if($price <= $pmoney){
	$api->setMoney($sender->getName(), 0);
	$this->setRebirth($sender, $amount+1);
	$sender->sendMessage(str_replace("{REBIRTHS}", $amount + 1, "§l§a♥ §e[§aMine§bTown§e] §aBạn Đã Rebirth Thành Công Và Lên Rebirth {REBIRTHS}"));
	$this->rbcoin->addRebirthCoin($sender->getName(), "10");
					}else{$sender->sendMessage("§l§a♥ §e[§aMine§bTown§e] §aBạn Không Đủ Tiền Để Rebirth. Số Tiền Cần Để Rebirth Là: " . $price );}
		 }elseif($cmd->getName() == "topchuyensinh"){
		 $max = 0;
				 $c = $this->rebirth->getAll();			
            $max = count($c);
				$max = ceil(($max / 5));
				$page = array_shift($args);
				$page = max(1, $page);
				$page = min($max, $page);
				$page = (int)$page;
			
				$aa = $this->rebirth->getAll();
				arsort($aa);
				$i = 0;
			
					$sender->sendMessage("§¶§f-= §bTop Chuyển Sinh Trong §aMineTown §b(§a".$page."§f/§a".$max."§b) §f=-");

				
				foreach($aa as $b=>$a){
				if(($page - 1) * 5 <= $i && $i <= ($page - 1) * 5 + 4){
				$i1 = $i + 1;
				
				$message = "§¶§f[§c".$i1."§f] §b".$b.": §f".$a." §eChuyển Sinh\n";
			//	if(!$sender instanceof Player){
					 	$sender->sendMessage($message);
				}
				$i++;
				
				}
		 }elseif($cmd->getName() == "solanchuyensinh"){
		     $amount = $this->getRebirth($sender);
	$sender->sendMessage(str_replace("{REBIRTHS}", $amount, "§l§a♥ §e[§aMine§bTown§e] §aBạn Đã Rebirth Lần Thứ {REBIRTHS}"));
		 }elseif($cmd->getName() == "setcuplevel"){
             if($sender->isOp()){
                if(!isset($args[0])){
                    $sender->sendMessage($this->prefix . "§aSử dụng §e/setcuplevel §f<Tên Người Chơi> <Cấp> §ađể thay đổi Cấp Của pickaxe level");
                    return true;
                }elseif(!isset($args[1]) || !is_numeric($args[1])){
                    $sender->sendMessage($this->prefix . "§aSử dụng §e/setcuplevel §f<Tên Người Chơi> <Cấp> §ađể thay đổi Cấp Của pickaxe level");
                }else{
    if(($p = $this->getServer()->getPlayer($args[0])) instanceof Player){
        $this->setLevel($p, $args[1]);
                }
                }
                }else{
    $sender->sendMessage("Bạn Không Có Quyền Để Sử Dụng Lệnh Này");
                }
                }elseif($cmd->getName() == "setchuyensinh"){
             if($sender->isOp()){
                if(!isset($args[0])){
                    $sender->sendMessage($this->prefix . "§aSử dụng §e/setchuyensinh §f<Tên Người Chơi> <Số Lần Chuyển Sinh> §ađể thay đổi Số Lând Chuyển Sinh");
                    return true;
                }elseif(!isset($args[1]) || !is_numeric($args[1])){
                    $sender->sendMessage($this->prefix . "§aSử dụng §e/setchuyensinh §f<Tên Người Chơi> <Số Lần Chuyển Sinh> §ađể thay đổi Số Lần Chuyển Sinh");
                }else{
    if(($p = $this->getServer()->getPlayer($args[0])) instanceof Player){
        $this->setRebirth($p, $args[1]);
                }
                }
                }else{
    $sender->sendMessage("Bạn Không Có Quyền Để Sử Dụng Lệnh Này");
                }
                }elseif($cmd->getName() == "tangtilecs"){
                    $amount = $this->getRebirthu($sender);
        if($amount == 100){
            $sender->sendMessage("Đã Max Level");
            return false;
        }
		     $price = 30;
					$pmoney = ($this->rbcoin->myRebirthCoin($sender)+1);
					if($price < $pmoney){
	$this->rbcoin->reduceRebirthCoin($sender->getName(), 30);
	$this->setRebirthu($sender, $amount - 1);
	$sender->sendMessage(str_replace("{REBIRTHS}", $amount - 1, "§l§a♥ §e[§aMine§bTown§e] §aBạn Đã Tăng Tỉ Lệ Mine Xu Thành Công Và Tăng Tỉ Lệ Mine Xu Thành {REBIRTHS}"));
					}else{$sender->sendMessage("§l§a♥ §e[§aMine§bTown§e] §aBạn Không Đủ Tiền Để Tăng Tỉ Lệ Mine Xu. Số Tiền Cần Để Tăng Tỉ Lệ Mine Xu Là: " . $price . " Rebirth Coin");}
                }
         return true;
     }

	public function getLevel($player){
		if($player instanceof Player){
		$name = $player->getName();
		}
		$level = $this->lv->get(strtolower($name))["Level"];
		return $level;
	}
	
	public function getRebirth($player){
		if($player instanceof Player){
		$name = $player->getName();
		}
		$level = $this->rebirth->get(strtolower($name));
		return $level;
	}
	
	public function getRebirthu($player){
		if($player instanceof Player){
		$name = $player->getName();
		}
		$level = $this->rebirthu->get(strtolower($name));
		return $level;
	}
	
	public function setRebirth($player, $level){
		if($player instanceof Player){
			$name = $player->getName();
		}
          $this->rebirth->set(strtolower($name), $level);
          $this->rebirth->save();
	}
	
	public function setRebirthu($player, $level){
		if($player instanceof Player){
			$name = $player->getName();
		}
          $this->rebirthu->set(strtolower($name), $level);
          $this->rebirthu->save();
	}
	
	public function setLevel($player, $level){
		if($player instanceof Player){
			$name = $player->getName();
		}
          $nextexp = ($this->getLevel($player)+1)*120;
          $this->lv->set(strtolower($name), ["Level" => $level, "exp" => 0, "nextexp" => $nextexp]);
          $this->lv->save();
		  /*
		  */
		//  $this->level->getAll("Level")[strtolower($name)] = $level;
		$this->level->set(strtolower($name), $level);
		$this->level->save();
		 
		  /*
		  */
	}

	public function setNextExp($player, $exp){
		if($player instanceof Player){
			$player = $player->getName();
		}

		$player = strtolower($player);
		$lv = $this->lv->get($player)["nextexp"] + $exp;
		$this->lv->set($player, ["Level" => $this->lv->get($player)["Level"], "exp" => $this->lv->get($player)["exp"], "nextexp" => $lv]);
		$this->lv->save();
	}

	public function getExp($player){
		if($player instanceof Player){
			$player = $player->getName();
		}

		$player = strtolower($player);
		$e = $this->lv->get($player)["exp"];
		return $e;
	}

	public function getNextExp($player){
		if($player instanceof Player){
			$player = $player->getName();
		}

		$player = strtolower($player);
		$lv = $this->lv->get($player)["nextexp"];
		return $lv;
	}

	public function addExp($player, $exp){
		if($player instanceof Player){
			$player = $player->getName();
		}

		$player = strtolower($player);
		$e = $this->lv->get($player)["exp"];
		$lv = $this->lv->get($player)["Level"];
		$this->lv->set($player, ["Level" => $lv, "exp" => $e + $exp, "nextexp" => $this->getNextExp($player)]);
		$this->lv->save();

	}
    public function addCE(CommandSender $sender, $enchantment, $level, $target)
    {
        $plugin = $this->CE;
        if ($plugin instanceof CE) {
            if (!is_numeric($level)) {
                $level = 1;
                $sender->sendMessage(TextFormat::RED . "Level must be numerical. Setting level to 1.");
            }
            $target == null ? $target = $sender : $target = $this->getServer()->getPlayer($target);
            if (!$target instanceof Player) {
                if ($target instanceof ConsoleCommandSender) {
                    $sender->sendMessage(TextFormat::RED . "Please provide a player.");
                    return;
                }
                $sender->sendMessage(TextFormat::RED . "Invalid player.");
                return;
            }
            $target->getInventory()->setItemInHand($plugin->addEnchantment($target->getInventory()->getItemInHand(), $enchantment, $level, $sender->hasPermission("piggycustomenchants.overridecheck") ? false : true, $sender));
        }
    }
}