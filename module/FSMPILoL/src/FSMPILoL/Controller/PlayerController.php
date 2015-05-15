<?php

/*
 * Copyright (C) 2015 schurix
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 */

namespace FSMPILoL\Controller;

use XelaxAdmin\Controller\ListController;
use ReflectionClass;

/**
 * Description of PlayerController
 *
 * @author schurix
 */
class PlayerController extends ListController {
	protected $tournament;
	protected $team;
	protected $userService;
	
	public function __construct() {
	}
	
	protected function _showList($params) {
		$team = $this->getTeam();
		if(empty($team)){
			$params['title'] = 'Ersatzspieler';
		} else {
			$params['title'] = $team->getName();
		}
		
		return parent::_showList($params);
	}
	
	public function getTeam(){
		if(null === $this->team){
			$teamId = $this->getEvent()->getRouteMatch()->getParam('team_id');
			if(!empty($teamId)){
				$em = $this->getEntityManager();
				$this->team = $em->getRepository('FSMPILoL\Entity\Team')->find($teamId);
			}
		}
		return $this->team;
	}
	
	public function getTournament(){
		if(null === $this->tournament){
			$options = $this->getServiceLocator()->get('FSMPILoL\Options\Anmeldung');
			$tournamentId = $options->getTournamentId();
			$em = $this->getEntityManager();
			$this->tournament = $em->getRepository('FSMPILoL\Entity\Tournament')->find($tournamentId);
		}
		return $this->tournament;
	}
	
	protected function getPlayerClass(){
		return 'FSMPILoL\Entity\Player';
	}
	
	protected function getAll() {
		$em = $this->getEntityManager();
		$entityClass = $this->getPlayerClass();
		$items = $em->getRepository($entityClass)->findBy(array('team' => $this->getTeam()));
		
		$tournament = $this->getTournament();
		$res = array();
		foreach($items as $item){
			if($item->getAnmeldung()->getTournament() == $tournament){
				$res[] = $item;
			}
		}
		return $res;
	}
	
	public function createGetter($param) {
		$getter = parent::createGetter($param);
		if($param == $this->getOptions()->getAliasName() || $param == $this->getOptions()->getIdName()){
			return $getter;
		}
		
		$reflector = new ReflectionClass($this->getPlayerClass());
		if($reflector->hasMethod($getter) && $reflector->getMethod($getter)->isPublic()){
			return $getter;
		}
		return function($player) use ($getter){
			return call_user_func(array($player->getAnmeldung(), $getter));
		};
	}
	
	protected function _preCreate($item) {
		$item->getAnmeldung()->setTournament($this->getTournament());
		$item->getAnmeldung()->setIsSub(0);
		$em = $this->getEntityManager();
		$repo = $em->getRepository(get_class($this->zfcUserAuthentication()->getIdentity()));
		$user = $repo->findOneBy(array('email' => $item->getAnmeldug()->getEmail()));
		if(is_object($user)){
			$item->setUser($user);
		} else {
			/* @var $userService \ZfcUser\Service\User */
			$userService = $this->getUserService();
			$data = array(
				'email' => $item->getAnmeldung()->getEmail(),
				'username' => $item->getAnmeldung()->getEmail(),
				'displayName' => $item->getAnmeldung()->getName(),
				'password' => bin2hex(openssl_random_pseudo_bytes(20)), // TODO
			);
			$user = $userService->register($data);
			$item->setUser($user);
			// TODO
			mail($user->getEmail(), 'Anmeldung beim LoL Turnier', 'Hallo '.$user->getDisplayName().", \n\ndu bist nun auf der LoL Webseite registriert und dein Passwort lautet \n ".$data['password'].'. \n\nViele Grüße,\nDas LoL Team');
		}
	}

	public function getUserService()
	{
		if (null === $this->userService) {
			$this->userService = $this->getServiceLocator()->get('zfcuser_user_service');
		}
		return $this->userService;
	}

}
