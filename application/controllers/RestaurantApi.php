<?php

require_once(__DIR__.'./BaseApi.php');

class RestaurantApi extends BaseApi {
  
	public function __construct() {
		parent::__construct();
		$this->type = 'restaurant';
	}

	public function login() {
    	if (!is_null($this->session->userdata('user_id')) || !is_null($this->session->userdata('rst_id'))) {
			echo json_encode(['status' => false, 'msg' => 'Please first log out']);
			return;
		}
		$acc = $this->input->post('name');
		$pwd = $this->input->post('password');
		$res = $this->restaurant->login($acc, $pwd);
		if ($res){
			$id = $this->db->select(['id', 'location'])->where('name', $acc)->get('restaurant')->result_array();
			$this->session->set_userdata('rst_id', $id[0]['id']);
			$this->session->set_userdata('rst_name', $acc);
			$this->session->set_userdata('rst_loc', $id[0]['location']);
			$info = $this->db->select(['name', 'telephone', 'address', 'picture', 'location', 'open_time', 'close_time'])->where('id',$id[0]['id'])->get('restaurant')->result_array();
			echo json_encode(['status' => true, 'rstdata' => $info[0]]);
		}else echo json_encode(['status' => false]);
	}

	public function logout(){
		$this->session->set_userdata('rst_id', null);
		$this->session->set_userdata('rst_name', null);
		$this->session->set_userdata('rst_loc', null);
		echo json_encode(['status' => true]);
	}

	public function register() {
    	$acc = $this->input->post('name');
		$pwd = $this->input->post('password');
		if (empty(trim($acc)) || empty(trim($pwd))){
			echo json_encode(['status' => false]);
			return;
		}
		$tel = $this->input->post('telephone');
		$addr = $this->input->post('address');
		$loc = $this->input->post('location');
		$ot = $this->input->post('open_time');
		$ct = $this->input->post('close_time');
		$password = password_hash($pwd,PASSWORD_BCRYPT);
		$arr = ['name' => $acc, 'password' => $password, 'telephone' => $tel, 'address' => $addr, 'location' => $loc, 'open_time' => $ot, 'close_time' => $ct];
		$reg = $this->restaurant->register($arr);
		if (!$reg) {
			echo json_encode(['status' => false, 'msg' => 'account already exists']);
			return;
		}
		$id = $this->db->select(['id', 'location'])->where('name', $acc)->get('restaurant')->result_array();
		$this->session->set_userdata('rst_id', $id[0]['id']);
		$this->session->set_userdata('rst_loc', $id[0]['location']);
		$this->session->set_userdata('rst_name', $acc);
		echo json_encode(['status' => true]);
	}

	public function getInfo() {
		if (is_null($this->session->userdata('rst_id'))) {
			echo json_encode(['status' => false, 'msg' => 'no user logged in']);
			return;
		}
		$id = $this->session->userdata('rst_id');
		$arr = $this->db->select(['name', 'telephone', 'address', 'picture', 'location', 'open_time', 'close_time'])->where('id',$id)->get('restaurant')->result_array();
		echo json_encode($arr[0]);
	}

	public function updateInfo() {
		if (is_null($this->session->userdata('rst_id'))) {
			echo json_encode(['status' => false, 'msg' => 'no user logged in']);
			return;
		}
		$id = $this->session->userdata('rst_id');
		$name = $this->input->post('name');
		$tel = $this->input->post('telephone');
		$addr = $this->input->post('address');
		$loc = $this->input->post('location');
		$ot = $this->input->post('open_time');
		$ct = $this->input->post('close_time');
		$this->restaurant->updateInfo($id, ['name' => $name, 'telephone' => $tel, 'address' => $addr, 'location' => $loc, 'open_time' => $ot, 'close_time' => $ct]);
		$this->session->set_userdata('rst_name', $name);
		echo json_encode(['status' => true]);
	}

	public function listDishes() {
		if (is_null($this->session->userdata('rst_id'))) {
			echo json_encode(['status' => false, 'msg' => 'no user logged in']);
			return;
		}
		$id = $this->session->userdata('rst_id');
		$arr = $this->db->select(['name', 'picture', 'price', 'discount'])->where('rst_id',$id)->get('menu')->result_array();
		echo json_encode($arr);
	}

  /**
   * Return new orders, reject orders, accept orders
   */
	public function listOrders() {
		if (is_null($this->session->userdata('rst_id'))) {
			echo json_encode(['status' => false, 'msg' => 'no user logged in']);
			return;
		}''
		$id = $this->session->userdata('rst_id');
		$arr = $this->db->select(['user_id', 'name', 'picture', 'price', 'discount'])->where('rst_id',$id)->get('orders')->result_array();
		
	}

  /**
   * poll this method to check if there are new orders
   */
	public function checkNewOrders() {

	}
}
