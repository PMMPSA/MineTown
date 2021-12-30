<?php

namespace MoneyUI;

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
        case "money":
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
					$command = "mymoney";
					             $this->getServer()->getCommandMap()->dispatch($sender, $command);
                        break;
                    case 2:
                    $this->PayMoneyForm($sender);
                        break;
                    case 3:
                    $this->GiveMoneyForm($sender);
						break;
                    case 4:
                    $this->TakeMoneyForm($sender);
                        break;
                    case 5:
                    $this->SetMoneyForm($sender);
                        break;
                    case 6:
                    $this->SeeMoneyForm($sender);
                        break;
					case 7:
					$command = "topmoney";
					             $this->getServer()->getCommandMap()->dispatch($sender, $command);
            }
        });
        $form->setTitle("§l§a♣ §6Money§bUI§a ♣");
        $form->setContent("§lVui lòng chọn một ô để thực hiện lệnh");
        $form->addButton("§cTHOÁT!");
        $form->addButton("§l§6Số Tiền Của Bạn\n§r§aXem số tiền bạn có");
        $form->addButton("§l§6Chuyển Tiền\n§r§aChuyển tiền cho người khác");
        $form->addButton("§l§6Đưa Tiền\n§r§aĐưa tiền cho người khác");
        $form->addButton("§l§6Lấy Tiền\n§r§aLấy tiền của người khác");
        $form->addButton("§l§6Đặt Tiền\n§r§aĐặt tiền cho người khác");
        $form->addButton("§l§6Xem Tiền\n§r§aXem số tiền của người nào đó");
        $form->addButton("§l§6Top Money\n§r§aNhững người giàu nhất Server");
        $form->sendToPlayer($sender);
        }
return true;
}
public function PayMoneyForm(Player $player){ 
		$api = $this->getServer()->getPluginManager()->getPlugin("FormAPI");
			$form = $api->createCustomForm(function (Player $player,$data){
				$result = $data;
				if($result === null){
					$this->getServer()->dispatchCommand($player, "money");
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
					$this->getServer()->dispatchCommand($player, "pay ".$data[1]." ".$data[2]);
					return false;			
				});	
				$form->setTitle("§l§a♣ §6Money§bUI§a ♣");	
				$form->addLabel("§lVui lòng nhập tên người và số tiền cần chuyển");
				$form->addInput("§l§2Nhập Tên Người:","VD: TuiDepTraii");
				$form->addInput("§l§2Nhập Số Tiền Cần Chuyển:","VD: 100000");
				$form->sendToPlayer($player);
	   }
public function GiveMoneyForm(Player $player){ 
		$api = $this->getServer()->getPluginManager()->getPlugin("FormAPI");
			$form = $api->createCustomForm(function (Player $player,$data){
				$result = $data;
				if($result === null){
					$this->getServer()->dispatchCommand($player, "money");
					return false;
				}
					if(empty($data[1])){
						$this->GiveMoneyForm($player,"§cKhông được bỏ trống tên người chơi\n");		
					    return false;
					}
					if(empty($data[2])){
						$this->GiveMoneyForm($player,"§cKhông được bỏ trống số tiền\n");		
					    return false;
					}
					$this->getServer()->dispatchCommand($player, "givemoney ".$data[1]." ".$data[2]);
					return false;			
				});	
				$form->setTitle("§l§a♣ §6Money§bUI§a ♣");
				$form->addLabel("§lVui lòng nhập tên người và số tiền cần đưa");
				$form->addInput("§l§2Nhập Tên Người:","VD: TuiDepTraii");
				$form->addInput("§l§2Nhập Số Tiền Cần Đưa:","VD: 100000");
				$form->sendToPlayer($player);
	   }
public function TakeMoneyForm(Player $player){ 
		$api = $this->getServer()->getPluginManager()->getPlugin("FormAPI");
			$form = $api->createCustomForm(function (Player $player,$data){
				$result = $data;
				if($result === null){
					$this->getServer()->dispatchCommand($player, "money");
					return false;
				}
					if(empty($data[1])){
						$this->TakeMoneyForm($player,"§cKhông được bỏ trống tên người chơi\n");		
					    return false;
					}
					if(empty($data[2])){
						$this->TakeMoneyForm($player,"§cKhông được bỏ trống số tiền\n");		
					    return false;
					}
					$this->getServer()->dispatchCommand($player, "takemoney ".$data[1]." ".$data[2]);
					return false;		
				});	
				$form->setTitle("§l§a♣ §6Money§bUI§a ♣");	
				$form->addLabel("§lVui lòng nhập tên người và số tiền cần lấy");
				$form->addInput("§l§2Nhập Tên Người:","VD: TuiDepTraii");
				$form->addInput("§l§2Nhập Số Tiền Cần Lấy:","VD: 100000");
				$form->sendToPlayer($player);
	   }
public function SetMoneyForm(Player $player){ 
		$api = $this->getServer()->getPluginManager()->getPlugin("FormAPI");
			$form = $api->createCustomForm(function (Player $player,$data){
				$result = $data;
				if($result === null){
					$this->getServer()->dispatchCommand($player, "money");
					return false;
				}
          //if{
					if(empty($data[1])){
						$this->SetMoneyForm($player,"§cKhông được bỏ trống tên người chơi\n");		
					    return false;
					}
					if(empty($data[2])){
						$this->SetMoneyForm($player,"§cKhông được bỏ trống số tiền\n");		
					    return false;
					}
					$this->getServer()->dispatchCommand($player, "setmoney ".$data[1]." ".$data[2]);
					return false;			
				});	
				$form->setTitle("§l§a♣ §6Money§bUI§a ♣");	
				$form->addLabel("§lVui lòng nhập tên người và số tiền cần đặt");
				$form->addInput("§l§2Nhập Tên Người:","VD: TuiDepTraii");
				$form->addInput("§l§2Nhập Số Tiền Cần Đặt:","VD: 100000");
				$form->sendToPlayer($player);
	   }
public function SeeMoneyForm(Player $player){ 
		$api = $this->getServer()->getPluginManager()->getPlugin("FormAPI");
			$form = $api->createCustomForm(function (Player $player,$data){
				$result = $data;
				if($result === null){
					$this->getServer()->dispatchCommand($player, "money");
					return false;
				}
          //if{
					if(empty($data[1])){
						$this->PayMoneyForm($player,"§cKhông được bỏ trống tên người chơi\n");		
					    return false;
					}
					$this->getServer()->dispatchCommand($player, "seemoney ".$data[1]);
					return false;			
				});	
				$form->setTitle("§l§a♣ §6Money§bUI§a ♣");	
				$form->addLabel("§lVui lòng nhập tên người cần xem số tiền của họ");
				$form->addInput("§l§2Nhập Tên Người:","VD: TuiDepTraii");
				$form->sendToPlayer($player);
	   }
    }