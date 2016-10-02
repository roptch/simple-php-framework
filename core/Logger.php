<?php

namespace core;

class Logger {
  private static $logfile;

  public static function initialize($filename) {
    self::$logfile = $filename;
  }

  public static function log($message) {
    $data = '[' . date('d/m/Y H:i:s') . '] ' . $message . "\n";
    file_put_contents(self::$logfile, $data, FILE_APPEND);
  }
}
