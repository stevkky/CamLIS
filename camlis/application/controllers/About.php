<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class About extends MY_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->data['cur_main_page'] = 'about_camlis';
	}
	/*
	* administrator tell the users there is/are system update
	*/
	public function there_are_update()
	{
		$data = array();
		if (!empty($this->input->post())) {
			$user['system_update'] = 1;
			$this->db->trans_start();
			$this->db->update('camlis_aauth_users', $user);
			$this->db->trans_complete();
			if ($this->db->trans_status() === true) {
				$data = array('status' => true, 'message' => 'update complete');
			} else {
				$data = array('status' => false, 'message' => 'update fail!');
			}
		} else {
			$data = array('status' => false, 'message' => 'update fail!');
		}
		echo json_encode($data);
	}
	/*
	* users seen the system update
	*/
	public function seen_update()
	{
		$data = array();
		if (!empty($this->input->post())) {
			$user['system_update'] = 0;
			// current user login
			$this->db->where('id', $this->aauth->get_user_id());
			if ($this->db->update('camlis_aauth_users', $user)) {
				$this->session->set_userdata($user);
				$data = array('status' => true);
			} else {
				$data = array('status' => false);
			}
		}
		echo json_encode($data);
	}

	public function sop()
	{
		$this->download('sop.pdf');
	}

	public function help()
	{
		$this->download('help.pdf');
	}

	public function update()
	{
		$this->load->helper('file');
		$this->data['update'] = file_get_contents(base_url().'uploads/update.txt');
		$this->template->content->view('template/pages/about/update', $this->data);
		$this->template->publish();	
	}

	public function download($filename = NULL)
	{
		// load download helder
    	$this->load->helper('download');
    	// read file contents
    	$data = file_get_contents(base_url('/uploads/'.$filename));
    	// download file
    	force_download($filename, $data);
	}
}
