<?php

namespace core;

use core\ModelCache;

abstract class Model {
  private static $db = null;
  private static $cache = [];
  public $_mtmToUpdate = [];

  abstract public function getId();
  private function setId($id) {}

  public function __call($method, $args) {
    $model = self::initialize();

    if (isset(self::$cache[$model]->additionalGetters[$method])
        && is_callable(self::$cache[$model]->additionalGetters[$method])) {
      return call_user_func_array(
        self::$cache[$model]->additionalGetters[$method], [$this->getId()]);
    } else if (isset(self::$cache[$model]->additionalSetters[$method])
               && is_callable(self::$cache[$model]->additionalSetters[$method])) {
      return call_user_func_array(
        self::$cache[$model]->additionalSetters[$method], [$this, $args]);
    } else {
      // TODO: error method doesn't exist
    }
  }

  public function delete() {
    if ($this->getId() === null) {
      return ;
    }

    $model = self::initialize();

    $sql = "DELETE FROM " . self::_modelToSqlName($model) . "
            WHERE " . self::_modelToSqlName($model) . ".id=?";
    $query = self::$db->prepare($sql);
    $query->execute([$this->getId()]);

    if (count(self::$cache[$model]->manyToMany) > 0) {
      $this->deleteMTMRelations($model);
    }

    $this->id = null;
  }

  private function deleteMTMRelations($model) {
    $table = self::_modelToSqlName($model);
    foreach (self::$cache[$model]->manyToMany as $attr => $toModel) {
      $linkTable = self::_getMTMLinkTableName($toModel);

      $sql = "DELETE FROM " . $linkTable . "
              WHERE " . $linkTable . "." . $table . "_id=?";
      $query = self::$db->prepare($sql);
      $query->execute([$this->getId()]);
    }
  }

  public function save() {
    $model = self::initialize();
    $sql = '';
    $values = [];

    if ($this->getId() !== null) {
      // Update
      $sql = "UPDATE " . self::$cache[$model]->table . " SET";
      foreach (self::$cache[$model]->properties as $property) {
        if (count($values) > 0) {
          $sql .= ',';
        }
        $sql .= " " . self::$cache[$model]->table . "."
                . self::_modelToSqlName($property) . "=?";
        $values[] = $this->$property;
      }

      $sql .= " WHERE " . self::$cache[$model]->table . ".id=?";
      $values[] = $this->getId();
    } else {
      // Insert
      $sql = "INSERT INTO " . self::$cache[$model]->table . "(";
      $i = 0;
      foreach (self::$cache[$model]->properties as $property) {
        if ($property === 'id') {
          continue ;
        }
        if ($i > 0) {
          $sql .= ',';
        }
        $sql .= self::_modelToSqlName($property);
        ++$i;
      }
      $sql .= ") VALUES(";
      foreach (self::$cache[$model]->properties as $property) {
        if ($property === 'id') {
          continue ;
        }
        if (count($values) > 0) {
          $sql .= ',';
        }
        $sql .= "?";
        $values[] = $this->$property;
      }
      $sql .= ")";
    }

    $query = self::$db->prepare($sql);
    $query->execute($values);

    if ($this->getId() === null) {
      $this->id = self::$db->lastInsertId();
    }

    if (count($this->_mtmToUpdate) > 0) {
      $this->updateMTMRelations($model);
    }
  }

  private function updateMTMRelations($model) {
    foreach ($this->_mtmToUpdate as $attr => $entityList) {
      $table = self::_modelToSqlName($model);
      $toTable = self::_modelToSqlName(self::$cache[$model]->manyToMany[$attr]);

      $linkTable = self::_getMTMLinkTableName(self::$cache[$model]->manyToMany[$attr]);
      $sql = "DELETE FROM " . $linkTable . "
              WHERE " . $linkTable . "." . $table . "_id=?";
      $query = self::$db->prepare($sql);
      $query->execute([$this->getId()]);

      $sql = "INSERT INTO " . $linkTable . "(" . $table . "_id, " . $toTable . "_id)
              VALUES ";

      $value = [];
      $i = 0;
      foreach ($entityList as $entity) {
        if ($entity->getId() === null)
          continue;

        if ($i > 0) {
          $sql .= ',';
        }

        $sql .= "(?,?)";
        $values[] = $this->getId();
        $values[] = $entity->getId();
        ++$i;
      }

      $query = self::$db->prepare($sql);
      $query->execute($values);
    }

    $this->_mtmToUpdate = [];
  }

  public static function find($filters = []) {
    $model = self::initialize();

    $sql = self::generateSelectQuery($model, $filters);
    $query = self::$db->prepare($sql);
    $query->execute(self::generateSqlValueArray($filters));

    $entities = self::generateEntities($query->fetchAll(), $model);

    return $entities;
  }

  public static function findOne($filters = []) {
    $model = self::initialize();

    $sql = self::generateSelectQuery($model, $filters);
    $sql .= " LIMIT 0, 1";
    $query = self::$db->prepare($sql);
    $query->execute(self::generateSqlValueArray($filters));

    $entities = self::generateEntities($query->fetchAll(), $model);

    if (count($entities) > 0) {
      return $entities[0];
    }

    return null;
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

    self::generateMTMFuncs($model);

    return $model;
  }

  private static function generateMTMFuncs($model) {
    $db = self::$db;

    foreach (self::$cache[$model]->manyToMany as $attr => $toModel) {
      self::$cache[$model]->additionalGetters['get' . ucfirst($attr)] =
        function($id) use ($model, $toModel, $db) {
          $table = self::_modelToSqlName($model);
          $toTable = self::_modelToSqlName($toModel);
          $linkTable = self::_getMTMLinkTableName($toModel);

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
      self::$cache[$model]->additionalSetters['set' . ucfirst($attr)] =
        function($entity, $args) use ($attr) {
          $entity->_mtmToUpdate[$attr] = $args[0];
        };
    }
  }

  public static function _getMTMLinkTableName($toModel) {
    $table = self::_modelToSqlName(get_called_class());
    $toTable = self::_modelToSqlName($toModel);
    $linkTableArr = [$table, $toTable];
    sort($linkTableArr);
    $linkTable = implode('_', $linkTableArr);

    return $linkTable;
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
