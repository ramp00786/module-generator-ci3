<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Users extends MY_Controller
{


	

	protected $controller = 'Users';
	protected $table = 'users';
	protected $page_title = 'Users';
	protected $view_name = 'users';

	protected $formFields = array(
		'email' => array(
							'type' => 'email',
							'label' => 'Email',
							'duplicate' => FALSE,
							'attributes' => array(
								'required' => 'required', // either TRUE or 'required'
								'placeholder' => 'Please enter email',
								'class' => 'form-control',
								'data-validation' => 'required',
								'data-validation-error-msg' => 'Please enter email',
							),
		),

		'first_name' => array(
							'type' => 'text',
							'label' => 'First Name',
							'duplicate' => FALSE,
							'attributes' => array(
								'required' => 'required', // either TRUE or 'required'
								'placeholder' => 'Please enter first name',
								'class' => 'form-control',
								'data-validation' => 'required',
								'data-validation-error-msg' => 'Please enter first name',
							),
		),

		'last_name' => array(
							'type' => 'text',
							'label' => 'Last Name',
							'duplicate' => FALSE,
							'attributes' => array(
								'required' => 'required', // either TRUE or 'required'
								'placeholder' => 'Please enter last name',
								'class' => 'form-control',
								'data-validation' => 'required',
								'data-validation-error-msg' => 'Please enter last name',
							),
		),


		'contact_number' => array(
							'type' => 'text',
							'label' => 'Contact Number',
							'duplicate' => FALSE,
							'attributes' => array(
								'required' => 'required', // either TRUE or 'required'
								'placeholder' => 'Please enter last name',
								'class' => 'form-control',
								'data-validation' => 'required',
								'data-validation-error-msg' => 'Please enter last name',
								'maxlength' => 10
							),
		),

		'address' => array(
							'type' => 'textarea',
							'label' => 'Address',
							'duplicate' => FALSE,
							'attributes' => array(
								'required' => 'required', // either TRUE or 'required'
								'placeholder' => 'Please enter address',
								'class' => 'form-control',
								'data-validation' => 'required',
								'data-validation-error-msg' => 'Please enter address',
							),
		),

		'passwd' => array(
							'type' => 'text',
							'label' => 'Password',
							'duplicate' => FALSE,
							'attributes' => array(
								'required' => '', // either TRUE or 'required'
								'placeholder' => 'Please enter password if you want to change',
								'class' => 'form-control',
								'data-validation' => 'required',
								'data-validation-error-msg' => 'Please enter password if you want to change',
							),
		),

		'auth_level' => array(
							'type' => 'hidden',
							'label' => '',
							'duplicate' => FALSE,
							'attributes' => array(
								'required' => '', // either TRUE or 'required'
								'placeholder' => 'Please enter auth level',
								'class' => 'form-control',
								'data-validation' => 'required',
								'data-validation-error-msg' => 'Please enter password if you want to change',
								'value' => 1,
							),
		),
		
	);


	public function __construct()
	{
		parent::__construct();
		// Force SSL
		//$this->force_ssl();
		// Form and URL helpers always loaded (just for convenience)
		$this->load->helper('url');
		$this->load->helper('security');
		/*-----Check Login------*/
		if($this->is_logged_in()){						
			if($this->auth_level != 9){
				$data['security'] = $this->security; 
				$this->ap->constructPage('no_permission', $data, app_name.' | Dashboard');
			}
		}
		else{
			redirect('login','refresh');
		}
		/*-----./ End Check Login------*/

		$this->controller = $this->router->fetch_class();

		
		
		
	}

	public function index()
	{
		if($this->is_logged_in()){						
			if($this->auth_level == 9){  
				$data[$this->table] = $this->tfn->getData('*', $this->table, "auth_level != 9");
				$data['controller'] = $this->controller;
				$data['table_name'] = $this->table;
				$data['page_title'] = $this->page_title;
				$data['formFields'] = $this->formFields;
				$this->ap->constructPage($this->view_name, $data, app_name.' | ');
			}
		}
		else{
			redirect('login','refresh');
		}
	}
	public function add()
	{
		$this->load->helper('auth');
		$this->load->model('examples/examples_model');
		$this->load->model('examples/validation_callables');


		if($this->is_logged_in()){						
			if($this->auth_level == 9){
				$postData = $this->security->xss_clean($this->input->post());
				$full_name = $email = $passwd = $contact_no = false; $msg = '';
				
				if($postData['first_name'] == ''){
					$full_name = true;
					$msg.= '<li>Please enter first name</li>';
				}

				if($postData['email'] == ''){
					$email = true;
					$msg.= '<li>Please enter email</li>';
				}

				if($postData['contact_number'] == ''){
					$contact_no = true;
					$msg.= '<li>Please enter contact_number</li>';
				}

				if($postData['passwd'] == ''){
					$passwd = true;
					$msg.= '<li>Please enter password</li>';
				}

				if($full_name || $email || $contact_no || $passwd){
					$alertData['type'] = 'danger'; //-- Alert type
					$alertData['icon'] = 'fa fa-warning'; //----Alert Icon
					$alertData['heading'] = 'Empty error!'; //----Alert Icon
					$alertData['msg'] = '<ul>'.$msg.'</ul>'; //----Alert Icon
					$this->session->set_flashdata('alert', $alertData);
					redirect($this->controller, 'refresh');
				}else{
					if($postData['passwd'] !=''){
						$postData['passwd'] = password_hash($postData['passwd'], PASSWORD_BCRYPT);
					}
					$postData['user_id']    = $this->examples_model->get_unused_id();
					$postData['created_at'] = date('Y-m-d H:i:s');
					$postData['auth_level'] = 1;

					$emailToArray = explode('@', $postData['email']);
					$postData['username'] = str_replace(' ', '_', $emailToArray[0]);

					$res = $this->tfn->insertData($postData, $this->table);
					if($res){
						$alertData['type'] = 'success'; //-- Alert type
						$alertData['icon'] = 'fa fa-check'; //----Alert Icon
						$alertData['heading'] = 'Added'; //----Alert Icon
						$alertData['msg'] = 'Records has been added successfully.!'; //----Alert Icon
						$this->session->set_flashdata('alert', $alertData);
						redirect($this->controller, 'refresh');
					}
					else{
						$alertData['type'] = 'danger'; //-- Alert type
						$alertData['icon'] = 'fa fa-exclamation-triangle'; //----Alert Icon
						$alertData['heading'] = 'Error!'; //----Alert Icon
						$alertData['msg'] = 'Something went wrong.!'; //----Alert Icon
						$this->session->set_flashdata('alert', $alertData);
						redirect($this->controller, 'refresh');
					}
				}
			}
			else{
				$data['security'] = $this->security; 
				$this->ap->constructPage('no_permission', $data, app_name.' | Dashboard');
			}
		}
		else{
			redirect('login','refresh');
		}
	}

	
	public function updaterecord()
	{
		if($this->is_logged_in()){						
			if($this->auth_level == 9){	
				$postData = $this->input->post();
				$id = $postData['oldid'];
				unset($postData['oldid']);
				unset($postData['token']);
				if($postData['passwd'] !=''){
					$postData['passwd'] = password_hash($postData['passwd'], PASSWORD_BCRYPT);
				}
				$this->tfn->updateData($postData, $this->table, 'status', '2', 'user_id', $id, $this->auth_user_id);
				$alertData['type'] = 'warning'; //-- Alert type
				$alertData['icon'] = 'fa fa-check'; //----Alert Icon
				$alertData['heading'] = 'Success.!'; //----Alert Icon
				$alertData['msg'] = 'Record updated successfully.!'; //----Alert Icon
				$this->session->set_flashdata('alert', $alertData);
				redirect($this->controller,'refresh');
			}
			else{
				$data['security'] = $this->security; 
				$this->ap->constructPage('no_permission', $data, app_name.' | No Permission');
			}
		}
		else{
			redirect('login','refresh');
		}

	}



	public function editrecord()
	{
		if($this->is_logged_in()){						
			if($this->auth_level == 9){			
				$requestData = $this->input->post();	
				if(!empty($requestData)){
					$id_str=base64_decode($requestData['encData']);
					$id=explode('___', $id_str);
					if(!empty($id[1])){
						$table_name = $id[1];
						$where_col_name = 'user_id';
						$where_col_value = $id[2];
						$controller = $id[4];
						$msg = $id[5];
						$data = $this->tfn->getData('*', $table_name, $where_col_name.'='.$where_col_value); 
						$rowInfo = $data[0];

						$this->load->helper('form');
						$formFields = $this->formFields;
						
						?>

						<?php echo form_open($this->controller.'/updaterecord'); ?>
						
		                <div class="modal-body">   

			                <!-- Hidden fields like Id -->                  
			                <input type="hidden" name="oldid" value="<?php echo $rowInfo['user_id']; ?>">
			                <!-- Hidden fields like Id -->   

					        <?php 
		                		if(!empty($formFields)){
						            foreach ($formFields as $name => $field) { ?>
						              <div class="form-group">
						                  <label for="full_name">
						                    <?php echo $field['label']; ?>
						                    <?php if(isset($field['attributes']['required']) && $field['attributes']['required']){ 
						                      echo '<span class="text-red">*</span>';
						                    } ?>
						                  </label> 
						                  <?php if($field['type'] == 'textarea'){ 
						                    $attributes_str = $this->ap->attributesToStr($field['attributes']);
						                    ?>
						                    <textarea name="<?php echo $name; ?>" <?php echo $attributes_str; ?>><?php echo $rowInfo[$name] ?></textarea>
						                    <?php
						                  }else{ 
						                    $attributes_str = $this->ap->attributesToStr($field['attributes']);
						                    if($name !='passwd'){
						                    ?>
						                    <input type="<?php echo $field['type']; ?>" name="<?php echo $name; ?>" <?php echo $attributes_str; ?> value="<?php echo $rowInfo[$name] ?>" />
						                    <?php }
						                    else{ ?>

						                    	<input type="<?php echo $field['type']; ?>" name="<?php echo $name; ?>" <?php echo $attributes_str; ?>  />

						                    	<?php

						                    }
						                  } ?>
						                  
						              </div>
						              <?php
						            }
						        } 
						    ?>



		                </div>




		                <div class="modal-footer" style="border-top-color: #337ab7;">
		                  <button type="button" class="btn btn-danger pull-left" data-dismiss="modal">Cancel</button>
		                  <button type="submit" class="btn btn-warning">Update</button>
		                </div>
		            </form>


		            
						
					<?php }
					else{

						$alertData['type'] = 'danger'; //-- Alert type
						$alertData['icon'] = 'fa fa-warning'; //----Alert Icon
						$alertData['heading'] = 'Warning!'; //----Alert Icon
						$alertData['msg'] = 'This operation is harmful for your application.!'; //----Alert Icon
						$this->session->set_flashdata('alert', $alertData);
						redirect(base_url().$controller, 'refresh');
					}

				}
				else{ ?>
						<div class="modal-body">   
							<h4>Something went wrong.!!</h4>
		                </div>
		                <div class="modal-footer" style="border-top-color: #337ab7;">
		                  <button type="button" class="btn btn-danger pull-left" data-dismiss="modal">Cancel</button>                  
		                </div>
			  <?php }
			}
			else{
				$data['security'] = $this->security; 
				$this->ap->constructPage('no_permission', $data, app_name.' | Dashboard');
			}
		}
		else{
			redirect('login','refresh');
		}
	}


	public function api_key(){
		$user_id = $this->input->post('user_id');
		$key = implode('-', str_split(substr(strtolower(md5(microtime().rand(1000, 9999))), 0, 30), 6));
		$up['api_key'] = $key;
		$this->tfn->updateDataSame($up, $this->table, 'user_id', $user_id);
		echo $key;
	}

}

/* End of file Dashboard.php */
/* Location: /controllers/Dashboard.php */