<?php
namespace Controllers;

use \Core\Controller;
use \Models\Users;
use \Models\Photos;

class PhotosController extends Controller {

	public function index() {}

	public function random() {

		$array = array('error'=>'', 'logged'=>false);

		$method = $this->getMethod();
		$data = $this->getRequestData();

		$users = new Users();
		$p = new Photos();

		if(!empty($data['jwt']) && $users->validateJwt($data['jwt'])) {
			$array['logged'] = true;

			if($method == 'GET') {

				$per_page = 10;
				if(!empty($data['per_page'])) {
					$per_page = intval( $data['per_page'] );
				}

				$excludes = array();
				if(!empty($data['excludes'])) {
					$excludes = explode(',', $data['excludes']);
				}

				$array['data'] = $p->getRandomPhotos($per_page, $excludes);

			} else {
				$array['error'] = 'Método '.$method.' não disponível';
			}

		} else {
			$array['error'] = 'Acesso negado';
		}

		$this->returnJson($array);

	}

	public function view($id_photo) {
		$array = array('error'=>'', 'logged'=>false);

		$method = $this->getMethod();
		$data = $this->getRequestData();

		$users = new Users();
		$p = new Photos();

		if(!empty($data['jwt']) && $users->validateJwt($data['jwt'])) {
			$array['logged'] = true;

			switch($method) {
				case 'GET':
					$array['data'] = $p->getPhoto($id_photo);
					break;
				case 'DELETE':
					$array['error'] = $p->deletePhoto($id_photo, $users->getId());
					break;
				default:
					$array['error'] = 'Método '.$method.' não disponível';
					break;
			}

		} else {
			$array['error'] = 'Acesso negado';
		}

		$this->returnJson($array);
	}

	public function comment($id_photo) {
		$array = array('error'=>'', 'logged'=>false);

		$method = $this->getMethod();
		$data = $this->getRequestData();

		$users = new Users();
		$p = new Photos();

		if(!empty($data['jwt']) && $users->validateJwt($data['jwt'])) {
			$array['logged'] = true;

			switch($method) {
				case 'POST':
					if(!empty($data['txt'])) {
						$array['error'] = $p->addComment($id_photo, $users->getId(), $data['txt']);
					} else {
						$array['error'] = 'Comentário vazio.';
					}
					break;
				default:
					$array['error'] = 'Método '.$method.' não disponível';
					break;
			}

		} else {
			$array['error'] = 'Acesso negado';
		}

		$this->returnJson($array);
	}

	public function delete_comment($id) {
		$array = array('error'=>'', 'logged'=>false);

		$method = $this->getMethod();
		$data = $this->getRequestData();

		$users = new Users();
		$p = new Photos();

		if(!empty($data['jwt']) && $users->validateJwt($data['jwt'])) {
			$array['logged'] = true;

			switch($method) {
				case 'DELETE':
					$array['error'] = $p->deleteComment($id, $users->getId());
					break;
				default:
					$array['error'] = 'Método '.$method.' não disponível';
					break;
			}

		} else {
			$array['error'] = 'Acesso negado';
		}

		$this->returnJson($array);
	}

	public function like($id_photo) {
		$array = array('error'=>'', 'logged'=>false);

		$method = $this->getMethod();
		$data = $this->getRequestData();

		$users = new Users();
		$p = new Photos();

		if(!empty($data['jwt']) && $users->validateJwt($data['jwt'])) {
			$array['logged'] = true;

			switch($method) {
				case 'POST':
					$array['error'] = $p->like($id_photo, $users->getId());
					break;
				case 'DELETE':
					$array['error'] = $p->unlike($id_photo, $users->getId());
					break;
				default:
					$array['error'] = 'Método '.$method.' não disponível';
					break;
			}

		} else {
			$array['error'] = 'Acesso negado';
		}

		$this->returnJson($array);
	}

	public function new_record() {
		$array = array();
		// var_dump($_FILES);
		if(!empty($_FILES)) {
			$photos =  new Photos();
			$users = new Users();

			$temp_name = $_FILES['file']['tmp_name'];
			$img_name = $_FILES['file']['name'];
			$upload_dir =  "./media/prints/".$img_name;

			//PEGA O FORMATO DO ARQUIVO
			$ext = pathinfo($img_name, PATHINFO_EXTENSION);
			$tipos_arquivos = array('jpg', 'JPG', 'png', 'JPEG', 'gif');

			if(in_array($ext, $tipos_arquivos)) {
				if(move_uploaded_file($temp_name, $upload_dir)) {
					$url = BASE_URL."media/prints/".$img_name;
					//VERIFICAR PORQUE GETID NÃO ESTÁ RETORNANDO A ID
					$id = $users->getId();

					//TROCAR UM NOVE POR ALGUMA HASH  PARA QUE NÃO TENHA DUPLICAÇÃO NO NOME DO ARQUIVO
					if($photos->savePhoto(1, $url)) {
						$array['info'] =  'Salvou no banco de dados';
					}

				$array['id'] = $id;

				$array['img'] = 'funcionous';
				$array['url'] = $url;
				} else {
					$array['error'] = 'Formato de aquivo inválido';
				}

			}else {
				$array['error'] = 'Formato do aquivo inválido';
			}


		}else {
			$array['error'] = 'Não há arquivos para fazer upload';
		}
		$this->returnJson($array);
	}

}














