<?php
defined('BASEPATH') OR die("Access denined!");
class Healthfacility_model extends MY_Model {
	public function __construct() {
		parent::__construct();
	}
	
	public function fetch() {
		$this->db->select('
			hf.code,
			hf.name_en,
			hf.name_kh,
			hf.type,
			hf.od_code,
			pro.code AS province_code,
			pro.name_en AS province_nameEN,
			pro.name_kh AS province_nameKH
		');
		$this->db->from('health_facilities AS hf');
		$this->db->join('operational_districts AS opd', 'hf.od_code=opd.code', 'join');
		$this->db->join('provinces AS pro', 'opd.province_code=pro.code', 'inner');
		$this->db->where_in('hf.type', array(5, 6));
			
		return $this->db->get()->result();
	}
}