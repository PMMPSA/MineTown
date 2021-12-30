<?php

namespace MuaKey;

use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\Player;
use pocketmine\Server;
use pocketmine\command\ConsoleCommandSender;
use pocketmine\command\CommandSender;
use pocketmine\command\Command;
use pocketmine\utils\TextFormat as C;
use MuaKey\Main;

class Main extends PluginBase implements Listener {

	public function onEnable(){
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
		$this->pointapi = $this->getServer()->getPluginManager()->getPlugin("PointAPI");
	}
	
	public function onCommand(CommandSender $player, Command $command, string $label, array $args) : bool {
		switch($command->getName()){
			case "muakey":
			if($player instanceof Player){
			    $this->OpenMenu($player);
			} else {
				$player->sendMessage("You can use this command only in-game.");
					return true;
			}
			break;
		}
	    return true;
	}

	public function OpenMenu(Player $sender){
		$formapi = $this->getServer()->getPluginManager()->getPlugin("FormAPI");
		$form = $formapi->createSimpleForm(function (Player $sender, ?int $data = null){
		$result = $data;
		if($result === null){
			return;
		    }
			switch($result){
				case 0;
			    $this->MuaKey($sender);
					break;
			}
		});
		$kc = $this->pointapi->myPoint($sender);
		$form->setTitle("§l§b♦ §cMINETOWN BUYKEY §b♦");
		$form->setContent("§e• §cKim Cương của bạn: §e$".$kc);
		$form->addButton("§e• §cMua Key Premium §e•");
		$form->sendToPlayer($sender);
			return $form;
	}
	
	public function MuaKey($sender){
        $api = $this->getServer()->getPluginManager()->getPlugin("FormAPI");
        $form = $api->createModalForm(function (Player $sender, $data){
        $result = $data;
        if ($result == null) {
             }
             switch ($result) {
                 case 1:
                 $kc = $this->pointapi->myPoint($sender);
                 $name = $sender->getName();
                 $cost = 10;
                 if($kc >= $cost){
            
                 $this->pointapi->reducePoint($sender, $cost);	
                 $this->getServer()->dispatchCommand(new ConsoleCommandSender(), "key premium ".$name."");
		         $this->MuaThanhCong($sender);
              return true;
            }else{
                $this->MuaThatBai($sender);
            }
                        break;
                    case 2:
                        break;
            }
        });
        $cost = 10;
		$form->setTitle("§b♦ §cXác Nhận Mua Key §b♦");
        $form->setContent("§aBạn có muốn mua Key Premium §f(§a".$cost." §cKim Cương§f)?");
        $form->setButton1("Có", 1);
        $form->setButton2("Không", 2);
        $form->sendToPlayer($sender);
     }
     
	public function MuaThanhCong($sender){
		$api = $this->getServer()->getPluginManager()->getPlugin("FormAPI");
		$form = $api->createSimpleForm(function (Player $sender, $data){
			$result = $data;
			if ($result == null) {
				$this->OpenMenu($sender);
			}
			switch ($result) {
					case 1:
						break;
			}
		});
	$form->setTitle("");
	$form->setContent("§l§aBạn đã mua Key thành công!");
	$form->addButton("Submit");
	$form->sendToPlayer($sender);
	}
	
	public function MuaThatBai($sender){
		$api = $this->getServer()->getPluginManager()->getPlugin("FormAPI");
		$form = $api->createSimpleForm(function (Player $sender, $data){
			$result = $data;
			if ($result == null) {
				$this->OpenMenu($sender);
			}
			switch ($result) {
					case 1:
						break;
			}
		});
	$form->setTitle("");
	$form->setContent("§l§cBạn không đủ Kim Cương để mua Key Premium!");
	$form->addButton("Submit");
	$form->sendToPlayer($sender);
	}
}