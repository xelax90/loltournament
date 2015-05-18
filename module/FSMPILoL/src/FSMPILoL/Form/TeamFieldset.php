<?php

namespace FSMPILoL\Form;

use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilterProviderInterface;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;
use DoctrineModule\Persistence\ObjectManagerAwareInterface;
use Doctrine\Common\Persistence\ObjectManager;
use FSMPILoL\Entity\Team;
use FSMPILoL\Entity\Tournament;
use FSMPILoL\Tournament\TournamentAwareInterface;

/**
 * Fieldset to add/edit players
 */
class TeamFieldset  extends Fieldset implements InputFilterProviderInterface, ObjectManagerAwareInterface, TournamentAwareInterface{
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
			 ->setObject(new Team());
		
		$this->add(array(
			'name' => 'id',
			'type' => 'Hidden',
		));
		
		$this->add(array(
			'name' => 'group',
			'type' => 'DoctrineModule\Form\Element\ObjectSelect',
			'options' => array(
				'object_manager' => $this->getObjectManager(),
				'target_class'   => 'FSMPILoL\Entity\Group',
				'label_generator' => function($group) {
					return 'Gruppe '.$group->getNumber();
				},
				'find_method'    => array(
					'name'   => 'findBy',
					'params' => array(
						'criteria' => array ('tournament' => $tournament)
					),
				),
				'label' => 'Gruppe',
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
			'name' => 'name',
			'type' => 'Text',
			'options' => array(
				'label' => 'Name',
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
			'name' => 'ansprechpartner',
			'type' => 'DoctrineModule\Form\Element\ObjectSelect',
			'options' => array(
				'object_manager' => $this->getObjectManager(),
				'target_class'   => 'FSMPILoL\Entity\User',
				'label_generator' => function($user) {
					return $user->getDisplayName();
				},
				'find_method'    => array(
					'name'   => 'getAdminUsers',
				),
				'label' => 'Ansprechpartner',
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
			'name' => 'number',
			'type' => 'Number',
			'options' => array(
				'label' => 'Number',
				'column-size' => 'sm-10',
				'label_attributes' => array(
					'class' => 'col-sm-2',
				),
			),
			'attributes' => array(
				'min' => 0,
				'step' => 1,
				'id' => "",
			)
		));
		
		$this->add(array(
			'name' => 'isBlocked',
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
				'data-label-text' => 'Blocked',
				'data-label-width' => '100',
				'data-off-color' => 'warning',
				'data-on-text' => 'Yes',
				'data-off-text' => 'No',
			)
		));
		
		$this->add(array(
			'name' => 'icon',
			'type' => 'Text',
			'options' => array(
				'label' => 'Icon',
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
			'name' => 'anmerkung',
			'type' => 'Textarea',
			'options' => array(
				'label' => 'Anmerkungen',
				'column-size' => 'sm-10',
				'label_attributes' => array(
					'class' => 'col-sm-2',
				),
			),
			'attributes' => array(
				'id' => "",
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
			'number' => array(
				'required' => true,
				'filters' => array(
					array('name' => 'Int'),
				),
			),
			'isBlocked' => array(
				'required' => false,
				'filters' => array(
					array('name' => 'Digits'),
				),
			),
			'group' => array(
				'required' => true,
				'filters' => array(
					array('name' => 'Int'),
				),
			),
			'name' => array(
				'required' => true,
				'filters' => array(
					array('name' => 'StringTrim'),
					array('name' => 'StripTags'),
					array('name' => 'XelaxHTMLPurifier\Filter\HTMLPurifier'),
				)
			),
			'ansprechpartner' => array(
				'required' => true,
				'filters' => array(
					array('name' => 'Int'),
				),
			)
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
