<?PHP

use GuzzleHttp\Client;

class Indiegogo {
	private $CI;

	function __construct(){
		$this->CI =& get_instance(); 
	}

	function campaign_data($url){
		// https://www.indiegogo.com/private_api/campaigns/an-ode-to-divine-dirt-an-original-grimoire

		$matches = array();
		$regex = "#projects/(.*?)/#s";
		$works = preg_match_all($regex, $url, $matches);
		if(!$works){
			die("Parsing failed");
		}

		// $data = $this->project_page($url);

		$url = 'https://www.indiegogo.com/private_api/campaigns/'.$matches[1][0];
		return json_decode($this->project_page($url));
		
	}


	function create_from_url($url){

		$this->CI->load->model('Campaign');

		if($campaign = $this->CI->Campaign->fetch_by_url($url)){
			return $campaign;
		}

		$campaign = new Campaign_Object();

		$project = $this->campaign_data($url)->response;
		// $project = $data['campaign'];

		// header("Content-Type: Application/JSON");
		// print json_encode($project);
		// die();

		// die($project->name);


		$campaign->name         = $project->title;
		$campaign->URL          = $url;
		$campaign->target       = $project->goal/100;
		$campaign->pledged      = $project->collected_funds/100;
		$campaign->backer_count = $project->contributions_count;
		$campaign->site         = 'indiegogo';

		// ENUM('live', 'successful', 'failed', 'suspended', 'deleted', 'canceled');

		$started = strtotime($project->funding_started_at);
		$ended   = strtotime($project->funding_ends_at);
		$now = time();

		if($now > $ended){
			$state = 'successful';
		} elseif($now > $started){
			$state = 'live';
		} else {
			$state = 'suspended';
		} 

		$campaign->status   = $state;
		$campaign->vitality = ($state == 'successful' || $state == 'live');

		$campaign->creator  = $project->owner_name;
		$campaign->currency = $project->currency->iso_code;
		$campaign->category = $project->category->text;
		$campaign->photo    = $project->video_overlay_url;
		$campaign->country  = $project->country_code_alpha_2;

		$campaign->date_start    = date(DATETIME_MYSQL, strtotime($project->funding_started_at));
		$campaign->date_end      = date(DATETIME_MYSQL, strtotime($project->funding_ends_at));
		$campaign->date_checked  = date(DATETIME_MYSQL);
		$campaign->date_created  = date(DATETIME_MYSQL);
		$campaign->date_modified = date(DATETIME_MYSQL);

		$campaign->save();
		return $campaign;
	}


	function project_page($url){
		$cachefile = '/tmp/materialistic-indiegogo-'.md5($url);
		if(file_exists($cachefile)){
			// return file_get_contents($cachefile);
		}
		$client = new Client([
            // Base URI is used with relative requests
            // 'base_uri' => 'https://trello.com/',
            // You can set any number of default request options.
            'timeout'  => 2.0,
        ]);
        // $response = $client->request('GET', $url, ['debug' => true, 'User-Agent' => 'Materialist (Like Gecko)',]);
        $response = $client->request('GET', $url, ['debug' => false, 'headers' => ['User-Agent' => 'Materialist (Like Gecko)',]]);
    
        $data = (String)$response->getBody();

        if(!$response->getStatusCode() === 200) {
			die("X".$response->getStatusCode());
            return false;
        }

        $data = (String)$response->getBody();

		file_put_contents($cachefile, $data);

		return $data;
	}

	function rewards($url){
		$project = $this->campaign_data($url)->response;
		// header("Content-Type: Application/JSON");
		// print json_encode($project);
		// die();


		$rewards = array();
		foreach($project->perks as $perk){
			$reward = new stdClass();

			$eta = strtotime($perk->estimated_delivery_date);

			$reward->id = $perk->id;
			$reward->description = $perk->description;
			$reward->title = $perk->label;
			$reward->estimated_delivery_on = $eta;
			$reward->minimum = $perk->amount;
			$rewards[$reward->id] = $reward;

		}

		return $rewards;
	}
}
