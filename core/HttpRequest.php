<?php

namespace core;

class HttpRequest {
  private $params = [
    'post'  => [],
    'get'   => []
  ];

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

  public function get($method, $key) {
    if (isset($this->params[$method][$key])) {
      return $this->params[$method][$key];
    }

    return null;
  }
}
