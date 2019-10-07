<?php
global $jobsearch_plugin_options;
do_action( 'jobsearch_user_dashboard_header' );

get_header();

$user_id           = get_current_user_id();
$user_is_candidate = jobsearch_user_is_candidate( $user_id );
$user_is_employer  = jobsearch_user_is_employer( $user_id );
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

    <div class="container">
        <div class="featimage">
            <img src="<?php the_post_thumbnail_url(); ?>"/>
        </div>
        <div class="row">
            <div class="col-4">
                <h1 class="page-title-top"><?php the_title(); ?></h1>
            </div>
            <div class="col-8">
                <?php if( ! $user_is_candidate): ?>
                <a href="<?php echo add_query_arg( array( 'tab' => 'user-job' ), $page_url ) ?>" class="adaugaJob">
                    <i class="jobsearch-icon jobsearch-plus"></i>
					<?php esc_html_e( 'Post a New Job', 'wp-jobsearch' ) ?>
                </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
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
							if ( $get_tab == 'dashboard-settings' ) {
								echo( $Jobsearch_User_Dashboard_Settings->show_template_part( 'candidate', 'dashboard-settings' ) );
							}
							echo '</div>' . "\n";
							echo '<div id="dashboard-tab-my-resume" class="main-tab-section">';
							if ( $get_tab == 'my-resume' ) {
								echo( $Jobsearch_User_Dashboard_Settings->show_template_part( 'candidate', 'my-resume' ) );
							}
							echo '</div>' . "\n";
							echo '<div id="dashboard-tab-favourite-jobs" class="main-tab-section">';
							if ( $get_tab == 'favourite-jobs' ) {
								echo( $Jobsearch_User_Dashboard_Settings->show_template_part( 'candidate', 'favourite-jobs' ) );
							}
							echo '</div>' . "\n";
							echo '<div id="dashboard-tab-favourite-jobs" class="main-tab-section">';
							if ( $get_tab == 'reject-jobs' ) {
								echo( $Jobsearch_User_Dashboard_Settings->show_template_part( 'candidate', 'reject-jobs' ) );
							}
							echo '</div>' . "\n";

							echo '<div id="dashboard-tab-applied-jobs" class="main-tab-section">';
							if ( $get_tab == 'applied-jobs' ) {
								echo( $Jobsearch_User_Dashboard_Settings->show_template_part( 'candidate', 'applied-jobs' ) );
							}
							echo '</div>' . "\n";

							echo '<div id="dashboard-tab-packages" class="main-tab-section">';
							if ( $get_tab == 'user-packages' ) {
								echo( $Jobsearch_User_Dashboard_Settings->show_template_part( 'candidate', 'packages' ) );
							}
							echo '</div>' . "\n";
							echo '<div id="dashboard-tab-transactions" class="main-tab-section">';
							if ( $get_tab == 'user-transactions' ) {
								echo( $Jobsearch_User_Dashboard_Settings->show_template_part( 'candidate', 'transactions' ) );
							}
							echo '</div>' . "\n";
							echo '<div id="dashboard-tab-cv-manager" class="main-tab-section">';
							if ( $get_tab == 'cv-manager' ) {
								echo( $Jobsearch_User_Dashboard_Settings->show_template_part( 'candidate', 'cv-manager' ) );
							}
							echo '</div>' . "\n";
							echo '<div id="dashboard-tab-cv-manager" class="main-tab-section">';
							if ( $get_tab == 'text-manager' ) {
								echo( $Jobsearch_User_Dashboard_Settings->show_template_part( 'candidate', 'text-manager' ) );
							}
							echo '</div>' . "\n";
							echo '<div id="dashboard-tab-stats" class="main-tab-section">';
							if ( $get_tab == '' ) {
								echo( $Jobsearch_User_Dashboard_Settings->show_template_part( 'candidate', 'stats' ) );
							}
							echo '</div>' . "\n";
							echo apply_filters( 'jobsearch_dashboard_tab_content_ext', '', $get_tab );
							echo '<div id="dashboard-tab-change-password" class="main-tab-section">';
							if ( $get_tab == 'change-password' ) {
								echo( $Jobsearch_User_Dashboard_Settings->show_template_part( '', 'change-password' ) );
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
							if ( $get_tab == 'file-manager') {
								echo( $Jobsearch_User_Dashboard_Settings->show_template_part( 'employer', 'file-manager' ) );
							}
							echo '</div>' . "\n";

							echo '<div id="dashboard-tab-allapplicants" class="main-tab-section">';
							if ( $get_tab == 'all-applicants' ) {
								echo( $Jobsearch_User_Dashboard_Settings->show_template_part( 'employer', 'all-applicants' ) );
							}
							echo '</div>' . "\n";
							echo '<div id="dashboard-tab-transactions" class="main-tab-section">';

							if ( $get_tab == 'user-transactions' ) {
								echo( $Jobsearch_User_Dashboard_Settings->show_template_part( 'employer', 'transactions' ) );
							}
							echo '</div>' . "\n";
							echo '<div id="dashboard-tab-resumes" class="main-tab-section">';
							if ( $get_tab == 'user-resumes' ) {
								echo( $Jobsearch_User_Dashboard_Settings->show_template_part( 'employer', 'shortlisted-resumes' ) );
							}
							echo '</div>' . "\n";
							echo '<div id="dashboard-tab-packages" class="main-tab-section">';
							if ( $get_tab == 'user-packages' ) {
								echo( $Jobsearch_User_Dashboard_Settings->show_template_part( 'employer', 'packages' ) );
							}
							echo '</div>' . "\n";
							echo '<div id="dashboard-tab-user-job" class="main-tab-section">';
							if ( $get_tab == 'user-job' ) {
								echo( $Jobsearch_User_Dashboard_Settings->show_template_part( 'employer', 'job' ) );
							}
							echo '</div>' . "\n";
							echo '<div id="dashboard-tab-stats" class="main-tab-section">';
							if ( $get_tab == '' ) {
								echo( $Jobsearch_User_Dashboard_Settings->show_template_part( 'employer', 'stats' ) );
							}
							echo '</div>' . "\n";
							echo apply_filters( 'jobsearch_dashboard_tab_content_ext', '', $get_tab );
							echo '<div id="dashboard-tab-change-password" class="main-tab-section">';
							if ( $get_tab == 'change-password' ) {
								echo( $Jobsearch_User_Dashboard_Settings->show_template_part( '', 'change-password' ) );
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
