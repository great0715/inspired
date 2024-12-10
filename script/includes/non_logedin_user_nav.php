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
          	<li><a href="login.php"><span class="glyphicon glyphicon-log-in"></span> <?php echo $language['sign_in']; ?></a></li>
			<li><a href="register.php"><span class="glyphicon glyphicon-file"></span> <?php echo $language['register']; ?></a></li>
          </ul>
          <ul class="nav navbar-nav navbar-right">
            <li class="dropdown">
            	<a data-toggle="dropdown" class="dropdown-toggle" href="#"><?php echo $language['join_now']; ?> <b class="caret"></b></a>
                <ul class="dropdown-menu">
                	<li><a href="login.php"><span class="glyphicon glyphicon-log-in"></span> <?php echo $language['sign_in']; ?></a></li>
			        <li><a href="register.php"><span class="glyphicon glyphicon-file"></span> <?php echo $language['register']; ?></a></li>
                </ul>
            </li>
		 </ul>
        </div><!--/.nav-collapse -->
      </div>
    </div>