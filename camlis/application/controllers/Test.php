<?php
defined('BASEPATH') OR die('Access denied.');
class Test extends MY_Controller {
	public function __construct() {
		parent::__construct();
		$this->load->model(array('test_model', 'result_model'));
		
		/* Load Language */
		$this->app_language->load('test');
	}

	/**
	 * View Sample Test
	 */
	public function view_all_std_sample_test() {
		$_data			= new stdClass();
		$_data->reqData	= $this->input->post();
		
		$result			= $this->test_model->view_all_std_sample_test($_data);
		$data['result']	= json_encode($result);
		$this->load->view('ajax_view/view_result', $data);
	}

	/**
	 * Get Group that Sample Test is listed in
	 */
	public function get_std_sample_test() {
		$dep_sample_id	= $this->input->post('dep_sample_id');
		$is_heading		= $this->input->post('is_heading');
		$field_type		= $this->input->post('field_type');
		$group_by		= $this->input->post('group_by');

		$sample_tests	= array();
		$_data			= array();
		//if ((int)$dep_sample_id > 0) $_data['sample_test.department_sample_id'] = (int)$dep_sample_id;
		//if (is_numeric($is_heading)) $_data['sample_test.is_heading'] = (int)$is_heading;
		//$_data['sample_test.is_heading'] = ($is_heading == 1) ? true : false;
		//$_data['sample_test.is_heading'] = ($is_heading == TRUE ) ? true : false;
		
		if ((int)$dep_sample_id > 0) $_data['sample_test.department_sample_id'] = (int)$dep_sample_id;
		if (isset($is_heading)) $_data['sample_test.is_heading'] = $is_heading;


		if ($group_by == 'sample') {
			$this->load->helper('sample_test');
			$sample_tests = sample_test_hierarchy($this->test_model->get_std_sample_test($_data, TRUE), TRUE);
			$sample_tests = collect($sample_tests)->map(function($item) { $item->samples = array_values($item->samples); return $item; });
		} else {
			$sample_tests = $this->test_model->get_std_sample_test($_data);
		}

		$data['result']	= json_encode($sample_tests ? $sample_tests : array());
		$this->load->view('ajax_view/view_result', $data);
	}

	/**
	 * Get Standard Test Details (Department, Sample, Test Info, Organism and Antibiotic)
	 */
	public function get_std_sample_test_details() {
		$this->load->model('antibiotic_model');
		$sample_test_id = $this->input->post('sample_test_id');

		$_data = NULL;
		if ((int)$sample_test_id > 0) {
			$sample_test_info = $this->test_model->get_std_sample_test(array('sample_test.ID' => $sample_test_id));
			if (count($sample_test_info) > 0) {
				$_data = $sample_test_info[0];
				$org_antibiotic_data = array();
				$ref_ranges_data = array();

				//Single and Multiple Result Test
				if (in_array($_data->field_type, array(1, 2))) {
					$org_antibiotic = $this->antibiotic_model->get_std_sample_test_organism_antibiotic(array('test_org.sample_test_id' => $sample_test_id));

					if (count($org_antibiotic) > 0) {
						foreach ($org_antibiotic as $row) {
							if (!isset($org_antibiotic_data[$row->organism_id])) {
								$org_antibiotic_data[$row->organism_id] = array(
									'organism_id' => (int)$row->organism_id,
									'organism_name' => trim($row->organism_name),
									'antibiotic' => array()
								);
							}
							$org_antibiotic_data[$row->organism_id]['antibiotic'][] = (int)$row->antibiotic_id;
						}
					}
				}
				//3:Numeric, 5:calculate Result Test
				else if ($_data->field_type == 3 || $_data->field_type == 5) {
					$ref_ranges = $this->test_model->get_std_sample_test_ref_range($sample_test_id);
					if (count($ref_ranges) > 0) {
						$ref_ranges_data = $ref_ranges;
					}
				}

				$_data->organism_antibiotic = $org_antibiotic_data;
				$_data->ref_ranges = $ref_ranges_data;
			}
		}

		$data['result']	= json_encode($_data);
		$this->load->view('ajax_view/view_result', $data);
	}

	/**
	 * Add Standard Sample Test
	 */
	public function add_std_sample_test() {
		$this->load->model('organism_model');
		$this->load->model('antibiotic_model');
		$this->app_language->load('admin');

		$dep_sample_id			= $this->input->post('dep_sample_id');
		$test_id				= $this->input->post('test_id');
		$is_heading				= $this->input->post('is_heading');
		$is_heading = $is_heading === 'true'? true: false; // convert false|true string to boolean
		$default_select			= $this->input->post('default_selected');
		$group_by				= $this->input->post('group_by');
		$unit_sign				= $this->input->post('unit_sign');
		$field_type				= $this->input->post('field_type');
		$group_result			= $this->input->post('group_result');
		$test_order				= $this->input->post('test_order');
		$ref_ranges				= $this->input->post('ref_ranges');
		$organism_antibiotic	= $this->input->post('organism_antibiotic');

		$status		= 0;
		$msg		= _t('global.msg.fill_required_data');

		if ((int)$test_id > 0 && (int)$dep_sample_id > 0 && (($is_heading == false && (int)$field_type > 0) || $is_heading == true) ) {
			$sample_test = $this->test_model->get_std_sample_test(array('sample_test.test_id' => (int)$test_id, 'sample_test.department_sample_id' => (int)$dep_sample_id));
			if (count($sample_test) > 0) {
				$msg = _t('admin.msg.test_exist');
			}
			else
			{
				$sample_test_id = $this->test_model->add_std_sample_test(array(
					'department_sample_id'	=> $dep_sample_id,
					'testPID'				=> $group_by > 0 ? $group_by : 0,
					'test_id'				=> $test_id,
					'is_heading'			=> $is_heading,
					'unit_sign'				=> $unit_sign,
					'field_type'			=> $is_heading == false ? $field_type : 0,
					'group_result'			=> empty(trim($group_result)) ? NULL : trim($group_result),
					'default_select'		=> $default_select > 0 ? $default_select : 0,
					'order'					=> (int)$test_order
				));

				if ($sample_test_id > 0) {
					$status = TRUE;
					$msg = _t('global.msg.save_success');

					//Ref. Ranges for Numeric Result Test
					if ($is_heading == false && (int)($field_type == 3 || $field_type == 5 ) && count($ref_ranges) > 0) {
						$ref_ranges_data = array();
						foreach ($ref_ranges as $row) {
							if (isset($row['patient_type']) && $row['patient_type'] > 0) {
								$row['sample_test_id'] = $sample_test_id;
								$row['range_sign'] = isset($row['range_sign']) && !empty($row['range_sign']) ? $row['range_sign'] : NULL;
								$ref_ranges_data[] = $row;
							}
						}

						//Add Ref. Ranges
						if (count($ref_ranges_data) > 0) {
							$this->test_model->add_std_sample_test_ref_range($ref_ranges_data);
						}
					}
					//Add Possible Result (Organism/Antibiotic)
					else if ($is_heading == false && in_array($field_type, array(1, 2)) && count($organism_antibiotic) > 0) {
						foreach ($organism_antibiotic as $row) {
							if (isset($row['organism_id']) && $row['organism_id'] > 0) {
								$test_organism_id = $this->organism_model->assign_std_sample_test_organism(array('sample_test_id' => $sample_test_id, 'organism_id' => $row['organism_id']));

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
										$this->antibiotic_model->assign_std_organism_antibiotic($antibiotic_data);
									}
								}
							}
						}
					}
				}
			}
		}
		
		$data['result'] = json_encode(array('status' => $status, 'msg' => $msg));
		$this->load->view('ajax_view/view_result', $data);
	}

	/**
	 * Update Standard Test
	 */
	public function update_std_sample_test() {
		$this->load->model('organism_model');
		$this->load->model('antibiotic_model');
		$this->app_language->load('admin');

		$sample_test_id			= $this->input->post('sample_test_id');
		$dep_sample_id			= $this->input->post('dep_sample_id');
		$test_id				= $this->input->post('test_id');
		$is_heading				= $this->input->post('is_heading');
		$is_heading = $is_heading === 'true'? true: false; // convert false|true string to boolean
		$default_select			= $this->input->post('default_selected');
		$group_by				= $this->input->post('group_by');
		$unit_sign				= $this->input->post('unit_sign');
		$field_type				= $this->input->post('field_type');
		$group_result			= $this->input->post('group_result');
		$test_order				= $this->input->post('test_order');
		$ref_ranges				= $this->input->post('ref_ranges');
		$organism_antibiotic	= $this->input->post('organism_antibiotic');

		$status		= 0;
		$msg		= _t('global.msg.fill_required_data');

		if ((int)$sample_test_id > 0 && !(empty($test_id)) && (int)$dep_sample_id > 0 && (($is_heading == false && (int)$field_type > 0) || $is_heading == true) ) {
		    $sample_test = $this->test_model->get_std_sample_test(array('sample_test.test_id' => (int)$test_id, 'sample_test.department_sample_id' => (int)$dep_sample_id, 'sample_test."ID" !=' => (int)$sample_test_id ));
			if (count($sample_test) > 0) {
				$msg = _t('admin.msg.test_exist');
			}
			else
			{
				$result = $this->test_model->update_std_sample_test(array(
					'department_sample_id'	=> $dep_sample_id,
					'testPID'				=> $group_by > 0 ? $group_by : 0,
					'test_id'				=> trim($test_id),
					'is_heading'			=> $is_heading,
					'unit_sign'				=> $unit_sign,
					'field_type'			=> $is_heading == false ? $field_type : 0,
					'group_result'			=> empty(trim($group_result)) ? NULL : trim($group_result),
					'default_select'		=> $default_select > 0 ? $default_select : 0,
					'order'					=> (int)$test_order
				), $sample_test_id);

				if ($result > 0) {
					$status = TRUE;
					$msg = _t('global.msg.update_success');

					//Update Ref. Ranges for 3:Numeric, 5: Calculate Result Test
					if ($is_heading == false && ((int)$field_type == 3 || (int)$field_type == 5)) {
						//Assigned Ref.Ranged in this test
						$assigned_ref_ranges = $this->test_model->get_std_sample_test_ref_range($sample_test_id);
						$assigned_patient_types = array();
						foreach ($assigned_ref_ranges as $row) {
							$assigned_patient_types[] = $row->patient_type;
						}

						//delete Ref. Ranges (base on patient type) that are not in list
						$patient_types = array();
						if ($ref_ranges && count($ref_ranges) > 0) {
							foreach ($ref_ranges as $row) {
								if (isset($row['patient_type']) && $row['patient_type'] > 0) {
									$patient_types[] = $row['patient_type'];
								}
							}
							$this->test_model->delete_std_sample_test_ref_range($sample_test_id, $patient_types, FALSE);

							//Add New/Update Ref. Ranges of Standard Test
							$update_ref_ranges_data = array();
							$new_ref_ranges_data = array();
							foreach ($ref_ranges as $row) {
								if (isset($row['patient_type']) && $row['patient_type'] > 0) {
									$row['sample_test_id'] = $sample_test_id;
									$row['range_sign'] = isset($row['range_sign']) && !empty($row['range_sign']) ? $row['range_sign'] : NULL;

									if (!in_array($row['patient_type'], $assigned_patient_types)) {
										$new_ref_ranges_data[] = $row;
									} else {
										$update_ref_ranges_data[] = $row;
									}
								}
							}

							if (count($new_ref_ranges_data) > 0) $this->test_model->add_std_sample_test_ref_range($new_ref_ranges_data);
							if (count($update_ref_ranges_data) > 0) $this->test_model->update_std_sample_test_ref_range($update_ref_ranges_data, $test_id);
						}
					}
					else if ($is_heading == false && in_array($field_type, array(1, 2))) {
						$assigned_organism_antibiotic = $this->antibiotic_model->get_std_sample_test_organism_antibiotic(array('test_org.sample_test_id' => $sample_test_id));
						$assigned_organism = array();
						$assigned_org_antibiotic_data = array(); //Organism ID as Key
						if (count($assigned_organism_antibiotic) > 0) {
							foreach ($assigned_organism_antibiotic as $row) {
								if (!isset($assigned_org_antibiotic_data[$row->organism_id])) {
									$assigned_organism[] = $row->organism_id;
									$assigned_org_antibiotic_data[$row->organism_id] = array(
										'test_organism_id' => (int)$row->test_organism_id,
										'antibiotic' => array()
									);
								}
								$assigned_org_antibiotic_data[$row->organism_id]['antibiotic'][] = (int)$row->antibiotic_id;
							}
						}

						//Delete Organism/Antibiotic that are not in the new list
						$organism_list = array();
						if (count($organism_antibiotic) > 0) {
							foreach ($organism_antibiotic as $row) {
								if (isset($row['organism_id']) && $row['organism_id'] > 0) {
									$organism_list[] = $row['organism_id'];
								}
							}
						}
						if (count($organism_list) > 0) {
							$this->antibiotic_model->delete_std_sample_test_organism_antibiotic($sample_test_id, $organism_list, FALSE);
							$this->organism_model->delete_std_sample_test_organism($sample_test_id, $organism_list, FALSE);
						}

						//Add New/Update Organism and Antibiotic
						if (is_array($organism_antibiotic) && count($organism_antibiotic) > 0) {
							foreach ($organism_antibiotic as $row) {
								//New
								if (isset($row['organism_id']) && $row['organism_id'] > 0 && !in_array($row['organism_id'], $assigned_organism)) {
									$test_organism_id = $this->organism_model->assign_std_sample_test_organism(array('sample_test_id' => $sample_test_id, 'organism_id' => $row['organism_id']));

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
											$this->antibiotic_model->assign_std_organism_antibiotic($antibiotic_data);
										}
									}
								}
								//Update Antibiotic Only (in case this organism is already assigned)
								else if (isset($row['organism_id']) && $row['organism_id'] > 0 && in_array($row['organism_id'], $assigned_organism)) {
									$test_organism_id = $assigned_org_antibiotic_data[$row['organism_id']]['test_organism_id'];
									//delete antibiotic that are not in list
									if (isset($row['antibiotic']) && count($row['antibiotic']) > 0) {
										$this->antibiotic_model->delete_std_sample_test_organism_antibiotic($test_organism_id, $row['antibiotic'], FALSE, 'antibiotic_list');
									}

									//new antibiotic only
									$new_antibiotic = array();
									if (isset($row['antibiotic']) && count($row['antibiotic']) > 0) {
										$anti_list = $assigned_org_antibiotic_data[$row['organism_id']]['antibiotic'];
										foreach ($row['antibiotic'] as $_anti) {
											if ($_anti > 0 && !in_array($_anti, $anti_list)) {
												$new_antibiotic[] = array(
													'test_organism_id' => $test_organism_id,
													'antibiotic_id' => $_anti
												);
											}
										}
									}
									if (count($new_antibiotic) > 0) {
										$this->antibiotic_model->assign_std_organism_antibiotic($new_antibiotic);
									}
								}
							}
						}
					}
				}
			}
		}

		$data['result'] = json_encode(array('status' => $status, 'msg' => $msg));
		$this->load->view('ajax_view/view_result', $data);
	}

	/**
	 * Delete Sample Test
	 */
	public function delete_std_sample_test() {
		$this->load->model('organism_model');
		$this->load->model('antibiotic_model');

		$sample_test_id	= $this->input->post('sample_test_id');
		$status		= 0;
		$msg		= _t('global.msg.delete_fail');

		if ((int)$sample_test_id > 0) {
			$result = $this->test_model->update_std_sample_test(array('status' => FALSE), $sample_test_id);
			if ($result > 0) {
				$status = TRUE;
				$msg = _t('global.msg.delete_success');

				//delete Ref.Range
				//$this->test_model->delete_std_test_ref_range($sample_test_id);
				$this->test_model->delete_std_sample_test_ref_range($sample_test_id); // added 18 Dec 2020

				//delete Organism/Antibiotic
				//$this->antibiotic_model->delete_std_test_organism_antibiotic($sample_test_id);
				$this->antibiotic_model->delete_std_sample_test_organism_antibiotic($sample_test_id); // added 18 Dec 2020
				
				//$this->organism_model->delete_std_test_organism($sample_test_id);
				$this->organism_model->delete_std_sample_test_organism($sample_test_id);
			}
		}

		$data['result']	= json_encode(array('status'  => $status, 'msg' => $msg));
		$this->load->view('ajax_view/view_result', $data);
	}

	/**
	 * Add New Standard Test name
	 */
	public function add_std_test_name() {
		$this->app_language->load('admin');
		$test_name = $this->input->post('test_name');
		$msg = _t('global.msg.fill_required_data');
		$status = FALSE;
		$response_data = array();

		if (!empty(trim($test_name))) {
			//check if test name is exist
			if (count($this->test_model->get_std_test(array('test_name' => trim($test_name)))) > 0) {
				$msg = _t('admin.msg.test_exist');
			} else {
				$msg = _t('global.msg.save_fail');
				$test_id = $this->test_model->add_std_test_name(array('test_name' => trim($test_name)));
				if ($test_id > 0) {
					$status = TRUE;
					$msg = _t('global.msg.save_success');
					$response_data = array('test_id' => $test_id, 'test_name' => $test_name);
				}
			}
		}

		$data['result']	= json_encode(array('status'  => $status, 'msg' => $msg, 'data' => $response_data));
		$this->load->view('ajax_view/view_result', $data);
	}

	/**
	 * Update Standard Test name
	 */
	public function update_std_test_name() {
		$this->app_language->load('admin');
		$test_id = $this->input->post('test_id');
		$test_name = $this->input->post('test_name');
		$msg = _t('global.msg.fill_required_data');
		$status = FALSE;
		$response_data = array();

		if (!empty(trim($test_name)) && (int)$test_id > 0) {
			//check if test name is exist
			if (count($this->test_model->get_std_test(array('test_name' => trim($test_name), 'ID !=' => (int)$test_id))) > 0) {
				$msg = _t('admin.msg.test_exist');
			} else {
				$msg = _t('global.msg.update_fail');
				$test_id = $this->test_model->update_std_test_name($test_id, array('test_name' => trim($test_name)));
				if ($test_id > 0) {
					$status = TRUE;
					$msg = _t('global.msg.update_success');
				}
			}
		}

		$data['result']	= json_encode(array('status'  => $status, 'msg' => $msg, 'data' => $response_data));
		$this->load->view('ajax_view/view_result', $data);
	}

	/**
	 * Delete Standard Test name
	 */
	public function delete_std_test_name() {
		$this->app_language->load('admin');
		$test_id = $this->input->post('test_id');
		$msg = _t('global.msg.delete_fail');
		$status = FALSE;

		if ((int)$test_id > 0) {
			$result = $this->test_model->update_std_test_name($test_id, array('status' => FALSE));
			if ($result > 0) {
				$status = TRUE;
				$msg = _t('global.msg.delete_success');
			}
		}

		$data['result']	= json_encode(array('status'  => $status, 'msg' => $msg));
		$this->load->view('ajax_view/view_result', $data);
	}

	/**
	 * Add New Standard Group Result
	 */
	public function add_std_group_result() {
		$this->app_language->load('admin');
		$group_name = $this->input->post('group_name');
		$msg = _t('global.msg.fill_required_data');
		$status = FALSE;
		$response_data = array();

		if (!empty(trim($group_name))) {
			//check if group name is exist
			if (count($this->test_model->get_std_group_result(array('group_name' => trim($group_name)))) > 0) {
				$msg = _t('admin.msg.group_result_exist');
			} else {
				$msg = _t('global.msg.save_fail');
				$group_result_id = $this->test_model->add_std_group_result(array('group_name' => trim($group_name)));
				if ($group_result_id > 0) {
					$status = TRUE;
					$msg = _t('global.msg.save_success');
					$response_data = array('group_result_id' => $group_result_id, 'group_name' => $group_name);
				}
			}
		}

		$data['result']	= json_encode(array('status'  => $status, 'msg' => $msg, 'data' => $response_data));
		$this->load->view('ajax_view/view_result', $data);
	}

	/**
	 * Update Standard Group Result
	 */
	public function update_std_group_result() {
		$this->app_language->load('admin');
		$group_result_id = $this->input->post('group_result_id');
		$group_name = $this->input->post('group_name');
		$msg = _t('global.msg.fill_required_data');
		$status = FALSE;
		$response_data = array();

		if (!empty(trim($group_name)) && (int)$group_result_id > 0) {
			//check if test name is exist
			if (count($this->test_model->get_std_group_result(array('group_name' => trim($group_name), 'ID !=' => (int)$group_result_id))) > 0) {
				$msg = _t('admin.msg.group_result_exist');
			} else {
				$msg = _t('global.msg.update_fail');
				$result = $this->test_model->update_std_group_result($group_result_id, array('group_name' => trim($group_name)));
				if ($result > 0) {
					$status = TRUE;
					$msg = _t('global.msg.update_success');
				}
			}
		}

		$data['result']	= json_encode(array('status'  => $status, 'msg' => $msg, 'data' => $response_data));
		$this->load->view('ajax_view/view_result', $data);
	}

	/**
	 * Delete Standard Group Result
	 */
	public function delete_std_group_result() {
		$this->app_language->load('admin');
		$group_result_id = $this->input->post('group_result_id');
		$msg = _t('global.msg.delete_fail');
		$status = FALSE;

		if ((int)$group_result_id > 0) {
			$result = $this->test_model->update_std_group_result($group_result_id, array('status' => FALSE));
			if ($result > 0) {
				$status = TRUE;
				$msg = _t('global.msg.delete_success');
			}
		}

		$data['result']	= json_encode(array('status'  => $status, 'msg' => $msg));
		$this->load->view('ajax_view/view_result', $data);
	}

    /**
     * View lab test payment (DataTable)
     */
	public function view_lab_test_payment() {
	    $data = $this->input->post();
	    $result = $this->test_model->view_lab_test_payment($data);
	    echo json_encode($result);
    }

    /**
     * Add new lab test payment
     */
    public function add_lab_test_payment() {
        $group_result 	= $this->input->post('group_result');
	    $test_payments 	= $this->input->post('test_payments');

	    $msg 			= _t('global.msg.fill_required_data');
	    $status 		= FALSE;
	    if (count($test_payments) > 0 && !empty($group_result)) {
            $payments 	= $this->test_model->get_lab_test_payment($group_result);
            $msg 		= _t("global.msg.save_fail");

            if (count($payments) == 0) {
                $status = $this->test_model->add_lab_test_payment($test_payments);
                if ($status) $msg = _t("global.msg.save_success");
            }
        }

        echo json_encode(['status' => $status, 'msg' => $msg]);
    }

    /**
     * Update lab test payment
     */
    public function update_lab_test_payment() {
        $group_result = $this->input->post('group_result');
        $test_payments = $this->input->post('test_payments');

        $msg = _t('global.msg.fill_required_data');
        $status = FALSE;
        if (count($test_payments) > 0 && !empty($group_result)) {
            $msg = _t("global.msg.update_fail");
            $this->db->trans_start();
            $this->test_model->delete_lab_test_payment(NULL, $group_result);
            $this->test_model->add_lab_test_payment($test_payments);
            $this->db->trans_complete();
            $status = $this->db->trans_status();
            if ($status) $msg = _t("global.msg.update_success");
        }

        echo json_encode(['status' => $status, 'msg' => $msg]);
    }

    /**
     * Delete lab test payment
     */
    public function delete_lab_test_payment() {
        $group_result = $this->input->post('group_result');
        $msg = _t('global.msg.delete_fail');
        $status = FALSE;
        if (!empty($group_result)) {
            $status = $this->test_model->delete_lab_test_payment(NULL, $group_result);
            if ($status) $msg = _t("global.msg.update_success");
        }
        echo json_encode(['status' => $status, 'msg' => $msg]);
    }

    /**
     * Get lab test payment
     */
    public function get_lab_test_payment() {
        $group_result = $this->input->post('group_result');
        $result = $this->test_model->get_lab_test_payment($group_result);
        echo json_encode($result);
    }
}