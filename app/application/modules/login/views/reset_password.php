<?php

// If access is requested from anywhere other than index.php then exit
if ( ! defined('BASEPATH')) exit('No direct script access allowed');


$input['email'] = array(
						"name" => "email",
						"placeholder" => "email address",
						"maxlength" => "320",
						"required" => "required"
					);
					
$input['password'] = array(
						"name" => "password",
						"placeholder" => "password",
						"maxlength" => "32",
						"required" => "required"
					);
					
$input['password_confirmation'] = array(
						"name" => "password_confirmation",
						"placeholder" => "confirm password",
						"maxlength" => "32",
						"required" => "required"
					);
	
// If form has been submitted with errors populate fields that were already filled
if(isset($values_posted))
{
	foreach($values_posted as $post_name => $post_value)
	{
		$input[$post_name]['value'] = $post_value;
	}
}

echo "<div id='login-container'>";

if($new_link_sent === TRUE)
{
	header('Refresh: 5; URL=' . base_url() . custom_constants::login_page_url);
	echo "<p>This link has expired. A new reset password link has been sent to {$email}. Please check your spam inbox
	as well. You will now be redirected to the login page.</p>
	<p>Please <a href='" . base_url() . custom_constants::login_page_url . "'>click here</a> if you are not redirected.</p>";
}
else
{
	if($success_reset === TRUE)
	{
		header('Refresh: 5; URL=' . base_url() . custom_constants::login_page_url);
		echo "<p>Thanks. Your password has been successfully reset.
		You will now be redirected to the login page.</p>
		<p>Please <a href='" . base_url() . custom_constants::login_page_url . "'>click here</a> if you are not redirected.</p>";
	}
	else
	{
		echo form_open(custom_constants::reset_password_url . "/{$verification_string}");
		echo form_input($input['email']);
		echo form_password($input['password']);
		echo form_password($input['password_confirmation']);
		
		if(isset($form_errors))
		{
			echo "<div class='form-errors'>";
			echo "<p>" . $form_errors . "</p>";
			echo "</div>";
		}
		
		if(validation_errors())
		{
			echo "<div class='form-errors'>";
			echo validation_errors();
			echo "</div>";
		}
		
		echo "<div class='form-options'>";
		echo form_submit("submit", "reset password");
		echo "</div>";
		
		echo "<p class='register-link'><a href='" . base_url() . custom_constants::login_page_url . "'>cancel</a></p>";
		
		echo form_close();
	}
}

echo "</div>";

if(defined('custom_constants::main_site_url'))
{
	echo "<div class='login-options'>";
	echo "<p>";
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
	echo "</p>";
	echo "</div>";
}

?>