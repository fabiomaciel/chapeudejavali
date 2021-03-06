<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');




$active_group = 'default';
$active_record = TRUE;

$db['default']['hostname'] = $_ENV["OPENSHIFT_MYSQL_DB_HOST"]; 
$db['default']['username'] = $_ENV["OPENSHIFT_MYSQL_DB_USER"];
$db['default']['password'] = $_ENV["OPENSHIFT_MYSQL_DB_PASS"];
$db['default']['database'] = $_ENV["OPENSHIFT_MYSQL_DB_NAME"];
$db['default']['dbdriver'] = 'mysql';
$db['default']['dbprefix'] = '';
$db['default']['pconnect'] = TRUE;
$db['default']['db_debug'] = TRUE;
$db['default']['cache_on'] = FALSE;
$db['default']['cachedir'] = '';
$db['default']['char_set'] = 'utf8';
$db['default']['dbcollat'] = 'utf8_general_ci';
$db['default']['swap_pre'] = '';
$db['default']['autoinit'] = TRUE;
$db['default']['stricton'] = FALSE;


/* End of file database.php */
/* Location: ./application/config/database.php */
