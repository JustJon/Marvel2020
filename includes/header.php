<?php
/**********************************************************************************
header.php
Site wide includes so we only have to include one file across all pages 
Copyright Jonathan Lazar 2015
**********************************************************************************/

define('BASE', '/var/www/html/');
define('BASEURL', 'http://dev.justjon.net/');

define('DB_HOST', 'localhost');
define('DB_USER', '');
define('DB_PASSWORD', '');
define('DB_DBNAME', 'marvel');

//Include our configuration files
require_once BASE.'includes/config.php';

//Load classes
require_once BASE.'classes/marvel.php';


//Avatar image formats
$typelist[0]='image/gif';
$typelist[1]='image/png';
$typelist[2]='image/jpg';
$typelist[3]='image/jpeg';

date_default_timezone_set('America/New_York');

$alphabet = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z'];
