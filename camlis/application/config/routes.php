<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	http://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There are three reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router which controller/method to use if those
| provided in the URL cannot be matched to a valid route.
|
|	$route['translate_uri_dashes'] = FALSE;
|
| This is not exactly a route, but allows you to automatically route
| controller and method names that contain dashes. '-' isn't a valid
| class or method name character, so it requires translation.
| When you set this option to TRUE, it will replace ALL dashes in the
| controller and method URI segments.
|
| Examples:	my-controller/index	-> my_controller/index
|		my-controller/my-method	-> my_controller/my_method
*/
$route['default_controller']	= 'laboratory';
$route['404_override']			= '';
$route['translate_uri_dashes']	= FALSE;

//$route['^([a-zA-Z]{2})/(.+)$']	= '$2';
//$route['^([a-zA-Z]{2})$']		= $route['default_controller'];

$route['sample/new']				= 'sample/new_sample';
$route['sample/new/([\w\d-]+)']		= 'sample/new_sample/$1';

$route['api/bacteriology/(:any)/(:any)/(:any)'] = 'REST_api/bacteriology/labid/$1/start_date/$2/end_date/$3';
$route['api/patientdetail/(:any)/(:any)/(:any)'] = 'REST_api/patientdetail/labid/$1/patientid/$2/labnumber/$3';
$route['api/pathogens/(:any)/(:any)'] = 'REST_api/pathogens/start_date/$1/end_date/$2';

$route['api/login/(:any)/(:any)'] = 'REST_api/login/username/$1/password/$2';
$route['api/get_laboratory'] = 'REST_api/laboratory';