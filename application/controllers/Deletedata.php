<?php
// Restricted page directly access 
defined('BASEPATH') OR exit('No direct script access allowed');

class Deletedata extends MY_Controller {

	// create constructor
	public function __construct()
	{
		parent::__construct();
		
		// Load url helper
		$this->load->helper('url');

		/*-----Check Login------*/
		if($this->is_logged_in()){						
			if($this->auth_level != 1 && $this->auth_level != 9){
				$data['security'] = $this->security; 
				$this->ap->constructPage('no_permission', $data, 'VES | Dashboard');
			}
		}
		else{
			redirect('login','refresh');
		}
		/*-----./ End Check Login------*/

	}
	
	// Default function
	public function index(){
		// check users logged in or not
		if($this->is_logged_in()){	
			// Check logged in user is admin or not					
			if($this->auth_level == 1 || $this->auth_level == 9){
				// Get all input from form
				$requestData = $this->input->post(); 		
				if(!empty($requestData)){ 
					// Decrypt token for get data 
					$id_str=base64_decode($requestData['q']);
					// Export string from triple underscore
					$id=explode('___', $id_str);
					if(!empty($id[1])){
						// Get table name
						$table_name = $id[1];
						// where column name static 
						$where_col_name = 'id';
						// get where col value
						$where_col_value = $id[2];
						// get controller name for return back
						$controller = $id[4];
						// get message to be displayed after delete the data
						$msg = $id[5];

						// check table name is modules then delete child table data also
						if($table_name == 'modules'){
							$moduleInfo = $this->tfn->getData('*', 'modules', "id = '".$where_col_value."' ");
							$responce = $this->tfn->deleteDataP($table_name, $where_col_name, $where_col_value);
						}
						// Check if table name is user
						elseif($table_name == 'users'){
							$responce = $this->tfn->deleteDataP($table_name, 'user_id', $where_col_value);
						}
						// Delete data from database
						else{
							$responce = $this->tfn->deleteData($table_name, $where_col_name, $where_col_value);
						}
						// if table name module delete data from child tables
						if($table_name == 'modules'){													
							$this->tfn->deleteDataP('module_fields', 'module_id', $where_col_value);
							$this->tfn->deleteDataP('field_attributes', 'module_id', $where_col_value);
							$this->tfn->deleteDataP('dropdown_data', 'module_id', $where_col_value);
							//--Delete table
							if ($this->db->table_exists($moduleInfo[0]['module_name'].'_'.$moduleInfo[0]['menu_id']) ){
								$this->load->dbforge();
								$this->dbforge->drop_table($moduleInfo[0]['module_name'].'_'.$moduleInfo[0]['menu_id']);
							}
						}
						// If successfull deleted the data return back from here
						if($responce == 'Deleted'){
							$alertData['type'] = 'danger'; //-- Alert type
							$alertData['icon'] = 'fa fa-trash'; //----Alert Icon
							$alertData['heading'] = 'Deleted!'; //----Alert Icon
							$alertData['msg'] = 'Records has been deleted successfully'; //----Alert Icon
							$this->session->set_flashdata('alert', $alertData);
							redirect(base_url().$controller, 'refresh');
						}
						else{
							// Return back from here if any error found
							$alertData['type'] = 'danger'; //-- Alert type
							$alertData['icon'] = 'fa fa-exclamation-triangle'; //----Alert Icon
							$alertData['heading'] = 'Error!'; //----Alert Icon
							$alertData['msg'] = 'Somthing went wrong.'; //----Alert Icon
							$this->session->set_flashdata('alert', $alertData);
							redirect(base_url().$controller, 'refresh');
						}
					}
					else{
						// return back if input blank
						redirect(base_url(), 'refresh');
					}
				}
				else{
					// return back if input blank
					redirect(base_url().$controller, 'refresh');	
				}
			}
			else{
				// load no access page 
				$data['security'] = $this->security; 
				$this->ap->constructPage('no_permission', $data, app_name.'Dashboard');
			}
		}
		else{
			// return back if input blank
			redirect('login','refresh');
		}
	}
}
