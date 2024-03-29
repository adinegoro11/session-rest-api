<?php

defined('BASEPATH') or exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';
require_once('./vendor/autoload.php');
use Restserver\Libraries\REST_Controller;
use Firebase\JWT\JWT;

class Sessions extends REST_Controller
{
    public function __construct($config = 'rest')
    {
        parent::__construct($config);
        $this->load->model(['user_model','session_model']);
        $this->load->helper(['session','user']);
        date_default_timezone_set('Asia/Jakarta');
    }

    public function index_get()
    {
        $this->check_token();
        $params = $this->get();
        $session_id = $this->uri->segment(3, 0);
        validate_params(['session_id'=> $session_id]);

        if (isset($params['request']) && $session_id == 0) {
            $this->list_sessions($params);
        } else {
            $this->show_detail($session_id);
        }
    }

    public function index_post()
    {
        $headers = $this->check_token();
        $params = $this->post();
        $this->check_user($headers['data']['user_id']);
        $this->validate_before_insert($params);
        $params['userID'] = $headers['data']['user_id'];
        $query = $this->save($params);
        $output = [
            'success' => true,
            'message' => 'Session successfully created',
            'data' => $query,
        ];
        $this->set_output($output, 201);
    }

    public function index_delete($session_id = 0)
    {
        $headers = $this->check_token();
        $query = $this->get_session($session_id);
        if ($headers['data']['user_id'] != $query['userID']) {
            $output = [
                'success' => false,
                'message' => 'You not allowed to delete this data',
                'data' => [],
            ];
            $this->set_output($output, 403);
        } else {
            $this->session_model->delete($session_id);
            $output = [
                'success' => true,
                'message' => 'Data successfully removed',
                'data' => [],
            ];
            $this->set_output($output, 200);
        }
    }

    public function index_put($session_id = 0)
    {
        $headers = $this->check_token();
        validate_params(['session_id'=> $session_id]);
        $stream = $this->input->raw_input_stream;
        $stream = json_decode($stream,true);
        $params = $this->validate_before_insert($stream);
        $query = $this->get_session($session_id);
        if ($headers['data']['user_id'] != $query['userID']) {
            $output = [
                'success' => false,
                'message' => 'You not allowed to update this data',
                'data' => [],
            ];
            $this->set_output($output, 403);
        } else {
            $this->session_model->update($session_id, $params['data']);
            $query = $this->get_session($session_id);
            $output = [
                'success' => true,
                'message' => 'Data successfully update',
                'data' => $query,
            ];
            $this->set_output($output, 200);
        }
    }

    private function check_token()
    {
        try {
            $headers = $this->input->request_headers();
            if (!isset($headers['Authorization'])) {
                $output = [
                    'success' => false,
                    'message' => 'Authorization header not found',
                    'data' => [],
                ];
                $this->set_output($output, 403);
            }
            $auth = explode(' ', $headers['Authorization']);
            $decoded = JWT::decode($auth[1], ACCESS_TOKEN_SECRET, ['HS256']);
            $result = [
                'success' => true,
                'data' => (array) $decoded,
            ];
            return $result;
        } catch (Exception $e) {
            $output = [
                'success' => false,
                'message' => $e->getMessage(),
                'data' => [],
            ];
            $this->set_output($output, 400);
        }
    }

    private function check_user($user_id = null)
    {
        $query = get_user(['ID' => $user_id]);
        if (!isset($query['email'])) {
            $output = [
                'code' => 404,
                'error_messages' => 'User ID not found',
            ];
            $this->set_output($output, 403);
        }
        return;
    }

    private function validate_before_insert($params = [])
    {
        $validation = validate_before_insert($params);
        if (! $validation['success']) {
            $output = [
                'success' => false,
                'message' => $validation['error_message'],
                'data' => [],
            ];
            $this->set_output($output, 403);
        }
        return $validation;
    }

    private function save($data = [])
    {
        $query = [
            'userID' => $data['userID'],
            'name' => $data['name'],
            'description' => $data['description'],
            'start' => $data['start'],
            'duration' => $data['duration'],
            'created' => date('Y-m-d H:i:s'),
            'updated' =>date('Y-m-d H:i:s'),
        ];
        $id = $this->session_model->save($query);
        $query['id'] = $id;
        return $query;
    }

    private function set_output($response = [], $status = 200)
    {
        $this->output
        ->set_status_header($status)
        ->set_content_type('application/json', 'utf-8')
        ->set_output(json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES))
        ->_display();
        exit;
    }

    private function show_detail($session_id = 0)
    {
        $query = $this->session_model->get_detail($session_id);
        $found = count($query) > 0;

        if (! $found) {
            $output = [
                'success' => false,
                'message' => 'Session not found',
                'data' => [],
            ];
            $this->set_output($output, 404);
        } else {
            $data = $query[0];
            $output = [
            'success' => true,
            'data' => [
                'id' => $data['session_id'],
                'name' => $data['session_name'],
                'description' => $data['description'],
                'start' => $data['start'],
                'duration' => $data['duration'],
                'created' => $data['session_created'],
                'user' => [
                    'id' => $data['user_id'],
                    'name' => $data['user_name'],
                    'email' => $data['email'],
                    ]
                ],
           ];
            $this->set_output($output, 200);
        }
    }

    private function get_session($session_id = 0)
    {
        $query = $this->session_model->get_by_condition(['ID' => $session_id]);
        if (!isset($query)) {
            $output = [
                'success' => false,
                'message' => 'Session not found',
                'data' => $query,
            ];
            $this->set_output($output, 404);
        }
        return $query;
    }

    private function list_sessions($params)
    {
        $params = json_decode($params['request'], true);
        $params = mapping_columns($params);
        $query = $this->session_model->get_list($params['where'], $params['order_by']);
        if (count($query) == 0) {
            $output = [
                'success' => false,
                'message' => 'Session not found',
                'data' => [],
            ];
            $this->set_output($output, 404);
        } else {
            foreach ($query as $key => $value) {
                $query[$key]['user']['id'] = $value['user_id'];
                $query[$key]['user']['name'] = $value['user_name'];
                $query[$key]['user']['email'] = $value['email'];
                unset($query[$key]['email'],$query[$key]['user_name'],$query[$key]['user_id']);
            }

            $output = [
                'success' => true,
                'message' => 'List Sessions',
                'data' => $query,
            ];
            $this->set_output($output, 200);
        }
    }
}
