<?php
defined('BASEPATH') OR die('Access denied!');
class Quantity extends MY_Controller {
	public function __construct() {
		parent::__construct();
		$this->load->model(array('quantity_model'));
	}

	/**
	 * Add new Standard Quantity
	 */
	public function add_std_organism_quantity() {
		$this->app_language->load('admin');
		$quantity = $this->input->post('quantity');
		$quantity = trim($quantity);
		$msg = _t('global.msg.fill_required_data');
		$status = FALSE;

		if (!empty($quantity)) {
			//Check if quantity is exists
			if (count($this->quantity_model->get_std_organism_quantity(array("quantity" => $quantity))) > 0) {
				$msg = _t('admin.msg.quantity_exist');
			} else {
				if ($this->quantity_model->add_std_organism_quantity(array('quantity' => $quantity)) > 0) {
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
	 * Update Standard Quantity
	 */
	public function update_std_organism_quantity() {
		$this->app_language->load('admin');
		$id 			= $this->input->post('ID');
		$quantity 		= $this->input->post('quantity');
		$quantity 		= trim($quantity);
		$msg 			= _t('global.msg.fill_required_data');
		$status = FALSE;

		if (!empty($quantity) && $id > 0) {
			//Check if Quantity is exists
			if (count($this->quantity_model->get_std_organism_quantity(array("quantity" => $quantity, '"ID" <>' => $id))) > 0) {
				$msg = _t('admin.msg.organism_exist');
			}
			else {
				if ($this->quantity_model->update_std_organism_quantity($id, array("quantity" => $quantity)) > 0) {
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
	 * Delete Standard Quantity
	 */
	public function delete_std_organism_quantity() {
		$id = $this->input->post('ID');
		$status = FALSE;
		$msg = _t('global.msg.delete_fail');

		if ($id > 0) {
			if ($this->quantity_model->update_std_organism_quantity($id, array('status' => FALSE)) > 0) {
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
	 * View Standard Quantity
	 */
	public function view_std_organism_quantity() {
		$result			= $this->quantity_model->view_std_organism_quantity($this->input->post());

		$data['result'] = json_encode($result);
		$this->load->view('ajax_view/view_result', $data);
	}
}