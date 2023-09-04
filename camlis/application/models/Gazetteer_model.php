<?php
defined('BASEPATH') or die('Access denied!');
class Gazetteer_model extends MY_Model {
	public function __construct() {
		parent::__construct();
	}

	/**
	 * Get Province
	 * @param null $province_code
	 * @return mixed
	 */
	public function get_province($province_code = NULL) {
		$this->db->select('code, name_en, name_kh');
		$this->db->where('status', TRUE);
		$this->db->where('code !=', 25);
		if ($province_code !== NULL) $this->db->where('code', $province_code);
		return $this->db->get('provinces')->result();
	}
	
	public function get_district($province_code = NULL, $district_code = NULL) {
		$this->db->select('code, name_en, name_kh, province_code');
		
		if ($province_code !== NULL) $this->db->where(array('status' => TRUE, 'province_code' => $province_code));
		if ($district_code !== NULL) $this->db->where('code', $district_code);
		
		$query = $this->db->get('districts');
		
		return $query->result();
	}
	
	public function get_commune($district_code = NULL, $commune_code = NULL) {
		$this->db->select('code, name_en, name_kh');
		
		if ($district_code !== NULL) $this->db->where(array('status' => TRUE, 'district_code' => $district_code));
		if ($commune_code !== NULL) $this->db->where('code', $commune_code);
		
		$query = $this->db->get('communes');
		
		return $query->result();
	}
	
	public function get_village($commune_code = NULL, $village_code = NULL) {
		$this->db->select('code, name_en, name_kh');
		//if ($commune_code !== NULL) $this->db->where(array('status' => TRUE, 'commune_code' => $commune_code));
		if ($commune_code !== NULL) $this->db->where(array('status' => 1, 'commune_code' => $commune_code));
		if ($village_code !== NULL) $this->db->where('code', $village_code);		
		$query = $this->db->get('villages');
		return $query->result();
	}
	/**
	 * GRID FORM
	 */
	// Get province by get_province
	// Added 21-03-2021
	
	public function get_province_by_name($name_kh){
		$this->db->select('code, name_en, name_kh');
		$this->db->where('status', TRUE);
		$this->db->like('name_kh',$name_kh);
		return $this->db->get('provinces')->result();
	}
	public function get_district_by_name($province_code, $name_kh) {
		$this->db->select('code, name_en, name_kh, province_code');
		$this->db->where(array('status' => TRUE, 'province_code' => $province_code));
		$this->db->like('name_kh',$name_kh);
		$query = $this->db->get('districts');
		return $query->result();
	}
	public function get_commune_by_name($district_code, $name_kh) {
		$this->db->select('code, name_en, name_kh, district_code');
		$this->db->where(array('status' => TRUE, 'district_code' => $district_code));
		$this->db->where('name_kh', $name_kh);
		$query = $this->db->get('communes');
		return $query->result();
	}
	public function get_village_by_name($commune_code, $name_kh) {
		$this->db->select('code, name_en, name_kh');		
		$this->db->where(array('status' => 1, 'commune_code' => $commune_code));
		$this->db->where('name_kh', $name_kh);
		$query = $this->db->get('villages');
		return $query->result();
	}	

		
	public function get_commune_($district_code = NULL, $commune_code = NULL) {
		$this->db->select('code, name_en, name_kh, district_code');
		
		if ($district_code !== NULL) $this->db->where(array('status' => TRUE, 'district_code' => $district_code));
		if ($commune_code !== NULL) $this->db->where('code', $commune_code);
		
		$query = $this->db->get('communes');
		
		return $query->result();
	}
	
	public function get_village_($commune_code = NULL, $village_code = NULL) {
		$this->db->select('code, name_en, name_kh, commune_code');		
		if ($commune_code !== NULL) $this->db->where(array('status' => 1, 'commune_code' => $commune_code));
		if ($village_code !== NULL) $this->db->where('code', $village_code);		
		$query = $this->db->get('villages');
		return $query->result();
	}
}