<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class PatientWebService {
	private $CI		    = NULL;
	private $w_url      = '';
	private $w_user     = '';
	private $w_pass     = '';
	
	public function __construct() {
        global $CFG;
        $this->CI		= & get_instance();

        //Load Config
        $this->CI->load->config('webservice', TRUE);
        $this->w_url    = $CFG->item('patient.w_url', 'webservice');
        $this->w_user   = $CFG->item('patient.w_user', 'webservice');
        $this->w_pass   = $CFG->item('patient.w_pass', 'webservice');

        //Add Slash to end of URL
		$this->w_url = rtrim($this->w_url, '/') . '/';
	}
	
	public function set_w_url($w_url){
		$this->w_url = $w_url;
	}

	public function get_w_url() {
		return $this->w_url;
	}
	
	public function set_w_user($w_user) {
		$this->w_user   = $w_user;
	}
	
	public function set_w_pass($w_pass) {
		$this->w_pass   = $w_pass;
	}
	
	function execute($patient_id) {
		if(empty($this->w_user) || empty($this->w_pass)) throw new Exception('Username and Password Required');
		
		/*$curl_handle = curl_init();
		curl_setopt($curl_handle, CURLOPT_URL, $this->w_url.$patient_id);
		curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, 1);
		 
		// Optional, delete this line if your API is open
		curl_setopt($curl_handle, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
		curl_setopt($curl_handle, CURLOPT_USERPWD, $this->w_user . ':' . $this->w_pass);
		 
		$buffer = curl_exec($curl_handle);
		curl_close($curl_handle);
		 
		$result = json_decode($buffer);
		
		var_dump($result);*/
		
		
		$handle = curl_init();
		curl_setopt($handle, CURLOPT_URL, $this->w_url.$patient_id);
		curl_setopt($handle, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($handle, CURLOPT_SSL_VERIFYHOST, 2);
		curl_setopt($handle, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($handle, CURLOPT_SSLVERSION,3); 
		curl_setopt($handle, CURLOPT_USERPWD, $this->w_user . ':' . $this->w_pass);
		
		
		$response = curl_exec($handle);  
		$result = json_decode($response);
		curl_close($handle);  
		return $result;
	}
	
}

