<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Cancel extends Penjualan_Controller {

    public function index()
    {
        $this->_restrict_access('penjualan_orders_cancel');
        $this->session->set_userdata('orders_state', 'orders_v1/cancel');

        $this->load->model('cs_model');
        $tl = $this->session->userdata('tim_leader');
        $team_cs_id = 0;
        if(!empty($tl) && isset($tl->team_cs_id))
        {
            $team_cs_id = $tl->team_cs_id;
        }

        $this->_set_data([
            'title' => 'Canceled Orders',
            'list_cs' => $this->cs_model->get_active([
                'role_id' => $this->role_active['role_id'],
                'team_cs_id' => $team_cs_id
            ])->result()
        ]);

        $this->blade->view('inc/penjualan/orders/cancel_v1', $this->data);
    }
}
