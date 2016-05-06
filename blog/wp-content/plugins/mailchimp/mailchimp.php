<?php
/*
Plugin Name: MailChimp
Plugin URI: http://www.mailchimp.com/plugins/mailchimp-wordpress-plugin/
Description: The MailChimp plugin allows you to quickly and easily add a signup form for your MailChimp list.
Version: 1.2.2
Author: MailChimp and Crowd Favorite
Author URI: http://mailchimp.com/api/
*/
/*  Copyright 2008  MailChimp.com  (email : api@mailchimp.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

// Version constant for easy CSS refreshes
define('MCSF_VER', '1.2.2');

// What's our permission (capability) threshold
define('MCSF_CAP_THRESHOLD', 'edit_plugins');

// Define our location constants, both MCSF_DIR and MCSF_URL
mailchimpSF_where_am_i();

// Get our MailChimp API class in scope
if (!class_exists('mailchimpSF_MCAPI')) {
	require_once('miniMCAPI.class.php');
}

// includes the widget code so it can be easily called either normally or via ajax
include_once('mailchimp_widget.php');

// includes the backwards compatibility functions
include_once('mailchimp_compat.php');




/**
 * Do the following plugin setup steps here
 *
 * Internationalization
 * Resource (JS & CSS) enqueuing
 *
 * @return void
 */
function mailchimpSF_plugin_init() {
	// Internationalize the plugin
	load_plugin_textdomain( 'mailchimp_i18n', MCSF_LANG_DIR.'po/');

	// Bring in our appropriate JS and CSS resources 
	mailchimpSF_load_resources();
}
add_action( 'init', 'mailchimpSF_plugin_init' );

/**
 * Loads the appropriate JS and CSS resources depending on 
 * settings and context (admin or not)
 *
 * @return void
 */
function mailchimpSF_load_resources() {
	// JS
	if (get_option('mc_use_javascript') == 'on') {
		if (!is_admin()) {
			wp_enqueue_script('mailchimpSF_main_js', MCSF_URL.'js/mailchimp.js', array('jquery', 'jquery-form'), MCSF_VER);
			// some javascript to get ajax version submitting to the proper location
			global $wp_scripts;
			$wp_scripts->localize('mailchimpSF_main_js', 'mailchimpSF', array(
				'ajax_url' => trailingslashit(home_url()),
			));
		}
	}
	
	// CSS
	if (is_admin() && $_GET['page'] == 'mailchimpSF_options') {
		wp_enqueue_style('mailchimpSF_admin_css', MCSF_URL.'css/admin.css');
	}
	else {
		wp_enqueue_style('mailchimpSF_main_css', home_url('?mcsf_action=main_css&ver='.MCSF_VER));
		wp_enqueue_style('mailchimpSF_ie_css', MCSF_URL.'css/ie.css');
		global $wp_styles;
		$wp_styles->add_data( 'mailchimpSF_ie_css', 'conditional', 'IE' );
	}
}

/**
 * Handles requests that as light-weight a load as possible.
 * typically, JS or CSS
 **/
function mailchimpSF_early_request_handler() {
	if (isset($_GET['mcsf_action'])) {
		switch ($_GET['mcsf_action']) {
			case 'main_css':
				header("Content-type: text/css");
				mailchimpSF_main_css();
				exit;
		}
	}
}
add_action('init', 'mailchimpSF_early_request_handler', 0);

/**
 * Outputs the front-end CSS.  This checks several options, so it
 * was best to put it in a Request-handled script, as opposed to 
 * a static file.
 */
function mailchimpSF_main_css() {
	?>
	.mc_error_msg { 
		color: red;
	}
	.mc_success_msg {
		color: green;
	}
	.mc_merge_var{ 
		padding:0;
		margin:0;
	}
<?php
// If we're utilizing custom styles
if (get_option('mc_custom_style')=='on'){
	?>
	#mc_signup_form { 
		padding:5px;
		border-width: <?php echo get_option('mc_form_border_width'); ?>px;
		border-style: <?php echo (get_option('mc_form_border_width')==0) ? 'none' : 'solid'; ?>;
		border-color: #<?php echo get_option('mc_form_border_color'); ?>;
		color: #<?php echo get_option('mc_form_text_color'); ?>;
		background-color: #<?php echo get_option('mc_form_background'); ?>;
	}
	
	
	.mc_custom_border_hdr {
		border-width: <?php echo get_option('mc_header_border_width'); ?>px;
		border-style: <?php echo (get_option('mc_header_border_width')==0) ? 'none' : 'solid'; ?>;
		border-color: #<?php echo get_option('mc_header_border_color'); ?>;
		color: #<?php echo get_option('mc_header_text_color'); ?>;
		background-color: #<?php echo get_option('mc_header_background'); ?>;
		font-size: 1.2em;
		padding:5px 10px;
		width: 100%;
	}
	<?php
}
?>
	#mc_signup_container {}
	#mc_signup_form {}
	#mc_signup_form .mc_var_label {}
	#mc_signup_form .mc_input {}
	#mc-indicates-required { 
		width:100%;
	}
	#mc_display_rewards {}
	#mc_interests_header { 
		font-weight:bold;
	}
	div.mc_interest{
		width:100%;
	}
	#mc_signup_form input.mc_interest {}
	#mc_signup_form select {}
	#mc_signup_form label.mc_interest_label { 
		display:inline;
	}
	.mc_signup_submit { 
		text-align:center; 
	}
	<?php
}


/**
 * Add our settings page to the admin menu
 *
 * @return void
 */
function mailchimpSF_add_pages(){
	// Add settings page for users who can edit plugins
	add_options_page( __( 'MailChimp Setup', 'mailchimp_i18n' ), __( 'MailChimp Setup', 'mailchimp_i18n' ), MCSF_CAP_THRESHOLD, 'mailchimpSF_options', 'mailchimpSF_setup_page');  
}
add_action('admin_menu', 'mailchimpSF_add_pages');

function mailchimpSF_request_handler() {
	if (isset($_POST['mcsf_action'])) {
		switch ($_POST['mcsf_action']) {
			case 'logout':
				// Check capability & Verify nonce
				if (!current_user_can(MCSF_CAP_THRESHOLD) || !wp_verify_nonce($_POST['_mcsf_nonce_action'], 'mc_logout')) {
					wp_die('Cheatin&rsquo; huh?');
				}

				// erase API Key 
			    update_option('mc_apikey', '');
				break;
			case 'update_mc_apikey':
				// Check capability & Verify nonce
				if (!current_user_can(MCSF_CAP_THRESHOLD) || !wp_verify_nonce($_POST['_mcsf_nonce_action'], 'update_mc_api_key')) {
					wp_die('Cheatin&rsquo; huh?');
				}
				
				mailchimpSF_set_api_key(strip_tags(stripslashes($_POST['mc_apikey'])));
				break;
			case 'reset_list':
				// Check capability & Verify nonce
				if (!current_user_can(MCSF_CAP_THRESHOLD) || !wp_verify_nonce($_POST['_mcsf_nonce_action'], 'reset_mailchimp_list')) {
					wp_die('Cheatin&rsquo; huh?');
				}
				
				mailchimpSF_reset_list_settings();
				break;
			case 'change_form_settings':
				if (!current_user_can(MCSF_CAP_THRESHOLD) || !wp_verify_nonce($_POST['_mcsf_nonce_action'], 'update_general_form_settings')) {
					wp_die('Cheatin&rsquo; huh?');
				}
				
				// Update the form settings
				mailchimpSF_save_general_form_settings();
				break;
			case 'mc_submit_signup_form':
				// Validate nonce
				if (!wp_verify_nonce($_POST['_mc_submit_signup_form_nonce'], 'mc_submit_signup_form')) {
					wp_die('Cheatin&rsquo; huh?');
				}
				
				// Attempt the signup
				mailchimpSF_signup_submit();
				
				// Do a different action for html vs. js
				switch ($_POST['mc_submit_type']) {
					case 'html':
						/* Allow to fall through.  The widget will pick up the 
						* global message left over from the signup_submit function */
						break;
					case 'js':
					    if (!headers_sent()){ //just in case...
				            header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT', true, 200);
				        }
					    echo mailchimpSF_global_msg(); // Don't esc_html this, b/c we've already escaped it
						exit;
				}
		}
	}
}
add_action('init', 'mailchimpSF_request_handler');



/**
 * Checks to see if we're storing a password, if so, we need 
 * to upgrade to the API key
 *
 * @return bool
 **/
function mailchimpSF_needs_upgrade() {
	$ig = get_option('mc_interest_groups');
	return get_option('mc_show_interest_groups') == 'on' && (!is_array($ig['groups']) || !is_array($ig['groups'][0]));
}

/**
 * MCAPIv1.2 -> MCAPIv1.3 - update interest groups
 * 2011-02-09 - old password upgrade code deleted as 0.5 is way old
 */
function mailchimpSF_do_upgrade() {
    //left in just for good measure
    delete_option('mc_password');
    $api = new mailchimpSF_MCAPI(get_option('mc_apikey'));
    $ig = $api->listInterestGroupings(get_option('mc_list_id'));
    $ig = $ig[0];
    update_option('mc_interest_groups', $ig);

}

/**
 * Sets the API Key to whatever value was passed to this func
 *
 * @return array of vars
 **/
function mailchimpSF_set_api_key($api_key = '') {
	$delete_setup = false;
	$api = new mailchimpSF_MCAPI($api_key);
	$api->ping();
	if (empty($api->errorCode)) {
		$msg = "<p class='success_msg'>".esc_html(__("Success! We were able to verify your API Key! Let's continue, shall we?", 'mailchimp_i18n'))."</p>";
		update_option('mc_apikey', $api_key);
		$req = $api->getAccountDetails();
		update_option('mc_username', $req['username']);
		update_option('mc_user_id', $req['user_id']);
		$cur_list_id = get_option('mc_list_id');
		if (!empty($cur_list_id)) {
		    //we *could* support paging, but few users have that many lists (and shouldn't)
			$lists = $api->lists(array(),0,100);
			$lists = $lists['data'];
			//but don't delete if the list still exists...
            $delete_setup = true;
			foreach($lists as $list) {
				if ($list['id'] == $cur_list_id) {
					$list_id = $_POST['mc_list_id']; 
					$delete_setup = false;
				}
			}
		}
	} else {
		$msg = "<p class='error_msg'>".esc_html(__('Uh-oh, we were unable to verify your API Key. Please check them and try again!', 'mailchimp_i18n'))."<br/>";
		$msg .= __('The server said:', 'mailchimp_i18n')."<em>".esc_html($api->errorMessage)."</em></p>";
		$username = get_option('mc_username');
		if (empty($username)) {
			$delete_setup = true;
		}
	}

	// Set a global message
	mailchimpSF_global_msg($msg); 
	
	// If we need to delete our setup, do it
	if ($delete_setup){
		mailchimpSF_delete_setup();
	}
	
	//set these for the form fields below
	$user = $_REQUEST['mc_username'];
	
	// return compact('user', 'delete_setup', 'list_id')
}

/**
 * Deletes all mailchimp options
 **/
function mailchimpSF_delete_setup() {
	delete_option('mc_user_id');
	delete_option('mc_rewards');
	delete_option('mc_use_javascript');
	delete_option('mc_use_unsub_link');
	delete_option('mc_list_id');
	delete_option('mc_list_name');
	delete_option('mc_interest_groups');
	delete_option('mc_show_interest_groups');
	$mv = get_option('mc_merge_vars');
	if (is_array($mv)){
        foreach($mv as $var){
	        $opt = 'mc_mv_'.$var['tag'];
	        delete_option($opt);
        }
    }
	delete_option('mc_merge_vars');
}

/**
 * Resets the list settings, there's only one list
 * that can have settings at a time, so no list_id
 * parameter is necessary.
 **/
function mailchimpSF_reset_list_settings() {

	delete_option('mc_list_id');
	delete_option('mc_list_name');
	delete_option('mc_merge_vars');
	delete_option('mc_interest_groups');

	delete_option('mc_use_javascript');
	delete_option('mc_use_unsub_link');
	
	delete_option('mc_header_content');
	delete_option('mc_subheader_content');
	delete_option('mc_submit_text');

	delete_option('mc_custom_style');

	delete_option('mc_header_border_width');
	delete_option('mc_header_border_color');
	delete_option('mc_header_background');
	delete_option('mc_header_text_color');

	delete_option('mc_form_border_width');
	delete_option('mc_form_border_color');
	delete_option('mc_form_background');
	delete_option('mc_form_text_color');
	
	$msg = '<p class="success_msg">'.esc_html(__('Successfully Reset your List selection... Now you get to pick again!', 'mailchimp_i18n')).'</p>';
	mailchimpSF_global_msg($msg);
}

/**
 * Gets or sets a global message based on parameter passed to it
 *
 * @return string/bool depending on get/set
 **/
function mailchimpSF_global_msg($msg = null) {
	global $mcsf_msgs;
	
	// Make sure we're formed properly
	if (!is_array($mcsf_msgs)) {
		$mcsf_msgs = array();
	}
	
	// See if we're getting
	if (is_null($msg)) {
		return implode('', $mcsf_msgs);
	}

	// Must be setting
	$mcsf_msgs[] = $msg;
	return true;
}

/**
 * Sets the default options for the option form
 **/
function mailchimpSF_set_form_defaults($list_name = '') {
	update_option('mc_header_content',__( 'Sign up for', 'mailchimp_i18n' ).' '.$list_name);
	update_option('mc_submit_text',__( 'Subscribe', 'mailchimp_i18n' ));
	
	update_option('mc_custom_style','on');
	update_option('mc_use_javascript','on');
	update_option('mc_use_unsub_link','off');
	update_option('mc_header_border_width','1');
	update_option('mc_header_border_color','E3E3E3');
	update_option('mc_header_background','FFFFFF');
	update_option('mc_header_text_color','CC6600');
	
	update_option('mc_form_border_width','1');
	update_option('mc_form_border_color','C4D3EA');
	update_option('mc_form_background','EEF3F8');
	update_option('mc_form_text_color','555555');
	
	update_option('mc_show_interest_groups', 'on' );
}

/**
 * Saves the General Form settings on the options page
 *
 * @return void
 **/
function mailchimpSF_save_general_form_settings() {
	if (isset($_POST['mc_rewards'])){
		update_option('mc_rewards', 'on');
		$msg = '<p class="success_msg">'.__('Monkey Rewards turned On!', 'mailchimp_i18n').'</p>';
		mailchimpSF_global_msg($msg);
	} else if (get_option('mc_rewards')!='off') {
		update_option('mc_rewards', 'off');
		$msg = '<p class="success_msg">'.__('Monkey Rewards turned Off!', 'mailchimp_i18n').'</p>';
		mailchimpSF_global_msg($msg);
	}
	if (isset($_POST['mc_use_javascript'])){
		update_option('mc_use_javascript', 'on');
		$msg = '<p class="success_msg">'.__('Fancy Javascript submission turned On!', 'mailchimp_i18n').'</p>';
		mailchimpSF_global_msg($msg);
	} else if (get_option('mc_use_javascript')!='off') {
		update_option('mc_use_javascript', 'off');
		$msg = '<p class="success_msg">'.__('Fancy Javascript submission turned Off!', 'mailchimp_i18n').'</p>';
		mailchimpSF_global_msg($msg);
	}
	
	if (isset($_POST['mc_use_unsub_link'])){
		update_option('mc_use_unsub_link', 'on');
		$msg = '<p class="success_msg">'.__('Unsubscribe link turned On!', 'mailchimp_i18n').'</p>';
		mailchimpSF_global_msg($msg);
	} else if (get_option('mc_use_unsub_link')!='off') {
		update_option('mc_use_unsub_link', 'off');
		$msg = '<p class="success_msg">'.__('Unsubscribe link turned Off!', 'mailchimp_i18n').'</p>';
		mailchimpSF_global_msg($msg);
	}

	$content = stripslashes($_POST['mc_header_content']);
	$content = str_replace("\r\n","<br/>", $content);
	update_option('mc_header_content', $content );
	
	$content = stripslashes($_POST['mc_subheader_content']);
	$content = str_replace("\r\n","<br/>", $content);
	update_option('mc_subheader_content', $content );


	$submit_text = stripslashes($_POST['mc_submit_text']);
	$submit_text = str_replace("\r\n","", $submit_text);
	update_option('mc_submit_text', $submit_text);
	
	// Set Custom Style option
	update_option('mc_custom_style', (isset($_POST['mc_custom_style'])) ? 'on' : 'off');

	//we told them not to put these things we are replacing in, but let's just make sure they are listening...
	update_option('mc_header_border_width',str_replace('px','',$_POST['mc_header_border_width']) );
	update_option('mc_header_border_color', str_replace('#','',$_POST['mc_header_border_color']));
	update_option('mc_header_background',str_replace('#','',$_POST['mc_header_background']));
	update_option('mc_header_text_color', str_replace('#','',$_POST['mc_header_text_color']));

	update_option('mc_form_border_width',str_replace('px','',$_POST['mc_form_border_width']) );
	update_option('mc_form_border_color', str_replace('#','',$_POST['mc_form_border_color']));
	update_option('mc_form_background',str_replace('#','',$_POST['mc_form_background']));
	update_option('mc_form_text_color', str_replace('#','',$_POST['mc_form_text_color']));

	update_option('mc_show_interest_groups', (isset($_POST['mc_show_interest_groups'])) ? 'on' : 'off');

	$mv = get_option('mc_merge_vars');
	if (is_array($mv)) {
		foreach($mv as $var){
			$opt = 'mc_mv_'.$var['tag'];
			if (isset($_POST[$opt]) || $var['req']=='Y'){
				update_option($opt,'on');
			} else {
				update_option($opt,'off');
			}
		}
	}
	$msg = '<p class="success_msg">'.esc_html(__('Successfully Updated your List Subscribe Form Settings!', 'mailchimp_i18n')).'</p>';
	mailchimpSF_global_msg($msg);
}

/**
 * Sees if the user changed the list, and updates options accordingly
 **/
function mailchimpSF_change_list_if_necessary($api_key) {
	// Simple permission check before going through all this
	if (!current_user_can(MCSF_CAP_THRESHOLD)) { return; }
	
	$api = new mailchimpSF_MCAPI($api_key);
    //we *could* support paging, but few users have that many lists (and shouldn't)
	$lists = $api->lists(array(),0,100);
	$lists = $lists['data'];
	
	/* If our incoming list ID (the one chosen in the select dropdown)
	is in our array of lists, the set it to be the active list */
	foreach($lists as $list) { 
		if ($list['id'] == $_POST['mc_list_id']) {
			$list_id = $_POST['mc_list_id']; 
			$list_name = $list['name']; 
		}
	}
	
	$orig_list = get_option('mc_list_id');
	if ($list_id != '') {
        update_option('mc_list_id', $list_id);
	    update_option('mc_list_name', $list_name);
		
		// See if the user changed the list
        if ($orig_list != $list_id){
			// The user changed the list, Reset the Form Defaults
			mailchimpSF_set_form_defaults($list_name);
        }
		
		// Grab the merge vars and interest groups
	    $mv = $api->listMergeVars($list_id);
	    $ig = $api->listInterestGroupings($list_id);
	    $ig = $ig[0];
	    update_option('mc_merge_vars', $mv);
	    foreach($mv as $var){
		    $opt = 'mc_mv_'.$var['tag'];
		    //turn them all on by default
		    if ($orig_list != $list_id){
    		    update_option($opt, 'on' );
    		}
	    }
	    update_option('mc_interest_groups', $ig);

	    $msg = '<p class="success_msg">'.
	        sprintf(
				__('Success! Loaded and saved the info for %d Merge Variables and %d Interest Groups from your list', 'mailchimp_i18n'),
				count($mv),
				count($ig['groups'])
			).
	        ' "'.$list_name.'"<br/><br/>'.
		    __('Now you should either Turn On the MailChimp Widget or change your options below, then turn it on.', 'mailchimp_i18n').'</p>';
		mailchimpSF_global_msg($msg);
	}
}

/**
 * Outputs the Settings/Options page
 */
function mailchimpSF_setup_page() {

// See if we need an upgrade
if (mailchimpSF_needs_upgrade()) {
	// remove password option if it's set
	mailchimpSF_do_upgrade();
}

?>
<div class="wrap">

	<h2><?php esc_html_e('MailChimp List Setup', 'mailchimp_i18n');?> </h2>

<?php

$user = get_option('mc_username');
$api_key = get_option('mc_apikey');

// If we have an API Key, see if we need to change the lists and its options
if (!empty($api_key)){
	mailchimpSF_change_list_if_necessary($api_key);
}



// Display our success/error message(s) if have them
if (mailchimpSF_global_msg() != ''){
	// Message has already been html escaped, so we don't want to 2x escape it here
	?>
    <div id="mc_message" class=""><?php echo mailchimpSF_global_msg(); ?></div>
	<?php
}


// If we don't have an API Key, do a login form
if (get_option('mc_apikey') == '') {
	?>
	<div>
		<form method="post" action="options-general.php?page=mailchimpSF_options">
			<h3><?php esc_html_e('Login Info', 'mailchimp_i18n');?></h3>
			<?php esc_html_e('To start using the MailChimp plugin, we first need to login and get your API Key. Please enter your MailChimp API Key below.', 'mailchimp_i18n'); ?>
		
			<br/>
		
			<?php 
			echo sprintf(
				'%1$s <a href="http://www.mailchimp.com/signup/" target="_blank">%2$s</a>', 
				esc_html(__("Don't have a MailChimp account?", 'mailchimp_i18n')), 
				esc_html(__('Try one for Free!', 'mailchimp_i18n'))
			); 
			?>
		
			<br/>
		
			<table class="form-table">
				<tr valign="top">
				<th scope="row"><?php esc_html_e('API Key', 'mailchimp_i18n'); ?>:</th>
				<td>
					<input name="mc_apikey" type="text" id="mc_apikey" class="code" value="<?php echo esc_attr($apikey); ?>" size="32" />
					<br/>
				    <a href="http://admin.mailchimp.com/account/api-key-popup" target="_blank">get your API Key here</a>
				</td>
				</tr>
			</table>
		
			<input type="hidden" name="mcsf_action" value="update_mc_apikey"/>
			<input type="submit" name="Submit" value="<?php esc_attr_e('Save & Check', 'mailchimp_i18n');?>" class="button" />
			<?php wp_nonce_field('update_mc_api_key', '_mcsf_nonce_action'); ?>
		</form>
	</div>

	<?php 
    if (get_option('mc_username')!=''){
		?>
		<strong><?php esc_html_e('Notes', 'mailchimp_i18n'); ?>:</strong>
		<ul>
		    <li><em><?php esc_html_e('Changing your settings at MailChimp.com may cause this to stop working.', 'mailchimp_i18n'); ?></em></li>
		    <li><em><?php esc_html_e('If you change your login to a different account, the info you have setup below will be erased.', 'mailchimp_i18n'); ?></em></li>
		    <li><em><?php esc_html_e('If any of that happens, no biggie - just reconfigure your login and the items below...', 'mailchimp_i18n'); ?></em></li>
		</ul>
	    <br/>
		<?php
    }
} // End of login form

// Start logout form
else {
?>
<table style="min-width:400px;">
	<tr>
		<td><h3><?php esc_html_e('Logged in as', 'mailchimp_i18n');?>: <?php echo esc_html(get_option('mc_username')); ?></h3>
		</td>
		<td>
			<form method="post" action="options-general.php?page=mailchimpSF_options">
				<input type="hidden" name="mcsf_action" value="logout"/>
				<input type="submit" name="Submit" value="<?php esc_attr_e('Logout', 'mailchimp_i18n');?>" class="button" />
				<?php wp_nonce_field('mc_logout', '_mcsf_nonce_action'); ?>
			</form>
		</td>
	</tr>
</table>
<?php
} // End Logout form



//Just get out if nothing else matters...
if (get_option('mc_apikey') == '') return;

if (get_option('mc_apikey')!=''){
	?>
	<h3><?php esc_html_e('Your Lists', 'mailchimp_i18n'); ?></h3>
	
<div>

	<p><?php esc_html_e('Please select the List you wish to create a Signup Form for.', 'mailchimp_i18n'); ?></p>

	<form method="post" action="options-general.php?page=mailchimpSF_options">
		<?php
		$api = new mailchimpSF_MCAPI(get_option('mc_apikey'));
	    //we *could* support paging, but few users have that many lists (and shouldn't)
		$lists = $api->lists(array(),0,100);
		$lists = $lists['data'];

		if (count($lists) == 0) {
			?>
			<span class='error_msg'>
				<?php 
				echo sprintf(
					esc_html(__("Uh-oh, you don't have any lists defined! Please visit %s, login, and setup a list before using this tool!", 'mailchimp_i18n')),
					"<a href='http://www.mailchimp.com/'>MailChimp</a>"
				); 
				?>
			</span>
			<?php
		}
		else {
			?>
	    <table style="min-width:400px">
			<tr>
				<td>
		    	    <select name="mc_list_id" style="min-width:200px;">
			            <option value=""> &mdash; <?php esc_html_e('Select A List','mailchimp_i18n'); ?> &mdash; </option>
						<?php
					    foreach ($lists as $list) {
							$option = get_option('mc_list_id');
							?>
						    <option value="<?php echo esc_attr($list['id']); ?>"<?php selected($list['id'], $option); ?>><?php echo esc_html($list['name']); ?></option>
							<?php
					    }
						?>
					</select>
				</td>
				<td>
					<input type="hidden" name="mcsf_action" value="update_mc_list_id" />
					<input type="submit" name="Submit" value="<?php esc_attr_e('Update List', 'mailchimp_i18n'); ?>" class="button" />
				</td>
			</tr>
			<tr>
				<td colspan="2">
					<strong><?php esc_html_e('Note:', 'mailchimp_i18n'); ?></strong> <em><?php esc_html_e('Updating your list will not cause settings below to be lost. Changing to a new list will.', 'mailchimp_i18n'); ?></em>
				</td>
			</tr>
		</table>
			<?php
		} //end select list
		?>
	</form>
</div>

<br/>

<?php
} 
else {
//display the selected list...
?>

<p class="submit">
	<form method="post" action="options-general.php?page=mailchimpSF_options">
		<input type="hidden" name="mcsf_action" value="reset_list" />
		<input type="submit" name="reset_list" value="<?php esc_attr_e('Reset List Options and Select again', 'mailchimp_i18n'); ?>" class="button" />
		<?php wp_nonce_field('reset_mailchimp_list', '_mcsf_nonce_action'); ?>
	</form>
</p>
<h3><?php esc_html_e('Subscribe Form Widget Settings for this List', 'mailchimp_i18n'); ?>:</h3>
<h4><?php esc_html_e('Selected MailChimp List', 'mailchimp_i18n'); ?>: <?php echo esc_html(get_option('mc_list_name')); ?></h4>
<?php
}
//Just get out if nothing else matters...
if (get_option('mc_list_id') == '') return;


// The main Settings form
?>

<div>
<form method="post" action="options-general.php?page=mailchimpSF_options">
<div style="width:600px;">
<input type="hidden" name="mcsf_action" value="change_form_settings">
<?php wp_nonce_field('update_general_form_settings', '_mcsf_nonce_action'); ?>
<input type="submit" value="<?php esc_attr_e('Update Subscribe Form Settings', 'mailchimp_i18n'); ?>" class="button" />
<table class="widefat">
    <tr valign="top">
    <th scope="row"><?php esc_html_e('Monkey Rewards', 'mailchimp_i18n'); ?>:</th>
    <td><input name="mc_rewards" type="checkbox"<?php if (get_option('mc_rewards')=='on' || get_option('mc_rewards')=='' ) { echo ' checked="checked"'; } ?> id="mc_rewards" class="code" />
    <em><label for="mc_rewards"><?php esc_html_e('turning this on will place a "powered by MailChimp" link in your form that will earn you credits with us. It is optional and can be turned on or off at any time.', 'mailchimp_i18n'); ?></label></em>
    </td>
    </tr>
    <tr valign="top">
    <th scope="row"><?php esc_html_e('Use Javascript Support?', 'mailchimp_i18n'); ?>:</th>
    <td><input name="mc_use_javascript" type="checkbox" <?php checked(get_option('mc_use_javascript'), 'on'); ?> id="mc_use_javascript" class="code" />
    <em><label for="mc_use_javascript"><?php esc_html_e('turning this on will use fancy javascript submission and should degrade gracefully for users not using javascript. It is optional and can be turned on or off at any time.', 'mailchimp_i18n'); ?></label></em>
    </td>
    </tr>
    <tr valign="top">
    <th scope="row"><?php esc_html_e('Include Unsubscribe link?', 'mailchimp_i18n'); ?>:</th>
    <td><input name="mc_use_unsub_link" type="checkbox"<?php checked(get_option('mc_use_unsub_link'), 'on'); ?> id="mc_use_unsub_link" class="code" />
    <em><label for="mc_use_unsub_link"><?php esc_html_e('turning this on will add a link to your host unsubscribe form', 'mailchimp_i18n'); ?></label></em>
    </td>
    </tr>
    <tr valign="top">
	<th scope="row"><?php esc_html_e('Header content', 'mailchimp_i18n'); ?>:</th>
	<td>
	<textarea name="mc_header_content" rows="2" cols="50"><?php echo esc_html(get_option('mc_header_content')); ?></textarea><br/>
	<em><?php esc_html_e('You can fill this with your own Text, HTML markup (including image links), or Nothing!', 'mailchimp_i18n'); ?></em>
	</td>
	</tr>
	
    <tr valign="top">
	<th scope="row"><?php esc_html_e('Sub-header content', 'mailchimp_i18n'); ?>:</th>
	<td>
	<textarea name="mc_subheader_content" rows="2" cols="50"><?php echo esc_html(get_option('mc_subheader_content')); ?></textarea><br/>
	<em><?php esc_html_e('You can fill this with your own Text, HTML markup (including image links), or Nothing!', 'mailchimp_i18n'); ?></em>.
       <?php esc_html_e('This will be displayed under the heading and above the form.', 'mailchimp_i18n'); ?>
	</td>
	</tr>


	<tr valign="top">
	<th scope="row"><?php esc_html_e('Submit Button text', 'mailchimp_i18n'); ?>:</th>
	<td>
	<input type="text" name="mc_submit_text" size="30" value="<?php echo esc_attr(get_option('mc_submit_text')); ?>"/>
	</td>
	</tr>

	<tr valign="top">
	<th scope="row"><?php esc_html_e('Custom Styling', 'mailchimp_i18n'); ?>:</th>
	<td>
	<table class="widefat">

		<tr><th><label for="mc_custom_style"><?php esc_html_e('Turned On?', 'mailchimp_i18n'); ?></label></th><td><input type="checkbox" name="mc_custom_style" id="mc_custom_style"<?php checked(get_option('mc_custom_style'), 'on'); ?> /></td></tr>
        <tr><th colspan="2"><?php esc_html_e('Header Settings (only applies if there are no HTML tags in the Header Content area above)', 'mailchimp_i18n'); ?>:</th></tr>
		<tr><th><?php esc_html_e('Border Width', 'mailchimp_i18n'); ?>:</th><td><input type="text" name="mc_header_border_width" size="3" maxlength="3" value="<?php echo esc_attr(get_option('mc_header_border_width')); ?>"/> px<br/>
			<em><?php esc_html_e('Set to 0 for no border, do not enter', 'mailchimp_i18n'); ?> <strong>px</strong>!</em>
		</td></tr>
		<tr><th><?php esc_html_e('Border Color', 'mailchimp_i18n'); ?>:</th><td>#<input type="text" name="mc_header_border_color" size="7" maxlength="6" value="<?php echo esc_attr(get_option('mc_header_border_color')); ?>"/><br/>
			<em><?php esc_html_e('do not enter initial', 'mailchimp_i18n'); ?> <strong>#</strong></em>
		</td></tr>
		<tr><th><?php esc_html_e('Text Color', 'mailchimp_i18n'); ?>:</th><td>#<input type="text" name="mc_header_text_color" size="7" maxlength="6" value="<?php echo esc_attr(get_option('mc_header_text_color')); ?>"/><br/>
			<em><?php esc_html_e('do not enter initial', 'mailchimp_i18n'); ?> <strong>#</strong></em>
		</td></tr>
		<tr><th><?php esc_html_e('Background Color', 'mailchimp_i18n'); ?>:</th><td>#<input type="text" name="mc_header_background" size="7" maxlength="6" value="<?php echo esc_attr(get_option('mc_header_background')); ?>"/><br/>
			<em><?php esc_html_e('do not enter initial', 'mailchimp_i18n'); ?> <strong>#</strong></em>
		</td></tr>
		
        <tr><th colspan="2"><?php esc_html_e('Form Settings', 'mailchimp_i18n'); ?>:</th></tr>
		<tr><th><?php esc_html_e('Border Width', 'mailchimp_i18n'); ?>:</th><td><input type="text" name="mc_form_border_width" size="3" maxlength="3" value="<?php echo esc_attr(get_option('mc_form_border_width')); ?>"/> px<br/>
			<em><?php esc_html_e('Set to 0 for no border, do not enter', 'mailchimp_i18n'); ?> <strong>px</strong>!</em>
		</td></tr>
		<tr><th><?php esc_html_e('Border Color', 'mailchimp_i18n'); ?>:</th><td>#<input type="text" name="mc_form_border_color" size="7" maxlength="6" value="<?php echo esc_attr(get_option('mc_form_border_color')); ?>"/><br/>
			<em><?php esc_html_e('do not enter initial', 'mailchimp_i18n'); ?> <strong>#</strong></em>
		</td></tr>
		<tr><th><?php esc_html_e('Text Color', 'mailchimp_i18n'); ?>:</th><td>#<input type="text" name="mc_form_text_color" size="7" maxlength="6" value="<?php echo esc_attr(get_option('mc_form_text_color')); ?>"/><br/>
			<em><?php esc_html_e('do not enter initial', 'mailchimp_i18n'); ?> <strong>#</strong></em>
		</td></tr>
		<tr><th><?php esc_html_e('Background Color', 'mailchimp_i18n'); ?>:</th><td>#<input type="text" name="mc_form_background" size="7" maxlength="6" value="<?php echo esc_attr(get_option('mc_form_background')); ?>"/><br/>
			<em><?php esc_html_e('do not enter initial', 'mailchimp_i18n'); ?> <strong>#</strong></em>
		</td></tr>
	</table>
</td>
</tr>
</table>
</div>
<input type="submit" value="<?php esc_attr_e('Update Subscribe Form Settings', 'mailchimp_i18n'); ?>" class="button" />

<?php
// Merge Variables Table
?>
<div style="width:400px;">

<h4><?php esc_html_e('Merge Variables Included', 'mailchimp_i18n'); ?></h4>

<?php
$mv = get_option('mc_merge_vars');

if (count($mv) == 0 || !is_array($mv)){
	?>
	<em><?php esc_html_e('No Merge Variables found.', 'mailchimp_i18n'); ?></em>
	<?php
} else {
	?>
	
	<table class='widefat'>
		<tr valign="top">
			<th><?php esc_html_e('Name', 'mailchimp_i18n');?></th>
			<th><?php esc_html_e('Tag', 'mailchimp_i18n');?></th>
			<th><?php esc_html_e('Required?', 'mailchimp_i18n');?></th>
			<th><?php esc_html_e('Include?', 'mailchimp_i18n');?></th>
		</tr>
	<?php
	foreach($mv as $var){
		?>
		<tr valign="top">
			<td><?php echo esc_html($var['name']); ?></td>
			<td><?php echo esc_html($var['tag']); ?></td>
			<td><?php echo esc_html(($var['req'] == 1) ? 'Y' : 'N'); ?></td>
			<td>
				<?php
				if (!$var['req']){
					$opt = 'mc_mv_'.$var['tag'];
					?>
					<input name="<?php echo esc_attr($opt); ?>" type="checkbox" id="<?php echo esc_attr($opt); ?>" class="code"<?php checked(get_option($opt), 'on'); ?> />
					<?php
				} else {
					?>
					&nbsp;&mdash;&nbsp;
					<?php
				}
				?>
			</td>
		</tr>
		<?php
	}
	?>
	</table>
	<?php
}

?>

<h4><?php esc_html_e('Interest Groups', 'mailchimp_i18n'); ?></h4>

<?php
// Interest Groups Table
$ig = get_option('mc_interest_groups');

if (!is_array($ig) || empty($ig) || $ig == 'N') {
	?>
	<em><?php esc_html_e('No Interest Groups Setup for this List', 'mailchimp_i18n'); ?></em>
	<?php
}
else {
	?>
	<table class='widefat'>
		<tr valign="top">
			<th width="75px">
				<label for="mc_show_interest_groups"><?php esc_html_e('Show?', 'mailchimp_i18n'); ?></label>
			</th>
			<th>
				<input name="mc_show_interest_groups" id="mc_show_interest_groups" type="checkbox" id="mc_show_interest_groups" class="code"<?php checked('on', get_option('mc_show_interest_groups')); ?> />
			</th>
		</tr>
		<tr valign="top">
			<th><?php esc_html_e('Name', 'mailchimp_i18n'); ?>:</th>
			<th><?php echo esc_html($ig['name']); ?></th>
		</tr>
		<tr valign="top">
			<th><?php esc_html_e('Input Type', 'mailchimp_i18n'); ?>:</th>
			<td><?php echo esc_html($ig['form_field']); ?></td>
		</tr>
		<tr valign="top">
			<th><?php esc_html_e('Options', 'mailchimp_i18n'); ?>:</th>
			<td>
				<ul>
				<?php
				foreach($ig['groups'] as $interest){
					?>
					<li><?php echo esc_html($interest['name']); ?></li>
					<?php
				}
				?>
				</ul>
			</td>
		</tr>
	</table>
<?php
}
?>
<p class="submit">
<input type="submit" value="<?php esc_attr_e('Update Subscribe Form Settings', 'mailchimp_i18n'); ?>" class="button" />
</p>
</div>
</form>
</div>
</div><!--wrap-->
<?php
}//mailchimpSF_setup_page()


function mailchimpSF_register_widgets() {
	register_widget('mailchimpSF_Widget');
}
add_action('widgets_init', 'mailchimpSF_register_widgets');

function mailchimpSF_shortcode($atts){
	ob_start();
	mailchimpSF_signup_form();
	return ob_get_clean();
}
add_shortcode('mailchimpsf_form', 'mailchimpSF_shortcode');

/**
 * Attempts to signup a user, per the $_POST args.
 * 
 * This sets a global message, that is then used in the widget 
 * output to retrieve and display that message.
 *
 * @return bool
 */
function mailchimpSF_signup_submit() {
	$mv = get_option('mc_merge_vars');
	$ig = get_option('mc_interest_groups');
	
	$success = true;
	$listId = get_option('mc_list_id');
	$email = $_POST['mc_mv_EMAIL'];
	$merge = $errs = array(); // Set up some vars
	
	// Loop through our Merge Vars, and if they're empty, but required, then print an error, and mark as failed
	foreach($mv as $var) {
		$opt = 'mc_mv_'.$var['tag'];
		if ($var['req'] == 'Y' && trim($_POST[$opt]) == '') {
			$success = false;
			$errs[] = sprintf(__("You must fill in %s.", 'mailchimp_i18n'), esc_html($var['name']));
		}
		else {
			if ($var['tag'] != 'EMAIL') {
				$merge[$var['tag']] = $_POST[$opt];
			}
		}	
	}
	
	// Head back to the beginning of the merge vars array
	reset($mv);
	
	
	if (get_option('mc_show_interest_groups')=='on') {
		switch ($ig['form_field']) {
			case 'select':
			case 'dropdown':
			case 'radio':
				$merge['INTERESTS'] = str_replace(',', '\,', $_POST['interests']);
				break;
			case 'checkbox':
				if (isset($_POST['interests'])) {
					foreach ($_POST['interests'] as $i => $nothing) {
						$merge['INTERESTS'] .= str_replace(',', '\,', $i).',';
					}
				}
				break;
			default: 
				// Nothing
				break;
		}
	}
	
	
	// If we're good
	if ($success) {
		// Clear out empty merge vars
		foreach ($merge as $k => $v) {
			if (trim($v) === '') {
				unset($merge[$k]);
			}
		}
		
		// If we have an empty $merge, then assign empty string.
		if (count($merge) == 0 || $merge == '') {
			$merge = '';
		}

		$api = new mailchimpSF_MCAPI(get_option('mc_apikey'));
		$retval = $api->listSubscribe( $listId, $email, $merge);
		if (!$retval) {
			switch($api->errorCode) {
				case '214' : 
					$errs[] = __("That email address is already subscribed to the list", 'mailchimp_i18n').'.'; 
					break;
				case '250' : 
					list($field, $rest) = explode(' ', $api->errorMessage, 2);
					$errs[] = sprintf(__("You must fill in %s.", 'mailchimp_i18n'), esc_html($field));
					break;
				case '254' : 
					list($i1, $i2, $i3, $field, $rest) = explode(' ',$api->errorMessage,5);
					$errs[] = sprintf(__("%s has invalid content.", 'mailchimp_i18n'), esc_html($field));
					break;
				case '270' : 
					$errs[] = __("An invalid Interest Group was selected", 'mailchimp_i18n').'.';
					break;
				case '502' : 
					$errs[] = __("That email address is invalid", 'mailchimp_i18n').'.';
					break;
				default:
					$errs[] = $api->errorCode.":".$api->errorMessage;
					break;
			}
			$success = false;
		}
		else {
			$msg = "<strong class='mc_success_msg'>".esc_html(__("Success, you've been signed up! Please look for our confirmation email!", 'mailchimp_i18n'))."</strong>";
		}
	}
	
	// If we have errors, then show them
	if (count($errs) > 0) {
		$msg = '<span class="mc_error_msg">';
		foreach($errs as $error){
			$msg .= '&raquo; '.esc_html($error).'<br />';
		}
		$msg .= '</span>';
	}
	
	// Set our global message
	mailchimpSF_global_msg($msg);
	
	return $success;
}



/**********************
 * Utility Functions *
**********************/
/**
 * Utility function to allow placement of plugin in plugins, mu-plugins, child or parent theme's plugins folders
 * 
 * This function must be ran _very early_ in the load process, as it sets up important constants for the rest of the plugin
 */
function mailchimpSF_where_am_i() {
	$locations = array(
		'plugins' => array(
			'dir' => WP_PLUGIN_DIR,
			'url' => WP_PLUGIN_URL,
		),
		'mu_plugins' => array(
			'dir' => WPMU_PLUGIN_DIR,
			'url' => WPMU_PLUGIN_URL,
		),
		'template' => array(
			'dir' => trailingslashit(get_template_directory()).'plugins/',
			'url' => trailingslashit(get_template_directory_uri()).'plugins/',
		),
		'stylesheet' => array(
			'dir' => trailingslashit(get_stylesheet_directory()).'plugins/',
			'url' => trailingslashit(get_stylesheet_directory_uri()).'plugins/',
		),
	);

	// Set defaults
	$mscf_dir = trailingslashit(WP_PLUGIN_DIR).'mailchimp/';
	$mscf_url = trailingslashit(WP_PLUGIN_URL).'mailchimp/';

	// Try our hands at finding the real location
	foreach ($locations as $key => $loc) {
		$dir = trailingslashit($loc['dir']).'mailchimp/';
		$url = trailingslashit($loc['url']).'mailchimp/';
		if (is_file($dir.basename(__FILE__))) {
			$mscf_dir = $dir;
			$mscf_url = $url;
			break;
		}
	}
	// Define our complete filesystem path
	define('MCSF_DIR', $mscf_dir);
	
	/* Lang location needs to be relative *from* ABSPATH, 
	so strip it out of our language dir location */
	define('MCSF_LANG_DIR', str_replace(ABSPATH, '', MCSF_DIR)); 
	
	// Define our complete URL to the plugin folder
	define('MCSF_URL', $mscf_url);
}


?>
