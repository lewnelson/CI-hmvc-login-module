<?php

// If access is requested from anywhere other than index.php then exit
if ( ! defined('BASEPATH')) exit('No direct script access allowed');


echo "<div id='login-container'>";

echo "<p>New email verifification link sent to {$email}.</p>";
echo "<p>If this email address is incorrect please change it by
<a href='". base_url() . custom_constants::change_email_before_ver_url . "'>clicking here</a>.</p>";

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