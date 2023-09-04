<?php
defined('BASEPATH') OR die('Access denied!');
class Antibiotic_model extends MY_Model {
	public function __construct() {
		parent::__construct();
	}

	/**
	 * Add Standard Antibiotic
	 * @param $data
	 * @return mixed
	 */
	public function add_std_antibiotic($data) {
		$this->db->set('"entryBy"', $this->user_id);
		$this->db->set('"entryDate"', $this->timestamp);
		$this->db->insert('camlis_std_antibiotic', $data);
		return $this->db->insert_id();
	}

	/**
	 * Update Standard Antibiotic
	 * @param $id
	 * @param $data
	 * @return mixed
	 */
	public function update_std_antibiotic($id, $data) {
		$this->db->set('"modifiedBy"', $this->user_id);
		$this->db->set('"modifiedDate"', $this->timestamp);
		$this->db->where('"ID"', $id);
		$this->db->update('camlis_std_antibiotic', $data);

		return $this->db->affected_rows();
	}

	/**
	 * Get Standard Antibiotic
	 * @return object Result Query
	 */
	public function get_std_antibiotic($condition = FALSE) {
		$this->db->select('"ID", antibiotic_name, order');
		$this->db->from('camlis_std_antibiotic');
		//$this->db->where('status', TRUE);
		$this->db->where('status', 't');
		!$condition OR $this->db->where($condition);
		$this->db->order_by('order');
		
		return $this->db->get()->result();
	}
    /**
     * get_gram_type
     * @return object Result Query
     */
    public function get_gram_type() {
        $this->db->select('id, gram_type');
        $this->db->from('gram_type');
        //$this->db->where('status', TRUE);
		$this->db->where('status', 't');
        return $this->db->get()->result();
    }

	/**
	 * Assgin Antibiotic to Organism of Standard Test
	 * @param $_data Required Data
	 * @return Number of Inserted Rows
	 */
	public function assign_std_organism_antibiotic($_data) {
		$_data = (array)$_data;
		foreach ($_data as $index => $row) {
			$row['entryBy'] = $this->user_id;
			$row['entryDate'] = $this->timestamp;
			$_data[$index] = $row;
		}
		$this->db->insert_batch('camlis_std_test_organism_antibiotic', $_data);
		return $this->db->affected_rows();
	}

	/**
	 * Get Antibiotic that Assign to Organism of Standard Test
	 * @param bool $condition Condition to get Data
	 */
	public function get_std_sample_test_organism_antibiotic($condition = FALSE) {
		$this->db->select('
			org."ID" AS organism_id,
			(CASE org.organism_value
				WHEN 1 THEN CONCAT(org.organism_name, \' Positive\')
				WHEN 2 THEN CONCAT(org.organism_name, \' Negative\')
				ELSE org.organism_name
			END) AS organism_name,
			anti."ID" AS antibiotic_id,
			anti.antibiotic_name,
			anti.order,
			test_org."ID" AS test_organism_id,
			org_anti."ID" AS org_antibiotic_id
		');
		$this->db->from('camlis_std_test_organism AS test_org');
		$this->db->join('camlis_std_organism AS org', 'test_org.organism_id = org."ID"', 'inner');
		$this->db->join('camlis_std_test_organism_antibiotic AS org_anti', "test_org.\"ID\" = org_anti.test_organism_id AND org_anti.status = 't'", 'left');
		$this->db->join("camlis_std_antibiotic AS anti", "org_anti.antibiotic_id = anti.\"ID\" AND anti.status ='t'", 'left');
		!$condition OR $this->db->where($condition);
		//$this->db->where('test_org.status', TRUE);
		$this->db->where('test_org.status', 't');
		//$this->db->where('org.status', TRUE);
		$this->db->where('org.status', 't');
        $this->db->order_by('anti.order');

		return $this->db->get()->result();
	}

	/**
	 * Delete Antibiotic that assigned to to Organism of Standard Test
	 * @param integer $_id If $list_type is organism_list, test_id is required. If $list_type is antibiotic_list, test_organism_id is required.
	 * @param array $list_id List of Organism or Antibiotic
	 * @param bool $match_list_id IF TRUE, delete Antibiotic within List, FALSE delete Antibiotic that are not in List
	 * @param string $list_type Type of List to best use can be organism_list or antibiotic_list
	 * @param array $organism_id List of Organism
	 */
	public function delete_std_sample_test_organism_antibiotic($_id, array $list_id = array(), $match_list_id = TRUE, $list_type = 'organism_list') {
		if ($list_type == 'organism_list') {
			$extraWhere = "";
			if (count($list_id) > 0) {
				$extraWhere = " AND test_org.organism_id IN (" . implode(',', $list_id) . ")";
				if ($match_list_id === FALSE) $extraWhere = " AND test_org.organism_id NOT IN (" . implode(',', $list_id) . ")";
			}
			/*
			$sql = "UPDATE camlis_std_test_organism_antibiotic org_anti
					INNER JOIN camlis_std_test_organism test_org ON org_anti.test_organism_id = test_org.\"ID\"
					SET  org_anti.status = FALSE,
						 org_anti.\"modifiedBy\" = ". $this->user_id .",
						 org_anti.\"modifiedDate\" = '". $this->timestamp ."'	
					WHERE   org_anti.status = TRUE
							AND test_org.status = TRUE
							AND test_org.sample_test_id = $_id " . $extraWhere;
						*/
			$sql = "UPDATE camlis_std_test_organism_antibiotic
					SET status = FALSE,
						\"modifiedBy\" = ". $this->user_id .",
						\"modifiedDate\" = '". $this->timestamp ."'
					FROM camlis_std_test_organism test_org		
					WHERE camlis_std_test_organism_antibiotic.status = TRUE
							AND test_org.status = TRUE
							AND test_organism_id = test_org.\"ID\"
							AND test_org.sample_test_id = ".$_id . $extraWhere;


			$this->db->query($sql);
		}
		else if ($list_type = 'antibiotic_list') {
			$this->db->where('status', TRUE);
			$this->db->where('test_organism_id', $_id);

			if ($list_id || count($list_id) > 0) {
				if ($match_list_id)
					$this->db->where_in('antibiotic_id', $list_id);
				else
					$this->db->where_not_in('antibiotic_id', $list_id);
			}
			//$this->db->set("status", FALSE);
			$this->db->set("status", 'f');
			$this->db->set('"modifiedBy"', $this->user_id);
			$this->db->set('"modifiedDate"', $this->timestamp);
			$this->db->update('camlis_std_test_organism_antibiotic');
		}
		return $this->db->affected_rows();
	}

	/**
	 * Get Department for Laboratory using Datatable
	 */
	public function view_std_antibiotic($data) {
		$table		= 'camlis_std_antibiotic';
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
                'db'		=> 'antibiotic_name',
                'dt'		=> 'antibiotic_name',
                'field'		=> 'antibiotic_name'
            ),
            array(
                'db'		=> 'gram_type',
                'dt'		=> 'gram_type',
                'field'		=> 'gram_type'
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