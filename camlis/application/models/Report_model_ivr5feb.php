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
	function aggregate_table($obj){
		 //var_dump( $this->session->userdata('laboratory')->labID);
/*		$con=" WHERE DATE_FORMAT(pv.`received_date`,'%Y-%m-%d') >= '".$obj->start."'
				AND DATE_FORMAT(pv.`received_date`,'%Y-%m-%d') <= '".$obj->end."'
				AND pv.received_time BETWEEN '".$obj->start_time."' AND '".$obj->end_time."'";*/
		$con=" WHERE DATE_FORMAT(concat(pv.`received_date`,' ',pv.`received_time`),'%Y-%m-%d %H:%i') >= '".$obj->start.' '.$obj->start_time."'
				AND DATE_FORMAT(concat(pv.`received_date`,' ',pv.`received_time`),'%Y-%m-%d %H:%i') <= '".$obj->end.' '.$obj->end_time."'";

		$sql = '	SELECT 	case when name_kh!="" then name_kh when  Title!="" then "Sub total" else "Grand total" end as distribute,
						Title, 
						name_kh,
						SUM(male) AS male,
						SUM(female) AS female,
						SUM(total) AS total
					FROM(
						SELECT 
							DISTINCT "1.0. Distribution by province of patient" AS Title,  
							name_kh,
							name_en,
							COUNT(IF(sex= "F",1,null)) AS female,
							COUNT(IF(sex= "M",1,null)) AS male,
							SUM(CASE WHEN sex = "F" THEN 1 WHEN sex = "M" THEN 1 ELSE 0 END) AS total
						FROM (
						
							SELECT  
								pr.name_kh,
								pr.name_en, 
								p.sex,
								p.patient_name,
								pv.received_date 
								
							FROM provinces pr 
							INNER JOIN patient_v p ON pr.code = p.province 
							inner join camlis_patient_sample pv on pv.patient_id = p.pid 
							
							'.$con.' 
							and pv."labID" = "'.$this->session->userdata('laboratory')->labID.'"
							GROUP BY p.pid,pv.sample_number,pv.`received_date`
						
						) AS tbl
						GROUP BY name_en
							
							
						UNION ALL
/* aging section */
						select 
							"2.0. Distribution by age group" as Title,
							name_kh, 
							name_en, 
							COUNT(IF(sex= "F",1,null)) AS female,
							COUNT(IF(sex= "M",1,null)) AS male,
							count(sex) AS total
							
						from (
						
							SELECT  
								p.pid,
								"1: 0 - 29 days" as name_kh,
								"1: 0 - 29 days" as name_en,
								p.sex,
								p.patient_name,
								pv.received_date 
								
							FROM patient_v p  
							inner join camlis_patient_sample pv on pv.patient_id = p.pid 
							
							'.$con.'  
							and pv."labID" = "'.$this->session->userdata('laboratory')->labID.'" 
							and p._day <= 30
							and p._month = 0
							and p._year = 0
							
							
							union all
							
							SELECT 
								p.pid, 
								"2: 1 - 11 m" as name_kh,
								"2: 1 - 11 m" as name_en,
								p.sex,
								p.patient_name,
								pv.received_date 
								
							FROM patient_v p  
							inner join camlis_patient_sample pv on pv.patient_id = p.pid 
							
							'.$con.'  
							and pv."labID" = "'.$this->session->userdata('laboratory')->labID.'"
							and p._month >=1 
							and p._month <= 12 
							and p._year = 0
							
							
							union all
							
							SELECT  
								p.pid,
								"3: 1 y - 4 y" as name_kh,
								"3: 1 y - 4 y" as name_en,
								p.sex,
								p.patient_name,
								pv.received_date 
								
							FROM patient_v p 
							inner join camlis_patient_sample pv on pv.patient_id = p.pid 
							
							'.$con.'  
							and pv."labID" = "'.$this->session->userdata('laboratory')->labID.'"
							and p._year >=1 
							and p._year <= 4
							
							
							union all
							
							SELECT 
								p.pid, 
								"4: 5 y - 14 y" as name_kh,
								"4: 5 y - 14 y" as name_en,
								p.sex,
								p.patient_name,
								pv.received_date 
								
							FROM patient_v p  
							inner join camlis_patient_sample pv on pv.patient_id = p.pid 
							
							'.$con.'  
							and pv."labID" = "'.$this->session->userdata('laboratory')->labID.'"
							and p._year >= 5 
							and p._year <= 14 
							
							
							union all
							
							SELECT 
								p.pid, 
								"5: 15 y - 24 y" as name_kh,
								"5: 15 y - 24 y" as name_en,
								p.sex,
								p.patient_name,
								pv.received_date 
								
							FROM patient_v p  
							inner join camlis_patient_sample pv on pv.patient_id = p.pid 
							
							'.$con.'  
							and pv."labID" = "'.$this->session->userdata('laboratory')->labID.'"
							and p._year >=15 
							and p._year <= 24 
							
							
							union all
							
							SELECT  
								p.pid,
								"6: 25 y - 49 y" as name_kh,
								"6: 25 y - 49 y" as name_en,
								p.sex,
								p.patient_name,
								pv.received_date 
								
							FROM patient_v p  
							inner join camlis_patient_sample pv on pv.patient_id = p.pid 
							
							'.$con.'  
							and pv."labID" = "'.$this->session->userdata('laboratory')->labID.'"
							and p._year >=25 
							and p._year <= 49
							
							
							union all
							
							SELECT  
								p.pid,
								"7: 50 y - 64 y" as name_kh,
								"7: 50 y - 64 y" as name_en,
								p.sex,
								p.patient_name,
								pv.received_date 
								
							FROM patient_v p  
							inner join camlis_patient_sample pv on pv.patient_id = p.pid 
							
							'.$con.'  
							and pv."labID" = "'.$this->session->userdata('laboratory')->labID.'"
							and p._year >=50 
							and p._year <= 64
							
							
							union all
							
							SELECT  
								p.pid,
								"8: >= 65 y" as name_kh,
								"8: >= 65 y" as name_en,
								p.sex,
								p.patient_name,
								pv.received_date 
								
							FROM patient_v p  
							inner join camlis_patient_sample pv on pv.patient_id = p.pid 
							
							'.$con.'  
							and pv."labID" = "'.$this->session->userdata('laboratory')->labID.'"
							AND p._year >=65 
							
							
						) as p 
						group by name_kh 
/* end aging */ 
						
						UNION ALL
						SELECT 
                            Title,
                            name_kh,
                            name_en,
                            COUNT(IF(sex= "F",1,null)) AS female,
	                        COUNT(IF(sex= "M",1,null)) AS male,
                            COUNT(total) as total
                        FROM (
                            SELECT 
                                "3.0. Distribution by Sample Source" as Title,
                                ss.source_name  as name_kh, 
                                ss.source_name  as name_en, 
                                p.sex,
                                COUNT(p.sex) AS total
                                
                            from patient_v p 
                            inner join camlis_patient_sample pv on pv.patient_id= p.pid  and pv.status=1
                            inner join camlis_lab_sample_source ss on ss.ID = pv.sample_source_id
                            '.$con.' 
                            and pv."labID" = "'.$this->session->userdata('laboratory')->labID.'"
                            group by ss.source_name,p.pid,pv.sample_number
						) dis3
                        group by name_en
						
						UNION ALL
/* 4.0. Distribution by paymement type */
						select 
							"4.0. Distribution by Payment type" as Title,
							spt.name as name_kh,
							spt.name as name_en,
							count(if(p.sex = "M",1,null)) as male,
							count(if(p.sex = "F",1,null)) as female ,
							count(p.sex) as total 
						
						from patient_v p
						inner join camlis_patient_sample pv on pv.patient_id = p.pid
						inner join camlis_lab_payment_type lpt on lpt.payment_type_id=pv.payment_type_id
						inner join camlis_std_payment_type spt on spt.id = lpt.payment_type_id
						'.$con.'
						and pv."labID" = "'.$this->session->userdata('laboratory')->labID.'"
						and lpt.lab_id="'.$this->session->userdata('laboratory')->labID.'"
						group by spt.name
						
						UNION ALL
/* 5.0. Distribution by Sample status */						
						select 
							"5.0. Distribution by Sample status" as Title,
							 name_kh, 
							 name_en, 
							 sum(female) as female,
							 sum(male) as male,
							 sum(total) as total
							
						from (
							
							select 
								"Research Sample" as name_kh, 
								"Research Sample" as name_en,
								count(if(p.sex = "M",1,null)) as male,
								count(if(p.sex = "F",1,null)) as female ,
								count(p.sex) as total 
							
							from patient_v p
							inner join camlis_patient_sample pv on pv.patient_id = p.pid
							'.$con.'
							and pv."labID" = "'.$this->session->userdata('laboratory')->labID.'"
							and pv.for_research = 1 
							
							UNION ALL
							
							select 
								"Reject Sample" as name_kh, 
								"Reject Sample" as name_en,
								count(if(p.sex = "M",1,null)) as male,
								count(if(p.sex = "F",1,null)) as female ,
								count(p.sex) as total 
							
							from patient_v p
							inner join camlis_patient_sample pv on pv.patient_id = p.pid
							'.$con.'
							and pv."labID" = "'.$this->session->userdata('laboratory')->labID.'"
							and pv.is_rejected = 1 
							
							UNION ALL
							
							select 
								"Urgent" as name_kh, 
								"Urgent" as name_en,
								count(if(p.sex = "M",1,null)) as male,
								count(if(p.sex = "F",1,null)) as female ,
								count(p.sex) as total 
							
							from patient_v p
							inner join camlis_patient_sample pv on pv.patient_id = p.pid
							'.$con.'
							
							and pv."labID" = "'.$this->session->userdata('laboratory')->labID.'"
							and pv.is_urgent = 1
						) as pv 
						group by name_kh
						
						UNION ALL   
						
						select 
							"6.0. Distirubtion by Sample Type" AS Title,
							sample_name as name_kh,
							"" as name_en,
							count(if(sex="F",1,Null)) as female,
							count(if(sex="M",1,Null)) as male,
							count(sex) as total 
						from(
							select 
								p.sex,
								ss.sample_name,
								p.patient_name,
								psd.department_sample_id
							from patient_v p
							inner join camlis_patient_sample pv on pv.patient_id = p.pid and p.status = TRUE
							inner join camlis_patient_sample_detail psd on psd.patient_sample_id = pv.ID 
							inner join camlis_std_department_sample sds on sds.ID = psd.department_sample_id
							
							inner join camlis_std_sample ss on ss.ID = sds.sample_id
							  
							'.$con.'
							and pv."labID" = "'.$this->session->userdata('laboratory')->labID.'" 
						) v
						group by patient_name,name_kh,department_sample_id
						  
						
						UNION ALL
					  
						
						select 
							concat("7.0. Distribution by "," ",d.department_name) as Title, 
							sst.group_result as name_kh,
							"" as name_en,
							count(if(p.sex="F",1,Null)) as female,
							count(if(p.sex="M",1,Null)) as male,
							count(p.sex) as total 
							 
						
						from  patient_v p
						inner join camlis_patient_sample pv on pv.patient_id = p.pid
						inner join camlis_patient_sample_tests pst on pst.patient_sample_id = pv.ID and pst.status = TRUE
						inner join camlis_std_sample_test sst on sst.ID = pst.sample_test_id 
						inner join camlis_std_test st on st.ID = sst.test_id 
						inner join camlis_std_department_sample ds on ds.ID= sst.department_sample_id 
						inner join camlis_std_department d on d.ID = ds.department_id
	  
						'.$con.'
						and pv."labID" = "'.$this->session->userdata('laboratory')->labID.'"
						and sst.group_result is not null
						group by d.department_name,sst.group_result

					)AS atbl
					
					GROUP BY Title,name_kh WITH ROLLUP';
					//"'.$this->session->userdata('laboratory')->labID.'"
				
				
			$result=$this->db->query($sql)->result_array(); 
            return $result;
				
				
	}
	
	/* this function using for get patient base the sample source count gender
	 * @parameters date start and date end
	 * group by sample source name and test name
	 * roll up the fields
	 */
	function ward_table($obj){ 
		
		$sql = 'SELECT 	case when name_kh!="" then name_kh when  Title!="" then "Sub total" else "Grand total" end as distribute,
						Title, 
						name_kh,
						SUM(male) AS male,
						SUM(female) AS female,
						SUM(total) AS total
					FROM(
						select 
							concat("Distribution by "," ",source_name) as Title, 
							group_result as name_kh,
							"" as name_en,
							count(if(sex="F",1,Null)) as female,
							count(if(sex="M",1,Null)) as male,
							count(sex) as total 
						from ( 
								select 
									p.pid,
									p.patient_name,
									p.sex,
									ss.source_name,
									t.test_name,
									ps.received_date,
									group_result
									 
								from patient_v p
								inner join camlis_patient_sample ps on ps.patient_id = p.pid 
								inner join camlis_lab_sample_source ss on ss.ID = ps.sample_source_id
								inner join camlis_patient_sample_tests pst on pst.patient_sample_id = ps.ID and pst.status = TRUE
								inner join camlis_std_sample_test st on st.ID = pst.sample_test_id
								inner join camlis_std_test t on t.ID = st.test_id
								
								where ps."labID" = "'.$this->session->userdata('laboratory')->labID.'"
								
								and date_format(ps.received_date,"%Y-%m-%d") >= ?
								and date_format(ps.received_date,"%Y-%m-%d") <= ?
								and ps.received_time BETWEEN ? AND ? 
								and st.group_result is not null
								
						) as v
						group by source_name,group_result

					)AS atbl
					
					GROUP BY Title,name_kh WITH ROLLUP';
				
				
			$result=$this->db->query($sql,array($obj->start,$obj->end, $obj->start_time, $obj->end_time))->result_array();
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
                inner join camlis_std_department_sample ds on ds.sample_id = ss.id
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
		$sql = " 
                 select 
					patient_id,
                    \"labID\",
                    laboname,
                    lab_code,
                    sample_number,
                    sex,
                    dob,
					patient_age,
                    department_name, 
                    sample_name,collected_date, 
                    test_date,
                    test_name,
                    results,
					diagnosis,
					description,
					sample_volume1,
					sample_volume2,
                    antibiotic_name,
                    result_comment,
                    /*-- Positive --*/
                    max(if(antibiotic_id=2,sensitivity,'')) as Amoxi_Clav,
                    max(if(antibiotic_id=10,sensitivity,'')) as Ceftriaxone,
                    max(if(antibiotic_id=12,sensitivity,'')) as Cephalothin,
                    max(if(antibiotic_id=13,sensitivity,'')) as Chloramphenicol,
                    max(if(antibiotic_id=15,sensitivity,'')) as Clindamycin,
                    max(if(antibiotic_id=16,sensitivity,'')) as Cloxacillin,
                    max(if(antibiotic_id=17,sensitivity,'')) as Erythromycin,
                    max(if(antibiotic_id=27,sensitivity,'')) as Nitrofurantoin,
                    max(if(antibiotic_id=28,sensitivity,'')) as Norfloxacin,
                    max(if(antibiotic_id=29,sensitivity,'')) as Oxacillin,
                    max(if(antibiotic_id=31,sensitivity,'')) as Penicillin,
                    max(if(antibiotic_id=32,sensitivity,'')) as Tetracycline,
                    max(if(antibiotic_id=33,sensitivity,'')) as Trimeth_Sulfa,
                    max(if(antibiotic_id=34,sensitivity,'')) as Vancomycin,
                    max(if(antibiotic_id='NA',sensitivity,'')) as Cefoxitin,
                    
                    /* negative */
                    max(if(antibiotic_id=4,sensitivity,'')) as Ampicillin,
                    max(if(antibiotic_id=1,sensitivity,'')) as Amikacin,
                    max(if(antibiotic_id=5,sensitivity,'')) as Azithromycin,
                    max(if(antibiotic_id=6,sensitivity,'')) as Cefazolin,
                    max(if(antibiotic_id=7,sensitivity,'')) as Cefepime,
                    max(if(antibiotic_id=9,sensitivity,'')) as Ceftazidime,
                    
                    max(if(antibiotic_id='NA',sensitivity,'')) as Ceftriaxone_30_GNB,
                    max(if(antibiotic_id='NA',sensitivity,'')) as Chloramphenicol_30,
                    max(if(antibiotic_id=14,sensitivity,'')) as Ciprofloxacin,
                    max(if(antibiotic_id=19,sensitivity,'')) as Fosfomycin,
                    max(if(antibiotic_id=20,sensitivity,'')) as Gentamicin,
                    max(if(antibiotic_id=21,sensitivity,'')) as Imipenem,
                    max(if(antibiotic_id=22,sensitivity,'')) as Levofloxacin,
                    max(if(antibiotic_id=23,sensitivity,'')) as Meropenem,
                    max(if(antibiotic_id=25,sensitivity,'')) as Minocycline,
                    max(if(antibiotic_id=26,sensitivity,'')) as Nalidixic_acid,
                    
                    max(if(antibiotic_id='NA',sensitivity,'')) as Norfloxacin_10_GNB,
                    max(if(antibiotic_id='NA',sensitivity,'')) as Trimeth_Sulfa_1_25  
                     
                from( select  
                        stv.patient_id,
						stv.\"labID\",
                        l.name_en as laboname,
                        l.name_kh,
                        l.lab_code,
                        stv.sample_number,
                        stv.sex,
                        stv.dob,
						TIMESTAMPDIFF(YEAR, stv.dob, stv.collected_date) AS patient_age,
                        stv.department_name, 
                        s.sample_name, 
                        date_format(stv.test_date,'%d-%m-%Y') as test_date,
                        stv.test_name,
                        pr.organism_name as results,
                        pr.quantity,  
                        date_format(stv.collected_date,'%d-%m-%Y') as collected_date,
                        stv.diagnosis,
						stv.description,
						stv.sample_volume1,
						stv.sample_volume2,
                        pr.antibiotic_id,
                        an.antibiotic_name, 
                        (case when `pr`.`sensitivity`=1 then 'S' 
                              when `pr`.`sensitivity`= 2 then 'R' else 'I' end) as sensitivity, 
                        stv.result_comment
                        
                        
                    from  camlis_laboratory l  
                    inner join patient_sample_test_v stv on stv.\"labID\" = l.\"labID\"
                    inner join camlis_std_department_sample ds on ds.\"ID\" = stv.department_sample_id 
                    inner join camlis_std_sample s on s.\"ID\" = ds.sample_id
                    inner join result_test_organism_v pr on pr.patient_test_id = stv.patient_sample_test_id
                    left join camlis_std_antibiotic an on an.\"ID\" = pr.antibiotic_id
                    where l.status = TRUE and ds.department_id=4 and stv.test_id=170
                    ".$con."   
				) as r  
                group by \"labID\",
                    laboname,
                    sample_number,
                    sex,
                    dob,
                    department_name, 
                    sample_name, 
                    test_date,
                    test_name,
                    results 
                order by laboname,results desc  
			";

		$result=$this->db->query($sql)->result_array();
        return $result;
	}

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
    public function get_raw_data($data) {
        $this->db->select("
            psample.\"labID\",
            psample.sample_number,
            DATE_FORMAT(psample.collected_date, '%d/%m/%Y') AS collected_date,
            DATE_FORMAT(psample.received_date, '%d/%m/%Y') AS received_date,
            CONCAT_WS(
                ', ',
                IF(psample.for_research = 1, 'Research Sample', NULL),
                IF(psample.is_rejected = 1, 'Rejected Sample', NULL),
                IF(psample.is_urgent = 1, 'Urgent Sample', NULL)
            ) AS sample_status
        ");
        $this->db->from('camlis_patient_sample AS psample');
        $this->db->where('psample.status', 1);

        //Fields and join table
        if (!empty($data['laboratory']['value']) || isset($data['laboratory']['is_show'])) {
            $this->db->select('lab.name_en AS laboratory_name');
            $this->db->join('camlis_laboratory AS lab', 'psample."labID" = lab."labID"', 'inner');
        }
        if (isset($data['sample_source']['is_show'])) {
            $this->db->select('sample_source.source_name AS sample_source');
            $this->db->join('camlis_lab_sample_source AS sample_source', 'psample.sample_source_id = sample_source.ID AND psample."labID" = sample_source.lab_id AND sample_source.status = TRUE', 'inner');
        }
        if (isset($data['requester']['is_show'])) {
            $this->db->select('requester.requester_name AS requester');
            $this->db->join('camlis_lab_requester AS requester', 'psample.requester_id = requester.ID AND psample."labID" = requester.lab_id', 'inner');
        }
        if (isset($data['payment_type']['is_show'])) {
            $this->db->select('payment_type.name AS payment_type');
            $this->db->join('camlis_std_payment_type AS payment_type', 'psample.payment_type_id = payment_type.id', 'inner');
        }
        if (!empty($data['patient_code']['value']) || isset($data['patient_code']['is_show']) ||
            !empty($data['patient_name']['value']) || isset($data['patient_name']['is_show']) ||
            !empty($data['patient_age']['min']) || !empty($data['patient_age']['max']) || isset($data['patient_age']['is_show']) ||
            !empty($data['patient_gender']['value']) || isset($data['patient_gender']['is_show']) ||
            !empty($data['province']['value']) || isset($data['province']['is_show']) ||
            !empty($data['district']['value']) || isset($data['district']['is_show']) ) {

            $this->db->select("
                patient.patient_code,
                patient.patient_name,
                TIMESTAMPDIFF(YEAR, patient.dob, psample.collected_date) AS patient_age,
                (CASE patient.sex WHEN 1 THEN 'M' WHEN 2 THEN 'F' ELSE '' END) AS patient_gender
            ");
            $this->db->join('v_camlis_all_patients AS patient', 'psample.patient_id = patient.pid AND psample."labID" = patient.lab_id', 'inner');
        }
        if (!empty($data['province']['value']) || isset($data['province']['is_show'])) {
            $this->db->select('province.name_en AS province');
            $this->db->join('provinces AS province', 'patient.province = province.code AND province.status = TRUE', 'left');
        }
        if (!empty($data['district']['value']) || isset($data['district']['is_show'])) {
            $this->db->select('district.name_en AS district');
            $this->db->join('districts AS district', 'patient.district = district.code AND district.status = TRUE', 'left');
        }
        if (!empty($data['commune']['value']) || isset($data['commune']['is_show'])) {
            $this->db->select('commune.name_en AS commune');
            $this->db->join('communes AS commune', 'patient.commune = commune.code AND commune.status = TRUE', 'left');
        }
        if (!empty($data['village']['value']) || isset($data['village']['is_show'])) {
            $this->db->select('village.name_en AS village');
            $this->db->join('villages AS village', 'patient.village = village.code AND village.status = TRUE', 'left');
        }

        $has_department = !empty($data['department']['value']) || isset($data['department']['is_show']);
        $has_sample_type = !empty($data['sample_type']['value']) || isset($data['sample_type']['is_show']);
        $has_sample_description = !empty($data['sample_description']['value']) || isset($data['sample_description']['is_show']);
        $has_test = isset($data['test']) && (!empty($data['test']['value']) || isset($data['test']['is_show']));
        if ($has_department || $has_sample_type || $has_sample_description || $has_test) {
            $this->db->select('std_sampletest.group_result AS test');
            $this->db->join('camlis_patient_sample_tests AS psample_test', 'psample.ID = psample_test.patient_sample_id AND psample_test.status = TRUE', 'left');
            $this->db->join('camlis_std_sample_test AS std_sampletest', 'psample_test.sample_test_id = std_sampletest.ID AND std_sampletest.group_result IS NOT NULL', 'inner');
            $this->db->join('camlis_std_department_sample AS department_sample', 'std_sampletest.department_sample_id = department_sample.ID AND department_sample.status = TRUE', 'inner');
            $this->db->join('camlis_std_test AS test', 'std_sampletest.test_id = test.ID', 'inner');

            if ($has_department) {
                $this->db->select('department.department_name AS department');
                $this->db->join('camlis_std_department AS department', 'department_sample.department_id = department.ID AND department.status = TRUE', 'inner');
            }
            if ($has_sample_type) {
                $this->db->select('sample_type.sample_name AS sample_type');
                $this->db->join('camlis_std_sample AS sample_type', 'department_sample.sample_id = sample_type.ID AND sample_type.status = TRUE', 'inner');
            }

            if ($has_sample_description) {
                $this->db->select('sample_description.description AS sample_description');
                $this->db->join('camlis_patient_sample_detail AS psample_detail', 'psample_detail.patient_sample_id = psample.ID AND department_sample.ID = psample_detail.department_sample_id AND psample_detail.status = TRUE', 'left');
                $this->db->join('camlis_std_sample_description AS sample_description', 'psample_detail.sample_description = sample_description.ID', 'left');
            }
        }
        $has_test_date = !empty($data['test_date']['min']) || !empty($data['test_date']['max']) || isset($data['test_date']['is_show']);
        $has_organism = !empty($data['result_organism']['value']) || isset($data['result_organism']['is_show']);
        $has_antibiotic = $has_antibiotic = !empty($data['antibiotic']['value']) || isset($data['antibiotic']['is_show']);
        $has_sensitivity = !empty($data['sensitivity']['value']) || isset($data['sensitivity']['is_show']);
        if ($has_test_date || $has_organism || $has_antibiotic || $has_sensitivity ) {
            $this->db->select("DATE_FORMAT(ptest_result.test_date, '%d/%m/%Y') AS test_date");
            $this->db->join('camlis_ptest_result AS ptest_result', 'psample_test.ID = ptest_result.patient_test_id AND ptest_result.patient_sample_id = psample.ID AND ptest_result.status = TRUE', 'left');
            $this->db->join('camlis_std_test_organism AS test_organism', 'ptest_result.`type` = 1 AND ptest_result.result = test_organism.ID', 'left');

            if ($has_organism) {
                $this->db->select("IF(ptest_result.type = 1, CONCAT(organism.organism_name, CASE organism.organism_value WHEN 1 THEN ' Positive' WHEN 2 THEN ' Negative' ELSE '' END), NULL) AS result_organism");
                $this->db->join('camlis_std_organism AS organism', 'test_organism.organism_id = organism.ID', 'left');
            }

            if ($has_antibiotic || $has_sensitivity) {
                $this->db->select("
                    antibiotic.antibiotic_name AS antibiotic,
                    (CASE presult_antibiotic.sensitivity
                        WHEN 1 THEN 'Sensitive'
                        WHEN 2 THEN 'Resistant'
                        WHEN 3 THEN 'Intermediate'
                    END) AS sensitivity
                 ");
                $this->db->join('camlis_ptest_result_antibiotic AS presult_antibiotic', 'ptest_result.ID = presult_antibiotic.presult_id', 'left');
                $this->db->join('camlis_std_antibiotic AS antibiotic', 'presult_antibiotic.antibiotic_id = antibiotic.ID', 'left');
            }
        }

        //Condition
		//Laboratory ID
        if (isset($data['laboratory']['value']) && count($data['laboratory']['value']) > 0) {
            $this->db->where_in('psample."labID"', $data['laboratory']['value']);
        }
        //Patient ID
        if (isset($data['patient_code']['value']) && !empty($data['patient_code']['value'])) {
            $this->db->where('patient.patient_code', $data['patient_code']['value']);
        }
        //Patient Name
        if (isset($data['patient_name']['value']) && !empty($data['patient_name']['value'])) {
            $this->db->where("patient.patient_name", $data['patient_name']['value']);
        }
        //Patient Age
        if (isset($data['patient_age']) && (!empty($data['patient_age']['min']) || !empty($data['patient_age']['max']))) {
            $min_age = empty($data['patient_age']['min']) ? PHP_INT_MIN : $data['patient_age']['min'];
            $max_age = empty($data['patient_age']['max']) ? PHP_INT_MAX : $data['patient_age']['max'];
            $this->db->where("TIMESTAMPDIFF(YEAR, patient.dob, psample.collected_date) BETWEEN $min_age AND $max_age", NULL, FALSE);
        }
        //Patient Gender
        if (isset($data['patient_gender']['value']) && !empty($data['patient_gender']['value'])) {
            $this->db->where("patient.sex", $data['patient_gender']['value']);
        }
        //Patient Province
        if (isset($data['province']['value']) && count($data['province']['value']) > 0) {
            $this->db->where_in('patient.province', $data['province']['value']);
        }
        //Patient District
        if (isset($data['district']['value']) && count($data['district']['value']) > 0) {
            $this->db->where_in('patient.district', $data['district']['value']);
        }
        //Department
        if (isset($data['department']['value']) && count($data['department']['value']) > 0) {
            $this->db->where_in('department.ID', $data['department']['value']);
        }
        //Sample Type
        if (isset($data['sample_type']['value']) && count($data['sample_type']['value']) > 0) {
            $this->db->where_in('sample_type.ID', $data['sample_type']['value']);
        }
        //Sample Description
        if (isset($data['sample_description']['value']) && count($data['sample_description']['value']) > 0) {
            $this->db->where('sample_description.ID', $data['sample_description']['value']);
        }
        //Sample Number
        if (isset($data['sample_number']['value']) && !empty($data['sample_number']['value'])) {
            $this->db->where('psample.sample_number', $data['sample_number']['value']);
        }
        //Collected Date
        if (isset($data['collected_date']) && !empty($data['collected_date']['min']) || !empty($data['collected_date']['max'])) {
            $min = DateTime::createFromFormat('d/m/Y', $data['collected_date']['min']);
            $max = DateTime::createFromFormat('d/m/Y', $data['collected_date']['max']);
            $min = $min ? $min->format('Y-m-d') : '';
            $max = $max ? $max->format('Y-m-d') : '';
            $this->db->where("psample.collected_date BETWEEN '$min' AND '$max'", NULL, FALSE);
        }
        //Received Date
        if (isset($data['received_date']) && !empty($data['received_date']['min']) || !empty($data['received_date']['max'])) {
            $min = DateTime::createFromFormat('d/m/Y', $data['received_date']['min']);
            $max = DateTime::createFromFormat('d/m/Y', $data['received_date']['max']);
            $min = $min ? $min->format('Y-m-d') : '';
            $max = $max ? $max->format('Y-m-d') : '';
            $this->db->where("psample.received_date BETWEEN '$min' AND '$max'", NULL, FALSE);
        }
        //Sample Source
        if (isset($data['sample_source']['value']) && count($data['sample_source']['value']) > 0) {
            $this->db->where_in('psample.sample_source_id', $data['sample_source']['value']);
        }
        //Requester
        if (isset($data['requester']['value']) && count($data['requester']['value']) > 0) {
            $this->db->where_in('psample.requester_id', $data['requester']['value']);
        }
        //Test Date
        if (isset($data['test_date']) && !empty($data['test_date']['min']) || !empty($data['test_date']['max'])) {
            $min = DateTime::createFromFormat('d/m/Y', $data['test_date']['min']);
            $max = DateTime::createFromFormat('d/m/Y', $data['test_date']['max']);
            $min = $min ? $min->format('Y-m-d') : '';
            $max = $max ? $max->format('Y-m-d') : '';
            $this->db->where("ptest_result.test_date BETWEEN '$min' AND '$max'", NULL, FALSE);
        }
        //Test Name/group result
        if (isset($data['test']['value']) && count($data['test']['value']) > 0) {
            $this->db->where_in('std_sampletest.group_result', $data['test']['value']);
        }
        //Organism
        if (isset($data['result_organism']['value']) && count($data['result_organism']['value']) > 0) {
            $this->db->where_in('organism.ID', $data['result_organism']['value']);
        }
        //Antibiotic
        if (isset($data['antibiotic']['value']) && count($data['antibiotic']['value']) > 0) {
            $this->db->where_in('antibiotic.ID', $data['antibiotic']['value']);
        }
        //Sensitivity
        if (isset($data['sensitivity']['value']) && !empty($data['sensitivity']['value'])) {
            $this->db->where('presult_antibiotic.sensitivity', $data['sensitivity']['value']);
        }
        //Sample Status
        $has_is_rejected_value = isset($data['is_rejected']['value']) && !empty($data['is_rejected']['value']);
        $has_for_research_value = isset($data['for_research']['value']) && !empty($data['for_research']['value']);
        $has_is_urgent_value = isset($data['is_urgent']['value']) && !empty($data['is_urgent']['value']);
        if ($has_is_rejected_value || $has_for_research_value || $has_is_urgent_value) $this->db->group_start();
        if ($has_is_rejected_value) {
            $this->db->where('psample.is_rejected', $data['is_rejected']['value']);
        }
        if ($has_for_research_value) {
            $this->db->where('psample.for_research', $data['for_research']['value']);
        }
        if ($has_is_urgent_value) {
            $this->db->where('psample.is_urgent', $data['is_urgent']['value']);
        }
        if ($has_is_rejected_value || $has_for_research_value || $has_is_urgent_value) $this->db->group_end();

        //Payment type
        if (isset($data['payment_type']['value']) && !empty($data['payment_type']['value'])) {
            $this->db->where('psample.payment_type_id', $data['payment_type']['value']);
        }
        
        //GroupBy
        $this->db->group_by('psample.ID');
        if (isset($data['antibiotic']['is_show'])) {
            $this->db->group_by('presult_antibiotic.ID');
        }
        if (isset($data['organism']['is_show'])) {
            $this->db->group_by('ptest_result.ID');
        }
        if (isset($data['test']['is_show'])) {
            $this->db->group_by('std_sampletest.group_result');
        }
        if (isset($data['sample_type']['is_show'])) {
            $this->db->group_by('sample_type.ID');
        }
        if (isset($data['department']['is_show'])) {
            $this->db->group_by('department.ID');
        }

        return $this->db->get()->result_array();
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
        $this->db->select('
            CONCAT(psample.collected_date, " ", psample.collected_time) AS collected_date,
            CONCAT(psample.received_date, " ", psample.received_time) AS received_date,
            IF(psample.is_urgent = 1, "URGENT", "ROUTINE") AS type,
            psample.printedDate,
			dep.ID AS department_id,
			dep.department_name,
			sample_test.group_result,
			ptest.patient_sample_id,
			_t.test_count
		');
        $this->db->from('camlis_patient_sample_tests AS ptest');
        $this->db->join('camlis_patient_sample AS psample', 'ptest.patient_sample_id = psample.ID', 'inner');
        $this->db->join('camlis_std_sample_test AS sample_test', 'ptest.sample_test_id = sample_test.ID', 'inner');
        $this->db->join('camlis_std_test AS test', 'sample_test.test_id = test.ID', 'inner');
        $this->db->join('camlis_std_department_sample AS dsample', 'dsample.ID = sample_test.department_sample_id', 'inner');
        $this->db->join('camlis_std_department AS dep', 'dep.ID = dsample.department_id', 'inner');
        $this->db->join('
            (SELECT ptest.patient_sample_id,
			        COUNT(DISTINCT sample_test.group_result) AS test_count
            FROM camlis_patient_sample_tests AS ptest
            INNER JOIN camlis_std_sample_test AS sample_test ON ptest.sample_test_id = sample_test.ID
            WHERE ptest.status = TRUE
                  AND LENGTH(sample_test.group_result) > 0
            GROUP BY ptest.patient_sample_id) _t
        ', 'psample.ID = _t.patient_sample_id', 'inner');
        $this->db->where('ptest.status', 1);
        $this->db->where('psample.status', 1);
        $this->db->where('psample."labID"', $this->laboratory_id);
        $this->db->where('psample.received_date >=', $start_date);
        $this->db->where('psample.received_date <=', $end_date);
        $this->db->where('LENGTH(sample_test.group_result) >', 0);
        $this->db->where('psample.is_printed', 1);
        $this->db->order_by('dep.order', 'asc');
        $this->db->order_by('sample_test.order', 'asc');
        $this->db->group_by('psample.ID');
        $this->db->group_by('sample_test.group_result');

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
                SUM(IF(_t.sex = 1, 1, 0)) AS male,
                SUM(IF(_t.sex = 2, 1, 0)) AS female
            FROM 
            (
            SELECT 
                patient.sex,
                (CASE
                    WHEN TIMESTAMPDIFF(DAY, patient.dob, psample.collected_date) <= 29 THEN '0 - 29 days'
                    WHEN TIMESTAMPDIFF(MONTH, patient.dob, psample.collected_date) BETWEEN 1 AND 11 THEN '1 - 11 months'
                    WHEN TIMESTAMPDIFF(YEAR, patient.dob, psample.collected_date) BETWEEN 1 AND 4 THEN '1 - 4 years'
                    WHEN TIMESTAMPDIFF(YEAR, patient.dob, psample.collected_date) BETWEEN 5 AND 14 THEN '5 - 14 years'
                    WHEN TIMESTAMPDIFF(YEAR, patient.dob, psample.collected_date) BETWEEN 15 AND 24 THEN '15 - 24 years'
                    WHEN TIMESTAMPDIFF(YEAR, patient.dob, psample.collected_date) BETWEEN 25 AND 49 THEN '25 - 49 years'
                    WHEN TIMESTAMPDIFF(YEAR, patient.dob, psample.collected_date) BETWEEN 50 AND 64 THEN '50 - 64 years'
                    ELSE '>= 65 years'	
                END) AS age_group
            FROM camlis_patient_sample AS psample
            INNER JOIN v_camlis_all_patients AS patient ON psample.patient_id = patient.pid AND psample.\"labID\" = patient.lab_id
            WHERE psample.status = TRUE AND psample.received_date BETWEEN ? AND ? $extraWhere
            ) _t
            WHERE _t.age_group IS NOT NULL
            GROUP BY _t.age_group";

        return $this->db->query($sql, [$start_date, $end_date])->result_array();
    }

    public function patient_by_sample_source($start_date, $end_date, $laboratory_id) {
        $extraWhere = count($laboratory_id) > 0 ? "AND psample.\"labID\" IN (".implode(',', $laboratory_id).")" : "";
        $sql = "SELECT source.source_name,
                       SUM(IF(patient.sex = 1, 1, 0)) AS male,
                       SUM(IF(patient.sex = 2, 1, 0)) AS female
                FROM camlis_patient_sample AS psample
                INNER JOIN camlis_lab_sample_source AS source ON psample.sample_source_id = source.ID
                INNER JOIN v_camlis_all_patients AS patient ON psample.patient_id = patient.pid AND psample.\"labID\" = patient.lab_id
                WHERE psample.status = TRUE
                      AND psample.received_date BETWEEN ? AND ?
                      $extraWhere
                GROUP BY source.ID";
        return $this->db->query($sql, [$start_date, $end_date])->result_array();
    }

    public function patient_by_sample_type($start_date, $end_date, $laboratory_id) {
        $extraWhere = count($laboratory_id) > 0 ? "AND psample.\"labID\" IN (".implode(',', $laboratory_id).")" : "";
        $sql = "SELECT _t.sample_name,
                       SUM(IF(_t.sex = 1, 1, 0)) AS male,
                       SUM(IF(_t.sex = 2, 1, 0)) AS female
                FROM
                (SELECT sample.ID AS sample_id,
                        sample.sample_name,
                        patient.sex
                FROM camlis_patient_sample AS psample
                INNER JOIN v_camlis_all_patients AS patient ON psample.patient_id = patient.pid AND psample.\"labID\" = patient.lab_id
                INNER JOIN camlis_patient_sample_tests AS ptest ON psample.ID = ptest.patient_sample_id
                INNER JOIN camlis_std_sample_test AS sample_test ON ptest.sample_test_id = sample_test.ID
                INNER JOIN camlis_std_department_sample AS dsample ON sample_test.department_sample_id = dsample.ID
                INNER JOIN camlis_std_sample AS sample ON dsample.sample_id = sample.ID
                WHERE psample.status = TRUE
                            AND ptest.status = TRUE
                            AND psample.received_date BETWEEN ? AND ?
                            $extraWhere
                GROUP BY psample.ID, sample.ID
                ) _t
                GROUP BY _t.sample_id";
        return $this->db->query($sql, [$start_date, $end_date])->result_array();
    }

    public function patient_by_department($start_date, $end_date, $laboratory_id) {
        $extraWhere = count($laboratory_id) > 0 ? "AND psample.\"labID\" IN (".implode(',', $laboratory_id).")" : "";
        $sql = "SELECT _t.department_name,
                       SUM(IF(_t.sex = 1, 1, 0)) AS male,
                       SUM(IF(_t.sex = 2, 1, 0)) AS female
                FROM
                (SELECT department.ID AS department_id,
                        department.department_name,
                        patient.sex
                FROM camlis_patient_sample AS psample
                INNER JOIN v_camlis_all_patients AS patient ON psample.patient_id = patient.pid AND psample.\"labID\" = patient.lab_id
                INNER JOIN camlis_patient_sample_tests AS ptest ON psample.ID = ptest.patient_sample_id
                INNER JOIN camlis_std_sample_test AS sample_test ON ptest.sample_test_id = sample_test.ID
                INNER JOIN camlis_std_department_sample AS dsample ON sample_test.department_sample_id = dsample.ID
                INNER JOIN camlis_std_department AS department ON dsample.department_id = department.ID
                WHERE psample.status = TRUE
                      AND ptest.status = TRUE
                      AND psample.received_date BETWEEN ? AND ?
                      $extraWhere
                GROUP BY psample.ID, department.ID
                ) _t
                GROUP BY _t.department_id";
        return $this->db->query($sql, [$start_date, $end_date])->result_array();
    }

    public function patient_by_month($start_date, $end_date, $laboratory_id) {
        $extraWhere = count($laboratory_id) > 0 ? "AND psample.\"labID\" IN (".implode(',', $laboratory_id).")" : "";
        $sql = "SELECT MONTHNAME(psample.received_date) AS month,
                       YEAR(psample.received_date) AS year,
                       SUM(IF(patient.sex = 1, 1, 0)) AS male,
                       SUM(IF(patient.sex = 2, 1, 0)) AS female
                FROM camlis_patient_sample AS psample
                INNER JOIN v_camlis_all_patients AS patient ON psample.patient_id = patient.pid AND psample.\"labID\" = patient.lab_id
                WHERE psample.status = TRUE
                      AND psample.received_date BETWEEN ? AND ?
                      $extraWhere
                GROUP BY MONTH(psample.received_date), YEAR(psample.received_date)";
        return $this->db->query($sql, [$start_date, $end_date])->result_array();
    }

    public function sample_type_by_month($sample_type_id, $start_date, $end_date, $laboratory_id) {
        $extraWhere = count($laboratory_id) > 0 ? "AND psample.\"labID\" IN (".implode(',', $laboratory_id).")" : "";
        $sql = "SELECT _t.month,
                       _t.year,
                       SUM(_t.count) AS count,
                       _t.sample_id
                FROM(
                SELECT MONTHNAME(psample.received_date) AS month,
                       YEAR(psample.received_date) AS year,
                       COUNT(DISTINCT dsample.sample_id) As count,
                       dsample.sample_id
                FROM camlis_patient_sample AS psample
                INNER JOIN v_camlis_all_patients AS patient ON psample.patient_id = patient.pid AND psample.\"labID\" = patient.lab_id
                INNER JOIN camlis_patient_sample_tests AS ptest ON psample.ID = ptest.patient_sample_id
                INNER JOIN camlis_std_sample_test AS sample_test ON ptest.sample_test_id = sample_test.ID
                INNER JOIN camlis_std_department_sample AS dsample ON sample_test.department_sample_id = dsample.ID
                WHERE psample.status = TRUE
                      AND ptest.status = TRUE
                      AND psample.received_date BETWEEN ? AND ?
                      AND dsample.sample_id = ?
                      $extraWhere
                GROUP BY psample.ID, dsample.ID
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
                SELECT MONTHNAME(psample.received_date) AS month,
                       YEAR(psample.received_date) AS year,
                       sample_test.group_result
                FROM camlis_patient_sample AS psample
                INNER JOIN v_camlis_all_patients AS patient ON psample.patient_id = patient.pid AND psample.\"labID\" = patient.lab_id
                INNER JOIN camlis_patient_sample_tests AS ptest ON psample.ID = ptest.patient_sample_id
                INNER JOIN camlis_std_sample_test AS sample_test ON ptest.sample_test_id = sample_test.ID
                WHERE psample.status = TRUE
                      AND ptest.status = TRUE
                      AND psample.received_date BETWEEN ? AND ?
                      AND sample_test.group_result = ?
                      $extraWhere
                GROUP BY MONTH(psample.received_date), YEAR(psample.received_date), psample.ID, sample_test.group_result) _t
                GROUP BY _t.month, _t.year";
        return $this->db->query($sql, [$start_date, $end_date, $group_result])->result_array();
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
                    AND psample.status = TRUE
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
                                ON ptest.ID = presult.patient_test_id
                                AND presult.status = TRUE
                                AND presult.type = 1";
            if (count($possible_result_id) > 0) $joinResultQuery .= " AND presult.result IN (".implode(',', $possible_result_id).")";
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
                        ON psample.ID = ptest.patient_sample_id
                        AND psample.status = TRUE
                        AND psample.received_date BETWEEN ? AND ?
                        $sample_test_condition
                        $lab_condition
                    INNER JOIN camlis_std_sample_test AS sample_test ON ptest.sample_test_id = sample_test.ID $department_sample_condition
                    $joinResultQuery
                    RIGHT JOIN provinces AS province ON patient.province = province.code
                    WHERE province.code != 25
                    GROUP BY psample.ID, province.code, ptest.sample_test_id
                ) _t
                GROUP BY _t.province_code";
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
                    ON psample.ID = ptest.patient_sample_id
                    AND psample.status = TRUE
                    AND psample.received_date BETWEEN ? AND ?
                    $extraWhere
                INNER JOIN camlis_std_sample_test AS sample_test 
                    ON ptest.sample_test_id = sample_test.ID
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
                    ON psample.ID = ptest.patient_sample_id
                    AND psample.status = TRUE
                    AND psample.received_date BETWEEN ? AND ?
                    $extraWhere
                INNER JOIN camlis_std_sample_test AS sample_test 
                    ON ptest.sample_test_id = sample_test.ID
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
                    ON psample.ID = ptest.patient_sample_id
                    AND psample.status = TRUE
                    AND psample.received_date BETWEEN ? AND ?
                    $extraWhere
                INNER JOIN camlis_std_sample_test AS sample_test 
                    ON ptest.sample_test_id = sample_test.ID
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
                                ON ptest.ID = presult.patient_test_id
                                AND presult.status = TRUE
                                AND presult.type = 1";
            if (count($possible_result_id) > 0) $joinResultQuery .= " AND presult.result IN (".implode(',', $possible_result_id).")";
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
                        ON psample.ID = ptest.patient_sample_id
                        AND psample.status = TRUE
                        AND psample.received_date BETWEEN ? AND ?
                        $sample_test_condition
                    INNER JOIN camlis_std_sample_test AS sample_test ON ptest.sample_test_id = sample_test.ID $department_sample_condition
                    $joinResultQuery
                    RIGHT JOIN camlis_laboratory AS lab ON psample.\"labID\" = lab.\"labID\"
                    WHERE lab.status = TRUE $lab_condition
                    GROUP BY psample.ID, lab.\"labID\", ptest.sample_test_id
                ) _t
                GROUP BY _t.lab_id";
        return $this->db->query($sql, [$start_date, $end_date])->result_array();
    }

    /**
     * Get financial report
     * @param $start_date
     * @param $end_date
     * @return mixed
     */
    public function get_financial_report($start_date, $end_date) {
        $sql = "SELECT department.ID AS department_id,
                       department.department_name,
                       psample.payment_type_id,
                       payment_type.name AS payment_type_name,
                       ptest_payment.group_result,
                       ptest_payment.price,
                       ptest_payment.patient_sample_id
                FROM camlis_patient_sample AS psample
                INNER JOIN camlis_patient_sample_test_payment AS ptest_payment ON psample.ID = ptest_payment.patient_sample_id
                INNER JOIN camlis_std_payment_type AS payment_type ON psample.payment_type_id = payment_type.id
                INNER JOIN camlis_patient_sample_tests AS ptest ON psample.ID = ptest.patient_sample_id
                INNER JOIN camlis_std_sample_test AS sample_test ON ptest.sample_test_id = sample_test.ID AND ptest_payment.group_result = sample_test.group_result
                INNER JOIN camlis_std_department_sample AS department_sample ON sample_test.department_sample_id = department_sample.ID
                INNER JOIN camlis_std_department AS department ON department_sample.department_id = department.ID
                WHERE psample.status = TRUE
                      AND ptest.status = TRUE
                      AND psample.\"labID\" = ?
                      AND DATE_FORMAT(CONCAT(psample.received_date, ' ', psample.received_time), '%Y-%m-%d %H:%i') BETWEEN ? AND ?
                GROUP BY psample.ID, ptest_payment.group_result
                ORDER BY department.order";

        return $this->db->query($sql, [$this->laboratory_id, $start_date, $end_date])->result_array();
    }
}