<?php

namespace FSMPILoL\Model;

use Doctrine\ORM\EntityRepository;

class UserRepository extends EntityRepository{
	
	public function getAdminUsers(){
		$query = $this->createQueryBuilder('u')
			->join('u.roles', 'r')
			->andWhere('r.roleId = ?1')
			->setParameter(1, 'administrator')
			->orderBy('u.displayName', 'ASC');
		return $query->getQuery()->getResult();
	}
	
}
