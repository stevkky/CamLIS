<?php
defined('BASEPATH') OR die('Access denied!');

class Sample_source extends MY_Controller {
	public function __construct() {
		parent::__construct();
		$this->load->model('sample_source_model');
	}

	/**
	 * Add new Standard Sample Source
	 */
	public function add_lab_sample_source() {
		$this->app_language->load('manage');
		$source_name = $this->input->post('source_name');
		$source_name = trim($source_name);
		$msg = _t('global.msg.fill_required_data');
		$status = FALSE;

		if (!empty($source_name)) {
			//Check if sample source is exists
			if (count($this->sample_source_model->get_lab_sample_source(array("source_name" => $source_name))) > 0) {
				$msg = _t('manage.msg.sample_source_exist');
			}
			else {
				if ($this->sample_source_model->add_lab_sample_source(array('source_name' => $source_name)) > 0) {
					$status = TRUE;
					$msg = _t('global.msg.save_success');
				} else {
					$msg = _t('global.msg.save_fail');
				}
			}
		}

		$this->data['result'] = json_encode(array('status' => $status, 'msg' => $msg));
		$this->load->view('ajax_view/view_result', $this->data);
	}

	/**
	 * Update Standard Sample Source
	 */
	public function update_lab_sample_source() {
		$this->app_language->load('admin');

		$source_id = $this->input->post('source_id');
		$source_name = $this->input->post('source_name');
		$source_name = trim($source_name);
		$msg = _t('global.msg.fill_required_data');
		$status = FALSE;

		if (!empty($source_name) && $source_id > 0) {
			//Check if sample source is exists
			if (count($this->sample_source_model->get_lab_sample_source(array("source_name" => $source_name, 'ID <>' => $source_id))) > 0) {
				$msg = _t('admin.msg.sample_source_exist');
			}
			else {
				if ($this->sample_source_model->update_lab_sample_source($source_id, array('source_name' => $source_name)) > 0) {
					$status = TRUE;
					$msg = _t('global.msg.update_success');
				} else {
					$msg = _t('global.msg.update_fail');
				}
			}
		} else {
			$msg = _t('global.msg.update_fail');
		}

		$this->data['result'] = json_encode(array('status' => $status, 'msg' => $msg));
		$this->load->view('ajax_view/view_result', $this->data);
	}

	/**
	 * Delete Standard Sample Source
	 */
	public function delete_lab_sample_source() {
		$source_id = $this->input->post('source_id');
		$status = FALSE;
		$msg = _t('global.msg.delete_fail');

		if ($source_id > 0) {
			if ($this->sample_source_model->update_lab_sample_source($source_id, array('status' => FALSE)) > 0) {
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
	 * View Standard Sample Source (DataTable)
	 */
	public function view_lab_sample_source() {
		$result			= $this->sample_source_model->view_lab_sample_source($this->input->post());

		$data['result'] = json_encode($result);
		$this->load->view('ajax_view/view_result', $data);
	}
}