<?php

namespace ac\top;

use pocketmine\plugin\PluginBase;
use pocketmine\Player; 
use pocketmine\Server;
use pocketmine\event\Listener;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\utils\Config;

use ac\top\form\SimpleForm;

class Main extends PluginBase implements Listener {

	public $i;

    /**
     * @deprecated
     *
     * @param callable|null $function
     * @return SimpleForm
     */
    public function createSimpleForm(callable $function = null) : SimpleForm {
        return new SimpleForm($function);
    }

	public function onLoad(){
		$this->saveResource("setting.yml");  
  }

	public function onEnable(){
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
		$this->getServer()->getLogger()->info("TopMoneyUI by §4Execu§rtivAC!");
		@mkdir($this->getDataFolder());
    $this->saveDefaultConfig();
		$this->config = new Config($this->getDataFolder() . "setting.yml", Config::YAML);
	}

	public function onCommand(CommandSender $sender, Command $command, $label, array $params) : bool{
		switch(array_shift($params)){
			default:
				$this->TopM($sender);
		}
		return true;
	}

	public function TopM($player){
		$money = $this->getServer()->getPluginManager()->getPlugin("EconomyAPI");
		$money_top = $money->getAllMoney();
		$message = "";
		$message1 = "";
		$topmoney = "     §eTopMoney on §l§eᴍɪɴᴇ§bᴛᴏᴡɴ    ";
		$topmoney1 = "     §6TopMoney on §l§eᴍɪɴᴇ§bᴛᴏᴡɴ    ";
		if(count($money_top) > 0){
			arsort($money_top);
			$i = 1;
			foreach($money_top as $name => $money){
				$message .= "  §f[".$i."] ".$name.":    ".$money." §a".$this->config->get("unit")."\n";
				$message1 .= " §b ".$i.". §d".$name."    §9".$money." §a".$this->config->get("unit")."\n";
				if($i >= 10){
					break;
					}
					++$i;
				}}
		$form = $this->createSimpleForm(function (Player $player, int $data = null){
			$result = $data;
			if($result === null){
				return true;
				}
				switch($result){
					case "0";
					break;
				}
			});
			$form->setTitle("".$this->config->get("title"));
			$form->setContent("".$message);
			$form->addButton("".$this->config->get("button"));
			$form->sendToPlayer($player);
			return $form;
	}
}
