<?php
// Restricted page directly access 
defined('BASEPATH') OR exit('No direct script access allowed');

class Home extends MY_Controller {

	// create constructor
	public function __construct()
	{
		parent::__construct();

		

		// Form and URL helpers always loaded (just for convenience)
		$this->load->helper('url');
		$this->load->helper('form');

		if($this->is_logged_in()){							
			if($this->auth_level != 1 && $this->auth_level != 9){
				$data['security'] = $this->security; 
				$this->ap->constructPage('no_permission', $data, app_name.'Dashboard');
			}
		}
		else{
			redirect('login','refresh');
		}

	}

	// Default function
	public function index()
	{
		// check if logged in then redirect to dashboad page
		if($this->is_logged_in()){
			redirect('dashboard', 'refresh');
		}
		else{
			// redirect to login page
			redirect('login','refresh');
		}
	}

	



} // End home controller
