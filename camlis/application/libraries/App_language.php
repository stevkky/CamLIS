<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class App_language {
	private $CI;
	private $all_languages;
	private $special_uri;
	private $use_cookie;
	private $cookie_name;
	private $cookie_expiration;
	private $cookie_domain;
	private $cookie_path;
	private $cookie_prefix;
	private $cookie_secure;
	private $cookie_httponly;
	
	public function __construct() {
		global $CFG;
		
		$this->CI = & get_instance();
		
		//Don't check for ajax request
		/*if ($this->CI->input->is_ajax_request()) {
			return false;
		}*/
		
		//Load Helper and Config File
		$this->CI->load->config('app_language', TRUE);
		
		$this->all_languages		= $CFG->item('languages', 'app_language');
		$this->special_uri			= $CFG->item('special_uri', 'app_language');
		$this->use_cookie			= $CFG->item('use_cookie', 'app_language');
		$this->cookie_name			= $CFG->item('cookie_name', 'app_language');
		$this->cookie_expiration	= $CFG->item('cookie_expiration', 'app_language');
		$this->cookie_domain		= $CFG->item('cookie_domain', 'app_language');
		$this->cookie_path			= $CFG->item('cookie_path', 'app_language');
		$this->cookie_prefix		= $CFG->item('cookie_prefix', 'app_language');
		$this->cookie_secure		= $CFG->item('cookie_secure', 'app_language');
		$this->cookie_httponly		= $CFG->item('cookie_httponly', 'app_language');
		
		$this->special_uri = array('godata','generate','generatedev','api','rrt','exceljs'); // added 25 Jan 2021

		//Delete Language Cookie is user don't use cookie
		if (!$this->use_cookie) {
			delete_cookie($this->cookie_name);
		}
		
		$this->setLanguage();
		$this->loadeDefaultLanguageFile();
	}
	
	//Get default language
	private function getDefaultLanguage() {
		global $CFG;
		$default_lang = $CFG->item('default_language', 'app_language');
		
		if ($default_lang) {
			return $default_lang;
		}
		else {
			//return first language
			foreach ($this->all_languages as $lang => $name) {
				return $lang;
			}
		}
		
		return NULL;
	}
	
	//Set language
	private function setLanguage() {
		global $CFG;
		$segment					= $this->CI->uri->segment(1);
		$cookie_lang				= get_cookie($this->cookie_name);
		$cur_lang					= $segment;
		$this->cookie_expiration	= $this->cookie_expiration == NULL || $this->cookie_expiration == '' ? -1 : $this->cookie_expiration;
		
		//Get Language from cookie
		if ($this->use_cookie && isset($this->all_languages[$cookie_lang]) && !$this->hasLanguage($segment)) {
			$cur_lang = $cookie_lang;
		}
		
		//Has Language in URI or has Language in Cookie
		if (isset($this->all_languages[$cur_lang]))
		{
			//Set to current uri language
			if ($this->use_cookie && $segment != $cookie_lang) {
				set_cookie(
					$this->cookie_name,
					$cur_lang,
					$this->cookie_expiration,
					$this->cookie_domain,
					$this->cookie_path,
					$this->cookie_prefix,
					$this->cookie_secure,
					$this->cookie_httponly
				);
			}
			
			$CFG->set_item('language', $cur_lang);
			
			if (!$this->hasLanguage($segment) && !$this->isSpecialURI($segment)) {
				header("Location: " . $CFG->site_url($this->localized(uri_string())), TRUE, 302);
				exit;
			}
			
		}
		else if ($this->isSpecialURI($segment))
		{
			return false;
		}
		else
		{
			if ($this->use_cookie) {
				set_cookie(
					$this->cookie_name,
					$this->getDefaultLanguage(),
					$this->cookie_expiration,
					$this->cookie_domain,
					$this->cookie_path,
					$this->cookie_prefix,
					$this->cookie_secure,
					$this->cookie_httponly
				);
			}
			
			$CFG->set_item('language', $this->getDefaultLanguage());
			
			header("Location: " . $CFG->site_url($this->localized(uri_string())), TRUE, 302);
			exit;
		}
	}
	
	//load default language file
	private function loadeDefaultLanguageFile() {
		global $CFG;
		$default_files = $CFG->item('default_language_file', 'app_language');
		
		if (is_array($default_files) && count($default_files) > 0) {
			foreach ($default_files as $filename) {
				//if (file_exists(APPPATH.'language/'.$this->app_lang().'/'.$filename.'_lang.php')) {
					$this->load($filename);
				//}
			}
		}
	}
	
	//CodeIgniter i18n library by Jérôme Jaglale
	function isSpecialURI($uri)
	{
		$exploded = explode('/', $uri);
		if (in_array($exploded[0], $this->special_uri))
		{
			return TRUE;
		}
		if(isset($this->all_languages[$uri]))
		{
			return TRUE;
		}
		return FALSE;
	}
	
	//CodeIgniter i18n library by Jérôme Jaglale
	//Is there a language segment in this $uri?
	private function hasLanguage($uri)
	{
		$first_segment	= NULL;
		$exploded		= !empty($uri) ? explode('/', $uri) : array();
		
		if(isset($exploded[0]))
		{
			if($exploded[0] != '')
			{
				$first_segment = $exploded[0];
			}
			else if(isset($exploded[1]) && $exploded[1] != '')
			{
				$first_segment = $exploded[1];
			}
		}
		
		if($first_segment != NULL)
		{
			return isset($this->all_languages[$first_segment]);
		}
		
		return FALSE;
	}
	
	//CodeIgniter i18n library by Jérôme Jaglale
	//Add language segment to $uri (if appropriate)
	public function localized($uri)
	{
		if($this->hasLanguage($uri) || $this->isSpecialURI($uri) || preg_match('/(.+)\.[a-zA-Z0-9]{2,4}$/', $uri))
		{
			// we don't need a language segment because:
			// - there's already one or
			// - it's a special uri (set in $special_uri) or
			// - that's a link to a file
		}
		else
		{
			$uri = $this->app_lang() . '/' . $uri;
		}
		
		return $uri;
	}
	
	/**
	 * Current App Language
	 * @return string Current Set Language
	 */
	public function app_lang() {
		global $CFG;
		global $URI;

		$lang	= $CFG->item('language');
		if (isset($this->all_languages[$lang]))
		{
			return $lang;
		}
		
		return $this->getDefaultLanguage();
	}
	
	//Switch Language
	public function switchLanguage($lang) {
		global $CFG;
		global $URI;
		
		$uri_string = $URI->uri_string();
		
		if ($uri_string != "") {
			$uri_arr	= explode('/', $uri_string);
			
			//current language
			if ($uri_arr[0] == $this->app_lang()) {
				$uri_arr[0] = $lang;
			}
			
			$uri_string = implode('/', $uri_arr);
		}
		
		return $CFG->site_url().$uri_string;
	}
	
	/**
	 * Load Language file with current language
	 */
	public function load($files)
	{
		$this->CI->lang->load($files, $this->app_lang());
	}
	
	public function site_url($uri = '', $protocol = NULL)
	{
		if (is_array($uri))
		{
			$uri = implode('/', $uri);
		}
		else {
			$uri	= $this->localized($uri);
		}
		
		return site_url($uri, $protocol);
	}
	
	public function anchor($uri = '', $title = '', $attributes = '')
	{
		$uri = $this->site_url($uri);
		return anchor($uri, $title, $attributes);
	}
}