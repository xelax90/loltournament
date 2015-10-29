<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace FSMPILoL\Validator;

/**
 * Description of MinMaxEmailsNotRwth
 *
 * @author schurix
 */
class MinMaxEmailsNotRwth extends MinMaxEmailsMatchingCallback{
	
	protected function getCallback() {
		return array($this, 'emailIsNotRwth');
	}
	
	protected function emailIsNotRwth($email) {
		$mail = strtolower($email);
		
		if(empty($mail)){
			return false;
		} elseif (strpos($mail, 'rwth-aachen') === false && strpos($mail, 'fh-aachen') === false) {
			return true;
		}
		return false;
	}
}
