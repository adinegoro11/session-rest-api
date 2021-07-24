<?php if (! defined('BASEPATH')) {
    exit('No direct script access allowed');
}

if (! function_exists('validate_before_insert')) {
    function validate_before_insert($params = [])
    {
        $ci=& get_instance();
        $data = [
            'name' => isset($params['name']) ? $params['name'] : null,
            'description' => isset($params['description']) ? $params['description'] : null,
            'start' => isset($params['start']) ? $params['start'] : null,
            'duration' => isset($params['duration']) ? $params['duration'] : null,
        ];
        $ci->form_validation->set_data($data);
        $ci->form_validation->set_rules('name', 'name', 'trim|required|min_length[4]');
        $ci->form_validation->set_rules('description', 'description', 'required|min_length[4]');
        $ci->form_validation->set_rules('start', 'start', 'required|min_length[4]');
        $ci->form_validation->set_rules('duration', 'duration', 'required|integer');

        $result['success'] = true;
        $result['data'] = $data;
        if ($ci->form_validation->run() == false) {
            $result['success'] = false;
            $result['error_message'] = validation_errors();
        }
        return $result;
    }
}

if (! function_exists('validate_params')) {
    function validate_params($params = [])
    {
        $ci=& get_instance();
        $data = [
            'session_id' => isset($params['session_id']) ? $params['session_id'] : null,
        ];
        $ci->form_validation->set_data($data);
        $ci->form_validation->set_rules('session_id', 'Session ID', 'required|integer');
        $result['success'] = true;
        $result['session_id'] = $data['session_id'];
        if ($ci->form_validation->run() == false) {
            $result['success'] = false;
            $result['error_message'] = validation_errors();
        }
        return $result;
    }
}

if (! function_exists('mapping_columns')) {
    function mapping_columns($params = [])
    {
        $mapping = [
            // db column => params column
            'duration' => 'duration',
            'u.name' => 'name',
            'user.id '=> 'user_id',
            'email' => 'email',
            's.id'=>'session_id',
            's.name' => 'session_name',
            'description'=>'description',
        ];

        $where = [];
        foreach ($params['search'] as $key => $value) {
            $db_column = array_search($key, $mapping);
            if ($db_column) {
                $where[$db_column] = $value;
            }
        }

        $order_by = [];
        $sort = ['asc','desc'];
        foreach ($params['order_by'] as $key => $value) {
            $db_column = array_search($key, $mapping);
            $valid_sort = in_array($value, $sort);
            if ($db_column && $valid_sort) {
                $order_by[$db_column] = $value;
            }
        }

        $result = ['where' => $where, 'order_by' => $order_by];
        return $result;
    }
}
