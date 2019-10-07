<?php
global $jobsearch_plugin_options, $Jobsearch_User_Dashboard_Settings;
$user_id  = get_current_user_id();
$user_obj = get_user_by( 'ID', $user_id );

$page_id = $user_dashboard_page = isset( $jobsearch_plugin_options['user-dashboard-template-page'] ) ? $jobsearch_plugin_options['user-dashboard-template-page'] : '';
$page_id = $user_dashboard_page = jobsearch__get_post_id( $user_dashboard_page, 'page' );

$page_url = jobsearch_wpml_lang_page_permalink( $page_id, 'page' ); //get_permalink($page_id);

//var_dump($page_url);

$all_location_allow = isset( $jobsearch_plugin_options['all_location_allow'] ) ? $jobsearch_plugin_options['all_location_allow'] : '';

if ( jobsearch_user_isemp_member( $user_id ) ) {
	$employer_id = jobsearch_user_isemp_member( $user_id );
} else {
	$employer_id = jobsearch_get_user_employer_id( $user_id );
}

$reults_per_page = isset( $jobsearch_plugin_options['user-dashboard-per-page'] ) && $jobsearch_plugin_options['user-dashboard-per-page'] > 0 ? $jobsearch_plugin_options['user-dashboard-per-page'] : 10;

$page_num = isset( $_GET['page_num'] ) ? $_GET['page_num'] : 1;
if ( $employer_id > 0 ) {
	$args        = array(
		'post_type'      => 'package',
		'posts_per_page' => - 1,
		'post_status'    => 'publish',
		'fields'         => 'ids',
		'order'          => 'ASC',
		'orderby'        => 'title',
		'meta_query'     => array(
		),
	);
	$fpkgs_query = new WP_Query( $args );
	wp_reset_postdata();

	$args = array(
		'post_type'      => 'job',
		'posts_per_page' => $reults_per_page,
		'paged'          => $page_num,
		'post_status'    => array( 'publish', 'draft' ),
		'order'          => 'DESC',
		'orderby'        => 'ID',
		'meta_query'     => array(
						array(
				'key'     => 'jobsearch_field_job_status',
				'value'   => 'approved',
				'compare' => '=',
			),
		),
	);

	if ( isset( $_GET['keyword'] ) && $_GET['keyword'] != '' ) {
		$args['s'] = sanitize_text_field( $_GET['keyword'] );
	}

	$jobs_query = new WP_Query( $args );

//	print_r($jobs_query);
	$total_jobs = $jobs_query->found_posts;

//	nice_print_r($total_jobs);

	?>
    <div class="jobsearch-employer-dasboard">
        <div class="jobsearch-employer-box-section">

            <div class="jobsearch-profile-title">
                <h2><?php echo apply_filters( 'jobsearch_emp_dash_manage_jobs_maintitle', esc_html__( 'Manage Jobs', 'wp-jobsearch' ) ) ?></h2>
				<?php
				if ( $jobs_query->have_posts() ) {
					?>
                    <form method="get" class="jobsearch-employer-search" action="<?php echo( $page_url ) ?>">
                        <input type="hidden" name="tab" value="lista-joburi">
                        <input placeholder="<?php esc_html_e( 'Search job', 'wp-jobsearch' ) ?>" name="keyword"
                               type="text"
                               value="<?php echo( isset( $_GET['keyword'] ) ? $_GET['keyword'] : '' ) ?>">
                        <input type="submit" value="">
                        <i class="jobsearch-icon jobsearch-search"></i>
                    </form>
					<?php
				}
				?>
            </div>
			<?php
			$free_jobs_allow = isset( $jobsearch_plugin_options['free-jobs-allow'] ) ? $jobsearch_plugin_options['free-jobs-allow'] : '';
			if ( $jobs_query->have_posts() ) {
				?>
                <script>
                    jQuery(function () {
                        jQuery('.jobsearch-fill-the-job').tooltip();
                    });
                </script>


                <div class="jobsearch-jobs-list-holder">
                    <div class="jobsearch-managejobs-list">
                        <div class="candidat-fisiere">
                            <div class="visual-header">
                                <h3>Anunturi active</h3>
                                <a href="javascript:void(0);" class="edit-preview list-edit" data-field="editJobList">editeaza
                                    sectiunea</a>
                            </div>

                            <div class="file-wrap2">
                                <table id="tabel-fisiere1">
                                    <tr>
                                        <th>Post</th>
                                        <th>Noi</th>
                                        <th>Ok</th>
                                        <th>Rezerve</th>
                                        <th>Respinsi</th>
                                        <th class="editJobList">Actiuni</th>
                                    </tr>
									<?php
									while ( $jobs_query->have_posts() ) : $jobs_query->the_post();

									    $job_id = get_the_ID();


										$job_is_feature = get_post_meta( $job_id, 'jobsearch_field_job_featured', true );

										$job_applicants_list = get_post_meta( $job_id, 'jobsearch_job_applicants_list', true );
										$job_applicants_list = jobsearch_is_post_ids_array( $job_applicants_list, 'candidate' );
										if ( empty( $job_applicants_list ) ) {
											$job_applicants_list = array();
										}

										$_job_id              = $job_id;
										$job_applicants_count = ! empty( $job_applicants_list ) ? count( $job_applicants_list ) : 0;


										$job_short_int_list = get_post_meta( $_job_id, '_job_short_interview_list', true );
										$job_short_int_list = $job_short_int_list != '' ? explode( ',', $job_short_int_list ) : '';
										if ( empty( $job_short_int_list ) ) {
											$job_short_int_list = array();
										}
										$job_short_int_list   = jobsearch_is_post_ids_array( $job_short_int_list, 'candidate' );
										$job_short_int_list_c = ! empty( $job_short_int_list ) ? count( $job_short_int_list ) : 0;


										$job_reject_int_list = get_post_meta( $_job_id, '_job_reject_interview_list', true );
										$job_reject_int_list = $job_reject_int_list != '' ? explode( ',', $job_reject_int_list ) : '';
										if ( empty( $job_reject_int_list ) ) {
											$job_reject_int_list = array();
										}
										$job_reject_int_list   = jobsearch_is_post_ids_array( $job_reject_int_list, 'candidate' );
										$job_reject_int_list_c = ! empty( $job_reject_int_list ) ? count( $job_reject_int_list ) : 0;


										$job_reserved_int_list = get_post_meta( $_job_id, '_job_reserved_interview_list', true );
										$job_reserved_int_list = $job_reserved_int_list != '' ? explode( ',', $job_reserved_int_list ) : '';
										if ( empty( $job_reserved_int_list ) ) {
											$job_reserved_int_list = array();
										}
										$job_reserved_int_list   = jobsearch_is_post_ids_array( $job_reserved_int_list, 'candidate' );
										$job_reserved_int_list_c = ! empty( $job_reserved_int_list ) ? count( $job_reserved_int_list ) : 0;


										?>


                                        <tr>
                                            <td>
                                                <a href="<?=  get_permalink($job_id) ?>" target="_blank">
													<?php echo get_the_title() ?>
                                                </a>
                                            </td>
                                            <td><a <?php echo( 'href="' . add_query_arg( array(
														'tab'    => 'manage-jobs',
														'view'   => 'applicants',
														'job_id' => $job_id
													), $page_url ) . '"' ) ?>
                                                        class="jobsearch-managejobs-appli"><?= $job_applicants_count; ?></a>
                                            </td>
                                            <td><a <?php echo( 'href="' . add_query_arg( array(
														'tab'    => 'manage-jobs',
														'view'   => 'applicants',
														'job_id' => $job_id
													), $page_url ) . '&mod=shortlisted"' ) ?>
                                                        class="jobsearch-managejobs-appli2"><?= $job_short_int_list_c; ?></a>
                                            </td>
                                            <td><a <?php echo( 'href="' . add_query_arg( array(
														'tab'    => 'manage-jobs',
														'view'   => 'applicants',
														'job_id' => $job_id
													), $page_url ) . '&mod=reserved"' ) ?>
                                                        class="jobsearch-managejobs-appl4"><?= $job_reserved_int_list_c; ?></a>
                                            </td>
                                            <td><a <?php echo( 'href="' . add_query_arg( array(
														'tab'    => 'manage-jobs',
														'view'   => 'applicants',
														'job_id' => $job_id
													), $page_url ) . '&mod=shortlisted"' ) ?>
                                                        class="jobsearch-managejobs-appli3"><?= $job_reject_int_list_c; ?></a>
                                            </td>
                                            <td class="editJobList">
                                                <a href="<?php echo get_permalink( $job_id ) ?>"
                                                   class="jobsearch-icon jobsearch-view"></a>
                                                <a href="http://managero.ro/user-dashboard/?tab=job-edit&job_id=<?= $job_id; ?>"
                                                   class="jobsearch-icon jobsearch-edit"></a>

                                                <a href="javascript:void(0);" data-id="<?php echo( $job_id ) ?>"
                                                   class="jobsearch-icon jobsearch-rubbish jobsearch-trash-job"></a>
                                            </td>

                                        </tr>

										<?php
									//	$actions_html = ob_get_clean();
										echo apply_filters( 'jobsearch_empdash_managejobs_list_actions', $actions_html, $job_id, $page_url );
										?>

										<?php
									endwhile;
									wp_reset_postdata();
									?>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="jobsearch-jobs-list-holder">
                    <div class="jobsearch-managejobs-list">
                        <div class="candidat-fisiere">
                            <div class="visual-header">
                                <h3>Anunturi inactive</h3>
                                <a href="javascript:void(0);" class="edit-preview list-edit" data-field="editJobList2">editeaza
                                    sectiunea</a>
                            </div>

                            <div class="file-wrap2">
                                <table id="tabel-fisiere1">
                                    <tr>
                                        <th>Post</th>
                                        <th>Noi</th>
                                        <th>Ok</th>
                                        <th>Rezerve</th>
                                        <th>Respinsi</th>
                                        <th class="editJobList2">Actiuni</th>
                                    </tr>
									<?php

									$args2 = array(
										'post_type'      => 'job',
										'posts_per_page' => $reults_per_page,
										'paged'          => $page_num,
										'post_status'    => array( 'publish', 'draft' ),
										'order'          => 'DESC',
										'orderby'        => 'ID',
										'meta_query'     => array(
											array(
												'key'     => 'jobsearch_field_job_status',
												'value'   => 'pending ',
												'compare' => '=',
											),
										),
									);

									if ( isset( $_GET['keyword'] ) && $_GET['keyword'] != '' ) {
										$args2['s'] = sanitize_text_field( $_GET['keyword'] );
									}

									$jobs_query2 = new WP_Query( $args2 );


									while ( $jobs_query2->have_posts() ) : $jobs_query2->the_post();
										$job_id = get_the_ID();

										$status_job = get_field('status_job',$job_id);

										$sectors    = wp_get_post_terms( $job_id, 'sector' );
										$job_sector = isset( $sectors[0]->name ) ? $sectors[0]->name : '';

										$jobtypes = wp_get_post_terms( $job_id, 'jobtype' );
										$job_type = isset( $jobtypes[0]->term_id ) ? $jobtypes[0]->term_id : '';

										$get_job_location = get_post_meta( $job_id, 'jobsearch_field_location_address', true );

										$job_publish_date = get_post_meta( $job_id, 'jobsearch_field_job_publish_date', true );
										$job_expiry_date  = get_post_meta( $job_id, 'jobsearch_field_job_expiry_date', true );

										$job_filled = get_post_meta( $job_id, 'jobsearch_field_job_filled', true );

										$job_status = 'pending';
										$job_status = get_post_meta( $job_id, 'jobsearch_field_job_status', true );

										if ( $job_expiry_date != '' && $job_expiry_date <= strtotime( current_time( 'd-m-Y H:i:s', 1 ) ) ) {
											$job_status = 'expired';
										}

										$status_txt = '';
										if ( $job_status == 'pending' ) {
											$status_txt = esc_html__( 'Pending', 'wp-jobsearch' );
										} else if ( $job_status == 'expired' ) {
											$status_txt = esc_html__( 'Expired', 'wp-jobsearch' );
										} else if ( $job_status == 'canceled' ) {
											$status_txt = esc_html__( 'Canceled', 'wp-jobsearch' );
										} else if ( $job_status == 'approved' ) {
											$status_txt = esc_html__( 'Approved', 'wp-jobsearch' );
										} else if ( $job_status == 'admin-review' ) {
											$status_txt = esc_html__( 'Admin Review', 'wp-jobsearch' );
										}

										$job_is_feature = get_post_meta( $job_id, 'jobsearch_field_job_featured', true );

										$job_applicants_list = get_post_meta( $job_id, 'jobsearch_job_applicants_list', true );
										$job_applicants_list = jobsearch_is_post_ids_array( $job_applicants_list, 'candidate' );
										if ( empty( $job_applicants_list ) ) {
											$job_applicants_list = array();
										}

										$_job_id              = $job_id;
										$job_applicants_count = ! empty( $job_applicants_list ) ? count( $job_applicants_list ) : 0;


										$job_short_int_list = get_post_meta( $_job_id, '_job_short_interview_list', true );
										$job_short_int_list = $job_short_int_list != '' ? explode( ',', $job_short_int_list ) : '';
										if ( empty( $job_short_int_list ) ) {
											$job_short_int_list = array();
										}
										$job_short_int_list   = jobsearch_is_post_ids_array( $job_short_int_list, 'candidate' );
										$job_short_int_list_c = ! empty( $job_short_int_list ) ? count( $job_short_int_list ) : 0;


										$job_reject_int_list = get_post_meta( $_job_id, '_job_reject_interview_list', true );
										$job_reject_int_list = $job_reject_int_list != '' ? explode( ',', $job_reject_int_list ) : '';
										if ( empty( $job_reject_int_list ) ) {
											$job_reject_int_list = array();
										}
										$job_reject_int_list   = jobsearch_is_post_ids_array( $job_reject_int_list, 'candidate' );
										$job_reject_int_list_c = ! empty( $job_reject_int_list ) ? count( $job_reject_int_list ) : 0;


										$job_reserved_int_list = get_post_meta( $_job_id, '_job_reserved_interview_list', true );
										$job_reserved_int_list = $job_reserved_int_list != '' ? explode( ',', $job_reserved_int_list ) : '';
										if ( empty( $job_reserved_int_list ) ) {
											$job_reserved_int_list = array();
										}
										$job_reserved_int_list   = jobsearch_is_post_ids_array( $job_reserved_int_list, 'candidate' );
										$job_reserved_int_list_c = ! empty( $job_reserved_int_list ) ? count( $job_reserved_int_list ) : 0;


										?>


                                        <tr>
                                            <td>
                                                <a href="<?=  get_permalink($job_id) ?>" target="_blank">
													<?php echo get_the_title() ?>
                                                </a>
                                            </td>
                                            <td><a <?php echo( 'href="' . add_query_arg( array(
														'tab'    => 'manage-jobs',
														'view'   => 'applicants',
														'job_id' => $job_id
													), $page_url ) . '"' ) ?>
                                                        class="jobsearch-managejobs-appli"><?= $job_applicants_count; ?></a>
                                            </td>
                                            <td><a <?php echo( 'href="' . add_query_arg( array(
														'tab'    => 'manage-jobs',
														'view'   => 'applicants',
														'job_id' => $job_id
													), $page_url ) . '&mod=shortlisted"' ) ?>
                                                        class="jobsearch-managejobs-appli2"><?= $job_short_int_list_c; ?></a>
                                            </td>
                                            <td><a <?php echo( 'href="' . add_query_arg( array(
														'tab'    => 'manage-jobs',
														'view'   => 'applicants',
														'job_id' => $job_id
													), $page_url ) . '&mod=reserved"' ) ?>
                                                        class="jobsearch-managejobs-appl4"><?= $job_reserved_int_list_c; ?></a>
                                            </td>
                                            <td><a <?php echo( 'href="' . add_query_arg( array(
														'tab'    => 'manage-jobs',
														'view'   => 'applicants',
														'job_id' => $job_id
													), $page_url ) . '&mod=shortlisted"' ) ?>
                                                        class="jobsearch-managejobs-appli3"><?= $job_reject_int_list_c; ?></a>
                                            </td>
                                            <td class="editJobList2">
                                                <a href="<?php echo get_permalink( $job_id ) ?>"
                                                   class="jobsearch-icon jobsearch-view"></a>
                                                <a href="http://managero.ro/user-dashboard/?tab=job-edit&job_id=<?= $job_id; ?>"
                                                   class="jobsearch-icon jobsearch-edit"></a>

                                                <a href="javascript:void(0);" data-id="<?php echo( $job_id ) ?>"
                                                   class="jobsearch-icon jobsearch-rubbish jobsearch-trash-job"></a>
                                            </td>

                                        </tr>


										<?php
										$actions_html = ob_get_clean();
										echo apply_filters( 'jobsearch_empdash_managejobs_list_actions', $actions_html, $job_id, $page_url );
										?>

										<?php
									endwhile;
									wp_reset_postdata();
									?>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>



                <script>
                    jQuery('.list-edit').click(function () {
                        var item = jQuery(this).attr('data-field');
                        jQuery('.' + item).toggle();
                        jQuery(this).toggle(function () {
                            jQuery(this).html('mod visual');
                        }, function () {
                            jQuery(this).html('editeaza sectiunea');
                        });
                    });
                </script>


			<?php
			$total_pages = 1;
			if ( $total_jobs > 0 && $reults_per_page > 0 && $total_jobs > $reults_per_page ) {
			$total_pages = ceil( $total_jobs / $reults_per_page );
			?>
                <div class="jobsearch-pagination-blog">
					<?php $Jobsearch_User_Dashboard_Settings->pagination( $total_pages, $page_num, $page_url ) ?>
                </div>
			<?php
			}
			} else {
			?>
                <p><?php esc_html_e( 'No job found.', 'wp-jobsearch' ) ?></p>
				<?php
			}
			?>

        </div>
    </div>
	<?php
}