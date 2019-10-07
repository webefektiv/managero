<?php
/*
  Class : Location
 */


// this is an include only WP file
if (!defined('ABSPATH')) {
    die;
}

// main plugin class
class Jobsearch_Location {

// hook things up
    public function __construct() {
        add_action('jobsearch_admin_location_map', array($this, 'jobsearch_admin_location_map_callback'), 10, 1);
        add_action('jobsearch_dashboard_location_map', array($this, 'jobsearch_dashboard_location_map_callback'), 10, 2);
        //
        add_action('wp_ajax_jobsearch_loc_levels_names_to_address', array($this, 'loc_levels_names_to_address'));
        add_action('wp_ajax_nopriv_jobsearch_loc_levels_names_to_address', array($this, 'loc_levels_names_to_address'));
        //
        add_action('wp_ajax_jobsearch_location_load_location2_data', array($this, 'jobsearch_location_load_location2_data_callback'));
        add_action('wp_ajax_nopriv_jobsearch_location_load_location2_data', array($this, 'jobsearch_location_load_location2_data_callback'));
        //
        add_filter('redux/options/jobsearch_plugin_options/sections', array($this, 'jobsearch_location_plugin_option_fields'));
        add_action('init', array($this, 'titles_translation'));
        $this->load_files();
    }

    public function titles_translation() {
        global $jobsearch_plugin_options;

        $label_location1 = isset($jobsearch_plugin_options['jobsearch-location-label-location1']) ? $jobsearch_plugin_options['jobsearch-location-label-location1'] : esc_html__('Country', 'wp-jobsearch');
        do_action('wpml_register_single_string', 'JobSearch Options', 'Location First Field - ' . $label_location1, $label_location1);
        $label_location2 = isset($jobsearch_plugin_options['jobsearch-location-label-location2']) ? $jobsearch_plugin_options['jobsearch-location-label-location2'] : esc_html__('State', 'wp-jobsearch');
        do_action('wpml_register_single_string', 'JobSearch Options', 'Location Second Field - ' . $label_location2, $label_location2);
        $label_location3 = isset($jobsearch_plugin_options['jobsearch-location-label-location3']) ? $jobsearch_plugin_options['jobsearch-location-label-location3'] : esc_html__('Region', 'wp-jobsearch');
        do_action('wpml_register_single_string', 'JobSearch Options', 'Location Third Field - ' . $label_location3, $label_location3);
        $label_location4 = isset($jobsearch_plugin_options['jobsearch-location-label-location4']) ? $jobsearch_plugin_options['jobsearch-location-label-location4'] : esc_html__('City', 'wp-jobsearch');
        do_action('wpml_register_single_string', 'JobSearch Options', 'Location Forth Field - ' . $label_location4, $label_location4);
    }

    public function location_front_enqueue_scripts() {
        
    }

    public function load_files() {
        $jobsearch__options = get_option('jobsearch_plugin_options');
        $all_locations_type = isset($jobsearch__options['all_locations_type']) ? $jobsearch__options['all_locations_type'] : '';
        if ($all_locations_type == 'api') {
            include plugin_dir_path(dirname(__FILE__)) . 'locations/include/location-settings.php';
            include plugin_dir_path(dirname(__FILE__)) . 'locations/include/locations-vc-hooks.php';
        } else {
            include plugin_dir_path(dirname(__FILE__)) . 'locations/include/register-taxonomy.php';
        }
        include plugin_dir_path(dirname(__FILE__)) . 'locations/include/locations-html.php';
    }

    public function jobsearch_admin_location_map_callback($id = '') {
        global $jobsearch_form_fields, $jobsearch_plugin_options, $sitepress, $jobsearch_gdapi_allocation;

        $all_location_allow = isset($jobsearch_plugin_options['all_location_allow']) ? $jobsearch_plugin_options['all_location_allow'] : '';

        if ($all_location_allow == 'on') {

            $all_locations_type = isset($jobsearch_plugin_options['all_locations_type']) ? $jobsearch_plugin_options['all_locations_type'] : '';

            $lang_code = '';
            $admin_ajax_url = admin_url('admin-ajax.php');
            if (function_exists('icl_object_id') && function_exists('wpml_init_language_switcher')) {
                $lang_code = $sitepress->get_current_language();
                $admin_ajax_url = add_query_arg(array('lang' => $lang_code), $admin_ajax_url);
            }

            wp_register_script('jobsearch-location', jobsearch_plugin_get_url('modules/locations/js/location-functions.js'), array('jquery'), '', true);
            // Localize the script
            $jobsearch_location_common_arr = array(
                'plugin_url' => jobsearch_plugin_get_url(),
                'ajax_url' => $admin_ajax_url,
            );

            $switch_location_fields = isset($jobsearch_plugin_options['switch_location_fields']) ? $jobsearch_plugin_options['switch_location_fields'] : '';

            $required_fields_count = isset($jobsearch_plugin_options['jobsearch-location-required-fields-count']) ? $jobsearch_plugin_options['jobsearch-location-required-fields-count'] : 'all';
            $label_location1 = isset($jobsearch_plugin_options['jobsearch-location-label-location1']) ? apply_filters('wpml_translate_single_string', $jobsearch_plugin_options['jobsearch-location-label-location1'], 'JobSearch Options', 'Location First Field - ' . $jobsearch_plugin_options['jobsearch-location-label-location1'], $lang_code) : esc_html__('Country', 'wp-jobsearch');
            $label_location2 = isset($jobsearch_plugin_options['jobsearch-location-label-location2']) ? apply_filters('wpml_translate_single_string', $jobsearch_plugin_options['jobsearch-location-label-location2'], 'JobSearch Options', 'Location Second Field - ' . $jobsearch_plugin_options['jobsearch-location-label-location2'], $lang_code) : esc_html__('State', 'wp-jobsearch');
            $label_location3 = isset($jobsearch_plugin_options['jobsearch-location-label-location3']) ? apply_filters('wpml_translate_single_string', $jobsearch_plugin_options['jobsearch-location-label-location3'], 'JobSearch Options', 'Location Third Field - ' . $jobsearch_plugin_options['jobsearch-location-label-location3'], $lang_code) : esc_html__('Region', 'wp-jobsearch');
            $label_location4 = isset($jobsearch_plugin_options['jobsearch-location-label-location4']) ? apply_filters('wpml_translate_single_string', $jobsearch_plugin_options['jobsearch-location-label-location4'], 'JobSearch Options', 'Location Forth Field - ' . $jobsearch_plugin_options['jobsearch-location-label-location4'], $lang_code) : esc_html__('City', 'wp-jobsearch');

            $default_location = isset($jobsearch_plugin_options['jobsearch-location-default-address']) ? $jobsearch_plugin_options['jobsearch-location-default-address'] : '';

            $allow_full_address = isset($jobsearch_plugin_options['location-allow-full-address']) ? $jobsearch_plugin_options['location-allow-full-address'] : '';
            $allow_location_map = isset($jobsearch_plugin_options['location-allow-map']) ? $jobsearch_plugin_options['location-allow-map'] : '';

            $def_map_zoom = isset($jobsearch_plugin_options['jobsearch-location-map-zoom']) && $jobsearch_plugin_options['jobsearch-location-map-zoom'] > 0 ? absint($jobsearch_plugin_options['jobsearch-location-map-zoom']) : '12';

            $map_styles = isset($jobsearch_plugin_options['jobsearch-location-map-style']) ? $jobsearch_plugin_options['jobsearch-location-map-style'] : '';

            $allow_latlng_fileds = isset($jobsearch_plugin_options['allow_latlng_fileds']) ? $jobsearch_plugin_options['allow_latlng_fileds'] : '';

            wp_localize_script('jobsearch-location', 'jobsearch_location_common_vars', $jobsearch_location_common_arr);
            wp_enqueue_script('jobsearch-location');
            
            if ($allow_location_map == 'yes') {
                wp_enqueue_script('jobsearch-google-map');
            }
            $rand_num = rand(1000000, 99999999);
            $loc_location1 = get_post_meta($id, 'jobsearch_field_location_location1', true);
            $loc_location2 = get_post_meta($id, 'jobsearch_field_location_location2', true);
            $loc_location3 = get_post_meta($id, 'jobsearch_field_location_location3', true);
            $loc_location4 = get_post_meta($id, 'jobsearch_field_location_location4', true);
            //update_post_meta($id, 'jobsearch_field_location_address', '');
            $loc_address = get_post_meta($id, 'jobsearch_field_location_address', true);
            $loc_lat = get_post_meta($id, 'jobsearch_field_location_lat', true);
            $loc_lng = get_post_meta($id, 'jobsearch_field_location_lng', true);
            $loc_zoom = get_post_meta($id, 'jobsearch_field_location_zoom', true);
            $map_height = get_post_meta($id, 'jobsearch_field_map_height', true);
            $marker_image = get_post_meta($id, 'jobsearch_field_marker_image', true);
            if (($loc_lat == '' || $loc_lng == '') && $default_location != '') {

                $loc_geo_cords = jobsearch_address_to_cords($default_location);
                $loc_lat = isset($loc_geo_cords['lat']) ? $loc_geo_cords['lat'] : '';
                $loc_lng = isset($loc_geo_cords['lng']) ? $loc_geo_cords['lng'] : '';
            }

            if ($loc_lat == '' || $loc_lng == '') {
                $loc_lat = '37.090240';
                $loc_lng = '-95.712891';
            }

            if ($map_height == '' || $map_height <= 100) {
                $map_height = 250;
            }
            if ($loc_zoom == '') {
                $loc_zoom = $def_map_zoom;
            }

            if ($all_locations_type != 'api') {
                $please_select = esc_html__('Please select', 'wp-jobsearch');
                $location_location1 = array('' => $please_select . ' ' . $label_location1);
                $location_location2 = array('' => $please_select . ' ' . $label_location2);
                $location_location3 = array('' => $please_select . ' ' . $label_location3);
                $location_location4 = array('' => $please_select . ' ' . $label_location4);
//                $location_obj = get_terms('job-location', array(
//                    'orderby' => 'name',
//                    'order' => 'ASC',
//                    'hide_empty' => 0,
//                    'parent' => 0,
//                ));
                $location_obj = jobsearch_custom_get_terms('job-location');
                foreach ($location_obj as $country_arr) {
                    $location_location1[$country_arr->slug] = $country_arr->name;
                    // get all state, region and city
                    // not neccessory for first load, it will populate on seelct country
                }
            }

            ob_start();

            if ($all_locations_type != 'api') {
                ?>
                <div class="jobsearch-element-field" style="display: <?php echo ($switch_location_fields == 'on' ? 'inline-block' : 'none') ?>;">
                    <div class="elem-label">
                        <label><?php echo esc_html($label_location1) ?></label>
                    </div>
                    <div class="elem-field">
                        <?php
                        $field_params = array(
                            'classes' => 'location_location1',
                            'id' => 'location_location1_' . $rand_num,
                            'name' => 'location_location1',
                            'options' => $location_location1,
                            'force_std' => $loc_location1,
                            'ext_attr' => ' data-randid="' . $rand_num . '" data-nextfieldelement="' . $please_select . ' ' . $label_location2 . '" data-nextfieldval="' . $loc_location2 . '"',
                        );
                        $jobsearch_form_fields->select_field($field_params);
                        ?>
                    </div>
                </div> 
                <?php if ($required_fields_count > 1 || $required_fields_count == 'all') { ?>
                    <div class="jobsearch-element-field" style="display: <?php echo ($switch_location_fields == 'on' ? 'inline-block' : 'none') ?>;">
                        <div class="elem-label">
                            <label><?php echo esc_html($label_location2) ?></label>
                        </div>
                        <div class="elem-field">
                            <?php
                            $field_params = array(
                                'classes' => 'location_location2',
                                'id' => 'location_location2_' . $rand_num,
                                'name' => 'location_location2',
                                'options' => $location_location2,
                                'force_std' => $loc_location2,
                                'ext_attr' => ' data-randid="' . $rand_num . '" data-nextfieldelement="' . $please_select . ' ' . $label_location3 . '" data-nextfieldval="' . $loc_location3 . '"',
                            );
                            $jobsearch_form_fields->select_field($field_params);
                            ?>
                            <span class="jobsearch-field-loader location_location2_<?php echo absint($rand_num); ?>"></span>
                        </div>
                    </div>
                <?php }if ($required_fields_count > 2 || $required_fields_count == 'all') { ?>
                    <div class="jobsearch-element-field" style="display: <?php echo ($switch_location_fields == 'on' ? 'inline-block' : 'none') ?>;">
                        <div class="elem-label">
                            <label><?php echo esc_html($label_location3) ?></label>
                        </div>
                        <div class="elem-field">
                            <?php
                            $field_params = array(
                                'classes' => 'location_location3',
                                'id' => 'location_location3_' . $rand_num,
                                'name' => 'location_location3',
                                'options' => $location_location3,
                                'force_std' => $loc_location3,
                                'ext_attr' => ' data-randid="' . $rand_num . '" data-nextfieldelement="' . $please_select . ' ' . $label_location4 . '" data-nextfieldval="' . $loc_location4 . '"',
                            );
                            $jobsearch_form_fields->select_field($field_params);
                            ?>
                            <span class="jobsearch-field-loader location_location3_<?php echo absint($rand_num); ?>"></span>
                        </div>
                    </div>
                <?php }if ($required_fields_count > 3 || $required_fields_count == 'all') { ?>
                    <div class="jobsearch-element-field" style="display: <?php echo ($switch_location_fields == 'on' ? 'inline-block' : 'none') ?>;">
                        <div class="elem-label">
                            <label><?php echo esc_html($label_location4) ?></label>
                        </div>
                        <div class="elem-field">
                            <?php
                            $field_params = array(
                                'classes' => 'location_location4',
                                'id' => 'location_location4_' . $rand_num,
                                'name' => 'location_location4',
                                'options' => $location_location4,
                                'force_std' => $loc_location4,
                                'ext_attr' => ' data-randid="' . $rand_num . '"',
                            );
                            $jobsearch_form_fields->select_field($field_params);
                            ?>
                            <span class="jobsearch-field-loader location_location4_<?php echo absint($rand_num); ?>"></span>
                        </div>
                    </div>
                    <?php
                }
            } else if ($all_locations_type == 'api') {
                wp_enqueue_script('jobsearch-gdlocation-api');
                $jobsearch_locsetin_options = get_option('jobsearch_locsetin_options');

                $api_contries_list = $jobsearch_gdapi_allocation::get_countries();

                $loc_optionstype = isset($jobsearch_locsetin_options['loc_optionstype']) ? $jobsearch_locsetin_options['loc_optionstype'] : '';
                $contry_singl_contry = isset($jobsearch_locsetin_options['contry_singl_contry']) ? $jobsearch_locsetin_options['contry_singl_contry'] : '';
                $contry_order = isset($jobsearch_locsetin_options['contry_order']) ? $jobsearch_locsetin_options['contry_order'] : '';
                $contry_order = $contry_order != '' ? $contry_order : 'alpha';
                $contry_filtring = isset($jobsearch_locsetin_options['contry_filtring']) ? $jobsearch_locsetin_options['contry_filtring'] : '';
                $contry_filtring = $contry_filtring != '' ? $contry_filtring : 'none';
                $contry_filtr_limreslts = isset($jobsearch_locsetin_options['contry_filtr_limreslts']) ? $jobsearch_locsetin_options['contry_filtr_limreslts'] : '';
                $contry_filtr_limreslts = $contry_filtr_limreslts <= 0 ? 1000000 : $contry_filtr_limreslts;
                $contry_filtrinc_contries = isset($jobsearch_locsetin_options['contry_filtrinc_contries']) ? $jobsearch_locsetin_options['contry_filtrinc_contries'] : '';
                $contry_filtrexc_contries = isset($jobsearch_locsetin_options['contry_filtrexc_contries']) ? $jobsearch_locsetin_options['contry_filtrexc_contries'] : '';
                $contry_preselct = isset($jobsearch_locsetin_options['contry_preselct']) ? $jobsearch_locsetin_options['contry_preselct'] : '';
                $contry_preselct = $contry_preselct != '' ? $contry_preselct : 'none';
                $contry_presel_contry = isset($jobsearch_locsetin_options['contry_presel_contry']) ? $jobsearch_locsetin_options['contry_presel_contry'] : '';

                // For saved country
                if ($loc_location1 != '' && in_array($loc_location1, $api_contries_list)) {
                    $contry_preselct = 'by_contry';
                    $contry_singl_contry = $contry_presel_contry = array_search($loc_location1, $api_contries_list);
                }
                //
                $continent_group = isset($jobsearch_locsetin_options['continent_group']) ? $jobsearch_locsetin_options['continent_group'] : '';
                $continent_order = isset($jobsearch_locsetin_options['continent_order']) ? $jobsearch_locsetin_options['continent_order'] : '';
                $continent_order = $continent_order != '' ? $continent_order : 'alpha';
                $continent_filter = isset($jobsearch_locsetin_options['continent_filter']) ? $jobsearch_locsetin_options['continent_filter'] : '';
                $continent_filter = $continent_filter != '' ? $continent_filter : 'none';
                $continents_selected = isset($jobsearch_locsetin_options['continents_selected']) ? $jobsearch_locsetin_options['continents_selected'] : '';
                //
                $state_order = isset($jobsearch_locsetin_options['state_order']) ? $jobsearch_locsetin_options['state_order'] : '';
                $state_order = $state_order != '' ? $state_order : 'alpha';
                $state_filtring = isset($jobsearch_locsetin_options['state_filtring']) ? $jobsearch_locsetin_options['state_filtring'] : '';
                $state_filtring = $state_filtring != '' ? $state_filtring : 'none';
                $state_filtr_limreslts = isset($jobsearch_locsetin_options['state_filtr_limreslts']) ? $jobsearch_locsetin_options['state_filtr_limreslts'] : '';
                $state_filtr_limreslts = $state_filtr_limreslts <= 0 ? 1000000 : $state_filtr_limreslts;
                //
                $city_order = isset($jobsearch_locsetin_options['city_order']) ? $jobsearch_locsetin_options['city_order'] : '';
                $city_order = $city_order != '' ? $city_order : 'alpha';
                $city_filtring = isset($jobsearch_locsetin_options['city_filtring']) ? $jobsearch_locsetin_options['city_filtring'] : '';
                $city_filtring = $city_filtring != '' ? $city_filtring : 'none';
                $city_filtr_limreslts = isset($jobsearch_locsetin_options['city_filtr_limreslts']) ? $jobsearch_locsetin_options['city_filtr_limreslts'] : '';
                $city_filtr_limreslts = $city_filtr_limreslts <= 0 ? 1000000 : $city_filtr_limreslts;
                //

                $continents_class = '';
                if ($continent_group == 'on') {
                    $continents_class = ' group-continents';
                    if ($continent_order == 'alpha') {
                        $continents_class .= ' group-order-alpha';
                    } else if ($continent_order == 'by_population') {
                        $continents_class .= ' group-order-pop';
                    } else if ($continent_order == 'north_america') {
                        $continents_class .= ' group-order-na';
                    } else if ($continent_order == 'europe') {
                        $continents_class .= ' group-order-eu';
                    } else if ($continent_order == 'africa') {
                        $continents_class .= ' group-order-af';
                    } else if ($continent_order == 'oceania') {
                        $continents_class .= ' group-order-oc';
                    } else if ($continent_order == 'asia') {
                        $continents_class .= ' group-order-as';
                    } else if ($continent_order == 'rand') {
                        $continents_class .= ' group-order-rand';
                    }

                    //
                    if ($continent_filter == 'by_select' && !empty($continents_selected) && is_array($continents_selected)) {
                        $inc_continents_selected = implode('-', $continents_selected);
                        $continents_class .= ' continent-include-' . $inc_continents_selected;
                    }
                }

                $contries_class = '';
                if ($contry_order == 'alpha') {
                    $contries_class .= ' order-alpha';
                } else if ($contry_order == 'by_population') {
                    $contries_class .= ' order-pop';
                } else if ($contry_order == 'random') {
                    $contries_class .= ' order-rand';
                }
                if ($contry_filtring == 'limt_results' && $contry_filtr_limreslts > 0) {
                    $contries_class .= ' limit-pop-' . absint($contry_filtr_limreslts);
                } else if ($contry_filtring == 'inc_contries' && !empty($contry_filtrinc_contries) && is_array($contry_filtrinc_contries)) {
                    $inc_contries_implist = implode('-', $contry_filtrinc_contries);
                    $contries_class .= ' include-' . $inc_contries_implist;
                } else if ($contry_filtring == 'exc_contries' && !empty($contry_filtrexc_contries) && is_array($contry_filtrexc_contries)) {
                    $exc_contries_implist = implode('-', $contry_filtrexc_contries);
                    $contries_class .= ' exclude-' . $exc_contries_implist;
                }
                if ($contry_preselct == 'by_contry' && $contry_presel_contry != '') {
                    $contries_class .= ' presel-' . $contry_presel_contry;
                } else if ($contry_preselct == 'by_user_ip') {
                    $contries_class .= ' presel-byip';
                }

                //
                $states_class = '';
                if ($state_order == 'alpha') {
                    $states_class .= ' order-alpha';
                } else if ($state_order == 'by_population') {
                    $states_class .= ' order-pop';
                } else if ($state_order == 'random') {
                    $states_class .= ' order-rand';
                }

                //
                $cities_class = '';
                if ($city_order == 'alpha') {
                    $cities_class .= ' order-alpha';
                } else if ($city_order == 'by_population') {
                    $cities_class .= ' order-pop';
                } else if ($city_order == 'random') {
                    $cities_class .= ' order-rand';
                }

                if ($loc_optionstype == '0' || $loc_optionstype == '1') {
                    ?>
                    <div class="jobsearch-element-field">
                        <div class="elem-label">
                            <label><?php esc_html_e('Country', 'wp-jobsearch') ?></label>
                        </div>
                        <div id="jobsearch-gdapilocs-contrycon" data-val="<?php echo ($loc_location1) ?>" class="elem-field">
                            <select name="jobsearch_field_location_location1" class="countries<?php echo ($contries_class . $continents_class) ?>" id="countryId">
                                <option value=""><?php esc_html_e('Select Country', 'wp-jobsearch') ?></option>
                            </select>
                        </div>
                    </div>
                    <?php
                }
                ?>
                <div class="jobsearch-element-field">
                    <div class="elem-label">
                        <label><?php esc_html_e('State', 'wp-jobsearch') ?></label>
                    </div>
                    <?php
                    if ($loc_optionstype == '2' || $loc_optionstype == '3') {
                        ?>
                        <input type="hidden" name="country" id="countryId" value="<?php echo ($contry_singl_contry) ?>"/>
                        <?php
                    }
                    ?>
                    <div id="jobsearch-gdapilocs-statecon" data-val="<?php echo ($loc_location2) ?>" class="elem-field">
                        <select name="jobsearch_field_location_location2" class="states<?php echo ($states_class) ?>" id="stateId">
                            <option value=""><?php esc_html_e('Select State', 'wp-jobsearch') ?></option>
                        </select>
                    </div>
                </div>
                <?php
                if ($loc_optionstype == '1' || $loc_optionstype == '2') {
                    ?>
                    <div class="jobsearch-element-field">
                        <div class="elem-label">
                            <label><?php esc_html_e('City', 'wp-jobsearch') ?></label>
                        </div>
                        <div id="jobsearch-gdapilocs-citycon" data-val="<?php echo ($loc_location3) ?>" class="elem-field">
                            <select name="jobsearch_field_location_location3" class="cities<?php echo ($cities_class) ?>" id="cityId">
                                <option value=""><?php esc_html_e('Select City', 'wp-jobsearch') ?></option>
                            </select>
                        </div>
                    </div>
                    <?php
                }
            }
            ?>
            <div class="jobsearch-element-field" <?php echo ($allow_full_address != 'yes' ? 'style="display: none;"' : '') ?>>
                <div class="elem-label">
                    <label><?php esc_html_e('Address', 'wp-jobsearch') ?></label>
                </div>
                <div class="elem-field">
                    <?php
                    $field_params = array(
                        'id' => 'jobsearch_location_address_' . $rand_num,
                        'name' => 'location_address',
                        'force_std' => $loc_address,
                    );
                    $jobsearch_form_fields->input_field($field_params);
                    ?>
                    <input id="check_loc_addr_<?php echo ($rand_num) ?>" type="hidden" value="<?php echo ($loc_address) ?>">
                </div>
            </div>

            <?php
            $loc_fields_html = ob_get_clean();
            echo apply_filters('jobsearch_admin_loc_address_simpfields', $loc_fields_html, $id, $rand_num);
            ?>

            <div class="jobsearch-element-field" <?php echo ($allow_location_map == 'yes' && $allow_latlng_fileds == 'yes' ? '' : 'style="display: none;"') ?>>
                <div class="elem-label">
                    <label><?php esc_html_e('Latitude', 'wp-jobsearch') ?></label>
                </div>
                <div class="elem-field">
                    <?php
                    $field_params = array(
                        'id' => 'jobsearch_location_lat_' . $rand_num,
                        'name' => 'location_lat',
                        'force_std' => $loc_lat,
                    );
                    $jobsearch_form_fields->input_field($field_params);
                    ?>
                </div>
            </div>
            <div class="jobsearch-element-field" <?php echo ($allow_location_map == 'yes' && $allow_latlng_fileds == 'yes' ? '' : 'style="display: none;"') ?>>
                <div class="elem-label">
                    <label><?php esc_html_e('Longitude', 'wp-jobsearch') ?></label>
                </div>
                <div class="elem-field">
                    <?php
                    $field_params = array(
                        'id' => 'jobsearch_location_lng_' . $rand_num,
                        'name' => 'location_lng',
                        'force_std' => $loc_lng,
                    );
                    $jobsearch_form_fields->input_field($field_params);
                    ?>
                </div>
            </div>
            <div class="jobsearch-element-field" <?php echo ($allow_location_map == 'yes' && $allow_latlng_fileds == 'yes' ? '' : 'style="display: none;"') ?>>
                <div class="elem-label">
                    <label><?php esc_html_e('Zoom Level', 'wp-jobsearch') ?></label>
                </div>
                <div class="elem-field">
                    <?php
                    $field_params = array(
                        'id' => 'jobsearch_location_zoom_' . $rand_num,
                        'name' => 'location_zoom',
                        'force_std' => $loc_zoom,
                    );
                    $jobsearch_form_fields->input_field($field_params);
                    ?>
                </div>
            </div>
            <div class="jobsearch-element-field" <?php echo ($allow_location_map != 'yes' ? 'style="display: none;"' : '') ?>>
                <div class="elem-label">
                    <label><?php esc_html_e('Map Height', 'wp-jobsearch') ?></label>
                </div>
                <div class="elem-field">
                    <?php
                    $field_params = array(
                        'name' => 'map_height',
                        'force_std' => $map_height,
                    );
                    $jobsearch_form_fields->input_field($field_params);
                    ?>
                </div>
            </div>
            <div class="jobsearch-element-field" <?php echo ($allow_location_map != 'yes' ? 'style="display: none;"' : '') ?>>
                <div class="elem-label">
                    <label><?php esc_html_e('Marker', 'wp-jobsearch') ?></label>
                </div>
                <div class="elem-field">
                    <?php
                    $field_params = array(
                        'id' => 'marker_image_' . $rand_num,
                        'name' => 'marker_image',
                        'force_std' => $marker_image,
                    );
                    $jobsearch_form_fields->image_upload_field($field_params);
                    ?>
                </div>
            </div>
            <div id="jobsearch-map-<?php echo absint($rand_num); ?>" style="width: 100%; height: <?php echo ($allow_location_map != 'yes' ? '0' : $map_height) ?>px;"></div>
            <?php
            if ($allow_location_map == 'yes') {
                ?>
                <script>
                    var map;
                    var markers = [];
                    jQuery(document).ready(function () {
                        function jobsearch_map_autocomplete_fields_<?php echo ($rand_num) ?>() {
                            var autocomplete_input = document.getElementById('jobsearch_location_address_<?php echo ($rand_num) ?>');
                            var autocomplete = new google.maps.places.Autocomplete(autocomplete_input);
                        }
                        google.maps.event.addDomListener(window, 'load', jobsearch_map_autocomplete_fields_<?php echo ($rand_num) ?>);
                <?php
                if ($loc_lat != '' && $loc_lng != '' && $loc_zoom != '') {
                    ?>
                            function initMap<?php echo ($rand_num) ?>() {
                                var myLatLng = {lat: <?php echo esc_js($loc_lat) ?>, lng: <?php echo esc_js($loc_lng) ?>};
                                map = new google.maps.Map(document.getElementById('jobsearch-map-<?php echo absint($rand_num); ?>'), {
                                    zoom: <?php echo esc_js($loc_zoom) ?>,
                                    center: myLatLng,
                                    streetViewControl: false,
                                    scrollwheel: false,
                                    mapTypeControl: false,
                                });
                    <?php
                    if ($map_styles != '') {
                        $map_styles = stripslashes($map_styles);
                        $map_styles = preg_replace('/\s+/', ' ', trim($map_styles));
                        ?>
                                    var styles = '<?php echo ($map_styles) ?>';
                                    if (styles != '') {
                                        styles = jQuery.parseJSON(styles);
                                        var styledMap = new google.maps.StyledMapType(
                                                styles,
                                                {name: 'Styled Map'}
                                        );
                                        map.mapTypes.set('map_style', styledMap);
                                        map.setMapTypeId('map_style');
                                    }
                        <?php
                    }
                    ?>

                                var marker = new google.maps.Marker({
                                    position: myLatLng,
                                    map: map,
                                    draggable: true,
                                    title: '',
                                    icon: '<?php echo esc_js($marker_image) ?>',
                                });

                                markers.push(marker);

                                google.maps.event.addListener(map, 'zoom_changed', function () {
                                    var zoom_lvl = map.getZoom();
                                    document.getElementById("jobsearch_location_zoom_<?php echo absint($rand_num); ?>").value = zoom_lvl;
                                });
                                google.maps.event.addListener(marker, 'dragend', function (event) {
                                    document.getElementById("jobsearch_location_lat_<?php echo absint($rand_num); ?>").value = this.getPosition().lat();
                                    document.getElementById("jobsearch_location_lng_<?php echo absint($rand_num); ?>").value = this.getPosition().lng();
                                });
                            }

                            google.maps.event.addDomListener(window, 'load', initMap<?php echo ($rand_num) ?>);

                            function find_on_map<?php echo ($rand_num) ?>(_this) {
                                var $ = jQuery;
                                var geocoder = new google.maps.Geocoder();
                                var addres = _this.val();
                                var lat_con = $('#jobsearch_location_lat_<?php echo ($rand_num) ?>');
                                var lng_con = $('#jobsearch_location_lng_<?php echo ($rand_num) ?>');
                                geocoder.geocode({address: addres}, function (results, status) {
                                    if (status == google.maps.GeocoderStatus.OK) {
                                        var new_latitude = results[0].geometry.location.lat();
                                        var new_longitude = results[0].geometry.location.lng();
                                        lat_con.val(new_latitude);
                                        lng_con.val(new_longitude);
                                        map.setCenter(results[0].geometry.location);//center the map over the result

                                        // clear markers
                                        for (var i = 0; i < markers.length; i++) {
                                            markers[i].setMap(null);
                                        }
                                        //

                                        //place a marker at the location
                                        var marker = new google.maps.Marker({
                                            map: map,
                                            position: results[0].geometry.location,
                                            draggable: true,
                                            title: '',
                                            icon: '<?php echo esc_js($marker_image) ?>',
                                        });

                                        markers.push(marker);

                                        google.maps.event.addListener(marker, 'dragend', function (event) {
                                            document.getElementById("jobsearch_location_lat_<?php echo ($rand_num) ?>").value = this.getPosition().lat();
                                            document.getElementById("jobsearch_location_lng_<?php echo ($rand_num) ?>").value = this.getPosition().lng();
                                        });
                                    }
                                });
                            }

                            jQuery(document).on('change', '#jobsearch_location_address_<?php echo ($rand_num) ?>', function () {
                                find_on_map<?php echo ($rand_num) ?>($(this));
                            });

                            jQuery(document).on('click', '#jobsearch-findmap-<?php echo absint($rand_num); ?>', function (e) {
                                e.preventDefault();
                                find_on_map<?php echo ($rand_num) ?>($('#jobsearch_location_address_<?php echo ($rand_num) ?>'));
                                false;
                            });
                    <?php
                }
                ?>
                    });
                    // load state
                </script>
                <?php
            }
            if ($loc_location1 != '') {
                ?>
                <script>
                    jQuery(document).ready(function () {
                        if (jQuery('.location_location1').length > 0) {
                            jQuery('.location_location1').trigger('change');
                        }
                    });
                </script>
                <?php
            }
        }
    }

    public function jobsearch_dashboard_location_map_callback($id = '') {
        global $jobsearch_form_fields, $jobsearch_plugin_options, $sitepress, $jobsearch_gdapi_allocation;

        $all_location_allow = isset($jobsearch_plugin_options['all_location_allow']) ? $jobsearch_plugin_options['all_location_allow'] : '';

        if ($all_location_allow == 'on') {

            $all_locations_type = isset($jobsearch_plugin_options['all_locations_type']) ? $jobsearch_plugin_options['all_locations_type'] : '';

            $lang_code = '';
            $admin_ajax_url = admin_url('admin-ajax.php');
            if (function_exists('icl_object_id') && function_exists('wpml_init_language_switcher')) {
                $lang_code = $sitepress->get_current_language();
                $admin_ajax_url = add_query_arg(array('lang' => $lang_code), $admin_ajax_url);
            }

            wp_register_script('jobsearch-location', jobsearch_plugin_get_url('modules/locations/js/location-functions.js'), array('jquery'), '', true);
            // Localize the script
            $jobsearch_location_common_arr = array(
                'plugin_url' => jobsearch_plugin_get_url(),
                'ajax_url' => $admin_ajax_url,
            );

            $switch_location_fields = isset($jobsearch_plugin_options['switch_location_fields']) ? $jobsearch_plugin_options['switch_location_fields'] : '';

            $required_fields_count = isset($jobsearch_plugin_options['jobsearch-location-required-fields-count']) ? $jobsearch_plugin_options['jobsearch-location-required-fields-count'] : 'all';
            $label_location1 = isset($jobsearch_plugin_options['jobsearch-location-label-location1']) ? apply_filters('wpml_translate_single_string', $jobsearch_plugin_options['jobsearch-location-label-location1'], 'JobSearch Options', 'Location First Field - ' . $jobsearch_plugin_options['jobsearch-location-label-location1'], $lang_code) : esc_html__('Country', 'wp-jobsearch');
            $label_location2 = isset($jobsearch_plugin_options['jobsearch-location-label-location2']) ? apply_filters('wpml_translate_single_string', $jobsearch_plugin_options['jobsearch-location-label-location2'], 'JobSearch Options', 'Location Second Field - ' . $jobsearch_plugin_options['jobsearch-location-label-location2'], $lang_code) : esc_html__('State', 'wp-jobsearch');
            $label_location3 = isset($jobsearch_plugin_options['jobsearch-location-label-location3']) ? apply_filters('wpml_translate_single_string', $jobsearch_plugin_options['jobsearch-location-label-location3'], 'JobSearch Options', 'Location Third Field - ' . $jobsearch_plugin_options['jobsearch-location-label-location3'], $lang_code) : esc_html__('Region', 'wp-jobsearch');
            $label_location4 = isset($jobsearch_plugin_options['jobsearch-location-label-location4']) ? apply_filters('wpml_translate_single_string', $jobsearch_plugin_options['jobsearch-location-label-location4'], 'JobSearch Options', 'Location Forth Field - ' . $jobsearch_plugin_options['jobsearch-location-label-location4'], $lang_code) : esc_html__('City', 'wp-jobsearch');

            $default_location = isset($jobsearch_plugin_options['jobsearch-location-default-address']) ? $jobsearch_plugin_options['jobsearch-location-default-address'] : '';

            $map_styles = isset($jobsearch_plugin_options['jobsearch-location-map-style']) ? $jobsearch_plugin_options['jobsearch-location-map-style'] : '';

            $allow_full_address = isset($jobsearch_plugin_options['location-allow-full-address']) ? $jobsearch_plugin_options['location-allow-full-address'] : '';
            $allow_location_map = isset($jobsearch_plugin_options['location-allow-map']) ? $jobsearch_plugin_options['location-allow-map'] : '';

            $def_map_zoom = isset($jobsearch_plugin_options['jobsearch-location-map-zoom']) && $jobsearch_plugin_options['jobsearch-location-map-zoom'] > 0 ? absint($jobsearch_plugin_options['jobsearch-location-map-zoom']) : '12';

            //
            $loc_firstf_is_req = isset($jobsearch_plugin_options['loc_firstf_is_req']) ? $jobsearch_plugin_options['loc_firstf_is_req'] : '';
            $loc_scndf_is_req = isset($jobsearch_plugin_options['loc_scndf_is_req']) ? $jobsearch_plugin_options['loc_scndf_is_req'] : '';
            $loc_thrdf_is_req = isset($jobsearch_plugin_options['loc_thrdf_is_req']) ? $jobsearch_plugin_options['loc_thrdf_is_req'] : '';
            $loc_forthf_is_req = isset($jobsearch_plugin_options['loc_forthf_is_req']) ? $jobsearch_plugin_options['loc_forthf_is_req'] : '';
            $loc_fadresf_is_req = isset($jobsearch_plugin_options['loc_fadresf_is_req']) ? $jobsearch_plugin_options['loc_fadresf_is_req'] : '';
            //
            $allow_latlng_fileds = isset($jobsearch_plugin_options['allow_latlng_fileds']) ? $jobsearch_plugin_options['allow_latlng_fileds'] : '';
            //

            wp_localize_script('jobsearch-location', 'jobsearch_location_common_vars', $jobsearch_location_common_arr);
            wp_enqueue_script('jobsearch-location');
            if ($allow_location_map == 'yes') {
                wp_enqueue_script('jobsearch-google-map');
            }
            $rand_num = rand(1000000, 99999999);
            $loc_location1 = get_post_meta($id, 'jobsearch_field_location_location1', true);
            $loc_location2 = get_post_meta($id, 'jobsearch_field_location_location2', true);
            $loc_location3 = get_post_meta($id, 'jobsearch_field_location_location3', true);
            $loc_location4 = get_post_meta($id, 'jobsearch_field_location_location4', true);
            $loc_address = get_post_meta($id, 'jobsearch_field_location_address', true);
            $loc_lat = get_post_meta($id, 'jobsearch_field_location_lat', true);
            $loc_lng = get_post_meta($id, 'jobsearch_field_location_lng', true);
            $loc_zoom = get_post_meta($id, 'jobsearch_field_location_zoom', true);
            $map_height = get_post_meta($id, 'jobsearch_field_map_height', true);
            $marker_image = get_post_meta($id, 'jobsearch_field_marker_image', true);

            if (($loc_lat == '' || $loc_lng == '') && $default_location != '') {

                $loc_geo_cords = jobsearch_address_to_cords($default_location);
                $loc_lat = isset($loc_geo_cords['lat']) ? $loc_geo_cords['lat'] : '';
                $loc_lng = isset($loc_geo_cords['lng']) ? $loc_geo_cords['lng'] : '';
            }

            if ($loc_lat == '' || $loc_lng == '') {
                $loc_lat = '37.090240';
                $loc_lng = '-95.712891';
            }

            if ($map_height == '' || $map_height <= 100) {
                $map_height = 250;
            }
            if ($loc_zoom == '') {
                $loc_zoom = $def_map_zoom;
            }

            if ($all_locations_type != 'api') {
                $please_select = esc_html__('Please select', 'wp-jobsearch');
                $location_location1 = array('' => $please_select . ' ' . $label_location1);
                $location_location2 = array('' => $please_select . ' ' . $label_location2);
                $location_location3 = array('' => $please_select . ' ' . $label_location3);
                $location_location4 = array('' => $please_select . ' ' . $label_location4);
//                $location_obj = get_terms('job-location', array(
//                    'orderby' => 'name',
//                    'order' => 'ASC',
//                    'hide_empty' => 0,
//                    'parent' => 0,
//                ));
                $location_obj = jobsearch_custom_get_terms('job-location');
                foreach ($location_obj as $country_arr) {
                    $location_location1[$country_arr->slug] = $country_arr->name;
                    // get all state, region and city
                    // not neccessory for first load, it will populate on select country
                }
            }
            ?>
            <div class="jobsearch-employer-box-section">
                <div class="jobsearch-profile-title"><h2><?php esc_html_e('Address / Location', 'wp-jobsearch') ?></h2></div>
                <ul class="jobsearch-row jobsearch-employer-profile-form">
                    <?php
                    ob_start();

                    if ($all_locations_type != 'api') {
                        ?>
                        <li class="jobsearch-column-6" style="display: <?php echo ($switch_location_fields == 'on' ? 'inline-block' : 'none') ?>;">
                            <label><?php echo esc_html($label_location1) ?><?php echo ($loc_firstf_is_req == 'yes' ? ' *' : '') ?></label>
                            <div class="jobsearch-profile-select">
                                <?php
                                $field_params = array(
                                    'classes' => 'location_location1 selectize-select' . ($loc_firstf_is_req == 'yes' ? ' selectize-req-field' : ''),
                                    'id' => 'location_location1_' . $rand_num,
                                    'name' => 'location_location1',
                                    'options' => $location_location1,
                                    'force_std' => $loc_location1,
                                    'ext_attr' => ' data-randid="' . $rand_num . '" data-nextfieldelement="' . $please_select . ' ' . $label_location2 . '" data-nextfieldval="' . $loc_location2 . '"',
                                );
                                $jobsearch_form_fields->select_field($field_params);
                                ?>
                            </div>
                        </li>
                        <?php
                        if ($required_fields_count > 1 || $required_fields_count == 'all') {
                            ?>
                            <li class="jobsearch-column-6" style="display: <?php echo ($switch_location_fields == 'on' ? 'inline-block' : 'none') ?>;">
                                <label><?php echo esc_html($label_location2) ?><?php echo ($loc_scndf_is_req == 'yes' ? ' *' : '') ?></label>
                                <div class="jobsearch-profile-select">
                                    <?php
                                    $field_params = array(
                                        'classes' => 'location_location2 location_location2_selectize' . ($loc_scndf_is_req == 'yes' ? ' selectize-req-field' : ''),
                                        'id' => 'location_location2_' . $rand_num,
                                        'name' => 'location_location2',
                                        'options' => $location_location2,
                                        'force_std' => $loc_location2,
                                        'ext_attr' => ' data-randid="' . $rand_num . '" data-nextfieldelement="' . $please_select . ' ' . $label_location3 . '" data-nextfieldval="' . $loc_location3 . '"',
                                    );
                                    $jobsearch_form_fields->select_field($field_params);
                                    ?>
                                    <span class="jobsearch-field-loader location_location2_<?php echo absint($rand_num); ?>"></span>
                                </div>
                            </li>
                            <?php
                        }
                        if ($required_fields_count > 2 || $required_fields_count == 'all') {
                            ?>
                            <li class="jobsearch-column-6" style="display: <?php echo ($switch_location_fields == 'on' ? 'inline-block' : 'none') ?>;">
                                <label><?php echo esc_html($label_location3) ?><?php echo ($loc_thrdf_is_req == 'yes' ? ' *' : '') ?></label>
                                <div class="jobsearch-profile-select">
                                    <?php
                                    $field_params = array(
                                        'classes' => 'location_location3 location_location3_selectize' . ($loc_thrdf_is_req == 'yes' ? ' selectize-req-field' : ''),
                                        'id' => 'location_location3_' . $rand_num,
                                        'name' => 'location_location3',
                                        'options' => $location_location3,
                                        'force_std' => $loc_location3,
                                        'ext_attr' => ' data-randid="' . $rand_num . '" data-nextfieldelement="' . $please_select . ' ' . $label_location4 . '" data-nextfieldval="' . $loc_location4 . '"',
                                    );
                                    $jobsearch_form_fields->select_field($field_params);
                                    ?>
                                    <span class="jobsearch-field-loader location_location3_<?php echo absint($rand_num); ?>"></span>
                                </div>
                            </li>
                        <?php } if ($required_fields_count > 3 || $required_fields_count == 'all') { ?>
                            <li class="jobsearch-column-6" style="display: <?php echo ($switch_location_fields == 'on' ? 'inline-block' : 'none') ?>;">
                                <label><?php echo esc_html($label_location4) ?><?php echo ($loc_forthf_is_req == 'yes' ? ' *' : '') ?></label>
                                <div class="jobsearch-profile-select">
                                    <?php
                                    $field_params = array(
                                        'classes' => 'location_location4 location_location4_selectize' . ($loc_forthf_is_req == 'yes' ? ' selectize-req-field' : ''),
                                        'id' => 'location_location4_' . $rand_num,
                                        'name' => 'location_location4',
                                        'options' => $location_location4,
                                        'force_std' => $loc_location4,
                                        'ext_attr' => ' data-randid="' . $rand_num . '"',
                                    );
                                    $jobsearch_form_fields->select_field($field_params);
                                    ?>
                                    <span class="jobsearch-field-loader location_location4_<?php echo absint($rand_num); ?>"></span>
                                </div>
                            </li>
                            <?php
                        }
                    } else {
                        wp_enqueue_script('jobsearch-gdlocation-api');
                        $jobsearch_locsetin_options = get_option('jobsearch_locsetin_options');

                        $api_contries_list = $jobsearch_gdapi_allocation::get_countries();

                        $loc_optionstype = isset($jobsearch_locsetin_options['loc_optionstype']) ? $jobsearch_locsetin_options['loc_optionstype'] : '';
                        $contry_singl_contry = isset($jobsearch_locsetin_options['contry_singl_contry']) ? $jobsearch_locsetin_options['contry_singl_contry'] : '';
                        $contry_order = isset($jobsearch_locsetin_options['contry_order']) ? $jobsearch_locsetin_options['contry_order'] : '';
                        $contry_order = $contry_order != '' ? $contry_order : 'alpha';
                        $contry_filtring = isset($jobsearch_locsetin_options['contry_filtring']) ? $jobsearch_locsetin_options['contry_filtring'] : '';
                        $contry_filtring = $contry_filtring != '' ? $contry_filtring : 'none';
                        $contry_filtr_limreslts = isset($jobsearch_locsetin_options['contry_filtr_limreslts']) ? $jobsearch_locsetin_options['contry_filtr_limreslts'] : '';
                        $contry_filtr_limreslts = $contry_filtr_limreslts <= 0 ? 1000000 : $contry_filtr_limreslts;
                        $contry_filtrinc_contries = isset($jobsearch_locsetin_options['contry_filtrinc_contries']) ? $jobsearch_locsetin_options['contry_filtrinc_contries'] : '';
                        $contry_filtrexc_contries = isset($jobsearch_locsetin_options['contry_filtrexc_contries']) ? $jobsearch_locsetin_options['contry_filtrexc_contries'] : '';
                        $contry_preselct = isset($jobsearch_locsetin_options['contry_preselct']) ? $jobsearch_locsetin_options['contry_preselct'] : '';
                        $contry_preselct = $contry_preselct != '' ? $contry_preselct : 'none';
                        $contry_presel_contry = isset($jobsearch_locsetin_options['contry_presel_contry']) ? $jobsearch_locsetin_options['contry_presel_contry'] : '';

                        // For saved country
                        if ($loc_location1 != '' && in_array($loc_location1, $api_contries_list)) {
                            $contry_preselct = 'by_contry';
                            $contry_singl_contry = $contry_presel_contry = array_search($loc_location1, $api_contries_list);
                        }
                        //
                        $continent_group = isset($jobsearch_locsetin_options['continent_group']) ? $jobsearch_locsetin_options['continent_group'] : '';
                        $continent_order = isset($jobsearch_locsetin_options['continent_order']) ? $jobsearch_locsetin_options['continent_order'] : '';
                        $continent_order = $continent_order != '' ? $continent_order : 'alpha';
                        $continent_filter = isset($jobsearch_locsetin_options['continent_filter']) ? $jobsearch_locsetin_options['continent_filter'] : '';
                        $continent_filter = $continent_filter != '' ? $continent_filter : 'none';
                        $continents_selected = isset($jobsearch_locsetin_options['continents_selected']) ? $jobsearch_locsetin_options['continents_selected'] : '';
                        //
                        $state_order = isset($jobsearch_locsetin_options['state_order']) ? $jobsearch_locsetin_options['state_order'] : '';
                        $state_order = $state_order != '' ? $state_order : 'alpha';
                        $state_filtring = isset($jobsearch_locsetin_options['state_filtring']) ? $jobsearch_locsetin_options['state_filtring'] : '';
                        $state_filtring = $state_filtring != '' ? $state_filtring : 'none';
                        $state_filtr_limreslts = isset($jobsearch_locsetin_options['state_filtr_limreslts']) ? $jobsearch_locsetin_options['state_filtr_limreslts'] : '';
                        $state_filtr_limreslts = $state_filtr_limreslts <= 0 ? 1000000 : $state_filtr_limreslts;
                        //
                        $city_order = isset($jobsearch_locsetin_options['city_order']) ? $jobsearch_locsetin_options['city_order'] : '';
                        $city_order = $city_order != '' ? $city_order : 'alpha';
                        $city_filtring = isset($jobsearch_locsetin_options['city_filtring']) ? $jobsearch_locsetin_options['city_filtring'] : '';
                        $city_filtring = $city_filtring != '' ? $city_filtring : 'none';
                        $city_filtr_limreslts = isset($jobsearch_locsetin_options['city_filtr_limreslts']) ? $jobsearch_locsetin_options['city_filtr_limreslts'] : '';
                        $city_filtr_limreslts = $city_filtr_limreslts <= 0 ? 1000000 : $city_filtr_limreslts;
                        //

                        $continents_class = '';
                        if ($continent_group == 'on') {
                            $continents_class = ' group-continents';
                            if ($continent_order == 'alpha') {
                                $continents_class .= ' group-order-alpha';
                            } else if ($continent_order == 'by_population') {
                                $continents_class .= ' group-order-pop';
                            } else if ($continent_order == 'north_america') {
                                $continents_class .= ' group-order-na';
                            } else if ($continent_order == 'europe') {
                                $continents_class .= ' group-order-eu';
                            } else if ($continent_order == 'africa') {
                                $continents_class .= ' group-order-af';
                            } else if ($continent_order == 'oceania') {
                                $continents_class .= ' group-order-oc';
                            } else if ($continent_order == 'asia') {
                                $continents_class .= ' group-order-as';
                            } else if ($continent_order == 'rand') {
                                $continents_class .= ' group-order-rand';
                            }

                            //
                            if ($continent_filter == 'by_select' && !empty($continents_selected) && is_array($continents_selected)) {
                                $inc_continents_selected = implode('-', $continents_selected);
                                $continents_class .= ' continent-include-' . $inc_continents_selected;
                            }
                        }

                        $contries_class = '';
                        if ($contry_order == 'alpha') {
                            $contries_class .= ' order-alpha';
                        } else if ($contry_order == 'by_population') {
                            $contries_class .= ' order-pop';
                        } else if ($contry_order == 'random') {
                            $contries_class .= ' order-rand';
                        }
                        if ($contry_filtring == 'limt_results' && $contry_filtr_limreslts > 0) {
                            $contries_class .= ' limit-pop-' . absint($contry_filtr_limreslts);
                        } else if ($contry_filtring == 'inc_contries' && !empty($contry_filtrinc_contries) && is_array($contry_filtrinc_contries)) {
                            $inc_contries_implist = implode('-', $contry_filtrinc_contries);
                            $contries_class .= ' include-' . $inc_contries_implist;
                        } else if ($contry_filtring == 'exc_contries' && !empty($contry_filtrexc_contries) && is_array($contry_filtrexc_contries)) {
                            $exc_contries_implist = implode('-', $contry_filtrexc_contries);
                            $contries_class .= ' exclude-' . $exc_contries_implist;
                        }
                        if ($contry_preselct == 'by_contry' && $contry_presel_contry != '') {
                            $contries_class .= ' presel-' . $contry_presel_contry;
                        } else if ($contry_preselct == 'by_user_ip') {
                            $contries_class .= ' presel-byip';
                        }

                        //
                        $states_class = '';
                        if ($state_order == 'alpha') {
                            $states_class .= ' order-alpha';
                        } else if ($state_order == 'by_population') {
                            $states_class .= ' order-pop';
                        } else if ($state_order == 'random') {
                            $states_class .= ' order-rand';
                        }

                        //
                        $cities_class = '';
                        if ($city_order == 'alpha') {
                            $cities_class .= ' order-alpha';
                        } else if ($city_order == 'by_population') {
                            $cities_class .= ' order-pop';
                        } else if ($city_order == 'random') {
                            $cities_class .= ' order-rand';
                        }

                        if ($loc_optionstype == '0' || $loc_optionstype == '1') {
                            ?>
                            <li class="jobsearch-column-6">
                                <label><?php esc_html_e('Country', 'wp-jobsearch') ?></label>
                                <div id="jobsearch-gdapilocs-contrycon" data-val="<?php echo ($loc_location1) ?>" class="jobsearch-profile-select">
                                    <select name="jobsearch_field_location_location1" class="countries<?php echo ($contries_class . $continents_class) ?>" id="countryId">
                                        <option value=""><?php esc_html_e('Select Country', 'wp-jobsearch') ?></option>
                                    </select>
                                </div>
                            </li>
                            <?php
                        }
                        ?>
                        <li class="jobsearch-column-6">
                            <label><?php esc_html_e('State', 'wp-jobsearch') ?></label>
                            <?php
                            if ($loc_optionstype == '2' || $loc_optionstype == '3') {
                                ?>
                                <input type="hidden" name="country" id="countryId" value="<?php echo ($contry_singl_contry) ?>"/>
                                <?php
                            }
                            ?>
                            <div id="jobsearch-gdapilocs-statecon" data-val="<?php echo ($loc_location2) ?>" class="jobsearch-profile-select">
                                <select name="jobsearch_field_location_location2" class="states<?php echo ($states_class) ?>" id="stateId">
                                    <option value=""><?php esc_html_e('Select State', 'wp-jobsearch') ?></option>
                                </select>
                            </div>
                        </li>
                        <?php
                        if ($loc_optionstype == '1' || $loc_optionstype == '2') {
                            ?>
                            <li class="jobsearch-column-6">
                                <label><?php esc_html_e('City', 'wp-jobsearch') ?></label>
                                <div id="jobsearch-gdapilocs-citycon" data-val="<?php echo ($loc_location3) ?>" class="jobsearch-profile-select">
                                    <select name="jobsearch_field_location_location3" class="cities<?php echo ($cities_class) ?>" id="cityId">
                                        <option value=""><?php esc_html_e('Select City', 'wp-jobsearch') ?></option>
                                    </select>
                                </div>
                            </li>
                            <?php
                        }
                    }

                    $full_addr_title = esc_html__('Full Address', 'wp-jobsearch');
                    //
                    if ($loc_fadresf_is_req == 'yes') {
                        $full_addr_title = esc_html__('Full Address *', 'wp-jobsearch');
                    }
                    ?>
                    <li class="jobsearch-column-<?php echo ($allow_location_map != 'yes' ? '12' : '10') ?>" <?php echo ($allow_full_address != 'yes' ? 'style="display: none;"' : '') ?>>
                        <label><?php echo ($full_addr_title) ?></label>
                        <?php
                        $field_params = array(
                            'id' => 'jobsearch_location_address_' . $rand_num,
                            'name' => 'location_address',
                            'classes' => '' . ($loc_fadresf_is_req == 'yes' && $allow_full_address == 'yes' ? ' jobsearch-req-field jobsearch-cpreq-field' : ''),
                            'force_std' => $loc_address,
                        );
                        $jobsearch_form_fields->input_field($field_params);
                        ?>
                        <input id="check_loc_addr_<?php echo ($rand_num) ?>" type="hidden" value="<?php echo ($loc_address) ?>">
                    </li>

                    <li class="jobsearch-column-2" <?php echo ($allow_location_map != 'yes' ? 'style="display: none;"' : '') ?>>
                        <button id="jobsearch-findmap-<?php echo absint($rand_num); ?>" class="jobsearch-findmap-btn"><?php esc_html_e('Find on Map', 'wp-jobsearch') ?></button>
                    </li>
                    <?php
                    $loc_fields_html = ob_get_clean();
                    echo apply_filters('jobsearch_dash_loc_address_simpfields', $loc_fields_html, $id, $loc_fadresf_is_req, $rand_num);
                    ?>

                    <li class="jobsearch-column-4 dash-maploc-latfield" <?php echo ($allow_location_map == 'yes' && $allow_latlng_fileds == 'yes' ? '' : 'style="display: none;"') ?>>
                        <label><?php esc_html_e('Latitude', 'wp-jobsearch') ?></label>
                        <?php
                        $field_params = array(
                            'id' => 'jobsearch_location_lat_' . $rand_num,
                            'name' => 'location_lat',
                            'force_std' => $loc_lat,
                        );
                        $jobsearch_form_fields->input_field($field_params);
                        ?>
                    </li>
                    <li class="jobsearch-column-4 dash-maploc-lngfield" <?php echo ($allow_location_map == 'yes' && $allow_latlng_fileds == 'yes' ? '' : 'style="display: none;"') ?>>
                        <label><?php esc_html_e('Longitude', 'wp-jobsearch') ?></label>
                        <?php
                        $field_params = array(
                            'id' => 'jobsearch_location_lng_' . $rand_num,
                            'name' => 'location_lng',
                            'force_std' => $loc_lng,
                        );
                        $jobsearch_form_fields->input_field($field_params);
                        ?>
                    </li>
                    <li class="jobsearch-column-4 dash-maploc-zoomfield" <?php echo ($allow_location_map == 'yes' && $allow_latlng_fileds == 'yes' ? '' : 'style="display: none;"') ?>>
                        <label><?php esc_html_e('Zoom', 'wp-jobsearch') ?></label>
                        <?php
                        $field_params = array(
                            'id' => 'jobsearch_location_zoom_' . $rand_num,
                            'name' => 'location_zoom',
                            'force_std' => $loc_zoom,
                        );
                        $jobsearch_form_fields->input_field($field_params);
                        ?>
                    </li>
                    <li class="jobsearch-column-12" <?php echo ($allow_location_map != 'yes' ? 'style="display: none;"' : '') ?>>
                        <div class="jobsearch-profile-map"><div id="jobsearch-map-<?php echo absint($rand_num); ?>" style="width: 100%; height: <?php echo ($allow_location_map != 'yes' ? '0' : $map_height) ?>px;"></div></div>
                        <span class="jobsearch-short-message" <?php echo ($allow_location_map != 'yes' ? 'style="display: none;"' : '') ?>><?php esc_html_e('For the precise location, you can drag and drop the pin.', 'wp-jobsearch') ?></span>
                    </li>
                </ul>
            </div>
            <?php
            if ($allow_location_map == 'yes') {
                ?>
                <script>
                    var map;
                    var markers = [];
                    jQuery(document).ready(function () {
                        function jobsearch_map_autocomplete_fields_<?php echo ($rand_num) ?>() {
                            var autocomplete_input = document.getElementById('jobsearch_location_address_<?php echo ($rand_num) ?>');
                            var autocomplete = new google.maps.places.Autocomplete(autocomplete_input);
                        }
                        google.maps.event.addDomListener(window, 'load', jobsearch_map_autocomplete_fields_<?php echo ($rand_num) ?>);
                <?php
                if ($loc_lat != '' && $loc_lng != '' && $loc_zoom != '') {
                    ?>
                            function initMap<?php echo ($rand_num) ?>() {
                                var myLatLng = {lat: <?php echo esc_js($loc_lat) ?>, lng: <?php echo esc_js($loc_lng) ?>};
                                map = new google.maps.Map(document.getElementById('jobsearch-map-<?php echo absint($rand_num); ?>'), {
                                    zoom: <?php echo esc_js($loc_zoom) ?>,
                                    center: myLatLng,
                                    streetViewControl: false,
                                    scrollwheel: false,
                                    mapTypeControl: false,
                                });
                    <?php
                    if ($map_styles != '') {
                        $map_styles = stripslashes($map_styles);
                        $map_styles = preg_replace('/\s+/', ' ', trim($map_styles));
                        ?>
                                    var styles = '<?php echo ($map_styles) ?>';
                                    if (styles != '') {
                                        styles = jQuery.parseJSON(styles);
                                        var styledMap = new google.maps.StyledMapType(
                                                styles,
                                                {name: 'Styled Map'}
                                        );
                                        map.mapTypes.set('map_style', styledMap);
                                        map.setMapTypeId('map_style');
                                    }
                        <?php
                    }
                    ?>

                                var marker = new google.maps.Marker({
                                    position: myLatLng,
                                    map: map,
                                    draggable: true,
                                    title: '',
                                    icon: '<?php echo esc_js($marker_image) ?>',
                                });

                                markers.push(marker);

                                google.maps.event.addListener(map, 'zoom_changed', function () {
                                    var zoom_lvl = map.getZoom();
                                    document.getElementById("jobsearch_location_zoom_<?php echo absint($rand_num); ?>").value = zoom_lvl;
                                });
                                google.maps.event.addListener(marker, 'dragend', function (event) {
                                    document.getElementById("jobsearch_location_lat_<?php echo absint($rand_num); ?>").value = this.getPosition().lat();
                                    document.getElementById("jobsearch_location_lng_<?php echo absint($rand_num); ?>").value = this.getPosition().lng();
                                });
                            }

                            google.maps.event.addDomListener(window, 'load', initMap<?php echo ($rand_num) ?>);

                            function find_on_map<?php echo ($rand_num) ?>(_this) {
                                var $ = jQuery;
                                var geocoder = new google.maps.Geocoder();
                                var addres = _this.val();
                                var lat_con = $('#jobsearch_location_lat_<?php echo ($rand_num) ?>');
                                var lng_con = $('#jobsearch_location_lng_<?php echo ($rand_num) ?>');
                                geocoder.geocode({address: addres}, function (results, status) {
                                    if (status == google.maps.GeocoderStatus.OK) {
                                        var new_latitude = results[0].geometry.location.lat();
                                        var new_longitude = results[0].geometry.location.lng();
                                        lat_con.val(new_latitude);
                                        lng_con.val(new_longitude);
                                        map.setCenter(results[0].geometry.location);//center the map over the result

                                        // clear markers
                                        for (var i = 0; i < markers.length; i++) {
                                            markers[i].setMap(null);
                                        }
                                        //

                                        //place a marker at the location
                                        var marker = new google.maps.Marker({
                                            map: map,
                                            position: results[0].geometry.location,
                                            draggable: true,
                                            title: '',
                                            icon: '<?php echo esc_js($marker_image) ?>',
                                        });

                                        markers.push(marker);

                                        google.maps.event.addListener(marker, 'dragend', function (event) {
                                            document.getElementById("jobsearch_location_lat_<?php echo ($rand_num) ?>").value = this.getPosition().lat();
                                            document.getElementById("jobsearch_location_lng_<?php echo ($rand_num) ?>").value = this.getPosition().lng();
                                        });
                                    }
                                });
                            }

                            jQuery(document).on('change', '#jobsearch_location_address_<?php echo ($rand_num) ?>', function () {
                                find_on_map<?php echo ($rand_num) ?>($(this));
                            });

                            jQuery(document).on('click', '#jobsearch-findmap-<?php echo absint($rand_num); ?>', function (e) {
                                e.preventDefault();
                                find_on_map<?php echo ($rand_num) ?>($('#jobsearch_location_address_<?php echo ($rand_num) ?>'));
                                false;
                            });
                    <?php
                }
                ?>
                    });
                    // load state

                </script>
                <?php
            }
            ?>
            <script>
                jQuery(document).ready(function () {
                    if (jQuery('.location_location1').length > 0) {
                        jQuery('.location_location1').trigger('change');
                    }
                });
            </script>
            <?php
        }
    }

    public function loc_levels_names_to_address() {
        $loc_loc_1 = isset($_POST['loc_loc_1']) ? $_POST['loc_loc_1'] : '';
        $loc_loc_2 = isset($_POST['loc_loc_2']) ? $_POST['loc_loc_2'] : '';
        $loc_loc_3 = isset($_POST['loc_loc_3']) ? $_POST['loc_loc_3'] : '';

        $job_city_title = '';
        $get_job_city = $loc_loc_2;
        if ($get_job_city == '' && $loc_loc_3 != '') {
            $get_job_city = $loc_loc_3;
        }

        $get_job_country = $loc_loc_1;

        $job_city_tax = $get_job_city != '' ? get_term_by('slug', $get_job_city, 'job-location') : '';
        if ($get_job_city != '' && is_object($job_city_tax)) {
            $job_city_title = isset($job_city_tax->name) ? $job_city_tax->name : '';

            $job_country_tax = $get_job_country != '' ? get_term_by('slug', $get_job_country, 'job-location') : '';
            if (is_object($job_country_tax)) {
                $job_city_title .= isset($job_country_tax->name) ? ', ' . $job_country_tax->name : '';
            }
        } else if ($job_city_title == '') {
            $job_country_tax = $get_job_country != '' ? get_term_by('slug', $get_job_country, 'job-location') : '';
            if (is_object($job_country_tax)) {
                $job_city_title .= isset($job_country_tax->name) ? $job_country_tax->name : '';
            }
        }

        echo json_encode(array('locadres' => $job_city_title));
        die;
    }

    public function jobsearch_location_load_location2_data_callback() {
        $html = '';
        $nextfieldelement = $_POST['nextfieldelement'];
        $nextfieldval = $_POST['nextfieldval'];
        $html .= "<option value=\"\">" . $nextfieldelement . "</option>" . "\n";
        if (isset($_POST['location_location']) && $_POST['location_location'] != '') {
            $location = $_POST['location_location'];
            //$term = get_term_by('slug', $location, 'job-location');
            $term = jobsearch_get_custom_term_by('slug', $location);

            if (!empty($term)) {

//                $location_obj = get_terms('job-location', array(
//                    'orderby' => 'name',
//                    'order' => 'ASC',
//                    'hide_empty' => 0,
//                    'parent' => $term->term_id,
//                ));
                $term_parent = $term->term_id;
                $location_obj = jobsearch_custom_get_terms('job-location', $term_parent);

                if (!empty($location_obj)) {
                    foreach ($location_obj as $country_arr) {
                        $selected = $country_arr->slug == $nextfieldval ? ' selected="selected"' : '';
                        $html .= "<option{$selected} value=\"{$country_arr->slug}\">{$country_arr->name}</option>" . "\n";
                    }
                }
            }
        }
        echo json_encode(array('html' => $html));

        wp_die();
    }

    public function jobsearch_location_plugin_option_fields($sections) {
        global $wp_filesystem;
        require_once ABSPATH . '/wp-admin/includes/file.php';
        if (false === ($creds = request_filesystem_credentials(wp_nonce_url('post.php'), '', false, false, array()) )) {
            return true;
        }
        if (!WP_Filesystem($creds)) {
            request_filesystem_credentials(wp_nonce_url('post.php'), '', true, false, array());
            return true;
        }
        $countries_file = jobsearch_plugin_get_path('modules/import-locations/data/countries.json');
        $get_json_data = $wp_filesystem->get_contents($countries_file);
        $countries_data = json_decode($get_json_data, true);
        $all_countries_data = isset($countries_data['countries']) ? $countries_data['countries'] : array();

        $res_countries_list = array();
        if (!empty($all_countries_data)) {
            foreach ($all_countries_data as $country_data) {
                $res_countries_list[$country_data['sortname']] = $country_data['name'];
            }
        }
        //$sections = array(); // Delete this if you want to keep original sections!
        $sections[] = array(
            'title' => __('Location Settings', 'wp-jobsearch'),
            'id' => 'location-settings',
            'desc' => __('Location fields setting.', 'wp-jobsearch'),
            'icon' => 'el el-map-marker',
            'fields' => array(
                array(
                    'id' => 'location-settings-section',
                    'type' => 'section',
                    'title' => __('Location fields settings', 'wp-jobsearch'),
                    'subtitle' => '',
                    'indent' => true,
                ),
                array(
                    'id' => 'all_location_allow',
                    'type' => 'button_set',
                    'title' => __('Locations', 'wp-jobsearch'),
                    'subtitle' => __('Enable/Disable Locations from all site.', 'wp-jobsearch'),
                    'desc' => '',
                    'options' => array(
                        'on' => __('Enable', 'wp-jobsearch'),
                        'off' => __('Disable', 'wp-jobsearch'),
                    ),
                    'default' => 'on',
                ),
                array(
                    'id' => 'all_locations_type',
                    'type' => 'button_set',
                    'title' => __('Locations Type', 'wp-jobsearch'),
                    'required' => array('all_location_allow', 'equals', 'on'),
                    'subtitle' => __('Select locations type.', 'wp-jobsearch'),
                    'desc' => '',
                    'options' => array(
                        'manual' => __('Manual', 'wp-jobsearch'),
                        'api' => __('API Locations', 'wp-jobsearch'),
                    ),
                    'default' => 'api',
                ),
                array(
                    'id' => 'switch_location_fields',
                    'type' => 'button_set',
                    'title' => __('Location Fields', 'wp-jobsearch'),
                    'required' => array(
                        array('all_location_allow', 'equals', 'on'),
                        array('all_locations_type', 'equals', 'manual'),
                    ),
                    'subtitle' => __('Enable/Disable Location fields (Countries, Cities, States).', 'wp-jobsearch'),
                    'desc' => '',
                    'options' => array(
                        'on' => __('On', 'wp-jobsearch'),
                        'off' => __('Off', 'wp-jobsearch'),
                    ),
                    'default' => 'on',
                ),
                array(
                    'id' => 'jobsearch-location-required-fields-count',
                    'type' => 'select',
                    'title' => __('Enable Fields', 'wp-jobsearch'),
                    'required' => array(
                        array('all_location_allow', 'equals', 'on'),
                        array('switch_location_fields', 'equals', 'on'),
                        array('all_locations_type', 'equals', 'manual'),
                    ),
                    'subtitle' => __('Select how many fields will enable for location.', 'wp-jobsearch'),
                    'desc' => '',
                    'options' => array(
                        'all' => __('All Fields', 'wp-jobsearch'),
                        '1' => __('One Field only', 'wp-jobsearch'),
                        '2' => __('Two Fields', 'wp-jobsearch'),
                        '3' => __('Three Fields', 'wp-jobsearch'),
                    ),
                    'default' => 'all',
                ),
                array(
                    'id' => 'jobsearch-location-label-location1',
                    'type' => 'text',
                    'title' => __('First Field Label', 'wp-jobsearch'),
                    'required' => array(
                        array('all_location_allow', 'equals', 'on'),
                        array('switch_location_fields', 'equals', 'on'),
                        array('all_locations_type', 'equals', 'manual'),
                    ),
                    'subtitle' => __('First location field label i.e Country', 'wp-jobsearch'),
                    'desc' => '',
                    'default' => 'Country',
                ),
                array(
                    'id' => 'loc_firstf_is_req',
                    'type' => 'button_set',
                    'title' => __('Required First Field', 'wp-jobsearch'),
                    'required' => array(
                        array('all_location_allow', 'equals', 'on'),
                        array('switch_location_fields', 'equals', 'on'),
                        array('all_locations_type', 'equals', 'manual'),
                    ),
                    'subtitle' => __('Choose if you want to make this field "required" by user or not.', 'wp-jobsearch'),
                    'desc' => '',
                    'options' => array(
                        'yes' => __('Yes', 'wp-jobsearch'),
                        'no' => __('No', 'wp-jobsearch'),
                    ),
                    'default' => 'yes',
                ),
                array(
                    'id' => 'jobsearch-location-label-location2',
                    'type' => 'text',
                    'title' => __('Second Field Label', 'wp-jobsearch'),
                    'required' => array(
                        array('all_location_allow', 'equals', 'on'),
                        array('switch_location_fields', 'equals', 'on'),
                        array('all_locations_type', 'equals', 'manual'),
                    ),
                    'subtitle' => __('Second location field label i.e State', 'wp-jobsearch'),
                    'desc' => '',
                    'default' => 'State',
                ),
                array(
                    'id' => 'loc_scndf_is_req',
                    'type' => 'button_set',
                    'title' => __('Required Second Field', 'wp-jobsearch'),
                    'required' => array(
                        array('all_location_allow', 'equals', 'on'),
                        array('switch_location_fields', 'equals', 'on'),
                        array('all_locations_type', 'equals', 'manual'),
                    ),
                    'subtitle' => __('Choose if you want to make this field "required" by user or not.', 'wp-jobsearch'),
                    'desc' => '',
                    'options' => array(
                        'yes' => __('Yes', 'wp-jobsearch'),
                        'no' => __('No', 'wp-jobsearch'),
                    ),
                    'default' => 'yes',
                ),
                array(
                    'id' => 'jobsearch-location-label-location3',
                    'type' => 'text',
                    'title' => __('Third Field Label', 'wp-jobsearch'),
                    'required' => array(
                        array('all_location_allow', 'equals', 'on'),
                        array('switch_location_fields', 'equals', 'on'),
                        array('all_locations_type', 'equals', 'manual'),
                    ),
                    'subtitle' => __('Third location field label i.e City', 'wp-jobsearch'),
                    'desc' => '',
                    'default' => 'Region',
                ),
                array(
                    'id' => 'loc_thrdf_is_req',
                    'type' => 'button_set',
                    'title' => __('Required Third Field', 'wp-jobsearch'),
                    'required' => array(
                        array('all_location_allow', 'equals', 'on'),
                        array('switch_location_fields', 'equals', 'on'),
                        array('all_locations_type', 'equals', 'manual'),
                    ),
                    'subtitle' => __('Choose if you want to make this field "required" by user or not.', 'wp-jobsearch'),
                    'desc' => '',
                    'options' => array(
                        'yes' => __('Yes', 'wp-jobsearch'),
                        'no' => __('No', 'wp-jobsearch'),
                    ),
                    'default' => 'yes',
                ),
                array(
                    'id' => 'jobsearch-location-label-location4',
                    'type' => 'text',
                    'title' => __('Fourth Field Label', 'wp-jobsearch'),
                    'required' => array(
                        array('all_location_allow', 'equals', 'on'),
                        array('switch_location_fields', 'equals', 'on'),
                        array('all_locations_type', 'equals', 'manual'),
                    ),
                    'subtitle' => __('Fourth location field label i.e Area', 'wp-jobsearch'),
                    'desc' => '',
                    'default' => 'City',
                ),
                array(
                    'id' => 'loc_forthf_is_req',
                    'type' => 'button_set',
                    'title' => __('Required Fourth Field', 'wp-jobsearch'),
                    'required' => array(
                        array('all_location_allow', 'equals', 'on'),
                        array('switch_location_fields', 'equals', 'on'),
                        array('all_locations_type', 'equals', 'manual'),
                    ),
                    'subtitle' => __('Choose if you want to make this field "required" by user or not.', 'wp-jobsearch'),
                    'desc' => '',
                    'options' => array(
                        'yes' => __('Yes', 'wp-jobsearch'),
                        'no' => __('No', 'wp-jobsearch'),
                    ),
                    'default' => 'yes',
                ),
                array(
                    'id' => 'location-allow-full-address',
                    'type' => 'button_set',
                    'title' => __('Allow Full Address', 'wp-jobsearch'),
                    'required' => array('all_location_allow', 'equals', 'on'),
                    'subtitle' => __('Allow users to enter full address.', 'wp-jobsearch'),
                    'desc' => '',
                    'options' => array(
                        'yes' => __('Yes', 'wp-jobsearch'),
                        'no' => __('No', 'wp-jobsearch'),
                    ),
                    'default' => 'yes',
                ),
                array(
                    'id' => 'loc_fadresf_is_req',
                    'type' => 'button_set',
                    'title' => __('Required Full Address Field', 'wp-jobsearch'),
                    'subtitle' => __('Choose if you want to make this field "required" by user or not.', 'wp-jobsearch'),
                    'required' => array(
                        array('all_location_allow', 'equals', 'on'),
                        array('location-allow-full-address', 'equals', 'yes'),
                    ),
                    'desc' => '',
                    'options' => array(
                        'yes' => __('Yes', 'wp-jobsearch'),
                        'no' => __('No', 'wp-jobsearch'),
                    ),
                    'default' => 'yes',
                ),
                array(
                    'id' => 'jobsearch-location-default-address',
                    'type' => 'text',
                    'required' => array(
                        array('all_location_allow', 'equals', 'on'),
                        array('location-allow-full-address', 'equals', 'yes'),
                    ),
                    'title' => __('Default Address', 'wp-jobsearch'),
                    'subtitle' => __('Set Default Locaion address for your site.', 'wp-jobsearch'),
                    'desc' => '',
                    'default' => '',
                ),
                array(
                    'id' => 'locmapsettings-sec',
                    'type' => 'section',
                    'title' => __('Map settings', 'wp-jobsearch'),
                    'subtitle' => '',
                    'indent' => true,
                ),
                array(
                    'id' => 'location-allow-map',
                    'type' => 'button_set',
                    'title' => __('Allow Map', 'wp-jobsearch'),
                    'subtitle' => '',
                    'desc' => '',
                    'options' => array(
                        'yes' => __('Yes', 'wp-jobsearch'),
                        'no' => __('No', 'wp-jobsearch'),
                    ),
                    'default' => 'yes',
                ),
                array(
                    'id' => 'allow_latlng_fileds',
                    'type' => 'button_set',
                    'title' => __('Allow Lat/Lng/Zoom Fields', 'wp-jobsearch'),
                    'subtitle' => '',
                    'required' => array('location-allow-map', 'equals', 'yes'),
                    'desc' => '',
                    'options' => array(
                        'yes' => __('Yes', 'wp-jobsearch'),
                        'no' => __('No', 'wp-jobsearch'),
                    ),
                    'default' => 'yes',
                ),
                array(
                    'id' => 'jobsearch-location-map-zoom',
                    'type' => 'text',
                    'required' => array('location-allow-map', 'equals', 'yes'),
                    'title' => __('Default Map Zoom', 'wp-jobsearch'),
                    'subtitle' => __('Set Default Zoom Level for Map.', 'wp-jobsearch'),
                    'desc' => '',
                    'default' => '12',
                ),
                array(
                    'id' => 'jobsearch-location-map-style',
                    'type' => 'textarea',
                    'title' => __('Map Style', 'wp-jobsearch'),
                    'required' => array('location-allow-map', 'equals', 'yes'),
                    'subtitle' => sprintf(__('You can get all map styles from <a href="%s" target="_blank">here</a>', 'wp-jobsearch'), 'https://snazzymaps.com/'),
                    'desc' => '',
                    'default' => '',
                ),
                array(
                    'id' => 'jobsearch-detail-map-switch',
                    'type' => 'button_set',
                    'multi' => true,
                    'title' => __('Display Map', 'wp-jobsearch'),
                    'subtitle' => '',
                    'desc' => __('Enable / Disable Map view in detail Pages', 'wp-jobsearch'),
                    'options' => array(
                        'employer' => __('Employer', 'wp-jobsearch'),
                        'job' => __('Job', 'wp-jobsearch'),
                    ),
                    'default' => array('employer', 'job'),
                ),
                array(
                    'id' => 'geo-location-settings-sec',
                    'type' => 'section',
                    'title' => __('Geo Location settings', 'wp-jobsearch'),
                    'subtitle' => '',
                    'indent' => true,
                ),
                array(
                    'id' => 'top_search_geoloc',
                    'type' => 'button_set',
                    'title' => __('AutoFill Geo Location', 'wp-jobsearch'),
                    'subtitle' => '',
                    'desc' => '',
                    'options' => array(
                        'yes' => __('Yes', 'wp-jobsearch'),
                        'no' => __('No', 'wp-jobsearch'),
                    ),
                    'default' => 'yes',
                ),
                array(
                    'id' => 'top_search_locsugg',
                    'type' => 'button_set',
                    'title' => __('Location Suggestions', 'wp-jobsearch'),
                    'subtitle' => '',
                    'desc' => '',
                    'options' => array(
                        'yes' => __('Yes', 'wp-jobsearch'),
                        'no' => __('No', 'wp-jobsearch'),
                    ),
                    'default' => 'yes',
                ),
                array(
                    'id' => 'restrict_contries_locsugg',
                    'type' => 'select',
                    'multi' => true,
                    'title' => __('Autocomplete Countries List', 'wp-jobsearch'),
                    'subtitle' => '',
                    'desc' => '',
                    'options' => $res_countries_list,
                    'default' => '',
                ),
                array(
                    'id' => 'radius-settings-sec',
                    'type' => 'section',
                    'title' => __('Radius settings', 'wp-jobsearch'),
                    'subtitle' => '',
                    'indent' => true,
                ),
                array(
                    'id' => 'top_search_radius',
                    'type' => 'button_set',
                    'title' => __('Radius', 'wp-jobsearch'),
                    'subtitle' => '',
                    'desc' => '',
                    'options' => array(
                        'yes' => __('Yes', 'wp-jobsearch'),
                        'no' => __('No', 'wp-jobsearch'),
                    ),
                    'default' => 'yes',
                ),
                array(
                    'id' => 'top_search_def_radius',
                    'type' => 'text',
                    'title' => __('Default Radius', 'wp-jobsearch'),
                    'subtitle' => '',
                    'desc' => '',
                    'default' => '50',
                ),
                array(
                    'id' => 'top_search_max_radius',
                    'type' => 'text',
                    'title' => __('Maximum Radius', 'wp-jobsearch'),
                    'subtitle' => '',
                    'desc' => '',
                    'default' => '500',
                ),
            ),
        );
        // echo '<pre>'; print_r($sections);echo '</pre>'; 
        return $sections;
    }

}

// class Jobsearch_Location 
global $Jobsearch_Location_obj;
$Jobsearch_Location_obj = new Jobsearch_Location();

