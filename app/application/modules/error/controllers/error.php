<?php

// If access is requested from anywhere other than index.php then exit
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
 |--------------------------------------------------------------------------
 |	CONTROLLER SUMMARY AND DATABASE TABLES
 |--------------------------------------------------------------------------
 | 
 |	Shows custom error_404 page
 |
 |	Database table structure
 |
 |	Table name(s) - No table
 |
 */
 
 
class Error extends MX_Controller {

	function __construct() {
		parent::__construct();
		
		// Redirect to the login if page requested is in a protected section
		foreach(custom_constants::$protected_pages as $page)
		{
			if(strpos($this->uri->uri_string, $page) === 0)
			{
				check_user_login(FALSE);
			}
		}
	}
	
	function error_404() {
		$data['meta_title'] = "Error 404";
		$data['meta_description'] = "Error 404 page not found";
		
		$data['modules'][] = "error";
		$data['methods'][] = "view_error_404";
		
		echo Modules::run("templates/error_template", $data);
	}
	
	function view_error_404() {
		$this->load->view("error/error_404");
	}
}
