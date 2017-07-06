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
		$pwd = $this->input->post('password');
		if (empty(trim($name))){
			echo json_encode(['status' => false]);
			return;
		}
		if (empty(trim($pwd))){
			$pwd = $this->db->select('password')->where('id', $id)->get('restaurant')->result_array();
			$password = $pwd[0]['password'];
		}
		else $password = password_hash($pwd, PASSWORD_BCRYPT);
		$tel = $this->input->post('telephone');
		$addr = $this->input->post('address');
		$pic = $this->input->post('picture');
		$loc = $this->input->post('location');
		$ot = $this->input->post('open_time');
		$ct = $this->input->post('close_time');
		$this->restaurant->updateInfo($id, ['name' => $name, 'password' => $password, 'picture' => $pic, 'telephone' => $tel, 'address' => $addr, 'location' => $loc, 'open_time' => $ot, 'close_time' => $ct]);
		$this->session->set_userdata('rst_name', $name);
		$this->session->set_userdata('rst_loc', $loc);
		echo json_encode(['status' => true]);
	}

	public function listDishes() {
		if (is_null($this->session->userdata('rst_id'))) {
			echo json_encode(['status' => false, 'msg' => 'no user logged in']);
			return;
		}
		$id = $this->session->userdata('rst_id');
		$arr = $this->db->select(['id', 'name', 'picture', 'price', 'discount'])->where('rst_id',$id)->get('menu')->result_array();
		echo json_encode($arr);
	}

	public function updateDishes(){
		if (is_null($this->session->userdata('rst_id'))) {
			echo json_encode(['status' => false, 'msg' => 'no user logged in']);
			return;
		}
		$rst_id = $this->session->userdata('rst_id');
		$d = $this->input->post('dish');
		$dish = json_decode($d, true);
		$id = $dish['id'];
		$this->db->where('rst_id', $rst_id);
		$this->db->where('id', $id);
		$check = $this->db->select('name')->get('menu')->result_array();
		if (empty($check)){
			echo json_encode(['status' => false]);
      		return false;
    	}
		$arr = ['name' => $dish['name'], 'picture' => $dish['picture'], 'discount' => $dish['discount'], 'price' => $dish['price'], 'rst_id' => $rst_id];
		$this->restaurant->updateDishes($id, $arr);
		echo json_encode(['status' => true]);
	}

	public function addDish(){
		if (is_null($this->session->userdata('rst_id'))) {
			echo json_encode(['status' => false, 'msg' => 'no user logged in']);
			return;
		}
		$rst_id = $this->session->userdata('rst_id');
		$d = $this->input->post('dish');
		$dish = json_decode($d, true);
		$dish['rst_id'] = $rst_id;
		$this->restaurant->addDish($dish);
		echo json_encode(['status' => true]);
	}

	public function deleteDish(){
		if (is_null($this->session->userdata('rst_id'))) {
			echo json_encode(['status' => false, 'msg' => 'no user logged in']);
			return;
		}
		$rst_id = $this->session->userdata('rst_id');
		$id = $this->input->post('id');
		$arr = $this->db->select('rst_id')->where('id', $id)->get('menu')->result_array();
    	if ($arr === []){
			echo json_encode(['status' => false, 'msg' => 'no such dish']);
			return;
		}
		if ($arr[0]['rst_id'] != $rst_id){
			echo json_encode(['status' => false, 'msg' => 'incorrect user']);
			return;
		}
		$this->restaurant->deleteDish($id);
		echo json_encode(['status' => true]);
	}

	public function upload() {
		if (is_null($this->session->userdata('rst_id'))) {
			echo json_encode(['status' => false, 'msg' => 'no user logged in']);
			return;
		}

		$config['upload_path']      = './uploads/';
    	$config['allowed_types']    = 'gif|jpg|png';
    	$config['max_size']     = 1024;
    	$config['max_width']        = 1024;
    	$config['max_height']       = 768;
		$config['encrypt_name'] = true;

    	$this->load->library('upload');
		$this->upload->initialize($config);

    if ( ! $this->upload->do_upload('picture'))
    {
        echo json_encode(array('status' => false, 'error' => $this->upload->display_errors('', '')));
    }
    else
    {
			$data = $this->upload->data();
			echo json_encode(['status'=>true, 'url'=>('http://localhost/MeiBaiE/uploads/'.$data['file_name'])]);
    }
	}

  /**
   * Return new orders, reject orders, accept orders
   */
	public function listOrders() {
		if (is_null($this->session->userdata('rst_id'))) {
			echo json_encode(['status' => false, 'msg' => 'no user logged in']);
			return;
		}
		$rst_id = $this->session->userdata('rst_id');
		// $orders = $this->db->select('*')->where('rst_id', $rst_id)->get('orders');
		// echo json_encode($orders);
		$this->db->from('orders');
		$this->db->join('user', 'user.id = orders.user_id');
		$this->db->join('order_menu', 'order_menu.order_id = orders.id');
		$this->db->join('menu', 'menu.id = order_menu.food_id');
		$this->db->where('orders.rst_id', $rst_id);
		$this->db->where('menu.rst_id', $rst_id);
		$this->db->order_by('orders.id', 'DESC');
		$new = [];
		$acc = [];
		$can = [];
		$rej = [];
		$uncom = [];
		$com = [];
		$res = [];
		$dish = [];
		$arr = $this->db->select(['order_id', 'user.name as u_name', 'orders.telephone', 'orders.address', 'orders.time', 'orders.price as total_price', 'postscript', 'comment', 'menu.name', 'order_menu.amount', 'status'])->get()->result_array();
		$this->session->set_userdata('latest_order', $arr[0]['order_id']);
		foreach ($arr as $or){
			$order_id = $or['order_id'];
			$dish[$order_id][] = ['name' => $or['name'], 'amount' => $or['amount']];
		}
		foreach ($arr as $or){
			$order_id = $or['order_id'];
			if ($or['status'] == 0 && @is_null($new[$order_id])){
				$or['dishes'] = $dish[$order_id];
				$new[$order_id] = $or;
				unset($new[$order_id]['name']);
				unset($new[$order_id]['amount']);
				unset($new[$order_id]['status']);
			}
			if ($or['status'] == 1 && @is_null($can[$order_id])){
				$or['dishes'] = $dish[$order_id];
				$can[$order_id] = $or;
				unset($can[$order_id]['name']);
				unset($can[$order_id]['amount']);
				unset($can[$order_id]['status']);
			}
			if ($or['status'] == 2 && @is_null($acc[$order_id])){
				$or['dishes'] = $dish[$order_id];
				$acc[$order_id] = $or;
				unset($acc[$order_id]['name']);
				unset($acc[$order_id]['amount']);
				unset($acc[$order_id]['status']);
			}
			if ($or['status'] == 3 && @is_null($rej[$order_id])){
				$or['dishes'] = $dish[$order_id];
				$rej[$order_id] = $or;
				unset($rej[$order_id]['name']);
				unset($rej[$order_id]['amount']);
				unset($rej[$order_id]['status']);
			}
			if ($or['status'] == 4 && @is_null($uncom[$order_id])){
				$or['dishes'] = $dish[$order_id];
				$uncom[$order_id] = $or;
				unset($uncom[$order_id]['name']);
				unset($uncom[$order_id]['amount']);
				unset($uncom[$order_id]['status']);
			}
			if ($or['status'] == 5 && @is_null($com[$order_id])){
				$or['dishes'] = $dish[$order_id];
				$com[$order_id] = $or;
				unset($com[$order_id]['name']);
				unset($com[$order_id]['amount']);
				unset($com[$order_id]['status']);
			}
		}
		$res['new'] = [];
		$res['canceled'] = [];
		$res['accepted'] = [];
		$res['rejected'] = [];
		$res['uncommented'] = [];
		$res['completed'] = [];
		foreach ($new as $i){
			$res['new'][] = $i;
		}
		foreach ($can as $i){
			$res['canceled'][] = $i;
		}
		foreach ($acc as $i){
			$res['accepted'][] = $i;
		}
		foreach ($rej as $i){
			$res['rejected'][] = $i;
		}
		foreach ($uncom as $i){
			$res['uncommented'][] = $i;
		}
		foreach ($com as $i){
			$res['completed'][] = $i;
		}
		echo json_encode($res);
	}

	public function acceptOrder() {
		if (is_null($this->session->userdata('rst_id'))) {
			echo json_encode(['status' => false, 'msg' => 'no user logged in']);
			return;
		}
		$rst_id = $this->session->userdata('rst_id');
		$id = $this->input->post('id');
		$arr = $this->db->select(['rst_id', 'status'])->where('id', $id)->get('orders')->result_array();
    	if ($arr === []){
			echo json_encode(['status' => false, 'msg' => 'no such order']);
			return;
		}
		if ($arr[0]['rst_id'] != $rst_id){
			echo json_encode(['status' => false, 'msg' => 'incorrect user']);
			return;
		}
		if ($arr[0]['status'] != 0){
			echo json_encode(['status' => false, 'msg' => 'order cannot be accepted']);
			return;
		}
		$this->restaurant->changeOrder($id, 2);
		echo json_encode(['status' => true]);
  	}

	public function rejectOrder() {
		if (is_null($this->session->userdata('rst_id'))) {
			echo json_encode(['status' => false, 'msg' => 'no user logged in']);
			return;
		}
		$rst_id = $this->session->userdata('rst_id');
		$id = $this->input->post('id');
		$arr = $this->db->select(['rst_id', 'status'])->where('id', $id)->get('orders')->result_array();
    	if ($arr === []){
			echo json_encode(['status' => false, 'msg' => 'no such order']);
			return;
		}
		if ($arr[0]['rst_id'] != $rst_id){
			echo json_encode(['status' => false, 'msg' => 'incorrect user']);
			return;
		}
		if ($arr[0]['status'] != 0){
			echo json_encode(['status' => false, 'msg' => 'order cannot be rejected']);
			return;
		}
		$this->restaurant->changeOrder($id, 3);
		echo json_encode(['status' => true]);
  	}

	public function completeOrder() {
		if (is_null($this->session->userdata('rst_id'))) {
			echo json_encode(['status' => false, 'msg' => 'no user logged in']);
			return;
		}
		$rst_id = $this->session->userdata('rst_id');
		$id = $this->input->post('id');
		$arr = $this->db->select(['rst_id', 'status'])->where('id', $id)->get('orders')->result_array();
    	if ($arr === []){
			echo json_encode(['status' => false, 'msg' => 'no such order']);
			return;
		}
		if ($arr[0]['rst_id'] != $rst_id){
			echo json_encode(['status' => false, 'msg' => 'incorrect user']);
			return;
		}
		if ($arr[0]['status'] != 2){
			echo json_encode(['status' => false, 'msg' => 'order cannot be completed']);
			return;
		}
		$this->restaurant->changeOrder($id, 4);
		echo json_encode(['status' => true]);
  	}

  /**
   * poll this method to check if there are new orders
   */
	public function checkNewOrders() {
		if (is_null($this->session->userdata('rst_id'))) {
			echo json_encode(['status' => false, 'msg' => 'no user logged in']);
			return;
		}
		$rst_id = $this->session->userdata('rst_id');
		$this->db->from('orders');
		$this->db->join('user', 'user.id = orders.user_id');
		$this->db->join('order_menu', 'order_menu.order_id = orders.id');
		$this->db->join('menu', 'menu.id = order_menu.food_id');
		$this->db->where('orders.rst_id', $rst_id);
		$this->db->where('menu.rst_id', $rst_id);
		$this->db->order_by('orders.id', 'DESC');
		$arr = $this->db->select(['order_id', 'user.name as u_name', 'orders.telephone', 'orders.address', 'orders.time', 'orders.price as total_price', 'postscript', 'comment', 'menu.name', 'order_menu.amount', 'status'])->get()->result_array();
		$res = [];
		foreach ($arr as $or){
			$order_id = $or['order_id'];
			if ($order_id > $this->session->userdata('latest_order')){
				$dish[$order_id][] = ['name' => $or['name'], 'amount' => $or['amount']];
			}else break;
		}
		
	}
}
