<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Interpretation_model extends MY_Model {

	public function create($interpretations)
	{
		$result = false;
		$this->db->trans_begin();
		foreach ($interpretations as $interpretation) {
			$interpretation['"entryBy"'] = $this->user_id;
			$interpretation['"entryDate"'] = $this->timestamp;
			$interpretation['status'] = true;
			$this->db->insert('camlis_std_ast_interpretation', $interpretation);	
		}
		if ($this->db->trans_status() === false) {
			$this->db->trans_rollback();
		} else {
			$this->db->trans_commit();
			$result = true;
		}
		return $result;
	}

	public function update($interpretations)
	{
		$result = false;
		$this->db->trans_begin();
		foreach ($interpretations as $interpretation) {
			$interpretation['"modifiedBy"'] = $this->user_id;
			$interpretation['"modifiedDate"'] = $this->timestamp;
			$interpretation['status'] = true;
			$this->db->where('"ID"', $interpretation['"ID"']);
			$this->db->update('camlis_std_ast_interpretation', $interpretation);	
		}
		if ($this->db->trans_status() === false) {
			$this->db->trans_rollback();
		} else {
			$this->db->trans_commit();
			$result = true;
		}
		return $result;
	}

	public function get_organism_antibiotic_id($organism_id, $antibiotic_id)
	{
		$this->db->select('organism_antibiotic."ID"');
		$this->db->from('camlis_std_test_organism_antibiotic as organism_antibiotic');
		$this->db->join('camlis_std_test_organism as organism', 'organism."ID" = organism_antibiotic.test_organism_id');
		$this->db->join('camlis_std_organism org', 'org."ID" = organism.organism_id');
		$this->db->where('org."ID"', $organism_id);
		$this->db->where('organism_antibiotic.antibiotic_id', $antibiotic_id);
		$this->db->where('organism_antibiotic.status', true);
		$this->db->where('organism.sample_test_id', 170);
		$this->db->where('organism.status', true);
		return $this->db->get()->row();
	}

	public function get_existing_interpretation($test_organism_antibiotic_id)
	{
		$this->db->select('interpretation.*');
		$this->db->from('camlis_std_ast_interpretation as interpretation');
		$this->db->where('test_organism_antibiotic_id', $test_organism_antibiotic_id);
		$this->db->order_by('interpretation.description', 'asc');
		return $this->db->get()->result();
	}

	public function get_rank($condition)
	{
		$this->db->select('interpretation.description');
		$this->db->from('camlis_std_ast_interpretation as interpretation');
		$this->db->join('camlis_std_test_organism_antibiotic as organism_antibiotic', 'organism_antibiotic."ID" = interpretation.test_organism_antibiotic_id');
		$this->db->join('camlis_std_test_organism as test_organism', 'test_organism."ID" = organism_antibiotic.test_organism_id');
		$this->db->where('test_organism."ID"', $condition['test_organism_id']);
		$this->db->where('organism_antibiotic.antibiotic_id', $condition['antibiotic_id']);
		$this->db->where('interpretation.min_value <=', (!empty($condition['diffusion'])) ? $condition['diffusion'] : $condition['test_zone']);
		$this->db->where('interpretation.max_value >=', (!empty($condition['diffusion'])) ? $condition['diffusion'] : $condition['test_zone']);
		$this->db->where('interpretation.status', true);
		$this->db->where('organism_antibiotic.status', true);
		$this->db->where('test_organism.status', true);
		return $this->db->get()->row();
	}

}