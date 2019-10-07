<script src="/wp-content/plugins/acf-extended/assets/acf-extended.js"></script>
<link href="/wp-content/plugins/acf-extended/assets/acf-extended.css">
<?php
acf_enqueue_uploader();
acf_form_head();

$id = get_the_ID();

global $jobsearch_plugin_options, $Jobsearch_User_Dashboard_Settings;

$user_id  = get_current_user_id();
$user_obj = get_user_by( 'ID', $user_id );

$candidate_id = jobsearch_get_user_candidate_id( $user_id );

if ( $candidate_id > 0 ) {

	?>
    <a href="" class="previewJob" target="_blank" style="margin-top: -63px">Adauga text</a>
    <div class="jobEdit">
        <div class="jobsearch-employer-box-sectios">
            <div class="row">
                <div class="col-12 col-md-3">
					<?php

					$templates = get_post_meta( $candidate_id, "candidat_texte_predefinite", true );
					//		print_r( $templates );
					$templates = unserialize(base64_decode($templates));
					//	print_r( $templates );

					?>

                    <ul class="lista-template lista-template-custom">
                        <h3 class="title-template-anunt">Alege text</h3>
						<?php
						$count = 0;
						foreach ( $templates as $key => $template ) {
							$activ      = ( $count == 0 ) ? 'activlink' : '';
                            $fullTemplatedescriere = $template['descriere_template'];
                            $max_length = 42;

							$id_template = wc_strtolower( str_replace( ' ', '', $key ) );

							if ( strlen( $template['descriere_template'] ) > $max_length ) {
								$offset                         = ( $max_length - 3 ) - strlen( $template['descriere_template'] );
								$template['descriere_template'] = substr( $template['descriere_template'], 0, strrpos( $template['descriere_template'], ' ', $offset ) ) . '...';
							}
							$max_lengthTitle = 35;
							if (strlen($key) > $max_lengthTitle) {
								$offset = ($max_lengthTitle - 3) - strlen($key);
								$key = substr($key, 0, strrpos($key, ' ', $offset)) . '...';
							}


							echo "<li class='template-name'><strong><a href='javascript:void(0);' id='link-template-$id_template' class='template-on  $activ'  data-template='$id_template'>" .
                                $key . "</a></strong><br />" . "<div class='text-description_template'>" . $template['descriere_template'] . "<div class='more_info'>" . $fullTemplatedescriere . "</div></div>"."</li>";
                            $count++;
						}
						?>

                    </ul>

                </div>

                <div class="col-12 col-md-9">
					<?php
					$count = 0;
					foreach ( $templates as $key => $template ) {
						//   print_r($template);

						$id_template = wc_strtolower( str_replace( ' ', '', $key ) );
						//	var_dump( $template );
						?>

                        <div class="template-wrap <?php echo ( $count == 0 ) ? 'activ' : ''; ?>"
                             id="template-<?= $id_template; ?>">



                            <!--    aici incepe template candidat-->

                            <div id="profil-candidat">

                                <div class="container-wrapper">
                                    <h2 class="job-title"><span>Ai selectat: <?= $key; ?></span>  <?php
										echo "<a href='javascript:void(0)' class='edit-template' data-template='$key' data-title='" . $template['nume'] . "'>editeaza</a>";
										?></h2>
                                </div>
                                <div class="zonaCandidat" style=" ">

                                    <?= nl2br($template['text']); ?>

                                </div>
                            </div>


                            <!--  end candiat-->


                        </div>

						<?php
						$count ++;
					} ?>

                </div>
            </div>


        </div>
    </div>

    <div id="rezultatModal">

    </div>
    <script>

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


        function get_template_data() {
            var obj_values = jQuery('#acf-form-candidat-profile').serializeObject();

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


            var format_obj = {};
            var id = 0;
            for (var key in object_new_label) {


                if (object_new_label.hasOwnProperty(key)) {

                    if (format_obj.hasOwnProperty(object_new_label[key]) && obj_values[key] !== '') {

                        if (typeof(format_obj[object_new_label[key]]) === 'object') {

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

            return format_obj;
        }

        function call_save_template() {



            jQuery('#salveaza_profil_job').click(function (event) {
               var numeTextPredefinit =jQuery('#acf-field_5d532a93d3aa9-field_5d532728f4626');


              
                   jQuery('#overwLoad').show();
                   var format_obj = get_template_data();

                   console.log(format_obj);

                   jQuery.ajax({
                       type: "post",
                       url: "<?=  admin_url( 'admin-ajax.php' ); ?>",
                       data: {
                           'action': 'job_template_save',
                           'companie': <?= $candidate_id; ?>,
                           'template': {
                               'nume': format_obj.nume_template,
                               'date': format_obj,
                               'tip': 'candidat_texte',
                               'new' : false
                           },
                           'new': false
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

            jQuery('#salveaza_ca_job_profil').click(function () {
                jQuery('#wrap-salveaza-ca').slideToggle();
            });


            jQuery('#ok_save_as').click(function () {

                var format_obj = get_template_data();

                format_obj.nume_template = format_obj.nume_template_as;
                format_obj.descriere_template = format_obj.descriere_template_as;

                jQuery.ajax({
                    type: "post",
                    url: "<?=  admin_url( 'admin-ajax.php' ); ?>",
                    data: {
                        'action': 'job_template_save',
                        'companie': <?= $candidate_id; ?>,
                        'template': {
                            'nume': format_obj.nume_template_as,
                            'date': format_obj,
                            'tip': 'candidat_texte',
                            'new': true
                        },
                        'new': true
                    },
                    success: function (data) {
                       //    console.log(data);
                        location.reload();
                    }
                    ,
                    error: function (errorThrown) {
                        console.log(errorThrown);
                    }
                });


            });


            jQuery('#delete_template').click(function () {
                jQuery('#overwLoad').show();
                if (confirm("Sigur doresti sa stergi acest template?")) {
                    delete_profile();
                }
                else {
                    return false;
                }

                function delete_profile() {
                    var template = jQuery('#acf-field_5d532a93d3aa9-field_5d532728f4626').val();

                    //  console.log(template);
                    jQuery.ajax({
                        type: "post",
                        url: "<?=  admin_url( 'admin-ajax.php' ); ?>",
                        data: {
                            'action': 'job_profile_delete',
                            'companie': <?= $candidate_id; ?>,
                            'template': template,
                            'tip': 'candidat_texte'
                        },
                        success: function (data) {
                            //   console.log(data);
                            location.reload();
                            (function ($) {
                                $(document).ready(function () {
                                    acf.unload.active = false;
                                });
                            })(jQuery);
                        }
                        ,
                        error: function (errorThrown) {
                            console.log(errorThrown);
                        }
                    });
                }

            });

        }


        // schimba tabul
        jQuery('.template-on').click(function () {
            jQuery('.template-on').removeClass('activlink');
            jQuery(this).addClass('activlink');
            jQuery('.job-title span').html('Ai selectat: ' + jQuery(this).text());

            var template = '#template-' + jQuery(this).attr('data-template');
            jQuery('.template-wrap').removeClass('activ');
            jQuery(template).addClass('activ');


        });

        jQuery('.edit-template').click(function () {
            jQuery('#acf-field_5d532a93d3aa9-field_5d532728f4626').attr('readonly','true');
            jQuery('#overwLoad').show();
            var template = jQuery(this).attr('data-template');

            jQuery.ajax({
                type: "post",
                url: "<?=  admin_url( 'admin-ajax.php' ); ?>",
                data: {
                    'action': 'job_profile_apply',
                    'companie': <?= $candidate_id; ?>,
                    'template': template,
                    'tip': 'candidat_texte'
                },

                success: function (data) {

                    jQuery('#rezultatModal').html(data);
                    jQuery('#overwLoad').hide();
                    jQuery('#formModal').modal('show');

                    (function ($) {
                        acf.do_action('append', $('#acf-form-candidat-profile'));
                    })(jQuery);

                    (function ($) {
                        $(document).ready(function () {
                            acf.unload.active = false;
                        });
                    })(jQuery);
                    jQuery('#acf-field_5d532a93d3aa9-field_5d532728f4626').attr('readonly','true');
                    call_save_template();
                }
                ,
                error: function (errorThrown) {
                    console.log(errorThrown);
                }
            });

        });

        (function ($) {
            acf.do_action('append', $('#rezultatModal-id'));
        })(jQuery);

        jQuery('.previewJob').attr('href', '').html('Adauga text').click(function (event) {
            jQuery('#overwLoad').show();
            event.preventDefault();
            jQuery.ajax({
                type: "post",
                url: "<?=  admin_url( 'admin-ajax.php' ); ?>",
                data: {
                    'action': 'add_new_profile',
                    'companie': <?= $candidate_id; ?>,
                    'tip': 'candidat_texte'
                },

                success: function (data) {

                    //  console.log(data);
                    jQuery('#rezultatModal').html(data);

                    jQuery('#overwLoad').hide();

                    jQuery('#formModal').modal('show');

                    (function ($) {
                        acf.do_action('append', $('#acf-form-candidat-profile'));
                    })(jQuery);

                    (function ($) {
                        $(document).ready(function () {
                            acf.unload.active = false;
                        });
                    })(jQuery);
                    jQuery('.acf-field-5d532796f4629').hide();
                    jQuery('.acf-field-5d5327b2f462a').hide();
                    jQuery('.acf-field-5d53276ff4628').css({
                        'left': '50%',
                        'transform': 'translateX(-50%)',
                        'border-left': 'none'
                    });

                    call_save_template();
                }
                ,
                error: function (errorThrown) {
                    console.log(errorThrown);
                }
            });

        });

    </script>

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

        .acf-field-image-aspect-ratio-crop[data-name=imagine_cover] {
            position: relative;
            top: initial;
            right: 0;
            border-left: none !important;
        }
    </style>
    <div id="overwLoad">
        <div class="loader"></div>
    </div>

    <style>
        .modal-dialog {
            max-width: 1000px;
        }

        #formModal {
            overflow-y: scroll;
        }
        .wrap-date{
            top: 50%;
            left:50%;
            position: absolute;
            transform: translate(-50%,-50%);
        }
    </style>

	<?php
}