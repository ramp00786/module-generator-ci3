<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Userpermission extends MY_Controller {


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
		$data['security'] = $this->security; 
		$data['users'] = $this->tfn->getData('*', 'users', "auth_level <> 9 ");
		$data['modules'] = $this->tfn->getData('*', 'modules', "status = 1 ");
		$this->ap->constructPage('user_permission', $data, app_name.' Dashboard');
	}


	public function addorupdate(){
		$postData = $this->input->post();

		
		if($postData['user_id'] ==''){
			$alertData['type'] = 'danger'; //-- Alert type
			$alertData['icon'] = 'fa fa-exclamation-triangle'; //----Alert Icon
			$alertData['heading'] = 'Empty User!'; //----Alert Icon
			$alertData['msg'] = 'Please select user.!'; //----Alert Icon
			$this->session->set_flashdata('alert', $alertData);
			redirect('Userpermission', 'refresh');
		}
		

		if(isset($postData['modules'])){

			$this->tfn->deleteData('user_permissions', 'user_id', $postData['user_id']);


			foreach($postData['modules'] as $module_id){
				//--Check user and module already exist
				$checkUserPermission = $this->tfn->getData('*', 'user_permissions', "status = 1 AND user_id = '".$postData['user_id']."' AND module_id = '".$module_id."'  ");
				
				// ---Start Create data array----
				$createOrUpdate['user_id'] = $postData['user_id'];
				$createOrUpdate['module_id'] = $module_id;

				$createOrUpdate['show_items'] = 0;
				$createOrUpdate['create_items'] = 0;
				$createOrUpdate['update_items'] = 0;
				$createOrUpdate['delete_items'] = 0;

				if(isset($postData['show_items_'.$module_id]) && $postData['show_items_'.$module_id]){
					$createOrUpdate['show_items'] = 1;
				}

				if(isset($postData['create_items_'.$module_id]) && $postData['create_items_'.$module_id]){
					$createOrUpdate['create_items'] = 1;
				}

				if(isset($postData['update_items_'.$module_id]) && $postData['update_items_'.$module_id]){
					$createOrUpdate['update_items'] = 1;
				}

				if(isset($postData['delete_items_'.$module_id]) && $postData['delete_items_'.$module_id]){
					$createOrUpdate['delete_items'] = 1;
				}
				// ---End Create data array----

				if($checkUserPermission == 'No data' || $checkUserPermission == 'Table not exists'){
					$res = $this->tfn->insertData($createOrUpdate, 'user_permissions');
				}
				else{
					$res = $this->tfn->updateData($createOrUpdate, 'user_permissions', 'status', '2', 'id', $checkUserPermission[0]['id'], $this->auth_user_id);
				}
			}
		}

		$alertData['type'] = 'success'; //-- Alert type
		$alertData['icon'] = 'fa fa-check'; //----Alert Icon
		$alertData['heading'] = 'Added'; //----Alert Icon
		$alertData['msg'] = 'Records has been added successfully.!'; //----Alert Icon
		$this->session->set_flashdata('alert', $alertData);
		redirect('Userpermission', 'refresh');

		

		// $createOrUpdate['user_id'] = $postData['user_id'];

		// if(isset($postData['create']) && $postData['create']){
		// 	$createOrUpdate['create'] = 1;
		// }
		// else{
		// 	$createOrUpdate['create'] = 0;
		// }
		// if(isset($postData['update']) && $postData['update']){
		// 	$createOrUpdate['update'] = 1;
		// }
		// else{
		// 	$createOrUpdate['update'] = 0;
		// }
		// if(isset($postData['delete']) && $postData['delete']){
		// 	$createOrUpdate['delete'] = 1;
		// }
		// else{
		// 	$createOrUpdate['delete'] = 0;
		// }

		// if(isset($postData['modules']) && !empty($postData['modules'])){
		// 	$createOrUpdate['modules'] = implode(',', $postData['modules']);
		// }
		// else{
		// 	$createOrUpdate['modules'] = '';
		// }

		// $checkUserPermission = $this->tfn->getData('*', 'user_permissions', "status = 1 AND user_id = '".$postData['user_id']."' ");

		// if($checkUserPermission == 'No data' || $checkUserPermission == 'Table not exists'){
		// 	$res = $this->tfn->insertData($createOrUpdate, 'user_permissions');
		// 	if($res){
		// 		$alertData['type'] = 'success'; //-- Alert type
		// 		$alertData['icon'] = 'fa fa-check'; //----Alert Icon
		// 		$alertData['heading'] = 'Added'; //----Alert Icon
		// 		$alertData['msg'] = 'Records has been added successfully.!'; //----Alert Icon
		// 		$this->session->set_flashdata('alert', $alertData);
		// 		redirect('Userpermission', 'refresh');
		// 	}
		// 	else{
		// 		$alertData['type'] = 'danger'; //-- Alert type
		// 		$alertData['icon'] = 'fa fa-exclamation-triangle'; //----Alert Icon
		// 		$alertData['heading'] = 'Error!'; //----Alert Icon
		// 		$alertData['msg'] = 'Something went wrong.!'; //----Alert Icon
		// 		$this->session->set_flashdata('alert', $alertData);
		// 		redirect('Userpermission', 'refresh');
		// 	}
		// }
		// else{
		// 	$res = $this->tfn->updateData($createOrUpdate, 'user_permissions', 'status', '2', 'id', $checkUserPermission[0]['id'], $this->auth_user_id);
		// 	if($res){
		// 		$alertData['type'] = 'success'; //-- Alert type
		// 		$alertData['icon'] = 'fa fa-check'; //----Alert Icon
		// 		$alertData['heading'] = 'Added'; //----Alert Icon
		// 		$alertData['msg'] = 'Records has been updated successfully.!'; //----Alert Icon
		// 		$this->session->set_flashdata('alert', $alertData);
		// 		redirect('Userpermission', 'refresh');
		// 	}
		// 	else{
		// 		$alertData['type'] = 'danger'; //-- Alert type
		// 		$alertData['icon'] = 'fa fa-exclamation-triangle'; //----Alert Icon
		// 		$alertData['heading'] = 'Error!'; //----Alert Icon
		// 		$alertData['msg'] = 'Something went wrong.!'; //----Alert Icon
		// 		$this->session->set_flashdata('alert', $alertData);
		// 		redirect('Userpermission', 'refresh');
		// 	}
		// }

	}


	function users_module_data(){
		$modules = $this->tfn->getData('*', 'modules', "status = 1 ");
		$user_id = $this->input->get('user_id');
		?>

			<div class="checkbox">

			<?php 
				if($modules != 'No data' && $modules !='Table not exists'){
					foreach($modules as $module){

						$show_check = '';
						$create_check = '';
						$update_check = '';
						$delete_check = '';

						$getCheckedInfo = $this->tfn->getData('*', 'user_permissions', "status = 1 AND module_id = '".$module['id']."' ");

						if($getCheckedInfo !='No data' && $getCheckedInfo !='Table not exists'){
							$sel = 'checked';

							if($getCheckedInfo[0]['show_items'] == 1){
								$show_check = 'checked';
							}

							if($getCheckedInfo[0]['create_items'] == 1){
								$create_check = 'checked';
							}
							if($getCheckedInfo[0]['update_items'] == 1){
								$update_check = 'checked';
							}
							if($getCheckedInfo[0]['delete_items'] == 1){
								$delete_check = 'checked';
							}

							
						}
						else{
							$sel = '';
							
						}

						echo '<label style="color:red">';
						echo '<input '.$sel.' type="checkbox" name="modules[]" value="'.$module['id'].'"> <b>'.$module['module_name'].' ('.$module['slug'].')</b>';
						echo '</label>';

						echo '<hr style="border-top: 1px solid #f3efef7d" />';

			?>

			<div class="checkbox">
				<label >
					<input type="checkbox" name="show_items_<?php echo $module['id']; ?>" value="1"  <?php echo $show_check ?> ><b>Show</b>
				</label>
				&nbsp; | &nbsp;
				<label >
					<input type="checkbox" name="create_items_<?php echo $module['id']; ?>" value="1" <?php echo $create_check ?> ><b>Create</b>
				</label>
				&nbsp; | &nbsp;
				<label >
					<input type="checkbox" name="update_items_<?php echo $module['id']; ?>" value="1" <?php echo $update_check ?> ><b>Update</b>
				</label>
				&nbsp; | &nbsp;
				<label >
					<input type="checkbox" name="delete_items_<?php echo $module['id']; ?>" value="1" <?php echo $delete_check ?> ><b>Delete</b>
				</label>
			</div>


			<?php

			echo '<hr style="border-top: 1px solid #b5b5b5" />';
					}
				}
			?>


			</div>
		<?php
	}

}

/* End of file Userpersion.php */
/* Location: ./application/controllers/Userpersion.php */
