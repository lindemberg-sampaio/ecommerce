<?php

	function formatPrice(float $vlprice)
	{
		return number_format($vlprice, 2, ",", "."); // o primeiro separador é a de centena (,) depois o separador de milhar (.)
	}



?>