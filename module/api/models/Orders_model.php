<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Orders_model extends API_Model {

    function add($orders)
    {
        $sql = "INSERT INTO orders (
                version,
                created_at,
                shipping_code,
                order_code,
                customer_id,
                customer_address_id,
                customer_info,
                customer_address,
                payment_method_id,
                logistic_id,
                order_status_id,
                logistics_status_id,
                call_method_id,
                order_status,
                logistics_status,
                call_method,
                total_price
            )
            SELECT
                ? AS version,
                ? AS created_at,
                '' AS shipping_code,
                ? AS order_code,
                ? AS customer_id,
                ? AS customer_address_id,
                ? AS customer_info,
                ? AS customer_address,
                ? AS payment_method_id,
                ? AS logistic_id,
            	a.order_status_id,
            	b.logistics_status_id,
            	c.call_method_id,
                a.label AS order_status,
                b.label AS logistics_status,
                c.name AS call_method,
            	CASE
            	    WHEN d.price_type = 'PACKAGE' THEN d.price
            	    ELSE (SELECT SUM(price) FROM product_package_list WHERE product_package_id = d.product_package_id AND `status` = 1)
            	END AS total_price
            FROM master_order_status a
            LEFT JOIN master_logistics_status b ON b.logistics_status_id = ?
            LEFT JOIN master_call_method c ON c.call_method_id = ?
            LEFT JOIN product_package d ON d.product_package_id = ?
            WHERE a.order_status_id = ?
            LIMIT 1";
        return $this->db->query($sql, [
            'version' => $orders['version'],
            'created_at' => $orders['created_at'],
            'order_code' => $orders['order_code'],
            'customer_id' => (int) $orders['customer_id'],
            'customer_address_id' => (int) $orders['customer_address_id'],
            'customer_info' => $orders['customer_info'],
            'customer_address' => $orders['customer_address'],
            'payment_method_id' => (int) $orders['payment_method_id'],
            'logistic_id' => (int) $orders['logistic_id'],
            'logistics_status_id' => (int) $orders['logistics_status_id'],
            'call_method_id' => (int) $orders['call_method_id'],
            'product_package_id' => (int) $orders['product_package_id'],
            'order_status_id' => (int) $orders['order_status_id']
        ]);
    }

    function customer_add($data = [])
    {
        $data = (array) $data;
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['status'] = 1;
        $this->db->insert('customer', $this->_sanity_field($data, ['full_name','telephone','created_at','status']));
        return $this->db->get_where('customer', ['customer_id' => $this->db->insert_id()])->first_row();
    }

    function customer_address_add($data = [])
    {
        $data = (array) $data;

        $sql = "INSERT INTO customer_address (
                    customer_id,
                    address,
                    postal_code,
                    created_at,
                    status,
                    provinsi_id,
                    kabupaten_id,
                    kecamatan_id,
                    desa_id,
                    provinsi,
                    kabupaten,
                    kecamatan,
                    desa_kelurahan
                )
                SELECT
            	? AS customer_id,
            	? AS address,
            	? AS postal_code,
            	? AS created_at,
                ? AS status,
            	a.id AS provinsi_id,
            	b.id AS kabupaten_id,
            	c.id AS kecamatan_id,
            	d.id AS desa_id,
                a.name AS provinsi,
                b.name AS kabupaten,
                c.name AS kecamatan,
                d.name AS desa_kelurahan

            from master_wilayah_provinsi a
            left join master_wilayah_kabupaten b on a.id = b.provinsi_id and b.id = ?
            LEFT JOIN master_wilayah_kecamatan c ON b.id = c.kabupaten_id AND c.id = ?
            LEFT JOIN master_wilayah_desa d ON c.id = d.kecamatan_id AND d.id = ?
            where a.id = ?
            limit 1";

        $this->db->query($sql, [
            'customer_id' => isset($data['customer_id'])?$data['customer_id']:0,
            'address' => isset($data['address'])?$data['address']:'',
            'postal_code' => isset($data['postal_code'])?$data['postal_code']:'',
            'created_at' => date('Y-m-d H:i:s'),
            'status' => 1,
            'kabupaten_id' => (string) isset($data['kabupaten_id'])?$data['kabupaten_id']:'',
            'kecamatan_id' => (string) isset($data['kecamatan_id'])?$data['kecamatan_id']:'',
            'desa_id' => (string) isset($data['desa_id'])?$data['desa_id']:'',
            'provinsi_id' => (string) isset($data['provinsi_id'])?$data['provinsi_id']:''
        ]);
        return $this->db->get_where('customer_address', ['customer_address_id' => $this->db->insert_id()])->first_row();
    }

    function get_last_order()
    {
        $sql = "SELECT * FROM orders
        WHERE
        YEAR(created_at) = ? AND
        MONTH(created_at) = ? AND
        DAY(created_at) = ?
        ORDER BY order_id DESC
        LIMIT 1";
        return $this->db->query($sql, [
            date('Y'), (int) date('m'), date('d')
        ]);
    }
}
