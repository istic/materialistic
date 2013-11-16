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

}

class Pledge_Object extends My_Object {

	protected $model = 'Pledge';

	function view_url(){
		return "/pledge/id/".$this->id;
	}
	function edit_url(){
		return "/pledge/id/".$this->id.'/edit';
	}
}