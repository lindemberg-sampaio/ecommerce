<?php

	use \Hcode\Model\User;

	function formatPrice(float $vlprice)
	{

		return number_format($vlprice, 2, ",", "."); // o primeiro separador é a de centena (,) depois o separador de milhar (.)

	}


	function checkLogin($inadmin = true)
	{

		return User::checkLogin($inadmin);

	}


	function getUserName()
	{

		$user = User::getFromSession();

		return $user->getdesperson();


	}



?>