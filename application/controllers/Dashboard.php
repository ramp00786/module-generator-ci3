<?php
// Restricted page directly access 
defined('BASEPATH') or exit('No direct script access allowed');
class Dashboard extends MY_Controller
{
	// create constructor
	public function __construct()
	{
		parent::__construct();
		
		// Form and URL helpers always loaded (just for convenience)
		$this->load->helper('url');

		/*-----Check Login------*/
		if($this->is_logged_in()){						
			if($this->auth_level != 1 && $this->auth_level != 9){
				$data['security'] = $this->security; 
				$this->ap->constructPage('no_permission', $data, app_name.' Dashboard');
			}
		}
		else{
			redirect('login','refresh');
		}
		/*-----./ End Check Login------*/

		
		
		
	}

	// Default function
	public function index()
	{
		// check users logged in or not
		if($this->is_logged_in()){						
			if($this->auth_level == 1 || $this->auth_level == 9){

				$data['security'] = $this->security;
				// Get dashboard data
				$data['total_users'] = $this->tfn->getNumrows('user_id', 'users', "auth_level = 1");
				$data['total_menus'] = $this->tfn->getNumrows('id', 'menus', "status = 1");
				$data['total_modules'] = $this->tfn->getNumrows('id', 'modules', "status = 1");
				// Get dashboard data
				// load view using library
				$this->ap->constructPage('dashboard', $data, app_name.' Dashboard');
			}
		}
		else{
			redirect('login','refresh');
		}
	}


	// Make all messages read
	public function read_all()
	{
		$upRead['send_msg_read_sts'] = 1;
		$upRead['new_msg'] = 1;
		$this->tfn->updateDataSame($upRead, 'wa_bu_chat', 'id', ' != 0');
	}

}

/* End of file Dashboard.php */
/* Location: /controllers/Dashboard.php */