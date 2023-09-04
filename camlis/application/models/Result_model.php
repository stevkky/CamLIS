<?php
defined('BASEPATH') OR die("Access denied!");
class Result_model extends MY_Model {
	public function __construct() {
		parent::__construct();
		$this->load->database();
	}

	/**
	 * Add Result of Patient Test which value is text/amount
	 */
	public function set_ptest_text_result($patient_sample_id, $data) {
		$result        = $this->db->get_where('camlis_ptest_result', array('status' => 1, 'type' => 0, 'patient_sample_id' => $patient_sample_id))->result_array();	
		$prev_ptest    = array_column($result, 'patient_test_id');
		$new_result    = array();
		$update_result = array();
		if (count($data) > 0) {
			foreach ($data as $row) {
				if (in_array($row['patient_test_id'], $prev_ptest)) {
					$row['modifiedBy']   = $this->user_id;
					$row['modifiedDate'] = $this->timestamp;
					$update_result[]     = $row;
				}
				else {
					$row['entryBy']   = $this->user_id;
					$row['entryDate'] = $this->timestamp;
					$new_result[]     = $row;
				}
			}

			if (count($new_result) > 0) $this->db->insert_batch('camlis_ptest_result', $new_result);
			if (count($update_result) > 0) {
				$this->db->where('status', 1);
				$this->db->where('patient_sample_id', $patient_sample_id);
				$this->db->update_batch('camlis_ptest_result', $update_result, 'patient_test_id');
			}			
			return $this->db->affected_rows() > 0;
		}		
		return FALSE;
	}

	public function set_ptest_organism_antibiotic_result($patient_sample_id, $data) {		
		//
		$result     = $this->db->get_where('camlis_ptest_result', array('status' => 1, 'type' => 1, 'patient_sample_id' => $patient_sample_id))->result_array();
		//
	//	$patient_id = $this->db->get_where('camlis_patient_sample', array('"ID"' => $patient_sample_id))->row()->patient_id;
	//	$patient_info = $this->db->get_where('patient_v', array('pid' => $patient_id))->row();
		 
		$prev_ptest = array();
		foreach ($result as $row) {
			$key = $row['patient_test_id'].'-'.trim($row['result']);
			$prev_ptest[$key] = $row['ID'];
		}
		if (count($data) > 0) {
			$_antibiotic_result = array();
			foreach ($data as $item) {
				$key 			= trim($item['patient_test_id']).'-'.trim($item['result']);
				$result_id 		= 0;
				//Organism
				if (isset($prev_ptest[$key])) {
					$result_id 	= $prev_ptest[$key]; // presult_id
					$_values 	= elements(array('performer_id', 'machine_name', 'test_date', 'quantity_id', 'contaminant'), $item);
					$this->db->set($_values);
					$this->db->set('"modifiedBy"', $this->user_id);
					$this->db->set('"modifiedDate"', "'".$this->timestamp."'");
					$this->db->where(array('status' => 1, '"ID"' => $result_id));
					$this->db->update('camlis_ptest_result'); 
					unset($prev_ptest[$key]);
				} else {
					$_values = elements(array('patient_sample_id', 'patient_test_id', 'performer_id','machine_name', 'test_date', 'result', 'type', 'quantity_id', 'contaminant'), $item);
					$this->db->set($_values);
					$this->db->set('"entryBy"', $this->user_id);
					$this->db->set('"entryDate"', "'".$this->timestamp."'");
					$this->db->insert('camlis_ptest_result');
					$result_id = $this->db->insert_id(); 
					
					/* vuthy sin
					 * 2017-01-18
					 * @target: while add result meet the create urgent send mail
					 * @@param: resutl id
					 * return data row object
					*/
					// query get organism map criteria send mail
					$result_value = $this->db->query("select 
														sto.\"ID\",
														sto.sample_test_id, 
														om.organism_id,
														om.organism_map_name,
														om.organism_map_kh,
														om.description
														
													from camlis_std_test_organism as sto
													inner join camlis_organism_map om on om.organism_id = sto.organism_id
													where sto.\"ID\" = ?
													and om.type = 1",
									array($_values["result"]))->row(); 
					 // is checking have data in mapping
					if(isset($result_value->organism_id) && $result_value->organism_id > 0){
						// mail every meet the disseases
						$this->load->model('email_model'); 
						$obj				= new stdClass();
						$obj->patient_id 	= $patient_id; 
						$obj->sex 			= $patient_info->sex;
						$obj->_year 		= $patient_info->_year;
						$obj->diseases 		= $result_value->organism_id.' : '.$result_value->organism_map_name.' - '.$result_value->organism_map_kh; 
						 
						// insert tracking email
						$this->db->set('mail', json_encode($obj));
						$this->db->set('date', date('Y-m-d'));						
						$this->db->set('status', 't');
						$this->db->insert('daily_mail'); 
						$obj->inscress_id = $this->db->insert_id(); 
						// send email
						$this->email_model->email_urgent_test($obj); 
						// end
					}
				}
					 
				//Antibiotic
				if ($result_id > 0) {
					$_antibiotic_id 		= array_column($item['antibiotic'], 'antibiotic_id');
					foreach ($item['antibiotic'] as $value) {
						if (isset($value['antibiotic_id']) && $value['antibiotic_id'] > 0 && isset($value['sensitivity']) && $value['sensitivity'] > 0) {
							$disc_diffusion 		= isset($value['disc_diffusion']) ? "'".$value['disc_diffusion']."'" : 'NULL';
							//$test_zone 		= (isset($value['test_zone'])) ? "'".$value['test_zone']."'" : 'NULL';
							$test_zone 				= 'NULL';
							if(!empty($value['test_zone'])){
								if(isset($value['test_zone']) && strlen($value['test_zone']) > 0){
									$test_zone 		= "'".$value['test_zone']."'";
								}
							}

							$invisible 				= isset($value['invisible']) ? "'".$value['invisible']."'" : false;
							$_tmp 					= array($result_id, $value['antibiotic_id'], $value['sensitivity'], $disc_diffusion, $test_zone, $invisible, $this->user_id, "'".$this->timestamp."'");
							$_antibiotic_result[] 	= "(".implode(',', $_tmp).")";
						}
					}
					//Delete antibiotic result that ar not in list
					if (count($_antibiotic_id) > 0) {
						$this->db->set(array('status' => false, '"modifiedBy"' => $this->user_id, '"modifiedDate"' => "'".$this->timestamp."'"));
						$this->db->where('status', true);
						$this->db->where('presult_id', $result_id);
						$this->db->where_in('antibiotic_id', $_antibiotic_id);
						$this->db->update('camlis_ptest_result_antibiotic');
					}
				}
			}

			//Add antibiotic result
			if (count($_antibiotic_result) > 0) {				
				$sql  = "INSERT INTO camlis_ptest_result_antibiotic (presult_id, antibiotic_id, sensitivity, disc_diffusion, test_zone, invisible, \"entryBy\", \"entryDate\")";
				$sql .= " VALUES ".implode(",", $_antibiotic_result);
				$sql .= " ON CONFLICT (\"ID\") DO UPDATE SET status = true, sensitivity = EXCLUDED.sensitivity, disc_diffusion = EXCLUDED.disc_diffusion, test_zone = EXCLUDED.test_zone, invisible = EXCLUDED.invisible, \"modifiedBy\" = $this->user_id, \"modifiedDate\" = '".$this->timestamp."'";				
				$this->db->query($sql);
			}

			//Delete result that are not in list
			$prev_result = array_values($prev_ptest);
			if (count($prev_result) > 0) {
                $this->db->set(array('status' => 0, '"modifiedBy"' => $this->user_id, '"modifiedDate"' => "'".$this->timestamp."'"));
                $this->db->where('status', 1);
				$this->db->where('type', 1);
				$this->db->where_in('"ID"', $prev_result);
				$this->db->update('camlis_ptest_result');

                $this->db->set(array('status' => false, '"modifiedBy"' => $this->user_id, '"modifiedDate"' => "'".$this->timestamp."'"));
                $this->db->where('status', true);
				$this->db->where_in('presult_id', $prev_result);
				$this->db->update('camlis_ptest_result_antibiotic');
			}
			return $this->db->affected_rows() > 0;
		}
		return FALSE;
	}

	/**
	 * Delete Result
	 * @param integer $patient_sample_id
	 * @param  integer $ptest_ids Patient's Sample_test ID
	 * @return integer Number of deleted rows
	 */
	public function delete_ptest_result($patient_sample_id, $ptest_ids) {
		if (count($ptest_ids) == 0) return 0;
		/*
		$this->db->set('status', FALSE);
		$this->db->set('"modifiedBy"', $this->user_id);
		$this->db->set('"modifiedDate"', $this->timestamp);
		$this->db->where('status', TRUE);
		$this->db->where('patient_sample_id', $patient_sample_id);
		$this->db->where_not_in('patient_test_id', $ptest_ids);
		$this->db->update('camlis_ptest_result');		
		return $this->db->affected_rows() > 0;
		*/

		$this->db->set("status", 0);
		$this->db->set('"modifiedBy"', $this->user_id);
		$this->db->set('"modifiedDate"', "'".$this->timestamp."'");
		$this->db->where("status", 1);
		$this->db->where("patient_sample_id", $patient_sample_id);
		$this->db->where_not_in("patient_test_id", $ptest_ids);
		$this->db->update('camlis_ptest_result');		
		return $this->db->affected_rows() > 0;
	}

    /**
     * Delete Patient sample result
     * @param $patient_sample_id
     * @param bool $patient_id
     */
	public function delete_patient_sample_result($patient_sample_id, $patient_id = FALSE) {
	    /*$sql = "UPDATE camlis_ptest_result AS presult
	            INNER JOIN camlis_patient_sample AS psample ON presult.patient_sample_id = psample.\"ID\"
	            SET presult.status = FALSE,
	                presult.modifiedBy = ?,
	                presult.modifiedDate = ?
	            WHERE presult.status = TRUE
					  AND psample.\"labID\" = ?";
		*/
		$sql = "UPDATE camlis_ptest_result
				SET status = 0,
					\"modifiedBy\" = ?,
	                \"modifiedDate\" = ?
				FROM ( SELECT \"ID\", \"labID\", patient_id FROM camlis_patient_sample ) psample
				WHERE patient_sample_id = psample.\"ID\" AND camlis_ptest_result.status = 1 AND psample.\"labID\" = ?";

        !$patient_sample_id OR $sql .= " AND psample.\"ID\" = ".$patient_sample_id;
        !$patient_id OR $sql .= " AND psample.patient_id='".$patient_id."'";
        return $this->db->query($sql, [$this->user_id, $this->timestamp, $this->laboratory_id]) > 0;
    }

    /**
     * Delete Patient sample result antibiotic
     * @param $patient_sample_id
     * @param bool $patient_id
     */
	public function delete_patient_sample_result_antibiotic($patient_sample_id, $patient_id = FALSE) {
		/*
		$sql = "UPDATE camlis_ptest_result_antibiotic AS rantibiotic

                INNER JOIN camlis_ptest_result AS presult ON rantibiotic.presult_id = presult.\"ID\"
	            INNER JOIN camlis_patient_sample AS psample ON presult.patient_sample_id = psample.\"ID\"
	            SET rantibiotic.status = 'f',
	                rantibiotic.modifiedBy = ?,
	                rantibiotic.modifiedDate = ?
	            WHERE rantibiotic.status = 't'
					  AND psample.\"labID\" = ?";
		*/

		$sql = "UPDATE camlis_ptest_result_antibiotic
				SET status = 'f',
	                \"modifiedBy\" = ?,
	                \"modifiedDate\" = ?
				FROM (
					SELECT \"ID\" , patient_sample_id FROM camlis_ptest_result
				) presult,
				( 
					SELECT \"ID\", \"labID\" , patient_id FROM camlis_patient_sample
				) psample
				WHERE presult_id = presult.\"ID\" AND presult.patient_sample_id = psample.\"ID\" AND status = 't' AND psample.\"labID\" = ?";

        !$patient_sample_id OR $sql .= " AND psample.\"ID\" = ".$patient_sample_id;
        !$patient_id OR $sql .= " AND psample.patient_id='".$patient_id."'";
        return $this->db->query($sql, [$this->user_id,$this->timestamp, $this->laboratory_id]) > 0;
    }

	/**
	 * Fetch All Test Result
	 * @param $patient_sample_id
	 */
	public function get_patient_sample_result($patient_sample_id) {
		$this->db->select('
			presult."ID" AS ptest_result_id,
			presult.patient_test_id,
			ptest.sample_test_id,
			sample_test.field_type,
			sample_test.is_heading,
			ptest.is_rejected,
			presult.result,
			test.test_name,
			presult.type,
			presult.quantity_id,
			presult.contaminant,
			qty.quantity,
			presult.test_date,
			presult.number_update,
			_tmp1.first_test_date,
			presult.performer_id,
			presult.machine_name,
			(CASE organism.organism_value
				WHEN 1 THEN CONCAT(organism.organism_name, \' Positive\')
				WHEN 2 THEN CONCAT(organism.organism_name, \' Negative\')
				ELSE organism.organism_name
			END) AS organism_name,
			r_antibiotic.antibiotic_id,
			antibiotic.antibiotic_name,
			r_antibiotic.sensitivity,
			r_antibiotic.disc_diffusion,
			r_antibiotic."ID" as pra_id,
			r_antibiotic.test_zone,
			r_antibiotic.invisible,
			NULLIF(_tmp.child_count, 0) AS child_count
		');
		$this->db->from("camlis_ptest_result AS presult");
		$this->db->join("camlis_patient_sample_tests AS ptest", "presult.patient_test_id = ptest.\"ID\"", "inner");
		$this->db->join("camlis_std_sample_test AS sample_test", "sample_test.\"ID\" = ptest.sample_test_id", "inner");
        $this->db->join("camlis_std_test AS test", "test.\"ID\" = sample_test.test_id", "inner");
		$this->db->join("camlis_ptest_result_antibiotic AS r_antibiotic", "presult.\"ID\" = r_antibiotic.presult_id AND r_antibiotic.status = 't'", "left");
		$this->db->join("camlis_std_antibiotic AS antibiotic", "r_antibiotic.antibiotic_id = antibiotic.\"ID\" AND antibiotic.status = 't'", "left");
		$this->db->join("camlis_std_test_organism AS t_organism", "presult.result = (t_organism.\"ID\")::TEXT AND t_organism.status = 't' AND presult.type = 1", "left");
		$this->db->join("camlis_std_organism AS organism", "organism.\"ID\" = t_organism.organism_id AND organism.status = 't'", "left");
		$this->db->join("camlis_std_organism_quantity AS qty", "qty.\"ID\" = presult.quantity_id AND qty.status = 't'", "left");
		$this->db->join('(SELECT "testPID", count(*) AS child_count FROM camlis_std_sample_test WHERE status = TRUE GROUP BY "testPID") _tmp', 'sample_test."ID" = _tmp."testPID"', 'left');
        $this->db->join('(
		    SELECT test_result.patient_sample_id, sample_test.department_sample_id, MIN(test_result.test_date) AS first_test_date
		    FROM camlis_ptest_result AS test_result
		    INNER JOIN camlis_patient_sample_tests AS psample_test ON test_result.patient_test_id = psample_test."ID"
		    INNER JOIN camlis_std_sample_test AS sample_test ON sample_test."ID" = psample_test.sample_test_id
		    WHERE test_result.status = 1 AND psample_test.status = 1 AND sample_test.status = \'t\' AND test_result.test_date IS NOT NULL AND psample_test.patient_sample_id = '.$patient_sample_id.'
		    GROUP BY test_result.patient_sample_id, sample_test.department_sample_id
		) _tmp1', 'presult.patient_sample_id = _tmp1.patient_sample_id AND sample_test.department_sample_id = _tmp1.department_sample_id', 'left');
		$this->db->where('presult.status', 1);
		$this->db->where('ptest.status', 1);
		$this->db->where('sample_test.status', 't');
		$this->db->where('ptest.patient_sample_id', $patient_sample_id);
		$this->db->order_by('sample_test.order', 'asc');
		$this->db->order_by('organism.order', 'asc');
		$this->db->order_by('antibiotic.order', 'asc');

		return $this->db->get()->result_array();
	}

    /**
     * Get User that work on the result
     * @param $patient_sample_id
     * @return mixed
     */
	public function get_patient_sample_result_user($patient_sample_id) {
	    $this->db->select('
	        department_sample.department_id,
			department_sample.sample_id,
			string_agg ( DISTINCT entry_user.fullname, \', \' ) AS entry_users,
			string_agg ( DISTINCT (presult."entryDate")::TEXT, \', \'  ) AS entry_dates,
			string_agg ( DISTINCT modify_user.fullname, \', \'  ) AS modified_users,
			string_agg ( DISTINCT (presult."modifiedDate")::TEXT, \', \'  ) AS modified_dates 
	    ');
	    $this->db->from('camlis_ptest_result AS presult');
	    $this->db->join('camlis_patient_sample_tests AS ptest', 'presult.patient_test_id = ptest."ID"', 'inner');
	    $this->db->join('camlis_std_sample_test AS sample_test', 'ptest.sample_test_id = sample_test."ID"', 'inner');
	    $this->db->join('camlis_std_department_sample AS department_sample', 'sample_test.department_sample_id = department_sample."ID"', 'inner');
	    $this->db->join('camlis_aauth_users AS entry_user', 'presult."entryBy" = entry_user.id', 'left');
	    $this->db->join('camlis_aauth_users AS modify_user', 'presult."entryBy" = modify_user.id', 'left');
	    $this->db->where('presult.status', 1);
	    $this->db->where('presult.patient_sample_id', $patient_sample_id);
	    $this->db->group_by('department_sample.department_id');
	    $this->db->group_by('department_sample.sample_id');
	    return $this->db->get()->result_array();
	}		
}