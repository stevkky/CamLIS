<?php
defined('BASEPATH') OR die("Access denied!");
class Comment extends MY_Controller {
	public function __construct() {
		parent::__construct();
		$this->load->model('comment_model');
	}

	/**
	 * Add standard comment
	 */
	public function add_std_comment() {
		$this->app_language->load('admin');

		$comment		= $this->input->post('comment');
		$comment		= trim($comment);
		$sample_types	= $this->input->post('sample_type');
		$is_reject      = $this->input->post('is_reject_comment');
		$msg			= _t('global.msg.save_fail');
		$status			= FALSE;

		if (!empty($comment) && is_array($sample_types) && count($sample_types) > 0) {
			//Check if comment is exists
			if (count($this->comment_model->get_std_comment(array("comment" => $comment))) > 0) {
				$msg = _t('admin.msg.comment_exist');
			} else {
				$comment_id = $this->comment_model->add_std_comment(array('comment' => $comment, 'is_reject_comment' => $is_reject));
				if ($comment_id > 0) {
					$status = TRUE;
					$msg = _t('global.msg.save_success');

					//Assign Comment to Sample Type
					$sample_comment_data = array();
					foreach ($sample_types as $sample_type) {
						if ((int)$sample_type > 0) {
							$sample_comment_data[] = array(
								'dep_sample_id' => $sample_type,
								'comment_id'	=> $comment_id
							);
						}
					}

					if (count($sample_comment_data) > 0) $this->comment_model->assign_std_sample_comment($sample_comment_data);
				}
			}
		}

		$this->data['result'] = json_encode(array('status' => $status, 'msg' => $msg));
		$this->load->view('ajax_view/view_result', $this->data);
	}

	/**
	 * Add standard comment
	 */
	public function update_std_comment() {
		$this->app_language->load('admin');

		$comment_id		= $this->input->post('comment_id');
		$comment		= $this->input->post('comment');
		$comment		= trim($comment);
		$sample_types	= $this->input->post('sample_type');
        $is_reject      = $this->input->post('is_reject_comment');
		$msg			= _t('global.msg.update_fail');
		$status			= FALSE;

		if (!empty($comment) && is_array($sample_types) && count($sample_types) > 0 && (int)$comment_id > 0) {
			//Check if comment is exists
			if (count($this->comment_model->get_std_comment(array("comment" => $comment, '"ID" !=' => $comment_id))) > 0) {
				$msg = _t('admin.msg.comment_exist');
			} else {
				$this->comment_model->update_std_comment($comment_id, array('comment' => $comment, 'is_reject_comment' => $is_reject));
				$status = TRUE;
				$msg 	= _t('global.msg.update_success');

				//delete sample comment that are not in list
				$this->comment_model->delete_std_sample_comment($comment_id, $sample_types, FALSE);

				//get current assigned sample
				$assigned_sample = array();
				$sample_comment = $this->comment_model->get_std_sample_comment(array('comment."ID"' => $comment_id));
				if (count($sample_comment) > 0) {
					foreach ($sample_comment as $row) {
						$assigned_sample[] = (int)$row->dep_sample_id;
					}
				}

				//Assign Comment to Sample Type
				$sample_comment_data = array();
				foreach ($sample_types as $sample_type) {
					if ((int)$sample_type > 0 && !in_array((int)$sample_type, $assigned_sample)) {
						$sample_comment_data[] = array(
							'dep_sample_id' => $sample_type,
							'comment_id'	=> $comment_id
						);
					}
				}

				if (count($sample_comment_data) > 0) $this->comment_model->assign_std_sample_comment($sample_comment_data);
			}
		}

		$this->data['result'] = json_encode(array('status' => $status, 'msg' => $msg));
		$this->load->view('ajax_view/view_result', $this->data);
	}

	/**
	 * Delete Standard Comment
	 */
	public function delete_std_comment() {
		$comment_id 	= $this->input->post('comment_id');
		$status 		= FALSE;
		$msg 			= _t('global.msg.delete_fail');

		if ($comment_id > 0) {
			if ($this->comment_model->update_std_comment($comment_id, array('status' => FALSE)) > 0) {
				$status = TRUE;
				$msg = _t('global.msg.delete_success');

				$this->comment_model->delete_std_sample_comment($comment_id);
			} else {
				$msg = _t('global.msg.delete_fail');
			}
		}
		$this->data['result'] = json_encode(array('status' => $status, 'msg' => $msg));
		$this->load->view('ajax_view/view_result', $this->data);
	}

	/**
	 * View Standard Comment (DataTable)
	 */
	public function view_std_sample_comment() {
		$result			= $this->comment_model->view_std_sample_comment($this->input->post());

		$data['result'] = json_encode($result);
		$this->load->view('ajax_view/view_result', $data);
	}
}