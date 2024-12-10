<?php
	include('system_load.php');
	//This loads system.
	
	//user Authentication.
	authenticate_user('admin');
	//creating company object.
	$new_company = new Company;
	//creating user object.
	$new_user = new Users;
	//new user access company object. 
	$new_company_access = new CompanyAccess;
	//add access
	if(isset($_POST['user_id']) && isset($_POST['company_id'])) { 
		if($_POST['user_id'] == '' && $_POST['company_id'] == '') { 
			$message = 'Company id and user id required. Please select.';
		} else { 
			$message =  $new_company_access->add_company_access($_POST['user_id'], $_POST['company_id']);
		}
	}//add company access ends here.
	//delete access
	if(isset($_POST['delete_access']) && $_POST['delete_access'] != '') { 
		$message = $new_company_access->delete_access($_POST['delete_access']);
	}
	//delete access ends here.	
	$page_title = "Manage Companies Access"; //You can edit this to change your page title.
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

                    <h3>Grant Access</h3>
                    <form name="grand_access" id="grand_access" action="" method="post">
                    <table cellpadding="10" border="0">
                    	<tr>
                        	<th>Select User</th>
                            <th>Select Company</th>
                        </tr>
                        <tr>
                        	<td>
                            	<select name="user_id" required="required">
                                	<option value="">Select User</option>
                                    <?php $new_user->subscriber_options(); ?>
                                </select>
                            </td>
                            <td>
                            	<select name="company_id" required="required">
                                	<option value="">Select Company</option>
                                    <?php $new_company->company_options(); ?>
                                </select>
                            </td>
                            <tr>
                            	<td><input type="submit" class="btn btn-primary btn-sm" value="Grant Access" /></td>
                                <td>&nbsp;</td>
                            </tr>
                        </tr>
                    </table>
                    </form>
                    <br />
					<br />
					<table cellpadding="0" cellspacing="0" border="0" class="table-responsive table-hover table display table-bordered" id="wc_table" width="100%">	
                        <thead>
                            <tr>
                                <th>User Id</th>
                                <th>User Name</th>
                                <th>Email</th>
                                <th>Company Access</th>
                                <th>Delete</th>
                            </tr>
                        </thead>
                        <tbody>
							<?php $new_company_access->list_company_access(); ?>
                        </tbody>
                    </table>
                 </div>
                  <script type="text/javascript">
						$(document).ready(function() {
						// validate the register form
					$("#grand_access").validate();
						});
                    </script>
                <div class="clear"></div><!--clear Float-->
            </div><!--admin wrap ends here.-->
                        
<?php
	require_once("includes/footer.php");
?>