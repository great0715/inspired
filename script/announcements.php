<?php
	include('system_load.php');
	//This loads system.
	
	//user Authentication.
	authenticate_user('admin');
	
	$announcement_obj = new Announcements;
	
	//Delete note.
	if(isset($_POST['delete_announcement']) && $_POST['delete_announcement'] != '') { 
		$message = $announcement_obj->delete_announcement($_POST['delete_announcement']); 
	}//delete ends here.
		
	$page_title = $language["announcements"]; //You can edit this to change your page title.
	require_once("includes/header.php"); //including header file.
	
	//display message if exist.
	if(isset($message) && $message != '') { 
	   echo '<div class="alert alert-success">';
	   echo $message;
	   echo '</div>';
	}
?>
    <p>
    <a href="manage_announcement.php" class="btn btn-primary btn-default"><?php echo $language["add_new"]; ?></a>
    </p>
   <?php $announcement_obj->list_announcements(); ?>

<?php
	require_once("includes/footer.php");
?>