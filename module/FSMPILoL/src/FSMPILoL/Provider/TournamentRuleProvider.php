<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace FSMPILoL\Provider;

use BjyAuthorize\Provider\Rule\ProviderInterface;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;
use FSMPILoL\Tournament\TournamentAwareInterface;
use FSMPILoL\Tournament\TournamentAwareTrait;
use FSMPILoL\Options\TournamentOptions;


/**
 * Description of TournamentRuleProvider
 *
 * @author schurix
 */
class TournamentRuleProvider implements ProviderInterface, TournamentAwareInterface{
	use TournamentAwareTrait;
	
	public function getRules() {
		$config = $this->getTournament()->getConfig();
		
		$allow = array('info', 'kontakt');
		switch($config->getTournamentPhase()){
			case TournamentOptions::TOURNAMENT_PHASE_ANNOUNCED:
			case TournamentOptions::TOURNAMENT_PHASE_REGISTRATION:
				$allow = array_merge($allow, array('home', 'anmeldung', 'teilnehmer'));
				break;
			case TournamentOptions::TOURNAMENT_PHASE_PRE_ROUND:
			case TournamentOptions::TOURNAMENT_PHASE_MAIN_ROUND:
			case TournamentOptions::TOURNAMENT_PHASE_PLAYOFFS:
			default:
				$allow = array_merge($allow, array('ergebnisse', 'paarungen', 'teams', 'myteam', 'meldung'));
				break;
		}
		
		$rules = array();
		foreach($allow as $privilege){
			$rules[] = [['user', 'guest'], 'tournament', 'navigation/'.$privilege];
		}
		return array('allow' => $rules, 'deny' => array());
	}
}
