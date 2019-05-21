<?php
	/*
		Plugin Name:	DPVA ActBlue Donor Display Screen
		Description: 	A custom plug-in to integrate ActBlue with a donor display screen.  This plugin relies on the WP-API Basic-Auth plugin in order for ActBlue to connect https://github.com/WP-API/Basic-Auth.
		Version: 		0.1
		Author: 		Ricardo Alfaro
		License:     	GNUGPLv3
		License URI: 	https://www.gnu.org/licenses/gpl.html
	*/

global $dpva_actblue_donor_screen_db_version;
$dpva_actblue_donor_screen_db_version = '0.1';

// TO DO: Make table name and database function as real global variables

/* BEGIN ACTIVATION FUNCTIONS */

function dpva_actblue_donor_screen_install () {
   global $wpdb;
   global $dpva_actblue_donor_screen_db_version;

   $charset_collate = $wpdb->get_charset_collate();
   
   $table_name = $wpdb->prefix . "dpva_actblue_donor_screen";

   $sql = array();
   if( $wpdb->get_var("show tables like '". $table_name . "'") !== $table_name ) {
   		$sql[] = "CREATE TABLE $table_name (
			id int(11) NOT NULL AUTO_INCREMENT,
			firstname varchar(250) COLLATE utf8_unicode_ci DEFAULT NULL,
			lastname varchar(250) COLLATE utf8_unicode_ci DEFAULT NULL,
			created_at timestamp NULL DEFAULT NULL,
			refunded_at timestamp NULL DEFAULT NULL,
			order_number varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
			contribution_form varchar(250) COLLATE utf8_unicode_ci DEFAULT NULL,
			status varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
			amount varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
			UNIQUE KEY id (id)
		) $charset_collate;";
	}

   $settings_table_name = $wpdb->prefix . "dpva_actblue_donor_screen_settings";

	if( $wpdb->get_var("show tables like '". $settings_table_name . "'") !== $settings_table_name ) {
		$sql[] = "CREATE TABLE $settings_table_name (
			id int(11) NOT NULL AUTO_INCREMENT,
			actblue_contribution_form varchar(255) NULL,
			alternate_actblue_contribution_form varchar(255) NULL,
			goal varchar(10) NULL,
			title varchar(255) NULL,
			UNIQUE KEY id (id)
		) $charset_collate;";
	}
	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	dbDelta( $sql );
	add_option( 'dpva_actblue_donor_screen_db_version', $dpva_actblue_donor_screen_db_version );
}

register_activation_hook( __FILE__, 'dpva_actblue_donor_screen_install' );

/* END ACTIVATION FUNCTIONS */

/* BEGIN DEACTIVATION FUNCTIONS */

function dpva_actblue_donor_screen_deactivation() {
	global $wpdb;
	$table = $wpdb->prefix."dpva_actblue_donor_screen";
	$settings_table_name = $wpdb->prefix . "dpva_actblue_donor_screen_settings";
	$wpdb->query("DROP TABLE IF EXISTS $table");
	$wpdb->query("DROP TABLE IF EXISTS $settings_table_name");
}
register_deactivation_hook( __FILE__, 'dpva_actblue_donor_screen_deactivation' );

/* END DEACTIVATION FUNCTIONS */

/* BEGIN PUBLIC FACING FUNCTIONS AND SCRIPT HANDLERS */

function get_dpva_actblue_donor_screen_settings() {
	global $wpdb;

	$table_name = $wpdb->prefix . "dpva_actblue_donor_screen_settings";

	$query =
		"SELECT actblue_contribution_form,alternate_actblue_contribution_form,goal,title
		FROM $table_name";
	$dpva_actblue_donor_screen_settings = $wpdb->get_results($query);
	return $dpva_actblue_donor_screen_settings;
}

/* BEGIN REGISTERING THE SECURE ENDPOINT */

add_action( 'rest_api_init', 'actblue_listener_registration' );

// Registers the ActBlue Secure Endpoint

function actblue_listener_registration() {
  register_rest_route( 'actblue/v1', '/endpoint', array(
    'methods'  => 'POST',
    'callback' => 'actblue_donation',
	'permission_callback' => function() {
		return current_user_can( 'edit_posts' );
    }, 
  ) );

  register_rest_route( 'bcg/v1', '/endpoint', array(
    'methods'  => 'POST',
    'callback' => 'get_bcg_donation', 
  ) );
}

// Handles the ActBlue Webhook notification

function actblue_donation( $request ) {
	global $wpdb;
	$donation_table_name = $wpdb->prefix . "dpva_actblue_donor_screen";

	// Capture the JSON Parameters
	$data = $request->get_json_params();

	// Basic donor information
	$FirstName = $data['donor']['firstname'];
	$LastName = $data['donor']['lastname'];
	
	// Order information
	$CreatedAt = $data['contribution']['createdAt'];
	$RefundedAt = $data['lineitems'][0]['refundedAt'];
	$OrderNumber = $data['contribution']['orderNumber'];
	$ContributionForm = $data['contribution']['contributionForm'];
	$Status = $data['contribution']['status'];
	$Amount = $data['lineitems'][0]['amount'];

	$wpdb->insert($donation_table_name,array(
			'firstname' => $FirstName,
			'lastname' => $LastName,
			'created_at' => $CreatedAt,
			'refunded_at' => $RefundedAt,
			'order_number' => $OrderNumber,
			'contribution_form' => $ContributionForm,
			'status' => $Status,
			'amount' => $Amount));
	
	return new WP_REST_Response( $data, 200 );
}

function get_bcg_donation( $request ) {
	global $wpdb;
	$donation_table_name = $wpdb->prefix . "dpva_actblue_donor_screen";
	$settings_table_name = $wpdb->prefix . "dpva_actblue_donor_screen_settings";

	// Capture the JSON Parameters
	$data = $request->get_json_params();

	// Basic donor information
	$function_name = $data['functionname'];
	switch($function_name)
	{
		case 'getSettings':
			$query = "SELECT title,goal,actblue_contribution_form,alternate_actblue_contribution_form FROM $settings_table_name";
			$query_results = $wpdb->get_results($query);
		break;
		case 'getDonors':
			$contribution_form = $data['contributionForm'];
			$alternate_contribution_form = $data['alternateContributionForm'];
			if($alternate_contribution_form != '')
			{
				$query = "SELECT firstname,lastname,amount,contribution_form FROM $donation_table_name WHERE (contribution_form='$contribution_form' OR contribution_form='$alternate_contribution_form') ORDER BY created_at DESC LIMIT 3";
			} else {
				$query = "SELECT firstname,lastname,amount,contribution_form FROM $donation_table_name WHERE contribution_form='$contribution_form' ORDER BY created_at DESC LIMIT 3";
			}
			$query_results = $wpdb->get_results($query);
		break;
		case 'getTotal':
			$contribution_form = $data['contributionForm'];
			$alternate_contribution_form = $data['alternateContributionForm'];
			if($alternate_contribution_form != '')
			{
				$query = "SELECT FORMAT(SUM(amount),0) AS total_amount FROM $donation_table_name WHERE (contribution_form='$contribution_form' OR contribution_form='$alternate_contribution_form')";
			} else {
				$query = "SELECT FORMAT(SUM(amount),0) AS total_amount FROM $donation_table_name WHERE contribution_form='$contribution_form'";
			}
			$query_results = $wpdb->get_row($query);
		break;
	}
	return new WP_REST_Response( $query_results, 200 );
}

/* ENDS REGISTERING THE SECURE ENDPOINT */

/* BEGIN ADMINISTRATIVE MENU ITEMS */

// Create a custom menu item on the administrative side

function dpva_actblue_donor_screen_custom_menu() {
    $plugin_basename = basename(dirname(__FILE__));
    add_menu_page (
        'ActBlue Donor Screen Settings',
        'Donor Screen',
        'manage_options',
        $plugin_basename.'/admin/dpva-actblue-donor-screen-admin.php',
        '',
        plugin_dir_url( __FILE__ ).'icons/full_screen.png'
    );
}
add_action( 'admin_menu', 'dpva_actblue_donor_screen_custom_menu' );

/* END ADMINISTRATIVE MENU ITEMS */

?>