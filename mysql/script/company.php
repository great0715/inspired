<?php
	include('system_load.php');
	//This loads system.
	
	//user Authentication.
	authenticate_user('subscriber');
	//creating company object.
	$new_company = new Company;
	
	if(isset($_GET['message']) && $_GET['message'] != '') { 
		$message = 'Please select your company.';
	}//Message ends here select company
	
	//delete company if exist.
	if(isset($_POST['delete_company']) && $_POST['delete_company'] != '') { 
		$message = $new_company->delete_company($_POST['delete_company']);
	}//delete account.
		
	$page_title = "Companies"; //You can edit this to change your page title.
	require_once("includes/header.php"); //including header file.
	?>

	<?php
    //display message if exist.
        if(isset($message) && $message != '') { 
            echo '<div class="alert alert-success">';
            echo $message;
            echo '</div>';
        }
    ?>
    <?php if(partial_access('admin')) { ?><p>
	    <a href="manage_company.php" class="btn btn-primary btn-default">Add New</a>
    </p><?php } ?>

    <table cellpadding="0" cellspacing="0" border="0" class="table-responsive table-hover table display table-bordered" id="wc_table" width="100%">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Type</th>
                <th>City</th>
                <th>Phone</th>
                <th>Email</th>
                <th>Logo</th>
                <th>Projects</th>
                <?php if(partial_access('admin')) { ?><th>Edit</th>
                <th>Delete</th><?php } ?>
            </tr>
        </thead>
        <tbody>
           <?php echo $new_company->list_companies(); ?>
        </tbody>
    </table>
                        
<?php
	require_once("includes/footer.php");
?>