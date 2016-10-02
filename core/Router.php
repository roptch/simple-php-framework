<?php

namespace core;

use core\Route;

class Router {
  private static $mandatoryRouteAttributes = ['url', 'controller', 'action'];
  private static $routes = [];

  /**
   * Extract routes from a json config file
   * @param  {string} $filename
   */
  public static function initialize($filename) {
    $data = file_get_contents($filename);
    if ($filename === false) {
      // TODO: error cannot read file
      return ;
    }

    $routes = json_decode($data);
    if ($data === null) {
      // TODO: error file not correctly formatted
      return ;
    }

    foreach ($routes as $route) {
      foreach (self::$mandatoryRouteAttributes as $attr) {
        if (empty($route->$attr)) {
          // TODO: error missing attribute in route
          return ;
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
  public static function resolve($url) {
    $urlParts = explode('/', $url);

    foreach (self::$routes as $route) {
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
