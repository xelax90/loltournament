<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace FSMPILoL\Form;

use Zend\Form\Form;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;
use Zend\InputFilter\InputFilter;
use DoctrineModule\Persistence\ObjectManagerAwareInterface;
use DoctrineModule\Persistence\ProvidesObjectManager;
use Zend\InputFilter\InputFilterProviderInterface;
use FSMPILoL\Tournament\TournamentAwareInterface;
use FSMPILoL\Tournament\TournamentAwareTrait;
use FSMPILoL\Validator\MinMaxEmailsRwth;
use FSMPILoL\Validator\MinMaxEmailsNotRwth;
use FSMPILoL\Entity\Anmeldung as AnmeldungEntity;
use FSMPILoL\Validator\NoObjectExistsInTournament;


/**
 * Description of AnmeldungSingleForm
 *
 * @author schurix
 */
class AnmeldungTeamForm extends Form implements ObjectManagerAwareInterface, InputFilterProviderInterface, TournamentAwareInterface{
	use ProvidesObjectManager, TournamentAwareTrait;
	
	public function __construct($name = "", $options = array()){
		// we want to ignore the name passed
		parent::__construct('anmeldung_team', $options);
		$this->setAttribute('method', 'post');
	}
	
	public function init(){
		$this->setHydrator(new DoctrineHydrator($this->getObjectManager()))
			 ->setInputFilter(new InputFilter());
		
		$this->add(array(
			'name' => 'teamName',
			'type' => 'Text',
			'options' => array(
				'label' => 'Team Name<sup>*</sup>',
				'label_options' => array(
					'disable_html_escape' => true,
				),
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
			'name' => 'team_icon_text',
			'type' => 'Hidden',
			'attributes' => array(
				'id' => 'team_icon_text',
			),
		));
		
		$this->add(array(
            'type' => 'Zend\Form\Element\Collection',
            'name' => 'anmeldungen',
            'options' => array(
                'label' => 'Spieler',
                'count' => 2,
                'should_create_template' => true,
		        'template_placeholder' => '__placeholder__',
                'allow_add' => true,
                'target_element' => array(
                    'type' => 'FSMPILoL\Form\AnmeldungFieldset',
					'options' => array(
						'showSub' => false,
						'showAnmerkungen' => false,
						'data_required' => false,
					)
                )
            ),
			'attributes' => array(
				'id' => "teamanmeldung_spieler",
			)
        ));

		$this->add(array(
			'name' => 'ausschreibung_gelesen',
			'type' => 'Checkbox',
			'options' => array(
				'label' => 'Ausschreibung gelesen<sup>*</sup>',
				'checked_value' => '1',
				'label_options' => array(
					'disable_html_escape' => true,
				),
				'column-size' => 'sm-10 col-sm-offset-2',
			),
			'attributes' => array(
			),
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

		$this->add(array(
			'name' => 'submit',
			'type' => 'Submit',
			'attributes' => array(
				'value' => 'Eingaben Prüfen',
			),
			'options' => array(
				'as-group' => true,
			)
		));
	}
	
	public function getInputFilterSpecification() {
		$tournament = $this->getTournament()->getTournament();
		$filterSpec = array(
			'teamName' => array(
				'required' => true,
				'filters' => array(
					array('name' => 'StringTrim'),
					array('name' => 'StripTags'),
					array('name' => 'XelaxHTMLPurifier\Filter\HTMLPurifier'),
				),
				'validators' => array(
					array(
						'name' => NoObjectExistsInTournament::class,
						'options' => array(
							'object_repository' => $this->getObjectManager()->getRepository(AnmeldungEntity::class),
							'tournament' => $tournament,
							'fields' => 'teamName'
						),
					),
					array(
						'name' => MinMaxEmailsRwth::class,
						'options' => array(
							'min' => 1, // use float to get percent instead of absolute limit
							'messages'=> array(
								MinMaxEmailsRwth::MESSAGE_NOT_ENOUGH => 'Es muss mindestens eine RWTH-Mailadresse angegeben sein',
							)
						),
					),
					array(
						'name' => MinMaxEmailsNotRwth::class,
						'options' => array(
							'max' => 3, // use float to get percent instead of absolute limit
							'messages'=> array(
								MinMaxEmailsRwth::MESSAGE_NOT_ENOUGH => 'Es dürfen nicht mehr, als 3 externe Mailadressen angegeben sein',
							)
						),
					),
				),
			),
			'team_icon_text' => array(
				'required' => true,
				'filters' => array(
					array('name' => 'StringTrim'),
					array('name' => 'StripTags'),
					array('name' => 'XelaxHTMLPurifier\Filter\HTMLPurifier'),
				),
				'validators' => array(
					array(
						'name' => NoObjectExistsInTournament::class,
						'options' => array(
							'object_repository' => $this->getObjectManager()->getRepository(AnmeldungEntity::class),
							'tournament' => $tournament,
							'fields' => 'icon'
						),
					),
				),
			),
			'anmerkung' => array(
				'required' => false,
				'filters' => array(
					array('name' => 'StringTrim'),
					array('name' => 'StripTags'),
					array('name' => 'XelaxHTMLPurifier\Filter\HTMLPurifier'),
				),
			),
			'ausschreibung_gelesen' => array(
				'required' => true,
				'allow_empty' => false,
				'filters' => array(
					array('name' => 'Digits'),
				),
				'validators' => array(
					array(
						'name' => 'Callback',
						'options' => array(
							'callback' => function($v){
								return !empty($v);
							},
							'message' => 'Du musst die Ausschreibung lesen, um am Turnier teilzunehmen',
						)
					)
				)
			),
		);
		
		return $filterSpec;
	}
}