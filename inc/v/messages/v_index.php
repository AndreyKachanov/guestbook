<? extract($navparams); ?>
<div class="container container-bg">
		<div class="table-responsive">
			<table class="table" id="myTable">
				<thead>
					<tr>
						<th class="sortable">Имя</th>
						<th class="sortable">E-mail</th>
						<th>Текст</th>
						<th class="sortable">Дата создания</th>
						<th>Просмотр файла</th>
					</tr>
				</thead>
				<tbody>
				<? foreach ($messages as $message): ?>
					<tr>
						<td><?=$message['name']?></td>			
						<td><?=$message['email']?></td>
						<td class="task"><?=$message['text']; ?></td>
						<td><?=$message['created_at']?></td>
						<td>
							<?php if ($message['file_name']): ?>
								<a href="/message/<?=$message['id']?>">Смотреть</a>
							<?php else: ?>
								Файл отсутствует
							<?php endif; ?>
						</td>
					</tr>
				<? endforeach ?>
				</tbody>
			</table>
		</div>
		<!-- Постраничный вывод -->
		<?=$navbar ?>
</div>