<?php
/**
 * The template for displaying all single posts.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 */
global $post, $jobsearch_plugin_options;
$job_id = $post->ID;

$allow_page_access = false;
if (is_user_logged_in() && current_user_can('administrator')) {
    $allow_page_access = true;
}
$job_employer = get_post_meta($job_id, 'jobsearch_field_job_posted_by', true);
if (is_user_logged_in()) {
    $user_id = get_current_user_id();
    $user_is_employer = jobsearch_user_is_employer($user_id);
    if ($user_is_employer) {
        $employer_id = jobsearch_get_user_employer_id($user_id);
        if ($employer_id == $job_employer) {
            $allow_page_access = true;
        }
    }
}

$job_expiry_date = get_post_meta($job_id, 'jobsearch_field_job_expiry_date', true);
$job_status = get_post_meta($job_id, 'jobsearch_field_job_status', true);

if ($job_expiry_date < current_time('timestamp') && !$allow_page_access) {
    wp_redirect(home_url('/'));
}
if ($job_status != 'approved' && !$allow_page_access) {
    wp_redirect(home_url('/'));
}

$job_view = isset($jobsearch_plugin_options['jobsearch_job_detail_views']) && !empty($jobsearch_plugin_options['jobsearch_job_detail_views']) ? $jobsearch_plugin_options['jobsearch_job_detail_views'] : 'view1';

jobsearch_job_views_count($job_id);
get_header();

$job_view = apply_filters('careerfy_job_detail_page_style_display',$job_view,$job_id);

jobsearch_get_template_part($job_view, 'job', 'detail-pages/job');

do_action('jobsearch_job_detail_before_footer', $job_id);
get_footer();
