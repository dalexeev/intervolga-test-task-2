<?php

require_once 'models/country.php';

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
	require 'views/countries.php';
	exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	header('Content-Type: text/json');
	
	if (!has_valid_csrf_token()) {
		echo '{"success": false, "error": "ERR_INVALID_CSRF_TOKEN"}';
		exit;
	}
	
	$name = $_POST['name'];
	$code = $_POST['code'];
	if ($code == '') {
		$code = null;
	}
	
	echo json_encode(Country::create($name, $code));
	exit;
}
