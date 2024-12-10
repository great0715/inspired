<?php
	include('system_load.php');
	//This loads system.

	//user Authentication.
	authenticate_user('all');

	//User object.
	$new_user = new Users;

	//user level object
	$new_userlevel = new Userlevel;
	$notes_obj = new Notes;
	
	if(isset($_POST['profile_image']) && $_POST['profile_image'] != '') { 
		$pr_img = $_POST['profile_image'];
	}

//User update submission image processing edit user password setting if not changed.
if(isset($_GET['user_id']) && $_GET['user_id'] != '') { 
	if(isset($pr_img)) { 
		$pr_img = $pr_img;
	} else { 
		if(isset($_POST['already_img'])) { 
			$pr_img = $_POST['already_img'];
		} else { 
			$pr_img = '';
		}
	}
	if(isset($_POST['password']) && $_POST['password'] != '') { 
		if($_POST['password'] == $_POST['confirm_password']) { 
			$password_set = $_POST['password'];
		} else { 
			$message = $language['password_do_not_match'];
		}
	} else { 
		$password_set = '';
	}
	if(isset($_POST['update_user']) && $_POST['update_user'] == '1') {
	extract($_POST);
	if($password != $confirm_password){ 
		$message = $language['password_do_not_match'];
	} else {
	$message = $new_user->edit_profile($_SESSION['user_id'], $first_name, $last_name, $gender, $date_of_birth, $address1, $address2, $city, $state, $country, $zip_code, $mobile, $phone, $username, $email, $password_set, $pr_img, $description);
		if(isset($_POST['message_email_notification']) && $_POST['message_email_notification'] == '1') {
			$new_user->set_user_meta($_SESSION['user_id'], 'message_email', $_POST['message_email_notification']);
		} else { 
			$new_user->set_user_meta($_SESSION['user_id'], 'message_email', '0');
		}
		}
	}
}//update user submission.
	
	if(isset($_GET['user_id']) && $_GET['user_id'] != '') { 
		$new_user->set_user($_GET['user_id'], $_SESSION['user_type'], $_SESSION['user_id']);
	}//setting user data if editing. 	
	
	$page_title = $language['edit_your_profile']; //You can edit this to change your page title.
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
                    <div class="col-sm-8">
                    <form action="<?php $_SERVER['PHP_SELF']?>" id="add_user" name="user" method="post" enctype="multipart/form-data">

                    	<div class="form-group">
                        	<label class="control-label"><?php echo $language['first_name']; ?>*</label>
                            <input type="text" name="first_name" class="form-control" placeholder="<?php echo $language['enter_first_name']; ?>" value="<?php echo $new_user->first_name; ?>" required="required" />
                        </div>
                        
                        <div class="form-group">
                        	<label class="control-label"><?php echo $language['last_name']; ?>*</label>
                            <input type="text" name="last_name" class="form-control" placeholder="<?php echo $language['enter_last_name']; ?>" value="<?php echo $new_user->last_name; ?>" required="required" />
                        </div>
                        
                        <div class="form-group">
                        	<label class="control-label"><?php echo $language['gender']; ?></label>
                            <select class="form-control" name="gender">
                            	<option vale=''><?php echo $language['select_gender']; ?></option>
                                <option value="<?php echo $language['male']; ?>" <?php if($new_user->gender == $language['male']) { echo 'selected="selected"'; } ?>><?php echo $language['male']; ?></option>
                                <option value="<?php echo $language['female']; ?>" <?php if($new_user->gender == $language['female']) { echo 'selected="selected"'; } ?>><?php echo $language['female']; ?></option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                        	<label class="control-label"><?php echo $language['date_of_birth']; ?></label>
                            <input type="text" name="date_of_birth" class="form-control" placeholder="<?php echo $language['date_of_birth']; ?> 2013-12-03" value="<?php echo $new_user->date_of_birth; ?>" />
                        </div>
                        
                        <div class="form-group">
                        	<label class="control-label"><?php echo $language['address']; ?> 1</label>
                            <textarea name="address1" class="form-control" placeholder="<?php echo $language['address']; ?> 1"><?php echo $new_user->address1; ?></textarea>
                        </div>
                        
                        <div class="form-group">
                        	<label class="control-label"><?php echo $language['address']; ?> 2</label>
                            <textarea name="address2" class="form-control" placeholder="<?php echo $language['address']; ?> 2"><?php echo $new_user->address2; ?></textarea>
                        </div>
                        
                        <div class="form-group">
                        	<label class="control-label"><?php echo $language['city']; ?></label>
                            <input type="text" name="city" class="form-control" placeholder="<?php echo $language['city']; ?>" value="<?php echo $new_user->city; ?>" />
                        </div>
                        
                        <div class="form-group">
                        	<label class="control-label"><?php echo $language['state_province']; ?></label>
                            <input type="text" name="state" class="form-control" placeholder="<?php echo $language['state_province']; ?>" value="<?php echo $new_user->state; ?>" />
                        </div>
                        
                        <div class="form-group">
                        	<label class="control-label"><?php echo $language['country']; ?></label>
                            <input type="text" name="country" class="form-control" placeholder="<?php echo $language['country']; ?>" value="<?php echo $new_user->country; ?>" />
                        </div>
                        
                        <div class="form-group">
                        	<label class="control-label"><?php echo $language['zip_code']; ?></label>
                            <input type="text" name="zip_code" class="form-control" placeholder="<?php echo $language['zip_code']; ?>" value="<?php echo $new_user->zip_code; ?>" />
                        </div>
                        
                        <div class="form-group">
                        	<label class="control-label"><?php echo $language['mobile']; ?></label>
                            <input type="text" name="mobile" class="form-control" placeholder="<?php echo $language['mobile']; ?>" value="<?php echo $new_user->mobile; ?>" />
                        </div>
                        
                        <div class="form-group">
                        	<label class="control-label"><?php echo $language['phone']; ?></label>
                            <input type="text" name="phone" class="form-control" placeholder="<?php echo $language['phone']; ?>" value="<?php echo $new_user->phone; ?>" />
                        </div>
                        
                        <div class="form-group">
                        	<label class="control-label"><?php echo $language['username']; ?>*</label>
                            <input type="text" class="form-control" name="username" placeholder="<?php echo $language['username']; ?>" value="<?php echo $new_user->username; ?>" required="required" />
                        </div>
                        
                        <div class="form-group">
                        	<label class="control-label"><?php echo $language['email']; ?>*</label>
                            <input type="text" class="form-control" name="email" placeholder="<?php echo $language['your_email_address']; ?>" value="<?php echo $new_user->email; ?>" required="required" />
                        </div>
                       
                        <div class="form-group">
                        	<label class="control-label"><?php echo $language['password']; ?></label>
                            <input type="password" class="form-control" name="password" placeholder="<?php echo $language['password']; ?>" value="" /><small><?php echo $language['dont_want_to_change']; ?></small>                
                        </div>
                        
                        <div class="form-group">
                        	<label class="control-label"><?php echo $language['confirm_password']; ?></label>
                            <input class="form-control" type="password" name="confirm_password" placeholder="<?php echo $language['confirm_password']; ?>" value="" />
                        </div>
                       
                        <div class="form-group">
                        	<label class="control-label"><?php echo $language['profile_image']; ?></label>
                            <div class="clearfix"></div>
                           	<div class="clearfix"></div>
                            <div class="col-lg-4 ">
                                <div id="cropContaineroutput"></div>
                                <input type="hidden" name="profile_image" id="cropOutput" value="" />
                            </div>
                            	<?php
									if(isset($new_user->profile_image) && $new_user->profile_image != '') {
										echo '<a href="'.$new_user->profile_image.'" target="_blank"><img src="'.$new_user->profile_image.'" height="80" class="pull-left img-thumbnail" style="height:80px;" /></a><input type="hidden" name="already_img" value="'.$new_user->profile_image.'">';	
									}
								?>
                                <div class="clearfix"></div>
                        </div>
                        
                        <div class="form-group">
                        	<label class="control-label"><?php echo $language['description']; ?></label>
                            <textarea name="description" class="form-control" placeholder="<?php echo $language['description']; ?>"><?php echo $new_user->description; ?></textarea>
                        </div>
                        
                        <div class="form-group">
                        <label class="control-label"><?php echo $language['notify_user_on_message']; ?>:</label>
                        <input type="checkbox" name="message_email_notification" <?php if($new_user->get_user_meta($_SESSION['user_id'], 'message_email') == '1'){echo 'checked="checked"'; }?> value="1" />
                        </div>
                        
                       	<input type="hidden" name="update_user" value="1" /> 
						<input type="submit" value="<?php echo $language['update_user']; ?>" class="btn btn-primary" />

                    </form>
		<script type="text/javascript">
            $(document).ready(function() {
            // validate the register form
        $("#add_user").validate();
            });
        </script>
	</div><!--left-side-form ends here.-->                
<?php
	require_once('includes/sidebar.php');
?>                        
<?php
	require_once("includes/footer.php");
?>