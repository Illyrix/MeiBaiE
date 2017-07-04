<?php

abstract class BaseApi extends CI_Controller {
  public function __construct() {
		parent::__construct();
		date_default_timezone_set('Asia/Shanghai');
		$this->load->library('session');
		$this->load->database();
		$this->load->model('user', '', true);
    	$this->load->model('restaurant', '', true);
	}

  /**
	 * Return user info as a JSON string
	 */
	public function getLogged() {
		if (is_null($this->session['user'])) 
			echo json_encode(['status' => false]);
		else
			echo json_encode([
				'status' => true, 
				'type' => $this->session['user']['type'],
				'name' => $this->session['user']['name']
			]);
	}

  /**
	 * Log in
	 */
	public function login() {

		if (!is_null($this->session['user'])) {
			echo json_encode(['status' => false, 'msg' => 'already login']);
			return;
		}
		
		$log = $this->user->login($this->type, $this->input->post('acc'), $this->input->post('pwd'));
		if (!$log) {
			echo json_encode(['status' => false, 'msg' => 'account or password incorrect']);
			return;
		}
		
		echo json_encode(['status' => true]);
		$this->session['user'] = $log;
	}

	/**
	 * Log out
	 */
	public function logout() {
		if (is_null($this->session['user'])) {
			echo json_encode(['status' => false, 'msg' => 'no user has logged in']);
		} else {
			$this->session['user'] = null;
			echo json_encode(['status' => true]);
		}
	}

	public function register() {
		$reg = $this->user->register($this->type, $this->input->post('acc'), $this->input->post('pwd'));
		if (!$reg) {
			echo json_encode(['status' => false, 'msg' => 'account already exists']);
			return;
		}
		echo json_encode(['status' => true]);
		$this->session['user'] = $reg;
	}
}