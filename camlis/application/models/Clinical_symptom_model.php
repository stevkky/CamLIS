<?php
defined('BASEPATH') or die('Access denied!');
class Clinical_symptom_model extends MY_Model {
	public function __construct() {
		parent::__construct();
	}

	/**
	 * Get Clinical Symptom
	 * @param null $id
	 * @return mixed
	 */
	public function get($id = NULL) {
		$this->db->select('"ID", name_en, name_kh');		
		if ($id !== NULL) $this->db->where('"ID"', $id);
		return $this->db->get('camlis_clinical_symptoms')->result();
	}
	// get patient_sample_clinical_symptom
	public function get_ps_clinical_symptom($patient_sample_id = NULL) {
		$this->db->select('"ID", patient_sample_id, clinical_symptom_id');		
		if ($patient_sample_id !== NULL) $this->db->where("patient_sample_id", $patient_sample_id);
		return $this->db->get('camlis_patient_sample_clinical_symptoms')->result();
	}

	public function add_clinical_symptom($data) {
		$data = (array)$data;		
		$this->db->insert_batch('camlis_patient_sample_clinical_symptoms', $data);
		return $this->db->affected_rows();
	}
	public function update_clinical_symptom($psample_id, array $clinical_symptom_data = array()) {
		
		// delete all existing 
		$this->db->where('patient_sample_id', $psample_id);
		$this->db->delete('camlis_patient_sample_clinical_symptoms');
		if(count($clinical_symptom_data) > 0){
			$this->add_clinical_symptom($clinical_symptom_data);
		}		
	}
	public function delete($patient_sample_id){
		// delete all existing 
		$this->db->where('patient_sample_id', $patient_sample_id);
		$this->db->delete('camlis_patient_sample_clinical_symptoms');
	}
}