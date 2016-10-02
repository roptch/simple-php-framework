<?php

namespace core;

use core\HttpResponse;
use core\AppException;

class View {
  public static function jsonResponse($data) {
    $json = json_encode($data);
    if ($json === false){
      throw new AppException('Couldn\'t encode response data as JSON');
    }

    $response = new HttpResponse();
    $response->setHeader('Content-Type', 'application/json');
    $response->setContent($json);
    return $response;
  }

  public static function htmlResponse($templatePath, $data) {
    if (!file_exists(ROOT_DIR . $templatePath)) {
      throw new AppException('Template file [' . $templatePath . '] doesn\'t exist');
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
