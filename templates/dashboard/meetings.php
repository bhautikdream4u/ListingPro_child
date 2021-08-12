<?php
$current_user = wp_get_current_user();
$user_id = $current_user->ID;

$a_args = array(
    'post_type' => 'bbb-room',
    'post_status' => 'publish',
    'posts_per_page' => -1,    
    'author' => $user_id,
    //'post__in' => [192],
);
$meetings = new WP_Query($a_args);
$count_meetings = $meetings->found_posts;

$meeting_id = isset($_GET['meeting_id']) ? $_GET['meeting_id'] : '';

ajax_response_markup();
?>
<!-- Modal -->
<div class="modal fade" id="meeting-delete-modal" tabindex="-1" role="dialog" aria-labelledby="meeting-delete-modal" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <?php echo esc_html__( 'are you sure you want to delete?', 'listingpro' ); ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal"><?php echo esc_html__( 'Cancel', 'listingpro' ); ?></button>
                <button type="button" class="btn btn-primary meeting-confirm-del-btn"><?php echo esc_html__( 'Delete', 'listingpro' ); ?></button>
            </div>
        </div>
    </div>
</div>
<div class="tab-pane fade in active lp-meeting-form padding-bottom-30" id="lp-meeting-form">
    <?php
    if( $count_meetings == 0 ):
        ?>
        <div class="lp-blank-section">
            <div class="col-md-12 blank-left-side">
                <img src="<?php echo listingpro_icons_url('lp_blank_trophy'); ?>">
                <h1><?php echo esc_html__('Nothing but this golden trophy!', 'listingpro'); ?></h1>
                <p class="margin-bottom-20"><?php echo esc_html__('You must be here for the first time. If you like to add some thing, click the button below.', 'listingpro'); ?></p>
                <button data-form="meetings" class="lp-add-new-btn add-new-open-form"><span><i class="fa fa-plus" aria-hidden="true"></i></span><?php echo esc_html__('Add new meeting', 'listingpro'); ?> </button>
            </div>
        </div>
        <?php
    else:
        ?>
        <div class="panel with-nav-tabs panel-default lp-dashboard-tabs col-md-11 align-center">
		
            <div class="lp-add-menu-outer clearfix">
                <h5><?php esc_html_e('All Meetings', 'listingpro'); ?></h5>
                <button data-form="meetings" class="lp-add-new-btn add-new-open-form"><span><i class="fa fa-plus" aria-hidden="true"></i></span> <?php esc_html_e('Add new', 'listingpro'); ?></button>
            </div>
            <div class="panel-body">
                <div class="lp-main-title clearfix">
                    <div class="col-md-4"><p><?php esc_html_e('Name', 'listingpro'); ?></p></div>
                    <div class="col-md-4"><p><?php esc_html_e('Email', 'listingpro'); ?></p></div>
                    <div class="col-md-2"><p><?php esc_html_e('Date', 'listingpro'); ?></p></div>
                    <div class="col-md-2"><p class="text-center"><?php esc_html_e('Action', 'listingpro'); ?></p></div>
                </div>
                <div class="tab-content clearfix">
                    <div class="tab-pane fade in active" id="tab1default">
                        <?php
                        foreach ($meetings->posts as $key => $meeting) : ?>
                            <div class="col-md-4 lp-content-before-after" data-content="Title">
                                <?php echo $meeting->post_title; ?>
                            </div>
                            <div class="col-md-4 lp-content-before-after" data-content="Email">
                                <?php echo get_post_meta( $meeting->ID, 'bbb-room-user-email', true ); ?>
                            </div>
                            <div class="col-md-2 lp-content-before-after" data-content="Date">
                                <?php echo get_the_date('Y-m-d', $meeting->ID); ?>
                            </div>
                            <div class="col-md-2 text-center lp-content-before-after" data-content="Action">
                                <div class="lp-display-inline lp-pull-left-new">
                                    <div class="lp-dot-extra-buttons">
                                        <img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABYAAAAWCAYAAADEtGw7AAAAAXNSR0IArs4c6QAAAARnQU1BAACxjwv8YQUAAAAJcEhZcwAADsMAAA7DAcdvqGQAAABtSURBVEhLYxgFgwN4R2UKekXl7gJhEBsqTDnwiM4N8YrO/Q/GUTlBUGHKAciVntG5O0DYJTSNHyo8UoFnVI61V0yuFZRLHQAyEBZ5PpHZllBhygHIMKjB/6hqMAiADKS6oUMPjGbpUUANwMAAAIAtN4uDPUCkAAAAAElFTkSuQmCC">
                                        <ul class="lp-user-menu list-style-none">
                                            <li><a class="meet-join" href="<?php echo get_the_permalink( $meeting->ID ); ?>"><i class="fa fa-video-camera"></i><span><?php esc_html_e('Join', 'listingpro'); ?></span></a></li>
                                            <li><a class="meet-edit" href="" data-targetid="<?php echo $meeting->ID; ?>" data-annID="<?php echo $meeting->ID; ?>" data-uid="<?php echo $user_id; ?>"><i class="fa fa-pencil-square-o"></i><span><?php esc_html_e('Edit', 'listingpro'); ?></span></a></li>
                                            <li><a class="meet-del" href="" data-targetID="<?php echo $meeting->ID; ?>" data-uid="<?php echo $user_id; ?>"><i class="fa fa-trash-o"></i><span><?php esc_html_e('Delete', 'listingpro'); ?></span></a></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <div style="display: none;" id="update-wrap-<?php echo $meeting->ID; ?>" class="panel with-nav-tabs panel-default lp-dashboard-tabs lp-left-panel-height margin-top-30">
                                <div class="lp-coupns-form-outer">
                                    <div class="lp-coupons-form-inner">
                                        <form class="lp-add-meeting-form" method="post">

                                            <div class="lp-coupon-box-row">
                                                <div class="form-group">
                                                    <label  class="lp-dashboard-top-label" for="meetings-btn-text">
                                                        <?php echo esc_html__('Meeting title', 'listingpro'); ?><span class="lp-requires-filed">*</span>
                                                    </label>

                                                    <input required type="text" class="form-control lp-dashboard-text-field" value="<?php echo $meeting->post_title; ?>" name="meeting_title" id="meeting_title" placeholder="<?php echo esc_html__('Meeting title', 'listingpro'); ?>"/>
                                                </div>
                                            </div>

                                            <div class="lp-coupon-box-row">
                                                <div class="form-group">

                                                    <label class="lp-dashboard-top-label" for="meeting_message">
                                                        <?php echo esc_html__('Meeting Description', 'listingpro'); ?>
                                                    </label>

                                                    <textarea class="form-control lp-dashboard-des-field" name="meeting_message" id="meeting_message" rows="3" placeholder="<?php echo esc_html__('Your Message', 'listingpro'); ?>"><?php echo $meeting->post_content; ?></textarea>
                                                </div>
                                            </div>

                                            <div class="lp-coupon-box-row">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="lp-dashboard-top-label" for="meetings-btn-text">
                                                                <?php echo esc_html__('User Name', 'listingpro'); ?><span class="lp-requires-filed">*</span>
                                                            </label>

                                                            <input required type="text" class="form-control lp-dashboard-text-field" value="<?php echo get_post_meta( $meeting->ID, 'bbb-room-user-name', true ); ?>" name="bbb-room-user-name" id="bbb-room-user-name" placeholder="<?php echo esc_html__('Enter viewer name', 'listingpro'); ?>"/>
                                                        </div>
                                                    </div>

                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="lp-dashboard-top-label" for="meetings-btn-text">
                                                                <?php echo esc_html__('User Email', 'listingpro'); ?><span class="lp-requires-filed">*</span>
                                                            </label>

                                                            <input required type="email" class="form-control lp-dashboard-text-field" value="<?php echo get_post_meta( $meeting->ID, 'bbb-room-user-email', true ); ?>" name="bbb-room-user-email" id="bbb-room-user-email" placeholder="<?php echo esc_html__('Enter viewer email', 'listingpro'); ?>"/>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="lp-coupon-box-row">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="lp-dashboard-top-label" for="meetings-btn-text">
                                                                <?php echo esc_html__('Moderator Code', 'listingpro'); ?><span class="lp-requires-filed">*</span>
                                                            </label>
                                                            <input required type="text" class="form-control lp-dashboard-text-field" name="bbb-moderator-code" id="bbb-moderator-code" value="<?php echo get_post_meta( $meeting->ID, 'bbb-room-moderator-code', true ); ?>" />
                                                        </div>
                                                    </div>

                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="lp-dashboard-top-label" for="meetings-btn-text">
                                                                <?php echo esc_html__('Viewer Code', 'listingpro'); ?><span class="lp-requires-filed">*</span>
                                                            </label>
                                                            <input required type="text" class="form-control lp-dashboard-text-field" name="bbb-viewer-code" id="bbb-viewer-code" value="<?php echo get_post_meta( $meeting->ID, 'bbb-room-viewer-code', true ); ?>" />
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="lp-coupon-box-row">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="lp-dashboard-top-label" for="meetings-btn-text">
                                                                <?php echo esc_html__('Recordable', 'listingpro'); ?><span class="lp-requires-filed">*</span>
                                                            </label>
                                                            <?php $recordable = get_post_meta( $meeting->ID, 'bbb-room-recordable', true ); ?>
                                                            <input type="checkbox" name="bbb-room-recordable" id="bbb-room-recordable" value="checked" <?php echo ($recordable) ? 'checked' : ''; ?> />
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="lp-dashboard-top-label" for="meetings-btn-text">
                                                                <?php echo esc_html__('Wait for Moderator', 'listingpro'); ?><span class="lp-requires-filed">*</span>
                                                            </label>
                                                            <?php $wait_for = get_post_meta( $meeting->ID, 'bbb-room-wait-for-moderator', true ); ?>
                                                            <input type="checkbox" name="bbb-room-wait-for-moderator" id="bbb-room-wait-for-moderator" value="checked" <?php echo ($wait_for) ? 'checked' : ''; ?> />
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="lp-coupon-box-row lp-save-btn-container">
                                                <div class="form-group">
                                                    <div class="row">
                                                        <div class="form-group col-sm-12 clearfix">
                                                            <input type="hidden" name="user_id" value="<?php echo absint( $user_id ); ?>" />
                                                            <input type="hidden" name="meeting_id" value="<?php echo absint( $meeting->ID ); ?>" />
                                                            <button id="ad-meeting-btn" data-uid="<?php echo $user_id; ?>" class="lp-coupns-btns pull-right "><?php echo esc_html__( 'save', 'listingpro' ); ?></button>
                                                            <button data-cancel="meetings" id="cancelLpAnnouncment" class="lp-coupns-btns cancel-ad-new-btn pull-right lp-margin-right-10 "><?php echo esc_html__( 'Cancel', 'listingpro' ); ?></button>

                                                            <?php wp_nonce_field( 'connex_add_meeting' ); ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach;
                        wp_reset_postdata(); 
                        ?>
                    </div>
                </div>
            </div>
        </div>
        <?php
    endif;
    ?>

    <div style="display: none;" id="meetings-form-toggle" class="panel with-nav-tabs panel-default lp-dashboard-tabs lp-left-panel-height  lp-container-center">
        <div class="lp-review-sorting clearfix">
            <a href="" class="lp-view-all-btn all-with-refresh"><i class="fa fa-angle-left" aria-hidden="true"></i> <?php echo esc_html__('All Meetings', 'listingpro'); ?></a>
            <h5 class="margin-top-0 clearfix"><?php echo esc_html__('Add Meetings', 'listingpro'); ?>
            </h5>
        </div>
        <div class="lp-coupns-form-outer">
            <div class="lp-coupons-form-inner">
                <form class="lp-add-meeting-form" method="post">

                    <div class="lp-coupon-box-row">
                        <div class="form-group">
                            <label  class="lp-dashboard-top-label" for="meetings-btn-text">
                                <?php echo esc_html__('Meeting title', 'listingpro'); ?><span class="lp-requires-filed">*</span>
                            </label>

                            <input required type="text" class="form-control lp-dashboard-text-field" value="" name="meeting_title" id="meeting_title" placeholder="<?php echo esc_html__('Meeting title', 'listingpro'); ?>"/>
                        </div>
                    </div>

                    <div class="lp-coupon-box-row">
                        <div class="form-group">

                            <label class="lp-dashboard-top-label" for="meeting_message">
                                <?php echo esc_html__('Meeting Description', 'listingpro'); ?>
                            </label>

                            <textarea class="form-control lp-dashboard-des-field" name="meeting_message" id="meeting_message" rows="3" placeholder="<?php echo esc_html__('Your Message', 'listingpro'); ?>"></textarea>
                        </div>
                    </div>

                    <div class="lp-coupon-box-row">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="lp-dashboard-top-label" for="meetings-btn-text">
                                        <?php echo esc_html__('User Name', 'listingpro'); ?><span class="lp-requires-filed">*</span>
                                    </label>

                                    <input required type="name" class="form-control lp-dashboard-text-field" value="" name="bbb-room-user-name" id="bbb-room-user-name" placeholder="<?php echo esc_html__('Enter view name', 'listingpro'); ?>"/>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="lp-dashboard-top-label" for="meetings-btn-text">
                                        <?php echo esc_html__('User Email', 'listingpro'); ?><span class="lp-requires-filed">*</span>
                                    </label>

                                    <input required type="email" class="form-control lp-dashboard-text-field" value="" name="bbb-room-user-email" id="bbb-room-user-email" placeholder="<?php echo esc_html__('Enter view email', 'listingpro'); ?>"/>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="lp-coupon-box-row">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="lp-dashboard-top-label" for="meetings-btn-text">
                                        <?php echo esc_html__('Moderator Code', 'listingpro'); ?><span class="lp-requires-filed">*</span>
                                    </label>
                                    <?php $moderator_code = Bigbluebutton_Admin_Helper::generate_random_code(); ?>
                                    <input required type="text" class="form-control lp-dashboard-text-field" name="bbb-moderator-code" id="bbb-moderator-code" value="<?php echo $moderator_code; ?>" />
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="lp-dashboard-top-label" for="meetings-btn-text">
                                        <?php echo esc_html__('Viewer Code', 'listingpro'); ?><span class="lp-requires-filed">*</span>
                                    </label>
                                    <?php $viewer_code = Bigbluebutton_Admin_Helper::generate_random_code(); ?>
                                    <input required type="text" class="form-control lp-dashboard-text-field" name="bbb-viewer-code" id="bbb-viewer-code" value="<?php echo $viewer_code; ?>" />
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="lp-coupon-box-row">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="lp-dashboard-top-label" for="meetings-btn-text">
                                        <?php echo esc_html__('Recordable', 'listingpro'); ?><span class="lp-requires-filed">*</span>
                                    </label>

                                    <input type="checkbox" name="bbb-room-recordable" id="bbb-room-recordable" value="checked"/>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="lp-dashboard-top-label" for="meetings-btn-text">
                                        <?php echo esc_html__('Wait for Moderator', 'listingpro'); ?><span class="lp-requires-filed">*</span>
                                    </label>

                                    <input type="checkbox" name="bbb-room-wait-for-moderator" id="bbb-room-wait-for-moderator" value="checked"/>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="lp-coupon-box-row lp-save-btn-container">
                        <div class="form-group">
                            <div class="row">
                                <div class="form-group col-sm-12 clearfix">
                                    <input type="hidden" name="user_id" value="<?php echo absint( $user_id ); ?>" />
                                    <button id="ad-meeting-btn" data-uid="<?php echo $user_id; ?>" class="lp-coupns-btns pull-right "><?php echo esc_html__( 'save', 'listingpro' ); ?></button>
                                    <button data-cancel="meetings" id="cancelLpAnnouncment" class="lp-coupns-btns cancel-ad-new-btn pull-right lp-margin-right-10 "><?php echo esc_html__( 'Cancel', 'listingpro' ); ?></button>

                                    <?php wp_nonce_field( 'connex_add_meeting' ); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>