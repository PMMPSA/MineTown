<?php

namespace HuongDan;

use pocketmine\plugin\PluginBase;

use pocketmine\event\Listener;

use pocketmine\Player;
use pocketmine\Server;

use pocketmine\command\CommandSender;
use pocketmine\command\Command;

use pocketmine\utils\TextFormat as C;

use HuongDan\Main;

class Main extends PluginBase implements Listener {

	public function onEnable(){
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
	}
	
	public function onCommand(CommandSender $player, Command $command, string $label, array $args) : bool {
		switch($command->getName()){
			case "huongdan":
			if($player instanceof Player){
			    $this->OpenMenu($player);
			} else {
				$player->sendMessage("§aLệnh này chỉ có thể sử dụng trong trò chơi");
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
				case 0:
				break;
			}
		}); 
		$form->setTitle("§l§a♦ §6Hướng dẫn §a♦");
		$form->setContent("§l§e♦ §fVOTE: §bbit.do/minetown1 §e♦\n§l§c• §a/§esbui §d→§f Mở Menu Skyblock\n§l§c• §a/§emuaec §d→§f Mở Menu Mua Enchant\n§l§c• §a/§enapkimcuong §d→§f Nạp kim cương\n§l§c• §a/§evote §d→§f Nhận qua sau khi Vote cho Server\n§l§c• §a/§emycode §d→§f Nhận quà trong Giftcode\n§l§c• §a/§emuafly §d→§f Mua bay khi đang ở trong Server, out là mất\n§l§c• §a/§emuakey §d→§f Mua Key Premium quay Crate");
		$form->addButton("§l§e• §cĐóng §l§e•");
		$form->sendToPlayer($sender);
			return $form;
	}
}