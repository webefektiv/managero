<?php
global $jobsearch_plugin_options, $Jobsearch_User_Dashboard_Settings;
$user_id  = get_current_user_id();
$user_obj = get_user_by( 'ID', $user_id );

$page_id  = $user_dashboard_page = isset( $jobsearch_plugin_options['user-dashboard-template-page'] ) ? $jobsearch_plugin_options['user-dashboard-template-page'] : '';
$page_id  = $user_dashboard_page = jobsearch__get_post_id( $user_dashboard_page, 'page' );
$page_url = jobsearch_wpml_lang_page_permalink( $page_id, 'page' ); //get_permalink($page_id);

$all_location_allow = isset( $jobsearch_plugin_options['all_location_allow'] ) ? $jobsearch_plugin_options['all_location_allow'] : '';

if ( jobsearch_user_isemp_member( $user_id ) ) {
	$employer_id = jobsearch_user_isemp_member( $user_id );
} else {
	$employer_id = jobsearch_get_user_employer_id( $user_id );
}

$_employer_id = $employer_id;

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
			array(
				'key'     => 'jobsearch_field_package_type',
				'value'   => 'feature_job',
				'compare' => '=',
			),
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
				'key'     => 'jobsearch_field_job_posted_by',
				'value'   => $employer_id,
				'compare' => '=',
			),
		),
	);

	if ( isset( $_GET['keyword'] ) && $_GET['keyword'] != '' ) {
		$args['s'] = sanitize_text_field( $_GET['keyword'] );
	}

	$jobs_query = new WP_Query( $args );

	$total_jobs = $jobs_query->found_posts;
	?>
    <div class="jobsearch-employer-dasboard">
        <div class="jobsearch-employer-box-section">
			<?php
			if ( isset( $_GET['view'] ) && $_GET['view'] == 'applicants' && isset( $_GET['job_id'] ) && $_GET['job_id'] > 0 ) {
				$_job_id = $_GET['job_id'];

				$job_applicants_list = get_post_meta( $_job_id, 'jobsearch_job_applicants_list', true );
				// print_r($job_applicants_list);
				$job_applicants_list = jobsearch_is_post_ids_array( $job_applicants_list, 'candidate' );
				if ( empty( $job_applicants_list ) ) {
					$job_applicants_list = array();
				}

				$job_applicants_count = ! empty( $job_applicants_list ) ? count( $job_applicants_list ) : 0;

				$viewed_candidates = get_post_meta( $_job_id, 'jobsearch_viewed_candidates', true );
				if ( empty( $viewed_candidates ) ) {
					$viewed_candidates = array();
				}
				$viewed_candidates = jobsearch_is_post_ids_array( $viewed_candidates, 'candidate' );

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




				$applicants_mange_view = get_post_meta( $employer_id, 'applicants_mange_view', true );

				$_selected_view = isset( $_GET['ap_view'] ) && $_GET['ap_view'] != '' ? $_GET['ap_view'] : $applicants_mange_view;
				if ( $applicants_mange_view != '' && $applicants_mange_view != $_selected_view ) {
					update_post_meta( $employer_id, 'applicants_mange_view', $_selected_view );
					$_selected_view = get_post_meta( $employer_id, 'applicants_mange_view', true );
				}

				$_mod_tab       = isset( $_GET['mod'] ) && $_GET['mod'] != '' ? $_GET['mod'] : 'applicants';
				$_sort_selected = isset( $_GET['sort_by'] ) && $_GET['sort_by'] != '' ? $_GET['sort_by'] : '';

				ob_start();
				?>
                <div class="jobsearch-profile-title">
                    <h2><?php printf( esc_html__( 'Job "%s" Applicants', 'wp-jobsearch' ), get_the_title( $_job_id ) ) ?></h2>
                </div>
				<?php
				$apps_title_html = ob_get_clean();
				echo apply_filters( 'jobseacrh_dash_manag_apps_maintitle_html', $apps_title_html, $_job_id );
				?>
                <div class="jobsearch-applicants-tabs">
                    <script>
                        jQuery(document).on('click', '.jobsearch-modelemail-btn-<?php echo( $_job_id ) ?>', function () {
                            jobsearch_modal_popup_open('JobSearchModalSendEmail<?php echo( $_job_id ) ?>');
                        });
                    </script>
                    <ul class="tabs-list">

                        <!--                        modificari tabel lsita aplicati la job-->
                        <li <?php echo( $_mod_tab == '' || $_mod_tab == 'applicants' ? 'class="active"' : '' ) ?>><a
                                    href="<?php echo add_query_arg( array(
										'tab'    => 'manage-jobs',
										'view'   => 'applicants',
										'job_id' => $_job_id
									), $page_url ) ?>"><?php printf( esc_html__( 'Candidati noi (%s)', 'wp-jobsearch' ), $job_applicants_count ) ?></a>
                        </li>

                        <li <?php

						$procesati = $job_short_int_list_c + $job_reject_int_list_c + $job_reserved_int_list_c;
						echo( $_mod_tab == 'procesati' ? 'class="active"' : '' ) ?>><a
                                    href="<?php echo add_query_arg( array(
										'tab'    => 'manage-jobs',
										'view'   => 'applicants',
										'job_id' => $_job_id,
										'mod'    => 'procesati'
									), $page_url ) ?>"><?php printf( esc_html__( 'Candidati procesati (%s)', 'wp-jobsearch' ), $procesati ) ?></a>
                        </li>

                    </ul>
                    <div class="applied-jobs-sort" style="display: none">
						<?php
						ob_start();
						?>
                        <div class="sort-by-option">
                            <form id="jobsearch-applicants-form" method="get">
                                <input type="hidden" name="tab" value="manage-jobs">
                                <input type="hidden" name="view" value="applicants">
                                <input type="hidden" name="job_id" value="<?php echo absint( $_job_id ) ?>">
                                <input type="hidden" name="mod" value="<?php echo( $_mod_tab ) ?>">
                                <input type="hidden" name="ap_view" value="<?php echo( $_selected_view ) ?>">
								<?php
								if ( isset( $_GET['page_num'] ) && $_GET['page_num'] != '' ) {
									?>
                                    <input type="hidden" name="page_num" value="<?php echo( $_GET['page_num'] ) ?>">
									<?php
								}
								?>
                                <select id="jobsearch-applicants-sort" class="selectize-select"
                                        placeholder="<?php esc_html_e( 'Sort by', 'wp-jobsearch' ) ?>" name="sort_by">
                                    <option value=""><?php esc_html_e( 'Sort by', 'wp-jobsearch' ) ?></option>
                                    <option value="recent"<?php echo( $_sort_selected == 'recent' ? ' selected="selected"' : '' ) ?>><?php esc_html_e( 'Data', 'wp-jobsearch' ) ?></option>
                                    <option value="alphabetic"<?php echo( $_sort_selected == 'alphabetic' ? ' selected="selected"' : '' ) ?>><?php esc_html_e( 'Alfabetic', 'wp-jobsearch' ) ?></option>
                                    <option value="salary"<?php echo( $_sort_selected == 'salary' ? ' selected="selected"' : '' ) ?>><?php esc_html_e( 'Salariu minim', 'wp-jobsearch' ) ?></option>
                                    <!--                                    <option value="viewed"-->
									<?php //echo( $_sort_selected == 'viewed' ? ' selected="selected"' : '' ) ?><!-->-->
									<?php //esc_html_e( 'Vizualizari', 'wp-jobsearch' ) ?><!--</option>-->
                                    <!--                                    <option value="unviewed"-->
									<?php //echo( $_sort_selected == 'unviewed' ? ' selected="selected"' : '' ) ?><!-->
                                    --><?php //esc_html_e( 'Unviewed', 'wp-jobsearch' ) ?><!--</option>-->
                                </select>

                            </form>
                        </div>
						<?php
						$sort_by_dropdown = ob_get_clean();
						$sort_by_args     = array(
							'job_id'        => $_job_id,
							'sort_selected' => $_sort_selected,
							'mob_tab'       => $_mod_tab,
							'selected_view' => $_selected_view,
						);
						echo apply_filters( 'jobsearch_applicants_sortby_dropdown', $sort_by_dropdown, $sort_by_args );
						?>

						<?php
						ob_start();
						?>
                        <div class="sort-list-view">
                            <a href="javascript:void(0);"
                               class="apps-view-btn<?php echo( $_selected_view == 'list' ? ' active' : '' ) ?>"
                               data-view="list"><i class="fa fa-list"></i></a>
                            <a href="javascript:void(0);"
                               class="apps-view-btn<?php echo( $_selected_view == 'grid' ? ' active' : '' ) ?>"
                               data-view="grid"><i class="fa fa-bars"></i></a>
                        </div>
						<?php
						$app_viewbtns_html = ob_get_clean();
						echo apply_filters( 'jobseacrh_dash_manag_apps_viewbtns_html', $app_viewbtns_html, $_selected_view );
						?>
                    </div>
					<?php


					//  var_dump(jobsearch_job_applicants_sort_list( $_job_id, $_sort_selected ));

					if ( $_mod_tab == 'procesati' ) {

						$liste_candidati = [
							'shortlist' => jobsearch_job_applicants_sort_list( $_job_id, $_sort_selected, '_job_short_interview_list' ),
							'rezerved'  => jobsearch_job_applicants_sort_list( $_job_id, $_sort_selected, '_job_reserved_interview_list' ),
							'rejected'  => jobsearch_job_applicants_sort_list( $_job_id, $_sort_selected, '_job_reject_interview_list' )
						];

					} else {

						$lista_full = jobsearch_job_applicants_sort_list( $_job_id, $_sort_selected );

						$filtre = get_field( 'filtre_candidati', $_job_id );

						$lista_filtru_ok = [];
						$lista_filtru_nu = [];


						foreach ( $lista_full as $candidat ) {

							$date_profil   = get_field( 'date_personale', $candidat );
							$anul_nasterii = $date_profil['anul_nasterii'];
							$varsta        = ( date( 'Y' ) - $anul_nasterii ) . ' ani';


							$sex           = $date_profil['sex'];
							$salariu_minim = get_field( 'salariu_minim_accepta', $candidat );

							if ( $varsta > $filtre['varsta_scm'] && $salariu_minim < $filtre['salariu_minim_scm'] ) {

								$lista_filtru_ok[] = $candidat;

							} else {
								$lista_filtru_nu[] = $candidat;
							}

						}

						//	var_dump($lista_filtru_nu);
						//	var_dump($lista_filtru_ok);


						$liste_candidati = [
							'lista_ok' => $lista_filtru_ok,
							'lista_nu' => $lista_filtru_nu
						];

						$total_records = ! empty( $job_applicants_list ) ? count( $job_applicants_list ) : 0;

						$start               = ( $page_num - 1 ) * ( $reults_per_page );
						$offset              = $reults_per_page;
						$job_applicants_list = array_slice( $job_applicants_list, $start, $offset );
					}


					?>
					<?php
					//   var_dump($liste_candidati);
					foreach ( $liste_candidati as $key => $candidati ):
						$job_applicants_list = $candidati;
						?>
                        <div class="jobsearch-applied-jobs <?php echo( $_selected_view == 'grid' ? 'aplicants-grid-view' : '' ) ?>">

                            <div class="visual-header">
                                <h3>
									<?php if ( $key == 'shortlist' ):
										echo 'Ok';
                                    elseif ( $key == 'rezerved' ):
										echo 'Rezerve';
                                    elseif ( $key == 'rejected' ):
										echo 'Respinsi';
                                    elseif ( $key == 'lista_ok' ):
										echo 'Candidati trecuti de filtru';
                                    elseif ( $key == 'lista_nu' ):
										echo 'Candidati care nu au trecut de filtru';
									endif;
									?>
                                </h3>
                                <a href="javascript:void(0);" class="edit-preview list-edit"
                                   data-field="editCandidatiList-<?= $key; ?>">editeaza
                                    sectiunea</a>
                            </div>
							<?php
							if ( ! empty( $job_applicants_list ) ) {
								?>
                                <script>
                                    jQuery(function () {
                                        jQuery('.jobsearch-apppli-tooltip').tooltip();
                                    });
                                </script>

                                <!--                            aici incepe tabel lista candidati-->


                                <table class="tabel-candidati">
                                    <tbody>
                                    <tr>
                                        <th>Nume</th>
                                        <th>Varsta</th>
                                        <th>Sex</th>
                                        <th>Salariu</th>
                                        <th>Ultimul angajator</th>
                                        <th>Ultimul job</th>
                                        <th>Note</th>
                                        <th class="editCandidatiList-<?= $key; ?>" style="display: none;">Actiuni</th>
                                    </tr>

									<?php
									foreach ( $job_applicants_list as $_candidate_id ) {
										$candidate_user_id = jobsearch_get_candidate_user_id( $_candidate_id );
										if ( absint( $candidate_user_id ) <= 0 ) {
											continue;
										}

										$sectors                = wp_get_post_terms( $_candidate_id, 'sector' );
										$candidate_sector       = isset( $sectors[0]->name ) ? $sectors[0]->name : '';
										$send_message_form_rand = rand( 100000, 999999 );

										$date_profil   = get_field( 'date_personale', $_candidate_id );
										$salariu_minim = get_field( 'salariu_minim_accepta', $_candidate_id );
										$nume          = $date_profil['titlu'] . ' ' . $date_profil['prenume'] . ' ' . $date_profil['nume'];

										$anul_nasterii = $date_profil['anul_nasterii'];
										$varsta        = ( date( 'Y' ) - $anul_nasterii ) . ' ani';

										$sex         = $date_profil['sex'];
										$experienta  = get_field( 'experienta', $_candidate_id );
										$ultimul_job = '';
										//var_dump($sex);
										foreach ( $experienta as $companie ) {
											foreach ( $companie['post'] as $post ) {
												if ( $post['ultimul_job'] != '' && isset( $post['ultimul_job'] ) ) {
													$ultimul_job = [
														'companie' => $companie['companie'],
														'post'     => $post['post'],

													];
												}
											}
										}
										$note = 'Lorem ipsum';

										?>

                                        <tr class="jobsearch-table-row">
                                            <td><a href="<?php echo add_query_arg( array(
													'job_id'      => $_job_id,
													'employer_id' => $employer_id,
													'action'      => 'preview_profile'
												), get_permalink( $_candidate_id ) ) ?>"
                                                   class="" target="_blank"><?= $nume; ?></a></td>
                                            <td><?= $varsta; ?></td>
                                            <td><?= $sex; ?></td>
                                            <td><?= $salariu_minim; ?></td>
                                            <td><?= $ultimul_job['companie']; ?></td>
                                            <td><?= $ultimul_job['post']; ?></td>
                                            <td style=" padding: 10px 15px;">
                                                <span class="editCandidatiList-<?= $key; ?>" style="display:block;">
                                                    <?= $note; ?>
                                                </span>
                                                <input type="text" data-job="<?= $_job_id; ?>"
                                                       data-candidat="<?= $_candidate_id; ?>" value="<?= $note; ?>"
                                                       style="width: 100%; display: none; "
                                                       class="notacandidat-<?= $key; ?> editCandidatiList-<?= $key; ?>">
                                            </td>
                                            <td class="editCandidatiList-<?= $key; ?>"
                                                style="width: 130px; display: none;">
												<?php ob_start(); ?>
												<?php if ( in_array( $_candidate_id, $job_short_int_list ) ) { ?>


												<?php } else { ?>
                                                    <a href="javascript:void(0);"
                                                       class="shortlist-cand-to-intrview ajax-enable companie-icons"
                                                       data-toggle="tooltip"
                                                       data-jid="<?php echo absint( $_job_id ); ?>"
                                                       data-cid="<?php echo absint( $_candidate_id ); ?>"
                                                       title="Adauga la candidati ok">
                                                        <i class="fa fa-check"></i>
                                                        <span class="app-loader"></span>
                                                    </a>

												<?php }
												if ( in_array( $_candidate_id, $job_reserved_int_list ) ) { ?>

												<?php } else { ?>

                                                    <a href="javascript:void(0);"
                                                       class="reserved-cand-to-intrview ajax-enable companie-icons"
                                                       data-toggle="tooltip"
                                                       data-jid="<?php echo absint( $_job_id ); ?>"
                                                       data-cid="<?php echo absint( $_candidate_id ); ?>"
                                                       title="Adauga la candidati rezervati">
                                                        <i class="fa fa-history"></i>
                                                        <span class="app-loader"></span></a>

												<?php }
												if ( in_array( $_candidate_id, $job_reject_int_list ) ) { ?>


												<?php } else { ?>

                                                    <a href="javascript:void(0);"
                                                       class="reject-cand-to-intrview ajax-enable companie-icons"
                                                       data-toggle="tooltip"
                                                       data-jid="<?php echo absint( $_job_id ); ?>"
                                                       data-cid="<?php echo absint( $_candidate_id ); ?>"
                                                       title="Adauga la lista de candidati respinsi">
                                                        <i class="fa fa-close"></i>
                                                        <span class="app-loader"></span></a>

												<?php }; ?>

                                                <a href="javascript:void(0);"
                                                   class="delete-cand-from-job ajax-enable companie-icons"
                                                   data-toggle="tooltip"
                                                   data-jid="<?php echo absint( $_job_id ); ?>"
                                                   data-cid="<?php echo absint( $_candidate_id ); ?>"
                                                   title="Sterge candidatul din liste">
                                                    <i class="fa fa-trash"></i>
                                                    <span class="app-loader"></span></a>

                                                <a href="javascript:void(0);"
                                                   class="blacklist-candidate ajax-enable companie-icons"
                                                   data-toggle="tooltip"
                                                   data-jid="<?php echo absint( $_job_id ); ?>"
                                                   data-cid="<?php echo absint( $_candidate_id ); ?>"
                                                   data-eid="<?php echo absint( $_employer_id ); ?>"
                                                   title="Adauga la blacklist">
                                                    <i class="fa fa-box"></i>
                                                    <span class="app-loader"></span>
                                                </a>

												<?php
												$app_actbtns_html = ob_get_clean();
												echo apply_filters( 'jobseacrh_dash_manag_apps_actbtns_html', $app_actbtns_html, $_candidate_id, $_job_id, $employer_id, $send_message_form_rand );
												?>
                                            </td>
                                        </tr>
										<?php
									}
									?>
                                    </tbody>
                                </table>
                                <div class="acf-form-submit  editCandidatiList-<?= $key; ?>"
                                     style="float:left; width: 100%; display: none;">
                                    <input type="submit" class="acf-button button button-primary button-large"
                                           value="Actualizează" id="saveNota-<?= $key; ?>">
                                    <span class="acf-spinner loadAplicatii" style="margin-top: 15px;"></span>
                                </div>
                                <script>
                                    var unsaved = false;
                                    jQuery('#saveNota-<?= $key; ?>').click(function () {
                                        var listaJobs = [];
                                        jQuery('.loadAplicatii').css('display', 'block');
                                        jQuery('.notacandidat-<?= $key; ?>').each(function () {
                                            var nota = jQuery(this).val();
                                            var job = jQuery(this).attr('data-job');
                                            var candidat = jQuery(this).attr('data-candidat');
                                            var item = {
                                                'candidat_id': candidat,
                                                'job_id': job, 
                                                'nota': nota
                                            };
                                            listaJobs.push(item);
                                        });
                                        console.log(listaJobs);
                                        jQuery.ajax({
                                            type: "post",
                                            url: "<?=  admin_url( 'admin-ajax.php' ); ?>",
                                            data: {
                                                'action': 'candidate_save_note',
                                                'job': <?= $_job_id; ?>
                                            },
                                            success: function (data) {
                                                unsaved = false;
                                                console.log(data);
                                                location.reload();
                                            }
                                            ,
                                            error: function (errorThrown) {
                                                console.log(errorThrown);
                                            }
                                        })
                                        ;
                                    });
                                    jQuery('.notacandidat-<?= $key; ?>').change(function () {
                                        unsaved = true;
                                    });

                                    function unloadPage() {
                                        if (unsaved) {
                                            return "You have unsaved changes on this page.";
                                        }
                                    }

                                    window.onbeforeunload = unloadPage;

                                </script>

								<?php
							}
							?>

                        </div>

						<?php
					endforeach;
					?>



					<?php
					if ( ! empty( $job_applicants_list ) ) {
						$total_pages = 1;
						if ( $total_records > 0 && $reults_per_page > 0 && $total_records > $reults_per_page ) {
							$total_pages = ceil( $total_records / $reults_per_page );
							?>
                            <div class="jobsearch-pagination-blog">
								<?php $Jobsearch_User_Dashboard_Settings->pagination( $total_pages, $page_num, $page_url ) ?>
                            </div>
							<?php
						}
					}
					?>
                </div>
			<?php
			} else {

			//lista joburi din companie
			//


			?>
                <div class="jobsearch-profile-title">
                    <h2><?php echo apply_filters( 'jobsearch_emp_dash_manage_jobs_maintitle', esc_html__( 'Manage Jobs', 'wp-jobsearch' ) ) ?></h2>
					<?php
					if ( $jobs_query->have_posts() ) {
						?>
                        <form method="get" class="jobsearch-employer-search" action="<?php echo( $page_url ) ?>">
                            <input type="hidden" name="tab" value="manage-jobs">
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


                <div class="jobsearch-jobs-list-holder 2pacshakur">
                    <div class="jobsearch-managejobs-list">
                        <h5>Anunturi active</h5>
                        <!-- Manage Jobs Header -->
                        <div class="jobsearch-table-layer jobsearch-managejobs-thead">
                            <div class="jobsearch-table-row">
                                <div class="jobsearch-table-cell"><?php esc_html_e( 'Job', 'wp-jobsearch' ) ?></div>
                                <div class="jobsearch-table-cell"><?php esc_html_e( 'Noi', 'wp-jobsearch' ) ?></div>
                                <div class="jobsearch-table-cell"><?php esc_html_e( 'OK', 'wp-jobsearch' ) ?></div>
                                <div class="jobsearch-table-cell"><?php esc_html_e( 'Rezerve', 'wp-jobsearch' ) ?></div>
                                <div class="jobsearch-table-cell"><?php esc_html_e( 'Nu', 'wp-jobsearch' ) ?></div>
                                <!--                                    <div class="jobsearch-table-cell">-->
								<?php //esc_html_e('Comentarii', 'wp-jobsearch') ?><!--</div>-->
                                <div class="jobsearch-table-cell"></div>
                            </div>
                        </div>
						<?php
						while ( $jobs_query->have_posts() ) : $jobs_query->the_post();
							$job_id = get_the_ID();

							$status_job = get_post_meta( $job_id, 'staus_job_1', true );


							if ( $status_job != 'activ' ) {
								continue;
							}

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


                            <div class="jobsearch-table-layer jobsearch-managejobs-tbody">
                                <div class="jobsearch-table-row">
                                    <div class="jobsearch-table-cell">
                                        <h6>
                                            <a href="<?php echo get_permalink( $job_id ) ?>"><?php echo get_the_title() ?></a>
                                            <span class="job-filled"><?php echo( $job_filled == 'on' ? esc_html__( '(Filled)', 'wp-jobsearch' ) : '' ) ?></span>
                                        </h6>
                                    </div>

                                    <div class="jobsearch-table-cell"><a <?php echo( 'href="' . add_query_arg( array(
												'tab'    => 'manage-jobs',
												'view'   => 'applicants',
												'job_id' => $job_id
											), $page_url ) . '"' ) ?>
                                                class="jobsearch-managejobs-appli"><?= $job_applicants_count; ?></a>
                                    </div>
                                    <div class="jobsearch-table-cell"><a <?php echo( 'href="' . add_query_arg( array(
												'tab'    => 'manage-jobs',
												'view'   => 'applicants',
												'job_id' => $job_id
											), $page_url ) . '&mod=shortlisted"' ) ?>
                                                class="jobsearch-managejobs-appli2"><?= $job_short_int_list_c; ?></a>
                                    </div>

                                    <div class="jobsearch-table-cell"><a <?php echo( 'href="' . add_query_arg( array(
												'tab'    => 'manage-jobs',
												'view'   => 'applicants',
												'job_id' => $job_id
											), $page_url ) . '&mod=reserved"' ) ?>
                                                class="jobsearch-managejobs-appl4"><?= $job_reserved_int_list_c; ?></a>
                                    </div>

                                    <div class="jobsearch-table-cell"><a <?php echo( 'href="' . add_query_arg( array(
												'tab'    => 'manage-jobs',
												'view'   => 'applicants',
												'job_id' => $job_id
											), $page_url ) . '&mod=shortlisted"' ) ?>
                                                class="jobsearch-managejobs-appli3"><?= $job_reject_int_list_c; ?></a>
                                    </div>


									<?php
									ob_start();
									?>
                                    <!--                                        <div class="jobsearch-table-cell"></div>-->
                                    <div class="jobsearch-table-cell">
                                        <div class="jobsearch-managejobs-links">
                                            <a href="<?php echo get_permalink( $job_id ) ?>"
                                               class="jobsearch-icon jobsearch-view"></a>
                                            <a href="<?php echo add_query_arg( array(
												'tab'    => 'user-job',
												'job_id' => $job_id,
												'action' => 'update'
											), $page_url ) ?>" class="jobsearch-icon jobsearch-edit"></a>
                                            <a href="javascript:void(0);" data-id="<?php echo( $job_id ) ?>"
                                               class="jobsearch-icon jobsearch-rubbish jobsearch-trash-job"></a>
                                        </div>
                                    </div>

                                    <div class="jobsearch-table-cell" style="display: none">
										<?php
										$job_status = get_post_meta( $job_id, 'jobsearch_field_job_status', true );
										if ( $job_status == 'approved' ) {
											?>
                                            <div class="jobsearch-filledjobs-links">
												<?php
												if ( $job_filled == 'on' ) {
													?>
                                                    <a class="jobsearch-fill-the-job"
                                                       title="<?php esc_html_e( 'Filled Job', 'wp-jobsearch' ) ?>"><span></span><i
                                                                class="fa fa-check"></i></a>
													<?php
												} else {
													?>
                                                    <a href="javascript:void(0);"
                                                       title="<?php esc_html_e( 'Fill this Job', 'wp-jobsearch' ) ?>"
                                                       data-id="<?php echo( $job_id ) ?>"
                                                       class="jobsearch-fill-the-job ajax-enable"><span></span><span
                                                                class="fill-job-loader"></span></a>
													<?php
												}
												?>
                                            </div>
											<?php
										}
										?>
                                    </div>
									<?php
									$actions_html = ob_get_clean();
									echo apply_filters( 'jobsearch_empdash_managejobs_list_actions', $actions_html, $job_id, $page_url );
									?>
                                </div>
                            </div>
							<?php
						endwhile;
						wp_reset_postdata();
						?>
                    </div>
                </div>

                <div class="jobsearch-jobs-list-holder 2pacshakur">
                    <div class="jobsearch-managejobs-list">
                        <h5>Anunturi inactive</h5>
                        <!-- Manage Jobs Header -->
                        <div class="jobsearch-table-layer jobsearch-managejobs-thead">
                            <div class="jobsearch-table-row">
                                <div class="jobsearch-table-cell"><?php esc_html_e( 'Job', 'wp-jobsearch' ) ?></div>
                                <div class="jobsearch-table-cell"><?php esc_html_e( 'OK', 'wp-jobsearch' ) ?></div>
                                <div class="jobsearch-table-cell"><?php esc_html_e( 'Rezerve', 'wp-jobsearch' ) ?></div>
                                <div class="jobsearch-table-cell"><?php esc_html_e( 'Nu', 'wp-jobsearch' ) ?></div>
                                <!--                                    <div class="jobsearch-table-cell">-->
								<?php //esc_html_e('Comentarii', 'wp-jobsearch') ?><!--</div>-->
                                <div class="jobsearch-table-cell"></div>
                            </div>
                        </div>
						<?php
						while ( $jobs_query->have_posts() ) : $jobs_query->the_post();
							$job_id = get_the_ID();

							$status_job = get_post_meta( $job_id, 'staus_job_1', true );

							if ( $status_job == 'activ' ) {
								continue;
							}


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


                            <div class="jobsearch-table-layer jobsearch-managejobs-tbody">
                                <div class="jobsearch-table-row">
                                    <div class="jobsearch-table-cell">
                                        <h6>
                                            <a href="<?php echo get_permalink( $job_id ) ?>"><?php echo get_the_title() ?></a>
                                            <span class="job-filled"><?php echo( $job_filled == 'on' ? esc_html__( '(Filled)', 'wp-jobsearch' ) : '' ) ?></span>
                                        </h6>
                                    </div>

                                    <div class="jobsearch-table-cell"><a <?php echo( 'href="' . add_query_arg( array(
												'tab'    => 'manage-jobs',
												'view'   => 'applicants',
												'job_id' => $job_id
											), $page_url ) . '&mod=shortlisted"' ) ?>
                                                class="jobsearch-managejobs-appli2"><?= $job_short_int_list_c; ?></a>
                                    </div>
                                    <div class="jobsearch-table-cell"><a <?php echo( 'href="' . add_query_arg( array(
												'tab'    => 'manage-jobs',
												'view'   => 'applicants',
												'job_id' => $job_id
											), $page_url ) . '&mod=reserved"' ) ?>
                                                class="jobsearch-managejobs-appli3"><?= $job_reserved_int_list_c; ?></a>
                                    </div>
                                    <div class="jobsearch-table-cell"><a <?php echo( 'href="' . add_query_arg( array(
												'tab'    => 'manage-jobs',
												'view'   => 'applicants',
												'job_id' => $job_id
											), $page_url ) . '&mod=reject"' ) ?>
                                                class="jobsearch-managejobs-appl4"><?= $job_reject_int_list_c; ?></a>
                                    </div>

									<?php
									ob_start();
									?>
                                    <!--                                        <div class="jobsearch-table-cell"></div>-->
                                    <div class="jobsearch-table-cell">
                                        <div class="jobsearch-managejobs-links">
                                            <a href="<?php echo get_permalink( $job_id ) ?>"
                                               class="jobsearch-icon jobsearch-view"></a>
                                            <a href="<?php echo add_query_arg( array(
												'tab'    => 'user-job',
												'job_id' => $job_id,
												'action' => 'update'
											), $page_url ) ?>" class="jobsearch-icon jobsearch-edit"></a>
                                            <a href="javascript:void(0);" data-id="<?php echo( $job_id ) ?>"
                                               class="jobsearch-icon jobsearch-rubbish jobsearch-trash-job"></a>
                                        </div>
                                    </div>

                                    <div class="jobsearch-table-cell" style="display: none">
										<?php
										$job_status = get_post_meta( $job_id, 'jobsearch_field_job_status', true );
										if ( $job_status == 'approved' ) {
											?>
                                            <div class="jobsearch-filledjobs-links">
												<?php
												if ( $job_filled == 'on' ) {
													?>
                                                    <a class="jobsearch-fill-the-job"
                                                       title="<?php esc_html_e( 'Filled Job', 'wp-jobsearch' ) ?>"><span></span><i
                                                                class="fa fa-check"></i></a>
													<?php
												} else {
													?>
                                                    <a href="javascript:void(0);"
                                                       title="<?php esc_html_e( 'Fill this Job', 'wp-jobsearch' ) ?>"
                                                       data-id="<?php echo( $job_id ) ?>"
                                                       class="jobsearch-fill-the-job ajax-enable"><span></span><span
                                                                class="fill-job-loader"></span></a>
													<?php
												}
												?>
                                            </div>
											<?php
										}
										?>
                                    </div>
									<?php
									$actions_html = ob_get_clean();
									echo apply_filters( 'jobsearch_empdash_managejobs_list_actions', $actions_html, $job_id, $page_url );
									?>
                                </div>
                            </div>
							<?php
						endwhile;
						wp_reset_postdata();
						?>
                    </div>
                </div>

                <div class="jobsearch-jobs-list-holder 2pacshakur">
                    <div class="jobsearch-managejobs-list">
                        <h5>Template-uri anunturi</h5>
                        <!-- Manage Jobs Header -->
                        <div class="jobsearch-table-layer jobsearch-managejobs-thead">
                            <div class="jobsearch-table-row">
                                <div class="jobsearch-table-cell"><?php esc_html_e( 'Job', 'wp-jobsearch' ) ?></div>
                                <div class="jobsearch-table-cell"><?php esc_html_e( 'Descriere', 'wp-jobsearch' ) ?></div>
                                <!--                                    <div class="jobsearch-table-cell">-->
								<?php //esc_html_e('Comentarii', 'wp-jobsearch') ?><!--</div>-->
                                <div class="jobsearch-table-cell"></div>
                            </div>
                        </div>
						<?php
						while ( $jobs_query->have_posts() ) : $jobs_query->the_post();
							$job_id = get_the_ID();


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


							$job_reserved_int_list = get_post_meta( $_job_id, '_job_reservedt_interview_list', true );
							$job_reserved_int_list = $job_reserved_int_list != '' ? explode( ',', $job_reserved_int_list ) : '';
							if ( empty( $job_reserved_int_list ) ) {
								$job_reserved_int_list = array();
							}
							$job_reserved_int_list   = jobsearch_is_post_ids_array( $job_reserved_int_list, 'candidate' );
							$job_reserved_int_list_c = ! empty( $job_reserved_int_list ) ? count( $job_reserved_int_list ) : 0;


							?>


                            <div class="jobsearch-table-layer jobsearch-managejobs-tbody">
                                <div class="jobsearch-table-row">
                                    <div class="jobsearch-table-cell">
                                        <h6>
                                            <a href="<?php echo get_permalink( $job_id ) ?>"><?php echo get_the_title() ?></a>
                                            <span class="job-filled"><?php echo( $job_filled == 'on' ? esc_html__( '(Filled)', 'wp-jobsearch' ) : '' ) ?></span>
                                        </h6>
                                    </div>

                                    <div class="jobsearch-table-cell">

                                    </div>

									<?php
									ob_start();
									?>
                                    <!--                                        <div class="jobsearch-table-cell"></div>-->
                                    <div class="jobsearch-table-cell">
                                        <div class="jobsearch-managejobs-links">
                                            <a href="<?php echo get_permalink( $job_id ) ?>"
                                               class="jobsearch-icon jobsearch-view"></a>
                                            <a href="<?php echo add_query_arg( array(
												'tab'    => 'user-job',
												'job_id' => $job_id,
												'action' => 'update'
											), $page_url ) ?>" class="jobsearch-icon jobsearch-edit"></a>
                                            <a href="javascript:void(0);" data-id="<?php echo( $job_id ) ?>"
                                               class="jobsearch-icon jobsearch-rubbish jobsearch-trash-job"></a>
                                        </div>
                                    </div>

                                    <div class="jobsearch-table-cell" style="display: none">
										<?php
										$job_status = get_post_meta( $job_id, 'jobsearch_field_job_status', true );
										if ( $job_status == 'approved' ) {
											?>
                                            <div class="jobsearch-filledjobs-links">
												<?php
												if ( $job_filled == 'on' ) {
													?>
                                                    <a class="jobsearch-fill-the-job"
                                                       title="<?php esc_html_e( 'Filled Job', 'wp-jobsearch' ) ?>"><span></span><i
                                                                class="fa fa-check"></i></a>
													<?php
												} else {
													?>
                                                    <a href="javascript:void(0);"
                                                       title="<?php esc_html_e( 'Fill this Job', 'wp-jobsearch' ) ?>"
                                                       data-id="<?php echo( $job_id ) ?>"
                                                       class="jobsearch-fill-the-job ajax-enable"><span></span><span
                                                                class="fill-job-loader"></span></a>
													<?php
												}
												?>
                                            </div>
											<?php
										}
										?>
                                    </div>
									<?php
									$actions_html = ob_get_clean();
									echo apply_filters( 'jobsearch_empdash_managejobs_list_actions', $actions_html, $job_id, $page_url );
									?>
                                </div>
                            </div>
							<?php
						endwhile;
						wp_reset_postdata();
						?>
                    </div>
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
			?>
                <p><?php esc_html_e( 'No job found.', 'wp-jobsearch' ) ?></p>
				<?php
			}
			}
			?>

        </div>
    </div>
    <script>

        jQuery('.edit-preview').toggle(function () {
            var fields = jQuery(this).attr('data-field');
            var visual = jQuery(this).attr('data-visual');
            jQuery('.' + fields).toggle();
            jQuery('.' + visual).toggle();
            jQuery(this).html('mod visual');
             jQuery("td:nth-last-of-type(2)").css("border-right",
                "1px solid #bfbfbf");

        }, function () {
            var fields = jQuery(this).attr('data-field');
            var visual = jQuery(this).attr('data-visual');
            jQuery('.' + fields).toggle();
            jQuery('.' + visual).toggle();
            jQuery(this).html('editeaza sectiunea');

            jQuery("td:nth-last-of-type(2)").css("border-right",
                "1px solid transparent");
        });


    </script>
	<?php
}
