<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Machine_test extends MY_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model('machine_test_model', 'machine_test');
	}

	public function get_all_tests()
	{
		echo json_encode($this->machine_test->get_all_sample_test());
	}

	public function create()
	{
		$lab_id = $this->input->post('lab_id');
		$machines = json_decode(stripslashes($this->input->post('machine_id')));
		$tests = json_decode(stripslashes($this->input->post('sample_tests')));
		$sample_tests = array();
		$length = 0;
		for ($j=0; $j < count($machines); $j++) { 
			for ($i=0; $i < count($tests); $i++) { 
				$sample_tests[$i + $length] = array(
					'lab_id' => $lab_id,
					'machine_id' => $machines[$j],
					'std_sample_test_id' => $tests[$i]
				);
			}
			$length = count($sample_tests);
			if ($this->existing($machines[$j])) {
				$this->db->delete('camlis_machine_test', array('machine_id' => $machines[$j]));
			}
		}
		$this->db->insert_batch('camlis_machine_test', $sample_tests);
		$message = ($this->db->affected_rows() > 0) ? array('status' => true,'msg'=> 'Save complete') : array('status' => false,'msg'=> 'Save fail');
		echo json_encode($message);
	}

	public function existing($id)
	{
		$this->db->select('machine_id');
		$this->db->from('camlis_machine_test');
		$this->db->where('machine_id', $id);
		return ($this->db->get()->num_rows() > 0 ) ? true : false ;
	}

	public function get_test_by_machine_lab()
	{
		$machine_id = json_decode(stripslashes($this->input->post('machine_id')));
		$lab_id = $this->input->post('lab_id');
		echo json_encode($this->machine_test->get_test_by_machine_lab($machine_id, $lab_id));
	}

	public function delete()
	{
		echo json_encode($this->machine_test->delete($this->input->post('id')));
	}
}