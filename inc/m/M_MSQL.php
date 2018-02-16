<?php
//
// Помощник работы с БД
//

class M_MSQL 
{
	private static $instance; // экземпляр класса  
	private $db;
	
	//
	// Получение экземпляра класса
	// результат	- экземпляр класса MSQL
	//	
	public static function Instance()
	{
		if(self::$instance == null){
			self::$instance = new self();
		}
		
		return self::$instance;
	}
	
	private function __construct()
	{
		// Языковая настройка
		setlocale(LC_ALL, 'ru_RU.UTF8');

		// Подключение к БД	
		$this->db = new PDO('mysql:host=' . MYSQL_SERVER . ';dbname=' . MYSQL_DB, MYSQL_USER, MYSQL_PASSWORD);
		$this->db->exec('SET NAMES UTF8');
		$this->db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
	}
	
	//
	// Выборка строк
	// $query    	- полный текст SQL запроса
	// результат	- массив выбранных объектов
	//
	public function Select($query)
	{
		$q = $this->db->prepare($query);
		$q->execute();
		
		if($q->errorCode() != PDO::ERR_NONE){
			$info = $q->errorInfo();
			die($info[2]);
		}
			
		return $q->fetchAll();					
	}

	//
	// Вставка строки
	// $table 		- имя таблицы
	// $object 		- ассоциативный массив с парами вида "имя столбца - значение"
	// результат	- идентификатор новой строки
	//
	public function Insert($table, $object)
	{
		$columns = array();
		
		foreach($object as $key => $value){
		
			$columns[] = $key;
			$masks[] = ":$key";
			
			if($value === null){
				$object[$key] = 'NULL';
			}
		}
		
		$columns_s = implode(',', $columns);
		$masks_s = implode(',', $masks);
		
		$query = "INSERT INTO $table ($columns_s) VALUES ($masks_s)";

		$q = $this->db->prepare($query);
		$q->execute($object);
		
		if($q->errorCode() != PDO::ERR_NONE){
			$info = $q->errorInfo();
			die($info[2]);
		}
		
		return $this->db->lastInsertId();		
	}
	
	//
	// Изменение строк
	// $table 		- имя таблицы
	// $object 		- ассоциативный массив с парами вида "имя столбца - значение"
	// $where		- условие (часть SQL запроса)
	// результат	- число измененных строк
	//		
	public function Update($table, $object, $where)
	{
		$sets = [];
		 
		foreach($object as $key => $value){
			
			$sets[] = "$key=:$key";
			
			if($value === NULL){
				$object[$key]='NULL';
			}
		 }
		 
		$sets_s = implode(',',$sets);
		$query = "UPDATE $table SET $sets_s WHERE $where";

		$q = $this->db->prepare($query);
		$q->execute($object);

		if($q->errorCode() != PDO::ERR_NONE){
			$info = $q->errorInfo();
			die($info[2]);
		}
		
		return $q->rowCount();
	}
	
	//
	// Удаление строк
	// $table 		- имя таблицы
	// $where		- условие (часть SQL запроса)	
	// результат	- число удаленных строк
	//			
	public function Delete($table, $where)
	{
		$query = "DELETE FROM $table WHERE $where";
		$q = $this->db->prepare($query);
		$q->execute();
		
		if($q->errorCode() != PDO::ERR_NONE){
			$info = $q->errorInfo();
			die($info[2]);
		}
		
		return $q->rowCount();
	}
}