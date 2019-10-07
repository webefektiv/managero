<script src="/wp-content/plugins/acf-extended/assets/acf-extended.js"></script>
<link href="/wp-content/plugins/acf-extended/assets/acf-extended.css">
<?php
acf_enqueue_uploader();
acf_form_head();



$post_id = $_REQUEST['id'];


$date_job = get_post_meta($post_id , 'job_data_set', true);
//print_r($date_job);
//$date_job =  unserialize($date_job);
//print_r($date_job);


global $jobsearch_plugin_options, $Jobsearch_User_Dashboard_Settings;

$current_user = wp_get_current_user();
$user_id = get_current_user_id();
$user_obj = get_user_by('ID', $user_id);

$employer_id = jobsearch_get_user_employer_id($user_id);

if ($employer_id > 0) {


    ?>

    <div id="rezultatModal">

        <?php

        $tip = 'anunt_template';

        $date_acf = [
            'titlu' => 'Template anunt - Template nou',
            'id' => 'acf-form-job-profile',
            'field_groups' => [881]
        ];

        $settings1 = array(

            /* (string) Unique identifier for the form. Defaults to 'acf-form' */
            'id' => $date_acf['id'],

            /* (int|string) The post ID to load data from and save data to. Defaults to the current post ID.
            Can also be set to 'new_post' to create a new post on submit */
            'post_id' => 'new_post',

            /* (array) An array of post data used to create a post. See wp_insert_post for available parameters.
            The above 'post_id' setting must contain a value of 'new_post' */
            //		'new_post'              => 'new_post',

            /* (array) An array of field group IDs/keys to override the fields displayed in this form */
            'field_groups' => $date_acf['field_groups'] ,
//				'field_groups'          => [ 1366 ],

            /* (array) An array of field IDs/keys to override the fields displayed in this form */
            'fields' => false,

            /* (boolean) Whether or not to show the post title text field. Defaults to false */
            'post_title' => false,

            /* (boolean) Whether or not to show the post content editor field. Defaults to false */
            'post_content' => false,

            /* (boolean) Whether or not to create a form element. Useful when a adding to an existing form. Defaults to true */
            'form' => true,

            /* (array) An array or HTML attributes for the form element */
            'form_attributes' => array(),

            /* (string) The URL to be redirected to after the form is submit. Defaults to the current URL with a GET parameter '?updated=true'.
            A special placeholder '%post_url%' will be converted to post's permalink (handy if creating a new post)
            A special placeholder '%post_id%' will be converted to post's ID (handy if creating a new post) */
            'return' => '',

            /* (string) Extra HTML to add before the fields */
            'html_before_fields' => '',

            /* (string) Extra HTML to add after the fields */
            'html_after_fields' => '',

            /* (string) The text displayed on the submit button */
            'submit_value' => __("Update", 'acf'),

            /* (string) A message displayed above the form after being redirected. Can also be set to false for no message */
            'updated_message' => __("Post updated", 'acf'),

            /* (string) Determines where field labels are places in relation to fields. Defaults to 'top'.
            Choices of 'top' (Above fields) or 'left' (Beside fields) */
            'label_placement' => 'top',

            /* (string) Determines where field instructions are places in relation to fields. Defaults to 'label'.
            Choices of 'label' (Below labels) or 'field' (Below fields) */
            'instruction_placement' => 'label',

            /* (string) Determines element used to wrap a field. Defaults to 'div'
            Choices of 'div', 'tr', 'td', 'ul', 'ol', 'dl' */
            'field_el' => 'div',

            /* (string) Whether to use the WP uploader or a basic input for image and file fields. Defaults to 'wp'
            Choices of 'wp' or 'basic'. Added in v5.2.4 */
            'uploader' => 'wp',

            /* (boolean) Whether to include a hidden input field to capture non human form submission. Defaults to true. Added in v5.3.4 */
            'honeypot' => true,

            /* (string) HTML used to render the updated message. Added in v5.5.10 */
            'html_updated_message' => '<div id="message" class="updated"><p>%s</p></div>',

            /* (string) HTML used to render the submit button. Added in v5.5.10 */
            'html_submit_button' => '<input type="submit" class="acf-button button button-primary button-large" value="%s"  style="display: none;"/>',

            /* (string) HTML used to render the submit button loading spinner. Added in v5.5.10 */
            'html_submit_spinner' => '<span class="acf-spinner"></span>',

            /* (boolean) Whether or not to sanitize all $_POST data with the wp_kses_post() function. Defaults to true. Added in v5.6.5 */
            'kses' => true,

            'load_fields' => $date_job,

            'fields_type' => $tip

        );
        acf_form($settings1);

        ?>
    </div>

    <script>
        // get form to object
        (function ($, undefined) {
            '$:nomunge';

            $.fn.serializeObject = function () {
                var obj = {};

                $.each(this.serializeArray(), function (i, o) {
                    var n = o.name,
                        v = o.value;

                    obj[n] = obj[n] === undefined ? v
                        : $.isArray(obj[n]) ? obj[n].concat(v)
                            : [obj[n], v];
                });

                return obj;
            };

        })(jQuery);

        // modal next step
        function steps_modal() {
            // next step scroll up
            $('#pasul-urmator').click(function () {
                $('a[data-key=field_5d38459692fdc]').trigger('click');
                $('#formModal').animate({scrollTop: 0}, "slow");
            });
            $('#pasul-urmator2').click(function () {
                $('a[data-key=field_5d789c99fcab7]').trigger('click');
                $('#formModal').animate({scrollTop: 0}, "slow");
            });

            $('#pasul-urmator3').click(function () {
                $('a[data-key=field_5d789e0dcf67e]').trigger('click');
                $('#formModal').animate({scrollTop: 0}, "slow");
            });
        }

        // generate scm bar
        function scm_radio_bar() {
            // scm bar
            $('.acf-field-radio').each(function () {
                var nume = $(this).attr('data-name');
                var value = $('input[type=radio]:checked', this).val();

                $('input[type=radio]', this).each(function (index) {
                    if (value == null) {
                        value = 0;
                    }
                    if ((index + 1) > value) {
                        return false;
                    }
                    $(this).css('background-color', 'green');
                });
            });

            // bar
            function checkInput(id) {
                return jQuery('#' + id).is(':checked');
            }

            //  radio to bar
            $('input[type=radio]').live('change', function () {

                var inputId = $(this).attr('id');
                var value = $(this).val();
                // ul
                var parent = $(this).parent().parent().parent();

                var isChecked = checkInput(inputId);

                if (isChecked) {
                    // clear old select
                    $('input[type=radio]', parent).css('background-color', '#fff');

                    $('input[type=radio]', parent).each(function (index) {

                        if ((index + 1) > value) {
                            return false;
                        }
                        $(this).css('background-color', 'green');
                    });

                } else {
                    $(this).css('background-color', '#fff');
                }
            });

        }

        // scm bar values
        var scmLoad = {};

        function get_template_data_scm() {

            var inputs_wrap = $('.acf-field-5d789cadfcab8');
            $('.acf-field-radio', inputs_wrap).each(function () {
                var nume = $(this).attr('data-name');
                var value = $('input[type=radio]:checked', this).val();
                // if val undefined
                if (value == null || value == '') {
                    value = 0;
                }

                scmLoad[nume] = value;
            });
            return scmLoad;

        }

        // get inputs data
        function get_template_data() {
            var obj_values = jQuery('#acf-form-job-profile').serializeObject();
            //    console.log(obj_values);
            var fields = Object.keys(obj_values);

            var object_new_label = [];

            jQuery(fields).each(function () {

                var string = this.replace('acf[', '');
                string = string.replace(']', '');
                var item = string.replace('[', ' ');
                item = item.replace(']', '');
                item = item.replace('[', '');
                item = item.replace(']', '');
                var field = item.substr(item.length - 19);

                var name = jQuery('*[data-key="' + field + '"]').attr('data-name');

                object_new_label[this] = name;

            });

            object_new_label['acf[field_5d0a6a80b2a9b][]'] = 'galerie';
            object_new_label['acf[field_5d0a6b02fa2bd][]'] = 'domeniu';
            object_new_label['acf[field_5d0bfb256cebc][]'] = 'date_visibile';


            var format_obj = {};
            var id = 0;
            for (var key in object_new_label) {

                if (object_new_label.hasOwnProperty(key)) {

                    if (format_obj.hasOwnProperty(object_new_label[key]) && obj_values[key] !== '') {

                        if (typeof (format_obj[object_new_label[key]]) === 'object') {

                        } else {
                            var temp = format_obj[object_new_label[key]];
                            format_obj[object_new_label[key]] = [];
                            format_obj[object_new_label[key]].push(temp);
                        }

                        format_obj[object_new_label[key]].push(obj_values[key]);

                    } else if (obj_values[key] !== '') {

                        format_obj[object_new_label[key]] = obj_values[key];

                    }
                }
            }

            format_obj['fisiere'] = {};
            format_obj['link-uri'] = {};

            if (typeof (format_obj['denumire']) === 'string') {
                format_obj['fisiere'][format_obj['denumire']] = format_obj['fisier'];
            } else {

                for (var key in format_obj['denumire']) {
                    format_obj['fisiere'][format_obj['denumire'][key]] = format_obj['fisier'][key];
                }
            }


            if (typeof (format_obj['titlu']) === 'string') {
                format_obj['link-uri'][format_obj['titlu']] = format_obj['link'];
            } else {
                console.log(format_obj['titlu']);
                for (var key in format_obj['titlu']) {

                    format_obj['link-uri'][format_obj['titlu'][key]] = format_obj['link'][key];
                }
            }

            format_obj['profil_scm'] = get_template_data_scm();

            return format_obj;
        }

        // save functions@modal
        function call_save_template() {

            jQuery('#adauga-job').click(function (event) {

                jQuery('#overwLoad').show();

                var format_obj = get_template_data();
                delete format_obj['undefined'];

                jQuery.ajax({
                    type: "post",
                    url: "<?=  admin_url( 'admin-ajax.php' ) ?>",
                    dataType: "json",
                    data: {
                        'action': 'single_job_insert',
                        'companie': <?= $employer_id ?>,
                        'template' : format_obj,
                        'new': false
                    },
                    success: function (data) {
                        //     console.log(data);
                        location.href =  data.url;
                    },
                    error: function (errorThrown) {
                        console.log(errorThrown);
                    }
                });
            });

            jQuery('#salveaza_template_anunt').click(function (event) {
                jQuery('#overwLoad').show();
                var format_obj = get_template_data();
                delete format_obj['undefined'];

                jQuery.ajax({
                    type: "post",
                    url: "<?=  admin_url('admin-ajax.php'); ?>",
                    data: {
                        'action': 'job_template_save',
                        'companie': <?= $employer_id; ?>,
                        'template': {
                            'nume': format_obj.nume_template,
                            'date': format_obj,
                            'tip': 'anunt_template'
                        },
                        'new': false
                    },
                    success: function (data) {
                        location.reload();
                    }
                    ,
                    error: function (errorThrown) {
                        console.log(errorThrown);
                    }
                });
            });

            jQuery('#salveaza_profil_companie_ca').click(function () {

                jQuery('#overwLoad').show();
                var format_obj = get_template_data();

                format_obj.nume_template = format_obj.nume_profil_companie;
                format_obj.descriere_template = format_obj.descriere_profil_companie;

                console.log(format_obj);

                jQuery.ajax({
                    type: "post",
                    url: "<?=  admin_url('admin-ajax.php'); ?>",
                    data: {
                        'action': 'job_template_save',
                        'companie': <?= $employer_id; ?>,
                        'template': {
                            'nume': format_obj.nume_template,
                            'date': format_obj,
                            'tip': 'company_profile'
                        },
                        'new': false
                    },
                    success: function (data) {
                        // console.log(data);
                        //    location.reload();
                        jQuery('#overwLoad').hide();
                    }
                    ,
                    error: function (errorThrown) {
                        console.log(errorThrown);
                    }
                });

            });

            jQuery('#salveaza_profil_job_ca').click(function (event) {

                jQuery('#overwLoad').show();
                var format_obj = get_template_data();

                format_obj.nume_template = format_obj.nume_profil_job;
                format_obj.descriere_template = format_obj.descriere_profil_job;

                jQuery.ajax({
                    type: "post",
                    url: "<?=  admin_url('admin-ajax.php'); ?>",
                    data: {
                        'action': 'job_template_save',
                        'companie': <?= $employer_id; ?>,
                        'template': {
                            'nume': format_obj.nume_template,
                            'date': format_obj,
                            'tip': 'job_profile'
                        },
                        'new': false
                    },
                    success: function (data) {
                        // console.log(data);
                        //      location.reload();
                        jQuery('#overwLoad').hide();
                    }
                    ,
                    error: function (errorThrown) {
                        console.log(errorThrown);
                    }
                });

            });

            // group butoane
            jQuery('#wrap-salveaza-ca, #wrap-salveaza-profil-companie,  #wrap-salveaza-profil-job, #wrap-save-as-template').hide();

            jQuery('#salveaza_ca_job_profil').click(function () {
                jQuery('#wrap-salveaza-ca').slideToggle();
            });

            jQuery('#trigger_salveaza_profil_companie_ca').click(function () {
                jQuery('#wrap-salveaza-profil-companie').slideToggle();
            });

            jQuery('#show_profil_job').click(function () {
                jQuery('#wrap-salveaza-profil-job').slideToggle();
            });

            jQuery('#salveaza_template_anunt_ca').click(function () {
                jQuery('#wrap-save-as-template').slideToggle();
            });
            // end butoane

            // salveaza ca
            jQuery('#ok_salveaza_ca').click(function () {

                var format_obj = get_template_data();

                jQuery.ajax({
                    type: "post",
                    url: "<?=  admin_url('admin-ajax.php'); ?>",
                    data: {
                        'action': 'job_template_save',
                        'companie': <?= $employer_id; ?>,
                        'template': {
                            'nume': format_obj.nume_template_as,
                            'date': format_obj,
                            'tip': 'anunt_template'
                        },
                        'new': true
                    },
                    success: function (data) {
                        // console.log(data);
                        location.reload();
                    }
                    ,
                    error: function (errorThrown) {
                        console.log(errorThrown);
                    }
                });

            });

        }

        // schimba tabul
        jQuery('.template-on').click(function () {
            jQuery('.template-on').removeClass('activlink');
            jQuery(this).addClass('activlink');

            var template = '#template-' + jQuery(this).attr('data-template');
            jQuery('.template-wrap').removeClass('activ');
            jQuery(template).addClass('activ');
        });

        function get_temp_profile() {
            var job_profil = jQuery('#modalZone').attr('data-job');
            var companie_profil = jQuery('#modalZone').attr('data-companie');
            var profile = {
                'job_profil': job_profil,
                'companie_profil': companie_profil
            };

            return profile;
        }

        function aplica_profile() {

            jQuery('#aplica_companie').click(function () {

                jQuery('#overwLoad').show();
                var format_obj = get_template_data();
                var template = jQuery('#acf-field_5d4bf6f20de13-field_5d4bf7380de14').val();

                var profile = get_temp_profile();

                jQuery.ajax({
                    type: "post",
                    url: "<?=  admin_url('admin-ajax.php'); ?>",
                    data: {
                        'action': 'job_profile_apply_partial_2',
                        'companie': <?= $employer_id; ?>,
                        'template': template,
                        'template_temp': format_obj,
                        'tip': 'company_profile_combo',
                        'profil_job': profile.job_profil,
                        'profil_companie': profile.companie_profil
                    },

                    success: function (data) {

                        jQuery('#rezultatModal').html(data);
                        jQuery('#overwLoad').hide();
                        jQuery('#formModal').modal('show');

                        (function ($) {
                            acf.do_action('append', $('#acf-form-job-profile'));
                        })(jQuery);

                        (function ($) {
                            $(document).ready(function () {
                                acf.unload.active = false;
                            });
                        })(jQuery);

                        call_save_template();
                        aplica_profile();
                        steps_modal();
                        scm_radio_bar();


                    }
                    ,
                    error: function (errorThrown) {
                        console.log(errorThrown);
                    }
                });

            });


            // aplica profil template@modal(edit/new)
            jQuery('#aplica_profil_job').click(function () {

                jQuery('#overwLoad').show();
                var format_obj = get_template_data();
                var template = jQuery('#acf-field_5d4bfc2e43023-field_5d4bfc6343024').val();

                var profile = get_temp_profile();

                jQuery.ajax({
                    type: "post",
                    url: "<?=  admin_url('admin-ajax.php'); ?>",
                    data: {
                        'action': 'job_profile_apply_partial_2',
                        'companie': <?= $employer_id; ?>,
                        'template': template,
                        'template_temp': format_obj,
                        'tip': 'job_profile_combo',
                        'profil_job': profile.job_profil,
                        'profil_companie': profile.companie_profil
                    },

                    success: function (data) {

                        jQuery('#rezultatModal').html(data);
                        jQuery('#overwLoad').hide();
                        jQuery('#formModal').modal('show');

                        (function ($) {
                            acf.do_action('append', $('#acf-form-job-profile'));
                        })(jQuery);

                        (function ($) {
                            $(document).ready(function () {
                                acf.unload.active = false;
                            });
                        })(jQuery);

                        call_save_template();
                        aplica_profile();
                        steps_modal();
                        scm_radio_bar();


                    }
                    ,
                    error: function (errorThrown) {
                        console.log(errorThrown);
                    }
                });

            });
        }

        (function ($) {
            acf.do_action('append', $('#rezultatModal-id'));
        })(jQuery);

        // call modal adauga template
        call_save_template();
        aplica_profile();

        // prevent submit
        (function ($) {
            $(document).ready(function () {
                acf.unload.active = false;
            });
        })(jQuery);


        // call modal edit template anunt
        jQuery('#aplica_template_anunt').click(function () {
            jQuery('#overwLoad').show();

            var template = jQuery('#acf-field_5d81c7650ed46-field_5d81c7a70ed47').val();

            jQuery.ajax({
                type: "post",
                url: "<?=  admin_url( 'admin-ajax.php' ); ?>",
                data: {
                    'action': 'job_profile_apply_2',
                    'companie': <?= $employer_id; ?>,
                    'template': template,
                    'tip': 'anunt_template'
                },

                success: function (data) {

                    //   console.log(data);

                    jQuery('#rezultatModal').html(data);
                    jQuery('#overwLoad').hide();
                    jQuery('#formModal').modal('show');

                    (function ($) {
                        acf.do_action('append', $('#acf-form-job-profile'));
                    })(jQuery);

                    (function ($) {
                        $(document).ready(function () {
                            acf.unload.active = false;
                        });
                    })(jQuery);

                    call_save_template();
                    aplica_profile();
                    steps_modal();
                    scm_radio_bar();

                    // disable template name
                    jQuery('#acf-field_5d496a147165c-field_5d496a2e7165d').attr('readonly', 'true');

                }
                ,
                error: function (errorThrown) {
                    console.log(errorThrown);
                }
            });

        });


    </script>

    <!--load mask-->
    <style>
        .loader {
            border: 16px solid #f3f3f3;
            border-radius: 50%;
            border-top: 16px solid orange;
            border-bottom: 16px solid darkorange;
            width: 120px;
            height: 120px;
            -webkit-animation: spin 2s linear infinite;
            animation: spin 2s linear infinite;
            left: 45%;
            top: 50%;
            transform: translate(-50%, -50%);
            position: absolute;
        }

        @-webkit-keyframes spin {
            0% {
                -webkit-transform: rotate(0deg);
            }
            100% {
                -webkit-transform: rotate(360deg);
            }
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }
            100% {
                transform: rotate(360deg);
            }
        }

        #overwLoad {
            width: 100%;
            height: 100%;
            position: fixed;
            z-index: 9999999999;
            left: 0;
            top: 0;
            right: 0;
            bottom: 0;
            background-color: rgba(8, 8, 8, 0.8);
            display: none;
        }
    </style>
    <div id="overwLoad">
        <div class="loader"></div>
    </div>
    <style>
        .acf-field[data-type=group] input[type=button] {
            width: 150px;
            border: 1px solid black;
            border-radius: 2px;
            text-align: center;
            line-height: 35px;
            font-size: 12px;
            text-transform: lowercase;
            display: block;
            color: black;
            text-decoration: none;
            transition: 0.3s;
            left: 50%;
            transform: translateX(-50%);
            float: left;
            position: relative;
            background-color: #ffff;
            padding: 0;
            margin-top: 20px;
            font-family: "Oswald", sans-serif;
            font-weight: 500;
        }

        #preModal .acf-field-button .acf-label {
            display: none;
        }
        .modal-dialog {
            max-width: 1000px;
        }

        #formModal {
            overflow-y: scroll;
        }

        .acf-field-image-aspect-ratio-crop[data-name=imagine_profil_companie] {
            position: absolute !important;
            top: 156px !important;
            right: 0;
        }

        .acf-image-uploader-aspect-ratio-crop .image-wrap img {
            max-width: 460px;
            width: auto;
            height: auto;
            display: block;
            min-width: 30px;
            min-height: 30px;
            background: #f1f1f1;
            margin: 0;
            padding: 0;
        }

        .acf-field-image-aspect-ratio-crop[data-name=imagine_cover] {
            position: absolute !important;
            top: 457px !important;
            right: 0;
            border-left: none !important;
        }

        .acf-field-image-aspect-ratio-crop[data-name=imagine_profil_companie] {
            position: absolute !important;
            top: 315px !important;
            right: 0;
        }

        .acf-field-5d789cadfcab8 li {
            margin-right: 0px !important;
            clear: none;
        }

        .acf-field-5d789cadfcab8 label {
            font-size: 0;
        }

        .acf-field-5d789cadfcab8 input[type=radio] {
            font-size: 20px;
        }

        .acf-field-5d789cadfcab8 input[type=radio] {
            border: 1px solid #dbe3e0;
            padding: 0.5em;
            -webkit-appearance: none;
        }

        .acf-field-5d789cadfcab8 input[type=radio]:checked {
            background: green;
            background-size: 9px 9px;
        }

        .acf-field-5d789cadfcab8 input[type=radio]:focus {
            outline-color: transparent;
        }

        .acf-field-5d789cadfcab8 .domeniu_wrap {
            padding-left: 20px !important;
        }

        .acf-field-5d789cadfcab8 .subdomeniu_wrap {
            padding-left: 40px !important;
        }

        .acf-field-5d789cadfcab8 .domeniu_wrap .acf-label {
            margin: 0 0 0px;
        }

        .acf-field-5d789cadfcab8 .subdomeniu_wrap .acf-label {
            margin: 0 0 0px;
        }

        .acf-field-5d789cadfcab8 .subdomeniu_wrap label {
            margin: 0 0 0px;
        }

        .acf-field-5d789cadfcab8 .domeniu_wrap label {
            margin: 0 0 0px;
        }

    </style>

    <?php
}