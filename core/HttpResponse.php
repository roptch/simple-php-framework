<?php

namespace core;

class HttpResponse {
  protected $headers = [];
  protected $content = '';

  public function setHeader($key, $value) {
    $this->headers[$key] = $value;
  }

  public function setContent($content) {
    $this->content = $content;
    return $this;
  }

  public function setCode($code) {
    http_response_code($code);
  }

  public function send() {
    foreach ($this->headers as $key => $value) {
      header($key . ': ' . $value);
    }

    echo $this->content;
  }
}
