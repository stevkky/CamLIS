<?php
defined('BASEPATH') OR die('Permission denied!');
class Exceljs_model extends MY_Model {
	public function __construct()
    {
        parent::__construct();
        $this->load->library('patientwebservice');
    }
    public function get($laboratory_code = FALSE, $fullname = FALSE , $phone = FALSE) {
        $this->db->select("
			patient.id,
			patient.no_by_day,
			patient.case_no,
            patient.laboratory_code AS patient_code, 
            CASE WHEN patient.sex = '1' THEN 'M' ELSE 'F' END AS sex,
            patient.age,
			patient.nationality,
			patient.phone,
			patient.date_of_sampling,
			patient.date_of_result,
			patient.f20_event,
			patient.imported_country,
			patient.date_of_onset,
			patient.symptoms,
			patient.positive_on,
			patient.reason_for_testing,
            patient.province,
            patient.commune,
            patient.district,
            patient.village,
			patient.detection_province,
			patient.remark,
            provinces.name_en AS province_en,
            provinces.name_kh AS province_kh,
            districts.name_en AS district_en,
            districts.name_kh AS district_kh,
            communes.name_en AS commune_en,
            communes.name_kh AS commune_kh,
            villages.name_en AS village_en,
			villages.name_kh AS village_kh,				
			nat.nationality_en AS nationality_en,						
			patient.vaccination_status,
			patient.vaccine,
			patient.first_injection_date,		
			patient.second_injection_date,
			patient.fullname,
			patient.remark,
			patient.img_url
			");
        $this->db->from('tbl_tmp as patient');
		$this->db->join('provinces', 'provinces.code = patient.province', 'left');
        $this->db->join('districts', 'districts.code = patient.district', 'left');
        $this->db->join('communes', 'communes.code = patient.commune', 'left');
		$this->db->join('villages', 'villages.code = patient.village', 'left');		
		$this->db->join('countries as nat','patient.nationality = nat.num_code','left');
        if ($laboratory_code) $this->db->where('laboratory_code', $laboratory_code);
        if ($fullname) $this->db->where('LOWER(fullname)', $fullname);
		if ($phone) $this->db->where('phone', $phone);
        return $this->db->get()->row_array();
    }

	public function save($data) {
        $this->db->set($data);
        $this->db->set('entry_date', $this->timestamp);
		$this->db->insert("tbl_tmp");
		return $this->db->insert_id();
	}

	public function get_data($start_date, $end_date){
		$this->db->select("
			patient.id,
			patient.no_by_day,
			patient.case_no,
            patient.laboratory_code AS patient_code, 
            CASE WHEN patient.sex = '1' THEN 'M' ELSE 'F' END AS sex,
            patient.age,
			patient.nationality,
			patient.phone,
			patient.date_of_sampling,
			patient.date_of_result,
			patient.f20_event,
			patient.imported_country,
			patient.date_of_onset,
			patient.symptoms,
			patient.positive_on,
			patient.reason_for_testing,
            patient.province,
            patient.commune,
            patient.district,
            patient.village,
			patient.detection_province,
			patient.remark,
            provinces.name_en AS province_en,
            provinces.name_kh AS province_kh,
            districts.name_en AS district_en,
            districts.name_kh AS district_kh,
            communes.name_en AS commune_en,
            communes.name_kh AS commune_kh,
            villages.name_en AS village_en,
			villages.name_kh AS village_kh,				
			nat.nationality_en AS nationality_en,						
			patient.vaccination_status,
			patient.vaccine,
			patient.first_injection_date,		
			patient.second_injection_date,
			patient.fullname,
			patient.remark,
			patient.img_url,
			cv.name as vaccine_name,
			pro.name_en AS detection_province_en
			");
        $this->db->from('tbl_tmp as patient');
		$this->db->join('provinces', 'provinces.code = patient.province', 'left');
        $this->db->join('districts', 'districts.code = patient.district', 'left');
        $this->db->join('communes', 'communes.code = patient.commune', 'left');
		$this->db->join('villages', 'villages.code = patient.village', 'left');		
		$this->db->join('countries as nat','patient.nationality = nat.num_code','left');		
		$this->db->join('camlis_vaccine as cv','cv.id = patient.vaccine','left');
		$this->db->join('provinces as pro', 'pro.code = patient.detection_province', 'left');
		$this->db->where('patient.date_of_result >=', $start_date);
		$this->db->where('patient.date_of_result <=', $end_date);       
        return $this->db->get()->result_array();
	}

 }