<?php
namespace Dao;

class DaoUsers extends Model {

    public function createUsers($name, $email, $hash) {
        $sql = "INSERT INTO users (name, email, pass) VALUES (:name, :email, :pass)";
			$sql = $this->db->prepare($sql);
			$sql->bindValue(':name', $name);
			$sql->bindValue(':email', $email);
			$sql->bindValue(':pass', $hash);
			$sql->execute();
    }

}