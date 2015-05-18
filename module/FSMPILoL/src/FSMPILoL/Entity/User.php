<?php
namespace FSMPILoL\Entity;

use BjyAuthorize\Provider\Role\ProviderInterface;
use ZfcUser\Entity\User as ZfcUserEntity;
use Doctrine\ORM\Mapping as ORM;
use JsonSerializable;
use Zend\Json\Json;
use FSMPILoL\Entity\Tournament;

/**
 * A User.
 *
 * @ORM\Entity(repositoryClass="FSMPILoL\Model\UserRepository")
 * @ORM\Table(name="user")
 */
class User extends ZfcUserEntity implements JsonSerializable, ProviderInterface
{
    /**
     * @var \Doctrine\Common\Collections\Collection
     * @ORM\ManyToMany(targetEntity="Role")
     * @ORM\JoinTable(name="user_role_linker",
     *      joinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="user_id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="role_id", referencedColumnName="id")}
     * )
     */
    protected $roles;

	/**
	 * @ORM\Column(type="string", nullable=true)
	 */
	protected $jabber;

	/**
	 * @ORM\Column(type="string", nullable=true)
	 */
	protected $phone;
	
	/**
	 * @ORM\OneToMany(targetEntity="Player", mappedBy="user")
	 */
	protected $players;
	
	public function __construct() {
		$this->roles = new \Doctrine\Common\Collections\ArrayCollection();
	}
	
    /**
     * Get role name.
     *
     * @return string
     */
	public function getRoleName(){ 
		$res = "";
		$roles = $this->getRoles();
		foreach($roles as $role){
			if(!empty($res)){
				$res .= ", ";
			}
			$res .= $role->getRoleId();
		}
		return $res;
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
	
	public function getPlayer(Tournament $tournament){
		/** @var $player \FSMPILoL\Entity\Player  */
		foreach($this->players as $player){
			if($player->getAnmeldung()->getTournament() == $tournament)
				return $player;
		}
		return null;
	}
	
    /**
     * Get roles
     *
     * @return array
     */
    public function getRoles()
    {
        return $this->roles->getValues();
    }

    /**
     * Add a role to the user.
     *
     * @param Role $role
     *
     * @return void
     */
    public function addRole($role)
    {
        $this->roles[] = $role;
    }
	
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
			'roles' => $this->getRoles(),
			'state' => $this->getState(),
			'jabber' => $this->getJabber(),
			'phone' => $this->getPhone(),
		);
		return $data;
	}
}
