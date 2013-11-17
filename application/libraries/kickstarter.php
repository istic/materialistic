<?PHP

class Kickstarter {
	private $CI;

	function __construct(){
		$this->CI =& get_instance(); 
	}

	function search($query){
		$cachefile = '/tmp/materialistic-'.md5($query);
		if(file_exists($cachefile)){
			return unserialize(file_get_contents($cachefile));
		}
		$url = 'http://www.kickstarter.com/projects/search.json?search=&term='.urlencode($query);
		$data = json_decode(file_get_contents($url));
		file_put_contents($cachefile, serialize($data));
		return $data;
	}

	function canonical_url($creator, $project){
		return 'http://www.kickstarter.com/projects/'.$creator.'/'.$project;
	}

	function create_from_search_results($search_results){
		$campaigns = array();
		foreach($search_results->projects as $project){
			$campaigns[] = $this->create_from_ks_object($project);
		}
		return $campaigns;
	}

	function create_from_ks_object($project){

		$this->CI->load->model('Campaign');

		$creator_slug = isset($project->creator->slug) ? $project->creator->slug : $project->creator->id;

		$url = $this->canonical_url($creator_slug, $project->slug);

		if($campaign = $this->CI->Campaign->fetch_by_url($url)){
			return $campaign;
		}

		$campaign = new Campaign_Object();

		$campaign->name         = $project->name;
		$campaign->URL          = $url;
		$campaign->target       = $project->goal;
		$campaign->pledged      = $project->pledged;
		$campaign->backer_count = $project->backers_count;
		$campaign->site         = 'kickstarter';

		// ENUM('live', 'successful', 'failed', 'suspended', 'deleted', 'canceled');
		$campaign->status   = $project->state;
		$campaign->vitality = ($project->state == 'successful' || $project->state == 'live');

		$campaign->creator  = $project->creator->name;
		$campaign->currency = $project->currency;
		$campaign->category = $project->category->name;
		$campaign->photo    = $project->photo->med;
		$campaign->country  = $project->country;

		$campaign->date_start    = date(DATETIME_MYSQL, $project->created_at);
		$campaign->date_end      = date(DATETIME_MYSQL, $project->deadline);
		$campaign->date_checked  = date(DATETIME_MYSQL);
		$campaign->date_created  = date(DATETIME_MYSQL);
		$campaign->date_modified = date(DATETIME_MYSQL);
		$campaign->save();
		return $campaign;
	}
}