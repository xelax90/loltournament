<?php
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace FSMPILoL\Entity;

use Doctrine\ORM\Mapping as ORM;
use JsonSerializable;
use SkelletonApplication\Entity\UserProfile as SkelletonProfile;

/**
 * UserProfile Entity
 * @ORM\Entity
 */
class LoLUserProfile extends SkelletonProfile implements JsonSerializable{
	
	/**
	 * @ORM\Column(type="string", nullable=true)
	 */
	protected $jabber;
	
	/**
	 * @ORM\Column(type="string")
	 */
	protected $phone;
	
	public function getJabber() {
		return $this->jabber;
	}

	public function getPhone() {
		return $this->phone;
	}

	public function setJabber($jabber) {
		$this->jabber = $jabber;
		return $this;
	}

	public function setPhone($phone) {
		$this->phone = $phone;
		return $this;
	}
	/**
	 * Returns data to show in json
	 * @return array
	 */
	public function jsonSerialize() {
		$data = parent::jsonSerialize();
		$data2 = array(
			'jabber' => $this->getJabber(),
			'phone' => $this->getPhone()
		);
		return array_merge($data, $data2);
	}

}
