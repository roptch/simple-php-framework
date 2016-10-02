<?php

namespace app\controllers;

use core\View;

class DefaultController {
  public function helloAction($name) {
    echo 'Hello ' . $name . '!';
  }

  public function pageNotFoundAction() {
    return View::htmlResponse('/app/views/404.php', [
      'url' => $_GET['q']
    ]);
  }
}
