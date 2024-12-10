<?php
	include('system_load.php');
	//Including this file we load system.
	
	//user Authentication.
	authenticate_user('admin');
	
	//user level object
	$new_userlevel = new Userlevel;
	
	//installation form processing when submits.
	if(isset($_POST['settings_submit']) && $_POST['settings_submit'] == 'Yes') {
	//validation to check if fields are empty!
	if($_POST['site_url'] == '') { 
		$message = $language['site_url_empty'];
	} else if($_POST['email_from'] == '') { 
		$message = $language['email_from_required'];
	} else if($_POST['email_to'] == '') { 
		$message = $language['reply_cannot_empty'];
	} else {
		//adding site url
		set_option('site_url', $_POST['site_url']);
		set_option('site_name', $_POST['site_name']);
		set_option('email_from', $_POST['email_from']);
		set_option('email_to', $_POST['email_to']);
		set_option('public_key', $_POST['public_key']);
		set_option('private_key', $_POST['private_key']);
		set_option('redirect_on_logout', $_POST['redirect_on_logout']);
		set_option('language', $_POST['language']);
		set_option('skin', $_POST['skin']);
		set_option('maximum_login_attempts', $_POST['maximum_login_attempts']);
		set_option('wrong_attempts_time', $_POST['wrong_attempts_time']);
		set_option('session_timeout', $_POST['session_timeout']);
		set_option('register_user_level', $_POST['register_user_level']);
		set_option('facebook_api_key', $_POST['facebook_api_key']);
		
		if(isset($_POST['activate_captcha'])) {
			set_option('activate_captcha', $_POST['activate_captcha']);
		} else { 
			set_option('activate_captcha', '0');
		}
		if(isset($_POST['notify_user_group'])) {
			set_option('notify_user_group', $_POST['notify_user_group']);
		} else { 
			set_option('notify_user_group', '0');
		}
		if(isset($_POST['register_verification'])) {
			set_option('register_verification', $_POST['register_verification']);
		} else { 
			set_option('register_verification', '0');
		}
		if(isset($_POST['facebook_login'])) {
			set_option('facebook_login', $_POST['facebook_login']);
		} else { 
			set_option('facebook_login', '0');
		}
		if(isset($_POST['disable_login'])) {
			set_option('disable_login', $_POST['disable_login']);
		} else { 
			set_option('disable_login', '0');
		}
		if(isset($_POST['disable_registration'])) {
			set_option('disable_registration', $_POST['disable_registration']);
		} else { 
			set_option('disable_registration', '0');
		}
		$message = $language['settings_saved1'];
		HEADER('LOCATION: general_settings.php?message='.$message); 
		}//form validations
	}//form processing.

	//Page display settings.
	$page_title = $language['general_setting_page_title']; //You can edit this to change your page title.
	require_once("includes/header.php"); //including header file.

    //display message if exist.
	if(isset($_GET['message']) && $_GET['message'] != '') { 
		echo '<div class="alert alert-success">';
		echo $_GET['message'];
		echo '</div>';
	}
	if(isset($message) && $message != '') { 
		echo '<div class="alert alert-success">';
		echo $message;
		echo '</div>';
	}
?>
                    <div class="col-sm-8">
                    <form name="set_install" id="set_install" action="<?php $_SERVER['PHP_SELF']; ?>" method="post">
                    	
                        <div class="form-group">
                        <label class="control-label"><?php echo $language['site_url']; ?>*:</label>
                        <input type="text" name="site_url" class="form-control" value="<?php echo get_option('site_url'); ?>" required /><small><?php echo $language['site_url_des']; ?></small>
                        </div>
                        
                        <div class="form-group">
                        <label class="control-label"><?php echo $language['site_name']; ?>:</label>
                        <input type="text" name="site_name" class="form-control" value="<?php echo get_option('site_name'); ?>" />
                        </div>
                        
                        <div class="form-group">
                    	<label class="control-label"><?php echo $language['default_system_language']; ?>:</label>
                        <select name="language" class="form-control">
                        	<option <?php if(get_option('language') == 'french'){ echo "selected='selected'"; } ?> value="french">French</option>
                            <option <?php if(get_option('language') == 'english'){ echo "selected='selected'"; } ?> value="english">English</option>
                            <option <?php if(get_option('language') == 'dutch'){ echo "selected='selected'"; } ?> value="dutch">Dutch</option>
                            <option <?php if(get_option('language') == 'german'){ echo "selected='selected'"; } ?> value="german">German</option>
                            <option <?php if(get_option('language') == 'italian'){ echo "selected='selected'"; } ?> value="italian">Italian</option>
                        </select>
                    </div>
                        
                        <div class="form-group">
                    	<label class="control-label"><?php echo $language["select_skin"]; ?>:</label>
                        <select name="skin" class="form-control">
                        	<option <?php if(get_option('skin') == 'default'){ echo "selected='selected'"; } ?> value="default">Default</option>
                            <option <?php if(get_option('skin') == 'yeti'){ echo "selected='selected'"; } ?> value="yeti">Yeti</option>
                            <option <?php if(get_option('skin') == 'united'){ echo "selected='selected'"; } ?> value="united">United</option>
                            <option <?php if(get_option('skin') == 'superhero'){ echo "selected='selected'"; } ?> value="superhero">Superhero</option>
                            <option <?php if(get_option('skin') == 'spacelab'){ echo "selected='selected'"; } ?> value="spacelab">Spacelab</option>
                            <option <?php if(get_option('skin') == 'slate'){ echo "selected='selected'"; } ?> value="slate">Slate</option>
                            <option <?php if(get_option('skin') == 'simplex'){ echo "selected='selected'"; } ?> value="simplex">Simplex</option>
                            <option <?php if(get_option('skin') == 'readable'){ echo "selected='selected'"; } ?> value="readable">Readable</option>
                            <option <?php if(get_option('skin') == 'lumen'){ echo "selected='selected'"; } ?> value="lumen">Lumen</option>
                            <option <?php if(get_option('skin') == 'journal'){ echo "selected='selected'"; } ?> value="journal">Journal</option>
                            <option <?php if(get_option('skin') == 'flatly'){ echo "selected='selected'"; } ?> value="flatly">Flatly</option>
                            <option <?php if(get_option('skin') == 'darkly'){ echo "selected='selected'"; } ?> value="darkly">Darkly</option>
                            <option <?php if(get_option('skin') == 'cyborg'){ echo "selected='selected'"; } ?> value="cyborg">Cyborg</option>
                            <option <?php if(get_option('skin') == 'cosmo'){ echo "selected='selected'"; } ?> value="cosmo">Cosmo</option>
                            <option <?php if(get_option('skin') == 'cerulean'){ echo "selected='selected'"; } ?> value="cerulean">Cerulean</option>
                            <option <?php if(get_option('skin') == 'amelia'){ echo "selected='selected'"; } ?> value="amelia">Amelia</option>
                        </select>
                        </div>
                        
                        <div class="form-group">
                        <label class="control-label"><?php echo $language['redirect_on_logout']; ?>:</label>
                        <input type="text" name="redirect_on_logout" class="form-control" value="<?php echo get_option('redirect_on_logout'); ?>" />
                        </div>
                        
                        <div class="form-group">
                        <label class="control-label"><?php echo $language['disable_registration']; ?>:</label>
                        <input type="checkbox" name="disable_registration" <?php if(get_option('disable_registration') == '1'){echo 'checked="checked"'; }?> value="1" title="<?php echo $language['disable_registration_title']; ?>" />
                        </div>
                        
                        <div class="form-group">
                        <label class="control-label"><?php echo $language['disable_login']; ?>:</label>
                        <input type="checkbox" name="disable_login" <?php if(get_option('disable_login') == '1'){echo 'checked="checked"'; }?> value="1" title="<?php echo $language['disable_login_check_title']; ?>" />
                        </div>
                        
                        <div class="form-group">
                        <label class="control-label"><?php echo $language['notify_user_group']; ?>:</label>
                        <input type="checkbox" name="notify_user_group" <?php if(get_option('notify_user_group') == '1'){echo 'checked="checked"'; }?> value="1" title="<?php echo $language['notify_user_group_title']; ?>" />
                        </div>
                        
                        <div class="form-group">
                        	<label class="control-label"><?php echo $language["default_system_user_type"]; ?></label>
                            <select name="register_user_level" class="form-control">
									<option value=""><?php echo $language["select_user_type"]; ?></option>
                                    <?php $new_userlevel->userlevel_options(get_option('register_user_level')); ?>	                            </select>
                         </div>
                        
                        <div class="form-group">
                        <label class="control-label"><?php echo $language['session_timeout']; ?>:</label>
                        <input type="text" name="session_timeout" class="form-control" value="<?php echo get_option('session_timeout'); ?>" />
                        </div>
                        
                        <div class="form-group">
                        <label class="control-label"><?php echo $language['maximum_login_attempts']; ?>:</label>
                        <input type="text" name="maximum_login_attempts" class="form-control" value="<?php echo get_option('maximum_login_attempts'); ?>" />
                        </div>
                        
                        <div class="form-group">
                        <label class="control-label"><?php echo $language['wrong_attempts_time']; ?>:</label>
                        <input type="text" name="wrong_attempts_time" class="form-control" value="<?php echo get_option('wrong_attempts_time'); ?>" />
                        </div>
                        
                    	<div class="panel-group" id="accordion">
                          <!--stars here.-->
                          <div class="panel panel-default">
                            <div class="panel-heading">
                              <h4 class="panel-title">
                                <a data-toggle="collapse" data-parent="#accordion" href="#facebook_setting">
                                  <?php echo $language['facebook_login_setting']; ?>
                                </a>
                              </h4>
                            </div>
                            <div id="facebook_setting" class="panel-collapse collapse">
                              <div class="panel-body">
                                
                                <div class="form-group">
                                <label class="control-label"><?php echo $language['activate_facebook_login']; ?>:</label>
                                <input type="checkbox" name="facebook_login" <?php if(get_option('facebook_login') == '1'){echo 'checked="checked"'; }?> value="1" title="<?php echo $language['facebook_login_check_title']; ?>" />
                                </div>
                                <div class="form-group">
                                <label class="control-label"><?php echo $language['facebook_api_key_label']; ?>:</label>
                                <input type="text" name="facebook_api_key" class="form-control" value="<?php echo get_option('facebook_api_key'); ?>" /><small><?php echo $language['facebook_api_helper']; ?></small>
                                </div>
                              </div>
                            </div>
                          </div>
                            <!--end here.-->
                          
                          <!--stars here.-->
                          <div class="panel panel-default">
                            <div class="panel-heading">
                              <h4 class="panel-title">
                                <a data-toggle="collapse" data-parent="#accordion" href="#email_setting">
                                  <?php echo $language['email_seeting']; ?>
                                </a>
                              </h4>
                            </div>
                            <div id="email_setting" class="panel-collapse collapse">
                              <div class="panel-body">
                                <p>
									<?php echo $language['email_des_1']; ?>
                                </p>
                                
                                <div class="form-group">
                                <label class="control-label"><?php echo $language['email_from']; ?>*:</label>
                                <input type="text" name="email_from" class="form-control" value="<?php echo get_option('email_from'); ?>"  />
                                </div>
                                
                                <div class="form-group">
                                <label class="control-label"><?php echo $language['reply_to']; ?>*:</label>
                                <input type="text" name="email_to" class="form-control" value="<?php echo get_option('email_to'); ?>" required />
                                </div>
                                <div class="form-group">
                                <label class="control-label"><?php echo $language['activate_without_verification']; ?>:</label>
                                <input type="checkbox" name="register_verification" <?php if(get_option('register_verification') == '1'){echo 'checked="checked"'; }?> value="1" title="<?php echo $language['activate_without_2']; ?>" />
                                </div>
                              </div>
                            </div>
                          </div>
                            <!--end here.-->
                          
                          <!--stars here.-->
                          <div class="panel panel-default">
                            <div class="panel-heading">
                              <h4 class="panel-title">
                                <a data-toggle="collapse" data-parent="#accordion" href="#collapseThree">
                                  <?php echo $language['captcha_settings']; ?>
                                </a>
                              </h4>
                            </div>
                            <div id="collapseThree" class="panel-collapse collapse">
                              <div class="panel-body">
                                <p><?php echo $language['captcha_des_1']; ?> <a href="http://www.google.com/recaptcha/whyrecaptcha" target="_blank"><?php echo $language['captcha_des_2']; ?></a> <?php echo $language['captcha_des_3']; ?></p>
                                <div class="form-group">
                                <label class="control-label"><?php echo $language['activate_captcha']; ?>:</label>
                                <input type="checkbox" name="activate_captcha" <?php if(get_option('activate_captcha') == '1'){echo 'checked="checked"'; }?> value="1" />
                                </div>
                                
                                <div class="form-group">
                                <label class="control-label"><?php echo $language['private_key']; ?>:</label>
                                <input type="text" class="form-control" name="private_key" value="<?php echo get_option('private_key'); ?>" />
                                </div>
                                
                                <div class="form-group">
                                <label class="control-label"><?php echo $language['public_key']; ?>:</label>
                                <input type="text" class="form-control" name="public_key" value="<?php echo get_option('public_key'); ?>" />
                                </div>
                              </div>
                            </div>
                          </div>
                            <!--end here.-->
                        </div>
						<hr />

                    <input type="hidden" name="settings_submit" value="Yes" />
                    <input type="submit" value="<?php echo $language['submit_button']; ?>" class="btn btn-primary" />
            </form>
            <script>
				$(document).ready(function() {
					// validate the Installation form
					$("#set_install").validate();
				});
            </script>
           </div><!--left-side-form ends here.-->                   
<?php
	require_once('includes/sidebar.php');
	require_once("includes/footer.php");
?>