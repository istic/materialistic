<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class Pledges extends AUTHED_Controller {


	public function from_url(){
		$url = $this->input->post('url');
		$purl = parse_url($url);
		switch(isset($purl['host']) ? $purl['host'] : false){
			case "www.kickstarter.com":
				$dirs = explode('/', $purl['path']);
				return $this->ks_search($dirs[3], $dirs[2]);
				break;

			case false:
				$this->viewdata['error'] = 'That wasn\'t a URL';
				return $this->create_campaign();
				break;

			default:
				$this->viewdata['error'] = 'Sorry, don\'t know how to grab data from '.$purl['host'].' automatically yet, you\'ll have to do this manually';
				return $this->create_campaign();
		}
	}

	public function undeliver(){
		$this->load->model("Pledge");

		$pledge_id = $this->input->get('id');
		if(!$pledge_id){
			return $this->error(404);
		}

		$pledge = $this->Pledge->fetch_by_id($pledge_id);
		if( !$pledge || $pledge->user_id != $this->current_user->id ){
			return $this->error(404);
		}

		$pledge->is_delivered = "No";
		$pledge->date_delivered = 0;
		$pledge->save();
		return $this->redirect('/my/projects');
	}


	public function fail(){
		$this->load->model("Pledge");

		$pledge_id = $this->input->get('id');
		if(!$pledge_id){
			return $this->error(404);
		}

		$pledge = $this->Pledge->fetch_by_id($pledge_id);
		if( !$pledge || $pledge->user_id != $this->current_user->id ){
			return $this->error(404);
		}

		$pledge->is_delivered = "Failed";
		$pledge->date_delivered = 0;
		$pledge->save();
		return $this->redirect('/my/projects');
	}

	public function unfail(){
		$this->load->model("Pledge");

		$pledge_id = $this->input->get('id');
		if(!$pledge_id){
			return $this->error(404);
		}

		$pledge = $this->Pledge->fetch_by_id($pledge_id);
		if( !$pledge || $pledge->user_id != $this->current_user->id ){
			return $this->error(404);
		}

		$pledge->is_delivered = "Waiting";
		$pledge->date_delivered = 0;
		$pledge->save();
		return $this->redirect('/my/projects');
	}

	public function delivered(){
		$this->load->model("Pledge");
		$this->load->model('Campaign');
        $this->load->library('form_validation');

		$pledge_id = $this->input->get_post('id');
		if(!$pledge_id){
			return $this->error(404);
		}

		$pledge = $this->Pledge->fetch_by_id($pledge_id);
		if( !$pledge || $pledge->user_id != $this->current_user->id ){
			return $this->error(404);
		}

		$this->viewdata['pledge']   = $pledge;
		$this->viewdata['campaign'] = $pledge->campaign();


		$req = 'required|trim|xss_clean';
		$nreq = 'trim|xss_clean';

        $this->form_validation->set_rules('is_delivered', 'Delivered Status', 'required|trim|xss_clean|callback_valid_deliver_status');
        $this->form_validation->set_rules('date_delivered', 'Delivered Date', $nreq.'|callback_valid_date[date_delivered]');

        if ($this->form_validation->run() == FALSE) {
            $this->render('pledge/delivered');
        } else {
        	$pledge->is_delivered   = $this->input->post('is_delivered');
        	if($pledge->is_delivered == 'No'){
        		$pledge->date_delivered = 0;
        	} else {
               	$pledge->date_delivered = $this->input->post('date_delivered');
            }
        	$pledge->save();
        	return $this->redirect('/my/projects');
        }
	}


	public function kickstarter(){
		return $this->ks_search($this->input->post('query'));
	}

	public function create_campaign(){
        $this->load->library('form_validation');
        $this->load->model('Campaign');
		$this->render('campaign/create');
	}

	public function ks_search($project, $creator = false){
		$this->load->library('Kickstarter');
		$this->load->model('Campaign');

		$canonical_url = $this->kickstarter->canonical_url($creator, $project);
		$campaign = $this->Campaign->fetch_by_url($canonical_url);

		if($campaign){
			$this->redirect('/pledges/create?campaign='.$campaign->id);
		} else {
			$query = strtr($project, '-', ' ');
			$search = $this->kickstarter->search($query);
			$campaigns = $this->kickstarter->create_from_search_results($search);
			if(count($campaigns) == 1){
				$this->redirect('/pledges/create?campaign='.$campaigns[0]->id);
			} else {
				$this->viewdata['campaigns'] = $campaigns;
				$this->viewdata['query'] = $query;
				$this->render('pledge/ks_search');
			}
		}
	}

	public function edit(){
		return $this->create();
	}

	public function create(){
		$this->load->model('Campaign');
		$this->load->model('Pledge');
        $this->load->library('form_validation');
        $this->load->library('kickstarter');

        $pledge_id = $this->input->get_post('id', TRUE);
        $pledge = new Pledge_Object;
        if($pledge_id){
        	$pledge = $this->Pledge->fetch_by_id($pledge_id);
        	$campaign_id = $pledge->campaign_id;
        	if(!$pledge_id){
        		return $this->error(404);
        	}
        	if($pledge->user_id != $this->current_user->id){
        		return $this->error(404);
        	}
        } else {
	       	$campaign_id = $this->input->get_post('campaign_id', TRUE);
	       	if(!$campaign_id){
	       		$campaign_id = $this->input->get_post('campaign', TRUE);
	       	}
        }

        $this->viewdata['pledge'] = $pledge;

		if($campaign_id){
			$campaign = $this->Campaign->fetch_by_id($campaign_id);
			$this->viewdata['campaign'] = $campaign;
			if(!$campaign){
				return $this->error(404);
			}
		} else {
			return $this->error(404);
		}

		$campaign_data = $this->kickstarter->campaign_data($campaign->URL);
		if($campaign_data){
			$this->viewdata['rewards'] = $campaign_data->rewards;
		}

		$req = 'required|trim|xss_clean';
		$nreq = 'trim|xss_clean';

        $this->form_validation->set_rules('backing_tier', 'Backing Tier', $req);
        $this->form_validation->set_rules('description', 'Description', $nreq);
        $this->form_validation->set_rules('value', 'Pledge', $nreq.'|numeric');
        $this->form_validation->set_rules('is_delivered', 'Delivered Status', 'required|trim|xss_clean|callback_valid_deliver_status');

        $this->form_validation->set_rules('date_promised', 'Promised Date', $req.'|callback_valid_date[date_promised]');
        $this->form_validation->set_rules('date_reasonable', 'Reasonable Date', $nreq.'|callback_valid_date[date_reasonable]');
        $this->form_validation->set_rules('date_delivered', 'Delivered Date', $nreq.'|callback_valid_date[date_delivered]');

        if ($this->form_validation->run() == FALSE) {
            $this->render('pledge/create');
        } else {
        	var_dump($_POST);
        	if($this->input->post('id')){
        		$pledge = $this->Pledge->fetch_by_id($this->input->post('id'));
        	} else {
        		$pledge = new Pledge_Object();
        	}

        	if($this->input->post('date_delivered')){
        		$delivered = date(DATETIME_MYSQL, strtotime($this->input->post('date_delivered')));
        	} else {
        		$delivered = false;
        	}

			$pledge->campaign_id     = $campaign->id;
			$pledge->user_id         = $this->current_user->id;
			$pledge->backing_tier    = $this->input->post('backing_tier');
			$pledge->description     = $this->input->post('description');
			$pledge->value           = $this->input->post('value');
			$pledge->is_delivered    = $this->input->post('is_delivered');
			if($pledge_id){
				$pledge->date_created    = date(DATETIME_MYSQL);
			}
			$pledge->date_modified   = date(DATETIME_MYSQL);
			if($this->input->post('date_promised')){
				$pledge->date_promised = date(DATETIME_MYSQL, strtotime($this->input->post('date_promised')));
			} else {
				$pledge->date_promised = false;
			}

			if($this->input->post('date_reasonable')){
				$pledge->date_reasonable = date(DATETIME_MYSQL, strtotime($this->input->post('date_reasonable')));
			} else {
				$pledge->date_reasonable = false;
			}

			$pledge->date_delivered  = $delivered;
			$pledge->save();
			$this->redirect('/');
        }
	}

    public function valid_deliver_status($str) {

        $statuses = array("No", "Partially", "Yes");

        if (!in_array($str, $statuses)) {
            $this->form_validation->set_message('is_delivered', 'Not a valid delivery status');
            return FALSE;
        } else {
            return TRUE;
        }
    }
    public function valid_date($str, $field) {

        if(strtotime($str) || !$str){
        	return true;
        } else {
            $this->form_validation->set_message('valid_date', 'Not a valid date in %s');
            return FALSE;
        }
    }
}
