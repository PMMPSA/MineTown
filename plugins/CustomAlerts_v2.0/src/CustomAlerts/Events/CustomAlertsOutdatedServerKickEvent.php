<?php

/*
 * CustomAlerts v2.0 by EvolSoft
 * Developer: Flavius12
 * Website: https://www.evolsoft.tk
 * Copyright (C) 2014-2018 EvolSoft
 * Licensed under MIT (https://github.com/EvolSoft/CustomAlerts/blob/master/LICENSE)
 */

namespace CustomAlerts\Events;

use pocketmine\Player;

class CustomAlertsOutdatedServerKickEvent extends CustomAlertsEvent {
	
	public static $handlerList = null;
	
	/** @var Player $player */
	private $player;
	
	/**
	 * @param Player $player
	 */
	public function __construct(Player $player){
		$this->player = $player;
	}

	/**
	 * Get outdated server kick event player
	 * 
	 * @return Player
	 */
	public function getPlayer() : Player {
		return $this->player;
	}
}