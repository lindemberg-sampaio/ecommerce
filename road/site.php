<?php
	
	use \Hcode\Page; // Hcode é o vendor principal
	use \Hcode\Model\Product;
	use \Hcode\Model\Category;
	
	$app->get('/', function() {

		$products = Product::listAll();
	    
		$page = new Page();

		$page->setTpl("index", [
			'products'=>Product::checkList($products)
		]);

	});

	$app->get("/categories/:idcategory", function($idcategory) { // rota de CATEGORIAS

		$category = new Category();

		$category->get((int)$idcategory);

		$page = new Page();
		
		$page->setTpl("category", [
			'category'=>$category->getValues(),
			'products'=>Product::checkList($category->getProducts())
		]);

	});

?>