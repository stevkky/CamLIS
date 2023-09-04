<?php
defined('BASEPATH') OR die('Access denied.');
class Requester extends MY_Controller {
	public function __construct() {
		parent::__construct();
		$this->load->model(array('requester_model'));
	}

	/**
	 * Get Lab requester
	 */
	public function get_lab_requester() {
		$sample_source_id = $this->input->post('sample_source_id');
		$requesters = array();

		if ((int)$sample_source_id > 0) {
			$requesters = $this->requester_model->get_lab_requester(FALSE, $sample_source_id);
		}

		$data['result']	= json_encode(array('requesters' => $requesters));
		$this->load->view('ajax_view/view_result', $data);
	}

	/**
	 * Add new Lab requester
	 */
	public function save() {
		$_data					= new stdClass();
		$_data->requester_name	= $this->input->post("requester_name");
		$_data->gender			= $this->input->post("gender");
		$sample_source			= $this->input->post('sample_source');
		$_data->requester_name	= trim($_data->requester_name);
		$_data->gender			= in_array((int)$_data->gender, array(1, 2)) ? (int)$_data->gender : 1;
		$msg 					= _t('admin.msg.fill_required_data');
		$status 				= FALSE;

		if (!empty($_data->requester_name) && $_data->gender > 0) {
			if ($this->requester_model->is_exist($_data->requester_name)) {
				$this->app_language->load('manage');
				$msg = _t('manage.msg.requester_exist');
			} else {
				$msg = _t('global.msg.save_fail');
				$requester_id = $this->requester_model->add_lab_requester($_data);
				if ($requester_id > 0) {
					$status = TRUE;
					$msg = _t('global.msg.save_success');

					//Assigned Sample Source
					if (is_array($sample_source) && count($sample_source) > 0) {
						$requester_sample_source = array();
						foreach ($sample_source as $item) {
							if ((int)$item > 0) {
								$requester_sample_source[] = array(
									'requester_id' => $requester_id,
									'sample_source_id' => $item
								);
							}
						}
						if (count($requester_sample_source) > 0) $this->requester_model->assign_requester_sample_source($requester_sample_source);
					}
				}
			}
		}
		
		$data['result']	= json_encode(array('status' => $status, 'msg' => $msg));
		$this->load->view('ajax_view/view_result', $data);
	}

	/**
	 * Update Lab requester
	 */
	public function update() {
		$requester_name	= $this->input->post("requester_name");
		$gender			= $this->input->post("gender");
		$requester_id	= $this->input->post("requester_id");
		$sample_source	= $this->input->post('sample_source');
		$requester_name	= trim($requester_name);
		$gender			= in_array((int)$gender, array(1, 2)) ? (int)$gender : 1;
		$msg 			= _t('admin.msg.fill_required_data');
		$status = FALSE;

		if (!empty($requester_name) && $gender > 0 && (int)$requester_id > 0) {
			if ($this->requester_model->is_exist($requester_name, $requester_id)) {
				$this->app_language->load('manage');
				$msg = _t('manage.msg.requester_exist');
			} else {
				$msg = _t('global.msg.update_fail');
				$result = $this->requester_model->update_lab_requester($requester_id, array('requester_name' => $requester_name, 'gender' => $gender));
				if ($result > 0) {
					$status = TRUE;
					$msg = _t('global.msg.update_success');

					//delete Sample Source of Requester that are not in list
					$this->requester_model->delete_requester_sample_source($requester_id, $sample_source, FALSE);

					//get current assigned sample source
					$assigned_source = array();
					$_sources = $this->requester_model->get_requester_sample_source(array('requester.ID' => $requester_id));
					if (count($_sources) > 0) {
						foreach ($_sources as $row) {
							$assigned_source[] = (int)$row->sample_source_id;
						}
					}

					//Assign New Sample Source
					if (is_array($sample_source) && count($sample_source) > 0) {
						$new_source = array();
						foreach ($sample_source as $item) {
							if ((int)$item > 0 && !in_array((int)$item, $assigned_source)) {
								$new_source[] = array(
									'requester_id' => $requester_id,
									'sample_source_id' => $item
								);
							}
						}
						if (count($new_source) > 0) $this->requester_model->assign_requester_sample_source($new_source);
					}
				}
			}
		}

		$data['result']	= json_encode(array('status' => $status, 'msg' => $msg));
		$this->load->view('ajax_view/view_result', $data);
	}

	/**
	 * Delete Lab perfomer
	 */
	public function delete() {
		$requester_id	= $this->input->post("requester_id");
		$msg			= _t('global.msg.delete_fail');
		$status			= FALSE;
		
		if (!empty($requester_id) && (int)$requester_id > 0) {
			$result = $this->requester_model->update_lab_requester($requester_id, array('status' => FALSE));
			if ($result > 0) {
				$msg = _t('global.msg.delete_success');
				$status = TRUE;

				//delete sample source
				$this->requester_model->delete_requester_sample_source($requester_id);
			}
		}

		$data['result']	= json_encode(array('status' => $status, 'msg' => $msg));
		$this->load->view('ajax_view/view_result', $data);
	}

	/**
	 * View Lab Perform (DataTable)
	 */
	public function view_lab_requester() {
		$_data			= new stdClass();
		$_data->reqData	= $this->input->post();
		
		$result			= $this->requester_model->view_lab_requester($_data);
		
		$data['result'] = json_encode($result);
		$this->load->view('ajax_view/view_result', $data);
	}
}