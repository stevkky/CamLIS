<?php
defined('BASEPATH') OR die("Access denied!");
class Result extends MY_Controller {
    public function __construct() {
        parent::__construct();
        $this->load->model(array('result_model', "sample_model", "patient_model"));

        //Load Language
        $this->app_language->load('sample');
    }

    /**
     * Get Qunatiy of result
     */
    public function resultQty() {
        $result = $this->result_model->getResultQty();
        return $result;
    }

    /**
     * Save Patient's Sample Result
     */
    public function save() {
        $this->aauth->control('add_psample_result');

        $this->load->model('patient_sample_model');

        $this->form_validation->set_rules('patient_sample_id', 'patient_sample_id', 'required|integer|greater_than[0]');

        $patient_sample_id	= $this->input->post("patient_sample_id");
        $results			= $this->input->post("results");
        $result_comment		= $this->input->post("result_comment");
        $patient_tests      = $this->input->post("patient_tests");
        $status             = false;
        $msg                = _t('global.msg.fill_required_data');

        //Set Result
        $is_valid = true;
        if ($this->form_validation->run() === TRUE) {
            $this->db->trans_start();
            if (is_array($results) && count($results) > 0) {
                $text_result                = array();
                $organism_antibiotic_result = array();
                $patient_test_info          = array();
                $patient_test_list          = array_column($results, 'patient_test_id');
                foreach ($results as $result) {
                    $result['patient_sample_id'] = $patient_sample_id;
                    $test_date = !isset($result['test_date']) ? false : DateTime::createFromFormat("Y-m-d", $result['test_date']);                    
                    $result['test_time'] = $result['test_time'] == "" ? NULL : $result['test_time'];//19032021
                    if (!$test_date || (int)$result['performer_id'] <= 0 || (int)$result['patient_test_id'] <= 0) {
                        $is_valid = false;
                        break;
                    }

                    if (in_array($result['field_type'], array(0, 3, 4, 5))) {
                        $text_result[]              = elements(['patient_sample_id', 'patient_test_id', 'performer_id', 'machine_name', 'test_date', 'result','test_time','reason_comment'], $result);
                        $patient_test_info[]        = elements(['patient_test_id', 'unit_sign', 'ref_range_min_value', 'ref_range_max_value', 'ref_range_sign'], $result);
                    } else if (in_array($result['field_type'], array(1, 2)) && is_array($result['result']) && count($result['result']) > 0) {
                        foreach ($result['result'] as $item) {
                            $organism_antibiotic_result[] = [
                                'patient_sample_id' => $result['patient_sample_id'],
                                'patient_test_id'   => $result['patient_test_id'],
                                'performer_id'      => $result['performer_id'],
                                'machine_name'      => $result['machine_name'],
                                'test_date'         => $result['test_date'],
                                'test_time'         => $result['test_time'], // 19072021
                                'result'            => $item['test_organism_id'],
                                'type'              => 1,
                                'quantity_id'       => isset($item['quantity_id']) ? $item['quantity_id'] : NULL,
                                'contaminant'       => isset($item['contaminant']) ? $item['contaminant'] : NULL,
                                'antibiotic'        => isset($item['antibiotic']) && is_array($item['antibiotic']) ? $item['antibiotic'] : array()
                            ];
                        }
                    }
                }
                if ($is_valid) {
                    $r = 0;
                    if (count($patient_test_list) > 0) $this->result_model->delete_ptest_result($patient_sample_id, $patient_test_list);
                    if (count($text_result) > 0){ 
                        $this->result_model->set_ptest_text_result($patient_sample_id, $text_result);
                    }
                    if (count($organism_antibiotic_result) > 0) $this->result_model->set_ptest_organism_antibiotic_result($patient_sample_id, $organism_antibiotic_result);

                    $patient_test_info = collect($patient_test_info)->map(function ($item) {
                        $item['"ID"'] = $item['patient_test_id'];
                        unset($item['patient_test_id']);
                        return $item;
                    })->toArray();
                    if (count($patient_test_info) > 0) $this->patient_sample_model->set_patient_sample_test_info($patient_sample_id, $patient_test_info);
                    $this->patient_sample_model->update_progress_status($patient_sample_id);
                }
            }
           
            //Save result comment
            $this->patient_sample_model->delete_result_comment($patient_sample_id);                        
            if (!empty($result_comment) && count($result_comment) > 0) $this->patient_sample_model->create_result_comment($result_comment);
 
            //Update patient test info
            if (count($patient_tests) > 0) {                
                $this->patient_sample_model->update_patient_sample_test($patient_sample_id, $patient_tests);
            }
           
            $this->db->trans_complete();
            if ($this->db->trans_status() == TRUE) {
                //$status = FALSE;
                $status = TRUE;
                $msg = _t('sample.msg.sresult_success');
            } else {
                $status = false;
                $msg = _t('sample.msg.save_fail');
            }
        }
        $users = $this->patient_sample_model->get_patient_sample_user($patient_sample_id);
        $data['result'] = json_encode(array('status' => $status && $is_valid, 'msg' => $msg, 'data' => ['users' => $users]));
        $this->load->view('ajax_view/view_result', $data);
    }

    /**
     * Fetch All result base on patient sample
     */
    public function get_patient_sample_result() {
        $patient_sample_id = $this->input->post('patient_sample_id');

        $result = array();
        if ((int)$patient_sample_id > 0) {
            $_tmp_result = $this->result_model->get_patient_sample_result($patient_sample_id);
            $result = $_tmp_result;
        }

        $data['result'] = json_encode($result);
        $this->load->view('ajax_view/view_result', $data);
    }

    /**
     * Print result Patient's sample
     * @param $patient_sample_id
     * @param string $type
     */
    public function preview_psample_result($patient_sample_id, $type = 'preview',$param_opt = '',$sample_id='') {
        $this->load->helper("sample_test");
        $this->load->model(['gazetteer_model', 'patient_sample_model', 'test_model', 'laboratory_model', 'reference_range_model']);

        // checking
        if($param_opt!=''){
            $dep_opt_view = str_replace("%2C",",",$param_opt);
            $sam_opt_view = str_replace("%2C",",",$sample_id);
        }else{
            $dep_opt_view = $this->input->post('department_optional_view');
            $sam_opt_view = $this->input->post('sample_optional_view');
        }



        /* Get Patient's Sample */
        $patient_sample	 = collect($this->patient_sample_model->get_patient_sample($patient_sample_id))->first();
        $psample_details = collect($this->patient_sample_model->get_patient_sample_detail($patient_sample_id))->keyBy('department_sample_id')->toArray();
        $patient_id		 = collect($patient_sample)->get('patient_id');
        $patient_info	 = isPMRSPatientID($patient_id) ? $this->patient_model->get_pmrs_patient($patient_id) : $this->patient_model->get_outside_patient($patient_id);

        //Get Result
        $hasAntibiotic	= FALSE;
        $_tmp_result    = $this->result_model->get_patient_sample_result($patient_sample_id);
        $ptest_result   = array();

        //var_dump($_tmp_result);

        if (count($_tmp_result) > 0) {
            foreach ($_tmp_result as $row) {
                $patient_test_id = $row['patient_test_id'];
                // array in 1: single 2: multiple
                // note 1 :single not to be apply in criteria
                //if (in_array($row['field_type'], array(1, 2)) && $row['type'] == 1) {
                if (in_array($row['field_type'], array(1,2)) && $row['type'] == 1) {

                    if (!isset($ptest_result[$patient_test_id])) {
                        $ptest_result[$patient_test_id]           = elements(['patient_test_id', 'sample_test_id', 'test_name', 'field_type', 'is_heading', 'is_rejected', 'test_date', 'first_test_date', 'performer_id'], $row);
                        $ptest_result[$patient_test_id]['result'] = array();
                    }
                    // checking result equal culture that have id 274
                    if ($row['test_name']=='Culture') {
                        $hasAntibiotic = TRUE;
                    }

                    $test_organism_id = $row['result'];
                    if (!isset($ptest_result[$patient_test_id]['result'][$test_organism_id])) {
                        $ptest_result[$patient_test_id]['result'][$test_organism_id] = array(
                            'test_organism_id'  => $test_organism_id,
                            'organism_name'     => $row['organism_name'],
                            'quantity_id'       => $row['quantity_id'],
                            'quantity'          => $row['quantity'],
                            'antibiotic'        => array()
                        );
                    }
                    if ($row['antibiotic_id'] > 0 && isset($ptest_result[$patient_test_id]['result'][$test_organism_id])) {
                        $ptest_result[$patient_test_id]['result'][$test_organism_id]['antibiotic'][] = elements(['antibiotic_id', 'antibiotic_name', 'sensitivity', 'test_zone', 'invisible'], $row);
                    }
                } else {
                    $ptest_result[$patient_test_id] = elements(['patient_test_id', 'sample_test_id', 'test_name', 'field_type', 'is_heading', 'is_rejected', 'result', 'test_date', 'first_test_date', 'performer_id'], $row);
                }
            }
        }

        $this->data['sensitivity_type']	= array(
            1 => array('abbr'	=> 'S', 'full'	=> 'Sensitive'),
            2 => array('abbr'	=> 'R', 'full'	=> 'Resistant'),
            3 => array('abbr'	=> 'I', 'full'	=> 'Intermediate')
        );

        //Test of Patient's sample
        $psample_tests  = $this->patient_sample_model->get_patient_sample_test($patient_sample_id, FALSE,0,$dep_opt_view,$sam_opt_view);

        //Get Ref. Ranges
        $sample_test_id       = array_column($psample_tests, 'sample_test_id');
        $age                  = getAge($patient_info['dob']);
        $gender               = !is_numeric($patient_info['sex']) && $patient_info['sex'] == 'M' ? MALE : FEMALE;
        $std_reference_ranges = collect($this->reference_range_model->get_std_reference_range($sample_test_id))->groupBy(function($item, $key) { return 'sample-test-'.$item['sample_test_id']; });
        $lab_reference_ranges = collect($this->reference_range_model->get_lab_reference_range($sample_test_id))->groupBy(function($item, $key) { return 'sample-test-'.$item['sample_test_id']; });
        $_reference_ranges    = $std_reference_ranges->merge($lab_reference_ranges)->flatten(1)->toArray();
        $reference_ranges     = [];

        if ($_reference_ranges) {
            foreach ($_reference_ranges as $ref_range) {
                if (isset($reference_ranges[$ref_range['sample_test_id']])) continue;
                $min_age = (int)($ref_range['min_age'] * $ref_range['min_age_unit']);
                $max_age = (int)($ref_range['max_age'] * $ref_range['max_age_unit']);

                if ($ref_range['is_equal'] == 1 && ($age >= $min_age && $age <= $max_age) && ($ref_range['gender'] == 3 || $ref_range['gender'] == $gender))
                {
                    $reference_ranges[$ref_range['sample_test_id']] = array('min_value' => $ref_range['min_value'], 'range_sign' => $ref_range['range_sign'], 'max_value' => $ref_range['max_value']);
                } else if ($age >= $min_age && $age < $max_age && ($ref_range['gender'] == 3 || $ref_range['gender'] == $gender))
                {
                    $reference_ranges[$ref_range['sample_test_id']] = array('min_value' => $ref_range['min_value'], 'range_sign' => $ref_range['range_sign'], 'max_value' => $ref_range['max_value']);
                }
            }
        }

        $this->data['patient']              = $patient_info;
        $this->data['patient_sample']       = $patient_sample;
        $this->data['psample_details']      = $psample_details;
        $this->data['psample_tests']        = sample_test_hierarchy(sample_test_hierarchy_row(convertToHierarchy($psample_tests, 'sample_test_id', 'testPID', 'childs', 'level')));
        $this->data['psample_results']      = $ptest_result;
        $this->data['ref_ranges']           = $reference_ranges;
        $this->data['type']                 = $type;
        $this->data['laboratory_variables'] = (array)$this->laboratory_model->get_variables();

        if ($hasAntibiotic == FALSE) $this->data['sensitivity_type'] = array();

        $this->load->view('template/print/psample_result.php', $this->data);
    }

    /**
     * Print/Preview Patient sample result
     * @param $action
     * @param $patient_sample_ids
     */
    public function patient_sample_result($action, $patient_sample_ids, $param_opt = '', $sample_id='') {
        $this->load->helper("sample_test");
        $this->load->model(['gazetteer_model', 'patient_sample_model', 'test_model', 'laboratory_model', 'reference_range_model']);
        $this->app_language->load('pages/patient_sample_result');

        // checking
        if($param_opt!=''){
            $dep_opt_view = str_replace("%2C",",",$param_opt);
            $sam_opt_view = str_replace("%2C",",",$sample_id);
        }else{
            $dep_opt_view = $this->input->post('department_optional_view');
            $sam_opt_view = $this->input->post('sample_optional_view');
        }

        $result = [];
        $patient_sample_ids = explode(',', urldecode($patient_sample_ids));

        
        foreach ($patient_sample_ids as $index => $patient_sample_id) {
            //Patient Sample
            $patient_sample = collect($this->patient_sample_model->get_patient_sample($patient_sample_id))->first();
            $psample_details = collect($this->patient_sample_model->get_patient_sample_detail($patient_sample_id))->keyBy('department_sample_id')->toArray();

            if (!$patient_sample) continue;

            //Patient Info
            $patient_id = collect($patient_sample)->get('patient_id');
            $patient_info = isPMRSPatientID($patient_id) ? $this->patient_model->get_pmrs_patient($patient_id) : $this->patient_model->get_outside_patient($patient_id);

            //Get Result
            $hasAntibiotic = FALSE;
            $_tmp_result = $this->result_model->get_patient_sample_result($patient_sample_id);
            $ptest_result = array();

            if (count($_tmp_result) > 0) {
                foreach ($_tmp_result as $row) {
                    $patient_test_id = $row['patient_test_id'];
                    //1: Single 2: Multiple
                    if (in_array($row['field_type'], array(1, 2)) && $row['type'] == 1) {

                        if (!isset($ptest_result[$patient_test_id])) {
                            $ptest_result[$patient_test_id] = elements(['patient_test_id', 'sample_test_id', 'test_name', 'field_type', 'is_heading', 'is_rejected', 'test_date', 'first_test_date', 'performer_id', 'machine_name'], $row);
                            $ptest_result[$patient_test_id]['result'] = array();
                        }
                        // checking result equal culture that have id 274
                        if ($row['test_name'] == 'Culture') {
                            $hasAntibiotic = TRUE;
                        }

                        $test_organism_id = $row['result'];
                        if (!isset($ptest_result[$patient_test_id]['result'][$test_organism_id])) {
                            $ptest_result[$patient_test_id]['result'][$test_organism_id] = array(
                                'test_organism_id'  => $test_organism_id,
                                'organism_name'     => $row['organism_name'],
                                'quantity_id'       => $row['quantity_id'],
                                'quantity'          => $row['quantity'],
                                'machine_name'      => $row['machine_name'],
                                'antibiotic'        => array()
                            );
                        }
                        if ($row['antibiotic_id'] > 0 && isset($ptest_result[$patient_test_id]['result'][$test_organism_id])) {
                            $ptest_result[$patient_test_id]['result'][$test_organism_id]['antibiotic'][] = elements(['antibiotic_id', 'antibiotic_name', 'sensitivity', 'test_zone', 'invisible'], $row);
                        }
                    } else {
                        $ptest_result[$patient_test_id] = elements(['patient_test_id', 'sample_test_id', 'test_name', 'field_type', 'is_heading', 'is_rejected', 'result', 'test_date', 'first_test_date', 'performer_id','machine_name'], $row);
                    }
                }
            }

            $this->data['sensitivity_type'] = array(
                1 => array('abbr' => 'S', 'full' => 'Sensitive'),
                2 => array('abbr' => 'R', 'full' => 'Resistant'),
                3 => array('abbr' => 'I', 'full' => 'Intermediate')
            );

            //Test of Patient's sample
            $psample_tests = $this->patient_sample_model->get_patient_sample_test($patient_sample_id, FALSE, 0, $dep_opt_view, $sam_opt_view);
            $psample_tests = collect($psample_tests)->keyBy('sample_test_id')->toArray();

            //Get Ref. Ranges
            $sample_test_id         = array_column($psample_tests, 'sample_test_id');
            $age                    = calculateAge($patient_info['dob'], $patient_sample['collected_date'], 'days');
            $age                    = $age > 0 ? $age : 1;
            $gender                 = !is_numeric($patient_info['sex']) && $patient_info['sex'] == 'M' ? MALE : FEMALE;
            $std_reference_ranges   = collect($this->reference_range_model->get_std_reference_range($sample_test_id))->groupBy(function ($item, $key) {
                return 'sample-test-' . $item['sample_test_id'];
            });
            $lab_reference_ranges = collect($this->reference_range_model->get_lab_reference_range($sample_test_id))->groupBy(function ($item, $key) {
                return 'sample-test-' . $item['sample_test_id'];
            });
            $_reference_ranges = $std_reference_ranges->merge($lab_reference_ranges)->flatten(1)->toArray();
            $reference_ranges = [];

            if ($_reference_ranges) {
                foreach ($_reference_ranges as $ref_range) {
                    $sample_test_id = $ref_range['sample_test_id'];
                    if (isset($reference_ranges[$ref_range['sample_test_id']])) continue;
                    $min_age = (int)($ref_range['min_age'] * $ref_range['min_age_unit']);
                    $max_age = (int)($ref_range['max_age'] * $ref_range['max_age_unit']);

                    if (!empty($psample_tests[$sample_test_id]) && (!is_null($psample_tests[$sample_test_id]['ref_range_min_value']) || !is_null($psample_tests[$sample_test_id]['ref_range_max_value']) || !is_null($psample_tests[$sample_test_id]['ref_range_sign']))) {
                        $reference_ranges[$sample_test_id] = ['min_value' => $psample_tests[$sample_test_id]['ref_range_min_value'], 'max_value' => $psample_tests[$sample_test_id]['ref_range_max_value'], 'range_sign' => $psample_tests[$sample_test_id]['ref_range_sign']];
                    } else if ($ref_range['is_equal'] == 1 && ($age >= $min_age && $age <= $max_age) && ($ref_range['gender'] == 3 || $ref_range['gender'] == $gender)) {
                        $reference_ranges[$ref_range['sample_test_id']] = array('min_value' => $ref_range['min_value'], 'range_sign' => $ref_range['range_sign'], 'max_value' => $ref_range['max_value']);
                    } else if ($age >= $min_age && $age < $max_age && ($ref_range['gender'] == 3 || $ref_range['gender'] == $gender)) {
                        $reference_ranges[$ref_range['sample_test_id']] = array('min_value' => $ref_range['min_value'], 'range_sign' => $ref_range['range_sign'], 'max_value' => $ref_range['max_value']);
                    }
                }
            }

            if ($hasAntibiotic == FALSE) $this->data['sensitivity_type'] = array();

            $this->data['patient_sample']               = $patient_sample;
            $this->data['patient_sample_details']       = $psample_details;
            $this->data['patient_info']                 = $patient_info;
            $this->data['patient_sample_laboratory']    = CamlisSession::getLabSession("labID") == $patient_sample['laboratory_id'] ? CamlisSession::getLabSession() : collect($this->laboratory_model->get_laboratory($patient_sample['laboratory_id']))->first();
            $this->data['patient_sample_laboratory']    = collect($this->data['patient_sample_laboratory']);
            $this->data['laboratory_variables']         = $this->laboratory_model->get_variables(NULL, $patient_sample['laboratory_id']);
            $this->data['patient_sample_tests']         = sample_test_hierarchy(sample_test_hierarchy_row(convertToHierarchy($psample_tests, 'sample_test_id', 'testPID', 'childs', 'level')));
            $this->data['patient_sample_results']       = $ptest_result;
            $this->data['ref_ranges']                   = $reference_ranges;
            $this->data['result_comment']               = collect($this->patient_sample_model->get_result_comment($patient_sample_id))->keyBy('department_sample_id')->toArray();
            $this->data['action'] = $index == count($patient_sample_ids) - 1 ? $action : '';
            $this->data['total_department_sample']      = collect($psample_tests)->pluck('dep_sample_id')->unique()->count();

            if ($this->input->server('REQUEST_METHOD') == 'POST')
                $result[] = ['patient_sample_id' => $patient_sample_id, 'template' => $this->load->view('template/print/patient_sample_result.php', $this->data, TRUE)];
            else
                $this->load->view('template/print/patient_sample_result.php', $this->data);
        }
        if ($this->input->server('REQUEST_METHOD') == 'POST') echo json_encode($result);
    }

    /**
     * Print/Preview Patient Covid Form
     * @param $action
     * @param $patient_sample_ids
     */
    public function patient_covid_form($action, $patient_sample_ids, $param_opt = '', $sample_id='') {
        $this->load->helper("sample_test");
        $this->load->model(['gazetteer_model', 'patient_sample_model',  'laboratory_model', 'patient_model','clinical_symptom_model','vaccine_model']);
        $this->app_language->load('pages/patient_sample_result');
        
        $result = [];
        $patient_sample_ids = explode(',', urldecode($patient_sample_ids));

        foreach ($patient_sample_ids as $index => $patient_sample_id) {
            //Patient Sample
            $patient_sample = collect($this->patient_sample_model->get_patient_sample($patient_sample_id))->first();            

            if (!$patient_sample) continue;

            //Patient Info
            $patient_id = collect($patient_sample)->get('patient_id');
            $patient_info = isPMRSPatientID($patient_id) ? $this->patient_model->get_pmrs_patient($patient_id) : $this->patient_model->get_outside_patient($patient_id);

            $this->data['patient_sample']               = $patient_sample;
            
            $this->data['patient_info']                 = $patient_info;
            $this->data['patient_sample_laboratory']    = CamlisSession::getLabSession("labID") == $patient_sample['laboratory_id'] ? CamlisSession::getLabSession() : collect($this->laboratory_model->get_laboratory($patient_sample['laboratory_id']))->first();
            $this->data['patient_sample_laboratory']    = collect($this->data['patient_sample_laboratory']);
            $this->data['action']                       = $index == count($patient_sample_ids) - 1 ? $action : '';
           
            $this->data['sample_test']                  = $this->patient_sample_model->is_tested_covid($patient_sample_id);
            $this->data['clinical_symptoms']            = $this->clinical_symptom_model->get(); //added 10 Jan 2021
            $this->data['clinical_symptoms_dd']         = $this->clinical_symptom_model->get_ps_clinical_symptom($patient_sample_id); //added 10 Jan 2021
            $this->data['vaccines']                     = $this->vaccine_model->get_vaccine(); //12-07-2021

            $this->data['number_of_sample']             = $this->patient_model->get_number_of_sample($patient_id);            
            if ($this->input->server('REQUEST_METHOD') == 'POST')
                $result[] = ['patient_sample_id' => $patient_sample_id, 'template' => $this->load->view('template/print/patient_covid_form.php', $this->data, TRUE)];
            else
                $this->load->view('template/print/patient_covid_form.php', $this->data);
        }
        if ($this->input->server('REQUEST_METHOD') == 'POST') echo json_encode($result);
    }

    // print multiples covid form
    public function patient_covid_forms($action, $patient_sample_ids) {
        $this->load->helper("sample_test");
        $this->load->model(['gazetteer_model', 'patient_sample_model',  'laboratory_model', 'patient_model','clinical_symptom_model','vaccine_model']);
        $this->app_language->load('pages/patient_sample_result');
        
        $result = [];
        $patient_sample_ids = explode('n', urldecode($patient_sample_ids));
        
        foreach ($patient_sample_ids as $index => $patient_sample_id) {
            //Patient Sample
            $patient_sample = collect($this->patient_sample_model->get_patient_sample($patient_sample_id))->first();

            if (!$patient_sample) continue;

            //Patient Info
            $patient_id = collect($patient_sample)->get('patient_id');
            $patient_info = isPMRSPatientID($patient_id) ? $this->patient_model->get_pmrs_patient($patient_id) : $this->patient_model->get_outside_patient($patient_id);

            $this->data['patient_sample']               = $patient_sample;
            
            $this->data['patient_info']                 = $patient_info;
            $this->data['patient_sample_laboratory']    = CamlisSession::getLabSession("labID") == $patient_sample['laboratory_id'] ? CamlisSession::getLabSession() : collect($this->laboratory_model->get_laboratory($patient_sample['laboratory_id']))->first();
            $this->data['patient_sample_laboratory']    = collect($this->data['patient_sample_laboratory']);
            $this->data['action'] = $index == count($patient_sample_ids) - 1 ? $action : '';
           
            $this->data['sample_test']                  = $this->patient_sample_model->is_tested_covid($patient_sample_id);
            $this->data['clinical_symptoms']            = $this->clinical_symptom_model->get(); //added 10 Jan 2021
            $this->data['clinical_symptoms_dd']         = $this->clinical_symptom_model->get_ps_clinical_symptom($patient_sample_id); //added 10 Jan 2021
            
            $this->data['number_of_sample']             = $this->patient_model->get_number_of_sample($patient_id);
            $this->data['vaccines']                     = $this->vaccine_model->get_vaccine();
            if ($this->input->server('REQUEST_METHOD') == 'POST')
                $result[] = ['patient_sample_id' => $patient_sample_id, 'template' => $this->load->view('template/print/patient_covid_form.php', $this->data, TRUE)];
            else
                $this->load->view('template/print/patient_covid_form.php', $this->data);
        }
        if ($this->input->server('REQUEST_METHOD') == 'POST') echo json_encode($result);
    }

}