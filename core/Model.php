<?php

namespace core;

use core\ModelCache;

abstract class Model {
  private static $db = null;
  private static $cache = [];

  public function getId();

  public function __call($method, $args) {
    $model = self::initialize();

    if (isset(self::$cache[$model]->additionalGetters[$method])
        && is_callable(self::$cache[$model]->additionalGetters[$method])) {
      return call_user_func_array(
        self::$cache[$model]->additionalGetters[$method], [$this->getId()]);
    } else {
      // TODO: error method doesn't exist
    }
  }

  public static function find($filters = []) {
    $model = self::initialize();

    $sql = self::generateSelectQuery($model, $filters);
    $query = self::$db->prepare($sql);
    $query->execute(self::generateSqlValueArray($filters));

    $entities = self::generateEntities($query->fetchAll(), $model);

    return $entities;
  }

  private static function generateSelectQuery($model, $args) {
    $sql = "SELECT " . self::$cache[$model]->table . ".*
            FROM " . self::$cache[$model]->table;

    $i = 0;
    foreach ($args as $column => $values) {
      if ($i === 0) {
        $sql .= " WHERE (";
      } else {
        $sql .= ") AND (";
      }

      $j = 0;
      foreach ($values as $value) {
        if ($j > 0) {
          $sql .= " OR ";
        }

        $sql .= self::$cache[$model]->table . "." . $column . " = ?";

        ++$j;
      }

      $sql .= ")";

      ++$i;
    }

    return $sql;
  }

  private static function generateSqlValueArray($filters) {
    $result = [];

    foreach ($filters as $column => $values) {
      foreach ($values as $value) {
        $result[] = $value;
      }
    }

    return $result;
  }

  private static function generateEntities($sqlResults, $model) {
    $entities = [];

    foreach ($sqlResults as $result) {
      $entity = new $model;

      foreach (self::$cache[$model]->properties as $property) {
        $entity->$property = $result[self::_modelToSqlName($property)];
      }

      $entities[] = $entity;
    }

    return $entities;
  }

  private static function initialize() {
    $model = get_called_class();

    if (self::$db === null) {
      self::$db = new \PDO('mysql:host=localhost;dbname=deezer_api;charset=utf8',
                          'root', '');
    }

    if (isset(self::$cache[$model]))
      return $model;

    self::$cache[$model] = new ModelCache();
    self::$cache[$model]->table = self::_modelToSqlName($model);

    $reflect = new \ReflectionClass($model);
    foreach ($reflect->getDefaultProperties() as $name => $defaultValue) {
      if ($name[0] !== '_') {
        self::$cache[$model]->properties[] = $name;
      } else if ($name === '_manyToMany') {
        self::$cache[$model]->manyToMany = $defaultValue;
      }
    }

    self::generateManyToManyGetters($model);

    return $model;
  }

  private static function generateManyToManyGetters($model) {
    $db = self::$db;

    foreach (self::$cache[$model]->manyToMany as $attr => $toModel) {
      self::$cache[$model]->additionalGetters['get' . ucfirst($attr)] =
        function($id) use ($model, $toModel, $db) {
          $table = self::_modelToSqlName($model);
          $toTable = self::_modelToSqlName($toModel);
          $linkTableArr = [$table, $toTable];
          sort($linkTableArr);
          $linkTable = implode('_', $linkTableArr);

          $sql = "SELECT " . $linkTable . "." . $toTable . "_id
                  FROM " . $linkTable . "
                  WHERE " . $table . "_id = ?";
          $query = $db->prepare($sql);
          $query->execute([$id]);

          $idList = [];
          foreach ($query->fetchAll() as $row) {
            $idList[] = $row[$toTable . '_id'];
          }

          return $toModel::find(['id' => $idList]);
        };
    }
  }

  public static function _modelToSqlName($model) {
    $modelPath = explode('\\', $model);
    $modelName = end($modelPath);

    $result = '';
    for ($i = 0; $i < strlen($modelName); ++$i) {
      if ($i > 0 && ctype_upper($modelName[$i])) {
        $result .= '_';
      }

      $result .= strtolower($modelName[$i]);
    }

    return $result;
  }
}
