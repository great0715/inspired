<?php
	if(get_option('version') != '2.1') { 
		set_option('version', '2.1');
		set_option('redirect_on_logout', 'index.php');
		set_option('register_user_level', 'subscriber');
		set_option('session_timeout', '180');
		set_option('maximum_login_attempts', '10');
		set_option('wrong_attempts_time', '10');
	}