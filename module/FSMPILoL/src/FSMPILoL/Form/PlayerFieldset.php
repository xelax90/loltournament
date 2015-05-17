<?php

namespace FSMPILoL\Form;

use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilterProviderInterface;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;
use DoctrineModule\Persistence\ObjectManagerAwareInterface;
use Doctrine\Common\Persistence\ObjectManager;
use FSMPILoL\Entity\Player;
use FSMPILoL\Entity\Tournament;
use FSMPILoL\Tournament\TournamentAwareInterface;

/**
 * Fieldset to add/edit players
 */
class PlayerFieldset  extends Fieldset implements InputFilterProviderInterface, ObjectManagerAwareInterface, TournamentAwareInterface{
	protected $objectManager;
	protected $tournament;


	public function __construct($name = "", $options = array()){
		if($name == ""){
			$name = 'item';
		}
		parent::__construct($name, $options);
	}
	
	public function init(){
		$tournament = $this->getTournament();
		if(empty($tournament)){
			throw new \RuntimeException('Tournament cannot be empty');
		}
		
		$this->setHydrator(new DoctrineHydrator($this->getObjectManager()))
			 ->setObject(new Player());
		
		$this->add(array(
			'name' => 'id',
			'type' => 'Hidden',
		));
		
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
			'name' => 'anmeldung',
            'type' => 'FSMPILoL\Form\AnmeldungFieldset',
            'options' => array(
				'showSub' => false,
				'showAnmerkungen' => true,
				'validateSummonerNameTournament' => true,
				'validateEmailTournament' => true,
				'validateSummonerNameExists' => false,
				'validateEmailExists' => false,
            ),
			'attributes' => array(
				'class' => 'anmeldung_single_fieldset',
			)
        ));
		
		$this->add(array(
			'name' => 'isCaptain',
			'type' => 'Checkbox',
			'options' => array(
				'label' => '',
				'use-switch' => true,
				'checked_value' => '1',
				'label_options' => array(
					'position' => \Zend\Form\View\Helper\FormRow::LABEL_APPEND,
				),
				'column-size' => 'sm-12',
			),
			'attributes' => array(
				'id' => "",
				'data-label-text' => 'Captain',
				'data-label-width' => '100',
				'data-off-color' => 'warning',
				'data-on-text' => 'Yes',
				'data-off-text' => 'No',
			)
		));
	}

	public function getInputFilterSpecification() {
		$filters = array(
			'id' => array(
				'required' => false,
				'filters' => array(
					array('name' => 'Int'),
				),
			),
			'team' => array(
				'required' => false,
				'filters' => array(
					array('name' => 'Int'),
				),
			),
			'isCaptain' => array(
				'required' => false,
				'filters' => array(
					array('name' => 'Digits'),
				)
			),
		);
		return $filters;
	}

	public function getObjectManager() {
		return $this->objectManager;
	}

	public function setObjectManager(ObjectManager $objectManager) {
		$this->objectManager = $objectManager;
		return $this;
	}

	public function getTournament() {
		return $this->tournament;
	}

	public function setTournament(Tournament $tournament) {
		$this->tournament = $tournament;
	}

}
