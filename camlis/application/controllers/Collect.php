<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Collect extends MY_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->app_language->load(array('admin', 'patient', 'request', 'manage'));
	}

	public function sample($patient_sample_id)
	{
		$this->load->model('patient_model', 'patient');
		$this->load->model('patient_sample_model', 'patient_sample');
		$patient_sample = collect($this->patient_sample->get_patient_sample($patient_sample_id))->first();
		$patient_id     = collect($patient_sample)->get('patient_id');
		$patient_info	= isPMRSPatientID($patient_id) ? $this->patient->get_pmrs_patient($patient_id) : $this->patient->get_outside_patient($patient_id);

		$patient_sample_tests   = collect($this->patient_sample->get_patient_sample_test($patient_sample_id))->filter(function($value) {
            return !empty($value['group_result']);
        })->unique('group_result')->groupBy('dep_sample_id')->toArray(); 
		
		$this->data['patient']			= $patient_info;
		$this->data['patient_sample'] 	= $patient_sample;
		$this->data['patient_sample_test_groups'] = $patient_sample_tests;
		$this->template->plugins->add(['MomentJS', 'BootstrapDateTimePicker', 'BootstrapDatePicker', 'BootstrapTimePicker']);
		$this->template->stylesheet->add('assets/camlis/css/print/patient_sample_test.css');
		$this->template->javascript->add('assets/camlis/js/camlis_collect_script.js');
		$this->template->content->view('template/pages/collect_sample', $this->data);
		$this->template->content_title = _t('patient.patient_information');
		$this->template->publish();
	}

	public function save()
	{
		$this->aauth->control('collect_sample');
		$patient_sample = $this->input->post('patient_sample');
		$patient_sample_id = $patient_sample['patient_sample_id'];
		$patient_sample['progress_status'] = 7;
		unset($patient_sample['patient_sample_id']);
		$this->db->set($patient_sample);
		$this->db->where('status', 1);
		$this->db->where('ID', $patient_sample_id);
		$this->db->update('camlis_patient_sample');
		echo json_encode(array('status' => true));
	}

}