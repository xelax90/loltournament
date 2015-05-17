<?php
namespace FSMPILoL\Model;

use Doctrine\ORM\EntityRepository;

class TeamRepository extends EntityRepository{
	
	public function getTeamsForTournament($tournament){
		$query = $this->createQueryBuilder('t')
			->join('t.group', 'g')
			->where('g.tournament = ?1')
			->setParameter(1, $tournament)
			->orderBy('t.name', 'ASC');
		return $query->getQuery()->getResult();
	}
	
}
