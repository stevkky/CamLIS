<?php

/**
 * Created by PhpStorm.
 * User: spanga
 * Date: 17-Jan-17
 * Time: 2:16 PM
 */
class Email extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function send() {
        $config = array();
        $config['protocol']		= "smtp";
        $config['smtp_host']	= 'ssl://smtp.googlemail.com';
        $config['smtp_user'] 	= 'sopagna9404@gmail.com';
        $config['smtp_pass'] 	= 'p@gn@070';
        $config['smtp_port']	= 465;

        $config['mailtype']		= 'html';
        $config['charset']		= 'utf-8';
        $config['newline']		= "\r\n";
        $this->load->library('email');
        $this->email->initialize($config);

        $this->email->from($config['smtp_user'], 'So Pagna');
        $this->email->to('sopagna.kh@gmail.com');
        $this->email->subject('Email Test');
        $this->email->message('Testing the email class.');

        if ($this->email->send()) {
            echo "Email sent!";
        } else {
            show_error($this->email->print_debugger());
        }
    }
}