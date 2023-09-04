<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| File and Directory Modes
|--------------------------------------------------------------------------
|
| These prefs are used when checking and setting modes when working
| with the file system.  The defaults are fine on servers with proper
| security, but you may wish (or even need) to change the values in
| certain environments (Apache running a separate process for each
| user, PHP under CGI with Apache suEXEC, etc.).  Octal values should
| always be used to set the mode correctly.
|
*/
define('FILE_READ_MODE', 0644);
define('FILE_WRITE_MODE', 0666);
define('DIR_READ_MODE', 0755);
define('DIR_WRITE_MODE', 0755);

/*
|--------------------------------------------------------------------------
| File Stream Modes
|--------------------------------------------------------------------------
|
| These modes are used when working with fopen()/popen()
|
*/
define('FOPEN_READ', 'rb');
define('FOPEN_READ_WRITE', 'r+b');
define('FOPEN_WRITE_CREATE_DESTRUCTIVE', 'wb'); // truncates existing file data, use with care
define('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE', 'w+b'); // truncates existing file data, use with care
define('FOPEN_WRITE_CREATE', 'ab');
define('FOPEN_READ_WRITE_CREATE', 'a+b');
define('FOPEN_WRITE_CREATE_STRICT', 'xb');
define('FOPEN_READ_WRITE_CREATE_STRICT', 'x+b');

/*
|--------------------------------------------------------------------------
| Display Debug backtrace
|--------------------------------------------------------------------------
|
| If set to TRUE, a backtrace will be displayed along with php errors. If
| error_reporting is disabled, the backtrace will not display, regardless
| of this setting
|
*/
define('SHOW_DEBUG_BACKTRACE', TRUE);

/*
|--------------------------------------------------------------------------
| Exit Status Codes
|--------------------------------------------------------------------------
|
| Used to indicate the conditions under which the script is exit()ing.
| While there is no universal standard for error codes, there are some
| broad conventions.  Three such conventions are mentioned below, for
| those who wish to make use of them.  The CodeIgniter defaults were
| chosen for the least overlap with these conventions, while still
| leaving room for others to be defined in future versions and user
| applications.
|
| The three main conventions used for determining exit status codes
| are as follows:
|
|    Standard C/C++ Library (stdlibc):
|       http://www.gnu.org/software/libc/manual/html_node/Exit-Status.html
|       (This link also contains other GNU-specific conventions)
|    BSD sysexits.h:
|       http://www.gsp.com/cgi-bin/man.cgi?section=3&topic=sysexits
|    Bash scripting:
|       http://tldp.org/LDP/abs/html/exitcodes.html
|
*/
define('EXIT_SUCCESS', 0); // no errors
define('EXIT_ERROR', 1); // generic error
define('EXIT_CONFIG', 3); // configuration error
define('EXIT_UNKNOWN_FILE', 4); // file not found
define('EXIT_UNKNOWN_CLASS', 5); // unknown class
define('EXIT_UNKNOWN_METHOD', 6); // unknown class member
define('EXIT_USER_INPUT', 7); // invalid user input
define('EXIT_DATABASE', 8); // database error
define('EXIT__AUTO_MIN', 9); // lowest automatically-assigned error code
define('EXIT__AUTO_MAX', 125); // highest automatically-assigned error code

/*
|--------------------------------------------------------------------------
| Application
|--------------------------------------------------------------------------
| Constants that are used in the CamLIS Application
*/
define('PATIENT_MOVED', 2);
define('MALE', 1);
define('FEMALE', 2);
define('ORGANISM_POSITIVE', 1);
define('ORGANISM_NEGATIVE', 2);
define('ANTIBIOTIC_SENSITIVE', 1);
define('ANTIBIOTIC_RESISTANT', 2);
define('ANTIBIOTIC_INTERMEDIATE', 3);
define('PSAMPLE_PENDING', 1);
define('PSAMPLE_PENDING_COLOR', 'red');
define('PSAMPLE_PROGRESSING', 2);
define('PSAMPLE_PROGRESSING_COLOR', 'yellow');
define('PSAMPLE_COMPLETE', 3);
define('PSAMPLE_COMPLETE_COLOR', '#0faf0f');
define('PSAMPLE_PRINTED', 4);
define('PSAMPLE_PRINTED_COLOR', '#1e88e5');
define('PSAMPLE_REJECTED', 5);
define('PSAMPLE_REJECTED_COLOR', '#f9a50b');
define('PSAMPLE_REQUESTED', 6);
define('PSAMPLE_REQUESTED_COLOR', 'red');
define('PSAMPLE_COLLECTED', 7);
define('PSAMPLE_COLLECTED_COLOR', '#1e88e5');

define("FOR_RESEARCH_FIELD_ARRAY",serialize(array(
    '0' => 'Select',
    '1' => 'Suspect',
    '2' => 'Pneumonia',
    '3' => 'Follow Up',
    '4' => 'Contact',
    '5' => 'HCW',
    '6' => 'Other',
    '7' => 'Migrants',
    '8' => 'Passenger',
    '9' => 'Certificate',
    '10' => 'Followup  Positive Cases',
    '11' => 'F20 Event',
    '12' => 'Death'
)));

define("FOR_RESEARCH_FIELD_ARRAY_KH",serialize(array(
    '0' => 'ជ្រើសរើស',
    '1' => 'សង្ស័យ',
    '2' => 'រលាកសួត',
    '3' => 'តាមដាន',
    '4' => 'អ្នកប៉ះពាល់',
    '5' => 'បុគ្គលិកពេទ្យ',
    '6' => 'ផ្សេងទៀត',
    '7' => 'ពលករចំណាកស្រុក',
    '8' => 'អ្នកដំណើរតាមយន្តហោះ',
    '9' => 'វិញ្ញាបនបត្រ',
    '10' => 'តាមដានអ្នកជំងឺវិជ្ជមាន',
    '11' => 'ព្រឹត្តការណ៏ ២០គុម្ភះ',
    '12' => 'ករណីស្លាប់'
)));
//Clinical Symptoms

define("CLINICAL_SYMTOMS_ARRAY_KH",serialize(array(    
    '1' => 'គ្រុនក្តៅ',
    '2' => 'ក្អក',
    '3' => 'ហៀរសំបោរ',
    '4' => 'ឈឺបំពង់ក',
    '5' => 'ពិបាកដកដង្ហើម',
    '6' => 'គ្មាន',
    '7' => 'ផ្សេងទៀត'
)));

define("CLINICAL_SYMTOMS_ARRAY_EN",serialize(array(
    '1' => 'Fever',
    '2' => 'Cough',
    '3' => 'Runny Nose',
    '4' => 'Sore Throat',
    '5' => 'Difficulty Breathing',
    '6' => 'No symptoms',
    '7' => 'Other'  
)));

// Key from GoData
define("CLASSIFICATION_KEY",serialize(array(
    'LNG_REFERENCE_DATA_CATEGORY_CASE_CLASSIFICATION_AIRPLANE_PASSENGER'    =>'Airplane',
    'LNG_REFERENCE_DATA_CATEGORY_CASE_CLASSIFICATION_LAND_CROSSING'         =>'Land crossing',
    'LNG_REFERENCE_DATA_CATEGORY_CASE_CLASSIFICATION_SUSPECT'               =>'Suspect',
    'LNG_REFERENCE_DATA_CATEGORY_CASE_CLASSIFICATION_ILI_SARI'              =>'ILI/SARI',
    'LNG_REFERENCE_DATA_CATEGORY_CASE_CLASSIFICATION_FOLLOWUP'              =>'Followup',
    'LNG_REFERENCE_DATA_CATEGORY_CASE_CLASSIFICATION_CONTACT'               =>'Contact',
    'LNG_REFERENCE_DATA_CATEGORY_CASE_CLASSIFICATION_CERTIFICATE'           =>'Cerficate',
    'LNG_REFERENCE_DATA_CATEGORY_CASE_CLASSIFICATION_PENUMONIA'             =>'Pneumonia',
    'LNG_REFERENCE_DATA_CATEGORY_CASE_CLASSIFICATION_HOTSPOT_SURVEILLANCE'  =>'Hotspot',
    'LNG_REFERENCE_DATA_CATEGORY_CASE_CLASSIFICATION_HEALTH_CARE_WORKER'    =>'Health Care Worker',
    'LNG_REFERENCE_DATA_CATEGORY_CASE_CLASSIFICATION_NOT_A_CASE_DISCARDED'  =>'Case Discarded',
    'LNG_REFERENCE_DATA_CATEGORY_CASE_CLASSIFICATION_SCHOOL'                =>'School',
    'LNG_REFERENCE_DATA_CATEGORY_CASE_CLASSIFICATION_FACTORY_WORKERS'       =>'Factory Workers',
    'LNG_REFERENCE_DATA_CATEGORY_CASE_CLASSIFICATION_CONFIRMED'             =>'Confirmed',
    'LNG_REFERENCE_DATA_CATEGORY_CASE_CLASSIFICATION_EXPORTED_CASES'        =>'Exported Cases',
    'LNG_REFERENCE_DATA_CATEGORY_CASE_CLASSIFICATION_PROBABLE'              =>'Probable',
    'LNG_REFERENCE_DATA_CATEGORY_CASE_CLASSIFICATION_GENERAL_SCREENING'     =>'General Screening'
)));
// added 20-04-2021
define("NUMBER_OF_SAMPLE_DD",serialize(array(
    '0' => 'ជ្រើសរើស',
    '1' => '1',
    '2' => '2',
    '3' => '3',
    '4' => '4',
    '5' => '5',
    '6' => '6',
    '7' => '7',
    '8' => '8',
    '9' => '9',
    '10' => '10',
    '11' => '11',
    '12' => '12',
    '13' => '13',
    '14' => '14',
    '15' => '15',
    '16' => '16',
    '17' => '17',
    '19' => '18',
    '19' => '19',
    '20' => '20',
    '21' => '21',
    '22' => '22',
    '23' => '23',
    '24' => '24',
    '25' => '25',
    '26' => '26',
    '27' => '27',
    '28' => '28',
    '29' => '29',
    '30' => '30'

)));

// added 27-04-2021
define("SARSCOV2_DD",serialize(array(
    '419' => 'SARS-CoV-2 (Method: real time RT-PCR by GeneXpert)',
    '438' => 'SARS-CoV-2 (Applied Biosystems 7500 Fast Real Time PCR)',
    '446' => 'SARS-CoV-2 Rapid Antigen Test',
    '447' => 'SARS-CoV-2 (Method: real time RT-PCR by Cobas 6800)',
	'456' => 'SARS-CoV-2 (BIOER Gene 9660 Real Time PCR Instruments)'
)));
define("VACCINATION_STATUS_DD_KH",serialize(array(    
    '1' => 'មិនបានចាក់',
    '2' => 'ចាក់លើកទី១',
    '3' => 'ចាក់លើកទី២',
    '4' => 'ចាក់លើកទី៣',
    '5' => 'ចាក់លើកទី៤',
)));
define("VACCINATION_STATUS_DD_EN",serialize(array(    
    '1' => 'Not vaccinated',
    '2' => '1 dose',
    '3' => '2 doses',
    '4' => '3 doses',
    '5' => '4 doses',
)));

define('QRCODE_PATH', $_SERVER['DOCUMENT_ROOT'].'/assets/camlis/images/patient_qr_code/');
define('UPLOAD_FOLDER' , $_SERVER['DOCUMENT_ROOT'].'/assets/camlis/upload/');