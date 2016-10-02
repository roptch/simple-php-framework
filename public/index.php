<?php

define('ROOT_DIR', dirname(dirname(__FILE__)));

use core\Router;
use app\controllers\DefaultController;

// Simple autoloading
spl_autoload_register(function($class) {
  $file = ROOT_DIR . '/' . str_replace('\\', '/', $class) . '.php';

  if (file_exists($file)) {
    require_once($file);
  } else {
    // TODO: error
  }
});

Router::initialize(ROOT_DIR . '/configuration/routes.json');
$route = Router::resolve($_GET['q'], $_SERVER['REQUEST_METHOD']);

if ($route === null) {
  $route = Router::getPageNotFoundRoute();
}

$controllerName = $route->getController();
$controller = new $controllerName();
$response = call_user_func_array([$controller, $route->getAction()], $route->getParams());
$response->send();
