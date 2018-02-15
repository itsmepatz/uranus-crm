<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User_model extends SSO_Model {
    protected
        $datatable_param = NULL,
        $table = 'sso_user',
        $orderable_field = ['username','email','first_name','last_name','status'],
        $fillable_field = ['username','password','email','first_name','last_name','created_at','updated_at','status'],
        $searchable_field = ['username','email','first_name','last_name'];

    function get_datatable()
    {
        $sql = $this->_combine_datatable_param("SELECT * FROM {$this->table}");
        $sql_count = $this->_combine_datatable_param("SELECT * FROM {$this->table}", TRUE);
        return [
            'row' => $this->db->query($sql)->result(),
            'total' => $this->db->query($sql_count)->row()->count
        ];
    }

    function get_byid($id)
    {
        return $this->db->where('user_id', ((int) $id))->get($this->table)->row();
    }

    function del($id)
    {
        return $this->db->delete($this->table, ['user_id' => ((int) $id)]);
    }

    function upd($data, $user_id)
    {
        $this->db->where('user_id', $user_id);
        return $this->db->update($this->table, $this->_sanity_field($data));
    }

    function add($data)
    {
        return $this->db->insert($this->table, $this->_sanity_field($data));
    }

    function check_unique_data($data, $self_id = 0)
    {
        if(!empty($data))
        {
            $this->db->or_where($this->_sanity_field($data, ['username','email']));
            if($self_id) $this->db->where('user_id <>', $self_id);
            $this->db->from($this->table);
            return $this->db->count_all_results();
        }
        else
        {
            return 0;
        }
    }
}