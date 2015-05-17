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
use FSMPILoL\Form\PlayerForm;

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
	
	protected function getEditForm() {
		$frmCls = PlayerForm::class;
		$form = $this->getServiceLocator()->get('FormElementManager')->get($frmCls, array('forEdit' => true));
		return $form;
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
	
	protected function _showCreateForm($params) {
		/* @var $form \FSMPILoL\Form\PlayerForm */
		$form = $params['form'];
		if(!empty($this->getTeam())){
			$data = array(
				'player' => array(
					'team' => $this->getTeam()
				)
			);
			$form->setData($data);
			$form->get('player')->get('team')->setAttribute('disabled', 'disabled');
		}
		return parent::_showCreateForm($params);
	}
	
	protected function _preCreate($item) {
		$item->getAnmeldung()->setTournament($this->getTournament());
		$item->getAnmeldung()->setIsSub(0);
		
		if(!empty($this->getTeam())){
			$item->setTeam($this->getTeam());
		}
		
		/* @var $userService \ZfcUser\Service\User */
		$userService = $this->getUserService();
		$userMapper = $userService->getUserMapper();
		$user = $userMapper->findByEmail($item->getAnmeldung()->getEmail());
		if(is_object($user)){
			$item->setUser($user);
		} else {
			/* @var $pwGen \Hackzilla\PasswordGenerator\Generator\PasswordGeneratorInterface */
			$pwGen = $this->getServiceLocator()->get('XelaxPasswordGenerator\Default');
			$pw = $pwGen->generatePassword();
			$data = array(
				'email' => $item->getAnmeldung()->getEmail(),
				'username' => $item->getAnmeldung()->getEmail(),
				'display_name' => $item->getAnmeldung()->getName(),
				'password' => $pw,
				'passwordVerify' => $pw,
			);
			$user = $userService->register($data);
			$item->setUser($user);
			$this->flashMessenger()->addInfoMessage('Benutzer mit Email '.$data['email'].' und Passwort '.$pw.' wurde angelegt');
		}
	}

	public function getUserService()
	{
		if (null === $this->userService) {
			$this->userService = $this->getServiceLocator()->get('zfcuser_user_service');
		}
		return $this->userService;
	}
	
	public function buildRouteParams($action = 'list') {
		$params = parent::buildRouteParams($action);
		if(!empty($this->getTeam())){
			$params['team_id'] = $this->getTeam()->getId();
		}
		return $params;
	}

}
