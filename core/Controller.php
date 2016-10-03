<?php

namespace core;

use core\HttpRequest;

abstract class Controller {
  /**
   * Http request
   * @var core\HttpRequest
   */
  private $request;

  /**
   * Ctor
   */
  function __construct() {
    $this->request = new HttpRequest();
  }

  /**
   * Request getter
   * @return core\HttpRequest
   */
  public function getRequest() {
    return $this->request;
  }
}
