<?php
defined('BASEPATH') OR die('Permission denied.');
class Report_model extends MY_Model {
	public function __construct() {
		parent::__construct(); 
		$this->load->database(); 
	}
	/* vuthy 
	 * 2016-11-29
	 * @parame date start and end
	 */
	function create_temp_patients_v($receivedDateCond, $labId){
        $sql = "CREATE TEMPORARY TABLE IF NOT EXISTS temp_patients_v as (
            SELECT
            	distinct
                CASE
                    WHEN op.patient_id::text <> '0'::text THEN op.patient_id::text
                    ELSE op.pid::text
                END AS pid,
            op.patient_name,
                CASE
                    WHEN op.sex = 1 THEN 'M'::text
                    WHEN op.sex = 2 THEN 'F'::text
                    ELSE NULL::text
                END AS sex,
            op.dob,
            date_part('year'::text, age(now(), op.dob::timestamp with time zone)) AS _year,
            date_part('month'::text, age(now(), op.dob::timestamp with time zone)) AS _month,
            date_part('day'::text, age(now(), op.dob::timestamp with time zone)) AS _day,
            op.province,
            op.status
            FROM camlis_outside_patient op
        	inner join camlis_patient_sample AS psample on psample.patient_id = case when op.patient_id::text <> '0'::text then op.patient_id::text else op.pid::text end
            where psample.\"labID\" = '".$labId."' ".$receivedDateCond."   
            and psample.status = 1 
            and op.status = 1
            
            UNION ALL
            SELECT 
            	distinct
            	op.pid,
                op.name AS patient_name,
                op.sex,
                op.dob,
                date_part('year'::text, age(now(), op.dob::timestamp with time zone)) AS _year,
                date_part('month'::text, age(now(), op.dob::timestamp with time zone)) AS _month,
                date_part('day'::text, age(now(), op.dob::timestamp with time zone)) AS _day,
                op.province,
                op.status
            FROM camlis_pmrs_patient op
            inner join camlis_patient_sample AS psample on psample.patient_id = op.pid
            where psample.\"labID\" = '".$labId."' ".$receivedDateCond."
            and psample.status = 1
            and op.status = 1
            
        )";

        $this->db->query($sql);
        
    }

	function aggregate_table_($obj){		         
        $startDateTime = $obj->start.' '.$obj->start_time;
        $endDateTime = $obj->end.' '.$obj->end_time;
        $sql = "
            SELECT
            CASE
                WHEN
                    name_kh != '' THEN
                        name_kh 
                        WHEN Title != '' THEN
                        'Sub total' ELSE 'Grand total' 
                    END AS distribute,
                    Title,
                    name_kh,
                    SUM ( male ) AS male,
                    SUM ( female ) AS female,
                    SUM ( total ) AS total 
                FROM
                    (
                    SELECT DISTINCT
                        '1.0. Distribution by province of patient' AS Title,
                        name_kh,
                        name_en,
                        COUNT ( CASE WHEN sex = 'F' THEN 1 ELSE NULL END ) AS female,
                        COUNT ( CASE WHEN sex = 'M' THEN 1 ELSE NULL END ) AS male,
                        SUM ( CASE WHEN sex = 'F' THEN 1 WHEN sex = 'M' THEN 1 ELSE 0 END ) AS total 
                    FROM
                        (
                        SELECT
                            pr.name_kh,
                            pr.name_en,
                            P.sex,
                            P.patient_name,
                            pv.received_date 
                        FROM
                            provinces pr
                            INNER JOIN patient_v P ON pr.code = P.province
                            INNER JOIN camlis_patient_sample pv ON pv.patient_id = P.pid 
                        WHERE
                            concat( pv.received_date,' ',pv.received_time) >= '".$startDateTime."'
                            AND concat( pv.received_date, ' ', pv.received_time) <= '".$endDateTime."'
                            AND pv.\"labID\" = '".$this->session->userdata('laboratory')->labID."' 
                        GROUP BY
                            P.pid,
                            P.sex,
                            P.patient_name,
                            pr.name_kh,
                            pr.name_en,
                            pv.sample_number,
                            pv.received_date 
                        ) AS tbl 
                    GROUP BY
                        name_kh,
                        name_en UNION ALL
            /* aging section */
                    SELECT
                        '2.0. Distribution by age group' AS Title,
                        name_kh,
                        name_en,
                        COUNT ( CASE WHEN sex = 'F' THEN 1 ELSE NULL END ) AS female,
                        COUNT ( CASE WHEN sex = 'M' THEN 1 ELSE NULL END ) AS male,
                        COUNT ( sex ) AS total 
                    FROM
                        (
                        SELECT P.pid,
                            '1: 0 - 29 days' AS name_kh,
                            '1: 0 - 29 days' AS name_en,
                            P.sex,
                            P.patient_name,
                            pv.received_date 
                        FROM
                            patient_v
                            P INNER JOIN camlis_patient_sample pv ON pv.patient_id = P.pid 
                        WHERE
                             
                            concat( pv.received_date,' ',pv.received_time) >= '".$startDateTime."'
                            AND concat( pv.received_date, ' ', pv.received_time) <= '".$endDateTime."'

                            AND pv.\"labID\" = '".$this->session->userdata('laboratory')->labID."' 
                            AND P._day <= 30 
                            AND P._month = 0 
                            AND P._year = 0 UNION ALL
                        SELECT P.pid,
                            '2: 1 - 11 m' AS name_kh,
                            '2: 1 - 11 m' AS name_en,
                            P.sex,
                            P.patient_name,
                            pv.received_date 
                        FROM
                            patient_v
                            P INNER JOIN camlis_patient_sample pv ON pv.patient_id = P.pid 
                        WHERE
                            concat( pv.received_date,' ',pv.received_time) >= '".$startDateTime."'
                            AND concat( pv.received_date, ' ', pv.received_time) <= '".$endDateTime."'
                            AND pv.\"labID\" = '".$this->session->userdata('laboratory')->labID."' 
                            AND P._month >= 1 
                            AND P._month <= 12 
                            AND P._year = 0 UNION ALL
                        SELECT P.pid,
                            '3: 1 y - 4 y' AS name_kh,
                            '3: 1 y - 4 y' AS name_en,
                            P.sex,
                            P.patient_name,
                            pv.received_date 
                        FROM
                            patient_v
                            P INNER JOIN camlis_patient_sample pv ON pv.patient_id = P.pid 
                        WHERE                
                            concat( pv.received_date,' ',pv.received_time) >= '".$startDateTime."'
                            AND concat( pv.received_date, ' ', pv.received_time) <= '".$endDateTime."'

                            AND pv.\"labID\" = '".$this->session->userdata('laboratory')->labID."' 
                            AND P._year >= 1 
                            AND P._year <= 4 UNION ALL
                        SELECT P.pid,
                            '4: 5 y - 14 y' AS name_kh,
                            '4: 5 y - 14 y' AS name_en,
                            P.sex,
                            P.patient_name,
                            pv.received_date 
                        FROM
                            patient_v
                            P INNER JOIN camlis_patient_sample pv ON pv.patient_id = P.pid 
                        WHERE
                            concat( pv.received_date,' ',pv.received_time) >= '".$startDateTime."'
                            AND concat( pv.received_date, ' ', pv.received_time) <= '".$endDateTime."'
                            AND pv.\"labID\" = '".$this->session->userdata('laboratory')->labID."' 
                            AND P._year >= 5 
                            AND P._year <= 14 UNION ALL
                        SELECT P.pid,
                            '5: 15 y - 24 y' AS name_kh,
                            '5: 15 y - 24 y' AS name_en,
                            P.sex,
                            P.patient_name,
                            pv.received_date 
                        FROM
                            patient_v
                            P INNER JOIN camlis_patient_sample pv ON pv.patient_id = P.pid 
                        WHERE
                            concat( pv.received_date,' ',pv.received_time) >= '".$startDateTime."'
                            AND concat( pv.received_date, ' ', pv.received_time) <= '".$endDateTime."' 
                            AND pv.\"labID\" = '".$this->session->userdata('laboratory')->labID."' 
                            AND P._year >= 15 
                            AND P._year <= 24 UNION ALL
                        SELECT P.pid,
                            '6: 25 y - 49 y' AS name_kh,
                            '6: 25 y - 49 y' AS name_en,
                            P.sex,
                            P.patient_name,
                            pv.received_date 
                        FROM
                            patient_v
                            P INNER JOIN camlis_patient_sample pv ON pv.patient_id = P.pid 
                        WHERE
                            concat( pv.received_date,' ',pv.received_time) >= '".$startDateTime."'
                            AND concat( pv.received_date, ' ', pv.received_time) <= '".$endDateTime."'
                            AND pv.\"labID\" = '".$this->session->userdata('laboratory')->labID."' 
                            AND P._year >= 25 
                            AND P._year <= 49 UNION ALL
                        SELECT P.pid,
                            '7: 50 y - 64 y' AS name_kh,
                            '7: 50 y - 64 y' AS name_en,
                            P.sex,
                            P.patient_name,
                            pv.received_date 
                        FROM
                            patient_v
                            P INNER JOIN camlis_patient_sample pv ON pv.patient_id = P.pid 
                        WHERE
                            concat( pv.received_date,' ',pv.received_time) >= '".$startDateTime."'
                            AND concat( pv.received_date, ' ', pv.received_time) <= '".$endDateTime."' 
                            AND pv.\"labID\" = '".$this->session->userdata('laboratory')->labID."' 
                            AND P._year >= 50 
                            AND P._year <= 64 UNION ALL
                        SELECT P.pid,
                            '8: >= 65 y' AS name_kh,
                            '8: >= 65 y' AS name_en,
                            P.sex,
                            P.patient_name,
                            pv.received_date 
                        FROM
                            patient_v
                            P INNER JOIN camlis_patient_sample pv ON pv.patient_id = P.pid 
                        WHERE
                            concat( pv.received_date,' ',pv.received_time) >= '".$startDateTime."'
                            AND concat( pv.received_date, ' ', pv.received_time) <= '".$endDateTime."'
                            AND pv.\"labID\" = '".$this->session->userdata('laboratory')->labID."' 
                            AND P._year >= 65 
                        ) AS P 
                    GROUP BY
                        name_en,
                        name_kh /* end aging */
                    UNION ALL
                    SELECT
                        Title,
                        name_kh,
                        name_en,
                        COUNT ( CASE WHEN sex = 'F' THEN 1 ELSE NULL END ) AS female,
                        COUNT ( CASE WHEN sex = 'M' THEN 1 ELSE NULL END ) AS male,
                        COUNT ( total ) AS total 
                    FROM
                        (
                        SELECT
                            '3.0. Distribution by Sample Source' AS Title,
                            ss.source_name AS name_kh,
                            ss.source_name AS name_en,
                            P.sex,
                            COUNT ( P.sex ) AS total 
                        FROM
                            patient_v
                            P INNER JOIN camlis_patient_sample pv ON pv.patient_id = P.pid 
                            AND pv.status = 1
                            INNER JOIN camlis_lab_sample_source ss ON ss.\"ID\" = pv.sample_source_id 
                        WHERE
                            concat( pv.received_date,' ',pv.received_time) >= '".$startDateTime."'
                            AND concat( pv.received_date, ' ', pv.received_time) <= '".$endDateTime."' 
                            AND pv.\"labID\" = '".$this->session->userdata('laboratory')->labID."' 
                        GROUP BY
                            ss.source_name,
                            P.pid,
                            P.sex,
                            pv.sample_number 
                        ) dis3 
                    GROUP BY
                        Title,
                        name_kh,
                        name_en UNION ALL
            /* 4.0. Distribution by paymement type */
                    SELECT
                        '4.0. Distribution by Payment type' AS Title,
                        spt.NAME AS name_kh,
                        spt.NAME AS name_en,
                        COUNT ( CASE WHEN P.sex = 'F' THEN 1 ELSE NULL END ) AS female,
                        COUNT ( CASE WHEN P.sex = 'M' THEN 1 ELSE NULL END ) AS male,
                        COUNT ( P.sex ) AS total 
                    FROM
                        patient_v
                        P INNER JOIN camlis_patient_sample pv ON pv.patient_id = P.pid
                        INNER JOIN camlis_lab_payment_type lpt ON lpt.payment_type_id = pv.payment_type_id
                        INNER JOIN camlis_std_payment_type spt ON spt.id = lpt.payment_type_id 
                    WHERE
                        concat( pv.received_date,' ',pv.received_time) >= '".$startDateTime."'
                        AND concat( pv.received_date, ' ', pv.received_time) <= '".$endDateTime."'

                        AND pv.\"labID\" = '".$this->session->userdata('laboratory')->labID."' 
                        AND lpt.lab_id = '".$this->session->userdata('laboratory')->labID."'
                    GROUP BY
                        spt.NAME UNION ALL
            /* 5.0. Distribution by Sample status */
                    SELECT
                        '5.0. Distribution by Sample status' AS Title,
                        name_kh,
                        name_en,                        
                        SUM ( female ) AS female,
                        SUM ( male ) AS male,
                        SUM ( total ) AS total 
                    FROM
                        ( 
                            /*13-02-2021*/
                        SELECT
                            'សង្ស័យ' AS name_kh,
                            'Suspect' AS name_en,
                            COUNT ( CASE WHEN P.sex = 'M' THEN 1 ELSE NULL END ) AS male,
                            COUNT ( CASE WHEN P.sex = 'F' THEN 1 ELSE NULL END ) AS female,
                            COUNT ( P.sex ) AS total 
                        FROM
                            patient_v
                            P INNER JOIN camlis_patient_sample pv ON pv.patient_id = P.pid 
                        WHERE
                            concat( pv.received_date,' ',pv.received_time) >= '".$startDateTime."'
                            AND concat( pv.received_date, ' ', pv.received_time) <= '".$endDateTime."' 
                            
                            AND pv.\"labID\" = '".$this->session->userdata('laboratory')->labID."' 
                            AND pv.for_research = 1 UNION ALL
                        
                        SELECT
                            'រលាកសួត' AS name_kh,
                            'Pneumonia' AS name_en,
                            COUNT ( CASE WHEN P.sex = 'M' THEN 1 ELSE NULL END ) AS male,
                            COUNT ( CASE WHEN P.sex = 'F' THEN 1 ELSE NULL END ) AS female,
                            COUNT ( P.sex ) AS total 
                        FROM
                            patient_v
                            P INNER JOIN camlis_patient_sample pv ON pv.patient_id = P.pid 
                        WHERE
                            concat( pv.received_date,' ',pv.received_time) >= '".$startDateTime."'
                            AND concat( pv.received_date, ' ', pv.received_time) <= '".$endDateTime."' 
                            
                            AND pv.\"labID\" = '".$this->session->userdata('laboratory')->labID."' 
                            AND pv.for_research = 2 UNION ALL
                        SELECT
                            'តាមដាន' AS name_kh,
                            'Follow Up' AS name_en,
                            COUNT ( CASE WHEN P.sex = 'M' THEN 1 ELSE NULL END ) AS male,
                            COUNT ( CASE WHEN P.sex = 'F' THEN 1 ELSE NULL END ) AS female,
                            COUNT ( P.sex ) AS total 
                        FROM
                            patient_v
                            P INNER JOIN camlis_patient_sample pv ON pv.patient_id = P.pid 
                        WHERE
                            concat( pv.received_date,' ',pv.received_time) >= '".$startDateTime."'
                            AND concat( pv.received_date, ' ', pv.received_time) <= '".$endDateTime."' 
                            
                            AND pv.\"labID\" = '".$this->session->userdata('laboratory')->labID."' 
                            AND pv.for_research = 3 UNION ALL
                        SELECT
                            'អ្នកប៉ះពាល់' AS name_kh,
                            'Contact' AS name_en,
                            COUNT ( CASE WHEN P.sex = 'M' THEN 1 ELSE NULL END ) AS male,
                            COUNT ( CASE WHEN P.sex = 'F' THEN 1 ELSE NULL END ) AS female,
                            COUNT ( P.sex ) AS total 
                        FROM
                            patient_v
                            P INNER JOIN camlis_patient_sample pv ON pv.patient_id = P.pid 
                        WHERE
                            concat( pv.received_date,' ',pv.received_time) >= '".$startDateTime."'
                            AND concat( pv.received_date, ' ', pv.received_time) <= '".$endDateTime."' 
                            
                            AND pv.\"labID\" = '".$this->session->userdata('laboratory')->labID."' 
                            AND pv.for_research = 4 UNION ALL
                        SELECT
                            'បុគ្គលិកពេទ្យ' AS name_kh,
                            'HCW' AS name_en,
                            COUNT ( CASE WHEN P.sex = 'M' THEN 1 ELSE NULL END ) AS male,
                            COUNT ( CASE WHEN P.sex = 'F' THEN 1 ELSE NULL END ) AS female,
                            COUNT ( P.sex ) AS total 
                        FROM
                            patient_v
                            P INNER JOIN camlis_patient_sample pv ON pv.patient_id = P.pid 
                        WHERE
                            concat( pv.received_date,' ',pv.received_time) >= '".$startDateTime."'
                            AND concat( pv.received_date, ' ', pv.received_time) <= '".$endDateTime."' 
                            
                            AND pv.\"labID\" = '".$this->session->userdata('laboratory')->labID."' 
                            AND pv.for_research = 5 UNION ALL
                        SELECT
                            'ផ្សេងទៀត' AS name_kh,
                            'Other' AS name_en,
                            COUNT ( CASE WHEN P.sex = 'M' THEN 1 ELSE NULL END ) AS male,
                            COUNT ( CASE WHEN P.sex = 'F' THEN 1 ELSE NULL END ) AS female,
                            COUNT ( P.sex ) AS total 
                        FROM
                            patient_v
                            P INNER JOIN camlis_patient_sample pv ON pv.patient_id = P.pid 
                        WHERE
                            concat( pv.received_date,' ',pv.received_time) >= '".$startDateTime."'
                            AND concat( pv.received_date, ' ', pv.received_time) <= '".$endDateTime."' 
                            
                            AND pv.\"labID\" = '".$this->session->userdata('laboratory')->labID."' 
                            AND pv.for_research = 6 UNION ALL 
                        SELECT
                            'ពលករចំណាកស្រុក' AS name_kh,
                            'Migrants' AS name_en,
                            COUNT ( CASE WHEN P.sex = 'M' THEN 1 ELSE NULL END ) AS male,
                            COUNT ( CASE WHEN P.sex = 'F' THEN 1 ELSE NULL END ) AS female,
                            COUNT ( P.sex ) AS total 
                        FROM
                            patient_v
                            P INNER JOIN camlis_patient_sample pv ON pv.patient_id = P.pid 
                        WHERE
                            concat( pv.received_date,' ',pv.received_time) >= '".$startDateTime."'
                            AND concat( pv.received_date, ' ', pv.received_time) <= '".$endDateTime."' 
                            
                            AND pv.\"labID\" = '".$this->session->userdata('laboratory')->labID."' 
                            AND pv.for_research = 7 UNION ALL
                        SELECT
                            'អ្នកដំណើរតាមយន្តហោះ' AS name_kh,
                            'Passenger' AS name_en,
                            COUNT ( CASE WHEN P.sex = 'M' THEN 1 ELSE NULL END ) AS male,
                            COUNT ( CASE WHEN P.sex = 'F' THEN 1 ELSE NULL END ) AS female,
                            COUNT ( P.sex ) AS total 
                        FROM
                            patient_v
                            P INNER JOIN camlis_patient_sample pv ON pv.patient_id = P.pid 
                        WHERE
                            concat( pv.received_date,' ',pv.received_time) >= '".$startDateTime."'
                            AND concat( pv.received_date, ' ', pv.received_time) <= '".$endDateTime."' 
                            
                            AND pv.\"labID\" = '".$this->session->userdata('laboratory')->labID."' 
                            AND pv.for_research = 8 UNION ALL
                        SELECT
                            'វិញ្ញាបនបត្រ' AS name_kh,
                            'Certificate' AS name_en,
                            COUNT ( CASE WHEN P.sex = 'M' THEN 1 ELSE NULL END ) AS male,
                            COUNT ( CASE WHEN P.sex = 'F' THEN 1 ELSE NULL END ) AS female,
                            COUNT ( P.sex ) AS total 
                        FROM
                            patient_v
                            P INNER JOIN camlis_patient_sample pv ON pv.patient_id = P.pid 
                        WHERE
                            concat( pv.received_date,' ',pv.received_time) >= '".$startDateTime."'
                            AND concat( pv.received_date, ' ', pv.received_time) <= '".$endDateTime."' 
                            
                            AND pv.\"labID\" = '".$this->session->userdata('laboratory')->labID."' 
                            AND pv.for_research = 9 UNION ALL   /* End 13-02-2021*/
                        SELECT
                            'Urgent' AS name_kh,
                            'Urgent' AS name_en,
                            COUNT ( CASE WHEN P.sex = 'M' THEN 1 ELSE NULL END ) AS male,
                            COUNT ( CASE WHEN P.sex = 'F' THEN 1 ELSE NULL END ) AS female,
                            COUNT ( P.sex ) AS total 
                        FROM
                            patient_v
                            P INNER JOIN camlis_patient_sample pv ON pv.patient_id = P.pid 
                        WHERE
                            concat( pv.received_date,' ',pv.received_time) >= '".$startDateTime."'
                            AND concat( pv.received_date, ' ', pv.received_time) <= '".$endDateTime."'

                            AND pv.\"labID\" = '".$this->session->userdata('laboratory')->labID."' 
                            AND pv.is_urgent = 1 
                        ) AS pv 
                    GROUP BY
                        name_en,
                        name_kh /* Display by sample type */
                        
                    UNION ALL
                    SELECT
                        '6.0. Distribution by Sample Type' AS Title,
                        sample_name AS name_kh,
                        '' AS name_en,
                        COUNT ( CASE WHEN sex = 'F' THEN 1 ELSE NULL END ) AS female,
                        COUNT ( CASE WHEN sex = 'M' THEN 1 ELSE NULL END ) AS male,
                        COUNT ( sex ) AS total 
                    FROM
                        (
                        SELECT P
                            .sex,
                            ss.sample_name,
                            P.patient_name,
                            psd.department_sample_id 
                        FROM
                            patient_v
                            P INNER JOIN camlis_patient_sample pv ON pv.patient_id = P.pid 
                            AND P.status = 1
                            INNER JOIN camlis_patient_sample_detail psd ON psd.patient_sample_id = pv.\"ID\"
                            INNER JOIN camlis_std_department_sample sds ON sds.\"ID\" = psd.department_sample_id
                            INNER JOIN camlis_std_sample ss ON ss.\"ID\" = sds.sample_id 
                        WHERE
                        concat( pv.received_date,' ',pv.received_time) >= '".$startDateTime."'
                        AND concat( pv.received_date, ' ', pv.received_time) <= '".$endDateTime."' 
                            AND pv.\"labID\" = '".$this->session->userdata('laboratory')->labID."' 
                        ) v 
                    GROUP BY
                        patient_name,
                        name_kh,
                        department_sample_id /*  Display by sample rejection*/
                    UNION ALL
                    SELECT
                        '7.0. Distribution by Sample Rejection' AS Title,
                        rs.sample_name AS name_kh,
                        rs.sample_name AS name_en,
                        SUM ( rs.male ) AS male,
                        SUM ( rs.female ) AS female,
                        COUNT ( rs.sample_name ) AS total 
                    FROM
                        (
                        SELECT
                            ss.sample_name,
                        CASE WHEN P.sex = 'M' THEN 1 ELSE NULL END AS male,
                        CASE WHEN P.sex = 'F' THEN 1 ELSE NULL END AS female 
                        FROM
                            patient_v P
                            INNER JOIN camlis_patient_sample pv ON pv.patient_id = P.pid
                            INNER JOIN camlis_patient_sample_tests pst ON pst.patient_sample_id = pv.\"ID\"
                            INNER JOIN camlis_std_sample_test sst ON sst.\"ID\" = pst.sample_test_id
                            INNER JOIN camlis_std_department_sample sds ON sds.\"ID\" = sst.department_sample_id
                            INNER JOIN camlis_std_sample ss ON ss.\"ID\" = sds.sample_id 
                        WHERE
                        concat( pv.received_date,' ',pv.received_time) >= '".$startDateTime."'
                        AND concat( pv.received_date, ' ', pv.received_time) <= '".$endDateTime."' 
                            AND pv.\"labID\" = '".$this->session->userdata('laboratory')->labID."' 
                            AND pv.status = 1 
                            AND P.status = 1 
                            AND pst.is_rejected = TRUE
                        GROUP BY
                            P.pid,
                            P.sex,
                            pv.sample_number,
                            ss.sample_name 
                        ) rs 
                    GROUP BY
                        rs.sample_name /*  Display by department and test*/
                    UNION ALL
                    SELECT
                            concat ( '8.0. Distribution by ', ' ', d.department_name ) AS Title,
                            sst.group_result AS name_kh,
                            '' AS name_en,
                            COUNT ( CASE WHEN P.sex = 'F' THEN 1 ELSE NULL END ) AS female,
                            COUNT ( CASE WHEN P.sex = 'M' THEN 1 ELSE NULL END ) AS male,
                            COUNT ( P.sex ) AS total 
                        FROM
                            patient_v
                            P INNER JOIN camlis_patient_sample pv ON pv.patient_id = P.pid
                            INNER JOIN camlis_patient_sample_tests pst ON pst.patient_sample_id = pv.\"ID\" 
                            AND pst.status = 1
                            INNER JOIN camlis_std_sample_test sst ON sst.\"ID\" = pst.sample_test_id
                            INNER JOIN camlis_std_test st ON st.\"ID\" = sst.test_id
                            INNER JOIN camlis_std_department_sample ds ON ds.\"ID\" = sst.department_sample_id
                            INNER JOIN camlis_std_department d ON d.\"ID\" = ds.department_id 
                        WHERE
                        concat( pv.received_date,' ',pv.received_time) >= '".$startDateTime."'
                        AND concat( pv.received_date, ' ', pv.received_time) <= '".$endDateTime."' 
                            AND pv.\"labID\" = '".$this->session->userdata('laboratory')->labID."' 
                            AND sst.group_result IS NOT NULL 
                        GROUP BY
                            d.department_name,
                            sst.group_result
                        ) AS atbl 
                    GROUP BY
                        ROLLUP (Title, name_kh) ORDER BY Title
        ";
        $result=$this->db->query($sql)->result_array(); 
        return $result;		
	}
	function aggregate_table($obj){
        $startDateTime = $obj->start.' '.$obj->start_time;
        $endDateTime = $obj->end.' '.$obj->end_time;
        $receivedDateCond  = " AND concat( psample.received_date,' ',psample.received_time) >= '".$startDateTime."'";
        $receivedDateCond .= " AND concat( psample.received_date,' ',psample.received_time) <= '".$endDateTime."'";
        $lab_id = $this->session->userdata('laboratory')->labID;
        $this->create_temp_patients_v($receivedDateCond, $lab_id);
        $sql = "
            WITH pv AS (
                SELECT *
                FROM camlis_patient_sample AS psample
                WHERE psample.status = 1 
                    AND psample.\"labID\" = ".$lab_id."
                    AND concat(psample.received_date, ' ', psample.received_time) >= '".$startDateTime."'
                    AND concat(psample.received_date, ' ', psample.received_time) <= '".$endDateTime."'
            ), tbl1 AS (
                SELECT DISTINCT '1.0. Distribution by province of patient' AS Title,
                    name_kh,
                    name_en,
                    COUNT(CASE WHEN sex = 'F' THEN 1 ELSE NULL END) AS female,
                    COUNT(CASE WHEN sex = 'M' THEN 1 ELSE NULL END) AS male,
                    SUM(CASE WHEN sex = 'F' THEN 1 WHEN sex = 'M' THEN 1  ELSE 0 END) AS total
                FROM (
                    SELECT
                        pr.name_kh,
                        pr.name_en,
                        P.sex,
                        P.patient_name,
                        pv.received_date
                    FROM provinces pr
                    INNER JOIN temp_patients_v P ON pr.code = P.province
                    INNER JOIN pv ON pv.patient_id = P.pid        
                    GROUP BY P.pid,
                            P.sex,
                            P.patient_name,
                            pr.name_kh,
                            pr.name_en,
                            pv.sample_number,
                            pv.received_date
                ) AS tbl
                GROUP BY name_kh,
                        name_en
            ), tbl2 AS (
                SELECT
                    '2.0. Distribution by age group' AS Title,
                    name_kh,
                    name_en,
                    COUNT(CASE WHEN sex = 'F' THEN 1 ELSE NULL END) AS female,
                    COUNT(CASE WHEN sex = 'M' THEN 1 ELSE NULL END) AS male,
                    COUNT(sex) AS total
                FROM (
                    SELECT
                        P.pid,
                        '1: 0 - 29 days' AS name_kh,
                        '1: 0 - 29 days' AS name_en,
                        P.sex,
                        P.patient_name,
                        pv.received_date
                    FROM temp_patients_v P  
                    INNER JOIN pv ON pv.patient_id = P.pid
                    WHERE P._day <= 30
                        AND P._month = 0
                        AND P._year = 0
            
                    UNION ALL
                    SELECT
                        P.pid,
                        '2: 1 - 11 m' AS name_kh,
                        '2: 1 - 11 m' AS name_en,
                        P.sex,
                        P.patient_name,
                        pv.received_date
                    FROM temp_patients_v P
                    INNER JOIN pv ON pv.patient_id = P.pid
                    WHERE P._month >= 1
                        AND P._month <= 12
                        AND P._year = 0
            
                    UNION ALL
                    SELECT
                        P.pid,
                        '3: 1 y - 4 y' AS name_kh,
                        '3: 1 y - 4 y' AS name_en,
                        P.sex,
                        P.patient_name,
                        pv.received_date
                    FROM temp_patients_v P
                    INNER JOIN pv ON pv.patient_id = P.pid
                    WHERE P._year >= 1
                        AND P._year <= 4
            
                    UNION ALL
                    SELECT
                        P.pid,
                        '4: 5 y - 23 y' AS name_kh,
                        '4: 5 y - 23 y' AS name_en,
                        P.sex,
                        P.patient_name,
                        pv.received_date
                    FROM temp_patients_v P
                    INNER JOIN pv ON pv.patient_id = P.pid
                    WHERE P._year >= 5
                        AND P._year <= 23
                    UNION ALL
            
                    SELECT
                        P.pid,
                        '5: 15 y - 24 y' AS name_kh,
                        '5: 15 y - 24 y' AS name_en,
                        P.sex,
                        P.patient_name,
                        pv.received_date
                    FROM temp_patients_v P
                    INNER JOIN pv ON pv.patient_id = P.pid
                    WHERE P._year >= 15
                        AND P._year <= 24
            
                    UNION ALL
                    SELECT
                        P.pid,
                        '6: 25 y - 49 y' AS name_kh,
                        '6: 25 y - 49 y' AS name_en,
                        P.sex,
                        P.patient_name,
                        pv.received_date
                    FROM temp_patients_v P
                    INNER JOIN pv ON pv.patient_id = P.pid
                    WHERE P._year >= 25
                        AND P._year <= 49
                    UNION ALL
                    SELECT
                        P.pid,
                        '7: 50 y - 64 y' AS name_kh,
                        '7: 50 y - 64 y' AS name_en,
                        P.sex,
                        P.patient_name,
                        pv.received_date
                    FROM temp_patients_v P
                    INNER JOIN pv ON pv.patient_id = P.pid
                    WHERE P._year >= 50
                        AND P._year <= 64
                    UNION ALL
                    SELECT
                        P.pid,
                        '8: >= 65 y' AS name_kh,
                        '8: >= 65 y' AS name_en,
                        P.sex,
                        P.patient_name,
                        pv.received_date
                    FROM temp_patients_v P
                    INNER JOIN pv ON pv.patient_id = P.pid
                    WHERE P._year >= 65
                ) AS P
                GROUP BY name_en, name_kh /* end aging */
            ), tbl3 AS (
                SELECT
                    Title,
                    name_kh,
                    name_en,
                    COUNT(CASE WHEN sex = 'F' THEN 1 ELSE NULL END) AS female,
                    COUNT(CASE WHEN sex = 'M' THEN 1 ELSE NULL END) AS male,
                    COUNT(total) AS total
                FROM 
                (
                    SELECT
                        '3.0. Distribution by Sample Source' AS Title,
                        ss.source_name AS name_kh,
                        ss.source_name AS name_en,
                        P.sex,
                        COUNT(P.sex) AS total
                    FROM temp_patients_v P
                    INNER JOIN pv ON pv.patient_id = P.pid  AND pv.status = 1
                    INNER JOIN camlis_lab_sample_source ss ON ss.\"ID\" = pv.sample_source_id        
                    GROUP BY ss.source_name, P.pid, P.sex, pv.sample_number
                ) dis3
                GROUP BY Title, name_kh, name_en
            ), tbl4 AS (
                SELECT
                    '4.0. Distribution by Payment type' AS Title,
                    spt.NAME AS name_kh,
                    spt.NAME AS name_en,
                COUNT(CASE WHEN P.sex = 'F' THEN 1 ELSE NULL END) AS female,
                COUNT(CASE WHEN P.sex = 'M' THEN 1 ELSE NULL END) AS male,
                COUNT(P.sex) AS total
                FROM temp_patients_v P
                INNER JOIN pv ON pv.patient_id = P.pid
                INNER JOIN camlis_lab_payment_type lpt ON lpt.payment_type_id = pv.payment_type_id
                INNER JOIN camlis_std_payment_type spt ON spt.id = lpt.payment_type_id
                WHERE lpt.lab_id = ".$lab_id."
                GROUP BY spt.NAME
            ), tbl5 AS (
                SELECT
                    '5.0. Distribution by Sample status' AS Title,
                    name_kh,
                    name_en,
                    SUM(female) AS female,
                    SUM(male) AS male,
                    SUM(total) AS total
                FROM ( /*13-02-2021*/
                    SELECT
                        'សង្ស័យ' AS name_kh,
                        'Suspect' AS name_en,
                        COUNT(CASE WHEN P.sex = 'M' THEN 1 ELSE NULL END) AS male,
                        COUNT(CASE WHEN P.sex = 'F' THEN 1 ELSE NULL END) AS female,
                        COUNT(P.sex) AS total
                    FROM temp_patients_v P
                    INNER JOIN pv  ON pv.patient_id = P.pid
                    WHERE pv.for_research = 1
                    UNION ALL
                    SELECT
                        'រលាកសួត' AS name_kh,
                        'Pneumonia' AS name_en,
                        COUNT(CASE WHEN P.sex = 'M' THEN 1 ELSE NULL END) AS male,
                        COUNT(CASE WHEN P.sex = 'F' THEN 1 ELSE NULL END) AS female,
                        COUNT(P.sex) AS total
                    FROM temp_patients_v P
                    INNER JOIN pv ON pv.patient_id = P.pid
                    WHERE concat(pv.received_date, ' ', pv.received_time) >= '2022-04-01 00:00'
                    AND pv.for_research = 2
                    UNION ALL
                    SELECT
                        'តាមដាន' AS name_kh,
                        'Follow Up' AS name_en,
                        COUNT(CASE WHEN P.sex = 'M' THEN 1 ELSE NULL END) AS male,
                        COUNT(CASE WHEN P.sex = 'F' THEN 1 ELSE NULL END) AS female,
                        COUNT(P.sex) AS total
                    FROM temp_patients_v P
                    INNER JOIN pv ON pv.patient_id = P.pid
                    WHERE pv.for_research = 3
                    UNION ALL
                    SELECT
                        'អ្នកប៉ះពាល់' AS name_kh,
                        'Contact' AS name_en,
                        COUNT(CASE WHEN P.sex = 'M' THEN 1 ELSE NULL END) AS male,
                        COUNT(CASE WHEN P.sex = 'F' THEN 1 ELSE NULL END) AS female,
                        COUNT(P.sex) AS total
                    FROM temp_patients_v P
                    INNER JOIN pv ON pv.patient_id = P.pid
                    WHERE pv.for_research = 4
                    UNION ALL
                    SELECT
                        'បុគ្គលិកពេទ្យ' AS name_kh,
                        'HCW' AS name_en,
                        COUNT(CASE WHEN P.sex = 'M' THEN 1 ELSE NULL  END) AS male,
                        COUNT(CASE WHEN P.sex = 'F' THEN 1 ELSE NULL  END) AS female,
                        COUNT(P.sex) AS total
                    FROM temp_patients_v P
                    INNER JOIN pv ON pv.patient_id = P.pid
                    WHERE pv.for_research = 5
            
                    UNION ALL
                    SELECT
                        'ផ្សេងទៀត' AS name_kh,
                        'Other' AS name_en,
                        COUNT(CASE
                            WHEN P.sex = 'M' THEN 1
                            ELSE NULL
                        END) AS male,
                        COUNT(CASE
                            WHEN P.sex = 'F' THEN 1
                            ELSE NULL
                        END) AS female,
                        COUNT(P.sex) AS total
                    FROM temp_patients_v P
                    INNER JOIN pv ON pv.patient_id = P.pid
                    WHERE pv.for_research = 6
                    UNION ALL
                    SELECT
                        'ពលករចំណាកស្រុក' AS name_kh,
                        'Migrants' AS name_en,
                        COUNT(CASE
                            WHEN P.sex = 'M' THEN 1
                            ELSE NULL
                        END) AS male,
                        COUNT(CASE
                            WHEN P.sex = 'F' THEN 1
                            ELSE NULL
                        END) AS female,
                        COUNT(P.sex) AS total
                    FROM temp_patients_v P
                    INNER JOIN pv ON pv.patient_id = P.pid
                    WHERE pv.for_research = 7
                    UNION ALL
                    SELECT
                        'អ្នកដំណើរតាមយន្តហោះ' AS name_kh,
                        'Passenger' AS name_en,
                        COUNT(CASE
                            WHEN P.sex = 'M' THEN 1
                            ELSE NULL
                        END) AS male,
                        COUNT(CASE
                            WHEN P.sex = 'F' THEN 1
                            ELSE NULL
                        END) AS female,
                        COUNT(P.sex) AS total
                    FROM temp_patients_v P
                    INNER JOIN  pv ON pv.patient_id = P.pid
                    WHERE  pv.for_research = 8
            
                    UNION ALL
                    SELECT
                        'វិញ្ញាបនបត្រ' AS name_kh,
                        'Certificate' AS name_en,
                        COUNT(CASE WHEN P.sex = 'M' THEN 1 ELSE NULL END) AS male,
                        COUNT(CASE WHEN P.sex = 'F' THEN 1 ELSE NULL END) AS female,
                        COUNT(P.sex) AS total
                    FROM temp_patients_v P
                    INNER JOIN pv ON pv.patient_id = P.pid
                    WHERE pv.for_research = 9
                    UNION ALL /* End 13-02-2021*/
                    SELECT
                        'Urgent' AS name_kh,
                        'Urgent' AS name_en,
                        COUNT(CASE WHEN P.sex = 'M' THEN 1 ELSE NULL END) AS male,
                        COUNT(CASE WHEN P.sex = 'F' THEN 1 ELSE NULL END) AS female,
                        COUNT(P.sex) AS total
                    FROM temp_patients_v P
                    INNER JOIN pv ON pv.patient_id = P.pid
                    WHERE pv.is_urgent = 1
                ) AS pv
                GROUP BY name_en, name_kh /* Display by sample type */
            ), tbl6 AS (
                SELECT
                    '6.0. Distribution by Sample Type' AS Title,
                    sample_name AS name_kh,
                    '' AS name_en,
                    COUNT(CASE WHEN sex = 'F' THEN 1 ELSE NULL END) AS female,
                    COUNT(CASE WHEN sex = 'M' THEN 1 ELSE NULL END) AS male,
                    COUNT(sex) AS total
                FROM (
                    SELECT
                        P.sex,
                        ss.sample_name,
                        P.patient_name,
                        psd.department_sample_id
                    FROM temp_patients_v P
                    INNER JOIN pv ON pv.patient_id = P.pid
                    AND P.status = 1
                    INNER JOIN camlis_patient_sample_detail psd ON psd.patient_sample_id = pv.\"ID\"
                    INNER JOIN camlis_std_department_sample sds ON sds.\"ID\" = psd.department_sample_id
                    INNER JOIN camlis_std_sample ss ON ss.\"ID\" = sds.sample_id        
                ) v
                GROUP BY patient_name,            name_kh,            department_sample_id /* Display by sample rejection*/
            ), tbl7 AS (
                SELECT
                    '7.0. Distribution by Sample Rejection' AS Title,
                    rs.sample_name AS name_kh,
                    rs.sample_name AS name_en,
                    SUM(rs.male) AS male,
                    SUM(rs.female) AS female,
                    COUNT(rs.sample_name) AS total
                FROM (
                    SELECT
                        ss.sample_name,
                    CASE WHEN P.sex = 'M' THEN 1 ELSE NULL END AS male,
                    CASE WHEN P.sex = 'F' THEN 1 ELSE NULL END AS female
                    FROM temp_patients_v P
                    INNER JOIN pv ON pv.patient_id = P.pid
                    INNER JOIN camlis_patient_sample_tests pst ON pst.patient_sample_id = pv.\"ID\"
                    INNER JOIN camlis_std_sample_test sst ON sst.\"ID\" = pst.sample_test_id
                    INNER JOIN camlis_std_department_sample sds ON sds.\"ID\" = sst.department_sample_id
                    INNER JOIN camlis_std_sample ss ON ss.\"ID\" = sds.sample_id
                    WHERE P.status = 1
                    AND pst.is_rejected = TRUE
                    GROUP BY P.pid,
                            P.sex,
                            pv.sample_number,
                            ss.sample_name
                ) rs
                GROUP BY rs.sample_name /* Display by department and test*/
            ), tbl8 AS (
                SELECT
                    concat('8.0. Distribution by ', ' ', d.department_name) AS Title,
                    sst.group_result AS name_kh,
                    '' AS name_en,
                    COUNT(CASE WHEN P.sex = 'F' THEN 1 ELSE NULL END) AS female,
                    COUNT(CASE WHEN P.sex = 'M' THEN 1 ELSE NULL END) AS male,
                    COUNT(P.sex) AS total
                FROM temp_patients_v P
                INNER JOIN pv ON pv.patient_id = P.pid
                INNER JOIN camlis_patient_sample_tests pst ON pst.patient_sample_id = pv.\"ID\"
                AND pst.status = 1
                INNER JOIN camlis_std_sample_test sst ON sst.\"ID\" = pst.sample_test_id
                INNER JOIN camlis_std_test st ON st.\"ID\" = sst.test_id
                INNER JOIN camlis_std_department_sample ds ON ds.\"ID\" = sst.department_sample_id
                INNER JOIN camlis_std_department d ON d.\"ID\" = ds.department_id
                WHERE sst.group_result IS NOT NULL
                GROUP BY d.department_name,
                        sst.group_result
            ) 
            SELECT * FROM tbl1 UNION ALL 
            SELECT * FROM tbl2 UNION ALL
            SELECT * from tbl3 UNION ALL
            SELECT * from tbl4 UNION ALL
            SELECT * from tbl5 UNION ALL    
            SELECT tbl6.title , tbl6.name_kh, '' AS name_en, sum(tbl6.female) as female , sum(tbl6.male) as male , sum(tbl6.total) as total from tbl6 group by title, name_kh UNION ALL
            SELECT * from tbl7 UNION ALL
            SELECT * from tbl8
            ORDER by title, name_kh
        ";
        echo $sql;
        $result=$this->db->query($sql)->result_array(); 
        return $result;		
	}
	/* this function using for get patient base the sample source count gender
	 * @parameters date start and date end
	 * group by sample source name and test name
	 * roll up the fields
	 */
	function ward_table($obj){ 
        $sql = "
            SELECT
            CASE
                    
                WHEN
                    name_kh != '' THEN
                        name_kh 
                        WHEN Title != '' THEN
                        'Sub total' ELSE'Grand total' 
                    END AS distribute,
                    Title,
                    name_kh,
                    SUM ( male ) AS male,
                    SUM ( female ) AS female,
                    SUM ( total ) AS total 
                FROM
                    (
                    SELECT
                        concat ( 'Distribution by ', ' ', source_name ) AS Title,
                        group_result AS name_kh,
                        '' AS name_en,
                        COUNT ( CASE WHEN sex = 'F' THEN 1 ELSE NULL END ) AS female,
                        COUNT ( CASE WHEN sex = 'M' THEN 1 ELSE NULL END ) AS male,
                        COUNT ( sex ) AS total 
                    FROM
                        (
                        SELECT P
                            .pid,
                            P.patient_name,
                            P.sex,
                            ss.source_name,
                            T.test_name,
                            ps.received_date,
                            group_result 
                        FROM
                            patient_v P
                            INNER JOIN camlis_patient_sample ps ON ps.patient_id = P.pid
                            INNER JOIN camlis_lab_sample_source ss ON ss.\"ID\" = ps.sample_source_id
                            INNER JOIN camlis_patient_sample_tests pst ON pst.patient_sample_id = ps.\"ID\" AND pst.status = 1
                            INNER JOIN camlis_std_sample_test st ON st.\"ID\" = pst.sample_test_id
                            INNER JOIN camlis_std_test T ON T.\"ID\" = st.test_id 
                        WHERE
                            ps.\"labID\" = '".$this->session->userdata('laboratory')->labID."' 
                            AND to_char ( ps.received_date, 'YYYY-mm-dd' ) >= ? 
                            AND to_char ( ps.received_date, 'YYYY-mm-dd' ) <= ? 
                            AND ps.received_time BETWEEN ? AND ? 
                            AND st.group_result IS NOT NULL 
                        ) AS v 
                    GROUP BY
                        source_name,
                        group_result 
                    ) AS atbl 
                GROUP BY
                    ROLLUP (Title, name_kh)
        ";
        $result=$this->db->query($sql,array($obj->start,$obj->end, $obj->start_time, $obj->end_time))->result_array();
        return $result;		
    }
    /*
	function Rejection_Sample($obj){ 
		
		$sql = 'select rs.source_name,rs.sample_name as Title,rs.reject_comment,count(rs.sample_number) as total
		from (select pv.sample_number,lss.source_name,ss.sample_name,pv.reject_comment from camlis_patient_sample pv
		inner join camlis_patient_sample_tests pst on pst.patient_sample_id=pv."ID"
		inner join camlis_std_sample_test sst on sst."ID"=pst.sample_test_id
		inner join camlis_std_department_sample sds on sds."ID"=sst.department_sample_id
		inner join camlis_std_sample ss on ss."ID"=sds.sample_id
		inner join camlis_lab_sample_source lss on lss."ID"=pv.sample_source_id
		where pv."labID"=\''.$this->session->userdata('laboratory')->labID.'\'
		and to_char(pv.received_date, \'YYYY-mm-dd\') >= ?
		and to_char(pv.received_date, \'YYYY-mm-dd\') <= ?
		and pv.received_time BETWEEN ? AND ? 
		and pv.status=1
		and pst.status=1
		and pst.is_rejected = TRUE
		group by pv.sample_number,lss.source_name,ss.sample_name,pv.reject_comment) rs
		group by rs.source_name,rs.sample_name,rs.reject_comment
		order by rs.sample_name,rs.source_name,rs.reject_comment';
				
		$result=$this->db->query($sql,array($obj->start,$obj->end, $obj->start_time, $obj->end_time))->result_array();
		return $result;
			
				
	}
    */
    function Gen_sample_reject($con){
		$sql_reject="CREATE TEMPORARY TABLE temp_ps AS select pv.sample_source_id,pv.reject_comment, pv.received_date,pv.received_time, pv.sample_number,pv.\"ID\",pv.\"labID\" from camlis_patient_sample as pv ".$con."
		and pv.status=1 and pv.is_rejected=1";
		$this->db->query($sql_reject);
	}
    function Rejection_Sample($obj){ 		
		$con=" WHERE pv.\"labID\"='".$this->laboratory_id."' and concat(pv.received_date,' ',pv.received_time) >= '".$obj->start.' '.$obj->start_time."' AND concat(pv.received_date,' ',pv.received_time) <= '".$obj->end.' '.$obj->end_time."' ";
		$this->Gen_sample_reject($con);
		$sql = "select rs.source_name,rs.sample_name as Title,rs.reject_comment,count(rs.sample_number) as total
		from (select ps.sample_number,lss.source_name,ss.sample_name,rc.reject_comment 
		from temp_ps as ps
		inner join camlis_result_comment rc on rc.patient_sample_id=ps.\"ID\"
		inner join camlis_patient_sample_tests pst on pst.patient_sample_id=ps.\"ID\"
		inner join camlis_std_sample_test sst on sst.\"ID\"=pst.sample_test_id
		inner join camlis_std_department_sample sds on sds.\"ID\" = sst.department_sample_id
		inner join camlis_std_sample ss on ss.\"ID\"=sds.sample_id
		inner join camlis_lab_sample_source lss on lss.\"ID\"=ps.sample_source_id
		group by ps.sample_number,lss.source_name,ss.sample_name,rc.reject_comment ) rs
		group by rs.source_name,rs.sample_name,rs.reject_comment
		order by rs.sample_name,rs.source_name,rs.reject_comment";

		$result=$this->db->query($sql)->result_array();
		return $result;		
	}
	function aggregate_sql_short($data){ 
	
		$table		= 'patients';
		$primaryKey	= 'pid'; 
		$columns	= array(
							array(
								'db'		=> '`ppt`.pid as code', 
								'dt'		=> 'code',
								'field'		=> 'code' 
							),
							array(
								'db'		=> '`pp`.name_kh',
								'dt'		=> 'name_kh',
								'field'		=> 'name_kh'
							),
							array(
								'db'		=> '`pp`.name_en',
								'dt'		=> 'name_en',
								'field'		=> 'name_en'
							),array(
								'db'		=> 'COUNT(CASE WHEN `ppt`.sex = "M" THEN 1 END)', 'as'=> 'male',   
								'dt'		=> 'male',
								'field'		=> 'male' 
							),array(
								'db'		=> 'COUNT(CASE WHEN `ppt`.sex = "F" THEN 1 END)', 'as'=> 'female',
								'dt'		=> 'female',
								'field'		=> 'female' 
							),array(
								'db'		=> 'sum(CASE WHEN `ppt`.sex = "F" THEN 1 WHEN `ppt`.sex = "M" THEN 1 ELSE 0 END)', 'as'=> 'total',
								'dt'		=> 'total',
								'field'		=> 'total' 
							)
						);
		
		
		$joinQuery	= " FROM `provinces` `pp`
						INNER  `patients` `ppt` ON `ppt`.`province` = `pp`.`code` 
					
					";
		
		
		//			
		$dstart = DateTime::createFromFormat('d/m/Y', $data->reqData['start']==''?date('d/m/Y'):$data->reqData['start']);
		$dend = DateTime::createFromFormat('d/m/Y', $data->reqData['end']==''?date('d/m/Y'):$data->reqData['end']);
 
		// where condition						
		$extraWhere	= " 
						`ppt`.status = TRUE
						and date_format(`ppt`.entrydate,'%Y-%m-%d') >= '".$dstart->format('Y-m-d')."'
						and date_format(`ppt`.entrydate,'%Y-%m-%d') <= '".$dend->format('Y-m-d')."'
					";
		// group by
		$groupBy =  " `ppt`.province ";
		 
		//echo date('Y-m-d',strtotime($data->reqData['start']));
		//$data->reqData['end'];
		  
		
		//config
		
		$db_config		= $this->load->database('pmrs', TRUE); 
		$sql_details	= array(
			'user'	=> $db_config->username,
			'pass'	=> $db_config->password,
			'port'	=> $db_config->port,
			'db'	=> $db_config->database,
			'host'	=> $db_config->hostname
		);
		
		$this->load->library('DataTable');
		$result = DataTable::simple( $data->reqData, $sql_details, $table, $primaryKey, $columns, $joinQuery, $extraWhere,$groupBy);
		
		for ($i=0; $i < count($result['data']); $i++) {
			$result['data'][$i]['number'] = $i + 1;
		}
		
		return $result;
		  
	}
	/**
	 * Get Sample for view_all using Datatable
	 */
	public function aggregate_sql($data) { 
		
		$table		= 'province_patient_v';
		$primaryKey	= 'pid'; 
		$columns	= array(
							array(
								'db'		=> '`ppt`.pid as code', 
								'dt'		=> 'code',
								'field'		=> 'code' 
							),
							array(
								'db'		=> '`ppt`.name_kh',
								'dt'		=> 'name_kh',
								'field'		=> 'name_kh'
							),
							array(
								'db'		=> '`ppt`.name_en',
								'dt'		=> 'name_en',
								'field'		=> 'name_en'
							),array(
								'db'		=> 'COUNT(CASE WHEN `ppt`.sex = "M" THEN 1 END)', 'as'=> 'male',   
								'dt'		=> 'male',
								'field'		=> 'male' 
							),array(
								'db'		=> 'COUNT(CASE WHEN `ppt`.sex = "F" THEN 1 END)', 'as'=> 'female',
								'dt'		=> 'female',
								'field'		=> 'female' 
							),array(
								'db'		=> 'sum(CASE WHEN `ppt`.sex = "F" THEN 1 WHEN `ppt`.sex = "M" THEN 1 ELSE 0 END)', 'as'=> 'total',
								'dt'		=> 'total',
								'field'		=> 'total' 
							)
						);
		
		
		$joinQuery	= " FROM province_patient_v as ppt ";
		
		
		//			
		$dstart = DateTime::createFromFormat('d/m/Y', $data->reqData['start']==''?date('d/m/Y'):$data->reqData['start']);
		$dend = DateTime::createFromFormat('d/m/Y', $data->reqData['end']==''?date('d/m/Y'):$data->reqData['end']);
 
		// where condition						
		$extraWhere	= " 
						`ppt`.status = TRUE
						and date_format(`ppt`.entrydate,'%Y-%m-%d') >= '".$dstart->format('Y-m-d')."'
						and date_format(`ppt`.entrydate,'%Y-%m-%d') <= '".$dend->format('Y-m-d')."'
						
						group by `ppt`.name_kh with rollup
					";
		// group by
		//$groupBy =  " `ppt`.name_kh with rollup";
		 
		//echo date('Y-m-d',strtotime($data->reqData['start']));
		//$data->reqData['end'];
		  
		
		//config
		
		$db_config		= $this->load->database('default', TRUE); 
		$sql_details	= array(
			'user'	=> $db_config->username,
			'pass'	=> $db_config->password,
			'port'	=> $db_config->port,
			'db'	=> $db_config->database,
			'host'	=> $db_config->hostname
		);
		
		$this->load->library('DataTable');
		$result = DataTable::simple( $data->reqData, $sql_details, $table, $primaryKey, $columns, $joinQuery, $extraWhere);
		
		for ($i=0; $i < count($result['data']); $i++) {
			$result['data'][$i]['number'] = $i + 1;
		}
		
		return $result;
	}
	
	// $this->laboratory_id
	function load_labo(){
        $labId = ($this->aauth->is_admin()==true)?0:$this->laboratory_id;
		$sql = " select * from camlis_laboratory where (\"labID\" =? or 0=?)";
		$result=$this->db->query($sql,array($labId,$labId))->result_array();
        return $result;
	}
	// 
	function load_dept(){
		$sql = " select * from camlis_std_department ";
		$result=$this->db->query($sql)->result_array();
        return $result;
	}
	// 
	function load_sample(){
		$sql = "select 
                    ss.* 
                from camlis_std_sample ss
                inner join camlis_std_department_sample ds on ds.sample_id = ss.\"ID\"
                inner join camlis_std_sample_test st on st.department_sample_id = ds.\"ID\" and test_id = 170
                ";
		$result=$this->db->query($sql)->result_array();
        return $result;
	}
	// 
	function load_test(){
		$sql = " select * from camlis_std_test ";
		$result=$this->db->query($sql)->result_array();
        return $result;
	}
	// 
	function load_result($obj,$con){
        $sql = "SELECT 
            lab_code as lab_name,
            patient_code as patient_id,
            sample_number as lab_id,
            sex,
            dob,
            patient_age as age,
            sample_name as sample,
            description as sample_site,
            sample_volume1 as volume1,
            sample_volume2 as volume2,
            source_name as sample_source,
            concat(collected_date, ' ',collected_time) as collection_date,
            admission_date,
            test_date,
            diagnosis,
            contaminant,
            results,
                                                            
            MAX ( CASE WHEN (antibiotic_id)::TEXT = '1' THEN sensitivity ELSE '' END ) AS Amikacin,
            MAX ( CASE WHEN (antibiotic_id)::TEXT = '2' THEN sensitivity ELSE '' END ) AS Amoxi_Clav,
            MAX ( CASE WHEN (antibiotic_id)::TEXT = '3' THEN sensitivity ELSE '' END ) AS Ampi_Peni,
            MAX ( CASE WHEN (antibiotic_id)::TEXT = '4' THEN sensitivity ELSE '' END ) AS Ampicillin,
            MAX ( CASE WHEN (antibiotic_id)::TEXT = '5' THEN sensitivity ELSE '' END ) AS Azithromycin,
            MAX ( CASE WHEN (antibiotic_id)::TEXT = '6' THEN sensitivity ELSE '' END ) AS Cefazolin,
            MAX ( CASE WHEN (antibiotic_id)::TEXT = '7' THEN sensitivity ELSE '' END ) AS Cefepime,
            MAX ( CASE WHEN (antibiotic_id)::TEXT = '8' THEN sensitivity ELSE '' END ) AS Cefotaxime,
            MAX ( CASE WHEN (antibiotic_id)::TEXT = '9' THEN sensitivity ELSE '' END ) AS Ceftazidime,
            MAX ( CASE WHEN (antibiotic_id)::TEXT = '10' THEN sensitivity ELSE '' END ) AS Ceftriaxone,
            MAX ( CASE WHEN (antibiotic_id)::TEXT = '11' THEN sensitivity ELSE '' END ) AS Cephalosporins,
            MAX ( CASE WHEN (antibiotic_id)::TEXT = '12' THEN sensitivity ELSE '' END ) AS Cephalothin,
            MAX ( CASE WHEN (antibiotic_id)::TEXT = '13' THEN sensitivity ELSE '' END ) AS Chloramphenicol,
            MAX ( CASE WHEN (antibiotic_id)::TEXT = '14' THEN sensitivity ELSE '' END ) AS Ciprofloxacin,
            MAX ( CASE WHEN (antibiotic_id)::TEXT = '15' THEN sensitivity ELSE '' END ) AS Clindamycin,
            MAX ( CASE WHEN (antibiotic_id)::TEXT = '16' THEN sensitivity ELSE '' END ) AS Cloxacillin,
            MAX ( CASE WHEN (antibiotic_id)::TEXT = '17' THEN sensitivity ELSE '' END ) AS Erythromycin,
            MAX ( CASE WHEN (antibiotic_id)::TEXT = '19' THEN sensitivity ELSE '' END ) AS Fosfomycin,
            MAX ( CASE WHEN (antibiotic_id)::TEXT = '18' THEN sensitivity ELSE '' END ) AS Fluoroquinolone,
            MAX ( CASE WHEN (antibiotic_id)::TEXT = '20' THEN sensitivity ELSE '' END ) AS Gentamicin,
            MAX ( CASE WHEN (antibiotic_id)::TEXT = '21' THEN sensitivity ELSE '' END ) AS Imipenem,
            MAX ( CASE WHEN (antibiotic_id)::TEXT = '22' THEN sensitivity ELSE '' END ) AS Levofloxacin,
            MAX ( CASE WHEN (antibiotic_id)::TEXT = '23' THEN sensitivity ELSE '' END ) AS Meropenem,
            MAX ( CASE WHEN (antibiotic_id)::TEXT = '24' THEN sensitivity ELSE '' END ) AS Metronidazole,
            MAX ( CASE WHEN (antibiotic_id)::TEXT = '25' THEN sensitivity ELSE '' END ) AS Minocycline,
            MAX ( CASE WHEN (antibiotic_id)::TEXT = '26' THEN sensitivity ELSE '' END ) AS Nalidixic_acid,
            MAX ( CASE WHEN (antibiotic_id)::TEXT = '27' THEN sensitivity ELSE '' END ) AS Nitrofurantoin,
            MAX ( CASE WHEN (antibiotic_id)::TEXT = '28' THEN sensitivity ELSE '' END ) AS Norfloxacin,
            MAX ( CASE WHEN (antibiotic_id)::TEXT = '29' THEN sensitivity ELSE '' END ) AS Oxacillin,
            MAX ( CASE WHEN (antibiotic_id)::TEXT = '30' THEN sensitivity ELSE '' END ) AS Penicilin,
            MAX ( CASE WHEN (antibiotic_id)::TEXT = '31' THEN sensitivity ELSE '' END ) AS Penicillin,
            MAX ( CASE WHEN (antibiotic_id)::TEXT = '32' THEN sensitivity ELSE '' END ) AS Tetracycline,
            MAX ( CASE WHEN (antibiotic_id)::TEXT = '33' THEN sensitivity ELSE '' END ) AS Trimeth_Sulfa,
            MAX ( CASE WHEN (antibiotic_id)::TEXT = '34' THEN sensitivity ELSE '' END ) AS Vancomycin,
            MAX ( CASE WHEN (antibiotic_id)::TEXT = '35' THEN sensitivity ELSE '' END ) AS Pip_tazobactam,
            MAX ( CASE WHEN (antibiotic_id)::TEXT = '36' THEN sensitivity ELSE '' END ) AS Penicillin_meningitis,
            MAX ( CASE WHEN (antibiotic_id)::TEXT = '37' THEN sensitivity ELSE '' END ) AS Penicillin_non_meningitis,
            MAX ( CASE WHEN (antibiotic_id)::TEXT = '38' THEN sensitivity ELSE '' END ) AS Ceftriaxone_meningitis,
            MAX ( CASE WHEN (antibiotic_id)::TEXT = '39' THEN sensitivity ELSE '' END ) AS Ceftriaxone_non_meningitis,
            MAX ( CASE WHEN (antibiotic_id)::TEXT = '40' THEN sensitivity ELSE '' END ) AS Gentamicin_synergy,
            MAX ( CASE WHEN (antibiotic_id)::TEXT = '41' THEN sensitivity ELSE '' END ) AS Oral_Cephalosporins,
            MAX ( CASE WHEN (antibiotic_id)::TEXT = '42' THEN sensitivity ELSE '' END ) AS Novobiocin,			
            comment,
            reject_comment
            FROM 
            ( SELECT
                    stv.patient_id,
                    stv.patient_code,
                    stv.labid,  
                    l.name_en AS laboname,
                    l.name_kh,
                    l.lab_code,
                    stv.sample_number,
                    stv.sex,
                    stv.dob,
                    date_part('year'::text, age(stv.collected_date, (stv.dob)::timestamp with time zone)) AS patient_age,
                    stv.department_name,
                    s.sample_name,
                    lss.source_name,
                    to_char ( stv.test_date, 'dd-mm-YYYY' ) AS test_date,
                    stv.test_name,
                    pr.organism_name AS results,
                    pr.contaminant,
                    pr.quantity,
                    to_char ( stv.collected_date, 'dd-mm-YYYY' ) AS collected_date,
                    stv.diagnosis,
                    stv.description,
                    stv.sample_volume1,
                    stv.sample_volume2,
                    pr.antibiotic_id,
                    an.antibiotic_name,
                    ( CASE WHEN pr.sensitivity= 1 THEN 'S' WHEN pr.sensitivity= 2 THEN 'R' ELSE'I' END ) AS sensitivity,
                    psample.result_comment,
                    rescomm.result_comment AS comment,
                    stv.collected_time,
                    to_char ( stv.admission_date, 'dd-mm-YYYY HH24:MM' ) AS admission_date,
                    rescomm.reject_comment
                FROM
                    camlis_laboratory l  
                    inner join patient_sample_test_v stv on stv.labid = l.\"labID\" and stv.test_id=170
                    inner join camlis_patient_sample psample on stv.patient_sample_id = psample.\"ID\"
                    inner join camlis_lab_sample_source lss on lss.\"ID\"=stv.sample_source_id
                    inner join camlis_std_department_sample ds on ds.\"ID\" = stv.department_sample_id 
                    inner join camlis_std_sample s on s.\"ID\" = ds.sample_id
                    left join result_test_organism_v pr on pr.patient_test_id = stv.patient_sample_test_id
                    left join camlis_std_antibiotic an on an.\"ID\" = pr.antibiotic_id
                    left join camlis_result_comment rescomm on rescomm.patient_sample_id = stv.patient_sample_id and rescomm.department_sample_id = stv.department_sample_id
                    left join camlis_ptest_result presult on presult.patient_test_id = pr.patient_test_id and presult.status = 1
                    where l.status = TRUE and ds.department_id=4
                    ".$con."
            ) _tbl  
            GROUP BY                 
                lab_name,
                patient_code,
                lab_id,
                sex,
                dob,
                age,
                sample,
                sample_site,
                volume1,
                volume2,
                sample_source,
                collection_date,
                admission_date,
                test_date,
                diagnosis,
                contaminant,
                results,
                comment,
                reject_comment
            order by lab_name,results desc";
        echo $sql;

        exit();
		$result=$this->db->query($sql)->result_array();
        return $result;
	}

	/** Load Result v2 */

	function load_result_v2($obj, $con){

		$sql = "SELECT 
					lab_code as lab_name,
					patient_code as patient_id,
					sample_number as lab_id,
					sex,
					dob,
					patient_age as age,
					sample_name as sample,
					description as sample_site,
					sample_volume1 as volume1,
					sample_volume2 as volume2,
					source_name as sample_source,
					concat(collected_date, ' ',collected_time) as collection_date,
					admission_date,
					test_date,
					diagnosis,
					contaminant,
					results,
				
					MAX ( CASE WHEN (antibiotic_id)::TEXT = '1' THEN sensitivity ELSE '' END ) AS Amikacin,
					MAX ( CASE WHEN (antibiotic_id)::TEXT = '2' THEN sensitivity ELSE '' END ) AS Amoxi_Clav,
					MAX ( CASE WHEN (antibiotic_id)::TEXT = '3' THEN sensitivity ELSE '' END ) AS Ampi_Peni,
					MAX ( CASE WHEN (antibiotic_id)::TEXT = '4' THEN sensitivity ELSE '' END ) AS Ampicillin,
					MAX ( CASE WHEN (antibiotic_id)::TEXT = '5' THEN sensitivity ELSE '' END ) AS Azithromycin,
					MAX ( CASE WHEN (antibiotic_id)::TEXT = '6' THEN sensitivity ELSE '' END ) AS Cefazolin,
					MAX ( CASE WHEN (antibiotic_id)::TEXT = '7' THEN sensitivity ELSE '' END ) AS Cefepime,
					MAX ( CASE WHEN (antibiotic_id)::TEXT = '8' THEN sensitivity ELSE '' END ) AS Cefotaxime,
					MAX ( CASE WHEN (antibiotic_id)::TEXT = '9' THEN sensitivity ELSE '' END ) AS Ceftazidime,
					MAX ( CASE WHEN (antibiotic_id)::TEXT = '10' THEN sensitivity ELSE '' END ) AS Ceftriaxone,
					MAX ( CASE WHEN (antibiotic_id)::TEXT = '11' THEN sensitivity ELSE '' END ) AS Cephalosporins,
					MAX ( CASE WHEN (antibiotic_id)::TEXT = '12' THEN sensitivity ELSE '' END ) AS Cephalothin,
					MAX ( CASE WHEN (antibiotic_id)::TEXT = '13' THEN sensitivity ELSE '' END ) AS Chloramphenicol,
					MAX ( CASE WHEN (antibiotic_id)::TEXT = '14' THEN sensitivity ELSE '' END ) AS Ciprofloxacin,
					MAX ( CASE WHEN (antibiotic_id)::TEXT = '15' THEN sensitivity ELSE '' END ) AS Clindamycin,
					MAX ( CASE WHEN (antibiotic_id)::TEXT = '16' THEN sensitivity ELSE '' END ) AS Cloxacillin,
					MAX ( CASE WHEN (antibiotic_id)::TEXT = '17' THEN sensitivity ELSE '' END ) AS Erythromycin,
					MAX ( CASE WHEN (antibiotic_id)::TEXT = '19' THEN sensitivity ELSE '' END ) AS Fosfomycin,
					MAX ( CASE WHEN (antibiotic_id)::TEXT = '18' THEN sensitivity ELSE '' END ) AS Fluoroquinolone,
					MAX ( CASE WHEN (antibiotic_id)::TEXT = '20' THEN sensitivity ELSE '' END ) AS Gentamicin,
					MAX ( CASE WHEN (antibiotic_id)::TEXT = '21' THEN sensitivity ELSE '' END ) AS Imipenem,
					MAX ( CASE WHEN (antibiotic_id)::TEXT = '22' THEN sensitivity ELSE '' END ) AS Levofloxacin,
					MAX ( CASE WHEN (antibiotic_id)::TEXT = '23' THEN sensitivity ELSE '' END ) AS Meropenem,
					MAX ( CASE WHEN (antibiotic_id)::TEXT = '24' THEN sensitivity ELSE '' END ) AS Metronidazole,
					MAX ( CASE WHEN (antibiotic_id)::TEXT = '25' THEN sensitivity ELSE '' END ) AS Minocycline,
					MAX ( CASE WHEN (antibiotic_id)::TEXT = '26' THEN sensitivity ELSE '' END ) AS Nalidixic_acid,
					MAX ( CASE WHEN (antibiotic_id)::TEXT = '27' THEN sensitivity ELSE '' END ) AS Nitrofurantoin,
					MAX ( CASE WHEN (antibiotic_id)::TEXT = '28' THEN sensitivity ELSE '' END ) AS Norfloxacin,
					MAX ( CASE WHEN (antibiotic_id)::TEXT = '29' THEN sensitivity ELSE '' END ) AS Oxacillin,
					MAX ( CASE WHEN (antibiotic_id)::TEXT = '30' THEN sensitivity ELSE '' END ) AS Penicilin,
					MAX ( CASE WHEN (antibiotic_id)::TEXT = '31' THEN sensitivity ELSE '' END ) AS Penicillin,
					MAX ( CASE WHEN (antibiotic_id)::TEXT = '32' THEN sensitivity ELSE '' END ) AS Tetracycline,
					MAX ( CASE WHEN (antibiotic_id)::TEXT = '33' THEN sensitivity ELSE '' END ) AS Trimeth_Sulfa,
					MAX ( CASE WHEN (antibiotic_id)::TEXT = '34' THEN sensitivity ELSE '' END ) AS Vancomycin,
					MAX ( CASE WHEN (antibiotic_id)::TEXT = '35' THEN sensitivity ELSE '' END ) AS Pip_tazobactam,
					MAX ( CASE WHEN (antibiotic_id)::TEXT = '36' THEN sensitivity ELSE '' END ) AS Penicillin_meningitis,
					MAX ( CASE WHEN (antibiotic_id)::TEXT = '37' THEN sensitivity ELSE '' END ) AS Penicillin_non_meningitis,
					MAX ( CASE WHEN (antibiotic_id)::TEXT = '38' THEN sensitivity ELSE '' END ) AS Ceftriaxone_meningitis,
					MAX ( CASE WHEN (antibiotic_id)::TEXT = '39' THEN sensitivity ELSE '' END ) AS Ceftriaxone_non_meningitis,
					MAX ( CASE WHEN (antibiotic_id)::TEXT = '40' THEN sensitivity ELSE '' END ) AS Gentamicin_synergy,
					MAX ( CASE WHEN (antibiotic_id)::TEXT = '41' THEN sensitivity ELSE '' END ) AS Oral_Cephalosporins,
					MAX ( CASE WHEN (antibiotic_id)::TEXT = '42' THEN sensitivity ELSE '' END ) AS Novobiocin,			
					comment,
					reject_comment
				FROM 
				(
				
					SELECT	
						psample.patient_id,
						p.patient_code,
						psample.\"labID\" as labid,  
						l.name_en AS laboname,
						l.name_kh,
						l.lab_code,
						psample.sample_number,
						p.sex,
						p.dob,
						date_part('year'::text, age(psample.collected_date, (p.dob)::timestamp with time zone)) AS patient_age,
						d.department_name,
						s.sample_name,
						lss.source_name,
						to_char ( pst.\"entryDate\", 'dd-mm-YYYY' ) AS test_date,
						t.test_name,
						pr.organism_name AS results,
						pr.contaminant,
						pr.quantity,
						to_char ( psample.collected_date, 'dd-mm-YYYY' ) AS collected_date,
						psample.clinical_history as diagnosis,
						sd.description,
						CASE
							WHEN ds.sample_id = 6 THEN psd.sample_volume1
							ELSE NULL::double precision
						END as sample_volume1,
						CASE
							WHEN ds.sample_id = 6 THEN psd.sample_volume2
							ELSE NULL::double precision
						END AS sample_volume2,
						pr.antibiotic_id,
						an.antibiotic_name,
						( CASE WHEN pr.sensitivity= 1 THEN 'S' WHEN pr.sensitivity= 2 THEN 'R' ELSE'I' END ) AS sensitivity,
						psample.result_comment,
						rescomm.result_comment AS comment,
						psample.collected_time,
						to_char ( psample.admission_date, 'dd-mm-YYYY HH24:MM' ) AS admission_date,
						rescomm.reject_comment
					
					FROM
					(
						SELECT
							distinct
							CASE
								WHEN op.patient_id::text <> '0'::text THEN op.patient_id::text
								ELSE op.pid::text
							END AS pid,
							op.patient_code,
							op.patient_name,
							''::text AS name_en,
								CASE
									WHEN op.sex = 1 THEN 'M'::text
									WHEN op.sex = 2 THEN 'F'::text
									ELSE NULL::text
								END AS sex,
							op.dob,
							date_part('year'::text, age(now(), op.dob::timestamp with time zone)) AS _year,
							date_part('month'::text, age(now(), op.dob::timestamp with time zone)) AS _month,
							date_part('day'::text, age(now(), op.dob::timestamp with time zone)) AS _day,
							op.phone,
							op.province,
							op.district,
							op.commune,
							op.village,
							op.\"entryDate\" AS entrydate,
							op.\"entryBy\" AS entryby,
							op.status
							FROM camlis_outside_patient op
							inner join camlis_patient_sample AS psample on psample.patient_id = case when op.patient_id::text <> '0'::text then op.patient_id::text else op.pid::text end
							where psample.status = 1 
							and op.status = 1
							".$con."
		
			
							UNION ALL
							SELECT 
								op.pid,
								op.pid as patient_code,
								op.name AS patient_name,
								''::text AS name_en,
								op.sex,
								op.dob,
								date_part('year'::text, age(now(), op.dob::timestamp with time zone)) AS _year,
								date_part('month'::text, age(now(), op.dob::timestamp with time zone)) AS _month,
								date_part('day'::text, age(now(), op.dob::timestamp with time zone)) AS _day,
								op.phone,
								op.province,
								op.district,
								op.commune,
								op.village,
								op.\"entryDate\" AS entrydate,
								op.\"entryBy\" AS entryby,
								op.status
							FROM camlis_pmrs_patient op
							inner join camlis_patient_sample AS psample on psample.patient_id = op.pid
							where psample.status = 1
							".$con."
							and op.status = 1
							GROUP BY op.pid, psample.patient_id, psample.\"labID\"
						) as p
						JOIN camlis_patient_sample psample ON psample.patient_id::text = p.pid AND psample.status = 1
						join camlis_laboratory l on l.\"labID\" = psample.\"labID\" and l.status = TRUE 
						 JOIN camlis_patient_sample_tests pst ON pst.patient_sample_id = psample.\"ID\" AND pst.status = 1
						 JOIN camlis_std_sample_test st ON st.\"ID\" = pst.sample_test_id
						 JOIN camlis_std_test t ON t.\"ID\" = st.test_id
						 JOIN camlis_std_department_sample ds ON ds.\"ID\" = st.department_sample_id
						 JOIN camlis_std_department d ON d.\"ID\" = ds.department_id and ds.department_id=4
						 JOIN camlis_std_sample ss ON ss.\"ID\" = ds.sample_id AND ss.status = true
						 inner join camlis_lab_sample_source lss on lss.\"ID\"=psample.sample_source_id
				
						 inner join camlis_std_sample s on s.\"ID\" = ds.sample_id
				
						 LEFT JOIN camlis_patient_sample_detail psd ON psd.patient_sample_id = psample.\"ID\" AND ds.\"ID\" = psd.department_sample_id
						 LEFT JOIN camlis_std_sample_description sd ON psd.sample_description::text = sd.\"ID\"::text AND ss.\"ID\" = sd.sample_id
				
						left join result_test_organism_v pr on pr.patient_test_id = pst.\"ID\"
						left join camlis_std_antibiotic an on an.\"ID\" = pr.antibiotic_id
						left join camlis_result_comment rescomm on rescomm.patient_sample_id = psd.patient_sample_id and rescomm.department_sample_id = psd.department_sample_id
						left join camlis_ptest_result presult on presult.patient_test_id = pr.patient_test_id and presult.status = 1
						where psample.status = 1
						and t.\"ID\" = 170
						
						".$con."
						
						".(isset($obj->sample_id) && $obj->sample_id > 0 ? ' and ds.sample_id in('.$obj->sample_id.')' : '')."
						
						".(isset($obj->result_id) && $obj->result_id > 0 ? ' and pr.result_id in('.$obj->result_id.')' : '')."
									
					) _tbl  
							GROUP BY                 
								lab_name,
								patient_code,
								lab_id,
								sex,
								dob,
								age,
								sample,
								sample_site,
								volume1,
								volume2,
								sample_source,
								collection_date,
								admission_date,
								test_date,
								diagnosis,
								contaminant,
								results,
								comment,
								reject_comment
							order by lab_name,results desc";

		$result=$this->db->query($sql)->result_array();
		return $result;
	}

	/** End Load result v2*/

	//
	function lookup_sample_code($obj){
		$sql = " select * from camlis_patient_sample 
            where sample_number like ? 
            and labID = ?
            and status = TRUE limit 25
            ";
		$result=$this->db->query($sql,array('%'.$obj->val.'%',$this->laboratory_id))->result();
        return $result;
	}
	// 
	function lookup_patient_id($obj){
		$sql = "  
				select 
					ps.sample_number,
					pid, 
					patient_name 
				from patient_v p
				inner join camlis_patient_sample ps on ps.patient_id = p.pid
				where pid like ?
				 and ps.\"labID\" = ?
				and ps.status = TRUE
				limit 25
				
		";
		$result=$this->db->query($sql,array($obj->val.'%',$this->laboratory_id))->result();
        return $result;
	}
	// 
	function lookup_patient_name($obj){
		$sql = "  select 
					ps.sample_number, 
					pid, 
					patient_name 
				from patient_v p
				inner join camlis_patient_sample ps on ps.patient_id = p.pid
				where patient_name like ? 
				and ps.\"labID\"=?
				and ps.status = TRUE
				limit 25 ";
		$result=$this->db->query($sql,array($obj->val.'%',$this->laboratory_id))->result();
        return $result;
	}
	// 
	function lookup_labo_number($obj){
		$sql = "  select * from  camlis_patient_sample where sample_number like ? ";
		$result=$this->db->query($sql,array($obj->val.'%'))->result();
        return $result;
	}
	//
	function get_patient_info($obj){
		  
		// checking
		/*if($obj->type==1){  // patient id
			$sql = "  select * from patient_v where PID = ? ";
		}else if($obj->type==2){ // patient name
			$sql = "  select * from patient_v where patient_name = ? ";
		}else if($obj->type==3){ // patinent sample code*/
			$sql = "  select 
							* 
                    from v_camlis_all_patients p
                    inner join camlis_patient_sample ps on ps.patient_id = p.pid
                    where ps.sample_number = ? 
                    and p.lab_id =?
                    group by p.patient_id";
			
		//}
		
		// return list 
        return $this->db->query($sql,array($obj->filter_val,$this->laboratory_id))->result();
	}
	
	function normalize_critial($criteria){
		$_val = "";
		$sep = ','; 
		$i=0;
		if(count($criteria)>0){
			for($i=0; $i< count($criteria); $i++){
				$_val.= ($i==0?0:$sep)."".$criteria[$i]."";
				 
			}  
		}
		return $_val;
	}
	
	// get all permission report
	function lookup_perm_report($obj){
		$sql = "  select 
					pr.* 
				from permission_report_user pru
				inner join permission_report pr on pr.id = pru.perm_report_id
				where pru.user_id=?"; 
		// return list 
        return $this->db->query($sql,array($obj->val))->result();
	}
	
	//get report inner report
	public function get_report_name($obj) {
		 $sql = "select 
					pr.id,
					pr.report_name,
					pr.url, 
				   COALESCE(pru.user_id,0) as is_assign
					
				from permission_report pr 
				inner join permission_report_user pru on pru.perm_report_id = pr.id and user_id = ?";

		$query = $this->db->query($sql,array($obj->user_id));
		return $query->result();
	}

	public  function getSampleNumber($obj){
        $sql = "select 
                    p.pid,
                    p.patient_id,
                    p.patient_name,
                    ps.sample_number
                from v_camlis_all_patients p
                inner join camlis_patient_sample ps on ps.patient_id = p.pid
                where p.lab_id = ?
                order by ps.sample_number;";

        $query = $this->db->query($sql,array($this->laboratory_id));
        return $query->result();
    }

    /**
     * Query Raw Data
     */
    function create_temp_v_patients($laboratory, $wheres){
        $sql = "CREATE TEMPORARY TABLE IF NOT EXISTS temp_v_patients as (
            SELECT pmrs_patient.pid,
                psample.\"labID\" AS lab_id,
                pmrs_patient.pid AS patient_id,
                pmrs_patient.pid AS patient_code,
                pmrs_patient.name AS patient_name,
                    CASE pmrs_patient.sex
                        WHEN 'M'::bpchar THEN 1
                        WHEN 'F'::bpchar THEN 2
                        ELSE 0
                    END AS sex,
                pmrs_patient.dob,
                pmrs_patient.phone,
                pmrs_patient.province,
                pmrs_patient.district,
                pmrs_patient.commune,
                pmrs_patient.village,
                ''::character varying AS nationality_en,
                ''::character varying AS passport_number,
                ''::character varying AS flight_number,
                NULL::date AS date_arrival
            FROM camlis_patient_sample psample
            JOIN camlis_pmrs_patient pmrs_patient ON psample.patient_id::text = pmrs_patient.pid::text AND pmrs_patient.status = 1
            WHERE psample.status = 1 and psample.\"labID\" = ANY(VALUES".$laboratory.") ".$wheres."
             GROUP BY pmrs_patient.pid, psample.patient_id, psample.\"labID\"
             UNION
             SELECT 
             outside_patient.pid::text AS pid,
             outside_patient.lab_id,
             outside_patient.patient_id,
             outside_patient.patient_code,
             outside_patient.patient_name,
             outside_patient.sex,
             outside_patient.dob,
             outside_patient.phone,
             outside_patient.province,
             outside_patient.district,
             outside_patient.commune,
             outside_patient.village,
             countries.nationality_en,
             outside_patient.passport_number,
             outside_patient.flight_number,
             outside_patient.date_arrival
             FROM camlis_outside_patient outside_patient
             join camlis_patient_sample AS psample on psample.patient_id = outside_patient.pid::text
             LEFT JOIN countries ON outside_patient.nationality = countries.num_code
             WHERE outside_patient.status = 1 AND outside_patient.lab_id = ANY(VALUES".$laboratory.")
             and psample.\"labID\" = ANY(VALUES".$laboratory.")
             and psample.status = 1 ".$wheres."
            
             
        )";
        $this->db->query($sql);
    }
    public function get_raw_data($data) {
        //$con=" WHERE pv.\"labID\"='".$this->laboratory_id."' and concat(pv.received_date,' ',pv.received_time) >= '".$obj->start.' '.$obj->start_time."' AND concat(pv.received_date,' ',pv.received_time) <= '".$obj->end.' '.$obj->end_time."' ";//14022022
		//$this->Gen_sample_reject($con); //14022022

        $with_clause                = "";
        $JOIN_CLAUSE_STRING         = "";
        $with_clause_sample_source  = "";        
        $fields_seleted             = "";
        $patient_clause             = "";
        $WHERE_CLAUSE               = "";
        $WHERE_PSAMPLE_CLAUSE       = " ";
        $GROUP_BY_CLAUSE            = " GROUP BY ";
        $lab_string = "";
        foreach($data['laboratory']['value'] as $val){
            $lab_string .= "(".$val."),";
        }
        $lab_string = substr($lab_string, 0, strlen($lab_string) - 1);// remove ,

		/** improvement */

		$whereClosesForTmpPatient = "";
		//Collected Date
		if (isset($data['collected_date']) && !empty($data['collected_date']['min']) || !empty($data['collected_date']['max'])) {
			$min = DateTime::createFromFormat('d/m/Y', $data['collected_date']['min']);
			$max = DateTime::createFromFormat('d/m/Y', $data['collected_date']['max']);
			$min = $min ? $min->format('Y-m-d') : '';
			$max = $max ? $max->format('Y-m-d') : '';
			$whereClosesForTmpPatient .= " AND psample.collected_date >= '$min' AND psample.collected_date <= '$max' ";
			$WHERE_PSAMPLE_CLAUSE .= " AND psample.collected_date >= '$min' AND psample.collected_date <= '$max' ";
		}
		//Received Date
		if (isset($data['received_date']) && !empty($data['received_date']['min']) || !empty($data['received_date']['max'])) {
			$min = DateTime::createFromFormat('d/m/Y', $data['received_date']['min']);
			$max = DateTime::createFromFormat('d/m/Y', $data['received_date']['max']);
			$min = $min ? $min->format('Y-m-d') : '';
			$max = $max ? $max->format('Y-m-d') : '';
			$whereClosesForTmpPatient .= " AND psample.received_date >= '$min' AND psample.received_date <= '$max' ";
			$WHERE_PSAMPLE_CLAUSE .= " AND psample.received_date >= '$min' AND psample.received_date <= '$max' ";
		}

		if($whereClosesForTmpPatient==''){
			$min = DateTime::createFromFormat('d/m/Y', $data['test_date']['min']);
			$max = DateTime::createFromFormat('d/m/Y', $data['test_date']['max']);
			$min = $min ? $min->format('Y-m-d') : '';
			$max = $max ? $max->format('Y-m-d') : '';
			$whereClosesForTmpPatient .= " AND psample.received_date >= '".date('Y-m-d', strtotime('-1 days', strtotime($min)))."' AND psample.received_date <= '$max' ";
		}

        $this->create_temp_v_patients($lab_string, $whereClosesForTmpPatient);
        //Fields and join table
        if (!empty($data['laboratory']['value']) || isset($data['laboratory']['is_show'])) {
            
            $fields_seleted .= ", lab.name_en AS laboratory_name";
            $JOIN_CLAUSE_STRING .=" INNER JOIN camlis_laboratory lab ON psample.\"labID\" = lab.\"labID\" ";
        }
        if (isset($data['sample_source']['is_show'])) {
            $with_clause_sample_source = " , sample_source AS ( 
                SELECT * FROM camlis_lab_sample_source AS sample_source
                WHERE status = TRUE AND lab_id = ANY (VALUES".$lab_string.")
            )";

            $fields_seleted .= ", sample_source.source_name AS sample_source ";
            $JOIN_CLAUSE_STRING .= " INNER JOIN sample_source ON psample.sample_source_id = sample_source.\"ID\" AND psample.\"labID\" = sample_source.lab_id AND sample_source.status = TRUE ";
        }
        if (isset($data['requester']['is_show'])) {            
            $fields_seleted .= ", requester.requester_name AS requester ";
            $JOIN_CLAUSE_STRING .= " INNER JOIN camlis_lab_requester AS requester ON psample.requester_id = requester.\"ID\" AND psample.\"labID\" = requester.lab_id";
        }
        if (isset($data['payment_type']['is_show'])) {            
            $fields_seleted .= ", payment_type.name AS payment_type";
            $JOIN_CLAUSE_STRING .= " INNER JOIN camlis_std_payment_type AS payment_type ON psample.payment_type_id = payment_type.id";
        }
        if (!empty($data['patient_code']['value']) || isset($data['patient_code']['is_show']) ||
            !empty($data['patient_name']['value']) || isset($data['patient_name']['is_show']) ||
            !empty($data['patient_age']['min']) || !empty($data['patient_age']['max']) || isset($data['patient_age']['is_show']) ||
            !empty($data['patient_gender']['value']) || isset($data['patient_gender']['is_show']) ||
            !empty($data['province']['value']) || isset($data['province']['is_show']) ||
            !empty($data['district']['value']) || isset($data['district']['is_show']) ) {
            
            $patient_clause .= ", patient AS (
                SELECT * FROM temp_v_patients as patient
                WHERE patient.lab_id = ANY(VALUES".$lab_string.")
             ) ";
            $fields_seleted .= ", patient.patient_code,
                patient.patient_name,
                DATE_PART('year',age(now(),patient.dob))AS patient_age,
                DATE_PART('month',age(now(),patient.dob))AS age_month,
                DATE_PART('day',age(now(),patient.dob))AS age_day,
                patient.nationality_en as nationality,
                (CASE patient.sex WHEN 1 THEN 'M' WHEN 2 THEN 'F' ELSE '' END) AS patient_gender,
                patient.phone ";
            $JOIN_CLAUSE_STRING .= " INNER JOIN patient ON psample.patient_id = patient.pid AND psample.\"labID\" = patient.lab_id";
        }
        if (!empty($data['province']['value']) || isset($data['province']['is_show'])) {

            $fields_seleted .= ",province.name_en AS province ";
            $JOIN_CLAUSE_STRING .= " LEFT JOIN provinces AS province ON patient.province = province.code AND province.status = TRUE ";
        }
        if (!empty($data['district']['value']) || isset($data['district']['is_show'])) {            
            $fields_seleted .=", district.name_en AS district ";
            $JOIN_CLAUSE_STRING .= " LEFT JOIN districts AS district ON patient.district = district.code AND district.status = TRUE ";
        }
        if (!empty($data['commune']['value']) || isset($data['commune']['is_show'])) {            
            $fields_seleted .= ", commune.name_en AS commune ";
            $JOIN_CLAUSE_STRING .= " LEFT JOIN communes AS commune ON patient.commune = commune.code AND commune.status = TRUE ";
        }
        if (!empty($data['village']['value']) || isset($data['village']['is_show'])) {            
            $fields_seleted .= ", village.name_en AS village";
            $JOIN_CLAUSE_STRING .= " LEFT JOIN villages AS village ON patient.village = village.code AND village.status = 1";
        }

        $has_department         = !empty($data['department']['value']) || isset($data['department']['is_show']);
        $has_sample_type        = !empty($data['sample_type']['value']) || isset($data['sample_type']['is_show']);
        $has_sample_description = !empty($data['sample_description']['value']) || isset($data['sample_description']['is_show']);
        $has_test               = isset($data['test']) && (!empty($data['test']['value']) || isset($data['test']['is_show']));
        if ($has_department || $has_sample_type || $has_sample_description || $has_test) {                        
            $fields_seleted .= ", test.test_name AS test, psample_test.ref_range_max_value as max_val, psample_test.ref_range_min_value as min_val, psample_test.unit_sign as unit";
            $JOIN_CLAUSE_STRING .= " LEFT JOIN camlis_patient_sample_tests AS psample_test ON psample.\"ID\" = psample_test.patient_sample_id AND psample_test.status = 1 ";
            $JOIN_CLAUSE_STRING .= " INNER JOIN camlis_std_sample_test AS std_sampletest ON psample_test.sample_test_id = std_sampletest.\"ID\"";
            $JOIN_CLAUSE_STRING .= " INNER JOIN camlis_std_department_sample AS department_sample ON std_sampletest.department_sample_id = department_sample.\"ID\" AND department_sample.status = TRUE ";
            $JOIN_CLAUSE_STRING .= " INNER JOIN camlis_std_test AS test ON std_sampletest.test_id = test.\"ID\"";
            if ($has_department) {
                $fields_seleted .= ", department.department_name AS department";
                $JOIN_CLAUSE_STRING .=" INNER JOIN camlis_std_department AS department ON department_sample.department_id = department.\"ID\" AND department.status = TRUE ";
            }
            if ($has_sample_type) {
                            $fields_seleted .= ", sample_type.sample_name AS sample_type ";
                $JOIN_CLAUSE_STRING .= " INNER JOIN camlis_std_sample AS sample_type ON department_sample.sample_id = sample_type.\"ID\" AND sample_type.status = TRUE ";
            }

            if ($has_sample_description) {
                $fields_seleted .= ",sample_description.description AS sample_description,psample_detail.sample_volume1 as volume1 ,psample_detail.sample_volume2 as volume2 ";
                $JOIN_CLAUSE_STRING .= " LEFT JOIN camlis_patient_sample_detail AS psample_detail ON psample_detail.patient_sample_id = psample.\"ID\" AND department_sample.\"ID\" = psample_detail.department_sample_id AND psample_detail.status = TRUE ";
                $JOIN_CLAUSE_STRING .= " LEFT JOIN camlis_std_sample_description AS sample_description ON cast(psample_detail.sample_description as bigint) = sample_description.\"ID\"";
            }

        }
        $has_test_date      = !empty($data['test_date']['min']) || !empty($data['test_date']['max']) || isset($data['test_date']['is_show']);
        $has_organism       = !empty($data['result_organism']['value']) || isset($data['result_organism']['is_show']);
        $has_antibiotic     = !empty($data['antibiotic']['value']) || isset($data['antibiotic']['is_show']);
        $has_sensitivity    = !empty($data['sensitivity']['value']) || isset($data['sensitivity']['is_show']);
        if ($has_test_date || $has_organism || $has_antibiotic || $has_sensitivity ) {                        
            $fields_seleted .= ", to_char(ptest_result.test_date, 'DD/MM/YYYY') AS test_date ";
            $JOIN_CLAUSE_STRING .= " LEFT JOIN camlis_ptest_result AS ptest_result ON psample_test.\"ID\" = ptest_result.patient_test_id AND ptest_result.patient_sample_id = psample.\"ID\" AND ptest_result.status = 1 ";
            $JOIN_CLAUSE_STRING .= " LEFT JOIN camlis_std_test_organism AS test_organism ON ptest_result.type = 1 AND ptest_result.result = cast(test_organism.\"ID\" as varchar)";

            if ($has_organism) {
                $fields_seleted .= ", (CASE WHEN ptest_result.type = 1 THEN CONCAT(organism.organism_name, CASE organism.organism_value WHEN 1 THEN ' Positive' WHEN 2 THEN ' Negative' ELSE '' END) ELSE ptest_result.result END) AS result_organism";
                $JOIN_CLAUSE_STRING .= " LEFT JOIN camlis_std_organism AS organism ON test_organism.organism_id = organism.\"ID\" ";
            }

            if ($has_antibiotic || $has_sensitivity) {
                
                $fields_seleted .= ", antibiotic.antibiotic_name AS antibiotic, presult_antibiotic.test_zone as \"MIC\",presult_antibiotic.disc_diffusion as \"DD\",
                (CASE presult_antibiotic.sensitivity
                    WHEN 1 THEN 'Sensitive'
                    WHEN 2 THEN 'Resistant'
                    WHEN 3 THEN 'Intermediate'
                END) AS sensitivity ";
                $JOIN_CLAUSE_STRING .= " LEFT JOIN camlis_ptest_result_antibiotic AS presult_antibiotic ON ptest_result.\"ID\" = presult_antibiotic.presult_id";
                $JOIN_CLAUSE_STRING .= " LEFT JOIN camlis_std_antibiotic AS antibiotic ON presult_antibiotic.antibiotic_id = antibiotic.\"ID\" ";
            }
        }

        //Condition
		//Laboratory ID
        if (isset($data['laboratory']['value']) && count($data['laboratory']['value']) > 0) {
            
        }
        //Patient ID
        if (isset($data['patient_code']['value']) && !empty($data['patient_code']['value'])) {
            $WHERE_CLAUSE .= " patient.patient_code = '".$data['patient_code']['value']."' AND ";
        }
        //Patient Name
        if (isset($data['patient_name']['value']) && !empty($data['patient_name']['value'])) {
            
            $WHERE_CLAUSE .= " patient.patient_name = '".$data['patient_name']['value']."' AND ";
        }
        //Patient Age
        if (isset($data['patient_age']) && (!empty($data['patient_age']['min']) || !empty($data['patient_age']['max']))) {
            $min_age = empty($data['patient_age']['min']) ? 0 : $data['patient_age']['min'];
            $max_age = empty($data['patient_age']['max']) ? PHP_INT_MAX : $data['patient_age']['max'];            
            
            $WHERE_CLAUSE .= " DATE_PART('year',age(psample.collected_date, patient.dob)) >= $min_age AND DATE_PART('year',age(psample.collected_date, patient.dob)) <= $max_age AND";
        }
        //Patient Gender
        if (isset($data['patient_gender']['value']) && !empty($data['patient_gender']['value'])) {
            $WHERE_CLAUSE .= " patient.sex = ".$data['patient_gender']['value']." AND ";
        }
        //Patient Province
        if (isset($data['province']['value']) && count($data['province']['value']) > 0) {
            $WHERE_CLAUSE .= " patient.province IN (".implode(",",$data['province']['value']).") AND";

        }
        //Patient District
        if (isset($data['district']['value']) && count($data['district']['value']) > 0) {            
            $WHERE_CLAUSE .= " patient.district IN (".implode(",",$data['district']['value']).") AND";
        }
        //Department
        if (isset($data['department']['value']) && count($data['department']['value']) > 0) {            
            $WHERE_CLAUSE .= " department.\"ID\" IN (".implode(",",$data['department']['value']).") AND";
        }
        //Sample Type
        if (isset($data['sample_type']['value']) && count($data['sample_type']['value']) > 0) {            
            $WHERE_CLAUSE .= " sample_type.\"ID\" IN (".implode(",",$data['sample_type']['value']).") AND";
        }
        //Sample Description
        if (isset($data['sample_description']['value']) && count($data['sample_description']['value']) > 0) {            
            $WHERE_CLAUSE .= " sample_description.\"ID\" = ".$data['sample_description']['value']." AND ";
        }
        //Sample Number
        if (isset($data['sample_number']['value']) && !empty($data['sample_number']['value'])) {            
            $WHERE_PSAMPLE_CLAUSE .= " AND psample.sample_number = ".$data['sample_number']['value']." ";
        }
        //Collected Date
        /*if (isset($data['collected_date']) && !empty($data['collected_date']['min']) || !empty($data['collected_date']['max'])) {
            $min = DateTime::createFromFormat('d/m/Y', $data['collected_date']['min']);
            $max = DateTime::createFromFormat('d/m/Y', $data['collected_date']['max']);
            $min = $min ? $min->format('Y-m-d') : '';
            $max = $max ? $max->format('Y-m-d') : '';
            $WHERE_PSAMPLE_CLAUSE .= " AND psample.collected_date >= '$min' AND psample.collected_date <= '$max' ";
        }
        //Received Date
        if (isset($data['received_date']) && !empty($data['received_date']['min']) || !empty($data['received_date']['max'])) {
            $min = DateTime::createFromFormat('d/m/Y', $data['received_date']['min']);
            $max = DateTime::createFromFormat('d/m/Y', $data['received_date']['max']);
            $min = $min ? $min->format('Y-m-d') : '';
            $max = $max ? $max->format('Y-m-d') : '';
            $WHERE_PSAMPLE_CLAUSE .= " AND psample.received_date >= '$min' AND psample.received_date <= '$max' ";
        }*/
        //Sample Source
        if (isset($data['sample_source']['value']) && count($data['sample_source']['value']) > 0) {
            $this->db->where_in('psample.sample_source_id', $data['sample_source']['value']);
            $WHERE_PSAMPLE_CLAUSE .= " AND psample.sample_source_id IN (".implode(", ",$data['sample_source']['value']).")";
        }
        //Requester
        if (isset($data['requester']['value']) && count($data['requester']['value']) > 0) {            
            $WHERE_PSAMPLE_CLAUSE .= " AND psample.requester_id IN (".implode(", ",$data['requester']['value']).")";
        }
        //Test Date
        if (isset($data['test_date']) && !empty($data['test_date']['min']) || !empty($data['test_date']['max'])) {
            $min = DateTime::createFromFormat('d/m/Y', $data['test_date']['min']);
            $max = DateTime::createFromFormat('d/m/Y', $data['test_date']['max']);
            $min = $min ? $min->format('Y-m-d') : '';
            $max = $max ? $max->format('Y-m-d') : '';            
            $WHERE_CLAUSE .= " ptest_result.test_date >= '$min' AND ptest_result.test_date <= '$max' AND ";
        }
        //Test Name/group result
        if (isset($data['test']['value']) && count($data['test']['value']) > 0) {            
            $WHERE_CLAUSE .= " test.\"ID\" IN (".implode(", ",$data['test']['value']).") AND ";
        }
        //Organism
        if (isset($data['result_organism']['value']) && count($data['result_organism']['value']) > 0) {            
            $WHERE_CLAUSE .= " organism.\"ID\" IN (".implode(", ",$data['result_organism']['value']).") AND";
        }
        //Antibiotic
        if (isset($data['antibiotic']['value']) && count($data['antibiotic']['value']) > 0) {            
            $WHERE_CLAUSE .= "antibiotic.\"ID\" IN (".implode(",",$data['antibiotic']['value']).") AND ";
        }
        //Sensitivity
        if (isset($data['sensitivity']['value']) && !empty($data['sensitivity']['value'])) {            
            $WHERE_CLAUSE .= " presult_antibiotic.sensitivity = ".$data['sensitivity']['value']." AND ";
        }

        //14022022
        if (!empty($data['sample_status']['value']) || isset($data['sample_status']['is_show'])) {            
            $fields_seleted .= ", res_comment.reject_comment";
            $JOIN_CLAUSE_STRING .= " LEFT JOIN camlis_result_comment AS res_comment ON res_comment.patient_sample_id = psample.\"ID\" AND department_sample.\"ID\" = res_comment.department_sample_id";
        }
        // END
        //Sample Status
        $has_is_rejected_value      = isset($data['is_rejected']['value']) && !empty($data['is_rejected']['value']);
        $has_for_research_value     = isset($data['for_research']['value']) && !empty($data['for_research']['value']);
        $has_is_urgent_value        = isset($data['is_urgent']['value']) && !empty($data['is_urgent']['value']);
        if ($has_is_rejected_value || $has_for_research_value || $has_is_urgent_value) $this->db->group_start();
        if ($has_is_rejected_value) {
            $WHERE_PSAMPLE_CLAUSE .= " AND psample.is_rejected = ".$data['is_rejected']['value']." ";
        }
        if ($has_for_research_value) {
            $WHERE_PSAMPLE_CLAUSE .= " AND psample.for_research = ".$data['for_research']['value']." ";
        }
        if ($has_is_urgent_value) {            
            $WHERE_PSAMPLE_CLAUSE .= " AND psample.is_urgent = ".$data['is_urgent']['value']." ";
        }
        if ($has_is_rejected_value || $has_for_research_value || $has_is_urgent_value) $this->db->group_end();

        //Payment type
        if (isset($data['payment_type']['value']) && !empty($data['payment_type']['value'])) {            
            $WHERE_PSAMPLE_CLAUSE .= " AND psample.payment_type_id = ".$data['payment_type']['value']."";
        }

        //GroupBy
        //$this->db->group_by('psample."ID"');
        $GROUP_BY_CLAUSE .= " psample.\"ID\" ";

        if ($has_antibiotic || $has_sensitivity){            
            //$this->db->group_by('presult_antibiotic."ID" , antibiotic.antibiotic_name');
            //$this->db->group_by('presult_antibiotic.sensitivity');
            $GROUP_BY_CLAUSE .= ", presult_antibiotic.\"ID\" , antibiotic.antibiotic_name, presult_antibiotic.sensitivity";
        }

        if (isset($data['organism']['is_show'])) {
            //$this->db->group_by('ptest_result."ID"');
            $GROUP_BY_CLAUSE .= ", ptest_result.\"ID\"";
        }
        
        if (isset($data['test']['is_show'])) {
            //$this->db->group_by('test.test_name');
            $GROUP_BY_CLAUSE .= ", test.test_name";
        }
        if (isset($data['sample_type']['is_show'])) {
            //$this->db->group_by('sample_type."ID"');
            $GROUP_BY_CLAUSE .= ', sample_type."ID"';
        }
        if (isset($data['department']['is_show'])) {
            //$this->db->group_by('department."ID"');
            $GROUP_BY_CLAUSE .= ", department.\"ID\" ";
        }
        if (isset($data['sample_source']['is_show'])) {
            //$this->db->group_by('sample_source.source_name');
            $GROUP_BY_CLAUSE .= ", sample_source.source_name";
        }
        if (isset($data['province']['is_show'])){
            //$this->db->group_by(' province.name_en');
            $GROUP_BY_CLAUSE .= ", province.name_en";
        }
        
        if (isset($data['district']['is_show'])){            
            $GROUP_BY_CLAUSE .= ", district.name_en, commune.name_en, village.name_en ";
        }        

        if ($has_department){            
            $GROUP_BY_CLAUSE .= ", department.department_name ";
        }
        if (isset($data['payment_type']['is_show'])){            
            $GROUP_BY_CLAUSE .= ", payment_type.name ";
        }
        if (isset($data['sample_description']['is_show'])){            
            $GROUP_BY_CLAUSE .= ", sample_description.description";
        }
        if (isset($data['requester']['is_show'])){            
            $GROUP_BY_CLAUSE .= ", requester.requester_name";
        }
        if (isset($data['result_organism']['is_show'])){            
            $GROUP_BY_CLAUSE .= ", organism.organism_name, organism.organism_value, ptest_result.result";
        }
        if ($has_test_date || $has_organism || $has_antibiotic || $has_sensitivity ) {
            $GROUP_BY_CLAUSE .= ", ptest_result.test_date , ptest_result.type";
        }
        if ($has_department || $has_sample_type || $has_sample_description || $has_test) {
            //$this->db->group_by('test.test_name');            
            //$this->db->group_by('psample_test.ref_range_max_value');
            //$this->db->group_by('psample_test.ref_range_min_value');
            //$this->db->group_by('psample_test.unit_sign');
            $GROUP_BY_CLAUSE .= ", test.test_name, psample_test.ref_range_max_value, psample_test.ref_range_min_value, psample_test.unit_sign";
            if($has_sample_description){
                //$this->db->group_by('psample_detail.sample_volume1');
                //$this->db->group_by('psample_detail.sample_volume2');
                $GROUP_BY_CLAUSE .= ", psample_detail.sample_volume1, psample_detail.sample_volume2";
            }
        }
        //14022022
        if (!empty($data['sample_status']['value']) || isset($data['sample_status']['is_show'])) {
            $GROUP_BY_CLAUSE .= ", res_comment.reject_comment";
        }
        //End
        $GROUP_BY_CLAUSE .= ", psample.\"labID\",
        psample.sample_number,
        lab.name_en, 
        collected_date,
        received_date,
        for_research,
        sample_status,
        patient.patient_code, 
        patient.patient_name,
        patient.nationality_en,
        psample.clinical_history,
        patient.dob, patient.sex , patient.phone";
        
        if(strlen($WHERE_CLAUSE)>0){
            $leng = strlen($WHERE_CLAUSE);
            $WHERE_CLAUSE = substr($WHERE_CLAUSE,0,($leng - 4));
            $WHERE_CLAUSE = "WHERE ".$WHERE_CLAUSE;          
        }
        
        $sql = "
            WITH psample AS (
                SELECT *
                FROM camlis_patient_sample AS psample
                WHERE psample.status = 1 AND psample.\"labID\" = ANY (VALUES".$lab_string.") ".$WHERE_PSAMPLE_CLAUSE."
            ), lab AS (
                SELECT * FROM camlis_laboratory as lab
                WHERE lab.status = true AND lab.\"labID\" = ANY (VALUES".$lab_string.") 
            ) ".$with_clause_sample_source." ".$patient_clause." SELECT psample.\"labID\",
                psample.sample_number,
                to_char(psample.collected_date, 'DD/MM/YYYY') AS collected_date,
                to_char(psample.received_date, 'DD/MM/YYYY') AS received_date,
                CONCAT_WS(
                    ', ',
                    CASE psample.for_research 
                        WHEN 0 THEN ''
                        WHEN 1 THEN 'Suspect' 
                        WHEN 2 THEN 'Pneumonia'
                        WHEN 3 THEN 'Follow Up'
                        WHEN 4 THEN 'Contact'
                        WHEN 5 THEN 'HCW'
                        WHEN 6 THEN 'Other'
                        WHEN 7 THEN 'Migrants'
                        WHEN 8 THEN 'Passenger'
                        WHEN 9 THEN 'Certificate'
                        WHEN 10 THEN 'Followup Positive Cases'
                        WHEN 11 THEN 'F20 Event'
                        WHEN 12 THEN 'Death'
                        ELSE NULL END,
                    CASE WHEN psample.is_rejected = 1 THEN 'Rejected Sample' ELSE NULL END,
                    CASE WHEN psample.is_urgent = 1 THEN 'Urgent Sample' ELSE NULL END
                ) AS sample_status,
                psample.clinical_history as diagnosis
                ".$fields_seleted."
                FROM psample
                ".$JOIN_CLAUSE_STRING."
                ".$WHERE_CLAUSE."
                ".$GROUP_BY_CLAUSE."
        ";
        return $this->db->query($sql)->result_array();
    }

    /**
     * Query Raw Data In Khmer
     * 07-07-2021
     */
    public function get_raw_data_kh($data) {
        $with_clause                = "";
        $JOIN_CLAUSE_STRING         = "";
        $with_clause_sample_source  = "";        
        $fields_seleted             = "";
        $patient_clause             = "";
        $WHERE_CLAUSE               = "";
        $WHERE_PSAMPLE_CLAUSE       = " ";
        $GROUP_BY_CLAUSE            = " GROUP BY ";
        $lab_string = "";
        foreach($data['laboratory']['value'] as $val){
            $lab_string .= "(".$val."),";
        }
        $lab_string = substr($lab_string, 0, strlen($lab_string) - 1);// remove ,

		/** improvement */

		$whereClosesForTmpPatient = "";
		//Collected Date
		if (isset($data['collected_date']) && !empty($data['collected_date']['min']) || !empty($data['collected_date']['max'])) {
			$min = DateTime::createFromFormat('d/m/Y', $data['collected_date']['min']);
			$max = DateTime::createFromFormat('d/m/Y', $data['collected_date']['max']);
			$min = $min ? $min->format('Y-m-d') : '';
			$max = $max ? $max->format('Y-m-d') : '';
			$whereClosesForTmpPatient .= " AND psample.collected_date >= '$min' AND psample.collected_date <= '$max' ";
			$WHERE_PSAMPLE_CLAUSE .= " AND psample.collected_date >= '$min' AND psample.collected_date <= '$max' ";
		}
		//Received Date
		if (isset($data['received_date']) && !empty($data['received_date']['min']) || !empty($data['received_date']['max'])) {
			$min = DateTime::createFromFormat('d/m/Y', $data['received_date']['min']);
			$max = DateTime::createFromFormat('d/m/Y', $data['received_date']['max']);
			$min = $min ? $min->format('Y-m-d') : '';
			$max = $max ? $max->format('Y-m-d') : '';
			$whereClosesForTmpPatient .= " AND psample.received_date >= '$min' AND psample.received_date <= '$max' ";
			$WHERE_PSAMPLE_CLAUSE .= " AND psample.received_date >= '$min' AND psample.received_date <= '$max' ";
		}

		if($whereClosesForTmpPatient==''){
			$min = DateTime::createFromFormat('d/m/Y', $data['test_date']['min']);
			$max = DateTime::createFromFormat('d/m/Y', $data['test_date']['max']);
			$min = $min ? $min->format('Y-m-d') : '';
			$max = $max ? $max->format('Y-m-d') : '';
			$whereClosesForTmpPatient .= " AND psample.received_date >= '".date('Y-m-d', strtotime('-1 days', strtotime($min)))."' AND psample.received_date <= '$max' ";
		}


        $this->create_temp_v_patients($lab_string, $whereClosesForTmpPatient);
        //Fields and join table
        if (!empty($data['laboratory']['value']) || isset($data['laboratory']['is_show'])) {
            
            $fields_seleted .= ", lab.name_en AS laboratory_name";
            $JOIN_CLAUSE_STRING .=" INNER JOIN camlis_laboratory lab ON psample.\"labID\" = lab.\"labID\" ";
        }
        if (isset($data['sample_source']['is_show'])) {
            $with_clause_sample_source = " , sample_source AS ( 
                SELECT * FROM camlis_lab_sample_source AS sample_source
                WHERE status = TRUE AND lab_id = ANY (VALUES".$lab_string.")
            )";

            $fields_seleted .= ", sample_source.source_name AS sample_source ";
            $JOIN_CLAUSE_STRING .= " INNER JOIN sample_source ON psample.sample_source_id = sample_source.\"ID\" AND psample.\"labID\" = sample_source.lab_id AND sample_source.status = TRUE ";
        }
        if (isset($data['requester']['is_show'])) {            
            $fields_seleted .= ", requester.requester_name AS requester ";
            $JOIN_CLAUSE_STRING .= " INNER JOIN camlis_lab_requester AS requester ON psample.requester_id = requester.\"ID\" AND psample.\"labID\" = requester.lab_id";
        }
        if (isset($data['payment_type']['is_show'])) {            
            $fields_seleted .= ", payment_type.name AS payment_type";
            $JOIN_CLAUSE_STRING .= " INNER JOIN camlis_std_payment_type AS payment_type ON psample.payment_type_id = payment_type.id";
        }
        if (!empty($data['patient_code']['value']) || isset($data['patient_code']['is_show']) ||
            !empty($data['patient_name']['value']) || isset($data['patient_name']['is_show']) ||
            !empty($data['patient_age']['min']) || !empty($data['patient_age']['max']) || isset($data['patient_age']['is_show']) ||
            !empty($data['patient_gender']['value']) || isset($data['patient_gender']['is_show']) ||
            !empty($data['province']['value']) || isset($data['province']['is_show']) ||
            !empty($data['district']['value']) || isset($data['district']['is_show']) ) {
            
            $patient_clause .= ", patient AS (
                SELECT * FROM temp_v_patients as patient
                WHERE patient.lab_id = ANY(VALUES".$lab_string.")
             ) ";
            $fields_seleted .= ", patient.patient_code,
                patient.patient_name,
                DATE_PART('year',age(now(),patient.dob))AS patient_age,
                DATE_PART('month',age(now(),patient.dob))AS age_month,
                DATE_PART('day',age(now(),patient.dob))AS age_day,
                patient.nationality_en as nationality,
                (CASE patient.sex WHEN 1 THEN 'ប្រុស' WHEN 2 THEN 'ស្រី' ELSE '' END) AS patient_gender,
                patient.phone ";
            $JOIN_CLAUSE_STRING .= " INNER JOIN patient ON psample.patient_id = patient.pid AND psample.\"labID\" = patient.lab_id";
        }
        if (!empty($data['province']['value']) || isset($data['province']['is_show'])) {
            $fields_seleted .= ",province.name_kh AS province ";
            $JOIN_CLAUSE_STRING .= " LEFT JOIN provinces AS province ON patient.province = province.code AND province.status = TRUE ";
        }
        if (!empty($data['district']['value']) || isset($data['district']['is_show'])) {            
            $fields_seleted .=", district.name_kh AS district ";
            $JOIN_CLAUSE_STRING .= " LEFT JOIN districts AS district ON patient.district = district.code AND district.status = TRUE ";
        }
        if (!empty($data['commune']['value']) || isset($data['commune']['is_show'])) {            
            $fields_seleted .= ", commune.name_kh AS commune ";
            $JOIN_CLAUSE_STRING .= " LEFT JOIN communes AS commune ON patient.commune = commune.code AND commune.status = TRUE ";
        }
        if (!empty($data['village']['value']) || isset($data['village']['is_show'])) {            
            $fields_seleted .= ", village.name_kh AS village";
            $JOIN_CLAUSE_STRING .= " LEFT JOIN villages AS village ON patient.village = village.code AND village.status = 1";
        }

        $has_department         = !empty($data['department']['value']) || isset($data['department']['is_show']);
        $has_sample_type        = !empty($data['sample_type']['value']) || isset($data['sample_type']['is_show']);
        $has_sample_description = !empty($data['sample_description']['value']) || isset($data['sample_description']['is_show']);
        $has_test               = isset($data['test']) && (!empty($data['test']['value']) || isset($data['test']['is_show']));
        if ($has_department || $has_sample_type || $has_sample_description || $has_test) {                        
            $fields_seleted .= ", test.test_name AS test, psample_test.ref_range_max_value as max_val, psample_test.ref_range_min_value as min_val, psample_test.unit_sign as unit";
            $JOIN_CLAUSE_STRING .= " LEFT JOIN camlis_patient_sample_tests AS psample_test ON psample.\"ID\" = psample_test.patient_sample_id AND psample_test.status = 1 ";
            $JOIN_CLAUSE_STRING .= " INNER JOIN camlis_std_sample_test AS std_sampletest ON psample_test.sample_test_id = std_sampletest.\"ID\"";
            $JOIN_CLAUSE_STRING .= " INNER JOIN camlis_std_department_sample AS department_sample ON std_sampletest.department_sample_id = department_sample.\"ID\" AND department_sample.status = TRUE ";
            $JOIN_CLAUSE_STRING .= " INNER JOIN camlis_std_test AS test ON std_sampletest.test_id = test.\"ID\"";
            if ($has_department) {
                $fields_seleted .= ", department.department_name AS department";
                $JOIN_CLAUSE_STRING .=" INNER JOIN camlis_std_department AS department ON department_sample.department_id = department.\"ID\" AND department.status = TRUE ";
            }
            if ($has_sample_type) {
                            $fields_seleted .= ", sample_type.sample_name AS sample_type ";
                $JOIN_CLAUSE_STRING .= " INNER JOIN camlis_std_sample AS sample_type ON department_sample.sample_id = sample_type.\"ID\" AND sample_type.status = TRUE ";
            }

            if ($has_sample_description) {
                $fields_seleted .= ",sample_description.description AS sample_description,psample_detail.sample_volume1 as volume1 ,psample_detail.sample_volume2 as volume2 ";
                $JOIN_CLAUSE_STRING .= " LEFT JOIN camlis_patient_sample_detail AS psample_detail ON psample_detail.patient_sample_id = psample.\"ID\" AND department_sample.\"ID\" = psample_detail.department_sample_id AND psample_detail.status = TRUE ";
                $JOIN_CLAUSE_STRING .= " LEFT JOIN camlis_std_sample_description AS sample_description ON cast(psample_detail.sample_description as bigint) = sample_description.\"ID\"";
            }

        }
        $has_test_date      = !empty($data['test_date']['min']) || !empty($data['test_date']['max']) || isset($data['test_date']['is_show']);
        $has_organism       = !empty($data['result_organism']['value']) || isset($data['result_organism']['is_show']);
        $has_antibiotic     = !empty($data['antibiotic']['value']) || isset($data['antibiotic']['is_show']);
        $has_sensitivity    = !empty($data['sensitivity']['value']) || isset($data['sensitivity']['is_show']);
        if ($has_test_date || $has_organism || $has_antibiotic || $has_sensitivity ) {                        
            $fields_seleted .= ", to_char(ptest_result.test_date, 'DD/MM/YYYY') AS test_date ";
            $JOIN_CLAUSE_STRING .= " LEFT JOIN camlis_ptest_result AS ptest_result ON psample_test.\"ID\" = ptest_result.patient_test_id AND ptest_result.patient_sample_id = psample.\"ID\" AND ptest_result.status = 1 ";
            $JOIN_CLAUSE_STRING .= " LEFT JOIN camlis_std_test_organism AS test_organism ON ptest_result.type = 1 AND ptest_result.result = cast(test_organism.\"ID\" as varchar)";

            if ($has_organism) {
                                      
                $fields_seleted .= ", (CASE WHEN ptest_result.type = 1 THEN CONCAT(organism.organism_name, CASE organism.organism_value WHEN 1 THEN ' Positive' WHEN 2 THEN ' Negative' ELSE '' END) ELSE ptest_result.result END) AS result_organism";
                $JOIN_CLAUSE_STRING .= " LEFT JOIN camlis_std_organism AS organism ON test_organism.organism_id = organism.\"ID\" ";
            }

            if ($has_antibiotic || $has_sensitivity) {
                
                $fields_seleted .= ", antibiotic.antibiotic_name AS antibiotic, presult_antibiotic.test_zone as \"MIC\",presult_antibiotic.disc_diffusion as \"DD\",
                (CASE presult_antibiotic.sensitivity
                    WHEN 1 THEN 'Sensitive'
                    WHEN 2 THEN 'Resistant'
                    WHEN 3 THEN 'Intermediate'
                END) AS sensitivity ";
                $JOIN_CLAUSE_STRING .= " LEFT JOIN camlis_ptest_result_antibiotic AS presult_antibiotic ON ptest_result.\"ID\" = presult_antibiotic.presult_id";
                $JOIN_CLAUSE_STRING .= " LEFT JOIN camlis_std_antibiotic AS antibiotic ON presult_antibiotic.antibiotic_id = antibiotic.\"ID\" ";
            }
        }

        //Condition
		//Laboratory ID
        if (isset($data['laboratory']['value']) && count($data['laboratory']['value']) > 0) {
            
        }
        //Patient ID
        if (isset($data['patient_code']['value']) && !empty($data['patient_code']['value'])) {
            $WHERE_CLAUSE .= " patient.patient_code = '".$data['patient_code']['value']."' AND ";
        }
        //Patient Name
        if (isset($data['patient_name']['value']) && !empty($data['patient_name']['value'])) {
            
            $WHERE_CLAUSE .= " patient.patient_name = '".$data['patient_name']['value']."' AND ";
        }
        //Patient Age
        if (isset($data['patient_age']) && (!empty($data['patient_age']['min']) || !empty($data['patient_age']['max']))) {
            $min_age = empty($data['patient_age']['min']) ? 0 : $data['patient_age']['min'];
            $max_age = empty($data['patient_age']['max']) ? PHP_INT_MAX : $data['patient_age']['max'];            
            
            $WHERE_CLAUSE .= " DATE_PART('year',age(psample.collected_date, patient.dob)) >= $min_age AND DATE_PART('year',age(psample.collected_date, patient.dob)) <= $max_age AND";
        }
        //Patient Gender
        if (isset($data['patient_gender']['value']) && !empty($data['patient_gender']['value'])) {
            $WHERE_CLAUSE .= " patient.sex = ".$data['patient_gender']['value']." AND ";
        }
        //Patient Province
        if (isset($data['province']['value']) && count($data['province']['value']) > 0) {
            $WHERE_CLAUSE .= " patient.province IN (".implode(",",$data['province']['value']).") AND";

        }
        //Patient District
        if (isset($data['district']['value']) && count($data['district']['value']) > 0) {            
            $WHERE_CLAUSE .= " patient.district IN (".implode(",",$data['district']['value']).") AND";
        }
        //Department
        if (isset($data['department']['value']) && count($data['department']['value']) > 0) {            
            $WHERE_CLAUSE .= " department.\"ID\" IN (".implode(",",$data['department']['value']).") AND";
        }
        //Sample Type
        if (isset($data['sample_type']['value']) && count($data['sample_type']['value']) > 0) {            
            $WHERE_CLAUSE .= " sample_type.\"ID\" IN (".implode(",",$data['sample_type']['value']).") AND";
        }
        //Sample Description
        if (isset($data['sample_description']['value']) && count($data['sample_description']['value']) > 0) {            
            $WHERE_CLAUSE .= " sample_description.\"ID\" = ".$data['sample_description']['value']." AND ";
        }
        //Sample Number
        if (isset($data['sample_number']['value']) && !empty($data['sample_number']['value'])) {            
            $WHERE_PSAMPLE_CLAUSE .= " AND psample.sample_number = ".$data['sample_number']['value']." ";
        }
        //Collected Date
        /*if (isset($data['collected_date']) && !empty($data['collected_date']['min']) || !empty($data['collected_date']['max'])) {
            $min = DateTime::createFromFormat('d/m/Y', $data['collected_date']['min']);
            $max = DateTime::createFromFormat('d/m/Y', $data['collected_date']['max']);
            $min = $min ? $min->format('Y-m-d') : '';
            $max = $max ? $max->format('Y-m-d') : '';
            $WHERE_PSAMPLE_CLAUSE .= " AND psample.collected_date >= '$min' AND psample.collected_date <= '$max' ";
        }
        //Received Date
        if (isset($data['received_date']) && !empty($data['received_date']['min']) || !empty($data['received_date']['max'])) {
            $min = DateTime::createFromFormat('d/m/Y', $data['received_date']['min']);
            $max = DateTime::createFromFormat('d/m/Y', $data['received_date']['max']);
            $min = $min ? $min->format('Y-m-d') : '';
            $max = $max ? $max->format('Y-m-d') : '';
            $WHERE_PSAMPLE_CLAUSE .= " AND psample.received_date >= '$min' AND psample.received_date <= '$max' ";
        }*/
        //Sample Source
        if (isset($data['sample_source']['value']) && count($data['sample_source']['value']) > 0) {
            $this->db->where_in('psample.sample_source_id', $data['sample_source']['value']);
            $WHERE_PSAMPLE_CLAUSE .= " AND psample.sample_source_id IN (".implode(", ",$data['sample_source']['value']).")";
        }
        //Requester
        if (isset($data['requester']['value']) && count($data['requester']['value']) > 0) {            
            $WHERE_PSAMPLE_CLAUSE .= " AND psample.requester_id IN (".implode(", ",$data['requester']['value']).")";
        }
        //Test Date
        if (isset($data['test_date']) && !empty($data['test_date']['min']) || !empty($data['test_date']['max'])) {
            $min = DateTime::createFromFormat('d/m/Y', $data['test_date']['min']);
            $max = DateTime::createFromFormat('d/m/Y', $data['test_date']['max']);
            $min = $min ? $min->format('Y-m-d') : '';
            $max = $max ? $max->format('Y-m-d') : '';            
            $WHERE_CLAUSE .= " ptest_result.test_date >= '$min' AND ptest_result.test_date <= '$max' AND ";
        }
        //Test Name/group result
        if (isset($data['test']['value']) && count($data['test']['value']) > 0) {            
            $WHERE_CLAUSE .= " test.\"ID\" IN (".implode(", ",$data['test']['value']).") AND ";
        }
        //Organism
        if (isset($data['result_organism']['value']) && count($data['result_organism']['value']) > 0) {            
            $WHERE_CLAUSE .= " organism.\"ID\" IN (".implode(", ",$data['result_organism']['value']).") AND";
        }
        //Antibiotic
        if (isset($data['antibiotic']['value']) && count($data['antibiotic']['value']) > 0) {            
            $WHERE_CLAUSE .= "antibiotic.\"ID\" IN (".implode(",",$data['antibiotic']['value']).") AND ";
        }
        //Sensitivity
        if (isset($data['sensitivity']['value']) && !empty($data['sensitivity']['value'])) {            
            $WHERE_CLAUSE .= " presult_antibiotic.sensitivity = ".$data['sensitivity']['value']." AND ";
        }

        //14022022
        if (!empty($data['sample_status']['value']) || isset($data['sample_status']['is_show'])) {            
            $fields_seleted .= ", res_comment.reject_comment";
            $JOIN_CLAUSE_STRING .= " LEFT JOIN camlis_result_comment AS res_comment ON res_comment.patient_sample_id = psample.\"ID\" AND department_sample.\"ID\" = res_comment.department_sample_id";
        }
        // END
        //Sample Status
        $has_is_rejected_value      = isset($data['is_rejected']['value']) && !empty($data['is_rejected']['value']);
        $has_for_research_value     = isset($data['for_research']['value']) && !empty($data['for_research']['value']);
        $has_is_urgent_value        = isset($data['is_urgent']['value']) && !empty($data['is_urgent']['value']);
        if ($has_is_rejected_value || $has_for_research_value || $has_is_urgent_value) $this->db->group_start();
        if ($has_is_rejected_value) {
            $WHERE_PSAMPLE_CLAUSE .= " AND psample.is_rejected = ".$data['is_rejected']['value']." ";
        }
        if ($has_for_research_value) {
            $WHERE_PSAMPLE_CLAUSE .= " AND psample.for_research = ".$data['for_research']['value']." ";
        }
        if ($has_is_urgent_value) {            
            $WHERE_PSAMPLE_CLAUSE .= " AND psample.is_urgent = ".$data['is_urgent']['value']." ";
        }
        if ($has_is_rejected_value || $has_for_research_value || $has_is_urgent_value) $this->db->group_end();

        //Payment type
        if (isset($data['payment_type']['value']) && !empty($data['payment_type']['value'])) {            
            $WHERE_PSAMPLE_CLAUSE .= " AND psample.payment_type_id = ".$data['payment_type']['value']."";
        }

        //GroupBy
        //$this->db->group_by('psample."ID"');
        $GROUP_BY_CLAUSE .= " psample.\"ID\" ";

        if ($has_antibiotic || $has_sensitivity){                        
            $GROUP_BY_CLAUSE .= ", presult_antibiotic.\"ID\" , antibiotic.antibiotic_name, presult_antibiotic.sensitivity";
        }

        if (isset($data['organism']['is_show'])) {            
            $GROUP_BY_CLAUSE .= ", ptest_result.\"ID\"";
        }
        
        if (isset($data['test']['is_show'])) {            
            $GROUP_BY_CLAUSE .= ", test.test_name";
        }
        if (isset($data['sample_type']['is_show'])) {            
            $GROUP_BY_CLAUSE .= ', sample_type."ID"';
        }
        if (isset($data['department']['is_show'])) {            
            $GROUP_BY_CLAUSE .= ", department.\"ID\" ";
        }
        if (isset($data['sample_source']['is_show'])) {            
            $GROUP_BY_CLAUSE .= ", sample_source.source_name";
        }
        if (isset($data['province']['is_show'])){            
            $GROUP_BY_CLAUSE .= ", province.name_kh";
        }
        
        if (isset($data['district']['is_show'])){            
            $GROUP_BY_CLAUSE .= ", district.name_kh, commune.name_kh, village.name_kh ";
        }        

        if ($has_department){            
            $GROUP_BY_CLAUSE .= ", department.department_name ";
        }
        if (isset($data['payment_type']['is_show'])){            
            $GROUP_BY_CLAUSE .= ", payment_type.name ";
        }
        if (isset($data['sample_description']['is_show'])){            
            $GROUP_BY_CLAUSE .= ", sample_description.description";
        }
        if (isset($data['requester']['is_show'])){            
            $GROUP_BY_CLAUSE .= ", requester.requester_name";
        }
        if (isset($data['result_organism']['is_show'])){            
            $GROUP_BY_CLAUSE .= ", organism.organism_name, organism.organism_value , ptest_result.result";
        }
        if ($has_test_date || $has_organism || $has_antibiotic || $has_sensitivity ) {
            $GROUP_BY_CLAUSE .= ", ptest_result.test_date , ptest_result.type";
        }
        if ($has_department || $has_sample_type || $has_sample_description || $has_test) {
            //$this->db->group_by('test.test_name');            
            //$this->db->group_by('psample_test.ref_range_max_value');
            //$this->db->group_by('psample_test.ref_range_min_value');
            //$this->db->group_by('psample_test.unit_sign');
            $GROUP_BY_CLAUSE .= ", test.test_name, psample_test.ref_range_max_value, psample_test.ref_range_min_value, psample_test.unit_sign";
            if($has_sample_description){
                //$this->db->group_by('psample_detail.sample_volume1');
                //$this->db->group_by('psample_detail.sample_volume2');
                $GROUP_BY_CLAUSE .= ", psample_detail.sample_volume1, psample_detail.sample_volume2";
            }
        }
        //14022022
        if (!empty($data['sample_status']['value']) || isset($data['sample_status']['is_show'])) {
            $GROUP_BY_CLAUSE .= ", res_comment.reject_comment";
        }
        //End
        $GROUP_BY_CLAUSE .= ", psample.\"labID\",
        psample.sample_number,
        lab.name_en, 
        collected_date,
        received_date,
        for_research,
        sample_status,
        patient.patient_code, 
        patient.patient_name,
        patient.nationality_en,
        psample.clinical_history,
        patient.dob, patient.sex , patient.phone";
        
        if(strlen($WHERE_CLAUSE)>0){
            $leng = strlen($WHERE_CLAUSE);
            $WHERE_CLAUSE = substr($WHERE_CLAUSE,0,($leng - 4));
            $WHERE_CLAUSE = "WHERE ".$WHERE_CLAUSE;          
        }
        
        $sql = "
            WITH psample AS (
                SELECT *
                FROM camlis_patient_sample AS psample
                WHERE psample.status = 1 AND psample.\"labID\" = ANY (VALUES".$lab_string.") ".$WHERE_PSAMPLE_CLAUSE."
            ), lab AS (
                SELECT * FROM camlis_laboratory as lab
                WHERE lab.status = true AND lab.\"labID\" = ANY (VALUES".$lab_string.") 
            ) ".$with_clause_sample_source." ".$patient_clause." SELECT psample.\"labID\",
                psample.sample_number,
                to_char(psample.collected_date, 'DD/MM/YYYY') AS collected_date,
                to_char(psample.received_date, 'DD/MM/YYYY') AS received_date,
                CONCAT_WS(
                    ', ',
                    CASE psample.for_research 
                        WHEN 0 THEN ''
                        WHEN 1 THEN 'សង្ស័យ' 
                        WHEN 2 THEN 'រលាកសួត'
                        WHEN 3 THEN 'តាមដាន'
                        WHEN 4 THEN 'អ្នកប៉ះពាល់'
                        WHEN 5 THEN 'បុគ្គលិកពេទ្យ'
                        WHEN 6 THEN 'ផ្សេងទៀត'
                        WHEN 7 THEN 'ពលករចំណាកស្រុក'
                        WHEN 8 THEN 'អ្នកដំណើរតាមយន្តហោះ'
                        WHEN 9 THEN 'វិញ្ញាបនបត្រ'
                        WHEN 10 THEN 'តាមដានអ្នកជំងឺវិជ្ជមាន'
                        WHEN 11 THEN 'ព្រឹត្តការណ៏ ២០គុម្ភះ'
                        WHEN 12 THEN 'ករណីស្លាប់'
                        ELSE NULL END,
                    CASE WHEN psample.is_rejected = 1 THEN 'បដិសេធសំណាក' ELSE NULL END,
                    CASE WHEN psample.is_urgent = 1 THEN 'បន្ទាន់' ELSE NULL END
                ) AS sample_status,
                psample.clinical_history as diagnosis
                ".$fields_seleted."
                FROM psample
                ".$JOIN_CLAUSE_STRING."
                ".$WHERE_CLAUSE."
                ".$GROUP_BY_CLAUSE."
        ";

        return $this->db->query($sql)->result_array();
    }

    /**
     * Get Patient code
     * @param $patient_code
     * @param $limit
     */
    public function lookup_patient_code($patient_code, $limit = 25) {
        $this->db->select('
            patient.pid,
            patient.patient_code,
            patient.patient_name,
            psample.sample_number
        ');
        $this->db->from("v_camlis_all_patients AS patient");
        $this->db->join('camlis_patient_sample AS psample', 'patient.pid = psample.patient_id AND psample."labID" = patient.lab_id');
        $this->db->where('psample."labID"', $this->laboratory_id);
        $this->db->where('psample.status', 1);
        $this->db->like('patient.patient_code', $patient_code, 'after');
        if ($limit) $this->db->limit($limit);

        return $this->db->get()->result_array();
    }

    /**
     * Get TAT report data
     * @param $start_date
     * @param $end_date
     * @param $testing_type
     * @return mixed
     */
    public function get_tat_report_data($start_date, $end_date, $testing_type) {
        $this->db->select("
            CONCAT(psample.collected_date, ' ', psample.collected_time) AS collected_date,
            CONCAT(psample.received_date, ' ', psample.received_time) AS received_date,
            CASE WHEN psample.is_urgent = 1 THEN 'URGENT' ELSE 'ROUTINE' END AS type,
            psample.printedDate,
			dep.\"ID\" AS department_id,
			dep.department_name,
			sample_test.group_result,
			ptest.patient_sample_id,
			_t.test_count
		");
        $this->db->from('camlis_patient_sample_tests AS ptest');
        $this->db->join('camlis_patient_sample AS psample', 'ptest.patient_sample_id = psample."ID"', 'inner');
        $this->db->join('camlis_std_sample_test AS sample_test', 'ptest.sample_test_id = sample_test."ID"', 'inner');
        $this->db->join('camlis_std_test AS test', 'sample_test.test_id = test."ID"', 'inner');
        $this->db->join('camlis_std_department_sample AS dsample', 'dsample."ID" = sample_test.department_sample_id', 'inner');
        $this->db->join('camlis_std_department AS dep', 'dep."ID" = dsample.department_id', 'inner');
        $this->db->join('
            (SELECT ptest.patient_sample_id,
			        COUNT(DISTINCT sample_test.group_result) AS test_count
            FROM camlis_patient_sample_tests AS ptest
            INNER JOIN camlis_std_sample_test AS sample_test ON ptest.sample_test_id = sample_test."ID"
           	INNER join camlis_patient_sample as psample ON ptest.patient_sample_id = psample."ID"
            WHERE ptest.status = 1
                  AND LENGTH(sample_test.group_result) > 0
                  AND psample.received_date >=\''.$start_date.'\'
                  AND psample.received_date <=\''.$end_date.'\'
                  AND psample."labID" = '.$this->laboratory_id.'
            GROUP BY ptest.patient_sample_id) _t
        ', 'psample."ID" = _t.patient_sample_id', 'inner');
        $this->db->where('ptest.status', 1);
        $this->db->where('psample.status', 1);
        $this->db->where('psample."labID"', $this->laboratory_id);
        $this->db->where('psample.received_date >=', $start_date);
        $this->db->where('psample.received_date <=', $end_date);
        $this->db->where('LENGTH(sample_test.group_result) >', 0);
        $this->db->where('psample.is_printed', TRUE);
        $this->db->order_by('dep.order', 'asc');
        $this->db->order_by('sample_test.order', 'asc');
        $this->db->group_by('dep."ID"');
        $this->db->group_by('psample."ID"');
        $this->db->group_by('ptest.patient_sample_id');
        $this->db->group_by('sample_test.group_result');
        $this->db->group_by('sample_test.order');
        $this->db->group_by('_t.test_count');

        if ($testing_type == "SINGLE") {
            $this->db->where('_t.test_count', 1);
        }
        else if ($testing_type == "MULTIPLE") {
            $this->db->where('_t.test_count >', 1);
        }

        return $this->db->get()->result_array();
    }

    /**
     * Get Patient count by age group
     * @param $start_date
     * @param $end_date
     * @param $laboratory_id
     * @return array
     */
    public function patient_by_age_group($start_date, $end_date, $laboratory_id) {
        $extraWhere = count($laboratory_id) > 0 ? "AND psample.\"labID\" IN (".implode(',', $laboratory_id).")" : "";
        $sql = "
            SELECT 
                _t.age_group,
                /*SUM(IF(_t.sex = 1, 1, 0)) AS male,
                SUM(IF(_t.sex = 2, 1, 0)) AS female */
                SUM( CASE WHEN _t.sex = 1 THEN 1 ELSE 0 END ) AS male,
                SUM( CASE WHEN _t.sex = 2 THEN 1 ELSE 0 END ) AS female
            FROM 
            (
            SELECT 
                patient.sex,
                (CASE
                    WHEN (psample.collected_date - patient.dob <= 29) THEN '0 - 29 days'
                    WHEN ((psample.collected_date - patient.dob) / 30) BETWEEN 1 AND 11 THEN '1 - 11 months'
                    WHEN DATE_PART('year', AGE(psample.collected_date, patient.dob)) BETWEEN 1 AND 4 THEN '1 - 4 years'
                    WHEN DATE_PART('year', AGE(psample.collected_date, patient.dob)) BETWEEN 5 AND 14 THEN '5 - 14 years'
                    WHEN DATE_PART('year', AGE(psample.collected_date, patient.dob)) BETWEEN 15 AND 24 THEN '15 - 24 years'
                    WHEN DATE_PART('year', AGE(psample.collected_date, patient.dob)) BETWEEN 25 AND 49 THEN '25 - 49 years'
                    WHEN DATE_PART('year', AGE(psample.collected_date, patient.dob)) BETWEEN 50 AND 64 THEN '50 - 64 years'
                    ELSE '>= 65 years'  
                END) AS age_group
            FROM camlis_patient_sample AS psample
            INNER JOIN v_camlis_all_patients AS patient ON psample.patient_id = patient.pid AND psample.\"labID\" = patient.lab_id
            WHERE psample.status = 1 AND psample.received_date BETWEEN ? AND ? $extraWhere
            ) _t
            WHERE _t.age_group IS NOT NULL
            GROUP BY _t.age_group";

        return $this->db->query($sql, [$start_date, $end_date])->result_array();
    }

    public function patient_by_sample_source($start_date, $end_date, $laboratory_id) {
        $extraWhere = count($laboratory_id) > 0 ? "AND psample.\"labID\" IN (".implode(',', $laboratory_id).")" : "";
        $sql = "SELECT source.source_name,
                       /*SUM(IF(patient.sex = 1, 1, 0)) AS male,
                       SUM(IF(patient.sex = 2, 1, 0)) AS female */
                       SUM(CASE WHEN patient.sex = 1 THEN 1 ELSE 0 END) AS male,
                       SUM(CASE WHEN patient.sex = 2 THEN 1 ELSE 0 END) AS female
                FROM camlis_patient_sample AS psample
                INNER JOIN camlis_lab_sample_source AS source ON psample.sample_source_id = source.\"ID\"
                INNER JOIN v_camlis_all_patients AS patient ON psample.patient_id = patient.pid AND psample.\"labID\" = patient.lab_id
                WHERE psample.status = 1
                      AND psample.received_date BETWEEN ? AND ?
                      $extraWhere
                GROUP BY source.\"ID\"";
        return $this->db->query($sql, [$start_date, $end_date])->result_array();
    }

    public function patient_by_sample_type($start_date, $end_date, $laboratory_id) {
        $extraWhere = count($laboratory_id) > 0 ? "AND psample.\"labID\" IN (".implode(',', $laboratory_id).")" : "";
        $sql = "SELECT _t.sample_name,
                       /*SUM(IF(_t.sex = 1, 1, 0)) AS male,
                       SUM(IF(_t.sex = 2, 1, 0)) AS female*/
                       SUM(CASE WHEN _t.sex = 1 THEN 1 ELSE 0 END) AS male,
                       SUM(CASE WHEN _t.sex = 2 THEN 1 ELSE 0 END) AS female
                FROM
                (SELECT sample.\"ID\" AS sample_id,
                        sample.sample_name,
                        patient.sex
                FROM camlis_patient_sample AS psample
                INNER JOIN v_camlis_all_patients AS patient ON psample.patient_id = patient.pid AND psample.\"labID\" = patient.lab_id
                INNER JOIN camlis_patient_sample_tests AS ptest ON psample.\"ID\" = ptest.patient_sample_id
                INNER JOIN camlis_std_sample_test AS sample_test ON ptest.sample_test_id = sample_test.\"ID\"
                INNER JOIN camlis_std_department_sample AS dsample ON sample_test.department_sample_id = dsample.\"ID\"
                INNER JOIN camlis_std_sample AS sample ON dsample.sample_id = sample.\"ID\"
                WHERE psample.status = 1
                            AND ptest.status = 1
                            AND psample.received_date BETWEEN ? AND ?
                            $extraWhere
                GROUP BY psample.\"ID\", sample.\"ID\", patient.sex
                ) _t
                GROUP BY _t.sample_id , _t.sample_name";
        return $this->db->query($sql, [$start_date, $end_date])->result_array();
    }

    public function get_patient_by_culture($obj) {
        $labid = $this->session->userdata('laboratory')->labID;
        
        $tblallpatients="(SELECT CAST(pmrs_patient.pid AS VARCHAR) AS pid, 
                    psample.\"labID\" AS lab_id, 
                    CAST(pmrs_patient.pid AS varchar(50)) AS patient_id, 
                    CAST(pmrs_patient.pid AS varchar(50)) AS patient_code, 
                    pmrs_patient.name AS patient_name, 
                    (CASE WHEN pmrs_patient.sex = 'M' THEN 1 WHEN pmrs_patient.sex = 'F' THEN 2 ELSE 0 END) AS sex, 
                    pmrs_patient.dob AS dob, 
                    pmrs_patient.phone AS phone, 
                    pmrs_patient.province AS province, 
                    pmrs_patient.district AS district, 
                    pmrs_patient.commune AS commune, 
                    pmrs_patient.village AS village 
			FROM (camlis_patient_sample psample 
                    JOIN camlis_pmrs_patient pmrs_patient ON (
                        (psample.patient_id = pmrs_patient.pid) AND (pmrs_patient.status = 1)
                    )
                 ) 
			WHERE psample.status = 1 and psample.\"labID\" = $labid
            GROUP BY psample.patient_id, psample.\"labID\" , pmrs_patient.pid
            UNION ALL
            SELECT CAST(outside_patient.pid AS VARCHAR(50)) AS pid,
                    outside_patient.lab_id AS lab_id,
                    outside_patient.patient_id AS patient_id,
                    outside_patient.patient_code AS patient_code,
                    outside_patient.patient_name AS patient_name,
                    outside_patient.sex AS sex,
                    outside_patient.dob AS dob,
                    outside_patient.phone AS phone,
                    outside_patient.province AS province,
                    outside_patient.district AS district,
                    outside_patient.commune AS commune,
                    outside_patient.village AS village
            FROM camlis_outside_patient outside_patient
            WHERE outside_patient.status = 1 and outside_patient.lab_id = $labid
            )";
		$sql="
            SELECT _t.title, _t.sampletype,
                
                SUM(CASE WHEN (_t.sex = 1 AND _t.age_group = '1') THEN 1 ELSE 0 END) AS SEX1M,
                SUM(CASE WHEN (_t.sex = 2 AND _t.age_group = '1') THEN 1 ELSE 0 END) AS SEX1F,
				SUM(CASE WHEN (_t.sex = 1 AND _t.age_group = '2') THEN 1 ELSE 0 END) AS SEX2M,
				SUM(CASE WHEN (_t.sex = 2 AND _t.age_group = '2') THEN 1 ELSE 0 END) AS SEX2F,
				SUM(CASE WHEN (_t.sex = 1 AND _t.age_group = '3') THEN 1 ELSE 0 END) AS SEX3M,
				SUM(CASE WHEN (_t.sex = 2 AND _t.age_group = '3') THEN 1 ELSE 0 END) AS SEX3F,
				SUM(CASE WHEN (_t.sex = 1 AND _t.age_group = '4') THEN 1 ELSE 0 END) AS SEX4M,
				SUM(CASE WHEN (_t.sex = 2 AND _t.age_group = '4') THEN 1 ELSE 0 END) AS SEX4F,
				SUM(CASE WHEN (_t.sex = 1 AND _t.age_group = '5') THEN 1 ELSE 0 END) AS SEX5M,
				SUM(CASE WHEN (_t.sex = 2 AND _t.age_group = '5') THEN 1 ELSE 0 END) AS SEX5F,
				SUM(CASE WHEN (_t.sex = 1 AND _t.age_group = '6') THEN 1 ELSE 0 END) AS SEX6M,
				SUM(CASE WHEN (_t.sex = 2 AND _t.age_group = '6') THEN 1 ELSE 0 END) AS SEX6F,
				SUM(CASE WHEN (_t.sex = 1 AND _t.age_group = '7') THEN 1 ELSE 0 END) AS SEX7M,
				SUM(CASE WHEN (_t.sex = 2 AND _t.age_group = '7') THEN 1 ELSE 0 END) AS SEX7F,
				SUM(CASE WHEN (_t.sex = 1 AND _t.age_group = '8') THEN 1 ELSE 0 END) AS SEX8M,
                SUM(CASE WHEN (_t.sex = 2 AND _t.age_group = '8') THEN 1 ELSE 0 END) AS SEX8F,
				COUNT(_t.sampletype) AS total
            FROM (
                SELECT distinct '1.TOTAL SAMPLE TESTED' as title, st.group_result as sampletype, patient.sex,
                    (CASE					
                    WHEN (ps.collected_date - patient.dob <= 29) THEN '1'
                    WHEN ((ps.collected_date - patient.dob) / 30) BETWEEN 1 AND 11 THEN '2'
                    WHEN DATE_PART('year', AGE(ps.collected_date, patient.dob)) BETWEEN 1 AND 4 THEN '3'
                    WHEN DATE_PART('year', AGE(ps.collected_date, patient.dob)) BETWEEN 5 AND 4 THEN '4'
                    WHEN DATE_PART('year', AGE(ps.collected_date, patient.dob)) BETWEEN 15 AND 24 THEN '5'
                    WHEN DATE_PART('year', AGE(ps.collected_date, patient.dob)) BETWEEN 25 AND 49 THEN '6'
                    WHEN DATE_PART('year', AGE(ps.collected_date, patient.dob)) BETWEEN 50 AND 64 THEN '7'
                    ELSE '8' END) AS age_group, 
                    ps.sample_number, ps.\"labID\", ps.received_date, ps.received_time 
                FROM $tblallpatients as patient
                INNER JOIN camlis_patient_sample ps ON patient.pid = ps.patient_id 
                INNER JOIN camlis_patient_sample_tests pst ON ps.\"ID\" = pst.patient_sample_id
                INNER JOIN camlis_std_sample_test st ON pst.sample_test_id = st.\"ID\"
                WHERE ps.status=1 and st.test_id = 170                
                UNION ALL 
                SELECT distinct '2.Number of positive sample' as title, concat(st.group_result,'_positive') as sampletype, patient.sex,
                (CASE
                    WHEN (ps.collected_date - patient.dob <= 29) THEN '1'
                    WHEN ((ps.collected_date - patient.dob) / 30) BETWEEN 1 AND 11 THEN '2'
                    WHEN DATE_PART('year', AGE(ps.collected_date, patient.dob)) BETWEEN 1 AND 4 THEN '3'
                    WHEN DATE_PART('year', AGE(ps.collected_date, patient.dob)) BETWEEN 5 AND 4 THEN '4'
                    WHEN DATE_PART('year', AGE(ps.collected_date, patient.dob)) BETWEEN 15 AND 24 THEN '5'
                    WHEN DATE_PART('year', AGE(ps.collected_date, patient.dob)) BETWEEN 25 AND 49 THEN '6'
                    WHEN DATE_PART('year', AGE(ps.collected_date, patient.dob)) BETWEEN 50 AND 64 THEN '7'
                    ELSE '8'	
                END) AS age_group, ps.sample_number, ps.\"labID\", ps.received_date, ps.received_time 
				FROM $tblallpatients as patient
				INNER JOIN camlis_patient_sample ps ON patient.pid = ps.patient_id 
				INNER JOIN camlis_patient_sample_tests pst ON ps.\"ID\" = pst.patient_sample_id
				INNER JOIN camlis_std_sample_test st ON pst.sample_test_id = st.\"ID\"
				INNER JOIN camlis_ptest_result tr ON tr.patient_sample_id = ps.\"ID\"
				INNER JOIN camlis_ptest_result_antibiotic ran ON ran.presult_id = tr.\"ID\"
                WHERE ps.status=1 AND tr.status = 1 and st.test_id = 170
            ) _t
			WHERE _t.\"labID\" = ".$labid." 
                AND _t.received_date >= ?
                AND _t.received_date <= ?
                AND _t.received_time BETWEEN ? AND ? 
			GROUP BY _t.title,_t.sampletype
			ORDER BY _t.title,_t.sampletype";
			
	return $this->db->query($sql,array($obj->start,$obj->end, $obj->start_time.":00", $obj->end_time.":00"))->result_array();
	}


    public function patient_by_department($start_date, $end_date, $laboratory_id) {
        $extraWhere = count($laboratory_id) > 0 ? "AND psample.\"labID\" IN (".implode(',', $laboratory_id).")" : "";
        $sql = "SELECT _t.department_name,
                      /* SUM(IF(_t.sex = 1, 1, 0)) AS male,
                       SUM(IF(_t.sex = 2, 1, 0)) AS female */
                       SUM(CASE WHEN _t.sex = 1 THEN 1 ELSE 0 END) AS male,
                       SUM(CASE WHEN _t.sex = 2 THEN 1 ELSE 0 END) AS female
                FROM
                (   SELECT department.\"ID\" AS department_id,
                            department.department_name,
                            patient.sex
                    FROM camlis_patient_sample AS psample
                    INNER JOIN v_camlis_all_patients AS patient ON psample.patient_id = patient.pid AND psample.\"labID\" = patient.lab_id
                    INNER JOIN camlis_patient_sample_tests AS ptest ON psample.\"ID\" = ptest.patient_sample_id
                    INNER JOIN camlis_std_sample_test AS sample_test ON ptest.sample_test_id = sample_test.\"ID\"
                    INNER JOIN camlis_std_department_sample AS dsample ON sample_test.department_sample_id = dsample.\"ID\"
                    INNER JOIN camlis_std_department AS department ON dsample.department_id = department.\"ID\"
                    WHERE psample.status = 1
                        AND ptest.status = 1
                        AND psample.received_date BETWEEN ? AND ?
                        $extraWhere
                    GROUP BY psample.\"ID\", department.\"ID\", patient.sex
                ) _t
                GROUP BY _t.department_id, _t.department_name";
        return $this->db->query($sql, [$start_date, $end_date])->result_array();
    }
    public function patient_by_month($start_date, $end_date, $laboratory_id) {
        $extraWhere = count($laboratory_id) > 0 ? "AND psample.\"labID\" IN (".implode(',', $laboratory_id).")" : "";
        $sql = "SELECT /*MONTHNAME(psample.received_date) AS month,
                       YEAR(psample.received_date) AS year,*/ 
                       to_char(psample.received_date,'Month') AS month,
                       to_char(psample.received_date, 'YYYY') AS year,

                       SUM(CASE WHEN patient.sex = 1 THEN 1 ELSE 0 END) AS male,
                       SUM(CASE WHEN patient.sex = 2 THEN 1 ELSE 0 END) AS female
                FROM camlis_patient_sample AS psample
                INNER JOIN v_camlis_all_patients AS patient ON psample.patient_id = patient.pid AND psample.\"labID\" = patient.lab_id
                WHERE psample.status = 1
                      AND psample.received_date BETWEEN ? AND ?
                      $extraWhere
                
                GROUP BY to_char(psample.received_date,'Month'), to_char(psample.received_date, 'YYYY')";
        return $this->db->query($sql, [$start_date, $end_date])->result_array();
    }

    public function sample_type_by_month($sample_type_id, $start_date, $end_date, $laboratory_id) {
        $extraWhere = count($laboratory_id) > 0 ? "AND psample.\"labID\" IN (".implode(',', $laboratory_id).")" : "";
        $sql = "SELECT _t.month,
                       _t.year,
                       SUM(_t.count) AS count,
                       _t.sample_id
                FROM(                
                    SELECT 
                        to_char(psample.received_date,'Month') AS month,
                        to_char(psample.received_date, 'YYYY') AS year,
                        COUNT(DISTINCT dsample.sample_id) As count,
                        dsample.sample_id

                    FROM camlis_patient_sample AS psample
                    INNER JOIN v_camlis_all_patients AS patient ON psample.patient_id = patient.pid AND psample.\"labID\" = patient.lab_id
                    INNER JOIN camlis_patient_sample_tests AS ptest ON psample.\"ID\" = ptest.patient_sample_id
                    INNER JOIN camlis_std_sample_test AS sample_test ON ptest.sample_test_id = sample_test.\"ID\"
                    INNER JOIN camlis_std_department_sample AS dsample ON sample_test.department_sample_id = dsample.\"ID\"
                    WHERE psample.status = 1
                        AND ptest.status = 1
                        AND psample.received_date BETWEEN ? AND ?
                        AND dsample.sample_id = ?
                        $extraWhere
                    GROUP BY psample.\"ID\", dsample.\"ID\"
                ) _t
                GROUP BY _t.month, _t.year, _t.sample_id";
        return $this->db->query($sql, [$start_date, $end_date, $sample_type_id])->result_array();
    }

    public function test_by_month($group_result, $start_date, $end_date, $laboratory_id) {
        $extraWhere = count($laboratory_id) > 0 ? "AND psample.\"labID\" IN (".implode(',', $laboratory_id).")" : "";
        $sql = "SELECT _t.month,
                       _t.year,
                       COUNT(_t.group_result) AS count
                FROM(
                    SELECT to_char(psample.received_date,'Month') AS month,
                        to_char(psample.received_date, 'YYYY') AS year,
                        sample_test.group_result
                    FROM camlis_patient_sample AS psample
                    INNER JOIN v_camlis_all_patients AS patient ON psample.patient_id = patient.pid AND psample.\"labID\" = patient.lab_id
                    INNER JOIN camlis_patient_sample_tests AS ptest ON psample.\"ID\" = ptest.patient_sample_id
                    INNER JOIN camlis_std_sample_test AS sample_test ON ptest.sample_test_id = sample_test.\"ID\"
                    WHERE psample.status = 1
                        AND ptest.status = 1
                        AND psample.received_date BETWEEN ? AND ?
                        AND LOWER(sample_test.group_result) = ?
                        $extraWhere
                    GROUP BY to_char(psample.received_date,'Month'), to_char(psample.received_date, 'YYYY'), psample.\"ID\", sample_test.group_result
                ) _t
                GROUP BY _t.month, _t.year";
        return $this->db->query($sql, [$start_date, $end_date, strtolower($group_result)])->result_array();
    }

    public function patient_by_province($start_date, $end_date, $laboratory_id = NULL) {
        $extraWhere = count($laboratory_id) > 0 ? "AND psample.\"labID\" IN (".implode(',', $laboratory_id).")" : "";
        $sql = "SELECT province.code AS province_code,
                       province.name_kh AS province_name_kh,
                       province.name_en AS province_name_en,
                       COUNT(patient.pid) AS patient_count
                FROM v_camlis_all_patients AS patient
                INNER JOIN camlis_patient_sample AS psample
                    ON patient.pid = psample.patient_id
                    AND psample.\"labID\" = patient.lab_id
                    AND psample.status = 1
                    AND psample.received_date BETWEEN ? AND ?				
                    $extraWhere
                RIGHT JOIN provinces AS province ON patient.province = province.code
                WHERE province.code != 25
                GROUP BY province.code";
        return $this->db->query($sql, [$start_date, $end_date])->result_array();
    }

    public function patient_by_district($start_date, $end_date, $laboratory_id = NULL) {
        $extraWhere = $laboratory_id > 0 ? "AND psample.\"labID\" = ".$laboratory_id : "";
        $sql = "SELECT province.code AS province_code,
                       province.name_kh AS province_name_kh,
                       province.name_en AS province_name_en,
                       district.code AS district_code,
                       district.name_kh AS district_name_kh,
                       district.name_en AS district_name_en,
                       COUNT(patient.pid) AS patient_count
                FROM v_camlis_all_patients AS patient
                INNER JOIN camlis_patient_sample AS psample
                    ON patient.pid = psample.patient_id
                    AND psample.\"labID\" = patient.lab_id
                    AND psample.status = TRUE
                    AND psample.received_date BETWEEN ? AND ?		
                    $extraWhere		
                RIGHT JOIN districts AS district ON patient.district = district.code
                INNER JOIN provinces AS province ON district.province_code = province.code
                WHERE province.code != 25
                GROUP BY district.code			
                ORDER BY province.code";
        return $this->db->query($sql, [$start_date, $end_date])->result_array();
    }

    public function patient_by_commune($start_date, $end_date, $laboratory_id = NULL) {
        $extraWhere = $laboratory_id > 0 ? "AND psample.\"labID\" = ".$laboratory_id : "";
        $sql = "SELECT province.code AS province_code,
                       province.name_kh AS province_name_kh,
                       province.name_en AS province_name_en,
                       district.code AS district_code,
                       district.name_kh AS district_name_kh,
                       district.name_en AS district_name_en,
                       commune.code AS commune_code,
                       commune.name_kh AS commune_name_kh,
                       commune.name_en AS commune_name_en,
                       COUNT(patient.pid) AS patient_count
                FROM v_camlis_all_patients AS patient
                INNER JOIN camlis_patient_sample AS psample
                    ON patient.pid = psample.patient_id
                    AND psample.\"labID\" = patient.lab_id
                    AND psample.status = TRUE
                    AND psample.received_date BETWEEN ? AND ?
                    $extraWhere
                RIGHT JOIN communes AS commune ON patient.commune = commune.code			
                INNER JOIN districts AS district ON commune.district_code = district.code
                INNER JOIN provinces AS province ON district.province_code = province.code
                WHERE province.code != 25
                GROUP BY commune.code			
                ORDER BY province.code";
        return $this->db->query($sql, [$start_date, $end_date])->result_array();
    }

    public function patient_by_village($start_date, $end_date, $laboratory_id = NULL) {
        $extraWhere = $laboratory_id > 0 ? "AND psample.\"labID\" = ".$laboratory_id : "";
        $sql = "SELECT province.code AS province_code,
                       province.name_kh AS province_name_kh,
                       province.name_en AS province_name_en,
                       district.code AS district_code,
                       district.name_kh AS district_name_kh,
                       district.name_en AS district_name_en,
                       commune.code AS commune_code,
                       commune.name_kh AS commune_name_kh,
                       commune.name_en AS commune_name_en,
                       village.code AS village_code,
                       village.name_kh AS village_name_kh,
                       village.name_en AS village_name_en,
                       COUNT(patient.pid) AS patient_count
                FROM v_camlis_all_patients AS patient
                INNER JOIN camlis_patient_sample AS psample
                    ON patient.pid = psample.patient_id
                    AND psample.\"labID\" = patient.lab_id
                    AND psample.status = TRUE
                    AND psample.received_date BETWEEN ? AND ?
                    $extraWhere
                RIGHT JOIN villages AS village ON patient.village = village.code
                INNER JOIN communes AS commune ON village.commune_code = commune.code			
                INNER JOIN districts AS district ON commune.district_code = district.code
                INNER JOIN provinces AS province ON district.province_code = province.code
                WHERE province.code != 25
                GROUP BY village.code
                ORDER BY province.code";
        return $this->db->query($sql, [$start_date, $end_date])->result_array();
    }

    public function test_by_province($start_date, $end_date, $department_sample, $sample_test_id, $possible_result_id = NULL, $laboratory_id = NULL) {
        $lab_condition = count($laboratory_id) > 0 ? "AND psample.\"labID\" IN (".implode(',', $laboratory_id).")" : "";
        $department_sample_condition = count($department_sample) > 0 ? "AND sample_test.department_sample_id IN (".implode(',', $department_sample).")" : "";
        $sample_test_condition = count($sample_test_id) > 0 ? "AND ptest.sample_test_id IN (".implode(',', $sample_test_id).")" : "";
        $joinResultQuery  = "";
        if ($possible_result_id > 0) {
            $joinResultQuery = "INNER JOIN camlis_ptest_result AS presult
                                ON ptest.\"ID\" = presult.patient_test_id
                                AND presult.status = 1
                                AND presult.type = 1";
            if (count($possible_result_id) > 0) {
                //$joinResultQuery .= " AND presult.result IN (".implode(',', $possible_result_id).")";
                $joinResultQuery .= " AND presult.result::int IN (".implode(',', $possible_result_id).")";
                
            }
        }

        $sql = "SELECT _t.province_code,
                       _t.province_name_kh,
                       _t.province_name_en,
                       COUNT(_t.sample_test_id) AS sample_test_count
                FROM (
                    SELECT province.code AS province_code,
                           province.name_kh AS province_name_kh,
                           province.name_en AS province_name_en,
                           ptest.sample_test_id
                    FROM camlis_patient_sample AS psample
                    INNER JOIN v_camlis_all_patients AS patient ON psample.patient_id = patient.pid AND psample.\"labID\" = patient.lab_id
                    INNER JOIN camlis_patient_sample_tests AS ptest
                        ON psample.\"ID\" = ptest.patient_sample_id
                        AND psample.status = 1
                        AND psample.received_date BETWEEN ? AND ?
                        $sample_test_condition
                        $lab_condition
                    INNER JOIN camlis_std_sample_test AS sample_test ON ptest.sample_test_id = sample_test.\"ID\" $department_sample_condition
                    $joinResultQuery
                    RIGHT JOIN provinces AS province ON patient.province = province.code
                    WHERE province.code != 25
                    GROUP BY psample.\"ID\", province.code, ptest.sample_test_id
                ) _t
                GROUP BY _t.province_code , _t.province_name_kh, _t.province_name_en";
        return $this->db->query($sql, [$start_date, $end_date])->result_array();
    }

    public function test_by_district($start_date, $end_date, $test_id, $laboratory_id = NULL) {
        $extraWhere = $laboratory_id > 0 ? "AND psample.\"labID\" = ".$laboratory_id : "";
        $sql = "SELECT province.code AS province_code,
                       province.name_kh AS province_name_kh,
                       province.name_en AS province_name_en,
                       district.code AS district_code,
                       district.name_kh AS district_name_kh,
                       district.name_en AS district_name_en,
                       COUNT(sample_test.test_id) AS test_count
                FROM camlis_patient_sample AS psample
                INNER JOIN camlis_patient_sample_tests AS ptest
                    ON psample.\"ID\" = ptest.patient_sample_id
                    AND psample.status = TRUE
                    AND psample.received_date BETWEEN ? AND ?
                    $extraWhere
                INNER JOIN camlis_std_sample_test AS sample_test 
                    ON ptest.sample_test_id = sample_test.\"ID\"
                    AND sample_test.test_id = ?
                INNER JOIN v_camlis_all_patients AS patient ON psample.patient_id = patient.pid AND psample.\"labID\" = patient.lab_id
                RIGHT JOIN districts AS district ON patient.district = district.code
                INNER JOIN provinces AS province ON district.province_code = province.code
                WHERE province.code != 25
                GROUP BY district.code			
                ORDER BY province.code";
        return $this->db->query($sql, [$start_date, $end_date, $test_id])->result_array();
    }

    public function test_by_commune($start_date, $end_date, $test_id, $laboratory_id = NULL) {
        $extraWhere = $laboratory_id > 0 ? "AND psample.\"labID\" = ".$laboratory_id : "";
        $sql = "SELECT province.code AS province_code,
                       province.name_kh AS province_name_kh,
                       province.name_en AS province_name_en,
                       district.code AS district_code,
                       district.name_kh AS district_name_kh,
                       district.name_en AS district_name_en,
                       commune.code AS commune_code,
                       commune.name_kh AS commune_name_kh,
                       commune.name_en AS commune_name_en,
                       COUNT(sample_test.test_id) AS test_count
                FROM camlis_patient_sample AS psample
                INNER JOIN camlis_patient_sample_tests AS ptest
                    ON psample.\"ID\" = ptest.patient_sample_id
                    AND psample.status = TRUE
                    AND psample.received_date BETWEEN ? AND ?
                    $extraWhere
                INNER JOIN camlis_std_sample_test AS sample_test 
                    ON ptest.sample_test_id = sample_test.\"ID\"
                    AND sample_test.test_id = ?
                INNER JOIN v_camlis_all_patients AS patient ON psample.patient_id = patient.pid AND psample.\"labID\" = patient.lab_id
                RIGHT JOIN communes AS commune ON patient.commune = commune.code			
                INNER JOIN districts AS district ON commune.district_code = district.code
                INNER JOIN provinces AS province ON district.province_code = province.code
                WHERE province.code != 25
                GROUP BY commune.code			
                ORDER BY province.code";
        return $this->db->query($sql, [$start_date, $end_date, $test_id])->result_array();
    }

    public function test_by_village($start_date, $end_date, $test_id, $laboratory_id = NULL) {
        $extraWhere = $laboratory_id > 0 ? "AND psample.\"labID\" = ".$laboratory_id : "";
        $sql = "SELECT province.code AS province_code,
                       province.name_kh AS province_name_kh,
                       province.name_en AS province_name_en,
                       district.code AS district_code,
                       district.name_kh AS district_name_kh,
                       district.name_en AS district_name_en,
                       commune.code AS commune_code,
                       commune.name_kh AS commune_name_kh,
                       commune.name_en AS commune_name_en,
                       village.code AS village_code,
                       village.name_kh AS village_name_kh,
                       village.name_en AS village_name_en,
                       COUNT(sample_test.test_id) AS test_count
                FROM camlis_patient_sample AS psample
                INNER JOIN camlis_patient_sample_tests AS ptest
                    ON psample.\"ID\" = ptest.patient_sample_id
                    AND psample.status = TRUE
                    AND psample.received_date BETWEEN ? AND ?
                    $extraWhere
                INNER JOIN camlis_std_sample_test AS sample_test 
                    ON ptest.sample_test_id = sample_test.\"ID\"
                    AND sample_test.test_id = ?
                INNER JOIN v_camlis_all_patients AS patient ON psample.patient_id = patient.pid AND psample.\"labID\" = patient.lab_id
                RIGHT JOIN villages AS village ON patient.village = village.code
                INNER JOIN communes AS commune ON village.commune_code = commune.code			
                INNER JOIN districts AS district ON commune.district_code = district.code
                INNER JOIN provinces AS province ON district.province_code = province.code
                WHERE province.code != 25
                GROUP BY village.code
                ORDER BY province.code";
        return $this->db->query($sql, [$start_date, $end_date, $test_id])->result_array();
    }

    public function test_by_laboratory($start_date, $end_date, $department_sample, $sample_test_id, $possible_result_id = NULL, $laboratory_id = NULL) {
        $lab_condition = count($laboratory_id) > 0 ? "AND psample.\"labID\" IN (".implode(',', $laboratory_id).")" : "";
        $department_sample_condition = count($department_sample) > 0 ? "AND sample_test.department_sample_id IN (".implode(',', $department_sample).")" : "";
        $sample_test_condition = count($sample_test_id) > 0 ? "AND ptest.sample_test_id IN (".implode(',', $sample_test_id).")" : "";
        $joinResultQuery  = "";
        if ($possible_result_id > 0) {
            $joinResultQuery = "INNER JOIN camlis_ptest_result AS presult
                                ON ptest.\"ID\" = presult.patient_test_id
                                AND presult.status = 1
                                AND presult.type = 1";
            if (count($possible_result_id) > 0) $joinResultQuery .= " AND presult.result::int IN (".implode(',', $possible_result_id).")";
        }

        $sql = "SELECT _t.lab_id,
                       _t.lab_code,
                       _t.name_kh,
                       _t.name_en,
                       COUNT(_t.sample_test_id) AS sample_test_count
                FROM (
                    SELECT lab.\"labID\" AS lab_id,
                           lab.lab_code,
                           lab.name_kh,
                           lab.name_en,
                           ptest.sample_test_id
                    FROM camlis_patient_sample AS psample
                    INNER JOIN camlis_patient_sample_tests AS ptest
                        ON psample.\"ID\" = ptest.patient_sample_id
                        AND psample.status = 1
                        AND psample.received_date BETWEEN ? AND ?
                        $sample_test_condition
                    INNER JOIN camlis_std_sample_test AS sample_test ON ptest.sample_test_id = sample_test.\"ID\" $department_sample_condition
                    $joinResultQuery
                    RIGHT JOIN camlis_laboratory AS lab ON psample.\"labID\" = lab.\"labID\"
                    WHERE lab.status = TRUE $lab_condition
                    GROUP BY psample.\"ID\", lab.\"labID\", ptest.sample_test_id
                ) _t
                GROUP BY _t.lab_id, _t.lab_code, _t.name_kh,  _t.name_en";
        return $this->db->query($sql, [$start_date, $end_date])->result_array();
    }

    /**
     * Get financial report
     * @param $start_date
     * @param $end_date
     * @return mixed
     */
    public function get_financial_report($start_date, $end_date) {
        $sql = "SELECT department.\"ID\" AS department_id,
                       department.department_name,
                       psample.payment_type_id,
                       payment_type.name AS payment_type_name,
                       ptest_payment.group_result,
                       ptest_payment.price,
                       ptest_payment.patient_sample_id
                FROM camlis_patient_sample AS psample
                INNER JOIN camlis_patient_sample_test_payment AS ptest_payment ON psample.\"ID\" = ptest_payment.patient_sample_id
                INNER JOIN camlis_std_payment_type AS payment_type ON psample.payment_type_id = payment_type.id
                INNER JOIN camlis_patient_sample_tests AS ptest ON psample.\"ID\" = ptest.patient_sample_id
                INNER JOIN camlis_std_sample_test AS sample_test ON ptest.sample_test_id = sample_test.\"ID\" AND ptest_payment.group_result = sample_test.group_result
                INNER JOIN camlis_std_department_sample AS department_sample ON sample_test.department_sample_id = department_sample.\"ID\"
                INNER JOIN camlis_std_department AS department ON department_sample.department_id = department.\"ID\"
                WHERE psample.status = 1
                      AND ptest.status = 1
                      AND psample.\"labID\" = ?
                      AND CONCAT(psample.received_date, ' ', psample.received_time) BETWEEN ? AND ?
                GROUP BY department.\"ID\", psample.\"ID\", ptest_payment.group_result, payment_type.name, ptest_payment.price, ptest_payment.patient_sample_id
                /**GROUP BY psample.ID, ptest_payment.group_result */
                ORDER BY department.order";
        
        return $this->db->query($sql, [$this->laboratory_id, $start_date.":00", $end_date.":00"])->result_array();
    }
    /**
    * group result
    * @return result
    */
    public function group_result()
    {
    	$this->db->select('sample_test."ID", sample_test.group_result, department.department_name');
    	$this->db->from('camlis_std_sample_test sample_test');
    	$this->db->join('camlis_std_department_sample department_sample', 'department_sample."ID" = sample_test.department_sample_id');
    	$this->db->join('camlis_std_department department', 'department."ID" = department_sample.department_id');
    	$this->db->where('sample_test.group_result IS NOT NULL', null, false);
    	$this->db->order_by('department.order', 'asc');
    	$this->db->order_by('sample_test.order', 'asc');
    	return $this->db->get()->result();
    }

    public function __get_patient_by_culture($obj) {
        $labid=$this->session->userdata('laboratory')->labID;
        $tblallpatients="
                (SELECT cast(pmrs_patient.pid as bigint) AS pid,
                        psample.\"labID\" AS lab_id,
                        pmrs_patient.pid AS patient_id,
                        pmrs_patient.pid AS patient_code,
                        pmrs_patient.name AS patient_name,
                        (CASE pmrs_patient.sex WHEN 'M' THEN 1 WHEN 'F' THEN 2 ELSE 0 END) AS sex,
                        pmrs_patient.dob AS dob,
                        pmrs_patient.phone AS phone,
                        pmrs_patient.province AS province,
                        pmrs_patient.district AS district,
                        pmrs_patient.commune AS commune,
                        pmrs_patient.village AS village 
                        FROM 
                            (camlis_patient_sample psample
                            JOIN camlis_pmrs_patient pmrs_patient ON(((psample.patient_id = pmrs_patient.pid) AND (pmrs_patient.status = 1)))
                            )
                        WHERE (psample.status = 1 and psample.\"labID\"=".$labid.")
                        GROUP BY psample.patient_id, pmrs_patient.pid, psample.\"labID\" UNION ALL
                        SELECT 
                            outside_patient.pid AS pid,
                            outside_patient.lab_id AS lab_id,
                            outside_patient.patient_id AS patient_id,
                            outside_patient.patient_code AS patient_code,
                            outside_patient.patient_name AS patient_name,
                            cast(outside_patient.sex as varchar(10)) AS sex,
                            outside_patient.dob AS dob,
                            outside_patient.phone AS phone,
                            outside_patient.province AS province,
                            outside_patient.district AS district,
                            outside_patient.commune AS commune,
                            outside_patient.village AS village
                        FROM camlis_outside_patient outside_patient
                        WHERE (outside_patient.status = 1 and outside_patient.lab_id=".$labid.")
                )";
        $sql="
            SELECT _t.title,_t.sampletype,
                SUM(IF(_t.sex = 1 and _t.age_group='1', 1, 0)) AS '1M',
                SUM(IF(_t.sex = 2 and _t.age_group='1', 1, 0)) AS '1F',
                SUM(IF(_t.sex = 1 and _t.age_group='2', 1, 0)) AS '2M',
                SUM(IF(_t.sex = 2 and _t.age_group='2', 1, 0)) AS '2F',
                SUM(IF(_t.sex = 1 and _t.age_group='3', 1, 0)) AS '3M',
                SUM(IF(_t.sex = 2 and _t.age_group='3', 1, 0)) AS '3F',
                SUM(IF(_t.sex = 1 and _t.age_group='4', 1, 0)) AS '4M',
                SUM(IF(_t.sex = 2 and _t.age_group='4', 1, 0)) AS '4F',
                SUM(IF(_t.sex = 1 and _t.age_group='5', 1, 0)) AS '5M',
                SUM(IF(_t.sex = 2 and _t.age_group='5', 1, 0)) AS '5F',
                SUM(IF(_t.sex = 1 and _t.age_group='6', 1, 0)) AS '6M',
                SUM(IF(_t.sex = 2 and _t.age_group='6', 1, 0)) AS '6F',
                SUM(IF(_t.sex = 1 and _t.age_group='7', 1, 0)) AS '7M',
                SUM(IF(_t.sex = 2 and _t.age_group='7', 1, 0)) AS '7F',
                SUM(IF(_t.sex = 1 and _t.age_group='8', 1, 0)) AS '8M',
                SUM(IF(_t.sex = 2 and _t.age_group='8', 1, 0)) AS '8F',
                COUNT(_t.sampletype) AS 'total'
                from (select distinct '1.TOTAL SAMPLE TESTED' as title,st.group_result as sampletype,patient.sex,
                (CASE
                    WHEN TIMESTAMPDIFF(DAY, patient.dob, ps.collected_date) <= 29 THEN '1'
                    WHEN TIMESTAMPDIFF(MONTH, patient.dob, ps.collected_date) BETWEEN 1 AND 11 THEN '2'
                    WHEN TIMESTAMPDIFF(YEAR, patient.dob, ps.collected_date) BETWEEN 1 AND 4 THEN '3'
                    WHEN TIMESTAMPDIFF(YEAR, patient.dob, ps.collected_date) BETWEEN 5 AND 14 THEN '4'
                    WHEN TIMESTAMPDIFF(YEAR, patient.dob, ps.collected_date) BETWEEN 15 AND 24 THEN '5'
                    WHEN TIMESTAMPDIFF(YEAR, patient.dob, ps.collected_date) BETWEEN 25 AND 49 THEN '6'
                    WHEN TIMESTAMPDIFF(YEAR, patient.dob, ps.collected_date) BETWEEN 50 AND 64 THEN '7'
                    ELSE '8'	
                END) AS age_group,ps.sample_number,ps.labID,ps.received_date,ps.received_time 
                from $tblallpatients as patient
                inner join camlis_patient_sample ps on patient.pid=ps.patient_id 
                inner join camlis_patient_sample_tests pst on ps.ID=pst.patient_sample_id
                inner join camlis_std_sample_test st on pst.sample_test_id=st.ID
                where ps.status=1 and st.test_id=170
    
                union all 
    
                select distinct '2.Number of positive sample' as title,concat(st.group_result,'_positive') as sampletype,patient.sex,
                (CASE
                    WHEN TIMESTAMPDIFF(DAY, patient.dob, ps.collected_date) <= 29 THEN '1'
                    WHEN TIMESTAMPDIFF(MONTH, patient.dob, ps.collected_date) BETWEEN 1 AND 11 THEN '2'
                    WHEN TIMESTAMPDIFF(YEAR, patient.dob, ps.collected_date) BETWEEN 1 AND 4 THEN '3'
                    WHEN TIMESTAMPDIFF(YEAR, patient.dob, ps.collected_date) BETWEEN 5 AND 14 THEN '4'
                    WHEN TIMESTAMPDIFF(YEAR, patient.dob, ps.collected_date) BETWEEN 15 AND 24 THEN '5'
                    WHEN TIMESTAMPDIFF(YEAR, patient.dob, ps.collected_date) BETWEEN 25 AND 49 THEN '6'
                    WHEN TIMESTAMPDIFF(YEAR, patient.dob, ps.collected_date) BETWEEN 50 AND 64 THEN '7'
                    ELSE '8'	
                END) AS age_group,ps.sample_number,ps.labID,ps.received_date,ps.received_time 
                from $tblallpatients as patient
                inner join camlis_patient_sample ps on patient.pid=ps.patient_id 
                inner join camlis_patient_sample_tests pst on ps.ID=pst.patient_sample_id
                inner join camlis_std_sample_test st on pst.sample_test_id=st.id
                inner join camlis_ptest_result tr on tr.patient_sample_id=ps.ID
                inner join camlis_ptest_result_antibiotic ran on ran.presult_id = tr.ID
                where ps.status=1  and tr.status = 1 and st.test_id=170) _t
            where _t.labID = ".$labid." 
            and date_format(_t.received_date,'%Y-%m-%d') >= ?
            and date_format(_t.received_date,'%Y-%m-%d') <= ?
            and _t.received_time BETWEEN ? AND ? 
            group by _t.title,_t.sampletype
            order by _t.title,_t.sampletype";
            
        return $this->db->query($sql,array($obj->start,$obj->end, $obj->start_time, $obj->end_time))->result_array();
    }

    /*
     * Added 23 Dec 2020
     * 
     */
    
    function covid_table($obj , $for_research = null , $lab_id = null , $extraGroupBy = null){
        //if(!empty($obj->lab_id)){
        if($lab_id !== null){
            //$labID          = implode(',',$obj->lab_id);
            $labID = $lab_id;
        } else{
            $labID          = $this->session->userdata('laboratory')->labID;
        }
        if($extraGroupBy !== null){
            $extraGroupBy = "order by psample.for_research ASC , psample.sample_number ASC";
        }else $extraGroupBy = "order by psample.sample_number ASC ";
        
       $sampleSourceWhere   = "";
       $collectedDateWhere  = "";
       $testDateWhere       = "";
       $sampleNumberWhere   = "";
       $testResultWhere     = "";
       $numberSampleWhere   = "";
       $reasonForTestingWhere = "";
       $whereClause = "";
       // Collected_date Where clause
       if(!empty( $obj->start ) || !empty( $obj->end ) ){ 
           $startDateTime   = $obj->start.' '.$obj->start_time.":00";
           $endDateTime     = $obj->end.' '.$obj->end_time.":00";
           $collectedDateWhere  = " AND concat( psample.received_date,' ',psample.received_time) >= '".$startDateTime."'";
           $collectedDateWhere .= " AND concat( psample.received_date,' ',psample.received_time) <= '".$endDateTime."'";
       }
       if(!empty($obj->test_start) || !empty($obj->test_end)){
            $test_date_start = $obj->test_start;
            $test_date_end = $obj->test_end;
            $testDateWhere = " ptest_result.test_date BETWEEN '".$test_date_start."' AND '".$test_date_end."'";
       }
       if(!empty($obj->start_sn) || !empty($obj->end_sn)){
            $start_sn   = $obj->start_sn;
            $end_sn     = $obj->end_sn;
            $sampleNumberWhere   = " AND psample.sample_number BETWEEN '".$start_sn."' AND '".$end_sn."'";
       }
       if($for_research !== null){
            if($for_research !== 'all'){
                $reasonForTestingWhere = " AND psample.for_research =".$for_research;
           }
       }
       
       //added 27-04-2021
       if(!empty($obj->sample_source)){
        $sampleSourceWhere = " AND sample_source.\"ID\" in (".$obj->sample_source.")";
       }
       if(!empty($obj->test_name)){
            $testWhere = " ( ".$obj->test_name.") ";
       }else{
            $testWhere = "( test.\"ID\" = 419 OR test.\"ID\" = 438 OR test.\"ID\" = 446 OR test.\"ID\" = 447 OR test.\"ID\" = 456)";
       }

       // end
       // 10-05-2021
       if(!empty($obj->test_result)){
           if($obj->test_result !== 0){
               if($testDateWhere !== ''){
                   $expressionEnd = " AND ";
               }else{
                $expressionEnd = " ";
               }
                if($obj->test_result == 1){
                    $testResultWhere = $expressionEnd." (organism.organism_name = 'Positive' OR organism.organism_name = 'Reaction Positive') ";
                }else if ($obj->test_result == 2){
                    $testResultWhere = $expressionEnd." (organism.organism_name = 'Negative' OR organism.organism_name = 'Reaction Negative') ";
                }else{
                    $testResultWhere = $expressionEnd." (organism.organism_name = 'Invalid') ";
                }
           }
       }
       //End
       if(!empty($obj->number_of_sample)){
            $numberSampleWhere = " AND (psample.number_of_sample = ".$obj->number_of_sample.") ";
       }
       if($testDateWhere !== "" || $testResultWhere !== ""){
            $whereClause .= " WHERE ";
       }
        $sql = "WITH psample AS (
            SELECT * from camlis_patient_sample as psample
            WHERE  psample.status = 1  
                AND psample.\"labID\" IN (".$labID.")
                ".$collectedDateWhere."
                ".$sampleNumberWhere."
                ".$numberSampleWhere."
                ".$reasonForTestingWhere."
            order by psample.\"ID\"
        ), lab AS (
            SELECT * FROM camlis_laboratory
            WHERE \"labID\" in (".$labID.")
            ORDER BY \"labID\"
        ), sample_source AS (
            SELECT * FROM camlis_lab_sample_source AS sample_source
            WHERE lab_id IN (".$labID.") AND status = 'TRUE' ".$sampleSourceWhere."
        ), patient AS (
            SELECT * FROM v_patients WHERE lab_id IN (".$labID.") ORDER BY pid
        ), test as (
            SELECT * FROM camlis_std_test AS test
            WHERE ".$testWhere."
        ) SELECT psample.\"labID\", psample.sample_number, 
        to_char(psample.collected_date, 'DD/MM/YYYY') AS collected_date, 
        to_char(psample.received_date, 'DD/MM/YYYY') AS received_date,
        (CASE psample.for_research
                WHEN 0 THEN ''
                WHEN 1 THEN 'Suspect' 
                WHEN 2 THEN 'Pneumonia'
                WHEN 3 THEN 'Follow Up'
                WHEN 4 THEN 'Contact'
                WHEN 5 THEN 'HCW'
                WHEN 6 THEN 'Other'
                WHEN 7 THEN 'Migrants'
                WHEN 8 THEN 'Passenger'
                WHEN 9 THEN 'Certificate'
                WHEN 10 THEN 'Followup  Positive Cases'
                WHEN 11 THEN 'F20 Event'
                WHEN 12 THEN 'Death'
                ELSE NULL END) as reason_for_testing,
        psample.clinical_history as diagnosis, 
        psample.for_research,
        
        (CASE psample.number_of_sample WHEN 0 THEN NULL ELSE psample.number_of_sample END) AS number_of_sample,
        lab.name_en AS laboratory_name, 
        sample_source.source_name AS sample_source, 
        requester.requester_name AS requester,
        patient.pid,
        patient.patient_code, 
        patient.patient_name, 
        '' as nationality,
        '' as passport_number,
        '' as flight_number,
        '' as date_arrival,
        DATE_PART('year', age(now(), patient.dob))AS patient_age, 
        DATE_PART('month', age(now(), patient.dob))AS age_month, 
        DATE_PART('day', age(now(), patient.dob))AS age_day, 
        (CASE patient.sex WHEN 1 THEN 'M' WHEN 2 THEN 'F' ELSE '' END) AS patient_gender, 
        std_sampletest.group_result AS test, 
        department.department_name AS department, 
        sample_type.sample_name AS sample_type,
        to_char(ptest_result.test_date, 'DD/MM/YYYY') AS test_date, 
        (CASE WHEN ptest_result.type = 1 THEN CONCAT(organism.organism_name, CASE organism.organism_value WHEN 1 THEN ' Positive' WHEN 2 THEN ' Negative' ELSE '' END) ELSE NULL END) AS result_organism
        FROM psample
        INNER JOIN lab ON psample.\"labID\" = lab.\"labID\"
        INNER JOIN sample_source ON psample.sample_source_id = sample_source.\"ID\" AND psample.\"labID\" = sample_source.lab_id AND sample_source.status = 'TRUE'
        INNER JOIN camlis_lab_requester AS requester ON psample.requester_id = requester.\"ID\" AND psample.\"labID\" = requester.lab_id
        
        INNER JOIN patient ON psample.patient_id = patient.pid AND psample.\"labID\" = patient.lab_id
        LEFT JOIN camlis_patient_sample_tests AS psample_test ON psample.\"ID\" = psample_test.patient_sample_id AND psample_test.status = 1
        INNER JOIN camlis_std_sample_test AS std_sampletest ON psample_test.sample_test_id = std_sampletest.\"ID\"
        INNER JOIN camlis_std_department_sample AS department_sample ON std_sampletest.department_sample_id = department_sample.\"ID\" AND department_sample.status = 'TRUE'
        INNER JOIN test ON std_sampletest.test_id = test.\"ID\"
        INNER JOIN camlis_std_department AS department ON department_sample.department_id = department.\"ID\" AND department.status = 'TRUE'
        INNER JOIN camlis_std_sample AS sample_type ON department_sample.sample_id = sample_type.\"ID\" AND sample_type.status = 'TRUE'
        
        LEFT JOIN camlis_ptest_result AS ptest_result ON psample_test.\"ID\" = ptest_result.patient_test_id AND ptest_result.patient_sample_id = psample.\"ID\" AND ptest_result.status = 1
        LEFT JOIN camlis_std_test_organism AS test_organism ON ptest_result.type = 1 AND ptest_result.result = cast(test_organism.\"ID\" as varchar)
        LEFT JOIN camlis_std_organism AS organism ON test_organism.organism_id = organism.\"ID\"
        ".$whereClause."
        ".$testDateWhere."
        ".$testResultWhere."
        GROUP BY patient.pid,
            std_sampletest.group_result, 
            sample_type.\"ID\", department.\"ID\", 
            sample_source.source_name, 
            psample.number_of_sample,
            department.department_name,    
            
            requester.requester_name, 
            organism.organism_name, 
            organism.organism_value, 
            psample.clinical_history, 
            ptest_result.test_date, 
            ptest_result.type, 
            std_sampletest.group_result, 
            psample.\"labID\", 
            psample.sample_number, 
            lab.name_en, 
            collected_date, 
            received_date, 
            for_research, 
            patient.patient_code, 
            patient.patient_name, 
            patient.dob, patient.sex
        ".$extraGroupBy."
        ";        
        return $this->db->query($sql)->result_array();
    }
    public function culture_result($start_date, $end_date)
    {
    	$this->db->select("to_char(patient_sample.received_date, 'YYYY') AS years")
    	//->select("DATE_FORMAT(patient_sample.received_date, '%b') AS months")
        ->select("to_char(patient_sample.received_date, 'Mon') AS months")
        // AS test_date
    	->select('sample_detail.sample_volume1')
    	->select('sample_detail.sample_volume2')
    	->from('camlis_patient_sample_tests tests')
    	->join('camlis_patient_sample patient_sample', 'patient_sample."ID" = tests.patient_sample_id')
    	->join('camlis_patient_sample_detail sample_detail', 'sample_detail.patient_sample_id = patient_sample."ID"')
    	->join('camlis_std_sample_test sample_test', 'sample_test."ID" = tests.sample_test_id')
    	->join('camlis_std_test std_test', 'std_test."ID" = sample_test.test_id')
    	->join('camlis_std_department_sample department_sample', 'department_sample."ID" = sample_test.department_sample_id')
    	->join('camlis_std_sample std_sample', 'std_sample."ID" = department_sample.sample_id AND std_sample."ID" = 6')
    	->join('camlis_std_test_organism test_organism', 'test_organism.sample_test_id = std_test."ID"')
    	->join('camlis_std_organism organism', 'organism."ID" = test_organism.organism_id')
    	->where('patient_sample."labID"', $this->laboratory_id)
    	->where('std_test."ID"', 170)
    	->where('patient_sample.received_date >=', $start_date)
    	->where('patient_sample.received_date <=', $end_date);
    	return $this->db->get()->result();
    }
    /**
     * Generate Data for GoData
     */
    function create_temp_patients_godata($laboratory){
        $sql = "CREATE TEMPORARY TABLE temp_all_patients_godata as (
            SELECT pmrs_patient.pid,
                 psample.\"labID\" AS lab_id,
                 pmrs_patient.pid AS patient_id,
                 pmrs_patient.pid AS patient_code,
                 pmrs_patient.name AS patient_name,
                     CASE pmrs_patient.sex
                         WHEN 'M'::bpchar THEN 1
                         WHEN 'F'::bpchar THEN 2
                         ELSE 0
                     END AS sex,
                 pmrs_patient.dob,
                 pmrs_patient.phone,
                 pmrs_patient.province,
                 pmrs_patient.district,
                 pmrs_patient.commune,
                 pmrs_patient.village,
                 ''::character varying AS nationality_en,
                 ''::character varying AS passport_number,
                 ''::character varying AS flight_number,
                 NULL::date AS date_arrival,
                 NULL::boolean AS is_positive_covid,
                ''::character varying AS contact_with,
                NULL::boolean AS is_direct_contact,
                ''::character varying AS occupation,
                ''::character varying AS residence,
                ''::character varying AS country_name,
                prov.name_en AS province_name,
                com.name_en AS commune_name,
                dis.name_en AS district_name,
                vil.name_en AS village_name,
                NULL::integer AS vaccination_status,
                NULL::date AS first_vaccinated_date,
                NULL::date AS second_vaccinated_date,
                NULL::date AS third_vaccinated_date,
                ''::character varying AS first_vaccine_name,
                ''::character varying AS second_vaccine_name,
                ''::character varying AS seat_number,
                NULL::integer AS vaccine_id,
                NULL::bigint AS second_vaccine_id,
                NULL::boolean AS is_contacted,
                ''::character varying AS nationality,
                NULL::date AS forth_vaccinated_date,
                ''::character varying AS third_vaccine_name

             FROM camlis_patient_sample psample
             JOIN camlis_pmrs_patient pmrs_patient ON psample.patient_id::text = pmrs_patient.pid::text AND pmrs_patient.status = 1
             JOIN provinces prov ON prov.code = pmrs_patient.province
             JOIN communes com ON com.code = pmrs_patient.commune
             JOIN districts dis ON dis.code = pmrs_patient.district
             JOIN villages vil ON vil.code = pmrs_patient.village
             WHERE psample.status = 1 and psample.\"labID\" = ANY(VALUES".$laboratory.")
             GROUP BY pmrs_patient.pid, psample.patient_id, psample.\"labID\", prov.name_en, com.name_en, dis.name_en, vil.name_en
             UNION
             SELECT outside_patient.pid::text AS pid,
                 outside_patient.lab_id,
                 outside_patient.patient_id,
                 outside_patient.patient_code,
                 outside_patient.patient_name,
                 outside_patient.sex,
                 outside_patient.dob,
                 outside_patient.phone,
                 outside_patient.province,
                 outside_patient.district,
                 outside_patient.commune,
                 outside_patient.village,
                 countries.nationality_en,
                 outside_patient.passport_number,
                 outside_patient.flight_number,
                 outside_patient.date_arrival,
                 outside_patient.is_positive_covid,
                outside_patient.contact_with,
                outside_patient.is_direct_contact,
                outside_patient.occupation,
                outside_patient.residence,
                outside_patient.country_name,
                prov.name_en AS province_name,
                com.name_en AS commune_name,
                dis.name_en AS district_name,
                vil.name_en AS village_name,
                outside_patient.vaccination_status,
                outside_patient.first_vaccinated_date,
                outside_patient.second_vaccinated_date,
                outside_patient.third_vaccinated_date,
                vaccine1.name AS first_vaccine_name,
                vaccine2.name AS second_vaccine_name,                
                outside_patient.seat_number,
                outside_patient.vaccine_id,
                outside_patient.second_vaccine_id,
                outside_patient.is_contacted,
                countries.nationality_en AS nationality,
                outside_patient.forth_vaccinated_date,
                vaccine3.name AS third_vaccine_name
             FROM camlis_outside_patient outside_patient
             JOIN provinces prov ON prov.code = outside_patient.province
            JOIN communes com ON com.code = outside_patient.commune
            JOIN districts dis ON dis.code = outside_patient.district
            JOIN villages vil ON vil.code = outside_patient.village
            LEFT JOIN countries ON outside_patient.nationality = countries.num_code
            LEFT JOIN camlis_vaccine vaccine1 ON vaccine1.id = outside_patient.vaccine_id
            LEFT JOIN camlis_vaccine vaccine2 ON vaccine2.id = outside_patient.second_vaccine_id
             LEFT JOIN camlis_vaccine vaccine3 ON vaccine3.id = outside_patient.third_vaccine_id
             WHERE outside_patient.status = 1 AND outside_patient.lab_id = ANY(VALUES".$laboratory.")
        )";
        $this->db->query($sql);
    }
    function generate_covid_report_for_godata($labID, $obj){
        $collectedDateWhere  = "";
        // Collected_date Where clause
        if(!empty( $obj->start ) || !empty( $obj->end ) ){ 
            $startDateTime   = $obj->start.' '.$obj->start_time.":00";
            $endDateTime     = $obj->end.' '.$obj->end_time.":00";
            
            $collectedDateWhere  = " AND concat( psample.received_date,' ',psample.received_time) >= '".$startDateTime."'";
            $collectedDateWhere .= " AND concat( psample.received_date,' ',psample.received_time) <= '".$endDateTime."'";
        }
        $this->create_temp_patients_godata($labID);
         $sql = "WITH psample AS (
            SELECT * from camlis_patient_sample psample
            WHERE status =  1 
            AND \"labID\" = ANY(VALUES".$labID.")
            ".$collectedDateWhere."
            ORDER BY \"ID\"
        ), lab AS (
            SELECT * from camlis_laboratory
            WHERE \"labID\" = ANY(VALUES".$labID.")
        ), test AS (
            SELECT * from camlis_std_test
            WHERE \"ID\" = 419 OR \"ID\" = 438 OR \"ID\" = 447
        )  SELECT psample.health_facility, 
             psample.completed_by,
             psample.phone_number,
             psample.\"ID\" as sample_id,
             (CASE psample.for_research
                     WHEN 0 THEN ''
                     WHEN 1 THEN 'Suspect' 
                     WHEN 2 THEN 'Pneumonia'
                     WHEN 3 THEN 'Follow Up'
                     WHEN 4 THEN 'Contact'
                     WHEN 5 THEN 'HCW'
                     WHEN 6 THEN 'Other'
                     WHEN 7 THEN 'Migrants'
                     WHEN 8 THEN 'Passenger'
                     WHEN 9 THEN 'Certificate'
                     WHEN 10 THEN 'Followup  Positive Cases'
                     WHEN 11 THEN 'F20 Event'
                     WHEN 12 THEN 'Death'
                     ELSE NULL END) as reason_for_testing,
             (CASE patient.is_positive_covid WHEN true THEN 'Yes' ELSE 'No' END) as is_positive_covid,
             patient.contact_with,
             patient.is_direct_contact,
             patient.patient_name,
             patient.patient_code,
             psample.sample_number,
             (CASE patient.sex WHEN 1 THEN 'M' WHEN 2 THEN 'F' ELSE '' END) AS patient_gender, 
             DATE_PART('year', age(now(), patient.dob))AS patient_age,
             DATE_PART('month', age(now(), patient.dob))AS age_month,
             DATE_PART('day', age(now(), patient.dob))AS age_day,
             patient.nationality,
             patient.nationality_en,
             patient.occupation,
             patient.phone,
             patient.residence,
             patient.province_name,
             patient.commune_name,
             patient.district_name,
             patient.village_name,             
             array_to_json(array_agg(sym.name_en)) as symptoms,
             to_char(psample.admission_date, 'DD/Mon/YYYY') AS date_of_onset,
             (CASE patient.is_positive_covid
                 WHEN true THEN 'Yes'
                 WHEN false THEN 'No'
                 ELSE '' END) as history_of_covid19_positive,
             to_char(ptest_result.test_date, 'DD/Mon/YYYY') AS test_date,
             patient.country_name,
             to_char(patient.date_arrival, 'DD/Mon/YYYY') AS date_arrival,
             patient.passport_number,
             patient.seat_number,
             patient.flight_number,
             sample_source.source_name AS place_of_collection, 
             to_char(psample.collected_date, 'DD/Mon/YYYY') AS date_of_collection,
             lab.name_en AS laboratory_name,
             lab.lab_code,
             (CASE patient.vaccination_status
                     WHEN 1 THEN 'Not vaccinated'
                     WHEN 2 THEN '1 dose'
                     WHEN 3 THEN '2 doses'
                     WHEN 4 THEN '3 doses'
                     WHEN 5 THEN '4 doses'
                     ELSE 'Not vaccinated' END) as vaccination_status,
             CASE WHEN patient.vaccination_status > 1 THEN 'Yes' ELSE 'No' END vaccinated_one,
             CASE WHEN patient.vaccination_status > 2 THEN 'Yes' ELSE 'No' END vaccinated_two,
             CASE WHEN patient.vaccination_status > 3 THEN 'Yes' ELSE 'No' END vaccinated_three,
             CASE WHEN patient.vaccination_status > 4 THEN 'Yes' ELSE 'No' END vaccinated_four,
             to_char(patient.first_vaccinated_date, 'DD/Mon/YYYY') AS first_vaccinated_date,
             to_char(patient.second_vaccinated_date, 'DD/Mon/YYYY') AS second_vaccinated_date,
             to_char(patient.third_vaccinated_date, 'DD/Mon/YYYY') AS third_vaccinated_date,
             patient.first_vaccine_name,
             patient.second_vaccine_name,
             psample.number_of_sample,
             psample.sample_collector,
             psample.phone_number_sample_collector as phone_collector,
             to_char(psample.received_date, 'DD/Mon/YYYY') AS received_date,
             psample.clinical_history as diagnosis,
             psample.number_of_sample,
             sample_source.source_name AS sample_source, 
             requester.requester_name AS requester, 
             
             CASE WHEN test.\"ID\" = 438 THEN 'SARS-CoV-2 (Applied Biosystems 7500 Fast Real Time PCR)' ELSE test.test_name END test_name,
             std_sampletest.group_result AS test_result, 
             
             sample_type.sample_name AS sample_type, 
             to_char(ptest_result.test_date, 'DD/Mon/YYYY') AS test_result_date, 
             (CASE WHEN ptest_result.type = 1 THEN CONCAT(organism.organism_name, CASE organism.organism_value WHEN 1 THEN 'Positive' WHEN 2 THEN ' Negative' ELSE '' END) ELSE NULL END) AS result_organism,             
             to_char(patient.forth_vaccinated_date, 'DD/Mon/YYYY') AS forth_vaccinated_date,
             patient.third_vaccine_name

             FROM psample
             INNER JOIN camlis_laboratory AS lab ON psample.\"labID\" = lab.\"labID\"
             INNER JOIN camlis_lab_sample_source AS sample_source ON psample.sample_source_id = sample_source.\"ID\" AND psample.\"labID\" = sample_source.lab_id AND sample_source.status = 'TRUE'
             INNER JOIN camlis_lab_requester AS requester ON psample.requester_id = requester.\"ID\" AND psample.\"labID\" = requester.lab_id
             INNER JOIN temp_all_patients_godata patient ON psample.patient_id = patient.pid AND psample.\"labID\" = patient.lab_id
             LEFT JOIN camlis_patient_sample_clinical_symptoms as symptoms ON symptoms.patient_sample_id = psample.\"ID\"
             LEFT JOIN camlis_clinical_symptoms as sym ON sym.\"ID\" = symptoms.clinical_symptom_id
             LEFT JOIN camlis_patient_sample_tests AS psample_test ON psample.\"ID\" = psample_test.patient_sample_id AND psample_test.status = 1
             INNER JOIN camlis_std_sample_test AS std_sampletest ON psample_test.sample_test_id = std_sampletest.\"ID\"
             INNER JOIN camlis_std_department_sample AS department_sample ON std_sampletest.department_sample_id = department_sample.\"ID\" AND department_sample.status = 'TRUE'
             INNER JOIN test ON std_sampletest.test_id = test.\"ID\"
             INNER JOIN camlis_std_sample AS sample_type ON department_sample.sample_id = sample_type.\"ID\" AND sample_type.status = 'TRUE'
             LEFT JOIN camlis_ptest_result AS ptest_result ON psample_test.\"ID\" = ptest_result.patient_test_id AND ptest_result.patient_sample_id = psample.\"ID\" AND ptest_result.status = 1
             LEFT JOIN camlis_std_test_organism AS test_organism ON ptest_result.type = 1 AND ptest_result.result = cast(test_organism.\"ID\" as varchar)
             LEFT JOIN camlis_std_organism AS organism ON test_organism.organism_id = organism.\"ID\"
             WHERE ptest_result.result is not null
             GROUP BY  patient.pid,
              std_sampletest.group_result, 
              sample_type.\"ID\", 
              sample_source.source_name, 
              psample.number_of_sample,
              requester.requester_name,
              psample.health_facility,
              organism.organism_name, 
              organism.organism_value, 
              psample.clinical_history, 
              ptest_result.test_date, 
              ptest_result.type, 
              std_sampletest.group_result, 
              psample.\"labID\", 
              psample.sample_number, 
              lab.name_en, 
              collected_date, 
              received_date, 
              for_research, 
              patient.patient_code, 
              patient.patient_name, 
              patient.dob, 
              patient.sex,
              psample.completed_by,
              psample.phone_number,
              patient.is_positive_covid,
              patient.contact_with,
              patient.is_direct_contact,
              patient.occupation,
              patient.phone,
              patient.residence,
              province_name,
              commune_name,
              district_name,
              village_name,   
              psample.\"ID\",        
              patient.country_name,
              patient.date_arrival,
              patient.passport_number,
              patient.seat_number,
              patient.vaccination_status,
              patient.first_vaccinated_date,
              patient.second_vaccinated_date,
              patient.third_vaccinated_date,
              patient.vaccine_id,
              patient.second_vaccine_id,
              psample.sample_number,
              psample.sample_collector,		
              psample.phone_number_sample_collector,
              nationality,
              patient.nationality_en,
              patient.first_vaccine_name,
              patient.second_vaccine_name,              
              patient.flight_number,
              patient.is_contacted,
              psample.admission_date,
              lab.lab_code,
              test.test_name,
              test.\"ID\",
              patient.forth_vaccinated_date,
              patient.third_vaccine_name
              order by psample.sample_number ASC;
         ";
         return $this->db->query($sql)->result_array();
    }

    function create_temp_patients($laboratory , $con){
        $sql = "CREATE TEMPORARY TABLE IF NOT EXISTS temp_all_patients as (
            SELECT pmrs_patient.pid,
                 psample.\"labID\" AS lab_id,
                 pmrs_patient.pid AS patient_id,
                 pmrs_patient.pid AS patient_code,
                 pmrs_patient.name AS patient_name,
                     CASE pmrs_patient.sex
                         WHEN 'M'::bpchar THEN 1
                         WHEN 'F'::bpchar THEN 2
                         ELSE 0
                     END AS sex,
                 pmrs_patient.dob,
                 pmrs_patient.phone,
                 pmrs_patient.province,
                 pmrs_patient.district,
                 pmrs_patient.commune,
                 pmrs_patient.village,
                 ''::character varying AS nationality_en,
                 ''::character varying AS passport_number,
                 ''::character varying AS flight_number,
                 NULL::date AS date_arrival
             FROM camlis_patient_sample psample
             JOIN camlis_pmrs_patient pmrs_patient ON psample.patient_id::text = pmrs_patient.pid::text AND pmrs_patient.status = 1
             WHERE psample.status = 1 and psample.\"labID\" = ANY(VALUES".$laboratory.") $con
             GROUP BY pmrs_patient.pid, psample.patient_id, psample.\"labID\"
             UNION
             SELECT outside_patient.pid::text AS pid,
                 outside_patient.lab_id,
                 outside_patient.patient_id,
                 outside_patient.patient_code,
                 outside_patient.patient_name,
                 outside_patient.sex,
                 outside_patient.dob,
                 outside_patient.phone,
                 outside_patient.province,
                 outside_patient.district,
                 outside_patient.commune,
                 outside_patient.village,
                 countries.nationality_en,
                 outside_patient.passport_number,
                 outside_patient.flight_number,
                 outside_patient.date_arrival
             FROM camlis_outside_patient outside_patient
             LEFT JOIN countries ON outside_patient.nationality = countries.num_code
             WHERE outside_patient.status = 1 AND outside_patient.lab_id = ANY(VALUES".$laboratory.")
        )";
        $this->db->query($sql);
    }
    /**
     * Micro Report
     * 13012022
     */
    public function get_patient_gender($startDateTime, $endDateTime, $laboratory) {
        $receivedDateCond  = " AND concat( received_date,' ',received_time) >= '".$startDateTime."'";
        $receivedDateCond .= " AND concat( received_date,' ',received_time) <= '".$endDateTime."'";
        $this->create_temp_patients($laboratory, $receivedDateCond);
        $sql = "
            WITH psample AS (
                SELECT DISTINCT patient_id, psample.\"labID\" FROM camlis_patient_sample as psample
                JOIN camlis_patient_sample_tests AS ptest ON psample.\"ID\" = ptest.patient_sample_id AND ptest.status = 1
                JOIN camlis_std_sample_test AS sample_test ON ptest.sample_test_id = sample_test.\"ID\" AND sample_test.test_id = 170
                JOIN camlis_std_department_sample AS dsample ON sample_test.department_sample_id = dsample.\"ID\"
                WHERE psample.status = 1 and psample.\"labID\" = ANY(VALUES".$laboratory.") ".$receivedDateCond."
                group by patient_id, psample.\"labID\" 
            )
            SELECT
                (CASE WHEN  patient.sex = 1 THEN 'Male' ELSE 'Female' END) as gender,
                SUM( CASE WHEN patient.sex = 1 THEN 1 ELSE 0 END ) AS male,
                SUM( CASE WHEN patient.sex = 2 THEN 1 ELSE 0 END ) AS female
            FROM psample
            INNER JOIN temp_all_patients patient ON patient.pid = psample.patient_id AND patient.lab_id = psample.\"labID\"            
            GROUP BY patient.sex
        ";
        return $this->db->query($sql)->result_array();
    }

    public function get_patient_age($startDateTime, $endDateTime, $laboratory) {                
        $receivedDateCond  = " AND concat( received_date,' ',received_time) >= '".$startDateTime."'";
        $receivedDateCond .= " AND concat( received_date,' ',received_time) <= '".$endDateTime."'";
        $this->create_temp_patients($laboratory, $receivedDateCond);
        $sql = "
            SELECT 
                _t.age_group,
                SUM( CASE WHEN _t.sex = 1 THEN 1 ELSE 0 END ) AS male,
                SUM( CASE WHEN _t.sex = 2 THEN 1 ELSE 0 END ) AS female
            FROM 
            (
            SELECT 
                DISTINCT psample.patient_id,
                patient.sex,
                (CASE                    
                    WHEN ((psample.collected_date - patient.dob <= 28)) THEN '0 - 28d'
                    WHEN ((psample.collected_date - patient.dob)) BETWEEN 29 AND 364 THEN '29d - <1y'
                    WHEN DATE_PART('year', AGE(psample.collected_date, patient.dob)) BETWEEN 1 AND 4 THEN '1y - 4y'
                    WHEN DATE_PART('year', AGE(psample.collected_date, patient.dob)) BETWEEN 5 AND 14 THEN '5y - 14y'
                    WHEN DATE_PART('year', AGE(psample.collected_date, patient.dob)) BETWEEN 15 AND 24 THEN '15y - 24y'
                    WHEN DATE_PART('year', AGE(psample.collected_date, patient.dob)) BETWEEN 25 AND 34 THEN '25y - 34y'
                    WHEN DATE_PART('year', AGE(psample.collected_date, patient.dob)) BETWEEN 35 AND 44 THEN '35y - 44y'
                    WHEN DATE_PART('year', AGE(psample.collected_date, patient.dob)) BETWEEN 45 AND 54 THEN '45y - 54y'
                    WHEN DATE_PART('year', AGE(psample.collected_date, patient.dob)) BETWEEN 55 AND 64 THEN '55y - 64y'
                    WHEN DATE_PART('year', AGE(psample.collected_date, patient.dob)) BETWEEN 65 AND 80 THEN '65y - 80y'
                    ELSE '>= 81y' 
                END) AS age_group
            FROM camlis_patient_sample AS psample
            INNER JOIN temp_all_patients AS patient ON psample.patient_id = patient.pid AND psample.\"labID\" = patient.lab_id
            JOIN camlis_patient_sample_tests AS ptest ON psample.\"ID\" = ptest.patient_sample_id AND ptest.status = 1
            JOIN camlis_std_sample_test AS sample_test ON ptest.sample_test_id = sample_test.\"ID\" AND sample_test.test_id = 170
            JOIN camlis_std_department_sample AS dsample ON sample_test.department_sample_id = dsample.\"ID\"
            WHERE psample.status = 1 AND psample.\"labID\" = ANY(VALUES".$laboratory.") ".$receivedDateCond."
            ) _t
            WHERE _t.age_group IS NOT NULL
            GROUP BY _t.age_group";

        return $this->db->query($sql)->result_array();
    }
    // 3) Microbiology Specimen

    public function get_micro_specimen($sample_id, $startDateTime, $endDateTime, $laboratory){
        $receivedDateCond  = " AND concat( received_date,' ',received_time) >= '".$startDateTime."'";
        $receivedDateCond .= " AND concat( received_date,' ',received_time) <= '".$endDateTime."'";
        $this->create_temp_patients($laboratory, $receivedDateCond);
        $sql = " 
            WITH psample AS (
                SELECT * FROM camlis_patient_sample 
                WHERE status = 1 AND \"labID\" = ANY(VALUES".$laboratory.") ".$receivedDateCond." 
            ) SELECT sub_tbl.month,
                    sub_tbl.year,
                    SUM(sub_tbl.count) AS count,
                    sub_tbl.sample_id,
                    sub_tbl.sample_name
            FROM(
                SELECT 
                    to_char(psample.received_date,'Mon') AS month,
                    to_char(psample.received_date, 'YY') AS year,
                    COUNT(DISTINCT dsample.sample_id) As count,
                    dsample.sample_id,
                    sample.sample_name
                FROM psample
                INNER JOIN temp_all_patients patient ON psample.patient_id = patient.pid AND psample.\"labID\" = patient.lab_id
                INNER JOIN camlis_patient_sample_tests AS ptest ON psample.\"ID\" = ptest.patient_sample_id AND ptest.status = 1
                INNER JOIN camlis_std_sample_test AS sample_test ON ptest.sample_test_id = sample_test.\"ID\" AND sample_test.test_id = 170
                INNER JOIN camlis_std_department_sample AS dsample ON sample_test.department_sample_id = dsample.\"ID\" and dsample.sample_id IN (".$sample_id.")
                INNER JOIN camlis_std_sample AS sample ON dsample.sample_id = sample.\"ID\"
                GROUP BY psample.\"ID\", dsample.\"ID\", month, year, sample_name
            ) sub_tbl
            GROUP BY sub_tbl.month, sub_tbl.year, sub_tbl.sample_id, sub_tbl.sample_name
        ";
        return $this->db->query($sql)->result_array();
    }
    /**
     * Merge Pus Swap and Pus Aspirate together
     * 9 & 17
     */
    public function get_pus_specimen($startDateTime, $endDateTime, $laboratory){
        $receivedDateCond  = " AND concat( received_date,' ',received_time) >= '".$startDateTime."'";
        $receivedDateCond .= " AND concat( received_date,' ',received_time) <= '".$endDateTime."'";
        $this->create_temp_patients($laboratory, $receivedDateCond);
        $sql = " 
        WITH psample AS (
            SELECT * FROM camlis_patient_sample 
            WHERE status = 1 AND \"labID\" = ANY(VALUES".$laboratory.") ".$receivedDateCond." 
        ) 
        SELECT sub_tbl.month,
                sub_tbl.year,
                SUM(sub_tbl.count) AS count,
                month_n
        FROM(
            SELECT 
                to_char(psample.received_date,'Mon') AS month,
                to_char(psample.received_date, 'YY') AS year,
                to_char(psample.received_date, 'mm') AS month_n,
                COUNT(DISTINCT dsample.sample_id) As count

            FROM psample
            INNER JOIN temp_all_patients patient ON psample.patient_id = patient.pid AND psample.\"labID\" = patient.lab_id
            INNER JOIN camlis_patient_sample_tests AS ptest ON psample.\"ID\" = ptest.patient_sample_id AND ptest.status = 1
            INNER JOIN camlis_std_sample_test AS sample_test ON ptest.sample_test_id = sample_test.\"ID\" AND sample_test.test_id = 170
            INNER JOIN camlis_std_department_sample AS dsample ON sample_test.department_sample_id = dsample.\"ID\" and dsample.sample_id IN (9,17)
            INNER JOIN camlis_std_sample AS sample ON dsample.sample_id = sample.\"ID\"
            GROUP BY psample.\"ID\", month, year, month_n
        ) sub_tbl
        GROUP BY sub_tbl.month, sub_tbl.year, month_n
        ORDER BY month_n, year ASC
        ";
        return $this->db->query($sql)->result_array();
    }

    public function get_specimen_by_month($startDateTime, $endDateTime, $laboratory){
        $receivedDateCond  = " AND concat( received_date,' ',received_time) >= '".$startDateTime."'";
        $receivedDateCond .= " AND concat( received_date,' ',received_time) <= '".$endDateTime."'";
        $this->create_temp_patients($laboratory, $receivedDateCond);
        $sql = " 
            WITH psample AS (
                SELECT * FROM camlis_patient_sample 
                WHERE status = 1 AND \"labID\" = ANY(VALUES".$laboratory.") ".$receivedDateCond." 
            ) SELECT month,
            year,
            month_n,
            concat(month_n,'-',year) as month_year,
            sample_name,
            count(sample_id) as value,
            (CASE 
                WHEN sample_id = 6 THEN '#ea756c'
                WHEN sample_id = 7 THEN '#c87bff'
                WHEN sample_id = 8 THEN '#a3a500'
                WHEN sample_id = 9 THEN '#d1b087'
                WHEN sample_id = 10 THEN '#5ec0c5'
                WHEN sample_id = 11 THEN '#59b1f6'
                WHEN sample_id = 13 THEN '#57b705'
                WHEN sample_id = 14 THEN '#9590ff'
                WHEN sample_id = 15 THEN '#e76bf3'
                WHEN sample_id = 16 THEN '#f4c90a'
                WHEN sample_id = 17 THEN '#d1b087'
                ELSE '' END) AS color,
            sample_id
            FROM(
                SELECT 
                    to_char(psample.received_date,'Mon') AS month,
                    to_char(psample.received_date, 'YY') AS year,
                    to_char(psample.received_date, 'mm') AS month_n,
                    dsample.sample_id,
                    sample.sample_name as sample_name                    
                FROM psample
                INNER JOIN temp_all_patients patient ON psample.patient_id = patient.pid AND psample.\"labID\" = patient.lab_id
                INNER JOIN camlis_patient_sample_tests AS ptest ON psample.\"ID\" = ptest.patient_sample_id AND ptest.status = 1
                INNER JOIN camlis_std_sample_test AS sample_test ON ptest.sample_test_id = sample_test.\"ID\" AND sample_test.test_id = 170
                INNER JOIN camlis_std_department_sample AS dsample ON sample_test.department_sample_id = dsample.\"ID\"
                INNER JOIN camlis_std_sample AS sample ON dsample.sample_id = sample.\"ID\"
                GROUP BY psample.\"ID\", dsample.\"ID\", month, year, month_n, sample_name
            ) sub_tbl
            GROUP BY month, year, month_n, sample_name, sample_id
            ORDER BY month_n , year ASC
        ";
        return $this->db->query($sql)->result_array();
    }
       
    /**
     * Specimen: Blood Culture
     * Blood Culture volume
     * Count the volumn base on patient age
     * 
     */
    public function get_adult_blood_volume( $startDateTime, $endDateTime, $laboratory){
        
        $receivedDateCond  = " AND concat( received_date,' ',received_time) >= '".$startDateTime."'";
        $receivedDateCond .= " AND concat( received_date,' ',received_time) <= '".$endDateTime."'";
        $this->create_temp_patients($laboratory, $receivedDateCond);
        $sql = "
        WITH psample AS (
            SELECT * FROM camlis_patient_sample 
            WHERE status = 1 AND \"labID\" = ANY(VALUES".$laboratory.") ".$receivedDateCond." 
        )
        select month,year, month_n, concat(month,'-',year) as month_year, low , correct , heigh,
            round((low::decimal / (low + correct + heigh)) *100 ) as low_percentage,
            round((correct::decimal / (low + correct + heigh)) * 100 ) as correct_percentage,
            round((heigh::decimal / (low + correct + heigh)) * 100 ) as heigh_percentage
        FROM (
            select month,year, month_n, concat(month,'-',year) as month_year,
                SUM( (CASE WHEN volume1 < 8 THEN 1 ELSE 0 END) + (CASE WHEN volume2 < 8 THEN 1 ELSE 0 END)) AS low,
                SUM( (CASE WHEN volume1 >= 8 AND volume1 <= 12 THEN 1 ELSE 0 END) + (CASE WHEN volume2 >= 8 AND volume2 <= 12 THEN 1 ELSE 0 END)) AS correct,
                SUM( (CASE WHEN volume1 > 12 THEN 1 ELSE 0 END) + (CASE WHEN volume2 > 12 THEN 1 ELSE 0 END)) AS heigh            
            FROM (select 
                to_char(psample.received_date,'Mon') AS month,
                to_char(psample.received_date, 'YY') AS year,
                to_char(psample.received_date, 'mm') AS month_n,
                psample_detail.sample_volume1 AS volume1, 
                psample_detail.sample_volume2 AS volume2,
                patient.pid
                FROM psample 
                INNER JOIN temp_all_patients patient ON patient.pid = psample.patient_id
                INNER JOIN camlis_patient_sample_detail AS psample_detail ON psample.\"ID\" = psample_detail.patient_sample_id
                WHERE (psample_detail.sample_volume1 is not null or psample_detail.sample_volume2 is not null) and psample_detail.department_sample_id = 6
                AND DATE_PART('year',age(psample.collected_date, patient.dob)) >= 14 
                group by month, year, volume1 , volume2, pid, month_n
            ) sub_tbl
            group by month, year , month_n
            ORDER BY month_n,year ASC
        ) tbl
        ";
        return $this->db->query($sql)->result_array();
    }

    public function get_pediatric_under_28d_blood_volume($startDateTime, $endDateTime, $laboratory){
        $receivedDateCond  = " AND concat( received_date,' ',received_time) >= '".$startDateTime."'";
        $receivedDateCond .= " AND concat( received_date,' ',received_time) <= '".$endDateTime."'";
        $this->create_temp_patients($laboratory, $receivedDateCond);
        $sql = "
        WITH psample AS (
            SELECT * FROM camlis_patient_sample
            WHERE status = 1 AND \"labID\" = ANY(VALUES".$laboratory.") ".$receivedDateCond." 
        )
        select month,year, month_n, concat(month,'-',year) as month_year,
            SUM( (CASE WHEN volume1 > 0 THEN 1 ELSE 0 END) + (CASE WHEN volume2 > 0 THEN 1 ELSE 0 END)) AS total
        FROM (select 
            to_char(psample.received_date,'Mon') AS month,
            to_char(psample.received_date, 'YY') AS year,
            to_char(psample.received_date, 'mm') AS month_n,
            psample_detail.sample_volume1 AS volume1, 
            psample_detail.sample_volume2 AS volume2,
            patient.pid
            FROM psample 
            INNER JOIN temp_all_patients patient ON patient.pid = psample.patient_id
            INNER JOIN camlis_patient_sample_detail AS psample_detail ON psample.\"ID\" = psample_detail.patient_sample_id
            WHERE (psample_detail.sample_volume1 is not null or psample_detail.sample_volume2 is not null) and psample_detail.department_sample_id = 6
            AND ((psample.collected_date - patient.dob)) BETWEEN 0 AND 28
            group by month, year, volume1 , volume2, pid, month_n
        ) sub_tbl
        group by month, year , month_n
        ORDER BY month_n,year ASC
        ";
        return $this->db->query($sql)->result_array();
    }

    public function get_pediatric_29d1y_blood_volume($startDateTime, $endDateTime, $laboratory){
        $receivedDateCond  = " AND concat( received_date,' ',received_time) >= '".$startDateTime."'";
        $receivedDateCond .= " AND concat( received_date,' ',received_time) <= '".$endDateTime."'";
        $this->create_temp_patients($laboratory, $receivedDateCond);
        $sql = "
        WITH psample AS (
            SELECT * FROM camlis_patient_sample 
            WHERE status = 1 AND \"labID\" = ANY(VALUES".$laboratory.") ".$receivedDateCond." 
        )
        select month,year, month_n, concat(month,'-',year) as month_year, low , correct , heigh,
            round((low::decimal / (low + correct + heigh)) *100 ) as low_percentage,
            round((correct::decimal / (low + correct + heigh)) * 100 ) as correct_percentage,
            round((heigh::decimal / (low + correct + heigh)) * 100 ) as heigh_percentage
        FROM (
            select month,year, month_n, concat(month,'-',year) as month_year,
                SUM( (CASE WHEN volume1 < 0.5 THEN 1 ELSE 0 END) + (CASE WHEN volume2 < 0.5 THEN 1 ELSE 0 END)) AS low,
                SUM( (CASE WHEN volume1 >= 0.5 AND volume1 < 6 THEN 1 ELSE 0 END) + (CASE WHEN volume2 >= 0.5 AND volume2 < 6 THEN 1 ELSE 0 END)) AS correct,
                SUM( (CASE WHEN volume1 >= 2 THEN 1 ELSE 0 END) + (CASE WHEN volume2 > 2 THEN 1 ELSE 0 END)) AS heigh
            FROM (select 
                to_char(psample.received_date,'Mon') AS month,
                to_char(psample.received_date, 'YY') AS year,
                to_char(psample.received_date, 'mm') AS month_n,
                psample_detail.sample_volume1 AS volume1, 
                psample_detail.sample_volume2 AS volume2,
                patient.pid
                FROM psample 
                INNER JOIN temp_all_patients patient ON patient.pid = psample.patient_id
                INNER JOIN camlis_patient_sample_detail AS psample_detail ON psample.\"ID\" = psample_detail.patient_sample_id
                WHERE (psample_detail.sample_volume1 is not null or psample_detail.sample_volume2 is not null) and psample_detail.department_sample_id = 6
                AND ((psample.collected_date - patient.dob)) BETWEEN 29 AND 364
                group by month, year, volume1 , volume2, pid, month_n

            ) sub_tbl
            group by month, year , month_n
            ORDER BY month_n,year ASC
        ) tbl
        ";
        return $this->db->query($sql)->result_array();
    }

    public function get_pediatric_1y14y_blood_volume($startDateTime, $endDateTime, $laboratory){
        $receivedDateCond  = " AND concat( received_date,' ',received_time) >= '".$startDateTime."'";
        $receivedDateCond .= " AND concat( received_date,' ',received_time) <= '".$endDateTime."'";
        $this->create_temp_patients($laboratory, $receivedDateCond);
        $sql = "
        WITH psample AS (
            SELECT * FROM camlis_patient_sample 
            WHERE status = 1 AND \"labID\" = ANY(VALUES".$laboratory.") ".$receivedDateCond." 
        )
        select month,year, month_n, concat(month,'-',year) as month_year, low , correct , heigh,
            round((low::decimal / (low + correct + heigh)) *100 ) as low_percentage,
            round((correct::decimal / (low + correct + heigh)) * 100 ) as correct_percentage,
            round((heigh::decimal / (low + correct + heigh)) * 100 ) as heigh_percentage
        FROM (
            select month,year, month_n, concat(month,'-',year) as month_year,
                SUM( (CASE WHEN volume1 < 1 THEN 1 ELSE 0 END) + (CASE WHEN volume2 < 1 THEN 1 ELSE 0 END)) AS low,
                SUM( (CASE WHEN volume1 >= 1 AND volume1 < 6 THEN 1 ELSE 0 END) + (CASE WHEN volume2 >= 1 AND volume2 < 6 THEN 1 ELSE 0 END)) AS correct,
                SUM( (CASE WHEN volume1 >= 6 THEN 1 ELSE 0 END) + (CASE WHEN volume2 >= 6 THEN 1 ELSE 0 END)) AS heigh
            FROM (select 
                to_char(psample.received_date,'Mon') AS month,
                to_char(psample.received_date, 'YY') AS year,
                to_char(psample.received_date, 'mm') AS month_n,
                psample_detail.sample_volume1 AS volume1, 
                psample_detail.sample_volume2 AS volume2,
                patient.pid
                FROM psample 
                INNER JOIN temp_all_patients patient ON patient.pid = psample.patient_id
                INNER JOIN camlis_patient_sample_detail AS psample_detail ON psample.\"ID\" = psample_detail.patient_sample_id
                WHERE (psample_detail.sample_volume1 is not null or psample_detail.sample_volume2 is not null) and psample_detail.department_sample_id = 6
                AND DATE_PART('year', AGE(psample.collected_date, patient.dob)) BETWEEN 1 AND 14
                group by month, year, volume1 , volume2, pid, month_n

            ) sub_tbl
            group by month, year , month_n
            ORDER BY month_n,year ASC
        ) tbl
        ";
        return $this->db->query($sql)->result_array();
    }
    
    /**
     * Notifiable and other important pathegens list: All specimen
     * 
     * 131: Bacillus anthracis
     * 150: Burkholderia pseudomallei
     * 181: Corynebacterium diphtheriae
     * 247: Francisella tularensis
     * 309: Listeria monocytogenes
     * 333: Neisseria gonorrhoeae
     * 334: Neisseria meningitidis
     * 402: Salmonella Paratyphi A
     * 405: Salmonella sp.
     * 406: Salmonella Typhi
     * 448: Streptococcus suis
     * 480: Vibrio cholerae
     * 495: Yersinia pestis
     */
    public function get_notifiable_pathogens_list($patient_sample_id , $startDateTime, $endDateTime, $laboratory){
        $receivedDateCond  = " AND concat( received_date,' ',received_time) >= '".$startDateTime."'";
        $receivedDateCond .= " AND concat( received_date,' ',received_time) <= '".$endDateTime."'";
        $this->create_temp_patients($laboratory, $receivedDateCond);
        $sql = "
        WITH psample AS (
            SELECT * FROM camlis_patient_sample 
            WHERE status = 1 AND \"ID\" IN (".$patient_sample_id.") AND \"labID\" = ANY(VALUES".$laboratory.") ".$receivedDateCond." 
        )
        SELECT 
            organism_name, 
            month, 
            month_n, 
            year, 
            count(organism_name) as total,
            organism_id
        FROM (
            SELECT
            to_char(psample.received_date,'Month') AS month,
            to_char(psample.received_date,'mm') AS month_n,
            to_char(psample.received_date, 'YYYY') AS year,
            organism.organism_name,
            test_organism.organism_id
            FROM psample
            JOIN temp_all_patients patient ON patient.pid = psample.patient_id
            JOIN camlis_ptest_result AS presult ON presult.patient_sample_id = psample.\"ID\" AND presult.status = 1
            JOIN camlis_patient_sample_tests AS psample_tests ON psample_tests.\"ID\" = presult.patient_test_id AND psample_tests.status = 1
            JOIN camlis_std_sample_test AS sample_test ON sample_test.\"ID\" = psample_tests.sample_test_id and sample_test.status = True AND sample_test.test_id = 170
            JOIN camlis_std_test_organism AS test_organism ON test_organism.sample_test_id = sample_test.\"ID\" and test_organism.status =True AND presult.result = cast(test_organism.\"ID\" as varchar)
            JOIN camlis_std_organism AS organism ON test_organism.organism_id = organism.\"ID\" 
            WHERE test_organism.organism_id IN (150,448,309,405,406,402,480,131,495,247,181,333,334)
        ) sub_tbl
        group by organism_name, month, year, month_n,organism_id
        order by organism_name, month_n asc";
        return $this->db->query($sql)->result_array();
    }
    
    /**
     * Get Burkholderia pseudomallei (BPS) from all specimen
     */
     
    public function get_bps_from_all_specimen($startDateTime, $endDateTime, $laboratory){
        $receivedDateCond  = " AND concat( received_date,' ',received_time) >= '".$startDateTime."'";
        $receivedDateCond .= " AND concat( received_date,' ',received_time) <= '".$endDateTime."'";
        $this->create_temp_patients($laboratory, $receivedDateCond);
        $sql = "
        WITH psample AS (
            SELECT * FROM camlis_patient_sample 
            WHERE status = 1 AND \"labID\" = ANY(VALUES".$laboratory.") ".$receivedDateCond." 
        )
        SELECT 
            patient.pid,
            dsample.sample_id,
            psample.collected_date,
            psample.\"ID\" AS patient_sample_id,
            to_char(psample.received_date,'Mon') AS month,
            to_char(psample.received_date, 'YY') AS year, 
            to_char(psample.received_date,'mm') AS month_n
            
        FROM psample
        JOIN temp_all_patients patient ON patient.pid = psample.patient_id 
        JOIN camlis_ptest_result AS presult ON presult.patient_sample_id = psample.\"ID\" AND presult.status = 1
        JOIN camlis_std_test_organism AS test_organism ON presult.result = cast(test_organism.\"ID\" as varchar)
        JOIN camlis_std_organism AS organism ON test_organism.organism_id = organism.\"ID\"    
        JOIN camlis_patient_sample_tests AS psample_tests ON psample_tests.\"ID\" = presult.patient_test_id AND psample_tests.status = 1
        JOIN camlis_std_sample_test AS std_sample_test ON std_sample_test.\"ID\" = psample_tests.sample_test_id AND std_sample_test.status = TRUE AND std_sample_test.test_id = 170 
        JOIN camlis_std_department_sample AS dsample ON dsample.\"ID\" = std_sample_test.department_sample_id AND dsample.status = TRUE
        JOIN camlis_std_sample AS std_sample ON std_sample.\"ID\" = dsample.sample_id AND std_sample.status = TRUE
        WHERE test_organism.organism_id = 150 AND dsample.department_id = 4 AND dsample.sample_id IN (6,7,8,9,10,11,12,13,14,15,16,17)        
        ORDER BY patient.pid, psample.collected_date, psample.\"ID\" ASC
        ";
        return $this->db->query($sql)->result_array();
    }  
    /**
     * Get Burkholderia pseudomallei (Bps) from all specimens
     */
    public function get_antibiotic_bps($patient_sample_ids, $startDateTime, $endDateTime, $laboratory){
        $receivedDateCond  = " AND concat( received_date,' ',received_time) >= '".$startDateTime."'";
        $receivedDateCond .= " AND concat( received_date,' ',received_time) <= '".$endDateTime."'";
        $this->create_temp_patients($laboratory, $receivedDateCond);
        $sql = "
        WITH psample AS (
            SELECT * FROM camlis_patient_sample 
            WHERE status = 1 AND \"ID\" IN (".$patient_sample_ids.") AND \"labID\" = ANY(VALUES".$laboratory.") ".$receivedDateCond." 
        )
        SELECT antibiotic_name, 
            SUM( CASE WHEN sensitivity = 'S' THEN 1 ELSE 0 END ) AS sensitive,
            SUM( CASE WHEN sensitivity = 'R' THEN 1 ELSE 0 END ) AS resistant,
            SUM( CASE WHEN sensitivity = 'I' THEN 1 ELSE 0 END ) AS intermediate,
            COUNT(sensitivity) AS total
        from (
            SELECT 
            antibiotic.antibiotic_name,
            (CASE presult_antibiotic.sensitivity
                WHEN 1 THEN 'S'
                WHEN 2 THEN 'R'
                WHEN 3 THEN 'I'
            END) AS sensitivity
            FROM 
            psample
            JOIN temp_all_patients patient ON patient.pid = psample.patient_id 
            JOIN camlis_ptest_result AS presult ON presult.patient_sample_id = psample.\"ID\" AND presult.status = 1
            JOIN camlis_std_test_organism AS test_organism ON presult.result = cast(test_organism.\"ID\" as varchar)
            JOIN camlis_std_organism AS organism ON test_organism.organism_id = organism.\"ID\"    
            JOIN camlis_patient_sample_tests AS psample_tests ON psample_tests.\"ID\" = presult.patient_test_id  AND psample_tests.status = 1
            JOIN camlis_std_sample_test AS std_sample_test ON std_sample_test.\"ID\" = psample_tests.sample_test_id AND std_sample_test.status = TRUE AND std_sample_test.test_id = 170
            JOIN camlis_std_department_sample AS dsample ON dsample.\"ID\" = std_sample_test.department_sample_id AND dsample.status = TRUE   
            JOIN camlis_std_sample AS std_sample ON std_sample.\"ID\" = dsample.sample_id AND std_sample.status = TRUE
            JOIN camlis_ptest_result_antibiotic AS presult_antibiotic ON presult.\"ID\" = presult_antibiotic.presult_id AND presult_antibiotic.status = TRUE
            JOIN camlis_std_antibiotic AS antibiotic ON presult_antibiotic.antibiotic_id = antibiotic.\"ID\"
            WHERE test_organism.organism_id = 150 AND dsample.department_id = 4 AND dsample.sample_id IN (6,7,8,9,10,11,12,13,14,15,16,17)
        ) sub_tbl
        group by antibiotic_name
        ";
        return $this->db->query($sql)->result_array();
    }
    /**
     * Get all Salmonella from all specimen
     * 399: Salmonella Choleraesuis
     * 400: Salmonella Enteritidis
     * 401: Salmonella Paratyphi
     * 402: Salmonella Paratyphi A
     * 403: Salmonella Paratyphi B
     * 404: Salmonella Paratyphi C
     * 405: Salmonella sp.
     * 406: Salmonella Typhi
     * 407: Salmonella Typhimurium
     * 408: Salmonella, non-typhi
     * 409: Salmonella, non-typhi/non-paratyphi
     * 643: Presumptive Salmonella Typhi
     * 644: Presumptive Salmonella Paratyphi A
     */

    function get_patient_sample_id($startDateTime, $endDateTime, $laboratory){
        $receivedDateCond  = " AND concat( received_date,' ',received_time) >= '".$startDateTime."'";
        $receivedDateCond .= " AND concat( received_date,' ',received_time) <= '".$endDateTime."'";
        $this->create_temp_patients($laboratory, $receivedDateCond);
        $sql = "
            WITH psample AS (
                SELECT * FROM camlis_patient_sample 
                WHERE status = 1 AND \"labID\" = ANY(VALUES".$laboratory.") ".$receivedDateCond." 
            )
            SELECT 
                to_char(psample.received_date,'Mon') AS month,
                to_char(psample.received_date, 'YY') AS year,
                test_organism.organism_id
            FROM 
            psample
            JOIN temp_all_patients patient ON patient.pid = psample.patient_id 
            JOIN camlis_ptest_result AS presult ON presult.patient_sample_id = psample.\"ID\" AND presult.status = 1
            JOIN camlis_std_test_organism AS test_organism ON presult.result = cast(test_organism.\"ID\" as varchar)
            JOIN camlis_std_organism AS organism ON test_organism.organism_id = organism.\"ID\"    
            JOIN camlis_patient_sample_tests AS psample_tests ON psample_tests.\"ID\" = presult.patient_test_id  AND psample_tests.status = 1
            JOIN camlis_std_sample_test AS std_sample_test ON std_sample_test.\"ID\" = psample_tests.sample_test_id AND std_sample_test.status = TRUE
            JOIN camlis_std_department_sample AS dsample ON dsample.\"ID\" = std_sample_test.department_sample_id AND dsample.status = TRUE   
            JOIN camlis_std_sample AS std_sample ON std_sample.\"ID\" = dsample.sample_id AND std_sample.status = TRUE    
            WHERE test_organism.organism_id IN (399, 400, 401,402,403,404,405,406,407,408,409,643,644) AND dsample.department_id = 4 AND dsample.sample_id = 6
            group by month, year
        ";
        return $this->db->query($sql)->result_array();
    }
    
    public function get_salmonella_from_all_specimen($patient_sample_ids, $startDateTime, $endDateTime, $laboratory){
        $receivedDateCond  = " AND concat( received_date,' ',received_time) >= '".$startDateTime."'";
        $receivedDateCond .= " AND concat( received_date,' ',received_time) <= '".$endDateTime."'";
        $this->create_temp_patients($laboratory, $receivedDateCond);
        $sql = "
        WITH psample AS (
            SELECT * FROM camlis_patient_sample 
            WHERE status = 1 AND \"ID\" IN (".$patient_sample_ids.") AND \"labID\" = ANY(VALUES".$laboratory.") ".$receivedDateCond." 
        )
        SELECT month, year, count(organism_id) as total
        from (
            SELECT 
            to_char(psample.received_date,'Mon') AS month,
            to_char(psample.received_date, 'YY') AS year,
            test_organism.organism_id
            FROM 
            psample
            JOIN temp_all_patients patient ON patient.pid = psample.patient_id 
            JOIN camlis_ptest_result AS presult ON presult.patient_sample_id = psample.\"ID\" AND presult.status = 1
            JOIN camlis_std_test_organism AS test_organism ON presult.result = cast(test_organism.\"ID\" as varchar)
            JOIN camlis_std_organism AS organism ON test_organism.organism_id = organism.\"ID\"    
            JOIN camlis_patient_sample_tests AS psample_tests ON psample_tests.\"ID\" = presult.patient_test_id  AND psample_tests.status = 1
            JOIN camlis_std_sample_test AS std_sample_test ON std_sample_test.\"ID\" = psample_tests.sample_test_id AND std_sample_test.status = TRUE
            JOIN camlis_std_department_sample AS dsample ON dsample.\"ID\" = std_sample_test.department_sample_id AND dsample.status = TRUE   
            JOIN camlis_std_sample AS std_sample ON std_sample.\"ID\" = dsample.sample_id AND std_sample.status = TRUE    
            WHERE test_organism.organism_id IN (399, 400, 401,402,403,404,405,406,407,408,409,643,644) AND dsample.department_id = 4 AND dsample.sample_id = 6
        ) sub_tbl
        group by month, year
        ";
        return $this->db->query($sql)->result_array();
    }
    /**
     * Get antibiotic of organism 
     *         235: Escherichia coli
     *         430: Staphylococcus
     *         301: Khlebsiella pneumoniae
     *         383: Pseudomonas aeruginosa
     *         Acinetobacter (92,93,94)
     * Sample_id = 6 (Blood Cuture)
     */

    public function get_antibiotic_organism($patient_sample_ids, $orgaism_id , $sample_id,  $startDateTime, $endDateTime, $laboratory){
        $receivedDateCond  = " AND concat( received_date,' ',received_time) >= '".$startDateTime."'";
        $receivedDateCond .= " AND concat( received_date,' ',received_time) <= '".$endDateTime."'";
        $this->create_temp_patients($laboratory, $receivedDateCond);
        $sql = "
        WITH psample AS (
            SELECT * FROM camlis_patient_sample 
            WHERE status = 1 AND \"ID\" IN (".$patient_sample_ids.") AND \"labID\" = ANY(VALUES".$laboratory.") ".$receivedDateCond." 
        )
        SELECT antibiotic_name, 
            SUM( CASE WHEN sensitivity = 'S' THEN 1 ELSE 0 END ) AS sensitive,
            SUM( CASE WHEN sensitivity = 'R' THEN 1 ELSE 0 END ) AS resistant,
            SUM( CASE WHEN sensitivity = 'I' THEN 1 ELSE 0 END ) AS intermediate,
            COUNT(sensitivity) AS total
        from (
            SELECT 
            antibiotic.antibiotic_name,
            (CASE presult_antibiotic.sensitivity
                WHEN 1 THEN 'S'
                WHEN 2 THEN 'R'
                WHEN 3 THEN 'I'
            END) AS sensitivity
            FROM 
            psample
            JOIN temp_all_patients patient ON patient.pid = psample.patient_id
            JOIN camlis_ptest_result AS presult ON presult.patient_sample_id = psample.\"ID\" AND presult.status = 1
            JOIN camlis_std_test_organism AS test_organism ON presult.result = cast(test_organism.\"ID\" as varchar)
            JOIN camlis_std_organism AS organism ON test_organism.organism_id = organism.\"ID\"    
            JOIN camlis_patient_sample_tests AS psample_tests ON psample_tests.\"ID\" = presult.patient_test_id  AND psample_tests.status = 1
            JOIN camlis_std_sample_test AS std_sample_test ON std_sample_test.\"ID\" = psample_tests.sample_test_id AND std_sample_test.status = TRUE
            JOIN camlis_std_department_sample AS dsample ON dsample.\"ID\" = std_sample_test.department_sample_id AND dsample.status = TRUE   
            JOIN camlis_std_sample AS std_sample ON std_sample.\"ID\" = dsample.sample_id AND std_sample.status = TRUE
            JOIN camlis_ptest_result_antibiotic AS presult_antibiotic ON presult.\"ID\" = presult_antibiotic.presult_id AND presult_antibiotic.status = TRUE
            JOIN camlis_std_antibiotic AS antibiotic ON presult_antibiotic.antibiotic_id = antibiotic.\"ID\"
            WHERE test_organism.organism_id IN (".$orgaism_id.") AND dsample.department_id = 4 AND dsample.sample_id = ".$sample_id."        
        ) sub_tbl
        group by antibiotic_name
        order by total DESC
        ";
        return $this->db->query($sql)->result_array();
    }
    // CSF 
    public function get_antibiotic_organism_csf($orgaism_id , $startDateTime, $endDateTime, $laboratory){
        $receivedDateCond  = " AND concat( received_date,' ',received_time) >= '".$startDateTime."'";
        $receivedDateCond .= " AND concat( received_date,' ',received_time) <= '".$endDateTime."'";
        $this->create_temp_patients($laboratory, $receivedDateCond);
        $sql = "
        WITH psample AS (
            SELECT * FROM camlis_patient_sample 
            WHERE status = 1 AND \"labID\" = ANY(VALUES".$laboratory.") ".$receivedDateCond." 
        )
        SELECT antibiotic_name, 
            SUM( CASE WHEN sensitivity = 'S' THEN 1 ELSE 0 END ) AS sensitive,
            SUM( CASE WHEN sensitivity = 'R' THEN 1 ELSE 0 END ) AS resistant,
            SUM( CASE WHEN sensitivity = 'I' THEN 1 ELSE 0 END ) AS intermediate,
            COUNT(sensitivity) AS total
        from (
            SELECT 
            antibiotic.antibiotic_name,
            (CASE presult_antibiotic.sensitivity
                WHEN 1 THEN 'S'
                WHEN 2 THEN 'R'
                WHEN 3 THEN 'I'
            END) AS sensitivity
            FROM 
            psample
            JOIN temp_all_patients patient ON patient.pid = psample.patient_id
            JOIN camlis_ptest_result AS presult ON presult.patient_sample_id = psample.\"ID\" AND presult.status = 1
            JOIN camlis_std_test_organism AS test_organism ON presult.result = cast(test_organism.\"ID\" as varchar)
            JOIN camlis_std_organism AS organism ON test_organism.organism_id = organism.\"ID\"    
            JOIN camlis_patient_sample_tests AS psample_tests ON psample_tests.\"ID\" = presult.patient_test_id  AND psample_tests.status = 1
            JOIN camlis_std_sample_test AS std_sample_test ON std_sample_test.\"ID\" = psample_tests.sample_test_id AND std_sample_test.status = TRUE
            JOIN camlis_std_department_sample AS dsample ON dsample.\"ID\" = std_sample_test.department_sample_id AND dsample.status = TRUE   
            JOIN camlis_std_sample AS std_sample ON std_sample.\"ID\" = dsample.sample_id AND std_sample.status = TRUE
            JOIN camlis_ptest_result_antibiotic AS presult_antibiotic ON presult.\"ID\" = presult_antibiotic.presult_id AND presult_antibiotic.status = TRUE
            JOIN camlis_std_antibiotic AS antibiotic ON presult_antibiotic.antibiotic_id = antibiotic.\"ID\"
            WHERE test_organism.organism_id IN (".$orgaism_id.") AND dsample.department_id = 4 AND dsample.sample_id = 8        
        ) sub_tbl
        group by antibiotic_name
        order by total DESC
        ";
        return $this->db->query($sql)->result_array();
    }
    public function get_antibiotic_bps_organism($startDateTime, $endDateTime, $laboratory){
        $receivedDateCond  = " AND concat( received_date,' ',received_time) >= '".$startDateTime."'";
        $receivedDateCond .= " AND concat( received_date,' ',received_time) <= '".$endDateTime."'";
        $this->create_temp_patients($laboratory, $receivedDateCond);
        $sql = "
        WITH psample AS (
            SELECT * FROM camlis_patient_sample 
            WHERE status = 1 AND \"labID\" = ANY(VALUES".$laboratory.") ".$receivedDateCond." 
        )
        SELECT antibiotic_name, 
            SUM( CASE WHEN sensitivity = 'S' THEN 1 ELSE 0 END ) AS sensitive,
            SUM( CASE WHEN sensitivity = 'R' THEN 1 ELSE 0 END ) AS resistant,
            SUM( CASE WHEN sensitivity = 'I' THEN 1 ELSE 0 END ) AS intermediate,
            COUNT(sensitivity) AS total
        from (
            SELECT 
            antibiotic.antibiotic_name,
            (CASE presult_antibiotic.sensitivity
                WHEN 1 THEN 'S'
                WHEN 2 THEN 'R'
                WHEN 3 THEN 'I'
            END) AS sensitivity
            FROM 
            psample
            JOIN temp_all_patients patient ON patient.pid = psample.patient_id
            JOIN camlis_ptest_result AS presult ON presult.patient_sample_id = psample.\"ID\" AND presult.status = 1
            JOIN camlis_std_test_organism AS test_organism ON presult.result = cast(test_organism.\"ID\" as varchar)
            JOIN camlis_std_organism AS organism ON test_organism.organism_id = organism.\"ID\"    
            JOIN camlis_patient_sample_tests AS psample_tests ON psample_tests.\"ID\" = presult.patient_test_id  AND psample_tests.status = 1
            JOIN camlis_std_sample_test AS std_sample_test ON std_sample_test.\"ID\" = psample_tests.sample_test_id AND std_sample_test.status = TRUE
            JOIN camlis_std_department_sample AS dsample ON dsample.\"ID\" = std_sample_test.department_sample_id AND dsample.status = TRUE   
            JOIN camlis_std_sample AS std_sample ON std_sample.\"ID\" = dsample.sample_id AND std_sample.status = TRUE
            JOIN camlis_ptest_result_antibiotic AS presult_antibiotic ON presult.\"ID\" = presult_antibiotic.presult_id AND presult_antibiotic.status = TRUE
            JOIN camlis_std_antibiotic AS antibiotic ON presult_antibiotic.antibiotic_id = antibiotic.\"ID\"
            WHERE test_organism.organism_id IN (".$orgaism_id.") AND dsample.department_id = 4 AND dsample.sample_id = 6        
        ) sub_tbl
        group by antibiotic_name
        order by total DESC
        ";
        return $this->db->query($sql)->result_array();
    }
    /**
     * 4) Isolated Pathogens among Blood and CSF
     *  6: Blood Culture
     *  8: CSF
     */
    public function get_isolated_pathogens($sample_id, $startDateTime, $endDateTime, $laboratory){
        $receivedDateCond  = " AND concat( received_date,' ',received_time) >= '".$startDateTime."'";
        $receivedDateCond .= " AND concat( received_date,' ',received_time) <= '".$endDateTime."'";
        $this->create_temp_patients($laboratory, $receivedDateCond);
        $sql = "
        WITH psample AS (
            SELECT * FROM camlis_patient_sample 
            WHERE status = 1 AND \"labID\" = ANY(VALUES".$laboratory.") ".$receivedDateCond." 
        )
        SELECT 
        organism_name, 
        SUM(CASE when sample_id = 8 THEN 1 ELSE 0 END) AS csf,
        SUM(CASE when sample_id = 6 THEN 1 ELSE 0 END) AS blood
        from (
            SELECT 
            dsample.sample_id,
            organism.organism_name
            FROM psample
            JOIN temp_all_patients patient ON patient.pid = psample.patient_id
            JOIN camlis_ptest_result AS presult ON presult.patient_sample_id = psample.\"ID\" AND presult.status = 1 AND presult.contaminant NOT IN (1)
            JOIN camlis_std_test_organism AS test_organism ON presult.result = cast(test_organism.\"ID\" as varchar)
            JOIN camlis_std_organism AS organism ON test_organism.organism_id = organism.\"ID\"    
            JOIN camlis_patient_sample_tests AS psample_tests ON psample_tests.\"ID\" = presult.patient_test_id  AND psample_tests.status = 1
            JOIN camlis_std_sample_test AS std_sample_test ON std_sample_test.\"ID\" = psample_tests.sample_test_id AND std_sample_test.status = TRUE AND std_sample_test.test_id = 170
            JOIN camlis_std_department_sample AS dsample ON dsample.\"ID\" = std_sample_test.department_sample_id AND dsample.status = TRUE       
            WHERE dsample.department_id = 4 AND dsample.sample_id IN (".$sample_id.") AND test_organism.organism_id NOT IN (338 , 633, 339, 340, 341, 342, 343)
        ) sub_tbl
        group by sample_id, organism_name
        ";
        return $this->db->query($sql)->result_array();
    }
    /**
     * Bloodstream pathogen isolated
     * 338: no growth
     * 347: Non-fermenting gram negative rods
     * 342: No pathogens found
     * 349: Normal oral flora
     */
    // display as table
    public function get_bloodstream_pathogens_isolated($startDateTime, $endDateTime, $laboratory){
        $receivedDateCond  = " AND concat( received_date,' ',received_time) >= '".$startDateTime."'";
        $receivedDateCond .= " AND concat( received_date,' ',received_time) <= '".$endDateTime."'";
        $this->create_temp_patients($laboratory, $receivedDateCond);
        $sql = "
        WITH psample AS (
            SELECT * FROM camlis_patient_sample 
            WHERE status = 1 AND \"labID\" = ANY(VALUES".$laboratory.") ".$receivedDateCond." 
        )
        SELECT organism_name, month, month_n, year, count(organism_name) as total, organism_id
        FROM (
            SELECT
                to_char(psample.received_date,'Mon') AS month,
                to_char(psample.received_date,'mm') AS month_n,
                to_char(psample.received_date, 'YY') AS year,
                organism.organism_name,
                organism.\"ID\" as organism_id
            FROM psample
            JOIN temp_all_patients patient ON patient.pid = psample.patient_id
            JOIN camlis_ptest_result AS presult ON presult.patient_sample_id = psample.\"ID\" AND presult.status = 1 AND presult.contaminant NOT IN (1)
            JOIN camlis_std_test_organism AS test_organism ON presult.result = cast(test_organism.\"ID\" as varchar)
            JOIN camlis_std_organism AS organism ON test_organism.organism_id = organism.\"ID\"
            JOIN camlis_patient_sample_tests AS psample_tests ON psample_tests.\"ID\" = presult.patient_test_id  AND psample_tests.status = 1
            JOIN camlis_std_sample_test AS std_sample_test ON std_sample_test.\"ID\" = psample_tests.sample_test_id AND std_sample_test.status = TRUE
            JOIN camlis_std_department_sample AS dsample ON dsample.\"ID\" = std_sample_test.department_sample_id AND dsample.status = TRUE       
            JOIN camlis_patient_sample_detail as psample_detail ON psample_detail.patient_sample_id = psample.\"ID\"
            WHERE std_sample_test.test_id = 170 AND dsample.sample_id IN (6) AND psample_detail.sample_description IN ('39','40') AND organism.\"ID\" NOT IN (338,347,342,349)
        ) sub_tbl
        group by organism_name, month, year, month_n, organism_id
        order by month_n , year asc
        ";
        return $this->db->query($sql)->result_array();
    }
    
    // Display as graph
    public function get_bloodstream_pathogen_as_graph($startDateTime, $endDateTime, $laboratory){
        $receivedDateCond  = " AND concat( received_date,' ',received_time) >= '".$startDateTime."'";
        $receivedDateCond .= " AND concat( received_date,' ',received_time) <= '".$endDateTime."'";
        $this->create_temp_patients($laboratory, $receivedDateCond);
        $sql = "
        WITH psample AS (
            SELECT * FROM camlis_patient_sample 
            WHERE status = 1 AND \"labID\" = ANY(VALUES".$laboratory.") ".$receivedDateCond." 
        )
        SELECT organism_name, 
        SUM((CASE WHEN sample_description = '39' THEN 1 ELSE 0 END)) AS adult,
        SUM((CASE WHEN sample_description = '40' THEN 1 ELSE 0 END)) AS pediatric,
        count(organism_name) as value
        FROM (
            SELECT 
                organism.organism_name,    	
	            psample_detail.sample_description
            FROM psample
            JOIN temp_all_patients patient ON patient.pid = psample.patient_id 
            JOIN camlis_ptest_result AS presult ON presult.patient_sample_id = psample.\"ID\" AND presult.status = 1 AND presult.contaminant NOT IN (1)
            JOIN camlis_std_test_organism AS test_organism ON presult.result = cast(test_organism.\"ID\" as varchar)
            JOIN camlis_std_organism AS organism ON test_organism.organism_id = organism.\"ID\"    
            JOIN camlis_patient_sample_tests AS psample_tests ON psample_tests.\"ID\" = presult.patient_test_id  AND psample_tests.status = 1
            JOIN camlis_std_sample_test AS std_sample_test ON std_sample_test.\"ID\" = psample_tests.sample_test_id AND std_sample_test.status = TRUE
            JOIN camlis_std_department_sample AS dsample ON dsample.\"ID\" = std_sample_test.department_sample_id AND dsample.status = TRUE       
            JOIN camlis_patient_sample_detail as psample_detail ON psample_detail.patient_sample_id = psample.\"ID\"
            WHERE std_sample_test.test_id = 170 AND dsample.sample_id IN (6) AND psample_detail.sample_description IN ('39','40') AND organism.\"ID\" NOT IN (338,347,342,349)
        ) sub_tbl
        group by organism_name
        order by value desc
        ";
        return $this->db->query($sql)->result_array();
    }
    /**
     * Blood culture true pathogen rate and contamination rate by wards
     * 338 : No growth
     * 633:  No Malaria Parasite Seen
     * 339:  No organisms seen
     * 340: No parasites detected
     * 341: No parasites were found
     * 342: No pathogens found
     * 343: No significant growth found
     * 
     * Exclude Fake pathogen: 
     *      - 178: Coagulase Negative Staphylococcus
     *      - Bacillus
     *      
     *      - 132	"Bacillus cereus"
     *      - 133	"Bacillus sp."
     *      - 134	"Bacillus subtilis"
     *      - 449: Streptococcus viridans, alpha-hem. 
     *      - Corynebacterium
     *     
     *      - 182: "Corynebacterium jeikeium"
     *      - 183: "Corynebacterium sp."
     *      - 184: "Corynebacterium urealyticum"
     *      - Micrococcus
     *      - 314	"Micrococcus luteus"
    *       - 315	"Micrococcus sp."
    */
    // Split Get True pathogen by ward into 3 query 
    // Find patient and number of bottle

    public function get_patient_request_by_wards($startDateTime, $endDateTime, $laboratory){
        $receivedDateCond  = " AND concat( received_date,' ',received_time) >= '".$startDateTime."'";
        $receivedDateCond .= " AND concat( received_date,' ',received_time) <= '".$endDateTime."'";
        $this->create_temp_patients($laboratory, $receivedDateCond);

        $sql = "
        WITH psample AS (
            SELECT * FROM camlis_patient_sample 
            WHERE status = 1 AND \"labID\" = ANY(VALUES".$laboratory.") ".$receivedDateCond." 
        ), lab_sample_source AS (
            select * from camlis_lab_sample_source where lab_id = ANY(VALUES".$laboratory.") and status = true
        )
        SELECT 
            DISTINCT(source_name),
            \"ID\",
            COUNT( DISTINCT (pid)) as patient_request,
            SUM( volume1 + volume2 ) AS number_of_bottle
        FROM  
        (
        SELECT
            lab_sample_source.\"ID\",
            lab_sample_source.source_name,
            patient.pid,
            (CASE WHEN psample_detail.sample_volume1 is null THEN 0 WHEN psample_detail.sample_volume1 = 0 THEN 0 ELSE 1 END ) AS volume1,
            (CASE WHEN psample_detail.sample_volume2 is null THEN 0 WHEN psample_detail.sample_volume2 = 0 THEN 0 ELSE 1 END ) AS volume2       
        FROM psample 
        JOIN temp_all_patients patient ON patient.pid = psample.patient_id
        JOIN lab_sample_source ON lab_sample_source.\"ID\" = psample.sample_source_id 
        JOIN camlis_ptest_result AS presult ON presult.patient_sample_id = psample.\"ID\" AND presult.status = 1 
        JOIN camlis_std_test_organism AS test_organism ON presult.result = cast(test_organism.\"ID\" as varchar)
        JOIN camlis_patient_sample_tests as psample_tests ON psample_tests.\"ID\" = presult.patient_test_id AND psample_tests.status = 1
        JOIN camlis_patient_sample_detail AS psample_detail ON psample_detail.patient_sample_id = psample.\"ID\" AND psample_detail.status = TRUE 
        JOIN camlis_std_sample_test AS sample_test ON sample_test.\"ID\" = psample_tests.sample_test_id AND sample_test.status = true
        JOIN camlis_std_department_sample AS dsample ON dsample.\"ID\" = sample_test.department_sample_id AND dsample.department_id = 4 AND dsample.sample_id = 6
        WHERE sample_test.test_id = 170
        GROUP BY patient.pid , lab_sample_source.source_name , psample_detail.sample_volume1 , psample_detail.sample_volume2, lab_sample_source.\"ID\"
        ORDER BY lab_sample_source.source_name
        ) tbl 
        GROUP BY \"ID\", source_name
        order by patient_request desc
        ";
        return $this->db->query($sql)->result_array();
    }

    public function get_true_pathogen($startDateTime, $endDateTime, $laboratory){
        $receivedDateCond  = " AND concat( received_date,' ',received_time) >= '".$startDateTime."'";
        $receivedDateCond .= " AND concat( received_date,' ',received_time) <= '".$endDateTime."'";
        $this->create_temp_patients($laboratory, $receivedDateCond);

        $sql = "
        WITH psample AS (
            SELECT * FROM camlis_patient_sample 
            WHERE status = 1 AND \"labID\" = ANY(VALUES".$laboratory.") ".$receivedDateCond." 
        ), lab_sample_source AS (
            select * from camlis_lab_sample_source where lab_id = ANY(VALUES".$laboratory.") and status = true
        )
        SELECT        
            lab_sample_source.\"ID\",
            patient.pid,
            test_organism.organism_id,
            psample.collected_date
        FROM psample 
        JOIN temp_all_patients patient ON patient.pid = psample.patient_id
        JOIN lab_sample_source ON lab_sample_source.\"ID\" = psample.sample_source_id 
        JOIN camlis_ptest_result AS presult ON presult.patient_sample_id = psample.\"ID\" AND presult.status = 1 
        JOIN camlis_std_test_organism AS test_organism ON presult.result = cast(test_organism.\"ID\" as varchar)
        JOIN camlis_patient_sample_tests as psample_tests ON psample_tests.\"ID\" = presult.patient_test_id AND psample_tests.status = 1
        JOIN camlis_patient_sample_detail AS psample_detail ON psample_detail.patient_sample_id = psample.\"ID\" AND psample_detail.status = TRUE 
        JOIN camlis_std_sample_test AS sample_test ON sample_test.\"ID\" = psample_tests.sample_test_id AND sample_test.status = true
        JOIN camlis_std_department_sample AS dsample ON dsample.\"ID\" = sample_test.department_sample_id AND dsample.department_id = 4 AND dsample.sample_id = 6
        WHERE test_organism.organism_id NOT IN (338, 633, 339, 340, 341, 342, 343, 343, 178 , 132, 133, 134, 449, 182,183, 184, 314, 315) AND sample_test.test_id = 170
        GROUP BY patient.pid , test_organism.organism_id ,lab_sample_source.\"ID\", psample.collected_date
        ORDER BY patient.pid, test_organism.organism_id, psample.collected_date
        ";
        return $this->db->query($sql)->result_array();
        
    }
    // Blood culture contamination (bc_count)
    /**
     * specimen = Blood culture
     * choose “No growth”, “Coagulase negative staphylococcus”, “Corynebacterium”, “Streptococcus viridans, alpha-hem.”, “Micrococcus”, “Bacillus”
     * Contamination rate = (bc_cont/bc_bottle) * 100
     */

    public function get_contaminant_rate($startDateTime, $endDateTime, $laboratory){
        $receivedDateCond  = " AND concat( received_date,' ',received_time) >= '".$startDateTime."'";
        $receivedDateCond .= " AND concat( received_date,' ',received_time) <= '".$endDateTime."'";
        $this->create_temp_patients($laboratory, $receivedDateCond);

        $sql = "
        WITH psample AS (
            SELECT * FROM camlis_patient_sample 
            WHERE status = 1 AND \"labID\" = ANY(VALUES".$laboratory.") ".$receivedDateCond." 
        ), lab_sample_source AS (
            select * from camlis_lab_sample_source where lab_id = ANY(VALUES".$laboratory.") and status = true
        )
        SELECT 
            DISTINCT(source_name), 
            \"ID\",            
            COUNT(organism_id) as fake_organism
            FROM
            (
            SELECT
                lab_sample_source.\"ID\",
                lab_sample_source.source_name,
                patient.pid,
                test_organism.organism_id
            FROM psample 
            JOIN temp_all_patients patient ON patient.pid = psample.patient_id
            JOIN lab_sample_source ON lab_sample_source.\"ID\" = psample.sample_source_id 
            JOIN camlis_ptest_result AS presult ON presult.patient_sample_id = psample.\"ID\" AND presult.status = 1
            JOIN camlis_std_test_organism AS test_organism ON presult.result = cast(test_organism.\"ID\" as varchar)
            JOIN camlis_patient_sample_tests as psample_tests ON psample_tests.\"ID\" = presult.patient_test_id AND psample_tests.status = 1
            JOIN camlis_patient_sample_detail AS psample_detail ON psample_detail.patient_sample_id = psample.\"ID\" AND psample_detail.status = TRUE 
            JOIN camlis_std_sample_test AS sample_test ON sample_test.\"ID\" = psample_tests.sample_test_id AND sample_test.status = true
            JOIN camlis_std_department_sample AS dsample ON dsample.\"ID\" = sample_test.department_sample_id AND dsample.department_id = 4 AND dsample.sample_id = 6
            WHERE test_organism.organism_id IN (178 , 132, 133, 134, 449, 182, 183, 184, 314, 315) AND sample_test.test_id = 170
            GROUP BY patient.pid , lab_sample_source.source_name , test_organism.organism_id ,lab_sample_source.\"ID\"
            ORDER BY lab_sample_source.source_name
            ) tbl 
        GROUP BY source_name ,\"ID\"
        order by \"ID\" desc
        ";
        return $this->db->query($sql)->result_array();
    }
    /**
     * 
     */
    public function get_patient_sample_by_pathogen($organism_ids, $sample_id, $startDateTime, $endDateTime, $laboratory){
        $receivedDateCond  = " AND concat( received_date,' ',received_time) >= '".$startDateTime."'";
        $receivedDateCond .= " AND concat( received_date,' ',received_time) <= '".$endDateTime."'";
        $this->create_temp_patients($laboratory, $receivedDateCond);
        $sql = "
        WITH psample AS (
            SELECT * FROM camlis_patient_sample 
            WHERE status = 1 AND \"labID\" = ANY(VALUES".$laboratory.") ".$receivedDateCond." 
        )
        SELECT
            patient.pid,
            psample.\"ID\" as psample_id,
            test_organism.organism_id,
            psample.collected_date
        FROM psample
        JOIN temp_all_patients patient ON patient.pid = psample.patient_id 
        JOIN camlis_ptest_result AS presult ON presult.patient_sample_id = psample.\"ID\" AND presult.status = 1
        JOIN camlis_std_test_organism AS test_organism ON presult.result = cast(test_organism.\"ID\" as varchar)
        JOIN camlis_std_organism AS organism ON test_organism.organism_id = organism.\"ID\"    
        JOIN camlis_patient_sample_tests AS psample_tests ON psample_tests.\"ID\" = presult.patient_test_id  AND psample_tests.status = 1
        JOIN camlis_std_sample_test AS std_sample_test ON std_sample_test.\"ID\" = psample_tests.sample_test_id AND std_sample_test.status = TRUE
        JOIN camlis_std_department_sample AS dsample ON dsample.\"ID\" = std_sample_test.department_sample_id AND dsample.status = TRUE   
        JOIN camlis_std_sample AS std_sample ON std_sample.\"ID\" = dsample.sample_id AND std_sample.status = TRUE    
        WHERE test_organism.organism_id IN (".$organism_ids.") AND dsample.department_id = 4 AND dsample.sample_id IN (".$sample_id.")
        group by patient.pid, test_organism.organism_id, psample.collected_date , psample_id
        ORDER BY patient.pid, test_organism.organism_id, psample.collected_date
        ";
        return $this->db->query($sql)->result_array();
    }
    
    public function get_rejected_sample($startDateTime, $endDateTime, $laboratory){
        $receivedDateCond  = " AND concat( received_date,' ',received_time) >= '".$startDateTime."'";
        $receivedDateCond .= " AND concat( received_date,' ',received_time) <= '".$endDateTime."'";
        $sql = "
        WITH psample AS(
            SELECT sample_source_id, reject_comment, received_date, received_time, sample_number,\"ID\",\"labID\" 
            FROM camlis_patient_sample
            WHERE status = 1 AND \"labID\" = ANY(VALUES".$laboratory.") ".$receivedDateCond." 
        )
        SELECT rs.source_name as ward,
            rs.sample_name as specimen, 
            json_agg(rs.reject_comment) as reject_comment,
            count(rs.sample_number) as total 
        FROM (
            SELECT psample.sample_number, lss.source_name, ss.sample_name, rc.reject_comment 
            FROM psample
            LEFT JOIN camlis_result_comment rc on rc.patient_sample_id= psample.\"ID\" 
            JOIN camlis_patient_sample_tests pst on pst.patient_sample_id= psample.\"ID\" AND pst.is_rejected = True
            JOIN camlis_std_sample_test sst on sst.\"ID\"=pst.sample_test_id and sst.test_id = 170
            JOIN camlis_std_department_sample sds on sds.\"ID\" = sst.department_sample_id 
            JOIN camlis_std_sample ss on ss.\"ID\"=sds.sample_id 
            JOIN camlis_lab_sample_source lss on lss.\"ID\"= psample.sample_source_id 
            GROUP BY psample.sample_number, lss.source_name, ss.sample_name, rc.reject_comment 
        ) rs 
        GROUP BY rs.source_name,rs.sample_name
        ORDER BY rs.sample_name,rs.source_name
        ";
        return $this->db->query($sql)->result_array();
    }
}
