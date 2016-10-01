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

  function __construct() {
  }

  public function getId() {
    return $this->id;
  }

  public function setName($name) {
    $this->name = $name;
  }

  public function setMail($mail) {
    $this->mail = $mail;
  }
}
