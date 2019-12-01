<?php
namespace Dao;

class DaoUsers extends DaoConection {

    public function createUsers($name, $email, $hash) {
        $sql = "INSERT INTO users (name, email, pass) VALUES (:name, :email, :pass)";
			$sql = $this->db->prepare($sql);
			$sql->bindValue(':name', $name);
			$sql->bindValue(':email', $email);
			$sql->bindValue(':pass', $hash);
			$sql->execute();
	}
	
	public function verifyEmail($email) {
		
		$sql = "SELECT id FROM users WHERE email = :email";		
		$sql = $this->db->prepare($sql);
		$sql->bindValue(':email', $email);
		$sql->execute();
		
		if($sql->rowCount() > 0) {
			
			return true;
		} else {
			return false;
			
		}
	}

	public function lastId() {
		return $this->db->lastInsertId();
	}

	public function checkCredentials($email, $pass) {

		$sql = "SELECT id, pass FROM users WHERE email = :email";
		$sql = $this->db->prepare($sql);
		$sql->bindValue(':email', $email);
		$sql->execute();

		if($sql->rowCount() > 0) {
			$info = $sql->fetch();

			if(password_verify($pass, $info['pass'])) {
				$this->id_user = $info['id'];

				return true;
			} else {
				return false;
			}
		} else {
			return false;
		}

	}

	public function getInfo($id) {
		$array = array();

		$sql = "SELECT id, name, email, avatar FROM users WHERE id = :id";
		$sql = $this->db->prepare($sql);
		$sql->bindValue(':id', $id);
		$sql->execute();

		return $sql;
	}

	public function getFollowingCount($id_user) {
		
		$sql = "SELECT COUNT(*) as c FROM users_following WHERE id_user_active = :id";
		$sql = $this->db->prepare($sql);
		$sql->bindValue(':id', $id_user);
		$sql->execute();
		$info = $sql->fetch();
		//$info['c'] = $id_user;
		return $info;
	}

	public function getFollowersCount($id_user) {
		$sql = "SELECT COUNT(*) as c FROM users_following WHERE id_user_passive = :id";
		$sql = $this->db->prepare($sql);
		$sql->bindValue(':id', $id_user);
		$sql->execute();
		$info = $sql->fetch();

		return $info;
	}

	public function getFollowing($id_user) {
		$array = array();

		$sql = "SELECT id_user_passive FROM users_following WHERE id_user_active = :id";
		$sql = $this->db->prepare($sql);
		$sql->bindValue(':id', $id_user);
		$sql->execute();

		if($sql->rowCount() > 0) {
			return $sql->fetchAll();

			// foreach($data as $item) {
			// 	$array[] = intval( $item['id_user_passive'] );
			// }
		}

		return '';
	}

	public function editInfo($id, $toChange,  $fields) {
		
		$sql = "UPDATE users SET ".implode(',', $fields)." WHERE id = :id";
		$sql = $this->db->prepare($sql);
		$sql->bindValue(':id', $id);

		foreach($toChange as $key => $value) {
			$sql->bindValue(':'.$key, $value);
		}

		$sql->execute();
		return '';
	}

	public function delete($id) {		

			$sql = "DELETE FROM users_following WHERE id_user_active = :id OR id_user_passive = :id";
			$sql = $this->db->prepare($sql);
			$sql->bindValue(':id', $id);
			$sql->execute();

			$sql = "DELETE FROM users WHERE id = :id";
			$sql = $this->db->prepare($sql);
			$sql->bindValue(':id', $id);
			$sql->execute();

			return '';


	}

	public function follow($id_user) {

		$sql = "SELECT * FROM users_following WHERE id_user_active = :id_user_active AND id_user_passive = :id_user_passive";
		$sql = $this->db->prepare($sql);
		$sql->bindValue(':id_user_active', $this->getId());
		$sql->bindValue(':id_user_passive', $id_user);
		$sql->execute();

		if($sql->rowCount() === 0) {

			$sql = "INSERT INTO users_following (id_user_active, id_user_passive) VALUES (:id_user_active, :id_user_passive)";
			$sql = $this->db->prepare($sql);
			$sql->bindValue(':id_user_active', $this->getId());
			$sql->bindValue(':id_user_passive', $id_user);
			$sql->execute();

			return true;
		} else {
			return false;
		}

	}

	public function unfollow($id_user) {

		$sql = "DELETE FROM users_following WHERE id_user_active = :id_user_active AND id_user_passive = :id_user_passive";
		$sql = $this->db->prepare($sql);
		$sql->bindValue(':id_user_active', $this->getId());
		$sql->bindValue(':id_user_passive', $id_user);
		$sql->execute();

	}

			

	
}