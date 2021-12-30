<?php

declare(strict_types=1);

namespace CLADevs\Minion\minion;

use CLADevs\Minion\Main as Plu;
use pocketmine\command\Command;
use pocketmine\plugin\PluginBase;
use pocketmine\command\CommandSender;
use pocketmine\block\Block;
use pocketmine\block\Chest;
use pocketmine\entity\Human;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\utils\Config;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\item\Item;
use pocketmine\level\Position;
use pocketmine\math\Vector3;
use pocketmine\network\mcpe\protocol\AnimatePacket;
use onebone\economyapi\EconomyAPI;
use DaPigGuy\PiggyCustomEnchants\CustomEnchants\CustomEnchants;
use pocketmine\item\enchantment\{Enchantment, EnchantmentInstance};
use DaPigGuy\PiggyCustomEnchants\Main as CE;
use pocketmine\entity\Entity;
use pocketmine\Player;
use pocketmine\Server;
use pocketmine\utils\TextFormat as C;
use SellHand\Main as el;
use function yaml_parse;

class Minion extends Human{

    protected $player;
    public $itemhand;
    public $inv;
    public $pointapi;
    public $picklv;
    public $eco;
    private $mode;
    protected $nosell;
	
    public function initEntity(): void{
        parent::initEntity();
        $this->player = $this->namedtag->getString("player");
        $this->setHealth(1);
        $this->setMaxHealth(1);
        $this->setNameTagAlwaysVisible();
        $this->setNameTag("§l§c♥ §aĐệ Tử§b\n§eCủa §f" . $this->player . "\n§b♥ §fLevel: §r§a" . $this->getLevelM()."\n§l§eAutoFix: §r§c".$this->getAutoFix()."\n§l§6Eternity: §r§9".$this->getEter());
        $this->setScale((float)Plu::get()->getConfig()->get("size"));
        $this->sendSpawnItems();
    }

    public function attack(EntityDamageEvent $source): void{
        $source->setCancelled();
        if($source instanceof EntityDamageByEntityEvent){
            $damager = $source->getDamager();
            if($damager instanceof Player){
            if($damager->getName() !== $this->player){
                if(!$damager->hasPermission("detu.openall")){
                    $damager->sendMessage(C::RED . "§l§c♥ §aĐệ Tử§b → Đây Không Phải Đệ Tử Của Bạn.");
                    return;
                }}
                $pos = new Position(intval($damager->getX()), intval($damager->getY()) + 2, intval($damager->getZ()), $damager->getLevel());
                $damager->addWindow(new HopperInventory($pos, $this));
            }
        }
    }

    public function entityBaseTick(int $tickDiff = 1): bool{
        $this->eco = Plu::get()->getServer()->getPluginManager()->getPlugin("EconomyAPI");
		$this->picklv = Plu::get()->getServer()->getPluginManager()->getPlugin("PickaxeLevelV7");
        $this->pointapi = Plu::get()->getServer()->getPluginManager()->getPlugin("PointAPI");
        $update = parent::entityBaseTick($tickDiff);
        if($this->getLevel()->getServer()->getTick() % $this->getMineTime() == 0){
            //Checks if theres a chest behind him
            if($this->getLookingBehind() instanceof Chest){
                $b = $this->getLookingBehind();
                $this->namedtag->setString("xyz", $b->getX() . ":" . $b->getY() . ":" . $b->getZ());
            }
            //Update the coordinates
            if($this->namedtag->getString("xyz") !== "n"){
                if(isset($this->getCoord()[1])){
                    $block = $this->getLevel()->getBlock(new Vector3(intval($this->getCoord()[0]), intval($this->getCoord()[1]), intval($this->getCoord()[2])));
                    if(!$block instanceof Chest){
                        $this->namedtag->setString("xyz", "n");
                    }
                    $k = $this->getLevel()->getBlock(new Vector3(intval($this->getCoord()[0] ?? 0), intval($this->getCoord()[1] ?? 0), intval($this->getCoord()[2] ?? 0)));
        $tile = $this->getLevel()->getTile($k);
        if($tile instanceof \pocketmine\tile\Chest){
            $inv = $tile->getInventory();
            $this->itemhand = $inv->getItem(0);
            $this->inv = $inv;
            $this->setNameTag("§l§c♥ §aĐệ Tử§b\n§eCủa §f" . $this->player . "\n§b♥ §fLevel: §r§a" . $this->getLevelM()."\n§l§eAutoFix: §r§c".$this->getAutoFix()."\n§l§6Eternity: §r§9".$this->getEter());
            Entity::registerEntity(Self::class, true);
            $player = $this->getLevel()->getServer()->getPlayer($this->player);
            $wood = Item::get(270);
            $stone = Item::get(274);
            $iron = Item::get(257);
            $gold = Item::get(285);
            $diamond = Item::get(278);
            $air = Item::get(Item::AIR);
        $this->getInventory()->setItemInHand($this->itemhand ?? $air);
        $this->getInventory()->sendHeldItem($this->getViewers());
        if($this->itemhand->getId() !== 278){
		if($player = Plu::get()->getServer()->getPlayer($this->player)){
            $player->sendMessage("§l§c♥ §aĐệ Tử§b → Đệ Của Bạn Không Thể Mine Vì Không Có Cúp Hoặc Không Phải Cúp §l§6(Chỉ Sử Dụng Cúp Kim Cương)");
            return false;
        }
        }
                }
            }
			}
            //Breaks
            if ($this->getLookingBlock()->getId() !== Block::AIR and $this->isChestLinked()){
                if($this->checkEverythingElse()){
                    $pk = new AnimatePacket();
                    $pk->entityRuntimeId = $this->id;
                    $pk->action = AnimatePacket::ACTION_SWING_ARM;
                    foreach (Server::getInstance()->getOnlinePlayers() as $p) $p->dataPacket($pk);
                    $this->breakBlock($this->getLookingBlock());
                }
            }
        }
        return $update;
    }

    public function sendSpawnItems(): void{
        $air = Item::get(Item::AIR);
        $this->getInventory()->setItemInHand($this->itemhand ?? $air);
        $this->getArmorInventory()->setHelmet( Item::get(Item::AIR));
        $this->getArmorInventory()->setChestplate(Item::get(Item::AIR));
        $this->getArmorInventory()->setLeggings(Item::get(Item::AIR));
        $this->getArmorInventory()->setBoots(Item::get(Item::AIR));
    }

    public function getLookingBlock(): Block{
        $block = Block::get(Block::AIR);
        switch($this->getDirection()){
            case 0:
                $block = $this->getLevel()->getBlock($this->add(1, 0, 0));
                break;
            case 1:
                $block = $this->getLevel()->getBlock($this->add(0, 0, 1));
                break;
            case 2:
                $block = $this->getLevel()->getBlock($this->add(-1, 0, 0));
                break;
            case 3:
                $block = $this->getLevel()->getBlock($this->add(0, 0, -1));
                break;
        }
        return $block;
    }

    public function getLookingBehind(): Block{
        $block = Block::get(Block::AIR);
        switch($this->getDirection()){
            case 0:
                $block = $this->getLevel()->getBlock($this->add(-1, 0, 0));
                break;
            case 1:
                $block = $this->getLevel()->getBlock($this->add(0, 0, -1));
                break;
            case 2:
                $block = $this->getLevel()->getBlock($this->add(1, 0, 0));
                break;
            case 3:
                $block = $this->getLevel()->getBlock($this->add(0, 0, 1));
                break;
        }
        return $block;
    }

    public function checkEverythingElse(): bool{
        $player = $this->getLevel()->getServer()->getPlayer($this->player);
        $damage = $this->itemhand->getDamage();
        if($damage >= 1560){
            if($player = Plu::get()->getServer()->getPlayer($this->player)){
        $player->sendMessage("§l§c♥ §aĐệ Tử§b → Cúp Của Đệ Tử Sắp Hỏng Hãy Fix Để Đệ Tiếp Tục Mine");
            }
        return false;
        }
        $player = $this->getLevel()->getServer()->getPlayer($this->player);
        if(null == $player){
            return false;
        }
        $land = $this->getLevel()->getServer()->getPluginManager()->getPlugin("SellAll");
        $sell = new Config($land->getDataFolder() . "sell.yml", Config::YAML);
        $block = $this->getLevel()->getBlock(new Vector3(intval($this->getCoord()[0]), intval($this->getCoord()[1]), intval($this->getCoord()[2])));
        $tile = $this->getLevel()->getTile($block);

        if($tile instanceof \pocketmine\tile\Chest){
            $inventory = $tile->getInventory();

            if(Plu::get()->getConfig()->getNested("blocks.normal")){
                foreach($block->getDropsForCompatibleTool($this->itemhand) as $drop){
                    if(!$inventory->canAddItem($drop)){
    $items = $inventory->getContents();
						foreach($items as $item){
							if($sell->get($item->getId()) !== null && $sell->get($item->getId()) > 0){
								$price = $sell->get($item->getId()) * $item->getCount();
								EconomyAPI::getInstance()->addMoney($this->player, $price);
								if(Plu::get()->mode($this->player) == "on"){
								    if($plauer = Plu::get()->getServer()->getPlayer($this->player)){
								$player->sendMessage(C::GREEN . C::BOLD . "(!) " . C::RESET . C::GREEN . "Bạn đã nhận được " . C::RED . "$" . $price . C::GREEN . " từ§b§l đệ Tử §r" . C::GREEN . "của bạn khi bán (" . $item->getCount() . " " . $item->getName() . " với $" . $sell->get($item->getId()) . " với mỗi vật phẩm).");
							}
								}
								$inventory->remove($item);
							}
							}
                }
						}
    if($inventory->canAddItem($drop)){ return true;}elseif(!$inventory->canAddItem($drop)){ 
        if($player = Plu::get()->getServer()->getPlayer($this->player)){
        $player->sendMessage(C::RED . C::BOLD . "§l§c♥ §aĐệ Tử§b → Đệ Của Bạn Không Bán Được Item Trong Chest");
        return false;
        }
        }
            }elseif(!in_array($block->getId(), Plu::get()->getConfig()->getNested("blocks.cannot"))){
    if($player = Plu::get()->getServer()->getPlayer($this->player)){
        $player->sendMessage(C::RED . C::BOLD . "§l§c♥ §aĐệ Tử§b → Đệ Của Bạn Không Thể Phá Được Block Này");
    }
        return false;
            }
            return false;
        }
        return false;
    }

    public function breakBlock(Block $block): bool{
		$i = $this->itemhand;
		$icn = $i->getCustomName();
		$pas = explode(" ", $icn);
        $player = $this->getLevel()->getServer()->getPlayer($this->player);
        $p = $player;
        $amount = $this->picklv->getRebirth($p);
        $amount2 = $this->picklv->getRebirthu($p);
		$name = $p->getName();
        $b = $this->getLevel()->getBlock(new Vector3(intval($this->getCoord()[0]), intval($this->getCoord()[1]), intval($this->getCoord()[2])));
        $tile = $this->getLevel()->getTile($b);
        $damage = $this->itemhand->getDamage();
		if($amount > 1){
		    $id = mt_rand(0, $amount2);
		    if($id == 0){
	$money = $amount * 10000;
	$this->eco->addMoney($p->getName(), $money);
	if($p = Plu::get()->getServer()->getPlayer($this->player)){
	if(Plu::get()->mode($this->player) == "on")
	$p->sendMessage(str_replace("{MONEY}", $money,"§l§c♥ §aĐệ Tử§b → Bạn Đã Nhận Được {MONEY} Xu Khi Đệ Mine\n(Xu Này Từ Nguồn Rebirth Của Bạn)"));
	}
			}
		}
	$id2 = mt_rand(0,100000);
        if($id2 == 0){
    switch($block->getId()) {
        case Item::COBBLESTONE:
            $item = Item::get(433);
            $item->setCustomName("§l§f[ §bMảnh §aMiner §f]");
            $item->setLore(["§l§f➜ §eItem cực hiếm khó rơi ra.
§l§f➜ §cTỉ lệ rơi §f: §60.00001℅.\n§l§f➜ §aDùng để chế tạo trong §6Custom craft.\n§l§f➜ §4Giữ cẩn thận tránh làm mất."]);
            $enchantment = Enchantment::getEnchantment(22);
            $item->addEnchantment(new EnchantmentInstance($enchantment, 1000));
            $event->setDrops([$item]);
            Server::getInstance()->broadcastMessage(str_replace("{player}", $event->getPlayer()->getName(), "§l§8{player} §r§eVừa Mine Được §l§f[ §bMảnh §aMiner §f]"));
            break;
    }
        }
		if($pas[0] == "§r§l§a⚒§6")
		if(strpos($icn, $name)  == false){
				if($p = Plu::get()->getServer()->getPlayer($this->player)){
				$p->sendMessage($this->picklv->prefix . "§aCúp này không phải của bạn. Vì vậy đệ sẽ không đào được");
				}
				return false;
		}else{
		    if($pas[0] == "§r§l§a⚒§6"){
				$name = strtolower($p->getName());
				$n = $this->picklv->lv->get($name);
				
               switch($block->getId()) {
                   case 56:// Kim Cương Ore
                       $this->picklv->addExp($p, 2);
                       break;
                   case 14:// Vàng Ore
                       $this->picklv->addExp($p, 2);
                       break;
                   case 15:// Sắt Ore
                       $this->picklv->addExp($p, 2);
                       break;
                   case 16:// Than Ore
                       $this->picklv->addExp($p, 2);
                       break;
                   case 129:// Emerald Ore
                       $this->picklv->addExp($p, 2);
                       break;
                   case 21:// Lapis Lazuli Ore
                       $this->picklv->addExp($p, 2);
                       break;
                   case 22:// Lapis Lazuli Block
                       $this->picklv->addExp($p, 3);
                       break;
                   case 133:// Emerald Block
                       $this->picklv->addExp($p, 3);
                       break;
                   case 57:// Kim Cương Block
                       $this->picklv->addExp($p, 3);
					   break;
                   case 42:// Sắt Block
                       $this->picklv->addExp($p, 3);
					   break;
                   case 41:// Vàng Block
                       $this->picklv->addExp($p, 3);
                       break;
                   default:// All Khối
                       $this->picklv->addExp($p, 1);
                       break;

                }
				if($this->picklv->getExp($p) >= $this->picklv->getNextExp($p)){
					$this->picklv->setLevel($p, $this->picklv->getLevel($p) +1);
					#$money = $this->getLevel($p) * 1000;
					$money = 1000;
					if(in_array($this->picklv->getLevel($p), array(100,200,300,400,500 ))){
					    #$point = $this->getLevel($p)/2;
					    $point = 2;
                        $this->pointapi->addPoint($p->getName(), $point);
                        if($p = Plu::get()->getServer()->getPlayer($this->player)){
                        $p->sendMessage($this->picklv->prefix . "§aBạn đã nhận được §c" . $point . "point §atừ phần thưởng");
                        }
                    }
					$this->eco->addMoney($p->getName(), $money);
					Plu::get()->getServer()->broadcastMessage($this->picklv->prefix . "§aCúp của người chơi §c".$p->getName()."§a vừa lên cấp§c ".$this->picklv->getLevel($p)."§e do đệ Tử mine");
					if($p = Plu::get()->getServer()->getPlayer($this->player)){
					$p->sendMessage($this->picklv->prefix . "§aChúc mừng cúp của bạn đã đạt Level §c".$this->picklv->getLevel($p)."§9 do đệ Tử mine");
					$p->sendMessage($this->picklv->prefix . "§aHãy kiểm tra lại phần thưởng trong túi đồ nhé");
					$p->sendMessage($this->picklv->prefix . "§aBạn đã nhận được §c" . $money . " Xu §atừ phần thưởng §9do đệ Tử mine");
					}
                $id = 15;
                $lv = $this->picklv->getLevel($p)/2.5;
                $i->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment($id), (int)$lv));
                if($p = Plu::get()->getServer()->getPlayer($this->player)){
                $p->sendMessage($this->picklv->prefix . "§aCúp của bạn đã được cường hóa: Hiệu xuất level ".$lv);
                }
                    $id = 18;
                    $lv = $this->picklv->getLevel($p)/3;
                    $i->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment($id), (int)$lv));
                    if($p = Plu::get()->getServer()->getPlayer($this->player)){
                    $p->sendMessage($this->picklv->prefix . "§aCúp của bạn đã được cường hóa: Gia tài level ".$lv);
                    }
            $ce = Plu::get()->getServer()->getPluginManager()->getPlugin("PiggyCustomEnchants");
            $cup = $this->itemhand;
					if($ce instanceof CE){
			$cuplevel = $this->picklv->getLevel($p);
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
            $this->inv->setItem(0, $cup);
				}
		//	}
	  }
		}
        $add5 = 0;
        if(($level2 = $this->itemhand->getEnchantmentLevel(Enchantment::UNBREAKING)) > 0){
            $add2 = rand(0, $level2);
            $add3 = rand(0, $add2);
            $add4 = rand(0, $add3);
            $add5 = rand(0, $add4);
        }
        $level = 0;
        if($add5 == 0){
        $dam = $this->itemhand->setDamage($damage + 1);
            $this->inv->setItem(0, $dam);
        }
        if($this->getAutoFix() === "yes"){
            if($damage >= 1500){
    $dam = $this->itemhand->setDamage(0);
            $this->inv->setItem(0, $dam);
        }
        }
        if($tile instanceof \pocketmine\tile\Chest){
            $inv = $tile->getInventory();
            if(Plu::get()->getConfig()->getNested("blocks.normal")){
                $drop = $block->getDrops($this->itemhand);
                /*foreach($drop as $drops){
                $inv->addItem($drops);
                }*/
               if(($level = $this->itemhand->getEnchantmentLevel(Enchantment::FORTUNE)) > 0 || $this->itemhand->getEnchantmentLevel(Enchantment::SILK_TOUCH) > 0){
                   if(in_array($block->getId(), [Item::NETHER_QUARTZ_ORE,Item::LEAVES,Item::EMERALD_ORE,Item::REDSTONE_ORE,Item::LAPIS_ORE,Item::DIAMOND_ORE,Item::COAL_ORE])){
					$add = rand(0, $level);
					switch($block->getId()){
						case Item::COAL_ORE:
						     if($silk = $this->itemhand->getEnchantmentLevel(Enchantment::SILK_TOUCH) > 0){$inv->addItem(Item::get(Item::COAL_ORE, 0, 1 + $add));}else{
						     $inv->addItem(Item::get(Item::COAL, 0, 1 + $add));}
						break;
						case Item::DIAMOND_ORE:
							if($this->itemhand->getEnchantmentLevel(Enchantment::SILK_TOUCH) > 0){$inv->addItem(Item::get(Item::DIAMOND_ORE, 0, 1 + $add));}else{
							$inv->addItem(Item::get(Item::DIAMOND, 0, 1 + $add));}
						break;
						case Item::LAPIS_ORE:
							if($this->itemhand->getEnchantmentLevel(Enchantment::SILK_TOUCH) > 0){$inv->addItem(Item::get(Item::LAPIS_ORE, 4, 1 + $add));}else{
							$inv->addItem(Item::get(Item::DYE, 4, rand(4, 8) + $add));}
						break;
						case Item::REDSTONE_ORE:
							if($this->itemhand->getEnchantmentLevel(Enchantment::SILK_TOUCH) > 0){$inv->addItem(Item::get(Item::REDSTONE_ORE, 0, 1 + $add));}else{
							$inv->addItem(Item::get(Item::REDSTONE_DUST, 0, rand(4, 8) + $add));}
						break;
						case Item::EMERALD_ORE:
							if($this->itemhand->getEnchantmentLevel(Enchantment::SILK_TOUCH) > 0){$inv->addItem(Item::get(Item::EMERALD_ORE, 0, 1 + $add));}else{
							$inv->addItem(Item::get(Item::EMERALD, 0, 1 + $add));}
						break;
						case Item::LEAVES:
							if(rand(0, 100) <= $level * 2){
							    if($this->itemhand->getEnchantmentLevel(Enchantment::SILK_TOUCH) > 0){$inv->addItem(Item::get(Item::LEAVES));}else{
								$inv->addItem(Item::get(Item::APPLE));}
							}
						break;
						case Item::NETHER_QUARTZ_ORE:
							if($this->itemhand->getEnchantmentLevel(Enchantment::SILK_TOUCH) > 0){$inv->addItem(Item::get(Item::NETHER_QUARTZ, 1, 1 + $add));}else{
							$inv->addItem(Item::get(Item::NETHER_QUARTZ, 1, rand(4, 8) + $add));}
						break;
					}
					if($this->getEter() === "no"){
					$this->getLevel()->setBlock($block, Block::get(Block::AIR), true, true);
					}
			    return false;
                   }
                }
						    if($this->itemhand->getEnchantmentLevel(Enchantment::SILK_TOUCH) > 0){
				        foreach($drop as $drops){
                $inv->addItem($drops);
				        }
				    }else{
                foreach($drop as $drops){
                $inv->addItem($drops);
				        }
                    if($this->getEter() === "no"){
						$this->getLevel()->setBlock($block, Block::get(Block::AIR), true, true);
                    }
                }
				    }
					}else{
                if(in_array($block->getId(), Plu::get()->getConfig()->getNested("blocks.cannot"))){
                    return false;
                //$inv->addItem(Item::get($block->getId(), $block->getDamage()));
            }
        }
        if($this->getEter() === "no"){
        $this->getLevel()->setBlock($block, Block::get(Block::AIR), true, true);
        }
        return true;
    }

    public function getMaxTime(): int{
        return (1 * Plu::get()->getConfig()->getNested("level.max")) + 1;
    }

    public function getMineTime(): int{
        return $this->getMaxTime() - (1 * $this->namedtag->getInt("level"));
    }

    public function getCost(): int{
        return Plu::get()->getConfig()->getNested("level.cost") * $this->getLevelM();
    }

    public function getLevelM(): int{
        return $this->namedtag->getInt("level");
    }
    
    public function getEter(): string{
        return $this->namedtag->getString("eternity") ?? "no";
    }
    
    public function getAutoFix(): string{
        return $this->namedtag->getString("autofix") ?? "no";
    }

    public function isChestLinked(): bool{
        return $this->namedtag->getString("xyz") === "n" ? false : true;
    }

    public function getChestCoordinates(): string{
        if(!isset($this->getCoord()[1])){
            return C::RED . "§l§c♥ §aĐệ Tử§b → Không Tìm Thấy Kết Nối";
        }
        $coord = C::YELLOW . "X: " . C::WHITE . $this->getCoord()[0] . " ";
        $coord .= C::YELLOW . "Y: " . C::WHITE . $this->getCoord()[1] . " ";
        $coord .= C::YELLOW . "Z: " . C::WHITE . $this->getCoord()[2] . " ";
        return $coord;
    }

    public function getCoord(): array{
        $coord = explode(":", $this->namedtag->getString("xyz"));
        return $coord;
    }
}
