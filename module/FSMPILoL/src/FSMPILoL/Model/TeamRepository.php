<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace FSMPILoL\Model;

use Doctrine\ORM\EntityRepository;

/**
 * Description of PlayerRepository
 *
 * @author schurix
 */
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
