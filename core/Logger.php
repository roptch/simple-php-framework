<?php

namespace core;

class Logger {
  /**
   * Log file path
   * @var string
   */
  private static $logfile;

  /**
   * Saves the file path for later use
   * @param  string $filename Log file path
   */
  public static function initialize($filename) {
    self::$logfile = $filename;
  }

  /**
   * Logs a new message
   * @param  string $message
   */
  public static function log($message) {
    $data = '[' . date('d/m/Y H:i:s') . '] ' . $message . "\n";
    file_put_contents(self::$logfile, $data, FILE_APPEND);
  }
}
