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

    function pledges_by_user(User_Object $user){

        $this->db->select("pledge.*");
        $this->db->from("pledge");
        $this->db->where("user_id", $user->id);
        $this->db->join('campaign', 'campaign.id = pledge.campaign_id');
        $this->db->order_by('campaign.name');
        
        $res = $this->db->get();
        
        return $this->multiple_results($res);
    }

}

class Pledge_Object extends My_Object {

	protected $model = 'Pledge';
	protected $campaign = false;

	function view_url(){
		return "/pledge/id/".$this->id;
	}
	function edit_url(){
		return "/pledge/id/".$this->id.'/edit';
	}

	function is_late(){
		if($this->date_delivered !== '0000-00-00'){
			return strtotime($this->date_delivered) > strtotime($this->date_promised);
		} else {
			return time() > strtotime($this->date_promised);
		}
	}

	function campaign(){
		if(!$this->campaign){
			$this->CI->load->model("Campaign");
			$this->campaign = $this->CI->Campaign->fetch_by_id($this->campaign_id);
		}
		return $this->campaign;
	}

	function convert_to_currency($currency){
		$this->CI->load->library("openexchangerates");
		if(  strtotime($this->date_ended) > time() || $this->date_ended == '0000-00-00' ){
			$date = false;
		} else {
			$date = $this->date_ended;
		}
		return $this->CI->openexchangerates->convert($this->value, $this->campaign()->currency, $currency, $date);
	}
}