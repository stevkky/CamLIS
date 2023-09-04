<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Request extends MY_Controller {

	public function __construct()
	{
		parent::__construct();
		//Load Language
		$this->app_language->load(array('admin', 'patient', 'request', 'manage'));
		// Active navigation request menu
		$this->data['cur_main_page'] = 'request';
	}
	// View all request
	public function index()
	{
		$this->app_language->load(['pages/view_request']);
		$this->template->plugins->add(array('DataTable', 'MomentJS'));
		$this->template->stylesheet->add('assets/camlis/css/camlis_request_style.css');
		$this->template->javascript->add('assets/camlis/js/camlis_view_request.js');
		$this->template->content->view('template/pages/request/view_request', $this->data);
		$this->template->content_title = _t('request.sample_list');
		$this->template->publish();
	}
	// Load all request for datatable
	public function view_all_patient_request()
	{
		$this->app_language->load(['pages/view_sample']);
		$this->load->model('request_model', 'request');
		$reqData = $this->input->post();
		$_data['reqData'] = $reqData;
		$result = $this->request->view_all_requests($_data);
		$data['result']	= json_encode($result);
		$this->load->view('ajax_view/view_result', $data);
	}
	// Show new request form
	public function add($patient_id = "")
	{
		// Load related model
		$this->load->model('department_model', 'department');
		$this->load->model('requester_model', 'requester');
		$this->load->model('gazetteer_model', 'gazetteer');
		$this->load->model('performer_model', 'performer');
		$this->load->model('sample_source_model', 'sample_source');
		$this->load->model('sample_model', 'sample');
		$this->load->model('quantity_model', 'quantity');
		$this->load->model('payment_type_model', 'payment_type');
		$this->load->model('test_model');

		$this->data['patient_id'] 			= $patient_id;
		$this->data['provinces'] 			= $this->gazetteer->get_province();
		$this->data['sample_source'] 		= $this->sample_source->get_lab_sample_source();
		$this->data['sample_descriptions']  = collect($this->sample->get_std_sample_descriptions())->groupBy('sample_id')->toArray();
		$this->data['payment_types'] 		= $this->payment_type->get_lab_payment_type();
		$this->data['test_payments'] 		= $this->test_model->get_lab_test_payment();
		$this->data['departments']   		= $this->department->get_std_department();

		$this->template->plugins->add(['MomentJS', 'TreeView', 'AutoNumeric', 'BootstrapDateTimePicker', 'BootstrapDatePicker', 'BootstrapTimePicker', 'MathExpressionEvaluator', 'AutoComplete']);
		$this->template->stylesheet->add('assets/camlis/css/camlis_request_style.css');
		$this->template->javascript->add('assets/camlis/js/camlis_request_script.js');
		$this->template->content->view('template/pages/request/add_request', $this->data);
		$this->template->content->view('template/pages/request/sample_form_templete', $this->data);
		$this->template->content_title = _t('patient.patient_information');
		$this->template->modal->view('template/modal/modal_patient_request_existed_patient');
		$this->template->modal->view('template/modal/modal_patient_request_std_test');
		$this->template->publish();
	}

	public function save()
	{
		$this->aauth->control('request_sample');
		$status = true;
		$msg = "";
		$users = array();
		if (!empty($this->input->post())) {
			$patient_sample_id = 0;
			$patient = elements(['pid', 'name', 'sex', 'dob', 'phone', 'province', 'district', 'commune', 'village'], (array)$this->input->post('patient'));
			$sample = elements(array('patient_id', 'sample_source_id', 'requester_id', 'is_urgent', 'payment_type_id', 'for_research', 'clinical_history', 'admission_date'), (array)$this->input->post());
			// set progress status to request
			$sample['progress_status'] = 6;
			$is_pmrs_patient_id = isPMRSPatientID($patient['pid']);
			$is_saved_patient  = false;
			if ($is_pmrs_patient_id) {
				$province = !empty($patient['province']) ? $patient['province'] : -1;
                $district = !empty($patient['district']) ? $patient['district'] : -1;
                $commune  = !empty($patient['commune']) ? $patient['commune'] : -1;
                $village  = !empty($patient['village']) ? $patient['village'] : -1;
                $is_saved_patient = $this->db->replace('camlis_pmrs_patient', $patient);
			}
			if (!$is_pmrs_patient_id || ($is_pmrs_patient_id && $is_saved_patient)) {
				$this->load->model('patient_sample_model', 'psample_model');
				$sample_number_type = CamlisSession::getLabSession('sample_number');
				$this->db->trans_begin();
				if ($sample_number_type == 2) {
					$sample['sample_number'] = $sample["sample_number"].'-'.date('dmY');
				} else {
					$_r = $this->psample_model->get_psample_number();
                    $sample['sample_number'] = $_r->sample_number;
				}
				if ($this->psample_model->is_unique_sample_number($sample['sample_number'])) {
					// counting micro
					$count_micro = array();
                    if (is_array($this->input->post('sample_tests')) && count($this->input->post('sample_tests'))) {
                        $count_micro = array_filter($this->input->post('sample_tests'), function($value){
                            $micros = array('170', '187', '203', '207', '212', '224', '226', '230', '231', '235', '264', '280');
                            return $value == in_array($value, $micros);
                        });
                    }
                    $sample['micro'] = count($count_micro);
					$patient_sample_id = $this->psample_model->add_patient_sample($sample);
				} else {
					$msg = _t('sample.msg.sample_number_exist');
				}
				if ($patient_sample_id > 0) {
					$status = true;
					$msg = _t("global.msg.save_success");
					$users = $this->psample_model->get_patient_sample($patient_sample_id);
					if (count($users) > 0) {
						$users = $users[0];
                    	$users['users'] = $this->psample_model->get_patient_sample_user($patient_sample_id);
					}
				}
				$is_assign_test = $this->input->post('is_assign_test');
                $sample_tests = $this->input->post('sample_tests');
				$sample_details = $this->input->post('sample_details');
				$test_payments = $this->input->post('test_payments');
				if ($patient_sample_id > 0 && (int)$is_assign_test == 200 && is_array($sample_tests) && count($sample_tests) > 0) {
					$this->psample_model->assign_sample_test($patient_sample_id, $sample_tests);
					$this->psample_model->set_psample_detail($patient_sample_id, $sample_details);
					$this->psample_model->update_rejection_status($patient_sample_id);
                    //$this->psample_model->update_progress_status($patient_sample_id);
				}
				$test_payments = collect($test_payments)->map(function($d) use ($patient_sample_id) { 
                	$d['patient_sample_id'] = $patient_sample_id; return $d; 
                })->toArray();
                if ($patient_sample_id > 0 && count($test_payments) > 0) {
                    $this->psample_model->add_patient_sample_test_payment($test_payments);
                }
                ($this->db->trans_status() === FALSE) ? $this->db->trans_rollback() : $this->db->trans_commit() ;
			}
		}
		echo json_encode(array('status' => $status, 'msg' => $msg, 'data'=> $users, 'test'=>$sample_tests));
	}

	public function edit($patient_sample_id, $action = null)
	{
		/*===========load model=============*/
		$this->load->model('patient_model', 'patient');
		$this->load->model('patient_sample_model', 'patient_sample');
		$this->load->model('sample_model', 'sample');
		$this->load->model('department_model', 'department');
		$this->load->model('gazetteer_model', 'gazetteer');
		$this->load->model('sample_source_model', 'sample_source');
		$this->load->model('requester_model', 'requester');
		$this->load->model('performer_model', 'performer');
		$this->load->model('quantity_model', 'quantity');
		$this->load->model('payment_type_model', 'payment_type');
		$this->load->model('test_model', 'test');
		/*=========pass data to view=============*/
		$patient_sample	= collect($this->patient_sample->get_patient_sample($patient_sample_id))->first();
		$patient_id		= collect($patient_sample)->get('patient_id');
		// check patient code
		$patient_info	= isPMRSPatientID($patient_id) ? $this->patient->get_pmrs_patient($patient_id) : $this->patient->get_outside_patient($patient_id);
		$patient_sample['is_assigned_test']	= $this->patient_sample->is_assigned_test($patient_sample_id);
        $patient_sample['test_payments']    = $this->patient_sample->get_patient_sample_test_payment($patient_sample_id);

        $this->data['patient']              = $patient_info;
        $this->data['patient_sample']       = $patient_sample;
        $this->data['patient_sample_user']  = $this->patient_sample->get_patient_sample_user($patient_sample_id);
        $this->data['departments']          = $this->department->get_std_department();
		$this->data['sample_source']        = $this->sample_source->get_lab_sample_source();
		$this->data['sample_descriptions']  = collect($this->sample->get_std_sample_descriptions())->groupBy('sample_id')->toArray();
		$this->data['requesters']           = $this->requester->get_lab_requester(FALSE, collect($patient_sample)->get('sample_source_id', -1));
		$this->data['performers']           = $this->performer->get_lab_performer();
		$this->data['provinces']            = $this->gazetteer->get_province();
		$this->data['organism_quantity']    = $this->quantity->get_std_organism_quantity();
        $this->data['payment_types']        = $this->payment_type->get_lab_payment_type();
        $this->data['test_payments']        = $this->test->get_lab_test_payment();
        $this->data['page_action']          = $action;
		/*==========load plugin====================*/
		$this->template->plugins->add(['DataTable', 'AutoNumeric', 'TreeView', 'MomentJS', 'BootstrapDateTimePicker', 'BootstrapDatePicker', 'BootstrapTimePicker', 'MultipleList', 'MathExpressionEvaluator', 'AutoComplete']);
		/*=========load css and js================*/
		$this->template->stylesheet->add('assets/camlis/css/camlis_request_style.css');
		$this->template->javascript->add('assets/camlis/js/camlis_request_script.js');
		/*============load view===================*/
		$this->template->content->view('template/pages/request/edit_request', $this->data);
		$this->template->content->view('template/pages/request/sample_form_templete', $this->data);
		/*========load modal====================*/
		$this->template->modal->view('template/modal/modal_patient_request_std_test');
		$this->template->content_title = _t('patient.patient_information');
		$this->template->publish();
	}

	public function update()
	{
		$this->aauth->control('edit_request_sample');
		$status = false;
		$msg = "";
		// store user operation this record
		$users = array();
		// post request
		if (!empty($this->input->post())) {
			$patient = elements(['pid','name','sex','dob','phone','province','district','commune','village'], (array)$this->input->post('patient'));
			$sample = elements(array('patient_id', 'sample_source_id', 'requester_id', 'is_urgent', 'payment_type_id', 'for_research', 'clinical_history', 'admission_date'), (array)$this->input->post());

			$sample_tests = $this->input->post('sample_tests');
			$sample_details = $this->input->post('sample_details');
			$test_payments = $this->input->post('test_payments');
			$is_pmrs_patient_id = isPMRSPatientID($patient['pid']);
			$is_saved_patient  = false;
			if ($is_pmrs_patient_id) {
                $province = !empty($patient['province']) ? $patient['province'] : -1;
                $district = !empty($patient['district']) ? $patient['district'] : -1;
                $commune  = !empty($patient['commune']) ? $patient['commune'] : -1;
                $village  = !empty($patient['village']) ? $patient['village'] : -1;
                $is_saved_patient = $this->db->replace('camlis_pmrs_patient', $patient);
            }
           
            if (!$is_pmrs_patient_id || ($is_pmrs_patient_id && $is_saved_patient)) {
            	$this->load->model('patient_sample_model', 'psample_model');
            	$patient_sample_id = $this->input->post('patient_sample_id');
            	// counting micro
            	$count_micro = array();
                if (is_array($this->input->post('sample_tests')) && count($this->input->post('sample_tests'))) {
                    $count_micro = array_filter($this->input->post('sample_tests'), function($value){
                        $micros = array('170', '187', '203', '207', '212', '224', '226', '230', '231', '235', '264', '280');
                        return $value == in_array($value, $micros);
                    });
                }
                $sample['micro'] = count($count_micro);
            	if ($this->psample_model->update_patient_sample($patient_sample_id, $sample) > 0) {
            		$status = true;
                    $msg = _t("global.msg.update_success");
                    $users = $this->psample_model->get_patient_sample($patient_sample_id);
                    if (count($users) > 0) {
                    	$users = $users[0];
                    	$users['users'] = $this->psample_model->get_patient_sample_user($patient_sample_id);
                    }
            	}

            	if ($patient_sample_id > 0 && is_array($sample_tests) && count($sample_tests) > 0) {
                    $this->psample_model->assign_sample_test($patient_sample_id, $sample_tests);
                    $this->psample_model->set_psample_detail($patient_sample_id, $sample_details);
                    $this->psample_model->update_rejection_status($patient_sample_id);
                    //$this->psample_model->update_progress_status($patient_sample_id);
                }
                $test_payments = collect($test_payments)->map(function($d) use ($patient_sample_id) { 
                	$d['patient_sample_id'] = $patient_sample_id; return $d; 
                })->toArray();
                if ($patient_sample_id > 0) $this->psample_model->delete_patient_sample_test_payment($patient_sample_id);
                if ($patient_sample_id > 0 && count($test_payments) > 0) {
                    $this->psample_model->add_patient_sample_test_payment($test_payments);
                }
            }

		}
		echo json_encode(array('status' => $status, 'msg' => $msg, 'data'=> $users));
	}
}