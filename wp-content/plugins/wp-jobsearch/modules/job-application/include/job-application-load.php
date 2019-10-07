<?php
/**
 * Directory Plus JobApplicationLoads Module
 */
// Direct access not allowed.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
if ( ! class_exists( 'Jobsearch_JobApplicationLoad' ) ) {
	wp_enqueue_editor();

	class Jobsearch_JobApplicationLoad {

		public function __construct() {
			add_filter( 'jobsearch_job_applications_btn', array(
				$this,
				'jobsearch_job_applications_btn_callback'
			), 11, 2 );
			add_action( 'wp_ajax_jobsearch_job_application_submit', array(
				$this,
				'jobsearch_job_application_submit_callback'
			) );

			//
			add_action( 'wp_ajax_jobsearch_apply_job_with_cv_file', array( $this, 'apply_job_with_cv_file' ) );

			//
			add_filter( 'jobsearch_job_detail_before_footer', array( $this, 'job_application_popup_form' ), 10, 1 );
			//
			add_filter( 'wp_ajax_jobsearch_job_apply_without_login', array( $this, 'job_apply_without_login' ) );
			add_filter( 'wp_ajax_nopriv_jobsearch_job_apply_without_login', array( $this, 'job_apply_without_login' ) );
			//
			add_filter( 'wp_ajax_jobsearch_applying_job_with_email', array( $this, 'job_apply_with_email' ) );
			add_filter( 'wp_ajax_nopriv_jobsearch_applying_job_with_email', array( $this, 'job_apply_with_email' ) );
		}

		public function apply_job_with_cv_file() {
			global $jobsearch_plugin_options;

			$user_id = get_current_user_id();

			$user_is_candidate = jobsearch_user_is_candidate( $user_id );

			if ( $user_is_candidate ) {
				if ( jobsearch_candidate_not_allow_to_mod() ) {
					$msg = esc_html__( 'You are not allowed to upload file.', 'wp-jobsearch' );
					echo json_encode( array( 'err_msg' => $msg ) );
					die;
				}
				$multiple_cv_files_allow = isset( $jobsearch_plugin_options['multiple_cv_uploads'] ) ? $jobsearch_plugin_options['multiple_cv_uploads'] : '';

				$candidate_id = jobsearch_get_user_candidate_id( $user_id );
				$atach_id     = jobsearch_upload_candidate_cv( 'on_apply_cv_file', $candidate_id );

				if ( $atach_id > 0 ) {
					$file_url = wp_get_attachment_url( $atach_id );
					if ( $file_url ) {
						$arg_arr         = array(
							'file_id'  => $atach_id,
							'file_url' => $file_url,
							'primary'  => '',
						);
						$ca_at_cv_files  = get_post_meta( $candidate_id, 'candidate_cv_files', true );
						$ca_jat_cv_files = get_post_meta( $candidate_id, 'jobsearch_field_user_cv_attachments', true );
						$ca_at_cv_files  = ! empty( $ca_at_cv_files ) ? $ca_at_cv_files : array();
						$ca_jat_cv_files = ! empty( $ca_jat_cv_files ) ? $ca_jat_cv_files : array();

						$ca_at_cv_files[ $atach_id ]  = $arg_arr;
						$ca_jat_cv_files[ $atach_id ] = $arg_arr;
						update_post_meta( $candidate_id, 'candidate_cv_files', $ca_at_cv_files );
						update_post_meta( $candidate_id, 'jobsearch_field_user_cv_attachments', $ca_jat_cv_files );
					}

					$cv_file_title = get_the_title( $atach_id );
					$attach_post   = get_post( $atach_id );

					$attach_date = isset( $attach_post->post_date ) ? $attach_post->post_date : '';
					$attach_mime = isset( $attach_post->post_mime_type ) ? $attach_post->post_mime_type : '';

					if ( $attach_mime == 'application/pdf' ) {
						$attach_icon = 'fa fa-file-pdf-o';
					} else if ( $attach_mime == 'application/msword' || $attach_mime == 'application/vnd.openxmlformats-officedocument.wordprocessingml.document' ) {
						$attach_icon = 'fa fa-file-word-o';
					} else {
						$attach_icon = 'fa fa-file-word-o';
					}

					ob_start();
					?>
                    <li>
                        <i class="<?php echo( $attach_icon ) ?>"></i>
                        <label for="cv_file_<?php echo( $atach_id ) ?>">
                            <input id="cv_file_<?php echo( $atach_id ) ?>" type="radio" class="cv_file_item"
                                   name="cv_file_item" value="<?php echo( $atach_id ) ?>">
							<?php echo( strlen( $cv_file_title ) > 40 ? substr( $cv_file_title, 0, 40 ) . '...' : $cv_file_title ) ?>
                            <span class="upload-datetime"><i
                                        class="fa fa-calendar"></i> <?php echo date_i18n( get_option( 'date_format' ), strtotime( $attach_date ) ) . ' ' . date_i18n( get_option( 'time_format' ), strtotime( $attach_date ) ) ?></span>
                        </label>
                    </li>
					<?php
					$file_html = ob_get_clean();

					echo json_encode( array( 'fileUrl' => $file_url, 'filehtml' => $file_html ) );
				}
			}
			wp_die();
		}

		public function jobsearch_job_applications_btn_callback( $html, $arg = array() ) {
			global $jobsearch_plugin_options;
			$rand_id = rand( 123400, 9999999 );
			extract( shortcode_atts( array(
				'classes'           => 'jobsearch-applyjob-btn',
				'btn_after_label'   => '',
				'btn_before_label'  => '',
				'btn_applied_label' => '',
				'job_id'            => ''
			), $arg ) );

			$job_extrnal_apply_switch_arr     = isset( $jobsearch_plugin_options['apply-methods'] ) ? $jobsearch_plugin_options['apply-methods'] : '';
			$without_login_signin_restriction = isset( $jobsearch_plugin_options['without-login-apply-restriction'] ) ? $jobsearch_plugin_options['without-login-apply-restriction'] : '';


			$job_apply_switch = isset( $jobsearch_plugin_options['job-apply-switch'] ) ? $jobsearch_plugin_options['job-apply-switch'] : 'on';;

			if ( isset( $job_apply_switch ) && $job_apply_switch != 'on' ) {

				return $html;
			}

			$job_extrnal_apply_internal_switch = '';
			$job_extrnal_apply_external_switch = '';
			$job_extrnal_apply_email_switch    = '';

			if ( isset( $job_extrnal_apply_switch_arr ) && is_array( $job_extrnal_apply_switch_arr ) && sizeof( $job_extrnal_apply_switch_arr ) > 0 ) {


				foreach ( $job_extrnal_apply_switch_arr as $apply_switch ) {
					if ( $apply_switch == 'internal' ) {
						$job_extrnal_apply_internal_switch = 'internal';

					}
					if ( $apply_switch == 'external' ) {
						$job_extrnal_apply_external_switch = 'external';


					}
					if ( $apply_switch == 'email' ) {
						$job_extrnal_apply_email_switch = 'email';


					}
				}
			}

			$job_aply_type = get_post_meta( $job_id, 'jobsearch_field_job_apply_type', true );

			$job_aply_type = "internal";


//			var_dump($job_aply_type);
//			die($job_aply_type);
			//	print_r($job_aply_type);

//			if ( empty( $job_aply_type ) ) {
//				$job_aply_type = 'internal';
//			}
			$job_aply_extrnal_url = get_post_meta( $job_id, 'jobsearch_field_job_apply_url', true );

			$apply_without_login = isset( $jobsearch_plugin_options['job-apply-without-login'] ) ? $jobsearch_plugin_options['job-apply-without-login'] : '';

			$multiple_cv_files_allow = isset( $jobsearch_plugin_options['multiple_cv_uploads'] ) ? $jobsearch_plugin_options['multiple_cv_uploads'] : '';

			if ( $job_id != '' ) {


				$classes_str = 'jobsearch-open-signin-tab jobsearch-wredirct-url';
				$multi_cvs   = false;
				if ( is_user_logged_in() ) {

					if ( jobsearch_user_is_candidate() ) {
						if ( $multiple_cv_files_allow == 'on' ) {
							$multi_cvs = true;
						}
						$classes_str = 'jobsearch-apply-btn';
					} else {
						$classes_str = 'jobsearch-other-role-btn jobsearch-applyjob-msg-popup-btn';
					}
				}
				ob_start();
				$jobsearch_applied_list = array();

				$btn_text = $btn_before_label;
				if ( ! is_user_logged_in() && $apply_without_login != 'on' ) {

					$btn_text =  '<strong>' . esc_html__( 'Login', 'wp-jobsearch' ) . '</strong><br />' .  esc_html__( 'pentru a aplica', 'wp-jobsearch' );
				}

				$is_applied = false;

				if ( is_user_logged_in() ) {
					$finded_result_list = jobsearch_find_index_user_meta_list( $job_id, 'jobsearch-user-jobs-applied-list', 'post_id', jobsearch_get_user_id() );
					if ( is_array( $finded_result_list ) && ! empty( $finded_result_list ) ) {
						$classes_str = 'jobsearch-applied-btn';
						$btn_text    = $btn_applied_label;
						$is_applied  = true;
					}
				}

				if ( $apply_without_login == 'on' && ! is_user_logged_in() ) {
					$classes_str = 'jobsearch-nonuser-apply-btn';
				}

				// signin restriction on without login methods

				$internal_signin_switch = false;
				$external_signin_switch = false;
				$email_signin_switch    = false;


				// check cu login sau fara
				if ( isset( $without_login_signin_restriction ) && is_array( $without_login_signin_restriction ) && sizeof( $without_login_signin_restriction ) > 0 ) {
					foreach ( $without_login_signin_restriction as $restrict_signin_switch ) {
						if ( $restrict_signin_switch == 'internal' ) {
							$internal_signin_switch = true;


						}
						if ( $restrict_signin_switch == 'external' ) {
							$external_signin_switch = true;
						}
						if ( $restrict_signin_switch == 'email' ) {
							$email_signin_switch = true;
						}
					}

				}
				// end cu login sau fara

				// start apply with email job
				//$job_extrnal_apply_internal_switch = 'internal';
				$job_aply_type = 'internal';

				if ( $job_extrnal_apply_external_switch == 'external' && $job_aply_type == 'external' && $job_aply_extrnal_url != '' ) {

					if ( $apply_without_login == 'off' && ! is_user_logged_in() && $external_signin_switch ) {
						$classes_str = 'jobsearch-open-signin-tab';
						?>
                        <a href="javascript:void(0);"
                           class="<?php echo esc_html( $classes_str ); ?> <?php echo esc_html( $classes ); ?>"><?php echo esc_html( $btn_text ) ?> </a>
						<?php
					} else {
						?>
                        <a href="<?php echo( $job_aply_extrnal_url ) ?>" class="maluma2 <?php echo esc_html( $classes ); ?>"
                           target="_blank"><?php echo esc_html( $btn_text ) ?></a>
						<?php
					}
				}
				//   end apply external

				//   start internal job apply
				else if ( $job_extrnal_apply_internal_switch == 'internal' && $job_aply_type == 'internal' ) {

					$this_wredirct_url = jobsearch_server_protocol() . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
					if ( $apply_without_login == 'off' && ! is_user_logged_in() && $internal_signin_switch ) {

						$classes_str = 'jobsearch-open-signin-tab';
						?><a href="javascript:void(0);"
                             id="button-apply"
                             class="maluma <?php echo esc_html( $classes_str ); ?> <?php echo( ! is_user_logged_in() ? 'data-wredircto="' . $this_wredirct_url . '"' : '' ) ?> <?php echo esc_html( $classes ); ?>">
						<?= $btn_text; ?>
                        </a>



						<?php
					} else {


						if ( $multi_cvs === true ) {
							?>
                            <script>
                                jQuery(document).on('click', '.jobsearch-modelcvs-btn-<?php echo( $rand_id ) ?>', function () {
                                    jobsearch_modal_popup_open('JobSearchModalMultiCVs<?php echo( $rand_id ) ?>');
                                });
                            </script>

<!--                            <a href="javascript:void(0);"-->
<!--                               class="maluma3 --><?php //echo esc_html( $classes ); ?><!-- --><?php //echo( $is_applied ? '' : 'jobsearch-modelcvs-btn-' . $rand_id ) ?><!--">--><?php //echo esc_html( $btn_text ) ?><!--</a>
-->
                            <a href="/aplicatie?jobid=<?= $job_id; ?>" target="_blank"
                               class="jobsearch-applyjob-btn2 <?php // echo esc_html( $classes ); ?> <?php // echo( $is_applied ? '' : 'jobsearch-modelcvs-btn-' . $rand_id ) ?>"><?php echo esc_html( $btn_text ) ?></a>


							<?php

							// stadii joburi

							$apideja_mark_args = array(
								'job_id'      => $job_id,
								'before_icon' => 'fa fa-trash',
								'after_icon'  => 'fa fa-check',
							);

							$book_mark_args = array(
								'job_id'      => $job_id,
								'before_icon' => 'fa fa-trash',
								'after_icon'  => 'fa fa-history',
							);


							$reject_mark_args = array(
								'job_id'      => $job_id,
								'before_icon' => 'fa fa-trash',
								'after_icon'  => 'fas fa-times',
							);

							echo "<div class='centerIcons'>";

							do_action( 'jobsearch_job_apideja_button_frontend', $apideja_mark_args );
							do_action( 'jobsearch_job_shortlist_button_frontend', $book_mark_args );

							do_action( 'jobsearch_job_reject_button_frontend', $reject_mark_args );

							//	do_action( 'jobsearch_job_reject_button_frontend', $reject_mark_args );
							echo "</div>";


							?>


							<?php
							$max_cvs_allow = isset( $jobsearch_plugin_options['max_cvs_allow'] ) && absint( $jobsearch_plugin_options['max_cvs_allow'] ) > 0 ? absint( $jobsearch_plugin_options['max_cvs_allow'] ) : 5;
							$popup_args    = array(
								'p_job_id'          => $job_id,
								'p_rand_id'         => $rand_id,
								'p_btn_text'        => $btn_text,
								'p_classes'         => $classes,
								'p_classes_str'     => $classes_str,
								'p_btn_after_label' => $btn_after_label,
								'max_cvs_allow'     => $max_cvs_allow,
							);
							add_action( 'wp_footer', function () use ( $popup_args ) {
								global $jobsearch_plugin_options;
								extract( shortcode_atts( array(
									'p_job_id'          => '',
									'p_rand_id'         => '',
									'p_btn_text'        => '',
									'p_classes'         => '',
									'p_classes_str'     => '',
									'p_btn_after_label' => '',
									'max_cvs_allow'     => '',
								), $popup_args ) );

								$cand_files_types = isset( $jobsearch_plugin_options['cand_cv_types'] ) ? $jobsearch_plugin_options['cand_cv_types'] : '';

								if ( empty( $cand_files_types ) ) {
									$cand_files_types = array(
										'application/msword',
										'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
										'application/pdf',
									);
								}
								$sutable_files_arr = array();
								$file_typs_comarr  = array(
									'text/plain'                                                              => __( 'text', 'wp-jobsearch' ),
									'image/jpeg'                                                              => __( 'jpeg', 'wp-jobsearch' ),
									'image/png'                                                               => __( 'png', 'wp-jobsearch' ),
									'application/msword'                                                      => __( 'doc', 'wp-jobsearch' ),
									'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => __( 'docx', 'wp-jobsearch' ),
									'application/vnd.ms-excel'                                                => __( 'xls', 'wp-jobsearch' ),
									'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'       => __( 'xlsx', 'wp-jobsearch' ),
									'application/pdf'                                                         => __( 'pdf', 'wp-jobsearch' ),
								);
								foreach ( $file_typs_comarr as $file_typ_key => $file_typ_comar ) {
									if ( in_array( $file_typ_key, $cand_files_types ) ) {
										$sutable_files_arr[] = '.' . $file_typ_comar;
									}
								}
								$sutable_files_str = implode( ', ', $sutable_files_arr );

								$job_id        = $popup_args['p_job_id'];
								$companie_id   = get_post_meta( $job_id, 'jobsearch_field_job_posted_by', true );
								$date_companie = get_field( 'date_companie_2', $job_id );
								$companie      = $date_companie['nume_companie'];
								$job           = get_the_title( $job_id );
                                $logo = get_field('imagine_profil_companie', $job_id);
                                $logo = wp_get_attachment_url($logo);

								$user_id      = get_current_user_id();
								$candidate_id = jobsearch_get_user_candidate_id( $user_id );

								$imagine_cover = get_field( 'imagine_cover', $candidate_id );

								?>
                                <!--                                incepe modal aplicare -->
                                <input type="hidden" value="<?= $candidate_id; ?>" class="candidat-<?= $job_id; ?>"/>
                                <div class="jobsearch-modal fade "
                                     id="JobSearchModalMultiCVs<?php echo( $p_rand_id ) ?>">
                                    <div class="modal-inner-area">&nbsp;</div>
                                    <div class="modal-content-area modal-click-<?php echo( $p_rand_id ) ?>">
                                        <div class="modal-box-area">

                                            <div class="jobsearch-apply-withcvs">

                                                <div class="fereastra-aplicare-pop-up pop-up-<?php echo( $p_rand_id ) ?>">
                                                    <div class="header-pop-up">
                                                        <div class="left-header-pop-up">
                                                            <h5 class="title-header">Aplica pentru</h5>
                                                        </div>
                                                        <div class="right-header-pop-up">
                                                            <div class="left">
                                                                <img src="<?= $logo; ?>" />
                                                            </div>
                                                            <div class="right">
                                                                <p class="header-company-applied"><?= $companie; ?></p>
                                                                <span class="header-position-applied"><?= $job; ?></span>
                                                            </div>
                                                        </div>
                                                        <span class="modal-close"><i class="fa fa-times"></i></span>
                                                    </div>
                                                    <div class="content-pop-up">

<!--                                                        <div class="title-section">-->
<!--                                                            <h6 class="title-section-text">Alege un profil pentru aplicare</h6>-->
<!--                                                            <hr class="end-titile">-->
<!--                                                        </div>-->


                                                        <div class="jobEdit" style="margin: 30px -30px; float:left;">
                                                            <div class="jobsearch-employer-box-sectios">
                                                                <div class="row">
                                                                    <div class="col-12 col-md-3">
					                                                    <?php
					                                                    $templates = get_post_meta( $candidate_id, "candidat_profiles", true );
					                                                    $templates = unserialize( $templates );
					                                                    ?>

                                                                        <ul class="lista-template" style="padding: 0; margin: 0">
                                                                            <h3 class="title-template-anunt">Alege profil pentru aplicare</h3>
						                                                    <?php
						                                                    $count = 0;
						                                                    foreach ( $templates as $key => $template ) {
							                                                    $activ      = ( $count == 0 ) ? 'activlink' : '';
							                                                    $max_length = 120;

							                                                    if ( strlen( $template['descriere_template'] ) > $max_length ) {
								                                                    $offset                         = ( $max_length - 3 ) - strlen( $template['descriere_template'] );
								                                                    $template['descriere_template'] = substr( $template['descriere_template'], 0, strrpos( $template['descriere_template'], ' ', $offset ) ) . '...';
							                                                    }
							                                                    $id_template = wc_strtolower( str_replace( ' ', '', $key ) );
							                                                    echo "<li class='template-name'><strong><a href='#' onclick='void(0);return false;' class='template-on  template-on-apply-$job_id link-template-$id_template $activ'  data-template='$id_template'>" .
							                                                         $key . "</a></strong><br />" . $template['descriere_template'] .
							                                                         "</li>";
							                                                    $count ++;
						                                                    }
						                                                    ?>

                                                                        </ul>
                                                                        <input type="hidden" id="profil-candidat-<?= $job_id; ?>" name="profil-candidat-<?= $job_id; ?>">

                                                                    </div>

                                                                    <div class="col-12 col-md-9">
					                                                    <?php
					                                                    $count = 0;
					                                                    foreach ( $templates as $key => $template ) {
						                                                    $id_template = wc_strtolower( str_replace( ' ', '', $key ) );
						                                                    ?>

                                                                            <div class="template-wrap template-wrap-apply-<?= $job_id ?> template-<?= $id_template; ?>  <?php echo ( $count == 0 ) ? 'activ' : ''; ?>">

                                                                                <!--    aici incepe template candidat-->

                                                                                <div id="profil-candidat">

                                                                                    <div class="container-wrapper">

                                                                                        <div class="zonaCandidat">
                                                                                            <div class="row">
                                                                                                <div class="col-md-12" style="padding-bottom: 30px;">
                                                                                                    <img src="<?= wp_get_attachment_url( $template['imagine_cover'] ); ?>"/>
                                                                                                </div>
                                                                                                <div class="col-md-4">
                                                                                                    <img src="<?= wp_get_attachment_url( $template['imagine_profil'] ); ?>" style="max-height: 200px"/>
                                                                                                </div>
                                                                                                <div class="col-md-4">
                                                                                                    <div class="wrap-date">
                                                                                                        <strong>Date personale</strong>:<br>
                                                                                                        <p>Varsta: <?= date('Y') - $template['anul_nasterii']; ?> ani</p>
                                                                                                        <p>Sex: <?= $template['sex']; ?></p>
                                                                                                        <p>Nickname: <?= $template['sex']; ?></p>
                                                                                                        <p>Resedinta: <?= $template['judet_candidat']; ?></p>
                                                                                                    </div>
                                                                                                </div>
                                                                                                <div class="col-md-4">
                                                                                                    <div class="wrap-date">
                                                                                                        <strong>Link-uri</strong>:<br>
													                                                    <?php
													                                                    foreach ( $template['link-uri'] as $titlu => $link ) {
														                                                    echo "<a href='$link' target='_blank'>$titlu</a><br/>";
													                                                    }
													                                                    ?>
                                                                                                    </div>
                                                                                                </div>
                                                                                            </div>
                                                                                        </div>

                                                                                        <h2 class="job-title"><?= $template['prenume'] . ' ' . $template['particula'] . '  ' . $template['nume'] . '(' . $template['formula_adresare'] . ')' ?> <?php
										                                                 //   echo "<a href='javascript:void(0)' class='edit-template' data-template='$key' data-title='" . $template['nume'] . "'>EDITEAZA</a>";
										                                                    ?></h2>

                                                                                        <div class="zonaCandidat" id="scrisoare-intentie">

                                                                                        </div>

                                                                                        <div class="zonaCandidat" id="educatie">
                                                                                            <h2>Educatie</h2>
                                                                                            <ul style="padding-left: 20px; list-style: square; ">
											                                                    <?php
											                                                    foreach ( $template['educatie'] as $studiu ) {
												                                                    echo '<li>' . $studiu . '</li>';
											                                                    }
											                                                    ?>
                                                                                            </ul>
                                                                                        </div>

                                                                                        <div class="zonaCandidat" id="limbi">
                                                                                            <h2>Limbi straine</h2>
                                                                                            Engleza - <?= $template['limba_engleza']; ?><br>
										                                                    <?php
										                                                    foreach ( $template['limba_nivel'] as $studiu ) {
											                                                    echo $studiu . '</br>';
										                                                    }
										                                                    ?>
                                                                                        </div>
                                                                                        <div class="zonaCandidat" id="certificari">
                                                                                            <h2>Competente relevante</h2>
										                                                    <?= $template['relevant_skils']; ?>
                                                                                        </div>

                                                                                        <div class="zonaCandidat" id="certificari">
                                                                                            <h2>Certificari</h2>
										                                                    <?php
										                                                    foreach ( $template['certificare'] as $studiu ) {
											                                                    echo $studiu . ', ';
										                                                    }
										                                                    ?>
                                                                                        </div>

                                                                                        <div class="zonaCandidat" id="experienta">
                                                                                            <h2>Experienta</h2>
                                                                                            <ul>
											                                                    <?php
											                                                    foreach ( $template['experinta_full'] as $label => $companie ): ?>
                                                                                                    <li><span class="tcomapnie"><?= $label; ?></span> <br/>
													                                                    <?= $companie['descriere_companie']; ?>
                                                                                                        <ul>
														                                                    <?php foreach ( $companie['post'] as $post => $date ): ?>
                                                                                                                <li>
                                                                                                                    <span> <?= $date['data_la']; ?></span> -
                                                                                                                    <span><?= $date['pana_la']; ?></span><br>
                                                                                                                    <strong><span><?= $date['post']; ?></span>,
                                                                                                                        <span><?= $date['ierarhie']; ?></span></strong>
                                                                                                                    <br/>
                                                                                                                    <span><?= $date['descriere_job']; ?></span><br>
                                                                                                                    <span><?= $date['alte_detalii']; ?></span><br>

                                                                                                                </li>
														                                                    <?php endforeach;; ?>
                                                                                                        </ul>
                                                                                                    </li>
											                                                    <?php endforeach; ?>
                                                                                            </ul>
                                                                                        </div>

                                                                                        <div class="zonaCandidat" id="cerinte">
                                                                                            <h2>Cerinte</h2>
                                                                                            <b>Salariu: <?= $template['salariu_minim_accepta']; ?>€</b> <br>
										                                                    <?= $template['alte_cerinte']; ?>
                                                                                        </div>

                                                                                        <div class="zonaCandidat" id="brief">
                                                                                            <h2>Note si comentarii libere</h2>
										                                                    <?= $template['note_comentarii']; ?>
                                                                                        </div>

                                                                                    </div>
                                                                                </div>
                                                                                <!--  end candiat-->
                                                                            </div>

						                                                    <?php
						                                                    $count ++;
					                                                    } ?>

                                                                    </div>
                                                                </div>


                                                            </div>
                                                        </div>

                                                        <script>
                                                            // schimba tabul
                                                            jQuery('<?=  ".template-on-apply-$job_id" ?>').click(function () {
                                                                jQuery('<?=  ".template-on-apply-$job_id" ?>').removeClass('activlink');
                                                                jQuery(this).addClass('activlink');

                                                                var template = '.template-' + jQuery(this).attr('data-template');
                                                                jQuery('#profil-candidat-<?= $job_id; ?>').val(template);
                                                                jQuery('<?=  ".template-wrap-apply-$job_id" ?>').removeClass('activ');
                                                                jQuery(template).addClass('activ');


                                                            });
                                                        </script>

                                                        <style>
                                                            .loader {
                                                                border: 16px solid #f3f3f3;
                                                                border-radius: 50%;
                                                                border-top: 16px solid orange;
                                                                border-bottom: 16px solid darkorange;
                                                                width: 120px;
                                                                height: 120px;
                                                                -webkit-animation: spin 2s linear infinite;
                                                                animation: spin 2s linear infinite;
                                                                left: 45%;
                                                                top: 50%;
                                                                transform: translate(-50%, -50%);
                                                                position: absolute;
                                                            }

                                                            @-webkit-keyframes spin {
                                                                0% {
                                                                    -webkit-transform: rotate(0deg);
                                                                }
                                                                100% {
                                                                    -webkit-transform: rotate(360deg);
                                                                }
                                                            }

                                                            @keyframes spin {
                                                                0% {
                                                                    transform: rotate(0deg);
                                                                }
                                                                100% {
                                                                    transform: rotate(360deg);
                                                                }
                                                            }

                                                            #overwLoad {
                                                                width: 100%;
                                                                height: 100%;
                                                                position: fixed;
                                                                z-index: 9999999999;
                                                                left: 0;
                                                                top: 0;
                                                                right: 0;
                                                                bottom: 0;
                                                                background-color: rgba(8, 8, 8, 0.8);
                                                                display: none;
                                                            }

                                                            .acf-field-image-aspect-ratio-crop[data-name=imagine_cover] {
                                                                position: relative;
                                                                top: initial;
                                                                right: 0;
                                                                border-left: none !important;
                                                            }
                                                        </style>
                                                        <div id="overwLoad">
                                                            <div class="loader"></div>
                                                        </div>

                                                        <style>
                                                            .modal-dialog {
                                                                max-width: 1000px;
                                                            }

                                                            #formModal {
                                                                overflow-y: scroll;
                                                            }
                                                            .wrap-date{
                                                                top: 50%;
                                                                left:50%;
                                                                position: absolute;
                                                                transform: translate(-50%,-50%);
                                                            }
                                                            .lista-template li {
                                                                font-size: 12px;
                                                                font-weight: 400;
                                                                font-family: "Open Sans", sans-serif;
                                                                border-bottom: 2px solid #e1e1e1;
                                                                line-height: 21px;
                                                                margin-bottom: 30px;
                                                                padding-bottom: 30px;
                                                                height: 75px;
                                                            }
                                                        </style>




<!--    aici se termina toate partea de candidat-->

                                                        <div class="title-section">
                                                            <h6 class="title-section-text">Alege text pentru
                                                                aplicare</h6>
                                                            <hr class="end-titile">
                                                        </div>

														<?php
														$user_id      = get_current_user_id();
														$candidate_id = jobsearch_get_user_candidate_id( $user_id );

														$text_list    = get_post_meta( $candidate_id, "candidat_texte_predefinite", true );
														$text_list = unserialize($text_list);


														$objTextList  = json_encode( $text_list );
														?>
                                                        <div class="text-section">
                                                            <div class="left-text-section">
                                                                <ul class="lista-pop-up">
																	<?php foreach ( $text_list as $key => $text ): ?>
                                                                        <li>
                                                                            <a href="javascript:void(0)"
                                                                               class="add-text-apply-<?php echo( $p_rand_id ) ?>"
                                                                               style="display: block; line-height: 20px; padding-top: 10px;"
                                                                               data-key="<?= $key; ?>">
																				<?= $key; ?>
                                                                            </a>
                                                                            <p style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; line-height: 20px; font-size:12px; font-family: 'Open Sans', sans-serif;">
		                                                                        <?php echo strip_tags( $text['descriere_template'] ); ?>
                                                                            </p>
                                                                        </li>
																	<?php endforeach; ?>
                                                                </ul>
                                                                <div class="fullwWrap">
                                                                    <a href="javascript:void(0)"
                                                                       class="<?= "new-text-$job_id"; ?> newaddbtn">
                                                                        Adauga text nou</a>
                                                                </div>
                                                            </div>
                                                            <div class="right-text-section">
                                                                <div class="description-text">

																	<?php
																	$content   = '';
																	$editor_id = "zonaText_$job_id";

																	$settings = [
																		'textarea_name' => "zonaText_$job_id",
																		'wpautop'       => false,
																		'media_buttons' => false,
																		'quicktags'     => [
																			'buttons' => 'strong,em,del,ul,ol,li,block,close'
																		],
																	];

																	wp_editor( $content, $editor_id, $settings ); ?>

                                                                </div>


                                                            </div>


                                                            <script>
                                                                var texte = <?= $objTextList; ?>;

                                                                function updateContent(idText, content) {
                                                                    tinymce.get(idText).setContent(content);
                                                                }

                                                                jQuery('.add-text-apply-<?php echo( $p_rand_id ) ?>').click(function () {
                                                                    jQuery('.add-text-apply-<?php echo( $p_rand_id ) ?>').removeClass('activat');
                                                                    jQuery(this).addClass('activat');
                                                                    var key = jQuery(this).attr('data-key');
                                                                    var text = texte[key]['text'];
                                                                    updateContent('<?= $editor_id; ?>', text)
                                                                });

                                                                jQuery('.<?= "new-text-$job_id"; ?>').click(function () {
                                                                    jQuery('.add-text-apply-<?php echo( $p_rand_id ) ?>').removeClass('activat');
                                                                    updateContent('<?= $editor_id; ?>', '');
                                                                });


                                                            </script>

                                                        </div>
                                                        <div class="title-section">
                                                            <h6 class="title-section-text">Alege fisiere atasate in
                                                                contul personal</h6>
                                                            <hr class="end-titile">
                                                        </div>
														<?php
														$user_id        = get_current_user_id();
														$candidate_id   = jobsearch_get_user_candidate_id( $user_id );
													//	$fisire_atasate = get_field( 'fisiere', $candidate_id );

														?>
                                                        <div class="text-section">
                                                            <div class="left-text-section">
                                                                <ul class="lista-pop-up" style="height: 200px; overflow-y: scroll;">

	                                                                <?php
	                                                                $the_query = new WP_Query( array(
		                                                                'post_type'      => 'attachment',
		                                                                'post_status'    => 'inherit',
		                                                                'author'         => $user_id,
		                                                                'posts_per_page' => -1,
		                                                                'post_mime_type' => array( 'application/doc', 'application/pdf', 'text/plain' ),
	                                                                ) );
	                                                                if ( $the_query->have_posts() ) {
		                                                                while ( $the_query->have_posts() ) : $the_query->the_post();
			                                                                $url  = wp_get_attachment_url();
			                                                                $id   = get_the_ID();
			                                                                ?>

                                                                            <li>
                                                                                <a href="javascript:void(0)"
                                                                                   class="add-file-apply-<?php echo( $p_rand_id ) ?> filelink  link-<?= $id; ?>"
                                                                                   style="display: block; line-height: 20px; padding-top: 10px;"
                                                                                   data-id="<?= $id; ?>">
                                                                                    <span class="nume-fisier"><?= the_title(); ?></span>
                                                                                    <span class="link-fisier"><?= $url; ?></span>
                                                                                </a>
                                                                                <p style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; font-size:12px; font-family: 'Open Sans', sans-serif;">
                                                                                    <?php echo strip_tags( get_the_excerpt() ); ?>
                                                                                </p>
                                                                            </li>


			                                                                <?php
		                                                                endwhile;
	                                                                } ?>

                                                                </ul>
                                                            </div>


                                                            <div class="right-text-section">
                                                                <div class="description-text description-text-1" style="height: 200px">
                                                                    <div class="fisier-pop-up-text"
                                                                         id="fisiereAtasate-<?php echo( $job_id ) ?>">

                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <script>


                                                                function removeline(element) {
                                                                    var id = jQuery(element).attr('data-id');
                                                                    jQuery('.link-' + id).removeClass('activat');
                                                                    jQuery(element).parent().remove();
                                                                }

                                                                var count = 0;
                                                                jQuery('.add-file-apply-<?php echo( $p_rand_id ) ?>').click(function () {
                                                                    //  jQuery('.filelink').removeClass('activat');
                                                                    jQuery(this).addClass('activat');
                                                                    var link, nume, fisiere, zona, id;
                                                                    id = jQuery(this).attr('data-id');
                                                                    nume = jQuery('.nume-fisier', this).text();
                                                                    link = jQuery('.link-fisier', this).text();
                                                                    // creare fisier
                                                                    fisiere = '<div class="linie-fisier fisier-' + id + '"><span>' + nume + '</span><input name="linkfisier-' + count + '"  class="fisier-<?php echo( $job_id ) ?>" type="hidden" data-nume="' + nume + '" val="' + nume + '" data-link="' + link + '"  data-id=' + id + '>' +
                                                                        '<span class="remove" onclick="javascript:removeline(this);" data-id=' + id + '><i class="fa fa-minus-square" aria-hidden="true"></i></span></div>';
                                                                    zona = jQuery('#fisiereAtasate-<?php echo( $job_id ) ?>')
                                                                    // verific daca exista fifsier
                                                                    if (zona.has(".fisier-" + id).length === 0) {
                                                                        count++;
                                                                        zona.append(fisiere);
                                                                    }

                                                                })
                                                            </script>
                                                        </div>

                                                        <div class="title-section">
                                                            <h6 class="title-section-text">Adauga extern</h6>
                                                            <hr class="end-titile">
                                                        </div>
                                                        <div class="text-section">

                                                            <div class="left-text-section">
                                                                <div class="formfileupload">
                                                                    <form id="featured_upload-<?php echo( $job_id ) ?>"
                                                                          method="post" action="#"
                                                                          enctype="multipart/form-data">
                                                                        <div class="file-upload-wrapper">

                                                                            <input type="file"
                                                                                   name="new_ap_file_<?php echo( $job_id ) ?>"
                                                                                   id="new_ap_file_<?php echo( $job_id ) ?>"
                                                                                   class="new_ap_file file-upload-field fileinput-<?php echo( $job_id ) ?>"
                                                                                   multiple="false"/>

                                                                        </div>

                                                                        <input type="hidden" name="post_id" id="post_id_<?php echo( $job_id ) ?>"
                                                                               value="<?= $candidate_id; ?>"/>

																		<?php wp_nonce_field( "new_ap_file_$job_id", "new_ap_file_nonce_$job_id" ); ?>

<!--                                                                        <input id="submit_new_ap_file"-->
<!--                                                                               name="submit_new_ap_file" type="submit"-->
<!--                                                                               value="Upload"/>-->
                                                                    </form>
                                                                </div>
                                                            </div>
                                                            <div class="right-text-section">
                                                                <div class="description-text description-text-1"
                                                                     style="height: 100px;">
                                                                    <div class="fisier-pop-up-text"
                                                                         id="fisiereAtasate-new-<?php echo( $job_id ) ?>">

                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>


                                                        <script>
                                                            jQuery("#featured_upload-<?php echo( $job_id ) ?>").on("change", ".file-upload-field", function () {

                                                                $(this).parent(".file-upload-wrapper").attr("data-text",);

                                                                var numeFisier = $(this).val().replace(/.*(\/|\\)/, '');
                                                                // creare fisier
                                                                var fisiere = '<div class="linie-fisier fisier-new"><span>' + numeFisier + '</span>' +
                                                                    '<span class="remove removenew-<?php echo( $job_id ) ?>" onclick="javascript:removeline(this);"><i class="fa fa-minus-square" aria-hidden="true"></i></span></div>';

                                                                jQuery('#fisiereAtasate-new-<?php echo( $job_id ) ?>').html(fisiere);
                                                            });

                                                            jQuery('.removenew<?php echo( $job_id ) ?>').click(function () {
                                                                jQuery('.fileinput-<?php echo( $job_id ) ?>').val();
                                                            });


                                                        </script>


                                                        <button onclick="void(0);"
                                                                class="send-pop-up-button action-<?php echo( $p_rand_id ) ?>"
                                                                data-app="<?php echo( $p_rand_id ) ?>">Preview si
                                                            trimite
                                                        </button>
                                                    </div>
                                                </div>

                                                <div class="preview-aplicatie preview-<?php echo( $p_rand_id ) ?>"
                                                     style="display:none;">

                                                    <div class="jobsearch-main-content whitebg"
                                                         style="width: 100%; float: left; padding: 10px 30px">

                                                        <!-- Main Section -->
                                                        <div class="jobsearch-main-section">
                                                            <div class="jobsearch-plugin-default-container"
                                                                 style="padding: 20px;">
                                                                <div class="jobsearch-row">
																	<?php


																	// date personale

																	$date_personale   = get_field( 'date_personale', $candidate_id );
																	$nume             = $date_personale['nume'];
																	$prenume          = $date_personale['prenume'];
																	$cuvleg           = $date_personale['particula'];
																	$titlu_academic   = $date_personale['titlu'];
																	$formula_adresare = $date_personale['formula_adresare'];

																	$nume_complet = "$titlu_academic $prenume $cuvleg $nume ($formula_adresare)";
																	$nickname     = $date_personale['nickaname'];

																	$anul_nasterii = $date_personale['anul_nasterii'];

																	$varsta    = date( 'Y' ) - $anul_nasterii;
																	$sex       = $date_personale['sex'];
																	$resedinta = $date_personale['judet_candidat']['label'];


																	// educatie

																	$educatie   = get_field( 'educatie', $candidate_id ); //array
																	$calificari = get_field( 'calificari', $candidate_id ); //text

																	$engleza    = get_field( 'limba_engleza', $candidate_id ); //
																	$alte_limbi = get_field( 'alte_limbi', $candidate_id );

																	$relevant_skils = get_field( 'relevant_skils', $candidate_id );
																	$linkuri        = get_field( 'link-uri', $candidate_id ); //array

																	$salariu = get_field( 'salariu_minim_accepta', $candidate_id );

																	$experienta = get_field( 'experienta', $candidate_id );
																	// companie // post (post, de_la, pana_la, ierarhie, descriere_job, alte_detalii)

																	$comentarii_libere = get_field( 'note_comentarii', $candidate_id );

																	$alte_cerinte = get_field( 'alte_cerinte', $candidate_id );

																	$imagine_profil = get_field( 'imagine_profil', $candidate_id );


																	// date personale


																	?>

                                                                    <aside class="jobsearch-column-3 jobsearch-typo-wrap">
                                                                        <div class="widget widget_candidate_info">
                                                                            <div class="jobsearch_candidate_info">
                                                                                <div class="wrapAvatar">
                                                                                    <img src="<?= $imagine_profil; ?>"/>
                                                                                </div>
                                                                                <div class="datePersonale">
                                                                                    <p>Varsta1: <?= $varsta; ?>
                                                                                        ani</p>
                                                                                    <p>Sex: <?= $sex; ?></p>
                                                                                    <p>
                                                                                        Nickname: <?= $nickname; ?></p>
                                                                                    <p>
                                                                                        Resedinta: <?= $resedinta; ?></p>
                                                                                    <p>Link-uri:<br>
																						<?php
																						foreach ( $linkuri as $link ): ?>
                                                                                            <a href="<?= $link['link']; ?>"><?= $link['titlu']; ?></a>
                                                                                            <br/>
																						<?php endforeach;
																						?>
                                                                                    </p>

                                                                                </div>

                                                                            </div>
                                                                        </div>
                                                                    </aside>
                                                                    <div id="profil-candidat"
                                                                         class="jobsearch-column-9 jobsearch-typo-wrap">
                                                                        <div class="container-wrapper">
                                                                            <h1 class="numecandidat"><?= $nume_complet; ?></h1>

                                                                            <div class="zonaCandidat text-<?php echo( $p_rand_id ) ?>"
                                                                                 id="scrisoare-intentie">
                                                                            </div>

                                                                            <div class="zonaCandidat" id="educatie">
                                                                                <h2><?php esc_html_e( 'Educatie', 'managero' ); ?></h2>
                                                                                <ul style="padding-left: 20px; list-style: square; "><?php
																					foreach ( $educatie as $perioada ):
																						echo '<li>' . $perioada['educatie'] . '</li>';
																					endforeach; ?>
                                                                                </ul>
                                                                            </div>

                                                                            <div class="zonaCandidat" id="limbi">
                                                                                <h2><?php esc_html_e( 'Limbi straine', 'managero' ); ?></h2>
                                                                                Engleza - <?= $engleza; ?><br/>
																				<?php foreach ( $alte_limbi as $limba ):
																					echo $limba['limba_nivel'] . '<br />';
																				endforeach;
																				?>
                                                                            </div>


                                                                            <div class="zonaCandidat"
                                                                                 id="certificari">
                                                                                <h2><?php esc_html_e( 'Competente relevante', 'managero' ); ?></h2>
																				<?= $relevant_skils; ?>
                                                                            </div>

                                                                            <div class="zonaCandidat"
                                                                                 id="certificari">
                                                                                <h2><?php esc_html_e( 'Certificari', 'managero' ); ?></h2>
																				<?= $calificari; ?>
                                                                            </div>

                                                                            <div class="zonaCandidat"
                                                                                 id="experienta">
                                                                                <h2><?php esc_html_e( 'Experienta', 'managero' ); ?></h2>
                                                                                <ul>
																					<?php
																					foreach ( $experienta as $experientaS ): ?>

                                                                                        <li>
                                                                                            <span class="tcomapnie"><?= $experientaS['companie']; ?></span>
                                                                                            <ul>
																								<?php foreach ( $experientaS['post'] as $postx ): ?>
                                                                                                    <li>
                                                                                                        <span><?= $postx['de_la'] ?></span>
                                                                                                        -
                                                                                                        <span><?= $postx['pana_la'] ?></span><br/>
                                                                                                        <strong><span><?= $postx['post'] ?></span>,
                                                                                                            <span><?= $postx['ierarhie'] ?></span></strong>
                                                                                                        <br/>
                                                                                                        <span><?= $postx['descriere_job'] ?></span><br/>
                                                                                                        <span><?= $postx['alte_detalii'] ?></span><br/>

                                                                                                    </li>
																								<?php endforeach; ?>
                                                                                            </ul>
                                                                                        </li>
																					<?php endforeach;
																					?>
                                                                                </ul>

                                                                            </div>

                                                                            <div class="zonaCandidat" id="cerinte">
                                                                                <h2><?php esc_html_e( 'Cerinte', 'managero' ); ?></h2>
                                                                                <b>Salariu:
																					<?= $salariu; ?>&euro;</b> <br/>
																				<?= $alte_cerinte; ?>
                                                                            </div>

                                                                            <div class="zonaCandidat" id="brief">
                                                                                <h2><?php esc_html_e( 'Note si comentarii libere', 'managero' ); ?></h2>
																				<?= $comentarii_libere; ?>

                                                                            </div>


                                                                            <div class="zonaCandidat fisiere-<?php echo( $p_rand_id ) ?>"
                                                                                 id="zonaFisiere">
                                                                                <h2><?php esc_html_e( 'Fisiere atasate', 'managero' ); ?></h2>
                                                                                <div class="row">
																					<?php
																					$get_job_files_attached = get_post_meta( $job_id, 'jobsearch_job_files_attached', true );
																					$atached_files          = json_decode( $get_job_files_attached[ $candidate_id ], true );

																					foreach ( $atached_files as $file ) { ?>
                                                                                        <div class="col-md-3">
                                                                                            <a href="<?= $file['link'] ?>"
                                                                                               download><?= $file['nume']; ?></a>
                                                                                        </div>

																					<?php } ?>
                                                                                </div>
                                                                            </div>


																			<?php
																			echo apply_filters( 'jobsearch_applying_job_before_apply', '' );
																			?>
                                                                            <a href="javascript:void(0)"
                                                                               class="btnbackapply switch-<?php echo( $p_rand_id ) ?>">
                                                                                Inapoi la texte si fisiere
                                                                            </a>
                                                                            <a href="javascript:void(0);"
                                                                               class="<?php echo esc_html( $p_classes_str ); ?> btnapplyn jobsearch-apply-btn-<?php echo absint( $p_rand_id ); ?> <?php echo esc_html( $p_classes ); ?>" <?php echo( ! is_user_logged_in() ? 'data-wredircto="' . $this_wredirct_url . '"' : '' ) ?>
                                                                               data-randid="<?php echo absint( $p_rand_id ); ?>"
                                                                               data-jobid="<?php echo absint( $p_job_id ); ?>"
                                                                               data-btnafterlabel="<?php echo esc_html( $p_btn_after_label ) ?>"
                                                                               data-btnbeforelabel="<?php echo esc_html( $p_btn_text ) ?>"
                                                                            >
																				<?php echo esc_html( $p_btn_text ) ?>
                                                                            </a>
                                                                            <small class="apply-bmsg"></small>


                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <!-- Main Section -->

                                                    </div>

                                                    <style>
                                                        .jobsearch-applied-job-btns .candidate-more-acts-con ul {
                                                            position: absolute;
                                                            top: 100%;
                                                            right: 0px;
                                                            border: 1px solid #ddd;
                                                            background-color: #ffffff;
                                                            margin-top: 2px;
                                                            display: none;
                                                            z-index: 100;
                                                        }

                                                        .candidate-more-acts-con ul li {
                                                            float: left;
                                                            width: 100%;
                                                            border-bottom: 1px solid #ddd;
                                                            padding: 9px 8px;
                                                            -webkit-transition: all 0.4s ease-in-out;
                                                            -moz-transition: all 0.4s ease-in-out;
                                                            -ms-transition: all 0.4s ease-in-out;
                                                            -o-transition: all 0.4s ease-in-out;
                                                            transition: all 0.4s ease-in-out;
                                                        }

                                                        .candidate-more-acts-con ul li a {
                                                            display: block;
                                                            white-space: nowrap;
                                                            -webkit-transition: all 0.4s ease-in-out;
                                                            -moz-transition: all 0.4s ease-in-out;
                                                            -ms-transition: all 0.4s ease-in-out;
                                                            -o-transition: all 0.4s ease-in-out;
                                                            transition: all 0.4s ease-in-out;
                                                        }

                                                    </style>

                                                </div>


                                                <script>
                                                    jQuery('.action-<?php echo( $p_rand_id ) ?>').click(function () {
                                                        jQuery('.preview-<?php echo( $p_rand_id ) ?>').toggle();
                                                        jQuery('.pop-up-<?php echo( $p_rand_id ) ?>').toggle();

                                                        var text = tinyMCE.get('<?= "zonaText_$job_id"; ?>').getContent();

                                                        jQuery('.text-<?php echo( $p_rand_id ) ?>').html(text);
                                                    });

                                                    jQuery(document).on('click', '#JobSearchModalMultiCVs<?php echo( $p_rand_id ) ?> .modal-close', function () {
                                                        jQuery('.preview-<?php echo( $p_rand_id ) ?>').css('display', 'none');
                                                        jQuery('.pop-up-<?php echo( $p_rand_id ) ?>').css('display', 'block');
                                                    });

                                                    jQuery('.modal-click-<?php echo( $p_rand_id ) ?>').on('click', function (e) {
                                                        if (e.target !== e.currentTarget)
                                                            return;
                                                        jQuery('.preview-<?php echo( $p_rand_id ) ?>').css('display', 'none');
                                                        jQuery('.pop-up-<?php echo( $p_rand_id ) ?>').css('display', 'block');
                                                    });
                                                    jQuery('.switch-<?php echo( $p_rand_id ) ?>').click(function () {
                                                        jQuery('.preview-<?php echo( $p_rand_id ) ?>').css('display', 'none');
                                                        jQuery('.pop-up-<?php echo( $p_rand_id ) ?>').css('display', 'block');
                                                    });

                                                </script>


                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!--                                  end modal aplicare  -->


								<?php
							}, 11, 1 );
						} else {
							ob_start();
							?>
                            <a href="javascript:void(0);"
                               class="<?php echo esc_html( $classes_str ); ?> jobsearch-apply-btn-<?php echo absint( $rand_id ); ?> <?php echo esc_html( $classes ); ?>" <?php echo( ! is_user_logged_in() ? 'data-wredircto="' . $this_wredirct_url . '"' : '' ) ?>
                               data-randid="<?php echo absint( $rand_id ); ?>"
                               data-jobid="<?php echo absint( $job_id ); ?>"
                               data-btnafterlabel="<?php echo esc_html( $btn_after_label ) ?>"
                               data-btnbeforelabel="<?php echo esc_html( $btn_text ) ?>"><?php echo esc_html( $btn_text ) ?></a>
                            <small class="apply-bmsg"></small>
							<?php
							$appbtn_html = ob_get_clean();
							echo apply_filters( 'jobsearch_jobaplybtn_simple_default', $appbtn_html, $classes_str, $rand_id, $classes, $job_id, $btn_after_label, $btn_text );
						}
					}
				}

				// end internal job
			}

			$html .= ob_get_clean();

			return $html;

		}

		public function job_application_popup_form( $job_id ) {
			global $jobsearch_plugin_options;

			$rand_num = rand( 100000, 9999999 );

			$apply_without_login = isset( $jobsearch_plugin_options['job-apply-without-login'] ) ? $jobsearch_plugin_options['job-apply-without-login'] : '';
			if ( $apply_without_login == 'on' && ! is_user_logged_in() ) {

				$wout_fields_sort = isset( $jobsearch_plugin_options['aplywout_login_fields_sort'] ) ? $jobsearch_plugin_options['aplywout_login_fields_sort'] : '';
				$wout_fields_sort = isset( $wout_fields_sort['fields'] ) ? $wout_fields_sort['fields'] : '';

				$popup_args = array(
					'job_id'           => $job_id,
					'rand_id'          => $rand_num,
					'wout_fields_sort' => $wout_fields_sort,
				);
				add_action( 'wp_footer', function () use ( $popup_args ) {

					global $jobsearch_plugin_options;

					extract( shortcode_atts( array(
						'job_id'           => '',
						'rand_num'         => '',
						'wout_fields_sort' => '',
					), $popup_args ) );

					$cand_files_types = isset( $jobsearch_plugin_options['cand_cv_types'] ) ? $jobsearch_plugin_options['cand_cv_types'] : '';

					if ( empty( $cand_files_types ) ) {
						$cand_files_types = array(
							'application/msword',
							'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
							'application/pdf',
						);
					}
					$sutable_files_arr = array();
					$file_typs_comarr  = array(
						'text/plain'                                                              => __( 'text', 'wp-jobsearch' ),
						'image/jpeg'                                                              => __( 'jpeg', 'wp-jobsearch' ),
						'image/png'                                                               => __( 'png', 'wp-jobsearch' ),
						'application/msword'                                                      => __( 'doc', 'wp-jobsearch' ),
						'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => __( 'docx', 'wp-jobsearch' ),
						'application/vnd.ms-excel'                                                => __( 'xls', 'wp-jobsearch' ),
						'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'       => __( 'xlsx', 'wp-jobsearch' ),
						'application/pdf'                                                         => __( 'pdf', 'wp-jobsearch' ),
					);
					foreach ( $file_typs_comarr as $file_typ_key => $file_typ_comar ) {
						if ( in_array( $file_typ_key, $cand_files_types ) ) {
							$sutable_files_arr[] = '.' . $file_typ_comar;
						}
					}
					$sutable_files_str = implode( ', ', $sutable_files_arr );
					?>
                    <div class="jobsearch-modal jobsearch-typo-wrap fade" id="JobSearchNonuserApplyModal">
                        <div class="modal-inner-area">&nbsp;</div>
                        <div class="modal-content-area">
                            <div class="modal-box-area">
                                <div class="jobsearch-modal-title-box">
                                    <h2><?php esc_html_e( 'Apply for this Job', 'wp-jobsearch' ) ?></h2>
                                    <span class="modal-close"><i class="fa fa-times"></i></span>
                                </div>
                                <form id="apply-form-<?php echo absint( $rand_num ) ?>" method="post">
                                    <div class="jobsearch-user-form jobsearch-user-form-coltwo">
                                        <ul class="apply-fields-list">
											<?php
											if ( isset( $wout_fields_sort['name'] ) ) {
												foreach ( $wout_fields_sort as $field_sort_key => $field_sort_val ) {
													$field_name_swich_key = 'aplywout_log_f' . $field_sort_key . '_swch';
													$field_name_swich     = isset( $jobsearch_plugin_options[ $field_name_swich_key ] ) ? $jobsearch_plugin_options[ $field_name_swich_key ] : '';
													if ( $field_sort_key == 'name' && ( $field_name_swich == 'on' || $field_name_swich == 'on_req' ) ) {
														?>
                                                        <li>
                                                            <label><?php esc_html_e( 'Full Name:', 'wp-jobsearch' ) ?><?php echo( $field_name_swich == 'on_req' ? ' *' : '' ) ?></label>
                                                            <input class="<?php echo( $field_name_swich == 'on_req' ? 'required-apply-field' : 'required' ) ?>"
                                                                   name="user_fullname" type="text"
                                                                   placeholder="<?php esc_html_e( 'Full Name', 'wp-jobsearch' ) ?>">
                                                        </li>
														<?php
													} else if ( $field_sort_key == 'email' ) {
														?>
                                                        <li>
                                                            <label><?php esc_html_e( 'Email: *', 'wp-jobsearch' ) ?></label>
                                                            <input class="required" name="user_email" type="text"
                                                                   placeholder="<?php esc_html_e( 'Email Address', 'wp-jobsearch' ) ?>">
                                                        </li>
														<?php
													} else if ( $field_sort_key == 'phone' && ( $field_name_swich == 'on' || $field_name_swich == 'on_req' ) ) {
														?>
                                                        <li>
                                                            <label><?php esc_html_e( 'Phone:', 'wp-jobsearch' ) ?><?php echo( $field_name_swich == 'on_req' ? ' *' : '' ) ?></label>
                                                            <input class="<?php echo( $field_name_swich == 'on_req' ? 'required-apply-field' : 'required' ) ?>"
                                                                   name="user_phone" type="text"
                                                                   placeholder="<?php esc_html_e( 'Phone Number', 'wp-jobsearch' ) ?>">
                                                        </li>
														<?php
													} else if ( $field_sort_key == 'current_jobtitle' && ( $field_name_swich == 'on' || $field_name_swich == 'on_req' ) ) {
														?>
                                                        <li>
                                                            <label><?php esc_html_e( 'Current Job Title:', 'wp-jobsearch' ) ?><?php echo( $field_name_swich == 'on_req' ? ' *' : '' ) ?></label>
                                                            <input class="<?php echo( $field_name_swich == 'on_req' ? 'required-apply-field' : 'required' ) ?>"
                                                                   name="user_job_title" type="text"
                                                                   placeholder="<?php esc_html_e( 'Current Job Title', 'wp-jobsearch' ) ?>">
                                                        </li>
														<?php
													} else if ( $field_sort_key == 'current_salary' && ( $field_name_swich == 'on' || $field_name_swich == 'on_req' ) ) {
														?>
                                                        <li>
                                                            <label><?php esc_html_e( 'Current Salary:', 'wp-jobsearch' ) ?><?php echo( $field_name_swich == 'on_req' ? ' *' : '' ) ?></label>
                                                            <input class="<?php echo( $field_name_swich == 'on_req' ? 'required-apply-field' : 'required' ) ?>"
                                                                   name="user_salary" type="text"
                                                                   placeholder="<?php esc_html_e( 'Current Salary', 'wp-jobsearch' ) ?>">
                                                        </li>
														<?php
													} else if ( $field_sort_key == 'custom_fields' && $field_name_swich == 'on' ) {
														do_action( 'jobsearch_form_custom_fields_load', 0, 'candidate' );
													} else if ( $field_sort_key == 'cv_attach' && ( $field_name_swich == 'on' || $field_name_swich == 'on_req' ) ) {
														?>
                                                        <li class="jobsearch-user-form-coltwo-full">
                                                            <div id="jobsearch-upload-cv-main"
                                                                 class="jobsearch-upload-cv jobsearch-applyjob-upload-cv">
                                                                <label><?php esc_html_e( 'Curriculum Vitae', 'wp-jobsearch' ) ?><?php echo( $field_name_swich == 'on_req' ? ' *' : '' ) ?></label>
                                                                <input class="jobsearch-disabled-input"
                                                                       id="jobsearch-uploadfile"
                                                                       placeholder="<?php esc_html_e( 'Sample_CV.pdf', 'wp-jobsearch' ) ?>"
                                                                       disabled="disabled">
                                                                <div class="jobsearch-cvupload-file">
                                                                    <span><?php esc_html_e( 'Upload CV', 'wp-jobsearch' ) ?></span>
                                                                    <input id="jobsearch-uploadbtn" type="file"
                                                                           name="candidate_cv_file"
                                                                           class="jobsearch-upload-btn <?php echo( $field_name_swich == 'on_req' ? 'cv_is_req' : '' ) ?>">
                                                                </div>
                                                                <p><?php printf( esc_html__( 'Suitable files are %s.', 'wp-jobsearch' ), $sutable_files_str ) ?></p>
                                                            </div>
                                                        </li>
														<?php
													}
												}
											} else {
												?>
                                                <li>
                                                    <label><?php esc_html_e( 'Full Name:', 'wp-jobsearch' ) ?></label>
                                                    <input class="required" name="user_fullname" type="text"
                                                           placeholder="<?php esc_html_e( 'Full Name', 'wp-jobsearch' ) ?>">
                                                </li>
                                                <li>
                                                    <label><?php esc_html_e( 'Email:', 'wp-jobsearch' ) ?></label>
                                                    <input class="required" name="user_email" type="text"
                                                           placeholder="<?php esc_html_e( 'Email Address', 'wp-jobsearch' ) ?>">
                                                </li>
                                                <li>
                                                    <label><?php esc_html_e( 'Phone:', 'wp-jobsearch' ) ?></label>
                                                    <input class="required" name="user_phone" type="text"
                                                           placeholder="<?php esc_html_e( 'Phone Number', 'wp-jobsearch' ) ?>">
                                                </li>
                                                <li>
                                                    <label><?php esc_html_e( 'Current Job Title:', 'wp-jobsearch' ) ?></label>
                                                    <input class="required" name="user_job_title" type="text"
                                                           placeholder="<?php esc_html_e( 'Current Job Title', 'wp-jobsearch' ) ?>">
                                                </li>
                                                <li>
                                                    <label><?php esc_html_e( 'Current Salary:', 'wp-jobsearch' ) ?></label>
                                                    <input class="required" name="user_salary" type="text"
                                                           placeholder="<?php esc_html_e( 'Current Salary', 'wp-jobsearch' ) ?>">
                                                </li>
												<?php do_action( 'jobsearch_form_custom_fields_load', 0, 'candidate' ); ?>
                                                <li class="jobsearch-user-form-coltwo-full">
                                                    <div id="jobsearch-upload-cv-main"
                                                         class="jobsearch-upload-cv jobsearch-applyjob-upload-cv">
                                                        <label><?php esc_html_e( 'Curriculum Vitae', 'wp-jobsearch' ) ?></label>
                                                        <input class="jobsearch-disabled-input"
                                                               id="jobsearch-uploadfile"
                                                               placeholder="<?php esc_html_e( 'Sample_CV.pdf', 'wp-jobsearch' ) ?>"
                                                               disabled="disabled">
                                                        <div class="jobsearch-cvupload-file">
                                                            <span><?php esc_html_e( 'Upload CV', 'wp-jobsearch' ) ?></span>
                                                            <input id="jobsearch-uploadbtn" type="file"
                                                                   name="candidate_cv_file"
                                                                   class="jobsearch-upload-btn">
                                                        </div>
                                                        <p><?php printf( esc_html__( 'Suitable files are %s.', 'wp-jobsearch' ), $sutable_files_str ) ?></p>
                                                    </div>
                                                </li>
												<?php
											}
											?>
                                            <li class="jobsearch-user-form-coltwo-full">
                                                <input type="hidden" name="action"
                                                       value="<?php echo apply_filters( 'jobsearch_apply_btn_action_without_reg', 'jobsearch_job_apply_without_login' ) ?>">
                                                <input type="hidden" name="job_id"
                                                       value="<?php echo absint( $job_id ) ?>">
												<?php jobsearch_terms_and_con_link_txt() ?>
                                                <input class="<?php echo apply_filters( 'jobsearch_apply_btn_class_without_reg', 'jobsearch-apply-woutreg-btn' ) ?>"
                                                       data-id="<?php echo absint( $rand_num ) ?>" type="submit"
                                                       value="<?php esc_html_e( 'Apply Job', 'wp-jobsearch' ) ?>">
                                                <div class="form-loader"></div>
                                            </li>
                                        </ul>
                                        <div class="apply-job-form-msg"></div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
					<?php
				}, 11, 1 );
			}
		}

		public function job_apply_without_login() {

			global $jobsearch_plugin_options;
			$job_id = isset( $_POST['job_id'] ) ? $_POST['job_id'] : '';

			$user_name  = isset( $_POST['user_fullname'] ) ? $_POST['user_fullname'] : '';
			$user_email = isset( $_POST['user_email'] ) ? $_POST['user_email'] : '';

			//
			$field_name_swich = isset( $jobsearch_plugin_options['aplywout_log_fname_swch'] ) ? $jobsearch_plugin_options['aplywout_log_fname_swch'] : '';

			$redirect_url = isset( $jobsearch_plugin_options['job_apply_redirect_uri'] ) ? $jobsearch_plugin_options['job_apply_redirect_uri'] : '';

			$error = 0;

			if ( $user_email != '' && $error == 0 && filter_var( $user_email, FILTER_VALIDATE_EMAIL ) ) {
				$user_email = esc_html( $user_email );
			} else {
				$error = 1;
				$msg   = esc_html__( 'Please Enter a valid email.', 'wp-jobsearch' );
			}

			if ( $field_name_swich == 'on_req' ) {
				if ( $user_name != '' && $error == 0 ) {
					$user_name = esc_html( $user_name );
				} else {
					$error = 1;
					$msg   = esc_html__( 'Please Enter your Name.', 'wp-jobsearch' );
				}
			}

			if ( $error == 1 ) {
				echo json_encode( array( 'error' => '1', 'msg' => $msg ) );
				die;
			}

			$email_parts = explode( "@", $user_email );
			$user_login  = isset( $email_parts[0] ) ? $email_parts[0] : '';
			if ( $user_login != '' && username_exists( $user_login ) ) {
				$user_login .= '_' . rand( 10000, 99999 );
			}
			if ( $user_login == '' ) {
				$user_login = 'user_' . rand( 10000, 99999 );
				$user_email = 'user_' . rand( 10000, 99999 ) . '@example.com';
			}

			$user_pass = wp_generate_password( 12 );

			$create_user = wp_create_user( $user_login, $user_pass, $user_email );

			if ( is_wp_error( $create_user ) ) {

				$registration_error_messages = $create_user->errors;

				$display_errors = '';
				foreach ( $registration_error_messages as $error ) {
					$display_errors .= $error[0];
				}

				echo json_encode( array( 'error' => '1', 'msg' => $display_errors ) );
				die;
			} else {
				wp_update_user( array( 'ID' => $create_user, 'role' => 'jobsearch_candidate' ) );
				if ( $user_name != '' ) {
					$user_def_array = array(
						'ID'           => $create_user,
						'display_name' => $user_name,
					);
					wp_update_user( $user_def_array );
				}

				$candidate_id = jobsearch_get_user_candidate_id( $create_user );

				if ( $candidate_id > 0 ) {

					if ( $user_name != '' ) {
						$cup_post = array(
							'ID'         => $candidate_id,
							'post_title' => $user_name,
						);
						wp_update_post( $cup_post );
					}

					if ( isset( $_POST['user_phone'] ) ) {
						update_post_meta( $candidate_id, 'jobsearch_field_user_phone', $_POST['user_phone'] );
					}
					if ( isset( $_POST['user_job_title'] ) ) {
						update_post_meta( $candidate_id, 'jobsearch_field_candidate_jobtitle', $_POST['user_job_title'] );
					}
					if ( isset( $_POST['user_salary'] ) ) {
						update_post_meta( $candidate_id, 'jobsearch_field_candidate_salary', $_POST['user_salary'] );
					}

					$atach_id = jobsearch_upload_candidate_cv( 'candidate_cv_file', $candidate_id );

					$multiple_cv_files_allow = isset( $jobsearch_plugin_options['multiple_cv_uploads'] ) ? $jobsearch_plugin_options['multiple_cv_uploads'] : '';

					if ( $atach_id > 0 ) {
						$file_url = wp_get_attachment_url( $atach_id );

						if ( $file_url ) {
							if ( $multiple_cv_files_allow == 'on' ) {
								$arg_arr         = array(
									'file_id'  => $atach_id,
									'file_url' => $file_url,
									'primary'  => '',
								);
								$ca_at_cv_files  = get_post_meta( $candidate_id, 'candidate_cv_files', true );
								$ca_jat_cv_files = get_post_meta( $candidate_id, 'jobsearch_field_user_cv_attachments', true );
								$ca_at_cv_files  = ! empty( $ca_at_cv_files ) ? $ca_at_cv_files : array();
								$ca_jat_cv_files = ! empty( $ca_jat_cv_files ) ? $ca_jat_cv_files : array();

								$ca_at_cv_files[ $atach_id ]  = $arg_arr;
								$ca_jat_cv_files[ $atach_id ] = $arg_arr;
								update_post_meta( $candidate_id, 'candidate_cv_files', $ca_at_cv_files );
								update_post_meta( $candidate_id, 'jobsearch_field_user_cv_attachments', $ca_jat_cv_files );
							} else {
								$arg_arr = array(
									'file_id'  => $atach_id,
									'file_url' => $file_url,
								);
								update_post_meta( $candidate_id, 'candidate_cv_file', $arg_arr );
								update_post_meta( $candidate_id, 'jobsearch_field_user_cv_attachment', $file_url );
							}
						}
					}

					$this->jobsearch_job_apply_by_job_id( $job_id, $create_user );

					$c_user = get_user_by( 'email', $user_email );
					do_action( 'jobsearch_new_user_register', $c_user, $user_pass );

					echo json_encode( array(
						'error'      => '0',
						'redrct_uri' => $redirect_url,
						'msg'        => __( 'Applied Successfully. You can view it after logged in your account. Also please check your e-mail address.', 'wp-jobsearch' )
					) );
				} else {
					echo json_encode( array(
						'error' => '1',
						'msg'   => __( 'You cannot apply this job.', 'wp-jobsearch' )
					) );
				}
			}
			die;
		}

		public function jobsearch_job_apply_by_job_id( $job_id, $user_id = '' ) {
			$candidate_id = jobsearch_get_user_candidate_id( $user_id );
			if ( $job_id > 0 && $candidate_id > 0 ) {

				$default_args     = array( 'status' => 1, 'msg' => '' );
				$dealine_response = apply_filters( 'jobsearch_check_job_deadline_date', $default_args, $job_id );

				$job_filled = get_post_meta( $job_id, 'jobsearch_field_job_filled', true );
				if ( $job_filled == 'on' ) {
					return false;
				}
				if ( $dealine_response['status'] == 1 ) {

					jobsearch_create_user_meta_list( $job_id, 'jobsearch-user-jobs-applied-list', $user_id );

					//
					$job_applicants_list = get_post_meta( $job_id, 'jobsearch_job_applicants_list', true );
					if ( $job_applicants_list != '' ) {
						$job_applicants_list = explode( ',', $job_applicants_list );
						if ( ! in_array( $candidate_id, $job_applicants_list ) ) {
							$job_applicants_list[] = $candidate_id;
						}
						$job_applicants_list = implode( ',', $job_applicants_list );
					} else {
						$job_applicants_list = $candidate_id;
					}
					update_post_meta( $job_id, 'jobsearch_job_applicants_list', $job_applicants_list );

					$user_obj = get_user_by( 'ID', $user_id );
					do_action( 'jobsearch_job_applied_to_employer', $user_obj, $job_id );

					return $candidate_id;
				}
			}
		}


		// functia de submit
		public function jobsearch_job_application_submit_callback() {
			$job_id = $_REQUEST['job_id'];


			global $jobsearch_plugin_options;
			$user     = jobsearch_get_user_id();
			$response = array();
			if ( isset( $user ) && $user <> '' ) {

				$free_job_apply      = isset( $jobsearch_plugin_options['free-job-apply-allow'] ) ? $jobsearch_plugin_options['free-job-apply-allow'] : '';
				$candidate_pkgs_page = isset( $jobsearch_plugin_options['candidate_package_page'] ) ? $jobsearch_plugin_options['candidate_package_page'] : '';

//				$redirect_url = isset( $jobsearch_plugin_options['job_apply_redirect_uri'] ) ? $jobsearch_plugin_options['job_apply_redirect_uri'] : '';
				$redirect_url = '';


				$candidate_id = jobsearch_get_user_candidate_id( $user );

				//specify post id here
				$post      = get_post( $candidate_id );
				$user_name = $post->post_name;

				$companie_id = get_post_meta( $job_id, 'jobsearch_field_job_posted_by', true );

				$redirect_url = home_url() . "/candidat/$user_name?job_id=$job_id&employer_id=$companie_id&action=preview_app";
				//  $response['url'] = home_url() . "/candidat/$user_name?job_id=$job_id&employer_id=$companie_id";


				// /candidat/johndoe/?job_id=913&employer_id=519&action=preview_profile
				$candidate_pkgs_page_url = '';
				if ( $candidate_pkgs_page != '' ) {
					$candidate_pkgs_page_obj = get_page_by_path( $candidate_pkgs_page );
					if ( is_object( $candidate_pkgs_page_obj ) && isset( $candidate_pkgs_page_obj->ID ) ) {
						$candidate_pkgs_page_url = get_permalink( $candidate_pkgs_page_obj->ID );
					}
				}


				if ( ( isset( $job_id ) && $job_id <> '' ) && $candidate_id > 0 ) {

					$candidate_skills = isset( $jobsearch_plugin_options['jobsearch_candidate_skills'] ) ? $jobsearch_plugin_options['jobsearch_candidate_skills'] : '';
					if ( $candidate_skills == 'on' ) {
						$candidate_approve_skill = isset( $jobsearch_plugin_options['jobsearch-candidate-skills-percentage'] ) ? $jobsearch_plugin_options['jobsearch-candidate-skills-percentage'] : 0;
						$candidate_skill_perc    = get_post_meta( $candidate_id, 'overall_skills_percentage', true );
						if ( ( $candidate_approve_skill > 0 && $candidate_skill_perc < $candidate_approve_skill ) ) {
							$response['status'] = 0;
							$response['msg']    = sprintf( esc_html__( 'You must have atleast %s skills set to apply this job.', 'wp-jobsearch' ), $candidate_approve_skill . '%' );
							echo json_encode( $response );
							wp_die();
						}
					}

					$default_args = array( 'status' => 1, 'msg' => '' );

					$job_filled = get_post_meta( $job_id, 'jobsearch_field_job_filled', true );
					if ( $job_filled == 'on' ) {
						$response['status'] = 0;
						$response['msg']    = esc_html__( 'This job is filled and no longer accepting applications.', 'wp-jobsearch' );
						echo json_encode( $response );
						wp_die();
					}

					$dealine_response = apply_filters( 'jobsearch_check_job_deadline_date', $default_args, $job_id );

					if ( $dealine_response['status'] == 1 ) {

						$candidate_status = get_post_meta( $candidate_id, 'jobsearch_field_candidate_approved', true );
						if ( $candidate_status != 'on' ) {
							$response['status'] = 0;
							$response['msg']    = esc_html__( 'Your profile is not approved yet.', 'wp-jobsearch' );
							echo json_encode( $response );
							wp_die();
						}

						$job_applicants_list = get_post_meta( $job_id, 'jobsearch_job_applicants_list', true );
						$job_applicants_list = $job_applicants_list != '' ? explode( ',', $job_applicants_list ) : array();
						if ( $free_job_apply != 'on' && ! in_array( $candidate_id, $job_applicants_list ) ) {
							$user_app_pkg = jobsearch_candidate_first_subscribed_app_pkg();
							if ( $user_app_pkg ) {
								do_action( 'jobsearch_add_candidate_apply_job_id_to_order', $candidate_id, $user_app_pkg );
							} else {
								$response['status'] = 0;
								if ( $candidate_pkgs_page_url != '' ) {
									$response['msg'] = wp_kses( sprintf( __( 'You have no package. <a href="%s">Click here</a> to subscribe a package.', 'wp-jobsearch' ), $candidate_pkgs_page_url ), array( 'a' => array( 'href' => array() ) ) );
								} else {
									$response['msg'] = esc_html__( 'You have no package. Please subscribe a package first.', 'wp-jobsearch' );
								}
								echo json_encode( $response );
								wp_die();
							}
						}

						//
						do_action( 'jobsearch_job_applying_before_action', $candidate_id, $job_id );
						//

						$job_employer = get_post_meta( $job_id, 'jobsearch_job_username', true );

						jobsearch_create_user_meta_list( $job_id, 'jobsearch-user-jobs-applied-list', $user );

						//
						if ( ! in_array( $candidate_id, $job_applicants_list ) ) {
							$job_applicants_list[] = $candidate_id;
						}
						if ( ! empty( $job_applicants_list ) ) {
							$job_applicants_list = implode( ',', $job_applicants_list );
						} else {
							$job_applicants_list = '';
						}

						update_post_meta( $job_id, 'jobsearch_job_applicants_list', $job_applicants_list );


						$response['fisiere1'] = $_POST['fisiere_atasate'];
						$response['texte1']   = $_POST['text_atasat'];
						$response['new_file'] = $_POST['fisierenou'];
						$response['new_file_url'] = wp_get_attachment_url( $_POST['fisierenou'] );
						$response['profil_atasat']= $_POST['profil_candidat'];




						if ( isset( $response['new_file_url'] ) ) {
							$get_job_apps_cv_att                  = get_post_meta( $job_id, 'jobsearch_job_file_new', true );
							$get_job_apps_cv_att                  = ! empty( $get_job_apps_cv_att ) ? $get_job_apps_cv_att : array();
							$get_job_apps_cv_att[ $candidate_id ] = $response['new_file_url'];
							update_post_meta( $job_id, 'jobsearch_job_file_new', $get_job_apps_cv_att );
							$response['clear'] = true;
						}

						// adauga fisiere
						if ( isset( $_POST['fisiere_atasate'] ) ) {
							$get_job_files_attached                  = get_post_meta( $job_id, 'jobsearch_job_files_attached', true );
							$get_job_files_attached                  = ! empty( $get_job_files_attached ) ? $get_job_files_attached : array();
							$get_job_files_attached[ $candidate_id ] = $_POST['fisiere_atasate'];
							$response['fisiere']                     = $get_job_files_attached;
							update_post_meta( $job_id, 'jobsearch_job_files_attached', $get_job_files_attached );
						}


						if ( isset( $_POST['text_atasat'] ) ) {
							$get_job_text_attached                  = get_post_meta( $job_id, 'jobsearch_job_text_attached', true );
							$get_job_text_attached                  = ! empty( $get_job_text_attached ) ? $get_job_text_attached : array();
							$get_job_text_attached[ $candidate_id ] = $_POST['text_atasat'];
							update_post_meta( $job_id, 'jobsearch_job_text_attached', $get_job_text_attached );
						}

						if ( isset( $_POST['profil_atasat'] ) ) {
							$get_job_candidate_profile                  = get_post_meta( $job_id, 'jobsearch_job_profil_atasat', true );
							$get_job_candidate_profile                   = ! empty( $get_job_candidate_profile  ) ? $get_job_candidate_profile  : array();

							$templates = get_post_meta( $candidate_id, "candidat_profiles", true );
							$templates = unserialize( $templates );
							$get_job_candidate_profile [ $candidate_id ] = $templates[$_POST['profil_atasat']];
							update_post_meta( $job_id, 'jobsearch_job_profil_atasat', $get_job_candidate_profile  );
						}



						$candidate_rej_jobs_list = get_post_meta( $candidate_id, 'jobsearch_rej_jobs_list', true );
						$candidate_rej_jobs_list = $candidate_rej_jobs_list != '' ? explode( ',', $candidate_rej_jobs_list ) : array();
						if ( ( $key = array_search( $job_id, $candidate_rej_jobs_list ) ) !== false ) {
							$response['job']   = $job_id;
							$response['state'] = 'rej-jobs';
							unset( $candidate_rej_jobs_list[ $key ] );
							$candidate_rej_jobs_list = implode( ',', $candidate_rej_jobs_list );
							update_post_meta( $candidate_id, 'jobsearch_rej_jobs_list', $candidate_rej_jobs_list );
						}



						$candidate_fav_jobs_list = get_post_meta( $candidate_id, 'jobsearch_fav_jobs_list', true );
						$candidate_fav_jobs_list = $candidate_fav_jobs_list != '' ? explode( ',', $candidate_fav_jobs_list ) : array();
						if ( ( $key = array_search( $job_id, $candidate_fav_jobs_list ) ) !== false ) {
							$response['job']   = $job_id;
							$response['state'] = 'fav-jobs';
							unset( $candidate_fav_jobs_list[ $key ] );
							$candidate_fav_jobs_list = implode( ',', $candidate_fav_jobs_list );
							update_post_meta( $candidate_id, 'jobsearch_fav_jobs_list', $candidate_fav_jobs_list );
						}


						$candidate_apd_jobs_list = get_post_meta( $candidate_id, 'jobsearch_apd_jobs_list', true );
						$candidate_apd_jobs_list = $candidate_apd_jobs_list != '' ? explode( ',', $candidate_apd_jobs_list ) : array();
						if ( ( $key = array_search( $job_id, $candidate_apd_jobs_list ) ) !== false ) {
							$response['job']   = $job_id;
							$response['state'] = 'apd-jobs';
							unset( $candidate_apd_jobs_list[ $key ] );
							$candidate_apd_jobs_list = implode( ',', $candidate_apd_jobs_list );
							update_post_meta( $candidate_id, 'jobsearch_apd_jobs_list', $candidate_apd_jobs_list );
						}


						//
						do_action( 'jobsearch_job_applying_save_action', $candidate_id, $job_id );
						//
						//
						$c_user = wp_get_current_user();
						do_action( 'jobsearch_job_applied_to_employer', $c_user, $job_id );

						//
						do_action( 'jobsearch_job_applying_after_save_action', $candidate_id, $job_id, $response );

						$response['status']     = 1;
						$response['redrct_uri'] = $redirect_url;
						$response['msg']        = '<i class="icon-thumbsup"></i><span>' . esc_html__( 'Applied', 'wp-jobsearch' ) . '</span>';
						$response['succmsg']    = $redirect_url != '' ? esc_html__( 'redirecting...', 'wp-jobsearch' ) : '';

						echo json_encode( $response );
						wp_die();
					} else {
						$response['status'] = 0;
						$response['msg']    = esc_html__( 'Application deadline is closed.', 'wp-jobsearch' );
					}
				} else {
					$response['status'] = 0;
					$response['msg']    = esc_html__( 'You are not authorised', 'wp-jobsearch' );
				}
			} else {
				$response['status'] = 0;
				$response['msg']    = esc_html__( 'You have to login first.', 'wp-jobsearch' );
			}
			echo json_encode( $response );

			wp_die();
		}

		public function job_apply_with_email() {
			$response = array();

			$user_name          = isset( $_POST['user_fullname'] ) ? $_POST['user_fullname'] : '';
			$user_surname       = isset( $_POST['user_surname'] ) ? $_POST['user_surname'] : '';
			$user_email         = isset( $_POST['user_email'] ) ? $_POST['user_email'] : '';
			$user_phone         = isset( $_POST['user_phone'] ) ? $_POST['user_phone'] : '';
			$user_msg           = isset( $_POST['user_msg'] ) ? $_POST['user_msg'] : '';
			$email_commun_check = isset( $_POST['email_commun_check'] ) ? $_POST['email_commun_check'] : '';

			$job_id = isset( $_POST['job_id'] ) ? $_POST['job_id'] : '';

			if ( $job_id > 0 ) {
				$employer_id     = get_post_meta( $job_id, 'jobsearch_field_job_posted_by', true );
				$job_apply_email = get_post_meta( $job_id, 'jobsearch_field_job_apply_email', true );
				if ( $job_apply_email == '' ) {
					$emp_user_id     = jobsearch_get_employer_user_id( $employer_id );
					$user_obj        = get_user_by( 'ID', $emp_user_id );
					$job_apply_email = $user_obj->user_email;
				}
				// cv file
				$att_file_path = '';
				if ( isset( $_POST['cv_file_item'] ) && ! empty( $_POST['cv_file_item'] ) ) {
					$selcted_cv_att = $_POST['cv_file_item'];
					if ( is_array( $selcted_cv_att ) ) {
						add_filter( 'upload_dir', 'jobsearch_user_upload_files_path' );
						$wp_upload_dir    = wp_upload_dir();
						$upload_file_path = array();
						foreach ( $selcted_cv_att as $sel_cv_att ) {
							$att_file_url     = wp_get_attachment_url( $sel_cv_att );
							$upload_file_path = $wp_upload_dir['path'] . '/' . basename( $att_file_url );
							//$att_file_path[] = wp_get_attachment_url($sel_cv_att);
							$att_file_path[] = $upload_file_path;
						}
						remove_filter( 'upload_dir', 'jobsearch_user_upload_files_path' );
					} else {
						$att_file_path = wp_get_attachment_url( $selcted_cv_att );
					}
				}
				if ( isset( $_FILES['cuser_cv_file'] ) && ! empty( $_FILES['cuser_cv_file'] ) ) {
					$uploded_file = $_FILES['cuser_cv_file'];
					if ( isset( $uploded_file['name'] ) && $uploded_file['name'] != '' ) {
						$att_file_path = jobsearch_cv_attachment_upload_path( 'cuser_cv_file' );
					}
				}

				//
				$apply_data = array(
					'id'                 => $job_id,
					'email'              => $job_apply_email,
					'username'           => $user_name,
					'user_surname'       => $user_surname,
					'user_email'         => $user_email,
					'user_phone'         => $user_phone,
					'user_msg'           => $user_msg,
					'att_file_path'      => $att_file_path,
					'email_commun_check' => $email_commun_check,
				);
				do_action( 'jobsearch_new_apply_job_by_email', $apply_data );

				$response['error'] = '0';
				$response['msg']   = esc_html__( 'Job applied Successfully.', 'wp-jobsearch' );
			} else {
				$response['error'] = '1';
				$response['msg']   = esc_html__( 'No job found.', 'wp-jobsearch' );
			}
			echo json_encode( $response );
			wp_die();
		}

	}

	global $jobsearch_job_application_load;
	$jobsearch_job_application_load = new Jobsearch_JobApplicationLoad();
}