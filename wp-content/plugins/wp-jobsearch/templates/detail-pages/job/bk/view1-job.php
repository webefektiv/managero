<?php

global $post, $jobsearch_plugin_options;
$job_id = $post->ID;

$job_employer_id = get_post_meta( $post->ID, 'jobsearch_field_job_posted_by', true ); // get job employer

$job_employer_name = jobsearch_job_get_company_name( $job_id, '' );


wp_enqueue_script( 'jobsearch-job-functions-script' );

//
$social_share_allow = isset( $jobsearch_plugin_options['job_detail_soc_share'] ) ? $jobsearch_plugin_options['job_detail_soc_share'] : '';

$all_location_allow     = isset( $jobsearch_plugin_options['all_location_allow'] ) ? $jobsearch_plugin_options['all_location_allow'] : '';
$job_views_publish_date = isset( $jobsearch_plugin_options['job_views_publish_date'] ) ? $jobsearch_plugin_options['job_views_publish_date'] : '';


// job end
// employer start

$employer_id = $job_employer_id;

$captcha_switch    = isset( $jobsearch_plugin_options['captcha_switch'] ) ? $jobsearch_plugin_options['captcha_switch'] : '';
$jobsearch_sitekey = isset( $jobsearch_plugin_options['captcha_sitekey'] ) ? $jobsearch_plugin_options['captcha_sitekey'] : '';

$all_location_allow = isset( $jobsearch_plugin_options['all_location_allow'] ) ? $jobsearch_plugin_options['all_location_allow'] : '';
$job_types_switch   = isset( $jobsearch_plugin_options['job_types_switch'] ) ? $jobsearch_plugin_options['job_types_switch'] : '';

$plugin_default_view          = isset( $jobsearch_plugin_options['jobsearch-default-page-view'] ) ? $jobsearch_plugin_options['jobsearch-default-page-view'] : 'full';
$plugin_default_view_with_str = '';
if ( $plugin_default_view == 'boxed' ) {

	$plugin_default_view_with_str = isset( $jobsearch_plugin_options['jobsearch-boxed-view-width'] ) && $jobsearch_plugin_options['jobsearch-boxed-view-width'] != '' ? $jobsearch_plugin_options['jobsearch-boxed-view-width'] : '1140px';
	if ( $plugin_default_view_with_str != '' ) {
		$plugin_default_view_with_str = ' style="width:' . $plugin_default_view_with_str . '"';
	}
}

$reviews_switch = isset( $jobsearch_plugin_options['reviews_switch'] ) ? $jobsearch_plugin_options['reviews_switch'] : '';

$employer_views_count = get_post_meta( $employer_id, "jobsearch_employer_views_count", true );

//
$user_facebook_url    = get_post_meta( $employer_id, 'jobsearch_field_user_facebook_url', true );
$user_twitter_url     = get_post_meta( $employer_id, 'jobsearch_field_user_twitter_url', true );
$user_google_plus_url = get_post_meta( $employer_id, 'jobsearch_field_user_google_plus_url', true );
$user_youtube_url     = get_post_meta( $employer_id, 'jobsearch_field_user_youtube_url', true );
$user_dribbble_url    = get_post_meta( $employer_id, 'jobsearch_field_user_dribbble_url', true );
$user_linkedin_url    = get_post_meta( $employer_id, 'jobsearch_field_user_linkedin_url', true );

$sectors_enable_switch = isset( $jobsearch_plugin_options['sectors_onoff_switch'] ) ? $jobsearch_plugin_options['sectors_onoff_switch'] : '';
//
$emp_phone_switch    = isset( $jobsearch_plugin_options['employer_phone_field'] ) ? $jobsearch_plugin_options['employer_phone_field'] : '';
$emp_web_switch      = isset( $jobsearch_plugin_options['employer_web_field'] ) ? $jobsearch_plugin_options['employer_web_field'] : '';
$emp_foundate_switch = isset( $jobsearch_plugin_options['employer_founded_date'] ) ? $jobsearch_plugin_options['employer_founded_date'] : '';

$employer_obj     = get_post( $employer_id );
$employer_content = $employer_obj->post_content;
$employer_content = apply_filters( 'the_content', $employer_content );

$employer_join_date = isset( $employer_obj->post_date ) ? $employer_obj->post_date : '';

$employer_address = get_post_meta( $employer_id, 'jobsearch_field_location_address', true );

if ( $employer_address == '' ) {
	$employer_address = jobsearch_job_item_address( $employer_id );
}

$employer_phone = get_post_meta( $employer_id, 'jobsearch_field_user_phone', true );

$user_id          = jobsearch_get_employer_user_id( $employer_id );
$user_obj         = get_user_by( 'ID', $user_id );
$user_displayname = isset( $user_obj->display_name ) ? $user_obj->display_name : '';
$user_displayname = apply_filters( 'jobsearch_user_display_name', $user_displayname, $user_obj );

$user_def_avatar_url = get_avatar_url( $user_id, array( 'size' => 140 ) );


$user_avatar_id = get_post_thumbnail_id( $employer_id );
if ( $user_avatar_id > 0 ) {
	$user_thumbnail_image = wp_get_attachment_image_src( $user_avatar_id, 'full' );
	$user_def_avatar_url  = isset( $user_thumbnail_image[0] ) && esc_url( $user_thumbnail_image[0] ) != '' ? $user_thumbnail_image[0] : '';
}
$user_def_avatar_url = $user_def_avatar_url == '' ? jobsearch_employer_image_placeholder() : $user_def_avatar_url;


wp_enqueue_script( 'isotope-min' );

$employer_cover_image_src_style_str = '';
if ( $employer_id != '' ) {
	if ( class_exists( 'JobSearchMultiPostThumbnails' ) ) {
		$employer_cover_image_src = JobSearchMultiPostThumbnails::get_post_thumbnail_url( 'employer', 'cover-image', $employer_id );
		if ( $employer_cover_image_src != '' ) {
			$employer_cover_image_src_style_str = ' style="background:url(' . esc_url( $employer_cover_image_src ) . ')"';
		}
	}
}

ob_start();
?>

    <div class="headerWrap">
        <img src="<?= get_template_directory_uri(); ?>/assets/bgheader.png"/>
    </div>

    <div class="titleWrap">
        <h1><?php the_title(); ?></h1>
        <h2><?= $job_employer_name; ?></h2>
    </div>

    <div class="container-white">
        <div class="container-content">
            <nav id="jobtabs">
                <div class="nav nav-tabs" id="nav-tab" role="tablist">
                    <a class="nav-item nav-link active" id="nav-company-tab" data-toggle="tab" href="#nav-company"
                       role="tab"
                       aria-controls="nav-company" aria-selected="true">Descrierea Companiei</a>
                    <a class="nav-item nav-link" id="nav-job-tab" data-toggle="tab" href="#nav-job" role="tab"
                       aria-controls="nav-job" aria-selected="false">Descrierea Jobului</a>
                </div>
            </nav>

            <div class="tab-content" id="nav-tabContent">
                <!--- company tab -->
                <div class="tab-pane fade show active" id="nav-company" role="tabpanel"
                     aria-labelledby="nav-company-tab">
                    <div class="jobsearch-main-content">

                        <!-- Main Section -->
                        <div class="jobsearch-main-section">
                            <div class="jobsearch-plugin-default-container" <?php echo force_balance_tags( $plugin_default_view_with_str ); ?>>
                                <div class="jobsearch-row">

                                    <div class="jobsearch-column-12 jobsearch-typo-wrap employerWrap">
                                        <figure class="jobsearch-jobdetail-list">

                        <span class="jobsearch-jobdetail-listthumb">
                            <?php echo jobsearch_member_promote_profile_iconlab( $employer_id, 'employer_detv1' ); ?>
                            <img src="<?php echo( $user_def_avatar_url ) ?>" alt="">
                             <h2><?= $job_employer_name; ?></h2>
                        </span>

                                            <figcaption <?php echo force_balance_tags( $employer_cover_image_src_style_str ); ?>>

                                            </figcaption>

                                        </figure>
                                    </div>
                                    <!-- Job Detail List -->
                                    <div class="jobsearch-column-12">
										<?php if ( $employer_content != '' ) { ?>
                                            <div class="jobsearch-description pads">
												<?php echo( $employer_content ) ?>
                                            </div>
										<?php } ?>

                                    </div>


                                    <!-- Job Detail Content -->
                                    <div class="jobsearch-column-12 jobsearch-typo-wrap">
										<?php
										$custom_all_fields = get_option( 'jobsearch_custom_field_employer' );
										if ( ! empty( $custom_all_fields ) || $employer_content != '' ) {
										?>
                                        <div class="jobsearch-jobdetail-content jobsearch-employerdetail-content">
											<?php
											if ( ! empty( $custom_all_fields ) ) {
											$sector_str = jobsearch_employer_get_all_sectors( $employer_id, '', '', '', '<small>', '</small>' );
											$sector_str = apply_filters( 'jobsearch_gew_wout_anchr_sector_str_html', $sector_str, $employer_id, '<small>', '</small>' );
											?>
                                            <div class="jobsearch-content-title">
                                                <!--                                                        <h2>-->
												<?php //esc_html_e('Overview', 'wp-jobsearch') ?><!--</h2></div>-->
                                                <div class="jobsearch-jobdetail-services">
                                                    <ul class="jobsearch-row">
                                                        <!--													        --><?php
														//													        if ($sectors_enable_switch == 'on') {
														//														        ?>
                                                        <!--                                                                <li class="jobsearch-column-4">-->
                                                        <!--                                                                    <i class="jobsearch-icon jobsearch-folder"></i>-->
                                                        <!--                                                                    <div class="jobsearch-services-text">-->
														<?php //esc_html_e('Sectors', 'wp-jobsearch') ?><!---->
														<?php //echo wp_kses($sector_str, array('small' => array())) ?><!--</div>-->
                                                        <!--                                                                </li>-->
                                                        <!--														        --><?php
														//													        }
														//													        ?>
                                                        <!--                                                            <li class="jobsearch-column-4">-->
                                                        <!--                                                                <i class="jobsearch-icon jobsearch-briefcase"></i>-->
                                                        <!--                                                                <div class="jobsearch-services-text">--><?php //esc_html_e('Posted Jobs', 'wp-jobsearch') ?>
                                                        <!--                                                                    <small>-->
														<?php //echo jobsearch_employer_total_jobs_posted($employer_id) ?><!--</small>-->
                                                        <!--                                                                </div>-->
                                                        <!--                                                            </li>-->
                                                        <!--                                                            <li class="jobsearch-column-4">-->
                                                        <!--                                                                <i class="jobsearch-icon jobsearch-view"></i>-->
                                                        <!--                                                                <div class="jobsearch-services-text">--><?php //esc_html_e('Viewed', 'wp-jobsearch') ?>
                                                        <!--                                                                    <small>-->
														<?php //echo($employer_views_count) ?><!--</small>-->
                                                        <!--                                                                </div>-->
                                                        <!--                                                            </li>-->
														<?php
														$cus_fields = array( 'content' => '' );
														$cus_fields = apply_filters( 'jobsearch_custom_fields_list', 'employer', $employer_id, $cus_fields, '<li class="jobsearch-column-4">', '</li>' );
														if ( isset( $cus_fields['content'] ) && $cus_fields['content'] != '' ) {
															echo( $cus_fields['content'] );
														}
														?>
                                                    </ul>
                                                </div>
												<?php
												}

												?>
                                            </div>


											<?php
											}
											$exfield_list               = get_post_meta( $employer_id, 'jobsearch_field_team_title', true );
											$exfield_list_val           = get_post_meta( $employer_id, 'jobsearch_field_team_description', true );
											$team_designationfield_list = get_post_meta( $employer_id, 'jobsearch_field_team_designation', true );
											$team_experiencefield_list  = get_post_meta( $employer_id, 'jobsearch_field_team_experience', true );
											$team_imagefield_list       = get_post_meta( $employer_id, 'jobsearch_field_team_image', true );
											$team_facebookfield_list    = get_post_meta( $employer_id, 'jobsearch_field_team_facebook', true );
											$team_googlefield_list      = get_post_meta( $employer_id, 'jobsearch_field_team_google', true );
											$team_twitterfield_list     = get_post_meta( $employer_id, 'jobsearch_field_team_twitter', true );
											$team_linkedinfield_list    = get_post_meta( $employer_id, 'jobsearch_field_team_linkedin', true );

											if ( is_array( $exfield_list ) && sizeof( $exfield_list ) > 0 ) {
												$total_team = sizeof( $exfield_list );

												$rand_num_ul = rand( 1000000, 99999999 );
												?>
                                                <div class="jobsearch-employer-wrap-section">
                                                    <div class="jobsearch-content-title jobsearch-addmore-space">
                                                        <h2><?php printf( esc_html__( 'Team Members (%s)', 'wp-jobsearch' ), $total_team ); ?></h2>
                                                    </div>
                                                    <div class="jobsearch-candidate jobsearch-candidate-grid">
                                                        <ul id="members-holder-<?php echo absint( $rand_num_ul ) ?>"
                                                            class="jobsearch-row">
															<?php
															$exfield_counter = 0;
															foreach ( $exfield_list as $exfield ) {
																$rand_num = rand( 1000000, 99999999 );

																$exfield_val               = isset( $exfield_list_val[ $exfield_counter ] ) ? $exfield_list_val[ $exfield_counter ] : '';
																$team_designationfield_val = isset( $team_designationfield_list[ $exfield_counter ] ) ? $team_designationfield_list[ $exfield_counter ] : '';
																$team_experiencefield_val  = isset( $team_experiencefield_list[ $exfield_counter ] ) ? $team_experiencefield_list[ $exfield_counter ] : '';
																$team_imagefield_val       = isset( $team_imagefield_list[ $exfield_counter ] ) ? $team_imagefield_list[ $exfield_counter ] : '';
																$team_facebookfield_val    = isset( $team_facebookfield_list[ $exfield_counter ] ) ? $team_facebookfield_list[ $exfield_counter ] : '';
																$team_googlefield_val      = isset( $team_googlefield_list[ $exfield_counter ] ) ? $team_googlefield_list[ $exfield_counter ] : '';
																$team_twitterfield_val     = isset( $team_twitterfield_list[ $exfield_counter ] ) ? $team_twitterfield_list[ $exfield_counter ] : '';
																$team_linkedinfield_val    = isset( $team_linkedinfield_list[ $exfield_counter ] ) ? $team_linkedinfield_list[ $exfield_counter ] : '';
																?>
                                                                <li class="jobsearch-column-4">
                                                                    <script>
                                                                        jQuery(document).ready(function () {
                                                                            jQuery('a[id^="fancybox_notes"]').fancybox({
                                                                                'titlePosition': 'inside',
                                                                                'transitionIn': 'elastic',
                                                                                'transitionOut': 'elastic',
                                                                                'width': 400,
                                                                                'height': 250,
                                                                                'padding': 40,
                                                                                'autoSize': false
                                                                            });
                                                                        });
                                                                    </script>
                                                                    <figure>
                                                                        <a id="fancybox_notes<?php echo( $rand_num ) ?>"
                                                                           href="#notes<?php echo( $rand_num ) ?>"
                                                                           class="jobsearch-candidate-grid-thumb"><img
                                                                                    src="<?php echo( $team_imagefield_val ) ?>"
                                                                                    alt=""> <span
                                                                                    class="jobsearch-candidate-grid-status"></span></a>
                                                                        <figcaption>
                                                                            <h2>
                                                                                <a id="fancybox_notes_txt<?php echo( $rand_num ) ?>"
                                                                                   href="#notes<?php echo( $rand_num ) ?>"><?php echo( $exfield ) ?></a>
                                                                            </h2>
                                                                            <p><?php echo( $team_designationfield_val ) ?></p>
																			<?php
																			if ( $team_experiencefield_val != '' ) {
																				echo '<span>' . sprintf( esc_html__( 'Experience: %s', 'wp-jobsearch' ), $team_experiencefield_val ) . '</span>';
																			}
																			?>
                                                                        </figcaption>
                                                                    </figure>

                                                                    <div id="notes<?php echo( $rand_num ) ?>"
                                                                         style="display: none;"><?php echo( $exfield_val ) ?></div>
																	<?php
																	if ( $team_facebookfield_val != '' || $team_googlefield_val != '' || $team_twitterfield_val != '' || $team_linkedinfield_val != '' ) {
																		?>
                                                                        <ul class="jobsearch-social-icons">
																			<?php
																			if ( $team_facebookfield_val != '' ) {
																				?>
                                                                                <li>
                                                                                    <a href="<?php echo( $team_facebookfield_val ) ?>"
                                                                                       data-original-title="facebook"
                                                                                       class="jobsearch-icon jobsearch-facebook-logo"></a>
                                                                                </li>
																				<?php
																			}
																			if ( $team_googlefield_val != '' ) {
																				?>
                                                                                <li>
                                                                                    <a href="<?php echo( $team_googlefield_val ) ?>"
                                                                                       data-original-title="google-plus"
                                                                                       class="jobsearch-icon jobsearch-google-plus-logo-button"></a>
                                                                                </li>
																				<?php
																			}
																			if ( $team_twitterfield_val != '' ) {
																				?>
                                                                                <li>
                                                                                    <a href="<?php echo( $team_twitterfield_val ) ?>"
                                                                                       data-original-title="twitter"
                                                                                       class="jobsearch-icon jobsearch-twitter-logo"></a>
                                                                                </li>
																				<?php
																			}
																			if ( $team_linkedinfield_val != '' ) {
																				?>
                                                                                <li>
                                                                                    <a href="<?php echo( $team_linkedinfield_val ) ?>"
                                                                                       data-original-title="linkedin"
                                                                                       class="jobsearch-icon jobsearch-linkedin-button"></a>
                                                                                </li>
																				<?php
																			}
																			?>
                                                                        </ul>
																		<?php
																	}
																	?>
                                                                </li>
																<?php
																$exfield_counter ++;

																if ( $exfield_counter >= 3 ) {
																	break;
																}
															}
															?>
                                                        </ul>
                                                    </div>
													<?php
													$reults_per_page = 3;
													$total_pages     = 1;
													if ( $total_team > 0 && $reults_per_page > 0 && $total_team > $reults_per_page ) {
														$total_pages = ceil( $total_team / $reults_per_page );
														?>
                                                        <div class="jobsearch-load-more">
                                                            <a class="load-more-team" href="javascript:void(0);"
                                                               data-id="<?php echo( $employer_id ) ?>"
                                                               data-pref="jobsearch"
                                                               data-rand="<?php echo( $rand_num_ul ) ?>"
                                                               data-pages="<?php echo( $total_pages ) ?>"
                                                               data-page="1"><?php esc_html_e( 'Load More', 'wp-jobsearch' ) ?></a>
                                                        </div>
														<?php
													}
													?>
                                                </div>
												<?php
											}
											//
											$company_gal_imgs   = get_post_meta( $employer_id, 'jobsearch_field_company_gallery_imgs', true );
											$company_gal_videos = get_post_meta( $employer_id, 'jobsearch_field_company_gallery_videos', true );
											if ( ! empty( $company_gal_imgs ) ) {
												?>
                                                <div class="jobsearch-employer-wrap-section">
                                                    <div class="jobsearch-content-title jobsearch-addmore-space">
                                                        <!--                                                    <h2>-->
														<?php //esc_html_e('Office Photos', 'wp-jobsearch') ?><!--</h2>-->
                                                    </div>
                                                    <div class="jobsearch-gallery jobsearch-simple-gallery">
                                                        <ul class="jobsearch-row grid">
															<?php
															$profile_gal_counter = 1;
															$_gal_img_counter    = 0;
															foreach ( $company_gal_imgs as $company_gal_img ) {
																if ( $company_gal_img != '' && absint( $company_gal_img ) <= 0 ) {
																	$company_gal_img = jobsearch_get_attachment_id_from_url( $company_gal_img );
																}
																$gal_thumbnail_image = wp_get_attachment_image_src( $company_gal_img, 'large' );
																$gal_thumb_image_src = isset( $gal_thumbnail_image[0] ) && esc_url( $gal_thumbnail_image[0] ) != '' ? $gal_thumbnail_image[0] : '';

																$gal_video_url = isset( $company_gal_videos[ $_gal_img_counter ] ) && ( $company_gal_videos[ $_gal_img_counter ] ) != '' ? $company_gal_videos[ $_gal_img_counter ] : '';
																if ( $gal_video_url != '' ) {

																	if ( strpos( $gal_video_url, 'watch?v=' ) !== false ) {
																		$gal_video_url = str_replace( 'watch?v=', 'embed/', $gal_video_url );
																	}

																	if ( strpos( $gal_video_url, '?' ) !== false ) {
																		$gal_video_url .= '&autoplay=1';
																	} else {
																		$gal_video_url .= '?autoplay=1';
																	}
																}

																$gal_full_image     = wp_get_attachment_image_src( $company_gal_img, 'full' );
																$gal_full_image_src = isset( $gal_full_image[0] ) && esc_url( $gal_full_image[0] ) != '' ? $gal_full_image[0] : '';
																?>
                                                                <li class="grid-item <?php echo( $profile_gal_counter == 2 ? 'jobsearch-column-6' : 'jobsearch-column-3' ) ?>">
                                                                    <figure>
                                                                <span class="grid-item-thumb"><small
                                                                            style="background-image: url('<?php echo( $gal_thumb_image_src ) ?>');"></small></span>
                                                                        <figcaption>
                                                                            <div class="img-icons">
                                                                                <a href="<?php echo( $gal_video_url != '' ? $gal_video_url : $gal_full_image_src ) ?>"
                                                                                   class="<?php echo( $gal_video_url != '' ? 'fancybox-video' : 'fancybox' ) ?>" <?php echo( $gal_video_url != '' ? 'data-fancybox-type="iframe"' : '' ) ?>
                                                                                   data-fancybox-group="group"><i
                                                                                            class="<?php echo( $gal_video_url != '' ? 'fa fa-play' : 'fa fa-image' ) ?>"></i></a>
                                                                            </div>
                                                                        </figcaption>
                                                                    </figure>
                                                                </li>
																<?php
																$profile_gal_counter ++;
																$_gal_img_counter ++;
															}
															?>
                                                        </ul>
                                                    </div>
                                                </div>
												<?php
											}

											if ( $reviews_switch == 'on' ) {
												$post_reviews_args = array(
													'post_id'    => $employer_id,
													'list_label' => esc_html__( 'Company Reviews', 'wp-jobsearch' ),
												);
												do_action( 'jobsearch_post_reviews_list', $post_reviews_args );

												$review_form_args = array(
													'post_id' => $employer_id,
												);
												do_action( 'jobsearch_add_review_form', $review_form_args );
											}
											//
											$default_date_time_formate = 'd-m-Y H:i:s';
											$args                      = array(
												'posts_per_page' => 20,
												'paged'          => 1,
												'post_type'      => 'job',
												'post_status'    => 'publish',
												'meta_key'       => $meta_key,
												'order'          => 'DESC',
												'orderby'        => 'ID',
												'meta_query'     => array(
													array(
														'key'     => 'jobsearch_field_job_expiry_date',
														'value'   => strtotime( current_time( $default_date_time_formate, 1 ) ),
														'compare' => '>=',
													),
													array(
														'key'     => 'jobsearch_field_job_status',
														'value'   => 'approved',
														'compare' => '=',
													),
													array(
														'key'     => 'jobsearch_field_job_posted_by',
														'value'   => $employer_id,
														'compare' => '=',
													),
												),
											);
											$args                      = apply_filters( 'jobsearch_employer_rel_jobs_query_args', $args );
											$jobs_query                = new WP_Query( $args );

											if ( $jobs_query->have_posts() ) {
												?>
                                                <div class="jobsearch-margin-top">
                                                    <div class="jobsearch-section-title">
                                                        <h2><?php printf( esc_html__( 'Active Jobs From %s', 'wp-jobsearch' ), $user_displayname ) ?></h2>
                                                    </div>

													<?php
													ob_start();
													?>
                                                    <div class="jobsearch-job jobsearch-joblisting-classic jobsearch-jobdetail-joblisting">
                                                        <ul class="jobsearch-row">
															<?php
															while ( $jobs_query->have_posts() ) : $jobs_query->the_post();
																$job_id               = get_the_ID();
																$post_thumbnail_id    = jobsearch_job_get_profile_image( $job_id );
																$post_thumbnail_image = wp_get_attachment_image_src( $post_thumbnail_id, 'thumbnail' );
																$post_thumbnail_src   = isset( $post_thumbnail_image[0] ) && esc_url( $post_thumbnail_image[0] ) != '' ? $post_thumbnail_image[0] : '';

																$company_name           = jobsearch_job_get_company_name( $job_id, '@ ' );
																$jobsearch_job_featured = get_post_meta( $job_id, 'jobsearch_field_job_featured', true );
																$get_job_location       = get_post_meta( $job_id, 'jobsearch_field_location_address', true );

																$job_city_title = '';
																$get_job_city   = get_post_meta( $job_id, 'jobsearch_field_location_location3', true );
																if ( $get_job_city == '' ) {
																	$get_job_city = get_post_meta( $job_id, 'jobsearch_field_location_location2', true );
																}
																if ( $get_job_city == '' ) {
																	$get_job_city = get_post_meta( $job_id, 'jobsearch_field_location_location1', true );
																}

																$job_city_tax = $get_job_city != '' ? get_term_by( 'slug', $get_job_city, 'job-location' ) : '';
																if ( is_object( $job_city_tax ) ) {
																	$job_city_title = $job_city_tax->name;
																}

																$sector_str = jobsearch_job_get_all_sectors( $job_id, '', '', '', '<li><i class="jobsearch-icon jobsearch-filter-tool-black-shape"></i>', '</li>' );

																$job_type_str = jobsearch_job_get_all_jobtypes( $job_id, 'jobsearch-option-btn' );
																?>
                                                                <li class="jobsearch-column-12">
                                                                    <div class="jobsearch-joblisting-classic-wrap">
                                                                        <figure>
                                                                            <a href="<?php echo get_permalink( $job_id ) ?>"><img
                                                                                        src="<?php echo( $post_thumbnail_src ) ?>"
                                                                                        alt=""></a></figure>
                                                                        <div class="jobsearch-joblisting-text">
                                                                            <div class="jobsearch-list-option">
                                                                                <h2>
                                                                                    <a href="<?php echo get_permalink( $job_id ) ?>"><?php echo get_the_title( $job_id ) ?></a>
																					<?php
																					if ( $jobsearch_job_featured == 'on' ) {
																						?>
                                                                                        <span><?php echo esc_html__( 'Featured', 'wp-jobsearch' ); ?></span>
																						<?php
																					}
																					?>
                                                                                </h2>
                                                                                <ul>
																					<?php
																					if ( $company_name != '' ) {
																						?>
                                                                                        <li><?php echo force_balance_tags( $company_name ); ?></li>
																						<?php
																					}
																					if ( ! empty( $job_city_title ) && $all_location_allow == 'on' ) {
																						?>
                                                                                        <li>
                                                                                            <i class="jobsearch-icon jobsearch-maps-and-flags"></i><?php echo esc_html( $job_city_title ); ?>
                                                                                        </li>
																						<?php
																					}

																					if ( ! empty( $sector_str ) && $sectors_enable_switch == 'on' ) {
																						echo apply_filters( 'jobsearch_joblisting_sector_str_html', $sector_str, $job_id, '<li><i class="jobsearch-icon jobsearch-calendar"></i>', '</li>' );
																					}
																					?>
                                                                                </ul>
                                                                            </div>
                                                                            <div class="jobsearch-job-userlist">
																				<?php
																				if ( $job_type_str != '' && $job_types_switch == 'on' ) {
																					echo force_balance_tags( $job_type_str );
																				}
																				$book_mark_args = array(
																					'job_id'       => $job_id,
																					'before_icon'  => 'fa fa-heart-o',
																					'after_icon'   => 'fa fa-heart',
																					'anchor_class' => 'jobsearch-job-like'
																				);
																				do_action( 'jobsearch_job_shortlist_button_frontend', $book_mark_args );
																				?>
                                                                            </div>
                                                                            <div class="clearfix"></div>
                                                                        </div>
                                                                    </div>
                                                                </li>
																<?php
															endwhile;
															wp_reset_postdata();
															?>
                                                        </ul>
                                                    </div>
													<?php
													$activ_jobs_html = ob_get_clean();
													echo apply_filters( 'jobsearch_employer_detail_active_jobs_html', $activ_jobs_html, $jobs_query );
													?>
                                                </div>
												<?php
											}
											?>
                                        </div>
                                        <!-- Job Detail Content -->
                                        <!-- Job Detail SideBar -->
                                        <aside class="jobsearch-column-12 jobsearch-typo-wrap">
											<?php do_action( 'jobsearch_employer_detail_side_before_contact_form', array( 'id' => $employer_id ) ); ?>
											<?php
											$emp_det_contact_form = isset( $jobsearch_plugin_options['emp_det_contact_form'] ) ? $jobsearch_plugin_options['emp_det_contact_form'] : '';
											if ( $emp_det_contact_form == 'on' ) {
												ob_start();
												?>

                                                <div class="widget widget_contact_form">
													<?php
													$cnt_counter = rand( 1000000, 9999999 );
													?>
                                                    <div class="jobsearch-widget-title">
                                                        <h2><?php esc_html_e( 'Contact Form', 'wp-jobsearch' ) ?></h2>
                                                    </div>
                                                    <form id="ct-form-<?php echo absint( $cnt_counter ) ?>"
                                                          data-uid="<?php echo absint( $user_id ) ?>" method="post">
                                                        <ul>
                                                            <li>
                                                                <label><?php esc_html_e( 'User Name:', 'wp-jobsearch' ) ?></label>
                                                                <input name="u_name"
                                                                       placeholder="<?php esc_html_e( 'Enter Your Name', 'wp-jobsearch' ) ?>"
                                                                       type="text">
                                                                <i class="jobsearch-icon jobsearch-user"></i>
                                                            </li>
                                                            <li>
                                                                <label><?php esc_html_e( 'Email Address:', 'wp-jobsearch' ) ?></label>
                                                                <input name="u_email"
                                                                       placeholder="<?php esc_html_e( 'Enter Your Email Address', 'wp-jobsearch' ) ?>"
                                                                       type="text">
                                                                <i class="jobsearch-icon jobsearch-mail"></i>
                                                            </li>
                                                            <li>
                                                                <label><?php esc_html_e( 'Phone Number:', 'wp-jobsearch' ) ?></label>
                                                                <input name="u_number"
                                                                       placeholder="<?php esc_html_e( 'Enter Your Phone Number', 'wp-jobsearch' ) ?>"
                                                                       type="text">
                                                                <i class="jobsearch-icon jobsearch-technology"></i>
                                                            </li>
                                                            <li>
                                                                <label><?php esc_html_e( 'Message:', 'wp-jobsearch' ) ?></label>
                                                                <textarea name="u_msg"
                                                                          placeholder="<?php esc_html_e( 'Type Your Message here', 'wp-jobsearch' ) ?>"></textarea>
                                                            </li>
															<?php
															if ( $captcha_switch == 'on' ) {
																wp_enqueue_script( 'jobsearch_google_recaptcha' );
																?>
                                                                <li>
                                                                    <script>
                                                                        var recaptcha_empl_contact;
                                                                        var jobsearch_multicap = function () {
                                                                            //Render the recaptcha_empl_contact on the element with ID "recaptcha_empl_contact"
                                                                            recaptcha_empl_contact = grecaptcha.render('recaptcha_empl_contact', {
                                                                                'sitekey': '<?php echo( $jobsearch_sitekey ); ?>', //Replace this with your Site key
                                                                                'theme': 'light'
                                                                            });
                                                                        };
                                                                        jQuery(document).ready(function () {
                                                                            jQuery('.recaptcha-reload-a').click();
                                                                        });
                                                                    </script>
                                                                    <div class="recaptcha-reload"
                                                                         id="recaptcha_empl_contact_div">
																		<?php echo jobsearch_recaptcha( 'recaptcha_empl_contact' ); ?>
                                                                    </div>
                                                                </li>
																<?php
															}
															?>
                                                            <li>
																<?php
																jobsearch_terms_and_con_link_txt();
																?>
                                                                <input type="submit" class="jobsearch-employer-ct-form"
                                                                       data-id="<?php echo absint( $cnt_counter ) ?>"
                                                                       value="<?php esc_html_e( 'Send now', 'wp-jobsearch' ) ?>">
																<?php
																$cnt__emp_wout_log = isset( $jobsearch_plugin_options['emp_cntct_wout_login'] ) ? $jobsearch_plugin_options['emp_cntct_wout_login'] : '';
																if ( ! is_user_logged_in() && $cnt__emp_wout_log != 'on' ) {
																	?>
                                                                    <a class="jobsearch-open-signin-tab"
                                                                       style="display: none;"><?php esc_html_e( 'login', 'wp-jobsearch' ) ?></a>
																	<?php
																}
																?>
                                                            </li>
                                                        </ul>
                                                        <span class="jobsearch-ct-msg"></span>
                                                    </form>
                                                </div>
												<?php
												$emp_cntct_form = ob_get_clean();
												echo apply_filters( 'jobsearch_employer_detail_cntct_frm_html', $emp_cntct_form, $employer_id );
											}
											//map
											$map_switch_arr = isset( $jobsearch_plugin_options['jobsearch-detail-map-switch'] ) ? $jobsearch_plugin_options['jobsearch-detail-map-switch'] : '';
											$employer_map   = false;
											if ( isset( $map_switch_arr ) && is_array( $map_switch_arr ) && sizeof( $map_switch_arr ) > 0 ) {
												foreach ( $map_switch_arr as $map_switch ) {
													if ( $map_switch == 'employer' ) {
														$employer_map = true;
													}
												}
											}
											if ( $employer_map ) {
												?>
                                                <div class="widget jobsearch_widget_map">
													<?php
													jobsearch_google_map_with_directions( $employer_id );
													?>
                                                </div>
											<?php } ?>

                                        </aside>

                                    </div>
                                </div>
                            </div>
                            <!-- Main Section -->

                        </div>
                    </div>


                    <!--- job tab -->
                    <div class="tab-pane fade" id="nav-job" role="tabpanel" aria-labelledby="nav-job-tab">
                        <div class="jobsearch-main-content">

                            <!-- Main Section -->
                            <div class="jobsearch-main-section">

                                <div class="jobsearch-plugin-default-container">
                                    <div class="jobsearch-row">
										<?php
										while ( have_posts() ) : the_post();
											$post_id = $post->ID;

											$rand_num = rand( 1000000, 99999999 );


											$job_apply_type = get_post_meta( $post_id, 'jobsearch_field_job_apply_type', true );

											$post_thumbnail_id             = jobsearch_job_get_profile_image( $post_id );
											$post_thumbnail_image          = wp_get_attachment_image_src( $post_thumbnail_id, 'jobsearch-job-medium' );
											$post_thumbnail_src            = isset( $post_thumbnail_image[0] ) && esc_url( $post_thumbnail_image[0] ) != '' ? $post_thumbnail_image[0] : '';
											$post_thumbnail_src            = $post_thumbnail_src == '' ? jobsearch_no_image_placeholder() : $post_thumbnail_src;
											$application_deadline          = get_post_meta( $post_id, 'jobsearch_field_job_application_deadline_date', true );
											$jobsearch_job_posted          = get_post_meta( $post_id, 'jobsearch_field_job_publish_date', true );
											$jobsearch_job_posted_ago      = jobsearch_time_elapsed_string( $jobsearch_job_posted, ' ' . esc_html__( 'posted', 'wp-jobsearch' ) . ' ' );
											$jobsearch_job_posted_formated = '';
											if ( $jobsearch_job_posted != '' ) {
												$jobsearch_job_posted_formated = date_i18n( get_option( 'date_format' ), ( $jobsearch_job_posted ) );
											}
											$get_job_location = get_post_meta( $post_id, 'jobsearch_field_location_address', true );

											//
											$postby_emp_id = get_post_meta( $job_id, 'jobsearch_field_job_posted_by', true );


											$job_city_title = '';
											$get_job_city   = get_post_meta( $post_id, 'jobsearch_field_location_location3', true );
											if ( $get_job_city == '' ) {
												$get_job_city = get_post_meta( $post_id, 'jobsearch_field_location_location2', true );
											}
											if ( $get_job_city != '' ) {
												$get_job_country = get_post_meta( $post_id, 'jobsearch_field_location_location1', true );
											}

											$job_city_tax = $get_job_city != '' ? get_term_by( 'slug', $get_job_city, 'job-location' ) : '';
											if ( is_object( $job_city_tax ) ) {
												$job_city_title = isset( $job_city_tax->name ) ? $job_city_tax->name : '';

												$job_country_tax = $get_job_country != '' ? get_term_by( 'slug', $get_job_country, 'job-location' ) : '';
												if ( is_object( $job_country_tax ) ) {
													$job_city_title .= isset( $job_country_tax->name ) ? ', ' . $job_country_tax->name : '';
												}
											} else if ( $job_city_title == '' ) {
												$get_job_country = get_post_meta( $post_id, 'jobsearch_field_location_location1', true );
												$job_country_tax = $get_job_country != '' ? get_term_by( 'slug', $get_job_country, 'job-location' ) : '';
												if ( is_object( $job_country_tax ) ) {
													$job_city_title .= isset( $job_country_tax->name ) ? $job_country_tax->name : '';
												}
											}

											if ( $job_city_title != '' && $get_job_location == '' ) {
												$get_job_location = $job_city_title;
											}

											//
											$sectors_enable_switch = isset( $jobsearch_plugin_options['sectors_onoff_switch'] ) ? $jobsearch_plugin_options['sectors_onoff_switch'] : '';

											$job_date            = get_post_meta( $post_id, 'jobsearch_field_job_date', true );
											$job_views_count     = get_post_meta( $post_id, 'jobsearch_job_views_count', true );
											$job_type_str        = jobsearch_job_get_all_jobtypes( $post_id, 'jobsearch-jobdetail-type', '', '', '<small>', '</small>' );
											$sector_str          = jobsearch_job_get_all_sectors( $post_id, '', ' ' . esc_html__( 'in', 'wp-jobsearch' ) . ' ', '', '<small class="post-in-category">', '</small>' );
											$company_name        = jobsearch_job_get_company_name( $post_id, '' );
											$skills_list         = jobsearch_job_get_all_skills( $post_id );
											$job_obj             = get_post( $post_id );
											$job_content         = isset( $job_obj->post_content ) ? $job_obj->post_content : '';
											$job_content         = apply_filters( 'the_content', $job_content );
											$job_salary          = jobsearch_job_offered_salary( $post_id );
											$job_applicants_list = get_post_meta( $post_id, 'jobsearch_job_applicants_list', true );
											$job_applicants_list = jobsearch_is_post_ids_array( $job_applicants_list, 'candidate' );
											$job_field_user      = get_post_meta( $job_id, 'jobsearch_field_job_posted_by', true );
											if ( empty( $job_applicants_list ) ) {
												$job_applicants_list = array();
											}
											$job_applicants_count = ! empty( $job_applicants_list ) ? count( $job_applicants_list ) : 0;
											?>
                                            <!-- Job Detail List -->
                                            <div class="jobsearch-column-12">
                                                <div class="jobsearch-typo-wrap">
                                                    <figure class="jobsearch-jobdetail-list">

														<?php if ( $post_thumbnail_src != '' ) { ?>
                                                            <span class="jobsearch-jobdetail-listthumb">
                                        <?php jobsearch_empjobs_urgent_pkg_iconlab( $postby_emp_id, $job_id ) ?>
                                                                <img src="<?php echo esc_url( $post_thumbnail_src ) ?>"
                                                                     alt="">
                                    </span>
														<?php } ?>

                                                        <figcaption>
                                                            <h2><?php echo force_balance_tags( get_the_title() ); ?></h2>
															<?php
															ob_start();
															?>
                                                            <span>
                                        <?php
                                        if ( $job_type_str != '' ) {
	                                        echo force_balance_tags( $job_type_str );
                                        }
                                        if ( $company_name != '' ) {
	                                        echo force_balance_tags( $company_name );
                                        }
                                        if ( $jobsearch_job_posted_ago != '' && $job_views_publish_date == 'on' ) {
	                                        ?>
                                            <small class="jobsearch-jobdetail-postinfo"><?php echo esc_html( $jobsearch_job_posted_ago ); ?></small>
	                                        <?php
                                        }

                                        if ( $sectors_enable_switch == 'on' ) {
	                                        echo apply_filters( 'jobsearch_jobdetail_sector_str_html', $sector_str, $job_id );
                                        }
                                        ?>
                                    </span>
                                                            <ul class="jobsearch-jobdetail-options">
																<?php
																if ( ! empty( $get_job_location ) && $all_location_allow == 'on' ) {
																	$google_mapurl = 'https://www.google.com/maps/search/' . $get_job_location;
																	?>
                                                                    <li>
                                                                        <i class="fa fa-map-marker"></i> <?php echo esc_html( $get_job_location ); ?>
                                                                        <a href="<?php echo esc_url( $google_mapurl ); ?>"
                                                                           target="_blank"
                                                                           class="jobsearch-jobdetail-view"><?php echo esc_html__( 'View on Map', 'wp-jobsearch' ) ?></a>
                                                                    </li>
																	<?php
																}
																if ( $jobsearch_job_posted_formated != '' && $job_views_publish_date == 'on' ) {
																	?>
                                                                    <li>
                                                                        <i class="jobsearch-icon jobsearch-calendar"></i> <?php echo esc_html__( 'Post Date', 'wp-jobsearch' ) ?>
                                                                        : <?php echo esc_html( $jobsearch_job_posted_formated ); ?>
                                                                    </li>
																	<?php
																}
																$jobsearch_last_date_formated = '';
																if ( $application_deadline != '' ) {
																	$jobsearch_last_date_formated = date_i18n( get_option( 'date_format' ), ( $application_deadline ) );
																}
																if ( isset( $jobsearch_last_date_formated ) && ! empty( $jobsearch_last_date_formated ) ) {
																	?>
                                                                    <li>
                                                                    <i class="careerfy-icon careerfy-calendar"></i> <?php echo esc_html__( 'Apply Before ', 'wp-jobsearch' ); ?>
                                                                    : <?php echo esc_html( $jobsearch_last_date_formated ); ?>
                                                                    </li><?php
																}
																if ( $job_salary != '' ) {
																	?>
                                                                    <li>
                                                                        <i class="fa fa-money"></i> <?php printf( esc_html__( 'Salary: %s', 'wp-jobsearch' ), $job_salary ) ?>
                                                                    </li>
																	<?php
																}

																if ( isset( $job_apply_type ) && $job_apply_type != 'external' ) {
																	?>
                                                                    <li>
                                                                        <i class="jobsearch-icon jobsearch-summary"></i> <?php printf( esc_html__( 'Applications %s', 'wp-jobsearch' ), $job_applicants_count ) ?>
                                                                    </li>
																<?php } ?>
                                                                <li>
                                                                    <a><i class="jobsearch-icon jobsearch-view"></i> <?php echo esc_html__( 'View(s)', 'wp-jobsearch' ) ?> <?php echo absint( $job_views_count ); ?>
                                                                    </a></li>
                                                            </ul>
															<?php
															// wrap in this this due to enquire arrange button style.
															$before_label   = esc_html__( 'Shortlist', 'careerfy' );
															$after_label    = esc_html__( 'Shortlisted', 'careerfy' );
															$book_mark_args = array(
																'before_label' => $before_label,
																'after_label'  => $after_label,
																'before_icon'  => 'careerfy-icon careerfy-add-list',
																'after_icon'   => 'careerfy-icon careerfy-add-list',
																'anchor_class' => 'careerfy-jobdetail-btn active',
																'view'         => 'job_detail_3',
																'job_id'       => $job_id,
															);
															do_action( 'jobsearch_job_shortlist_button_frontend', $book_mark_args );

															$popup_args = array(
																'job_id' => $job_id,
															);
															do_action( 'jobsearch_job_send_to_email_filter', $popup_args );

															//
															if ( $social_share_allow == 'on' ) {
																wp_enqueue_script( 'jobsearch-addthis' );
																?>
                                                                <ul class="jobsearch-jobdetail-media">
                                                                    <li>
                                                                        <span><?php esc_html_e( 'Share:', 'wp-jobsearch' ) ?></span>
                                                                    </li>
                                                                    <li><a href="javascript:void(0);"
                                                                           data-original-title="facebook"
                                                                           class="jobsearch-icon jobsearch-facebook-logo-in-circular-button-outlined-social-symbol addthis_button_facebook"></a>
                                                                    </li>
                                                                    <li><a href="javascript:void(0);"
                                                                           data-original-title="twitter"
                                                                           class="jobsearch-icon jobsearch-twitter-circular-button addthis_button_twitter"></a>
                                                                    </li>
                                                                    <li><a href="javascript:void(0);"
                                                                           data-original-title="linkedin"
                                                                           class="jobsearch-icon jobsearch-linkedin addthis_button_linkedin"></a>
                                                                    </li>
                                                                    <li><a href="javascript:void(0);"
                                                                           data-original-title="share_more"
                                                                           class="jobsearch-icon jobsearch-plus addthis_button_compact"></a>
                                                                    </li>
                                                                </ul>
																<?php
															}
															$job_info_output = ob_get_clean();
															echo apply_filters( 'jobsearch_job_detail_content_info', $job_info_output, $job_id );
															?>
                                                        </figcaption>
                                                    </figure>
                                                </div>
                                            </div>
                                            <!-- Job Detail List -->

                                            <!-- Job Detail Content -->
                                            <div class="jobsearch-column-8 jobsearch-typo-wrap">

                                                <div class="jobsearch-jobdetail-content">
													<?php
													ob_start();
													$cus_fields = array( 'content' => '' );
													$cus_fields = apply_filters( 'jobsearch_custom_fields_list', 'job', $post_id, $cus_fields, '<li class="jobsearch-column-4">', '</li>' );
													if ( isset( $cus_fields['content'] ) && $cus_fields['content'] != '' ) {
														?>
                                                        <div class="jobsearch-content-title">
                                                            <h2><?php echo esc_html__( 'Job Detail', 'wp-jobsearch' ) ?></h2>
                                                        </div>
                                                        <div class="jobsearch-jobdetail-services">
                                                            <ul class="jobsearch-row">
																<?php
																// All custom fields with value
																echo force_balance_tags( $cus_fields['content'] );
																?>
                                                            </ul>
                                                        </div>
														<?php
													}
													$job_fields_output = ob_get_clean();
													echo apply_filters( 'jobsearch_job_detail_content_fields', $job_fields_output, $job_id );
													//
													if ( $job_content != '' ) {
														ob_start();
														?>
                                                        <div class="jobsearch-content-title">
                                                            <h2><?php echo esc_html__( 'Job Description', 'wp-jobsearch' ) ?></h2>
                                                        </div>
                                                        <div class="jobsearch-description">
															<?php
															echo force_balance_tags( $job_content );
															?>
                                                        </div>
														<?php
														$job_det_output = ob_get_clean();
														echo apply_filters( 'jobsearch_job_detail_content_detail', $job_det_output, $job_id );
													}
													do_action( 'jobsearch_job_detail_after_description', $job_id );
													$job_attachments_switch = isset( $jobsearch_plugin_options['job_attachments'] ) ? $jobsearch_plugin_options['job_attachments'] : '';
													if ( $job_attachments_switch == 'on' ) {
														$all_attach_files = get_post_meta( $job_id, 'jobsearch_field_job_attachment_files', true );
														if ( ! empty( $all_attach_files ) ) {
															?>
                                                            <div class="jobsearch-content-title">
                                                                <h2><?php esc_html_e( 'Attached Files', 'wp-jobsearch' ) ?></h2>
                                                            </div>
                                                            <div class="jobsearch-file-attach-sec">
                                                                <ul class="jobsearch-row">
																	<?php
																	foreach ( $all_attach_files as $_attach_file ) {
																		$_attach_id    = jobsearch_get_attachment_id_from_url( $_attach_file );
																		$_attach_post  = get_post( $_attach_id );
																		$_attach_mime  = isset( $_attach_post->post_mime_type ) ? $_attach_post->post_mime_type : '';
																		$_attach_guide = isset( $_attach_post->guid ) ? $_attach_post->guid : '';
																		$attach_name   = basename( $_attach_guide );

																		$file_icon = 'fa fa-file-text-o';
																		if ( $_attach_mime == 'image/png' || $_attach_mime == 'image/jpeg' ) {
																			$file_icon = 'fa fa-file-image-o';
																		} else if ( $_attach_mime == 'application/msword' || $_attach_mime == 'application/vnd.openxmlformats-officedocument.wordprocessingml.document' ) {
																			$file_icon = 'fa fa-file-word-o';
																		} else if ( $_attach_mime == 'application/vnd.ms-excel' || $_attach_mime == 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' ) {
																			$file_icon = 'fa fa-file-excel-o';
																		} else if ( $_attach_mime == 'application/pdf' ) {
																			$file_icon = 'fa fa-file-pdf-o';
																		}
																		?>
                                                                        <li class="jobsearch-column-4">
                                                                            <div class="file-container">
                                                                                <a href="<?php echo( $_attach_file ) ?>"
                                                                                   download="<?php echo( $attach_name ) ?>"
                                                                                   class="file-download-icon"><i
                                                                                            class="<?php echo( $file_icon ) ?>"></i> <?php echo( $attach_name ) ?>
                                                                                </a>
                                                                                <a href="<?php echo( $_attach_file ) ?>"
                                                                                   download="<?php echo( $attach_name ) ?>"
                                                                                   class="file-download-btn"><?php esc_html_e( 'Download', 'wp-jobsearch' ) ?>
                                                                                    <i class="jobsearch-icon jobsearch-download-arrow"></i></a>
                                                                            </div>
                                                                        </li>
																		<?php
																	}
																	?>
                                                                </ul>
                                                            </div>
															<?php
														}
													}
													do_action( 'jobsearch_job_detail_before_skills', $job_id );
													if ( $skills_list != '' ) {
														ob_start();
														?>
                                                        <div class="jobsearch-content-title">
                                                            <h2><?php echo esc_html__( 'Required skills', 'wp-jobsearch' ) ?></h2>
                                                        </div>
                                                        <div class="jobsearch-jobdetail-tags">
															<?php echo force_balance_tags( $skills_list ); ?>
                                                        </div>
														<?php
														$job_skills_output = ob_get_clean();
														echo apply_filters( 'jobsearch_job_detail_content_skills', $job_skills_output, $job_id );
													}
													?>
                                                </div>
												<?php
												echo apply_filters( 'jobsearch_job_defdetail_after_detcont_html', '', $job_id );
												//
												$related_job_html = jobsearch_job_related_post( $post_id, esc_html__( 'Other jobs you may like', 'wp-jobsearch' ), 5, 5, 'jobsearch-job-like' );
												echo apply_filters( 'jobsearch_job_detail_content_related', $related_job_html, $job_id );
												?>
                                            </div>
											<?php
										endwhile;
										wp_reset_postdata();
										?>
                                        <!-- Job Detail Content -->
                                        <!-- Job Detail SideBar -->
                                        <aside class="jobsearch-column-4 jobsearch-typo-wrap">

											<?php
											echo apply_filters( 'jobsearch_job_detail_sidebar_bef4_apply', '', $job_id );
											ob_start();
											?>
                                            <div class="widget widget_apply_job">
												<?php
												ob_start();
												?>
                                                <div class="widget_apply_job_wrap">
													<?php
													$current_date = strtotime( current_time( 'd-m-Y H:i:s' ) );

													if ( $application_deadline != '' && $application_deadline <= $current_date ) {
														?>
                                                        <span class="deadline-closed"><?php esc_html_e( 'Application deadline closed.', 'wp-jobsearch' ); ?></span>
														<?php
													} else {
														$arg              = array(
															'classes'           => 'jobsearch-applyjob-btn',
															'btn_before_label'  => esc_html__( 'Apply for the job', 'wp-jobsearch' ),
															'btn_after_label'   => esc_html__( 'Successfully Applied', 'wp-jobsearch' ),
															'btn_applied_label' => esc_html__( 'Applied', 'wp-jobsearch' ),
															'job_id'            => $job_id
														);
														$apply_filter_btn = apply_filters( 'jobsearch_job_applications_btn', '', $arg );
														echo( $apply_filter_btn );
													}

													$job_apply_deadline_sw = isset( $jobsearch_plugin_options['job_appliction_deadline'] ) ? $jobsearch_plugin_options['job_appliction_deadline'] : '';

													if ( $job_apply_deadline_sw == 'on' && $application_deadline != '' && $application_deadline > $current_date ) {
														$creat_date  = date( 'Y-m-d H:i:s', $application_deadline );
														$creat_date  = date_create( $creat_date );
														$creat_date2 = date( 'Y-m-d H:i:s', $current_date );
														$creat_date2 = date_create( $creat_date2 );
														$date_diff   = date_diff( $creat_date, $creat_date2 );

														$date_diff = json_decode( json_encode( $date_diff ), true );

														$app_deadline_rtime = '';
														$app_deadline_rtime .= ( isset( $date_diff['y'] ) && $date_diff['y'] > 0 ) ? ( ' ' . $date_diff['y'] . esc_html__( 'y', 'wp-jobsearch' ) ) : '';
														$app_deadline_rtime .= isset( $date_diff['m'] ) && $date_diff['m'] > 0 ? ' ' . $date_diff['m'] . esc_html__( 'm', 'wp-jobsearch' ) : '';
														$app_deadline_rtime .= isset( $date_diff['d'] ) && $date_diff['d'] > 0 ? ' ' . $date_diff['d'] . esc_html__( 'd', 'wp-jobsearch' ) : '';
														$app_deadline_rtime .= isset( $date_diff['h'] ) && $date_diff['h'] > 0 ? ' ' . $date_diff['h'] . esc_html__( 'h', 'wp-jobsearch' ) : '';
														$app_deadline_rtime .= isset( $date_diff['i'] ) && $date_diff['i'] > 0 ? ' ' . $date_diff['i'] . esc_html__( 'min', 'wp-jobsearch' ) : '';
														?>
                                                        <span><?php printf( esc_html__( 'Application ends in %s', 'wp-jobsearch' ), $app_deadline_rtime ) ?></span>
														<?php
													}
													$facebook_login      = isset( $jobsearch_plugin_options['facebook-social-login'] ) ? $jobsearch_plugin_options['facebook-social-login'] : '';
													$linkedin_login      = isset( $jobsearch_plugin_options['linkedin-social-login'] ) ? $jobsearch_plugin_options['linkedin-social-login'] : '';
													$google_social_login = isset( $jobsearch_plugin_options['google-social-login'] ) ? $jobsearch_plugin_options['google-social-login'] : '';

													if ( $application_deadline != '' && $application_deadline <= $current_date ) {
														// check for social apply in case
														// job deadline is passed
													} else {
														$apply_social_platforms = isset( $jobsearch_plugin_options['apply_social_platforms'] ) ? $jobsearch_plugin_options['apply_social_platforms'] : '';
														if ( ! is_user_logged_in() && ( $facebook_login == 'on' || $linkedin_login == 'on' || $google_social_login == 'on' ) && ! empty( $apply_social_platforms ) ) {
															?>
                                                            <div class="jobsearch-applywith-title">
                                                                <small><?php echo esc_html__( 'OR apply with', 'wp-jobsearch' ) ?></small>
                                                            </div>
                                                            <p><?php echo esc_html__( 'An easy way to apply for this job. Use the following social media.', 'wp-jobsearch' ) ?></p>
                                                            <ul>
																<?php
																$apply_args = array(
																	'job_id' => $job_id
																);
																if ( in_array( 'facebook', $apply_social_platforms ) ) {
																	do_action( 'jobsearch_apply_job_with_fb', $apply_args );
																}
																if ( in_array( 'linkedin', $apply_social_platforms ) ) {
																	do_action( 'jobsearch_apply_job_with_linkedin', $apply_args );
																}
																if ( in_array( 'google', $apply_social_platforms ) ) {
																	do_action( 'jobsearch_apply_job_with_google', $apply_args );
																}
																?>
                                                            </ul>
                                                            <span class="apply-msg" style="display: none;"></span>
															<?php
														}
													}
													?>
                                                </div>
												<?php
												$apply_bbox = ob_get_clean();
												echo apply_filters( 'jobsearch_job_defdet_applybtn_boxhtml', $apply_bbox, $job_id );

												//
												$popup_args = array(
													'job_employer_id' => $job_employer_id,
													'job_id'          => $job_id,
												);
												$popup_html = apply_filters( 'jobsearch_job_send_message_html_filter', '', $popup_args );
												echo force_balance_tags( $popup_html );
												?>
                                            </div>
											<?php
											$sidebar_apply_output = ob_get_clean();
											echo apply_filters( 'jobsearch_job_detail_sidebar_apply_btns', $sidebar_apply_output, $job_id );
											// map
											$map_switch_arr = isset( $jobsearch_plugin_options['jobsearch-detail-map-switch'] ) ? $jobsearch_plugin_options['jobsearch-detail-map-switch'] : '';
											$job_map        = false;
											if ( isset( $map_switch_arr ) && is_array( $map_switch_arr ) && sizeof( $map_switch_arr ) > 0 ) {
												foreach ( $map_switch_arr as $map_switch ) {
													if ( $map_switch == 'job' ) {
														$job_map = true;
													}
												}
											}
											if ( $job_map ) {
												?>
                                                <div class="widget jobsearch_widget_map">
													<?php jobsearch_google_map_with_directions( $job_id ); ?>
                                                </div>
												<?php
											}
											$company_job_html = jobsearch_job_related_company_post( $post_id, esc_html__( 'More Jobs From ', 'wp-jobsearch' ) . get_the_title( $job_field_user ), 3 );

											echo force_balance_tags( $company_job_html );
											?>
                                        </aside>
                                        <!-- Job Detail SideBar -->
                                    </div>
                                </div>
                            </div>
                            <!-- Main Section -->
                        </div>

                    </div>
                </div>

            </div>
        </div>
            <div class="container-sidebar">
                <ul class="sidebar-menu">
                    <li>
                        <div class="project-status aplicat">
							<?php esc_html_e( 'Deja ai aplicat la acest Job', 'managero' ); ?>
                        </div>
                    </li>
                    <li>
                        <select name="jobstatus" id="statusJob">
                            <option value="">Alege o optiune</option>
                            <option value="1">Aplic mai tarziu</option>
                            <option value="2">Am aplicat extern</option>
                        </select>
                    </li>
                    <li>
                        <div class="back-btn">
							<?php esc_html_e( 'Inapoi la joburi', 'managero' ); ?>
                        </div>
                    </li>
                </ul>

            </div>

    </div>


    <script>
        //for login popup
        jQuery(document).on('click', '.jobsearch-sendmessage-popup-btn', function () {
            jobsearch_modal_popup_open('JobSearchModalSendMessage');
        });
        jQuery(document).on('click', '.jobsearch-sendmessage-messsage-popup-btn', function () {
            jobsearch_modal_popup_open('JobSearchModalSendMessageWarning');
        });
        jQuery(document).on('click', '.jobsearch-applyjob-msg-popup-btn', function () {
            jobsearch_modal_popup_open('JobSearchModalApplyJobWarning');
        });
    </script>
    <!-- Main Content -->
<?php
jobsearch_google_job_posting( $job_id );
$dethtml = ob_get_clean();
echo apply_filters( 'jobsearch_job_detail_pagehtml', $dethtml );
