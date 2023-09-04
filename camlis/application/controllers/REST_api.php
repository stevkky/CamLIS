<?php
defined('BASEPATH') OR die('Access Denied.');

require APPPATH . 'libraries/REST_Controller.php';

class REST_api extends REST_Controller
{
    public function __construct($config = 'REST')
    {
        parent::__construct($config);
        
        $this->load->model('REST_model');
    }

    public function bacteriology_get() {
        $labid = $this->get('labid');
		$start_date = $this->get('start_date');
        $end_date = $this->get('end_date');

        try {
            $result = $this->REST_model->get_patient_by_culture($labid, $start_date, $end_date);
            $this->response([
                'status' => true,
                'data' => $result
            ]);
        } catch (Exception $e) {
            $this->response([
                'status' => false,
                'data' => []
            ]);
        }
    }
	public function pathogens_get() {
		$start_date = $this->get('start_date');
        $end_date = $this->get('end_date');

        try {
            $result = $this->REST_model->get_priorities_pathogens($start_date, $end_date);
            $this->response([
                'status' => true,
                'data' => $result
            ]);
        } catch (Exception $e) {
            $this->response([
                'status' => false,
                'data' => []
            ]);
        }
    }
	
	public function patientdetail_get() {
        $labid = $this->get('labid');
		$patientid = $this->get('patientid');
        $labnumber = $this->get('labnumber');

        try {
            $result = $this->REST_model->get_bacteriology_data($labid, $patientid, $labnumber);
            $this->response([
                'status' => true,
                'data' => $result
            ]);
        } catch (Exception $e) {
            $this->response([
                'status' => false,
                'data' => []
            ]);
        }
    }
    /**
     * For one dashboard
     * Select all positive covid result from all Lab
     */
    public function login_get(){
        $username = $this->get('username');
        $password = $this->get('password');
        #$username = $this->input->post('username', TRUE);
        #$password = $this->input->post('password', TRUE);

        try {
            $result = $this->aauth->login($username, $password);
            $this->response([
                'status' => true,
                'data' => $result
            ]);
        } catch (Exception $e) {
            $this->response([
                'status' => false,
                'data' => []
            ]);
        }
    }

    public function laboratory_get(){
        try {
            $result = $this->REST_model->get_laboratory();
            $this->response([
                'status' => true,
                'data' => $result
            ]);
        } catch (Exception $e) {
            $this->response([
                'status' => false,
                'data' => []
            ]);
        }
    }
}