<?php

/* -----[NaptheUI]-----
* Updated Main UI System
* Author: BlackPMFury
* Current Plugin: NaptheUI/Phuongaz
* Version 3.0-SPECIALS
*/

namespace Napthe\SPNVN;

use pocketmine\plugin\PluginBase;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\ConsoleCommandSender;
use pocketmine\{Player, Server};
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\Listener;
use pocketmine\utils\Config;
use pocketmine\math\Vector3;
use jojoe7777\FormAPI;
use Napthe\SPNVN\Main;

class Main extends PluginBase implements Listener{
	public $tag = "§l§a♥ §e[§aMine§bTown§e] §a";
	public $config;
	
	public function onEnable(){
		$this->getServer()->getLogger()->info($this->tag . "§l§a Enable Plugin...");
		$this->dnt = new Config($this->getDataFolder(). "Donation.yml", Config::YAML);
		$this->pp = $this->getServer()->getPluginManager()->getPlugin("PurePerms");
		
		@mkdir($this->getDataFolder());
		$this->saveDefaultConfig();
		$this->getResource("Config.yml");
	}
	
	public function onJoin(PlayerJoinEvent $ev){
		$player = $ev->getPlayer();
		$name = $player->getName();
		if($player->isOp()){
			foreach($this->getServer()->getOnlinePlayers() as $dnt){
				if($this->dnt->exists($name)){
				    $dnt->sendMessage($this->tag . "§b Found a donater at Donation.yml -". $this->dnt->get($name));
				    return true;
				}
			}
			return true;
		}else{
			$player->sendPopup("§d/napthe§a Để ủng hộ Server nhé <3");
			return true;
		}
	}
	
	public function onLoad(): void{
		$this->getServer()->getLogger()->info("§l§b-=-=-=-=| ".$this->tag."§l§b |=-=-=-=-");
		$this->getServer()->getLogger()->notice($this->tag . "§l§a Code By BlackPMFury");
	}
	
	public function onCommand(CommandSender $sender, Command $cmd, string $label, array $args): bool{
		switch($cmd->getName()){
			case "napthe":
			if(!($sender instanceof Player)){
				$this->getServer()->getLogger()->info($this->tag . "§l§c You can not use this command In Here!");
				return true;
			}
			$api = $this->getServer()->getPluginManager()->getPlugin("FormAPI");
			$form = $api->createSimpleForm(Function (Player $sender, $data){
				
				$result = $data;
				if ($result == null){
				}
				switch ($result) {
					case 0:
					$sender->sendMessage("§l§a Thank For Donate!");
					break;
					case 1:
					$this->infoCard($sender);
					break;
					case 2:
					$this->napthe($sender);
					break;
				}
			});
			$mcoin = $this->getServer()->getPluginManager()->getPlugin("PointAPI")->myPoint($sender);
			$form->setTitle($this->getConfig()->get("plugin.title"));
			$form->setContent("§l§b•§a Mcoin của bạn: §f$mcoin");
			$form->addButton("§cThoát", 0);
			$form->addButton($this->getConfig()->get("Profile.title"), 1);
			$form->addButton($this->getConfig()->get("Donation.title"), 2);
			$form->sendToPlayer($sender);
		}
		return true;
	}
	
	/**public function thongTin($sender){
		$api = $this->getServer()->getPluginManager()->getPlugin("FormAPI");
		$form = $api->createCustomForm(Function (Player $sender, $data){
		});
		$form->setTitle($this->getConfig()->get("Profile.title"));
		$form->addLabel("§a Nạp Thẻ Giúp Bạn Mua Rank và Các Mặt Hàng Bằng SCoin.");
		$form->addLabel("§cNOTE:§e Trường Hợp Thẻ Sai sẽ Bị Xoá Thẻ (Nếu Cố Ý gửi Thẻ Sai)");
		$form->sendToPlayer($sender);
	}*/
	
	public function napthe($sender){
		$api = $this->getServer()->getPluginManager()->getPlugin("FormAPI");
		$form = $api->createCustomForm(Function (Player $sender, $data){
			switch($data[0]){
				case 0:
				$loaithe = "Mobiphone";
				break;
				case 1:
				$loaithe = "Vinaphone";
				break;
				case 2:
				$loaithe = "Viettel";
				break;
				case 3:
				$loaithe = "Zing";
				break;
			}
			switch($data[1]){
				case 0:
				$menhgia = "10000";
				break;
				case 1:
				$menhgia = "20000";
				break;
				case 2:
				$menhgia = "50000";
				break;
				case 3:
				$menhgia = "100000";
				break;
				case 4:
				$menhgia = "200000";
				break;
				case 5:
				$menhgia = "500000";
				break;
				case 6:
				$menhgia = "1000000";
				break;
			}
			if(!(is_numeric($data[2]) || is_numeric($data[3]))){
				$sender->sendMessage("§a§l Phải Là Số!");
				return true;
			}
			$this->getServer()->getLogger()->notice("Donate By ".$sender->getName().", Check In Donation.yml");
			$sender->sendMessage($this->tag . " §l§aSeri:§e ".$data[1].",§a Mã Thẻ: §e".$data[3]."\n§a Typer:§b ".$loaithe.", §aMệnh Giá: §e". $menhgia);
			$this->dnt->set( $sender->getName(), ["Typer" => $loaithe, "Mệnh Giá" => $menhgia, "Seri" => $data[2], "Mã Thẻ" => $data[3]]);
			$this->dnt->save();
		});
		$form->setTitle($this->getConfig()->get("Donation.title"));
		$form->addDropdown("§l§6•§a Loại Thẻ", ["Mobiphone", "Vinaphone", "Viettel", "Zing"]);
		$form->addDropdown("§l§6•§a Mệnh Giá", ["10000", "20000", "50000", "100000", "200000", "500000", "1000000"]);
		//$form->addInput("§aMệnh Giá:");
		$form->addInput("§aSeri:");
		$form->addInput("§aCode:");
		$form->sendToPlayer($sender);
		return true;
	}
	
	
	public function infoCard($sender){
		$name = $this->dnt->get($sender->getName());
		$type = $name["Type"];
		$cost = $name["Mệnh Giá"];
		$seri = $name["Seri"];
		$code = $name["Mã Thẻ"];
		$api = $this->getServer()->getPluginManager()->getPlugin("FormAPI");
		$form = $api->createCustomForm(Function (Player $sender, $data){
			/**$rank = $this->pp->getUserDataMgr()->getGroup($sender);
			if(is_null($data[6])){
				switch($data[6]){
					case 0:
					$sender->sendMessage($this->tag . "§l§a Thanks For Donation!");
					break;
					case 1:
					$this->dnt->remove($this->dnt->get($sender->getName()));
					break;
				}
				return true;
			}*/
		});
		$form->setTitle($this->getConfig()->get("Profile.title"));
		$form->addLabel("§l§6•§a Thẻ Đang Chờ Duyệt:");
		$form->addLabel("§c •§a Loại Thẻ:§e ". $type);
		$form->addLabel("§c •§a Mệnh Giá:§e ". $cost);
		$form->addLabel("§c •§a Mã Thẻ:§e ". $code);
		$form->addLabel("§c •§a Seri:§e ". $seri);
		//$form->addDropdown("Bạn có muốn xoá donation?", ["Don't Delete", "Delete it"]);
		$form->sendToPlayer($sender);
	}
	
	
}