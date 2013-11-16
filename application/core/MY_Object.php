<?PHP


abstract class MY_Object {
	
	private $CI;
	private $data;

	function __construct($data = false){
		if($data){
			$this->data = (Array)$data;
		
			if(method_exists($this, "setup")){
				$this->setup();
			}	
		
		} else {
			$this->data = array();
		}
		
		$this->CI =& get_instance();
	}
	
	abstract function view_url();
	abstract function edit_url();
	
	function __get($key){
		if(isset($this->data[$key])){
			return $this->data[$key];
		}
		
		if(isset($this->$key)){
			return $this->$key;
		}
	}
	
	function __set($key, $value){
		$validation_function = "_validate_".$key;
		if(method_exists($this, $validation_function)){
			$value = $this->$validation_function($value);
		}
		
		$this->data[$key] = $value;
	}
	
	function __isset($key){
		return(isset($this->$key) || isset($this->data[$key]));		
	}
	
	
	function get_data(){
		return $this->data;
	}
	function set_data($data){
		return $this->data = $data;
	}

	function save(){
		if(!$this->model){
			throw new Exception("Model not defined for ".get_class($this));
		}
		$model = $this->model;
		$this->CI->load->model($model);
		$id = $this->CI->$model->save($this->data);
		$this->id = $id;
		return;
	}
	
}