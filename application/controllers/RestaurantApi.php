<?php

class RestaurantApi extends BaseApi {
  
	public function login() {
    parent::login('restaurant');
	}

	public function register() {
    parent::register('restaurant');
	}

	public function getInfo() {
		if (is_null($this->session['user'])) {
			echo json_encode(['status' => false, 'msg' => 'no user logged in']);
			return;
		}

	}

	public function updateInfo() {

	}

  /**
   * Return new orders, reject orders, accept orders
   */
	public function listOrders() {

	}

  /**
   * poll this method to check if there are new orders
   */
	public function checkNewOrders() {

	}
}
