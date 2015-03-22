<?php

// If access is requested from anywhere other than index.php then exit
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
 |--------------------------------------------------------------------------
 |	CONTROLLER SUMMARY AND DATABASE TABLES
 |--------------------------------------------------------------------------
 | 
 |	Templates is used to put together the main structure of the HTML view. It
 |	calls head, header, content and footer in most cases. Other items can been
 |	called and used. Each part can be dynamic but content is loaded through
 |	modules and methods.
 |
 |	Database table structure
 |
 |	No table
 |
 */

class Templates extends MX_Controller
{
	private $meta_module;

	function __construct() {
		parent::__construct();
	}

	function admin_template($data) {
		$meta['meta_title'] = $data['meta_title'];
		$meta['meta_description'] = $data['meta_description'];
		
		$this->load->view('templates/admin/admin_template_head', $meta);

		$this->load->view('templates/admin/admin_template_header', $data);
		$this->load->view('templates/admin/admin_template_content', $data);
		$this->load->view('templates/admin/admin_template_footer', $data);
	}
	
	function login_template($data) {
		$meta['meta_title'] = $data['meta_title'];
		$meta['meta_description'] = $data['meta_description'];
		
		$this->load->view('templates/login/login_template_head', $meta);

		$this->load->view('templates/login/login_template_header', $data);
		$this->load->view('templates/login/login_template_content', $data);
		$this->load->view('templates/login/login_template_footer', $data);
	}
	
	function error_template($data) {
		$meta['meta_title'] = $data['meta_title'];
		$meta['meta_description'] = $data['meta_description'];
		
		$this->load->view('templates/error/error_template_head', $meta);

		$this->load->view('templates/error/error_template_header', $data);
		$this->load->view('templates/error/error_template_content', $data);
		$this->load->view('templates/error/error_template_footer', $data);
	}
}
