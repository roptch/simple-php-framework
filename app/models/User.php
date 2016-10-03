<?php

namespace app\models;

use core\Model;

class User extends Model {
  /**
   * User identifier
   * @var int
   */
  protected $id = null;

  /**
   * User name
   * @var string
   */
  protected $name;

  /**
   * User mail
   * @var string
   */
  protected $mail;

  /**
   * Many to many relations
   * @var array
   */
  protected $_manyToMany = [
    'loved' => 'app\\models\\Track'
  ];

  /**
   * Returns an array containing the data to be json encoded
   * @return array
   */
  public function getJsonFormatted() {
    return [
      'id'    => $this->getId(),
      'name'  => $this->getName(),
      'mail'  => $this->getMail()
    ];
  }

  /**
   * Id getter
   * @return int
   */
  public function getId() {
    return $this->id;
  }

  /**
   * Name getter
   * @return string
   */
  public function getName() {
    return $this->name;
  }

  /**
   * Name setter
   * @param string $name
   * @return User
   */
  public function setName($name) {
    $this->name = $name;
    return $this;
  }

  /**
   * Mail getter
   * @return string
   */
  public function getMail() {
    return $this->mail;
  }

  /**
   * Mail setter
   * @param string $mail
   * @return User
   */
  public function setMail($mail) {
    $this->mail = $mail;
    return $this;
  }
}
