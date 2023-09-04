<?php
defined('BASEPATH') OR die('Access Denied.');

class REST_model extends MY_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Get Bacteriology data
     * @param $start_date
     * @param $end_date
     * @return mixed
     */
    public function get_bacteriology_data($start_date, $end_date) {
        $sql = "
            SELECT 
                 lab.lab_code AS laboratory_code,
                 lab.name_en AS laboratory_name_en,
                 lab.name_kh AS laboratory_name_kh,
                 psample.sample_number,
                 CONCAT(psample.collected_date, ' ', psample.collected_time) AS collection_date,
                 CONCAT(psample.received_date, ' ', psample.received_time) AS received_date,
                 psample.admission_date,
                 psample.clinical_history AS diagnosis,
                 psample.reject_comment,
                 (CASE patient.sex
                    WHEN 1 THEN 'M'
                    WHEN 2 THEN 'F'
                    ELSE NULL
                 END) AS sex,
                 patient.dob,
                 department.department_name,
                 sample.sample_name,
                 test.test_name,
                 ptest.is_rejected AS is_test_rejected,
                 presult.test_date,
                 organism.organism_name,
                 antibiotic.antibiotic_name,
                 (CASE presult_antibiotic.sensitivity
                    WHEN 1 THEN 'SENSITIVE'
                    WHEN 2 THEN 'RESISTANT'
                    WHEN 3 THEN 'INTERMEDIATE'
                    ELSE NULL
                 END) AS sensitivity,
                 result_comment.result_comment
            FROM camlis_patient_sample AS psample
            INNER JOIN camlis_laboratory AS lab ON psample.\"labID\" = lab.\"labID\"
            INNER JOIN v_camlis_all_patients AS patient ON psample.patient_id = patient.pid
            
            INNER JOIN camlis_patient_sample_tests AS ptest ON psample.\"ID\" = ptest.patient_sample_id
            INNER JOIN camlis_std_sample_test AS sample_test ON ptest.sample_test_id = sample_test.\"ID\"
            INNER JOIN camlis_std_test AS test ON sample_test.test_id = test.\"ID\"
            INNER JOIN camlis_std_department_sample AS department_sample ON sample_test.department_sample_id = department_sample.\"ID\"
            INNER JOIN camlis_std_department AS department ON department_sample.department_id = department.\"ID\"
            INNER JOIN camlis_std_sample AS sample ON department_sample.sample_id = sample.\"ID\"
            
            INNER JOIN camlis_ptest_result AS presult ON ptest.\"ID\" = presult.patient_test_id
            INNER JOIN camlis_std_organism AS organism ON presult.result = organism.\"ID\" AND presult.type = 1
            
            INNER JOIN camlis_ptest_result_antibiotic AS presult_antibiotic ON presult.\"ID\" = presult_antibiotic.presult_id AND presult_antibiotic.status = TRUE
            INNER JOIN camlis_std_antibiotic AS antibiotic ON presult_antibiotic.antibiotic_id = antibiotic.\"ID\"
            
            LEFT JOIN camlis_result_comment AS result_comment ON psample.\"ID\" = result_comment.patient_sample_id AND department_sample.\"ID\" = result_comment.department_sample_id
            WHERE psample.status = TRUE
                  AND ptest.status = TRUE
                  AND presult.status = TRUE
                  AND DATE_FORMAT(CONCAT(psample.received_date, ' ', psample.received_time), '%Y-%m-%d %H:%i') BETWEEN ? AND ?";

        return $this->db->query($sql, [$start_date, $end_date])->result_array();
    }
}