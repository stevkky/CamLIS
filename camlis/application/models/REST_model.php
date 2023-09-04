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
    public function get_bacteriology_data($labid,$patientid,$labnumber) {
		$tblallpatients="(
				SELECT pmrs_patient.pid AS pid,psample.\"labID\" AS lab_id,pmrs_patient.pid AS patient_id,pmrs_patient.pid AS patient_code,pmrs_patient.name AS patient_name,(CASE pmrs_patient.sex WHEN 'M' THEN 1 WHEN 'F' THEN 2 ELSE 0 END) AS sex,pmrs_patient.dob AS dob,pmrs_patient.phone AS phone,pmrs_patient.province AS province,pmrs_patient.district AS district,pmrs_patient.commune AS commune,pmrs_patient.village AS village
						FROM (camlis_patient_sample psample
						JOIN camlis_pmrs_patient pmrs_patient ON(((psample.patient_id = pmrs_patient.pid) AND (pmrs_patient.status = 1))))
						WHERE (psample.status = 1 and psample.\"labID\"=$labid and pmrs_patient.pid='$patientid')
						GROUP BY psample.patient_id,psample.\"labID\" , pmrs_patient.pid
						UNION ALL
						SELECT outside_patient.pid::text AS pid,
						
						outside_patient.lab_id AS lab_id,outside_patient.patient_id AS patient_id,outside_patient.patient_code AS patient_code,outside_patient.patient_name AS patient_name,outside_patient.sex AS sex,outside_patient.dob AS dob,outside_patient.phone AS phone,outside_patient.province AS province,outside_patient.district AS district,outside_patient.commune AS commune,outside_patient.village AS village
						FROM camlis_outside_patient outside_patient
						WHERE (outside_patient.status = 1 and outside_patient.lab_id=$labid and outside_patient.patient_code='$patientid'))";
        /*	psd.sample_volume1,
			psd.sample_volume2,
			INNER JOIN camlis_patient_sample_detail as psd ON psample.ID=psd.patient_sample_id
		*/
		$sql = "
            SELECT
                 lab.lab_code AS laboratory_code,
				 patient.pid,
				 (case when patient.phone != '' then patient.phone else 'NA' end) as tel,				 
                 psample.sample_number,
				 to_char (psample.collected_date, 'dd-mm-YYYY' ) AS collecteddate,
				 to_char (psample.admission_date, 'dd-mm-YYYY' ) AS admissiondate,
                 to_char ( psample.admission_date, 'HH24:MI:SS' ) AS admissiontime,
				 
				 sls.source_name,
                 psample.clinical_history AS diagnosis,
                 patient.sex,
				 
				 

				 date_part('year', age(psample.collected_date, (patient.dob)::timestamp with time zone)) AS _year,
				 date_part('month', age(psample.collected_date, (patient.dob)::timestamp with time zone)) AS _month,
				 date_part('day', age(psample.collected_date, (patient.dob)::timestamp with time zone)) AS _day,




				 patient.province,
				 patient.district,
				 patient.commune,
				 patient.village,
                 sample.\"ID\" as sampleid,
				 sample.sample_name as samplename,
				 
                 
				 to_char (presult.test_date, 'dd-mm-YYYY' ) AS detecteddate,
                 organism.\"ID\" as orgid,
				 organism.organism_name,
                 antibiotic.\"ID\" as astid,
                 presult_antibiotic.sensitivity,
				 presult_antibiotic.test_zone,
				 presult_antibiotic.disc_diffusion,
				 result_comment.result_comment,
				 
				 (case when sample.sample_name = 'CSF' then (
					SELECT tr.result from camlis_patient_sample ps 
					inner join camlis_patient_sample_tests pt on ps.\"ID\"=pt.patient_sample_id
					inner join camlis_ptest_result tr on pt.\"ID\"=tr.patient_test_id
					where ps.\"ID\"=psample.\"ID\" and pt.sample_test_id=190 group by tr.result)
				 else '' end) as WBC,

				 (case when sample.sample_name='CSF' then (SELECT org.organism_name from camlis_patient_sample ps 
				 inner join camlis_patient_sample_tests pt on ps.\"ID\" = pt.patient_sample_id
				 inner join camlis_ptest_result tr on pt.\"ID\" = tr.patient_test_id
				 INNER JOIN camlis_std_test_organism AS torg ON tr.result = cast(torg.\"ID\" as varchar) AND tr.type = 1
				 INNER JOIN camlis_std_organism AS org ON torg.organism_id = org.\"ID\"
				 where ps.\"ID\"= psample.\"ID\" and pt.sample_test_id=199 and org.\"ID\"=268 group by tr.result , org.organism_name) else '' end) as Gram

            FROM $tblallpatients as patient
			INNER JOIN camlis_patient_sample AS psample ON psample.patient_id = patient.pid and psample.sample_number='$labnumber'
			
			INNER JOIN camlis_lab_sample_source as sls on psample.sample_source_id=sls.\"ID\" 
            INNER JOIN camlis_laboratory AS lab ON psample.\"labID\" = lab.\"labID\"
            
            INNER JOIN camlis_patient_sample_tests AS ptest ON psample.\"ID\" = ptest.patient_sample_id
            INNER JOIN camlis_std_sample_test AS sample_test ON ptest.sample_test_id = sample_test.\"ID\"
            INNER JOIN camlis_std_test AS test ON sample_test.test_id = test.\"ID\"
            INNER JOIN camlis_std_department_sample AS department_sample ON sample_test.department_sample_id = department_sample.\"ID\"
            INNER JOIN camlis_std_department AS department ON department_sample.department_id = department.\"ID\"
            INNER JOIN camlis_std_sample AS sample ON department_sample.sample_id = sample.\"ID\"
            
            INNER JOIN camlis_ptest_result AS presult ON ptest.\"ID\" = presult.patient_test_id
			INNER JOIN camlis_std_test_organism AS organism_std ON presult.result = cast(organism_std.\"ID\" as varchar) AND presult.type = 1
			INNER JOIN camlis_std_organism AS organism ON organism_std.organism_id = organism.\"ID\"
            
            INNER JOIN camlis_ptest_result_antibiotic AS presult_antibiotic ON presult.\"ID\" = presult_antibiotic.presult_id AND presult_antibiotic.status = True
            INNER JOIN camlis_std_antibiotic AS antibiotic ON presult_antibiotic.antibiotic_id = antibiotic.\"ID\"
            
            LEFT JOIN camlis_result_comment AS result_comment ON psample.\"ID\" = result_comment.patient_sample_id AND department_sample.\"ID\" = result_comment.department_sample_id
            WHERE psample.status = 1
                  AND ptest.status = 1
                  AND presult.status = 1
				  AND (presult.contaminant is null or presult.contaminant=0)
				  AND sample.\"ID\" in (6,8)
			ORDER BY lab.lab_code,patient.pid,psample.sample_number,sample.\"ID\",organism.organism_name";

        return $this->db->query($sql)->result_array();
    }
	
	public function get_patient_by_culture($labid,$start_date, $end_date) {

		$tblallpatients="(
						SELECT 
						CAST(pmrs_patient.pid AS VARCHAR) AS pid, 
						psample.\"labID\" AS lab_id,
						CAST(pmrs_patient.pid AS varchar(50)) AS patient_id, 
                    	CAST(pmrs_patient.pid AS varchar(50)) AS patient_code,
						pmrs_patient.name AS patient_name,
						(CASE pmrs_patient.sex WHEN 'M' THEN 1 WHEN 'F' THEN 2 ELSE 0 END) AS sex,pmrs_patient.dob AS dob,
						pmrs_patient.phone AS phone,
						pmrs_patient.province AS province,
						pmrs_patient.district AS district,
						pmrs_patient.commune AS commune,
						pmrs_patient.village AS village
						FROM (camlis_patient_sample psample
						JOIN camlis_pmrs_patient pmrs_patient ON(((psample.patient_id = pmrs_patient.pid) AND (pmrs_patient.status = 1))))
						WHERE (psample.status = 1 and psample.\"labID\"=$labid)
						GROUP BY psample.patient_id,psample.\"labID\",pmrs_patient.pid 
						UNION ALL
						SELECT CAST(outside_patient.pid AS VARCHAR(50)) AS pid,
						outside_patient.lab_id AS lab_id,
						outside_patient.patient_id AS patient_id,
						outside_patient.patient_code AS patient_code,
						outside_patient.patient_name AS patient_name,
						outside_patient.sex AS sex,outside_patient.dob AS dob,
						outside_patient.phone AS phone,
						outside_patient.province AS province,outside_patient.district AS district,outside_patient.commune AS commune,outside_patient.village AS village
						FROM camlis_outside_patient outside_patient
						WHERE (outside_patient.status = 1 and outside_patient.lab_id=$labid))";
		$sql="
			SELECT _t.title,_t.sampletype,				
				SUM(CASE WHEN (_t.sex = 1 AND _t.age_group = '1') THEN 1 ELSE 0 END) AS a1M,				
				SUM(CASE WHEN (_t.sex = 2 AND _t.age_group = '1') THEN 1 ELSE 0 END) AS a1F,
				SUM(CASE WHEN (_t.sex = 1 AND _t.age_group = '2') THEN 1 ELSE 0 END) AS a2M,
				SUM(CASE WHEN (_t.sex = 2 AND _t.age_group = '2') THEN 1 ELSE 0 END) AS a2F,
				SUM(CASE WHEN (_t.sex = 1 AND _t.age_group = '3') THEN 1 ELSE 0 END) AS a3M,
				SUM(CASE WHEN (_t.sex = 2 AND _t.age_group = '3') THEN 1 ELSE 0 END) AS a3F,
				SUM(CASE WHEN (_t.sex = 2 AND _t.age_group = '4') THEN 1 ELSE 0 END) AS a4M,
				SUM(CASE WHEN (_t.sex = 2 AND _t.age_group = '4') THEN 1 ELSE 0 END) AS a4F,
				SUM(CASE WHEN (_t.sex = 1 AND _t.age_group = '5') THEN 1 ELSE 0 END) AS a5M,
				SUM(CASE WHEN (_t.sex = 2 AND _t.age_group = '5') THEN 1 ELSE 0 END) AS a5F,
				SUM(CASE WHEN (_t.sex = 1 AND _t.age_group = '6') THEN 1 ELSE 0 END) AS a6M,
				SUM(CASE WHEN (_t.sex = 2 AND _t.age_group = '6') THEN 1 ELSE 0 END) AS a6F,
				SUM(CASE WHEN (_t.sex = 1 AND _t.age_group = '7') THEN 1 ELSE 0 END) AS a7M,
				SUM(CASE WHEN (_t.sex = 2 AND _t.age_group = '7') THEN 1 ELSE 0 END) AS a7F,				
				SUM(CASE WHEN (_t.sex = 1 AND _t.age_group = '8') THEN 1 ELSE 0 END) AS a8M,				
				SUM(CASE WHEN (_t.sex = 2 AND _t.age_group = '8') THEN 1 ELSE 0 END) AS a8F,
				COUNT(_t.sampletype) AS total
				from (select distinct '1.TOTAL SAMPLE TESTED' as title,st.group_result as sampletype,patient.sex,
				(CASE
					WHEN (ps.collected_date - patient.dob <= 29) THEN '1'
                    WHEN ((ps.collected_date - patient.dob) / 30) BETWEEN 1 AND 11 THEN '2'
                    WHEN DATE_PART('year', AGE(ps.collected_date, patient.dob)) BETWEEN 1 AND 4 THEN '3'
                    WHEN DATE_PART('year', AGE(ps.collected_date, patient.dob)) BETWEEN 5 AND 14 THEN '4'
                    WHEN DATE_PART('year', AGE(ps.collected_date, patient.dob)) BETWEEN 15 AND 24 THEN '5'
                    WHEN DATE_PART('year', AGE(ps.collected_date, patient.dob)) BETWEEN 25 AND 49 THEN '6'
                    WHEN DATE_PART('year', AGE(ps.collected_date, patient.dob)) BETWEEN 50 AND 64 THEN '7'
					ELSE '8'	
				END) AS age_group,ps.sample_number,ps.\"labID\",ps.received_date,ps.received_time 
				from $tblallpatients as patient
				inner join camlis_patient_sample ps on cast(patient.pid as varchar) = ps.patient_id 
				inner join camlis_patient_sample_tests pst on ps.\"ID\"=pst.patient_sample_id
				inner join camlis_std_sample_test st on pst.sample_test_id=st.\"ID\"
				where ps.status=1 and st.department_sample_id in (6,8) and st.test_id=170

				union all 

				select distinct '2.Number of positive sample' as title,concat(st.group_result,'_positive') as sampletype,patient.sex,
				(CASE
					WHEN (ps.collected_date - patient.dob <= 29) THEN '1'
					WHEN ((ps.collected_date - patient.dob) / 30) BETWEEN 1 AND 11 THEN '2'
					WHEN DATE_PART('year', AGE(ps.collected_date, patient.dob)) BETWEEN 1 AND 4 THEN '3'
					WHEN DATE_PART('year', AGE(ps.collected_date, patient.dob)) BETWEEN 5 AND 14 THEN '4'
					WHEN DATE_PART('year', AGE(ps.collected_date, patient.dob)) BETWEEN 15 AND 24 THEN '5'
					WHEN DATE_PART('year', AGE(ps.collected_date, patient.dob)) BETWEEN 25 AND 49 THEN '6'
					WHEN DATE_PART('year', AGE(ps.collected_date, patient.dob)) BETWEEN 50 AND 64 THEN '7'
					ELSE '8'	
				END) AS age_group,ps.sample_number,ps.\"labID\",ps.received_date,ps.received_time 
				from $tblallpatients as patient
				inner join camlis_patient_sample ps on patient.pid=ps.patient_id 
				inner join camlis_patient_sample_tests pst on ps.\"ID\"=pst.patient_sample_id
				inner join camlis_std_sample_test st on pst.sample_test_id=st.\"ID\"
				inner join camlis_ptest_result tr on tr.patient_sample_id=ps.\"ID\"
				inner join camlis_ptest_result_antibiotic ran on ran.presult_id = tr.\"ID\"
				where ps.status=1 and tr.status = 1 and tr.contaminant = 0 and st.department_sample_id in (6,8) and st.test_id=170) _t
			where _t.\"labID\" = $labid 			
			AND _t.received_date >= '$start_date'
            AND _t.received_date <= '$end_date'
			group by _t.title,_t.sampletype
			order by _t.sampletype";
			
        return $this->db->query($sql)->result_array();
	}


	public function get_priorities_pathogens($start_date, $end_date) {

		$tblallpatients="(SELECT pmrs_patient.pid AS pid
						FROM camlis_pmrs_patient as pmrs_patient
						WHERE pmrs_patient.status = 1
                        GROUP BY pmrs_patient.pid 
                        
                        UNION ALL
						SELECT CAST(outside_patient.pid AS VARCHAR(50)) AS pid
						FROM camlis_outside_patient outside_patient
						WHERE outside_patient.status = 1
                        GROUP BY outside_patient.pid)";
		$sql="
			SELECT _t.\"ID\",_t.organism_name,COUNT(_t.organism_name) AS total 
				from (
					select distinct organism.\"ID\",organism.organism_name,ps.sample_number,ps.\"labID\",ps.received_date,ps.received_time 
					from $tblallpatients as patient
					inner join camlis_patient_sample ps on patient.pid=ps.patient_id 
					inner join camlis_ptest_result ptr on ps.\"ID\"=ptr.patient_sample_id
					inner join camlis_std_test_organism AS organism_std ON ptr.result = CAST(organism_std.\"ID\" AS VARCHAR(50)) AND ptr.type = 1
					inner join camlis_std_organism AS organism ON organism_std.organism_id = organism.\"ID\"
					where ps.status=1 and organism.\"ID\" in (480,131,495,247,181,406,401,402,403,404,448,148,488)) _t
			where _t.received_date >= '$start_date' AND _t.received_date <= '$end_date'
			group by _t.organism_name , _t.\"ID\"
			order by _t.organism_name";
			
        return $this->db->query($sql)->result_array();
	}
	
	/** ONE DASHBOARD */
	/**
     * Get Uer
     * @param null $username
     * @param null $user_id
     * @param bool $match_user_id
     */
	public function login($username = NULL, $password = NULL) {
	    $this->db->where('banned', FALSE);
        $this->db->where('username', $username);
        $this->db->where('pass', $password);
        $query = $this->db->get(self::$CamLIS_db.'.camlis_aauth_users');

        if ($query->num_rows() > 0) return TRUE;

        return FALSE;
    }
	public function get_laboratory(){
        $sql = "SELECT * FROM camlis_laboratory where status = true limit 10";
        return $this->db->query($sql)->result_array();
    }
}