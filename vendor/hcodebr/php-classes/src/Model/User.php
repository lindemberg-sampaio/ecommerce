<?php 

	namespace Hcode\Model;

	use \Hcode\DB\Sql;
	use \Hcode\Model;
	use \Hcode\Mailer;

	class User extends Model{

		const SESSION = "User";
		const SECRET = "HcodePhp7_Secret"; // tem que ter ao menos 16 caracteres

		public static function login($login, $password)
		{
			$sql = new Sql();

			$results = $sql->select("SELECT * FROM tb_users WHERE deslogin = :LOGIN", array(
				":LOGIN"=>$login
			));

			if (count($results) === 0)
			{
				throw new \Exception("Usuário inexistente ou senha inválida.");
				
			}

			$data = $results[0];

			if (password_verify($password, $data["despassword"]) === true)
			{
				$user = new User();

				$user->setData($data);

				$_SESSION[User::SESSION] = $user->getValues();

				return $user;


			} else {
				throw new \Exception("Usuário inexistente ou senha inválida.");
			}
		}

		public static function verifyLogin($inadmin = true) // aula 104 - 30min
		{
			if ( // etapas a serem verificadas

				!isset($_SESSION[User::SESSION])
				// se não foi definida esta session com a constante session, ou se ela não existir
				||
				!$_SESSION[User::SESSION]
				// se foi definida, mas está vazia ou perdeu o valor
				||
				!(int)$_SESSION[User::SESSION]["iduser"] > 0
				// se o id do usuário NÃO for maior do que 0 (se for >0, realmente é um usuário)
				||
				(bool)$_SESSION[User::SESSION]["inadmin"] !== $inadmin
				// para verificar se o usuário é um usuário da administração
			)
			{
				header("Location: /admin/login"); // se não foi definida, redireciona para a tela de autenticação
				exit;
			}
		}

		public static function logout()
		{
			$_SESSION[User::SESSION] = NULL;
		}

		public static function listAll() {

			$sql = new Sql();

			return $sql->select("SELECT * 
									FROM tb_users a 
									INNER JOIN tb_persons b 
									USING(idperson) 
									ORDER BY b.desperson");
		}

		public function save() {

			$sql = new Sql();

			$results = $sql->SELECT("CALL sp_users_save(:desperson, :deslogin, :despassword, :desemail, :nrphone, :inadmin)", array(

				":desperson"=>$this->getdesperson(),
				":deslogin"=>$this->getdeslogin(),
				":despassword"=>$this->getdespassword(),
				":desemail"=>$this->getdesemail(),
				":nrphone"=>$this->getnrphone(),
				":inadmin"=>$this->getinadmin()

			));

			$this->setData($results[0]);

		}

		public function get($iduser) {

			$sql = new Sql();

			$results = $sql->select("SELECT * FROM tb_users a INNER JOIN tb_persons b USING(idperson) WHERE a.iduser = :iduser", array(

				":iduser"=>$iduser
			));

			$this->setData($results[0]);

		}

		public function update() {

			$sql = new Sql();

			$results = $sql->SELECT("CALL sp_usersupdate_save(:iduser, :desperson, :deslogin, :despassword, :desemail, :nrphone, :inadmin)", array(

				":iduser"=>$this->getiduser(),
				":desperson"=>$this->getdesperson(),
				":deslogin"=>$this->getdeslogin(),
				":despassword"=>$this->getdespassword(),
				":desemail"=>$this->getdesemail(),
				":nrphone"=>$this->getnrphone(),
				":inadmin"=>$this->getinadmin()

			));


		}

		public function delete() {

			$sql = new Sql();

			$sql->query("CALL sp_users_delete(:iduser)", array(
				":iduser"=>$this->getiduser()
			));
		}

		public static function getForgot($email) {

			$sql = new Sql();

			$results = $sql->select("SELECT *
									FROM tb_persons a
									INNER JOIN tb_users b USING(idperson)
									WHERE a.desemail = :email", array(
										"email"=>$email
									));;

			if (count($results) === 0)
			{

				throw new \Exception("Não foi possível recuperar a senha.");
				
			}
			else
			{
				$data = $results[0];

				$results2 = $sql->select("CALL sp_userspasswordsrecoveries_create(:iduser, :desip)", array(
										":iduser"=>$data["iduser"],
										":desip"=>$_SERVER["REMOTE_ADDR"]
				));

				if (count($results2) === 0)
				{
					throw new \Exception("Não foi possível recuperar a senha");
					
				}
				else
				{
					$dataRecovery = $results2[0];
					
					$code = base64_encode(openssl_encrypt($dataRecovery["idrecovery"], 'AES-128-CBC', User::SESSION, 0, User::SECRET));

					$link = "http://www.hcodecommerce.com.br/admin/forgot/reset?code=$code";

					$emailer = new Mailer($data["desemail"], $data["desperson"], "Redefinir senha da Hcode Store", "forgot"
						, array(
							"name"=>$data["desperson"],
							"link"=>$link
					));

					$emailer->send();

					return $data;


				}
			}


		}

		public static function validForgotDecrypt($code) {

			$idrecovery = openssl_decrypt(base64_decode($code), 'AES-128-CBC', User::SESSION, 0, User::SECRET);

			$sql = new Sql();

			$results = $sql->select("
									SELECT *
									FROM tb_userspasswordsrecoveries a
									INNER JOIN tb_users b USING(iduser)
									INNER JOIN tb_persons c USING(idperson)
									WHERE
										a.idrecovery = :idrecovery 
										AND 
										a.dtrecovery IS NULL 
										AND 
										DATE_ADD(a.dtregister, INTERVAL 1 HOUR) >= NOW()
										",
									array(
										":idrecovery"=>$idrecovery
			));

			if ($results[0] === 0) {

				//throw new \Exception("Não foi possível recuperar a senha.");
				throw new \Exception("Não achei o bendito usuário, não!");
				
			}
			else {

				return $results[0];
			}


		}

		public static function setForgotUsed($idrecovery) {

			$sql = new Sql();

			$sql->query("UPDATE tb_userspasswordsrecoveries SET dtrecovery = NOW() WHERE idrecovery = :idrecovery", array(
				":idrecovery"=>$idrecovery
			));

		}

		public function setPassword($password) {

			$sql = new Sql();

			$sql->query("UPDATE tb_users SET despassword = :password WHERE iduser = :iduser", array(
				":password"=>$password,
				":iduser"=>$this->getiduser()
			));

		}

	}


?>