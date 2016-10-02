<?php

define('ROOT_DIR', dirname(dirname(__FILE__)));

use core\Configuration;
use core\Logger;
use core\Router;
use core\AppException;
use core\View;

use app\controllers\DefaultController;

// Simple autoloading
spl_autoload_register(function($class) {
  $file = ROOT_DIR . '/' . str_replace('\\', '/', $class) . '.php';

  if (file_exists($file)) {
    require_once($file);
  } else {
    throw new AppException('Couldn\'t load class [' . $class . ']');
  }
});

try {
  Logger::initialize(ROOT_DIR . '/logs/log.txt');
  Configuration::initialize(ROOT_DIR . '/configuration/app.json');
  Router::initialize(ROOT_DIR . '/configuration/routes.json');
  $route = Router::resolve(isset($_GET['q']) ? ($_GET['q']) : ('/'), $_SERVER['REQUEST_METHOD']);

  if ($route === null) {
    $route = Router::getPageNotFoundRoute();
  }

  $controllerName = $route->getController();
  $controller = new $controllerName();
  $response = call_user_func_array([$controller, $route->getAction()], $route->getParams());
  $response->send();
} catch (Exception $e) {
  Logger::log($e->getMessage());
  View::jsonResponse(['error' => 'Server error'])->send();
}
