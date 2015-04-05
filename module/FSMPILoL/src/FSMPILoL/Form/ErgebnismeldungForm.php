<?php
namespace FSMPILoL\Form;

use Zend\Form\Form;
use FSMPILoL\Entity\Match;
use Zend\Form\Element;
use Zend\InputFilter;
use Zend\InputFilter\InputFilterProviderInterface;

class ErgebnismeldungForm extends Form implements InputFilterProviderInterface{
	protected $filterSpec = array();
	
	/**
	 * @var Match
	 */
	protected $match = null;
	
	protected $ergebnisse = array('-' => '-', '1-0' => '1-0', '0-1' => '0-1','+--' => '1-0 kampflos','--+' => '0-1 kampflos');

	public function __construct(Match $match){
		$this->match = $match;
		// we want to ignore the name passed
		parent::__construct('ergebnismeldung');
		
		$this->add(array(
			'name' => 'match_id',
			'type' => 'Hidden',
		));
		
		$games = $match->getGames();
		
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
					'options' => $this->ergebnisse,
				),
				'attributes' => array(
					'id' => 'ergebnismeldung_ergebnis_'.$game->getId(),
				)
			);
			
			$file = new Element\File('screen_'.$game->getId());
	        $file->setLabel('Screen Spiel '.$i);
			$file->setAttribute('id', 'ergebnismeldung_screen_'.$game->getId());
			$screens[] = $file;
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
	}

	public function getInputFilterSpecification() {
		$filterSpec = array();
		$games = $this->match->getGames();
		
		$minGames = ceil(count($games) / 2);
		$i = 1;
		foreach($games as $game){
			$filterSpec['ergebnis_'.$game->getId()] = array(
				'required' => $i <= $minGames,
				'filters' => array(
					array('name' => 'StringTrim'),
					array('name' => 'StripTags')
				),
				'validators' => array(
					array('name' => 'InArray', array('haystack' => $this->ergebnisse)),
				)
			);
			
			$filterSpec['screen_'.$game->getId()] = array(
				'type' => 'Zend\InputFilter\FileInput',
				'required' => false
			);
			$i++;
		}
		
		$filterSpec['anmerkung'] = array(
			'required' => false,
			'filters' => array(
				array('name' => 'StringTrim'),
				array('name' => 'StripTags'),
				array('name' => 'XelaxHTMLPurifier\Filter\HTMLPurifier'),
			),
		);
		
		$filterSpec['match_id'] = array(
			'required' => true,
			'filters' => array(
				array('name' => 'Int'),
			),
		);
		
		return $filterSpec;
	}

}