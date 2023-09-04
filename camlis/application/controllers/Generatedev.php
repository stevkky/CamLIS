<?php
defined('BASEPATH') or die('Access denied!');
// Generate ID of patient base on format XX-MMDDYYNNN
class Generatedev extends MY_Controller {
	public function __construct() {
        parent::__construct();
		$this->data['cur_main_page'] = '';
		$this->load->model(array('gazetteer_model','pidgeneratordev_model','report_model'));
	//	$this->load->library('phpqrcode/qrlib');
		$this->load->helper('form');
		$this->load->helper('url');
		$this->load->library('session');
	}

	public function index() {
		if($this->session->has_userdata('pid_username')){
			$this->data['provinces']    = $this->gazetteer_model->get_province();
			$currentDate 				= date("Y-m-d");
			$this->data['result'] 		= $this->pidgeneratordev_model->getNumber($currentDate);
			$this->data['province_id']  = $this->session->userdata('pid_province');
			$this->load->view("template/pages/generate_pid_dev",$this->data);
		}else{
			redirect('generatedev/login');
		}
	}
	public function pid(){
		
		$data 				= array();
		$province_id  		= $this->input->post('province_id');
		$numberCode			= $this->input->post('number');
		$generate_date		= trim($this->input->post('generate_date'));
		
		if(strlen($province_id) == 1) {
			$province_id 	= "0".$province_id;
		}

		if($generate_date == "" || strlen($generate_date) == 0) {
			$currentDate = date("Y-m-d"); // 2021-01-11
			$cdate 		 = date("ymd");
			$timestamp	 = date('Y-m-d H:i:s');
		}else{
			$currentDate = $generate_date; // 2021-01-11
			$cdate 		 = date('ymd',strtotime($generate_date));
			$today		 = date('Y-m-d');
			$timestamp 	 = date('Y-m-d',strtotime($generate_date));
			if($today == $timestamp){
				$timestamp	.= " ".date('H:i:s');
			}else{
				$timestamp 	 = date('Y-m-d H:i:s',strtotime($generate_date));
			}
		}
		
		for($i = 0 ; $i < $numberCode; $i++){
			$result 		= $this->pidgeneratordev_model->getNumber($currentDate);
			$number 		= $result[0]["number"];
			$number++;
			if(strlen($number) == 1) $number = "000".$number;
			else if(strlen($number) == 2) $number = "00".$number;
			else if(strlen($number) == 3) $number = "0".$number;
			
			//XX-YYMMDDNNNN
			// check if pid is exist		
			$pid 				= $province_id."-".$cdate.$number;
			$check 				= $this->pidgeneratordev_model->isPidExist($pid);
			if($check[0]["number"] !== 1){
				$user_id = 0;
				if($this->session->has_userdata('pid_id')) $user_id = $this->session->userdata('pid_id');
				$id = $this->pidgeneratordev_model->add($pid,$user_id,$timestamp);
				$this->load->library('phpqrcode/Qrlib');
				// Generate QRCODE
				//file path for store images
				$SERVERFILEPATH = $_SERVER['DOCUMENT_ROOT'].'/assets/plugins/qrcode/img_dev/';
				// get info of the user
				// PID user
				if($user_id == 0){
					$text = 'user=pid,pid='.$pid;
				}else{
					$user_info = $this->pidgeneratordev_model->getUser($user_id);
					$text = 'name='.$user_info[0]['fullname'].',phone='.$user_info[0]['phone'].',location='.$user_info[0]['location'].',pid='.$pid;
				}
				$text 			= $pid;
				$folder 		= $SERVERFILEPATH;
				$file_name1 	= $pid.".png";
				$file_name 		= $folder.$file_name1;
				QRcode::png($text,$file_name);

				$data[$i]['status'] = 1;	
				$data[$i]['pid'] 	= $pid;
				$data[$i]['qrcode'] = $file_name1;
				$data[$i]['currentDate'] 	= $currentDate;
				$data[$i]['cDate'] 	= $cdate;
			}else{
				$data[$i]['status'] 		= 2;
				$data[$i]['pid'] 			= $pid;
				$data[$i]['msg'] 			= "exist";
				$data[$i]['currentDate'] 	= $currentDate;
				$data[$i]['cDate'] 			= $cdate;
				$data[$i]['number'] 		= $result[0]["number"];
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
			$res = $this->pidgeneratordev_model->update($pid);
			$result[$i]['pid'] = $pid;
			$result[$i]['status'] = $res;
		}
		header('Content-Type: application/json');
		echo json_encode($result);
	}	
	// count pid
	// added 13 Jan 2021
	public function counter(){
		
		if($this->session->userdata('roleid') == 1){
			$this->data['users'] = $this->pidgeneratordev_model->getUsers();
			$this->load->view("template/pages/pid_counter",$this->data);
		}else{
			redirect('generatedev/login');
		}
		
	}
	public function count_pid(){
		$startDate  		= $this->input->post('startDate');
		$endDate  			= $this->input->post('endDate');
		$result 			= $this->pidgeneratordev_model->count_pid($startDate, $endDate);
		$data["result"] 	= $result;
		header('Content-Type: application/json');
		echo json_encode($result);
	}

	// added 25 Feb 2021
	public function register(){
		$this->data['provinces']    = $this->gazetteer_model->get_province();
		$this->load->view("template/pages/pid_register",$this->data);
	}
	// added 25 Feb 2021
	public function search(){
		$data 				= array();
		$pid  				= $this->input->post('pid');
		$result 			= $this->pidgeneratordev_model->search($pid);
		$status				= false;
		$data['result']		= $result;
		if(count($result) == 0){
			$status = false;
			$msg = 'PID not found';
		}else{
			$status 		= true;
			$msg 			= 'Result found';
			$data['qrcode'] = '/assets/plugins/qrcode/img_dev/'.$pid.'.png';
			$data['user'] 	= array();
			if($result[0]['entryBy'] !== null){
				$data['user'] 	= $this->pidgeneratordev_model->getUser($result[0]['entryBy']);
			}
		}
		$data["result"] 	= $result;
		header('Content-Type: application/json');
		echo json_encode(array('status' => $status, 'msg' => $msg, 'data' => $data));		
	}
	public function usernameExist(){		
		$username  	= $this->input->post('username');
		$username 	= strtolower($username);
		$result 	= $this->pidgeneratordev_model->usernameExist($username);
		if(count($result) > 0){
			$status = true;
			$msg = "Username exists";
		}else{
			$status = false;
			$msg = "Username does not exist";
		}
		header('Content-Type: application/json');
		echo json_encode(array('status' => $status, 'msg' => $msg));
	}
	public function emailExist(){		
		$email  	= $this->input->post('email');
		$email 		= strtolower($email);
		$result 	= $this->pidgeneratordev_model->emailExist($email);
		if(count($result) > 0){
			$status = true;
			$msg = "Email exists";
		}else{
			$status = false;
			$msg = "Email does not exist";
		}
		header('Content-Type: application/json');
		echo json_encode(array('status' => $status, 'msg' => $msg));
	}
	/*
	public function doRegister(){
		
	//	if($this->input->server('REQUEST_METHOD') == 'POST'){
			
			$this->load->library('form_validation');
			//$this->form_validation->set_rules('full_name', 'Full name', 'trim|required');
			//$this->form_validation->set_rules('username', 'username', 'trim|required');
			//$this->form_validation->set_rules('province', 'province', 'trim|required');			
			//$this->form_validation->set_rules('location', 'location', 'trim|required');
			//$this->form_validation->set_rules('password', 'password', 'trim|required');
			
			if ($this->form_validation->run() === TRUE){			
				$today			= date('Y-m-d H:i:s');

				$data['fullname']		= $this->input->post("full_name",true);
				$data['username']		= $this->input->post("username",true);
				$data['province_id']	= $this->input->post("province",true);
				$data['approval']		= 0; // 0 Pending , 1 Reject 2 Approve
				$data['entryDate']		= $today;
				$data['email']			= $this->input->post("email",true);
				$data['phone']			= $this->input->post("phone",true);
				$data['location']		= $this->input->post("location",true);
				$data['password']		= md5($this->input->post("password",true));
				$data['status']			= false;
				$result 				= $this->pidgeneratordev_model->addUser($data);
				if($result > 0){
					$status = true;
					$msg = "Registration successful";
					$this->data['status'] = $status;
					$this->data['msg'] 	  = $msg;
					redirect('generatedev/registration',$this->data);
				}
			}else{
				echo validation_errors(); echo form_error();
				//redirect('generatedev/register');
			}
	
	//	}else{
	//		echo "page not found";
	//	}
		
	}
	*/
	public function doRegister(){
		
		$today			= date('Y-m-d H:i:s');		
		$data['fullname']		= $this->input->post("fullname",true);
		$data['username']		= $this->input->post("username",true);
		$data['province_id']	= $this->input->post("province",true);
		$data['approval']		= 0; // 0 Pending , 1 Reject 2 Approve
		$data['entryDate']		= $today;
		$data['email']			= $this->input->post("email",true);
		$data['phone']			= $this->input->post("phone",true);
		$data['location']		= $this->input->post("location",true);
		$data['password']		= md5($this->input->post("password",true));
		$data['status']			= false;
		$usernameExist			= $this->pidgeneratordev_model->usernameExist(trim($data['username']));
		if(count($usernameExist) > 0){
			$status = false;
			$msg = "Username exist";
		}else{
			if(trim($data['email']) !== ""){
				$emailExist			= $this->pidgeneratordev_model->emailExist(strtolower($data['email']));
				if(count($emailExist) > 0){
					$status = false;
					$msg = "Email exist";
				}
			}else{
				$result 				= $this->pidgeneratordev_model->addUser($data);
				if($result > 0){
					$status = true;
					$msg = "Registration successful";
					$this->data['status'] = $status;
					$this->data['msg'] 	  = $msg;
				}else{
					$status = false;
					$msg = "Registration fail, please try again....";
				}
			}
		}
		
		header('Content-Type: application/json');
		echo json_encode(array('status' => $status, 'msg' => $msg, 'data' => $data ));
	}

	public function login(){
		$this->load->view("template/pages/pid_login_dev");
	}
	public function doLogin(){
		$this->load->library('form_validation');
		$this->form_validation->set_rules('username', 'Username', 'trim|required');
		$this->form_validation->set_rules('password', 'Password', 'trim|required');
		if ($this->form_validation->run() === TRUE){
			$username = $this->input->post("username",true);
			$password = $this->input->post("password",true);
			$pwd =  md5($password);
			//echo $username."<br />";
			//echo $pwd."<br />";
			$result   = $this->pidgeneratordev_model->login(trim($username) ,$pwd);
			//print_r($result);
			if(count($result) > 0){
				if($result[0]['approval'] == 0){
					$data['status'] 	= false;
					$data['msg'] 		= 'Your account is pending, please contact Mr. Vandra to approve your account.';
				}else if($result[0]['approval'] == 1){
					$data['status'] 	= false;
					$data['msg'] 		= 'Login fails.....';
				}else if($result[0]['approval'] == 2){
					$data['status'] 	= true;
					$this->session->set_userdata('pid_id',$result[0]['ID']);
					$this->session->set_userdata('pid_username',$username);
					$this->session->set_userdata('pid_province',$result[0]['province_id']);
					redirect("generatedev");
				}
				$this->session->set_flashdata($data);
				echo "form true";
		//		redirect("generatedev/login");
			}else{			
				$data['status'] 	= false;
				$data['msg'] 		= 'Login fails.....';
				$this->session->set_flashdata($data);
				echo "login fail";
				print_r($result);
		//		redirect("generatedev/login");
			}			
		}else{
			echo "Fields required....!!!!!";			
		}
	//	redirect("generatedev/login");
	}
	public function updateUser(){
		$status = false;
		$msg = "";
		$data = array();
		if($this->session->userdata('roleid') == 1){
			$user_id = $this->input->post("id");
			$approve = $this->input->post("approve");
			$result = $this->pidgeneratordev_model->updateUser(intval($user_id) , intval($approve));
			if($result > 0){
				$data['result'] = $result;
				$status = true;
				$msg = "Update successful";
				$data['users'] = $this->pidgeneratordev_model->getUsers();
			}else{
				$status = false;
				$msg = "Update fails.....";
			}
		}else{
			$status = false;
			$msg = "You dont have permission to perform this task...";
		}
		header('Content-Type: application/json');
		echo json_encode(array('status' => $status, 'msg' => $msg, 'data' => $data ));
	}
	public function logout(){
		$this->session->unset_userdata('pid_id');
		$this->session->unset_userdata('pid_username');
		$this->session->unset_userdata('pid_province');
		redirect("generatedev/login");
	}	
}