<?php

// == Подготовка ядра ==

chdir(__DIR__.'/..');

require 'core/main.php';

// == Передача управления контроллеру ==

foreach(require 'config/routes.php' as $pattern => $controller) {
	if(preg_match($pattern, REQUEST_PATH)) {
		require "controllers/$controller.php";
		break;
	}
}
