<?php
namespace FSMPILoL\Options;
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of RoundCreatorOptions
 *
 * @author schurix
 */

use Zend\Stdlib\AbstractOptions;
 
class RoundCreatorOptions extends AbstractOptions
{
	protected $roundTypes;
	
	function getRoundTypes() {
		return $this->roundTypes;
	}

	function setRoundTypes($roundTypes) {
		$this->roundTypes = $roundTypes;
	}


}