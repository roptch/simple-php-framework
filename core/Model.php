<?php

namespace core;

use core\Configuration;
use core\ModelCache;
use core\AppException;

abstract class Model {
  /**
   * PDO connection
   * @var \PDO
   */
  private static $db = null;

  /**
   * Model cached data
   * @var array
   */
  private static $cache = [];

  /**
   * Many to many relations to update in the next save()
   * @var array
   */
  public $_mtmToUpdate = [];

  abstract public function getId();
  private function setId($id) {}

  /**
   * Used to call the dynamically added methods (many2many getters/setters)
   * @param  string $method Method name
   * @param  array $args   Arguments to pass
   * @return mixed
   */
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
      throw new AppException('Calling undefined method [' . $method . '] in model [' . $model . ']');
    }
  }

  /**
   * Deletes the entity from the database
   */
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

  /**
   * Deletes many to many relation links of the entity
   * @param  string $model Current model name of the entity
   */
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

  /**
   * Saves an entity to db. If it doesn't exist, creates it
   */
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

  /**
   * Deletes all many to many relation links and creates the ones needed
   * @param  string $model Current model name of the entity
   */
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

      $values = [];
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

  /**
   * Retrieves the entities using the filters
   * @param  array $filters Filters. It's a 2 dimensional array (first dimension: column names, second dimension: values)
   * @return array          Array of entities
   */
  public static function find($filters = []) {
    $model = self::initialize();

    $sql = self::generateSelectQuery($model, $filters);
    $query = self::$db->prepare($sql);
    $query->execute(self::generateSqlValueArray($filters));

    $entities = self::generateEntities($query->fetchAll(), $model);

    return $entities;
  }

  /**
   * Retrieves only one entity using the filters
   * @param  array $filters Filters. It's a 2 dimensional array (first dimension: column names, second dimension: values)
   * @return Model|null     Either the found entity or null if nothing is found
   */
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

  /**
   * Using the filters, generate the sql query to retrives entities from the db
   * @param  string $model Name of the model we are searching
   * @param  array  $args  Filters
   * @return string        Sql query
   */
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

  /**
   * Builds a list containing all the values contained in the filters
   * @param  array $filters
   * @return array
   */
  private static function generateSqlValueArray($filters) {
    $result = [];

    foreach ($filters as $column => $values) {
      foreach ($values as $value) {
        $result[] = $value;
      }
    }

    return $result;
  }

  /**
   * Given the results of the sql query, creates the corresponding entities
   * @param  array $sqlResults Results given by \PDO::fetchAll
   * @param  string $model     Name of the model we are using
   * @return array             Array of resulting entities
   */
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

  /**
   * Caching data concerning the model used, generating many to many function, connecting to db
   */
  private static function initialize() {
    $model = get_called_class();

    if (self::$db === null) {
      $config = Configuration::$config;

      if (!isset($config->database->user) || !isset($config->database->password)
          || !isset($config->database->dbname) || !isset($config->database->host)) {
        throw new AppException('Missing database configuration attribute');
      }

      self::$db = new \PDO('mysql:host=' . $config->database->host . ';dbname='
                  . $config->database->dbname . ';charset=utf8',
                  $config->database->user, $config->database->password);
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

  /**
   * Generates one getter and one setter for each many to many relationships
   * @param  string $model Name of the model we are using
   */
  private static function generateMTMFuncs($model) {
    $db = self::$db;

    foreach (self::$cache[$model]->manyToMany as $attr => $toModel) {
      self::$cache[$model]->additionalGetters['get' . ucfirst($attr)] =
        function($id) use ($model, $toModel, $db) { // M2M Getter
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
        function($entity, $args) use ($attr) { // M2M Setter
          // Caching the list of related entities, it will be updated in db on the next save()
          $entity->_mtmToUpdate[$attr] = $args[0];
        };
    }
  }

  /**
   * Computes the name of the many to many links table
   * It is the names of the 2 tables, ordered alphabetically and splitted by an underscore
   * @param  string $toModel Target model of the relationship
   * @return string          Name of the link table
   */
  public static function _getMTMLinkTableName($toModel) {
    $table = self::_modelToSqlName(get_called_class());
    $toTable = self::_modelToSqlName($toModel);
    $linkTableArr = [$table, $toTable];
    sort($linkTableArr);
    $linkTable = implode('_', $linkTableArr);

    return $linkTable;
  }

  /**
   * Computes the name of a table/column from the name of a model/attribute
   * It is lowercase and each part is splitted by underscores
   * The name of a model is CamelCase
   * The name of an attribute is camelCase
   * @param  string $model Model or attribute name
   * @return string        Table or column name
   */
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
