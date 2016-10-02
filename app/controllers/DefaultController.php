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
    $response = View::htmlResponse('/app/views/404.php', [
      'url' => $_GET['q']
    ]);
    $response->setCode(404);
    return $response;
  }
}
