<?php
//users Class
class Users {
	public $user_id;
	public $first_name;
	public $last_name;
	public $gender;
	public $date_of_birth;
	public $address1;
	public $address2;
	public $city;
	public $state;
	public $country;
	public $zip_code;
	public $mobile;
	public $phone;
	public $username;
	public $email;
	public $profile_image;
	public $description;
	public $status;
	public $user_type;
	
	function set_user_meta($user_id, $term, $value) { 
		global $db;
		$query = "SELECT * from user_meta WHERE user_id='".$user_id."'";
		$result = $db->query($query) or die($db->error);
		$rows = $result->num_rows;
		
		if($rows > 0) {
			//We have to update existing record. 
			$query = 'UPDATE user_meta SET
   	    			'.$term.' = "'.$value.'"
			WHERE user_id="'.$user_id.'"';
		} else { 
			//we have to add new record.
			$query = 'INSERT into user_meta(user_meta_id, user_id, '.$term.') VALUES(NULL, "'.$user_id.'", "'.$value.'")';
		}
		$result = $db->query($query) or die($db->error);
	}//set user meta information.
	
	function get_user_meta($user_id, $term) { 
		global $db;
		$query = "SELECT * from user_meta WHERE user_id='".$user_id."'";
		$result = $db->query($query) or die($db->error);
		$row = $result->fetch_array();
		return $row[$term];
	}//get user email ends here.
	
	function get_user_info($user_id, $term) { 
		global $db;
		$query = "SELECT * from users WHERE user_id='".$user_id."'";
		$result = $db->query($query) or die($db->error);
		$row = $result->fetch_array();
		return $row[$term];
	}//get user email ends here.
	
	function subscriber_options() { 
		global $db;
		$query = "SELECT * from users WHERE user_type='subscriber' ORDER by first_name ASC";
		$result = $db->query($query) or die($db->error);

		$options = '';
		while($row = $result->fetch_array()) {
			extract($row);
			$options .= '<option value="'.$user_id.'">'.$user_id.' | '.$first_name.'</option>';	
		}//while loop ends here.
		echo $options;
	}
	
	function register_user($first_name, $last_name, $user_type, $username, $email, $password){
			global $db;
			global $language;
			//Check if user already exist
			$query = "SELECT * from users WHERE email='".$email."'";
			$result = $db->query($query);
			
			$num_user = $result->num_rows;
			
			if($num_user > 0) { 
				return $language["email_exit_user_err"].' <strong>'.$email.'</strong> '.$language["already_REgistered"];
				exit();
			}
			//username validation
			$query = "SELECT * from users WHERE username='".$username."'";
			$result = $db->query($query);
			
			$num_user = $result->num_rows;
			
			if($num_user > 0) { 
				return $language["username_couldniot_add"].' <strong>'.$username.'</strong> '.$language["already_REgistered"];
				exit();
			}
			$registration_date = date('Y-m-d');
			$password = md5($password);
			$activation_key = substr(md5(uniqid(rand(), true)), 16, 16);
			
			if(get_option('register_verification') != '1') { 
				$status = "deactivate";
			} else { 
				$status = "activate";
			}
			if($user_type == 'admin') { 
				$user_type = get_option('notify_user_group');
			}
			//adding user into database
			$query = "INSERT INTO users(user_id, first_name,last_name,username,email,password,activation_key,date_register,user_type,status) VALUES (NULL, '$first_name', '$last_name', '$username', '$email', '$password', '$activation_key', '$registration_date', '$user_type', '$status')";
			$result = $db->query($query) or die($mysqli->error);
			$user_id = $db->insert_id;
			//Email to user
			$site_url = get_option('site_url');
					
			$email_message = $language["email_register_1"]."<br />";
			$email_message .= $language["email_register_2"].": <strong> ".$username.'</strong><br>';
			$email_message .= $language["email_register_3"]."<br />";
			$email_message .= "<a href='".$site_url."login.php?confirmation_code=".$activation_key."&user_id=".$user_id."'>".$language["email_register_4"]."</a>";
			$email_message .= "<br><br>".$language["email_register_5"];			
			
			$message = $email_message;
			$mailto = $email;
			$subject = $language["email_register_6"];
			
			send_email($mailto, $subject, $message);
			//Notify other users of same level on new registration.
			if(get_option('notify_user_group') == '1'):
			//message object.
			$subject = "New user registration.";
			$message = "<h2>New user on your user group.</h2>";
			$message .= "<p><strong>Name: </strong>".$first_name." ".$last_name."</p>";
			$message .= "<p><strong>Email: </strong>".$email."</p>";
			$message .= "<p><strong>Username: </strong>".$username."</p>";
			
			$message_obj = new Messages;
			$message_obj->level_message($user_type, $subject, $message);
			endif;
			return $language['registrat_success'];
	}//register_user ends here.
	
		function facebook_login_register($first_name, $last_name, $gender, $email, $user_type) {
		 global $db; //starting database object.
		 global $language;
		 
		 $query = "SELECT * from users WHERE email='".$email."' OR username='".$email."'";
		 $result = $db->query($query) or die($db->error);
		 $num_rows = $result->num_rows;
		 
		 $registration_date = date('Y-m-d');
		 $pass = randomPassword();
		 $password = md5($pass);
		 $status = 'activate';
  		 
		 if($user_type == 'admin') { 
			$user_type = get_option('register_user_level');
		 }
			
		 if($num_rows == 0) {
		 	$query = "INSERT INTO users(user_id, first_name,last_name,gender,email,password,date_register,user_type,status) VALUES (NULL, '$first_name', '$last_name', '$gender', '$email', '$password', '$registration_date', '$user_type', '$status')";
			$result = $db->query($query) or die($mysqli->error);
			$user_id = $db->insert_id;
			
			$email_msg = '<h1>'.$language['fb_reg_thanks'].'.</h1>';
			$email_msg .= '<p>'.$language['fb_reg_des'].'</p>';			
			$email_msg .= '<p>Email:'.$email.'<br>';
			$email_msg .= 'Password:'.$pass.'</p>';
			$email_msg .= get_option('site_url');
			
			$subject = $language['fb_reg_thanks'].' | '.get_option('site_name');
			
			send_email($email, $subject, $email_msg);
			//Notification to user group on new registration.
			if(get_option('notify_user_group') == '1'):
			//message object.
			$subject = "New user registration.";
			$message = "<h2>New user on your user group.</h2>";
			$message .= "<p><strong>Name: </strong>".$first_name." ".$last_name."</p>";
			$message .= "<p><strong>Email: </strong>".$email."</p>";
			$message .= "<p><strong>Username: </strong>".$username."</p>";
			
			$message_obj = new Messages;
			$message_obj->level_message($user_type, $subject, $message);
			endif;
			$num_rows = 1;
		 }//if user do not exist register user.
		 
		 if($num_rows > 0) { 
		 	$row = $result->fetch_array();
			
				if($row['status'] == 'deactivate') { 
					$message = $language["not_active_yet_em"];
				} else if($row['status'] == 'activate'){
					extract($row);
					$this->user_id = $user_id; 
					$this->first_name = $first_name;
					$this->last_name = $last_name;
					$this->username = $username;
					$this->email = $email;
					$this->status = $status;
					$this->user_type = $user_type;
					if($profile_image != '') { 
					$this->profile_image = $profile_image;
					} else { 
					$this->profile_image = 'images/thumb.png';
					}
					
					$message = 1;
				} else { 
					$message = $language["ban_suspend_login_con"];
				}
			
		 } else { 
		 	$message = $language["could_not_find_email"];
		 }
		 return $message;
	}//login func ends here.
		
		function login_user($email, $password) {
		 global $db; //starting database object.
		 global $language;
		 $password = md5($password); //Converting input password to md5 to match in database.
		 
		 $query = "SELECT * from users WHERE email='".$email."' OR username='".$email."'";
		 $result = $db->query($query) or die($db->error);
		 $num_rows = $result->num_rows;
		 
		 if($num_rows > 0) { 
		 	$row = $result->fetch_array();
			
			$lock_account = $this->get_user_meta($row['user_id'], 'login_lock');
			if($lock_account != 'No') { 
				if($lock_account + get_option('wrong_attempts_time') * 60 > time()) { 
					return $language['wrong_attempt_lock'];
					exit();
				} else { 
					$this->set_user_meta($row['user_id'], 'login_attempt', '0');
					$this->set_user_meta($row['user_id'], 'login_lock', 'No'); //setting last login time.
				}
			}
			
			if($row['password'] == $password) {
				if($row['status'] == 'deactivate') { 
					$message = $language["not_active_yet_em"];
				} else if($row['status'] == 'activate'){
					extract($row);
					$this->user_id = $user_id; 
					$this->first_name = $first_name;
					$this->last_name = $last_name;
					$this->username = $username;
					$this->email = $email;
					$this->status = $status;
					$this->user_type = $user_type;
					if($profile_image != '') { 
					$this->profile_image = $profile_image;
					} else { 
					$this->profile_image = 'images/thumb.png';
					}
					$message = 1;
				} else { 
					$message = $language["ban_suspend_login_con"];
				}
			} else { 
				$message = $language["password_do_not_match_err"];
				$login_attempt = $this->get_user_meta($row['user_id'], 'login_attempt')+1;
				$this->set_user_meta($row['user_id'], 'login_attempt', $login_attempt);
				if($login_attempt >= get_option('maximum_login_attempts')) { 
					$this->set_user_meta($row['user_id'], 'login_lock', time());	
				}
			}
			
		 } else { 
		 	$message = $language["could_not_find_email"];
		 }
		 return $message;
	}//login func ends here.
	
	function delete_user($user_type, $user_id) {
		global $db;
		global $language;
		if($user_type == 'admin') {
			$query = 'DELETE from users WHERE user_id="'.$user_id.'"';
			$result = $db->query($query) or die($db->error);
			$message = $language["user_delete_succ"];	
		} else { 
			$message = $language["cannot_delete_user_err"];
		}	
		return $message;
	}//delete level ends here.

function list_users($user_type) {
		global $db;
		global $language;
		if($user_type == 'admin') { 
			$query = 'SELECT * from users ORDER by first_name ASC';
			$result = $db->query($query) or die($db->error);
			$content = '';
			$count = 0;
			while($row = $result->fetch_array()) { 
				extract($row);
				$count++;
				if($count%2 == 0) { 
					$class = 'even';
				} else { 
					$class = 'odd';
				}
				$content .= '<tr class="'.$class.'">';
				$content .= '<td>';
				$content .= $first_name.' '.$last_name;
				$content .= '</td><td>';
				if($city != '') { 
				$content .= $city.', ';
				}
				if($state != '') { 
				$content .= $state.', ';
				}
				$content .= $country;
				$content .= '</td><td>';
				$content .= $username;
				$content .= '</td><td>';
				$content .= $email;
				$content .= '</td><td>';
				$content .= ucfirst($status);
				$content .= '</td><td>';
				$content .= ucfirst($user_type);
				$content .= '</td><td>';
				$content .= $this->get_user_meta($user_id, 'last_login_time');
				$content .= '</td><td>';
				$content .= $this->get_user_meta($user_id, 'last_login_ip');
				$content .= '</td><td>';
				$content .= '<button class="btn btn-default btn-sm pull-left" style="margin-right:5px;" data-toggle="modal" data-target="#modal_'.$user_id.'">'.$language["message"].'</button>';
				$content .= '<!-- Modal -->
<script type="text/javascript">
$(function(){
$("#message_form_'.$user_id.'").on("submit", function(e){
  e.preventDefault();
  tinyMCE.triggerSave();
  $.post("includes/messageprocess.php", 
	 $("#message_form_'.$user_id.'").serialize(), 
	 function(data, status, xhr){
	   $("#success_message_'.$user_id.'").html("<div class=\'alert alert-success\'>"+data+"</div>");
	 });
});
});
</script>				
<div class="modal fade" id="modal_'.$user_id.'" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form id="message_form_'.$user_id.'" method="post" name="send_message">
	<div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title" id="myModalLabel">'.$language["send_message"].'</h4>
      </div>
	  
      <div class="modal-body">
      		<div id="success_message_'.$user_id.'"></div>
	   		<div class="form-group">
				<label class="control-label">'.$language["message_to"].'</label>
				<input type="text" class="form-control" name="message_to" value="Email:('.$email.') Username: ('.$username.')" readonly="readonly" />
			</div>
			
			<div class="form-group">
				<label class="control-label">'.$language["subject"].'</label>
				<input type="text" class="form-control" name="subject" value="" />
			</div>
			
			<div class="form-group">
				<label class="control-label">'.$language["message"].'</label>
				<textarea class="tinyst form-control" name="message"></textarea>
			</div>
      </div>
	  <input type="hidden" name="from" value="'.$_SESSION['user_id'].'" />
	  <input type="hidden" name="user_id" value="'.$user_id.'" />
	  <input type="hidden" name="single_form" value="1" />
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">'.$language["close"].'</button>
		<input type="submit" value="Send Message" class="btn btn-primary" />
      </div>
    </div><!-- /.modal-content -->
   </form>
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->';			
				$content .= '<form method="post" name="edit" action="manage_users.php">';
				$content .= '<input type="hidden" name="edit_user" value="'.$user_id.'">';
				$content .= '<input type="submit" style="margin-right:5px;" class="btn btn-default btn-sm pull-left" value="'.$language["edit"].'">';
				$content .= '</form>';
				$content .= '<form method="post" name="delete" onsubmit="return confirm_delete();" action="">';
				$content .= '<input type="hidden" name="delete_user" value="'.$user_id.'">';
				$content .= '<input type="submit" class="btn btn-default btn-sm pull-left" value="'.$language["delete"].'">';
				$content .= '</form>';
				$content .= '</td>';
				$content .= '</tr>';
				unset($class);
			}//loop ends here.
		} else { 
			$content = $language["cannot_i_user"];
		}	
		echo $content;
	}//list_levels ends here.

	function get_total_users($condition) { 
		global $db;
		global $language;
		if($_SESSION['user_type'] == 'admin') {
			if($condition == 'all') { 
				$query = "SELECT * from users";
			} else { 
				$query = "SELECT * from users WHERE status='".$condition."'";
			}
			$result = $db->query($query) or die($db->error);
			$num_rows = $result->num_rows;
			echo $num_rows;
		} else { 
			echo $language["cannot_view_this_list"];
		}
	}//prints total registered users.

function edit_profile($user_id, $first_name, $last_name, $gender, $date_of_birth, $address1, $address2, $city, $state, $country, $zip_code, $mobile, $phone, $username, $email, $password, $profile_image, $description) {
		global $db;
		global $language;
		
		$current_email = $this->get_user_info($_SESSION['user_id'], 'email');
		$current_username = $this->get_user_info($_SESSION['user_id'], 'username');
		
		if($email != $current_email) {
		$query = "SELECT * from users WHERE email='".$email."'";
			$result = $db->query($query);
			
			$num_user = $result->num_rows;
			
			if($num_user > 0) { 
				return $language["email_exit_user_err"].' <strong>'.$email.'</strong> '.$language["already_REgistered"];
				exit();
			}
		}
		
		if($current_username != $username) {
			//username validation
			$query = "SELECT * from users WHERE username='".$username."'";
			$result = $db->query($query);
			
			$num_user = $result->num_rows;
			
			if($num_user > 0) { 
				return $language["username_couldniot_add"].' <strong>'.$username.'</strong> '.$language["already_REgistered"];
				exit();
			}
		}
		if($password == '') {
			$query = 'UPDATE users SET
   	    			first_name = "'.$first_name.'",
					last_name = "'.$last_name.'",
					gender = "'.$gender.'",
					date_of_birth = "'.$date_of_birth.'",
					address1 = "'.$address1.'",
					address2 = "'.$address2.'",
					city = "'.$city.'",
					state = "'.$state.'",
					country = "'.$country.'",
					zip_code = "'.$zip_code.'",
					mobile = "'.$mobile.'",
					phone = "'.$phone.'",
					username = "'.$username.'",
					email = "'.$email.'",
					profile_image = "'.$profile_image.'",
					description = "'.$description.'"
			WHERE user_id="'.$user_id.'"';
			} else { 
			$query = 'UPDATE users SET
   	    			first_name = "'.$first_name.'",
					last_name = "'.$last_name.'",
					gender = "'.$gender.'",
					date_of_birth = "'.$date_of_birth.'",
					address1 = "'.$address1.'",
					address2 = "'.$address2.'",
					city = "'.$city.'",
					state = "'.$state.'",
					country = "'.$country.'",
					zip_code = "'.$zip_code.'",
					mobile = "'.$mobile.'",
					phone = "'.$phone.'",
					username = "'.$username.'",
					email = "'.$email.'",
					password = "'.md5($password).'",
					profile_image = "'.$profile_image.'",
					description = "'.$description.'"
			WHERE user_id="'.$user_id.'"';
			}
			$result = $db->query($query) or die($db->error);
			return $language["user_update_success"];
	}//update user ends here.

	function set_user($user_id, $user_type, $login_user) {
		 global $db;
		 global $language;
		 if($user_type == 'admin') { 
			$query = 'SELECT * from users WHERE user_id="'.$user_id.'"'; 
		 } else if($user_id == $login_user) { 
		 	$query = 'SELECT * from users WHERE user_id="'.$user_id.'"';
		 } else { 
		 	echo $language["trying_do_to_illegal"];
		 }
		$result = $db->query($query) or die($db->error);
		$row = $result->fetch_array();
		//results ends here.
		$this->user_id = $row['user_id'];
		$this->first_name = $row['first_name'];
		$this->last_name = $row['last_name'];
		$this->gender = $row['gender'];
		$this->date_of_birth = $row['date_of_birth'];
		$this->address1 = $row['address1'];
		$this->address2 = $row['address2'];
		$this->city = $row['city'];
		$this->state = $row['state'];
		$this->country = $row['country'];
		$this->zip_code = $row['zip_code'];
		$this->mobile = $row['mobile'];
		$this->phone = $row['phone'];
		$this->username = $row['username'];
		$this->email = $row['email'];
		$this->profile_image = $row['profile_image'];
		$this->description = $row['description'];
		$this->status = $row['status'];
		$this->user_type = $row['user_type'];
	}//level set ends here.

	function update_user($user_id, $user_type_ses, $first_name, $last_name, $gender, $date_of_birth, $address1, $address2, $city, $state, $country, $zip_code, $mobile, $phone, $username, $email, $password, $profile_image, $description, $status, $user_type) {
		global $db;
		global $language;
		
		$current_email = $this->get_user_info($user_id, 'email');
		$current_username = $this->get_user_info($user_id, 'username');
		
		if($email != $current_email) {
		$query = "SELECT * from users WHERE email='".$email."'";
			$result = $db->query($query);
			
			$num_user = $result->num_rows;
			
			if($num_user > 0) { 
				return $language["email_exit_user_err"].' <strong>'.$email.'</strong> '.$language["already_REgistered"];
				exit();
			}
		}
			
			if($current_username != $username) {
			//username validation
			$query = "SELECT * from users WHERE username='".$username."'";
			$result = $db->query($query);
			
			$num_user = $result->num_rows;
			
			if($num_user > 0) { 
				return $language["username_couldniot_add"].' <strong>'.$username.'</strong> '.$language["already_REgistered"];
				exit();
			}
			}
		//updating user info.
		if($user_type_ses == 'admin') { 
			if($password == '') {
			$query = 'UPDATE users SET
   	    			first_name = "'.$first_name.'",
					last_name = "'.$last_name.'",
					gender = "'.$gender.'",
					date_of_birth = "'.$date_of_birth.'",
					address1 = "'.$address1.'",
					address2 = "'.$address2.'",
					city = "'.$city.'",
					state = "'.$state.'",
					country = "'.$country.'",
					zip_code = "'.$zip_code.'",
					mobile = "'.$mobile.'",
					phone = "'.$phone.'",
					username = "'.$username.'",
					email = "'.$email.'",
					profile_image = "'.$profile_image.'",
					description = "'.$description.'",
					status = "'.$status.'",
					user_type = "'.$user_type.'"
			WHERE user_id="'.$user_id.'"';
			} else { 
			$query = 'UPDATE users SET
   	    			first_name = "'.$first_name.'",
					last_name = "'.$last_name.'",
					gender = "'.$gender.'",
					date_of_birth = "'.$date_of_birth.'",
					address1 = "'.$address1.'",
					address2 = "'.$address2.'",
					city = "'.$city.'",
					state = "'.$state.'",
					country = "'.$country.'",
					zip_code = "'.$zip_code.'",
					mobile = "'.$mobile.'",
					phone = "'.$phone.'",
					username = "'.$username.'",
					email = "'.$email.'",
					password = "'.md5($password).'",
					profile_image = "'.$profile_image.'",
					description = "'.$description.'",
					status = "'.$status.'",
					user_type = "'.$user_type.'"
			WHERE user_id="'.$user_id.'"';
			}
			$result = $db->query($query) or die($db->error);
			return $language["user_update_success"];
		} else { 
			return $language["you_have_no_rights"];
		}
	}//update user ends here.
	
	function reset_pass_user($user_id,$confirmation_code,$new_pass){
		global $db;
		global $language;
		$query = "SELECT * from users WHERE user_id='".$user_id."'";
		$result = $db->query($query) or die($db->error);
		$row = $result->fetch_array();
		
		$new_pass = md5($new_pass);
		if($confirmation_code==$row['activation_key']){
				$query = 'UPDATE users SET password="'.$new_pass.'",activation_key="" WHERE user_id="'.$user_id.'"';
				$row = $db->query($query) or die($db->error);
				$message = $language["password_reset_msg"];
			} else { 
				$message = $language["activation_key_expire"];
			}
			return $message;
		}//reset password function ends here.	

function match_confirm_code($confirmation_code,$user_id){
		global $db;
		global $language;
		//Getting Confirmation Code from database.
		$query = "SELECT * from users WHERE user_id='".$user_id."'";
		$result = $db->query($query) or die($db->error);
		$row = $result->fetch_array();
		
		if($row['activation_key'] == $confirmation_code){
			if($row['status'] == 'suspend'||$row['status'] == 'ban'){
				$message= $language["suspend_help"];
			} else {
				$status = 'activate';
				$query = 'UPDATE users SET status="'.$status.'",activation_key="" WHERE user_id="'.$user_id.'"';
				$row = $db->query($query) or die($db->error);
				$message = $language["activation_succ_ms"];
			} 
		} else {
			  $message = $language["cannot_activate_acc_1"];
		}
		return $message;
}//function  close

function forgot_user($email){
	global $db;
	global $language;
	 $query = "SELECT * from users WHERE email='".$email."' OR username='".$email."'";
	 $result = $db->query($query) or die($db->error);
	 $num_rows = $result->num_rows;
	 
		 if($num_rows > 0) { 
		 	$row = $result->fetch_array();
			$user_id =$row['user_id'];
			$email = $row['email'];
		 } else {
			return $language["email_not_in_system"];
			exit();
		}
	$activation_key = substr(md5(uniqid(rand(), true)), 16, 16);
	$query = 'UPDATE users SET activation_key="'.$activation_key.'" WHERE user_id="'.$user_id.'"';
	$result = $db->query($query) or die($db->error);

	$site_url = get_option('site_url');
	$email_message = $language["reset_your_pass_1"]."<br />";
	$email_message .= $language["click_link_reset_pass"]."<br />";
	$email_message .= "<a href='".$site_url."forgot.php?confirmation_code=".$activation_key."&user_id=".$user_id."'>".$language["email_register_4"]."</a>";
	$email_message .= "<br><br>".$language["email_register_5"];			
	$message = $email_message;
	$mailto = $email;
	$subject = $language["reset_your_pass1"];
	
	send_email($mailto, $subject, $message);

	return $language["check_email_rest_pass"];
	}//forgot password function endsh ere.

	function add_user($first_name, $last_name, $gender, $date_of_birth, $address1, $address2, $city, $state, $country, $zip_code, $mobile, $phone, $username, $email, $password, $profile_image, $description, $status, $user_type) { 
			global $db;
			global $language;
			//Check if user already exist
			$query = "SELECT * from users WHERE email='".$email."'";
			$result = $db->query($query) or die($db->error);
			
			$num_user = $result->num_rows;
			if($num_user > 0) { 
				return $language["email_exit_user_err"].' <strong>'.$email.'</strong> '.$language["already_REgistered"];
				exit();
			}
			//username validation
			$query = "SELECT * from users WHERE username='".$username."'";
			$result = $db->query($query);
			
			$num_user = $result->num_rows;
			
			if($num_user > 0) { 
				return $language["username_couldniot_add"].' <strong>'.$username.'</strong> '.$language["already_REgistered"];
				exit();
			}
			$registration_date = date('Y-m-d');
			$password_con = md5($password);
			
			//Running Query to add user.
			$query = "INSERT into users VALUES(NULL, '".$first_name."', '".$last_name."', '".$gender."', '".$date_of_birth."', '".$address1."', '".$address2."', '".$city."', '".$state."', '".$country."', '".$zip_code."', '".$mobile."', '".$phone."', '".$username."', '".$email."', '".$password_con."', '".$profile_image."', '".$description."', '".$status."', '', '".date('Y-m-d')."', '".$user_type."')";
			$result = $db->query($query) or die($db->error);
			//Email to user
			$site_url = get_option('site_url');
					
			$email_message = $language["your_account_registered"]."<br />";
			$email_message .= $language["use_following_details"];
			$email_message .= "<br><a href='".$site_url."'>".$language["email_register_4"]."</a><br>";
			$email_message .= $language["email_or_username"]." <strong>".$email."</strong><br>";
			$email_message .= $language["password"].": <strong>".$password."</strong>";			
			
			$message = $email_message;
			$mailto = $email;
			$subject = $language["registration_details"];
			
			send_email($mailto, $subject, $message);

			//Notify other users of same level on new registration.
			if(get_option('notify_user_group') == '1'):
			//message object.
			$subject = "New user registration.";
			$message = "<h2>New user on your user group.</h2>";
			$message .= "<p><strong>Name: </strong>".$first_name." ".$last_name."</p>";
			$message .= "<p><strong>Email: </strong>".$email."</p>";
			$message .= "<p><strong>Username: </strong>".$username."</p>";
			
			$message_obj = new Messages;
			$message_obj->level_message($user_type, $subject, $message);
			endif;
		return $language["user_add_details_sent"].' '.$email;
	}//add user function ends here.
}//class ends here.