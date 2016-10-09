<?PHP

use GuzzleHttp\Client;

class Indiegogo {
	private $CI;

	function __construct(){
		$this->CI =& get_instance(); 
	}

	function campaign_data($url){
		$data = $this->project_page($url);

		var_dump(htmlentities($url));
		var_dump(htmlentities($data));die();
		$regex = "#<script>(.*?)</script>#s";
		$works = preg_match_all($regex, $data, $matches);
		if(!$works){
			die("Parsing failed");
		}

		$script_data = $matches[1][0];

		var_dump(htmlentities($script_data));die();

		$regex = '#FIG_CACHE\["(.*?)"\] \= (.*?);$#sm';
		$works = preg_match_all($regex, $script_data, $matches);
		if(!$works){
			die("Parsing failed");
		}



		$project = array();

		foreach($matches[1] as $i => $data){
			$project[$matches[1][$i]] = json_decode($matches[2][$i]);
		}

		return $project;
	}


	function create_from_url($url){

		$this->CI->load->model('Campaign');

		if($campaign = $this->CI->Campaign->fetch_by_url($url)){
			return $campaign;
		}

		$campaign = new Campaign_Object();

		$data = $this->campaign_data($url);
		$project = $data['campaign'];

		// header("Content-Type: Application/JSON");
		// print json_encode($project);
		// die();


		$campaign->name         = $project->title;
		$campaign->URL          = $url;
		$campaign->target       = $project->goal_no_cents;
		$campaign->pledged      = $project->total_pledged_cents/100;
		$campaign->backer_count = $project->total_fans;
		$campaign->site         = 'fig';

		// ENUM('live', 'successful', 'failed', 'suspended', 'deleted', 'canceled');

		if($project->ended && $project->success){
			$state = 'successful';
		} elseif($project->ended && !$project->success){
			$state = 'failed';
		} elseif(!$project->ended){
			$state = 'live';
		} 

		$campaign->status   = $state;
		$campaign->vitality = ($project->state == 'successful' || $project->state == 'live');

		$campaign->creator  = $project->company->name;
		$campaign->currency = $project->currency;
		$campaign->category = "Computer Games";
		$campaign->photo    = $project->featured_image_url;
		$campaign->country  = "US";

		$campaign->date_start    = date(DATETIME_MYSQL, strtotime($project->start_timestamp));
		$campaign->date_end      = date(DATETIME_MYSQL, strtotime($project->end_timestamp));
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
        echo "<pre>";
        // $response = $client->request('GET', $url, ['debug' => true, 'User-Agent' => 'Materialist (Like Gecko)',]);
        $response = $client->request('GET', $url, ['debug' => true, 'headers' => ['User-Agent' => 'Materialist (Like Gecko)',]]);

        var_dump($request->headers);
    
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
		$data = $this->campaign_data($url);

		$rewards = array();
		foreach($data['reward_bundles'] as $reward_src){
			$reward = new stdClass();

			$eta = strtotime($reward_src->delivery_year."-".$reward_src->delivery_month."-01");

			$reward->id = $reward_src->id;
			$reward->description = $reward_src->description;
			$reward->title = $reward_src->name;
			$reward->estimated_delivery_on = $eta;
			$reward->minimum = $reward_src->price_cents/100;
			$rewards[$reward->id] = $reward;

		}

		return $rewards;
	}
}
