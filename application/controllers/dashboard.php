<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class Dashboard extends MY_Controller {

	public function index()
	{
		if($this->current_user->logged_in()){
			return $this->dashboardAction();
		} else {
			$this->viewdata['title'] = "Front Page";
			//$this->viewdata['subtitle'] = "";
			$this->render('welcome_message');
		}
	}

	public function dashboardAction(){
		$this->render('dashboard');
	}
}
