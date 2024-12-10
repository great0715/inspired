<?php
//Company Class

class Project {
	public $project_name;
	public $project_manual_id;
	public $project_type;
	public $project_logo;
	public $description;
	public $project_status;
	public $company_id;
	
	function get_project_plan($project_id) { 
		global $db;
		
		$query = "SELECT * from project_plan WHERE project_id='".$project_id."'";
		$result = $db->query($query) or die($db->error);
		$row = $result->fetch_array();
		
		if($row['plan'] == '') { 
			echo 'Your project have no plan yet.';
		} else { 
			echo $row['plan'];
		}
	}//Echo project plan.
	
	function manage_plan($project_id, $plan_detail) { 
		global $db;
		$plan_detail = $db->real_escape_string($plan_detail);
		//removing slashes from variables.
		$query = "SELECT * from project_plan WHERE project_id='".$project_id."'";
		$result = $db->query($query) or die($db->error);
		$num_rows = $result->num_rows;
		if($num_rows > 0) { 
			//project plan already exist.
			$query = "UPDATE project_plan SET 
					plan='".$plan_detail."'
					WHERE project_id='".$project_id."'
					";
			$result = $db->query($query) or die($db->error);
			$project_log = 'Project plan was updated';
			project_log($project_id, $project_log);
			return 'Project plan updated successfuly!';		
		} else { 
			//no project plan exist add new.
			$query = "INSERT into project_plan (plan_id, plan, project_id) VALUES(NULL, '".$plan_detail."', '".$project_id."')";
			$result = $db->query($query) or die($db->error);
			$project_log = 'Project plan was added';
			project_log($project_id, $project_log);
			
			return 'Your project plan was added successfuly!';
		}
		//this function add new plan if plan already exist this function will update the plan.
	}//manage plan ends here.
	
	function set_project($project_id) { 
		global $db;
		$query = "SELECT * from projects WHERE project_id='".$project_id."'"; 
		$result = $db->query($query) or die($db->error);
		if($result->num_rows > 0) {
			$row = $result->fetch_array();
			extract($row);	
			$this->project_manual_id = $project_manual_id;
			$this->project_name = $project_name;
			$this->project_type = $project_type;
			$this->project_logo = $project_logo;
			$this->project_status = $project_status;
			$this->company_id = $company_id;
			$this->description = $description;
		} else { 
			echo 'This project does not exist or You cant access this project.';
		}
		
	}//level set ends here.
	
	function update_company($company_id, $company_manual_id, $company_name, $business_type, $address1, $address2, $city, $state, $country, $zip_code, $phone, $email, $company_logo, $description) {
		global $db;
		if($_SESSION['user_type'] != 'admin') {
			exit();
		}//checks admin user.
		$query = 'UPDATE companies SET
			company_manual_id="'.$company_manual_id.'",
			company_name="'.$company_name.'",
			business_type="'.$business_type.'",
			address1="'.$address1.'",
			address2="'.$address2.'",
			city="'.$city.'",
			state="'.$state.'",
			country="'.$country.'",
			zip_code="'.$zip_code.'",
			phone="'.$phone.'",
			email="'.$email.'",
			company_logo="'.$company_logo.'",
			description="'.$description.'"
			WHERE company_id='.$company_id.'
			';	
		$result = $db->query($query) or die($db->error);
		return 'Company was updated successfuly!';
		}//update_company function ends here.
	
	function add_project($project_manual_id, $project_name, $project_type, $project_logo, $description, $project_member) { 
		global $db;
		foreach($project_member as $member) { 
			if(!filter_var($member, FILTER_VALIDATE_EMAIL)) {
				if($member != '') { 
				return 'Email is not valid '.$member;
				exit();
				}
			}//if all emails are valid in member section.
		}
		//check manual id if already exist.
		$query = "SELECT * from projects WHERE project_manual_id='".$project_manual_id."'";
		$result = $db->query($query) or die($db->error);
		$num_rows = $result->num_rows;
		
		if($num_rows > 0) { 
			return 'Please chose different manual unique id. The id '.$project_manual_id.' already exists.';
			exit();
		} else { 
			$query = 'INSERT into projects
			(project_id, project_manual_id, project_name, project_type, project_logo, description, project_status, company_id) 
			VALUES(NULL, "'.$project_manual_id.'", "'.$project_name.'", "'.$project_type.'", "'.$project_logo.'", "'.$description.'", "active", "'.$_SESSION['company_id'].'")';
			$result = $db->query($query) or die($db->error);
			$project_id = $db->insert_id;
			$project_log = "New project created.";
			
			project_log($project_id, $project_log);
			
			//processing members here.
			foreach($project_member as $member) { 
				$query = "SELECT * from users WHERE email='".$member."'";
				$result = $db->query($query) or die($db->error);
				$num_rows = $result->num_rows;
				
				if($num_rows > 0) { 
					//add user access here.
					$row = $result->fetch_array();
					$this->add_project_access($row['user_id'], $project_id);
					
					$project_log = "Project access granted to user ".$row['email'];
					 project_log($project_id, $project_log);
					
				} else if($member != '') { 
					//add new user here.
					$email = $member;
					$email_split = explode('@', $member);
					$username = $email_split[0];
					$active_pass = substr(md5(uniqid(rand(), true)), 6, 6);
					$registration_date = date('Y-m-d');
					$password = md5($active_pass);
					
					
					$query = "INSERT INTO users(user_id, first_name, username, email, password, date_register, user_type, status) ".		"VALUES(NULL, '$username',  '$username', '$email', '$password', '$registration_date', 'subscriber', 'activate')";
			$result = $db->query($query) or die($mysqli->error);
			$user_id = $db->insert_id;
			$this->add_project_access($user_id, $project_id);
			$project_log = "Project access granted to user ".$email;
			project_log($project_id, $project_log);
			
			$site_url = get_option('site_url');
			$site_name = get_option('site_name');
			
			$email_message = "<h1>".$site_name."</h1>";
			$email_message .= "You are registered to manage project $project_name.<br />";
			$email_message .= "Your Username is: <strong>".$username.'</strong>';
			$email_message .= "Your Email is: <strong>".$email.'</strong>';
			$email_message .= "Your Password is: <strong>".$active_pass.'</strong>';
			$email_message .= "Kindly click the link below to login into your account and start managing our project.<br />";
			$email_message .= "<a href='".$site_url."'>Login here</a>";
			$email_message .= "<br><br>Thank you again. Please contact us if you need any assistance.";
			
			$subject = "Account created.";
			send_email($email, $subject, $email_message);
				}
			}//foreach members ends here.
			
			return 'Project added successfuly.';
		}
	}//add_project ends here.
	
	function add_project_access($user_id, $project_id) { 
		global $db;
			$query = "SELECT * from project_access WHERE user_id='".$user_id."' AND project_id='".$project_id."'";
			$result = $db->query($query) or die($db->error);
			$rows = $result->num_rows;
			if($rows > 0) { 
				//do nothing.
			} else { 
				$query = "INSERT into project_access(user_id, project_id) VALUES('".$user_id."', '".$project_id."')";
				$result = $db->query($query) or die($db->error);
			}
	}//add project acces ends here,.
	
	function delete_project_access($user_id, $project_id) { 
			global $db;
			$query = "DELETE from project_access WHERE user_id='".$user_id."' AND project_id='".$project_id."'";
			$result = $db->query($query) or die($db->error);
			
			$new_user = new Users;
			$email = $new_user->get_user_info($user_id, 'email');
			
			$project_log = "Project access deleted for user ".$email;
			 project_log($project_id, $project_log);
	}//add project acces ends here,.
	
	function have_project_access($project_id) { 
		global $db;
		$query = "SELECT * from project_access WHERE user_id='".$_SESSION['user_id']."' AND project_id='".$project_id."'";
		$result = $db->query($query) or die($db->error);
		$num_rows = $result->num_rows;
		if($num_rows > 0) { 
			return TRUE;
		} else { 
			return FALSE;
		}
	}//have_project_access.
	
	function list_projects() { 
		global $db;
		$new_company = new CompanyAccess;
		$content = '';
		if(partial_access('admin') || $new_company->have_company_access()) { 
			$query = "SELECT * from projects WHERE project_status='active' AND company_id='".$_SESSION['company_id']."' ORDER by project_name ASC";
			$result = $db->query($query) or die($db->error);
			
			while($row = $result->fetch_array()) { 
				$member_qu = "SELECT * from project_access WHERE project_id='".$row['project_id']."'";
				$member_res = $db->query($member_qu) or die($db->error);
				$total_members = $member_res->num_rows;
				
				$task_query = 'SELECT * from tasks WHERE project_id="'.$row['project_id'].'"';
				$task_result = $db->query($task_query) or die($db->error);
				$tasks = $task_result->num_rows;
			
				$complete_task_query = 'SELECT * from tasks WHERE project_id="'.$row['project_id'].'" AND status="Complete"';
				$complete_task_result = $db->query($complete_task_query) or die($db->error);
				$complete_tasks = $complete_task_result->num_rows;
				
				if($tasks == 0) { 
					$area = 0;
				} else { 
					$area = ($complete_tasks/$tasks)*100;
				}
				
				$content .= '<a href="project_detail.php?project_id='.$row['project_id'].'" style="color:#000;">';
				$content .= '<div class="col-md-4">';
				$content .= '<div class="panel panel-info" style="min-height:220px;">';
				$content .= '<div class="panel-heading"><strong>'.$row['project_name'].'</strong></div>';
				$content .= '<div class="panel-body list-group" style="padding:0px;">';
				$content .= '<div class="list-group-item"><strong>Project Type: </strong>'.$row['project_type'].'</div>';
				$content .= '<div class="list-group-item"><strong>Project Description: </strong>'.substr($row['description'], 0,65).'</div>';
				$content .= '<div class="list-group-item"><strong>Total Members: </strong>'.$total_members.'</div>';
				$content .= '<div class="list-group-item"><div class="progress">
  <div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="'.$area.'" aria-valuemin="0" aria-valuemax="100" style="width:'.$area.'%">
    <span class="sr-only">'.$area.'% Complete (success)</span>
  </div>
</div></div>';
				$content .= '</div>
						     </div>
	    					 </div>
							  </a>';
			}//while loop ends here.
		} else { 
			$query = "SELECT * from projects WHERE project_status='active' AND company_id='".$_SESSION['company_id']."' ORDER by project_name ASC";
			$result = $db->query($query) or die($db->error);
			
			while($row = $result->fetch_array()) { 
				if($this->have_project_access($row['project_id'])) {
				$member_qu = "SELECT * from project_access WHERE project_id='".$row['project_id']."'";
				$member_res = $db->query($member_qu) or die($db->error);
				$total_members = $member_res->num_rows;
				
				$task_query = 'SELECT * from tasks WHERE project_id="'.$row['project_id'].'"';
				$task_result = $db->query($task_query) or die($db->error);
				$tasks = $task_result->num_rows;
			
				$complete_task_query = 'SELECT * from tasks WHERE project_id="'.$row['project_id'].'" AND status="Complete"';
				$complete_task_result = $db->query($complete_task_query) or die($db->error);
				$complete_tasks = $complete_task_result->num_rows;
				
				if($tasks == 0) { 
					$area = 0;
				} else { 
					$area = ($complete_tasks/$tasks)*100;
				}
				
				$content .= '<a href="project_detail.php?project_id='.$row['project_id'].'" style="color:#000;">';
				$content .= '<div class="col-md-4">';
				$content .= '<div class="panel panel-info" style="min-height:220px;">';
				$content .= '<div class="panel-heading"><strong>'.$row['project_name'].'</strong></div>';
				$content .= '<div class="panel-body list-group" style="padding:0px;">';
				$content .= '<div class="list-group-item"><strong>Project Type: </strong>'.$row['project_type'].'</div>';
				$content .= '<div class="list-group-item"><strong>Project Description: </strong>'.substr($row['description'], 0,65).'</div>';
				$content .= '<div class="list-group-item"><strong>Total Members: </strong>'.$total_members.'</div>';
				$content .= '<div class="list-group-item"><div class="progress">
  <div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="'.$area.'" aria-valuemin="0" aria-valuemax="100" style="width:'.$area.'%">
    <span class="sr-only">'.$area.'% Complete (success)</span>
  </div>
</div></div>';
				$content .= '</div>
						     </div>
	    					 </div>
							  </a>';
				}//if have access ends here.
			}//while loop ends here.
		}
		echo $content;
	}//list_projects ends here.
	
	function project_members_count($project_id) { 
		global $db;
		$member_qu = "SELECT * from project_access WHERE project_id='".$project_id."'";
		$member_res = $db->query($member_qu) or die($db->error);
		$total_members = $member_res->num_rows;
		echo $total_members;
	}//project_members count ends here.
	
	function project_members_checkbox($project_id) { 
		global $db;
		$query = "SELECT * from project_access WHERE project_id='".$project_id."'";
		$result = $db->query($query) or die($db->error);
		$num_rows = $result->num_rows;
		$content = '';
		if($num_rows == 0) { 
			$content = 'No member in this project.';
		}
		while($row = $result->fetch_array()) {
			$new_user = new Users;
			$email = $new_user->get_user_info($row['user_id'], 'email');
			$content .= '<div class="checkbox">';
			$content .= '<label>';
			$content .= '<input type="checkbox" name="del_user_access[]" value="'.$row['user_id'].' " /> '.$email;
			$content .= '</label>';
			$content .= '</div>';	
		}//while loop ends here.
		
		echo $content;
	}//project_members_checkbox end hsr
	
	function assign_members($project_id) { 
		global $db;
		$query = "SELECT * from project_access WHERE project_id='".$project_id."'";
		$result = $db->query($query) or die($db->error);
		$num_rows = $result->num_rows;
		$content = '';
		if($num_rows == 0) { 
			$content = 'No member in this project.';
		}
		while($row = $result->fetch_array()) {
			$new_user = new Users;
			$first_name = $new_user->get_user_info($row['user_id'], 'first_name');
			$last_name = $new_user->get_user_info($row['user_id'], 'last_name');
			$content .= '<div class="pull-left">';
			$content .= ' <input type="radio" name="assign" value="'.$row['user_id'].' " /> '.$first_name.' '.$last_name.' &nbsp;';
			$content .= '</div>';	
		}//while loop ends here.
		$content .= '<div class="clearfix"></div>';
		echo $content;
	}//project_members_checkbox end hsr
	
	function include_members($project_id) { 
		global $db;
		$query = "SELECT * from project_access WHERE project_id='".$project_id."'";
		$result = $db->query($query) or die($db->error);
		$num_rows = $result->num_rows;
		$content = '';
		if($num_rows == 0) { 
			$content = 'No member in this project.';
		}
		while($row = $result->fetch_array()) {
			$new_user = new Users;
			$first_name = $new_user->get_user_info($row['user_id'], 'first_name');
			$last_name = $new_user->get_user_info($row['user_id'], 'last_name');
			$content .= '<div class="pull-left">';
			$content .= ' <input type="checkbox" name="include_member[]" value="'.$row['user_id'].' " /> '.$first_name.' '.$last_name.' &nbsp;';
			$content .= '</div>';	
		}//while loop ends here.
		$content .= '<div class="clearfix"></div>';
		echo $content;
	}//project_members_checkbox end hsr
	
	function project_updates($length, $project_id) { 
		global $db;
		if($length == 'all') {
			$query = 'SELECT * from project_logs WHERE project_id="'.$project_id.'" ORDER by log_id DESC';
		} else if($length == '3') { 
			$query = 'SELECT * from project_logs WHERE project_id="'.$project_id.'" ORDER by log_id DESC LIMIT 0,3';
		}
		$result = $db->query($query) or die($db->error);
		$content = '';
		while($row = $result->fetch_array()) { 
			extract($row);
			$new_user = new Users;
			$first_name = $new_user->get_user_info($row['user_id'], 'first_name');
			$last_name = $new_user->get_user_info($row['user_id'], 'last_name');
			$log_datetime = strtotime($log_datetime);
			$log_datetime = date("m/d/y", $log_datetime);
			
			$content .= '<div class="list-group-item">';
			$content .= '<strong>'.$log_datetime.'</strong> '.$description.' <strong>by '.$first_name.' '.$last_name.'</strong>';
			$content .= '</div>';
		}
		echo $content;
	}//project_updates ends here.
	
	function add_project_note($note_title, $note_detail, $project_id) { 
		global $db;
		$datetime = date("Y-m-d H:i:s");
		
		$query = "INSERT into project_notes(note_id, note_date, note_title, note_detail, project_id, user_id) 
				  VALUES(NULL, '".$datetime."', '".$note_title."', '".$note_detail."', '".$project_id."', '".$_SESSION['user_id']."')";
		$result = $db->query($query) or die($db->error);
		
		$project_log = "A note was added ".$note_title;
		 project_log($project_id, $project_log);
		return "Project note added successfuly!";		  
	}//add project_file ends here.
	
	function add_project_file($file_name, $file_desc, $file_url, $project_id) { 
		global $db;
		$datetime = date("Y-m-d H:i:s");
		$query = "INSERT into project_files(file_id, file_datetime, file_name, description, file_url, project_id, user_id) 
				  VALUES(NULL, '".$datetime."', '".$file_name."', '".$file_desc."', '".$file_url."', '".$project_id."', '".$_SESSION['user_id']."')";
		$result = $db->query($query) or die($db->error);
		$project_log = "A file was added ".$file_name;
		 project_log($project_id, $project_log);
		return "Project file added successfuly!";		  
	}//add project_file ends here.
	
	function add_tasks($start_date, $end_date, $title, $description, $assign_to, $list_id, $project_id) { 
		global $db;
		$query = "INSERT into tasks(task_id, start_date, end_date, title, description, status, assigned_to,  project_id, user_id, list_id) 
				  VALUES(NULL, '".$start_date."', '".$end_date."', '".$title."', '".$description."', 'Active', '".$assign_to."', '".$project_id."', '".$_SESSION['user_id']."', '".$list_id."')";
		$result = $db->query($query) or die($db->error);
		
		$new_user = new Users;
		$first_name = $new_user->get_user_info($assign_to, 'first_name');
		$last_name = $new_user->get_user_info($assign_to, 'last_name');
		
		$project_log = "A task ".$title." assigned to ".$first_name." ".$last_name;
		 project_log($project_id, $project_log);
		return "Task added successfuly!";		  
	}//add project_file ends here.
	
	function list_project_notes($project_id) {
		 global $db;
		 
		 $query = "SELECT * from project_notes WHERE project_id='".$project_id."' ORDER by note_id DESC";
		 $result = $db->query($query) or die($db->error);
		 $content = '';
		 
		 while($row = $result->fetch_array()) { 
		 	extract($row);
			
			$company_access = new CompanyAccess;
			
			$content .= '<div class="project_note col-md-3">';
			$content .= '<div class="note_title">';
			$content .= '<h2 class="pull-left">'.$note_title.'</h2>';
			
			$new_user = new Users;
			
			$name = $new_user->get_user_info($user_id, 'first_name').' '.$new_user->get_user_info($user_id, 'last_name');
			
			if(partial_access('admin') || $company_access->have_company_access()) {
			//$content .= '<h2 class="pull-right"><a href="#">Delete</a></h2>';
            } 
            $content .= '<div class="clearfix"></div>';
            $content .= '</div>';
			$content .= '<p><strong>Note Date: </strong>'.$note_date.' <strong>Note By: </strong>'.$name.'</p>';
            $content .= '<div class="note_detail"><p>'.$note_detail.'</p></div>';
            $content .= '</div><!--note ends here.-->';
		 }//while loop ends here.
		 echo $content;
	}//function ends here.
	
	function add_project_todo($todo_title, $todo_description, $project_id) { 
		global $db;
		$datetime = date("Y-m-d H:i:s");
		$query = "INSERT into todo_lists(list_id, datetime, title, description, project_id, user_id) 
				  VALUES(NULL, '".$datetime."', '".$todo_title."', '".$todo_description."', '".$project_id."', '".$_SESSION['user_id']."')";
		$result = $db->query($query) or die($db->error);
		
		$project_log = "A todo list was added ".$todo_title;
		 project_log($project_id, $project_log);
		return "Project todo added successfuly!";		  
	}//add project_file ends here.
	
	function get_project_info($project_id, $term) { 
		global $db;
		$query = "SELECT * from projects WHERE project_id='".$project_id."'";
		$result = $db->query($query) or die($db->error);
		$row = $result->fetch_array();
		return $row[$term];
	}//get  project info ends here.
	
	function get_file_info($file_id, $term) { 
		global $db;
		$query = "SELECT * from project_files WHERE file_id='".$file_id."'";
		$result = $db->query($query) or die($db->error);
		$row = $result->fetch_array();
		return $row[$term];
	}//get  project info ends here.
	
	function get_todos_info($list_id, $term) { 
		global $db;
		$query = "SELECT * from todo_lists WHERE list_id='".$list_id."'";
		$result = $db->query($query) or die($db->error);
		$row = $result->fetch_array();
		return $row[$term];
	}//get  project info ends here.
	
	function get_task_info($task_id, $term) { 
		global $db;
		$query = "SELECT * from tasks WHERE task_id='".$task_id."'";
		$result = $db->query($query) or die($db->error);
		$row = $result->fetch_array();
		return $row[$term];
	}//get  project info ends here.
	
	function list_project_tasks($project_id, $list_id) { 
		global $db;
		$query = "SELECT * from tasks WHERE project_id='".$project_id."' AND list_id='".$list_id."' ORDER by task_id DESC";
		$result = $db->query($query) or die($db->error);
		
		$content = '';
		while($row = $result->fetch_array()) { 
			extract($row);

			$new_user = new Users;
			$first_name = $new_user->get_user_info($user_id, 'first_name');
			$last_name = $new_user->get_user_info($user_id, 'last_name');
			
			$comments = "SELECT * from task_meta WHERE task_id='".$task_id."'";
			$comments_result = $db->query($comments) or die($db->error);
			$comment_num = $comments_result->num_rows;
			
			if($status == 'Active') { 
				$color = 'blue';
			} else { 
				$color = 'green';
			}
			
			$content .= '<tr>';
			$content .= '<td>'.$start_date.'</td>';
			$content .= '<td>'.$end_date.'</td>';
			$content .= '<td>'.$title.'</td>';
			$content .= '<td>'.substr($description, 0,100).'</td>';
			$content .= '<td>'.$first_name.' '.$last_name.'</td>';
			$content .= '<td style="color:'.$color.';">'.$status.'</td>';
			
			$first_name = $new_user->get_user_info($assigned_to, 'first_name');
			$last_name = $new_user->get_user_info($assigned_to, 'last_name');
			
			$content .= '<td>'.$first_name.' '.$last_name.'</td>';
			$content .= '<td>'.$comment_num.'</td>';
			$content .= '<td><a href="project_task.php?project_id='.$project_id.'&task_id='.$task_id.'&list_id='.$list_id.'">View</a></td>';
			$content .= '</tr>';
		}//loop ends here.
		echo $content;
	}//ends here.
	
	function list_project_files($project_id) { 
		global $db;
		$query = "SELECT * from project_files WHERE project_id='".$project_id."' ORDER by file_id DESC";
		$result = $db->query($query) or die($db->error);
		
		$content = '';
		while($row = $result->fetch_array()) { 
			extract($row);

			$new_user = new Users;
			$first_name = $new_user->get_user_info($user_id, 'first_name');
			$last_name = $new_user->get_user_info($user_id, 'last_name');
			
			$comments = "SELECT * from file_meta WHERE file_id='".$file_id."'";
			$comments_result = $db->query($comments) or die($db->error);
			$comment_num = $comments_result->num_rows;
			
			$content .= '<tr>';
			$content .= '<td>'.$file_id.'</td>';
			$content .= '<td>'.$file_name.'</td>';
			$content .= '<td>'.substr($description, 0,100).'</td>';
			$content .= '<td>'.$first_name.' '.$last_name.'</td>';
			$content .= '<td>'.$comment_num.'</td>';
			$content .= '<td><a href="project_file.php?project_id='.$project_id.'&file_id='.$file_id.'">View</a></td>';
			$content .= '</tr>';
		}//loop ends here.
		echo $content;
	}//ends here.
	
	function list_project_tods($project_id) { 
		global $db;
		$query = "SELECT * from todo_lists WHERE project_id='".$project_id."' ORDER by list_id DESC";
		$result = $db->query($query) or die($db->error);
		
		$content = '';
		while($row = $result->fetch_array()) { 
			extract($row);

			$new_user = new Users;
			$first_name = $new_user->get_user_info($user_id, 'first_name');
			$last_name = $new_user->get_user_info($user_id, 'last_name');
			
			$task_query = 'SELECT * from tasks WHERE list_id="'.$list_id.'"';
			$task_result = $db->query($task_query) or die($db->error);
			$tasks = $task_result->num_rows;
			
			$complete_task_query = 'SELECT * from tasks WHERE list_id="'.$list_id.'" AND status="Complete"';
			$complete_task_result = $db->query($complete_task_query) or die($db->error);
			$complete_tasks = $complete_task_result->num_rows;
			
			$content .= '<tr>';
			$content .= '<td>'.$list_id.'</td>';
			$content .= '<td>'.$title.'</td>';
			$content .= '<td>'.substr($description, 0,100).'</td>';
			$content .= '<td>'.$first_name.' '.$last_name.'</td>';
			$content .= '<td>'.$complete_tasks.'/'.$tasks.'</td>';
			$content .= '<td><a href="project_todo_lists.php?project_id='.$project_id.'&list_id='.$list_id.'">View</a></td>';
			$content .= '</tr>';
		}//loop ends here.
		echo $content;
	}//ends here.
	
	function add_discussion($detail, $project_id) { 
		global $db;
		$datetime = date("Y-m-d H:i:s");
		$detail = $db->real_escape_string($detail);
		$query = "INSERT into discussion (discussion_id, datetime, detail, project_id, user_id) 
				VALUES(NULL, '".$datetime."', '".$detail."', '".$project_id."', '".$_SESSION['user_id']."')";
		$result = $db->query($query) or die($db->error);
		return $db->insert_id;		
	}//add discussion function returns discussion id
	
	function add_file_meta($file_id, $discussion_id) { 
		global $db;
		$query = "INSERT into file_meta VALUES(NULL, '".$file_id."', '".$discussion_id."')";
		$result = $db->query($query) or die($db->error);
		return 'Message posted successfuly.';
	}//file meta ends here.
	
	function add_task_meta($task_id, $discussion_id) { 
		global $db;
		$query = "INSERT into task_meta VALUES(NULL, '".$task_id."', '".$discussion_id."')";
		$result = $db->query($query) or die($db->error);
		return 'Message posted successfuly.';
	}//file meta ends here.
	
	function file_discussion($file_id) {
		global $db;
		$query = "SELECT * from file_meta WHERE file_id='".$file_id."' ORDER by file_meta_id ASC";
		$result = $db->query($query) or die($db->error);
		
		$content = '';
		while($row = $result->fetch_array()) {
			$query = "SELECT * from discussion WHERE discussion_id='".$row['discussion_id']."'";
			$result_user = $db->query($query) or die($db->error);
			$row_user = $result_user->fetch_array();
			
			$new_user = new Users;
			$first_name = $new_user->get_user_info($row_user['user_id'], 'first_name');
			$last_name = $new_user->get_user_info($row_user['user_id'], 'last_name');
			$profile_image = $new_user->get_user_info($row_user['user_id'], 'profile_image');
			
			$content .= '<div class="col-sm-3">';
			$content .= '<img src="'.$profile_image.'" class="img-thumbnail" style="width:50px" />';
			$content .= '<h5>By: <strong>'.$first_name.' '.$last_name.'</strong></h5>';
			$content .= $row_user['datetime'];
			$content .= '</div>';
			$content .= '<div class="col-sm-9">';
			$content .= $row_user['detail'];
			$content .= '</div>';
			$content .= '<div class="clearfix"></div>
						 <hr>';
		}//while loop	 
		echo $content;
	}//end of file disucssion.
	
	function task_discussion($task_id) {
		global $db;
		$query = "SELECT * from task_meta WHERE task_id='".$task_id."' ORDER by task_meta_id ASC";
		$result = $db->query($query) or die($db->error);
		
		$content = '';
		while($row = $result->fetch_array()) {
			$query = "SELECT * from discussion WHERE discussion_id='".$row['discussion_id']."'";
			$result_user = $db->query($query) or die($db->error);
			$row_user = $result_user->fetch_array();
			
			$new_user = new Users;
			$first_name = $new_user->get_user_info($row_user['user_id'], 'first_name');
			$last_name = $new_user->get_user_info($row_user['user_id'], 'last_name');
			$profile_image = $new_user->get_user_info($row_user['user_id'], 'profile_image');
			
			$content .= '<div class="col-sm-3">';
			$content .= '<img src="'.$profile_image.'" class="img-thumbnail" style="width:50px" />';
			$content .= '<h5>By: <strong>'.$first_name.' '.$last_name.'</strong></h5>';
			$content .= $row_user['datetime'];
			$content .= '</div>';
			$content .= '<div class="col-sm-9">';
			$content .= $row_user['detail'];
			$content .= '</div>';
			$content .= '<div class="clearfix"></div>
						 <hr>';
		}//while loop	 
		echo $content;
	}//end of Task disucssion.
	
	function change_status($status, $task_id) {
		global $db;
		
		if($status == 'Active') { 
			$stat = 'Complete';
		} else if($status == 'Complete') { 
			$stat = 'Active';
		}
		$query = 'UPDATE tasks SET
			status="'.$stat.'"
			WHERE task_id='.$task_id.'
			';	
		$result = $db->query($query) or die($db->error);
		return 'Status was updated successfuly!';
	}//change status function ends here.
	
	function active_tasks() { 
		global $db;
		$query = "SELECT * from tasks WHERE assigned_to='".$_SESSION['user_id']."' AND status='Active'";
		$result = $db->query($query) or die($db->error);
		
		$content = '';
		while($row = $result->fetch_array()) { 
			extract($row);
			$content .= '<tr>';
			$content .= '<td>';
			$content .= $end_date;
			$content .= '</td>';
			$content .= '<td>';
			$content .= $title;
			$content .= '</td>';
			$content .= '<td>';
			$content .= $description;
			$content .= '</td>';
			$content .= '<td>';
			$content .= $this->get_project_info($project_id, 'project_name');
			$content .= '</td>';
			$content .= '</tr>';
		}
		echo $content;
	}//list active tasks ends here.
}//project class ends here.