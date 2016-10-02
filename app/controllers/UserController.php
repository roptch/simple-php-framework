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
      $response = View::jsonResponse([
        'error' => 'User not found'
      ]);
      $response->setCode(404);
      return $response;
    } else {
      return View::jsonResponse($user->getJsonFormatted());
    }
  }

  public function deleteUserAction($id) {
    $user = User::findOne(['id' => [$id]]);
    if ($user === null) {
      $response = View::jsonResponse([
        'error' => 'User not found'
      ]);
      $response->setCode(404);
      return $response;
    }

    $user->delete();
    return View::jsonResponse([
      'success' => 'User removed successfully'
    ]);
  }

  public function getAllUsersAction() {
    $users = User::find();
    $data = [];

    foreach ($users as $user) {
      $data[] = $user->getJsonFormatted();
    }

    return View::jsonResponse($data);
  }

  public function addUserAction() {
    $name = $this->getRequest()->get('post', 'name');
    $mail = $this->getRequest()->get('post', 'mail');

    if ($name === null || $mail === null) {
      $response = View::jsonResponse([
        'error' => 'Missing parameter(s)'
      ]);
      $response->setCode(404);
      return $response;
    }

    $user = new User();
    $user->setName($name);
    $user->setMail($mail);
    $user->save();

    $response = View::jsonResponse([
      'success' => 'User created with id "' . $user->getId() . '"'
    ]);
    $response->setCode(201);
    return $response;
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
    return $response;
  }

  public function deleteLovedAction($userId, $trackId) {
    $user = User::findOne(['id' => [$userId]]);
    if ($user === null) {
      $response = View::jsonResponse([
        'error' => 'User "' . $id . '" not found'
      ]);
      $response->setCode(404);
      return $response;
    }

    $loved = $user->getLoved();
    $newLoved = [];
    foreach ($loved as $lovedTrack) {
      if ($lovedTrack->getId() !== $trackId) {
        $newLoved[] = $lovedTrack;
      }
    }

    if (count($loved) === count($newLoved)) {
      $response = View::jsonResponse([
        'error' => 'Track "' . $trackId . '" is not in user "' . $userId . '"\'s list'
      ]);
      $response->setCode(404);
      return $response;
    }

    $user->setLoved($newLoved);
    $user->save();

    return View::jsonResponse([
      'success' => 'Track removed successfully from the list'
    ]);
  }
}
