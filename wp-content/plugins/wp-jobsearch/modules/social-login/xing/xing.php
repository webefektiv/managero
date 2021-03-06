<?php

class JobsearchXingLogin {

    private $xing_details;
    private $redirect_url;

    /**
     * JobsearchXingLogin constructor.
     */
    public function __construct() {

        add_shortcode('jobsearch_xing_login', array($this, 'display_login_button'));
        add_action('wp_ajax_jobsearch_xing_response_data', array($this, 'jobsearch_xing_response_data_callback'));
        add_action('wp_ajax_nopriv_jobsearch_xing_response_data', array($this, 'jobsearch_xing_response_data_callback'));
    }

    public function jobsearch_xing_response_data_callback() {
        global $jobsearch_plugin_options;

        $json_arr = array();

        $user_data = isset($_POST['user_data']) && !empty($_POST['user_data']) ? $_POST['user_data'] : '';

        $user_dashboard_page = isset($jobsearch_plugin_options['user-dashboard-template-page']) ? $jobsearch_plugin_options['user-dashboard-template-page'] : '';
        $user_dashboard_page = isset($user_dashboard_page) && !empty($user_dashboard_page) ? jobsearch__get_post_id($user_dashboard_page, 'page') : 0;
        $real_redirect_url = $user_dashboard_page > 0 ? get_permalink($user_dashboard_page) : home_url('/');

        $json_arr['redirect_url'] = $real_redirect_url; // redirect url after login
        $json_arr['status'] = true;

        $this->xing_details = $user_data; // data format in json
        $xing_user = json_decode(stripslashes($user_data), true);

        $user_image = $xing_user['photo_urls']['maxi_thumb'];

        $job_title = $xing_user['professional_experience']['primary_company']['title'];

        // check a user is already reistered or not

        $user_id = isset($xing_user['id']) ? $xing_user['id'] : '';
        $wp_users = get_users(array(
            'meta_key' => 'jobsearch_xing_id',
            'meta_value' => $user_id,
            'number' => 1,
            'count_total' => false,
            'fields' => 'id',
        ));
        if (empty($wp_users[0])) { // execute if user is not registered
            // create user for non-registerd
            $user_id = isset($xing_user['id']) ? $xing_user['id'] : '';
            $first_name = isset($xing_user['first_name']) ? $xing_user['first_name'] : '';
            $last_name = isset($xing_user['last_name']) ? $xing_user['last_name'] : '';
            $email = isset($xing_user['active_email']) ? $xing_user['active_email'] : '';

            $_social_user_obj = get_user_by('email', $email);
            if (is_object($_social_user_obj) && isset($_social_user_obj->ID)) {
                update_user_meta($_social_user_obj->ID, 'jobsearch_xing_id', $user_id);
            }
            if ($first_name != '' && $last_name != '') {
                $name = $first_name . '_' . $last_name;
                $name = str_replace(array(' '), array('_'), $name);
                $username = sanitize_user(str_replace(' ', '_', strtolower($name)));
            } else {
                $username = $email;
            }
            if (username_exists($username)) {
                $username .= '_' . rand(10000, 99999);
            }
            // Creating our user
            $new_user = wp_create_user($username, wp_generate_password(), $email);
            if (is_wp_error($new_user)) {
                $json_arr['status'] = false;
                $json_arr['msg'] = $new_user->get_error_message();
            } else {
                // user role
                $user_role = 'jobsearch_candidate';
                wp_update_user(array('ID' => $new_user, 'role' => $user_role));
                update_user_meta($new_user, 'first_name', $first_name);
                update_user_meta($new_user, 'jobsearch_xing_id', $user_id);
                $candidate_id = get_user_meta($new_user, 'jobsearch_candidate_id', true);
                update_post_meta($candidate_id, 'jobsearch_field_candidate_jobtitle', $job_title);

                wp_set_auth_cookie($new_user);
            }
        } else {
            wp_set_auth_cookie($wp_users[0]);
        }

        echo json_encode($json_arr);
        wp_die();
    }

    public function display_login_button() {

        global $jobsearch_plugin_options;
        $admin_ajax_url = admin_url('admin-ajax.php');
        $jobsearch_xing_consumer_key = isset($jobsearch_plugin_options['jobsearch_xing_consumer_key']) ? $jobsearch_plugin_options['jobsearch_xing_consumer_key'] : '';
        $Button_Data = '';
        if (!empty($jobsearch_xing_consumer_key)) {
            $Button_Data = '<script type="xing/login">{"consumer_key": "' . $jobsearch_xing_consumer_key . '","size": "xlarge"}</script>';
        }
        ?>
        <li id="jobsearch-xing-wrapper">
            <script>
                function onXingAuthLogin(xing_response) {
                    if (xing_response.error && xing_response.user != null) {
                        alert(xing_response.error);
                    } else {
                        if (xing_response.user != 'undefined' && xing_response.user != null) {
                            var userStr = JSON.stringify(xing_response.user);
                            var dataString = 'action=jobsearch_xing_response_data&user_data=' + userStr + '';
                            var ajax_url = '<?php echo ($admin_ajax_url); ?>';
                            jQuery.ajax({
                                type: 'POST',
                                url: ajax_url,
                                dataType: "json",
                                data: dataString,
                                success: function (response) {
                                    if (response.status !== 'undefined' && response.status == true) {
                                        window.location.href = response.redirect_url;
                                    } else {
                                        alert(response.msg);
                                    }
                                }
                            });
                        }
                    }
                }
            </script>
            <?php echo ($Button_Data); ?>
            <script>
                (function (d) {
                    var js, id = 'lwx';
                    if (d.getElementById(id))
                        return;
                    js = d.createElement('script');
                    js.id = id;
                    js.src = "https://www.xing-share.com/plugins/login.js";
                    d.getElementsByTagName('head')[0].appendChild(js)
                }(document));
            </script>
        </li>

        <li><a id="jobsearch-xing-login" class="jobsearch-xing-bg" href="javascript:void(0)"><i class="fa fa-google-plus"></i><?php echo __('Login with Xing', 'wp-jobsearch') ?></a>

            <?php
        }

    }

    /*
     * Starts social login
     */
    new JobsearchXingLogin();
    ?>