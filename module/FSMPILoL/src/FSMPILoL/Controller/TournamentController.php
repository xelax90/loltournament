<?php
namespace FSMPILoL\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use FSMPILoL\Riot\RiotAPI;
use FSMPILoL\Tournament\Summonerdata;
use FSMPILoL\Tournament\Group;
use FSMPILoL\Form\ZeitmeldungForm;
use FSMPILoL\Form\ErgebnismeldungForm;

use DateTime;

class TournamentController extends AbstractActionController
{
	/** @var Tournament */
	protected $tournament;

	/** @var Doctrine\ORM\EntityManager */
	protected $em;
	
	public function getEntityManager(){
		if (null === $this->em) {
			$this->em = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');
		}
		return $this->em;
	}
	
	public function getTournament(){
		if(null === $this->tournament){
			$options = $this->getServiceLocator()->get('FSMPILoL\Options\Anmeldung');
			$tournamentId = $options->getTournamentId();
			$em = $this->getEntityManager();
			$this->tournament = $em->getRepository('FSMPILoL\Entity\Tournament')->find($tournamentId);
		}
		return $this->tournament;
	}
	
	protected function setTeamdata(){
		$tournament = $this->getTournament();
		if(!$tournament)
		 	return;
		
		foreach($tournament->getGroups() as $group){
			$gGroup = new Group($group, $this->getServiceLocator());
			$gGroup->setTeamdata();
		}
	}

	
	protected function setAPIData(){
		$tournament = $this->getTournament();
		if(!$tournament)
		 	return;
		
		foreach($tournament->getGroups() as $group){
			$gGroup = new Group($group, $this->getServiceLocator());
			$gGroup->setAPIData();
		}
	}
	
	public function indexAction(){
		return new ViewModel();
	}
	
	public function ergebnisseAction(){
		$tournament = $this->getTournament();
		if (!$tournament) {
			return new ViewModel();
		}

		$this->setTeamdata();
		$this->setAPIData();
		return new ViewModel(array('tournament' => $tournament));
	}
	
	public function teamsAction(){
		$tournament = $this->getTournament();
		if (!$tournament) {
			return new ViewModel();
		}

		$this->setAPIData();
		
		//$api = new RiotAPI($this->getServiceLocator());
		$loginForm = $this->getServiceLocator()->get('zfcuser_login_form');
		$fm = $this->flashMessenger()->setNamespace('zfcuser-login-form')->getMessages();
		if (isset($fm[0])) {
			$loginForm->setMessages(
				array('identity' => array($fm[0]))
			);
		}
		
		return new ViewModel(array('tournament' => $tournament, 'loginForm' => $loginForm));
	}
	
	public function paarungenAction(){
		$this->authenticate();
		$tournament = $this->getTournament();
		if (!$tournament) {
			return new ViewModel();
		}

		$this->setTeamdata();
		$this->setAPIData();
		
		$loginForm = $this->getServiceLocator()->get('zfcuser_login_form');
		$fm = $this->flashMessenger()->setNamespace('zfcuser-login-form')->getMessages();
		if (isset($fm[0])) {
			$loginForm->setMessages(
				array('identity' => array($fm[0]))
			);
		}
		
		return new ViewModel(array('tournament' => $tournament, 'loginForm' => $loginForm));
	}
	
	public function meldungAction(){
		$this->authenticate();
		$request = $this->getRequest();
		$em = $this->getEntitymanager();
		$tournament = $this->getTournament();
		if (!$tournament) {
			return $this->redirect()->toRoute('paarungen');
		}

		if (!$this->zfcUserAuthentication()->hasIdentity()) {
			return $this->redirect()->toRoute('paarungen');
		}

		$identity = $this->zfcUserAuthentication()->getIdentity();
		$player = $identity->getPlayer($tournament);
		
		if($player){
			$team = $player->getTeam();
			if ($team) {
				$group = $team->getGroup();
			}
		}
		
		if (!$player | !$team | !$group) {
			return $this->redirect()->toRoute('paarungen');
		}

		$forms = array();
		foreach($group->getRounds() as $round){
			foreach($round->getMatches() as $match){
				if($match->getIsBlocked())
					continue;
				
				if($match->getTeamHome() == $team || $match->getTeamGuest() == $team){
					$isHome = $team == $match->getTeamHome();
					
					$zeitForm = new ZeitmeldungForm($match, $team);
					$data = array('match_id' => $match->getId());
					
					if($isHome){
						$timeGetter = 'getTimeHome';
					} else {
						$timeGetter = 'getTimeGuest';
					}
					
					if(!empty($match->$timeGetter()) && $match->$timeGetter()->format('Y') > 0)
						$data['time'] = $match->$timeGetter()->format('Y-m-d\TH:i:s');
					$zeitForm->setData($data);
					
					$ergebnisForm = new ErgebnismeldungForm($match, $team);
					$data = array('match_id' => $match->getId());
					foreach($match->getGames() as $game){
						if($isHome && !empty($game->getMeldungHome())){
							$data['ergebnis_'.$game->getId()] = $game->getMeldungHome();
						} elseif(!$isHome && !empty($game->getMeldungGuest()) ) {
							$data['ergebnis_'.$game->getId()] = $game->getMeldungGuest();
						}
					}
					$ergebnisForm->setData($data);
					
					$forms[$round->getId()]['zeit'] = $zeitForm;
					$forms[$round->getId()]['ergebnis'] = $ergebnisForm;
					break;
				}
			}
		}
		
		$viewMessages = array();
		if($request->isPost()){
			$data = array_merge_recursive(
				$request->getPost()->toArray(),
				$request->getFiles()->toArray()
			);
			foreach($forms as $round => $formIndex){
				if(isset($data['time'])){
					if($this->validateTimereport($formIndex['zeit'], $data)){
						$data = $formIndex['zeit']->getData();
						$match = $em->getRepository('FSMPILoL\Entity\Match')->find((int)$data['match_id']);
						$date = new DateTime($data['time']);
						$isHome = $team == $match->getTeamHome();
						$isComplete = false;
						$isConflict = false;
						if($isHome){
							$match->setTimeHome($date);
							$isComplete = !empty($match->getTimeGuest());
						} else {
							$match->setTimeGuest($date);
							$isComplete = !empty($match->getTimeHome());
						}
						
						if($isComplete){
							$isConflict = $match->getTimeHome() != $match->getTimeGuest();
						}
						
						$em->flush();
						$viewMessages['zeitmeldung_success'] = 'Zeit erfolgreich gespeichert';
						if(!$isComplete){
							$viewMessages['zeitmeldung_success'] .= '. Sie muss noch vom Gegnerteam bestätigt werden!';
						} else if($isConflict) {
							$viewMessages['zeitmeldung_success'] .= '. WARNUNG: Sie stimmt nicht mit der eingetragenen Zeit des Gegerteams überein!';
						}
					}
				} elseif(isset($data['anmerkung'])) {
					if($this->validateErgebnisreport($formIndex['ergebnis'], $data)){
						$data = $formIndex['ergebnis']->getData();
						$uploaddir = './public/img/uploads/';
						$match = $em->getRepository('FSMPILoL\Entity\Match')->find((int)$data['match_id']);
						$isHome = $team == $match->getTeamHome();
						foreach($match->getGames() as $game){
							$ergebnis = $data['ergebnis_'.$game->getId()];
							if($isHome){
								$game->setMeldungHome($ergebnis);
								$game->setAnmerkungHome($data['anmerkung']);
							} else {
								$game->setMeldungGuest($ergebnis);
								$game->setAnmerkungGuest($data['anmerkung']);
							}
							$split = explode('-', $ergebnis);
							
							if(
								( $game->getPointsBlue() === null && $game->getPointsPurple() === null ) ||
								( $isHome && empty($game->getMeldungGuest())  ) || 
								( !$isHome && empty($game->getMeldungHome())  ) ||
								$game->getMeldungGuest() == $game->getMeldungHome()
							){
								if($ergebnis == '1-0' || $ergebnis == '0-1'){
									$game->setPointsBlue($split[1 - ($game->getNumber() % 2)]);
									$game->setPointsPurple($split[$game->getNumber() % 2]);
								} elseif($ergebnis == '+--') {
									$game->setPointsBlue($game->getNumber() % 2 != 0 ? 1 : null);
									$game->setPointsPurple($game->getNumber() % 2 == 0 ? 1 : null);
								} elseif($ergebnis == '--+') {
									$game->setPointsBlue($game->getNumber() % 2 == 0 ? 1 : null);
									$game->setPointsPurple($game->getNumber() % 2 != 0 ? 1 : null);
								} else {
									$game->setPointsBlue(null);
									$game->setPointsPurple(null);
								}
							}
							
							$screen = $data['screen_'.$game->getId()];
							if(!empty($screen['tmp_name'])){
								$ext = pathinfo($screen['name'], PATHINFO_EXTENSION);
								$filename = 'turn'.$tournament->getId() . '_grp'.$group->getNumber().'_ma'.$match->getId().'_sp'.$game->getNumber().($isHome ? 'H' : 'G').'.'.$ext;
								$uploadfile = $uploaddir . $filename;
								if(!is_dir($uploaddir)){
									mkdir($uploaddir, 0770, true);
								}
								if(!move_uploaded_file($screen['tmp_name'], $uploadfile))
									echo "Fehler beim Upload von ".$game->getNumber();
								
								if($isHome){
									$game->setScreenHome('/img/uploads/'.$filename);
								} else {
									$game->setScreenHome('/img/uploads/'.$filename);
								}
							}
						}
						$em->flush();
						$viewMessages['ergebnismeldung_success'] = 'Ergebnis erfolgreich gespeichert';
					}
				}
			}
		}
		
		$this->setTeamdata();
		$this->setAPIData();
		
		return new ViewModel(array('tournament' => $tournament, 'forms' => $forms, 'messages' => $viewMessages));
	}
	
	protected function validateTimereport(ZeitmeldungForm $form, $data){
		$form->setData($data);
		$em = $this->getEntitymanager();
		$identity = $this->zfcUserAuthentication()->getIdentity();
		$tournament = $this->getTournament();
		$player = $identity->getPlayer($tournament);
		$team = $player->getTeam();
		$group = $team->getGroup();
		
		$time = $form->get('time');
		
		if(empty($player) || empty($team) || empty($group)){
			$time->setMessages(array("ID nicht gültig"));
			return false;
		} 
		
		/* Not necessary since both teams have to enter a date.
		if(!$player->getIsCaptain()){
			$time->setMessages(array("Nur Captains können eine Zeit eintragen"));
			return false;
		} */
		
		if(empty($data['time'])){ 
			$time->setMessages(array("Termin nicht eingetragen"));
			return false;
		}
		
		try{
			$date = new DateTime($data['time']);
		} catch (Exception $e) {
			$time->setMessages(array("Datum nicht gültig"));
			return false;
		}
		
		if(empty($data['match_id'])){
			$time->setMessages(array("Match nicht gewählt"));
			return false;
		} 
		
		$match = $em->getRepository('FSMPILoL\Entity\Match')->find((int)$data['match_id']);
		if(empty($match)){
			$time->setMessages(array("Match nicht gefunden"));
			return false;
		}
		if($match->getTeamHome() != $team && $match->getTeamGuest() != $team){
			$time->setMessages(array("Keine Berechtigung, das Match zu bearbeiten"));
			return false;
		}
		if($match->getIsBlocked()){ 
			$time->setMessages(array("Das Match ist bereits abgeschlossen"));
			return false;
		}
		
		$form->setData($data);
		return $form->isValid();
	}
	
	protected function validateErgebnisreport(ErgebnismeldungForm $form, $data){
		$form->setData($data);
		$em = $this->getEntitymanager();
		$identity = $this->zfcUserAuthentication()->getIdentity();
		$player = $identity->getPlayer($this->getTournament());
		$team = $player->getTeam();
		$group = $team->getGroup();
		
		$anmerkung = $form->get('anmerkung');
		
		if(empty($player) || empty($team) || empty($group)){
			$anmerkung->setMessages(array("ID nicht gültig"));
			return false;
		}

		if(empty($data['match_id'])){
			$anmerkung->setMessages(array("Match nicht gewählt"));
			return false;
		} 
		
		$match = $em->getRepository('FSMPILoL\Entity\Match')->find((int)$data['match_id']);
		if(empty($match)){
			$anmerkung->setMessages(array("Match nicht gefunden"));
			return false;
		}
		if($match->getTeamHome() != $team && $match->getTeamGuest() != $team){
			$anmerkung->setMessages(array("Keine Berechtigung, das Match zu bearbeiten"));
			return false;
		}
		if($match->getIsBlocked()){ 
			$anmerkung->setMessages(array("Das Match ist bereits abgeschlossen"));
			return false;
		}
		
		$maxPoints = ceil(count($match->getGames()) / 2);
		$sumH = 0;
		$sumG = 0;
		$ergebnisName = '';
		foreach($match->getGames() as $game){
			$ergebnisName = 'ergebnis_'.$game->getId();
			if(empty($data[$ergebnisName])){
				$form->get($ergebnisName)->setMessages(array("Kein Ergebnis"));
				return false;
			}

			if(!in_array($data[$ergebnisName], array('-', '1-0', '0-1', '+--', '--+'))){
				$form->get($ergebnisName)->setMessages(array("Ungültiges Ergebnis eingetragen"));
				return false;
			}
			
			if($match->getTeamHome() == $team && !empty($game->getMeldungHome() && !$player->getIsCaptain())){
				$form->get($ergebnisName)->setMessages(array("Nur Captains können eine Meldung ändern"));
				return false;
			}
			
			if($match->getTeamGuest() == $team && !empty($game->getMeldungGuest() && !$player->getIsCaptain())){
				$form->get($ergebnisName)->setMessages(array("Nur Captains können eine Meldung ändern"));
				return false;
			}
			
			if ($data[$ergebnisName] == '1-0' || $data[$ergebnisName] == '+--') {
				$sumH++;
			} elseif ($data[$ergebnisName] == '0-1' || $data[$ergebnisName] == '--+') {
				$sumG++;
			}
		}
		
		if($sumH > $maxPoints || $sumG > $maxPoints){
			$form->get($ergebnisName)->setMessages(array("Ungültiges Ergebnis eingetragen"));
			return false;
		}
		
		
		$form->setData($data);
		return $form->isValid();
	}
	
	public function myteamAction(){
		$this->authenticate();
		$tournament = $this->getTournament();
		if (!$tournament) {
			return $this->redirect()->toRoute('teams');
		}

		if (!$this->zfcUserAuthentication()->hasIdentity()) {
			return $this->redirect()->toRoute('teams');
		}

		$identity = $this->zfcUserAuthentication()->getIdentity();
		$player = $identity->getPlayer($tournament);
		
		if($player){
			$team = $player->getTeam();
			if ($team) {
				$group = $team->getGroup();
			}
		}
		
		if (!$player | !$team | !$group) {
			return $this->redirect()->toRoute('teams');
		}

		$this->setAPIData();
		
		return new ViewModel(array('tournament' => $tournament, 'team' => $team));
	}
	
	protected function authenticate(){
		if($this->zfcUserAuthentication()->hasIdentity()){
			return true;
		}
		
        $adapter = $this->zfcUserAuthentication()->getAuthAdapter();
        $result = $adapter->prepareForAuthentication($this->getRequest());
        $auth = $this->zfcUserAuthentication()->getAuthService()->authenticate($adapter);
		return $auth;
	}
	
}
