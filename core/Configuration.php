<?php

namespace core;

class Configuration {
  public static $config = [];

  public static function initialize($filename) {
    $data = file_get_contents($filename);
    if ($data === false) {
      // TODO: error cannot read file
      return ;
    }

    $config = json_decode($data);
    if ($config === null) {
      // TODO: error file formatting
    }

    self::$config = $config;
  }
}
