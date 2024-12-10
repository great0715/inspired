<?php
	//messages processings
	require_once('../system_load.php');
	//loading system.

	//message object.
	$new_project = new Project;
	extract($_POST);
	
	foreach($project_member as $member) { 
		if(!filter_var($member, FILTER_VALIDATE_EMAIL)) {
			if($member != '') { 
			echo 'Email is not valid '.$member;
			exit();
			}
		}//if all emails are valid in member section.
	}
	
	if(isset($del_user_access)) {
		foreach($del_user_access as $user_id) { 
			$new_project->delete_project_access($user_id, $project_id);
		}
	}
	
	//processing members here.
			if(isset($project_member)) {
			foreach($project_member as $member) { 
				global $db;
				$query = "SELECT * from users WHERE email='".$member."'";
				$result = $db->query($query) or die($db->error);
				$num_rows = $result->num_rows;
				
				if($num_rows > 0) { 
					//add user access here.
					$row = $result->fetch_array();
					$new_project->add_project_access($row['user_id'], $project_id);
					
					$project_log = "Project access granted to user ".$row['email'];
					 project_log($project_id, $project_log);
					
				} else if($member != '') { 
					//add new user here.
					$email = $member;
					$email_split = explode('@', $member);
					$username = $email_split[0];
					$active_pass = substr(md5(uniqid(rand(), true)), 6, 6);
					$registration_date = date('Y-m-d');
					$password = md5($active_pass);
					
					
					$query = "INSERT INTO users(user_id, first_name, username, email, password, date_register, user_type, status) ".		"VALUES(NULL, '$username',  '$username', '$email', '$password', '$registration_date', 'subscriber', 'activate')";
			$result = $db->query($query) or die($mysqli->error);
			$user_id = $db->insert_id;
			$new_project->add_project_access($user_id, $project_id);
			$project_log = "Project access granted to user ".$email;
			project_log($project_id, $project_log);
			
			$site_url = get_option('site_url');
			$site_name = get_option('site_name');
			
			$email_message = "<h1>".$site_name."</h1>";
			$email_message .= "You are registered to manage project.<br />";
			$email_message .= "Your Username is: <strong>".$username.'</strong>';
			$email_message .= "Your Email is: <strong>".$email.'</strong>';
			$email_message .= "Your Password is: <strong>".$active_pass.'</strong>';
			$email_message .= "Kindly click the link below to login into your account and start managing our project.<br />";
			$email_message .= "<a href='".$site_url."'>Login here</a>";
			$email_message .= "<br><br>Thank you again. Please contact us if you need any assistance.";
			
			$subject = "Account created.";
			send_email($email, $subject, $email_message);
				}
			}//foreach members ends here.
			}
			echo 'Members updated successfuly.';