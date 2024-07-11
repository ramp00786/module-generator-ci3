<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Login extends MY_Controller
{
	public function __construct()
	{
		parent::__construct();

		// Force SSL
		//$this->force_ssl();

		// Form and URL helpers always loaded (just for convenience)
		$this->load->helper('url');
		$this->load->helper('form');
	}

	// -----------------------------------------------------------------------

	/**
	 * Demonstrate being redirected to login.
	 * If you are logged in and request this method,
	 * you'll see the message, otherwise you will be
	 * shown the login form. Once login is achieved,
	 * you will be redirected back to this method.
	 */
	public function index()
	{
		if($this->is_logged_in()){
			redirect('dashboard', 'refresh');

		}
		else{

			/**
			 * This login method only serves to redirect a user to a 
			 * location once they have successfully logged in. It does
			 * not attempt to confirm that the user has permission to 
			 * be on the page they are being redirected to.
			 */


			// Method should not be directly accessible
			if( $this->uri->uri_string() == 'examples/login')
				show_404();

			if( strtolower( $_SERVER['REQUEST_METHOD'] ) == 'post' )
				$this->require_min_level(1);

			$this->setup_login_form();

			$this->load->view('login');
		}
	}
	
	
	// -----------------------------------------------------------------------

	

	/**
	 * Log out
	 */
	public function logout()
	{
		$this->authentication->logout();

		// Set redirect protocol
		$redirect_protocol = USE_SSL ? 'https' : NULL;
		
		redirect( site_url( LOGIN_PAGE . '?' . AUTH_LOGOUT_PARAM . '=1', $redirect_protocol ) );
	}

	// --------------------------------------------------------------

	
	
	
}

/* End of file Login.php */
/* Location: /controllers/login.php */