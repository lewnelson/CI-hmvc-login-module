<?php

// If access is requested from anywhere other than index.php then exit
if ( ! defined('BASEPATH')) exit('No direct script access allowed');


echo "<div class='error-page'>";
echo "<h3>Error 404 Page Not Found</h3>";
echo "<p><a href='" . base_url() . custom_constants::admin_page_url . "'>admin page</a></p>";
echo "</div>";

?>