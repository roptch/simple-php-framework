<?php

namespace app\models;

use core\Model;

class Track extends Model {
  /**
   * Track identifier
   * @var int
   */
  protected $id = null;

  /**
   * Track name
   * @var string
   */
  protected $name;

  /**
   * Track duration
   * @var int
   */
  protected $duration;

  /**
   * Many to many relations
   * @var array
   */
  protected $_manyToMany = [
    'lovedBy' => 'app\\models\\User'
  ];

  /**
   * Returns an array containing the data to be json encoded
   * @return array
   */
  public function getJsonFormatted() {
    return [
      'id'        => $this->getId(),
      'name'      => $this->getName(),
      'duration'  => $this->getDuration()
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
   * @return Track
   */
  public function setName($name) {
    $this->name = $name;
    return $this;
  }

  /**
   * Duration getter
   * @return int
   */
  public function getDuration() {
    return $this->duration;
  }

  /**
   * Duration setter
   * @param int $duration
   * @return Track
   */
  public function setDuration($duration) {
    $this->duration = $duration;
    return $this;
  }
}
