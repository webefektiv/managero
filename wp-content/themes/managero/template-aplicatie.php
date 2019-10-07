<?php
/**
 * Template Name: Aplicatie
 */

get_header();
global $jobsearch_plugin_options;

wp_enqueue_style('jobsearch-datetimepicker-style');
wp_enqueue_script('jobsearch-datetimepicker-script');
wp_enqueue_script('jquery-ui');

?>


<?php

$rand_id = rand(123400, 9999999);
$job_id = $_GET['job_id'];

$companie_id = get_post_meta($job_id, 'jobsearch_field_job_posted_by', true);

//die();
$user_id = get_current_user_id();
$candidate_id = jobsearch_get_user_candidate_id($user_id);

$btn_text = "Aplica";

$classes = '';
$btn_after_label = '';

$popup_args = array(
    'p_job_id' => $job_id,
    'p_rand_id' => $rand_id,
    'p_btn_text' => $btn_text,
    'p_classes' => $classes,
    'p_classes_str' => $classes_str,
    'p_btn_after_label' => $btn_after_label,
    'max_cvs_allow' => $max_cvs_allow,
);

//$p_job_id = $job_id = $job_id;
//$p_rand_id = $rand_id;
//$p_btn_text = $btn_text;
//$p_classes = $classes;
//$p_classes_str = $classes_str;
//$p_btn_after_label = $btn_after_label;
//$max_cvs_allow = $max_cvs_allow;
//


extract(shortcode_atts(array(
    'p_job_id' => '',
    'p_rand_id' => '',
    'p_btn_text' => '',
    'p_classes' => '',
    'p_classes_str' => '',
    'p_btn_after_label' => '',
    'max_cvs_allow' => '',
), $popup_args));

$rand_id = rand(123400, 9999999);
$classes = 'jobsearch-applyjob-btn';

$job_extrnal_apply_internal_switch = 'internal';

$classes_str = 'jobsearch-open-signin-tab jobsearch-wredirct-url';
$multi_cvs = false;
if (is_user_logged_in()) {

    if (jobsearch_user_is_candidate()) {
        if ($multiple_cv_files_allow == 'on') {
            $multi_cvs = true;
        }
        $classes_str = 'jobsearch-apply-btn';
    } else {
        $classes_str = 'jobsearch-other-role-btn jobsearch-applyjob-msg-popup-btn';
    }
}

if (is_user_logged_in()) {
    $finded_result_list = jobsearch_find_index_user_meta_list($job_id, 'jobsearch-user-jobs-applied-list', 'post_id', jobsearch_get_user_id());
    if (is_array($finded_result_list) && !empty($finded_result_list)) {
        $classes_str = 'jobsearch-applied-btn';
        $btn_text = $btn_applied_label;
        $is_applied = true;
    }
}


$job_aply_type = 'internal';

$this_wredirct_url = jobsearch_server_protocol() . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

$max_cvs_allow = isset($jobsearch_plugin_options['max_cvs_allow']) && absint($jobsearch_plugin_options['max_cvs_allow']) > 0 ? absint($jobsearch_plugin_options['max_cvs_allow']) : 5;


?>
    <div class="container">
        <div class="featimage" style="height: 300px">
            <img src="<?php the_post_thumbnail_url(); ?>" style="width: 100%; height: 100%; object-fit: cover;"/>
        </div>
    </div>


<?php

// get date job
$jobid = $_GET['jobid'];
$date_job = get_post_meta($jobid, 'job_data_set', true);
//print_r($date_job);

?>
    <!--                                incepe modal aplicare -->
    <input type="hidden" value="<?= $candidate_id; ?>" class="candidat-<?= $job_id; ?>"/>
    <div class="jobsearch-apply-withcvs">
        <div class="header-pop-up-1">
            <div class="left-header-pop-up">
                <h5 class="title-header">Aplica pentru > <a href=""><?= $date_job['nume_companie']; ?>
                        > <?= $date_job['post_job']; ?></a></h5>

            </div>
        </div>

        <div class="fereastra-aplicare-pop-up pop-up-<?php echo($p_rand_id) ?>">

            <div class="content-pop-up">

                <div class="jobEdit" style="margin: 30px -30px; float:left;">
                    <div class="jobsearch-employer-box-sectios">
                        <div class="row">
                            <div class="col-12 col-md-12">
                                <div class="title-section title-section-top">
                                    <h6 class="title-section-text title-section-text-aplica">Aplici
                                        la: <?= $date_job['nume_companie'] . '-' . $date_job['post_job']; ?> </h6>
                                    <hr class="end-titile">
                                    <div class="zonaCompanieTop">
                                        <div class="row">
                                            <div class="col-md-9">
                                                <div class="holder-name">
                                                    <h1 class="title_company"><strong>Nume
                                                            companie: </strong><?= $date_job['nume_companie']; ?></h1>
                                                    <h2 class="job_company_apply"><strong>Job
                                                            : </strong><?= $date_job['post_job']; ?></h2>
                                                    <h2 class="job_company_apply"><strong>Localizare
                                                            : </strong><?= $date_job['localizare_in_romania'][1]; ?>
                                                    </h2>
                                                    <h2 class="job_company_apply"><strong>Numarul de angajati
                                                            : </strong><?= $date_job['numar_angajati']; ?></h2>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <img class="img_profil_company"
                                                     src="<?= wp_get_attachment_url($date_job['imagine_profil_companie']) ?>"
                                                     style="max-height: 200px"/>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <h3 class="title-template-anunt title-template-anunt-brd">ALEGE PROFIL CANDIDAT</h3>
                            </div>
                            <div class="col-12 col-md-4">
                                <?php
                                $templates = get_post_meta($candidate_id, "candidat_profiles", true);
                                $templates = unserialize(base64_decode($templates));

                                ?>

                                <ul class="lista-template" style="padding: 0; margin: 0">

                                    <?php
                                    $count = 0;
                                    foreach ($templates as $key => $template) {
                                        $activ = ($count == 0) ? 'activlink' : '';
                                        $max_length = 100;

                                        if (strlen($template['descriere_template']) > $max_length) {
                                            $offset = ($max_length - 3) - strlen($template['descriere_template']);
                                            $template['descriere_template'] = substr($template['descriere_template'], 0, strrpos($template['descriere_template'], ' ', $offset)) . '...';
                                        }
                                        $id_template = wc_strtolower(str_replace(' ', '', $key));
                                        $max_length = 120;

                                        if (strlen($template['descriere_template']) > $max_length) {
                                            $offset = ($max_length - 3) - strlen($template['descriere_template']);
                                            $template['descriere_template'] = substr($template['descriere_template'], 0, strrpos($template['descriere_template'], ' ', $offset)) . '...';
                                        }
                                        echo "<li class='template-name'><strong><a href='#' onclick='void(0);return false;' class='template-on  template-on-apply-$job_id link-template-$id_template $activ'  data-template='$id_template' data-nume='$key'>" .
                                            $key . "</a></strong><br />" . $template['descriere_template'] .
                                            "</li>";
                                        $count++;
                                    }

                                    ?>

                                </ul>
                                <input type="hidden" id="profil-candidat-<?= $job_id; ?>"
                                       name="profil-candidat-<?= $job_id; ?>">

                            </div>

                            <div class="col-12 col-md-8 class-wrapp">
                                <?php
                                $count = 0;
                                foreach ($templates as $key => $template) {
                                    $id_template = wc_strtolower(str_replace(' ', '', $key));
                                    ?>
                                    <input type="hidden" name="profil_candidat_obj-<?= $id_template ?>"
                                           value='<?= $key ?>'
                                           class="template-input template-<?= $id_template; ?> a-profil-candidat-<?= $job_id ?> <?php echo ($count == 0) ? 'activ' : ''; ?>">

                                    <div class="template-wrap template-wrap-apply-<?= $job_id ?> template-<?= $id_template; ?>  <?php echo ($count == 0) ? 'activ' : ''; ?>">

                                        <!--    aici incepe template candidat-->

                                        <div id="profil-candidat">

                                            <div class="container-wrapper">


                                                <div class="zonaCandidat">

                                                </div>

                                                <!--                                                <h2 class="job-title">-->
                                                <? //= $template['prenume'] . ' ' . $template['particula'] . '  ' . $template['nume'] . '(' . $template['formula_adresare'] . ')' ?><!---->
                                                <?php
                                                //													//   echo "<a href='javascript:void(0)' class='edit-template' data-template='$key' data-title='" . $template['nume'] . "'>EDITEAZA</a>";
                                                //													?><!--</h2>-->
                                                <h2 class="job-title"><?= $template['prenume'] . ' ' . $template['particula'] . '  ' . $template['nume'] . '(' . $template['formula_adresare'] . ')' ?></h2>




                                                <div class="zonaCandidat" id="informatiiCandidat">


                                                    <!--                                        <img src="--><?//= wp_get_attachment_url($template['imagine_cover']); ?><!--"/>-->
                                                    <!---->
                                                    <!--                                        <img src="--><?//= wp_get_attachment_url($template['imagine_profil']); ?><!--"-->
                                                    <!--                                             style="max-height: 200px"/>-->
                                                    <div class="row textCandidat">
                                                        <div class="col-12 col-md-4">
                                                            <h2>Date personale</h2>
                                                            <p>Varsta: <?= date('Y') - $template['anul_nasterii']; ?> ani</p>
                                                            <p>Sex: <?= $template['sex']; ?></p>

                                                        </div>
                                                        <div class="col-12 col-md-4">
                                                            <br>
                                                            <p>Nickname: <?= $template['sex']; ?></p>
                                                            <p>Resedinta: <?= $template['judet_candidat']; ?></p>
                                                        </div>
                                                        <div class="col-12 col-md-4 links-profiles">
                                                            <strong>Link-uri</strong>:<br>
                                                            <?php
                                                            foreach ($template['link-uri'] as $titlu => $link) {
                                                                echo "<a href='$link' target='_blank'>$titlu</a><br/>";
                                                            }
                                                            ?>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="zonaCandidat" id="scrisoare-intentie">

                                                </div>


                                                <div class="zonaCandidat" id="educatie">
                                                    <h2>Educatie</h2>
                                                    <ul style="padding-left: 20px; list-style: square; ">
                                                        <?php
                                                        foreach ($template['educatie'] as $studiu) {
                                                            echo '<li>' . $studiu . '</li>';
                                                        }
                                                        ?>
                                                    </ul>
                                                </div>

                                                <div class="zonaCandidat" id="limbi">
                                                    <h2>Limbi straine</h2>
                                                    Engleza - <?= $template['limba_engleza']; ?><br>
                                                    <?php
                                                    foreach ($template['limba_nivel'] as $studiu) {
                                                        echo $studiu . '</br>';
                                                    }
                                                    ?>
                                                </div>
                                                <div class="zonaCandidat" id="certificari">
                                                    <h2>Competente relevante</h2>
                                                    <?= $template['relevant_skils']; ?>
                                                </div>

                                                <div class="zonaCandidat" id="certificari">
                                                    <h2>Certificari</h2>
                                                    <?php
                                                    foreach ($template['certificare'] as $studiu) {
                                                        echo $studiu . ', ';
                                                    }
                                                    ?>
                                                </div>

                                                <div class="zonaCandidat" id="experienta">
                                                    <h2>Experienta</h2>
                                                    <ul style="margin:0;">
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
                                                            </li>
                                                        <?php endforeach; ?>
                                                    </ul>
                                                </div>

                                                <div class="zonaCandidat" id="cerinte">
                                                    <h2>Cerinte</h2>
                                                    <b>Salariu: <?= $template['salariu_minim_accepta']; ?>€</b> <br>
                                                    <?= $template['alte_cerinte']; ?>
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

                <script>
                    // schimba tabul
                    jQuery('<?=  ".template-on-apply-$job_id" ?>').click(function () {
                        jQuery('<?=  ".template-on-apply-$job_id" ?>').removeClass('activlink');
                        jQuery(this).addClass('activlink');

                        var template = '.template-' + jQuery(this).attr('data-template');
                        jQuery('#profil-candidat-<?= $job_id; ?>').val(template);
                        jQuery('<?=  ".template-wrap-apply-$job_id" ?>').removeClass('activ');
                        jQuery('<?=  ".template-input" ?>').removeClass('activ');
                        jQuery(template).addClass('activ');
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

                    .wrap-date {
                        top: 50%;
                        left: 50%;
                        position: absolute;
                        transform: translate(-50%, -50%);
                    }

                    .lista-template li {
                        font-size: 12px;
                        font-weight: 400;
                        font-family: "Open Sans", sans-serif;
                        border-bottom: 2px solid #e1e1e1;
                        line-height: 21px;
                        margin-bottom: 30px;
                        padding-bottom: 30px;
                        height: 75px;
                    }
                </style>


                <!--    aici se termina toate partea de candidat-->

                <div class="title-section">
                    <h6 class="title-section-text">Alege text pentru
                        aplicare</h6>
                    <hr class="end-titile">
                </div>

                <?php
                $user_id = get_current_user_id();
                $candidate_id = jobsearch_get_user_candidate_id($user_id);

                $text_list = get_post_meta($candidate_id, "candidat_texte_predefinite", true);
                $text_list = unserialize(base64_decode($text_list));


                $objTextList = json_encode($text_list);
                ?>
                <div class="text-section">
                    <div class="left-text-section">
                        <ul class="lista-pop-up lista-pop-up-re">
                            <?php foreach ($text_list as $key => $text): ?>
                                <li>
                                    <a href="javascript:void(0)"
                                       class="add-text-apply-<?php echo($p_rand_id) ?> nume-text"
                                       style="display: block; line-height: 20px; padding-top: 10px;"
                                       data-key="<?= $key; ?>">
                                        <?= $key; ?>
                                    </a>
                                    <p style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; line-height: 20px; font-size:12px; font-family: 'Open Sans', sans-serif;">
                                        <?php echo strip_tags($text['descriere_template']); ?>
                                    </p>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                        <div class="fullwWrap">
                            <a href="javascript:void(0)"
                               class="<?= "new-text-$job_id"; ?> newaddbtn">
                                Adauga text nou</a>
                        </div>
                    </div>
                    <div class="right-text-section">
                        <div class="description-text">

                            <?php
                            $content = '';
                            $editor_id = "zonaText_$job_id";

                            $settings = [
                                'textarea_name' => "zonaText_$job_id",
                                'wpautop' => false,
                                'media_buttons' => false,
                                'quicktags' => [
                                    'buttons' => 'strong,em,del,ul,ol,li,block,close'
                                ],
                            ];

                            wp_editor($content, $editor_id, $settings); ?>

                        </div>
                        <br>
                        <button id="salveaza_profil_job">Save</button>
                        <button id="salveaza_ca_job_profil">Save as</button>

                        <div id="wrap-salveaza-ca" style="display: none">
                            <input type="text" name="nume_template_as" placeholder="denumire text"
                                   id="nume_template_as">
                            <input type="text" name="descriere_template_as" placeholder="descriere"
                                   id="descriere_template_as">
                            <button id="ok_save_as">Salveaza text predefinit</button>
                        </div>

                    </div>


                    <script>
                        jQuery('.newaddbtn').hide();

                        jQuery('#salveaza_profil_job').hide();
                        jQuery('#salveaza_ca_job_profil').hide();


                        var texte = <?= $objTextList; ?>;

                        function updateContent(idText, content) {
                            tinymce.get(idText).setContent(content);
                        }

                        jQuery('.add-text-apply-<?php echo($p_rand_id) ?>').click(function () {
                            jQuery('.add-text-apply-<?php echo($p_rand_id) ?>').removeClass('activat');
                            jQuery(this).addClass('activat');
                            var key = jQuery(this).attr('data-key');
                            var text = texte[key]['text'];
                            updateContent('<?= $editor_id; ?>', text)
                            jQuery('#salveaza_profil_job').show();
                            jQuery('#salveaza_ca_job_profil').show();
                            jQuery('.newaddbtn').show();

                        });

                        jQuery('.<?= "new-text-$job_id"; ?>').click(function () {
                            jQuery('.add-text-apply-<?php echo($p_rand_id) ?>').removeClass('activat');
                            updateContent('<?= $editor_id; ?>', '');
                            jQuery('#salveaza_profil_job').hide();
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


                        function get_template_data() {

                            var input = jQuery('.nume-text.activat');
                            var format_obj = {};

                            if (input.attr('data-key') != '' && input.attr('data-key') != null) {
                                format_obj['nume_template'] = input.attr('data-key');
                                format_obj['descriere_template'] = input.next().html();
                            } else {
                                format_obj['nume_template'] = 'nume default';
                                format_obj['descriere_template'] = 'descriere default';
                            }

                            format_obj['nume_template_as'] = jQuery('#nume_template_as').val();
                            format_obj['descriere_template_as'] = jQuery('#descriere_template_as').val();

                            format_obj['text'] = tinymce.get('<?= $editor_id; ?>').getContent();


                            return format_obj;
                        }

                        jQuery('#salveaza_profil_job').click(function (event) {

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
                                        'tip': 'candidat_texte'
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

                            var format_obj = get_template_data();

                            format_obj.nume_template = format_obj.nume_template_as;
                            format_obj.descriere_template = format_obj.descriere_template_as;

                            console.log(format_obj);
                            jQuery.ajax({
                                type: "post",
                                url: "<?=  admin_url('admin-ajax.php'); ?>",
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
                                    //   console.log(data);
                                    location.reload();
                                }
                                ,
                                error: function (errorThrown) {
                                    console.log(errorThrown);
                                }
                            });


                        });


                    </script>

                </div>
                <div class="title-section">
                    <h6 class="title-section-text">Alege fisiere atasate in
                        contul personal</h6>
                    <hr class="end-titile">
                </div>

                <?php
                $user_id = get_current_user_id();
                $candidate_id = jobsearch_get_user_candidate_id($user_id);
                //	$fisire_atasate = get_field( 'fisiere', $candidate_id );

                ?>
                <div class="text-section">
                    <div class="left-text-section left-text-section-1">
                        <ul class="lista-pop-up" style="height: 292px; overflow-y: auto;">

                            <?php
                            $the_query = new WP_Query(array(
                                'post_type' => 'attachment',
                                'post_status' => 'inherit',
                                'author' => $user_id,
                                'posts_per_page' => -1,
                                'post_mime_type' => array('application/doc', 'application/pdf', 'text/plain'),
                            ));
                            if ($the_query->have_posts()) {
                                while ($the_query->have_posts()) : $the_query->the_post();
                                    $url = wp_get_attachment_url();
                                    $id = get_the_ID();
                                    ?>

                                    <li>
                                        <a href="javascript:void(0)"
                                           class="add-file-apply-<?php echo($p_rand_id) ?> filelink  link-<?= $id; ?>"
                                           style="display: block; line-height: 20px; padding-top: 10px;"
                                           data-id="<?= $id; ?>">
                                            <span class="nume-fisier"><?= the_title(); ?></span>
                                            <span class="link-fisier"><?= $url; ?></span>
                                        </a>
                                        <p style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; font-size:12px; font-family: 'Open Sans', sans-serif;">
                                            <?php echo strip_tags(get_the_excerpt()); ?>
                                        </p>
                                    </li>


                                <?php
                                endwhile;
                            } ?>

                        </ul>
                    </div>


                    <div class="right-text-section right-text-section-1">
                        <div class="description-text description-text-1" style="height: 291px">
                            <div class="fisier-pop-up-text"
                                 id="fisiereAtasate-<?php echo($job_id) ?>">

                            </div>
                        </div>
                    </div>
                    <script>


                        function removeline(element) {
                            var id = jQuery(element).attr('data-id');
                            jQuery('.link-' + id).removeClass('activat');
                            jQuery(element).parent().remove();
                        }

                        var count = 0;
                        jQuery('.add-file-apply-<?php echo($p_rand_id) ?>').click(function () {
                            //  jQuery('.filelink').removeClass('activat');
                            jQuery(this).addClass('activat');
                            var link, nume, fisiere, zona, id;
                            id = jQuery(this).attr('data-id');
                            nume = jQuery('.nume-fisier', this).text();
                            link = jQuery('.link-fisier', this).text();


                            // creare fisier
                            fisiere = '<div class="linie-fisier fisier-' + id + '"><span>' + nume + '</span><input name="linkfisier-' + count + '"  class="fisier-<?php echo $_REQUEST['jobid'] ?>" type="hidden" data-nume="' + nume + '" val="' + nume + '" data-link="' + link + '"  data-id=' + id + '>' +
                                '<span class="remove" onclick="javascript:removeline(this);" data-id=' + id + '><i class="fa fa-minus-square" aria-hidden="true"></i></span></div>';
                            zona = jQuery('#fisiereAtasate-<?php echo($job_id) ?>')
                            // verific daca exista fifsier
                            if (zona.has(".fisier-" + id).length === 0) {
                                count++;
                                zona.append(fisiere);
                            }

                        })
                    </script>
                </div>

                <script>
                    jQuery("#featured_upload-<?php echo($job_id) ?>").on("change", ".file-upload-field", function () {

                        $(this).parent(".file-upload-wrapper").attr("data-text",);

                        var numeFisier = $(this).val().replace(/.*(\/|\\)/, '');
                        // creare fisier
                        var fisiere = '<div class="linie-fisier fisier-new"><span>' + numeFisier + '</span>' +
                            '<span class="remove removenew-<?php echo($job_id) ?>" onclick="javascript:removeline(this);"><i class="fa fa-minus-square" aria-hidden="true"></i></span></div>';

                        jQuery('#fisiereAtasate-new-<?php echo($job_id) ?>').html(fisiere);
                    });

                    jQuery('.removenew<?php echo($job_id) ?>').click(function () {
                        jQuery('.fileinput-<?php echo($job_id) ?>').val();
                    });


                </script>


                <button onclick="void(0);"
                        class="send-pop-up-button action-<?php echo($p_rand_id) ?>"
                        data-app="<?php echo($p_rand_id) ?>">Preview si
                    trimite
                </button>


            </div>
        </div>

        <div class="preview-aplicatie preview-<?php echo($p_rand_id) ?>"
             style="display:none;">

            <div class="jobsearch-main-content whitebg"
                 style="width: 100%; float: left; padding: 10px 30px">

                <!-- Main Section -->
                <div class="jobsearch-main-section">
                    <div class="jobsearch-plugin-default-container"
                         style="padding: 20px;">
                        <div class="jobsearch-row">
                            <?php
                            // date personale
                       //     $job_id = $p_job_id = $_REQUEST['jobid'];

                            ?>

                            <aside class="jobsearch-column-3 jobsearch-typo-wrap">
                                <div class="widget widget_candidate_info">
                                    <div class="jobsearch_candidate_info">
                                        <div class="wrapAvatar">
                                            <img src=" " class="data-imagine-profil"/>
                                        </div>
                                        <div class="datePersonale">
                                            <p>Varsta: <span class="data-varsta"></span> ani</p>
                                            <p>Sex: <span class="data-sex"></span></p>
                                            <p>Nickname: <span class="data-nickname"></span></p>
                                            <p> Resedinta: <span class="data-resedinta"></span></p>
                                            <p>Link-uri:<br>
                                                <span class="data-links"></span>
                                            </p>

                                        </div>

                                    </div>
                                </div>
                            </aside>


                            <div id="profil-candidat"
                                 class="jobsearch-column-9 jobsearch-typo-wrap"   >
                                <div class="container-wrapper">
                                    <h1 class="numecandidat"><span class="data-nume-candidat"></span></h1>

                                    <div class="zonaCandidat text-<?php echo($p_rand_id) ?> text-aplicare"
                                         id="scrisoare-intentie">
                                    </div>

                                    <div class="zonaCandidat" id="educatie">
                                        <h2><?php esc_html_e('Educatie', 'managero'); ?></h2>
                                        <ul style="padding-left: 20px; list-style: square; " class="data-educatie">
                                        </ul>
                                    </div>

                                    <div class="zonaCandidat" id="limbi">
                                        <h2><?php esc_html_e('Limbi straine', 'managero'); ?></h2>
                                        <span class="data-engleaza"></span>
                                        <span class="data-alte-limbi"></span>
                                    </div>


                                    <div class="zonaCandidat"  id="certificari">
                                        <h2><?php esc_html_e('Certificari', 'managero'); ?></h2>
                                        <span class="data-certificari"></span>
                                    </div>

                                    <div class="zonaCandidat" id="relevente">
                                        <h2><?php esc_html_e('Competente relevante', 'managero'); ?></h2>
                                        <span class="data-relevante"></span>
                                    </div>

                                    <div class="zonaCandidat" id="experienta">
                                        <h2><?php esc_html_e('Experienta', 'managero'); ?></h2>
                                        <ul class="data-educatie">

                                        </ul>
                                    </div>

                                    <div class="zonaCandidat" id="cerinte">
                                        <h2><?php esc_html_e('Cerinte', 'managero'); ?></h2>
                                        <b>Salariu:
                                            <span class="data-salariu"></span>&euro;</b> <br/>
                                        <span class="data-alte-cerinte"></span>
                                    </div>

                                    <div class="zonaCandidat" id="brief">
                                        <h2><?php esc_html_e('Note si comentarii libere', 'managero'); ?></h2>
                                        <span class="data-comentarii"></span>

                                    </div>


                                    <div class="zonaCandidat fisiere-<?php echo($p_rand_id) ?>"
                                         id="zonaFisiere">
                                        <h2><?php esc_html_e('Fisiere atasate', 'managero'); ?></h2>
                                        <span class="data-fisiere"></span>
                                    </div>

                                    <?php
                                    echo apply_filters('jobsearch_applying_job_before_apply', '');  ?>
                                    <a href="javascript:void(0)"
                                       class="btnbackapply switch-<?php echo($p_rand_id) ?>">
                                        Inapoi la texte si fisiere
                                    </a>
                                    <a href="javascript:void(0);"
                                       class="<?php echo esc_html($p_classes_str); ?> jobsearch-apply-btn btnapplyn tupac123  jobsearch-applyjob-btn jobsearch-apply-btn-<?php echo absint($p_rand_id); ?> " <?php echo(!is_user_logged_in() ? 'data-wredircto="' . $this_wredirct_url . '"' : '') ?>
                                       data-randid="<?php echo absint($p_rand_id); ?>"
                                       data-jobid="<?php echo $_REQUEST['jobid']; //  absint($p_job_id); ?>"
                                       data-btnafterlabel="<?php echo esc_html($p_btn_after_label) ?>"
                                       data-btnbeforelabel="<?php echo esc_html($p_btn_text) ?>"
                                    >
                                        <?php echo esc_html($p_btn_text) ?>
                                    </a>
                                    <small class="apply-bmsg"></small>


                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Main Section -->

            </div>

            <style>
                .jobsearch-applied-job-btns .candidate-more-acts-con ul {
                    position: absolute;
                    top: 100%;
                    right: 0px;
                    border: 1px solid #ddd;
                    background-color: #ffffff;
                    margin-top: 2px;
                    display: none;
                    z-index: 100;
                }

                .candidate-more-acts-con ul li {
                    float: left;
                    width: 100%;
                    border-bottom: 1px solid #ddd;
                    padding: 9px 8px;
                    -webkit-transition: all 0.4s ease-in-out;
                    -moz-transition: all 0.4s ease-in-out;
                    -ms-transition: all 0.4s ease-in-out;
                    -o-transition: all 0.4s ease-in-out;
                    transition: all 0.4s ease-in-out;
                }

                .candidate-more-acts-con ul li a {
                    display: block;
                    white-space: nowrap;
                    -webkit-transition: all 0.4s ease-in-out;
                    -moz-transition: all 0.4s ease-in-out;
                    -ms-transition: all 0.4s ease-in-out;
                    -o-transition: all 0.4s ease-in-out;
                    transition: all 0.4s ease-in-out;
                }

                .datePersonale span{
                display:  initial !important;
                color:  initial !important;
                }

            </style>

        </div>


        <script>

            <?php
            $templates = get_post_meta($candidate_id, "candidat_profiles", true);
            $templates = unserialize(base64_decode($templates));

            ?>

            var profile_candidat = <?php echo json_encode($templates); ?>;

            var data_obj = {};

            function get_data() {
                var selected_profile = jQuery('.activlink', '.template-name').attr('data-nume');
                var curent_profile = profile_candidat[selected_profile];
                var text = tinyMCE.get('<?= "zonaText_$job_id"; ?>').getContent();
                var fisiere = {};

                jQuery('.activat').each(function (index) {
                    var nume = jQuery('.nume-fisier', this).text();
                    var fisier = jQuery('.link-fisier', this).text();

                    fisiere[index] = {
                        'nume': nume,
                        'fisier': fisier
                    };
                });

                var imagine_candidat = jQuery('img','.activ').attr('src');
                data_obj['job'] = curent_profile;
                data_obj['job'].imagine_cadidat = imagine_candidat;
                data_obj['text'] = text;
                data_obj['fisiere'] = fisiere;

                return data_obj;
            }

            function apply_data() {

                // get profile data
                var data_obj = get_data();

                // imagine profil
                jQuery('.data-imagine-profil').attr('src', data_obj.job.imagine_cadidat );

                // date personal candidat
                jQuery('.data-nume-candidat').html(data_obj.job.nume);
                jQuery('.data-nickname').html(data_obj.job.nickaname);
                jQuery('.data-particula').html(data_obj.job.particula);
                jQuery('.data-resedinta').html(data_obj.job.judet_candidat);

                // link-uri
                var links = '';
                $.each( data_obj.job['link-uri'] , function( key, value ) {
                    links = links + '<li><a href="' + value + '" >' + key  + '</a></li>';
                });
                jQuery('.data-link-uri').html(links);

                // varsta, returneaza anul nasterii
                var varsta = (new Date()).getFullYear() - data_obj.job.anul_nasterii;
                jQuery('.data-varsta').html(varsta);

                // sex
                jQuery('.data-sex').html(data_obj.job.sex);

                // educatie
                var educatie = '';
                jQuery(data_obj.job.educatie).each(function () {
                   educatie = educatie + '<li>' + this + '</li>';
                });
                jQuery('.data-educatie').html(educatie);

                // limba engleza
                jQuery('.data-engleaza').html('<li>Engleza - '  +  data_obj.job.limba_engleza + '</li>');

                // alte limbi
                var limbi = '';
                jQuery(data_obj.job.limba_nivel).each(function () {
                    limbi = limbi + '<li>' +  this + '</li>';
                });
                jQuery('.data-alte-limbi').html(limbi);

                // salariu minim acceptat
                jQuery('.data-salariu').html(data_obj.job.salariu_minim_accepta);

                // note comentarii
                jQuery('.data-comentarii').html(data_obj.job.note_comentarii);

                // certificari
                var certificare = '';
                jQuery(data_obj.job.certificare).each(function () {
                    certificare = certificare + ', ' +  this;
                });
                jQuery('.data-certificari').html(certificare);

                // relevant skils
                jQuery('.data-relevante').html(data_obj.job.relevant_skils);

                // fisiere aplicatie
                var fisiere = '';
                $.each( data_obj.fisiere, function( key, value ) {
                    fisiere = fisiere + '<li><a href="' + value.fisier + '" >' + value.nume  + '</a></li>';
                });
                jQuery('.data-fisiere').html(fisiere);


            }

            jQuery('.action-<?php echo($p_rand_id) ?>').click(function () {
                apply_data();
                jQuery('.preview-<?php echo($p_rand_id) ?>').toggle();
                jQuery('.pop-up-<?php echo($p_rand_id) ?>').toggle();

                var text = tinyMCE.get('<?= "zonaText_$job_id" ?>').getContent();

                jQuery('.text-<?php echo($p_rand_id) ?>').html(text);

            });

            jQuery(document).on('click', '#JobSearchModalMultiCVs<?php echo($p_rand_id) ?> .modal-close', function () {
                jQuery('.preview-<?php echo($p_rand_id) ?>').css('display', 'none');
                jQuery('.pop-up-<?php echo($p_rand_id) ?>').css('display', 'block');
                apply_data();
            });

            jQuery('.modal-click-<?php echo($p_rand_id) ?>').on('click', function (e) {
                if (e.target !== e.currentTarget)
                    return;
                jQuery('.preview-<?php echo($p_rand_id) ?>').css('display', 'none');
                jQuery('.pop-up-<?php echo($p_rand_id) ?>').css('display', 'block');
                apply_data();
            });
            jQuery('.switch-<?php echo($p_rand_id) ?>').click(function () {
                jQuery('.preview-<?php echo($p_rand_id) ?>').css('display', 'none');
                jQuery('.pop-up-<?php echo($p_rand_id) ?>').css('display', 'block');
                apply_data();
            });

        </script>


    </div>

<?php
wp_enqueue_script('jobsearch-job-functions-script');
get_footer();
