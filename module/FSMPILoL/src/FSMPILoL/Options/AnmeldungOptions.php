<?php
namespace FSMPILoL\Options;

use Zend\Stdlib\AbstractOptions;
 
class AnmeldungOptions extends AbstractOptions
{
	protected $iconDir = './public/img/teamIcons/';
	protected $tournamentId = 4;
 
	public function getIconDir() { return $this->iconDir; }
    public function setIconDir($iconDir) { $this->iconDir = $iconDir; }
	public function getTournamentId() { return $this->tournamentId; }
    public function setTournamentId($tournamentId) { $this->tournamentId = $tournamentId; }
}