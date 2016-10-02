<?php

namespace app\controllers;

class DefaultController {
  public function helloAction($name) {
    echo 'Hello ' . $name . '!';
  }

  public function pageNotFoundAction() {
    echo 'Page not found';
  }
}
