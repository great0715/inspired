<?php
	//messages processings
	require_once('../system_load.php');
	//loading system.
	$new_project = new Project;
	extract($_POST);
	
	if($note_title == '') { 
		HEADER('LOCATION: ../project_detail.php?project_id='.$project_id.'&message=Note title required.');
		exit();
	}
	
	if($note_detail == '') { 
		HEADER('LOCATION: ../project_detail.php?project_id='.$project_id.'&message=Note detail required.');
		exit();
	}
	
	if($project_id == '') { 
		HEADER('LOCATION: ../project_detail.php?project_id='.$project_id.'&message=Something is not right really.');
		exit();
	}
	
	
	$ret_message = $new_project->add_project_note($note_title, $note_detail, $project_id);
	
	if($include_member){
		foreach($include_member as $user_id) { 
			$new_user = new Users;
			$mailto = $new_user->get_user_info($user_id, 'email');
			
			$project_name = $new_project->get_project_info($project_id, 'project_name');
			
			$subject = "Project Note added to ".$project_name;
			
			$message = '<h2>Project Note added.</h2>';
			$message .= '<hr />';
			$message = '<h3>'.$note_title.'</h3>';
			$message .= '<p>'.$note_detail.'</p>';
			send_email($mailto, $subject, $message);
		} //sending email ends here.
	}
	HEADER('LOCATION: ../project_detail.php?project_id='.$project_id.'&message='.$ret_message);