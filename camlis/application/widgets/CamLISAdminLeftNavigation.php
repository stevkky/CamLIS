<?php
class CamLISAdminLeftNavigation extends Widget {

	public function display($data) {
		$this->view('template/camlis_admin_left_navigation', $data);
	}
}