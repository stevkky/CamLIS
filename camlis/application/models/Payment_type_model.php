<?php
defined('BASEPATH') OR die('Access denied!');

class Payment_type_model extends MY_Model
{
    public function __construct() {
        parent::__construct();
    }

    /**
     * Check for unique name
     * @param $name
     * @param null $ignore_id
     * @return boolean
     */
    public function is_unique_payment_type($name, $ignore_id = NULL) {
        $this->db->where('name', $name);
        $this->db->where('status', TRUE);
        !$ignore_id OR $this->db->where('id !=', $ignore_id);
        $query = $this->db->get('camlis_std_payment_type');
        return $query->num_rows() == 0;
    }

    /**
     * Get standard payment type
     * @return mixed
     */
    public function get_std_payment_type() {
        $this->db->select('id, name');
        $this->db->order_by('name');
        return $this->db->get_where('camlis_std_payment_type', ['status' => TRUE])->result_array();
    }

    /**
     * Get Laboratory Payment type
     * @param null $laboratory_id
     * @return mixed
     */
    public function get_lab_payment_type($laboratory_id = NULL) {
        $laboratory_id = $laboratory_id > 0 ? $laboratory_id : $this->laboratory_id;
        $this->db->select('payment_type.id, payment_type.name');
        $this->db->from('camlis_std_payment_type AS payment_type');
        $this->db->join('camlis_lab_payment_type AS lab_payment_type', 'payment_type.id = lab_payment_type.payment_type_id', 'inner');
        $this->db->where('payment_type.status', TRUE);
        $this->db->where('lab_payment_type.lab_id', $laboratory_id);
        return $this->db->get()->result_array();
    }

    /**
     * Add new payment type
     * @param $data
     */
    public function add_std_payment_type($data) {
        $this->db->set($data);
        $this->db->set('entry_date', $this->timestamp);
        $this->db->set('entry_by', $this->user_id);
        $this->db->insert('camlis_std_payment_type');
        return $this->db->insert_id();
    }

    /**
     * Update payment type
     * @param $id
     * @param $data
     * @return mixed
     */
    public function update_std_payment_type($id, $data) {
        $this->db->set($data);
        $this->db->set('modified_date', $this->timestamp);
        $this->db->set('modified_by', $this->user_id);
        $this->db->where('id', $id);
        $this->db->update('camlis_std_payment_type');
        return $this->db->affected_rows() > 0;
    }

    /**
     * Delete lab payment type
     * @param null $id
     */
    public function delete_lab_payment_type($id = NULL) {
        $this->db->where('lab_id', $this->laboratory_id);
        !$id OR $this->db->where('id', $id);
        $this->db->delete('camlis_lab_payment_type');
        return $this->db->affected_rows();
    }

    /**
     * Assign lab payment type
     * @param $data
     */
    public function assign_lab_payment_type($data) {
        $this->db->insert_batch('camlis_lab_payment_type', $data);
        return $this->db->affected_rows();
    }

    /**
     * Get Standard Payment Type (DataTable)
     */
    public function view_std_payment_type($data) {
        $table		= 'camlis_std_payment_type';
        $primaryKey	= 'ID';

        $columns	= array(
            array(
                'db'		=> 'id',
                'dt'		=> 'id',
                'field'		=> 'id'
            ),
            array(
                'db'		=> 'name',
                'dt'		=> 'name',
                'field'		=> 'name'
            ),
            array(
                'db'		=> 'id',
                'dt'		=> 'action',
                'field'		=> 'id',
                'formatter'	=> function($d, $row) {
                    return "<a href='#' class='text-blue edit hint--left hint--info' data-hint='"._t('global.edit')."'><i class='fa fa-pencil-square-o'></i></a>&nbsp;|&nbsp;
							<a href='#' class='text-red remove hint--left hint--error' data-hint='"._t('global.remove')."'><i class='fa fa-trash'></i></a>";
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

    /**
     * Get lab Payment Type (DataTable)
     */
    public function view_lab_payment_type($data) {
        $table		= 'camlis_lab_payment_type';
        $primaryKey	= 'ID';

        $columns	= array(
            array(
                'db'		=> 'lab_ptype.id',
                'dt'		=> 'lab_payment_type_id',
                'field'		=> 'id'
            ),
            array(
                'db'		=> 'std_ptype.name',
                'dt'		=> 'name',
                'field'		=> 'name'
            ),
            array(
                'db'		=> 'lab_ptype.id',
                'dt'		=> 'action',
                'field'		=> 'id',
                'formatter'	=> function($d, $row) {
                    return "<a href='#' class='text-red remove hint--left hint--error' data-hint='"._t('global.remove')."'><i class='fa fa-trash'></i></a>";
                }
            )
        );

        $joinQuery  = "FROM camlis_std_payment_type AS std_ptype INNER JOIN camlis_lab_payment_type AS lab_ptype ON std_ptype.id = lab_ptype.payment_type_id";
        $extraWhere	= "std_ptype.status = TRUE AND lab_ptype.lab_id =".$this->laboratory_id;

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