<?php

define('ROOT_DIR', dirname(dirname(__FILE__)));

// Simple autoloading
spl_autoload_register(function($class) {
  $file = ROOT_DIR . '/' . str_replace('\\', '/', $class) . '.php';

  if (file_exists($file)) {
    require_once($file);
  } else {
    // TODO: error
  }
});

core\Router::initialize(ROOT_DIR . '/configuration/routes.json');
$route = core\Router::resolve($_GET['q']);

if ($route === null) {
  // TODO: 404
}

$controller = new $route['controller'];
call_user_func_array([$controller, $route['action']], $route['params']);
