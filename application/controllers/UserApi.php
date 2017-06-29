<?php

require_once(__DIR__.'./BaseApi.php');

class UserApi extends BaseApi {

	public function __construct() {
		parent::__construct();
		$this->type = 'user';
	}

	public function login() {
    parent::login('user');
	}

	public function register() {
    parent::register('user');
	}

	public function getInfo() {
		if (is_null($this->session['user'])) {
			echo json_encode(['status' => false, 'msg' => 'no user logged in']);
			return;
		}

	}

	/**
	 * Return new orders and history orders
	 */
	public function listOrders() {
	}

	public function updateInfo() {

	}

	public function commentOrder() {
		
	}

	public function createOrder() {
		// $rst = $this->input->post('rstid');
		// $arr = json_decode($this->input->post('dishes'), true);
		// $pst=$this->input->post('postscript');
		// $user= $this->session['user']['userid'];
		 $time=time();
		// $info=$this->user->createOrder($user,$rst,$time,$pst,$arr);
        $info=$this->user->createOrder(3,2,19216800111,$time,'恶狠狠地放辣椒',[6=>3,8=>1,9=>2,12=>3]);
	}

	public function cancelOrder() {

	}

	public function listDishes() {
		if (is_null($this->session['user'])) {
			echo json_encode(['status' => false, 'msg' => 'please log in first']);
			return;
		}
	}
}
