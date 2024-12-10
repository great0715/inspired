<?php
//Announcements Class

class Announcements {
	public $announcement_title;
	public $announcement_detail;
	public $user_type;
	public $announcement_status;
	
	function set_announcement($announcement_id) { 
		global $db;
		$query = 'SELECT * from announcements WHERE announcement_id="'.$announcement_id.'"';
		$result = $db->query($query) or die($db->error);
		$row = $result->fetch_array();
		$this->announcement_title = $row['announcement_title'];
		$this->announcement_detail = $row['announcement_detail'];
		$this->user_type = $row['user_type'];
		$this->announcement_status = $row['announcement_status'];
	}//level set ends here.
	
	function get_latest_announcement() {
		global $db;
		$query = "SELECT * from announcements WHERE announcement_status='active' ORDER by announcement_id DESC";
		$result = $db->query($query) or die($db->error);
		
		while($row = $result->fetch_array()) { 
			$content = '';
			if($row['user_type'] == 'all' || $row['user_type'] == $_SESSION['user_type']) { 
				$content .= '<div class="alert alert-info fade in">
    						 <form action="" method="post">
						     <input type="hidden" name="active_notification" value="No" />
  							 <input type="submit" class="close" value="x" />
 							 </form>';
							
				$content .= '<strong>'.$row['announcement_title'].'</strong><br />'.$row['announcement_detail'];
				$content .= '</div>';
				echo $content;
				//exit();
			}
		}
	}
	function update_announcement($announcement_id, $announcement_title, $announcement_detail, $user_type, $announcement_status) { 
		global $db;
		global $language;
		$query = 'UPDATE announcements SET
				  announcement_title = "'.$announcement_title.'",
				  announcement_detail = "'.$announcement_detail.'",
				  user_type = "'.$user_type.'",
				  announcement_status = "'.$announcement_status.'"
				WHERE announcement_id="'.$announcement_id.'"';
		$result = $db->query($query) or die($db->error);
		return $language["announcement_update_success"];
	}//update user level ends here.
	
	function list_announcements() {
		global $db;
		global $language;
		$query = 'SELECT * from announcements ORDER by announcement_id DESC';
		$result = $db->query($query) or die($db->error);
		
		$content = '<hr>';
		while($row = $result->fetch_array()) { 
		 	extract($row);
			$content .= '<div class="col-md-16">';
			$content .= '<div class="bs-callout bs-callout-info">';
    		$content .= '<h4>'.$announcement_title;
			$content .= '<div class="pull-right" style="width:105px;">';
			$content .= '<form method="post" name="edit" action="manage_announcement.php">';
			$content .= '<input type="hidden" name="edit_announcement" value="'.$announcement_id.'">';
			$content .= '<input type="submit" class="btn btn-default btn-sm pull-left" value="'.$language['edit'].'">';
			$content .= '</form>';
			$content .= '<form method="post" name="delete" onsubmit="return confirm_delete();" action="">';
			$content .= '<input type="hidden" name="delete_announcement" value="'.$announcement_id.'">';
			$content .= '<input type="submit" class="btn btn-default btn-sm pull-right" value="'.$language['delete'].'">';
			$content .= '</form>';
            $content .= '</div><div class="clearfix"></div></h4>';
    		$content .= '<p><strong>Date: </strong>'.$announcement_date.'<strong> User type: </strong>'.$user_type.' <strong> Status: </strong>'.$announcement_status.'</p>';
			$content .= $announcement_detail;
  			$content .= '</div>';
  			$content .= '</div><!--row ends here.--><hr>';
		 }//while loop ends here.
		 echo $content;
	}//list_notes ends here.
	
	function add_announcement($announcement_title, $announcement_detail, $user_type, $announcement_status) { 
		global $db;
		global $language;
		$query = 'INSERT into announcements VALUES(NULL, "'.date("Y-m-d").'", "'.$announcement_title.'", "'.$announcement_detail.'", "'.$user_type.'", "'.$announcement_status.'")';
		$result = $db->query($query) or die($db->error);
		return $language["announcement_added_success"];
	}//add notes ends here.

	function delete_announcement($announcement_id) {
		global $db;
		global $language;
			$query = 'DELETE from announcements WHERE announcement_id="'.$announcement_id.'"';
			$result = $db->query($query) or die($db->error);
			$message = $language["announcement_delete_success"];	
		return $message;
	}//delete level ends here.
}//class ends here.