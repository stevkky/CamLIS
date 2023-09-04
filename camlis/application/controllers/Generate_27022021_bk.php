<?php
defined('BASEPATH') or die('Access denied!');
// Generate ID of patient base on format XX-MMDDYYNNN
class Generate extends MY_Controller {
	public function __construct() {
        parent::__construct();					
		$this->data['cur_main_page'] = '';
		$this->load->model(array('gazetteer_model','pidgenerator_model'));
	//	$this->load->library('phpqrcode/qrlib');
	}

	public function index() {
		$this->data['provinces']    = $this->gazetteer_model->get_province();
		$currentDate 				= date("Y-m-d");
		$this->data['result'] 		= $this->pidgenerator_model->getNumber($currentDate);
        $this->load->view("template/pages/generate_pid",$this->data);
	}
	public function pid(){
		$data 				= array();
		$province_id  		= $this->input->post('province_id');
		$numberCode			= $this->input->post('number');
		$timestamp			= date('Y-m-d H:i:s');
		if(strlen($province_id) == 1) {
			$province_id 	= "0".$province_id;
		}
		for($i = 0 ; $i < $numberCode; $i++){
			$currentDate 		= date("Y-m-d"); // 2021-01-11
			$result 			= $this->pidgenerator_model->getNumber($currentDate);
			$number 			= $result[0]["number"];
			$number++;
			if(strlen($number) == 1) $number = "000".$number;
			else if(strlen($number) == 2) $number = "00".$number;
			else if(strlen($number) == 3) $number = "0".$number;
			$cdate 				= date("ymd");
			//XX-YYMMDDNNNN
			// check if pid is exist		
			$pid 				= $province_id."-".$cdate.$number;
			$check 				= $this->pidgenerator_model->isPidExist($pid);
			if($check[0]["number"] !== 1){
				$id = $this->pidgenerator_model->add($pid,$timestamp);
				$this->load->library('phpqrcode/Qrlib');
				// Generate QRCODE
				//file path for store images
				$SERVERFILEPATH = $_SERVER['DOCUMENT_ROOT'].'/assets/plugins/qrcode/img/';

				$text 			= $pid;
				$folder 		= $SERVERFILEPATH;
				$file_name1 	= $pid.".png";
				$file_name 		= $folder.$file_name1;
				QRcode::png($text,$file_name);

				$data[$i]['status'] = 1;
				$data[$i]['pid'] 	= $pid;
				$data[$i]['qrcode'] = $file_name1;
			}
		}
		//add the header here
		header('Content-Type: application/json');
		echo json_encode($data);		
	}
	public function is_downloaded(){
		$result = array();
		$data  	= $this->input->post('data');
		for($i = 0 ; $i <= count($data) ; $i++){
			$pid = $data[$i];
			$res = $this->pidgenerator_model->update($pid);
			$result[$i]['pid'] = $pid;
			$result[$i]['status'] = $res;
		}
		header('Content-Type: application/json');
		echo json_encode($result);
	}
	// count pid
	// added 13 Jan 2021
	public function counter(){
		$this->load->view("template/pages/pid_counter",$this->data);
	}
	public function count_pid(){
		$startDate  		= $this->input->post('startDate');
		$endDate  			= $this->input->post('endDate');
		$result 			= $this->pidgenerator_model->count_pid($startDate, $endDate);
		$data["result"] 	= $result;
		header('Content-Type: application/json');
		echo json_encode($result);
	}	
}