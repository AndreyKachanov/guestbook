<?php if (!empty($message)): ?>

<section class="tasks">
		<article class="task">
			<div class="card">
				<div class="card-header">
					<p><span>Имя: </span> <?=$message['name']?></p>
					<p><span>Email: </span><a href="mailto:<?=$message['email']?>"><?=$message['email']?></a></p>

					<?php if (!empty($message['url'])): ?>
						<p><span>Домашняя страница: </span><a target="_blank" href="<?=$message['url']?>"><?=$message['url']?></a></p>
					<?php endif; ?>
					
				</div>
				<div class="card-body text-secondary row justify-content-center">
					<?php if (!empty($message['img'])): ?>

						<div class="card-text col-4">
							<img class="img_prev" src="<?=DIR_PREV . $message['img']?>" alt="">
						</div>

					<?php elseif(!empty($message['txt'])): ?>

						<div class="card-text col">
						<h3 class="text-center">Содержимое TXT файла:</h3>
							<p><?=$message['txt']?></p>
						</div>
					<?php else: ?>	
						<div class="card-text col">	
						<h3 class="text-center">Файл не выбран.</h3>
						</div>
					<?php endif; ?>											
				</div>
			</div>
		</article>
</section>

<?php else: ?>

	<h3>Форма пуста</h3>

<?php endif; ?>


