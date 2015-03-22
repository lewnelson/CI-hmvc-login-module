<?php

// If access is requested from anywhere other than index.php then exit
if ( ! defined('BASEPATH')) exit('No direct script access allowed');


echo "<h3>logged in</h3>";

$this->load->library('table');

$table_data =	array(
				array('User Data Type', 'User Data Value'),
				array('User ID', $user_id),
				array('Username', $username),
				array('Account Type', $account_type),
				array('Logged In Since', $logged_in_since)
				);

echo $this->table->generate($table_data);

echo "<p><a href='" . base_url() . custom_constants::logout_url . "'>logout</a></p>";

?>