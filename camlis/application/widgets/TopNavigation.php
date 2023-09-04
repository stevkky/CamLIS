<?php
class TopNavigation extends Widget {

    public function display($data) {
        $this->view('template/top_navigation', $data);
    }
    
}