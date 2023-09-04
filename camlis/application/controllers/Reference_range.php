<?php
defined('BASEPATH') OR die('Access denied!');

class Reference_range extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('reference_range_model');
    }

    public function get_lab_reference_range() {
        $sample_test_id   = $this->input->post('sample_test_id');
        $reference_ranges = [];

        if ($sample_test_id > 0) {
            $reference_ranges = $this->reference_range_model->get_lab_reference_range([$sample_test_id]);
            if (count($reference_ranges) == 0) $reference_ranges = $this->reference_range_model->get_std_reference_range([$sample_test_id]);
        }

        $data['result'] = json_encode($reference_ranges);
        $this->load->view('ajax_view/view_result', $data);
    }

    public function set_lab_reference_range() {
        $msg              = _t('global.msg.update_fail');
        $sample_test_id   = $this->input->post('sample_test_id');
        $reference_ranges = $this->input->post('ref_ranges');
        $laboratory_id    = $this->data['laboratoryInfo']->labID;

        if (count($reference_ranges) > 0) {
            foreach ($reference_ranges as & $reference_range) {
                $reference_range['lab_id'] = $laboratory_id;
            }
        }

        if ($sample_test_id > 0 && $laboratory_id > 0) {
            $this->db->trans_start();
            $this->reference_range_model->delete_lab_reference_range($sample_test_id);
            if (count($reference_ranges) > 0) $this->reference_range_model->set_lab_reference_range($sample_test_id, $reference_ranges);
            $this->db->trans_complete();
        }

        if ($status = $this->db->trans_status()) $msg = _t('global.msg.update_success');

        $data['result'] = json_encode(['msg' => $msg, 'status' => $status]);
        $this->load->view('ajax_view/view_result', $data);
    }
}