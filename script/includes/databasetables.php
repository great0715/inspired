<?php
if($db->query('SELECT 1 from user_meta') == FALSE) { 
		$query = 'CREATE TABLE user_meta (
			`user_meta_id` bigint(20) NOT NULL AUTO_INCREMENT,
			`user_id` bigint(20) NOT NULL,
			`message_email` varchar(50) NOT NULL,
			`last_login_time` datetime NOT NULL,
			`last_login_ip` varchar(120) NOT NULL,
  			`login_attempt` bigint(20) NOT NULL,
			`login_lock` varchar(50) NOT NULL,
			PRIMARY KEY (`user_meta_id`)
		)';	
		$result = $db->query($query) or die($db->error);
		echo 'User Meta Table created.<br>';
	}  //Creating user notes table ends here.
	
	if($db->query('SELECT 1 from message_meta') == FALSE) { 
		$query = 'CREATE TABLE message_meta (
			`msg_meta_id` bigint(20) NOT NULL AUTO_INCREMENT,
			`message_id` bigint(20) NOT NULL,
			`status` varchar(100) NOT NULL,
			`from_id` bigint(20) NOT NULL,
			`to_id` bigint(20) NOT NULL,
  			`subject_id` bigint(20) NOT NULL,
			PRIMARY KEY (`msg_meta_id`)
		)';	
		$result = $db->query($query) or die($db->error);
		echo 'Message Meta Table created.<br>';
	}  //Creating user notes table ends here.
	
	if($db->query('SELECT 1 from messages') == FALSE) { 
		$query = 'CREATE TABLE messages (
			`message_id` bigint(20) NOT NULL AUTO_INCREMENT,
			`message_datetime` datetime NOT NULL,
			`message_detail` varchar(1000) NOT NULL,
			PRIMARY KEY (`message_id`)
		)';	
		$result = $db->query($query) or die($db->error);
		echo 'Messages Table created.<br>';
	}  //Creating user notes table ends here.
	
	if($db->query('SELECT 1 from subjects') == FALSE) { 
		$query = 'CREATE TABLE subjects (
			`subject_id` bigint(20) NOT NULL AUTO_INCREMENT,
			`subject_title` varchar(600) NOT NULL,
  			PRIMARY KEY (`subject_id`)
		)';	
		$result = $db->query($query) or die($db->error);
		echo 'Subjects Table created.<br>';
	}  //Creating user notes table ends here.
	
	if($db->query('SELECT 1 from project_notes') == FALSE) { 
		$query = 'CREATE TABLE project_notes (
			`note_id` bigint(20) NOT NULL AUTO_INCREMENT,
			`note_date` date NOT NULL,
			`note_title` varchar(200) NOT NULL,
			`note_detail` varchar(1600) NOT NULL,
			`project_id` bigint(20) NOT NULL,
			`user_id` bigint(20) NOT NULL,
  			PRIMARY KEY (`note_id`)
		)';	
		$result = $db->query($query) or die($db->error);
		echo 'Notes Table created.<br>';
	}  //Creating user notes table ends here.
	
	if($db->query('SELECT 1 from notes') == FALSE) { 
		$query = 'CREATE TABLE notes (
			`note_id` bigint(20) NOT NULL AUTO_INCREMENT,
			`note_date` date NOT NULL,
			`note_title` varchar(200) NOT NULL,
			`note_detail` varchar(600) NOT NULL,
			`user_id` bigint(20) NOT NULL,
  			PRIMARY KEY (`note_id`)
		)';	
		$result = $db->query($query) or die($db->error);
		echo 'Notes Table created.<br>';
	}  //Creating user notes table ends here.
	
	if($db->query('SELECT 1 from logins') == FALSE) { 
		$query = 'CREATE TABLE logins (
			`login_id` bigint(20) NOT NULL AUTO_INCREMENT,
			`login_title` varchar(200) NOT NULL,
			`login_type` varchar(200) NOT NULL,
			`login_url` varchar(400) NOT NULL,
			`login_username` varchar(200) NOT NULL,
			`login_password` varchar(200) NOT NULL,
			`other_info` varchar(600) NOT NULL,
			`user_id` bigint(20) NOT NULL,
  			PRIMARY KEY (`login_id`)
		)';	
		$result = $db->query($query) or die($db->error);
		echo 'Logins Table created.<br>';
	}  //Creating user notes table ends here.
	
	if($db->query('SELECT 1 from announcements') == FALSE) { 
		$query = 'CREATE TABLE announcements (
			`announcement_id` bigint(20) NOT NULL AUTO_INCREMENT,
			`announcement_date` date NOT NULL,
			`announcement_title` varchar(200) NOT NULL,
			`announcement_detail` varchar(1000) NOT NULL,
			`user_type` varchar(100) NOT NULL,
			`announcement_status` varchar(50) NOT NULL,
  			PRIMARY KEY (`announcement_id`)
		)';	
		$result = $db->query($query) or die($db->error);
		echo 'Notes Table created.<br>';
	}  //Creating user notes table ends here.
	
	//if database tables does not exist already create them.
	if($db->query('SELECT 1 from options') == FALSE) {
		$query = 'CREATE TABLE options (
			`option_id` bigint(20) NOT NULL AUTO_INCREMENT,
			`option_name` varchar(500) NOT NULL,
			`option_value` varchar(500) NOT NULL,
  			PRIMARY KEY (`option_id`)
		)';	
		$result = $db->query($query) or die($db->error);
		echo 'Options Table created.<br>';
	} //creating options table.
	
	if($db->query('SELECT 1 from users') == FALSE) { 
		$query = 'CREATE TABLE users (
			`user_id` bigint(20) NOT NULL AUTO_INCREMENT,
			`first_name` varchar(100) NOT NULL,
			`last_name` varchar(100) NOT NULL,
			`gender` varchar(50) NOT NULL,
			`date_of_birth` date NOT NULL,
			`address1` varchar(200) NOT NULL,
			`address2` varchar(200) NOT NULL,
			`city` varchar(100) NOT NULL,
			`state` varchar(100) NOT NULL,
			`country` varchar(100) NOT NULL,
			`zip_code` varchar(100) NOT NULL,
			`mobile` varchar(200) NOT NULL,
			`phone` varchar(200) NOT NULL,
			`username` varchar(100) NOT NULL,
			`email` varchar(200) NOT NULL,
			`password` varchar(200) NOT NULL,
			`profile_image` varchar(500) NOT NULL,
			`description` varchar(600) NOT NULL,
			`status` varchar(100) NOT NULL,
			`activation_key` varchar(100) NOT NULL,
			`date_register` date NOT NULL,
			`user_type` varchar(100) NOT NULL,
  			PRIMARY KEY (`user_id`)
		)';	
		$result = $db->query($query) or die($db->error);
		echo 'Users Table created.<br>';
	}  //Creating users table ends here.
	
	//if database tables does not exist already create them.
	if($db->query('SELECT 1 from user_level') == FALSE) {
		$query = 'CREATE TABLE user_level (
			`level_id` bigint(20) NOT NULL AUTO_INCREMENT,
			`level_name` varchar(200) NOT NULL,
			`level_description` varchar(600) NOT NULL,
			`level_page` varchar(100) NOT NULL,
  			PRIMARY KEY (`level_id`)
		)';	
		$result = $db->query($query) or die($db->error);
		echo 'Options Table created.<br>';
	} //creating user level table ends.
	
	if($db->query('SELECT 1 from project_plan') == FALSE) { 
		$query = 'CREATE TABLE project_plan (
			`plan_id` bigint(20) NOT NULL AUTO_INCREMENT,
			`plan` varchar(1500) NOT NULL,
			`project_id` bigint(20) NOT NULL,
			PRIMARY KEY (`plan_id`)
		)';	
		$result = $db->query($query) or die($db->error);
		echo 'Project Plan Table created.<br>';
	}  //Creating user notes table ends here.
	
	if($db->query('SELECT 1 from task_meta') == FALSE) { 
		$query = 'CREATE TABLE task_meta (
			`task_meta_id` bigint(20) NOT NULL AUTO_INCREMENT,
			`task_id` bigint(20) NOT NULL,
			`discussion_id` bigint(20) NOT NULL,
			PRIMARY KEY (`task_meta_id`)
		)';	
		$result = $db->query($query) or die($db->error);
		echo 'Task Meta Table created.<br>';
	}  //Creating user notes table ends here.
	
	if($db->query('SELECT 1 from tasks') == FALSE) { 
		$query = 'CREATE TABLE tasks (
			`task_id` bigint(20) NOT NULL AUTO_INCREMENT,
			`start_date` date NOT NULL,
			`end_date` date NOT NULL,
			`title` varchar(200) NOT NULL,
			`description` varchar(1000) NOT NULL,
			`status` varchar(200) NOT NULL,
			`assigned_to` bigint(20) NOT NULL,
			`project_id` bigint(20) NOT NULL,
			`user_id` bigint(20) NOT NULL,
			`list_id` bigint(20) NOT NULL,
			PRIMARY KEY (`task_id`)
		)';	
		$result = $db->query($query) or die($db->error);
		echo 'Tasks Table created.<br>';
	}  //Creating user notes table ends here.
	
	if($db->query('SELECT 1 from todo_lists') == FALSE) { 
		$query = 'CREATE TABLE todo_lists (
			`list_id` bigint(20) NOT NULL AUTO_INCREMENT,
			`datetime` datetime NOT NULL,
			`title` varchar(200) NOT NULL,
			`description` varchar(1000) NOT NULL,
			`project_id` bigint(20) NOT NULL,
			`user_id` bigint(20) NOT NULL,
			PRIMARY KEY (`list_id`)
		)';	
		$result = $db->query($query) or die($db->error);
		echo 'Todo lists Table created.<br>';
	}  //Creating user notes table ends here.
	
	if($db->query('SELECT 1 from file_meta') == FALSE) { 
		$query = 'CREATE TABLE file_meta (
			`file_meta_id` bigint(20) NOT NULL AUTO_INCREMENT,
			`file_id` bigint(20) NOT NULL,
			`discussion_id` bigint(20) NOT NULL,
			PRIMARY KEY (`file_meta_id`)
		)';	
		$result = $db->query($query) or die($db->error);
		echo 'Message Meta Table created.<br>';
	}  //Creating user notes table ends here.
	
	if($db->query('SELECT 1 from discussion') == FALSE) { 
		$query = 'CREATE TABLE discussion (
			`discussion_id` bigint(20) NOT NULL AUTO_INCREMENT,
			`datetime` datetime NOT NULL,
			`detail` varchar(1000) NOT NULL,
			`project_id` bigint(20) NOT NULL,
			`user_id` bigint(20) NOT NULL,
			PRIMARY KEY (`discussion_id`)
		)';	
		$result = $db->query($query) or die($db->error);
		echo 'Messages Table created.<br>';
	}  //Creating user notes table ends here.
	
	//if database tables does not exist already create them.
	if($db->query('SELECT 1 from project_files') == FALSE) {
		$query = 'CREATE TABLE project_files (
			`file_id` bigint(20) NOT NULL AUTO_INCREMENT,
			`file_datetime` datetime NOT NULL,
			`file_name` varchar(500) NOT NULL,
			`description` varchar(600) NOT NULL,
			`file_url` varchar(700) NOT NULL,
			`project_id` bigint(20) NOT NULL,
			`user_id` bigint(20) NOT NULL,
  			PRIMARY KEY (`file_id`)
		)';	
		$result = $db->query($query) or die($db->error);
		echo 'Project Files Table created.<br>';
	} //creating user level table ends.
	
	//if database tables does not exist already create them.
	if($db->query('SELECT 1 from project_access') == FALSE) {
		$query = 'CREATE TABLE project_access (
			`pr_access_id` bigint(20) NOT NULL AUTO_INCREMENT,
			`user_id` bigint(20) NOT NULL,
			`project_id` bigint(20) NOT NULL,
  			PRIMARY KEY (`pr_access_id`)
		)';	
		$result = $db->query($query) or die($db->error);
		echo 'Project Access Table created.<br>';
	} //creating user level table ends.
	
	if($db->query('SELECT 1 from project_logs') == FALSE) { 
		$query = 'CREATE TABLE project_logs (
			`log_id` bigint(20) NOT NULL AUTO_INCREMENT,
			`log_datetime` datetime NOT NULL,

			`description` varchar(600) NOT NULL,
			`project_id` bigint(20) NOT NULL,
			`user_id` bigint(20) NOT NULL,
		PRIMARY KEY (`log_id`)
		)';	
		$result = $db->query($query) or die($db->error);
		echo 'Project Logs Table created.<br>';
	}  //Creating Projects logs table ends here.
	
	if($db->query('SELECT 1 from projects') == FALSE) { 
		$query = 'CREATE TABLE projects (
			`project_id` bigint(20) NOT NULL AUTO_INCREMENT,
			`project_manual_id` varchar(100) NOT NULL,
			`project_name` varchar(100) NOT NULL,
			`project_type` varchar(100) NOT NULL,
			`project_logo` varchar(500) NOT NULL,
			`description` varchar(1500) NOT NULL,
			`project_status` varchar(100) NOT NULL,
			`company_id` bigint(20) NOT NULL,
		PRIMARY KEY (`project_id`)
		)';	
		$result = $db->query($query) or die($db->error);
		echo 'Projects Table created.<br>';
	}  //Creating Projects table ends here.
	
	if($db->query('SELECT 1 from companies') == FALSE) { 
		$query = 'CREATE TABLE companies (
			`company_id` bigint(20) NOT NULL AUTO_INCREMENT,
			`company_manual_id` varchar(100) NOT NULL,
			`company_name` varchar(100) NOT NULL,
			`business_type` varchar(100) NOT NULL,
			`address1` varchar(200) NOT NULL,
			`address2` varchar(200) NOT NULL,
			`city` varchar(100) NOT NULL,
			`state` varchar(100) NOT NULL,
			`country` varchar(100) NOT NULL,
			`zip_code` varchar(100) NOT NULL,
			`phone` varchar(200) NOT NULL,
			`email` varchar(200) NOT NULL,
			`company_logo` varchar(500) NOT NULL,
			`description` varchar(600) NOT NULL,
			`user_id` varchar(100) NOT NULL,
		PRIMARY KEY (`company_id`)
		)';	
		$result = $db->query($query) or die($db->error);
		echo 'Companies Table created.<br>';
	}  //Creating users table ends here.
	
	//if database tables does not exist already create them.
	if($db->query('SELECT 1 from company_access') == FALSE) {
		$query = 'CREATE TABLE company_access (
			`access_id` bigint(20) NOT NULL AUTO_INCREMENT,
			`user_id` bigint(20) NOT NULL,
			`company_id` bigint(20) NOT NULL,
  			PRIMARY KEY (`access_id`)
		)';	
		$result = $db->query($query) or die($db->error);
		echo 'Company Access Table created.<br>';
	} //creating user level table ends.