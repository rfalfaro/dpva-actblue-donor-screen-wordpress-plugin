<?php
$wpdb->show_errors();
	if(isset($_REQUEST['submit_btn']))
	{
		global $wpdb;
		$setting_input_contribution_form = $_POST["wp_dpva_actblue_donor_screen_contribution_form_identifier"];
		$setting_input_alternate_contribution_form = $_POST["wp_dpva_alternate_actblue_contribution_form_identifier"];
		$setting_input_goal = $_POST["wp_dpva_actblue_donor_screen_goal"];
		$setting_input_title = $_POST["wp_dpva_actblue_donor_screen_title"];
		$setting_input_disclaimer = $_POST["wp_dpva_actblue_donor_screen_disclaimer"];
		$setting_input_active_fl = $_POST["wp_dpva_actblue_donor_screen_active_fl"];

		$settings_table_name = $wpdb->prefix . "dpva_actblue_donor_screen_settings";
		
		$wpdb->query("TRUNCATE TABLE $settings_table_name");

		$wpdb->insert($settings_table_name,array(
					'actblue_contribution_form' => $setting_input_contribution_form,
					'alternate_actblue_contribution_form'=> $setting_input_alternate_contribution_form,
					'goal' =>  $setting_input_goal,
					'title' => $setting_input_title,
					'active_fl' => $setting_input_active_fl,
					'disclaimer' => $setting_input_disclaimer));
	}

	if(isset($_REQUEST['clear_btn']))
	{
		global $wpdb;
		$table_name = $wpdb->prefix . "dpva_actblue_donor_screen";
		$wpdb->query("TRUNCATE TABLE $table_name");
	}

	if(isset($_REQUEST['status_deactivate']))
	{
		global $wpdb;
		$settings_table_name = $wpdb->prefix . "dpva_actblue_donor_screen_settings";
		$query = "UPDATE $settings_table_name SET active_fl='0'";
		$wpdb->query($query);
	}

	if(isset($_REQUEST['status_activate']))
	{
		global $wpdb;
		$settings_table_name = $wpdb->prefix . "dpva_actblue_donor_screen_settings";
		$query = "UPDATE $settings_table_name SET active_fl='1'";
		$wpdb->query($query);
	}

	function dpva_actblue_status()
	{
		global $wpdb;
		$settings_table_name = $wpdb->prefix . "dpva_actblue_donor_screen_settings";
		$status_fl = $wpdb->get_var("SELECT active_fl FROM $settings_table_name");
		return $status_fl;
	}

	$screen_status = dpva_actblue_status();

	function dpva_actblue_donor_screen_get_settings()
	{
		global $wpdb;
		$settings_table_name = $wpdb->prefix . "dpva_actblue_donor_screen_settings";
		$settings_parameters = $wpdb->get_row("SELECT * FROM $settings_table_name");
		return $settings_parameters;
	}
	
	$get_parameters = dpva_actblue_donor_screen_get_settings();
	
	if(sizeof($get_parameters) != '0')
	{
		$setting_data_contribution_form = $get_parameters->actblue_contribution_form;
		$setting_data_alternate_contribution_form = $get_parameters->alternate_actblue_contribution_form;
		$setting_data_goal = $get_parameters->goal;
		$setting_data_title = wp_unslash($get_parameters->title);
		$setting_data_disclaimer = wp_unslash($get_parameters->disclaimer);
	}
	else
	{
		$setting_data_contribution_form = '';
		$setting_data_alternate_contribution_form = '';
		$setting_data_goal = '';
		$setting_data_title = '';
		$setting_data_disclaimer = '';
	}	
	
	function dpva_actblue_donor_count($contribution_form)
	{
		global $wpdb;
		$table_name = $wpdb->prefix . "dpva_actblue_donor_screen";
		$get_donor_count_query = "SELECT COUNT(*) FROM $table_name WHERE contribution_form='$contribution_form'";
		$donor_counter = $wpdb->get_var($get_donor_count_query);
		return $donor_counter;
	}

	function dpva_actblue_global_donor_count()
	{
		global $wpdb;
		$table_name = $wpdb->prefix . "dpva_actblue_donor_screen";
		$get_donor_count_query = "SELECT COUNT(*) FROM $table_name";		
		$donor_counter = $wpdb->get_var($get_donor_count_query);
		return $donor_counter;
	}
	
	$page_parameters = explode("/", $_GET["page"]);
	$plugin_basename = $page_parameters[0];
?>
<div class="wrap">

<h1>ActBlue Donor Screen</h1>

<p>Webhook Listener:<br/>
<strong><?php echo(get_site_url().'/wp-json/actblue/v1/endpoint/'); ?></strong></p>

<p>Donation Screen:<br/>
<strong><a href="<?php echo(get_site_url().'/wp-content/plugins/'.$plugin_basename.'/screen/'); ?>" target="_blank"><?php echo(get_site_url().'/wp-content/plugins/'.$plugin_basename.'/screen/'); ?></a></strong></p>

<hr width="450" align="left">
<form method="post" action="">
<?php if($screen_status == '1') { ?>
<p style="color: #22C52B">Screen is <strong>Active</strong></p>
<input name="status_deactivate" class="button button-primary button-large" type="submit" value="Deactivate">
<?php } else { ?>
<p style="color: #C57222">Screen is <strong>Inactive</strong></p>
<input name="status_activate" class="button button-primary button-large" type="submit" value="Activate">
<?php } ?>
</form>

<hr width="450" align="left">

<h2>Configuration</h2>

<form method="post" action="">
<input type="hidden" name="wp_dpva_actblue_donor_screen_active_fl" value="<?php echo($screen_status); ?>">
<p><label>ActBlue Contribution Form Identifier</label><br/>
<input type="text" name="wp_dpva_actblue_donor_screen_contribution_form_identifier" style="width: 300px;" value="<?php echo $setting_data_contribution_form; ?>" /></p>
<p><label>Alternate ActBlue Contribution Form Identifier</label><br/>
<input type="text" name="wp_dpva_alternate_actblue_contribution_form_identifier" style="width: 300px;" value="<?php echo $setting_data_alternate_contribution_form; ?>" /></p>
<p><label>Goal amount</label><br/>
<input type="text" name="wp_dpva_actblue_donor_screen_goal" style="width: 300px;" value="<?php echo $setting_data_goal; ?>" /></p>
<p><label>Title</label><br/>
<input type="text" name="wp_dpva_actblue_donor_screen_title" style="width: 300px;" value="<?php echo htmlentities($setting_data_title); ?>" /></p>
<p><label>Disclaimer</label><br/>
<input type="text" name="wp_dpva_actblue_donor_screen_disclaimer" style="width: 300px;" value="<?php echo htmlentities($setting_data_disclaimer); ?>" /></p>

<p><input name="submit_btn" class="button button-primary button-large" type="submit" value="Store Settings" /></p>
</form>

<hr width="450" align="left">

<h2>Donor Data</h2>

<?php if(!empty($setting_data_contribution_form)) { ?>
<p>Total registered donations for <?php echo($setting_data_contribution_form); ?>: <strong><?php echo(dpva_actblue_donor_count($setting_data_contribution_form)); ?></strong></p>
<?php } ?>

<?php if(!empty($setting_data_alternate_contribution_form)) { ?>
<p>Total registered donations for <?php echo($setting_data_alternate_contribution_form); ?>: <strong><?php echo(dpva_actblue_donor_count($setting_data_alternate_contribution_form)); ?></strong></p>
<?php } ?>

<p>Total registered donations: <strong><?php echo(dpva_actblue_global_donor_count()); ?></strong></p>

<form method="post" action="" onsubmit="return confirm('Are you sure you want to clear all donor data?');">
<p><input name="clear_btn" class="button button-primary button-large" type="submit" value="Clear Donor Data"></p>
</form>

</div>