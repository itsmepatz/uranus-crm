<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Payment_method extends API_Controller {

    public function list()
    {
        $key = 'master_payment_method_list';
        $data = $this->cache->get($key);
        $source = 'cache';
        if($data === FALSE)
        {
            $this->load->model('master_model');
            $data = $this->master_model->payment_method()->result();
            $source = 'database';
            $this->cache->save($key, $data, 3600);
        }

        $this->_response_json([
            'data' => $data,
            'source' => $source
        ]);
    }
}
