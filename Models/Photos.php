<?php
namespace Models;

use \Core\Model;
use \Dao\DaoPhotos;

class Photos extends Model {

	public function getRandomPhotos($per_page, $excludes = array()) {
		$array = array();

		foreach($excludes as $k => $item) {
			$excludes[$k] = intval($item);
		}

		$DaoPhotos = new DaoPhotos();

		$sql = $DaoPhotos->getRandomPhotos($per_page, $excludes = array());		

		if($sql->rowCount() > 0) {
			$array = $sql->fetchAll(\PDO::FETCH_ASSOC);

			foreach($array as $k => $item) {
				$array[$k]['url'] = BASE_URL.'media/photos/'.$item['url'];

				$array[$k]['like_count'] = $this->getLikeCount($item['id']);
				$array[$k]['comments'] = $this->getComments($item['id']);
			}
		}

		return $array;
	}

	public function getPhotosFromUser($id_user, $offset, $per_page) {
		$array = array();

		$DaoPhotos = new DaoPhotos();
		$array = $DaoPhotos->getPhotosFromUser($id_user, $offset, $per_page);
		if(!empty($array)) {

			foreach($array as $k => $item) {
				$array[$k]['url'] = BASE_URL.'media/photos/'.$item['url'];

				$array[$k]['like_count'] = $this->getLikeCount($item['id']);
				$array[$k]['comments'] = $this->getComments($item['id']);
			}
		}

		return $array;
	}

	public function getFeedCollection($ids, $offset, $per_page) {
		$array = array();
		$users = new Users();

		
		if(count($ids) > 0) {
			$DaoPhotos = new DaoPhotos();
			$array = $DaoPhotos->getFeedCollection($ids, $offset, $per_page);

			if(!empty($array)) {

				foreach($array as $k => $item) {
					$user_info = $users->getInfo($item['id_user']);

					$array[$k]['name'] = $user_info['name'];
					$array[$k]['avatar'] = $user_info['avatar'];
					$array[$k]['url'] = BASE_URL.'media/photos/'.$item['url'];

					$array[$k]['like_count'] = $this->getLikeCount($item['id']);
					$array[$k]['comments'] = $this->getComments($item['id']);


				}

			}

		}

		return $array;
	}

	public function getPhoto($id_photo) {
		$array = array();

		$users = new Users();
		
		$DaoPhotos = new DaoPhotos();
		$array = $DaoPhotos->getPhoto($id_photo);

		if(!empty($array)) {

			$user_info = $users->getInfo($array['id_user']);

			$array['name'] = $user_info['name'];
			$array['avatar'] = $user_info['avatar'];
			$array['url'] = BASE_URL.'media/photos/'.$array['url'];

			$array['like_count'] = $this->getLikeCount($array['id']);
			$array['comments'] = $this->getComments($array['id']);

		}

		return $array;
	}

	public function getComments($id_photo) {
		$array = array();

		$DaoPhotos = new DaoPhotos();
		$array = $DaoPhotos->getComments($id_photo);

		return $array;
	}

	public function getLikeCount($id_photo) {

		$DaoPhotos = new DaoPhotos();
		$info['c'] = $DaoPhotos->getLikeCount($id_photo);

		return $info['c'];
	}

	public function getPhotosCount($id_user) {
		$DaoPhotos = new DaoPhotos();

		$info['c'] = $DaoPhotos->getPhotosCount($id_user);
			
		return $info['c'];
	}

	public function deletePhoto($id_photo, $id_user) {
		$DaoPhotos = new DaoPhotos();
		
		$return = $DaoPhotos->deletePhoto($id_photo, $id_user);

		if($return == true) {
			return '';

		} else {
			return 'Esta foto não é sua.';
		}
	}

	public function deleteAll($id_user) {
		$DaoPhotos = new DaoPhotos();
		
		$DaoPhotos-> deleteAll($id_user);
	}

	public function addComment($id_photo, $id_user, $txt) {
		$DaoPhotos = new DaoPhotos();
		
		$return = $DaoPhotos->addComment($id_photo, $id_user, $txt);

		if($return == true) {	

			return '';
		} else {
			return 'Comentário vazio';
		}

	}

	public function deleteComment($id_comment, $id_user) {
		$DaoPhotos = new DaoPhotos();
		
		$return = $DaoPhotos->deleteComment($id_comment, $id_user);

		if($return == true) {
			return '';

		} else {
			return 'Este comentário não é seu.';
		}
	}

	public function like($id_photo, $id_user) {
		$DaoPhotos = new DaoPhotos();
		
		$return = $DaoPhotos->like($id_photo, $id_user);

		if($return == true) {
			return '';

		} else {
			return 'Você já deu like nesta foto.';
		}
	}

	public function unlike($id_photo, $id_user) {
		$DaoPhotos = new DaoPhotos();
		
		$DaoPhotos->unlike($id_photo, $id_user);

		return '';
	}




















}