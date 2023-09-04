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
	public function add($pid, $user_id , $timestamp) {
		$this->db->set('pid',$pid);
		$this->db->set('"entryDate"', '"'.$timestamp.'"');
		$this->db->set('"entryBy"', $user_id);
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
	// added 25 Feb 2021
	public function search($pid){
		$this->db->select('*');
		$this->db->where("pid" , $pid);
		return $this->db->get('camlis_patient_id')->result_array();
	}
	public function getUser($id){		
		$sql = "SELECT pro.name_kh as province_name , gu.*
				FROM provinces AS pro
				INNER JOIN camlis_generator_user as gu on pro.code = gu.province_id
				WHERE gu.\"ID\" = '".$id."' ";
		return $this->db->query($sql)->result_array();
	}
	public function usernameExist($username){		
		$this->db->select('*');
		$this->db->where('username' , $username);
		return $this->db->get('camlis_generator_user')->result_array();
	}
	public function emailExist($email){		
		$this->db->select('*');
		$this->db->where('email' , $email);
		return $this->db->get('camlis_generator_user')->result_array();
	}
	public function addUser($data){
		$this->db->set($data);
        $this->db->insert('camlis_generator_user');
        return $this->db->insert_id();
	}
	public function login($username , $pwd){
		$this->db->select('*');
		$this->db->where('username' , $username);
		$this->db->where('password' , $pwd);
		$this->db->where('status' , true);		
		return $this->db->get('camlis_generator_user')->result_array();
	}
	public function getUsers($approval = null){	
		$extra = '';	
		if($approval !== null){
			$extra = 'WHERE approval = '.$approval;
		}
		$sql = "SELECT pro.name_kh as province_name , gu.*
				FROM provinces AS pro
				INNER JOIN camlis_generator_user as gu on pro.code = gu.province_id
				".$extra." order by gu.approval";
				
		return $this->db->query($sql)->result_array();
	}
	public function updateUser($user_id , $approve){	
		$timestamp			= date('Y-m-d H:i:s');	
		$this->db->set('approval',$approve);
		$this->db->set('"approveDate"',$timestamp);
		if($approve == 2) $this->db->set('status', true);
		if($approve == 1) $this->db->set('status', false); // reject
		$this->db->where('"ID"', $user_id);
		$this->db->update('camlis_generator_user');
		return $this->db->affected_rows();
	}
}
