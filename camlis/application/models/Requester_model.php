<?php
defined('BASEPATH') OR die('Access Denied.');
class Requester_model extends MY_Model {
	public function __construct() {
		parent::__construct();
		$this->load->database();
	}

	/**
	 * Check if Rquester is exist
	 * @param $requester_name
	 * @param bool $requester_id
	 * @return bool
	 */
	public function is_exist($requester_name, $requester_id = FALSE) {
		$this->db->where('lab_id', $this->laboratory_id);
		$this->db->where('requester_name', $requester_name);
		if ($requester_id) $this->db->where('"ID" !=', $requester_id);
		$query = $this->db->get(self::$CamLIS_db.'.camlis_lab_requester');
		if ($query->num_rows() > 0) return TRUE;
		return FALSE;
	}

	/**
	 * Get Lab requester
	 * @param $requester_id
	 * @param bool $sample_source_id
	 * @return mixed
	 */
	public function get_lab_requester($requester_id, $sample_source_id = FALSE) {
		$this->db->select('requester."ID" AS requester_id, requester.requester_name, requester.gender, source_req.sample_source_id');
		$this->db->where('requester.status', TRUE);
		$this->db->where('requester.lab_id', $this->laboratory_id);
		!$requester_id OR $this->db->where('requester."ID"', $requester_id);
		!$sample_source_id OR $this->db->where('source_req.sample_source_id', $sample_source_id);

		$this->db->from(self::$CamLIS_db.'.camlis_lab_requester AS requester');
		$this->db->join(self::$CamLIS_db.'.camlis_lab_requester_sample_source AS source_req', 'requester."ID" = source_req.requester_id','inner');
		$this->db->order_by('requester.requester_name', 'asc');
		$query = $this->db->get();
		return $query->result();
	}

	/**
	 * Get Requester with Sample Source
	 * @param bool $condition
	 */
	public function get_requester_sample_source($condition = FALSE) {
		$this->db->select('
			requester."ID" AS requester_id,
			requester.requester_name,
			requester.gender,
			req_source.sample_source_id
		');
		$this->db->from(self::$CamLIS_db.'.camlis_lab_requester AS requester');
		$this->db->join(self::$CamLIS_db.'.camlis_lab_requester_sample_source AS req_source', 'requester."ID" = req_source.requester_id', 'inner');
		$this->db->where('requester.status', TRUE);
		$this->db->where('req_source.status', TRUE);
		!$condition OR $this->db->where($condition);
		return $this->db->get()->result();
	}

	/**
	 * Add Lab requester
	 * @param $data
	 * @return mixed
	 */
	public function add_lab_requester($data) {
		$this->db->set($data);
		$this->db->set('lab_id', $this->laboratory_id);
		$this->db->set('entryBy', $this->user_id);
		$this->db->set('entryDate', $this->timestamp);
		$this->db->insert(self::$CamLIS_db.'.camlis_lab_requester');
		return $this->db->insert_id();
	}

	/**
	 * Update Lab requester
	 * @param $requester_id
	 * @param $data
	 * @return mixed
	 */
	public function update_lab_requester($requester_id, $data) {
		$this->db->set($data);
		$this->db->set('lab_id', $this->laboratory_id);
		$this->db->set('modifiedBy', $this->user_id);
		$this->db->set('modifiedDate', $this->timestamp);
		$this->db->where('status', TRUE);
		$this->db->where('lab_id', $this->laboratory_id);
		$this->db->where('"ID"', $requester_id);
		$this->db->update(self::$CamLIS_db.'.camlis_lab_requester');
		return $this->db->affected_rows();
	}

	/**
	 * Assign Sample Source with Requester
	 * @param $_data
	 */
	public function assign_requester_sample_source($_data) {
		$_data = (array)$_data;
		foreach ($_data as $index => $row) {
			$row['entryBy'] = $this->user_id;
			$row['entryDate'] = $this->timestamp;
			$_data[$index] = $row;
		}
		$this->db->insert_batch(self::$CamLIS_db.'.camlis_lab_requester_sample_source', $_data);
		return $this->db->affected_rows();
	}

	/**
	 * Delete Sample Source of Requester
	 * @param $requester_id
	 * @param $sample_sources
	 * @param bool $match_list
	 */
	public function delete_requester_sample_source($requester_id, $sample_sources = array(), $match_list = TRUE) {
		$this->db->where('status', TRUE);
		$this->db->where('requester_id', $requester_id);

		if ($sample_sources && count($sample_sources) > 0) {
			if ($match_list) $this->db->where_in('sample_source_id', $sample_sources);
			else $this->db->where_not_in('sample_source_id', $sample_sources);
		}

		$this->db->set('status', FALSE);
		$this->db->set('modifiedBy', $this->user_id);
		$this->db->set('modifiedDate', $this->timestamp);
		$this->db->update(self::$CamLIS_db.'.camlis_lab_requester_sample_source');
		return $this->db->affected_rows();
	}
	
	/**
	 * View Perform using DataTable
	 */
	public function view_lab_requester($data) {
		$_model		= $this;
		$table		= 'camlis_lab_requester';
		$primaryKey	= 'ID';
		
		$columns	= array(
			array(
				'db'		=> 'gender',
				'dt'		=> 'gender_code',
				'field'		=> 'gender'
			),
			array(
				'db'		=> 'requester."ID"',
				'dt'		=> 'requester_id',
				'field'		=> 'ID',
			),
			array(
				'db'		=> 'string_agg((sc."ID")::TEXT, \',\') AS sample_source_id',
				'dt'		=> 'sample_sources',
				'field'		=> 'sample_source_id',
				'formatter'	=> function($d, $row) use($_model) {
					return explode(',', $d);
				}
			),
			array(
				'db'		=> 'string_agg(sc.source_name, \',\') AS sample_source',
				'dt'		=> 'sample_source_text',
				'field'		=> 'sample_source',
				'formatter'	=> function($d, $row) {
					$arr = explode(',', $d);
					$formatted = "";
					foreach ($arr as $item) {
						$formatted .= "<span style='padding: 3px 10px; background: #298c49; color:white; margin: 0 0 5px 3px; border-radius: 2px; white-space: nowrap; display: inline-block;'>$item</span>";
					}
					return $formatted;
				}
			),
			array(
				'db'		=> 'requester_name',
				'dt'		=> 'requester_name',
				'field'		=> 'requester_name'
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
				'db'		=> 'requester."ID"',
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

		$joinQuery = "	FROM $table AS requester 
						INNER JOIN camlis_lab_requester_sample_source AS req_sc ON requester.\"ID\" = req_sc.requester_id
						INNER JOIN camlis_lab_sample_source AS sc ON req_sc.sample_source_id = sc.\"ID\" ";
		$extraWhere	= " requester.lab_id = $this->laboratory_id AND requester.status = TRUE AND req_sc.status = TRUE AND sc.status = TRUE GROUP BY requester.\"ID\" ";
		
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
		$result = DataTable::simple( $data->reqData, $sql_details, $table, $primaryKey, $columns, $joinQuery, $extraWhere );
		
		for ($i=0; $i < count($result['data']); $i++) {
			$result['data'][$i]['number'] = $i + 1;
		}
		
		return $result;
	}
}