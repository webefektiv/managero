<?php
global $jobsearch_plugin_options;

$post_id = get_the_ID();
$job_id = $post_id;

// get job id
$job_id = get_the_ID();
$post_id = $job_id;

// get job user
$companie_id = get_the_author_meta('ID');


// returneaza date job
$date_job = get_post_meta($post_id, 'job_data_set', true);


$companie_id = get_post_meta($job_id, 'jobsearch_field_job_posted_by', true);

?>

<div class="container">
    <div class="featimage">
        <?php if ($date_job['imagine_cover']): ?>
            <?php /* returneaza imagine companie coperta */
            $coverImage = wp_get_attachment_url($date_job['imagine_cover']) ?>
            <img src="<?= $coverImage; ?>"/>
        <?php endif; ?>


    </div>
    <div class="row">
        <div class="col-4">
            <h1 class="page-title-top" style="width: 700px;"><?php the_title(); ?>
                - <?= $date_job['nume_companie']; ?> </h1>
        </div>
        <?php
        /* returneaza url imagine companie-logo */
        $imagine_profil_companie = wp_get_attachment_url($date_job['imagine_profil_companie']);
        /* returneaza url imagine companie-principala */
        $imagine_profil_principala = wp_get_attachment_url($date_job['imagine_principala']); ?>
        <div class="col-8">
            <img src="<?= $imagine_profil_companie ?>" alt="" style="height: 70px; float:right; margin-top: 10px;">
        </div>
    </div>
</div>

<div class="bg-white">
    <div class="container">
        <div class="row">
            <div class="col-sm-9">

                <div id="sectiune-companie">
                    <h2 class="job-title"><?php esc_html_e('Compania', 'managero'); ?></h2>

                    <div class="row jobDetails">
                        <div class="col-sm-12">
                            <h4><?php esc_html_e('Nume', 'managero'); ?></h4>
                        </div>
                        <div class="col-sm-6">
                            <?= /* nume companie */
                            $date_job['nume_companie']; ?><br>
                            <?= /* nume alternativ */
                            $date_job['nume_alternativ']; ?>
                        </div>
                        <div class="col-sm-6">
                            <img src="<?= $imagine_profil_principala ?>" alt=""
                                 style="height: 100px; float:right;  margin-top: -40px;">
                        </div>
                    </div>


                    <?php /* verificam daca exista descriere companie + returneaza descriere daca exista */
                    if (in_array('descriere', $date_job['date_visibile'])): ?>

                        <div class="row jobDetails">
                            <div class="col-sm-12">
                                <h4><?php esc_html_e('Descriere', 'managero'); ?></h4>
                            </div>
                            <div class="col-sm-12">
                                <?= $date_job['descriere_companie']; ?>
                            </div>
                        </div>

                    <?php endif; ?>


                    <?php /* verificam daca exista descriere domeniu pe larg + returneaza descriere daca exista */
                    if (in_array('domeniu-larg', $date_job['date_visibile'])): ?>

                        <div class="row jobDetails">
                            <div class="col-sm-12">
                                <h4><?php esc_html_e('Domeniu de activitate', 'managero'); ?>
                                    <span>(pe larg)</span>
                                </h4>
                            </div>
                            <div class="col-sm-12">
                                <?= $date_job['descriere_domeniu_larg']; ?>
                            </div>
                        </div>

                    <?php endif; ?>

                    <?php /* verificam daca exista descriere domeniu sintetic + returneaza descriere daca exista */
                    if (in_array('domeniu-sintetic', $date_job['date_visibile'])): ?>

                        <div class="row jobDetails">
                            <div class="col-sm-12">
                                <h4><?php esc_html_e('Domeniu de activitate', 'managero'); ?>
                                    <span>(sintetic)</span>
                                </h4>
                            </div>
                            <div class="col-sm-12">
                                <?= $date_job['descriere_domeniu_sintetic']; ?>
                            </div>
                        </div>

                    <?php endif; ?>


                    <div class="row jobDetails">
                        <?php /* verificam daca exista extindere companie + returneaza extindere daca exista */
                        if (in_array('extindere', $date_job['date_visibile'])): ?>
                            <div class="col-md-4">
                                <h4><?php esc_html_e('Extindere', 'managero'); ?></h4>
                                <?= $date_job['extindere']; ?>
                            </div>
                        <?php endif; ?>
                        <?php /* verificam daca exista origine companie + returneaza originea daca exista */
                        if (in_array('origine', $date_job['date_visibile'])): ?>
                            <div class="col-md-4">
                                <h4><?php esc_html_e('Tara de origine', 'managero'); ?></h4>
                                <?= $date_job['origine']['1']; ?>
                            </div>
                        <?php endif; ?>
                        <?php /* verificam daca exista locatie principala companie + returneaza locatia principala daca exista */
                        if (in_array('head-office', $date_job['date_visibile'])): ?>
                            <div class="col-md-4">
                                <h4><?php esc_html_e('Head of office', 'managero'); ?></h4>
                                <?= $date_job['head_office']; ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="row jobDetails">
                        <?php /* verificam daca exista locaziare companie in romania + returneaza locatia daca exista */
                        if (in_array('localizare-ro', $date_job['date_visibile'])): ?>
                            <div class="col-md-4">
                                <h4><?php esc_html_e('Localizare in Romania', 'managero'); ?></h4>
                                <?php print_r($date_job['localizare_in_romania']['1']); ?>
                            </div>
                        <?php endif; ?>
                        <?php /* verificam daca exista cifra de afaceri a companiei + returneaza cifra de afaceri, daca exista */
                        if (in_array('cifra_afaceri', $date_job['date_visibile'])): ?>
                            <div class="col-md-4">
                                <h4><?php esc_html_e('Cifra de afaceri in Romania', 'managero'); ?></h4>
                                <?= $date_job['cifra_de_afaceri']; ?> &euro;
                            </div>
                        <?php endif; ?>
                        <?php /* verificam daca exista numarul de angajati ai companiei + returneaza numarul de angajati, daca exista */
                        if (in_array('nr-angajati', $date_job['date_visibile'])): ?>
                            <div class="col-md-4">
                                <h4><?php esc_html_e('Numar de angajati in Romania', 'managero'); ?></h4>
                                <?= $date_job['numar_angajati']; ?> de angajati
                            </div>
                        <?php endif; ?>
                    </div>

                </div>

                <div id="sectiune-job">
                    <h2 class="job-title"><?php esc_html_e('Jobul', 'managero'); ?></h2>
                    <div class="row jobDetails">
                        <div class="col-md-12">
                            <h4><?php esc_html_e('Descriere'); ?></h4>
                            <?= $date_job['descrierea_sintetica_a_jobului']; ?>
                        </div>
                    </div>

                    <div class="row jobDetails">
                        <div class="col-md-4">
                            <h4><?php esc_html_e('Pozitie ierarhica'); ?></h4>
                            <?= /* returneaza nivelul ierarhic al jobului */
                            $date_job['nivelul_ierarhic']; ?>
                        </div>
                        <div class="col-md-4">
                            <h4><?php esc_html_e('Cui este subordonat'); ?></h4>
                            <?= /* returneaza nivelul cui ii este subordonat */
                            $date_job['cui_este_subordonat']; ?>
                        </div>
                        <div class="col-md-4">
                            <h4><?php esc_html_e('Numar de subordonati'); ?></h4>
                            <?= /* returneaza cati subordonati are */
                            $date_job['subordonati_total']; ?> subordonati
                        </div>
                    </div>


                </div>

                <div id="sectiune-oferta">
                    <div class="row jobDetails">
                        <div class="col-sm-12">
                            <h4><?php esc_html_e('Cerinte job', 'managero'); ?></h4>
                            <?= $date_job['cerinte_job']; ?>
                        </div>
                    </div>
                </div>

                <div id="sectiune-scm">
                    <?php $domenii = get_field('domeniu', '1648');


                    $user_id = get_current_user_id();
                    $candidate_id = jobsearch_get_user_candidate_id($user_id);
                    $fields_candidat = get_post_meta($candidate_id, "profil_scm", true);

                    $lista_curenta = $fields_candidat = unserialize($fields_candidat);


                    $domenii_job_curent = [];
                    foreach ($domenii as $domeniu) {

                        $check = 0;
                        if ($date_job['profil_scm'][str_replace(' ', '_', preg_replace("/[^a-zA-Z0-9\s]/", "", $domeniu['nume_domeniu']))] > 0) {
                            $check++;

                        }

                        if ($lista_curenta[str_replace(' ', '_', preg_replace("/[^a-zA-Z0-9\s]/", "", $domeniu['nume_domeniu']))] > 0) {
                            $check++;

                        }

                        if ($check) {
                            $domenii_job_curent[] = str_replace(' ', '_', preg_replace("/[^a-zA-Z0-9\s]/", "", $domeniu['nume_domeniu']));
                            $check = 0;

                        }

                        if (count($domeniu['sub_domeniu']) > 0) {
                            foreach ($domeniu['sub_domeniu'] as $subdomeniu) {
                                if ($date_job['profil_scm'][str_replace(' ', '_', preg_replace("/[^a-zA-Z0-9\s]/", "", $subdomeniu['nume_subdomeniu']))] > 0) {
                                    $check++;

                                }
                                if ($lista_curenta[str_replace(' ', '_', preg_replace("/[^a-zA-Z0-9\s]/", "", $subdomeniu['nume_subdomeniu']))] > 0) {
                                    $check++;

                                }
                                if ($check) {
                                    $domenii_job_curent[] = str_replace(' ', '_', preg_replace("/[^a-zA-Z0-9\s]/", "", $subdomeniu['nume_subdomeniu']));
                                    $check = 0;

                                }
                            }
                        }

                    }

                    ?>
                    <div class="sectiune-scm">
                        <div class="row">
                            <div class="col-4 col-md-4">
                                <div class="holder-numbers holder-numbers-1">
                                    <p class="titleDomeniu">Domeniu SCM</p>
                                </div>
                                <?php
                                foreach ($domenii as $domeniu) {

                                    if (in_array(str_replace(' ', '_', preg_replace("/[^a-zA-Z0-9\s]/", "", $domeniu['nume_domeniu'])), $domenii_job_curent)) {
                                        ?>
                                        <div class="scm_domeniu">
                                            <div class="scm_lavel"><?= $domeniu['nume_domeniu']; ?></div>


                                            <?php if (count($domeniu['sub_domeniu']) > 0) {
                                                foreach ($domeniu['sub_domeniu'] as $subdomeniu) {
                                                    if (in_array(str_replace(' ', '_', preg_replace("/[^a-zA-Z0-9\s]/", "", $subdomeniu['nume_subdomeniu'])), $domenii_job_curent)) {
                                                        ?>
                                                        <div class="scm_subdomeniu">
                                                            <div class="scm_lavel"><?= $subdomeniu['nume_subdomeniu'] ?></div>

                                                        </div>

                                                    <?php }
                                                }
                                            } ?>
                                        </div>

                                    <?php }
                                } ?>
                            </div>
                            <div class="col-4 col-md-4">
                                <h4 class="profil_scm_titlu">Profil minim candidat</h4>
                                <div class="holder-numbers">
                                    <p class="number-pscm">1</p>
                                    <p class="number-pscm">2</p>
                                    <p class="number-pscm">3</p>
                                    <p class="number-pscm">4</p>
                                    <p class="number-pscm">5</p>
                                </div>
                                <?php

                                foreach ($domenii as $domeniu) {
                                    if (in_array(str_replace(' ', '_', preg_replace("/[^a-zA-Z0-9\s]/", "", $domeniu['nume_domeniu'])), $domenii_job_curent)) {
                                        ?>
                                        <div class="scm_domeniu">



                                            <div class="stars stars-<?= $date_job['profil_scm'][str_replace(' ', '_', preg_replace("/[^a-zA-Z0-9\s]/", "", $domeniu['nume_domeniu']))]; ?>">
                                                <span></span>
                                                <span></span>
                                                <span></span>
                                                <span></span>
                                                <span></span>
                                            </div>

                                            <?php if (count($domeniu['sub_domeniu']) > 0) {
                                                foreach ($domeniu['sub_domeniu'] as $subdomeniu) {
                                                    if (in_array(str_replace(' ', '_', preg_replace("/[^a-zA-Z0-9\s]/", "", $subdomeniu['nume_subdomeniu'])), $domenii_job_curent)) {
                                                        ?>
                                                        <div class="scm_subdomeniu">

                                                            <div class="stars stars-<?= $date_job['profil_scm'][str_replace(' ', '_', preg_replace("/[^a-zA-Z0-9\s]/", "", $subdomeniu['nume_subdomeniu']))]; ?>">
                                                                <span></span>
                                                                <span></span>
                                                                <span></span>
                                                                <span></span>
                                                                <span></span>
                                                            </div>

                                                        </div>
                                                    <?php }
                                                }
                                            } ?>
                                        </div>

                                    <?php }
                                } ?>
                            </div>
                            <div class="col-4 col-md-4">
                                <?php

                                $user_id = get_current_user_id();
                                $candidate_id = jobsearch_get_user_candidate_id($user_id);
                                $fields_candidat = get_post_meta($candidate_id, "profil_scm", true);

                                $lista_curenta = $fields_candidat = unserialize($fields_candidat);

                                if ($candidate_id > 0) :

                                    ?>
                                    <h4 class="profil_scm_titlu">Autoevaluare</h4>
                                    <div class="holder-numbers">
                                    <p class="number-pscm">1</p>
                                    <p class="number-pscm">2</p>
                                    <p class="number-pscm">3</p>
                                    <p class="number-pscm">4</p>
                                    <p class="number-pscm">5</p>
                                    </div>
                                    <?php
                                    $domenii = get_field('domeniu', '1648');

                                    foreach ($domenii as $domeniu) {
                                        if (in_array(str_replace(' ', '_', preg_replace("/[^a-zA-Z0-9\s]/", "", $domeniu['nume_domeniu'])), $domenii_job_curent)) {

                                            ?>
                                            <div class="scm_domeniu">
                                                <div class="stars stars-<?= $lista_curenta[str_replace(' ', '_', preg_replace("/[^a-zA-Z0-9\s]/", "", $domeniu['nume_domeniu']))]; ?>">
                                                    <span></span>
                                                    <span></span>
                                                    <span></span>
                                                    <span></span>
                                                    <span></span>
                                                </div>

                                                <?php if (count($domeniu['sub_domeniu']) > 0) {
                                                    foreach ($domeniu['sub_domeniu'] as $subdomeniu) {
                                                        if (in_array(str_replace(' ', '_', preg_replace("/[^a-zA-Z0-9\s]/", "", $subdomeniu['nume_subdomeniu'])), $domenii_job_curent)) {
                                                            ?>
                                                            <div class="scm_subdomeniu">
                                                                <div class="stars stars-<?= $lista_curenta[str_replace(' ', '_', preg_replace("/[^a-zA-Z0-9\s]/", "", $subdomeniu['nume_subdomeniu']))]; ?>">
                                                                    <span></span>
                                                                    <span></span>
                                                                    <span></span>
                                                                    <span></span>
                                                                    <span></span>
                                                                </div>

                                                            </div>
                                                        <?php }
                                                    }
                                                } ?>
                                            </div>

                                        <?php }
                                    }
                                endif;
                                ?>
                            </div>
                        </div>
                    </div>
                </div>

                <div id="sectiune-oferta">
                    <div class="row jobDetails">
                        <div class="col-sm-12">
                            <h4><?php esc_html_e('Salariu', 'managero'); ?></h4>
                            <?= /*salariu oferit */
                            $date_job['salariu_oferit_job']; ?>&euro;
                        </div>
                    </div>

                    <div class="row jobDetails">
                        <div class="col-sm-12">
                            <h4><?php esc_html_e('Alte detalii', 'managero'); ?></h4>
                            <?= /*salariu detalii */
                            $date_job['comentarii']; ?>
                        </div>
                    </div>
                    <div class="row jobDetails">
                        <div class="col-sm-12">
                            <h4><?php esc_html_e('Fisiere atasate', 'managero'); ?></h4>
                            <ul class="listaFisiere">
                                <?php
                                /*parcurge array pentru afisarea tuturor fisierelor */
                                foreach ($date_job['fisiere'] as $fisier => $key):


                                    ?>
                                    <li><a href="<?= wp_get_attachment_url($key); ?>" download><?= $fisier; ?></a>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                </div>
                <div id="sectiune-oferta">
                    <div class="row jobDetails">
                        <div class="col-sm-12">
                            <h4><?php esc_html_e('Galeria media', 'managero'); ?></h4>
                            <div class="galerieJob">
                                <?php
                                /* parcurge array pentru afisarea tuturor imaginilor */
                                foreach ($date_job['galerie'] as $media => $key):


                                    ?>
                                    <div class="imgGalWrap">

                                        <a class="fancybox" data-fancybox-group="button" rel="gallery1"
                                           href="<?= wp_get_attachment_url($key) ?>">
                                            <img class="wrap-img" src="<?= wp_get_attachment_url($key) ?>" alt=""/>
                                        </a>

                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>


            </div>


            <div class="col-sm-3">

                <?php
                /* Ia ID-ul utilizatorului curent */
                $current_user = wp_get_current_user();
                $user_id = get_current_user_id();
                $user_obj = get_user_by('ID', $user_id);
                /* Ia ID-ul Angajatorului */
                $employer_id = jobsearch_get_user_employer_id($user_id);
                $owner = get_post_meta($job_id, 'jobsearch_field_job_posted_by', $employer_id);

                if ($employer_id > 0 && $owner == $employer_id):
                    wp_enqueue_script('jobsearch-user-dashboard');
                    ?>

                    <aside class="jobsearch-typo-wrap">

                        <div class="widget widget_apply_job">
                            <div class="widget_apply_job_wrap widget_apply_job_wrap_single">
                                <a href="<?= home_url('') . "/user-dashboard/?tab=job-edit&job_id=$job_id"; ?>"
                                   class="job-edit-icon">
                                    Edit
                                </a>
                                <br/>
                                <a href="javascript:void(0);" data-id="<?php echo($job_id) ?>"
                                   class="jobsearch-icon jobsearch-rubbish jobsearch-trash-job"></a>
                            </div>
                        </div>
                    </aside>
                <?php else: ?>
                    <?php the_apply_button($job_id, $application_deadline, $companie_id);
                    ?>


                    <?php
                    $cum_sa_aplice = get_field('cum_sa_aplice', $job_id);
                    if ($cum_sa_aplice['mod_aplicare'] == 'E-mail') {
                        echo '<div class="widget_apply_job_wrap" style="margin-top: 20px;">';
                        echo "<h4 class='jobsearch-open-signin-tab'>Aplica pe email</h4>";
                        echo "<p>" . $cum_sa_aplice['instructiuni_aplicare'] . "</p>";
                        echo '</div>';
                    };

                    ?>


                <?php endif; ?>

            </div>

        </div>

    </div>
</div>

<script>
    //for login popup
    jQuery('.job-edit-icon').click(function () {
        setTimeout(function () {
            window.location.href = "/user-dashboard/?tab=lista-joburi";
        }, 1000);
    });
    jQuery(document).on('click', '.jobsearch-sendmessage-popup-btn', function () {
        jobsearch_modal_popup_open('JobSearchModalSendMessage');
    });
    jQuery(document).on('click', '.jobsearch-sendmessage-messsage-popup-btn', function () {
        jobsearch_modal_popup_open('JobSearchModalSendMessageWarning');
    });
    jQuery(document).on('click', '.jobsearch-applyjob-msg-popup-btn', function () {
        jobsearch_modal_popup_open('JobSearchModalApplyJobWarning');
    });

</script>

<!--    <script type="text/javascript" src="../lib/jquery-1.10.2.min.js"></script>-->

<!-- Add mousewheel plugin (this is optional) -->
<script type="text/javascript"
        src="<?= get_template_directory_uri() . "/assets/" ?>/lib/jquery.mousewheel.pack.js?v=3.1.3"></script>

<!-- Add fancyBox main JS and CSS files -->
<script type="text/javascript"
        src="<?= get_template_directory_uri() . "/assets/" ?>/source/jquery.fancybox.pack.js?v=2.1.5"></script>
<link rel="stylesheet" type="text/css"
      href="<?= get_template_directory_uri() . "/assets/" ?>/source/jquery.fancybox.css?v=2.1.5" media="screen"/>

<!-- Add Button helper (this is optional) -->
<link rel="stylesheet" type="text/css"
      href="<?= get_template_directory_uri() . "/assets/" ?>/source/helpers/jquery.fancybox-buttons.css?v=1.0.5"/>
<script type="text/javascript"
        src="<?= get_template_directory_uri() . "/assets/" ?>/source/helpers/jquery.fancybox-buttons.js?v=1.0.5"></script>

<!-- Add Thumbnail helper (this is optional) -->
<link rel="stylesheet" type="text/css"
      href="<?= get_template_directory_uri() . "/assets/" ?>/source/helpers/jquery.fancybox-thumbs.css?v=1.0.7"/>
<script type="text/javascript"
        src="<?= get_template_directory_uri() . "/assets/" ?>/source/helpers/jquery.fancybox-thumbs.js?v=1.0.7"></script>

<!-- Add Media helper (this is optional) -->
<script type="text/javascript"
        src="<?= get_template_directory_uri() . "/assets/" ?>/source/helpers/jquery.fancybox-media.js?v=1.0.6"></script>

<script>
    jQuery(document).ready(function () {
//        jQuery(".fancybox").fancybox({
//            openEffect	: 'none',
//            closeEffect	: 'none'
//        });

        $('.fancybox').fancybox({
            openEffect: 'none',
            closeEffect: 'none',

            prevEffect: 'none',
            nextEffect: 'none',

            closeBtn: false,

            helpers: {
                title: {
                    type: 'inside'
                },
                buttons: {}
            },

            afterLoad: function () {
                this.title = 'Image ' + (this.index + 1) + ' of ' + this.group.length + (this.title ? ' - ' + this.title : '');
            }
        });


    });
</script>
<style>
    p {
        font-family: "Open Sans", sans-serif;
        font-size: 14px;
        font-weight: 400;
        line-height: 24px;
        margin-bottom: 10px;
        text-transform: inherit;
    }

    .imgGalWrap img.wrap-img {
        width: 100%;
        object-fit: cover;
        height: 100%;
        border: 1px solid #ececec;
    }

    .imgGalWrap {
        width: 25%;
        padding: 2px;
        float: left;
        height: 140px;
    }

</style>


