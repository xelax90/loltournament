<?php
namespace FSMPILoL\Form;

use Zend\Form\Form;
use Zend\InputFilter\InputFilterProviderInterface;
use Zend\Stdlib\Hydrator\ClassMethods as ClassMethodsHydrator;
use FSMPILoL\Entity\Group;

class GroupForm extends Form implements InputFilterProviderInterface
{
	public function __construct(){
		// we want to ignore the name passed
		parent::__construct('group');
		
		$this->setHydrator(new ClassMethodsHydrator(false))
				->setObject(new Group());
		
		$this->add(array(
			'name' => 'id',
			'type' => 'Hidden',
		));
		
		$this->add(array(
			'name' => 'number',
			'type' => 'Number',
			'options' => array(
				'label' => 'Number',
			),
			'attributes' => array(
				'id' => 'group_number',
				'class' => 'form-control',
				'interval' => '1',
				'min' => 1,
			)
		));
		
		$this->add(array(
			'name' => 'submit',
			'type' => 'Submit',
			'attributes' => array(
				'value' => 'Go',
				'class' => 'btn btn-success'
			),
		));
	}

	public function getInputFilterSpecification() {
		$filter = array(
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
		);
		return $filter;
	}

}