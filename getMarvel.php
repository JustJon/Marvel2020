<?php
/**********************************************************************************
getMarvel.php
API to retrienve and store Series and Character lookups based on a letter
Copyright Jonathan Lazar 2020
**********************************************************************************/

header("Content-Type: application/json;charset=utf-8");

require_once 'includes/header.php';

$type=$_REQUEST['type'];
$alpha=$_REQUEST['alpha'];

if (in_array(strtoupper($alpha), $alphabet)) {

	$status = "OK";
	$code = 200;
	$marvel = new Marvel(MARVEL_KEY, MARVEL_SECRET);
	switch ($type) {
		case 'characters':
			$data = $marvel->getCharacters($alpha);
			break;
		case 'series':
			$data = $marvel->getSeries($alpha);
			break;
		default:
			$data = [];
			$status = 'Invalid search type';
			$code = 400;
			break;
	}
} else {

	$data = [];
	$code = 400;
	$status = "Illegal character";
}

$output['code'] = $code;
$output['status'] = $status;
$output['data'] = $data;

echo json_encode($output);

