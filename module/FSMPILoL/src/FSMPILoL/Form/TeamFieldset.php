<?php

namespace FSMPILoL\Form;

use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilterProviderInterface;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;
use DoctrineModule\Persistence\ObjectManagerAwareInterface;
use DoctrineModule\Persistence\ProvidesObjectManager;
use FSMPILoL\Entity\Team;
use FSMPILoL\Tournament\TournamentAwareInterface;
use FSMPILoL\Tournament\TournamentAwareTrait;
use FSMPILoL\Entity\LoLUser as User;
use FSMPILoL\Entity\Group;
use DoctrineModule\Form\Element\ObjectSelect;
use XelaxHTMLPurifier\Filter\HTMLPurifier;

/**
 * Fieldset to add/edit players
 */
class TeamFieldset  extends Fieldset implements InputFilterProviderInterface, ObjectManagerAwareInterface, TournamentAwareInterface{
	use TournamentAwareTrait, ProvidesObjectManager;


	public function __construct($name = "", $options = array()){
		if($name == ""){
			$name = 'item';
		}
		parent::__construct($name, $options);
	}
	
	public function init(){
		$tournament = $this->getTournament()->getTournament();
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
			'type' => ObjectSelect::class,
			'options' => array(
				'object_manager' => $this->getObjectManager(),
				'target_class'   => Group::class,
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
			'type' => ObjectSelect::class,
			'options' => array(
				'object_manager' => $this->getObjectManager(),
				'target_class'   => User::class,
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
					array('name' => HTMLPurifier::class),
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
}
