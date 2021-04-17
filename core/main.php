<?php

// == Константы ==

## Путь текущего запроса относительно корня сайта (без параметров).
define('REQUEST_PATH', parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));

// == Действия ==

// Установка CSRF токена.
if (!isset($_COOKIE['csrf'])) {
	x_setcookie('csrf', random_key());
}

// == Функции ==

## Экранирует HTML.
function escape_html(string $string, int $flags = ENT_QUOTES): string {
	return htmlspecialchars($string, ENT_HTML5 | ENT_SUBSTITUTE | $flags);
}

## Возвращает случайный ключ.
function random_key(int $bytes = 8): string {
	return bin2hex(random_bytes($bytes));
}

## Возвращает подключение PDO.
function get_pdo(): PDO {
	static $pdo;
	if (!$pdo) {
		$pdo = new PDO(...require 'config/db.php');
		$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
	}
	return $pdo;
}

## Возвращает CSRF токен.
function get_csrf_token(): string {
	return $_COOKIE['csrf'] ?? '';
}

## Возвращает, подписан ли текущий запрос CSRF токеном.
function has_valid_csrf_token(): bool {
	return isset($_POST['csrf'], $_COOKIE['csrf']) && $_POST['csrf'] &&
			$_POST['csrf'] == $_COOKIE['csrf'];
}

## Устанавливает cookie (третий параметр max-age вместо expires).
function x_setcookie(string $name, string $value, int $max_age = 0): void {
	$options = ['path' => '/', 'samesite' => 'Lax'];
	if ($max_age > 0) {
		$options['expires'] = time() + $max_age;
	}
	setcookie($name, $value, $options);
}
