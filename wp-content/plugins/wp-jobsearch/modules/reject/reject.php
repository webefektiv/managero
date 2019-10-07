<?php
/**
 * Directory Plus Rejects Module
 */
// Direct access not allowed.
if (!defined('ABSPATH')) {
    exit;
}

if (!class_exists('Jobsearch_Reject')) {

    class Jobsearch_Reject {

        /**
         * Start construct Functions
         */
        public function __construct() {

            // Initialize Addon
            add_action('init', array($this, 'init'));
        }

        /**
         * Initialize application, load text domain, enqueue scripts and bind hooks
         */
        public function init() {

            // Add actions
            add_action('jobsearch_job_reject_button_frontend', array($this, 'jobsearch_job_reject_button_callback'), 11, 1);
            add_action('wp_ajax_jobsearch_add_candidate_job_to_reject', array($this, 'jobsearch_reject_submit_callback'), 11);
            add_action('wp_ajax_jobsearch_remv_candidate_job_frm_reject', array($this, 'jobsearch_removed_reject_callback'), 11);
            add_action('wp_enqueue_scripts', array($this, 'jobsearch_reject_enqueue_scripts'), 10);
        }

// <a href="#" class="widget_jobdetail_three_apply_btn"><i class="careerfy-icon careerfy-heart"></i> Save this Job</a>
        public function jobsearch_reject_enqueue_scripts() {
            global $sitepress;

            $admin_ajax_url = admin_url('admin-ajax.php');
            if (function_exists('icl_object_id') && function_exists('wpml_init_language_switcher')) {
                $lang_code = $sitepress->get_current_language();
                $admin_ajax_url = add_query_arg(array('lang' => $lang_code), $admin_ajax_url);
            }

            // Enqueue JS 
            wp_register_script('jobsearch-reject-functions-script', plugins_url('assets/js/reject-functions.js', __FILE__), '', '', true);
            wp_localize_script('jobsearch-reject-functions-script', 'jobsearch_reject_vars', array(
                'admin_url' => $admin_ajax_url,
                'plugin_url' => jobsearch_plugin_get_url(),
            ));
            wp_enqueue_script('jobsearch-reject-functions-script');
        }

        /**
         * Member Rejects Frontend Button
         * @ rejects frontend buuton based on job id
         */
        public function jobsearch_job_reject_button_callback($args = '') {
            wp_enqueue_script('jobsearch-reject-script');

            $job_id = isset($args['job_id']) ? $args['job_id'] : '';
            $before_icon = isset($args['before_icon']) ? $args['before_icon'] : '';
            $after_icon = isset($args['after_icon']) ? $args['after_icon'] : '';
            $before_label = isset($args['before_label']) ? $args['before_label'] : '';
            $after_label = isset($args['after_label']) ? $args['after_label'] : '';
            $container_class = isset($args['container_class']) ? $args['container_class'] : '';
            $anchor_class = isset($args['anchor_class']) ? $args['anchor_class'] : '';
            $view = isset($args['view']) ? $args['view'] : '';

            if ($anchor_class == '' && $view != 'job_detail') {
                $anchor_class = 'jobsearch-job-like';
            }
            if ($view == 'job_detail_3') {
                if (!is_user_logged_in()) {
                    ?>
                    <a href="javascript:void(0);" class="<?php echo ($anchor_class); ?> jobsearch-open-signin-tab"><?php esc_html_e('Job Neinteresant','managero'); ?></a>
                    <?php
                } else {
                    $user_id = get_current_user_id();
                    $user_is_candidate = jobsearch_user_is_candidate($user_id);
                    $candidate_rej_jobs_list = array();
                    if ($user_is_candidate) {
                        $candidate_id = jobsearch_get_user_candidate_id($user_id);
                        $candidate_rej_jobs_list = get_post_meta($candidate_id, 'jobsearch_rej_jobs_list', true);
                        $candidate_rej_jobs_list = explode(',', $candidate_rej_jobs_list);
                    }
                    ?>
                    <a href="javascript:void(0);" data-after-label="<?php echo ($after_label); ?>" data-view="job3" class="<?php echo in_array($job_id, $candidate_rej_jobs_list) ? '' : 'jobsearch-add-job-to-reject' ?> <?php echo ($anchor_class); ?>" data-id="<?php echo ($job_id); ?>" data-before-icon='<?php echo ($before_icon); ?>' data-after-icon='<?php echo ($after_icon); ?>'>
                        <i class="<?php echo in_array($job_id, $candidate_rej_jobs_list) ? $before_icon : $after_icon ?>"></i>
                    <?php // echo in_array($job_id, $candidate_rej_jobs_list) ? ($after_label) : ($before_label); ?>
                    </a>
                    <span class="job-to-fav-msg-con"></span>

                    <?php


                }
            } elseif ($view == 'job_detail') {
                if (!is_user_logged_in()) {
                    ?>
                    <a href="javascript:void(0);" class="reject_job_btn jobsearch-open-signin-tab"><?php echo ($before_label); ?></a>
                    <?php
                } else {
                    $user_id = get_current_user_id();
                    $user_is_candidate = jobsearch_user_is_candidate($user_id);
                    $candidate_rej_jobs_list = array();
                    if ($user_is_candidate) {
                        $candidate_id = jobsearch_get_user_candidate_id($user_id);
                        $candidate_rej_jobs_list = get_post_meta($candidate_id, 'jobsearch_rej_jobs_list', true);
                        $candidate_rej_jobs_list = explode(',', $candidate_rej_jobs_list);
                    }
                    ?>
                    <a href="javascript:void(0);" data-after-label="<?php echo ($after_label); ?>" data-view="job2" class="reject_job_btn <?php echo in_array($job_id, $candidate_rej_jobs_list) ? '' : 'jobsearch-add-job-to-reject' ?>" data-id="<?php echo ($job_id); ?>" data-before-icon="<?php echo ($before_icon); ?>" data-after-icon="<?php echo ($after_icon); ?>">
                    <?php echo '<i class=""></i>';
                    echo in_array($job_id, $candidate_rej_jobs_list) ? ($after_label) : ($before_label); ?>
                    </a>
                    <span class="job-to-fav-msg-con"></span>

                    <?php
                }
            } else {

                if (!is_user_logged_in()) {
                    ?>
                    <div class="like-btn <?php echo ($container_class) ?>">
                        <a href="javascript:void(0);" class="reject jobsearch-open-signin-tab <?php echo ($anchor_class) ?>">
                            <i class="fa fa-heart-o"></i>
                        </a>
                    </div>
                    <?php
                } else {
                    

                    $user_id = get_current_user_id();
                    $user_is_candidate = jobsearch_user_is_candidate($user_id);
                    $candidate_rej_jobs_list = array();
                    if ($user_is_candidate) {
                        $candidate_id = jobsearch_get_user_candidate_id($user_id);
                        $candidate_rej_jobs_list = get_post_meta($candidate_id, 'jobsearch_rej_jobs_list', true);
                        $candidate_rej_jobs_list = explode(',', $candidate_rej_jobs_list);
                    }
                    
                    ob_start();

                    ?>
                    <div class="like-btn <?php echo ($container_class) ?>">
                        <a href="javascript:void(0);"     data-toggle="tooltip" title="<?php echo in_array($job_id, $candidate_rej_jobs_list) ? 'Sterge din lista de joburi neinteresante' : 'Jobul nu este interesant' ?>"     class="reject button-state-2 <?php echo in_array($job_id, $candidate_rej_jobs_list) ? 'jobsearch-delete-rej-job' : 'jobsearch-add-job-to-reject' ?> <?php echo ($anchor_class) ?> " data-id="<?php echo ($job_id) ?>" data-before-icon="<?php echo ($before_icon) ?>" data-after-icon="<?php echo ($after_icon) ?>">

                            <i class="<?php echo in_array($job_id, $candidate_rej_jobs_list) ? $before_icon : $after_icon ?>">
                            </i>

	                        <?php
                           // var_dump('test');
                            echo in_array($job_id, $candidate_rej_jobs_list) ? ($after_label) : ($before_label); ?>
                        </a>
                        <span class="job-to-fav-msg-con"></span>
                    </div>


                    <?php
                    $btn_html = ob_get_clean();
                    echo apply_filters('jobsearch_addtofav_reject_btn_html', $btn_html, $job_id, $args);
                    
                    
                }
            }
        }

        /**
         * Member Rejects
         * @ added member rejects based on job id
         */
        public function jobsearch_reject_submit_callback() {
            if (!is_user_logged_in()) {
                echo json_encode(array('msg' => esc_html__('You are not logged in.', 'wp-jobsearch'), 'error' => '1'));
                die;
            }
            $job_id = isset($_POST['job_id']) ? $_POST['job_id'] : '0';
            $user_id = get_current_user_id();
            $user_is_candidate = jobsearch_user_is_candidate($user_id);
            if ($user_is_candidate) {
                $candidate_id = jobsearch_get_user_candidate_id($user_id);
                $candidate_rej_jobs_list = get_post_meta($candidate_id, 'jobsearch_rej_jobs_list', true);

                if ($candidate_rej_jobs_list != '') {
	                $candidate_rej_jobs_list = explode(',', $candidate_rej_jobs_list);
                    if (!in_array($job_id, $candidate_rej_jobs_list)) {
	                    $candidate_rej_jobs_list[] = $job_id;
                    }
	                $candidate_rej_jobs_list = implode(',', $candidate_rej_jobs_list);
                } else {
	                $candidate_rej_jobs_list = $job_id;
                }

	            update_post_meta($candidate_id, 'jobsearch_rej_jobs_list', $candidate_rej_jobs_list);

	            $state = 'reject';

	            $candidate_fav_jobs_list = get_post_meta( $candidate_id, 'jobsearch_fav_jobs_list', true );
	            $candidate_fav_jobs_list = $candidate_fav_jobs_list != '' ? explode( ',', $candidate_fav_jobs_list ) : array();
	            if ( ( $key = array_search( $job_id, $candidate_fav_jobs_list ) ) !== false ) {
		            $response['job']   = $job_id;
		            $response['state'] = 'fav-jobs';
		            unset( $candidate_fav_jobs_list[ $key ] );
		            $candidate_fav_jobs_list = implode( ',', $candidate_fav_jobs_list );
		            update_post_meta( $candidate_id, 'jobsearch_fav_jobs_list', $candidate_fav_jobs_list );
		            $state = 'fav';
	            }

	            $candidate_apd_jobs_list = get_post_meta( $candidate_id, 'jobsearch_apd_jobs_list', true );
	            $candidate_apd_jobs_list = $candidate_apd_jobs_list != '' ? explode( ',', $candidate_apd_jobs_list ) : array();
	            if ( ( $key = array_search( $job_id, $candidate_apd_jobs_list ) ) !== false ) {
		            $response['job']   = $job_id;
		            $response['state'] = 'apd-jobs';
		            unset( $candidate_apd_jobs_list[ $key ] );
		            $candidate_apd_jobs_list = implode( ',', $candidate_apd_jobs_list );
		            update_post_meta( $candidate_id, 'jobsearch_apd_jobs_list', $candidate_apd_jobs_list );
		            $state = 'apd';
	            }

	            echo json_encode(array('msg' => esc_html__('Job added to list. reject', 'wp-jobsearch'),'state' => $state ,'job' => $job_id));
                die;
            } else {
                $msgva = esc_html__('You are not a candidate.', 'wp-jobsearch');
                $msgva = apply_filters('jobsearch_favjob_cand_notalowerr_msg', $msgva);
                echo json_encode(array('msg' => $msgva, 'error' => '1'));
                die;
            }
        }

        /**
         * Member Removed Reject
         * @ removed member rejects based on job id
         */
        public function jobsearch_removed_reject_callback() {
            $job_id = isset($_POST['job_id']) ? $_POST['job_id'] : '0';
            $user_id = get_current_user_id();
            $candidate_id = jobsearch_get_user_candidate_id($user_id);
            $candidate_rej_jobs_list = get_post_meta($candidate_id, 'jobsearch_rej_jobs_list', true);
	        $candidate_rej_jobs_list = $candidate_rej_jobs_list != '' ? explode(',', $candidate_rej_jobs_list) : array();

            if (!empty($candidate_rej_jobs_list)) {

                if (($key = array_search($job_id, $candidate_rej_jobs_list)) !== false) {
                    unset($candidate_rej_jobs_list[$key]);

	                $candidate_rej_jobs_list = implode(',', $candidate_rej_jobs_list);
                    update_post_meta($candidate_id, 'jobsearch_rej_jobs_list', $candidate_rej_jobs_list);

                    echo json_encode(array('msg' => esc_html__('Job removed.', 'wp-jobsearch')));
                    die;
                }
            }
            echo json_encode(array('error' => '1'));
            die;
        }

    }

    global $jobsearch_reject;
    $jobsearch_reject = new Jobsearch_Reject();
}
