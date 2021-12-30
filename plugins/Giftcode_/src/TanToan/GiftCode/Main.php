<?php

namespace TanToan\GiftCode;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\ConsoleCommandSender;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\Server;
use pocketmine\scheduler\ClosureTask;
use pocketmine\utils\Config;
//use jojoe77777\FormAPI\FormAPI as FAPI;
class Main extends PluginBase implements Listener
{
    /**
     * @var Config
     */
    private $code, $type, $form;

    public function onEnable()
    {
        if (!file_exists($this->getDataFolder())) {
            mkdir($this->getDataFolder());
        }
        $this->saveResource("type.yml");
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
		$this->form = $this->getServer()->getPluginManager()->getPlugin('FormAPI');
        $this->code = new Config($this->getDataFolder() . "code.yml", Config::YAML);
        $this->type = new Config($this->getDataFolder() . "type.yml", Config::YAML);
        //check code lỗi
        $this->getScheduler()->scheduleTask(new Checkallcode($this));
    }

    public function onjoin(PlayerJoinEvent $event)
    {
        if($event->getPlayer()->isOp()){
        $t = $this->code->getAll();
        foreach (array_keys($this->code->getAll()) as $code) {
                $event->getPlayer()->sendMessage("§f• §aCode chưa sử dụng§r " . $code . "§a- Type: §a" . $t[$code]['Type']);
        }
    }
}
    public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool
    {
        switch (strtolower($command->getName())) {
                case "nhapcode":
                        if (!$sender instanceof Player) {
                            $sender->sendMessage("§f• §aSử dụng lệnh ingame ! ");
                            return true;
                        }
						if(!isset($args[0])){
							return true;
						}
                        $code = $args[0];
                        $t = $this->code->getAll();
                            //give reward va xoa data code
                            if (isset($t[$code])) {
                            foreach ($this->type->get($t[$code]["Type"]) as $command) {
                                Server::getInstance()->dispatchCommand(new ConsoleCommandSender(), str_replace("{player}", $sender->getName(), $command));
                            }
                            $this->code->remove($code);
                            $this->code->save();
                        } else {
                            $sender->sendMessage("§f• §cCode không hợp lệ, vui lòng thử lại hoặc bấm §c/giftcode help§c để biết thêm chi tiết ");
                        }
            break;
            case "giftcode":
                if (!isset($args[0])) {
                    $sender->sendMessage("§f• §aBấm §c/giftcode help §ađể biết thêm chi tiết ! ");
                    return true;
                }
                switch (strtolower($args[0])) {
                    case "help":
                        $sender->sendMessage("§f• §6GiftCode Help");
                        $sender->sendMessage("§f• §c/giftcode type :để xem loại code");
                        $sender->sendMessage("§f• §c/giftcode create <type> : Đưa code cho người chơi");
                        $sender->sendMessage("§f• §c/nhapcode§a: để dùng và kiểm tra code của bạn");
                        //$sender->sendMessage("§f• §c/giftcode get <code> §a: để sử dụng code");
                        $sender->sendMessage("§f• §c/giftcode all <page>§a : để xem tất cả code chưa sử dụng của member");
                        break;
                    case "create":
                        if (count($args) < 2) {
                            $sender->sendMessage("§f• §c /giftcode create <type> : Đưa code cho người chơi");
                            return true;
                        }
                        if (!$sender->isOp()) {
                            $sender->sendMessage("§4♤ Bạn không đủ quyền dùng lệnh này !");
                            return true;
                        }
                        //check type code
                        $type = $this->type->getAll();
                        if (!isset($type[$args[1]])) {
                            $sender->sendMessage("§f• §cLoại code không hợp lệ, bấm §a/giftcode type §cđể xem");
                            return true;
                        }
    if (!isset($args[2])) {
        $this->arg2 = 2;
    }else{
        $this->arg2 = $args[2] + 1;
    }
    if (!isset($args[3])) {
        $this->arg3 = "false";
    }elseif($args[3] === "true"){
        $this->arg3 = "true";
    }else{
        $this->arg3 = "false";
    }
    $this->arg1 = $args[1];
    $this->sender = $sender;
                        $this->createcode = $this->getScheduler()->scheduleRepeatingTask(new ClosureTask(function($_) : void{
            if(--$this->arg2 === 0){
                $this->getScheduler()->cancelTask($this->createcode->getTaskId());
            }else{
$code = $this->createcode();
$t = $this->code->getAll();
$t[$code]["Type"] = $this->arg1;
$this->code->setAll($t);
$this->code->save();
if($this->arg3 === "true"){
Server::getInstance()->broadcastMessage("§7Code Mới: §l§3".$code);
}else{
$this->sender->sendMessage("§f• §cTạo code thành công $code - $this->arg1");
                }
            }
        }), 1);
                        break;
                    case "all":
                        if (!$sender->isOp()) {
                            $sender->sendMessage("♤ Bạn không đủ quyền dùng lệnh này !");
                            return true;
                        }
                        $sender->sendMessage("Code Member chưa sử dụng");
                        $t = $this->code->getAll();
                        foreach (array_keys($this->code->getAll()) as $code) {
                            $sender->sendMessage("- Code " . $code . "- Type: " . $t[$code]['Type']);
                        }
                        break;
                    case "type":
                        $sender->sendMessage("Cac loai code");
                        foreach (array_keys($this->type->getAll()) as $code) {
                            $sender->sendMessage("- Code " . $code);
                        }
                        break;
                    default:
                        $sender->sendMessage("§f• §aCú pháp không hợp lệ ,Bấm §c/giftcode help §ađể biết thêm chi tiết ! ");
                        return true;
                }
                break;
        }
        return true;
    }

    private function createcode()
    {
        $t = $this->code->getAll();
        $code = ("CMT" . substr(md5(uniqid(mt_rand(), true)), 0, mt_rand(5,10)));
        if (isset($t[$code])) {
            // code trùng tạo lại
            $this->createcode();
        }
        return $code;
    }
}