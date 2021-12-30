<?php

namespace NCDEnchantUI;

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
use NCDEnchantUI\libs\jojoe77777\FormAPI\{
    CustomForm,
    SimpleForm
};
use pocketmine\plugin\PluginBase;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\ConsoleCommandSender;

use onebone\economyapi\EconomyAPI;
use DaPigGuy\PiggyCustomEnchants\CustomEnchants\CustomEnchants;

class Main extends PluginBase{
    
    public function onEnable(): void{
        @mkdir($this->getDataFolder());
        $this->eco = $this->getServer()->getPluginManager()->getPlugin("EconomyAPI");
        $this->shop = new Config($this->getDataFolder() . "Shop.yml", Config::YAML);
        if(is_null($this->shop->getNested('version'))){
            file_put_contents($this->getDataFolder() . "Shop.yml",$this->getResource("Shop.yml"));
            $this->getLogger()->notice("Updating Plugin Config.....");
        }
        $this->saveDefaultConfig();
        $this->getServer()->getCommandMap()->register("buyec", new Commands\ShopCommand($this));
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
		$money = EconomyAPI::getInstance()->myMoney($player);
        $form->setTitle("§l§b♦ §cMINETOWN ENCHANT §b♦");
        $form->setContent("§e• §cTiền của bạn: §e$".$money);
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
            "MONEY" => EconomyAPI::getInstance()->myMoney($player)
            );
            if ($data === null){
                $this->listForm($player);
                return;
            }
            if(!$player->getInventory()->getItemInHand() instanceof Tool and !$player->getInventory()->getItemInHand() instanceof Armor){
                #$player->sendMessage("");
                $this->EnchantHoldItem($player);
                return;
            }
            if(EconomyAPI::getInstance()->myMoney($player) > $c = $array[$id]['price'] * $data[1]){
                EconomyAPI::getInstance()->reduceMoney($player, $c);
                $this->enchantItem($player, $data[1], $array[$id]['enchantment']); 
                #$player->sendMessage("");
                $this->EnchantSuccess($player);
            }else{
                #$player->sendMessage("");
                $this->EnchantUnSuccessFul($player);
            }
        }
        );
        $money = EconomyAPI::getInstance()->myMoney($player);
        $form->addLabel("§l§e• §cTiền của bạn: §e$".$money."\n\n".$this->replace($this->shop->getNested('messages.label'),["PRICE" => $array[$id]['price']]));
        $form->setTitle("§l§b♦ §cMINETOWN ENCHANT §b♦");
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
    
    public function EnchantHoldItem($player){
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
	$form->setTitle("§l§b♦ §cMINETOWN ENCHANT §b♦");
	$form->setContent("§l§cHãy Cầm Đúng Vật Phẩm Để Enchant");
	$form->addButton("Submit");
	$form->sendToPlayer($player);
    }
    
    public function EnchantSuccess($player){
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
	$form->setTitle("§l§b♦ §cMINETOWN ENCHANT §b♦");
	$form->setContent("§l§aBạn Đã Mua Enchant Thành Công");
	$form->addButton("Submit");
	$form->sendToPlayer($player);
	}
	
	public function EnchantUnSuccessFul($player){
		$api = $this->getServer()->getPluginManager()->getPlugin("FormAPI");
		$form = $api->createSimpleForm(function (Player $player, $data){
			$result = $data;
			if ($result == null) 
			{$this->listForm($player);
			                return;
			}
			switch ($result) {
					case 1:
						break;
			}
		});
	$form->setTitle("§l§b♦ §cMINETOWN ENCHANT §b♦");
	$form->setContent("§l§cBạn Không Đủ Tiền Để Mua Enchant");
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
