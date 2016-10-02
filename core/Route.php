<?php

namespace core;

class Route {
  protected $controller;
  protected $action;
  protected $params;

  function __construct($controller, $action, $params = []) {
    $this->controller = $controller;
    $this->action     = $action;
    $this->params     = $params;
  }

  public function getController() {
    return $this->controller;
  }

  public function setController($controller) {
    $this->controller = $controller;
    return $this;
  }

  public function getAction() {
    return $this->action;
  }

  public function setAction() {
    $this->action = $action;
    return $this;
  }

  public function getParams() {
    return $this->params;
  }

  public function setParams($params) {
    $this->params = $params;
    return $this;
  }
}
