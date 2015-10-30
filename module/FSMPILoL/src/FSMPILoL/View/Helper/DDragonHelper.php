<?php
namespace FSMPILoL\View\Helper;

use Zend\View\Helper\AbstractHelper;

class DDragonHelper extends AbstractHelper {
	protected $version = '5.21.1';
	protected $base = 'http://ddragon.leagueoflegends.com/cdn/';
	protected $profileIconPath = 'img/profileicon/';
	
	protected static $instance;
	
	public function __invoke(){
		if(null === self::$instance)
			 self::$instance = new self();
		return self::$instance;
	}
	
	public function __construct(){
		
	}
	
	public function version(){
		return $this->version;
	}
	
	public function baseUrl(){
		return $this->base . $this->version()."/";
	}
	
	public function profileIcon($iconId){
		return $this->baseUrl() . $this->profileIconPath . $iconId . ".png";
	}
}
