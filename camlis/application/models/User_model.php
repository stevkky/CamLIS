<?php
defined('BASEPATH') OR die('Permission denied.');
class User_model extends MY_Model {
	public function __construct() {
		parent::__construct();
	}

    /**
     * Get Uer
     * @param null $username
     * @param null $user_id
     * @param bool $match_user_id
     */
	public function get_user($username = NULL, $user_id = NULL, $match_user_id = TRUE) {
	    $this->db->where('status', TRUE);
	    $this->db->where('banned', FALSE);
        !$username OR $this->db->where("username", $username);
        if ($match_user_id && $user_id) {
            $this->db->where("id", $user_id);
        }
        else if(!$match_user_id && $user_id) {
            $this->db->where("id !=", $user_id);
        }
        return $this->db->get(self::$CamLIS_db.'.camlis_aauth_users')->result_array();
    }

    /**
     * Verify Password
     * @param $password
     * @param $user_id
     */
    public function verify_password($password, $user_id) {
        $this->db->where('banned', FALSE);
        $this->db->where('id', $user_id);
        $this->db->where('pass', $password);
        $query = $this->db->get(self::$CamLIS_db.'.camlis_aauth_users');

        if ($query->num_rows() > 0) return TRUE;

        return FALSE;
    }

    /**
     * Create New User
     * @param $data
     */
	public function add_user($data) {
	    $this->db->set($data);
        $this->db->set('entry_by', $this->user_id);
        $this->db->set('entry_date', $this->timestamp);
        $this->db->insert(self::$CamLIS_db.'.camlis_aauth_users');
        return $this->db->insert_id();
    }

    /**
     * Update User
     * @param $user_id
     * @param $data
     */
    public function update_user($user_id, $data) {
        $this->db->set($data);
        $this->db->set('modified_by', $this->user_id);
        $this->db->set('modified_date', $this->timestamp);
        $this->db->where('id', $user_id);
        $this->db->where('status', TRUE);
        $this->db->update(self::$CamLIS_db.'.camlis_aauth_users');
        return $this->db->affected_rows();
    }

	/**
	 * Assign user laboratory
	 * @param $user_id
	 * @param $laboratories
	 * @return mixed
	 */
	public function assign_user_laboratory($user_id, $laboratories) {
		$values = array();
		foreach ($laboratories as $lab) {
			$values[] = "(".$user_id.",".$lab.")";
		}


		$this->db->trans_start();
		//Delete Lab that are not in list
		$this->db->set('status', FALSE);
		$this->db->where('status', TRUE);
		$this->db->where('user_id', $user_id);
		if (is_array($laboratories) && count($laboratories) > 0) $this->db->where_not_in('lab_id', $laboratories);
		$this->db->update(self::$CamLIS_db.".camlis_user_laboratory");

		if (count($values) > 0) {
			$sql  = "INSERT INTO " . self::$CamLIS_db . ".camlis_user_laboratory (user_id, lab_id) VALUES " . implode(',', $values);
            //$sql .= " ON CONFLICT (user_id) DO UPDATE set status = TRUE";
            $sql .= " ON CONFLICT (user_id , lab_id) DO UPDATE set status = EXCLUDED.status , user_id = EXCLUDED.user_id , lab_id = EXCLUDED.lab_id";
			$this->db->query($sql);
		}
		$this->db->trans_complete();

		if ($this->db->trans_status() === TRUE) return 1;

		return 0;
	}

	/**
	 * Get Laboratory that assigned to user
	 * @param $uid
	 * @return mixed
	 */
	public function get_user_laboratory($user_id) {
		$this->db->where('status', TRUE);
		$this->db->where('user_id', $user_id);
		$query = $this->db->get(self::$CamLIS_db.'.camlis_user_laboratory');
		
		return $query->result();
	}

	public function delete_group_permission($group_id) {
        $this->db->where('group_id', $group_id);
        $this->db->delete('camlis_aauth_perm_to_group');
        return $this->db->affected_rows();
    }

    /**
     * Assign group permission
     * @param $permissions
     */
	public function assign_group_permission(array $permissions) {
        $this->db->insert_batch('camlis_aauth_perm_to_group', $permissions);
        return $this->db->affected_rows();
    }
	
	/**
	 * Get all camlis user
	 */
	public function view_all_user($data) {
		$table		= 'camlis_aauth_users';
		$primaryKey	= 'id';
		
		$columns	= array(
			array(
				'db'		=> 'users.id',
				'dt'		=> 'user_id',
				'field'		=> 'id'
			),
			array(
				'db'		=> 'fullname',
				'dt'		=> 'fullname',
				'field'		=> 'fullname'
			),
			array(
				'db'		=> 'username',
				'dt'		=> 'username',
				'field'		=> 'username'
			),
			array(
				'db'		=> 'email',
				'dt'		=> 'email',
				'field'		=> 'email'
			),
			array(
				'db'		=> 'phone',
				'dt'		=> 'phone',
				'field'		=> 'phone'
			),
			array(
				'db'		=> 'users.id',
				'dt'		=> 'action',
				'field'		=> 'id',
				'formatter' => function($d, $row) use ($data) {
				    $formatted  = "";
				    if (empty($data->reqData['current_laboratory'])) {
                        $formatted .= "<a href='#' class='text-blue hint hint--info hint--left assign_lab' data-hint='" . _t('user.assign-laboratory') . "'><i class='fa fa-building-o'></i></a> | ";
                    }
					$formatted .= "<a href='#' class='text-blue hint hint--info hint--left set_group' data-hint='"._t('user.set-role')."'><i class='fa fa-key'></i></a> | ";
					$formatted .= "<a href='#' class='text-blue hint hint--info hint--left edit' data-hint='"._t('global.edit')."'><i class='fa fa-pencil-square-o'></i></a> | ";
					$formatted .= "<a href='#' class='text-red hint hint--error hint--left remove' data-hint='"._t('global.remove')."'><i class='fa fa-trash'></i></a>";

					return $formatted;
				} 
			)
		);

		$joinType    = "LEFT";
		$extraWhere  = " users.status = TRUE AND users.banned = FALSE ";

		if (!empty($data->reqData['current_laboratory']) && is_numeric($data->reqData['current_laboratory']) && $data->reqData['current_laboratory'] > 0) {
            $extraWhere .= " AND lab.lab_id = ".$data->reqData['current_laboratory'];
            $joinType    = "INNER";
        }
		$extraWhere .= " GROUP BY users.id";

        $joinQuery  = " FROM $table AS users $joinType JOIN camlis_user_laboratory AS lab ON users.id = lab.user_id AND lab.status = TRUE";
		
		//config
		$db_config   = $this->load->database('default', TRUE);
		$sql_details = array(
			'user' => $db_config->username,
			'pass' => $db_config->password,
			'port' => $db_config->port,
			'db'   => $db_config->database,
			'host' => $db_config->hostname
		);
		
		$this->load->library('DataTablepg');
        
		$result = DataTablepg::simple( $data->reqData, $sql_details, $table, $primaryKey, $columns, $joinQuery, $extraWhere );
		
		for ($i=0; $i < count($result['data']); $i++) {
			$result['data'][$i]['number'] = $i + 1;
		}
		
		return $result;
	}

    /**
     * View all user roles
     */
	public function view_all_user_roles($data) {
	    $table = "camlis_aauth_groups";
	    $primaryKey = "id";

	    $columns = [
            [
                'db'		=> 'group.id',
                'dt'		=> 'id',
                'field'		=> 'id'
            ],
            [
                'db'		=> 'group.name',
                'dt'		=> 'name',
                'field'		=> 'name'
            ],
            [
                'db'		=> 'group.definition',
                'dt'		=> 'definition',
                'field'		=> 'definition'
            ],
            [
                'db'		=> 'group.default_page',
                'dt'		=> 'default_page',
                'field'		=> 'default_page'
            ],
            [
                'db'		=> 'group.is_predefined',
                'dt'		=> 'is_predefined',
                'field'		=> 'is_predefined',
                'formatter' => function($d) {
                    return $d > 0;
                }
            ],
            [
                'db'		=> "string_agg((perm.perm_id)::TEXT, ',')",
                'as'        => 'permissions',
                'dt'		=> 'permissions',
                'field'		=> 'permissions',
                'formatter' => function($d, $row) {
	                return !empty($d) ? explode(',', $d) : array();
                }
            ],
            [
                'db'		=> 'group.id',
                'dt'		=> 'action',
                'field'		=> 'id',
                'formatter' => function($d, $row) {
	                $formatted  = "<a href='#' class='text-blue hint--left hint--info edit' data-hint='"._t('global.edit')."'><i class='fa fa-pencil-square-o'></i></a>&nbsp;|&nbsp;";
	                $formatted .= "<a href='#' class='text-red hint--left hint--error remove' data-hint='"._t('global.remove')."'><i class='fa fa-trash'></i></a>";
	                return $formatted;
                }
            ],
        ];

	    $joinQuery = "FROM $table AS \"group\" LEFT JOIN camlis_aauth_perm_to_group AS perm ON \"group\".id = perm.group_id";
	    $groupBy = "\"group\".id";

        //config
        $db_config   = $this->load->database('default', TRUE);
        $sql_details = [
            'user' => $db_config->username,
            'pass' => $db_config->password,
            'port' => $db_config->port,
            'db'   => $db_config->database,
            'host' => $db_config->hostname
        ];

        $this->load->library('DataTable');
        $result = DataTable::simple( $data, $sql_details, $table, $primaryKey, $columns, $joinQuery, NULL, $groupBy );

        for ($i=0; $i < count($result['data']); $i++) {
            $result['data'][$i]['number'] = $i + 1;
        }

        return $result;
    }
    /**
     * Added 23-04-2021
     */
    public function get_rrt_user(){        
        $this->db->select('user.id, user.username');
        $this->db->from('camlis_aauth_users AS user');
        $this->db->join('camlis_aauth_user_to_group AS ugroup','user.id = ugroup.user_id','inner');
        
        $this->db->where('status', TRUE);
	    $this->db->where('banned', FALSE);
        $this->db->where('ugroup.group_id', 16);
        return $this->db->get()->result_array();
    }    
    public function get_province($user_id){
        $this->db->select('user.id,user,fullname, user.location, user.phone, user.province_code, pro.name_kh as province_name');
        $this->db->from('camlis_aauth_users AS user');
        $this->db->join('provinces AS pro','pro.code = user.province_code','left');
        
        $this->db->where('user.id', $user_id);	    
        return $this->db->get()->row_array();
    }
}