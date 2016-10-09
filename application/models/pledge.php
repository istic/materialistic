<?PHP

class Pledge extends MY_Model {
	
	var $tablename = "pledge";
	var $idfield   = "id";

	function __construct()
    {
        // Call the Model constructor
        parent::__construct();
		$this->load->database();
    }

    function pledges_by_user(User_Object $user, $status = false){

        $this->db->select("pledge.*");
        $this->db->from("pledge");
        $this->db->where("user_id", $user->id);
        $this->db->join('campaign', 'campaign.id = pledge.campaign_id');
        $this->db->order_by('campaign.name');
        
        if($status == "waiting"){
        	$this->db->where_in("is_delivered", ["No", "Failed", "Partially"]);
        } elseif($status == "delivered"){
        	$this->db->where("is_delivered", "Yes");
        }

        $res = $this->db->get();
        
        return $this->multiple_results($res);
    }

    function pledges_for_campaign(User_Object $user, Campaign_Object $campaign){

        $this->db->select("pledge.*");
        $this->db->from("pledge");
        $this->db->where("user_id", $user->id);
        $this->db->where("campaign_id", $campaign->id);
        $this->db->join('campaign', 'campaign.id = pledge.campaign_id');
        
        $res = $this->db->get();
        return $this->single_result($res);
        

    }   
}

class Pledge_Object extends My_Object {

	protected $model = 'Pledge';
	protected $campaign = false;

	public function deadline(){
		if(REASONABLE){
			if($this->date_reasonable_if_exists()){
				return $this->date_reasonable;
			} else {
				return $this->date_promised;
			}
		} else {
			return $this->date_promised;
		}
	}

	function view_url(){
		return "/pledge/id/".$this->id;
	}
	function edit_url(){
		return "/pledge/id/".$this->id.'/edit';
	}

	function lateness($group = false){
		if(!$group){
			$group = (60*60*24*7);
		}

		if($this->date_delivered !== '0000-00-00'){
			return (strtotime($this->date_delivered) - strtotime($this->deadline())) / $group;
		} else {
			return (time() - strtotime($this->deadline())) / $group;
		}
	}

	function date_delivered_if_exists(){
		if($this->date_delivered !== '0000-00-00'){
			return $this->date_delivered;
		} else {
			return '';
		}
	}

	function date_reasonable_if_exists(){
		if($this->date_reasonable !== '0000-00-00'){
			return $this->date_reasonable;
		} else {
			return '';
		}
	}

	function date_promised_if_exists(){
		if($this->date_promised !== '0000-00-00'){
			return $this->date_promised;
		} else {
			return '';
		}
	}

	function is_late(){
		if($this->date_delivered !== '0000-00-00'){
			return strtotime($this->date_delivered) > strtotime($this->deadline());
		} else {
			return time() > strtotime($this->deadline());
		}
	}

	function campaign(){
		if(!$this->campaign){
			$this->CI->load->model("Campaign");
			$this->campaign = $this->CI->Campaign->fetch_by_id($this->campaign_id);
		}
		return $this->campaign;
	}

	function status(){
		if($this->is_delivered == "Yes"){
			if( $this->is_late() ){
				return "Arrived Late";
			} else {
				return "Arrived";
			}
		} elseif($this->is_delivered == "Failed"){
			return 'Failed';
			continue;
		} else {
			if( $this->is_late() ){
				return "In Progress, but Late";
			} else {
				return "In Progress";
			}
		}


	}

	function convert_to_currency($currency){
		if(!isset($this->CI->openexchangerates)){
		    $this->CI->load->library("openexchangerates");
		}

		if(  strtotime($this->date_ended) > time() || $this->date_ended == '0000-00-00' ){
			$date = false;
		} else {
			$date = $this->date_ended;
		}
		return $this->CI->openexchangerates->convert($this->value, $this->campaign()->currency, $currency, $date);
	}

	function delete(){
		$this->CI->db->where('id', $this->id);
		$this->CI->db->delete('pledge');
		return true;
	}
}
