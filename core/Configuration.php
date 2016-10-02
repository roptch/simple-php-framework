<?php

namespace core;

use core\AppException;

class Configuration {
  public static $config = [];

  public static function initialize($filename) {
    $data = file_get_contents($filename);
    if ($data === false) {
      throw new AppException('Cannot read configuration file [' . $filename . ']');
    }

    $config = json_decode($data);
    if ($config === null) {
      throw new AppException('Configuration file [' . $filename . '] is not correctly JSON formatted');
    }

    self::$config = $config;
  }
}
