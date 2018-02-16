<?php
// правила для валидации полей

	return [
		TABLE_PREFIX . 'messages' => [
			'fields' =>    ['id', 'name', 'email', 'url', 'captcha', 'text', 'file_name', 'ip', 'browser', 'created_at'], 
			'not_empty' => ['id', 'name', 'email', 'text', 'captcha', 'file_name', 'ip', 'browser', 'created_at'],
			'html_allowed' => [],
			'only_latin_letters' => ['name'],
			'valid_captcha' => ['captcha'],
			'email' => ['email'],
			'url' => ['url'],
			'range' => [
						'name' => ['3', '20'],
						'text' => ['1', '300'],
						'url' => ['1', '300']
						],
			'labels' => [
				'name' => '"Имя"',
				'email' => '"Email"',
				'url' => '"Url"',
				'captcha' => '"Капча"',
				'text' => '"Сообщение"'
			],
			'pk' => 'id'
		]
	];
