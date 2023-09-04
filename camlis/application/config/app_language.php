<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
Define App Language Configuration
*/
$config['languages'] = [
	'en' => 'english',
	'kh' => 'khmer'
];

//Default Language (if not set, default language will be the first language in Languages config)
$config['default_language']			= 'en';

//Language file to be load every time
$config['default_language_file']	= ['global', 'navigation'];

//This URI won't be redirect with language
$config['special_uri']				= ['login', 'logout', 'rest_api'];

/**
 * Language Cookie Option
*/
$config['use_cookie']				= TRUE;
$config['cookie_name']				= 'camlis_language';
$config['cookie_expiration']		= 604800; //7 Day
$config['cookie_domain']			= '';
$config['cookie_path']				= '/';
$config['cookie_prefix']			= '';
$config['cookie_secure']			= FALSE;
$config['cookie_httponly']			= FALSE;