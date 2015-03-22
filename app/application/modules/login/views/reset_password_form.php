<?php

// If access is requested from anywhere other than index.php then exit
if ( ! defined('BASEPATH')) exit('No direct script access allowed');


$input['email'] = array(
						"name" => "email",
						"placeholder" => "email address",
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

if(isset($email_errors))
{
	if($email_errors === FALSE)
	{
		header('Refresh: 10; URL=' . base_url() . custom_constants::login_page_url);
		echo "<div class='login-message'>";
		echo "<p>Thanks. You should receive and email shortly with a link to reset your password.
		Remember to check your spam inbox. You will now be redirected back to the login page.</p>
		<p>Please <a href='" . base_url() . custom_constants::login_page_url . "'>click here</a> if you are not redirected.</p>";
		echo "</div>";
		
		// Don't display the form
		$show_form = FALSE;
		$no_errors = TRUE;
	}
	else
	{
		// Display the form
		$show_form = TRUE;
		$no_errors = FALSE;
		unset($input['email']['value']);
	}
}
else
{
	// Display the form
	$show_form = TRUE;
	$no_errors = TRUE;
}

if($show_form === TRUE)
{
	echo form_open(custom_constants::reset_password_form_url);
	echo form_input($input['email']);
	
	if($no_errors === FALSE)
	{
		echo "<div class='form-errors'>";
		echo "<p>" . $email_errors . "</p>";
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