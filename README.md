# CI hmvc login module
A login module for CodeIgniter with Wiredesignz MX extensions - HMVC

###**DESCRIPTION**

This is a login module designed and built to be used with Wiredesignz MX extensions for CodeIgniter. The module is contains the same setup as the demo version which can be found here.

It features user registration, password resets, forgotten username reminders, IP blacklisting, email address whitelisting, user timeouts, multiple account types and email verification. All of the features are customisable and some can be turned off entirely which makes this module very flexible.

###**FULL FEATURES**

#####IP blacklisting

This feature can be turned off/on. By default a user is allowed 10 failed login attempts before their IP address is blacklisted. Their IP address is then removed from the blacklist after n minutes. The default length of time is 15 minutes. If a user has had for example 5 failed attempts and they leave then return after 1 hour the next failed login attempt will reset the counter to 1. The default reset time is 1 hour but this can also be changed.

#####User registration

This feature can be turned off/on. This allows anybody to register using the registration form. Once a user has registered they will assume the default account type which by default is set to basic.

#####Email address whitelist

This feature can be turned off/on. This feature is turned off by default. However it is always on, but not in strict mode. When it is turned on inside the configuration it will enter strict mode whereby if users try to register they must have their email on the whitelist. If it is not in strict mode and a user who is on the whitelist tries to register they will assume the account type that has been assigned to them via the whitelist.

#####Password reset

This feature is always on. It requires the use of PHP mail. Registered users can request a password reset by submitting their email address. If the supplied email address is not registered they will be prompted with an error. The password reset works by creating a unique hashed link associated with the account. The link is valid for 24 hours by default.

#####Forgot username

If logging in using email is not enabled and users must login using their username then they will have the option to retrieve their username. The user submits their email address and if the email address is registered they will be sent an email telling them their username.

#####Email verification

When a user registers if they are not on the whitelist they must verify their email. They will be automatically sent an email with a verification link much like the password reset link. If the user is not logged in when they follow the link they will be prompted to login first. The link is valid for 24 hours by default. Once the time is up they will be sent another link.

#####User timeout

By default a user will be timed out 30 minutes after last activity. Last activity is updated in the check_user_login function which should be run in every __construct / function where you require a user to be logged on.

#####Multiple account types

These can be completely customised with an endless number of account types. By default there is only one account which is the account every user receives when they sign up. This is the basic account. If you wish to specify different accounts this can be done through whitelisting.

###**REQUIREMENTS**

 - sendmail for PHP's mail() function.

###**SETUP**

There are a number of steps for initial setup so I will list them here.
 1. Setup the login database with the tables. I have supplied a .sql file to import all the blank tables.
 2. Setup your default database. I have separated the login database from your apps default database for security reasons. This shouldn't affect how active record should use your default database though. Database user for login requires SELECT, UPDATE, INSERT and DELETE.
 2. Change base_url inside /application/config/config.php.
 3. Setup the CodeIgniter session encryption key.
 3. Change database settings inside /application/config/database.php. By default the app is setup to use a database called login. This can be changed by modifying /application/modules/login/models/mdl_login.php then changing the database to load inside the __construct method.
 4. Check /application/config/routes.php and modify default settings if you wish. Otherwise just leave them.
 5. Check /application/libraries/custom_constants.php modify default settings you wish to change. If you have changed any route settings then make sure to reflect these changes in the URL constants.
 6. This should be all that is required to get the application functioning. The next section will cover further configuration.

###**FURTHER CONFIGURATION**

Once you have got the app setup with your desired settings you can begin to build your application around it. If you choose to follow the structuring I have used by using templates then each module will run the templates controller with a specified template passing the $data used to specify meta title and description. The $data will also tell the content view what modules and methods to run to get the view content.

If you want to make a module only accessable for users logged in then just add check_user_login(); to the __construct method. This runs the function from inside the check_user_login_helper and checks if the user is logged in and that they have verified their email address. If you want the user to have logged in but they don't need to have verified their email address then just run the function passing FALSE as an argument like this check_user_login(FALSE); This way they will be able to access the module without having the verify their email address. Just remember check_user_login() checks email verification by default.
