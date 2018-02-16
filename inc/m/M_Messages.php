<?php 
//
// Модель сообщений
//
class M_Messages extends M_Model 
{
	private static $instance;

	// прием синглтон(одиночка)
	// таким методом будет создаваться только 1 объект
	// Позволяет не плодить экземпляры класса, а пользоваться одним
	public static function Instance() 
	{
		if(self::$instance == null) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	public function __construct() 
	{
		parent::__construct(TABLE_PREFIX . "messages", 'id');
	}

	public function get($id_message)
	{
		return $res = parent::get($id_message);
	}

	public function delete($pk)
	{
		return parent::delete($pk);
	}	

	public function add($fields)
	{	
		if (isset($fields['file'])) {
			$file = $fields['file'];

			if ($file['size'] > 0) {
				$valid_file = $this->checkFile($file);

				if (!$valid_file)
					return false;

				$fields['file_name'] = $valid_file;
			}
		}	

		// перегоняем IP в int, для экономии места
		$fields['ip'] = $this->ip2int($_SERVER['REMOTE_ADDR']);

		$fields['browser'] = $_SERVER['HTTP_USER_AGENT'];

		// запись данных в бд
		$id_message = parent::add($fields);

		// если массив с ошибками пуст и файл валидный - сохраняем файл
		if ( count($this->errors) == 0 && isset($fields['file_name']) ) {
		 	// заливаем файл в нужную папку
			$this->сopyFile($valid_file, $file, $id_message);
		}

		return $id_message;
	}

	public function сopyFile($file_name, $file, $id_message)
	{
		// получаем расширение
		$tmp = explode('.', $file_name);
		$ext = strtolower($tmp[count($tmp) - 1]);

		if ($ext == "txt") {
			if (!copy($file['tmp_name'], TXT_DIR . $file_name)){
				// если не скопировалось, удаляем запись из бд
				$this->delete($id_message);
				die("Ошибка копирования txt файла. Проверить каталог.");
			}
		} else {
			if (!copy($file['tmp_name'], IMG_DIR . $file_name)) {
				$this->delete($id_message);
				die("Ошибка копирования img файла. Проверить каталог.");	
			}

			$size = getimagesize(IMG_DIR . $file_name);
			if ($size[0] > IMG_SMALL_WIDTH || $size[1] > IMG_SMALL_HEIGHT) {
				// изменяем размер
				$this->resize(IMG_DIR . $file_name, IMG_DIR . $file_name, IMG_SMALL_WIDTH, IMG_SMALL_HEIGHT);
			}			
		}

		return true;	
	}	

	public function resize($src, $dest, $width, $height, $rgb = 0xFFFFFF, $quality = 100)
    {
      if (!file_exists($src)) return false;

      $size = getimagesize($src);

      if ($size === false) return false;

      // Определяем исходный формат по MIME-информации, предоставленной
      // функцией getimagesize, и выбираем соответствующую формату
      // imagecreatefrom-функцию.
      $format = strtolower(substr($size['mime'], strpos($size['mime'], '/')+1));
      $icfunc = "imagecreatefrom" . $format;
      if (!function_exists($icfunc)) return false;
		
      $x_ratio = $width / $size[0];
	  
	  if($height === null)
			$height = $size[1] * $x_ratio;
	  
      $y_ratio = $height / $size[1];

      $ratio       = min($x_ratio, $y_ratio);
      $use_x_ratio = ($x_ratio == $ratio);

      $new_width   = $use_x_ratio  ? $width  : floor($size[0] * $ratio);
      $new_height  = !$use_x_ratio ? $height : floor($size[1] * $ratio);
      $new_left    = $use_x_ratio  ? 0 : floor(($width - $new_width) / 2);
      $new_top     = !$use_x_ratio ? 0 : floor(($height - $new_height) / 2);

      $isrc = $icfunc($src);
      $idest = imagecreatetruecolor($width, $height);

      imagefill($idest, 0, 0, $rgb);
      imagecopyresampled($idest, $isrc, $new_left, $new_top, 0, 0,
        $new_width, $new_height, $size[0], $size[1]);

      imagejpeg($idest, $dest, $quality);

      imagedestroy($isrc);
      imagedestroy($idest);

      return true;
    }
	
	//
	// Проверка типа и размера файла
	// $file 		- файл 
	// результат	- имя файла или ошибку
	//

	public function checkFile($file)
	{
		$white_list = ['jpg', 'gif', 'png', 'txt'];
		
		$ext = $this->getFileExt($file);

		if (!in_array(strtolower($ext), $white_list)) { //проверка расширения файла
			$this->errors[] = 'Не верный тип файла (допустимые форматы jpg, png, gif, txt).';
			return false;
		} 

		if ($ext == "txt") { //если txt файл

			if (($file['size'] > 100 * 1024)) { //Файл превышает 100 Кб
				$this->errors[] = 'Размер txt файла не должен превышать 100 Кб.';
				return false;				
			}

		} else { //если форматы изображений
			if (($file['size'] > 5 * 1024 * 1024)) {
				$this->errors[] = 'Размер файла изображения не должен превышать 5 Мб.';
				return false;				
			}
		}

		return $file_name = $this->randomStr() . "." . $ext;
	}

	//
	// загрузка файлов для превью
	// $file 		- переданные файл
	// результат	- массив с именем файла и расширением или false
	//	
	public function uploadPreview($file)
	{
		// получаем расширение файла
		$ext = $this->getFileExt($file);
		$name = $this->randomStr(). "." . $ext; //присваиваем новое имя файла, чтобы не было конфликтов

		if (!copy($file['tmp_name'], DIR_PREV . $name)) return false; //загружаем файл в папку для превью
		$type = $this->getFileType($ext);

		return ['name' => $name, 'type' => $type];		
	}

	public function getTxtPrev($file_name)
	{
		return file_get_contents(DIR_PREV . $file_name);
	}

	public function getTxtUser($file_name)
	{
		return file_get_contents(TXT_DIR . $file_name);
	}	

	// Возвращает расширение файла
	public function getFileExt($file)
	{
		$tmp = explode('.', $file['name']);
		return $ext = strtolower($tmp[count($tmp) - 1]);
	}

	//
	// Определяет тип файла
	// $ext 		- расширение файла
	// результат	- возвращает txt - текстовый файл, img - файл изображений
	//	
	public function getFileType($ext)
	{
		if ($ext == 'txt') 
			return 'txt';
		else
			return 'img';
	}		

	// Генерация случайной строки
	private function randomStr($length = 10) 
	{
		$s = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ_";
	    return substr(str_shuffle(str_repeat($s, ceil($length/strlen($s)))), 1, $length);
	}

	// перевод ip адреса в число
	private function ip2int($ip) {
	   $a = explode(".",$ip);
	   return $a[0]*256*256*256+$a[1]*256*256+$a[2]*256+$a[3];
	}	

	// перевод числа в ip адрес
	private function int2ip($i) {
	   $d[0] = (int)($i/256/256/256);
	   $d[1] = (int)(($i-$d[0]*256*256*256)/256/256);
	   $d[2] = (int)(($i-$d[0]*256*256*256-$d[1]*256*256)/256);
	   $d[3] = $i-$d[0]*256*256*256-$d[1]*256*256-$d[2]*256;
	   return "$d[0].$d[1].$d[2].$d[3]";
	}		    
}