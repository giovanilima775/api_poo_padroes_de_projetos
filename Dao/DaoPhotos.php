<?php
namespace Dao;

class DaoPhotos extends DaoConection {


    public function getRandomPhotos($per_page, $excludes = array()) {
		if(count($excludes) > 0) {
			$sql = "SELECT * FROM photos WHERE id NOT IN (".implode(',', $excludes).") ORDER by RAND() LIMIT ".$per_page;
		} else {
			  $sql = "SELECT * FROM photos ORDER by RAND() LIMIT ".$per_page;
        }	
        return $sql = $this->db->query($sql);
    }

    public function getPhoto($id_photo) {      
        

		$sql = "SELECT * FROM photos WHERE id = :id";
		$sql = $this->db->prepare($sql);
		$sql->bindValue(':id', $id_photo);
		$sql->execute();

		if($sql->rowCount() > 0) {

			return $sql->fetch(\PDO::FETCH_ASSOC);
		}

		return '';
    }
    
    
    public function getFeedCollection($ids, $offset, $per_page) {

        $sql = "SELECT * FROM photos
        WHERE id_user IN (".implode(',', $ids).")
        ORDER by id DESC
        LIMIT ".$offset.", ".$per_page;

        $sql = $this->db->query($sql);

        if($sql->rowCount() > 0) {
            return $sql->fetchAll(\PDO::FETCH_ASSOC);
        }

		return '';
    }
    
    public function getPhotosFromUser($id_user, $offset, $per_page) {
		

		$sql = "SELECT * FROM photos WHERE id_user = :id ORDER BY id DESC LIMIT ".$offset.",".$per_page;
		$sql = $this->db->prepare($sql);
		$sql->bindValue(':id', $id_user);
		$sql->execute();

		if($sql->rowCount() > 0) {
			return $sql->fetchAll(\PDO::FETCH_ASSOC);
			
		}

		return '';
	}

    public function getPhotosCount($id_user) {		
		$sql = "SELECT COUNT(*) as c FROM photos WHERE id_user = :id";
		$sql = $this->db->prepare($sql);
		$sql->bindValue(':id', $id_user);
		$sql->execute();
		$info = $sql->fetch();
		
		return $info;
    }

    public function getLikeCount($id_photo) {
		$sql = "SELECT COUNT(*) as c FROM photos_likes WHERE id_photo = :id";
		$sql = $this->db->prepare($sql);
		$sql->bindValue(':id', $id_photo);
		$sql->execute();
		$info = $sql->fetch();

		return $info;
    }
    
    public function getComments($id_photo) {		

		$sql = "SELECT photos_comments.*, users.name FROM photos_comments LEFT JOIN users ON users.id = photos_comments.id_user WHERE photos_comments.id_photo = :id";
		$sql = $this->db->prepare($sql);
		$sql->bindValue(':id', $id_photo);
        $sql->execute();        
        
		if($sql->rowCount() > 0) {
			return $sql->fetchAll(\PDO::FETCH_ASSOC);
		}

    }
    
    public function deletePhoto($id_photo, $id_user) {
		$sql = "SELECT id FROM photos WHERE id = :id AND id_user = :id_user";
		$sql = $this->db->prepare($sql);
		$sql->bindValue(':id', $id_photo);
		$sql->bindValue(':id_user', $id_user);
		$sql->execute();

		if($sql->rowCount() > 0) {

			$sql = "DELETE FROM photos WHERE id = :id";
			$sql = $this->db->prepare($sql);
			$sql->bindValue(':id', $id_photo);
			$sql->execute();

			$sql = "DELETE FROM photos_comments WHERE id_photo = :id_photo";
			$sql = $this->db->prepare($sql);
			$sql->bindValue(':id_photo', $id_photo);
			$sql->execute();

			$sql = "DELETE FROM photos_likes WHERE id_photo = :id_photo";
			$sql = $this->db->prepare($sql);
			$sql->bindValue(':id_photo', $id_photo);
			$sql->execute();

			return true;

		} else {
			return false;
		}
    }
    
    public function deleteAll($id_user) {
		$sql = "DELETE FROM photos WHERE id_user = :id_user";
		$sql = $this->db->prepare($sql);
		$sql->bindValue(':id_user', $id_user);
		$sql->execute();

		$sql = "DELETE FROM photos_comments WHERE id_user = :id_user";
		$sql = $this->db->prepare($sql);
		$sql->bindValue(':id_user', $id_user);
		$sql->execute();

		$sql = "DELETE FROM photos_likes WHERE id_user = :id_user";
		$sql = $this->db->prepare($sql);
		$sql->bindValue(':id_user', $id_user);
		$sql->execute();
    }
    
    public function addComment($id_photo, $id_user, $txt) {

		if(!empty($txt)) {
			$sql = "INSERT INTO photos_comments (id_user, id_photo, date_comment, txt) VALUES (:id_user, :id_photo, NOW(), :txt)";
			$sql = $this->db->prepare($sql);
			$sql->bindValue(':id_user', $id_user);
			$sql->bindValue(':id_photo', $id_photo);
			$sql->bindValue(':txt', $txt);
			$sql->execute();

			return true;
		} else {
			return false;
		}

    }
    
    public function deleteComment($id_comment, $id_user) {
		$sql = "SELECT id FROM photos_comments WHERE id_user = :id_user AND id = :id";
		$sql = $this->db->prepare($sql);
		$sql->bindValue(':id_user', $id_user);
		$sql->bindValue(':id', $id_comment);
		$sql->execute();

		if($sql->rowCount() > 0) {

			$sql = "DELETE FROM photos_comments WHERE id = :id";
			$sql = $this->db->prepare($sql);
			$sql->bindValue(':id', $id_comment);
			$sql->execute();

			return true;

		} else {
			return false;
		}
    }
    
    public function like($id_photo, $id_user) {
		$sql = "SELECT * FROM photos_likes WHERE id_user = :id_user AND id_photo = :id_photo";
		$sql = $this->db->prepare($sql);
		$sql->bindValue(':id_user', $id_user);
		$sql->bindValue(':id_photo', $id_photo);
		$sql->execute();

		if($sql->rowCount() == 0) {

			$sql = "INSERT INTO photos_likes (id_user, id_photo) VALUES (:id_user, :id_photo)";
			$sql = $this->db->prepare($sql);
			$sql->bindValue(':id_user', $id_user);
			$sql->bindValue(':id_photo', $id_photo);
			$sql->execute();

			return true;

		} else {
			return false;
		}
    }
    
    public function unlike($id_photo, $id_user) {

		$sql = "DELETE FROM photos_likes WHERE id_user = :id_user AND id_photo = :id_photo";
		$sql = $this->db->prepare($sql);
		$sql->bindValue(':id_user', $id_user);
		$sql->bindValue(':id_photo', $id_photo);
		$sql->execute();

		
	}
    
    
}
