<?php
defined('BASEPATH') OR die("Access denined!");
class Comment_model extends MY_Model {
	public function __construct() {
		parent::__construct();
		$this->load->database();
	}

	/**
	 * Get Standard Comment
	 * @param bool $condition
	 */
	public function get_std_comment($condition = FALSE) {
		//$this->db->where('status', TRUE);
		$this->db->where('status', 't');
		!$condition OR $this->db->where($condition);
		return $this->db->get(self::$CamLIS_db.'.camlis_std_comment')->result();
	}

	/**
	 * Get Standard Comment
	 * @param bool $condition
	 */
	public function get_std_sample_comment($condition = FALSE) {
		$this->db->select('
			comment."ID" AS comment_id,
			comment.comment,
			sample_comment.dep_sample_id
		');
		$this->db->from(self::$CamLIS_db.'.camlis_std_comment AS comment');
		$this->db->join(self::$CamLIS_db.'.camlis_std_sample_comment AS sample_comment', 'comment."ID" = sample_comment.comment_id', 'inner');
		$this->db->where('comment.status', TRUE);
		$this->db->where('sample_comment.status', TRUE);
		!$condition OR $this->db->where($condition);
		return $this->db->get()->result();
	}

	/**
	 * Add Std comment
	 * @param $_data
	 */
	public function add_std_comment($_data) {
		$this->db->set('"entryBy"', $this->user_id);
		$this->db->set('"entryDate"', $this->timestamp);
		$this->db->set($_data);
		$this->db->insert(self::$CamLIS_db.'.camlis_std_comment');

		return $this->db->insert_id();
	}

	/**
	 * Update Std comment
	 * @param $_data
	 */
	public function update_std_comment($comment_id, $_data) {
		$this->db->set('"modifiedBy"', $this->user_id);
		$this->db->set('"modifiedDate"', $this->timestamp);
		$this->db->set($_data);
		$this->db->where('status', TRUE);
		$this->db->where('"ID"', $comment_id);
		$this->db->update(self::$CamLIS_db.'.camlis_std_comment');

		return $this->db->affected_rows();
	}

	/**
	 * Assign comment to Standard Sample
	 * @param array $_data
	 */
	public function assign_std_sample_comment(array $_data) {
		foreach ($_data as $index => $row) {
			$row['entryBy'] = $this->user_id;
			$row['entryDate'] = $this->timestamp;
			$_data[$index] = $row;
		}

		$this->db->insert_batch(self::$CamLIS_db.'.camlis_std_sample_comment', $_data);
		return $this->db->affected_rows();
	}

	/**
	 * Delete Comment that assign to Department Sample
	 * @param $comment_id
	 * @param $dep_sample_id
	 * @param bool $match_list
	 */
	public function delete_std_sample_comment($comment_id, array $dep_sample_id = array(), $match_list = TRUE) {
		$this->db->where('status', TRUE);
		$this->db->where('comment_id', $comment_id);

		if ($dep_sample_id && count($dep_sample_id) > 0) {
			if ($match_list) $this->db->where_in('dep_sample_id', $dep_sample_id);
			else $this->db->where_not_in('dep_sample_id', $dep_sample_id);
		}

		$this->db->set('status', FALSE);
		$this->db->set('"modifiedBy"', $this->user_id);
		$this->db->set('"modifiedDate"', $this->timestamp);
		$this->db->update(self::$CamLIS_db.'.camlis_std_sample_comment');
		return $this->db->affected_rows();
	}

	/**
	 * Get comment  Datatable
	 */
	public function view_std_sample_comment($data) {
		$table		= '_view_comment';
		$primaryKey	= 'comment_id';

		$columns	= array(
			array(
				'db'		=> 'comment_id',
				'dt'		=> 'comment_id',
				'field'		=> 'comment_id',
				'type'		=> 'bigint'
			),
			array(
				'db'		=> 'department_id',
				'dt'		=> 'department_id',
				'field'		=> 'department_id'
			),
			array(
				'db'		=> 'dep_sample_id',
				'dt'		=> 'dep_sample_id',
				'field'		=> 'dep_sample_id'
			),
			array(
				'db'		=> 'department_name',
				'dt'		=> 'department_name',
				'field'		=> 'department_name'
			),
			array(
				'db'		=> 'sample_name',
				'dt'		=> 'sample_type',
				'field'		=> 'sample_name'
			),
			
			array(
				'db'		=> 'comment',
				'dt'		=> 'comment',
				'field'		=> 'comment'
			),
			
            array(
                'db'		=> 'is_reject_comment',
                'dt'		=> 'is_reject_comment',
				'field'		=> 'is_reject_comment',
				'type'		=> 'Boolean'
			),
			
			array(
				'db'		=> 'comment_id',
				'dt'		=> 'action',
				'field'		=> 'comment_id',
				'formatter'	=> function($d, $row) {
					return "<center>
								<a href='#' class='text-blue edit hint--left hint--info' data-hint='"._t('global.edit')."'><i class='fa fa-pencil-square-o'></i></a>&nbsp;|&nbsp;
								<a href='#' class='text-red remove hint--left hint--error' data-hint='"._t('global.remove')."'><i class='fa fa-trash'></i></a>
							</center>";
				}
			)
		);
		
		$joinQuery  = "";
        $extraWhere = [];
		if (!empty($data['department_sample'])) $extraWhere[] = "dep_sample_id IN (".implode(', ', $data['department_sample']).")";
		if (!empty($data['is_reject_comment'])) $extraWhere[] = "is_reject_comment = ".$data['is_reject_comment'];
		$extraWhere = implode(' AND ', $extraWhere);

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
		$result = DataTable::simple( $data, $sql_details, $table, $primaryKey, $columns, $joinQuery, $extraWhere );

		for ($i=0; $i < count($result['data']); $i++) {
			$result['data'][$i]['number'] = $i + 1;
		}

		return $result;
	}
}