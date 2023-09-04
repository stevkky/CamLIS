<?php
defined('BASEPATH') OR die('Access denied!');

class Payment_type extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('payment_type_model');
    }

    /**
     * Get lab payment type
     */
    public function get_lab_payment_type() {
        $lab_id = $this->input->post('laboratory_id');
        $result = $this->payment_type_model->get_lab_payment_type($lab_id);
        echo json_encode($result);
    }

    /**
     * Add new Standard payment type
     */
    public function add_std_payment_type() {
        $this->app_language->load('admin');
        $name = $this->input->post('name');
        $name = trim($name);
        $msg = _t('global.msg.fill_required_data');
        $status = FALSE;

        if (!empty($name)) {
            if (!$this->payment_type_model->is_unique_payment_type($name)) {
                $msg = _t('admin.msg.payment_type_exist');
            }
            else {
                if ($this->payment_type_model->add_std_payment_type(['name' => $name]) > 0) {
                    $status = TRUE;
                    $msg = _t('global.msg.save_success');
                } else {
                    $msg = _t('global.msg.save_fail');
                }
            }
        }

        $this->data['result'] = json_encode(array('status' => $status, 'msg' => $msg));
        $this->load->view('ajax_view/view_result', $this->data);
    }

    /**
     * Update Standard payment type
     */
    public function update_std_payment_type() {
        $this->app_language->load('admin');
        $id     = $this->input->post('id');
        $name   = $this->input->post('name');
        $name   = trim($name);
        $msg    = _t('global.msg.fill_required_data');
        $status = FALSE;

        if (!empty($name) && $id > 0) {
            if (!$this->payment_type_model->is_unique_payment_type($name, $id)) {
                $msg = _t('admin.msg.payment_type_exist');
            }
            else {
                if ($this->payment_type_model->update_std_payment_type($id, ['name' => $name]) > 0) {
                    $status = TRUE;
                    $msg = _t('global.msg.update_success');
                } else {
                    $msg = _t('global.msg.update_fail');
                }
            }
        }

        $this->data['result'] = json_encode(array('status' => $status, 'msg' => $msg));
        $this->load->view('ajax_view/view_result', $this->data);
    }

    /**
     * Delete standard payment type
     */
    public function delete_std_payment_type() {
        $id = $this->input->post('id');
        $status = FALSE;
        $msg = _t('global.msg.delete_fail');

        if ($id > 0) {
            if ($this->payment_type_model->update_std_payment_type($id, ['status' => FALSE])) {
                $status = TRUE;
                $msg = _t('global.msg.delete_success');
            } else {
                $msg = _t('global.msg.delete_fail');
            }
        }
        $this->data['result'] = json_encode(array('status' => $status, 'msg' => $msg));
        $this->load->view('ajax_view/view_result', $this->data);
    }

    /**
     * Assign payment type to laboratory
     */
    public function assign_lab_payment_type() {
        $assign_payment_type    = $this->input->post('payment_type');
        $data                   = [];
        $msg                    = _t('global.msg.save_fail');

        if (is_array($assign_payment_type)) {
            $lab_id = CamlisSession::getLabSession('labID');
            foreach ($assign_payment_type as $ptype) {
                array_push($data, ['lab_id' => $lab_id, 'payment_type_id' => $ptype]);
            }
        }

        $this->db->trans_start();
        $this->payment_type_model->delete_lab_payment_type();
        if (count($data) > 0) $this->payment_type_model->assign_lab_payment_type($data);
        $this->db->trans_complete();

        if ($this->db->trans_status()) $msg = _t('global.msg.save_success');

        echo json_encode(['status' => $this->db->trans_status(), 'msg' => $msg]);
    }

    /**
     * Delete lab payment type
     */
    public function delete_lab_payment_type() {
        $id = $this->input->post('id');
        $status = FALSE;
        $msg = _t('global.msg.delete_fail');

        if ($id > 0) {
            if ($this->payment_type_model->delete_lab_payment_type($id)) {
                $status = TRUE;
                $msg = _t('global.msg.delete_success');
            } else {
                $msg = _t('global.msg.delete_fail');
            }
        }
        echo json_encode(['status' => $status, 'msg' => $msg]);
    }

    /**
     * View std payment type
     */
    public function view_std_payment_type() {
        $data = $this->input->post();
        $result = $this->payment_type_model->view_std_payment_type($data);
        echo json_encode($result);
    }

    /**
     * View lab payment type
     */
    public function view_lab_payment_type() {
        $data = $this->input->post();
        $result = $this->payment_type_model->view_lab_payment_type($data);
        echo json_encode($result);
    }
}