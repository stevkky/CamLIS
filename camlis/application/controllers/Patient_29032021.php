<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Patient extends MY_Controller {
	public function __construct() {
		parent::__construct();
		$this->load->model(array('patient_model'));
		
		//Load Language
		$this->app_language->load(array('patient', 'sample'));

		$this->data['cur_main_page'] = 'patient';
	}
	
	public function index() {
        $this->aauth->control('view_patients');
		$this->template->plugins->add(['DataTable', 'AutoComplete']);
		$this->template->stylesheet->add('assets/camlis/css/camlis_patient_style.css');
		$this->template->javascript->add('assets/camlis/js/camlis_patient_script.js');
		$this->template->javascript->add('assets/camlis/js/camlis_merge_patient.js');
		$this->template->content->view('template/pages/patients', $this->data);
		$this->template->content_title = null;
        $this->template->modal->view('template/modal/modal_patient_merging');
		$this->template->publish();
	}

	/**
	 * View patient's info and sample
	 * @param $patient_id
	 */
	public function details($patient_id) {
		$this->load->model(['patient_sample_model', 'gazetteer_model']);

        $this->data['patient']   = isPMRSPatientID($patient_id) ? $this->patient_model->get_pmrs_patient($patient_id) : $this->patient_model->get_outside_patient($patient_id);
		$this->data['samples']   = $this->patient_sample_model->get_patient_sample(NULL, NULL, $patient_id, NULL, CamlisSession::getLabSession('labID'));
        $this->data['provinces'] = $this->gazetteer_model->get_province();

        $this->template->plugins->add(['MomentJS', 'BootstrapDateTimePicker']);
		$this->template->javascript->add('assets/camlis/js/camlis_patient_script.js');
		$this->template->content->view('template/pages/patient_detail', $this->data);
        $this->template->content_title = _t('patient.patient_information');
        if ($this->data['patient'] && !isPMRSPatientID($patient_id) && $this->aauth->is_allowed('edit_patients')) {
            $this->template->content_title .= "&nbsp;&nbsp;<button type='button' id='edit-patient' class='btn btn-primary btn-sm'><i class='fa fa-pencil-square-o'></i>&nbsp;" . _t('global.edit') . "</button>";
        }
		$this->template->publish();
	}

    /**
     * Search for patient
     * @param $patient_code
     * return mixed
     */
    public function search($patient_code) {
        $this->load->library('patientwebservice');
        $this->load->model('gazetteer_model');

        if (isPMRSPatientID($patient_code)) {
            $response  = $this->patientwebservice->execute($patient_code);
            $patient   = $response->status && isset($response->data[0]) ? (array)$response->data[0] : NULL;

            if ($patient && !empty($patient['pid'])) {
                $province   = collect($this->gazetteer_model->get_province((int)$patient['province']))->first();
                $district   = collect($this->gazetteer_model->get_district(NULL, (int)$patient['district']))->first();
                $commune    = collect($this->gazetteer_model->get_commune(NULL, (int)$patient['commune']))->first();
                $village    = collect($this->gazetteer_model->get_village(NULL, (int)$patient['village']))->first();
                $patient['province_en'] = element('name_en', (array)$province);
                $patient['province_kh'] = element('name_kh', (array)$province);
                $patient['district_en'] = element('name_en', (array)$district);
                $patient['district_kh'] = element('name_kh', (array)$district);
                $patient['commune_en']  = element('name_en', (array)$commune);
                $patient['commune_kh']  = element('name_kh', (array)$commune);
                $patient['village_en']  = element('name_en', (array)$village);
                $patient['village_kh']  = element('name_kh', (array)$village);

                $patient['patient_code'] = $patient['pid'];
                $patient['is_pmrs_patient'] = TRUE;
            }
            
            else{                
                //
                 // ADDED: 16 Feb 2021
                // There is an issue happen, patient_code exists in our db but not in PMRS server which cause an error while saving test and result 
                 // so if this case happen we will retrieve data of patient from our db instead
                // Patient Code error: AH-000428
                 //
                $patient		= $this->patient_model->get_pmrs_patient($patient_code);
                $patient['is_pmrs_patient'] = TRUE;
            }
            
        } else {
            $laboratory_code = CamlisSession::getLabSession('lab_code');
            if (strpos($patient_code, $laboratory_code.'-') === 0) {
                $patient_code = substr_replace($patient_code, '', 0, strlen($laboratory_code.'-'));
            }

            $patient_code = $laboratory_code.'-'.$patient_code;
            $patient = $this->patient_model->get_outside_patient(FALSE, $patient_code);
            if ($patient) $patient['is_pmrs_patient'] = FALSE;
        }
        $ispmrs = isPMRSPatientID($patient_code);
        $data['result'] = json_encode(['patient' => $patient]);
        $this->load->view('ajax_view/view_result',$data);
    }

    /**
     * Get Patients
     * @return mixed
     */
    public function get_patients() {
        $patient_code = $this->input->post('patient_code');
        $patient_name = $this->input->post('patient_name');
        $limit        = $this->input->post('limit');
        $withAddress  = $this->input->post('with_address') > 0;
        $patients     = $this->patient_model->get_patients(FALSE, $patient_code, $patient_name, $limit, $withAddress);
        $patients     = collect($patients)->map(function($d) {
            $d['is_pmrs_patient'] = isPMRSPatientID($d['pid']);
            return $d;
        })->toArray();
        echo json_encode(['patients' => $patients]);
    }

	/**
	 * View all patient (DataTable)
	 */
	public function view() {
		$reqData          = $this->input->post();
		$_data['reqData'] = $reqData;
		$result = $this->patient_model->view_all_patients($_data);
		
		$data['result'] = json_encode($result);
		$this->load->view('ajax_view/view_result',$data);
	}

    /**
     * Get Patient Type
     */
	public function get_patient_type() {
		$result = $this->patient_model->get_patient_type();
		
		$data['result'] = json_encode($result);
		$this->load->view('ajax_view/view_result',$data);
	}

    /**
     * Add New Outside patient
     */
	public function save_outside_patient() {
        $this->app_language->load(['pages/patient_sample_entry']);

	    //Validation
        $this->form_validation->set_rules('patient_manual_code', 'patient_manual_code', 'trim');
        $this->form_validation->set_rules('patient[patient_name]', 'patient_name', 'trim|required');
        $this->form_validation->set_rules('patient[sex]', 'sex', 'trim|required|numeric|in_list[1,2]');
        $this->form_validation->set_rules('patient[dob]', 'dob', 'trim|required');


		$patient         = $this->input->post('patient');
        $laboratory_code = CamlisSession::getLabSession('lab_code');
        $msg             = _t('global.msg.save_fail');
		$status          = FALSE;
        $id_exist        = FALSE;
        $patient_info    = NULL;

        if ($this->form_validation->run() == FALSE) {
            $msg = _t('global.msg.fill_required_data');
        }
        else {
            $patient_code = empty($patient['patient_manual_code']) ? date('dmyHis') : strtoupper($patient['patient_manual_code']);
            if (strpos($patient_code, $laboratory_code.'-') === 0) {
                $patient_code = substr_replace($patient_code, '', 0, strlen($laboratory_code.'-'));
            }

            if (isPMRSPatientID($patient_code)) {
                $msg = _t('patient_sample.msg.is_pmrs_patient');
                $id_exist = TRUE;
            }
            else {
                $patient_code = trim($laboratory_code).'-'.$patient_code;
                $patient_code = str_replace(' ', '', $patient_code);

                //Check Patient Code existent
                $patient_info = $this->patient_model->get_outside_patient(FALSE, $patient_code);
                if ($patient_info && !empty($patient_info['pid'])) {
                    $id_exist = TRUE;
                    $msg      = _t('patient.msg.patient_exist');
                } else {
                    $patient['patient_code'] = $patient_code;
                    $patient = elements(['patient_code', 'patient_name', 'sex', 'dob', 'phone', 'province', 'district', 'commune', 'village',
                    'residence','nationality','country','date_arrival','passport_number','seat_number','is_positive_covid','test_date','is_contacted','contact_with','relationship_with_case','travel_in_past_30_days','flight_number','is_direct_contact'], $patient);
                    
                    $pid = $this->patient_model->save_outside_patient($patient);
                    if ($status = $pid > 0) {
                        $msg = _t('global.msg.save_success');
                        $patient_info = $this->patient_model->get_outside_patient($pid);
                    }
                }
            }
        }
  
		$data['result'] = json_encode(array('status' => $status, 'msg' => $msg, 'patient' => $patient_info, 'id_exist' => $id_exist));
		$this->load->view('ajax_view/view_result', $data);
	}

    /**
     * Update outside patient
     * @param $pid
     */
    public function update_outside_patient($pid) {
        //Validation
        $this->form_validation->set_rules('patient[patient_name]', 'patient_name', 'trim|required');
        $this->form_validation->set_rules('patient[sex]', 'sex', 'trim|required|numeric|in_list[1,2]');
        $this->form_validation->set_rules('patient[dob]', 'dob', 'trim|required');
        $patient = $this->input->post('patient');
        $msg     = _t('global.msg.update_fail');
        $status  = FALSE;
        $patient_info = null;

        if ($this->form_validation->run() == FALSE) {
            $msg = _t('global.msg.fill_required_data');
        } else {
            //$patient = elements(['patient_name', 'sex', 'dob', 'phone', 'province', 'district', 'commune', 'village'], $patient);
            $patient = elements(['patient_name', 'sex', 'dob', 'phone', 'province', 'district', 'commune', 'village',
                    'residence','nationality','country','date_arrival','passport_number','seat_number','flight_number','is_positive_covid','test_date','is_contacted','contact_with','relationship_with_case','travel_in_past_30_days','is_direct_contact'], $patient);
            if ($this->patient_model->update_outside_patient($pid, $patient)) {
                //$status = FALSE;
                $status = TRUE;
                $msg = _t('global.msg.update_success');
                $patient_info = $this->patient_model->get_outside_patient($pid);
            }
        }

        $data['result'] = json_encode(['status' => $status, 'msg' => $msg, 'patient' => $patient_info]);
        $this->load->view('ajax_view/view_result', $data);
    }

    /**
     * Delete Outside Patient
     */
	public function delete() {
	    $this->load->model(['patient_sample_model', 'result_model']);

	    $patient_id = $this->input->post('patient_id');
	    $status     = FALSE;
	    $msg        = _t('global.msg.delete_fail');

	    if ($patient_id > 0) {
            $this->db->trans_start();
            $this->patient_model->update_outside_patient($patient_id, ["status" => 0]);
            $this->patient_sample_model->delete_patient_sample(FALSE, $patient_id);
            $this->patient_sample_model->delete_patient_sample_detail(FALSE, $patient_id);
            $this->patient_sample_model->delete_patient_sample_test(FALSE, $patient_id);
            $this->result_model->delete_patient_sample_result(FALSE, $patient_id);
            $this->result_model->delete_patient_sample_result_antibiotic(FALSE, $patient_id);
            $this->db->trans_complete();

            if ($this->db->trans_status() === TRUE) {
                $status = FALSE;
                $msg	= _t('global.msg.delete_success');
            }
        }

        $data['result'] = json_encode(array('status' => $status, 'msg' => $msg));
        $this->load->view('ajax_view/view_result',$data);
    }

	/**
	 * Add new Standard Patient Type
	 */
	public function add_std_patient_type() {
		$this->app_language->load('admin');

		$patient_type	= $this->input->post('patient_type');
		$patient_type	= trim($patient_type);
		$gender			= $this->input->post('gender');
		$min_age		= $this->input->post('min_age');
		$max_age		= $this->input->post('max_age');
		$min_age_unit	= $this->input->post('min_age_unit');
		$max_age_unit	= $this->input->post('max_age_unit');
		$equal_max_age	= $this->input->post('equal_max_age');
		$msg = _t('global.msg.fill_required_data');
		$status = FALSE;

		$valid_age_range = (int)$max_age > 0 && (int)$max_age > 0 && ((int)$min_age * (int)$min_age_unit <= (int)$max_age * (int)$max_age_unit);

		if (!empty($patient_type)) {
			//Check if patient type is exists
			if (count($this->patient_model->get_std_patient_type(array("REPLACE(ptype.type,' ','') =" => str_replace(' ', '', $patient_type)))) > 0) {
				$msg = _t('admin.msg.patient_type_exist');
			}
			else {
				$_data = array(
					'type'			=> $patient_type,
					'gender'		=> (int)$gender,
					'min_age'		=> empty(trim($min_age)) ? NULL : (int)$min_age,
					'max_age'		=> empty(trim($max_age)) ? NULL : (int)$max_age,
					'min_age_unit'	=> empty(trim($min_age_unit)) ? NULL : (int)$min_age_unit,
					'max_age_unit'	=> empty(trim($max_age_unit)) ? NULL : (int)$max_age_unit,
                    
                    'is_equal'		=> (int)$equal_max_age == 0 ? FALSE : TRUE
				);
				if ($this->patient_model->add_std_patient_type($_data) > 0) {
					$status = TRUE;
					$msg = _t('global.msg.save_success');
				} else {
					$msg = _t('global.msg.save_fail');
				}
			}
		}/* else if (!empty($patient_type) && !$valid_age_range && (int)$max_age > 0 && (int)$max_age) {
			$msg = _t('admin.msg.valid_age_range');
		}*/

		$this->data['result'] = json_encode(array('status' => $status, 'msg' => $msg));
		$this->load->view('ajax_view/view_result', $this->data);
	}

	/**
	 * Update Standard Patient Type
	 */
	public function update_std_patient_type() {
		$this->app_language->load('admin');

        $gender			= $this->input->post('gender');
		$patient_type_id= $this->input->post('patient_type_id');
		$patient_type	= $this->input->post('patient_type');
		$patient_type	= trim($patient_type);
		$min_age		= $this->input->post('min_age');
		$max_age		= $this->input->post('max_age');
		$min_age_unit	= $this->input->post('min_age_unit');
		$max_age_unit	= $this->input->post('max_age_unit');
		$equal_max_age	= $this->input->post('equal_max_age');
		$msg            = _t('global.msg.fill_required_data');
		$status         = FALSE;

		$valid_age_range = !empty(trim($min_age)) && !empty(trim($max_age)) && ((int)$min_age * (int)$min_age_unit <= (int)$max_age * (int)$max_age_unit);

		if (!empty($patient_type) && (int)$patient_type_id > 0 && $valid_age_range) {
			//Check if patient type is exists
			if (count($this->patient_model->get_std_patient_type(array('ptype."ID" !=' => (int)$patient_type_id, "REPLACE(ptype.type,' ','') =" => str_replace(' ', '', $patient_type)))) > 0) {
				$msg = _t('admin.msg.patient_type_exist');
			}
			else {
				$_data = array(
					'type'			=> $patient_type,
                    'gender'		=> (int)$gender,
					'min_age'		=> empty(trim($min_age)) ? NULL : (int)$min_age,
					'max_age'		=> empty(trim($max_age)) ? NULL : (int)$max_age,
					'min_age_unit'	=> empty(trim($min_age_unit)) ? NULL : (int)$min_age_unit,
					'max_age_unit'	=> empty(trim($max_age_unit)) ? NULL : (int)$max_age_unit,
					'is_equal'		=> (int)$equal_max_age == 0 ? FALSE : TRUE
				);

				$this->patient_model->update_std_patient_type($patient_type_id, $_data);
				$status = TRUE;
				$msg = _t('global.msg.update_success');
			}
		} else if (!empty($patient_type) && (int)$patient_type_id > 0 && !$valid_age_range) {
			$msg = _t('admin.msg.valid_age_range');
		}

		$this->data['result'] = json_encode(array('status' => $status, 'msg' => $msg));
		$this->load->view('ajax_view/view_result', $this->data);
	}

	/**
	 * Delete Standard Patient Type
	 */
	public function delete_std_patient_type() {
		$id = $this->input->post('patient_type_id');
		$status = FALSE;
		$msg = _t('global.msg.delete_fail');

		if ((int)$id > 0) {
			if ($this->patient_model->update_std_patient_type($id, array('status' => FALSE)) > 0) {
				$status = TRUE;
				$msg = _t('global.msg.delete_success');
			} else {
				$msg = _t('global.msg.delete_fail');
			}
		}

		$this->data['result'] = json_encode(array('status' => $status, 'msg' => $msg));
		$this->load->view('ajax_view/view_result', $this->data);
	}

	/**
	 * View Standard Patient Type (DataTable)
	 */
	public function view_std_patient_type() {
		$result			= $this->patient_model->view_std_patient_type($this->input->post());

		$data['result'] = json_encode($result);
		$this->load->view('ajax_view/view_result', $data);
	}

    /**
     * Merge patient
     */
    public function merge() {
        $this->aauth->control('merge_patient');
        $this->load->model(['patient_sample_model']);

        $this->form_validation->set_rules('patient_id_source', 'patient_id_source', 'trim|required');
        $this->form_validation->set_rules('patient_id_destination', 'patient_id_destination', 'trim|required|differs[patient_id_source]');

        $patient_id_source = $this->input->post('patient_id_source');
        $patient_id_destination = $this->input->post('patient_id_destination');
        $msg = _t('global.msg.save_fail');
        $status = FALSE;


        if ($this->form_validation->run() === TRUE && !isPMRSPatientID($patient_id_source) ) {
            $source_patient = $this->patient_model->get_outside_patient($patient_id_source);
            $destination_patient = isPMRSPatientID($patient_id_destination) ? $this->patient_model->get_pmrs_patient($patient_id_destination) : $this->patient_model->get_outside_patient($patient_id_destination);

            if ($source_patient && $destination_patient) {
                $source_patient_samples = $this->patient_sample_model->get_patient_sample(NULL, NULL, $source_patient['pid']);

                $this->db->trans_start();
                foreach($source_patient_samples as $source_patient_sample) {
                    //move patient sample to destination patient
                    $this->patient_sample_model->update_patient_sample($source_patient_sample['patient_sample_id'], ['patient_id' => $destination_patient['pid']]);
                    //store ref info
                    $this->patient_sample_model->add_ref_patient_sample([
                        'ref_patient_sample_id' => $source_patient_sample['patient_sample_id'],
                        'patient_id' => $source_patient_sample['patient_id']
                    ]);
                }
                //mark patient as moved
                $this->patient_model->update_outside_patient($source_patient['pid'], ['status' => PATIENT_MOVED]);
                $this->db->trans_complete();

                if ($this->db->trans_status()) {
                    $status = FALSE;
                    $msg = _t('global.msg.save_success');
                }
            }
        }

        echo json_encode(["status" => $status, "msg" => $msg]);
    }
    /** Added 19-03-2021 
     * Upload excel and read its content
    */
    public function upload_file(){
        $this->load->model(['gazetteer_model']);
        $status             = false;
        $msg                = "";
        $file_element_name  = 'theExcelFile';
        $data               = array();
        $laboratory_code    = CamlisSession::getLabSession('lab_code');
        $config['upload_path']      = $_SERVER['DOCUMENT_ROOT'].'/assets/camlis/upload/';
        $config['allowed_types']    = 'xlsx|xls';
        $config['max_size']         = 1024 * 8;
        $config['encrypt_name']     = TRUE;

        $this->load->library('upload',$config);
        if($this->upload->do_upload("theExcelFile")){
            $data               = array('upload_data' => $this->upload->data());           
            $status             = true;
            $msg                = "Upload successful";

            
            $file_name  = $_SERVER['DOCUMENT_ROOT'].'/assets/camlis/upload/'.$data['upload_data']['file_name'];
            
            //load the excel library
            $this->load->library('phptoexcel');
            
            //read file from path
            $objPHPExcel = PHPExcel_IOFactory::load($file_name);
            
            //get only the Cell Collection
            $cell_collection = $objPHPExcel->getActiveSheet()->getCellCollection();
            
            //extract to a PHP readable array format
            $tableHeaderString = '';
            foreach ($cell_collection as $cell) {
                $column     = $objPHPExcel->getActiveSheet()->getCell($cell)->getColumn();
                $row        = $objPHPExcel->getActiveSheet()->getCell($cell)->getRow();
                $data_value = $objPHPExcel->getActiveSheet()->getCell($cell)->getValue();
            
                //The header will/should be in row 1 only. 
                if ($row == 1) {
                    $header[$row][$column] = $data_value;                   
                } else {
                    $arr_data[$row][$column] = $data_value;                                                        
                }            
            }
            //send the data in an array format
            $data['header'] = $header;
            $data['values'] = $arr_data;
            foreach($header as $ind => $head){
                $tableHeaderString .= "<th>".$header[$ind]["B"]."</th>";
                $tableHeaderString .= "<th>".$header[$ind]["C"]."</th>";
                $tableHeaderString .= "<th>".$header[$ind]["D"]."</th>";
                $tableHeaderString .= "<th>".$header[$ind]["E"]."</th>";
                $tableHeaderString .= "<th>".$header[$ind]["F"]."</th>";
                $tableHeaderString .= "<th>".$header[$ind]["G"]."</th>";
                $tableHeaderString .= "<th>".$header[$ind]["H"]."</th>";
                $tableHeaderString .= "<th>".$header[$ind]["I"]."</th>";
                $tableHeaderString .= "<th>".$header[$ind]["J"]."</th>";
                $tableHeaderString .= "<th>".$header[$ind]["K"]."</th>";
                $tableHeaderString .= "<th>".$header[$ind]["L"]."</th>";
                $tableHeaderString .= "<th>".$header[$ind]["M"]."</th>";
                $tableHeaderString .= "<th>".$header[$ind]["N"]."</th>";
                $tableHeaderString .= "<th>".$header[$ind]["O"]."</th>";
            }
            $tableBodyString = '';
            $gender_array = array(
                'female' => 2,
                'f'      => 2,
                'ស្រី'     => 2,
                'male'   => 1,
                'm'      => 1,
                'ប្រុស'   => 1
            );
            $nationality_array = array(
                'ខ្មែរ'       => '116',
                'វៀតណាម'    => '704',
                'ចិន'        => '156',
                'កូរ៉េ'        => '410'
            );
            foreach($arr_data as $key => $val){
                // B2 is patient_name
                $patient_name   = $arr_data[$key]["B"];
                //C2 is patient code
                $patient_code   = $arr_data[$key]["C"];
                /**
                 * D2 is gender
                 * Content could be ស្រី F or Female 
                 */
                //D2 is gender
                $gender = "";
                if(isset($arr_data[$key]["D"])){
                    if(empty($gender_array[$arr_data[$key]["D"]]) ){
                        $gender_status = false;
                        $gender = "";
                    }else{
                        $gender_status = true;
                        $gender = $gender_array[$arr_data[$key]["D"]];
                    }
                }

                //E2 is age
                $dob            = "";
                // if age null or
                if( $dob == "" || $dob == 0){
                    $dob = date('Y-m-d');                    
                }else{
                    // usually only age
                    $dob = date('Y') - $arr_data[$key]["E"];
                    $dob .= '-01-01';
                }
                //F is nationality
                
                if(!$arr_data[$key]["F"] == ""){
                    $nationality = "";
                }else{

                }
                //G is phone number
                $phone          = $arr_data[$key]["G"];
                // H is residence 
                $residence      = $arr_data[$key]["H"];
                
                //L is province                
                if(isset($arr_data[$key]["L"])){
                    $province   = collect($this->gazetteer_model->get_province_by_name($arr_data[$key]["L"])->first());
                    if($province){
                        $province_status = true;
                        $province_code = element('code', (array)$province);                        
                        
                    }else{
                        $province_status = false;
                        $province_msg = "ឈ្មោះខេត្តក្រុង ".$arr_data[$key]["L"]." ពុំមាននៅក្នុងប្រពន្ទ័";
                    }
                }
                $district_code = "";
                if(isset($province_code)){
                    // Get district Code
                    //K is district
                    $district   = collect($this->gazetteer_model->get_district_by_name($province_code, $arr_data[$key]["K"]))->first();
                    if($district){
                        $district_status = true;
                        $district_code   = element('code', (array)$district);
                    }else{
                        $district_status = false;
                        $district_msg    = "ឈ្មោះស្រុក/ខ័ណ្ឌ ".$arr_data[$key]["K"]." ពុំមាននៅក្នុងប្រពន្ទ័ទេ";
                    }

                }
                 //J is commune  
                 $commune_code = "";              
                if(isset($district_code)){
                    $commune    = collect($this->gazetteer_model->get_commune_by_name($district_code, $arr_data[$key]["J"]))->first();
                    if($commune){
                        $commune_status = true;
                        $commune_code   = element('code', (array)$commune);
                    }else{
                        $commune_status = false;
                        $commune_msg = "ពុំមាននៅក្នុងប្រពន្ធ័ទេ";
                    }
                   
                }
                // I is village
                $village_code        = "";
                if(isset($commune_code)){
                     $village    = collect($this->gazetteer_model->get_village_by_name(NULL, $arr_data[$key]["I"]))->first();
                     if($village){
                         $village_status = true;
                         $village_code = element('code', (array)$village);
                     }else{
                        $village_status = false;
                        $village_msg = "ពុំមាននៅក្នុងប្រពន្ធ័ទេ";
                     }
                }

                //M is country
                $country        = $arr_data[$key]["M"];
                //N is date_arrival
                $date_arrival   = $arr_data[$key]["N"];
                //O is passport_number
                $password_number = $arr_data[$key]["O"];

                $patient = array(
                    'patient_code'              => $patient_code, 
                    'patient_name'              => $patient_name, 
                    'sex'                       => $gender, 
                    'dob'                       => $dob, 
                    'phone'                     => $phone, 
                    'province'                  => $province_code, 
                    'district'                  => $district_code, 
                    'commune'                   => $commune_code, 
                    'village'                   => $village_code,
                    'residence'                 => $residence,
                    'nationality'               => $nationality,
                    'country'                   => $country,
                    'date_arrival'              => $date_arrival,
                    'passport_number'           => $password_number,
                    'seat_number'               => "",
                    'is_positive_covid'         => "",
                    'test_date'                 => "",
                    'is_contacted'              => "",
                    'contact_with'              => "",
                    'relationship_with_case'    => "",
                    'travel_in_past_30_days'    => "",
                    'flight_number'             => "",
                    'is_direct_contact'         => ""   
                );

                $patient_code = empty($patient_code) ? date('dmyHis') : strtoupper($patient_code);
                if (strpos($patient_code, $laboratory_code.'-') === 0) {
                    $patient_code = substr_replace($patient_code, '', 0, strlen($laboratory_code.'-'));
                }

                if (isPMRSPatientID($patient_code)) {
                    $msg = _t('patient_sample.msg.is_pmrs_patient');
                    $id_exist = TRUE;
                }
                else {
                    $patient_code = trim($laboratory_code).'-'.$patient_code;
                    $patient_code = str_replace(' ', '', $patient_code);

                    //Check Patient Code existent
                    $patient_info = $this->patient_model->get_outside_patient(FALSE, $patient_code);
                    if ($patient_info && !empty($patient_info['pid'])) {
                        $id_exist = TRUE;
                        $msg      = _t('patient.msg.patient_exist');
                    } else {
                        $patient['patient_code'] = $patient_code;
                        $patient = elements(['patient_code', 'patient_name', 'sex', 'dob', 'phone', 'province', 'district', 'commune', 'village',
                        'residence','nationality','country','date_arrival','passport_number','seat_number','is_positive_covid','test_date','is_contacted','contact_with','relationship_with_case','travel_in_past_30_days','flight_number','is_direct_contact'], $patient);
                        
                        $pid = $this->patient_model->save_outside_patient($patient);
                        if ($status = $pid > 0) {
                            $msg = _t('global.msg.save_success');
                            $patient_info = $this->patient_model->get_outside_patient($pid);
                        }
                    }
                }

            }
            
            /*
            
            */
        }else{
            $status             = false;
            $msg                = "Fail to upload the file";
        }
        echo json_encode(array('status' => $status, 'msg' => $msg, 'data' => $data, 'patient' => $patient));
    }


    public function add_line_list(){
        $this->app_language->load(['pages/patient_sample_entry']);
        $this->load->model('patient_sample_model', 'psample_model');
        $data               = $this->input->post('data');
        $msg                = "";
        $status             = false;
        $laboratory_code    = CamlisSession::getLabSession('lab_code');
        $patients           = array();
        $samples            = array();
        $result_status      = array();
        $sample_status      = array();
        $n                  = 1;
        foreach($data as $key => $row){
            /*
            0: "" លេខសំគាល់អ្នកជំងឺ patient_manual_code
            1: "ឈ្មោះសាកល្បង" ឈ្មោះ patient_name
            2: "40" អាយុ patient_dob
            3: "1" patient_sex
            4: "012888999" លេខទូរសព្ទ័ phone
            5: "1" ខេត្ត province
            6: "1" ស្រុក district
            7: "1" សង្កាត់ commune
            8: "1" ភូមិ village
            9: "កន្លែងស្នាក់នៅ"  residence
            10: "704" មកពីប្រទេស country
            11: "116" សញ្ញាតិ nationality
            12: "2021-03-23 00:00:00" ថ្ងៃខែមកដល់  date_arrival
            13: "PN00098" លិខិតឆ្លងដែន passport_number
            14: "FN224022021" លេខជើងហោះហើរ flight_number
            15: "SN009" លេខកៅអី seat_number
            16: false ធ្លាប់កើតកូវីឌ is_positive_covid
            17: "" ថ្ងៃតេស្ត  test_date
            18: false ជាអ្នកប៉ះពាល់ផ្ទាល់ is_contacted
            19: "" ឈ្មោះអ្នកជំងឺ contact_with
            20: false is_direct_contact
            21: "" sample_number
            22: "237" sample_source
            23: "1292" requester
            24: "2021-03-23 16:56:00" collected_date
            25: "2021-03-23 16:56:00" received_date
            26: "3" payment_type
            27: "2021-03-24 16:56:00" admission_date
            28: "រោគវិនិឆ្ឋ័យ" clinical_history
            29: true is_urgent
            30: "2" for_research
            31: "សំណាងជោកជ័យ" completed_by
            32: "099887766" phone_number
            33: "ដៃទេពក្តោបក្តាប់" sample_collector
            34: "078787878" phone_number_sample_collector
            35: "1;2;3;" clinical_symptom
            36: "" Test
            */
            


            // Check field required 
            // if patient_name is empty, just skip it 
            if($row[1] !== ""){
                if( isset($row[1]) && 
                    isset($row[2]) && 
                    isset($row[3]) && 
                    isset($row[5]) && 
                    isset($row[6]) &&
                    isset($row[7]) &&
                    isset($row[8])
                ){
                    $patient_code               = $row[0];                    
                    $patient_name               = $row[1];
                    $patient["patient_name"]    = $patient_name;
                    $isPMRSPatientID            = isPMRSPatientID($patient_code);
                    $isSavedPatient             = FALSE;
                
                    if ($isPMRSPatientID) {
                        $this->load->model('patient_model');                        

                        $province           = !empty($row[5]) ? $row[5] : -1;
                        $district           = !empty($row[6]) ? $row[6] : -1;
                        $commune            = !empty($row[7]) ? $row[7] : -1;
                        $village            = !empty($row[8]) ? $row[8] : -1;
                        $dob                = date('Y') - $row[2];
                        $dob               .= '-'.date("m-d");
                        $patient_sex        = $row[3];
                        $patient_phone      = ($row[4] !== "") ? $row[4] : null ;
                        
                        $phone = khNumberToLatinNumber($patient_phone);
                        if(strlen($phone) > 0){
                            $phone = khNumberToLatinNumber($patient_phone);
                            if(strlen($phone) > 10) $phone = substr($phone,0,10);
                        }else{                            
                            if(is_numeric($patient_phone)){
                                $phone = str_replace(' ', '', $patient_phone); // added 09 Feb 2021, phone column is limited 10 Chars we need to take out space 
                                $phone = preg_replace("/[^0-9]/", "",$phone);                                                        
                                if(strlen($phone) > 10) $phone = substr($phone,0,10);
                            }else{
                                $phone = str_replace(' ', '', $patient_phone); // added 09 Feb 2021, phone column is limited 10 Chars we need to take out space 
                                $phone = preg_replace("/[^0-9]/", "",$phone); 
                            }
                        }
                        
                        $isSavedPatient = $this->patient_model->save_pmrs_patient($patient_code, $patient_name, $patient_sex ,$dob, $phone, $province, $district, $commune, $village);
                        $patient["patient_code"]    = $patient_code;
                        $patient["msg"]             = "ជាអ្នកជំងឺPMRS";  
                        $patient["pstatus"]         = false;                      
                        $pid                        = $patient_code;
                    } else {

                        $patient_code = empty($patient_code) ? (date('dmyHis') + $n) : strtoupper($patient_code);
                        if (strpos($patient_code, $laboratory_code.'-') === 0) {
                            $patient_code = substr_replace($patient_code, '', 0, strlen($laboratory_code.'-'));
                        }
                        $patient_code = trim($laboratory_code).'-'.$patient_code;
                        $patient_code = str_replace(' ', '', $patient_code);
                        
                        //Check Patient Code existent
                        $patient_info        = $this->patient_model->get_outside_patient(FALSE, $patient_code);
                        $isPatientExist      = false;
                        if ($patient_info && !empty($patient_info['pid'])) {
                            $patient["msg"]         = _t('patient.msg.patient_exist');
                            $patient["pstatus"]     = false;
                            $pid                    = $patient_info['pid'];
                            $isPatientExist         = true;
                        }
                        // if patient is exist just upda
                        $dob                = date('Y') - $row[2];
                        $dob               .= '-'.date("m-d");
                        $patient_sex        = $row[3];
                        $phone              = ($row[4] !== "") ? $row[4] : null ;
                        $province           = $row[5];
                        $district           = $row[6];
                        $commune            = $row[7];
                        $village            = $row[8];
                        $residence          = $row[9];
                        $country            = ($row[10] !== "") ? $row[10] : null;
                        $nationality        = ($row[11] !== "") ? $row[11] : null;
                        $date_arrival       = ($row[12] !== "") ? date('Y-m-d', strtotime($row[12])): null;
                        $passport_number    = ($row[13] !== "") ? $row[13] : null;
                        $flight_number      = ($row[14] !== "") ? $row[14] : null;
                        $seat_number        = ($row[15] !== "") ? $row[15] : null;
                        $is_positive_covid  = ($row[16] == "true") ? $row[16] : null;
                        // if positive covid
                        $test_date          = null;
                        if($is_positive_covid == true){
                            $test_date      = ($row[17] !== "") ? date('Y-m-d', strtotime($row[17])) : null;
                        }
                        $is_contacted       = ($row[18] == "true") ? $row[18] : null;
                        $contact_with       = null;
                        $is_direct_contact  = null;
                        if($is_contacted == true){
                            $contact_with   = ($row[19] !== "") ? $row[19] : null;
                            $is_direct_contact = ($row[20] == "true") ? $row[20] : null;
                        }
                        $patient = array(
                            'patient_code'              => $patient_code, 
                            'patient_name'              => $patient_name, 
                            'sex'                       => $patient_sex, 
                            'dob'                       => $dob, 
                            'phone'                     => $phone, 
                            'province'                  => $province, 
                            'district'                  => $district, 
                            'commune'                   => $commune, 
                            'village'                   => $village,
                            'residence'                 => $residence,
                            'nationality'               => $nationality,
                            'country'                   => $country,
                            'date_arrival'              => $date_arrival,
                            'passport_number'           => $passport_number,
                            'seat_number'               => $seat_number,
                            'is_positive_covid'         => $is_positive_covid,
                            'test_date'                 => $test_date,
                            'is_contacted'              => $is_contacted,
                            'contact_with'              => $contact_with,
                            'relationship_with_case'    => null,
                            'travel_in_past_30_days'    => null,
                            'flight_number'             => $flight_number,
                            'is_direct_contact'         => $is_direct_contact  
                        );

                        if($isPatientExist == true){
                            if ($this->patient_model->update_outside_patient($pid, $patient)) {
                                $patient["msg"]         = _t('global.msg.save_success');
                                $patient["pstatus"]     = true;
                            }else{
                                $patient["msg"]         = "បញ្ជូលមិនបានជោគជ័យ";
                                $patient["pstatus"]     = false;
                                $pid                    = 0; // if pid does not exist;
                            }
                        }else{
                            // add new patient
                            $pid = $this->patient_model->save_outside_patient($patient);
                            if ($pid > 0) {                                
                                $patient["msg"]         = _t('global.msg.save_success');
                                $patient["pstatus"]     = true;
                            }else{
                                $patient["msg"]         = "បញ្ជូលមិនបានជោគជ័យ";
                                $patient["pstatus"]     = false;
                                $pid                    = 0; // if pid does not exist;
                            }
                        }
                    }

                    if(strlen($pid) > 0){
                        /**
                         * Insert Sample 
                         */
                        // Check fields required     
                        if(
                            isset($row[22]) &&
                            isset($row[23]) &&
                            isset($row[24]) &&
                            isset($row[25]) &&
                            isset($row[26])
                        ) {
                            $sample_number      = $row[21];
                            $sample_source_id   = ($row[22] !== "") ? $row[22] : null ;
                            $requester_id       = ($row[23] !== "") ? $row[23] : null ;
                            $collected_date     = ($row[24] !== "") ? date('Y-m-d', strtotime($row[24])) : null ;
                            $collected_time     = ($row[24] !== "") ? date('H:i', strtotime($row[24])).":00" : null ;
                            $received_date      = ($row[25] !== "") ? date('Y-m-d', strtotime($row[25])) : null ;
                            $received_time      = ($row[25] !== "") ? date('H:i', strtotime($row[25])).":00" : null ;
                            $payment_type_id    = ($row[26] !== "") ? $row[26] : null ;
                            $admission_date     = ($row[27] !== "") ? date('Y-m-d H:i', strtotime($row[27])).":00" : null ;
                            $clinical_history   = ($row[28] !== "") ? $row[28] : null ;
                            $is_urgent          = ($row[29] == "true") ? 1 : 0 ;
                            $for_research       = ($row[30] !== "") ? $row[30] : 0 ;
                            $completed_by       = ($row[31] !== "") ? $row[31] : null ;
                            $phone_number       = ($row[32] !== "") ? $row[32] : null ;
                            $sample_collector   = ($row[33] !== "") ? $row[33] : null ;
                            $phone_number_sample_collector  =   ($row[34] !== "") ? $row[34] : null ;
                            
                            $data = array(
                                'patient_id'            => $pid, 
                                'sample_source_id'      => $sample_source_id, 
                                'requester_id'          => $requester_id, 
                                'collected_date'        => $collected_date, 
                                'collected_time'        => $collected_time, 
                                'received_date'         => $received_date, 
                                'received_time'         => $received_time, 
                                'is_urgent'             => $is_urgent, 
                                'payment_type_id'       => $payment_type_id,  
                                'for_research'          => $for_research, 
                                'clinical_history'      => $clinical_history, 
                                'sample_number'         => $sample_number, 
                                'admission_date'        => $admission_date, 
                                'phone_number'          => $phone_number, 
                                'sample_collector'      => $sample_collector, 
                                'completed_by'          => $completed_by, 
                                'phone_number_sample_collector' => $phone_number_sample_collector
                            );
                            
                            $sample_number_type = CamlisSession::getLabSession('sample_number');
                            if ($sample_number_type == 2) {
                                $data['sample_number'] = $data["sample_number"].'-'.date('dmY');
                            } else {
                                $_r = $this->psample_model->get_psample_number();
                                $data['sample_number'] = $_r->sample_number;
                            }

                            if ($this->psample_model->is_unique_sample_number($data['sample_number'])) {
                                $psample_id = $this->psample_model->add_patient_sample($data);
                                $patient["sample_msg"]     = "ជោគជ័យ";
                                $patient["sample_status"]  = true;
                                $patient["sample_number"]  = $data['sample_number'];
                                // clinical_symptom
                                if(isset($row[35])){
                                    $this->load->model('clinical_symptom_model');
                                    $clinical_symptom = explode(";" , $row[35]);
                                    $clinical_symptom_data = array();
                                    for($i = 0 ; $i < (count($clinical_symptom) - 1); $i++){
                                        $clinical_symptom_data[] = array(
                                            'clinical_symptom_id'   => $clinical_symptom[$i],
                                            'patient_sample_id' 	=> $psample_id
                                        );
                                    }
                                    $this->clinical_symptom_model->add_clinical_symptom($clinical_symptom_data);
                                }                                                                        
                            } else {
                                $patient["sample_msg"]     = _t('sample.msg.sample_number_exist');
                                $patient["sample_status"]  = false;
                                $patient["sample_number"]  = $data['sample_number'];
                            }

                            if($psample_id > 0){
                                // Add Test Here
                                if(isset($row[36])){
                                    if($row[36] == 479){
                                        $sample_tests = array(479);
                                        $sample_details = array(
                                            "department_sample_id"  => 26, 
                                            "sample_description"    => "-1", 
                                            "first_weight"          => null, 
                                            "second_weight"         => null
                                        );
                                    }else if($row[36] == 497){
                                        $sample_tests = array(495,497);
                                        $sample_details = array(
                                            "department_sample_id"  => 29, 
                                            "sample_description"    => "-1", 
                                            "first_weight"          => null, 
                                            "second_weight"         => null
                                        );
                                    }
                                    $this->psample_model->assign_sample_test($psample_id, $sample_tests);
                                    $this->psample_model->set_psample_detail($psample_id, $sample_details);
                                    $patient["test_msg"]     = "បញ្ជូលតេស្តបានជោគជ័យ";
                                    $patient["test_status"]  = true;
                                }else{
                                    $patient["test_msg"]     = "តេស្តមិនបានជ្រើសរើស";
                                    $patient["test_status"]  = false;
                                }
                                
                                //End
                            }
                        }else{
                            $patient["sample_msg"]     = "ទិន្នន័យសំណាកមិនគ្រប់គ្រាន់";
                            $patient["sample_status"]  = false;
                            $patient["sample_number"]  = null;
                        }  
                        $samples[] = $data;
                    }
                    $n++;
                }
                $patients[]  = $patient; 
            }
        }
        echo json_encode(array('patients' => $patients , 'samples' => $samples , 'patient_status' => $result_status, 'sample_status' => $sample_status));
    }
}