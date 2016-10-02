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
}
