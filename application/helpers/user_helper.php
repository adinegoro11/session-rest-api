<?php if (! defined('BASEPATH')) {
    exit('No direct script access allowed');
}

if (! function_exists('validate_register')) {
    function validate_register($params = [])
    {
        $ci=& get_instance();
        $data = [
            'name' => isset($params['name']) ? $params['name'] : null,
            'email' => isset($params['email']) ? $params['email'] : null,
            'password' => isset($params['password']) ? $params['password'] : null,
            'confirm_password' => isset($params['confirm_password']) ? $params['confirm_password'] : null,
        ];
        $ci->form_validation->set_data($data);
        $ci->form_validation->set_rules('name', 'name', 'trim|required|min_length[4]');
        $ci->form_validation->set_rules('password', 'password', 'required|min_length[4]');
        $ci->form_validation->set_rules('confirm_password', 'confirm_password', 'required');
        $ci->form_validation->set_rules('email', 'email', 'trim|required|valid_email|is_unique[user.email]');

        $result['success'] = true;
        if ($ci->form_validation->run() == false) {
            $result['success'] = false;
            $result['error_message'] = validation_errors();
        }

        if ($data['password'] !== $data['confirm_password']) {
            $result['success'] = false;
            $result['error_message'] = 'Password not match';
        }
        return $result;
    }
}

if (! function_exists('validate_login')) {
    function validate_login($params = [])
    {
        $ci=& get_instance();
        $data = [
            'email' => isset($params['email']) ? $params['email'] : null,
            'password' => isset($params['password']) ? $params['password'] : null,
        ];
        $ci->form_validation->set_data($data);
        $ci->form_validation->set_rules('email', 'email', 'trim|required|valid_email');
        $ci->form_validation->set_rules('password', 'password', 'required');
        $result = [];
        $result['success'] = true;
        if ($ci->form_validation->run() == false) {
            $result['success'] = false;
            $result['error_message'] = validation_errors();
        }
        return $result;
    }
}

if (! function_exists('check_user')) {
    function check_user($params = [])
    {
        $ci=& get_instance();
        $where = ['email' => $params['email']];
        $query = $ci->user_model->get_by_condition($where);

        if (!isset($query['email'])) {
            $result['success'] = false;
            $result['error_message'] = 'Email not found';
            return $result;
        }

        if (! password_verify($params['password'], $query['password'])) {
            $result['success'] = false;
            $result['error_message'] = 'Invalid password';
            return $result;
        } else {
            $result['success'] = true;
            $result['data'] = $query;
            return $result;
        }
    }
}
 
