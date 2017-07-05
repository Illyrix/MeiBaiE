<?php

require_once(__DIR__.'./BaseApi.php');

class UserApi extends BaseApi {

	public function __construct() {
		parent::__construct();
		$this->type = 'user';
	}

	// public function verifyUser(){
	// 	if (is_null($this->session['user'])) 
	// 		echo json_encode(['status' => false, 'msg' => 'no user logged in']);
	// 	return $this->session['user']['user_id'];
	// }

	public function login() {
    	// parent::login('user');
		if (!is_null($this->session->userdata('user_id')) || !is_null($this->session->userdata('rst_id'))) {
			echo json_encode(['status' => false, 'msg' => 'Please first log out']);
			return;
		}
		$acc = $this->input->post('name');
		$pwd = $this->input->post('password');
		$res = $this->user->login($acc, $pwd);
		if ($res){
			$id = $this->db->select(['id', 'location'])->where('name', $acc)->get('user')->result_array();
			$this->session->set_userdata('user_id', $id[0]['id']);
			$this->session->set_userdata('user_loc', $id[0]['location']);
			$this->session->set_userdata('user_name', $acc);
			$info = $this->db->select(['name', 'gender', 'telephone', 'address', 'e-mail', 'time', 'location'])->where('id',$id[0]['id'])->get('user')->result_array();
			echo json_encode(['status' => true, 'userdata' => $info[0]]);
		}else echo json_encode(['status' => false]);
	}

	public function logout(){
		$this->session->set_userdata('user_id', null);
		$this->session->set_userdata('user_name', null);
		$this->session->set_userdata('user_loc', null);
		echo json_encode(['status' => true]);
	}

	public function register() {
    	//parent::register('user');
		$pwd = $this->input->post('password');
		$acc = $this->input->post('name');
		if (empty(trim($acc)) || empty(trim($pwd))){
			echo json_encode(['status' => false]);
			return;
		}
		$gen = $this->input->post('gender');
		$tel = $this->input->post('telephone');
		$addr = $this->input->post('address');
		$mail = $this->input->post('e-mail');
		$loc = $this->input->post('location');
		$password = password_hash($pwd,PASSWORD_BCRYPT);
		$time = time();
		$arr = ['name' => $acc, 'password' => $password, 'gender' => $gen, 'telephone' => $tel, 'address' => $addr, 'e-mail' => $mail, 'time' => date('Y-m-d H:i:s', $time), 'location' => $loc];
		$reg = $this->user->register($arr);
		if (!$reg) {
			echo json_encode(['status' => false, 'msg' => 'account already exists']);
			return;
		}
		$id = $this->db->select(['id', 'location'])->where('name', $acc)->get('user')->result_array();
		$this->session->set_userdata('user_id', $id[0]['id']);
		$this->session->set_userdata('user_loc', $id[0]['location']);
		$this->session->set_userdata('user_name', $acc);
		echo json_encode(['status' => true]);
	}

	public function getInfo() {
		if (is_null($this->session->userdata('user_id'))) {
			echo json_encode(['status' => false, 'msg' => 'no user logged in']);
			return;
		}
		$id = $this->session->userdata('user_id');
		$arr = $this->db->select(['name', 'gender', 'telephone', 'address', 'e-mail', 'time', 'location'])->where('id',$id)->get('user')->result_array();
		echo json_encode($arr[0]);
	}

	/**
	 * Return new orders and history orders
	 */
	public function listOrders() {
		if (is_null($this->session->userdata('user_id'))) {
			echo json_encode(['status' => false, 'msg' => 'no user logged in']);
			return;
		}
		$id = $this->session->userdata('user_id');
		$arr = $this->db->select(['id', 'rst_id', 'telephone', 'address', 'time', 'price', 'postscript', 'comment', 'status'])->where('user_id', $id)->order_by('time', 'DESC')->get('orders')->result_array();
		foreach($arr as $key => $val){
			$rst = $this->db->select(['name', 'picture', 'telephone'])->where('id', $arr[$key]['rst_id'])->get('restaurant')->result_array();
			$arr[$key]['name'] = $rst[0]['name'];
			$arr[$key]['picture'] = $rst[0]['picture'];
			$arr[$key]['rst_tel'] = $rst[0]['telephone'];
		}
		echo json_encode($arr);
	}

	public function updateInfo() {
		if (is_null($this->session->userdata('user_id'))) {
			echo json_encode(['status' => false, 'msg' => 'no user logged in']);
			return;
		}
		$id = $this->session->userdata('user_id');
		$name = $this->input->post('name');
		$gen = $this->input->post('gender');
		$tel = $this->input->post('telephone');
		$addr = $this->input->post('address');
		$mail = $this->input->post('e-mail');
		$loc = $this->input->post('location');
		$this->user->updateInfo($id, ['name' => $name, 'gender' => $gen, 'telephone' => $tel, 'address' => $addr, 'e-mail' => $mail, 'location' => $loc]);
		$this->session->set_userdata('user_name', $name);
		$this->session->set_userdata('user_loc', $loc);
		echo json_encode(['status' => true]);
	}

	public function listRestaurants() {
		$arr = $this->db->select(['name', 'telephone', 'address', 'picture', 'open_time', 'close_time'])->get('restaurant')->result_array();
		echo json_encode($arr);
	}

	public function commentOrder() {
		if (is_null($this->session->userdata('user_id'))) {
			echo json_encode(['status' => false, 'msg' => 'no user logged in']);
			return;
		}
		$user_id = $this->session->userdata('user_id');
		$id = $this->input->post('id');
		$text = $this->input->post('comment');
		$arr = $this->db->select(['user_id', 'status'])->where('id', $id)->get('orders')->result_array();
    	if ($arr === []){
			echo json_encode(['status' => false, 'msg' => 'no such order']);
			return;
		}
		if ($arr[0]['user_id'] != $user_id){
			echo json_encode(['status' => false, 'msg' => 'incorrect user']);
			return;
		}
		if ($arr[0]['status'] != 4){
			echo json_encode(['status' => false, 'msg' => 'not a completed order']);
			return;
		}
		$this->user->commentOrder($id, $text);
		echo json_encode(['status' => true]);
	}

	public function createOrder() {
		if (is_null($this->session->userdata('user_id'))) {
			echo json_encode(['status' => false, 'msg' => 'no user logged in']);
			return;
		}
		$user = $this->session->userdata('user_id');
		$user = $this->input->post('user_id');
		$rst = $this->input->post('rst_id');
		$tel = $this->input->post('telephone');
		$addr = $this->input->post('address');
		$time = time();
		$pst = $this->input->post('postscript');
		$arr = json_decode($this->input->post('dishes'), true);
		$info = $this->user->createOrder($user, $rst, $tel, $addr, $time, $pst, $arr);
		// if (!$info) {
		// 	echo json_encSode(['status' => false]);
		// 	return;
		// }
		echo json_encode(['status' => true]);
		
	}

	public function cancelOrder() {
		if (is_null($this->session->userdata('user_id'))) {
			echo json_encode(['status' => false, 'msg' => 'no user logged in']);
			return;
		}
		$user_id = $this->session->userdata('user_id');
		$id = $this->input->post('id');
		$arr = $this->db->select(['user_id', 'status'])->where('id', $id)->get('orders')->result_array();
    	if ($arr === []){
			echo json_encode(['status' => false, 'msg' => 'no such order']);
			return;
		}
		if ($arr[0]['user_id'] != $user_id){
			echo json_encode(['status' => false, 'msg' => 'incorrect user']);
			return;
		}
		if ($arr[0]['status'] != 0){
			echo json_encode(['status' => false, 'msg' => 'order cannot be canceled']);
			return;
		}
		$this->user->cancelOrder($id);
		echo json_encode(['status' => true]);
	}

	public function listDishes() {
		// if (is_null($this->session['user'])) {
		// 	echo json_encode(['status' => false, 'msg' => 'no user logged in']);
		// 	return;
		// }
		$rst = $this->input->post('rst_id');
		$arr = $this->db->select(['name', 'picture', 'discount', 'price'])->where('rst_id', $rst)->get('menu')->result_array();
		echo json_encode($arr);
	}

	public function search(){
		$str = $this->input->post('string');
		$id_f = $this->db->select('rst_id')->like('name', $str)->get('menu')->result_array();
		$rst_f = [];
		foreach($id_f as $id){
			$f = $this->db->select(['name', 'telephone', 'address', 'picture', 'open_time', 'close_time'])->where('id', $id)->get('restaurant')->result_array();
			$rst_f = array_merge($rst_f, $f);
		}
		$rst = $this->db->select(['name', 'telephone', 'address', 'picture', 'open_time', 'close_time'])->like('name', $str)->get('restaurant')->result_array();
		$arr['rst'] = $rst;
		$arr['food'] = $rst_f;
		echo json_encode($arr);
	}
}
