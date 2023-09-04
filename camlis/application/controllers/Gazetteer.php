<?php
defined('BASEPATH') or die('Access denied!');
class Gazetteer extends MY_Controller {
	public function __construct() {
		parent::__construct();
		$this->load->model('gazetteer_model', 'gzModel');
	}
	
	public function get_province() {
		$provinces		= $this->gzModel->get_province();
		
		$data['result']	= json_encode($provinces);
		$this->load->view('ajax_view/view_result', $data);
	}
	
	public function get_district() {
		$pro_code	= $this->input->post('code'); 
		$districts	= $this->gzModel->get_district($pro_code);
		
		$data['result'] = json_encode($districts);
		$this->load->view('ajax_view/view_result', $data);
	}
	
	public function get_commune() {
		$dis_code	= $this->input->post('code');
		$communes	= $this->gzModel->get_commune($dis_code);
		
		$data['result'] = json_encode($communes);
		$this->load->view('ajax_view/view_result', $data);
	}
	
	public function get_village() {
		$com_code	= $this->input->post('code');				
		$villages	= $this->gzModel->get_village($com_code);
		
		$data['result'] = json_encode($villages);
		$this->load->view('ajax_view/view_result', $data);
	}
}