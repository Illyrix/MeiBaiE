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
  public function createOrder($user,$rst,$tel,$time,$arr) {
//    if(!(in_array($user,$this->db->get('user')))||!(in_array($rst,$this->db->get('restaurant'))))
//      return [false,'user or restaurant error'];
    $total = 0.0;
    foreach ($arr as $id=>$amount) {
      $price = $this->db->select('price')
          ->where('id', $id)
          ->get('menu');
      $total += $price * $amount;
    }
    $order=['custer_id'=>$user->id,'telephone'=>$tel,$user->addr,$time,]
    $this->db->insert('order',)
  }

  public function cancelOrder($order) {
    
  }

  public function search($str) {

  }
}