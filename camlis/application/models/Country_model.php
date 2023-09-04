<?php
defined('BASEPATH') or die('Access denied!');
class Country_model extends MY_Model {
	public function __construct() {
		parent::__construct();
	}

	/**
	 * Get Country
	 * @param null $country_code
	 * @return mixed
	 */
	public function get_country($country_code = NULL) {
		$this->db->select('num_code, name_en');		
		if ($country_code !== NULL) $this->db->where('num_code', $country_code);
		return $this->db->get('countries')->result();
	}
	public function get_nationality($country_code = NULL) {
		$this->db->select('num_code, nationality_en');		
		if ($country_code !== NULL) $this->db->where('num_code', $country_code);
		$this->db->order_by("nationality_en", "ASC");
		return $this->db->get('countries')->result();
	}
}