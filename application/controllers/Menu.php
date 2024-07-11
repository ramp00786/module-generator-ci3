<?php
defined('BASEPATH') or exit('No direct script access allowed');



class Menu extends MY_Controller
{


	protected $controller = 'Menu';
	protected $table = 'menus';
	protected $page_title = 'Menu';
	protected $view_name = 'menus';

	protected $formFields = array(
		'menu_name' => array(
							'type' => 'text',
							'label' => 'Menu Name',
							'duplicate' => FALSE,
							'attributes' => array(
								'required' => 'required', // either TRUE or 'required'
								'placeholder' => 'Please enter activity name',
								'class' => 'form-control',
								'data-validation' => 'required',
								'data-validation-error-msg' => 'Please enter activity name',
							),
		),
		
	);


	public function __construct()
	{
		parent::__construct();		
		$this->load->helper('url');
		$this->load->helper('security');

		/*-----Check Login------*/
		if($this->is_logged_in()){							
			if($this->auth_level != 9){
				$data['p'] = ''; 
				$this->ap->constructPage('no_permission', $data, app_name.' | Access Denied');
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
		
		$data[$this->table] = $this->tfn->getData('*', $this->table, "status = 1");
		$data['controller'] = $this->controller;
		$data['table_name'] = $this->table;
		$data['page_title'] = $this->page_title;
		$data['formFields'] = $this->formFields;
		$this->ap->constructPage($this->view_name, $data, app_name.' | ');
			
	}
	public function add()
	{
		if($this->is_logged_in()){						
			if($this->auth_level == 9){

				if( strtolower( $_SERVER['REQUEST_METHOD'] ) == 'post' ){


					$postData = $this->security->xss_clean($this->input->post());

					$res = $this->ap->formValidator($postData, $this->formFields);

					if($res !='' ){
						$alertData['type'] = 'danger'; //-- Alert type
						$alertData['icon'] = 'fa fa-warning'; //----Alert Icon
						$alertData['heading'] = 'Empty error!'; //----Alert Icon
						$alertData['msg'] = '<ul>'.$res.'</ul>'; //----Alert Icon
						$this->session->set_flashdata('alert', $alertData);
						redirect($this->controller, 'refresh');
					}else{

						$check = $this->ap->duplicateCheck($postData, $this->table, $this->formFields);

						if($check['count'] > 0){
							$alertData['type'] = 'danger'; //-- Alert type
							$alertData['icon'] = 'fa fa-exclamation-triangle'; //----Alert Icon
							$alertData['heading'] = 'Error!'; //----Alert Icon
							$alertData['msg'] = "Record already exists (".$check['duplicate_str'].") "; //----Alert Icon
							$this->session->set_flashdata('alert', $alertData);
							redirect($this->controller, 'refresh');
						}
						else{
							unset($postData['token']);
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
				}
				else{
					redirect($this->controller, 'refresh');
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

				if( strtolower( $_SERVER['REQUEST_METHOD'] ) == 'post' ){					
					$postData = $this->input->post();
					$id = $postData['oldid'];
					unset($postData['oldid']);

					$check = $this->ap->duplicateCheck($postData, $this->table, $this->formFields, $id);

					if($check['count'] > 0){
						$alertData['type'] = 'danger'; //-- Alert type
						$alertData['icon'] = 'fa fa-exclamation-triangle'; //----Alert Icon
						$alertData['heading'] = 'Error!'; //----Alert Icon
						$alertData['msg'] = "Record already exists (".$check['duplicate_str'].") "; //----Alert Icon
						$this->session->set_flashdata('alert', $alertData);
						redirect($this->controller, 'refresh');
					}
					unset($postData['token']);
					$res = $this->tfn->updateData($postData, $this->table, 'status', '2', 'id', $id, $this->auth_user_id);

					if($res == 'no_changes_found'){
						$alertData['type'] = 'info'; //-- Alert type
						$alertData['icon'] = 'fa fa-info'; //----Alert Icon
						$alertData['heading'] = 'Notice.!'; //----Alert Icon
						$alertData['msg'] = 'No changes found for update.!'; //----Alert Icon
						$this->session->set_flashdata('alert', $alertData);
					}
					elseif($res == 'updated'){
						$alertData['type'] = 'warning'; //-- Alert type
						$alertData['icon'] = 'fa fa-check'; //----Alert Icon
						$alertData['heading'] = 'Success.!'; //----Alert Icon
						$alertData['msg'] = 'Record updated successfully.!'; //----Alert Icon
						$this->session->set_flashdata('alert', $alertData);						
					}
					else{
						$alertData['type'] = 'danger'; //-- Alert type
						$alertData['icon'] = 'fa fa-warning'; //----Alert Icon
						$alertData['heading'] = 'Failed.!'; //----Alert Icon
						$alertData['msg'] = 'Something went wrong.!'; //----Alert Icon
						$this->session->set_flashdata('alert', $alertData);	
					}
					redirect($this->controller,'refresh');
					
				}
				else{
					redirect($this->controller, 'refresh');
				}
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
						$where_col_name = 'id';
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
			                <input type="hidden" name="oldid" value="<?php echo $rowInfo['id']; ?>">
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
						                    ?>
						                    <input type="<?php echo $field['type']; ?>" name="<?php echo $name; ?>" <?php echo $attributes_str; ?> value="<?php echo $rowInfo[$name] ?>" />
						                    <?php
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
				$this->ap->constructPage('no_permission', $data, app_name.' Dashboard');
			}
		}
		else{
			redirect('login','refresh');
		}
	}
	public function show_with_goc_client()
	{
		$data['projects'] = $this->tfn->getData('*', 'projects', "status = 1");
		$data['controller'] = $this->controller;
		$data['table_name'] = 'projects';
		$data['page_title'] = 'Create Project with GOC';
		$data['formFields'] = $this->formFields;

		$data['activity_list'] = $this->tfn->getData('*', 'activity', "status = 1");
		$this->ap->constructPage('show_list_for_goc_client', $data, app_name);
		// $this->load->view('show_list_for_goc_client');
	}
}

/* End of file Dashboard.php */
/* Location: /controllers/Dashboard.php */
