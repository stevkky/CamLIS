<?php
defined('BASEPATH') or die('Access denied!');
class User extends MY_Controller {
	public function __construct() {
		parent::__construct();
		$this->load->model('user_model');
		$this->app_language->load('user');
	}

	public function profile() {
	    $this->data['user'] = $user = $this->aauth->get_user();
	    $this->template->content_title = _t("user.account");
        $this->template->javascript->add('assets/camlis/js/camlis_user_profile.js');
        $this->template->content->view('template/pages/profile', $this->data);
        $this->template->publish();
    }

    /**
     * Add New User
     */
	public function save() {
        $this->form_validation->set_rules(array(
            array(
                'field' => 'username',
                'label' => 'Username',
                'rules' => 'trim|required'
            ),
            array(
                'field' => 'fullname',
                'label' => 'Fullname',
                'rules' => 'trim|required'
            ),
            array(
                'field' => 'password',
                'label' => 'Password',
                'rules' => 'trim|required|matches[confirm_password]'
            ),
            array(
                'field' => 'confirm_password',
                'label' => 'Confirm Password',
                'rules' => 'trim|required'
            ),
        ));

        $result = 0;
        $msg    = _t('global.msg.save_fail');

        if ($this->form_validation->run() === TRUE) {
            $laboratory = $this->input->post("laboratory");
            $password   = $this->input->post("password");
            $_data = array(
                "fullname" => $this->input->post("fullname"),
                "username" => $this->input->post("username"),
                "pass"     => $this->aauth->hash_password($password, 0),
                "email"    => $this->input->post("email"),
                "phone"    => $this->input->post("phone"),
                "location" => $this->input->post("location"),
                "province_code" => $this->input->post("province")
            );

            if (count($this->user_model->get_user($_data["username"])) > 0) {
                $msg = _t("user.msg.user_exist");
            }
            else {
                $id = $this->user_model->add_user($_data);
                if ($id > 0) {
                    $result = $this->user_model->update_user($id, array('pass' => $this->aauth->hash_password($password, $id)));
                    if ($laboratory > 0) $this->user_model->assign_user_laboratory($id, array($laboratory));
                }
                if ($result > 0) $msg = _t("global.msg.save_success");
            }
        }

        $data['result']	= json_encode(array('status' => $result > 0 ? TRUE : FALSE, 'msg' => $msg));
        $this->load->view('ajax_view/view_result', $data);
    }

    /**
     * Update User
     * @param $user_id
     * @param $reset_login
     */
    public function update($user_id = NULL, $reset_login = FALSE) {
        $this->form_validation->set_rules(array(
            array(
                'field' => 'username',
                'label' => 'Username',
                'rules' => 'trim|required'
            ),
            array(
                'field' => 'fullname',
                'label' => 'Fullname',
                'rules' => 'trim|required'
            ),
            array(
                'field' => 'password',
                'label' => 'Password',
                'rules' => 'trim'
            ),
            array(
                'field' => 'confirm_password',
                'label' => 'Confirm Password',
                'rules' => 'trim'
            ),
        ));

        if (empty($user_id)) $user_id = $this->input->post('user_id');
        $result  = 0;
        $msg     = _t('global.msg.update_fail');
        if ($this->form_validation->run() === TRUE && is_numeric($user_id) && $user_id > 0) {
            $password = $this->input->post("password");
            $confirm_password = $this->input->post("confirm_password");
            $_data = array(
                "fullname" => $this->input->post("fullname"),
                "username" => $this->input->post("username"),
                "email"    => $this->input->post("email"),
                "phone"    => $this->input->post("phone"),
                "location" => $this->input->post("location"),
                "province_code" => $this->input->post("province")
            );
            $users  = $this->user_model->get_user($_data["username"], $user_id, FALSE);

            if (!empty($password) && $password == $confirm_password) {
                $_data['pass'] = $this->aauth->hash_password($password, $user_id);
            }

            if (count($users) > 0) {
                $msg = _t("user.msg.user_exist");
            } else {
                $result = $this->user_model->update_user($user_id, $_data);
                if ($result > 0) {
                    $msg = _t("global.msg.update_success");
                    if ($reset_login) $this->aauth->login($_data['username'], $password);
                }
            }
        }

        $data['result']	= json_encode(array('status' => $result > 0 ? TRUE : FALSE, 'msg' => $msg));
        $this->load->view('ajax_view/view_result', $data);
    }

    /**
     * Delete User
     */
    public function delete() {
        $user_id = $this->input->post('user_id');
        $result  = 0;
        $msg     = _t("global.msg.delete_fail");
        if ($user_id > 0) {
            $result = $this->user_model->update_user($user_id, array('banned' => true));
            if ($result > 0) $msg =_t("global.msg.delete_success");
        }

        $data['result']	= json_encode(array('status' => $result > 0 ? TRUE : FALSE, 'msg' => $msg));
        $this->load->view('ajax_view/view_result', $data);
    }

    /**
     * Verify User with Password
     */
    public function verify_password() {
        $user_id  = $this->input->post("user_id");
        $password = $this->input->post("password");
        $result   = false;
        $msg      = "";

        $user_id  = empty($user_id) || (int)$user_id <= 0 ? $this->aauth->get_user_id() : $user_id;
        if ($user_id > 0) {
            $result = $this->user_model->verify_password($this->aauth->hash_password($password, $user_id), $user_id);
        }
        $data['result']	= json_encode(array('status' => $result, 'msg' => $msg));
        $this->load->view('ajax_view/view_result', $data);
    }

    /**
     * Update User Profile
     */
    public function updateProfile() {
        $result  = 0;
        $msg     = _t('global.msg.update_fail');
        $user_id = $this->aauth->get_user_id();

        if ($this->aauth->is_loggedin() && is_numeric($user_id) && $user_id > 0) {
            $this->update($user_id, TRUE);
        } else {
            $data['result']	= json_encode(array('status' => $result, 'msg' => $msg));
            $this->load->view('ajax_view/view_result', $data);
        }
    }

	/**
	 * Login
	 */
	public function login()
    {
        $this->form_validation->set_rules(array(
            array(
                'field' => 'username',
                'label' => 'Username',
                'rules' => 'trim|required'
            ),
            array(
                'field' => 'password',
                'label' => 'Password',
                'rules' => 'trim|required'
            )
        ));

        if ($this->form_validation->run() === TRUE) {
            $username = $this->input->post('username');
            $password = $this->input->post('password');

            if ($this->aauth->login($username, $password)) {
                //Set User's assigned laboratory
            
                $user_laboratory = $this->user_model->get_user_laboratory($this->aauth->get_user_id());
                $laboratories    = array();
                foreach ($user_laboratory as $row) {
                    $laboratories[] = (int)$row->lab_id;
                }
                $this->session->set_userdata('user_laboratories', $laboratories); 

				if (!$this->aauth->is_admin() && count($laboratories) == 1 && end($laboratories) > 0) {                    
                    redirect("laboratory/change/".end($laboratories));
                }
                
                redirect("laboratory");

            } else {
                $this->session->set_tempdata("login_errors", $this->aauth->get_errors_array(), 1);
                redirect($this->app_language->app_lang()."/login");
            }
        } 

        redirect($this->app_language->app_lang()."/login");
    }
	/**
	 * Logout
	 */
	public function logout() {
	    $this->aauth->logout();
		$this->session->unset_userdata('laboratory');
		$this->session->unset_userdata('user');
		$this->session->sess_destroy();
		
		redirect($this->app_language->app_lang().'/login');
	}

	/**
	 * View all user
	 */
	public function view_all_user() {
		$_data				= new stdClass();
		$_data->reqData		= $this->input->post();
		
		$user				= $this->user_model->view_all_user($_data);
		$data['result']		= json_encode($user);
		$this->load->view('ajax_view/view_result', $data);
	}

    /**
     * View all user roles
     */
	public function view_all_user_roles() {
	    $this->app_language->load('admin');
        $data = $this->input->post();
        $roles = $this->user_model->view_all_user_roles($data);
        $this->load->view('ajax_view/view_result', ['result' => json_encode($roles)]);
    }

	/**
	 * Get User laboratory
	 */
	public function get_user_laboratory() {
		$user_id	= $this->input->post('user_id');
		$result		= array();

		if ((int)$user_id > 0) {
			$user_laboratory = $this->user_model->get_user_laboratory($user_id);
			$result = array_map(function ($row) {
				return (int)$row->lab_id;
			}, $user_laboratory);
		}
		
		$data['result']		= json_encode($result);
		$this->load->view('ajax_view/view_result', $data);
	}

	/**
	 * Assign Lab to user
	 */
	public function assign_user_laboratory() {
		$laboratories	= $this->input->post('laboratories');
		$user_id	    = $this->input->post('user_id');
		$laboratories	= is_array($laboratories) && count($laboratories) > 0 ? $laboratories : array();
		$msg			= _t('global.msg.save_fail');
		$status			= FALSE;
		
		if ((int)$user_id > 0) {
			$_laboratories = array();
			foreach ($laboratories as $lab) {
				if ((int)$lab > 0) $_laboratories[] = $lab;
			}

			$result = $this->user_model->assign_user_laboratory($user_id, $_laboratories);
			if ($result > 0) {
				$status = TRUE;
				$msg = _t('global.msg.save_success');
			}
		}

		$data['result']	= json_encode(array('status' => $status, 'msg' => $msg));
		$this->load->view('ajax_view/view_result', $data);
	}

	/**
	 * Get user's group
	 */
	public function get_user_groups() {
		$user_id = $this->input->post("user_id");
		$groups  = array();
		if ((int)$user_id > 0) {
			$groups = $this->aauth->get_user_groups($user_id);
		}

		$data['result']	= json_encode($groups);
		$this->load->view('ajax_view/view_result', $data);
	}

    /**
     * Add user group
     */
	public function add_user_group() {
	    $this->form_validation->set_rules('name', 'name', 'required|trim');
	    $this->form_validation->set_rules('default_page', 'default_page', 'required|trim');
	    $this->form_validation->set_rules('definition', 'definition', 'required|trim');
	    $status = FALSE;
	    $msg = _t('global.msg.fill_required_data');

	    if ($this->form_validation->run() === TRUE) {
            $data = elements(['name', 'definition', 'default_page', 'permissions'], $this->input->post());

            $this->db->trans_start();
            $group_id = $this->caauth->create_group($data['name'], $data['definition'], $data['default_page']);
            if ($group_id > 0 && count($data['permissions']) > 0) {
                $permissions = collect($data['permissions'])->map(function ($perm_id) use($group_id) { return ['perm_id' => $perm_id, 'group_id' => $group_id]; })->toArray();
                $this->user_model->assign_group_permission($permissions);
            }
            $this->db->trans_complete();

            if ($this->db->trans_status() === TRUE) {
               // $status = FALSE;
               $status = TRUE;
                $msg = _t('global.msg.save_success');
            }
        }

        echo json_encode(compact('status', 'msg'));
    }

    /**
     * Update user group
     */
    public function update_user_group() {
        $this->form_validation->set_rules('group_id', 'group_id', 'required|greater_than[0]');
        $this->form_validation->set_rules('name', 'name', 'required|trim');
        $this->form_validation->set_rules('default_page', 'default_page', 'required|trim');
        $this->form_validation->set_rules('definition', 'definition', 'required|trim');
        $status = FALSE;
        $msg = _t('global.msg.fill_required_data');

        if ($this->form_validation->run() === TRUE) {
            $data = elements(['group_id', 'name', 'definition', 'default_page', 'permissions'], $this->input->post());
            $group_id = $data['group_id'];

            $this->db->trans_start();
            $this->caauth->update_group($data['group_id'], $data['name'], $data['definition'], $data['default_page']);
            $this->user_model->delete_group_permission($group_id);
            if (count($data['permissions']) > 0) {
                $permissions = collect($data['permissions'])->map(function ($perm_id) use($group_id) { return ['perm_id' => $perm_id, 'group_id' => $group_id]; })->toArray();
                $this->user_model->assign_group_permission($permissions);
            }
            $this->db->trans_complete();

            if ($this->db->trans_status() === TRUE) {
                $status = True;
                $msg = _t('global.msg.save_success');
            }
        }

        echo json_encode(compact('status', 'msg'));
    }

    /**
     * Delete user group
     * @param $group_id
     */
    public function delete_user_group($group_id) {
        $status = FALSE;
        $msg    = _t("global.msg.delete_fail");
        if ($group_id > 0 && $this->aauth->delete_group($group_id) === TRUE) {
            $status = FALSE;
            $msg = _t('global.msg.delete_success');
        }
        echo json_encode(compact('status', 'msg'));
    }

	/**
	 * Add User to group
	 */
	public function assign_user_group() {
		$new_groups	= $this->input->post('groups');
		$user_id	= $this->input->post("user_id");
		$new_groups	= is_array($new_groups) && count($new_groups) > 0 ? $new_groups : array();
		$msg		= _t('global.msg.save_fail');
		$status		= FALSE;

		if ((int)$user_id > 0) {
			$assigned_groups = $this->aauth->get_user_groups($user_id);

			$deleted_groups = array();
			foreach ($assigned_groups as $row) {
				if (!in_array($row->group_id, $new_groups)) {
					$deleted_groups[] = $row->group_id;
				}
			}

			//Deleted unassigned groups
			foreach ($deleted_groups as $group_id) {
				$this->aauth->remove_member($user_id, $group_id);
			}

			//Add new groups
			foreach ($new_groups as $group_id) {
				$this->aauth->add_member($user_id, $group_id);
			}

			$msg	= _t('global.msg.save_success');
			$status	= TRUE;
		}

		$data['result']	= json_encode(array('status' => $status, 'msg' => $msg));
		$this->load->view('ajax_view/view_result', $data);
	}
    // added 24 04-2021
    public function get_province(){
        $user_id	= $this->input->post("user_id");
        $user = $this->user_model->get_province($user_id);
        $data['result']	= json_encode($user);
		$this->load->view('ajax_view/view_result', $data);

    }
}