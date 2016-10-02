<?php

namespace core;

use core\Route;
use core\AppException;

class Router {
  private static $mandatoryRouteAttributes = [
    'url',
    'controller',
    'action',
    'method'
  ];
  private static $routes = [];

  /**
   * Extract routes from a json config file
   * @param  {string} $filename
   */
  public static function initialize($filename) {
    $data = file_get_contents($filename);
    if ($data === false) {
      throw new AppException('Cannot read route file [' . $filename . ']');
    }

    $routes = json_decode($data);
    if ($routes === null) {
      throw new AppException('Route file [' . $filename . '] is not correctly JSON formatted');
    }

    foreach ($routes as $route) {
      foreach (self::$mandatoryRouteAttributes as $attr) {
        if (!isset($route->$attr)) {
          throw new AppException('Missing attribute [' . $attr . '] in route configuration');
        }
      }
    }

    self::$routes = $routes;
  }

  /**
   * Returns the route concerning the url passed in parameter
   * if it has matched one, or null if not found
   * @param  {string} $url
   * @return {array|null} controller/action/parameters to call
   */
  public static function resolve($url, $method) {
    $urlParts = ($url === '/') ? (['']) : (explode('/', $url));

    foreach (self::$routes as $route) {
      if ($route->method !== $method)
        continue;

      $routeUrlParts = explode('/',
        ($route->url[0] === '/') ? (substr($route->url, 1)) : ($route->url));

      if (count($urlParts) !== count($routeUrlParts))
        continue ;

      $paramStack = [];
      $i = 0;
      for ($i = 0; $i < count($urlParts); ++$i) {
        if (isset($routeUrlParts[$i][0]) && $routeUrlParts[$i][0] === '{') {
          $paramStack[substr($routeUrlParts[$i], 1,
            strlen($routeUrlParts[$i]) - 2)] = $urlParts[$i];
        } else if ($urlParts[$i] !== $routeUrlParts[$i]) {
          $paramStack = [];
          break ;
        }
      }

      // Route found
      if ($i === count($urlParts)) {
        return new Route($route->controller, $route->action, $paramStack);
      }
    }

    return null;
  }

  public static function getPageNotFoundRoute() {
    return new Route('app\\controllers\\DefaultController', 'pageNotFoundAction');
  }
}
