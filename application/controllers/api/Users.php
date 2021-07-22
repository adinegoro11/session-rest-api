<?php

defined('BASEPATH') or exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';
require_once('./vendor/autoload.php');
use Restserver\Libraries\REST_Controller;
use Firebase\JWT\JWT;

class Users extends REST_Controller
{
    public function __construct($config = 'rest')
    {
        parent::__construct($config);
        $this->load->model(['user_model']);
        $this->load->helper(['user']);
        date_default_timezone_set('Asia/Jakarta');
    }

    public function index_post($type = 'register')
    {
        $params = $this->post();
        $data = [];
        if ($type == 'register') {
            $data = validate_register($params);
        }

        if ($type == 'register' && ! $data['success']) {
            $output = [
                'code' => 403,
                'error_messages' => $data['error_message'],
            ];
            $this->response($output, 403);
        }

        if ($type == 'register' && $data['success']) {
            $pass_hash = password_hash($params['password'], PASSWORD_DEFAULT, ['cost' => 10]);
            $insert = [
                'name' => $params['name'],
                'email' => $params['email'],
                'password' => $pass_hash,
                'created' => date('Y-m-d H:i:s'),
                'updated' =>date('Y-m-d H:i:s'),
            ];
            $this->user_model->save($insert);
            unset($insert['password'],$insert['created'], $insert['updated']);
            $output = [
                'code' => 201,
                'messages' => 'Data successfully created',
                'result' => $insert,
            ];
            $this->response($output, 201);
        }

        if ($type == 'login') {
            $validation = validate_login($params);
        }

        if ($type == 'login' && ! $validation['success']) {
            $output = [
                'code' => 403,
                'error_messages' => $validation['error_message'],
            ];
            $this->response($output, 403);
        }

        if ($type == 'login' && $validation['success']) {
            $validation = check_user($params);
        }

        if ($type == 'login' && ! $validation['success']) {
            $output = [
                'code' => 403,
                'error_messages' => $validation['error_message'],
            ];
            $this->response($output, 403);
        }

        if ($type == 'login' && $validation['success']) {
            $payload = [
                'user_id' => $validation['data']['ID'],
                'exp' => time() + (86400 * 60), // 86400 * 60 (second) = 24 hours
            ];
            $access_token = JWT::encode($payload, ACCESS_TOKEN_SECRET);
            $responses = [
                'code' => 200,
                'result' => [
                    'email' => $params['email'],
                    'token' => $access_token,
                ]
            ];
            $this->response($responses, 200);
        }
    }
}
