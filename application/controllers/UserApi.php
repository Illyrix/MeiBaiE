<?php

class UserApi extends BaseApi {

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
