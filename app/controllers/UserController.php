<?php

namespace app\controllers;

use core\View;

use app\models\User;

class UserController {
  public function getUserAction($id) {
    $user = User::findOne(['id' => [$id]]);
    if ($user === null) {
      return View::jsonResponse([
        'error' => 'User not found'
      ]);
    } else {
      return View::jsonResponse([
        'id'    => $user->getId(),
        'name'  => $user->getName(),
        'mail'  => $user->getMail()
      ]);
    }
  }
}
