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
		//$this->template->plugins->add(['DataTable', 'AutoComplete']);
        $this->template->plugins->add(array('DataTable', 'AutoComplete'));
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
            if(!isset($this->data['patient']['parent_code'])) $this->template->content_title .= "&nbsp;&nbsp;<button type='button' id='edit-patient' class='btn btn-primary btn-sm'><i class='fa fa-pencil-square-o'></i>&nbsp;" . _t('global.edit') . "</button>";
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
        $original_patient_code = $patient_code; //added 08 June 2021
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


            // 08-6-2021
            // Get Patient from Camlis
            if($patient == null){
                // Get if this patient already in the lab
                $patient = $this->patient_model->get_outside_patient(FALSE, $original_patient_code);
                if($patient == null){
                    $patient = $this->patient_model->get_camlis_patient($original_patient_code);
                    if($patient !== null){
                        $patient['is_camlis_patient'] = TRUE;
                    }
                    
                }                
                
            }
            //sha1()
        }
        //$ispmrs = isPMRSPatientID($patient_code);
        $data['result'] = json_encode(['patient' => $patient , 'patient_code'=> $patient_code]);
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
            //$patient_code = empty($patient['patient_manual_code']) ? date('dmyHis') : strtoupper($patient['patient_manual_code']);
            $patient_code = empty($patient['patient_manual_code']) ? date('ymdHis') : strtoupper($patient['patient_manual_code']);
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
                    'residence','nationality','country','date_arrival','passport_number','seat_number','is_positive_covid','test_date','is_contacted','contact_with','relationship_with_case','travel_in_past_30_days','flight_number','is_direct_contact','country_name'], $patient);
                    
                    $pid = $this->patient_model->save_outside_patient($patient);
                    if ($status = $pid > 0) {
                        // 16-06-2021
                        // Generate QR-CODE
                        $this->load->library('phpqrcode/Qrlib');
                        $SERVERFILEPATH = $_SERVER['DOCUMENT_ROOT'].'/assets/camlis/images/patient_qr_code/';
                        // Generate QR-Code
                        //file path for store images
                        $province       = $patient['province'] == null ? "" : $patient['province'];
                        $text           = 'name='.$patient['patient_name'].',phone='.$patient['phone'].',location='.$province.',pid='.$patient_code;
                        
                        //$text 			= $pid;
                        //$folder 		= $SERVERFILEPATH;
                        $file_name      = $patient_code.".png";
                        $file_path 		= $SERVERFILEPATH.$patient_code.".png";
                        QRcode::png($text,$file_path);
                        // End
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
                    'residence','nationality','country','date_arrival','passport_number','seat_number','flight_number','is_positive_covid','test_date','is_contacted','contact_with','relationship_with_case','travel_in_past_30_days','is_direct_contact','country_name'], $patient);
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
        $status                     = false;
        $msg                        = "";
        $file_element_name          = 'theExcelFile';
        $data                       = array();
        $laboratory_code            = CamlisSession::getLabSession('lab_code');
        $config['upload_path']      = $_SERVER['DOCUMENT_ROOT'].'/assets/camlis/upload/';
        //$config['upload_path']      = $_SERVER['DOCUMENT_ROOT'].'/camlis/assets/camlis/upload/';
        $config['allowed_types']    = 'xlsx|xls';
        $config['max_size']         = 1024 * 8;
        $config['encrypt_name']     = TRUE;
        
        ini_set('memory_limit', '512M');
        ini_set('max_execution_time', 3600);

        $col = array(
                "patient_code"                    => "A",
                "patient_name"                    => "B",
                "age"                             => "C",
                "gender"                          => "D",
                "phone"                           => "E",
                "province"                        => "F",
                "district"                        => "G",
                "commune"                         => "H",
                "village"                         => "I",
                "residence"                       => "J",
                "reason_for_testing"              => "K",
                "is_contacted"                    => "L",
                "is_contacted_with"               => "M",
                "is_directed_contact"             => "N",
                "sample_number"                   => "O",
                "sample_source"                   => "P",
                "requester"                       => "Q",
                "collected_date"                  => "R",
                "received_date"                   => "S",                           
                "diagnosis"                       => "T",
                "completed_by"                    => "U",
                "phone_completed_by"              => "V",
                "sample_collector"                => "W",
                "phone_number_sample_collctor"    => "X",
                "clinical_symptom"                => "Y",
                "health_facility"                 => "Z",
                "test_name"                       => "AA",
                "machine_name"                    => "AB",        
                "test_result"                     => "AC",
                "test_result_date"                => "AD",
                "perform_by"                      => "AE",
                "country"                         => "AF",
                "nationality"                     => "AG",
                "arrival_date"                    => "AH",
                "passport_number"                 => "AI",
                "flight_number"                   => "AJ",
                "seat_number"                     => "AK",
                "is_positive_covid"               => "AL",
                "test_date"                       => "AM",
                "number_of_sample"                => "AN"
        );
        $content = array();
        $this->load->library('upload',$config);
        if($this->upload->do_upload("theExcelFile")){
            $data                   = array('upload_data' => $this->upload->data());           
            $status                 = true;
            $msg                    = "Upload successful";

            $file_name  = $_SERVER['DOCUMENT_ROOT'].'/assets/camlis/upload/'.$data['upload_data']['file_name'];
            //$file_name  = $_SERVER['DOCUMENT_ROOT'].'/camlis/assets/camlis/upload/'.$data['upload_data']['file_name'];
            //load the excel library
            $this->load->library('phptoexcel');
            
            //read file from path
            $objPHPExcel = PHPExcel_IOFactory::load($file_name);
            
            //get only the Cell Collection
            $cell_collection = $objPHPExcel->getActiveSheet()->getCellCollection();

            //extract to a PHP readable array format
            $tableHeaderString = '';
            $tableBodyString   = '';
            $dt = array();
            foreach ($cell_collection as $cell) {
                $column     = $objPHPExcel->getActiveSheet()->getCell($cell)->getColumn();
                $row        = $objPHPExcel->getActiveSheet()->getCell($cell)->getRow();
                $data_value = $objPHPExcel->getActiveSheet()->getCell($cell)->getValue();
                $patient_name  = $objPHPExcel->getActiveSheet()->getCell("B".$row)->getValue(); 
                //The header will/should be in row 1 only. 
                if ($row == 1 || $row == 2) {
                    $header[$row][$column] = $data_value;
                } else {
                    $dt[$row][$column] = $patient_name;
                    if($patient_name !== null){
                        $arr_data[$row][$column] = $data_value;
                    }
                }
            }
            //send the data in an array format
            //$data['header'] = $dt;
            $data['values'] = $arr_data;
            
            /*
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
                $tableHeaderString .= "<th>".$header[$ind]["P"]."</th>";
                $tableHeaderString .= "<th>".$header[$ind]["Q"]."</th>";
                $tableHeaderString .= "<th>".$header[$ind]["R"]."</th>";
                $tableHeaderString .= "<th>".$header[$ind]["S"]."</th>";
                $tableHeaderString .= "<th>".$header[$ind]["T"]."</th>";
                $tableHeaderString .= "<th>".$header[$ind]["U"]."</th>";
            }
            
            $counter = 1;
            foreach($arr_data as $ind => $item){
                $counter++;
                //$tableBodyString .= $cell[$ind];
                    //$tableBodyString .= $item["A"];
                    $tableBodyString .= $item;
                    
                   $content[] = $item;
            }
            */
            unlink($file_name); 
        }else{
            $status             = false;
            $msg                = "Fail to upload the file";
        }
       // echo json_encode(array('status' => $status, 'msg' => $msg, 'data' => $content));
       echo json_encode(array('status' => $status, 'msg' => $msg, 'data' => $data));
    }

    public function add_line_list_short_form(){
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
        $result_test        = array();
        $col = array(
            "patient_code"        => 0,
            "patient_name"        => 1,
            "age"                 => 2,
            "gender"              => 3,
            "phone"               => 4,
            "province"            => 5,
            "district"            => 6,
            "commune"             => 7,
            "village"             => 8,
            "residence"           => 9,
            "reason_for_testing"  => 10,
            "is_contacted"        => 11,
            "is_contacted_with"   => 12,
            "is_directed_contact" => 13,
            "sample_number"       => 14,
            "sample_source"       => 15,
            "requester"           => 16,
            "collected_date"      => 17,
            "received_date"       => 18,
            "payment_type"        => 19,
            "admission_date"      => 20,
            "diagnosis"           => 21,
            "is_urgent"           => 22,
            "completed_by"        => 23,
            "phone_completed_by"  => 24,
            "sample_collector"    => 25,
            "phone_number_sample_collctor" => 26,
            "clinical_symptom"    => 27,
            "test_name"           => 28,
            "test_result"         => 29,
            "test_result_date"    => 30,
            "perform_by"          => 31,
            "country"             => 32,
            "nationality"         => 33,
            "arrival_date"        => 34,
            "passport_number"     => 35,
            "flight_number"       => 36,
            "seat_number"         => 37,
            "is_positive_covid"   => 38,
            "test_date"           => 39,
            "sex_id"              => 40,
            "province_code"       => 41,
            "district_code"       => 42,
            "commune_code"        => 43,
            "village_code"        => 44,
            "reason_for_testing_id" => 45,
            "sample_source_id"    => 46,
            "requester_id"        => 47,
            "clinical_symtop_id"  => 48,
            "test_id"             => 49,
            "country_id"          => 50,
            "nationality_id"      => 51,
            "number_of_sample"    => 52,
            "performer_by_id"     => 53
        );
                            
        ini_set('memory_limit', '512M');
        ini_set('max_execution_time', 1200);
        
        foreach($data as $key => $row){                        
            // Check field required 
            // if patient_name is empty, just skip it 
            if(!empty($row[$col["patient_name"]])){
                if( isset($row[$col["patient_name"]]) && 
                    isset($row[$col['age']]) && 
                    isset($row[$col["gender"]]) && 
                    isset($row[$col["province"]]) && 
                    isset($row[$col["district"]]) &&
                    isset($row[$col["commune"]]) &&
                    isset($row[$col["village"]])
                ){
                    
                    $patient_code               = trim($row[$col["patient_code"]]);
                    $patient_name               = trim($row[$col["patient_name"]]);
                    $patient["patient_name"]    = $patient_name;
                    $isPMRSPatientID            = isPMRSPatientID($patient_code);
                    $isSavedPatient             = FALSE;
                
                    if ($isPMRSPatientID) {
                        $this->load->model('patient_model');                                            
                        
                        $province           = !empty($row[$col["province_code"]]) ? $row[$col["province_code"]] : -1;
                        $district           = !empty($row[$col["district_code"]]) ? $row[$col["district_code"]] : -1;
                        $commune            = !empty($row[$col["commune_code"]]) ? $row[$col["commune_code"]] : -1;
                        $village            = !empty($row[$col["village_code"]]) ? $row[$col["village_code"]] : -1;
                        $dob                = date('Y') - $row[$col["age"]];
                        $dob               .= '-'.date("m-d");                        
                        $patient_sex        = $row[$col["sex_id"]] == 1 ? "M" : "F";
                        $patient_phone      = (trim($row[$col["phone"]]) !== "") ? trim($row[$col["phone"]]) : null ;

                        $phone = $patient_phone;

                        /*
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
                        */

                        $isSavedPatient = $this->patient_model->save_pmrs_patient($patient_code, $patient_name, $patient_sex ,$dob, $phone, $province, $district, $commune, $village);
                        $patient["patient_code"]    = $patient_code;
                        $patient["msg"]             = _t('patient.msg.is_pmrs');
                        $patient["pstatus"]         = false;                      
                        $pid                        = $patient_code;
                    } else {

                        //$patient_code = empty($patient_code) ? (date('dmyHis') + $n) : strtoupper($patient_code);
                        $patient_code = empty($patient_code) ? (date('ymdHis') + $n) : strtoupper($patient_code); // 11-06-2021
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
                        
                        // if patient is exist just update
                        /**
                         * added 29-04-2021
                         * check last char if it is M or D
                         */
                        $char = substr($row[2],strlen($row[$col["age"]])-1 , strlen($row[$col["age"]]));
                        // if it is day
                        if(strtolower($char) == "m"){
                            $monthVal = substr($row[$col["age"]],0, strlen($row[$col["age"]])-1);
                            $cYear  = date('Y');
                            $cMonth = date('n'); // month without leading 0 
                            $cDay   = date('d');
                            $rMonth = ($cMonth + (12 - intval($monthVal)));
                            $rMonth = ($rMonth > 12 ) ? ($rMonth - 12) : $rMonth;    
                            if($monthVal > $cMonth){
                                $cYear--;
                            }
                            if(strlen($rMonth) == 1){
                                $rMonth = "0".$rMonth;
                            }
                            $dob    = $cYear."-".$rMonth."-".$cDay;
                        }else if(strtolower($char) == "d"){
                            $dayVal = substr($row[$col["age"]],0, strlen($row[$col["age"]])-1);
                            $cYear  = date('Y');
                            $cMonth = date('n'); // month without leading 0 
                            $cDay   = date('d');

                            $rDay = ($cDay + (31 - intval($dayVal)));
                            $rDay = ($rDay > 31 ) ? ($rDay - 31) : $rDay;  
                            if($rDay > $cDay){
                                $cMonth--;
                            }
                            if(strlen($cMonth) == 1){
                                $cMonth = "0".$cMonth;
                            }
                            if(strlen($rDay) == 1){
                                $rDay = "0".$rDay;
                            }
                            $dob    = $cYear."-".$cMonth."-".$rDay;
                        }else{
                            $dob                = date('Y') - $row[2];
                            $dob               .= '-'.date("m-d");
                        }
                        
                        
                        $patient_sex        = $row[$col["sex_id"]];
                        $patient_phone      = (trim($row[$col["phone"]]) !== "") ? trim($row[$col["phone"]]) : null ;

                        $phone              = $patient_phone;

                        /*
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
                        */

                        $province           = $row[$col["province_code"]];
                        $district           = $row[$col["district_code"]];
                        $commune            = $row[$col["commune_code"]];
                        $village            = $row[$col["village_code"]];
                        $residence          = trim($row[$col["residence"]]);

                        $country            = ($row[$col["country_id"]] !== "") ? $row[$col["country_id"]] : null;
                        $nationality        = ($row[$col["nationality_id"]] !== "") ? $row[$col["nationality_id"]] : null;
                        $date_arrival       = ($row[$col["arrival_date"]] !== "") ? date('Y-m-d', strtotime($row[$col["arrival_date"]])): null;
                        $passport_number    = ($row[$col["passport_number"]] !== "") ? $row[$col["passport_number"]] : null;
                        $flight_number      = ($row[$col["flight_number"]] !== "") ? $row[$col["flight_number"]] : null;
                        $seat_number        = ($row[$col["seat_number"]] !== "") ? $row[$col["seat_number"]] : null;
                        $is_positive_covid  = ($row[$col["is_positive_covid"]] == "true") ? $row[$col["is_positive_covid"]] : null;
                        // if positive covid
                        $test_date          = null;
                        if($is_positive_covid == true){
                            $test_date      = ($row[$col["test_date"]] !== "") ? date('Y-m-d', strtotime($row[$col["test_date"]])) : null;
                        }
                        $is_contacted       = ($row[$col["is_contacted"]] == "true") ? $row[$col["is_contacted"]] : null;
                        $contact_with       = null;
                        $is_direct_contact  = null;
                        if($is_contacted == true){
                            $contact_with   = ($row[$col["is_contacted_with"]] !== "") ? $row[$col["is_contacted_with"]] : null;
                            $is_direct_contact = ($row[$col["is_directed_contact"]] == "true") ? $row[$col["is_directed_contact"]] : null;
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
                            'passport_number'           => trim($passport_number),
                            'seat_number'               => trim($seat_number),
                            'is_positive_covid'         => $is_positive_covid,
                            'test_date'                 => $test_date,
                            'is_contacted'              => $is_contacted,
                            'contact_with'              => $contact_with,
                            'relationship_with_case'    => null,
                            'travel_in_past_30_days'    => null,
                            'flight_number'             => trim($flight_number),
                            'is_direct_contact'         => $is_direct_contact  
                        );

                        if($isPatientExist == true){
                            if ($this->patient_model->update_outside_patient($pid, $patient)) {
                                $patient["msg"]         = _t('global.msg.save_success');
                                $patient["pstatus"]     = true;
                            }else{
                                $patient["msg"]         = _t('global.msg.save_fail');
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
                                $patient["msg"]         = _t('global.msg.save_fail');
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
                            isset($row[$col["sample_source"]]) &&
                            isset($row[$col["requester"]]) &&
                            isset($row[$col["collected_date"]]) &&
                            isset($row[$col["collected_date"]]) 
                        ) {                            
                            $sample_number      = $row[$col["sample_number"]];
                            $sample_source_id   = ($row[$col["sample_source_id"]] !== "") ? $row[$col["sample_source_id"]] : null ;
                            $requester_id       = ($row[$col["requester_id"]] !== "") ? $row[$col["requester_id"]] : null ;
                            $collected_date     = ($row[$col["collected_date"]] !== "") ? date('Y-m-d', strtotime($row[$col["collected_date"]])) : null ;
                            $collected_time     = ($row[$col["collected_date"]] !== "") ? date('H:i', strtotime($row[$col["collected_date"]])).":00" : null ;
                            $received_date      = ($row[$col["received_date"]] !== "") ? date('Y-m-d', strtotime($row[$col["received_date"]])) : null ;
                            $received_time      = ($row[$col["received_date"]] !== "") ? date('H:i', strtotime($row[$col["received_date"]])).":00" : null ;
                            //$payment_type_id    = ($row[19] !== "") ? $row[19] : null ;
                            $payment_type_id    = 1 ;
                            $admission_date     = ($row[$col["admission_date"]] !== "") ? date('Y-m-d H:i', strtotime($row[$col["admission_date"]])).":00" : null ;
                            $clinical_history   = ($row[$col["diagnosis"]] !== "") ? $row[$col["diagnosis"]] : null ;
                            $is_urgent          = ($row[$col["is_urgent"]] == "true") ? 1 : 0 ;
                            $for_research       = ($row[$col["reason_for_testing_id"]] !== "") ? $row[$col["reason_for_testing_id"]] : 0 ;
                            $completed_by       = ($row[$col["completed_by"]] !== "") ? $row[$col["completed_by"]] : null ;
                            $phone_number       = ($row[$col["phone_completed_by"]] !== "") ? $row[$col["phone_completed_by"]] : null ;
                            $sample_collector   = ($row[$col["sample_collector"]] !== "") ? $row[$col["sample_collector"]] : null ;
                            $phone_number_sample_collector  =   ($row[$col["phone_number_sample_collctor"]] !== "") ? $row[$col["phone_number_sample_collctor"]] : null ;
                            
                            $number_of_sample   = ($row[$col["number_of_sample"]] !== "" || (is_int($row[$col["number_of_sample"]]) == 1) ) ? $row[$col["number_of_sample"]] : null ;
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
                                'phone_number_sample_collector' => $phone_number_sample_collector,
                                'number_of_sample'      => $number_of_sample
                            );
                            
                            $sample_number_type = CamlisSession::getLabSession('sample_number');
                            if ($sample_number_type == 2) {
                                $data['sample_number'] = $data["sample_number"].'-'.date('dmY');
                            } else {
                                $_r = $this->psample_model->get_psample_number();
                                $data['sample_number'] = $_r->sample_number;
                            }
                            $psample_id = 0;
                            if ($this->psample_model->is_unique_sample_number($data['sample_number'])) {
                                $psample_id = $this->psample_model->add_patient_sample($data);
                                $patient["sample_msg"]     = _t('global.msg.save_success');
                                $patient["sample_status"]  = true;
                                $patient["sample_number"]  = $data['sample_number'];
                                $patient["psample_id"]     = $psample_id;
                                // clinical_symptom
                                
                                if($row[$col["clinical_symptom"]] !== ""){
                                    $this->load->model('clinical_symptom_model');
                                    $clinical_symptom = explode(";" , $row[$col["clinical_symtop_id"]]);
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
                            
                            if($psample_id > 0 || $patient["sample_status"]){
                                // Add Test Here
                                if(isset($row[$col["test_name"]])){
                                    if($row[$col["test_id"]] !== ""){
                                        if($row[$col["test_id"]] == 479){
                                            $sample_tests = array(479);
                                            $sample_details = array(
                                                "department_sample_id"  => 26, 
                                                "sample_description"    => "-1", 
                                                "first_weight"          => null, 
                                                "second_weight"         => null
                                            );
                                        }else if($row[$col["test_id"]] == 497){
                                            $sample_tests = array(495,497);
                                            $sample_details = array(
                                                "department_sample_id"  => 29, 
                                                "sample_description"    => "-1", 
                                                "first_weight"          => null, 
                                                "second_weight"         => null
                                            );
                                        }else if($row[$col["test_id"]] == 505){
                                            $sample_tests = array(505); // id table patient_sample_tests
                                            $sample_details = array(
                                                "department_sample_id"  => 17, 
                                                "sample_description"    => "-1", 
                                                "first_weight"          => null, 
                                                "second_weight"         => null
                                            );
                                        }
                                        if($row[$col["test_id"]] == 497){
                                            $this->psample_model->assign_sample_test($psample_id, $sample_tests);
                                            // get patient_test_id
                                            $res = $this->psample_model->get_patient_sample_test($psample_id);
                                            $patient_test_ids = array_column($res, 'patient_test_id');
                                            $patient_test_id = $patient_test_ids[1];
                                        }else{
                                            $patient_test_id = $this->psample_model->assign_single_sample_test($psample_id, $sample_tests);
                                        }
                                       
                                        $this->psample_model->set_psample_detail($psample_id, $sample_details);
                                       
                                        $patient["test_msg"]     = _t('global.msg.save_success');
                                        $patient["test_status"]  = true;
                                        // 
                                        /**
                                         * added 01-05-2021
                                         * Add test result 
                                         */
                                        if(isset($row[$col["test_result"]])){
                                            $this->load->model(['result_model']);
                                            $organism_antibiotic_result = [];
                                            if($row[$col["test_id"]] == 479){
                                                $result = $row[$col["test_result"]] == "Negative" ? 4848 : 4849;
                                            }else if($row[$col["test_id"]] == 497){
                                                $result = $row[$col["test_result"]] == "Negative" ? 4858 : 4859;
                                            }else{
                                                $result = $row[$col["test_result"]] == "Negative" ? 4865 : 4864;
                                            }

                                            //SARS-CoV-2 (Applied Biosystems 7500 Fast Real Time PCR)
                                            if($row[$col["test_result_date"]] !== "" && $row[$col["performer_by_id"] !== ""]){
                                                $result_test_date = ($row[$col["test_result_date"]] !== "") ? date('Y-m-d', strtotime($row[$col["test_result_date"]])) : null ;
                                                                                            
                                                $organism_antibiotic_result[] = [
                                                    'patient_sample_id' => $psample_id,
                                                    'patient_test_id'   => $patient_test_id,
                                                    'performer_id'      => $row[$col["performer_by_id"]],
                                                    'machine_name'      => "",
                                                    'test_date'         => $result_test_date,
                                                    'result'            => $result,
                                                    'type'              => 1,
                                                    'quantity_id'       => -1,
                                                    'contaminant'       => 0,
                                                    'antibiotic'        => array()
                                                ];
                                                if (count($organism_antibiotic_result) > 0) $this->result_model->set_ptest_organism_antibiotic_result($psample_id, $organism_antibiotic_result);
                                                $patient["test_result_msg"]     = _t('global.msg.save_success');
                                                $patient["test_result_status"]  = true;
                                                $this->psample_model->update_progress_status($psample_id);
                                            }else{
                                                $patient["test_result_msg"]     = _t('global.msg.result_not_selected');
                                                $patient["test_result_status"]  = false;
                                            }
                                        }
                                    }else{
                                        $patient["test_msg"]     = _t('global.msg.result_not_selected');
                                        $patient["test_status"]  = false;
                                    }
                                }else{
                                    $patient["test_msg"]     = _t('global.msg.result_not_selected');
                                    $patient["test_status"]  = false;
                                }
                                //End
                            }
                        }else{
                            $patient["sample_msg"]     = _t('global.msg.field_for_sample_required');
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

    public function add_line_list_full_form(){
        $this->app_language->load(['pages/patient_sample_entry']);
        $this->load->model('patient_sample_model', 'psample_model');
        $this->load->library('phpqrcode/Qrlib');
        $SERVERFILEPATH = $_SERVER['DOCUMENT_ROOT'].'/assets/camlis/images/patient_qr_code/';
        $start_time         = microtime(true); 
        $data               = $this->input->post('data');
        $msg                = "";
        $status             = false;
        $laboratory_code    = CamlisSession::getLabSession('lab_code');
        $patients           = array();
        $samples            = array();
        $result_status      = array();
        $sample_status      = array();
        $n                  = 1;
        $result_test        = array();
        
        $col = array(
            "patient_code"        => 0,
            "patient_name"        => 1,
            "age"                 => 2,
            "gender"              => 3,
            "phone"               => 4,
            "province"            => 5,
            "district"            => 6,
            "commune"             => 7,
            "village"             => 8,
            "residence"           => 9,
            "reason_for_testing"  => 10,
            "is_contacted"        => 11,
            "is_contacted_with"   => 12,
            "is_directed_contact" => 13,
            "sample_number"       => 14,
            "sample_source"       => 15,
            "requester"           => 16,
            "collected_date"      => 17,
            "received_date"       => 18,
            "payment_type"        => 19,
            "admission_date"      => 20,
            "diagnosis"           => 21,
            "is_urgent"           => 22,
            "completed_by"        => 23,
            "phone_completed_by"  => 24,
            "sample_collector"    => 25,
            "phone_number_sample_collctor" => 26,
            "clinical_symptom"    => 27,
            "health_facility"     => 28,
            "test_name"           => 29,
            "machine_name"        => 30,
            "test_result"         => 31,
            
            "test_result_date"    => 32,
            "perform_by"          => 33,
            "country"             => 34,
            "nationality"         => 35,
            "arrival_date"        => 36,
            "passport_number"     => 37,
            "flight_number"       => 38,
            "seat_number"         => 39,
            "is_positive_covid"   => 40,

            "test_date"           => 41,
            "sex_id"              => 42,
            "province_code"       => 43,
            "district_code"       => 44,
            "commune_code"        => 45,
            "village_code"        => 46,
            "reason_for_testing_id" => 47,
            "sample_source_id"    => 48,
            "requester_id"        => 49,
            "clinical_symtop_id"  => 50,
            "test_id"             => 51,
            "country_id"          => 52,
            "nationality_id"      => 53,
            "number_of_sample"    => 54,
            "performer_by_id"     => 55,
            "test_result_id"      => 56,
            "is_camlis_patient"   => 57
        );
            
        ini_set('memory_limit', '512M');
        ini_set('max_execution_time', 1200); // Zero means the script can run forever
        
        foreach($data as $key => $row){
            // Check field required 
            if(!empty($row[$col["patient_name"]])){
                if( isset($row[$col["patient_name"]]) && 
                    isset($row[$col['age']]) && 
                    isset($row[$col["gender"]]) && 
                    isset($row[$col["province"]]) && 
                    isset($row[$col["district"]]) &&
                    isset($row[$col["commune"]]) &&
                    isset($row[$col["village"]])
                ){
                    
                    $patient_code               = trim($row[$col["patient_code"]]);
                    $patient_name               = trim($row[$col["patient_name"]]);
                    $patient["patient_name"]    = $patient_name;
                    $isPMRSPatientID            = isPMRSPatientID($patient_code);
                    $isSavedPatient             = FALSE;
                    $isCamlisPatient            = ($row[$col["is_camlis_patient"]] == "" || $row[$col["is_camlis_patient"]] == 0) ? false : true; //22062021
                    $original_patient_code      = trim($row[$col["patient_code"]]); //22062021

                    if ($isPMRSPatientID) {
                        $this->load->model('patient_model');                                            
                        
                        $province           = !empty($row[$col["province_code"]]) ? $row[$col["province_code"]] : -1;
                        $district           = !empty($row[$col["district_code"]]) ? $row[$col["district_code"]] : -1;
                        $commune            = !empty($row[$col["commune_code"]]) ? $row[$col["commune_code"]] : -1;
                        $village            = !empty($row[$col["village_code"]]) ? $row[$col["village_code"]] : -1;
                        $dob                = date('Y') - $row[$col["age"]];
                        $dob               .= '-'.date("m-d");
                        $patient_sex        = $row[$col["sex_id"]] == 1 ? "M" : "F";
                        $patient_phone      = (trim($row[$col["phone"]]) !== "") ? trim($row[$col["phone"]]) : null;

                        $phone              = $patient_phone;

                        $isSavedPatient             = $this->patient_model->save_pmrs_patient($patient_code, $patient_name, $patient_sex ,$dob, $phone, $province, $district, $commune, $village);
                        $patient["patient_code"]    = $patient_code;
                        $patient["msg"]             = _t('patient.msg.is_pmrs');
                        $patient["pstatus"]         = false;
                        $pid                        = $patient_code;
                    } else {
                        // Check Length of each column
                        //$patient_code = empty($patient_code) ? (date('dmyHis') + $n) : strtoupper($patient_code);
                        $patient_code = empty($patient_code) ? (date('ymdHis') + $n) : strtoupper($patient_code);
                        
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
                        

                        // if patient is exist just update
                        /**
                         * added 29-04-2021
                         * check last char if it is M or D
                         */
                        $char = substr($row[2],strlen($row[$col["age"]])-1 , strlen($row[$col["age"]]));
                        // if it is day
                        if(strtolower($char) == "m"){
                            $monthVal = substr($row[$col["age"]],0, strlen($row[$col["age"]])-1);
                            $cYear  = date('Y');
                            $cMonth = date('n'); // month without leading 0 
                            $cDay   = date('d');
                            $rMonth = ($cMonth + (12 - intval($monthVal)));
                            $rMonth = ($rMonth > 12 ) ? ($rMonth - 12) : $rMonth;    
                            if($monthVal > $cMonth){
                                $cYear--;
                            }
                            if(strlen($rMonth) == 1){
                                $rMonth = "0".$rMonth;
                            }
                            $dob    = $cYear."-".$rMonth."-".$cDay;
                        }else if(strtolower($char) == "d"){
                            $dayVal = substr($row[$col["age"]],0, strlen($row[$col["age"]])-1);
                            $cYear  = date('Y');
                            $cMonth = date('n'); // month without leading 0 
                            $cDay   = date('d');

                            $rDay = ($cDay + (31 - intval($dayVal)));
                            $rDay = ($rDay > 31 ) ? ($rDay - 31) : $rDay;  
                            if($rDay > $cDay){
                                $cMonth--;
                            }
                            if(strlen($cMonth) == 1){
                                $cMonth = "0".$cMonth;
                            }
                            if(strlen($rDay) == 1){
                                $rDay = "0".$rDay;
                            }
                            $dob    = $cYear."-".$cMonth."-".$rDay;
                        }else{
                            $dob            = date('Y') - $row[2];
                            $dob           .= '-'.date("m-d");
                        }

                        $patient_sex        = $row[$col["sex_id"]];
                        $patient_phone      = (trim($row[$col["phone"]]) !== "") ? trim($row[$col["phone"]]) : null ;
                        $phone              = $patient_phone;
                        $province           = $row[$col["province_code"]];
                        $district           = $row[$col["district_code"]];
                        $commune            = $row[$col["commune_code"]];
                        $village            = $row[$col["village_code"]];
                        $residence          = trim($row[$col["residence"]]);
                        $country            = ($row[$col["country_id"]] !== "") ? $row[$col["country_id"]] : null;
                        $country_name       = ($row[$col["country"]] !== "") ? $row[$col["country"]] : null;
                        $nationality        = ($row[$col["nationality_id"]] !== "") ? $row[$col["nationality_id"]] : null;
                        $date_arrival       = ($row[$col["arrival_date"]] !== "") ? date('Y-m-d', strtotime($row[$col["arrival_date"]])): null;
                        $passport_number    = ($row[$col["passport_number"]] !== "") ? $row[$col["passport_number"]] : null;
                        $flight_number      = ($row[$col["flight_number"]] !== "") ? $row[$col["flight_number"]] : null;
                        $seat_number        = ($row[$col["seat_number"]] !== "") ? $row[$col["seat_number"]] : null;
                        $is_positive_covid  = ($row[$col["is_positive_covid"]] == "true") ? $row[$col["is_positive_covid"]] : null;
                        // if positive covid
                        $test_date          = null;
                        if($is_positive_covid == true){
                            $test_date      = ($row[$col["test_date"]] !== "") ? date('Y-m-d', strtotime($row[$col["test_date"]])) : null;
                        }
                        $is_contacted       = ($row[$col["is_contacted"]] == "true") ? $row[$col["is_contacted"]] : null;
                        $contact_with       = null;
                        $is_direct_contact  = null;
                        if($is_contacted == true){
                            $contact_with   = ($row[$col["is_contacted_with"]] !== "") ? $row[$col["is_contacted_with"]] : null;
                            $is_direct_contact = ($row[$col["is_directed_contact"]] == "true") ? $row[$col["is_directed_contact"]] : null;
                        }
                        //if($isCamlisPatient){ $patient_code = $original_patient_code; }
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
                            'passport_number'           => trim($passport_number),
                            'seat_number'               => trim($seat_number),
                            'is_positive_covid'         => $is_positive_covid,
                            'test_date'                 => $test_date,
                            'is_contacted'              => $is_contacted,
                            'contact_with'              => $contact_with,
                            'relationship_with_case'    => null,
                            'travel_in_past_30_days'    => null,
                            'flight_number'             => trim($flight_number),
                            'is_direct_contact'         => $is_direct_contact,
                            'country_name'              => $country_name
                        );

                        if($isPatientExist == true){
                            if ($this->patient_model->update_outside_patient($pid, $patient)) {
                                $patient["msg"]         = _t('global.msg.save_success');
                                $patient["pstatus"]     = true;
                            }else{
                                $patient["msg"]         =  _t('global.msg.save_fail');
                                $patient["pstatus"]     = false;
                                $pid                    = 0; // if pid does not exist;
                            }
                        }else if ($isCamlisPatient){
                            // Added 22062021                            
                            $patient_info               = $this->patient_model->get_outside_patient(FALSE, $original_patient_code);
                            if ($patient_info) {
                                $patient["msg"]         = _t('patient.msg.patient_exist');
                                $patient["pstatus"]     = false;
                                $pid                    = $patient_info['pid'];
                            }else{
                                $patient['parent_code'] = $patient_code;
                                $pid                    = $this->patient_model->save_outside_patient($patient);
                                if($pid > 0) {
                                    $patient["msg"]         = _t('global.msg.save_success');
                                    $patient["pstatus"]     = true;
                                }else{
                                    $patient["msg"]         =  _t('global.msg.save_fail');
                                    $patient["pstatus"]     = false;
                                    $pid                    = 0; // if pid does not exist;
                                }
                            }
                        }else{
                            //Check if QR-CODE exist
                            // add new patient
                            $pid = $this->patient_model->save_outside_patient($patient);
                            if ($pid > 0) {
                                $patient["msg"]         = _t('global.msg.save_success');
                                $patient["pstatus"]     = true;
                            }else{
                                $patient["msg"]         =  _t('global.msg.save_fail');
                                $patient["pstatus"]     = false;
                                $pid                    = 0; // if pid does not exist;
                                $patient["qr_code_status"]    = false;
                            }
                        }
                    }

                    if(strlen($pid) > 0){
                        // Check
                        if($isCamlisPatient){
                            $file_name = $original_patient_code.'.png';
                            $text           = 'name='.$patient_name.',phone='.$phone.',location='.$province.',pid='.$original_patient_code;
                        }else{
                            $file_name = $patient_code.'.png';
                            $text           = 'name='.$patient_name.',phone='.$phone.',location='.$province.',pid='.$original_patient_code;
                        }
                        
                        if (file_exists($SERVERFILEPATH.$file_name)) {
                            $patient["qr_code"]             = site_url()."/assets/camlis/images/patient_qr_code/".$file_name;
                            $patient["qr_code_status"]      = true;
                        }else{
                            $province       = $row[$col["province"]] == null ? "" : $row[$col["province"]];                                                        
                            $file_path 		= $SERVERFILEPATH.$file_name;
                            QRcode::png($text,$file_path);
                            $patient["qr_code"]     = site_url()."/assets/camlis/images/patient_qr_code/".$file_name;
                            $patient["qr_code_status"]    = true;
                        }
                        //End 

                        /**
                         * Insert Sample 
                         */                        
                        // Check fields required     
                        if(
                            isset($row[$col["sample_source"]]) &&
                            isset($row[$col["requester"]]) &&
                            isset($row[$col["collected_date"]]) &&
                            isset($row[$col["collected_date"]]) 
                        ) {                            
                            $sample_number      = $row[$col["sample_number"]];
                            $sample_source_id   = ($row[$col["sample_source_id"]] !== "") ? $row[$col["sample_source_id"]] : null ;
                            $requester_id       = ($row[$col["requester_id"]] !== "") ? $row[$col["requester_id"]] : null ;
                            $collected_date     = ($row[$col["collected_date"]] !== "") ? date('Y-m-d', strtotime($row[$col["collected_date"]])) : null ;
                            $collected_time     = ($row[$col["collected_date"]] !== "") ? date('H:i', strtotime($row[$col["collected_date"]])).":00" : null ;
                            $received_date      = ($row[$col["received_date"]] !== "") ? date('Y-m-d', strtotime($row[$col["received_date"]])) : null ;
                            $received_time      = ($row[$col["received_date"]] !== "") ? date('H:i', strtotime($row[$col["received_date"]])).":00" : null ;
                            //$payment_type_id    = ($row[19] !== "") ? $row[19] : null ;
                            $payment_type_id    = 1 ;
                            $admission_date     = ($row[$col["admission_date"]] !== "") ? date('Y-m-d H:i', strtotime($row[$col["admission_date"]])).":00" : null ;
                            $clinical_history   = ($row[$col["diagnosis"]] !== "") ? $row[$col["diagnosis"]] : null ;
                            $is_urgent          = ($row[$col["is_urgent"]] == "true") ? 1 : 0 ;
                            $for_research       = ($row[$col["reason_for_testing_id"]] !== "") ? $row[$col["reason_for_testing_id"]] : 0 ;
                            $completed_by       = ($row[$col["completed_by"]] !== "") ? $row[$col["completed_by"]] : null ;
                            $phone_number       = ($row[$col["phone_completed_by"]] !== "") ? $row[$col["phone_completed_by"]] : null ;
                            $sample_collector   = ($row[$col["sample_collector"]] !== "") ? $row[$col["sample_collector"]] : null ;
                            $phone_number_sample_collector  =   ($row[$col["phone_number_sample_collctor"]] !== "") ? $row[$col["phone_number_sample_collctor"]] : null ;
                            
                            $number_of_sample   = ($row[$col["number_of_sample"]] !== "" || (is_int($row[$col["number_of_sample"]]) == 1) ) ? $row[$col["number_of_sample"]] : null ;
                            $health_facility    = ($row[$col["health_facility"]] !== "" ) ? $row[$col["health_facility"]] : "" ;
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
                                'phone_number_sample_collector' => $phone_number_sample_collector,
                                'number_of_sample'      => $number_of_sample,
                                'health_facility'       => $health_facility
                            );
                            
                            $sample_number_type = CamlisSession::getLabSession('sample_number');
                            if ($sample_number_type == 2) {
                                $data['sample_number'] = $data["sample_number"].'-'.date('dmY');
                            } else {
                                $_r = $this->psample_model->get_psample_number();
                                $data['sample_number'] = $_r->sample_number;
                            }
                            $psample_id = 0;
                            if ($this->psample_model->is_unique_sample_number($data['sample_number'])) {
                                $psample_id = $this->psample_model->add_patient_sample($data);
                                $patient["sample_msg"]     = _t('global.msg.save_success');
                                $patient["sample_status"]  = true;
                                $patient["sample_number"]  = $data['sample_number'];
                                $patient["psample_id"]     = $psample_id;
                                // clinical_symptom
                                
                                if($row[$col["clinical_symptom"]] !== ""){
                                    $this->load->model('clinical_symptom_model');
                                    $clinical_symptom = explode(";" , $row[$col["clinical_symtop_id"]]);
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
                            
                            if($psample_id > 0 || $patient["sample_status"]){
                                // Add Test Here
                                if(isset($row[$col["test_name"]])){
                                    if($row[$col["test_id"]] !== ""){
                                        if($row[$col["test_id"]] == 479){
                                            $sample_tests = array(479);
                                            $sample_details = array(
                                                "department_sample_id"  => 26, 
                                                "sample_description"    => "-1", 
                                                "first_weight"          => null, 
                                                "second_weight"         => null
                                            );
                                        }else if($row[$col["test_id"]] == 497){
                                            $sample_tests = array(495,497);
                                            $sample_details = array(
                                                "department_sample_id"  => 29, 
                                                "sample_description"    => "-1", 
                                                "first_weight"          => null, 
                                                "second_weight"         => null
                                            );
                                        }else if($row[$col["test_id"]] == 505){
                                            $sample_tests = array(505); // id table patient_sample_tests
                                            $sample_details = array(
                                                "department_sample_id"  => 17, 
                                                "sample_description"    => "-1", 
                                                "first_weight"          => null, 
                                                "second_weight"         => null
                                            );
                                        }
                                        if($row[$col["test_id"]] == 497){
                                            $this->psample_model->assign_sample_test($psample_id, $sample_tests);
                                            // get patient_test_id
                                            $res = $this->psample_model->get_patient_sample_test($psample_id);
                                            $patient_test_ids = array_column($res, 'patient_test_id');
                                            $patient_test_id = $patient_test_ids[1];
                                        }else{
                                            $patient_test_id = $this->psample_model->assign_single_sample_test($psample_id, $sample_tests);
                                        }
                                       
                                        
                                        $this->psample_model->set_psample_detail($psample_id, $sample_details);
                                       
                                        $patient["test_msg"]     = _t('global.msg.save_success');
                                        $patient["test_status"]  = true;
                                        // 
                                        /**
                                         * added 01-05-2021
                                         * Add test result 
                                         */
                                        if(isset($row[$col["test_result"]])){
                                            $organism_antibiotic_result = [];
                                            $this->load->model(['result_model']);
                                            /*
                                            if($row[$col["test_id"]] == 479){
                                                $result = $row[$col["test_result"]] == "Negative" ? 4848 : 4849;
                                            }else if($row[$col["test_id"]] == 497){
                                                $result = $row[$col["test_result"]] == "Negative" ? 4858 : 4859;
                                            }else{
                                                $result = $row[$col["test_result"]] == "Negative" ? 4865 : 4864;
                                            }
                                            */

                                            //SARS-CoV-2 (Applied Biosystems 7500 Fast Real Time PCR)
                                            if($row[$col["test_result_date"]] !== "" && $row[$col["performer_by_id"]] !== "" && $row[$col["test_result_id"]] !== ""){
                                                $result_test_date = ($row[$col["test_result_date"]] !== "") ? date('Y-m-d', strtotime($row[$col["test_result_date"]])) : null ;

                                                $organism_antibiotic_result[] = [
                                                    'patient_sample_id' => $psample_id,
                                                    'patient_test_id'   => $patient_test_id,
                                                    'performer_id'      => $row[$col["performer_by_id"]],
                                                    'machine_name'      => $row[$col["machine_name"]],
                                                    'test_date'         => $result_test_date,
                                                    'result'            => $row[$col["test_result_id"]],
                                                    'type'              => 1,
                                                    'quantity_id'       => -1,
                                                    'contaminant'       => 0,
                                                    'antibiotic'        => array()
                                                ];
                                                if (count($organism_antibiotic_result) > 0) $this->result_model->set_ptest_organism_antibiotic_result($psample_id, $organism_antibiotic_result);
                                                $patient["test_result_msg"]     = _t('global.msg.save_success');
                                                $patient["test_result_status"]  = true;
                                                $this->psample_model->update_progress_status($psample_id);
                                            }else{
                                                $patient["test_result_msg"]     = _t('global.msg.result_not_selected');
                                                $patient["test_result_status"]  = false;
                                            }
                                            
                                        }
                                    }else{
                                        $patient["test_msg"]     = _t('global.msg.result_not_selected');
                                        $patient["test_status"]  = false;
                                    }
                                }else{
                                    $patient["test_msg"]     = _t('global.msg.result_not_selected');
                                    $patient["test_status"]  = false;
                                }
                                //End
                            }
                        }else{
                            $patient["sample_msg"]     = _t('global.msg.field_for_sample_required');
                            $patient["sample_status"]  = false;
                            $patient["sample_number"]  = null;
                        }  
                        $samples[] = $data;
                    }
                    $n++;
                }
                $patients[]  = $patient; 
            }
        }// End the clock time in seconds 
        $end_time = microtime(true); 
        // Calculate the script execution time 
        $execution_time = ($end_time - $start_time);

        echo json_encode(array('patients' => $patients , 'samples' => $samples , 'patient_status' => $result_status, 'sample_status' => $sample_status, 'execution_time' => $execution_time));
    }


    public function add_line_list_full_form_test(){
        $this->app_language->load(['pages/patient_sample_entry']);
        $this->load->model('patient_sample_model', 'psample_model');
        $start_time         = microtime(true); 
        $data               = $this->input->post('data');
        $msg                = "";
        $status             = false;
        $laboratory_code    = CamlisSession::getLabSession('lab_code');
        $patients           = array();
        $samples            = array();
        $result_status      = array();
        $sample_status      = array();
        $n                  = 1;
        $result_test        = array();
        
        $col = array(
            "patient_code"        => 0,
            "patient_name"        => 1,
            "age"                 => 2,
            "gender"              => 3,
            "phone"               => 4,
            "province"            => 5,
            "district"            => 6,
            "commune"             => 7,
            "village"             => 8,
            "residence"           => 9,
            "reason_for_testing"  => 10,
            "is_contacted"        => 11,
            "is_contacted_with"   => 12,
            "is_directed_contact" => 13,
            "sample_number"       => 14,
            "sample_source"       => 15,
            "requester"           => 16,
            "collected_date"      => 17,
            "received_date"       => 18,
            "payment_type"        => 19,
            "admission_date"      => 20,
            "diagnosis"           => 21,
            "is_urgent"           => 22,
            "completed_by"        => 23,
            "phone_completed_by"  => 24,
            "sample_collector"    => 25,
            "phone_number_sample_collctor" => 26,
            "clinical_symptom"    => 27,
            "health_facility"     => 28,
            "test_name"           => 29,
            "machine_name"        => 30,
            "test_result"         => 31,
            
            "test_result_date"    => 32,
            "perform_by"          => 33,
            "country"             => 34,
            "nationality"         => 35,
            "arrival_date"        => 36,
            "passport_number"     => 37,
            "flight_number"       => 38,
            "seat_number"         => 39,
            "is_positive_covid"   => 40,

            "test_date"           => 41,
            "sex_id"              => 42,
            "province_code"       => 43,
            "district_code"       => 44,
            "commune_code"        => 45,
            "village_code"        => 46,
            "reason_for_testing_id" => 47,
            "sample_source_id"    => 48,
            "requester_id"        => 49,
            "clinical_symtop_id"  => 50,
            "test_id"             => 51,
            "country_id"          => 52,
            "nationality_id"      => 53,
            "number_of_sample"    => 54,
            "performer_by_id"     => 55,
            "test_result_id"      => 56
        );
            
        
    
        ini_set('memory_limit', '512M');
        ini_set('max_execution_time', 1200); // Zero means the script can run forever
        
        foreach($data as $key => $row){
            // Check field required 
            // if patient_name is empty, just skip it 
            if(!empty($row[$col["patient_name"]])){
                if( isset($row[$col["patient_name"]]) && 
                    isset($row[$col['age']]) && 
                    isset($row[$col["gender"]]) && 
                    isset($row[$col["province"]]) && 
                    isset($row[$col["district"]]) &&
                    isset($row[$col["commune"]]) &&
                    isset($row[$col["village"]])
                ){
                    
                    $patient_code               = trim($row[$col["patient_code"]]);
                    $patient_name               = trim($row[$col["patient_name"]]);
                    $patient["patient_name"]    = $patient_name;
                    $isPMRSPatientID            = isPMRSPatientID($patient_code);
                    $isSavedPatient             = FALSE;
                
                    if ($isPMRSPatientID) {
                        $this->load->model('patient_model');                                            
                        
                        $province           = !empty($row[$col["province_code"]]) ? $row[$col["province_code"]] : -1;
                        $district           = !empty($row[$col["district_code"]]) ? $row[$col["district_code"]] : -1;
                        $commune            = !empty($row[$col["commune_code"]]) ? $row[$col["commune_code"]] : -1;
                        $village            = !empty($row[$col["village_code"]]) ? $row[$col["village_code"]] : -1;
                        $dob                = date('Y') - $row[$col["age"]];
                        $dob               .= '-'.date("m-d");
                        $patient_sex        = $row[$col["sex_id"]];
                        $patient_phone      = (trim($row[$col["phone"]]) !== "") ? trim($row[$col["phone"]]) : null ;

                        $phone = $patient_phone;

                        $isSavedPatient = $this->patient_model->save_pmrs_patient($patient_code, $patient_name, $patient_sex ,$dob, $phone, $province, $district, $commune, $village);
                        $patient["patient_code"]    = $patient_code;
                        $patient["msg"]             = _t('patient.msg.is_pmrs');  
                        $patient["pstatus"]         = false;                      
                        $pid                        = $patient_code;
                    } else {

                        // Check Length of each column

                        //$patient_code = empty($patient_code) ? (date('dmyHis') + $n) : strtoupper($patient_code);
                        $patient_code = empty($patient_code) ? (date('ymdHis') + $n) : strtoupper($patient_code);
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
                        
                        // if patient is exist just update
                        /**
                         * added 29-04-2021
                         * check last char if it is M or D
                         */
                        $char = substr($row[2],strlen($row[$col["age"]])-1 , strlen($row[$col["age"]]));
                        // if it is day
                        if(strtolower($char) == "m"){
                            $monthVal = substr($row[$col["age"]],0, strlen($row[$col["age"]])-1);
                            $cYear  = date('Y');
                            $cMonth = date('n'); // month without leading 0 
                            $cDay   = date('d');
                            $rMonth = ($cMonth + (12 - intval($monthVal)));
                            $rMonth = ($rMonth > 12 ) ? ($rMonth - 12) : $rMonth;    
                            if($monthVal > $cMonth){
                                $cYear--;
                            }
                            if(strlen($rMonth) == 1){
                                $rMonth = "0".$rMonth;
                            }
                            $dob    = $cYear."-".$rMonth."-".$cDay;
                        }else if(strtolower($char) == "d"){
                            $dayVal = substr($row[$col["age"]],0, strlen($row[$col["age"]])-1);
                            $cYear  = date('Y');
                            $cMonth = date('n'); // month without leading 0 
                            $cDay   = date('d');

                            $rDay = ($cDay + (31 - intval($dayVal)));
                            $rDay = ($rDay > 31 ) ? ($rDay - 31) : $rDay;  
                            if($rDay > $cDay){
                                $cMonth--;
                            }
                            if(strlen($cMonth) == 1){
                                $cMonth = "0".$cMonth;
                            }
                            if(strlen($rDay) == 1){
                                $rDay = "0".$rDay;
                            }
                            $dob    = $cYear."-".$cMonth."-".$rDay;
                        }else{
                            $dob                = date('Y') - $row[2];
                            $dob               .= '-'.date("m-d");
                        }
                        
                        
                        $patient_sex        = $row[$col["sex_id"]];
                        $patient_phone      = (trim($row[$col["phone"]]) !== "") ? trim($row[$col["phone"]]) : null ;

                        $phone              = $patient_phone;


                        $province           = $row[$col["province_code"]];
                        $district           = $row[$col["district_code"]];
                        $commune            = $row[$col["commune_code"]];
                        $village            = $row[$col["village_code"]];
                        $residence          = trim($row[$col["residence"]]);

                        $country            = ($row[$col["country_id"]] !== "") ? $row[$col["country_id"]] : null;
                        $country_name       = ($row[$col["country"]] !== "") ? $row[$col["country"]] : null;
                        $nationality        = ($row[$col["nationality_id"]] !== "") ? $row[$col["nationality_id"]] : null;
                        $date_arrival       = ($row[$col["arrival_date"]] !== "") ? date('Y-m-d', strtotime($row[$col["arrival_date"]])): null;
                        $passport_number    = ($row[$col["passport_number"]] !== "") ? $row[$col["passport_number"]] : null;
                        $flight_number      = ($row[$col["flight_number"]] !== "") ? $row[$col["flight_number"]] : null;
                        $seat_number        = ($row[$col["seat_number"]] !== "") ? $row[$col["seat_number"]] : null;
                        $is_positive_covid  = ($row[$col["is_positive_covid"]] == "true") ? $row[$col["is_positive_covid"]] : null;
                        // if positive covid
                        $test_date          = null;
                        if($is_positive_covid == true){
                            $test_date      = ($row[$col["test_date"]] !== "") ? date('Y-m-d', strtotime($row[$col["test_date"]])) : null;
                        }
                        $is_contacted       = ($row[$col["is_contacted"]] == "true") ? $row[$col["is_contacted"]] : null;
                        $contact_with       = null;
                        $is_direct_contact  = null;
                        if($is_contacted == true){
                            $contact_with   = ($row[$col["is_contacted_with"]] !== "") ? $row[$col["is_contacted_with"]] : null;
                            $is_direct_contact = ($row[$col["is_directed_contact"]] == "true") ? $row[$col["is_directed_contact"]] : null;
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
                            'passport_number'           => trim($passport_number),
                            'seat_number'               => trim($seat_number),
                            'is_positive_covid'         => $is_positive_covid,
                            'test_date'                 => $test_date,
                            'is_contacted'              => $is_contacted,
                            'contact_with'              => $contact_with,
                            'relationship_with_case'    => null,
                            'travel_in_past_30_days'    => null,
                            'flight_number'             => trim($flight_number),
                            'is_direct_contact'         => $is_direct_contact,
                            'country_name'              => $country_name
                        );

                        if($isPatientExist == true){
                            if ($this->patient_model->update_outside_patient($pid, $patient)) {
                                $patient["msg"]         = _t('global.msg.save_success');
                                $patient["pstatus"]     = true;
                            }else{
                                $patient["msg"]         =  _t('global.msg.save_fail');
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
                                $patient["msg"]         =  _t('global.msg.save_fail');
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
                            isset($row[$col["sample_source"]]) &&
                            isset($row[$col["requester"]]) &&
                            isset($row[$col["collected_date"]]) &&
                            isset($row[$col["collected_date"]]) 
                        ) {                            
                            $sample_number      = $row[$col["sample_number"]];
                            $sample_source_id   = ($row[$col["sample_source_id"]] !== "") ? $row[$col["sample_source_id"]] : null ;
                            $requester_id       = ($row[$col["requester_id"]] !== "") ? $row[$col["requester_id"]] : null ;
                            $collected_date     = ($row[$col["collected_date"]] !== "") ? date('Y-m-d', strtotime($row[$col["collected_date"]])) : null ;
                            $collected_time     = ($row[$col["collected_date"]] !== "") ? date('H:i', strtotime($row[$col["collected_date"]])).":00" : null ;
                            $received_date      = ($row[$col["received_date"]] !== "") ? date('Y-m-d', strtotime($row[$col["received_date"]])) : null ;
                            $received_time      = ($row[$col["received_date"]] !== "") ? date('H:i', strtotime($row[$col["received_date"]])).":00" : null ;
                            //$payment_type_id    = ($row[19] !== "") ? $row[19] : null ;
                            $payment_type_id    = 1 ;
                            $admission_date     = ($row[$col["admission_date"]] !== "") ? date('Y-m-d H:i', strtotime($row[$col["admission_date"]])).":00" : null ;
                            $clinical_history   = ($row[$col["diagnosis"]] !== "") ? $row[$col["diagnosis"]] : null ;
                            $is_urgent          = ($row[$col["is_urgent"]] == "true") ? 1 : 0 ;
                            $for_research       = ($row[$col["reason_for_testing_id"]] !== "") ? $row[$col["reason_for_testing_id"]] : 0 ;
                            $completed_by       = ($row[$col["completed_by"]] !== "") ? $row[$col["completed_by"]] : null ;
                            $phone_number       = ($row[$col["phone_completed_by"]] !== "") ? $row[$col["phone_completed_by"]] : null ;
                            $sample_collector   = ($row[$col["sample_collector"]] !== "") ? $row[$col["sample_collector"]] : null ;
                            $phone_number_sample_collector  =   ($row[$col["phone_number_sample_collctor"]] !== "") ? $row[$col["phone_number_sample_collctor"]] : null ;
                            
                            $number_of_sample   = ($row[$col["number_of_sample"]] !== "" || (is_int($row[$col["number_of_sample"]]) == 1) ) ? $row[$col["number_of_sample"]] : null ;
                            $health_facility    = ($row[$col["health_facility"]] !== "" ) ? $row[$col["health_facility"]] : "" ;
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
                                'phone_number_sample_collector' => $phone_number_sample_collector,
                                'number_of_sample'      => $number_of_sample,
                                'health_facility'       => $health_facility
                            );
                            
                            $sample_number_type = CamlisSession::getLabSession('sample_number');
                            if ($sample_number_type == 2) {
                                $data['sample_number'] = $data["sample_number"].'-'.date('dmY');
                            } else {
                                $_r = $this->psample_model->get_psample_number();
                                $data['sample_number'] = $_r->sample_number;
                            }
                            $psample_id = 0;
                            if ($this->psample_model->is_unique_sample_number($data['sample_number'])) {
                                $psample_id = $this->psample_model->add_patient_sample($data);
                                $patient["sample_msg"]     = _t('global.msg.save_success');
                                $patient["sample_status"]  = true;
                                $patient["sample_number"]  = $data['sample_number'];
                                $patient["psample_id"]     = $psample_id;
                                // clinical_symptom
                                
                                if($row[$col["clinical_symptom"]] !== ""){
                                    $this->load->model('clinical_symptom_model');
                                    $clinical_symptom = explode(";" , $row[$col["clinical_symtop_id"]]);
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
                            
                            if($psample_id > 0 || $patient["sample_status"]){
                                // Add Test Here
                                if(isset($row[$col["test_name"]])){
                                    if($row[$col["test_id"]] !== ""){
                                        if($row[$col["test_id"]] == 479){
                                            $sample_tests = array(479);
                                            $sample_details = array(
                                                "department_sample_id"  => 26, 
                                                "sample_description"    => "-1", 
                                                "first_weight"          => null, 
                                                "second_weight"         => null
                                            );
                                        }else if($row[$col["test_id"]] == 497){
                                            $sample_tests = array(495,497);
                                            $sample_details = array(
                                                "department_sample_id"  => 29, 
                                                "sample_description"    => "-1", 
                                                "first_weight"          => null, 
                                                "second_weight"         => null
                                            );
                                        }else if($row[$col["test_id"]] == 505){
                                            $sample_tests = array(505); // id table patient_sample_tests
                                            $sample_details = array(
                                                "department_sample_id"  => 17, 
                                                "sample_description"    => "-1", 
                                                "first_weight"          => null, 
                                                "second_weight"         => null
                                            );
                                        }
                                        if($row[$col["test_id"]] == 497){
                                            $this->psample_model->assign_sample_test($psample_id, $sample_tests);
                                            // get patient_test_id
                                            $res = $this->psample_model->get_patient_sample_test($psample_id);
                                            $patient_test_ids = array_column($res, 'patient_test_id');
                                            $patient_test_id = $patient_test_ids[1];
                                        }else{
                                            $patient_test_id = $this->psample_model->assign_single_sample_test($psample_id, $sample_tests);
                                        }
                                       
                                        
                                        $this->psample_model->set_psample_detail($psample_id, $sample_details);
                                       
                                        $patient["test_msg"]     = _t('global.msg.save_success');
                                        $patient["test_status"]  = true;
                                        // 
                                        /**
                                         * added 01-05-2021
                                         * Add test result 
                                         */
                                        if(isset($row[$col["test_result"]])){
                                            $organism_antibiotic_result = [];
                                            $this->load->model(['result_model']);
                                            /*
                                            if($row[$col["test_id"]] == 479){
                                                $result = $row[$col["test_result"]] == "Negative" ? 4848 : 4849;
                                            }else if($row[$col["test_id"]] == 497){
                                                $result = $row[$col["test_result"]] == "Negative" ? 4858 : 4859;
                                            }else{
                                                $result = $row[$col["test_result"]] == "Negative" ? 4865 : 4864;
                                            }
                                            */

                                            //SARS-CoV-2 (Applied Biosystems 7500 Fast Real Time PCR)
                                            if($row[$col["test_result_date"]] !== "" && $row[$col["performer_by_id"]] !== "" && $row[$col["test_result_id"]] !== ""){
                                                $result_test_date = ($row[$col["test_result_date"]] !== "") ? date('Y-m-d', strtotime($row[$col["test_result_date"]])) : null ;

                                                $organism_antibiotic_result[] = [
                                                    'patient_sample_id' => $psample_id,
                                                    'patient_test_id'   => $patient_test_id,
                                                    'performer_id'      => $row[$col["performer_by_id"]],
                                                    'machine_name'      => $row[$col["machine_name"]],
                                                    'test_date'         => $result_test_date,
                                                    'result'            => $row[$col["test_result_id"]],
                                                    'type'              => 1,
                                                    'quantity_id'       => -1,
                                                    'contaminant'       => 0,
                                                    'antibiotic'        => array()
                                                ];
                                                if (count($organism_antibiotic_result) > 0) $this->result_model->set_ptest_organism_antibiotic_result($psample_id, $organism_antibiotic_result);
                                                $patient["test_result_msg"]     = _t('global.msg.save_success');
                                                $patient["test_result_status"]  = true;
                                                $this->psample_model->update_progress_status($psample_id);
                                            }else{
                                                $patient["test_result_msg"]     = _t('global.msg.result_not_selected');
                                                $patient["test_result_status"]  = false;
                                            }
                                            
                                        }
                                    }else{
                                        $patient["test_msg"]     = _t('global.msg.result_not_selected');
                                        $patient["test_status"]  = false;
                                    }
                                }else{
                                    $patient["test_msg"]     = _t('global.msg.result_not_selected');
                                    $patient["test_status"]  = false;
                                }
                                //End
                            }
                        }else{
                            $patient["sample_msg"]     = _t('global.msg.field_for_sample_required');
                            $patient["sample_status"]  = false;
                            $patient["sample_number"]  = null;
                        }  
                        $samples[] = $data;
                    }
                    $n++;
                }
                $patients[]  = $patient; 
            }
        }// End the clock time in seconds 
        $end_time = microtime(true); 
        // Calculate the script execution time 
        $execution_time = ($end_time - $start_time);

        echo json_encode(array('patients' => $patients , 'samples' => $samples , 'patient_status' => $result_status, 'sample_status' => $sample_status, 'execution_time' => $execution_time));
    }
}