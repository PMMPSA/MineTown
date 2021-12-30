<?php

namespace IdDao;

use pocketmine\Player;
use pocketmine\event\Listener;
use pocketmine\plugin\PluginBase;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\utils\Config;
use pocketmine\utils\TextFormat as TF;
use MyPlot\MyPlot as SB;

class Main extends PluginBase implements Listener{

	public function onLoad(){
		$this->getLogger()->info("§ePlugin Loading!");
	}

	public function onEnable(){
    	$this->getLogger()->info(TF::GREEN.TF::BOLD."Plugin Make By GreenJajot");
	}

	public function onDisable(){
    	$this->getLogger()->info("§cPlugin Disabled!");
  	}

	public function onCommand(CommandSender $sender, Command $cmd, string $label, array $args): bool{
		switch(strtolower($cmd->getName())){
			case "iddao":
			if(!($sender instanceof Player)){
				$sender->sendMessage(TF::RED . TF::BOLD ."Error: ". TF::RESET . TF::DARK_RED ."Please use this command in game!");
				return true;
				break;
			}
				if($sender->hasPermission("iddao.command")){
					if(isset($args[0])){
						if(!$sender->hasPermission("iddao.command")){
							$error_handPermission = "Bạn Không Có Quyền Sử Dụng Lệnh Này";
							$sender->sendMessage(TF::RED . TF::BOLD . "Lỗi: " . TF::RESET . TF::RED . $error_handPermission);
							return false;
						}
						$player = $args[0];
        $levelName = $player;
        $plots = SB::getInstance()->getProvider()->getPlotsByOwner($args[0]);
        if (empty($plots)) {
            $sender->sendMessage(TF::RED . "Mục tiêu không sở hữu đảo nào");
            return true;
        }
        $sender->sendMessage(TF::DARK_GREEN . "Đảo đang hoạt động:");

        usort($plots, function ($plot1, $plot2) {
            /** @var $plot1 Plot */
            /** @var $plot2 Plot */
            if ($plot1->levelName == $plot2->levelName) {
                return 0;
            }
            return ($plot1->levelName < $plot2->levelName) ? -1 : 1;
        });

        for ($i = 0; $i < count($plots); $i++) {
            $plot = $plots[$i];
            $message = "§d• §eID đảo: §a" . ($i + 1) . " ";
            $message .= "§d• §eĐịa chỉ §a" . " " . $plot;
            if ($plot->name !== "") {
                $message .= " = " . $plot->name;
            }
            $sender->sendMessage($message);
        }
					}
		return true;
	}
}
}
}
