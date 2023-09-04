<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Login extends MY_Controller {
    public function __construct()
    {
        parent::__construct();
        $this->app_language->load('login');

        $laboratories = $this->session->userdata('user_laboratories');
        if ($this->aauth->is_loggedin()) {
            if (!$this->aauth->is_admin() && count($laboratories) == 1 && end($laboratories) > 0) {
                redirect("laboratory/change/".end($laboratories));
            }
            else redirect("laboratory");
        }
    }

    public function index() {
        $this->session->unset_userdata('timestamp');
        $this->data["login_errors"] = $this->session->tempdata("login_errors");
        $this->template->stylesheet->add('/assets/camlis/css/login_style.css');
        $this->template->content->view('template/pages/login', $this->data);
        $this->template->publish();
    }
}
