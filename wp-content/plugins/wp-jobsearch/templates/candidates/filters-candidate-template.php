<?php
global $jobsearch_plugin_options;

$output = '';
$left_filter_count_switch = 'no';

$filters_op_sort = isset($jobsearch_plugin_options['cand_srch_filtrs_sort']) ? $jobsearch_plugin_options['cand_srch_filtrs_sort'] : '';

$filters_op_sort = isset($filters_op_sort['fields']) ? $filters_op_sort['fields'] : '';
?>
<div class="jobsearch-column-3 jobsearch-typo-wrap">
    <?php
    $sh_atts = isset($candidate_arg['atts']) ? $candidate_arg['atts'] : '';
    if (isset($sh_atts['candidate_filters_count']) && $sh_atts['candidate_filters_count'] == 'yes') {
        $left_filter_count_switch = 'yes';
    }

    if (isset($filters_op_sort['date_posted'])) {
        foreach ($filters_op_sort as $filter_sort_key => $filter_sort_val) {
            if ($filter_sort_key == 'date_posted') {
                $output .= apply_filters('jobsearch_candidate_filter_date_posted_box_html', '', $global_rand_id, $args_count, $left_filter_count_switch, $sh_atts);
            } else if ($filter_sort_key == 'sector') {
                $output .= apply_filters('jobsearch_candidate_filter_sector_box_html', '', $global_rand_id, $args_count, $left_filter_count_switch, $sh_atts);
            } else if ($filter_sort_key == 'job_type') {
                $output .= apply_filters('jobsearch_candidate_filter_candidatetype_box_html', '', $global_rand_id, $args_count, $left_filter_count_switch, $sh_atts);
            } else if ($filter_sort_key == 'custom_fields') {
                $output .= apply_filters('jobsearch_custom_fields_filter_box_html', '', 'candidate', $global_rand_id, $args_count, $left_filter_count_switch, 'jobsearch_candidate_content_load');
            }
        }
    } else {

        /*
         * add filter box for location fields filter 
         */
        //$output .= apply_filters('jobsearch_location_field_filter_box_html', '', 'candidate', $global_rand_id, $args_count, 'jobsearch_candidate_content_load');
        /*
         * add filter box for date posted filter 
         */
        $output .= apply_filters('jobsearch_candidate_filter_date_posted_box_html', '', $global_rand_id, $args_count, $left_filter_count_switch, $sh_atts);
        /*
         * add filter box for candidate types filter 
         */
        $output .= apply_filters('jobsearch_candidate_filter_candidatetype_box_html', '', $global_rand_id, $args_count, $left_filter_count_switch, $sh_atts);
        /*
         * add filter box for sectors filter 
         */
        $output .= apply_filters('jobsearch_candidate_filter_sector_box_html', '', $global_rand_id, $args_count, $left_filter_count_switch, $sh_atts);
        /*
         * add filter box for custom fields filter 
         */
        $output .= apply_filters('jobsearch_custom_fields_filter_box_html', '', 'candidate', $global_rand_id, $args_count, $left_filter_count_switch, 'jobsearch_candidate_content_load');
        /*
         * add filter box for location fields filter 
         */
        // $output .= apply_filters('jobsearch_location_field_filter_box_html', '', 'candidate', $global_rand_id, $args_count, 'jobsearch_candidate_content_load');
    }

    echo apply_filters('jobsearch_candidate_listing_filters_output', $output, $global_rand_id, $args_count, $left_filter_count_switch, $sh_atts);
    ?>
</div>
