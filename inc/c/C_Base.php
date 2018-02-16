<?php 

// Базовый контроллер
abstract class C_Base extends C_Controller 
{
	protected $title;	// заголовок страницы
	protected $content; // содержание страницы
	protected $keywords; //ключевые слова страницы
	protected $description; //описание страницы
	protected $styles; //стили
	protected $scripts; //скрипты

	public function __construct() 
	{
		$this->keywords = '';
		$this->description = '';
		$this->styles = [
			'../libs/bootstrap-4.0.0/dist/css/bootstrap.min', 
			'main.min'
		];		
		$this->scripts = [
			'jquery.min',
			'jquery.tablesorter.min',
			'../libs/bootstrap-4.0.0/dist/js/bootstrap.min',
			'../libs/ckeditor/ckeditor',
			'scripts.min'
		];
	}	

	// Можно добавить доп. действия перед выполнением actions контроллера
	protected function before() 
	{
		$this->title = "Main | Гостевая книга";
		$this->content = '';
	}

	// Генерация базового шаблона
	public function render() 
	{
		$vars = [
					'title' => $this->title, 
					'content' => $this->content,
					'keywords' => $this->keywords,
					'description' => $this->description,
					'styles' => $this->styles,
					'scripts' => $this->scripts,
		];

		$page = $this->template("inc/v/v_main.php", $vars);
		echo $page;
	}
}