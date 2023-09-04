<?php
defined('BASEPATH') OR die('No direct script allowed.');
class Sample extends MY_Controller {
	public function __construct() {
		parent::__construct();
		$this->load->model(array('sample_model'));
		
		//Load Language
		$this->app_language->load(array('admin', 'patient', 'sample', 'manage'));

		$this->data['cur_main_page'] = 'patient_sample';
	}
	
	/* ================================ View ================================ */
	/**
	 * View All Sample
	 */
	public function view() {
		$this->load->model(['performer_model']);
		$this->app_language->load(['pages/view_sample']);		
		$this->template->plugins->add(array('DataTable', 'MomentJS', 'BootstrapDateTimePicker', 'AutoNumeric','DataTableFixedColumn'));
		// add sorting library for date time
		// 18-03-2021
		//<script src="https://cdn.datatables.net/plug-ins/1.10.19/sorting/datetime-moment.js"></script>
		$this->template->javascript->add('assets/plugins/datetime-moment.js');
		$this->template->javascript->add('assets/plugins/moment-2.20.1.min.js');
		//End
		$this->data['performers']           = $this->performer_model->get_lab_performer();
		$this->data['can_edit_psample']   	= $this->aauth->is_allowed('edit_psample');
		
        $this->template->stylesheet->add('assets/camlis/css/camlis_sample_style.css');
		
		$this->template->javascript->add('assets/camlis/js/camlis_view_sample.js?_='.time());
		$this->template->content->view('template/pages/view_sample', $this->data);
        $this->template->modal->view('template/modal/modal_patient_sample_preview_result');
		//04-06-2021: add result
		//jspreadsheet
		if($this->aauth->is_allowed('edit_psample')){
			$this->template->stylesheet->add('assets/plugins/jspreadsheet/css/jsuites.css');
			$this->template->stylesheet->add('assets/plugins/jspreadsheet/css/jexcel.css');
			$this->template->javascript->add('assets/plugins/jspreadsheet/js/jexcel.js');
			$this->template->javascript->add('assets/plugins/jspreadsheet/js/jsuites.js');		
			$this->template->javascript->add('assets/camlis/js/camlis_add_test_result_line_list.js?_='.time());
			$this->template->javascript->add('assets/plugins/printjs/print.min.js'); // 16-06-2021
			$this->template->modal->view('template/modal/modal_add_sample_result');
			$this->template->modal->view('template/modal/modal_test_results');
			$this->template->modal->view('template/modal/modal_error_add_result'); // added 17-04-2021
			$this->template->modal->view('template/modal/modal_qr_code'); //16-06-2021
		}
		// end
		$this->template->content_title = _t('sample.sample_list');
		$this->template->publish();
	}
	/**
	 * Add New Sample View
	 * @param $patient_id
	 */
	public function new_sample($patient_id = "") {		
	    $this->aauth->control('add_psample');
		$this->load->model([
			'department_model',
			'requester_model',
			'gazetteer_model',
			'performer_model',
			'sample_source_model',
			'quantity_model',
            'payment_type_model',
			'test_model',
			'patient_sample_model',
			'country_model',
			'clinical_symptom_model',
			'user_model',
			'organism_model',
			'machine_model',
			'vaccine_model'
        ]);

		$this->data['patient_id']           = $patient_id;
		$this->data['departments']          = $this->department_model->get_std_department();
		$this->data['sample_source']        = $this->sample_source_model->get_lab_sample_source();
        $this->data['sample_descriptions']  = collect($this->sample_model->get_std_sample_descriptions())->groupBy('sample_id')->toArray();
		$this->data['provinces']            = $this->gazetteer_model->get_province();		
		$this->data['performers']           = $this->performer_model->get_lab_performer();
		$this->data['organism_quantity']    = $this->quantity_model->get_std_organism_quantity();
        $this->data['payment_types']        = $this->payment_type_model->get_lab_payment_type();
		$this->data['test_payments']        = $this->test_model->get_lab_test_payment();
		$this->data['countries']            = $this->country_model->get_country();
		$this->data['nationalities']        = $this->country_model->get_nationality();
		$this->data['clinical_symptoms']    = $this->clinical_symptom_model->get();
		// added 22-03-2021 for LINE LIST
		$this->data['districts']            = $this->gazetteer_model->get_district();
		$this->data['communes']             = $this->gazetteer_model->get_commune_();
		$this->data['villages']             = $this->gazetteer_model->get_village_();
		$this->data['requester']            = $this->requester_model->get_lab_requester(false);
		$this->data['rrt_user']             = $this->user_model->get_rrt_user(); //
		$this->data['vaccines']             = $this->vaccine_model->get_vaccine(); //12-07-2021
		
		//END
		//print_r($this->data['clinical_symptoms']);

		$this->template->plugins->add(['DataTable', 'AutoNumeric', 'TreeView', 'MomentJS', 'BootstrapDateTimePicker', 'BootstrapDatePicker', 'BootstrapTimePicker','MultipleList', 'MathExpressionEvaluator','AutoComplete']);		
		$this->template->stylesheet->add('assets/camlis/css/camlis_sample_style.css');
		$this->template->stylesheet->add('assets/plugins/qtip/jquery.qtip.min.css');
		$this->template->javascript->add('assets/plugins/qtip/jquery.qtip.min.js');
        $this->template->javascript->add('assets/camlis/js/camlis_variable_format.js');
		$this->template->javascript->add('assets/camlis/js/camlis_sample_script.js?_='.time());

		$this->template->content->view('template/pages/add_sample', $this->data);
		$this->template->content->view('template/patient-sample-form-template', $this->data);
		/* Load Modal */
		$this->template->modal->view('template/modal/modal_patient_sample_std_test');
		$this->template->modal->view('template/modal/modal_reject_patient_sample');
		$this->template->modal->view('template/modal/modal_patient_sample_test_result_entry', $this->data);
		$this->template->modal->view('template/modal/modal_patient_sample_possible_result', $this->data);
		$this->template->modal->view('template/modal/modal_patient_sample_result_comment');
		$this->template->modal->view('template/modal/modal_patient_sample_preview_result');
		$this->template->modal->view('template/modal/modal_patient_sample_reject_comment');
		
		/**
		 * Add 19-03-2021
		 * Modal Line List
		 */
		//jspreadsheet
		$this->template->stylesheet->add('assets/plugins/jspreadsheet/css/jsuites.css');
		$this->template->stylesheet->add('assets/plugins/jspreadsheet/css/jexcel.css');
		$this->template->javascript->add('assets/plugins/jspreadsheet/js/jexcel.js');
		$this->template->javascript->add('assets/plugins/jspreadsheet/js/jsuites.js');

		// Backup Line List Version 1		
		$this->template->javascript->add('assets/plugins/printjs/print.min.js');
		$this->template->javascript->add('assets/camlis/js/camlis_line_list.js');
		//$this->template->modal->view('template/modal/modal_result_line_list');
		
		/** For admin only to dev */
		if($this->session->userdata("roleid") == 1){
			$this->template->modal->view('template/modal/modal_tmp_patient'); // added 22-04-2021
			// modal exel short form
		}
		/** End */

		/** End */
		//02-04-2021 For Dev		
		$this->template->modal->view('template/modal/modal_excel_full_form'); // added 30-04-2021
		$this->template->modal->view('template/modal/modal_excel_short_form'); // added 22-04-2021
		$this->template->modal->view('template/modal/modal_error_line_list_new'); // added 17-04-2021
		$this->template->modal->view('template/modal/modal_result_line_list_new');
		$this->template->modal->view('template/modal/modal_qr_code');
		
		
		$this->template->modal->view('template/modal/modal_covid_form_preview_result'); // header the covid form
		
		//31-05-2021
		$this->template->modal->view('template/modal/modal_upload_excel_file');
		//
		$this->template->modal->view('template/modal/modal_patient_sample_existed_patient');
		$this->template->modal->view('template/modal/modal_machine');
		$this->template->modal->view('template/modal/modal_datetime_picker'); //16092022
		$this->template->modal->view('template/modal/modal_comment');
		$this->template->content_title = _t('patient.patient_information');
        if ($this->aauth->is_allowed('edit_patients')) $this->template->content_title .= "&nbsp;&nbsp;<button type='button' id='edit-patient' class='btn btn-primary btn-sm' style='display: none;'><i class='fa fa-pencil-square-o'></i>&nbsp;" . _t('global.edit') . "</button>";
		$this->template->publish();
	}
	
	/**
	 * Edit Sample
	 * @param $patient_sample_id
     * @param $action
	 */
	public function edit($patient_sample_id, $action = NULL) {
		$this->load->model(array(
			'patient_sample_model',
			'department_model',
			'patient_model',
			'gazetteer_model',
			'sample_source_model',
			'requester_model',
			'performer_model',
			'quantity_model',
            'payment_type_model',
			'test_model',
			'clinical_symptom_model',
			'country_model',
			'vaccine_model'
		));
		$patient_sample						= collect($this->patient_sample_model->get_patient_sample($patient_sample_id))->first();		
		$patient_id							= collect($patient_sample)->get('patient_id');
		$patient_info						= isPMRSPatientID($patient_id) ? $this->patient_model->get_pmrs_patient($patient_id) : $this->patient_model->get_outside_patient($patient_id);
		
		$this->data['isPMRSPatientID']		= isPMRSPatientID($patient_id);// for testing 

		$patient_sample['is_assigned_test']	= $this->patient_sample_model->is_assigned_test($patient_sample_id);
        $patient_sample['test_payments']    = $this->patient_sample_model->get_patient_sample_test_payment($patient_sample_id);

		$this->data['patient']              = $patient_info;
		$this->data['patient_sample']       = $patient_sample;
		$this->data['patient_sample_user']  = $this->patient_sample_model->get_patient_sample_user($patient_sample_id);
		$this->data['departments']          = $this->department_model->get_std_department();
		$this->data['sample_source']        = $this->sample_source_model->get_lab_sample_source();
		$this->data['sample_descriptions']  = collect($this->sample_model->get_std_sample_descriptions())->groupBy('sample_id')->toArray();
		$this->data['requesters']           = $this->requester_model->get_lab_requester(FALSE, collect($patient_sample)->get('sample_source_id', -1));
		$this->data['performers']           = $this->performer_model->get_lab_performer();
		$this->data['provinces']            = $this->gazetteer_model->get_province();
		$this->data['organism_quantity']    = $this->quantity_model->get_std_organism_quantity();
        $this->data['payment_types']        = $this->payment_type_model->get_lab_payment_type();
		$this->data['test_payments']        = $this->test_model->get_lab_test_payment();
		$this->data['clinical_symptoms']    = $this->clinical_symptom_model->get(); //added 04 Dec 2020
		$this->data['clinical_symptoms_dd'] = $this->clinical_symptom_model->get_ps_clinical_symptom($patient_sample_id); //added 04 Dec 2020
		$this->data['countries']            = $this->country_model->get_country();
		$this->data['nationalities']        = $this->country_model->get_nationality();
		$this->data['vaccines']        		= $this->vaccine_model->get_vaccine();
		$this->data['page_action']          = $action;

		$this->template->plugins->add(['DataTable', 'AutoNumeric', 'TreeView', 'MomentJS', 'BootstrapDateTimePicker', 'BootstrapDatePicker', 'BootstrapTimePicker', 'MultipleList', 'MathExpressionEvaluator','AutoComplete']);
		$this->template->stylesheet->add('assets/camlis/css/camlis_sample_style.css');
		$this->template->stylesheet->add('assets/plugins/qtip/jquery.qtip.min.css');
		$this->template->javascript->add('assets/plugins/qtip/jquery.qtip.min.js');
        $this->template->javascript->add('assets/camlis/js/camlis_variable_format.js');
        $this->template->javascript->add('assets/camlis/js/camlis_sample_script.js?_='.time());
		$this->template->content->view('template/pages/edit_sample', $this->data);
        $this->template->content->view('template/patient-sample-form-template', $this->data);
		/* Load Modal */
		$this->template->modal->view('template/modal/modal_patient_sample_std_test');
		$this->template->modal->view('template/modal/modal_reject_patient_sample');
		$this->template->modal->view('template/modal/modal_patient_sample_test_result_entry', $this->data);
		$this->template->modal->view('template/modal/modal_patient_sample_possible_result', $this->data);
		$this->template->modal->view('template/modal/modal_patient_sample_result_comment');
		$this->template->modal->view('template/modal/modal_patient_sample_preview_result');
		$this->template->modal->view('template/modal/modal_patient_sample_reject_comment');
		$this->template->modal->view('template/modal/modal_covid_form_preview_result'); // header the covid form
		$this->template->modal->view('template/modal/modal_machine');
		$this->template->modal->view('template/modal/modal_datetime_picker'); //16092022
		$this->template->modal->view('template/modal/modal_comment');
		$this->template->content_title = _t('patient.patient_information');
        if (!isPMRSPatientID($patient_id) && $this->aauth->is_allowed('edit_patients')) {            
			if(!isset($patient_info['parent_code'])) $this->template->content_title .= "&nbsp;&nbsp;<button type='button' id='edit-patient' class='btn btn-primary btn-sm' data-parent_code='".$patient_info['parent_code']."'><i class='fa fa-pencil-square-o'></i>&nbsp;" . _t('global.edit') . "</button>";
        }
		$this->template->publish();
	}
	
	/* ================================  End View ================================ */

	/**
	 * Add New Standard Sample
	 */
	public function add_std_sample() {
		$sample_name	= $this->input->post("sample_name");
		$departments	= $this->input->post("departments");
		$descriptions   = $this->input->post('descriptions');
		$sample_name	= trim($sample_name);
		$status			= FALSE;
		$msg			= _t('global.msg.fill_required_data');

		if (!empty($sample_name) && count($departments) > 0) {
			$sample = $this->sample_model->get_std_sample(array('sample_name' => $sample_name));
			if (count($sample) > 0) {
				$msg = _t('admin.msg.sample_type_exist');
			}
			else {
				$sample_id = $this->sample_model->add_std_sample(array('sample_name' => $sample_name));
				$msg = _t('global.msg.save_fail');

				if ($sample_id > 0 && is_array($departments) && count($departments) > 0) {
					$dep_sample = array();
					foreach ($departments as $dep) {
						$dep_sample[] = array(
							'department_id' => $dep,
							'sample_id'		=> $sample_id
						);
					}

					$result = $this->sample_model->assign_std_department_sample($dep_sample);

					if ($result == count($departments)) {
						$status = TRUE;
						$msg = _t('global.msg.save_success');
					} else {
						$this->sample_model->delete_std_sample($sample_id);
					}

					//Set Sample Description
                    $this->sample_model->set_std_sample_description($sample_id, (array)$descriptions);
				}
			}
		}

		$data['result']			= json_encode(array('status' => $status, 'msg' => $msg));
		$this->load->view('ajax_view/view_result', $data);
	}

	/**
	 * Update Standard Sample
	 */
	public function update_std_sample() {
		$sample_name	= $this->input->post("sample_name");
		$sample_id		= $this->input->post("sample_id");
		$departments	= $this->input->post("departments");
        $descriptions   = $this->input->post('descriptions');
		$sample_name	= trim($sample_name);
		$status			= FALSE;
		$msg			= _t('global.msg.fill_required_data');

		if (!empty($sample_name) && count($departments) > 0 && $sample_id > 0) {
			$sample = $this->sample_model->get_std_sample(array('sample_name' => $sample_name, 'ID !=' => $sample_id));
			if (count($sample) > 0) {
				$msg = _t('admin.msg.sample_type_exist');
			}
			else {
				$assigned_dep_sample = $this->sample_model->get_std_department_sample(array('sample_id' => $sample_id));
				$result = $this->sample_model->update_std_sample(array('sample_name' => $sample_name), array('ID' => $sample_id));
				$msg = _t('global.msg.update_fail');

				//delete un-assigned department
				$result += $this->sample_model->delete_std_department_sample($sample_id, $departments, FALSE);

				//assigned department
				$assigned_departments = array();
				foreach ($assigned_dep_sample as $row) {
					$assigned_departments[] = $row->department_id;
				}

				//Assign sample to new department
				$dep_sample = array();
				foreach ($departments as $dep) {
					if (!in_array($dep, $assigned_departments)) {
						$dep_sample[] = array(
							'department_id' => $dep,
							'sample_id' => $sample_id
						);
					}
				}

				if (count($dep_sample) > 0) {
					$result += $this->sample_model->assign_std_department_sample($dep_sample);
				}

                //Set Sample Description
                $this->sample_model->set_std_sample_description($sample_id, (array)$descriptions);

				if ($result > 0) {
					$status = TRUE;
					$msg = _t('global.msg.update_success');
				}
			}
		}

		$data['result']			= json_encode(array('status' => $status, 'msg' => $msg));
		$this->load->view('ajax_view/view_result', $data);
	}

	/**
	 * Delete Standard Sample
	 */
	public function delete_std_sample() {
		$sample_id	= $this->input->post("sample_id");
		
		$result		= 0;
		if (!empty($sample_id) && $sample_id > 0) {
			$result += $this->sample_model->delete_std_sample($sample_id);
			$result += $this->sample_model->delete_std_department_sample($sample_id);
		}
		
		$data['result']		= json_encode(array('status' => $result > 0 ? TRUE : FALSE));
		$this->load->view('ajax_view/view_result', $data);
	}

	/**
	 * Get Standard Assgiend Sample -> Department
	 */
	public function get_std_department_sample() {
		$_data					= new stdClass();
		$_data->sample_id		= $this->input->post('sample_id');
		$_data->department_id	= $this->input->post('department_id');

		$dep_sample			= $this->sample_model->get_std_department_sample($_data);
		$data['result']		= json_encode($dep_sample);
		$this->load->view('ajax_view/view_result', $data);
	}

	/**
	 * View Standard Sample
	 */
	public function view_all_std_sample() {
		$_data			= new stdClass();
		$_data->reqData	= $this->input->post();

		$result			= $this->sample_model->view_all_std_sample($_data);

		$data['result'] = json_encode($result,true);
		$this->load->view('ajax_view/view_result', $data);
	}

	/**
	 * View Standard Sample Comment
	 */
	public function view_std_sample_comment() {
		$_data			= new stdClass();
		$_data->reqData	= $this->input->post();
		
		$result			= $this->sample_model->view_std_sample_comment($_data);
		
				
		$data['result'] = json_encode($result);
		$this->load->view('ajax_view/view_result', $data);
	}

	/**
	* Clinical history autocomplete
	* @return json
	*/
	public function clinical_history()
	{
		$keyword = $this->input->post('filter_val');
		$this->db->select('diag_name');
		$this->db->where('diag_name !=', '');
		// $this->db->like('clinical_history', $keyword);
		$this->db->like('diag_name', $keyword, 'both');
		//$this->db->group_by('clinical_history');
		$this->db->from('camlis_diagnosis');
		$this->db->limit(10);
		$query = $this->db->get();
		$json_array = array();
		foreach ($query->result() as $row) {
			array_push($json_array, $row->diag_name);
		}
		echo json_encode($json_array);
	}

	/**
	 * Get clinical Symptom
	 */
	public function get_clinical_symptom() {
		$this->load->model("clinical_symptom_model");
		$_data				= new stdClass();
		$patient_sample_id	= $this->input->post('psample_id');
		$clinical_symptoms_dd = $this->clinical_symptom_model->get_ps_clinical_symptom($patient_sample_id); //added 04 Dec 2020
		$data['result']		= json_encode($clinical_symptoms_dd);
		$this->load->view('ajax_view/view_result', $data);
	}
}
