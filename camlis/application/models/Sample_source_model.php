<?php
defined('BASEPATH') OR die("Access denined!");
class Sample_source_model extends MY_Model {
	public function __construct() {
		parent::__construct();
		$this->load->database();
	}

	/**
	 * Get Standard Sample Source
	 * @param $condition Array More condition to get Standard Department
	 */
	public function get_lab_sample_source($condition = FALSE) {
		$this->db->select('ID AS source_id, source_name');
		$this->db->where('status', TRUE);
		$this->db->where('lab_id', $this->laboratory_id);
		!$condition OR $this->db->where($condition);
		$this->db->order_by('source_name');
		return $this->db->get('camlis_lab_sample_source')->result();
	}

	/**
	 * Add Standard Sample Source
	 * @param $data
	 * @return mixed
	 */
	public function add_lab_sample_source($data) {
		$this->db->set('entryBy', $this->user_id);
		$this->db->set('entryDate', $this->timestamp);
		$this->db->set("lab_id", $this->laboratory_id);
		$this->db->insert('camlis_lab_sample_source', $data);
		return $this->db->insert_id();
	}

	/**
	 * Update Standard Sample Source
	 * @param $data
	 * @return mixed
	 */
	public function update_lab_sample_source($source_id, $data) {
		$this->db->set('modifiedBy', $this->user_id);
		$this->db->set('modifiedDate', $this->timestamp);
		$this->db->set("lab_id", $this->laboratory_id);
		$this->db->where('status', TRUE);
		$this->db->where('"ID"', $source_id);
		$this->db->update('camlis_lab_sample_source', $data);
		return $this->db->affected_rows();
	}

	/**
	 * Get Std Sample Source using Datatable
	 */
	public function view_lab_sample_source($data) {
		$table		= 'camlis_lab_sample_source';
		$primaryKey	= 'ID';

		$columns	= array(
			array(
				'db'		=> 'ID',
				'dt'		=> 'source_id',
				'field'		=> 'ID'
			),
			array(
				'db'		=> 'source_name',
				'dt'		=> 'source_name',
				'field'		=> 'source_name'
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

		$extraWhere	= " status = TRUE AND lab_id = ".$this->laboratory_id;

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
	// added 27-04-2021	
	public function get_sample_source($lab_id) {
		$this->db->select('ID AS source_id, source_name');
		$this->db->where('status', TRUE);
		$this->db->where('lab_id', $lab_id);		
		$this->db->order_by('source_name');
		return $this->db->get('camlis_lab_sample_source')->result();
	}
}