<?php

namespace FSMPILoL\Model;

use Doctrine\ORM\EntityRepository;

class PlayerRepository extends EntityRepository{
	
	public function getPlayerByEmail($mail, $tournament){
		$query = $this->createQueryBuilder('p')
			->join('p.anmeldung', 'a')
			->andWhere('a.tournament = ?1')
			->andWhere('a.email = ?2')
			->setParameter(1, $tournament)
			->setParameter(2, $mail);
		//var_dump($query->getDQL());
		//aser();
		return $query->getQuery()->getResult();
	}
	
	public function getPlayerBySummonerName($summonerName, $tournament){
		$query = $this->createQueryBuilder('p')
			->join('p.anmeldung', 'a')
			->andWhere('a.tournament = ?1')
			->andWhere('a.summonerName = ?2')
			->setParameter(1, $tournament)
			->setParameter(2, $summonerName);
		return $query->getQuery()->getResult();
	}
}
