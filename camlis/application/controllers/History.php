<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class History extends MY_Controller {
	public function __construct()
	{
		parent::__construct();
		$this->load->model('history_model', 'history');
	}

	public function result()
	{
		echo json_encode($this->history->result($this->input->post('patient_test_id')));
	}

}
