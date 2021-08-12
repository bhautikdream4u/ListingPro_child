<?php
add_action( 'wp_enqueue_scripts', 'my_theme_enqueue_styles' );
function my_theme_enqueue_styles() 
{
	wp_enqueue_style( 'listingpr-parent-style', get_template_directory_uri() . '/style.css' );

	wp_enqueue_script( 'custom-script', get_stylesheet_directory_uri() . '/assets/js/custom.js', ['jquery'], '', true );
}

add_action( 'wp_loaded', 'meeting_handler' );
function meeting_handler() 
{
	//include_once ABSPATH.'wp-content/plugins/bigbluebutton/admin/helpers/class-bigbluebutton-admin-helper.php';

	if ( isset($_POST['_wpnonce']) && wp_verify_nonce( $_POST['_wpnonce'], 'connex_add_meeting' ) ) 
	{
		$meeting_title = sanitize_text_field( $_POST['meeting_title'] );
		$meeting_message = sanitize_text_field( $_POST['meeting_message'] );

		$meeting_id = isset( $_POST['meeting_id'] ) ? $_POST['meeting_id'] : '';

		if( !empty($meeting_id) )
		{
			$post_id = $meeting_id;

			wp_update_post([
				'ID' => $post_id,
				'post_title' => $meeting_title,
				'post_content' => $meeting_message,
			]);
		}
		else
		{
			$post_id = wp_insert_post([
				'post_title' => $meeting_title,
				'post_content' => $meeting_message,
				'post_status' => 'publish',
				'post_type' => 'bbb-room',
			]);	
		}

		$user_name = sanitize_text_field( $_POST['bbb-room-user-name'] );
		$user_email = sanitize_text_field( $_POST['bbb-room-user-email'] );
		$moderator_code = sanitize_text_field( $_POST['bbb-moderator-code'] );
		$viewer_code    = sanitize_text_field( $_POST['bbb-viewer-code'] );
		$recordable     = ( array_key_exists( 'bbb-room-recordable', $_POST ) && sanitize_text_field( $_POST['bbb-room-recordable'] ) == 'checked' );
		$wait_for_mod = ( isset( $_POST['bbb-room-wait-for-moderator'] ) && sanitize_text_field( $_POST['bbb-room-wait-for-moderator'] ) == 'checked' );

		// Ensure neither code is empty.
		if ( '' == $moderator_code ) {
			$moderator_code = Bigbluebutton_Admin_Helper::generate_random_code();
		}
		if ( '' == $viewer_code ) {
			$viewer_code = Bigbluebutton_Admin_Helper::generate_random_code();
		}

		// Ensure the moderator code is not the same as the viewer code.
		if ( $moderator_code === $viewer_code ) {
			$viewer_code = $moderator_code . Bigbluebutton_Admin_Helper::generate_random_code( 1 );
		}

		// Add room codes to postmeta data.
		update_post_meta( $post_id, 'bbb-room-user-name', $user_name );
		update_post_meta( $post_id, 'bbb-room-user-email', $user_email );
		update_post_meta( $post_id, 'bbb-room-moderator-code', $moderator_code );
		update_post_meta( $post_id, 'bbb-room-viewer-code', $viewer_code );

		if ( ! get_post_meta( $post_id, 'bbb-room-meeting-id', true ) ) {
			update_post_meta( $post_id, 'bbb-room-meeting-id', sha1( home_url() . Bigbluebutton_Admin_Helper::generate_random_code( 12 ) ) );
		}

		// Update room recordable value.
		update_post_meta( $post_id, 'bbb-room-recordable', ( $recordable ? 'true' : 'false' ) );
		update_post_meta( $post_id, 'bbb-room-wait-for-moderator', ( $wait_for_mod ? 'true' : 'false' ) );

		$subject = "Enquiry Details : Connex Networking Platform";

		$message = "Hello " . $user_name . ", <br/><br/><br/>";

		$message .= "Thank you for submitting enquiry. We have arrange meeting for more disscuss. We have added below meeting details :  <br/><br/>";

		$message .= sprintf(__('Useremail: %s'), $user_email) . "<br/>";
	    $message .= sprintf(__('Password: %s'), $viewer_code) . "<br/>";
	    $message .= "Meeting link: <a href='".get_permalink($post_id)."'>Join Meeting</a> <br/><br/><br/>";

	    $message .= "Kind regards, <br/>";
		$message .= "Connex Networking Platform <br/>";
		$message .= "info@connex.com <br/>";
		$message .= "<img src='".get_site_url()."/wp-content/uploads/2020/01/logo-dark.png' height='50' style='height:50px'>";

		$headers = [];
	    $headers[] = 'From: Connex Networking Platform <info@connex.com>';
	    $headers[] = 'Content-Type: text/html; charset=UTF-8';

	    $user = wp_mail($user_email, $subject, $message, $headers);
	}
}

add_action('wp_ajax_meeting_delete', 'meeting_delete');
add_action('wp_ajax_nopriv_meeting_delete', 'meeting_delete');
if( !function_exists( 'meeting_delete' ) )
{
	function meeting_delete()
	{
		check_ajax_referer( 'lp_ajax_nonce', 'lpNonce' );
	    // Nonce is checked, get the POST data and sign user on
	    if( !wp_verify_nonce(sanitize_text_field($_POST['lpNonce']), 'lp_ajax_nonce')) {
	        $res = json_encode(array('nonceerror'=>'yes'));
	        die($res);
	    }
		$return =   array();


		$user_id        =   get_current_user_id();
		$user_idd       =   sanitize_text_field($_POST['user_id']);

		$delType        =   sanitize_text_field($_POST['delType']);
		$targetID       =   sanitize_text_field($_POST['targetID']);
		$delIDS         =   sanitize_text_field($_POST['delIDS']);

		if( $user_id != $user_idd )
		{
			$return['status']   =   'error';

			$return['msg']      =   esc_html__('Invalid User Session', 'listingpro');

			die( json_encode( $return ) );
		}

		if( $delType == 'meet' )
		{
			wp_delete_post( $targetID, true );

			$return['status'] = 'success';
			$return['msg'] = esc_html__('Meeting deleted', 'listingpro');

			die(json_encode($return));
		}

		$return['status'] = 'fail';
		$return['msg'] = esc_html__('Bad Request', 'listingpro');
		die(json_encode($return));
	}
}

add_action( 'admin_init', 'bbb_room_cap' );
function bbb_room_cap()
{
	$administrator = get_role('administrator');
	$administrator->add_cap( 'join_as_viewer_bbb_room' );

	$subscriber = get_role('subscriber');
	$subscriber->add_cap( 'join_as_moderator_bbb_room' );
	$subscriber->add_cap( 'edit_bbb_rooms' );
	$subscriber->add_cap( 'edit_published_bbb_rooms' );
	$subscriber->add_cap( 'delete_bbb_rooms' );
	$subscriber->add_cap( 'delete_published_bbb_rooms' );
	$subscriber->add_cap( 'publish_bbb_rooms' );
	$subscriber->add_cap( 'manage_categories' );
	$subscriber->add_cap( 'edit_others_bbb_rooms' );
	$subscriber->add_cap( 'delete_others_bbb_rooms' );
	$subscriber->add_cap( 'create_recordable_bbb_room' );
	$subscriber->add_cap( 'manage_bbb_room_recordings' );
	$subscriber->add_cap( 'view_extended_bbb_room_recording_formats' );
}

function appoinmentmeeting() {
    ?>
	<script>
		jQuery(document).ready(function(){
			<?php if(isset($_GET['dashboard']) && $_GET['dashboard']=='manage-booking'){ ?>
			jQuery("ul.booking-action-content i.noticefi_er").each(function() {
				var cbid = jQuery(this).closest('ul');
				var cbibd = cbid.find('.radio-container-box').attr('id');
				jQuery(this).attr('data-id',cbibd);
			});
			
			var cbid = jQuery("ul.booking-action-content i.noticefi_er").attr('data-id');
				//alert(cbid);
				
				setTimeout(function(){
					
					
					jQuery.ajax({
						type : "post",
						dataType: 'html',
						url: '<?php echo admin_url('admin-ajax.php');?>',
						data: {
							'action': 'display_meetinglink',
							'cbid': cbid
						},
						success: function (res) {
							jQuery('#booking-details-sidebar .user-booking-detail').append(res);
							
						},
						error: function (err) {
							console.log(err);
						}
					});
				}, 1000);
			
			
			<?php } ?>
			
			jQuery('ul.booking-action-content a').click(function(){
				
				
				var dataid = jQuery(this).attr('data-id');
				var datastatus = jQuery(this).attr('data-status');
				
				
					jQuery.ajax({
						type : "post",
						dataType: 'html',
						url: '<?php echo admin_url('admin-ajax.php');?>',
						data: {
							'action': 'get_meetinglink',
							'dataid': dataid,
							'datastatus': datastatus,
						},
						success: function (res) {
							//alert(res);
							
						},
						error: function (err) {
							console.log(err);
						}
					});
				
			});
			
			<?php if(isset($_GET['dashboard']) && $_GET['dashboard']=='manage-booking'){ ?>
			
			jQuery(document).on('click', '.booking-content ul.booking-action-content i.noticefi_er', function () {
				
				var cbid = jQuery(this).attr('data-id');
				//alert(cbid);
				
				setTimeout(function(){
					
					
					jQuery.ajax({
						type : "post",
						dataType: 'html',
						url: '<?php echo admin_url('admin-ajax.php');?>',
						data: {
							'action': 'display_meetinglink',
							'cbid': cbid
						},
						success: function (res) {
							jQuery('#booking-details-sidebar .user-booking-detail').append(res);
							
						},
						error: function (err) {
							console.log(err);
						}
					});
				}, 1000);
			});
			
			<?php } ?>
			
		});
		
		
	</script>
	<?php
}
add_action( 'wp_footer', 'appoinmentmeeting' );

add_action( 'wp_ajax_get_meetinglink', 'get_meetinglink' );
add_action( 'wp_ajax_nopriv_get_meetinglink', 'get_meetinglink' );

function get_meetinglink(){
	
	global $wpdb;
	if($_POST['datastatus']=='APPROVED'){
		$post_id = $wpdb->get_results("SELECT id FROM wp_appoinmentlink WHERE (userid = ".get_current_user_id()." AND dataid = '". $_POST['dataid'] ."')");
		print_r($post_id);
		if(count($post_id)==0){
			$meeting_title = sanitize_text_field( get_post_meta( $_POST['dataid'], 'lp_booking_lName', true ) );
			$meeting_message = sanitize_text_field( '' );
			$meeting_id = '';

			$post_id = wp_insert_post([
					'post_title' => $meeting_title,
					'post_content' => $meeting_message,
					'post_status' => 'publish',
					'post_type' => 'bbb-room',
				]);	

			$user_name = sanitize_text_field( get_post_meta( $_POST['dataid'], 'lp_booking_emial', true ) );
			$user_email = sanitize_text_field( get_post_meta( $_POST['dataid'], 'lp_booking_emial', true ) );
			$moderator_code = sanitize_text_field( 'cb7b91149d' );
			$viewer_code    = sanitize_text_field( '94dda90282' );
			$recordable     = 'checked';
			$wait_for_mod = 'checked';

			// Ensure neither code is empty.
			if ( '' == $moderator_code ) {
				$moderator_code = Bigbluebutton_Admin_Helper::generate_random_code();
			}
			if ( '' == $viewer_code ) {
				$viewer_code = Bigbluebutton_Admin_Helper::generate_random_code();
			}

			// Ensure the moderator code is not the same as the viewer code.
			if ( $moderator_code === $viewer_code ) {
				$viewer_code = $moderator_code . Bigbluebutton_Admin_Helper::generate_random_code( 1 );
			}

			// Add room codes to postmeta data.
			update_post_meta( $post_id, 'bbb-room-user-name', $user_name );
			update_post_meta( $post_id, 'bbb-room-user-email', $user_email );
			update_post_meta( $post_id, 'bbb-room-moderator-code', $moderator_code );
			update_post_meta( $post_id, 'bbb-room-viewer-code', $viewer_code );

			if ( ! get_post_meta( $post_id, 'bbb-room-meeting-id', true ) ) {
				update_post_meta( $post_id, 'bbb-room-meeting-id', sha1( home_url() . Bigbluebutton_Admin_Helper::generate_random_code( 12 ) ) );
			}

			// Update room recordable value.
			update_post_meta( $post_id, 'bbb-room-recordable', ( $recordable ? 'true' : 'false' ) );
			update_post_meta( $post_id, 'bbb-room-wait-for-moderator', ( $wait_for_mod ? 'true' : 'false' ) );

			$subject = "Enquiry Details : Connex Networking Platform";

			$message = "Hello " . $user_name . ", <br/><br/><br/>";

			$message .= "Thank you for submitting enquiry. We have arrange meeting for more disscuss. We have added below meeting details :  <br/><br/>";

			$message .= sprintf(__('Useremail: %s'), $user_email) . "<br/>";
			$message .= sprintf(__('Password: %s'), $viewer_code) . "<br/>";
			$message .= "Meeting link: <a href='".get_permalink($post_id)."'>Join Meeting</a> <br/><br/><br/>";

			$message .= "Kind regards, <br/>";
			$message .= "Connex Networking Platform <br/>";
			$message .= "info@connex.com <br/>";
			$message .= "<img src='".get_site_url()."/wp-content/uploads/2020/01/logo-dark.png' height='50' style='height:50px'>";

			$headers = [];
			$headers[] = 'From: Connex Networking Platform <info@connex.com>';
			$headers[] = 'Content-Type: text/html; charset=UTF-8';

			$user = wp_mail($user_email, $subject, $message, $headers);
		
			$wpdb->query("INSERT INTO wp_appoinmentlink (userid,link,dataid) VALUES (".get_current_user_id().",'".get_permalink($post_id)."', ".$_POST['dataid'].")" );
			
			
		}
	}
	die();
}


add_action( 'wp_ajax_display_meetinglink', 'display_meetinglink' );
add_action( 'wp_ajax_nopriv_display_meetinglink', 'display_meetinglink' );

function display_meetinglink(){
	global $wpdb;
	//echo "SELECT link FROM wp_appoinmentlink WHERE (userid = ".get_current_user_id()." AND dataid = ". $booking_listing_id .");
	$post_link = $wpdb->get_results("SELECT * FROM wp_appoinmentlink WHERE dataid = ". $_POST['cbid']);
	//print_r($post_link[0]->link);
	
	echo '<br/><span class="user-booking-detail-name">Meeting Link</span><br>
                    <span class="user-booking-detail-name-detail"><a href="'.$post_link[0]->link.'" target="_blank">'.$post_link[0]->link.'</a></span>';
	die();
}