<?PHP

class OpenExchangeRates {
	private $CI;

	function __construct(){
		$this->CI =& get_instance(); 
	}
	
	public function get_from_api($from, $to, $date = false){
		$key = $this->CI->config->item('openexchange_key');

		$route = 'http://openexchangerates.org/api/';
		if($date){
			$endpoint = $route.'historical/'.$date.'.json?app_id='.$key;
		} else {
			$endpoint = $route.'latest.json?app_id='.$key;
		}
		$data = file_get_contents($endpoint);

		$query = array(
			'date'        => $date ? $date : date("Y-m-d"),
			'conversions' => $data
		);

		$this->CI->db->insert('currency_conversion', $query);
		

		$data = json_decode($data);

		return $data;

	}

	public function get_from_database($from, $to, $date = false){
		if(!$date){
			$date = date("Y-m-d");
		}

        $this->CI->db->select("*");
        $this->CI->db->from("currency_conversion");
        $this->CI->db->where("date", $date);
        
        $res = $this->CI->db->get();
        $result = $res->row();
        if($result){
        	return json_decode($result->conversions);
        } else {
        	return false;
        }
		
	}

	public function convert($value, $from, $to, $date = false){
		if($from == $to){
			return $value;
		}

		$data = $this->get_from_database($from, $to, $date);
		if(!$data){
			$data = $this->get_from_api($from, $to, $date);
		} 

		// First, convert everything to dollars.
		if($from == 'USD'){
			$USD = $value;
		} else {
			$to_usd = $data->rates->$from;
			$USD = $value * $to_usd;
		}

		$to_value = $data->rates->$to;

		// Then, convert

		return round($USD * $to_value);

	}
}