<?php

class Restaurant extends CI_Model{
  private $id;

  private $name;

  private $location;

  private $open_time;

  private $close_time;

  private $dishes;

  private $accepted_orders;

  private $new_orders;

  private $reject_orders;

  public function login($acc,$pwd) {
    $hash=$this->db->select('password')->where('name',$acc)->get('restaurant')->result_array();
    if(empty($hash)){
      return false;
    }
    if(password_verify($pwd,$hash[0]['password'])){
      return true;
    }else return false;
   }

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