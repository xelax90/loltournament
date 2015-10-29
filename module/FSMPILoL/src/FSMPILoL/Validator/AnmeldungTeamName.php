<?php

namespace FSMPILoL\Validator;

/**
 * Description of AnmeldungTeamName
 *
 * @author schurix
 */
class AnmeldungTeamName extends AnmeldungTournament {
	
    /**
     * {@inheritDoc}
     */
    public function isValid($value)
    {
		$match = $this->objectRepository->getAnmeldungByTeamName($value, $this->tournament);
        if (count($match) > 0) {
            $this->error(self::ERROR_OBJECT_FOUND, $value);
            return false;
        }
        return true;
    }
	
}
