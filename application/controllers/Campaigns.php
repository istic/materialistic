<?PHP

class Campaigns extends AUTHED_Controller {

	public function create(){
        $this->load->library('form_validation');
        $this->load->model('Campaign');

        $this->form_validation->set_rules('name', 'Name', 'required|trim');
        $this->form_validation->set_rules('url', 'URL', 'required|trim|is_unique[campaign.URL]');
        $this->form_validation->set_rules('target', 'Target', 'required|trim|integer');

        $this->form_validation->set_rules('status', 'Status', 'required|trim|callback_valid_status');
        $this->form_validation->set_rules('creator', 'Creator', 'required|trim');
        $this->form_validation->set_rules('currency', 'Currency', 'required|trim|exact_length[3]');
        $this->form_validation->set_rules('category', 'Category', 'required|trim');
        $this->form_validation->set_rules('country', 'Country', 'required|trim|exact_length[2]');

        if ($this->form_validation->run() == FALSE) {
        	$validator =& _get_validation_object();
			$error_messages = $this->form_validation->error_array();
			if(isset($error_messages['url']) && $error_messages['url'] == 'The URL field must contain a unique value.'){
				$campaign = $this->Campaign->fetch_by_column("url", $this->input->post('url'));
				$this->redirect('/pledges/create?campaign='.$campaign->id);
				return;
			}
            $this->render('campaign/create');
        } else {
	        $campaign = new Campaign_Object();
			$campaign->name         = $this->input->post('name');
			$campaign->URL          = $this->input->post('url');
			$campaign->target       = $this->input->post('target');
			$campaign->pledged      = false;
			$campaign->backer_count = false;
			$campaign->site         = 'manual';

			// ENUM('live', 'successful', 'failed', 'suspended', 'deleted', 'canceled');
			$campaign->status   = $this->input->post('status');
			$campaign->vitality = ($campaign->status == 'successful' || $campaign->status == 'live');

			$campaign->creator  = $this->input->post('creator');
			$campaign->currency = $this->input->post('currency');
			$campaign->category = $this->input->post('category');
			$campaign->photo    = false;
			$campaign->country  = $this->input->post('country');

			$campaign->date_start    = false;
			$campaign->date_end      = false;
			$campaign->date_checked  = false;
			$campaign->date_created  = date(DATETIME_MYSQL);
			$campaign->date_modified = date(DATETIME_MYSQL);
			$campaign->save();
			$this->redirect('/pledges/create?campaign='.$campaign->id);
		}

		
	}

    public function valid_status($str) {

        $statuses = array('live', 'successful', 'failed', 'suspended', 'deleted', 'canceled');

        if (!in_array($str, $statuses)) {
            $this->form_validation->set_message('status', 'Not a valid status');
            return FALSE;
        } else {
            return TRUE;
        }
    }

}

