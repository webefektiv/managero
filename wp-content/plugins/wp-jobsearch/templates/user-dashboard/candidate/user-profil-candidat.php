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

    ?>
    <div class="jobEdit">
        <div class="jobsearch-employer-box-sectios">
            <div class="row">
                <div class="col-12 col-md-3">
                    <?php
                    $templates = get_post_meta($candidate_id, "candidat_profiles", true);
                    $templates = unserialize(base64_decode($templates));
                    ?>

                    <ul class="lista-template lista-template-custom">
                        <h3 class="title-template-anunt">Alege profil candidat</h3>
                        <?php
                        $count = 0;
                        foreach ($templates as $key => $template) {
                            $activ = ($count == 0) ? 'activlink' : '';
                            $max_length = 42;
                            $fullTemplatedescriere = $template['descriere_template'];
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
                                $key . "</a></strong><br />" . "<div class='text-description_template'>" . $template['descriere_template'] . "<div class='more_info'>" . $fullTemplatedescriere . "</div></div>" . "</li>";
                            $count++;
                        }
                        ?>
                    </ul>
                </div>

                <div class="col-12 col-md-9">
                    <?php
                    $count = 0;
                    foreach ($templates as $key => $template) {
                        //   print_r($template);

                        $id_template = wc_strtolower(str_replace(' ', '', $key));
                     //   var_dump( $template );

                        ?>

                        <div class="template-wrap <?php echo ($count == 0) ? 'activ' : ''; ?>"
                             id="template-<?= $id_template; ?>">

                            <!--    aici incepe template candidat-->

                            <div id="profil-candidat">

                                <div class="container-wrapper">


                                    <h2 class="job-title"><label style="text-align: center;"><?= $template['titlu_date'] . ' ' . $template['prenume'] . ' ' . $template['particula']; ?>
                                        <span><?= $template['nume']; ?></span> <?= '(' . $template['formula_adresare'] . ')' ?>
                                       <br>
                                            <span style="font-size: 12px;">(<?= $template['nickaname']; ?>)</span>
                                        </label> <?php echo "<a href='javascript:void(0)' class='edit-template' data-template='$key' data-title='" . $template['nume'] . "'>EDITEAZA</a>"; ?>
                                    </h2>
                                    <div class="zonaCandidat" id="informatiiCandidat">


<!--                                        <img src="--><?//= wp_get_attachment_url($template['imagine_cover']); ?><!--"/>-->


                                        <div class="row">
                                            <div class="col-12 col-md-3">
                                                <h2>Date personale</h2>
                                                <p>Varsta: <?= date('Y') - $template['anul_nasterii']; ?> ani</p>
                                                <p>Sex: <?= $template['sex']; ?></p>
                                            </div>
                                            <div class="col-12 col-md-3">
                                               <br>
                                                <p>Nickname: <?= $template['nickaname']; ?></p>
                                                <p>Resedinta: <?= $template['judet_candidat']; ?></p>
                                            </div>
                                            <div class="col-12 col-md-3 links-profiles">
                                                <strong>Link-uri</strong>:<br>
                                                <?php
                                                foreach ($template['link-uri'] as $titlu => $link) {
                                                    echo "<a href='$link' target='_blank'>$titlu</a><br/>";
                                                }
                                                ?>
                                            </div>
                                            <div class="col-12 col-md-3">
                                                <img src="<?= wp_get_attachment_url($template['imagine_profil']); ?>"
                                                     style="max-height: 80px"/>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="zonaCandidat" id="scrisoare-intentie">

                                    </div>


                                    <div class="zonaCandidat" id="educatie">
                                        <h2>Educatie</h2>
                                        <ul style="padding-left: 20px; list-style: square; margin-left: 0px;">
                                            <?php
                                            foreach ($template['educatie'] as $studiu) {
                                                echo '<li>' . $studiu . '</li>';
                                            }
                                            ?>
                                        </ul>
                                    </div>

                                    <div class="zonaCandidat" id="certificari">
                                        <h2>Certificari</h2>
                                        <?php
                                        if(is_array( $template['certificare'])):
                                        foreach ($template['certificare'] as $studiu) {
                                            echo $studiu . ', ';
                                        }
                                        else:
	                                        echo $template['certificare'] . '<br/>';
                                        endif;
                                        ?>
                                    </div>

                                    <div class="zonaCandidat" id="limbi">
                                        <h2>Limbi straine</h2>
                                        Engleza - <?= $template['limba_engleza']; ?><br>
                                        <?php

                                        if(is_array( $template['limba_nivel'])):
                                        foreach ($template['limba_nivel'] as $studiu) {
                                            echo $studiu . '<br/>';
                                        }
                                        else:
                                            echo $template['limba_nivel'] . '<br/>';
                                        endif;
                                        ?>
                                    </div>

                                    <div class="zonaCandidat" id="experienta">
                                        <h2>Experienta</h2>
                                        <ul>
                                            <?php
                                            foreach ($template['experinta_full'] as $label => $companie): ?>
                                                <li><span class="tcomapnie"><?= $label; ?></span> <br/>
                                                    <?= $companie['descriere_companie']; ?>
                                                    <ul class="posturi-companie">
                                                        <?php foreach ($companie['post'] as $post => $date): ?>
                                                            <li>
                                                                <span> <?= $date['data_la']; ?></span> -
                                                                <span><?= $date['pana_la']; ?></span><br>
                                                                <strong><span><?= $date['post']; ?></span>,
                                                                    <span><?= $date['ierarhie']; ?></span></strong>
                                                                <br/>
                                                                <span><?= $date['descriere_job']; ?></span><br>
                                                                <span><?= $date['alte_detalii']; ?></span><br>
                                                            </li>
                                                        <?php endforeach;; ?>
                                                    </ul>
                                                    <br>
                                                </li>
                                            <?php endforeach; ?>
                                        </ul>

                                    </div>

                                    <div class="zonaCandidat" id="certificari">
                                        <h2>Competente relevante</h2>
                                        <?= $template['relevant_skils']; ?>
                                    </div>

                                    <div class="zonaCandidat" id="cerinte">
                                        <h2>Salariu</h2>
                                        <b><?= ($template['salariu_minim_accepta'] != 0 || $template['salariu_minim_accepta'] != '') ? $template['salariu_minim_accepta'] . '&euro;' : 'nespecificat' ?></b>
                                        <br>
                                    </div>
                                    <div class="zonaCandidat" id="cerinte">
                                        <h2>Alte cerinte</h2>
	                                    <?= $template['alte_cerinte']; ?>
                                        <br>
                                    </div>

                                    <div class="zonaCandidat" id="brief">
                                        <h2>Note si comentarii libere</h2>
                                        <?= $template['note_comentarii']; ?>
                                    </div>


                                </div>
                            </div>


                            <!--  end candiat-->


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


        function get_experinta() {

            var experinta = {};

            jQuery('.data-companie').each(function (index) {

                var nume_companie = jQuery('input[type=text]', this).val();

                var temp = jQuery(this).siblings();

                var descriere_companie = jQuery('textarea', temp[0]).val();

                if (nume_companie !== '') {

                    var post_companie = {}

                    jQuery('.acf-fields', temp[1]).each(function (index2) {

                        var post = jQuery('.post-titlu input[type=text]', this).val();
                        var dela = jQuery('.date_dela input[type=text]', this).val();
                        var pana_la = jQuery('.pana-la input[type=text]', this).val();
                        var job_curent = jQuery('.job-curent input:checked', this).val();
                        var ierarhie = jQuery('.post-ierarhie select', this).val();
                        var departament = jQuery('.data-departament input[type=text]', this).val();
                        var descriere_job = jQuery('.descriere_job textarea', this).val();
                        var alte_detalii = jQuery('.alte_detalii textarea', this).val();

                        if (post !== '') {
                            post_companie[post] = {
                                'post': post,
                                'data_la': dela,
                                'pana_la': pana_la,
                                'job_curent': job_curent,
                                'ierarhie': ierarhie,
                                'departament': departament,
                                'descriere_job': descriere_job,
                                'alte_detalii': alte_detalii
                            };
                        }
                    });

                    experinta[nume_companie] = {
                        'nume_companie': nume_companie,
                        'descriere_companie': descriere_companie,
                        'post': post_companie
                    };
                }
            });

            return experinta;
        }

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

            format_obj['experinta_full'] = get_experinta();

            format_obj['link-uri'] = {};

            if (typeof(format_obj['titlu']) === 'string') {
                format_obj['link-uri'][format_obj['titlu']] = format_obj['link'];
            } else {
                for (var key in format_obj['titlu']) {
                    format_obj['link-uri'][format_obj['titlu'][key]] = format_obj['link'][key];
                }
            }

            return format_obj;
        }

        function call_save_template() {

            jQuery('#salveaza_profil_job').click(function (event) {
             var numeProfilCandidat = jQuery('#acf-field_5d36f3aad0988-field_5d36f440d0989');

              
                    jQuery('#overwLoad').show();
                    var format_obj = get_template_data();

                    console.log(format_obj);

                    jQuery.ajax({
                        type: "post",
                        url: "<?=  admin_url('admin-ajax.php'); ?>",
                        data: {
                            'action': 'job_template_save',
                            'companie': <?= $candidate_id; ?>,
                            'template': {
                                'nume': format_obj.nume_template,
                                'date': format_obj,
                                'tip': 'candidat_profile'
                            },
                            'new': false
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
                        'companie': <?= $candidate_id; ?>,
                        'template': {
                            'nume': format_obj.nume_template_as,
                            'date': format_obj,
                            'tip': 'candidat_profile',
                            'new': true
                        }
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
                    var template = jQuery('#acf-field_5d36f3aad0988-field_5d36f440d0989').val();
                    //  console.log(template);
                    jQuery.ajax({
                        type: "post",
                        url: "<?=  admin_url('admin-ajax.php'); ?>",
                        data: {
                            'action': 'job_profile_delete',
                            'companie': <?= $candidate_id; ?>,
                            'template': template,
                            'tip': 'candidat_profile'
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
                    'companie': <?= $candidate_id; ?>,
                    'template': template,
                    'tip': 'candidat_profile'
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

                    call_save_template();

                    jQuery('#acf-field_5d36f3aad0988-field_5d36f440d0989').attr('readonly', 'true');
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

        // adauga un profil de job
        jQuery('.previewJob').attr('href', '').html('Adauga template').click(function (event) {
            jQuery('#overwLoad').show();
            event.preventDefault();
            jQuery.ajax({
                type: "post",
                url: "<?=  admin_url('admin-ajax.php'); ?>",
                data: {
                    'action': 'add_new_profile',
                    'companie': <?= $candidate_id; ?>,
                    'tip': 'candidat_profile'
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

                    $('.acf-field[data-name="salveaza_ca"]').hide();
                    $('.acf-field[data-name="delete_template"]').hide();

                    $('.acf-field[data-name="salveaza"]').css({
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

        .acf-image-uploader-aspect-ratio-crop  .acf-icon.-pencil {
            display:none;
        }

        .label .setting{
            display: none;
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

        .wrap-date {
            top: 50%;
            left: 50%;
            position: absolute;
            transform: translate(-50%, -50%);
        }
    </style>

    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.8/css/select2.min.css" rel="stylesheet"/>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.8/js/select2.min.js"></script>

    <script>
        jQuery("#acf-field_5d03586cc72e3-field_5d02a41eb02bd").select2({});
    </script>


    <?php
}