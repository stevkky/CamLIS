<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class History_Model extends CI_Model {

	public function result($patient_test_id)
	{
		$this->db->select('user.fullname, ptest_result.from_result, ptest_result.to_result, ptest_result.from_quantity_id, ptest_result.to_quantity_id, organism_quantity.quantity, ptest_result.modified_date, ptest_result.reason_comment');
		$this->db->from('camlis_history_ptest_result as ptest_result');
		$this->db->join('camlis_aauth_users as user', 'user.id = ptest_result.user_id');
		$this->db->join('camlis_std_organism_quantity as organism_quantity', 'organism_quantity."ID" = ptest_result.to_quantity_id', 'left');
		$this->db->where('ptest_result.patient_test_id', $patient_test_id);
		return $this->db->get()->result();	
	}
}
