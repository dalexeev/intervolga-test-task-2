<!DOCTYPE html>
<html>
<head>
<title>Страны</title>
<?php require 'views/global/head.php'; ?>
</head>
<body>
<h1>Страны</h1>

<form id="AddForm" method="POST">
Название:
<input type="text" name="name" required minlength="1" maxlength="50" />
Код:
<input type="text" name="code" size="2" maxlength="2" pattern="[A-Z]{2}" />
<input type="hidden" name="csrf" value="<?= escape_html(get_csrf_token()) ?>" />
<input type="submit" id="AddFormSubmitButton" value="Добавить" />
<input type="reset" id="AddFormResetButton" value="Очистить" />
</form>

<table>
<tbody id="CountryTBody">
<tr>
<th>ID</th>
<th>Название</th>
<th>Код</th>
</tr>
<?php foreach(Country::get_countries() as $country): ?>
<tr>
<td><?= $country->get_id() ?></td>
<td><?= escape_html($country->get_name()) ?></td>
<td><?= escape_html($country->get_code() ?? '—') ?></td>
</tr>
<?php endforeach; ?>
</tbody>
</table>

<script>
AddForm.onsubmit = async function(e) {
	e.preventDefault();
	
	AddForm.name.readOnly = true;
	AddForm.code.readOnly = true;
	AddFormSubmitButton.disabled = true;
	AddFormResetButton.disabled = true;
	
	let response = await fetch('', {
		method: 'POST',
		body: new FormData(AddForm),
	});
	
	let result = await response.json();
	
	if (result.success) {
		let tr = document.createElement('tr');
		
		let id = result.id;
		let name = escapeHTML(AddForm.name.value);
		let code = escapeHTML(AddForm.code.value);
		if (code == '') {
			code = '—';
		}
		
		tr.innerHTML = `<td>${id}</td><td>${name}</td><td>${code}</td>`;
		CountryTBody.append(tr);
		AddForm.reset();
	} else {
		alert('Ошибка: ' + result.error);
	}
	
	AddForm.name.readOnly = false;
	AddForm.code.readOnly = false;
	AddFormSubmitButton.disabled = false;
	AddFormResetButton.disabled = false;
};
</script>

</body>
</html>
