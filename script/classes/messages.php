<?php
//Messages Class

class Messages {
	function list_thread($thread_id) {
		global $db;
		global $language;
		$query = "SELECT * from subjects WHERE subject_id='".$thread_id."'";
		$result = $db->query($query) or die($db->error);
		$row = $result->fetch_array();
		$subject_title = $row['subject_title'];
		
		$content = '<div class="pull-left">';
		$content .= '<h2>'.$subject_title.'</h2>';
		$content .= '</div>';
		//$content .= '<div class="pull-right" style="padding-top:25px;">';
		//$content .= '<form method="post" name="delete_message_thread" action="messages.php">
		//			<input type="hidden" name="thread_id" value="'.$thread_id.'" />
		//			<input type="hidden" name="delete_thread" value="Yes" />
		//			<input type="submit" class="btn btn-primary" value="Delete Thread">
		//			</form>
		//			';
		//$content .= '</div>';
		$content .= '<div class="clearfix"></div>';
		$message_query = "SELECT * from message_meta WHERE subject_id='".$thread_id."' ORDER by message_id ASC";
		$message_result = $db->query($message_query) or die($db->error);
		
		while($row_message = $message_result->fetch_array()) {
			if($row_message['to_id'] == $_SESSION['user_id']) {
				$read_msg = "UPDATE message_meta SET 
					status = 'read'
					WHERE message_id='".$row_message['message_id']."'
				"; 
				$read_result = $db->query($read_msg) or die($db->error);
			//read message ends here.
			}
			
			if($row_message['from_id'] == $_SESSION['user_id'] || $row_message['to_id'] == $_SESSION['user_id']) { 
				if($row_message['from_id'] == $_SESSION['user_id']) { 
					$sender = $language["sender_me"];
				} else { 
					$sender = $row_message['from_id'];
					$user_query = "SELECT * from users WHERE user_id='".$sender."'";
					$user_result = $db->query($user_query) or die($db->error);
					$user_row = $user_result->fetch_array();
					//printable.
					$sender = $user_row['first_name'].' '.$user_row['last_name'];
				}
				
				$receiver = $row_message['to_id'];
				$user_query = "SELECT * from users WHERE user_id='".$receiver."'";
				$user_result = $db->query($user_query) or die($db->error);
				$user_row = $user_result->fetch_array();
				//printable.
				$receiver = $user_row['first_name'].' '.$user_row['last_name'];
				
				$query = "SELECT * from messages WHERE message_id='".$row_message['message_id']."'";
				$result = $db->query($query) or die($db->error);
				$message = $result->fetch_array();
				
				$content .= '<hr />
 						       <div class="col-sm-3">';				
				$content .= '<h5>'.$language["sender_from"].': <strong>'.$sender.'</strong></h5>';
				$content .= '<h5>'.$language["sender_to"].': <strong>'.$receiver.'</strong></h5>';
				$content .= $message['message_datetime'];
				$content .= '</div>';
				$content .= '<div class="col-sm-9">'
    						.$message['message_detail'].'
	       					</div>
							<div class="clearfix"></div>
							';
				if($row_message['from_id'] == $_SESSION['user_id']) { 
					$reply_to = $row_message['to_id'];
				} else if($row_message['to_id'] == $_SESSION['user_id']) { 
					$reply_to = $row_message['from_id'];
				}
			}
		}
		if(!isset($reply_to)) { 
			$reply_to = $_SESSION['user_id'];
		}
		$content .= '<hr><form action="" id="reply" name="reply" method="post" enctype="multipart/form-data" role="form">';
		$content .= '<div class="form-group">
					<textarea name="reply_detail" class="tinyst form-control" placeholder="'.$language["send_a_reply"].'"></textarea>
					</div>';
		$content .= '<input type="hidden" name="reply_form" value="1">';
		$content .= '<input type="hidden" name="reply_to" value="'.$reply_to.'">';
		$content .= '<input type="hidden" name="subject_id" value="'.$thread_id.'">';
		$content .= '<div class="form-group">
                        	<input type="submit" value="Send" class="btn btn-primary" />
                        </div>';			
		$content .= '</form>';
		echo $content;
	}//list thread ends here.
	
	function list_sent() { 
		global $db;
		$query_messages = "SELECT * from message_meta WHERE from_id='".$_SESSION['user_id']."' ORDER by message_id DESC";
		$result_messages = $db->query($query_messages) or die($db->error);
		
		$subjects = array();
		while($message_row = $result_messages->fetch_array()) { 
			if(in_array($message_row['subject_id'], $subjects)) { 
			} else { 
			array_push($subjects, $message_row['subject_id']);
			}
		} //subject array ends here.
		$content = '';
		foreach($subjects as $subject) { 
			$subject_q = "SELECT * from subjects WHERE subject_id='".$subject."'";
			$subject_result = $db->query($subject_q) or die($db->error);
			$subject_row = $subject_result->fetch_array();
			//printable
			$subject_title = $subject_row['subject_title'];
		
			$messages_query = "SELECT * from message_meta WHERE subject_id='".$subject."' AND from_id='".$_SESSION['user_id']."' LIMIT 1";
			$message_result = $db->query($messages_query) or die($db->error);
			$message_row = $message_result->fetch_array();
			$to_id = $message_row['to_id'];
			//printable
			$message_query = "SELECT * from messages WHERE message_id='".$message_row['message_id']."'";
			$message_result = $db->query($message_query) or die($db->error);
			$message_row = $message_result->fetch_array();
			$message_detail = $message_row['message_detail'];
			$message_detail = (strlen($message_detail) > 103) ? substr($message_detail,0,100).'...' : $message_detail;
			
			$user_query = "SELECT * from users WHERE user_id='".$to_id."'";
			$user_result = $db->query($user_query) or die($db->error);
			$user_row = $user_result->fetch_array();
			//printable.
			$user_name = $user_row['first_name'].' '.$user_row['last_name'];
			$profile_img = $user_row['profile_image'];
			
			if($profile_img == '') { 
				$profile_img = 'images/thumb.png';
			} else { 
				//
			}
			
			$content .= '<tr>';
			$content .= '<td width="50">';
			$content .= '<img src="'.$profile_img.'" class="img-thumbnail" style="width:30px; height:30px;" />';
			$content .= '</td><td width="160">';
			$content .= '<strong>'.$user_name.'</strong>';
			$content .= '</td><td>';
			$content .= '<a href="messages.php?thread_id='.$subject.'">'.$subject_title.'</a><br />';
			$content .= $message_detail;
			$content .= '</td></tr>';
		}
		echo $content;		
	}//ibox ends here.
	
	
	function list_inbox() { 
		global $db;
		$query_messages = "SELECT * from message_meta WHERE to_id='".$_SESSION['user_id']."' ORDER by message_id DESC";
		$result_messages = $db->query($query_messages) or die($db->error);
		
		$subjects = array();
		while($message_row = $result_messages->fetch_array()) { 
			if(in_array($message_row['subject_id'], $subjects)) { 
			} else { 
			array_push($subjects, $message_row['subject_id']);
			}
		} //subject array ends here.
		$content = '';
		foreach($subjects as $subject) { 
			$subject_q = "SELECT * from subjects WHERE subject_id='".$subject."'";
			$subject_result = $db->query($subject_q) or die($db->error);
			$subject_row = $subject_result->fetch_array();
			//printable
			$subject_title = $subject_row['subject_title'];
		
			$messages_query = "SELECT * from message_meta WHERE subject_id='".$subject."' AND to_id='".$_SESSION['user_id']."' LIMIT 1";
			$message_result = $db->query($messages_query) or die($db->error);
			$message_row = $message_result->fetch_array();
			$from_id = $message_row['from_id'];
			//printable
			$message_query = "SELECT * from messages WHERE message_id='".$message_row['message_id']."'";
			$message_result = $db->query($message_query) or die($db->error);
			$message_row = $message_result->fetch_array();
			
			$message_detail = $message_row['message_detail'];
			$message_detail = (strlen($message_detail) > 103) ? substr($message_detail,0,100).'...' : $message_detail;
			
			$user_query = "SELECT * from users WHERE user_id='".$from_id."'";
			$user_result = $db->query($user_query) or die($db->error);
			$user_row = $user_result->fetch_array();
			//printable.
			$user_name = $user_row['first_name'].' '.$user_row['last_name'];
			$profile_img = $user_row['profile_image'];
			
			if($profile_img == '') { 
				$profile_img = 'images/thumb.png';
			} else { 
				//
			}
			
			$unread_count = "SELECT * from message_meta WHERE to_id='".$_SESSION['user_id']."' AND subject_id='".$subject."' AND status='unread'";
			$unread_result = $db->query($unread_count) or die($db->error);
			$num_rows = $unread_result->num_rows;
			if($num_rows > 0) { 
			$content .= '<tr class="well">';
			} else { 
			$content .= '<tr>';
			}
			$content .= '<td width="50">';
			$content .= '<img src="'.$profile_img.'" class="img-thumbnail" style="width:30px; height:30px;" />';
			$content .= '</td><td width="160">';
			$content .= '<strong>'.$user_name.'</strong> ('.$num_rows.')';
			$content .= '</td><td>';
			$content .= '<a href="messages.php?thread_id='.$subject.'">'.$subject_title.'</a><br />';
			$content .= $message_detail;
			$content .= '</td></tr>';
		}
		echo $content;		
	}//ibox ends here.
	function get_subject_by_id($subject_id) {
		global $db;
		$query = "SELECT * from subjects WHERE subject_id='".$subject_id."'";
		$result = $db->query($query) or die($db->error); 
		$row = $result->fetch_array();
		$subject_title = $row['subject_title'];
		return $subject_title;
	}
	function send_reply($reply_to, $subject_id, $reply_detail) { 
			global $db;
			global $language;
			//insert Message.
			$query = "INSERT into messages VALUES(NULL, '".date("Y-m-d H:i:s")."', '".$reply_detail."')";
			$result = $db->query($query) or die($db->error);
			$message_id = $db->insert_id;
			
			$query = "INSERT into message_meta VALUES(NULL, '".$message_id."', 'unread', '".$_SESSION['user_id']."', '".$reply_to."', '".$subject_id."')";
			$result = $db->query($query) or die($db->error);
			
			$new_user = new Users;
			if($new_user->get_user_meta($reply_to, 'message_email') == '1') { 
				$mailto = $new_user->get_user_info($reply_to, 'email');
				$subject = 'Re:'.$this->get_subject_by_id($subject_id);
				$message = $reply_detail;
				send_email($mailto, $subject, $message);
			}
			
			return $language["message_sent_success"];
	}//new_message ends here.
	
	function new_message($username_email, $subject, $message) { 
		global $db;
		global $language;
		$message =  $db->real_escape_string($message);
		$subject =  $db->real_escape_string($subject);
		
		$query = "SELECT * from users WHERE email='".$username_email."' OR username='".$username_email."'";
		$result = $db->query($query) or die($db->error);
		$num_rows = $result->num_rows;
		
		if($num_rows >0) {
			$row = $result->fetch_array();
			$to_id = $row['user_id'];
			 
			//insert Subject.
			$query = "INSERT into subjects VALUES(NULL, '".$subject."')";
			$result = $db->query($query) or die($db->error);
			$subject_id = $db->insert_id;
			
			//insert Message.
			$query = "INSERT into messages VALUES(NULL, '".date("Y-m-d H:i:s")."', '".$message."')";
			$result = $db->query($query) or die($db->error);
			$message_id = $db->insert_id;
			//Insert MESSAGE META.
			$query = "INSERT into message_meta VALUES(NULL, '".$message_id."', 'unread', '".$_SESSION['user_id']."', '".$to_id."', '".$subject_id."')";
			$result = $db->query($query) or die($db->error);
			
			$new_user = new Users;
			if($new_user->get_user_meta($to_id, 'message_email') == '1') { 
				$mailto = $new_user->get_user_info($to_id, 'email');
				send_email($mailto, $subject, $message);
			}
			
			return $language["message_sent_success"];
		} else { 
			return $language["no_user_to_message"].' '.$username_email;
		}
	}//new_message ends here.
	
	function single_user_msg($user_id, $subject, $message) { 
			global $db;
			global $language;
			$message =  $db->real_escape_string($message);
			$subject =  $db->real_escape_string($subject);
			//insert Subject.
			$query = "INSERT into subjects VALUES(NULL, '".$subject."')";
			$result = $db->query($query) or die($db->error);
			$subject_id = $db->insert_id;
			//insert Message.
			$query = "INSERT into messages VALUES(NULL, '".date("Y-m-d H:i:s")."', '".$message."')";
			$result = $db->query($query) or die($db->error);
			$message_id = $db->insert_id;
			//Insert message meta.
			$query = "INSERT into message_meta VALUES(NULL, '".$message_id."', 'unread', '".$_SESSION['user_id']."', '".$user_id."', '".$subject_id."')";
			$result = $db->query($query) or die($db->error);
			
			$new_user = new Users;
			if($new_user->get_user_meta($user_id, 'message_email') == '1') { 
				$mailto = $new_user->get_user_info($user_id, 'email');
				send_email($mailto, $subject, $message);
			}
			
			return $language["message_sent_success"];
	}//new_message ends here.
	
	function level_message($level_name, $subject, $message) { 
		global $db;
		global $language;
		$message =  $db->real_escape_string($message);
		$subject =  $db->real_escape_string($subject);
		//insert Subject.
		$query = "INSERT into subjects VALUES(NULL, '".$subject."')";
		$result = $db->query($query) or die($db->error);
		$subject_id = $db->insert_id;
		//insert Message.
		$query = "INSERT into messages VALUES(NULL, '".date("Y-m-d H:i:s")."', '".$message."')";
		$result = $db->query($query) or die($db->error);
		$message_id = $db->insert_id;
		//getting users.
		$query_user = "SELECT * from users WHERE user_type='".$level_name."'";
		$result_user = $db->query($query_user) or die($db->error);
		
		while($row = $result_user->fetch_array()) { 
			$user_id = $row['user_id'];
			
			$query = "INSERT into message_meta VALUES(NULL, '".$message_id."', 'unread', '".$_SESSION['user_id']."', '".$user_id."', '".$subject_id."')";
			$result = $db->query($query) or die($db->error);
			
			$new_user = new Users;
			if($new_user->get_user_meta($user_id, 'message_email') == '1') { 
				$mailto = $new_user->get_user_info($user_id, 'email');
				send_email($mailto, $subject, $message);
			}
		}
		return $language["message_to_all"].' '.$level_name.'.';
	}//message to all admins ends here.
	
	function message_all($subject, $message) { 
		global $db;
		global $language;
		
		$message =  $db->real_escape_string($message);
		$subject =  $db->real_escape_string($subject);
		
		//insert Subject.
		$query = "INSERT into subjects VALUES(NULL, '".$subject."')";
		$result = $db->query($query) or die($db->error);
		$subject_id = $db->insert_id;
		
		//insert Message.
		$query = "INSERT into messages VALUES(NULL, '".date("Y-m-d H:i:s")."', '".$message."')";
		$result = $db->query($query) or die($db->error);
		$message_id = $db->insert_id;
		
		//getting users.
		$query_user = "SELECT * from users";
		$result_user = $db->query($query_user) or die($db->error);
		
		while($row = $result_user->fetch_array()) { 
			$user_id = $row['user_id'];
			
			$query = "INSERT into message_meta VALUES(NULL, '".$message_id."', 'unread', '".$_SESSION['user_id']."', '".$user_id."', '".$subject_id."')";
			$result = $db->query($query) or die($db->error);
			
			$new_user = new Users;
			if($new_user->get_user_meta($user_id, 'message_email') == '1') { 
				$mailto = $new_user->get_user_info($user_id, 'email');
				send_email($mailto, $subject, $message);
			}
		}
		return $language["message_sent_to_all"];
	}//message to all admins ends here.
	
	function unread_count() { 
		global $db;
		$query = "SELECT * from message_meta WHERE status='unread' AND to_id='".$_SESSION['user_id']."'";
		$result = $db->query($query) or die($db->error);
		echo $result->num_rows;
	}//unread count ends here.
	
	function message_widget() { 
		global $db;
		$query = "SELECT * from message_meta WHERE to_id='".$_SESSION['user_id']."' ORDER by message_id DESC LIMIT 3";
		$result = $db->query($query) or die($db->error);
		
		$content = '';
		while($row = $result->fetch_array()) { 
			$query_sub = "SELECT * from subjects WHERE subject_id='".$row['subject_id']."'";
			$result_sub = $db->query($query_sub) or die($db->error);
			$sub_row = $result_sub->fetch_array();
			
			$subject = $sub_row['subject_title'];
			
			$message = "SELECT * from messages WHERE message_id='".$row['message_id']."'";
			$message_result = $db->query($message) or die($db->error);
			$message_row = $message_result->fetch_array();
			
			$message_detail = $message_row['message_detail'];
			$message_detail = (strlen($message_detail) > 103) ? substr($message_detail,0,100).'...' : $message_detail;
			
			$content .= '<div class="list-group-item">';
			$content .= '<h4 class="list-group-item-heading">'.$subject.'</h4>';
			$content .= '<p class="list-group-item-text">'.$message_detail.'</p>';
			$content .= '</div>';
		}
		echo $content;
		
	}//message widget ends here.
}//class ends here.