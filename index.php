<?php
define('APP_DIR', dirname(__DIR__) . '/future1');
require_once APP_DIR . '/src/functions.php';

#showData($_GET);

// Определяем метод запроса
$method = $_SERVER['REQUEST_METHOD'];

// Получаем данные из тела запроса
$formData = getFormData($method);
if (isset($formData['url'])) {
    unset($formData['url']);
}

// Разбираем url
$url = (isset($_GET['url'])) ? $_GET['url'] : '';
$url = rtrim($url, '/');
$urls = explode('/', $url);

// Определяем роутер и url data
$urlData = array_slice($urls, 3);

/*echoBR('method');
showData($method);*/

// Подключаем файл-роутер и запускаем главную функцию
include_once 'routers/' . $method . '.php';
route($urlData, $formData);

