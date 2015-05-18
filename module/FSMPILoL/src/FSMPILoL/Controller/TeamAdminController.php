<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace FSMPILoL\Controller;

use Zend\View\Model\ViewModel;
use FSMPILoL\Form\CommentPaarungForm;
use FSMPILoL\Form\WarningForm;
use FSMPILoL\Entity\Team;
use FSMPILoL\Entity\Warning;
use FSMPILoL\Form\AddSubToTeamForm;
use FSMPILoL\Form\TeamForm;

/**
 * Description of TeamAdminController
 *
 * @author schurix
 */
class TeamAdminController extends AbstractTournamentAdminController{
	
	public function indexAction() {
		$tournament = $this->getTournament();
		if(!$tournament){
			return new ViewModel();
		}
		$this->setTeamdata();
		$this->setAPIData();
		
		return new ViewModel(array('tournament' => $tournament, 'identity' => $this->zfcUserAuthentication()->getIdentity()));
	}
	
	protected function _redirectToTeams(){
		return $this->redirect()->toRoute('zfcadmin/teams');
	}
	
	public function anmerkungAction(){
		$tournament = $this->getTournament();
		if(!$tournament){
			return $this->_redirectToTeams();
		}
        $team_id = $this->getEvent()->getRouteMatch()->getParam('team_id');
		$em = $this->getEntityManager();
		/* @var $team \FSMPILoL\Entity\Team */
		$team = $em->getRepository('FSMPILoL\Entity\Team')->find((int)$team_id);
		if(!$team){
			return $this->_redirectToTeams();
		}
		$form = new CommentPaarungForm();
		
        /** @var $request \Zend\Http\Request */
        $request = $this->getRequest();
		if ($request->isPost()) {
			$form->setData($request->getPost());
			if ($form->isValid()) {
				$data = $form->getData();
				$team->setAnmerkung($data['anmerkung']);
				$em->flush();
				return $this->_redirectToTeams();
			}
        }
		return new ViewModel(array('id' => $team->getId(), 'form' => $form));
	}
	
	public function blockAction(){
		$tournament = $this->getTournament();
		if(!$tournament){
			return $this->_redirectToTeams();
		}
        $team_id = $this->getEvent()->getRouteMatch()->getParam('team_id');
		$em = $this->getEntityManager();
		/* @var $team \FSMPILoL\Entity\Team */
		$team = $em->getRepository('FSMPILoL\Entity\Team')->find((int)$team_id);
		if(!$team){
			return $this->_redirectToTeams();
		}
		
		if($this->fsmpiLoLTournamentPermission()->isAllowed('edit', $team)){
			$team->setIsBlocked(true);
			$em->flush();
		}
		return $this->_redirectToTeams();
	}
	
	public function unblockAction(){
		$tournament = $this->getTournament();
		if(!$tournament){
			return $this->_redirectToTeams();
		}
        $team_id = $this->getEvent()->getRouteMatch()->getParam('team_id');
		$em = $this->getEntityManager();
		/* @var $team \FSMPILoL\Entity\Team */
		$team = $em->getRepository('FSMPILoL\Entity\Team')->find((int)$team_id);
		if(!$team){
			return $this->_redirectToTeams();
		}
		
		if($this->fsmpiLoLTournamentPermission()->isAllowed('edit', $team)){
			$team->setIsBlocked(false);
			$em->flush();
		}
		return $this->_redirectToTeams();
	}
	
	public function warnAction(){
		$tournament = $this->getTournament();
		if(!$tournament){
			return $this->_redirectToTeams();
		}
        $team_id = $this->getEvent()->getRouteMatch()->getParam('team_id');
		$em = $this->getEntityManager();
		/* @var $team \FSMPILoL\Entity\Team */
		$team = $em->getRepository('FSMPILoL\Entity\Team')->find((int)$team_id);
		if(!$team){
			return $this->_redirectToTeams();
		}
		$form = new WarningForm();
		
        /** @var $request \Zend\Http\Request */
        $request = $this->getRequest();
		if ($request->isPost() && $this->fsmpiLoLTournamentPermission()->isAllowed('edit', $team)) {
			$form->setData($request->getPost());
			if ($form->isValid()) {
				$data = $form->getData();
				$warning = new Warning();
				$warning->setTeam($team);
				$warning->setComment($data['comment']);
				$em->persist($warning);
				$em->flush();
				return $this->_redirectToTeams();
			}
        }
		return new ViewModel(array('team' => $team, 'form' => $form));
	}
	
	public function warnPlayerAction(){
		$tournament = $this->getTournament();
		if(!$tournament){
			return $this->_redirectToTeams();
		}
        $player_id = $this->getEvent()->getRouteMatch()->getParam('player_id');
		$em = $this->getEntityManager();
		/* @var $team \FSMPILoL\Entity\Player */
		$player = $em->getRepository('FSMPILoL\Entity\Player')->find((int)$player_id);
		if(!$player){
			return $this->_redirectToTeams();
		}
		$form = new WarningForm();
		
        /** @var $request \Zend\Http\Request */
        $request = $this->getRequest();
		if ($request->isPost() && $this->fsmpiLoLTournamentPermission()->isAllowed('edit', $player->getTeam())) {
			$form->setData($request->getPost());
			if ($form->isValid()) {
				$data = $form->getData();
				$warning = new Warning();
				$warning->setPlayer($player);
				$warning->setComment($data['comment']);
				$em->persist($warning);
				$em->flush();
				return $this->_redirectToTeams();
			}
        }
		return new ViewModel(array('player' => $player, 'form' => $form));
	}
	
	public function deleteWarningAction(){
		$tournament = $this->getTournament();
		if(!$tournament){
			return $this->_redirectToTeams();
		}
        $warning_id = $this->getEvent()->getRouteMatch()->getParam('warning_id');
		$em = $this->getEntityManager();
		/* @var $warning \FSMPILoL\Entity\Warning */
		$warning = $em->getRepository('FSMPILoL\Entity\Warning')->find((int)$warning_id);
		if(!$warning){
			return $this->_redirectToTeams();
		}
		if($this->fsmpiLoLTournamentPermission()->isAllowed('edit', $warning->getTeam())){
			$em->remove($warning);
			$em->flush();
		}
		return $this->_redirectToTeams();
	}
	
	protected $addsubSuccessFormat = 'Spieler %s erfolgreich zu Team %s hinzugefügt.';
	protected $playerNotFoundFormat = 'Spieler %s nicht gefunden.';
	protected $teamNotFoundFormat = 'Team %s nicht gefunden.';
	protected $playerNotInTeamFormat = 'Spieler %s ist nicht im Team %s.';
	protected $cannotEditTeamFormat = 'Du darfst das Team %s nicht bearbeiten';
	protected $makeSubSuccessFormat = 'Der Spieler %s ist jetzt ein Ersatzspieler';
	
	public function addsubAction(){
		$tournament = $this->getTournament();
		if(!$tournament){
			return $this->_redirectToTeams();
		}
        $team_id = $this->getEvent()->getRouteMatch()->getParam('team_id');
		$em = $this->getEntityManager();
		/* @var $team \FSMPILoL\Entity\Team */
		$team = $em->getRepository('FSMPILoL\Entity\Team')->find((int)$team_id);
		if(!$team){
			return $this->_redirectToTeams();
		}
		/* @var $form \FSMPILoL\Form\AddSubToTeamForm */
		$form = $this->getServiceLocator()->get('FormElementManager')->get(AddSubToTeamForm::class);
		$data = array(
			'team' => $team->getId()
		);
		$form->setData($data);
		$form->get('team')->setAttribute('disabled', 'disabled');
		
        /** @var $request \Zend\Http\Request */
        $request = $this->getRequest();
		if ($request->isPost()) {
			$data = $request->getPost();
			$data['team'] = $team->getId();
			$form->setData($data);
			if ($form->isValid()) {
				$data = $form->getData();
				$player = $em->getRepository('FSMPILoL\Entity\Player')->find((int)$data['player']);
				if(empty($player)){
					$form->get('player')->setMessages(array(
						'Spieler nicht gefunden'
					));
				} elseif(!empty($player->getTeam())){
					$form->get('player')->setMessages(array(
						'Spieler ist kein Ersatzspieler'
					));
				} else {
					$player->setTeam($team);
					$em->flush();
					$this->flashMessenger()->addSuccessMessage(sprintf($this->addsubSuccessFormat, $player->getAnmeldung()->getSummonerName(), $team->getName()));
					return $this->_redirectToTeams();
				}
			}
        }
		return new ViewModel(array('id' => $team->getId(), 'form' => $form));
	}
	
	public function makesubAction(){
		$tournament = $this->getTournament();
		if(!$tournament){
			return $this->_redirectToTeams();
		}
        $team_id = $this->getEvent()->getRouteMatch()->getParam('team_id');
		$em = $this->getEntityManager();
		/* @var $team \FSMPILoL\Entity\Team */
		$team = $em->getRepository('FSMPILoL\Entity\Team')->find((int)$team_id);
		if(!$team){
			$this->flashMessenger()->addErrorMessage(sprintf($this->teamNotFoundFormat, $team_id));
			return $this->_redirectToTeams();
		}

        $player_id = $this->getEvent()->getRouteMatch()->getParam('player_id');
		$player = $em->getRepository('FSMPILoL\Entity\Player')->find((int)$player_id);
		if(!$player){
			$this->flashMessenger()->addErrorMessage(sprintf($this->playerNotFoundFormat, $player_id));
			return $this->_redirectToTeams();
		}
		
		if($player->getTeam() != $team){
			$this->flashMessenger()->addErrorMessage(sprintf($this->playerNotInTeamFormat, $player->getAnmeldung()->getSummonerName(), $team->getName()));
			return $this->_redirectToTeams();
		}
		
		if(!$this->fsmpiLoLTournamentPermission()->isAllowed('edit', $team)){
			$this->flashMessenger()->addErrorMessage(sprintf($this->cannotEditTeamFormat, $team->getName()));
			return $this->_redirectToTeams();
		}
		
		$player->setTeam(null);
		$em->flush();
		$this->flashMessenger()->addSuccessMessage(sprintf($this->makeSubSuccessFormat, $player->getAnmeldung()->getSummonerName()));
		return $this->_redirectToTeams();
	}
	
	function createAction() {
		$form = $this->getServiceLocator()->get('FormElementManager')->get(TeamForm::class);
 		$em = $this->getEntityManager();
		$request = $this->getRequest();
		
        /** @var $request \Zend\Http\Request */
        if ($request->isPost()) {
			$item = new Team();
			$data = $request->getPost();
	        $form->bind($item);
	        $form->setData($data);
			if ($form->isValid()) {
				$em->persist($item);
				$em->flush();
				$this->flashMessenger()->addSuccessMessage('The Team was created');
				return $this->_redirectToTeams();
			}
        }
		
		$params = array(
			'form' => $form,
		);
		
		return new ViewModel($params);
	}
	
	
	function editAction() {
		$id = $this->getEvent()->getRouteMatch()->getParam('team_id');
		$em = $this->getEntityManager();
		$item = $em->getRepository(Team::class)->find((int)$id);
		if(empty($item)){
			$this->flashMessenger()->addErrorMessage(sprintf($this->teamNotFoundFormat, $id));
			$this->_redirectToTeams();
		}
		$form = $this->getServiceLocator()->get('FormElementManager')->get(TeamForm::class);
		
		$form->setBindOnValidate(false);
		$form->bind($item);
		
        /** @var $request \Zend\Http\Request */
        $request = $this->getRequest();
		if ($request->isPost()) {
			$data = $request->getPost();
			$form->setData($data);
			if ($form->isValid()) {
				$form->bindValues();
				$em->flush();
				$this->flashMessenger()->addSuccessMessage('The Team was edited');
				return $this->_redirectToTeams();
			}
        }
		
		$params = array(
            'form' => $form,
		);
		return new ViewModel($params);
	}
	
}
