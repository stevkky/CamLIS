<?php
defined('BASEPATH') OR exit('No direct script allowed.');
class MY_Model extends CI_Model {
	protected $user_id; //Current Login user
	protected $timestamp; //Current timestamp
	protected $laboratory_id; //Current Laboratory ID
    protected $laboratory_code;
	protected static $CamLIS_db;
	protected static $PMRS_db;
	protected static $PMRS_share_db;
	
	public function __construct() {
		parent::__construct();

        $this->timestamp		= date('Y-m-d H:i:s');
        $this->user_id			= $this->aauth->get_user_id();
		$this->laboratory_id	= CamlisSession::getLabSession('labID');
		$this->laboratory_code	= CamlisSession::getLabSession('lab_code');

		self::$CamLIS_db		= 'public';
		self::$PMRS_db			= 'pmrs';
		self::$PMRS_share_db	= 'pmrs_share';
	}

	public function lastQuery() {
		return $this->db->last_query();
	}
}