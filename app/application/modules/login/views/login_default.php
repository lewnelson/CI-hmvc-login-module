<?php

// If access is requested from anywhere other than index.php then exit
if ( ! defined('BASEPATH')) exit('No direct script access allowed');


$username_email_input = array(
						"name" => "username/email",
						"placeholder" => "username",
						"maxlength" => "320",
						"required" => "required"
					);

if(custom_constants::email_login_allowed === TRUE)
{
	$username_email_input['placeholder'] = "username/email";
}
					
$password_input = array(
						"name" => "password",
						"placeholder" => "password",
						"maxlength" => "32",
						"required" => "required"
					);
					
$username_email_input['value'] = $this->session->flashdata("username");

echo "<div id='login-container'>";

// If currently black listed then show time left before removed from black list. Otherwise show the login form
if($timeout_left !== FALSE)
{
	echo "<div class='form-errors'>";
	echo "<p>You have been locked out for too many incorrect login attempts. Please
	wait " . ceil($timeout_left) . " minutes before attempting to login again.</p>";
	
	if(custom_constants::email_login_allowed === TRUE)
	{
		echo "<p>If you have forgot your password then click on the reset
		password link below.</p>";
	}
	else
	{
		echo "<p>If you have forgot your username or password then click on the forgot username or reset
		password link below.</p>";
	}
	echo "</div>";
}
else
{
	echo form_open(custom_constants::login_page_url);

	echo form_input($username_email_input);
	echo form_password($password_input);

	if(validation_errors())
	{
		echo "<div class='form-errors'>";
		echo validation_errors();
		echo "</div>";
	}

	if(isset($auth_failed))
	{
		echo "<div class='form-errors'>";
		echo "<p>Invalid Username/Password</p>";
		echo "</div>";
	}
	
	if($this->session->flashdata('timed_out'))
	{
		if($this->session->flashdata('timed_out') === 'TRUE')
		{
			echo "<div class='form-errors'>";
			echo "<p>Session timed out</p>";
			echo "</div>";
		}
	}
	
	echo "<div class='form-options'>";
	echo form_submit("submit", "login");
	
	// Close form-options
	echo "</div>";

	if(custom_constants::registration_disable === FALSE)
	{
		echo "<p class='register-link'>or <a href='" . base_url() . custom_constants::register_url . "'>register</a></p>";
	}

	// Close the form
	echo form_close();	
}

// Close the login container
echo "</div>";

echo "<div class='login-options'>";

echo "<p>";
if(custom_constants::email_login_allowed === FALSE)
{
	echo "<a href='" . base_url() . custom_constants::forgot_username_url . "'>forgot username</a> | ";
}

echo "<a href='" . base_url() . custom_constants::reset_password_form_url . "'>reset password</a> | ";

if(defined('custom_constants::main_site_url'))
{
	if(defined('custom_constants::main_site_display'))
	{
		// Link to the main site with full access
		echo "<a href='" . custom_constants::main_site_url . "'>" . custom_constants::main_site_display . "</a>";
	}
	else
	{
		// Link to the main site with full access
		echo "<a href='" . custom_constants::main_site_url . "'>" . custom_constants::main_site_display . "</a>";
	}
}

echo "</p>";
// Close the login-options
echo "</div>";


?>