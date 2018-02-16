<?php
	session_start();
	include('inc/Captcha.php');	
	
	$captcha = new Captcha();
	
	// //печатаем капчу
	$captcha->printCaptcha();
	// //записываем значение капчи в сессию
    $_SESSION['keycaptcha'] = $captcha->getKeyCaptcha();
  
?>    