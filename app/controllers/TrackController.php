<?php

namespace app\controllers;

use core\Controller;
use core\View;

use app\models\Track;

class TrackController extends Controller {
  function __construct() {
    parent::__construct();
  }

  /**
   * Retrieves a track data
   * @param  int $id track identifier
   * @return core\HttpResponse
   */
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

  /**
   * Deletes a track
   * @param  int $id track identifier
   * @return core\HttpResponse
   */
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

  /**
   * Retrieves all the existing tracks
   * @return core\HttpResponse
   */
  public function getAllTracksAction() {
    $tracks = Track::find();
    $data = [];

    foreach ($tracks as $track) {
      $data[] = $track->getJsonFormatted();
    }

    return View::jsonResponse($data);
  }

  /**
   * Adds a new track
   * @return core\HttpResponse
   */
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
