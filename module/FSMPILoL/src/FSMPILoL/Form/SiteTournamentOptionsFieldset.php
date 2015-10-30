<?php
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace FSMPILoL\Form;

use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilterProviderInterface;
use DoctrineModule\Persistence\ObjectManagerAwareInterface;
use DoctrineModule\Persistence\ProvidesObjectManager;
use FSMPILoL\Options\TournamentOptions;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;
use FSMPILoL\Entity\Tournament as TournamentEntity;
use DoctrineModule\Form\Element\ObjectSelect;

/**
 * SiteTournamentOptionsFieldset Fieldset
 *
 * @author schurix
 */
class SiteTournamentOptionsFieldset extends Fieldset implements InputFilterProviderInterface, ServiceLocatorAwareInterface, ObjectManagerAwareInterface{
	use ProvidesObjectManager, ServiceLocatorAwareTrait;
	
	public function __construct($name = "", $options = array()){
		if($name == ""){
			$name = 'SiteTournamentOptionsFieldset';
		}
		parent::__construct($name, $options);
	}
	
	public function init(){
		$this->add(array(
			'name' => 'current_tournament',
			'type' => ObjectSelect::class,
			'options' => array(
				'object_manager' => $this->getObjectManager(),
				'target_class' => TournamentEntity::class,
				'label_generator' => function($item) {
					return $item->getName();
				},
				'display_empty_item' => true,
				'empty_item_label' => gettext_noop('-- Tournament --'),
				'label' => gettext_noop('Tournament'),
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
            'name' => 'tournament_phase',
            'type' => 'select',
            'options' => array(
                'label' => gettext_noop('Tournament phase'),
                'value_options' => array(
					TournamentOptions::TOURNAMENT_PHASE_ANNOUNCED
						=> gettext_noop('Announced'),
					TournamentOptions::TOURNAMENT_PHASE_REGISTRATION
						=> gettext_noop('Registration'),
					TournamentOptions::TOURNAMENT_PHASE_PRE_ROUND
						=> gettext_noop('Pre rounds'),
					TournamentOptions::TOURNAMENT_PHASE_MAIN_ROUND 
						=> gettext_noop('Main rounds'),
					TournamentOptions::TOURNAMENT_PHASE_PLAYOFFS
						=> gettext_noop('Playoffs'),
				),
				'column-size' => 'sm-10',
				'label_attributes' => array(
					'class' => 'col-sm-2',
				),
            )
        ));
		
		$this->add(array(
            'name' => 'registration_status',
            'type' => 'select',
            'options' => array(
                'label' => gettext_noop('Registration status'),
                'value_options' => array(
					TournamentOptions::REGISTRATION_STATUS_OPEN
						=> gettext_noop('Open'),
					TournamentOptions::REGISTRATION_STATUS_NO_TEAMS
						=> gettext_noop('Team registration closed'),
					TournamentOptions::REGISTRATION_STATUS_SUB_ONLY 
						=> gettext_noop('Teams disabled and only sub registration allowed'),
					TournamentOptions::REGISTRATION_STATUS_CLOSED
						=> gettext_noop('Closed'),
				),
				'column-size' => 'sm-10',
				'label_attributes' => array(
					'class' => 'col-sm-2',
				),
            )
        ));
		
	}
	
	public function getInputFilterSpecification() {
		$filters = array(
			'current_tournament' => array(
				'required' => true,
			),
			'tournament_phase' => array(
				'required' => true,
			),
			'registration_status' => array(
				'required' => true,
			),
		);
		return $filters;
	}
}
