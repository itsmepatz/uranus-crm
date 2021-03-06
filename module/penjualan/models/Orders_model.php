<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Orders_model extends Penjualan_Model {
    protected
        $datatable_param = NULL,
        $table = 'orders',
        $orderable_field = ['name','franchise_name','franchise_name','jumlah_cs','status'],
        $fillable_field = ['franchise_id','payment_method_id','logistic_id','order_status_id','logistics_status_id','order_status','logistics_status','call_method_id','customer_info','customer_address','total_price','customer_id','customer_address_id','note','customer_phonenumber_id'],
        $searchable_field = ['payment_method_id','logistic_id','order_status_id','logistics_status_id','call_method_id','order_status','logistics_status','shipping_code','call_method','order_code','customer_info','customer_address'];

    function get_datatable_v1($params = [], $only_self = TRUE)
    {
        $select = [];
        $join = [];
        $where = [];
        $ordering = 'ORDER BY created_at ASC';

        if(
            isset($params['date_start']) && !empty($params['date_start'])
        ) $params['date_start'] = $this->db->escape($params['date_start'].' 00:00:00');
        if(
            isset($params['date_end']) && !empty($params['date_end'])
        ) $params['date_end'] = $this->db->escape($params['date_end'].' 23:59:59');


        if($params['order_status_id'] == 4) $ordering = 'ORDER BY created_at DESC';

        if(isset($params['order_status_id']) && $params['order_status_id'] != 1)
        {
            $history_order_status_id = $params['order_status_id'];
            if(
                in_array($params['order_status_id'], [7,8,9]) &&
                in_array($params['role_id'], [1,5])
            )
            {
                // role cs
                // view sale untuk memfilter yg hanya di verify pay oleh yg bersangkutan
                // pada menu sale
                $history_order_status_id = 6;
            }
            else if(
                in_array($params['order_status_id'], [7,8,9]) &&
                $params['role_id'] == 3
            )
            {
                // role finance
                // view sale untuk memfilter yg hanya di sale oleh yg bersangkutan
                // pada menu sale
                $history_order_status_id = 7;
            }

            $join[] = "LEFT JOIN (SELECT
                z.order_id, z.order_status_id, z.user_id, zo.username, z.created_at
                FROM orders_process z
                LEFT JOIN sso_user zo ON z.user_id = zo.user_id
                WHERE z.order_status_id = {$history_order_status_id}
                GROUP BY z.order_id) d ON a.order_id = d.order_id";
            $select[] = 'd.username';
            $select[] = 'd.created_at as action_date';
        }

        if(
            $only_self && isset($params['user_id']) &&
            !in_array($params['order_status_id'], [1]))
        {
            $user_id = (int) $params['user_id'];
            $where[] = "d.user_id = $user_id";
        }
        else if(
            $params['role_id'] == 6 &&
            $params['order_status_id'] != 1 && $params['order_status_id'] != 7
            )
        {
            $where[] = "d.user_id IN (SELECT user_id FROM management_team_cs_member WHERE team_cs_id = {$params['tim_leader']->team_cs_id})";
        }
        else if(
            $params['role_id'] == 6 &&
            $params['order_status_id'] == 7
            )
        {
            $join[] = "LEFT JOIN orders_process e ON a.order_id = e.order_id AND e.order_status_id = 6";
            $where[] = "e.user_id IN (SELECT user_id FROM management_team_cs_member WHERE team_cs_id = {$params['tim_leader']->team_cs_id})";
        }

        // filter tanggal action dan cs yg bersanguktan
        if(in_array($params['order_status_id'], [1,2,3,4,5,6,10]))
        {
            $where[] = "a.order_status_id = {$params['order_status_id']}";

            // filter tanggal
            if($params['order_status_id'] == 1)
            {
                $where[] = "a.created_at BETWEEN ".$params['date_start'] ." AND ". $params['date_end'];
            }
            else
            {
                $where[] = "d.created_at BETWEEN ".$params['date_start'] ." AND ". $params['date_end'];
            }
        }
        else
        {
            $where[] = "a.order_status_id >= {$params['order_status_id']}";
            // select nama cs yg sale produk
            $select[] = "(SELECT ho.username
                FROM orders_process h
                LEFT JOIN sso_user ho ON h.user_id = ho.user_id
                WHERE h.order_status_id = 6 and h.order_id = a.order_id LIMIT 1) as cs_sale";

            // filter tanggal sale dan tampilkan
            $join[] = "LEFT JOIN orders_process j ON a.order_id = j.order_id and j.order_status_id = 7";
            $select[] = 'j.created_at AS sale_date';
            $where[] = "j.created_at BETWEEN ".$params['date_start'] ." AND ". $params['date_end'];
            $ordering = 'ORDER BY j.created_at DESC';

            switch ($params['filter_sale']) {
                case 'orders_sale':
                $where[] = "a.order_status_id = 7";
                    break;

                case 'orders_logistics':
                    $where[] = "(a.order_status_id > 7 AND a.order_status_id < 10)";
                    break;
            }
        }

        if(empty($select)) $select = ''; else $select = ", ".implode(", ",$select);
        if(empty($join)) $join = ''; else $join = implode(" \n",$join);
        if(empty($where)) $where = ''; else $where = " AND ".implode(" AND ",$where);

        if(!isset($params['order_status_id'])) $params['order_status_id'] = 1;

        $sql = "SELECT
                a.*, b.icon AS call_method_icon, c.name AS payment_method,
                (SELECT package_name FROM orders_cart WHERE order_id = a.order_id AND is_package = 1 LIMIT 1) AS package_name $select
            FROM orders a
            LEFT JOIN master_call_method b ON a.call_method_id = b.call_method_id
            LEFT JOIN master_payment_method c ON a.payment_method_id = c.payment_method_id
            $join
            WHERE
            a.version = 1 AND
            (a.orders_double_id IS NULL OR a.orders_double_id = 0) AND
            a.is_deleted = 0
            $where
            $ordering";

        // echo $sql;
        // exit;

        $sql = $this->_combine_datatable_param($sql);
        $sql_count = $this->_combine_datatable_param("SELECT a.order_id
            FROM orders a
            $join
            WHERE
            a.version = 1 AND
            (a.orders_double_id IS NULL OR a.orders_double_id = 0) AND
            a.is_deleted = 0
            $where", TRUE);
        return [
            'row' => $this->db->query($sql)->result(),
            'total' => $this->db->query($sql_count)->row()->count
        ];
    }

    function cart_v1($id)
    {
        $orders_cart = $this->db->order_by('is_package', 'DESC')->get_where('orders_cart', ['order_id' => $id, 'version' => 1])->result();
        return $this->parse_cart_v1($orders_cart);
    }

    function parse_cart_v1($orders_cart = [])
    {
        $orders_cart_package = [];
        foreach ($orders_cart as $key => $value) {
            $key = 'RETAIL';
            $package_name = "Lain-lain";
            if($value->is_package)
            {
                $key = $value->product_package_id;
                $package_name = $value->package_name;
            }

            $orders_cart_package[$key]['info'] = (object) [
                'product_package_id' => $value->product_package_id,
                'package_name' => $package_name,
                'price_type' => $value->price_type,
                'package_price' => $value->package_price
            ];
            $orders_cart_package[$key]['cart'][] = (object) [
                'cart_id' => $value->cart_id,
                'order_id' => $value->order_id,
                'product_id' => $value->product_id,
                'product_merk' => $value->product_merk,
                'product_name' => $value->product_name,
                'price' => $value->price,
                'qty' => $value->qty,
                'weight' => $value->weight,
                'price_type' => $value->price_type,
                'is_package' => $value->is_package
            ];
        }
        return $orders_cart_package;
    }

    function get_byid_v1($id)
    {
        $sql = "SELECT
                a.*, b.icon AS call_method_icon, c.name AS payment_method,
                d.order_invoice_id, d.invoice_number, d.paid_date, d.publish_date,
                d.total_price as invoice_total_price
            FROM orders a
            LEFT JOIN master_call_method b ON a.call_method_id = b.call_method_id
            LEFT JOIN master_payment_method c ON a.payment_method_id = c.payment_method_id
            LEFT JOIN orders_invoices d ON a.order_id = d.order_id
            WHERE a.version = 1 AND a.order_id = ? LIMIT 1";
        return $this->db->query($sql, [$id]);
    }

    function get_byid_bulk($id = [])
    {
        foreach ($id as $key => $value) {
            $id[$key] = (int) $value;
        }
        $id = implode(',', $id);
        $sql = "SELECT
            a.*, b.total_product, c.name AS logistic_name,
            (SELECT package_name FROM orders_cart WHERE order_id = a.order_id AND is_package = 1 LIMIT 1) AS package_name
        FROM orders a
        LEFT JOIN (
            SELECT order_id, SUM(qty) AS total_product
            FROM orders_cart
            WHERE product_id IS NOT NULL AND product_id != 0
            GROUP BY order_id
        ) b ON a.order_id = b.order_id
        LEFT JOIN master_logistics c ON a.logistic_id = c.logistic_id
        WHERE a.order_id IN ({$id})";
        return $this->db->query($sql);
    }

    function upd($id, $params)
    {
        $this->db->where('order_id', $id);
        return $this->db->update($this->table, $this->_sanity_field($params, $this->fillable_field));
    }

    function clear_cart_package($order_id = 0)
    {
        return $this->db->delete('orders_cart', ['order_id' => $order_id, 'is_package' => 1]);
    }

    function upd_cart_package($order_id, $product_package_id, $qty = 0)
    {
        $qty = (int) $qty;
        if($qty == 0) $qty = 1;

        $sql = "INSERT INTO orders_cart (
            order_id, product_id, product_package_id, product_merk, product_name, package_name, price, qty, weight, is_package, price_type, package_price)
        SELECT
            ?, a.product_id, a.product_package_id, a.merk, a.name, b.name,
            CASE b.price_type
                WHEN 'RETAIL' THEN (a.price * $qty)
                WHEN 'PACKAGE' THEN 0
            ELSE NULL
            END,
            (a.qty * $qty),
            (a.weight * $qty), 1,
            b.price_type,
            CASE b.price_type
                WHEN 'RETAIL' THEN 0
                WHEN 'PACKAGE' THEN (b.price * $qty)
            ELSE NULL
            END
        FROM product_package b
        LEFT JOIN product_package_list a ON b.product_package_id = a.product_package_id
        WHERE b.product_package_id = ?";
        return $this->db->query($sql, [$order_id, $product_package_id]);
    }

    function get_order_cart($orders_id = 0)
    {
        return $this->db->where('order_id', $orders_id)->get('orders_cart');
    }

    function get_latest_price_cart($id)
    {
        $price = [
            'PACKAGE' => [],
            'RETAIL' => [],
            'OTHER' => []
        ];
        $cart = $this->get_order_cart($id)->result();
        foreach ($cart as $key => $value) {
            if(
                $value->price_type == 'PACKAGE' &&
                $value->is_package == 1 &&
                !isset($price['PACKAGE'][$value->product_package_id]))
            {
                $price['PACKAGE'][$value->product_package_id] = $value->package_price;
            }
            else if(
                $value->price_type == 'RETAIL' &&
                !isset($price['RETAIL'][$value->product_id]) &&
                !empty($value->product_id)
            )
            {
                $price['RETAIL'][$value->product_id] = $value->price;
            }
            else if($value->is_package != 1)
            {
                $price['OTHER'][] = $value->price;
            }
        }

        $retail_price = 0;
        return $total_price = array_sum($price['PACKAGE']) +  array_sum($price['RETAIL']) + array_sum($price['OTHER']);
    }

    function del($id = 0)
    {
        return $this->db->delete('orders', ['order_id' => (int) $id]);
    }

    function clear_kode_unik($order_id)
    {
        $this->db->where([
            'order_id' => (int) $order_id,
            'product_name' => 'Kode Unik',
            'product_id' => NULL,
            'product_merk' => NULL,
            'price_type' => 'RETAIL',
        ]);
        return $this->db->delete('orders_cart');
    }

    function addon_cart($params)
    {
        return $this->db->insert('orders_cart', $this->_sanity_field($params, ['order_id','product_id','product_merk','product_name','qty','is_package','price','price_type','version']));
    }

    function del_by_cart_id($id = 0)
    {
        return $this->db->delete('orders_cart', ['cart_id' => (int) $id]);
    }

    function del_by_package_id($order_id = 0, $product_package_id = 0)
    {
        return $this->db->delete('orders_cart', ['order_id' => (int) $order_id, 'product_package_id' => (int) $product_package_id]);
    }

    function get_follow_up($order_id)
    {
        return $this->db->order_by('process_id', 'ASC')->get_where('orders_process', [
            'order_status_id' => 2,
            'order_id' => (int) $order_id
        ]);
    }

    function validate_followup_cs($order_id, $user_id)
    {
        return $this->db->get_where('orders_process', [
            'order_status_id' => 2,
            'order_id' => (int) $order_id,
            'user_id' => (int) $user_id
        ]);
    }

    function get_active_orders_by_customer_id($customer_id)
    {
        $this->db->where('orders_double_id IS NULL', NULL, FALSE);
        return $this->db->get_where('orders', [
            'customer_id' => $customer_id,
            'order_status_id <' => 7,
            'order_status_id !=' => 4,
            'is_deleted' => 0
        ]);
    }

    function trash($id = 0)
    {
        if(!is_array($id))
        {
            $id = [$id];
        }
        $this->db->where_in('order_id', $id);
        return $this->db->update('orders', [
            'is_deleted' => 1
        ]);
    }
}
