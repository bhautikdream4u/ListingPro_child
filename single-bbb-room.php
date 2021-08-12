<?php get_header();?>
	<?php 
	/* The loop starts here. */
	if ( have_posts() ) {
		while ( have_posts() ) {
			the_post(); 
			$author_avatar_url = get_user_meta(get_the_author_meta( 'ID' ), "listingpro_author_img_url", true); 
			$avatar ='';
			if(!empty($author_avatar_url)) {
				$avatar =  $author_avatar_url;

			} else { 			
				$avatar_url = listingpro_get_avatar_url (get_the_author_meta( 'ID' ), $size = '51' );
				$avatar =  $avatar_url;

			}
            global $listingpro_options;
            $blog_detail_template   =   $listingpro_options['blog_single_template'];
            $blog_sidebar_style   =   $listingpro_options['blog_sidebar_style'];
            $blog_detail_class      =   '12';

            if( $blog_detail_template == 'left_sidebar' || $blog_detail_template == 'right_sidebar' || $blog_detail_template == 'right_sidebar2' || $blog_detail_template == 'left_sidebar2' )
            {
                $blog_detail_class      =   '12';
            }

            $lp_page_banner =   get_post_meta( $post->ID, 'lp_listingpro_options', true );

            if( !empty( $lp_page_banner ) && array_key_exists( 'lp_post_banner', $lp_page_banner ) ){
                $lp_page_banner =   $lp_page_banner['lp_post_banner'];
            }
            else
            {
                $lp_page_banner =   get_the_post_thumbnail_url( $post->ID, 'full' );
            }
            $style2Class    =   '';
            $style2_detail_class    =   '';
            $style2_detail_class_container  =   '';
            if( $blog_detail_template == 'fullwidth2' || $blog_detail_template == 'right_sidebar2' || $blog_detail_template == 'left_sidebar2' )
            {
                $style2Class    =   'blog-single-page-style2';
                $style2_detail_class    =   'blog-single-inner-container-style2';

            }
            if( $blog_detail_template == 'fullwidth2' || $blog_detail_template == 'left_sidebar2' || $blog_detail_template == 'right_sidebar2' )
            {
                $style2_detail_class_container  =   'page-container-second-style2';
            }
			?>
	<!--==================================Single Banner =================================-->
	<div class="page-heading listing-page <?php echo $style2Class; ?>">
        <?php
        if( $blog_detail_template == 'fullwidth2' || $blog_detail_template == 'left_sidebar2' || $blog_detail_template == 'right_sidebar2' )
        {
            echo '<div class="breadcrumb-style2">';
            echo '<div class="container">';
            if (function_exists('listingpro_breadcrumbs')) listingpro_breadcrumbs();
            echo '</div>';
            echo '</div>';
        }
        ?>
		<div class="page-heading-inner-container text-center">
            <?php
            if( $blog_detail_template == 'fullwidth2' || $blog_detail_template == 'right_sidebar2' || $blog_detail_template == 'left_sidebar2' )
            {
                 echo '<div class="blog-page-heading-style2 container text-left">';
            }
            ?>
			<h1 class="padding-bottom-15"><?php echo get_the_title(); ?></h1>
			<?php if (function_exists('listingpro_breadcrumbs')) listingpro_breadcrumbs(); ?>
			
			<?php
            if( $blog_detail_template == 'fullwidth2' || $blog_detail_template == 'right_sidebar2' || $blog_detail_template == 'left_sidebar2' )
            {
                 echo '</div>';
            }
            ?>
		</div>
		<div class="page-header-overlay"></div>
	</div><!-- ..-->	
	<!--==================================Section Open=================================-->
	<section class="aliceblue">
		<div class="container page-container-second page-container-second-blog <?php echo $style2_detail_class_container; ?>">
			<div class="row">
                <div class="blog-single-inner-container lp-border lp-border-radius-8 <?php echo $blog_detail_template; ?> <?php echo $style2_detail_class; ?>">
                    <div class="col-md-<?php echo $blog_detail_class; ?>">
						<div class="blog-content-outer-container">
							<div class="blog-content popup-gallery">
								<?php the_content(); ?>
							</div>
							<div class="blog-meta clearfix">
								<div class="blog-tags pull-left">
									<ul>
										<li><i class="fa fa-tag"></i></li>

										<?php
										$posttags = get_the_tags();
										if ($posttags) {
											foreach($posttags as $tag) {
												echo '&nbsp;<li><a href="' .get_tag_link($tag->term_id). '">#' .$tag->name. '</a></li>';
											}
										}
										?>
									</ul>
								</div>
								<div class="blog-social pull-right listing-second-view">
                                    <div class="lp-blog-grid-shares">
                                        <span><i class="fa fa-share-alt" aria-hidden="true"></i></span>
                                        <a href="<?php echo listingpro_social_sharing_buttons('facebook'); ?>" class="lp-blog-grid-shares-icon icon-fb"><i class="fa fa-facebook" aria-hidden="true"></i></a>
                                        <a href="<?php echo listingpro_social_sharing_buttons('twitter'); ?>" class="lp-blog-grid-shares-icon icon-tw"><i class="fa fa-twitter" aria-hidden="true"></i></a>
                                        <a href="<?php echo listingpro_social_sharing_buttons('pinterest'); ?>" class="lp-blog-grid-shares-icon icon-pin"><i class="fa fa-pinterest-p" aria-hidden="true"></i></a>
                                        <a href="<?php echo listingpro_social_sharing_buttons('reddit'); ?>" class="lp-blog-grid-shares-icon icon-pin"><i class="fa fa-reddit"></i></a>
                                        <a href="<?php echo listingpro_social_sharing_buttons('stumbleupon'); ?>" class="lp-blog-grid-shares-icon icon-pin"><i class="fa fa-stumbleupon"></i></a>
                                        <a href="<?php echo listingpro_social_sharing_buttons('del'); ?>" class="lp-blog-grid-shares-icon icon-pin"><i class="fa fa-delicious"></i></a>
                                    </div>
<!--									<ul class="post-stat">-->
<!--										<li class="reviews sbutton">-->
<!--										--><?php //listingpro_sharing(); ?>
<!--										</li>-->
<!--									</ul>-->
								</div>
							</div>
							<div class="blog-pagination clearfix">
								<div class="pull-left"><?php previous_post_link('%link', '<i class="fa fa-angle-double-left" aria-hidden="true"></i> '.esc_html__('Previous', 'listingpro'), false); ?></div>
								<div class="pull-right"><?php next_post_link( '%link', esc_html__('Next', 'listingpro').' <i class="fa fa-angle-double-right" aria-hidden="true"></i> ', false ); ?></div>
							</div> <!-- end navigation -->	
							<?php
							
							comments_template();
							?>

						</div>
                    </div>
                    <div class="clearfix"></div>
                </div>
			</div>
		</div>
	</section>
	<!--==================================Section Close=================================-->
	<?php 
		} // end while
	} // end if
	?>
<?php get_footer();?>