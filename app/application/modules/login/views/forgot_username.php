<?php

// If access is requested from anywhere other than index.php then exit
if ( ! defined('BASEPATH')) exit('No direct script access allowed');


// Form input settings
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

if($logged_in === TRUE)
{
	echo "You are already logged in. To check your username go to your account settings.";
}
else
{
	if(isset($email_errors))
	{
		if($email_errors === FALSE)
		{
			header('Refresh: 10; URL=' . base_url() . custom_constants::login_page_url);
			echo "Thanks. You should receive and email shortly reminding you of your username.
			Remember to check your spam inbox. You will now be redirected back to the login page.
			please <a href='" . base_url() . custom_constants::login_page_url . "'>click here</a> if you are not redirected.";
			
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
		echo form_open(custom_constants::forgot_username_url);
		echo form_input($input['email']);
		echo form_submit("submit", "send username");
		
		echo "<a href='" . base_url() . custom_constants::login_page_url . "'>cancel</a>";
		echo form_close();
		if($no_errors === FALSE)
		{
			echo $email_errors;
		}
		echo validation_errors();
	}
}

echo "</div>";

?>