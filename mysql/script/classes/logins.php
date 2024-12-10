<?php
//LOGINS Class

class Logins {
	public $login_title;
	public $login_type;
	public $login_url;
	public $login_username;
	public $login_password;
	public $other_info;
	
	function set_login($login_id) { 
		global $db;
		$query = 'SELECT * from logins WHERE login_id="'.$login_id.'" AND user_id="'.$_SESSION['user_id'].'"';
		$result = $db->query($query) or die($db->error);
		$row = $result->fetch_array();
		extract($row);
		$this->login_title = $login_title;
		$this->login_type = $login_type;
		$this->login_url = $login_url;
		$this->login_username = $login_username;
		$this->login_password = $login_password;
		$this->other_info = $other_info;
	}//level set ends here.
	
	function update_login($edit_login, $login_title, $login_type, $login_url, $login_username, $login_password, $other_info) { 
		global $db;
		$query = 'UPDATE logins SET
				  login_title = "'.$login_title.'",
				  login_type = "'.$login_type.'",
				  login_url = "'.$login_url.'",
				  login_username = "'.$login_username.'",
				  login_password = "'.$login_password.'",
				  other_info = "'.$other_info.'"
				   WHERE login_id="'.$edit_login.'" AND user_id="'.$_SESSION['user_id'].'"';
		$result = $db->query($query) or die($db->error);
		return 'Login information updated Successfuly!';
	}//update user level ends here.
	
	function list_logins() {
		global $db;
		$query = 'SELECT * from logins WHERE user_id="'.$_SESSION['user_id'].'" ORDER by login_id DESC';
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
			$content .= $login_id;
			$content .= '</td><td>';
			$content .= $login_title;
			$content .= '</td><td>';
			$content .= $login_type;
			$content .= '</td><td>';
			$content .= $login_url;
			$content .= '</td><td>';
			$content .= $login_username;
			$content .= '</td><td>';
			$content .= $login_password;
			$content .= '</td><td>';
			$content .= $other_info;
			$content .= '</td><td>';
			$content .= '<form method="post" name="edit" action="manage_logins.php">';
			$content .= '<input type="hidden" name="edit_login" value="'.$login_id.'">';
			$content .= '<input type="submit" class="btn btn-default btn-sm" value="Edit">';
			$content .= '</form>';
			$content .= '</td><td>';
			$content .= '<form method="post" name="delete" onsubmit="return confirm_delete();" action="">';
			$content .= '<input type="hidden" name="delete_login" value="'.$login_id.'">';
			$content .= '<input type="submit" class="btn btn-default btn-sm" value="Delete">';
			$content .= '</form>';
			$content .= '</td>';
			$content .= '</tr>';
			unset($class);
		}//loop ends here.	
	echo $content;
	}//list_notes ends here.
	
	function add_login($login_title, $login_type, $login_url, $login_username, $login_password, $other_info) { 
		global $db;
		$query = 'INSERT into logins VALUES(NULL, "'.$login_title.'", "'.$login_type.'", "'.$login_url.'", "'.$login_username.'", "'.$login_password.'", "'.$other_info.'", "'.$_SESSION['user_id'].'")';
		$result = $db->query($query) or die($db->error);
		return 'Login info added successfuly!';
	}//add notes ends here.

	function delete_login($login_id) {
		global $db;
			$query = 'DELETE from logins WHERE user_id="'.$_SESSION['user_id'].'" AND login_id="'.$login_id.'"';
			$result = $db->query($query) or die($db->error);
			$message = 'Login info was deleted successfuly!';	
		return $message;
	}//delete level ends here.

}//class ends here.