<script src="/wp-content/plugins/acf-extended/assets/acf-extended.js"></script>
<link href="/wp-content/plugins/acf-extended/assets/acf-extended.css">
<?php
acf_enqueue_uploader();
acf_form_head();

$id = get_the_ID();

global $jobsearch_plugin_options, $Jobsearch_User_Dashboard_Settings;

$current_user = wp_get_current_user();
$user_id = get_current_user_id();
$user_obj = get_user_by('ID', $user_id);

$employer_id = jobsearch_get_user_employer_id($user_id);

if ($employer_id > 0) {

    ?>
    <div class="jobEdit">
        <div class="jobsearch-employer-box-sectios">
            <div class="row">
                <div class="col-12 col-md-3">
                    <?php

                    $templates = get_post_meta($employer_id, "companie_job_profiles", true);
                    $templates = unserialize(base64_decode($templates));

                    ?>

                    <ul class="lista-template lista-template-custom">
                        <h3 class="title-template-anunt">Alege template job</h3>
                        <?php
                        $count = 0;
                        foreach ($templates as $key => $template) {
                            $activ = ($count == 0) ? 'activlink' : '';
                            $fullTemplatedescriere = $template['descriere_template'];
                            $max_length = 42;

                            if (strlen($template['descriere_template']) > $max_length) {
                                $offset = ($max_length - 3) - strlen($template['descriere_template']);
                                $template['descriere_template'] = substr($template['descriere_template'], 0, strrpos($template['descriere_template'], ' ', $offset)) . '...';
                            }

                            $max_lengthTitle = 35;
                            if (strlen($key) > $max_lengthTitle) {
                                $offset = ($max_lengthTitle - 3) - strlen($key);
                                $key = substr($key, 0, strrpos($key, ' ', $offset)) . '...';
                            }



                            $id_template = wc_strtolower(str_replace(' ', '', $key));

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
                    foreach ($templates as $key => $template) {
                        $id_template = wc_strtolower(str_replace(' ', '', $key));
                        ?>

                        <div class="template-wrap <?php echo ($count == 0) ? 'activ' : ''; ?>"
                             id="template-<?= $id_template; ?>">

                            <div id="sectiune-job">
                                <h2 class="job-title">
                                    <span>Ai selectat: <?= $template['nume_template'] ?></span><?php echo "<a href='javascript:void(0)' class='edit-template' data-template='$key' data-title='" . $template['nume'] . "'>editeaza</a>" ?>
                                </h2>

                                <!--                                <img class="imgJob" src="-->
                                <? //= wp_get_attachment_url( $template['imagine_cover']); ?><!--" />-->

                                <div class="row jobDetails">
                                    <div class="col-md-12">
                                        <h4>Nume job</h4>
                                        <?= $template['post_job']; ?>
                                    </div>
                                </div>

                                <div class="row jobDetails">
                                    <div class="col-md-12">
                                        <h4>Descriere</h4>
                                        <?= nl2br($template['descrierea_sintetica_a_jobului']); ?>
                                    </div>
                                </div>

                                <div class="row jobDetails">
                                    <div class="col-md-4">
                                        <h4>Pozitie ierarhica</h4>
                                        <?= $template['nivelul_ierarhic']; ?>
                                    </div>
                                    <div class="col-md-4">
                                        <h4>Cui este subordonat</h4>
                                        <?= $template['cui_este_subordonat']; ?>
                                    </div>
                                    <div class="col-md-4">
                                        <h4>Numar de subordonati</h4>
                                        <?= $template['subordonati_total']; ?>
                                    </div>
                                </div>


                            </div>

                            <div id="sectiune-oferta">
                                <div class="row jobDetails">
                                    <div class="col-sm-12">
                                        <h4>Cerinte job</h4>
                                        <?= $template['cerinte_job']; ?>
                                    </div>
                                </div>
                            </div>

                            <div id="sectiune-oferta">
                                <div class="row jobDetails">
                                    <div class="col-sm-12">
                                        <h4>Oferta</h4>
                                        <?= nl2br($template['descriere_oferta']); ?>
                                    </div>
                                </div>

                                <div class="row jobDetails">
                                    <div class="col-sm-12">
                                        <h4>Salariu</h4>
                                        <?= $template['salariu_oferit_job']; ?>€
                                    </div>
                                </div>

                                <div class="row jobDetails">
                                    <div class="col-sm-12">
                                        <h4>Alte detalii</h4>
                                        <?= nl2br($template['comentarii_job']); ?>
                                    </div>
                                </div>
                            </div>

                            <!--                            <div id="sectiune-oferta">-->
                            <!--                                <div class="row jobDetails">-->
                            <!--                                    <div class="col-sm-12">-->
                            <!--                                        <h4>Fisiere atasate</h4>-->
                            <!--                                        <ul class="listaFisiere">-->
                            <!--											--><?php
                            //											foreach ( $template['fisiere'] as $nume => $fisier ) {
                            //
                            //												echo "<li><a href='" . wp_get_attachment_url( $fisier ) . "'>" . $nume . "</a></li>";
                            //											}
                            //											?>
                            <!--                                        </ul>-->
                            <!--                                    </div>-->
                            <!--                                </div>-->
                            <!--                            </div>-->

                            <!--                            <div id="sectiune-oferta">-->
                            <!--                                <div class="row jobDetails">-->
                            <!--                                    <div class="col-sm-12">-->
                            <!--                                        <h4>Galeria media</h4>-->
                            <!--                                        <div class="galerieJob">-->
                            <!--											--><?php //foreach ( $template['galerie'] as $media ) { ?>
                            <!--                                                <div class="imgGalWrap">-->
                            <!--                                                    <a class="fancybox" data-fancybox-group="button" rel="gallery1"-->
                            <!--                                                       href="-->
                            <? //= wp_get_attachment_url( $media ); ?><!--">-->
                            <!--                                                        <img class="wrap-img"-->
                            <!--                                                             src="-->
                            <? //=  wp_get_attachment_image_src( $media )[0]; ?><!--" alt=""/>-->
                            <!--                                                    </a>-->
                            <!--                                                </div>-->
                            <!--											--><?php //} ?>
                            <!--                                        </div>-->
                            <!--                                    </div>-->
                            <!--                                </div>-->
                            <!--                            </div>-->


                        </div>

                        <?php
                        $count++;
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
            var obj_values = jQuery('#acf-form-job-profile').serializeObject();
            console.log(obj_values);
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

            object_new_label['acf[field_5d4169c4c0cac][]'] = 'galerie';
            object_new_label['acf[field_5d4168184ce35][]'] = 'domeniu';


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

            format_obj['fisiere'] = {};

            if (typeof(format_obj['denumire']) === 'string') {
                format_obj['fisiere'][format_obj['denumire']] = format_obj['fisier'];
            } else {

                for (var key in format_obj['denumire']) {
                    format_obj['fisiere'][format_obj['denumire'][key]] = format_obj['fisier'][key];
                }
            }

            return format_obj;
        }

        function call_save_template() {

            jQuery('#salveaza_profil_job').click(function (event) {
                var numeJobTemplate= jQuery('#acf-field_5d416a2cc0caf-field_5d416acac61e8');


                    jQuery('#overwLoad').show();
                    var format_obj = get_template_data();

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
                            jQuery('.acf-field[data-name=fisiere_atasate], .acf-field[data-name=galerie], .acf-field[data-name=imagine_cover]').hide();
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

                jQuery('#overwLoad').show();
                var format_obj = get_template_data();

                format_obj.nume_template = format_obj.nume_template_as;
                format_obj.descriere_template = format_obj.descriere_template_as;

                jQuery.ajax({
                    type: "post",
                    url: "<?=  admin_url('admin-ajax.php'); ?>",
                    data: {
                        'action': 'job_template_save',
                        'companie': <?= $employer_id; ?>,
                        'template': {
                            'nume': format_obj.nume_template_as,
                            'date': format_obj,
                            'tip': 'job_profile',
                            'new': true
                        },
                        'new': true
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


            jQuery('#delete_template').click(function () {
                jQuery('#overwLoad').show();
                if (confirm("Sigur doresti sa stergi acest template?")) {
                    delete_profile();
                }
                else {
                    return false;
                }

                function delete_profile() {
                    var template = jQuery('#acf-field_5d416a2cc0caf-field_5d416acac61e8').val();
                    //  console.log(template);
                    jQuery.ajax({
                        type: "post",
                        url: "<?=  admin_url('admin-ajax.php'); ?>",
                        data: {
                            'action': 'job_profile_delete',
                            'companie': <?= $employer_id; ?>,
                            'template': template,
                            'tip': 'job_profile'
                        },
                        success: function (data) {
                            //      console.log(data);
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
            jQuery('#overwLoad').show();
            var template = jQuery(this).attr('data-template');

            jQuery.ajax({
                type: "post",
                url: "<?=  admin_url('admin-ajax.php'); ?>",
                data: {
                    'action': 'job_profile_apply',
                    'companie': <?= $employer_id; ?>,
                    'template': template,
                    'tip': 'job_profile'
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

                    jQuery('.acf-field[data-name=fisiere_atasate], .acf-field[data-name=galerie], .acf-field[data-name=imagine_cover]').hide();

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

        jQuery('.adaugaJob').attr('href', '').html('Adauga template').click(function (event) {
            jQuery('#overwLoad').show();
            event.preventDefault();
            jQuery.ajax({
                type: "post",
                url: "<?=  admin_url('admin-ajax.php'); ?>",
                data: {
                    'action': 'add_new_profile',
                    'companie': <?= $employer_id; ?>,
                    'tip': 'job_profile'
                },

                success: function (data) {

                    //  console.log(data);
                    jQuery('#rezultatModal').html(data);

                    jQuery('#overwLoad').hide();

                    jQuery('#formModal').modal('show');

                    $('.acf-field[data-name="delete_template"]').hide();
                    $('.acf-field[data-name="salveaza_ca"]').hide();

                    $('.acf-field[data-name="salveaza"]').css({
                        'left': '50%',
                        'transform': 'translateX(-50%)',
                        'border-left': 'none'
                    });

                    jQuery('.acf-field[data-name=fisiere_atasate], .acf-field[data-name=galerie], .acf-field[data-name=imagine_cover]').hide();


                    (function ($) {
                        acf.do_action('append', $('#acf-form-job-profile'));
                    })(jQuery);

                    (function ($) {
                        $(document).ready(function () {
                            acf.unload.active = false;
                        });
                    })(jQuery);

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

        .acf-field[data-name=fisiere_atasate] {
            display: none !important;
        }

        .acf-field[data-name=galerie] {
            display: none !important;
        }

        .acf-field[data-name=imagine_cover] {
            display: none !important;
        }

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
            position: relative !important;
            top: initial !important;
            right: initial !important;
            border-left: initial !important;
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
    </style>


    <?php
}