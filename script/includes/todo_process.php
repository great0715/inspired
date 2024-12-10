<?php
	//messages processings
	require_once('../system_load.php');
	//loading system.
	$new_project = new Project;
	extract($_POST);
	
	if($todo_title == '') { 
		HEADER('LOCATION: ../project_detail.php?project_id='.$project_id.'&message=File name is required.');
		exit();
	}

	$ret_message = $new_project->add_project_todo($todo_title, $todo_description, $project_id);

	
	HEADER('LOCATION: ../project_detail.php?project_id='.$project_id.'&message='.$ret_message);