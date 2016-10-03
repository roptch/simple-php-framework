<?php

namespace core;

class Route {
  /**
   * Controller name to use
   * @var string
   */
  protected $controller;

  /**
   * Method to call in the controller
   * @var string
   */
  protected $action;

  /**
   * Parameters to pass to the action
   * @var array
   */
  protected $params;

  /**
   * Ctor
   * @param string $controller
   * @param string $action
   * @param array  $params
   */
  function __construct($controller, $action, $params = []) {
    $this->controller = $controller;
    $this->action     = $action;
    $this->params     = $params;
  }

  /**
   * Controller getter
   * @return string
   */
  public function getController() {
    return $this->controller;
  }

  /**
   * Controller setter
   * @param string $controller
   * @return Route
   */
  public function setController($controller) {
    $this->controller = $controller;
    return $this;
  }

  /**
   * Action getter
   * @return string
   */
  public function getAction() {
    return $this->action;
  }

  /**
   * Action setter
   * @param string $action
   * @return Route
   */
  public function setAction($action) {
    $this->action = $action;
    return $this;
  }

  /**
   * Params getter
   * @return array
   */
  public function getParams() {
    return $this->params;
  }

  /**
   * Params setter
   * @param array $params
   * @return array
   */
  public function setParams($params) {
    $this->params = $params;
    return $this;
  }
}
