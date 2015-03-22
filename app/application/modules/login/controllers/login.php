<?php

// If access is requested from anywhere other than index.php then exit
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
 |--------------------------------------------------------------------------
 |	CONTROLLER SUMMARY AND DATABASE TABLES
 |--------------------------------------------------------------------------
 | 
 |	Used to authenticate user logins. Features IP blacklisting, email address
 |	whitelisting, register user, reset password, email verification and
 |	username reminder emails.
 |
 |	Configuration can be found inside the library called custom_constants.php
 |
 |	Module can be downloaded from GitHub at https://github.com/lewnelson/CI-hmvc-login-module
 |
 |
 |	Default database is called login. This can be changed in mdl_login.php
 |
 |	Database table structure
 |
 |	Table name(s) - login, ip_blacklist, email_whitelist
 |
 |	ai = auto_increment
 |	pk = primary_key
 |	null = value can be set to null if not set assume not null
 |
 |	Table - login
 |
 |	||==================================================================================================||
 |	|| column						| type (flags)			| description								||
 |	||==================================================================================================||
 |	|| id							| int (ai, pk)			| id primary_key							||
 |	||------------------------------+-----------------------+-------------------------------------------||
 |	|| first_name					| varchar(64)			| Users first name							||
 |	||------------------------------+-----------------------+-------------------------------------------||
 |	|| surname						| varchar(64)			| Users surname								||
 |	||------------------------------+-----------------------+-------------------------------------------||
 |	|| username						| varchar(24)			| Users username							||
 |	||------------------------------+-----------------------+-------------------------------------------||
 |	|| password_hash				| varchar(256)			| Users password hash						||
 |	||------------------------------+-----------------------+-------------------------------------------||
 |	|| account_type					| varchar(32)			| Account type (custom string)				||
 |	||------------------------------+-----------------------+-------------------------------------------||
 |	|| email						| varchar(320)			| User email address						||
 |	||------------------------------+-----------------------+-------------------------------------------||
 |	|| email_verification_link		| varchar(64) null		| sha1 email verification link				||
 |	||------------------------------+-----------------------+-------------------------------------------||
 |	|| email_ver_time				| varchar(15) null		| Time email verification link was created	||
 |	||								|						| unix timestamp format						||
 |	||------------------------------+-----------------------+-------------------------------------------||
 |	|| email_verified				| varchar(3) null		| Whether user has verified email. NULL or	||
 |	||								|						| yes										||
 |	||------------------------------+-----------------------+-------------------------------------------||
 |	|| accnt_create_time			| varchar(15)			| When the account was registered			||
 |	||------------------------------+-----------------------+-------------------------------------------||
 |	|| passwd_reset_str				| varchar(64) null		| sha1 email password reset link			||
 |	||------------------------------+-----------------------+-------------------------------------------||
 |	|| passwd_reset_time			| varchar (15) null		| Time password reset link was created		||
 |	||								|						| unix timestamp format						||
 |	||==================================================================================================||
 |
 |
 |
 |	Table - ip_blacklist
 |
 |	||==================================================================================================||
 |	|| column						| type (flags)			| description								||
 |	||==================================================================================================||
 |	|| id							| int (ai, pk)			| id primary_key							||
 |	||------------------------------+-----------------------+-------------------------------------------||
 |	|| ip_address					| varchar(15)			| Users IP address							||
 |	||------------------------------+-----------------------+-------------------------------------------||
 |	|| failed_attempts				| int					| Number of failed attempts from IP address	||
 |	||------------------------------+-----------------------+-------------------------------------------||
 |	|| lock_time					| varchar(15)			| Time when IP was locked out. Unix			||
 |	||								|						| timestamp									||
 |	||------------------------------+-----------------------+-------------------------------------------||
 |	|| last_login_attempt			| varchar(15)			| Time when user last attempted login. Unix	||
 |	||								|						| timestamp									||
 |	||==================================================================================================||
 |
 |
 |
 |	Table - email_whitelist
 |
 |	||==================================================================================================||
 |	|| column						| type (flags)			| description								||
 |	||==================================================================================================||
 |	|| id							| int (ai, pk)			| id primary_key							||
 |	||------------------------------+-----------------------+-------------------------------------------||
 |	|| email						| varchar(320)			| User email address						||
 |	||------------------------------+-----------------------+-------------------------------------------||
 |	|| account_type					| varchar (32)			| Account type (custom string)				||
 |	||==================================================================================================||
 |
 */
 
 
class Login extends MX_Controller {

	// Configuration properties used in blacklisting
	private $num_login_attempts;
	private $ip_address;
	private $logged_in;
	
	function __construct() {
		parent::__construct();
		$this->ip_address = $this->session->userdata("ip_address");

		// If logged in then set logged_in to TRUE otherwise set to FALSE
		if($this->session->userdata('logged_in'))
		{
			$this->logged_in = TRUE;
		}
		else
		{
			$this->logged_in = FALSE;
		}
	}

    function index() {
		$data = array();
		
		// Check if currently timed out
		$data['timeout_left'] = $this->_check_blacklist($this->ip_address);
		
		if($this->logged_in === TRUE)
		{
			redirect(base_url() . custom_constants::admin_page_url);
		}
		
		if($this->input->post('username/email') or $this->input->post('password'))
		{
			$this->load->library("form_validation");
			
			if(custom_constants::email_login_allowed === TRUE)
			{				
				// Check if user has entered email or username
				if(strpos($this->input->post('username/email'), '@'))
				{
					// Email was entered
					$type = 'email';
					$this->form_validation->set_rules('username/email', 'email', 'required|maxlength[320]|valid_email');
				}
				else
				{
					// Username was entered
					$type = 'username';
					$this->form_validation->set_rules('username/email', 'username', 'required|maxlength[24]|alpha_dash');
				}
			}
			else
			{
				// Treat input as username
				$type = 'username';
				$this->form_validation->set_rules('username/email', 'username', 'required|maxlength[24]|alpha_dash');
			}
			
			$this->form_validation->set_rules('password', 'password', 'required|maxlength[32]');
			
			if($this->form_validation->run())
			{
				$username = strtolower($this->input->post("username/email"));
				$password = $this->input->post("password");
				
				if($this->_validate_login($username, $password, $type))
				{
					if(!empty($this->session->userdata('requested_url')))
					{
						// Get requested URL, remove it from the session and redirect to it 302
						$req_url = $this->session->userdata('requested_url');
						$this->session->unset_userdata('requested_url');
						redirect($req_url);
					}
					else
					{
						redirect(base_url() . custom_constants::admin_page_url);
					}
				}
				else
				{
					// Authentication failed so we update our blacklist
					if($this->_update_blacklist() === FALSE)
					{
						// If max login attempts reached then reload the login page
						redirect(base_url() . custom_constants::login_page_url);
					}
					
					// Set data auth_failed to let view know authentication failed
					$data['auth_failed'] = TRUE;
				}
			}
		}
		
		$data['meta_title'] = "Login";
		$data['meta_description'] = "Login to the admin panel";
		
		$data['modules'][] = "login";
		$data['methods'][] = "view_login_default";
		
		echo Modules::run("templates/login_template", $data);
    }
	
	private function _check_blacklist() {
		if(custom_constants::num_login_attempts === FALSE)
		{
			return FALSE;
		}
		
		// See if the IP address is on the blacklist
		$this->load->model("login/mdl_login");
		$this->mdl_login->set_table("ip_blacklist");
		if($this->mdl_login->count_where("ip_address", $this->ip_address) > 0)
		{			
			$timeout = custom_constants::black_list_timeout;
			$reset_time = custom_constants::black_list_reset_time;
			
			$current_time = time();
			
			$query = $this->mdl_login->get_where_custom("ip_address", $this->ip_address);
			foreach($query->result() as $row)
			{
				$login_attempts = $row->failed_attempts;
				if(!$row->lock_time !== NULL)
				{
					$locked_out_since = $row->lock_time;
				}
				$time_last_attempt = $row->last_login_attempt;
			}
			
			$time_since_last_attempt = ($current_time - $time_last_attempt) / 60;
			if($time_since_last_attempt > $reset_time)
			{
				$this->_remove_ip_blacklist();
			}
			
			if($login_attempts > custom_constants::num_login_attempts)
			{
				$time_waited = ($current_time - $locked_out_since) / 60;
				
				if($time_waited < $timeout)
				{
					$timeout_left = $timeout - $time_waited;
					return $timeout_left;
				}
				else
				{
					$this->_remove_ip_blacklist();
					return FALSE;
				}
			}
			else
			{				
				// IP address is not blacklisted.
				return FALSE;
			}
		}
		else
		{
			// IP address is not blacklisted.
			return FALSE;
		}
	}
	
	private function _remove_ip_blacklist() {
		$this->load->model("login/mdl_login");
		$this->mdl_login->set_table("ip_blacklist");
		$this->mdl_login->delete_where("ip_address", $this->ip_address);
	}
	
	private function _update_blacklist() {
		if(custom_constants::num_login_attempts === FALSE)
		{
			return TRUE;
		}
		
		$last_login_attempt = time();
		
		$this->load->model("login/mdl_login");
		$this->mdl_login->set_table("ip_blacklist");
		if($this->mdl_login->count_where("ip_address", $this->ip_address) > 0)
		{
			$query = $this->mdl_login->get_where_custom("ip_address", $this->ip_address);
			foreach($query->result() as $row)
			{
				$id = $row->id;
				$failed_attempts = $row->failed_attempts;
			}
			
			if($failed_attempts == custom_constants::num_login_attempts)
			{
				$update_data['lock_time'] = time();
			}
			
			$update_data['failed_attempts'] = $failed_attempts + 1;
			$update_data['last_login_attempt'] = $last_login_attempt + 1;
			$this->mdl_login->_update($id, $update_data);
			
			if(isset($update_data['lock_time']))
			{
				// User locked out
				return FALSE;
			}
		}
		else
		{
			$failed_attempts = 1;
			$insert_data = array(
								"ip_address" => $this->ip_address,
								"failed_attempts" => $failed_attempts,
								"last_login_attempt" => $last_login_attempt
							);
			
			$this->mdl_login->_insert($insert_data);
		}
	}
	
	private function _validate_login($username, $password, $type) {
		// type is username or email
		$this->load->model("login/mdl_login");
		$this->mdl_login->set_table("login");
		
		if($this->mdl_login->count_where($type, $username) > 0)
		{
			$query = $this->mdl_login->get_where_custom($type, $username);
			foreach($query->result() as $row)
			{
				$user_id = $row->id;
				$account_type = $row->account_type;
				$user_username = $row->username;
				$hashed_pass = $row->password_hash;
				$passwd_reset_str = $row->passwd_reset_str;
				
				if($row->email_verified === "yes")
				{
					$email_verified = TRUE;
				}
				else
				{
					$email_verified = FALSE;
				}
			}
			
			if(password_verify($password, $hashed_pass) === TRUE)
			{
				$session_data = array(
									'user_id' => $user_id,
									'username' => $user_username,
									'logged_in' => TRUE,
									'account_type' => $account_type,
									'last_activity' => time(),
									'email_verified' => $email_verified,
									'logged_in_since' => date('H:i d-m-y')
								);

				$this->session->set_userdata($session_data);
				$this->_remove_ip_blacklist();
				
				if($passwd_reset_str !== NULL)
				{
					$this->mdl_login->set_table("login");
					
					$update_data['passwd_reset_str'] = NULL;
					$update_data['passwd_reset_time'] = NULL;
					
					$this->mdl_login->_update($user_id, $update_data);
				}
				
				// Successful login
				return TRUE;
			}
			else
			{
				// Invalid password
				return FALSE;
			}
		}
		else
		{
			// Invalid username/email
			return FALSE;
		}
	}
	
	function register_user_form() {
		if(custom_constants::registration_disable === TRUE)
		{
			redirect(base_url() . custom_constants::login_page_url);
		}
		
		if($this->logged_in === TRUE)
		{
			$data['logged_in'] = TRUE;
		}
		else
		{
			$data['logged_in'] = FALSE;
		}
		
		$data['registered'] = FALSE;
		
		if($this->input->post('first_name'))
		{
			$data['values_posted']['first_name'] = $this->input->post('first_name');
			$data['values_posted']['surname'] = $this->input->post('surname');
			$data['values_posted']['username'] = $this->input->post('username');
			$data['values_posted']['email'] = $this->input->post('email');
			
			$this->load->library("form_validation");
			
			$this->form_validation->set_rules('first_name', 'first name', 'required|maxlength[64]|alpha_dash');
			$this->form_validation->set_rules('surname', 'surname', 'required|maxlength[64]|minlength[2]|alpha_dash');
			$this->form_validation->set_rules('username', 'username', 'required|maxlength[32]|minlength[3]|alpha_dash');
			$this->form_validation->set_rules('email', 'email', 'required|maxlength[320]|valid_email|matches[email_confirmation]');
			$this->form_validation->set_rules('email_confirmation', 'confirm email', 'required|maxlength[320]|valid_email');
			$this->form_validation->set_rules('password', 'password', 'required|minlength[8]|maxlength[32]|matches[password_confirmation]');
			$this->form_validation->set_rules('password_confirmation', 'confirm password', 'required|minlength[8]|maxlength[32]');
			
			if($this->form_validation->run())
			{
				$post_data['first_name'] = $this->input->post('first_name');
				$post_data['surname'] = $this->input->post('surname');
				$post_data['username'] = $this->input->post('username');
				$post_data['password'] = $this->input->post('password');
				$post_data['email'] = $this->input->post('email');
				
				$reg_user = $this->_register_user($post_data);
				
				if($reg_user === FALSE)
				{
					// Successfully registered
					$data['registered'] = TRUE;
				}
				else
				{
					// Registration error
					$data['form_error'] = $reg_user;
				}
			}
		}
		
		$data['meta_title'] = "Register";
		$data['meta_description'] = "New user registration";
		
		$data['modules'][] = "login";
		$data['methods'][] = "view_login_register";
		
		echo Modules::run("templates/login_template", $data);
	}
	
	private function _register_user($data) {
		$this->load->model("login/mdl_login");
		$this->mdl_login->set_table("login");
		
		if($this->mdl_login->count_where("username", $data['username']) > 0)
		{
			return "username has been taken";
		}
		
		if($this->mdl_login->count_where("email", $data['email']) > 0)
		{
			return "email is already in use";
		}
		
		$insert_data['first_name'] = $data['first_name'];
		$insert_data['surname'] = $data['surname'];
		$insert_data['username'] = strtolower($data['username']);	// Usernames are case insensitive so always make them lower case
		$insert_data['email'] =  $data['email'];
		$insert_data['password_hash'] = password_hash($data['password'], PASSWORD_DEFAULT);
		$insert_data['accnt_create_time'] = time();
		$white_list_account = $this->_email_whitelisted($data['email']);
		
		if($white_list_account === FALSE)
		{
			if(custom_constants::white_list === FALSE)
			{
				$insert_data['account_type'] = custom_constants::default_account_type;
				
				$insert_data['email_verification_link'] = $this->_create_email_ver_string($data['username'], $data['email']);
				$insert_data['email_ver_time'] = time();
			}
			else
			{
				return "Your email address is not on the white list for registration. If you
				believe this information is wrong please contact the site administrator.";
			}
		}
		else
		{
			$insert_data['email_verified'] = "yes";
			$insert_data['account_type'] = $white_list_account;
		}
		
		$this->mdl_login->set_table("login");
		$this->mdl_login->_insert($insert_data);
		
		$query = $this->mdl_login->get_where_custom("email", $data['email']);
		foreach($query->result() as $row)
		{
			$user_id = $row->id;
			$username = $row->username;
			$account_type = $row->account_type;
			if($row->email_verified === "yes")
			{
				$email_verified = TRUE;
			}
			else
			{
				$email_verified = FALSE;
			}
		}
		
		$session_data = array(
							'user_id' => $user_id,
							'username' => $username,
							'logged_in' => TRUE,
							'account_type' => $account_type,
							'last_activity' => time(),
							'email_verified' => $email_verified,
							'logged_in_since' => time()
						);
		
		$this->session->set_userdata($session_data);
		
		// Successful registration. User is also logged in
		return FALSE;
	}
	
	private function _email_whitelisted($email) {
		$this->load->model("mdl_login");
		$this->mdl_login->set_table("email_whitelist");
		
		if($this->mdl_login->count_where("email", $email) > 0)
		{
			$query = $this->mdl_login->get_where_custom("email", $email);
			foreach($query->result() as $row)
			{
				$account_type = $row->account_type;
			}
		}
		else
		{
			// Email address is not on whitelist
			return FALSE;
		}
		
		return $account_type;
	}
	
	function email_verification($ver_string = FALSE) {
		check_user_login(FALSE);
		
		if($this->session->userdata('email_verified') === TRUE)
		{
			$data['email_already_verified'] = TRUE;
		}
		else
		{
			$data['email_already_verified'] = FALSE;
			
			if($ver_string === FALSE)
			{
				$data['string_entered'] = FALSE;
			}
			else
			{
				$data['string_entered'] = TRUE;
				$string_check = $this->_check_email_ver_string($ver_string);
				
				if($string_check === FALSE)
				{
					$data['email_verified'] = TRUE;
				}
				else
				{
					$data['email_verified'] = FALSE;
					$data['email_ver_error'] = $string_check;
				}
			}
		}
		
		$data['meta_title'] = "Verify Email";
		$data['meta_description'] = "Email verification";
		
		$data['modules'][] = "login";
		$data['methods'][] = "view_verify_email";
		
		echo Modules::run("templates/login_template", $data);
	}
	
	private function _check_email_ver_string($ver_string)
	{
		$this->load->model("login/mdl_login");
		$this->mdl_login->set_table("login");
		$query = $this->mdl_login->get_where_custom("username", $this->session->userdata("username"));
		
		foreach($query->result() as $row)
		{
			$user_id = $row->id;
			$username = $row->username;
			$email_address = $row->email;
			$email_verification_link = $row->email_verification_link;
			$email_ver_time = $row->email_ver_time;
		}
		
		if($ver_string === $email_verification_link)
		{
			$time_elapsed = ((time() - $email_ver_time) / 60) / 60;		// In hours
			
			$valid_time = custom_constants::email_ver_string_time;
			if($time_elapsed < $valid_time)
			{
				$update_data['email_verification_link'] = NULL;
				$update_data['email_ver_time'] = NULL;
				$update_data['email_verified'] = "yes";
				$this->session->set_userdata('email_verified', TRUE);
				
				$this->mdl_login->_update($user_id, $update_data);
				
				// Email verified
				return FALSE;
			}
			else
			{
				// String expired create a new one and email
				$new_email_hash_string = $this->_create_email_ver_string($username, $email_address);
				
				$update_data['email_verification_link'] = $new_email_hash_string;
				$update_data['email_ver_time'] = time();
				
				$this->mdl_login->_update($user_id, $update_data);
				
				return "Verification link expired. New link was sent to {$email_address}. Don't forget
				to check your spam folder.";
			}
		}
		else
		{
			return "Verification link does not match. Please check the link or request a new
			link to be emailed by clicking the link below.";
		}
	}
	
	function send_new_verification_email() {
		if($this->logged_in === FALSE)
		{
			redirect(base_url() . custom_constants::login_page_url);
		}
		
		$username = $this->session->userdata("username");
		$this->load->model("mdl_login");
		$this->mdl_login->set_table("login");
		$query = $this->mdl_login->get_where_custom("username", $username);
		foreach($query->result() as $row)
		{
			$id = $row->id;
			$email = $row->email;
		}
		
		$new_link = $this->_create_email_ver_string($username, $email);
		$update_data['email_verification_link'] = $new_link;
		$this->mdl_login->_update($id, $update_data);
		
		$data['email'] = $email;
		
		$data['meta_title'] = "New Email Verification Sent";
		$data['meta_description'] = "Email verification resent";
		
		$data['modules'][] = "login";
		$data['methods'][] = "view_new_verify_email";
		
		echo Modules::run("templates/login_template", $data);
	}
	
	function change_email_address() {
		if($this->logged_in === FALSE)
		{
			redirect(base_url() . custom_constants::login_page_url);
		}
		
		$username = $this->session->userdata("username");
		$this->load->model("mdl_login");
		$this->mdl_login->set_table("login");
		$query = $this->mdl_login->get_where_custom("username", $username);
		foreach($query->result() as $row)
		{
			$id = $row->id;
			$email_verified = $row->email_verified;
			$data['old_email'] = $row->email;
		}
		
		if($email_verified === "yes")
		{
			redirect(base_url() . custom_constants::admin_page_url);
		}
		
		$data['new_email_successful'] = FALSE;
		$data['email_exists'] = FALSE;
		
		if($this->input->post('email'))
		{
			$this->load->library("form_validation");
			
			$data['values_posted']['email'] = $this->input->post('email');
			
			$this->form_validation->set_rules('email', 'email', 'required|maxlength[320]|valid_email|matches[email_confirmation]');
			$this->form_validation->set_rules('email_confirmation', 'confirm email', 'required|maxlength[320]|valid_email');
			
			if($this->form_validation->run())
			{
				if($this->mdl_login->count_where("email", $this->input->post("email")) > 0)
				{
					$data['email_exists'] = TRUE;
					$data['values_posted']['email'] = '';
				}
				else
				{
					$update_data['email'] = $this->input->post('email');
					$data['new_email_successful'] = TRUE;
					$data['new_email'] = $this->input->post('email');
				
					$new_link = $this->_create_email_ver_string($username, $data['new_email']);
					$update_data['email_verification_link'] = $new_link;
					$this->mdl_login->_update($id, $update_data);
				}
			}
		}
		
		$data['meta_title'] = "Change Email Address";
		$data['meta_description'] = "Change your email address";
		
		$data['modules'][] = "login";
		$data['methods'][] = "view_change_email_address";
		
		echo Modules::run("templates/login_template", $data);
	}
	
	private function _create_email_ver_string($username, $email) {
		$string_pt1 = $username;
		$string_pt2 = rand(100000, 999999);

		$hash_string = sha1($string_pt1 . $string_pt2);
		
		$this->load->library('email');
		$this->email->from(custom_constants::mailer_address, custom_constants::mailer_name);
		$this->email->to($email);
		
		$site_cn = base_url();
		$this->email->subject("Verify email for {$username}");
		$this->email->message("Paste this link in your browser to verify your email address. {$site_cn}" . custom_constants::email_verification_url . "/{$hash_string}");
		
		$this->email->send();
		
		return $hash_string;
	}
	
	function forgot_username() {
		if(custom_constants::email_login_allowed === TRUE)
		{
			redirect(base_url() . custom_constants::login_page_url);
		}
		
		if($this->logged_in === TRUE)
		{
			$data['logged_in'] = TRUE;
		}
		else
		{
			$data['logged_in'] = FALSE;
		}
		
		if($this->input->post('email'))
		{
			$data['values_posted']['email'] = $this->input->post('email');
			
			$this->load->library("form_validation");
			$this->form_validation->set_rules('email', 'email', 'required|maxlength[320]|valid_email');
			
			if($this->form_validation->run())
			{
				$email = $this->input->post('email');
				$data['email_errors'] = $this->_email_credentials($email, "username");
			}
		}
		
		$data['meta_title'] = "Username Recovery";
		$data['meta_description'] = "Enter email to recover username";
		
		$data['modules'][] = "login";
		$data['methods'][] = "view_forgot_username";
		
		echo Modules::run("templates/login_template", $data);
	}
	
	private function _email_credentials($email, $type) {
		$this->load->model("login/mdl_login");
		$this->mdl_login->set_table("login");
		
		if($this->mdl_login->count_where("email", $email) > 0)
		{
			$query = $this->mdl_login->get_where_custom("email", $email);
			foreach($query->result() as $row)
			{
				$id = $row->id;
				$first_name = $row->first_name;
				$username = $row->username;
			}
		}
		else
		{
			return "{$email} is not registered.";
		}
		
		$this->load->library('email');
		$this->email->from(custom_constants::mailer_address, custom_constants::mailer_name);
		$this->email->to($email);
		$site_cn = base_url();
		
		if($type === "username")
		{			
			$this->email->subject("Forgot username request for {$username}");
			$this->email->message("Hi {$first_name}, your username is {$username}. To login paste this link into your browser {$site_cn}" . custom_constants::login_page_url);
			$this->email->send();
		}
		else if($type === "password")
		{
			$string_pt1 = $username;
			$string_pt2 = rand(100000, 999999);
			$hash_string = sha1($string_pt1 . $string_pt2);
			
			$update_data['passwd_reset_str'] = $hash_string;
			$update_data['passwd_reset_time'] = time();
			
			$this->mdl_login->_update($id, $update_data);
			
			$this->email->subject("Reset password request for {$username}");
			$this->email->message("Hi {$first_name}, this is an automated email sent because you requested a password reset.
			To reset your password paste this link into your browser {$site_cn}" . custom_constants::reset_password_url . "/{$hash_string}");
			$this->email->send();
		}
		else
		{
			echo "email_credentials type is invalid";
		}
		
		// Email successfully sent
		return FALSE;
	}
	
	function reset_password_form() {
		if($this->logged_in === TRUE)
		{
			redirect(base_url() . custom_constants::admin_page_url);
		}
		
		$data = array();
		
		if($this->input->post('email'))
		{
			$data['values_posted']['email'] = $this->input->post('email');
			
			$this->load->library("form_validation");
			$this->form_validation->set_rules('email', 'email', 'required|maxlength[320]|valid_email');
			
			if($this->form_validation->run())
			{
				$email = $this->input->post('email');
				$data['email_errors'] = $this->_email_credentials($email, "password");
			}
		}
		
		$data['meta_title'] = "Reset password";
		$data['meta_description'] = "Request a password reset";
		
		$data['modules'][] = "login";
		$data['methods'][] = "view_reset_password_form";
		
		echo Modules::run("templates/login_template", $data);
	}
	
	function reset_password($verification_string = FALSE) {		
		if($this->logged_in === TRUE)
		{
			redirect(base_url() . custom_constants::admin_page_url);
		}
		
		$reset_password_form_url = base_url() . custom_constants::reset_password_form_url;
		
		if($verification_string === FALSE)
		{
			redirect($reset_password_form_url);
		}
		
		$data['new_link_sent'] = FALSE;
		$data['success_reset'] = FALSE;
		
		$this->load->model("login/mdl_login");
		$this->mdl_login->set_table("login");
		if($this->mdl_login->count_where("passwd_reset_str", $verification_string) > 0)
		{
			$query = $this->mdl_login->get_where_custom("passwd_reset_str", $verification_string);
			foreach($query->result() as $row)
			{
				$id = $row->id;
				$username = $row->username;
				$email = $row->email;
				$passwd_reset_time = $row->passwd_reset_time;
			}
			
			$current_time = time();
			
			$time_dif = $current_time - $passwd_reset_time;//) / 60) / 60;	// In hours
			$valid_time = custom_constants::passwd_reset_valid_time;
			
			if($time_dif > $valid_time)
			{
				$this->_email_credentials($email, "password");
				$data['new_link_sent'] = TRUE;
				$data['email'] = $email;
			}
		}
		else
		{
			redirect($reset_password_form_url);
		}
		
		if($this->input->post('password'))
		{
			$data['values_posted']['email'] = $this->input->post("email");
			
			$this->load->library("form_validation");
			$this->form_validation->set_rules('email', 'email', 'required|maxlength[320]|valid_email');
			$this->form_validation->set_rules('password', 'password', 'required|minlength[8]|maxlength[32]|matches[password_confirmation]');
			$this->form_validation->set_rules('password_confirmation', 'confirm password', 'required|minlength[8]|maxlength[32]');
			
			if($this->form_validation->run())
			{
				if($this->input->post("email") !== $email)
				{
					$data['form_errors'] = "email address does not match the address attached to this link";
				}
				else
				{					
					$password = $this->input->post("password");
					$update_data['password_hash'] = password_hash($password, PASSWORD_DEFAULT);
					$update_data['passwd_reset_str'] = NULL;
					$update_data['passwd_reset_time'] = NULL;
					
					$this->mdl_login->_update($id, $update_data);
					
					$data['success_reset'] = TRUE;
				}
			}
		}
		
		$data['verification_string'] = $verification_string;
		
		$data['meta_title'] = "Reset your password";
		$data['meta_description'] = "Enter a new password for your account";
		
		$data['modules'][] = "login";
		$data['methods'][] = "view_reset_password";
		
		echo Modules::run("templates/login_template", $data);
	}
	
	function user_logout() {
		// Destroy the session and redirect the user to the login page
		$this->session->sess_destroy();
		redirect(base_url() . custom_constants::login_page_url);
	}
	
	function view_login_default() {
		$this->load->view("login/login_default");
	}
	
	function view_login_register() {
		$this->load->view("login/login_register");
	}
	
	function view_forgot_username() {
		$this->load->view("login/forgot_username");
	}
	
	function view_reset_password_form() {
		$this->load->view("login/reset_password_form");
	}
	
	function view_reset_password() {
		$this->load->view("login/reset_password");
	}
	
	function view_verify_email() {
		$this->load->view("login/verify_email");
	}
	
	function view_new_verify_email() {
		$this->load->view("login/new_verify_email");
	}
	
	function view_change_email_address() {
		$this->load->view("login/change_email_address");
	}
}
