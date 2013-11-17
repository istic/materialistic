<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class Dashboard extends MY_Controller {

	public function index()
	{
		if($this->current_user->logged_in()){
			return $this->stats();
		} else {
			$this->viewdata['title'] = "Front Page";
			//$this->viewdata['subtitle'] = "";
			$this->render('docs/about');
		}
	}

	public function projects(){
		$this->viewdata['navsection'] = 'projects';

        $this->load->model('Pledge');
		$this->viewdata['pledges'] = $this->Pledge->pledges_by_user($this->current_user);

		$this->render('dashboard/projects');
	}

	public function stats(){
		$this->viewdata['navsection'] = 'stats';

        $this->load->model('Pledge');
		$this->viewdata['pledges'] = $this->Pledge->pledges_by_user($this->current_user);

		$this->render("dashboard/stats");
	}

	public function inflight(){
		$this->viewdata['navsection'] = 'inflight';

		$this->render("dashboard/inflight");
	}

	public function category(){
		$this->viewdata['navsection'] = 'category';

		$this->render("dashboard/category");
	}

	public function monthly(){
		$this->viewdata['navsection'] = 'monthly';

		$this->render("dashboard/monthly");
	}

	public function lateness(){
		$this->viewdata['navsection'] = 'lateness';

		$this->render("dashboard/lateness");
	}

}
