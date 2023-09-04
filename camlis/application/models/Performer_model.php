<?php
defined('BASEPATH') OR die('Access Denied.');
class Performer_model extends MY_Model {
	public function __construct() {
		parent::__construct();
		$this->load->database();
	}

	/**
	 * Get Lab Performer
	 * @param bool $condition
	 * @return mixed
	 */
	public function get_lab_performer($condition= FALSE) {
		$this->db->select('ID, performer_name, gender');
		$this->db->where('status', TRUE);
		$this->db->where('lab_id', $this->laboratory_id);
		!$condition OR $this->db->where($condition);
		
		$query = $this->db->get(self::$CamLIS_db.'.camlis_lab_performer');
		return $query->result_array();
	}

	/**
	 * Add Lab Performer
	 * @param $data
	 * @return mixed
	 */
	public function add_lab_performer($data) {
		$this->db->set($data);
		$this->db->set('lab_id', $this->laboratory_id);
		$this->db->set('entryBy', $this->user_id);
		$this->db->set('entryDate', $this->timestamp);
		$this->db->insert(self::$CamLIS_db.'.camlis_lab_performer');
		return $this->db->insert_id();
	}

	/**
	 * Update Lab Performer
	 * @param $performer_id
	 * @param $data
	 * @return mixed
	 */
	public function update_lab_performer($performer_id, $data) {
		$this->db->set($data);
		$this->db->set('lab_id', $this->laboratory_id);
		$this->db->set('modifiedBy', $this->user_id);
		$this->db->set('modifiedDate', $this->timestamp);
		$this->db->where('status', TRUE);
		$this->db->where('lab_id', $this->laboratory_id);
		$this->db->where('"ID"', $performer_id);
		$this->db->update(self::$CamLIS_db.'.camlis_lab_performer');
		return $this->db->affected_rows();
	}
	
	/**
	 * View Perform using DataTable
	 */
	public function view_lab_performer($data) {
		$table		= 'camlis_lab_performer';
		$primaryKey	= 'ID';
		
		$columns	= array(
			array(
				'db'		=> 'gender',
				'dt'		=> 'gender_code',
				'field'		=> 'gender'
			),
			array(
				'db'		=> 'ID',
				'dt'		=> 'performer_id',
				'field'		=> 'ID',
			),
			array(
				'db'		=> 'performer_name',
				'dt'		=> 'performer_name',
				'field'		=> 'performer_name'
			),
			array(
				'db'		=> 'gender',
				'dt'		=> 'gender',
				'field'		=> 'gender',
				'type'		=> 'Number',
				'formatter'	=> function($d, $row) {
					$gender = $d == 1 ? "global.male" : "global.female";
					return _t($gender);
				}
			),
			array(
				'db'		=> 'ID',
				'dt'		=> 'action',
				'field'		=> 'ID',
				'formatter'	=> function($d, $row) {
					return "<center>
								<a href='#' class='edit hint--left hint--info' data-hint='"._t('global.edit')."'><i class='fa fa-pencil-square-o'></i></a>&nbsp;&nbsp;|&nbsp;&nbsp;
								<a href='#' class='remove hint--left hint--error' data-hint='"._t('global.remove')."'><i class='fa fa-trash text-red'></i></a>
							</center>";
				}
			)
		);
		
		$extraWhere	= " lab_id = $this->laboratory_id AND status = TRUE";
		
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
		$result = DataTable::simple( $data->reqData, $sql_details, $table, $primaryKey, $columns, NULL, $extraWhere );
		
		for ($i=0; $i < count($result['data']); $i++) {
			$result['data'][$i]['number'] = $i + 1;
		}
		
		return $result;
	}
}