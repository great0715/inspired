<?php
	include('system_load.php');
	//Including this file we load system.
	/*
	Logout function if called.
	*/
	if(isset($_GET['logout']) && $_GET['logout'] == 1) { 
		session_destroy();
		HEADER('LOCATION: '.get_option('redirect_on_logout'));
		exit();
	} //Logout done.
	
	//user Authentication.
	authenticate_user('admin');
	
	$new_user = new Users;//New user object.
	$new_level = new Userlevel;
	$notes_obj = new Notes;
	$message_obj = new Messages;
	$new_project = new Project;
	
	$page_title = $language['dashboard_title']; //You can edit this to change your page title.
	require_once("includes/header.php"); //including header file.
?>

	<div class="page-header">
        <h2><?php echo $language['system_information']; ?></h2>
     </div>
     
     <div class="row">
        <div class="col-lg-2">
            <div class="alert alert-info" style="font-size:105%;">
              <?php echo $language['total_users']; ?> <span  style="font-size:100%;" class="badge"><?php $new_user->get_total_users('all');?></span>
            </div>
        </div>
        
        <div class="col-lg-2">
            <div class="alert alert-info" style="font-size:105%;">
              <?php echo $language['active_users']; ?> <span  style="font-size:100%;" class="badge"><?php $new_user->get_total_users('activate');?></span>
            </div>
        </div>
        
        <div class="col-lg-2">
            <div class="alert alert-info" style="font-size:105%;">
              <?php echo $language['deactivate_users']; ?> <span  style="font-size:100%;" class="badge"><?php $new_user->get_total_users('deactivate');?></span>
            </div>
        </div>
        
        <div class="col-lg-2">
            <div class="alert alert-info" style="font-size:105%;">
              <?php echo $language['ban_users']; ?> <span  style="font-size:100%;" class="badge"><?php $new_user->get_total_users('ban');?></span>
            </div>
        </div>
        
        <div class="col-lg-2">
            <div class="alert alert-info" style="font-size:105%;">
              <?php echo $language['suspend_users']; ?> <span  style="font-size:100%;" class="badge"><?php $new_user->get_total_users('suspend');?></span>
            </div>
        </div>
	</div>
	
		<hr />
     <div class="row clearfix">
      <div class="col-sm-6">
         <!--level starts here.-->
         <div class="panel panel-primary">
            <div class="panel-heading">
              <h3 class="panel-title"><?php echo $language['my_notes']; ?></h3>
            </div>
            <div class="list-group">
			 	<?php $notes_obj->notes_widget(); ?>
          </div>
       </div> <!--mynotes ends here.-->
       </div>
       <!--level starts here.-->
      <div class="col-sm-6">
         <div class="panel panel-primary">
            <div class="panel-heading">
              <h3 class="panel-title"><?php echo $language['messages']; ?></h3>
            </div>
            <div class="list-group">
			 	<?php $message_obj->message_widget(); ?>
          </div>
       </div> <!--mynotes ends here.-->    
      </div><!--row ends here.-->                    

<div class="col-sm-12">
 <div class="panel panel-primary">
    <div class="panel-heading">
      <h3 class="panel-title">Your Active Tasks</h3>
    </div>
    <div class="list-group">
    	<table class="table table-hover">
        	<tr>
            	<th>Deadline</th>
                <th>Title</th>
                <th>Description</th>
                <th>Project</th>
            </tr>
            <?php $new_project->active_tasks(); ?>
        </table>
  </div>
</div> <!--mynotes ends here.-->

<?php
	require_once("includes/footer.php");
?>