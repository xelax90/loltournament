<?php

namespace FSMPILoL\Controller;

use FSMPILoL\Form\CommentPaarungForm;
use FSMPILoL\Form\ResultPaarungForm;
use Zend\View\Model\ViewModel;
use FSMPILoL\Entity\Match;

/**
 * Description of TournamentAdminController
 *
 * @author schurix
 */
class TournamentAdminController extends AbstractTournamentAdminController{
	public function paarungenAdminAction(){
		$tournament = $this->getTournament()->getTournament();
		if(!$tournament){
			return new ViewModel();
		}
		
		$this->getTournament()->setTeamdata();
		$this->getTournament()->setAPIData();
		
		return new ViewModel(array('tournament' => $tournament));
	}
	
	protected function _redirectToPaarungen(){
		return $this->redirect()->toRoute('zfcadmin/paarungen');
	}
	
	public function paarungBlockAction(){
		$tournament = $this->getTournament();
		if(!$tournament){
			return $this->_redirectToPaarungen();
		}
        $matchId = $this->getEvent()->getRouteMatch()->getParam('match_id');
		$em = $this->getEntityManager();
		$match = $em->getRepository(Match::class)->find((int)$matchId);
		if(!$match){
			return $this->_redirectToPaarungen();
		}
		
		$match->setIsBlocked(true);
		$em->flush();
		
		return $this->_redirectToPaarungen();
	}

	public function paarungUnblockAction(){
		$tournament = $this->getTournament();
		if(!$tournament){
			return $this->_redirectToPaarungen();
		}
        $matchId = $this->getEvent()->getRouteMatch()->getParam('match_id');
		$em = $this->getEntityManager();
		$match = $em->getRepository(Match::class)->find((int)$matchId);
		if(!$match){
			return $this->_redirectToPaarungen();
		}
		$match->setIsBlocked(false);
		$em->flush();
		
		return $this->_redirectToPaarungen();
	}
	
	public function paarungCommentAction(){
		$tournament = $this->getTournament();
		if(!$tournament){
			return $this->_redirectToPaarungen();
		}
        $matchId = $this->getEvent()->getRouteMatch()->getParam('match_id');
		$em = $this->getEntityManager();
		$match = $em->getRepository(Match::class)->find((int)$matchId);
		if(!$match){
			return $this->_redirectToPaarungen();
		}
		$form = $this->getServiceLocator()->get('FormElementManager')->get(CommentPaarungForm::class);
		$form->setBindOnValidate(false);
		$form->bind($match);
		
        /** @var $request \Zend\Http\Request */
        $request = $this->getRequest();
		if ($request->isPost()) {
			$form->setData($request->getPost());
			if ($form->isValid()) {
				$form->bindValues();
				$em->flush();
				return $this->_redirectToPaarungen();
			}
        }
		return new ViewModel(array('id' => $match->getId(), 'form' => $form));
	}
	
	public function paarungSetResultAction(){
		$tournament = $this->getTournament();
		if(!$tournament){
			return $this->_redirectToPaarungen();
		}
        $matchId = $this->getEvent()->getRouteMatch()->getParam('match_id');
		$em = $this->getEntityManager();
		$match = $em->getRepository(Match::class)->find((int)$matchId);
		if(!$match){
			return $this->_redirectToPaarungen();
		}
		$form = $this->getServiceLocator()->get('FormElementManager')->get(ResultPaarungForm::class);
		$form->setBindOnValidate(false);
		$form->bind($match);
		
        /** @var $request \Zend\Http\Request */
        $request = $this->getRequest();
		if ($request->isPost()) {
			$form->setData($request->getPost());
			if ($form->isValid()) {
				$form->bindValues();
				$match->setIsBlocked(true);
				$em->flush();
				return $this->_redirectToPaarungen();
			}
        }
		return new ViewModel(array('id' => $match->getId(), 'form' => $form));
	}
}
