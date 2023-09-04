<?php
defined('BASEPATH') OR die('Permission denied.');
class Godata_model extends MY_Model {
	public function __construct() {
		parent::__construct();		
	}
	public function getOutbreak($id = null){
		$this->db->select('*');
		if($id !== null) $this->db->where('id', $id);
		$this->db->order_by('id', 'asc');	
		return $this->db->get('gd_outbreak')->result();	
	}
	public function getClassification($outbreak_id){
		$this->db->select('*');
		$this->db->where('status', true);
		$this->db->where('"outbreakID"', $outbreak_id);		
		return $this->db->get('gd_classification')->result();
	}
	public function getLocation($outbreak_id){
		$this->db->select('*');
		$this->db->where('"outbreakID"', $outbreak_id);	
		$this->db->where('status', true);	
		return $this->db->get('gd_location')->result();
	}
	public function getNationality($outbreak_id){
		$this->db->select('*');
		$this->db->where('"outbreakID"', $outbreak_id);
		$this->db->where('status', true);
		return $this->db->get('gd_nationality')->result();
	}
	public function getHighRiskContact($outbreak_id){
		$this->db->select('*');
		$this->db->where('"outbreakID"', $outbreak_id);
		$this->db->where('status', true);
		return $this->db->get('gd_high_risk_contact')->result();
	}
	public function updateClassification($id , $data){
		// delete old data
		$this->db->set('status', false);
		$this->db->set('"modifiedDate"', date('Y-m-d H:i:s'));
		$this->db->where('"outbreakID"', $id);
		$this->db->where('status', true);
		$this->db->update('gd_classification');

		$this->db->insert_batch('gd_classification', $data);		
		return $this->db->affected_rows();
	}

	public function updateLocation($id , $data){
		// delete old data
		$this->db->set('status', false);
		$this->db->set('"modifiedDate"', date('Y-m-d H:i:s'));
		$this->db->where('"outbreakID"', $id);
		$this->db->where('status', true);
		$this->db->update('gd_location');

		$this->db->insert_batch('gd_location', $data);		
		return $this->db->affected_rows();
	}
	public function updateHighRish($id , $data){
		$this->db->set('status', false);	
		$this->db->where('"outbreakID"', $id);
		$this->db->where('status', true);
		$this->db->update('gd_high_risk_contact');
		$this->db->insert('gd_high_risk_contact', $data);
		return $this->db->affected_rows();
	}

	public function updateNationality($id , $data){
		// delete old data
		$this->db->set('status', false);
		$this->db->where('"outbreakID"', $id);
		$this->db->where('status', true);
		$this->db->update('gd_nationality');

		$this->db->insert_batch('gd_nationality', $data);		
		return $this->db->affected_rows();
	}
}