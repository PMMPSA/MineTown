<?php

/*
 * PointS, the massive point plugin with many features for PocketMine-MP
 * Copyright (C) 2013-2017  onebone <jyc00410@gmail.com>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace onebone\rebirthcoinapi\provider;


use onebone\rebirthcoinapi\RebirthCoinAPI;
use pocketmine\Player;
use pocketmine\utils\Config;

class YamlProvider implements Provider{
	/**
	 * @var Config
	 */
	private $config;

	/** @var RebirthCoinAPI */
	private $plugin;

	private $rebirthcoin = [];

	public function __construct(RebirthCoinAPI $plugin){
		$this->plugin = $plugin;
	}

	public function open(){
		$this->config = new Config($this->plugin->getDataFolder() . "RebirthCoin.yml", Config::YAML, ["version" => 2, "rebirthcoin" => []]);
		$this->rebirthcoin = $this->config->getAll();
	}

	public function accountExists($player){
		if($player instanceof Player){
			$player = $player->getName();
		}
		$player = strtolower($player);

		return isset($this->rebirthcoin["rebirthcoin"][$player]);
	}

	public function createAccount($player, $defaultRebirthCoin = 1000){
		if($player instanceof Player){
			$player = $player->getName();
		}
		$player = strtolower($player);

		if(!isset($this->rebirthcoin["rebirthcoin"][$player])){
			$this->rebirthcoin["rebirthcoin"][$player] = $defaultRebirthCoin;
			return true;
		}
		return false;
	}

	public function removeAccount($player){
		if($player instanceof Player){
			$player = $player->getName();
		}
		$player = strtolower($player);

		if(isset($this->rebirthcoin["rebirthcoin"][$player])){
			unset($this->rebirthcoin["rebirthcoin"][$player]);
			return true;
		}
		return false;
	}

	public function getRebirthCoin($player){
		if($player instanceof Player){
			$player = $player->getName();
		}
		$player = strtolower($player);

		if(isset($this->rebirthcoin["rebirthcoin"][$player])){
			return $this->rebirthcoin["rebirthcoin"][$player];
		}
		return false;
	}

	public function setRebirthCoin($player, $amount){
		if($player instanceof Player){
			$player = $player->getName();
		}
		$player = strtolower($player);

		if(isset($this->rebirthcoin["rebirthcoin"][$player])){
			$this->rebirthcoin["rebirthcoin"][$player] = $amount;
			$this->rebirthcoin["rebirthcoin"][$player] = round($this->rebirthcoin["rebirthcoin"][$player], 2);
			return true;
		}
		return false;
	}

	public function addRebirthCoin($player, $amount){
		if($player instanceof Player){
			$player = $player->getName();
		}
		$player = strtolower($player);

		if(isset($this->rebirthcoin["rebirthcoin"][$player])){
			$this->rebirthcoin["rebirthcoin"][$player] += $amount;
			$this->rebirthcoin["rebirthcoin"][$player] = round($this->rebirthcoin["rebirthcoin"][$player], 2);
			return true;
		}
		return false;
	}

	public function reduceRebirthCoin($player, $amount){
		if($player instanceof Player){
			$player = $player->getName();
		}
		$player = strtolower($player);

		if(isset($this->rebirthcoin["rebirthcoin"][$player])){
			$this->rebirthcoin["rebirthcoin"][$player] -= $amount;
			$this->rebirthcoin["rebirthcoin"][$player] = round($this->rebirthcoin["rebirthcoin"][$player], 2);
			return true;
		}
		return false;
	}

	public function getAll(){
		return isset($this->rebirthcoin["rebirthcoin"]) ? $this->rebirthcoin["rebirthcoin"] : [];
	}

	public function save(){
		$this->config->setAll($this->rebirthcoin);
		$this->config->save();
	}

	public function close(){
		$this->save();
	}

	public function getName(){
		return "Yaml";
	}
}
