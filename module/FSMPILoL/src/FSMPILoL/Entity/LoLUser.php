<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace FSMPILoL\Entity;

use SkelletonApplication\Entity\User;
use Doctrine\ORM\Mapping as ORM;


/**
 * Adds players
 * @author schurix
 * 
 * @ORM\Entity(repositoryClass="FSMPILoL\Model\UserRepository")
 */
class LoLUser extends User{
	/**
	 * @ORM\OneToMany(targetEntity="Player", mappedBy="user")
	 */
	protected $players;
	
	/**
	 * Init collections
	 */
	public function __construct() {
		parent::__construct();
		$this->players = new ArrayCollection();
	}
	
	/**
	 * Returns player for given tournament
	 * 
	 * @param Tournament $tournament
	 * @return Player
	 */
	public function getPlayer(Tournament $tournament){
		foreach($this->players as $player){
			/* @var $player Player */
			if($player->getAnmeldung()->getTournament() == $tournament){
				return $player;
			}
		}
		return null;
	}
	
	/**
	 * @return Player
	 */
	public function getMostRecentPlayer(){
		$maxId = 0;
		$found = null;
		foreach($this->players as $player){
			/* @var $player Player */
			if($player->getAnmeldung()->getTournament()->getId() >= $maxId){
				$found = $player;
			}
		}
		return $found;
	}
	
	
}
