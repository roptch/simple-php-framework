<?php

namespace app\controllers;

class IndexController {
  public function helloAction($name) {
    echo 'Hello ' . $name . '!';
  }
}
