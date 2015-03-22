<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	http://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There area two reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router what URI segments to use if those provided
| in the URL cannot be matched to a valid route.
|
*/

$route['default_controller'] = "login";
$route['404_override'] = 'error/error_404';

// DO NOT CHANGE THESE. THESE ROUTES STOP DIRECT ACCESS TO METHODS
$route['login/register_user_form'] = "";
$route['login/email_verification'] = "";
$route['login/send_new_verification_email'] = "";
$route['login/change_email_address'] = "";
$route['login/email_verification/(:any)'] = "";
$route['login/forgot_username'] = "";
$route['login/reset_password_form'] = "";
$route['login/reset_password'] = "";
$route['login/reset_password/(:any)'] = "";
$route['login/view_(:any)'] = "";
$route['templates'] = "";
$route['templates/(:any)'] = "";
// END DO NOT CHANGE THESE. THESE ROUTES STOP DIRECT ACCESS TO METHODS

// Routes below can be changed. Just make sure to reflect your changes in /application/libraries/custom_constants.php
$route['register'] = "login/register_user_form";

$route['email_verification'] = "login/email_verification";
$route['email_verification/(:any)'] = "login/email_verification/$1";
$route['send_new_verification_email'] = "login/send_new_verification_email";
$route['change_email_address'] = "login/change_email_address";

$route['forgot_username'] = "login/forgot_username";
$route['reset_password_form'] = "login/reset_password_form";
$route['reset_password'] = "login/reset_password";
$route['reset_password/(:any)'] = "login/reset_password/$1";

$route['login/user_logout'] = "";
$route['logout'] = "login/user_logout";

$route['admin_panel'] = "";
$route['admin'] = "admin_panel";


/* End of file routes.php */
/* Location: ./application/config/routes.php */