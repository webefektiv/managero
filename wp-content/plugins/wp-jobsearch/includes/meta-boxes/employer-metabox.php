<?php
/**
 * Define Meta boxes for plugin
 * and theme.
 *
 */
add_action('save_post', 'jobsearch_employers_meta_save');

function jobsearch_employers_meta_save($post_id) {
    global $pagenow;
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    $post_type = '';
    if ($pagenow == 'post.php') {
        $post_type = get_post_type();
    }
    if (isset($_POST)) {
        if ($post_type == 'employer') {
            // extra save
            // Employer jobs status change according his/her status
            do_action('jobsearch_employer_update_jobs_status', $post_id);

            // Cus Fields Upload Files /////
            do_action('jobsearch_custom_field_upload_files_save', $post_id, 'employer');
            //
            // feature cand from bckend
            if (isset($_POST['cusemp_feature_fbckend'])) {
                $promote_pckg_subtime = get_post_meta($post_id, 'promote_profile_substime', true);
                //
                $cusemp_feature_fbckend = $_POST['cusemp_feature_fbckend'];
                if ($cusemp_feature_fbckend == 'on') {
                    update_post_meta($post_id, '_feature_mber_frmadmin', 'yes');
                    if ($promote_pckg_subtime <= 0) {
                        update_post_meta($post_id, 'promote_profile_substime', current_time('timestamp'));
                    }
                } else if ($cusemp_feature_fbckend == 'off') {
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
        'post_type' => 'employer',
            )
    );
}

/**
 * Employer settings meta box.
 */
function jobsearch_employers_settings_meta_boxes() {
    add_meta_box('jobsearch-employers-settings', esc_html__('Employer Settings', 'wp-jobsearch'), 'jobsearch_employers_meta_settings', 'employer', 'normal');
}

/**
 * Employer settings meta box callback.
 */
function jobsearch_employers_meta_settings() {
    global $post, $jobsearch_form_fields, $jobsearch_plugin_options;
    $rand_num = rand(1000000, 99999999);
    $_post_id = $post->ID;
    $employer_posted_by = get_post_meta($post->ID, 'jobsearch_field_users', true);

    $employer_user_id = get_post_meta($post->ID, 'jobsearch_user_id', true);
    //
    $emp_phone_switch = isset($jobsearch_plugin_options['employer_phone_field']) ? $jobsearch_plugin_options['employer_phone_field'] : '';
    $emp_web_switch = isset($jobsearch_plugin_options['employer_web_field']) ? $jobsearch_plugin_options['employer_web_field'] : '';
    $emp_foundate_switch = isset($jobsearch_plugin_options['employer_founded_date']) ? $jobsearch_plugin_options['employer_founded_date'] : '';
    ?>
    <script>
        jQuery(document).ready(function () {
            jQuery('#jobsearch_employer_publish_date').datetimepicker({
                timepicker: true,
                format: 'd-m-Y H:i:s'
            });
            jQuery('#jobsearch_employer_expiry_date').datetimepicker({
                timepicker: true,
                format: 'd-m-Y H:i:s'
            });
        });
    </script>
    <div class="jobsearch-post-settings">
        <?php
        $get_user_emp_id = get_user_meta($employer_user_id, 'jobsearch_employer_id', true);
        update_user_meta($employer_user_id, 'jobsearch_employer_id', filter_var($get_user_emp_id, FILTER_SANITIZE_NUMBER_INT));

   // atach user
       if ($get_user_emp_id != '' && $post->ID == $get_user_emp_id) {
            $user_obj = get_user_by('ID', $employer_user_id);

            if (is_object($user_obj)) {
                ?>
                <br><br>
                <div class="jobsearch-element-field" style="display: none;">
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
// end atach user





 // date founded
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
        
        if ($emp_foundate_switch == 'on') {
            ?>
            <div class="jobsearch-element-field" style="display: none;">
                <div class="elem-label">
                    <label><?php esc_html_e('Founded Date', 'wp-jobsearch') ?></label>
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
            <?php
        }
// end date




// phone
        if ($emp_phone_switch == 'on') {
            ?>
            <div class="jobsearch-element-field">
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
            <?php
        }

        // endphone

        ?>



<!--        featured company -->

        <div class="jobsearch-element-field" style="display: none;">
            <div class="elem-label">
                <label><?php esc_html_e('Featured Employer', 'wp-jobsearch') ?></label>
            </div>
            <div class="elem-field">
                <?php
                $emp_feature_val = get_post_meta($_post_id, 'cusemp_feature_fbckend', true);

                if ($emp_feature_val != 'on' && $emp_feature_val != 'off') {
                    $feature_att_pckg = get_post_meta($_post_id, 'att_promote_profile_pkgorder', true);
                    if (!jobsearch_promote_profile_pkg_is_expired($feature_att_pckg)) {
                        $emp_feature_val = 'on';
                    }
                }

                $field_params = array(
                    'force_std' => $emp_feature_val,
                    'name' => 'emp_feature',
                    'cus_name' => 'cusemp_feature_fbckend',
                );
                $jobsearch_form_fields->checkbox_field($field_params);
                ?>
            </div>
        </div>

<!--        feature company end-->



        <div class="jobsearch-element-field">
            <div class="elem-label">
                <label><?php esc_html_e('Approved', 'wp-jobsearch') ?></label>
            </div>
            <div class="elem-field">
                <?php
                $field_params = array(
                    'std' => 'on',
                    'name' => 'employer_approved',
                );
                $jobsearch_form_fields->checkbox_field($field_params);
                ?>
            </div>
        </div>



        <?php
        // custom fields
        // load custom fields which is configured in employer custom fields
        do_action('jobsearch_custom_fields_load', $post->ID, 'employer');

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
        $all_location_allow = isset($jobsearch_plugin_options['all_location_allow']) ? $jobsearch_plugin_options['all_location_allow'] : '';

        if ($all_location_allow == 'on') {
            ?>
            <div class="jobsearch-elem-heading">
                <h2><?php esc_html_e('Location', 'wp-jobsearch') ?></h2>
            </div>
            <?php
        }
        do_action('jobsearch_admin_location_map', $post->ID);

        // employer multi meta fields
     //   do_action('employer_multi_fields_meta', $post);
        ?>
        <div class="jobsearch-elem-heading" >
            <h2><?php esc_html_e('Profile Photos', 'wp-jobsearch') ?></h2>
        </div>
        <div class="jobsearch-element-field">
            <?php
            jobsearch_admin_gallery('company_gallery_imgs', esc_html__('Add Photos', 'wp-jobsearch'));
            ?>
        </div>
        <?php
       echo apply_filters('jobsearch_emp_bk_meta_fields_after_photo_gal', '', $_post_id);
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

        //
        $employer_id = $_post_id;
        $reults_per_page = isset($jobsearch_plugin_options['user-dashboard-per-page']) && $jobsearch_plugin_options['user-dashboard-per-page'] > 0 ? $jobsearch_plugin_options['user-dashboard-per-page'] : 10;
        $page_num = isset($_GET['page_num']) ? $_GET['page_num'] : 1;

        $args = array(
            'post_type' => 'job',
            'posts_per_page' => $reults_per_page,
            'paged' => $page_num,
            'post_status' => 'publish',
            'order' => 'DESC',
            'orderby' => 'ID',
            'meta_query' => array(
                array(
                    'key' => 'jobsearch_field_job_posted_by',
                    'value' => $employer_id,
                    'compare' => '=',
                ),
            ),
        );

        $jobs_query = new WP_Query($args);

        $total_jobs = $jobs_query->found_posts;

        $free_jobs_allow = isset($jobsearch_plugin_options['free-jobs-allow']) ? $jobsearch_plugin_options['free-jobs-allow'] : '';
        if ($jobs_query->have_posts()) {
            global $Jobsearch_User_Dashboard_Settings;

            $page_url = admin_url('post.php');
            wp_enqueue_script('jobsearch-user-dashboard');
            ?>
            <div class="jobsearch-elem-heading">
                <h2><?php esc_html_e('Manage Jobs', 'wp-jobsearch') ?></h2>
            </div>
            <div class="jobsearch-jobs-list-holder">
                <div class="jobsearch-managejobs-list">
                    <!-- Manage Jobs Header -->
                    <div class="jobsearch-table-layer jobsearch-managejobs-thead">
                        <div class="jobsearch-table-row">
                            <div class="jobsearch-table-cell"><?php esc_html_e('Job Title', 'wp-jobsearch') ?></div>
                            <div class="jobsearch-table-cell"><?php esc_html_e('Applications', 'wp-jobsearch') ?></div>
<!--                            <div class="jobsearch-table-cell">--><?php //esc_html_e('Featured', 'wp-jobsearch') ?><!--</div>-->
                            <div class="jobsearch-table-cell"><?php esc_html_e('Status', 'wp-jobsearch') ?></div>
                            <div class="jobsearch-table-cell"></div>
                        </div>
                    </div>
                    <?php
                    while ($jobs_query->have_posts()) : $jobs_query->the_post();
                        $job_id = get_the_ID();

                        $sectors = wp_get_post_terms($job_id, 'sector');
                        $job_sector = isset($sectors[0]->name) ? $sectors[0]->name : '';

                        $jobtypes = wp_get_post_terms($job_id, 'jobtype');
                        $job_type = isset($jobtypes[0]->term_id) ? $jobtypes[0]->term_id : '';

                        $get_job_location = get_post_meta($job_id, 'jobsearch_field_location_address', true);

                        $job_publish_date = get_post_meta($job_id, 'jobsearch_field_job_publish_date', true);
                        $job_expiry_date = get_post_meta($job_id, 'jobsearch_field_job_expiry_date', true);

                        $job_filled = get_post_meta($job_id, 'jobsearch_field_job_filled', true);

                        $job_status = 'pending';
                        $job_status = get_post_meta($job_id, 'jobsearch_field_job_status', true);

                        if ($job_expiry_date != '' && $job_expiry_date <= strtotime(current_time('d-m-Y H:i:s', 1))) {
                            $job_status = 'expired';
                        }

                        $status_txt = '';
                        if ($job_status == 'pending') {
                            $status_txt = esc_html__('Pending', 'wp-jobsearch');
                        } else if ($job_status == 'expired') {
                            $status_txt = esc_html__('Expired', 'wp-jobsearch');
                        } else if ($job_status == 'canceled') {
                            $status_txt = esc_html__('Canceled', 'wp-jobsearch');
                        } else if ($job_status == 'approved') {
                            $status_txt = esc_html__('Approved', 'wp-jobsearch');
                        } else if ($job_status == 'admin-review') {
                            $status_txt = esc_html__('Admin Review', 'wp-jobsearch');
                        }

                        $job_is_feature = get_post_meta($job_id, 'jobsearch_field_job_featured', true);

                        $job_applicants_list = get_post_meta($job_id, 'jobsearch_job_applicants_list', true);
                        $job_applicants_list = jobsearch_is_post_ids_array($job_applicants_list, 'candidate');
                        if (empty($job_applicants_list)) {
                            $job_applicants_list = array();
                        }

                        $job_applicants_count = !empty($job_applicants_list) ? count($job_applicants_list) : 0;
                        ?>
                        <div class="jobsearch-table-layer jobsearch-managejobs-tbody">
                            <div class="jobsearch-table-row">
                                <div class="jobsearch-table-cell">
                                    <h6><a href="<?php echo get_permalink($job_id) ?>"><?php echo get_the_title() ?></a> <span class="job-filled"><?php echo ($job_filled == 'on' ? esc_html__('(Filled)', 'wp-jobsearch') : '') ?></span></h6>

                                    <ul>
                                        <?php
//                                        if ($job_publish_date != '') {
//                                            ?>
<!--                                            <li><i class="jobsearch-icon jobsearch-calendar"></i> --><?php //printf(wp_kses(__('Created: <span>%s</span>', 'wp-jobsearch'), array('span' => array())), date_i18n('M d, Y', $job_publish_date)) ?><!--</li>-->
<!--                                            --><?php
//                                        }
//                                        if ($job_expiry_date != '') {
//                                            ?>
<!--                                            <li><i class="jobsearch-icon jobsearch-calendar"></i> --><?php //printf(wp_kses(__('Expiry: <span>%s</span>', 'wp-jobsearch'), array('span' => array())), date_i18n('M d, Y', $job_expiry_date)) ?><!--</li>-->
<!--                                            --><?php
//                                        }
                                        if ($get_job_location != '') {
                                            ?>
                                            <li><i class="jobsearch-icon jobsearch-maps-and-flags"></i> <?php echo ($get_job_location) ?></li>
                                            <?php
                                        }
                                        if ($job_sector != '') {
                                            ?>
                                            <li><i class="jobsearch-icon jobsearch-filter-tool-black-shape"></i> <a><?php echo ($job_sector) ?></a></li>
                                            <?php
                                        }
                                        ?>
                                    </ul>
                                </div>

                                <div class="jobsearch-table-cell"><a <?php echo ($job_applicants_count > 0 ? 'href="' . add_query_arg(array('post' => $job_id, 'action' => 'edit', 'view' => 'applicants'), $page_url) . '"' : '') ?> class="jobsearch-managejobs-appli"><?php printf(__('%s Application(s)', 'wp-jobsearch'), $job_applicants_count) ?></a></div>
<!--                                <div class="jobsearch-table-cell">-->
<!--                                    <a><i class="--><?php //echo ($job_is_feature == 'on' ? 'fa fa-star' : 'fa fa-star-o') ?><!--"></i></a>-->
<!--                                </div>-->
                                <div class="jobsearch-table-cell"><span class="jobsearch-managejobs-option <?php echo ($job_status == 'approved' ? 'active' : '') ?><?php echo ($job_status == 'expired' || $job_status == 'canceled' ? 'expired' : '') ?>"><?php echo ($status_txt) ?></span></div>
                                <div class="jobsearch-table-cell">
                                    <div class="jobsearch-managejobs-links">
                                        <a href="<?php echo get_permalink($job_id) ?>" class="jobsearch-icon jobsearch-view dashicons dashicons-visibility"></a>
                                        <a href="<?php echo add_query_arg(array('post' => $job_id, 'action' => 'edit'), $page_url) ?>" class="jobsearch-icon jobsearch-edit dashicons dashicons-edit"></a>
                                        <a href="javascript:void(0);" data-id="<?php echo ($job_id) ?>" class="jobsearch-icon jobsearch-rubbish dashicons dashicons-trash jobsearch-trash-job"></a>
                                    </div>
                                </div>
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
            if ($total_jobs > 0 && $reults_per_page > 0 && $total_jobs > $reults_per_page) {
                $total_pages = ceil($total_jobs / $reults_per_page);
                ?>
                <div class="jobsearch-pagination-blog">
                    <?php $Jobsearch_User_Dashboard_Settings->pagination($total_pages, $page_num, $page_url) ?>
                </div>
                <?php
            }
        }

        //
        ?>
        <div class="jobsearch-elem-heading" style="display:none;">
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
                    'value' => array('job', 'cv', 'featured_jobs', 'urgent_pkg', 'promote_profile'),
                    'compare' => 'IN',
                ),
            ),
        );
        $pkgs_query = new WP_Query($args);

        if ($pkgs_query->have_posts()) {
            ?>
            <div class="packge-asignbtn-holder" style="display:none;">>
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
                <a href="javascript:void(0);" data-uid="<?php echo ($employer_user_id) ?>" data-id="<?php echo ($firts_pkg_id) ?>" class="button button-primary button-large admin-packge-asignbtn"><?php esc_html_e('Assign new package to this User', 'wp-jobsearch') ?></a>
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
                        } else if (typeof response.msg !== 'undefined' && response.msg != '') {
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
                    'value' => array('job', 'featured_jobs', 'cv', 'urgent_pkg', 'promote_profile'),
                    'compare' => 'IN',
                ),
                array(
                    'key' => 'jobsearch_order_user',
                    'value' => $employer_user_id,
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
                            <div class="jobsearch-table-cell"><?php esc_html_e('Total Jobs/CVs', 'wp-jobsearch') ?></div>
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

                        if ($pkg_type == 'cv') {
                            $total_cvs = get_post_meta($pkg_order_id, 'num_of_cvs', true);

                            $used_cvs = jobsearch_pckg_order_used_cvs($pkg_order_id);
                            $remaining_cvs = jobsearch_pckg_order_remaining_cvs($pkg_order_id);
                        } else if ($pkg_type == 'featured_jobs') {
                            $total_jobs = get_post_meta($pkg_order_id, 'num_of_fjobs', true);

                            $job_exp_dur = get_post_meta($pkg_order_id, 'fjob_expiry_time', true);
                            $job_exp_dur_unit = get_post_meta($pkg_order_id, 'fjob_expiry_time_unit', true);

                            $used_jobs = jobsearch_pckg_order_used_fjobs($pkg_order_id);
                            $remaining_jobs = jobsearch_pckg_order_remaining_fjobs($pkg_order_id);
                        } else {
                            $total_jobs = get_post_meta($pkg_order_id, 'num_of_jobs', true);
                            $total_jobs = apply_filters('jobsearch_emp_dash_pkg_total_jobs_count', $total_jobs, $pkg_order_id);

                            $job_exp_dur = get_post_meta($pkg_order_id, 'job_expiry_time', true);
                            $job_exp_dur_unit = get_post_meta($pkg_order_id, 'job_expiry_time_unit', true);

                            $used_jobs = jobsearch_pckg_order_used_jobs($pkg_order_id);
                            $used_jobs = apply_filters('jobsearch_emp_dash_pkg_used_jobs_count', $used_jobs, $pkg_order_id);
                            $remaining_jobs = jobsearch_pckg_order_remaining_jobs($pkg_order_id);
                            $remaining_jobs = apply_filters('jobsearch_emp_dash_pkg_remain_jobs_count', $remaining_jobs, $pkg_order_id);
                        }
                        $pkg_exp_dur = get_post_meta($pkg_order_id, 'package_expiry_time', true);
                        $pkg_exp_dur_unit = get_post_meta($pkg_order_id, 'package_expiry_time_unit', true);

                        $status_txt = esc_html__('Active', 'wp-jobsearch');
                        $status_class = ' style="color: green;"';
                        if ($pkg_type == 'cv') {
                            if (jobsearch_cv_pckg_order_is_expired($pkg_order_id)) {
                                $status_txt = esc_html__('Expired', 'wp-jobsearch');
                                $status_class = ' style="color: red;"';
                            }
                        } else if ($pkg_type == 'featured_jobs') {
                            if (jobsearch_fjobs_pckg_order_is_expired($pkg_order_id)) {
                                $status_txt = esc_html__('Expired', 'wp-jobsearch');
                                $status_class = ' style="color: red;"';
                            }
                        } else {
                            if (jobsearch_pckg_order_is_expired($pkg_order_id)) {
                                $status_txt = esc_html__('Expired', 'wp-jobsearch');
                                $status_class = ' style="color: red;"';
                            }
                            $status_txt = apply_filters('jobsearch_emp_dash_jobpkgs_list_status_txt', $status_txt, $pkg_order_id);
                            $status_class = apply_filters('jobsearch_emp_dash_jobpkgs_list_status_class', $status_class, $pkg_order_id);
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
                        <div class="jobsearch-table-layer jobsearch-managejobs-tbody">
                            <div class="jobsearch-table-row">
                                <div class="jobsearch-table-cell" style="width: 20%;">#<?php echo ($pkg_order_id) ?></div>
                                <div class="jobsearch-table-cell">
                                    <?php
                                    ob_start();
                                    ?>
                                    <span><?php echo ($pkg_order_name) ?></span>
                                    <?php
                                    $pkg_name_html = ob_get_clean();
                                    echo apply_filters('jobsearch_emp_dashboard_pkgs_list_pkg_title', $pkg_name_html, $pkg_order_id);
                                    ?>
                                </div>
                                <?php
                                if ($pkg_type == 'cv') {
                                    ?>
                                    <div class="jobsearch-table-cell"><?php echo ($total_cvs) ?></div>
                                    <div class="jobsearch-table-cell"><?php echo ($used_cvs) ?></div>
                                    <div class="jobsearch-table-cell"><?php echo ($remaining_cvs) ?></div>
                                    <?php
                                } else {
                                    ?>
                                    <div class="jobsearch-table-cell"><?php echo ($total_jobs) ?></div>
                                    <div class="jobsearch-table-cell"><?php echo ($used_jobs) ?></div>
                                    <div class="jobsearch-table-cell"><?php echo ($remaining_jobs) ?></div>
                                    <?php
                                }
                                ?>
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

        //
        ?>
        <div class="jobsearch-elem-heading" style="display: none;">
            <h2><?php esc_html_e('Account Members', 'wp-jobsearch') ?></h2>
        </div>

        <?php
        $popup_args = array(
            '_post_id' => $_post_id,
            'employer_user_id' => $employer_user_id,
        );
        add_action('admin_footer', function () use ($popup_args) {

            global $jobsearch_plugin_options;

            extract(shortcode_atts(array(
                '_post_id' => '',
                'employer_user_id' => '',
                            ), $popup_args));
            ?>
            <div class="jobsearch-modal fade" id="JobSearchModalEmpAccMembAdd" style="display: none;">
                <div class="modal-inner-area">&nbsp;</div>
                <div class="modal-content-area">
                    <div class="modal-box-area">
                        <div class="jobsearch-modal-title-box">
                            <h2><?php esc_html_e('Add Account Member', 'wp-jobsearch') ?></h2>
                            <span class="modal-close"><i class="fa fa-times"></i></span>
                        </div>
                        <div class="jobsearch-addempacount-membcon jobsearch-typo-wrap">
                            <?php
                            echo '<form id="addempmemb-account-form" method="post">';
                            ?>
                            <div class="jobsearch-user-form jobsearch-user-form-coltwo">
                                <ul class="addempmemb-fields-list">
                                    <li> 
                                        <label><?php esc_html_e('First Name:', 'wp-jobsearch') ?></label>
                                        <input class="required" name="u_firstname" type="text" placeholder="<?php esc_html_e('First Name', 'wp-jobsearch') ?>">
                                    </li>
                                    <li> 
                                        <label><?php esc_html_e('Last Name:', 'wp-jobsearch') ?></label>
                                        <input class="required" name="u_lastname" type="text" placeholder="<?php esc_html_e('Last Name', 'wp-jobsearch') ?>">
                                    </li>
                                    <li> 
                                        <label><?php esc_html_e('Username:', 'wp-jobsearch') ?></label>
                                        <input class="required" name="u_username" type="text" placeholder="<?php esc_html_e('Username', 'wp-jobsearch') ?>">
                                    </li>
                                    <li> 
                                        <label><?php esc_html_e('Email Address:', 'wp-jobsearch') ?></label>
                                        <input class="required" name="u_emailadres" type="text" placeholder="<?php esc_html_e('Email Address', 'wp-jobsearch') ?>">
                                    </li>
                                    <li> 
                                        <label><?php esc_html_e('Password:', 'wp-jobsearch') ?></label>
                                        <input name="u_password" type="password" placeholder="<?php esc_html_e('Password', 'wp-jobsearch') ?>">
                                    </li>
                                    <li> 
                                        <label><?php esc_html_e('Confirm Password:', 'wp-jobsearch') ?></label>
                                        <input class="required" name="u_confpass" type="password" placeholder="<?php esc_html_e('Confirm Password', 'wp-jobsearch') ?>">
                                    </li>
                                    <li class="jobsearch-user-form-coltwo-full"> 
                                        <div class="jobsearch-adingmem-permisons">
                                            <h3><?php esc_html_e('Member Permissions', 'wp-jobsearch') ?></h3>
                                            <ul>
                                                <li> 
                                                    <input id="u-post-job-btn" name="u_memb_perms[]" type="checkbox" value="u_post_job" checked="checked">
                                                    <label for="u-post-job-btn"><?php esc_html_e('Post New Job', 'wp-jobsearch') ?></label>
                                                </li>
                                                <li> 
                                                    <input id="u-mange-jobs-btn" name="u_memb_perms[]" type="checkbox" value="u_manage_jobs" checked="checked">
                                                    <label for="u-mange-jobs-btn"><?php esc_html_e('Manage Jobs', 'wp-jobsearch') ?></label>
                                                </li>
                                                <li> 
                                                    <input id="u-saved-cands-btn" name="u_memb_perms[]" type="checkbox" value="u_saved_cands" checked="checked">
                                                    <label for="u-saved-cands-btn"><?php esc_html_e('Saved Candidates', 'wp-jobsearch') ?></label>
                                                </li>
                                                <li> 
                                                    <input id="u-pkgs-perms-btn" name="u_memb_perms[]" type="checkbox" value="u_packages" checked="checked">
                                                    <label for="u-pkgs-perms-btn"><?php esc_html_e('Packages', 'wp-jobsearch') ?></label>
                                                </li>
                                                <li> 
                                                    <input id="u-trans-perms-btn" name="u_memb_perms[]" type="checkbox" value="u_transactions" checked="checked">
                                                    <label for="u-trans-perms-btn"><?php esc_html_e('Transactions', 'wp-jobsearch') ?></label>
                                                </li>
                                            </ul>
                                        </div>
                                    </li>
                                    <li class="jobsearch-user-form-coltwo-full">
                                        <input type="hidden" name="action" value="jobsearch_employer_ading_member_account">
                                        <input type="hidden" name="cus_employer_id" value="<?php echo ($employer_user_id) ?>">
                                        <input class="jobsearch-empmember-add-btn" type="submit" value="<?php esc_html_e('Add Member', 'wp-jobsearch') ?>">
                                        <div class="form-loader"></div>
                                    </li>
                                </ul>
                                <div class="form-msg"></div>
                            </div>
                            <?php
                            echo '</form>';
                            ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php
        }, 11, 1);
        ?>
        <div class="jobsearch-addemp-membcon">
            <a href="javascript:void(0)" class="button button-primary button-large jobsearch-empmember-add-popup"><span class="fa fa-plus"></span> <?php esc_html_e('Add Account Member', 'wp-jobsearch') ?> </a>
        </div>
        <div class="empacc-menbers-list">
            <?php
            $emp_accmembers = get_post_meta($_post_id, 'emp_acount_member_acounts', true);
            if (!empty($emp_accmembers)) {
                ?>
                <ul class="accmem-head">
                    <li><?php esc_html_e('Account Member', 'wp-jobsearch') ?></li>
                    <li><?php esc_html_e('Actions', 'wp-jobsearch') ?></li>
                </ul>
                <?php
                foreach ($emp_accmembers as $emp_accmemb_uid) {

                    $get_acuser_obj = get_user_by('ID', $emp_accmemb_uid);

                    //
                    $att_user_pperms = get_user_meta($emp_accmemb_uid, 'jobsearch_attchprof_perms', true);

                    if (isset($get_acuser_obj->display_name)) {
                        ?>
                        <ul class="accmem-head">
                            <li><?php echo ($get_acuser_obj->display_name) ?></li>
                            <li>
                                <a href="javascript:void(0);" class="emp-memb-updatebtn" data-id="<?php echo ($emp_accmemb_uid) ?>"><i class="jobsearch-icon jobsearch-edit dashicons dashicons-edit"></i></a>
                                <a href="javascript:void(0);" class="emp-memb-removebtn" data-id="<?php echo ($emp_accmemb_uid) ?>" data-euid="<?php echo ($employer_user_id) ?>"><i class="jobsearch-icon jobsearch-rubbish dashicons dashicons-trash"></i></a>
                            </li>
                        </ul>
                        <?php
                        $popup_args = array(
                            '_post_id' => $_post_id,
                            'employer_user_id' => $employer_user_id,
                            'memb_acc_uid' => $emp_accmemb_uid,
                        );
                        add_action('admin_footer', function () use ($popup_args) {

                            global $jobsearch_plugin_options;

                            extract(shortcode_atts(array(
                                '_post_id' => '',
                                'employer_user_id' => '',
                                'memb_acc_uid' => '',
                                            ), $popup_args));

                            $get_acuser_obj = get_user_by('ID', $memb_acc_uid);
                            $att_user_pperms = get_user_meta($memb_acc_uid, 'jobsearch_attchprof_perms', true);
                            ?>
                            <div class="jobsearch-modal fade" id="JobSearchModalEmpAccMembUpdate<?php echo ($memb_acc_uid) ?>">
                                <div class="modal-inner-area">&nbsp;</div>
                                <div class="modal-content-area">
                                    <div class="modal-box-area">
                                        <div class="jobsearch-modal-title-box">
                                            <h2><?php esc_html_e('Update Account Member', 'wp-jobsearch') ?></h2>
                                            <span class="modal-close"><i class="fa fa-times"></i></span>
                                        </div>
                                        <div class="jobsearch-addempacount-membcon jobsearch-typo-wrap">
                                            <?php
                                            echo '<form id="editempmemb-account-form-' . ($memb_acc_uid) . '" method="post">';
                                            ?>
                                            <div class="jobsearch-user-form jobsearch-user-form-coltwo">
                                                <ul class="addempmemb-fields-list">
                                                    <li> 
                                                        <label><?php esc_html_e('First Name:', 'wp-jobsearch') ?></label>
                                                        <input class="required" name="u_firstname" type="text" placeholder="<?php esc_html_e('First Name', 'wp-jobsearch') ?>" value="<?php echo ($get_acuser_obj->first_name) ?>">
                                                    </li>
                                                    <li> 
                                                        <label><?php esc_html_e('Last Name:', 'wp-jobsearch') ?></label>
                                                        <input class="required" name="u_lastname" type="text" placeholder="<?php esc_html_e('Last Name', 'wp-jobsearch') ?>" value="<?php echo ($get_acuser_obj->last_name) ?>">
                                                    </li>
                                                    <li> 
                                                        <label><?php esc_html_e('Username:', 'wp-jobsearch') ?></label>
                                                        <input class="required" type="text" readonly="readonly" value="<?php echo ($get_acuser_obj->user_login) ?>">
                                                    </li>
                                                    <li> 
                                                        <label><?php esc_html_e('Email Address:', 'wp-jobsearch') ?></label>
                                                        <input class="required" type="text" readonly="readonly" value="<?php echo ($get_acuser_obj->user_email) ?>">
                                                    </li>
                                                    <li class="jobsearch-user-form-coltwo-full"> 
                                                        <div class="jobsearch-adingmem-permisons">
                                                            <h3><?php esc_html_e('Member Permissions', 'wp-jobsearch') ?></h3>
                                                            <ul>
                                                                <li>
                                                                    <input id="u-post-job-btn-<?php echo ($memb_acc_uid) ?>" name="u_memb_perms[]" type="checkbox" value="u_post_job" <?php echo (!empty($att_user_pperms) && in_array('u_post_job', $att_user_pperms) ? 'checked="checked"' : '') ?>>
                                                                    <label for="u-post-job-btn-<?php echo ($memb_acc_uid) ?>"><?php esc_html_e('Post New Job', 'wp-jobsearch') ?></label>
                                                                </li>
                                                                <li>
                                                                    <input id="u-mange-jobs-btn-<?php echo ($memb_acc_uid) ?>" name="u_memb_perms[]" type="checkbox" value="u_manage_jobs" <?php echo (!empty($att_user_pperms) && in_array('u_manage_jobs', $att_user_pperms) ? 'checked="checked"' : '') ?>>
                                                                    <label for="u-mange-jobs-btn-<?php echo ($memb_acc_uid) ?>"><?php esc_html_e('Manage Jobs', 'wp-jobsearch') ?></label>
                                                                </li>
                                                                <li>
                                                                    <input id="u-saved-cands-btn-<?php echo ($memb_acc_uid) ?>" name="u_memb_perms[]" type="checkbox" value="u_saved_cands" <?php echo (!empty($att_user_pperms) && in_array('u_saved_cands', $att_user_pperms) ? 'checked="checked"' : '') ?>>
                                                                    <label for="u-saved-cands-btn-<?php echo ($memb_acc_uid) ?>"><?php esc_html_e('Saved Candidates', 'wp-jobsearch') ?></label>
                                                                </li>
                                                                <li>
                                                                    <input id="u-pkgs-perms-btn-<?php echo ($memb_acc_uid) ?>" name="u_memb_perms[]" type="checkbox" value="u_packages" <?php echo (!empty($att_user_pperms) && in_array('u_packages', $att_user_pperms) ? 'checked="checked"' : '') ?>>
                                                                    <label for="u-pkgs-perms-btn-<?php echo ($memb_acc_uid) ?>"><?php esc_html_e('Packages', 'wp-jobsearch') ?></label>
                                                                </li>
                                                                <li>
                                                                    <input id="u-trans-perms-btn-<?php echo ($memb_acc_uid) ?>" name="u_memb_perms[]" type="checkbox" value="u_transactions" <?php echo (!empty($att_user_pperms) && in_array('u_transactions', $att_user_pperms) ? 'checked="checked"' : '') ?>>
                                                                    <label for="u-trans-perms-btn-<?php echo ($memb_acc_uid) ?>"><?php esc_html_e('Transactions', 'wp-jobsearch') ?></label>
                                                                </li>
                                                            </ul>
                                                        </div>
                                                    </li>
                                                    <li class="jobsearch-user-form-coltwo-full">
                                                        <input type="hidden" name="action" value="jobsearch_employer_update_member_account">
                                                        <input type="hidden" name="member_uid" value="<?php echo ($memb_acc_uid) ?>">
                                                        <input type="hidden" name="cus_employer_id" value="<?php echo ($employer_user_id) ?>">
                                                        <input class="jobsearch-empmember-updte-btn" data-id="<?php echo ($memb_acc_uid) ?>" type="submit" value="<?php esc_html_e('Add Member', 'wp-jobsearch') ?>">
                                                        <div class="form-loader"></div>
                                                    </li>
                                                </ul>
                                                <div class="form-msg"></div>
                                            </div>
                                            <?php
                                            echo '</form>';
                                            ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php
                        }, 11, 1);
                    }
                }
            }
            ?>
        </div>

    </div> 
    <?php
}
