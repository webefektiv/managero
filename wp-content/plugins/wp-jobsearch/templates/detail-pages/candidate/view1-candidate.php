<?php
/**
 * The template for displaying all single posts.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 */

global $post, $jobsearch_plugin_options;
$candidate_id = $post->ID;

$candidate_user_id = jobsearch_get_candidate_user_id($candidate_id);

$candidates_reviews = isset($jobsearch_plugin_options['candidate_reviews_switch']) ? $jobsearch_plugin_options['candidate_reviews_switch'] : '';

$all_location_allow = isset($jobsearch_plugin_options['all_location_allow']) ? $jobsearch_plugin_options['all_location_allow'] : '';

$view_candidate = true;
$restrict_candidates = isset($jobsearch_plugin_options['restrict_candidates']) ? $jobsearch_plugin_options['restrict_candidates'] : '';
$restrict_candidates_for_users = isset($jobsearch_plugin_options['restrict_candidates_for_users']) ? $jobsearch_plugin_options['restrict_candidates_for_users'] : '';

$is_employer = false;
if ($restrict_candidates == 'on') {
    $view_candidate = false;

    if (is_user_logged_in()) {
        $cur_user_id = get_current_user_id();
        $cur_user_obj = wp_get_current_user();
        $employer_id = jobsearch_get_user_employer_id($cur_user_id);
        $ucandidate_id = jobsearch_get_user_candidate_id($cur_user_id);
        $employer_dbstatus = get_post_meta($employer_id, 'jobsearch_field_employer_approved', true);
        if ($employer_id > 0 && $employer_dbstatus == 'on') {
            $is_employer = true;
            if ($restrict_candidates_for_users == 'register_resume') {
                $user_cv_pkg = jobsearch_employer_first_subscribed_cv_pkg();
                if ($user_cv_pkg) {
                    $view_candidate = true;
                }
            } else if ($restrict_candidates_for_users == 'only_applicants') {
                $employer_job_args = array(
                    'post_type' => 'job',
                    'posts_per_page' => '-1',
                    'post_status' => 'publish',
                    'fields' => 'ids',
                    'meta_query' => array(
                        array(
                            'key' => 'jobsearch_field_job_posted_by',
                            'value' => $employer_id,
                            'compare' => '=',
                        ),
                    ),
                );
                $employer_jobs_query = new WP_Query($employer_job_args);
                $employer_jobs_posts = $employer_jobs_query->posts;
                if (!empty($employer_jobs_posts) && is_array($employer_jobs_posts)) {
                    foreach ($employer_jobs_posts as $employer_job_id) {
                        $finded_result_list = jobsearch_find_index_user_meta_list($employer_job_id, 'jobsearch-user-jobs-applied-list', 'post_id', $candidate_user_id);
                        if (is_array($finded_result_list) && !empty($finded_result_list)) {
                            $view_candidate = true;
                            break;
                        }
                    }
                }
            } else {
                $view_candidate = true;
            }
        } else if (in_array('administrator', (array) $cur_user_obj->roles)) {
            $view_candidate = true;
        } else if ($ucandidate_id > 0 && $ucandidate_id == $candidate_id) {
            $view_candidate = true;
        }
    }
}

$captcha_switch = isset($jobsearch_plugin_options['captcha_switch']) ? $jobsearch_plugin_options['captcha_switch'] : '';
$jobsearch_sitekey = isset($jobsearch_plugin_options['captcha_sitekey']) ? $jobsearch_plugin_options['captcha_sitekey'] : '';

$plugin_default_view = isset($jobsearch_plugin_options['jobsearch-default-page-view']) ? $jobsearch_plugin_options['jobsearch-default-page-view'] : 'full';
$plugin_default_view_with_str = '';
if ($plugin_default_view == 'boxed') {

    $plugin_default_view_with_str = isset($jobsearch_plugin_options['jobsearch-boxed-view-width']) && $jobsearch_plugin_options['jobsearch-boxed-view-width'] != '' ? $jobsearch_plugin_options['jobsearch-boxed-view-width'] : '1140px';
    if ($plugin_default_view_with_str != '') {
        $plugin_default_view_with_str = ' style="width:' . $plugin_default_view_with_str . '"';
    }
}

wp_enqueue_script('jobsearch-progressbar');

$inopt_cover_letr = isset($jobsearch_plugin_options['cand_resm_cover_letr']) ? $jobsearch_plugin_options['cand_resm_cover_letr'] : '';
$inopt_resm_education = isset($jobsearch_plugin_options['cand_resm_education']) ? $jobsearch_plugin_options['cand_resm_education'] : '';
$inopt_resm_experience = isset($jobsearch_plugin_options['cand_resm_experience']) ? $jobsearch_plugin_options['cand_resm_experience'] : '';
$inopt_resm_portfolio = isset($jobsearch_plugin_options['cand_resm_portfolio']) ? $jobsearch_plugin_options['cand_resm_portfolio'] : '';
$inopt_resm_skills = isset($jobsearch_plugin_options['cand_resm_skills']) ? $jobsearch_plugin_options['cand_resm_skills'] : '';
$inopt_resm_honsawards = isset($jobsearch_plugin_options['cand_resm_honsawards']) ? $jobsearch_plugin_options['cand_resm_honsawards'] : '';

$candidate_obj = get_post($candidate_id);
$candidate_content = $candidate_obj->post_content;
$candidate_content = apply_filters('the_content', $candidate_content);

$candidate_join_date = isset($candidate_obj->post_date) ? $candidate_obj->post_date : '';

$candidate_jobtitle = get_post_meta($candidate_id, 'jobsearch_field_candidate_jobtitle', true);
$candidate_address = get_post_meta($candidate_id, 'jobsearch_field_location_address', true);
if ($candidate_address == '') {
    $candidate_address = jobsearch_job_item_address($candidate_id);
}

$user_facebook_url = get_post_meta($candidate_id, 'jobsearch_field_user_facebook_url', true);
$user_twitter_url = get_post_meta($candidate_id, 'jobsearch_field_user_twitter_url', true);
$user_google_plus_url = get_post_meta($candidate_id, 'jobsearch_field_user_google_plus_url', true);
$user_youtube_url = get_post_meta($candidate_id, 'jobsearch_field_user_youtube_url', true);
$user_dribbble_url = get_post_meta($candidate_id, 'jobsearch_field_user_dribbble_url', true);
$user_linkedin_url = get_post_meta($candidate_id, 'jobsearch_field_user_linkedin_url', true);

$user_id = jobsearch_get_candidate_user_id($candidate_id);
$user_obj = get_user_by('ID', $user_id);
$user_displayname = isset($user_obj->display_name) ? $user_obj->display_name : '';
$user_displayname = apply_filters('jobsearch_user_display_name', $user_displayname, $user_obj);

$user_def_avatar_url = get_avatar_url($user_id, array('size' => 184));

$user_avatar_id = get_post_thumbnail_id($candidate_id);
if ($user_avatar_id > 0) {
    $user_thumbnail_image = wp_get_attachment_image_src($user_avatar_id, 'thumbnail');
    $user_def_avatar_url = isset($user_thumbnail_image[0]) && esc_url($user_thumbnail_image[0]) != '' ? $user_thumbnail_image[0] : '';
}
$user_def_avatar_url = $user_def_avatar_url == '' ? jobsearch_candidate_image_placeholder() : $user_def_avatar_url;
wp_enqueue_script('isotope-min');
?>

<div class="jobsearch-main-content" style="display: none">

    <!-- Main Section -->
    <div class="jobsearch-main-section">
        <div class="jobsearch-plugin-default-container" <?php echo force_balance_tags($plugin_default_view_with_str); ?>>
            <div class="jobsearch-row">
                <?php
                if ($view_candidate === false) {
                    $restrict_img = isset($jobsearch_plugin_options['candidate_restrict_img']) ? $jobsearch_plugin_options['candidate_restrict_img'] : '';
                    $restrict_img_url = isset($restrict_img['url']) ? $restrict_img['url'] : '';
                    $restrict_cv_pckgs = isset($jobsearch_plugin_options['restrict_cv_packages']) ? $jobsearch_plugin_options['restrict_cv_packages'] : '';
                    $restrict_msg = isset($jobsearch_plugin_options['restrict_cand_msg']) && $jobsearch_plugin_options['restrict_cand_msg'] != '' ? $jobsearch_plugin_options['restrict_cand_msg'] : esc_html__('The Page is Restricted only for Subscribed Employers', 'wp-jobsearch');
                    ?>
                    <div class="jobsearch-column-12">
                        <div class="restrict-candidate-sec">
                            <img src="<?php echo ($restrict_img_url) ?>" alt="">
                            <h2><?php echo ($restrict_msg) ?></h2>

                            <?php
                            if ($is_employer) {
                                ?>
                                <p><?php esc_html_e('Please buy a C.V package to view this candidate.', 'wp-jobsearch') ?></p>
                                <?php
                            } else if (is_user_logged_in()) {
                                ?>
                                <p><?php esc_html_e('You are not an employer. Only an Employer can view a candidate.', 'wp-jobsearch') ?></p>
                                <?php
                            } else {
                                ?>
                                <p><?php esc_html_e('If you are employer just login to view this candidate or buy a C.V package to download His Resume.', 'wp-jobsearch') ?></p>
                                <?php
                            }
                            if (is_user_logged_in()) {
                                ?>
                                <div class="login-btns">
                                    <a href="<?php echo wp_logout_url(home_url('/')); ?>"><i class="jobsearch-icon jobsearch-logout"></i><?php esc_html_e('Logout', 'wp-jobsearch') ?></a>
                                </div>
                                <?php
                            } else {
                                ?>
                                <div class="login-btns">
                                    <a href="javascript:void(0);" class="jobsearch-open-signin-tab"><i class="jobsearch-icon jobsearch-user"></i><?php esc_html_e('Login', 'wp-jobsearch') ?></a>
                                    <a href="javascript:void(0);" class="jobsearch-open-register-tab"><i class="jobsearch-icon jobsearch-plus"></i><?php esc_html_e('Become a Employer', 'wp-jobsearch') ?></a>
                                </div>
                                <?php
                            }
                            if (!empty($restrict_cv_pckgs) && is_array($restrict_cv_pckgs) && $restrict_candidates_for_users == 'register_resume') {
                                ?>
                                <div class="jobsearch-box-title">
                                    <span><?php esc_html_e('OR', 'wp-jobsearch') ?></span>
                                </div>
                                <?php
                            }
                            ?>
                        </div>
                        <?php
                        if (!empty($restrict_cv_pckgs) && is_array($restrict_cv_pckgs) && $restrict_candidates_for_users == 'register_resume') {
                            wp_enqueue_script('jobsearch-packages-scripts');
                            ?>
                            <div class="cv-packages-section">
                                <div class="packages-title"><h2><?php esc_html_e('Buy any CV Packages to get started', 'wp-jobsearch') ?></h2></div>
                                <?php
                                ob_start();
                                ?>
                                <div class="jobsearch-row">
                                    <?php
                                    foreach ($restrict_cv_pckgs as $restrict_cv_pckg) {
                                        $cv_pkg_obj = $restrict_cv_pckg != '' ? get_page_by_path($restrict_cv_pckg, 'OBJECT', 'package') : '';
                                        if (is_object($cv_pkg_obj) && isset($cv_pkg_obj->ID)) {
                                            $cv_pkg_id = $cv_pkg_obj->ID;
                                            $pkg_type = get_post_meta($cv_pkg_id, 'jobsearch_field_charges_type', true);
                                            $pkg_price = get_post_meta($cv_pkg_id, 'jobsearch_field_package_price', true);

                                            $num_of_cvs = get_post_meta($cv_pkg_id, 'jobsearch_field_num_of_cvs', true);
                                            $pkg_exp_dur = get_post_meta($cv_pkg_id, 'jobsearch_field_package_expiry_time', true);
                                            $pkg_exp_dur_unit = get_post_meta($cv_pkg_id, 'jobsearch_field_package_expiry_time_unit', true);

                                            $pkg_exfield_title = get_post_meta($cv_pkg_id, 'jobsearch_field_package_exfield_title', true);
                                            $pkg_exfield_val = get_post_meta($cv_pkg_id, 'jobsearch_field_package_exfield_val', true);
                                            $pkg_exfield_status = get_post_meta($cv_pkg_id, 'jobsearch_field_package_exfield_status', true);
                                            ?>
                                            <div class="jobsearch-column-4">
                                                <div class="jobsearch-classic-priceplane">
                                                    <h2><?php echo get_the_title($cv_pkg_id) ?></h2>
                                                    <div class="jobsearch-priceplane-section">
                                                        <?php
                                                        if ($pkg_type == 'paid') {
                                                            echo '<span>' . jobsearch_get_price_format($pkg_price) . ' <small>' . esc_html__('only', 'wp-jobsearch') . '</small></span>';
                                                        } else {
                                                            echo '<span>' . esc_html__('Free', 'wp-jobsearch') . '</span>';
                                                        }
                                                        ?>
                                                    </div>
                                                    <div class="grab-classic-priceplane">
                                                        <ul>
                                                            <?php
                                                            if (!empty($pkg_exfield_title)) {
                                                                $_exf_counter = 0;
                                                                foreach ($pkg_exfield_title as $_exfield_title) {
                                                                    $_exfield_val = isset($pkg_exfield_val[$_exf_counter]) ? $pkg_exfield_val[$_exf_counter] : '';
                                                                    $_exfield_status = isset($pkg_exfield_status[$_exf_counter]) ? $pkg_exfield_status[$_exf_counter] : '';
                                                                    if ($_exfield_val != '') {
                                                                        ?>
                                                                        <li<?php echo ( $_exfield_status == 'active' ? ' class="active"' : '') ?>><i class="jobsearch-icon jobsearch-check-square"></i> <?php echo $_exfield_title . ' ' . $_exfield_val ?></li>
                                                                        <?php
                                                                    }
                                                                    $_exf_counter++;
                                                                }
                                                            }
                                                            ?>
                                                        </ul>
                                                        <?php if (is_user_logged_in()) { ?>
                                                            <a href="javascript:void(0);" class="jobsearch-classic-priceplane-btn jobsearch-subscribe-cv-pkg" data-id="<?php echo ($cv_pkg_id) ?>"><?php esc_html_e('Get Started', 'wp-jobsearch') ?> </a>
                                                            <span class="pkg-loding-msg" style="display:none;"></span>
                                                        <?php } else { ?>
                                                            <a href="javascript:void(0);" class="jobsearch-classic-priceplane-btn jobsearch-open-signin-tab"><?php esc_html_e('Get Started', 'wp-jobsearch') ?> </a>
                                                        <?php } ?>
                                                    </div>
                                                </div>
                                            </div>
                                            <?php
                                        }
                                    }
                                    ?>
                                </div>
                                <?php
                                $pkgs_html = ob_get_clean();
                                echo apply_filters('jobsearch_restrict_candidate_pakgs_html', $pkgs_html, $restrict_cv_pckgs);
                                ?>
                            </div>
                            <?php
                        }
                        ?>
                    </div>
                    <?php
                } else {
                    $sectors_enable_switch = isset($jobsearch_plugin_options['sectors_onoff_switch']) ? $jobsearch_plugin_options['sectors_onoff_switch'] : '';
                    $cand_dob_switch = isset($jobsearch_plugin_options['cand_dob_switch']) ? $jobsearch_plugin_options['cand_dob_switch'] : 'on';
                    $candidate_age = jobsearch_candidate_age($candidate_id);
                    $candidate_salary_switch = isset($jobsearch_plugin_options['cand_salary_switch']) ? $jobsearch_plugin_options['cand_salary_switch'] : 'on';
                    $candidate_salary = jobsearch_candidate_current_salary($candidate_id);
                    $sectors = wp_get_post_terms($candidate_id, 'sector');
                    $candidate_sector = isset($sectors[0]->name) ? $sectors[0]->name : '';
                    ?>
                    <aside class="jobsearch-column-4 jobsearch-typo-wrap">
                        <div class="widget widget_candidate_info">
                            <div class="jobsearch_candidate_info">
                                <?php echo jobsearch_member_promote_profile_iconlab($candidate_id); ?>
                                <?php echo jobsearch_cand_urgent_pkg_iconlab($candidate_id) ?>
                                <figure><img src="<?php echo ($user_def_avatar_url) ?>" alt=""></figure>
                                <?php
                                if ($candidates_reviews == 'on') {
                                    $post_avg_review_args = array(
                                        'post_id' => $candidate_id,
                                    );
                                    do_action('jobsearch_cand_detail_post_avg_rating', $post_avg_review_args);
                                }

                                ob_start();
                                ?>
                                <h2><a><?php echo apply_filters('jobsearch_candidate_detail_side_displayname', $user_displayname, $candidate_id) ?></a></h2>
                                <p><?php echo ($candidate_jobtitle) ?></p>
                                <?php
                                if ($candidate_sector != '' && $sectors_enable_switch == 'on') {
                                    echo '<p>' . sprintf(esc_html__('Sector: %s', 'wp-jobsearch'), apply_filters('jobsearch_gew_wout_anchr_sector_str_html', $candidate_sector, $candidate_id)) . '</p>';
                                }
                                
                                do_action('jobsearch_cand_detail_side_after_sector', $candidate_id);
                                if ($candidate_salary != '' && $candidate_salary_switch == 'on') {
                                    echo '<p>' . sprintf(esc_html__('Salary: %s', 'wp-jobsearch'), $candidate_salary) . '</p>';
                                }
                                do_action('jobsearch_cand_detail_side_after_salary', $candidate_id);
                                if ($candidate_age != '' && $cand_dob_switch == 'on') {
                                    echo apply_filters('jobsearch_candidate_detail_page_age_html', '<p>' . sprintf(esc_html__('(Age: %s years)', 'wp-jobsearch'), $candidate_age) . '</p>');
                                }
                                if ($candidate_address != '' && $all_location_allow == 'on') {
                                    ?>
                                    <span><?php echo ($candidate_address) ?></span>
                                    <?php
                                }
                                if ($candidate_join_date != '') {
                                    ?>
                                    <small><?php printf(esc_html__('Member Since, %s', 'wp-jobsearch'), date_i18n('M d, Y', strtotime($candidate_join_date))) ?></small>
                                    <?php
                                }

                                $cand_alow_fb_smm = isset($jobsearch_plugin_options['cand_alow_fb_smm']) ? $jobsearch_plugin_options['cand_alow_fb_smm'] : '';
                                $cand_alow_twt_smm = isset($jobsearch_plugin_options['cand_alow_twt_smm']) ? $jobsearch_plugin_options['cand_alow_twt_smm'] : '';
                                $cand_alow_gplus_smm = isset($jobsearch_plugin_options['cand_alow_gplus_smm']) ? $jobsearch_plugin_options['cand_alow_gplus_smm'] : '';
                                $cand_alow_linkd_smm = isset($jobsearch_plugin_options['cand_alow_linkd_smm']) ? $jobsearch_plugin_options['cand_alow_linkd_smm'] : '';
                                $cand_alow_dribbb_smm = isset($jobsearch_plugin_options['cand_alow_dribbb_smm']) ? $jobsearch_plugin_options['cand_alow_dribbb_smm'] : '';
                                $candidate_social_mlinks = isset($jobsearch_plugin_options['candidate_social_mlinks']) ? $jobsearch_plugin_options['candidate_social_mlinks'] : '';

                                if (!empty($candidate_social_mlinks) || ($cand_alow_fb_smm == 'on' || $cand_alow_twt_smm == 'on' || $cand_alow_gplus_smm == 'on' || $cand_alow_linkd_smm == 'on' || $cand_alow_dribbb_smm == 'on')) {
                                    ob_start();
                                    ?>
                                    <ul>
                                        <?php
                                        if ($user_facebook_url != '' && $cand_alow_fb_smm == 'on') {
                                            ?>
                                            <li><a href="<?php echo ($user_facebook_url) ?>" data-original-title="facebook" class="jobsearch-icon jobsearch-facebook-logo"></a></li>
                                            <?php
                                        }
                                        if ($user_twitter_url != '' && $cand_alow_twt_smm == 'on') {
                                            ?>
                                            <li><a href="<?php echo ($user_twitter_url) ?>" data-original-title="twitter" class="jobsearch-icon jobsearch-twitter-logo"></a></li>
                                            <?php
                                        }
                                        if ($user_linkedin_url != '' && $cand_alow_gplus_smm == 'on') {
                                            ?>
                                            <li><a href="<?php echo ($user_linkedin_url) ?>" data-original-title="linkedin" class="jobsearch-icon jobsearch-linkedin-button"></a></li>
                                            <?php
                                        }
                                        if ($user_google_plus_url != '' && $cand_alow_linkd_smm == 'on') {
                                            ?>
                                            <li><a href="<?php echo ($user_google_plus_url) ?>" data-original-title="google-plus" class="jobsearch-icon jobsearch-google-plus-logo-button"></a></li>
                                            <?php
                                        }
                                        if ($user_dribbble_url != '' && $cand_alow_dribbb_smm == 'on') {
                                            ?>
                                            <li><a href="<?php echo ($user_dribbble_url) ?>" data-original-title="dribbble" class="jobsearch-icon jobsearch-dribbble-logo"></a></li>
                                            <?php
                                        }
                                        if (!empty($candidate_social_mlinks)) {
                                            if (isset($candidate_social_mlinks['title']) && is_array($candidate_social_mlinks['title'])) {
                                                $field_counter = 0;
                                                foreach ($candidate_social_mlinks['title'] as $field_title_val) {
                                                    $field_random = rand(10000000, 99999999);
                                                    $field_icon_styles = '';
                                                    $field_icon = isset($candidate_social_mlinks['icon'][$field_counter]) ? $candidate_social_mlinks['icon'][$field_counter] : '';
                                                    $field_icon_group = isset($candidate_social_mlinks['icon_group'][$field_counter]) ? $candidate_social_mlinks['icon_group'][$field_counter] : '';
                                                    if ($field_icon_group == '') {
                                                        $field_icon_group = 'default';
                                                    }
                                                    $field_icon_clr = isset($candidate_social_mlinks['icon_clr'][$field_counter]) ? $candidate_social_mlinks['icon_clr'][$field_counter] : '';
                                                    if ($field_icon_clr != '') {
                                                        $field_icon_styles .= 'color: ' . $field_icon_clr . ';';
                                                    }
                                                    $field_icon_bgclr = isset($candidate_social_mlinks['icon_bgclr'][$field_counter]) ? $candidate_social_mlinks['icon_bgclr'][$field_counter] : '';
                                                    if ($field_icon_bgclr != '') {
                                                        $field_icon_styles .= ' background-color: ' . $field_icon_bgclr . ';';
                                                    }
                                                    $cand_dynm_social = get_post_meta($candidate_id, 'jobsearch_field_dynm_social' . $field_counter, true);
                                                    if ($field_title_val != '' && $cand_dynm_social != '') {
                                                        ?>
                                                        <li><a href="<?php echo ($cand_dynm_social) ?>" <?php echo ($field_icon_styles != '' ? 'style="' . $field_icon_styles . '"' : '') ?> class="<?php echo ($field_icon) ?>"></a></li>
                                                        <?php
                                                    }
                                                    $field_counter++;
                                                }
                                            }
                                        }
                                        ?>
                                    </ul>
                                    <?php
                                    $cand_socilinks = ob_get_clean();
                                    echo apply_filters('jobsearch_cand_detail_socilinks_html', $cand_socilinks, $candidate_id);
                                }
                                do_action('jobsearch_download_candidate_cv_btn', array('id' => $candidate_id));
                                jobsearch_add_employer_resume_to_list_btn(array('id' => $candidate_id));

                                $info_html = ob_get_clean();
                                echo apply_filters('jobsearch_candetail_page_sideinfo_html', $info_html, $candidate_id);
                                ?>
                            </div>
                        </div>
                        <?php do_action('jobsearch_candidate_detail_side_after_info', array('id' => $candidate_id)); ?>
                        <?php
                        $cand_det_contact_form = isset($jobsearch_plugin_options['cand_det_contact_form']) ? $jobsearch_plugin_options['cand_det_contact_form'] : '';
                        if ($cand_det_contact_form == 'on') {
                            ob_start();
                            ?>
                            <div class="widget widget_contact_form">
                                <?php
                                $cnt_counter = rand(1000000, 9999999);
                                ?>
                                <div class="jobsearch-widget-title"><h2><?php esc_html_e('Contact Form', 'wp-jobsearch') ?></h2></div>
                                <form id="ct-form-<?php echo absint($cnt_counter) ?>" data-uid="<?php echo absint($user_id) ?>" method="post">
                                    <ul>
                                        <li>
                                            <label><?php esc_html_e('User Name:', 'wp-jobsearch') ?></label>
                                            <input name="u_name" placeholder="<?php esc_html_e('Enter Your Name', 'wp-jobsearch') ?>" type="text">
                                            <i class="jobsearch-icon jobsearch-user"></i>
                                        </li>
                                        <li>
                                            <label><?php esc_html_e('Email Address:', 'wp-jobsearch') ?></label>
                                            <input name="u_email" placeholder="<?php esc_html_e('Enter Your Email Address', 'wp-jobsearch') ?>" type="text">
                                            <i class="jobsearch-icon jobsearch-mail"></i>
                                        </li>
                                        <li>
                                            <label><?php esc_html_e('Phone Number:', 'wp-jobsearch') ?></label>
                                            <input name="u_number" placeholder="<?php esc_html_e('Enter Your Phone Number', 'wp-jobsearch') ?>" type="text">
                                            <i class="jobsearch-icon jobsearch-technology"></i>
                                        </li>
                                        <li>
                                            <label><?php esc_html_e('Message:', 'wp-jobsearch') ?></label>
                                            <textarea name="u_msg" placeholder="<?php esc_html_e('Type Your Message here', 'wp-jobsearch') ?>"></textarea>
                                        </li>
                                        <?php
                                        if ($captcha_switch == 'on') {
                                            wp_enqueue_script('jobsearch_google_recaptcha');
                                            ?>
                                            <li>
                                                <script>
                                                    var recaptcha_cand_contact;
                                                    var jobsearch_multicap = function () {
                                                        //Render the recaptcha_cand_contact on the element with ID "recaptcha1"
                                                        recaptcha_cand_contact = grecaptcha.render('recaptcha_cand_contact', {
                                                            'sitekey': '<?php echo ($jobsearch_sitekey); ?>', //Replace this with your Site key
                                                            'theme': 'light'
                                                        });
                                                    };
                                                    jQuery(document).ready(function () {
                                                        jQuery('.recaptcha-reload-a').click();
                                                    });
                                                </script>
                                                <div class="recaptcha-reload" id="recaptcha_cand_contact_div">
                                                    <?php echo jobsearch_recaptcha('recaptcha_cand_contact'); ?>
                                                </div>
                                            </li>
                                            <?php
                                        }
                                        ?>
                                        <li>
                                            <?php
                                            jobsearch_terms_and_con_link_txt();
                                            ?>
                                            <input type="submit" class="jobsearch-candidate-ct-form" data-id="<?php echo absint($cnt_counter) ?>" value="<?php esc_html_e('Send now', 'wp-jobsearch') ?>">
                                            <?php
                                            $cnt__cand_wout_log = isset($jobsearch_plugin_options['cand_cntct_wout_login']) ? $jobsearch_plugin_options['cand_cntct_wout_login'] : '';
                                            if (!is_user_logged_in() && $cnt__cand_wout_log != 'on') {
                                                ?>
                                                <a class="jobsearch-open-signin-tab" style="display: none;"><?php esc_html_e('login', 'wp-jobsearch') ?></a>
                                                <?php
                                            }
                                            ?>
                                        </li>
                                    </ul>
                                    <span class="jobsearch-ct-msg"></span>
                                </form>
                            </div>
                            <?php
                            $cand_cntct_form = ob_get_clean();
                            echo apply_filters('jobsearch_candidate_detail_cntct_frm_html', $cand_cntct_form, $candidate_id);
                        }
                        ?>
                    </aside>
                    <div class="jobsearch-column-8 jobsearch-typo-wrap">
                        <div class="container-wrapper">
                            <div class="jobsearch-candidate-editor">
                                <?php
                                $show_disp_name = apply_filters('jobsearch_candidate_detail_content_top_displayname', $user_displayname, $candidate_id);
                                ?>
                                <div class="jobsearch-content-title"><h2><?php printf(esc_html__('About %s', 'wp-jobsearch'), $show_disp_name) ?></h2></div>
                                <?php
                                echo apply_filters('jobsearch_canddetail_page_before_cusfields_html', '', $candidate_id);
                                $custom_all_fields = get_option('jobsearch_custom_field_candidate');
                                if (!empty($custom_all_fields)) {
                                    ?>
                                    <div class="jobsearch-jobdetail-services">
                                        <ul class="jobsearch-row">
                                            <?php
                                            $cus_fields = array('content' => '');
                                            $cus_fields = apply_filters('jobsearch_custom_fields_list', 'candidate', $candidate_id, $cus_fields, '<li class="jobsearch-column-4">', '</li>');
                                            if (isset($cus_fields['content']) && $cus_fields['content'] != '') {
                                                echo ($cus_fields['content']);
                                            }
                                            ?>
                                        </ul>
                                    </div>
                                    <?php
                                }

                                if ($candidate_content != '') {
                                    ob_start();
                                    ?>
                                    <div class="jobsearch-content-title"><h2><?php esc_html_e('Description', 'wp-jobsearch') ?></h2></div>
                                    <div class="jobsearch-description">
                                        <?php echo ($candidate_content) ?>
                                    </div>
                                    <?php
                                    $desc_html = ob_get_clean();
                                    echo apply_filters('jobsearch_cand_dash_detel_contnt_html', $desc_html, $candidate_id);
                                }

                                //
                                do_action('jobseach_candidate_detail_after_desctxt', $candidate_id);
                                ?>
                            </div>
                            <?php
                            $exfield_list = get_post_meta($candidate_id, 'jobsearch_field_education_title', true);
                            $exfield_list_val = get_post_meta($candidate_id, 'jobsearch_field_education_description', true);
                            $education_academyfield_list = get_post_meta($candidate_id, 'jobsearch_field_education_academy', true);
                            $education_yearfield_list = get_post_meta($candidate_id, 'jobsearch_field_education_year', true);

                            ob_start();
                            if (!empty($exfield_list)) {
                                ?>
                                <div class="jobsearch-candidate-title"> <h2><i class="jobsearch-icon jobsearch-mortarboard"></i> <?php esc_html_e('Education', 'wp-jobsearch') ?></h2> </div>
                                <div class="jobsearch-candidate-timeline">
                                    <ul class="jobsearch-row">
                                        <?php
                                        $exfield_counter = 0;
                                        foreach ($exfield_list as $exfield) {
                                            $exfield_val = isset($exfield_list_val[$exfield_counter]) ? $exfield_list_val[$exfield_counter] : '';
                                            $education_academyfield_val = isset($education_academyfield_list[$exfield_counter]) ? $education_academyfield_list[$exfield_counter] : '';
                                            $education_yearfield_val = isset($education_yearfield_list[$exfield_counter]) ? $education_yearfield_list[$exfield_counter] : '';
                                            ?>
                                            <li class="jobsearch-column-12">
                                                <small><?php echo ($education_yearfield_val) ?></small>
                                                <div class="jobsearch-candidate-timeline-text">
                                                    <span><?php echo ($education_academyfield_val) ?></span>
                                                    <?php
                                                    echo apply_filters('jobsearch_cand_det_resume_edu_list_aftr_inst', '', $candidate_id, $exfield_counter);
                                                    ?>
                                                    <h2><a><?php echo ($exfield) ?></a></h2>
                                                    <p><?php echo ($exfield_val) ?></p>
                                                </div>
                                            </li>
                                            <?php
                                            $exfield_counter ++;
                                        }
                                        ?>
                                    </ul>
                                </div>
                                <?php
                            }
                            $edu_html = ob_get_clean();
                            
                            if ($inopt_resm_education != 'off') {
                                echo apply_filters('jobsearch_candidate_detail_education_html', $edu_html, $candidate_id);
                            }
                            //
                            ob_start();
                            $exfield_list = get_post_meta($candidate_id, 'jobsearch_field_experience_title', true);
                            $exfield_list_val = get_post_meta($candidate_id, 'jobsearch_field_experience_description', true);
                            $experience_start_datefield_list = get_post_meta($candidate_id, 'jobsearch_field_experience_start_date', true);
                            $experience_end_datefield_list = get_post_meta($candidate_id, 'jobsearch_field_experience_end_date', true);
                            $experience_company_field_list = get_post_meta($candidate_id, 'jobsearch_field_experience_company', true);
                            if (is_array($exfield_list) && sizeof($exfield_list) > 0) {

                                $exfield_counter = 0;
                                ?>
                                <div class="jobsearch-candidate-title"> <h2><i class="jobsearch-icon jobsearch-social-media"></i> <?php esc_html_e('Experience', 'wp-jobsearch') ?></h2> </div>
                                <div class="jobsearch-candidate-timeline">
                                    <ul class="jobsearch-row">
                                        <?php
                                        foreach ($exfield_list as $exfield) {
                                            $exfield_val = isset($exfield_list_val[$exfield_counter]) ? $exfield_list_val[$exfield_counter] : '';
                                            $experience_start_datefield_val = isset($experience_start_datefield_list[$exfield_counter]) ? $experience_start_datefield_list[$exfield_counter] : '';
                                            $experience_end_datefield_val = isset($experience_end_datefield_list[$exfield_counter]) ? $experience_end_datefield_list[$exfield_counter] : '';
                                            $experience_end_companyfield_val = isset($experience_company_field_list[$exfield_counter]) ? $experience_company_field_list[$exfield_counter] : '';
                                            ?>
                                            <li class="jobsearch-column-12">
                                                <small><?php echo ($experience_start_datefield_val != '' ? date('Y', strtotime($experience_start_datefield_val)) : '') . ($experience_end_datefield_val != '' ? ' - ' . date('Y', strtotime($experience_end_datefield_val)) : '') ?></small>
                                                <div class="jobsearch-candidate-timeline-text">
                                                    <span><?php echo ($experience_end_companyfield_val) ?></span>
                                                    <?php
                                                    echo apply_filters('jobsearch_cand_det_resume_axpr_list_aftr_comp', '', $candidate_id, $exfield_counter);
                                                    ?>
                                                    <h2><a><?php echo ($exfield) ?></a></h2>
                                                    <p><?php echo ($exfield_val) ?></p>
                                                </div>
                                            </li>
                                            <?php
                                            $exfield_counter ++;
                                        }
                                        ?>
                                    </ul>
                                </div>
                                <?php
                            }
                            $expu_html = ob_get_clean();
                            if ($inopt_resm_experience != 'off') {
                                echo apply_filters('jobsearch_candidate_detail_exprience_html', $expu_html, $candidate_id);
                            }
                            
                            if ($inopt_resm_skills != 'off') {
                                $exfield_list = get_post_meta($candidate_id, 'jobsearch_field_skill_title', true);
                                $skill_percentagefield_list = get_post_meta($candidate_id, 'jobsearch_field_skill_percentage', true);
                                if (is_array($exfield_list) && sizeof($exfield_list) > 0) {
                                    ?>
                                    <div class="jobsearch_progressbar_wrap">
                                        <div class="jobsearch-row">
                                            <div class="jobsearch-column-12">
                                                <div class="jobsearch-candidate-title"> <h2><i class="jobsearch-icon jobsearch-design-skills"></i> <?php esc_html_e('Skills', 'wp-jobsearch') ?></h2> </div>
                                            </div>
                                            <?php
                                            $exfield_counter = 0;
                                            foreach ($exfield_list as $exfield) {
                                                $rand_num = rand(1000000, 99999999);

                                                $skill_percentagefield_val = isset($skill_percentagefield_list[$exfield_counter]) ? absint($skill_percentagefield_list[$exfield_counter]) : '';

                                                $skill_percentagefield_val = $skill_percentagefield_val > 100 ? 100 : $skill_percentagefield_val;
                                                ?>
                                                <div class="jobsearch-column-6">
                                                    <div class="jobsearch_progressbar1" data-width='<?php echo ($skill_percentagefield_val) ?>'><?php echo ($exfield) ?></div>
                                                </div>
                                                <?php
                                                $exfield_counter ++;
                                            }
                                            ?>
                                        </div>
                                    </div>

                                    <?php
                                }
                            }

                            //
                            ob_start();

                            $exfield_list = get_post_meta($candidate_id, 'jobsearch_field_portfolio_title', true);
                            $exfield_list_val = get_post_meta($candidate_id, 'jobsearch_field_portfolio_image', true);
                            $exfield_portfolio_url = get_post_meta($candidate_id, 'jobsearch_field_portfolio_url', true);
                            $exfield_portfolio_vurl = get_post_meta($candidate_id, 'jobsearch_field_portfolio_vurl', true);
                            if (is_array($exfield_list) && sizeof($exfield_list) > 0) {
                                ?>
                                <div class="jobsearch-candidate-title"> <h2><i class="jobsearch-icon jobsearch-briefcase"></i> <?php esc_html_e('Portfolio', 'wp-jobsearch') ?></h2> </div>
                                <div class="jobsearch-gallery candidate_portfolio">
                                    <ul class="jobsearch-row grid">
                                        <?php
                                        $exfield_counter = 0;
                                        foreach ($exfield_list as $exfield) {
                                            $rand_num = rand(1000000, 99999999);

                                            $portfolio_img = isset($exfield_list_val[$exfield_counter]) ? $exfield_list_val[$exfield_counter] : '';
                                            $portfolio_url = isset($exfield_portfolio_url[$exfield_counter]) ? $exfield_portfolio_url[$exfield_counter] : '';
                                            $portfolio_vurl = isset($exfield_portfolio_vurl[$exfield_counter]) ? $exfield_portfolio_vurl[$exfield_counter] : '';

                                            if ($portfolio_vurl != '') {
                                                if (strpos($portfolio_vurl, 'watch?v=') !== false) {
                                                    $portfolio_vurl = str_replace('watch?v=', 'embed/', $portfolio_vurl);
                                                }

                                                if (strpos($portfolio_vurl, '?') !== false) {
                                                    $portfolio_vurl .= '&autoplay=1';
                                                } else {
                                                    $portfolio_vurl .= '?autoplay=1';
                                                }
                                            }

                                            $port_thumb_img = $portfolio_img;
                                            if ($portfolio_img != '') {
                                                $attach_id = jobsearch_get_attachment_id_from_url($portfolio_img);
                                                $port_thumb_image = wp_get_attachment_image_src($attach_id, 'large');
                                                $port_thumb_img = isset($port_thumb_image[0]) && esc_url($port_thumb_image[0]) != '' ? $port_thumb_image[0] : $portfolio_img;
                                            }
                                            ?>
                                            <li class="grid-item <?php echo ($exfield_counter == 1 ? 'jobsearch-column-6' : 'jobsearch-column-3') ?>">
                                                <figure>
                                                    <span class="grid-item-thumb"><small style="background-image: url('<?php echo ($port_thumb_img) ?>');"></small></span>
                                                    <figcaption>
                                                        <div class="img-icons">
                                                            <a href="<?php echo ($portfolio_vurl != '' ? $portfolio_vurl : $portfolio_img) ?>" class="<?php echo ($portfolio_vurl != '' ? 'fancybox-video' : 'fancybox') ?>" title="<?php echo ($exfield) ?>" <?php echo ($portfolio_vurl != '' ? 'data-fancybox-type="iframe"' : '') ?> data-fancybox-group="group"><i class="<?php echo ($portfolio_vurl != '' ? 'fa fa-play' : 'fa fa-image') ?>"></i></a>
                                                            <?php
                                                            if ($portfolio_url != '') {
                                                                ?>
                                                                <a href="<?php echo ($portfolio_url) ?>" target="_blank"><i class="fa fa-chain"></i></a>
                                                                <?php
                                                            }
                                                            ?>
                                                        </div>
                                                    </figcaption>
                                                </figure>
                                            </li>
                                            <?php
                                            $exfield_counter ++;
                                        }
                                        ?>
                                    </ul>
                                </div>

                                <?php
                            }

                            $ports_html = ob_get_clean();
                            
                            if ($inopt_resm_portfolio != 'off') {
                                echo apply_filters('jobsearch_candidate_detail_portsfolio_html', $ports_html, $candidate_id);
                            }
                            
                            if ($inopt_resm_honsawards != 'off') {
                                $exfield_list = get_post_meta($candidate_id, 'jobsearch_field_award_title', true);
                                $exfield_list_val = get_post_meta($candidate_id, 'jobsearch_field_award_description', true);
                                $award_yearfield_list = get_post_meta($candidate_id, 'jobsearch_field_award_year', true);
                                if (is_array($exfield_list) && sizeof($exfield_list) > 0) {
                                    ?>
                                    <div class="jobsearch-candidate-title"> <h2><i class="jobsearch-icon jobsearch-trophy"></i> <?php esc_html_e('Honors & awards', 'wp-jobsearch') ?></h2> </div>
                                    <div class="jobsearch-candidate-timeline">
                                        <ul class="jobsearch-row">
                                            <?php
                                            $exfield_counter = 0;
                                            foreach ($exfield_list as $exfield) {
                                                $rand_num = rand(1000000, 99999999);

                                                $exfield_val = isset($exfield_list_val[$exfield_counter]) ? $exfield_list_val[$exfield_counter] : '';
                                                $award_yearfield_val = isset($award_yearfield_list[$exfield_counter]) ? $award_yearfield_list[$exfield_counter] : '';
                                                ?>
                                                <li class="jobsearch-column-12">
                                                    <small><?php echo ($award_yearfield_val) ?></small>
                                                    <div class="jobsearch-candidate-timeline-text">
                                                        <h2><a><?php echo ($exfield) ?></a></h2>
                                                        <p><?php echo ($exfield_val) ?></p>
                                                    </div>
                                                </li>
                                                <?php
                                                $exfield_counter ++;
                                            }
                                            ?>
                                        </ul>
                                    </div>
                                    <?php
                                }
                            }
                            
                            if ($candidates_reviews == 'on') {
                                $post_reviews_args = array(
                                    'post_id' => $candidate_id,
                                    'list_label' => esc_html__('Candidate Reviews', 'wp-jobsearch'),
                                );
                                do_action('jobsearch_post_reviews_list', $post_reviews_args);

                                $review_form_args = array(
                                    'post_id' => $candidate_id,
                                    'must_login' => 'no',
                                );
                                do_action('jobsearch_add_review_form', $review_form_args);
                            }


                            ?>
                        </div>
                    </div>
                    <?php
                }
                ?>
            </div>
        </div>
    </div>
    <!-- Main Section -->

</div>



