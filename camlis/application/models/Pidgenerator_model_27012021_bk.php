<?php
defined('BASEPATH') OR die('Permission denied!');
class Pidgenerator_model extends MY_Model {
	public function __construct() {
		parent::__construct();		
	}
	public function getNumber($date){
		$this->db->select('count(*) as number');
		$this->db->where("to_char(\"entryDate\" , 'YYYY-mm-dd') = '".$date."' ");
		return $this->db->get('camlis_patient_id')->result_array();
	}
	public function add($pid,$timestamp) {
		$this->db->set('pid',$pid);
		$this->db->set('"entryDate"', '"'.$timestamp.'"');
		$this->db->insert("camlis_patient_id");
		return $this->db->insert_id();
	}
	public function update($pid){
		$this->db->set('is_downloaded',TRUE);
		$this->db->where('pid',$pid);
		$this->db->update('camlis_patient_id');
		return $this->db->affected_rows();
	}
	public function isPidExist($pid){
		$this->db->select('count(*) as number');
		$this->db->where("pid" , $pid);
		return $this->db->get('camlis_patient_id')->result_array();
	}
	// data is valid from 12 Jan 2021
	public function count_pid($startDate , $endDate){
		$sql = "SELECT pro.name_kh as province_name , count(p.pid) AS number
				FROM provinces AS pro
				INNER JOIN camlis_patient_id as p on pro.code = substring(p.pid,1,2)::int
				WHERE \"entryDate\"::date >= date '".$startDate."' AND \"entryDate\"::date <= '".$endDate."' GROUP BY pro.name_kh;";
		return $this->db->query($sql)->result_array();
	}

}
