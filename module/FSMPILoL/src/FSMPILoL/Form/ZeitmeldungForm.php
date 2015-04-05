<?php
namespace FSMPILoL\Form;

use Zend\Form\Form;
use FSMPILoL\Entity\Match;
use Zend\InputFilter\InputFilterProviderInterface;

class ZeitmeldungForm extends Form implements InputFilterProviderInterface{
	public function __construct(Match $match){
		// we want to ignore the name passed
		parent::__construct('zeitmeldung');
		
		$this->add(array(
			'name' => 'match_id',
			'type' => 'Hidden',
		));
		
		$this->add(array(
			'name' => 'time',
			'type' => 'DateTimeLocal',
			'options' => array(
				'label' => 'Time',
			),
			'attributes' => array(
				'id' => 'zeitmeldung_time',
				'placeholder' => 'YYYY-MM-DD HH:MM'
			)
		));

		$this->add(array(
			'name' => 'submit',
			'type' => 'Submit',
			'attributes' => array(
				'value' => 'Offiziellen Spieltermin melden',
				'id' => 'zeitmeldung_submit',
				'style' => 'float: left; clear: both',
			),
		));

	}

	public function getInputFilterSpecification() {
		$filter = array(
			'match_id' => array(
				'required' => true,
				'filters' => array(
					array('name' => 'Int'),
				),
			),
			'time' => array(
				'required' => true,
			),
		);
		return $filter;		
	}

}