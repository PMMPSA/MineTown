<?php

namespace SkillMiner;

use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\nbt\tag\{CompoundTag, IntTag, StringTag, IntArrayTag};
use pocketmine\utils\TextFormat as TF;
use pocketmine\Player;
use pocketmine\command\{Command,CommandSender, CommandExecutor, ConsoleCommandSender};
use pocketmine\entity\Effect;
use pocketmine\entity\EffectInstance;
use pocketmine\event\player\PlayerInteractEvent;
use muqsit\invmenu\inventories\BaseFakeInventory;
use muqsit\invmenu\{InvMenu,InvMenuHandler};
use muqsit\invmenu\inventories\ChestInventory;
use pocketmine\inventory\PlayerInventory;
use pocketmine\utils\Config;
use SkillMiner\CooldownTask;
use pocketmine\scheduler\ClosureTask;
use pocketmine\item\Item;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\item\ItemFactory;
use pocketmine\event\player\PlayerJoinEvent;


class Main extends PluginBase implements Listener{

	private $a = [];
	public $kingofblock = [];
	public $pickaxeleveling = [];
	public $richdreamer = [];
	public $rebirthminer = [];
	public $eternity = [];


	public function onEnable(){
		$this->getLogger()->info(TF::GREEN . "SkillMiner by GreenJajot");
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
		$this->getScheduler()->scheduleRepeatingTask(new CooldownTask($this, 20), 20);
		$this->saveDefaultConfig();
		$this->cooldown = new Config($this->getDataFolder(). "cooldowns.yml", Config::YAML);
		if(!is_dir($this->getDataFolder())) mkdir($this->getDataFolder());
		$this->autosell = $this->getServer()->getPluginManager()->getPlugin("AutoSell");
	}
	
	public function get(){
	    return $this;
	}

    public function onJoin (PlayerJoinEvent $j)
	{
	    $player = $j->getPlayer()->getName();
		$this->kingofblock[strtolower($player)] = "off";
		$this->pickaxeleveling[strtolower($player)] = "off";
		$this->richdreamer[strtolower($player)] = "off";
		$this->rebirthminer[strtolower($player)] = "off";
		$this->eternity[strtolower($player)] = "off";
	}

	public function onDisable(){
		$this->cooldown->save();
	}
	public function onCommand(CommandSender $sender, Command $cmd, string $label, array $args): bool{
       if (strtolower($cmd->getName()) == "skill") {
       $this->MenuSkill($sender);
       return true;
    }elseif(strtolower($cmd->getName()) == "muaskill") {
       $this->MenuMuaSkill($sender);
       return true;
    }
    return false;
	}

	public function newCooldown($player,$time,$name,$time2){
		$time3 = $this->cooldown->getAll();
		$time3[strtolower($player->getName())][$name] = $time;
		$time3[strtolower($player->getName())]["cooldown"] = $time2;
		$this->cooldown->setAll($time3);
		$this->a[strtolower($player->getName())][$name] = 1;
		$this->a[strtolower($player->getName())]["cooldown"] = 1;
		$this->cooldown->save();
	}

	public function timer(){
		foreach($this->cooldown->getAll() as $player => $time){
		    foreach($time as $time1=>$time2){
			if($time2 > 0 ){
			$time2--;
			$time3 = $this->cooldown->getAll();
			$time3[$player][$time1] = $time2;
			$this->cooldown->setAll($time3);
			$this->cooldown->save();
			}
			if($time2 == 0){
			unset($this->a[$player][$time1]);
			$time2--;
			if($player2 = $this->getServer()->getPlayer($player)){
			    if($time1 !== "cooldown"){
			$player2->sendMessage("Kĩ Năng $time1 Đã Hồi Xong");
			    }
			}
			$time3 = $this->cooldown->getAll();
			$time3[$player][$time1] = $time2;
			$this->cooldown->setAll($time3);
			$this->cooldown->save();
			}
		    }
		}
	}
	public function MenuMuaSkill(Player $player) {
		
		$menu = InvMenu::create(InvMenu::TYPE_DOUBLE_CHEST);
		$menu->setName("     §l§a★ §6MineTown Mua Skill §a★");
		$menu->readonly();
		$minv = $menu->getInventory();
		$al = Item::get(Item::LAPIS_BLOCK)->setLore(["§l§cBạn Đã Mua Kĩ Năng Này Rồi"]);
		$al->setNamedTagEntry(new StringTag("skillminer", "al"));
		if($player->hasPermission("skillminer.fastmine")){
		$minv->setItem(21, $al->setCustomName("FastMine"));
		}else{
		$fastmine = Item::get(Item::IRON_PICKAXE);
		$fastmine->setLore(["§l§eKhi Sử Dụng Giúp Tăng Tốc Độ Đào Lên Level 5\n§bThời Gian Tác Dụng: 60 Giây\n§cThời Gian Hồi Skill: 120 Giây Tương Đương 2 Phút\n§aGiá: 10§f RbCoin"]);
		$fastmine->setNamedTagEntry(new StringTag("skillminer", "fastmine"));
		$fastmine->setCustomName("FastMine");
		$minv->setItem(21, $fastmine);
		}

		if($player->hasPermission("skillminer.kingofblock")){
		$minv->setItem(22, $al->setCustomName("KingOfBlock"));
		}else{
		$kingofblock = Item::get(Item::DIAMOND_BLOCK);
		$kingofblock->setLore(["§l§eKhi Sử Dụng Giúp Biến Tất Cả Những Quặng Mine Được Thành Khối\n§bThời Gian Tác Dụng: 60 Giây\n§cThời Gian Skill: 900 Giây Tương Đương 15 Phút\n§aGiá: 150§f RbCoin"]);
		$kingofblock->setNamedTagEntry(new StringTag("skillminer", "kingofblock"));
		$kingofblock->setCustomName("KingOfBlock");
		$minv->setItem(22, $kingofblock);
		}
		
		if($player->hasPermission("skillminer.pickaxeleveling")){
		$minv->setItem(23, $al->setCustomName("PickaxeLevel"));
		}else{
		$pickaxeleveling = Item::get(Item::EXPERIENCE_BOTTLE);
		$pickaxeleveling->setLore(["§d§l§6Khi Sử Dụng Giúp X5 EXP Khi Mine Bằng Cúp Level\n§7Thời Gian Tác Dụng: 60 Giây\n§8Thời Gian Hồi Chiêu: 900 Giây Tương Đương 15 Phút\n§6Giá: 200"]);
		$pickaxeleveling->setNamedTagEntry(new StringTag("skillminer", "pickaxeleveling"));
		$pickaxeleveling->setCustomName("PickaxeLevel");
		$minv->setItem(23, $pickaxeleveling);
		}
		
		if($player->hasPermission("skillminer.richdreamer")){
		$minv->setItem(30, $al->setCustomName("RichDreamer"));
		}else{
		$richdreamer = Item::get(Item::EMERALD_BLOCK);
		$richdreamer->setLore(["§d§l§6Khi Sử Dụng Giúp X3 Giá Sell\n§7Thời Gian Tác Dụng: 60 Giây\n§8Thời Gian Hồi Chiêu: 1200 Giây Tương Đương 20 Phút\n§6Giá: 160"]);
		$richdreamer->setNamedTagEntry(new StringTag("skillminer", "richdreamer"));
		$richdreamer->setCustomName("RichDreamer");
		$minv->setItem(30, $richdreamer);
		}
		
		if($player->hasPermission("skillminer.rebirthminer")){
		$minv->setItem(31, $al->setCustomName("RebirthMiner"));
		}else{
		$rebirthminer = Item::get(Item::SLIME_BALL);
		$rebirthminer->setLore(["§d§l§6Khi Sử Dụng Giúp X5 Số Tiền Kiếm Được Từ Chuyển Sinh\n§7Thời Gian Tác Dụng: 60 Giây\n§8Thời Gian Hồi Chiêu: 900 Giây Tương Đương 15 Phút\n§6Giá: 140"]);
		$rebirthminer->setNamedTagEntry(new StringTag("skillminer", "rebirthminer"));
		$rebirthminer->setCustomName("RebirthMiner");
		$minv->setItem(31, $rebirthminer);
		}
		
		if($player->hasPermission("skillminer.eternity")){
		$minv->setItem(32, $al->setCustomName("Eternity"));
		}else{
		$eternity = Item::get(Item::DRAGON_EGG);
		$eternity->setLore(["§d§l§6Khi Sử Dụng Giúp Đào Không Mất Block\n§7Thời Gian Tác Dụng: 60 Giây\n§8Thời Gian Hồi Chiêu: 600 Giây Tương Đương 10 Phút\n§6Giá: 130"]);
		$eternity->setNamedTagEntry(new StringTag("skillminer", "eternity"));
		$eternity->setCustomName("Eternity");
		$minv->setItem(32, $eternity);
		}
		$menu->send($player);
		$menu->setListener([new MuaSkillListener($this),"onTransaction"]);
	}
	
	public function MenuSkill(Player $player) {
		
		$menu = InvMenu::create(InvMenu::TYPE_DOUBLE_CHEST);
		$menu->setName("§l§5★ §3Menu Skill §5★");
		$menu->readonly();
		$minv = $menu->getInventory();
		$cd = Item::get(Item::GOLD_BLOCK)->setLore(["§d§l§eKĩ Năng Này Đang Hồi"]);
		$cd->setNamedTagEntry(new StringTag("skillminer", "cd"));
		$al = Item::get(Item::LAPIS_BLOCK)->setLore(["§d§l§eKhông Thể Sử Dụng 2 Kĩ Năng 1 Lúc"]);
		$al->setNamedTagEntry(new StringTag("skillminer", "al"));
		$no = Item::get(Item::REDSTONE_BLOCK)->setLore(["§d§l§cBạn Chưa Sở Hữu Kĩ Năng Này"]);
		$no->setNamedTagEntry(new StringTag("skillminer", "no"));
		$minv->setItem(21, $no->setCustomName("FastMine"));
		if($player->hasPermission("skillminer.fastmine")){
		if(isset($this->a[strtolower($player->getName())]["cooldown"])){
		$minv->setItem(21, $al->setCustomName("FastMine"));
		}elseif(isset($this->a[strtolower($player->getName())]["FastMine"])){
		$minv->setItem(21, $cd->setCustomName("FastMine"));
		}else{
		$fastmine = Item::get(Item::IRON_PICKAXE);
		$fastmine->setLore(["§d§l§6Khi Sử Dụng Giúp Tăng Tốc Độ Đào Lên Level 5\n§7Thời Gian Tác Dụng: 60 Giây\n§8Thời Gian Hồi Chiêu: 120 Giây Tương Đương 2 Phút"]);
		$fastmine->setNamedTagEntry(new StringTag("skillminer", "fastmine"));
		$fastmine->setCustomName("FastMine");
		$minv->setItem(21, $fastmine);
		}
		}
		
		$minv->setItem(22, $no->setCustomName("KingOfBlock"));
		if($player->hasPermission("skillminer.kingofblock")){
		if(isset($this->a[strtolower($player->getName())]["cooldown"])){
		$minv->setItem(22, $al->setCustomName("KingOfBlock"));
		}elseif(isset($this->a[strtolower($player->getName())]["KingOfBlock"])){
		$minv->setItem(22, $cd->setCustomName("KingOfBlock"));
		}else{
		$kingofblock = Item::get(Item::DIAMOND_BLOCK);
		$kingofblock->setLore(["§d§l§6Khi Sử Dụng Giúp Biến Tất Cả Những Quặng Mine Được Thành Khối\n§7Thời Gian Tác Dụng: 60 Giây\n§8Thời Gian Hồi Chiêu: 900 Giây Tương Đương 15 Phút"]);
		$kingofblock->setNamedTagEntry(new StringTag("skillminer", "kingofblock"));
		$kingofblock->setCustomName("KingOfBlock");
		$minv->setItem(22, $kingofblock);
		}
		}
		
		$minv->setItem(23, $no->setCustomName("PickaxeLeveling"));
		if($player->hasPermission("skillminer.pickaxeleveling")){
		if(isset($this->a[strtolower($player->getName())]["cooldown"])){
		$minv->setItem(23, $al->setCustomName("PickaxeLeveling"));
		}elseif(isset($this->a[strtolower($player->getName())]["PickaxeLeveling"])){
		$minv->setItem(23, $cd->setCustomName("PickaxeLeveling"));
		}else{
		$pickaxeleveling = Item::get(Item::EXPERIENCE_BOTTLE);
		$pickaxeleveling->setLore(["§d§l§6Khi Sử Dụng Giúp X5 EXP Khi Mine Bằng Cúp Level\n§7Thời Gian Tác Dụng: 60 Giây\n§8Thời Gian Hồi Chiêu: 900 Giây Tương Đương 15 Phút"]);
		$pickaxeleveling->setNamedTagEntry(new StringTag("skillminer", "pickaxeleveling"));
		$pickaxeleveling->setCustomName("PickaxeLeveling");
		$minv->setItem(23, $pickaxeleveling);
		}
		}
		
		$minv->setItem(30, $no->setCustomName("RichDreamer"));
		if($player->hasPermission("skillminer.richdreamer")){
		if(isset($this->a[strtolower($player->getName())]["cooldown"])){
		$minv->setItem(30, $al->setCustomName("RichDreamer"));
		}elseif(isset($this->a[strtolower($player->getName())]["RichDreamer"])){
		$minv->setItem(30, $cd->setCustomName("RichDreamer"));
		}else{
		$richdreamer = Item::get(Item::EMERALD_BLOCK);
		$richdreamer->setLore(["§d§l§6Khi Sử Dụng Giúp X3 Giá Sell\n§7Thời Gian Tác Dụng: 60 Giây\n§8Thời Gian Hồi Chiêu: 1200 Giây Tương Đương 20 Phút"]);
		$richdreamer->setNamedTagEntry(new StringTag("skillminer", "richdreamer"));
		$richdreamer->setCustomName("RichDreamer");
		$minv->setItem(30, $richdreamer);
		}
		}
		
		$minv->setItem(31, $no->setCustomName("RebirthMiner"));
		if($player->hasPermission("skillminer.rebirthminer")){
		if(isset($this->a[strtolower($player->getName())]["cooldown"])){
		$minv->setItem(31, $al->setCustomName("RebirthMiner"));
		}elseif(isset($this->a[strtolower($player->getName())]["RebirthMiner"])){
		$minv->setItem(31, $cd->setCustomName("RebirthMiner"));
		}else{
		$rebirthminer = Item::get(Item::SLIME_BALL);
		$rebirthminer->setLore(["§d§l§6Khi Sử Dụng Giúp X5 Số Tiền Kiếm Được Từ Chuyển Sinh\n§7Thời Gian Tác Dụng: 60 Giây\n§8Thời Gian Hồi Chiêu: 900 Giây Tương Đương 15 Phút"]);
		$rebirthminer->setNamedTagEntry(new StringTag("skillminer", "rebirthminer"));
		$rebirthminer->setCustomName("RebirthMiner");
		$minv->setItem(31, $rebirthminer);
		}
		}
		
		$minv->setItem(32, $no->setCustomName("Eternity"));
		if($player->hasPermission("skillminer.eternity")){
		if(isset($this->a[strtolower($player->getName())]["cooldown"])){
		$minv->setItem(32, $al->setCustomName("Eternity"));
		}elseif(isset($this->a[strtolower($player->getName())]["Eternity"])){
		$minv->setItem(32, $cd->setCustomName("Eternity"));
		}else{
		$eternity = Item::get(Item::DRAGON_EGG);
		$eternity->setLore(["§d§l§6Khi Sử Dụng Giúp Đào Không Mất Block\n§7Thời Gian Tác Dụng: 60 Giây\n§8Thời Gian Hồi Chiêu: 600 Giây Tương Đương 10 Phút"]);
		$eternity->setNamedTagEntry(new StringTag("skillminer", "eternity"));
		$eternity->setCustomName("Eternity");
		$minv->setItem(32, $eternity);
		}
		}
		$menu->send($player);
		$menu->setListener([new SkillListener($this),"onTransaction"]);
	}
	public function FastMine($player){
	    $this->playca = $player->getName();
	    $this->seconds = 60;
	    $this->newCooldown($player,120,"FastMine",60);
	$this->time = $this->getScheduler()->scheduleRepeatingTask(new ClosureTask(function($_) : void{
            if(--$this->seconds === 0){
                if($player2 = $this->getServer()->getPlayer($this->playca)){
                $player2->sendMessage("Đã Hết Thời Gian Hiệu Lực Kĩ Năng FastMine. Bạn Có Thể Sử Dụng Kĩ Năng Khác Trong Thời Gian Hồi Chiêu");
                }
                $this->getScheduler()->cancelTask($this->time->getTaskId());
            }else{
    if($player2 = $this->getServer()->getPlayer($this->playca)){
    $effect4 = Effect::getEffect(3);
            $player2->addEffect(new EffectInstance($effect4, 30, 5, true));
}
            }
	}), 20);
	}
	public function KingOfBlock($player){
	    $this->playca = $player->getName();
	    $this->seconds = 60;
	    $this->kingofblock[strtolower($this->playca)]= "on";
	    $this->newCooldown($player,900,"KingOfBlock",60);
	$this->time = $this->getScheduler()->scheduleRepeatingTask(new ClosureTask(function($_) : void{
            if(--$this->seconds === 0){
                if($player2 = $this->getServer()->getPlayer($this->playca)){
                $player2->sendMessage("Đã Hết Thời Gian Hiệu Lực Kĩ Năng KingOfBlock. Bạn Có Thể Sử Dụng Kĩ Năng Khác Trong Thời Gian Hồi Chiêu");
                }
                $this->kingofblock[strtolower($this->playca)]= "off";
                $this->getScheduler()->cancelTask($this->time->getTaskId());
            }
	}), 20);
	}
	public function PickaxeLeveling($player){
	    $this->playca = $player->getName();
	    $this->seconds = 60;
	    $this->pickaxeleveling[strtolower($this->playca)]= "on";
	    $this->newCooldown($player,900,"PickaxeLeveling",60);
	$this->time = $this->getScheduler()->scheduleRepeatingTask(new ClosureTask(function($_) : void{
            if(--$this->seconds === 0){
                if($player2 = $this->getServer()->getPlayer($this->playca)){
                $player2->sendMessage("Đã Hết Thời Gian Hiệu Lực Kĩ Năng PickaxeLeveling. Bạn Có Thể Sử Dụng Kĩ Năng Khác Trong Thời Gian Hồi Chiêu");
                }
                $this->pickaxeleveling[strtolower($this->playca)]= "off";
                $this->getScheduler()->cancelTask($this->time->getTaskId());
            }
	}), 20);
	}
	public function RichDreamer($player){
	    $this->playca = $player->getName();
	    $this->seconds = 60;
	    $this->richdreamer[strtolower($this->playca)]= "on";
	    $this->newCooldown($player,1200,"RichDreamer",60);
	$this->time = $this->getScheduler()->scheduleRepeatingTask(new ClosureTask(function($_) : void{
            if(--$this->seconds === 0){
                if($player2 = $this->getServer()->getPlayer($this->playca)){
                $player2->sendMessage("Đã Hết Thời Gian Hiệu Lực Kĩ Năng RichDreamer. Bạn Có Thể Sử Dụng Kĩ Năng Khác Trong Thời Gian Hồi Chiêu");
                }
                $this->richdreamer[strtolower($this->playca)]= "off";
                $this->getScheduler()->cancelTask($this->time->getTaskId());
            }
	}), 20);
	}
	public function RebirthMiner($player){
	    $this->playca = $player->getName();
	    $this->seconds = 60;
	    $this->rebirthminer[strtolower($this->playca)]= "on";
	    $this->newCooldown($player,900,"RebirthMiner",60);
	$this->time = $this->getScheduler()->scheduleRepeatingTask(new ClosureTask(function($_) : void{
            if(--$this->seconds === 0){
                if($player2 = $this->getServer()->getPlayer($this->playca)){
                $player2->sendMessage("Đã Hết Thời Gian Hiệu Lực Kĩ Năng RebirthMiner. Bạn Có Thể Sử Dụng Kĩ Năng Khác Trong Thời Gian Hồi Chiêu");
                }
                $this->rebirthminer[strtolower($this->playca)]= "off";
                $this->getScheduler()->cancelTask($this->time->getTaskId());
            }
	}), 20);
	}
	public function Eternity($player){
	    $this->playca = $player->getName();
	    $this->seconds = 60;
	    $this->eternity[strtolower($this->playca)]= "on";
	    $this->newCooldown($player,600,"Eternity",60);
	$this->time = $this->getScheduler()->scheduleRepeatingTask(new ClosureTask(function($_) : void{
            if(--$this->seconds === 0){
                if($player2 = $this->getServer()->getPlayer($this->playca)){
                $player2->sendMessage("Đã Hết Thời Gian Hiệu Lực Kĩ Năng Eternity. Bạn Có Thể Sử Dụng Kĩ Năng Khác Trong Thời Gian Hồi Chiêu");
                }
                $this->eternity[strtolower($this->playca)]= "off";
                $this->getScheduler()->cancelTask($this->time->getTaskId());
            }
	}), 20);
	}
	public function handleBlockBreak(BlockBreakEvent $event) : void {
		$player = $event->getPlayer();
		if ($this->eternity[strtolower($player->getName())] == "on"){
			foreach($event->getDrops() as $drop){
				if($player->getInventory()->canAddItem($drop)){
				$player->getInventory()->addItem($drop);
			}else{
				if ($this->autosell->get()->mode[$player->getName()] == "on"){
				$this->getServer()->dispatchCommand($player, "sell all");
				$player->sendMessage("§l§b[§aMine§bTown§b]§a Tự động bán đồ thành công!");
				}
				}
				}
		$event->setDrops([]);
		$event->setCancelled();
	}
	}
}