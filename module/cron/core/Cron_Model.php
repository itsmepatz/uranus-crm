<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once(FCPATH.'resources/core/Dermeva_Model.php');

class Cron_Model extends Dermeva_Model {
    function __construct()
    {
        parent::__construct();
        $this->load->database();
    }
}
