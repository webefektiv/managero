<?php
if (!defined('ABSPATH')) {
    die;
}

global $jobsearch_gdapi_allocation;

if (!class_exists('jobsearch_allocation_settings_handle')) {

    class jobsearch_allocation_settings_handle {

        // hook things up
        public function __construct() {
            $this->save_locsettings();
            add_action('wp_enqueue_scripts', array($this, 'loc_style_scripts'));
            add_action('admin_enqueue_scripts', array($this, 'loc_style_scripts'));
            add_action('admin_menu', array($this, 'jobsearch_loc_settings_create_menu'));
        }

        public static function get_countries() {
            $api_url = 'http://geodata.solutions/api/api.php?type=getCountries';

            $response = wp_remote_get($api_url, array('timeout' => 180));

            $contries_list = '';
            if (!is_wp_error($response) && is_array($response)) {
                $header = $response['headers']; // array of http header lines
                $body = $response['body']; // use the content

                $cont_list = json_decode($body, true);
                if (isset($cont_list['result']) && !empty($cont_list['result']) && is_array($cont_list['result'])) {
                    $contries_list = $cont_list['result'];
                }
            }
            return $contries_list;
        }

        public static function get_states($contry_code) {
            $api_url = 'http://geodata.solutions/api/api.php?type=getStates&countryId=' . $contry_code . '&addClasses=order-alpha';

            $response = wp_remote_get($api_url, array('timeout' => 180));

            $statets_list = '';
            if (!is_wp_error($response) && is_array($response)) {
                $header = $response['headers']; // array of http header lines
                $body = $response['body']; // use the content

                $cont_list = json_decode($body, true);
                if (isset($cont_list['result']) && !empty($cont_list['result']) && is_array($cont_list['result'])) {
                    $statets_list = $cont_list['result'];
                }
            }
            return $statets_list;
        }

        public static function get_cities($contry_code, $state_code) {
            $api_url = 'http://geodata.solutions/api/api.php?type=getCities&countryId=' . $contry_code . '&stateId=' . $state_code . '&addClasses=order-alpha';

            $response = wp_remote_get($api_url, array('timeout' => 180));

            $cities_list = '';
            if (!is_wp_error($response) && is_array($response)) {
                $header = $response['headers']; // array of http header lines
                $body = $response['body']; // use the content

                $cont_list = json_decode($body, true);
                if (isset($cont_list['result']) && !empty($cont_list['result']) && is_array($cont_list['result'])) {
                    $cities_list = $cont_list['result'];
                }
            }
            return $cities_list;
        }

        public function loc_style_scripts() {
            $jobsearch_locsetin_options = get_option('jobsearch_locsetin_options');

            $admin_ajax_url = admin_url('admin-ajax.php');

            $loc_optionstype = isset($jobsearch_locsetin_options['loc_optionstype']) ? $jobsearch_locsetin_options['loc_optionstype'] : '';

            if ($loc_optionstype == '0') {
                wp_register_script('jobsearch-gdlocation-api', '//geodata.solutions/includes/countrystate.js', array(), JobSearch_plugin::get_version());
            } else if ($loc_optionstype == '2') {
                wp_register_script('jobsearch-gdlocation-api', '//geodata.solutions/includes/statecity.js', array(), JobSearch_plugin::get_version());
            } else if ($loc_optionstype == '3') {
                wp_register_script('jobsearch-gdlocation-api', '//geodata.solutions/includes/state.js', array(), JobSearch_plugin::get_version());
            } else {
                wp_register_script('jobsearch-gdlocation-api', '//geodata.solutions/includes/countrystatecity.js', array(), JobSearch_plugin::get_version());
            }
        }

        public function jobsearch_loc_settings_create_menu() {
            // create new top-level menu
            add_menu_page(esc_html__('Location Settings', 'wp-jobsearch'), esc_html__('Location Settings', 'wp-jobsearch'), 'administrator', 'jobsearch-location-sett', function () {

                $rand_id = rand(10000000, 99999999);
                wp_enqueue_script('jobsearch-gdlocation-api');
                wp_enqueue_script('jobsearch-selectize');

                $api_contries_list = self::get_countries();

                $jobsearch_locsetin_options = get_option('jobsearch_locsetin_options');

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
                ?>

                <div class="jobsearch-allocssett-holder">
                    <script>
                        jQuery(document).ready(function () {
                            jQuery('.selectiz-locfield').selectize({
                                plugins: ['remove_button'],
                            });
                        });
                        jQuery(document).on('click', '.jobsearch-locsve-btn', function () {
                            jQuery('#allocs-settings-form').submit();
                        });
                        jQuery(document).on('click', '.panl-title > a', function () {
                            var _this = jQuery(this);
                            var main_acholder = jQuery('#panl-filter-options');
                            main_acholder.find('.panl-opened').removeClass('panl-opened').addClass('panl-closed');
                            main_acholder.find('.panel-body-opened').removeClass('panel-body-opened').addClass('panel-body-closed');
                            main_acholder.find('.panel-body-closed').hide();
                            //
                            _this.parents('.loc-panl-sec').find('.panl-closed').removeClass('panl-closed').addClass('panl-opened');
                            _this.parents('.loc-panl-sec').find('.panel-body-closed').removeClass('panel-body-closed').addClass('panel-body-opened');
                            _this.parents('.loc-panl-sec').find('.panel-body-opened').slideDown();
                        });
                        jQuery(document).on('change', 'select[name="loc_optionstype"]', function () {
                            if (jQuery(this).val() == '0' || jQuery(this).val() == '1') {
                                jQuery('.allocs-contdrpdwn-selt').slideUp();
                            } else {
                                jQuery('.allocs-contdrpdwn-selt').slideDown();
                            }
                        });
                        jQuery(document).on('change', 'input[name="contry_filtring"]', function () {
                            if (jQuery(this).val() == 'inc_contries') {
                                jQuery('#contry-filtrinc-cont-<?php echo ($rand_id) ?>').slideDown();
                                jQuery('#contry-filtrexc-cont-<?php echo ($rand_id) ?>').slideUp();
                            } else if (jQuery(this).val() == 'exc_contries') {
                                jQuery('#contry-filtrexc-cont-<?php echo ($rand_id) ?>').slideDown();
                                jQuery('#contry-filtrinc-cont-<?php echo ($rand_id) ?>').slideUp();
                            } else {
                                jQuery('#contry-filtrexc-cont-<?php echo ($rand_id) ?>').slideUp();
                                jQuery('#contry-filtrinc-cont-<?php echo ($rand_id) ?>').slideUp();
                            }
                        });
                        jQuery(document).on('change', 'input[name="contry_preselct"]', function () {
                            if (jQuery(this).val() == 'by_contry') {
                                jQuery('#contry-presel-contry-<?php echo ($rand_id) ?>').slideDown();
                            } else {
                                jQuery('#contry-presel-contry-<?php echo ($rand_id) ?>').slideUp();
                            }
                        });
                        //
                        jQuery(document).on('change', 'input[type="checkbox"][name="continent_group"]', function () {
                            if (jQuery(this).is(":checked")) {
                                jQuery('.contint-group-options').slideDown();
                            } else {
                                jQuery('.contint-group-options').slideUp();
                            }
                        });
                    </script>
                    <div class="allocs-sett-label"><h1><?php esc_html_e('Location Settings', 'wp-jobsearch') ?></h1></div>
                    <div class="allocs-sett-view">
                        <div class="preview-loc-exmphdin"><h3><?php esc_html_e('Preview Example', 'wp-jobsearch') ?></h3></div>
                        <?php
                        if ($loc_optionstype == '0' || $loc_optionstype == '1') {
                            ?>
                            <select name="country" class="countries<?php echo ($contries_class . $continents_class) ?>" id="countryId">
                                <option value=""><?php esc_html_e('Select Country', 'wp-jobsearch') ?></option>
                            </select>
                            <?php
                        } else {
                            ?>
                            <input type="hidden" name="country" id="countryId" value="<?php echo ($contry_singl_contry) ?>"/>
                            <?php
                        }
                        ?>
                        <select name="state" class="states<?php echo ($states_class) ?>" id="stateId">
                            <option value=""><?php esc_html_e('Select State', 'wp-jobsearch') ?></option>
                        </select>
                        <?php
                        if ($loc_optionstype == '1' || $loc_optionstype == '2') {
                            ?>
                            <select name="city" class="cities<?php echo ($cities_class) ?>" id="cityId">
                                <option value=""><?php esc_html_e('Select City', 'wp-jobsearch') ?></option>
                            </select>
                            <?php
                        }
                        ?>
                    </div>
                    <div class="allocs-sett-filtrs">
                        <div class="allocs-configdrpdwn-sett">
                            <span><?php esc_html_e('Configure your dropdowns', 'wp-jobsearch') ?></span>
                            <a href="javascript:void(0);" class="jobsearch-locsve-btn button button-primary"><?php esc_html_e('Generate Settings', 'wp-jobsearch') ?></a>
                        </div>
                        <form id="allocs-settings-form" method="post">
                            <div class="allocs-configdrpdwn-sett">
                                <span><?php esc_html_e('Dropdown Sequence', 'wp-jobsearch') ?></span>
                                <select class="drpdwn-type-control" name="loc_optionstype">
                                    <option <?php echo ($loc_optionstype == '0' ? 'selected="selected"' : '') ?> value="0">
                                        <?php esc_html_e('Country - State', 'wp-jobsearch') ?>
                                    </option>
                                    <option <?php echo ($loc_optionstype == '1' || $loc_optionstype == '' ? 'selected="selected"' : '') ?> value="1">
                                        <?php esc_html_e('Country - State - City', 'wp-jobsearch') ?>
                                    </option>
                                    <option <?php echo ($loc_optionstype == '2' ? 'selected="selected"' : '') ?> value="2">
                                        <?php esc_html_e('State - City (Single country)', 'wp-jobsearch') ?>
                                    </option>
                                    <option <?php echo ($loc_optionstype == '3' ? 'selected="selected"' : '') ?> value="3">
                                        <?php esc_html_e('State (Single country)', 'wp-jobsearch') ?>
                                    </option>
                                </select>
                                <input type="hidden" name="jobsearch_allocs_setingsubmit" value="1">
                            </div>
                            <div class="allocs-contdrpdwn-selt" style="display: <?php echo ($loc_optionstype == '0' || $loc_optionstype == '1' ? 'none' : 'block') ?>;">
                                <label for="contry-singl-contry-<?php echo ($rand_id) ?>"><?php esc_html_e('Select Country', 'wp-jobsearch') ?></label>
                                <select id="contry-singl-contry-<?php echo ($rand_id) ?>" name="contry_singl_contry">
                                    <?php
                                    if (!empty($api_contries_list)) {
                                        foreach ($api_contries_list as $contry_key => $contry_title) {
                                            ?>
                                            <option value="<?php echo ($contry_key) ?>" <?php echo ($contry_singl_contry == $contry_key ? 'selected="selected"' : '') ?>><?php echo ($contry_title) ?></option>
                                            <?php
                                        }
                                    }
                                    ?>
                                </select>
                            </div>
                            <div id="panl-filter-options" class="jobsearch-filtr-options">
                                <div class="loc-panl-sec">
                                    <div class="panl-heading">
                                        <h4 class="panl-title">
                                            <a href="javascript:void(0);" class="panl-opened">
                                                <?php esc_html_e('Country Options', 'wp-jobsearch') ?>
                                            </a>
                                        </h4>
                                    </div>
                                    <div class="panel-body-opened">
                                        <div class="panl-body">
                                            <div class="filtr-chks-box">
                                                <span><?php esc_html_e('Ordering', 'wp-jobsearch') ?></span>
                                                <ul>
                                                    <li>
                                                        <input id="contry-order-alpha-<?php echo ($rand_id) ?>" type="radio" name="contry_order" value="alpha" <?php echo ($contry_order == 'alpha' ? 'checked="checked"' : '') ?>>
                                                        <label for="contry-order-alpha-<?php echo ($rand_id) ?>"><?php esc_html_e('Alphabetical', 'wp-jobsearch') ?></label>
                                                    </li>
                                                    <li>
                                                        <input id="contry-order-bypop-<?php echo ($rand_id) ?>" type="radio" name="contry_order" value="by_population" <?php echo ($contry_order == 'by_population' ? 'checked="checked"' : '') ?>>
                                                        <label for="contry-order-bypop-<?php echo ($rand_id) ?>"><?php esc_html_e('By Population', 'wp-jobsearch') ?></label>
                                                    </li>
                                                    <li>
                                                        <input id="contry-order-randm-<?php echo ($rand_id) ?>" type="radio" name="contry_order" value="random" <?php echo ($contry_order == 'random' ? 'checked="checked"' : '') ?>>
                                                        <label for="contry-order-randm-<?php echo ($rand_id) ?>"><?php esc_html_e('Random', 'wp-jobsearch') ?></label>
                                                    </li>
                                                </ul>
                                            </div>
                                            <div class="filtr-chks-box">
                                                <span><?php esc_html_e('Filtering', 'wp-jobsearch') ?></span>
                                                <ul>
                                                    <li>
                                                        <input id="contry-filtr-none-<?php echo ($rand_id) ?>" type="radio" name="contry_filtring" value="none" <?php echo ($contry_filtring == 'none' ? 'checked="checked"' : '') ?>>
                                                        <label for="contry-filtr-none-<?php echo ($rand_id) ?>"><?php esc_html_e('None', 'wp-jobsearch') ?></label>
                                                    </li>
                                                    <!--                                                    <li class="with-frm-fields">
                                                                                                            <div class="orig-radio-field">
                                                                                                                <input id="contry-filtr-limitr-<?php echo ($rand_id) ?>" type="radio" name="contry_filtring" value="limt_results" <?php echo ($contry_filtring == 'limt_results' ? 'checked="checked"' : '') ?>>
                                                                                                                <label for="contry-filtr-limitr-<?php echo ($rand_id) ?>"><?php esc_html_e('Limit results by population', 'wp-jobsearch') ?></label>
                                                                                                            </div>
                                                                                                            <div class="filtrs-inputext-field">
                                                                                                                <input type="number" name="contry_filtr_limreslts" value="<?php echo absint($contry_filtr_limreslts) ?>" min="1">
                                                                                                            </div>
                                                                                                        </li>-->
                                                    <li class="with-frm-fields">
                                                        <div class="orig-radio-field">
                                                            <input id="contry-filtr-inclist-<?php echo ($rand_id) ?>" type="radio" name="contry_filtring" value="inc_contries" <?php echo ($contry_filtring == 'inc_contries' ? 'checked="checked"' : '') ?>>
                                                            <label for="contry-filtr-inclist-<?php echo ($rand_id) ?>"><?php esc_html_e('Include only countries selected', 'wp-jobsearch') ?></label>
                                                        </div>
                                                        <div id="contry-filtrinc-cont-<?php echo ($rand_id) ?>" class="filtrs-select-field multiseltc" style="display: <?php echo ($contry_filtring == 'inc_contries' ? 'block' : 'none') ?>;">
                                                            <select multiple="multiple" name="contry_filtrinc_contries[]">
                                                                <?php
                                                                if (!empty($api_contries_list)) {
                                                                    foreach ($api_contries_list as $contry_key => $contry_title) {
                                                                        ?>
                                                                        <option value="<?php echo ($contry_key) ?>" <?php echo (!empty($contry_filtrinc_contries) && is_array($contry_filtrinc_contries) && in_array($contry_key, $contry_filtrinc_contries) ? 'selected="selected"' : '') ?>><?php echo ($contry_title) ?></option>
                                                                        <?php
                                                                    }
                                                                }
                                                                ?>
                                                            </select>
                                                        </div>
                                                    </li>
                                                    <li class="with-frm-fields">
                                                        <div class="orig-radio-field">
                                                            <input id="contry-filtr-exclist-<?php echo ($rand_id) ?>" type="radio" name="contry_filtring" value="exc_contries" <?php echo ($contry_filtring == 'exc_contries' ? 'checked="checked"' : '') ?>>
                                                            <label for="contry-filtr-exclist-<?php echo ($rand_id) ?>"><?php esc_html_e('Exclude countries selected', 'wp-jobsearch') ?></label>
                                                        </div>
                                                        <div id="contry-filtrexc-cont-<?php echo ($rand_id) ?>" class="filtrs-select-field multiseltc" style="display: <?php echo ($contry_filtring == 'exc_contries' ? 'block' : 'none') ?>;">
                                                            <select multiple="multiple" name="contry_filtrexc_contries[]">
                                                                <?php
                                                                if (!empty($api_contries_list)) {
                                                                    foreach ($api_contries_list as $contry_key => $contry_title) {
                                                                        ?>
                                                                        <option value="<?php echo ($contry_key) ?>" <?php echo (!empty($contry_filtrexc_contries) && is_array($contry_filtrexc_contries) && in_array($contry_key, $contry_filtrexc_contries) ? 'selected="selected"' : '') ?>><?php echo ($contry_title) ?></option>
                                                                        <?php
                                                                    }
                                                                }
                                                                ?>
                                                            </select>
                                                        </div>
                                                    </li>
                                                </ul>
                                            </div>
                                            <div class="filtr-chks-box">
                                                <span><?php esc_html_e('Preselect Country', 'wp-jobsearch') ?></span>
                                                <ul>
                                                    <li>
                                                        <input id="contry-presel-none-<?php echo ($rand_id) ?>" type="radio" name="contry_preselct" value="none" <?php echo ($contry_preselct == 'none' ? 'checked="checked"' : '') ?>>
                                                        <label for="contry-presel-none-<?php echo ($rand_id) ?>"><?php esc_html_e('None', 'wp-jobsearch') ?></label>
                                                    </li>
                                                    <li class="with-frm-fields">
                                                        <div class="orig-radio-field">
                                                            <input id="contry-presel-bycontry-<?php echo ($rand_id) ?>" type="radio" name="contry_preselct" value="by_contry" <?php echo ($contry_preselct == 'by_contry' ? 'checked="checked"' : '') ?>>
                                                            <label for="contry-presel-bycontry-<?php echo ($rand_id) ?>"><?php esc_html_e('Choose country', 'wp-jobsearch') ?></label>
                                                        </div>
                                                        <div id="contry-presel-contry-<?php echo ($rand_id) ?>" class="filtrs-select-field" style="display: <?php echo ($contry_preselct == 'by_contry' ? 'block' : 'none') ?>;">
                                                            <select name="contry_presel_contry">
                                                                <?php
                                                                if (!empty($api_contries_list)) {
                                                                    foreach ($api_contries_list as $contry_key => $contry_title) {
                                                                        ?>
                                                                        <option value="<?php echo ($contry_key) ?>" <?php echo ($contry_presel_contry == $contry_key ? 'selected="selected"' : '') ?>><?php echo ($contry_title) ?></option>
                                                                        <?php
                                                                    }
                                                                }
                                                                ?>
                                                            </select>
                                                        </div>
                                                    </li>
                                                    <li>
                                                        <input id="contry-presel-byip-<?php echo ($rand_id) ?>" type="radio" name="contry_preselct" value="by_user_ip" <?php echo ($contry_preselct == 'by_user_ip' ? 'checked="checked"' : '') ?>>
                                                        <label for="contry-presel-byip-<?php echo ($rand_id) ?>"><?php esc_html_e('Predict by user IP', 'wp-jobsearch') ?></label>
                                                    </li>
                                                </ul>
                                            </div>
                                            <div class="filtr-chks-box contint-group-holder">
                                                <div class="contint-group-chkbox">
                                                    <input id="contint-group-chkbox-<?php echo ($rand_id) ?>" type="checkbox" name="continent_group" <?php echo ($continent_group == 'on' ? 'checked="checked"' : '') ?>>
                                                    <label for="contint-group-chkbox-<?php echo ($rand_id) ?>"><?php esc_html_e('Group by continent', 'wp-jobsearch') ?></label>
                                                </div>
                                                <div class="contint-group-options" style="display: <?php echo ($continent_group == 'on' ? 'block' : 'none') ?>;">
                                                    <h3><?php esc_html_e('Options', 'wp-jobsearch') ?></h3>
                                                    <div class="filtr-chks-box">
                                                        <span><?php esc_html_e('Ordering', 'wp-jobsearch') ?></span>
                                                        <ul>
                                                            <li>
                                                                <input id="contint-order-alpha-<?php echo ($rand_id) ?>" type="radio" name="continent_order" value="alpha" <?php echo ($continent_order == 'alpha' ? 'checked="checked"' : '') ?>>
                                                                <label for="contint-order-alpha-<?php echo ($rand_id) ?>"><?php esc_html_e('Alphabetical', 'wp-jobsearch') ?></label>
                                                            </li>
                                                            <li>
                                                                <input id="contint-order-pop-<?php echo ($rand_id) ?>" type="radio" name="continent_order" value="by_pop" <?php echo ($continent_order == 'by_pop' ? 'checked="checked"' : '') ?>>
                                                                <label for="contint-order-pop-<?php echo ($rand_id) ?>"><?php esc_html_e('By population', 'wp-jobsearch') ?></label>
                                                            </li>
                                                            <li>
                                                                <input id="contint-order-northameric-<?php echo ($rand_id) ?>" type="radio" name="continent_order" value="north_america" <?php echo ($continent_order == 'north_america' ? 'checked="checked"' : '') ?>>
                                                                <label for="contint-order-northameric-<?php echo ($rand_id) ?>"><?php esc_html_e('North America first', 'wp-jobsearch') ?></label>
                                                            </li>
                                                            <li>
                                                                <input id="contint-order-europe-<?php echo ($rand_id) ?>" type="radio" name="continent_order" value="europe" <?php echo ($continent_order == 'europe' ? 'checked="checked"' : '') ?>>
                                                                <label for="contint-order-europe-<?php echo ($rand_id) ?>"><?php esc_html_e('Europe First', 'wp-jobsearch') ?></label>
                                                            </li>
                                                            <li>
                                                                <input id="contint-order-africa-<?php echo ($rand_id) ?>" type="radio" name="continent_order" value="africa" <?php echo ($continent_order == 'africa' ? 'checked="checked"' : '') ?>>
                                                                <label for="contint-order-africa-<?php echo ($rand_id) ?>"><?php esc_html_e('Africa first', 'wp-jobsearch') ?></label>
                                                            </li>
                                                            <li>
                                                                <input id="contint-order-aceania-<?php echo ($rand_id) ?>" type="radio" name="continent_order" value="oceania" <?php echo ($continent_order == 'oceania' ? 'checked="checked"' : '') ?>>
                                                                <label for="contint-order-aceania-<?php echo ($rand_id) ?>"><?php esc_html_e('Oceania first', 'wp-jobsearch') ?></label>
                                                            </li>
                                                            <li>
                                                                <input id="contint-order-asia-<?php echo ($rand_id) ?>" type="radio" name="continent_order" value="asia" <?php echo ($continent_order == 'asia' ? 'checked="checked"' : '') ?>>
                                                                <label for="contint-order-asia-<?php echo ($rand_id) ?>"><?php esc_html_e('Asia first', 'wp-jobsearch') ?></label>
                                                            </li>
                                                            <li>
                                                                <input id="contint-order-rand-<?php echo ($rand_id) ?>" type="radio" name="continent_order" value="rand" <?php echo ($continent_order == 'rand' ? 'checked="checked"' : '') ?>>
                                                                <label for="contint-order-rand-<?php echo ($rand_id) ?>"><?php esc_html_e('Random', 'wp-jobsearch') ?></label>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                    <div class="filtr-chks-box">
                                                        <span><?php esc_html_e('Filtering', 'wp-jobsearch') ?></span>
                                                        <ul>
                                                            <li>
                                                                <input id="contint-filtr-none-<?php echo ($rand_id) ?>" type="radio" name="continent_filter" value="none" <?php echo ($continent_filter == 'none' ? 'checked="checked"' : '') ?>>
                                                                <label for="contint-filtr-none-<?php echo ($rand_id) ?>"><?php esc_html_e('None', 'wp-jobsearch') ?></label>
                                                            </li>
                                                            <li class="with-frm-fields">
                                                                <div class="orig-radio-field">
                                                                    <input id="contint-filtr-byselct-<?php echo ($rand_id) ?>" type="radio" name="continent_filter" value="by_select" <?php echo ($continent_filter == 'by_select' ? 'checked="checked"' : '') ?>>
                                                                    <label for="contint-filtr-byselct-<?php echo ($rand_id) ?>"><?php esc_html_e('Include only continents selected', 'wp-jobsearch') ?></label>
                                                                </div>
                                                                <div class="filtrs-select-field">
                                                                    <select id="contint-bysel-selct-<?php echo ($rand_id) ?>" name="continents_selected[]" multiple="multiple">
                                                                        <option value="AF" <?php echo (!empty($continents_selected) && in_array('AF', $continents_selected) ? 'selected="selected"' : '') ?>><?php esc_html_e('Africa', 'wp-jobsearch') ?></option>
                                                                        <option value="AN" <?php echo (!empty($continents_selected) && in_array('AN', $continents_selected) ? 'selected="selected"' : '') ?>><?php esc_html_e('Antarctica', 'wp-jobsearch') ?></option>
                                                                        <option value="AS" <?php echo (!empty($continents_selected) && in_array('AS', $continents_selected) ? 'selected="selected"' : '') ?>><?php esc_html_e('Asia', 'wp-jobsearch') ?></option>
                                                                        <option value="EU" <?php echo (!empty($continents_selected) && in_array('EU', $continents_selected) ? 'selected="selected"' : '') ?>><?php esc_html_e('Europe', 'wp-jobsearch') ?></option>
                                                                        <option value="NA" <?php echo (!empty($continents_selected) && in_array('NA', $continents_selected) ? 'selected="selected"' : '') ?>><?php esc_html_e('North America', 'wp-jobsearch') ?></option>
                                                                        <option value="OC" <?php echo (!empty($continents_selected) && in_array('OC', $continents_selected) ? 'selected="selected"' : '') ?>><?php esc_html_e('Oceania', 'wp-jobsearch') ?></option>
                                                                        <option value="SA" <?php echo (!empty($continents_selected) && in_array('SA', $continents_selected) ? 'selected="selected"' : '') ?>><?php esc_html_e('South America', 'wp-jobsearch') ?></option>
                                                                    </select>
                                                                </div>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="loc-panl-sec">
                                    <div class="panl-heading">
                                        <h4 class="panl-title">
                                            <a href="javascript:void(0);" class="panl-closed">
                                                <?php esc_html_e('State Options', 'wp-jobsearch') ?>
                                            </a>
                                        </h4>
                                    </div>
                                    <div class="panel-body-closed" style="display: none;">
                                        <div class="panl-body">
                                            <div class="filtr-chks-box">
                                                <span><?php esc_html_e('Ordering', 'wp-jobsearch') ?></span>
                                                <ul>
                                                    <li>
                                                        <input id="state-order-alpha-<?php echo ($rand_id) ?>" type="radio" name="state_order" value="alpha" <?php echo ($state_order == 'alpha' ? 'checked="checked"' : '') ?>>
                                                        <label for="state-order-alpha-<?php echo ($rand_id) ?>"><?php esc_html_e('Alphabetical', 'wp-jobsearch') ?></label>
                                                    </li>
                                                    <li>
                                                        <input id="state-order-bypop-<?php echo ($rand_id) ?>" type="radio" name="state_order" value="by_population" <?php echo ($state_order == 'by_population' ? 'checked="checked"' : '') ?>>
                                                        <label for="state-order-bypop-<?php echo ($rand_id) ?>"><?php esc_html_e('By Population', 'wp-jobsearch') ?></label>
                                                    </li>
                                                    <li>
                                                        <input id="state-order-randm-<?php echo ($rand_id) ?>" type="radio" name="state_order" value="random" <?php echo ($state_order == 'random' ? 'checked="checked"' : '') ?>>
                                                        <label for="state-order-randm-<?php echo ($rand_id) ?>"><?php esc_html_e('Random', 'wp-jobsearch') ?></label>
                                                    </li>
                                                </ul>
                                            </div>
                                            <!--                                            <div class="filtr-chks-box">
                                                                                            <span><?php esc_html_e('Filtering', 'wp-jobsearch') ?></span>
                                                                                            <ul>
                                                                                                <li>
                                                                                                    <input id="state-filtr-none-<?php echo ($rand_id) ?>" type="radio" name="state_filtring" value="none" <?php echo ($state_filtring == 'none' ? 'checked="checked"' : '') ?>>
                                                                                                    <label for="state-filtr-none-<?php echo ($rand_id) ?>"><?php esc_html_e('None', 'wp-jobsearch') ?></label>
                                                                                                </li>
                                                                                                <li class="with-frm-fields">
                                                                                                    <div class="orig-radio-field">
                                                                                                        <input id="state-filtr-limitr-<?php echo ($rand_id) ?>" type="radio" name="state_filtring" value="limt_results" <?php echo ($state_filtring == 'limt_results' ? 'checked="checked"' : '') ?>>
                                                                                                        <label for="state-filtr-limitr-<?php echo ($rand_id) ?>"><?php esc_html_e('Limit results by population', 'wp-jobsearch') ?></label>
                                                                                                    </div>
                                                                                                    <div class="filtrs-inputext-field">
                                                                                                        <input type="number" name="state_filtr_limreslts" value="<?php echo absint($state_filtr_limreslts) ?>" min="1">
                                                                                                    </div>
                                                                                                </li>
                                                                                            </ul>
                                                                                        </div>-->
                                        </div>
                                    </div>
                                </div>
                                <div class="loc-panl-sec">
                                    <div class="panl-heading">
                                        <h4 class="panl-title">
                                            <a href="javascript:void(0);" class="panl-closed">
                                                <?php esc_html_e('City Options', 'wp-jobsearch') ?>
                                            </a>
                                        </h4>
                                    </div>
                                    <div class="panel-body-closed" style="display: none;">
                                        <div class="panl-body">
                                            <div class="filtr-chks-box">
                                                <span><?php esc_html_e('Ordering', 'wp-jobsearch') ?></span>
                                                <ul>
                                                    <li>
                                                        <input id="city-order-alpha-<?php echo ($rand_id) ?>" type="radio" name="city_order" value="alpha" <?php echo ($city_order == 'alpha' ? 'checked="checked"' : '') ?>>
                                                        <label for="city-order-alpha-<?php echo ($rand_id) ?>"><?php esc_html_e('Alphabetical', 'wp-jobsearch') ?></label>
                                                    </li>
                                                    <li>
                                                        <input id="city-order-bypop-<?php echo ($rand_id) ?>" type="radio" name="city_order" value="by_population" <?php echo ($city_order == 'by_population' ? 'checked="checked"' : '') ?>>
                                                        <label for="city-order-bypop-<?php echo ($rand_id) ?>"><?php esc_html_e('By Population', 'wp-jobsearch') ?></label>
                                                    </li>
                                                    <li>
                                                        <input id="city-order-randm-<?php echo ($rand_id) ?>" type="radio" name="city_order" value="random" <?php echo ($city_order == 'random' ? 'checked="checked"' : '') ?>>
                                                        <label for="city-order-randm-<?php echo ($rand_id) ?>"><?php esc_html_e('Random', 'wp-jobsearch') ?></label>
                                                    </li>
                                                </ul>
                                            </div>
                                            <!--                                            <div class="filtr-chks-box">
                                                                                            <span><?php esc_html_e('Filtering', 'wp-jobsearch') ?></span>
                                                                                            <ul>
                                                                                                <li>
                                                                                                    <input id="city-filtr-none-<?php echo ($rand_id) ?>" type="radio" name="city_filtring" value="none" <?php echo ($city_filtring == 'none' ? 'checked="checked"' : '') ?>>
                                                                                                    <label for="city-filtr-none-<?php echo ($rand_id) ?>"><?php esc_html_e('None', 'wp-jobsearch') ?></label>
                                                                                                </li>
                                                                                                <li class="with-frm-fields">
                                                                                                    <div class="orig-radio-field">
                                                                                                        <input id="city-filtr-limitr-<?php echo ($rand_id) ?>" type="radio" name="city_filtring" value="limt_results" <?php echo ($city_filtring == 'limt_results' ? 'checked="checked"' : '') ?>>
                                                                                                        <label for="city-filtr-limitr-<?php echo ($rand_id) ?>"><?php esc_html_e('Limit results by population', 'wp-jobsearch') ?></label>
                                                                                                    </div>
                                                                                                    <div class="filtrs-inputext-field">
                                                                                                        <input type="number" name="city_filtr_limreslts" value="<?php echo absint($city_filtr_limreslts) ?>" min="1">
                                                                                                    </div>
                                                                                                </li>
                                                                                            </ul>
                                                                                        </div>-->
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                        <div class="setingsave-btncon">
                            <a href="javascript:void(0);" class="jobsearch-locsve-btn button button-primary"><?php esc_html_e('Generate Settings', 'wp-jobsearch') ?></a>
                        </div>
                    </div>
                </div>

                <?php
            }, '', 30);
        }

        public function save_locsettings() {
            if (isset($_POST['jobsearch_allocs_setingsubmit']) && $_POST['jobsearch_allocs_setingsubmit'] == '1') {
                $data_arr_list = array();
                foreach ($_POST as $post_key => $post_val) {
                    $data_arr_list[$post_key] = $post_val;
                }
                update_option('jobsearch_locsetin_options', $data_arr_list);
            }
        }

    }

    $jobsearch_gdapi_allocation = new jobsearch_allocation_settings_handle();
}
