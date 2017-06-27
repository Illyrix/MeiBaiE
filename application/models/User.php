<?php

class User {
  private $id;

  private $name;

  private $phone;

  private $location;

  private $address;

  private $history_orders;

  private $active_orders;

  public function __get($name) {
    return $this->$name;
  }

  public function updateInfo($arr) {

  }

  public function register($type, $acc, $pwd) {

  }

  public function login($type, $acc, $pwd) {

  }
/*
  public function logout() {

  }
*/
  public function createOrder() {

  }

  public function cancelOrder($order) {
    
  }
}