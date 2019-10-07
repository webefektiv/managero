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
global $wp;

?>
<div class="jobsearch-row  dashboardjobs" id="joburi" style="padding: 0">
    <div class="jobsearch-column-3">
		<?php
		// lista aplicatii
		if ( $candidate_id > 0 ) {
			$user_applied_jobs_list  = array();
			$user_applied_jobs_liste = get_user_meta( $user_id, 'jobsearch-user-jobs-applied-list', true );
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
                    <a href="<?php echo home_url( $wp->request ) . '?tab=applied-jobs&view=aplicat'; ?>">
                        <h2><?php echo apply_filters( 'jobsearch_dash_applid_jobs_tab_main_title', esc_html__( 'Applied Jobs', 'wp-jobsearch' ) ) ?></h2>
                    </a>
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
                                <div class="jobsearch-table-layer jobsearch-managejobs-tbody manage3">
                                    <ul class="listWrap">
										<?php
										foreach ( $user_applied_jobs_list as $job_key => $job_val ) {

											$job_id   = isset( $job_val['post_id'] ) ? $job_val['post_id'] : 0;
											$job_name = get_the_title( $job_id );

											if ( get_post_type( $job_id ) == 'job' ) {
												ob_start(); ?>
                                                <li><a href="<?php the_permalink( $job_id ); ?>"
                                                       target="_blank"><?= $job_name; ?></a></li>
												<?php
												$apply_job_html = ob_get_clean();
												echo apply_filters( 'jobsearch_cand_dash_apply_jobs_list_itm', $apply_job_html, $job_id, $job_key, $candidate_id );
											}
										}
										?>
                                    </ul>
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
		// liste de aplicat
		if ( $candidate_id > 0 ) {
			$candidate_fav_jobs_list  = array();
			$candidate_fav_jobs_liste = get_post_meta( $candidate_id, 'jobsearch_fav_jobs_list', true );
			$candidate_fav_jobs_liste = $candidate_fav_jobs_liste != '' ? explode( ',', $candidate_fav_jobs_liste ) : array();
			if ( ! empty( $candidate_fav_jobs_liste ) ) {
				foreach ( $candidate_fav_jobs_liste as $er_fav_job_list ) {
					$job_id = $er_fav_job_list;
					if ( get_post_type( $job_id ) == 'job' ) {
						$candidate_fav_jobs_list[] = $job_id;
					}
				}
			}
			if ( ! empty( $candidate_fav_jobs_list ) ) {
				$candidate_fav_jobs_list = implode( ',', $candidate_fav_jobs_list );
			} else {
				$candidate_fav_jobs_list = '';
			}
			?>
            <div class="jobsearch-employer-box-section">
                <div class="jobsearch-profile-title">
                    <a href="<?php echo home_url( $wp->request ) . '?tab=applied-jobs&view=de_aplicat'; ?>">
                        <h2><?php echo apply_filters( 'jobsearch_dash_applid_jobs_tab_main_title', esc_html__( 'De aplicat', 'wp-jobsearch' ) ) ?></h2>
                    </a>
                </div>
				<?php
				if ( $candidate_fav_jobs_list != '' ) {
					$candidate_fav_jobs_list = explode( ',', $candidate_fav_jobs_list );

					if ( ! empty( $candidate_fav_jobs_list ) ) {
						$total_jobs = count( $candidate_fav_jobs_list );
						krsort( $candidate_fav_jobs_list );

						$start  = ( $page_num - 1 ) * ( $reults_per_page );
						$offset = $reults_per_page;

						$candidate_fav_jobs_list = array_slice( $candidate_fav_jobs_list, $start, $offset );

						ob_start();
						?>

                        <div class="jobsearch-applied-jobs">
                            <ul class="jobsearch-row">
                                <div class="jobsearch-column-12">
                                    <div class="jobsearch-table-layer jobsearch-managejobs-tbody manage3">
                                        <ul class="listWrap">
											<?php
											foreach ( $candidate_fav_jobs_list as $job_key => $job_val ) {
												$job_id   = $job_val;
												$job_name = get_the_title( $job_id );

												if ( get_post_type( $job_id ) == 'job' ) {
													ob_start(); ?>
                                                    <li><a href="<?php the_permalink( $job_id ); ?>"
                                                           target="_blank"><?= $job_name; ?></a></li>
													<?php
													$rejjobs_html = ob_get_clean();
													echo apply_filters( 'jobsearch_cand_dash_favjobs_list_html', $rejjobs_html, $candidate_rej_jobs_list, $candidate_id );
												}
											}
											?>
                                        </ul>
                                    </div>
                                </div>
                            </ul>
                        </div>
						<?php
						$favjobs_html = ob_get_clean();
						echo apply_filters( 'jobsearch_cand_dash_favjobs_list_html', $favjobs_html, $candidate_fav_jobs_list, $candidate_id );

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

		//liste reject
		if ( $candidate_id > 0 ) {
			$candidate_rej_jobs_list  = array();
			$candidate_rej_jobs_liste = get_post_meta( $candidate_id, 'jobsearch_rej_jobs_list', true );
			$candidate_rej_jobs_liste = $candidate_rej_jobs_liste != '' ? explode( ',', $candidate_rej_jobs_liste ) : array();


			if ( ! empty( $candidate_rej_jobs_liste ) ) {
				foreach ( $candidate_rej_jobs_liste as $er_fav_job_list ) {
					$job_id = $er_fav_job_list;
					if ( get_post_type( $job_id ) == 'job' ) {
						$candidate_rej_jobs_list[] = $job_id;

					}
				}
			}

			?>
            <div class="jobsearch-employer-box-section">
                <div class="jobsearch-profile-title">
                    <a href="<?php echo home_url( $wp->request ) . '?tab=applied-jobs&view=respinse'; ?>">
                        <h2><?php echo apply_filters( 'jobsearch_dash_applid_jobs_tab_main_title', esc_html__( 'Respinse', 'wp-jobsearch' ) ) ?></h2>
                    </a>
                </div>
				<?php
				if ( ! empty( $candidate_rej_jobs_list ) ) {
					?>
                    <div class="jobsearch-applied-jobs">
                        <ul class="jobsearch-row">
                            <div class="jobsearch-column-12">
                                <div class="jobsearch-table-layer jobsearch-managejobs-tbody manage3">
                                    <ul class="listWrap">
										<?php
										foreach ( $candidate_rej_jobs_list as $job_key => $job_val ) {
											$job_id   = $job_val;
											$job_name = get_the_title( $job_id );

											if ( get_post_type( $job_id ) == 'job' ) {
												ob_start(); ?>
                                                <li><a href="<?php the_permalink( $job_id ); ?>"
                                                       target="_blank"><?= $job_name; ?></a></li>
												<?php
												$rejjobs_html = ob_get_clean();
												echo apply_filters( 'jobsearch_cand_dash_favjobs_list_html', $rejjobs_html, $candidate_rej_jobs_list, $candidate_id );
											}
										}
										?>
                                    </ul>
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

		//aplicate direct
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

			?>
            <div class="jobsearch-employer-box-section">
                <div class="jobsearch-profile-title">
                    <a href="<?php echo home_url( $wp->request ) . '?tab=applied-jobs&view=direct'; ?>">
                        <h2><?php echo apply_filters( 'jobsearch_dash_applid_jobs_tab_main_title', esc_html__( 'Am aplicat direct', 'wp-jobsearch' ) ) ?></h2>
                    </a>
                </div>
				<?php
				if ( ! empty( $candidate_apd_jobs_list ) ) {
					?>
                    <div class="jobsearch-applied-jobs">
                        <ul class="jobsearch-row">
                            <div class="jobsearch-column-12">
                                <div class="jobsearch-table-layer jobsearch-managejobs-tbody manage3">
                                    <ul class="listWrap">
										<?php
										foreach ( $candidate_apd_jobs_list as $job_key => $job_val ) {
											$job_id   = $job_val;
											$job_name = get_the_title( $job_id );

											if ( get_post_type( $job_id ) == 'job' ) {
												ob_start(); ?>
                                                <li><a href="<?php the_permalink( $job_id ); ?>"
                                                       target="_blank"><?= $job_name; ?></a></li>
												<?php
												$rejjobs_html = ob_get_clean();
												echo apply_filters( 'jobsearch_cand_dash_favjobs_list_html', $rejjobs_html, $candidate_apd_jobs_list, $candidate_id );
											}
										}
										?>
                                    </ul>
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


		?>
    </div>
    <div class="jobsearch-column-9">
		<?php
		if ( isset( $_GET['view'] ) && $_GET['view'] != '' ):
			$view = $_GET['view'];
		else:
			$view = 'all';
		endif;
		if ( $view == 'aplicat'|| $view == 'all' ) {
			foreach ( $user_applied_jobs_list as $job_key => $job_val ) {

				$post_id = $job_id = isset( $job_val['post_id'] ) ? $job_val['post_id'] : 0;

				$companie_id = get_post_meta( $job_id, 'jobsearch_field_job_posted_by', true );

				$logo     = get_the_post_thumbnail_url( $companie_id );
				$companie = get_the_title( $companie_id );
				$job      = get_the_title( $job_id );

				$adresa = get_post_meta( $job_id, 'jobsearch_field_location_address', true );

				$adresa2 = get_post_meta( $companie_id, 'jobsearch_field_location_location1', true );
				$adresa3 = get_post_meta( $companie_id, 'jobsearch_field_location_location2', true );

				$adresa4 = get_post_meta( $job_id, 'jobsearch_field_location_location2', true );

				$angajati = get_post_meta( $companie_id, 'numar_agajati', true );

				$nivel = get_post_meta( $job_id, 'nivel-ierarhic', true );

				$salariu = get_post_meta( $job_id, 'jobsearch_field_job_salary', true );

				$domenii = wp_get_post_terms( $job_id, 'sector' );

				$extindere = get_post_meta( $companie_id, 'extindere', true );

				$listaDomenii = '';

				foreach ( $domenii as $domeniu ) {
					$listaDomenii .= $domeniu->name . ', ';
				}

				$listaDomenii = substr( $listaDomenii, 0, - 2 );


				$application_deadline = get_post_meta( $post_id, 'jobsearch_field_job_application_deadline_date', true );
				$jobsearch_job_posted = get_post_meta( $post_id, 'jobsearch_field_job_publish_date', true );
				$current_date         = strtotime( current_time( 'd-m-Y H:i:s' ) );


				$job_employer_id = $companie_id;


				$user_id           = get_current_user_id();
				$user_is_candidate = jobsearch_user_is_candidate( $user_id );
				?>
                <div class="col-12 jobslist-job" data-job="<?= $job_id; ?>">
                    <div class="row">
                        <div class="col-sm-2">
                            <img src="<?= $logo; ?>"/>
                        </div>
                        <div class="col-sm-7">
                            <a href="<?php the_permalink(); ?>"><h2><?= $companie ?><br/><span><?= $job; ?></span>
                                </h2>
                            </a>
                            <div class="row">
                                <div class="col-sm-3 text-gray">
									<?php esc_html_e( 'Detalii companie', 'managero' ); ?>
                                </div>
                                <div class="col-sm-9">
									<?php esc_html_e( 'Origine  ', 'managero' ); ?><?= $adresa2; ?>
                                    | <?php esc_html_e( 'extindere  ', 'managero' ); ?><?= $extindere; ?>
                                    | <?= $angajati; ?>+ <?php esc_html_e( 'angajati', 'managero' ); ?>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-3 text-gray">
									<?php esc_html_e( 'Detalii job', 'managero' ); ?>
                                </div>
                                <div class="col-sm-9">
									<?= $adresa4; ?> | <?= $nivel; ?> | <?= $listaDomenii; ?>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-3 text-gray">
									<?php esc_html_e( 'Salariu', 'managero' ); ?>
                                </div>
                                <div class="col-sm-9">
									<?= $salariu; ?> &euro;
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-3">

							<?php the_apply_button( $job_id, $application_deadline, $job_employer_id ); ?>

							<?php
							if ( $job_type_str != '' ) {
								echo force_balance_tags( $job_type_str );
							}
							?>

                        </div>
                    </div>
                </div>
			<?php }
		}
		if ( $view == 'de_aplicat'|| $view == 'all'  ) {
			foreach ( $candidate_fav_jobs_list as $job_key => $job_val ) {

				$post_id = $job_id = $job_val;

				$companie_id = get_post_meta( $job_id, 'jobsearch_field_job_posted_by', true );

				$logo     = get_the_post_thumbnail_url( $companie_id );
				$companie = get_the_title( $companie_id );
				$job      = get_the_title( $job_id );

				$adresa = get_post_meta( $job_id, 'jobsearch_field_location_address', true );

				$adresa2 = get_post_meta( $companie_id, 'jobsearch_field_location_location1', true );
				$adresa3 = get_post_meta( $companie_id, 'jobsearch_field_location_location2', true );

				$adresa4 = get_post_meta( $job_id, 'jobsearch_field_location_location2', true );

				$angajati = get_post_meta( $companie_id, 'numar_agajati', true );

				$nivel = get_post_meta( $job_id, 'nivel-ierarhic', true );

				$salariu = get_post_meta( $job_id, 'jobsearch_field_job_salary', true );

				$domenii = wp_get_post_terms( $job_id, 'sector' );

				$extindere = get_post_meta( $companie_id, 'extindere', true );

				$listaDomenii = '';

				foreach ( $domenii as $domeniu ) {
					$listaDomenii .= $domeniu->name . ', ';
				}

				$listaDomenii = substr( $listaDomenii, 0, - 2 );


				$application_deadline = get_post_meta( $post_id, 'jobsearch_field_job_application_deadline_date', true );
				$jobsearch_job_posted = get_post_meta( $post_id, 'jobsearch_field_job_publish_date', true );
				$current_date         = strtotime( current_time( 'd-m-Y H:i:s' ) );


				$job_employer_id = $companie_id;


				$user_id           = get_current_user_id();
				$user_is_candidate = jobsearch_user_is_candidate( $user_id );
				?>
                <div class="col-12 jobslist-job" data-job="<?= $job_id; ?>">
                    <div class="row">
                        <div class="col-sm-2">
                            <img src="<?= $logo; ?>"/>
                        </div>
                        <div class="col-sm-7">
                            <a href="<?php the_permalink(); ?>"><h2><?= $companie ?><br/><span><?= $job; ?></span>
                                </h2>
                            </a>
                            <div class="row">
                                <div class="col-sm-3 text-gray">
									<?php esc_html_e( 'Detalii companie', 'managero' ); ?>
                                </div>
                                <div class="col-sm-9">
									<?php esc_html_e( 'Origine  ', 'managero' ); ?><?= $adresa2; ?>
                                    | <?php esc_html_e( 'extindere  ', 'managero' ); ?><?= $extindere; ?>
                                    | <?= $angajati; ?>+ <?php esc_html_e( 'angajati', 'managero' ); ?>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-3 text-gray">
									<?php esc_html_e( 'Detalii job', 'managero' ); ?>
                                </div>
                                <div class="col-sm-9">
									<?= $adresa4; ?> | <?= $nivel; ?> | <?= $listaDomenii; ?>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-3 text-gray">
									<?php esc_html_e( 'Salariu', 'managero' ); ?>
                                </div>
                                <div class="col-sm-9">
									<?= $salariu; ?> &euro;
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-3">

							<?php the_apply_button( $job_id, $application_deadline, $job_employer_id ); ?>

							<?php
							if ( $job_type_str != '' ) {
								echo force_balance_tags( $job_type_str );
							}
							?>

                        </div>
                    </div>
                </div>
			<?php }
		}
		if ( $view == 'respinse'|| $view == 'all'  ) {
			foreach ( $candidate_rej_jobs_list as $job_key => $job_val ) {

				$post_id = $job_id = $job_val;

				$companie_id = get_post_meta( $job_id, 'jobsearch_field_job_posted_by', true );

				$logo     = get_the_post_thumbnail_url( $companie_id );
				$companie = get_the_title( $companie_id );
				$job      = get_the_title( $job_id );

				$adresa = get_post_meta( $job_id, 'jobsearch_field_location_address', true );

				$adresa2 = get_post_meta( $companie_id, 'jobsearch_field_location_location1', true );
				$adresa3 = get_post_meta( $companie_id, 'jobsearch_field_location_location2', true );

				$adresa4 = get_post_meta( $job_id, 'jobsearch_field_location_location2', true );

				$angajati = get_post_meta( $companie_id, 'numar_agajati', true );

				$nivel = get_post_meta( $job_id, 'nivel-ierarhic', true );

				$salariu = get_post_meta( $job_id, 'jobsearch_field_job_salary', true );

				$domenii = wp_get_post_terms( $job_id, 'sector' );

				$extindere = get_post_meta( $companie_id, 'extindere', true );

				$listaDomenii = '';

				foreach ( $domenii as $domeniu ) {
					$listaDomenii .= $domeniu->name . ', ';
				}

				$listaDomenii = substr( $listaDomenii, 0, - 2 );


				$application_deadline = get_post_meta( $post_id, 'jobsearch_field_job_application_deadline_date', true );
				$jobsearch_job_posted = get_post_meta( $post_id, 'jobsearch_field_job_publish_date', true );
				$current_date         = strtotime( current_time( 'd-m-Y H:i:s' ) );


				$job_employer_id = $companie_id;


				$user_id           = get_current_user_id();
				$user_is_candidate = jobsearch_user_is_candidate( $user_id );
				?>
                <div class="col-12 jobslist-job" data-job="<?= $job_id; ?>">
                    <div class="row">
                        <div class="col-sm-2">
                            <img src="<?= $logo; ?>"/>
                        </div>
                        <div class="col-sm-7">
                            <a href="<?php the_permalink(); ?>"><h2><?= $companie ?><br/><span><?= $job; ?></span>
                                </h2>
                            </a>
                            <div class="row">
                                <div class="col-sm-3 text-gray">
									<?php esc_html_e( 'Detalii companie', 'managero' ); ?>
                                </div>
                                <div class="col-sm-9">
									<?php esc_html_e( 'Origine  ', 'managero' ); ?><?= $adresa2; ?>
                                    | <?php esc_html_e( 'extindere  ', 'managero' ); ?><?= $extindere; ?>
                                    | <?= $angajati; ?>+ <?php esc_html_e( 'angajati', 'managero' ); ?>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-3 text-gray">
									<?php esc_html_e( 'Detalii job', 'managero' ); ?>
                                </div>
                                <div class="col-sm-9">
									<?= $adresa4; ?> | <?= $nivel; ?> | <?= $listaDomenii; ?>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-3 text-gray">
									<?php esc_html_e( 'Salariu', 'managero' ); ?>
                                </div>
                                <div class="col-sm-9">
									<?= $salariu; ?> &euro;
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-3">

							<?php the_apply_button( $job_id, $application_deadline, $job_employer_id ); ?>

							<?php
							if ( $job_type_str != '' ) {
								echo force_balance_tags( $job_type_str );
							}
							?>

                        </div>
                    </div>
                </div>
			<?php }
		}

		if ( $view == 'direct'|| $view == 'all'  ) {
			foreach ( $candidate_apd_jobs_list as $job_key => $job_val ) {

				$post_id = $job_id = $job_val;

				$companie_id = get_post_meta( $job_id, 'jobsearch_field_job_posted_by', true );

				$logo     = get_the_post_thumbnail_url( $companie_id );
				$companie = get_the_title( $companie_id );
				$job      = get_the_title( $job_id );

				$adresa = get_post_meta( $job_id, 'jobsearch_field_location_address', true );

				$adresa2 = get_post_meta( $companie_id, 'jobsearch_field_location_location1', true );
				$adresa3 = get_post_meta( $companie_id, 'jobsearch_field_location_location2', true );

				$adresa4 = get_post_meta( $job_id, 'jobsearch_field_location_location2', true );

				$angajati = get_post_meta( $companie_id, 'numar_agajati', true );

				$nivel = get_post_meta( $job_id, 'nivel-ierarhic', true );

				$salariu = get_post_meta( $job_id, 'jobsearch_field_job_salary', true );

				$domenii = wp_get_post_terms( $job_id, 'sector' );

				$extindere = get_post_meta( $companie_id, 'extindere', true );

				$listaDomenii = '';

				foreach ( $domenii as $domeniu ) {
					$listaDomenii .= $domeniu->name . ', ';
				}

				$listaDomenii = substr( $listaDomenii, 0, - 2 );


				$application_deadline = get_post_meta( $post_id, 'jobsearch_field_job_application_deadline_date', true );
				$jobsearch_job_posted = get_post_meta( $post_id, 'jobsearch_field_job_publish_date', true );
				$current_date         = strtotime( current_time( 'd-m-Y H:i:s' ) );


				$job_employer_id = $companie_id;


				$user_id           = get_current_user_id();
				$user_is_candidate = jobsearch_user_is_candidate( $user_id );
				?>
                <div class="col-12 jobslist-job" data-job="<?= $job_id; ?>">
                    <div class="row">
                        <div class="col-sm-2">
                            <img src="<?= $logo; ?>"/>
                        </div>
                        <div class="col-sm-7">
                            <a href="<?php the_permalink(); ?>"><h2><?= $companie ?><br/><span><?= $job; ?></span>
                                </h2>
                            </a>
                            <div class="row">
                                <div class="col-sm-3 text-gray">
									<?php esc_html_e( 'Detalii companie', 'managero' ); ?>
                                </div>
                                <div class="col-sm-9">
									<?php esc_html_e( 'Origine  ', 'managero' ); ?><?= $adresa2; ?>
                                    | <?php esc_html_e( 'extindere  ', 'managero' ); ?><?= $extindere; ?>
                                    | <?= $angajati; ?>+ <?php esc_html_e( 'angajati', 'managero' ); ?>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-3 text-gray">
									<?php esc_html_e( 'Detalii job', 'managero' ); ?>
                                </div>
                                <div class="col-sm-9">
									<?= $adresa4; ?> | <?= $nivel; ?> | <?= $listaDomenii; ?>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-3 text-gray">
									<?php esc_html_e( 'Salariu', 'managero' ); ?>
                                </div>
                                <div class="col-sm-9">
									<?= $salariu; ?> &euro;
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-3">

							<?php the_apply_button( $job_id, $application_deadline, $job_employer_id ); ?>

							<?php
							if ( $job_type_str != '' ) {
								echo force_balance_tags( $job_type_str );
							}
							?>

                        </div>
                    </div>
                </div>
			<?php }
		}
		?>
    </div>
</div>
