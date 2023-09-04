<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Machine_test_model extends CI_Model {

	public function get_all_sample_test()
	{
		$this->db->select('sample_test."ID", sample_test.is_heading, department.department_name, sample.sample_name, test.test_name');
		$this->db->from('camlis_std_sample_test as sample_test');
		$this->db->join('camlis_std_test as test', 'test."ID" = sample_test.test_id', 'left');
		$this->db->join('camlis_std_department_sample as department_sample', 'department_sample."ID" = sample_test.department_sample_id', 'left');
		$this->db->join('camlis_std_department as department', 'department."ID" = department_sample.department_id', 'left');
		$this->db->join('camlis_std_sample as sample', 'sample."ID" = department_sample.sample_id', 'left');
		return $this->db->get()->result();
	}

	public function get_test_by_machine_lab($machine_id, $lab_id)
	{
		$this->db->select('std_sample_test_id');
		$this->db->from('camlis_machine_test');
		$this->db->where_in('machine_id', $machine_id);
		$this->db->where('lab_id', $lab_id);
		return $this->db->get()->result();
	}

	public function delete($id)
	{
		$this->db->where('id', $id);
		$this->db->delete('camlis_machine_test');
		$message = ($this->db->affected_rows() > 0 ) ? array('status' => 1 , 'msg' => 'Deleted') : array('status' => 0 , 'msg' => 'Delete failed');
		return $message;
	}

}
