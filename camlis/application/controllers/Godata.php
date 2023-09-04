<?php
defined('BASEPATH') or die('Access denied!');
class Godata extends MY_Controller {
	public function __construct() {
        parent::__construct();
		$this->data['token'] = '';
		$this->load->helper('form');
		$this->load->helper('url');
		$this->load->library('session');
		$this->load->library('restservice');
		$this->app_language->load('login');	
		$this->load->model(array('godata_model'));

    }
	public $token = '';
	
	public function index() {
		$this->load->view('godata/gd-login');
	}
	public function login(){
		$this->load->view('godata/gd-login');
	}
	public function dashboard(){
		if(!$this->session->userdata("gd_username")){
			redirect("godata");
		}
		$data['data'] 							= array();
		$listOutbreak 							= $this->godata_model->getOutbreak();
		$this->data["listOutbreak"] 			= $listOutbreak;
		foreach($listOutbreak as $item){
			$this->data["classification"][$item->id] 	= $this->godata_model->getClassification($item->id);
			$this->data["nationality"][$item->id] 		= $this->godata_model->getNationality($item->id);
			$this->data["location"][$item->id]			= $this->godata_model->getLocation($item->id);
			$this->data["highRiskContact"][$item->id] 	= $this->godata_model->getHighRiskContact($item->id);
		}
		$this->load->view('godata/gd-dashboard',$this->data);
		/*
		$currentTime = time();
		if($currentTime - $this->session->userdata('gd_timestart') >= 600){
			// request a new token
			$result   		= $thi	= $this->session->userdata('gd_token');
		$data['data'] 				= array();
		$listOutbreak 				= $this->restservice->getOutbreak($token);
		
		$this->data["listOutbreak"] = $listOutbreak;
		// Check if token is rejected because of multiple login
		// This happen when you use same u&p for multiple login
		if(isset($listOutbreak['error'])){			
			redirect("godata");
		}
		$count 						= 0;

		// only two outbreak were displayed
		$list = array('8d9a7514-84a1-41ee-a527-3b4532f5d2b7','747494dc-fdbc-4c68-96c4-7f8ad79f896f');

		//$data['data']['token'] = $token;
		foreach($listOutbreak as $item){

			if(in_array($item['id'] , $list)){
				$data['data'][$count]["name"] 					= $item['name'];
				$data['data'][$count]["id"]   					= $item['id'];

				$groupClassification 							= $this->restservice->getNumberCasesByClassification($token , $item['id']);
				$sample 										= $this->restservice->getNumberOfSample($token , $item['id']);
				$data['data'][$count]["groupclassification"] 	= $groupClassification;
				
				$data['data'][$count]["numberSample"] 			= $sample["count"];

				$count++;
			}
		}
		$this->load->view('godata/gd-dashboard',$data);

	//	$result = $this->restservice->getCasesOfOutbreak($token , $this->outbreak_id);
	//	print_r($result);
		*/
	}

	public function doLogin(){
		$this->load->library('form_validation');
		$this->form_validation->set_rules('email', 'Email', 'trim|required');
		$this->form_validation->set_rules('password', 'Password', 'trim|required');
		if ($this->form_validation->run() === TRUE){			
			$username = $this->input->post("email",true);
			$password = $this->input->post("password",true);
			$result   = $this->restservice->login($username , $password);

			if(isset($result['access_token'])){
				//$this->token 	= $result['access_token'];
				$this->session->set_userdata('gd_username',$username);
				$this->session->set_userdata('gd_password',$password);
				$this->session->set_userdata('gd_token', $result['access_token']);
				$this->session->set_userdata('gd_timestart',time());
				redirect("godata/dashboard");
			}else{
				echo "Username & password incorrect...!";
			}
		}else{
			echo "Fields required....!!!!!";			
		}		
		redirect("godata");
	}
	function signout(){
		$this->aauth->logout();
		$this->session->unset_userdata('gd_username');
		$this->session->unset_userdata('gd_password');
		$this->session->unset_userdata('gd_token');
		//redirect($this->app_language->app_lang().'/godata/login');
		redirect("godata");
	}

	public function getNumberCasesByClassification(){
		$startDate 		= $this->input->post("startDate");
		$endDate   		= $this->input->post("endDate");
		$outbreak_id 	= $this->input->post("outbreak_id");
		$ob_id 			= $this->input->post("ob_id"); // id from our database	
		// check if Token Expire
		$currentTime 	= time();	
		if($currentTime - $this->session->userdata('gd_timestart') >= 600){
			// request a new token
			$result   	= $this->restservice->login($this->session->userdata('gd_username') , $this->session->userdata('gd_password'));
			$this->session->set_userdata('gd_token', $result['access_token']);
			$this->session->set_userdata('gd_timestart',time());
		}
		$token 			= $this->session->userdata('gd_token');		
		$mData 			= $this->restservice->getClassWithGender($token , $outbreak_id, "m",$startDate, $endDate); 
		$fData 			= $this->restservice->getClassWithGender($token , $outbreak_id, 'f',$startDate, $endDate);

		if($mData == null && $fData == null){
			$status = false;
			$msg = 'Could not retrieve data from GoData';
		}else{
			// update the data to database
			$updateData 	= array();
			$currentDate 	= date('Y-m-d H:i:s');
			$index 			= 0;
			$total 			= 0;
			foreach($mData['classification'] as $key => $value){
				
				$updateData[$index]["outbreakID"] 				= $ob_id;
				$updateData[$index]["classKey"] 				= $key;
				$updateData[$index]["label"] 					= $key;
				$mCount 	= empty($mData['classification'][$key]['count']) ? 0 : $mData['classification'][$key]['count'];
				$updateData[$index]["maleCount"] 				= $mCount;
				
				$fCount = empty($fData['classification'][$key]['count']) ? 0 : $fData['classification'][$key]['count'];
				$updateData[$index]["femaleCount"] 				= $fCount;
				// discharge exist only in Covid19
				if($ob_id == 1){
					$mDischarge 									= $this->restservice->getDischargedByClass($token , $outbreak_id , $key , "m",$startDate, $endDate);
					$fDischarge 									= $this->restservice->getDischargedByClass($token , $outbreak_id , $key , "f",$startDate, $endDate);
					$updateData[$index]["dischargedMaleCount"] 		= $mDischarge['count'];
					$updateData[$index]["dischargedFemaleCount"] 	= $fDischarge['count'];
				}else{
					$updateData[$index]["dischargedMaleCount"] 		= null;
					$updateData[$index]["dischargedFemaleCount"] 	= null;
				}				
				$updateData[$index]["lastUpdate"] 				= $currentDate;				
				$total 											= $mCount + $fCount;
				$updateData[$index]["total"] 					= $total;
				$updateData[$index]["status"] 					= true;
				$index++;
			}
			$status = true;
			$msg = "Data Retrieval successful";			
		}
		header('Content-Type: application/json');
		echo json_encode(array('status' => $status, 'msg' => $msg, 'data' => $updateData ));
	}
	/**
	 * Get Classification of the outbreak
	 * AJAX
	 */

	public function getClassification(){
		$outbreak_id 	= $this->input->post("outbreak_id");	
		$ob_id 			= $this->input->post("ob_id"); // id from our database	
		$currentTime 	= time();
		$status 		= false;
		$msg 			= '';
		if($currentTime - $this->session->userdata('gd_timestart') >= 600){
			// request a new token
			$result   	= $this->restservice->login($this->session->userdata('gd_username') , $this->session->userdata('gd_password'));
			$this->session->set_userdata('gd_token', $result['access_token']);
			$this->session->set_userdata('gd_timestart',time());
		}

		$token 			= $this->session->userdata('gd_token');		
		$mData 			= $this->restservice->getClassWithGender($token , $outbreak_id, "m"); 
		$fData 			= $this->restservice->getClassWithGender($token , $outbreak_id, 'f'); 		

		if($mData == null && $fData == null){
			$status = false;
			$msg = 'Could not retrieve data from GoData';
		}else{
			// update the data to database
			$updateData 	= array();
			$currentDate 	= date('Y-m-d H:i:s');
			$index 			= 0;
			$total 			= 0;
			foreach($mData['classification'] as $key => $value){
				
				$updateData[$index]["outbreakID"] 				= $ob_id;
				$updateData[$index]["classKey"] 				= $key;
				$updateData[$index]["label"] 					= $key;
				$mCount 	= empty($mData['classification'][$key]['count']) ? 0 : $mData['classification'][$key]['count'];
				$updateData[$index]["maleCount"] 				= $mCount;
				
				$fCount = empty($fData['classification'][$key]['count']) ? 0 : $fData['classification'][$key]['count'];
				$updateData[$index]["femaleCount"] 				= $fCount;
				// discharge exist only in Covid19
				if($ob_id == 1){
					$mDischarge 									= $this->restservice->getDischargedByClass($token , $outbreak_id , $key , "m");
					$fDischarge 									= $this->restservice->getDischargedByClass($token , $outbreak_id , $key , "f");
					$updateData[$index]["dischargedMaleCount"] 		= $mDischarge['count'];
					$updateData[$index]["dischargedFemaleCount"] 	= $fDischarge['count'];
				}else{
					$updateData[$index]["dischargedMaleCount"] 		= null;
					$updateData[$index]["dischargedFemaleCount"] 	= null;
				}				
				$updateData[$index]["lastUpdate"] 				= $currentDate;				
				$total 											= $mCount + $fCount;
				$updateData[$index]["total"] 					= $total;
				$updateData[$index]["status"] 					= true;
				$index++;
			}
			if($this->godata_model->updateClassification($ob_id,$updateData)){
				$status = true;
				$msg = "Sync Successful";
			}else{
				$status = false;
				$msg = "Could not insert into db";
			}
		}
		header('Content-Type: application/json');
		echo json_encode(array('status' => $status, 'msg' => $msg, 'data' => $updateData ));
	}

	public function getGenderByLocation(){
		$outbreak_id 	= $this->input->post("outbreak_id");
		$ob_id 			= $this->input->post("ob_id"); // id from our database		
		$currentTime 	= time();
		$status 		= false;
		$msg 			= '';
		if($currentTime - $this->session->userdata('gd_timestart') >= 600){
			// request a new token
			$result   	= $this->restservice->login($this->session->userdata('gd_username') , $this->session->userdata('gd_password'));
			$this->session->set_userdata('gd_token', $result['access_token']);
			$this->session->set_userdata('gd_timestart',time());
		}
		$token 			= $this->session->userdata('gd_token');	
		// Province from GoData
		$province = array(
			'1' => 'Banteay Meanchey',
			'2' => 'Battambang',
			'3' => 'Kampong Cham',
			'4' => 'Kampong Chhnang',
			'5' => 'Kampong Speu',
			'6' => 'Kampong Thom',
			'7' => 'Kampot',
			'8' => 'Kandal',
			'9' => 'Koh Kong',
			'10' => 'Kratie',
			'11' => 'Mondul Kiri',
			'12' => 'Phnom Penh',
			'13' => 'Preah Vihear',
			'14' => 'Prey Veng',
			'15' => 'Pursat',
			'16' => 'Ratanak Kiri',
			'17' => 'Siem Reap',
			'18' => 'Preah Sihanouk',
			'19' => 'Takeo',
			'20' => 'Svay Rieng',
			'21' => 'Takeo',
			'22' => 'Oddar Meanchey',
			'23' => 'Kep',
			'24' => 'Pailin',
			'25' => 'Tbong Khmum'
		);
		// outbreak19 Nationational got stuck because of large data 
		// so we 
		if($ob_id == 2){
			//$retrieveData 	= $this->restservice->getLocations($token , $outbreak_id);
			$updateData 	= array();
				$currentDate 	= date('Y-m-d H:i:s');
				$index 			= 0;
				for($i = 1  ; $i < count($province); $i++){
				//foreach($retrieveData['locations'] as $key => $value){
					$mCount 							= $this->restservice->getGenderByLocation($token , $outbreak_id, $i , "m");
					$fCount 							= $this->restservice->getGenderByLocation($token , $outbreak_id, $i , "f");
					$updateData[$index]["id"] 			= $i;
					$updateData[$index]["outbreakID"] 	= $ob_id;
					$updateData[$index]["name"] 		= $province[$i];
					$updateData[$index]["maleCount"] 	= $mCount['count'];
					$updateData[$index]["femaleCount"] 	= $fCount['count'];
					$updateData[$index]["geolocation"] 	= null;
					$updateData[$index]["casesCount"] 	= $mCount['count']+$fCount['count'];
					$updateData[$index]["lastUpdate"] 	= $currentDate;
					$updateData[$index]["status"] 		= true;
					$index++;
				}
				if($this->godata_model->updateLocation($ob_id,$updateData)){
					$status = true;
					$msg 	= "Sync Successful";
				}else{
					$status = false;
					$msg = "Could not insert into db";
				}
				header('Content-Type: application/json');
				echo json_encode(array('status' => $status, 'msg' => $msg, 'data' => $updateData));
		}else{
			$retrieveData 	= $this->restservice->getLocations($token , $outbreak_id);
			if(empty($retrieveData)){
				$status = false;
				$msg 	= 'Could not retrieve data from GoData';
			}else{
				$updateData 	= array();
				$currentDate 	= date('Y-m-d H:i:s');
				$index 			= 0;			
				
				foreach($retrieveData['locations'] as $key => $value){
					$mCount 							= $this->restservice->getGenderByLocation($token , $outbreak_id, $value['location']['id'] , "m");
					$fCount 							= $this->restservice->getGenderByLocation($token , $outbreak_id, $value['location']['id'] , "f");
					$updateData[$index]["id"] 			= $value['location']['id'];
					$updateData[$index]["outbreakID"] 	= $ob_id;
					$updateData[$index]["name"] 		= $value['location']['name'];
					$updateData[$index]["maleCount"] 	= $mCount['count'];
					$updateData[$index]["femaleCount"] 	= $fCount['count'];
					$updateData[$index]["geolocation"] 	= null;
					$updateData[$index]["casesCount"] 	= $value['casesCount'];
					$updateData[$index]["lastUpdate"] 	= $currentDate;
					$updateData[$index]["status"] 		= true;
					$index++;
				}
				if($this->godata_model->updateLocation($ob_id,$updateData)){
					$status = true;
					$msg = "Sync Successful";
				}else{
					$status = false;
					$msg = "Could not insert into db";
				}
			}
			header('Content-Type: application/json');
			echo json_encode(array('status' => $status, 'msg' => $msg, 'data' => $updateData , 'retrieve' => $retrieveData));
		}
	}

	// 
	function getHigtRisk(){
		$outbreak_id 	= $this->input->post("outbreak_id");
		$ob_id 			= $this->input->post("ob_id"); // id from our database
		$currentTime 	= time();
		$status 		= false;
		$msg 			= '';
		if($currentTime - $this->session->userdata('gd_timestart') >= 600){
			// request a new token
			$result   	= $this->restservice->login($this->session->userdata('gd_username') , $this->session->userdata('gd_password'));
			$this->session->set_userdata('gd_token', $result['access_token']);
			$this->session->set_userdata('gd_timestart',time());
		}
		$token 	= $this->session->userdata('gd_token');	
		$mData 	= $this->restservice->getHighRisk($token , $outbreak_id, 'm');
		$fData 	= $this->restservice->getHighRisk($token , $outbreak_id, 'f');
		// true = followup
		$mfuData 	= $this->restservice->getHighRisk($token , $outbreak_id, 'm', true);
		$ffuData 	= $this->restservice->getHighRisk($token , $outbreak_id, 'f', true);
		if(empty($mData) && (empty($fData))){
			$status = false;
			$msg 	= 'Could not retrieve data from GoData';
		}else{
			$updateData 	= array();
			$currentDate 	= date('Y-m-d H:i:s');
			$updateData["outbreakID"] 			= $ob_id;
			$updateData["maleCount"] 			= $mData['count'];
			$updateData["femaleCount"] 			= $fData['count'];
			$updateData["activeMaleCount"] 		= $mfuData['count'];
			$updateData["activeFemaleCount"] 	= $ffuData['count'];
			$updateData["lastUpdate"] 			= $currentDate;
			$updateData["status"]				= true;
			if($this->godata_model->updateHighRish($ob_id,$updateData)){
				$status = true;
				$msg = "Sync Successful";
			}else{
				$status = false;
				$msg = "Could not insert into db";
			}
			header('Content-Type: application/json');
			echo json_encode(array('status' => $status, 'msg' => $msg, 'data' => $updateData));
		}
	}
	function getClassByLab(){
		$outbreak_id 	= $this->input->post("outbreak_id");
		$lab_name		= $this->input->post("lab_name");
		$currentTime 	= time();
		$status 		= false;
		$msg 			= '';
		if($currentTime - $this->session->userdata('gd_timestart') >= 600){
			// request a new token
			$result   	= $this->restservice->login($this->session->userdata('gd_username') , $this->session->userdata('gd_password'));
			$this->session->set_userdata('gd_token', $result['access_token']);
			$this->session->set_userdata('gd_timestart',time());
		}		
		$token 			= $this->session->userdata('gd_token');	
		$updateData 	= array();
		$currentDate 	= date('Y-m-d H:i:s');
		$index 			= 0;
		$total			= 0;
		$CLASSIFICATION_KEY = unserialize (CLASSIFICATION_KEY);
		
		foreach($CLASSIFICATION_KEY as $key => $value){
			$data 	= $this->restservice->getClassByLab($token , $outbreak_id , $lab_name, $key);	
			if(empty($data) || empty($data['count'])){
				$total = $data['count'];
				if(isset($data['error'])){
					$total = $data['error'];
				}
			}else{
				$total = $data['count'];
			}
			if($total !== 0){
				if($total == null) $total = "No response from server";
				$updateData[$index]["label"] 		= $value;
				$updateData[$index]["maleCount"] 	= "-";
				$updateData[$index]["femaleCount"] 	= "-";
				$updateData[$index]["total"] 		= $total;
				$updateData[$index]["lastUpdate"] 	= $currentDate;
				$index++;
			}
			
		}

		header('Content-Type: application/json');
		echo json_encode(array('status' => true, 'msg' => "Data Retrieve Done", 'data' => $updateData));
	}
	function getNationality(){
		$outbreak_id 	= $this->input->post("outbreak_id");
		
		$ob_id 			= $this->input->post("ob_id"); // id from our database
		$currentTime 	= time();
		$status 		= false;
		$msg 			= '';
		if($currentTime - $this->session->userdata('gd_timestart') >= 600){
			// request a new token
			$result   	= $this->restservice->login($this->session->userdata('gd_username') , $this->session->userdata('gd_password'));
			$this->session->set_userdata('gd_token', $result['access_token']);
			$this->session->set_userdata('gd_timestart',time());
		}		
		$token 			= $this->session->userdata('gd_token');	
		$updateData 	= array();
		$currentDate 	= date('Y-m-d H:i:s');
		$index 			= 0;
		$total			= 0;
		$nbMale			= 0;
		$nbFemale		= 0;

		$retrieveData 	= $this->restservice->getNationality($token , $outbreak_id);	
		
		if(empty($retrieveData)){
			$status = false;
			$msg 	= 'Could not retrieve data from GoData';
		}else{
			$updateData = array();		
			foreach($retrieveData as $key => $value){
				$person 		= $value["person"];
				$gender 		= $person['gender'] == 'LNG_REFERENCE_DATA_CATEGORY_GENDER_MALE' ? "m" : "f";
				$nationality 	= $person['questionnaireAnswers']['nationality'];
				$nat			= $nationality['0']['value'];
				// natioanality exist in array count gender, if not add new {nationality,mCount,fCount}
				if(count($updateData) == 0){
					if($gender == 'm'){
						$nbMale = 1;
					}else{
						$nbFemale = 1;
					}
					$updateData[$index]["outbreakID"] 	= $ob_id;
					$updateData[$index]["name"] 		= $nat;
					$updateData[$index]["maleCount"] 	= $nbMale;
					$updateData[$index]["femaleCount"] 	= $nbFemale;
					$updateData[$index]["total"] 		= $nbMale+$nbFemale;
					$updateData[$index]["lastUpdate"] 	= $currentDate;
					$updateData[$index]["status"] 		= true;
					$index++;
				}else{
					$search = false;
					for($i = 0 ; $i < count($updateData) ; $i++){
						if($updateData[$i]['name'] == $nat){
							if($gender == 'm'){
								$updateData[$i]['maleCount'] ++;
							}else{
								$updateData[$i]['femaleCount'] ++;
							}
							$search = true;
							break;
						}
					}
					// if not found, add new
					if(!$search){
						if($gender == 'm'){
							$nbMale = 1;
						}else{
							$nbFemale = 1;
						}
						$updateData[$index]["outbreakID"] 	= $ob_id;
						$updateData[$index]["name"] 		= $nat;
						$updateData[$index]["maleCount"] 	= $nbMale;
						$updateData[$index]["femaleCount"] 	= $nbFemale;
						$updateData[$index]["total"] 		= $nbMale+$nbFemale;
						$updateData[$index]["lastUpdate"] 	= $currentDate;
						$updateData[$index]["status"] 		= true;
						$index++;
					}
				}
			}
			if($this->godata_model->updateNationality($ob_id,$updateData)){
				$status = true;
				$msg = "Sync Successful";
			}else{
				$status = false;
				$msg = "Could not insert into db";
			}
		}

		header('Content-Type: application/json');
		echo json_encode(array('status' => $status, 'msg' => $msg, 'data' => $updateData));
	}

	//
	public function getGenderByLocationAndClass(){
		$outbreak_id 	= $this->input->post("outbreak_id");
		$ob_id 			= $this->input->post("ob_id"); // id from our database		
		$currentTime 	= time();
		$status 		= false;
		$msg 			= '';
		if($currentTime - $this->session->userdata('gd_timestart') >= 600){
			// request a new token
			$result   	= $this->restservice->login($this->session->userdata('gd_username') , $this->session->userdata('gd_password'));
			$this->session->set_userdata('gd_token', $result['access_token']);
			$this->session->set_userdata('gd_timestart',time());
		}
		$token 			= $this->session->userdata('gd_token');	
		// Province from GoData
		$province = array(
			'1' => 'Banteay Meanchey',
			'2' => 'Battambang',
			'3' => 'Kampong Cham',
			'4' => 'Kampong Chhnang',
			'5' => 'Kampong Speu',
			'6' => 'Kampong Thom',
			'7' => 'Kampot',
			'8' => 'Kandal',
			'9' => 'Koh Kong',
			'10' => 'Kratie',
			'11' => 'Mondul Kiri',
			'12' => 'Phnom Penh',
			'13' => 'Preah Vihear',
			'14' => 'Prey Veng',
			'15' => 'Pursat',
			'16' => 'Ratanak Kiri',
			'17' => 'Siem Reap',
			'18' => 'Preah Sihanouk',
			'19' => 'Takeo',
			'20' => 'Svay Rieng',
			'21' => 'Takeo',
			'22' => 'Oddar Meanchey',
			'23' => 'Kep',
			'24' => 'Pailin',
			'25' => 'Tbong Khmum'
		);
		// outbreak19 Nationational got stuck because of large data 
		// so we 
		if($ob_id == 2){
			//$retrieveData 	= $this->restservice->getLocations($token , $outbreak_id);
			$updateData 	= array();
				$currentDate 	= date('Y-m-d H:i:s');
				$index 			= 0;
				$CLASSIFICATION_KEY = unserialize (CLASSIFICATION_KEY);
				for($i = 1  ; $i < count($province); $i++){
				/*
					foreach($CLASSIFICATION_KEY as $key => $value){
						$data 	= $this->restservice->getClassByLab($token , $outbreak_id , $lab_name, $key);
					}
				*/	
				//foreach($retrieveData['locations'] as $key => $value){
					$mCount 							= $this->restservice->getGenderByLocationAndClass($token , $outbreak_id, $i , "m");
					$fCount 							= $this->restservice->getGenderByLocationAndClass($token , $outbreak_id, $i , "f");
					$updateData[$index]["id"] 			= $i;
					$updateData[$index]["outbreakID"] 	= $ob_id;
					$updateData[$index]["name"] 		= $province[$i];
					$updateData[$index]["maleCount"] 	= $mCount['count'];
					$updateData[$index]["femaleCount"] 	= $fCount['count'];
					$updateData[$index]["geolocation"] 	= null;
					$updateData[$index]["casesCount"] 	= $mCount['count']+$fCount['count'];
					$updateData[$index]["lastUpdate"] 	= $currentDate;
					$updateData[$index]["status"] 		= true;
					$index++;
				}
				if($this->godata_model->updateLocation($ob_id,$updateData)){
					$status = true;
					$msg 	= "Sync Successful";
				}else{
					$status = false;
					$msg = "Could not insert into db";
				}
				header('Content-Type: application/json');
				echo json_encode(array('status' => $status, 'msg' => $msg, 'data' => $updateData));
		}else{
			$retrieveData 	= $this->restservice->getLocations($token , $outbreak_id);
			if(empty($retrieveData)){
				$status = false;
				$msg 	= 'Could not retrieve data from GoData';
			}else{
				$updateData 	= array();
				$currentDate 	= date('Y-m-d H:i:s');
				$index 			= 0;			
				
				foreach($retrieveData['locations'] as $key => $value){
					$mCount 							= $this->restservice->getGenderByLocation($token , $outbreak_id, $value['location']['id'] , "m");
					$fCount 							= $this->restservice->getGenderByLocation($token , $outbreak_id, $value['location']['id'] , "f");
					$updateData[$index]["id"] 			= $value['location']['id'];
					$updateData[$index]["outbreakID"] 	= $ob_id;
					$updateData[$index]["name"] 		= $value['location']['name'];
					$updateData[$index]["maleCount"] 	= $mCount['count'];
					$updateData[$index]["femaleCount"] 	= $fCount['count'];
					$updateData[$index]["geolocation"] 	= null;
					$updateData[$index]["casesCount"] 	= $value['casesCount'];
					$updateData[$index]["lastUpdate"] 	= $currentDate;
					$updateData[$index]["status"] 		= true;
					$index++;
				}
				if($this->godata_model->updateLocation($ob_id,$updateData)){
					$status = true;
					$msg = "Sync Successful";
				}else{
					$status = false;
					$msg = "Could not insert into db";
				}
			}
			header('Content-Type: application/json');
			echo json_encode(array('status' => $status, 'msg' => $msg, 'data' => $updateData , 'retrieve' => $retrieveData));
		}
	}
}