<?php
namespace FSMPILoL\Form;

use Zend\Form\Form;
use FSMPILoL\Entity\Match;
use Zend\Form\Element;
use Zend\InputFilter;

class ErgebnismeldungForm extends Form{
	public function __construct(Match $match){
		// we want to ignore the name passed
		parent::__construct('ergebnismeldung');
		$inputFilter = new InputFilter\InputFilter();
		
		$this->add(array(
			'name' => 'match_id',
			'type' => 'Hidden',
		));
		
		$games = $match->getGames();
		
		/*
		$ergebnisse = array();
		$gc = count($games);
		for($i = 0; $i <= ceil($gc / 2); $i++){
			for($j = 0; $j <= ceil($gc / 2); $j++){
				if($i + $j > $gc)
					continue;
				if($i < ceil($gc / 2) && $j < ceil($gc / 2))
					continue;
				$ergebnisse[] = $i."-".$j;
			}
		}*/
		
		$ergebnisse = array('-' => '-', '1-0' => '1-0', '0-1' => '0-1');
		
		$i = 0;
		$ergs = array();
		$screens = array();
		foreach($games as $game){
			$i++;
			$ergs[] = array(
				'name' => 'ergebnis_'.$game->getId(),
				'type' => 'Select',
				'options' => array(
					'label' => 'Ergebnis Spiel '.$i,
					'options' => $ergebnisse,
				),
				'attributes' => array(
					'id' => 'ergebnismeldung_ergebnis_'.$game->getId(),
				)
			);
			
			$file = new Element\File('screen_'.$game->getId());
	        $file->setLabel('Screen Spiel '.$i);
			$file->setAttribute('id', 'ergebnismeldung_screen_'.$game->getId());
			$screens[] = $file;
			
			$fileInput = new InputFilter\FileInput('screen_'.$game->getId());
			$fileInput->setRequired(false);
			$inputFilter->add($fileInput);
		}
		
		foreach($ergs as $ergebnis){
			$this->add($ergebnis);
		}
		
		foreach($screens as $screen){
			$this->add($screen);
		}
		
		$this->add(array(
			'name' => 'anmerkung',
			'type' => 'Textarea',
			'options' => array(
				'label' => 'Anmerkung',
			),
			'attributes' => array(
				'id' => 'zeitmeldung_anmerkung',
			)
		));

		$this->add(array(
			'name' => 'submit',
			'type' => 'Submit',
			'attributes' => array(
				'value' => 'Ergebnis melden',
				'id' => 'zeitmeldung_submit',
				'style' => 'float: left; clear: both',
				'placeholder' => 'YYYY-MM-DD HH:MM'
			),
		));

		$this->setInputFilter($inputFilter);
	}
}