<?php
namespace FSMPILoL\Options;

use Zend\Stdlib\AbstractOptions;
 
class APIOptions extends AbstractOptions
{
	protected $key = '';
	protected $region = 'euw';
	protected $maxRequests = 20;
 
	public function getKey() { return $this->key; }
    public function getRegion() { return $this->region; }
    public function getMaxRequests() { return $this->maxRequests; }
 
    public function setKey($key) { $this->key = $key; }
    public function setRegion($region) { $this->region = $region; }
    public function setMaxRequests($maxRequests) { $this->maxRequests = $maxRequests; }
}