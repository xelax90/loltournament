<?php
namespace FSMPILoL\Model;

use Doctrine\ORM\EntityRepository;

/**
 * Description of AnmeldungRepository
 *
 * @author schurix
 */
class AnmeldungRepository extends EntityRepository{
	public function getAnmeldungBySummonerName($summonerName, $tournament){
		return $this->findBy(array(
			'tournament' => $tournament,
			'summonerName' => $summonerName,
		));
	}
	
	public function getAnmeldungByTeamName($teamName, $tournament){
		return $this->findBy(array(
			'tournament' => $tournament,
			'teamName' => $teamName,
		));
	}
	
	public function getAnmeldungByTeamIcon($icon, $tournament){
		return $this->findBy(array(
			'tournament' => $tournament,
			'icon' => $icon,
		));
	}
}
