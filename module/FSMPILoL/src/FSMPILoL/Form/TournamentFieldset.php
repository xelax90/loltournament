<?php
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace FSMPILoL\Form;

use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilterProviderInterface;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;
use DoctrineModule\Persistence\ObjectManagerAwareInterface;
use Doctrine\Common\Persistence\ObjectManager;

use FSMPILoL\Entity\Tournament;

/**
 * TournamentFieldset Fieldset
 *
 * @author schurix
 */
class TournamentFieldset extends Fieldset implements InputFilterProviderInterface, ObjectManagerAwareInterface{
	protected $objectManager;
	
	public function __construct($name = "", $options = array()){
		if($name == ""){
			$name = 'TournamentFieldset';
		}
		parent::__construct($name, $options);
	}
	
	public function init(){
		$this->setHydrator(new DoctrineHydrator($this->getObjectManager()))
			 ->setObject(new Tournament());
		
		$this->add(array(
			'name' => 'id',
			'type' => 'Hidden',
		));
		
		$this->add(array(
			'name' => 'name',
			'type' => 'Text',
			'options' => array(
				'label' => gettext_noop('Name'),
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
			'name' => array(
				'required' => false,
				'filters' => array(
					array('name' => 'StringTrim'),
					array('name' => 'StripTags'),
					array('name' => 'XelaxHTMLPurifier\Filter\HTMLPurifier'),
				),
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
}
