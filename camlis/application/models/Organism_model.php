<?php
defined('BASEPATH') OR die("Access denied!");
class Organism_model extends MY_Model {
	public function __construct() {
		parent::__construct();
		$this->load->database();
	}

	/**
	 * Add Standard Organism
	 * @param $data
	 * @return mixed
	 */
	public function add_std_organism($data) {
		$this->db->set('entryBy', $this->user_id);
		$this->db->set('entryDate', $this->timestamp);
		$this->db->insert('camlis_std_organism', $data);
		return $this->db->insert_id();
	}

	/**
	 * Update Standard Organism
	 * @param $id
	 * @param $data
	 * @return mixed
	 */
	public function update_std_organism($id, $data) {
		$this->db->set('"modifiedBy"', $this->user_id);
		$this->db->set('"modifiedDate"', $this->timestamp);
		$this->db->where('"ID"', $id);
		$this->db->update('camlis_std_organism', $data);

		return $this->db->affected_rows();
	}

	/**
	 * Get Standard Organism
	 * @return object Result Query
	 */
	public function get_std_organism($condition = FALSE) {
		$this->db->select('
			"ID",
			(CASE organism_value
				WHEN 1 THEN CONCAT(organism_name, \' Positive\')
				WHEN 2 THEN CONCAT(organism_name, \' Negative\')
				ELSE organism_name
			END) AS organism_name,
			organism_value,
			order
		');
		$this->db->from('camlis_std_organism');
		$this->db->where('status', TRUE);
		!$condition OR $this->db->where($condition);
		$this->db->order_by('order');
		
		return $this->db->get()->result();
	}
	
	/**
	 * Get Test organism
	 * @param  $sample_test_id
	 * @return array list of organism
	 */
	public function get_sample_test_organism($sample_test_id = FALSE) {
		$this->db->select('
			test_org.ID AS test_organism_id,
			test_org.sample_test_id,
			test_org.organism_id,
			(CASE org.organism_value
				WHEN 1 THEN CONCAT(org.organism_name, \' Positive\')
				WHEN 2 THEN CONCAT(org.organism_name, \' Negative\')
				ELSE org.organism_name
			END) AS organism_name,
			order
		');
		$this->db->from('camlis_std_test_organism AS test_org');
		$this->db->join('camlis_std_organism AS org', 'test_org.organism_id = org.ID', 'inner');
		$this->db->where('test_org.status', TRUE);
		$this->db->where('org.status', TRUE);
		if ($sample_test_id > 0) $this->db->where('test_org.sample_test_id', $sample_test_id);
		$this->db->order_by('org.order', 'asc');

		return $this->db->get()->result();
	}

	/**
	 * Assign Organism to Standard Test
	 * @param $_data Requuire Data
	 * @return integer ID of new inserted row
	 */
	public function assign_std_sample_test_organism($_data) {
		$this->db->set($_data);
		$this->db->set('"entryBy"', $this->user_id);
		$this->db->set('"entryDate"', $this->timestamp);
		$this->db->insert('camlis_std_test_organism');

		return $this->db->insert_id();
	}

	/**
	 * Delete Organism of Sample Test
	 * @param $sample_test_id Sample Test ID
	 * @param bool [$organism_id = array()] Organism ID
	 * @param bool [$match_organism_id = TRUE] IF TRUE, delete Ref. Ranges with provided Organism, FALSE delete Ref. Ranges that are not in provided Organism
	 * @return number Number of Deleted Rows
	 */
	public function delete_std_sample_test_organism($sample_test_id, array $organism_id = array(), $match_organism_id = TRUE) {
		$this->db->where('status', TRUE);
		$this->db->where('sample_test_id', $sample_test_id);

		if ($organism_id || count($organism_id) > 0) {
			if ($match_organism_id)
				$this->db->where_in('organism_id', $organism_id);
			else
				$this->db->where_not_in('organism_id', $organism_id);
		}
		$this->db->set("status", FALSE);
		$this->db->set("modifiedBy", $this->user_id);
		$this->db->set("modifiedDate", $this->timestamp);
		$this->db->update('camlis_std_test_organism');

		return $this->db->affected_rows();
	}

	/**
	 * Get Department for Laboratory using Datatable
	 */
	public function view_std_organism($data) {
		$table		= 'camlis_std_organism';
		$primaryKey	= 'ID';

		$columns	= array(
			array(
				'db'		=> 'ID',
				'dt'		=> 'ID',
				'field'		=> 'ID'
			),
            array(
				'db'		=> 'order',
				'dt'		=> 'order',
				'field'		=> 'order',
				'type'		=> 'Number'
			),
			array(
				'db'		=> 'organism_name',
				'dt'		=> 'organism_name',
				'field'		=> 'organism_name'
			),
			array(
				'db'		=> 'organism_value',
				'dt'		=> 'organism_value',
				'field'		=> 'organism_value',
				'type'		=> 'Number'
			),
			array(
				'db'		=> 'organism_value',
				'dt'		=> 'value_text',
				'field'		=> 'organism_value',
				'type'		=> 'Number',
				'formatter'	=> function($d, $row) {
					if ($d == 1) return _t('global.positive');
					else if ($d == 2) return _t('global.negative');
					return "";
				}
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