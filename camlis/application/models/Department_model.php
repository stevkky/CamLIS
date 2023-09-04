<?php
defined('BASEPATH') OR die("No direct script acccess allowed.");
class Department_model extends MY_Model {
	public function __construct() {
		parent::__construct();
		$this->load->database();
	}

	/**
	 * Add Standard Department
	 * @param $data
	 * @return mixed
	 */
	public function add_std_department($data) {
		$this->db->set('entryBy', $this->user_id);
		$this->db->set('entryDate', $this->timestamp);
		$this->db->insert(self::$CamLIS_db.'.camlis_std_department', $data);
		return $this->db->insert_id();
	}

	/**
	 * Update Standard Department
	 * @param $id
	 * @param $data
	 * @return mixed
	 */
	public function update_std_department($id, $data) {
		$this->db->set('"modifiedBy"', $this->user_id);
		$this->db->set('"modifiedDate"', $this->timestamp);
		$this->db->where('"ID"', $id);
		$this->db->update(self::$CamLIS_db.'.camlis_std_department', $data);

		return $this->db->affected_rows();
	}
	
	/**
	 * Get Standard Department
	 * @param $condition Array More condition to get Standard Department
	*/
	public function get_std_department($condition = FALSE) {
		$this->db->select('"ID" AS department_id, department_name, show_weight');
		$this->db->where('status', TRUE);
		!$condition OR $this->db->where($condition);
		$this->db->order_by('order', 'asc');
		$query = $this->db->get(self::$CamLIS_db.'.camlis_std_department');
		
		return $query->result();
	}

	/**
	 * Get Department for Laboratory using Datatable
	 */
	public function view_std_department($data) {
		$table		= 'camlis_std_department';
		$primaryKey	= 'ID';

		$columns	= array(
			array(
				'db'		=> 'ID',
				'dt'		=> 'ID',
				'field'		=> 'ID'
			),
			array(
				'db'		=> 'department_name',
				'dt'		=> 'department_name',
				'field'		=> 'department_name'
			),
			array(
				'db'		=> 'ID',
				'dt'		=> 'action',
				'field'		=> 'ID',
				'formatter'	=> function($d, $row) {
					return "<center>
								<a href='#' class='text-blue edit hint--left hint--info' data-hint='"._t('global.edit')."'><i class='fa fa-pencil-square-o'></i></a>&nbsp;|&nbsp;
								<a href='#' class='text-red remove hint--left hint--error' data-hint='"._t('global.remove')."'><i class='fa fa-trash'></i></a>
							</center>";
				}
			)
		);

		$extraWhere	= " status = TRUE";

		//config
		$db_config		= $this->load->database('default', TRUE);
		$sql_details	= array(
			'user'	=> $db_config->username,
			'pass'	=> $db_config->password,
			'port'	=> $db_config->port,
			'db'	=> $db_config->database,
			'host'	=> $db_config->hostname
		);

		$this->load->library('DataTable');
		$result = DataTable::simple( $data, $sql_details, $table, $primaryKey, $columns, NULL, $extraWhere );

		for ($i=0; $i < count($result['data']); $i++) {
			$result['data'][$i]['number'] = $i + 1;
		}

		return $result;
	}
}
	
	