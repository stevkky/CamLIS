<?php
defined('BASEPATH') OR die("Access denied!");
class Quantity_model extends MY_Model {
	public function __construct() {
		parent::__construct();
		$this->load->database();
	}

	/**
	 * Add Standard Quantity
	 * @param $data
	 * @return mixed
	 */
	public function add_std_organism_quantity($data) {
		$this->db->set('entryBy', $this->user_id);
		$this->db->set('entryDate', $this->timestamp);
		$this->db->insert(self::$CamLIS_db.'.camlis_std_organism_quantity', $data);
		return $this->db->insert_id();
	}

	/**
	 * Update Standard Quantity
	 * @param $id
	 * @param $data
	 * @return mixed
	 */
	public function update_std_organism_quantity($id, $data) {
		$this->db->set('"modifiedBy"', $this->user_id);
		$this->db->set('"modifiedDate"', $this->timestamp);
		$this->db->where('"ID"', $id);
		$this->db->update(self::$CamLIS_db.'.camlis_std_organism_quantity', $data);

		return $this->db->affected_rows();
	}

	/**
	 * Get Standard Quantity
	 * @return object Result
	 */
	public function get_std_organism_quantity($condition = FALSE) {
		$this->db->select('"ID", quantity');
		$this->db->from(self::$CamLIS_db.'.camlis_std_organism_quantity');
		$this->db->where('status', TRUE);
		!$condition OR $this->db->where($condition);
		$this->db->order_by('quantity');
		
		return $this->db->get()->result_array();
	}

	/**
	 * Get Department for Laboratory using Datatable
	 */
	public function view_std_organism_quantity($data) {
		$table		= 'camlis_std_organism_quantity';
		$primaryKey	= 'ID';

		$columns	= array(
			array(
				'db'		=> 'ID',
				'dt'		=> 'ID',
				'field'		=> 'ID'
			),
			array(
				'db'		=> 'quantity',
				'dt'		=> 'quantity',
				'field'		=> 'quantity'
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