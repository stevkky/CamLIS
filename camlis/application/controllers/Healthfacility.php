<?php
defined('BASEPATH') OR die("Access denied!");
class HealthFacility extends MY_Controller {
	public function __construct() {
		parent::__construct();
		$this->load->model('healthfacility_model');
	}
	
	public function fetch() {
		$hf = $this->healthfacility_model->fetch();
		
		$data['result'] = json_encode($result);
		$this->load->view('ajax_view/view_result', $data);
	}
}