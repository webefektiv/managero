<?php
if (!defined('ABSPATH')) {
    die;
}

global $jobsearch_allocations_vc_hooks;

if (!class_exists('jobsearch_allocations_vc_hooks')) {

    class jobsearch_allocations_vc_hooks {

        // hook things up
        public function __construct() {
            $this->vc_add_shortcode_param();
        }

        public function vc_add_shortcode_param() {
            if (function_exists('vc_add_shortcode_param')) {
                vc_add_shortcode_param('jobsearch_gapi_locs', array($this, 'apiloc_dropdowns_field'));
            }
        }

        public function apiloc_dropdowns_field($settings, $value) {
            $dropdown_class = 'wpb_vc_param_value wpb-textinput ' . esc_attr($settings['param_name']) . ' ' . esc_attr($settings['type']) . '_field';
            $api_contries_list = $settings['api_contry_list'];

            $rand_num = rand(1000000, 9999999);

            $jobsearch_locsetin_options = get_option('jobsearch_locsetin_options');

            $loc_optionstype = isset($jobsearch_locsetin_options['loc_optionstype']) ? $jobsearch_locsetin_options['loc_optionstype'] : '';

            $nameof_singl_contry = '';
            $contry_singl_contry = isset($jobsearch_locsetin_options['contry_singl_contry']) ? $jobsearch_locsetin_options['contry_singl_contry'] : '';
            if ($contry_singl_contry != '' && ($loc_optionstype == '2' || $loc_optionstype == '3')) {
                $nameof_singl_contry = isset($api_contries_list[$contry_singl_contry]) ? $api_contries_list[$contry_singl_contry] : '';
            }

            if ($value != '' && !is_array($value)) {
                $value = explode('|', $value);
            }
            $saved_country = isset($value[0]) ? $value[0] : '';
            $saved_state = isset($value[1]) ? $value[1] : '';
            $saved_city = isset($value[2]) ? $value[2] : '';

            ob_start();
            ?>
            <script>
                var jobsearch_vc_custm_getJSON = function (url, callback) {
                    var xhr = new XMLHttpRequest();
                    xhr.open('GET', url, true);
                    xhr.responseType = 'json';
                    xhr.onload = function () {
                        var status = xhr.status;
                        if (status === 200) {
                            callback(null, xhr.response);
                        } else {
                            callback(status, xhr.response);
                        }
                    };
                    xhr.send();
                };
                function all_loc_str_snd_<?php echo ($rand_num) ?>() {
                    var loc_contry = '';
                    if (jQuery('#api_country_locs_<?php echo ($rand_num) ?>').length > 0) {
                        loc_contry = jQuery('#api_country_locs_<?php echo ($rand_num) ?>').val();
                    }

                    var loc_state = jQuery('#api_state_locs_<?php echo ($rand_num) ?>').val();
                    
                    var loc_city = '';
                    if (jQuery('#api_city_locs_<?php echo ($rand_num) ?>').length > 0) {
                        loc_city = jQuery('#api_city_locs_<?php echo ($rand_num) ?>').val();
                    }
                    
                    var loc_str = '';
                    loc_str = loc_contry + '|' + loc_state + '|' + loc_city;
                    jQuery('#api_all_locs_<?php echo ($rand_num) ?>').val(loc_str);
                }
                jQuery(document).on('change', 'select[id="api_country_locs_<?php echo ($rand_num) ?>"]', function () {
                    var selctdContryId = jQuery(this).find(":selected").attr('data-countryid');

                    jobsearch_vc_custm_getJSON('http://geodata.solutions/api/api.php?type=getStates&countryId=' + selctdContryId + '&addClasses=order-alpha', function (err, data) {
                        if (typeof data.result !== 'undefined') {
                            var toStatesData = '<option value=""><?php esc_html_e('Select State', 'wp-jobsearch') ?></option>';
                            jQuery.each(data.result, function (cnrty_indx, cntry_elm) {
                                toStatesData += '<option value="' + cntry_elm + '" data-stateid=' + cnrty_indx + '>' + cntry_elm + '</option>';
                            });
                            jQuery('#api_state_locs_<?php echo ($rand_num) ?>').html(toStatesData);
                        }
                    });
                    all_loc_str_snd_<?php echo ($rand_num) ?>();
                });
                jQuery(document).on('change', 'select[id="api_state_locs_<?php echo ($rand_num) ?>"]', function () {
                    var selctdContryId = '';
                    <?php
                    if ($loc_optionstype == '2' || $loc_optionstype == '3') {
                        ?>
                        selctdContryId = '<?php echo ($contry_singl_contry) ?>';
                        <?php
                    } else {
                        ?>
                        selctdContryId = jQuery('select[id="api_country_locs_<?php echo ($rand_num) ?>"]').find(":selected").attr('data-countryid');
                        <?php
                    }
                    ?>
                    var selctdStateId = jQuery(this).find(":selected").attr('data-stateid');

                    jobsearch_vc_custm_getJSON('http://geodata.solutions/api/api.php?type=getCities&countryId=' + selctdContryId + '&stateId=' + selctdStateId + '&addClasses=order-alpha', function (err, data) {
                        if (typeof data.result !== 'undefined') {
                            var toCitiesData = '<option value=""><?php esc_html_e('Select City', 'wp-jobsearch') ?></option>';
                            jQuery.each(data.result, function(cnrty_indx, cntry_elm) {
                                toCitiesData += '<option value="' + cntry_elm + '" data-cityid=' + cnrty_indx + '>' + cntry_elm + '</option>';
                            });
                            jQuery('#api_city_locs_<?php echo ($rand_num) ?>').html(toCitiesData);
                        }
                    });
                    all_loc_str_snd_<?php echo ($rand_num) ?>();
                });
                jQuery(document).on('change', 'select[id="api_city_locs_<?php echo ($rand_num) ?>"]', function () {
                    all_loc_str_snd_<?php echo ($rand_num) ?>();
                });
            </script>
            <?php
            if ($loc_optionstype == '0' || $loc_optionstype == '1') {
                ?>
                <div class="jobsearch-vcloc-dropdwn-con">
                    <label><?php esc_html_e('Country', 'wp-jobsearch') ?></label>
                    <select id="api_country_locs_<?php echo ($rand_num) ?>">
                        <?php
                        foreach ($api_contries_list as $dr_opt_key => $dr_opt_val) {
                            ?>
                            <option value="<?php echo esc_html($dr_opt_val) ?>" <?php echo ($dr_opt_val == $saved_country ? 'selected="selected"' : '') ?> data-countryid="<?php echo esc_html($dr_opt_key) ?>"><?php echo esc_html($dr_opt_val) ?></option>
                            <?php
                        }
                        ?>
                    </select>
                </div>
                <?php
            }
            ?>

            <div class="jobsearch-vcloc-dropdwn-con">
                <label><?php esc_html_e('State', 'wp-jobsearch') ?></label>
                <?php
                if ($loc_optionstype == '2' || $loc_optionstype == '3') {
                    ?>
                    <input type="hidden" id="api_country_locs_<?php echo ($rand_num) ?>" value="<?php echo ($nameof_singl_contry) ?>">
                    <?php
                }
                ?>
                <select id="api_state_locs_<?php echo ($rand_num) ?>">
                    <option value=""><?php esc_html_e('Select State', 'wp-jobsearch') ?></option>
                    <?php
                    if ($loc_optionstype == '2' || $loc_optionstype == '3') {
                        $states_cntry = $nameof_singl_contry;
                    } else {
                        $states_cntry = $saved_country;
                    }
                    if ($states_cntry != '') {
                        $selected_contry_key = array_search($states_cntry, $api_contries_list);
                        if ($selected_contry_key != '') {
                            $api_states_list = jobsearch_allocation_settings_handle::get_states($selected_contry_key);
                            foreach ($api_states_list as $api_state_key => $api_state_val) {
                                ?>
                                <option value="<?php echo ($api_state_val) ?>" <?php echo ($api_state_val == $saved_state ? 'selected="selected"' : '') ?> data-stateid=<?php echo ($api_state_key) ?>><?php echo ($api_state_val) ?></option>
                                <?php
                            }
                        }
                    }
                    ?>
                </select>
            </div>

            <?php
            if ($loc_optionstype == '1' || $loc_optionstype == '2') {
                ?>
                <div class="jobsearch-vcloc-dropdwn-con">
                    <label><?php esc_html_e('City', 'wp-jobsearch') ?></label>
                    <select id="api_city_locs_<?php echo ($rand_num) ?>">
                        <option value=""><?php esc_html_e('Select City', 'wp-jobsearch') ?></option>
                        <?php
                        if (isset($api_states_list) && !empty($api_states_list) && $saved_state != '') {
                            $selected_state_key = array_search($saved_state, $api_states_list);
                            $selected_state_key = str_replace(array('"'), array(''), $selected_state_key);
                            $api_cities_list = jobsearch_allocation_settings_handle::get_cities($selected_contry_key, $selected_state_key);
                            foreach ($api_cities_list as $api_city_key => $api_city_val) {
                                ?>
                                <option value="<?php echo ($api_city_val) ?>" <?php echo ($api_city_val == $saved_city ? 'selected="selected"' : '') ?> data-cityid="<?php echo ($api_city_key) ?>"><?php echo ($api_city_val) ?></option>
                                <?php
                            }
                        }
                        ?>
                    </select>
                </div>
                <?php
            }
            
            $saved_value = '';
            if (!empty($value)) {
                $saved_value = implode('|', $value);
            }
            ?>
            <input id="api_all_locs_<?php echo ($rand_num) ?>" type="hidden" name="<?php echo esc_html($settings['param_name']) ?>" class="<?php echo esc_html($dropdown_class) ?>" value="<?php echo ($saved_value) ?>">
            <?php
            $dropdown_html = ob_get_clean();

            return $dropdown_html;
        }

    }

    $jobsearch_allocations_vc_hooks = new jobsearch_allocations_vc_hooks();
}
