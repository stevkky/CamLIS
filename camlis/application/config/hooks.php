<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| Hooks
| -------------------------------------------------------------------------
| This file lets you define "hooks" to extend CI without hacking the core
| files.  Please see the user guide for info:
|
|	http://codeigniter.com/user_guide/general/hooks.html
|
*/

$hook['post_controller_constructor'][] = array(
	'class'		=> 'Environment',
	'function'	=> 'checkEnvironment',
	'filename'	=> 'Environment.php',
	'filepath'	=> 'hooks'
);

$hook['post_controller_constructor'][] = array(
    'class'    => 'AppIdleTime',
    'function' => 'checkIdleTime',
    'filename' => 'AppIdleTime.php',
    'filepath' => 'hooks'
);

$hook['post_controller_constructor'][] = array(
	'class'		=> 'Authorization',
	'function'	=> 'isLogin',
	'filename'	=> 'Authorization.php',
	'filepath'	=> 'hooks'
);

$hook['post_controller_constructor'][] = array(
	'class'		=> 'Authorization',
	'function'	=> 'laboratoryAccess',
	'filename'	=> 'Authorization.php',
	'filepath'	=> 'hooks'
);