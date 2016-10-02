<?php

namespace core;

use core\HttpRequest;

abstract class Controller {
  private $request;

  function __construct() {
    $this->request = new HttpRequest();
  }

  public function getRequest() {
    return $this->request;
  }
}
