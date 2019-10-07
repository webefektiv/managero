<?php
global $jobsearch_plugin_options, $jobsearch_currencies_list, $diff_form_errs, $sitepress;

$lang_code = '';
if (function_exists('icl_object_id') && function_exists('wpml_init_language_switcher')) {
    $lang_code = $sitepress->get_current_language();
}

$get_tab = isset($_REQUEST['tab']) ? $_REQUEST['tab'] : '';
$page_id = $user_dashboard_page = isset($jobsearch_plugin_options['user-dashboard-template-page']) ? $jobsearch_plugin_options['user-dashboard-template-page'] : '';
$page_id = $user_dashboard_page = jobsearch__get_post_id($user_dashboard_page, 'page');
$page_url = jobsearch_wpml_lang_page_permalink($page_id, 'page'); //get_permalink($page_id);

$_salary_types = isset($jobsearch_plugin_options['job-salary-types']) ? $jobsearch_plugin_options['job-salary-types'] : '';

$current_user = wp_get_current_user();
$user_id = get_current_user_id();
$user_obj = get_user_by('ID', $user_id);

$candidate_id = jobsearch_get_user_candidate_id($user_id);

$user_displayname = $user_obj->display_name;
$user_displayname = apply_filters('jobsearch_user_display_name', $user_displayname, $user_obj);
$user_bio = $user_obj->description;
$user_website = $user_obj->user_url;
$user_email = $user_obj->user_email;

//
$user_dob_dd = get_post_meta($candidate_id, 'jobsearch_field_user_dob_dd', true);
$user_dob_mm = get_post_meta($candidate_id, 'jobsearch_field_user_dob_mm', true);
$user_dob_yy = get_post_meta($candidate_id, 'jobsearch_field_user_dob_yy', true);

$user_phone = get_post_meta($candidate_id, 'jobsearch_field_user_phone', true);
//

$can_post_obj = get_post($candidate_id);
$candidate_content = isset($can_post_obj->post_content) ? $can_post_obj->post_content : '';
$candidate_content = apply_filters('the_content', $candidate_content);

$user_profile_url = isset($can_post_obj->post_name) ? $can_post_obj->post_name : '';

//
$sectors = wp_get_post_terms($candidate_id, 'sector');
$candidate_sector = isset($sectors[0]->term_id) ? $sectors[0]->term_id : '';

$_candidate_salary_type = get_post_meta($candidate_id, 'jobsearch_field_candidate_salary_type', true);
$_candidate_salary = get_post_meta($candidate_id, 'jobsearch_field_candidate_salary', true);

$user_facebook_url = get_post_meta($candidate_id, 'jobsearch_field_user_facebook_url', true);
$user_twitter_url = get_post_meta($candidate_id, 'jobsearch_field_user_twitter_url', true);
$user_google_plus_url = get_post_meta($candidate_id, 'jobsearch_field_user_google_plus_url', true);
$user_youtube_url = get_post_meta($candidate_id, 'jobsearch_field_user_youtube_url', true);
$user_dribbble_url = get_post_meta($candidate_id, 'jobsearch_field_user_dribbble_url', true);
$user_linkedin_url = get_post_meta($candidate_id, 'jobsearch_field_user_linkedin_url', true);

//
$_candidate_salary_currency = get_post_meta($candidate_id, 'jobsearch_field_candidate_salary_currency', true);
$_candidate_salary_pos = get_post_meta($candidate_id, 'jobsearch_field_candidate_salary_pos', true);
$_candidate_salary_deci = get_post_meta($candidate_id, 'jobsearch_field_candidate_salary_deci', true);
$_candidate_salary_sep = get_post_meta($candidate_id, 'jobsearch_field_candidate_salary_sep', true);

//
$job_title = get_post_meta($candidate_id, 'jobsearch_field_candidate_jobtitle', true);

$user_def_avatar_url = get_avatar_url($user_id, array('size' => 128));

$user_avatar_id = get_user_meta($user_id, 'jobsearch_user_avatar_id', true);

if ($user_avatar_id > 0) {
    $user_thumbnail_image = wp_get_attachment_image_src($user_avatar_id, 'thumbnail');
    $user_def_avatar_url = isset($user_thumbnail_image[0]) && esc_url($user_thumbnail_image[0]) != '' ? $user_thumbnail_image[0] : '';
}
?>

<div class="jobsearch-typo-wrap">
    
</div>



<div class="jobsearch-typo-wrap">
    <form id="candidate-profilesetings-form" class="jobsearch-employer-dasboard" method="post" action="<?php echo add_query_arg(array('tab' => 'dashboard-settings'), $page_url) ?>" enctype="multipart/form-data">
        <div class="jobsearch-employer-box-section">
            <?php
            ob_start();
            ?>
            <div class="jobsearch-profile-title"><h2><?php esc_html_e('Basic Information', 'wp-jobsearch') ?></h2></div>
            <?php
            $title_html = ob_get_clean();
            echo apply_filters('jobsearch_cand_dash_profile_maintitle_html', $title_html);
            if (isset($_POST['user_settings_form']) && $_POST['user_settings_form'] == '1') {
                if (empty($diff_form_errs)) {
                    ?>
                    <div class="jobsearch-alert jobsearch-success-alert">
                        <p><?php echo wp_kses(__('<strong>Success!</strong> All changes updated.', 'wp-jobsearch'), array('strong' => array())) ?></p>
                    </div>
                    <?php
                } else if (isset($diff_form_errs['user_not_allow_mod']) && $diff_form_errs['user_not_allow_mod'] == true) {
                    ?>
                    <div class="jobsearch-alert jobsearch-error-alert">
                        <p><?php echo wp_kses(__('<strong>Error!</strong> You are not allowed to modify settings.', 'wp-jobsearch'), array('strong' => array())) ?></p>
                    </div>
                    <?php
                }
            }
            $sdate_format = jobsearch_get_wp_date_simple_format();

            //
            $user_cover_img_url = '';
            if ($candidate_id != '') {
                if (class_exists('JobSearchMultiPostThumbnails')) {
                    $employer_cover_image_src = JobSearchMultiPostThumbnails::get_post_thumbnail_url('candidate', 'cover-image', $candidate_id);
                    if ($employer_cover_image_src != '') {
                        $user_cover_img_url = $employer_cover_image_src;
                    }
                }
            }
            $candidate_cover_image_src_style_str = ' style="background:url(' . esc_url($user_cover_img_url) . ') no-repeat center/cover;"';

            ob_start();
            ?>
            <div class="jobsearch-employer-cvr-img">

                <figure>
                    <div class="img-cont-sec" style="display: <?php echo ($user_cover_img_url == '' ? 'none' : 'block') ?>;">
                        <a href="javascript:void(0);" class="candidate-remove-coverimg"><i class="fa fa-times"></i> <?php esc_html_e('Delete Cover', 'wp-jobsearch') ?></a>    
                        <a id="com-cvrimg-holder" class="employer-dashboard-cvr">
                            <span<?php echo ($candidate_cover_image_src_style_str) ?>></span>
                        </a>

                    </div>
                    <figcaption>
                        <span class="file-loader"></span>
                        <div class="jobsearch-fileUpload">
                            <span><i class="jobsearch-icon jobsearch-add"></i> <?php esc_html_e('Upload Cover Photo', 'wp-jobsearch') ?></span>
                            <input type="file" id="user_cvr_photo_cand" name="user_cvr_photo_cand" class="jobsearch-upload">
                        </div>
                    </figcaption>
                </figure>
            </div>
            <?php
            $cphot_html = ob_get_clean();
            echo apply_filters('jobsearch_cand_dash_profile_coverimg_html', $cphot_html, $user_cover_img_url, $candidate_id);
            ?>

            <ul class="jobsearch-row jobsearch-employer-profile-form">
                <?php
                ob_start();
                ?>
                <li class="jobsearch-column-6" style="display: none">
                    <label><?php esc_html_e('Your Name *', 'wp-jobsearch') ?></label>
                    <input type="text" name="display_name" value="<?php echo ($user_displayname) ?>">
                </li>
                <li class="jobsearch-column-6">
                    <label><?php esc_html_e('Email', 'wp-jobsearch') ?></label>
                    <input value="<?php echo ($user_email) ?>" type="text" readonly="readonly">
                </li>
                <?php
                $simp_field_html = ob_get_clean();
                echo apply_filters('jobsearch_cand_dash_simpfields_html', $simp_field_html, $candidate_id);
                
                //
                $candidate_site_slug = isset($jobsearch_plugin_options['candidate_rewrite_slug']) && $jobsearch_plugin_options['candidate_rewrite_slug'] != '' ? $jobsearch_plugin_options['candidate_rewrite_slug'] : 'candidate';
                ?>
                <li class="jobsearch-column-12" style="display:none;">
                    <label><?php esc_html_e('Profile URL', 'wp-jobsearch') ?></label>
                    <div class="jobsearch-userprofile-url">
                        <span><?php echo home_url('/' . $candidate_site_slug . '/') ?></span><strong><?php echo urldecode($user_profile_url) ?></strong>
                        <input type="text" class="profile-slug-field" style="display: none;" name="user_profile_slug" value="<?php echo urldecode($user_profile_url) ?>">
                        <a href="javascript:void(0);" class="updte-profile-slugbtn"><?php esc_html_e('Edit', 'wp-jobsearch') ?></a>
                        <a href="javascript:void(0);" class="ok-profile-slugbtn" style="display: none;"><?php esc_html_e('Ok', 'wp-jobsearch') ?></a>
                        <span class="slugchng-loder"></span>
                    </div>
                </li>
                <?php
                $cand_dob_switch = isset($jobsearch_plugin_options['cand_dob_switch']) ? $jobsearch_plugin_options['cand_dob_switch'] : 'on';
                ob_start();
                if ($cand_dob_switch == 'on') {
                    ?>
                    <li class="jobsearch-column-6">
                        <label><?php esc_html_e('Date of Birth:', 'wp-jobsearch') ?></label>
                        <div class="jobsearch-three-column-row">
                            <?php
                            ob_start();
                            ?>
                            <div class="jobsearch-profile-select jobsearch-three-column" style="display: none">
                                <select name="user_dob_dd" class="selectize-select" placeholder="<?php esc_html_e('Day', 'wp-jobsearch') ?>">
                                    <?php
                                    for ($dd = 1; $dd <= 31; $dd++) {
                                        $db_val = $user_dob_dd != '' ? $user_dob_dd : date('d');
                                        ?>
                                        <option <?php echo ($db_val == $dd ? 'selected="selected"' : '') ?> value="<?php echo ($dd) ?>"><?php echo ($dd) ?></option>
                                        <?php
                                    }
                                    ?>
                                </select>
                            </div>
                            <?php
                            $dob_dd_html = ob_get_clean();
                            ob_start();
                            ?>
                            <div class="jobsearch-profile-select jobsearch-three-column" style="display: none">
                                <select name="user_dob_mm" class="selectize-select" placeholder="<?php esc_html_e('Month', 'wp-jobsearch') ?>">
                                    <?php
                                    for ($mm = 1; $mm <= 12; $mm++) {
                                        $db_val = $user_dob_mm != '' ? $user_dob_mm : date('m');
                                        ?>
                                        <option <?php echo ($db_val == $mm ? 'selected="selected"' : '') ?> value="<?php echo ($mm) ?>"><?php echo ($mm) ?></option>
                                        <?php
                                    }
                                    ?>
                                </select>
                            </div>
                            <?php
                            $dob_mm_html = ob_get_clean();
                            ob_start();
                            ?>
                            <div class="jobsearch-profile-select jobsearch-three-column">
                                <select name="user_dob_yy" class="selectize-select" placeholder="<?php esc_html_e('Year', 'wp-jobsearch') ?>">
                                    <?php
                                    for ($yy = 1900; $yy <= date('Y'); $yy++) {
                                        $db_val = $user_dob_yy != '' ? $user_dob_yy : date('Y');
                                        ?>
                                        <option <?php echo ($db_val == $yy ? 'selected="selected"' : '') ?> value="<?php echo ($yy) ?>"><?php echo ($yy) ?></option>
                                        <?php
                                    }
                                    ?>
                                </select>
                            </div>
                            <?php
                            $dob_yy_html = ob_get_clean();
                            //
                            if ($sdate_format == 'm-d-y') {
                                echo ($dob_mm_html);
                                echo ($dob_dd_html);
                                echo ($dob_yy_html);
                            } else if ($sdate_format == 'y-m-d') {
                                echo ($dob_yy_html);
                                echo ($dob_mm_html);
                                echo ($dob_dd_html);
                            } else {
                                echo ($dob_dd_html);
                                echo ($dob_mm_html);
                                echo ($dob_yy_html);
                            }
                            ?>
                        </div>
                    </li>
                    <?php
                    $dob_html = ob_get_clean();
                    echo apply_filters('jobsearch_candidate_dash_dob_html', $dob_html, $candidate_id);
                } // end dob switch

                ob_start();
                ?>
                <li class="jobsearch-column-6" style="none">
                    <label><?php esc_html_e('Phone', 'wp-jobsearch') ?></label>
                    <input value="<?php echo ($user_phone) ?>" onkeyup="javascript:jobsearch_is_valid_phone_number(this)" type="text" name="user_phone">
                </li>
                <?php
                $phonef_html = ob_get_clean();
                echo apply_filters('jobsearch_cand_dash_profields_phone_html', $phonef_html, $candidate_id);

                //
                ob_start();
                $sectors_enable_switch = isset($jobsearch_plugin_options['sectors_onoff_switch']) ? $jobsearch_plugin_options['sectors_onoff_switch'] : '';
                if ($sectors_enable_switch == 'on') {
                    ?>
                    <li class="jobsearch-column-6">
                        <label><?php esc_html_e('Sector', 'wp-jobsearch') ?></label>
                        <div class="jobsearch-profile-select">
                            <?php
                            $sector_args = array(
                                'show_option_all' => esc_html__('Select Sector', 'wp-jobsearch'),
                                'show_option_none' => '',
                                'option_none_value' => '',
                                'orderby' => 'title',
                                'order' => 'ASC',
                                'show_count' => 0,
                                'hide_empty' => 0,
                                'echo' => 0,
                                'selected' => $candidate_sector,
                                'hierarchical' => 1,
                                'id' => 'user-sector',
                                'class' => 'postform selectize-select',
                                'name' => 'user_sector',
                                'depth' => 0,
                                'taxonomy' => 'sector',
                                'hide_if_empty' => false,
                                'value_field' => 'term_id',
                            );
                            $sector_sel_html = wp_dropdown_categories($sector_args);
                            echo apply_filters('jobsearch_cand_profile_sector_select', $sector_sel_html, $candidate_id);
                            ?>
                        </div>
                    </li>
                    <?php
                }
                $sects_html = ob_get_clean();
                echo apply_filters('jobsearch_cand_dash_profields_sector_html', $sects_html, $candidate_id);

                //
                ob_start();
                ?>
                <li class="jobsearch-column-6">
                    <label><?php esc_html_e('Job Title', 'wp-jobsearch') ?></label>
                    <input value="<?php echo ($job_title) ?>" type="text" name="jobsearch_field_candidate_jobtitle">
                </li>
                <?php
                $jobtitlef_html = ob_get_clean();
                echo apply_filters('jobsearch_cand_dash_profields_jobtitle_html', $jobtitlef_html, $candidate_id);

                //
                ob_start();
                $salary_onoff_switch = isset($jobsearch_plugin_options['cand_salary_switch']) ? $jobsearch_plugin_options['cand_salary_switch'] : 'on';
                if ($salary_onoff_switch == 'on') {
                    ?>
                    <li class="jobsearch-column-6">
                        <label><?php esc_html_e('Salary', 'wp-jobsearch') ?></label>
                        <?php
                        if (!empty($_salary_types)) {
                            ?>
                            <div class="salary-type">
                                <div class="jobsearch-profile-select">
                                    <select name="candidate_salary_type" class="selectize-select">
                                        <?php
                                        $slar_type_count = 1;
                                        foreach ($_salary_types as $_salary_type) {
                                            $_salary_type = apply_filters('wpml_translate_single_string', $_salary_type, 'JobSearch Options', 'Salary Type - ' . $_salary_type, $lang_code);
                                            ?>
                                            <option value="type_<?php echo ($slar_type_count) ?>" <?php echo ($_candidate_salary_type == 'type_' . $slar_type_count ? 'selected="selected"' : '') ?>><?php echo ($_salary_type) ?></option>
                                            <?php
                                            $slar_type_count++;
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <?php
                            echo '<div class="salary-input">';
                        } else {
                            echo '<div class="salary-input salary-input-full">';
                        }
                        $_job_currency_sym = isset($jobsearch_currencies_list[$_candidate_salary_currency]['symbol']) ? $jobsearch_currencies_list[$_candidate_salary_currency]['symbol'] : jobsearch_get_currency_symbol();
                        ?>
                        <span><?php echo ($_job_currency_sym) ?></span>
                        <input type="text" placeholder="<?php esc_html_e('Salary', 'wp-jobsearch') ?>" name="candidate_salary" <?php echo ('value="' . ($_candidate_salary) . '"') ?>>
                        <?php
                        echo '</div>';
                        ?>
                    </li>
                    <?php
                    $job_custom_currency_switch = isset($jobsearch_plugin_options['job_custom_currency']) ? $jobsearch_plugin_options['job_custom_currency'] : '';
                    if (!empty($jobsearch_currencies_list) && $job_custom_currency_switch == 'on') {
                        ?>
                        <li class="jobsearch-column-12">
                            <div class="jobsearch-row">
                                <div class="jobsearch-column-3">
                                    <label><?php esc_html_e('Salary Currency', 'wp-jobsearch') ?></label>
                                    <div class="jobsearch-profile-select">
                                        <select name="candidate_salary_currency" class="selectize-select">
                                            <option value="default" data-cur="<?php echo jobsearch_get_currency_symbol() ?>"><?php esc_html_e('Default', 'wp-jobsearch') ?></option>
                                            <?php
                                            foreach ($jobsearch_currencies_list as $cus_currency_key => $cus_currency) {
                                                $cus_cur_name = isset($cus_currency['name']) ? $cus_currency['name'] : '';
                                                $cus_cur_symbol = isset($cus_currency['symbol']) ? $cus_currency['symbol'] : '';
                                                ?>
                                                <option value="<?php echo ($cus_currency_key) ?>" data-cur="<?php echo ($cus_cur_symbol) ?>" <?php echo ($_candidate_salary_currency == $cus_currency_key ? 'selected="selected"' : '') ?>><?php echo ($cus_cur_name . ' - ' . $cus_cur_symbol) ?></option>
                                                <?php
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="jobsearch-column-3">
                                    <label><?php esc_html_e('Currency position', 'wp-jobsearch') ?></label>
                                    <div class="jobsearch-profile-select">
                                        <select name="candidate_salary_pos" class="selectize-select">
                                            <option value="left" <?php echo ($_candidate_salary_pos == 'left' ? 'selected="selected"' : '') ?>><?php esc_html_e('Left', 'wp-jobsearch') ?></option>
                                            <option value="right" <?php echo ($_candidate_salary_pos == 'right' ? 'selected="selected"' : '') ?>><?php esc_html_e('Right', 'wp-jobsearch') ?></option>
                                            <option value="left_space" <?php echo ($_candidate_salary_pos == 'left_space' ? 'selected="selected"' : '') ?>><?php esc_html_e('Left with space', 'wp-jobsearch') ?></option>
                                            <option value="right_space" <?php echo ($_candidate_salary_pos == 'right_space' ? 'selected="selected"' : '') ?>><?php esc_html_e('Right with space', 'wp-jobsearch') ?></option>
                                        </select>
                                    </div>
                                </div>
                                <div class="jobsearch-column-3">
                                    <label><?php esc_html_e('Thousand separator', 'wp-jobsearch') ?></label>
                                    <input type="text" name="candidate_salary_sep" value="<?php echo ($_candidate_salary_sep != '' ? $_candidate_salary_sep : ',') ?>">
                                </div>
                                <div class="jobsearch-column-3">
                                    <label><?php esc_html_e('Number of decimals', 'wp-jobsearch') ?></label>
                                    <input type="text" name="candidate_salary_deci" value="<?php echo ($_candidate_salary_deci != '' && $_candidate_salary_deci > 0 ? $_candidate_salary_deci : '2') ?>">
                                </div>
                            </div>
                        </li>
                        <?php
                    }
                }
                $salry_html = ob_get_clean();
                echo apply_filters('jobsearch_cand_dash_profields_salary_html', $salry_html, $candidate_id);

                ob_start();
                ?>
                <li class="jobsearch-column-12">
                    <label><?php esc_html_e('Description', 'wp-jobsearch') ?></label>
                    <?php
                    $cand_desc_with_media = isset($jobsearch_plugin_options['cand_desc_with_media']) ? $jobsearch_plugin_options['cand_desc_with_media'] : '';
                    $settings = array(
                        'media_buttons' => ($cand_desc_with_media == 'on' ? true : false),
                        'quicktags' => array('buttons' => 'strong,em,del,ul,ol,li,close'),
                        'tinymce' => array(
                            'toolbar1' => 'bold,bullist,numlist,italic,underline,alignleft,aligncenter,alignright,separator,link,unlink,undo,redo',
                            'toolbar2' => '',
                            'toolbar3' => '',
                        ),
                    );

                    wp_editor($candidate_content, 'user_bio', $settings);
                    ?>
                </li>
                <?php
                $desc_html = ob_get_clean();
                echo apply_filters('jobsearch_cand_dash_desc_contnt_html', $desc_html, $candidate_id);

                //
                echo apply_filters('jobsearch_cand_dashbord_after_desc_content', '', $candidate_id);
                ?>
            </ul>
        </div>
        <?php echo apply_filters('jobsearch_cand_dash_profile_after_basicinfo', '', $candidate_id) ?>
        <?php
        do_action('jobsearch_dashboard_custom_fields_load', $candidate_id, 'candidate');

        //
        ob_start();

        $cand_alow_fb_smm = isset($jobsearch_plugin_options['cand_alow_fb_smm']) ? $jobsearch_plugin_options['cand_alow_fb_smm'] : '';
        $cand_alow_twt_smm = isset($jobsearch_plugin_options['cand_alow_twt_smm']) ? $jobsearch_plugin_options['cand_alow_twt_smm'] : '';
        $cand_alow_gplus_smm = isset($jobsearch_plugin_options['cand_alow_gplus_smm ']) ? $jobsearch_plugin_options['cand_alow_gplus_smm '] : '';
        $cand_alow_linkd_smm = isset($jobsearch_plugin_options['cand_alow_linkd_smm']) ? $jobsearch_plugin_options['cand_alow_linkd_smm'] : '';
        $cand_alow_dribbb_smm = isset($jobsearch_plugin_options['cand_alow_dribbb_smm']) ? $jobsearch_plugin_options['cand_alow_dribbb_smm'] : '';
        $candidate_social_mlinks = isset($jobsearch_plugin_options['candidate_social_mlinks']) ? $jobsearch_plugin_options['candidate_social_mlinks'] : '';

        if (!empty($candidate_social_mlinks) || ($cand_alow_fb_smm == 'on' || $cand_alow_twt_smm == 'on' || $cand_alow_gplus_smm == 'on' || $cand_alow_linkd_smm == 'on' || $cand_alow_dribbb_smm == 'on')) {
            ?>
            <div class="jobsearch-employer-box-section">
                <div class="jobsearch-profile-title"><h2><?php esc_html_e('Social Links', 'wp-jobsearch') ?></h2></div>
                <ul class="jobsearch-row jobsearch-employer-profile-form">
                    <?php
                    if ($cand_alow_fb_smm == 'on') {
                        ?>
                        <li class="jobsearch-column-6">
                            <label><?php esc_html_e('Facebook', 'wp-jobsearch') ?></label>
                            <input value="<?php echo ($user_facebook_url) ?>" name="jobsearch_field_user_facebook_url" type="text">
                        </li>
                        <?php
                    }
                    if ($cand_alow_twt_smm == 'on') {
                        ?>
                        <li class="jobsearch-column-6">
                            <label><?php esc_html_e('Twitter', 'wp-jobsearch') ?></label>
                            <input value="<?php echo ($user_twitter_url) ?>" name="jobsearch_field_user_twitter_url" type="text">
                        </li>
                        <?php
                    }
                    if ($cand_alow_gplus_smm == 'on') {
                        ?>
                        <li class="jobsearch-column-6">
                            <label><?php esc_html_e('Google Plus', 'wp-jobsearch') ?></label>
                            <input value="<?php echo ($user_google_plus_url) ?>" name="jobsearch_field_user_google_plus_url" type="text">
                        </li>
                        <?php
                    }
                    if ($cand_alow_linkd_smm == 'on') {
                        ?>
                        <li class="jobsearch-column-6">
                            <label><?php esc_html_e('Linkedin', 'wp-jobsearch') ?></label>
                            <input value="<?php echo ($user_linkedin_url) ?>" name="jobsearch_field_user_linkedin_url" type="text">
                        </li>
                        <?php
                    }
                    if ($cand_alow_dribbb_smm == 'on') {
                        ?>
                        <li class="jobsearch-column-6">
                            <label><?php esc_html_e('Dribbble', 'wp-jobsearch') ?></label>
                            <input value="<?php echo ($user_dribbble_url) ?>" name="jobsearch_field_user_dribbble_url" type="text">
                        </li>
                        <?php
                    }
                    if (!empty($candidate_social_mlinks)) {
                        if (isset($candidate_social_mlinks['title']) && is_array($candidate_social_mlinks['title'])) {
                            $field_counter = 0;
                            foreach ($candidate_social_mlinks['title'] as $field_title_val) {
                                $field_random = rand(10000000, 99999999);
                                $field_icon = isset($candidate_social_mlinks['icon'][$field_counter]) ? $candidate_social_mlinks['icon'][$field_counter] : '';
                                $field_icon_group = isset($candidate_social_mlinks['icon_group'][$field_counter]) ? $candidate_social_mlinks['icon_group'][$field_counter] : '';
                                if ($field_icon_group == '') {
                                    $field_icon_group = 'default';
                                }
                                if ($field_title_val != '') {
                                    $cand_dynm_social = get_post_meta($candidate_id, 'jobsearch_field_dynm_social' . $field_counter, true);
                                    ?>
                                    <li class="jobsearch-column-6">
                                        <label><?php echo ($field_title_val) ?></label>
                                        <input value="<?php echo ($cand_dynm_social) ?>" name="candidate_dynm_social<?php echo ($field_counter) ?>" type="text">
                                    </li>
                                    <?php
                                }
                                $field_counter++;
                            }
                        }
                    }
                    ?>
                </ul>
            </div>
            <?php
        }
        $socilinks_html = ob_get_clean();
        echo apply_filters('jobsearch_canddash_profilesett_socilinks', $socilinks_html, $candidate_id);

        //
        do_action('jobsearch_dashboard_location_map', $candidate_id);

        ob_start();
        jobsearch_terms_and_con_link_txt();
        $sscon_html = ob_get_clean();
        echo apply_filters('jobsearch_canddash_profilesett_update_termscon', $sscon_html);
        ?>

        <input type="hidden" name="user_settings_form" value="1">
        <?php
        ob_start();
        ?>
        <input type="submit" class="jobsearch-employer-profile-submit" value="<?php esc_html_e('Save Settings', 'wp-jobsearch') ?>">
        <?php
        $btns_html = ob_get_clean();
        echo apply_filters('jobsearch_canddash_profilesett_update_mainbtn', $btns_html);

        ob_start();
        do_action('jobsearch_translate_profile_with_wpml_btn', $candidate_id, 'candidate', 'dashboard-settings');
        $btns_html = ob_get_clean();
        echo apply_filters('jobsearch_translate_cprofile_with_wpml_btn_html', $btns_html);
        //
        ?>
    </form>
</div>
