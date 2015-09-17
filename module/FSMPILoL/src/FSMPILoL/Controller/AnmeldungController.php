<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace FSMPILoL\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use FSMPILoL\Form\AnmeldungSingleForm;
use FSMPILoL\Form\AnmeldungTeamForm;
use FSMPILoL\Entity\Anmeldung as AnmeldungEntity;
use FSMPILoL\Tournament\Anmeldung;

/**
 * Description of AnmeldungController
 *
 * @author schurix
 */
class AnmeldungController extends AbstractActionController
{
	protected $em;
	protected $tournament;
	protected $anmeldung;
	
	public function getEntityManager(){
		if (null === $this->em) {
			$this->em = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');
		}
		return $this->em;
	}
	
	public function getAnmeldung(){
		if(null === $this->anmeldung){
			$this->anmeldung = new Anmeldung($this->getTournament(), $this->getServiceLocator());
		}
		return $this->anmeldung;
	}
	
	/**
	 * 
	 * @return \FSMPILoL\Entity\Tournament
	 */
	public function getTournament(){
		if(null === $this->tournament){
			$options = $this->getServiceLocator()->get('FSMPILoL\Options\Anmeldung');
			$tournamentId = $options->getTournamentId();
			$em = $this->getEntityManager();
			$this->tournament = $em->getRepository('FSMPILoL\Entity\Tournament')->find($tournamentId);
		}
		return $this->tournament;
	}

	/**
	 * Creates form with name $name
	 * @param string $name
	 * @return \Zend\Form\Form
	 */
	protected function getForm($name){
		$form = $this->getServiceLocator()->get('FormElementManager')->get($name);
		return $form;
	}

	public function formAction(){
		$this->authenticate();
		$lastPlayer = null;
		if($this->zfcUserAuthentication()->hasIdentity()){
			/* @var $identity \FSMPILoL\Entity\User */
			$identity = $this->zfcUserAuthentication()->getIdentity();
			$lastPlayer = $identity->getMostRecentPlayer();
		}
		
		$request = $this->getRequest();
		$singleForm = $this->getForm('FSMPILoL\Form\AnmeldungSingleForm');
		$anmeldungEntity = new AnmeldungEntity();
		
		$teamForm = $this->getForm('FSMPILoL\Form\AnmeldungTeamForm');
		$teamForm->prepare();
		$anmeldungCollection = $teamForm->get('anmeldungen');
		/* @var $anmeldungCollection \Zend\Form\Element\Collection */
		$i = 1;
		foreach ($anmeldungCollection as $anmeldungFieldset) {
			$anmeldungFieldset->setLabel('Spieler '.$i);
			$i++;
		}
		
		$data = array();
		if($request->isPost()){
			$data = $request->getPost();
		}
		
		if($lastPlayer && (!$request->isPost() || (!isset($data['teamName']) && !isset($data['anmeldung'])))){
			$anmeldungEntity->setName($lastPlayer->getUser()->getDisplayName());
			$anmeldungEntity->setEmail($lastPlayer->getUser()->getEmail());
			$anmeldungEntity->setFacebook($lastPlayer->getAnmeldung()->getFacebook());
			$anmeldungEntity->setOtherContact($lastPlayer->getAnmeldung()->getOtherContact());
			$anmeldungEntity->setSummonerName($lastPlayer->getAnmeldung()->getSummonerName());
			
			$teamData = array();
			if(!empty($lastPlayer->getTeam())){
				foreach($lastPlayer->getTeam()->getPlayers() as $player){
					$teamData[] = array(
						'name' => $player->getUser()->getDisplayName(),
						'email' => $player->getUser()->getEmail(),
						'facebook' => $player->getAnmeldung()->getFacebook(),
						'otherContact' => $player->getAnmeldung()->getOtherContact(),
						'summonerName' => $player->getAnmeldung()->getSummonerName()
					);
				}
			}
			$teamForm->setData(array(
				'teamName' => $lastPlayer->getTeam()->getName(),
				'anmeldungen' => $teamData,
			));
		}
		$singleForm->bind($anmeldungEntity);
		
		
		$icons = $this->getAnmeldung()->getAvailableIcons();
		
		//$anmeldung = new AnmeldungEntity();
		//$form->bind($anmeldung);

		if ($request->isPost() && (isset($data['teamName']) || isset($data['anmeldung']))) {
			if(isset($data['teamName'])){ // team form
				$validation = $this->validateTeamAnmeldung($teamForm, $request->getPost());
				if (empty($validation)) {
					$match = $this->getEvent()->getRouteMatch()->getParams();
					$match['action'] = 'confirm';
					return $this->forward()->dispatch($match['controller'], $match);
				} elseif(is_array($validation)) {
					foreach($validation as $k => $valid){
						if($k != 'anmeldungen'){
							$teamForm->get($k)->setMessages(array_merge($teamForm->get($k)->getMessages(), array('teamvalidate' => $valid)));
						}
					}
				}
				
			} else { // single form
				$singleForm->setData($request->getPost());

				if ($singleForm->isValid()) {
					//var_dump($anmeldung);
				}
			}
		}
		
		$loginForm = $this->getServiceLocator()->get('zfcuser_login_form');
		$fm = $this->flashMessenger()->setNamespace('zfcuser-login-form')->getMessages();
		if (isset($fm[0])) {
			$loginForm->setMessages(
				array('identity' => array($fm[0]))
			);
		}
		
		return new ViewModel(array( 'singleForm' => $singleForm, 'teamForm' => $teamForm, 'icons' => $icons, 'loginForm' => $loginForm));
	}
	
	private function validateTeamAnmeldung(AnmeldungTeamForm $form, $data){
		/*if(NOTEAMS)
			return array('teamname' => "Teamanmeldung nicht mehr möglich");*/
		
		$validation = array();

		$turnierAnmeldungen = $this->getAnmeldung()->getAll();
		$summonerNames = array();
		$teamNames = array();
		foreach($turnierAnmeldungen as $turnierAnmeldung){
			$summonerNames[] = $turnierAnmeldung->getSummonerName();
			$teamNames[] = $turnierAnmeldung->getTeamName();
		}
		
		$form->setData($data);
		if(!$form->isValid()){
			return true;
		}
		$teamdata = $form->getData();
		
		if (empty($teamdata['team_icon_text'])) {
			$validation['team_icon_text'] = 'Team Icon nicht ausgesucht';
		} else {
			$icons = $this->getAnmeldung()->getAvailableIcons();
			if (!in_array($teamdata['team_icon_text'], $icons)) {
				$validation['team_icon_text'] = 'Team Icon bereits vergeben';
			}
		}

		if (empty($teamdata['teamName'])) {
			$validation['teamName'] = 'Team Name nicht angegeben';
		} else {
			if (in_array($teamdata['teamName'], $teamNames)) {
				$validation['teamName'] = 'Team Name bereits vergeben';
			}
		}

		$notRWTHCount = 0;
		
		$hasRWTH = false;
		$anmeldungen = $teamdata['anmeldungen'];
		$i = 0;
		foreach($anmeldungen as $k => $anmeldung){
			$anmeldungV = $this->validate_team_player($anmeldung, $i);
			if(!empty($anmeldungV)){
				if (empty($validation['anmeldungen'])) {
					$validation['anmeldungen'] = array();
				}
				$validation['anmeldungen'][$k] = $anmeldungV;
			}
			if (strpos(strtolower($anmeldung['email']), 'rwth-aachen') === false && strpos(strtolower($anmeldung['email']), 'fh-aachen') === false && !empty($anmeldung['email'])) {
				$notRWTHCount++;
			} elseif (!empty($anmeldung['email'])) {
				$hasRWTH = true;
			}
			$i++;
		}
		
		if($notRWTHCount > 3){
			if (!empty($validation['anmeldungen'][0]['email'])) {
				$validation['anmeldungen'][0]['email'] .= '<br>';
			} else {
				$validation['anmeldungen'][0]['email'] = '';
			}
			$validation['anmeldungen'][0]['email'] .= 'Es dürfen maximal zwei nicht-RWTH/FH Adressen angegeben sein';
		}
		if(!$hasRWTH){
			if (!empty($validation['anmeldungen'][0]['email'])) {
				$validation['anmeldungen'][0]['email'] .= '<br>';
			} else {
				$validation['anmeldungen'][0]['email'] = '';
			}
			$validation['anmeldungen'][0]['email'] .= 'Es muss mindestens ein RWTH/FH Angehöriger angegeben sein';
		}

		if (empty($teamdata['ausschreibung_gelesen'])) {
			$validation['ausschreibung_gelesen'] = 'Du musst die Ausschreibung gelesen haben.';
		}

		return $validation;
	}
	
	private function validate_team_player($player, $num){
		if (empty($player['name']) && $num > 1) {
			return array();
		}

		$validation = array();
		
		if (empty($player['name'])) {
			$validation['name'] = 'Name nicht angegeben';
		}

		if (empty($player['email'])) {
			$validation['email'] = 'Email nicht angegeben';
		} elseif (!filter_var($player['email'], FILTER_VALIDATE_EMAIL)) {
			$validation['email'] = 'Email nicht korrekt';
		}

		if(empty($player['summonerName'])){
			$validation['summonerName'] = 'Beschw&ouml;rername nicht angegeben';
		}
		else{
			$turnierAnmeldungen = $this->getAnmeldung()->getAll();
			$summonerNames = array();
			foreach($turnierAnmeldungen as $turnierAnmeldung){
				$summonerNames[] = $turnierAnmeldung->getSummonerName();
			}
			
			if(in_array($summonerNames, $summonerNames)){
				$validation['summonerName'] = 'Dieser Beschwörer ist bereits angemeldet';
			}
		}
		
		//if(empty($player['tier']))
		//	$validation[$prefix.'tier'] = 'Ranked Liga nicht angegeben';
		
		return $validation;
	}
	
	public function confirmAction(){
		$request = $this->getRequest();
		var_dump($request->getPost());
		return new ViewModel();
	}
	
	public function readyAction() {
		return new ViewModel();
	}
	
	protected function authenticate(){
		if($this->zfcUserAuthentication()->hasIdentity()){
			return true;
		}
		
        $adapter = $this->zfcUserAuthentication()->getAuthAdapter();
        $result = $adapter->prepareForAuthentication($this->getRequest());
        $auth = $this->zfcUserAuthentication()->getAuthService()->authenticate($adapter);
		return $auth;
	}
}
