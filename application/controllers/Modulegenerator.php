<?php
defined('BASEPATH') or exit('No direct script access allowed');



class Modulegenerator extends MY_Controller
{


	protected $controller = 'Modulegenerator';
	protected $table = 'modules';
	protected $page_title = 'Module Generator';
	protected $view_name = 'module_generator/module_generator';

	protected $formFields = array();


	public function __construct()
	{
		parent::__construct();		
		$this->load->helper('url');
		$this->load->helper('security');

		/*-----Check Login------*/
		if($this->is_logged_in()){							
			if($this->auth_level != 9){
				$data['p'] = ''; 
				$this->ap->constructPage('no_permission', $data, app_name.'Access Denied');
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
		
		$data[$this->table] = $this->tfn->getData('*', $this->table, "status = 1", "id", "DESC");
		$data['menus'] = $this->tfn->getData('*', 'menus', "status = 1");
		$data['controller'] = $this->controller;
		$data['table_name'] = $this->table;
		$data['page_title'] = $this->page_title;
		$data['formFields'] = $this->formFields;
		$this->ap->constructPage($this->view_name, $data, app_name);
			
	}
	public function add()
	{

		$testing = false; //----Insert recored in the database
		//$testing = true; //--- Just print the data with respective table name

		if($testing){
			echo "<pre>";
			echo "<br/>";
			echo "---Start posted array---";
			echo "<br/>";
			print_r($this->input->post());
			echo "<br/>";
			echo "---End posted array--";
		}

		if($this->is_logged_in()){						
			if($this->auth_level == 9){

				if( strtolower( $_SERVER['REQUEST_METHOD'] ) == 'post' ){

					$postData = $this->security->xss_clean($this->input->post());

					$module_name = str_replace(' ', '_', strtolower($postData['module_name']));
					$module_name = str_replace('-', '_', $module_name);
					$slug = str_replace(' ', '-', $postData['slug']);
					$menu_id = $postData['menu_name'];
					
					$msg = ''; $module_name_error = $slug_error = FALSE; 

					if($module_name == ''){
						$module_name_error = TRUE;
						$msg .= '<li>Module name is mandatory</li>';
					}

					if($slug == ''){
						$slug_error = TRUE;
						$msg .= '<li>Slug is mandatory</li>';
					}


					if($slug_error || $module_name_error){
						if(!$testing){
							$alertData['type'] = 'danger'; //-- Alert type
							$alertData['icon'] = 'fa fa-warning'; //----Alert Icon
							$alertData['heading'] = 'Empty error!'; //----Alert Icon
							$alertData['msg'] = '<ul>'.$msg.'</ul>'; //----Alert Icon
							$this->session->set_flashdata('alert', $alertData);
							redirect($this->controller, 'refresh');
						}
						else{
							echo $this->ap->getAlert($alertData['type'], $alertData['msg'], $alertData['heading'], $alertData['icon']);
							exit();
						}
					}else{

						//--Check duplicate module and slug
						$check = $this->tfn->getNumrows('id', $this->table, "status = 1 AND (module_name = '$module_name' OR slug = '$slug' )  AND menu_id = '$menu_id' ");

						if($check > 0){
							if(!$testing){
								$alertData['type'] = 'danger'; //-- Alert type
								$alertData['icon'] = 'fa fa-exclamation-triangle'; //----Alert Icon
								$alertData['heading'] = 'Error!'; //----Alert Icon
								$alertData['msg'] = "Record already exists"; //----Alert Icon
								$this->session->set_flashdata('alert', $alertData);
								redirect($this->controller, 'refresh');
							}
							else{

								echo "<br/>Record already exists<br/>";
								exit();
							}
						}
						else{
							unset($postData['token']);

							$moduleIn['module_name'] = str_replace(' ', '_', strtolower($postData['module_name']));
							$moduleIn['module_name'] = str_replace('-', '_', $moduleIn['module_name']);
							$moduleIn['slug'] = str_replace(' ', '-', $postData['slug']);
							$moduleIn['menu_id'] = $postData['menu_name'];

							//--Insert Module
							if(!$testing){
								$res = $this->tfn->insertData($moduleIn, $this->table);
								$module_id = $this->tfn->lastInsertId();
							}
							else{
								$res = false;
								$module_id = 'module_id-testing-100';
								echo "<br/>";
								echo "---Start Module Main data (table: $this->table)---";
								echo "<br/>";
								print_r($moduleIn);
								echo "<br/>";
								echo "---End Module Main--";

							}

							

							$last_field_id = '';
							
							//---Loop for all fields
							for ($i=1; $i <= $postData['field_count']; $i++) { 

								//---Check field type
								if(isset($postData['input_type_'.$i])){
									$field_row['field_name'] = str_replace(' ', '_', $postData['field_name_'.$i]);
									$field_row['field_type'] = $postData['field_type_'.$i];
									$field_row['field_label'] = $postData['field_label_'.$i];
									$field_row['field_duplicate'] = $postData['field_duplicate_'.$i];
									$field_row['module_id'] = $module_id;

									if(isset($postData['input_type_'.$i])){
										$field_row['input_type'] = $postData['input_type_'.$i];
									}
									else{
										$field_row['input_type'] = 'Database';
									}

									if(isset($postData['module_parent_'.$i])){
										$field_row['module_parent'] = str_replace(' ', '_', $postData['module_parent_'.$i]);
									}
									else{
										$field_row['module_parent'] = null;
									}

									if(isset($postData['field_display_status_'.$i])){
										$field_row['field_display_status'] = $postData['field_display_status_'.$i];
									}										

									if($postData['field_type_'.$i] == 'SELECT-BOX'){

										
										/* ======== Working only single lavel not working for multilavel
										if(isset($postData['select_box_type_'.$i]) && $postData['select_box_type_'.$i] == 'Database'){
											$getModuleInfo_r = $this->tfn->getData('*', 'modules', "status = 1 AND id = '".$postData['select_box_table_'.$i]."' ");
											$getModuleFieldsInfo_r = $this->tfn->getData('*', 'module_fields', "status = 1 AND id = '".$postData['select_box_table_column_'.$i]."' ");
											
											if(!$testing){
												//Insert table relationship
												$tblRelationData['parent_table_id'] = $postData['select_box_table_'.$i];
												$tblRelationData['parent_col_id'] = $postData['select_box_table_column_'.$i];
												$tblRelationData['child_table_id'] = $module_id;

												$lastModuleFieldsInfo = $this->tfn->getData('*', 'module_fields', "status = 1", 'id', 'DESC', 1, 0);
												$tblRelationData['child_col_id'] = $lastModuleFieldsInfo[0]['id']+1;

												$res = $this->tfn->insertData($tblRelationData, 'tables_relation');
											}
											else{
												// Check and make table relationship
												echo "<br/>";
												echo "<br/>";
												echo "=======Table Relationship===";
												echo "<br/>";
												echo "Parent table name<br/>";
												echo $getModuleInfo_r[0]['module_name'].'_'.$getModuleInfo_r[0]['menu_id'];
												echo "<br/>";		
												echo "Parent table id<br/>";
												echo $postData['select_box_table_'.$i];	
												echo "<br/>";									
												echo "Parent table column name<br/>";
												echo $getModuleFieldsInfo_r[0]['field_name'];
												echo "<br/>";
												echo "Parent table column id<br/>";
												echo $postData['select_box_table_column_'.$i];
												echo "<br/>";	
												echo "Child table name<br/>";
												echo $postData['module_name'].'_'.$postData['menu_name'];
												echo "<br/>";
												echo "Child table id<br/>";
												//--Get last id of modules and plus 1
												$lastModuleInfo = $this->tfn->getData('*', 'modules', "status = 1", 'id', 'DESC', 1, 0);
												echo $lastModuleInfo[0]['id']+1;
												echo "<br/>";
												echo "Child table column name<br/>";
												echo $postData['field_name_'.$i];
												echo "<br/>";
												echo "Child table column id<br/>";
												//--Get last id of modules_fields and plus 1
												$lastModuleFieldsInfo = $this->tfn->getData('*', 'module_fields', "status = 1", 'id', 'DESC', 1, 0);
												echo $lastModuleFieldsInfo[0]['id']+1;
												echo "<br/>";

												echo "<br/>";
												echo "<br/>";
												echo "=======Table Relationship===";
												echo "<br/>";
											}
										}
										*/

										

										

										// Check and make table relationship

										if(isset($postData['select_box_type_'.$i])){
											$field_row['field_select_box_type'] = $postData['select_box_type_'.$i];
										}
										else{
											$field_row['field_select_box_type'] = NULL;
										}

										
									}
									else{
										$field_row['field_select_box_type'] = NULL;
									}

									if(isset($postData['child_module_name_'.$i])){
										$field_row['child_module_name'] = $postData['child_module_name_'.$i];
									}
									else{
										$field_row['child_module_name'] = NULL;	
									}

									if(!$testing){
										$this->tfn->insertData($field_row, 'module_fields');
										$field_id = $this->tfn->lastInsertId();
									}
									else{
										$field_id = 'module_fields-id-test-101';
										echo "<br/>";
										echo "---Start Module Fields data (table: module_fields)---";
										echo "<br/>";
										print_r($field_row);
										echo "<br/>";
										echo "---End Module Fields--";
									}


									//--insert select box (dropdown)
									if($postData['input_type_'.$i] == 'HTML'){
										if(isset($postData['field_type_'.$i]) && $postData['field_type_'.$i] == 'SELECT-BOX'){
											$dropDown = array();
											if($postData['select_box_type_'.$i] == 'Options'){
												$field_row['field_select_box_type'] = 'Options';
												if(!empty($postData['option_name_'.$i])){
													foreach ($postData['option_name_'.$i] as $key_sec_op => $op_name) {
														$dropDown['module_id'] = $module_id;
														$dropDown['field_id'] = $field_id;
														$dropDown['op_name'] = $op_name;
														$dropDown['op_val'] = $postData['option_value_'.$i][$key_sec_op];
														if(!$testing){
															$this->tfn->insertData($dropDown, 'dropdown_data');
														}
														else{
															echo "<br/>";
															echo "---Start Fields manual dropdown (table: dropdown_data)---";
															echo "<br/>";
															print_r($dropDown);
															echo "<br/>";
															echo "---End Fields manual dropdown--";
														}
													}
												}
											}
											elseif($postData['select_box_type_'.$i] == 'Database'){
												$dropDown['module_id'] = $module_id;
												$dropDown['field_id'] = $field_id;										
												$dropDown['field_select_box_table'] = $postData['select_box_table_'.$i];
												$dropDown['field_select_box_table_column'] = $postData['select_box_table_column_'.$i];

												if(!$testing){
													$this->tfn->insertData($dropDown, 'dropdown_data');
												}
												else{
													echo "<br/>";
													echo "---Start Fields database dropdown (table: dropdown_data)---";
													echo "<br/>";
													print_r($dropDown);
													echo "<br/>";
													echo "---End Fields database dropdown--";
												}
											}
										}
									}
									elseif($postData['input_type_'.$i] == 'Database'){
										$dd_db_d['module_id'] = $module_id;
										$dd_db_d['field_id'] = $field_id;
										$dd_db_d['field_select_box_table'] = $postData['module_name_'.$i];
										$dd_db_d['field_select_box_table_column'] = $postData['module_column_'.$i];
										$dd_db_d['display_in_form'] = $postData['module_field_type_'.$i];
										$dd_db_d['db_module_have_child'] = $postData['module_child_'.$i];
										if(!$testing){
											$this->tfn->insertData($dd_db_d, 'dropdown_data');
										}
										else{
											echo "<br/>";
											echo "---Start field from Database (dropdown_data)---";
											echo "<br/>";
											print_r($dd_db_d);
											echo "<br/>";
											echo "---End field from Database--";
										}
									}

									//--Insert field's attributes
									if(!empty($postData['attr_name_'.$i])){
										foreach ($postData['attr_name_'.$i] as $key => $atr_name) {
											$attributes_array = array();
											$attributes_array['module_id'] = $module_id;
											$attributes_array['field_id'] = $field_id;
											$attributes_array['attribute_name'] = $atr_name;
											$attributes_array['attribute_value'] = $postData['attr_value_'.$i][$key];
											if(!$testing){
												$this->tfn->insertData($attributes_array, 'field_attributes');
											}
											else{
												echo "<br/>";
												echo "---Start Fields attributes (table: field_attributes)---";
												echo "<br/>";
												print_r($attributes_array);
												echo "<br/>";
												echo "---End Fields attributes--";
											}
										}
									}
								}

								
								
							}
							if($res){
								if(!$testing){
									$alertData['type'] = 'success'; //-- Alert type
									$alertData['icon'] = 'fa fa-check'; //----Alert Icon
									$alertData['heading'] = 'Added'; //----Alert Icon
									$alertData['msg'] = 'Records has been added successfully.!'; //----Alert Icon
									$this->session->set_flashdata('alert', $alertData);
									redirect($this->controller, 'refresh');
								}
								else{									
									exit();
								}
							}
							else{
								if(!$testing){
									$alertData['type'] = 'danger'; //-- Alert type
									$alertData['icon'] = 'fa fa-exclamation-triangle'; //----Alert Icon
									$alertData['heading'] = 'Error!'; //----Alert Icon
									$alertData['msg'] = 'Something went wrong.!'; //----Alert Icon
									$this->session->set_flashdata('alert', $alertData);
									redirect($this->controller, 'refresh');
								}
								else{									
									echo '<br/>=========================================End=====================';
									exit();
								}
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
				$this->ap->constructPage('no_permission', $data, app_name.'Dashboard');
			}
		}
		else{
			redirect('login','refresh');
		}
	}

	public function updaterecord()
	{

		$testing = false; //----Insert recored in the database
		//$testing = true; //--- Just print the data with respective table name

		if($testing){
			echo "<pre>";
			echo "<br/>";
			echo "---Start posted array---";
			echo "<br/>";
			print_r($this->input->post());
			echo "<br/>";
			echo "---End posted array--";
		}

		if($this->is_logged_in()){						
			if($this->auth_level == 9){

				if( strtolower( $_SERVER['REQUEST_METHOD'] ) == 'post' ){

					$postData = $this->security->xss_clean($this->input->post());
					$id = $postData['oldid'];
					unset($postData['oldid']);

					$module_name = str_replace(' ', '_', strtolower($postData['module_name']));
					$slug = str_replace(' ', '-', $postData['slug']);
					$menu_id = $postData['menu_name'];
					
					$msg = ''; $module_name_error = $slug_error = FALSE; 

					if($module_name == ''){
						$module_name_error = TRUE;
						$msg .= '<li>Module name is mandatory</li>';
					}

					if($slug == ''){
						$slug_error = TRUE;
						$msg .= '<li>Slug is mandatory</li>';
					}


					if($slug_error || $module_name_error){
						if(!$testing){
							$alertData['type'] = 'danger'; //-- Alert type
							$alertData['icon'] = 'fa fa-warning'; //----Alert Icon
							$alertData['heading'] = 'Empty error!'; //----Alert Icon
							$alertData['msg'] = '<ul>'.$msg.'</ul>'; //----Alert Icon
							$this->session->set_flashdata('alert', $alertData);
							redirect($this->controller, 'refresh');
						}
						else{
							echo '<ul>'.$msg.'</ul>';
							exit();
						}
					}else{

						//--Check duplicate module and slug
						$check = $this->tfn->getNumrows('id', $this->table, "status = 1 AND (module_name = '$module_name' OR slug = '$slug' ) AND id !='$id' AND menu_id = '$menu_id' ");

						if($check > 0){
							if(!$testing){
								$alertData['type'] = 'danger'; //-- Alert type
								$alertData['icon'] = 'fa fa-exclamation-triangle'; //----Alert Icon
								$alertData['heading'] = 'Error!'; //----Alert Icon
								$alertData['msg'] = "Record already exists"; //----Alert Icon
								$this->session->set_flashdata('alert', $alertData);
								redirect($this->controller, 'refresh');
							}
							else{

								echo "<br/>Record already exists<br/>";
								exit();
							}
						}
						else{
							unset($postData['token']);

							$moduleIn['module_name'] = str_replace(' ', '_', strtolower($postData['module_name']));
							$moduleIn['slug'] = str_replace(' ', '-', $postData['slug']);
							$moduleIn['menu_id'] = $postData['menu_name'];

							//--Insert Module
							if(!$testing){
								//$res = $this->tfn->insertData($moduleIn, $this->table);
								$res = $this->tfn->updateData($moduleIn, $this->table, 'status', '2', 'id', $id, $this->auth_user_id);
								$module_id = $id;

								$storeOld = $this->tfn->getData('*', 'dropdown_data', "field_select_box_table = '".$module_id."' ");
								$oldFields = array();
								foreach($storeOld as $field_ids){
									$oldFields[$field_ids['field_select_box_table_column']] = $this->ap->fieldIdtoFieldName($field_ids['field_select_box_table_column']);
								}
								$this->tfn->deleteDataP('module_fields', 'module_id', $id);
								$this->tfn->deleteDataP('field_attributes', 'module_id', $id);
								$this->tfn->deleteDataP('dropdown_data', 'module_id', $id);

							}
							else{
								$res = false;
								$module_id = 'module_id-testing-100';
								echo "<br/>";
								echo "---Start Module Main data (table: $this->table)---";
								echo "<br/>";
								print_r($moduleIn);
								echo "<br/>";
								echo "---End Module Main--";

							}

							

							$last_field_id = '';
							
							//---Loop for all fields
							for ($i=1; $i <= $postData['field_count_up']; $i++) { 

								//---Check field type
								if(isset($postData['input_type_'.$i])){
									$field_row['field_name'] = str_replace(' ', '_', $postData['field_name_'.$i]);
									$field_row['field_type'] = $postData['field_type_'.$i];
									$field_row['field_label'] = $postData['field_label_'.$i];
									$field_row['field_duplicate'] = $postData['field_duplicate_'.$i];
									$field_row['module_id'] = $module_id;

									if(isset($postData['input_type_'.$i])){
										$field_row['input_type'] = $postData['input_type_'.$i];
									}
									else{
										$field_row['input_type'] = 'Database';
									}

									if(isset($postData['module_parent_'.$i])){
										$field_row['module_parent'] = str_replace(' ', '_', $postData['module_parent_'.$i]);
									}
									else{
										$field_row['module_parent'] = null;
									}

									if(isset($postData['field_display_status_'.$i])){
										$field_row['field_display_status'] = $postData['field_display_status_'.$i];
									}										

									if($postData['field_type_'.$i] == 'SELECT-BOX'){

										if(isset($postData['select_box_type_'.$i])){
											$field_row['field_select_box_type'] = $postData['select_box_type_'.$i];
										}
										else{
											$field_row['field_select_box_type'] = NULL;
										}

										
									}
									else{
										$field_row['field_select_box_type'] = NULL;
									}

									if(isset($postData['child_module_name_'.$i])){
										$field_row['child_module_name'] = $postData['child_module_name_'.$i];
									}
									else{
										$field_row['child_module_name'] = NULL;	
									}

									if(!$testing){
										$this->tfn->insertData($field_row, 'module_fields');
										$field_id = $this->tfn->lastInsertId();
									}
									else{
										$field_id = 'module_fields-id-test-101';
										echo "<br/>";
										echo "---Start Module Fields data (table: module_fields)---";
										echo "<br/>";
										print_r($field_row);
										echo "<br/>";
										echo "---End Module Fields--";
									}


									//--insert select box (dropdown)
									if($postData['input_type_'.$i] == 'HTML'){
										if(isset($postData['field_type_'.$i]) && $postData['field_type_'.$i] == 'SELECT-BOX'){
											$dropDown = array();
											if($postData['select_box_type_'.$i] == 'Options'){
												$field_row['field_select_box_type'] = 'Options';
												if(!empty($postData['option_name_'.$i])){
													foreach ($postData['option_name_'.$i] as $key_sec_op => $op_name) {
														$dropDown['module_id'] = $module_id;
														$dropDown['field_id'] = $field_id;
														$dropDown['op_name'] = $op_name;
														$dropDown['op_val'] = $postData['option_value_'.$i][$key_sec_op];
														if(!$testing){
															$this->tfn->insertData($dropDown, 'dropdown_data');
														}
														else{
															echo "<br/>";
															echo "---Start Fields manual dropdown (table: dropdown_data)---";
															echo "<br/>";
															print_r($dropDown);
															echo "<br/>";
															echo "---End Fields manual dropdown--";
														}
													}
												}
											}
											elseif($postData['select_box_type_'.$i] == 'Database'){
												$dropDown['module_id'] = $module_id;
												$dropDown['field_id'] = $field_id;										
												$dropDown['field_select_box_table'] = $postData['select_box_table_'.$i];
												$dropDown['field_select_box_table_column'] = $postData['select_box_table_column_'.$i];

												if(!$testing){
													$this->tfn->insertData($dropDown, 'dropdown_data');
												}
												else{
													echo "<br/>";
													echo "---Start Fields database dropdown (table: dropdown_data)---";
													echo "<br/>";
													print_r($dropDown);
													echo "<br/>";
													echo "---End Fields database dropdown--";
												}
											}
										}
									}
									elseif($postData['input_type_'.$i] == 'Database'){
										$dd_db_d['module_id'] = $module_id;
										$dd_db_d['field_id'] = $field_id;
										$dd_db_d['field_select_box_table'] = $postData['module_name_'.$i];
										$dd_db_d['field_select_box_table_column'] = $postData['module_column_'.$i];
										$dd_db_d['display_in_form'] = $postData['module_field_type_'.$i];
										$dd_db_d['db_module_have_child'] = $postData['module_child_'.$i];
										if(!$testing){
											$this->tfn->insertData($dd_db_d, 'dropdown_data');
										}
										else{
											echo "<br/>";
											echo "---Start field from Database (dropdown_data)---";
											echo "<br/>";
											print_r($dd_db_d);
											echo "<br/>";
											echo "---End field from Database--";
										}
									}

									//--Insert field's attributes
									if(!empty($postData['attr_name_'.$i])){
										foreach ($postData['attr_name_'.$i] as $key => $atr_name) {
											$attributes_array = array();
											$attributes_array['module_id'] = $module_id;
											$attributes_array['field_id'] = $field_id;
											$attributes_array['attribute_name'] = $atr_name;
											$attributes_array['attribute_value'] = $postData['attr_value_'.$i][$key];
											if(!$testing){
												$this->tfn->insertData($attributes_array, 'field_attributes');
											}
											else{
												echo "<br/>";
												echo "---Start Fields attributes (table: field_attributes)---";
												echo "<br/>";
												print_r($attributes_array);
												echo "<br/>";
												echo "---End Fields attributes--";
											}
										}
									}
								}

								
								
							}
							if($res){
								if(!$testing){

									//---Update data of dropdown_data

									if(!empty($oldFields)){
										foreach($oldFields as $field_id_old => $field_name_old){
											$checkDropDownOld = $this->tfn->getData('*', 'dropdown_data', "status = 1 AND  field_select_box_table_column = '".$field_id_old."' ");
											if($checkDropDownOld !='No data' && $checkDropDownOld !='Table not exists'){
												$getNewIdByFieldName = $this->ap->fieldNametoFieldId($field_name_old, $module_id);
												$dropDownUpdateSame['field_select_box_table_column'] = $getNewIdByFieldName;
												$this->tfn->updateDataSame($dropDownUpdateSame, 'dropdown_data', 'id', $checkDropDownOld[0]['id']);
											}
											//fieldNametoFieldId
										}
									}

									$alertData['type'] = 'warning'; //-- Alert type
									$alertData['icon'] = 'fa fa-check'; //----Alert Icon
									$alertData['heading'] = 'Updated'; //----Alert Icon
									$alertData['msg'] = 'Records has been updated successfully.!'; //----Alert Icon
									$this->session->set_flashdata('alert', $alertData);
									redirect($this->controller, 'refresh');
								}
								else{									
									exit();
								}
							}
							else{
								if(!$testing){
									$alertData['type'] = 'danger'; //-- Alert type
									$alertData['icon'] = 'fa fa-exclamation-triangle'; //----Alert Icon
									$alertData['heading'] = 'Error!'; //----Alert Icon
									$alertData['msg'] = 'Something went wrong.!'; //----Alert Icon
									$this->session->set_flashdata('alert', $alertData);
									redirect($this->controller, 'refresh');
								}
								else{									
									echo '<br/>=========================================End=====================';
									exit();
								}
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
				$this->ap->constructPage('no_permission', $data, app_name.'Dashboard');
			}
		}
		else{
			redirect('login','refresh');
		}
	}



	public function updaterecord_old()
	{
		if($this->is_logged_in()){						
			if($this->auth_level == 9){

				if( strtolower( $_SERVER['REQUEST_METHOD'] ) == 'post' ){

					$postData = $this->security->xss_clean($this->input->post());
					$module_name = str_replace(' ', '_', $postData['module_name']);
					$slug = str_replace(' ', '-', $postData['slug']);
					$id = $postData['oldid'];
					unset($postData['oldid']);
					
					$msg = ''; $module_name_error = $slug_error = FALSE; 

					if($module_name == ''){
						$module_name_error = TRUE;
						$msg .= '<li>Module name is mandatory</li>';
					}

					if($slug == ''){
						$slug_error = TRUE;
						$msg .= '<li>Slug is mandatory</li>';
					}


					if($slug_error || $module_name_error){
						$alertData['type'] = 'danger'; //-- Alert type
						$alertData['icon'] = 'fa fa-warning'; //----Alert Icon
						$alertData['heading'] = 'Empty error!'; //----Alert Icon
						$alertData['msg'] = '<ul>'.$msg.'</ul>'; //----Alert Icon
						$this->session->set_flashdata('alert', $alertData);
						redirect($this->controller, 'refresh');
					}else{

						$check = $this->tfn->getNumrows('id', $this->table, "status = 1 AND (module_name = '$module_name' OR slug = '$slug' ) AND id !='".$id."' ");

						if($check > 0){
							$alertData['type'] = 'danger'; //-- Alert type
							$alertData['icon'] = 'fa fa-exclamation-triangle'; //----Alert Icon
							$alertData['heading'] = 'Error!'; //----Alert Icon
							$alertData['msg'] = "Record already exists"; //----Alert Icon
							$this->session->set_flashdata('alert', $alertData);
							redirect($this->controller, 'refresh');
						}
						else{
							unset($postData['token']);							

							$moduleIn['module_name'] = str_replace(' ', '_', $postData['module_name']);
							$moduleIn['slug'] = str_replace(' ', '-', $postData['slug']);

							$res = $this->tfn->updateData($moduleIn, $this->table, 'status', '2', 'id', $id, $this->auth_user_id);

							$module_id = $id;

							$this->tfn->deleteDataP('module_fields', 'module_id', $id);
							$this->tfn->deleteDataP('field_attributes', 'module_id', $id);
							$this->tfn->deleteDataP('dropdown_data', 'module_id', $id);

							

							for ($i=1; $i <= $postData['field_count']; $i++) {

								if(isset($postData['field_name_'.$i])){
									$field_row['field_name'] = str_replace(' ', '_', $postData['field_name_'.$i]);
									$field_row['field_type'] = $postData['field_type_'.$i];
									$field_row['field_label'] = $postData['field_label_'.$i];
									$field_row['field_duplicate'] = $postData['field_duplicate_'.$i];
									$field_row['module_id'] = $module_id;

									if($postData['field_type_'.$i] == 'SELECT-BOX'){
										$field_row['field_select_box_type'] = $postData['select_box_type_'.$i];
									}
									else{
										$field_row['field_select_box_type'] = NULL;
									}
									
									$this->tfn->insertData($field_row, 'module_fields');
									$field_id = $this->tfn->lastInsertId();


									//--insert seelct box (dropdown)
									if($postData['field_type_'.$i] == 'SELECT-BOX'){
										$dropDown = array();
										if($postData['select_box_type_'.$i] == 'Options'){
											$field_row['field_select_box_type'] = 'Options';
											if(!empty($postData['option_name_'.$i])){
												foreach ($postData['option_name_'.$i] as $key_sec_op => $op_name) {
													$dropDown['module_id'] = $module_id;
													$dropDown['field_id'] = $field_id;
													$dropDown['op_name'] = $op_name;
													$dropDown['op_val'] = $postData['option_value_'.$i][$key_sec_op];
													$this->tfn->insertData($dropDown, 'dropdown_data');
												}
											}
										}
										elseif($postData['select_box_type_'.$i] == 'Database'){
											$dropDown['module_id'] = $module_id;
											$dropDown['field_id'] = $field_id;										
											$dropDown['field_select_box_table'] = $postData['select_box_table_'.$i];
											$dropDown['field_select_box_table_column'] = $postData['select_box_table_column_'.$i];
											$this->tfn->insertData($dropDown, 'dropdown_data');
										}
									}



									
									foreach ($postData['attr_name_'.$i] as $key => $atr_name) {
										$attributes_array = array();
										$attributes_array['module_id'] = $module_id;
										$attributes_array['field_id'] = $field_id;
										$attributes_array['attribute_name'] = $atr_name;
										$attributes_array['attribute_value'] = $postData['attr_value_'.$i][$key];
										$this->tfn->insertData($attributes_array, 'field_attributes');
									}
								}

							}
							
							if($res){
								$alertData['type'] = 'warning'; //-- Alert type
								$alertData['icon'] = 'fa fa-check'; //----Alert Icon
								$alertData['heading'] = 'Updated'; //----Alert Icon
								$alertData['msg'] = 'Records has been updated successfully.!'; //----Alert Icon
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

						$menus = $this->tfn->getData('*', 'menus', "status = 1");
						
						?>

						<?php echo form_open($this->controller.'/updaterecord'); ?>


						
					  <div class="modal-body">
					   <!-- Hidden fields like Id -->                  
					   <input type="hidden" name="oldid" value="<?php echo $rowInfo['id']; ?>">
					   <!-- Hidden fields like Id -->


					   <div class="form-group">
			              <label id="for_module_name">Select Menu <span class="text-red">*</span> </label>
			              <select name="menu_name" id="menu_name" class="form-control menu_name">
			                <option value="0">None</option>
			                <?php
			                  if($menus !='No data'){
			                    foreach ($menus as $m_row) {
			                    	if($m_row['id'] == $rowInfo['menu_id']){
			                    		$sel = 'selected';
			                    	}
			                    	else{
			                    		$sel = '';
			                    	}
			                      echo '<option '.$sel.' value="'.$m_row['id'].'">'.$m_row['menu_name'].'</option>';
			                    }
			                  }
			                ?>
			              </select>
			            </div>


					   <div class="form-group">
					      <label id="for_module_name">Module Name <span class="text-red">*</span> </label>
					      <input type="text" id="module_name" name="module_name" class="form-control" placeholder="enter module name" value="<?php echo $rowInfo['module_name'] ?>">
					   </div>
					   <div class="form-group">
					      <label id="for_slug">Slug <span class="text-red">*</span> </label>
					      <input type="text" id="slug" name="slug" class="form-control" placeholder="enter module name" value="<?php echo $rowInfo['slug'] ?>">
					   </div>

					   <input type="hidden" id="field_count_up" name="field_count_up" value="0">


					   <div class="form-group" id="next_btn">
					      <button type="button" class="btn btn-success pull-right add_more_field" onclick="addField_up($(this))">Add More Input</button>
					      <div class="clearfix"></div>
					   </div>
					   <div class="fields_data">					   	
					   		<?php
					   			$fieldData = $this->tfn->getData('*', 'module_fields', "status = 1 AND  (module_parent = '0' OR module_parent IS NULL ) AND module_id = '".$rowInfo['id']."'  " ); 
					   			if($fieldData !='No data'){
					   				$field_count = 0;
					   				foreach($fieldData as $field){ $field_count++;
					   					if($field['input_type'] == 'HTML' || $field['input_type'] == NULL){
					   						?>
					   						<script type="text/javascript">
													$('#field_count_up').val('<?php echo $field_count; ?>');
												</script>
					   						<?php
					   						$this->new_field($field, $field_count);
					   					}
					   					else{
					   						?>
					   						<fieldset id="row_<?php echo $field_count ?>">
													<legend>Input &nbsp; | &nbsp;<i class="fa fa-trash text-red" style="cursor:pointer" onclick="parentRemove(<?php echo $field_count ?>, $(this))"></i> </legend>													

															<div class="row" style="display:none">
																<div class="col-md-12">
																	<div class="form-group col-md-12">
																		<label>Input Type</label>
																		<input <?php if($field['input_type'] == 'HTML'){echo 'checked';} ?> class="input_type" type="radio" name="input_type_<?php echo $field_count ?>" checked="checked" value="HTML"> HTML
																		<input <?php if($field['input_type'] == 'Database'){echo 'checked';} ?> class="input_type" type="radio" name="input_type_<?php echo $field_count ?>" value="Database"> Database
																	</div>
																</div>
															</div>

															<div class="col-md-12" style="display:none">
																<hr style="border-color: #c9c3c3" />
															</div>

															<div class="clearfix"></div>
															<input type="text" class="hide-field this-row" value="<?php echo $field_count ?>">
															<div class="clearfix"></div>
															<script type="text/javascript">
																$('#field_count').val('<?php echo $field_count; ?>');
															</script>
								   						<?php $this->new_db_field_inner($field, $field_count); ?>
						   					</fieldset>
						   					<?php
					   					}
					   				}
					   			}
					   			
					   		?>
					   </div>
					 </div>
					 <div class="modal-footer" style="border-top-color: #337ab7;" id="fotter_btns">
            <?php //$this->ap->getButtons();

            		$this->ap->getButtons([['type' => 'submit', 'value' => 'Update', 'class'=> 'btn btn-warning'],['type' => 'reset', 'value' => 'Reset', 'class'=> 'btn btn-success', 'onclick' => 'resetUpdateForm()']]);

             ?>
          </div>
					<?php }
					else{

						$alertData['type'] = 'danger'; //-- Alert type
						$alertData['icon'] = 'fa fa-warning'; //----Alert Icon
						$alertData['heading'] = 'Warning!'; //----Alert Icon
						$alertData['msg'] = 'This operation is harmful for your application.!'; //----Alert Icon
						$this->session->set_flashdata('alert', $alertData);
						redirect(base_url().$this->controller, 'refresh');
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


	public function old_check()
	{
		
		if( strtolower( $_SERVER['REQUEST_METHOD'] ) == 'post' ){

			if($this->input->post('module_name') == ''){
				echo 'Please enter module name<br/>';
				
			}

			if($this->input->post('slug') == ''){
				echo 'Please enter slug<br/>';
				
			}

			if($this->input->post('module_name') !=null){
				
				$module_name = str_replace(' ', '_', strtolower($this->input->post('module_name')));
				$menu_id = str_replace(' ', '_', strtolower($this->input->post('module_name')));				
				$check = $this->tfn->getNumrows('id', $this->table, "status = 1 AND module_name = '$module_name' AND menu_id = '$menu_id' ");
				if($check > 0){
					echo "Module Name = '".$module_name."' is already exists<br/>";
					
				}
				else{
					echo '';
					
				}
			}
			if($this->input->post('slug') != null){				
				$slug = str_replace(' ', '-', strtolower($this->input->post('slug')));
				$check = $this->tfn->getNumrows('id', $this->table, "status = 1 AND slug = '$slug'  AND menu_id = '$menu_id'  ");
				if($check > 0){
					echo "Slug = '".$slug."' is already exists<br/>";
					
				}
				else{
					echo '';
					
				}
			}
		}
	}


	public function new_field($postData = '', $posted_field = '')
	{	
		//print_r($postData);
		if($posted_field ==''){
			$field_count = $this->input->post('field_count');
		}
		else{
			$field_count = $posted_field;
		}
		?>

		
		<fieldset id="row_<?php echo $field_count ?>">
			<legend>Input &nbsp; | &nbsp;<i class="fa fa-trash text-red" style="cursor:pointer" onclick="parentRemove(<?php echo $field_count ?>, $(this))"></i> </legend>

			

			<div class="row">
				<div class="col-md-12">
					<div class="form-group col-md-12" style="display:none">
						<label>Input Type</label>
						<input <?php if(!empty($postData)){if($postData['input_type'] == 'HTML'){echo 'checked';}} ?> class="input_type" type="radio" name="input_type_<?php echo $field_count ?>" checked="checked" value="HTML"> HTML
						<input <?php if(!empty($postData)){if($postData['input_type'] == 'Database'){echo 'checked';}} ?> class="input_type" type="radio" name="input_type_<?php echo $field_count ?>" value="Database"> Database
					</div>
				</div>
			</div>

			<div class="col-md-12" style="display:none">
				<hr style="border-color: #c9c3c3" />
			</div>

			<div class="clearfix"></div>
			<input type="text" class="hide-field this-row" value="<?php echo $field_count ?>">
			<div class="clearfix"></div>
			

			<span>				
				<div class="row field_row_main field_row_<?php echo $field_count ?>">
					<div class="col-md-12">

						<input type="hidden" name="module_parent_<?php echo $field_count ?>" value="0">


						<div class="form-group col-md-6">
							<label>Name <span class="text-red">*</span></label>
							<input required="required" class="form-control field_name" placeholder="Enter name" type="text" name="field_name_<?php echo $field_count ?>" value="<?php if(!empty($postData)){if(isset($postData['field_name'])){echo $postData['field_name']; } } ?>" >
						</div>
						

						<div class="form-group col-md-6">
							<label>Type <span class="text-red">*</span></label>
							<select onchange="getNextFld(this.value, '<?php echo $field_count ?>', $(this))" required="required" class="form-control" name="field_type_<?php echo $field_count ?>">
								<option <?php if(!empty($postData)){if($postData['field_type'] == 'TEXT'){echo 'selected';}} ?> >TEXT</option>
								<option <?php if(!empty($postData)){if($postData['field_type'] == 'TEXTAREA'){echo 'selected';}} ?> >TEXTAREA</option>
								<option <?php if(!empty($postData)){if($postData['field_type'] == 'NUMBER'){echo 'selected';}} ?> >NUMBER</option>
								<option <?php if(!empty($postData)){if($postData['field_type'] == 'EMAIL'){echo 'selected';}} ?> >EMAIL</option>
								<option <?php if(!empty($postData)){if($postData['field_type'] == 'FILE'){echo 'selected';}} ?> >FILE</option>
								<option <?php if(!empty($postData)){if($postData['field_type'] == 'PASSWORD'){echo 'selected';}} ?> >PASSWORD</option>
								<option <?php if(!empty($postData)){if($postData['field_type'] == 'DATE'){echo 'selected';}} ?> >DATE</option>
								<option <?php if(!empty($postData)){if($postData['field_type'] == 'MONTH'){echo 'selected';}} ?> >MONTH</option>
								<option <?php if(!empty($postData)){if($postData['field_type'] == 'WEEK'){echo 'selected';}} ?> >WEEK</option>
								<option <?php if(!empty($postData)){if($postData['field_type'] == 'TIME'){echo 'selected';}} ?> >TIME</option>
								<option <?php if(!empty($postData)){if($postData['field_type'] == 'URL'){echo 'selected';}} ?> >URL</option>
								<option <?php if(!empty($postData)){if($postData['field_type'] == 'TEL'){echo 'selected';}} ?> >TEL</option>
								<option <?php if(!empty($postData)){if($postData['field_type'] == 'CHECKBOX'){echo 'selected';}} ?> >CHECKBOX</option>
								<option <?php if(!empty($postData)){if($postData['field_type'] == 'RADIO'){echo 'selected';}} ?> >RADIO</option>
								<option <?php if(!empty($postData)){if($postData['field_type'] == 'SELECT-BOX'){echo 'selected';}} ?> >SELECT-BOX</option>
							</select>
						</div>
						<div class="form-group col-md-6">
							<label>Label <span class="text-red">*</span></label>
							<input placeholder="Enter label" required="required" class="form-control" type="text" name="field_label_<?php echo $field_count ?>" value="<?php if(!empty($postData)){if(isset($postData['field_name'])){echo $postData['field_label']; } } ?>">
						</div>
						<div class="form-group col-md-3">
							<label>Duplicate <span class="text-red">*</span></label>
							<select required="required" class="form-control" name="field_duplicate_<?php echo $field_count ?>">
								<option <?php if(!empty($postData)){if($postData['field_duplicate'] == 'TRUE'){echo 'selected';}} ?> >TRUE</option>
								<option <?php if(!empty($postData)){if($postData['field_duplicate'] == 'FALSE'){echo 'selected';}} ?> >FALSE</option>
							</select>
						</div>
						<div class="form-group col-md-3">
							<label>Status <span class="text-red">*</span></label>
							<select required="required" class="form-control" name="field_display_status_<?php echo $field_count ?>">
								<option <?php if(!empty($postData)){if($postData['field_display_status'] == 'Active'){echo 'selected';}} ?>  >Active</option>
								<option  <?php if(!empty($postData)){if($postData['field_display_status'] == 'Inactive'){echo 'selected';}} ?> >Inactive</option>
							</select>
						</div>
					</div>
					<div class="col-md-12">
						<hr style="border-color: #c9c3c3" />
					</div>
					<div class="col-md-12">
						<div class="clearfix"></div>
						<div class="form-group col-md-12">
							<h3 class="btn btn-info" onclick="toggleDiv('field_row_<?php echo $field_count ?>', 'attribute')">Attributes</h3>
						</div>
						<div class="clearfix"></div>
						<span class="attribute" style="display:none">

							<?php if(!empty($postData)){
								$getAttributes = $this->tfn->getData('*', 'field_attributes', "status = 1 AND module_id = '".$postData['module_id']."' AND field_id = '".$postData['id']."' ");
								if($getAttributes !='No data'){
									foreach ($getAttributes as $key => $atr) {
										if($key == 0){
											?>
											<div class="attribute_div">
												<div class="form-group col-md-5">
													<label>Attribute Name <span class="text-red">*</span></label>
													<input placeholder="Enter name" class="form-control" type="text" name="attr_name_<?php echo $field_count ?>[]" required="required" value="<?php echo $atr['attribute_name']; ?>">
												</div>
												<div class="form-group col-md-5">
													<label>Attribute Value <span class="text-red">*</span> </label>
													<input placeholder="Enter value" class="form-control" type="text" name="attr_value_<?php echo $field_count ?>[]" required="required" value="<?php echo $atr['attribute_value']; ?>">
												</div>
												<div class="form-group col-md-2">
													<label>&nbsp;</label>
													<button type="button" class="btn btn-primary form-control" onclick="addFieldAtr('<?php echo $field_count ?>', $(this))"><i class="fa fa-plus"></i> </button>
												</div>
											</div>
											<div class="clearfix"></div>
											<?php
											if(count($getAttributes) == 1){
												echo '<div class="more_atr">';
											}
										}
										else{											
											if($key == 1){
												echo '<div class="more_atr">';
											}											
											$this->add_attr($atr, $field_count);
											
										}
									}
									echo '</div>';
								}
								
							}else{ ?>

							<div class="attribute_div">
								<div class="form-group col-md-5">
									<label>Attribute Name <span class="text-red">*</span></label>
									<input placeholder="Enter name" class="form-control" type="text" name="attr_name_<?php echo $field_count ?>[]" required="required" value="class">
								</div>
								<div class="form-group col-md-5">
									<label>Attribute Value <span class="text-red">*</span> </label>
									<input placeholder="Enter value" class="form-control" type="text" name="attr_value_<?php echo $field_count ?>[]" required="required" value="form-control">
								</div>
								<div class="form-group col-md-2">
									<label>&nbsp;</label>
									<button type="button" class="btn btn-primary form-control" onclick="addFieldAtr('<?php echo $field_count ?>', $(this))"><i class="fa fa-plus"></i> </button>
								</div>
							</div>
							<div class="clearfix"></div>
							<div class="more_atr">
									<span class="atr_child">
										<div class="form-group col-md-5">
											<label>Attribute Name <span class="text-red">*</span> </label>
											<input placeholder="Enter name" class="form-control" type="text" name="attr_name_<?php echo $field_count ?>[]" required="required" value="required">
										</div>
										<div class="form-group col-md-5">
											<label>Attribute Value <span class="text-red">*</span></label>
											<input placeholder="Enter value" class="form-control" type="text" name="attr_value_<?php echo $field_count ?>[]" required="required" value="required">
										</div>
										<div class="form-group col-md-2">
											<label>&nbsp;</label>
											<button type="button" class="btn btn-danger form-control" onclick="remFieldAtr('<?php echo $field_count ?>', $(this))"><i class="fa fa-minus"></i> </button>
										</div>
									</span>

									<span class="atr_child">
										<div class="form-group col-md-5">
											<label>Attribute Name <span class="text-red">*</span> </label>
											<input placeholder="Enter name" class="form-control" type="text" name="attr_name_<?php echo $field_count ?>[]" required="required" value="data-validation">
										</div>
										<div class="form-group col-md-5">
											<label>Attribute Value <span class="text-red">*</span></label>
											<input placeholder="Enter value" class="form-control" type="text" name="attr_value_<?php echo $field_count ?>[]" required="required" value="required">
										</div>
										<div class="form-group col-md-2">
											<label>&nbsp;</label>
											<button type="button" class="btn btn-danger form-control" onclick="remFieldAtr('<?php echo $field_count ?>', $(this))"><i class="fa fa-minus"></i> </button>
										</div>
									</span>

									<span class="atr_child">
										<div class="form-group col-md-5">
											<label>Attribute Name <span class="text-red">*</span> </label>
											<input placeholder="Enter name" class="form-control" type="text" name="attr_name_<?php echo $field_count ?>[]" required="required" value="data-validation-error-msg">
										</div>
										<div class="form-group col-md-5">
											<label>Attribute Value <span class="text-red">*</span></label>
											<input placeholder="Enter value" class="form-control" type="text" name="attr_value_<?php echo $field_count ?>[]" required="required" value="Please enter value">
										</div>
										<div class="form-group col-md-2">
											<label>&nbsp;</label>
											<button type="button" class="btn btn-danger form-control" onclick="remFieldAtr('<?php echo $field_count ?>', $(this))"><i class="fa fa-minus"></i> </button>
										</div>
									</span>
							</div>
							<?php } ?>
						</span>
					</div>
					<div class="clearfix"></div>
					<div class="select_box_inner">
						<?php if(!empty($postData)){
							if($postData['field_type'] == 'SELECT-BOX'){
								$this->select_box_op($postData, $field_count);
							}
						} ?>
					</div>
				</div>
			</span>
		</fieldset>
		<?php
	}

	public function new_field_inner()
	{	
		$field_count = $this->input->post('field_count');
		?>	
			<div class="col-md-12">

				<input type="hidden" name="module_parent_<?php echo $field_count ?>" value="0">


				<div class="form-group col-md-6">
					<label>Name <span class="text-red">*</span></label>
					<input required="required" class="form-control field_name" placeholder="Enter name" type="text" name="field_name_<?php echo $field_count ?>">
				</div>
				<div class="form-group col-md-6">
					<label>Type <span class="text-red">*</span></label>
					<select onchange="getNextFld(this.value, '<?php echo $field_count ?>', $(this))" required="required" class="form-control" name="field_type_<?php echo $field_count ?>">
						<option>TEXT</option>
						<option>TEXTAREA</option>
						<option>NUMBER</option>
						<option>EMAIL</option>
						<option>FILE</option>
						<option>PASSWORD</option>
						<option>DATE</option>
						<option>MONTH</option>
						<option>WEEK</option>
						<option>TIME</option>
						<option>URL</option>
						<option>TEL</option>
						<option>CHECKBOX</option>
						<option>RADIO</option>
						<option>SELECT-BOX</option>
					</select>
				</div>
				<div class="form-group col-md-6">
					<label>Label <span class="text-red">*</span></label>
					<input placeholder="Enter label" required="required" class="form-control" type="text" name="field_label_<?php echo $field_count ?>">
				</div>
				<div class="form-group col-md-3">
					<label>Duplicate <span class="text-red">*</span></label>
					<select required="required" class="form-control" name="field_duplicate_<?php echo $field_count ?>">
						<option>TRUE</option>
						<option>FALSE</option>
					</select>
				</div>

				<div class="form-group col-md-3">
					<label>Status <span class="text-red">*</span></label>
					<select required="required" class="form-control" name="field_display_status_<?php echo $field_count ?>">
						<option>Active</option>
						<option>Inactive</option>
					</select>
				</div>


			</div>
			<div class="col-md-12">
				<hr style="border-color: #c9c3c3" />
			</div>
			<div class="col-md-12">

				<div class="form-group col-md-12">
					<h3 class="btn btn-info" onclick="toggleDiv('field_row_<?php echo $field_count ?>', 'attribute')">Attributes</h3>
				</div>
				<span class="attribute" style="display: none;">

					<div class="attribute_div">
						<div class="form-group col-md-5">
							<label>Attribute Name <span class="text-red">*</span></label>
							<input placeholder="Enter name" class="form-control" type="text" name="attr_name_<?php echo $field_count ?>[]" required="required" value="class">
						</div>
						<div class="form-group col-md-5">
							<label>Attribute Value <span class="text-red">*</span> </label>
							<input placeholder="Enter value" class="form-control" type="text" name="attr_value_<?php echo $field_count ?>[]" required="required" value="form-control">
						</div>
						<div class="form-group col-md-2">
							<label>&nbsp;</label>
							<button type="button" class="btn btn-primary form-control" onclick="addFieldAtr('<?php echo $field_count ?>', $(this))"><i class="fa fa-plus"></i> </button>
						</div>
					</div>	
					<div class="clearfix"></div>				
					<div class="more_atr">
							<span class="atr_child">
								<div class="form-group col-md-5">
									<label>Attribute Name <span class="text-red">*</span> </label>
									<input placeholder="Enter name" class="form-control" type="text" name="attr_name_<?php echo $field_count ?>[]" required="required" value="required">
								</div>
								<div class="form-group col-md-5">
									<label>Attribute Value <span class="text-red">*</span></label>
									<input placeholder="Enter value" class="form-control" type="text" name="attr_value_<?php echo $field_count ?>[]" required="required" value="required">
								</div>
								<div class="form-group col-md-2">
									<label>&nbsp;</label>
									<button type="button" class="btn btn-danger form-control" onclick="remFieldAtr('<?php echo $field_count ?>', $(this))"><i class="fa fa-minus"></i> </button>
								</div>
							</span>

							<span class="atr_child">
								<div class="form-group col-md-5">
									<label>Attribute Name <span class="text-red">*</span> </label>
									<input placeholder="Enter name" class="form-control" type="text" name="attr_name_<?php echo $field_count ?>[]" required="required" value="data-validation">
								</div>
								<div class="form-group col-md-5">
									<label>Attribute Value <span class="text-red">*</span></label>
									<input placeholder="Enter value" class="form-control" type="text" name="attr_value_<?php echo $field_count ?>[]" required="required" value="required">
								</div>
								<div class="form-group col-md-2">
									<label>&nbsp;</label>
									<button type="button" class="btn btn-danger form-control" onclick="remFieldAtr('<?php echo $field_count ?>', $(this))"><i class="fa fa-minus"></i> </button>
								</div>
							</span>

							<span class="atr_child">
								<div class="form-group col-md-5">
									<label>Attribute Name <span class="text-red">*</span> </label>
									<input placeholder="Enter name" class="form-control" type="text" name="attr_name_<?php echo $field_count ?>[]" required="required" value="data-validation-error-msg">
								</div>
								<div class="form-group col-md-5">
									<label>Attribute Value <span class="text-red">*</span></label>
									<input placeholder="Enter value" class="form-control" type="text" name="attr_value_<?php echo $field_count ?>[]" required="required" value="Please enter value">
								</div>
								<div class="form-group col-md-2">
									<label>&nbsp;</label>
									<button type="button" class="btn btn-danger form-control" onclick="remFieldAtr('<?php echo $field_count ?>', $(this))"><i class="fa fa-minus"></i> </button>
								</div>
							</span>
					</div>
				</span>
			</div>
			<div class="clearfix"></div>
			<div class="select_box_inner"></div>
		
		<?php
	}

	public function new_db_field_inner($postData = '', $posted_field = '')
	{	

		if($posted_field ==''){
			$field_count = $this->input->post('field_count');
		}
		else{
			$field_count = $posted_field;
		}


		//$field_count = $this->input->post('field_count');
		//$listAllTables = $this->tfn->listAllTables();

		$listAllTables = $this->tfn->getData('id,module_name', 'modules', "status = 1 ");


		$ignoreTable = array('acl','acl_actions','acl_categories','auth_sessions','ci_sessions','clients','denied_access','edit_details','field_attributes','ips_on_hold','login_errors','module_fields','modules','password_history','username_or_email_on_hold','users');
		?>	

			<div class="field_row_<?php echo $field_count ?>">
				<div class="col-md-12">

					<input type="hidden" name="module_parent_<?php echo $field_count ?>" value="0">

					<div class="form-group col-md-6">
						<label>Name <span class="text-red">*</span></label>
						<input onkeyup="renameChild(this.value, '<?php echo $field_count ?>')" required="required" class="form-control field_name" placeholder="Enter name" type="text" name="field_name_<?php echo $field_count ?>" value="<?php if(!empty($postData)){if(isset($postData['field_name'])){echo $postData['field_name'];}} ?>">
					</div>
					<div class="form-group col-md-6">
						<label>Type <span class="text-red">*</span></label>
						<select onchange="getNextFldDB(this.value, '<?php echo $field_count ?>', $(this))" required="required" class="form-control" name="field_type_<?php echo $field_count ?>">							
							<option <?php if(!empty($postData)){if($postData['field_type']== 'SELECT-BOX'){echo 'selected';}} ?> >SELECT-BOX</option>							
							<option <?php if(!empty($postData)){if($postData['field_type']== 'CHECKBOX'){echo 'selected';}} ?> >CHECKBOX</option>
							<option <?php if(!empty($postData)){if($postData['field_type']== 'RADIO'){echo 'selected';}} ?> >RADIO</option>
							
						</select>
					</div>
					<div class="form-group col-md-6">
						<label>Label <span class="text-red">*</span></label>
						<input placeholder="Enter label" required="required" class="form-control" type="text" name="field_label_<?php echo $field_count ?>" value="<?php if(!empty($postData)){if(isset($postData['field_name'])){echo $postData['field_label'];}} ?>">
					</div>
					<div class="form-group col-md-3">
						<label>Duplicate <span class="text-red">*</span></label>
						<select required="required" class="form-control" name="field_duplicate_<?php echo $field_count ?>">
							<option <?php if(!empty($postData)){if($postData['field_duplicate']== 'TRUE'){echo 'selected';}} ?> >TRUE</option>
							<option <?php if(!empty($postData)){if($postData['field_duplicate']== 'FALSE'){echo 'selected';}} ?> >FALSE</option>
						</select>
					</div>
					<div class="form-group col-md-3">
						<label>Status <span class="text-red">*</span></label>
						<select required="required" class="form-control" name="field_display_status_<?php echo $field_count ?>">
							<option <?php if(!empty($postData)){if($postData['field_display_status']== 'Active'){echo 'selected';}} ?> >Active</option>
							<option <?php if(!empty($postData)){if($postData['field_display_status']== 'Inactive'){echo 'selected';}} ?> >Inactive</option>
						</select>
					</div>
				</div>
				<div class="col-md-12">
					<hr style="border-color: #c9c3c3" />
				</div>
				<div class="col-md-12">
					<div class="form-group col-md-12">
						<h3 class="btn btn-info" onclick="toggleDiv('field_row_<?php echo $field_count ?>', 'attribute')">Attributes</h3>
					</div>
					<span class="attribute" style="display:none">
							<?php if(!empty($postData)){
								$getAttributes = $this->tfn->getData('*', 'field_attributes', "status = 1 AND module_id = '".$postData['module_id']."' AND field_id = '".$postData['id']."' ");
								if($getAttributes !='No data'){
									foreach ($getAttributes as $key => $atr) {
										if($key == 0){
											?>
											<div class="attribute_div">
												<div class="form-group col-md-5">
													<label>Attribute Name <span class="text-red">*</span></label>
													<input placeholder="Enter name" class="form-control" type="text" name="attr_name_<?php echo $field_count ?>[]" required="required" value="<?php echo $atr['attribute_name']; ?>">
												</div>
												<div class="form-group col-md-5">
													<label>Attribute Value <span class="text-red">*</span> </label>
													<input placeholder="Enter value" class="form-control" type="text" name="attr_value_<?php echo $field_count ?>[]" required="required" value="<?php echo $atr['attribute_value']; ?>">
												</div>
												<div class="form-group col-md-2">
													<label>&nbsp;</label>
													<button type="button" class="btn btn-primary form-control" onclick="addFieldAtr('<?php echo $field_count ?>', $(this))"><i class="fa fa-plus"></i> </button>
												</div>
											</div>
											<div class="clearfix"></div>
											<?php
										}
										else{
											if($key == 1){
												echo '<div class="more_atr">';
											}											
											$this->add_attr($atr, $field_count);
											
										}
									}
									echo '</div>';
								}
								
							}else{ ?>

							<div class="attribute_div">
								<div class="form-group col-md-5">
									<label>Attribute Name <span class="text-red">*</span></label>
									<input placeholder="Enter name" class="form-control" type="text" name="attr_name_<?php echo $field_count ?>[]" required="required" value="class">
								</div>
								<div class="form-group col-md-5">
									<label>Attribute Value <span class="text-red">*</span> </label>
									<input placeholder="Enter value" class="form-control" type="text" name="attr_value_<?php echo $field_count ?>[]" required="required" value="form-control">
								</div>
								<div class="form-group col-md-2">
									<label>&nbsp;</label>
									<button type="button" class="btn btn-primary form-control" onclick="addFieldAtr('<?php echo $field_count ?>', $(this))"><i class="fa fa-plus"></i> </button>
								</div>
							</div>
							<div class="clearfix"></div>
							<div class="more_atr">
									<span class="atr_child">
										<div class="form-group col-md-5">
											<label>Attribute Name <span class="text-red">*</span> </label>
											<input placeholder="Enter name" class="form-control" type="text" name="attr_name_<?php echo $field_count ?>[]" required="required" value="required">
										</div>
										<div class="form-group col-md-5">
											<label>Attribute Value <span class="text-red">*</span></label>
											<input placeholder="Enter value" class="form-control" type="text" name="attr_value_<?php echo $field_count ?>[]" required="required" value="required">
										</div>
										<div class="form-group col-md-2">
											<label>&nbsp;</label>
											<button type="button" class="btn btn-danger form-control" onclick="remFieldAtr('<?php echo $field_count ?>', $(this))"><i class="fa fa-minus"></i> </button>
										</div>
									</span>

									<span class="atr_child">
										<div class="form-group col-md-5">
											<label>Attribute Name <span class="text-red">*</span> </label>
											<input placeholder="Enter name" class="form-control" type="text" name="attr_name_<?php echo $field_count ?>[]" required="required" value="data-validation">
										</div>
										<div class="form-group col-md-5">
											<label>Attribute Value <span class="text-red">*</span></label>
											<input placeholder="Enter value" class="form-control" type="text" name="attr_value_<?php echo $field_count ?>[]" required="required" value="required">
										</div>
										<div class="form-group col-md-2">
											<label>&nbsp;</label>
											<button type="button" class="btn btn-danger form-control" onclick="remFieldAtr('<?php echo $field_count ?>', $(this))"><i class="fa fa-minus"></i> </button>
										</div>
									</span>

									<span class="atr_child">
										<div class="form-group col-md-5">
											<label>Attribute Name <span class="text-red">*</span> </label>
											<input placeholder="Enter name" class="form-control" type="text" name="attr_name_<?php echo $field_count ?>[]" required="required" value="data-validation-error-msg">
										</div>
										<div class="form-group col-md-5">
											<label>Attribute Value <span class="text-red">*</span></label>
											<input placeholder="Enter value" class="form-control" type="text" name="attr_value_<?php echo $field_count ?>[]" required="required" value="Please enter value">
										</div>
										<div class="form-group col-md-2">
											<label>&nbsp;</label>
											<button type="button" class="btn btn-danger form-control" onclick="remFieldAtr('<?php echo $field_count ?>', $(this))"><i class="fa fa-minus"></i> </button>
										</div>
									</span>
							</div>
							<?php } ?>
						</span>
				</div>
				<div class="clearfix"></div>
				<div class="select_box_inner">

					<?php 
					if(!empty($postData)){
						if($postData['field_type'] == 'CHECKBOX'){
							$this->select_box_check_db($postData, $field_count);
						}
						if($postData['field_type'] == 'SELECT-BOX'){
							$this->select_box_op_db($postData, $field_count);
						}
						if($postData['field_type'] == 'RADIO'){
							$this->select_box_radio_db($postData, $field_count);
						}
					}else{ ?>

						<div class="col-md-12">
							<hr style="border-color: #c9c3c3" />
						</div>

						<input type="hidden" name="module_parent_<?php echo $field_count ?>" value="0">

						<input type="hidden" id="db_row_pc_<?php echo $field_count ?>" value="1">

						<div class="col-md-12">

							<div class="col-md-12">
								<table class="designTable">
									<tr>
										<td>
											<div class="form-group">
												<label>Select Table <span class="text-red">*</span> </label>
												<select required="required" onchange="getDbTableFieldsMain(this.value, '<?php echo $field_count ?>', '1', $(this))" class="form-control module_name_row" name="module_name_<?php echo $field_count ?>" data-validation="required" data-validation-error-msg="Please table">
													<option value="">Select Table</option>
													<?php foreach ($listAllTables as $table_name) {
														if(!in_array($table_name['module_name'], $ignoreTable) ){
															echo '<option value="'.$table_name['id'].'">'.$table_name['module_name'].'</option>';
														}
													} ?>
													
												</select>
											</div>
										</td>
										<td>
											<div class="form-group">
												<label>Select Column <span class="text-red">*</span></label>
												<select required="required" class="form-control module_column" name="module_column_<?php echo $field_count ?>" data-validation="required" data-validation-error-msg="Please module column">
													<option value="">Select Table first</option>
												</select>
											</div>
										</td>
									</tr>
									<tr>
										<td>
											<div class="form-group">
												<label>Field Type <span class="text-red">*</span></label>
												<select required="required" class="form-control" name="module_field_type_<?php echo $field_count ?>" data-validation="required" data-validation-error-msg="Please select field type">
													<option value="drow_dwn_single">Dropdown single selection</option>
													<option value="drow_dwn_multi">Dropdown multiple selection</option>
												</select>
											</div>
										</td>
										<td>
											<div class="form-group">
												<label>Child <span class="text-red">*</span></label>
												<select onchange="getModuleChild(this.value, '<?php echo $field_count ?>', '1', $(this))" required="required" class="form-control module_child_yew_no"  name="module_child_<?php echo $field_count ?>">
													<option>No</option>
													<option>Yes</option>
												</select>
											</div>
										</td>
									</tr>
								</table>
							</div>
							<div class="clearfix"></div>
							<div class="module_child_more"></div>

						</div>
						<?php 
					} ?>
				</div>
		<?php
	}


	public function get_child_modules($postData = '', $posted_field = '', $parent_id = '', $parent_name = '')
	{	

		if(!empty($postData)){

				//print_r($postData);
				//echo "asdf";
				
				$child_field = $this->tfn->getData('*', 'module_fields', "status = 1 AND module_parent = '".$postData['field_name']."' AND module_id = '".$postData['module_id']."' ");

				//print_r($child_field);

				$field_count = $posted_field;		
				$module_parent = $parent_id;
				$parent_name = $parent_name;
				$parent_row_num = $posted_field;


				$listAllTables = $this->tfn->getData('id,module_name', 'modules', "status = 1 AND id = '".$module_parent."' ");

				$ignoreTable = array('acl','acl_actions','acl_categories','auth_sessions','ci_sessions','clients','denied_access','edit_details','field_attributes','ips_on_hold','login_errors','module_fields','modules','password_history','username_or_email_on_hold','users');


				$moduleNamesArray = array();
				if($child_field !='No data'){
					foreach ($child_field as $ch_key => $ch_row) { $field_count++;
						$moduleNamesArray[] = $ch_row['child_module_name'];
						if($ch_key == 0){
							?>
							<div class="clearfix"></div>
							<div class="form-group col-md-12" align="center">
								<i class="fa fa-arrow-up text-success"></i><br/>
								<span class="label bg-green">Parent</span> <br/>
								<span class="label bg-blue"> Child </span><br/>
								<i class="fa fa-arrow-down text-primary"></i>
							</div>
							<div class="clearfix"></div>
							<div class="border-green">
								<div class="row">
									<div class="col-md-12">
							<?php
						}
						?>
						<div class="form-group col-md-12">
							<label>Module Name:  <?php echo $ch_row['child_module_name']; ?> 
							<input checked name="child_module_name_<?php echo $field_count ?>" class="module_enable" type="checkbox" onclick="fieldsEnable('<?php echo $field_count ?>', $(this))" value="<?php echo $ch_row['child_module_name'] ?>">
							</label>
						</div>
						<div class="row chid_row_<?php echo $field_count ?>">
							<input type="hidden" class="module_child_class_<?php echo $parent_row_num ?>" name="module_parent_<?php echo $field_count ?>" value="<?php echo $postData['field_name'] ?>">
							<input type="hidden" name="input_type_<?php echo $field_count ?>" value="Database">
							<div class="col-md-12">
								<div class="form-group col-md-6">
									<label>Name <span class="text-red">*</span></label>
									<input required="required" class="form-control field_name" placeholder="Enter name" type="text" name="field_name_<?php echo $field_count ?>" value="<?php echo $ch_row['field_name']; ?>">
								</div>
								<div class="form-group col-md-6">
									<label>Type <span class="text-red">*</span></label>
									<select onchange="getNextFldDBChild(this.value, '<?php echo $field_count ?>', $(this), '<?php echo $module_parent ?>')" required="required" class="form-control" name="field_type_<?php echo $field_count ?>">							
										<option <?php if($ch_row['field_type'] == 'SELECT-BOX'){echo 'selected';} ?> >SELECT-BOX</option>							
										<option <?php if($ch_row['field_type'] == 'CHECKBOX'){echo 'selected';} ?> >CHECKBOX</option>
										<option <?php if($ch_row['field_type'] == 'RADIO'){echo 'selected';} ?> >RADIO</option>
										
									</select>
								</div>
								<div class="form-group col-md-6">
									<label>Label <span class="text-red">*</span></label>
									<input placeholder="Enter label" required="required" class="form-control" type="text" name="field_label_<?php echo $field_count ?>" value="<?php echo $ch_row['field_label']; ?>">
								</div>
								<div class="form-group col-md-3">
									<label>Duplicate <span class="text-red">*</span></label>
									<select required="required" class="form-control" name="field_duplicate_<?php echo $field_count ?>">
										<option <?php if($ch_row['field_duplicate'] == 'TRUE'){echo 'selected';} ?> >TRUE</option>
										<option <?php if($ch_row['field_duplicate'] == 'FALSE'){echo 'selected';} ?> >FALSE</option>
									</select>
								</div>
								<div class="form-group col-md-3">
									<label>Status <span class="text-red">*</span></label>
									<select required="required" class="form-control" name="field_display_status_<?php echo $field_count ?>">
										<option <?php if($ch_row['field_display_status'] == 'Active'){echo 'selected';} ?> >Active</option>
										<option <?php if($ch_row['field_display_status'] == 'Inactive'){echo 'selected';} ?> >Inactive</option>
									</select>
								</div>
							</div>
							<div class="col-md-12">
								<hr style="border-color: #c9c3c3" />
							</div>
							<div class="col-md-12">
								<div class="form-group col-md-12">
									<h3 class="btn btn-info" onclick="toggleDiv('chid_row_<?php echo $field_count ?>', 'attribute')">Attributes</h3>
								</div>


								<span class="attribute" style="display:none">

									<?php if(!empty($postData)){
										$getAttributes = $this->tfn->getData('*', 'field_attributes', "status = 1 AND module_id = '".$ch_row['module_id']."' AND field_id = '".$ch_row['id']."' ");
										if($getAttributes !='No data'){
											foreach ($getAttributes as $key => $atr) {
												if($key == 0){
													?>
													<div class="attribute_div">
														<div class="form-group col-md-5">
															<label>Attribute Name <span class="text-red">*</span></label>
															<input placeholder="Enter name" class="form-control" type="text" name="attr_name_<?php echo $field_count ?>[]" required="required" value="<?php echo $atr['attribute_name']; ?>">
														</div>
														<div class="form-group col-md-5">
															<label>Attribute Value <span class="text-red">*</span> </label>
															<input placeholder="Enter value" class="form-control" type="text" name="attr_value_<?php echo $field_count ?>[]" required="required" value="<?php echo $atr['attribute_value']; ?>">
														</div>
														<div class="form-group col-md-2">
															<label>&nbsp;</label>
															<button type="button" class="btn btn-primary form-control" onclick="addFieldAtr('<?php echo $field_count ?>', $(this))"><i class="fa fa-plus"></i> </button>
														</div>
													</div>
													<div class="clearfix"></div>
													<?php
												}
												else{
													if($key == 1){
														echo '<div class="more_atr">';
													}											
													$this->add_attr($atr, $field_count);
													
												}
											}
											echo '</div>';
										}
										
									}else{ ?>

									<div class="attribute_div">
										<div class="form-group col-md-5">
											<label>Attribute Name <span class="text-red">*</span></label>
											<input placeholder="Enter name" class="form-control" type="text" name="attr_name_<?php echo $field_count ?>[]" required="required" value="class">
										</div>
										<div class="form-group col-md-5">
											<label>Attribute Value <span class="text-red">*</span> </label>
											<input placeholder="Enter value" class="form-control" type="text" name="attr_value_<?php echo $field_count ?>[]" required="required" value="form-control">
										</div>
										<div class="form-group col-md-2">
											<label>&nbsp;</label>
											<button type="button" class="btn btn-primary form-control" onclick="addFieldAtr('<?php echo $field_count ?>', $(this))"><i class="fa fa-plus"></i> </button>
										</div>
									</div>
									<div class="clearfix"></div>
									<div class="more_atr">
											<span class="atr_child">
												<div class="form-group col-md-5">
													<label>Attribute Name <span class="text-red">*</span> </label>
													<input placeholder="Enter name" class="form-control" type="text" name="attr_name_<?php echo $field_count ?>[]" required="required" value="required">
												</div>
												<div class="form-group col-md-5">
													<label>Attribute Value <span class="text-red">*</span></label>
													<input placeholder="Enter value" class="form-control" type="text" name="attr_value_<?php echo $field_count ?>[]" required="required" value="required">
												</div>
												<div class="form-group col-md-2">
													<label>&nbsp;</label>
													<button type="button" class="btn btn-danger form-control" onclick="remFieldAtr('<?php echo $field_count ?>', $(this))"><i class="fa fa-minus"></i> </button>
												</div>
											</span>

											<span class="atr_child">
												<div class="form-group col-md-5">
													<label>Attribute Name <span class="text-red">*</span> </label>
													<input placeholder="Enter name" class="form-control" type="text" name="attr_name_<?php echo $field_count ?>[]" required="required" value="data-validation">
												</div>
												<div class="form-group col-md-5">
													<label>Attribute Value <span class="text-red">*</span></label>
													<input placeholder="Enter value" class="form-control" type="text" name="attr_value_<?php echo $field_count ?>[]" required="required" value="required">
												</div>
												<div class="form-group col-md-2">
													<label>&nbsp;</label>
													<button type="button" class="btn btn-danger form-control" onclick="remFieldAtr('<?php echo $field_count ?>', $(this))"><i class="fa fa-minus"></i> </button>
												</div>
											</span>

											<span class="atr_child">
												<div class="form-group col-md-5">
													<label>Attribute Name <span class="text-red">*</span> </label>
													<input placeholder="Enter name" class="form-control" type="text" name="attr_name_<?php echo $field_count ?>[]" required="required" value="data-validation-error-msg">
												</div>
												<div class="form-group col-md-5">
													<label>Attribute Value <span class="text-red">*</span></label>
													<input placeholder="Enter value" class="form-control" type="text" name="attr_value_<?php echo $field_count ?>[]" required="required" value="Please enter value">
												</div>
												<div class="form-group col-md-2">
													<label>&nbsp;</label>
													<button type="button" class="btn btn-danger form-control" onclick="remFieldAtr('<?php echo $field_count ?>', $(this))"><i class="fa fa-minus"></i> </button>
												</div>
											</span>
									</div>
									<?php } ?>
								</span>


							</div>
							<div class="clearfix"></div>
							<div class="select_box_inner">
								<?php 
									if(!empty($postData)){
										if($postData['field_type'] == 'CHECKBOX'){
											$this->select_box_check_db_child($ch_row, $field_count, $ch_row['child_module_name']);
										}
										if($postData['field_type'] == 'SELECT-BOX'){
											$this->select_box_op_db_child($ch_row, $field_count, $ch_row['child_module_name']);
										}
										if($postData['field_type'] == 'RADIO'){
											$this->select_box_radio_db_child($ch_row, $field_count, $ch_row['child_module_name']);
										}
									} 
								?>
							</div>
						</div>

						<script type="text/javascript">
							$('#field_count_up').val('<?php echo $field_count; ?>');
						</script>
						<?php
						
					}
					
				}

				
				

				$rel = $this->tfn->getData('*', 'tables_relation', "status = 1 AND parent_table_id = '$module_parent' ");
				if($rel !='No data'){
					foreach ($rel as $chile_m_count => $rel_row) {
								
								$listAllTables = $this->tfn->getData('id,module_name', 'modules', "status = 1 AND id = '".$rel_row['child_table_id']."' ");

								$ignoreTable = array('acl','acl_actions','acl_categories','auth_sessions','ci_sessions','clients','denied_access','edit_details','field_attributes','ips_on_hold','login_errors','module_fields','modules','password_history','username_or_email_on_hold','users');

								if(in_array($listAllTables[0]['module_name'], $moduleNamesArray)){
									continue;
								}
								else{
									$field_count++;
								}

								?>

								<div class="form-group col-md-12" align="center">
									<hr/>
								</div>


								<div class="form-group col-md-12">
									<label>Module Name:  <?php echo $listAllTables[0]['module_name'] ?> 
									<input name="child_module_name_<?php echo $field_count ?>" class="module_enable" type="checkbox" onclick="fieldsEnable('<?php echo $field_count ?>', $(this))" value="<?php echo $listAllTables[0]['module_name'] ?>">
									</label>
								</div>


								<div class="row chid_row_<?php echo $field_count ?>">

									<input type="hidden" class="module_child_class_<?php echo $parent_row_num ?>" name="module_parent_<?php echo $field_count ?>" value="<?php echo $parent_name ?>">


									<input type="hidden" name="input_type_<?php echo $field_count ?>" value="Database">


									<div class="col-md-12">
										<div class="form-group col-md-6">
											<label>Name <span class="text-red">*</span></label>
											<input required="required" class="form-control field_name" placeholder="Enter name" type="text" name="field_name_<?php echo $field_count ?>">
										</div>
										<div class="form-group col-md-6">
											<label>Type <span class="text-red">*</span></label>
											<select onchange="getNextFldDBChild(this.value, '<?php echo $field_count ?>', $(this), '<?php echo $listAllTables[0]['id'] ?>')" required="required" class="form-control" name="field_type_<?php echo $field_count ?>">							
												<option>SELECT-BOX</option>							
												<option>CHECKBOX</option>
												<option>RADIO</option>
												
											</select>
										</div>
										<div class="form-group col-md-6">
											<label>Label <span class="text-red">*</span></label>
											<input placeholder="Enter label" required="required" class="form-control" type="text" name="field_label_<?php echo $field_count ?>">
										</div>
										<div class="form-group col-md-3">
											<label>Duplicate <span class="text-red">*</span></label>
											<select required="required" class="form-control" name="field_duplicate_<?php echo $field_count ?>">
												<option>TRUE</option>
												<option>FALSE</option>
											</select>
										</div>
										<div class="form-group col-md-3">
											<label>Duplicate <span class="text-red">*</span></label>
											<select required="required" class="form-control" name="field_display_status_<?php echo $field_count ?>">
												<option>Active</option>
												<option>Inactive</option>
											</select>
										</div>
									</div>
									<div class="col-md-12">
										<hr style="border-color: #c9c3c3" />
									</div>
									<div class="col-md-12">

										<div class="form-group col-md-12">
											<h3 class="btn btn-info" onclick="toggleDiv('chid_row_<?php echo $field_count ?>', 'attribute')">Attributes</h3>
										</div>
										<span class="attribute" style="display: none;">

											<div class="attribute_div">
												<div class="form-group col-md-5">
													<label>Attribute Name <span class="text-red">*</span></label>
													<input placeholder="Enter name" class="form-control" type="text" name="attr_name_<?php echo $field_count ?>[]" required="required" value="class">
												</div>
												<div class="form-group col-md-5">
													<label>Attribute Value <span class="text-red">*</span> </label>
													<input placeholder="Enter value" class="form-control" type="text" name="attr_value_<?php echo $field_count ?>[]" required="required" value="form-control">
												</div>
												<div class="form-group col-md-2">
													<label>&nbsp;</label>
													<button type="button" class="btn btn-primary form-control" onclick="addFieldAtr('<?php echo $field_count ?>', $(this))"><i class="fa fa-plus"></i> </button>
												</div>
											</div>
											<div class="clearfix"></div>
											<div class="more_atr">
													<span class="atr_child">
														<div class="form-group col-md-5">
															<label>Attribute Name <span class="text-red">*</span> </label>
															<input placeholder="Enter name" class="form-control" type="text" name="attr_name_<?php echo $field_count ?>[]" required="required" value="required">
														</div>
														<div class="form-group col-md-5">
															<label>Attribute Value <span class="text-red">*</span></label>
															<input placeholder="Enter value" class="form-control" type="text" name="attr_value_<?php echo $field_count ?>[]" required="required" value="required">
														</div>
														<div class="form-group col-md-2">
															<label>&nbsp;</label>
															<button type="button" class="btn btn-danger form-control" onclick="remFieldAtr('<?php echo $field_count ?>', $(this))"><i class="fa fa-minus"></i> </button>
														</div>
													</span>

													<span class="atr_child">
														<div class="form-group col-md-5">
															<label>Attribute Name <span class="text-red">*</span> </label>
															<input placeholder="Enter name" class="form-control" type="text" name="attr_name_<?php echo $field_count ?>[]" required="required" value="data-validation">
														</div>
														<div class="form-group col-md-5">
															<label>Attribute Value <span class="text-red">*</span></label>
															<input placeholder="Enter value" class="form-control" type="text" name="attr_value_<?php echo $field_count ?>[]" required="required" value="required">
														</div>
														<div class="form-group col-md-2">
															<label>&nbsp;</label>
															<button type="button" class="btn btn-danger form-control" onclick="remFieldAtr('<?php echo $field_count ?>', $(this))"><i class="fa fa-minus"></i> </button>
														</div>
													</span>

													<span class="atr_child">
														<div class="form-group col-md-5">
															<label>Attribute Name <span class="text-red">*</span> </label>
															<input placeholder="Enter name" class="form-control" type="text" name="attr_name_<?php echo $field_count ?>[]" required="required" value="data-validation-error-msg">
														</div>
														<div class="form-group col-md-5">
															<label>Attribute Value <span class="text-red">*</span></label>
															<input placeholder="Enter value" class="form-control" type="text" name="attr_value_<?php echo $field_count ?>[]" required="required" value="Please enter value">
														</div>
														<div class="form-group col-md-2">
															<label>&nbsp;</label>
															<button type="button" class="btn btn-danger form-control" onclick="remFieldAtr('<?php echo $field_count ?>', $(this))"><i class="fa fa-minus"></i> </button>
														</div>
													</span>
											</div>
										</span>
									</div>
									<div class="clearfix"></div>
									<div class="select_box_inner">

										<div class="col-md-12">
											<hr style="border-color: #c9c3c3" />
										</div>
										<div class="clearfix"></div>
										<div class="col-md-12">
											<div class="form-group col-md-6">
												<label>Select Table <span class="text-red">*</span> </label>
												<select disabled required="required" onchange="getDbTableFieldsMain(this.value, '<?php echo $field_count ?>', '1', $(this))" class="form-control module_name_row" name="module_name_<?php echo $field_count ?>" data-validation="required" data-validation-error-msg="Please table">
													<option value="">Select Table</option>
													<?php foreach ($listAllTables as $table_name) {
														if(!in_array($table_name['module_name'], $ignoreTable) ){
															echo '<option value="'.$table_name['id'].'">'.$table_name['module_name'].'</option>';
														}
													} ?>
													
												</select>
											</div>
											<div class="form-group col-md-6">
												<label>Select Column <span class="text-red">*</span></label>
												<select disabled required="required" class="form-control module_column" name="module_column_<?php echo $field_count ?>" data-validation="required" data-validation-error-msg="Please module column">
													<option value="">Select Table first</option>
												</select>
											</div>
											<div class="form-group col-md-6">
												<label>Field Type <span class="text-red">*</span></label>
												<select disabled required="required" class="form-control module_field_type" name="module_field_type_<?php echo $field_count ?>"  data-validation="required" data-validation-error-msg="Please select field type">
													<option value="drow_dwn_single">Dropdown single selection</option>
													<option value="drow_dwn_multi">Dropdown multiple selection</option>
												</select>
											</div>
											<div class="form-group col-md-6">
												<label>Child <span class="text-red">*</span></label>
												<select disabled onchange="getModuleChild(this.value, '<?php echo $field_count ?>', '1', $(this))" required="required" class="form-control module_child_yew_no" name="module_child_<?php echo $field_count ?>">
													<option>No</option>
													<option>Yes</option>
												</select>
											</div>
											<div class="clearfix"></div>
											<div class="module_child_more"></div>
																					
										</div>
									</div>
								</div>
								<script type="text/javascript">
									disabledAllChild('chid_row_<?php echo $field_count ?>');
								</script>	
								<script type="text/javascript">
									$('#field_count_up').val('<?php echo $field_count; ?>');
								</script>

								<?php
						}

				}
				echo "</div></div></div>";

				//$listAllTables = $this->tfn->getData('id,module_name', 'modules', "status = 1 AND id = '".$module_parent."' ");

				


		}else{
			$field_count = $this->input->post('field_count');		
			$module_parent = $this->input->post('module_parent');
			$parent_name = $this->input->post('parent_name');
			$parent_row_num = $this->input->post('parent_row_num');

			// SELECT GROUP_CONCAT(id) as all_ids FROM modules WHERE status = 1

			$rel = $this->tfn->getData('*', 'tables_relation', "status = 1 AND parent_table_id = '$module_parent' ");

			if($rel !='No data'){
				?>
				<div class="clearfix"></div>
				<div class="form-group col-md-12" align="center">
					<i class="fa fa-arrow-up text-success"></i><br/>
					<span class="label bg-green">Parent</span> <br/>
					<span class="label bg-blue"> Child </span><br/>
					<i class="fa fa-arrow-down text-primary"></i>
				</div>
				<div class="clearfix"></div>
				<div class="border-green">
					<div class="row">
						<div class="col-md-12">
						
							<?php
							
							foreach ($rel as $chile_m_count => $rel_row) {
								
								$listAllTables = $this->tfn->getData('id,module_name', 'modules', "status = 1 AND id = '".$rel_row['child_table_id']."' ");

								$ignoreTable = array('acl','acl_actions','acl_categories','auth_sessions','ci_sessions','clients','denied_access','edit_details','field_attributes','ips_on_hold','login_errors','module_fields','modules','password_history','username_or_email_on_hold','users');

								if($chile_m_count > 0 ){
								?>
				
								<div class="form-group col-md-12" align="center">
									<hr/>
								</div>
								<?php } ?>


								


								
								<div class="form-group col-md-12">
									<label>Module Name:  <?php echo $listAllTables[0]['module_name'] ?> 
									<input name="child_module_name_<?php echo $field_count ?>" class="module_enable" type="checkbox" onclick="fieldsEnable('<?php echo $field_count ?>', $(this))" value="<?php echo $listAllTables[0]['module_name'] ?>">
									</label>
								</div>


								<div class="row chid_row_<?php echo $field_count ?>">

									<input type="hidden" class="module_child_class_<?php echo $parent_row_num ?>" name="module_parent_<?php echo $field_count ?>" value="<?php echo $parent_name ?>">


									<input type="hidden" name="input_type_<?php echo $field_count ?>" value="Database">


									<div class="col-md-12">
										<div class="form-group col-md-6">
											<label>Name <span class="text-red">*</span></label>
											<input required="required" class="form-control field_name" placeholder="Enter name" type="text" name="field_name_<?php echo $field_count ?>">
										</div>
										<div class="form-group col-md-6">
											<label>Type <span class="text-red">*</span></label>
											<select onchange="getNextFldDBChild(this.value, '<?php echo $field_count ?>', $(this), '<?php echo $listAllTables[0]['id'] ?>')" required="required" class="form-control" name="field_type_<?php echo $field_count ?>">							
												<option>SELECT-BOX</option>							
												<option>CHECKBOX</option>
												<option>RADIO</option>
												
											</select>
										</div>
										<div class="form-group col-md-6">
											<label>Label <span class="text-red">*</span></label>
											<input placeholder="Enter label" required="required" class="form-control" type="text" name="field_label_<?php echo $field_count ?>">
										</div>
										<div class="form-group col-md-3">
											<label>Duplicate <span class="text-red">*</span></label>
											<select required="required" class="form-control" name="field_duplicate_<?php echo $field_count ?>">
												<option>TRUE</option>
												<option>FALSE</option>
											</select>
										</div>
										<div class="form-group col-md-3">
											<label>Duplicate <span class="text-red">*</span></label>
											<select required="required" class="form-control" name="field_display_status_<?php echo $field_count ?>">
												<option>Active</option>
												<option>Inactive</option>
											</select>
										</div>
									</div>
									<div class="col-md-12">
										<hr style="border-color: #c9c3c3" />
									</div>
									<div class="col-md-12">

										<div class="form-group col-md-12">
											<h3 class="btn btn-info" onclick="toggleDiv('chid_row_<?php echo $field_count ?>', 'attribute')">Attributes</h3>
										</div>
										<span class="attribute" style="display: none;">

											<div class="attribute_div">
												<div class="form-group col-md-5">
													<label>Attribute Name <span class="text-red">*</span></label>
													<input placeholder="Enter name" class="form-control" type="text" name="attr_name_<?php echo $field_count ?>[]" required="required" value="class">
												</div>
												<div class="form-group col-md-5">
													<label>Attribute Value <span class="text-red">*</span> </label>
													<input placeholder="Enter value" class="form-control" type="text" name="attr_value_<?php echo $field_count ?>[]" required="required" value="form-control">
												</div>
												<div class="form-group col-md-2">
													<label>&nbsp;</label>
													<button type="button" class="btn btn-primary form-control" onclick="addFieldAtr('<?php echo $field_count ?>', $(this))"><i class="fa fa-plus"></i> </button>
												</div>
											</div>
											<div class="clearfix"></div>
											<div class="more_atr">
													<span class="atr_child">
														<div class="form-group col-md-5">
															<label>Attribute Name <span class="text-red">*</span> </label>
															<input placeholder="Enter name" class="form-control" type="text" name="attr_name_<?php echo $field_count ?>[]" required="required" value="required">
														</div>
														<div class="form-group col-md-5">
															<label>Attribute Value <span class="text-red">*</span></label>
															<input placeholder="Enter value" class="form-control" type="text" name="attr_value_<?php echo $field_count ?>[]" required="required" value="required">
														</div>
														<div class="form-group col-md-2">
															<label>&nbsp;</label>
															<button type="button" class="btn btn-danger form-control" onclick="remFieldAtr('<?php echo $field_count ?>', $(this))"><i class="fa fa-minus"></i> </button>
														</div>
													</span>

													<span class="atr_child">
														<div class="form-group col-md-5">
															<label>Attribute Name <span class="text-red">*</span> </label>
															<input placeholder="Enter name" class="form-control" type="text" name="attr_name_<?php echo $field_count ?>[]" required="required" value="data-validation">
														</div>
														<div class="form-group col-md-5">
															<label>Attribute Value <span class="text-red">*</span></label>
															<input placeholder="Enter value" class="form-control" type="text" name="attr_value_<?php echo $field_count ?>[]" required="required" value="required">
														</div>
														<div class="form-group col-md-2">
															<label>&nbsp;</label>
															<button type="button" class="btn btn-danger form-control" onclick="remFieldAtr('<?php echo $field_count ?>', $(this))"><i class="fa fa-minus"></i> </button>
														</div>
													</span>

													<span class="atr_child">
														<div class="form-group col-md-5">
															<label>Attribute Name <span class="text-red">*</span> </label>
															<input placeholder="Enter name" class="form-control" type="text" name="attr_name_<?php echo $field_count ?>[]" required="required" value="data-validation-error-msg">
														</div>
														<div class="form-group col-md-5">
															<label>Attribute Value <span class="text-red">*</span></label>
															<input placeholder="Enter value" class="form-control" type="text" name="attr_value_<?php echo $field_count ?>[]" required="required" value="Please enter value">
														</div>
														<div class="form-group col-md-2">
															<label>&nbsp;</label>
															<button type="button" class="btn btn-danger form-control" onclick="remFieldAtr('<?php echo $field_count ?>', $(this))"><i class="fa fa-minus"></i> </button>
														</div>
													</span>
											</div>
										</span>
									</div>
									<div class="clearfix"></div>
									<div class="select_box_inner">

										<div class="col-md-12">
											<hr style="border-color: #c9c3c3" />
										</div>
										<div class="clearfix"></div>
										<div class="col-md-12">
											<div class="form-group col-md-6">
												<label>Select Table <span class="text-red">*</span> </label>
												<select disabled required="required" onchange="getDbTableFieldsMain(this.value, '<?php echo $field_count ?>', '1', $(this))" class="form-control module_name_row" name="module_name_<?php echo $field_count ?>" data-validation="required" data-validation-error-msg="Please table">
													<option value="">Select Table</option>
													<?php foreach ($listAllTables as $table_name) {
														if(!in_array($table_name['module_name'], $ignoreTable) ){
															echo '<option value="'.$table_name['id'].'">'.$table_name['module_name'].'</option>';
														}
													} ?>
													
												</select>
											</div>
											<div class="form-group col-md-6">
												<label>Select Column <span class="text-red">*</span></label>
												<select disabled required="required" class="form-control module_column" name="module_column_<?php echo $field_count ?>" data-validation="required" data-validation-error-msg="Please module column">
													<option value="">Select Table first</option>
												</select>
											</div>
											<div class="form-group col-md-6">
												<label>Field Type <span class="text-red">*</span></label>
												<select disabled required="required" class="form-control module_field_type" name="module_field_type_<?php echo $field_count ?>"  data-validation="required" data-validation-error-msg="Please select field type">
													<option value="drow_dwn_single">Dropdown single selection</option>
													<option value="drow_dwn_multi">Dropdown multiple selection</option>
												</select>
											</div>
											<div class="form-group col-md-6">
												<label>Child <span class="text-red">*</span></label>
												<select disabled onchange="getModuleChild(this.value, '<?php echo $field_count ?>', '1', $(this))" required="required" class="form-control module_child_yew_no" name="module_child_<?php echo $field_count ?>">
													<option>No</option>
													<option>Yes</option>
												</select>
											</div>
											<div class="clearfix"></div>
											<div class="module_child_more"></div>
																					
										</div>
									</div>
								</div>
								<script type="text/javascript">
									disabledAllChild('chid_row_<?php echo $field_count ?>');
								</script>							
								<?php

								
								$field_count++;
							}
							?> 
							<script type="text/javascript">
								$('#field_count').val('<?php echo $field_count; ?>');
							</script>
						</div>
					</div>
				</div>
				<?php
			}
			else{
				echo '<div class="form-group col-md-12" align="center"><span class="label bg-red">No child available</span></div>';
			}

		}
		
	}	

	public function add_attr($postData = '', $posted_field = '')
	{
		if($posted_field == ''){
			$field_count = $this->input->post('input_row_id');
		}
		else{
			$field_count = $posted_field;
		}
		?>		
		<span class="atr_child">
			<div class="form-group col-md-5">
				<label>Attribute Name <span class="text-red">*</span> </label>
				<input placeholder="Enter name" class="form-control" type="text" name="attr_name_<?php echo $field_count ?>[]" required="required" value="<?php if(!empty($postData)){if(isset($postData['attribute_value'])){echo $postData['attribute_name']; }} ?>">
			</div>
			<div class="form-group col-md-5">
				<label>Attribute Value <span class="text-red">*</span></label>
				<input placeholder="Enter value" class="form-control" type="text" name="attr_value_<?php echo $field_count ?>[]" required="required" value="<?php if(!empty($postData)){if(isset($postData['attribute_value'])){echo $postData['attribute_value']; }} ?>">
			</div>
			<div class="form-group col-md-2">
				<label>&nbsp;</label>
				<button type="button" class="btn btn-danger form-control" onclick="remFieldAtr('<?php echo $field_count ?>', $(this))"><i class="fa fa-minus"></i> </button>
			</div>
		</span>
		<?php
	}


	public function select_box_op($postData = '', $posted_field = '')
	{

		//print_r($postData);
		//echo $postData['field_select_box_type'];
		if($posted_field == ''){
			$field_count = $this->input->post('field_count');
		}
		else{
			$field_count = $posted_field;
		}
		
		?>

		<div class="col-md-12">
			<hr style="border-color: #c9c3c3" />
		</div>

		<div class="col-md-12">
			<input class="option_count" type="hidden" value="0">
			<div class="form-group col-md-12">
				<label>&nbsp;</label>
				Options<input <?php if(!empty($postData)){if($postData['field_select_box_type']=='Options'){echo 'checked';}} ?> type="radio" onclick="selectBoxOptions(this.value ,'<?php echo $field_count ?>', $(this))" name="select_box_type_<?php echo $field_count ?>" checked value="Options">

				Database
				<input <?php if(!empty($postData)){if($postData['field_select_box_type']=='Database'){echo 'checked';}} ?> type="radio" onclick="selectBoxOptions(this.value ,'<?php echo $field_count ?>', $(this))" name="select_box_type_<?php echo $field_count ?>" value="Database">
			</div>
			<div class="col-md-12">
				<div class="clearfix"></div>
			</div>
			<div class="clearfix"></div>
			<div class="select_db_info">
				<?php 

				if(!empty($postData)){
					if($postData['field_select_box_type'] == 'Options'){
						$this->select_box_op_only($postData, $field_count);
					}
					else if($postData['field_select_box_type'] == 'Database'){
						$this->select_box_db($postData, $field_count);
					}
				}else{ ?>
				<span class="dropdown_option">
					<div class="form-group col-md-5">
						<label>Option Name <span class="text-red">*</span> </label>
						<input placeholder="Enter name" class="form-control" type="text" name="option_name_<?php echo $field_count ?>[]" required="required">
					</div>
					<div class="form-group col-md-5">
						<label>Option Value</label>
						<input placeholder="Enter value" class="form-control" type="text" name="option_value_<?php echo $field_count ?>[]" required="required">
					</div>
					<div class="form-group col-md-2">
						<label>&nbsp;</label>
						<button type="button" class="btn btn-primary form-control" onclick="addOp('<?php echo $field_count ?>', $(this))"><i class="fa fa-plus"></i> </button>
					</div>
				</span>
				<?php } ?>
			</div>
		</div>


		<?php
	}

	public function select_box_db($postData = '', $posted_field = '')
	{

		if($posted_field == ''){
			$field_count = $this->input->post('field_count');
		}
		else{
			$dropdown_data = $this->tfn->getData('*', 'dropdown_data', "status = 1 AND module_id = '".$postData['module_id']."' AND field_id = '".$postData['id']."' ");
			$field_count = $posted_field;
			//print_r($dropdown_data);
		}

		//$field_count = $this->input->post('field_count');
		//$listAllTables = $this->tfn->listAllTables();

		$listAllTables = $this->tfn->getData('id,module_name', 'modules', "status = 1 ");


		$ignoreTable = array('acl','acl_actions','acl_categories','auth_sessions','ci_sessions','clients','denied_access','edit_details','field_attributes','ips_on_hold','login_errors','module_fields','modules','password_history','username_or_email_on_hold','users');
		?>
		
			
			<div class="col-md-12">
				<table class="designTable">
					<tr>
						<td>
							<div class="form-group">
								<label>Select Table <span class="text-red">*</span> </label>
								<select required="required" onchange="getDbTableFields(this.value, '<?php echo $field_count ?>', $(this))" class="form-control select_box_table" name="select_box_table_<?php echo $field_count ?>">
									<option value="">Select Table</option>
									<?php foreach ($listAllTables as $table_name) {
										if(!in_array($table_name['module_name'], $ignoreTable) ){

											if(!empty($postData)){
												if($dropdown_data !='No data'){
													if($dropdown_data[0]['field_select_box_table'] == $table_name['id']){
														$sel = 'selected';
													}
													else{
														$sel = '';
													}
												}
												else{
													$sel = '';
												}
											}else{
												$sel = '';
											}
											echo '<option '.$sel.' value="'.$table_name['id'].'">'.$table_name['module_name'].'</option>';
										}
									} ?>
								</select>
							</div>
						</td>
						<td>
							<div class="form-group">
								<label>Select Column <span class="text-red">*</span></label>
								<div class="clearfix"></div>
								<select required="required" class="form-control select_box_table_column" name="select_box_table_column_<?php echo $field_count ?>">
									<?php if(!empty($postData)){
										if($dropdown_data !='No data'){
											$this->get_columns_from_table($dropdown_data[0]['field_select_box_table'], $dropdown_data[0]['field_select_box_table_column']);
										}
										else{
											echo '<option value="">Select Table first</option>';
										}
									}
									else{
										echo '<option value="">Select Table first</option>';
									} ?>
									
									
								</select>
							</div>
						</td>
					</tr>
				</table>
			</div>
		<?php
	}

	public function select_box_op_only($postData = '', $posted_field = '')
	{

		if($posted_field == ''){
			$field_count = $this->input->post('field_count');
		}
		else{
			$dropdown_data = $this->tfn->getData('*', 'dropdown_data', "status = 1 AND module_id = '".$postData['module_id']."' AND field_id = '".$postData['id']."' ");
			$field_count = $posted_field;
			//print_r($dropdown_data);
		}

		//$field_count = $this->input->post('field_count');

		if(!empty($postData)){
			if($dropdown_data !='No data'){
				foreach ($dropdown_data as $d_key => $d_info) {
					if($d_key == 0){ ?>
						<span class="dropdown_option">
							<div class="form-group col-md-5">
								<label>Option Name <span class="text-red">*</span> </label>
								<input placeholder="Enter name" class="form-control" type="text" name="option_name_<?php echo $field_count ?>[]" required="required" value="<?php echo $d_info['op_name'] ?>">
							</div>
							<div class="form-group col-md-5">
								<label>Option Value</label>
								<input placeholder="Enter value" class="form-control" type="text" name="option_value_<?php echo $field_count ?>[]" required="required" value="<?php echo $d_info['op_val'] ?>">
							</div>
							<div class="form-group col-md-2">
								<label>&nbsp;</label>
								<button type="button" class="btn btn-primary form-control" onclick="addOp('<?php echo $field_count ?>', $(this))"><i class="fa fa-plus"></i> </button>
							</div>
						</span>
						<?php
					}
					else{
						$this->select_box_op_only_count($d_info, $field_count);
					}
				}
			}
		}
		else{
		?>

		<span class="dropdown_option">
			<div class="form-group col-md-5">
				<label>Option Name <span class="text-red">*</span> </label>
				<input placeholder="Enter name" class="form-control" type="text" name="option_name_<?php echo $field_count ?>[]" required="required">
			</div>
			<div class="form-group col-md-5">
				<label>Option Value</label>
				<input placeholder="Enter value" class="form-control" type="text" name="option_value_<?php echo $field_count ?>[]" required="required">
			</div>
			<div class="form-group col-md-2">
				<label>&nbsp;</label>
				<button type="button" class="btn btn-primary form-control" onclick="addOp('<?php echo $field_count ?>', $(this))"><i class="fa fa-plus"></i> </button>
			</div>
		</span>

		<?php
		}
	}

	public function get_columns_from_table($t_name = '', $selected_col = '')
	{
		if($t_name == ''){
			$table_name = $this->input->post('table_name');
		}
		else{
			$table_name = $t_name;
		}

		$moduleInfo = $this->tfn->getData('id', 'modules', "status = 1 AND id = '$table_name' ");
		if($moduleInfo == 'No data'){

		}
		else{
			$fieldsInfo = $this->tfn->getData('id,field_name', 'module_fields', "status = 1 AND module_id = '".$moduleInfo[0]['id']."' ");
			if($fieldsInfo !='No data'){
				echo '<option value="">Select Column</option>';
				foreach ($fieldsInfo as $value) {
					if($selected_col !=''){
						if($selected_col == $value['id']){
							$sel = 'selected';
						}
						else{
							$sel = '';
						}
					}
					else{
						$sel = '';
					}
					echo '<option '.$sel.' value="'.$value['id'].'">'.$value['field_name'].'</option>';
				}
			}
		}
		

	}

	public function select_box_op_only_count($postData = '', $posted_field = '')
	{
		if($posted_field == ''){
			$field_count = $this->input->post('field_count');
		}
		else{			
			$field_count = $posted_field;
			//print_r($dropdown_data);
		}

		//$field_count = $this->input->post('field_count');

		?>

		<span class="dropdown_option">
					<div class="form-group col-md-5">
						<label>Option Name <span class="text-red">*</span> </label>
						<input placeholder="Enter name" class="form-control" type="text" name="option_name_<?php echo $field_count ?>[]" required="required" value="<?php if(!empty($postData)){if($postData !='No data'){echo $postData['op_name'];}} ?>">
					</div>
					<div class="form-group col-md-5">
						<label>Option Value</label>
						<input placeholder="Enter value" class="form-control" type="text" name="option_value_<?php echo $field_count ?>[]" required="required" value="<?php if(!empty($postData)){if($postData !='No data'){echo $postData['op_val'];}} ?>">
					</div>
					<div class="form-group col-md-2">
						<label>&nbsp;</label>
						<button type="button" class="btn btn-danger form-control" onclick="removeOp('<?php echo $field_count ?>', $(this))"><i class="fa fa-minus"></i> </button>
					</div>
				</span>

		<?php
	}

	public function select_box_op_db($postData = '', $posted_field = '')
	{

		if($posted_field == ''){
			$field_count = $this->input->post('field_count');
		}
		else{
			$dropdown_data = $this->tfn->getData('*', 'dropdown_data', "status = 1 AND module_id = '".$postData['module_id']."' AND field_id = '".$postData['id']."' ");
			$field_count = $posted_field;
		}

		//$field_count = $this->input->post('field_count');

		$listAllTables = $this->tfn->getData('id,module_name', 'modules', "status = 1 ");

		$ignoreTable = array('acl','acl_actions','acl_categories','auth_sessions','ci_sessions','clients','denied_access','edit_details','field_attributes','ips_on_hold','login_errors','module_fields','modules','password_history','username_or_email_on_hold','users');
		?>
		<div class="col-md-12">
			<hr style="border-color: #c9c3c3" />
		</div>

			<div class="col-md-12">
				<div class="col-md-12">
					<table class="designTable">
						<tr>
							<td>
								<div class="form-group">
									<label>Select Table <span class="text-red">*</span> </label>
									<select required="required" onchange="getDbTableFieldsMain(this.value, '<?php echo $field_count ?>', '1', $(this))" class="form-control module_name_row" name="module_name_<?php echo $field_count ?>" data-validation="required" data-validation-error-msg="Please table">
										<option value="">Select Table</option>
										<?php foreach ($listAllTables as $table_name) {
											if(!in_array($table_name['module_name'], $ignoreTable) ){

												if(!empty($postData)){
												if($dropdown_data !='No data'){
													if($dropdown_data[0]['field_select_box_table'] == $table_name['id']){
														$sel = 'selected';
													}
													else{
														$sel = '';
													}
												}
												else{
													$sel = '';
												}
											}else{
												$sel = '';
											}


												echo '<option '.$sel.' value="'.$table_name['id'].'">'.$table_name['module_name'].'</option>';
											}
										} ?>
										
									</select>
								</div>
							</td>
							<td>
								<div class="form-group">
									<label>Select Column <span class="text-red">*</span></label>
									<select required="required" class="form-control module_column" name="module_column_<?php echo $field_count ?>" data-validation="required" data-validation-error-msg="Please module column">
										<?php if(!empty($postData)){
										if($dropdown_data !='No data'){
											$this->get_columns_from_table($dropdown_data[0]['field_select_box_table'], $dropdown_data[0]['field_select_box_table_column']);
										}
										else{
											echo '<option value="">Select Table first</option>';
										}
									}
									else{
										echo '<option value="">Select Table first</option>';
									} ?>
									</select>
								</div>
							</td>
						</tr>
						<tr>
							<td>
								<div class="form-group">
									<label>Field Type <span class="text-red">*</span></label>
									<select required="required" class="form-control" name="module_field_type_<?php echo $field_count ?>" data-validation="required" data-validation-error-msg="Please select field type">
										<option <?php if(!empty($postData)){if($dropdown_data !='No data'){if($dropdown_data[0]['display_in_form'] == 'drow_dwn_single'){echo 'selected';}}} ?> value="drow_dwn_single">Dropdown single selection</option>
										<option <?php if(!empty($postData)){if($dropdown_data !='No data'){if($dropdown_data[0]['display_in_form'] == 'drow_dwn_multi'){echo 'selected';}}} ?> value="drow_dwn_multi">Dropdown multiple selection</option>
									</select>
								</div>
							</td>
							<td>
								<div class="form-group">

									<label>Child <span class="text-red">*</span></label>
									<select onchange="getModuleChild(this.value, '<?php echo $field_count ?>', '1', $(this))" required="required" class="form-control module_child_yew_no" name="module_child_<?php echo $field_count ?>">
										<option <?php if(!empty($postData)){if($dropdown_data !='No data'){if($dropdown_data[0]['db_module_have_child'] == 'No'){echo 'selected';}}} ?> >No</option>
										<option <?php if(!empty($postData)){if($dropdown_data !='No data'){if($dropdown_data[0]['db_module_have_child'] == 'Yes'){echo 'selected';}}} ?> >Yes</option>
									</select>
								</div>
							</td>
						</tr>
					</table>
				</div>
				<div class="clearfix"></div>
				<div class="module_child_more">
					<?php if(!empty($postData)){if($dropdown_data !='No data'){
						if($dropdown_data[0]['db_module_have_child'] == 'Yes'){
						$this->get_child_modules($postData, $field_count, $dropdown_data[0]['field_select_box_table'], $parent_name);

					}}} ?>
				</div>
			</div>

		<?php
	}

	public function select_box_check_db($postData = '', $posted_field = '')
	{

		$parent_name = '';

		if($posted_field == ''){
			$field_count = $this->input->post('field_count');
		}
		else{
			$dropdown_data = $this->tfn->getData('*', 'dropdown_data', "status = 1 AND module_id = '".$postData['module_id']."' AND field_id = '".$postData['id']."' ");
			$field_count = $posted_field;
		}

		//$field_count = $this->input->post('field_count');

		$listAllTables = $this->tfn->getData('id,module_name', 'modules', "status = 1 ");
		$ignoreTable = array('acl','acl_actions','acl_categories','auth_sessions','ci_sessions','clients','denied_access','edit_details','field_attributes','ips_on_hold','login_errors','module_fields','modules','password_history','username_or_email_on_hold','users');
		?>
		<div class="col-md-12">
			<hr style="border-color: #c9c3c3" />
		</div>

			<div class="col-md-12">
				<div class="col-md-12">
					<table class="designTable">
						<tr>
							<td>
								<div class="form-group">
									<label>Select Table <span class="text-red">*</span> </label>
									<select required="required" onchange="getDbTableFieldsMain(this.value, '<?php echo $field_count ?>', '1', $(this))" class="form-control module_name_row" name="module_name_<?php echo $field_count ?>" data-validation="required" data-validation-error-msg="Please table">
										<option value="">Select Table</option>
										<?php foreach ($listAllTables as $table_name) {
											if(!in_array($table_name['module_name'], $ignoreTable) ){

												if(!empty($postData)){
												if($dropdown_data !='No data'){
													if($dropdown_data[0]['field_select_box_table'] == $table_name['id']){
														$sel = 'selected';
														$parent_name = $table_name['module_name'];
													}
													else{
														$sel = '';
														
													}
												}
												else{
													$sel = '';
													
												}
											}else{
												$sel = '';
												
											}


												echo '<option '.$sel.' value="'.$table_name['id'].'">'.$table_name['module_name'].'</option>';
											}
										} ?>
										
									</select>
								</div>
							</td>
							<td>
								<div class="form-group">
									<label>Select Column <span class="text-red">*</span></label>
									<select required="required" class="form-control module_column" name="module_column_<?php echo $field_count ?>" data-validation="required" data-validation-error-msg="Please module column">
										<?php if(!empty($postData)){
										if($dropdown_data !='No data'){
											$this->get_columns_from_table($dropdown_data[0]['field_select_box_table'], $dropdown_data[0]['field_select_box_table_column']);
										}
										else{
											echo '<option value="">Select Table first</option>';
										}
									}
									else{
										echo '<option value="">Select Table first</option>';
									} ?>
									</select>
								</div>
							</td>
						</tr>
						<tr>
							<td>
								<div class="form-group">
									<label>Field Type <span class="text-red">*</span></label>
									<select required="required" class="form-control" name="module_field_type_<?php echo $field_count ?>" data-validation="required" data-validation-error-msg="Please select field type">
										<option value="Checkbox">Checkbox</option>
									</select>
								</div>
							</td>
							<td>
								<div class="form-group">
									<label>Child <span class="text-red">*</span></label>
									<select onchange="getModuleChild(this.value, '<?php echo $field_count ?>', '1', $(this))" required="required" class="form-control module_child_yew_no" name="module_child_<?php echo $field_count ?>">
										<option <?php if(!empty($postData)){if($dropdown_data !='No data'){if($dropdown_data[0]['db_module_have_child'] == 'No'){echo 'selected';}}} ?> >No</option>
										<option <?php if(!empty($postData)){if($dropdown_data !='No data'){if($dropdown_data[0]['db_module_have_child'] == 'Yes'){echo 'selected';}}} ?> >Yes</option>
									</select>
								</div>
							</td>
						</tr>
					</table>
				</div>
				<div class="clearfix"></div>
				<div class="module_child_more">
					<?php if(!empty($postData)){if($dropdown_data !='No data'){
						if($dropdown_data[0]['db_module_have_child'] == 'Yes'){
						$this->get_child_modules($postData, $field_count, $dropdown_data[0]['field_select_box_table'], $parent_name);

					}}} ?>
				</div>
			</div>

		<?php
	}

	public function select_box_radio_db($postData = '', $posted_field = '')
	{

		if($posted_field == ''){
			$field_count = $this->input->post('field_count');
		}
		else{
			$dropdown_data = $this->tfn->getData('*', 'dropdown_data', "status = 1 AND module_id = '".$postData['module_id']."' AND field_id = '".$postData['id']."' ");
			$field_count = $posted_field;
		}

		//$field_count = $this->input->post('field_count');

		$listAllTables = $this->tfn->getData('id,module_name', 'modules', "status = 1 ");
		$ignoreTable = array('acl','acl_actions','acl_categories','auth_sessions','ci_sessions','clients','denied_access','edit_details','field_attributes','ips_on_hold','login_errors','module_fields','modules','password_history','username_or_email_on_hold','users');
		?>

			<div class="col-md-12">
				<hr style="border-color: #c9c3c3" />
			</div>

			<div class="col-md-12">
				<div class="col-md-12">
					<table class="designTable">
						<tr>
							<td>
								<div class="form-group">
									<label>Select Table <span class="text-red">*</span> </label>
									<select required="required" onchange="getDbTableFieldsMain(this.value, '<?php echo $field_count ?>', '1', $(this))" class="form-control module_name_row" name="module_name_<?php echo $field_count ?>" data-validation="required" data-validation-error-msg="Please table">
										<option value="">Select Table</option>
										<?php foreach ($listAllTables as $table_name) {
											if(!in_array($table_name['module_name'], $ignoreTable) ){

												if(!empty($postData)){
												if($dropdown_data !='No data'){
													if($dropdown_data[0]['field_select_box_table'] == $table_name['id']){
														$sel = 'selected';
													}
													else{
														$sel = '';
													}
												}
												else{
													$sel = '';
												}
											}else{
												$sel = '';
											}


												echo '<option '.$sel.' value="'.$table_name['id'].'">'.$table_name['module_name'].'</option>';
												
											}
										} ?>
										
									</select>
								</div>
							</td>
							<td>
								<div class="form-group">
									<label>Select Column <span class="text-red">*</span></label>
									<select required="required" class="form-control module_column" name="module_column_<?php echo $field_count ?>" data-validation="required" data-validation-error-msg="Please module column">
										<?php if(!empty($postData)){
										if($dropdown_data !='No data'){
											$this->get_columns_from_table($dropdown_data[0]['field_select_box_table'], $dropdown_data[0]['field_select_box_table_column']);
										}
										else{
											echo '<option value="">Select Table first</option>';
										}
									}
									else{
										echo '<option value="">Select Table first</option>';
									} ?>
									</select>
								</div>
							</td>
						</tr>
						<tr>
							<td>
								<div class="form-group">
									<label>Field Type <span class="text-red">*</span></label>
									<select required="required" class="form-control" name="module_field_type_<?php echo $field_count ?>" data-validation="required" data-validation-error-msg="Please select field type">
										<option value="Radio">Radio</option>
									</select>
								</div>
							</td>
							<td>
								<div class="form-group">
									<label>Child <span class="text-red">*</span></label>
									<select onchange="getModuleChild(this.value, '<?php echo $field_count ?>', '1', $(this))" required="required" class="form-control module_child_yew_no" name="module_child_<?php echo $field_count ?>">
										<option <?php if(!empty($postData)){if($dropdown_data !='No data'){if($dropdown_data[0]['db_module_have_child'] == 'No'){echo 'selected';}}} ?> >No</option>
										<option <?php if(!empty($postData)){if($dropdown_data !='No data'){if($dropdown_data[0]['db_module_have_child'] == 'Yes'){echo 'selected';}}} ?> >Yes</option>
									</select>
								</div>
							</td>
						</tr>
					</table>
				</div>
				<div class="clearfix"></div>
				<div class="module_child_more">
					<?php if(!empty($postData)){if($dropdown_data !='No data'){
						if($dropdown_data[0]['db_module_have_child'] == 'Yes'){
						$this->get_child_modules($postData, $field_count, $dropdown_data[0]['field_select_box_table'], $parent_name);

					}}} ?>
				</div>
			</div>

		<?php
	}


	public function select_box_op_db_child($postData = '', $posted_field = '', $p_module_id = '')
	{

		if(!empty($postData)){
			$field_count = $posted_field;
			$mI = $this->tfn->getData('id,module_name', 'modules', "status = 1 AND module_name ='$p_module_id' ");			
			$module_id = $mI[0]['id'];

			$dropdown_data = $this->tfn->getData('*', 'dropdown_data', "status = 1 AND module_id = '".$postData['module_id']."' AND field_id = '".$postData['id']."' ");
		}
		else{
			$field_count = $this->input->post('field_count');
			$module_id = $this->input->post('parent_id');
		}


		$listAllTables = $this->tfn->getData('id,module_name', 'modules', "status = 1 AND id ='$module_id' ");

		$ignoreTable = array('acl','acl_actions','acl_categories','auth_sessions','ci_sessions','clients','denied_access','edit_details','field_attributes','ips_on_hold','login_errors','module_fields','modules','password_history','username_or_email_on_hold','users');



		?>
		<div class="col-md-12">
			<hr style="border-color: #c9c3c3" />
		</div>

			<div class="col-md-12">
				<div class="col-md-12">
					<table class="designTable">
						<tr>
							<td>
								<div class="form-group">
									<label>Select Table <span class="text-red">*</span> </label>
									<select required="required" onchange="getDbTableFieldsMain(this.value, '<?php echo $field_count ?>', '1', $(this))" class="form-control module_name_row" name="module_name_<?php echo $field_count ?>" data-validation="required" data-validation-error-msg="Please table">
										<option value="">Select Table</option>
										<?php foreach ($listAllTables as $table_name) {
											if(!in_array($table_name['module_name'], $ignoreTable) ){

												if(!empty($postData)){
													if($module_id == $table_name['id'] ){
														$sel = 'selected';
													}
													else{
														$sel = '';
													}
												}
												else{
													$sel = '';
												}

												echo '<option '.$sel.' value="'.$table_name['id'].'">'.$table_name['module_name'].'</option>';
											}
										} ?>
										
									</select>
								</div>
							</td>
							<td>
								<div class="form-group">
									<label>Select Column <span class="text-red">*</span></label>
									<select required="required" class="form-control module_column" name="module_column_<?php echo $field_count ?>" data-validation="required" data-validation-error-msg="Please module column">
										<?php if(!empty($postData)){
										if($dropdown_data !='No data'){
											$this->get_columns_from_table($dropdown_data[0]['field_select_box_table'], $dropdown_data[0]['field_select_box_table_column']);
										}
										else{
											echo '<option value="">Select Table first</option>';
										}
									}
									else{
										echo '<option value="">Select Table first</option>';
									} ?>
									</select>
								</div>
							</td>
						</tr>
						<tr>
							<td>
								<div class="form-group">
									<label>Field Type <span class="text-red">*</span></label>
									<select required="required" class="form-control" name="module_field_type_<?php echo $field_count ?>" data-validation="required" data-validation-error-msg="Please select field type">
										<option value="drow_dwn_single">Dropdown single selection</option>
										<option value="drow_dwn_multi">Dropdown multiple selection</option>
									</select>
								</div>
							</td>
							<td>
								<div class="form-group">
									<label>Child <span class="text-red">*</span></label>
									<select onchange="getModuleChild(this.value, '<?php echo $field_count ?>', '1', $(this))" required="required" class="form-control module_child_yew_no" name="module_child_<?php echo $field_count ?>">
										<option>No</option>
										<option>Yes</option>
									</select>
								</div>
							</td>
						</tr>
					</table>
				</div>
				<div class="clearfix"></div>
				<div class="module_child_more">
					<?php if(!empty($postData)){if($dropdown_data !='No data'){
						if($dropdown_data[0]['db_module_have_child'] == 'Yes'){
						$this->get_child_modules($postData, $field_count, $dropdown_data[0]['field_select_box_table'], $parent_name);
					}}} ?>
				</div>
			</div>

		<?php
	}

	public function select_box_check_db_child($postData = '', $posted_field = '', $p_module_id = '')
	{


		if(!empty($postData)){

			$field_count = $posted_field;
			$mI = $this->tfn->getData('id,module_name', 'modules', "status = 1 AND module_name ='$p_module_id' ");			
			$module_id = $mI[0]['id'];

			$dropdown_data = $this->tfn->getData('*', 'dropdown_data', "status = 1 AND module_id = '".$postData['module_id']."' AND field_id = '".$postData['id']."' ");

			//print_r($dropdown_data);
		}
		else{
			$field_count = $this->input->post('field_count');
			$module_id = $this->input->post('parent_id');
		}

		/*$field_count = $this->input->post('field_count');
		$module_id = $this->input->post('parent_id');*/
		

		$listAllTables = $this->tfn->getData('id,module_name', 'modules', "status = 1 AND id ='$module_id' ");



		$ignoreTable = array('acl','acl_actions','acl_categories','auth_sessions','ci_sessions','clients','denied_access','edit_details','field_attributes','ips_on_hold','login_errors','module_fields','modules','password_history','username_or_email_on_hold','users');
		?>
		<div class="col-md-12">
			<hr style="border-color: #c9c3c3" />
		</div>

			<div class="col-md-12">
				<div class="col-md-12">
					<table class="designTable">
						<tr>
							<td>
								<div class="form-group">
									<label>Select Table <span class="text-red">*</span> </label>
									<select required="required" onchange="getDbTableFieldsMain(this.value, '<?php echo $field_count ?>', '1', $(this))" class="form-control module_name_row" name="module_name_<?php echo $field_count ?>" data-validation="required" data-validation-error-msg="Please table">
										<option value="">Select Table</option>
										<?php foreach ($listAllTables as $table_name) {
											if(!in_array($table_name['module_name'], $ignoreTable) ){


												if(!empty($postData)){
													if($module_id == $table_name['id'] ){
														$sel = 'selected';
													}
													else{
														$sel = '';
													}
												}
												else{
													$sel = '';
												}


												echo '<option '.$sel.' value="'.$table_name['id'].'">'.$table_name['module_name'].'</option>';
											}
										} ?>
										
									</select>
								</div>
							</td>
							<td>
								<div class="form-group">
									<label>Select Column <span class="text-red">*</span></label>
									<select required="required" class="form-control module_column" name="module_column_<?php echo $field_count ?>" data-validation="required" data-validation-error-msg="Please module column">
										<?php if(!empty($postData)){
										if($dropdown_data !='No data'){
											$this->get_columns_from_table($dropdown_data[0]['field_select_box_table'], $dropdown_data[0]['field_select_box_table_column']);
										}
										else{
											echo '<option value="">Select Table first</option>';
										}
									}
									else{
										echo '<option value="">Select Table first</option>';
									} ?>
									</select>
								</div>
							</td>
						</tr>
						<tr>
							<td>
								<div class="form-group">
									<label>Field Type <span class="text-red">*</span></label>
									<select required="required" class="form-control" name="module_field_type_<?php echo $field_count ?>" data-validation="required" data-validation-error-msg="Please select field type">
										<option value="Checkbox">Checkbox</option>
									</select>
								</div>
							</td>
							<td>
								<div class="form-group">
									<label>Child <span class="text-red">*</span></label>
									<select onchange="getModuleChild(this.value, '<?php echo $field_count ?>', '1', $(this))" required="required" class="form-control module_child_yew_no" name="module_child_<?php echo $field_count ?>">
										<option <?php if(!empty($postData)){if($dropdown_data !='No data'){if($dropdown_data[0]['db_module_have_child'] == 'No'){echo 'selected';}}} ?> >No</option>
										<option <?php if(!empty($postData)){if($dropdown_data !='No data'){if($dropdown_data[0]['db_module_have_child'] == 'Yes'){echo 'selected';}}} ?> >Yes</option>
									</select>
								</div>
							</td>
						</tr>
					</table>
				</div>
				<div class="clearfix"></div>
				<div class="module_child_more">
					<?php if(!empty($postData)){if($dropdown_data !='No data'){
						if($dropdown_data[0]['db_module_have_child'] == 'Yes'){
						$this->get_child_modules($postData, $field_count, $dropdown_data[0]['field_select_box_table'], $parent_name);

					}}} ?>
				</div>
			</div>

		<?php
	}

	public function select_box_radio_db_child($postData = '', $posted_field = '', $p_module_id = '')
	{

		if(!empty($postData)){
			$field_count = $posted_field;
			$mI = $this->tfn->getData('id,module_name', 'modules', "status = 1 AND module_name ='$p_module_id' ");			
			$module_id = $mI[0]['id'];

			$dropdown_data = $this->tfn->getData('*', 'dropdown_data', "status = 1 AND module_id = '".$postData['module_id']."' AND field_id = '".$postData['id']."' ");

		}
		else{
			$field_count = $this->input->post('field_count');
			$module_id = $this->input->post('parent_id');
		}
		

		$listAllTables = $this->tfn->getData('id,module_name', 'modules', "status = 1 AND id ='$module_id' ");


		$ignoreTable = array('acl','acl_actions','acl_categories','auth_sessions','ci_sessions','clients','denied_access','edit_details','field_attributes','ips_on_hold','login_errors','module_fields','modules','password_history','username_or_email_on_hold','users');
		?>

			<div class="col-md-12">
				<hr style="border-color: #c9c3c3" />
			</div>

			<div class="col-md-12">
				<div class="col-md-12">
					<table class="designTable">
						<tr>
							<td>
								<div class="form-group">
									<label>Select Table <span class="text-red">*</span> </label>
									<select required="required" onchange="getDbTableFieldsMain(this.value, '<?php echo $field_count ?>', '1', $(this))" class="form-control module_name_row" name="module_name_<?php echo $field_count ?>" data-validation="required" data-validation-error-msg="Please table">
										<option value="">Select Table</option>
										<?php foreach ($listAllTables as $table_name) {
											if(!in_array($table_name['module_name'], $ignoreTable) ){

												if(!empty($postData)){
													if($module_id == $table_name['id'] ){
														$sel = 'selected';
													}
													else{
														$sel = '';
													}
												}
												else{
													$sel = '';
												}


												echo '<option '.$sel.' value="'.$table_name['id'].'">'.$table_name['module_name'].'</option>';
											}
										} ?>
										
									</select>
								</div>
							</td>
							<td>
								<div class="form-group">
									<label>Select Column <span class="text-red">*</span></label>
									<select required="required" class="form-control module_column" name="module_column_<?php echo $field_count ?>" data-validation="required" data-validation-error-msg="Please module column">
										<?php if(!empty($postData)){
										if($dropdown_data !='No data'){
											$this->get_columns_from_table($dropdown_data[0]['field_select_box_table'], $dropdown_data[0]['field_select_box_table_column']);
										}
										else{
											echo '<option value="">Select Table first</option>';
										}
									}
									else{
										echo '<option value="">Select Table first</option>';
									} ?>
									</select>
								</div>
							</td>
						</tr>
						<tr>
							<td>
								<div class="form-group">
									<label>Field Type <span class="text-red">*</span></label>
									<select required="required" class="form-control" name="module_field_type_<?php echo $field_count ?>" data-validation="required" data-validation-error-msg="Please select field type">
										<option value="Radio">Radio</option>
									</select>
								</div>
							</td>
							<td>
								<div class="form-group">
									<label>Child <span class="text-red">*</span></label>
									<select onchange="getModuleChild(this.value, '<?php echo $field_count ?>', '1', $(this))" required="required" class="form-control module_child_yew_no" name="module_child_<?php echo $field_count ?>">
										<option>No</option>
										<option>Yes</option>
									</select>
								</div>
							</td>
						</tr>
					</table>
				</div>
				<div class="clearfix"></div>
				<div class="module_child_more">
					<?php if(!empty($postData)){if($dropdown_data !='No data'){
						if($dropdown_data[0]['db_module_have_child'] == 'Yes'){
						$this->get_child_modules($postData, $field_count, $dropdown_data[0]['field_select_box_table'], $parent_name);

					}}} ?>
				</div>
			</div>

		<?php
	}


	
	

	
}

/* End of file Dashboard.php */
/* Location: /controllers/Dashboard.php */
