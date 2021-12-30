<?php

namespace MineTownVIP;

use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\Player;
use pocketmine\Server;
use pocketmine\command\ConsoleCommandSender;
use pocketmine\command\CommandSender;
use pocketmine\command\Command;
use pocketmine\utils\TextFormat as C;
use MineTownVIP\Main;

class Main extends PluginBase implements Listener {

	public function onEnable(){
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
		$this->pointapi = $this->getServer()->getPluginManager()->getPlugin("PointAPI");
		
		@mkdir($this->getDataFolder());
		$this->saveDefaultConfig();
		$this->getResource("config.yml");
	}
	public function onCommand(CommandSender $player, Command $command, string $label, array $args) : bool {
		switch($command->getName()){
			case "muavip":
			if($player instanceof Player){
			    $this->openMyForm($player);
			} else {
				$player->sendMessage("You can use this command only in-game.");
					return true;
			}
			break;
		}
	    return true;
	}

	public function openMyForm(Player $sender){
		$formapi = $this->getServer()->getPluginManager()->getPlugin("FormAPI");
		$form = $formapi->createSimpleForm(function (Player $sender, ?int $data = null){
		$result = $data;
		if($result === null){
			return;
		    }
			switch($result){
				case 0;
			    $this->VIP($sender);
					break;
				case 1;
				$this->VIPs($sender);
					break;
			}
		});
		$point = $this->pointapi->myPoint($sender);
		$form->setTitle("§l§a♣ §6MineTown§a ♣");
				$form->setContent("§l§b•§a Mcoin của bạn: §f$point");
		$form->addButton("§l§dGói §bVIP §cGiá: §f100 Mcoin §cSố ngày: §fVĩnh Viễn");
	    $form->addButton("§l§dGói §bVIP§6s §cGiá: §f800 Mcoin §cSố ngày: §fVĩnh Viễn");
		$form->sendToPlayer($sender);
			return $form;
	} # Gói VIP
	public function VIP($sender){
        $api = $this->getServer()->getPluginManager()->getPlugin("FormAPI");
        $form = $api->createModalForm(function (Player $sender, $data){
        $result = $data;
        if ($result == null) {
             }
             switch ($result) {
                 case 1:
                 $point = $this->pointapi->myPoint($sender);
                 $name = $sender->getName();
                 $package = $this->getConfig()->get("VIP.Package");
                 $cost = $this->getConfig()->get("VIP.Cost");
                 if($point >= $cost){
            
                 $this->pointapi->reducePoint($sender, $cost);	
                 $this->getServer()->dispatchCommand(new ConsoleCommandSender(), "setgroup ".$name." VIP");
                 $this->getServer()->broadcastMessage("§l§cNgười chơi §a".$name." §cđã mua thành công §e".$package);
		         $this->VipBuySuccess($sender);
              return true;
            }else{
                $this->VipBuyNoPoint($sender);
            }
                        break;
                    case 2:
                        break;
            }
        });
        $package = $this->getConfig()->get("VIP.Package");
        $cost = $this->getConfig()->get("VIP.Cost");
		$form->setTitle("§l§d♦ §bXác Nhận Mua ".$package." §d♦");
        $form->setContent("§l§bBạn có muốn mua §d".$package." §f(§a".$cost." §cMcoin§f) = §f(§6Vĩnh Viễn§f)?");
        $form->setButton1($this->getConfig()->get("Vip.Buy.Button.Confirm"), 1);
        $form->setButton2($this->getConfig()->get("Vip.Buy.Button.NoConfirm"), 2);
        $form->sendToPlayer($sender);
     } # Gói VIPs
	public function VIPs($sender){
        $api = $this->getServer()->getPluginManager()->getPlugin("FormAPI");
        $form = $api->createModalForm(function (Player $sender, $data){
        $result = $data;
        if ($result == null) {
             }
             switch ($result) {
                 case 1:
                 $point = $this->pointapi->myPoint($sender);
                 $name = $sender->getName();
                 $package = $this->getConfig()->get("VIPs.Package");
                 $cost = $this->getConfig()->get("VIPs.Cost");
                 if($point >= $cost){
            
                 $this->pointapi->reducePoint($sender, $cost);	
                 $this->getServer()->dispatchCommand(new ConsoleCommandSender(), "setgroup ".$name." VIPs");
                 $this->getServer()->broadcastMessage("§l§cNgười chơi §a".$name." §cđã mua thành công §e".$package);
		         $this->VipBuySuccess($sender);
              return true;
            }else{
                $this->VipBuyNoPoint($sender);
            }
                        break;
                    case 2:
                        break;
            }
        });
        $package = $this->getConfig()->get("VIPs.Package");
        $cost = $this->getConfig()->get("VIPs.Cost");
		$form->setTitle("§l§d♦ §bXác Nhận Mua ".$package." §d♦");
        $form->setContent("§l§bBạn có muốn mua §d".$package." §f(§a".$cost." §cMcoin§f) = §f(§6Vĩnh Viễn§f)?");
        $form->setButton1($this->getConfig()->get("Vip.Buy.Button.Confirm"), 1);
        $form->setButton2($this->getConfig()->get("Vip.Buy.Button.NoConfirm"), 2);
        $form->sendToPlayer($sender);
     }
	public function VipBuySuccess($sender){
		$api = $this->getServer()->getPluginManager()->getPlugin("FormAPI");
		$form = $api->createSimpleForm(function (Player $sender, $data){
			$result = $data;
			if ($result == null) {
			}
			switch ($result) {
					case 1:
						break;
			}
		});
	$form->setTitle($this->getConfig()->get("Vip.Buy.Success.Title"));
	$form->setContent($this->getConfig()->get("Vip.Buy.Success.Content"));
	$form->addButton($this->getConfig()->get("Vip.Buy.Success.Submit"));
	$form->sendToPlayer($sender);
	}
	public function translateMessage($scut, $message) {
	$message = str_replace($scut."{name}", $sender->getName(), $message);
		return $message;
     } # UI Vip No Point
	public function VipBuyNoPoint($sender){
		$api = $this->getServer()->getPluginManager()->getPlugin("FormAPI");
		$form = $api->createSimpleForm(function (Player $sender, $data){
			$result = $data;
			if ($result == null) {
			}
			switch ($result) {
					case 1:
						break;
			}
		});
	$form->setTitle($this->getConfig()->get("Vip.Buy.NoPoint.Title"));
	$form->setContent($this->getConfig()->get("Vip.Buy.NoPoint.Content"));
	$form->addButton($this->getConfig()->get("Vip.Buy.NoPoint.Submit"));
	$form->sendToPlayer($sender);
	}
	public function processor(Player $player, string $string): string{		$string = str_replace("{name}", $player->getName(), $string);
	return $string;
	}
}