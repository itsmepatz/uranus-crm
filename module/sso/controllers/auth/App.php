<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class App extends SSO_Controller {

	public function index()
	{
        if($this->_is_sso_signed()) redirect($this->config->item('portal_link'));
	}
}
