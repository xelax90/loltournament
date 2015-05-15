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
		$request = $this->getRequest();
		$singleForm = $this->getForm('FSMPILoL\Form\AnmeldungSingleForm');
		$anmeldungEntity = new AnmeldungEntity();
		$singleForm->bind($anmeldungEntity);
		
		$teamForm = $this->getForm('FSMPILoL\Form\AnmeldungTeamForm');
		$teamForm->prepare();
		$anmeldungCollection = $teamForm->get('anmeldungen');
		/* @var $anmeldungCollection \Zend\Form\Element\Collection */
		$i = 1;
		foreach ($anmeldungCollection as $anmeldungFieldset) {
			$anmeldungFieldset->setLabel('Spieler '.$i);
			$i++;
		}
		
		$icons = $this->getAnmeldung()->getAvailableIcons();
		
		//$anmeldung = new AnmeldungEntity();
		//$form->bind($anmeldung);

		if ($request->isPost()) {
			$data = $request->getPost();
			if(isset($data['teamName'])){ // team form
				$validation = $this->validateTeamAnmeldung($teamForm, $request->getPost());
				if (empty($validation)) {
					$teamdata = $teamForm->getData();
					var_dump($teamdata);
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

		return new ViewModel(array( 'singleForm' => $singleForm, 'teamForm' => $teamForm, 'icons' => $icons));
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

		if(empty($player['summonername'])){
			$validation['summonername'] = 'Beschw&ouml;rername nicht angegeben';
		}
		else{
			$turnierAnmeldungen = $this->getAnmeldung()->getAll();
			$summonerNames = array();
			foreach($turnierAnmeldungen as $turnierAnmeldung){
				$summonerNames[] = $turnierAnmeldung->getSummonerName();
			}
			
			if(in_array($anmeldung['summonerName'], $summonerNames)){
				$validation['summonername'] = 'Dieser Beschwörer ist bereits angemeldet';
			}
		}
		
		//if(empty($player['tier']))
		//	$validation[$prefix.'tier'] = 'Ranked Liga nicht angegeben';
		
		return $validation;
	}
	
	public function confirmAction(){
		return new ViewModel();
	}
	
	public function readyAction() {
		return new ViewModel();
	}
}
