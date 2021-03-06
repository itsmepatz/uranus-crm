<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Detail extends Penjualan_Controller {

    public function index($id = 0)
    {
        $this->_restrict_access('penjualan_orders_detail');
        $id = (int) $id;
        $this->load->model(['orders_model','master_model','orders_process_model','logistics_process_model','reason_model']);

        $res = $this->orders_model->get_byid_v1($id);
        $orders = $res->first_row();

        if(empty($orders)) redirect($this->session->userdata('orders_state'));

        if(isset($orders->customer_info)) $orders->customer_info = json_decode($orders->customer_info);
        if(isset($orders->customer_address)) $orders->customer_address = json_decode($orders->customer_address);

        if(
            (
                !in_array($this->role_active['role_id'], [1,2,6]) &&
                $orders->order_status_id > 1 && $orders->order_status_id < 6
            ) ||
            (
                $orders->order_status_id > 5 && $orders->order_status_id != 10 &&
                !in_array($this->role_active['role_id'], [1,2,3,4])
            )
        )
        {
            $check_followup_cs = $this->orders_model->validate_followup_cs($orders->order_id, $this->profile['user_id']);
            if($check_followup_cs->num_rows() == 0)
            {
                redirect($this->session->userdata('orders_state'));
            }
        }

        $orders_cart_package = $this->orders_model->cart_v1($id);
        $orders_cart_package_id = 0;
        foreach ($orders_cart_package as $key => $value) {
            if(isset($value['info']->product_package_id) && !empty($value['info']->product_package_id)) {
                $orders_cart_package_id = $value['info']->product_package_id;
            }
        }

        $reason_cancel = $this->reason_model->get_cancel();
        $reason_pending = $this->reason_model->get_pending();

        if($orders->order_status_id == 4)
        {
            $reason_pending = ['FROM_CANCEL: Customer jadi beli'];
        }

        $product_package = $this->master_model->product_package()->result();
        foreach ($product_package as $key => $value) {
            $value->product_list = $this->master_model->product_package_list([
                'product_package_id' => $value->product_package_id
            ])->result();
            $product_package[$key] = $value;
        }

        $this->_set_data([
            'title' => 'Detail Pesanan',
            'orders' => $orders,
            'attr_readonly' => 'readonly',
            'reason_cancel' => $reason_cancel,
            'reason_pending' => $reason_pending,
            'master_payment_method' => $this->master_model->payment_method()->result(),
            'master_call_method' => $this->master_model->call_method()->result(),
            'master_wilayah_provinsi' => $this->master_model->wilayah_provinsi()->result(),
            'master_logistics' => $this->master_model->logistics()->result(),
            'master_product_package' => $product_package,
            'orders_cart_package_id' => $orders_cart_package_id,
            'orders_cart_package' => $orders_cart_package,
            'orders_process' => $this->orders_process_model->get($id)->result(),
            'logistics_process' => $this->logistics_process_model->get($id)->result()
        ]);

        $this->blade->view('inc/penjualan/orders/detail_v1', $this->data);
    }

    public function save_note()
    {
        $this->load->model('orders_model');
        $order_id = (int) $this->input->post('order_id');

        $data = [
            'note' => $this->input->post('note'),
        ];
        $res = $this->orders_model->upd($order_id, $data);

        if($res)
        {
            $this->_response_json([
                'status' => 1,
                'message' => 'Berhasil mengubah note'
            ]);
        }
        else
        {
            $this->_response_json([
                'status' => 0,
                'message' => 'Gagal mengubah note'
            ]);
        }
    }
}
