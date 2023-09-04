<?php
defined('BASEPATH') or die('Access denied!');
class Exceljs extends MY_Controller {
	public function __construct() {
        parent::__construct();
		$this->load->helper('form');
		$this->load->helper('url');
		$this->load->helper('util');
		$this->load->library('session');
		$this->load->model(['exceljs_model','patient_model']);
    }
	public function index() {
		$this->session->unset_userdata('laboratory');
		$this->session->unset_userdata('user');
		$this->session->sess_destroy();
		$this->load->model(['gazetteer_model','country_model','vaccine_model']);
		$this->data['countries']            = $this->country_model->get_country();
		$this->data['provinces']            = $this->gazetteer_model->get_province();
		$this->data['nationalities']        = $this->country_model->get_nationality();	
		$this->data['districts']            = $this->gazetteer_model->get_district();
		$this->data['communes']             = $this->gazetteer_model->get_commune_();
		$this->data['villages']             = $this->gazetteer_model->get_village_();		
		$this->data['vaccines']             = $this->vaccine_model->get_vaccine(); //12-07-2021
		$this->load->view('exceljs/index',$this->data);
	}
	public function save(){
		$start_time = microtime(true); 
        $data       = $this->input->post('data');
		$status 	= false;
		$msg		= "";
		$results	= array();
		$col = array(
			"no_by_day"               => 0,
			"cdc_case_no"             => 1,
			"laboratory_code"         => 2,
			"fullname"                => 3,
			"sex"                     => 4,
			"age"                     => 5,
			"nationality"             => 6,
			"phone"                   => 7,
			"date_of_sampling"        => 8,
			"date_of_result"          => 9,
			"f20_event"               => 10,
			"imported_country"        => 11,
			"date_of_onset"           => 12,
			"symptoms"                => 13,
			"positive_on"             => 14,
			"reason_for_testing"      => 15,
			"province"                => 16,
			"district"                => 17,
			"commune"                 => 18,
			"village"                 => 19,
			"province_of_detection"   => 20,
			"remark"                  => 21,
			"vaccination_status"      => 22,
			"first_vaccinated_date"   => 23,
			"second_vaccinated_date"  => 24,
			"vaccine_name"            => 25,
			"image"                   => 26,
			"sex_id"                  => 27,
			"nationality_id"          => 28,
			"reason_for_testing_id"   => 29,
			"province_code"           => 30,
			"district_code"           => 31,
			"commune_code"            => 32,
			"village_code"            => 33,
			"province_of_detection_code"   => 34,
			"vaccination_status_code" => 35,
			"vaccine_id"              => 36,
			"patient_exist"           => 37,
		);
		foreach($data as $key => $row){			
			$phone      = (trim($row[$col["phone"]]) !== "") ? str_replace(' ', '', trim($row[$col["phone"]])) : "";		
			$input = array(
				'no_by_day' 		=> trim($row[$col["no_by_day"]]),
				'case_no' 			=> trim($row[$col["cdc_case_no"]]),
				'laboratory_code' 	=> trim($row[$col["laboratory_code"]]),
				'fullname' 			=> trim($row[$col["fullname"]]),
				'sex' 				=> trim($row[$col["sex_id"]]),
				'age' 				=> trim($row[$col["age"]]),
				'nationality' 		=> empty($row[$col["nationality_id"]]) ? null : $row[$col["nationality_id"]],
				'phone' 			=> $phone,
				'date_of_sampling'  => ($row[$col["date_of_sampling"]] !== "") ? date('Y-m-d', strtotime($row[$col["date_of_sampling"]])): null,
				'date_of_result' 	=> ($row[$col["date_of_result"]] !== "") ? date('Y-m-d', strtotime($row[$col["date_of_result"]])): null,
				'f20_event' 		=> trim($row[$col["f20_event"]]),
				'imported_country'  => trim($row[$col["imported_country"]]),
				'date_of_onset' 	=> ($row[$col["date_of_onset"]] !== "") ? date('Y-m-d', strtotime($row[$col["date_of_onset"]])): null,
				'symptoms' 			=> trim($row[$col["symptoms"]]),
				'positive_on' 		=> $row[$col["positive_on"]],
				'reason_for_testing' => $row[$col["reason_for_testing_id"]],
				'province' 			=> $row[$col["province_code"]],
				'district' 			=> $row[$col["district_code"]],
				'commune' 			=> $row[$col["commune_code"]],
				'village' 			=> $row[$col["village_code"]],
				'detection_province' => $row[$col["province_of_detection_code"]],
				'remark' 			=> trim($row[$col["remark"]]),
				'vaccination_status' => $row[$col["vaccination_status_code"]],
				'first_injection_date' => ($row[$col["first_vaccinated_date"]] !== "") ? date('Y-m-d', strtotime($row[$col["first_vaccinated_date"]])): null,
				'second_injection_date' => ($row[$col["second_vaccinated_date"]] !== "") ? date('Y-m-d', strtotime($row[$col["second_vaccinated_date"]])): null,
				'vaccine' 			=> $row[$col["vaccine_id"]],
				'img_url'			=> ($row[$col["image"]] !== "") ? $row[$col["image"]] : null
			);
			// Check if laboratory phone or name exist
			$res['name'] = trim($row[$col["fullname"]]);
			$exist = ($row[$col["patient_exist"]] == 1) ? 1: 0;			
			if( $exist == 0){
				if ($this->exceljs_model->save($input)){
					$res['status'] = true;
					$res['msg'] = "success";					
				}else{
					$res['status'] = false;
					$res['msg'] = "fail";
				}
			}else{
				$res['status'] = false;
				$res['msg'] = "Patient exists";				
			}
			$results[]  = $res; 
		}
		echo json_encode(array('results' => $results));
	}

	public function search($laboratory_code){
		$patient = "";
		$patient = $this->exceljs_model->get($laboratory_code);
		$is_camlis_patient = false;
		if(!$patient){
			$patient =  $this->patient_model->get_camlis_patient($laboratory_code);
			if($patient) $is_camlis_patient = true;
		}
		$data['result'] = json_encode(['patient' => $patient , 'is_camlis_patient' => $is_camlis_patient]);
        $this->load->view('ajax_view/view_result',$data);
	}
	public function search_by_phone($phone){
		$patient = "";
		$patient = $this->exceljs_model->get(false,false,$phone);
		$is_camlis_patient = false;
		$data['result'] = json_encode(['patient' => $patient , 'is_camlis_patient' => $is_camlis_patient]);
        $this->load->view('ajax_view/view_result',$data);
	}	
	public function search_by_name($name){
		$patient = "";
		$patient = $this->exceljs_model->get(false,strtolower($name),false);
		$is_camlis_patient = false;
		$data['result'] = json_encode(['patient' => $patient , 'is_camlis_patient' => $is_camlis_patient]);
        $this->load->view('ajax_view/view_result',$data);
	}	
	// format date time Y-m-d
    public function format_date($date){
        $arr = explode('/',$date);
        return $arr[2].'-'.$arr[1].'-'.$arr[0];
    }
	public function get_data(){	
		$start_date = $this->format_date($this->input->post('start_date'));
		$end_date 	= $this->format_date($this->input->post('end_date'));
		$result_list   = $this->exceljs_model->get_data($start_date, $end_date);
		$table_body = '';
		$n = 1;
		if(count($result_list) == 0){
            $table_body .= '<tr>';
            $table_body .= '<td colspan="26" class="text-center">No data found</td>';
            $table_body .= '</tr>';
        }else{
			$lang = 'en';
			if($lang == 'en'){
				$FOR_RESEARCH_ARR = unserialize(FOR_RESEARCH_FIELD_ARRAY);
				$VACCINATION_STATUS = unserialize(VACCINATION_STATUS_DD_EN);
				
			}else{
				$FOR_RESEARCH_ARR = unserialize(FOR_RESEARCH_FIELD_ARRAY_KH);
				$VACCINATION_STATUS = unserialize(VACCINATION_STATUS_DD_KH);
			}
			foreach($result_list as $key => $row){
				$table_body .= '<tr>';
				$table_body .= '<td>'.$n.'</td>';
				$table_body .= '<td>'.$row['no_by_day'].'</td>';
				$table_body .= '<td>'.$row['case_no'].'</td>';
				$table_body .= '<td>'.$row['patient_code'].'</td>';
				$table_body .= '<td>'.$row['fullname'].'</td>';
				$table_body .= '<td>'.$row['sex'].'</td>';
				$table_body .= '<td>'.$row['age'].'</td>';
				$table_body .= '<td>'.$row['nationality_en'].'</td>';
				$table_body .= '<td>'.$row['phone'].'</td>';
				$table_body .= '<td>'.$row['date_of_sampling'].'</td>';
				$table_body .= '<td>'.$row['date_of_result'].'</td>';
				$table_body .= '<td>'.$row['f20_event'].'</td>';
				$table_body .= '<td>'.$row['imported_country'].'</td>';
				$table_body .= '<td>'.$row['date_of_onset'].'</td>';
				$table_body .= '<td>'.$row['symptoms'].'</td>';
				$table_body .= '<td>'.$row['positive_on'].'</td>';
				if(isset($row['reason_for_testing'])){
					$reason = $FOR_RESEARCH_ARR[$row['reason_for_testing']];
				}else{
					$reason = "";
				}
				$table_body .= '<td>'.$reason.'</td>';
				$table_body .= '<td>'.$row['province_en'].'</td>';
				$table_body .= '<td>'.$row['district_en'].'</td>';
				$table_body .= '<td>'.$row['commune_en'].'</td>';
				$table_body .= '<td>'.$row['village_en'].'</td>';
				$table_body .= '<td>'.$row['detection_province_en'].'</td>';
				$table_body .= '<td>'.$row['remark'].'</td>';
				if(isset($row['vaccination_status'])){
					$status = $VACCINATION_STATUS[$row['vaccination_status']];
				}else $status = "";
				$table_body .= '<td>'.$status.'</td>';
				$table_body .= '<td>'.$row['first_injection_date'].'</td>';
				$table_body .= '<td>'.$row['second_injection_date'].'</td>';
				$table_body .= '<td>'.$row['vaccine_name'].'</td>';
				if(isset($row['img_url'])){
					$img_str = '<img src="'.$row['img_url'].'" style="height:64px;" />';
				}else{
					$img_str = "";
				}
				$table_body .= '<td>'.$img_str.'</td>';
				$table_body .= '</tr>';
				$n++;
			}
		}
        
		echo json_encode(array('data' => $result_list , 'htmlString' =>$table_body));
	
	}
}