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

  public function updateInfo($id, $arr) {
    $this->db->where('id', $id);
    $this->db->update('user', $arr);
  }

  public function register($arr) {
    $name = $this->db->select('name')->get('user')->result_array();
    foreach ($name as $n){
      if ($n['name']==$arr['name']) return false;
    }
    $this->db->insert('user',$arr);
    return true;
  }

  public function login($acc,$pwd) {
    $hash=$this->db->select('password')->where('name',$acc)->get('user')->result_array();
    if(empty($hash)){
      return false;
    }
    if(password_verify($pwd,$hash[0]['password'])){
      return true;
    }else return false;
   }
/*
  public function logout() {

  }
*/
  public function createOrder($user,$rst,$tel,$addr,$time,$pst,$arr) {
    $total = 0.0;
    foreach ($arr as $food_id=>$amount) {
      $price = $this->db->select(['price','discount'])
          ->where('id', $food_id)
          ->get('menu');
      $price = $price->result_array();
      $total += floatval($price[0]['price']) *floatval($price[0]['discount'])* $amount;
    }
    // $order=['customer_id'= >$user->id,'telephone'=>$tel,'address'=>$user->addr,'time'=>$time,'price'=>$total,'postscript'=>$pst,'status'=>0];
      $order=['user_id'=>$user,'rst_id'=>$rst,'telephone'=>$tel,'address'=>$addr,'time'=>date('Y-m-d H:i:s', $time),'price'=>$total,'postscript'=>$pst,'status'=>0];
     if(!$this->db->insert('orders',$order)){
       return false;
     }
    $order_id=$this->db->insert_id();
    foreach ($arr as $food_id=>$amount){
      $this->db->insert('order_menu',['order_id'=>$order_id,'amount'=>$amount,'food_id'=>$food_id]);
    }
  }

  public function commentOrder($id, $text){
    $this->db->where('id', $id);
    $this->db->update('orders', ['comment'=> $text, 'status' => 5]);
  }

  public function cancelOrder($id) {
    $this->db->where('id', $id);
    $this->db->update('orders', ['status' => 1]);
  }
}