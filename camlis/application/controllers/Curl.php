<?php
defined('BASEPATH') or die('Access denied!');
class Curl extends CI_Controller {
	public function __construct() {
        parent::__construct();					
		$this->data['cur_main_page'] = 'api';
	}

	public function index() {
        echo "hello";
    }
    public function get(){
       
    }
    public function post(){
        echo "POST Method <br />";
        $url        = "https://reqres.in/api/login";
        $handle     = curl_init();
        $data_arr   = array(
            "email"     => "hello@hello.com",
            "password"  => "cityslickasss"
        );
        $data = http_build_query($data_arr);
        curl_setopt($handle, CURLOPT_URL,$url);
        curl_setopt($handle, CURLOPT_POST,true);
        curl_setopt($handle, CURLOPT_POSTFIELDS,$data);
        curl_setopt($handle, CURLOPT_RETURNTRANSFER,true);

        $response = curl_exec($handle);  
        if($error = curl_error($handle)){
            echo $error;
        }else{
            $res = json_decode($response,true);
            print_r($res);
        }
        curl_close($handle);
    }
    public function put(){
        echo "PUT Method <br />";
        $url        = "https://reqres.in/api/users/2";
        $handle     = curl_init();
        $data_arr   = array(
            "name"  => "Shem",
            "job"   => "Programmer"
        );
        $data = http_build_query($data_arr);
        curl_setopt($handle, CURLOPT_URL,$url);
        curl_setopt($handle, CURLOPT_CUSTOMREQUEST,'PUT');
        curl_setopt($handle, CURLOPT_POSTFIELDS,$data);
        curl_setopt($handle, CURLOPT_RETURNTRANSFER,true);

        $response = curl_exec($handle);  
        if($error = curl_error($handle)){
            echo $error;
        }else{
            $res = json_decode($response,true);
            print_r($res);
        }
        curl_close($handle);
    }
}