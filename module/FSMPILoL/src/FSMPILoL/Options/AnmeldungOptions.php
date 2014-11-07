<?php
namespace FSMPILoL\Options;

use Zend\Stdlib\AbstractOptions;
 
class AnmeldungOptions extends AbstractOptions
{
	protected $iconDir = './public/img/teamIcons/';
 
	public function getIconDir() { return $this->iconDir; }
    public function setIconDir($iconDir) { $this->iconDir = $iconDir; }
}