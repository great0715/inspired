<?php
	//messages processings
	require_once('../system_load.php');
	//loading system.
	$new_project = new Project;
	extract($_POST);
	
	if($file_name == '') { 
		HEADER('LOCATION: ../project_detail.php?project_id='.$project_id.'&message=File name is required.');
		exit();
	}
	
	//Profile Image Processing.
	if(isset($_FILES['project_file']) && $_FILES['project_file'] != '') { 
		$allowedExts = array("gif", "jpeg", "jpg", "png");
		$temp = explode(".", $_FILES["project_file"]["name"]);
		$extension = end($temp);

		if (($_FILES["project_file"]["size"] < 2048000)) {
 			 if ($_FILES["project_file"]["error"] > 0) {
    			$message = "Return Code: " . $_FILES["project_file"]["error"];
    	} else 	{
			$phrase = substr(md5(uniqid(rand(), true)), 16, 16);
	  if (file_exists("../project_files/" .$phrase.$_FILES["project_file"]["name"])) {
	      $message = $_FILES["project_file"]["name"] . " already exists. ";
      } else {
		  move_uploaded_file($_FILES["project_file"]["tmp_name"],
		  "../project_files/".date('y-m-d-h-i-s').$phrase.str_replace(' ', '-',$_FILES["project_file"]["name"]));
		  $project_file = "project_files/".date('y-m-d-h-i-s').$phrase.str_replace(' ', '-', $_FILES["project_file"]["name"]);
	  } //if file not exist already.
	  
    } //if file have no error
  }//if file type is alright.
  HEADER('LOCATION: ../project_detail.php?project_id='.$project_id.'&message=Please select a file to upload.');
} //if image was uploaded processing.
/*Image processing ends here.*/

	$ret_message = $new_project->add_project_file($file_name, $file_description, $project_file, $project_id);
	
	if($include_member){
	foreach($include_member as $user_id) { 
		$new_user = new Users;
		$mailto = $new_user->get_user_info($user_id, 'email');
		
		$project_name = $new_project->get_project_info($project_id, 'project_name');
		
		$subject = "New file added to ".$project_name;
		
		$message = '<h2>New file added.</h2>';
		$message .= '<p>New file was added to project $project_name please login your account to see or download the file.</p>';
		$message .= '<hr />';
		$message .= $file_description;
		send_email($mailto, $subject, $message);
	} //sending email ends here.
	}
	HEADER('LOCATION: ../project_detail.php?project_id='.$project_id.'&message='.$ret_message);