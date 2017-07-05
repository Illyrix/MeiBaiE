<?php

abstract class BaseApi extends CI_Controller {
  public function __construct() {
		parent::__construct();
		date_default_timezone_set('Asia/Shanghai');
		$this->load->library('session');
		$this->load->library('upload');
		$this->load->database();
		$this->load->model('user', '', true);
    	$this->load->model('restaurant', '', true);
		header('Access-Control-Allow-Origin: http://localhost:8080');
		header("Access-Control-Allow-Methods: HEAD,POST,GET,PUT,DELETE,OPTIONS");
		header('Access-Control-Allow-Credentials: true');
		header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
	}

  /**
	 * Return user info as a JSON string
	 */
	public function getLogged() {
		if (is_null($this->session->userdata('user_id')) && is_null($this->session->userdata('rst_id'))) 
			echo json_encode(['status' => false]);
		else if (!is_null($this->session->userdata('user_id')))
			echo json_encode([
				'status' => true, 
				'type' => 0,
				'name' => $this->session->userdata('user_name'),
				'location' => $this->session->userdata('user_loc')
			]);
		else
			echo json_encode([
				'status' => true, 
				 'type' => 1,
				'name' => $this->session->userdata('rst_name'),
				'location' => $this->session->userdata('rst_loc')

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

	public function do_upload()
    {
        $config['upload_path']      = './uploads/';
        $config['allowed_types']    = 'gif|jpg|png';
        $config['max_size']     = 100;
        $config['max_width']        = 1024;
        $config['max_height']       = 768;

        $this->load->library('upload', $config);

        if ( ! $this->upload->do_upload('userfile'))
        {
            echo json_encode(['status' => false]);
			return;
        }
        else echo json_encode(['status' => true]);
    }

}