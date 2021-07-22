<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Welcome extends CI_Controller
{

    /**
     * Index Page for this controller.
     *
     * Maps to the following URL
     * 		http://example.com/index.php/welcome
     *	- or -
     * 		http://example.com/index.php/welcome/index
     *	- or -
     * Since this controller is set as the default controller in
     * config/routes.php, it's displayed at http://example.com/
     *
     * So any other public methods not prefixed with an underscore will
     * map to /index.php/welcome/<method_name>
     * @see https://codeigniter.com/user_guide/general/urls.html
     */

    public function __construct()
    {
        parent::__construct();
        date_default_timezone_set('Asia/Jakarta');
    }

    public function index()
    {
        // $this->load->view('welcome_message');

        $json = file_get_contents(base_url()."uploads/70f32935206208b35c0e50b00e884a59.json");
        $obj  = json_decode($json, true);
        echo "<pre>";

        $i = 0;
        foreach ($obj as $key => $value) {
            $text = str_replace(" ", "", $value['business_hours']);


            print_r(explode("|", $text));

            if ($i == 100) {
                break;
            }
            $i++;
        }
        // print_r($obj);
        die();
    }

    public function users()
    {
        $json = file_get_contents(base_url()."uploads/871434c6b6d8fdbd25bc3bb68db3cf4d.json");
        $obj  = json_decode($json, true);


        $i = 0;
        // foreach ($obj as $key => $value) {
        //     $this->insert_data_user($value);
        //     if ($i == 3) {
        //         break;
        //     }
        // }


        try {
            echo "<pre>";
            print_r($obj);
            die();
            $memory = $this->benchmark->memory_usage();
        } catch (Exception $exception) {
            echo $exception->getMessage();
        }
    }

    private function insert_data_user(array $attributes)
    {
        $location = explode(",", $attributes['location']);
        $data['name'] = $attributes['name'];
        $data['latitude'] = $location[0];
        $data['longitude'] = $location[1];
        $data['balance'] = $attributes['balance'];
        $data['last_update'] = date('Y-m-d H:i:s');

        $user_id = $this->user_model->save($data);

        foreach ($attributes['purchases'] as $key => $value) {
            $value['user_id'] = $user_id;
            $this->purchase_model->save($value);
        }
    }

    public function restaurants()
    {
        $json = file_get_contents(base_url()."uploads/70f32935206208b35c0e50b00e884a59.json");
        $obj  = json_decode($json, true);

        $i = 0;
        foreach ($obj as $key => $value) {
            $this->insert_restaurant($value);
            if ($i == 200) {
                break;
            }
            $i++;
        }
    }

    private function get_business_hours(string $business_hours)
    {
        $business_hours = str_replace(" ", "", $business_hours);
        $days = explode("|", $business_hours);




        print_r($days);

        foreach ($days as $key => $value) {
            $times = explode("-", $value);
            $office_hour = $this->pecah($times);
        }

        // die();
        // echo "==========<br>";
    }

    private function get_24hours(array $data = [])
    {
        $check = explode(":", $data[1]);
        $minute = count($check);
        if ($minute == 0) {
            $result['start_at'] = date("H:i:s", strtotime($check[1]));
        } elseif ($minute == 2) {
            $start_at = $check[0].":".$check[1];
            $result['start_at'] = date("H:i:s", strtotime($start_at));
        }
        $result['closed_at'] = date("H:i:s", strtotime($data[2]));
    }

    private function pecah(array $data)
    {
        // print_r($data);

        $result = [];
        $length = count($data);
        if ($length == 3) {
            $check = explode(":", $data[1]);
            $minute = count($check);
            if ($minute == 0) {
                $result['start_at'] = date("H:i:s", strtotime($check[1]));
            } elseif ($minute == 2) {
                $start_at = $check[0].":".$check[1];
                $result['start_at'] = date("H:i:s", strtotime($start_at));
            }
            $result['closed_at'] = date("H:i:s", strtotime($data[2]));
            $this->get_day_open($check[0]);
        }
        if ($length == 2) {
            $check = explode(":", $data[0]);
            $minute = count($check);
            if ($minute == 3) {
                $start_at = $check[1].":".$check[2];
                $result['start_at'] = date("H:i:s", strtotime($start_at));
            } elseif ($minute == 2) {
                $result['start_at'] = date("H:i:s", strtotime($check[1]));
            }
            // print_r($check);
            $result['closed_at'] = date("H:i:s", strtotime($data[1]));
            // substr($string, 0, 3);
            $this->get_day_open($check[0]);
        }
        print_r($result);
        return $result;
    }

    private function get_day_open($string = null)
    {
        var_dump($string);
        $result = substr($string, 0, 3);

        switch ($result) {
            case 'value':
                # code...
                break;

            default:
                # code...
                break;
        }
    }

    private function insert_restaurant(array $attributes)
    {
        echo "<pre>";
        $check = strlen($attributes['business_hours']) > 1;
        if ($check) {
            $this->get_business_hours($attributes['business_hours']);
        }



        $location = explode(",", $attributes['location']);
        $data = [];
        $data['name'] = $attributes['name'];
        $data['latitude'] = $location[0];
        $data['longitude'] = $location[1];
        $data['balance'] = $attributes['balance'];
        $data['last_update'] = date('Y-m-d H:i:s');





        // $restaurant_id = $this->restaurant_model->save($data);

        // $data = [];
        // foreach ($attributes['menu'] as $key => $value) {
        //     $value['restaurant_id'] = $restaurant_id;
        //     $value['last_update'] = date('Y-m-d H:i:s');
        //     $this->restaurant_menu_model->save($value);
        // }
    }
}
