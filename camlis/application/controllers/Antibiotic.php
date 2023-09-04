<?php
defined('BASEPATH') OR die('Access denied!');
class Antibiotic extends MY_Controller {
	public function __construct() {
		parent::__construct();
		$this->load->model(array('antibiotic_model'));
	}

	/**
	 * Add new Standard Antibiotic
	 */
	public function add_std_antibiotic() {
		$this->app_language->load('admin');
        $antibiotic_name = $this->input->post('antibiotic_name');
        $antibiotic_order = $this->input->post('order');
        //$gram_type = $this->input->post('gram_type');
		$antibiotic_name = trim($antibiotic_name);
		$msg = _t('global.msg.fill_required_data');
		$status = FALSE;

		if (!empty($antibiotic_name)) {
			//Check if antibiotic is exists
			if (count($this->antibiotic_model->get_std_antibiotic(array("antibiotic_name" => $antibiotic_name))) > 0) {
				$msg = _t('admin.msg.antibiotic_exist');
			} else {
				if ($this->antibiotic_model->add_std_antibiotic(array('antibiotic_name' => $antibiotic_name, 'order' => $antibiotic_order)) > 0) {
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
	 * Update Standard Antibiotic
	 */
	public function update_std_antibiotic() {
		$this->app_language->load('admin');
		$id = $this->input->post('ID');
		$antibiotic_name = $this->input->post('antibiotic_name');
		$antibiotic_order = $this->input->post('order');
        //$gram_type = $this->input->post('gram_type');
		$antibiotic_name = trim($antibiotic_name);
		$msg = _t('global.msg.fill_required_data');
		$status = FALSE;

		if (!empty($antibiotic_name) && $id > 0) {
			//Check if antibiotic is exists
			if (count($this->antibiotic_model->get_std_antibiotic(array("antibiotic_name" => $antibiotic_name, '"ID" <>' => $id))) > 0) {
				$msg = _t('admin.msg.antibiotic_exist');
			}
			else {
				if ($this->antibiotic_model->update_std_antibiotic($id, array("antibiotic_name" => $antibiotic_name, 'order' => $antibiotic_order)) > 0) {
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
	 * Delete Standard Antibiotic
	 */
	public function delete_std_antibiotic() {
		$id = $this->input->post('ID');
		$status = FALSE;
		$msg = _t('global.msg.delete_fail');

		if ($id > 0) {
			if ($this->antibiotic_model->update_std_antibiotic($id, array('status' => FALSE)) > 0) {
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
	 * Get Standard Antibiotic
	 */
	public function get_std_antibiotic() {
		$result			= $this->antibiotic_model->get_std_antibiotic();
		
		$data['result']	= json_encode($result);
		$this->load->view('ajax_view/view_result', $data);
	}

	public function copy_sample_test_antibiotic() {
		$this->app_language->load('admin');
		$src_organism		= $this->input->post('src_organism');
		$target_organism	= $this->input->post('target_organism');
		$type 				= $this->input->post('type');
		$status 			= FALSE;
		$msg				= _t('global.msg.fill_required_data');

		if ((int)$type == 2 && (int)$src_organism > 0 && (int)$target_organism > 0 && $src_organism != $target_organism ) {
			$target_antibiotic = $this->antibiotic_model->get_std_sample_test_organism_antibiotic(array('test_org.ID' => $target_organism));
			$src_antibiotic = $this->antibiotic_model->get_std_sample_test_organism_antibiotic(array('test_org.ID' => $src_organism));

			$assigned_antibiotic = array();
			if (count($target_antibiotic) > 0) {
				foreach ($target_antibiotic as $row) {
					$assigned_antibiotic[] = $row->antibiotic_id;
				}
			}
			if (count($src_antibiotic) > 0) {
				$msg = _t('admin.msg.copy_fail');
				$antibiotic_data = array();
				foreach ($src_antibiotic as $row) {
					if (!in_array($row->antibiotic_id, $assigned_antibiotic)) {
						$antibiotic_data[] = array(
							'test_organism_id' => $target_organism,
							'antibiotic_id' => $row->antibiotic_id
						);
					}
				}
				if (count($antibiotic_data) > 0) {
					$this->antibiotic_model->assign_std_organism_antibiotic($antibiotic_data);
				}

				$status = TRUE;
				$msg = _t('admin.msg.copy_success');
			}
		} else if ($src_organism == $target_organism) {
			$msg = _t('admin.msg.diff_copy');
		}

		$data['result'] = json_encode(array('status' => $status, 'msg' => $msg));
		$this->load->view('ajax_view/view_result', $data);
	}

	/**
	 * Get Antibiotic of Sample Test Organism
	 */
	public function get_std_sample_test_organism_antibiotic() {
		$test_organism_id = $this->input->post('test_organism_id');
		$result = array();

		if ((int)$test_organism_id > 0) {
			$result = $this->antibiotic_model->get_std_sample_test_organism_antibiotic(array('test_org."ID"' => $test_organism_id));
		}
		
		$data['result'] = json_encode($result);
		$this->load->view('ajax_view/view_result', $data);
	}

	/**
	 * View Standard Department
	 */
	public function view_std_antibiotic() {
		$result			= $this->antibiotic_model->view_std_antibiotic($this->input->post());

		$data['result'] = json_encode($result);
		$this->load->view('ajax_view/view_result', $data);
	}
}