<?php
defined('BASEPATH') OR die('Permission denied.');
class Vaccine_model extends MY_Model {
	public function __construct() {
		parent::__construct(); 
		$this->load->database(); 
	}
    public function get_vaccine(){
        $this->db->select('*');
		$this->db->from('camlis_vaccine');
		return $this->db->get()->result();
    }
}