<?php
namespace FSMPILoL\Tournament\RoundCreator;

use FSMPILoL\Entity\Team;

interface AlreadyPlayedInterface{
	public function alreadyPlayed(Team $t1, Team $t2);
}
