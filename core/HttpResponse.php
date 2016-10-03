<?php

namespace core;

class HttpResponse {
  /**
   * Response http headers
   * @var array
   */
  protected $headers = [];

  /**
   * Content to be displayed
   * @var string
   */
  protected $content = '';

  /**
   * Adds a header line in the response
   * @param string $key   Name of the header attribute
   * @param string $value Value of the header attribute
   */
  public function setHeader($key, $value) {
    $this->headers[$key] = $value;
  }

  /**
   * Sets what content to display in the response
   * @param string $content
   */
  public function setContent($content) {
    $this->content = $content;
    return $this;
  }

  /**
   * Sets the http code
   * @param int $code
   */
  public function setCode($code) {
    http_response_code($code);
  }

  /**
   * Sets the headers and display the content
   */
  public function send() {
    foreach ($this->headers as $key => $value) {
      header($key . ': ' . $value);
    }

    echo $this->content;
  }
}
