<div class="clearfix"></div>
<br />
<br />
<hr />
<div class="footer">
    <p class="text-muted"><?php echo $language['copyright']; ?> <?php echo date('Y'); ?> <?php echo get_option('site_name'); ?> <?php echo $language['all_rights_reserved']; ?></p>
</div>
</div><!--//container main body-->

<script src="js/croppic.min.js"></script>
<script>
	var croppicContaineroutputOptions = {
			uploadUrl:'includes/img_save_to_file.php',
			cropUrl:'includes/img_crop_to_file.php', 
			outputUrlId:'cropOutput',
			modal:false,
			loaderHtml:'<div class="loader bubblingG"><span id="bubblingG_1"></span><span id="bubblingG_2"></span><span id="bubblingG_3"></span></div> '
	}
	var cropContaineroutput = new Croppic('cropContaineroutput', croppicContaineroutputOptions);
</script>
</body>
</html>