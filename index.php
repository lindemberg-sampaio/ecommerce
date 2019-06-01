<?php
	
	session_start();

	require_once("vendor/autoload.php");

	use \Slim\Slim;
	use \Hcode\Page; // Hcode é o vendor principal
	use \Hcode\PageAdmin;
	use Hcode\Model\User;
	use Hcode\Model\Category;

	$app = new \Slim\Slim();


	$app->config('debug', true);

	$app->get('/', function() {
	    
		$page = new Page();

		$page->setTpl("index");

	});


	/********************************************/
	$app->get('/admin', function() {

		User::verifyLogin(); // verifica se o usuário está autenticado
	    
		$page = new PageAdmin();

		$page->setTpl("index");

	});


	$app->get('/admin/login', function() {

		$page = new PageAdmin([
			"header"=>false,
			"footer"=>false
		]);

		$page->setTpl("login");

	});


	$app->post('/admin/login', function() {
		
		User::login($_POST["login"], $_POST["password"]);
		
		header("Location: /admin");
		exit;
	});

	$app->get('/admin/logout', function() {

		User::logout();
		
		header("Location: /admin/login");
		exit;
	});


	$app->get("/admin/users", function(){ // rota para a tela que irá listar todos os usuários

		User::verifyLogin(); // verifica se o usuário está autenticado, como não está sendo passado nenhum parâmetro, o inadmin por padrão é TRUE e vai verificar se ele é um usuário LOGADO e se TEM ACESSO ao ADMINISTRATIVO

		$users = User::listAll();

		$page = new PageAdmin();

		$page->setTpl("users", array(
			"users"=>$users
		));

	});


	$app->get("/admin/users/create", function() { // rota CREATE de usuários

		User::verifyLogin();

		$page = new PageAdmin();

		$page->setTpl("users-create"); // esse formulário irá enviar um POST para uma outra rota que irá salvar os dados no BD. No caso ele irá enviar para a mesma rota utilizada nesta function: "/admin/users/create", porém com POST

	});


	$app->get("/admin/users/:iduser/delete", function($iduser) { // rota do DELETE

		User::verifyLogin();

		$user = new User();

		$user->get((int)$iduser);

		$user->delete();

		header("Location: /admin/users");
		exit;

	});


	$app->get("/admin/users/:iduser", function($iduser){ // rota do UPDATE de usuário

		User::verifyLogin();

		$user = new User();

		$user->get((int)$iduser);

		$page = new PageAdmin();

		$page->setTpl("users-update", array(
			"user"=>$user->getValues()
		));

	});


	$app->post("/admin/users/create", function() { // rota para SALVAR o CREATE

		User::verifyLogin();

		$user = new User();

		$_POST["inadmin"] = (isset($_POST["inadmin"])) ? 1 : 0;

		$user->setData($_POST);

		$user->save();

		header("Location: /admin/users");
		exit;

	});


	$app->post("/admin/users/:iduser", function($iduser) { // rota para SALVAR o UPDATE

		User::verifyLogin();

		$user = new User();

		$_POST["inadmin"] = (isset($_POST["inadmin"]))?1:0;

		$user->get((int)$iduser);

		$user->setData($_POST);

		$user->update();

		header("Location: /admin/users");
		exit;

	});


	$app->get("/admin/forgot", function() { // rota ESQUECI A SENHA

		$page = new PageAdmin([
			"header"=>false,
			"footer"=>false
		]);

		$page->setTpl("forgot");

	});


	$app->post("/admin/forgot", function() {
		
		$user = User::getForgot($_POST["email"]);

		header("Location: /admin/forgot/sent");
		exit;

	});


	$app->get("/admin/forgot/sent", function() {

		$page = new PageAdmin([
			"header"=>false,
			"footer"=>false
		]);

		$page->setTpl("forgot-sent");

	});

	
	$app->get("/admin/forgot/reset", function() {

		$user = User::validForgotDecrypt($_GET["code"]);

		$page = new PageAdmin([
			"header"=>false,
			"footer"=>false
		]);

		$page->setTpl("forgot-reset", array(
			"name"=>$user["desperson"],
			"code"=>$_GET["code"]
		));

	});


	$app->post("/admin/forgot/reset", function() {

		$forgot = User::validForgotDecrypt($_POST["code"]);

		User::setForgotUsed($forgot["idrecovery"]);

		$user = new User();

		$user->get((int)$forgot["iduser"]);

		$password = password_hash($_POST["password"], PASSWORD_DEFAULT, [
			"cost"=>12
		]);

		$user->setPassword($password);

		$page = new PageAdmin([
			"header"=>false,
			"footer"=>false
		]);

		$page->setTpl("forgot-reset-success");

	});


	$app->get("/admin/categories", function() { // rota de CATEGORIAS

		User::verifyLogin();

		$categories = Category::listAll();


		$page = new PageAdmin();

		$page->setTpl("categories", array(
			"categories"=>$categories
		));
	});


	$app->get("/admin/categories/create", function() {

		User::verifyLogin();

		$page = new PageAdmin();

		$page->setTpl("categories-create");

	});


	$app->post("/admin/categories/create", function() {

		User::verifyLogin();

		$category = new Category();

		$category->setData($_POST);

		$category->save();

		header("Location: /admin/categories");
		exit;

	});


	$app->get("/admin/categories/:idcategory/delete", function($idcategory) {

		User::verifyLogin();

		$category = new Category();

		$category->get((int)$idcategory);

		$category->delete();

		header("Location: /admin/categories");
		exit;

	});


	$app->get("/admin/categories/:idcategory", function($idcategory) {

		User::verifyLogin();

		$category = new Category();

		$category->get((int)$idcategory);

		$page = new PageAdmin();

		$page->setTpl("categories-update", [
			'category'=>$category->getValues()
		]);


	});


	$app->post("/admin/categories/:idcategory", function($idcategory) {

		User::verifyLogin();

		$category = new Category();

		$category->get((int)$idcategory);

		$category->setData($_POST);

		$category->save();

		header("Location: /admin/categories");
		exit;

	});

	$app->run();

 ?>