<?php

// If access is requested from anywhere other than index.php then exit
if ( ! defined('BASEPATH')) exit('No direct script access allowed');


echo "<div id='login-container'>";

if($email_already_verified === TRUE)
{
	echo "<p>You have already verified your email address.</p>";
	echo "<p><a href='" . base_url() . custom_constants::admin_page_url . "'>Click here</a>
	to go to the admin page.</p>";
	echo "<p><a href='" . base_url() . custom_constants::logout_url . "'>logout</a></p>";
}
else
{
	if($string_entered === FALSE)
	{
		echo "<p>Please check your email for the verification link you were sent.</p>";
		echo "<p>If you need a new verification email sent then please <a href='" . base_url() . custom_constants::new_email_ver_link_url . "'>click here</a>.</p>";
		echo "<p>To change your email address <a href='". base_url() . custom_constants::change_email_before_ver_url . "'>click here</a>.</p>";
		echo "<p><a href='" . base_url() . custom_constants::admin_page_url . "'>admin</a></p>";
		echo "<p><a href='" . base_url() . custom_constants::logout_url . "'>logout</a></p>";
	}
	else
	{
		if($email_verified === TRUE)
		{
			header('Refresh: 5; URL=' . base_url() . custom_constants::admin_page_url);
			echo "<p>Thank you. You have successfully verified your email address. You now have access to the site.
			You will now be redirected to the admin panel.</p>";
			echo "<p>Please <a href='" . base_url() . custom_constants::admin_page_url . "'>click here</a> if you are not redirected.";
		}
		else
		{
			echo "<p>" . $email_ver_error . "</p>";
		}
	}
}

echo "</div>";

?>