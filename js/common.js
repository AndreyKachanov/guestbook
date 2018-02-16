$(document).ready(function() {
	CKEDITOR.config.autoParagraph = false;
	// Добавление кнопки обновления капчи
	$('<button id="cap" type="button" class="btn btn-outline-secondary"><i class="fa fa-refresh" aria-hidden="true"></i></button>').appendTo('#append').click(function() {
  	$('#captcha_reload').attr("src","/imgcaptcha.php?" + Math.random());
	});	
	// сортировка полей таблицы
	$("#myTable").tablesorter( {selectorHeaders: 'thead th.sortable'} );
	
	// предпросмотер изображения
	$("#imgInput").change(function() {
	    readURL(this);
	});

	// Превью перед сохранением формы
	$("#buttonPreview").on('click', function() {
		$("#panel-heading").html('');
		$("#panel-heading").removeClass('succ-good');
		$("#panel-heading").removeClass('succ-error');

		$('input').removeClass("is-invalid");
		$("#cke_content").css( "border", "none" )
		$("#ckedit").css( "display", "none" ).html('');
		$("#captcha").next().css( "display", "none" ).html('');		


		var error = false;
		var author = $("#author").val();
		if(!isValidName(author)) {
			$("#author").addClass("is-invalid");
			$("#author").next().css( "display", "block" ).html('В поле Имя должно быть от 3 до 10 латинских символы и цифр.');
			error = true;
		} else {
			$("#author").removeClass("is-invalid");
			$("#author").next().css( "display", "none" ).html('');				
		}

		var email = $("#email").val();

		if (!isValidEmail(email)) {
			$("#email").addClass("is-invalid");
			$("#email").next().css( "display", "block" ).html('Введите корректный email адрес.');
			error = true;
		} else {
			$("#email").removeClass("is-invalid");
			$("#email").next().css( "display", "none" ).html('');	
		}

		// домашняя страница
		var url = $("#homepage").val();
		if (url) {
			if (!isValidUrl(url)) {
				$("#homepage").addClass("is-invalid");
				$("#homepage").next().css( "display", "block" ).html('В поле Домашняя страница впишите правильный url адрес');				
				error = true;
			}else {
				$("#homepage").removeClass("is-invalid");
				$("#homepage").next().css( "display", "none" ).html('');	
			}
		} else {
			$("#homepage").removeClass("is-invalid");
			$("#homepage").next().css( "display", "none" ).html('');			
		}

		// если нужные поля заполнены
		if (!error) {
		    var file = $("#imgInput").val();
		    var error_file = false;
		    // если выбран файл
		    if (file) {

		    	var size = $("#imgInput")[0].files[0].size;
		    	var ext = file.split('.').pop();					    	

		    	if (ext == 'txt') {
		    		if (size > 100 * 1024) {
						$("#panel-heading").removeClass('succ-good').addClass('succ-error').html('<p>Размер txt файла не должен превышать 100 Кб.</p>'); 			    			
		    			error_file = true;
		    		}
		    	} else if (ext == 'jpg' || ext == 'png' || ext == 'gif' ) {
		    		if (size > 5242880) { 
						$("#panel-heading").removeClass('succ-good').addClass('succ-error').html('<p>Размер изображения не должен превышать 5 Мб.</p>'); 			    			
		    			error_file = true;
		    		}
		    	} else {
						$("#panel-heading").removeClass('succ-good').addClass('succ-error').html('<p>Не верный тип файла (допустимые форматы jpg, png, gif, txt).</p>'); 			    								    		
		    			error_file = true;
		    	}					    	
		    }

		    // файл был выбран, и он подходит
		    if (!error_file) {

				var fd = new FormData();

		    	// если выбран файл - добавляет в массив для отправки на сервер
		    	if (file) {					    		
					var file = $(document).find('input[type="file"]');
				    var individual_file = file[0].files[0];
			 		fd.append("file", individual_file);
		    	}

			    fd.append("name", author);
			    fd.append("email", email);
			    fd.append("text", content);
			    
			    if (url) 
			    	fd.append("url", url);

			    $.ajax({
			        type: 'POST',
			        url: '/messages/preview/',
			        data: fd,
			        // dataType: 'json', //oтвeт ждeм в json фoрмaтe
					cache: false, // кэш и прочие настройки писать именно так (для файлов)
	            	// (связано это с кодировкой и всякой лабудой)
	            	contentType: false, // нужно указать тип контента false для картинки(файла)
	            	processData: false, // для передачи картинки(файла) нужно false 						        

			        success: function(res) {

			        	if (!res) alert("Ошибка");

			        	showModal(res);

			        },
			        error: function (xhr, ajaxOptions, thrownError) { // в случae нeудaчнoгo зaвeршeния зaпрoсa к сeрвeру
			            alert(xhr.status); // пoкaжeм oтвeт сeрвeрa
			            alert(thrownError); // и тeкст oшибки
			        }
			    });			    	
		    }
		} 			
	});	
		
	// Добавление сообщения
	$("#form_task").submit(function() { // пeрeхвaтывaeм всe при сoбытии oтпрaвки

		$("#panel-heading").html('');
		$("#panel-heading").removeClass('succ-good');
		$("#panel-heading").removeClass('succ-error');

		var error = false;
		var author = $("#author").val();
		if(!isValidName(author)) {
			$("#author").addClass("is-invalid");
			$("#author").next().css( "display", "block" ).html('В поле Имя должно быть от 3 до 10 латинских символы и цифр.');
			error = true;
		} else {
			$("#author").removeClass("is-invalid");
			$("#author").next().css( "display", "none" ).html('');				
		}

		var email = $("#email").val();

		if (!isValidEmail(email)) {
			$("#email").addClass("is-invalid");
			$("#email").next().css( "display", "block" ).html('Введите корректный email адрес.');
			error = true;
		} else {
			$("#email").removeClass("is-invalid");
			$("#email").next().css( "display", "none" ).html('');	
		}

		var content = CKEDITOR.instances.content.getData();
		if (!isValidContentCkeditor(content)) {
			$("#cke_content").css( "border", "1px solid red" )
			$("#ckedit").css( "display", "block" ).html('Сообщение должно иметь от 1 до 300 символов.');
			error = true;
		} else {
			$("#cke_content").css( "border", "none" )
			$("#ckedit").css( "display", "block" ).html('');
		}
		// домашняя страница
		var url = $("#homepage").val();
		if (url) {
			if (!isValidUrl(url)) {
				$("#homepage").addClass("is-invalid");
				$("#homepage").next().css( "display", "block" ).html('В поле Домашняя страница впишите правильный url адрес');				
				error = true;
			}else {
				$("#homepage").removeClass("is-invalid");
				$("#homepage").next().css( "display", "none" ).html('');	
			}
		} else {
			$("#homepage").removeClass("is-invalid");
			$("#homepage").next().css( "display", "none" ).html('');			
		}

		var captcha = $("#captcha").val();

		if (captcha == '') {
			error = true; 
			$("#captcha").addClass("is-invalid");
			$("#captcha").next().css( "display", "block" ).html('Введите код капчи.');
		} else {
			isValidCaptcha(captcha, function(jsondata) {
				if(jsondata.type == 'bad') {								
					$("#captcha").addClass("is-invalid");// устанавливаем рамку красного цвета
					$("#captcha").next().css( "display", "block" ).html('Неверный код капчи.');	
				} 
				else {
					$("#captcha").removeClass("is-invalid");
					$("#captcha").next().css( "display", "none" ).next().empty();

					// если поля заполнены
					if (!error) {
					    var file = $("#imgInput").val();
					    var error_file = false;
					    // если выбран файл
					    if (file) {

					    	var size = $("#imgInput")[0].files[0].size;
					    	var ext = file.split('.').pop();					    	

					    	if (ext == 'txt') {
					    		if (size > 100 * 1024) {
									$("#panel-heading").removeClass('succ-good').addClass('succ-error').html('<p>Размер txt файла не должен превышать 100 Кб.</p>'); 			    			
					    			error_file = true;
					    		}
					    	} else if (ext == 'jpg' || ext == 'png' || ext == 'gif' ) {
					    		if (size > 5242880) { 
									$("#panel-heading").removeClass('succ-good').addClass('succ-error').html('<p>Размер изображения не должен превышать 5 Мб.</p>'); 			    			
					    			error_file = true;
					    		}
					    	} else {
									$("#panel-heading").removeClass('succ-good').addClass('succ-error').html('<p>Не верный тип файла (допустимые форматы jpg, png, gif, txt).</p>'); 			    								    		
					    			error_file = true;
					    	}					    	
					    }

					    // файл был выбран, и он подходит
					    if (!error_file) {

					    	// если выбран файл - добавляет в массив для отправки на сервер
							var fd = new FormData();
					    	
					    	if (file) {					    		
								var file = $(document).find('input[type="file"]');
							    var individual_file = file[0].files[0];
						 		fd.append("file", individual_file);
					    	}

						    fd.append("name", author);
						    fd.append("email", email);
						    fd.append("text", content);
						    
						    if (url) 
						    	fd.append("url", url);						    

						    $.ajax({
						        type: 'POST',
						        url: '/messages/ajax_add/',
						        data: fd,
						        dataType: 'json', //oтвeт ждeм в json фoрмaтe
								cache: false, // кэш и прочие настройки писать именно так (для файлов)
				            	// (связано это с кодировкой и т.д.)
				            	contentType: false, // нужно указать тип контента false для картинки(файла)
				            	processData: false, // для передачи картинки(файла) нужно false 						        

						        success: function(jsondata) {
						        	if(jsondata.type == 'good') {
						        		$('#form_task').trigger('reset');//очистка формы
						        		CKEDITOR.instances.content.setData('');//очистка редактора
						        		$('#image').attr('src', "#"); //удаление превью картинки
										$("#panel-heading").removeClass('succ-error').addClass('succ-good').html('<p>Сообщение удачно отправлено.</p>');	
						        		$('#captcha_reload').attr("src","/imgcaptcha.php?" + Math.random());//генерация новой капчи
						        	} else {
										$("#panel-heading").removeClass('succ-good').addClass('succ-error').html('<p>Ошибка отправки сообщения.</p>');	
						        	}
						        	// if (!res) alert("Ошибка");
						        	// 	showModal(res);
						        },
						        error: function (xhr, ajaxOptions, thrownError) { // в случae нeудaчнoгo зaвeршeния зaпрoсa к сeрвeру
						            alert(xhr.status); // пoкaжeм oтвeт сeрвeрa
						            alert(thrownError); // и тeкст oшибки
						        }
						    });			    	
					    }
					} 					
				}
			}); 
		}
		return false; // отключаем стaндaртную oтпрaвку фoрмы
	});
});

function showModal(cart) {
	$("#cart .modal-body").html(cart);
	var image_src = $("#image").attr('src');
	$("#img_modal").attr('src', image_src);
	$("#cart").modal();
}

function readURL(input) {
        var reader = new FileReader();
        reader.onload = function (e) {
            $('#image').attr('src', e.target.result);
        };
        reader.readAsDataURL(input.files[0]);
}

function isValidName(name) {
	var pattern = new RegExp(/^[a-zA-Z0-9]{3,20}$/);
	return pattern.test(name);
}

function isValidEmail(email) {
	var pattern = new RegExp(/^[-._a-z0-9]+@(?:[a-z0-9][-a-z0-9]+\.)+[a-z]{2,6}$/);
	return pattern.test(email);
}

function isValidContentCkeditor(content) {
	if (content.length > 0 && content.length < 300)
	return true;	
}

function isValidUrl(url) {
	var pattern = new RegExp(/https?:\/\/(www\.)?[-a-zA-Z0-9@:%._\+~#=]{2,256}\.[a-z]{2,6}\b([-a-zA-Z0-9@:%_\+.~#?&//=]*)/);
	return pattern.test(url);
}

function isValidCaptcha(captcha, f) {
	var callback = f || function() {};
	$.ajax({ // инициaлизируeм ajax зaпрoс
		    type: 'POST', // oтпрaвляeм в POST фoрмaтe
		    url: '/messages/captcha/', // путь дo oбрaбoтчикa, у нaс oн лeжит в тoй жe пaпкe
		    dataType: 'json', // oтвeт ждeм в json фoрмaтe
		    data: {captcha:captcha}, // дaнныe для oтпрaвки

		    success: function(jsondata) { // сoбытиe пoслe удaчнoгo oбрaщeния к сeрвeру и пoлучeния oтвeтa
		    	callback(jsondata);
		    },
		    error: function (xhr, ajaxOptions, thrownError) { // в случae нeудaчнoгo зaвeршeния зaпрoсa к сeрвeру
		        alert(xhr.status); // пoкaжeм oтвeт сeрвeрa
		        alert(thrownError); // и тeкст oшибки				        
		    }		                  
	});    		
}