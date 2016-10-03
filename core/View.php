<?php

namespace core;

use core\HttpResponse;
use core\AppException;

class View {
  /**
   * Build an http response to display json data
   * @param  array $data Data to be formatted to json
   * @return core\HttpResponse
   */
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

  /**
   * Builds an http response to display an html page
   * @param  string $templatePath Template file containing html/php code
   * @param  array $data          Data to be used in the template with the $data[] variable
   * @return core\HttpResponse
   */
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
