<?php

// If access is requested from anywhere other than index.php then exit
if ( ! defined('BASEPATH')) exit('No direct script access allowed');


$input['first_name'] = array(
						"name" => "first_name",
						"placeholder" => "first name(s) *",
						"maxlength" => "64",
						"required" => "required"
					);

$input['surname'] = array(
						"name" => "surname",
						"placeholder" => "surname *",
						"maxlength" => "64",
						"required" => "required"
					);

$input['username'] = array(
						"name" => "username",
						"placeholder" => "username *",
						"maxlength" => "24",
						"required" => "required"
					);
					
$input['email'] = array(
						"name" => "email",
						"placeholder" => "email address *",
						"maxlength" => "320",
						"required" => "required"
					);

$input['email_confirmation'] = array(
						"name" => "email_confirmation",
						"placeholder" => "confirm email address *",
						"maxlength" => "320",
						"required" => "required"
					);
					
$input['password'] = array(
						"name" => "password",
						"placeholder" => "password *",
						"maxlength" => "32",
						"required" => "required"
					);
					
$input['password_confirmation'] = array(
						"name" => "password_confirmation",
						"placeholder" => "confirm password *",
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

if($logged_in === TRUE)
{
	echo "<p>You are already registered and logged in.</p>";
	echo "<p><a href='" . base_url() . custom_constants::admin_page_url . "'>Click here</a>
	to go to the admin page.</a>";
}
else
{
// If user has successfully registered
if($registered === TRUE)
{
	if($email_verified === TRUE)
	{
		header('Refresh: 5; URL=' . base_url() . custom_constants::admin_page_url);
		echo "<p>Thank you for registering. You are now setup to use the site.</p>";
		echo "<p>You will now be directed to the admin page.</p>";
		echo "<p>Please <a href='" . base_url() . custom_constants::admin_page_url . "'>click here</a> if you are not redirected.</p>";
	}
	else
	{
		echo "<p>You have successfully registered. You should receive an email
		with a link to verify your email address. You must verify your email
		address before you can access the site.</p>";
		echo "<p>Make sure to check your spam folder just in case your email
		has been filtered.</p>";
		echo "<p>The email verification link is valid for 24 hours.</p>";
		echo "<p>If you need a new verification email sent then please <a href='" . base_url() . custom_constants::new_email_ver_link_url . "'>click here</a>.</p>";
		echo "<a href='" . base_url() . custom_constants::logout_url . "'>logout</a>";
	}
}
else
{
		// Create the register user form
		echo form_open(custom_constants::register_url);
		
		echo "<p>* denotes required field.</p>";

		// Create our fields
		echo form_input($input['first_name']);
		echo form_input($input['surname']);
		echo form_input($input['username']);
		echo form_input($input['email']);
		echo form_input($input['email_confirmation']);
		echo form_password($input['password']);
		echo form_password($input['password_confirmation']);

		if(isset($form_error))
		{
			echo "<div class='form-errors'>";
			echo "<p>" . $form_error . "</p>";
			echo "</div>";
		}
		
		if(validation_errors())
		{
			echo "<div class='form-errors'>";
			echo validation_errors();
			echo "</div>";
		}
		
		// Add the submit button
		echo "<div class='form-options'>";
		echo form_submit("submit", "register");
		echo "</div>";
		
		echo "<p class='register-link'>already registered? <a href='" . base_url() .custom_constants::login_page_url .  "'>login</a></p>";

		// Close the form
		echo form_close();
	}
}

// Close the login container
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