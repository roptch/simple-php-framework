<?php

namespace app\controllers;

use app\models\User;
use app\models\Track;

class UserController {
  public function getAction($id) {
    $user = User::findOne(['id' => [$id]]);
    var_dump($user);
    // $user->delete();
    // var_dump($user);
    // var_dump($user[0]->getLoved());
    // $user[0]->setName('dreamstate');
    // $user[0]->save();
    // $track = Track::find(['id' => [3]])[0];
    // var_dump($track);
    //
    // $user = User::find(['id' => [1]])[0];
    // $user->setLoved([$track]);
    // $user->save();
    // echo $user->getId();
  }
}
