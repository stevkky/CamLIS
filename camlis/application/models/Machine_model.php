<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Machine_model extends MY_Model {

	public function get_machine()
	{
		$this->db->select('id, machine_name');
		$this->db->from('camlis_machine');
		return $this->db->get()->result();
	}

	public function get_name($id){
		$this->db->select('machine.machine_name');
		$this->db->from('camlis_machine machine');
		$this->db->join('camlis_machine_test machine_test', 'machine_test.machine_id = machine.id');
		$this->db->join('camlis_std_sample_test sample_test', 'sample_test."ID" = machine_test.std_sample_test_id');
		$this->db->join('camlis_std_test st_test', 'st_test."ID" = sample_test.test_id');
		$this->db->where('sample_test."ID"', $id);
		$this->db->where('machine_test.lab_id', $this->laboratory_id);
		return $this->db->get()->result();
	}

	public function create($data)
	{
		$this->db->insert('camlis_machine', $data);
		$message = ($this->db->affected_rows() > 0 ) ? array('status' => 1, 'msg' => 'complete', 'data' => $this->array_push_assoc($data, 'id', $this->db->insert_id())) : array('status' => 0, 'msg' => 'faile') ;
		return $message;
	}

	public function update($data, $id)
	{
		$this->db->where('id', $id);
		$this->db->update('camlis_machine', $data);
		$message = ($this->db->affected_rows() > 0 ) ? array('status' => 1, 'msg' => 'complete', 'data' => $this->array_push_assoc($data, 'id', $id)) : array('status' => 0, 'msg' => 'faile');
		return $message;
	}

	function array_push_assoc($array, $key, $value){
		$array[$key] = $value;
		return $array;
	}

	public function list_machine_test()
	{
		$this->db->select('machine_test.id as test_id, machine.machine_name, machine.id, machine_test.lab_id, department.department_name, std_sample.sample_name, std_test.test_name');
		$this->db->from('camlis_machine_test as machine_test');
		$this->db->join('camlis_std_sample_test as sample_test', 'sample_test."ID" = machine_test.std_sample_test_id', 'right');
		$this->db->join('camlis_machine as machine', 'machine.id = machine_test.machine_id', 'right');
		$this->db->join('camlis_std_test as std_test', 'std_test."ID" = sample_test.test_id', 'right');
		$this->db->join('camlis_std_department_sample as department_sample', 'department_sample."ID" = sample_test.department_sample_id', 'right');
		$this->db->join('camlis_std_sample as std_sample', 'std_sample."ID" = department_sample.sample_id', 'right');
		$this->db->join('camlis_std_department as department', 'department."ID" = department_sample.department_id', 'right');
		$this->db->where('sample_test.is_heading', false);
		$this->db->where('machine_test.lab_id', $this->laboratory_id);
		return $this->db->get()->result();
		/*Generat SQL command
		$this->db->get();
		return $this->db->last_query();
		*/
	}

	public function get_all_deparment()
	{
		$this->db->select('*');
		$this->db->from('camlis_std_department');
		$this->db->where('status', 1);
		$result = array();
		foreach ($this->db->get()->result() as $department) {
			$result[$department->ID] = $department;
			$result[$department->ID]->samples = $this->get_sample($department->ID);
		}
		return $result;
	}

	public function get_sample($department_id)
	{
		$this->db->select('std.test_name, st.is_heading');
		$this->db->from('camlis_std_test std');
		$this->db->join('camlis_std_sample_test st', 'st.test_id = std."ID"');
		$this->db->where('st.department_sample_id', $department_id);
		return $this->db->get()->result();
	}

}
