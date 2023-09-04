<?php
defined('BASEPATH') OR die('Access denied!');
class Organism extends MY_Controller {
	public function __construct() {
		parent::__construct();
		$this->load->model(array('organism_model', 'result_model', 'antibiotic_model'));
	}
	
	/**
	 * Get Standard Orgnism
	 */
	public function get_std_organism() {
		$result		= $this->organism_model->get_std_organism();
		
		$data['result'] = json_encode($result);
		$this->load->view('ajax_view/view_result', $data);
	}
	
	/**
	 * Get Organism for SampleTest
	 */
	public function get_sample_test_organism() {
		$sample_test_id = $this->input->post('sample_test_id');
		$organism       = array();
		if ((int)$sample_test_id > 0) $organism	= $this->organism_model->get_sample_test_organism($sample_test_id);

		$data['result'] = json_encode($organism);
		$this->load->view('ajax_view/view_result', $data);
	}

	/**
	 * Add new Standard Organism
	 */
	public function add_std_organism() {
		$this->app_language->load('admin');
		$organism_name = $this->input->post('organism_name');
        $organism_value = $this->input->post('organism_value');
        $organism_order = $this->input->post('order');
        $organism_name = trim($organism_name);
		$msg = _t('global.msg.fill_required_data');
		$status = FALSE;

		if (!empty($organism_name)) {
			//Check if organism is exists
			if (count($this->organism_model->get_std_organism(array("organism_name" => $organism_name, "organism_value" => $organism_value))) > 0) {
				$msg = _t('admin.msg.organism_exist');
			} else {
				if ($this->organism_model->add_std_organism(array('organism_name' => $organism_name, "organism_value" => $organism_value, "order" => $organism_order)) > 0) {
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
	 * Update Standard Organism
	 */
	public function update_std_organism() {
		$this->app_language->load('admin');
		$id 				= $this->input->post('ID');
		$organism_name 		= $this->input->post('organism_name');
		$organism_value 	= $this->input->post('organism_value');
        $organism_order 	= $this->input->post('order');
		$organism_name 		= trim($organism_name);
		$msg 				= _t('global.msg.fill_required_data');
		$status = FALSE;

		if (!empty($organism_name) && $id > 0) {
			//Check if organism is exists
			if (count($this->organism_model->get_std_organism(array("organism_name" => $organism_name, "organism_value" => $organism_value, '"ID" <>' => $id))) > 0) {
				$msg = _t('admin.msg.organism_exist');
			}
			else {
				if ($this->organism_model->update_std_organism($id, array("organism_name" => $organism_name, "organism_value" => $organism_value, "order" => $organism_order)) > 0) {
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
	 * Delete Standard Organism
	 */
	public function delete_std_organism() {
		$id 		= $this->input->post('ID');
		$status 	= FALSE;
		$msg 		= _t('global.msg.delete_fail');

		if ($id > 0) {
			if ($this->organism_model->update_std_organism($id, array('status' => FALSE)) > 0) {
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
	 * Copy Sample Test organism
	 */
	public function copy_sample_test_organism() {
		$this->app_language->load('admin');
		$this->load->model('antibiotic_model');
		$src_sample_test_id		= $this->input->post('src_sample_test_id');
		$target_sample_test_id	= $this->input->post('target_sample_test_id');
		$type 					= $this->input->post('type');
		$status 				= 0;
		$msg					= _t('global.msg.fill_required_data');

		if ((int)$type == 1 && (int)$src_sample_test_id > 0 && (int)$target_sample_test_id > 0 && $src_sample_test_id != $target_sample_test_id ) {
			$target_result = $this->antibiotic_model->get_std_sample_test_organism_antibiotic(array('test_org.sample_test_id' => $target_sample_test_id));
			$_result = $this->antibiotic_model->get_std_sample_test_organism_antibiotic(array('test_org.sample_test_id' => $src_sample_test_id));
			$target_organism = array();
			$target_antibiotic = array();
			if (count($target_result) > 0) {
				foreach ($target_result as $row) {
					if (!isset($target_antibiotic[$row->organism_id])) {
						$target_organism[$row->organism_id] = $row->test_organism_id;
						$target_antibiotic[$row->organism_id] = array();
					}
					$target_antibiotic[$row->organism_id][] = $row->antibiotic_id;
				}
			}
			if (count($_result) > 0) {
				$msg = _t('admin.msg.copy_fail');
				$org_anti = array();
				foreach ($_result as $row) {
					if (!isset($org_anti[$row->organism_id])) {
						$org_anti[$row->organism_id] = array(
							'organism_id' => $row->organism_id,
							'antibiotic'  => array()
						);
					}
					if (isset($target_antibiotic[$row->organism_id]) && in_array($row->antibiotic_id, $target_antibiotic[$row->organism_id])) continue;
					$org_anti[$row->organism_id]['antibiotic'][] = $row->antibiotic_id;
				}

				if (count($org_anti) > 0) {
					$count = 0;
					foreach ($org_anti as $row) {
						if (isset($row['organism_id']) && $row['organism_id'] > 0) {
							$test_organism_id = isset($target_organism[$row['organism_id']]) ? $target_organism[$row['organism_id']] : $this->organism_model->assign_std_sample_test_organism(array('sample_test_id' => $target_sample_test_id, 'organism_id' => $row['organism_id']));
							$count += $test_organism_id;
							//Assign Antibiotic to organism
							if ($test_organism_id > 0 && isset($row['antibiotic']) && count($row['antibiotic']) > 0) {
								$antibiotic_data = array();
								foreach ($row['antibiotic'] as $_anti) {
									if ($_anti > 0) {
										$antibiotic_data[] = array(
											'test_organism_id' => $test_organism_id,
											'antibiotic_id' => $_anti
										);
									}
								}

								if (count($antibiotic_data) > 0) {
									$count += $this->antibiotic_model->assign_std_organism_antibiotic($antibiotic_data);
								}
							}
						}
					}

					if ($count > 0) {
						$status = TRUE;
						$msg = _t('admin.msg.copy_success');
					}
				}
			}
		} else if ($src_sample_test_id == $target_sample_test_id) {
			$msg = _t('admin.msg.diff_copy');
		}

		$data['result'] = json_encode(array('status' => $status, 'msg' => $msg));
		$this->load->view('ajax_view/view_result', $data);
	}

	/**
	 * View Standard Department
	 */
	public function view_std_organism() {
		$result			= $this->organism_model->view_std_organism($this->input->post());

		$data['result'] = json_encode($result);
		$this->load->view('ajax_view/view_result', $data);
	}
}