<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Payment_method extends API_Controller {

    public function list()
    {
        $this->load->model('master_model');
        $this->_response_json([
            'data' => $this->master_model->payment_method()->result()
        ]);
    }
}
