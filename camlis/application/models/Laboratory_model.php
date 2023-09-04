<?php
defined('BASEPATH') or die('Access denied.');
class Laboratory_model extends MY_Model {
	public function __construct() {
		parent::__construct();
		$this->load->database();
	}

    /**
     * Check whether current lab is exist
     * @param $name_en
     * @param $name_kh
     * @param $lab_code
     * @param $ignore_id
     * @return boolean
     */
	public function is_unique_laboratory($name_en, $name_kh, $lab_code, $ignore_id = NULL) {
        $this->db->group_start();
        $this->db->where('name_en', $name_en);
        $this->db->or_where('name_kh', $name_kh);
        $this->db->or_where('lab_code', $lab_code);
        $this->db->group_end();
        !$ignore_id or $this->db->where('"labID" !=', $ignore_id);
        return $this->db->get('camlis_laboratory')->num_rows() == 0;
    }

	/**
	 * Get Labratory
	 * @param null $laboratory_id
	 * @param array $extraWhere
	 * @return mixed
	 */
	public function get_laboratory($laboratory_id = NULL, $extraWhere = FALSE) {
		$laboratory_id = (array)$laboratory_id;

		$this->db->select('lab."labID", lab.hf_code, lab.name_en, lab.name_kh, lab.lab_code, lab.address_en, lab.address_kh, lab.photo, lab.sample_number');
		$this->db->where('lab.status', 't');
		
		!$extraWhere OR $this->db->where($extraWhere);
		if (count($laboratory_id) > 0) $this->db->where_in('lab."labID"', $laboratory_id);
		$this->db->order_by('"labID"', 'ASC'); // added 23 03 2021
		$query = $this->db->get('camlis_laboratory AS lab');
		
		return $query->result();
	}
	
	public function get_report_name($obj) {
		 $sql = "select 
					pr.id,
					pr.report_name,
					pr.url, 
				   COALESCE(pru.user_id,0) as is_assign
					
				from permission_report pr 
				left join permission_report_user pru on pru.perm_report_id = pr.id and user_id = ?";

		$query = $this->db->query($sql,array($obj->user_id));
		return $query->result();
	}
	/* delete insert assign report to user */
	public function in_del_assing_report($obj) {
		if($obj->status == "insert"){
			$data["perm_report_id"] = $obj->rep_id;
			$data["user_id"] = $obj->user_id; 
			$this->db->insert("permission_report_user",$data);
		}else if($obj->status == "delete"){
			$this->db->where("perm_report_id",$obj->rep_id);
			$this->db->where("user_id",$obj->user_id);
			$this->db->delete("permission_report_user");
		}
	}

	/**
	 * Add laborartory
	 * @param $data
	 * @return mixed
	 */
	public function add_laboratory($data) {
		$this->db->set('"entryBy"', $this->user_id);
		$this->db->set('"entryDate"', '"'.$this->timestamp.'"');
		$this->db->set($data);
		$this->db->insert('camlis_laboratory');
		
		return $this->db->insert_id();
	}

	/**
	 * Update laborartory
	 * @param number $laboratory_id
	 * @param $data
	 * @return mixed
	 */
	public function update_laboratory($laboratory_id, $data) {
		$this->db->set('"modifiedBy"', $this->user_id);
		$this->db->set('"modifiedDate"','"'.$this->timestamp.'"' );
		$this->db->set($data);
		$this->db->where('status', 't');
		$this->db->where('"labID"', $laboratory_id);
		$this->db->update('camlis_laboratory');

		return $this->db->affected_rows();
	}

    /**
     * Set Variables
     * @param $data
     */
	public function set_variables($data) {
	    $values = array();
	    foreach ($data as $row) {
	        $_tmp     = "(";
	        $_tmp    .= (isset($row['lab_id']) && $row['lab_id'] > 0 ? $row['lab_id'] : $this->laboratory_id).", ";
	        $_tmp    .= "'".$row['data_key']."', ";
	        $_tmp    .= "'".$row['value']."', ";
	        $_tmp    .= "'".$this->user_id."', ";
	        $_tmp    .= "'".$this->timestamp."', ";
	        $_tmp    .= "'".$row['status']."'";
	        $_tmp    .= ")";
	        $values[] = $_tmp;
        }
        $sql  = "INSERT INTO camlis_laboratory_variables (lab_id, data_key, \"value\", entry_by, entry_date, \status\") VALUES ".implode(", ", $values);
		//$sql .= "ON DUPLICATE KEY UPDATE value = VALUES(value), status = VALUES(status),";
		$sql .= "ON CONFLICT (\"lab_id\") DO UPDATE SET value = EXCLUDED.value, status = EXCLUDED.status,";
	    $sql .= "modified_by = ".$this->user_id.",";
	    $sql .= "modified_date = '".$this->timestamp."'";
	    $this->db->query($sql);
	    return $this->db->affected_rows();
    }

    /**
     * Get Variables
     * @param $data_key
     * @param $lab_id
     * @return
     */
    public function get_variables($data_key = "", $lab_id = 0) {
        $lab_id = $lab_id > 0 ? $lab_id : $this->laboratory_id;

        $this->db->select('data_key, value, status');
        $this->db->where('lab_id', $lab_id);
        if (!empty($data_key)) $this->db->where('data_key', $data_key);
        $query = $this->db->get('camlis_laboratory_variables');

        if (!empty($data_key) && $query) {
            $result = $query->row_array();
            return $result;
        } else if ($query) {
            $result = $query->result_array();
            return array_column($result, NULL, 'data_key');
        }

        return NULL;
    }
	
	// 
	function lookup_user($obj){
		$sql = "  
				select *
				from camlis_aauth_users
				where fullname like ? 
		";
		$result=$this->db->query($sql,array('%'.$obj->val.'%'))->result();
        return $result;
	}
	
	/**
	 * Get Sample for Laboratory using Datatable
	 */
	public function view_all_laboratory($data) {
		$table		= 'camlis_laboratory';
		$primaryKey	= '"labID"';
		
		$columns	= array(
			array(
				'db'		=> 'labID',
				'dt'		=> 'labID',
				'field'		=> 'labID'
			),
			array(
				'db'		=> 'hf_code',
				'dt'		=> 'hf_code',
				'field'		=> 'hf_code'
			),
			array(
				'db'		=> 'name_en',
				'dt'		=> 'name_en',
				'field'		=> 'name_en'
			),
            array(
                'db'		=> 'name_kh',
                'dt'		=> 'name_kh',
                'field'		=> 'name_kh'
            ),
            array(
                'db'		=> 'lab_code',
                'dt'		=> 'lab_code',
                'field'		=> 'lab_code'
            ),
			array(
				'db'		=> 'address_en',
				'dt'		=> 'address_en',
				'field'		=> 'address_en'
			),
			array(
				'db'		=> 'address_kh',
				'dt'		=> 'address_kh',
				'field'		=> 'address_kh'
			),
			array(
				'db'		=> 'labID',
				'dt'		=> 'action',
				'field'		=> 'labID',
				'formatter'	=> function($d, $row) {
				    $action  = "<a href='#' class='text-blue edit hint--left hint--info' data-hint='"._t('global.edit')."'><i class='fa fa-pencil-square-o'></i></a>&nbsp;|&nbsp;";
					$action .= "<a href='#' class='text-red remove hint--left hint--error' data-hint='"._t('global.remove')."'><i class='fa fa-trash'></i></a>";
				    return $action;
				}
			),
            array(
                'db'		=> 'sample_number',
                'dt'		=> 'sample_number',
				'field'		=> 'sample_number',
				'type'		=> 'Number'
            )
		);
		
		$extraWhere	= " status = 't' ";
		
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