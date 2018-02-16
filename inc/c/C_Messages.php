<?php 
//
// Конттроллер сообщений.
//

class C_Messages extends C_Base 
{
	public function __construct() 
	{
		parent::__construct();
	}

	protected function before() 
	{
		parent::before();
	}	

	public function action_index() 
	{
		$this->action_page();	
	}
	
	public function action_page() 
	{
		$page_num = isset($this->params[2]) ? (int)$this->params[2] : 1;

		if (!$page_num)
			$this->p404();

		$mPagination = new M_Pagination(TABLE_PREFIX . 'messages', '/page/');

		$messages = $mPagination->fields(TABLE_PREFIX  . 'messages.*')
				->order_by('id DESC')							
				->on_page(5)->page_num($page_num)->page();	

		if(!$messages)
			$this->p404();							
	
		$this->keywords = 'гости, книга';
		$this->description = 'гостевая книга';
		
		// генерация пагинации
		$navbar = $this->template('inc/v/v_navbar.php', ['navparams' => $mPagination->navparams()]);

		// генерация контента страницы
		$this->content = $this->template("inc/v/messages/v_index.php", 
		[
			'messages' => $messages,
			'navbar' => $navbar,
			'navparams' => $mPagination->navparams()
		]);			
	}

	public function action_add() 
	{
		if (count($this->params) > 1)
			$this->p404();

		$mMessages = M_Messages::Instance();
		$fields = [];
		$errors = [];
		
		if ($this->IsPost()) {

			if ($mMessages->add(array_merge($_POST, $_FILES))) 
				$this->Redirect('/');

			$errors = $mMessages->errors();
			$fields = $_POST;	
		}	
		
		$this->scripts[] = '../libs/ckeditor/ck_init';			

		$this->title = "Гостевая книга | Отправить сообщение";
		$this->content = $this->template('inc/v/messages/v_add.php', 
		[	
		    'errors' => $errors, 
		    'fields' => $fields
		]);		
	}

	public function action_get() 
	{
		$id_message = (isset($this->params[2])) ? (int)$this->params[2] : null;

		// если id не введен или params > 3 - кидаем 404 ошибку
		if (!$id_message || count($this->params)>3)
			$this->p404();
		
		$mMessages = M_Messages::Instance();

		$message = $mMessages->get($id_message);

		if (!$message)
			$this->p404();

		if (isset($message['file_name'])) {
			$tmp = explode('.', $message['file_name']);
			$ext = strtolower($tmp[count($tmp) - 1]);
			$type = $mMessages->getFileType($ext);

			if ($type == 'img'){
				$message['img'] = $message['file_name'];
			}
			else {
				$message['txt'] = $mMessages->getTxtUser($message['file_name']);			
			}
		} else {
			$this->p404();
		}
		
		$this->scripts[] = '../libs/particles.js/particles_init';
		$this->title = 'Просмотр сообщения';
		$this->content = $this->template('inc/v/messages/v_message.php', [
			'message' => $message
		]); 		
	}						

	// Ajax превью перед сохранением формы
	public function action_preview()
	{
		$mMessages = M_Messages::Instance();
		$message = [];
		// если идет ajax запрос
		if( $this->IsAjax() ) {

			// если был вложен файл
			if (isset($_FILES['file']) && $_FILES['file']['size'] > 0) {
				$file = $_FILES['file'];
				
				// получаем массив с типом и именем файла
				$file_name = $mMessages->uploadPreview($file);

				if ($file_name['type'] == 'img')
					$message['img'] = $file_name['name'];
				else
					$message['txt'] = $mMessages->getTxtPrev($file_name['name']);				
			}

		
		if (isset($_POST['url'])) 
			$message['url'] = $_POST['url'];	 

		$message['name'] = $_POST['name'];
		$message['email'] = $_POST['email'];		
		echo $this->content = $this->template('inc/v/messages/v_preview.php', ['message' => $message]);
		die;
		}
		
		$this->p404();						
	}

	// Проверка капчи с помощью ajax
	public function action_captcha() 
	{
		if($this->IsAjax()) {
			if (strtoupper($_POST['captcha']) == strtoupper($_SESSION['keycaptcha'])) {
				header('Content-type: application/json');
				echo json_encode(array('type' => 'good')); 
				die;
			} else {
			    header('Content-type: application/json');           
			    echo json_encode(array('type' => 'bad')); 
			    die;  
			}
		}

		$this->p404();		
	}

	public function action_ajax_add()
	{
		if ($this->IsAjax()) {

			$mMessages = M_Messages::Instance();
			$data = [];

			// если передаётся файл
			if (!empty($_FILES)) 
				$data = array_merge($_POST, $_FILES);
			else
				$data = $_POST;

			if ($mMessages->add($data)) {
				header('Content-type: application/json');						
				echo json_encode(['type' => 'good']);
				die;					
			} else {
				header('Content-type: application/json');						
				echo json_encode(['type' => 'bad']);
				die;					
			}	
		}

		$this->p404();		
	}

	public function action_404()
	{
		$this->title = '404 - Not Found';
		$this->content = $this->template('inc/v/v_404.php');
	}		
}