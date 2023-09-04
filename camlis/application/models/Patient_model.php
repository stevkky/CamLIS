<?php
defined('BASEPATH') OR die('Permission denied!');
class Patient_model extends MY_Model {
	public function __construct()
    {
        parent::__construct();
        $this->load->library('patientwebservice');
    }

	/**
	 * Get Standard patient type
	 * @param array $condition Condition to get Data
	 * @return object DB Query
	 */
	public function get_std_patient_type($condition = FALSE) {
		$this->db->where('status', TRUE);
		!$condition OR $this->db->where($condition);

		return $this->db->get('camlis_std_patient_type AS ptype')->result();
	}

	/**
	 * Add Standard Patient Type
	 * @param $_data Data to be inserted
	 * @return integer new inserted id
	 */
	public function add_std_patient_type($_data) {
		$this->db->set($_data);
		$this->db->set('"entryBy"', $this->user_id);
		$this->db->set('"entryDate"', $this->timestamp);
		$this->db->insert('camlis_std_patient_type');

		return $this->db->insert_id();
	}

	/**
	 * Add Standard Patient Type
	 * @param $ptype_id Patient Type id
	 * @param $_data Data to be inserted
	 * @return integer new inserted id
	 */
	public function update_std_patient_type($ptype_id, $_data) {
		$this->db->set($_data);
		$this->db->set('"modifiedBy"', $this->user_id);
		$this->db->set('"modifiedDate"', $this->timestamp);
		$this->db->where('status', TRUE);
		$this->db->where('"ID"', $ptype_id);
		$this->db->update('camlis_std_patient_type');

		return $this->db->affected_rows();
	}

    /**
     * Get outside Patient
     * @param $patient_id
     * @param bool $patient_code
     * @return mixed
     */
    public function get_outside_patient($patient_id, $patient_code = FALSE) {
        $this->db->select("
            patient.pid, 
            patient.patient_code AS patient_code, 
            patient.patient_name AS name, 
            CASE WHEN patient.sex = 1 THEN 'M' ELSE 'F' END AS sex,
            patient.dob,
            patient.phone,
            patient.province,
            patient.commune,
            patient.district,
            patient.village,
            provinces.name_en AS province_en,
            provinces.name_kh AS province_kh,
            districts.name_en AS district_en,
            districts.name_kh AS district_kh,
            communes.name_en AS commune_en,
            communes.name_kh AS commune_kh,
            villages.name_en AS village_en,
			villages.name_kh AS village_kh,
			con.name_en AS country_name_en,
			
			patient.country,
			patient.nationality,
			nat.nationality_en AS nationality_en,
			patient.residence,
			patient.is_positive_covid,
			patient.contact_with,
			patient.relationship_with_case,
			patient.date_arrival,
			patient.passport_number,
			patient.seat_number,
			patient.travel_in_past_30_days,
			patient.test_date,
			patient.is_contacted,
			patient.flight_number,
			patient.is_direct_contact,
			patient.country_name,
			patient.parent_code,
			vaccination_status,
			vaccine_id,
			first_vaccinated_date,
			occupation,
			second_vaccinated_date,
			third_vaccinated_date,
			second_vaccine_id,
			forth_vaccinated_date,
			third_vaccine_id
        ");
        $this->db->from('camlis_outside_patient AS patient');
        $this->db->join('provinces', 'provinces.code = patient.province', 'left');
        $this->db->join('districts', 'districts.code = patient.district', 'left');
        $this->db->join('communes', 'communes.code = patient.commune', 'left');
		$this->db->join('villages', 'villages.code = patient.village', 'left');
		$this->db->join('countries as con','patient.country = con.num_code','left'); // ADDED 02 DEC 2020
		$this->db->join('countries as nat','patient.nationality = nat.num_code','left'); // ADDED 02 DEC 2020
        $this->db->where('patient.status', 1);
        $this->db->where('lab_id', $this->laboratory_id);
        if ($patient_id) $this->db->where('patient.pid', $patient_id);
        if ($patient_code) $this->db->where('patient.patient_code', $patient_code);
				
        return $this->db->get()->row_array();
    }

	/**
	 * Add new outside patient
	 * @param $data
	 * @return mixed
	 */
	public function save_outside_patient($data) {
        $this->db->set($data);
        $this->db->set('lab_id', $this->laboratory_id);
        $this->db->set('entryBy', $this->user_id);
        $this->db->set('entryDate', $this->timestamp);
		$this->db->insert("camlis_outside_patient");
		return $this->db->insert_id();
	}

    /**
	 * Update outside patient
	 * @param $pid
	 * @param $data
	 * @return mixed
	 */
	public function update_outside_patient($pid, $data) {
        $this->db->set($data);
        $this->db->set('modifiedBy', $this->user_id);
        $this->db->set('modifiedDate', $this->timestamp);
        $this->db->where('pid', $pid);
        $this->db->where('lab_id', $this->laboratory_id);
		$this->db->update("camlis_outside_patient");

		return $this->db->affected_rows() > 0 ? TRUE : FALSE;
	}

    /**
     * checking existing patient code
     * @param $patient_code
     * @return int
     */
    public function patient_code_exist($patient_code) {
        $result = $this->db->get_where("camlis_outside_patient", ["patient_code" => $patient_code]);
        return $result->num_rows() > 0 ? TRUE : FALSE;
    }

	function isPatientIDManual($val){
	    $sql ="select 
                pid,patient_id 
                from camlis_outside_patient
                where 
                (case when patient_id!=0 then patient_id when patient_id=0 then pid end)='".$val."'
               ";
        $query = $this->db->query($sql)->result();
        if($query[0]->patient_id!=0){
            return "patient_id";
        }else{
            return "pid";

        }

    }

    /**
     * Get outside Patient
     * @param $pid
     * @return mixed
     */
    public function get_pmrs_patient($pid) {
        $this->db->select("
            patient.pid, 
            patient.pid AS patient_code, 
            patient.name, 
            patient.sex,
            patient.dob,
            patient.phone,
            patient.province,
            patient.commune,
            patient.district,
            patient.village,
            provinces.name_en AS province_en,
            provinces.name_kh AS province_kh,
            districts.name_en AS district_en,
            districts.name_kh AS district_kh,
            communes.name_en AS commune_en,
            communes.name_kh AS commune_kh,
            villages.name_en AS village_en,
            villages.name_kh AS village_kh
        ");
        $this->db->from('camlis_pmrs_patient AS patient');
        $this->db->join('provinces', 'provinces.code = patient.province', 'left');
        $this->db->join('districts', 'districts.code = patient.district', 'left');
        $this->db->join('communes', 'communes.code = patient.commune', 'left');
        $this->db->join('villages', 'villages.code = patient.village', 'left');
        $this->db->where('patient.pid', $pid);
        $this->db->where('patient.status', 1);

        return $this->db->get()->row_array();
    }

    /**
     * Save PMRS Patient's Info
     * @param $patient_id
     * @param $name
     * @param $sex
     * @param $dob
     * @param $phone
     * @param $province
     * @param $district
     * @param $commune
     * @param $village
     * @return bool
     */
	public function save_pmrs_patient($patient_id, $name, $sex, $dob, $phone, $province, $district, $commune, $village) {
		//$sql  = "REPLACE INTO camlis_pmrs_p$sql  = "REPLACE INTO camlis_pmrs_patient (pid, `name`, sex, dob, phone, province, district, commune, village) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";atient (pid, `name`, sex, dob, phone, province, district, commune, village) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
		$sql  = "INSERT INTO camlis_pmrs_patient (pid, name, sex, dob, phone, province, district, commune, village) 
				VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
				ON CONFLICT (pid) DO UPDATE SET name = excluded.name , dob = excluded.dob, phone = excluded.phone , province = excluded.province, district = excluded.district, commune = excluded.commune, village = excluded.village";
        $this->db->query($sql, [$patient_id, $name, $sex, $dob, $phone, $province, $district, $commune, $village]);

        return $this->db->affected_rows() > 0 ? TRUE : FALSE;
    }

    /**
     * Get Patient Info
     * @param $patient_id
     * @param $patient_code
     * @param $patient_name
     * @param $limit
     * @param $withAddress
     * @return array|null
     */
	public function get_patients($patient_id, $patient_code, $patient_name, $limit = FALSE, $withAddress = FALSE) {
        $this->db->select("
            patient.pid, 
            patient.patient_code AS patient_code, 
            patient.patient_name AS name, 
            CASE WHEN patient.sex = 1 THEN 'M' ELSE 'F' END AS sex,
            patient.dob,
            patient.phone
        ");
        $this->db->from('v_camlis_all_patients AS patient');
        $this->db->where('lab_id', $this->laboratory_id);
        if ($withAddress) {
            $this->db->select("
                patient.province,
                patient.commune,
                patient.district,
                patient.village,
                provinces.name_en AS province_en,
                provinces.name_kh AS province_kh,
                districts.name_en AS district_en,
                districts.name_kh AS district_kh,
                communes.name_en AS commune_en,
                communes.name_kh AS commune_kh,
                villages.name_en AS village_en,
                villages.name_kh AS village_kh
            ");
            $this->db->join('provinces', 'provinces.code = patient.province', 'left');
            $this->db->join('districts', 'districts.code = patient.district', 'left');
            $this->db->join('communes', 'communes.code = patient.commune', 'left');
            $this->db->join('villages', 'villages.code = patient.village', 'left');
        }
        if ($patient_id) $this->db->where('patient.pid', $patient_id);
        if ($patient_code) $this->db->like('patient.patient_code', $patient_code, 'right');
        if ($patient_name) $this->db->like('patient.patient_name', $patient_name, 'both');
        if ($limit) $this->db->limit($limit);

        return $this->db->get()->result_array();
    }

    /**
     * Get All Patients for DataTable
     * @param $data
     * @return array
     */
	public function view_all_patients($data) {
		//$table		= "v_camlis_all_patients";
		$table		= "camlis_outside_patient";
		$primaryKey	= "pid";
		
		$columns	= array(
			array(
				'db'	=> 'patient.pid',
				'dt'	=> 'pid',
				'field'	=> 'pid'
			),
            array(
                'db'	=> 'patient.patient_code',
                'dt'	=> 'patient_code',
                'field'	=> 'patient_code'
            ),
			array(
				'db'	    => 'patient.patient_name',
				'dt'	    => 'patient_name',
				'field'	    => 'patient_name'
			),
			array(
				'db'		=> 'patient.sex',
				'dt'		=> 'gender',
				'field'		=> 'sex',
				'type'		=> 'Number',
				'formatter'	=> function($d, $row) {
					$d	= $d == '1' ? _t('global.male') : _t('global.female');
					return $d;
				}
			),
			array(
				'db'	    => 'patient.phone',
				'dt'	    => 'phone',
				'field'	    => 'phone'
			),
			array(
				'db'	    => 'patient.passport_number',
				'dt'	    => 'passport_number',
				'field'	    => 'passport_number'
			),
			
			array( 
				'db'		=> 'SUM(CASE WHEN psample."ID" IS NULL THEN 0 ELSE 1 END) AS has_sample',
				'dt'		=> 'has_sample',
				'field'		=> 'has_sample',
				'formatter'	=> function($d, $row) {
                    return $d > 0 ? "<i class='fa fa-check-circle text-green'></i>" : "";
				}
			),
			array(
				'db'		=> 'patient.pid',
				'dt'		=> 'action',
				'field'		=> 'pid',
				'formatter'	=> function($d, $row) {
				    $action  = "<a href='".$this->app_language->site_url("patient/details/$d")."' class='hint--left hint--info' data-hint='"._t('global.view_details')."'><i class='fa fa-search'></i></a>";
					if ($this->aauth->is_allowed('add_psample')) $action .= "&nbsp;|&nbsp;<a href='".$this->app_language->site_url("sample/new/".$row['patient_code'])."' class='hint--left hint--success text-green' data-hint='"._t('sample.add_sample')."'><i class='glyphicon glyphicon-plus-sign'></i></a>";
					if (!isPMRSPatientID($d) && $this->aauth->is_allowed('delete_patient')) $action .= "&nbsp;|&nbsp;<a href='#' class='hint--left hint--error text-red remove' data-hint='"._t('global.remove')."'><i class='fa fa-trash'></i></a>";
				    return $action;
				}
			)
		);
		
		$joinQuery	= " FROM $table AS patient
						LEFT JOIN camlis_patient_sample AS psample ON CAST(patient.pid AS varchar) = psample.patient_id AND psample.\"labID\" = patient.lab_id AND psample.status = 1 ";
		
		$patient_id	= isset($data['reqData']['patient_id']) ? $data['reqData']['patient_id'] : NULL;

		$extraWhere	= " patient.lab_id = $this->laboratory_id "." AND patient.status = 1";
		
		if (!empty($patient_id)) {
			$extraWhere .= " AND patient.patient_id = '".$patient_id."'";
		}

		$extraWhere .= " GROUP BY patient.pid, patient.patient_code, patient.patient_name, patient.sex";
		
		$db_config		= $this->load->database('default', true);
		$sql_details	= array(
			'user'	=> $db_config->username,
			'pass'	=> $db_config->password,
			'port'	=> $db_config->port,
			'db'	=> $db_config->database,
			'host'	=> $db_config->hostname
		);
		
		$this->load->library('DataTablepg');
		$result = DataTablepg::simple( $data['reqData'], $sql_details, $table, $primaryKey, $columns, $joinQuery, $extraWhere);
		for ($i=0; $i < count($result['data']); $i++) {
			$result['data'][$i]['number'] = $i + 1;
		}
		
		return $result;
	}

	/**
	 * Get Standard Patient Type (DataTable)
	 */
	public function view_std_patient_type($data) {
		$_unit = array(1 => 'global.day', 7 => 'global.week', 30 => 'global.month', 365 => 'global.year');

		$table		= 'camlis_std_patient_type';
		$primaryKey	= 'ID';

		$columns	= array(
			array(
				'db'		=> 'ID',
				'dt'		=> 'ID',
				'field'		=> 'ID'
			),
			array(
				'db'		=> 'min_age',
				'dt'		=> 'min_age',
				'field'		=> 'min_age'
			),
			array(
				'db'		=> 'max_age',
				'dt'		=> 'max_age',
				'field'		=> 'max_age'
			),
			array(
				'db'		=> 'min_age_unit',
				'dt'		=> 'min_age_unit',
				'field'		=> 'min_age_unit'
			),
			array(
				'db'		=> 'max_age_unit',
				'dt'		=> 'max_age_unit',
				'field'		=> 'max_age_unit'
			),
			array(
				'db'		=> 'is_equal',
				'dt'		=> 'is_equal',
				'field'		=> 'is_equal'
			),
			array(
				'db'		=> 'gender',
				'dt'		=> 'gender',
				'field'		=> 'gender',
				'type'		=> 'Number'
			),
			array(
				'db'		=> 'type',
				'dt'		=> 'type',
				'field'		=> 'type'
			),
			array(
				'db'		=> 'gender',
				'dt'		=> 'gender_format',
				'field'		=> 'gender',
				'type'		=> 'Number',
				'formatter'	=> function($d, $row) {
					if ($d == 1) return _t('global.male');
					else if ($d == 2) return _t('global.female');
					return _t('global.male').' & '._t('global.female');
				}
			),
			array(
				'db'		=> 'min_age',
				'dt'		=> 'min_age_format',
				'field'		=> 'min_age',
				'type'		=> 'Number',
				'formatter'	=> function($d, $row) use($_unit) {
					return $d.' '.(isset($_unit[$row['min_age_unit']]) ? _t($_unit[$row['min_age_unit']]) : '');
				}
			),
			array(
				'db'		=> 'max_age',
				'dt'		=> 'max_age_format',
				'field'		=> 'max_age',
				'type'		=> 'Number',
				'formatter'	=> function($d, $row) use($_unit) {
					return $d.' '.(isset($_unit[$row['max_age_unit']]) ? _t($_unit[$row['max_age_unit']]) : '');
				}
			),
			array(
				'db'		=> 'is_equal',
				'dt'		=> 'range_sign',
				'field'		=> 'is_equal',
				'type'		=> 'Boolean',
				'formatter'	=> function($d, $row) {
					$_val = '&ge;&nbsp;&nbsp;'._t('global.age').($d == 1 ? '&nbsp;&nbsp;&le;' : ' <');
					return "<center><b>$_val</b></center>";
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


	// testing purpose
	public function test_get_patient($patient_id) {
        $this->db->select("*");
        $this->db->from('camlis_outside_patient AS patient');
        $this->db->where('patient.status', 1);
        $this->db->where('lab_id', $this->laboratory_id);
        $this->db->where('patient.pid', $patient_id);


        return $this->db->get()->row_array();
	}
	/** 
	 * Get number of sample by patient
	 * added: 12 Jan 2021
	 */
	public function get_number_of_sample($patient_id){
		$this->db->select("count(*) as number_sample");
        $this->db->from('camlis_patient_sample AS psample');
        $this->db->where('psample.status', 1);        
		$this->db->where('psample.patient_id', $patient_id);
		$this->db->where('"labID"', $this->laboratory_id);
		return $this->db->get()->row_array();
	}	
	/**
	 * Get Outside Patient from Camlis, and used in other Lab
	 * 08 June 2021
	 */
	public function get_camlis_patient($patient_code) {
        $this->db->select("
            patient.pid, 
            patient.patient_code AS patient_code, 
            patient.patient_name AS name, 
            CASE WHEN patient.sex = 1 THEN 'M' ELSE 'F' END AS sex,
            patient.dob,
            patient.phone,
            patient.province,
            patient.commune,
            patient.district,
            patient.village,
            provinces.name_en AS province_en,
            provinces.name_kh AS province_kh,
            districts.name_en AS district_en,
            districts.name_kh AS district_kh,
            communes.name_en AS commune_en,
            communes.name_kh AS commune_kh,
            villages.name_en AS village_en,
			villages.name_kh AS village_kh,
			con.name_en AS country_name_en,
			
			patient.country,
			patient.nationality,
			nat.nationality_en AS nationality_en,
			patient.residence,
			patient.is_positive_covid,
			patient.contact_with,
			patient.relationship_with_case,
			patient.date_arrival,
			patient.passport_number,
			patient.seat_number,
			patient.travel_in_past_30_days,
			patient.test_date,
			patient.is_contacted,
			patient.flight_number,
			patient.is_direct_contact,
			patient.country_name,
			vaccination_status,
			vaccine_id,
			first_vaccinated_date,
			occupation,
			second_vaccinated_date
        ");
        $this->db->from('camlis_outside_patient AS patient');
        $this->db->join('provinces', 'provinces.code = patient.province', 'left');
        $this->db->join('districts', 'districts.code = patient.district', 'left');
        $this->db->join('communes', 'communes.code = patient.commune', 'left');
		$this->db->join('villages', 'villages.code = patient.village', 'left');
		$this->db->join('countries as con','patient.country = con.num_code','left'); // ADDED 02 DEC 2020
		$this->db->join('countries as nat','patient.nationality = nat.num_code','left'); // ADDED 02 DEC 2020
        $this->db->where('patient.status', 1);        
        if ($patient_code) $this->db->where('patient.patient_code', $patient_code);
				
        return $this->db->get()->row_array();
    }
	// added 28-06-2021
	public function get_parent_patient($patient_code){
		$this->db->select("
		patient_code,
		patient_name,
		sex,
		dob,
		phone,
		province,
		commune,
		district,
		village,
		nationality,
		country,
		residence,
		is_positive_covid,
		contact_with,
		date_arrival,
		passport_number,
		seat_number,
		test_date,
		travel_in_past_30_days,
		flight_number,
		is_direct_contact,
		country_name,
		vaccination_status,
		vaccine_id,
		first_vaccinated_date,
		occupation,
		second_vaccinated_date");
        $this->db->from('camlis_outside_patient AS patient');        
        $this->db->where('patient.status', 1);
		$this->db->where('patient.parent_code IS NULL', NULL, FALSE);
        $this->db->where('patient.patient_code', $patient_code);
        return $this->db->get()->row_array();
	}
	public function update_camlis_patient($pid, $data) {
        $this->db->set($data);
        $this->db->set('modifiedBy', $this->user_id);
        $this->db->set('modifiedDate', $this->timestamp);
        $this->db->where('pid', $pid);
        $this->db->where('lab_id', $this->laboratory_id);
		$this->db->update("camlis_outside_patient");

		return $this->db->affected_rows() > 0 ? TRUE : FALSE;
	}
 }