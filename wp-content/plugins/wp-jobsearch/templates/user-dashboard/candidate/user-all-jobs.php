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

        <div class="jobsearch-employer-box-section">
            <div class="jobsearch-profile-title">
                <a href="<?php echo home_url( $wp->request ) . '?tab=all-jobs&view=alerte'; ?>">
                    <h3><?php echo apply_filters( 'jobsearch_dash_applid_jobs_tab_main_title', esc_html__( 'Joburi conform alerte', 'wp-jobsearch' ) ) ?> (<?= count($candidate_fav_jobs_liste); ?>)</h3>
                </a>
            </div>

            <div class="jobsearch-applied-jobs">
                <ul class="jobsearch-row">
                    <div class="jobsearch-column-12">
                        <div class="jobsearch-table-layer jobsearch-managejobs-tbody manage3">
                            <ul class="listWrap">
								<?php
								$job_alerts = get_field( 'job_alert', $candidate_id );

							//	var_dump($job_alerts);

								$start       = - 1;
								$alerte_t    = [];
								$alerte_args = [];
								foreach ( $job_alerts as $alerta ):
									$start ++;
									$domenii    = [];

									$alerte_t[] = $alerta['titlu_alerta'];

									$args[ $start ] = [
										'post_type'      => 'job',
										'posts_per_page' => 20,
										'order'          => 'DESC'
									];

									$args[ $start ]['meta_query'] = [
									'relation' => 'AND',
									];

									$args[ $start ]['tax_query'] = [
										'relation' => 'AND',
										[
											'taxonomy'         => 'sector',
											'field'            => 'id',
											'terms'            => $alerta['domeniu'],
											'include_children' => true,
											'operator'         => 'IN'
										]
									];

									foreach ( $alerta['locatie'] as $key => $judet ) {
										if ( $key == 0 ) {
											$locatie .= $judet['value'];
										} else {
											$locatie .= ", ".$judet['value'];
										}
									}
									$locatie = explode(',',$locatie);
									$alerta['locatie'] = $locatie;


									$q_locatie = [
										'key'     => 'locatia_jobului',
										'value'   => $alerta['locatie'],
										'compare' => 'in',
									];

									$args[ $start ]['meta_query'][] = $q_locatie;

									$q_niveluri = [
										'key'     => 'pozitia_ierarhica_nivelul_ierarhic',
										'value'   => $alerta['nivel_ierarhic'],
										'compare' => 'IN',
									];

									$args[$start]['meta_query'][] = $q_niveluri;

									$q_salariu = [
										'key'     => 'oferta_salariu_oferit_job',
										'value'   => $alerta['salariu_minim'],
										'type'    => 'NUMERIC',
										'compare' => '>=',
									];
									$args[$start]['meta_query'][] = $q_salariu;

									$alerte_args[ wc_strtolower( str_replace( ' ', '-', $alerta['titlu_alerta'] ) ) ] = $args[ $start ];

								endforeach; ?>


								<?php
								$x = - 1;
								foreach ( $args as $arg ):
									$x ++;
									$alerta = wc_strtolower( str_replace( ' ', '-', $alerte_t[ $x ] ) );
									?>
                                    <div class="job-din-alerte">
                                        <a href="<?php echo home_url( $wp->request ) . '?tab=all-jobs&view=alerte&alerta=' . $alerta; ?>"
                                           class="href">
											<?php echo "<h2 class='alerteTitle'>$alerte_t[$x]</h2>"; ?>
                                        </a>
										<?php

										$user_applied_jobs_liste = get_user_meta( $user_id, 'jobsearch-user-jobs-applied-list', true );
										$aplicatii               = [];

										foreach ( $user_applied_jobs_liste as $post ) {
											$aplicatii[] = $post['post_id'];
										}

										$candidate_fav_jobs_liste = get_post_meta( $candidate_id, 'jobsearch_fav_jobs_list', true );
										$candidate_fav_jobs_liste = $candidate_fav_jobs_liste != '' ? explode( ',', $candidate_fav_jobs_liste ) : array();


										$candidate_rej_jobs_liste = get_post_meta( $candidate_id, 'jobsearch_rej_jobs_list', true );
										$candidate_rej_jobs_liste = $candidate_rej_jobs_liste != '' ? explode( ',', $candidate_rej_jobs_liste ) : array();

										$candidate_apd_jobs_liste = get_post_meta( $candidate_id, 'jobsearch_apd_jobs_list', true );
										$candidate_apd_jobs_liste = $candidate_apd_jobs_liste != '' ? explode( ',', $candidate_apd_jobs_liste ) : array();

										$exclude = array_merge( $candidate_fav_jobs_liste, $candidate_rej_jobs_liste, $candidate_apd_jobs_liste, $aplicatii );

										$arg['post__not_in'] = $exclude;

										$jobs = new WP_Query( $arg );
											?>
                                    </div>
								<?php endforeach;
								?>

                            </ul>
                        </div>
                    </div>
                </ul>
            </div>
        </div>

		<?php
		// lista aplicatii
		if ( $candidate_id == x ) {
			$user_applied_jobs_list  = array();
			$user_applied_jobs_liste = get_user_meta( $user_id, 'jobsearch-user-jobs-applied-list', true );
			;
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
                    <a href="<?php echo home_url( $wp->request ) . '?tab=all-jobs&view=aplicat'; ?>">
                        <h3><?php echo apply_filters( 'jobsearch_dash_applid_jobs_tab_main_title', esc_html__( 'Applied Jobs', 'wp-jobsearch' ) ) ?> ( 1011 )</h3>
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
					echo '<p class="none-text">' . esc_html__( 'Momentan nu ai selectat joburi in aceasta sectiune.', 'wp-jobsearch' ) . '</p>';
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
		//	print_r(count($candidate_fav_jobs_liste));

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
                    <a href="<?php echo home_url( $wp->request ) . '?tab=all-jobs&view=de_aplicat'; ?>">
                        <h3><?php echo apply_filters( 'jobsearch_dash_applid_jobs_tab_main_title', esc_html__( 'Joburi de aplicat', 'wp-jobsearch' ) ) ?>  (<?= count($candidate_fav_jobs_liste); ?>)</h3>
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
					} else {

					}
				} else {
					echo '<p class="none-text">' . esc_html__( 'Momentan nu ai selectat joburi in aceasta sectiune.', 'wp-jobsearch' ) . '</p>';
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
                    <a href="<?php echo home_url( $wp->request ) . '?tab=all-jobs&view=respinse'; ?>">
                        <h3><?php echo apply_filters( 'jobsearch_dash_applid_jobs_tab_main_title', esc_html__( 'Joburi neinteresante', 'wp-jobsearch' ) ) ?>  (<?= count($candidate_rej_jobs_list); ?>)</h3>
                    </a>
                </div>
				<?php
				if ( ! empty( $candidate_rej_jobs_liste ) ) {
					?>
                    <div class="jobsearch-applied-jobs">
                        <ul class="jobsearch-row">
                            <div class="jobsearch-column-12">
                                <div class="jobsearch-table-layer jobsearch-managejobs-tbody manage3">




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
					echo '<p class="none-text">' . esc_html__( 'Momentan nu ai selectat joburi in aceasta sectiune.', 'wp-jobsearch' ) . '</p>';
				}
				?>
            </div>
			<?php
		}

		//aplicate direct
		if ( $candidate_id > 0 && (0 == 1)) {
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
                    <a href="<?php echo home_url( $wp->request ) . '?tab=all-jobs&view=direct'; ?>">
                        <h3><?php echo apply_filters( 'jobsearch_dash_applid_jobs_tab_main_title', esc_html__( 'Am aplicat direct', 'wp-jobsearch' ) ) ?> (<?= count($candidate_apd_jobs_liste); ?>)</h3>
                    </a>
                </div>
				<?php
				if ( ! empty( $candidate_apd_jobs_liste ) ) {
					?>
                    <div class="jobsearch-applied-jobs">
                        <ul class="jobsearch-row">
                            <div class="jobsearch-column-12">
                                <div class="jobsearch-table-layer jobsearch-managejobs-tbody manage3">
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
					echo '<p class="none-text">' . esc_html__( 'Momentan nu ai selectat joburi in aceasta sectiune.', 'wp-jobsearch' ) . '</p>';
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

		if ( $view == 'alerte' || $view == 'all' ) {

			if ( isset( $_GET['alerta'] ) && $_GET['alerta'] != '' ):
				$args_alerte = $alerte_args[ $_GET['alerta'] ];
				$titlu = $_GET['alerta'];
				$titlu = str_replace( '-', ' ', $titlu );
				$titlu = ucfirst( $titlu );

				$candidate_fav_jobs_liste = get_post_meta( $candidate_id, 'jobsearch_fav_jobs_list', true );
				$candidate_fav_jobs_liste = $candidate_fav_jobs_liste != '' ? explode( ',', $candidate_fav_jobs_liste ) : array();


				$candidate_rej_jobs_liste = get_post_meta( $candidate_id, 'jobsearch_rej_jobs_list', true );
				$candidate_rej_jobs_liste = $candidate_rej_jobs_liste != '' ? explode( ',', $candidate_rej_jobs_liste ) : array();

				$candidate_apd_jobs_liste = get_post_meta( $candidate_id, 'jobsearch_apd_jobs_list', true );
				$candidate_apd_jobs_liste = $candidate_apd_jobs_liste != '' ? explode( ',', $candidate_apd_jobs_liste ) : array();

				$exclude = array_merge( $candidate_fav_jobs_liste, $candidate_rej_jobs_liste, $candidate_apd_jobs_liste, $aplicatii );


				$args_alerte['post__not_in'] = $exclude;

				$jobs = new WP_Query( $args_alerte ); ?>

                <a href="http://managero.ro/user-dashboard/?tab=all-jobs&view=alerte">
                    <h4 class="titluLista"><?= $titlu; ?></h4>
                </a>

               <?php if ($jobs->have_posts()):while ($jobs->have_posts()):$jobs->the_post();

                // get job id
                $job_id = get_the_ID();
                $post_id = $job_id;

                // get job user
                $companie_id = get_the_author_meta('ID');

                // get user post
                $employer_id = jobsearch_get_user_employer_id($companie_id);

                // returneaza date job
                $date_job = get_post_meta($post_id,'job_data_set', true);

                // returneza id imagine
                $logo = $date_job['imagine_profil_companie'];
                // returneaza url imagine
                $logo = wp_get_attachment_url($logo);

                // date companie
                $companie = $date_job['nume_companie'];
                $extindere = $date_job['extindere'];
                $adresa2 = $date_job['origine'][1];
                $angajati = $date_job['numar_angajati'];

                // titlu job
                $job = $date_job['post_job'];

                $nivel = $date_job['pozitia_ierarhica'];

                // locatie job
                $adresa4 = $date_job['locatia_jobului'][1];

                // nivel ierarhic
                $subordonat = $date_job['cui_este_subordonat'];
                $subordonati = $date_job['subordonati_total'];
                $nivelierarhic = $date_job['nivelul_ierarhic'];

                // salariu oferit job
                $salariu = $date_job['salariu_oferit_job'];

                // domenii job
                $domenii = $date_job['domeniu'];

                $listaDomenii = '';

                foreach ($domenii as $id) {
                $domeniu = get_term_by('id', $id, 'sector');
                $listaDomenii .= $domeniu->name . ', ';
                }

                $listaDomenii = substr($listaDomenii, 0, -2);



                // work in progress
                $application_deadline = get_post_meta($post_id, 'jobsearch_field_job_application_deadline_date', true);
                $jobsearch_job_posted = get_post_meta($post_id, 'jobsearch_field_job_publish_date', true);
                $current_date = strtotime(current_time('d-m-Y H:i:s'));

                $job_employer_id = $companie_id;

                $user_id = get_current_user_id();
                $user_is_candidate = jobsearch_user_is_candidate($user_id);

                if ($application_deadline != '' && $application_deadline <= $current_date) {
                continue;
                }

                if ($user_is_candidate) {

                $candidate_id = jobsearch_get_user_candidate_id($user_id);
                $candidate_rej_jobs_list = get_post_meta($candidate_id, 'jobsearch_rej_jobs_list', true);
                $candidate_fav_jobs_list = get_post_meta($candidate_id, 'jobsearch_fav_jobs_list', true);
                $candidate_apd_jobs_list = get_post_meta($candidate_id, 'jobsearch_apd_jobs_list', true);

                if ($candidate_rej_jobs_list != '') {
                $candidate_rej_jobs_list = explode(',', $candidate_rej_jobs_list);
                if (in_array($job_id, $candidate_rej_jobs_list)) {
                continue;
                }
                }

                if ($candidate_apd_jobs_list != '') {
                $candidate_apd_jobs_list = explode(',', $candidate_apd_jobs_list);
                if (in_array($job_id, $candidate_apd_jobs_list)) {
                continue;
                }
                }

                if ($candidate_fav_jobs_list != '') {
                $candidate_fav_jobs_list = explode(',', $candidate_fav_jobs_list);
                if (in_array($job_id, $candidate_fav_jobs_list)) {
                continue;
                }
                }

                }


                if (is_user_logged_in()) {
                $finded_result_list = jobsearch_find_index_user_meta_list($job_id, 'jobsearch-user-jobs-applied-list', 'post_id', jobsearch_get_user_id());
                if (is_array($finded_result_list) && !empty($finded_result_list)) {
                continue;
                }
                }

                $job_count++;

                ?>

                <div class="col-12 jobslist-job" data-job="<?= $job_id; ?>">
                    <div class="row custom-height">
                        <div class="col-sm-2">
                            <div class="wrapper-logo-company">
                                <img src="<?= $logo; ?>"/>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="company-name">
                                <a href="<?php the_permalink(); ?>"><h2><?= $companie ?>
                                    </h2>
                                    <h3 class="job-position"><?= $job; ?></h3></a>
                            </div>
                        </div>
                        <div class="col-sm-4">

							<?php
							$current_user = wp_get_current_user();
							$user_id = get_current_user_id();
							$user_obj = get_user_by('ID', $user_id);

							$employer_id = jobsearch_get_user_employer_id($user_id);
							if ($employer_id > 0):

							else:
								the_apply_button($job_id, $application_deadline, $job_employer_id);
							endif;
							?>

							<?php
							if ($job_type_str != '') {
								echo force_balance_tags($job_type_str);
							}
							?>

                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-2">
                            <div class="wrapper-company-details">
								<?php esc_html_e('Detalii companie', 'managero'); ?>
                            </div>
                        </div>
                        <div class="col-sm-7">
                            <div class="wrapper-text-details-company">
                                <b>Orgine</b>: <?= $adresa2 ?>
                                |  <b>Extindere</b>: <?= $extindere; ?>
                                |  <b>Nr angajati</b>: <?= $angajati; ?>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-2">
                            <div class="wrapper-company-details">
								<?php esc_html_e('Detalii job', 'managero'); ?>
                            </div>
                        </div>
                        <div class="col-sm-7">
                            <div class="wrapper-text-details-company">
                                <b>Locatie</b>: <?= $adresa4; ?> | <b>Domeniu de activitate</b>:  <?= $listaDomenii; ?><br>
                                <b>Nivel ierarhic</b>: <?= $nivelierarhic; ?> | <b>Subordonat lui</b>: <?= $subordonat; ?><br>
                                <b>Subordonati</b>:  <?= $subordonati; ?>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-2">
                            <div class="wrapper-company-details">
								<?php esc_html_e('Salariu', 'managero'); ?>
                            </div>
                        </div>
                        <div class="col-sm-7">
                            <div class="wrapper-text-details-company">
								<?= $salariu; ?> &euro;
                            </div>
                        </div>
                    </div>
                </div>


				<?php endwhile;

			else:
				echo "<h2 class='noJobs'>Momentan nu ai selectat joburi in aceasta sectiune.</h2>";
			endif;
			else: 
				$y = - 1;
				foreach ( $alerte_args as $altitlu => $aalerte ):
					$y ++;
					echo "<div class='alerta-list'>";
					$link = home_url( $wp->request ) . '?tab=all-jobs&view=alerte&alerta=' . $altitlu;

					echo "<a href='$link'><h4 class='titlu-alerte'>$alerte_t[$y]</h4></a>";

					$candidate_fav_jobs_liste = get_post_meta( $candidate_id, 'jobsearch_fav_jobs_list', true );
					$candidate_fav_jobs_liste = $candidate_fav_jobs_liste != '' ? explode( ',', $candidate_fav_jobs_liste ) : array();

					$candidate_rej_jobs_liste = get_post_meta( $candidate_id, 'jobsearch_rej_jobs_list', true );
					$candidate_rej_jobs_liste = $candidate_rej_jobs_liste != '' ? explode( ',', $candidate_rej_jobs_liste ) : array();

					$candidate_apd_jobs_liste = get_post_meta( $candidate_id, 'jobsearch_apd_jobs_list', true );
					$candidate_apd_jobs_liste = $candidate_apd_jobs_liste != '' ? explode( ',', $candidate_apd_jobs_liste ) : array();
					
					

					$exclude = array_merge( $candidate_fav_jobs_liste, $candidate_rej_jobs_liste, $candidate_apd_jobs_liste, $aplicatii );


					$aalerte['post__not_in'] = $exclude;
					//	var_dump($aalerte);

					$jobs = new WP_Query( $aalerte );
					if ($jobs->have_posts()):while ($jobs->have_posts()):$jobs->the_post();

						// get job id
						$job_id = get_the_ID();
						$post_id = $job_id;

						// get job user
						$companie_id = get_the_author_meta('ID');

						// get user post
						$employer_id = jobsearch_get_user_employer_id($companie_id);

						// returneaza date job
						$date_job = get_post_meta($post_id,'job_data_set', true);

						// returneza id imagine
						$logo = $date_job['imagine_profil_companie'];
						// returneaza url imagine
						$logo = wp_get_attachment_url($logo);

						// date companie
						$companie = $date_job['nume_companie'];
						$extindere = $date_job['extindere'];
						$adresa2 = $date_job['origine'][1];
						$angajati = $date_job['numar_angajati'];

						// titlu job
						$job = $date_job['post_job'];

						$nivel = $date_job['pozitia_ierarhica'];

						// locatie job
						$adresa4 = $date_job['locatia_jobului'][1];

						// nivel ierarhic
						$subordonat = $date_job['cui_este_subordonat'];
						$subordonati = $date_job['subordonati_total'];
						$nivelierarhic = $date_job['nivelul_ierarhic'];

						// salariu oferit job
						$salariu = $date_job['salariu_oferit_job'];

						// domenii job
						$domenii = $date_job['domeniu'];

						$listaDomenii = '';

						foreach ($domenii as $id) {
							$domeniu = get_term_by('id', $id, 'sector');
							$listaDomenii .= $domeniu->name . ', ';
						}

						$listaDomenii = substr($listaDomenii, 0, -2);



						// work in progress
						$application_deadline = get_post_meta($post_id, 'jobsearch_field_job_application_deadline_date', true);
						$jobsearch_job_posted = get_post_meta($post_id, 'jobsearch_field_job_publish_date', true);
						$current_date = strtotime(current_time('d-m-Y H:i:s'));

						$job_employer_id = $companie_id;

						$user_id = get_current_user_id();
						$user_is_candidate = jobsearch_user_is_candidate($user_id);


						if ($application_deadline != '' && $application_deadline <= $current_date) {
							continue;
						}

						if ($user_is_candidate) {

							$candidate_id = jobsearch_get_user_candidate_id($user_id);
							$candidate_rej_jobs_list = get_post_meta($candidate_id, 'jobsearch_rej_jobs_list', true);


							$candidate_fav_jobs_list = get_post_meta($candidate_id, 'jobsearch_fav_jobs_list', true);
							$candidate_apd_jobs_list = get_post_meta($candidate_id, 'jobsearch_apd_jobs_list', true);

							if ($candidate_rej_jobs_list != '') {
								$candidate_rej_jobs_list = explode(',', $candidate_rej_jobs_list);
								if (in_array($job_id, $candidate_rej_jobs_list)) {
									continue;
								}
							}

							if ($candidate_apd_jobs_list != '') {
								$candidate_apd_jobs_list = explode(',', $candidate_apd_jobs_list);
								if (in_array($job_id, $candidate_apd_jobs_list)) {
									continue;
								}
							}

							if ($candidate_fav_jobs_list != '') {
								$candidate_fav_jobs_list = explode(',', $candidate_fav_jobs_list);
								if (in_array($job_id, $candidate_fav_jobs_list)) {
									continue;
								}
							}

						}


						if (is_user_logged_in()) {
							$finded_result_list = jobsearch_find_index_user_meta_list($job_id, 'jobsearch-user-jobs-applied-list', 'post_id', jobsearch_get_user_id());
							if (is_array($finded_result_list) && !empty($finded_result_list)) {
								continue;
							}
						}

						$job_count++;

						?>

                        <div class="col-12 jobslist-job" data-job="<?= $job_id; ?>">
                            <div class="row custom-height">
                                <div class="col-sm-2">
                                    <div class="wrapper-logo-company">
                                        <img src="<?= $logo; ?>"/>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="company-name">
                                        <a href="<?php the_permalink(); ?>"><h2><?= $companie ?>
                                            </h2>
                                            <h3 class="job-position"><?= $job; ?></h3></a>
                                    </div>
                                </div>
                                <div class="col-sm-4">

									<?php
									$current_user = wp_get_current_user();
									$user_id = get_current_user_id();
									$user_obj = get_user_by('ID', $user_id);

									$employer_id = jobsearch_get_user_employer_id($user_id);
									if ($employer_id > 0):

									else:
										the_apply_button($job_id, $application_deadline, $job_employer_id);
									endif;
									?>

									<?php
									if ($job_type_str != '') {
										echo force_balance_tags($job_type_str);
									}
									?>

                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-2">
                                    <div class="wrapper-company-details">
										<?php esc_html_e('Detalii companie', 'managero'); ?>
                                    </div>
                                </div>
                                <div class="col-sm-7">
                                    <div class="wrapper-text-details-company">
                                        <b>Orgine</b>: <?= $adresa2 ?>
                                        |  <b>Extindere</b>: <?= $extindere; ?>
                                        |  <b>Nr angajati</b>: <?= $angajati; ?>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-2">
                                    <div class="wrapper-company-details">
										<?php esc_html_e('Detalii job', 'managero'); ?>
                                    </div>
                                </div>
                                <div class="col-sm-7">
                                    <div class="wrapper-text-details-company">
                                        <b>Locatie</b>: <?= $adresa4; ?> | <b>Domeniu de activitate</b>:  <?= $listaDomenii; ?><br>
                                        <b>Nivel ierarhic</b>: <?= $nivelierarhic; ?> | <b>Subordonat lui</b>: <?= $subordonat; ?><br>
                                        <b>Subordonati</b>:  <?= $subordonati; ?>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-2">
                                    <div class="wrapper-company-details">
										<?php esc_html_e('Salariu', 'managero'); ?>
                                    </div>
                                </div>
                                <div class="col-sm-7">
                                    <div class="wrapper-text-details-company">
										<?= $salariu; ?> &euro;
                                    </div>
                                </div>
                            </div>
                        </div>


					<?php endwhile;

					else:

						echo "<h2 class='noJobs'>Momentan nu ai selectat joburi in aceasta sectiune.</h2>";

					endif;
					echo "</div>";
				endforeach;

			endif;

			// $jobs = new WP_Query( $args_alerte );

		}

		if ( $view == 'de_aplicat' || $view == 'all' ) { 
				$candidate_fav_jobs_liste = get_post_meta( $candidate_id, 'jobsearch_fav_jobs_list', true );
			    $candidate_fav_jobs_liste = $candidate_fav_jobs_liste != '' ? explode( ',', $candidate_fav_jobs_liste ) : array();
										
									//	nice_print_r($candidate_fav_jobs_liste);
		
		?>
        <div class='alerta-list'>
            <a href="<?php echo home_url( $wp->request ) . '?tab=all-jobs&view=de_aplicat'; ?>">
                <h4 class="titluLista"><?php echo apply_filters( 'jobsearch_dash_applid_jobs_tab_main_title', esc_html__( 'Joburi de aplicat', 'wp-jobsearch' ) ) ?> (<?= count($candidate_fav_jobs_liste); ?>)
                </h4>
            </a>
            <div class="listaRezervate">
				<?php
				
				
									
										
										
				if ( ! empty( $candidate_fav_jobs_liste ) && isset( $candidate_fav_jobs_liste ) ):
					foreach ( $candidate_fav_jobs_liste as $job_key => $job_val ) {

						// get job id
						$job_id = $job_val;
						$post_id = $job_id;

						// get job user
						$companie_id = get_the_author_meta('ID');

						// get user post
						$employer_id = jobsearch_get_user_employer_id($companie_id);

						// returneaza date job
						$date_job = get_post_meta($post_id,'job_data_set', true);

						// returneza id imagine
						$logo = $date_job['imagine_profil_companie'];
						// returneaza url imagine
						$logo = wp_get_attachment_url($logo);

						// date companie
						$companie = $date_job['nume_companie'];
						$extindere = $date_job['extindere'];
						$adresa2 = $date_job['origine'][1];
						$angajati = $date_job['numar_angajati'];

						// titlu job
						$job = $date_job['post_job'];

						$nivel = $date_job['pozitia_ierarhica'];

						// locatie job
						$adresa4 = $date_job['locatia_jobului'][1];

						// nivel ierarhic
						$subordonat = $date_job['cui_este_subordonat'];
						$subordonati = $date_job['subordonati_total'];
						$nivelierarhic = $date_job['nivelul_ierarhic'];

						// salariu oferit job
						$salariu = $date_job['salariu_oferit_job'];

						// domenii job
						$domenii = $date_job['domeniu'];

						$listaDomenii = '';

						foreach ($domenii as $id) {
							$domeniu = get_term_by('id', $id, 'sector');
							$listaDomenii .= $domeniu->name . ', ';
						}

						$listaDomenii = substr($listaDomenii, 0, -2);



						// work in progress
						$application_deadline = get_post_meta($post_id, 'jobsearch_field_job_application_deadline_date', true);
						$jobsearch_job_posted = get_post_meta($post_id, 'jobsearch_field_job_publish_date', true);
						$current_date = strtotime(current_time('d-m-Y H:i:s'));

						$job_employer_id = $companie_id;

						$user_id = get_current_user_id();
						$user_is_candidate = jobsearch_user_is_candidate($user_id);

						?>
                        <div class="col-12 jobslist-job" data-job="<?= $job_id; ?>">
                            <div class="row custom-height">
                                <div class="col-sm-2">
                                    <div class="wrapper-logo-company">
                                        <img src="<?= $logo; ?>"/>
                                    </div>
                                </div>
                                <div class="col-sm-7">
                                    <div class="company-name">
                                        <a href="<?= get_the_permalink($job_id) ?>">
                                       <?php // var_dump($job); ?>
                                            <h2><?= $companie ?></h2>
                                            <h3 class="job-position"><?= $job; ?></h3></a>
                                    </div>
                                </div>
                                <div class="col-sm-3">

									<?php
									$current_user = wp_get_current_user();
									$user_id = get_current_user_id();
									$user_obj = get_user_by('ID', $user_id);

									$employer_id = jobsearch_get_user_employer_id($user_id);
									if ($employer_id > 0):

									else:
										the_apply_button($job_id, $application_deadline, $job_employer_id);
									endif;
									?>

									<?php
									if ($job_type_str != '') {
										echo force_balance_tags($job_type_str);
									}
									?>

                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-2">
                                    <div class="wrapper-company-details">
                                        <?php esc_html_e('Detalii companie', 'managero'); ?>
                                    </div>
                                </div>
                                <div class="col-sm-7">
                                    <div class="wrapper-text-details-company">
                                        <b>Orgine</b>: <?= $adresa2 ?>
                                        |  <b>Extindere</b>: <?= $extindere; ?>
                                        |  <b>Nr angajati</b>: <?= $angajati; ?>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-2">
                                    <div class="wrapper-company-details">
                                        <?php esc_html_e('Detalii job', 'managero'); ?>
                                    </div>
                                </div>
                                <div class="col-sm-7">
                                    <div class="wrapper-text-details-company">
                                        <b>Locatie</b>: <?= $adresa4; ?> | <b>Domeniu de activitate</b>:  <?= $listaDomenii; ?><br>
                                        <b>Nivel ierarhic</b>: <?= $nivelierarhic; ?> | <b>Subordonat lui</b>: <?= $subordonat; ?><br>
                                        <b>Subordonati</b>:  <?= $subordonati; ?>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-2">
                                    <div class="wrapper-company-details">
                                        <?php esc_html_e('Salariu', 'managero'); ?>
                                    </div>
                                </div>
                                <div class="col-sm-7">
                                    <div class="wrapper-text-details-company">
                                        <?= $salariu; ?> &euro;
                                    </div>
                                </div>
                            </div>
                        </div>
					<?php }
				else:
					echo "<h2 class='noJobs'>Momentan nu ai selectat joburi in aceasta sectiune.</h2>";
				endif; ?>
            </div>
        </div>
			<?php
		}

		if ( $view == 'respinse' || $view == 'all' ) { ?>
        <div class='alerta-list'>
            <a href="<?php echo home_url( $wp->request ) . '?tab=all-jobs&view=respinse'; ?>">
                <h4 class="titluLista"><?php echo apply_filters( 'jobsearch_dash_applid_jobs_tab_main_title', esc_html__( 'Joburi neinteresante', 'wp-jobsearch' ) ) ?> (<?= count($candidate_rej_jobs_list); ?>)
                </h4>
            </a>
            <div class="listaRespinse">
				<?php


                  //  print_r($candidate_rej_jobs_list);

				if ( ! empty( $candidate_rej_jobs_list ) && isset( $candidate_rej_jobs_list ) ):
					foreach ( $candidate_rej_jobs_list as $job_key => $job_val ) {

						// get job id
						$job_id = $job_val;
						$post_id = $job_id;

						// get job user
						$companie_id = get_the_author_meta('ID');

						// get user post
						$employer_id = jobsearch_get_user_employer_id($companie_id);

						// returneaza date job
						$date_job = get_post_meta($post_id,'job_data_set', true);

						// returneza id imagine
						$logo = $date_job['imagine_profil_companie'];
						// returneaza url imagine
						$logo = wp_get_attachment_url($logo);

						// date companie
						$companie = $date_job['nume_companie'];
						$extindere = $date_job['extindere'];
						$adresa2 = $date_job['origine'][1];
						$angajati = $date_job['numar_angajati'];

						// titlu job
						$job = $date_job['post_job'];

						$nivel = $date_job['pozitia_ierarhica'];

						// locatie job
						$adresa4 = $date_job['locatia_jobului'][1];

						// nivel ierarhic
						$subordonat = $date_job['cui_este_subordonat'];
						$subordonati = $date_job['subordonati_total'];
						$nivelierarhic = $date_job['nivelul_ierarhic'];

						// salariu oferit job
						$salariu = $date_job['salariu_oferit_job'];

						// domenii job
						$domenii = $date_job['domeniu'];

						$listaDomenii = '';

						foreach ($domenii as $id) {
							$domeniu = get_term_by('id', $id, 'sector');
							$listaDomenii .= $domeniu->name . ', ';
						}

						$listaDomenii = substr($listaDomenii, 0, -2);



						// work in progress
						$application_deadline = get_post_meta($post_id, 'jobsearch_field_job_application_deadline_date', true);
						$jobsearch_job_posted = get_post_meta($post_id, 'jobsearch_field_job_publish_date', true);
						$current_date = strtotime(current_time('d-m-Y H:i:s'));

						$job_employer_id = $companie_id;

						$user_id = get_current_user_id();
						$user_is_candidate = jobsearch_user_is_candidate($user_id);
						?>
                        <div class="col-12 jobslist-job" data-job="<?= $job_id; ?>">
                            <div class="row custom-height">
                                <div class="col-sm-2">
                                    <div class="wrapper-logo-company">
                                        <img src="<?= $logo; ?>"/>
                                    </div>
                                </div>
                                <div class="col-sm-7">
                                    <div class="company-name">
                                        <a href="<?php the_permalink(); ?>"><h2><?= $companie ?>
                                            </h2>
                                            <h3 class="job-position"><?= $job; ?></h3></a>
                                    </div>
                                </div>
                                <div class="col-sm-3">

									<?php
									$current_user = wp_get_current_user();
									$user_id = get_current_user_id();
									$user_obj = get_user_by('ID', $user_id);

									$employer_id = jobsearch_get_user_employer_id($user_id);
									if ($employer_id > 0):

									else:
										the_apply_button($job_id, $application_deadline, $job_employer_id);
									endif;
									?>

									<?php
									if ($job_type_str != '') {
										echo force_balance_tags($job_type_str);
									}
									?>

                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-2">
                                    <div class="wrapper-company-details">
                                        <?php esc_html_e('Detalii companie', 'managero'); ?>
                                    </div>
                                </div>
                                <div class="col-sm-7">
                                    <div class="wrapper-text-details-company">
                                        <b>Orgine</b>: <?= $adresa2 ?>
                                        |  <b>Extindere</b>: <?= $extindere; ?>
                                        |  <b>Nr angajati</b>: <?= $angajati; ?>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-2">
                                    <div class="wrapper-company-details">
                                        <?php esc_html_e('Detalii job', 'managero'); ?>
                                    </div>
                                </div>
                                <div class="col-sm-7">
                                    <div class="wrapper-text-details-company">
                                        <b>Locatie</b>: <?= $adresa4; ?> | <b>Domeniu de activitate</b>:  <?= $listaDomenii; ?><br>
                                        <b>Nivel ierarhic</b>: <?= $nivelierarhic; ?> | <b>Subordonat lui</b>: <?= $subordonat; ?><br>
                                        <b>Subordonati</b>:  <?= $subordonati; ?>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-2">
                                    <div class="wrapper-company-details">
                                        <?php esc_html_e('Salariu', 'managero'); ?>
                                    </div>
                                </div>
                                <div class="col-sm-7">
                                    <div class="wrapper-text-details-company">
                                        <?= $salariu; ?> &euro;
                                    </div>
                                </div>
                            </div>
                        </div>
					<?php }
				else:
					echo "<h2 class='noJobs'  style='margin-bottom: 30px;'>Momentan nu ai selectat joburi in aceasta sectiune.</h2>";
				endif; ?>
            </div>
        </div>


			<?php
		}

		if ( $view == 'direct' || $view == 'all' && (0 == 1) ) { ?>
        <div class='alerta-list'>
            <a href="<?php echo home_url( $wp->request ) . '?tab=all-jobs&view=direct'; ?>">
                <h4 class="titluLista"><?php echo apply_filters( 'jobsearch_dash_applid_jobs_tab_main_title', esc_html__( 'Am aplicat direct', 'wp-jobsearch' ) ) ?> (<?= count($candidate_apd_jobs_liste); ?>)
                </h4>
            </a>
            <div class="listaDirect">
				<?php
				if ( ! empty( $candidate_apd_jobs_list ) && isset( $candidate_rej_jobs_list ) ):
					//   var_dump($candidate_apd_jobs_list);
					foreach ( $candidate_apd_jobs_list as $job_key => $job_val ) {

						$post_id = $job_id = $job_val;

						// get job id
						$job_id = $job_val;
						$post_id = $job_id;

						// get job user
						$companie_id = get_the_author_meta('ID');

						// get user post
						$employer_id = jobsearch_get_user_employer_id($companie_id);

						// returneaza date job
						$date_job = get_post_meta($post_id,'job_data_set', true);

						// returneza id imagine
						$logo = $date_job['imagine_profil_companie'];
						// returneaza url imagine
						$logo = wp_get_attachment_url($logo);

						// date companie
						$companie = $date_job['nume_companie'];
						$extindere = $date_job['extindere'];
						$adresa2 = $date_job['origine'][1];
						$angajati = $date_job['numar_angajati'];

						// titlu job
						$job = $date_job['post_job'];

						$nivel = $date_job['pozitia_ierarhica'];

						// locatie job
						$adresa4 = $date_job['locatia_jobului'][1];

						// nivel ierarhic
						$subordonat = $date_job['cui_este_subordonat'];
						$subordonati = $date_job['subordonati_total'];
						$nivelierarhic = $date_job['nivelul_ierarhic'];

						// salariu oferit job
						$salariu = $date_job['salariu_oferit_job'];

						// domenii job
						$domenii = $date_job['domeniu'];

						$listaDomenii = '';

						foreach ($domenii as $id) {
							$domeniu = get_term_by('id', $id, 'sector');
							$listaDomenii .= $domeniu->name . ', ';
						}

						$listaDomenii = substr($listaDomenii, 0, -2);



						// work in progress
						$application_deadline = get_post_meta($post_id, 'jobsearch_field_job_application_deadline_date', true);
						$jobsearch_job_posted = get_post_meta($post_id, 'jobsearch_field_job_publish_date', true);
						$current_date = strtotime(current_time('d-m-Y H:i:s'));

						$job_employer_id = $companie_id;

						$user_id = get_current_user_id();
						$user_is_candidate = jobsearch_user_is_candidate($user_id);

						?>
                        <div class="col-12 jobslist-job" data-job="<?= $job_id; ?>">
                            <div class="row custom-height">
                                <div class="col-sm-2">
                                    <div class="wrapper-logo-company">
                                        <img src="<?= $logo; ?>"/>
                                    </div>
                                </div>
                                <div class="col-sm-7">
                                    <div class="company-name">
                                        <a href="<?php the_permalink(); ?>"><h2><?= $companie ?>
                                            </h2>
                                            <h3 class="job-position"><?= $job; ?></h3></a>
                                    </div>
                                </div>
                                <div class="col-sm-3">

									<?php
									$current_user = wp_get_current_user();
									$user_id = get_current_user_id();
									$user_obj = get_user_by('ID', $user_id);

									$employer_id = jobsearch_get_user_employer_id($user_id);
									if ($employer_id > 0):

									else:
										the_apply_button($job_id, $application_deadline, $job_employer_id);
									endif;
									?>

									<?php
									if ($job_type_str != '') {
										echo force_balance_tags($job_type_str);
									}
									?>

                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-2">
                                    <div class="wrapper-company-details">
                                        <?php esc_html_e('Detalii companie', 'managero'); ?>
                                    </div>
                                </div>
                                <div class="col-sm-7">
                                    <div class="wrapper-text-details-company">
                                        <b>Orgine</b>: <?= $adresa2 ?>
                                        |  <b>Extindere</b>: <?= $extindere; ?>
                                        |  <b>Nr angajati</b>: <?= $angajati; ?>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-2">
                                    <div class="wrapper-company-details">
                                        <?php esc_html_e('Detalii job', 'managero'); ?>
                                    </div>
                                </div>
                                <div class="col-sm-7">
                                    <div class="wrapper-text-details-company">
                                        <b>Locatie</b>: <?= $adresa4; ?> | <b>Domeniu de activitate</b>:  <?= $listaDomenii; ?><br>
                                        <b>Nivel ierarhic</b>: <?= $nivelierarhic; ?> | <b>Subordonat lui</b>: <?= $subordonat; ?><br>
                                        <b>Subordonati</b>:  <?= $subordonati; ?>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-2">
                                    <div class="wrapper-company-details">
                                        <?php esc_html_e('Salariu', 'managero'); ?>
                                    </div>
                                </div>
                                <div class="col-sm-7">
                                    <div class="wrapper-text-details-company">
                                        <?= $salariu; ?> &euro;
                                    </div>
                                </div>
                            </div>
                        </div>
					<?php }
				else:

					echo "<h2 class='noJobs'  style='margin-bottom: 30px;'>Momentan nu ai selectat joburi in aceasta sectiune.</h2>";

				endif; ?>
            </div>
        </div>
			<?php
		}


		?>
    </div>
</div>

