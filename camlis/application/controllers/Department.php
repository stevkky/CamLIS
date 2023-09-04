<?php
defined('BASEPATH') OR die('Access denied.');
class Department extends MY_Controller {
	public function __construct() {
		parent::__construct();
		$this->load->model('department_model', 'dModel');
	}

	/**
	 * Add new Standard Department
	 */
	public function add_std_department() {
		$this->app_language->load('admin');
		$department_name = $this->input->post('department_name');
		$department_name = trim($department_name);
		$msg = _t('global.msg.fill_required_data');
		$status = FALSE;

		if (!empty($department_name)) {
			//Check if department is exists
			if (count($this->dModel->get_std_department(array("department_name" => $department_name))) > 0) {
				$msg = _t('admin.msg.department_exist');
			}
			else {
				if ($this->dModel->add_std_department(array('department_name' => $department_name)) > 0) {
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
	 * Update Standard Department
	 */
	public function update_std_department() {
		$this->app_language->load('admin');
		$id = $this->input->post('ID');
		$department_name = $this->input->post('department_name');
		$department_name = trim($department_name);
		$msg = _t('global.msg.fill_required_data');
		$status = FALSE;

		if (!empty($department_name) && $id > 0) {
			//Check if department is exists
			if (count($this->dModel->get_std_department(array("department_name" => $department_name, '"ID" <>' => $id))) > 0) {
				$msg = _t('admin.msg.department_exist');
			}
			else {
				if ($this->dModel->update_std_department($id, array('department_name' => $department_name)) > 0) {
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
	 * Delete Standard Department
	 */
	public function delete_std_department() {
		$id = $this->input->post('ID');
		$status = FALSE;
		$msg = _t('global.msg.delete_fail');

		if ($id > 0) {
			if ($this->dModel->update_std_department($id, array('status' => FALSE)) > 0) {
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
	 * Get Standard Department List
	 */
	public function get_std_department() {
		$departments = $this->dModel->get_std_department();
		
		$this->data['result'] = json_encode($departments);
		$this->load->view('ajax_view/view_result', $this->data);
	}

	/**
	 * View Standard Department (DataTable)
	 */
	public function view_std_department() {
		$result			= $this->dModel->view_std_department($this->input->post());

		$data['result'] = json_encode($result);
		$this->load->view('ajax_view/view_result', $data);
	}
}