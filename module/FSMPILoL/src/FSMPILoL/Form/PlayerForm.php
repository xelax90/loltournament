<?php

namespace FSMPILoL\Form;

use Zend\Form\Form;
use Zend\InputFilter\InputFilter;
use DoctrineModule\Persistence\ObjectManagerAwareInterface;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * Form for Players
 */
class PlayerForm extends Form implements ObjectManagerAwareInterface{
	/**
	 * @var ObjectManager
	 */
	protected $objectManager;
	
	public function __construct($name = null, $options = array()){
		// we want to ignore the name passed
		parent::__construct('player', $options);
		$this->setAttribute('method', 'post');
	}
	
	public function setOptions($options) {
		parent::setOptions($options);
		
		if(!empty($options['forEdit'])){
			if($this->has('player')){
				$this->get('player')->setOption('forEdit', $options['forEdit']);
			}
		}
	}
	
	public function init() {
		$this->setHydrator(new DoctrineObject($this->getObjectManager()))
             ->setInputFilter(new InputFilter());
		
		$this->add(array(
			'name' => 'player',
            'type' => 'FSMPILoL\Form\PlayerFieldset',
            'options' => array(
                'use_as_base_fieldset' => true,
				'forEdit' => empty($this->getOption('forEdit')),
            ),
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
	
	public function getObjectManager() {
		return $this->objectManager;
	}

	public function setObjectManager(ObjectManager $objectManager) {
		$this->objectManager = $objectManager;
		return $this;
	}

}
