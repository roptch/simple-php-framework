<?php

namespace app\controllers;

use core\Controller;
use core\View;

class DefaultController extends Controller {
  function __construct() {
    parent::__construct();
  }

  public function helloAction($name) {
    echo 'Hello ' . $name . '!';
  }

  public function pageNotFoundAction() {
    $response = View::jsonResponse([
      'error' => '404 page not found'
    ]);
    $response->setCode(404);
    return $response;
  }
}
