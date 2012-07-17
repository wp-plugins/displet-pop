<?php
/*
Plugin Name: Displet Pop
Plugin URI: http://displet.com/displet-pop
Description: Displet Pop shows a pop-up window 30 seconds after the page loads, prompting visitors to complete a contact form or other action. Uses a week long cookie to avoid over-pestering.
Version: 1.2
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
    wp_register_style( 'style', plugins_url('style.css', __FILE__) );
    wp_enqueue_style( 'style' );
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
		'displetpop_testmode',
		'displetpop_style',
		'displetpop_customstyles',
		'displetpop_path',
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
			if ($setting_var == 'displetpop_style'){
				update_option( $setting_var, 'default' );
			}
		}
	}
}
add_action( 'admin_init', 'displetpop_settings' );

function displetpop_menu() {
	add_menu_page('Displet Tools',
		'Displet Tools',
		'administrator',
		'displettools-uid-slug',
		'',
		plugins_url('displetreader-wordpress-plugin/images/displet.png'),
		76
		);
	add_submenu_page('displettools-uid-slug',
		'Displet Pop Settings',
		'Displet Pop Settings',
		'administrator',
		'displetpop-uid-slug-submenu',
		'displetpop_options'
		);
	remove_submenu_page('displettools-uid-slug', 'displettools-uid-slug');
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
		<div class="entry">Show popup after <input name="displetpop_seconds" type="text" id="displetpop_seconds" size="1" value="<?php echo get_option('displetpop_seconds'); ?>"/> second(s) after visiting at least <input name="displetpop_pageviews" type="text" id="displetpop_pageviews" size="1" value="<?php echo get_option('displetpop_pageviews'); ?>"/> pages <span>Will only show once until cookie expires</span></div>
		<div class="entry">Show popup only on pages containing URL path: <input name="displetpop_path" type="text" id="displetpop_path" size="5" value="<?php echo get_option('displetpop_path'); ?>"/> <span>Leave blank to apply to all pages & posts</span></div>
		<div class="entry">Advanced users: Set cookie to expire after <input name="displetpop_expiration" type="text" id="displetpop_expiration" size="1" value="<?php echo get_option('displetpop_expiration'); ?>"/> days <span>1 day minimum</span></div>
		<div class="entry">Test mode: <input type="checkbox" id="displetpop_testmode" name="displetpop_testmode" value="1" <?php checked( '1', get_option( 'displetpop_testmode' ) ); ?> /> <span>If checked, the popup will show on <b>every pageview</b> and <b>ignore cookies</b>. URL path settings will still apply.</span></div>
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
<fieldset>
	<legend>Styles:</legend>
	<table class="form-table">
		<div class="entry"><div class="fieldleft">Color Scheme</div>
		<select name="displetpop_style" id="displetpop_style">
			<?php $saved_value=get_option('displetpop_style'); $is_selected = ' selected="selected"'; ?>
			<option value="<?php $value='default'; echo $value; ?>"<?php if ($value==$saved_value) echo $is_selected; ?>>Default</option>
			<option value="<?php $value='niche'; echo $value; ?>"<?php if ($value==$saved_value) echo $is_selected; ?>>Niche</option>
			<option value="<?php $value='general'; echo $value; ?>"<?php if ($value==$saved_value) echo $is_selected; ?>>General</option>
			<option value="<?php $value='red'; echo $value; ?>"<?php if ($value==$saved_value) echo $is_selected; ?>>Red</option>
			<option value="<?php $value='blue'; echo $value; ?>"<?php if ($value==$saved_value) echo $is_selected; ?>>Blue</option>
			<option value="<?php $value='green'; echo $value; ?>"<?php if ($value==$saved_value) echo $is_selected; ?>>Green</option>
			<option value="<?php $value='custom'; echo $value; ?>"<?php if ($value==$saved_value) echo $is_selected; ?>>Custom</option>
	    </select></div>
		<div class="entry"><div class="fieldleft">Custom Stylesheet</div><textarea name="displetpop_customstyles" type="text" id="displetpop_customstyles"><?php echo get_option('displetpop_customstyles'); ?></textarea><div class="marleft"><span>Select &quot;Custom&quot; Color Scheme above to enable usage</span></div></div>
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

function displetpop_markup() { 
		$cur_style=get_option('displetpop_style');
		$imagesdir = plugins_url() . '/displet-pop/images';
		if ($cur_style==='custom') {echo '<style>' . get_option('displetpop_customstyles') . '</style>';}
		elseif ($cur_style==='niche') { $imagesdir .= '/niche'; ?>
			<style>
			#displetpop *{
				margin:0;
				padding:0;
				border:0;
			}
			#displetpop .popup{
				width: 488px;
				padding: 20px 20px 13px 20px;
				border: 1px solid #ada99c;
				background-color:#fff;
			}
			#displetpop .tit{
				display: inline-block;
				margin: 0px auto;
				height: 40px;
				padding-left: 16px;
				background: url('<?php echo $imagesdir; ?>/titleft.png') 0px 0px no-repeat;
				margin-bottom: 11px;
			}
			#displetpop .tit .inner{
				height: 40px;
				padding-right: 16px;
				background: url('<?php echo $imagesdir; ?>/titright.png') right 0px no-repeat;
			}
			#displetpop .tit .inner2{
				height: 40px;
				line-height: 40px;
				padding: 0px 8px;
				background: url('<?php echo $imagesdir; ?>/titback.png') 0px 0px repeat-x;
				font-size: 18px;
				color: #fff;
				text-transform: uppercase;
				font-family: 'Oswald', sans-serif;
				text-shadow: 1px -1px 2px #333;
			}
			#displetpop .subtit{
				font-size: 15px;
				line-height: 23px;
				color: #4f3e30;
				font-family: 'Georgia', serif;
				text-transform: uppercase;
				text-align: center;
				margin-bottom: 11px;
			}
			#displetpop .subtit div{
				font-weight: bold;
			}
			#displetpop .form{
				border: 1px solid #b5b0a3;
				background-color: #e9e2d2;
				padding: 14px 15px 5px 15px;
				margin-bottom: 7px;
				overflow:auto;
			}
			#displetpop .description{
				font-size: 11px;
				line-height: 11px;
				font-family: 'Arial', sans-serif;
				color: #4f3e30;
				margin-bottom: 15px;
			}
			#displetpop input, #displetpop select, #displetpop textarea{
				border: 1px solid #b5b0a3;
				background-color: #fff;
				font-size: 11px;
				font-weight: bold;
				font-family: 'Arial', sans-serif;
				color: #695e55;
				float: left;
				margin: 0 10px 10px 0;
			}
			#displetpop ::-webkit-input-placeholder {
			    color: #695e55;
			}
			#displetpop :-moz-placeholder {
			    color: #695e55;
			}
			#displetpop input{
				height: 28px;
				line-height: 28px;
				padding: 0px 6px;
				width: 174px;
			}
			#displetpop select{
				height: 30px;
				line-height: 26px;
				width: 173px;
				padding: 1px 1px 1px 6px;
			}
			#displetpop textarea{
				line-height: 18px;
				padding: 5px 6px;
			}
			#displetpop input[type="submit"]{
				border:0;
				background-color: #695240;
				padding: 0px 7px;
				height: 30px;
				line-height: 30px;
				text-align: center;
				font-size: 11px;
				color: #fff;
				text-transform: uppercase;
				font-family: 'Oswald', 'Tahoma', sans-serif;
				margin-right: 0px;
				width: inherit;
				cursor: pointer;
			}
			#displetpop .gform_wrapper ul li.gfield, #displetpop .gform_wrapper .gform_footer{
				clear: none;
			}
			#displetpop .gform_wrapper .gform_footer, #displetpop .gform_wrapper, #displetpop .form_list li, #displetpop .gform_wrapper li{
				margin:0 !important;
				padding:0 !important;
			}
			#displetpop .gform_wrapper li{
				float: left;
			}
			#displetpop .gform_wrapper{
				max-width:100%;
			}
			#displetpop .gform_wrapper .gfield_label{
				margin-right: 400px;
			}
			#displetpop .privacy{
				text-align: center;
				font-size: 11px;
				line-height: 18px;
				font-family: 'Arial', sans-serif;
				color: #695e55;
				margin-top: 12px;
			}
			#displetpop .privacy span{
				color: #ee2f2b;
				margin-right: 2px;
			}
			#displetpop a{
				color: #68462b;
				text-decoration: underline;
			}
			#displetpop a:hover{
				text-decoration: none;
			}
			#displetpop .close{
				position: absolute;
				right: 6px;
				bottom: 6px;
				font-size: 10px;
				line-height: 10px;
				font-family: 'Arial', sans-serif;
			}
			#displetpop .close a{
				color: #695e55;
				text-decoration: none;
				padding-left: 10px;
				background: url('<?php echo $imagesdir; ?>/close.png') 0px 3px no-repeat;
			}
			#displetpop .close a:hover{
				text-decoration: underline;
			}
			</style>
		<?php }
		elseif ($cur_style==='general') { $imagesdir .= '/general'; ?>
			<style>
			#displetpop *{
				margin:0;
				padding:0;
				border:0;
			}
			#displetpop .shadow{
				background: url('<?php echo $imagesdir; ?>/shadow.png') 0px 0px repeat;
			}
			#displetpop .popup{
				background: url('<?php echo $imagesdir; ?>/border.png') 0px 0px repeat;
				padding: 10px;
				width: 600px;
				-moz-border-radius: 24px; 
				-webkit-border-radius: 24px;
				border-radius: 24px;
			}
			#displetpop .popupinner{
				padding-bottom: 37px;
				background: url('<?php echo $imagesdir; ?>/back.png') 0px 0px repeat;
				-moz-border-radius: 16px;
				-webkit-border-radius: 16px;
				border-radius: 16px;
				overflow: hidden;
			}
			#displetpop .tit{
				padding: 34px 40px;
				line-height: 30px;
				font-size: 26px;
				color: #343434;
				font-family: 'Georgia', serif;
				text-shadow: 1px 1px 2px #fff;
			}
			#displetpop .subtit{
				margin: 0px 40px;
				font-size: 16px;
				line-height: 33px;
				color: #fff;
				font-weight: bold;
				font-family: 'Tahoma', sans-serif;
				text-shadow: 0px 1px 2px #2562b0;
				text-align: center;
				text-transform: uppercase;
				padding-left: 16px;
				background: url('<?php echo $imagesdir; ?>/subtitleft.png') 0px 0px no-repeat;
			}
			#displetpop .subtit .inner{
				padding-right: 16px;
				background: url('<?php echo $imagesdir; ?>/subtitright.png') right 0px no-repeat;
			}
			#displetpop .subtit .inner2{
				background: url('<?php echo $imagesdir; ?>/subtitback.png') 0px 0px repeat-x;
			}
			#displetpop .form{
				margin: 0px 63px;
				padding: 0px 10px 10px 20px;
				background-color: #fff;
				border: 1px solid #ccc;
				border-top:0;
			}
			#displetpop .description{
				padding: 22px 10px 18px 0px;
				font-size: 14px;
				line-height: 16px;
				font-family: 'Georgia', serif;
				color: #000;
				text-align: center;
			}
			#displetpop input, #displetpop select, #displetpop textarea{
				float: left;
				margin: 0 10px 10px 0;
				border: 1px solid #ccc;
				background-color: #fff;
				font-size: 11px;
				color: #666;
				font-family: 'Arial', sans-serif;
				font-weight: bold;
			}
			#displetpop ::-webkit-input-placeholder {
			    color: #666;
			}
			#displetpop :-moz-placeholder {
			    color: #666;
			}
			#displetpop input{
				height: 28px;
				line-height: 28px;
				padding: 0px 5px;
				width: 160px;
			}
			#displetpop select{
				height: 30px;
				line-height: 26px;
				padding: 1px 1px 1px 5px;
				width: 173px;
			}
			#displetpop textarea{
				padding: 5px;
				line-height: 18px;
			}
			#displetpop input[type="submit"]{
				cursor: pointer;
				border:0;
				width: 67px;
				height: 30px;
				line-height: 30px;
				background: url('<?php echo $imagesdir; ?>/submit.png') 0px 0px no-repeat;
				text-indent: -9999px;
			}
			#displetpop input[type="submit"]:hover{
				box-shadow: 0px 1px 3px #666;
			}
			#displetpop .gform_wrapper ul li.gfield, #displetpop .gform_wrapper .gform_footer{
				clear: none;
			}
			#displetpop .gform_wrapper .gform_footer, #displetpop .gform_wrapper, #displetpop .form_list li, #displetpop .gform_wrapper li{
				margin:0 !important;
				padding:0 !important;
			}
			#displetpop .gform_wrapper li{
				float: left;
			}
			#displetpop .gform_wrapper{
				max-width:100%;
			}
			#displetpop .gform_wrapper .gfield_label{
				margin-right: 400px;
			}
			#displetpop .privacy{
				margin-top: 17px;
				padding: 0px 40px;
				font-size: 11px;
				line-height: 14px;
				color: #d94747;
				font-family: 'Arial', sans-serif;
				text-align: center;
			}
			#displetpop .close{
				position: absolute;
				top: 10px;
				right: 30px;
				width: 17px;
				height: 40px;
				line-height: 40px;
				background: url('<?php echo $imagesdir; ?>/close.png') 0px 0px no-repeat;
				text-indent: -9999px;
			}
			#displetpop .close a{
				display: block;
			}
			#displetpop .powered{
				display: block;
				position: absolute;
				bottom: -31px;
				left: 0px;
				width: 600px;
				height: 13px;
				line-height: 12px;
				text-align: center;
				font-size: 10px;
				color: #787878;
				font-family: 'Arial', sans-serif;
				text-shadow: 1px 1px 1px #000;
			}
			#displetpop .displet{
				margin-left: 5px;
				display: inline-block;
				height: 13px;
				width: 60px;
				background: url('<?php echo $imagesdir; ?>/displet.png') 0px 0px no-repeat;
				text-indent: -9999px;
			}
			</style>
		<?php }
		elseif ($cur_style==='red') { $imagesdir .= '/red'; ?>
			<link href='http://fonts.googleapis.com/css?family=Arvo:700' rel='stylesheet' type='text/css'>
			<link href='http://fonts.googleapis.com/css?family=Exo:600,700' rel='stylesheet' type='text/css'>
			<style>
			#displetpop *{
				margin:0;
				padding:0;
				border:0;
			}
			#displetpop .shadow{
				background: url('<?php echo $imagesdir; ?>/shadow.png') 0px 0px repeat;
			}
			#displetpop .popup{
				width: 600px;
			}
			#displetpop .popupinner{
				padding-bottom: 27px;
				background: url('<?php echo $imagesdir; ?>/back.png') 0px 0px repeat;
				overflow: hidden;
			}
			#displetpop .tit{
				padding: 31px 30px;
				line-height: 40px;
				font-size: 30px;
				color: #141414;
				font-family: 'Arvo', serif;
				font-weight: bold;
				background: url('<?php echo $imagesdir; ?>/titback.png') 0px 0px repeat;
			}
			#displetpop .subtit{
				font-size: 16px;
				line-height: 20px;
				color: #fff;
				font-weight: bold;
				font-family: 'Exo', sans-serif;
				text-shadow: 0px 1px 2px #111;
				text-align: center;
				text-transform: uppercase;
				border: 1px solid #e82c35;
				border-bottom: 0;
				padding-left: 29px;
				background: url('<?php echo $imagesdir; ?>/subtitleft.png') 0px 0px repeat-y;
			}
			#displetpop .subtit .inner{
				padding-right: 29px;
				background: url('<?php echo $imagesdir; ?>/subtitright.png') right 0px repeat-y;
			}
			#displetpop .subtit .inner2{
				padding: 14px 0px;
				background-color: #db1f26;
			}
			#displetpop .form{
				padding: 0px 20px 0px 30px;
				color: #fff;
			}
			#displetpop .description{
				padding: 25px 10px 16px 0px;
				font-size: 15px;
				line-height: 18px;
				font-family: 'Exo', serif;
				color: #fff;
				font-weight: 600;
				text-shadow: 0px 1px 2px #333;
			}
			#displetpop input, #displetpop select, #displetpop textarea{
				float: left;
				margin: 0 10px 10px 0;
				border: 4px solid #4f4f4f;
				outline: 1px solid #9c9c9c;
				background-color: #fff;
				font-size: 13px;
				color: #1c1c1c;
				font-family: 'Arial', sans-serif;
			}
			#displetpop ::-webkit-input-placeholder {
			    color: #1c1c1c;
			}
			#displetpop :-moz-placeholder {
			    color: #1c1c1c;
			}
			#displetpop input{
				height: 30px;
				line-height: 30px;
				padding: 0px 5px;
				width: 195px;
			}
			#displetpop select{
				height: 40px;
				line-height: 26px;
				padding: 1px 1px 1px 5px;
				width: 215px;
			}
			#displetpop textarea{
				padding: 5px;
				line-height: 18px;
			}
			#displetpop input[type="submit"]{
				cursor: pointer;
				border:0;
				outline:0;
				width: 88px;
				height: 33px;
				line-height: 33px;
				background: url('<?php echo $imagesdir; ?>/submit.png') 0px 0px no-repeat;
				text-indent: -9999px;
				box-shadow: 0px 5px 10px #333;
			}
			#displetpop input[type="submit"]:hover{
				box-shadow: 0px 5px 10px #111;
			}
			#displetpop .gform_wrapper ul li.gfield, #displetpop .gform_wrapper .gform_footer{
				clear: none;
			}
			#displetpop .gform_wrapper .gform_footer, #displetpop .gform_wrapper, #displetpop .form_list li, #displetpop .gform_wrapper li{
				margin:0 !important;
				padding:0 !important;
			}
			#displetpop .gform_wrapper li{
				float: left;
			}
			#displetpop .gform_wrapper{
				max-width:100%;
			}
			#displetpop .gform_wrapper .gfield_label{
				margin-right: 430px;
			}
			#displetpop .privacy{
				margin-top: 7px;
				padding: 0px 30px;
				font-size: 11px;
				line-height: 14px;
				color: #fff;
				font-family: 'Arial', sans-serif;
				text-shadow: 0px 1px 2px #333;
			}
			#displetpop .close{
				position: absolute;
				top: 0px;
				right: 19px;
				width: 18px;
				height: 37px;
				line-height: 37px;
				background: url('<?php echo $imagesdir; ?>/close.png') 0px 0px no-repeat;
				text-indent: -9999px;
			}
			#displetpop .close a{
				display: block;
			}
			#displetpop .powered{
				display: block;
				position: absolute;
				bottom: -31px;
				left: 0px;
				width: 600px;
				height: 13px;
				line-height: 12px;
				text-align: center;
				font-size: 10px;
				color: #787878;
				font-family: 'Arial', sans-serif;
				text-shadow: 1px 1px 1px #000;
			}
			#displetpop .displet{
				margin-left: 5px;
				display: inline-block;
				height: 13px;
				width: 60px;
				background: url('<?php echo $imagesdir; ?>/displet.png') 0px 0px no-repeat;
				text-indent: -9999px;
			}
			</style>
		<?php }
		elseif ($cur_style==='blue') { $imagesdir .= '/blue'; ?>
			<link href='http://fonts.googleapis.com/css?family=Arvo:700' rel='stylesheet' type='text/css'>
			<link href='http://fonts.googleapis.com/css?family=Exo:600,700' rel='stylesheet' type='text/css'>
			<style>
			#displetpop *{
				margin:0;
				padding:0;
				border:0;
			}
			#displetpop .shadow{
				background: url('<?php echo $imagesdir; ?>/shadow.png') 0px 0px repeat;
			}
			#displetpop .popup{
				width: 600px;
			}
			#displetpop .popupinner{
				padding-bottom: 27px;
				background: url('<?php echo $imagesdir; ?>/back.png') 0px 0px repeat;
				overflow: hidden;
			}
			#displetpop .tit{
				padding: 31px 30px;
				line-height: 40px;
				font-size: 30px;
				color: #141414;
				font-family: 'Arvo', serif;
				font-weight: bold;
				background: url('<?php echo $imagesdir; ?>/titback.png') 0px 0px repeat;
			}
			#displetpop .subtit{
				font-size: 16px;
				line-height: 20px;
				color: #fff;
				font-weight: bold;
				font-family: 'Exo', sans-serif;
				text-shadow: 0px 1px 2px #111;
				text-align: center;
				text-transform: uppercase;
				border: 1px solid #28abe3;
				border-bottom: 0;
				padding-left: 29px;
				background: url('<?php echo $imagesdir; ?>/subtitleft.png') 0px 0px repeat-y;
			}
			#displetpop .subtit .inner{
				padding-right: 29px;
				background: url('<?php echo $imagesdir; ?>/subtitright.png') right 0px repeat-y;
			}
			#displetpop .subtit .inner2{
				padding: 14px 0px;
				background-color: #228fd1;
			}
			#displetpop .form{
				padding: 0px 20px 0px 30px;
				color: #fff;
			}
			#displetpop .description{
				padding: 25px 10px 16px 0px;
				font-size: 15px;
				line-height: 18px;
				font-family: 'Exo', serif;
				color: #fff;
				font-weight: 600;
				text-shadow: 0px 1px 2px #333;
			}
			#displetpop input, #displetpop select, #displetpop textarea{
				float: left;
				margin: 0 10px 10px 0;
				border: 4px solid #4f4f4f;
				outline: 1px solid #9c9c9c;
				background-color: #fff;
				font-size: 13px;
				color: #1c1c1c;
				font-family: 'Arial', sans-serif;
			}
			#displetpop ::-webkit-input-placeholder {
			    color: #1c1c1c;
			}
			#displetpop :-moz-placeholder {
			    color: #1c1c1c;
			}
			#displetpop input{
				height: 30px;
				line-height: 30px;
				padding: 0px 5px;
				width: 195px;
			}
			#displetpop select{
				height: 40px;
				line-height: 26px;
				padding: 1px 1px 1px 5px;
				width: 215px;
			}
			#displetpop textarea{
				padding: 5px;
				line-height: 18px;
			}
			#displetpop input[type="submit"]{
				cursor: pointer;
				border:0;
				outline:0;
				width: 88px;
				height: 33px;
				line-height: 33px;
				background: url('<?php echo $imagesdir; ?>/submit.png') 0px 0px no-repeat;
				text-indent: -9999px;
				box-shadow: 0px 5px 10px #333;
			}
			#displetpop input[type="submit"]:hover{
				box-shadow: 0px 5px 10px #111;
			}
			#displetpop .gform_wrapper ul li.gfield, #displetpop .gform_wrapper .gform_footer{
				clear: none;
			}
			#displetpop .gform_wrapper .gform_footer, #displetpop .gform_wrapper, #displetpop .form_list li, #displetpop .gform_wrapper li{
				margin:0 !important;
				padding:0 !important;
			}
			#displetpop .gform_wrapper li{
				float: left;
			}
			#displetpop .gform_wrapper{
				max-width:100%;
			}
			#displetpop .gform_wrapper .gfield_label{
				margin-right: 430px;
			}
			#displetpop .privacy{
				margin-top: 7px;
				padding: 0px 30px;
				font-size: 11px;
				line-height: 14px;
				color: #fff;
				font-family: 'Arial', sans-serif;
				text-shadow: 0px 1px 2px #333;
			}
			#displetpop .close{
				position: absolute;
				top: 0px;
				right: 19px;
				width: 18px;
				height: 37px;
				line-height: 37px;
				background: url('<?php echo $imagesdir; ?>/close.png') 0px 0px no-repeat;
				text-indent: -9999px;
			}
			#displetpop .close a{
				display: block;
			}
			#displetpop .powered{
				display: block;
				position: absolute;
				bottom: -31px;
				left: 0px;
				width: 600px;
				height: 13px;
				line-height: 12px;
				text-align: center;
				font-size: 10px;
				color: #787878;
				font-family: 'Arial', sans-serif;
				text-shadow: 1px 1px 1px #000;
			}
			#displetpop .displet{
				margin-left: 5px;
				display: inline-block;
				height: 13px;
				width: 60px;
				background: url('<?php echo $imagesdir; ?>/displet.png') 0px 0px no-repeat;
				text-indent: -9999px;
			}
			</style>
		<?php }
		elseif ($cur_style==='green') { $imagesdir .= '/green'; ?>
			<link href='http://fonts.googleapis.com/css?family=Arvo:700' rel='stylesheet' type='text/css'>
			<link href='http://fonts.googleapis.com/css?family=Exo:600,700' rel='stylesheet' type='text/css'>
			<style>
			#displetpop *{
				margin:0;
				padding:0;
				border:0;
			}
			#displetpop .shadow{
				background: url('<?php echo $imagesdir; ?>/shadow.png') 0px 0px repeat;
			}
			#displetpop .popup{
				width: 600px;
			}
			#displetpop .popupinner{
				padding-bottom: 27px;
				background: url('<?php echo $imagesdir; ?>/back.png') 0px 0px repeat;
				overflow: hidden;
			}
			#displetpop .tit{
				padding: 31px 30px;
				line-height: 40px;
				font-size: 30px;
				color: #141414;
				font-family: 'Arvo', serif;
				font-weight: bold;
				background: url('<?php echo $imagesdir; ?>/titback.png') 0px 0px repeat;
			}
			#displetpop .subtit{
				font-size: 16px;
				line-height: 20px;
				color: #fff;
				font-weight: bold;
				font-family: 'Exo', sans-serif;
				text-shadow: 0px 1px 2px #111;
				text-align: center;
				text-transform: uppercase;
				border: 1px solid #b4cd3f;
				border-bottom: 0;
				padding-left: 29px;
				background: url('<?php echo $imagesdir; ?>/subtitleft.png') 0px 0px repeat-y;
			}
			#displetpop .subtit .inner{
				padding-right: 29px;
				background: url('<?php echo $imagesdir; ?>/subtitright.png') right 0px repeat-y;
			}
			#displetpop .subtit .inner2{
				padding: 14px 0px;
				background-color: #95aa34;
			}
			#displetpop .form{
				padding: 0px 20px 0px 30px;
				color: #fff;
			}
			#displetpop .description{
				padding: 25px 10px 16px 0px;
				font-size: 15px;
				line-height: 18px;
				font-family: 'Exo', serif;
				color: #fff;
				font-weight: 600;
				text-shadow: 0px 1px 2px #333;
			}
			#displetpop input, #displetpop select, #displetpop textarea{
				float: left;
				margin: 0 10px 10px 0;
				border: 4px solid #4f4f4f;
				outline: 1px solid #9c9c9c;
				background-color: #fff;
				font-size: 13px;
				color: #1c1c1c;
				font-family: 'Arial', sans-serif;
			}
			#displetpop ::-webkit-input-placeholder {
			    color: #1c1c1c;
			}
			#displetpop :-moz-placeholder {
			    color: #1c1c1c;
			}
			#displetpop input{
				height: 30px;
				line-height: 30px;
				padding: 0px 5px;
				width: 195px;
			}
			#displetpop select{
				height: 40px;
				line-height: 26px;
				padding: 1px 1px 1px 5px;
				width: 215px;
			}
			#displetpop textarea{
				padding: 5px;
				line-height: 18px;
			}
			#displetpop input[type="submit"]{
				cursor: pointer;
				border:0;
				outline:0;
				width: 88px;
				height: 33px;
				line-height: 33px;
				background: url('<?php echo $imagesdir; ?>/submit.png') 0px 0px no-repeat;
				text-indent: -9999px;
				box-shadow: 0px 5px 10px #333;
			}
			#displetpop input[type="submit"]:hover{
				box-shadow: 0px 5px 10px #111;
			}
			#displetpop .gform_wrapper ul li.gfield, #displetpop .gform_wrapper .gform_footer{
				clear: none;
			}
			#displetpop .gform_wrapper .gform_footer, #displetpop .gform_wrapper, #displetpop .form_list li, #displetpop .gform_wrapper li{
				margin:0 !important;
				padding:0 !important;
			}
			#displetpop .gform_wrapper li{
				float: left;
			}
			#displetpop .gform_wrapper{
				max-width:100%;
			}
			#displetpop .gform_wrapper .gfield_label{
				margin-right: 430px;
			}
			#displetpop .privacy{
				margin-top: 7px;
				padding: 0px 30px;
				font-size: 11px;
				line-height: 14px;
				color: #fff;
				font-family: 'Arial', sans-serif;
				text-shadow: 0px 1px 2px #333;
			}
			#displetpop .close{
				position: absolute;
				top: 0px;
				right: 19px;
				width: 18px;
				height: 37px;
				line-height: 37px;
				background: url('<?php echo $imagesdir; ?>/close.png') 0px 0px no-repeat;
				text-indent: -9999px;
			}
			#displetpop .close a{
				display: block;
			}
			#displetpop .powered{
				display: block;
				position: absolute;
				bottom: -31px;
				left: 0px;
				width: 600px;
				height: 13px;
				line-height: 12px;
				text-align: center;
				font-size: 10px;
				color: #787878;
				font-family: 'Arial', sans-serif;
				text-shadow: 1px 1px 1px #000;
			}
			#displetpop .displet{
				margin-left: 5px;
				display: inline-block;
				height: 13px;
				width: 60px;
				background: url('<?php echo $imagesdir; ?>/displet.png') 0px 0px no-repeat;
				text-indent: -9999px;
			}
			</style>
		<?php }
		else { $imagesdir .= '/default'; ?>
			<style>
			#displetpop *{
				margin:0;
				padding:0;
				border:0;
			}
			#displetpop .shadow{
				background: url('<?php echo $imagesdir; ?>/shadow.png') 0px 0px repeat;
			}
			#displetpop .popup{
				background: url('<?php echo $imagesdir; ?>/border.png') 0px 0px repeat;
				padding: 10px;
				width: 600px;
				-moz-border-radius: 24px; 
				-webkit-border-radius: 24px;
				border-radius: 24px;
			}
			#displetpop .popupinner{
				padding-bottom: 39px;
				background-color: #fff;
				-moz-border-radius: 16px; 
				-webkit-border-radius: 16px;
				border-radius: 16px;
				overflow: hidden;
			}
			#displetpop .tit{
				margin-bottom: 35px;
				padding: 30px 0px;
				line-height: 40px;
				background: #e3e2e2 url('<?php echo $imagesdir; ?>/titback.png') 0px 0px repeat-x;
				border-bottom: 1px solid #cfcfcf;
				font-size: 24px;
				color: #333;
				font-family: 'Helvetica', sans-serif;
				text-shadow: 1px 1px 2px #fff;
			}
			#displetpop .subtit{
				margin-bottom: 10px;
				padding: 0px 40px;
				font-size: 18px;
				line-height: 22px;
				font-family: 'Arial', sans-serif;
				color: #b12d2d;
			}
			#displetpop .form{
				padding: 0px 30px 0px 40px;
			}
			#displetpop .description{
				margin-bottom: 25px;
				padding-right: 10px;
				font-size: 14px;
				line-height: 16px;
				font-family: 'Arial', sans-serif;
				color: #999;
				font-weight: bold;
				font-style: italic;
			}
			#displetpop input, #displetpop select, #displetpop textarea{
				float: left;
				margin: 0 10px 10px 0;
				border-top: 1px solid #b2b2b2;
				border-left: 1px solid #b2b2b2;
				background-color: #eee;
				font-size: 12px;
				color: #666;
				font-family: 'Arial', sans-serif;
				-moz-border-radius: 4px; 
				-webkit-border-radius: 4px;
				border-radius: 4px;
			}
			#displetpop ::-webkit-input-placeholder {
			    color: #695e55;
			}
			#displetpop :-moz-placeholder {
			    color: #695e55;
			}
			#displetpop input{
				height: 28px;
				line-height: 28px;
				padding: 0px 5px;
				width: 185px;
			}
			#displetpop select{
				height: 29px;
				line-height: 26px;
				padding: 1px 1px 1px 5px;
				width: 196px;
			}
			#displetpop textarea{
				padding: 5px;
				line-height: 18px;
			}
			#displetpop input[type="submit"]{
				cursor: pointer;
				border:0;
				width: 83px;
				height: 29px;
				line-height: 29px;
				background: url('<?php echo $imagesdir; ?>/submit.png') 0px 0px no-repeat;
				text-indent: -9999px;
				-moz-border-radius: 8px; 
				-webkit-border-radius: 8px;
				border-radius: 8px;
			}
			#displetpop input[type="submit"]:hover{
				box-shadow: 0px 1px 3px #666;
			}
			#displetpop .gform_wrapper ul li.gfield, #displetpop .gform_wrapper .gform_footer{
				clear: none;
			}
			#displetpop .gform_wrapper .gform_footer, #displetpop .gform_wrapper, #displetpop .form_list li, #displetpop .gform_wrapper li{
				margin:0 !important;
				padding:0 !important;
			}
			#displetpop .gform_wrapper li{
				float: left;
			}
			#displetpop .gform_wrapper{
				max-width:100%;
			}
			#displetpop .gform_wrapper .gfield_label{
				margin-right: 400px;
			}
			#displetpop .privacy{
				margin-top: 15px;
				padding: 0px 40px;
				font-size: 11px;
				line-height: 14px;
				color: #999;
				font-family: 'Arial', sans-serif;
			}
			#displetpop .privacy .inner{
				padding-top: 12px;
				border-top: 1px solid #eee;
			}
			#displetpop .close{
				position: absolute;
				top: 47px;
				right: 45px;
				width: 26px;
				height: 26px;
				line-height: 26px;
				background: url('<?php echo $imagesdir; ?>/close.png') 0px 0px no-repeat;
				text-indent: -9999px;
			}
			#displetpop .close a{
				display: block;
			}
			#displetpop .powered{
				display: block;
				position: absolute;
				bottom: -31px;
				left: 0px;
				width: 600px;
				height: 13px;
				line-height: 12px;
				text-align: center;
				font-size: 10px;
				color: #787878;
				font-family: 'Arial', sans-serif;
				text-shadow: 1px 1px 1px #000;
			}
			#displetpop .displet{
				margin-left: 5px;
				display: inline-block;
				height: 13px;
				width: 60px;
				background: url('<?php echo $imagesdir; ?>/displet.png') 0px 0px no-repeat;
				text-indent: -9999px;
			}
			</style>
	<?php } ?>
	<div id="displetpop" style="display:none;">
		<div class="shadow"></div>
		<table class="inner">
			<tr></tr>
			<tr>
				<td>
					<div class="popup">
						<div class="popupinner">
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
								<div class="inner">
									<div class="inner2">
										<?php echo get_option('displetpop_subtitle'); ?>
									</div><!--// .inner2 -->
								</div><!--// .inner -->
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
								<div class="inner">
									<?php echo get_option('displetpop_privacy'); ?>
								</div><!--// .inner -->
							</div><!--// .privacy -->
							<div class="close"><a href="javascript:void(0);" title="Close">[close]</a></div>
						</div><!--// .popupinner -->
						<div class="powered">
							Brought to you by <div class="displet">Displet</div>
						</div><!--// .powered -->
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
	var displetpoppath = '<?php echo get_option("displetpop_path"); ?>';
	var urlmatch = '';
	if (displetpoppath!=''){
		if(window.location.href.indexOf(displetpoppath) > -1) {
			urlmatch = 'yes';
		}
		else{
			urlmatch = 'no';
		}
	}
	if ((($.cookie('recentpop') != 'yes' && '<?php echo $_SESSION["views"]; ?>' >= '<?php echo get_option("displetpop_pageviews"); ?>') || '<?php echo get_option("displetpop_testmode"); ?>' == '1') && urlmatch != 'no'){
		window.setTimeout(displetPop, <?php echo 1000*get_option('displetpop_seconds'); ?>);	
	}
	
// Ends allowance of jQuery to $ shortcut
});
</script>

<?php

}

add_action('wp_head', 'displetpop_action');

?>
