<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Tat_model extends MY_Model {

	public function get_tat_excel($condition)
	{
		/*select the wanted fields*/
		$this->db->select(array(
            "sample.\"labID\"",
		    'sample.sample_number',
		    'sample_test.group_result',
		    'count(*) as total',
		    "array_agg(concat(sample.collected_date, ' ', sample.collected_time)) as collected_date",
		    "array_agg(concat(sample.received_date, ' ', sample.received_time)) as received_date",
		    'sample."printedDate"'
		));

		$this->db->from('camlis_patient_sample_tests as test');

		/*first relation with test*/
        $this->db->join("camlis_std_sample_test as sample_test", "sample_test.\"ID\" = test.sample_test_id and sample_test.status = 'true'");
        $this->db->join("camlis_std_test as std_test", "std_test.\"ID\" = sample_test.test_id and std_test.status = 'true'");

        /*second relation department*/
        $this->db->join("camlis_std_department_sample as dep_sample", "dep_sample.\"ID\" = sample_test.department_sample_id and dep_sample.status = 'true'");
        $this->db->join("camlis_std_department department", "department.\"ID\" = dep_sample.department_id and department.status = 'true'");

        /*third relationship with sample*/
        $this->db->join("camlis_patient_sample as sample", 'sample."ID" = test.patient_sample_id and sample.status = 1');
        $this->db->where("sample.\"labID\"", $this->laboratory_id);

        /*condition*/
        $this->db->where('sample_test.group_result is NOT NULL', null, false);
        $this->db->where('sample."printedDate" is NOT NULL', null, false);
        $this->db->where('sample.received_date >=', $condition['start_date']);
        $this->db->where('sample.received_date <=', $condition['end_date']);
        if (count($condition['group_result']) > 0) {
            $this->db->where_in("sample_test.\"ID\"", $condition['group_result']);
        }

        /*group by*/
        $this->db->group_by("sample.\"labID\"");
        $this->db->group_by('sample.sample_number');
        $this->db->group_by('sample_test.group_result');
        $this->db->group_by('sample.collected_date');
        $this->db->group_by('sample.received_date');
        $this->db->group_by('sample."printedDate"');
        $this->db->group_by('sample_test.order');

        /*order by*/
        $this->db->order_by('sample.sample_number', 'asc');
        $this->db->order_by('sample."printedDate"', 'asc');
        $this->db->order_by('sample_test.order', 'asc');
        return $this->db->get()->result();
	}

    public function get_group_result_by_id($id)
    {
        $this->db->select('sample_test.group_result');
        $this->db->from('camlis_std_sample_test sample_test');
        $this->db->where_in('sample_test."ID"', $id);
        $this->db->order_by('sample_test.order', 'asc');
        return $this->db->get()->result();
    }

}