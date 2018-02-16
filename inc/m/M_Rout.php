<?php

// Модуль роутинга
class M_Rout 
{	
	private $controller;
	private $action;
	private $params;
	
	public function __construct($url) 
	{
		$info = explode('/', $url);		
		$this->params = [];

		foreach ($info as $v) {
			if ($v != '')
				$this->params[] = $v;
		}

		$this->action = 'action_';
		$this->action .= ( isset($this->params[1]) ) ? $this->params[1] : 'index';

		// если $params[0] не инициализирована, присваиваем null 
		$this->params[0] = $this->params[0] ?? null;
	
		switch ($this->params[0]) {	
			case 'messages':   $this->controller = 'C_Messages'; break;
			case 'add':  	   $this->controller = 'C_Messages'; $this->action = 'action_add'; break;	
							    	
			// null - заходим на главную страницу сайта
			case null: $this->controller = 'C_Messages';
					   $this->action = 'action_index';  
					   break;

			default: $this->controller = 'C_Messages';
			$this->action = 'action_404';	
		}		
	}
	
	public function Request() 
	{
		$c = new $this->controller();
		$c->Go($this->action, $this->params);
	}
}