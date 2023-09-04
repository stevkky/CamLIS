<?php
defined('BASEPATH') OR die('Permission denied.');
class Test_model extends MY_Model {
	public function __construct() {
		parent::__construct();
		$this->load->database();
		$this->load->library('DataTable');
	}

	/**
	 * Get Standard Test
	 * @param bool $condition
	 */
	public function get_std_test($condition = FALSE) {
		$this->db->select('test."ID" AS test_id, test.test_name');
		$this->db->where('test.status', TRUE);
		!$condition OR $this->db->where($condition);
		return $this->db->get('camlis_std_test AS test')->result();
	}
	
	/**
	 * Get Standard Sample Test List
	 * @param array $condition Condition to get Standard Test
	 * @param bool $hierarchy Recursively get all Header and Child
	 */
	public function get_std_sample_test($condition = FALSE, $hierarchy = FALSE) {
		if ($hierarchy === TRUE && !isset($condition['sample_test."testPID"'])) {
			$condition['sample_test."testPID"'] = 0;
		}

		$sample_test = array();
		$this->db->select('
		    sample_test."ID" AS sample_test_id, 
			test."ID" AS test_id, 
			test.test_name,
			dep."ID" AS department_id,
			dep.department_name,
			sample."ID" AS sample_id,
			sample.sample_name,
			dsample."ID" AS dep_sample_id,
			dsample.show_weight,
			sample_test."testPID",
			sample_test.is_heading,
			sample_test.unit_sign,
			sample_test.field_type,
			sample_test.group_result,
			sample_test.default_select,
			sample_test.order AS test_order,
			sample_test.formula,
			NULLIF(_tmp.child_count, 0) AS child_count			
		');
		$this->db->from('camlis_std_sample_test AS sample_test');
		$this->db->join('camlis_std_department_sample AS dsample', 'dsample."ID" = sample_test.department_sample_id', 'inner');
		$this->db->join('camlis_std_department AS dep', 'dsample.department_id = dep."ID"', 'inner');
		$this->db->join('camlis_std_sample AS sample', 'dsample.sample_id = sample."ID"', 'inner');
		$this->db->join('camlis_std_test AS test', 'sample_test.test_id = test."ID"', 'inner');
		$this->db->join('(SELECT "testPID", count(*) AS child_count FROM camlis_std_sample_test WHERE status = TRUE GROUP BY "testPID") _tmp', 'sample_test."ID" = _tmp."testPID"', 'left');
		$this->db->where('sample_test.status', TRUE);
		$this->db->where('test.status', TRUE);
		$this->db->where('dsample.status', TRUE);
		$this->db->where('dep.status', TRUE);
		$this->db->where('sample.status', TRUE);
		!$condition OR $this->db->where($condition);
		$this->db->order_by('dep.department_name', 'asc');
		$this->db->order_by('dsample.order', 'asc');
		$this->db->order_by('sample_test.order', 'asc');

		if ($hierarchy === FALSE) return $this->db->get()->result();

		//Recursively get all sample test (Header and Child)
		foreach ($this->db->get()->result() as $row) {
			if (isset($row->child_count) && (int)$row->child_count > 0) {
				$row->childs = $this->get_std_sample_test(array('sample_test."testPID"' => $row->sample_test_id), TRUE);
			}

			$sample_test[] = $row;
		}
		return $sample_test;
	}

	/**
	 * Add Standard Test Name
	 * @param $data Data to insert
	 * @return number New Test Id
	 */
	public function add_std_test_name($data) {
		$this->db->set($data);
		$this->db->set('entryBy', $this->user_id);
		$this->db->set('entryDate', $this->timestamp);
		$this->db->insert('camlis_std_test');

		return $this->db->insert_id();
	}

	/**
	 * Update Standard Test name
	 * @param $test_id
	 * @param $_data Data to be updated
	 * @return integer Number of Updated rows
	 */
	public function update_std_test_name($test_id, $_data) {
		$this->db->set($_data);
		$this->db->set('modifiedBy', $this->user_id);
		$this->db->set('modifiedDate', $this->timestamp);
		$this->db->where('ID', $test_id);
		$this->db->where('status', TRUE);
		$this->db->update('camlis_std_test');

		return $this->db->affected_rows();
	}

	/**
	 * Add Standard Sample Test
	 * @param $data Data to insert
	 * @return number New Test Id
	 */
	public function add_std_sample_test($data) {
		$this->db->set($data);
		$this->db->set('entryBy', $this->user_id);
		$this->db->set('entryDate', $this->timestamp);
		$this->db->insert('camlis_std_sample_test');
		
		return $this->db->insert_id();
	}

	/**
	 * Update Standard Sample Test
	 * @param $data Data to be updated
	 * @return integer Number of Updated rows
	 */
	public function update_std_sample_test($_data, $test_id) {
		$this->db->set($_data);
		$this->db->set('"modifiedBy"', $this->user_id);
		$this->db->set('"modifiedDate"', $this->timestamp);
		$this->db->where('"ID"', $test_id);
		$this->db->where('status', TRUE);
		$this->db->update('camlis_std_sample_test');
		
		return $this->db->affected_rows();
	}
	
	/**
	 * Upate Test that has been assigned to Sample in each Lab
	 * @param  object $_data Data for Insert
	 * @return integer ID of new Sample Test
	 */
	public function update_lab_sample_test($lab_stest_id, $_data) {
		$this->db->set($_data);
		$this->db->where('ID', $lab_stest_id);
		$this->db->update('camlis_lab_sample_test');
		
		return $this->db->affected_rows();
	}

	/**
	 * Get Ref. Range of Standard Test
	 * @param $sample_test_id
	 * @return object DB Query
	 */
	public function get_std_sample_test_ref_range($sample_test_id) {
		$this->db->select('
			nv."ID" AS ref_id,
			nv.sample_test_id,
			nv.patient_type,
			nv.range_sign,
			nv.min_value,
			nv.max_value,
			ptype.type,
			ptype.gender,
			ptype.min_age,
			ptype.max_age,
			ptype.min_age_unit,
			ptype.max_age_unit,
			ptype.is_equal
		');
		$this->db->from('camlis_std_normal_value AS nv');
		$this->db->join("camlis_std_patient_type AS ptype", "nv.patient_type = ptype.ID", "inner");
		$this->db->where('nv.status', TRUE);
		$this->db->where('ptype.status', TRUE);
		if (is_array($sample_test_id) && count($sample_test_id) > 0) {
			$this->db->where_in('nv.sample_test_id', $sample_test_id);
		} else if ((int)$sample_test_id > 0) {
			$this->db->where('nv.sample_test_id', $sample_test_id);
		} else {
			return null;
		}

		return $this->db->get()->result();
	}
	
	/**
	 * Set Reference Range of each test 
	 * @param  array $_data Require Data
	 * @return integer Number of inserted rows
	 */
	public function add_std_sample_test_ref_range($_data) {
		$_data = (array)$_data;
		foreach ($_data as $index => $row) {
			$row['entryBy'] = $this->user_id;
			$row['entryDate'] = $this->timestamp;
			$_data[$index] = $row;
		}
		$this->db->insert_batch('camlis_std_normal_value', $_data);
		
		return $this->db->affected_rows();
	}
	
	/**
	 * Update Reference Range of Standard Test
	 * @param  array $_data Data to be updated
	 * @return integer Number of inserted rows
	 */
	public function update_std_sample_test_ref_range($_data, $test_id) {
		$_data = (array)$_data;
		$this->db->where('status', TRUE);
		//$this->db->where('test_id', $test_id);
        $this->db->where('sample_test_id', $test_id);
		foreach ($_data as $index => $row) {
			$row['modifiedBy'] = $this->user_id;
			$row['modifiedDate'] = $this->timestamp;
			$_data[$index] = $row;
		}

		$this->db->update_batch('camlis_std_normal_value', $_data, 'patient_type');

		return $this->db->affected_rows();
	}

	/**
	 * Delete Ref. Ranges of Standard Test
	 * @param $sample_test_id Sample Test ID
	 * @param bool [$patient_types = FALSE] Patient Types ID
	 * @param bool [$match_patient_types = TRUE] IF TRUE, delete Ref. Ranges with provided patient types, FALSE delete Ref. Ranges that are not in provided patient types
	 * @return number Number of Deleted Rows
	 */
	public function delete_std_sample_test_ref_range($sample_test_id, array $patient_types = array(), $match_patient_types = TRUE) {
		$this->db->where('status', TRUE);
		$this->db->where('sample_test_id', $sample_test_id);

		if ($patient_types && count($patient_types) > 0) {
			if ($match_patient_types)
				$this->db->where_in('patient_type', $patient_types);
			else
				$this->db->where_not_in('patient_type', $patient_types);
		}
		$this->db->set("status", FALSE);
		$this->db->set('"modifiedBy"', $this->user_id);
		$this->db->set('"modifiedDate"', $this->timestamp);
		$this->db->update('camlis_std_normal_value');
		
		return $this->db->affected_rows();
	}
	
	/**
	 * Delete Patient Sample
	 * @param  integer $pSample_ID Patient's ID
	 * @return integer Number of Deleted Rows
	 */
	public function delete_psample_test($psample_id) {
		$this->db->select('ID');
		$this->db->where(array('status' => TRUE, 'psample_id' => $psample_id));
		$query	= $this->db->get('camlis_patient_sample_tests');
		$ids	= $query->result();
		$id_arr	= array();
		foreach($ids as $row) {
			$id_arr[] = $row->ID;
		}
		
		if (count($id_arr) > 0) {
			$this->db->set('status', FALSE);
			$this->db->where_in('ptest_id', $id_arr);
			$this->db->update('camlis_ptest_result');
		}
		
		$this->db->set('status', FALSE);
		$this->db->where('psample_id', $psample_id);
		$this->db->update('camlis_patient_sample_tests');
		return $this->db->affected_rows();
	}

	/**
	 * Get Field Type
	 * @return mixed
	 */
	public function get_test_fieldType() {
		$query = $this->db->get_where('camlis_std_test_field_type', array('status' => TRUE));
		return $query->result();
	}

	/**
	 * Add Standard Group Result
	 * @param $data Data to insert
	 * @return number New Test Id
	 */
	public function add_std_group_result($data) {
		$this->db->set($data);
		$this->db->set('entryBy', $this->user_id);
		$this->db->set('entryDate', $this->timestamp);
		$this->db->insert('camlis_std_group_result');

		return $this->db->insert_id();
	}

	/**
	 * Update Standard Test name
	 * @param $group_result_id
	 * @param $_data Data to be updated
	 * @return integer Number of Updated rows
	 */
	public function update_std_group_result($group_result_id, $_data) {
		$this->db->set($_data);
		$this->db->set('modifiedBy', $this->user_id);
		$this->db->set('modifiedDate', $this->timestamp);
		$this->db->where('ID', $group_result_id);
		$this->db->where('status', TRUE);
		$this->db->update('camlis_std_group_result');

		return $this->db->affected_rows();
	}

    /**
     * Check unique test payment
     * @param $group_result
     * @param $payment_type_id
     * @param null $ignore_id
     * @return boolean
     */
	public function is_unique_lab_test_payment($group_result, $payment_type_id, $ignore_id = NULL) {
	    $this->db->where('group_result', $group_result);
	    $this->db->where('payment_type_id', $payment_type_id);
	    $this->db->where('lab_id', $this->laboratory_id);
	    $this->db->where('status', TRUE);
	    !$ignore_id OR $this->db->where('id !=', $ignore_id);
	    return $this->db->get('camlis_lab_test_payment')->num_rows() == 0;
    }

    /**
     * Add new lab test payment
     * @param $data
     */
    public function add_lab_test_payment($data) {
        foreach($data as $key => $d) {
            $d['lab_id'] 		= $this->laboratory_id;
            $d['entry_by'] 		= $this->user_id;
            $d['entry_date'] 	= $this->timestamp;
            $data[$key] 		= $d;
        }
        $this->db->trans_start();
        $this->db->insert_batch('camlis_lab_test_payment', $data);
        $this->db->trans_complete();
        return $this->db->trans_status();
    }

    /**
     * Update lab test payment
     * @param $id test payment id
     * @param $data
     * @return mixed
     */
    public function update_lab_test_payment($id, $data) {
        $this->db->set($data);
        $this->db->set('lab_id', $this->laboratory_id);
        $this->db->set('modified_by', $this->user_id);
        $this->db->set('modified_date', $this->timestamp);
        $this->db->where('id', $id);
        $this->db->where('lab_id', $this->laboratory_id);
        $this->db->where('status', TRUE);
        $this->db->update('camlis_lab_test_payment');
        return $this->db->affected_rows() > 0;
    }

    /**
     * Delete lab test payment
     * @param $id
     * @param $group_result
     * @param $payment_type_id
     * @return bool
     */
    public function delete_lab_test_payment($id = NULL, $group_result = NULL, $payment_type_id = NULL) {
        if ($id > 0) $this->db->where('id', $id);
        if (count($group_result) > 0) $this->db->where('group_result', $group_result);
        if ($payment_type_id > 0) $this->db->where('payment_type_id', $payment_type_id);
        $this->db->where('lab_id', $this->laboratory_id);
        $this->db->delete('camlis_lab_test_payment');
        return $this->db->affected_rows() > 0;
    }

    /**
     * Get test lab payment
     * @param null $laboratory_id
     * @param null $group_result
     */
    public function get_lab_test_payment($group_result = NULL, $laboratory_id = NULL) {
        $laboratory_id = $laboratory_id > 0 ? $laboratory_id : $this->laboratory_id;
        $this->db->select('test_payment.id, test_payment.group_result, test_payment.price, test_payment.payment_type_id, payment_type.name AS payment_type_name');
        $this->db->from('camlis_lab_test_payment AS test_payment');
        $this->db->join('camlis_std_payment_type AS payment_type', 'test_payment.payment_type_id = payment_type.id', 'inner');
        $this->db->where('test_payment.status', TRUE);
        $this->db->where('test_payment.lab_id', $laboratory_id);
        !$group_result OR $this->db->where('test_payment.group_result', $group_result);
        return $this->db->get()->result_array();
    }

	/**
	 * Get Test Group Result
	 * @return mixed
	 */
	public function get_std_group_result($condition = FALSE) {
		$this->db->select('ID, group_name');
		$this->db->where('status', TRUE);
		!$condition OR $this->db->where($condition);
		$query = $this->db->get('camlis_std_group_result');
		return $query->result_array();
	}

    /**
     * Get group result of sample test
     */
	public function get_sample_test_group_result() {
	    $this->db->select('group_result');
	    $this->db->where('LENGTH(group_result) >', 0);
	    $this->db->group_by('group_result');
	    $this->db->order_by('group_result');
	    $query = $this->db->get('camlis_std_sample_test');
	    return $query->result_array();
    }
	
	/**
	 * Get Sample for Laboratory using Datatable
	 */
	public function view_all_std_sample_test($data) {
		$_model = $this;
		$table		= 'test_v';
		$primaryKey	= 'ID';
		
		$columns	= array(
			array(
				'db'		=> 'ID',
				'dt'		=> 'sample_test_id',
				'field'		=> 'ID'
			),
			array(
				'db'		=> 'order',
				'dt'		=> 'order',
				'field'		=> 'order'
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
				'db'		=> 'test_name',
				'dt'		=> 'test_name',
				'field'		=> 'test_name'
			),
			array(
				'db'		=> 'unit_sign',
				'dt'		=> 'unit_sign',
				'field'		=> 'unit_sign'
			),
			array(
				'db'		=> 'type',
				'dt'		=> 'field_type',
				'field'		=> 'type'
			),
            array(
                'db'		=> 'field_type',
                'dt'		=> 'field_type_id',
                'field'		=> 'field_type'
            ),
			array(
				'db'		=> 'default_select',
				'dt'		=> 'default_select',
				'field'		=> 'default_select',
				'formatter' => function($d, $row) {
					if ((int)$d == 1) return "<center><i class='fa fa-check-circle text-green' style='font-size: 11pt'></i></center>";

					return '.';
				}
			),
			array(
				'db'		=> 'is_heading',
				'dt'		=> 'is_heading',
				'field'		=> 'is_heading',
				'type'		=> 'Boolean',
				'formatter' => function($d, $row) {
					if ((int)$d == 1) return "<center><i class='fa fa-check-circle text-green' style='font-size: 11pt'></i></center>";

					return '';
				}
			),
			array(
				'db'		=> 'testPID',
				'dt'		=> 'header',
				'field'		=> 'testPID',
				'type'		=> 'Number',
				'formatter' => function($d, $row) use($_model) {
					$test = $_model->get_std_sample_test(array('sample_test.ID' => $d));
					if (count($test) > 0) return $test[0]->test_name;

					return '';
				}
			),
			array(
				'db'		=> 'ID',
				'dt'		=> 'action',
				'field'		=> 'ID',
				'formatter' => function($d, $row) use($data) {
					$formatted  = "<a href='#' class='text-blue edit hint--left hint--info' data-hint='"._t('global.edit')."'><i class='fa fa-pencil-square-o'></i></a>";
                    if (isset($data->reqData['allow_remove']) && $data->reqData['allow_remove'] == 1 && $this->aauth->is_admin()) {
                        $formatted .= "&nbsp;|&nbsp;<a href='#' class='text-red remove hint--left hint--error' data-hint='"._t('global.remove')."'><i class='fa fa-trash'></i></a>";
                    }
					return $formatted;
				}
			)
		);

		$joinQuery = "";
		$extraWhere = "";
		if (isset($data->reqData['department_id']) && $data->reqData['department_id'] > -1) {
			$extraWhere .= " department_id = ".$data->reqData['department_id'];
		}
		if (isset($data->reqData['dep_sample_id']) && $data->reqData['dep_sample_id'] > -1) {
			$extraWhere .= " AND department_sample_id = ".$data->reqData['dep_sample_id'];
		}
		if (isset($data->reqData['field_type']) && count($data->reqData['field_type']) > 0) {
            $where = "field_type IN (".implode(', ', $data->reqData['field_type']).")";
            $extraWhere .= empty($extraWhere) ? $where : " AND ".$where;
        }
		
		//config
		$db_config   = $this->load->database('default', TRUE); 
		$sql_details = array(
			'user' => $db_config->username,
			'pass' => $db_config->password,
			'port' => $db_config->port,
			'db'   => $db_config->database,
			'host' => $db_config->hostname
		);
		
		$result = DataTable::simple( $data->reqData, $sql_details, $table, $primaryKey, $columns, $joinQuery, $extraWhere );
		
		for ($i=0; $i < count($result['data']); $i++) {
			$result['data'][$i]['number'] = $i + 1;
		}
		
		return $result;
	}

    /**
     * View lab test payment
     * @param $data
     * @return mixed
     */
	public function view_lab_test_payment($data) {
        $table = "camlis_lab_test_payment";
        $primaryKey = "id";

        $columns	= [
            [
                'db'		=> 'test_payment.id',
                'dt'		=> 'id',
                'field'		=> 'id'
            ],
            [
                'db'		=> 'test_payment.payment_type_id',
                'dt'		=> 'payment_type_id',
                'field'		=> 'payment_type_id'
            ],
            [
                'db'		=> 'test_payment.group_result',
                'dt'		=> 'group_result',
                'field'		=> 'group_result'
            ],
            array(
                'db'		=> 'test_payment.id',
                'dt'		=> 'action',
                'field'		=> 'id',
                'formatter' => function($d, $row) use($data) {
                    $formatted  = "<a href='#' class='text-blue edit hint--left hint--info' data-hint='"._t('global.edit')."'><i class='fa fa-pencil-square-o'></i></a>";
                    $formatted .= "&nbsp;|&nbsp;<a href='#' class='text-red remove hint--left hint--error' data-hint='"._t('global.remove')."'><i class='fa fa-trash'></i></a>";
                    return $formatted;
                }
            )
        ];

		$joinQuery   = "FROM camlis_lab_test_payment AS test_payment INNER JOIN camlis_std_payment_type AS payment_type ON test_payment.payment_type_id = payment_type.id";
		//$joinQuery   = " INNER JOIN camlis_std_payment_type AS payment_type ON test_payment.payment_type_id = payment_type.id";
        $extraWhere  = " test_payment.status = TRUE AND test_payment.lab_id = ".$this->laboratory_id;
        $extraWhere .= " GROUP BY test_payment.group_result, test_payment.id ";
		$extraWhere .= " , test_payment.payment_type_id"; // added 14 Dec 2020
        //config
        $db_config   = $this->load->database('default', TRUE);
        $sql_details = array(
            'user' => $db_config->username,
            'pass' => $db_config->password,
            'port' => $db_config->port,
            'db'   => $db_config->database,
            'host' => $db_config->hostname
        );

        $result = DataTable::simple( $data, $sql_details, $table, $primaryKey, $columns, $joinQuery, $extraWhere );
        for ($i=0; $i < count($result['data']); $i++) {
            $result['data'][$i]['number'] = $i + 1;
        }

        return $result;
    }
}