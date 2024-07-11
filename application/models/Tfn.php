<?php 
	defined('BASEPATH') or exit('No direct script access allowed');
	class Tfn extends CI_Model {		


		//---------------Get Numrows----------------------------------
		function getNumrows($select, $table_name, $where_condition = '1=1' , $order_by_name = '', $order_by = ''){
			//----------------Check Table exists or not------------
			if ($this->db->table_exists($table_name) ){
				$this->db->select($select);
				$this->db->from($table_name);
				$this->db->where($where_condition);
				$this->db->order_by($order_by_name, $order_by);

				$query = $this->db->get();
				return $query->num_rows();
			}
			else{
				return 'Table not exists';
			}
		}

		//---------------Get Numrows----------------------------------
		function getData($select, $table_name, $where_condition = '1=1' , $order_by_name = '', $order_by = '', $limit = '', $start = ''){ 
			//----------------Check Table exists or not------------ 
			if ($this->db->table_exists($table_name) ){
				$this->db->select($select);
				$this->db->from($table_name);
				$this->db->where($where_condition);
				$this->db->order_by($order_by_name, $order_by);

				if ($limit != '' && $start != '') {
			       $this->db->limit($limit, $start);
			    }
			    elseif ($limit != '' && $start == '') {
			       $this->db->limit($limit);
			    }
				$query = $this->db->get();
				if($query -> num_rows()>=1){
					$result = $query->result();
					//--------------Return Data as Associative Array----
					return $row = json_decode(json_encode($result), true);

				}
				else{
					//----------If No Data Into Tabel or Fetch error------
					return 'No data';
				}
			}
			else{
				return 'Table not exists';
			}
		}

		//---------------Get distinct Data----------------------------------
		function getDataDistinct($select, $table_name, $where_condition = '1=1' , $order_by_name = '', $order_by = '', $limit = ''){ 
			//----------------Check Table exists or not------------ 
			if ($this->db->table_exists($table_name) ){
				$this->db->distinct();
				$this->db->select($select);
				$this->db->from($table_name);
				$this->db->where($where_condition);
				$this->db->order_by($order_by_name, $order_by);
				$query = $this->db->get();
				if($query -> num_rows()>=1){
					$result = $query->result();
					//--------------Return Data as Associative Array----
					return $row = json_decode(json_encode($result), true);

				}
				else{
					//----------If No Data Into Tabel or Fetch error------
					return 'No data';
				}
			}
			else{
				return 'Table not exists';
			}
		}


		function getJoinData($select, $tables_array, $where_condition_array, $order_by = ''){
			foreach ($tables_array as $table_name) {
				if ($this->db->table_exists($table_name) ){

				}
				else{
					return 'Table Not exists';
					exit();
				}
			}

			$this->db->select($select);
			$this->db->from($tables_array[0]);
			$loop=count($tables_array);
			for($i=1; $i<$loop; $i++){
				$this->db->join($tables_array[$i], $where_condition_array[$i-1]);
			}
			$this->db->where(end($where_condition_array));
			if($order_by!=''){
				$this->db->order_by($order_by[0], $order_by[1]);
			}
			$query = $this->db->get();
			if($query -> num_rows()>=1){
				$result = $query->result();
				return $row = json_decode(json_encode($result), true);
			}
			else{
				return 'No data';
			}
			
		}
		//-------------------Developed Database Table Fields dynamicaly-------
		function insertData($dataArray, $table_name){
			/*$results = $this->$db->query("SET session wait_timeout=28800", FALSE);
			// UPDATE - this is also needed
			$results = $this->$db->query("SET session interactive_timeout=28800", FALSE);*/			

			// Aditional data
			$dataTime = $this->ap->getTimeDate();
			$dataArray['ip_address'] = $this->ap->getIP();
			$dataArray['os'] = $this->ap->getOS();
			$dataArray['browser'] = $this->ap->getBrowser();
			$dataArray['date'] = $dataTime['dt'];
			$dataArray['time'] = $dataTime['tm'];
			$dataArray['status'] = 1;

			// Aditional data
			if ($this->db->table_exists($table_name) ){
				$fields = $this->db->list_fields($table_name);							
				$creatFields = array();
				$creatFieldstrue='false';
				foreach ($dataArray as $key => $value) {					
					if(!in_array($key, $fields)){
						$module_n_menu = explode('_', $table_name);						
						$menu_id = end($module_n_menu);
						if(isset($module_n_menu[1])){
							if(isset($module_n_menu[count($module_n_menu)-1])) {							
							    unset($module_n_menu[count($module_n_menu)-1]);
							}
							$module_name = implode('_', $module_n_menu);
							$module_id = $this->tfn->getData('id', 'modules', "status = 1 AND menu_id = '".$menu_id."' AND module_name = '".$module_name."' ");
							if($module_id !='No data'){
								$field_data = $this->tfn->getData('*', 'module_fields', "status = 1 AND module_id = '".$module_id[0]['id']."' AND field_name = '$key' ");
								if($field_data !='No data'){
									if($field_data[0]['field_type'] == 'TEXTAREA'){
										$field_to_be_created = array('type' => 'TEXT', 'null' => TRUE); 
									}
									else{
										$field_to_be_created = array('type' => 'VARCHAR', 'constraint' => '255', 'null' => TRUE);
									}
								}
								else{
									$field_to_be_created = array('type' => 'VARCHAR', 'constraint' => '255', 'null' => TRUE);
								}
							}
							else{
								$field_to_be_created = array('type' => 'VARCHAR', 'constraint' => '255', 'null' => TRUE);
							}
						}
						else{
							$field_to_be_created = array('type' => 'VARCHAR', 'constraint' => '255', 'null' => TRUE);
						}

						$creatFieldstrue='true';
						$creatFields[$key] = $field_to_be_created; 
					}					
				}
				if($creatFieldstrue==='true'){					
					$this->load->dbforge();					
				 	$this->dbforge->add_column($table_name, $creatFields);
				}

			}
			else{
				$this->load->dbforge();
				$creatFields = array();
				$creatFields['id'] = array('type' => 'INT', 'constraint' => 11, 'unsigned' => TRUE, 'auto_increment' => TRUE);
				
				foreach ($dataArray as $key => $value) {
					$module_n_menu = explode('_', $table_name);
					$menu_id = end($module_n_menu);
					if(isset($module_n_menu[1])){
						if(isset($module_n_menu[count($module_n_menu)-1])) {							
						    unset($module_n_menu[count($module_n_menu)-1]);
						}
						$module_name = implode('_', $module_n_menu);
						$module_id = $this->tfn->getData('id', 'modules', "status = 1 AND menu_id = '".$menu_id."' AND module_name = '".$module_name."' ");
						
						if($module_id !='No data'){ 
							$field_data = $this->tfn->getData('*', 'module_fields', "status = 1 AND module_id = '".$module_id[0]['id']."' AND field_name = '$key' ");
							if($field_data !='No data'){								
								if($field_data[0]['field_type'] == 'TEXTAREA'){
									$field_to_be_created = array('type' => 'TEXT', 'null' => TRUE); 
								}
								else{
									$field_to_be_created = array('type' => 'VARCHAR', 'constraint' => '255', 'null' => TRUE);
								}								
							}
							else{
								$field_to_be_created = array('type' => 'VARCHAR', 'constraint' => '255', 'null' => TRUE);
							}
						}
						else{
							$field_to_be_created = array('type' => 'VARCHAR', 'constraint' => '255', 'null' => TRUE);
						}
					}
					else{
						$field_to_be_created = array('type' => 'VARCHAR', 'constraint' => '255', 'null' => TRUE);
					}

					$creatFields[$key] = $field_to_be_created;
				}
							
				$this->dbforge->add_field($creatFields);
				$this->dbforge->add_key('id', TRUE);
				$this->dbforge->create_table($table_name);
			}			

			if($this->db->insert($table_name, $dataArray)){
				return true;
			}
			else{
				return false;
			}
		}

		//--------------------Update Record -------------------------------------------------------------
		function updateData($dataArray, $table_name, $update_col_name, $update_col_val, $where_col_name, $where_col_value, $user_id){
			//------------------------Check Table exists or not----
			if ($this->db->table_exists($table_name) ){
				$fields = $this->db->list_fields($table_name);
				$creatFields = array();
				$creatFieldstrue='false';
				foreach ($dataArray as $key => $value) {					
					//--------------Check Fields in table exists or not--
					if(!in_array($key, $fields)){
						$creatFieldstrue='true';
						$creatFields[$key] =array('type' => 'VARCHAR', 'constraint' => '255', 'null' => TRUE); 
					}
				}
				//------------Add Columns into existing table--------
				if($creatFieldstrue==='true'){
					$this->load->dbforge();
				 	$this->dbforge->add_column($table_name, $creatFields);
				}
				if(!in_array($update_col_name, $fields)){
					$creatFields[$update_col_name] =array('type' => 'VARCHAR', 'constraint' => '255', 'null' => TRUE);
					$this->load->dbforge();
				 	$this->dbforge->add_column($table_name, $creatFields);
				}
			}
			else{ //---------------Create New Table With New Columns-----------
				$this->load->dbforge();
				$creatFields = array();
				foreach ($dataArray as $key => $value) {
					$creatFields[$key] =array('type' => 'VARCHAR', 'constraint' => '255', 'null' => TRUE);
				}
				$creatFields[$update_col_name] =array('type' => 'VARCHAR', 'constraint' => '255', 'null' => TRUE);
				$this->dbforge->add_field($creatFields);
				$this->dbforge->add_key('id', TRUE);
				$this->dbforge->create_table($table_name);
			}
			//-----Get Old Data form Database-------------
			//echo $where_col_value;
			$fetchData = $this->tfn->getData('*', $table_name, "".$where_col_name."='".$where_col_value."' " , $order_by_name = '', $order_by = '');
			$oldData=$fetchData[0];
			if($table_name == 'users'){
				$oldId = $oldData['user_id'];
			}
			else{
				$oldId = $oldData['id'];
			}
			
			

			$updateDetails = array();

			//------------Unset Primary Column-----------
			unset($oldData['id']); 
			$oldData[$update_col_name]=$update_col_val;
			//-----------Save old value as old record---
			//$this->tfn->insertData($oldData, $table_name);
			$updateDetails['old_record'] = json_encode($oldData);
			$updateDetails['new_record'] = json_encode($dataArray);
			//---------Get Last inserted id------------
			//$lastInsertId = $this->db->insert_id();
			//---------Update old record status column----
			//$this->db->where('id', $lastInsertId);
			//$statusData[$update_col_name] = $update_col_val;
			//$res = $this->db->update($table_name, $statusData);
			//----------Now Finaly Update Record--------------
			$this->db->where($where_col_name, $where_col_value);
			$this->db->update($table_name, $dataArray);

			//echo $this->db->affected_rows();

			if($this->db->affected_rows() > 0){
				$res = 'updated';
			}
			else{
				$res = 'no_changes_found';
			}
			//---------Insert Update History----------------
			
			
			$updateDetails['table_name'] = $table_name;
			$updateDetails['edit_date'] = date('Y-m-d');
			$updateDetails['edit_by'] = $user_id;
			$updateDetails['view_status'] = 'unread';
			$updateDetails['row_id'] = $oldId;
			//$updateDetails['save_rc_in_id'] = $lastInsertId;
			$this->tfn->insertData($updateDetails, 'edit_details');
			//----------Return response----------------------------
			return $res;
			//---------------End of Update Function----------------
		}


		function updateDataSame($dataArray, $table_name, $where_col_name, $where_col_value){

			$this->db->where($where_col_name, $where_col_value);
			if($this->db->update($table_name, $dataArray)){
				return TRUE;
			}
			else{
				return FALSE;
			}
		}

		function deleteData($table_name, $where_col_name, $where_col_value){
			$dataArray['status']='0';
			//echo $where_col_value;
			$check = $this->tfn->getData('*', $table_name, $where_col_name."='".$where_col_value."' and status='1'");
			if($check!='No data'){
				$this->db->where($where_col_name, $where_col_value);
				//----------Update Data ------------------------
				$this->db->update($table_name, $dataArray);
				return 'Deleted';
			}
			else{
				return "No Record";
			}
		}

		function deleteDataP($table_name, $where_col_name, $where_col_value){			
			$check = $this->tfn->getNumrows('*', $table_name, $where_col_name."='".$where_col_value."'");
			if($check!='No Data'){
				$this->db->where($where_col_name, $where_col_value);
				//----------Update Data ------------------------
				$res = $this->db->delete($table_name);
				return $res;
			}
			else{
				return "No Record";
			}
		}

		function deleteDataWhereArray($table_name, $whereArray){
			$dataArray['status']='deleted';
			$check = $this->tfn->getData('*', $table_name, $whereArray);
			if($check!='No Data'){
				$this->db->where($whereArray);
				//----------Update Data ------------------------
				$res = $this->db->update($table_name, $dataArray);
				return $res;
			}
			else{
				return "No Record";
			}
		}


		//--------------------Update Record -------------------------------------------------------------
		function updateDataArray($dataArray, $table_name, $update_col_name, $update_col_val, $whereArray, $user_id){
			//------------------------Check Table exists or not----
			if ($this->db->table_exists($table_name) ){
				$fields = $this->db->list_fields($table_name);
				$creatFields = array();
				$creatFieldstrue='false';
				foreach ($dataArray as $key => $value) {					
					//--------------Check Fields in table exists or not--
					if(!in_array($key, $fields)){
						$creatFieldstrue='true';
						$creatFields[$key] =array('type' => 'VARCHAR', 'constraint' => '255', 'null' => TRUE); 
					}
				}
				//------------Add Columns into existing table--------
				if($creatFieldstrue==='true'){
					$this->load->dbforge();
				 	$this->dbforge->add_column($table_name, $creatFields);
				}
				if(!in_array($update_col_name, $fields)){
					$creatFields[$update_col_name] =array('type' => 'VARCHAR', 'constraint' => '255', 'null' => TRUE);
					$this->load->dbforge();
				 	$this->dbforge->add_column($table_name, $creatFields);
				}
			}
			else{ //---------------Create New Table With New Columns-----------
				$this->load->dbforge();
				$creatFields = array();
				foreach ($dataArray as $key => $value) {
					$creatFields[$key] =array('type' => 'VARCHAR', 'constraint' => '255', 'null' => TRUE);
				}
				$creatFields[$update_col_name] =array('type' => 'VARCHAR', 'constraint' => '255', 'null' => TRUE);
				$this->dbforge->add_field($creatFields);
				$this->dbforge->add_key('id', TRUE);
				$this->dbforge->create_table($table_name);
			}
			//-----Get Old Data form Database-------------
			//echo $where_col_value;
			$fetchData = $this->tfn->getData('*', $table_name, "".$where_col_name."='".$where_col_value."' " , $order_by_name = '', $order_by = '');
			$oldData=$fetchData[0];
			$oldId = $oldData['id'];
			
			//------------Unset Primary Column-----------
			unset($oldData['id']); 
			$oldData[$update_col_name]=$update_col_val;
			//-----------Save old value as old record---
			$this->tfn->insertData($oldData, $table_name); 
			//---------Get Last inserted id------------
			$lastInsertId = $this->db->insert_id();
			//----------Now Finaly Update Record--------------
			$this->db->where($whereArray);
			$res = $this->db->update($table_name, $dataArray);

			//---------Insert Update History----------------
			
			$updateDetails = array();
			$updateDetails['table_name'] = $table_name;
			$updateDetails['edit_date'] = date('Y-m-d');
			$updateDetails['edit_by'] = $user_id;
			$updateDetails['status'] = 'unread';
			$updateDetails['row_id'] = $oldId;
			$updateDetails['save_rc_in_id'] = $lastInsertId;
			$this->tfn->insertData($updateDetails, 'edit_details');
			//----------Return response----------------------------
			if($res){
				return 'update';
			}
			else{
				return $res;
			}
			//---------------End of Update Function----------------
		}

		//---Get lastInsertId 
		public function lastInsertId()
		{
			return $this->db->insert_id();
		}


		public function emptyTable($tableName)
		{
			$this->db->empty_table($tableName);
		}


		public function insert($data = array()){
	        $insert = $this->db->insert_batch('uploaded_img',$data);
	        return $insert?true:false;
	    }


	    public function getRows($id = ''){
	        $this->db->select('id,file_name,uploaded_on');
	        $this->db->from('files');
	        if($id){
	            $this->db->where('id',$id);
	            $query = $this->db->get();
	            $result = $query->row_array();
	        }else{
	            $this->db->order_by('uploaded_on','desc');
	            $query = $this->db->get();
	            $result = $query->result_array();
	        }
	        return !empty($result)?$result:false;
	    }

	    public function manualQuery($qry)
	    {

	    	$query = $this->db->query($qry);
	    	if($query -> num_rows()>=1){
				return $query->result_array();
			}
			else{
				//----------If No Data Into Tabel or Fetch error------
				return 'No data';
			}


	    	
	    }

	    public function listAllTables()
	    {
	    	$query = $this->db->query('SHOW TABLES FROM `'.$this->db->database.'`');
	    	if($query -> num_rows()>=1){
				
				$result = $query->result();
				//--------------Return Data as Associative Array----
				return $row = json_decode(json_encode($result), true);
			}
			else{
				//----------If No Data Into Tabel or Fetch error------
				return 'No data';
			}
	    	
	    }

		
	}
?>