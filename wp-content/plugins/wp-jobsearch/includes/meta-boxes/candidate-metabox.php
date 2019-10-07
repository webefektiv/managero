<?php
/**
 * Define Meta boxes for plugin
 * and theme.
 *
 */
add_action('save_post', 'jobsearch_candidates_time_save');

function jobsearch_candidates_time_save($post_id) {
    global $pagenow;
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    $post_type = '';
    if ($pagenow == 'post.php') {
        $post_type = get_post_type();
    }
    if (isset($_POST)) {
        if ($post_type == 'candidate') {
            // extra save
            if (isset($_POST['jobsearch_field_user_cv_attachment']) && $_POST['jobsearch_field_user_cv_attachment'] != '') {
                $cv_file_url = $_POST['jobsearch_field_user_cv_attachment'];
                $cv_file_id = jobsearch_get_attachment_id_from_url($cv_file_url);
                if ($cv_file_id) {
                    $arg_arr = array(
                        'file_id' => $cv_file_id,
                        'file_url' => $cv_file_url,
                    );
                    update_post_meta($post_id, 'candidate_cv_file', $arg_arr);
                }
            } else {
                update_post_meta($post_id, 'candidate_cv_file', '');
            }

            // Cus Fields Upload Files /////
            do_action('jobsearch_custom_field_upload_files_save', $post_id, 'candidate');
            //

            do_action('jobsearch_cand_bk_meta_fields_save_after', $post_id);

            // urgent cand from bckend
            if (isset($_POST['cuscand_urgent_fbckend'])) {
                $cuscand_urgent_fbckend = $_POST['cuscand_urgent_fbckend'];
                if ($cuscand_urgent_fbckend == 'on') {
                    update_post_meta($post_id, '_urgent_cand_frmadmin', 'yes');
                } else if ($cuscand_urgent_fbckend == 'off') {
                    update_post_meta($post_id, '_urgent_cand_frmadmin', 'no');
                }
            }
            
            // feature cand from bckend
            if (isset($_POST['cuscand_feature_fbckend'])) {
                $promote_pckg_subtime = get_post_meta($post_id, 'promote_profile_substime', true);
                //
                $cuscand_feature_fbckend = $_POST['cuscand_feature_fbckend'];
                if ($cuscand_feature_fbckend == 'on') {
                    update_post_meta($post_id, '_feature_mber_frmadmin', 'yes');
                    if ($promote_pckg_subtime <= 0) {
                        update_post_meta($post_id, 'promote_profile_substime', current_time('timestamp'));
                    }
                } else if ($cuscand_feature_fbckend == 'off') {
                    update_post_meta($post_id, '_feature_mber_frmadmin', 'no');
                }
            }
        }
    }
}

if (class_exists('JobSearchMultiPostThumbnails')) {
    new JobSearchMultiPostThumbnails(array(
        'label' => 'Cover Image',
        'id' => 'cover-image',
        'post_type' => 'candidate',
            )
    );
}

/**
 * Candidate settings meta box.
 */
function jobsearch_candidates_settings_meta_boxes() {
    add_meta_box('jobsearch-candidates-settings', esc_html__('Candidate Settings', 'wp-jobsearch'), 'jobsearch_candidates_meta_settings', 'candidate', 'normal');
}

/**
 * Candidate settings meta box callback.
 */
function jobsearch_candidates_meta_settings() {
    global $post, $jobsearch_form_fields, $jobsearch_plugin_options, $jobsearch_currencies_list;
    $rand_num = rand(1000000, 99999999);
    $_post_id = $post->ID;

    $job_salary_types = isset($jobsearch_plugin_options['job-salary-types']) ? $jobsearch_plugin_options['job-salary-types'] : '';

    $job_custom_currency_switch = isset($jobsearch_plugin_options['job_custom_currency']) ? $jobsearch_plugin_options['job_custom_currency'] : '';

    $candidate_posted_by = get_post_meta($post->ID, 'jobsearch_field_users', true);

    $candidate_user_id = get_post_meta($post->ID, 'jobsearch_user_id', true);

    $salar_cur_list = array('default' => esc_html__('Default', 'wp-jobsearch'));
    if (!empty($jobsearch_currencies_list)) {
        foreach ($jobsearch_currencies_list as $jobsearch_curr_key => $jobsearch_curr_item) {
            $cus_cur_name = isset($jobsearch_curr_item['name']) ? $jobsearch_curr_item['name'] : '';
            $cus_cur_symbol = isset($jobsearch_curr_item['symbol']) ? $jobsearch_curr_item['symbol'] : '';
            $salar_cur_list[$jobsearch_curr_key] = $cus_cur_name . ' - ' . $cus_cur_symbol;
        }
    }
    ?>
    <script>
        jQuery(document).ready(function () {
            jQuery('#jobsearch_candidate_publish_date').datetimepicker({
                timepicker: true,
                format: 'd-m-Y H:i:s'
            });
            jQuery('#jobsearch_candidate_expiry_date').datetimepicker({
                timepicker: true,
                format: 'd-m-Y H:i:s'
            });
        });
    </script>
    <div class="jobsearch-post-settings">
        <?php
        //
        do_action('jobsearch_candidate_meta_box_inbefore', $_post_id, $candidate_user_id);
        //
        $get_user_cand_id = get_user_meta($candidate_user_id, 'jobsearch_candidate_id', true);
        if ($get_user_cand_id != '' && $post->ID == $get_user_cand_id) {
            $user_obj = get_user_by('ID', $candidate_user_id);

            if (is_object($user_obj)) {
                ?>
                <br><br>
                <div class="jobsearch-element-field">
                    <div class="elem-label">
                        <label><?php esc_html_e('Attached User', 'wp-jobsearch') ?></label>
                    </div>
                    <div class="elem-field">
                        <?php
                        echo '<strong>' . ($user_obj->user_login) . '</strong>';
                        //
                        $user_phone = get_post_meta($_post_id, 'jobsearch_field_user_phone', true);
                        echo '<p>' . sprintf(esc_html__('User email : %s', 'wp-jobsearch'), $user_obj->user_email) . '</p>';
                        if ($user_phone != '') {
                            echo '<p>' . sprintf(esc_html__('User Phone : %s', 'wp-jobsearch'), $user_phone) . '</p>';
                        }
                        ?>
                    </div>
                </div>
                <br><br>
                <?php
            }
        }

        do_action('jobsearch_candidate_admin_meta_fields_before', $post->ID);

        $sdate_format = jobsearch_get_wp_date_simple_format();

        $days = array();
        for ($day = 1; $day <= 31; $day++) {
            $days[$day] = $day;
        }
        $months = array();
        for ($month = 1; $month <= 12; $month++) {
            $months[$month] = $month;
        }
        $years = array();
        for ($year = 1900; $year <= date('Y'); $year++) {
            $years[$year] = $year;
        }

        $cand_dob_switch = isset($jobsearch_plugin_options['cand_dob_switch']) ? $jobsearch_plugin_options['cand_dob_switch'] : 'on';
        if ($cand_dob_switch == 'on') {
            ?>
            <div class="jobsearch-element-field" style="display: none;">
                <div class="elem-label">
                    <label><?php esc_html_e('Date of Birth', 'wp-jobsearch') ?></label>
                </div>
                <div class="elem-field">
                    <?php
                    ob_start();
                    ?>
                    <div style="float:left; margin-right: 4px; width: 80px;">
                        <?php
                        $field_params = array(
                            'std' => date('d'),
                            'name' => 'user_dob_dd',
                            'options' => $days,
                        );
                        $jobsearch_form_fields->select_field($field_params);
                        ?>
                    </div>
                    <?php
                    $dob_dd_html = ob_get_clean();
                    ob_start();
                    ?>
                    <div style="float:left; margin-right: 4px; width: 80px;">
                        <?php
                        $field_params = array(
                            'std' => date('m'),
                            'name' => 'user_dob_mm',
                            'options' => $months,
                        );
                        $jobsearch_form_fields->select_field($field_params);
                        ?>
                    </div>
                    <?php
                    $dob_mm_html = ob_get_clean();
                    ob_start();
                    ?>
                    <div style="float:left; margin-right: 4px; width: 80px;">
                        <?php
                        $field_params = array(
                            'std' => date('Y'),
                            'name' => 'user_dob_yy',
                            'options' => $years,
                        );
                        $jobsearch_form_fields->select_field($field_params);
                        ?>
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
            </div>
        <?php } ?>
        <div class="jobsearch-element-field" style="display: none;">
            <div class="elem-label">
                <label><?php esc_html_e('Phone', 'wp-jobsearch') ?></label>
            </div>
            <div class="elem-field">
                <?php
                $field_params = array(
                    'name' => 'user_phone',
                );
                $jobsearch_form_fields->input_field($field_params);
                ?>
            </div>
        </div>

        <div class="jobsearch-element-field" style="display: none;">
            <div class="elem-label">
                <label><?php esc_html_e('Urgent Candidate', 'wp-jobsearch') ?></label>
            </div>
            <div class="elem-field">
                <?php
                $cand_urgnt_val = get_post_meta($_post_id, 'cuscand_urgent_fbckend', true);

                if ($cand_urgnt_val != 'on' && $cand_urgnt_val != 'off') {
                    $urgnt_att_pckg = get_post_meta($_post_id, 'att_urgent_pkg_orderid', true);
                    if (!jobsearch_promote_profile_pkg_is_expired($urgnt_att_pckg)) {
                        $cand_urgnt_val = 'on';
                    }
                }

                $field_params = array(
                    'force_std' => $cand_urgnt_val,
                    'name' => 'cand_urgent',
                    'cus_name' => 'cuscand_urgent_fbckend',
                );
                $jobsearch_form_fields->checkbox_field($field_params);
                ?>
            </div>
        </div>

        <div class="jobsearch-element-field" style="display: none;">
            <div class="elem-label">
                <label><?php esc_html_e('Featured Candidate', 'wp-jobsearch') ?></label>
            </div>
            <div class="elem-field">
                <?php
                $cand_feature_val = get_post_meta($_post_id, 'cuscand_feature_fbckend', true);

                if ($cand_feature_val != 'on' && $cand_feature_val != 'off') {
                    $feature_att_pckg = get_post_meta($_post_id, 'att_promote_profile_pkgorder', true);
                    if (!jobsearch_promote_profile_pkg_is_expired($feature_att_pckg)) {
                        $cand_feature_val = 'on';
                    }
                }

                $field_params = array(
                    'force_std' => $cand_feature_val,
                    'name' => 'cand_feature',
                    'cus_name' => 'cuscand_feature_fbckend',
                );
                $jobsearch_form_fields->checkbox_field($field_params);
                ?>
            </div>
        </div>

        <div class="jobsearch-element-field">
            <div class="elem-label">
                <label><?php esc_html_e('Approved', 'wp-jobsearch') ?></label>
            </div>
            <div class="elem-field">
                <?php
                $field_params = array(
                    'std' => 'on',
                    'name' => 'candidate_approved',
                );
                $jobsearch_form_fields->checkbox_field($field_params);
                ?>
            </div>
        </div>
        <div class="jobsearch-element-field" style="display: none;">
            <div class="elem-label">
                <label><?php esc_html_e('Job Title', 'wp-jobsearch') ?></label>
            </div>
            <div class="elem-field">
                <?php
                $field_params = array(
                    'name' => 'candidate_jobtitle',
                );
                $jobsearch_form_fields->input_field($field_params);
                ?>
            </div>
        </div>
        <?php
        $salary_onoff_switch = isset($jobsearch_plugin_options['cand_salary_switch']) ? $jobsearch_plugin_options['cand_salary_switch'] : 'on';
        if ($salary_onoff_switch == 'on') {
            ?>
            <div class="jobsearch-element-field" style="display: none;">
                <div class="elem-label">
                    <label><?php esc_html_e('Salary', 'wp-jobsearch') ?></label>
                </div>
                <div class="elem-field">
                    <?php
                    $field_params = array(
                        'name' => 'candidate_salary',
                    );
                    $jobsearch_form_fields->input_field($field_params);
                    ?>
                </div>
            </div>
            <?php
            if (!empty($job_salary_types)) {
                $salar_types = array();
                $slar_type_count = 1;
                foreach ($job_salary_types as $job_salary_type) {
                    $salar_types['type_' . $slar_type_count] = $job_salary_type;
                    $slar_type_count++;
                }
                ?>
                <div class="jobsearch-element-field" style="display: none;">
                    <div class="elem-label">
                        <label><?php esc_html_e('Salary Type', 'wp-jobsearch') ?></label>
                    </div>
                    <div class="elem-field">
                        <?php
                        $field_params = array(
                            'name' => 'candidate_salary_type',
                            'options' => $salar_types,
                        );
                        $jobsearch_form_fields->select_field($field_params);
                        ?>
                    </div>
                </div>
                <?php
            }
            if ($job_custom_currency_switch == 'on') {
                ?>
                <div class="jobsearch-element-field">
                    <div class="elem-label">
                        <label><?php esc_html_e('Salary Currency', 'wp-jobsearch') ?></label>
                    </div>
                    <div class="elem-field">
                        <?php
                        $field_params = array(
                            'name' => 'candidate_salary_currency',
                            'options' => $salar_cur_list,
                        );
                        $jobsearch_form_fields->select_field($field_params);
                        ?>
                    </div>
                </div>
                <div class="jobsearch-element-field">
                    <div class="elem-label">
                        <label><?php esc_html_e('Currency position', 'wp-jobsearch') ?></label>
                    </div>
                    <div class="elem-field">
                        <?php
                        $field_params = array(
                            'name' => 'candidate_salary_pos',
                            'options' => array(
                                'left' => esc_html__('Left', 'wp-jobsearch'),
                                'right' => esc_html__('Right', 'wp-jobsearch'),
                                'left_space' => esc_html__('Left with space', 'wp-jobsearch'),
                                'right_space' => esc_html__('Right with space', 'wp-jobsearch'),
                            ),
                        );
                        $jobsearch_form_fields->select_field($field_params);
                        ?>
                    </div>
                </div>
                <div class="jobsearch-element-field">
                    <div class="elem-label">
                        <label><?php esc_html_e('Thousand separator', 'wp-jobsearch') ?></label>
                    </div>
                    <div class="elem-field">
                        <?php
                        $field_params = array(
                            'std' => ',',
                            'name' => 'candidate_salary_sep',
                        );
                        $jobsearch_form_fields->input_field($field_params);
                        ?>
                    </div>
                </div>
                <div class="jobsearch-element-field">
                    <div class="elem-label">
                        <label><?php esc_html_e('Number of decimals', 'wp-jobsearch') ?></label>
                    </div>
                    <div class="elem-field">
                        <?php
                        $field_params = array(
                            'std' => '2',
                            'name' => 'candidate_salary_deci',
                        );
                        $jobsearch_form_fields->input_field($field_params);
                        ?>
                    </div>
                </div>
                <?php
            }
        }

        // load custom fields which is configured in candidate custom fields
        do_action('jobsearch_custom_fields_load', $post->ID, 'candidate');
        ?>
        <div class="jobsearch-elem-heading">
            <h2><?php esc_html_e('Social Links', 'wp-jobsearch') ?></h2>
        </div>
        <div class="jobsearch-element-field">
            <div class="elem-label">
                <label><?php esc_html_e('Facebook', 'wp-jobsearch') ?></label>
            </div>
            <div class="elem-field">
                <?php
                $field_params = array(
                    'name' => 'user_facebook_url',
                );
                $jobsearch_form_fields->input_field($field_params);
                ?>
            </div>
        </div>
        <div class="jobsearch-element-field">
            <div class="elem-label">
                <label><?php esc_html_e('Twitter', 'wp-jobsearch') ?></label>
            </div>
            <div class="elem-field">
                <?php
                $field_params = array(
                    'name' => 'user_twitter_url',
                );
                $jobsearch_form_fields->input_field($field_params);
                ?>
            </div>
        </div>
        <div class="jobsearch-element-field" style="display: none;">
            <div class="elem-label">
                <label><?php esc_html_e('Google Plus', 'wp-jobsearch') ?></label>
            </div>
            <div class="elem-field">
                <?php
                $field_params = array(
                    'name' => 'user_google_plus_url',
                );
                $jobsearch_form_fields->input_field($field_params);
                ?>
            </div>
        </div>
        <div class="jobsearch-element-field">
            <div class="elem-label">
                <label><?php esc_html_e('Linkedin', 'wp-jobsearch') ?></label>
            </div>
            <div class="elem-field">
                <?php
                $field_params = array(
                    'name' => 'user_linkedin_url',
                );
                $jobsearch_form_fields->input_field($field_params);
                ?>
            </div>
        </div>
        <div class="jobsearch-element-field" style="display: none;">
            <div class="elem-label">
                <label><?php esc_html_e('Dribbble', 'wp-jobsearch') ?></label>
            </div>
            <div class="elem-field">
                <?php
                $field_params = array(
                    'name' => 'user_dribbble_url',
                );
                $jobsearch_form_fields->input_field($field_params);
                ?>
            </div>
        </div>
        <?php
       do_action('jobsearch_cand_admin_meta_after_social', $post->ID);
        $all_location_allow = isset($jobsearch_plugin_options['all_location_allow']) ? $jobsearch_plugin_options['all_location_allow'] : '';

        if ($all_location_allow == 'on') {
            ?>
            <div class="jobsearch-elem-heading">
                <h2><?php esc_html_e('Location', 'wp-jobsearch') ?></h2>
            </div>
            <?php
        }
        do_action('jobsearch_admin_location_map', $post->ID);
        // candidate multi meta fields
    //    do_action('candidate_multi_fields_meta', $post);
        $multiple_cv_files_allow = isset($jobsearch_plugin_options['multiple_cv_uploads']) ? $jobsearch_plugin_options['multiple_cv_uploads'] : '';
        if ($multiple_cv_files_allow == 'on') {
            ?>
            <div class="jobsearch-elem-heading">
                <h2><?php esc_html_e('CV Files', 'wp-jobsearch') ?></h2>
            </div>
            <div class="jobsearch-element-field">
                <div class="elem-field">
                    <?php
                    $ca_at_cv_files = get_post_meta($_post_id, 'candidate_cv_files', true);
                    if (!empty($ca_at_cv_files)) {
                        ?>
                        <div class="cancom-cvfiles-holder">
                            <?php
                            $cv_files_count = count($ca_at_cv_files);
                            foreach ($ca_at_cv_files as $cv_file_key => $cv_file_val) {
                                $file_attach_id = isset($cv_file_val['file_id']) ? $cv_file_val['file_id'] : '';
                                $file_url = isset($cv_file_val['file_url']) ? $cv_file_val['file_url'] : '';
                                $cv_primary = isset($cv_file_val['primary']) ? $cv_file_val['primary'] : '';

                                $cv_file_title = get_the_title($file_attach_id);
                                $attach_post = get_post($file_attach_id);

                                $attach_date = isset($attach_post->post_date) ? $attach_post->post_date : '';
                                $attach_mime = isset($attach_post->post_mime_type) ? $attach_post->post_mime_type : '';

                                if ($attach_mime == 'application/pdf') {
                                    $attach_icon = 'fa fa-file-pdf-o';
                                } else if ($attach_mime == 'application/msword' || $attach_mime == 'application/vnd.openxmlformats-officedocument.wordprocessingml.document') {
                                    $attach_icon = 'fa fa-file-word-o';
                                } else {
                                    $attach_icon = 'fa fa-file-word-o';
                                }

                                if ($file_attach_id > 0) {
                                    ?>
                                    <div class="jobsearch-cv-manager-list">
                                        <ul class="jobsearch-row">
                                            <li class="jobsearch-column-12">
                                                <div class="jobsearch-cv-manager-wrap">
                                                    <a class="jobsearch-cv-manager-thumb"><i class="<?php echo ($attach_icon) ?>"></i></a>
                                                    <div class="jobsearch-cv-manager-text">
                                                        <div class="jobsearch-cv-manager-left">
                                                            <h2><a href="<?php echo ($file_url) ?>" download="<?php echo ($cv_file_title) ?>"><?php echo (strlen($cv_file_title) > 40 ? substr($cv_file_title, 0, 40) . '...' : $cv_file_title) ?></a></h2>
                                                            <?php
                                                            if ($attach_date != '') {
                                                                ?>
                                                                <ul>
                                                                    <li><i class="fa fa-calendar"></i> <?php echo date_i18n(get_option('date_format'), strtotime($attach_date)) . ' ' . date_i18n(get_option('time_format'), strtotime($attach_date)) ?></li>
                                                                </ul>
                                                                <?php
                                                            }
                                                            ?>
                                                        </div>
                                                        <a href="javascript:void(0);" class="jobsearch-cv-manager-link jobsearch-del-user-cv" data-id="<?php echo ($file_attach_id) ?>"><i class="jobsearch-icon jobsearch-rubbish"></i></a>
                                                        <a href="<?php echo ($file_url) ?>" class="jobsearch-cv-manager-link jobsearch-cv-manager-download" download="<?php echo ($cv_file_title) ?>"><i class="jobsearch-icon jobsearch-download-arrow"></i></a>
                                                    </div>
                                                </div>
                                            </li>
                                        </ul>
                                    </div>
                                    <?php
                                }
                            }
                            ?>
                        </div>
                        <?php
                    } else {
                        ?>
                        <p><?php esc_html_e('No File attached.', 'wp-jobsearch') ?></p>
                        <?php
                    }
                    ?>
                </div>
            </div>
            <?php
        } else {
            ?>
            <div class="jobsearch-element-field">
                <div class="elem-label">
                    <label><?php esc_html_e('CV Attachment', 'wp-jobsearch') ?></label>
                </div>
                <div class="elem-field">
                    <?php
                    $field_params = array(
                        'id' => 'user_cv_attachment' . rand(10000000, 999999999),
                        'name' => 'user_cv_attachment',
                    );
                    $jobsearch_form_fields->file_upload_field($field_params);
                    ?>
                </div>
            </div>
            <?php
        }
        echo apply_filters('jobsearch_cand_backend_meta_after_cv_field', '', $_post_id);
        //
        $security_questions = isset($jobsearch_plugin_options['jobsearch-security-questions']) ? $jobsearch_plugin_options['jobsearch-security-questions'] : '';
        if (!empty($security_questions) && sizeof($security_questions) >= 3) {

            $sec_questions = get_post_meta($post->ID, 'user_security_questions', true);
            if (!empty($sec_questions)) {
                ?>
                <div class="jobsearch-elem-heading"> <h2><?php esc_html_e('Security Questions', 'wp-jobsearch') ?></h2> </div>
                <?php
                $answer_to_ques = isset($sec_questions['answers']) ? $sec_questions['answers'] : '';
                $qcount = 0;
                $qcount_num = 1;
                if (!empty($answer_to_ques)) {
                    foreach ($answer_to_ques as $sec_ans) {
                        $_ques = isset($sec_questions['questions'][$qcount]) ? $sec_questions['questions'][$qcount] : '';
                        $_answer_to_ques = $sec_ans;
                        ?>
                        <div class="jobsearch-element-field">
                            <div class="elem-label">
                                <label><?php printf(esc_html__('Question No %s :', 'wp-jobsearch'), $qcount_num) ?> <span><?php echo ($_ques) ?></span></label>
                            </div>
                            <div class="elem-field">
                                <input type="hidden" name="user_security_questions[questions][]" value="<?php echo ($_ques) ?>">
                                <input type="text" name="user_security_questions[answers][]" disabled="disabled" value="<?php echo ($_answer_to_ques) ?>">
                            </div>
                        </div>
                        <?php
                        $qcount_num++;
                        $qcount++;
                    }
                }
            }
        }
        ?>
        <div class="jobsearch-elem-heading" style="display: none;">
            <h2><?php esc_html_e('User Asign Packages', 'wp-jobsearch') ?></h2>
        </div>

        <?php
        $args = array(
            'post_type' => 'package',
            'posts_per_page' => -1,
            'post_status' => 'publish',
            'order' => 'ASC',
            'orderby' => 'title',
            'meta_query' => array(
                array(
                    'key' => 'jobsearch_field_package_type',
                    'value' => array('candidate', 'urgent_pkg', 'promote_profile'),
                    'compare' => 'IN',
                ),
            ),
        );
        $pkgs_query = new WP_Query($args);

        if ($pkgs_query->have_posts()) {
            ?>
            <div class="packge-asignbtn-holder">
                <label><?php esc_html_e('Select Package and assign to user:', 'wp-jobsearch') ?></label>
                <select id="jobsearch-assign-pck-slect" class="user_asign_pckg_drpdown">
                    <?php
                    $firts_pkg_id = 0;
                    $pck_countre = 1;
                    while ($pkgs_query->have_posts()) : $pkgs_query->the_post();
                        $pkg_rand = rand(10000000, 99999999);
                        $pkg_id = get_the_ID();
                        if ($pck_countre == 1) {
                            $firts_pkg_id = $pkg_id;
                        }
                        ?>
                        <option value="<?php echo ($pkg_id) ?>"><?php echo get_the_title($pkg_id) ?></option>
                        <?php
                        $pck_countre ++;
                    endwhile;
                    wp_reset_postdata();
                    ?>
                </select>
                <a href="javascript:void(0);" data-uid="<?php echo ($candidate_user_id) ?>" data-id="<?php echo ($firts_pkg_id) ?>" class="button button-primary button-large admin-packge-asignbtn"><?php esc_html_e('Assign new package to this User', 'wp-jobsearch') ?></a>
                <span class="assign-loder"></span>
            </div>
            <script>
                jQuery(document).on('change', '#jobsearch-assign-pck-slect', function () {
                    jQuery('.admin-packge-asignbtn').attr('data-id', jQuery(this).val());
                });
                jQuery(document).on('click', '.admin-packge-asignbtn', function () {

                    var loader_con = jQuery(this).parent('.packge-asignbtn-holder').find('.assign-loder');

                    var pkg_id = jQuery(this).attr('data-id');
                    var user_id = jQuery(this).attr('data-uid');

                    loader_con.html('<i class="fa fa-refresh fa-spin"></i>');
                    var request = $.ajax({
                        url: '<?php echo admin_url('admin-ajax.php') ?>',
                        method: "POST",
                        data: {
                            'user_id': user_id,
                            'pkg_id': pkg_id,
                            'action': 'jobsearch_admin_assign_packge_to_user'
                        },
                        dataType: "json"
                    });
                    request.done(function (response) {
                        loader_con.html('');
                        if (typeof response.success !== 'undefined' && response.success == '1') {
                            loader_con.html(response.msg);
                        }
                    });

                    request.fail(function (jqXHR, textStatus) {
                        loader_con.html('');
                    });
                });
            </script>
            <?php
        }

        $args = array(
            'post_type' => 'shop_order',
            'posts_per_page' => -1,
            'post_status' => 'wc-completed',
            'order' => 'DESC',
            'orderby' => 'ID',
            'meta_query' => array(
                array(
                    'key' => 'jobsearch_order_attach_with',
                    'value' => 'package',
                    'compare' => '=',
                ),
                array(
                    'key' => 'package_type',
                    'value' => array('candidate', 'urgent_pkg', 'promote_profile'),
                    'compare' => 'IN',
                ),
                array(
                    'key' => 'jobsearch_order_user',
                    'value' => $candidate_user_id,
                    'compare' => '=',
                ),
            ),
        );
        $pkgs_query = new WP_Query($args);

        if ($pkgs_query->have_posts()) {
            ?>

            <div class="jobsearch-jobs-list-holder">
                <div class="jobsearch-managejobs-list">
                    <div class="jobsearch-table-layer jobsearch-managejobs-thead">
                        <div class="jobsearch-table-row">
                            <div class="jobsearch-table-cell" style="width: 20%;"><?php esc_html_e('Order ID', 'wp-jobsearch') ?></div>
                            <div class="jobsearch-table-cell"><?php esc_html_e('Package', 'wp-jobsearch') ?></div>
                            <div class="jobsearch-table-cell"><?php esc_html_e('Total Applications', 'wp-jobsearch') ?></div>
                            <div class="jobsearch-table-cell"><?php esc_html_e('Used', 'wp-jobsearch') ?></div>
                            <div class="jobsearch-table-cell"><?php esc_html_e('Remaining', 'wp-jobsearch') ?></div>
                            <div class="jobsearch-table-cell"><?php esc_html_e('Package Expiry', 'wp-jobsearch') ?></div>
                            <div class="jobsearch-table-cell"><?php esc_html_e('Satus', 'wp-jobsearch') ?></div>
                        </div>
                    </div>
                    <?php
                    while ($pkgs_query->have_posts()) : $pkgs_query->the_post();
                        $pkg_rand = rand(10000000, 99999999);
                        $pkg_order_id = get_the_ID();
                        $pkg_order_name = get_post_meta($pkg_order_id, 'package_name', true);

                        //
                        $pkg_type = get_post_meta($pkg_order_id, 'package_type', true);

                        $total_apps = get_post_meta($pkg_order_id, 'num_of_apps', true);

                        $used_apps = jobsearch_pckg_order_used_apps($pkg_order_id);
                        $remaining_apps = jobsearch_pckg_order_remaining_apps($pkg_order_id);

                        $pkg_exp_dur = get_post_meta($pkg_order_id, 'package_expiry_time', true);
                        $pkg_exp_dur_unit = get_post_meta($pkg_order_id, 'package_expiry_time_unit', true);

                        $status_txt = esc_html__('Active', 'wp-jobsearch');
                        $status_class = ' style="color: green;"';

                        if (jobsearch_app_pckg_order_is_expired($pkg_order_id)) {
                            $status_txt = esc_html__('Expired', 'wp-jobsearch');
                            $status_class = ' style="color: red;"';
                        }
                        if ($pkg_type == 'promote_profile') {
                            $status_txt = esc_html__('Active', 'wp-jobsearch');
                            $status_class = ' style="color: green;"';

                            if (jobsearch_promote_profile_pkg_is_expired($pkg_order_id)) {
                                $status_txt = esc_html__('Expired', 'wp-jobsearch');
                                $status_class = ' style="color: red;"';
                            }
                        }
                        if ($pkg_type == 'urgent_pkg') {
                            $status_txt = esc_html__('Active', 'wp-jobsearch');
                            $status_class = ' style="color: green;"';

                            if (jobsearch_member_urgent_pkg_is_expired($pkg_order_id)) {
                                $status_txt = esc_html__('Expired', 'wp-jobsearch');
                                $status_class = ' style="color: red;"';
                            }
                        }
                        ?>
                        <div class="jobsearch-table-layer jobsearch-packages-tbody">
                            <div class="jobsearch-table-row">
                                <div class="jobsearch-table-cell" style="width: 20%;">#<?php echo ($pkg_order_id) ?></div>
                                <div class="jobsearch-table-cell"><span><?php echo ($pkg_order_name) ?></span></div>

                                <div class="jobsearch-table-cell"><?php echo ($total_apps) ?></div>
                                <div class="jobsearch-table-cell"><?php echo ($used_apps) ?></div>
                                <div class="jobsearch-table-cell"><?php echo ($remaining_apps) ?></div>

                                <div class="jobsearch-table-cell"><?php echo absint($pkg_exp_dur) . ' ' . jobsearch_get_duration_unit_str($pkg_exp_dur_unit) ?></div>
                                <div class="jobsearch-table-cell"<?php echo ($status_class) ?>><?php echo ($status_txt) ?></div>
                            </div>
                        </div>
                        <?php
                    endwhile;
                    wp_reset_postdata();
                    ?>
                </div>
            </div>
            <?php
        }
        ?> 

    </div> 
    <?php
}
