<?php
	
	use \Hcode\PageAdmin;
	use \Hcode\Model\User;


	$app->get("/admin/users", function(){

		User::verifyLogin(); // verifica se o usuário está autenticado, como não está sendo passado nenhum parâmetro, o inadmin por padrão é TRUE e vai verificar se ele é um usuário LOGADO e se TEM ACESSO ao ADMINISTRATIVO

		$users = User::listAll();

		$page = new PageAdmin();

		$page->setTpl("users", array(
			"users"=>$users
		));

	});


	$app->get("/admin/users/create", function() {

		User::verifyLogin();

		$page = new PageAdmin();

		$page->setTpl("users-create"); // esse formulário irá enviar um POST para uma outra rota que irá salvar os dados no BD. No caso ele irá enviar para a mesma rota utilizada nesta function: "/admin/users/create", porém com POST

	});


	$app->get("/admin/users/:iduser/delete", function($iduser) {

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

?>