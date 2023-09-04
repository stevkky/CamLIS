<?php
defined('BASEPATH') OR die('Access denied.');
class Manage extends MY_Controller {
	public function __construct() {
		parent::__construct();
		$this->load->model(array(
			'department_model'  => 'dModel',
			'test_model'        => 'test_model'
		));

		//Permission
		$this->aauth->control('access_camlis_lab_admin_page');
		$this->load->model('gazetteer_model');
		//Load Language
		$this->app_language->load('manage');

		//set page title and left menu
		$this->template->plugins->add(['DataTable', 'TreeView']);
		$this->template->stylesheet->add('assets/camlis/css/camlis_admin_style.css');
		$this->template->content_title =  _t('global.manage_laboratory');
		$this->data['cur_main_page'] = 'lab_admin';
	}
	
	public function index() {
		$this->laboratory();
	}

	/**
	 * Laboratory Page
	 */
	public function laboratory() {
	    $this->load->model('laboratory_model');

        $laboratory = collect($this->laboratory_model->get_laboratory(CamlisSession::getLabSession('labID')))->first();
        if ($laboratory) {
            $this->data['laboratoryInfo'] = $laboratory;
            $this->session->set_userdata('laboratory', $laboratory);
        }

		$this->template->javascript->add('assets/camlis/js/admin/camlis_laboratory.js');
		$this->template->content->widget('LabAdminLeftNavigation', ['cur_page' => 'laboratory']);
		$this->template->content->view('template/pages/admin/lab_laboratory', $this->data);
		$this->template->publish();
	}
	
	public function sample() {
		$this->template->javascript->add('assets/camlis/js/admin/camlis_sample.js');
		$this->template->content->view('template/pages/admin/sample', $this->data);
		$this->template->publish();
	}
	
	public function test() {
		$this->data['departments']  = $this->dModel->get_lab_department($this->data['laboratoryInfo']->labID);
		$this->data['std_tests']    = $this->test_model->get_std_test();

		$this->template->javascript->add('assets/camlis/js/admin/camlis_test.js');
		$this->template->content->view('template/pages/admin/test', $this->data);
		$this->template->publish();
	}
	
	public function organism() {
		$this->data['departments']  = $this->dModel->get_lab_department($this->data['laboratoryInfo']->labID);

		$this->template->javascript->add('assets/camlis/js/admin/camlis_organism.js');
		$this->template->content->view('template/pages/admin/organism', $this->data);
		$this->template->publish();
	}
	
	public function department() {
		$this->template->javascript->add('assets/camlis/js/admin/camlis_department.js');
		$this->template->content->view('template/pages/admin/department', $this->data);
		$this->template->publish();
	}

	/**
	 * Performer Page
	 */
	public function performer() {
		$this->template->javascript->add('assets/camlis/js/admin/camlis_performer.js');
		$this->template->content->widget('LabAdminLeftNavigation', ['cur_page' => 'performer']);
		$this->template->content->view('template/pages/admin/lab_performer', $this->data);
		$this->template->modal->view('template/modal/modal_lab_performer');
		$this->template->publish();
	}

	/**
	 * Machine
	 */
	public function machine() {
		$this->load->model('machine_model', 'machine');
		$this->data['machines'] = $this->machine->get_machine();
		$this->data['list_machine_tests'] = $this->machine->list_machine_test();

		$this->template->plugins->add(['BootstrapMultiselect']);

		$this->template->javascript->add(['assets/camlis/js/admin/camlis_admin_machine.js?_='.time()]);
		$this->template->content->widget('LabAdminLeftNavigation', ['cur_page' => 'machine']);
		$this->template->modal->view('template/modal/modal_admin_machine', $this->data);
        $this->template->content->view('template/pages/admin/admin_machine', $this->data);
		$this->template->publish();
	}

	/**
	 * Requester Page
	 */
	public function requester() {
		$this->load->model('sample_source_model');
		$this->data['sample_sources'] = $this->sample_source_model->get_lab_sample_source();
		$this->template->javascript->add('assets/camlis/js/admin/camlis_requester.js');
		$this->template->content->widget('LabAdminLeftNavigation', ['cur_page' => 'requester']);
		$this->template->content->view('template/pages/admin/lab_requester', $this->data);
		$this->template->modal->view('template/modal/modal_lab_requester', $this->data);
		$this->template->publish();
	}

	/**
	 * Sample Source Page
	 */
	public function sample_source() {
		$this->template->javascript->add('assets/camlis/js/admin/camlis_lab_sample_source.js');
		$this->template->content->widget('LabAdminLeftNavigation', ['cur_page' => 'sample_source']);
		$this->template->content->view('template/pages/admin/lab_sample_source', $this->data);
		$this->template->modal->view('template/modal/modal_lab_sample_source');
		$this->template->publish();
	}

	/**
	 * Payment type Page
	 */
	public function payment_type() {
	    $this->load->model('payment_type_model');
	    $this->data['payment_types'] = $this->payment_type_model->get_std_payment_type();
		$this->template->javascript->add('assets/camlis/js/admin/camlis_lab_payment_type.js');
		$this->template->content->widget('LabAdminLeftNavigation', ['cur_page' => 'payment_type']);
		$this->template->content->view('template/pages/admin/lab_payment_type', $this->data);
		$this->template->modal->view('template/modal/modal_lab_payment_type');
		$this->template->publish();
	}

    /**
     * Payment type Page
     */
    public function test_payment() {
        $this->load->model(['payment_type_model', 'test_model']);
        $this->data['payment_types'] = $this->payment_type_model->get_std_payment_type();
        $this->data['group_results'] = $this->test_model->get_sample_test_group_result();
        $this->template->plugins->add(['AutoNumeric', 'BootstrapMultiselect']);
        $this->template->javascript->add('assets/camlis/js/admin/camlis_lab_test_payment.js');
        $this->template->content->widget('LabAdminLeftNavigation', ['cur_page' => 'test_payment']);
        $this->template->content->view('template/pages/admin/lab_test_payment', $this->data);
        $this->template->modal->view('template/modal/modal_lab_test_payment');
        $this->template->publish();
    }

    /**
     * User Page
     */
    public function user() {
        $this->app_language->load('user');
        $this->data['current_laboratory'] = isset($this->data['laboratoryInfo']->labID) ? $this->data['laboratoryInfo']->labID : "";
        $this->data['user_groups']  = collect($this->aauth->list_groups())->reject(function($value, $key) { return $value->name == 'camlis_admin'; });
		$this->data['provinces']    = $this->gazetteer_model->get_province();
        $this->template->javascript->add('assets/camlis/js/admin/camlis_admin_user.js');
        $this->template->content->widget('LabAdminLeftNavigation', ['cur_page' => 'user']);
        $this->template->content->view('template/pages/admin/admin_user', $this->data);
        $this->template->modal->view('template/modal/modal_admin_user', $this->data);
        $this->template->publish();
    }

    /**
     * Document Template
     */
    public function documents() {
        $this->load->model('laboratory_model');
        $this->data['laboratory_variables'] = (array)$this->laboratory_model->get_variables();

        $this->template->javascript->add('assets/camlis/js/admin/camlis_admin_documents.js');
        $this->template->content->widget('LabAdminLeftNavigation', ['cur_page' => 'documents']);
        $this->template->content->view('template/pages/admin/admin_documents', $this->data);
        $this->template->publish();
    }

    public function ref_range() {
        $this->app_language->load('admin');
        $this->load->model(['department_model', 'patient_model']);
        $this->data['departments']    = $this->department_model->get_std_department();
        $this->data['patient_types']  = $this->patient_model->get_std_patient_type();
        $this->template->javascript->add('assets/camlis/js/admin/camlis_lab_ref_range.js');
        $this->template->content->widget('LabAdminLeftNavigation', ['cur_page' => 'ref_range']);
        $this->template->content->view('template/pages/admin/lab_ref_range', $this->data);
        $this->template->modal->view('template/modal/modal_lab_ref_range', $this->data);
        $this->template->publish();
    }
}