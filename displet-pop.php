<?php
/*
Plugin Name: Displet Pop
Plugin URI: http://thebyob.com/displet-pop
Description: Displet Pop shows a pop-up window after a 30-seconds on a visitor's 5th pageview, prompting visitors to complete a contact form. Number of seconds and pageviews are customizable options. Uses cookies to avoid over-pestering.
Version: 1.0
Author: Displet
Author URI: http://displet.com/
*/

/*  Copyright 2012  Displet

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

function displetpop_scripts() {
	wp_enqueue_script('jquery');
    wp_register_script( 'jquery-cookie', plugins_url('jquery.cookie.js', __FILE__) );
    wp_enqueue_script( 'jquery-cookie', array('jquery') );
    wp_register_style( 'displetpop-style', plugins_url('style.css', __FILE__) );
    wp_enqueue_style( 'displetpop-style' );
}    
add_action('wp_enqueue_scripts', 'displetpop_scripts');

function displetpop_settings() {
	$setting_vars = array(
		'displetpop_seconds',
		'displetpop_pageviews',
		'displetpop_expiration',
		'displetpop_title',
		'displetpop_subtitle',
		'displetpop_description',
		'displetpop_privacy',
		);
	foreach ( $setting_vars as $setting_var ){
		register_setting( 'displetpop_set', $setting_var );
		$cur_value = get_option( $setting_var );
		if ( $cur_value === false) {
			if ($setting_var == 'displetpop_seconds'){
				update_option( $setting_var, '30' );
			}
			if ($setting_var == 'displetpop_pageviews'){
				update_option( $setting_var, '5' );
			}
			if ($setting_var == 'displetpop_expiration'){
				update_option( $setting_var, '7' );
			}
		}
	}
}
add_action( 'admin_init', 'displetpop_settings' );

function displetpop_menu() {
	add_options_page( 'Displet Pop Settings', 'Displet Pop', 'manage_options', 'displetpop_uid', 'displetpop_options' );
}

function displetpop_options() {
	if ( !current_user_can( 'manage_options' ) )  {
		wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
	}
	echo '<div class="wrap"><h2>Displet Pop Settings</h2><form method="post" action="options.php">';
	settings_fields('displetpop_set');
?>

<style>
.wrap{font-size: 13px; line-height: 17px;font-family: Arial, sans-serif; color: #000; padding-top: 10px;}
.wrap fieldset{margin:10px 0px; padding:15px; padding-top: 0px; border: 1px solid #ccc;}
.wrap fieldset legend{font-size: 13px; font-weight: bold; margin-left: -5px;}
.wrap fieldset span { font-size:11px; font-style:italic; color: #666;}
.wrap fieldset .entry{margin-top:10px; margin-bottom: 5px;}
.wrap fieldset .fieldleft{display: inline-block; width: 100px; text-align: right; vertical-align:top; margin: 3px 5px 0px 0px;}
.wrap fieldset .marleft{margin-left: 105px; margin-top: -5px;}
.wrap fieldset input{margin-bottom: 4px;}
.wrap fieldset textarea{margin-bottom: 0px;}
.wrap fieldset textarea{width: 300px; height: 80px;}
</style>

<fieldset>
	<legend>Settings:</legend>
		<div class="entry">Show popup after <input name="displetpop_seconds" type="text" id="displetpop_seconds" size="1" value="<?php echo get_option('displetpop_seconds'); ?>"/> seconds after visiting <input name="displetpop_pageviews" type="text" id="displetpop_pageviews" size="1" value="<?php echo get_option('displetpop_pageviews'); ?>"/> pages</div>
		<div class="entry">Advanced users: Set cookie to expire after <input name="displetpop_expiration" type="text" id="displetpop_expiration" size="1" value="<?php echo get_option('displetpop_expiration'); ?>"/> days</div>
	</table>
</fieldset>
<fieldset>
	<legend>Content:</legend>
	<table class="form-table">
		<div class="entry"><div class="fieldleft">Title</div><input name="displetpop_title" type="text" id="displetpop_title" size="40" value="<?php echo get_option('displetpop_title'); ?>"/><div class="marleft"><span>First line of popup</span></div></div>
		<div class="entry"><div class="fieldleft">Sub-title</div><textarea name="displetpop_subtitle" type="text" id="displetpop_subtitle"><?php echo get_option('displetpop_subtitle'); ?></textarea><div class="marleft"><span>Below the title</span></div></div>
		<div class="entry"><div class="fieldleft">Description</div><textarea name="displetpop_description" type="text" id="displetpop_description"><?php echo get_option('displetpop_description'); ?></textarea><div class="marleft"><span>Right above the form</span></div></div>
		<div class="entry"><div class="fieldleft">Privacy Info</div><textarea name="displetpop_privacy" type="text" id="displetpop_privacy"><?php echo get_option('displetpop_privacy'); ?></textarea><div class="marleft"><span>At the very bottom</span></div></div>
	</table>
</fieldset>
<p class="submit">
<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
</p>

<?php
	echo '</form></div>';
}
add_action( 'admin_menu', 'displetpop_menu' );

function displetpop_sidebar() {
	register_sidebar(array(
		'name' => 'Displet Pop Form',
		'id' => 'displetpop-form-widget-area',
		'before_widget' => '',
		'after_widget' => '',
		'before_title' => '',
		'after_title' => '',
	));
}
add_action('widgets_init', 'displetpop_sidebar');

function displetpop_markup() { ?>

	<?php ?>
	<div id="displetpop" style="display:none;">
		<div class="shadow"></div>
		<table class="inner">
			<tr></tr>
			<tr>
				<td>
					<div class="popup">
						<center>
						<div class="tit">
							<div class="inner">
								<div class="inner2">
									<?php echo get_option('displetpop_title'); ?>
								</div><!--// .inner2 -->
							</div><!--// .inner -->
						</div><!--// .tit -->
						</center>
						<div class="subtit">
							<?php echo get_option('displetpop_subtitle'); ?>
						</div><!--// .subtit -->
						<div class="form">
							<div class="description">
								<?php echo get_option('displetpop_description'); ?>
							</div><!--// .description -->
							<?php if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar('Displet Pop Form') ) : ?>
						  		Insert Contact Form on Widgets Page
							<?php endif; ?>
							<div class="clear"><!-- --></div>
						</div><!--// .form -->
						<div class="privacy">
							<?php echo get_option('displetpop_privacy'); ?>
						</div><!--// .privacy -->
						<div class="close"><a href="javascript:void(0);">[close]</a></div>
					</div><!--// .popup -->
				</td>
			</tr>
			<tr></tr>
		</table>
	</div>
	
<?php
	
}
add_action('wp_footer', 'displetpop_markup');

function init_sessions() {
    if (!session_id()) {
        session_start();
    }  
	if(isset($_SESSION['views']))
	    $_SESSION['views']++;
	else
	    $_SESSION['views'] = 1;
	
}
add_action('init', 'init_sessions');

function displetpop_action() { ?>

<script>
// Start allowance of jQuery to $ shortcut
jQuery(document).ready(function($){

	// Open and close popup, session management
	$('#displetpop .close a, #displetpop .shadow').click(function(){
		$('#displetpop').hide();
		$('body').removeClass('displetpop');
	});
	function displetPop(){
		$('#displetpop').show();
		$('body').addClass('displetpop');
		$.cookie('recentpop','yes', {expires:<?php echo get_option('displetpop_expiration'); ?>}, {path:'/'});
	}
	if ($.cookie('recentpop') != 'yes' && '<?php echo $_SESSION["views"]; ?>' == '<?php echo get_option("displetpop_pageviews"); ?>'){
		window.setTimeout(displetPop, <?php echo 1000*get_option('displetpop_seconds'); ?>);	
	}
	
// Ends allowance of jQuery to $ shortcut
});
</script>

<?php

}

add_action('wp_head', 'displetpop_action');

?>
