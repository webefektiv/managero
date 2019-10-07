<?php

/**
 * The template for displaying all single posts.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 */
global $post, $jobsearch_plugin_options;
$candidate_id = $post->ID;
do_action('jobsearch_user_profile_before', $candidate_id);
$cand_view = isset($jobsearch_plugin_options['jobsearch_cand_detail_views']) && !empty($jobsearch_plugin_options['jobsearch_cand_detail_views']) ? $jobsearch_plugin_options['jobsearch_cand_detail_views'] : 'view1';
get_header();
$cand_view = apply_filters('careerfy_cand_detail_page_style_display', $cand_view, $candidate_id);
jobsearch_get_template_part($cand_view, 'candidate', 'detail-pages/candidate');
get_footer();
