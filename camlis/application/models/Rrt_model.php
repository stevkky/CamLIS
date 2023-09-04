<?php
defined('BASEPATH') OR die('Permission denied!');
class Rrt_model extends MY_Model {
	public function __construct()
    {
        parent::__construct();
        $this->load->library('patientwebservice');
    }
	/**
	 * Add new outside patient
	 * @param $data
	 * @return mixed
	 */
	public function save_outside_patient($data) {
        $this->db->set($data);        
        $this->db->set('entryBy', $this->user_id);
        $this->db->set('entryDate', $this->timestamp);
        $this->db->set('status', 1);
		$this->db->insert("camlis_outside_patient_tmp");
		return $this->db->insert_id();
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
            patient.age,
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
			patient.is_direct_contact
        ");
        $this->db->from('camlis_outside_patient_tmp AS patient');
        $this->db->join('provinces', 'provinces.code = patient.province', 'left');
        $this->db->join('districts', 'districts.code = patient.district', 'left');
        $this->db->join('communes', 'communes.code = patient.commune', 'left');
		$this->db->join('villages', 'villages.code = patient.village', 'left');
		$this->db->join('countries as con','patient.country = con.num_code','left'); // ADDED 02 DEC 2020
		$this->db->join('countries as nat','patient.nationality = nat.num_code','left'); // ADDED 02 DEC 2020
        $this->db->where('patient.status', 1);        
        if ($patient_id) $this->db->where('patient.pid', $patient_id);
        if ($patient_code) $this->db->where('patient.patient_code', $patient_code);

        return $this->db->get()->row_array();
    }
    /**
	 * Add Patient's Sample
	 * @param $data
     * @return mixed
	 */
	public function add_patient_sample($data) {
		$this->db->set($data);		
		$this->db->set('"entryBy"', $this->user_id);
        $this->db->set('status', 1);
		$this->db->set('"entryDate"',$this->timestamp);
		$this->db->insert('camlis_patient_sample_tmp');
		return $this->db->insert_id();
	}
    public function get_number_of_sample($patient_id){
        $this->db->select("count(*) as number");
        $this->db->from('camlis_patient_sample_tmp AS psample');        
        $this->db->where('psample.patient_id', $patient_id);
        $this->db->where('status', 1);
        return $this->db->get()->row_array();
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
		$this->db->update("camlis_outside_patient_tmp");
		return $this->db->affected_rows() > 0 ? TRUE : FALSE;
	}

    /**
     * checking existing patient code
     * @param $patient_code
     * @return int
     */
    public function patient_code_exist($patient_code) {
        $result = $this->db->get_where("camlis_outside_patient_tmp", ["patient_code" => $patient_code]);
        return $result->num_rows() > 0 ? TRUE : FALSE;
    }

    public function get_patients($user_id, $date) {
        $this->db->select("
            patient.pid, 
            patient.patient_code AS patient_code, 
            patient.patient_name AS name, 
            CASE WHEN patient.sex = 1 THEN 'M' ELSE 'F' END AS sex,
            patient.age,
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
            psample.sample_number,
            psample.sample_source,
            to_char(psample.collected_date, 'DD/MM/YYYY') AS collected_date,
            psample.number_of_sample,
            psample.sample_collector,
            psample.phone_number,
            psample.for_labo,
            psample.\"ID\" as id
        ");
        $this->db->from('camlis_outside_patient_tmp AS patient');
        $this->db->join('camlis_patient_sample_tmp AS psample', 'psample.patient_id = patient.pid', 'inner');
        $this->db->join('provinces', 'provinces.code = patient.province', 'left');
        $this->db->join('districts', 'districts.code = patient.district', 'left');
        $this->db->join('communes', 'communes.code = patient.commune', 'left');
		$this->db->join('villages', 'villages.code = patient.village', 'left');
		$this->db->join('countries as con','patient.country = con.num_code','left'); // ADDED 02 DEC 2020
		$this->db->join('countries as nat','patient.nationality = nat.num_code','left'); // ADDED 02 DEC 2020
        $this->db->where('patient.status', 1);
        $this->db->where('psample.transfer_status', false);
        $this->db->where('patient."entryBy"', $user_id);
        $this->db->where('"psample.status"', 1);        
        $this->db->where('"psample.collected_date"', $date);
        return $this->db->get()->result_array();
    }
    public function update_patient_sample($id) {
        $this->db->set('transfer_status',true);
        $this->db->set('"transferedBy"', $this->user_id);
        $this->db->set('transferedDate', $this->timestamp);
        $this->db->where('"ID"', $id);
		$this->db->update("camlis_patient_sample_tmp");
		return $this->db->affected_rows() > 0 ? TRUE : FALSE;
	}
    public function get_patient_sample($psamle_id){
        $this->db->select("
            patient.pid, 
            patient.patient_code AS patient_code, 
            patient.patient_name AS name, 
            CASE WHEN patient.sex = 1 THEN 'M' ELSE 'F' END AS sex,
            patient.age,
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
            psample.sample_number,
            psample.sample_source,
            to_char(psample.collected_date, 'DD/MM/YYYY') AS collected_date,
            psample.number_of_sample,
            psample.sample_collector,
            psample.phone_number,
            psample.for_labo,
            psample.patient_id,
            psample.\"ID\" as id
        ");
        $this->db->from('camlis_outside_patient_tmp AS patient');
        $this->db->join('camlis_patient_sample_tmp AS psample', 'psample.patient_id = patient.pid', 'inner');
        $this->db->join('provinces', 'provinces.code = patient.province', 'left');
        $this->db->join('districts', 'districts.code = patient.district', 'left');
        $this->db->join('communes', 'communes.code = patient.commune', 'left');
		$this->db->join('villages', 'villages.code = patient.village', 'left');
		$this->db->join('countries as con','patient.country = con.num_code','left'); // ADDED 02 DEC 2020
		$this->db->join('countries as nat','patient.nationality = nat.num_code','left'); // ADDED 02 DEC 2020
        $this->db->where('patient.status', 1);        
        $this->db->where('"psample.status"', 1);        
        $this->db->where('"psample."ID"', $psamle_id);
        return $this->db->get()->row_array();
    }
    
    public function get_patient_samples($psamle_ids){
        $this->db->select("
            patient.pid, 
            patient.patient_code AS patient_code, 
            patient.patient_name AS name, 
            CASE WHEN patient.sex = 1 THEN 'M' ELSE 'F' END AS sex,
            patient.age,
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
            psample.sample_number,
            psample.sample_source,
            to_char(psample.collected_date, 'DD/MM/YYYY') AS collected_date,
            psample.number_of_sample,
            psample.sample_collector,
            psample.phone_number,
            psample.for_labo,
            psample.patient_id,
            psample.\"ID\" as id
        ");
        $this->db->from('camlis_outside_patient_tmp AS patient');
        $this->db->join('camlis_patient_sample_tmp AS psample', 'psample.patient_id = patient.pid', 'inner');
        $this->db->join('provinces', 'provinces.code = patient.province', 'left');
        $this->db->join('districts', 'districts.code = patient.district', 'left');
        $this->db->join('communes', 'communes.code = patient.commune', 'left');
		$this->db->join('villages', 'villages.code = patient.village', 'left');
		$this->db->join('countries as con','patient.country = con.num_code','left'); // ADDED 02 DEC 2020
		$this->db->join('countries as nat','patient.nationality = nat.num_code','left'); // ADDED 02 DEC 2020
        $this->db->where('patient.status', 1);        
        $this->db->where('"psample.status"', 1);        
        $this->db->where_in('"psample."ID" ', $psamle_ids);
        return $this->db->get()->result_array();
    }
 }