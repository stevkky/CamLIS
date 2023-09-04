<?php
class LabAdminLeftNavigation extends Widget {

	public function display($data) {
		$this->view('template/lab_admin_left_navigation', $data);
	}
}