<?php
$current_user = wp_get_current_user();
$user_id = $current_user->ID;
$current_offset = get_option( 'gmt_offset' );
        $tzstring       = get_option( 'timezone_string' );
        $check_zone_info = true;
        if ( false !== strpos( $tzstring, 'Etc/GMT' ) ) {
            $tzstring = '';
        }
        if ( empty( $tzstring ) ) { // Create a UTC+- zone if no timezone string exists
            $check_zone_info = false;
            if ( 0 == $current_offset ) {
                $tzstring = 'UTC+0';
            } elseif ( $current_offset < 0 ) {
                $tzstring = 'UTC' . $current_offset;
            } else {
                $tzstring = 'UTC+' . $current_offset;
            }
        }
$b_args = array(
    'post_type' => 'listingpro-bookings',
    'post_status' => 'publish',
    'posts_per_page' => -1,
    'meta_query' => array(
        array(
            'key' => 'lp_listing_author',
            'value' => $user_id,
            'compare' => '='
        ),
    )
);
$lp_bookings        =   new WP_Query($b_args);
$lp_bookings_arr    =   array();
if($lp_bookings->have_posts()) : while ($lp_bookings->have_posts()) : $lp_bookings->the_post();
$booking_start_time =   get_post_meta(get_the_ID(), 'lp_booking_start_time', true);
$lp_bookings_arr[$booking_start_time]   =   get_the_ID();
endwhile; wp_reset_postdata(); endif;
ksort($lp_bookings_arr);


$first_booking_id   =   reset($lp_bookings_arr);

?>

<input type="hidden" id="lp_booking_get_time_zone_val" value="<?php echo $tzstring; ?>">
<div class="lp-dashboard-booking-calander-header clearfix">
    <p class="grid-btn back-to-all-bookings"><i class="fa fa-chevron-left"></i><?php echo esc_html__('All Appointments','listingpro-bookings'); ?></p>
    <br>
    <div class="pull-left lp-dashboard-booking-calander-header-title"><?php echo esc_html__('Calendar','listingpro-bookings'); ?></div>
    <div class="pull-right lp-dashboard-booking-calander-header-status"><div class="booking-status approved pull-left"></div><?php echo esc_html__('Calendar only displays the approved Appointments.','listingpro-bookings'); ?></div>
</div>
<div class="back-to-bookings">
    <a href="" data-toggle="tab" aria-expanded="true">
        <button class="btn bookings-back-btn">
            <span><?php echo esc_html__(' Back To Appointments','listingpro-bookings'); ?></span></button>
    </a>
</div>
<div id="lp-dashboard-booking-calander"></div>
<div class="clear"></div>

<?php if(count($lp_bookings_arr) <= 0){?>

<div class="lp-blank-section">
    <div class="col-md-12 blank-left-side">

<!--        <img src="--><?php //echo listingpro_icons_url('lp_blank_trophy'); ?><!--">-->
        <img src="<?php echo esc_url( plugins_url( 'listingpro-bookings/assets/images/dash-booking-icon.png') ) ?>">

        <h1><?php echo esc_html__('Start Accepting Appointments Now', 'listingpro-bookings'); ?></h1>

        <p class="margin-bottom-20"><?php echo esc_html__('Start accepting appointments from all your listings and manage it from a single dashboard. Provide the details below and you can make changes later in the settings.', 'listingpro-bookings'); ?></p>

        <button data-form="bookings" class="lp-add-new-btn add-new-open-form lp-add-new-listing-for-booking"><?php echo esc_html__('Start Now', 'listingpro-bookings'); ?></button>

    </div>
</div>
<div id="bookings-form-toggle" style="display: none;">
    <div class="panel with-nav-tabs panel-default lp-dashboard-tabs lp-left-panel-height lp-container-center">

            <div class="lp-coupns-form-outer">

                <h4><?php echo esc_html__('Setup Appointments','listingpro-bookings'); ?></h4>

                <form class="lp-coupons-form-inner" id="lp-events-form">

                    <div class="lp-coupon-box-row">
                        <div class="form-group">
                            <?php
                            $lp_booking_timeslot_duration   =   get_option('lp_booking_timeslot_duration');
                            if(empty($lp_booking_timeslot_duration)) {
                                $lp_booking_timeslot_duration   =   30;
                            }
                            ?>
                            <label class="lp-dashboard-top-label"><?php esc_html_e("Select the appointment duration for each slot",'listingpro-bookings'); ?> <span>*</span></label>
                            <div class="pos-relative">
                                <span class="lp-field-icon"><i class="fa fa-clock-o" aria-hidden="true"></i></span>
                                <select class="dash-booking-select-time-slot" id="dashboard-timeslot">
                                    <option value="15" <?php if($lp_booking_timeslot_duration == '15') {echo 'selected';}; ?>><?php echo esc_html__('15 Minutes','listingpro-bookings'); ?></option>
                                    <option value="30" <?php if($lp_booking_timeslot_duration == '30') {echo 'selected';}; ?>><?php echo esc_html__('30 Minutes','listingpro-bookings'); ?></option>
                                    <option value="45" <?php if($lp_booking_timeslot_duration == '45') {echo 'selected';}; ?>><?php echo esc_html__('45 Minutes','listingpro-bookings'); ?></option>
                                    <option value="60" <?php if($lp_booking_timeslot_duration == '60') {echo 'selected';}; ?>><?php echo esc_html__('60 Minutes','listingpro-bookings'); ?></option>
                                    <option value="90" <?php if($lp_booking_timeslot_duration == '90') {echo 'selected';}; ?>><?php echo esc_html__('90 Minutes','listingpro-bookings'); ?></option>
                                </select>
                               <span class="timeslot-spinner"><i class="fa fa-spinner fa-spin" aria-hidden="true"></i></span>
                            </div>
                        </div>
                    </div>
                    <div class="lp-coupon-box-row">

                        <div class="form-group">

                            <label class="lp-dashboard-top-label"><?php esc_html_e("Add all listings to accept appointments",'listingpro-bookings'); ?> <span>*</span></label>
                            <div class="user-booking-settings">
                                <ul class="user-booking-settings-time-slot">
                                    <?php
                                    $listings_for_bookings = get_user_meta(get_current_user_id(), 'listings_for_bookings', true);
                                    if(!empty($listings_for_bookings)) {
                                        foreach ($listings_for_bookings as $key => $val) {
                                            $listings_for_bookings_ID = $key;
                                            echo '<li>' . '<span data-listing-id='.$listings_for_bookings_ID.'>' . get_the_title($listings_for_bookings_ID) .  '</span>' . '<a class="booking-setting-icon-delete-listing dash-booking-remove-icon pull-right"><i class="fa fa-times"></i></a>' . '</li>';
                                        }
                                    }
                                    ?>
                                </ul>
                                <span class="user-booking-settings-add-new-listing clearfix">
                                <span><?php lp_get_listing_dropdown('setting-listing-dropdown', 'select2-ajax', 'listingsettings', 'bookings', null); ?></span>
                                <?php $count_listings = count_user_posts($userID, 'listing');
                                    if($count_listings  >  0){
                                ?>
                                <button class="add-listing-for-booking dash-add-booking-btn form-control "><i class="fa fa-plus"></i></button>
                                <?php } ?>
                                <h6 class="lp-booking-error">Appointments not allowed for this listing</h6>
                            </div>
                            <div class="clearfix"></div>
                        </div>

                    </div>

                </form>

            </div>

        </div>
    <div class="lp-coupon-box-row lp-save-btn-container">
        <div class="form-group">
            <div class="row">
                <div class="form-group col-sm-12 clearfix">
                    <a href="" class="lp-unsaved-btn"><?php echo esc_html__( 'Unsaved Event', 'listingpro-bookings' ); ?></a>
                    <button id="bookings-save-btn" data-uid="<?php echo $user_id; ?>" class="lp-bookings-btns bookings-save-btn pull-right "><?php echo esc_html__( 'save', 'listingpro-bookings' ); ?></button>
                    <button data-cancel="bookings" id="cancelLpBookings" class="lp-bookings-btns cancelLpBookings cancel-bookings-new-btn pull-right lp-margin-right-10 "><?php echo esc_html__( 'Cancel', 'listingpro-bookings' ); ?></button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="lp-bookings-after-save-screen" style="display: none;">
    <div class="col-md-12 blank-left-side">

        <img src="<?php echo esc_url( plugins_url( 'listingpro-bookings/assets/images/bookings-after-save-screen.png') ) ?>">

        <h1><?php echo esc_html__('You are all set to accept Appointments', 'listingpro-bookings'); ?></h1>

        <p class="margin-bottom-20"><?php echo esc_html__('Just sit back and relax while the appointments start rolling in.', 'listingpro-bookings'); ?></p>

    </div>
</div>

<?php }else{ ?>
<div class="booking-grid-wrapper">


    <div class="custom-col-width col-md-9 booking-left-section">
        <div class="clearfix lp-dashboard-panel-outer lp-new-dashboard-panel-outer margin-top-20" style="display: none;">
            <div class="notices-area">
                <div class="notice warning">
                    <a href="#" class="close"><i class="fa fa-times"></i></a>
                    <div class="notice-icon">
                        <i class="fa fa-info-circle"></i>
                    </div>
                    <div class="notice-text">
                        <h2>
                            <span><?php esc_html__('  Appointments are pending approval. Pending Appointments will only show in calendar upon approval.', 'listingpro-bookings'); ?></span>
                        </h2>
                    </div>
                </div>
            </div>
        </div>

        <div class="bookings">
            <div class="booking-heading">
                <h1><?php echo esc_html__('All Appointments','listingpro-bookings'); ?></h1>
            </div>
            <div class="panel with-nav-tabs panel-default lp-dashboard-tabs col-md-12 lp-left-panel-height lp-left-panel-height-outer padding-bottom0">
                <div class="lp-menu-step-one margin-top-20">
                    <div class="panel-heading">
                        <ul class="nav nav-tabs">
                            <li class="active">
                                <a href="#all-bookings" data-toggle="tab" aria-expanded="true"><?php echo esc_html__('All','listingpro-bookings'); ?></a>
                            </li>
                            <li class="">
                                <a href="#pending-bookings" data-toggle="tab" aria-expanded="false"><?php echo esc_html__('PENDING','listingpro-bookings'); ?></a>
                            </li>
                            <li class="">
                                <a href="#approved-bookings" data-toggle="tab" aria-expanded="false"><?php echo esc_html__('APPROVED','listingpro-bookings'); ?></a>
                            </li>
                            <li class="">
                                <a href="#canceled-bookings" data-toggle="tab" aria-expanded="false"><?php echo esc_html__('CANCELED','listingpro-bookings'); ?></a>
                            </li>
                            <li class="">
                                <a href="#expired-bookings" data-toggle="tab" aria-expanded="false"><?php echo esc_html__('Expired','listingpro-bookings'); ?></a>
                            </li>
                            <li class="pull-right">
                                <a href="" data-toggle="tab" aria-expanded="false">
                                    <button class="btn calendar-btn" data-last-day="<?php echo date('t F Y'); ?>" data-first-day="<?php echo '1 '.date('F Y'); ?>"><i class="fa fa-calendar" aria-hidden="true"></i>
                                        <span> <?php echo esc_html__('CALENDAR','listingpro-bookings'); ?></span></button>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="clearfix"></div>
                <ue= class="tab-content lp-tab-content-outer clearfix">
                <div class="tab-pane fade active in" id="all-bookings">
                    <div class="booking-content">
                        <ul class="booking-action-header">
                            <li class="cell"><input type="radio" style="visibility: hidden;" name=""></li>
                            <li class="cell"><?php echo esc_html__('CUSTOMER','listingpro-bookings'); ?></li>
                            <li class="cell"><?php echo esc_html__('LISTING','listingpro-bookings'); ?></li>
                            <li class="cell"><?php echo esc_html__('DATE','listingpro-bookings'); ?></li>
                            <li class="cell"><?php echo esc_html__('TIME SLOT','listingpro-bookings'); ?></li>
                            <li class="cell"><?php echo esc_html__('STATUS','listingpro-bookings'); ?></li>
                            <li class="cell"><?php echo esc_html__('ACTION','listingpro-bookings'); ?></li>
                            <?php
                            if(is_plugin_active('listingpro-bookings-payment/listingpro-bookings-payment.php')){
                                echo apply_filters('dash_booking_action_header_cell','PAYMENT STATUS');
                            }
                            ?>
                        </ul>
                        <?php

                        $booking_couner =   0;


                            if (is_array($lp_bookings_arr) && !empty($lp_bookings_arr) && count($lp_bookings_arr) > 0) {
                                foreach ($lp_bookings_arr as $k => $v) {
                                    $booking_couner++;

                                    $listing_id = get_post_meta($v, 'lp_listing_id', true);
                                    $booking_id = $v;

                                    $booking_status =   get_post_meta($booking_id, 'lp_booking_status', true);

                                    $booker_id      =   get_post_field('post_author', $booking_id);
                                    $booker_data    =   get_user_by('ID', $booker_id);

                                    $booking_date = date_i18n(get_option('date_format'), (int)get_post_meta($booking_id, 'lp_booking_date', true));
                                    $booking_start = date_i18n(get_option('time_format'), (int)get_post_meta($booking_id, 'lp_booking_start_time', true));
                                    $booking_end = date_i18n(get_option('time_format'), (int)get_post_meta($booking_id, 'lp_booking_end_time', true));

                                    /* *************   CHANGE BOOKING STATUS  AND COLOR ************ */

                                    if ($booking_status == "APPROVED") {
                                        $status_color = "approved";
                                    } else if ($booking_status == "CANCELED") {
                                        $status_color = "canceled";
                                    } else {
                                        $status_color = "pending";
                                    }
                                    ?>
                                    <ul class="booking-action-content">
                                        <li class="cell">
                                            <label id="<?php echo $booking_id; ?>" class="radio-container-box">
                                                <input type="radio" name="radio" >
                                                <span class="checkmark<?php if($booking_couner == 1){echo ' active';}; ?>"></span>
                                            </label>
                                        </li>
                                        <li class="cell"><?php echo $booker_data->user_login; ?> </li>
                                        <li class="cell"><a href="<?php echo get_permalink($listing_id); ?>" target="_blank" class="underline color-a"><?php echo get_the_title($listing_id); ?></a></li>
                                        <li class="cell"><?php echo $booking_date; ?></li>
                                        <li class="cell"><?php echo $booking_start . ' - ' . $booking_end ?></li>
                                        <li class="cell" align="center">
                                            <div class="booking-status <?php echo $status_color; ?>"></div>
                                        </li>
                                        <li class="cell">
                                            <div class="dropdown">
                                                <button class="btn dropdown-toggle" type="button"
                                                        data-toggle="dropdown"> <?php echo $booking_status; ?>
                                                    &nbsp;&nbsp;<i class="fa fa-angle-down"></i></button>
                                                <ul class="dropdown-menu">
                                                    <li><a data-status="PENDING" data-id="<?php echo $booking_id; ?>" href="#"><?php echo esc_html__('PENDING','listingpro-bookings'); ?></a>
                                                    </li>
                                                    <li><a data-status="APPROVED" data-id="<?php echo $booking_id; ?>" href="#"><?php echo esc_html__('APPROVED','listingpro-bookings'); ?></a>
                                                    </li>
                                                    <li><a data-status="CANCELED" data-id="<?php echo $booking_id; ?>" href="#"><?php echo esc_html__('CANCELED','listingpro-bookings'); ?></a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </li>
                                        <li class="cell"><i class="fa fa-angle-right noticefi_er"  data-id="<?php echo $booking_id; ?>" ></i></li>
                                        <?php
                                        if(is_plugin_active('listingpro-bookings-payment/listingpro-bookings-payment.php')){
                                            echo apply_filters('booking_action_content_cell','');
                                        }
                                        ?>
                                    </ul>
                                <?php }
                            } ?>
                    </div>
                </div>
                <div class="tab-pane fade" id="pending-bookings">
                    <div class="booking-content">
                        <?php
                        $pending_listing_Arr    =   0;
                        ?>
                        <ul class="booking-action-header">
                            <li class="cell"><input type="radio" style="visibility: hidden;" name=""></li>
                            <li class="cell"><?php echo esc_html__('CUSTOMER','listingpro-bookings'); ?></li>
                            <li class="cell"><?php echo esc_html__('LISTING','listingpro-bookings'); ?></li>
                            <li class="cell"><?php echo esc_html__('DATE','listingpro-bookings'); ?></li>
                            <li class="cell"><?php echo esc_html__('TIME SLOT','listingpro-bookings'); ?></li>
                            <li class="cell"><?php echo esc_html__('STATUS','listingpro-bookings'); ?></li>
                            <li class="cell"><?php echo esc_html__('ACTION','listingpro-bookings'); ?></li>
                            <?php
                            if(is_plugin_active('listingpro-bookings-payment/listingpro-bookings-payment.php')){
                                echo apply_filters('dash_booking_action_header_cell','PAYMENT STATUS');
                            }
                            ?>
                        </ul>
                        <?php


                        if (is_array($lp_bookings_arr) && count($lp_bookings_arr) > 0) {
                            $booking_couner =   0;
                            foreach ($lp_bookings_arr as $k => $v) {

                                $listing_id = get_post_meta($v, 'lp_listing_id', true);
                                $booking_id = $v;
                                $booking_status =   get_post_meta($booking_id, 'lp_booking_status', true);
                                if($booking_status == 'PENDING') {
                                    $pending_listing_Arr++;
                                    $booking_couner++;
                                    $booking_status =   get_post_meta($booking_id, 'lp_booking_status', true);

                                    $booker_id      =   get_post_field('post_author', $booking_id);
                                    $booker_data    =   get_user_by('ID', $booker_id);

                                    $booking_date = date_i18n(get_option('date_format'), (int)get_post_meta($booking_id, 'lp_booking_date', true));
                                    $booking_start = date_i18n(get_option('time_format'), (int)get_post_meta($booking_id, 'lp_booking_start_time', true));
                                    $booking_end = date_i18n(get_option('time_format'), (int)get_post_meta($booking_id, 'lp_booking_end_time', true));

                                    /* *************   CHANGE BOOKING STATUS  AND COLOR ************ */


                                    if ($booking_status == "APPROVED") {
                                        $status_color = "approved";
                                    } else if ($booking_status == "CANCELED") {
                                        $status_color = "canceled";
                                    } else {
                                        $status_color = "pending";
                                    }

                                    ?>
                                    <ul class="booking-action-content">
                                        <li class="cell">
                                            <label id="<?php echo $booking_id; ?>" class="radio-container-box">
                                                <input type="radio" name="radio" >
                                                <span class="checkmark"></span>
                                            </label>
                                        </li>
                                        <li class="cell"><?php echo $booker_data->user_login; ?> </li>
                                        <li class="cell"><a href="<?php echo get_permalink($listing_id); ?>" class="underline color-a"><?php echo get_the_title($listing_id); ?></a></li>
                                        <li class="cell"><?php echo $booking_date; ?></li>
                                        <li class="cell"><?php echo $booking_start . ' - ' . $booking_end ?></li>
                                        <li class="cell" align="center">
                                            <div class="booking-status <?php echo $status_color; ?>"></div>
                                        </li>
                                        <li class="cell">
                                            <div class="dropdown">
                                                <button class="btn dropdown-toggle" type="button"
                                                        data-toggle="dropdown"> <?php echo $booking_status; ?>
                                                    &nbsp;&nbsp;<i class="fa fa-angle-down"></i></button>
                                                <ul class="dropdown-menu">
                                                    <li><a data-status="PENDING" data-id="<?php echo $booking_id; ?>" href="#"><?php echo esc_html__('PENDING','listingpro-bookings'); ?></a>
                                                    </li>
                                                    <li><a data-status="APPROVED" data-id="<?php echo $booking_id; ?>" href="#"><?php echo esc_html__('APPROVED','listingpro-bookings'); ?></a>
                                                    </li>
                                                    <li><a data-status="CANCELED" data-id="<?php echo $booking_id; ?>" href="#"><?php echo esc_html__('CANCELED','listingpro-bookings'); ?></a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </li>
                                        <li class="cell"><i class="fa fa-angle-right noticefi_er"  data-id="<?php echo $booking_id; ?>" ></i></li>
                                        <?php
                                        if(is_plugin_active('listingpro-bookings-payment/listingpro-bookings-payment.php')){
                                            echo apply_filters('booking_action_content_cell','');
                                        }
                                        ?>
                                    </ul>
                                    <?php
                                }
                            }
                        }
                        ?>

                    </div>
                </div>
                <div class="tab-pane fade" id="approved-bookings">
                    <div class="booking-content">
                        <ul class="booking-action-header">
                            <li class="cell"><input type="radio" style="visibility: hidden;" name=""></li>
                            <li class="cell"><?php echo esc_html__('CUSTOMER','listingpro-bookings'); ?></li>
                            <li class="cell"><?php echo esc_html__('LISTING','listingpro-bookings'); ?></li>
                            <li class="cell"><?php echo esc_html__('DATE','listingpro-bookings'); ?></li>
                            <li class="cell"><?php echo esc_html__('TIME SLOT','listingpro-bookings'); ?></li>
                            <li class="cell"><?php echo esc_html__('STATUS','listingpro-bookings'); ?></li>
                            <li class="cell"><?php echo esc_html__('ACTION','listingpro-bookings'); ?></li>
                            <?php
                            if(is_plugin_active('listingpro-bookings-payment/listingpro-bookings-payment.php')){
                                echo apply_filters('dash_booking_action_header_cell','PAYMENT STATUS');
                            }
                            ?>
                        </ul>


                        <?php
                        if (is_array($lp_bookings_arr) && count($lp_bookings_arr) > 0) {
                            $booking_couner =   0;
                            foreach ($lp_bookings_arr as $k => $v) {

                                $listing_id = get_post_meta($v, 'lp_listing_id', true);
                                $booking_id = $v;

                                $booking_status =   get_post_meta($booking_id, 'lp_booking_status', true);

                                if($booking_status == 'APPROVED') {
                                    $booking_couner++;
                                    $booking_status =   get_post_meta($booking_id, 'lp_booking_status', true);

                                    $booker_id      =   get_post_field('post_author', $booking_id);
                                    $booker_data    =   get_user_by('ID', $booker_id);

                                    $booking_date = date_i18n(get_option('date_format'), (int)get_post_meta($booking_id, 'lp_booking_date', true));
                                    $booking_start = date_i18n(get_option('time_format'), (int)get_post_meta($booking_id, 'lp_booking_start_time', true));
                                    $booking_end = date_i18n(get_option('time_format'), (int)get_post_meta($booking_id, 'lp_booking_end_time', true));

                                    /* *************   CHANGE BOOKING STATUS  AND COLOR ************ */


                                    if ($booking_status == "APPROVED") {
                                        $status_color = "approved";
                                    } else if ($booking_status == "CANCELED") {
                                        $status_color = "canceled";
                                    } else {
                                        $status_color = "pending";
                                    }

                                    ?>
                                    <ul class="booking-action-content">
                                        <li class="cell">
                                            <label id="<?php echo $booking_id; ?>" class="radio-container-box">
                                                <input type="radio" name="radio" >
                                                <span class="checkmark"></span>
                                            </label>
                                        </li>
                                        <li class="cell"><?php echo $booker_data->user_login; ?> </li>
                                        <li class="cell"><a href="<?php echo get_permalink($listing_id); ?>" class="underline color-a"><?php echo get_the_title($listing_id); ?></a></li>
                                        <li class="cell"><?php echo $booking_date; ?></li>
                                        <li class="cell"><?php echo $booking_start . ' - ' . $booking_end ?></li>
                                        <li class="cell" align="center">
                                            <div class="booking-status <?php echo $status_color; ?>"></div>
                                        </li>
                                        <li class="cell">
                                            <div class="dropdown">
                                                <button class="btn dropdown-toggle" type="button"
                                                        data-toggle="dropdown"> <?php echo $booking_status; ?>
                                                    &nbsp;&nbsp;<i class="fa fa-angle-down"></i></button>
                                                <ul class="dropdown-menu">
                                                    <li><a data-status="PENDING" data-id="<?php echo $booking_id; ?>" href="#"><?php echo esc_html__('PENDING','listingpro-bookings'); ?></a>
                                                    </li>
                                                    <li><a data-status="APPROVED" data-id="<?php echo $booking_id; ?>" href="#"><?php echo esc_html__('APPROVED','listingpro-bookings'); ?></a>
                                                    </li>
                                                    <li><a data-status="CANCELED" data-id="<?php echo $booking_id; ?>" href="#"><?php echo esc_html__('CANCELED','listingpro-bookings'); ?></a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </li>
                                        <li class="cell"><i class="fa fa-angle-right noticefi_er"  data-id="<?php echo $booking_id; ?>" ></i></li>
                                        <?php
                                        if(is_plugin_active('listingpro-bookings-payment/listingpro-bookings-payment.php')){
                                            echo apply_filters('booking_action_content_cell','');
                                        }
                                        ?>
                                    </ul>
                                    <?php
                                }
                            }
                        }
                        ?>

                    </div>
                </div>
                <div class="tab-pane fade" id="canceled-bookings">
                    <div class="booking-content">
                        <ul class="booking-action-header">
                            <li class="cell"><input type="radio" style="visibility: hidden;" name=""></li>
                            <li class="cell"><?php echo esc_html__('CUSTOMER','listingpro-bookings'); ?></li>
                            <li class="cell"><?php echo esc_html__('LISTING','listingpro-bookings'); ?></li>
                            <li class="cell"><?php echo esc_html__('DATE','listingpro-bookings'); ?></li>
                            <li class="cell"><?php echo esc_html__('TIME SLOT','listingpro-bookings'); ?></li>
                            <li class="cell"><?php echo esc_html__('STATUS','listingpro-bookings'); ?></li>
                            <li class="cell"><?php echo esc_html__('ACTION','listingpro-bookings'); ?></li>
                            <?php
                            if(is_plugin_active('listingpro-bookings-payment/listingpro-bookings-payment.php')){
                                echo apply_filters('dash_booking_action_header_cell','PAYMENT STATUS');
                            }
                            ?>
                        </ul>
                        <?php
                        if (is_array($lp_bookings_arr) && count($lp_bookings_arr) > 0) {
                            $booking_couner =   0;
                            foreach ($lp_bookings_arr as $k => $v) {

                                $listing_id = get_post_meta($v, 'lp_listing_id', true);
                                $booking_id = $v;

                                $booking_status =   get_post_meta($booking_id, 'lp_booking_status', true);

                                if($booking_status == 'CANCELED') {
                                    $booking_couner++;
                                    $booking_status =   get_post_meta($booking_id, 'lp_booking_status', true);

                                    $booker_id      =   get_post_field('post_author', $booking_id);
                                    $booker_data    =   get_user_by('ID', $booker_id);

                                    $booking_date = date_i18n(get_option('date_format'), (int)get_post_meta($booking_id, 'lp_booking_date', true));
                                    $booking_start = date_i18n(get_option('time_format'), (int)get_post_meta($booking_id, 'lp_booking_start_time', true));
                                    $booking_end = date_i18n(get_option('time_format'), (int)get_post_meta($booking_id, 'lp_booking_end_time', true));

                                    /* *************   CHANGE BOOKING STATUS  AND COLOR ************ */


                                    if ($booking_status == "APPROVED") {
                                        $status_color = "approved";
                                    } else if ($booking_status == "CANCELED") {
                                        $status_color = "canceled";
                                    } else {
                                        $status_color = "pending";
                                    }

                                    ?>
                                    <ul class="booking-action-content">
                                        <li class="cell">
                                            <label id="<?php echo $booking_id; ?>" class="radio-container-box">
                                                <input type="radio" name="radio" >
                                                <span class="checkmark"></span>
                                            </label>
                                        </li>
                                        <li class="cell"><a href="<?php echo get_permalink($listing_id); ?>" class="underline color-a"><?php echo get_the_title($listing_id); ?></a></li>
                                        <li class="cell"><?php echo $booker_data->user_login; ?> </li>
                                        <li class="cell"><?php echo $booking_date; ?></li>
                                        <li class="cell"><?php echo $booking_start . ' - ' . $booking_end ?></li>
                                        <li class="cell" align="center">
                                            <div class="booking-status <?php echo $status_color; ?>"></div>
                                        </li>
                                        <li class="cell">
                                            <div class="dropdown">
                                                <button class="btn dropdown-toggle" type="button"
                                                        data-toggle="dropdown"> <?php echo $booking_status; ?>
                                                    &nbsp;&nbsp;<i class="fa fa-angle-down"></i></button>
                                                <ul class="dropdown-menu">
                                                    <li><a data-status="PENDING" data-id="<?php echo $booking_id; ?>" href="#"><?php echo esc_html__('PENDING','listingpro-bookings'); ?></a>
                                                    </li>
                                                    <li><a data-status="APPROVED" data-id="<?php echo $booking_id; ?>" href="#"><?php echo esc_html__('APPROVED','listingpro-bookings'); ?></a>
                                                    </li>
                                                    <li><a data-status="CANCELED" data-id="<?php echo $booking_id; ?>" href="#"><?php echo esc_html__('CANCELED','listingpro-bookings'); ?></a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </li>
                                        <li class="cell"><i class="fa fa-angle-right noticefi_er"  data-id="<?php echo $booking_id; ?>" ></i></li>
                                        <?php
                                        if(is_plugin_active('listingpro-bookings-payment/listingpro-bookings-payment.php')){
                                            echo apply_filters('booking_action_content_cell','');
                                        }
                                        ?>
                                    </ul>
                                    <?php
                                }
                            }
                        }
                        ?>

                    </div>
                </div>
                <div class="tab-pane fade" id="expired-bookings">
                    <div class="booking-content">
                        <ul class="booking-action-header">
                            <li class="cell"><input type="radio" style="visibility: hidden;" name=""></li>
                            <li class="cell"><?php echo esc_html__('CUSTOMER','listingpro-bookings'); ?></li>
                            <li class="cell"><?php echo esc_html__('LISTING','listingpro-bookings'); ?></li>
                            <li class="cell"><?php echo esc_html__('DATE','listingpro-bookings'); ?></li>
                            <li class="cell"><?php echo esc_html__('TIME SLOT','listingpro-bookings'); ?></li>
                            <li class="cell"><?php echo esc_html__('STATUS','listingpro-bookings'); ?></li>
                            <li class="cell"><?php echo esc_html__('ACTION','listingpro-bookings'); ?></li>
                            <?php
                            if(is_plugin_active('listingpro-bookings-payment/listingpro-bookings-payment.php')){
                                echo apply_filters('dash_booking_action_header_cell','PAYMENT STATUS');
                            }
                            ?>
                        </ul>


                        <?php
                        if (is_array($lp_bookings_arr) && count($lp_bookings_arr) > 0) {
                            $booking_couner =   0;
                            foreach ($lp_bookings_arr as $k => $v) {

                                $listing_id = get_post_meta($v, 'lp_listing_id', true);
                                $booking_id = $v;

                                $booking_status =   get_post_meta($booking_id, 'lp_booking_status', true);

                                if($booking_status == 'APPROVED') {
                                    $booking_couner++;
                                    $booking_status =   get_post_meta($booking_id, 'lp_booking_status', true);

                                    $booker_id      =   get_post_field('post_author', $booking_id);
                                    $booker_data    =   get_user_by('ID', $booker_id);

                                    $booking_date = date_i18n(get_option('date_format'), (int)get_post_meta($booking_id, 'lp_booking_date', true));
                                    $booking_start = date_i18n(get_option('time_format'), (int)get_post_meta($booking_id, 'lp_booking_start_time', true));
                                    $booking_end = date_i18n(get_option('time_format'), (int)get_post_meta($booking_id, 'lp_booking_end_time', true));

                                    /* *************   CHANGE BOOKING STATUS  AND COLOR ************ */


                                    if ($booking_status == "APPROVED") {
                                        $status_color = "approved";
                                    } else if ($booking_status == "CANCELED") {
                                        $status_color = "canceled";
                                    } else {
                                        $status_color = "pending";
                                    }

                                    $timezone       =   get_option('gmt_offset');
                                    $time_now       =   gmdate("H:i", time() + 3600*($timezone+date("I")));
                                    $timeZone_str   =   strtotime($time_now);

                                    if($timeZone_str >= get_post_meta($booking_id, 'lp_booking_end_time', true)){
                                        ?>
                                        <ul class="booking-action-content">
                                            <li class="cell">
                                                <label id="<?php echo $booking_id; ?>" class="radio-container-box">
                                                    <input type="radio" name="radio" >
                                                    <span class="checkmark"></span>
                                                </label>
                                            </li>
                                            <li class="cell"><?php echo $booker_data->user_login; ?> </li>
                                            <li class="cell"><a href="<?php echo get_permalink($listing_id); ?>" class="underline color-a"><?php echo get_the_title($listing_id); ?></a></li>
                                            <li class="cell"><?php echo $booking_date; ?></li>
                                            <li class="cell"><?php echo $booking_start . ' - ' . $booking_end ?></li>
                                            <li class="cell" align="center">
                                                <div class="booking-status <?php echo $status_color; ?>"></div>
                                            </li>
                                            <li class="cell">
                                                <div class="dropdown">
                                                    <button class="btn dropdown-toggle" type="button"
                                                            data-toggle="dropdown"> <?php echo $booking_status; ?>
                                                        &nbsp;&nbsp;<i class="fa fa-angle-down"></i></button>
                                                    <ul class="dropdown-menu">
                                                        <li><a data-status="PENDING" data-id="<?php echo $booking_id; ?>" href="#"><?php echo esc_html__('PENDING','listingpro-bookings'); ?></a>
                                                        </li>
                                                        <li><a data-status="APPROVED" data-id="<?php echo $booking_id; ?>" href="#"><?php echo esc_html__('APPROVED','listingpro-bookings'); ?></a>
                                                        </li>
                                                        <li><a data-status="CANCELED" data-id="<?php echo $booking_id; ?>" href="#"><?php echo esc_html__('CANCELED','listingpro-bookings'); ?></a>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </li>
                                            <li class="cell"><i class="fa fa-angle-right noticefi_er " data-id="<?php echo $booking_id; ?>"></i></li>
                                            <?php
                                            if(is_plugin_active('listingpro-bookings-payment/listingpro-bookings-payment.php')){
                                                echo apply_filters('booking_action_content_cell','');
                                            }
                                            ?>
                                        </ul>
                                        <?php
                                    }
                                }
                            }
                        }
                        ?>

                    </div>
                </div>
                <input class="pending_listing_Arr" type="hidden" value="<?php echo $pending_listing_Arr .' '. esc_html__('Appointments are pending approval. Pending appointments will only show in calendar upon approval.','listingpro-bookings'); ?>">
            </div>
        </div>
    </div>



    <div class="custom-col-width col-md-3 tab-content lp-tab-content-outer">
        <div class="lp-dashboard-tabs">
			<p id="reply-title" class="clarfix lp-general-section-title comment-reply-title active"> <?php echo esc_html__('Details','listingpro-bookings'); ?> <i class="fa fa-angle-down" aria-hidden="true"></i></p>
            <div class="lp-menu-step-one">
                <div class="panel-heading">
                    <ul class="nav nav-tabs">
                        <li class="active">
                            <a href="#booking-details-sidebar" data-toggle="tab" aria-expanded="true"><?php echo esc_html__('Details','listingpro-bookings'); ?></a>
                        </li>
                        <li class="">
                            <a href="#booking-settings-sidebar" data-toggle="tab" aria-expanded="false"><?php echo esc_html__('Settings','listingpro-bookings'); ?></a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="bookings-sidebar tab-pane fade active in w" id="booking-details-sidebar">
            <?php
            if(!empty($first_booking_id)) {
                $booker_id      =   get_post_field('post_author', $first_booking_id);
                $booker_data    =   get_user_by('ID', $booker_id);

                $author_avatar_url = get_user_meta($booker_id, "listingpro_author_img_url", true);
                if(!empty($author_avatar_url)) {
                    $avatar =  $author_avatar_url;
                } else {
                    $avatar_url = listingpro_get_avatar_url ( $booker_id, $size = '94' );
                    $avatar =  $avatar_url;
                }
                ?>
                <div class="user-detail">
                    <div class="user-sidebar-avatar">
                        <img src="<?php echo $avatar; ?>">
                    </div>
                    <p class="user-name"><?php echo $booker_data->user_login; ?></p>
                    <p class="user-status"><?php echo esc_html__('Registered User','listingpro-bookings'); ?></p>
                </div>
                <h4 class="booking-action-header"><?php echo esc_html__('Appointment Detail','listingpro-bookings');  ?> </h4>
                <div class="user-booking-detail">
                    <?php

                    $c_booking_id = $first_booking_id;
                    $cb_date_str = get_post_meta($c_booking_id, 'lp_booking_date', true);

                    $cb_date = date_i18n(get_option('date_format'), $cb_date_str);

                    $cb_day = date_i18n('l', $cb_date_str);
                    $cb_start_time_str = get_post_meta($c_booking_id, 'lp_booking_start_time', true);
                    $cb_start_time = date_i18n(get_option('time_format'), $cb_start_time_str);
                    $cb_end_time_str = get_post_meta($c_booking_id, 'lp_booking_end_time', true);
                    $cb_end_time = date_i18n(get_option('time_format'), $cb_end_time_str);
                    $cb_name = get_post_meta($c_booking_id, 'lp_booking_name', true);
                    $cb_lName = get_post_meta($c_booking_id, 'lp_booking_lName', true);
                    $cb_email = get_post_meta($c_booking_id, 'lp_booking_emial', true);
                    $cb_phone = get_post_meta($c_booking_id, 'lp_booking_phone', true);
                    $cb_msg = get_post_field('post_content', $c_booking_id);
                    $booking_listing_id   =   get_post_meta($c_booking_id, 'lp_listing_id', true);

                    $gAddress   =   get_post_meta($booking_listing_id, 'lp_listingpro_options', true);
                    $gAddress   =   $gAddress['gAddress'];
					
                    ?>
                    <span class="user-booking-detail-name"><?php echo esc_html__('Full Name','listingpro-bookings');  ?> </span>
                    <span class="user-booking-detail-name-detail pull-right"><?php echo $cb_name, ' ', $cb_lName ?></span>
                    <br>
                    <span class="user-booking-detail-name"><?php echo esc_html__('Date','listingpro-bookings');  ?> </span>
                    <span class="user-booking-detail-name-detail pull-right"><?php echo $cb_day, ' , ', $cb_date ?></span>
                    <br>
                    <span class="user-booking-detail-name"><?php echo esc_html__('Time','listingpro-bookings');  ?></span>
                    <span class="user-booking-detail-name-detail pull-right"><?php echo $cb_start_time ?> - <?php echo $cb_end_time ?> <?php echo $tzstring; ?></span>
                    <br>
                    <span class="user-booking-detail-name"><?php echo esc_html__('Email','listingpro-bookings');  ?></span>
                    <span class="user-booking-detail-name-detail pull-right"><?php echo $cb_email ?></span><br>
                    <span class="user-booking-detail-name"><?php echo esc_html__('Phone','listingpro-bookings');  ?></span>
                    <span class="user-booking-detail-name-detail pull-right"><?php echo $cb_phone ?></span><br>
                    <span class="user-booking-detail-name"><?php echo esc_html__('Address','listingpro-bookings');  ?></span>
                    <span class="user-booking-detail-name-detail pull-right underline"><?php echo $gAddress; ?></span><br>
                    <span class="user-booking-detail-name"><?php echo esc_html__('Message','listingpro-bookings');  ?></span><br>
                    <span class="user-booking-detail-name-detail"><?php echo $cb_msg ?></span>
					<br/>
					
                </div>
            <?php
            } else {
                ?>
                <p><strong><?php echo esc_html__('No Appointment Selected', 'listingpro-bookings'); ?></strong></p>
                <?php
            }
            ?>

        </div>
        <div class="clearfix"></div>
        <div id="booking-settings-sidebar" class="tab-pane fade in pos-relative">
            <?php
            $lp_booking_timeslot_duration   =   get_option('lp_booking_timeslot_duration');
            if(empty($lp_booking_timeslot_duration)) {
                $lp_booking_timeslot_duration   =   30;
            }
            ?>
            <div class="user-booking-settings">
                <h4 class="booking-action-header">Appointment Slot Duration </h4>
                <select class="form-control" id="dashboard-timeslot">
                    <option value="15" <?php if($lp_booking_timeslot_duration == '15') {echo 'selected';}; ?>><?php echo esc_html__('15 Minutes','listingpro-bookings'); ?> </option>
                    <option value="30" <?php if($lp_booking_timeslot_duration == '30') {echo 'selected';}; ?>><?php echo esc_html__('30 Minutes','listingpro-bookings'); ?> </option>
                    <option value="45" <?php if($lp_booking_timeslot_duration == '45') {echo 'selected';}; ?>><?php echo esc_html__('45 Minutes','listingpro-bookings'); ?> </option>
                    <option value="60" <?php if($lp_booking_timeslot_duration == '60') {echo 'selected';}; ?>><?php echo esc_html__('60 Minutes','listingpro-bookings'); ?> </option>
                    <option value="90" <?php if($lp_booking_timeslot_duration == '90') {echo 'selected';}; ?>><?php echo esc_html__('90 Minutes','listingpro-bookings'); ?> </option>
                </select>


                <h4 class="booking-action-header"><?php echo esc_html__('LISTINGS FOR APPOINTMENTS', 'listingpro-bookings'); ?></h4>
                <ul class="user-booking-settings-time-slot">
                    <?php
                    $listings_for_bookings = get_user_meta(get_current_user_id(), 'listings_for_bookings', true);
                    if(!empty($listings_for_bookings)) {
                        foreach ($listings_for_bookings as $key => $val) {
                            $listings_for_bookings_ID = $key;
                            echo '<li>' . '<span data-listing-id='.$listings_for_bookings_ID.'>' . get_the_title($listings_for_bookings_ID) .  '</span>' . '<a class="booking-setting-icon-delete-listing pull-right"><i class="fa fa-trash" aria-hidden="true"></i></a>' . '</li>';
                        }
                    }
                    ?>
                </ul>
                <span class="user-booking-settings-add-new-listing clearfix">
                    <span><?php lp_get_listing_dropdown('setting-listing-dropdown', 'select2-ajax', 'listingsettings', 'bookings', null); ?></span>
                    <button class="add-listing-for-booking form-control "><i class="fa fa-plus"></i></button>
                    <h6 class="lp-booking-error"><?php echo esc_html__('Appointments not allowed for this listing', 'listingpro-bookings'); ?></h6>
                </div>

<!--                <h4 class="booking-action-header">Days Off</h4>-->
<!--                <ul class="user-booking-settings-time-slot">-->
<!--                    --><?php
//                    $dash_booking_setting = get_option('lp_booking_settings');
//                    if(!empty($dash_booking_setting)) {
//                        foreach ($dash_booking_setting as $key => $val) {
//                            $dash_booking_setting_Date = $key;
//                            echo '<li>' . '<a class="booking-setting-icon-calander"> <i class="fa fa-calendar" aria-hidden="true"></i></a> &nbsp;' . '<span>' . $dash_booking_setting_Date . '</span>' . '<a class="booking-setting-icon-delete pull-right"><i class="fa fa-trash" aria-hidden="true"></i></a>' . '</li>';
//                        }
//                    }
//                    ?>
<!--                </ul>-->
<!--                <div class="user-booking-settings-add-new-slot clearfix">-->
<!--                    <span><input maxlength="0" value="Date" class="form-control pull-left data-picker-in-out" placeholder="Date"></span>-->
<!--                    <button class="add-booking-date form-control pull-right"><i class="fa fa-plus"></i></button>-->
<!--                </div>-->
<!--                <div class="clearfix pos-relative">-->
<!--                    <div id="booking-settings-datepicker" style="height: 0px;overflow: hidden;"></div>-->
<!--                </div>-->
<!--                <div class="repeat-added-slot clearfix">-->
<!--                    <div class="repeat-switch pull-left">-->
<!--                        <input id="repeat-switch" name="repeatswitch" type="checkbox"/>-->
<!--                        <label for="repeat-switch"></label>-->
<!--                    </div>-->
<!--                    <span>Weekly recurring day off</span>-->
<!--                </div>-->
            </div>


        </div>
        <div class="clear"></div>
    </div>
<?php } ?>
</div>