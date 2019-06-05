<?php
	
	use \Hcode\Page; // Hcode é o vendor principal
	

	$app->get('/', function() {
	    
		$page = new Page();

		$page->setTpl("index");

	});

?>