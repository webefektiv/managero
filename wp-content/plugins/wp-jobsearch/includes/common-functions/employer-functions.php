<?php
if (!function_exists('jobsearch_employer_get_profile_image')) {

    function jobsearch_employer_get_profile_image($employer_id) {
        $post_thumbnail_id = '';
        if (isset($employer_id) && $employer_id != '' && has_post_thumbnail($employer_id)) {
            $post_thumbnail_id = get_post_thumbnail_id($employer_id);
        }
        return $post_thumbnail_id;
    }

}

if (!function_exists('jobsearch_employer_get_company_name')) {

    function jobsearch_employer_get_company_name($employer_id, $before_title = '', $after_title = '') {
        $company_name_str = '';
        $employer_field_user = get_post_meta($employer_id, 'jobsearch_field_employer_posted_by', true);
        if (isset($employer_field_user) && $employer_field_user != '') {
            $company_name_str = '<a href="' . get_permalink($employer_field_user) . '">' . $before_title . get_the_title($employer_field_user) . $after_title . '</a>';
        }
        return $company_name_str;
    }

}

if (!function_exists('jobsearch_employer_get_all_employertypes')) {

    function jobsearch_employer_get_all_employertypes($employer_id, $link_class = 'jobsearch-option-btn', $before_title = '', $after_title = '', $before_tag = '', $after_tag = '') {

        $employer_type = wp_get_post_terms($employer_id, 'employertype');
        ob_start();
        $html = '';
        if (!empty($employer_type)) {
            $link_class_str = '';
            if ($link_class != '') {
                $link_class_str = 'class="' . $link_class . '"';
            }
            echo ($before_tag);
            foreach ($employer_type as $term) :
                $employertype_color = get_term_meta($term->term_id, 'jobsearch_field_employertype_color', true);
                $employertype_textcolor = get_term_meta($term->term_id, 'jobsearch_field_employertype_textcolor', true);
                $employertype_color_str = '';
                if ($employertype_color != '') {
                    $employertype_color_str = ' style="background-color: ' . esc_attr($employertype_color) . '; color: ' . esc_attr($employertype_textcolor) . ' "';
                }
                ?>
                <a <?php echo force_balance_tags($link_class_str) ?> <?php echo force_balance_tags($employertype_color_str); ?>>
                    <?php
                    echo ($before_title);
                    echo esc_html($term->name);
                    echo ($after_title);
                    ?>
                </a>
                <?php
            endforeach;
            echo ($after_tag);
        }
        $html .= ob_get_clean();
        return $html;
    }

}

if (!function_exists('jobsearch_employer_not_allow_to_mod')) {

    function jobsearch_employer_not_allow_to_mod($user_id = 0) {
        global $jobsearch_plugin_options;
        if ($user_id <= 0 && is_user_logged_in()) {
            $user_id = get_current_user_id();
        }
        $user_is_employer = jobsearch_user_is_employer($user_id);
        if ($user_is_employer) {
            $demo_user_login = isset($jobsearch_plugin_options['demo_user_login']) ? $jobsearch_plugin_options['demo_user_login'] : '';
            $demo_user_mod = isset($jobsearch_plugin_options['demo_user_mod']) ? $jobsearch_plugin_options['demo_user_mod'] : '';
            $demo_employer = isset($jobsearch_plugin_options['demo_employer']) ? $jobsearch_plugin_options['demo_employer'] : '';
            $_demo_user_obj = get_user_by('login', $demo_employer);
            $_demo_user_id = isset($_demo_user_obj->ID) ? $_demo_user_obj->ID : '';
            if ($user_id == $_demo_user_id && $demo_user_login == 'on' && $demo_user_mod != 'on') {
                return true;
            }
        }
        return false;
    }

}

if (!function_exists('jobsearch_employer_get_all_sectors')) {

    function jobsearch_employer_get_all_sectors($employer_id, $link_class = '', $before_title = '', $after_title = '', $before_tag = '', $after_tag = '', $sec_link = '') {

        $sectors = wp_get_post_terms($employer_id, 'sector');
        ob_start();
        $html = '';
        if (!empty($sectors)) {
            $link_class_str = '';
            if ($link_class != '') {
                $link_class_str = 'class="' . $link_class . '"';
            }
            echo ($before_tag);
            $flag = 0;
            foreach ($sectors as $term) :
                if ($flag > 0) {
                    echo ", ";
                }
                ?>
                <a <?php echo ($sec_link != '' ? 'href="' . $sec_link . '?sector=' . $term->slug . '"' : '') ?> class="<?php echo force_balance_tags($link_class) ?>">
                    <?php
                    echo ($before_title);
                    echo esc_html($term->name);
                    echo ($after_title);
                    ?>
                </a>
                <?php
                $flag++;
            endforeach;
            echo ($after_tag);
        }
        $html .= ob_get_clean();
        return $html;
    }

}
if (!function_exists('jobsearch_get_employer_item_count')) {

    function jobsearch_get_employer_item_count($left_filter_count_switch, $args, $count_arr, $employer_short_counter, $field_meta_key, $open_house = '') {
        if ($left_filter_count_switch == 'yes') {
            global $jobsearch_shortcode_employers_frontend;

            // get all arguments from getting flters
            $left_filter_arr = array();
            $left_filter_arr = $jobsearch_shortcode_employers_frontend->get_filter_arg($employer_short_counter, $field_meta_key);
            if (!empty($count_arr)) {
                // check if count array has multiple condition
                foreach ($count_arr as $count_arr_single) {
                    $left_filter_arr[] = $count_arr_single;
                }
            }

            $post_ids = '';
            if (!empty($left_filter_arr)) {
                // apply all filters and get ids
                $post_ids = $jobsearch_shortcode_employers_frontend->get_employer_id_by_filter($left_filter_arr);
            }

            if (isset($_REQUEST['location']) && $_REQUEST['location'] != '' && !isset($_REQUEST['loc_polygon_path'])) {
                $radius = isset($_REQUEST['radius']) ? $_REQUEST['radius'] : '';
                $post_ids = $jobsearch_shortcode_employers_frontend->employer_location_filter($_REQUEST['location'], $post_ids);
                if (empty($post_ids)) {
                    $post_ids = array(0);
                }
            }

            $all_post_ids = $post_ids;
            if (!empty($all_post_ids)) {
                $args['post__in'] = $all_post_ids;
            }

            $restaurant_loop_obj = jobsearch_get_cached_obj('employer_result_cached_loop_count_obj', $args, 12, false, 'wp_query');
            $restaurant_totnum = $restaurant_loop_obj->found_posts;
            return $restaurant_totnum;
        }
    }

}

function jobsearch_candsh_btn_catlist() {

    $user_id = get_current_user_id();
    $user_is_employer = jobsearch_user_is_employer($user_id);

    $cats_list = '';

    if ($user_is_employer) {
        $employer_id = jobsearch_get_user_employer_id($user_id);
        $cats_list = get_post_meta($employer_id, 'emp_resumesh_types', true);
    }
    return $cats_list;
}

add_action('jobsearch_add_employer_resume_to_list_btn', 'jobsearch_add_employer_resume_to_list_btn', 10, 1);

function jobsearch_add_employer_resume_to_list_btn($args = array()) {
    if (!is_user_logged_in()) {
        ?>
        <a href="javascript:void(0);" class="jobsearch-candidate-default-btn jobsearch-open-signin-tab"><i class="jobsearch-icon jobsearch-add-list"></i> <?php esc_html_e('Save Candidate', 'wp-jobsearch') ?></a>
        <?php
    } else {
        $candidate_id = isset($args['id']) ? $args['id'] : '';
        $download_cv = isset($args['download_cv']) ? $args['download_cv'] : '';

        $user_id = get_current_user_id();
        $user_is_employer = jobsearch_user_is_employer($user_id);
        $employer_resumes_list = array();
        if ($user_is_employer) {
            $employer_id = jobsearch_get_user_employer_id($user_id);
            $employer_resumes_list = get_post_meta($employer_id, 'jobsearch_candidates_list', true);
            $employer_resumes_list = explode(',', $employer_resumes_list);
        }
        $shortlist_str = in_array($candidate_id, $employer_resumes_list) ? esc_html__('Saved', 'wp-jobsearch') : esc_html__('Save Candidate', 'wp-jobsearch');

        $cats_list = jobsearch_candsh_btn_catlist();
        if (!empty($cats_list) && !in_array($candidate_id, $employer_resumes_list)) {
            ?>
            <a href="javascript:void(0);" class="jobsearch-candidate-default-btn jobsearch-candidatesh-opopupbtn" data-id="<?php echo ($candidate_id) ?>"><i class="jobsearch-icon jobsearch-add-list"></i> <?php echo ($shortlist_str) ?></a>
            <?php
            $popup_args = array('args' => $args, 'cats_list' => $cats_list);
            add_action('wp_footer', function () use ($popup_args) {

                extract(shortcode_atts(array(
                    'args' => '',
                    'cats_list' => '',
                                ), $popup_args));

                $candidate_id = isset($args['id']) ? $args['id'] : '';
                ?>
                <div class="jobsearch-modal fade" id="JobSearchModalCandShPopup<?php echo ($candidate_id) ?>">
                    <div class="modal-inner-area">&nbsp;</div>
                    <div class="modal-content-area">
                        <div class="modal-box-area">
                            <span class="modal-close"><i class="fa fa-times"></i></span>
                            <div id="usercand-shrtlistsecs-<?php echo ($candidate_id) ?>" class="jobsearch-usercand-shrtlistsec">
                                <h2><?php esc_html_e('Choose Type', 'wp-jobsearch'); ?></h2>
                                <div class="shcand-types-list">
                                    <div class="jobsearch-profile-select">
                                        <select name="shrtlist_type[]" multiple="multiple" class="selectize-select" placeholder="<?php esc_html_e('Select Types', 'wp-jobsearch') ?>">
                                            <?php
                                            $typse_count = 1;
                                            foreach ($cats_list as $sh_type_key => $sh_type) {
                                                ?>
                                                <option value="<?php echo ($sh_type_key) ?>"><?php echo ($sh_type) ?></option>
                                                <?php
                                                $typse_count++;
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="usercand-shrtlist-btnsec">
                                    <a href="javascript:void(0);" class="jobsearch-candidate-default-btn jobsearch-svcand-withtyp-tolist" data-id="<?php echo ($candidate_id) ?>"><i class="jobsearch-icon jobsearch-add-list"></i> <?php esc_html_e('Save Candidate', 'wp-jobsearch') ?></a>
                                    <span class="resume-loding-msg"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php
            }, 11, 1);
        } else {
            ?>
            <a href="javascript:void(0);" class="jobsearch-candidate-default-btn <?php echo (in_array($candidate_id, $employer_resumes_list) ? '' : 'jobsearch-add-resume-to-list') ?>" data-id="<?php echo ($candidate_id) ?>" data-download="<?php echo ($download_cv) ?>"><i class="jobsearch-icon jobsearch-add-list"></i> <?php echo ($shortlist_str) ?></a>
            <span class="resume-loding-msg"></span>
            <?php
        }
    }
}

add_action('wp_ajax_jobsearch_add_employer_resume_to_list', 'jobsearch_add_employer_resume_to_list');

function jobsearch_add_employer_resume_to_list() {
    global $jobsearch_plugin_options;
    $free_shortlist_allow = isset($jobsearch_plugin_options['free-shortlist-allow']) ? $jobsearch_plugin_options['free-shortlist-allow'] : '';

    $employer_pkgs_page = isset($jobsearch_plugin_options['resume_package_page']) ? $jobsearch_plugin_options['resume_package_page'] : '';

    $employer_pkgs_page_url = '';
    if ($employer_pkgs_page != '') {
        $employer_pkgs_page_obj = get_page_by_path($employer_pkgs_page);
        if (is_object($employer_pkgs_page_obj) && isset($employer_pkgs_page_obj->ID)) {
            $employer_pkgs_page_url = get_permalink($employer_pkgs_page_obj->ID);
        }
    }

    if (!is_user_logged_in()) {
        echo json_encode(array('msg' => esc_html__('You are not logged in.', 'wp-jobsearch'), 'error' => '1'));
        die;
    }

    //
    $candidate_id = isset($_POST['candidate_id']) ? $_POST['candidate_id'] : '0';
    $user_id = get_current_user_id();
    $c_user = wp_get_current_user();

    $user_is_employer = jobsearch_user_is_employer($user_id);
    if ($user_is_employer) {
        $employer_id = jobsearch_get_user_employer_id($user_id);
        $employer_resumes_list = get_post_meta($employer_id, 'jobsearch_candidates_list', true);

        if ($free_shortlist_allow == 'on') {
            if ($employer_resumes_list != '') {
                $employer_resumes_list = explode(',', $employer_resumes_list);
                if (!in_array($candidate_id, $employer_resumes_list)) {
                    $employer_resumes_list[] = $candidate_id;
                }
                $employer_resumes_list = implode(',', $employer_resumes_list);
            } else {
                $employer_resumes_list = $candidate_id;
            }
            update_post_meta($employer_id, 'jobsearch_candidates_list', $employer_resumes_list);
            if (isset($_POST['type_selected']) && !empty($_POST['type_selected'])) {
                $types_selected = $_POST['type_selected'];
                $_resume_typsh_list = get_post_meta($employer_id, 'jobsearch_resumtypes_list', true);
                $_resume_typsh_list = !empty($_resume_typsh_list) ? $_resume_typsh_list : array();
                $_resume_typsh_list[$candidate_id] = $types_selected;
                update_post_meta($employer_id, 'jobsearch_resumtypes_list', $_resume_typsh_list);
            }

            //
            do_action('jobsearch_user_shortlist_to_candidate', $c_user, $candidate_id);
            do_action('jobsearch_user_shortlist_to_employer', $c_user, $candidate_id);

            echo json_encode(array('msg' => esc_html__('Resume added to list.', 'wp-jobsearch')));
            die;
        } else {
            $user_cv_pkg = jobsearch_employer_first_subscribed_cv_pkg();
            if ($user_cv_pkg) {
                if ($employer_resumes_list != '') {
                    $employer_resumes_list = explode(',', $employer_resumes_list);
                    if (!in_array($candidate_id, $employer_resumes_list)) {
                        $employer_resumes_list[] = $candidate_id;
                    }
                    $employer_resumes_list = implode(',', $employer_resumes_list);
                } else {
                    $employer_resumes_list = $candidate_id;
                }
                $download_cv = isset($_POST['download_cv']) ? $_POST['download_cv'] : '';
                update_post_meta($employer_id, 'jobsearch_candidates_list', $employer_resumes_list);
                if (isset($_POST['type_selected']) && !empty($_POST['type_selected'])) {
                    $types_selected = $_POST['type_selected'];
                    $_resume_typsh_list = get_post_meta($employer_id, 'jobsearch_resumtypes_list', true);
                    $_resume_typsh_list = !empty($_resume_typsh_list) ? $_resume_typsh_list : array();
                    $_resume_typsh_list[$candidate_id] = $types_selected;
                    update_post_meta($employer_id, 'jobsearch_resumtypes_list', $_resume_typsh_list);
                }
                do_action('jobsearch_add_candidate_resume_id_to_order', $candidate_id, $user_cv_pkg);

                $downloadcv_link_btn = '';
                if ($download_cv == '1') {
                    $candidate_cv_file = get_post_meta($candidate_id, 'candidate_cv_file', true);

                    $multiple_cv_files_allow = isset($jobsearch_plugin_options['multiple_cv_uploads']) ? $jobsearch_plugin_options['multiple_cv_uploads'] : '';
                    if ($multiple_cv_files_allow == 'on') {
                        $ca_at_cv_files = get_post_meta($candidate_id, 'candidate_cv_files', true);
                        if (!empty($ca_at_cv_files)) {
                            ob_start();
                            ?>
                            <a href="<?php echo apply_filters('jobsearch_user_attach_cv_file_url', '', $candidate_id, 0) ?>" download="<?php echo apply_filters('jobsearch_user_attach_cv_file_title', '', $candidate_id, 0) ?>" class="jobsearch-candidate-download-btn"><i class="jobsearch-icon jobsearch-download-arrow"></i> <?php echo apply_filters('jobsearch_cand_downlod_cv_btntxt', esc_html__('Download CV', 'wp-jobsearch')) ?></a>
                            <?php
                            $downloadcv_link_btn = ob_get_clean();
                        }
                    } else if (!empty($candidate_cv_file)) {
                        $file_attach_id = isset($candidate_cv_file['file_id']) ? $candidate_cv_file['file_id'] : '';
                        $file_url = isset($candidate_cv_file['file_url']) ? $candidate_cv_file['file_url'] : '';

                        $cv_file_title = get_the_title($file_attach_id);
                        ob_start();
                        ?>
                        <a href="<?php echo ($file_url) ?>" download="<?php echo ($cv_file_title) ?>" class="jobsearch-candidate-download-btn"><i class="jobsearch-icon jobsearch-download-arrow"></i> <?php echo apply_filters('jobsearch_cand_downlod_cv_btntxt', esc_html__('Download CV', 'wp-jobsearch')) ?></a>
                        <?php
                        $downloadcv_link_btn = ob_get_clean();
                    }
                }
                //
                do_action('jobsearch_user_shortlist_to_candidate', $c_user, $candidate_id);
                do_action('jobsearch_user_shortlist_to_employer', $c_user, $candidate_id);
                echo json_encode(array('msg' => esc_html__('Resume added to list.', 'wp-jobsearch'), 'dbn' => $downloadcv_link_btn));
                die;
            } else {
                if ($employer_pkgs_page_url != '') {
                    $err_msg = wp_kses(sprintf(__('You have no package. <a href="%s">Click here</a> to subscribe a package.', 'wp-jobsearch'), $employer_pkgs_page_url), array('a' => array('href' => array())));
                } else {
                    $err_msg = esc_html__('You have no package. Please subscribe a package first.', 'wp-jobsearch');
                }
                echo json_encode(array('msg' => $err_msg, 'error' => '1'));
                die;
            }
        }
    } else {
        echo json_encode(array('msg' => esc_html__('You are not an employer.', 'wp-jobsearch'), 'error' => '1'));
        die;
    }
}

add_action('jobsearch_download_candidate_cv_btn', 'jobsearch_download_candidate_cv_btn', 10, 1);

function jobsearch_download_candidate_cv_btn($args = array()) {

    global $jobsearch_plugin_options;
    $free_shortlist_allow = isset($jobsearch_plugin_options['free-shortlist-allow']) ? $jobsearch_plugin_options['free-shortlist-allow'] : '';
    $candidate_id = isset($args['id']) ? $args['id'] : '';
    $classes = isset($args['classes']) ? $args['classes'] : '';
    $classes_ext = '';
    if (isset($classes) && !empty($classes)) {
        $classes_ext = ' ' . $classes . '';
    }
    $candidate_cv_file = get_post_meta($candidate_id, 'candidate_cv_file', true);

    $candidate_obj = get_post($candidate_id);
    $candidate_join_date = isset($candidate_obj->post_date) ? $candidate_obj->post_date : '';

    $candidate_jobtitle = get_post_meta($candidate_id, 'jobsearch_field_candidate_jobtitle', true);

    $candidate_user_id = jobsearch_get_candidate_user_id($candidate_id);
    $candidate_user_obj = get_user_by('ID', $candidate_user_id);
    $candidate_displayname = isset($candidate_user_obj->display_name) ? $candidate_user_obj->display_name : '';
    $candidate_displayname = apply_filters('jobsearch_user_display_name', $candidate_displayname, $candidate_user_obj);

    $user_def_avatar_url = get_avatar_url($candidate_user_id, array('size' => 184));

    $candidate_avatar_id = get_post_thumbnail_id($candidate_id);
    if ($candidate_avatar_id > 0) {
        $candidate_thumb_img = wp_get_attachment_image_src($candidate_avatar_id, 'thumbnail');
        $user_def_avatar_url = isset($candidate_thumb_img[0]) && esc_url($candidate_thumb_img[0]) != '' ? $candidate_thumb_img[0] : '';
    }

    $candidate_cv_file_att = array();
    $multiple_cv_files_allow = isset($jobsearch_plugin_options['multiple_cv_uploads']) ? $jobsearch_plugin_options['multiple_cv_uploads'] : '';
    if ($multiple_cv_files_allow == 'on') {
        $ca_at_cv_files = get_post_meta($candidate_id, 'candidate_cv_files', true);
        if (!empty($ca_at_cv_files)) {
            $candidate_cv_file_att = array(
                'file_title' => apply_filters('jobsearch_user_attach_cv_file_title', '', $candidate_id, 0),
                'file_url' => apply_filters('jobsearch_user_attach_cv_file_url', '', $candidate_id, 0),
            );
        }
    } else if (!empty($candidate_cv_file)) {
        $file_attach_id = isset($candidate_cv_file['file_id']) ? $candidate_cv_file['file_id'] : '';
        $file_url = isset($candidate_cv_file['file_url']) ? $candidate_cv_file['file_url'] : '';

        $cv_file_title = get_the_title($file_attach_id);
        $candidate_cv_file_att = array(
            'file_title' => $cv_file_title,
            'file_url' => $file_url,
        );
    }

    if (!empty($candidate_cv_file_att)) {
        $cv_file_title = isset($candidate_cv_file_att['file_title']) ? $candidate_cv_file_att['file_title'] : '';
        $file_url = isset($candidate_cv_file_att['file_url']) ? $candidate_cv_file_att['file_url'] : '';

        ob_start();
        ?>
        <a href="<?php echo ($file_url) ?>" download="<?php echo ($cv_file_title) ?>" class="jobsearch-candidate-download-btn<?php echo ($classes_ext); ?>"><i class="jobsearch-icon jobsearch-download-arrow"></i> <?php echo apply_filters('jobsearch_cand_downlod_cv_btntxt', esc_html__('Download CV', 'wp-jobsearch')) ?></a>
        <?php
        $download_link_btn = ob_get_clean();
        //
        if (!is_user_logged_in()) {
            ?>
            <a href="javascript:void(0);" class="jobsearch-candidate-download-btn jobsearch-open-signin-tab<?php echo ($classes_ext); ?>"><?php echo apply_filters('jobsearch_cand_downlod_cv_btntxt', esc_html__('Download CV', 'wp-jobsearch')) ?></a>
            <?php
        } else {
            $user_id = get_current_user_id();
            $cur_user_obj = wp_get_current_user();
            $is_employer = jobsearch_user_is_employer($user_id);

            if (in_array('administrator', (array) $cur_user_obj->roles)) {
                echo ($download_link_btn);
            } else if ($is_employer) {
                $employer_id = jobsearch_get_user_employer_id($user_id);
                $employer_resumes_list = get_post_meta($employer_id, 'jobsearch_candidates_list', true);
                $employer_resumes_list = explode(',', $employer_resumes_list);

                if (in_array($candidate_id, $employer_resumes_list)) {
                    echo ($download_link_btn);
                } else {
                    ?>
                    <a href="javascript:void(0);" class="jobsearch-candidate-download-btn jobsearch-open-dloadres-popup<?php echo ($classes_ext); ?>"><i class="jobsearch-icon jobsearch-download-arrow"></i> <?php echo apply_filters('jobsearch_cand_downlod_cv_btntxt', esc_html__('Download CV', 'wp-jobsearch')) ?></a>
                    <div class="jobsearch-modal jobsearch-typo-wrap fade" id="JobSearchDLoadResModal">
                        <div class="modal-inner-area">&nbsp;</div>
                        <div class="modal-content-area">
                            <div class="modal-box-area">
                                <div class="user-shortlist-area">
                                    <h4><?php esc_html_e('You must have to shortlist this candidate before download CV.', 'wp-jobsearch') ?></h4>
                                    <div class="shortlisting-user-info">
                                        <figure><img src="<?php echo ($user_def_avatar_url) ?>" alt=""></figure>
                                        <h2><a><?php echo ($candidate_displayname) ?></a></h2>
                                        <p><?php echo ($candidate_jobtitle) ?></p>
                                        <?php
                                        if ($candidate_join_date != '') {
                                            ?>
                                            <small><?php printf(esc_html__('Member Since, %s', 'wp-jobsearch'), date_i18n('M d, Y', strtotime($candidate_join_date))) ?></small>
                                            <?php
                                        }
                                        ?>
                                    </div>
                                    <div class="shortlisting-user-btn">
                                        <?php
                                        do_action('jobsearch_add_employer_resume_to_list_btn', array('id' => $candidate_id, 'download_cv' => '1'));
                                        ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php
                }
            } else {
                ?>
                <a href="javascript:void(0);" class="jobsearch-candidate-download-btn employer-access-btn<?php echo ($classes_ext); ?>"><i class="jobsearch-icon jobsearch-download-arrow"></i> <?php echo apply_filters('jobsearch_cand_downlod_cv_btntxt', esc_html__('Download CV', 'wp-jobsearch')) ?></a>
                <span class="employer-access-msg" style="display: none; float: left;"><i class="fa fa-warning"></i> <?php esc_html_e('Only an Employer can download resume.', 'wp-jobsearch') ?></span>
                <?php
            }
        }
    }
}

// Check if job is post by current employer
function jobsearch_is_employer_job($job_id = 0, $user_id = 0) {
    global $sitepress;
    if ($user_id <= 0 && is_user_logged_in()) {
        $user_id = get_current_user_id();
    }
    if (jobsearch_user_isemp_member($user_id)) {
        $employer_id = jobsearch_user_isemp_member($user_id);
    } else {
        $employer_id = jobsearch_get_user_employer_id($user_id);
    }

    $job_employer_id = get_post_meta($job_id, 'jobsearch_field_job_posted_by', true);
    if (function_exists('icl_object_id') && function_exists('wpml_init_language_switcher')) {
        $current_lang = $sitepress->get_current_language();
        $icl_post_id = icl_object_id($job_employer_id, 'employer', false, $current_lang);

        if ($icl_post_id > 0) {
            $job_employer_id = $icl_post_id;
        }
    }
    if ($employer_id == $job_employer_id) {
        return true;
    }
    return false;
}

// get user package used jobs
function jobsearch_pckg_order_used_fjobs($order_id = 0) {
    $jobs_list_count = 0;
    if ($order_id > 0) {
        $total_jobs = get_post_meta($order_id, 'num_of_fjobs', true);
        $jobs_list = get_post_meta($order_id, 'jobsearch_order_featc_list', true);

        if (!empty($jobs_list)) {
            $jobs_list_count = count(explode(',', $jobs_list));
        }
    }

    return $jobs_list_count;
}

// get user package remaining jobs
function jobsearch_pckg_order_remaining_fjobs($order_id = 0) {
    $remaining_jobs = 0;
    if ($order_id > 0) {
        $total_jobs = get_post_meta($order_id, 'num_of_fjobs', true);
        $used_jobs = jobsearch_pckg_order_used_jobs($order_id);

        $remaining_jobs = $total_jobs > $used_jobs ? $total_jobs - $used_jobs : 0;
    }

    return $remaining_jobs;
}

function jobsearch_pckg_order_used_featjob_credits($order_id = 0) {
    $jobs_list_count = 0;
    if ($order_id > 0) {

        $jobs_list = get_post_meta($order_id, 'jobsearch_order_featc_list', true);

        if (!empty($jobs_list)) {
            $jobs_list_count = count(explode(',', $jobs_list));
        }
    }

    return $jobs_list_count;
}

function jobsearch_pckg_order_remain_featjob_credits($order_id = 0) {
    $remaining_credits = 0;
    if ($order_id > 0) {
        $total_credits = get_post_meta($order_id, 'feat_job_credits', true);
        $used_jobs = jobsearch_pckg_order_used_featjob_credits($order_id);

        $remaining_credits = $total_credits > $used_jobs ? $total_credits - $used_jobs : 0;
    }

    return $remaining_credits;
}

// check if user package subscribed
function jobsearch_fjobs_pckg_is_subscribed($pckg_id = 0, $user_id = 0) {
    if ($user_id <= 0 && is_user_logged_in()) {
        $user_id = get_current_user_id();
    }
    $args = array(
        'post_type' => 'shop_order',
        'posts_per_page' => '-1',
        'post_status' => 'wc-completed',
        'order' => 'DESC',
        'orderby' => 'ID',
        'fields' => 'ids',
        'meta_query' => array(
            array(
                'key' => 'package_type',
                'value' => 'featured_jobs',
                'compare' => '=',
            ),
            array(
                'key' => 'jobsearch_order_package',
                'value' => $pckg_id,
                'compare' => '=',
            ),
            array(
                'key' => 'package_expiry_timestamp',
                'value' => strtotime(current_time('d-m-Y H:i:s')),
                'compare' => '>',
            ),
            array(
                'key' => 'jobsearch_order_user',
                'value' => $user_id,
                'compare' => '=',
            ),
        ),
    );
    $pkgs_query = new WP_Query($args);

    $pkgs_query_posts = $pkgs_query->posts;
    if (!empty($pkgs_query_posts)) {
        foreach ($pkgs_query_posts as $order_post_id) {
            $remaining_jobs = jobsearch_pckg_order_remaining_fjobs($order_post_id);
            if ($remaining_jobs > 0) {
                return $order_post_id;
            }
        }
    }
    return false;
}

// check if user package subscribed
function jobsearch_fjobs_pckg_order_is_expired($order_id = 0) {

    $order_post_id = $order_id;
    $expiry_timestamp = get_post_meta($order_post_id, 'package_expiry_timestamp', true);


    if ($expiry_timestamp <= strtotime(current_time('d-m-Y H:i:s', 1))) {
        return true;
    }

    $remaining_jobs = jobsearch_pckg_order_remaining_fjobs($order_post_id);
    if ($remaining_jobs < 1) {
        return true;
    }
    return false;
}

// get user package used jobs
function jobsearch_pckg_order_used_jobs($order_id = 0) {
    $jobs_list_count = 0;
    if ($order_id > 0) {
        $total_jobs = get_post_meta($order_id, 'num_of_jobs', true);
        $jobs_list = get_post_meta($order_id, 'jobsearch_order_jobs_list', true);

        if (!empty($jobs_list)) {
            $jobs_list_count = count(explode(',', $jobs_list));
        }
    }

    return $jobs_list_count;
}

// get user package remaining jobs
function jobsearch_pckg_order_remaining_jobs($order_id = 0) {
    $remaining_jobs = 0;
    if ($order_id > 0) {
        $total_jobs = get_post_meta($order_id, 'num_of_jobs', true);
        $used_jobs = jobsearch_pckg_order_used_jobs($order_id);

        $remaining_jobs = $total_jobs > $used_jobs ? $total_jobs - $used_jobs : 0;
    }

    return $remaining_jobs;
}

// check if user package subscribed
function jobsearch_pckg_is_subscribed($pckg_id = 0, $user_id = 0) {
    if ($user_id <= 0 && is_user_logged_in()) {
        $user_id = get_current_user_id();
    }
    $args = array(
        'post_type' => 'shop_order',
        'posts_per_page' => '-1',
        'post_status' => 'wc-completed',
        'order' => 'DESC',
        'orderby' => 'ID',
        'fields' => 'ids',
        'meta_query' => array(
            array(
                'key' => 'package_type',
                'value' => 'job',
                'compare' => '=',
            ),
            array(
                'key' => 'jobsearch_order_package',
                'value' => $pckg_id,
                'compare' => '=',
            ),
            array(
                'key' => 'package_expiry_timestamp',
                'value' => strtotime(current_time('d-m-Y H:i:s')),
                'compare' => '>',
            ),
            array(
                'key' => 'jobsearch_order_user',
                'value' => $user_id,
                'compare' => '=',
            ),
        ),
    );
    $pkgs_query = new WP_Query($args);

    $pkgs_query_posts = $pkgs_query->posts;
    if (!empty($pkgs_query_posts)) {
        foreach ($pkgs_query_posts as $order_post_id) {
            $remaining_jobs = jobsearch_pckg_order_remaining_jobs($order_post_id);
            if ($remaining_jobs > 0) {
                return $order_post_id;
            }
        }
    }
    return false;
}

// check if user package subscribed
function jobsearch_pckg_order_is_expired($order_id = 0) {

    $order_post_id = $order_id;
    $expiry_timestamp = get_post_meta($order_post_id, 'package_expiry_timestamp', true);


    if ($expiry_timestamp <= strtotime(current_time('d-m-Y H:i:s', 1))) {
        return true;
    }

    $remaining_jobs = jobsearch_pckg_order_remaining_jobs($order_post_id);
    if ($remaining_jobs < 1) {
        return true;
    }
    return false;
}

// get user package used jobs
function jobsearch_pckg_order_used_cvs($order_id = 0) {
    $cvs_list_count = 0;
    if ($order_id > 0) {
        $total_cvs = get_post_meta($order_id, 'num_of_cvs', true);
        $cvs_list = get_post_meta($order_id, 'jobsearch_order_cvs_list', true);

        if (!empty($cvs_list)) {
            $cvs_list_count = count(explode(',', $cvs_list));
        }
    }

    return $cvs_list_count;
}

// get user package remaining cvs
function jobsearch_pckg_order_remaining_cvs($order_id = 0) {
    $remaining_cvs = 0;
    if ($order_id > 0) {
        $total_cvs = get_post_meta($order_id, 'num_of_cvs', true);
        $used_cvs = jobsearch_pckg_order_used_cvs($order_id);

        $remaining_cvs = $total_cvs > $used_cvs ? $total_cvs - $used_cvs : 0;
    }

    return $remaining_cvs;
}

// check if user package subscribed
function jobsearch_cv_pckg_is_subscribed($pckg_id = 0, $user_id = 0) {
    if ($user_id <= 0 && is_user_logged_in()) {
        $user_id = get_current_user_id();
    }
    $args = array(
        'post_type' => 'shop_order',
        'posts_per_page' => '-1',
        'post_status' => 'wc-completed',
        'order' => 'DESC',
        'orderby' => 'ID',
        'fields' => 'ids',
        'meta_query' => array(
            array(
                'key' => 'package_type',
                'value' => 'cv',
                'compare' => '=',
            ),
            array(
                'key' => 'jobsearch_order_package',
                'value' => $pckg_id,
                'compare' => '=',
            ),
            array(
                'key' => 'package_expiry_timestamp',
                'value' => strtotime(current_time('d-m-Y H:i:s')),
                'compare' => '>',
            ),
            array(
                'key' => 'jobsearch_order_user',
                'value' => $user_id,
                'compare' => '=',
            ),
        ),
    );
    $pkgs_query = new WP_Query($args);

    $pkgs_query_posts = $pkgs_query->posts;
    if (!empty($pkgs_query_posts)) {
        foreach ($pkgs_query_posts as $order_post_id) {
            $remaining_cvs = jobsearch_pckg_order_remaining_cvs($order_post_id);
            if ($remaining_cvs > 0) {
                return $order_post_id;
            }
        }
    }
    return false;
}

function jobsearch_all_locs_del_callback() {
    global $wpdb;

    $wpdb->query($wpdb->prepare("DELETE $wpdb->terms FROM $wpdb->terms LEFT JOIN $wpdb->term_taxonomy ON ($wpdb->terms.term_id = $wpdb->term_taxonomy.term_id) 
    WHERE $wpdb->term_taxonomy.taxonomy = %s", 'job-location'));

    $wpdb->query($wpdb->prepare("DELETE FROM $wpdb->term_taxonomy WHERE $wpdb->term_taxonomy.taxonomy = %s", 'job-location'));
}

// check if user package subscribed
function jobsearch_employer_first_subscribed_cv_pkg($user_id = 0) {
    if ($user_id <= 0 && is_user_logged_in()) {
        $user_id = get_current_user_id();
    }
    $args = array(
        'post_type' => 'shop_order',
        'posts_per_page' => '-1',
        'post_status' => 'wc-completed',
        'order' => 'ASC',
        'orderby' => 'ID',
        'fields' => 'ids',
        'meta_query' => array(
            array(
                'key' => 'jobsearch_order_attach_with',
                'value' => 'package',
                'compare' => '=',
            ),
            array(
                'key' => 'package_type',
                'value' => 'cv',
                'compare' => '=',
            ),
            array(
                'key' => 'package_expiry_timestamp',
                'value' => strtotime(current_time('d-m-Y H:i:s')),
                'compare' => '>',
            ),
            array(
                'key' => 'jobsearch_order_user',
                'value' => $user_id,
                'compare' => '=',
            ),
        ),
    );
    $pkgs_query = new WP_Query($args);

    $pkgs_query_posts = $pkgs_query->posts;
    if (!empty($pkgs_query_posts)) {
        foreach ($pkgs_query_posts as $order_post_id) {
            $remaining_cvs = jobsearch_pckg_order_remaining_cvs($order_post_id);
            if ($remaining_cvs > 0) {
                return $order_post_id;
            }
        }
    }
    return false;
}

// check if user cv package expired
function jobsearch_cv_pckg_order_is_expired($order_id = 0) {

    $order_post_id = $order_id;
    $expiry_timestamp = get_post_meta($order_post_id, 'package_expiry_timestamp', true);


    if ($expiry_timestamp <= strtotime(current_time('d-m-Y H:i:s', 1))) {
        return true;
    }

    $remaining_cvs = jobsearch_pckg_order_remaining_cvs($order_post_id);
    if ($remaining_cvs < 1) {
        return true;
    }
    return false;
}

function jobsearch_load_employer_team_next_page() {
    $total_pages = isset($_POST['total_pages']) ? $_POST['total_pages'] : 1;
    $cur_page = isset($_POST['cur_page']) ? $_POST['cur_page'] : 1;
    $employer_id = isset($_POST['employer_id']) ? $_POST['employer_id'] : 1;
    $class_pref = isset($_POST['class_pref']) && $_POST['class_pref'] != '' ? $_POST['class_pref'] : 'jobsearch';
    $team_style = isset($_POST['team_style']) ? $_POST['team_style'] : 'default';


    $per_page_results = 3;

    $start = ($cur_page) * ($per_page_results);
    $offset = $per_page_results;

    $exfield_list = get_post_meta($employer_id, 'jobsearch_field_team_title', true);
    $exfield_list_val = get_post_meta($employer_id, 'jobsearch_field_team_description', true);
    $team_designationfield_list = get_post_meta($employer_id, 'jobsearch_field_team_designation', true);
    $team_experiencefield_list = get_post_meta($employer_id, 'jobsearch_field_team_experience', true);
    $team_imagefield_list = get_post_meta($employer_id, 'jobsearch_field_team_image', true);
    $team_facebookfield_list = get_post_meta($employer_id, 'jobsearch_field_team_facebook', true);
    $team_googlefield_list = get_post_meta($employer_id, 'jobsearch_field_team_google', true);
    $team_twitterfield_list = get_post_meta($employer_id, 'jobsearch_field_team_twitter', true);
    $team_linkedinfield_list = get_post_meta($employer_id, 'jobsearch_field_team_linkedin', true);

    $exfield_list = array_slice($exfield_list, $start, $offset);
    $exfield_list_val = array_slice($exfield_list_val, $start, $offset);
    $team_designationfield_list = array_slice($team_designationfield_list, $start, $offset);
    $team_experiencefield_list = array_slice($team_experiencefield_list, $start, $offset);
    $team_imagefield_list = array_slice($team_imagefield_list, $start, $offset);
    $team_facebookfield_list = array_slice($team_facebookfield_list, $start, $offset);
    $team_googlefield_list = array_slice($team_googlefield_list, $start, $offset);
    $team_twitterfield_list = array_slice($team_twitterfield_list, $start, $offset);
    $team_linkedinfield_list = array_slice($team_linkedinfield_list, $start, $offset);

    ob_start();

    if (is_array($exfield_list) && sizeof($exfield_list) > 0) {
        $total_team = sizeof($exfield_list);

        $rand_num_ul = rand(1000000, 99999999);

        $exfield_counter = 0;
        foreach ($exfield_list as $exfield) {
            $rand_num = rand(1000000, 99999999);

            $exfield_val = isset($exfield_list_val[$exfield_counter]) ? $exfield_list_val[$exfield_counter] : '';
            $team_designationfield_val = isset($team_designationfield_list[$exfield_counter]) ? $team_designationfield_list[$exfield_counter] : '';
            $team_experiencefield_val = isset($team_experiencefield_list[$exfield_counter]) ? $team_experiencefield_list[$exfield_counter] : '';
            $team_imagefield_val = isset($team_imagefield_list[$exfield_counter]) ? $team_imagefield_list[$exfield_counter] : '';
            $team_facebookfield_val = isset($team_facebookfield_list[$exfield_counter]) ? $team_facebookfield_list[$exfield_counter] : '';
            $team_googlefield_val = isset($team_googlefield_list[$exfield_counter]) ? $team_googlefield_list[$exfield_counter] : '';
            $team_twitterfield_val = isset($team_twitterfield_list[$exfield_counter]) ? $team_twitterfield_list[$exfield_counter] : '';
            $team_linkedinfield_val = isset($team_linkedinfield_list[$exfield_counter]) ? $team_linkedinfield_list[$exfield_counter] : '';
            ?>
            <li class="<?php echo ($class_pref) ?>-column-4 new-entries" style="display: none;">
                <script>
                    jQuery('a[id^="fancybox_notes"]').fancybox({
                        'titlePosition': 'inside',
                        'transitionIn': 'elastic',
                        'transitionOut': 'elastic',
                        'width': 400,
                        'height': 250,
                        'padding': 40,
                        'autoSize': false
                    });
                </script>
                <figure>
                    <a id="fancybox_notes<?php echo ($rand_num) ?>" href="#notes<?php echo ($rand_num) ?>" class="jobsearch-candidate-grid-thumb"><img src="<?php echo ($team_imagefield_val) ?>" alt=""> <span class="jobsearch-candidate-grid-status"></span></a>
                    <figcaption>
                        <h2><a id="fancybox_notes_txt<?php echo ($rand_num) ?>" href="#notes<?php echo ($rand_num) ?>"><?php echo ($exfield) ?></a></h2>
                        <p><?php echo ($team_designationfield_val) ?></p>
                        <?php
                        if ($team_experiencefield_val != '') {
                            echo '<span>' . sprintf(esc_html__('Experience: %s', 'wp-jobsearch'), $team_experiencefield_val) . '</span>';
                        }
                        ?>
                    </figcaption>
                </figure>

                <div id="notes<?php echo ($rand_num) ?>" style="display: none;"><?php echo ($exfield_val) ?></div>
                <?php
                if ($team_facebookfield_val != '' || $team_googlefield_val != '' || $team_twitterfield_val != '' || $team_linkedinfield_val != '') {
                    ?>
                    <ul class="jobsearch-social-icons">
                        <?php
                        if ($team_facebookfield_val != '') {
                            ?>
                            <li><a href="<?php echo ($team_facebookfield_val) ?>" data-original-title="facebook" class="jobsearch-icon jobsearch-facebook-logo"></a></li>
                            <?php
                        }
                        if ($team_googlefield_val != '') {
                            ?>
                            <li><a href="<?php echo ($team_googlefield_val) ?>" data-original-title="google-plus" class="jobsearch-icon jobsearch-google-plus-logo-button"></a></li>
                            <?php
                        }
                        if ($team_twitterfield_val != '') {
                            ?>
                            <li><a href="<?php echo ($team_twitterfield_val) ?>" data-original-title="twitter" class="jobsearch-icon jobsearch-twitter-logo"></a></li>
                            <?php
                        }
                        if ($team_linkedinfield_val != '') {
                            ?>
                            <li><a href="<?php echo ($team_linkedinfield_val) ?>" data-original-title="linkedin" class="jobsearch-icon jobsearch-linkedin-button"></a></li>
                            <?php
                        }
                        ?>
                    </ul>
                    <?php
                }
                ?>
            </li>
            <?php
            $exfield_counter++;
        }
    }

    $html = ob_get_clean();

    $html = apply_filters('careerfy_employer_team_members_view', $html, $_POST);

    echo json_encode(array('html' => $html));
    die;
}

add_action('wp_ajax_jobsearch_load_employer_team_next_page', 'jobsearch_load_employer_team_next_page');
add_action('wp_ajax_nopriv_jobsearch_load_employer_team_next_page', 'jobsearch_load_employer_team_next_page');

add_action('wp_ajax_jobsearch_send_email_to_applicant_by_employer', 'jobsearch_send_email_to_applicant_by_employer');

function jobsearch_send_email_to_applicant_by_employer() {
    $job_id = isset($_POST['_job_id']) ? $_POST['_job_id'] : '';
    $candidate_id = isset($_POST['_candidate_id']) ? $_POST['_candidate_id'] : '';
    $employer_id = isset($_POST['_employer_id']) ? $_POST['_employer_id'] : '';
    $email_subject = isset($_POST['email_subject']) ? $_POST['email_subject'] : '';
    $email_content = isset($_POST['email_content']) ? $_POST['email_content'] : '';

    $error = '0';
    if ($email_subject != '' && $error == 0) {
        $email_subject = esc_html($email_subject);
    } else {
        $error = '1';
        $msg = esc_html__('Please Enter your Name.', 'wp-jobsearch');
    }
    if ($email_content != '' && $error == 0) {
        $email_content = esc_html($email_content);
    } else {
        $error = '1';
        $msg = esc_html__('Please Enter your Name.', 'wp-jobsearch');
    }

    if ($msg == '' && $error == '0') {

        $cuser_id = jobsearch_get_candidate_user_id($candidate_id);
        $cuser_obj = get_user_by('ID', $cuser_id);

        $cuser_email = isset($cuser_obj->user_email) ? $cuser_obj->user_email : '';

        $subject = $email_subject;

        if ($job_id == 0 && $employer_id > 0) {
            $job_emp = $employer_id;
        } else {
            $job_emp = get_post_meta($job_id, 'jobsearch_field_job_posted_by', true);
        }
        $euser_id = jobsearch_get_employer_user_id($job_emp);
        $euser_obj = get_user_by('ID', $euser_id);
        $euser_email = isset($euser_obj->user_email) ? $euser_obj->user_email : '';

        $mail_from_args = array(
            'p_mail_from' => $euser_email,
        );
        add_filter('wp_mail_from', function () use ($mail_from_args) {
            extract(shortcode_atts(array(
                'p_mail_from' => '',
                            ), $mail_from_args));
            return $p_mail_from;
        });
        //
        $euser_name = isset($euser_obj->display_name) ? $euser_obj->display_name : '';
        $euser_name = apply_filters('jobsearch_user_display_name', $euser_name, $euser_obj);
        $mail_from_args = array(
            'p_mail_from' => $euser_name,
        );
        add_filter('wp_mail_from_name', function () use ($mail_from_args) {
            extract(shortcode_atts(array(
                'p_mail_from' => '',
                            ), $mail_from_args));
            return $p_mail_from;
        });
        add_filter('wp_mail_content_type', function () {
            return 'text/html';
        });

        if (wp_mail($cuser_email, $subject, $email_content)) {
            $msg = esc_html__('Mail sent successfully', 'wp-jobsearch');
            $error = '0';
        } else {
            $msg = esc_html__('Error! There is some problem.', 'wp-jobsearch');
            $error = '1';
        }
    }
    echo json_encode(array('msg' => $msg, 'error' => $error));
    wp_die();
}

add_action('wp_ajax_jobsearch_send_email_to_multi_applicants_by_employer', 'jobsearch_send_email_to_multi_applicants_by_employer');

function jobsearch_send_email_to_multi_applicants_by_employer() {
    $job_id = isset($_POST['_job_id']) ? $_POST['_job_id'] : '';
    $_candidate_ids = isset($_POST['_candidate_ids']) ? $_POST['_candidate_ids'] : '';
    $employer_id = isset($_POST['_employer_id']) ? $_POST['_employer_id'] : '';
    $email_subject = isset($_POST['email_subject']) ? $_POST['email_subject'] : '';
    $email_content = isset($_POST['email_content']) ? $_POST['email_content'] : '';

    $_candidate_ids = $_candidate_ids != '' ? explode(',', $_candidate_ids) : '';

    $error = '0';
    if ($email_subject != '' && $error == 0) {
        $email_subject = esc_html($email_subject);
    } else {
        $error = '1';
        $msg = esc_html__('Please Enter your Name.', 'wp-jobsearch');
    }
    if ($email_content != '' && $error == 0) {
        $email_content = esc_html($email_content);
    } else {
        $error = '1';
        $msg = esc_html__('Please Enter your Name.', 'wp-jobsearch');
    }

    if ($msg == '' && $error == '0') {

        if (!empty($_candidate_ids)) {

            $subject = $email_subject;

            if ($job_id == 0 && $employer_id > 0) {
                $job_emp = $employer_id;
            } else {
                $job_emp = get_post_meta($job_id, 'jobsearch_field_job_posted_by', true);
            }
            $euser_id = jobsearch_get_employer_user_id($job_emp);
            $euser_obj = get_user_by('ID', $euser_id);
            $euser_email = isset($euser_obj->user_email) ? $euser_obj->user_email : '';

            $headers = "From: " . strip_tags($euser_email) . "\r\n";
            $headers .= "Reply-To: " . strip_tags($euser_email) . "\r\n";
            $headers .= "MIME-Version: 1.0\r\n";
            $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";

            foreach ($_candidate_ids as $candidate_id) {
                $cuser_id = jobsearch_get_candidate_user_id($candidate_id);
                $cuser_obj = get_user_by('ID', $cuser_id);

                $cuser_email = isset($cuser_obj->user_email) ? $cuser_obj->user_email : '';
                $rec_emails = $cuser_email;
                wp_mail($rec_emails, $subject, $email_content, $headers);
            }

            $msg = esc_html__('Mail sent successfully', 'wp-jobsearch');
            $error = '0';
        } else {
            $msg = esc_html__('Error! There is some problem.', 'wp-jobsearch');
            $error = '1';
        }
    }
    echo json_encode(array('msg' => $msg, 'error' => $error));
    wp_die();
}

add_action('wp_ajax_jobsearch_applicant_to_undoreject_by_employer', 'jobsearch_applicant_to_undoreject_by_employer');

function jobsearch_applicant_to_undoreject_by_employer() {

    $job_id = isset($_POST['_job_id']) ? $_POST['_job_id'] : '';
    $candidate_id = isset($_POST['_candidate_id']) ? $_POST['_candidate_id'] : '';

    if ($job_id > 0 && $candidate_id > 0) {
        $job_reject_int_list = get_post_meta($job_id, '_job_reject_interview_list', true);

        $job_reject_int_list = $job_reject_int_list != '' ? explode(',', $job_reject_int_list) : array();
        if (empty($job_reject_int_list)) {
            $job_reject_int_list = array();
        }
        if (in_array($candidate_id, $job_reject_int_list) && ($key = array_search($candidate_id, $job_reject_int_list)) !== false) {
            unset($job_reject_int_list[$key]);
            $job_reject_int_list = implode(',', $job_reject_int_list);
            update_post_meta($job_id, '_job_reject_interview_list', $job_reject_int_list);

            //
            $job_applicants_list = get_post_meta($job_id, 'jobsearch_job_applicants_list', true);
            $job_applicants_list = $job_applicants_list != '' ? explode(',', $job_applicants_list) : array();
            if (empty($job_applicants_list)) {
                $job_applicants_list = array();
            }
            if (!in_array($candidate_id, $job_applicants_list)) {
                $job_applicants_list[] = $candidate_id;
                $job_applicants_list = implode(',', $job_applicants_list);
                update_post_meta($job_id, 'jobsearch_job_applicants_list', $job_applicants_list);
            }
            //

            $msg = esc_html__('Undo Rejection', 'wp-jobsearch');
            $error = '0';
            echo json_encode(array('msg' => $msg, 'error' => $error));
            wp_die();
        }
    }
    $msg = '';
    $error = '1';
    echo json_encode(array('msg' => $msg, 'error' => $error));
    wp_die();
}

add_action('wp_ajax_jobsearch_applicant_to_reject_by_employer', 'jobsearch_applicant_to_reject_by_employer');

function jobsearch_applicant_to_reject_by_employer() {

    $job_id = isset($_POST['_job_id']) ? $_POST['_job_id'] : '';
    $candidate_id = isset($_POST['_candidate_id']) ? $_POST['_candidate_id'] : '';

    if ($job_id > 0 && $candidate_id > 0) {


	    // sterge din cadidati ok
	    $job_short_int_list = get_post_meta($job_id, '_job_short_interview_list', true);
	    $job_short_int_list = $job_short_int_list != '' ? explode(',', $job_short_int_list) : '';
	    if (empty($job_short_int_list)) {
		    $job_short_int_list = array();
	    }
	    if (($key = array_search($candidate_id, $job_short_int_list)) !== false) {
		    unset($job_short_int_list[$key]);

		    $job_short_int_list = implode(',', $job_short_int_list);
		    update_post_meta($job_id, '_job_short_interview_list', $job_short_int_list);
	    }


	    // sterge din rezerve
	    $job_reserved_int_list = get_post_meta($job_id, '_job_reserved_interview_list', true);
	    $job_reserved_int_list = $job_reserved_int_list != '' ? explode(',', $job_reserved_int_list) : '';
	    if (empty($job_reserved_int_list)) {
		    $job_reserved_int_list = array();
	    }
	    if (($key = array_search($candidate_id, $job_reserved_int_list)) !== false) {
		    unset($job_reserved_int_list[$key]);

		    $job_reserved_int_list = implode(',', $job_reserved_int_list);
		    update_post_meta($job_id, '_job_reserved_interview_list', $job_reserved_int_list);
	    }



	    // sterge din toata lista
	    $job_applicants_list = get_post_meta($job_id, 'jobsearch_job_applicants_list', true);
	    $job_applicants_list = $job_applicants_list != '' ? explode(',', $job_applicants_list) : array();
	    if (($key = array_search($candidate_id, $job_applicants_list)) !== false) {
		    unset($job_applicants_list[$key]);

		    $job_applicants_list = implode(',', $job_applicants_list);
		    update_post_meta($job_id, 'jobsearch_job_applicants_list', $job_applicants_list);
		    jobsearch_remove_user_meta_list($job_id, 'jobsearch-user-jobs-applied-list', $user_id);
	    }




        $job_reject_int_list = get_post_meta($job_id, '_job_reject_interview_list', true);
	    $user_id = jobsearch_get_candidate_user_id($candidate_id);

        $job_reject_int_list = $job_reject_int_list != '' ? explode(',', $job_reject_int_list) : array();
        if (empty($job_reject_int_list)) {
            $job_reject_int_list = array();
        }


        if (!in_array($candidate_id, $job_reject_int_list)) {
            $job_reject_int_list[] = $candidate_id;

            $job_reject_int_list = implode(',', $job_reject_int_list);
            update_post_meta($job_id, '_job_reject_interview_list', $job_reject_int_list);

            $msg = esc_html__('Rejected', 'wp-jobsearch');
            $error = '0';
            echo json_encode(array('msg' => $msg, 'error' => $error));
            wp_die();
        }
    }
    $msg = '';
    $error = '1';
    echo json_encode(array('msg' => $msg, 'error' => $error));
    wp_die();
}

add_action('wp_ajax_jobsearch_delete_applicant_by_employer', 'jobsearch_delete_applicant_by_employer');

function jobsearch_delete_applicant_by_employer() {

    $job_id = isset($_POST['_job_id']) ? $_POST['_job_id'] : '';
    $candidate_id = isset($_POST['_candidate_id']) ? $_POST['_candidate_id'] : '';

    if ($job_id > 0 && $candidate_id > 0) {

        $user_id = jobsearch_get_candidate_user_id($candidate_id);

        $job_applicants_list = get_post_meta($job_id, 'jobsearch_job_applicants_list', true);
        $job_applicants_list = $job_applicants_list != '' ? explode(',', $job_applicants_list) : array();

        if (jobsearch_employer_not_allow_to_mod()) {
            $msg = esc_html__('You are not allowed to delete this.', 'wp-jobsearch');
            $error = '1';
            echo json_encode(array('msg' => $msg, 'error' => $error));
            die;
        }


        // aici scoatem check - ul
//        if (!empty($job_applicants_list)) {

            // sterge din cadidati ok
            $job_short_int_list = get_post_meta($job_id, '_job_short_interview_list', true);
            $job_short_int_list = $job_short_int_list != '' ? explode(',', $job_short_int_list) : '';
            if (empty($job_short_int_list)) {
                $job_short_int_list = array();
            }
            if (($key = array_search($candidate_id, $job_short_int_list)) !== false) {
                unset($job_short_int_list[$key]);

                $job_short_int_list = implode(',', $job_short_int_list);
                update_post_meta($job_id, '_job_short_interview_list', $job_short_int_list);
            }


            // sterge din cadidati respinsi
            $job_reject_int_list = get_post_meta($job_id, '_job_reject_interview_list', true);
            $job_reject_int_list = $job_reject_int_list != '' ? explode(',', $job_reject_int_list) : '';
            if (empty($job_reject_int_list)) {
                $job_reject_int_list = array();
            }
            if (($key = array_search($candidate_id, $job_reject_int_list)) !== false) {
                unset($job_reject_int_list[$key]);

                $job_reject_int_list = implode(',', $job_reject_int_list);
                update_post_meta($job_id, '_job_reject_interview_list', $job_reject_int_list);
            }



            // sterge din rezerve
	        $job_reserved_int_list = get_post_meta($job_id, '_job_reserved_interview_list', true);
	        $job_reserved_int_list = $job_reserved_int_list != '' ? explode(',', $job_reserved_int_list) : '';
	        if (empty($job_reserved_int_list)) {
		        $job_reserved_int_list = array();
	        }
	        if (($key = array_search($candidate_id, $job_reserved_int_list)) !== false) {
		        unset($job_reserved_int_list[$key]);

		        $job_reserved_int_list = implode(',', $job_reserved_int_list);
		        update_post_meta($job_id, '_job_reserved_interview_list', $job_reserved_int_list);
	        }



            // sterge din toata lista
            if (($key = array_search($candidate_id, $job_applicants_list)) !== false) {
                unset($job_applicants_list[$key]);

                $job_applicants_list = implode(',', $job_applicants_list);
                update_post_meta($job_id, 'jobsearch_job_applicants_list', $job_applicants_list);
                jobsearch_remove_user_meta_list($job_id, 'jobsearch-user-jobs-applied-list', $user_id);
            }


//        }

        $msg = esc_html__('Deleted', 'wp-jobsearch');
        $error = '0';
        echo json_encode(array('msg' => $msg, 'error' => $error));
        wp_die();
    }
    $msg = '';
    $error = '1';
    echo json_encode(array('msg' => $msg, 'error' => $error));
    wp_die();
}


add_action('wp_ajax_jobsearch_blacklist_candidate', 'jobsearch_blacklist_candidate');

function jobsearch_blacklist_candidate() {

    $job_id = isset($_POST['_job_id']) ? $_POST['_job_id'] : '';
    $candidate_id = isset($_POST['_candidate_id']) ? $_POST['_candidate_id'] : '';
    $employer_id = isset($_POST['_employer_id']) ? $_POST['_employer_id'] : '';

    if ($job_id > 0 && $candidate_id > 0) {

        $user_id = jobsearch_get_candidate_user_id($candidate_id);

        $job_applicants_list = get_post_meta($job_id, 'jobsearch_job_applicants_list', true);
        $job_applicants_list = $job_applicants_list != '' ? explode(',', $job_applicants_list) : array();

        if (jobsearch_employer_not_allow_to_mod()) {
            $msg = esc_html__('You are not allowed to delete this.', 'wp-jobsearch');
            $error = '1';
            echo json_encode(array('msg' => $msg, 'error' => $error));
            die;
        }

            $job_short_int_list = get_post_meta($job_id, '_job_short_interview_list', true);
            $job_short_int_list = $job_short_int_list != '' ? explode(',', $job_short_int_list) : '';
            if (empty($job_short_int_list)) {
                $job_short_int_list = array();
            }
            if (($key = array_search($candidate_id, $job_short_int_list)) !== false) {
                unset($job_short_int_list[$key]);

                $job_short_int_list = implode(',', $job_short_int_list);
                update_post_meta($job_id, '_job_short_interview_list', $job_short_int_list);
            }


            // sterge din cadidati respinsi
            $job_reject_int_list = get_post_meta($job_id, '_job_reject_interview_list', true);
            $job_reject_int_list = $job_reject_int_list != '' ? explode(',', $job_reject_int_list) : '';
            if (empty($job_reject_int_list)) {
                $job_reject_int_list = array();
            }
            if (($key = array_search($candidate_id, $job_reject_int_list)) !== false) {
                unset($job_reject_int_list[$key]);

                $job_reject_int_list = implode(',', $job_reject_int_list);
                update_post_meta($job_id, '_job_reject_interview_list', $job_reject_int_list);
            }



            // sterge din rezerve
	        $job_reserved_int_list = get_post_meta($job_id, '_job_reserved_interview_list', true);
	        $job_reserved_int_list = $job_reserved_int_list != '' ? explode(',', $job_reserved_int_list) : '';
	        if (empty($job_reserved_int_list)) {
		        $job_reserved_int_list = array();
	        }
	        if (($key = array_search($candidate_id, $job_reserved_int_list)) !== false) {
		        unset($job_reserved_int_list[$key]);

		        $job_reserved_int_list = implode(',', $job_reserved_int_list);
		        update_post_meta($job_id, '_job_reserved_interview_list', $job_reserved_int_list);
	        }



            // sterge din toata lista
            if (($key = array_search($candidate_id, $job_applicants_list)) !== false) {
                unset($job_applicants_list[$key]);

                $job_applicants_list = implode(',', $job_applicants_list);
                update_post_meta($job_id, 'jobsearch_job_applicants_list', $job_applicants_list);
                jobsearch_remove_user_meta_list($job_id, 'jobsearch-user-jobs-applied-list', $user_id);
            }



        $job_black_int_list = get_post_meta($employer_id, '_job_black_list', true);
   //     $user_id = jobsearch_get_candidate_user_id($candidate_id);

        $job_black_int_list = $job_black_int_list != '' ? explode(',', $job_black_int_list) : array();
        if (empty($job_black_int_list)) {
            $job_black_int_list = array();
        }


        if (!in_array($candidate_id, $job_black_int_list)) {

            $job_black_int_list[] = $candidate_id;

            $job_black_int_list = implode(',', $job_black_int_list);
            update_post_meta($employer_id, '_job_black_list', $job_black_int_list);

            $msg = esc_html__('Blacklisted', 'wp-jobsearch');
            $error = '0';
            echo json_encode(array('msg' => $msg, 'error' => $error));
            wp_die();
        }

        $msg = esc_html__('Deleted', 'wp-jobsearch');
        $error = '0';
        echo json_encode(array('msg' => $msg, 'error' => $error));
        wp_die();
    }



    $msg = '';
    $error = '1';
    echo json_encode(array('msg' => $msg, 'error' => $error));
    wp_die();
}


// adauga candidat ok

add_action('wp_ajax_jobsearch_applicant_to_interview_by_employer', 'jobsearch_applicant_to_interview_by_employer');

function jobsearch_applicant_to_interview_by_employer() {

    $job_id = isset($_POST['_job_id']) ? $_POST['_job_id'] : '';
    $candidate_id = isset($_POST['_candidate_id']) ? $_POST['_candidate_id'] : '';
	$user_id = jobsearch_get_candidate_user_id($candidate_id);

    if ($job_id > 0 && $candidate_id > 0) {

        $c_user = wp_get_current_user();

        $job_short_int_list = get_post_meta($job_id, '_job_short_interview_list', true);



	    // sterge din lista de aplicatii
	    $job_applicants_list = get_post_meta($job_id, 'jobsearch_job_applicants_list', true);
	    $job_applicants_list = $job_applicants_list != '' ? explode(',', $job_applicants_list) : array();
	    if (($key = array_search($candidate_id, $job_applicants_list)) !== false) {
		    unset($job_applicants_list[$key]);
		    $job_applicants_list = implode(',', $job_applicants_list);
		    update_post_meta($job_id, 'jobsearch_job_applicants_list', $job_applicants_list);
	    }


	    // sterge din lista de respinsi
	    $job_reject_int_list = get_post_meta($job_id, '_job_reject_interview_list', true);
	    $job_reject_int_list = $job_reject_int_list != '' ? explode(',', $job_reject_int_list) : array();
	    if (($key = array_search($candidate_id, $job_reject_int_list)) !== false) {
		    unset($job_reject_int_list[$key]);
		    $job_reject_int_list = implode(',', $job_reject_int_list);
		    update_post_meta($job_id, '_job_reject_interview_list', $job_reject_int_list);
	    }

	    // sterge din lista de rezerve
	    $job_reserved_int_list = get_post_meta($job_id, '_job_reserved_interview_list', true);
	    $job_reserved_int_list = $job_reserved_int_list != '' ? explode(',', $job_reserved_int_list) : '';
	    if (empty($job_reserved_int_list)) {
		    $job_reserved_int_list = array();
	    }
	    if (($key = array_search($candidate_id, $job_reserved_int_list)) !== false) {
		    unset($job_reserved_int_list[$key]);
		    $job_reserved_int_list = implode(',', $job_reserved_int_list);
		    update_post_meta($job_id, '_job_reserved_interview_list', $job_reserved_int_list);
	    }


        if ($job_short_int_list != '') {
            $job_short_int_list = explode(',', $job_short_int_list);
            if (!in_array($candidate_id, $job_short_int_list)) {
                $job_short_int_list[] = $candidate_id;
                $job_short_int_list = implode(',', $job_short_int_list);
                update_post_meta($job_id, '_job_short_interview_list', $job_short_int_list);
                do_action('jobsearch_user_shortlist_for_interview', $c_user, $job_id, $candidate_id);
                $msg = esc_html__('Shortlisted', 'wp-jobsearch');
                $error = '0';
                echo json_encode(array('msg' => $msg, 'error' => $error));
                wp_die();
            }
        } else {
            $job_short_int_list = array($candidate_id);
            $job_short_int_list = implode(',', $job_short_int_list);
            update_post_meta($job_id, '_job_short_interview_list', $job_short_int_list);
            do_action('jobsearch_user_shortlist_for_interview', $c_user, $job_id, $candidate_id);

	        // sterge din toata lista
	        $job_applicants_list = get_post_meta($job_id, 'jobsearch_job_applicants_list', true);
	        $job_applicants_list = $job_applicants_list != '' ? explode(',', $job_applicants_list) : array();

            $msg = esc_html__('Shortlisted', 'wp-jobsearch');
            $error = '0';
            echo json_encode(array('msg' => $msg, 'error' => $error));
            wp_die();
        }
    }

    $msg = '';
    $error = '1';
    echo json_encode(array('msg' => $msg, 'error' => $error));
    wp_die();
}


// adauga candidat ca rezerva


add_action('wp_ajax_jobsearch_applicant_to_reserved_by_employer', 'jobsearch_applicant_to_reserved_by_employer');

function jobsearch_applicant_to_reserved_by_employer() {

	$job_id = isset($_POST['_job_id']) ? $_POST['_job_id'] : '';
	$candidate_id = isset($_POST['_candidate_id']) ? $_POST['_candidate_id'] : '';

	if ($job_id > 0 && $candidate_id > 0) {
		$job_reserved_int_list = get_post_meta($job_id, '_job_reserved_interview_list', true);

		$job_reserved_int_list = $job_reserved_int_list != '' ? explode(',', $job_reserved_int_list) : array();
		if (empty($job_reserved_int_list)) {
			$job_reserved_int_list = array();
		}


		// check if candidati ok
		$job_short_int_list = get_post_meta($job_id, '_job_short_interview_list', true);
		$job_short_int_list = $job_short_int_list != '' ? explode(',', $job_short_int_list) : array();
		if (($key = array_search($candidate_id, $job_short_int_list)) !== false) {
			unset($job_short_int_list[$key]);
			$job_short_int_list = implode(',', $job_short_int_list);
			update_post_meta($job_id, '_job_short_interview_list', $job_short_int_list);
		}


		// check if lista de aplicatii
		$job_applicants_list = get_post_meta($job_id, 'jobsearch_job_applicants_list', true);
		$job_applicants_list = $job_applicants_list != '' ? explode(',', $job_applicants_list) : array();
		if (($key = array_search($candidate_id, $job_applicants_list)) !== false) {
			unset($job_applicants_list[$key]);
			$job_applicants_list = implode(',', $job_applicants_list);
			update_post_meta($job_id, 'jobsearch_job_applicants_list', $job_applicants_list);
		}


		// check if is reject and clear it
		$job_reject_int_list = get_post_meta($job_id, '_job_reject_interview_list', true);
		$job_reject_int_list = $job_reject_int_list != '' ? explode(',', $job_reject_int_list) : array();
		if (($key = array_search($candidate_id, $job_reject_int_list)) !== false) {
			unset($job_reject_int_list[$key]);
			$job_reject_int_list = implode(',', $job_reject_int_list);
			update_post_meta($job_id, '_job_reject_interview_list', $job_reject_int_list);
		}


		if (!in_array($candidate_id, $job_reserved_int_list)) {
			$job_reserved_int_list[] = $candidate_id;

			$job_reserved_int_list = implode(',', $job_reserved_int_list);
			update_post_meta($job_id, '_job_reserved_interview_list', $job_reserved_int_list);



			$msg = esc_html__('Reserved', 'wp-jobsearch');
			$error = '0';
			echo json_encode(array('msg' => $msg, 'error' => $error));
			wp_die();
		}
	}
	$msg = '';
	$error = '1';
	echo json_encode(array('msg' => $msg, 'error' => $error));
	wp_die();
}



//multi apli cadidates

add_action('wp_ajax_jobsearch_multi_apps_to_interview_by_employer', 'jobsearch_multi_apps_to_interview_by_employer');

function jobsearch_multi_apps_to_interview_by_employer() {

    $job_id = isset($_POST['_job_id']) ? $_POST['_job_id'] : '';
    $_candidate_ids = isset($_POST['_candidate_ids']) ? $_POST['_candidate_ids'] : '';

    $_candidate_ids = $_candidate_ids != '' ? explode(',', $_candidate_ids) : '';
    if (!empty($_candidate_ids) && $job_id > 0) {
        $c_user = wp_get_current_user();
        foreach ($_candidate_ids as $candidate_id) {
            $job_short_int_list = get_post_meta($job_id, '_job_short_interview_list', true);
            $job_short_int_list = $job_short_int_list != '' ? explode(',', $job_short_int_list) : array();
            if (!in_array($candidate_id, $job_short_int_list)) {
                $job_short_int_list[] = $candidate_id;

                $job_short_int_list = implode(',', $job_short_int_list);
                update_post_meta($job_id, '_job_short_interview_list', $job_short_int_list);
                do_action('jobsearch_user_shortlist_for_interview', $c_user, $job_id, $candidate_id);
            }
        }
        $msg = esc_html__('Shortlisting', 'wp-jobsearch');
        $error = '0';
        echo json_encode(array('msg' => $msg, 'error' => $error));
        wp_die();
    }
    $msg = '';
    $error = '1';
    echo json_encode(array('msg' => $msg, 'error' => $error));
    wp_die();
}



//multi app reject

add_action('wp_ajax_jobsearch_multi_apps_to_reject_by_employer', 'jobsearch_multi_apps_to_reject_by_employer');

function jobsearch_multi_apps_to_reject_by_employer() {

    $job_id = isset($_POST['_job_id']) ? $_POST['_job_id'] : '';
    $_candidate_ids = isset($_POST['_candidate_ids']) ? $_POST['_candidate_ids'] : '';

    $_candidate_ids = $_candidate_ids != '' ? explode(',', $_candidate_ids) : '';

    //
    $job_applicants_list = get_post_meta($job_id, 'jobsearch_job_applicants_list', true);
    $job_applicants_list = $job_applicants_list != '' ? explode(',', $job_applicants_list) : array();
    //

    $job_short_int_list = get_post_meta($job_id, '_job_short_interview_list', true);
    $job_short_int_list = $job_short_int_list != '' ? explode(',', $job_short_int_list) : array();


	$job_reserved_int_list = get_post_meta($job_id, '_job_reserved_interview_list', true);
	$job_reserved_int_list = $job_reserved_int_list != '' ? explode(',', $job_reserved_int_list) : array();

    if (!empty($_candidate_ids) && $job_id > 0) {
        foreach ($_candidate_ids as $candidate_id) {
            $job_reject_int_list = get_post_meta($job_id, '_job_reject_interview_list', true);
            $job_reject_int_list = $job_reject_int_list != '' ? explode(',', $job_reject_int_list) : array();
            if (!in_array($candidate_id, $job_reject_int_list)) {
                $job_reject_int_list[] = $candidate_id;

                $job_reject_int_list = implode(',', $job_reject_int_list);
                update_post_meta($job_id, '_job_reject_interview_list', $job_reject_int_list);

                //
                if (($key = array_search($candidate_id, $job_applicants_list)) !== false) {
                    unset($job_applicants_list[$key]);
                    $job_applicants_list = implode(',', $job_applicants_list);
                    update_post_meta($job_id, 'jobsearch_job_applicants_list', $job_applicants_list);
                }
                //

	            if (($key = array_search($candidate_id, $job_reserved_int_list)) !== false) {
		            unset($job_reserved_int_list[$key]);
		            $job_reserved_int_list = implode(',', $job_reserved_int_list);
		            update_post_meta($job_id, '_job_reserved_interview_list', $job_reserved_int_list);
	            }

                //
                if (($key = array_search($candidate_id, $job_short_int_list)) !== false) {
                    unset($job_short_int_list[$key]);
                    $job_short_int_list = implode(',', $job_short_int_list);
                    update_post_meta($job_id, '_job_short_interview_list', $job_short_int_list);
                }
                //
            }
        }
        $msg = esc_html__('Rejecting', 'wp-jobsearch');
        $error = '0';
        echo json_encode(array('msg' => $msg, 'error' => $error));
        wp_die();
    }
    $msg = '';
    $error = '1';
    echo json_encode(array('msg' => $msg, 'error' => $error));
    wp_die();
}

add_action('wp_ajax_jobsearch_job_filled_by_employer', 'jobsearch_job_filled_by_employer');

function jobsearch_job_filled_by_employer() {
    $job_id = isset($_POST['_job_id']) ? $_POST['_job_id'] : '';

    if ($job_id > 0) {
        $user = wp_get_current_user();
        $user_id = $user->ID;

        if (jobsearch_user_isemp_member($user_id)) {
            $employer_id = jobsearch_user_isemp_member($user_id);
        } else {
            $employer_id = jobsearch_get_user_employer_id($user_id);
        }

        $job_emp_id = get_post_meta($job_id, 'jobsearch_field_job_posted_by', true);

        if ($employer_id == $job_emp_id) {
            update_post_meta($job_id, 'jobsearch_field_job_filled', 'on');
            $msg = esc_html__('(Filled)', 'wp-jobsearch');
            $error = '0';
            echo json_encode(array('msg' => $msg, 'error' => $error));
            wp_die();
        }
    }
    $msg = '';
    $error = '1';
    echo json_encode(array('msg' => $msg, 'error' => $error));
    wp_die();
}

if (!function_exists('jobsearch_employer_info_encoding_callback')) {

    function jobsearch_employer_info_encoding_callback($emp_data = '') {
        global $jobsearch_plugin_options;

        $show_info_flag = true;
        $show_info_for = isset($jobsearch_plugin_options['emp-sensinfo-for']) ? $jobsearch_plugin_options['emp-sensinfo-for'] : '';

        if ($show_info_for == 'public') {
            $show_info_flag = true;
        } else if ($show_info_for == 'for_login') {
            if (is_user_logged_in()) {
                $show_info_flag = true;
            } else {
                $show_info_flag = false;
            }
        } else if ($show_info_for == 'emp_cand') {
            if (is_user_logged_in()) {
                $user_id = get_current_user_id();
                $user_is_candidate = jobsearch_user_is_candidate($user_id);
                $user_is_employer = jobsearch_user_is_employer($user_id);
                if ($user_is_candidate || $user_is_employer) {
                    $show_info_flag = true;
                } else {
                    $show_info_flag = false;
                }
            } else {
                $show_info_flag = false;
            }
        } else if ($show_info_for == 'emp_only') {
            if (is_user_logged_in()) {
                $user_id = get_current_user_id();
                $user_is_employer = jobsearch_user_is_employer($user_id);
                if ($user_is_employer) {
                    $show_info_flag = true;
                } else {
                    $show_info_flag = false;
                }
            } else {
                $show_info_flag = false;
            }
        } else if ($show_info_for == 'cand_only') {
            if (is_user_logged_in()) {
                $user_id = get_current_user_id();
                $user_is_candidate = jobsearch_user_is_candidate($user_id);
                if ($user_is_candidate) {
                    $show_info_flag = true;
                } else {
                    $show_info_flag = false;
                }
            } else {
                $show_info_flag = false;
            }
        }
        
        if (is_user_logged_in()) {
            $cur_user_obj = wp_get_current_user();
            if (in_array('administrator', (array) $cur_user_obj->roles)) {
                $show_info_flag = true;
            }
        }

        if ($show_info_flag == true) {
            return $emp_data;
        } else {
            return 'xxx-xxx-xxx';
        }
    }

    add_filter('jobsearch_employer_info_encoding', 'jobsearch_employer_info_encoding_callback', 10, 1);
}

add_action('wp_ajax_jobsearch_remove_emp_resmue_shlist_from_list', 'jobsearch_remove_empdash_resmue_shlist_from_list');

function jobsearch_remove_empdash_resmue_shlist_from_list() {
    $candidate_id = isset($_POST['_cand_id']) ? $_POST['_cand_id'] : '';

    $user_id = get_current_user_id();
    $user_obj = get_user_by('ID', $user_id);

    if (jobsearch_user_isemp_member($user_id)) {
        $employer_id = jobsearch_user_isemp_member($user_id);
    } else {
        $employer_id = jobsearch_get_user_employer_id($user_id);
    }

    $employer_resumes_list = get_post_meta($employer_id, 'jobsearch_candidates_list', true);
    if ($employer_resumes_list != '') {
        $employer_resumes_list = explode(',', $employer_resumes_list);

        if (!empty($employer_resumes_list) && in_array($candidate_id, $employer_resumes_list)) {
            $capp_key = array_search($candidate_id, $employer_resumes_list);
            unset($employer_resumes_list[$capp_key]);
            $employer_resumes_list = implode(',', $employer_resumes_list);
            update_post_meta($employer_id, 'jobsearch_candidates_list', $employer_resumes_list);
        }
    }
    echo json_encode(array('msg' => 'done'));
    die;
}

add_action('jobsearch_emp_listin_sh_after_jobs_found', 'jobsearch_emp_listin_totalemps_found_html', 10, 3);
add_filter('jobsearch_emp_listin_top_jobfounds_html', 'jobsearch_emp_listin_top_jobfounds_html', 12, 4);
add_filter('jobsearch_emp_listin_before_top_jobfounds_html', 'jobsearch_emp_listin_before_top_jobfounds_html', 12, 4);
add_filter('jobsearch_emp_listin_after_sort_orders_html', 'jobsearch_emp_listin_after_sort_orders_html', 12, 4);

function jobsearch_emp_listin_totalemps_found_html($job_totnum, $employer_short_counter, $atts) {

    $counts_on = true;
    if (isset($atts['display_per_page']) && $atts['display_per_page'] == 'no') {
        $counts_on = false;
    }
    if ($counts_on) {
        $per_page = isset($atts['employer_per_page']) && absint($atts['employer_per_page']) > 0 ? $atts['employer_per_page'] : 0;
        if (isset($_REQUEST['per-page']) && $_REQUEST['per-page'] > 1) {
            $per_page = $_REQUEST['per-page'];
        }
        if ($per_page > 1) {
            $page_num = isset($_REQUEST['employer_page']) && $_REQUEST['employer_page'] > 1 ? $_REQUEST['employer_page'] : 1;
            $start_frm = $page_num > 1 ? (($page_num - 1) * $per_page) : 1;
            $offset = $page_num > 1 ? ($page_num * $per_page) : $per_page;

            $offset = $offset > $job_totnum ? $job_totnum : $offset;

            $strt_toend_disp = absint($job_totnum) > 0 ? ($start_frm > 1 ? ($start_frm + 1) : $start_frm) . ' - ' . $offset : '0';
            ?>
            <div class="displayed-here"><?php printf(esc_html__('Displayed Here : %s Employers', 'wp-jobsearch'), $strt_toend_disp) ?></div>
            <?php
        } else {
            $per_page = isset($atts['employer_per_page']) && absint($atts['employer_per_page']) > 0 ? $atts['employer_per_page'] : $job_totnum;
            $per_page = $per_page > $job_totnum ? $job_totnum : $per_page;

            $strt_toend_disp = absint($job_totnum) > 0 ? '1 - ' . $per_page : '0';
            ?>
            <div class="displayed-here"><?php printf(esc_html__('Displayed Here : %s Employers', 'wp-jobsearch'), $strt_toend_disp) ?></div>
            <?php
        }
    }
}

function jobsearch_emp_listin_top_jobfounds_html($html, $job_totnum, $employer_short_counter, $atts) {
    $counts_on = true;
    if (isset($atts['display_per_page']) && $atts['display_per_page'] == 'no') {
        $counts_on = false;
    }
    if ($counts_on) {
        $html = '';
    }
    return $html;
}

function jobsearch_emp_listin_before_top_jobfounds_html($html, $job_totnum, $employer_short_counter, $atts) {
    $counts_on = true;
    if (isset($atts['display_per_page']) && $atts['display_per_page'] == 'no') {
        $counts_on = false;
    }
    if ($counts_on) {
        ob_start();
        ?>
        <div class="jobsearch-filterable jobsearch-filter-sortable jobsearch-topfound-title">
            <h2>
                <?php
                echo absint($job_totnum) . '&nbsp;';
                if ($job_totnum == 1) {
                    echo esc_html__('Employer Found', 'wp-jobsearch');
                } else {
                    echo esc_html__('Employers Found', 'wp-jobsearch');
                }
                do_action('jobsearch_emp_listin_sh_after_jobs_found', $job_totnum, $employer_short_counter, $atts);
                ?>
            </h2>
        </div>
        <?php
        echo '<div class="jobsearch-topsort-holder">';
        $html = ob_get_clean();
    }
    return $html;
}

function jobsearch_emp_listin_after_sort_orders_html($html, $job_totnum, $employer_short_counter, $atts) {
    $counts_on = true;
    if (isset($atts['display_per_page']) && $atts['display_per_page'] == 'no') {
        $counts_on = false;
    }
    if ($counts_on) {
        $html = '</div>';
    }
    return $html;
}

add_action('wp_ajax_jobsearch_employer_ading_member_account', 'jobsearch_addingemp_accmemb_ajax_callback');

function jobsearch_addingemp_accmemb_ajax_callback() {
    $first_name = isset($_POST['u_firstname']) ? $_POST['u_firstname'] : '';
    $last_name = isset($_POST['u_lastname']) ? $_POST['u_lastname'] : '';
    $user_name = isset($_POST['u_username']) ? $_POST['u_username'] : '';
    $user_email = isset($_POST['u_emailadres']) ? $_POST['u_emailadres'] : '';
    $u_pass = isset($_POST['u_password']) ? $_POST['u_password'] : '';
    $conf_pass = isset($_POST['u_confpass']) ? $_POST['u_confpass'] : '';
    $mem_perms = isset($_POST['u_memb_perms']) ? $_POST['u_memb_perms'] : '';

    //
    if (isset($_POST['cus_employer_id'])) {
        $emp_user_id = $_POST['cus_employer_id'];
    } else {
        $emp_user_id = get_current_user_id();
    }
    $employer_id = jobsearch_get_user_employer_id($emp_user_id);
    if ($employer_id > 0) {
        //
        $error = 0;


        if ($user_email != '' && $error == 0 && filter_var($user_email, FILTER_VALIDATE_EMAIL)) {
            $user_email = esc_html($user_email);
        } else {
            $error = 1;
            $msg = esc_html__('Please Enter a valid email.', 'wp-jobsearch');
        }

        if ($u_pass != '' && $error == 0) {
            $u_pass = esc_html($u_pass);
        } else {
            $error = 1;
            $msg = esc_html__('Please enter value for the password field.', 'wp-jobsearch');
        }

        if ($conf_pass != '' && $error == 0) {
            $conf_pass = esc_html($conf_pass);
        } else {
            $error = 1;
            $msg = esc_html__('Please enter value for the confirm password field.', 'wp-jobsearch');
        }

        if ($u_pass != $conf_pass && $error == 0) {
            $error = 1;
            $msg = esc_html__('Confirm password does not match.', 'wp-jobsearch');
        }

        if ($error == 1) {
            echo json_encode(array('error' => '1', 'msg' => $msg));
            die;
        }

        $user_login = $user_name;
        if ($user_login == '') {
            $email_parts = explode("@", $user_email);
            $user_login = isset($email_parts[0]) ? $email_parts[0] : '';
            if ($user_login != '' && username_exists($user_login)) {
                $user_login .= '_' . rand(10000, 99999);
            }
        }
        if ($user_login == '') {
            $user_login = 'user_' . rand(10000, 99999);
            $user_email = 'user_' . rand(10000, 99999) . '@example.com';
        }

        $user_pass = $u_pass;

        $create_user = wp_create_user($user_login, $user_pass, $user_email);

        if (is_wp_error($create_user)) {

            $registration_error_messages = $create_user->errors;

            $display_errors = '';
            foreach ($registration_error_messages as $error) {
                $display_errors .= $error[0];
            }

            echo json_encode(array('error' => '1', 'msg' => $display_errors));
            die;
        } else {
            $user_id = $create_user;
            wp_update_user(array('ID' => $user_id, 'role' => 'subscriber'));
            if ($first_name != '') {
                $disply_name = $last_name != '' ? $first_name . ' ' . $last_name : $first_name;
                $user_def_array = array(
                    'ID' => $user_id,
                    'first_name' => $first_name,
                    'last_name' => $last_name,
                    'display_name' => $disply_name,
                );
                wp_update_user($user_def_array);
            }
            $candidate_id = jobsearch_get_user_candidate_id($user_id);
            if ($candidate_id > 0) {
                wp_delete_post($candidate_id, true);
                delete_user_meta($user_id, 'jobsearch_candidate_id');
            }

            $emp_members = get_post_meta($employer_id, 'emp_acount_member_acounts', true);
            $emp_members = !empty($emp_members) ? $emp_members : array();

            $emp_members[] = $user_id;
            update_post_meta($employer_id, 'emp_acount_member_acounts', $emp_members);

            //
            update_user_meta($user_id, 'attached_profile_empid', $employer_id);
            update_user_meta($user_id, 'attached_profile_empuid', $emp_user_id);
            update_user_meta($user_id, 'jobsearch_attchprof_perms', $mem_perms);

            echo json_encode(array('error' => '0', 'msg' => esc_html__('This member has been added in your profile successfully.', 'wp-jobsearch')));
            die;
        }
    } else {
        echo json_encode(array('error' => '1', 'msg' => esc_html__('You are not allowed to add an account member.', 'wp-jobsearch')));
        die;
    }
}

add_action('wp_ajax_jobsearch_employer_update_member_account', 'jobsearch_updtingemp_accmemb_ajax_callback');

function jobsearch_updtingemp_accmemb_ajax_callback() {
    $first_name = isset($_POST['u_firstname']) ? $_POST['u_firstname'] : '';
    $last_name = isset($_POST['u_lastname']) ? $_POST['u_lastname'] : '';
    $member_uid = isset($_POST['member_uid']) && $_POST['member_uid'] > 0 ? $_POST['member_uid'] : 0;
    $mem_perms = isset($_POST['u_memb_perms']) ? $_POST['u_memb_perms'] : '';

    //
    if (isset($_POST['cus_employer_id'])) {
        $emp_user_id = $_POST['cus_employer_id'];
    } else {
        $emp_user_id = get_current_user_id();
    }
    $employer_id = jobsearch_get_user_employer_id($emp_user_id);

    $emp_accmembers = get_post_meta($employer_id, 'emp_acount_member_acounts', true);
    $emp_accmembers = !empty($emp_accmembers) ? $emp_accmembers : array();
    $memb_uid_key = array_search($member_uid, $emp_accmembers);
    if ($employer_id > 0 && isset($emp_accmembers[$memb_uid_key]) && $emp_accmembers[$memb_uid_key] == $member_uid) {
        //
        $error = 0;

        $user_id = $member_uid;
        $get_acuser_obj = get_user_by('ID', $user_id);

        if (isset($get_acuser_obj->ID)) {
            $disply_name = $get_acuser_obj->disply_name;
            if ($first_name != '') {
                $disply_name = $last_name != '' ? $first_name . ' ' . $last_name : $first_name;
            }
            $user_def_array = array(
                'ID' => $user_id,
                'first_name' => $first_name,
                'last_name' => $last_name,
                'display_name' => $disply_name,
            );
            wp_update_user($user_def_array);
            $candidate_id = jobsearch_get_user_candidate_id($user_id);

            //
            update_user_meta($user_id, 'jobsearch_attchprof_perms', $mem_perms);

            echo json_encode(array('error' => '0', 'msg' => esc_html__('Member info updated successfully.', 'wp-jobsearch')));
            die;
        } else {
            echo json_encode(array('error' => '1', 'msg' => esc_html__('You are not allowed to update this account member.', 'wp-jobsearch')));
            die;
        }
    } else {
        echo json_encode(array('error' => '1', 'msg' => esc_html__('You are not allowed to update this account member.', 'wp-jobsearch')));
        die;
    }
}

add_action('wp_ajax_jobsearch_employer_remove_member_account', 'jobsearch_rmovinemp_accmemb_ajax_callback');

function jobsearch_rmovinemp_accmemb_ajax_callback() {
    $member_uid = isset($_POST['member_uid']) && $_POST['member_uid'] > 0 ? $_POST['member_uid'] : 0;

    //
    if (isset($_POST['cus_employer_id'])) {
        $emp_user_id = $_POST['cus_employer_id'];
    } else {
        $emp_user_id = get_current_user_id();
    }
    $employer_id = jobsearch_get_user_employer_id($emp_user_id);

    $emp_accmembers = get_post_meta($employer_id, 'emp_acount_member_acounts', true);
    $emp_accmembers = !empty($emp_accmembers) ? $emp_accmembers : array();
    $memb_uid_key = array_search($member_uid, $emp_accmembers);
    if ($employer_id > 0 && isset($emp_accmembers[$memb_uid_key]) && $emp_accmembers[$memb_uid_key] == $member_uid) {
        //
        $error = 0;

        $user_id = $member_uid;
        $get_acuser_obj = get_user_by('ID', $user_id);

        if (isset($get_acuser_obj->ID)) {

            wp_delete_user($user_id);

            //
            unset($emp_accmembers[$memb_uid_key]);
            update_post_meta($employer_id, 'emp_acount_member_acounts', $emp_accmembers);

            echo json_encode(array('error' => '0', 'msg' => esc_html__('Member removed successfully.', 'wp-jobsearch')));
            die;
        } else {
            echo json_encode(array('error' => '1', 'msg' => esc_html__('You are not allowed to remove this account member.', 'wp-jobsearch')));
            die;
        }
    } else {
        echo json_encode(array('error' => '1', 'msg' => esc_html__('You are not allowed to remove this account member.', 'wp-jobsearch')));
        die;
    }
}

function jobsearch_user_isemp_member($user_id) {

    $par_empuser_id = get_user_meta($user_id, 'attached_profile_empuid', true);

    $par_emp_id = jobsearch_get_user_employer_id($par_empuser_id);

    if ($par_emp_id > 0) {
        return $par_emp_id;
    }
}

function jobsearch_user_parmember_uid($user_id) {

    $par_empuser_id = get_user_meta($user_id, 'attached_profile_empuid', true);

    if ($par_empuser_id > 0) {
        return $par_empuser_id;
    }
}

function jobsearch_emp_accmember_perms($user_id) {

    $user_perms = get_user_meta($user_id, 'jobsearch_attchprof_perms', true);

    return $user_perms;
}
