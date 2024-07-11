<?php
defined('BASEPATH') OR exit('No direct script access allowed');



class Apphelper
{
	
	protected $CI;

	public function __construct()
	{
		//-------Get Ci instance--//
        $this->CI =& get_instance();
        //-------Get Ci instance--//

        //-----Filemanager--------
		$_SESSION['upload_image_file_manager']=TRUE;
		if(@$_SESSION['upload_image_file_manager'] == TRUE){
			$codeigniterAuth = true;
		} else {
			$codeigniterAuth = false;
		}
		//-----Filemanager--------

               

	}
	public function constructPage($fullpageName, $pageData='', $title='WA-BU', $favicon='favicon.png', $favicon_type='shortcut icon'){

		
		$pageData['modulePermission'] = $this->CI->tfn->getData('*', 'user_permissions', "status = 1 AND user_id = '".$this->CI->auth_user_id."' " );

		
	

		//--------Load Header, footer and slider -------//
		$pageData['left_menu'] = $this->CI->load->view('inc/leftmenu', $pageData, TRUE);
		$pageData['header'] = $this->CI->load->view('inc/header', $pageData, TRUE);
		//$pageData['a_setting'] = $this->CI->load->view('superadmin/inc/setting', $a_Info, TRUE);
		$pageData['footer'] = $this->CI->load->view('inc/footer', $pageData, TRUE);
		$pageData['favicon_link'] = $this->CI->load->view('inc/favicon', $pageData, TRUE);
		//--------Load Header, footer and slider -------//
		

		//--------Add Page Data-----//		
		$pageData['title'] = $title;
		$pageData['favicon'] = $favicon;
		$pageData['favicon_type'] = $favicon_type;
		$pageData['pageData'] = $pageData;
		//--------Add Page Data-----//


		//-------------Construct page-------//
		$this->CI->load->helper(array('form'));
		$this->CI->load->view($fullpageName, $pageData);		
		//-------------Construct page-------//

	}


	public function ActiveClass($call){
		$controller = $this->CI->uri->segment(1);
		$controller2 = $this->CI->uri->segment(2); 
		if($controller==$call){
			return 'class="active"';
		}
		if($controller2==$call){
			return 'class="active"';
		}
	}

	public function getCallout($alertType, $alertMsg, $alertHeading='', $alertIcon=''){

		$alertData = '<div class="callout callout-'.$alertType.'">
						<h4><i class="'.$alertIcon.'"></i> '.$alertHeading.'</h4>
					    <p>'.$alertMsg.'</p>
					</div>';
		return $alertData;
	}

	public function getAlert($alertType, $alertMsg, $alertHeading='Alert', $alertIcon=''){
	
		$alertData = '<div class="alert alert-'.$alertType.' alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                <h4><i class="icon '.$alertIcon.'"></i> '.$alertHeading.'</h4>
                '.$alertMsg.'
     			</div>';
     	return $alertData;
	}

	



	


	public function generateStrongPassword($length = 9, $add_dashes = false, $available_sets = 'luds')
	{
		$sets = array();
		if(strpos($available_sets, 'l') !== false)
			$sets[] = 'abcdefghjkmnpqrstuvwxyz';
		if(strpos($available_sets, 'u') !== false)
			$sets[] = 'ABCDEFGHJKMNPQRSTUVWXYZ';
		if(strpos($available_sets, 'd') !== false)
			$sets[] = '23456789';
		if(strpos($available_sets, 's') !== false)
			$sets[] = '!@#$%&*?';
		$all = '';
		$password = '';
		foreach($sets as $set)
		{
			$password .= $set[array_rand(str_split($set))];
			$all .= $set;
		}
		$all = str_split($all);
		for($i = 0; $i < $length - count($sets); $i++)
			$password .= $all[array_rand($all)];
		$password = str_shuffle($password);
		if(!$add_dashes)
			return $password;
		$dash_len = floor(sqrt($length));
		$dash_str = '';
		while(strlen($password) > $dash_len)
		{
			$dash_str .= substr($password, 0, $dash_len) . '-';
			$password = substr($password, $dash_len);
		}
		$dash_str .= $password;
		return $dash_str;
	}


	//-----------Get user Operationg syster info-----------//
	public function getOS() { 
		if(isset($_SERVER['HTTP_USER_AGENT'])){
			$user_agent 	=   $_SERVER['HTTP_USER_AGENT'];	 
		    $os_platform    =   "Unknown OS Platform";
		    $os_array       =   array(
		                            '/windows nt 10/i'     =>  'Windows 10',
		                            '/windows nt 6.3/i'     =>  'Windows 8.1',
		                            '/windows nt 6.2/i'     =>  'Windows 8',
		                            '/windows nt 6.1/i'     =>  'Windows 7',
		                            '/windows nt 6.0/i'     =>  'Windows Vista',
		                            '/windows nt 5.2/i'     =>  'Windows Server 2003/XP x64',
		                            '/windows nt 5.1/i'     =>  'Windows XP',
		                            '/windows xp/i'         =>  'Windows XP',
		                            '/windows nt 5.0/i'     =>  'Windows 2000',
		                            '/windows me/i'         =>  'Windows ME',
		                            '/win98/i'              =>  'Windows 98',
		                            '/win95/i'              =>  'Windows 95',
		                            '/win16/i'              =>  'Windows 3.11',
		                            '/macintosh|mac os x/i' =>  'Mac OS X',
		                            '/mac_powerpc/i'        =>  'Mac OS 9',
		                            '/linux/i'              =>  'Linux',
		                            '/ubuntu/i'             =>  'Ubuntu',
		                            '/iphone/i'             =>  'iPhone',
		                            '/ipod/i'               =>  'iPod',
		                            '/ipad/i'               =>  'iPad',
		                            '/android/i'            =>  'Android',
		                            '/blackberry/i'         =>  'BlackBerry',
		                            '/webos/i'              =>  'Mobile'
		                        );

		    foreach ($os_array as $regex => $value) { 
		        if (preg_match($regex, $user_agent)) {
		            $os_platform    =   $value;
		        }
		    }   
		    return $os_platform;
		}
		else{
			return 'API Call';
		}
	}
	//-----------Get user Operationg syster info-----------//
	//-----------Get user Brouwser info-----------------//
	function getBrowser() {

			if(isset($_SERVER['HTTP_USER_AGENT'])){
			    $user_agent 	=   $_SERVER['HTTP_USER_AGENT'];
			    $browser        =   "Unknown Browser";
			    $browser_array  =   array(
			                            '/msie/i'       =>  'Internet Explorer',
			                            '/firefox/i'    =>  'Firefox',
			                            '/safari/i'     =>  'Safari',
			                            '/chrome/i'     =>  'Chrome',
			                            '/edge/i'       =>  'Edge',
			                            '/opera/i'      =>  'Opera',
			                            '/netscape/i'   =>  'Netscape',
			                            '/maxthon/i'    =>  'Maxthon',
			                            '/konqueror/i'  =>  'Konqueror',
			                            '/mobile/i'     =>  'Handheld Browser'
			                        );

			    foreach ($browser_array as $regex => $value) { 

			        if (preg_match($regex, $user_agent)) {
			            $browser    =   $value;
			        }

			    }

			    return $browser;
			}
			else{
				return 'API Call';
			}

	}
	//-----------Get user Brouwser info-----------------//
	//-----------Get time and date---------------------//
	function getTimeDate() {
		$tm_dt=array();
		date_default_timezone_set('Asia/Kolkata'); 
		$tm_dt['tm']=date('h:i A');
		$tm_dt['dt']= date('Y-m-d');
		return $tm_dt;
	}
	//-----------Get time and date---------------------//

	//-----------Get time and date---------------------//
	function DateTime() {
		$tm_dt=array();
		date_default_timezone_set('Asia/Kolkata'); 
		return date('d-m-Y');		
	}
	//-----------Get time and date---------------------//

	//-----------Get user ip address------------------//
	function getIP()
	{
	    $client  = @$_SERVER['HTTP_CLIENT_IP'];
	    $forward = @$_SERVER['HTTP_X_FORWARDED_FOR'];
	    $remote  = $_SERVER['REMOTE_ADDR'];
	    if(filter_var($client, FILTER_VALIDATE_IP)){    
	        $ip = $client;
	    }
	    elseif(filter_var($forward, FILTER_VALIDATE_IP)){    
	        $ip = $forward;
	    }
	    else{    
	        $ip = $remote;
	    }
	    return $ip;
	}
	//-----------Get user ip address------------------//

	Public function numberTowords($num)
		{ 
		   $number = $num;
		   $no = round($number);
		   $point = round($number - $no, 2) * 100;
		   $hundred = null;
		   $digits_1 = strlen($no);
		   $i = 0;
		   $str = array();
		   $words = array('0' => '', '1' => 'one', '2' => 'two',
		    '3' => 'three', '4' => 'four', '5' => 'five', '6' => 'six',
		    '7' => 'seven', '8' => 'eight', '9' => 'nine',
		    '10' => 'ten', '11' => 'eleven', '12' => 'twelve',
		    '13' => 'thirteen', '14' => 'fourteen',
		    '15' => 'fifteen', '16' => 'sixteen', '17' => 'seventeen',
		    '18' => 'eighteen', '19' =>'nineteen', '20' => 'twenty',
		    '30' => 'thirty', '40' => 'forty', '50' => 'fifty',
		    '60' => 'sixty', '70' => 'seventy',
		    '80' => 'eighty', '90' => 'ninety');
		   $digits = array('', 'hundred', 'thousand', 'lakh', 'crore');
		   while ($i < $digits_1) {
		     $divider = ($i == 2) ? 10 : 100;
		     $number = floor($no % $divider);
		     $no = floor($no / $divider);
		     $i += ($divider == 10) ? 1 : 2;
		     if ($number) {
		        $plural = (($counter = count($str)) && $number > 9) ? 's' : null;
		        $hundred = ($counter == 1 && $str[0]) ? ' and ' : null;
		        $str [] = ($number < 21) ? $words[$number] .
		            " " . $digits[$counter] . $plural . " " . $hundred
		            :
		            $words[floor($number / 10) * 10]
		            . " " . $words[$number % 10] . " "
		            . $digits[$counter] . $plural . " " . $hundred;
		     } else $str[] = null;
		  }
		  $str = array_reverse($str);
		  $result = implode('', $str);
		  $points = ($point) ?
		    "." . $words[$point / 10] . " " . 
		          $words[$point = $point % 10] : '';
		   $result . "Rupees  " . $points . " Paise";
		return $result; 
		} 



		public function userIdToNameDocs($userId)
		{
			$userName = $this->CI->tfn->getData('*', 'users', "user_id = '".$userId."' ");
			if($userName === 'No data' || $userName === 'Table not exists' || $userName === ''){
				$userName2 = $this->CI->tfn->getData('Full_Name,Email_ID', 'pis_users_7', "id = '".$userId."' ");
				if($userName2 === 'No data' || $userName2 === 'Table not exists' || $userName2 === ''){
					return '';
				}
				else{
					return $userName2[0]['Full_Name'].'<br/>('.$userName2[0]['Email_ID'].')';
				}
			}else{ 
				return $userName[0]['first_name'].' '.$userName[0]['last_name'];
			}
		}


		public function userIdToName($userId)
		{
			$userName = $this->CI->tfn->getData('full_name', 'users', "user_id = '".$userId."' ");
			if($userName === 'No data' || $userName === 'Table not exists' || $userName === ''){return 'Invalid ID'; }else{ 
				return $userName[0]['full_name'];
			}
		}


		public function MenuIdToName($menu_id)
		{
			$menu_name = $this->CI->tfn->getData('menu_name', 'menus', "id = '".$menu_id."' ");
			if($menu_name === 'No data' || $menu_name === 'Table not exists' || $menu_name === ''){return 'None'; }else{ return $menu_name[0]['menu_name'];
			}
		}

		public function catIdToName($cat_id)
		{
			$catInfo = $this->CI->tfn->getData('cat_name', 'categories', "id = '".$cat_id."' ");
			if($catInfo === 'No data' || $catInfo === 'Table not exists' || $catInfo === ''){return 'None'; }else{ 
				return $catInfo[0]['cat_name'];
			}
		}


		public function moduleSlugToId($slug)
		{
			$ModuleId = $this->CI->tfn->getData('id', 'modules', "slug = '".$slug."' AND status = 1");
			if($ModuleId === 'No data' || $ModuleId === 'Table not exists' || $ModuleId === ''){return 'None'; }else{ 
				return $ModuleId[0]['id'];
			}
		}


		public function fieldIdtoFieldName($field_id)
		{
			$dataRes = $this->CI->tfn->getData('*', 'module_fields', "id = '".$field_id."' AND status = 1");
			if($dataRes === 'No data' || $dataRes === 'Table not exists' || $dataRes === ''){return 'None'; }else{ 
				return $dataRes[0]['field_name'];
			}
		}


		public function fieldNametoFieldId($field_name, $module_id)
		{
			$dataRes = $this->CI->tfn->getData('*', 'module_fields', "field_name = '".$field_name."' AND status = 1 AND module_id = '".$module_id."' ");
			if($dataRes === 'No data' || $dataRes === 'Table not exists' || $dataRes === ''){return 'None'; }else{ 
				return $dataRes[0]['id'];
			}
		}

		

		



		public function human_filesize($bytes, $decimals = 2) {
		    $size = array(' B',' KB',' MB',' GB',' TB',' PB',' EB',' ZB',' YB');
		    $factor = floor((strlen($bytes) - 1) / 3);
		    return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . @$size[$factor];
		}


		public function uploadSingleFile($upload_path, $file_type, $controller_name)
		{

			$config['upload_path']          = $upload_path;
			$config['allowed_types']        = $file_type;
			$config['encrypt_name'] 				= TRUE;
			$this->CI->load->library('upload', $config);
			$returnArray = array();
			if ( ! $this->CI->upload->do_upload($controller_name)){
				$error = $this->CI->upload->display_errors();
				$returnArray['status'] = 'failed';
				$returnArray['error'] = $error;
			}
			else{
				$data = array('images' => $this->CI->upload->data());
				$returnArray['status'] = 'success';
				$returnArray['file_name']=$data['images']['file_name'];
				$returnArray['path']=$data['images']['full_path'];
			}

			return $returnArray;

		}


		public function uploadMultipleFiles($path, $controller_name, $types = 'jpg|gif|png')
	    {
	        $config = array(
	            'upload_path'   => $path,
	            'allowed_types' => $types,	                                   
	        );

	        $this->CI->load->library('upload', $config);

	        $images = array();

	        foreach ($_FILES[$controller_name]['name'] as $key => $image) {
	            $_FILES['more_images[]']['name']= $_FILES[$controller_name]['name'][$key];
	            $_FILES['more_images[]']['type']= $_FILES[$controller_name]['type'][$key];
	            $_FILES['more_images[]']['tmp_name']= $_FILES[$controller_name]['tmp_name'][$key];
	            $_FILES['more_images[]']['error']= $_FILES[$controller_name]['error'][$key];
	            $_FILES['more_images[]']['size']= $_FILES[$controller_name]['size'][$key];

	            $fileName = $image;

	            $images[] = $fileName;

	            $config['file_name'] = $fileName;

	            $this->CI->upload->initialize($config);

	            if ($this->CI->upload->do_upload('more_images[]')) {
	                $this->CI->upload->data();
	            } else {
	                return false;
	            }
	        }

	        return $images;
	    }



	public function core_services($id)
	{
		$coerInfo = $this->CI->tfn->getData('name', 'core_services', "id = '$id' ");
		return $coerInfo[0]['name'];
	}

	public function product_name($id)
	{
		$coerInfo = $this->CI->tfn->getData('name', 'products', "id = '$id' ");
		return $coerInfo[0]['name'];
	}

	public function sub_product_name($id)
	{
		$coerInfo = $this->CI->tfn->getData('skill_category', 'sub_product', "id = '$id' ");
		return $coerInfo[0]['skill_category'];
	}



	public function formValidator($postData, $formFields){
		$msg = '';
		foreach ($postData as $key => $value) {
			if(array_key_exists($key, $formFields)){
				if(isset($formFields[$key]['attributes']['required'])){
					if($formFields[$key]['attributes']['required'] || $formFields[$key]['attributes']['required'] != 'false' ){
						if(strtolower( $formFields[$key]['type'] )   == 'file'){
							echo $key;
							if (empty($_FILES[$key]['name'])) {
								$msg .="<li>'$key=' file required</li>";
							}
						}else{
							if($value == NULL || $value == ''){
								$lable_name = ucwords($formFields[$key]['label']);
								if(isset($formFields[$key]['attributes']['data-validation-error-msg'])){
									$msg .="<li>".$formFields[$key]['attributes']['data-validation-error-msg']."</li>";
								}
								else{
									$msg .="<li>".$lable_name.": is required</li>";
								}
							}
						}
						
					}
				}
			}
		}
		return $msg;
	}

	public function attributesToStr($attributesArray)
	{		
		if(isset($attributesArray['required']) && $attributesArray['required'] && $attributesArray['required'] != 'false' ){
			//$attributesArray['required'] = 'required';
			unset($attributesArray['required']);
		}
		else{
			unset($attributesArray['required']);
			unset($attributesArray['data-validation']);
			unset($attributesArray['data-validation-error-msg']);
		}
		$str = ''; 
		foreach ($attributesArray as $key => $value) {
			$str .= $key.'="'.$value.'" ';
		}
		return $str;
	}

	public function duplicateCheck($postData, $table_name, $formFields, $id = '')
	{
		
		$whr = "";

		foreach ($postData as $key => $value) { 
			if(array_key_exists($key, $formFields) ){
				if($formFields[$key]['duplicate'] == 'TRUE'){
					continue;
				}
				else{
					if ($this->CI->db->table_exists($table_name) ){
						$fields = $this->CI->db->list_fields($table_name);
						if(in_array($key, $fields) ){
							$whr .= ' AND '.$key . " = '".$value."' " ;
						}
					}
					
				}
			}
		}
		if($id !=''){
			$id_check = "AND id !='$id'";
		}
		else{
			$id_check = "";
		}
		
		if($whr == ''){
			$result['count'] = 0;
			return $result;
		}
		else{
			$result['count'] = $this->CI->tfn->getNumrows('id', $table_name, "status = 1 $id_check $whr ");
			$whr = trim(strstr($whr," "));
			$result['duplicate_str'] = trim(strstr($whr," "));
			return $result;
		}

		
		
	}


	public function checkBoxHTMLnested($field, $parent_id = '')
	{

		//print_r($field);

		

		$field_data = $this->CI->tfn->getData('*', 'module_fields', "status = 1 AND id = '".$field['id']."' ");

		$attributes_str = $this->attributesToStr($field['attributes']);
    $attributes_str = str_replace('class="form-control"', '', $attributes_str);

    $dropDown_info = $this->CI->tfn->getData('*', 'dropdown_data', "status = 1 AND field_id = '".$field['id']."' AND module_id = '".$field_data[0]['module_id']."' ");



		$table_name = $this->CI->tfn->getData('module_name', 'modules', "status = 1 AND id = '".$dropDown_info[0]['field_select_box_table']."' ");
		$col_name = $this->CI->tfn->getData('field_name', 'module_fields', "status = 1 AND id = '".$dropDown_info[0]['field_select_box_table_column']."' ");


		$child_relation = $this->CI->tfn->getData('*', 'tables_relation', "status = 1 AND child_table_id = '".$dropDown_info[0]['field_select_box_table']."' ");
		//echo "<pre>";
		//print_r($child_relation);

		if($child_relation !='No data'){

			$get_child_col_name = $this->CI->tfn->getData('field_name', 'module_fields', "status = 1 AND id = '".$child_relation[0]['child_col_id']."' ");


			$checkbox_data = $this->CI->tfn->getData('id, '.$col_name[0]['field_name'] , $table_name[0]['module_name'], "status = 1 AND ".$get_child_col_name[0]['field_name']." = '$parent_id' ");
			
		}
		else{
			$checkbox_data = $this->CI->tfn->getData('id, '.$col_name[0]['field_name'] , $table_name[0]['module_name'], "status = 1 ");
		}
		
		//print_r($checkbox_data);
		//echo "<br/>";
		if($checkbox_data !='No data'){                  			
			foreach ($checkbox_data as $key => $op_row) {
				if($dropDown_info[0]['db_module_have_child'] == 'Yes'){ 
					//echo $field['db_name'];
					?>
					<div class="panel box box-success border-rounded-success">
            <div class="box-header with-border">
              <h4 class="box-title">
              <label> 
                <input type="checkbox" class="minimal" name="<?php echo $field['db_name'] ?>[]" value="<?php echo $op_row['id']; ?>">
              </label>

                <a data-toggle="collapse" data-parent="#accordion" href="#collapse<?php echo $key ?>" aria-expanded="false" class="collapsed">
                  <?php echo $op_row[$col_name[0]['field_name']]; ?>
                </a>
              </h4>
            </div>
            <div id="collapse<?php echo $key ?>" class="panel-collapse collapse" aria-expanded="false" style="height: 0px;">
              <div class="box-body"> 
              	<?php 

              		$child_field = $this->CI->tfn->getData('*', 'module_fields', "status = 1 AND module_id = '".$field['module_id']."' AND module_parent = '".$field['db_name']."' ");
              		if($child_field !='No data'){
              			foreach ($child_field as $sub_row) {

              					if($sub_row['input_type'] == 'Database'){

              							$dropDown_info = $this->CI->tfn->getData('*', 'dropdown_data', "status = 1 AND field_id = '".$field['id']."' AND module_id = '".$field_data[0]['module_id']."' ");

														$table_name = $this->CI->tfn->getData('module_name', 'modules', "status = 1 AND id = '".$dropDown_info[0]['field_select_box_table']."' ");
														$col_name = $this->CI->tfn->getData('field_name', 'module_fields', "status = 1 AND id = '".$dropDown_info[0]['field_select_box_table_column']."' ");
														if($dropDown_info[0]['db_module_have_child'] == 'Yes'){


															


															$checkbox_data = $this->CI->tfn->getData('id, '.$col_name[0]['field_name'] , $table_name[0]['module_name'], "status = 1 ");

															$reCall['module_id'] = $sub_row['module_id'];
															$reCall['input_type'] = $sub_row['input_type'];
															$reCall['db_name'] = $sub_row['field_name'];
															$reCall['id'] = $sub_row['id'];
															$reCall['type'] = $sub_row['field_type'];
															$reCall['label'] = $sub_row['field_label'];

															$reCall['duplicate'] = $sub_row['field_duplicate'];

															$attributes = $this->CI->tfn->getData('*', 'field_attributes', "status = 1 AND module_id = '".$sub_row['module_id']."' AND field_id = '".$sub_row['id']."' ");

															$atr = array();
															if($attributes !='No data'){
																foreach ($attributes as  $value) {
																	if(strtolower( $sub_row['field_type'] )   == 'file'){
																		if($value['attribute_value'] == 'required' || $value['attribute_value'] == 'TRUE'){
																			if(!in_array($sub_row['field_name'], $this->files_required) ){
																				array_push($this->files_required, $sub_row['field_name']);
																			}
																			
																		}
																	}
																	$atr[$value['attribute_name']] = $value['attribute_value'];
																}
															}
															$reCall['attributes'] = $atr;
															$this->checkBoxHTMLnested($reCall, $op_row['id']);
														}
														else{ 

															if($parent_id == ''){
																echo "<br/>";
																echo '<input name="'.$field['db_name'].'[]" type="checkbox" '.$attributes_str.' value="'.$op_row['id'].'" /> '.$op_row[$col_name[0]['field_name']];
																echo "<br/>";
															}
															else{
																//echo "<br/>";
																echo '<input name="'.$field['db_name'].'_'.$parent_id.'[]" type="checkbox" '.$attributes_str.' value="'.$op_row['id'].'" /> '.$op_row[$col_name[0]['field_name']];
																echo "<br/>";
															}

														}
              					}


              			 ?>
              				
              				<?php
              			}
              		}
              	?> 
              </div>
            </div>
          </div>
					<?php 
				}
				else{
					if($parent_id == ''){
						echo "<br/>";
						echo '<input name="'.$field['db_name'].'[]" type="checkbox" '.$attributes_str.' value="'.$op_row['id'].'" /> '.$op_row[$col_name[0]['field_name']];
						echo "<br/>";
					}
					else{
						//echo "<br/>";
						echo '<input name="'.$field['db_name'].'__'.$parent_id.'[]" type="checkbox" '.$attributes_str.' value="'.$op_row['id'].'" /> '.$op_row[$col_name[0]['field_name']];
						echo "<br/>";
					}
					
				}
				
			}
		}
		else{
			//echo "<option value=''>No data</option>";
		}


	}


	public function create_form_new($formFields)
	{
		if(!empty($formFields)){
            foreach ($formFields as $name => $field) { ?>
              <div class="form-group">
                  <label for="full_name">
                    <?php echo $field['label']; ?>
                    <?php if(isset($field['attributes']['required']) && $field['attributes']['required']){ 
                      echo '<span class="text-red">*</span>';
                    } ?>
                  </label> 
                  <?php 
                  //---Textarea
                  if( strtolower( $field['type'] )  == 'textarea'){ 
                    $attributes_str = $this->attributesToStr($field['attributes']);
                    ?>
                    <textarea name="<?php echo $name; ?>" <?php echo $attributes_str; ?>></textarea>
                    <?php
                  } //---Select Box (Dropdown)
                  else if( strtolower( $field['type'] )  == 'select-box'){
                  	$attributes_str = $this->attributesToStr($field['attributes']);
                  	echo "<select name='$name' $attributes_str>";
                  	echo "<option value=''>Select ".$field['label']."</option>";
                  	$field_data = $this->CI->tfn->getData('*', 'module_fields', "status = 1 AND id = '".$field['id']."' ");
                  	
                  	$dropDown_info = $this->CI->tfn->getData('*', 'dropdown_data', "status = 1 AND field_id = '".$field['id']."' AND module_id = '".$field_data[0]['module_id']."' ");

                  	

                  	if($field_data[0]['field_select_box_type'] == 'Database'){
                  		$attributes_str = $this->attributesToStr($field['attributes']);
                  		
                  		$table_name = $this->CI->tfn->getData('menu_id, module_name', 'modules', "status = 1 AND id = '".$dropDown_info[0]['field_select_box_table']."' ");
                  		$col_name = $this->CI->tfn->getData('field_name', 'module_fields', "status = 1 AND id = '".$dropDown_info[0]['field_select_box_table_column']."' ");

                  		
                  		$options_data = $this->CI->tfn->getData('id, '.$col_name[0]['field_name'] , $table_name[0]['module_name'].'_'.$table_name[0]['menu_id'], "status = 1 ");


                  		if($options_data !='No data'){
                  			foreach ($options_data as $op_row) {
                  				echo "<option value='".$op_row['id']."'>".$op_row[$col_name[0]['field_name']]."</option>";
                  			}
                  		}
                  		else{
                  			echo "<option value=''>No data</option>";
                  		}
                  	}
                  	else if($field_data[0]['field_select_box_type'] == 'Options'){
                  		if($dropDown_info !='No data'){
                  			foreach ($dropDown_info as $key => $value) {
                  				if($value['op_val'] !=''){
                  					$v = "value='".$value['op_val']."'";
                  				}
                  				else{
                  					$v = "";
                  				}
                  				echo "<option $v >".$value['op_name']."</option>";
                  			}
                  			
                  		}
                  		
                  	}
                  	?>

                  	<?php
                  	echo "</select>";
                  }//-----Checkbox
                  else if(strtolower( $field['type'] )  == 'checkbox'){

                  	if($field['input_type'] == 'Database'){
                  			
                  		echo $this->checkBoxHTMLnested($field);
                  	}
                  	else{

                  	}
                  	
                  }//---Others
                  else{ 
                    $attributes_str = $this->attributesToStr($field['attributes']);
                    ?>
                    <input type="<?php echo $field['type']; ?>" name="<?php echo $name; ?>" <?php echo $attributes_str; ?> />
                    <?php
                  } ?>
                  
              </div>
              <?php
            }
        }
	}


	public function create_form($formFields)
	{
		if(!empty($formFields)){
            foreach ($formFields as $name => $field) { ?>
              <div class="form-group">
                  <label for="full_name">
                    <?php echo $field['label']; ?>
                    <?php if(isset($field['attributes']['required']) && $field['attributes']['required']){ 
                      echo '<span class="text-red">*</span>';
                    } ?>
                  </label> 
                  <?php if( strtolower( $field['type'] )  == 'textarea'){ 
                    $attributes_str = $this->attributesToStr($field['attributes']);
                    ?>
                    <textarea name="<?php echo $name; ?>" <?php echo $attributes_str; ?>></textarea>
                    <?php
                  }else if( strtolower( $field['type'] )  == 'select-box'){
                  	$attributes_str = $this->attributesToStr($field['attributes']);
                  	echo "<select name='$name' $attributes_str>";
                  	echo "<option value=''>Select ".$field['label']."</option>";
                  	$field_data = $this->CI->tfn->getData('*', 'module_fields', "status = 1 AND id = '".$field['id']."' ");
                  	
                  	$dropDown_info = $this->CI->tfn->getData('*', 'dropdown_data', "status = 1 AND field_id = '".$field['id']."' AND module_id = '".$field_data[0]['module_id']."' ");

                  	

                  	if($field_data[0]['field_select_box_type'] == 'Database'){
                  		$attributes_str = $this->attributesToStr($field['attributes']);
                  		
                  		$table_name = $this->CI->tfn->getData('module_name', 'modules', "status = 1 AND id = '".$dropDown_info[0]['field_select_box_table']."' ");
                  		$col_name = $this->CI->tfn->getData('field_name', 'module_fields', "status = 1 AND id = '".$dropDown_info[0]['field_select_box_table_column']."' ");


                  		$options_data = $this->CI->tfn->getData('id, '.$col_name[0]['field_name'] , $table_name[0]['module_name'], "status = 1 ");


                  		if($options_data !='No data'){
                  			foreach ($options_data as $op_row) {
                  				echo "<option value='".$op_row['id']."'>".$op_row[$col_name[0]['field_name']]."</option>";
                  			}
                  		}
                  		else{
                  			echo "<option value=''>No data</option>";
                  		}
                  	}
                  	else if($field_data[0]['field_select_box_type'] == 'Options'){
                  		if($dropDown_info !='No data'){
                  			foreach ($dropDown_info as $key => $value) {
                  				if($value['op_val'] !=''){
                  					$v = "value='".$value['op_val']."'";
                  				}
                  				else{
                  					$v = "";
                  				}
                  				echo "<option $v >".$value['op_name']."</option>";
                  			}
                  			
                  		}
                  		
                  	}
                  	?>

                  	<?php
                  	echo "</select>";
                  }
                  else{ 
                    $attributes_str = $this->attributesToStr($field['attributes']);
                    ?>
                    <input type="<?php echo $field['type']; ?>" name="<?php echo $name; ?>" <?php echo $attributes_str; ?> />
                    <?php
                  } ?>
                  
              </div>
              <?php
            }
        }
	}


	public function create_form_for_update($formFields, $rowInfo, $table_name_del_file = '')
	{
		if(!empty($formFields)){
            foreach ($formFields as $name => $field) { ?>
              <div class="form-group">
                  <label for="full_name">
                    <?php echo $field['label']; ?>
                    <?php if(isset($field['attributes']['required']) && $field['attributes']['required']){ 
                      echo '<span class="text-red">*</span>';
                    } ?>
                  </label> 
                  <?php if( strtolower( $field['type'] )  == 'textarea'){ 
                    $attributes_str = $this->attributesToStr($field['attributes']);

                    $attributes_str = str_replace('ckeditor', 'edit-ckeditor', $attributes_str);

                    ?>
                    <textarea  name="<?php echo $name; ?>" <?php echo $attributes_str; ?>><?php echo $rowInfo[$name] ?></textarea>
                    <?php
                  }else if( strtolower( $field['type'] )  == 'select-box'){
                  	$attributes_str = $this->attributesToStr($field['attributes']);
                  	echo "<select name='$name' $attributes_str>";
                  	echo "<option value=''>Select ".$field['label']."</option>";

                  	$field_data = $this->CI->tfn->getData('*', 'module_fields', "status = 1 AND id = '".$field['id']."' ");
                  	
                  	$dropDown_info = $this->CI->tfn->getData('*', 'dropdown_data', "status = 1 AND field_id = '".$field['id']."' AND module_id = '".$field_data[0]['module_id']."' ");

                  	

                  	if($field_data[0]['field_select_box_type'] == 'Database'){
                  		$attributes_str = $this->attributesToStr($field['attributes']);
                  		
                  		$table_name = $this->CI->tfn->getData('menu_id, module_name', 'modules', "status = 1 AND id = '".$dropDown_info[0]['field_select_box_table']."' ");
                  		$col_name = $this->CI->tfn->getData('field_name', 'module_fields', "status = 1 AND id = '".$dropDown_info[0]['field_select_box_table_column']."' ");


                  		$options_data = $this->CI->tfn->getData('id, '.$col_name[0]['field_name'] , $table_name[0]['module_name'].'_'.$table_name[0]['menu_id'], "status = 1 ");

                  		
                  		if($options_data !='No data'){
                  			foreach ($options_data as $op_row) {

                  				if($rowInfo[$name] == $op_row['id']){
                  					$s = 'selected';
                  				}
                  				else{
                  					$s = '';
                  				}
                  				echo "<option value='".$op_row['id']."' $s >".$op_row[$col_name[0]['field_name']]."</option>";
                  			}
                  		}
                  		else{
                  			echo "<option value=''>No data</option>";
                  		}
                  	}
                  	else if($field_data[0]['field_select_box_type'] == 'Options'){
                  		if($dropDown_info !='No data'){
                  			foreach ($dropDown_info as $key => $value) {
                  				if($value['op_val'] !=''){
                  					$v = "value='".$value['op_val']."'";
                  				}
                  				else{
                  					$v = "";
                  				}

                  				if($rowInfo[$name] == $value['op_val']){
                  					$s = 'selected';
                  				}
                  				else{
                  					$s = '';
                  				}


                  				echo "<option $v $s >".$value['op_name']."</option>";
                  			}
                  			
                  		}
                  		
                  	}
                  	?>

                  	<?php
                  	echo "</select>";
                  }
                  else{ 
                    $attributes_str = $this->attributesToStr($field['attributes']);
                    if(strtolower($field['type']) == 'file'){
                    	if(isset($rowInfo[$name]) && $rowInfo[$name] != ''){
	                    	$ext = pathinfo(base_url().'uploads/'.$rowInfo[$name], PATHINFO_EXTENSION);
							if(in_array(strtolower($ext), explode(',', imageTypes))){
							echo '<span><br/><a href="'.base_url().'uploads/'.$rowInfo[$name].'" target="_blank"><img src="'.base_url().'uploads/'.$rowInfo[$name].'" class="img_preview" /></a><br/>';

							if(!isset($field['attributes']['required'])){							
								$del_file_atr = "'".$table_name_del_file."', '".$name."', '".$rowInfo[$name]."', 'id','".$rowInfo['id']."',  $(this)";
								echo '<i onclicl="deleteFile('.$del_file_atr.')" class="fa fa-close close_btn"></i><br/>';
							}						
							echo '<small>For replace select new image from below</small><br/></span> ';

							}
							else{
								echo '<span><br/><a href="'.base_url().'uploads/'.$rowInfo[$name].'" target="_blank">View/Download</a> ';

								if(!isset($field['attributes']['required'])){
									$del_file_atr = "'".$table_name_del_file."', '".$name."', '".$rowInfo[$name]."', 'id','".$rowInfo['id']."',  $(this)";
									echo '<i onclicl="deleteFile('.$del_file_atr.')" class="fa fa-close close_btn"></i><br/>';
								}						
								echo '<small>For replace select new image from below</small><br/></span>';
							}
						}
                    }
                    ?>
                    <input type="<?php echo $field['type']; ?>" name="<?php echo $name; ?>" <?php echo $attributes_str; ?> value="<?php if(isset($rowInfo[$name])){echo $rowInfo[$name]; } ?>" />
                    <?php
                  } ?>
                  
              </div>
              <?php
            }
        }

        ?>

        

		<script>
		  
		    ClassicEditor
		            .create( document.querySelector( '.edit-ckeditor',{
		             
		            } ) )
		            .then( editor => {
		                    console.log( editor );
		            } )
		            .catch( error => {
		                    console.error( error );
		            } );
		</script>



		        <?php 
	}

	public function getButtons($buttons = array())
	{
		if(empty($buttons)){
			echo '<button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-close"></i> Cancel</button>
          <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> Submit</button>';
		}
		else{
			
			foreach ($buttons as $key => $btn_row) {
				$btn_atr = '';
				foreach($btn_row as $attribute_name => $attribute_value){
					$btn_atr .= $attribute_name .'="'.$attribute_value.'" ';
				}
				echo "<input $btn_atr />";
			}
		}
		
	}

	public function allFieldCheck($postData)
	{
		$blank = TRUE;
		foreach ($postData as $key => $value) {
			if($value !=''){
				$blank = FALSE;
			}
		}
		return $blank;
	}



	public function sendMail($toEmail, $subject, $html)
	{
		$config = Array(
			'protocol' => 'smtp',
			'smtp_host' => 'ssl://smtp.googlemail.com',
			'smtp_port' => 465,
			'smtp_user' => 'iitm.monsoon@gmail.com', // change it to yours
			'smtp_pass' => 'ypmadssymhtlyzvc', // change it to yours
			'mailtype' => 'html',
			'charset' => 'iso-8859-1',
			'wordwrap' => TRUE
		);
	
		
		$this->CI->load->library('email', $config);
		$this->CI->email->set_newline("\r\n");

		$this->CI->email->from('iitm.monsoon@gmail.com', 'IITM Monsoon Mission'); // change it to yours
		

		$this->CI->email->to($toEmail);// change it to yours
		$this->CI->email->subject($subject);
		$this->CI->email->message($html);
		if($this->CI->email->send())
		{
			return true;
		}
		else
		{
			show_error($this->CI->email->print_debugger());
		}
	
	}
	

	

}

/* End of file app_helper.php */
/* Location: ./application/helpers/app_helper.php */


?>