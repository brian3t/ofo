<?php
/**
 * Displays a MailChimp Signup Form
 **/
function mailchimpSF_signup_form($args = array()) {
	extract($args);

	$mv = get_option('mc_merge_vars');
	$ig = get_option('mc_interest_groups');

	// See if we have valid Merge Vars
	if (!is_array($mv)){
		echo $before_widget;
		?>
		<div class="mc_error_msg">
			<?php esc_html_e('There was a problem loading your MailChimp details. Please re-run the setup process under Settings->MailChimp Setup', 'mailchimp_i18n'); ?>
		</div>
		<?php
		echo $after_widget;
		return;
	}
	
	// Get some options
	$uid = get_option('mc_user_id');
	$list_name = get_option('mc_list_name');
	
	echo $before_widget;

	$header =  get_option('mc_header_content');
	// See if we have custom header content
	if (!empty($header)) {
		// See if we need to wrap the header content in our own div
		if (strlen($header) == strlen(strip_tags($header))){
			echo $before_title ? $before_title : '<div class="mc_custom_border_hdr">';
			echo $header; // don't escape $header b/c it may have HTML allowed
			echo $after_title ? $after_title : '</div><!-- /mc_custom_border_hdr -->';
		}
		else {
			echo $header; // don't escape $header b/c it may have HTML allowed
		}
	}
	
	$sub_heading = trim(get_option('mc_subheader_content'));
	?>
	
<div id="mc_signup">
	<form method="post" action="#mc_signup" id="mc_signup_form">
		<input type="hidden" id="mc_submit_type" name="mc_submit_type" value="html" />
		<input type="hidden" name="mcsf_action" value="mc_submit_signup_form" />
		<?php wp_nonce_field('mc_submit_signup_form', '_mc_submit_signup_form_nonce', false); ?>
		
	<?php 
	if ($sub_heading) { 
		?>
		<div id="mc_subheader">
			<?php echo $sub_heading; ?>
		</div><!-- /mc_subheader -->
		<?php
	} 
	?>
	
	<div class="mc_form_inside">
		
		<div class="updated" id="mc_message">
			<?php echo mailchimpSF_global_msg(); ?>
		</div><!-- /mc_message -->

		<?php
		//don't show the "required" stuff if there's only 1 field to display.
		$num_fields = 0;
		foreach((array)$mv as $var) {
			$opt = 'mc_mv_'.$var['tag'];
			if ($var['req'] || get_option($opt) == 'on') {
				$num_fields++;
			}
		}

		if (is_array($mv)) {
			// head on back to the beginning of the array
			reset($mv);
		}
		
		// Loop over our vars, and output the ones that are set to display
		foreach($mv as $var) {
			$opt = 'mc_mv_'.$var['tag'];
			// See if that var is set as required, or turned on (for display)
			if ($var['req'] || get_option($opt) == 'on') {
				?>
				<div class="mc_merge_var">
					<label for="<?php echo esc_attr($opt); ?>" class="mc_var_label"><?php echo esc_html($var['name']); ?>
				<?php
				if ($var['req'] && $num_fields > 1) {
					?>
					<span class="mc_required">*</span>
					<?php
				}
				?>
					</label>
					
					<br />
					
					<input type="text" size="18" value="" name="<?php echo esc_attr($opt); ?>" id="<?php echo esc_attr($opt); ?>" class="mc_input"/>
				</div><!-- /mc_merge_var -->
				<?php
			}
		}
		
		
		// Show an explanation of the * if there's more than one field
		if ($num_fields > 1){
			?>
			<div id="mc-indicates-required">
				* = <?php esc_html_e('required field', 'mailchimp_i18n'); ?>
			</div><!-- /mc-indicates-required -->
			<?php
		}
		
		
		// Show our Interest groups fields if we have them, and they're set to on
		if ($ig && get_option('mc_show_interest_groups') == 'on') {
			?>
			
			<div id="mc_interests_header">
				<?php echo esc_html($ig['name']); ?>
			</div><!-- /mc_interests_header -->
			
			<div class="mc_interest">

				<?php
				$i=0; // Set our counter
				switch ($ig['form_field']) {
					case 'checkbox':
					case 'checkboxes':
						foreach($ig['groups'] as $interest){
    						$interest = $interest['name'];
							?>
							<input type="checkbox" name="<?php echo esc_attr('interests['.$interest.']'); ?>" id="<?php echo esc_attr('mc_interest_'.$i); ?>" class="mc_interest"/>
							<label for="<?php echo esc_attr('mc_interest_'.$i); ?>" class="mc_interest_label"> <?php echo esc_html($interest); ?></label>
							<br/>
							<?php
							$i++;
						}
						break;
					case 'radio':
						foreach($ig['groups'] as $interest){
    						$interest = $interest['name'];
							?>
							<input type="radio" name="interests" id="<?php echo esc_attr('mc_interest_'.$i); ?>" class="mc_interest" value="<?php echo esc_attr($interest); ?>"/>
							<label for="<?php echo esc_attr('mc_interest_'.$i); ?>" class="mc_interest_label"> <?php echo esc_html($interest); ?></label>
							<br/>
							<?php
							$i++;
						}
						break;
					case 'select':
					case 'dropdown':
						?>
						<select name="interests">
							<option value=""></option>
							<?php
							foreach($ig['groups'] as $interest){
        						$interest = $interest['name'];
								?>
								<option value="<?php echo esc_attr($interest); ?>"><?php echo esc_html($interest); ?></option>
								<?php
							}
							?>
						</select>
						<?php
						break;
				}
				?>
				
			</div><!-- /mc_interest -->
			
			<?php
		}
		?>

		<div class="mc_signup_submit">
			<input type="submit" name="mc_signup_submit" id="mc_signup_submit" value="<?php echo esc_attr(get_option('mc_submit_text')); ?>" class="button" />
		</div><!-- /mc_signup_submit -->
	
	
		<?php
		if ( get_option('mc_use_unsub_link') == 'on') {
        	list($key, $dc) = explode("-",get_option('mc_apikey'),2);
        	if (!$dc) $dc = "us1";
        	$host = 'http://'.$dc.'.list-manage.com';
			?>
			<div id="mc_unsub_link" align="center">
				<a href="<?php echo esc_url($host.'/unsubscribe/?u='.get_option('mc_user_id').'&amp;id='.get_option('mc_list_id')); ?>" target="_blank"><?php esc_html_e('unsubscribe from list', 'mailchimp_i18n'); ?></a>
			</div><!-- /mc_unsub_link -->
			<?php
		}
		if ( get_option('mc_rewards') == 'on') {
			?>
			<br/>
			<div id="mc_display_rewards" align="center">
				<?php esc_html_e('powered by', 'mailchimp_i18n'); ?> <a href="<?php echo esc_url('http://www.mailchimp.com/affiliates/?aid='.get_option('mc_user_id').'&amp;afl=1'); ?>">MailChimp</a>!
			</div><!-- /mc_display_rewards -->
			<?php
		}
		?>
	</div><!-- /mc_form_inside -->
	</form><!-- /mc_signup_form -->
</div><!-- /mc_signup_container -->
	<?php
	echo $after_widget;
}


/**
 * MailChimp Subscribe Box widget class
 */
class mailchimpSF_Widget extends WP_Widget {

	function mailchimpSF_Widget() {
		$widget_ops = array( 
			'description' => __('Displays a MailChimp Subscribe box', 'mailchimp_i18n')
		);
		$this->WP_Widget('mailchimpSF_widget', __('MailChimp Widget', 'mailchimp_i18n'), $widget_ops);
	}

	function widget( $args, $instance ) {
		mailchimpSF_signup_form(array_merge($args, $instance));
	}
}
?>
