<?php
if (!function_exists('jobsearch_job_get_profile_image')) {

    function jobsearch_job_get_profile_image($job_id) {
        $post_thumbnail_id = '';
        $job_field_user = get_post_meta($job_id, 'jobsearch_field_job_posted_by', true);
        if (isset($job_field_user) && $job_field_user != '' && has_post_thumbnail($job_field_user)) {
            $post_thumbnail_id = get_post_thumbnail_id($job_field_user);
        }
        return $post_thumbnail_id;
    }

}

if (!function_exists('jobsearch_job_get_company_name')) {

    function jobsearch_job_get_company_name($job_id, $before_title = '', $after_title = '') {
        $company_name_str = '';
        $job_field_user = get_post_meta($job_id, 'jobsearch_field_job_posted_by', true);
        if (isset($job_field_user) && $job_field_user != '') {
            $company_name_str = '<a href="' . get_permalink($job_field_user) . '">' . $before_title . get_the_title($job_field_user) . $after_title . '</a>';
        }
        return $company_name_str;
    }

}

if (!function_exists('jobsearch_check_job_approved_active')) {

    function jobsearch_check_job_approved_active($job_id) {
        $current_time = strtotime(current_time('Y-m-d H:i:s'));
        $job_status = get_post_meta($job_id, 'jobsearch_field_job_status', true);
        $job_expiry = get_post_meta($job_id, 'jobsearch_field_job_expiry_date', true);
        $job_employer = get_post_meta($job_id, 'jobsearch_field_job_posted_by', true);

        if ($job_status == 'approved' && $job_expiry > $current_time && $job_employer > 0) {
            return true;
        }
        return false;
    }

}

function jobsearch_get_job_salary_format($job_id = 0, $price = 0, $cur_tag = '') {

    global $jobsearch_currencies_list, $jobsearch_plugin_options;
    $job_custom_currency_switch = isset($jobsearch_plugin_options['job_custom_currency']) ? $jobsearch_plugin_options['job_custom_currency'] : '';
    $job_currency = get_post_meta($job_id, 'jobsearch_field_job_salary_currency', true);
    if ($job_currency != 'default' && $job_custom_currency_switch == 'on') {
        $job_currency = isset($jobsearch_currencies_list[$job_currency]['symbol']) ? $jobsearch_currencies_list[$job_currency]['symbol'] : jobsearch_get_currency_symbol();
    } else {
        $job_currency = 'default';
    }
    $cur_pos = get_post_meta($job_id, 'jobsearch_field_job_salary_pos', true);
    $job_salary_sep = get_post_meta($job_id, 'jobsearch_field_job_salary_sep', true);
    $job_salary_deci = get_post_meta($job_id, 'jobsearch_field_job_salary_deci', true);

    $job_salary_deci = $job_salary_deci < 10 ? absint($job_salary_deci) : 2;

    if ($job_currency == 'default') {
        $ret_price = jobsearch_get_price_format($price);
    } else {
        $price = $price > 0 ? trim($price) : 0;
        $price = preg_replace("/[^0-9.]+/iu", "", $price);
        if ($cur_pos == 'left_space') {
            $ret_price = ($cur_tag != '' ? '<' . $cur_tag . '>' : '') . $job_currency . ' ' . ($cur_tag != '' ? '</' . $cur_tag . '>' : '') . number_format($price, $job_salary_deci, ".", $job_salary_sep);
        } else if ($cur_pos == 'right') {
            $ret_price = number_format($price, $job_salary_deci, ".", $job_salary_sep) . ($cur_tag != '' ? '<' . $cur_tag . '>' : '') . $job_currency . ($cur_tag != '' ? '</' . $cur_tag . '>' : '');
        } else if ($cur_pos == 'right_space') {
            $ret_price = number_format($price, $job_salary_deci, ".", $job_salary_sep) . ($cur_tag != '' ? '<' . $cur_tag . '>' : '') . ' ' . $job_currency . ($cur_tag != '' ? '</' . $cur_tag . '>' : '');
        } else {
            $ret_price = ($cur_tag != '' ? '<' . $cur_tag . '>' : '') . $job_currency . ($cur_tag != '' ? '</' . $cur_tag . '>' : '') . number_format($price, $job_salary_deci, ".", $job_salary_sep);
        }
    }
    return $ret_price;
}

if (!function_exists('jobsearch_job_offered_salary')) {

    function jobsearch_job_offered_salary($job_id, $before_str = '', $after_str = '', $cur_tag = '', $pb_tag = '') {
        global $jobsearch_plugin_options, $sitepress;

        $lang_code = '';
        if (function_exists('icl_object_id') && function_exists('wpml_init_language_switcher')) {
            $lang_code = $sitepress->get_current_language();
        }

        $job_salary_types = isset($jobsearch_plugin_options['job-salary-types']) ? $jobsearch_plugin_options['job-salary-types'] : '';

        $salary_str = $before_str;
        $_job_salary_type = get_post_meta($job_id, 'jobsearch_field_job_salary_type', true);
        $_job_salary = get_post_meta($job_id, 'jobsearch_field_job_salary', true);
        $_job_max_salary = get_post_meta($job_id, 'jobsearch_field_job_max_salary', true);

        $salary_type_val_str = '';
        if (!empty($job_salary_types)) {
            $slar_type_count = 1;
            foreach ($job_salary_types as $job_salary_typ) {
                $job_salary_typ = apply_filters('wpml_translate_single_string', $job_salary_typ, 'JobSearch Options', 'Salary Type - ' . $job_salary_typ, $lang_code);
                if ($_job_salary_type == 'type_' . $slar_type_count) {
                    $salary_type_val_str = $job_salary_typ;
                }
                $slar_type_count++;
            }
        }

        $pb_strt_tag = '';
        $pb_clos_tag = '';
        if ($pb_tag != '') {
            $pb_strt_tag = '<' . $pb_tag . '>';
            $pb_clos_tag = '</' . $pb_tag . '>';
        }
        if ($_job_salary != '') {
            if ($_job_max_salary != '') {
                $salary_str .= jobsearch_get_job_salary_format($job_id, $_job_salary, $cur_tag) . ' - ' . jobsearch_get_job_salary_format($job_id, $_job_max_salary, $cur_tag) . ($salary_type_val_str != '' ? $pb_strt_tag . ' / ' . $salary_type_val_str . $pb_clos_tag : '');
            } else {
                $salary_str .= jobsearch_get_job_salary_format($job_id, $_job_salary, $cur_tag) . ($salary_type_val_str != '' ? $pb_strt_tag . ' / ' . $salary_type_val_str . $pb_clos_tag : '');
            }
        }
        $salary_str .= $after_str;
        return $salary_str;
    }

}

if (!function_exists('jobsearch_job_get_all_jobtypes')) {

    function jobsearch_job_get_all_jobtypes($job_id, $link_class = 'jobsearch-option-btn', $before_title = '', $after_title = '', $before_tag = '', $after_tag = '', $con_tag = 'a', $fill_type = 'bg_fill') {

        $job_type = wp_get_post_terms($job_id, 'jobtype');
        ob_start();
        $html = '';
        if (!empty($job_type)) {
            $link_class_str = '';
            if ($link_class != '') {
                $link_class_str = 'class="' . $link_class . '"';
            }
            echo ($before_tag);
            foreach ($job_type as $term) :
                $jobtype_color = get_term_meta($term->term_id, 'jobsearch_field_jobtype_color', true);
                $jobtype_textcolor = get_term_meta($term->term_id, 'jobsearch_field_jobtype_textcolor', true);
                $jobtype_color_str = '';
                if ($jobtype_color != '') {
                    if ($fill_type == 'border_fill') {
                        $jobtype_color_str = ' style="background-color: #ffffff; border-color: ' . esc_attr($jobtype_color) . '; color: ' . esc_attr($jobtype_color) . ' "';
                    } else {
                        $jobtype_color_str = ' style="background-color: ' . esc_attr($jobtype_color) . '; color: ' . esc_attr($jobtype_textcolor) . ' "';
                    }
                }
                ?>
                <<?php echo ($con_tag) ?> <?php echo ($link_class_str) ?> <?php echo ($jobtype_color_str); ?>>
                <?php
                echo ($before_title);
                echo esc_html($term->name);
                echo ($after_title);
                ?>
                </<?php echo ($con_tag) ?>>
                <?php
            endforeach;
            echo ($after_tag);
        }
        $html .= ob_get_clean();
        return $html;
    }

}

if (!function_exists('jobsearch_job_get_all_sectors')) {

    function jobsearch_job_get_all_sectors($job_id, $link_class = '', $before_title = '', $after_title = '', $before_tag = '', $after_tag = '') {

        global $jobsearch_plugin_options;
        $sectors = wp_get_post_terms($job_id, 'sector');
        ob_start();
        $html = '';
        if (!empty($sectors)) {
            $page_id = isset($jobsearch_plugin_options['jobsearch_search_list_page']) ? $jobsearch_plugin_options['jobsearch_search_list_page'] : '';
            $page_id = jobsearch__get_post_id($page_id, 'page');
            $page_id = jobsearch_wpml_lang_page_id($page_id, 'page');
            $result_page = get_permalink($page_id);
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
                echo ($before_title);
                ?>
                <a href="<?php echo add_query_arg(array('sector_cat' => $term->slug), $result_page); ?>" class="<?php echo force_balance_tags($link_class) ?>">
                    <?php
                    echo esc_html($term->name);
                    ?>
                </a>
                <?php
                echo ($after_title);
                $flag++;
            endforeach;
            echo ($after_tag);
        }
        $html .= ob_get_clean();
        return $html;
    }

}

if (!function_exists('jobsearch_job_get_all_skills')) {

    function jobsearch_job_get_all_skills($job_id, $seprator = '', $link_class = '', $before_title = '', $after_title = '', $before_tag = '', $after_tag = '') {

        global $jobsearch_plugin_options;

        $search_list_page = isset($jobsearch_plugin_options['jobsearch_search_list_page']) ? $jobsearch_plugin_options['jobsearch_search_list_page'] : '';
        $search_page_obj = $search_list_page != '' ? get_page_by_path($search_list_page, 'OBJECT', 'page') : '';

        $skills = wp_get_post_terms($job_id, 'skill');
        ob_start();
        $html = '';
        if (!empty($skills)) {
            $link_class_str = '';
            if ($link_class != '') {
                $link_class_str = 'class="' . $link_class . '"';
            }
            echo ($before_tag);
            $flag = 0;
            foreach ($skills as $term) :
                if ($flag > 0) {
                    echo $seprator;
                }
                $skill_page_url = '';
                if (isset($search_page_obj->ID)) {
                    $skill_page_url = add_query_arg(array('skill_in' => $term->slug), get_permalink($search_page_obj->ID));
                }
                ?>
                <a <?php echo ($skill_page_url != '' ? 'href="' . $skill_page_url . ' " ' : '') ?>class="<?php echo force_balance_tags($link_class) ?>">
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

if (!function_exists('jobsearch_job_related_post')) {

    function jobsearch_job_related_post($job_id, $title = '', $number_post = 5, $jobsearch_title_limit = 5, $job_like_class = '', $view = 'view1') {

        global $jobsearch_plugin_options;
        ob_start();
        $filter_arr2 = array();
        $sectors = wp_get_post_terms($job_id, 'sector');
        $filter_multi_spec_arr = array();
        if (!empty($sectors)) {
            foreach ($sectors as $term) :
                $filter_multi_spec_arr[] = $term->slug;
            endforeach;
        }
        $sectors_enable_switch = isset($jobsearch_plugin_options['sectors_onoff_switch']) ? $jobsearch_plugin_options['sectors_onoff_switch'] : '';
        $all_location_allow = isset($jobsearch_plugin_options['all_location_allow']) ? $jobsearch_plugin_options['all_location_allow'] : '';
        $job_types_switch = isset($jobsearch_plugin_options['job_types_switch']) ? $jobsearch_plugin_options['job_types_switch'] : '';
        $tax_query = array();
        if (!empty($filter_multi_spec_arr)) {
            $tax_query = array(
                'taxonomy' => 'sector',
                'field' => 'slug',
                'terms' => $filter_multi_spec_arr
            );
        }

        $featured_job_mypost = array(
            'posts_per_page' => $number_post,
            'post_type' => 'job',
            'order' => "DESC",
            'orderby' => 'post_date',
            'post_status' => 'publish',
            'fields' => 'ids',
            'post__not_in' => array($job_id),
            'meta_query' => array(
                array(
                    'key' => 'jobsearch_field_job_publish_date',
                    'value' => current_time('timestamp'),
                    'compare' => '<=',
                ),
                array(
                    'key' => 'jobsearch_field_job_expiry_date',
                    'value' => current_time('timestamp'),
                    'compare' => '>=',
                ),
                array(
                    'key' => 'jobsearch_field_job_status',
                    'value' => 'approved',
                    'compare' => '=',
                ),
            )
        );
        if (!empty($tax_query)) {
            $featured_job_mypost['tax_query'] = array($tax_query);
        }

        // Exclude expired jobs from listing
        $featured_job_mypost = apply_filters('jobsearch_jobs_listing_parameters', $featured_job_mypost);
        $featured_job_loop_count = new WP_Query($featured_job_mypost);
        $featuredjob_count_post = $featured_job_loop_count->found_posts;


        $related_jobs_output = '';
        if ($featuredjob_count_post > 0) {

            if ($view == 'view1') {
                if ($title != '') {
                    ?>
                    <div class="jobsearch-section-title"><h2><?php echo esc_html($title); ?></h2></div>
                    <?php
                }
                ob_start();
                ?>
                <div class="jobsearch-job jobsearch-joblisting-classic jobsearch-jobdetail-joblisting">
                    <ul class="jobsearch-row">
                        <?php
                        // getting if record not found
                        $cs_search_result_page = isset($cs_plugin_options['cs_search_result_page']) ? $cs_plugin_options['cs_search_result_page'] : '';
                        while ($featured_job_loop_count->have_posts()) : $featured_job_loop_count->the_post();
                            global $post;
                            $job_id = $post;
                            $job_random_id = rand(1111111, 9999999);
                            $post_thumbnail_id = jobsearch_job_get_profile_image($job_id);
                            $post_thumbnail_image = wp_get_attachment_image_src($post_thumbnail_id, 'jobsearch-job-medium');
                            $post_thumbnail_src = isset($post_thumbnail_image[0]) && esc_url($post_thumbnail_image[0]) != '' ? $post_thumbnail_image[0] : '';
                            $post_thumbnail_src = $post_thumbnail_src == '' ? jobsearch_no_image_placeholder() : $post_thumbnail_src;
                            $jobsearch_job_featured = get_post_meta($job_id, 'jobsearch_field_job_featured', true);
                            $company_name = jobsearch_job_get_company_name($job_id, '@ ');
                            $get_job_location = get_post_meta($job_id, 'jobsearch_field_location_address', true);

                            $job_city_title = '';
                            $get_job_city = get_post_meta($job_id, 'jobsearch_field_location_location3', true);
                            if ($get_job_city == '') {
                                $get_job_city = get_post_meta($job_id, 'jobsearch_field_location_location2', true);
                            }
                            if ($get_job_city == '') {
                                $get_job_city = get_post_meta($job_id, 'jobsearch_field_location_location1', true);
                            }

                            $job_city_tax = $get_job_city != '' ? get_term_by('slug', $get_job_city, 'job-location') : '';
                            if (is_object($job_city_tax)) {
                                $job_city_title = $job_city_tax->name;
                            }

                            $job_city_title = apply_filters('jobsearch_job_detail_relatjobs_location_str', $job_city_title, $job_id);

                            $job_type_str = jobsearch_job_get_all_jobtypes($job_id, 'jobsearch-option-btn');
                            $sector_str = jobsearch_job_get_all_sectors($job_id, '', '', '', '<li><i class="jobsearch-icon jobsearch-filter-tool-black-shape"></i>', '</li>');
                            ?>
                            <li class="jobsearch-column-12">
                                <div class="jobsearch-joblisting-classic-wrap">
                                    <?php if ($post_thumbnail_src != '') { ?>
                                        <figure>
                                            <a href="<?php echo esc_url(get_permalink($job_id)); ?>">
                                                <img src="<?php echo esc_url($post_thumbnail_src) ?>" alt="">
                                            </a>
                                        </figure>
                                    <?php } ?>
                                    <div class="jobsearch-joblisting-text">
                                        <div class="jobsearch-list-option">
                                            <h2>
                                                <a href="<?php echo esc_url(get_permalink($job_id)); ?>" title="<?php echo esc_html(get_the_title($job_id)); ?>">
                                                    <?php echo get_the_title($job_id); ?>
                                                </a>
                                                <?php
                                                if ($jobsearch_job_featured == 'on') {
                                                    ?>
                                                    <span><?php echo esc_html__('Featured', 'wp-jobsearch'); ?></span>
                                                    <?php
                                                }
                                                ?>  
                                            </h2>
                                            <ul>
                                                <?php if ($company_name != '') { ?>
                                                    <li><?php echo force_balance_tags($company_name); ?></li>
                                                    <?php
                                                }
                                                if (!empty($job_city_title) && $all_location_allow == 'on') {
                                                    ?>
                                                    <li><i class="jobsearch-icon jobsearch-maps-and-flags"></i><?php echo esc_html($job_city_title); ?></li>
                                                    <?php
                                                }
                                                if (!empty($sector_str) && $sectors_enable_switch == 'on') {
                                                    echo apply_filters('jobsearch_joblisting_sector_str_html', $sector_str, $job_id, '<li><i class="jobsearch-icon jobsearch-calendar"></i>', '</li>');
                                                }
                                                ?>  
                                            </ul>
                                        </div>
                                        <div class="jobsearch-job-userlist">
                                            <?php
                                            if ($job_type_str != '' && $job_types_switch == 'on') {
                                                echo force_balance_tags($job_type_str);
                                            }
                                            $figcaption_div = true;
                                            $book_mark_args = array(
                                                'job_id' => $job_id,
                                                'before_icon' => 'fa fa-heart-o',
                                                'after_icon' => 'fa fa-heart',
                                                'anchor_class' => $job_like_class
                                            );
                                            do_action('jobsearch_job_shortlist_button_frontend', $book_mark_args);
                                            ?>
                                        </div>
                                        <div class="clearfix"></div>
                                    </div>
                                </div>
                            </li>
                            <?php
                        endwhile;
                        wp_reset_postdata();
                        ?>
                    </ul>
                </div>
                <?php
                $rel_jobs_html = ob_get_clean();
                echo apply_filters('jobsearch_job_detail_related_jobs_html', $rel_jobs_html, $featured_job_loop_count);
                $related_jobs_output = ob_get_clean();
            } elseif ($view == 'view2') {
                ob_start();
                $rel_jobs_html = '';
                $related_args = array(
                    'title' => $title,
                    'featured_job_loop_count' => $featured_job_loop_count,
                    'job_types_switch' => $job_types_switch,
                    'all_location_allow' => $all_location_allow,
                );
                do_action('careerfy_job_detail_related_view2', $rel_jobs_html, $related_args);
                $rel_jobs_html = ob_get_clean();
                echo apply_filters('jobsearch_job_detail_related_jobs_html', $rel_jobs_html, $featured_job_loop_count);
                $related_jobs_output = ob_get_clean();
            } elseif ($view == 'view3') {
                ob_start();
                $rel_jobs_html = '';
                $related_args = array(
                    'title' => $title,
                    'featured_job_loop_count' => $featured_job_loop_count,
                    'job_types_switch' => $job_types_switch,
                    'all_location_allow' => $all_location_allow,
                );
                do_action('careerfy_job_detail_related_view3', $rel_jobs_html, $related_args);
                $rel_jobs_html = ob_get_clean();
                echo apply_filters('jobsearch_job_detail_related_jobs_html', $rel_jobs_html, $featured_job_loop_count);
                $related_jobs_output = ob_get_clean();
            } elseif ($view == 'view4') {

                ob_start();
                $rel_jobs_html = '';
                $related_args = array(
                    'title' => $title,
                    'featured_job_loop_count' => $featured_job_loop_count,
                    'job_types_switch' => $job_types_switch,
                    'all_location_allow' => $all_location_allow,
                );
                do_action('careerfy_job_detail_related_view4', $rel_jobs_html, $related_args);
                $rel_jobs_html = ob_get_clean();
                echo apply_filters('jobsearch_job_detail_related_jobs_html', $rel_jobs_html, $featured_job_loop_count);
                $related_jobs_output = ob_get_clean();
            }
        }



        return apply_filters('related_jobs', $related_jobs_output, $job_id);
    }

}

if (!function_exists('jobsearch_job_related_company_post')) {

    function jobsearch_job_related_company_post($job_id, $title = '', $number_post = 5, $jobsearch_title_limit = 5, $view = 'view1') {
        ob_start();
        global $jobsearch_plugin_options;

        $all_location_allow = isset($jobsearch_plugin_options['all_location_allow']) ? $jobsearch_plugin_options['all_location_allow'] : '';

        $filter_arr2 = array();
        $company_filter_arr = '';
        $job_posted_by = get_post_meta($job_id, 'jobsearch_field_job_posted_by', true);
        if (isset($job_posted_by) && $job_posted_by != '') {
            $company_filter_arr = array();
            $company_filter_arr = array(
                'key' => 'jobsearch_field_job_posted_by',
                'value' => $job_posted_by,
                'compare' => '=',
            );
        }
        $featured_job_mypost = array(
            'posts_per_page' => $number_post,
            'post_type' => 'job',
            'order' => "DESC",
            'orderby' => 'post_date',
            'post_status' => 'publish',
            'ignore_sticky_posts' => 1,
            'fields' => 'ids',
            'post__not_in' => array($job_id),
            'meta_query' => array(
                array(
                    'key' => 'jobsearch_field_job_publish_date',
                    'value' => current_time('timestamp'),
                    'compare' => '<=',
                ),
                array(
                    'key' => 'jobsearch_field_job_expiry_date',
                    'value' => current_time('timestamp'),
                    'compare' => '>=',
                ),
                array(
                    'key' => 'jobsearch_field_job_status',
                    'value' => 'approved',
                    'compare' => '=',
                ),
                $company_filter_arr
            )
        );

        // Exclude expired jobs from listing
        $featured_job_mypost = apply_filters('jobsearch_jobs_listing_parameters', $featured_job_mypost);
        $featured_job_loop_count = new WP_Query($featured_job_mypost);
        $featuredjob_count_post = $featured_job_loop_count->found_posts;

        if ($featuredjob_count_post > 0) {

            if ($view == 'view2') {
                $similar_args = array(
                    'title' => $title,
                    'jobsearch_title_limit' => $jobsearch_title_limit,
                    'featured_job_loop_count' => $featured_job_loop_count,
                    'all_location_allow' => $all_location_allow,
                );

                do_action('careerfy_similar_jobs', $similar_args);
            } else {
                ?>
                <div class="widget widget_view_jobs">
                    <?php
                    if ($title != '') {
                        ?>
                        <div class="jobsearch-widget-title"><h2><?php echo esc_html($title); ?></h2></div>
                    <?php } ?> 
                    <ul>
                        <?php
                        // getting if record not found
                        $cs_search_result_page = isset($cs_plugin_options['cs_search_result_page']) ? $cs_plugin_options['cs_search_result_page'] : '';
                        while ($featured_job_loop_count->have_posts()) : $featured_job_loop_count->the_post();
                            global $post;
                            $job_id = $post;
                            $get_job_location = get_post_meta($job_id, 'jobsearch_field_location_address', true);
                            $sector_str = jobsearch_job_get_all_sectors($job_id, '', '', '', '<span>', '</span>');
                            ?>
                            <li>
                                <h6>
                                    <a href="<?php echo esc_url(get_permalink($job_id)); ?>" title="<?php echo esc_html(get_the_title($job_id)); ?>">
                                        <?php echo esc_html(wp_trim_words(get_the_title($job_id), $jobsearch_title_limit)); ?>
                                    </a>    
                                </h6>
                                <?php
                                if (!empty($sector_str)) {
                                    echo force_balance_tags($sector_str);
                                }
                                if (!empty($get_job_location) && $all_location_allow == 'on') {
                                    ?>
                                    <small><?php echo esc_html($get_job_location); ?></small>
                                    <?php
                                }
                                ?>
                            </li> 
                            <?php
                        endwhile;
                        wp_reset_postdata();
                        ?>
                    </ul>
                    <a href="<?php echo esc_url(get_permalink($job_posted_by)); ?>" title="<?php echo esc_html(get_the_title($job_id)); ?>" class="widget_view_jobs_btn"><?php echo esc_html__('View all jobs', 'wp-jobsearch') ?> <i class="jobsearch-icon jobsearch-arrows32"></i></a>   
                </div>
                <?php
            }
        }



        $related_jobs_output = ob_get_clean();
        return apply_filters('jobsearch_sidebar_related_jobs', $related_jobs_output, $job_id);
    }

}

if (!function_exists('jobsearch_job_views_count')) {

    function jobsearch_job_views_count($postID) {
        $jobsearch_job_views_count = get_post_meta($postID, "jobsearch_job_views_count", true);
        if ($jobsearch_job_views_count == '') {
            $jobsearch_job_views_count = 0;
        }
        if (!isset($_COOKIE["jobsearch_job_views_count" . $postID])) {
            setcookie("jobsearch_job_views_count" . $postID, time() + 86400);
            $jobsearch_job_views_count = $jobsearch_job_views_count + 1;
            update_post_meta($postID, 'jobsearch_job_views_count', $jobsearch_job_views_count);
        }
    }

}
if (!function_exists('jobsearch_employer_views_count')) {

    function jobsearch_employer_views_count($postID) {
        $jobsearch_employer_views_count = get_post_meta($postID, "jobsearch_employer_views_count", true);
        if ($jobsearch_employer_views_count == '') {
            $jobsearch_employer_views_count = 0;
        }
        if (!isset($_COOKIE["jobsearch_employer_views_count" . $postID])) {
            setcookie("jobsearch_employer_views_count" . $postID, time() + 86400);
            $jobsearch_employer_views_count = $jobsearch_employer_views_count + 1;
            update_post_meta($postID, 'jobsearch_employer_views_count', $jobsearch_employer_views_count);
        }
    }

}

if (!function_exists('jobsearch_employer_total_jobs_posted')) {

    function jobsearch_employer_total_jobs_posted($employer_id) {
        $args = array(
            'post_type' => 'job',
            'posts_per_page' => '1',
            'post_status' => 'publish',
            'fields' => 'ids',
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

        return absint($total_jobs);
    }

}

if (!function_exists('jobsearch_job_send_message_employer_callback')) {

    function jobsearch_job_send_message_employer_callback() {
        global $jobsearch_plugin_options;
        $cnt_job_emp_wout_log = isset($jobsearch_plugin_options['job_emp_cntct_wout_login']) ? $jobsearch_plugin_options['job_emp_cntct_wout_login'] : '';

        $send_message_job_id = $_REQUEST['send_message_job_id'];
        $send_message_content = $_REQUEST['send_message_content'];
        $send_message_subject = $_REQUEST['send_message_subject'];
        $user_data = wp_get_current_user();
        // send to employer email 
        $cur_user_id = isset($user_data->ID) ? $user_data->ID : '';
        $user_candidate_id = jobsearch_get_user_candidate_id($cur_user_id);
        if ($user_candidate_id > 0 && $cnt_job_emp_wout_log != 'on') {
            $candidate_status = get_post_meta($user_candidate_id, 'jobsearch_field_candidate_approved', true);
            if ($candidate_status != 'on') {
                $error = 1;
                $msg = esc_html__('Your profile is not approved yet.', 'wp-jobsearch');
                echo json_encode(array('html' => $msg));
                wp_die();
            }
        }
        do_action('jobsearch_candidate_message_employer', $user_data, $send_message_job_id, $send_message_subject, $send_message_content);
        echo json_encode(array('html' => esc_html__('Your Message has been sent, will be contact you shortly', 'wp-jobsearch')));
        wp_die();
    }

    add_action('wp_ajax_jobsearch_job_send_message_employer', 'jobsearch_job_send_message_employer_callback');
    add_action('wp_ajax_nopriv_jobsearch_job_send_message_employer', 'jobsearch_job_send_message_employer_callback');
}


if (!function_exists('jobsearch_job_send_message_html_filter_callback')) {
    add_filter('jobsearch_job_send_message_html_filter', 'jobsearch_job_send_message_html_filter_callback', 10, 2);

    function jobsearch_job_send_message_html_filter_callback($html, $arg = array()) {
        global $jobsearch_plugin_options;
        extract(shortcode_atts(array(
            'job_employer_id' => '',
            'job_id' => '',
            'btn_class' => '',
            'btn_text' => '',
                        ), $arg));

        $job_det_contact_form = isset($jobsearch_plugin_options['job_det_contact_form']) ? $jobsearch_plugin_options['job_det_contact_form'] : '';
        $cnt_job_emp_wout_log = isset($jobsearch_plugin_options['emp_cntct_wout_login']) ? $jobsearch_plugin_options['emp_cntct_wout_login'] : '';

        $send_message_btn_class = 'jobsearch-open-signin-tab';
        if (is_user_logged_in()) {
            if (jobsearch_user_is_candidate()) {
                $send_message_btn_class = 'jobsearch-sendmessage-popup-btn';
            } else {
                $send_message_btn_class = 'jobsearch-sendmessage-messsage-popup-btn';
            }
        }

        $btn_class_new = 'jobsearch-sendmessage-btn';
        if (isset($btn_class) && !empty($btn_class)) {
            $btn_class_new = $btn_class;
        }
        $btn_text_new = esc_html__('Contact Employer', 'wp-jobsearch');
        if (isset($btn_text) && !empty($btn_text)) {
            $btn_text_new = $btn_text;
        }


        $view_from_pop = false;
        if (is_user_logged_in() && jobsearch_user_is_candidate()) {
            $view_from_pop = true;
        }
        if ($cnt_job_emp_wout_log == 'on') {
            $view_from_pop = true;
            $send_message_btn_class = 'jobsearch-sendmessage-popup-btn';
        }

        ob_start();
        if ($job_det_contact_form == 'on') {
            ?>
            <a href="javascript:void(0);" class="<?php echo esc_html($btn_class_new); ?> <?php echo esc_html($send_message_btn_class); ?>"><i class="jobsearch-icon jobsearch-envelope"></i> <?php echo ($btn_text_new) ?></a>
            <?php
            if ($view_from_pop) {
                $popup_args = array(
                    'job_employer_id' => $job_employer_id,
                    'job_id' => $job_id,
                );
                add_action('wp_footer', function () use ($popup_args) {

                    global $jobsearch_plugin_options;
                    extract(shortcode_atts(array(
                        'job_employer_id' => '',
                        'job_id' => ''
                                    ), $popup_args));
                    $send_message_form_rand = rand(1000, 99999);
                    ?>
                    <div class="jobsearch-modal fade" id="JobSearchModalSendMessage">
                        <div class="modal-inner-area">&nbsp;</div>
                        <div class="modal-content-area">
                            <div class="modal-box-area">
                                <span class="modal-close"><i class="fa fa-times"></i></span>
                                <div class="jobsearch-send-message-form">
                                    <form method="post" id="jobsearch_send_message_form<?php echo esc_html($send_message_form_rand); ?>">
                                        <div class="jobsearch-user-form">
                                            <ul>
                                                <li>
                                                    <label>
                                                        <?php echo esc_html__('Subject', 'wp-jobsearch'); ?>:
                                                    </label>
                                                    <div class="input-field">
                                                        <input type="text" name="send_message_subject" value="" />
                                                    </div>
                                                </li>
                                                <li>
                                                    <label>
                                                        <?php echo esc_html__('Message', 'wp-jobsearch'); ?>:
                                                    </label>
                                                    <div class="input-field">
                                                        <textarea name="send_message_content"></textarea>
                                                    </div>
                                                </li>
                                                <li>
                                                    <div class="input-field-submit">
                                                        <input type="submit" class="send-message-submit-btn" data-action="jobsearch_job_send_message_employer" data-randid="<?php echo esc_html($send_message_form_rand); ?>" name="send_message_content" value="<?php echo esc_html__('Send', 'wp-jobsearch'); ?>"/>
                                                        <?php jobsearch_terms_and_con_link_txt(); ?>
                                                    </div> 
                                                    <div class="message-box message-box-<?php echo esc_html($send_message_form_rand); ?>"></div> 
                                                    <input type="hidden" name="send_message_job_id" value="<?php echo absint($job_id); ?>" />
                                                </li>
                                            </ul>
                                        </div>
                                    </form>    
                                </div>

                            </div>
                        </div>
                    </div>
                    <?php
                }, 11, 1);
            } else {
                add_action('wp_footer', function () {
                    $rand_numb = rand(1000000, 9999999);
                    ?>
                    <div class="jobsearch-modal fade" id="JobSearchModalSendMessageWarning">
                        <div class="modal-inner-area">&nbsp;</div>
                        <div class="modal-content-area">
                            <div class="modal-box-area">
                                <span class="modal-close"><i class="fa fa-times"></i></span>
                                <div class="jobsearch-send-message-form">
                                    <div class="send-message-warning">
                                        <span><?php echo esc_html__("Required 'Candidate' login for send messasge", 'wp-jobsearch'); ?> </span>  
                                        <span><?php echo esc_html__("Click here to", 'wp-jobsearch'); ?> <a href="<?php echo wp_logout_url(get_permalink()); ?>"><?php echo esc_html__("logout", 'wp-jobsearch'); ?></a> </span>
                                        <span><?php echo esc_html__("And try again", 'wp-jobsearch'); ?> </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php
                });
            }
        }
        $html .= ob_get_clean();
        return $html;
    }

}

add_action('wp_footer', 'jobsearch_job_apply_btn_candidate_role_warning');

function jobsearch_job_apply_btn_candidate_role_warning() {
    ?>
    <div class="jobsearch-modal fade" id="JobSearchModalApplyJobWarning">
        <div class="modal-inner-area">&nbsp;</div>
        <div class="modal-content-area">
            <div class="modal-box-area">
                <span class="modal-close"><i class="fa fa-times"></i></span>
                <div class="jobsearch-send-message-form">
                    <div class="send-message-warning">
                        <span><?php echo esc_html__("Required 'Candidate' login for apply this job.", 'wp-jobsearch'); ?> </span>  
                        <span><?php echo esc_html__("Click here to", 'wp-jobsearch'); ?> <a href="<?php echo wp_logout_url(get_permalink()); ?>"><?php echo esc_html__("logout", 'wp-jobsearch'); ?></a> </span>
                        <span><?php echo esc_html__("And try again", 'wp-jobsearch'); ?> </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php
}

if (!function_exists('jobsearch_job_send_to_email_mail')) {
    add_action('wp_ajax_jobsearch_user_send_email_to_friend', 'jobsearch_job_send_to_email_mail');
    add_action('wp_ajax_nopriv_jobsearch_user_send_email_to_friend', 'jobsearch_job_send_to_email_mail');

    function jobsearch_job_send_to_email_mail() {
        $subject = isset($_POST['send_email_subject']) ? $_POST['send_email_subject'] : '';
        $email_msg = isset($_POST['send_email_content']) ? $_POST['send_email_content'] : '';
        $uemail = isset($_POST['send_email_to']) ? $_POST['send_email_to'] : '';
        $email_job = isset($_POST['send_email_job_id']) ? $_POST['send_email_job_id'] : '';

        $cnt_email = get_bloginfo('admin_email');

        if ($email_msg != '') {
            $email_msg = esc_html($email_msg);
        } else {
            $msg = esc_html__('Please type your Message.', 'wp-jobsearch');
            echo json_encode(array('msg' => $msg, 'error' => '1'));
            wp_die();
        }

        if ($uemail != '' && filter_var($uemail, FILTER_VALIDATE_EMAIL)) {
            $uemail = esc_html($uemail);
        } else {
            $msg = esc_html__('Please Enter a valid email.', 'wp-jobsearch');
            echo json_encode(array('msg' => $msg, 'error' => '1'));
            wp_die();
        }

        if ($subject != '') {
            $subject = esc_html($subject);
        } else {
            $msg = esc_html__('Please type the Subject.', 'wp-jobsearch');
            echo json_encode(array('msg' => $msg, 'error' => '1'));
            wp_die();
        }

        $send_msg = sprintf(__('Job Link: <a href="%s">%s</a>', 'wp-jobsearch'), get_permalink($email_job), get_the_title($email_job)) . ' <br><br> (' . get_permalink($email_job) . ')' . ' <br><br> ' . $email_msg;

        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        add_filter('wp_mail_content_type', function () {
            return 'text/html';
        });
        if (wp_mail($uemail, $subject, $send_msg)) {
            $msg = esc_html__('Mail sent successfully', 'wp-jobsearch');
            echo json_encode(array('msg' => $msg));
        } else {
            $msg = esc_html__('Error! There is some problem.', 'wp-jobsearch');
            echo json_encode(array('msg' => $msg, 'error' => '1'));
        }

        wp_die();
    }

}

if (!function_exists('jobsearch_job_send_to_email_callback')) {
    add_action('jobsearch_job_send_to_email_filter', 'jobsearch_job_send_to_email_callback', 10, 1);

    function jobsearch_job_send_to_email_callback($arg = array()) {
        extract(shortcode_atts(array(
            'job_id' => '',
            'btn_class' => '',
                        ), $arg));

        $btn_class_new = '';
        if (isset($btn_class) && !empty($btn_class)) {
            $btn_class_new = $btn_class;
        }
        ?>
        <a href="javascript:void(0);" class="<?php echo ($btn_class_new); ?> active jobsearch-send-email-popup-btn"><i class="jobsearch-icon jobsearch-envelope"></i> <?php echo esc_html__('Email Job', 'wp-jobsearch') ?></a>
        <?php
        $popup_args = array(
            'job_id' => $job_id,
        );
        add_action('wp_footer', function () use ($popup_args) {

            global $jobsearch_plugin_options;
            extract(shortcode_atts(array(
                'job_id' => ''
                            ), $popup_args));
            $send_message_form_rand = rand(100000, 999999);
            ?>
            <div class="jobsearch-modal fade" id="JobSearchSendEmailModal">
                <div class="modal-inner-area">&nbsp;</div>
                <div class="modal-content-area">
                    <div class="modal-box-area">
                        <span class="modal-close"><i class="fa fa-times"></i></span>
                        <div class="jobsearch-send-message-form">
                            <form method="post" id="jobsearch_send_to_email_form">
                                <div class="jobsearch-user-form">
                                    <ul>
                                        <li>
                                            <label>
                                                <?php echo esc_html__('Subject', 'wp-jobsearch'); ?>:
                                            </label>
                                            <div class="input-field">
                                                <input type="text" name="send_email_subject">
                                            </div>
                                        </li>
                                        <li>
                                            <label>
                                                <?php echo esc_html__('Email Address', 'wp-jobsearch'); ?>:
                                            </label>
                                            <div class="input-field">
                                                <input type="text" name="send_email_to">
                                            </div>
                                        </li>
                                        <li>
                                            <label>
                                                <?php echo esc_html__('Message', 'wp-jobsearch'); ?>:
                                            </label>
                                            <div class="input-field">
                                                <textarea name="send_email_content"></textarea>
                                            </div>
                                        </li>
                                        <li>
                                            <div class="input-field-submit">
                                                <input type="submit" class="send-message-submit-btn send-job-email-btn" value="<?php esc_html_e('Send', 'wp-jobsearch') ?>">
                                            </div> 
                                            <div class="send-email-loader-box"></div>
                                            <div class="send-email-msg-box"></div>
                                            <input type="hidden" name="send_email_job_id" value="<?php echo absint($job_id); ?>">
                                            <input type="hidden" name="action" value="jobsearch_user_send_email_to_friend">
                                            <?php
                                            jobsearch_terms_and_con_link_txt();
                                            ?>
                                        </li>
                                    </ul>
                                </div>
                            </form>    
                        </div>

                    </div>
                </div>
            </div>
            <?php
        }, 11, 1);
    }

}

if (!function_exists('jobsearch_jobs_update_cron_once')) {
    add_action('init', 'jobsearch_jobs_update_cron_once', 10);

    function jobsearch_jobs_update_cron_once() {
        $check_option = get_option('jobsearch_jobs_update_cron_once');

        if ($check_option == '') {
            $args = array(
                'post_type' => 'job',
                'posts_per_page' => '-1',
                'post_status' => 'publish',
                'fields' => 'ids',
                'order' => 'DESC',
                'orderby' => 'ID',
            );

            $jobs_query = new WP_Query($args);
            if (isset($jobs_query->posts) && !empty($jobs_query->posts)) {
                $all_jobs = $jobs_query->posts;
                foreach ($all_jobs as $job_id) {
                    $job_employer = get_post_meta($job_id, 'jobsearch_field_job_posted_by', true);
                    $employer_obj = get_post($job_employer);

                    if (is_object($employer_obj) && isset($employer_obj->ID)) {
                        $employer_status = get_post_meta($job_employer, 'jobsearch_field_employer_approved', true);
                        if ($employer_status == 'on') {
                            update_post_meta($job_id, 'jobsearch_job_employer_status', 'approved');
                        } else {
                            update_post_meta($job_id, 'jobsearch_job_employer_status', '');
                        }
                    }
                }
                update_option('jobsearch_jobs_update_cron_once', '1');
            }
        }
        //
    }

}

function jobsearch_remove_exfeatkeys_jobs_query($meta_query, $add_forc_feat = 'no') {
    $new_meta_qury = array();
    if (isset($meta_query[0]) && !empty($meta_query[0])) {
        foreach ($meta_query[0] as $meta_field) {
            if (isset($meta_field['key']) && $meta_field['key'] == 'jobsearch_field_job_featured') {
                continue;
            } else if (isset($meta_field[0]['key']) && $meta_field[0]['key'] == 'jobsearch_field_job_featured') {
                continue;
            } else {
                $new_meta_qury[] = $meta_field;
            }
        }
    }
    if ($add_forc_feat == 'yes') {
        $new_meta_qury[] = array(
            "key" => "jobsearch_field_job_featured",
            "value" => "on",
            "compare" => "="
        );
    }
    return $new_meta_qury;
}

if (!function_exists('jobsearch_employer_update_jobs_status')) {
    add_action('jobsearch_employer_update_jobs_status', 'jobsearch_employer_update_jobs_status', 10, 1);

    function jobsearch_employer_update_jobs_status($employer_id) {

        $employer_obj = get_post($employer_id);

        if (is_object($employer_obj) && isset($employer_obj->ID)) {

            $employer_status = get_post_meta($employer_id, 'jobsearch_field_employer_approved', true);

            $args = array(
                'post_type' => 'job',
                'posts_per_page' => '-1',
                'post_status' => 'publish',
                'fields' => 'ids',
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
            if (isset($jobs_query->posts) && !empty($jobs_query->posts)) {
                $all_jobs = $jobs_query->posts;
                foreach ($all_jobs as $job_id) {
                    if ($employer_status == 'on') {
                        update_post_meta($job_id, 'jobsearch_job_employer_status', 'approved');
                    } else {
                        update_post_meta($job_id, 'jobsearch_job_employer_status', '');
                    }
                }
            }
        }
    }

}

add_action('wp_ajax_jobsearch_admin_meta_job_apps_list', 'jobsearch_admin_meta_job_apps_list');

function jobsearch_admin_meta_job_apps_list() {

    $job_id = isset($_POST['job_id']) ? $_POST['job_id'] : '';
    $_job_applicants_list = get_post_meta($job_id, 'jobsearch_job_applicants_list', true);
    $_job_applicants_list = jobsearch_is_post_ids_array($_job_applicants_list, 'candidate');
    if (empty($_job_applicants_list)) {
        $_job_applicants_list = array();
    }

    $cand_args = array(
        'posts_per_page' => -1,
        'post_type' => 'candidate',
        'post_status' => 'publish',
        'fields' => 'ids',
        'order' => 'DESC',
        'orderby' => 'ID',
    );
    $all_cands_query = new WP_Query($cand_args);
    wp_reset_postdata();

    $all_cands_list = $all_cands_query->posts;

    ob_start();
    ?>
    <select name="job_all_apps_list[]" multiple="multiple" class="applicants-selectize" placeholder="<?php esc_html_e('Select Candidates', 'wp-jobsearch') ?>">
        <?php
        if (!empty($all_cands_list)) {
            foreach ($all_cands_list as $candidate_id) {
                ?>
                <option value="<?php echo ($candidate_id) ?>" <?php echo (in_array($candidate_id, $_job_applicants_list) ? 'selected="selected"' : '') ?>><?php echo get_the_title($candidate_id) ?></option>
                <?php
            }
        }
        ?>
    </select>
    <script>
        jQuery('.applicants-selectize').selectize({
            plugins: ['remove_button'],
        });
    </script>
    <?php
    $html = ob_get_clean();

    echo json_encode(array('html' => $html));
    die;
}

function jobsearch_remov_job_applicant_bycid($job_id, $candidate_id) {
    if ($job_id > 0 && $candidate_id > 0) {

        $user_id = jobsearch_get_candidate_user_id($candidate_id);

        $job_applicants_list = get_post_meta($job_id, 'jobsearch_job_applicants_list', true);
        $job_applicants_list = jobsearch_is_post_ids_array($job_applicants_list, 'candidate');
        if (empty($job_applicants_list)) {
            $job_applicants_list = array();
        }

        if (!empty($job_applicants_list)) {
            //

            if (($key = array_search($candidate_id, $job_applicants_list)) !== false) {
                unset($job_applicants_list[$key]);

                $job_applicants_list = implode(',', $job_applicants_list);
                update_post_meta($job_id, 'jobsearch_job_applicants_list', $job_applicants_list);
                jobsearch_remove_user_meta_list($job_id, 'jobsearch-user-jobs-applied-list', $user_id);
            }
        }
    }
}

//
add_action('wp_ajax_jobsearch_admin_meta_job_apps_list_save', 'jobsearch_admin_meta_job_apps_listsave');

function jobsearch_admin_meta_job_apps_listsave() {

    $all_apps = isset($_POST['all_apps']) ? $_POST['all_apps'] : '';
    $job_id = isset($_POST['job_id']) ? $_POST['job_id'] : '';

    $job_applicants_list = get_post_meta($job_id, 'jobsearch_job_applicants_list', true);
    $job_applicants_list = jobsearch_is_post_ids_array($job_applicants_list, 'candidate');
    if (empty($job_applicants_list)) {
        $job_applicants_list = array();
    }

    if (!empty($job_applicants_list)) {
        foreach ($job_applicants_list as $jobapp_id) {
            jobsearch_remov_job_applicant_bycid($job_id, $jobapp_id);
        }
    }

    $sjob_applicants_list = array();
    if (!empty($all_apps)) {
        foreach ($all_apps as $app_id) {
            $cand_user_id = jobsearch_get_candidate_user_id($app_id);
            jobsearch_create_user_meta_list($job_id, 'jobsearch-user-jobs-applied-list', $cand_user_id);

            //
            if (!in_array($app_id, $sjob_applicants_list)) {
                $sjob_applicants_list[] = $app_id;
            }
        }
    }
    if (!empty($sjob_applicants_list)) {
        $sjob_applicants_list = implode(',', $sjob_applicants_list);
    } else {
        $sjob_applicants_list = '';
    }
    update_post_meta($job_id, 'jobsearch_job_applicants_list', $sjob_applicants_list);

    $msg = esc_html__('Applicants Saved', 'wp-jobsearch');

    echo json_encode(array('msg' => $msg));
    die;
}

if (!function_exists('jobsearch_job_listing_custom_fields_callback')) {

    function jobsearch_job_listing_custom_fields_callback($atts = array()) {


        //print_r($atts);

        $job_custom_fields_switch = isset($atts['job_custom_fields_switch']) ? $atts['job_custom_fields_switch'] : '';
        //echo $job_elem_custom_fields = isset($atts['job_elem_custom_fields']) ? $atts['job_elem_custom_fields'] : '';
        //echo 'Shataka';
        if ($job_custom_fields_switch == 'yes') {
            //echo 'Shataka';
        }
    }

    add_action('jobsearch_job_listing_custom_fields', 'jobsearch_job_listing_custom_fields_callback', 10, 1);
}

if (!function_exists('jobsearch_job_listing_deadline_callback')) {

    function jobsearch_job_listing_deadline_callback($atts = array(), $job_id = '') {

        //echo $job_id;

        echo $job_deadline_date = get_post_meta($job_id, 'jobsearch_field_job_application_deadline_date', true);
        $job_deadline_switch = isset($atts['job_deadline_switch']) ? $atts['job_deadline_switch'] : '';

        $jobsearch_last_date_formated = date_i18n(get_option('date_format'), strtotime($job_deadline_date));

        if ($job_deadline_switch == 'Yes') {
            echo '<li><i class="jobsearch-icon jobsearch-calendar"></i>' . $jobsearch_last_date_formated . '</li>';
        }
    }

    add_action('jobsearch_job_listing_deadline', 'jobsearch_job_listing_deadline_callback', 10, 2);
}

//
add_action('jobsearch_job_listin_sh_after_jobs_found', 'jobsearch_jobs_listin_totaljobs_found_html', 10, 3);
add_filter('jobsearch_job_listin_top_jobfounds_html', 'jobsearch_jobs_listin_top_jobfounds_html', 10, 4);
add_filter('jobsearch_job_listin_before_top_jobfounds_html', 'jobsearch_jobs_listin_before_top_jobfounds_html', 10, 4);
add_filter('jobsearch_job_listin_after_sort_orders_html', 'jobsearch_jobs_listin_after_top_jobsorts_html', 10, 4);

function jobsearch_jobs_listin_totaljobs_found_html($job_totnum, $job_short_counter, $atts) {

    $counts_on = true;
    if (isset($atts['display_per_page']) && $atts['display_per_page'] == 'no') {
        $counts_on = false;
    }
    if ($counts_on) {
        $per_page = isset($atts['job_per_page']) && absint($atts['job_per_page']) > 0 ? $atts['job_per_page'] : 0;
        if (isset($_REQUEST['per-page']) && $_REQUEST['per-page'] > 1) {
            $per_page = $_REQUEST['per-page'];
        }
        if ($per_page > 1) {
            $page_num = isset($_REQUEST['job_page']) && $_REQUEST['job_page'] > 1 ? $_REQUEST['job_page'] : 1;
            $start_frm = $page_num > 1 ? (($page_num - 1) * $per_page) : 1;
            $offset = $page_num > 1 ? ($page_num * $per_page) : $per_page;

            $offset = $offset > $job_totnum ? $job_totnum : $offset;
            $strt_toend_disp = absint($job_totnum) > 0 ? ($start_frm > 1 ? ($start_frm + 1) : $start_frm) . ' - ' . $offset : '0';
            ?>
            <div class="displayed-here"><?php printf(esc_html__('Displayed Here : %s Jobs', 'wp-jobsearch'), $strt_toend_disp) ?></div>
            <?php
        } else {
            $per_page = isset($atts['job_per_page']) && absint($atts['job_per_page']) > 0 ? $atts['job_per_page'] : $job_totnum;
            $per_page = $per_page > $job_totnum ? $job_totnum : $per_page;

            $strt_toend_disp = absint($job_totnum) > 0 ? '1 - ' . $per_page : '0';
            ?>
            <div class="displayed-here"><?php printf(esc_html__('Displayed Here : %s Jobs', 'wp-jobsearch'), $strt_toend_disp) ?></div>
            <?php
        }
    }
}

function jobsearch_jobs_listin_top_jobfounds_html($html, $job_totnum, $job_short_counter, $atts) {
    $counts_on = true;
    if (isset($atts['display_per_page']) && $atts['display_per_page'] == 'no') {
        $counts_on = false;
    }
    if ($counts_on) {
        $html = '';
    }
    return $html;
}

function jobsearch_jobs_listin_before_top_jobfounds_html($html, $job_totnum, $job_short_counter, $atts) {
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
                    echo esc_html__('Job Found', 'wp-jobsearch');
                } else {
                    echo esc_html__('Jobs Found', 'wp-jobsearch');
                }
                do_action('jobsearch_job_listin_sh_after_jobs_found', $job_totnum, $job_short_counter, $atts);
                ?>
            </h2>
        </div>
        <?php
        echo '<div class="jobsearch-topsort-holder">';
        $html = ob_get_clean();
    }
    return $html;
}

function jobsearch_jobs_listin_after_top_jobsorts_html($html, $job_totnum, $job_short_counter, $atts) {
    $counts_on = true;
    if (isset($atts['display_per_page']) && $atts['display_per_page'] == 'no') {
        $counts_on = false;
    }
    if ($counts_on) {
        $html = '</div>';
    }
    return $html;
}

function jobsearch_job_expiry_cron_set_callback($job_id, $user_id) {
    $up_post = array(
        'ID' => $job_id,
        'post_status' => 'draft',
    );
    wp_update_post($up_post);
    update_post_meta($job_id, 'jobsearch_field_job_status', 'pending');

    //
    $c_user = get_user_by('ID', $user_id);
    do_action('jobsearch_job_expire_to_employer', $c_user, $job_id);
}

//add_action('jobsearch_job_expiry_cron_event', 'jobsearch_job_expiry_cron_set_callback', 10, 2);

function jobsearch_google_job_posting($job_id) {
    global $jobsearch_currencies_list, $jobsearch_gdapi_allocation;
    $jobsearch__options = get_option('jobsearch_plugin_options');
    $google_jobs_posting = isset($jobsearch__options['google_jobs_posting']) ? $jobsearch__options['google_jobs_posting'] : '';
    
    $all_locations_type = isset($jobsearch__options['all_locations_type']) ? $jobsearch__options['all_locations_type'] : '';

    if ($google_jobs_posting == 'on') {
        $job_title = get_the_title($job_id);
        $job_obj = get_post($job_id);
        $job_desc = isset($job_obj->post_content) ? $job_obj->post_content : '';
        $job_desc = apply_filters('the_content', $job_desc);

        $job_employer_id = get_post_meta($job_id, 'jobsearch_field_job_posted_by', true);
        $employer_post = get_post($job_employer_id);
        if (isset($employer_post->ID)) {
            $emp_user_id = jobsearch_get_employer_user_id($job_employer_id);
            $emp_user_obj = get_user_by('ID', $emp_user_id);
            $emp_user_url = isset($emp_user_obj->user_url) ? $emp_user_obj->user_url : '';
        }
        if (isset($emp_user_url) && $emp_user_url != '') {
            $emp_user_url = $emp_user_url;
        } else {
            $emp_user_url = home_url();
        }
        $employer_name = get_the_title($job_employer_id);
        $post_thumbnail_id = jobsearch_job_get_profile_image($job_id);
        $post_thumbnail_image = wp_get_attachment_image_src($post_thumbnail_id, 'medium');
        $emp_thumbnail_src = isset($post_thumbnail_image[0]) && esc_url($post_thumbnail_image[0]) != '' ? $post_thumbnail_image[0] : '';
        $emp_thumbnail_src = $emp_thumbnail_src == '' ? jobsearch_no_image_placeholder() : $emp_thumbnail_src;

        $get_job_contry = get_post_meta($job_id, 'jobsearch_field_location_location1', true);
        $get_job_region = get_post_meta($job_id, 'jobsearch_field_location_location2', true);
        $get_job_full_adres = get_post_meta($job_id, 'jobsearch_field_location_address', true);
        
        if ($all_locations_type == 'api') {
            $api_contries_list = $jobsearch_gdapi_allocation::get_countries();
            if ($get_job_contry != '' && in_array($get_job_contry, $api_contries_list)) {
                $get_job_contry = array_search($get_job_contry, $api_contries_list);
            }
        }

        $adres_locality = explode(', ', $get_job_full_adres);
        $adres_locality = isset($adres_locality[0]) && $adres_locality[0] != '' ? ', ' . $adres_locality[0] : $get_job_full_adres;

        $woo_currency = get_option('woocommerce_currency');

        $job_currency = get_post_meta($job_id, 'jobsearch_field_job_salary_currency', true);
        $job_currency = ($job_currency != '' && $job_currency != 'default' ? $job_currency : $woo_currency);

        if ($job_currency == '') {
            $job_currency = 'USD';
        }

        $_job_salary = get_post_meta($job_id, 'jobsearch_field_job_salary', true);

        $_job_salary = $_job_salary > 0 ? $_job_salary : 0;
        $_job_salary = str_replace(array(','), array(''), $_job_salary);

        $job_posted_date = get_post_meta($job_id, 'jobsearch_field_job_publish_date', true);
        $job_expiry_date = get_post_meta($job_id, 'jobsearch_field_job_expiry_date', true);

        if ($job_title != '' && $job_desc != '' && $job_posted_date > 0 && $job_expiry_date > 0) {
            ?>
            <script type="application/ld+json"> {
                "@context" : "http://schema.org/",
                "@type" : "JobPosting",
                "title" : "<?php echo ($job_title) ?>",
                "description" : "<?php echo ($job_desc) ?>",
                "identifier": {
                "@type": "PropertyValue",
                "name": "<?php echo ($employer_name) ?>",
                "value": "<?php echo ($job_employer_id) ?>"
                },
                "datePosted" : "<?php echo date('Y-m-d', $job_posted_date) ?>",
                "validThrough" : "<?php echo date('Y-m-d', $job_expiry_date) ?>T<?php echo date('H:i', $job_expiry_date) ?>",
                "employmentType" : "CONTRACTOR",
                "hiringOrganization" : {
                "@type" : "Organization",
                "name" : "<?php echo ($employer_name) ?>",
                "sameAs" : "<?php echo esc_url($emp_user_url) ?>",
                "logo" : "<?php echo ($emp_thumbnail_src) ?>"
                },
                "jobLocation": {
                "@type": "Place",
                "address": {
                "@type": "PostalAddress",
                "streetAddress": "<?php echo ($get_job_full_adres) ?>",
                "addressLocality": "<?php echo ($adres_locality) ?>",
                "addressRegion": "<?php echo ($get_job_region) ?>",
                "postalCode": "00000",
                "addressCountry": "<?php echo ($get_job_contry) ?>"
                }
                },
                "baseSalary": {
                "@type": "MonetaryAmount",
                "currency": "<?php echo ($job_currency) ?>",
                "value": {
                "@type": "QuantitativeValue",
                "value": <?php echo ($_job_salary) ?>,
                "unitText": "HOUR"
                }
                }
                }
            </script>
            <?php
        }
    }
}
