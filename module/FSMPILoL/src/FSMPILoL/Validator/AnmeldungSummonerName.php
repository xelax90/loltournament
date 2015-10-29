<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace FSMPILoL\Validator;

/**
 * Description of AnmeldungSummonerName
 *
 * @author schurix
 */
class AnmeldungSummonerName extends AnmeldungTournament {
	
    /**
     * {@inheritDoc}
     */
    public function isValid($value)
    {
		$match = $this->objectRepository->getAnmeldungBySummonerName($value, $this->tournament);
        if (count($match) > 0) {
            $this->error(self::ERROR_OBJECT_FOUND, $value);
            return false;
        }
        return true;
    }
	
}
