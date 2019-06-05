<?php
	
	session_start();

	require_once("vendor/autoload.php");

	use \Slim\Slim;

	$app = new \Slim\Slim();


	$app->config('debug', true);

	require_once("road/site.php");
	require_once("road/admin.php");
	require_once("road/admin-categories.php");
	require_once("road/admin-users.php");
	require_once("road/admin-products.php");



	$app->run();

 ?>