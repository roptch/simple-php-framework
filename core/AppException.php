<?php

namespace core;

class AppException extends \Exception {
  /**
   * Ctor
   * @param string  $message  Description of the exception
   * @param integer $code     Code of the exception
   * @param Exception  $previous Previously triggered exception
   */
  function __construct($message, $code = 0, Exception $previous = null) {
    parent::__construct($message, $code, $previous);
  }
}
