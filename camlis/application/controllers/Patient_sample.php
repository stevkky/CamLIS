<?php
defined('BASEPATH') OR die("Access denied!");
class Patient_sample extends MY_Controller {
    public function __construct() {
        parent::__construct();
        $this->load->model('patient_sample_model', 'psample_model');
    }

    /**
     * Save new patient's sample
     */
    public function save() {
        $this->aauth->control('add_psample');
        $this->app_language->load('sample');
        $this->form_validation->set_rules('patient_sample_id', 'Patient Sample ID', 'trim|less_than_equal_to[0]');
        $this->form_validation->set_rules('patient_id', 'Patient\'s ID', 'required|trim');
        $this->form_validation->set_rules('sample_source_id', 'Sample Source', 'required|integer|greater_than[0]');
        $this->form_validation->set_rules('requester_id', 'Requester', 'required|integer|greater_than[0]');
        $this->form_validation->set_rules('collected_date', 'collected_date', 'required|trim');
        $this->form_validation->set_rules('collected_time', 'collected_time', 'required|trim');
        $this->form_validation->set_rules('received_date', 'received_date', 'required|trim');
        $this->form_validation->set_rules('received_time', 'received_time', 'required|trim');
        $this->form_validation->set_rules('admission_date', 'admission_date', 'trim');
        $this->form_validation->set_rules('is_urgent', 'Urgent', 'required|integer|in_list[0,1]');
        $this->form_validation->set_rules('payment_type_id', 'Payment type', 'required|integer|greater_than[0]');        
        $this->form_validation->set_rules('for_research', 'For Research', 'integer|in_list[0,1,2,3,4,5,6,7,8,9,10,11,12]');
        $this->form_validation->set_rules('clinical_history', 'Clinical History', 'trim');
        $this->form_validation->set_rules('collected_date_time', 'collected_date_time', 'required|differs[received_date_time]');
        $this->form_validation->set_rules('number_of_sample', 'Number of sample');// 20 April 2021

        $status         = FALSE;
        $msg            = _t("global.msg.fill_required_data");
        $psample_info   = array();
        $patient        = elements(['pid', 'name', 'sex', 'dob', 'phone', 'province', 'district', 'commune', 'village'], (array)$this->input->post('patient'));
        $psample_id     = 0;
        $clinical_symptom	= $this->input->post("clinical_symptom");// ADDED 04 DEC 2020
       
        if ($this->form_validation->run() === TRUE && !empty($patient['pid'])) {
            $msg             = _t("global.msg.save_fail");
            $data            = elements(['patient_id', 'sample_source_id', 'requester_id', 'collected_date', 'collected_time', 'received_date', 'received_time', 'is_urgent', 'payment_type_id', 'for_research', 'clinical_history', 'sample_number', 'admission_date',
                                'phone_number','sample_collector','completed_by','phone_number_sample_collector','number_of_sample','health_facility'], $this->input->post());
            $test_payments   = $this->input->post('test_payments');
            $isPMRSPatientID = isPMRSPatientID($patient['pid']);
            $isSavedPatient  = FALSE;
    
            /**
             * Check if patient from camlis
             * 09-06-2021
             */            
            $data_            = elements(['is_camlis_patient'], $this->input->post()); // for Camlis_patient
            $is_camlis_patient = false;

            if ($isPMRSPatientID) {
                $this->load->model('patient_model');
                $province = !empty($patient['province']) ? $patient['province'] : -1;
                $district = !empty($patient['district']) ? $patient['district'] : -1;
                $commune  = !empty($patient['commune']) ? $patient['commune'] : -1;
                $village  = !empty($patient['village']) ? $patient['village'] : -1;
                // Check if phone number from PMRS is Kh unicode number or not
                // added: 26 Feb 2021
                // if return null mean that it is not khmer number
                $phone = khNumberToLatinNumber($patient['phone']);
                if(strlen($phone) > 0){
                    $phone = khNumberToLatinNumber($patient['phone']);
                    if(strlen($phone) > 10) $phone = substr($phone,0,10);
                }else{
                    // sometime phone number here is khmer unicode word: គ្មាន
                    if(is_numeric($patient['phone'])){
                        $phone = str_replace(' ', '', $patient['phone']); // added 09 Feb 2021, phone column is limited 10 Chars we need to take out space 
                        $phone = preg_replace("/[^0-9]/", "",$phone);
                        //$phone = preg_replace('/\D/', '', $phone);
                        //$phone = str_replace("&#8203;", "",  $phone); //Remove Unicode Zero Width Space PHP
                        //$phone = str_replace("\xE2\x80\x8C", "",  $phone);
                        if(strlen($phone) > 10) $phone = substr($phone,0,10);
                    }else{
                        $phone = str_replace(' ', '', $patient['phone']); // added 09 Feb 2021, phone column is limited 10 Chars we need to take out space 
                        $phone = preg_replace("/[^0-9]/", "",$phone); 
                    }
                }
                $isSavedPatient = $this->patient_model->save_pmrs_patient($patient['pid'], $patient['name'], $patient['sex'], $patient['dob'], $phone, $province, $district, $commune, $village);
                // Generate QR-CODE
                //16-06-2021
                if($isSavedPatient){
                    $this->load->library('phpqrcode/Qrlib');
                    $SERVERFILEPATH = $_SERVER['DOCUMENT_ROOT'].'/assets/camlis/images/patient_qr_code/';                    
                    $text           = 'name='.$patient['name'].',phone='.$phone.',location='.$province.',pid='.$patient['pid'];
                    //$text 			= $pid;
                    //$folder 		= $SERVERFILEPATH;
                    $file_name      = $patient['pid'].".png";
                    $file_path 		= $SERVERFILEPATH.$patient['pid'].".png";
                    if(!file_exists($file_path)){
                        QRcode::png($text,$file_path);
                    }
                }
                // End
            }else if($data_["is_camlis_patient"] == "true"){
                $this->load->model('patient_model');
                //$camlis_patient        = elements(['patient_code'], (array)$this->input->post('patient'));

                // add new patient here
                $camlis_patient = elements(['patient_code', 'patient_name', 'sex', 'dob', 'phone', 'province', 'district', 'commune', 'village',
                'residence','nationality','country','date_arrival','passport_number','seat_number','is_positive_covid','test_date','is_contacted',
                'contact_with','relationship_with_case','travel_in_past_30_days','flight_number','is_direct_contact','country_name','vaccination_status',
                'vaccine_id','first_vaccinated_date','occupation','second_vaccinated_date'], (array)$this->input->post('patient'));
                $camlis_patient["sex"]                  = ($camlis_patient["sex"] == 'M') ? 1 : 2;
                $camlis_patient["patient_name"]         = $patient['name'];
                $camlis_patient["country"]              = ($camlis_patient["country"] == "") ? null : $camlis_patient["patient_name"];
                $camlis_patient["nationality"]          = ($camlis_patient["nationality"] == "") ? null : $camlis_patient["nationality"];
                $camlis_patient["is_positive_covid"]    = ($camlis_patient["is_positive_covid"] == "") ? null : $camlis_patient["is_positive_covid"];
                $camlis_patient["date_arrival"]         = ($camlis_patient["date_arrival"] == "") ? null : $camlis_patient["date_arrival"];
                $camlis_patient["passport_number"]      = ($camlis_patient["passport_number"] == "") ? null : $camlis_patient["passport_number"];
                $camlis_patient["seat_number"]          = ($camlis_patient["seat_number"] == "") ? null : $camlis_patient["seat_number"];
                $camlis_patient["test_date"]            = ($camlis_patient["test_date"] == "") ? null : $camlis_patient["test_date"];
                $camlis_patient["is_contacted"]         = ($camlis_patient["is_contacted"] == "") ? null : $camlis_patient["is_contacted"];
                $camlis_patient["is_direct_contact"]    = ($camlis_patient["is_direct_contact"] == "") ? null : $camlis_patient["is_direct_contact"];
                $camlis_patient["parent_code"]          = $camlis_patient['patient_code'];
                
                $camlis_patient["vaccination_status"]   = $camlis_patient['vaccination_status'] == "-1"? null : $camlis_patient['vaccination_status'];
                $camlis_patient["first_vaccinated_date"]   = $camlis_patient['first_vaccinated_date'] == "" ? null : $camlis_patient['first_vaccinated_date'];
                $camlis_patient["second_vaccinated_date"]   = $camlis_patient['second_vaccinated_date'] == "" ? null : $camlis_patient['second_vaccinated_date'];
                $camlis_patient["occupation"]   = $camlis_patient['occupation'] == "" ? null : $camlis_patient['occupation'];

                $pid = $this->patient_model->save_outside_patient($camlis_patient);
                if($pid > 0) {
                    $isSavedPatient = true;
                    $data['patient_id'] = $pid;
                    $is_camlis_patient = true;
                }
            }
            /** End */
           
            if (!$isPMRSPatientID || ($isPMRSPatientID && $isSavedPatient)) {

                $sample_number_type = CamlisSession::getLabSession('sample_number');
                if ($sample_number_type == 2) {
                    $data['sample_number'] = $data["sample_number"].'-'.date('dmY');
                } else {
                   $_r = $this->psample_model->get_psample_number();
                   $data['sample_number'] = $_r->sample_number;
                }
               
                if ($this->psample_model->is_unique_sample_number($data['sample_number'])) {
                    /*
                    * counting micro
                    * 30-11-2018
                    */
                    $count_micro = array();
                    if (is_array($this->input->post('sample_tests')) && count($this->input->post('sample_tests'))) {
                        $count_micro = array_filter($this->input->post('sample_tests'), function($value){
                            $micros = array('170', '187', '203', '207', '212', '224', '226', '230', '231', '235', '264', '280');
                            return $value == in_array($value, $micros);
                        });
                    }
                    $data['micro'] = count($count_micro); // add number of micro to $data = patient sample
                    /*
                    * end counting micro
                    * 30-11-2018
                    */
                    $psample_id = $this->psample_model->add_patient_sample($data);
                    // add clinical symptom
                    //ADDED: 04 DEC 2020
                    if (is_array($clinical_symptom) && count($clinical_symptom) > 0) {

                        $this->load->model('clinical_symptom_model');
                        $clinical_symptom_data = array();
                        foreach ($clinical_symptom as $cs) {
                            $clinical_symptom_data[] = array(
                                'clinical_symptom_id'   => $cs,
                                'patient_sample_id' 	=> $psample_id
                            );
                        }
                        $this->clinical_symptom_model->add_clinical_symptom($clinical_symptom_data);
                    }
                } else {
                    $msg = _t('sample.msg.sample_number_exist');
                }

                if ($psample_id > 0) {
                    //$status = FALSE;
                    $status = TRUE; // Test
                    $msg    = _t("global.msg.save_success");
                    $info   = $this->psample_model->get_patient_sample($psample_id);
                    if (count($info) > 0) {
                        $psample_info = $info[0];
                        $psample_info['users'] = $this->psample_model->get_patient_sample_user($psample_id);
                    }
                }

                $is_assign_test = $this->input->post('is_assign_test');
                $sample_tests   = $this->input->post('sample_tests');
                $sample_details = $this->input->post('sample_details');

                if ($psample_id > 0 && (int)$is_assign_test == 200 && is_array($sample_tests) && count($sample_tests) > 0) {

                    $this->psample_model->assign_sample_test($psample_id, $sample_tests);
                    $this->psample_model->set_psample_detail($psample_id, $sample_details);
                    $this->psample_model->update_rejection_status($psample_id); 
                    $this->psample_model->update_progress_status($psample_id);
                } 
                
                $test_payments = collect($test_payments)->map(function($d) use ($psample_id) { $d['patient_sample_id'] = $psample_id; return $d; })->toArray();
                if ($psample_id > 0 && count($test_payments) > 0) {
                    $this->psample_model->add_patient_sample_test_payment($test_payments);
                }
            }
        }

        $data['result'] = json_encode(array('status' => $status, 'msg' => $msg, 'data' => $psample_info, 'sample_tesets'=> $sample_tests , 'sample_detail' => $sample_details , 'is_camlis_patient'=>$is_camlis_patient));
        $this->load->view('ajax_view/view_result', $data);
    }

    /**
     * Update patient's sample
     */
    public function update() {
        $this->aauth->control('edit_psample');

        $this->form_validation->set_rules('patient_sample_id', 'Patient Sample ID', 'required|trim|greater_than[0]|integer');
        $this->form_validation->set_rules('patient_id', 'Patient\'s ID', 'required|trim');
        $this->form_validation->set_rules('sample_source_id', 'Sample Source', 'required|integer|greater_than[0]');
        $this->form_validation->set_rules('requester_id', 'Requester', 'required|integer|greater_than[0]');
        $this->form_validation->set_rules('collected_date', 'collected_date', 'required|trim');
        $this->form_validation->set_rules('collected_time', 'collected_time', 'required|trim');
        $this->form_validation->set_rules('received_date', 'received_date', 'required|trim');
        $this->form_validation->set_rules('received_time', 'received_time', 'required|trim');
        $this->form_validation->set_rules('admission_date', 'admission_date', 'trim');
        $this->form_validation->set_rules('is_urgent', 'Urgent', 'required|integer|in_list[0,1]');
        //$this->form_validation->set_rules('for_research', 'For Research', 'required|integer|in_list[0,1]');        
        $this->form_validation->set_rules('for_research', 'For Research', 'integer|in_list[0,1,2,3,4,5,6,7,8,9,10,11,12]');
        $this->form_validation->set_rules('payment_type_id', 'Payment type', 'required|integer|greater_than[0]');
        $this->form_validation->set_rules('clinical_history', 'Clinical History', 'trim');
        $this->form_validation->set_rules('collected_date_time', 'collected_date_time', 'required|differs[received_date_time]');
        $this->form_validation->set_rules('number_of_sample', 'Number of sample');// 20 April 2021
        $status         = FALSE;
        $msg            = _t("global.msg.fill_required_data");
        $psample_info   = array();
        $patient        = elements(['pid', 'name', 'sex', 'dob', 'phone', 'province', 'district', 'commune', 'village'], (array)$this->input->post('patient'));

        $clinical_symptom = $this->input->post("clinical_symptom");
        
        if ($this->form_validation->run() === TRUE && !empty($patient['pid'])) {
            $msg             = _t("global.msg.save_fail");
            $data            = elements(array('patient_id', 'sample_source_id', 'requester_id', 'collected_date', 'collected_time', 'received_date', 'received_time', 'is_urgent', 'payment_type_id', 'for_research', 'clinical_history', 'admission_date',
                                'phone_number','sample_collector','completed_by','phone_number_sample_collector','number_of_sample','health_facility'), $this->input->post());            

            $psample_id      = $this->input->post('patient_sample_id');
            $test_payments   = $this->input->post('test_payments');
            $isPMRSPatientID = isPMRSPatientID($patient['pid']);
            $isSavedPatient  = FALSE;

            if ($isPMRSPatientID) {
                $this->load->model('patient_model');
                $province = !empty($patient['province']) ? $patient['province'] : -1;
                $district = !empty($patient['district']) ? $patient['district'] : -1;
                $commune  = !empty($patient['commune']) ? $patient['commune'] : -1;
                $village  = !empty($patient['village']) ? $patient['village'] : -1;
                                
                // Check if phone number from PMRS is Kh unicode number or not
                // added: 26 Feb 2021
                // if return null mean that it is not khmer number
                $phone = khNumberToLatinNumber($patient['phone']);
                if(strlen($phone) > 0){
                    $phone = khNumberToLatinNumber($patient['phone']);
                    if(strlen($phone) > 10) $phone = substr($phone,0,10);
                }else{
                    // sometime phone number here is khmer unicode word: គ្មាន
                    if(is_numeric($patient['phone'])){
                        $phone = str_replace(' ', '', $patient['phone']); // added 09 Feb 2021, phone column is limited 10 Chars we need to take out space 
                        
                        $phone = preg_replace("/[^0-9]/", "",$patient['phone']);
                        //$phone = str_replace("&#8203;", "",  $phone); //Remove Unicode Zero Width Space PHP
                        //$phone = str_replace("\xE2\x80\x8C", "",  $phone);
                        if(strlen($phone) > 10) $phone = substr($phone,0,10);
                    }else{
                        $phone = str_replace(' ', '', $patient['phone']); // added 09 Feb 2021, phone column is limited 10 Chars we need to take out space 
                        $phone = preg_replace("/[^0-9]/", "",$phone); 
                    }
                }

                $isSavedPatient = $this->patient_model->save_pmrs_patient($patient['pid'], $patient['name'], $patient['sex'], $patient['dob'], $phone, $province, $district, $commune, $village);
            }

            if (!$isPMRSPatientID || ($isPMRSPatientID && $isSavedPatient)) {
                /*
                * counting micro
                * 30-11-2018
                */
                $count_micro = array();
                if (is_array($this->input->post('sample_tests')) && count($this->input->post('sample_tests'))) {
                    $count_micro = array_filter($this->input->post('sample_tests'), function($value){
                        $micros = array('170', '187', '203', '207', '212', '224', '226', '230', '231', '235', '264', '280');
                        return $value == in_array($value, $micros);
                    });
                }
                $data['micro'] = count($count_micro); // add number of micro to $data = patient sample
                /*
                * end counting micro
                * 30-11-2018
                */
                if ($this->psample_model->update_patient_sample($psample_id, $data) > 0) {

                    // add clinical symptom
                    //ADDED: 04 DEC 2020
                    if (is_array($clinical_symptom) && count($clinical_symptom) > 0) {

                        $this->load->model('clinical_symptom_model');
                        $clinical_symptom_data = array();
                        foreach ($clinical_symptom as $cs) {
                            $clinical_symptom_data[] = array(
                                'clinical_symptom_id'   => $cs,
                                'patient_sample_id' 	=> $psample_id
                            );
                        }
                        $this->clinical_symptom_model->update_clinical_symptom($psample_id,$clinical_symptom_data);
                    }

                    $status = TRUE;
                    $msg    = _t("global.msg.update_success");
                    $info   = $this->psample_model->get_patient_sample($psample_id);
                    if (count($info) > 0) {
                        $psample_info = $info[0];
                        $psample_info['users'] = $this->psample_model->get_patient_sample_user($psample_id);
                    }
                }

                $is_assign_test = $this->input->post('is_assign_test');
                $sample_tests   = $this->input->post('sample_tests');
                $sample_details = $this->input->post('sample_details');
                if ($psample_id > 0 && (int)$is_assign_test == 200 && is_array($sample_tests) && count($sample_tests) > 0) {
                    $this->psample_model->assign_sample_test($psample_id, $sample_tests);
                    $this->psample_model->set_psample_detail($psample_id, $sample_details);
                    $this->psample_model->update_rejection_status($psample_id);
                    $this->psample_model->update_progress_status($psample_id);
                }

                $test_payments = collect($test_payments)->map(function($d) use ($psample_id) { $d['patient_sample_id'] = $psample_id; return $d; })->toArray();
                if ($psample_id > 0) $this->psample_model->delete_patient_sample_test_payment($psample_id);
                if ($psample_id > 0 && count($test_payments) > 0) {
                    $this->psample_model->add_patient_sample_test_payment($test_payments);
                }
            }
        }

        $data['result'] = json_encode(array('status' => $status, 'msg' => $msg, 'data' => $psample_info, 'sample_tests'=> $sample_tests , 'sample_detail' => $sample_details));
        $this->load->view('ajax_view/view_result', $data);
    }

    /**
     * Delete patient's sample
     */
    public function delete() {
        $this->aauth->control('delete_psample');

        $this->load->model('result_model');
        $this->load->model('clinical_symptom_model');
        $patient_sample_id  = $this->input->post('patient_sample_id');
        $status             = FALSE;
        $msg	            = _t('global.msg.delete_fail');

        if ($patient_sample_id > 0) {
            $this->db->trans_start();
            $this->psample_model->delete_patient_sample($patient_sample_id);
            $this->psample_model->delete_patient_sample_detail($patient_sample_id);
            $this->psample_model->delete_patient_sample_test($patient_sample_id);
            $this->result_model->delete_patient_sample_result($patient_sample_id);
            $this->result_model->delete_patient_sample_result_antibiotic($patient_sample_id);
            $this->clinical_symptom_model->delete($patient_sample_id); // added 06 DEC 2020
            $this->db->trans_complete();

            if ($this->db->trans_status() === TRUE) {
                //$status = FALSE;
                $status = TRUE;
                $msg	= _t('global.msg.delete_success');
            }
        }

        $data['result'] = json_encode(array('status' => $status, 'msg' => $msg));
        $this->load->view('ajax_view/view_result', $data);
    }

    /**
     * Get Test of Patient's Sample
     */
    public function get_patient_sample_test() {
        $this->load->model('patient_model');

        $patient_sample_id = $this->input->post('patient_sample_id');
        $rejection  = $this->input->post('rejection');
        $patient_id  = $this->input->post('patient_id');
        $result = array();

        if ($patient_sample_id > 0) {
            $result['sample_tests'] = $this->psample_model->get_patient_sample_test($patient_sample_id);
            $result['sample_details'] = $this->psample_model->get_patient_sample_detail($patient_sample_id);
            $result['patient_sample']	= $this->psample_model->get_patient_sample($patient_sample_id);
            $result['patient'] = isPMRSPatientID($patient_id) ? $this->patient_model->get_pmrs_patient($patient_id) : $this->patient_model->get_outside_patient($patient_id);

            if ((int)$rejection == 200) {
                $this->load->helper('sample_test');
                $result['patient_sample'] = collect($this->psample_model->get_patient_sample($patient_sample_id))->first();
                $result['sample_tests'] = sample_test_hierarchy($result['sample_tests'], TRUE);
            }
        }

        $data['result'] = json_encode($result);
        $this->load->view('ajax_view/view_result', $data);
    }

    /**
     * Set Reject Sample/Test
     */
    public function set_rejection() {
        $this->form_validation->set_rules('patient_sample_id', 'patient_sample_id', 'required|integer|greater_than[0]');
        $this->form_validation->set_rules('reject_comment', 'reject_comment', 'trim');
        $this->form_validation->set_rules('reject_sample', 'reject_sample', 'in_list[0,1]');
        $psample_id		= $this->input->post('patient_sample_id');
        $reject_sample	= $this->input->post('reject_sample');
        $reject_comment	= $this->input->post('reject_comment');
        $reject_tests	= $this->input->post('reject_tests');
        $reject_tests   = empty($reject_tests) ? [] : $reject_tests;
        $status         = FALSE;
        $msg            = _t("global.msg.fill_required_data");

        if ($this->form_validation->run() === TRUE) {
            $msg	= _t("global.msg.save_fail");
            $this->db->trans_start();
            /*
            * 29/08/2018
            * save and edit reject coment
            */
            $this->psample_model->save_and_edit_reject_comment($psample_id, $reject_comment);
            /*
            * Old code
            * $this->psample_model->update_patient_sample($psample_id, array('reject_comment' => $reject_comment));
            */           
            $this->psample_model->set_rejected_test($psample_id, $reject_tests);
            $this->psample_model->update_rejection_status($psample_id);
            $this->psample_model->update_progress_status($psample_id);
            $this->db->trans_complete();

            if ($this->db->trans_status() == TRUE) {                
                $status = TRUE;
                $msg  = _t("global.msg.update_success");
            }
        }

        $data['result'] = json_encode(array('status' => $status, 'msg' => $msg));
        $this->load->view('ajax_view/view_result', $data);
    }

    /**
     * Get Patient's Sample Info/Assigned Test/Result
     */
    public function get_patient_sample_details() {
        $this->load->model(['result_model', 'patient_model', 'test_model', 'reference_range_model']);
        $this->load->helper(['sample_test', 'util']);
        $patient_id  = $this->input->post('patient_id');
        $psample_id  = $this->input->post('patient_sample_id');
        $patient_age = $this->input->post('patient_age');
        $patient_sex = trim($this->input->post('patient_sex'));
        $result      = array();

        if ((int)$psample_id > 0) {
            $patient_sample  = collect($this->psample_model->get_patient_sample($psample_id))->first();
            $patient_info	 = isPMRSPatientID($patient_id) ? $this->patient_model->get_pmrs_patient($patient_id) : $this->patient_model->get_outside_patient($patient_id);
            if (!$patient_info) exit(400);

            //Sample Results
            $_tmp_result	= $this->result_model->get_patient_sample_result($psample_id);
            $ptest_result	= array();
            if (count($_tmp_result) > 0) {
                foreach ($_tmp_result as $row) {
                    $patient_test_id = $row['patient_test_id'];
                    if (in_array($row['field_type'], array(1, 2)) && $row['type'] == 1) {
                        if (!isset($ptest_result[$patient_test_id])) {
                            $ptest_result[$patient_test_id] = elements(array('patient_test_id', 'sample_test_id', 'field_type', 'is_rejected', 'test_date','number_update', 'performer_id','machine_name', ),$row);
                            
                            $ptest_result[$patient_test_id]['result'] = array();
                        }
                        $test_organism_id = $row['result'];
                        if (!isset($ptest_result[$patient_test_id]['result'][$test_organism_id])) {
                            $ptest_result[$patient_test_id]['result'][$test_organism_id] = array(
                                'test_organism_id'  => $test_organism_id,
                                'organism_name'     => $row['organism_name'],
                                'quantity_id'       => $row['quantity_id'],
                                'contaminant' => $row['contaminant'],
                                'antibiotic'        => array()
                            );
                        }
                        if ($row['antibiotic_id'] > 0 && isset($ptest_result[$patient_test_id]['result'][$test_organism_id])) {
                            $ptest_result[$patient_test_id]['result'][$test_organism_id]['antibiotic'][] = elements(array('antibiotic_id', 'sensitivity', 'disc_diffusion', 'test_zone', 'invisible'), $row);
                        }
                    } else {
                        $ptest_result[$patient_test_id] = elements(array('patient_test_id', 'sample_test_id', 'field_type', 'is_rejected', 'result', 'test_date', 'number_update', 'performer_id','machine_name'),$row);
                    }
                }
                //Reindex result
                foreach ($ptest_result as $key => $item) {
                    if (is_array($item['result'])) {
                        $ptest_result[$key]['result'] = array_values($item['result']);
                    }
                }
            }

            //Test of Patient's sample
            $psample_tests        = $this->psample_model->get_patient_sample_test($psample_id);
            $psample_tests        = collect($psample_tests)->keyBy('sample_test_id')->toArray();

            //Get Ref. Ranges
            $sample_test_id       = array_column($psample_tests, 'sample_test_id');
            $age                  = calculateAge($patient_info['dob'], $patient_sample['collected_date'], 'days');
            $age                  = $age > 0 ? $age : 1;
            $gender               = !is_numeric($patient_sex) && $patient_sex == 'M' ? MALE : FEMALE;
            $std_reference_ranges = collect($this->reference_range_model->get_std_reference_range($sample_test_id))->groupBy(function($item, $key) { return 'sample-test-'.$item['sample_test_id']; });
            $lab_reference_ranges = collect($this->reference_range_model->get_lab_reference_range($sample_test_id))->groupBy(function($item, $key) { return 'sample-test-'.$item['sample_test_id']; });
            $_reference_ranges    = $std_reference_ranges->merge($lab_reference_ranges)->flatten(1)->toArray();
            $reference_ranges     = [];

            if ($_reference_ranges) {
                foreach ($_reference_ranges as $ref_range) {
                    $sample_test_id = $ref_range['sample_test_id'];
                    if (isset($reference_ranges[$ref_range['sample_test_id']])) continue;
                    $min_age = (int)($ref_range['min_age'] * $ref_range['min_age_unit']);
                    $max_age = (int)($ref_range['max_age'] * $ref_range['max_age_unit']);

                    if (!empty($psample_tests[$sample_test_id]) && (!is_null($psample_tests[$sample_test_id]['ref_range_min_value']) || !is_null($psample_tests[$sample_test_id]['ref_range_max_value']) || !is_null($psample_tests[$sample_test_id]['ref_range_sign'])) ) {
                        $reference_ranges[$sample_test_id] = ['min_value' => $psample_tests[$sample_test_id]['ref_range_min_value'], 'max_value' => $psample_tests[$sample_test_id]['ref_range_max_value'], 'range_sign' => $psample_tests[$sample_test_id]['ref_range_sign']];
                    }
                    else if ($ref_range['is_equal'] == 1 && ($age >= $min_age && $age <= $max_age) && ($ref_range['gender'] == 3 || $ref_range['gender'] == $gender))
                    {
                        $reference_ranges[$ref_range['sample_test_id']] = array('min_value' => $ref_range['min_value'], 'range_sign' => $ref_range['range_sign'], 'max_value' => $ref_range['max_value']);
                    } else if ($age >= $min_age && $age < $max_age && ($ref_range['gender'] == 3 || $ref_range['gender'] == $gender))
                    {
                        $reference_ranges[$ref_range['sample_test_id']] = array('min_value' => $ref_range['min_value'], 'range_sign' => $ref_range['range_sign'], 'max_value' => $ref_range['max_value']);
                    }
                }
            }

            $result['patient']			= $patient_info;
            $result['patient_sample']	= $patient_sample;
            $result['sample_tests']		= sample_test_hierarchy(reindexArray(convertToHierarchy($psample_tests, 'sample_test_id', 'testPID', 'childs'), 'childs'), TRUE);
            $result['sample_tests']     = collect($result['sample_tests'])->map(function($item) { $item->samples = array_values($item->samples); return $item; });
            $result['results']			= $ptest_result;
            $result['ref_ranges']		= $reference_ranges;
            $result['result_comment']   = $this->psample_model->get_result_comment($psample_id);
            $result['result_users']     = $this->result_model->get_patient_sample_result_user($psample_id);
        }

        $data['result'] = json_encode($result);
        $this->load->view('ajax_view/view_result', $data);
    }

    /**
     * Update patient's sample printed info
     */
    public function update_printed_info() {
        $patient_sample_id  = $this->input->post('patient_sample_id');
        $status             = FALSE;

        if ($patient_sample_id > 0) {
            $this->psample_model->update_printed_info($patient_sample_id);
            $this->psample_model->update_progress_status($patient_sample_id);
            $status = FALSE;
        }
        $data['result'] = json_encode(array('status' => $status));
        $this->load->view('ajax_view/view_result', $data);
    }

    /**
     * Get Patient Sample
     */
    public function get_patient_sample() {
        $patient_id          = $this->input->post('patient_id');
        $patient_sample_id   = $this->input->post('patient_sample_id');
        $patient_code        = $this->input->post('patient_code');
        $sample_number       = $this->input->post('sample_number');
        $laboratory_id       = $this->input->post('laboratory_id');
        $patient_samples     = $this->psample_model->get_patient_sample($patient_sample_id, $sample_number, $patient_id, $patient_code, $laboratory_id);

        $id_list             = collect($patient_samples)->pluck('patient_sample_id')->toArray();
        $patient_sample_test = collect($this->psample_model->get_patient_sample_test($id_list));
        $payments            = collect($this->psample_model->get_patient_sample_test_payment($id_list));
        $patient_samples     = collect($patient_samples)->map(function ($item) use($patient_sample_test, $payments) {
            $item['is_assigned_test'] = $patient_sample_test->where('patient_sample_id', $item['patient_sample_id'])->count() > 0 ? TRUE : FALSE;
            $item['test_payments']    = $payments->where('patient_sample_id', $item['patient_sample_id'])->toArray();
            return $item;
        });

        $data['result']     = json_encode(['patient_samples' => $patient_samples]);
        $this->load->view('ajax_view/view_result', $data);
    }

    /**
     * Lookup patient sample
     */
    public function lookup_patient_sample() {
        $patient_code   = $this->input->post('patient_code');
        $sample_number  = $this->input->post('sample_number');
        $limit          = $this->input->post('limit');
        $patient_samples     = $this->psample_model->lookup_patient_sample($sample_number, $patient_code, $limit);
        echo json_encode(['patient_samples' => $patient_samples]);
    }

    /**
     * Check Sample number for uniqueness
     */
    public function is_unique_sample_number() {
        $this->app_language->load('sample');
        $sample_number = $this->input->post('sample_number');
        $is_unique     = FALSE;
        $msg           = NULL;
        if (!empty($sample_number)) {
            $sample_number .= "-".date('dmY');
            $is_unique = $this->psample_model->is_unique_sample_number($sample_number);
            $msg = _t('sample.msg.sample_number_exist');
        }
        echo json_encode(['is_unique' => $is_unique, 'msg' => $msg]);
    }

    /**
     * View patient's sample using DataTable
     */
    public function view_all_patient_sample() {
        $this->app_language->load(['pages/view_sample']);
        $reqData			= $this->input->post();
        $_data['reqData']	= $reqData;
        $result				= $this->psample_model->view_all_patient_sample($_data);
        $data['result']		= json_encode($result);
        $this->load->view('ajax_view/view_result', $data);
    }
    /**
     * View patient's sample using DataTable
     */
    public function view_all_patient_sample_urgent() {
        $this->load->library('patientwebservice');
        $reqData			= $this->input->post();
        $_data['reqData']	= $reqData;

        $result				= $this->psample_model->view_all_patient_sample_urgent($_data);
        $data['result']		= json_encode($result);

        $this->load->view('ajax_view/view_result', $data);
    }

    /**
     * Print Patient's sample rejected test
     * @param $patient_sample_id
     * @param string $action
     */
    public function preview_rejected_test($patient_sample_id, $action = 'preview') {
        $this->load->helper("sample_test");
        $this->load->model(['patient_sample_model', 'laboratory_model', 'patient_model']);

        /* Get Patient's Sample */
        $patient_sample	 = collect($this->patient_sample_model->get_patient_sample($patient_sample_id))->first();
        $psample_details = collect($this->patient_sample_model->get_patient_sample_detail($patient_sample_id))->keyBy('department_sample_id')->toArray();
        $patient_id		 = collect($patient_sample)->get('patient_id');
        $patient_info	 = isPMRSPatientID($patient_id) ? $this->patient_model->get_pmrs_patient($patient_id) : $this->patient_model->get_outside_patient($patient_id);

        //Test of Patient's sample
        $psample_tests  = collect($this->patient_sample_model->get_patient_sample_test($patient_sample_id))->filter(function($value) {
            return $value['is_rejected'] > 0 && !empty($value['group_result']);
        })->unique('group_result')->toArray();

        $this->data['patient']              = $patient_info;
        $this->data['patient_sample']       = $patient_sample;
        $this->data['psample_details']      = $psample_details;
        $this->data['psample_tests']        = sample_test_hierarchy(sample_test_hierarchy_row(convertToHierarchy($psample_tests, 'sample_test_id', 'testPID', 'childs', 'level')));
        $this->data['action']               = $action;
        $this->data['laboratory_variables'] = (array)$this->laboratory_model->get_variables();

        $this->load->view('template/print/psample_rejected_test.php', $this->data);
    }

    /**
     * Print/Preview Patient sample test
     * @param $patient_sample_id
     * @param string $action
     */
    public function preview_patient_sample_test($patient_sample_id, $action = 'preview') {
        $this->app_language->load(['patient', 'sample', 'pages/print_patient_sample_test']);
        $this->load->model(['patient_sample_model', 'patient_model', 'laboratory_model']);

        $patient_sample	        = collect($this->patient_sample_model->get_patient_sample($patient_sample_id))->first();
        $patient_id		        = collect($patient_sample)->get('patient_id');
        $patient_info	        = isPMRSPatientID($patient_id) ? $this->patient_model->get_pmrs_patient($patient_id) : $this->patient_model->get_outside_patient($patient_id);
        $patient_sample_tests   = collect($this->patient_sample_model->get_patient_sample_test($patient_sample_id))->filter(function($value) {
            return !empty($value['group_result']);
        })->unique('group_result')->groupBy('dep_sample_id')->toArray();

        $this->data['action']           = $action;
        $this->data['patient']          = $patient_info;
        $this->data['patient_sample']   = $patient_sample;
        $this->data['patient_sample_test_groups'] = $patient_sample_tests;
        $this->load->view('template/print/patient_sample_tests.php', $this->data);
    }

    /**
     * Lookup patient sample Test
     * Date: 07 Dec 2020
     * Sopheak HEM
     */
    public function is_assigned_test() {
        $patient_sample_id   = $this->input->post('patient_sample_id');
        $result     = $this->psample_model->is_assigned_test($patient_sample_id);
        echo json_encode(['result' => $result]);
        //echo $result;
    }
    /**
    * @desc countingg the result
    * @param $patient_sample_id
    * @return number
    */
    public function counting_result($patient_sample_id) {
        $this->db->from('camlis_ptest_result');
        $this->db->where('patient_sample_id', $patient_sample_id);
        $this->db->where('status', 1);
        return $this->db->get()->num_rows();
    }
    /**
    * @desc countingg the sample test
    * @param $patient_sample_id
    * @return number
    */
    public function counting_sample_test($patient_sample_id) {
        $this->db->from('camlis_patient_sample_tests as pst');
        $this->db->join('camlis_std_sample_test as std_test', 'std_test."ID" = pst.sample_test_id');
        $this->db->where('std_test.is_heading != true ', '', false);

        $this->db->where('pst.patient_sample_id', $patient_sample_id);
        $this->db->where('pst.is_rejected', false);
        $this->db->where('pst.status', 1);
        return $this->db->get()->num_rows();
    }
    /**
    * @desc update the patient sample verify
    * Set verify = 2 when counting result = counting test | => true
    * Set verify = 1 when counting result diferent from counting test => false
    * @return json $message
    */
    public function approve_result(){
        $data = array();
        if ($this->counting_result($this->input->post('patient_sample_id')) == $this->counting_sample_test($this->input->post('patient_sample_id'))) {
            $this->db->trans_start();
            //$this->db->set('verify', 2);
            $this->db->set('verify', true);
            //$this->db->set('verify_date', 'if(verify_date is null, \''.date('Y-m-d H:i:s').'\', concat(verify_date,\',\',\''.date('Y-m-d H:i:s').'\'))', FALSE);
            //$this->db->set('verify_by', 'if(verify_by is null, \''.$this->aauth->get_user()->username.'\', concat(verify_by,\',\',\''.$this->aauth->get_user()->username.'\'))', FALSE);
            
            //$this->db->set('verify_date', 'IF (verify_date == null) THEN  \''.date('Y-m-d H:i:s').'\'  ELSE concat(verify_date,\',\',\''.date('Y-m-d H:i:s').'\') END IF', FALSE);
            //$this->db->set('verify_by', ' IF (verify_by == null) THEN \''.$this->aauth->get_user()->username.'\' ELSE concat(verify_by,\',\',\''.$this->aauth->get_user()->username.'\') END IF ', FALSE);

            $this->db->set('verify_date', 'CASE WHEN length(verify_date) = 0 THEN \''.date('Y-m-d H:i:s').'\' ELSE concat(verify_date,\',\',\''.date('Y-m-d H:i:s').'\') END', FALSE);
            $this->db->set('verify_by', 'CASE WHEN length(verify_by) = 0 THEN \''.$this->aauth->get_user()->username.'\' ELSE concat(verify_by,\',\',\''.$this->aauth->get_user()->username.'\') END', FALSE);

            $this->db->where('"ID"', $this->input->post('patient_sample_id'));
            $this->db->update('camlis_patient_sample');
            $this->db->trans_complete();
            $message = ($this->db->trans_status()) ? true : false;
            $data = array('status' => 'complete', 'message' => $message, 'verify' => 2);
        }else{
            $this->db->trans_start();
            //$this->db->set('verify', 1);
            $this->db->set('verify', false);
            //$this->db->set('verify_date', 'if(verify_date is null, \''.date('Y-m-d H:i:s').'\', concat(verify_date,\',\',\''.date('Y-m-d H:i:s').'\'))', FALSE);
            //$this->db->set('verify_by', 'if(verify_by is null, \''.$this->aauth->get_user()->username.'\', concat(verify_by,\',\',\''.$this->aauth->get_user()->username.'\'))', FALSE);
            
            //$this->db->set('verify_date', 'IF (verify_date == null) THEN  \''.date('Y-m-d H:i:s').'\'  ELSE concat(verify_date,\',\',\''.date('Y-m-d H:i:s').'\') END IF', FALSE);
            //$this->db->set('verify_by', ' IF (verify_by == null) THEN \''.$this->aauth->get_user()->username.'\' ELSE concat(verify_by,\',\',\''.$this->aauth->get_user()->username.'\') END IF ', FALSE);

            $this->db->set('verify_date', 'CASE WHEN length(verify_date) = 0 THEN \''.date('Y-m-d H:i:s').'\' ELSE concat(verify_date,\',\',\''.date('Y-m-d H:i:s').'\') END', FALSE);
            $this->db->set('verify_by', 'CASE WHEN length(verify_by) = 0 THEN \''.$this->aauth->get_user()->username.'\' ELSE concat(verify_by,\',\',\''.$this->aauth->get_user()->username.'\') END', FALSE);
            $this->db->where('"ID"', $this->input->post('patient_sample_id'));
            $this->db->update('camlis_patient_sample');
            $this->db->trans_complete();
            $message = ($this->db->trans_status()) ? true : false;
            $data = array('status' => 'complete', 'message' => $message, 'verify' => 1);
        }
        echo json_encode($data);
    }
    /**
     * 18-05-2021
     * 
     */
    public function get_sample_by_sample_number_bk() {
        $this->load->model(['result_model', 'patient_model', 'test_model']);
        $this->load->helper(['sample_test', 'util']);

        $patient_id          = $this->input->post('patient_id');
        $patient_sample_id   = $this->input->post('patient_sample_id');
        $patient_code        = $this->input->post('patient_code');
        $sample_number       = $this->input->post('sample_number');
        $laboratory_id       = $this->input->post('laboratory_id');       
        $patient_info       = "";
        $patient_sample     = $this->psample_model->get_patient_sample($patient_sample_id, $sample_number, $patient_id, $patient_code, $laboratory_id);
        if($patient_sample){
            $patient_id         = $patient_sample[0]["patient_id"];
            $psample_id         = $patient_sample[0]["patient_sample_id"];
		    $patient_info		 = isPMRSPatientID($patient_id) ? $this->patient_model->get_pmrs_patient($patient_id) : $this->patient_model->get_outside_patient($patient_id);
            $status = true;
            //Sample Results
            // check if sample assigned test SAR-CoV-2 or not


            $_tmp_result	= $this->result_model->get_patient_sample_result($psample_id);
            $ptest_result	= array();
            if (count($_tmp_result) > 0) {
                foreach ($_tmp_result as $row) {
                    $patient_test_id = $row['patient_test_id'];
                    if (in_array($row['field_type'], array(1, 2)) && $row['type'] == 1) {
                        if (!isset($ptest_result[$patient_test_id])) {
                            $ptest_result[$patient_test_id] = elements(array('patient_test_id', 'sample_test_id', 'field_type', 'is_rejected', 'test_date', 'performer_id'),$row);
                            
                            $ptest_result[$patient_test_id]['result'] = array();
                        }
                        $test_organism_id = $row['result'];
                        if (!isset($ptest_result[$patient_test_id]['result'][$test_organism_id])) {
                            $ptest_result[$patient_test_id]['result'][$test_organism_id] = array(
                                'test_organism_id'  => $test_organism_id,
                                'organism_name'     => $row['organism_name'],
                                'quantity_id'       => $row['quantity_id'],
                                'antibiotic'        => array()
                            );
                        }
                        if ($row['antibiotic_id'] > 0 && isset($ptest_result[$patient_test_id]['result'][$test_organism_id])) {
                            $ptest_result[$patient_test_id]['result'][$test_organism_id]['antibiotic'][] = elements(array('antibiotic_id', 'sensitivity', 'disc_diffusion', 'test_zone', 'invisible'), $row);
                        }
                    } else {
                        $ptest_result[$patient_test_id] = elements(array('patient_test_id', 'sample_test_id', 'field_type', 'is_rejected', 'result', 'test_date', 'performer_id'),$row);
                    }
                }
                //Reindex result
                foreach ($ptest_result as $key => $item) {
                    if (is_array($item['result'])) {
                        $ptest_result[$key]['result'] = array_values($item['result']);
                    }
                }
            }
            //Test of Patient's sample
            $psample_tests              = $this->psample_model->get_patient_sample_test($psample_id);
            $psample_tests              = collect($psample_tests)->keyBy('sample_test_id')->toArray();
            $result['patient_info']		= $patient_info;
            $result['patient_sample']	= $patient_sample;
            $result['sample_tests']		= sample_test_hierarchy(reindexArray(convertToHierarchy($psample_tests, 'sample_test_id', 'testPID', 'childs'), 'childs'), TRUE);
            $result['sample_tests']     = collect($result['sample_tests'])->map(function($item) { $item->samples = array_values($item->samples); return $item; });
            $result['results']			= $ptest_result;
            $result['status']           = $status;

        }else{
            $result['msg']  = "No sample found";
            $result['status'] = false;
        }        
        
        $data['result'] = json_encode($result);                
        $this->load->view('ajax_view/view_result', $data);
    }

    public function get_sample_by_sample_number() {
        $this->load->model(['result_model', 'patient_model', 'test_model']);
        $this->load->helper(['sample_test', 'util']);
        $this->app_language->load('sample');

        $patient_id          = $this->input->post('patient_id');
        $patient_sample_id   = $this->input->post('patient_sample_id');
        $patient_code        = $this->input->post('patient_code');
        $sample_number       = $this->input->post('sample_number');
        $laboratory_id       = $this->input->post('laboratory_id');
        $patient_info        = "";
        $psample_tests       = "";

        $patient_sample      = $this->psample_model->get_patient_sample($patient_sample_id, $sample_number, $patient_id, $patient_code, $laboratory_id);
        if($patient_sample){
            $result['sample_msg']       = "";
            $result['sample_status']    = true;
            $patient_id                 = $patient_sample[0]["patient_id"];
            $psample_id                 = $patient_sample[0]["patient_sample_id"];
		    $patient_info		        = isPMRSPatientID($patient_id) ? $this->patient_model->get_pmrs_patient($patient_id) : $this->patient_model->get_outside_patient($patient_id);
            
            //Sample Results
            // check if sample assigned test SAR-CoV-2 or not
            $psample_tests              = $this->psample_model->get_test_sar_cov2($psample_id);
            if($psample_tests){
                $result['test_msg']     ="";
                $result['test_status']  = true;
            }else{
                $result['test_msg']     = _t('sample.no_test_sar_cov2_found');
                $result['test_status']  = false;
            }
        }else{
            $result['sample_msg']       = _t('sample.no_sample_found');
            $result['sample_status']    = false;
        }
        $result['sample_tests']         = $psample_tests;
        $result['patient_info']		    = $patient_info;
        $result['patient_sample']	    = $patient_sample;
        $data['result']                 = json_encode($result);
        $this->load->view('ajax_view/view_result', $data);
    }
    /**
     * Add Test Result via Line List
     * 
     */
    public function add_test_result(){
        $this->app_language->load(['pages/patient_sample_entry']);        
        $this->load->model('patient_sample_model', 'psample_model');
        $start_time         = microtime(true); 
        $data               = $this->input->post('data');
        $msg                = "";
        $status             = false;
        $laboratory_code    = CamlisSession::getLabSession('lab_code');   
        $this->load->library('phpqrcode/Qrlib');
        $SERVERFILEPATH     = $_SERVER['DOCUMENT_ROOT'].'/assets/camlis/images/patient_qr_code/';   
        $patient            = array(); 
        $col = array(
            "sample_number"   => 0,
            "patient_code"    => 1,
            "patient_name"    => 2,
            "test_name"       => 3,
            "machine_name"    => 4,
            "test_result"     => 5,
            "test_date"       => 6,
            "perform_by"      => 7,
            "test_id"         => 8,
            "psample_id"      => 9,
            "patient_test_id" => 10,
            "test_result_id"  => 11,
            "performer_by_id"       => 12,
            "sample_number_status"  => 13,
            "is_test_assigned"      => 14,
            "is_result_added"       => 15,
            "province"              => 16,
            "phone"                 => 17,
        );
        ini_set('memory_limit', '512M');
        ini_set('max_execution_time', 1200); // Zero means the script can run forever
        $this->load->model(['result_model','result_model']);
        foreach($data as $key => $row){
            if(!empty($row[$col["sample_number"]])){

                // Check if QR-CODE file exist
                // 16-06-2021                
                $file_name = $row[$col["patient_code"]].'.png';
                if (file_exists($SERVERFILEPATH.$file_name)) {
                    $patient["qr_code"]     = site_url()."/assets/camlis/images/patient_qr_code/".$file_name;
                    $patient["qr_code_status"]    = true;
                }else{
                    // if qr-code does not exist, generate a new one                    
                    $text = 'name='.$row[$col["patient_name"]].',phone='.$row[$col["phone"]].',location='.$row[$col["province"]].',pid='.$row[$col["patient_code"]];                                       
                    $file_name      = $row[$col["patient_code"]].".png";
                    $file_path 		= $SERVERFILEPATH.$row[$col["patient_code"]].".png";
                    QRcode::png($text,$file_path);
                    $patient["qr_code"]     = site_url()."/assets/camlis/images/patient_qr_code/".$file_name;
                }
                // End
                if(isset($row[$col["test_name"]]) && isset($row[$col["test_id"]])){
                    $is_result_added =  !empty($row[$col["is_result_added"]]) ? $row[$col["is_result_added"]] : "";
                    $organism_antibiotic_result = [];
                    $patient["sample_number"]   = $row[$col["sample_number"]];
                    $patient["patient_code"]    = $row[$col["patient_code"]];
                    $patient["patient_name"]    = $row[$col["patient_name"]];
                    $patient["test_name"]       = $row[$col["test_name"]];

                    if($row[$col["test_date"]] !== "" && $row[$col["performer_by_id"]] !== "" && $row[$col["test_result_id"]] !== ""){
                        $result_test_date   = ($row[$col["test_date"]] !== "") ? date('Y-m-d', strtotime($row[$col["test_date"]])) : null ;
                        $psample_id         = $row[$col["psample_id"]];
                        $patient_test_id    = $row[$col["patient_test_id"]];

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
                $patients[]  = $patient; 
            }
        }
        $end_time = microtime(true); 
        // Calculate the script execution time 
        $execution_time = ($end_time - $start_time);

        echo json_encode(array('patients' => $patients, 'execution_time' => $execution_time));
    }
}
