<?php

namespace core;

class ModelCache {
  /**
   * Table name corresponding to the model
   * @var string
   */
  public $table             = null;

  /**
   * Attributes of the model
   * @var array
   */
  public $properties        = [];

  /**
   * Many to many relationships of the model
   * @var array
   */
  public $manyToMany        = [];

  /**
   * List of anonymous functions dynamically added to the model, representing getters
   * @var array
   */
  public $additionalGetters = [];

  /**
   * List of anonymous functions dynamically added to the model, representing setters
   * @var array
   */
  public $additionalSetters = [];
}
