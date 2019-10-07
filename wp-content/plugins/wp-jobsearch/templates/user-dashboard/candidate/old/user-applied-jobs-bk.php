<?php
global $jobsearch_plugin_options, $Jobsearch_User_Dashboard_Settings;
$user_id  = get_current_user_id();
$user_obj = get_user_by( 'ID', $user_id );

$page_id  = $user_dashboard_page = isset( $jobsearch_plugin_options['user-dashboard-template-page'] ) ? $jobsearch_plugin_options['user-dashboard-template-page'] : '';
$page_id  = $user_dashboard_page = jobsearch__get_post_id( $user_dashboard_page, 'page' );
$page_url = jobsearch_wpml_lang_page_permalink( $page_id, 'page' ); //get_permalink($page_id);

$candidate_id = jobsearch_get_user_candidate_id( $user_id );

$reults_per_page = isset( $jobsearch_plugin_options['user-dashboard-per-page'] ) && $jobsearch_plugin_options['user-dashboard-per-page'] > 0 ? $jobsearch_plugin_options['user-dashboard-per-page'] : 10;

$page_num = isset( $_GET['page_num'] ) ? $_GET['page_num'] : 1;




if ( $candidate_id > 0 ) {
	$user_applied_jobs_list  = array();
	$user_applied_jobs_liste = get_user_meta( $user_id, 'jobsearch-user-jobs-applied-list', true );

//	print_r($user_applied_jobs_liste);
//	die();

	if ( ! empty( $user_applied_jobs_liste ) ) {
		foreach ( $user_applied_jobs_liste as $er_applied_jobs_list_key => $er_applied_jobs_list_val ) {
			$job_id = isset( $er_applied_jobs_list_val['post_id'] ) ? $er_applied_jobs_list_val['post_id'] : 0;
			if ( get_post_type( $job_id ) == 'job' ) {
				$user_applied_jobs_list[ $er_applied_jobs_list_key ] = $er_applied_jobs_list_val;
			}
		}
	}
	?>
    <div class="jobsearch-employer-box-section">
        <div class="jobsearch-profile-title">
            <h2><?php echo apply_filters( 'jobsearch_dash_applid_jobs_tab_main_title', esc_html__( 'Applied Jobs', 'wp-jobsearch' ) ) ?></h2>
        </div>
		<?php
		if ( ! empty( $user_applied_jobs_list ) ) {
			$total_jobs = count( $user_applied_jobs_list );
			krsort( $user_applied_jobs_list );

			$start  = ( $page_num - 1 ) * ( $reults_per_page );
			$offset = $reults_per_page;

			$user_applied_jobs_list = array_slice( $user_applied_jobs_list, $start, $offset );

			?>
            <div class="jobsearch-applied-jobs">
                <ul class="jobsearch-row">
                    <div class="jobsearch-column-12">
                        <div class="jobsearch-candidati-list">
                            <!-- Manage Jobs Header -->
                            <div class="jobsearch-table-layer jobsearch-applyjobs-thead">
                                <div class="jobsearch-table-row">
                                    <div class="jobsearch-table-cell">Job</div>
                                    <div class="jobsearch-table-cell">Companie</div>
                                    <div class="jobsearch-table-cell">Domeniu</div>
                                    <div class="jobsearch-table-cell">Locatie</div>
                                    <div class="jobsearch-table-cell"></div>
                                </div>
                            </div>

                        </div>
                    </div>
                    <div class="jobsearch-column-12">
                        <div class="jobsearch-table-layer jobsearch-managejobs-tbody manage3">
							<?php

							foreach ( $user_applied_jobs_list as $job_key => $job_val ) {

								$job_id            = isset( $job_val['post_id'] ) ? $job_val['post_id'] : 0;
								$job_post_date     = get_post_meta( $job_id, 'jobsearch_field_job_publish_date', true );
								$job_location      = get_post_meta( $job_id, 'jobsearch_field_location_location2', true );
								$job_post_employer = get_post_meta( $job_id, 'jobsearch_field_job_posted_by', true );

								$get_job_apps_cv_att = get_post_meta( $job_id, 'job_apps_cv_att', true );
								$attach_cv_job       = isset( $get_job_apps_cv_att[ $candidate_id ] ) ? $get_job_apps_cv_att[ $candidate_id ] : '';

								$job_post_user = jobsearch_get_employer_user_id( $job_post_employer );

								$job_name  =  	get_the_title($job_id);
								$company_name = get_the_title($job_post_employer);



								$user_def_avatar_url = get_avatar_url( $job_post_user, array( 'size' => 69 ) );
								$user_avatar_id      = get_post_thumbnail_id( $job_post_employer );
								if ( $user_avatar_id > 0 ) {
									$user_thumbnail_image = wp_get_attachment_image_src( $user_avatar_id, 'thumbnail' );
									$user_def_avatar_url  = isset( $user_thumbnail_image[0] ) && esc_url( $user_thumbnail_image[0] ) != '' ? $user_thumbnail_image[0] : '';
								}
								$user_def_avatar_url = $user_def_avatar_url == '' ? jobsearch_no_image_placeholder() : $user_def_avatar_url;

								$sectors    = wp_get_post_terms( $job_id, 'sector' );
								$job_sector = isset( $sectors[0]->name ) ? $sectors[0]->name : '';

								if ( get_post_type( $job_id ) == 'job' ) {
									ob_start();
									?>
                                    <div class="jobsearch-table-row">

                                        <div class="jobsearch-table-cell">
											<?= $job_name; ?>
                                        </div>
                                        <div class="jobsearch-table-cell">
		                                    <?= $company_name; ?>
                                        </div>
                                        <div class="jobsearch-table-cell">
											<?= $job_sector; ?>
                                        </div>
                                        <div class="jobsearch-table-cell">
											<?= $job_location; ?>
                                        </div>
                                        <div class="jobsearch-table-cell">
                                            <a href="javascript:void(0);"
                                               class="jobsearch-savedjobs-links jobsearch-delete-applied-job"
                                               data-id="<?php echo( $job_id ) ?>"
                                               data-key="<?php echo( $job_key ) ?>"><i
                                                        class="jobsearch-icon jobsearch-rubbish"></i></a>
                                            <span class="remove-applied-job-loader"></span>
                                            <a href="<?php echo get_permalink( $job_id ) ?>"
                                               class="jobsearch-savedjobs-links"><i
                                                        class="jobsearch-icon jobsearch-view"></i></a>
                                        </div>
                                    </div>

									<?php
									$apply_job_html = ob_get_clean();
									echo apply_filters( 'jobsearch_cand_dash_apply_jobs_list_itm', $apply_job_html, $job_id, $job_key, $candidate_id );
								}
							}
							?>
                        </div>
                    </div>
                </ul>
            </div>
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
			echo '<p>' . esc_html__( 'No record found.', 'wp-jobsearch' ) . '</p>';
		}
		?>
    </div>
	<?php
}





if ( $candidate_id > 0 ) {
	$candidate_apd_jobs_list  = array();
	$candidate_apd_jobs_liste = get_post_meta( $candidate_id, 'jobsearch_apd_jobs_list', true );
	$candidate_apd_jobs_liste = $candidate_apd_jobs_liste != '' ? explode( ',', $candidate_apd_jobs_liste ) : array();
	if ( ! empty( $candidate_apd_jobs_liste ) ) {
		foreach ( $candidate_apd_jobs_liste as $er_fav_job_list ) {
			$job_id = $er_fav_job_list;
			if ( get_post_type( $job_id ) == 'job' ) {
				$candidate_apd_jobs_list[] = $job_id;
			}
		}
	}
	if ( ! empty( $candidate_apd_jobs_list ) ) {
		$candidate_apd_jobs_list = implode( ',', $candidate_apd_jobs_list );
	} else {
		$candidate_apd_jobs_list = '';
	}
	?>
    <div class="jobsearch-employer-box-section">
        <div class="jobsearch-profile-title">
            <h2><?php echo apply_filters( 'jobsearch_cand_dash_favjobs_mainhead_title', esc_html__( 'Aplicatii externe', 'wp-jobsearch' ) ) ?></h2>
        </div>
		<?php
		if ( $candidate_apd_jobs_list != '' ) {
			$candidate_apd_jobs_list = explode( ',', $candidate_apd_jobs_list );

			if ( ! empty( $candidate_apd_jobs_list ) ) {
				$total_jobs = count( $candidate_apd_jobs_list );
				krsort( $candidate_apd_jobs_list );

				$start  = ( $page_num - 1 ) * ( $reults_per_page );
				$offset = $reults_per_page;

				$candidate_apd_jobs_list = array_slice( $candidate_apd_jobs_list, $start, $offset );

				ob_start();
				?>
                <div class="jobsearch-candidate-savedjobs">
                    <ul class="jobsearch-row">
                        <div class="jobsearch-column-12">
                            <div class="jobsearch-candidati-list">
                                <!-- Manage Jobs Header -->
                                <div class="jobsearch-table-layer jobsearch-applyjobs-thead">
                                    <div class="jobsearch-table-row">
                                        <div class="jobsearch-table-cell">Job</div>
                                        <div class="jobsearch-table-cell">Companie</div>
                                        <div class="jobsearch-table-cell">Domeniu</div>
                                        <div class="jobsearch-table-cell">Locatie</div>
                                        <div class="jobsearch-table-cell"></div>
                                    </div>
                                </div>

                            </div>
                        </div>
                        <div class="jobsearch-column-12">
                            <div class="jobsearch-table-layer jobsearch-managejobs-tbody manage3">


								<?php
								foreach ( $candidate_apd_jobs_list as $job_id ) {

									$job_post_date     = get_post_meta( $job_id, 'jobsearch_field_job_publish_date', true );
									$job_location      = get_post_meta( $job_id, 'jobsearch_field_location_location2', true );
									$job_post_employer = get_post_meta( $job_id, 'jobsearch_field_job_posted_by', true );

									$job_post_user = jobsearch_get_employer_user_id( $job_post_employer );

									$user_def_avatar_url = get_avatar_url( $job_post_user, array( 'size' => 44 ) );
									$user_avatar_id      = get_post_thumbnail_id( $job_post_employer );
									if ( $user_avatar_id > 0 ) {
										$user_thumbnail_image = wp_get_attachment_image_src( $user_avatar_id, 'thumbnail' );
										$user_def_avatar_url  = isset( $user_thumbnail_image[0] ) && esc_url( $user_thumbnail_image[0] ) != '' ? $user_thumbnail_image[0] : '';
									}
									$user_def_avatar_url = $user_def_avatar_url == '' ? jobsearch_no_image_placeholder() : $user_def_avatar_url;

									$sectors    = wp_get_post_terms( $job_id, 'sector' );
									$job_sector = isset( $sectors[0]->name ) ? $sectors[0]->name : '';

									$job_name     = get_the_title( $job_id );
									$company_name = get_the_title( $job_post_employer );

									?>

                                    <div class="jobsearch-table-row" data-job="<?= $job_id; ?>">

                                        <div class="jobsearch-table-cell">
											<?= $job_name; ?>
                                        </div>
                                        <div class="jobsearch-table-cell">
											<?= $company_name; ?>
                                        </div>
                                        <div class="jobsearch-table-cell">
											<?= $job_sector; ?>
                                        </div>
                                        <div class="jobsearch-table-cell">
											<?= $job_location; ?>
                                        </div>
                                        <div class="jobsearch-table-cell">
                                            <a href="javascript:void(0);"
                                               class="jobsearch-savedjobs-links jobsearch-delete-apd-job"
                                               data-id="<?php echo( $job_id ) ?>"><i
                                                        class="jobsearch-icon jobsearch-rubbish"></i></a>
                                            <span class="remove-fav-job-loader"></span>
                                            <a href="<?php echo get_permalink( $job_id ) ?>"
                                               class="jobsearch-savedjobs-links"><i
                                                        class="jobsearch-icon jobsearch-view"></i></a>
                                        </div>
                                    </div>

									<?php
								}
								?>

                            </div>
                        </div>
                    </ul>
                </div>
				<?php
				$favjobs_html = ob_get_clean();
				echo apply_filters( 'jobsearch_cand_dash_favjobs_list_html', $favjobs_html, $candidate_apd_jobs_list, $candidate_id );

				$total_pages = 1;
				if ( $total_jobs > 0 && $reults_per_page > 0 && $total_jobs > $reults_per_page ) {
					$total_pages = ceil( $total_jobs / $reults_per_page );
					?>
                    <div class="jobsearch-pagination-blog">
						<?php $Jobsearch_User_Dashboard_Settings->pagination( $total_pages, $page_num, $page_url ) ?>
                    </div>
					<?php
				}
			}
		} else {
			echo '<p>' . esc_html__( 'No record found.', 'wp-jobsearch' ) . '</p>';
		}
		?>
    </div>
	<?php
}