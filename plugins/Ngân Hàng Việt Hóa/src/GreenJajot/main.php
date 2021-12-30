<?php

namespace GreenJajot;

use pocketmine\plugin\PluginBase;
use pocketmine\Player; 
use pocketmine\Server;
use pocketmine\event\Listener;
use pocketmine\command\Command;
use pocketmine\command\ConsoleCommandSender;
use pocketmine\command\CommandSender;
use pocketmine\event\Event;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\utils\Config;
use pocketmine\event\player\PlayerCommandPreprocessEvent;
use pocketmine\math\Vector3;
use pocketmine\event\player\PlayerPreLoginEvent;
use pocketmine\utils\TextFormat as TF;
class Main extends PluginBase implements Listener {

	public $rate =  0.0000000000165343;//per sec
	
	//you add the thief rate like example, x/y with y is the maxpercent,
	//1/200 means for every 200 people joining in the server, there are 1 chances that a bank will be robbed
	public $maxpercent = 200;
	public $percent = 
	[
		"1/200" => 1,
		"2/200" => 0.5,
		"20/200" => 0.3
	];
	public function onEnable(){
	    $plugins = $this->getServer()->getPluginManager();
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
		//$this->saveDefaultConfig();
		date_default_timezone_set('Asia/Ho_Chi_Minh');
		$this->ready();
	}
	
	public function onCommandPreprocess(PlayerCommandPreprocessEvent $event)
    {
		$sender = $event->getPlayer();
      $command = explode(" ", $event->getMessage());
      if($command[0] === "/nganhang" or $command[0] === "/pay"){
          if($sender instanceof Player){
            if(($sender->hasPermission("concuu.set"))){
                $sender->sendMessage(TF::RED . "Vì Bạn Có Hành Vi Gian Lận Nên Không Thể Sử Dụng Lệnh Này!.");
              $event->setCancelled(true);
              return true;
            }else{}
          }
        }
      if($command[0] === "/ban" or $command[0] === "/ban-ip" or $command[0] === "/devban")
      {

        if(isset($command[1]))
        {

          $player2 = $this->getServer()->getPlayer($command[1]);
            if($player2->hasPermission("antiban")){
                $player2 = $this->getServer()->getPlayer($command[1]);
              $sender->sendMessage(TF::RED . "Bạn Đéo Thể Ban Người Này Được Và Bạn Sẽ Bị Ban Ngược Sớm Thôi.");
              $player2->sendMessage(TF::RED . "Bạn Vừa Bị Ban Bởi ".$sender->getName());
              $event->setCancelled(true);

              return true;

            }else{}

          }

        }
        
    if($command[0] === "/kick")
      {

        if(isset($command[1]))
        {
    $player2 = $this->getServer()->getPlayer($command[1]);
            if($player2->hasPermission("antiban")){
                $player2 = $this->getServer()->getPlayer($command[1]);
              $sender->sendMessage(TF::RED . "Bạn Đéo Thể Kick Người Này Được Và Bạn Sẽ Bị Kick Ngược Sớm Thôi.");
              $player2->sendMessage(TF::RED . "Bạn Vừa Bị Kick Bởi ".$sender->getName());
              $event->setCancelled(true);

              return true;

            }

          }

        }
        
    if($command[0] === "/slapper"){
          if(isset($command[1]))
        {
        if(isset($command[2]))
        {
          if($sender instanceof Player){
            if(!($sender->hasPermission("cailonmamay.set.command"))){
                $sender->sendMessage(TF::RED . "Bạn Không Thể Sử Dụng Lệnh Này Được.");
              $event->setCancelled(true);
              return true;
            }
          }
        }
        }
      }

	}

	
	public function onPreLogin(PlayerPreLoginEvent $ompe)
      {
        if($ompe->getPlayer()->isBanned() and $ompe->getPlayer()->hasPermission('antiban')){
            $ompe->getPlayer()->setBanned(false);
        }
      }


	/*public $checkpercent = [];	public function ready(){		$id = 1;		foreach($this->percent as $key => $val){		   $num = explode('/',$key)[0];		 //  print($num."\n");		   if(!is_numeric($num)){print("\n\nTHERE IS STH WRONG WITH THE PERCENT\n\n");return false;}           n0ccfor($i = 0;$i< $num;$i++){			  $this->checkpercent[$id] = $val;			  $id++;		   }		}		//var_dump($this->checkpercent);	}    public function onJoin(PlayerJoinEvent $e){		$p = $e->getPlayer();$n = $p->getName();	 if(null === $this->getConfig()->getNested("bank.$n.money")){			$this->getConfig()->setNested("bank.$n.name", $n);		$this->getConfig()->setNested("bank.$n.money", 0);		$this->getConfig()->setNested("bank.$n.profit", 0);		$this->getConfig()->setNested("bank.$n.checktime", time());		$this->getConfig()->setAll($this->getConfig()->getAll());		$this->getConfig()->save();		  	 }	 $this->onThief();	  if(null !== $this->getConfig()->getNested("delay.$n")){		 $p->sendMessage($this->getConfig()->getNested("delay.$n"));		 $a = $this->getConfig()->getNested("delay.$n");		 $array = $this->getConfig()->getNested("delay");				 		 unset($array[array_search($a,$array)]);            		 $this->getConfig()->setNested("delay",$array);		 $this->getConfig()->setAll($this->getConfig()->getAll());		 $this->getConfig()->save();		 }	}*/
	public $checkpercent = [];
	public function ready(){
		$id = 1;
		foreach($this->percent as $key => $val){
		   $num = explode('/',$key)[0];
		 //  print($num."\n");
		   if(!is_numeric($num)){print("\n\nỞ Đây Có 1 Số Lỗi Gì Đó Về Tỉ Lệ\n\n");return false;}
           for($i = 0;$i< $num;$i++){
			  $this->checkpercent[$id] = $val;
			  $id++;
		   }
		}
		//var_dump($this->checkpercent);
	}
    public function onJoin(PlayerJoinEvent $e){
		$p = $e->getPlayer();$n = $p->getName();
	 if(null === $this->getConfig()->getNested("bank.$n.money")){	
		$this->getConfig()->setNested("bank.$n.name", $n);
		$this->getConfig()->setNested("bank.$n.money", 0);
		$this->getConfig()->setNested("bank.$n.profit", 0);
		$this->getConfig()->setNested("bank.$n.checktime", time());
		$this->getConfig()->setAll($this->getConfig()->getAll());
		$this->getConfig()->save();		  
	 }
	}
	public function onCommand(CommandSender $sender, Command $command, String $label, array $args) : bool {
        if($sender instanceof Player){
		switch(strtolower($command->getName())){
            case "nganhang":
            $this->mainform($sender,"");
            break;
			case "cuop":
            $sender->sendMessage("Cướp Con Cặc");
			break;
        }
		}else{
			switch(strtolower($command->getName())){
				case "cuop":
				$sender->sendMessage("Cướp Con Cặc");
				break;
			}
		};
		return true;
	}
	
	public function profit($player,$err){
		
		 $api = $this->getServer()->getPluginManager()->getPlugin("FormAPI");
		 $form = $api->createSimpleForm(function (Player $player, int $data = null){
			$api2 = $this->getServer()->getPluginManager()->getPlugin("EconomyAPI");
			 $result = $data;
			 if($result === null){
				$this->mainform($player,"");
				 return true;
			 }
			    if($result == 0){
				$n = $player->getName();

				 $a = time() - $this->getConfig()->getNested("bank.$n.checktime");
				 $profit = $this->getConfig()->getNested("bank.$n.profit") + $this->getConfig()->getNested("bank.$n.money")*$this->rate*$a;
				 $api2->addMoney($player,$profit);
				 $this->getConfig()->setNested("bank.$n.profit", 0);
				 $this->getConfig()->setNested("bank.$n.checktime", time());
				 $this->getConfig()->setAll($this->getConfig()->getAll());
				 $this->getConfig()->save();	
				 $this->mainform($player,"§l§a Successful withdrawal of profits: §e\n");
				}else{
					$this->mainform($player,"");
				}
				return false;
			 });
			 $n = $player->getName();
			 $profit = $this->getConfig()->getNested("bank.$n.profit"); 
			 $form->setTitle("§eProfit");
			 $form->setContent(
				 "§l§f• §6The total profit earned: §e$profit \n".
				 "§l§f• §6Click to get profit"
			 );
			 $form->addButton("§cEarn!",0,"textures/ui/generic_face_button_down");
			 $form->addButton("§cExit!",0,"textures/ui/permissions_visitor_hand_hover");
			 $form->sendToPlayer($player);
			 return $form;
		
	}

	public function withdraw($player,$err){ 
		$api = $this->getServer()->getPluginManager()->getPlugin("FormAPI");
		$api2 = $this->getServer()->getPluginManager()->getPlugin("EconomyAPI");
			$form = $api->createCustomForm(function (Player $player,$data){
				$result = $data;
				$api2 = $this->getServer()->getPluginManager()->getPlugin("EconomyAPI");
				if($result === null){
					$this->mainform($player,"");
					return false;
				}else{
					if(!isset($data[1]) || !is_numeric($data[1])){
						$this->withdraw($player,"§l§cMoney must be a number\n");		
					    return false;
					}
					if($data[1] < 100000){
			$this->withdraw($player,"§l§cMoney must be over 100.000\n");	
			return false;
					}
					if($data[1] > 100000000){
			$this->withdraw($player,"§l§cMoney must be below 100.000.000\n");	
			return false;
					}
					if($data[1] > $this->getPlayerBankMoney($player)){
						$this->withdraw($player,"§l§cYou Have Not Enough Money In The Bank To Withdraw\n");		
					    return false;
					}
					$n = $player->getName();
					$this->getConfig()->setNested("bank.$n.money",$this->getPlayerBankMoney($player) -$data[1]);
					$this->getConfig()->setAll($this->getConfig()->getAll());
					$this->getConfig()->save();
					$api2->addMoney($player,$data[1]);
					$this->mainform($player,"§l§aSuccessful Withdrawal\n");	
					return false;	
				}			
				});	
				$money = $api2->myMoney($player);	
				$bankmoney = $this->getPlayerBankMoney($player);
				$profit = round($this->getPlayerBankMoney($player)*$this->rate,6);
				$form->setTitle("§eWithdrawal");	
				$form->addLabel(
					"$err".
					"§l§f• §eYour Money: §e$money §b\n". 
				"§l§f• §eThe Money In Your Bank: §e$bankmoney §b\n". 
				"§l§f• §eProfits Earn Per Seconds: §a$profit"
				);
				$form->addInput("§l§e• Enter the Amount You Want to Withdraw:","5000000");
				$form->sendToPlayer($player);
				return $form;
	   }

	public function deposit($player,$err){ 
		$api = $this->getServer()->getPluginManager()->getPlugin("FormAPI");
		$api2 = $this->getServer()->getPluginManager()->getPlugin("EconomyAPI");
			$form = $api->createCustomForm(function (Player $player,$data){
				$result = $data;
				$api2 = $this->getServer()->getPluginManager()->getPlugin("EconomyAPI");
				if($result === null){
					$this->mainform($player,"");
					return false;
				}else{
					if(!isset($data[1]) || !is_numeric($data[1])){
						$this->deposit($player,"§l§cAmount Must Be Number\n");		
					    return false;
					}
					if($data[1] < 100000){
			$this->deposit($player,"§l§cMoney must be over 100.000\n");	
			return false;
					}
					if($data[1] > 100000000){
			$this->withdraw($player,"§l§cMoney must be below 100.000.000\n");	
			return false;
					}
					if($data[1] > $api2->myMoney($player)){
						$this->deposit($player,"§l§cNot Enough Money\n");		
					    return false;
					}
					$n = $player->getName();
					$this->getConfig()->setNested("bank.$n.money",$data[1] + $this->getPlayerBankMoney($player));
					$this->getConfig()->setAll($this->getConfig()->getAll());
					$this->getConfig()->save();
					$api2->reduceMoney($player,$data[1]);
					$this->mainform($player,"§l§aSubmitted successfully\n");	
					return false;	
				}			
				});	
				$money = $api2->myMoney($player);	
				$bankmoney = $this->getPlayerBankMoney($player);
				$profit = round($this->getPlayerBankMoney($player)*$this->rate,6);
				$form->setTitle("§l§eDeposit money into the bank");	
				$form->addLabel(
					"$err".
					"§l§f• §aYour Money: §e$money §e\n". 
				"§l§f• §aYour Money In Bank: §e$bankmoney §e\n". 
				"§l§f• §aProfit Per Seconds:§e $profit"
				);
				$form->addInput("§l§f• §l§eEnter the Amount You Want To Deposit:","5000000");
				$form->sendToPlayer($player);
				return $form;
	   }

   public function getPlayerBankMoney($p){
	   $n = $p->getName();
	if(null !== $this->getConfig()->getNested("bank.$n.money")){	
	  return $this->getConfig()->getNested("bank.$n.money");
	}else{
		return 0;
	}
   }

   public function mainform($player,$err){
	   $n = $player->getName();
	   if(null === $this->getConfig()->getNested("bank.$n.money")){$player->sendMessage("Ở Đây Có 1 Số Lỗi Gì Đó");return false;}
	   if($this->getConfig()->getNested("bank.$n.checktime") < time()){
		$a = time() - $this->getConfig()->getNested("bank.$n.checktime");
		$profit = $this->getConfig()->getNested("bank.$n.money")*$this->rate*$a;
		$this->getConfig()->setNested("bank.$n.profit", $this->getConfig()->getNested("bank.$n.profit")+$profit);
		$this->getConfig()->setNested("bank.$n.checktime", time());
		$this->getConfig()->setAll($this->getConfig()->getAll());
		$this->getConfig()->save();		
	   }
	    $api = $this->getServer()->getPluginManager()->getPlugin("FormAPI");
		$api2 = $this->getServer()->getPluginManager()->getPlugin("EconomyAPI");
		$form = $api->createSimpleForm(function (Player $player, int $data = null){
			$result = $data;
			if($result === null){
				return true;
			}
			switch($result){				
					case "0";
                     $this->deposit($player,"");					
					break;	
					case "1";				
					 $this->withdraw($player,"");
					break;
					case "2";		
					$this->profit($player,"");		
					break;
					default:
					break;
			}
			});	
			$mon = $api2->myMoney($player);		
			$n = $player->getName();
			$money = $this->getConfig()->getNested("bank.$n.money"); 
			$profit = $this->getConfig()->getNested("bank.$n.profit"); 
			$rate2 = round($this->getPlayerBankMoney($player)*$this->rate,6);
			$rate = round(604800 * $this->rate,6)*100;
			$form->setTitle("§l§b♥ §aMineBank §b♥");
			$a = " Profit Per Week";
			$form->setContent(
				"$err".
				"§l§a• §bYour Money: §e$mon §a\n". 
				"§l§a• §bMoney In Bank: §e$money  \n".
				"§l§a• §bProfit: §e$profit \n".
				"§l§a• §bRate: §a".$rate."$a\n". 
				"§l§a• §bProfit Per Seconds: §a$rate2"
			);
			$form->addButton("§eSend Money",0,"textures/ui/generic_face_button_down");
			$form->addButton("§eWithdrawal",0,"textures/ui/generic_face_button_right");
			$form->addButton("§eWithdrawal of Interest",0,"textures/ui/generic_face_button_down");
			$form->addButton("§eExit!",0,"textures/ui/permissions_visitor_hand_hover");
			$form->sendToPlayer($player);
			return $form;
	   
   }
}