<?php
declare(strict_types=1);

/**
 * ___  ___          _                  _____           _
 * |  \/  |         | |                /  __ \         | |
 * | .  . |_   _ ___| |_ ___ _ __ _   _| /  \/_ __ __ _| |_ ___
 * | |\/| | | | / __| __/ _ \ '__| | | | |   | '__/ _` | __/ _ \
 * | |  | | |_| \__ \ ||  __/ |  | |_| | \__/\ | | (_| | ||  __/
 * \_|  |_/\__, |___/\__\___|_|   \__, |\____/_|  \__,_|\__\___|
 *          __/ |                  __/ |
 *         |___/                  |___/  By @JackMD for PMMP
 *
 * MysteryCrate, a Crate plugin for PocketMine-MP
 * Copyright (c) 2018 JackMD  < https://github.com/JackMD >
 *
 * Discord: JackMD#3717
 * Twitter: JackMTaylor_
 *
 * This software is distributed under "GNU General Public License v3.0".
 * This license allows you to use it and/or modify it but you are not at
 * all allowed to sell this plugin at any cost. If found doing so the
 * necessary action required would be taken.
 *
 * MysteryCrate is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License v3.0 for more details.
 *
 * You should have received a copy of the GNU General Public License v3.0
 * along with this program. If not, see
 * <https://opensource.org/licenses/GPL-3.0>.
 * ------------------------------------------------------------------------
 */

namespace JackMD\MysteryCrate;

use DaPigGuy\PiggyCustomEnchants\CustomEnchants\CustomEnchants;
use DaPigGuy\PiggyCustomEnchants\Main as CE;
use JackMD\MysteryCrate\Task\PutChest;
use JackMD\MysteryCrate\Task\RemoveChest;
use JackMD\MysteryCrate\Utils\Lang;
use pocketmine\command\ConsoleCommandSender;
use pocketmine\item\enchantment\Enchantment;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\item\Item;
use pocketmine\level\sound\ClickSound;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\StringTag;
use pocketmine\Player;
use pocketmine\scheduler\Task;
use pocketmine\tile\Chest as ChestTile;

class UpdaterEvent extends Task{

	/** @var Main */
	private $plugin;
	/** @var bool */
	private $canTakeItem = false;
	/** @var int */
	private $t_delay = 2 * 20;
	/** @var Player */
	private $player;
	/** @var ChestTile */
	private  $chestTile;

	public function __construct(Main $plugin, Player $player, ChestTile $chestTile, int $t_delay){
		$this->plugin = $plugin;
		$this->player = $player;
		$this->chestTile = $chestTile;
		$this->t_delay = $t_delay;
	}

	/**
	 * @param int $timer
	 */
	public function onRun(int $timer){
		$t_delay = $this->getTDelay();
		$chestTile = $this->chestTile;
		$player = $this->player;
		if((is_null($chestTile)) || (is_null($player))){
			return;
		}
		if(($chestTile instanceof ChestTile) && ($player instanceof Player) && ($player->isOnline())){
			$this->t_delay--;
			if($t_delay >= 0){
				$i = 0;
				while($i < 27){
					if($i != 4 && $i != 10 && $i != 11 && $i != 12 && $i != 13 && $i != 14 && $i != 15 && $i != 16 && $i != 22){
						$this->setItemINT($i, 106, 1);
					}
					$i++;
				}
				$this->setItemINT(4, 208, 1);
				$this->setItemINT(22, 208, 1);
				$block = $this->chestTile->getBlock();
				$block->getLevel()->addSound(new ClickSound($block), [$player]);
				$b = $block->getLevel()->getBlock($block->subtract(0, 1));
				$type = $this->plugin->isCrateBlock($b->getId(), $b->getDamage());
				$drops = array_rand($this->plugin->getCrateDrops($type), 1);
				if(!is_array($drops)){
					$drops = [$drops];
				}
				foreach($drops as $drop){
					$values = $this->plugin->getCrateDrops($type)[$drop];;
					$i = Item::get(($values["id"]), $values["meta"], $values["amount"]);
					$i->setCustomName($values["name"]);
					if(isset($values["enchantments"])){
						foreach($values["enchantments"] as $enchantment => $enchantmentinfo){
							$level = $enchantmentinfo["level"];
							$ce = $this->plugin->getServer()->getPluginManager()->getPlugin("PiggyCustomEnchants");
							if(!is_null($ce) && !is_null($enchant = CustomEnchants::getEnchantmentByName($enchantment))){
								if($ce instanceof CE){
									$i = $ce->addEnchantment($i, $enchantment, $level);
								}
							}else{
								if(!is_null($enchant = Enchantment::getEnchantmentByName($enchantment))){
									$i->addEnchantment(new EnchantmentInstance($enchant, $level));
								}
							}
						}
					}
					if(isset($values["lore"])){
						$i->setLore([$values["lore"]]);
					}
					if(isset($values["commands"])){
						foreach($values["commands"] as $index => $cmd){
							$nbt = $i->getNamedTag() ?? new CompoundTag("", []);
							$cmd = str_replace(["%PLAYER%"], [$player->getName()], $cmd);
							$nbt->setString((string) $index, $cmd);
							$i->setNamedTag($nbt);
						}
					}
					$cInv = $chestTile->getInventory();
					$this->setItem(10, $cInv->getItem(11), $cInv->getItem(11)->getCount(), $cInv->getItem(11)->getDamage());
					$this->setItem(11, $cInv->getItem(12), $cInv->getItem(12)->getCount(), $cInv->getItem(12)->getDamage());
					$this->setItem(12, $cInv->getItem(13), $cInv->getItem(13)->getCount(), $cInv->getItem(13)->getDamage());
					$this->setItem(13, $cInv->getItem(14), $cInv->getItem(14)->getCount(), $cInv->getItem(14)->getDamage());//reward
					$this->setItem(14, $cInv->getItem(15), $cInv->getItem(15)->getCount(), $cInv->getItem(15)->getDamage());
					$this->setItem(15, $cInv->getItem(16), $cInv->getItem(16)->getCount(), $cInv->getItem(16)->getDamage());
					$this->setItem(16, $i, $i->getCount(), $i->getDamage());
				}
			}
			if($t_delay == -1){
				$this->setItemINT(10, 0, 0);
				$this->setItemINT(11, 0, 0);
				$this->setItemINT(12, 0, 0);
				$this->setItemINT(14, 0, 0);
				$this->setItemINT(15, 0, 1);
				$this->setItemINT(16, 0, 1);
				$this->setCanTakeItem(true);
				$slot13 = $chestTile->getInventory()->getItem(13);
				$block = $this->chestTile->getBlock();
				$b = $block->getLevel()->getBlock($block->subtract(0, 1));
				$type = $this->plugin->isCrateBlock($b->getId(), $b->getDamage());
				if($player->isOnline()){
					if($slot13->getDamage() === $this->plugin->getConfig()->get("commandMeta")){
						$nbt = $slot13->getNamedTag();
						for($i = 0; $i < $this->plugin->getConfig()->get("maxCommands"); $i++){
							if($nbt->hasTag((string) $i, StringTag::class)){
								$cmd = $nbt->getString((string) $i);
								$this->plugin->getServer()->dispatchCommand(new ConsoleCommandSender(), $cmd);
							}
						}
					}else{
						$player->getInventory()->addItem($slot13);
						$win_message = str_replace(["%REWARD%", "%COUNT%", "%CRATE%"], [
							$slot13->getName(),
							$slot13->getCount(),
							ucfirst($type)
						], Lang::$win_message);
						$player->sendMessage($win_message);
					}
				}
				$dmg = $block->getDamage();
				$this->plugin->getScheduler()->scheduleDelayedTask(new RemoveChest($this->plugin, $block), 20);
				$this->plugin->getScheduler()->scheduleDelayedTask(new PutChest($this->plugin, $block, $dmg), 24);
				$this->plugin->getScheduler()->cancelTask($this->getTaskId());
			}
		}
	}

	/**
	 * @return int
	 */
	public function getTDelay(): int{
		return $this->t_delay;
	}

	/**
	 * @param     $index
	 * @param int $id
	 * @param     $count
	 * @param int $dmg
	 */
	public function setItemINT($index, int $id, $count, $dmg = 0){
		$item = Item::get($id);
		$item->setCount($count);
		$item->setDamage($dmg);

		$chestTile = $this->chestTile;
		if($chestTile instanceof ChestTile){
			$chestTile->getInventory()->setItem($index, $item);
		}
	}

	/**
	 * @param      $index
	 * @param Item $item
	 * @param      $count
	 * @param int  $dmg
	 */
	public function setItem($index, Item $item, $count, $dmg = 0){
		$item->setCount($count);
		$item->setDamage($dmg);

		$chestTile = $this->chestTile;
		if($chestTile instanceof ChestTile){
			$chestTile->getInventory()->setItem($index, $item);
		}
	}

	/**
	 * @return bool
	 */
	public function isCanTakeItem(): bool{
		return $this->canTakeItem;
	}

	/**
	 * @param bool $canTakeItem
	 */
	public function setCanTakeItem(bool $canTakeItem){
		$this->canTakeItem = $canTakeItem;
	}
}
