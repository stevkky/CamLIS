<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Machine extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model('machine_model', 'machine');
	}

	public function index()
	{
		echo json_encode($this->machine->get_name($this->input->post('id')));
	}

	public function create()
	{
		$machine = array('machine_name' => $this->input->post('machine_name'));
		echo json_encode($this->machine->create($machine));
	}

	public function update()
	{
		$machine = array('machine_name' => $this->input->post('machine_name'));
		echo json_encode($this->machine->update($machine, $this->input->post('machine_id')));
	}

	public function all_machine_test()
	{
		$data = $this->input->post();
		$result = $this->machine->list_machine_test($data);
	    echo json_encode($result);
	}
}
