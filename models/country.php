<?php

## Представляет таблицу `countries`.
class Country {


// Загруженные данные (массив массивов).
// Структура: [1 => ['id' => 1, 'name' => 'Россия', 'code' => 'RU']].
private static array $datas;

// Ссылка на элемент-массив в `self::$datas`.
private array $data;

## Самодельный статический конструктор. Вызывается автоматически.
public static function _load(): void {
	$pdo = get_pdo();
	$req = $pdo->prepare('SELECT * FROM `countries`');
	$res = $req->execute();
	
	if (!$res) {
		trigger_error('Country: Ошибка запроса к БД.');
	}
	
	self::$datas = $req->fetchAll();
}

## Возвращает, является ли указанное название страны допустимым.
public static function is_valid_name(string $name): bool {
	$l = mb_strlen($name);
	return $l > 0 && $l <= 50;
}

## Возвращает, является ли указанный код страны допустимым.
public static function is_valid_code(?string $code): bool {
	return $code === null || preg_match('/^[A-Z]{2}$/', $code);
}

## Возвращает, имеется ли в базе данных указанное название страны.
public static function has_name(string $name): bool {
	return in_array($name, array_column(self::$datas, 'name'));
}

## Возвращает, имеется ли в базе данных указанный код страны.
## Записи с пустым полем "Код" не учитываются.
public static function has_code(?string $code): bool {
	if ($code === null) {
		return false;
	}
	return in_array($code, array_column(self::$datas, 'code'));
}

## Добавляет новую страну в базу данных. Возвращает сведения о результате
## операции в виде ассоциативного массива, пригодного для преобразования в JSON.
public static function create(string $name, ?string $code): array {
	if (!self::is_valid_name($name)) {
		return ['success' => false, 'error' => 'ERR_INVALID_NAME'];
	}
	if (!self::is_valid_code($code)) {
		return ['success' => false, 'error' => 'ERR_INVALID_CODE'];
	}
	if (self::has_name($name)) {
		return ['success' => false, 'error' => 'ERR_NAME_EXISTS'];
	}
	if (self::has_code($code)) {
		return ['success' => false, 'error' => 'ERR_CODE_EXISTS'];
	}
	
	$pdo = get_pdo();
	$req = $pdo->prepare(
		'INSERT INTO `countries` (`name`, `code`)
		VALUES (:name, :code)'
	);
	$res = $req->execute([
		':name' => $name,
		':code' => $code,
	]);
	
	if (!$res) {
		return ['success' => false, 'error' => 'ERR_UNKNOWN'];
	}
	
	$id = (int) $pdo->lastInsertId();
	
	self::$datas[$id] = [
		'name' => $name,
		'code' => $code,
	];
	
	return ['success' => true, 'id' => $id];
}

## Возвращает генератор для перебора объектов стран.
public static function get_countries(): iterable {
	foreach (self::$datas as $k => $v) {
		yield new self($k);
	}
}

## Конструктор. См. также `Country::create($id)`.
public function __construct(int $id) {
	assert($id > 0);
	
	if (!isset(self::$datas[$id])) {
		trigger_error('Country: ID не существует.');
	}
	
	$this->data =& self::$datas[$id];
}

## Возвращает ID страны.
public function get_id(): int {
	return $this->data['id'];
}

## Возвращает название страны.
public function get_name(): string {
	return $this->data['name'];
}

## Задаёт название страны.
public function set_name(string $name): void {
	if (self::is_valid_name($name) && !self::has_name($name)) {
		$this->data['name'] = $name;
	}
}

## Возвращает двухбуквенный код страны.
public function get_code(): ?string {
	return $this->data['code'];
}

## Задаёт двухбуквенный код страны.
public function set_code(?string $code): void {
	if (self::is_valid_code($code) && !self::has_code($code)) {
		$this->data['code'] = $code;
	}
}


} // end class Country

Country::_load();
