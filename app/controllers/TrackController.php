<?php

namespace app\controllers;

use core\Controller;
use core\View;

use app\models\Track;

class TrackController extends Controller {
  function __construct() {
    parent::__construct();
  }

  public function getTrackAction($id) {
    $track = Track::findOne(['id' => [$id]]);
    if ($track === null) {
      return View::jsonResponse([
        'error' => 'Track not found'
      ]);
    } else {
      return View::jsonResponse([
        'id'        => $track->getId(),
        'name'      => $track->getName(),
        'duration'  => $track->getDuration()
      ]);
    }
  }

  public function deleteTrackAction($id) {
    $track = Track::findOne(['id' => [$id]]);
    if ($track === null) {
      $response = View::jsonResponse([
        'error' => 'Track not found'
      ]);
      $response->setCode(404);
      return $response;
    }

    $track->delete();
    return View::jsonResponse([
      'success' => 'Track removed successfully'
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

  public function getAllTracksAction() {
    $tracks = Track::find();
    $data = [];

    foreach ($tracks as $track) {
      $data[] = $track->getJsonFormatted();
    }

    return View::jsonResponse($data);
  }

  public function addTrackAction() {
    $name     = $this->getRequest()->get('post', 'name');
    $duration = $this->getRequest()->get('post', 'duration');

    if ($name === null || $duration === null) {
      $response = View::jsonResponse([
        'error' => 'Missing parameter(s)'
      ]);
      $response->setCode(404);
      return $response;
    }

    $track = new Track();
    $track->setName($name);
    $track->setDuration($duration);
    $track->save();

    $response = View::jsonResponse([
      'success' => 'Track created with id "' . $track->getId() . '"'
    ]);
    $response->setCode(201);
    return $response;
  }

}
