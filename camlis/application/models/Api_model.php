<?php
defined('BASEPATH') or die('Access denied!');
class Api_model extends MY_Model {
	public function __construct() {
		parent::__construct();
    }
    function get_patient()
    {
        $this->db->order_by('"ID"', 'DESC');
        return $this->db->get('camlis_outside_patient');
    }
}

?>