<?php
namespace FSMPILoL\Form;

use Zend\Form\Form;
use FSMPILoL\Tournament\RoundCreator\AbstractRoundCreator;
use Zend\ServiceManager\ServiceLocatorInterface;
use FSMPILoL\Options\RoundCreatorOptions;

class RoundForm extends Form
{
	public function __construct(ServiceLocatorInterface $sl){
		// we want to ignore the name passed
		parent::__construct('round');
		
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
				'id' => 'round_number',
				'class' => 'form-control',
				'min' => 1,
				'interval' => 1
			)
		));
		
		$this->add(array(
			'name' => 'duration',
			'type' => 'Number',
			'options' => array(
				'label' => 'Rundendauer in Tagen',
			),
			'attributes' => array(
				'id' => 'round_duration',
				'class' => 'form-control',
				'min' => 1,
				'interval' => 1
			)
		));

		$this->add(array(
			'name' => 'timeForDates',
			'type' => 'Number',
			'options' => array(
				'label' => 'Zeit für Terminfindung in Tagen',
			),
			'attributes' => array(
				'id' => 'round_number',
				'class' => 'form-control',
				'min' => 1,
				'interval' => 1
			)
		));
		
		$this->add(array(
			'name' => 'startDate',
			'type' => 'DateTimeLocal',
			'options' => array(
				'label' => 'Startdatum',
			),
			'attributes' => array(
				'id' => 'round_startdate',
				'placeholder' => 'YYYY-MM-DD HH:MM'
			)
		));
		
		$this->add(array(
			'name' => 'isHidden',
			'type' => 'Select',
			'options' => array(
				'label' => 'Versteckt',
				'options' => array('0' => 'Nein', '1' => "Ja"),
			),
			'attributes' => array(
				'id' => 'round_ishidden',
				'class' => 'form-control',
			)
		));
		
		/* @var $options RoundCreatorOptions */
		$options = $sl->get('FSMPILoL\Options\RoundCreator');
		$types = $options->getRoundTypes();
		
		$typeSelect = array();
		foreach($types as $type => $value){
			$typeSelect[$type] = ucfirst($type);
		}
		$this->add(array(
			'name' => 'type',
			'type' => 'Select',
			'options' => array(
				'label' => 'Typ',
				'options' => $typeSelect,
			),
			'attributes' => array(
				'id' => 'round_type',
				'class' => 'form-control',
			)
		));
		
		$globalDefaults = AbstractRoundCreator::getGlobalDefaults();
		foreach($globalDefaults as $param => $value){
			if(is_int($value)){
				$this->add(array(
					'name' => 'properties['.$param.']',
					'type' => 'Number',
					'options' => array(
						'label' => $param,
					),
					'attributes' => array(
						'id' => 'round_prop_'.$param,
						'class' => 'form-control',
					)
				));
			} elseif(is_bool($value)) {
				$this->add(array(
					'name' => 'properties['.$param.']',
					'type' => 'Select',
					'options' => array(
						'label' => $param,
						'options' => array('0' => 'Nein', '1' => "Ja"),
					),
					'attributes' => array(
						'id' => 'round_prop_'.$param,
						'class' => 'form-control',
					)
				));
			} else {
				$this->add(array(
					'name' => 'properties['.$param.']',
					'type' => 'Text',
					'options' => array(
						'label' => $param,
					),
					'attributes' => array(
						'id' => 'round_prop_'.$param,
						'class' => 'form-control',
					)
				));
			}
		}
		
		$this->add(array(
			'name' => 'submit',
			'type' => 'Submit',
			'attributes' => array(
				'value' => 'Go',
				'id' => 'submitbutton',
				'class' => 'btn btn-primary'
			),
		));

	}
}