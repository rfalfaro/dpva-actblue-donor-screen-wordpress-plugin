<?php
$wpdb->show_errors();
	if(isset($_REQUEST['submit_btn']))
	{
		global $wpdb;
		$setting_input_contribution_form = $_POST["wp_dpva_actblue_donor_screen_contribution_form_identifier"];
		$setting_input_alternate_contribution_form = $_POST["wp_dpva_alternate_actblue_contribution_form_identifier"];
		$setting_input_goal = $_POST["wp_dpva_actblue_donor_screen_goal"];
		$setting_input_title = $_POST["wp_dpva_actblue_donor_screen_title"];

		$settings_table_name = $wpdb->prefix . "dpva_actblue_donor_screen_settings";
		
		$wpdb->query("TRUNCATE TABLE $settings_table_name");

		$wpdb->insert($settings_table_name,array(
					'actblue_contribution_form' => $setting_input_contribution_form,
					'goal' =>  $setting_input_goal,
					'title' => $setting_input_title));
	}

	function dpva_actblue_donor_screen_get_settings()
	{
		global $wpdb;
		$settings_table_name = $wpdb->prefix . "dpva_actblue_donor_screen_settings";
		$active_data = array();
		$settings_parameters = $wpdb->get_row("SELECT * FROM $settings_table_name");
		return $settings_parameters;
	}

	$get_parameters = dpva_actblue_donor_screen_get_settings();
	if(sizeof($get_parameters) != '0')
	{
		$setting_data_contribution_form = $get_parameters->actblue_contribution_form;
		$setting_data_alternate_contribution_form = $get_parameters->alternate_actblue_contribution_form;
		$setting_data_goal = $get_parameters->goal;
		$setting_data_title = $get_parameters->title;
	}
	else
	{
		$setting_data_contribution_form = '';
		$setting_data_alternate_contribution_form = '';
		$setting_data_goal = '';
		$setting_data_title = '';
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

<h2>Configuration</h2>

<form method="post" action="">
<p><label>ActBlue Contribution Form Identifier</label><br/>
<input type="text" name="wp_dpva_actblue_donor_screen_contribution_form_identifier" style="width: 300px;" value="<?php echo $setting_data_contribution_form; ?>" /></p>
<p><label>Alternate ActBlue Contribution Form Identifier</label><br/>
<input type="text" name="wp_dpva_alternate_actblue_contribution_form_identifier" style="width: 300px;" value="<?php echo $setting_data_alternate_contribution_form; ?>" /></p>
<p><label>Goal amount</label><br/>
<input type="text" name="wp_dpva_actblue_donor_screen_goal" style="width: 300px;" value="<?php echo $setting_data_goal; ?>" /></p>
<p><label>Title</label><br/>
<input type="text" name="wp_dpva_actblue_donor_screen_title" style="width: 300px;" value="<?php echo $setting_data_title; ?>" /></p>
<p><input name="submit_btn" class="button button-primary button-large" type="submit" value="Store Settings" /></p>
</form>

</div>