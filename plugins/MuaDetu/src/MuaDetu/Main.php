<?php

namespace MuaDetu;

use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\Player;
use pocketmine\Server;
use pocketmine\command\ConsoleCommandSender;
use pocketmine\command\CommandSender;
use pocketmine\command\Command;
use pocketmine\utils\TextFormat as C;
use MuaDetu\Main;

class Main extends PluginBase implements Listener {

	public function onEnable(){
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
		$this->rebirthcoinapi = $this->getServer()->getPluginManager()->getPlugin("RebirthCoinAPI");
	}
	
	public function onCommand(CommandSender $player, Command $command, string $label, array $args) : bool {
		switch($command->getName()){
			case "muadetu":
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
			    $this->MuaDeTu($sender);
					break;
			}
		});
		$rb = $this->rebirthcoinapi->myRebirthCoin($sender);
		$form->setTitle("§l§b♦ §cMINETOWN MUA ĐỆ TỬ §b♦");
		$form->setContent("§e• §cRebirthCoin của bạn: §e$".$rb);
		$form->addButton("§e• §cMua Đệ Tử §e•");
		$form->sendToPlayer($sender);
			return $form;
	}
	
	public function MuaDeTu($sender){
        $api = $this->getServer()->getPluginManager()->getPlugin("FormAPI");
        $form = $api->createModalForm(function (Player $sender, $data){
        $result = $data;
        if ($result == null) {
             }
             switch ($result) {
                 case 1:
                 $rb = $this->rebirthcoinapi->myRebirthCoin($sender);
                 $name = $sender->getName();
                 $cost = 100;
                 if($rb >= $cost){
            
                 $this->rebirthcoinapi->reduceRebirthCoin($sender, $cost);	
                 $this->getServer()->dispatchCommand(new ConsoleCommandSender(), "detu ".$name."");
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
        $cost = 100;
		$form->setTitle("§b♦ §cXác Nhận Mua Đệ Tử §b♦");
        $form->setContent("§aBạn có muốn mua Đệ Tử §f(§a".$cost." §cRebirthCoin§f)?");
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
	$form->setContent("§l§aBạn đã mua đệ tử thành công!");
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
	$form->setContent("§l§cBạn không đủ RebirthCoin để mua Đệ Tử!");
	$form->addButton("Submit");
	$form->sendToPlayer($sender);
	}
}