<?php
// Restricted page directly access 
defined('BASEPATH') OR exit('No direct script access allowed');

class API extends MY_Controller {

    // Get users using API
	public function get()
	{   
        // Define content type
        header('Content-Type: application/json');
        $api_key = $this->input->get('key');

        // Check if where condition requested
        if($this->input->get('where')){
            $whereCnd = $this->input->get('where')." AND status = 1";
        }
        else{
            $whereCnd = "status = 1";
        }

        // Check API key if blank
        if($api_key == ''){
            $error['status'] = 403;
            $error['message'] = "Invalid api key";
            return $this->output
                    ->set_content_type('application/json')
                    ->set_status_header(403) // Return status
                    ->set_output(json_encode(array($error)));
        }
        else{  // if api key not blank
            // Get users data from database          
            $userInfo = $this->tfn->getData('*', 'users', "api_key = '".$api_key."' ");
            // Check data fetch or not
            if($userInfo !='No data' && $userInfo !='Table not exists'){
                // Get module data according slug
                $moduleInfo = $this->tfn->getData('*', 'modules', "slug = '".$this->input->get('module_slug')."' AND status = 1");
                // If module data fetch from database
                if($moduleInfo !='No data' && $moduleInfo !='Table not exists'){
                    $getUserPermission = $this->tfn->getData('*', 'user_permissions', "user_id = '".$userInfo[0]['user_id']."' AND status = 1 AND module_id = '".$moduleInfo[0]['id']."' ");
                    // Check user permission
                    if($getUserPermission !='No data' && $getUserPermission !='Table not exists'){
                        if($getUserPermission[0]['show_items'] == 1){
                            // Replace white space to underscore
                            $table_name = str_replace(' ', '_', $moduleInfo[0]['module_name']).'_'.$moduleInfo[0]['menu_id'];
                            // Get require data from database
                            $requireData = $this->tfn->getData('*', $table_name, $whereCnd);
                            $data['status'] = 200;
                            $data['message'] = "Please find the dataset";
                            $data['dataset'] = $requireData;
                            // Return json data to api
                            return $this->output
                                    ->set_content_type('application/json')
                                    ->set_status_header(200) // Return status
                                    ->set_output(json_encode(array($data)));
                        }
                        else{
                            // Return if user don't have permission
                            $error['status'] = 404;
                            $error['message'] = "You don't have permission to access this module.";
                            return $this->output
                                    ->set_content_type('application/json')
                                    ->set_status_header(404) // Return status
                                    ->set_output(json_encode(array($error)));
                        }
                    }
                    else{
                        // Return if user don't have permission
                        $error['status'] = 404;
                        $error['message'] = "You don't have permission to access this module.";
                        return $this->output
                                ->set_content_type('application/json')
                                ->set_status_header(404) // Return status
                                ->set_output(json_encode(array($error)));
                    }
                }
                else{
                    // Return if user don't have permission
                    $error['status'] = 404;
                    $error['message'] = "You don't have permission to access this module.";
                    return $this->output
                            ->set_content_type('application/json')
                            ->set_status_header(404) // Return status
                            ->set_output(json_encode(array($error)));
                }

                

                
            }
            else{
                // Return if api key is invalid
                $error['status'] = 403;
                $error['message'] = "Invalid api key";
                return $this->output
                        ->set_content_type('application/json')
                        ->set_status_header(403) // Return status
                        ->set_output(json_encode(array($error)));
            }
        }
	}

}

/* End of file API.php */
/* Location: ./application/controllers/API.php */


    