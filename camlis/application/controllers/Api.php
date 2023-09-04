<?php
defined('BASEPATH') or die('Access denied!');
class Api extends CI_Controller {
	public function __construct() {
        parent::__construct();					
		$this->data['cur_main_page'] = 'api';
        $this->load->model(array('gazetteer_model','pidgenerator_model'));
	}

	public function get_pid() {
        $return = array();
        /**
         * Status Code: 
         *   0 : no pararmeter
         *   1 : no result found
         *   2 : result found
         */
         if(!$this->input->get('code')){
             $return['status_code']  = 0;
             $return['msg']          = 'No parameter';
         }else{
             $pid  				= $this->input->get('code');
             $result 			= $this->pidgenerator_model->search($pid);
             $data['result']		= $result;
             if(count($result) == 0){
                 $return['status_code']  = 1;
                 $return['msg']          = 'No pid found';
             }else{
                $user 	= array();
                 if($result[0]['entryBy'] !== null){
                    $user 	= $this->pidgenerator_model->getUser($result[0]['entryBy']);
                 }
                 $return['status_code']  = 2;
                 $return['msg']          = 'Result found';
                 //$return['pid']          = $result;
                 $return['result']       = array(
                     'qrcode'           => base_url().'assets/plugins/qrcode/img/'.$pid.'.png'
                 );
             }
             if(!empty($user[0]["province_name"])){
                $return['result']['province'] = $user[0]["province_name"];
             }
             if(!empty($user[0]["fullname"])){
                $return['result']['sample_collector'] = $user[0]["fullname"];
             }
             if(!empty($user[0]["phone"])){
                $return['result']['phone_numer'] = $user[0]["phone"];
             }
             if(!empty($user[0]["location"])){
                $return['result']['location'] = $user[0]["location"];
             }
         }
 
         header('Content-Type: application/json');
         echo json_encode($return);
     }
}