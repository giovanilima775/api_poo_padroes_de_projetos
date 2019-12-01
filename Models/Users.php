<?php
namespace Models;

use \Core\Model;
use \Dao\DaoUsers;
use \Models\Jwt;
use \Models\Photos;

class Users extends Model {

	private $id_user;

	public function create($name, $email, $pass) {

		if(!$this->emailExists($email)) {
			$hash = password_hash($pass, PASSWORD_DEFAULT);
			$daoUser = new DaoUsers();
			$daoUser->createUsers($name, $email, $pass);

			$this->id_user = $daoUser->lastId();

			return true;
		} else {
			return false;
		}
	}

	public function checkCredentials($email, $pass) {

		$daoUser = new DaoUsers();
		return $daoUser->checkCredentials($email, $pass);
		// $sql = "SELECT id, pass FROM users WHERE email = :email";
		// $sql = $this->db->prepare($sql);
		// $sql->bindValue(':email', $email);
		// $sql->execute();

		// if($sql->rowCount() > 0) {
		// 	$info = $sql->fetch();

		// 	if(password_verify($pass, $info['pass'])) {
		// 		$this->id_user = $info['id'];

		// 		return true;
		// 	} else {
		// 		return false;
		// 	}
		// } else {
		// 	return false;
		// }

	}

	public function getId() {
		return $this->id_user;
	}

	public function getInfo($id) {
		$array = array();

		$daoUser = new DaoUsers();

		$sql = $daoUser->getInfo($id);

		if($sql->rowCount() > 0) {
			$array = $sql->fetch(\PDO::FETCH_ASSOC);

			$photos = new Photos();

			if(!empty($array['avatar'])) {
				$array['avatar'] = BASE_URL.'media/avatar/'.$array['avatar'];
			} else {
				$array['avatar'] = BASE_URL.'media/avatar/default.jpg';
			}

			$array['following'] = $this->getFollowingCount($id);
			$array['followers'] = $this->getFollowersCount($id);
			$array['photos_count'] = $photos->getPhotosCount($id);
		}

		return $array;
	}

	public function getFeed($offset = 0, $per_page = 10) {
		/*
		Passo 1: Pegar os seguidores
		Passo 2: Fazer uma lista das ultimas fotos desses seguidores
		*/
		$followingUsers = $this->getFollowing($this->getId());

		$p = new Photos();
		return $p->getFeedCollection($followingUsers, $offset, $per_page);
	}

	public function getFollowing($id_user) {
		$array = array();

		$daoUser =  new DaoUsers();
		$data =  $daoUser->getFollowing($id_user);
		if(!empty($data)) {
			
			foreach($data as $item) {
				$array[] = intval( $item['id_user_passive'] );
			}
		}

		return $array;
	}

	public function getFollowingCount($id_user) {

		$daoUser = new DaoUsers();

		$info['c'] = $daoUser->getFollowingCount($id_user);
		
		return $info['c'];
		
	}

	public function getFollowersCount($id_user) {
	
		$daoUser = new DaoUsers();

		$info['c'] = $daoUser->getFollowersCount($id_user);

		return $info['c'];
	}

	public function createJwt() {
		$jwt = new Jwt();
		return $jwt->create(array('id_user' => $this->id_user));
	}

	public function validateJwt($token) {
		$jwt = new Jwt();
		$info = $jwt->validate($token);

		if(isset($info->id_user)) {
			$this->id_user = $info->id_user;
			return true;
		} else {
			return false;
		}
	}

	private function emailExists($email) {

		$daoUsers = new DaoUsers();
		$return = $daoUsers->verifyEmail($email);		
		
		return $return;
		
	}

	public function editInfo($id, $data) {

		if($id === $this->getId()) {

			$toChange = array();

			if(!empty($data['name'])) {
				$toChange['name'] = $data['name'];
			}
			if(!empty($data['email'])) {
				if(filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
					if(!$this->emailExists($data['email'])) {
						$toChange['email'] = $data['email'];
					} else {
						return 'E-mail já existente!';
					}
				} else {
					return 'E-mail inválido';
				}
			}
			if(!empty($data['pass'])) {
				$toChange['pass'] = password_hash($data['pass'], PASSWORD_DEFAULT);
			}

			if(count($toChange) > 0) {

				$fields = array();
				foreach($toChange as $key => $value) {
					$fields[] = $key.' = :'.$key;
				}

				$daoUsers = new DaoUsers();
				$daoUsers->editInfo($id, $data, $toChange,  $fields);

				return '';

			} else {
				return 'Preencha os dados corretamente!';
			}


		} else {
			return 'Não é permitido editar outro usuário';
		}

	}

	public function delete($id) {

		if($id === $this->getId()) {

			$p = new Photos();
			$p->deleteAll($id);

			$daoUsers = new DaoUsers();
			$daoUsers->delete($id);

			return '';

		} else {
			return 'Não é permitido excluir outro usuário';
		}

	}

	public function follow($id_user) {

		$daoUsers = new DaoUsers();
		return $daoUsers->follow($id_user);

	}

	public function unfollow($id_user) {

		$daoUsers = new DaoUsers();
		$daoUsers->unfollow($id_user);

	}

}


















