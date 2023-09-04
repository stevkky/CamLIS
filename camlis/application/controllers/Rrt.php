<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Rrt extends MY_Controller {
	public function __construct() {
		parent::__construct();		
		$this->load->model('rrt_model');		
		$this->load->model(array('pidgenerator_model'));
	}
	
	public function index() {
		
		$this->load->model([
			'gazetteer_model',									
			'country_model',
			'user_model'
        ]);
		$this->data['cur_main_page'] 		= 'Grid';
		$this->data['provinces']            = $this->gazetteer_model->get_province();
		$this->data['countries']            = $this->country_model->get_country();
		$this->data['nationalities']        = $this->country_model->get_nationality();		
		// added 22-03-2021 for LINE LIST
		$this->data['districts']            = $this->gazetteer_model->get_district();
		$this->data['communes']             = $this->gazetteer_model->get_commune_();
		$this->data['villages']             = $this->gazetteer_model->get_village_();
		
		//END
		//Get Province_code of the user
		$user = $this->user_model->get_province($this->session->userdata('id'));
		$this->data['user']					= $user;
		if($user){
			$this->session->set_userdata('province_code',$user['province_code']);
			$this->session->set_userdata('fullname',$user['fullname']);
			$this->session->set_userdata('province_name',$user['province_name']);
			$this->session->set_userdata('location',$user['location']);
		}
		//jspreadsheet
		$this->template->stylesheet->add('assets/camlis/css/camlis_sample_style.css');
        $this->template->javascript->add('assets/camlis/js/camlis_variable_format.js');
		$this->template->stylesheet->add('assets/plugins/jspreadsheet/css/jsuites.css');
		$this->template->stylesheet->add('assets/plugins/jspreadsheet/css/jexcel.css');
		$this->template->javascript->add('assets/plugins/jspreadsheet/js/jexcel.js');
		$this->template->javascript->add('assets/plugins/jspreadsheet/js/jsuites.js');
		// Backup Line List Version 1
		$this->template->javascript->add('assets/camlis/js/camlis_grid.js');		
		$this->template->modal->view('template/modal/modal_result_grid');
		$this->template->modal->view('template/modal/modal_error_grid');
		$this->template->modal->view('template/modal/modal_labo_form');
		$this->template->content->view('template/pages/grid', $this->data);
		$this->template->content_title = _t('page_header');
		$this->template->publish();
	}

	public function add_line_list(){             
		$this->load->library('phpqrcode/Qrlib');
		// Generate QRCODE
		//file path for store images
		$SERVERFILEPATH = $_SERVER['DOCUMENT_ROOT'].'/assets/plugins/qrcode/img/';   
        $data               	= $this->input->post('data');
        $patients           	= array();
        $n                  	= 1;
		$patient_code_col 		= 0; 
		$patient_name_col 		= 1; 
		$age_col          		= 2;
		$gender_col       		= 3;
		$phone_col        		= 4;
		$residence_col    		= 5;
		$province_col     		= 6;
		$district_col     		= 7;
		$commune_col      		= 8;
		$village_col      		= 9;
		$country_col      		= 10;
		$nationality_col  		= 11;
		$arrival_date_col  		= 12;
		$passport_col	  		= 13;
		$flight_number_col 		= 14;
		$seat_number_col		= 15;
		$sample_source_col		= 16;
		$collected_date_col		= 17;
		$number_of_sample_col	= 18;
		$collector_name_col		= 19;
		$phone_collector_name_col= 20;
		$for_labo_col			= 21;
		$gender_id_col    		= 22;
		$province_id_col  		= 23;
		$district_id_col  		= 24;
		$commune_id_col   		= 25;
		$village_id_col   		= 26;
		$country_id_col   		= 27;
		$nationality_id_col 	= 28;

        ini_set('max_input_vars', 8000);
        ini_set('memory_limit', '512M');
        ini_set('max_execution_time', 3600);

        foreach($data as $key => $row){

            // Check field required 
            // if patient_name is empty, just skip it 
            if(!empty($row[$patient_name_col])){
                if( isset($row[$patient_name_col]) && 
                    isset($row[$age_col]) && 
                    isset($row[$gender_col]) && 
                    isset($row[$province_col]) && 
                    isset($row[$district_col]) &&
                    isset($row[$commune_col]) &&
                    isset($row[$village_col])
                ){
					if(($row[$patient_code_col] == null) || (strlen($row[$patient_code_col]) == 0) ){
						$patient_code			= $this->getAutoPatientCode();
						while ($patient_code == 0){
							$patient_code		= $this->getAutoPatientCode();
						}
					}else{
						$patient_code           = $row[$patient_code_col];
					}

                    $patient_name               = $row[$patient_name_col];
					$province           		= !empty($row[$province_id_col]) ? $row[$province_id_col] : -1;
					$district           		= !empty($row[$district_id_col]) ? $row[$district_id_col] : -1;
					$commune            		= !empty($row[$commune_id_col]) ? $row[$commune_id_col] : -1;
					$village           		 	= !empty($row[$village_id_col]) ? $row[$village_id_col] : -1;
					$age                		= $row[$age_col];
					$patient_sex        		= $row[$gender_id_col];
					$patient_phone      		= ($row[$phone_col] !== "") ? $row[$phone_col] : null ;

					$phone = khNumberToLatinNumber($patient_phone);
					if(strlen($phone) > 0){
						$phone = khNumberToLatinNumber($patient_phone);
						if(strlen($phone) > 10) $phone = substr($phone,0,10);
					}else{
						if(is_numeric($patient_phone)){
							$phone = str_replace(' ', '', $patient_phone);
							$phone = preg_replace("/[^0-9]/", "",$phone);
							if(strlen($phone) > 10) $phone = substr($phone,0,10);
						}else{
							$phone = str_replace(' ', '', $patient_phone);
							$phone = preg_replace("/[^0-9]/", "",$phone); 
						}
					}
					$residence          = $row[$residence_col];
					$country            = ($row[$country_id_col] !== "") ? $row[$country_id_col] : null;
					$nationality        = ($row[$nationality_id_col] !== "") ? $row[$nationality_id_col] : null;
					$date_arrival       = ($row[$arrival_date_col] !== "") ? date('Y-m-d', strtotime($row[$arrival_date_col])): null;
					$passport_number    = ($row[$passport_col] !== "") ? $row[$passport_col] : null;
					$flight_number      = ($row[$flight_number_col] !== "") ? $row[$flight_number_col] : null;
					$seat_number        = ($row[$seat_number_col] !== "") ? $row[$seat_number_col] : null;

					$patient_data = array(
						'patient_code'   	=> $patient_code, 
						'patient_name'   	=> $patient_name, 
						'sex'            	=> $patient_sex, 
						'age'            	=> $age, 
						'phone'          	=> $phone, 
						'province'       	=> $province, 
						'district'       	=> $district, 
						'commune'        	=> $commune, 
						'village'        	=> $village,
						'residence'      	=> $residence,
						'nationality'    	=> $nationality,
						'country'        	=> $country,
						'date_arrival'   	=> $date_arrival,
						'passport_number'	=> $passport_number,
						'seat_number'    	=> $seat_number,
						'flight_number'  	=> $flight_number
					);
					
					//Check Patient Code existent
					$patient_info				= $this->rrt_model->get_outside_patient(FALSE , $patient_code);
					$patient["patient_code"]  	= $patient_code;
					$patient["patient_name"]	= $patient_name;

					if($patient_info){
						if($this->rrt_model->update_outside_patient($patient_info['pid'], $patient_data)){
							$patient["msg"]         = "កែប្រែបានជោគជ័យ";
							$patient["pstatus"]     = true;
							$pid 					= $patient_info['pid'];
						}else{
							$patient["msg"]         = "កែប្រែមិនបានជោគជ័យ";
							$patient["pstatus"]     = false;
							$pid 					= $patient_info['pid'];
						}
					}else{
						$pid = $this->rrt_model->save_outside_patient($patient_data);
						if ($status = $pid > 0) {
							// add QR-Code
							$patient["msg"]         = "បញ្ជូលបានជោគជ័យ";
                            $patient["pstatus"]     = true;
							
							$text = 'name='.$this->session->userdata('fullname').',phone='.$this->session->userdata('phone').',location='.$this->session->userdata('location').',pid='.$patient_code;
							//$text 			= $pid;
							$folder 		= $SERVERFILEPATH;
							$file_name1 	= $patient_code.".png";
							$file_name 		= $folder.$file_name1;
							QRcode::png($text,$file_name);

							$patient["qrcode"]      	= $file_name1;
							$patient["qrcode_status"]   = true;
						}else{
							$patient["msg"]         = "បញ្ជូលមិនបានជោគជ័យ";
							$patient["pstatus"]     = false;
							$pid                    = 0; // if pid does not exist;
						}
					}
				// add sample info
					if(strlen($pid) > 0){						
						//Check sample field require
						if($collected_date_col !== "" && $row[$number_of_sample_col] > 0){
							$collected_date     = ($row[$collected_date_col] !== "") ? date('Y-m-d', strtotime($row[$collected_date_col])) : null ;
							$data = array(
                                'patient_id'            => $pid,
								'sample_number'			=> "",
                                'sample_source'      	=> $row[$sample_source_col],
                                'collected_date'        => $collected_date, 
                                'number_of_sample'      => $row[$number_of_sample_col],
                                'sample_collector'      => $row[$collector_name_col],
                                'phone_number'          => $row[$phone_collector_name_col],
								'for_labo'				=> $row[$for_labo_col]
                            );
							$psample_id 			   = $this->rrt_model->add_patient_sample($data);
							if($psample_id > 0){
								$patient["sample_msg"]     = "បញ្ចូលបានជោគជ័យ";
								$patient["sample_status"]  = true;
								$patient["sample_number"]  = $data['sample_number'];
								$patient["psample_id"]     = $psample_id;
							}
						}else{
							// Sample 
							$patient["sample_msg"]     = "ទិន្នន័យសំណាកមិនគ្រប់គ្រាន់";
                            $patient["sample_status"]  = false;
                            $patient["sample_number"]  = null;
						}
					}
                }
                $patients[]  = $patient; 
            }
        }
        echo json_encode(array('patients' => $patients));
    }
	public function search_patient($patient_code){
		$patient = $this->rrt_model->get_outside_patient(FALSE, $patient_code);
		// Get number of sample
		$number_of_sample = $this->rrt_model->get_number_of_sample($patient["pid"]);
		$data['result'] = json_encode(['patient' => $patient , 'number_of_sample' => $number_of_sample]);
        $this->load->view('ajax_view/view_result',$data);
	}
	public function get_patients(){
		$date 			= $this->input->post('date');
		$user 			= $this->input->post('user');
		$patients 		= $this->rrt_model->get_patients($user, $date);
		$data['result'] = json_encode(['patients' => $patients]);
        $this->load->view('ajax_view/view_result',$data);
	}
	public function update_patient_sample(){
		$data 			= $this->input->post('data');
		$result 		= array();
		for($i = 0 ; $i < count($data) ; $i++){
			$result[]	= $this->rrt_model->update_patient_sample($data[$i]);
		}
		$data['result'] = json_encode(['result' => $result]);
        $this->load->view('ajax_view/view_result',$data);
	}
	public function getAutoPatientCode(){
		$province_id = $this->session->userdata('province_code');
		$user_id 	 = $this->session->userdata('id');
		$timestamp	 = date('Y-m-d H:i:s');

		if(strlen($province_id) == 1) {
			$province_id 	= "0".$province_id;
		}
		$currentDate 		= date("Y-m-d"); // 2021-01-11
		$cdate 		 		= date("ymd");
		$result 			= $this->pidgenerator_model->getNumber($currentDate);
		$number 			= $result[0]["number"];
		$number++;
		if(strlen($number) == 1) $number = "000".$number;
		else if(strlen($number) == 2) $number = "00".$number;
		else if(strlen($number) == 3) $number = "0".$number;
		
		//XX-YYMMDDNNNN
		// check if pid is exist		
		$pid 				= $province_id."-".$cdate.$number;
		$check 				= $this->pidgenerator_model->isPidExist($pid);
		if($check[0]["number"] !== 1){
			$id = $this->pidgenerator_model->add($pid, $user_id , $timestamp);
			return $pid;
			
		}else{
			return 0;
		}
	}
	public function patient_covid_forms($action, $patient_sample_ids) {
        $this->load->model(['gazetteer_model','clinical_symptom_model']);
        $this->app_language->load('pages/patient_sample_result');
        $result 			= [];
        $patient_sample_ids = explode('n', urldecode($patient_sample_ids));
        
        foreach ($patient_sample_ids as $index => $patient_sample_id) {
            //Patient Sample
            //$patient_sample = collect($this->rrt_model->get_patient_sample($patient_sample_id))->first();
			$patient_sample = $this->rrt_model->get_patient_sample($patient_sample_id);
            if (!$patient_sample) continue;

            //Patient Info
            //$patient_id = collect($patient_sample)->get('pid');
			$patient_id = $patient_sample["pid"];
            $patient_info = $this->rrt_model->get_outside_patient($patient_id,FALSE);

            $this->data['patient_sample']               = $patient_sample;

			$dob                = date('Y') - $patient_info['age'];
            $dob               .= '-'.date("m-d");   
			$patient_info['dob'] = $dob;
			$this->data['patient_info']                 = $patient_info;
            $this->data['action'] = $index == count($patient_sample_ids) - 1 ? $action : '';
           
            $this->data['sample_test']                  = [];
            $this->data['clinical_symptoms']            = $this->clinical_symptom_model->get(); //added 10 Jan 2021
            $this->data['clinical_symptoms_dd']         = []; //added 10 Jan 2021
            
            $this->data['number_of_sample']             = collect($patient_sample)->get('number_of_sample');          
            $this->data['my_patient_id']	= $patient_id;
			if ($this->input->server('REQUEST_METHOD') == 'POST')
                $result[] = ['patient_sample_id' => $patient_sample_id, 'pid' => $patient_id,'pinfo'=>$patient_info, 'template' => $this->load->view('template/print/patient_labo_form.php', $this->data, TRUE)];
            else
                $this->load->view('template/print/patient_labo_form.php', $this->data);
        }
		if ($this->input->server('REQUEST_METHOD') == 'POST') echo json_encode($result);
    }

	function export_grid_result(){
		$this->load->library('phptoexcel');
		$patient_sample_ids = $this->input->get('psample_ids');
		$patient_sample_ids = explode('n', urldecode($patient_sample_ids));		
        $current_date = date('d/M/Y');
		$number  = 1;
		$num_row = 2;

		try{
			ob_start();
			$objPHPExcel    = new PHPExcel();
			$n_sheet = 0;			
			$table_columns  = array("លរ", "លេខសំគាល់អ្នកជំងឺ", "ឈ្មោះអ្នកជំងឺ", "អាយុ", "ភេទ", "លេខទូរស័ព្ទ","កន្លែងស្នាក់នៅ","ខេត្តក្រុង","ស្រុក","ឃុំ","ភូមិ","មកពីប្រទេស","សញ្ញាតិ","ថ្ងៃខែមកដល់","លេខលិខិតឆ្លងដែន","លេខជើងហោះហើរ","លេខកៅអី","ទីកន្លែងយកសំណាក","ថ្ងៃខែយកសំណាក","សំណាកលើកទី","ឈ្មោះអ្នកប្រមូល","លេខទូរស័ព្ទ","យកទៅមន្ទីរពិសោធន៏");
			$border_style = array(
				'borders' => array(
					'allborders' => array(
						'style' => PHPExcel_Style_Border::BORDER_THIN
					)
				)
			);
			$headerStyleArray = array(
				'font'  => array(
				'bold'  => true,
				'color' => array('rgb' => '1f01ff'),
				'size'  => 11,
				'name'  => 'Khmer OS Muol Light',
				'borders' => array(
					'allborders' => array(
						'style' => PHPExcel_Style_Border::BORDER_THIN
					)
				)
			));
			$bodyStyleArray = array(
                'font'  => array(
                        'color' => array('rgb' => '000000'),
                        'size'  => 11,
                        'name'  => 'Khmer OS Siemreap'
                    ));
			$objPHPExcel->createSheet();
			$sheet          = $objPHPExcel->setActiveSheetIndex($n_sheet);
			$sheet->setTitle("បញ្ចីអ្នកជំងឺ");
			$column         = 0;
			$sheet->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
            $sheet->getStyle("A1:W1")->applyFromArray($headerStyleArray);
			foreach($table_columns as $field)
			{
				$sheet->setCellValueByColumnAndRow($column, 1, $field);
				$column++;
			}
			$patient_samples = $this->rrt_model->get_patient_samples($patient_sample_ids);
			foreach($patient_samples as $key => $row){
				$sex = $row["sex"] == 'F' ? 'ស្រី' : 'ប្រុស';
				$sheet->setCellValueByColumnAndRow(0, $num_row, $number);
				$sheet->setCellValueByColumnAndRow(1, $num_row, $row["patient_code"]);
				$sheet->setCellValueByColumnAndRow(2, $num_row, $row["name"]);
				$sheet->setCellValueByColumnAndRow(3, $num_row, $row["age"]);
				$sheet->setCellValueByColumnAndRow(4, $num_row, $sex);
				$sheet->setCellValueByColumnAndRow(5, $num_row, $row["phone"]);
				$sheet->setCellValueByColumnAndRow(6, $num_row, $row["residence"]);
				$sheet->setCellValueByColumnAndRow(7, $num_row, $row["province_kh"]);
				$sheet->setCellValueByColumnAndRow(8, $num_row, $row["district_kh"]);
				$sheet->setCellValueByColumnAndRow(9, $num_row, $row["commune_kh"]);
				$sheet->setCellValueByColumnAndRow(10, $num_row, $row["village_kh"]);
				$sheet->setCellValueByColumnAndRow(11, $num_row, $row["country_name_en"]);
				$sheet->setCellValueByColumnAndRow(12, $num_row, $row["nationality_en"]);
				$sheet->setCellValueByColumnAndRow(13, $num_row, $row["date_arrival"]);
				$sheet->setCellValueByColumnAndRow(14, $num_row, $row["passport_number"]);
				$sheet->setCellValueByColumnAndRow(15, $num_row, $row["flight_number"]);
				$sheet->setCellValueByColumnAndRow(16, $num_row, $row["seat_number"]);
				$sheet->setCellValueByColumnAndRow(17, $num_row, $row["sample_source"]);
				$sheet->setCellValueByColumnAndRow(18, $num_row, $row["collected_date"]);
				$sheet->setCellValueByColumnAndRow(19, $num_row, $row["number_of_sample"]);
				$sheet->setCellValueByColumnAndRow(20, $num_row, $row["sample_collector"]);
				$sheet->setCellValueByColumnAndRow(21, $num_row, $row["phone_number"]);
				$sheet->setCellValueByColumnAndRow(22, $num_row, $row["for_labo"]);
				$number++;
				$num_row++;
			}
			$sheet->getDefaultStyle()->applyFromArray($bodyStyleArray);                        
            $sheet->getStyle("A1:W".($num_row - 1))->applyFromArray($border_style);
			$sheet->getStyle('A1:W'.($num_row - 1))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			// Auto size columns for each worksheet
			foreach ($objPHPExcel->getWorksheetIterator() as $worksheet) {

				$objPHPExcel->setActiveSheetIndex($objPHPExcel->getIndex($worksheet));

				$sheet = $objPHPExcel->getActiveSheet();
				$cellIterator = $sheet->getRowIterator()->current()->getCellIterator();
				$cellIterator->setIterateOnlyExistingCells(true);
				/** @var PHPExcel_Cell $cell */
				foreach ($cellIterator as $cell) {
					$sheet->getColumnDimension($cell->getColumn())->setAutoSize(true);
				}
			}
			$sheetIndex = $objPHPExcel->getIndex(
				$objPHPExcel->getSheetByName('Worksheet 1')
			);
			$objPHPExcel->removeSheetByIndex($sheetIndex);
			$wsIndexStr = "".$sheetIndex;
			$filename = "Patient_data_".$current_date;

			header("Pragma: public");
			header("Expires: 0");
			header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
			header("Content-Type: application/force-download");
			header("Content-Type: application/octet-stream");
			header("Content-Type: application/download");

			header('Content-Type: application/vnd.ms-excel');
			header('Content-Disposition: attachment;filename="'.$filename.'.xls"');
			header('Cache-Control: max-age=0'); //no cache
			ob_end_clean();

			$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
			$objWriter->save('php://output');

			exit;
		}catch (PHPExcel_Exception $e) {
			echo $e->getMessage();
		}
		
	}
}