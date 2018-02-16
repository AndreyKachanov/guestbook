<div class="container container-bg">
	<section class="tasks">
		<article class="task">
			<h3 class="text-center">Добавить сообщение</h2>

			<? if(!empty($errors)): ?>
				<div class="alert alert-danger" role="alert">
					<? foreach($errors as $error): ?>
						<p><?=$error?></p>
					<? endforeach; ?>
				</div>
			<? endif; ?>
			
			<div id="panel-heading"></div>
			<form method="POST" enctype="multipart/form-data" id="form_task">
				<div class="row">
					<div class="left col-xs-12 col-md-6">						
						<div class="form-group col">						
							<label for="author">Имя:</label>
							<input name="name" data-author="<?=$fields['name'] ?? ''?>" type="text" class="form-control" id="author" value="<?=$fields['name'] ?? ''?>">
							<div class="invalid-feedback"></div>
						</div>

						<div class="form-group col">	
							<label for="email">Email:</label>
							<input name="email" type="text" class="form-control" id="email" value="<?=$fields['email'] ?? ''?>">
							<div class="invalid-feedback"></div>
						</div>


						<div class="form-group col">	
							<label for="homepage">Домашняя страница:</label>
							<input name="url" type="text" class="form-control" id="homepage" value="<?=$fields['url'] ?? ''?>">
							<div class="invalid-feedback"></div>
						</div>
						<div class="form-group col">
							<div id="append"> <img class="" src="imgcaptcha.php" id="captcha_reload"></div>								
						</div>						
						<div class="form-group col">
							<label for="captcha">Введите код капчи:</label>
							<input type="text" id="captcha" name="captcha" value="" class="form-control">
							<div class="invalid-feedback"></div>	
						</div>
					</div>

					<div class="right col-xs-12 col-md-6">						
						<div class="form-group">
							<div class="d-flex flex-column align-items-center">
								<label for="InputImg">Изображение:</label>

							      <div class="upload-img form-control">
							      	<img id="image" src="#" alt="" />
							      </div>
							    	
							    <input type="file" name="file" id="imgInput"> 
							</div>	

						    <div class="help-block">Допустимые форматы jpg, png, gif, txt. Допустимый размер изображения - не более 320х240 пикселей, размер файла не больше 5 Мб. Допустимый размер txt файла - не больше 100 кб.</div>
						 </div>
					 </div>

				</div>
				<div class="row">
					<div class="col">
						<div class="form-group col editor">
							<label for="replace">Сообщение:</label>
								<textarea name="text" id="content" class="form-control" rows="10"><?=$fields['text'] ?? ''?></textarea>						
							<div id="ckedit" class="invalid-feedback"></div>
						</div>							
					</div>
				</div>	
				<div class="d-flex justify-content-md-center flex-column flex-md-row">
					<button id="buttonPreview" type="button" data-toggle="modal" data-target="#exampleModalCenter" class="btn btn-secondary col-xs-12">Предварительный просмотр</button>
					<button class="btn btn-warning col-xs-12" type="submit">Добавить</button>
				</div>				
			</form>
		</article>
	</section>
</div>			
