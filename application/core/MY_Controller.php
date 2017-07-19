<?PHP

class MY_Controller extends CI_Controller {

	var $viewdata = array();

	var $ssl_page = false;

	function __construct(){
		parent::__construct();
		
		$this->load->library('session');
		$this->load->helper('url');
		
        if(is_null($this->ssl_page)){
           // Do nothing.
		} elseif ($this->ssl_page){
			$this->force_ssl();
		} else {
			$this->not_ssl();
		}
		
		$this->load->database();
		
		
        $this->load->model("User");
        $this->load->model("Notification");

        define('REASONABLE', $this->session->userdata('reasonable_mode'));
		
		$this->viewdata = array(
			'title'    => "",
			'subtitle' => '',
			'section'  => 'Piracy Inc',
			'js_extra' => array(),
			'css_extra' => array(),
            'static_home' => "/static/",
		);
		
        
        if($id = $this->session->userdata("authorised_user") ){
            $this->current_user = $this->User->fetch_by_id($id);
			
			$this->viewdata['notifications'] = $this->Notification->my_messages($this->current_user->id);
			
			if ($this->current_user->is_admin or ENVIRONMENT == 'development'){
	 			$this->output->enable_profiler(true);       
			}
        } else {
            $this->current_user = new Anonymous_User();
            $this->viewdata['notifications'] = array();
        }

		if (ENVIRONMENT == 'development'){
 			$this->output->enable_profiler(true);       
		}
        
        $this->viewdata['current_user'] = $this->current_user;
		
		
	}
	
	function navigation_menu(){
		if($this->current_user->logged_in()){
			return $this->load->view("fragments/navigation", $this->viewdata, true);
		} else {
			return $this->load->view("fragments/navigation-nouser", $this->viewdata, true);
		}
		
	}
	
	function js_load_anytime(){
		// Loads the Anytime Picker
				
		$this->viewdata['js_extra']['anytime']    = $this->viewdata['static_home']."libraries/anytime/anytime.c.js";
		$this->viewdata['js_extra']['anytime_tz'] = $this->viewdata['static_home']."libraries/anytime/anytimetz.js";
		$this->viewdata['css_extra']['anytime']   = $this->viewdata['static_home']."libraries/anytime/anytime.css";
		
	}
    
    function js_load_lightbox(){
        $this->viewdata['js_extra']['lightbox2']    = $this->viewdata['static_home']."libraries/lightbox2/js/lightbox.js";
        $this->viewdata['css_extra']['lightbox2']   = $this->viewdata['static_home']."libraries/lightbox2/css/lightbox.css";
    }
	
	function render($viewname, $section = false){
		
        if(!isset($this->viewdata['nomenu']) || !$this->viewdata['nomenu'] ){
            $this->viewdata['navigation'] = $this->navigation_menu();
        } else {
            $this->viewdata['navigation'] = '';
        }
		
		$this->load->view('fragments/header', $this->viewdata);
		$this->load->view($viewname,          $this->viewdata);
		$this->load->view('fragments/footer', $this->viewdata);
    }
    
	function render_alone($viewname, $section = false){
		$this->load->view($viewname,          $this->viewdata);
	}
	
	function requires_authentication(){
		
		$this->load->library('session');
		$this->load->helper('url');
		
		$this->load->model("User");
		
		if($id = $this->session->userdata("authorised_user")){
			
			$this->current_user = $this->User->fetch_by_id($id);
			$this->viewdata['current_user'] = $this->current_user;
			
			return true;
		} else {

		    $url = $this->config->site_url($this->uri->uri_string());
		    $redirect_to = $_SERVER['QUERY_STRING'] ? $url.'?'.$_SERVER['QUERY_STRING'] : $url;

			redirect("/auth/login?redirect_to=".urlencode($redirect_to));
		}
	}
	
	function force_ssl(){
        if ($_SERVER['SERVER_PORT'] != 443 && $this->config->config['allow_ssl']){
            $url = $this->uri->uri_string();
            if(isset($_SERVER['QUERY_STRING']) && $_SERVER['QUERY_STRING']){
                $url .='?'.$_SERVER['QUERY_STRING'];
            }
            redirect($this->config->config['secure_url'].'/'.$url);
        }
    }
    
    function not_ssl(){
        if ($_SERVER['SERVER_PORT'] == 443){
        	#var_dump($this->config->config['base_url']);
            #redirect($this->config->config['base_url'].'/'.$this->uri->uri_string());
        }
    }

    public function error($view)
    {
        $this->render('error/'.$view,  $this->viewdata);
    }

    public function redirect($url, $statusCode = 303){
    	header('Location: '.$url, true, $statusCode);
    	die();
    }
    
}

include("ADMIN_Controller.php");
include("AUTHED_Controller.php");
