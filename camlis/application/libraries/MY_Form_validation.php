<?php

/**
 * Created by PhpStorm.
 * User: spanga
 * Date: 10/17/2016
 * Time: 11:12 PM
 */
class MY_Form_validation extends CI_Form_validation
{
	public function __construct(array $rules = array())
	{
		parent::__construct($rules);
	}

	public function is_unique_extra($str, $field)
	{
		sscanf($field, '%[^.].%[^.]', $table, $field);
		return isset($this->CI->db)
			? ($this->CI->db->limit(1)->get_where($table, array($field => $str, 'status' => TRUE))->num_rows() === 0)
			: FALSE;
	}
}