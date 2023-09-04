<?php

ini_set('max_execution_time', 0);
ini_set('memory_limit','2048M');

defined('BASEPATH') OR exit('No direct script access allowed');
class Laboratory extends MY_Controller {
	public function __construct() {
		parent::__construct();
		$this->app_language->load('laboratory');
		$this->load->model(array('laboratory_model' => 'labModel', 'user_model' => 'user_model'));
		
		$this->data['laboratoryInfo'] = new stdClass();
	}
	
	public function index() {
		//Clear Current Laboratory Session
		$this->session->unset_userdata('laboratory');
		
		//Get User Assign Laboratory
		$assign_lab	= $this->session->userdata('user_laboratories');

		if ($this->aauth->is_admin()) {
			$this->data['laboratories'] = $this->labModel->get_laboratory();
		} else {
			$this->data['laboratories'] = $assign_lab && count($assign_lab) > 0 ? $this->labModel->get_laboratory($assign_lab) : array();
		
		}
		// added 21-04-2021
		/**
		 * If user is RRT, redirect them to Exel page
		 */
		if ($this->session->userdata('roleid') == 16){
			redirect('rrt');
		}
		// end
		$this->data['cur_main_page'] = 'laboratory';
		$this->template->stylesheet->add('assets/camlis/css/camlis_laboratory_style.css');
		$this->template->javascript->add('assets/camlis/js/camlis_homepage.js');
		$this->template->content->view('template/pages/laboratories', $this->data);
		$this->template->content_title = _t('page_header');
		$this->template->publish();
	}
	
	/**
	 * Change Lab
	 * @param integer $lab_id Lab's ID
	 */
	public function change($lab_id)
	{
		$laboratory = collect($this->labModel->get_laboratory($lab_id))->first();
		if ($laboratory) {
			$this->session->set_userdata('laboratory', $laboratory);
			
			// checking weekly email

			/** comment out on send email */
			//self::weekly_checking();

			//			
			//Audit user			
			$this->load->model('audit_user_model', 'audit_user');
			$audit_user = array(
				'user_id' => $this->aauth->get_user_id() ,
				'lab_id' => $lab_id, 
				'ip_address' => $this->input->ip_address(),
				'timestamp' => date('Y-m-d H:i:s')
			);
			$this->audit_user->insert($audit_user);
			$group = collect($this->aauth->get_user_groups())->first();
			
			/** 
			 * If username is visitor we will redirect them pid generator page
			 * ADDED: 11 Jan 2021
			 */
			if(strtolower($this->session->userdata('username')) == "pid"){
				redirect("generate");
			 }
			 /**End */
			 
            redirect($this->app_language->site_url(empty($group->default_page) ? 'default' : $group->default_page));
		}
		else {
			redirect('laboratory');
		}
	}
	
	
	/*function checking email send for weekly*/
	function weekly_checking(){ 
			
			/* get max date last record */
				$sql = "select 
							DATE_PART('day', now() - max(date))  weekly,
                            to_char(now(),'YYYY-mm-dd') next_mail_date 
                        from weekly_mail
                ";
				$result_wkl = $this->db->query($sql)->row();

				// get all labo
                $sql_labo = "select * from camlis_laboratory where status = TRUE";
                $result_labo = $this->db->query($sql_labo)->result_array();


                // checking every week on Wednesday
				if($result_wkl->weekly>='7') {
					$obj_value = '';
                    $obj = new stdClass();
                    //
                    /*$sql = "
                            select
                                sto.ID,
                                sto.sample_test_id,
                                om.organism_id,
                                om.organism_map_name,
                                om.organism_map_kh,
                                om.description,
                                tr.test_date

                            from camlis_std_test_organism  sto
                            inner join camlis_organism_map om on om.organism_id = sto.organism_id
                            inner join camlis_ptest_result tr on tr.result = sto.ID
                            where om.`type` = 2
                            and tr.test_date >= ?";*/

                    foreach ($result_labo as $row_labo) {

                        $sql = "
							select  
                                sto.\"ID\",
                                sto.sample_test_id, 
                                om.organism_id,
                                om.organism_map_name,
                                om.organism_map_kh,
                                om.description,
                                vpstr.test_date  
                            from _view_patient_sample_test_result vpstr 
                            inner join camlis_std_test_organism sto on vpstr.result = sto.\"ID\" 
                            inner join camlis_organism_map om on om.organism_id = sto.organism_id
							where om.type = 2
							and vpstr.labid = '" . $row_labo["labID"] . "'
							and vpstr.test_date >= ?";
                        $result_list = $this->db->query($sql, array($result_wkl->next_mail_date))->result_array();

                        // checking count
                        if (count($result_list) >= 3) {

                            foreach ($result_list as $row) {
                                //$obj_value.= "<li>".$row["organism_id"].' : '.$row["organism_map_name"].' - '.$row["organism_map_kh"]."</li>";
                                $obj_value .= "<li> " . $row["organism_map_name"] . "</li>";
                            }
                            // model
                            $this->load->model('email_model');
                            // mail for 1 week cheching
                            $obj->labo_name = $row_labo["name_en"];
                            $obj->diseases = $obj_value;
                            $obj->number = count($result_list);
                            $this->email_model->email_weekly($obj);
                            // end
                        }
                    }


					
					// insert log date for sent mail every weekly 
					$this->db->set('mail', $obj_value);
					$this->db->set('date', date('Y-m-d'));
					$this->db->set('status', TRUE);
					$this->db->insert("weekly_mail");
				}
	}

	/**
	 * Add new laboratory
	 */
	public function add_new_laboratory() {
		$this->app_language->load('admin');

        $this->form_validation->set_rules('name_en', 'lab_name_en', 'required|trim');
        $this->form_validation->set_rules('name_kh', 'lab_name_kh', 'required|trim');
        $this->form_validation->set_rules('lab_code', 'lab_code', 'required|trim|min_length[3]|max_length[5]');
        $this->form_validation->set_rules('address_en', 'address_en', 'trim');
        $this->form_validation->set_rules('address_kh', 'address_kh', 'trim');
        $this->form_validation->set_rules('sample_number', 'sample_number', 'required|trim|in_list[1,2]');

        $labData = elements(['name_en', 'name_kh', "lab_code", "address_en", "address_kh", "sample_number"], (array)$this->input->post());
        $labData['lab_code'] = strtoupper($labData['lab_code']);

		$msg = _t('global.msg.fill_required_data');
		$status = FALSE;
        if ($this->form_validation->run() === TRUE) {
            if (!$this->labModel->is_unique_laboratory($labData['name_en'], $labData['name_kh'], $labData['lab_code'])) {
                $msg = _t('admin.msg.laboratory_exist');
            } else {
                $msg = _t('global.msg.save_fail');
                $laboratory_id = $this->labModel->add_laboratory($labData);
                if ($laboratory_id > 0) {
                    $status = TRUE;
                    $msg = _t('global.msg.save_success');

                    //Set Variables
                    $variables = array(
                        array('lab_id' => $laboratory_id, 'data_key' => 'left-result-footer', 'value' => 'ត្រួតពិនិត្យដោយ', 'status' => TRUE),
                        array('lab_id' => $laboratory_id, 'data_key' => 'middle-result-footer', 'value' => 'Verified By', 'status' => TRUE),
                        array('lab_id' => $laboratory_id, 'data_key' => 'right-result-footer', 'value' => 'បុគ្គលិកមន្ទីរពិសោធន៍', 'status' => TRUE)
                    );
                    $this->labModel->set_variables($variables);
                }
            }
        }

		$data['result'] = json_encode(array('status' => $status, 'msg' => $msg));
		$this->load->view('ajax_view/view_result', $data);
	}
	
	/*
	 * Update Lab 
	 */
	public function update() {
		$this->app_language->load('admin');

        $this->form_validation->set_rules('laboratory_id', 'laboratory_id', 'required|trim|greater_than[0]');
        $this->form_validation->set_rules('name_en', 'lab_name_en', 'required|trim');
        $this->form_validation->set_rules('name_kh', 'lab_name_kh', 'required|trim');
        $this->form_validation->set_rules('lab_code', 'lab_code', 'required|trim|min_length[3]|max_length[5]');
        $this->form_validation->set_rules('address_en', 'address_en', 'trim');
        $this->form_validation->set_rules('address_kh', 'address_kh', 'trim');
        $this->form_validation->set_rules('sample_number', 'sample_number', 'required|trim|in_list[1,2]');

        $labData 					= elements(['name_en', 'name_kh', "lab_code", "address_en", "address_kh", "sample_number"], $this->input->post());
        $labData['lab_code'] 		= strtoupper($labData['lab_code']);
        $laboratory_id  			= $this->input->post('laboratory_id');
        $clear_photo    			= $this->input->post('clear_photo') == 200;

		//File Upload config
		$config['upload_path']   	= './assets/camlis/images/laboratory/';
		$config['allowed_types'] 	= 'jpg|jpeg|png';
		$config['file_name']     	= $laboratory_id.'-'.date("YmdHis");
		
		$msg 						= _t('global.msg.fill_required_data');
		$status 					= FALSE;

		if ($this->form_validation->run() === TRUE) {
            if (!$this->labModel->is_unique_laboratory($labData['name_en'], $labData['name_kh'], $labData['lab_code'], $laboratory_id)) {
				$msg = _t('admin.msg.laboratory_exist');
			}
			else
			{
			    if ($clear_photo) {
                    $labData['photo'] = NULL;
                }
				else if (isset($_FILES['lab_icon']) && $_FILES['lab_icon']['error'] == 0) {
					$this->load->library("upload", $config);
					
					if (!file_exists($config['upload_path'])) mkdir($config['upload_path'], '0777', TRUE);
					if (file_exists($config['upload_path']) && $this->upload->do_upload('lab_icon')) {
						$file 				= $this->upload->data();
						$labData['photo'] 	= $file['file_name'];
					}
				}

				$result = $this->labModel->update_laboratory($laboratory_id, $labData);
				if ($result > 0) {
					$status 	= TRUE;
					$msg 		= _t('global.msg.update_success');
				}
			}
		}

		$data['result'] = json_encode(array('status' => $status, 'msg' => $msg, 'result_update'=>$result));
		$this->load->view('ajax_view/view_result', $data);
	}

	/**
	 * Delete Laboratory
	 */
	public function delete() {
		$laboratory_id	= $this->input->post("laboratory_id");
		$msg = _t('global.msg.delete_fail');
		$status = FALSE;
		
		if (!empty($laboratory_id) && (int)$laboratory_id > 0) {
			$result = $this->labModel->update_laboratory($laboratory_id, array('status' => FALSE));
			if ($result > 0) {
				$status = TRUE;
				$msg = _t('global.msg.delete_success');
			}
		}

		$data['result'] = json_encode(array('status' => $status, 'msg' => $msg));
		$this->load->view('ajax_view/view_result', $data);
	}

	/**
	 * View All Laboratory
	 */
	public function view_all_laboratory() {
		$_data			= new stdClass();
		$_data->reqData	= $this->input->post();
		
		$result			= $this->labModel->view_all_laboratory($_data);
		
		$data['result']	= json_encode($result);
		$this->load->view('ajax_view/view_result', $data);
	}

    /**
     * Set More property for Laboratory
     */
	public function set_variables() {
        $varialbes  = (array)$this->input->post("varialbes");
        $data       = array();
        foreach ($varialbes as $varialbe) {
            if (isset($varialbe['data_key']) && !empty($varialbe['data_key'])) {
                $data[] = array(
                    'data_key' => $varialbe['data_key'],
                    'value'    => isset($varialbe['value']) ? $varialbe['value'] : '',
                    'status'   => isset($varialbe['status']) && (int)$varialbe['status'] > 0 ? $varialbe['status'] : 0
                );
            }
        }

        $result = 0;
        $msg    = _t('global.msg.save_fail');
        if (count($data) > 0) {
            if ($result = $this->labModel->set_variables($data) > 0) {
                $msg = _t('global.msg.save_success');
            }
        }

        $data['result']	= json_encode(['status' => $result > 0 ? TRUE : FALSE, 'msg' => $msg]);
        $this->load->view('ajax_view/view_result', $data);
    }
	public function grid(){
        //Get User Assign Laboratory
		$assign_lab	= $this->session->userdata('user_laboratories');

		if ($this->aauth->is_admin()) {
			$this->data['laboratories'] = $this->labModel->get_laboratory();
		} else {
			$this->data['laboratories'] = $assign_lab && count($assign_lab) > 0 ? $this->labModel->get_laboratory($assign_lab) : array();
		
		}
        $this->data['cur_main_page'] = 'Grid';
		$this->template->stylesheet->add('assets/camlis/css/camlis_laboratory_style.css');
		$this->template->javascript->add('assets/camlis/js/camlis_homepage.js');
		$this->template->content->view('template/pages/grid', $this->data);
		$this->template->content_title = _t('page_header');
		$this->template->publish();
    }
}
