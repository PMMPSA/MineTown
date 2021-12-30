<?php

namespace Mcoin;

use pocketmine\plugin\PluginBase;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use jojoe77777\FormAPI;
use pocketmine\Player;
use pocketmine\Server;
use pocketmine\event\Listener;

class Main extends PluginBase implements Listener {
    
    public function onEnable(){
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
    }
    public function onCommand(CommandSender $sender, Command $cmd, string $label, array $args):bool{
        switch($cmd->getName()){
        case "mcoinui":
        if(!($sender instanceof Player)){
                $sender->sendMessage("§cVui lòng dùng lệnh trong Game");
                return true;
        }
        $api = $this->getServer()->getPluginManager()->getPlugin("FormAPI");
        $form = $api->createSimpleForm(function (Player $sender, $data){
            $result = $data;
            if ($result === null) {
            }
            switch ($result) {
            case 0:
            break;
                    case 1:
                    $this->PayMoneyForm($sender);
                        break;
                    case 2:
                    $this->TopMoneyForm($sender);
                        break;
                    case 3:
					$command = "muavip";
					             $this->getServer()->getCommandMap()->dispatch($sender, $command);
                        break;
					case 4:
                    $this->MuaKitForm($sender);
						break;
            }
        });
		$mcoin = $this->getServer()->getPluginManager()->getPlugin("PointAPI")->myPoint($sender);
        $form->setTitle("§l§a♣ §6MineTown§a ♣");
        $form->setContent("§l§b•§a Mcoin của bạn: §f$mcoin");
        $form->addButton("§l§6•§d Thoát §6•");
        $form->addButton("§l§6•§d Chuyển Mcoin §6•");
		$form->addButton("§l§6•§d TOP Mcoin §6•");
        $form->addButton("§l§6•§d Mua VIP §6•");
        $form->addButton("§l§6•§d Mua KIT §6•");
        $form->sendToPlayer($sender);
        }
return true;
}
public function PayMoneyForm(Player $player){ 
		$api = $this->getServer()->getPluginManager()->getPlugin("FormAPI");
			$form = $api->createCustomForm(function (Player $player,$data){
				$result = $data;
				if($result === null){
					$this->getServer()->dispatchCommand($player, "mcoinui");
					return false;
				}
          //if{
					if(empty($data[1])){
						$this->PayMoneyForm($player,"§cKhông được bỏ trống tên người chơi\n");		
					    return false;
					}
					if(empty($data[2])){
						$this->PayMoneyForm($player,"§cKhông được bỏ trống số tiền\n");		
					    return false;
					}
					$this->getServer()->dispatchCommand($player, "solomotooto2 ".$data[1]." ".$data[2]);
					return false;			
				});
				$mcoin1 = $this->getServer()->getPluginManager()->getPlugin("PointAPI")->myPoint($player);
				$form->setTitle("§l§a♣ §6MineTown§a ♣");	
				$form->addLabel("§l§d•§a Mcoin của bạn: §a$mcoin1");
				$form->addInput("§l§bNhập Tên Người:","VD: TuiDepTraii");
				$form->addInput("§l§bNhập Số Mcoin Cần Chuyển:","VD: 1");
				$form->sendToPlayer($player);
	   }
public function TopMoneyForm(Player $player){ 
		$api = $this->getServer()->getPluginManager()->getPlugin("FormAPI");
			$form = $api->createCustomForm(function (Player $player,$data){
				$result = $data;
				if($result === null){
					$this->getServer()->dispatchCommand($player, "mcoinui");
					return false;
				}
          //if{
					if(empty($data[1])){
						$this->TopMoneyForm($player,"§cKhông được bỏ trống số trang\n");		
					    return false;
					}
					$this->getServer()->dispatchCommand($player, "solomotooto6 ".$data[1]);
					return false;			
				});
				$mcoin2 = $this->getServer()->getPluginManager()->getPlugin("PointAPI")->myPoint($player);
				$form->setTitle("§l§a♣ §6MineTown§a ♣");	
				$form->addLabel("§l§b•§a Mcoin của bạn: §f$mcoin2");
				$form->addInput("§l§aNhập Số Trang:","VD: 1");
				$form->sendToPlayer($player);
	   }
public function MuaKitForm(Player $player){ 
		$api = $this->getServer()->getPluginManager()->getPlugin("FormAPI");
			$form = $api->createCustomForm(function (Player $player,$data){
				$result = $data;
				if($result === null){
					$this->getServer()->dispatchCommand($player, "mcoinui");
					return false;
				}
          //if{
					if(empty($data[1])){
						$this->MuaKitForm($player,"§cV\n");		
					    return false;
					}
					$this->getServer()->dispatchCommand($player, "muakit ".$data[1]);
					return false;			
				});	
				$form->setTitle("§l§a♣ §6MineTown§a ♣");
				$form->addLabel("§lSắp Ra Mắt!");
				$form->sendToPlayer($player);
	   }
    }