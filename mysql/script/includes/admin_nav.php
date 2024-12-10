<?php
$message_count = new Messages; 
?>
    <!-- Fixed navbar -->
    <div class="navbar navbar-default navbar-top" role="navigation">
      <div class="container">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
            <span class="sr-only"><?php echo $language['toggle_navigation']; ?></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="<?php echo get_option('site_url'); ?>"><?php echo get_option('site_name'); ?></a>
        </div>
        
        <div class="navbar-collapse collapse">
          <ul class="nav navbar-nav">
            <li><a href="dashboard.php"><?php echo $language['dashboard']; ?></a></li>
            <li><a href="users.php"><?php echo $language['users']; ?></a></li>
            <li class="dropdown">
            	<a data-toggle="dropdown" class="dropdown-toggle" href="company.php"><?php echo $language['companies']; ?> <b class="caret"></b></a>
                <ul class="dropdown-menu">
                	<li><a href="company.php"><?php echo $language['companies']; ?></a></li>
                	<li><a href="company_access.php"><?php echo $language['company_access']; ?></a></li>
                </ul>
            </li>
          <li><a href="project.php"><?php echo $language['projects']; ?></a></li>
          </ul>
          <ul class="nav navbar-nav navbar-right">
          	<li><a href="edit_profile.php?user_id=<?php echo $_SESSION['user_id']; ?>"><?php echo $language['welcome']; ?> <?php echo $_SESSION['first_name'].' '.$_SESSION['last_name']; ?></a></li>
            <li class="dropdown">
            	<a data-toggle="dropdown" class="dropdown-toggle" href="#"><?php echo $language['user_settings']; ?> <b class="caret"></b></a>
                <ul class="dropdown-menu">
                	<li><a href="messages.php"><span class="glyphicon glyphicon-envelope"></span> <?php echo $language['messages']; ?> <span class="badge"><?php $message_count->unread_count(); ?></span></a></li>
                    <li><a href="notes.php"><span class="glyphicon glyphicon-pushpin"></span> <?php echo $language['my_notes']; ?></a></li>
                    <li><a href="logins.php"><span class="glyphicon glyphicon-saved"></span> <?php echo $language['my_logins']; ?></a></li>
                	<li role="presentation" class="divider"></li>
                    <li><a href="general_settings.php"><span class="glyphicon glyphicon-wrench"></span> <?php echo $language['general_settings']; ?></a></li>
                    <li><a href="announcements.php"><span class="glyphicon glyphicon-bullhorn"></span> <?php echo $language['announcements']; ?></a></li>
                    <li><a href="user_levels.php"><span class="glyphicon glyphicon-globe"></span> <?php echo $language['user_groups']; ?></a></li>
			        <li><a href="edit_profile.php?user_id=<?php echo $_SESSION['user_id']; ?>"><span class="glyphicon glyphicon-user"></span> <?php echo $language['edit_profile']; ?></a></li>
                    <li><a href="dashboard.php?logout=1"><span class="glyphicon glyphicon-log-out"></span> <?php echo $language['logout']; ?></a></li>
                </ul>
            </li>
          </ul>
        </div><!--/.nav-collapse -->
      </div>
    </div>