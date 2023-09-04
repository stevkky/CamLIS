<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Interpretation extends MY_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model('interpretation_model', 'interpretation');
	}
	/**
	* @desc get organism_antibiotic id
	*/
	public function get_organism_antibiotic_id()
	{
		echo json_encode($this->interpretation->get_organism_antibiotic_id($this->input->post('organism_id'), $this->input->post('antibiotic_id')));
	}
	/**
	* @desc get interpretation
	*/
	public function get_interpretation()
	{
		echo json_encode($this->interpretation->get_existing_interpretation($this->input->post('test_organism_antibiotic_id')));
	}
	/**
	* get antibiotic rank
	*/
	public function get_antibiotic_rank()
	{
		echo json_encode($this->interpretation->get_rank(
			array(
				'test_organism_id' => $this->input->post('test_organism_id'),
				'antibiotic_id' => $this->input->post('antibiotic_id'),
				'diffusion' => $this->input->post('diffusion'),
				'test_zone' => $this->input->post('test_zone'),
			)
		));
	}
	/**
    * save interpretation
    */
    public function save()
    {
    	echo json_encode(($this->interpretation->create($this->input->post('interpretations'))) ? array('status' => true) : array('status' => false));
    }
    /**
    * update interpretation
    */
    public function update()
    {
    	echo json_encode(($this->interpretation->update($this->input->post('interpretations'))) ? array('status' => true) : array('status' => false));
    }
}