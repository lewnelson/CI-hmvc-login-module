<?php

// If access is requested from anywhere other than index.php then exit
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
 |----------------------------------------------------------------------
 |
 |	Class is used for configuring global constants. Rather than use
 |	the CodeIgniter constants file this library should be used in
 |	case of updates to CodeIgniter overwriting the constants file.
 |
 |----------------------------------------------------------------------
 */

class Custom_Constants {
	
	/*	
	 |	FALSE or int. Number of login attempts before IP is
	 |	blacklisted. FALSE turns off IP blacklisting.
	 */
	 
		const num_login_attempts = 10;
		
	/*	-------------------------------------------------	*/
	
	/*	
	 |	Int. Number of minutes since last activity until
	 |	session is lost.
	 */
	 
		const user_timeout = 30;
		
	/*	-------------------------------------------------	*/
		
	/*	
	 |	Int. How many minutes an IP is blacklisted for
	 |	after being locked out. Setting only applied
	 |	if num_login_attempts is not FALSE.
	 */
		
		const black_list_timeout = 15;
		
	/*	-------------------------------------------------	*/
	
	/*	
	 |	Int. How many minutes with no activity until
	 |	a blacklisted IP is removed from the
	 |	blacklist. Setting only applied if
	 |	num_login_attempts is not FALSE.
	 */
		
		const black_list_reset_time = 60;
		
	/*	-------------------------------------------------	*/
	
	/*	
	 |	Int. Time in hours how long validation strings
	 |	are valid for.
	 */
		
		const passwd_reset_valid_time = 24;
		const email_ver_string_time = 24;
		
	/*	-------------------------------------------------	*/
	
	/*	
	 |	String. URL's for respective pages. Make sure to
	 |	reflect changes in your routes config. These
	 |	URL's are appended to CodeIgniters base_url()
	 */

		const login_page_url = "login";
		const admin_page_url = "admin";
		const forgot_username_url = "forgot_username";
		const reset_password_url = "reset_password";
		const reset_password_form_url = "reset_password_form";
		const email_verification_url = "email_verification";
		const register_url = "register";
		const logout_url = "logout";
		const new_email_ver_link_url = "send_new_verification_email";
		const change_email_before_ver_url = "change_email_address";
		
	/*	-------------------------------------------------	*/
	
	/*	
	 |	Array of pages which require login to access. This
	 |	will match any URI beginning with this string.
	 |
	 |	Example.	If "admin/portal" is in the array then,
	 |				http://domain.com/admin would not be blocked
	 |				http://domain.com/admin/portal would be blocked
	 |				http://domain.com/admin/portal/page would also be blocked
	 |
	 |	This array is used for determining whether it is safe
	 |	to display the 404 error page.
	 */

		public static $protected_pages = array(
											"admin"
										);
		
	/*	-------------------------------------------------	*/
		
	/*	
	 |	String. If your login is part of a public site
	 |	this URL should be set to the public site.
	 |
	 |	Example.	Login is login.example.com
	 |				Main site is www.example.com
	 |
	 |	Display is how it will be presented in HTML.
	 |	If display is not set then display will default
	 |	to the full URL.
	 |
	 |	Both of these constants can be removed if
	 |	you don't want to use them.
	 */
	 
		const main_site_url = "http://lewnelson.com";
		const main_site_display = "lewnelson.com";
		
	/*	-------------------------------------------------	*/

	/*	
	 |	Boolean. Turns on/off white listing for
	 |	new users registering. If a new user tries
	 |	to register with white list set to TRUE
	 |	and their email is not whitelisted then they
	 |	will not be able to register.
	 */
	 
		const white_list = FALSE;
		
	/*	-------------------------------------------------	*/

	/*	
	 |	String. The email address that any password resets,
	 |	forgotten username requests or email verifications
	 |	come from.
	 */
	 
		const mailer_address = 'donotreply@yourdomain.com';
		const mailer_name = 'donotreply';
		
	/*	-------------------------------------------------	*/

	/*	
	 |	String. Default account type. Set this to whatever
	 |	your default user type is for your application.
	 |	Or use the default 'basic'.
	 */
	 
		const default_account_type = 'basic';
		
	/*	-------------------------------------------------	*/
	
	/*	
	 |	Boolean. Enables or disables user registration.
	 */
	 
		const registration_disable = FALSE;
		
	/*	-------------------------------------------------	*/

	/*	
	 |	Boolean. Enables or disables ability for user to login
	 |	with their email address as well as their username.
	 */
	 
		const email_login_allowed = TRUE;
		
	/*	-------------------------------------------------	*/

}

?>
