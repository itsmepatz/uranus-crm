<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Account_statement_model extends Penjualan_Model {

    protected
        $datatable_param = NULL,
        $table = 'account_statement',
        $orderable_field = [],
        $fillable_field = ['franchise_id','payment_method_id','seq_invoice','generated_invoice','commit','transaction_type','transaction_date','transaction_amount','note','is_use','user_id','updated_at','created_at','fix'],
        $searchable_field = ['name'];

    function get_useable_datatable($params)
    {
        $params['franchise_id'] = (int) $params['franchise_id'];

        $where = [];
        $order = [];

        if($params['payment_method_id'] != 0)
        {
            $where[] = 'a.payment_method_id = '.$params['payment_method_id'];
        }

        if(!empty($params['total_price']))
        {
            $params['total_price'] = $this->db->escape($params['total_price']);
            $order[] = "FIELD(transaction_amount, {$params['total_price']}) DESC";
        }

        if(!empty($where)) $where = ' AND '.implode(' AND ', $where);
        else $where = '';
        if(!empty($order)) $order = implode(', ', $order).', ';
        else $order = '';

        $sql = "SELECT
                a.*, b.name as account_name
            FROM account_statement a
            LEFT JOIN master_payment_method b ON a.payment_method_id = b.payment_method_id
            WHERE
                a.franchise_id = {$params['franchise_id']} AND
                a.transaction_date BETWEEN '{$params['date_start']}' AND '{$params['date_end']}' AND
                a.commit = 1 AND a.claim != 1 AND a.seq_invoice != 0 $where
            ORDER BY $order a.transaction_date ASC, a.seq_invoice ASC";

        $sql = $this->_combine_datatable_param($sql);
        $sql_count = $this->_combine_datatable_param($sql, TRUE);
        return [
            'row' => $this->db->query($sql)->result(),
            'total' => $this->db->query($sql_count)->row()->count
        ];
    }

    function get_byid($id)
    {
        return $this->db->where('account_statement_id', $id)->limit(1)->get($this->table)->first_row();
    }

    function get_last_date_inv($franchise_id)
    {
        $sql = "SELECT * FROM account_statement
            WHERE franchise_id = ? AND commit = 1
            ORDER BY transaction_date DESC LIMIT 1";
        return $this->db->query($sql, [(int) $franchise_id]);
    }

    function upd($data, $id)
    {
        $this->db->where('account_statement_id', $id);
        return $this->db->update($this->table, $this->_sanity_field($data, ['claim']));
    }

    function add($data)
    {
        return $this->db->insert($this->table, $this->_sanity_field($data));
    }

    function get_next_seq($franchise_id, $year, $commited = 0)
    {
        $where = '';
        if($commited != 0)
        {
            $where = "AND commit = 1 OR fix = 1";
        }

        $last_sequence = 0;
        $res = $this->db->query('SELECT
                MAX(seq_invoice) AS last_sequence
            FROM account_statement
            WHERE franchise_id = ? AND YEAR(transaction_date) = ? '.$where, [
                $franchise_id, $year
            ]);

        if($res->num_rows() > 0)
        {
            $last_sequence = (int) $res->first_row()->last_sequence;
        }

        return $last_sequence + 1;
    }
}
