<?php

namespace app\models;

use core\Model;

class User extends Model {
  protected $id = null;
  protected $name;
  protected $mail;

  protected $_manyToMany = [
    'loved' => 'app\\models\\Track'
  ];

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

  public function getMail() {
    return $this->mail;
  }

  public function setMail($mail) {
    $this->mail = $mail;
    return $this;
  }
}
