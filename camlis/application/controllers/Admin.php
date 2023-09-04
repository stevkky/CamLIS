<?php
defined('BASEPATH') OR die('Access denied!');
class Admin extends MY_Controller {
	public function __construct() {
		parent::__construct();

		//Permission
		$this->aauth->control('access_camlis_admin_page');
		
		//Load Lanugage
		$this->app_language->load(array('admin', 'sample', 'test', 'user'));

		//template
		$this->template->plugins->add(['DataTable', 'TreeView']);
		$this->template->stylesheet->add('assets/camlis/css/camlis_admin_style.css');
		$this->template->content_title = _t('nav.manage_camlis');
		$this->data['cur_main_page'] = 'camlis_admin';
	}
	
	public function index() {
		$this->laboratory();
	}
	
	public function laboratory() {
		$this->load->model("healthfacility_model", "hfModel");
		//$this->data['health_facilities']   = $this->hfModel->fetch();

		$this->template->javascript->add('assets/camlis/js/admin/camlis_admin_laboratory.js');
		$this->template->content->widget('CamLISAdminLeftNavigation', ['cur_page' => 'laboratory']);
		$this->template->content->view('template/pages/admin/admin_laboratory', $this->data);
		$this->template->modal->view('template/modal/modal_admin_laboratory', $this->data);
		$this->template->publish();
	}

	public function department() {
		$this->template->javascript->add('assets/camlis/js/admin/camlis_admin_department.js');
		$this->template->content->widget('CamLISAdminLeftNavigation', ['cur_page' => 'department']);
		$this->template->content->view('template/pages/admin/admin_department', $this->data);
		$this->template->modal->view('template/modal/modal_admin_department');
		$this->template->publish();
	}
	
	public function sample() {
		$this->load->model('department_model', 'dModel');
		$this->data['departments'] = $this->dModel->get_std_department();

        $this->template->plugins->add(['BootstrapTagsInput']);
		$this->template->javascript->add('assets/camlis/js/admin/camlis_admin_sample.js');
		$this->template->content->widget('CamLISAdminLeftNavigation', ['cur_page' => 'sample']);
		$this->template->content->view('template/pages/admin/admin_sample', $this->data);
		$this->template->modal->view('template/modal/modal_admin_sample', $this->data);
		$this->template->publish();
	}
	
	public function test() {
		$this->load->model('department_model', 'dModel');
		$this->load->model('test_model');
		$this->load->model('patient_model');
		$this->load->model('organism_model');
		$this->load->model('antibiotic_model');

		$this->data['tests']  			= $this->test_model->get_std_test();
		$this->data['departments']  	= $this->dModel->get_std_department();
		$this->data['field_types']  	= $this->test_model->get_test_fieldType();
		$this->data['group_results']  	= $this->test_model->get_std_group_result();
		$this->data['patient_types']  	= $this->patient_model->get_std_patient_type();
		$this->data['organisms']  		= $this->organism_model->get_std_organism();
		$this->data['antibiotic']  		= $this->antibiotic_model->get_std_antibiotic();

		$this->template->plugins->add('DataTableFixedColumn');
		$this->template->javascript->add(['assets/camlis/js/admin/camlis_admin_test.js', 'assets/camlis/js/admin/camlis_admin_group_result.js']);
		$this->template->content->widget('CamLISAdminLeftNavigation', ['cur_page' => 'test']);
		$this->template->modal->view('template/modal/modal_admin_test', $this->data);
        $this->template->content->view('template/pages/admin/admin_test', $this->data);
		$this->template->publish();
	}

	public function organism() {


        $this->load->model('antibiotic_model');
        $this->data['gram_type']  = $this->antibiotic_model->get_gram_type();

		$this->template->javascript->add('assets/camlis/js/admin/camlis_admin_organism.js');
		$this->template->content->widget('CamLISAdminLeftNavigation', ['cur_page' => 'organism']);
		$this->template->content->view('template/pages/admin/admin_organism', $this->data);
		$this->template->modal->view('template/modal/modal_admin_organism');
		$this->template->modal->view('template/modal/modal_admin_antibiotic');
		$this->template->modal->view('template/modal/modal_admin_organism_quantity');
		$this->template->publish();
	}

	public function patient_type() {
		$this->template->javascript->add('assets/camlis/js/admin/camlis_admin_patient_type.js');
		$this->template->content->widget('CamLISAdminLeftNavigation', ['cur_page' => 'patient_type']);
		$this->template->content->view('template/pages/admin/admin_patient_type', $this->data);
		$this->template->modal->view('template/modal/modal_admin_patient_type');
		$this->template->publish();
	}

	public function sample_comment() {
		$this->load->model("department_model", "dModel");
		$this->data['departments']  = $this->dModel->get_std_department();

		$this->template->javascript->add('assets/camlis/js/admin/camlis_admin_sample_comment.js');
		$this->template->content->widget('CamLISAdminLeftNavigation', ['cur_page' => 'sample_comment']);
		$this->template->content->view('template/pages/admin/admin_sample_comment', $this->data);
		$this->template->modal->view('template/modal/modal_admin_sample_comment', $this->data);
		$this->template->publish();
	}

    public function payment_type() {
        $this->load->model('payment_type_model');
        $this->template->javascript->add('assets/camlis/js/admin/camlis_admin_payment_type.js');
        $this->template->content->widget('CamLISAdminLeftNavigation', ['cur_page' => 'payment_type']);
        $this->template->content->view('template/pages/admin/admin_payment_type', $this->data);
        $this->template->modal->view('template/modal/modal_admin_payment_type', $this->data);
        $this->template->publish();
    }

	public function user() {

		$this->load->model('laboratory_model', 'labModel');
		$this->load->model('gazetteer_model');
		$this->data['laboratories'] = $this->labModel->get_laboratory();
        $this->data['user_groups']  = collect($this->aauth->list_groups());
		$this->data['provinces']    = $this->gazetteer_model->get_province();
		$this->template->javascript->add('assets/camlis/js/admin/camlis_admin_user.js');
		$this->template->content->widget('CamLISAdminLeftNavigation', ['cur_page' => 'user']);
		$this->template->content->view('template/pages/admin/admin_user', $this->data);
		$this->template->modal->view('template/modal/modal_admin_user', $this->data);
		$this->template->publish();
	}

	public function user_role() {
	    $this->data['permissions'] = collect($this->aauth->list_perms())->sortBy('definition')->toArray();

        $this->template->javascript->add('assets/camlis/js/admin/camlis_admin_user_role.js');
        $this->template->content->widget('CamLISAdminLeftNavigation', ['cur_page' => 'user_role']);
        $this->template->content->view('template/pages/admin/admin_user_role', $this->data);
        $this->template->modal->view('template/modal/modal_admin_user_role', $this->data);
        $this->template->publish();
    }

	public function permission() {
		$this->load->model('laboratory_model', 'labModel');
		$this->data['laboratories'] = $this->labModel->get_laboratory();
		
		$obj		= new stdClass();
		$obj->user_id = $this->input->post('user_id'); 
		$this->data['report_name'] = $this->labModel->get_report_name($obj);

	
		$this->template->stylesheet->add('assets/plugins/autocomplete/jquery-ui.css');
		$this->template->javascript->add('assets/plugins/autocomplete/jquery-ui.js');
		
		$this->template->javascript->add('assets/camlis/js/admin/camlis_admin_user.js');
		$this->template->content->widget('CamLISAdminLeftNavigation', ['cur_page' => 'permission']);
		$this->template->content->view('template/pages/admin/admin_role', $this->data); 
		$this->template->publish();
	}
	/* lookup data sample code
	 * 
	 */
	public function lookup_user(){
		
		$this->load->model('laboratory_model', 'labModel');
		$obj		= new stdClass();
		$obj->val = $this->input->post('filter_val'); 
		$rows   = $this->labModel->lookup_user($obj);
		
		
		$json_array = array();
		foreach ($rows as $row){
			array_push($json_array, $row->id.' - '.$row->fullname);
		}
 
		echo json_encode($json_array); 
		 
	}
	public function get_report_by_ajax(){ 
		$this->load->model('laboratory_model', 'labModel'); 
		
		$obj		= new stdClass();
		$obj->user_id = $this->input->post('user_id'); 
		$result = $this->labModel->get_report_name($obj);
 		
		$data = "";
		foreach ($result as $row){
			$check="";
			if($row->is_assign!=0){
				$check = " checked";
			}
			$data.='<input type="checkbox" '.$check.' onClick="onReportCheck('.$row->id.');" id="rep'.$row->id.'" /><label for="rep'.$row->id.'">'.$row->report_name.'</label><br />';
		}
 
		echo $data; 
		 
	}
	/* on checking assign report to user */
	public function assign_report_to_user(){ 
		$this->load->model('laboratory_model', 'labModel'); 
		
		$obj		= new stdClass();
		$obj->rep_id = $this->input->post('rep_id'); 
		$obj->status = $this->input->post('status'); 
		$obj->user_id = $this->input->post('user_id'); 
		$this->labModel->in_del_assing_report($obj); 
	}
}