<?php
defined('BASEPATH') OR exit('No direct script allowed.');

class MY_Controller extends CI_Controller {
	protected $data = NULL;
	
	public function __construct() {
		parent::__construct();
        date_default_timezone_set('Asia/Phnom_Penh');
		$this->load->helper(array('Util'));
		
		//Application Language
		$this->data['app_lang']			= $this->app_language->app_lang();
		
		//Current Login User
		$user            = new stdClass();
		$user->id		 = $this->session->userdata('id');
		$user->fullname	 = $this->session->userdata('fullname');
		$this->data['user'] = $user;
		
		//Current Laboratory Session
		$this->data['laboratoryInfo']	= CamlisSession::getLabSession();
		$this->data['cur_main_page']	= '';
	}
}