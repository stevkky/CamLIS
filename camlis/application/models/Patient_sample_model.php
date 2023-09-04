<?php
defined('BASEPATH') OR die("Access denined!");
class Patient_sample_model extends MY_Model {
	public function __construct() {
		parent::__construct();
		$this->load->library('DataTable');
	}

    /**
     * Get patient sample
     * @param null $patient_sample_id
     * @param null $sample_number
     * @param null $patient_id
     * @param null $patient_code
     * @param null $laboratory_id
     * @return mixed
     */
	public function get_patient_sample($patient_sample_id = NULL, $sample_number = NULL, $patient_id = NULL, $patient_code = NULL, $laboratory_id = NULL) {
		$this->db->select('
		    psample."labID" AS laboratory_id,
			psample."ID" AS patient_sample_id,
			psample.patient_id,
			psample.sample_number,
			psample.sample_source_id,
			source.source_name AS sample_source_name,
			psample.requester_id,
			requester.requester_name,
			psample.collected_date,
			psample.collected_time,
			psample.received_date,
			psample.received_time,
			psample.admission_date,
			psample.payment_type_id,
			psample.is_urgent,
			psample.payment_needed,
			psample.for_research,
			psample.clinical_history,
			psample.is_rejected,
			psample.reject_comment,
			psample.result_comment,
			psample.is_printed,
			psample."printedBy",
			psample."printedDate",
			MAX(result.test_date) AS test_date,
			psample.completed_by,
			psample.sample_collector,
			psample.phone_number,
			psample.phone_number_sample_collector,
			psample.number_of_sample,
			psample.health_facility
		');
		$this->db->from('camlis_patient_sample AS psample');
		if ($patient_code) $this->db->join('v_camlis_all_patients AS patient', 'psample.patient_id = patient.pid AND psample."labID" = patient.lab_id', 'inner');
		$this->db->join('camlis_lab_sample_source AS source', 'psample.sample_source_id = source."ID"', 'inner');
		$this->db->join('camlis_lab_requester AS requester', 'requester."ID" = psample.requester_id', 'inner');
		$this->db->join('camlis_ptest_result AS result', 'psample."ID" = result.patient_sample_id AND result.status = 1', 'left');
        $this->db->where('psample.status', 1);
        $this->db->where('source.status', TRUE);
        if ($laboratory_id) $this->db->where('psample."labID"', $laboratory_id);
        if ($patient_id) $this->db->where('psample.patient_id', $patient_id);
        if ($patient_code) $this->db->where('patient.patient_code', $patient_code);
        if ($patient_sample_id) $this->db->where('psample."ID"', $patient_sample_id);
		if ($sample_number) $this->db->where('psample.sample_number', $sample_number);
		$this->db->group_by('psample."ID", sample_source_name, requester.requester_name');
		return $this->db->get()->result_array();
	}

    /**
     * Look for patient sample
     * @param null $sample_number
     * @param null $patient_code
     * @param null $limit
     */
	public function lookup_patient_sample($sample_number = NULL, $patient_code = NULL, $limit = NULL) {
	    $this->db->select("psample.*");
        $this->db->from('camlis_patient_sample AS psample');
        if ($patient_code) $this->db->join('v_camlis_all_patients AS patient', 'psample.patient_id = patient.pid AND psample."labID" = patient.lab_id', 'inner');
        $this->db->where('psample.status', 1);
        if ($patient_code) $this->db->where('patient.patient_code', $patient_code);
        if ($sample_number) $this->db->like('psample.sample_number', $sample_number, 'both');
        if ($limit) $this->db->limit($limit);

        return $this->db->get()->result_array();
    }

    /**
     * Check if Sample number is unique
     * @param $sample_number
     * @return boolean
     */
	public function is_unique_sample_number($sample_number) {
	    $query = $this->db->get_where('camlis_patient_sample', ['status' => 1, 'sample_number' => $sample_number, '"labID"' => $this->laboratory_id]);
	    return !($query->num_rows() > 0);
    }

	/**
	 * Get patient's sample number
	 */
	public function get_psample_number() {
		//$this->db->select("CONCAT(REPEAT('0', 4 - LENGTH(COUNT(*) + 1)), COUNT(*) + 1, '-', DATE_FORMAT('".$this->timestamp."','%d%m%Y')) AS sample_number");
		//$this->db->where('labID', $this->laboratory_id);
		//$this->db->where('DATE(entryDate)', "DATE('".$this->timestamp."')", FALSE);
		//$this->db->limit(1);
		//$query = $this->db->get("camlis_patient_sample");

		$this->db->select("CONCAT(REPEAT('0', 4 - length( cast((count(*)+1) as TEXT))), COUNT(*) + 1, '-', to_char(timestamp '".$this->timestamp."', 'DDMMYYYY')) as sample_number");
		$this->db->where("labID", $this->laboratory_id);
		$this->db->where('DATE("entryDate")', "'".$this->timestamp."'"."::date",false);
		$this->db->limit(1);
		$query = $this->db->get("camlis_patient_sample");

		return $query->row();
	}

	/**
	 * Add Patient's Sample
	 * @param $data
     * @return mixed
	 */
	public function add_patient_sample($data) {

		$this->db->set($data);
		$this->db->set("labID", $this->laboratory_id);
		$this->db->set('"entryBy"', $this->user_id);
		$this->db->set('"entryDate"',$this->timestamp);
		$this->db->insert('camlis_patient_sample');
		
		return $this->db->insert_id();
		
	}
	/*
	* 29/08/2018 create this function
	* Add and edit table camlis_result_comment
	* @param $patient_sample_id
	* @param $data
	* @return number
	*/
	public function save_and_edit_reject_comment($patient_sample_id, $data)
	{
		$reject_comment_insert = array();
		$reject_comment_update = array();
		foreach ($data as $key => $value) {
			$this->db->where('patient_sample_id', $patient_sample_id);
			$this->db->where('department_sample_id', $data[$key]['department_sample_id']);
			$existing = $this->db->get('camlis_result_comment');
			($existing->num_rows() == 1) ? $reject_comment_update[] = $data[$key] : $reject_comment_insert[] = array_merge($data[$key], array('patient_sample_id'=>$patient_sample_id));	
		}
		if (count($reject_comment_insert) > 0) {
			$this->db->insert_batch('camlis_result_comment', $reject_comment_insert);
		}
		if (count($reject_comment_update) > 0) {
			$this->db->where('patient_sample_id', $patient_sample_id);
            $this->db->update_batch('camlis_result_comment', $reject_comment_update, 'department_sample_id');
		}
		return $this->db->affected_rows();
	}
	/**
	 * Update Patient's Sample
	 * @param $data
	 * @return mixed
	 */
	public function update_patient_sample($psample_id, $data) {
		$this->db->set($data);
		$this->db->set('"modifiedBy"', $this->user_id);
		$this->db->set('"modifiedDate"', $this->timestamp);
		$this->db->where('status', 1);
		$this->db->where('"ID"', $psample_id);
		$this->db->update('camlis_patient_sample');

		return $this->db->affected_rows();
	}

    /**
     * Update Progress status
     * @param $patient_sample_id
     * @return bool
     */
	public function update_progress_status($patient_sample_id) {
		/*
        $sql = "UPDATE camlis_patient_sample AS psample
                INNER JOIN (
                    SELECT ptest.patient_sample_id,
                           COUNT(DISTINCT ptest.ID) AS test_count,
                           COUNT(DISTINCT IF(ptest.is_rejected = 1, ptest.ID, NULL)) AS rejected_test_count, 
					       COUNT(DISTINCT IF(ptest.is_rejected = 0, presult.patient_test_id, NULL)) AS result_count
                    FROM camlis_patient_sample_tests AS ptest
                    INNER JOIN camlis_std_sample_test AS stest ON ptest.sample_test_id = stest.ID
                    LEFT JOIN camlis_ptest_result AS presult ON presult.patient_test_id = ptest.ID AND presult.`status` = 1
                    WHERE ptest.status = 1
                          AND stest.is_heading = 0
                          AND ptest.patient_sample_id = ?
                    GROUP BY ptest.patient_sample_id
                ) _t ON psample.ID = _t.patient_sample_id
                SET psample.progress_status = (CASE
                        WHEN psample.is_rejected = 1 THEN 5
                        WHEN psample.progress_status = 3 AND psample.is_printed = 1 THEN 4
                        WHEN _t.test_count - _t.rejected_test_count = _t.result_count AND psample.is_printed = 1 THEN 4
                        WHEN _t.result_count = 0 THEN 1
                        WHEN _t.test_count - _t.rejected_test_count > _t.result_count THEN 2
                        WHEN _t.test_count - _t.rejected_test_count = _t.result_count THEN 3
                     END)
				WHERE psample.`status` = 1 AND psample.ID = ? AND psample.\"labID\" = ?";
			*/

			$sql = "UPDATE camlis_patient_sample
					SET progress_status = (CASE
						WHEN is_rejected = 1 THEN 5
						WHEN progress_status = 3 AND is_printed = 't' THEN 4
						WHEN _t.test_count - _t.rejected_test_count = _t.result_count AND is_printed = 't' THEN 4
						WHEN _t.result_count = 0 THEN 1
						WHEN _t.test_count - _t.rejected_test_count > _t.result_count THEN 2
						WHEN _t.test_count - _t.rejected_test_count = _t.result_count THEN 3
					END)
                FROM (SELECT ptest.patient_sample_id,
                           COUNT(DISTINCT ptest.\"ID\") AS test_count,
                           COUNT(DISTINCT (CASE WHEN ptest.is_rejected = 't' THEN ptest.\"ID\" ELSE NULL END)) AS rejected_test_count,						   
					       COUNT(DISTINCT (CASE WHEN ptest.is_rejected = 'f' THEN presult.patient_test_id ELSE NULL END)) AS result_count
                    FROM camlis_patient_sample_tests AS ptest
                    INNER JOIN camlis_std_sample_test AS stest ON ptest.sample_test_id = stest.\"ID\"
                    LEFT JOIN camlis_ptest_result AS presult ON presult.patient_test_id = ptest.\"ID\" AND presult.status = 1
                    WHERE ptest.status = 1
                          AND stest.is_heading = 'f'
                          AND ptest.patient_sample_id = ?
                    GROUP BY ptest.patient_sample_id
                ) _t
                WHERE camlis_patient_sample.\"ID\" = _t.patient_sample_id AND camlis_patient_sample.status = 1 AND camlis_patient_sample.\"ID\" = ? AND camlis_patient_sample.\"labID\" = ?";
        $this->db->query($sql, [$patient_sample_id, $patient_sample_id, $this->laboratory_id]);

        return $this->db->affected_rows() > 0 ? TRUE : FALSE;
    }

    /**
     * Update Rejection status
     * @param $patient_sample_id
     * @return bool
     */
    public function update_rejection_status($patient_sample_id) {
		/*
        $sql = "UPDATE camlis_patient_sample AS psample
                INNER JOIN (
                    SELECT ptest.patient_sample_id, COUNT(ptest.ID) AS test_count, SUM(IF(ptest.is_rejected = 1, 1, 0)) AS rejected_test_count
                    FROM camlis_patient_sample_tests AS ptest
                    INNER JOIN camlis_std_sample_test AS stest ON ptest.sample_test_id = stest.\"ID\"
                    WHERE ptest.status = 1
                          AND stest.is_heading = 0
                          AND ptest.patient_sample_id = ?
                    GROUP BY ptest.patient_sample_id
                ) _t ON psample.ID = _t.patient_sample_id
                SET psample.is_rejected = IF(_t.test_count = _t.rejected_test_count, 1, 0)
				WHERE psample.status = 1 AND psample.\"ID\" = ? AND psample.\"labID\" = ?";
		*/

		$sql = "UPDATE camlis_patient_sample 
				SET is_rejected = (CASE WHEN _t.test_count = _t.rejected_test_count THEN 1 ELSE 0 END)
                FROM (
                    SELECT ptest.patient_sample_id, COUNT(ptest.\"ID\") AS test_count, SUM(CASE WHEN is_rejected = 't' THEN 1 ELSE 0 END) AS rejected_test_count
                    FROM camlis_patient_sample_tests AS ptest
                    INNER JOIN camlis_std_sample_test AS stest ON ptest.sample_test_id = stest.\"ID\"
                    WHERE ptest.status = 1
                          AND stest.is_heading = 'f'
                          AND ptest.patient_sample_id = ?
                    GROUP BY ptest.patient_sample_id
                ) _t
				WHERE camlis_patient_sample.\"ID\" = _t.patient_sample_id AND camlis_patient_sample.status = 1 AND camlis_patient_sample.\"ID\" = ? AND camlis_patient_sample.\"labID\" = ?";

		//return $sql;
        $this->db->query($sql, [$patient_sample_id, $patient_sample_id, $this->laboratory_id]);
        return $this->db->affected_rows() > 0 ? TRUE : FALSE;
    }

    /**
     * Create result comment
     * @param $data
     * @return mixed
     */
    public function create_result_comment($data) {
        $this->db->insert_batch('camlis_result_comment', $data);
        return $this->db->affected_rows() > 0;
    }
    /**
     * Delete result comment
     * @param $patient_sample_id
     * @return boolean
     */
    public function delete_result_comment($patient_sample_id) {
        $this->db->where('patient_sample_id', $patient_sample_id);
        $this->db->delete('camlis_result_comment');
        return $this->db->affected_rows() > 0;
    }

    /**
     * Get Result Comment
     * @param $patient_sample_id
     * @param bool $department_sample_id
     */
    public function get_result_comment($patient_sample_id, $department_sample_id = FALSE) {
        $this->db->where('patient_sample_id', $patient_sample_id);
        !$department_sample_id OR $this->db->where('department_sample_id', $department_sample_id);
        return $this->db->get('camlis_result_comment')->result_array();
    }

	/**
	 * Delete patient's sample
	 * @param $patient_sample_id
     * @param $patient_id
	 * @return bool
	 */
	public function delete_patient_sample($patient_sample_id, $patient_id = FALSE) {
		$this->db->set('status', 0);
		$this->db->set('"modifiedBy"', $this->user_id);
		$this->db->set('"modifiedDate"', $this->timestamp);
		$this->db->where('status', 1);
		$this->db->where('"labID"', $this->laboratory_id);
		!$patient_sample_id OR $this->db->where('"ID"', $patient_sample_id);
		!$patient_id OR $this->db->where('patient_id', $patient_id);
		$this->db->update('camlis_patient_sample');
		return $this->db->affected_rows() > 0;
	}

    /**
     * Delete Patient Sample Detail
     * @param $patient_sample_id
     * @param $patient_id
     * @return boolean
     */
	public function delete_patient_sample_detail($patient_sample_id, $patient_id = FALSE) {
		/*
	    $sql = "DELETE detail FROM camlis_patient_sample_detail AS detail
	            INNER JOIN camlis_patient_sample AS psample ON detail.patient_sample_id = psample.\"ID\"
				WHERE detail.status = 't' AND psample.\"labID\" = ".$this->laboratory_id;
		*/

		$sql = "DELETE FROM camlis_patient_sample_detail
				WHERE status = 't' AND patient_sample_id IN (SELECT \"ID\" FROM camlis_patient_sample as psample WHERE \"labID\" = ".$this->laboratory_id." ";

	    !$patient_sample_id OR $sql .= " AND psample.\"ID\" = ".$patient_sample_id;
		!$patient_id OR $sql .= " AND psample.patient_id='".$patient_id."'";
		$sql .= " ) ";
	    return $this->db->query($sql) > 0;
    }

    /**
     * Delete Patient Sample Test
     * @param $patient_sample_id
     * @param bool $patient_id
     * @return boolean
     */
    public function delete_patient_sample_test($patient_sample_id, $patient_id = FALSE) {
		/*
		$sql = "UPDATE camlis_patient_sample_tests AS ptest
                INNER JOIN camlis_patient_sample AS psample ON ptest.patient_sample_id = psample.\"ID\"
                SET ptest.status = 1,
                    ptest.\"modifiedBy\" = ?,
                    ptest.\"modifiedDate\" = ?
                WHERE ptest.status = 1
					  AND psample.\"labID\" = ?";
		*/		  
		$sql = "UPDATE camlis_patient_sample_tests
				SET status = 1,
					\"modifiedBy\" = ?,
					\"modifiedDate\" = ?
				FROM ( SELECT \"ID\" , \"labID\", patient_id FROM camlis_patient_sample ) psample
				WHERE patient_sample_id = psample.\"ID\" AND camlis_patient_sample_tests.status = 1 AND psample.\"labID\" = ?";

        !$patient_sample_id OR $sql .= " AND psample.\"ID\" = ".$patient_sample_id;
        !$patient_id OR $sql .= " AND psample.patient_id='".$patient_id."'";
        return $this->db->query($sql, [$this->user_id, $this->timestamp, $this->laboratory_id]) > 0;
    }

    /**
     * Get User who works on current sample
     * @param $patient_sample_id
     * @return mixed
     */
	public function get_patient_sample_user($patient_sample_id) {
        $this->db->select('
            string_agg ( DISTINCT s_user.fullname, \', \' ORDER BY "s_user".fullname ) AS sample_entry_user,
			string_agg ( DISTINCT r_user.fullname, \', \' ORDER BY "r_user".fullname) AS result_entry_user,
			string_agg ( DISTINCT m_user.fullname, \', \' ORDER BY "m_user".fullname) AS "modifiedBy",
			psample."entryDate",
			psample."modifiedDate"
			
        ');
        $this->db->from('camlis_patient_sample AS psample');		
		//$this->db->join('camlis_aauth_users AS m_user', 'psample."modifiedBy" = m_user.id', 'inner');
        $this->db->join('camlis_aauth_users AS s_user', 'psample."entryBy" = s_user.id', 'inner');
        $this->db->join('camlis_ptest_result AS presult', 'psample."ID" = presult.patient_sample_id AND psample.status = 1', 'left');
		$this->db->join('camlis_aauth_users AS r_user', 'presult."entryBy" = r_user.id', 'left');
		$this->db->join('camlis_aauth_users AS m_user', 'psample."modifiedBy" = m_user.id', 'left');
        $this->db->where('psample.status', 1);
        $this->db->where('psample."ID"', $patient_sample_id);
		$this->db->group_by('psample."entryDate", psample."modifiedDate"');
        return $this->db->get()->row_array();
    }

	/**
	 * Get Tests of Patient's sample
	 * @param $psample_id
	 * @param bool $get_heirarchy
	 * @param number $parent
	 * @return array Result
	 */
	public function get_patient_sample_test($psample_id, $get_heirarchy = FALSE, $parent = 0, $dep_opt_view = NULL, $sam_opt_view = NULL) {
		$this->db->select('
			ptest."ID" AS patient_test_id,
			ptest.patient_sample_id,
			ptest.sample_test_id, 
			ptest.is_rejected,
			ptest.ref_range_min_value,
			ptest.ref_range_max_value,
			ptest.ref_range_sign,
			ptest.is_show,
			test."ID" AS test_id,
			test.test_name,
			sample_test."testPID",
			sample_test.is_heading,
			sample_test.field_type,
			dsample."ID" AS dep_sample_id,
			dep."ID" AS department_id,
			dep.department_name,
			sample."ID" AS sample_id,
			sample.sample_name,			
			sample_test.group_result,
			sample_test.formula,
			0 AS child_count,
			0 AS parent_count,
			(CASE WHEN length(ptest.unit_sign) = 0 THEN sample_test.unit_sign ELSE sample_test.unit_sign END) AS unit_sign,
			
			(select fullname from camlis_aauth_users where id = sample_test."entryBy") entry_by,
			sample_test."entryDate" as entry_date, 
			(select fullname from camlis_aauth_users where id =sample_test."modifiedBy") modified_by, 
			sample_test."modifiedDate" as modified_date,			
			rcmt.result_comment,
			rcmt.reject_comment
		');
		$this->db->from('camlis_patient_sample_tests AS ptest');
		$this->db->join('camlis_std_sample_test AS sample_test', 'ptest.sample_test_id = sample_test."ID"', 'inner');
		$this->db->join('camlis_std_test AS test', 'sample_test.test_id = test."ID"', 'inner');
		$this->db->join('camlis_std_department_sample AS dsample', 'dsample."ID" = sample_test.department_sample_id', 'inner');
		$this->db->join('camlis_std_department AS dep', 'dep."ID" = dsample.department_id', 'inner');
		$this->db->join('camlis_std_sample AS sample', 'sample."ID" = dsample.sample_id', 'inner');
        $this->db->join('camlis_result_comment as rcmt', 'rcmt.patient_sample_id = ptest.patient_sample_id and rcmt.department_sample_id = dsample."ID"', 'left');
		$this->db->where('ptest.status', 1);
		$this->db->where('test.status', 't');
		$this->db->where('sample_test.status', 't');
		$this->db->where('dsample.status', 't');
		$this->db->where('dep.status', 't');
		$this->db->where('sample.status', 't');
        $this->db->where_in('ptest.patient_sample_id', (array)$psample_id);
        $this->db->order_by('dep.order', 'asc');
        $this->db->order_by('dsample.order', 'asc');
        $this->db->order_by('sample_test.order', 'asc');

		// department view
		if($dep_opt_view || $dep_opt_view) {
			$_arr = explode(',',$dep_opt_view);
			$_sam = explode(',',$sam_opt_view);
			$this->db->where_in('dep."ID"',$_arr);
			$this->db->where_in('sample."ID"',$_sam);
		}

		return $this->db->get()->result_array();
	}

	/**
	 * Assign Test to Patient's sample
	 * @param number $psample_id
	 * @param array $sample_tests
     * @return mixed
	 */
	public function assign_sample_test($psample_id, array $sample_tests = array()) {
		if ($psample_id > 0 && count($sample_tests) > 0) {
			$result = $this->get_patient_sample_test($psample_id);
			$assigned_tests = array_column($result, 'sample_test_id');

			$_data  = array();
			foreach ($sample_tests as $test) {
				if ((int)$test > 0 && !in_array($test, $assigned_tests)) {
					$_data[] = array(
						'patient_sample_id' => $psample_id,
						'sample_test_id' => (int)$test,
						'"entryBy"' => $this->user_id,
						'"entryDate"' => $this->timestamp
					);
				}
			}
			$this->db->trans_start();
			//Delete Tests that are not in assinged list
			$this->db->set('status', 0);
			$this->db->set('"modifiedBy"', $this->user_id);
			$this->db->set('"modifiedDate"', $this->timestamp);
			//$this->db->where('status', TRUE);
			$this->db->where('status', 1);
			$this->db->where('patient_sample_id', $psample_id);
			$this->db->where_not_in('sample_test_id', $sample_tests);
			$this->db->update('camlis_patient_sample_tests');

			if (count($_data) > 0) $this->db->insert_batch('camlis_patient_sample_tests', $_data);
			$this->db->trans_complete();
			if ($this->db->trans_status() === TRUE) {
				return 1;
			}
		}

		return 0;
	}

    /**
     * @param $patient_sample_id
     * @param $patient_sample_tests
     * @return mixed
     */
	public function update_patient_sample_test($patient_sample_id, $patient_sample_tests) {
        $patient_sample_tests = collect($patient_sample_tests)->filter(function($item) {
            return $item['patient_test_id'] > 0;
        })->toArray();
        $this->db->trans_start();
        foreach ($patient_sample_tests as $patient_sample_test) {
            $this->db->where('status', 1);
            $this->db->where('patient_sample_id', $patient_sample_id);
            $this->db->where('"ID"', $patient_sample_test['patient_test_id']);
            $this->db->set('"modifiedDate"','"'.$this->timestamp.'"');
            $this->db->set('"modifiedBy"', $this->user_id);
            $this->db->set('is_show', $patient_sample_test['is_show']);
            $this->db->update('camlis_patient_sample_tests');
        }
        $this->db->trans_complete();
        return $this->db->trans_status();
    }

	/**
	 * Check whether Patient's sample already have assigned test
	 */
	public function is_assigned_test($psample_id) {
		if (!$psample_id || (int)$psample_id <= 0) return FALSE;

		$this->db->where('patient_sample_id', $psample_id);
		$this->db->where('status', 1);
		if ($this->db->count_all_results('camlis_patient_sample_tests') > 0) return TRUE;

		return FALSE;
	}

	/**
	 * Get Patient's Sample Detail
	 * @param $psample_id
	 */
	public function get_patient_sample_detail($psample_id) {
		$this->db->select('			
		    psample_detail.patient_sample_id AS patient_sample_id,
		    psample_detail.department_sample_id,
		    psample_detail.sample_description AS sample_description_id,
		    description.description AS sample_description,
		    psample_detail.sample_volume1,
		    psample_detail.sample_volume2, psample_detail."ID" AS psample_detail_id');
		$this->db->where('psample_detail.status', 't');
		$this->db->where('psample_detail.patient_sample_id', $psample_id);
		$this->db->from('camlis_patient_sample_detail AS psample_detail');
		$this->db->join('camlis_std_sample_description AS description', "psample_detail.sample_description = (description.\"ID\")::TEXT AND description.status = 't'", 'left');
		return $this->db->get()->result_array();
	}

	/**
	 * Add patient's sample details
	 * @param $psample_id
	 * @param array $sample_details
     * @return mixed
	 */
	public function set_psample_detail($psample_id, array $sample_details = array()) {
		if ($psample_id > 0 && is_array($sample_details) && count($sample_details) > 0) {
			$_data = array();
			$_department_sample_id = array();
			foreach ($sample_details as $sample_detail) {
				if (isset($sample_detail['department_sample_id']) && $sample_detail['department_sample_id'] > 0) {
                    $_department_sample_id[] = $sample_detail['department_sample_id'];
					$_tmp = array(						
						$psample_id,
						$sample_detail['department_sample_id'],
						isset($sample_detail['sample_description']) ? "'".$sample_detail['sample_description']."'" : "''",
						isset($sample_detail['first_weight']) && $sample_detail['first_weight'] > 0 ? $sample_detail['first_weight'] : "NULL",
						'sample_volume2' => isset($sample_detail['second_weight']) && $sample_detail['second_weight'] > 0 ? $sample_detail['second_weight'] : "NULL",
						$this->user_id,
						"'".$this->timestamp."'",
						$psample_id
					);
					$_data[] = "(".implode(',', $_tmp).")";
				}
			}

			// remove
			// Check if patient_sample_id and department_sample_id exist, update, if not insert
			if (count($_data) > 0) {				
				$sql  = 'INSERT INTO camlis_patient_sample_detail (patient_sample_id, department_sample_id, sample_description, sample_volume1, sample_volume2, "entryBy", "entryDate","uni_index")';
				$sql .= " VALUES ".implode(",", $_data);
				$sql .= ' ON CONFLICT ON CONSTRAINT camlis_patient_sample_detail_patient_sample_id_department_s_key DO UPDATE SET status = \'t\', sample_description = EXCLUDED.sample_description, sample_volume1 = EXCLUDED.sample_volume1, sample_volume2 = EXCLUDED.sample_volume2, "modifiedBy" = '.$this->user_id.', "modifiedDate" = '."'".$this->timestamp."' , uni_index = EXCLUDED.uni_index";
				//$sql .= " ON CONFLICT (\"ID\") DO UPDATE SET status = 't', sample_description = EXCLUDED.sample_description, sample_volume1 = EXCLUDED.sample_volume1, sample_volume2 = EXCLUDED.sample_volume2, \"modifiedBy\" = ".$this->user_id.", \"modifiedDate\" = ".'"'.$this->timestamp.'"';
				
				$this->db->trans_start();				
				$this->db->set('status', 'f');
				$this->db->where('status', 't');
				$this->db->where('patient_sample_id', $psample_id);
				$this->db->where_not_in('department_sample_id', $_department_sample_id);
				$this->db->update('camlis_patient_sample_detail');

				$this->db->query($sql);
				$this->db->trans_complete();
				if ($this->db->trans_status() === TRUE) {
					return 1;
				}
				
			}
		}		
		return 0;
	}

	/**
	 * Update patient's sample details
	 * @param $psample_id
	 * @param array $sample_details
	 */
	public function update_psample_detail($psample_id, array $sample_details = array()) {
		if ($psample_id > 0 && is_array($sample_details) && count($sample_details) > 0) {
			$_data = array();
			foreach ($sample_details as $sample_detail) {
				if (isset($sample_detail['department_id']) && $sample_detail['department_id'] > 0) {
					$_data[] = array(
						'department_id' => $sample_detail['department_id'],
						'sample_description' => isset($sample_detail['sample_description']) ? $sample_detail['sample_description'] : "",
						'sample_volume1' => isset($sample_detail['first_weight']) && $sample_detail['first_weight'] > 0 ? $sample_detail['first_weight'] : NULL,
						'sample_volume2' => isset($sample_detail['second_weight']) && $sample_detail['second_weight'] > 0 ? $sample_detail['second_weight'] : NULL,
						'"modifiedBy"' => $this->user_id,
						'"modifiedDate"' => '"'.$this->timestamp.'"'
					);
				}
			}
			if (count($_data) > 0) {
				$this->db->where('patient_sample_id', $psample_id);
				$this->db->where('status', 't');
				$this->db->update_batch('camlis_patient_sample_detail', $_data, 'department_id');
			}
		}

		return $this->db->affected_rows();
	}

	/**
	 * Set Rejected Test
	 * @param $psample_id
	 * @param array $tests
	 * @return number Affected Rows
	 */
	public function set_rejected_test($psample_id, array $tests) {
        $this->db->trans_start();
        //Clear Rejection
		//$this->db->set('is_rejected', 0);
		$this->db->set('is_rejected', 'f');
		//$this->db->where('status', TRUE);
		$this->db->where('status', 1);
		//$this->db->where('is_rejected', 1);
		$this->db->where('is_rejected', 't');
        $this->db->where('patient_sample_id', $psample_id);
        $this->db->update('camlis_patient_sample_tests');

        //Set Rejected Test
        if (count($tests) > 0) {
			//$this->db->set('is_rejected', 1);
			$this->db->set('is_rejected', 't');
            $this->db->set('"modifiedBy"', $this->user_id);
            $this->db->set('"modifiedDate"', '"'.$this->timestamp.'"');
			//$this->db->where('status', TRUE);
			$this->db->where('status', 1);
            $this->db->where('patient_sample_id', $psample_id);
            $this->db->where_in('"ID"', $tests);
            $this->db->update('camlis_patient_sample_tests');
        }
        $this->db->trans_complete();
        if ($this->db->trans_status() === TRUE) return 1;
		return 0;
	}

	/**
	 * Update patient's sample printed info
	 */
	public function update_printed_info($psample_id) {
		//$this->db->set('is_printed', 1);
		$this->db->set('is_printed', 't');
		$this->db->set('"printedBy"', $this->user_id);
		$this->db->set('"printedDate"', '"'.$this->timestamp.'"');
		$this->db->set('"modifiedBy"', $this->user_id);
		$this->db->set('"modifiedDate"', '"'.$this->timestamp.'"');
		//$this->db->where('status', TRUE);
		$this->db->where('status', 1);
		$this->db->where('"ID"', $psample_id);
		$this->db->where('"labID"', $this->laboratory_id);
		$this->db->where('"printedDate" IS NULL', null, false);
		$this->db->update('camlis_patient_sample');

		return $this->db->affected_rows();
	}

    /**
     * Set Sample Test Ref. Range and Unit sign
     * @param $patient_sample_id
     * @param $data
     */
	public function set_patient_sample_test_info($patient_sample_id, $data) {
	    $this->db->where('patient_sample_id', $patient_sample_id);
		//$this->db->where('status', TRUE);
		$this->db->where('status', 1);
	    $this->db->group_start();
		$this->db->where('unit_sign IS NULL', NULL, true);		
	    $this->db->where('ref_range_min_value IS NULL', NULL, true);
	    $this->db->where('ref_range_max_value IS NULL', NULL, true);
	    $this->db->where('ref_range_sign IS NULL', NULL, true);
	    $this->db->group_end();
        return $this->db->update_batch('camlis_patient_sample_tests', $data, '"ID"') > 0;
    }

    /**
     * Add patient sample payment
     * @param $data
     */
    public function add_patient_sample_test_payment($data) {
        $this->db->insert_batch('camlis_patient_sample_test_payment', $data);
        return $this->db->affected_rows();
    }

    /**
     * Delete patient sample payment
     * @param $patient_sample_id
     * @return mixed
     */
    public function delete_patient_sample_test_payment($patient_sample_id) {
	    $this->db->where('patient_sample_id', $patient_sample_id);
	    $this->db->delete('camlis_patient_sample_test_payment');
	    return $this->db->affected_rows();
    }

    /**
     * Get patient sample payment
     * @param $patient_sample_id
     * @return mixed
     */
    public function get_patient_sample_test_payment($patient_sample_id) {
        $patient_sample_id = (array)$patient_sample_id;
        if (count($patient_sample_id) == 0) return NULL;

        $this->db->where_in('patient_sample_id', $patient_sample_id);
        return $this->db->get('camlis_patient_sample_test_payment')->result_array();
    }

    /**
     * Store reference of previous patient sample
     * @param $data
     */
    public function add_ref_patient_sample($data) {
        $this->db->set($data);
        $this->db->set('created_at', $this->timestamp);
        $this->db->set('created_by', $this->user_id);
        $this->db->insert('camlis_ref_patient_sample');
        return $this->db->insert_id();
    }

	/**
	 * View patient's sample
	 */
	public function view_all_patient_sample($data) {
        $table		= "camlis_patient_sample";
        $primaryKey	= "ID"; 
        $can_add_psample_result   = $this->aauth->is_allowed('add_psample_result');
        $can_print_psample_result = $this->aauth->is_allowed('print_psample_result');
        $can_edit_psample         = $this->aauth->is_allowed('edit_psample');
        $can_delete_psample       = $this->aauth->is_allowed('delete_psample');
        $columns	= array(
            array(
                'db'		=> 'psample.ID',
                'dt'		=> 'psample_id',
                'field'		=> 'ID'
            ),
            array(
                'db'		=> 'patient.patient_code',
                'dt'		=> 'patient_code',
                'field'		=> 'patient_code'
            ),
            array(
                'db'		=> 'patient.patient_name',
                'dt'		=> 'patient_name',
                'field'		=> 'patient_name'
            ),
            array(
                'db'		=> 'psample.sample_number',
                'dt'		=> 'sample_number',
                'field'		=> 'sample_number'
            ),
            array(
                'db'		=> 'psample.collected_time',
                'dt'		=> 'collected_time',
                'field'		=> 'collected_time'
            ),
            array(
                'db'		=> 'psample.received_time',
                'dt'		=> 'received_time',
                'field'		=> 'received_time'
			),
			array(
                'db'		=> 'psample.verify',
                'dt'		=> 'verify',
                'field'		=> 'verify'
			),
			array(
                'db'		=> 'psample.verify_by',
                'dt'		=> 'verify_by',
                'field'		=> 'verify_by'
            ),
            array(
                'db'		=> 'sample_source.source_name',
                'dt'		=> 'sample_source',
                'field'		=> 'source_name'
			),
			array(
                'db'		=> 'psample.micro',
                'dt'		=> 'micro',
                'field'		=> 'micro',
				'formatter'	=> function($d, $row) {
                    return $d >=1 ? "<b class='text-red'>M</b>" : "";
				}
            ),
            array(
                'db'		=> 'psample.collected_date',
                'dt'		=> 'collected_date',
                'field'		=> 'collected_date'
            ),
            array(
                'db'		=> 'psample.received_date',
                'dt'		=> 'received_date',
                'field'		=> 'received_date'
			),
			array(
                'db'		=> 'psample.printedDate',
                'dt'		=> 'printedDate',
                'field'		=> 'printedDate',
				'formatter' => function( $d, $row ) {
					return ($d!=null) ? date( 'd-m-Y H:i', strtotime($d)):'';
				}
            ),
            array(
                'db'		=> 'psample.progress_status',
                'dt'		=> 'psample_status',
                'field'		=> 'progress_status',
                'formatter'	=> function($d, $row) {
                    $colors	= [PSAMPLE_PENDING => PSAMPLE_PENDING_COLOR, PSAMPLE_PROGRESSING => PSAMPLE_PROGRESSING_COLOR, PSAMPLE_COMPLETE => PSAMPLE_COMPLETE_COLOR, PSAMPLE_PRINTED => PSAMPLE_PRINTED_COLOR, PSAMPLE_REJECTED => PSAMPLE_REJECTED_COLOR];
					$format = "<span style='display:none;' data-row=".strlen(!empty($row[7]) ? $row[7] : '' ).">".$d."</span><div style='width:100%; height:20px; border:1px solid #e3e3e3; background:".(isset($colors[$d]) ? $colors[$d] : '')."'>";
					$format.= (strlen(!empty($row[7]) ? $row[7] : '' ) > 0) ? "<center style= 'color: white;'><i class='fa fa-check'></i></center>" : "";
					$format.= "</div>";
					return $format;
                }
            ),
            array(
                'db'		=> 'psample."ID"',
                'dt'		=> 'action',
                'field'		=> 'ID',
                'formatter'	=> function($d, $row) use($can_add_psample_result, $can_delete_psample, $can_edit_psample, $can_print_psample_result) {
					$formatted  = "";
					
                    if ($can_add_psample_result) $formatted .= "<a href='".$this->app_language->site_url("sample/edit/".$d."/rs")."' class='text-blue hint--left hint--info add-result pointer' data-hint='"._t('view_sample.add_result')."'><i class='fa fa-wpforms'></i></a>";
                    if ($can_print_psample_result) $formatted .= "&nbsp;|&nbsp;<a href='#' class='text-green hint--left hint--success preview-sample pointer' data-hint='"._t('global.print')."'><i class='fa fa-print'></i></a>";
                    if ($can_edit_psample) $formatted .= "&nbsp;|&nbsp;<a href='".$this->app_language->site_url("sample/edit/".$d)."' class='hint--left hint--info pointer' data-hint='"._t('global.edit')."'><i class='fa fa-edit'></i></a>";
                    if ($can_delete_psample) $formatted .= " | <a href='' class='text-red hint--left hint--error pointer delete-sample' data-hint='"._t('global.remove')."'><i class='fa fa-trash'></i></a>";
                    return $formatted;
                }
            ),
			array(
                
				'db'		=> 'psample.sample_number',
                'dt'		=> 'sam_number',
                'field'		=> 'sample_number',
                'formatter'	=> function($d, $row) use($can_edit_psample) {
					$formatted  = "";
                    if ($can_edit_psample) $formatted .= "<input type='checkbox' data-per='".$can_edit_psample."' name='psample_ids[]' value='".$d."' />";                    
                    return $formatted;
                }
            )
        );

		//$patientWheres ="";
		$extraWhere	= " psample.status = 1 AND psample.\"labID\" = $this->laboratory_id";

		//return $data['reqData'];

		if(!isset($data['reqData']['collected_date']) || $data['reqData']['collected_date'] == ""){
			if((isset($data['reqData']['search']['value']) && empty($data['reqData']['search']['value'])) && ((!isset($data['reqData']['sample_progress'])) || (isset($data['reqData']['sample_progress']) && $data['reqData']['sample_progress'] ==-1)) ) {
				$extraWhere .= " AND psample.collected_date >= '" . date('Y-m-d', strtotime('-30 days')) . "' ";
				//$patientWheres .= " AND ps.collected_date >= '" . date('Y-m-d', strtotime('-7 days')) . "' ";
			}
		}
		//else{
			if (isset($data['reqData']['collected_date']) ) {
				if ($data['reqData']['collected_date'] !== "") {
					$extraWhere .= " AND psample.collected_date = '" . $data["reqData"]["collected_date"] . "' ";
					//$patientWheres .= " AND ps.collected_date = '" . $data["reqData"]["collected_date"] . "' ";
				}
			}
		//}

		//return $data['reqData']['search']['value'];

        $joinQuery  = " FROM $table AS psample 
        				INNER JOIN v_camlis_all_patients AS patient ON psample.patient_id = patient.pid AND psample.\"labID\" = patient.lab_id
                        INNER JOIN camlis_lab_sample_source AS sample_source ON psample.sample_source_id = sample_source.\"ID\"";



		/*if (isset($data['reqData']['collected_date']) ) {
			if($data['reqData']['collected_date'] !== ""){
				$extraWhere .= " AND psample.collected_date = '".$data["reqData"]["collected_date"]."' ";
			}
        }*/

        if (isset($data['reqData']['is_urgent']) && $data['reqData']['is_urgent'] == 1) {
            $extraWhere .= " AND psample.is_urgent = 1 AND psample.progress_status IN (1, 2)";
        } else if (isset($data['reqData']['sample_progress']) && $data['reqData']['sample_progress'] > 0) {
            $extraWhere .= " AND psample.progress_status = ".$data['reqData']['sample_progress'];
        }
        $db_config		= $this->load->database('default', TRUE);
        $sql_details	= array(
            'user'	=> $db_config->username,
            'pass'	=> $db_config->password,
            'db'	=> $db_config->database,
            'port'	=> $db_config->port,
            'host'	=> $db_config->hostname
        );

        //return $extraWhere;

		$result = DataTable::simple( $data['reqData'], $sql_details, $table, $primaryKey, $columns, $joinQuery, $extraWhere);		
        for ($i=0; $i < count($result['data']); $i++) {
            $result['data'][$i]['number'] = $i + 1;
        }
        return $result;
    }
	public function get_test_sample(){
		/*
		$sql = "select * from camlis_patient_sample limit 10";
		$query = $this->db->query($sql);
		foreach($query->result_array() as $row){
			$results[] = $row;
		}
		return $results;
		
		//$query = $this->db->get('camlis_patient_sample', 0, 4);
		*/
		$this->db->select('*');
		$this->db->from('camlis_patient_sample');
		//$this->db->where('ID', 1);
		$this->db->limit(10);
		return $this->db->get()->result_array();
		
		/*
		
		foreach ($query->result() as $row)
		{
				$result[] = $row->for_research;
		}
		return $result;
		
		$sql = 'SELECT * FROM camlis_patient_sample';
		return $this->db->get('camlis_patient_sample_test_payment')->result_array();
		
		$query = $this->db->query($sql);
		
		foreach ($query->result_array() as $row)
		{
			$result[] = $row['for_research'];
		}
		return $result;*/
	}	
	public function is_tested_covid($psample_id){
		$sql = " SELECT *
			FROM camlis_patient_sample AS psample			
			INNER JOIN camlis_patient_sample_tests AS psample_test ON psample.\"ID\" = psample_test.patient_sample_id AND psample_test.status = 1 			
			INNER JOIN camlis_std_sample_test AS std_sampletest ON psample_test.sample_test_id = std_sampletest.\"ID\"
			WHERE psample.status = 1 
				AND psample.\"ID\" = ".$psample_id."
					
			";
			//AND std_sampletest.test_id = 419 
	return $this->db->query($sql)->result_array();
	}
	// added 01-05-2021
	// For Line List

	public function assign_single_sample_test($psample_id, array $sample_tests = array()) {
		if ($psample_id > 0 && count($sample_tests) > 0) {
			$result = $this->get_patient_sample_test($psample_id);
			$assigned_tests = array_column($result, 'sample_test_id');

			$_data  = array();
			foreach ($sample_tests as $test) {
				if ((int)$test > 0 && !in_array($test, $assigned_tests)) {
					$_data[] = array(
						'patient_sample_id' => $psample_id,
						'sample_test_id' => (int)$test,
						'"entryBy"' => $this->user_id,
						'"entryDate"' => $this->timestamp
					);
				}
			}
			
			$this->db->set($_data[0]);
			$this->db->insert('camlis_patient_sample_tests');
			return $this->db->insert_id();
		}

		return 0;
	}
	/**
	 * 20-05-2021
	 */
	public function get_test_sar_cov2($psample_id){
		$test_ids = array(497,495,479,505,509,516);
		$this->db->select('
			ptest."ID" AS patient_test_id,
			ptest.patient_sample_id,
			ptest.sample_test_id, 
			ptest.is_rejected,
			ptest.ref_range_min_value,
			ptest.ref_range_max_value,
			ptest.ref_range_sign,
			ptest.is_show,
			presult."ID" AS ptest_result_id,			
			presult.result,
			presult.type,
			presult.quantity_id,
			presult.contaminant,
			presult.test_date,
			presult.number_update,
			presult.performer_id,
			presult.machine_name
		');
		$this->db->from('camlis_patient_sample_tests AS ptest');
		$this->db->join('camlis_ptest_result AS presult', 'ptest."ID" = presult.patient_test_id AND presult.status = 1', 'left');
		$this->db->where('ptest.status', 1);
		$this->db->where('ptest.is_rejected', false);
		$this->db->where_in('ptest.sample_test_id', $test_ids);
        $this->db->where('ptest.patient_sample_id', $psample_id);
		return $this->db->get()->result_array();
	}

}
