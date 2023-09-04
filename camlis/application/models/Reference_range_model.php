<?php
defined('BASEPATH') OR die('Access denied!');

class Reference_range_model extends MY_Model
{
    /**
     * Get Ref. Range from Standard Test
     * @param array $sample_test_id
     * @return object DB Query
     */
    public function get_std_reference_range(array $sample_test_id) {
        $this->db->select('
			nv.sample_test_id,
			nv.patient_type,
			nv.range_sign,
			nv.min_value,
			nv.max_value,
			ptype.type,
			ptype.gender,
			ptype.min_age,
			ptype.max_age,
			ptype.min_age_unit,
			ptype.max_age_unit,
			ptype.is_equal
		');
        $this->db->from('camlis_std_normal_value AS nv');
        $this->db->join('camlis_std_patient_type AS ptype', 'nv.patient_type = ptype."ID"', 'inner');
        $this->db->where('nv.status', 't');
        $this->db->where('ptype.status', 't');
        if(!empty($sample_test_id))
        {
            $this->db->where_in('nv.sample_test_id', $sample_test_id);
        }
        

        return $this->db->get()->result_array();
    }

    /**
     * Get Ref. Range from Standard Test (vary in each lab)
     * @param array $sample_test_id
     * @return object DB Query
     */
    public function get_lab_reference_range(array $sample_test_id) {
        $this->db->select('
			nv.sample_test_id,
			nv.patient_type,
			nv.range_sign,
			nv.min_value,
			nv.max_value,
			ptype.type,
			ptype.gender,
			ptype.min_age,
			ptype.max_age,
			ptype.min_age_unit,
			ptype.max_age_unit,
			ptype.is_equal
		');
        $this->db->from('camlis_lab_normal_value AS nv');
        $this->db->join('camlis_std_patient_type AS ptype', 'nv.patient_type = ptype."ID"', 'inner');
        $this->db->where('ptype.status', 't');
        $this->db->where('lab_id', $this->laboratory_id);
        if(!empty($sample_test_id))
        {
            $this->db->where_in('nv.sample_test_id', $sample_test_id);
        }
        

        return $this->db->get()->result_array();
    }

    /**
     * Delete Reference Lang for each Lab
     * @param $sample_test_id
     */
    public function delete_lab_reference_range($sample_test_id) {
        return $this->db->delete('camlis_lab_normal_value', ['lab_id' => $this->laboratory_id, 'sample_test_id' => $sample_test_id]);
    }

    /**
     * Set Reference Lang for each Lab
     * @param $sample_test_id
     * @param array $reference_ranges
     */
    public function set_lab_reference_range($sample_test_id, array $reference_ranges) {
        $result = $this->db->insert_batch('camlis_lab_normal_value', $reference_ranges);
        $this->db->where(['lab_id' => $this->laboratory_id, 'sample_test_id' => $sample_test_id]);
        $this->db->update('camlis_lab_normal_value', ['entry_by' => $this->user_id, 'entry_date' => $this->timestamp, 'modified_by' => $this->user_id, 'modified_date' => $this->timestamp]);
        return $result > 0;
    }
}