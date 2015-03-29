<?php
namespace FSMPILoL\Form;

use Zend\Form\Form;
use Zend\InputFilter\InputFilterProviderInterface;

class TournamentForm extends Form implements InputFilterProviderInterface
{
	public function __construct(){
		// we want to ignore the name passed
		parent::__construct('tournament');
		
		$this->add(array(
			'name' => 'id',
			'type' => 'Hidden',
		));
		
		$this->add(array(
			'name' => 'name',
			'type' => 'Text',
			'options' => array(
				'label' => 'Name',
			),
			'attributes' => array(
				'id' => 'tournament_name',
				'class' => 'form-control',
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
			'name' => array(
				'required' => true,
				'filters' => array(
					array('name' => 'StringTrim'),
					array('name' => 'XelaxHTMLPurifier\Filter\HTMLPurifier'),
				),
			),
		);
		return $filter;
	}

}