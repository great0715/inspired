<?php
	//messages processings
	require_once('../system_load.php');
	//loading system.
	$new_project = new Project;
	extract($_POST);
	
	if($title == '') { 
		HEADER('LOCATION: ../project_todo_lists.php?project_id='.$project_id.'&list_id='.$list_id.'&message=Task title is required.');
		exit();
	} else if($assign == '') { 
		HEADER('LOCATION: ../project_todo_lists.php?project_id='.$project_id.'&list_id='.$list_id.'&message=Assign task to a member.');
		exit();
	}
	$ret_message = $new_project->add_tasks($start_date, $end_date, $title, $description, $assign, $list_id, $project_id);
	
	$new_user = new Users;
	$first_name = $new_user->get_user_info($assign_to, 'first_name');
	$last_name = $new_user->get_user_info($assign_to, 'last_name');
	
	if(isset($include_member)) {	
	foreach($include_member as $user_id) { 
		$new_user = new Users;
		$mailto = $new_user->get_user_info($user_id, 'email');
		
		$project_name = $new_project->get_project_info($project_id, 'project_name');
		
		$subject = "New Task added to ".$project_name;
		
		$message = '<h2>New task on project '.$project_name.'.</h2>';
		$message .= '<p>New Task on project '.$project_name.' was assigned to <strong>'.$first_name.' '.$last_name.'</strong>.</p>';
		$message .= '<hr />';
		$message .= '<h3>'.$title.'</h3>';
		$message .= '<p>'.$description.'</p>';
		send_email($mailto, $subject, $message);
	} //sending email ends here.
	} //if ends here.
	HEADER('LOCATION: ../project_todo_lists.php?project_id='.$project_id.'&list_id='.$list_id.'&message='.$ret_message);