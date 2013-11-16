<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class Pledges extends AUTHED_Controller {


	public function from_url(){
		$url = $this->input->post('url');
		$purl = parse_url($url);
		switch(isset($purl['host']) ? $purl['host'] : false){
			case "www.kickstarter.com":
				$dirs = explode('/', $purl['path']);
				return $this->ks_search($dirs[2], $dirs[3]);
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

	public function create_campaign(){
        $this->load->library('form_validation');
        $this->load->model('Campaign');
		$this->render('campaign/create');
	}

	public function ks_search($creator, $project){
		$this->load->library('Kickstarter');
		$this->load->model('Campaign');

		$canonical_url = $this->kickstarter->canonical_url($creator, $project);
		$campaign = $this->Campaign->fetch_by_url($canonical_url);

		if($campaign){
			$this->redirect('/pledges/create?campaign='.$campaign->id);
		} else {
			$query = $project;
			$search = $this->kickstarter->search($query);
			$campaigns = $this->kickstarter->create_from_search_results($search);
			if(count($campaigns) == 1){
				$this->redirect('/pledges/create?campaign='.$campaigns[0]->id);
			} else {
				$this->viewdata['campaigns'] = $campaigns;
				$this->render('pledges/ks_search');
			}
		}
	}

	public function create(){
		$this->load->model('Campaign');
		$this->load->model('Pledge');
        $this->load->library('form_validation');

       	$campaign_id = $this->input->get_post('campaign_id', TRUE);
       	if(!$campaign_id){
       		$campaign_id = $this->input->get_post('campaign', TRUE);
       	}
		
		if($campaign_id){
			$campaign = $this->Campaign->fetch_by_id($campaign_id);
			$this->viewdata['campaign'] = $campaign;
			if(!$campaign){
				return $this->error(404);
			}
		} else {
			return $this->error(404);
		}

		$req = 'required|trim|xss_clean';
		$nreq = 'trim|xss_clean';

        $this->form_validation->set_rules('backing_tier', 'Backing Tier', $req);
        $this->form_validation->set_rules('description', 'Description', $nreq);
        $this->form_validation->set_rules('value', 'Pledge', $nreq.'|decimal');
        $this->form_validation->set_rules('is_delivered', 'Delivered Status', 'required|trim|xss_clean|callback_valid_deliver_status');

        $this->form_validation->set_rules('date_promised', 'Promised Date', $req.'|callback_valid_date[date_promised]');
        $this->form_validation->set_rules('date_reasonable', 'Reasonable Date', $req.'|callback_valid_date[date_reasonable]');
        $this->form_validation->set_rules('date_delivered', 'Delivered Date', $nreq.'|callback_valid_date[date_delivered]');

        if ($this->form_validation->run() == FALSE) {
            $this->render('pledge/create');
        } else {
        	if($this->input->post('id')){
        		$pledge = $this->Pledge->fetch_by_id($this->input->post('id'));
        	} else {
        		$pledge = new Pledge_Object();
        	}

        	if($this->input->post('date_delivered')){
        		$delivered = date(DATETIME_MYSQL, strtotime($this->input->post('date_delivered')));
        	} else {
        		$delivered = '';
        	}

			$pledge->campaign_id     = $campaign->id;
			$pledge->user_id         = $this->current_user->id;
			$pledge->backing_tier    = $this->input->post('backing_tier');
			$pledge->description     = $this->input->post('description');
			$pledge->value           = $this->input->post('value');
			$pledge->is_delivered    = $this->input->post('is_delivered');
			$pledge->date_created    = date(DATETIME_MYSQL);
			$pledge->date_modified   = date(DATETIME_MYSQL);
			$pledge->date_promised   = date(DATETIME_MYSQL, strtotime($this->input->post('date_promised')));
			$pledge->date_reasonable = date(DATETIME_MYSQL, strtotime($this->input->post('date_reasonable')));
			$pledge->date_delivered  = $delivered;
			$pledge->save();
			$this->redirect('/dashboard');
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
