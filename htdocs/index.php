<?php
header('Content-Type: text/html; charset=utf-8');

require_once __DIR__ . '/../vendor/autoload.php';
error_reporting(E_ALL);

/*
 *---------------------------------------------------------------
 * APPLICATION ENVIRONMENT
 *---------------------------------------------------------------
 *
 * You can load different configurations depending on your
 * current environment. Setting the environment also influences
 * things like logging and error reporting.
 *
 * This can be set to anything, but default usage is:
 *
 *     development
 *     testing
 *     production
 *
 * NOTE: If you change these, also change the error_reporting() code below
 */
 
    $env = isset($_SERVER['CI_ENV']) ? $_SERVER['CI_ENV'] : 'development';
    if(file_exists("../localconfig/environment.php")){
        include("../localconfig/environment.php");
    }
 
	define('ENVIRONMENT', $env);
/*
 *---------------------------------------------------------------
 * ERROR REPORTING
 *---------------------------------------------------------------
 *
 * Different environments will require different levels of error reporting.
 * By default development will show errors but testing and live will hide them.
 */

if (defined('ENVIRONMENT'))
{
	switch (ENVIRONMENT)
	{
		case 'development':
			error_reporting(-1);
			ini_set("display_errors", 1);
		break;
		case 'testing':
		case 'production':
			error_reporting(0);
		break;
		default:
			exit('The application environment is not set correctly.');
	}
}


	$system_path = '../libraries/codeigniter/system';
	$application_folder = '../application';
	$view_folder = '../views';


/*
 * --------------------------------------------------------------------
 * DEFAULT CONTROLLER
 * --------------------------------------------------------------------
 *
 * Normally you will set your default controller in the routes.php file.
 * You can, however, force a custom routing by hard-coding a
 * specific controller class/function here. For most applications, you
 * WILL NOT set your routing here, but it's an option for those
 * special instances where you might want to override the standard
 * routing in a specific front controller that shares a common CI installation.
 *
 * IMPORTANT: If you set the routing here, NO OTHER controller will be
 * callable. In essence, this preference limits your application to ONE
 * specific controller. Leave the function name blank if you need
 * to call functions dynamically via the URI.
 *
 * Un-comment the $routing array below to use this feature
 */
	// The directory name, relative to the "controllers" folder.  Leave blank
	// if your controller is not in a sub-folder within the "controllers" folder
	// $routing['directory'] = '';

	// The controller class file name.  Example:  Mycontroller
	// $routing['controller'] = '';

	// The controller function you wish to be called.
	// $routing['function']	= '';


/*
 * -------------------------------------------------------------------
 *  CUSTOM CONFIG VALUES
 * -------------------------------------------------------------------
 *
 * The $assign_to_config array below will be passed dynamically to the
 * config class when initialized. This allows you to set custom config
 * items or override any default config values found in the config.php file.
 * This can be handy as it permits you to share one application between
 * multiple front controller files, with each file containing different
 * config values.
 *
 * Un-comment the $assign_to_config array below to use this feature
 */
	// $assign_to_config['name_of_config_item'] = 'value of config item';



// --------------------------------------------------------------------
// END OF USER CONFIGURABLE SETTINGS.  DO NOT EDIT BELOW THIS LINE
// --------------------------------------------------------------------

/*
 * ---------------------------------------------------------------
 *  Resolve the system path for increased reliability
 * ---------------------------------------------------------------
 */

	// Set the current directory correctly for CLI requests
	if (defined('STDIN'))
	{
		chdir(dirname(__FILE__));
	}

	if (($_temp = realpath($system_path)) !== FALSE)
	{
		$system_path = $_temp.'/';
	}
	else
	{
		// Ensure there's a trailing slash
		$system_path = rtrim($system_path, '/').'/';
	}

	// Is the system path correct?
	if ( ! is_dir($system_path))
	{
		header('HTTP/1.1 503 Service Unavailable.', TRUE, 503);
		exit('Your system folder path does not appear to be set correctly. Please open the following file and correct this: '.pathinfo(__FILE__, PATHINFO_BASENAME));
	}

/*
 * -------------------------------------------------------------------
 *  Now that we know the path, set the main path constants
 * -------------------------------------------------------------------
 */
	// The name of THIS file
	define('SELF', pathinfo(__FILE__, PATHINFO_BASENAME));

	// Path to the system folder
	define('BASEPATH', str_replace('\\', '/', $system_path));

	// Path to the front controller (this file)
	define('FCPATH', str_replace(SELF, '', __FILE__));

	// Name of the "system folder"
	define('SYSDIR', trim(strrchr(trim(BASEPATH, '/'), '/'), '/'));

	// The path to the "application" folder
	if (is_dir($application_folder))
	{
		if (($_temp = realpath($application_folder)) !== FALSE)
		{
			$application_folder = $_temp;
		}

		define('APPPATH', $application_folder.'/');
	}
	else
	{
		if ( ! is_dir(BASEPATH.$application_folder.'/'))
		{
			header('HTTP/1.1 503 Service Unavailable.', TRUE, 503);
			exit('Your application folder path does not appear to be set correctly. Please open the following file and correct this: '.SELF);
		}

		define('APPPATH', BASEPATH.$application_folder.'/');
	}

	// The path to the "views" folder
	if ( ! is_dir($view_folder))
	{
		if ( ! empty($view_folder) && is_dir(APPPATH.$view_folder.'/'))
		{
			$view_folder = APPPATH.$view_folder;
		}
		elseif ( ! is_dir(APPPATH.'views/'))
		{
			header('HTTP/1.1 503 Service Unavailable.', TRUE, 503);
			exit('Your view folder path does not appear to be set correctly. Please open the following file and correct this: '.SELF);
		}
		else
		{
			$view_folder = APPPATH.'views';
		}
	}

	if (($_temp = realpath($view_folder)) !== FALSE)
	{
		$view_folder = realpath($view_folder).'/';
	}
	else
	{
		$view_folder = rtrim($view_folder, '/').'/';
	}

	define('VIEWPATH', $view_folder);

/*
 * --------------------------------------------------------------------
 * LOAD THE BOOTSTRAP FILE
 * --------------------------------------------------------------------
 *
 * And away we go...
*/

try {
	require_once BASEPATH.'core/CodeIgniter.php';
} catch (Exception $e){

	die("Xep: ".$e->getMessage());
}

