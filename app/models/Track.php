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

  public function getJsonFormatted() {
    return [
      'id'        => $this->getId(),
      'name'      => $this->getName(),
      'duration'  => $this->getDuration()
    ];
  }

  public function getId() {
    return $this->id;
  }

  public function getName() {
    return $this->name;
  }

  public function setName($name) {
    $this->name = $name;
    return $this;
  }

  public function getDuration() {
    return $this->duration;
  }

  public function setDuration($duration) {
    $this->duration = $duration;
    return $this;
  }
}
