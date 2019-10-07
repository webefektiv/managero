<?php
global $jobsearch_plugin_options;
$get_tab = isset($_REQUEST['tab']) ? $_REQUEST['tab'] : '';
$page_id = $user_dashboard_page = isset($jobsearch_plugin_options['user-dashboard-template-page']) ? $jobsearch_plugin_options['user-dashboard-template-page'] : '';
$page_id = $user_dashboard_page = jobsearch__get_post_id($user_dashboard_page, 'page');
$page_url = jobsearch_wpml_lang_page_permalink($page_id, 'page'); //get_permalink($page_id);

$candidate_skills = isset($jobsearch_plugin_options['jobsearch_candidate_skills']) ? $jobsearch_plugin_options['jobsearch_candidate_skills'] : '';
$user_id = get_current_user_id();
$user_obj = get_user_by('ID', $user_id);
$user_def_avatar_url = get_avatar_url($user_id, array('size' => 132));

$user_displayname = isset($user_obj->display_name) ? $user_obj->display_name : '';
$user_displayname = apply_filters('jobsearch_user_display_name', $user_displayname, $user_obj);

$user_is_candidate = jobsearch_user_is_candidate($user_id);
$user_is_employer = jobsearch_user_is_employer($user_id);

$user_has_cimg = false;
if ($user_is_employer) {
	$employer_id = jobsearch_get_user_employer_id($user_id);
	$user_avatar_id = get_post_thumbnail_id($employer_id);
	if ($user_avatar_id > 0) {
		$user_has_cimg = true;
		$user_thumbnail_image = wp_get_attachment_image_src($user_avatar_id, 'thumbnail');
		$user_def_avatar_url = isset($user_thumbnail_image[0]) && esc_url($user_thumbnail_image[0]) != '' ? $user_thumbnail_image[0] : '';
	}
	$user_def_avatar_url = $user_def_avatar_url == '' ? jobsearch_employer_image_placeholder() : $user_def_avatar_url;
	$user_type = 'emp';
} else {
	$candidate_id = jobsearch_get_user_candidate_id($user_id);
	$user_avatar_id = get_post_thumbnail_id($candidate_id);
	if ($user_avatar_id > 0) {
		$user_has_cimg = true;
		$user_thumbnail_image = wp_get_attachment_image_src($user_avatar_id, 'thumbnail');
		$user_def_avatar_url = isset($user_thumbnail_image[0]) && esc_url($user_thumbnail_image[0]) != '' ? $user_thumbnail_image[0] : '';
	}
	$user_def_avatar_url = $user_def_avatar_url == '' ? jobsearch_candidate_image_placeholder() : $user_def_avatar_url;
	$user_type = 'cand';
}
?>
<aside class="jobsearch-column-2 jobsearch-typo-wrap">
	<div class="jobsearch-typo-wrap">
		<div class="jobsearch-employer-dashboard-nav">
			<?php
			if ($user_is_candidate || $user_is_employer) {
				?>
                <figure>
					<?php
					if ($user_is_candidate) {
						ob_start();
						if ($candidate_skills == 'on') {
							?>
                            <style>
                                #circle {
                                    width: 150px;
                                    height: 150px;
                                    position: relative;
                                }
                                #circle img {
                                    border-radius: 100%;
                                    position: absolute;
                                    left: 9px;
                                    top: 9px;
                                }
                            </style>
							<?php
							wp_enqueue_script('jobsearch-circle-progressbar');
						}
						?>
                        <a href="javascript:void(0);" class="user-dashthumb-remove jobsearch-tooltipcon" title="<?php esc_html_e('Delete', 'wp-jobsearch') ?>" data-uid="<?php echo ($user_id) ?>" <?php echo ($user_has_cimg ? '' : 'style="display: none;"') ?>><i class="fa fa-times"></i></a>
                        <a id="com-img-holder" href="<?php echo ($page_url) ?>" class="employer-dashboard-thumb">
							<?php if ($candidate_skills == 'on') { ?><div id="circle"><?php } ?><img src="<?php echo ($user_def_avatar_url) ?>" alt="" style="max-width: 132px;"><?php if ($candidate_skills == 'on') { ?></div><?php } ?>
                        </a>
						<?php
						$cand_prfo_photo = ob_get_clean();
						echo apply_filters('jobsearch_candidate_dash_profile_img_html', $cand_prfo_photo, $page_url, $user_def_avatar_url, $user_has_cimg);
					} else {
						ob_start();
						?>
                        <a href="javascript:void(0);" class="user-dashthumb-remove jobsearch-tooltipcon" title="<?php esc_html_e('Delete', 'wp-jobsearch') ?>" data-uid="<?php echo ($user_id) ?>" <?php echo ($user_has_cimg ? '' : 'style="display: none;"') ?>><i class="fa fa-times"></i></a>
                        <a id="com-img-holder" href="<?php echo ($page_url) ?>" class="employer-dashboard-thumb">
                            <img src="<?php echo ($user_def_avatar_url) ?>" alt="" style="max-width: 132px;">
                        </a>
						<?php
						$emp_prfo_photo = ob_get_clean();
						echo apply_filters('jobsearch_employer_dash_profile_img_html', $emp_prfo_photo, $page_url, $user_def_avatar_url, $user_has_cimg);
					}
					$uplod_txt = '';
					if ($user_is_candidate) {
						$uplod_txt = esc_html__('Upload Photo', 'wp-jobsearch');
						$uplod_txt = apply_filters('jobsearch_dash_side_cand_upload_photobtn_txt', $uplod_txt);
					} else if ($user_is_employer) {
						$uplod_txt = esc_html__('Upload Company Logo', 'wp-jobsearch');
					}

					ob_start();
					?>
                    <figcaption>
                        <span class="fileUpLoader"></span>
                        <div class="jobsearch-fileUpload">
                            <span><i class="jobsearch-icon jobsearch-add"></i> <?php echo ($uplod_txt) ?></span>
                            <input type="file" id="user_avatar" name="user_avatar" class="jobsearch-upload">
                        </div>
                        <h2><a href="<?php echo ($page_url) ?>"><?php echo ($user_displayname) ?></a></h2>
						<?php
						if ($user_is_candidate) {

							ob_start();
							$job_title = get_post_meta($candidate_id, 'jobsearch_field_candidate_jobtitle', true);
							?>
                            <span class="jobsearch-dashboard-subtitle"><?php echo ($job_title) ?></span>
							<?php
							$job_title_html = ob_get_clean();
							$job_title_html = apply_filters('jobsearch_candidate_dash_side_job_title_html', $job_title_html, $candidate_id);
							echo ($job_title_html);
							if ($candidate_skills == 'on') {
								$overall_candidate_skills = get_post_meta($candidate_id, 'overall_skills_percentage', true);
								?>
                                <div class="required-skills-detail">
									<?php
									$all_skill_msgs = jobsearch_candidate_skill_percent_count($user_id, 'msgs');
									if (!empty($all_skill_msgs) && $overall_candidate_skills < 100) {
										if (isset($all_skill_msgs[0])) {
											?>
                                            <span class="skills-perc"><?php echo ($all_skill_msgs[0]) ?></span>
											<?php
										}

										if (count($all_skill_msgs) > 1) {
											?>
                                            <a id="skill-detail-popup-btn" href="javascript:void(0);" class="get-skill-detail-btn"><?php esc_html_e('Complete Required Skills', 'wp-jobsearch') ?></a>
											<?php
											$popup_args = array(
												'p_all_skill_msgs' => $all_skill_msgs,
												'p_overall_skills' => $overall_candidate_skills,
											);
											add_action('wp_footer', function () use ($popup_args) {

												global $jobsearch_plugin_options;
												extract(shortcode_atts(array(
													'p_all_skill_msgs' => '',
													'p_overall_skills' => '',
												), $popup_args));

												$candidate_min_skill = isset($jobsearch_plugin_options['jobsearch-candidate-skills-percentage']) && $jobsearch_plugin_options['jobsearch-candidate-skills-percentage'] > 0 ? $jobsearch_plugin_options['jobsearch-candidate-skills-percentage'] : 0;
												$p_overall_skills = $p_overall_skills > 0 ? $p_overall_skills : 0;

												$low_skills_clr = isset($jobsearch_plugin_options['skill_low_set_color']) && $jobsearch_plugin_options['skill_low_set_color'] != '' ? $jobsearch_plugin_options['skill_low_set_color'] : '#13b5ea';
												$med_skills_clr = isset($jobsearch_plugin_options['skill_med_set_color']) && $jobsearch_plugin_options['skill_med_set_color'] != '' ? $jobsearch_plugin_options['skill_med_set_color'] : '#13b5ea';
												$high_skills_clr = isset($jobsearch_plugin_options['skill_high_set_color']) && $jobsearch_plugin_options['skill_high_set_color'] != '' ? $jobsearch_plugin_options['skill_high_set_color'] : '#13b5ea';
												$comp_skills_clr = isset($jobsearch_plugin_options['skill_ahigh_set_color']) && $jobsearch_plugin_options['skill_ahigh_set_color'] != '' ? $jobsearch_plugin_options['skill_ahigh_set_color'] : '#13b5ea';

												$final_color = '#13b5ea';
												if ($p_overall_skills <= 25) {
													$final_color = $low_skills_clr;
												} else if ($p_overall_skills > 25 && $p_overall_skills <= 50) {
													$final_color = $med_skills_clr;
												} else if ($p_overall_skills > 50 && $p_overall_skills <= 75) {
													$final_color = $high_skills_clr;
												} else if ($p_overall_skills > 75) {
													$final_color = $comp_skills_clr;
												}
												?>
                                                <div class="jobsearch-modal fade" id="JobSearchModalSkillsDetail">
                                                    <div class="modal-inner-area">&nbsp;</div>
                                                    <div class="modal-content-area">
                                                        <div class="modal-box-area">
                                                            <span class="modal-close"><i class="fa fa-times"></i></span>
                                                            <div class="jobsearch-skills-set-popup">
                                                                <div class="complet-title">
                                                                    <h5><?php esc_html_e('Profile Completion', 'wp-jobsearch') ?></h5>
                                                                </div>
                                                                <div class="profile-completion-con">
                                                                    <div class="complet-percent">
                                                                        <span class="percent-num" style="color: <?php echo ($final_color) ?>;"><?php echo ($p_overall_skills) ?>%</span>
                                                                        <div class="percent-bar">
                                                                            <span style="width: <?php echo ($p_overall_skills) ?>%; background-color: <?php echo ($final_color) ?>;"></span>
                                                                        </div>
                                                                    </div>
                                                                    <div class="minimum-percent">
                                                                        <span><?php esc_html_e('Minimum Required', 'wp-jobsearch') ?></span>
                                                                        <small><?php echo ($candidate_min_skill) ?>% </small>
                                                                    </div>
                                                                </div>
                                                                <div class="profile-improve-con">
                                                                    <div class="improve-title">
                                                                        <h5><?php esc_html_e('Improve your profile', 'wp-jobsearch') ?></h5>
                                                                    </div>
                                                                    <ul>
																		<?php
																		foreach ($p_all_skill_msgs as $all_skill_msg) {
																			?>
                                                                            <li><?php echo ($all_skill_msg) ?></li>
																			<?php
																		}
																		?>
                                                                    </ul>
                                                                </div>
                                                            </div>

                                                        </div>
                                                    </div>
                                                </div>
												<?php
											}, 11, 1);
										}
									}
									?>
                                </div>
								<?php
							}
						}
						?>
                    </figcaption>
					<?php
					$cand_prfo_photof = ob_get_clean();
					echo apply_filters('jobsearch_cand_dash_profile_imgfcaption_html', $cand_prfo_photof, $page_url, $user_displayname);
					?>
                </figure>
				<?php
			} else {
				?>
                <h2><a><?php echo ($user_obj->display_name) ?></a></h2>
				<?php
			}
			?>
		</div>
	</div>
</aside>