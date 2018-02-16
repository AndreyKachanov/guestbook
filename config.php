<?php

spl_autoload_register(
	function($classname) {
		switch ($classname[0]) {
			case 'C':
				require_once "inc/c/$classname.php";
				break;
			case 'M':
				require_once "inc/m/$classname.php";
				break;		
		}	
	}
);

define('BASE_URL', '/');
// нужно для IE 7 и ниже
define('DOMEN', 'http://guestbook.loc');

define('MYSQL_SERVER', 'localhost');
define('MYSQL_USER', 'root');
define('MYSQL_PASSWORD', 'admin');
define('MYSQL_DB', 'guestbook');
define('TABLE_PREFIX', 'aily_');

define('RULES_PATH', 'inc/m/maps/rules.php');
define('MESSAGES_PATH', 'inc/m/maps/messages.php');

define('IMG_SMALL_WIDTH', 320);
define('IMG_SMALL_HEIGHT', 240);
define('IMG_DIR', 'user_files/images/');
define('TXT_DIR', 'user_files/txt/');

define('DIR_PREV', 'img/preview/');

define('CSS_DIR', 'css/');
define('JS_DIR', 'js/');
