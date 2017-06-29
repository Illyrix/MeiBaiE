<?php

class Restaurant {
  private $id;

  private $name;

  private $location;

  private $open_time;

  private $close_time;

  private $dishes;

  private $accepted_orders;

  private $new_orders;

  private $reject_orders;

  public function updateInfo($arr) {

  }

  public function updateDishes($dishes) {
    
  }

  public function __get($name) {
    if (@!isset($this->$name)) {
      return parent::__get($name);
    }
    else
      return $this->$name;
  }

  public function acceptOrder($order) {

  }

}