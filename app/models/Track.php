<?php

namespace app\models;

use core\Model;

class Track extends Model {
  protected $id = null;
  protected $name;
  protected $duration;

  protected $_manyToMany = [
    'lovedBy' => 'app\\models\\User'
  ];

  function __construct() {
  }

  public function getId() {
    return $this->id;
  }
}
