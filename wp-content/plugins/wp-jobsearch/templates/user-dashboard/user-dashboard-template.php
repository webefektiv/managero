<?php
global $jobsearch_plugin_options;
do_action( 'jobsearch_user_dashboard_header' );
acf_form_head();
get_header();

$user_id           = get_current_user_id();
$user_is_candidate = jobsearch_user_is_candidate( $user_id );
$user_is_employer  = jobsearch_user_is_employer( $user_id );

//var_dump($user_is_candidate);
//die();


wp_enqueue_script( 'jobsearch-user-dashboard' );

$plugin_default_view          = isset( $jobsearch_plugin_options['jobsearch-default-page-view'] ) ? $jobsearch_plugin_options['jobsearch-default-page-view'] : 'full';
$plugin_default_view_with_str = '';
if ( $plugin_default_view == 'boxed' ) {

	$plugin_default_view_with_str = isset( $jobsearch_plugin_options['jobsearch-boxed-view-width'] ) && $jobsearch_plugin_options['jobsearch-boxed-view-width'] != '' ? $jobsearch_plugin_options['jobsearch-boxed-view-width'] : '1140px';
	if ( $plugin_default_view_with_str != '' ) {
		$plugin_default_view_with_str = ' style="width:' . $plugin_default_view_with_str . '"';
	}
}

do_action( 'jobsearch_translate_profile_with_wpml_source', $user_id );
?>


    <!-- top part-->
    <div class="container">
        <div class="featimage dashboardImage">
            <img src="<?php the_post_thumbnail_url(); ?>"/>
        </div>
        <div class="row">
            <div class="col-4">
                <div class="breadCrumps">
					<?php
					$sectiune                  = $_GET['view'];
					$tab                       = $_GET['tab'];
					//var_dump($tab);

					switch ( $tab ) {
						case 'all-jobs':
							switch ( $sectiune ) {
								case 'aplicat': ?>
                                    <a href="<?php echo home_url( '/user-dashboard/' ); ?>">
                                        Dashboard
                                    </a>
									<?php break;
								case 'de_aplicat': ?>
                                    <a href="<?php echo home_url( '/user-dashboard/' ); ?>">
                                        Dashboard
                                    </a>
                                    <span> > </span>
                                    <a href="<?php echo home_url( '/user-dashboard/?tab=all-jobs' ); ?>">
                                        Lista joburi
                                    </a>
                                    <span> > </span>
                                    <a href="javascript:void(0);">
                                        Joburi de aplicat
                                    </a>
									<?php break;
								case 'respinse': ?>
                                    <a href="<?php echo home_url( '/user-dashboard/' ); ?>">
                                        Dashboard
                                    </a>
                                    <span> > </span>
                                    <a href="<?php echo home_url( '/user-dashboard/?tab=all-jobs' ); ?>">
                                        Lista joburi
                                    </a>
                                    <span> > </span>
                                    <a href="javascript:void(0);">
                                        Joburi respinse
                                    </a>
									<?php break;
								case 'direct': ?>
                                    <a href="<?php echo home_url( '/user-dashboard/' ); ?>">
                                        Dashboard
                                    </a>
                                    <span> > </span>
                                    <a href="<?php echo home_url( '/user-dashboard/?tab=all-jobs' ); ?>">
                                        Lista joburi
                                    </a>
                                    <span> > </span>
                                    <a href="javascript:void(0);">
                                        Joburi la care ai aplicat direct
                                    </a>
									<?php break;
								case 'alerte':
									if ( $_GET['alerta'] != '' ):
										$alerta = $_GET['alerta'];
										$titlu = $_GET['alerta'];
										$titlu = str_replace( '-', ' ', $titlu );
										$titlu = ucfirst( $titlu );
										?>

                                        <a href="<?php echo home_url( '/user-dashboard/' ); ?>">
                                            Dashboard
                                        </a>
                                        <span> > </span>
                                        <a href="<?php echo home_url( '/user-dashboard/?tab=all-jobs&view=alerte' ); ?>">
                                            Alerte si filtre
                                        </a>
                                        <span> > </span>
                                        <a href="javascript:void(0);">
											<?= $titlu; ?>
                                        </a>
									<?php else: ?>
                                        <a href="<?php echo home_url( '/user-dashboard/' ); ?>">
                                            Dashboard
                                        </a>
                                        <span> > </span>
                                        <a href="javascript:void(0);">
                                            Alerte si filtre
                                        </a>
									<?php endif;
									break;

								default : ?>
                                    <a href="<?php echo home_url( '/user-dashboard/' ); ?>">
                                        Dashboard
                                    </a>
                                    <span> > </span>
                                    <a href="javascript:void(0);">
                                        Lista joburi
                                    </a>
									<?php break;
							}
							break;
						case 'alerte-joburi': ?>

                            <a href="<?php echo home_url( '/user-dashboard/' ); ?>">
                                Dashboard
                            </a>
                            <span> > </span>
                            <a href="javascript:void(0);">
                                Alerte si filtre
                            </a>

							<?php break;
						case 'texte': ?>
                            <a href="<?php echo home_url( '/user-dashboard/' ); ?>">
                                Dashboard
                            </a>
                            <span> > </span>
                            <a href="javascript:void(0);">
                                Texte PREDEFINITE
                            </a>
							<?php break;
							case 'autoevaluare': ?>
                            <a href="<?php echo home_url( '/user-dashboard/' ); ?>">
                                Dashboard
                            </a>
                            <span> > </span>
                            <a href="javascript:void(0);">
                                Autoevaluare
                            </a>
							<?php break;
						case 'fisiere': ?>
                            <a href="<?php echo home_url( '/user-dashboard/' ); ?>">
                                Dashboard
                            </a>
                            <span> > </span>
                            <a href="javascript:void(0);">
                                FISIERE
                            </a>
							<?php break;
						case 'profil-candidat': ?>
                            <a href="<?php echo home_url( '/user-dashboard/' ); ?>">
                                Dashboard
                            </a>
                            <span> > </span>
                            <a href="javascript:void(0);">
                                Profil candidat
                            </a>
							<?php break;
						case 'aplicatii': ?>
                            <a href="<?php echo home_url( '/user-dashboard/' ); ?>">
                                Dashboard
                            </a>
                            <span> > </span>
                            <a href="javascript:void(0);">
                                Liste aplicatii
                            </a>
							<?php break;
						case 'adauga-job': ?>
                            <a href="<?php echo home_url( '/user-dashboard/' ); ?>">
                                Dashboard
                            </a>
                            <span> > </span>
                            <a href="javascript:void(0);">
                                Adauga un nou job
                            </a>
							<?php break;
						default: ?>
                            <a href="<?php echo home_url( '/user-dashboard/' ); ?>">
                                Dashboard
                            </a>

							<?php break;
						case 'lista-joburi': ?>
                            <a href="<?php echo home_url( '/user-dashboard/' ); ?>">
                                Dashboard
                            </a>
                            <span> > </span>
                            <a href="<?php echo home_url( '/user-dashboard/?tab=lista-joburi' ); ?>">
                                Lista joburi
                            </a>
							<?php break;
						case 'profil-companie': ?>
                            <a href="<?php echo home_url( '/user-dashboard/' ); ?>">
                                Dashboard
                            </a>
                            <span> > </span>
                            <a href="<?php echo home_url( '/user-dashboard/?tab=profil-companie' ); ?>">
                                Profil Companie
                            </a>
							<?php break;
						case 'job-template': ?>
                            <a href="<?php echo home_url( '/user-dashboard/' ); ?>">
                                Dashboard
                            </a>
                            <span> > </span>
                            <a href="<?php echo home_url( '/user-dashboard/?tab=job-template' ); ?>">
                                Job Template
                            </a>
							<?php break;

//						case 'profil-scm': ?>
<!--                            <a href="--><?php //echo home_url( '/user-dashboard/' ); ?><!--">-->
<!--                                Dashboard-->
<!--                            </a>-->
<!--                            <span> > </span>-->
<!--                            <a href="--><?php //echo home_url( '/user-dashboard/?tab=profil-scm' ); ?><!--">-->
<!--                                Profil scm-->
<!--                            </a>-->
<!--							--><?php //break;

						case 'template-anunt': ?>
                            <a href="<?php echo home_url( '/user-dashboard/' ); ?>">
                                Dashboard
                            </a>
                            <span> > </span>
                            <a href="<?php echo home_url( '/user-dashboard/?tab=template-anunt' ); ?>">
                                Template Anunt
                            </a>
							<?php break;
						case 'file-manager': ?>
                            <a href="<?php echo home_url( '/user-dashboard/' ); ?>">
                                Dashboard
                            </a>
                            <span> > </span>
                            <a href="<?php echo home_url( '/user-dashboard/?tab=file-manager' ); ?>">
                                Fisiere
                            </a>
							<?php break;
						case 'manage-jobs': ?>
                            <a href="<?php echo home_url( '/user-dashboard/' ); ?>">
                                Dashboard
                            </a>
                            <span> > </span>
                            <a href="<?php echo home_url( '/user-dashboard/?tab=file-manager' ); ?>">
                                Manage jobs
                            </a>
							<?php break;
					}

					?>
                </div>
            </div>
            <div class="col-8">
				<?php if ( ! $user_is_candidate ):
					switch ( $tab ) {
						case 'alerte-joburi': ?>
                            <a href="<?php echo add_query_arg( array( 'tab' => 'adauga-job' ), $page_url ) ?>"
                               class="adaugaJob">
                                <i class="jobsearch-icon jobsearch-plus"></i>
								<?php esc_html_e( 'Post a New Job', 'wp-jobsearch' ) ?>
                            </a>
							<?php break;
						case 'adauga-job': ?>

                            <a href="javascript:void(0)" class="previewJob" target="_blank">
                                <i class="fa fa-eye" aria-hidden="true"></i>
                                <span><?php esc_html_e( 'Vizualizeaza Job', 'wp-jobsearch' ) ?></span>
                            </a>

							<?php break;
						case 'job-edit': ?>
                            <a href="javascript:void(0)" class="previewJob" target="_blank">
                                <i class="fa fa-eye" aria-hidden="true"></i>
                                <span><?php esc_html_e( 'Vizualizeaza Job', 'wp-jobsearch' ) ?></span>
                            </a>
							<?php break;
						default: ?>
                            <a href="<?php echo add_query_arg( array( 'tab' => 'adauga-job' ), $page_url ) ?>"
                               class="adaugaJob">
                                <i class="jobsearch-icon jobsearch-plus"></i>
								<?php esc_html_e( 'Post a New Job', 'wp-jobsearch' ) ?>
                            </a>
							<?php break;
					}

					?>

				<?php else:
					$user_obj = get_user_by( 'ID', $user_id );
					$candidate_id = jobsearch_get_user_candidate_id( $user_id );
					if ( $tab == 'profil-candidat' ): ?>

                        <a href="javascript:void(0)" class="previewJob" target="_blank">
                            <i class="fa fa-eye" aria-hidden="true"></i>
                            <span><?php esc_html_e( 'Vizualizeaza CV', 'wp-jobsearch' ) ?></span>
                        </a>

						<?php
                    elseif ( $tab = '' ): ?>
                        <div class="dashboard-top">
                            <div class="left-account-info">
                                <p class="info-account">Data inscrierii in sistem:</p>
                                <span class="info-account-date">17 februarie 2019</span>
                            </div>
                            <div class="right-account-info">
                                <p class="info-account">Data validarii profilului:</p>
                                <span class="info-account-date">17 martie 2019 ( urmatoarea validare in 12 luni, sau la cerere )</span>
                            </div>
                        </div>
					<?php endif;
				endif; ?>

            </div>
        </div>
    </div>
    <!--endtoppart-->


    <div class="jobsearch-main-content">
        <div class="jobsearch-plugin-default-container" <?php echo force_balance_tags( $plugin_default_view_with_str ); ?>>
            <div class="container">
                <div class="user-dashboard-loader" style="display: none;"></div>
                <div class="jobsearch-row">
					<?php
					if ( $user_is_candidate ) {
						$candidate_id              = jobsearch_get_user_candidate_id( $user_id );
						$user_status               = get_post_meta( $candidate_id, 'jobsearch_field_candidate_approved', true );
						$candidate_unapproved_text = isset( $jobsearch_plugin_options['unapproverd_candidate_txt'] ) ? $jobsearch_plugin_options['unapproverd_candidate_txt'] : '';
						if ( $user_status != 'on' && $candidate_unapproved_text != '' ) {
							?>
                            <div class="jobsearch-column-12">
                                <div class="jobsearch-unapproved-user-con">
                                    <p><?php echo apply_filters( 'the_content', $candidate_unapproved_text ); ?></p>
                                </div>
                            </div>
							<?php
						}
					} else if ( $user_is_employer ) {
						$employer_id              = jobsearch_get_user_employer_id( $user_id );
						$user_status              = get_post_meta( $employer_id, 'jobsearch_field_employer_approved', true );
						$employer_unapproved_text = isset( $jobsearch_plugin_options['unapproverd_employer_txt'] ) ? $jobsearch_plugin_options['unapproverd_employer_txt'] : '';
						if ( $user_status != 'on' && $employer_unapproved_text != '' ) {
							?>
                            <div class="jobsearch-column-12">
                                <div class="jobsearch-unapproved-user-con">
                                    <p><?php echo apply_filters( 'the_content', $employer_unapproved_text ); ?></p>
                                </div>
                            </div>
							<?php
						}
					}
					$get_tab = isset( $_REQUEST['tab'] ) ? $_REQUEST['tab'] : '';
					//	require_once 'user-dashboard-sidebar-left.php';
					?>
                    <div class="jobsearch-column-12 jobsearch-typo-wrap">
						<?php
						if ( $user_is_candidate ) {
							echo '<div id="dashboard-tab-settings" class="main-tab-section">';
							if ( $get_tab == 'profil-candidat' ) {
								echo( $Jobsearch_User_Dashboard_Settings->show_template_part( 'candidate', 'profil-candidat' ) );
							}
							echo '</div>' . "\n";


							echo '<div id="dashboard-tab-cv-manager" class="main-tab-section">';
							if ( $get_tab == 'texte' ) {
								echo( $Jobsearch_User_Dashboard_Settings->show_template_part( 'candidate', 'texte' ) );
							}
							echo '</div>' . "\n";

							echo '<div id="dashboard-tab-cv-manager" class="main-tab-section">';
							if ( $get_tab == 'autoevaluare' ) {
								echo( $Jobsearch_User_Dashboard_Settings->show_template_part( 'candidate', 'autoevaluare' ) );
							}
							echo '</div>' . "\n";

							echo '<div id="dashboard-tab-cv-manager" class="main-tab-section">';
							if ( $get_tab == 'fisiere' ) {
								echo( $Jobsearch_User_Dashboard_Settings->show_template_part( 'candidate', 'fisiere' ) );
							}
							echo '</div>' . "\n";


							echo '<div id="dashboard-tab-cv-manager" class="main-tab-section">';
							if ( $get_tab == 'aplicatii' ) {
								echo( $Jobsearch_User_Dashboard_Settings->show_template_part( 'candidate', 'aplicatii' ) );
							}
							echo '</div>' . "\n";

							echo '<div id="dashboard-tab-cv-manager" class="main-tab-section">';
							if ( $get_tab == 'all-jobs' ) {
								echo( $Jobsearch_User_Dashboard_Settings->show_template_part( 'candidate', 'all-jobs' ) );
							}
							echo '</div>' . "\n";


							echo '<div id="dashboard-tab-cv-manager" class="main-tab-section">';
							if ( $get_tab == 'alerte-joburi' ) {
								echo( $Jobsearch_User_Dashboard_Settings->show_template_part( 'candidate', 'alerte-joburi' ) );
							}
							echo '</div>' . "\n";


							echo apply_filters( 'jobsearch_dashboard_tab_content_ext', '', $get_tab );
							echo '<div id="dashboard-tab-change-password" class="main-tab-section">';
							if ( $get_tab == 'change-password' ) {
								echo( $Jobsearch_User_Dashboard_Settings->show_template_part( '', 'change-password' ) );
							}
							echo '</div>' . "\n";

							echo '<div id="dashboard-tab-userdashboard" class="main-tab-section">';
							if ( $get_tab == '' || empty( $get_tab ) ) {
								echo( $Jobsearch_User_Dashboard_Settings->show_template_part( 'candidate', 'dashboard' ) );
							}
							echo '</div>' . "\n";


						}

						if ( jobsearch_user_isemp_member( $user_id ) ) {
							$membusr_perms = jobsearch_emp_accmember_perms( $user_id );


							echo '<div id="dashboard-tab-settings" class="main-tab-section">';
							if ( $get_tab == '' || $get_tab == 'dashboard-settings' ) {
								echo( $Jobsearch_User_Dashboard_Settings->show_template_part( 'employer', 'memb-dashboard' ) );
							}
							echo '</div>' . "\n";

							if ( is_array( $membusr_perms ) && in_array( 'u_post_job', $membusr_perms ) ) {
								echo '<div id="dashboard-tab-user-job" class="main-tab-section">';
								if ( $get_tab == 'user-job' ) {
									echo( $Jobsearch_User_Dashboard_Settings->show_template_part( 'employer', 'job' ) );
								}
								echo '</div>' . "\n";
							}
							if ( is_array( $membusr_perms ) && in_array( 'u_manage_jobs', $membusr_perms ) ) {
								echo '<div id="dashboard-tab-manage-jobs" class="main-tab-section">';
								if ( $get_tab == 'manage-jobs' || $get_tab == 'manage-applicants' ) {
									echo( $Jobsearch_User_Dashboard_Settings->show_template_part( 'employer', 'manage-jobs' ) );
								}
								echo '</div>' . "\n";
							}
							if ( is_array( $membusr_perms ) && in_array( 'u_saved_cands', $membusr_perms ) ) {
								echo '<div id="dashboard-tab-resumes" class="main-tab-section">';
								if ( $get_tab == 'user-resumes' ) {
									echo( $Jobsearch_User_Dashboard_Settings->show_template_part( 'employer', 'shortlisted-resumes' ) );
								}
								echo '</div>' . "\n";
							}
							if ( is_array( $membusr_perms ) && in_array( 'u_packages', $membusr_perms ) ) {
								echo '<div id="dashboard-tab-packages" class="main-tab-section">';
								if ( $get_tab == 'user-packages' ) {
									echo( $Jobsearch_User_Dashboard_Settings->show_template_part( 'employer', 'packages' ) );
								}
								echo '</div>' . "\n";
							}
							if ( is_array( $membusr_perms ) && in_array( 'u_transactions', $membusr_perms ) ) {
								echo '<div id="dashboard-tab-transactions" class="main-tab-section">';
								if ( $get_tab == 'user-transactions' ) {
									echo( $Jobsearch_User_Dashboard_Settings->show_template_part( 'employer', 'transactions' ) );
								}
								echo '</div>' . "\n";
							}
							echo '<div id="dashboard-tab-change-password" class="main-tab-section">';
							if ( $get_tab == 'change-password' ) {
								echo( $Jobsearch_User_Dashboard_Settings->show_template_part( '', 'change-password' ) );
							}
							echo '</div>' . "\n";
						}

						if ( $user_is_employer ) {

							echo '<div id="dashboard-tab-settings" class="main-tab-section">';
							if ( $get_tab == 'profil-companie' ) {
								echo( $Jobsearch_User_Dashboard_Settings->show_template_part( 'employer', 'profil-companie' ) );
							}
							echo '</div>' . "\n";

//							echo '<div id="dashboard-tab-settings" class="main-tab-section">';
//							if ( $get_tab == 'profil-scm' ) {
//								echo( $Jobsearch_User_Dashboard_Settings->show_template_part( 'employer', 'profil-scm' ) );
//							}
//							echo '</div>' . "\n";

							echo '<div id="dashboard-tab-settings" class="main-tab-section">';
							if ( $get_tab == 'dashboard-settings' ) {
								echo( $Jobsearch_User_Dashboard_Settings->show_template_part( 'employer', 'dashboard-settings' ) );
							}
							echo '</div>' . "\n";

							echo '<div id="dashboard-tab-manage-jobs" class="main-tab-section">';
							if ( $get_tab == 'manage-jobs' || $get_tab == 'manage-applicants' ) {
								echo( $Jobsearch_User_Dashboard_Settings->show_template_part( 'employer', 'manage-jobs' ) );
							}
							echo '</div>' . "\n";

							echo '<div id="dashboard-tab-manage-jobs" class="main-tab-section">';
							if ( $get_tab == 'lista-joburi' || $get_tab == 'manage-applicants' ) {
								echo( $Jobsearch_User_Dashboard_Settings->show_template_part( 'employer', 'lista-joburi' ) );
							}
							echo '</div>' . "\n";

							echo '<div id="dashboard-tab-manage-jobs" class="main-tab-section">';
							if ( $get_tab == 'file-manager' ) {
								echo( $Jobsearch_User_Dashboard_Settings->show_template_part( 'employer', 'file-manager' ) );
							}
							echo '</div>' . "\n";

							echo '<div id="dashboard-tab-allapplicants" class="main-tab-section">';
							if ( $get_tab == 'ceva2' ) {
								echo( $Jobsearch_User_Dashboard_Settings->show_template_part( 'employer', 'all-applicants' ) );
							}

							echo '<div id="dashboard-tab-user-job" class="main-tab-section">';
							if ( $get_tab == 'user-job' ) {
								echo( $Jobsearch_User_Dashboard_Settings->show_template_part( 'employer', 'job' ) );
							}
							echo '</div>' . "\n";

							echo '<div id="dashboard-tab-user-job" class="main-tab-section">';
							if ( $get_tab == 'adauga-job' ) {
								echo( $Jobsearch_User_Dashboard_Settings->show_template_part( 'employer', 'adauga-job' ) );
							}
							echo '</div>' . "\n";

							echo '<div id="dashboard-tab-user-job" class="main-tab-section">';
							if ( $get_tab == 'job-template' ) {
								echo( $Jobsearch_User_Dashboard_Settings->show_template_part( 'employer', 'job-template' ) );
							}
							echo '</div>' . "\n";


							echo '<div id="dashboard-tab-user-job" class="main-tab-section">';
							if ( $get_tab == 'template-anunt' ) {
								echo( $Jobsearch_User_Dashboard_Settings->show_template_part( 'employer', 'template-anunt' ) );
							}
							echo '</div>' . "\n";


							echo '<div id="dashboard-tab-user-job" class="main-tab-section">';
							if ( $get_tab == 'job-edit' ) {
								echo( $Jobsearch_User_Dashboard_Settings->show_template_part( 'employer', 'job-edit' ) );
							}
							echo '</div>' . "\n";


							echo '<div id="dashboard-tab-userdashboard" class="main-tab-section">';
							if ( $get_tab == '' || empty( $get_tab ) ) {
								echo( $Jobsearch_User_Dashboard_Settings->show_template_part( 'employer', 'dashboard' ) );
							}
							echo '</div>' . "\n";

							echo '<div id="dashboard-tab-userdashboard" class="main-tab-section">';
							if ( $get_tab == 'blacklist' || empty( $get_tab ) ) {
								echo( $Jobsearch_User_Dashboard_Settings->show_template_part( 'employer', 'blacklist' ) );
							}
							echo '</div>' . "\n";
						}

						?>
                    </div>
					<?php
					require_once 'user-dashboard-sidebar-right.php';
					?>
                </div>
            </div>
        </div>
    </div>

<?php
get_footer();
