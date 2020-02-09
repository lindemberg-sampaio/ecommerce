<?php

	use \Hcode\Model\User;

	function formatPrice($vlprice)
	{

		if (!$vlprice > 0) $vlprice = 0;

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