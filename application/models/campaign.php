<?PHP

class Campaign extends MY_Model {
	
	var $tablename = "campaign";
	var $idfield   = "id";

	function __construct()
    {
        // Call the Model constructor
        parent::__construct();
		$this->load->database();
    }


    function fetch_by_url($url){
    
        $this->db->select("*");
        $this->db->from("campaign");
        $this->db->where("url", $url);
        
        $res = $this->db->get();
        
        return $this->single_result($res);
    }
}

class Campaign_Object extends My_Object {

	protected $model = 'Campaign';

	function view_url(){
		return "/campaign/id/".$this->id;
	}
	function edit_url(){
		return "/campaign/id/".$this->id.'/edit';
	}
}