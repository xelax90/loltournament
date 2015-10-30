<?php

namespace FSMPILoL\Form;

use Zend\Form\Form;
use Zend\InputFilter\InputFilter;
use DoctrineModule\Persistence\ObjectManagerAwareInterface;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject;
use Zend\InputFilter\InputFilterProviderInterface;
use FSMPILoL\Tournament\TournamentAwareInterface;
use FSMPILoL\Tournament\TournamentAwareTrait;
use DoctrineModule\Persistence\ProvidesObjectManager;

/**
 * Form for Players
 */
class AddSubToTeamForm extends Form implements ObjectManagerAwareInterface, InputFilterProviderInterface, TournamentAwareInterface{
	use ProvidesObjectManager, TournamentAwareTrait;
	
	public function __construct($name = null, $options = array()){
		// we want to ignore the name passed
		parent::__construct('addsub', $options);
		$this->setAttribute('method', 'post');
	}
	
	public function init() {
		$tournament = $this->getTournament()->getTournament();
		if(empty($tournament)){
			throw new \RuntimeException('Tournament cannot be empty');
		}
		
		$this->setHydrator(new DoctrineObject($this->getObjectManager()))
             ->setInputFilter(new InputFilter());
		
		$this->add(array(
			'name' => 'team',
			'type' => 'DoctrineModule\Form\Element\ObjectSelect',
			'options' => array(
				'object_manager' => $this->getObjectManager(),
				'target_class'   => 'FSMPILoL\Entity\Team',
				'label_generator' => function($team) {
					return $team->getName();
				},
				'find_method'    => array(
					'name'   => 'getTeamsForTournament',
					'params' => array(
						'tournament' => $tournament
					),
				),
				'display_empty_item' => true,
				'empty_item_label'   => '-- Ersatzspieler --',
				'optgroup_identifier' => 'groupName',
				'label' => 'Team',
				'column-size' => 'sm-10',
				'label_attributes' => array(
					'class' => 'col-sm-2',
				),
			),
			'attributes' => array(
				'id' => "",
			)
		));
		
		$this->add(array(
			'name' => 'player',
			'type' => 'DoctrineModule\Form\Element\ObjectSelect',
			'options' => array(
				'object_manager' => $this->getObjectManager(),
				'target_class'   => 'FSMPILoL\Entity\Player',
				'label_generator' => function($player) {
					return $player->getAnmeldung()->getSummonerName()." - ".$player->getAnmeldung()->getName();
				},
				'find_method'    => array(
					'name'   => 'getSubsForTournament',
					'params' => array(
						'tournament' => $tournament
					),
				),
				'display_empty_item' => true,
				'empty_item_label'   => '-- Ersatzspieler wählen --',
				'label' => 'Spieler',
				'column-size' => 'sm-10',
				'label_attributes' => array(
					'class' => 'col-sm-2',
				),
			),
			'attributes' => array(
				'id' => "",
			)
		));
		
		$this->add(array(
			'name' => 'submit',
			'type' => 'Submit',
			'attributes' => array(
				'value' => 'Speichern',
				'class' => 'btn-success'
			),
			'options' => array(
				'as-group' => true,
			)
		));
	}
	
	public function getInputFilterSpecification() {
		$filters = array(
			'team' => array(
				'required' => true,
				'filters' => array(
					array('name' => 'Int'),
				),
			),
			'player' => array(
				'required' => true,
				'filters' => array(
					array('name' => 'Int'),
				),
			),
		);
		return $filters;
	}
}
