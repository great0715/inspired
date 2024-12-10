<?php
//Company Class

class Company {
	public $company_manual_id;
	public $company_name;
	public $business_type;
	public $address1;
	public $address2;
	public $city;
	public $state;
	public $country;
	public $zip_code;
	public $phone;
	public $email;
	public $company_logo;
	public $description;
	
	function dashboard_companies() { 
		if($_SESSION['user_type'] == 'admin') { 
			$query = "SELECT * from companies ORDER by company_name ASC";
			$result = $db->query($query) or die($db->error);
			$content = '';
			while($row = mysql_fetch_array($result)) { 
				extract($row);
				$content .= '<div class="info_box userinfo alignleft">';
                $content .= '<h3>'.$company_name.'</h3>';
                $content .= '<hr />';
                $content .= '<table width="100%" cellpadding="2" cellspacing="0" border="0">';
                $content .= '<tr>';
					$content .= '<th>Account Name</th>';
					$content .= '<th>Type</th>';
					$content .= '<th>Balance</th>';
					$content .= '</tr>';
				//accounts query ends here.
				$accounts_query = 'SELECT * from accounts WHERE company_id="'.$company_id.'"';
				$accounts_result = mysql_query($accounts_query) or die(mysql_error());
				while($account_row = mysql_fetch_array($accounts_result)) { 
					extract($account_row);
					$content .= '<tr>';
					$content .= '<td>'.$account_title.'</td>';
					$content .= '<td>'.$account_type.'</td>';
					//Balance Query
					$balance_query = 'SELECT SUM(debit), SUM(credit) from transactions WHERE account_id="'.$account_id.'"';
					$balance_result = mysql_query($balance_query) or die(mysql_error());
					$balance_row = mysql_fetch_array($balance_result);
					$balance = $balance_row['SUM(debit)']+$balance_row['SUM(credit)'];
					//balance Query ends here.
					$content .= '<td>'.number_format($balance).'</td>';
					$content .= '</tr>';
				}
				$content .= '</table>';
                $content .= '</div><!--users info ends here.-->';
			}
		} else { 
			$query = "SELECT * from company_access WHERE user_id='".$_SESSION['user_id']."'";
			$result = $db->query($query) or die($db->error);
			$content = '';
			while($row = mysql_fetch_array($result)) {
			$query_company = "SELECT * from companies WHERE company_id='".$row['company_id']."' ORDER BY company_name ASC";
			$result_company = mysql_query($query_company) or die(mysql_error());
			while($row_company = mysql_fetch_array($result_company)) { 
				extract($row_company);
				$content .= '<div class="info_box userinfo alignleft">';
                $content .= '<h3>'.$company_name.'</h3>';
                $content .= '<hr />';
                $content .= '<table width="100%" cellpadding="2" cellspacing="0" border="0">';
                $content .= '<tr>';
				$content .= '<th>Account Name</th>';
				$content .= '<th>Type</th>';
				$content .= '<th>Balance</th>';
				$content .= '</tr>';
				//accounts query ends here.
				$accounts_query = 'SELECT * from accounts WHERE company_id="'.$company_id.'"';
				$accounts_result = mysql_query($accounts_query) or die(mysql_error());
				while($account_row = mysql_fetch_array($accounts_result)) { 
					extract($account_row);
					$content .= '<tr>';
					$content .= '<td>'.$account_title.'</td>';
					$content .= '<td>'.$account_type.'</td>';
					//Balance Query
					$balance_query = 'SELECT SUM(debit), SUM(credit) from transactions WHERE account_id="'.$account_id.'"';
					$balance_result = mysql_query($balance_query) or die(mysql_error());
					$balance_row = mysql_fetch_array($balance_result);
					$balance = $balance_row['SUM(debit)']+$balance_row['SUM(credit)'];
					//balance Query ends here.
					$content .= '<td>'.number_format($balance).'</td>';
					$content .= '</tr>';
				}
				$content .= '</table>';
                $content .= '</div><!--users info ends here.-->';	
			}
			}//loop company access ends here.
		}//query ends here.
		echo $content;
	}//companies info ends here.
	
	function delete_company($company_id) {
		global $db; 
		if($_SESSION['user_type'] == 'admin') { 
			$query_account = "SELECT * from projects WHERE company_id='".$company_id."'";
			$result_account = $db->query($query_account) or die($db->error);
			$num_rows = $result_account->num_rows;
			if($num_rows > 0) { 
				return "This company have projects related to it. Please delete related projects first to delete this company.";
			} else { 
				$del_query = $db->query('DELETE from companies WHERE company_id="'.$company_id.'"') or die($db->error);
				return 'Company was deleted successfuly.';
			}
		} else { 
			return 'You have no permission to delete company.';
		}
	}//delete Account function ends here.
	
	function company_name($company_id) { 
		global $db;
		$query = 'SELECT * from companies WHERE company_id="'.$company_id.'"';
		$result = $db->query($query) or die($db->error);
		$row = $result->fetch_array();
		return $row['company_name'];
	}//company_info ends here.
		
	function set_company($company_id) { 
		global $db;
		if($_SESSION['user_type'] != 'admin') {
			$query_access = "SELECT * from company_access WHERE user_id='".$_SESSION['user_id']."' AND company_id='".$company_id."'";
			$result_access = $db->query($query_access) or die($db->error);
			$row_num = $result_access->num_rows;
			if($row_num < 0) { 
			echo 'You have no access to this company.';
			exit();
			}
		}
		$query = "SELECT * from companies WHERE company_id='".$company_id."'"; 
		$result = $db->query($query) or die($db->error);
		if($result->num_rows > 0) {
			$row = $result->fetch_array();
			extract($row);	
			$this->company_manual_id = $company_manual_id;
			$this->company_name = $company_name;
			$this->business_type = $business_type;
			$this->address1 = $address1;
			$this->address2 = $address2;
			$this->city = $city;
			$this->state = $state;
			$this->country = $country;
			$this->zip_code = $zip_code;
			$this->phone = $phone;
			$this->email = $email;
			$this->company_logo = $company_logo;
			$this->description = $description;
		} else { 
			echo 'This company does not exist or You cant access this company.';
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
	
	function add_company($company_manual_id, $company_name, $business_type, $address1, $address2, $city, $state, $country, $zip_code, $phone, $email, $company_logo, $description) { 
		global $db;
		//check manual id if already exist.
		$query = "SELECT * from companies WHERE company_manual_id='".$company_manual_id."'";
		$result = $db->query($query) or die($db->error);
		$num_rows = $result->num_rows;
		
		if($num_rows > 0) { 
			return 'Please chose different manual unique id. The id '.$company_manual_id.' already exists.';
			exit();
		} else { 
			$query = 'INSERT into companies
			(company_id, company_manual_id, company_name, business_type, address1, address2, city, state, country, zip_code, phone, email, company_logo, description, user_id) 
			VALUES(NULL, "'.$company_manual_id.'", "'.$company_name.'", "'.$business_type.'", "'.$address1.'", "'.$address2.'", "'.$city.'", "'.$state.'", "'.$country.'", "'.$zip_code.'", "'.$phone.'", "'.$email.'", "'.$company_logo.'", "'.$description.'", "'.$_SESSION['user_id'].'")';
			$result = $db->query($query) or die($db->error);
			return 'Company added successfuly.';
		}
	}//add_company ends here.
	
	function list_companies() {
			global $db;
			if($_SESSION['user_type'] == 'admin') { 
				$query = 'SELECT * from companies ORDER by company_name ASC';
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
				if($company_logo != '') { 
					$company_logo = '<img src="'.$company_logo.'" height="30" width="30" />';
				}
				$content .= '<tr class="'.$class.'">';
				$content .= '<td>';
				$content .= $company_manual_id;
				$content .= '</td><td>';
				$content .= $company_name;
				$content .= '</td><td>';
				$content .= $business_type;
				$content .= '</td><td>';
				$content .= $city;
				$content .= '</td><td>';
				$content .= $phone;
				$content .= '</td><td>';
				$content .= $email;
				$content .= '</td><td>';
				$content .= $company_logo;
				$content .= '</td><td>';
				$content .= '<form method="post" name="view_projects" action="project.php">';
				$content .= '<input type="hidden" name="company_id" value="'.$company_id.'">';
				$content .= '<input type="submit" class="btn btn-default btn-sm" value="Projects">';
				$content .= '</form>';
				$content .= '</td>';
				if(partial_access('admin')) { $content .= '<td><form method="post" name="edit" action="manage_company.php">';
				$content .= '<input type="hidden" name="edit_company" value="'.$company_id.'">';
				$content .= '<input type="submit" class="btn btn-default btn-sm" value="Edit">';
				$content .= '</form>';
				$content .= '</td><td>';
				$content .= '<form method="post" name="delete" onsubmit="return confirm_delete();" action="">';
				$content .= '<input type="hidden" name="delete_company" value="'.$company_id.'">';
				$content .= '<input type="submit" class="btn btn-default btn-sm" value="Delete">';
				$content .= '</form>';
				$content .= '</td>'; }
				$content .= '</tr>'; 
				unset($class);
			}//loop ends here.
			} else { 
				$content = '';
				$query = 'SELECT * from companies ORDER by company_name ASC';
				$result = $db->query($query) or die($db->error);
				$count = 0;
			while($row = $result->fetch_array()) { 
				$query = "SELECT * from company_access WHERE user_id='".$_SESSION['user_id']."' AND company_id='".$row['company_id']."'"; 
				$result_ca = $db->query($query) or die($db->error);
				$num_rows = $result_ca->num_rows;
				if($num_rows > 0) { 
					$company_acce = '1';
				} else { 
					$company_acce = '0';
				}
				//checking if user have access to company project
				if($company_acce == '0') { 
					$pr_access = '0';
					$pr_query = "SELECT * from projects WHERE company_id='".$row['company_id']."'";
					$pr_result = $db->query($pr_query) or die($db->error);
					
					while($pr_row = $pr_result->fetch_array()) { 
						$project_obj = new Project;
						
						if($project_obj->have_project_access($pr_row['project_id'])) { 
							$pr_access = '1';
						}	
					}
				}
				
				if($company_acce == '1' || $pr_access == '1') {
				extract($row);
				$count++;
				if($count%2 == 0) { 
					$class = 'even';
				} else { 
					$class = 'odd';
				}
				if($company_logo != '') { 
					$company_logo = '<img src="'.$company_logo.'" height="30" width="30" />';
				}
				$content .= '<tr class="'.$class.'">';
				$content .= '<td>';
				$content .= $company_manual_id;
				$content .= '</td><td>';
				$content .= $company_name;
				$content .= '</td><td>';
				$content .= $business_type;
				$content .= '</td><td>';
				$content .= $city;
				$content .= '</td><td>';
				$content .= $phone;
				$content .= '</td><td>';
				$content .= $email;
				$content .= '</td><td>';
				$content .= $company_logo;
				$content .= '</td><td>';
				$content .= '<form method="post" name="view_accounts" action="project.php">';
				$content .= '<input type="hidden" name="company_id" value="'.$company_id.'">';
				$content .= '<input type="submit" class="btn btn-default btn-sm" value="Projects">';
				$content .= '</form>';
				$content .= '</td>';
				$content .= '</tr>'; 
				unset($class);
				}//if have company access.
			}//loop ends here.
				
			} //if else ends here.
			
		echo $content;
	}//list_levels ends here.
	
	function company_options() {
		global $db; 
		$query = 'SELECT * from companies ORDER by company_name ASC';
		$result = $db->query($query) or die($db->error);
		
			while($row = $result->fetch_array()) { 
				$options .= '<option value="'.$row['company_id'].'">'.$row['company_manual_id'].' | '.ucfirst($row['company_name']).'</option>';
			}
		echo $options;
	}//company options
}//company class ends here.