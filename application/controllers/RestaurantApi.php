<?php

require_once(__DIR__.'./BaseApi.php');

class RestaurantApi extends BaseApi {
  
	public function __construct() {
		parent::__construct();
		$this->type = 'restaurant';
	}

	public function login() {
    parent::login('restaurant');
	}

	public function register() {
    parent::register('restaurant');
	}

	public function getInfo() {
		// if (is_null($this->session['restaurant'])) {
		// 	echo json_encode(['status' => false, 'msg' => 'no user logged in']);
		// 	return;
		// }
		$id = $this->input->post('id');
		$arr = $this->db->select(['name', 'telephone', 'address', 'picture', 'location', 'open_time', 'close_time'])->where('id',$id)->get('restaurant')->result_array();
		echo json_encode($arr[0]);
	}

	public function updateInfo() {
		$id = $this->input->post('id');
		$name = $this->input->post('name');
		$tel = $this->input->post('telephone');
		$addr = $this->input->post('address');
		$loc = $this->input->post('location');
		$ot = $this->input->post('open_time');
		$ct = $this->input->post('close_time');
		$this->restaurant->updateInfo($id, ['name' => $name, 'telephone' => $tel, 'address' => $addr, 'location' => $loc, 'open_time' => $ot, 'close_time' => $ct]);
		echo json_encode(['status' => true]);
	}

	public function listDishes() {
		$id = $this->input->post('id');
		$arr = $this->db->select(['name', 'picture', 'price', 'discount'])->where('rst_id',$id)->get('menu')->result_array();
		echo json_encode($arr);
	}

  /**
   * Return new orders, reject orders, accept orders
   */
	public function listOrders() {
		$id = $this->input->post('id');
	}

  /**
   * poll this method to check if there are new orders
   */
	public function checkNewOrders() {

	}
}
