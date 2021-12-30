<?php
namespace nlog\Builder;
use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\utils\TextFormat;
use pocketmine\utils\Config;
use pocketmine\command\CommandSender;
use pocketmine\command\Command;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\utils\Utils;
class Main extends PluginBase implements Listener{
	
public $pre = "§l§a[§6 BUILDER§a ]§r ";
 	 public function onEnable(){
    	$this->getServer()->getPluginManager()->registerEvents($this, $this);
       
		$this->getLogger()->notice("§l§bPlugin Builder");
		$this->getLogger()->info("§l§eEnable");
    	
    	
    	//Config
    	@mkdir($this->getDataFolder(), 0744, true);
    	$this->builder = new Config($this->getDataFolder() . "builder.yml", Config::YAML); //Config 생성
 	 }
 	 
 	 
 	 //경찰 API
 	 public function getBuilder() {
 	 	/*
 	 	 * 경찰의 목록을 Config에서 가져옵니다.
 	 	 */
 	 	return $this->builder->getAll(true);
 	 }
 	 
 	 public function isBuilder($name) {
 	 	/*
 	 	 * Config에 $name이 있으면 true를 없으면 false를 반환합니다.
 	 	 */
 	 	return $this->builder->exists($name);
 	 }
 	 
 	 public function setBuilder($name) {
 	 	/*
 	 	 * 경찰을 Config에 추가합니다.
 	 	 */
 	 	$this->builder->set($name, "builder");
 	 	$this->builder->save();
 	 	return true;
 	 }
 	 
 	 public function removeBuilder($name) {
 	 	/*
 	 	 * 경찰을 Config에서 제거합니다.
 	 	 */
 	 	$this->builder->remove($name, "builder");
 	 	$this->builder->save();
 	 	return true;
 	 }
 	 
 	 public function onCommand(CommandSender $sender,Command $cmd,string $label,array $args) :bool{
 	 	
 	 	$msg = "§7/builder <add | remove> <Username> \n §b§o[ Builder ] §7/builder list";
 	 	
 	 	if(strtolower($cmd->getName() === "builder")) {
 	 		if (!($sender->isOp())) {
 	 			$sender->sendMessage("§d•§e Bạn không có quyền");
 	 			return true; //OP 가 아닐 때 - 안전빵으로 한번 더ㅋㅋ
 	 		}
 	 		if (!(isset($args[0]))) {
 	 			$sender->sendMessage($msg);
 	 			return true;
 	 		}
			#-----------------------------------------------------------------------------
 	 		if ($args[0] === "add") {
 	 			if (!(isset($args[1]))) {
 	 				$sender->sendMessage($msg);
 	 				return true;
 	 			} //닉네임이 없을 때
				
 	 		$this->setBuilder(strtolower($args[1]));
 	 		$sender->sendMessage($this->pre."§b§l Bạn vừa set Builder cho §e".$args[1].".");
			return true;
 	 		}
			#-----------------------------------------------------------------------------
 	 		if ($args[0] === "remove") {
 	 			if (!(isset($args[1]))) {
 	 				$sender->sendMessage($msg);
 	 				return true;
 	 			} //닉네임이 없을 때
				
 	 		if (!($this->isBuilder(strtolower($args[1])))) {
 	 				$sender->sendMessage($this->pre. "§c§lNgười này không có trong danh sách Bulder.");
 	 				return true;
 	 			} //닉네임이 경찰이 아닐 때
				
 	 			$this->removeBuilder($args[1]);
 	 			$sender->sendMessage($this->pre."§b§lXóa Builder của §e".$args[1]."§b thành công");
 	 			return true;
 	 		}
			#-----------------------------------------------------------------------------
 	 		if ($args[0] === "list") {
 	 			$list = implode("\n ", $this->getBuilder());
 	 			$sender->sendMessage($this->pre."§b§oDanh sách Builder§a: " . $list);
 	 			return true; //리스트
				
			}else{
				$sender->sendMessage($msg);
				return true; //$args[0]이 없을 때
				
			}
 	 	}
 	 }
 	 
 	 public function onJoin (PlayerJoinEvent $ev) {
			$name = $ev->getPlayer()->getName();
			if ($this->isBuilder(strtolower($name)) === true) {
				$per = $ev->getPlayer()->addAttachment($this);
				$per->setPermission("pocketmine.command.time", true);
				$per->setPermission("essentials.kick", true);
				$per->setPermission("essentials.gamemode", true);
				$per->setPermission("pocketmine.command.teleport", true);
				$per->setPermission("pocketmine.command.list", true);
				$per->setPermission("we.session", true);
				$per->setPermission("we.command.brush", true);
				$per->setPermission("we.command.aset", true);
				$per->setPermission("we.command.copy", true);
				$per->setPermission("we.command.set", true);
				$per->setPermission("we.command.flip", true);
				$per->setPermission("we.command.paste", true);
				$per->setPermission("we.command.pos", true);
				$per->setPermission("we.command.replace", true);
				$per->setPermission("we.command.schematic", true);
				$per->setPermission("we.command.togglewand", true);
				$per->setPermission("we.command.wand", true);
				$per->setPermission("we.command.undo", true);
				$per->setPermission("we.command.redo", true);
				$per->setPermission("we.command.debug", true);
				$per->setPermission("we.command.toggledebug", true);
				$per->setPermission("we.command.rotate", true);
				$per->setPermission("we.command.flood", true);
				$per->setPermission("we.command", true);
				$ev->getPlayer()->setDisplayName("§l§f[§6Builder§f] §r".$name);
				/*pocketmine.command.ban
				pocketmine.command.ban.player
				pocketmine.command.ban.ip
				pocketmine.command.ban.list
				pocketmine.command.unban
				pocketmine.command.unban.player
				pocketmine.command.unban.ip*/
				}
 	}
 	 
 	 public function onQuit (PlayerQuitEvent $ev) {
		$name = $ev->getPlayer()->getName();
 	 	$per = $this->getServer()->getPlayer($name)->addAttachment($this);
				$per->setPermission("pocketmine.command.time", false);
				$per->setPermission("essentials.kick", false);
				$per->setPermission("essentials.gamemode", false);
				$per->setPermission("pocketmine.command.teleport", false);
				$per->setPermission("pocketmine.command.list", false);
				$per->setPermission("we.session", false);
				$per->setPermission("we.command.brush", false);
				$per->setPermission("we.command.aset", false);
				$per->setPermission("we.command.copy", false);
				$per->setPermission("we.command.set", false);
				$per->setPermission("we.command.flip", false);
				$per->setPermission("we.command.paste", false);
				$per->setPermission("we.command.pos", false);
				$per->setPermission("we.command.replace", false);
				$per->setPermission("we.command.schematic", false);
				$per->setPermission("we.command.togglewand", false);
				$per->setPermission("we.command.wand", false);
				$per->setPermission("we.command.undo", false);
				$per->setPermission("we.command.redo", false);
				$per->setPermission("we.command.debug", false);
				$per->setPermission("we.command.toggledebug", false);
				$per->setPermission("we.command.rotate", false);
				$per->setPermission("we.command.flood", false);
				$per->setPermission("we.command", false);
 	 }
  }
?>
