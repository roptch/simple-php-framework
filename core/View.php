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

  public static function htmlResponse($templatePath, $args) {
    if (!file_exists($templatePath)) {
      // TODO: error missing template
    }

    $data = [];
    foreach ($args as $key => $value) {
      $data[$key] = htmlspecialchars($value);
    }

    ob_start();
    include(ROOT_DIR . $templatePath);
    $content = ob_get_contents();
    ob_end_clean();

    $response = new HttpResponse();
    $response->setHeader('Content-Type', 'text/html; charset=utf-8');
    $response->setContent($content);
    return $response;
  }
}
