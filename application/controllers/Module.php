<?php
defined('BASEPATH') or exit('No direct script access allowed');



class Module extends MY_Controller
{

	public $SHOW_ITEMS = false;
	public $CREATE_ITEMS = false;
	public $UPDATE_ITEMS = false;
	public $DELETE_ITEMS = false;

	protected $controller = 'Module';
	protected $table = '';
	protected $page_title = '';
	protected $view_name = 'module';
	protected $slug_call = '';

	protected $redirect_str = '';

	protected $files_required = array();

	protected $files_update = array();

	protected $formFields = array();


	public function __construct()
	{
		parent::__construct();		
		$this->load->helper('url');
		$this->load->helper('security');

		/*-----Check Login------*/
		if($this->is_logged_in()){									
			if($this->auth_level != 1 && $this->auth_level != 9){
				$data['p'] = ''; 
				$this->ap->constructPage('no_permission', $data, app_name.'Access Denied');
			}
		}
		else{
			redirect('login','refresh');
		}
		/*-----./ End Check Login------*/

		$this->controller = $this->router->fetch_class();

		//-----Fetch module data
		$menu_id = $this->session->userdata('menu_id');
		$menu_name = $this->uri->segment(2);
		$slug = $this->uri->segment(3);
		$opration = $this->uri->segment(4);
		$this->slug_call = $slug;
		$this->controller = $this->controller.'/'.$menu_name.'/'.$this->slug_call;
		$this->redirect_str = $this->controller;

		$moduleData = $this->tfn->getData('*', 'modules', "status = 1 AND slug = '$slug' AND menu_id = '$menu_id' ");

		if($moduleData !='No data' || $opration == 'add' || $opration == 'editrecord' || $opration == 'updaterecord'){			
			$this->table = $moduleData[0]['module_name'].'_'.$moduleData[0]['menu_id'];
			$this->page_title = str_replace('-', ' ', $slug);
			$fields = $this->tfn->getData('*', 'module_fields', "status = 1 AND module_id = '".$moduleData[0]['id']."' AND (module_parent = '0' OR module_parent IS NULL) AND (field_display_status = 'Active' OR field_display_status IS NULL) ");
			$fieldsArray = array();
			if($fields !='No data'){
				foreach ($fields as $f_row) { 
					$fieldsArray[$f_row['field_name']]['module_id'] = $f_row['module_id'];
					$fieldsArray[$f_row['field_name']]['input_type'] = $f_row['input_type'];
					$fieldsArray[$f_row['field_name']]['db_name'] = $f_row['field_name'];
					$fieldsArray[$f_row['field_name']]['id'] = $f_row['id'];
					$fieldsArray[$f_row['field_name']]['type'] = $f_row['field_type'];
					$fieldsArray[$f_row['field_name']]['label'] = ucwords($f_row['field_label']);
					$fieldsArray[$f_row['field_name']]['duplicate'] = $f_row['field_duplicate'];
					$attributes = $this->tfn->getData('*', 'field_attributes', "status = 1 AND module_id = '".$moduleData[0]['id']."' AND field_id = '".$f_row['id']."' ");

					$atr = array();
					if($attributes !='No data'){
						foreach ($attributes as  $value) {
							if(strtolower( $f_row['field_type'] )   == 'file'){
								if($value['attribute_value'] == 'required' || $value['attribute_value'] == 'TRUE'){
									if(!in_array($f_row['field_name'], $this->files_required) ){
										array_push($this->files_required, $f_row['field_name']);
									}									
								}
								if(!in_array($f_row['field_name'], $this->files_update) ){
									array_push($this->files_update, $f_row['field_name']);
								}
							}
							$atr[$value['attribute_name']] = $value['attribute_value'];
						}
					}
					$fieldsArray[$f_row['field_name']]['attributes'] = $atr;
				}
			}			
			$this->formFields = $fieldsArray;
		}
		else{
			//redirect('dashboard', 'refresh');
		}



		// Check module permissions;
		if($this->auth_level == 9){
			$this->SHOW_ITEMS = true;
			$this->CREATE_ITEMS = true;
			$this->UPDATE_ITEMS = true;
			$this->DELETE_ITEMS = true;
		}
		else{
			// echo $slug; // Take it from above code
			$getModuleIdFromSlug = $this->ap->moduleSlugToId($slug);
			if($getModuleIdFromSlug !='None'){

				$permissionData = $this->tfn->getData('*', 'user_permissions', "status = 1 AND module_id = '".$getModuleIdFromSlug."' AND user_id = '".$this->auth_user_id."' ");

				if($permissionData[0]['show_items'] == 1){
					$this->SHOW_ITEMS = true;
				}

				if($permissionData[0]['create_items'] == 1){
					$this->CREATE_ITEMS = true;
				}

				if($permissionData[0]['update_items'] == 1){
					$this->UPDATE_ITEMS = true;
				}

				if($permissionData[0]['delete_items'] == 1){
					$this->DELETE_ITEMS = true;
				}
			
			}
			else{
				$permissionData = '';
			}
		}
		
		
		// Check module permissions;


	}

	public function index()
	{		
		if($this->slug_call !=''){
			$data[$this->table] = $this->tfn->getData('*', $this->table, "status = 1", "id", "DESC");
			$data['controller'] = $this->controller;
			$data['table_name'] = $this->table;
			$data['page_title'] = $this->page_title;
			$data['formFields'] = $this->formFields;

			$data['SHOW_ITEMS'] = $this->SHOW_ITEMS;
			$data['CREATE_ITEMS'] = $this->CREATE_ITEMS;
			$data['UPDATE_ITEMS'] = $this->UPDATE_ITEMS;
			$data['DELETE_ITEMS'] = $this->DELETE_ITEMS;

			$this->ap->constructPage($this->view_name, $data, app_name);
		}
		else{
			redirect('dashboard', 'refresh');
		}
		
			
	}

	function postDataArrayToJson($postData){
		//print_r($postData);
		$json = '';
		$field_name = '';
		foreach($postData as $p_key => $p_val){
			if(is_array($p_val)){

				$org = $p_key;	
				
				$module_id = $this->tfn->getData('id', 'modules', "status = 1 AND module_name = '".$this->table."' ");
				
				$pCheck = $this->tfn->getData('field_name', 'module_fields', "status = 1 AND module_parent = '".$org."' AND module_id = '".$module_id[0]['id']."' ");
				
				//print_r($pCheck);
				if($pCheck !='No data'){
					$ms = array();
					foreach ($p_val as $inner_key => $inner_val) {
						if(isset($postData[$pCheck[0]['field_name'].'__'.$inner_val])){
							$ms[$inner_val] = $postData[$pCheck[0]['field_name'].'__'.$inner_val];
						}
						unset($postData[$pCheck[0]['field_name'].'__'.$inner_val]);
					}					
					$postData[$pCheck[0]['field_name']] = json_encode($ms);
					$postData[$p_key] = json_encode($postData[$p_key]);
					//print_r($p_key);
				}
				else{
					//print_r($p_key);
					//print_r($postData[$p_key]);
					if(isset($postData[$p_key])){
						$postData[$p_key] = json_encode($postData[$p_key]);
					}
					
				}
				
			}
		}

		
		return $postData;


	}

	public function add()
	{
		if($this->is_logged_in()){						
			if($this->auth_level == 1 || $this->auth_level == 9 ){

				if( strtolower( $_SERVER['REQUEST_METHOD'] ) == 'post' ){


					$postData = $this->input->post(); //$this->security->xss_clean($this->input->post());

					$res = $this->ap->formValidator($postData, $this->formFields);

					if($res !='' ){
						$alertData['type'] = 'danger'; //-- Alert type
						$alertData['icon'] = 'fa fa-warning'; //----Alert Icon
						$alertData['heading'] = 'Empty error!'; //----Alert Icon
						$alertData['msg'] = '<ul>'.$res.'</ul>'; //----Alert Icon
						$this->session->set_flashdata('alert', $alertData);
						redirect($this->redirect_str, 'refresh');
					}else{

						$check = $this->ap->duplicateCheck($postData, $this->table, $this->formFields);
						if($check['count'] > 0){
							$alertData['type'] = 'danger'; //-- Alert type
							$alertData['icon'] = 'fa fa-exclamation-triangle'; //----Alert Icon
							$alertData['heading'] = 'Error!'; //----Alert Icon
							$alertData['msg'] = "Record already exists (".$check['duplicate_str'].") "; //----Alert Icon
							$this->session->set_flashdata('alert', $alertData);
							redirect($this->redirect_str, 'refresh');
						}
						else{
							unset($postData['token']);

							$allFieldCheck = false; //$this->ap->allFieldCheck($postData);

							if($allFieldCheck){
								$alertData['type'] = 'danger'; //-- Alert type
								$alertData['icon'] = 'fa fa-warning'; //----Alert Icon
								$alertData['heading'] = 'Empty error!'; //----Alert Icon
								$alertData['msg'] = '<ul><li>All fields are empty.</li></ul>'; //----Alert Icon
								$this->session->set_flashdata('alert', $alertData);
								redirect($this->redirect_str, 'refresh');
							}
							else{								
								if(!empty($this->files_required)){
									foreach ($this->files_required as $file_control_name) {
										$upRes = $this->ap->uploadSingleFile('uploads/', 'doc|docx|xlsx|xls|jpg|jpeg|png|gif|pdf|mp4|MP4|WMV|AVI|FLV|MOV||MKV|MPEG-2', $file_control_name);
										if($upRes['status'] == 'success'){
											$postData[$file_control_name] = $upRes['file_name'];
											$postData[$file_control_name.'_path'] = $upRes['path'];
										}
										else{
											$alertData['type'] = 'danger'; //-- Alert type
											$alertData['icon'] = 'fa fa-warning'; //----Alert Icon
											$alertData['heading'] = 'File upload failed!'; //----Alert Icon
											$alertData['msg'] = '<ul><li>'.$upRes['error'].'</li></ul>'; //----Alert Icon
											$this->session->set_flashdata('alert', $alertData);
											redirect($this->redirect_str, 'refresh');
										}
									}
									
								}


								$postData = $this->postDataArrayToJson($postData);
								$res = $this->tfn->insertData($postData, $this->table);

								if($res){
									$alertData['type'] = 'success'; //-- Alert type
									$alertData['icon'] = 'fa fa-check'; //----Alert Icon
									$alertData['heading'] = 'Added'; //----Alert Icon
									$alertData['msg'] = 'Records has been added successfully.!'; //----Alert Icon
									$this->session->set_flashdata('alert', $alertData);
									redirect($this->redirect_str, 'refresh');
								}
								else{
									$alertData['type'] = 'danger'; //-- Alert type
									$alertData['icon'] = 'fa fa-exclamation-triangle'; //----Alert Icon
									$alertData['heading'] = 'Error!'; //----Alert Icon
									$alertData['msg'] = 'Something went wrong.!'; //----Alert Icon
									$this->session->set_flashdata('alert', $alertData);
									redirect($this->redirect_str, 'refresh');
								}
							}
						}
					}
				}
				else{
					redirect($this->redirect_str, 'refresh');
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
			if($this->auth_level == 1 || $this->auth_level == 9){

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
						redirect($this->redirect_str, 'refresh');
					}
					unset($postData['token']);

					if(!empty($this->files_required)){
						foreach ($this->files_required as $file_control_name) {
							if($_FILES[$file_control_name]['name'] !=''){
								$upRes = $this->ap->uploadSingleFile('uploads/', 'doc|docx|xlsx|xls|jpg|jpeg|png|gif|pdf', $file_control_name);
								if($upRes['status'] == 'success'){
									$postData[$file_control_name] = $upRes['file_name'];
									$postData[$file_control_name.'_path'] = $upRes['path'];
								}
								else{
									$alertData['type'] = 'danger'; //-- Alert type
									$alertData['icon'] = 'fa fa-warning'; //----Alert Icon
									$alertData['heading'] = 'File upload failed!'; //----Alert Icon
									$alertData['msg'] = '<ul><li>'.$upRes['error'].'</li></ul>'; //----Alert Icon
									$this->session->set_flashdata('alert', $alertData);
									redirect($this->redirect_str, 'refresh');
								}
							}
						}						
					}

					if(!empty($this->files_update)){
						foreach ($this->files_update as $file_control_name) {
							if($_FILES[$file_control_name]['name'] !=''){
								$upRes = $this->ap->uploadSingleFile('uploads/', 'doc|docx|xlsx|xls|jpg|jpeg|png|gif|pdf', $file_control_name);
								if($upRes['status'] == 'success'){
									$postData[$file_control_name] = $upRes['file_name'];
									$postData[$file_control_name.'_path'] = $upRes['path'];
								}
								else{
									$alertData['type'] = 'danger'; //-- Alert type
									$alertData['icon'] = 'fa fa-warning'; //----Alert Icon
									$alertData['heading'] = 'File upload failed!'; //----Alert Icon
									$alertData['msg'] = '<ul><li>'.$upRes['error'].'</li></ul>'; //----Alert Icon
									$this->session->set_flashdata('alert', $alertData);
									redirect($this->redirect_str, 'refresh');
								}
							}
						}						
					}

					


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
					redirect($this->redirect_str,'refresh');
					
				}
				else{
					redirect($this->redirect_str, 'refresh');
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
			if($this->auth_level == 1 || $this->auth_level == 9){			
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

						<?php echo form_open($this->controller.'/updaterecord', array('enctype' => 'multipart/form-data')); ?>
						
		                <div class="modal-body">

		                	<!-- Hidden fields like Id -->                  
			                <input type="hidden" name="oldid" value="<?php echo $rowInfo['id']; ?>">
			                <!-- Hidden fields like Id -->  
		                	<?php 
		                		$this->ap->create_form_for_update($formFields, $rowInfo, $table_name);
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
						redirect($this->redirect_str, 'refresh');
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

	public function set_menu_id()
	{
		if($this->input->post('menu_id') != ''){			
			$this->session->set_userdata('menu_id', $this->input->post('menu_id'));
		}
	}

	public function delete_file()
	{
		$table_name = $this->input->post('table_name');
		$col_name = $this->input->post('col_name');
		$file_name = $this->input->post('file_name');

		
	}
}


/* End of file Dashboard.php */
/* Location: /controllers/Dashboard.php */
