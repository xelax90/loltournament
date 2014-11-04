<?php
namespace FSMPILoL\Entity;

use ZfcUser\Entity\User as ZfcUserEntity;
use Doctrine\ORM\Mapping as ORM;
use JsonSerializable;
use Zend\Json\Json;

/**
 * A User.
 *
 * @ORM\Entity
 * @ORM\Table(name="user")
 */
class User extends ZfcUserEntity implements JsonSerializable
{
	const ROLE_ADMIN = 10;
	const ROLE_MODERATOR = 20;
	const ROLE_REPORTER = 30;
	
	public static function getRoles(){
		return self::$roles;
	}
	
	protected static $roles = array(
		self::ROLE_ADMIN => 'Admin', 
		self::ROLE_MODERATOR => 'Moderator', 
		self::ROLE_REPORTER => 'Reporter'
	);

	/**
	 * @ORM\Column(type="integer")
	 */
	public $role;

	/**
	 * @ORM\Column(type="string")
	 */
	public $jabber;

	/**
	 * @ORM\Column(type="string")
	 */
	public $phone;

    /**
     * Get role.
     *
     * @return int
     */
	public function getRole(){ return $this->role; }

    /**
     * Get role name.
     *
     * @return string
     */
	public function getRoleName(){ 
		if(array_key_exists($this->role, self::$roles)) 
			return self::$roles[$this->role]; 
		return "";
	}

    /**
     * Get jabber.
     *
     * @return string
     */
	public function getJabber(){ return $this->jabber; }

    /**
     * Get phone.
     *
     * @return string
     */
	public function getPhone(){ return $this->phone; }

    /**
     * Set role.
     *
     * @param int $role
     * @return UserInterface
     */
	public function setRole($role){ $this->role = $role; return $this; }

    /**
     * Set jabber.
     *
     * @param string $jabber
     * @return UserInterface
     */
	public function setJabber($jabber){ $this->jabber = $jabber; return $this; }

    /**
     * Set phone.
     *
     * @param string $phone
     * @return UserInterface
     */
	public function setPhone($phone){ $this->phone = $phone; return $this; }

	public function getArrayCopy(){
		return $this->jsonSerialize();
	}
	
	public function __toString(){
		return $this->getDisplayName();
	}
	
	public function toJson(){
		$data = $this->jsonSerialize();
		return Json::encode($data, true, array('silenceCyclicalExceptions' => true));
	}
	
	public function jsonSerialize(){
		$data = array(
			'user_id' => $this->getId(),
			'username' => $this->getUsername(),
			'email' => $this->getEmail(),
			'displayname' => $this->getDisplayName(),
			'state' => $this->getState(),
			'role' => $this->getRole(),
			'jabber' => $this->getJabber(),
			'phone' => $this->getPhone(),
		);
		return $data;
	}
}
