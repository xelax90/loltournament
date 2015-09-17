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
use Doctrine\Common\Persistence\ObjectManager;
use Zend\InputFilter\InputFilterProviderInterface;

/**
 * Description of AnmeldungSingleForm
 *
 * @author schurix
 */
class AnmeldungTeamForm extends Form implements ObjectManagerAwareInterface, InputFilterProviderInterface{
	protected $em;

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
                'count' => 5,
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
				'label_options' => array(
					'disable_html_escape' => true,
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
		));
	}
	
	public function getInputFilterSpecification() {
		$filterSpec = array(
			'teamName' => array(
				'required' => true,
				'filters' => array(
					array('name' => 'StringTrim'),
					array('name' => 'StripTags'),
					array('name' => 'XelaxHTMLPurifier\Filter\HTMLPurifier'),
				),
			),
			'team_icon_text' => array(
				'required' => true,
				'filters' => array(
					array('name' => 'StringTrim'),
					array('name' => 'StripTags'),
					array('name' => 'XelaxHTMLPurifier\Filter\HTMLPurifier'),
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
			),
		);
		
		/*
		$filter = $this->get('anmeldungen')->getTargetElement()->getInputFilterSpecification();
		$collectionFilter = $filterFactory->createInputFilter($filter);
		$collectionFilter->get('name')->setRequired(false);
		$collectionFilter->get('email')->setRequired(false);
		$collectionFilter->get('summonerName')->setRequired(false);
		$collectionContainerFilter = new \Zend\InputFilter\CollectionInputFilter();
		$collectionContainerFilter->setInputFilter($collectionFilter);
		$this->getInputFilter()->add($collectionContainerFilter, 'anmeldungen');
		*/
		
		return $filterSpec;
	}

	public function getObjectManager() {
		return $this->em;
	}

	public function setObjectManager(ObjectManager $objectManager) {
		$this->em = $objectManager;
	}
}