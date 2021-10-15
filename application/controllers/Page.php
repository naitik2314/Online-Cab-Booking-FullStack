<?php

 defined('BASEPATH') OR exit('No direct script access allowed');

class Page extends MY_Controller {

	function __construct()
	{
		parent::__construct();
	
	}
	
	/**
     * Pages list
     * @param string $param
     * @param string $param1
     * @return array
    **/ 
	public function index($param = '' , $param1 = '')
	{	
		$table_name = "aboutus";
		$cond = "id";
		$cond_val = $param;
		if(!$this->base_model->check_duplicate($table_name, $cond, $cond_val)){
			redirect('auth', 'refresh');
		} 
		else {
		
			$this->db->select('*');
			$rec = $this->db->get_where('vbs_aboutus', array('id' => $param))->result()[0];
			
			if (empty($rec))
				redirect('auth', 'refresh');

		
			$this->data['css_type']=array();
			$this->data['title'] = $rec->name;

			$this->data['active_class'] = 'features';
			$this->data['heading'] = urldecode($rec->name);
			$this->data['sub_heading'] = urldecode($rec->name);

			$this->data['meta_keywords'] = $rec->seo_keywords;
			$this->data['meta_description'] = $rec->meta_description;
			$this->data['description'] = $rec->description;
			$this->data['bread_crumb'] = TRUE;
			$this->data['content'] = 'site/about_info';

			$this->_render_page(getTemplate(), $this->data);
		}
	}
}
