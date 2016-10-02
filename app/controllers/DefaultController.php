<?php

namespace app\controllers;

use core\Controller;
use core\View;

use app\models\User;
use app\models\Track;

class DefaultController extends Controller {
  function __construct() {
    parent::__construct();
  }

  /**
   * Index page
   * @return core\HttpResponse
   */
  public function indexAction() {
    $usersData = [];
    $tracksData = [];

    $users = User::find();
    foreach($users as $user) {
      $data = $user->getJsonFormatted();
      $data['loved'] = [];
      $loved = $user->getLoved();

      foreach ($loved as $lovedTrack) {
        $data['loved'][] = $lovedTrack->getJsonFormatted();
      }

      $usersData[] = $data;
    }

    $tracks = Track::find();
    foreach ($tracks as $track) {
      $tracksData[] = $track->getJsonFormatted();
    }

    return View::htmlResponse('/app/views/index.php', [
      'users'   => $usersData,
      'tracks'  => $tracksData
    ]);
  }

  /**
   * Hello test page
   * @param  string $name
   * @return core\HttpResponse
   */
  public function helloAction($name) {
    return View::htmlResponse('/app/views/hello.php', [
      'name' => $name
    ]);
  }

  /**
   * Action called in case no route is found
   * @return core\HttpResponse
   */
  public function pageNotFoundAction() {
    $response = View::jsonResponse([
      'error' => '404 page not found'
    ]);
    $response->setCode(404);
    return $response;
  }
}
