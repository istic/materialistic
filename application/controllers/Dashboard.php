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
		$this->requires_authentication();

		$this->viewdata['navsection'] = 'projects';

        $this->load->model('Pledge');
		$this->viewdata['pledges'] = $this->Pledge->pledges_by_user($this->current_user);

		$this->render('dashboard/projects');
	}

	public function reasonable(){
		$reasonable = $this->session->userdata('reasonable_mode');
		$this->session->set_userdata('reasonable_mode', !$reasonable);
		redirect($this->input->get('return'));
	}

	public function stats(){
		$this->requires_authentication();

		$this->viewdata['navsection'] = 'stats';

        $this->load->model('Pledge');
		$this->viewdata['pledges'] = $this->Pledge->pledges_by_user($this->current_user);

		if(!count($this->viewdata['pledges'])){
			redirect("/my/projects");
		}

		$this->render("dashboard/stats");
	}

	public function inflight(){
		$this->requires_authentication();

		$this->viewdata['navsection'] = 'inflight';

		$this->render("dashboard/inflight");
	}

	public function category(){
		$this->requires_authentication();

		$this->viewdata['navsection'] = 'category';

        $this->load->model('Pledge');
		$this->viewdata['pledges'] = $this->Pledge->pledges_by_user($this->current_user);
		
		$this->render("dashboard/category");
	}

	public function monthly(){
		$this->requires_authentication();

		$this->viewdata['navsection'] = 'monthly';


        $this->load->model('Pledge');
		$this->viewdata['pledges'] = $this->Pledge->pledges_by_user($this->current_user);
		
		$this->render("dashboard/monthly");
	}

	public function lateness($status = false){
		$this->requires_authentication();

	
		$this->viewdata['navsection'] = 'lateness';


        $this->load->model('Pledge');
		$this->viewdata['pledges'] = $this->Pledge->pledges_by_user($this->current_user, $status);
		
		$this->render("dashboard/lateness");
	}

}
