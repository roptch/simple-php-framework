<?php

namespace core;

class HttpRequest {
  /**
   * Contains GET and POST parameters
   * @var array
   */
  private $params = [
    'post'  => [],
    'get'   => []
  ];

  /**
   * Ctor
   */
  function __construct() {
    if (isset($_POST)) {
      foreach ($_POST as $key => $value) {
        $this->params['post'][$key] = $value;
      }
    }

    if (isset($_GET)) {
      foreach ($_GET as $key => $value) {
        if ($key === 'q') {
          continue ;
        }

        $this->params['get'][$key] = $value;
      }
    }
  }

  /**
   * Retrieves a parameter
   * @param  string $method Type of the parameter we need (post or get)
   * @param  string $key    Name of the parameter
   * @return string         Value of the parameter
   */
  public function get($method, $key) {
    if (isset($this->params[$method][$key])) {
      return $this->params[$method][$key];
    }

    return null;
  }
}
