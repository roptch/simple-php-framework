<?php

namespace core;

use core\HttpResponse;

class View {
  public static function jsonResponse($data) {
    $json = json_encode($data);
    if ($json === false){
      // TODO: error json encoding failed
    }

    $response = new HttpResponse();
    $response->setHeader('Content-Type', 'application/json');
    $response->setContent($json);
    return $response;
  }
}
