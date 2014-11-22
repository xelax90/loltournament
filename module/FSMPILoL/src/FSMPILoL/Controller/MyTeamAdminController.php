<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace FSMPILoL\Controller;

/**
 * Description of MyTeamAdminController
 *
 * @author schurix
 */
class MyTeamAdminController extends TeamAdminController{
	protected function _redirectToTeams() {
		return $this->redirect()->toRoute('zfcadmin/myteams');
	}
}
