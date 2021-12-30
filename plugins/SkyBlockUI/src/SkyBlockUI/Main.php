<?php

namespace SkyBlockUI;

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
        #Plugin Code By Nguyen Dong Quy, subscribe me on "NDOZ PMMP"
    }
    public function onCommand(CommandSender $sender, Command $cmd, string $label, array $args):bool{
        switch($cmd->getName()){
        case "sbui":
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
					$command = "sb auto";
					             $this->getServer()->getCommandMap()->dispatch($sender, $command);
					             $sender->sendPopup("§a§l• §cDịch chuyển đến đảo trống thành công§a •");
                        break;
                    case 2:
                    $command = "sb claim";
								$this->getServer()->getCommandMap()->dispatch($sender, $command);
								$sender->sendPopup("§a§l• §cMua Đảo thành công§a •");
                        break;
                    case 3:
                   $this->AddHelperForm($sender);
						break;
                    case 4:
                    $this->RemoveHelperForm($sender);
                        break;
                    case 5:
                    $command = "sb homes";
								$this->getServer()->getCommandMap()->dispatch($sender, $command);
                        break;
                    case 6:
                    $command = "sb home 1";
								$this->getServer()->getCommandMap()->dispatch($sender, $command);
                        break;
					case 7:
                   $this->TeleportForm($sender);
						break;
            }
        });
        $form->setTitle("§l§eᴍɪɴᴇ§bᴛᴏᴡɴ§l§c");
        $form->setContent("§f§l• Vui lòng chọn một mục để tiến hành §6chuyển hướng");
        $form->addButton("§c§l• Thoát •");
        $form->addButton("§b§l• Tìm Đảo •\n§r§e• Tiến hành tìm đảo trống");
        $form->addButton("§b§l• Mua Đảo •\n§r§e• Mua đảo bạn đang đứng");
        $form->addButton("§b§l• Thêm Người •\n§r§e• Thêm người chơi phụ xây đảo");
        $form->addButton("§b§l• Xóa Người •\n§r§e• Xóa người chơi phụ xây đảo");
        $form->addButton("§b§l• Danh Sách Đảo •\n§r§e• Xem danh sách đảo của bạn");
        $form->addButton("§b§l• Về Đảo •\n§r§e• Về nhanh đảo của bạn");
        $form->addButton("§b§l• Dịch Chuyển •\n§r§e• Dịch chuyển đến đảo khác");
        $form->sendToPlayer($sender);
        }
return true;
}
public function TeleportForm(Player $player){ 
		$api = $this->getServer()->getPluginManager()->getPlugin("FormAPI");
			$form = $api->createCustomForm(function (Player $player,$data){
				$result = $data;
				if($result === null){
					$this->getServer()->dispatchCommand($player, "sbui");
					return false;
				}
          //if{
					if(empty($data[1])){
						$this->TeleportForm($player,"§cKhông được bỏ trống ID đảo\n");		
					    return false;
					}
					$this->getServer()->dispatchCommand($player, "sb warp ".$data[1]);
					return false;			
				});	
				$form->setTitle("§l§eᴍɪɴᴇ§bᴛᴏᴡɴ");	
				$form->addLabel("§f§l• Vui lòng nhập ID đảo để tiến hành dịch chuyển");
				$form->addInput("§b§l• Nhập ID Đảo:","VD: 1;0");
				$form->sendToPlayer($player);
	   }
public function AddHelperForm(Player $player){ 
		$api = $this->getServer()->getPluginManager()->getPlugin("FormAPI");
			$form = $api->createCustomForm(function (Player $player,$data){
				$result = $data;
				if($result === null){
					$this->getServer()->dispatchCommand($player, "sbui");
					return false;
				}
					if(empty($data[1])){
						$this->TeleportForm($player,"§c§l• Không được bỏ trống tên người chơi\n");		
					    return false;
					}
					$this->getServer()->dispatchCommand($player, "sb addhelper ".$data[1]);
					return false;			
				});	
				$form->setTitle("§l§eᴍɪɴᴇ§bᴛᴏᴡɴ");
				$form->addLabel("§f§l• Vui lòng nhập tên người trợ giúp để thêm");
				$form->addInput("§b§l• Nhập Tên Người Chơi:","VD: TuiDepTraii");
				$form->sendToPlayer($player);
	   }
public function RemoveHelperForm(Player $player){ 
		$api = $this->getServer()->getPluginManager()->getPlugin("FormAPI");
			$form = $api->createCustomForm(function (Player $player,$data){
				$result = $data;
				if($result === null){
					$this->getServer()->dispatchCommand($player, "sbui");
					return false;
				}
					if(empty($data[1])){
						$this->TeleportForm($player,"§cKhông được bỏ trống tên người chơi\n");		
					    return false;
					}
					$this->getServer()->dispatchCommand($player, "sb removehelper ".$data[1]);
					return false;		
				});	
				$form->setTitle("§l§eᴍɪɴᴇ§bᴛᴏᴡɴ");	
				$form->addLabel("§f§l• Vui lòng nhập tên người trợ giúp để xoá");
				$form->addInput("§b§l• Nhập Tên Người Chơi:","VD: TuiDepTraii");
				$form->sendToPlayer($player);
	   }
    }