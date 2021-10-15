<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Packages extends MY_Controller
{
	
	function __construct()
	{
		parent::__construct();
	}

	/**
     * Packages list
     *
     *
     * @return array
    **/ 
	public function index()
	{
		$records = $this->dvbs_model->get_home_packages();
		$this->data['records'] 				= $records;
		$this->data['active_class'] 		= 'packages';
		$this->data['css_type'] 			= array();
		$this->data['title'] 				= $this->lang->line('packages');
		$this->data['sub_heading'] 			= $this->lang->line('packages');
		$this->data['content'] 				= 'site/packages';
		$this->_render_page(getTemplate(), $this->data);
	}

	/**
     * Package Booking
     *
     * @param string $param1
     * @return boolean
    **/ 
	public function booking($param1 = '')
	{
		if ($param1 == '' || !is_numeric($param1)) redirect('packages');
		$recs = $this->db->get_where($this->db->dbprefix('package_settings'), array(
			'status' => 'active',
			'id' => $param1
		))->result();


		if (count($recs) <= 0) 
			redirect('packages');


		$this->data['package_details'] = $recs[0];

		$vehicleid = $recs[0]->vehicle_id;
		unset($recs);

		$recs = $this->db->get_where($this->db->dbprefix('vehicle'), array('id' => $vehicleid))->result();


		$this->data['cabs'] 				= $recs;
		$this->data['gmaps'] 				= "true";
		$this->data['country_code'] 		= "in";
		$this->data['css_type'] 			= array("slider");
		$this->data['active_class'] 		= "packages";
		$this->data['heading'] 				= $this->lang->line('package_booking');
		$this->data['sub_heading'] 			= $this->lang->line('package_booking');
		$this->data['bread_crumb'] 			= TRUE;
		$this->data['title'] 				= $this->lang->line('welcome_to_DVBS');
		$this->data['content'] 				= 'site/package_booking_online';
		$this->_render_page(getTemplate(), $this->data);
	}
}
