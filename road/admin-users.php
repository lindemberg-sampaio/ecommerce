<?php
	
	use \Hcode\PageAdmin;
	use \Hcode\Model\User;


	$app->get("/admin/users/:iduser/password", function($iduser){

		User::verifyLogin();

		$user = new User();

		$user->get((int)$iduser);

		$page = new PageAdmin();

		$page->setTpl("users-password", [
			"user"=>$user->getValues(),
			"msgError"=>User::getError(),
			"msgSuccess"=>User::getSuccess()
		]);

	});


	$app->post("/admin/users/:iduser/password", function($iduser){

		User::verifyLogin();

		$user = new User();

		$user->get((int)$iduser);

		if(!isset($_POST['despassword']) || $_POST['despassword']===''){
			User::setError("Preencha a nova senha.");
			header("Location: /admin/users/$iduser/password");
			exit;
		}

		if(!isset($_POST['despassword-confirm']) || $_POST['despassword-confirm']===''){
			User::setError("Preencha a confirmação da nova senha.");
			header("Location: /admin/users/$iduser/password");
			exit;
		}

		if($_POST['despassword'] !== $_POST['despassword-confirm']){
			User::setError("Confirme corretamente as senhas.");
			header("Location: /admin/users/$iduser/password");
			exit;
		}

		$user = new User();

		$user->get((int)$iduser);

		$user->setPassword(User::getPasswordHash($_POST['despassword']));

		User::setSuccess("Senha alterada com sucesso!");

		header("Location: /admin/users/$iduser/password");
		exit;

	});



	$app->get("/admin/users", function(){

		User::verifyLogin(); // verifica se o usuário está autenticado, como não está sendo passado nenhum parâmetro, o inadmin por padrão é TRUE e vai verificar se ele é um usuário LOGADO e se TEM ACESSO ao ADMINISTRATIVO

		$search = (isset($_GET['search'])) ? $_GET['search'] : "";
		$page = (isset($_GET['page'])) ? (int)$_GET['page'] : 1;


		if ($search != '') {

			$pagination = User::getPageSearch($search, $page); // se for dado ao usuário definir quantos itens por página, a sentença será $users = User::getPage($page, $quantidadePorPaginaDefinidaPeloUsuario), porém deve tirar do método

		} else {

			$pagination = User::getPage($page);

		}

		$pages = [];

		for ($x = 0; $x < $pagination['pages']; $x++)
		{

			array_push($pages, [
				'href'=>'/admin/users?'.http_build_query([
					'page'=>$x+1,
					'search'=>$search
				]),
				'text'=>$x+1
			]);

		}

		$page = new PageAdmin();

		$page->setTpl("users", array(
			"users"=>$pagination['data'],
			"search"=>$search,
			"pages"=>$pages
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