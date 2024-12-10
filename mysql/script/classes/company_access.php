<?php
//CompanyAccess Class

class CompanyAccess {
	
	function add_company_access($user_id, $company_id) { 
		global $db;
		if($_SESSION['user_type'] == 'admin') {
			$query = "SELECT * from company_access WHERE user_id='".$user_id."' AND company_id='".$company_id."'";
			$result = $db->query($query) or die($db->error);
			$rows = $result->num_rows;
			if($rows > 0) { 
				return 'User already have access to this company.';
			} else { 
				$query = "INSERT into company_access(user_id, company_id) VALUES('".$user_id."', '".$company_id."')";
				$result = $db->query($query) or die($db->error);
				return 'Access granted successfuly.';
			}
		} else { 
			return 'You cannot access this feature.';
		}
	}//add company acces ends here,.
	
	function list_company_access() { 
		global $db;
		if($_SESSION['user_type'] != 'admin') {
			echo 'You cannot view this list.';	
		} else {
			$query = "SELECT * from company_access";
			$result = $db->query($query) or die($db->error);
			$options = '';
			while($row = $result->fetch_array()) {
				$query_user = "SELECT * from users WHERE user_id='".$row['user_id']."'";
				$result_user = $db->query($query_user) or die($db->error);
				$row_user = $result_user->fetch_array();
				//user info query ends here.
				$query_company = "SELECT * from companies WHERE company_id='".$row['company_id']."'";
				$result_company = $db->query($query_company) or die($db->error);
				$row_company = $result_company->fetch_array();	
				//company info ends here.
				
				$options .= '<tr>';
				$options .= '<td>'.$row['user_id'].'</td>';
				$options .= '<td>'.$row_user['first_name'].' '.$row_user['last_name'].'</td>';
				$options .= '<td>'.$row_user['email'].'</td>';
				$options .= '<td>'.$row_company['company_name'].'</td>';
				$options .= '<td><form method="post" name="delete" onsubmit="return confirm_delete();" action="">';
				$options .= '<input type="hidden" name="delete_access" value="'.$row['access_id'].'">';
				$options .= '<input type="submit" class="btn btn-default btn-sm" value="Delete Access">';
				$options .= '</form></td>';
				$options .= '</tr>';
			}//while loop ends here.
			echo $options;	
		}
	}//list_company_access function ends here.
	
	function delete_access($access_id) {
			global $db; 
		if($_SESSION['user_type'] == 'admin' && $access_id != '') { 
			$query = "DELETE from company_access WHERE access_id='".$access_id."'";
			$result = $db->query($query) or die($db->error);
			return 'Company access deleted successfuly!';
		}//if admin
	}//delete acces function ends here.
	
	function have_company_access() {
		global $db;
		$query = "SELECT * from company_access WHERE user_id='".$_SESSION['user_id']."' AND company_id='".$_SESSION['company_id']."'"; 
		$result = $db->query($query) or die($db->error);
		$num_rows = $result->num_rows;
		if($num_rows > 0) { 
			return TRUE;
		} else { 
			return FALSE;
		}
	}//have_company_access.
}//company access class ends here.