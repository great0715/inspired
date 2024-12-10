<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="HandheldFriendly" content="true" />
<meta name="MobileOptimized" content="width" />
<meta name="viewport" content="width=device-width, minimum-scale=1.0, maximum-scale=2.0, user-scalable=no" />

<title><?php echo $page_title; ?></title>
<!--add_bootstrap start here.-->
<?php 
	$skin = get_option('skin');
	if(isset($skin) && $skin == 'default') {
?>		
<link rel="stylesheet" type="text/css" href="css/default/bootstrap.min.css" media="all" />
<link rel="stylesheet" type="text/css" href="css/style.css" media="all" />
<?php } else { ?>
<link rel="stylesheet" type="text/css" href="css/<?php echo $skin; ?>/bootstrap.min.css" media="all" />
<link rel="stylesheet" type="text/css" href="css/style.css" media="all" />
<?php } ?>
<link rel="stylesheet" type="text/css" href="css/ui-lightness/jquery-ui-1.10.3.custom.min.css" media="all" />
<!--addd bootstap ends here.-->
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
<script type="text/javascript" language="javascript" src="js/bootstrap.min.js"></script>
<script type="text/javascript" language="javascript" src="js/jquery-ui-1.10.3.custom.min.js"></script>

<script type="text/javascript" src="js/tinymce/tinymce.min.js"></script>
<script type="text/javascript">
tinymce.init({
    selector: "textarea.tinyst",
	menubar : false,
	toolbar: "styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent",
	placeholder : String
 });
</script>

<style type="text/css" title="currentStyle">
	@import "css/demo_table.css";
</style>
<script type="text/javascript" language="javascript" src="js/jquery.dataTables.min.js"></script>
<script type="text/javascript" charset="utf-8">
	$(document).ready(function() {
		$('#wc_table').dataTable();
	} );
	function confirm_delete() { 
		var del = confirm('<?php echo $language["confirm_delete"]; ?>');
		if(del == true) { 
			return true;
		} else { 
			return false;
		}
	}//delete_confirmation ends here.
	
	$(function() {
		$(".datepick").datepicker({
			inline: true,
			dateFormat: 'yy-mm-dd',
		});
	});
</script>
	<link href="css/croppic.css" rel="stylesheet">
</head>
<body>
<?php if(partial_access('admin')): 
	//nav when user is loged in as admin.
	require_once('admin_nav.php');
elseif(partial_access('all')):
	//nav when user is not admin but loged in.
	require_once('non_admin_nav.php');
else:
	//nav when user is not loged in.
	require_once('non_logedin_user_nav.php');
endif; ?>
<div class="container theme-showcase">
	
    <?php
		//announcement box starts here.
		if(isset($_POST['active_notification'])) { 
			$_SESSION['active_notification'] = $_POST['active_notification'];
		}
		if(isset($_SESSION['active_notification']) && $_SESSION['active_notification'] == 'No'):
		//when notification is not active.
		else:
	 	if(isset($_SESSION['user_type'])){
		$ann_obj = new Announcements;
		$ann_obj->get_latest_announcement();
		}
		endif;//announcement box ends here. ?>
    
	<div class="page-header">
     	<h1><?php echo $page_title; ?></h1>
     </div>