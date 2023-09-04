<?php
defined('BASEPATH') OR die('Access denied.');
class Performer extends MY_Controller {
	public function __construct() {
		parent::__construct();
		$this->load->model(array('performer_model'));
	}

	/**
	 * Get Lab performer
	 */
	public function get_lab_performer() {
		$performer		= $this->performer_model->get_lab_performer();
		$data['result']	= json_encode($performer);
		$this->load->view('ajax_view/view_result', $data);
	}

	/**
	 * Add new Lab Performer
	 */
	public function save() {
		$_data					= new stdClass();
		$_data->performer_name	= $this->input->post("performer_name");
		$_data->gender			= $this->input->post("gender");
		$_data->performer_name	= trim($_data->performer_name);
		$_data->gender			= in_array((int)$_data->gender, array(1, 2)) ? (int)$_data->gender : 1;
		$msg = _t('admin.msg.fill_required_data');
		$status = FALSE;

		if (!empty($_data->performer_name) && $_data->gender > 0) {
			$msg			= _t('global.msg.save_fail');
			$performer_id	= $this->performer_model->add_lab_performer($_data);
			if ($performer_id > 0) {
				$status = TRUE;
				$msg = _t('global.msg.save_success');
			}
		}
		
		$data['result']	= json_encode(array('status' => $status, 'msg' => $msg));
		$this->load->view('ajax_view/view_result', $data);
	}

	/**
	 * Update Lab Performer
	 */
	public function update() {
		$performer_name	= $this->input->post("performer_name");
		$gender			= $this->input->post("gender");
		$performer_id	= $this->input->post("performer_id");
		$performer_name	= trim($performer_name);
		$gender			= in_array((int)$gender, array(1, 2)) ? (int)$gender : 1;
		$msg 			= _t('admin.msg.fill_required_data');
		$status = FALSE;

		if (!empty($performer_name) && $gender > 0 && (int)$performer_id > 0) {
			$msg			= _t('global.msg.update_fail');
			$result = $this->performer_model->update_lab_performer($performer_id, array('performer_name' => $performer_name, 'gender' => $gender));
			if ($result > 0) {
				$status = TRUE;
				$msg = _t('global.msg.update_success');
			}
		}

		$data['result']	= json_encode(array('status' => $status, 'msg' => $msg));
		$this->load->view('ajax_view/view_result', $data);
	}

	/**
	 * Delete Lab perfomer
	 */
	public function delete() {
		$performer_id	= $this->input->post("performer_id");
		$msg			= _t('global.msg.delete_fail');
		$status			= FALSE;
		
		if (!empty($performer_id) && (int)$performer_id > 0) {
			$result = $this->performer_model->update_lab_performer($performer_id, array('status' => FALSE));
			if ($result > 0) {
				$msg = _t('global.msg.delete_success');
				$status = TRUE;
			}
		}

		$data['result']	= json_encode(array('status' => $status, 'msg' => $msg));
		$this->load->view('ajax_view/view_result', $data);
	}

	/**
	 * View Lab Perform (DataTable)
	 */
	public function view_lab_performer() {
		$_data			= new stdClass();
		$_data->reqData	= $this->input->post();
		
		$result			= $this->performer_model->view_lab_performer($_data);
		
		$data['result'] = json_encode($result);
		$this->load->view('ajax_view/view_result', $data);
	}
}