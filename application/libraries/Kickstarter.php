<?php

use GuzzleHttp\Client;

class Kickstarter
{
    private $CI;

    public function __construct()
    {
        $this->CI =& get_instance();
    }

    public function search($query)
    {
        $url = 'https://www.kickstarter.com/projects/search.json?search=&term='.urlencode($query);
        $data = $this->GET($url);
        return json_decode($data);
    }


    public function GET($url)
    {
        $cachefile = getcwd().'/../data/temp/materialistic-ks-'.md5($url);
        if (file_exists($cachefile)) {
            // die("CACHED");
            return file_get_contents($cachefile);
        }
        $client = new Client([
            // Base URI is used with relative requests
            // 'base_uri' => 'https://trello.com/',
            // You can set any number of default request options.
            'timeout'  => 10.0,
            'cookies' => true,
            'proxy' => sprintf(
                'socks5://%s:%s@%s:%s',
                PROXY_USERNAME,
                PROXY_PASSWORD,
                PROXY_HOST,
                PROXY_PORT
            ),
        ]);

        // Add the cookie plugin to the client
        // $client->addSubscriber($cookiePlugin);

        // $response = $client->request('GET', $url, ['debug' => true, 'User-Agent' => 'Materialist (Like Gecko)',]);


        try {
            $options = [
                'debug' => false,
                'headers' => [
                    'User-Agent' => 'Materialist (Like Gecko) '.md5($url),
                    ]
                ];
            $response = $client->request('GET', $url, $options);
            #$response = $client->request('GET', $url);
            $data = (String)$response->getBody();
        } catch (Exception $e) {
            print '<h1>'.Error."</h1><br/>";
            echo '<pre>';
            var_dump($e);
            echo htmlentities($e->getMessage());
            echo html_entity_decode((String)$e->getResponse()->getBody());
            echo '</pre>';
            die();
        }

        if (!$response->getStatusCode() === 200) {
            var_dump($data);
            die("X".$response->getStatusCode());
            return false;
        }

        $data = (String)$response->getBody();

        file_put_contents($cachefile, $data);

        return $data;
    }


    public function canonical_url($creator, $project)
    {
        return 'https://www.kickstarter.com/projects/'.$creator.'/'.$project;
    }

    public function create_from_search_results($search_results)
    {
        $campaigns = array();
        foreach ($search_results->projects as $project) {
            $campaigns[] = $this->create_from_ks_object($project);
        }
        return $campaigns;
    }

    public function create_from_ks_object($project)
    {
        $this->CI->load->model('Campaign');

        $creator_slug = isset($project->creator->slug) ? $project->creator->slug : $project->creator->id;

        $url = $this->canonical_url($creator_slug, $project->slug);

        if ($campaign = $this->CI->Campaign->fetch_by_url($url)) {
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


    public function campaign_data($url)
    {
        $page = $this->GET($url);
        $res = preg_match_all('/window.current_project = "(.*)"/', $page, $matches);
        if (!count($matches)) {
            return false;
        }
        $data = html_entity_decode(stripslashes($matches[1][0]));
        #$data = $matches[1][0];
        /*$v8 = new V8Js();
        $js = 'var json = JSON.stringify('.$data.');';
        try {
            $json - $v8->executeString($js);
        } catch (V8JsException $e) {
             var_dump($e);
        }*/
        return json_decode($data);
    }
}
