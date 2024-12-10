<?php
//Notes Class

class Notes {
	public $note_title;
	public $note_detail;
	
	function set_note($note_id) { 
		global $db;
		$query = 'SELECT * from notes WHERE note_id="'.$note_id.'" AND user_id="'.$_SESSION['user_id'].'"';
		$result = $db->query($query) or die($db->error);
		$row = $result->fetch_array();
		$this->note_title = $row['note_title'];
		$this->note_detail = $row['note_detail'];
	}//level set ends here.
	
	function update_note($note_id, $note_title, $note_detail) { 
		global $db;
		global $language;
		$query = 'UPDATE notes SET
				  note_title = "'.$note_title.'",
				  note_detail = "'.$note_detail.'"
				   WHERE note_id="'.$note_id.'" AND user_id="'.$_SESSION['user_id'].'"';
		$result = $db->query($query) or die($db->error);
		return $language["note_update_success"];
	}//update user level ends here.
	
	function list_notes() {
		global $db;
		global $language;
		$query = 'SELECT * from notes WHERE user_id="'.$_SESSION['user_id'].'" ORDER by note_id DESC';
		$result = $db->query($query) or die($db->error);
		
		$content = '';
		while($row = $result->fetch_array()) { 
		 	extract($row);
			
			$content .= '<div class="my_note col-md-3">';
			$content .= '<div class="note_title">';
			$content .= '<h2 class="pull-left">'.$note_title.'</h2>';
			$content .= '<div class="pull-right" style="width:105px;padding-top:15px;">';
			$content .= '<form method="post" name="edit" action="manage_notes.php">';
			$content .= '<input type="hidden" name="edit_note" value="'.$note_id.'">';
			$content .= '<input type="submit" class="btn btn-default btn-sm pull-left" value="'.$language['edit'].'">';
			$content .= '</form>';
			$content .= '<form method="post" name="delete" onsubmit="return confirm_delete();" action="">';
			$content .= '<input type="hidden" name="delete_note" value="'.$note_id.'">';
			$content .= '<input type="submit" class="btn btn-default btn-sm pull-right" value="'.$language['delete'].'">';
			$content .= '</form>';
            $content .= '</div><div class="clearfix"></div>';
            $content .= '</div>';
			$content .= '<p><strong>'.$language["date"].': </strong>'.$note_date.'</p>';
            $content .= '<div class="note_detail"><p>'.$note_detail.'</p></div>';
            $content .= '</div><!--note ends here.-->';
		 }//while loop ends here.
		 echo $content;
	}//list_notes ends here.
	
	function add_note($note_title, $note_detail) { 
		global $db;
		global $language;
		$query = 'INSERT into notes VALUES(NULL, "'.date("Y-m-d").'", "'.$note_title.'", "'.$note_detail.'", "'.$_SESSION['user_id'].'")';
		$result = $db->query($query) or die($db->error);
		return $language["note_added_success"];
	}//add notes ends here.

	function delete_note($note_id) {
		global $db;
		global $language;
			$query = 'DELETE from notes WHERE user_id="'.$_SESSION['user_id'].'" AND note_id="'.$note_id.'"';
			$result = $db->query($query) or die($db->error);
			$message = $language["note_delete_success"];	
		return $message;
	}//delete level ends here.

function notes_widget() {
		global $db;
		$query = 'SELECT * from notes WHERE user_id="'.$_SESSION['user_id'].'" ORDER by note_id DESC LIMIT 3';
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
			$content .= '<div class="list-group-item">';
			$content .= '<h4 class="list-group-item-heading">'.$note_title.'</h4>';
			$content .= '<p class="list-group-item-text">'.$note_detail.'</p>';
			$content .= '</div>';
		}//loop ends here.	
	echo $content;
	}//list_notes ends here.

}//class ends here.