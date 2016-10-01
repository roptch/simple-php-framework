<?php

namespace app\controllers;

use app\models\User;

class UserController {
  public function getAction($id) {
    $user = User::find(['id' => [$id, 2]]);
    var_dump($user);
    var_dump($user[0]->getLoved());
  }
}
