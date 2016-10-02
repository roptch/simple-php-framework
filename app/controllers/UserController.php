<?php

namespace app\controllers;

use core\Controller;
use core\View;

use app\models\User;
use app\models\Track;

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

  public function addLovedAction($id) {
    $trackId = $this->getRequest()->get('post', 'track_id');
    if ($trackId === null) {
      $response = View::jsonResponse([
        'error' => 'Missing "track_id" parameter'
      ]);
      $response->setCode(404);
      return $response;
    }

    $user = User::findOne(['id' => [$id]]);
    if ($user === null) {
      $response = View::jsonResponse([
        'error' => 'User "' . $id . '" not found'
      ]);
      $response->setCode(404);
      return $response;
    }

    $track = Track::findOne(['id' => [$trackId]]);
    if ($track === null) {
      $response = View::jsonResponse([
        'error' => 'Track "' . $trackId . '" does not match a valid track'
      ]);
      $response->setCode(404);
      return $response;
    }

    $loved = $user->getLoved();
    foreach ($loved as $lovedTrack) {
      if ($lovedTrack->getId() === $track->getId()) {
        $response = View::jsonResponse([
          'error' => 'Track "' . $lovedTrack->getId() . '" is already in the list'
        ]);
        $response->setCode(409); // Conflict
        return $response;
      }
    }

    $loved[] = $track;
    $user->setLoved($loved);
    $user->save();

    $response = View::jsonResponse([
      'success' => 'Track added successfully to the list'
    ]);
    $response->setCode(201); // Created
    $response->setHeader('Location', ROOT_URL . '/user/' . $user->getId() . '/loved');
    return $response;
  }
}
