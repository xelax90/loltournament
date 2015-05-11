<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace FSMPILoL\Service\Tournament;

use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use FSMPILoL\Entity\User;
use FSMPILoL\Entity\Role;
use FSMPILoL\Entity\Team;
use FSMPILoL\Entity\Tournament;

/**
 * Permission handling for Tournaments
 */
class Permission implements ServiceLocatorAwareInterface {
	protected $ressources = array();

	/**
	 * @var TournamentPermissionHelper
	 */
	protected static $instance;
	
	/**
	 * @var ServiceLocatorInterface
	 */
	protected $serviceLocator;
	
	public function __invoke(){
		if (null === self::$instance) {
			self::$instance = new self();
		}
		return self::$instance;
	}
	
	/**
	 * 
	 * @param User $user
	 * @param Team|Tournament $team
	 */
	protected function sameTournament($user, $team){
		if($team instanceof Tournament){
			$tournament = $team;
		} else {
			$tournament = $team->getGroup()->getTournament();
		}
		$player = $user->getPlayer($tournament);
		return empty($player);
	}
	
	/**
	 * Checks if a user has the given role.
	 * @param User $user
	 * @param string $search
	 * @return boolean
	 */
	protected function userHasRole($user, $search){
		if(empty($user)){
			return $search == 'guest';
		}
		$roles = $user->getRoles();
		foreach($roles as $role){
			if($this->dfsRole($role, $search)){
				return true;
			}
		}
		return false;
	}
	
	/**
	 * Checks if $role matches $search or if $search is a parent of $role;
	 * @param Role $role
	 * @param string $search
	 * @return boolean
	 */
	protected function dfsRole($role, $search){
		if($role->getRoleId() == $search){
			return true;
		} elseif(!empty($role->getParent())){
			return $this->dfsRole($role->getParent(), $search);
		}
		return false;
	}
	
	/**
	 * Creates basic ressources
	 */
	public function __construct(){
		$that = $this;
		$this->addRessource('viewCaptain', function($user, $team) use($that){
			if(!$that->sameTournament($user, $team)){
				return false;
			}
			return !$that->userHasRole($user, 'guest');
		});
		
		$this->addRessource('viewContacts', function($user, $team) use($that){
			if($that->userHasRole($user, 'administrator')){
				return true;
			}
			
			if(empty($team) || !$team instanceof Team){
				return false;
			}
			
			if($that->userHasRole($user, 'moderator') && $team->getAnsprechpartner() == $user){
				return true;
			}
			
			if($that->userHasRole($user, 'guest')){
				return false;
			}
			
			if(!$that->sameTournament($user, $team)){
				return false;
			}
			
			$player = $user->getPlayer($team->getGroup()->getTournament());
			return $player->getTeam() == $team;
		});
		
		$this->addRessource('viewSubContacts', function($user, $team) use($that){
			if($that->userHasRole($user, 'moderator')){
				return true;
			}
			
			if($that->userHasRole($user, 'guest')){
				return false;
			}
			
			return $that->sameTournament($user, $team);
		});
		
		$this->addRessource('viewAnsprechpartner', function($user, $team) use($that){
			return $that->userHasRole($user, 'moderator');
		});
		
		$this->addRessource('edit', function($user, $team) use($that){
			if($that->userHasRole($user, 'administrator')){
				return true;
			}
			
			if(empty($team) || !$team instanceof Team){
				return false;
			}
			
			return $that->userHasRole($user, 'moderator') && $team->getAnsprechpartner() == $user;
		});
	}
	
	/**
	 * Check if current user is allowed to access $ressource for $team
	 * @param string $ressource
	 * @param Team $team Team to check for. If empty, returns access for all teams.
	 * @return boolean
	 */
	public function isAllowed($ressource, Team $team = null) {
		if(empty($this->ressources[$ressource])){
			return false;
		}
		
		$auth = $this->getServiceLocator()->get('zfcuser_auth_service');
		$identity = $auth->getIdentity();
		
		return call_user_func($this->ressources[$ressource], $identity, $team);
	}
	
	/**
	 * @return ServiceLocatorInterface
	 */
	public function getServiceLocator() {
		return $this->serviceLocator;
	}

	/**
	 * @param ServiceLocatorInterface $serviceLocator
	 */
	public function setServiceLocator(ServiceLocatorInterface $serviceLocator) {
		$this->serviceLocator = $serviceLocator;
	}
	
	/**
	 * Adds a new ressource
	 * 
	 * @param string $ressource Name of ressource
	 * @param callable $check Function with 2 arguments: The user and the team or tournament. Returns true if allowed, false otherwise. Both can be null. If team is null, give result for all teams.
	 */
	public function addRessource($ressource, callable $check ){
		$this->ressources[$ressource] = $check;
	}
	
	/**
	 * Removes ressource
	 * 
	 * @param string $ressource Name of ressource
	 */
	public function removeRessource($ressource){
		if(isset($this->ressources[$ressource])){
			unset($this->ressources[$ressource]);
		}
	}
}
