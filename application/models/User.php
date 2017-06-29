<?php

class User extends CI_Model {

  private $id;

  private $name;

  private $phone;

  private $location;

  private $address;

  private $history_orders;

  private $active_orders;

  public function __get($name) {
    if (@!isset($this->$name)) {
      return parent::__get($name);
    }
    else
      return $this->$name;
  }

  public function updateInfo($arr) {

  }

  public function register($acc, $pwd) {
    
  }

  public function login($acc, $pwd) {
    if(!in_array($acc,$this->db->select('id')->get('user'))){
      return false;
    }
    $password=$this->db->select('password')->where('id',$acc)->get('user');
    if($password==$pwd){
      return true;
    }else return false;
  }
/*
  public function logout() {

  }
*/
  public function createOrder($user,$rst,$tel,$time,$pst,$arr) {
//    if(!(in_array($user,$this->db->get('user')))||!(in_array($rst,$this->db->get('restaurant'))))
//      return [false,'user or restaurant error'];
    $total = 0.0;
    foreach ($arr as $food_id=>$amount) {
      $price = $this->db->select(['price','discount'])
          ->where('id', $food_id)
          ->get('menu');
      $price = $price->result_array();
      $total += floatval($price[0]['price']) *floatval($price[0]['discount'])* $amount;
    }
    // $order=['customer_id'= >$user->id,'telephone'=>$tel,'address'=>$user->addr,'time'=>$time,'price'=>$total,'postscript'=>$pst,'status'=>0];
      $order=['user_id'=>$user,'rst_id'=>$rst,'telephone'=>$tel,'address'=>'aaaaaa','time'=>date('Y-m-d H:i:s', $time),'price'=>$total,'postscript'=>$pst,'status'=>0];
    if(!$this->db->insert('orders',$order)){
      return false;
    }
    $order_id=$this->db->insert_id();
    foreach ($arr as $food_id=>$amount){
      $this->db->insert('order_menu',['order_id'=>$order_id,'amount'=>$amount,'food_id'=>$food_id]);
    }
  }
  
  public function cancelOrder($order) {
    
  }

  public function search($str) {

  }
}