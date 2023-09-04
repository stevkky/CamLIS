<?php
class CamLISReportNavigation extends Widget {

	public function display($data) { 
		$this->view('template/camlis_report_navigation', $data);
	}
}