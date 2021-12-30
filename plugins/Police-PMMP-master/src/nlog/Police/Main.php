<?php
namespace nlog\Police;
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
	
public $pre = "§l§a[§e MineTown§a ]§r ";
 	 public function onEnable(){
    	$this->getServer()->getPluginManager()->registerEvents($this, $this);
       
		$this->getLogger()->notice("§l§bPlugin Police");
		$this->getLogger()->info("§l§eEnable");
    	
    	
    	//Config
    	@mkdir($this->getDataFolder(), 0744, true);
    	$this->police = new Config($this->getDataFolder() . "office.yml", Config::YAML); //Config 생성
 	 }
 	 
 	 
 	 //경찰 API
 	 public function getPolice() {
 	 	/*
 	 	 * 경찰의 목록을 Config에서 가져옵니다.
 	 	 */
 	 	return $this->police->getAll(true);
 	 }
 	 
 	 public function isPolice($name) {
 	 	/*
 	 	 * Config에 $name이 있으면 true를 없으면 false를 반환합니다.
 	 	 */
 	 	return $this->police->exists($name);
 	 }
 	 
 	 public function setPolice($name) {
 	 	/*
 	 	 * 경찰을 Config에 추가합니다.
 	 	 */
 	 	$this->police->set($name, "police");
 	 	$this->police->save();
 	 	return true;
 	 }
 	 
 	 public function removePolice($name) {
 	 	/*
 	 	 * 경찰을 Config에서 제거합니다.
 	 	 */
 	 	$this->police->remove($name, "police");
 	 	$this->police->save();
 	 	return true;
 	 }
 	 
 	 public function onCommand(CommandSender $sender,Command $cmd,string $label,array $args) :bool{
 	 	
 	 	$msg = "§7/police <add | remove> <Username> \n §b§o[ Police ] §7/police list";
 	 	
 	 	if(strtolower($cmd->getName() === "police")) {
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
				
 	 		$this->setPolice(strtolower($args[1]));
 	 		$sender->sendMessage($this->pre."§b§l Bạn vừa set Police cho §e".$args[1].".");
			return true;
 	 		}
			#-----------------------------------------------------------------------------
 	 		if ($args[0] === "remove") {
 	 			if (!(isset($args[1]))) {
 	 				$sender->sendMessage($msg);
 	 				return true;
 	 			} //닉네임이 없을 때
				
 	 		if (!($this->isPolice(strtolower($args[1])))) {
 	 				$sender->sendMessage($this->pre. "§c§lNgười này không có trong danh sách Police.");
 	 				return true;
 	 			} //닉네임이 경찰이 아닐 때
				
 	 			$this->removePolice($args[1]);
 	 			$sender->sendMessage($this->pre."§b§lXóa Police của §e".$args[1]."§b thành công");
 	 			return true;
 	 		}
			#-----------------------------------------------------------------------------
 	 		if ($args[0] === "list") {
 	 			$list = implode("\n ", $this->getPolice());
 	 			$sender->sendMessage($this->pre."§b§oDanh sách Police§a: " . $list);
 	 			return true; //리스트
				
			}else{
				$sender->sendMessage($msg);
				return true; //$args[0]이 없을 때
				
			}
 	 	}
 	 }
 	 
 	 public function onJoin (PlayerJoinEvent $ev) {
			$name = $ev->getPlayer()->getName();
			if ($this->isPolice(strtolower($name)) === true) {
				$per = $ev->getPlayer()->addAttachment($this);
				$per->setPermission("pocketmine.command.kick", true);
				$per->setPermission("essentials.fly.use", true);
				$per->setPermission("pocketmine.command.teleport", true);
				$per->setPermission("pocketmine.command.list", true);
				$per->setPermission("essentials.mute.", true);
				$per->setPermission("essentials.mute.notify", true);
				$per->setPermission("essentials.mute.use", true);
				$per->setPermission("essentials.mute.exempt", true);
				$ev->getPlayer()->setDisplayName("§l§f[§bQuản Lí§f] §r".$name);
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
 	 	$per->setPermission("pocketmine.command.kick", false);
		$per->setPermission("essentials.fly.use", false);
 	 	$per->setPermission("pocketmine.command.teleport", false);
 	 	$per->setPermission("pocketmine.command.list", false);
		$per->setPermission("essentials.mute.", false);
		$per->setPermission("essentials.mute.notify", false);
		$per->setPermission("essentials.mute.use", false);
        $per->setPermission("essentials.mute.exempt", false);
 	 }
  }
?>
