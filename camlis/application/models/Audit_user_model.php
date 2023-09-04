<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Audit_user_model extends MY_Model {

	public function insert($data)
	{
		$this->db->insert('camlis_audit_trail_users', $data);
	}

	public function audit_user_report($labs, $start_date_time, $end_date_time)
	{
		$this->db->select('lab.name_en, lab.name_kh,users.fullname, groups.definition,  audit.ip_address, audit.timestamp');
		$this->db->from('camlis_audit_trail_users as audit');
		$this->db->join('camlis_aauth_users as users', 'users.id = audit.user_id', 'left');
		$this->db->join('camlis_laboratory as lab', 'lab."labID" = audit.lab_id', 'left');
		$this->db->join('camlis_aauth_user_to_group as user_group', 'user_group.user_id = users.id', 'left');
		$this->db->join('camlis_aauth_groups groups', 'groups.id = user_group.group_id', 'left');
		$this->db->where('timestamp >=', date('Y-m-d H:i',strtotime($start_date_time)));
		$this->db->where('timestamp <=', date('Y-m-d H:i',strtotime($end_date_time)));
		$this->db->where_in('audit.lab_id', $labs);	
		$this->db->order_by('lab.name_kh','asc');
		$this->db->order_by('audit.timestamp','desc');
		return $this->db->get()->result();;
	}

	public function get_lab()
	{
		$this->db->select('"labID", name_en, name_kh');
		$this->db->from('camlis_laboratory');
		return $this->db->get()->result();
	}

}