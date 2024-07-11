<?php
// Restricted page directly access 
defined('BASEPATH') OR exit('No direct script access allowed');

class Changepassword extends MY_Controller {

	// create constructor
	public function __construct()
	{
		parent::__construct();
		
		// Load security and url helper
		$this->load->helper('security');
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
		// check if user logged in or not
		if($this->is_logged_in()){	

				$data['is_logged_in'] = $this->is_logged_in();
				$data['hidden'] = array('class' => 'form-horizontal form-label-left', 'id' => 'demo-form2');
				$data['status'] = null;
				// Load view using library
				$this->ap->constructPage('changepassword', $data, app_name.' Change Password');
		}
		else{
			redirect('login','refresh');
		}
		
	}
	// Update password form post function
	public function updatepass()
	{
		// check if user logged in
		if($this->is_logged_in()){
			// get all inputs from form
			$postData = $this->input->post();
			// check inputs should not empty
			if(!empty($postData)){
				// check confirm password and password are same or not
				if($postData['new_pass'] === $postData['cnf_pass']){
					// Check user old password is correct or not
					$authData = $this->tfn->getData('passwd', 'users', "user_id='".$this->auth_user_id."'");
					$res = $this->authentication->check_passwd($authData[0]['passwd'], $postData['old_password'] );
					// if password is correct and update new password
					if($res){
						// check password is strong or not
						$pattern = ' /^.*(?=.{7,})(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).*$/ ';
   						if(preg_match($pattern, $postData['new_pass'])){
							// encript the new password
							$dataUp['passwd'] = $this->authentication->hash_passwd($postData['new_pass']);
							// Update the password 
							$update = $this->tfn->updateDataSame($dataUp, 'users', 'user_id', $this->auth_user_id);
							
							if($update){
								// Save history 
								$passHistory = array();
								$dataTime = $this->ap->getTimeDate();
								$passHistory['user_id'] = $this->auth_user_id;
								//$passHistory['user_name'] = $this->auth_name;
								$passHistory['user_email'] = $this->auth_email;
								$passHistory['ip_address'] = $this->ap->getIP();
								$passHistory['update_date'] = $dataTime['dt'];
								$passHistory['update_time'] = $dataTime['tm'];

								$this->tfn->insertData($passHistory, 'password_history');

								// Save history 
								// Create retur array with success message
								$alertData['type'] = 'success';
								$alertData['icon'] = 'icon fa fa-check';
								$alertData['heading'] = 'Success!';
								$alertData['message'] = '<p>Your password has been changed successfully.</p>';
								$this->session->set_flashdata('alert', $alertData);
								// redirect to view
								redirect('changepassword','refresh');
							}
						}
						else{
							// If password does not have special characters
							$alertData['type'] = 'danger';
							$alertData['icon'] = 'icon fa fa-warning';
							$alertData['heading'] = 'Error!';
							$alertData['message'] = '<p>Password contestants must have uppercase letter number and special characters.</p>';
							$this->session->set_flashdata('alert', $alertData);
							
							redirect('changepassword','refresh');
						}
					}
					else{
						// if Old password not match
						$alertData['type'] = 'danger';
						$alertData['icon'] = 'icon fa fa-warning';
						$alertData['heading'] = 'Error!';
						$alertData['message'] = '<p>Invalid password.</p>';
						$this->session->set_flashdata('alert', $alertData);
						
						redirect('changepassword','refresh');
					}
				}
				else{
					// if new password and confirm password not match
					$alertData['type'] = 'danger';
					$alertData['icon'] = 'icon fa fa-warning';
					$alertData['heading'] = 'Error!';
					$alertData['message'] = '<p>Password not match.</p>';
					$this->session->set_flashdata('alert', $alertData);
					redirect('changepassword','refresh');
				}
			}
			else{
				// if invalid inputs
				$alertData['type'] = 'danger';
				$alertData['icon'] = 'icon fa fa-warning';
				$alertData['heading'] = 'Error!';
				$alertData['message'] = '<p>Invalid input.</p>';
				$this->session->set_flashdata('alert', $alertData);
				redirect('changepassword','refresh');
			}
		}
	}

}

/* End of file Students.php */
/* Location: ./application/controllers/Students.php */