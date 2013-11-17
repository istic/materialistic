<?PHP

class Docs extends MY_Controller {

	public function __construct(){
		parent::__construct();
        $this->viewdata['subtitle'] = "RTFM";
	}
    
	public function index($view = 'index')
	{
        if(!file_exists(VIEWPATH.'docs/'.$view.'.php'))
        {
        	die("$view Not found");	
            throw new Exception_NotFound($view." not found");
        }

		$this->render('docs/'.$view,  $this->viewdata);
	}
}

