<?php
namespace NCDEnchantCEUI\Commands;

use pocketmine\command\{
    Command,
    PluginCommand,
    CommandSender
};
use pocketmine\Player;
use NCDEnchantCEUI\Main;

class ShopCommand extends PluginCommand {
    
    /**
     * ShopCommand constructor.
     * @param Main $plugin
     */
    public function __construct(Main $plugin) {
        parent::__construct('§l§l', $plugin);
        #$this->setAliases(['muace']);
        $this->setDescription('Mở Menu Enchant EC');
        $this->setPermission("buyce.command.enchant");
        $this->plugin = $plugin;
    }
    
   /**
    * @param CommandSender $sender
    * @param string $commandLabel
    * @param array $args
    *
    * @return bool
    */
    public function execute(CommandSender $sender, string $commandLabel, array $args): bool {
        if(!$sender->hasPermission("buyce.command.enchant")){
            $sender->sendMessage("§cBạn không có quyền để sử dụng câu lệnh này");
            return true;
        }
        if(!$sender instanceof Player){
            $sender->sendMessage("Please use this in-game.");
            return true;
        }   
        $this->plugin->listForm($sender);
        return true;
	}
   
}
