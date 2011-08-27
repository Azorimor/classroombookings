<?php

class CB_Controller extends CI_Controller
{
	
	private $_tpl;
	
	function __construct()
	{
		parent::__construct();
		// Set template
		// TODO: determine whether or not to use mobile or desktop
		$this->_tpl = 'template/layout-desktop';
		// Enable profiling or not
		$this->output->enable_profiler($this->config->item('profiler'));
		// Set up session if need to
		$this->_init_session();
	}
	
	
	function page($data)
	{
		$default['sidebar'] = '';
		$default['body'] = '';
		
		//$default['header_left'] = $this->load->view('template/menu1', NULL, true);
		
		$data = array_merge($default, $data);
		$this->load->view($this->_tpl, $data);
	}
	
	
	private function _init_session()
	{
		log_message('debug', 'Creating anonymous session if required.');
		$user_id = $this->session->userdata('user_id');
		if (empty($user_id))
		{
			log_message('debug', '_init_session(): Session user_id is empty.');
			$session_made = $this->auth->session_create_anon();
			if (!$session_made)
			{
				show_error($this->auth->lasterr, 500);
			}
		}
	}
	
}