<?php

namespace app\controllers;

use core\Controller;
use core\View;

use app\models\User;

class UserController extends Controller {
  function __construct() {
    parent::__construct();
  }

  public function getUserAction($id) {
    $user = User::findOne(['id' => [$id]]);
    if ($user === null) {
      return View::jsonResponse([
        'error' => 'User not found'
      ]);
    } else {
      return View::jsonResponse($user->getJsonFormatted());
    }
  }

  public function getLovedAction($id) {
    $user = User::findOne(['id' => [$id]]);

    if ($user === null) {
      return View::jsonResponse([
        'error' => 'User not found'
      ]);
    } else {
      $loved = $user->getLoved();

      $result = [];
      foreach ($loved as $track) {
        $result[] = $track->getJsonFormatted();
      }

      return View::jsonResponse($result);
    }
  }
}
