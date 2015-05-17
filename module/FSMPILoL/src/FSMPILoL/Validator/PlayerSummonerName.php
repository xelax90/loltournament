<?php

namespace FSMPILoL\Validator;

use FSMPILoL\Validator\PlayerTournament;

/**
 * Validates if there is a player with given summoner name in tournament
 */
class PlayerSummonerName extends PlayerTournament{
	
    /**
     * {@inheritDoc}
     */
    public function isValid($value)
    {
		$match = $this->objectRepository->getPlayerBySummonerName($value, $this->tournament);
        if (count($match) > 0) {
            $this->error(self::ERROR_OBJECT_FOUND, $value);

            return false;
        }

        return true;
    }
	
	
}
