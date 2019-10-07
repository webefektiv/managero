<?php

global $jobsearch_plugin_options, $Jobsearch_User_Dashboard_Settings;
$user_id  = get_current_user_id();
$user_obj = get_user_by( 'ID', $user_id );


if (jobsearch_user_isemp_member($user_id)) {
    $employer_id = jobsearch_user_isemp_member($user_id);
} else {
    $employer_id = jobsearch_get_user_employer_id($user_id);
}


$job_black_int_list = get_post_meta($employer_id, '_job_black_list', true);

$job_black_int_list = $job_black_int_list != '' ? explode(',', $job_black_int_list) : array();



?>
      <table class="tabel-candidati" style="display: none">
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
									foreach ( $job_black_int_list as $_candidate_id ) {
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

<br>