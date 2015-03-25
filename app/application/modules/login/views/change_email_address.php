<?php

// If access is requested from anywhere other than index.php then exit
if ( ! defined('BASEPATH')) exit('No direct script access allowed');


$input['email'] = array(
						"name" => "email",
						"placeholder" => "email address",
						"maxlength" => "320",
						"required" => "required"
					);
					
$input['email_confirmation'] = array(
						"name" => "email_confirmation",
						"placeholder" => "confirm email",
						"maxlength" => "320",
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

if($new_email_successful === TRUE)
{
	echo "<p>You have successfully updated your email. An email verification
	link has been sent to.</p>";
	echo "<p>{$new_email}</p>";
	echo "<p>This link is valid for 24 hours.</p>";
	echo "<p><a href='" . base_url() . custom_constants::logout_url . "'>logout</a></p>";
}
else
{
	echo form_open(custom_constants::change_email_before_ver_url);
	echo "<p>Current email address {$old_email}.</p>";
	echo form_input($input['email']);
	echo form_input($input['email_confirmation']);
	
	if(validation_errors())
	{
		echo "<div class='form-errors'>";
		echo validation_errors();
		echo "</div>";
	}
	
	if($email_exists)
	{
		echo "<div class='form-errors'>";
		echo "<p>Email is already registered.</p>";
		echo "</div>";
	}
	
	echo "<div class='form-options'>";
	echo form_submit("submit", "change email");
	echo "</div>";
	
	echo "<p><a href='" . base_url() . custom_constants::new_email_ver_link_url . "'>resend verification link</a></p>";
	echo "<p><a href='" . base_url() . custom_constants::logout_url . "'>logout</a></p>";
	echo form_close();
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