<?php
$current_user = wp_get_current_user();
$user_id      = get_current_user_id();
$user_obj     = get_user_by( 'ID', $user_id );
$employer_id = jobsearch_get_user_employer_id( $user_id );
?>


<div class="companie-dashboard">
    <div class="wrapper-content-dashboard-companie">
        <div class="wrapper-navigation-dashboard-company">

            <div class="box-dashboard-company">
                <a href="/user-dashboard/?tab=profil-companie">
                    <div class="wrapper-image-company">
                        <img src="<?= get_template_directory_uri(); ?>/assets/companie/companie.png" alt=""
                             class="wrap-image"/>
                    </div>
                    <h5>Profil companie</h5>
                </a>
            </div>

            <div class="box-dashboard-company">
                <a href="/user-dashboard/?tab=lista-joburi">
                    <div class="wrapper-image-company">
                        <img src="<?= get_template_directory_uri(); ?>/assets/companie/anunturi.png" alt=""
                             class="wrap-image"/>
                    </div>
                    <h5>Anunturile mele</h5>
                </a>
            </div>

            <div class="box-dashboard-company">
                <a href="/user-dashboard/?tab=job-template">
                    <div class="wrapper-image-company">
                        <img src="<?= get_template_directory_uri(); ?>/assets/companie/anunturi.png" alt=""
                             class="wrap-image"/>
                    </div>
                    <h5>Template job</h5>
                </a>
            </div>

            <div class="box-dashboard-company">
                <a href="/user-dashboard/?tab=template-anunt">
                    <div class="wrapper-image-company">
                        <img src="<?= get_template_directory_uri(); ?>/assets/companie/anunturi.png" alt=""
                             class="wrap-image"/>
                    </div>
                    <h5>Template anunt</h5>
                </a>
            </div>


            <div class="box-dashboard-company">
                <a href="/user-dashboard/?tab=file-manager">
                    <div class="wrapper-image-company">
                        <img src="<?= get_template_directory_uri(); ?>/assets/texte_si_fisiere.png"" alt=""
                             class="wrap-image"/>
                    </div>
                    <h5>Fisierele companiei</h5>
                </a>
            </div>
            <div class="box-dashboard-company">
                <a href="/user-dashboard/?tab=adauga-job">
                    <div class="wrapper-image-company">
                        <img src="<?= get_template_directory_uri(); ?>/assets/companie/anunturi.png" alt=""
                             class="wrap-image"/>
                    </div>
                    <h5>Adauga job</h5>
                </a>
            </div>

            <div class="box-dashboard-company">
                <div class="wrapper-image-company">
                    <img src="<?= get_template_directory_uri(); ?>/assets/companie/setari.png" alt=""
                         class="wrap-image"/>
                </div>
                <h5>Setari cont</h5>
            </div>
        </div>
        <div class="wrap-box-candidati-info">
            <h6 class="text-info-wrap-title">
                profile companie
            </h6>
            <span class="number-aplicanti">
                <?php
                $templates = get_post_meta($employer_id, "companie_company_profiles", true);



                $templates = unserialize(base64_decode($templates));
                //print_r($templates);
                echo count($templates);
                ?>
            </span>
        </div>
        <div class="wrap-box-candidati-info">
            <h6 class="text-info-wrap-title">
                template joburi
            </h6>
            <span class="number-aplicanti">
             <?php
             $templates = get_post_meta($employer_id, "companie_job_profiles", true);
             $templates = unserialize(base64_decode($templates));
             echo count($templates);
             ?>
            </span>
        </div>

        <div class="wrap-box-candidati-info">
            <h6 class="text-info-wrap-title">
                template anunturi
            </h6>
            <span class="number-aplicanti">
                <?php
                $templates = get_post_meta($employer_id, "companie_anunt_template", true);
                $templates = unserialize(base64_decode($templates));
                $templates = unserialize(base64_decode($templates));
                echo count($templates);
                 ?>
            </span>
        </div>
        <div class="wrap-box-candidati-info">
            <h6 class="text-info-wrap-title">
                candidati noi
            </h6>
            <span class="number-aplicanti">
                2
            </span>
        </div>
        <div class="wrap-box-candidati-info">
            <h6 class="text-info-wrap-title">
                candidati ok
            </h6>
            <span class="number-aplicanti">
               5
            </span>
        </div>
        <div class="wrap-box-candidati-info">
            <h6 class="text-info-wrap-title">
                candidati rezerve
            </h6>
            <span class="number-aplicanti">
                2
            </span>
        </div>
        <div class="wrap-box-candidati-info">
            <h6 class="text-info-wrap-title">
                candidati respinsi
            </h6>
            <span class="number-aplicanti">
                789
            </span>
        </div>
        <div class="wrap-box-candidati-info">
            <h6 class="text-info-wrap-title">
                candidati blacklisted
            </h6>
            <span class="number-aplicanti">
                4
            </span>
        </div>
    </div>
</div>