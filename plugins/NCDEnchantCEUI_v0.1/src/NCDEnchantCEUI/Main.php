<?php

namespace NCDEnchantCEUI;

use pocketmine\{
    Server,
    Player
};
use pocketmine\item\{
    Item,
    Tool,
    Armor,
    enchantment\Enchantment,
    enchantment\EnchantmentInstance
};
use pocketmine\utils\Config;
use NCDEnchantCEUI\libs\jojoe77777\FormAPI\{
    CustomForm,
    SimpleForm
};
use pocketmine\plugin\PluginBase;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\ConsoleCommandSender;

use onebone\pointapi\PointAPI;
use DaPigGuy\PiggyCustomEnchants\CustomEnchants\CustomEnchants;

class Main extends PluginBase{
    
    public function onEnable(): void{
        @mkdir($this->getDataFolder());
        $this->pointapi = $this->getServer()->getPluginManager()->getPlugin("PointAPI");
        $this->shop = new Config($this->getDataFolder() . "Shop.yml", Config::YAML);
        if(is_null($this->shop->getNested('version'))){
            file_put_contents($this->getDataFolder() . "Shop.yml",$this->getResource("Shop.yml"));
        }
        $this->saveDefaultConfig();
        $this->getServer()->getCommandMap()->register("buyce", new Commands\ShopCommand($this));
        $this->piggyCE = $this->getServer()->getPluginManager()->getPlugin("PiggyCustomEnchants");
    }
    
	/**
    * @param Player $player
    */
    public function listForm(Player $player): void{
        $form = new SimpleForm(function (Player $player, $data = null){
            if ($data === null){
			$this->getServer()->getCommandMap()->dispatch($player, "buyec");
                return;
            }
            $this->buyForm($player, $data);
        });
		foreach ($this->shop->getNested('shop') as $name){
            $var = array(
            "NAME" => $name['name'],
            "PRICE" => $name['price']
            );
			$form->addButton($this->replace($this->shop->getNested('Button'), $var));
		}
		$point = PointAPI::getInstance()->myPoint($player);
        $form->setTitle("§l§b♦ §cMINETOWN ENCHANT CE §b♦");
        $form->setContent("§e• §cMcoin của bạn: §e$".$point);
        $player->sendForm($form);
    }
    
	/**
    * @param Player $player
    * @param int $id
    */
    public function buyForm(Player $player,int $id): void{
        $array = $this->shop->getNested('shop');
        $form = new CustomForm(function (Player $player, $data = null) use ($array, $id){
            $var = array(
            "NAME" => $array[$id]['name'],
            "PRICE" => $array[$id]['price'] * $data[1],
            "LEVEL" => $data[1],
            "POINT" => PointAPI::getInstance()->myPoint($player)
            );
            if ($data === null){
                $this->listForm($player);
                return;
            }
            if(!$player->getInventory()->getItemInHand() instanceof Tool and !$player->getInventory()->getItemInHand() instanceof Armor){
                $this->NCD1($player);
                return;
            }
            if(PointAPI::getInstance()->myPoint($player) > $c = $array[$id]['price'] * $data[1]){
                PointAPI::getInstance()->reducePoint($player, $c);
                $this->enchantItem($player, $data[1], $array[$id]['enchantment']);
                $this->NCD2($player);
            }else{
                $this->NCD3($player);
            }
        }
        );
        $point = PointAPI::getInstance()->myPoint($player);
        $form->addLabel("§l§e• §cMcoin của bạn: §e$".$point."\n\n".$this->replace($this->shop->getNested('messages.label'),["PRICE" => $array[$id]['price']]));
        $form->setTitle("§l§b♦ §cMINETOWN ENCHANT CE §b♦");
        $form->addSlider($this->shop->getNested('slider-title'), 1, $array[$id]['max-level'], 1, -1);
        $player->sendForm($form);
    }
    
    /**
    * @param Player $Item
    * @param int $level
    * @param int|String $enchantment
    */
	public function enchantItem(Player $player, int $level, $enchantment): void{
        $item = $player->getInventory()->getItemInHand();
        if(is_string($enchantment)){
            $ench = Enchantment::getEnchantmentByName((string) $enchantment);
            if($this->piggyCE !== null && $ench === null){
                $ench = CustomEnchants::getEnchantmentByName((string) $enchantment);
            }
            if($this->piggyCE !== null && $ench instanceof CustomEnchants){
                $this->piggyCE->addEnchantment($item, $ench->getName(), (int) $level);
            }else{
                $item->addEnchantment(new EnchantmentInstance($ench, (int) $level));
            }
        }
        if(is_int($enchantment)){
            $ench = Enchantment::getEnchantment($enchantment);
            $item->addEnchantment(new EnchantmentInstance($ench, (int) $level));
        }
        $player->getInventory()->setItemInHand($item);
    }
    
    public function NCD1($player){
		$api = $this->getServer()->getPluginManager()->getPlugin("FormAPI");
		$form = $api->createSimpleForm(function (Player $player, $data){
			$result = $data;
			if ($result == null) {
			    $this->listForm($player);
                return;
			}
			switch ($result) {
					case 1:
						break;
			}
		});
	$form->setTitle("§l§b♦ §cMINETOWN ENCHANT CE §b♦");
	$form->setContent("§l§cHãy Cầm Đúng Vật Phẩm Để Enchant CE");
	$form->addButton("Submit");
	$form->sendToPlayer($player);
    }
    
    public function NCD2($player){
		$api = $this->getServer()->getPluginManager()->getPlugin("FormAPI");
		$form = $api->createSimpleForm(function (Player $player, $data){
			$result = $data;
			if ($result == null) {
				$this->listForm($player);
                return;
			}
			switch ($result) {
					case 1:
						break;
			}
		});
	$form->setTitle("§l§b♦ §cMINETOWN ENCHANT CE §b♦");
	$form->setContent("§l§aBạn Đã Mua Enchant EC Thành Công");
	$form->addButton("Submit");
	$form->sendToPlayer($player);
	}
	
	public function NCD3($player){
		$api = $this->getServer()->getPluginManager()->getPlugin("FormAPI");
		$form = $api->createSimpleForm(function (Player $player, $data){
			$result = $data;
			if ($result == null) {
				$this->listForm($player);
				                return;
			}
			switch ($result) {
					case 1:
						break;
			}
		});
	$form->setTitle("§l§b♦ §cMINETOWN ENCHANT CE §b♦");
	$form->setContent("§l§cBạn Không Đủ Mcoin Để Mua Enchant CE");
	$form->addButton("Submit");
	$form->sendToPlayer($player);
	}
    
    /**
    * @param string $message
    * @param array $keys
    *
    * @return string
    */
    public function replace($message, array $keys){
        foreach($keys as $word => $value){
            $message = str_replace("{".$word."}", $value, $message);
        }
        return $message;
    }
}
