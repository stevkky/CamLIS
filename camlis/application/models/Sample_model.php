<?php
defined('BASEPATH') OR die('Permission denied!');
class Sample_model extends MY_Model {
	public function __construct() {
		parent::__construct();
		$this->load->library('DataTable');
	}

	/**
	 * Get Standard Sample
	 * @param bool $condition
	 * @return object DB query
	 */
	public function get_std_sample($condition = FALSE) {
		!$condition OR $this->db->where($condition);
		$this->db->where('status', TRUE);
		return $this->db->get('camlis_std_sample')->result();
	}
	
	/**
	 * Add Standard Sample
	 * @param  object $data Required Data
	 * @return integer Id of inserted record
	 */
	public function add_std_sample($data) {
		$this->db->set('entryBy', $this->user_id);
		$this->db->set('entryDate', $this->timestamp);
		$this->db->insert('camlis_std_sample', $data);
		
		return $this->db->insert_id();
	}
	
	/**
	 * Update Standard Sample
	 * @param  object $data Required Data for Update
	 * @param array $condition Condition to Update
	 * @return integer Number of updated record
	 */
	public function update_std_sample($data, $condition) {
		$this->db->set($data);
		$this->db->set('modifiedBy', $this->user_id);
		$this->db->set('modifiedDate', $this->timestamp);
		$this->db->where($condition);
		$this->db->where('status', TRUE);
		$this->db->update('camlis_std_sample');
		
		return $this->db->affected_rows();
	}
	
	/**
	 * Delete Standard Sample
	 * @param  object $condition Condition
	 * @return integer Number of updated record
	 */
	public function delete_std_sample($sample_id) {
		$this->db->set('status', FALSE);
		$this->db->set('"modifiedBy"', $this->user_id);
		$this->db->set('"modifiedDate"', $this->timestamp);
		$this->db->where('"ID"', $sample_id);
		$this->db->update('camlis_std_sample');
		
		return $this->db->affected_rows();
	}

    /**
     * Set Sample Description
     * @param $sample_id
     * @param array $descriptions
     */
	public function set_std_sample_description($sample_id, array $descriptions) {
        $this->db->trans_start();

        //Delete descriptions that are not in list
        $this->db->set('status', FALSE);
        $this->db->set('"modifiedBy"', $this->user_id);
        $this->db->set('"modifiedDate"', $this->timestamp);
        $this->db->where('status', TRUE);
        $this->db->where('sample_id', $sample_id);
        if (count($descriptions) > 0 ) $this->db->where_not_in('description', $descriptions);
        $this->db->update('camlis_std_sample_description');

        //Insert description
        $values = array();
        foreach ($descriptions as $description) {
            if (!empty($description)) {
                $_tmp     = array($sample_id, "'".$description."'", $this->user_id, "'".$this->timestamp."'");
                $values[] = "(".implode(',', $_tmp).")";
            }
        }

        if (count($values) > 0) {
            $sql  = "INSERT INTO camlis_std_sample_description (sample_id, description, \"entryBy\", \"entryDate\")";
            $sql .= " VALUES ".implode(',', $values);
            $sql .= " ON DUPLICATE KEY UPDATE status = ?, modifiedBy = ?, modifiedDate = ?";
            $this->db->query($sql, array(TRUE, $this->user_id, $this->timestamp));
        }

        $this->db->trans_complete();

        return $this->db->affected_rows();
    }

    /**
     * Get Std Sample Descriptions
     * @param $sample_id
     */
    public function get_std_sample_descriptions($sample_id = 0) {
        $this->db->select('ID, sample_id, description');
        $this->db->where('status', TRUE);
        if ($sample_id > 0) $this->db->where('sample_id', $sample_id);
        return $this->db->get('camlis_std_sample_description')->result_array();
    }
	
	/**
	 * Assign Sample to departments
	 * @param  [[Type]] $data [[Description]]
	 * @return [[Type]] [[Description]]
	 */
	public function assign_std_department_sample($data) {
		$data = (array)$data;
		foreach ($data as $index => $d) {
			$d['entryBy'] = $this->user_id;
			$d['entryDate'] = $this->timestamp;
			$data[$index] = $d;
		}
		$this->db->insert_batch('camlis_std_department_sample', $data);
		
		return $this->db->affected_rows();
	}

	/**
	 * Delete Assigned Sample -> Department
	 * @param $sample_id Standard Sample ID
	 * @param bool [$department_id = FALSE] Standard Department ID
	 * @param bool [$match_department_id = TRUE] IF TRUE, delete Department in the provided List, else delete Department that is not in the provided List
	 * @return number Number of Deleted Rows
	 */
	public function delete_std_department_sample($sample_id, $department_id = FALSE, $match_department_id = TRUE) {
		$this->db->where('status', TRUE);
		$this->db->where('sample_id', $sample_id);

		if ($department_id) {
			if ($match_department_id)
				$this->db->where_in('department_id', $department_id);
			else
				$this->db->where_not_in('department_id', $department_id);
		}

		$this->db->set("status", FALSE);
		$this->db->set("modifiedBy", $this->user_id);
		$this->db->set("modifiedDate", $this->timestamp);
		$this->db->update('camlis_std_department_sample');
		
		return $this->db->affected_rows();
	}

	/**
	 * Get Standard Assigned Sample -> Department
	 * @param array|object Condition to get Data
	 * @return object DB Query
	 */
	public function get_std_department_sample($condition = NULL)
	{
		$condition = (array)$condition;
		$this->db->select("ds.\"ID\" AS department_sample_id, d.department_name, ds.department_id, ds.sample_id, s.sample_name");
		$this->db->where('d.status', TRUE);
		$this->db->where('s.status', TRUE);
		$this->db->where('ds.status', TRUE);

		if (isset($condition['sample_id']) && !empty($condition['sample_id']))
			$this->db->where('ds.sample_id', $condition['sample_id']);
		if (isset($condition['department_id']) && !empty($condition['department_id']))
			$this->db->where('ds.department_id', $condition['department_id']);

		$this->db->from('camlis_std_department AS d');
		//$this->db->join('camlis_std_department_sample AS ds', 'd.id = ds.department_id', 'inner');
		$this->db->join('camlis_std_department_sample AS ds', 'd."ID" = ds.department_id', 'inner');
		//$this->db->join('camlis_std_sample AS s', 's.id = ds.sample_id', 'inner');
		$this->db->join('camlis_std_sample AS s', 's."ID" = ds.sample_id', 'inner');
		$this->db->order_by('ds.order');
		$query = $this->db->get();

		return $query->result();
	}
	
	/**
	 * Get Sample for Laboratory using Datatable
	 */
	public function view_all_std_sample($data) {
		$table		= '_view_sample_type';
		//$table		= '_view_camlis_sample_comment';
		$primaryKey	= 'sample_id';
		
		$columns	= array(
			array(
				'db'		=> 'sample_id',
				'dt'		=> 'DT_RowData',
				'field'		=> 'sample_id',
				'formatter'	=> function($d, $row) {
					return array('sample_id' => $d);
				}
			),
            array(
                'db'		=> 'description',
                'dt'		=> 'DT_RowData',
                'field'		=> 'description',
                'formatter'	=> function($d, $row) {
                    $descriptions = array();
                    if (!empty($d)) {
                        $arr = explode(',', $d);
                        foreach ($arr as $item) {
                            $descriptions[] = $item;
                        }
                    }
                    return array('sample_id' => $row['sample_id'], 'descriptions' => $descriptions);
                }
            ),
			array(
				'db'		=> 'sample_name',
				'dt'		=> 'sample_name',
				'field'		=> 'sample_name'
			),
            array(
                'db'		=> 'department_name',
                'dt'		=> 'department_name',
                'field'		=> 'department_name',
                'formatter'	=> function($d, $row) {
                    $formatted = "";
                    if (!empty($d)) {
                        $arr = explode(',', $d);
                        foreach ($arr as $item) {
                            $formatted .= "<span style='padding: 3px 10px; background: #298c49; color:white; margin: 0 0 5px 3px; border-radius: 2px; white-space: nowrap; display: inline-block;'>$item</span>";
                        }
                    }
                    return $formatted;
                }
            ),
            array(
                'db'		=> 'sample_description',
                'dt'		=> 'sample_description',
                'field'		=> 'sample_description',
                'formatter'	=> function($d, $row) {
                    $formatted = "";
                    if (!empty($d)) {
                        $arr = explode(',', $d);
                        foreach ($arr as $item) {
                            $formatted .= "<span style='padding: 3px 10px; background: #337AB7; color:white; margin: 0 0 5px 3px; border-radius: 2px; white-space: nowrap; display: inline-block;'>$item</span>";
                        }
                    }
                    return $formatted;
                }
            ),
			array(
				'db'		=> 'sample_id',
				'dt'		=> 'action',
				'field'		=> 'sample_id',
				'formatter'	=> function($d, $row) {
				    $action  = "<a href='#' class='text-blue edit hint--left hint--info' data-hint='"._t('global.edit')."'><i class='fa fa-pencil-square-o'></i></a>&nbsp;|&nbsp;";
				    $action .= "<a href='#' class='text-red remove hint--left hint--error' data-hint='"._t('global.remove')."'><i class='fa fa-trash'></i></a>";
					return $action;
				}
			)
		);

		$joinQuery = "";
		$extraWhere	= "";
		
		//config
		$db_config		= $this->load->database('default', TRUE); 
		$sql_details	= array(
			'user'	=> $db_config->username,
			'pass'	=> $db_config->password,
			'port'	=> $db_config->port,
			'db'	=> $db_config->database,
			'host'	=> $db_config->hostname
		);
		
		$result = DataTable::simple( $data->reqData, $sql_details, $table, $primaryKey, $columns, $joinQuery, $extraWhere );

		for ($i=0; $i < count($result['data']); $i++) {
			$result['data'][$i]['number'] = $i + 1;
		}

		return $result;
	}

	/**
	 * Get Sample for Laboratory using Datatable
	 */
	public function view_std_sample_comment($data) {
		$table		= '_view_camlis_sample_comment';		
		$primaryKey	= '"ID"';

		$columns	= array(
			array(
				'db'		=> 'ID',
				'dt'		=> 'checkbox',
				'field'		=> 'ID',
				'formatter'	=> function($d, $row) {
					return "<center><input type='checkbox' value='".$d."' /></center>";
				}
			),
			array(
				'db'		=> 'comment',
				'dt'		=> 'comment',
				'field'		=> 'comment'
			)
		);

		$joinQuery 			= "";
		$psample_id 		= isset($data->reqData['patient_sample_id']) ? $data->reqData['patient_sample_id'] : '';
		$dep_result_opt 	= isset($data->reqData['dep_result_opt']) ?$data->reqData['dep_result_opt'] : '';
		$sam_result_opt 	= isset($data->reqData['sam_result_opt']) ?$data->reqData['sam_result_opt'] : '';
        //patient_sample_id = $psample_id
		$extraWhere			= " department_id in(".$dep_result_opt.")
						AND sample_id in(".$sam_result_opt.")
						GROUP BY comment , \"ID\" ";

		//config
		$db_config		= $this->load->database('default', TRUE);
		$sql_details	= array(
			'user'	=> $db_config->username,
			'pass'	=> $db_config->password,
			'port'	=> $db_config->port,
			'db'	=> $db_config->database,
			'host'	=> $db_config->hostname
		);
		//$result = array();
		$result = DataTable::simple( $data->reqData, $sql_details, $table, $primaryKey, $columns, $joinQuery, $extraWhere );

		return $result;
	}

    /*public function view_std_sample_comment($data) {
        $table		= 'camlis_std_sample_comment';
        $primaryKey	= 'ID';

        $columns	= array(
            array(
                'db'		=> 'sample_comment.ID',
                'dt'		=> 'checkbox',
                'field'		=> 'ID',
                'formatter'	=> function($d, $row) {
                    return "<center><input type='checkbox' value='".$d."'></center>";
                }
            ),
            array(
                'db'		=> 'comment.comment',
                'dt'		=> 'comment',
                'field'		=> 'comment'
            )
        );

        $joinQuery = " FROM camlis_std_sample_comment AS sample_comment
					   INNER JOIN camlis_std_comment AS comment ON sample_comment.comment_id = comment.ID
					   INNER JOIN camlis_std_department_sample AS dsample ON sample_comment.dep_sample_id = dsample.ID
					   INNER JOIN camlis_std_sample_test AS sample_test ON dsample.ID = sample_test.department_sample_id
					   INNER JOIN camlis_patient_sample_tests AS ptest ON ptest.sample_test_id = sample_test.ID
					 ";
        $psample_id = isset($data->reqData['patient_sample_id']) ? $data->reqData['patient_sample_id'] : '';
        $dep_result_opt = isset($data->reqData['dep_result_opt']) ?$data->reqData['dep_result_opt'] : '';
        $sam_result_opt = isset($data->reqData['sam_result_opt']) ?$data->reqData['sam_result_opt'] : '';

        $extraWhere	= " sample_comment.status = TRUE
						AND comment.status = TRUE
						AND dsample.status = TRUE
						AND sample_test.status = TRUE
						AND ptest.status = TRUE
						AND ptest.patient_sample_id = $psample_id
						AND dsample.department_id in(".$dep_result_opt.")
						AND dsample.sample_id in(".$sam_result_opt.")
						GROUP BY comment.comment";

        //config
        $db_config		= $this->load->database('default', TRUE);
        $sql_details	= array(
            'user'	=> $db_config->username,
            'pass'	=> $db_config->password,
            'port'	=> $db_config->port,
            'db'	=> $db_config->database,
            'host'	=> $db_config->hostname
        );

        $result = DataTable::simple( $data->reqData, $sql_details, $table, $primaryKey, $columns, $joinQuery, $extraWhere );

        return $result;
    }*/
}
