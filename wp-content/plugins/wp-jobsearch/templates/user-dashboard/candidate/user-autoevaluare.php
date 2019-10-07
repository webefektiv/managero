<script src="/wp-content/plugins/acf-extended/assets/acf-extended.js"></script>
<link href="/wp-content/plugins/acf-extended/assets/acf-extended.css">
<?php
acf_enqueue_uploader();
acf_form_head();

$id = get_the_ID();

global $jobsearch_plugin_options, $Jobsearch_User_Dashboard_Settings;

$user_id = get_current_user_id();
$user_obj = get_user_by('ID', $user_id);

$candidate_id = jobsearch_get_user_candidate_id($user_id);

if ($candidate_id > 0) {

   // print_r($candidate_id);
    $lista_curenta = get_post_meta($candidate_id, "profil_scm", true);
//   print_r($lista_curenta);
    ?>
    <!--    <a href="" class="previewJob" target="_blank" style="margin-top: -63px">Adauga text</a>-->
    <div class="jobEdit">

        <div class="jobsearch-employer-box-sectios">

            <?php
            $settings1 = array(

                /* (string) Unique identifier for the form. Defaults to 'acf-form' */
                'id' => 'acf-form-evaluare',

                /* (int|string) The post ID to load data from and save data to. Defaults to the current post ID.
                Can also be set to 'new_post' to create a new post on submit */
                'post_id' => $candidate_id,

                /* (array) An array of post data used to create a post. See wp_insert_post for available parameters.
                The above 'post_id' setting must contain a value of 'new_post' */
                'new_post' => false,

                /* (array) An array of field group IDs/keys to override the fields displayed in this form */
                'field_groups' => [1678],

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
                'html_submit_button' => '<input type="submit" class="acf-button button button-primary button-large" value="%s" />',

                /* (string) HTML used to render the submit button loading spinner. Added in v5.5.10 */
                'html_submit_spinner' => '<span class="acf-spinner"></span>',

                /* (boolean) Whether or not to sanitize all $_POST data with the wp_kses_post() function. Defaults to true. Added in v5.6.5 */
                'kses' => true,

                'load_fields' => null,

                'fields_type' => 'profil_scm'

            );

            acf_form($settings1);
            ?>

        </div>
    </div>


    <div id="overwLoad">
        <div class="loader"></div>
    </div>


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
            left:45%;
            top:50%;
            transform: translate(-50%,-50%);
            position: absolute;
        }

        @-webkit-keyframes spin {
            0% { -webkit-transform: rotate(0deg); }
            100% { -webkit-transform: rotate(360deg); }
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        #overwLoad{
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
        .acf-field-image-aspect-ratio-crop[data-name=imagine_cover] {
            position: relative !important;
            top: initial !important;
            right: initial !important;
            border-left: initial !important;
        }

        li {
            margin-right: 0px !important;
            clear: none;
        }
        label {
            font-size: 0;
        }
        input[type=radio] {
            font-size: 20px;
        }
        input[type=radio] {
            border: 1px solid #dbe3e0;
            padding: 0.5em;
            -webkit-appearance: none;
        }
        input[type=radio]:checked {
            background: #0172b6;
            background-size: 9px 9px;
        }
        input[type=radio]:focus {
            outline-color: transparent;
        }
        .domeniu_wrap {
            padding-left: 20px !important;
        }
        .subdomeniu_wrap {
            padding-left: 40px !important;
        }
        .domeniu_wrap .acf-label {
            margin: 0 0 0px;
        }
        .subdomeniu_wrap .acf-label {
            margin: 0 0 0px;
        }
        .subdomeniu_wrap label {
            margin: 0 0 0px;
        }
        .domeniu_wrap label {
            margin: 0 0 0px;
        }
        .acf-fields > .acf-field {
            position: relative;
            margin: 0;
            padding: 6px 12px;
            border-top: #EEEEEE solid 1px;
        }
        .acf-field-group .acf-field .acf-label label {
            display: block;
            margin: 0 0 0px;
            padding: 0;
            font-size: 12px;
            text-transform: none;
            font-family: "Open Sans", sans-serif;
        }
        input#salveaza_profil_job {
            margin: auto;
            display: block;
        }
        .acf-form-submit{
            display: none;
        }
        .acf-field-5d7655ae923d3 .acf-label{
            display: none;
        }
        .acf-field-5d789cadfcab8 .subdomeniu_wrap {
            padding-left: 0px !important;

        }
    </style>

    <script>

        $( document ).ready(function() {
            jQuery('.acf-field-5d76159332e59').append("<div class='head-scm'><div class='left-head-scm left-head-scm-1'>Domeniu SCM</div><div class='right-head-scm right-head-scm-1'><p>1</p><p>2</p><p>3</p><p>4</p><p>5</p></div></div>")

        });


        $('.acf-field-radio').each(function () {
            var nume = $(this).attr('data-name');
            var value = $('input[type=radio]:checked',this).val();

            $('input[type=radio]', this).each(function (index) {
                if ((index + 1) > value) {
                    return false;
                }
                $(this).css('background-color', '#0172b6');
            });

        });


        (function ($, undefined) {
            '$:nomunge'; // Used by YUI compressor.

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

        var scmLoad = {};

        function get_template_data_2() {
            $('.acf-field-radio').each(function () {
               var nume = $(this).attr('data-name');
               var value = $('input[type=radio]:checked',this).val();
                scmLoad[nume] = value;
            });
            return scmLoad;
        }



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
                    $(this).css('background-color', '#0172b6');
                });

            } else {
                $(this).css('background-color', '#fff');
            }
        });

        // check selectie
        function checkInput(id) {
            return jQuery('#' + id).is(':checked');
        }



        $('#salveaza_profil_job').click(function () {

            jQuery('#overwLoad').show();

            var format_obj = get_template_data_2();

            jQuery.ajax({
                type: "post",
                url: "<?=  admin_url( 'admin-ajax.php' ); ?>",
                data: {
                    'action': 'profil_scm_save',
                    'candidat': <?= $candidate_id ?>,
                    'dataload':  format_obj,
                },
                success: function (data) {
                  //   console.log(data);
                     location.reload();
                }
                ,
                error: function (errorThrown) {
                    console.log(errorThrown);
                }
            });

        });
		
		 (function ($) {
                        $(document).ready(function () {
                            acf.unload.active = false;
                        });
                    })(jQuery);
					
					
					
    </script>

    <?php
}