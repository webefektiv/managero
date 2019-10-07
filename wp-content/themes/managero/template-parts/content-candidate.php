<?php
/**
 * The template for displaying all single posts.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 */
//
wp_enqueue_script('jobsearch-user-dashboard');


global $post, $jobsearch_plugin_options;
$candidate_id = $post->ID;

$candidate_user_id = jobsearch_get_candidate_user_id($candidate_id);

$candidates_reviews = isset($jobsearch_plugin_options['candidate_reviews_switch']) ? $jobsearch_plugin_options['candidate_reviews_switch'] : '';

$all_location_allow = isset($jobsearch_plugin_options['all_location_allow']) ? $jobsearch_plugin_options['all_location_allow'] : '';

$view_candidate = true;
$restrict_candidates = isset($jobsearch_plugin_options['restrict_candidates']) ? $jobsearch_plugin_options['restrict_candidates'] : '';
$restrict_candidates_for_users = isset($jobsearch_plugin_options['restrict_candidates_for_users']) ? $jobsearch_plugin_options['restrict_candidates_for_users'] : '';

$is_employer = false;


// check permisiuni view

if ($restrict_candidates == 'on') {
    $view_candidate = false;


    if (is_user_logged_in()) {
        $cur_user_id = get_current_user_id();
        $cur_user_obj = wp_get_current_user();
        $employer_id = jobsearch_get_user_employer_id($cur_user_id);
        $ucandidate_id = jobsearch_get_user_candidate_id($cur_user_id);
        $employer_dbstatus = get_post_meta($employer_id, 'jobsearch_field_employer_approved', true);

        if ($employer_id > 0 && $employer_dbstatus == 'on') {
            $is_employer = true;
            if ($restrict_candidates_for_users == 'register_resume') {
                $user_cv_pkg = jobsearch_employer_first_subscribed_cv_pkg();
                if ($user_cv_pkg) {
                    $view_candidate = true;
                }


            } else if ($restrict_candidates_for_users == 'only_applicants') {


                $employer_job_args = array(
                    'post_type' => 'job',
                    'posts_per_page' => '-1',
                    'post_status' => 'publish',
                    'fields' => 'ids',
                    'meta_query' => array(
                        array(
                            'key' => 'jobsearch_field_job_posted_by',
                            'value' => $employer_id,
                            'compare' => '=',
                        ),
                    ),
                );
                $employer_jobs_query = new WP_Query($employer_job_args);
                $employer_jobs_posts = $employer_jobs_query->posts;
                if (!empty($employer_jobs_posts) && is_array($employer_jobs_posts)) {
                    foreach ($employer_jobs_posts as $employer_job_id) {
                        $finded_result_list = jobsearch_find_index_user_meta_list($employer_job_id, 'jobsearch-user-jobs-applied-list', 'post_id', $candidate_user_id);
                        if (is_array($finded_result_list) && !empty($finded_result_list)) {
                            $view_candidate = true;
                            break;
                        }
                    }
                }
            } else {
                $view_candidate = true;

            }
        } else if (in_array('administrator', (array)$cur_user_obj->roles)) {
            $view_candidate = true;

        } else if ($ucandidate_id > 0 && $ucandidate_id == $candidate_id) {
            $view_candidate = true;

        }
    }
}


$captcha_switch = isset($jobsearch_plugin_options['captcha_switch']) ? $jobsearch_plugin_options['captcha_switch'] : '';
$jobsearch_sitekey = isset($jobsearch_plugin_options['captcha_sitekey']) ? $jobsearch_plugin_options['captcha_sitekey'] : '';

$plugin_default_view = isset($jobsearch_plugin_options['jobsearch-default-page-view']) ? $jobsearch_plugin_options['jobsearch-default-page-view'] : 'full';
$plugin_default_view_with_str = '';
if ($plugin_default_view == 'boxed') {

    $plugin_default_view_with_str = isset($jobsearch_plugin_options['jobsearch-boxed-view-width']) && $jobsearch_plugin_options['jobsearch-boxed-view-width'] != '' ? $jobsearch_plugin_options['jobsearch-boxed-view-width'] : '1140px';
    if ($plugin_default_view_with_str != '') {
        $plugin_default_view_with_str = ' style="width:' . $plugin_default_view_with_str . '"';
    }
}

wp_enqueue_script('jobsearch-progressbar');

$inopt_cover_letr = isset($jobsearch_plugin_options['cand_resm_cover_letr']) ? $jobsearch_plugin_options['cand_resm_cover_letr'] : '';
$inopt_resm_education = isset($jobsearch_plugin_options['cand_resm_education']) ? $jobsearch_plugin_options['cand_resm_education'] : '';
$inopt_resm_experience = isset($jobsearch_plugin_options['cand_resm_experience']) ? $jobsearch_plugin_options['cand_resm_experience'] : '';
$inopt_resm_portfolio = isset($jobsearch_plugin_options['cand_resm_portfolio']) ? $jobsearch_plugin_options['cand_resm_portfolio'] : '';
$inopt_resm_skills = isset($jobsearch_plugin_options['cand_resm_skills']) ? $jobsearch_plugin_options['cand_resm_skills'] : '';
$inopt_resm_honsawards = isset($jobsearch_plugin_options['cand_resm_honsawards']) ? $jobsearch_plugin_options['cand_resm_honsawards'] : '';

$candidate_obj = get_post($candidate_id);
$candidate_content = $candidate_obj->post_content;
$candidate_content = apply_filters('the_content', $candidate_content);

$candidate_join_date = isset($candidate_obj->post_date) ? $candidate_obj->post_date : '';

$candidate_jobtitle = get_post_meta($candidate_id, 'jobsearch_field_candidate_jobtitle', true);
$candidate_address = get_post_meta($candidate_id, 'jobsearch_field_location_address', true);


if ($candidate_address == '') {
    $candidate_address = jobsearch_job_item_address($candidate_id);
}

$user_facebook_url = get_post_meta($candidate_id, 'jobsearch_field_user_facebook_url', true);
$user_twitter_url = get_post_meta($candidate_id, 'jobsearch_field_user_twitter_url', true);
$user_google_plus_url = get_post_meta($candidate_id, 'jobsearch_field_user_google_plus_url', true);
$user_youtube_url = get_post_meta($candidate_id, 'jobsearch_field_user_youtube_url', true);
$user_dribbble_url = get_post_meta($candidate_id, 'jobsearch_field_user_dribbble_url', true);
$user_linkedin_url = get_post_meta($candidate_id, 'jobsearch_field_user_linkedin_url', true);

$user_id = jobsearch_get_candidate_user_id($candidate_id);
$user_obj = get_user_by('ID', $user_id);
$user_displayname = isset($user_obj->display_name) ? $user_obj->display_name : '';
$user_displayname = apply_filters('jobsearch_user_display_name', $user_displayname, $user_obj);

$user_def_avatar_url = get_avatar_url($user_id, array('size' => 200));

$user_avatar_id = get_post_thumbnail_id($candidate_id);

if ($user_avatar_id > 0) {
    $user_thumbnail_image = wp_get_attachment_image_src($user_avatar_id, 'full');
    $user_def_avatar_url = isset($user_thumbnail_image[0]) && esc_url($user_thumbnail_image[0]) != '' ? $user_thumbnail_image[0] : '';
}

$user_def_avatar_url = $user_def_avatar_url == '' ? jobsearch_candidate_image_placeholder() : $user_def_avatar_url;
wp_enqueue_script('isotope-min');


$job_id = $_GET['job_id'];
$date_job = get_post_meta($job_id, 'job_data_set', true);

$imagine_cover = wp_get_attachment_url($date_job['imagine_cover']);

$action = '';
if ($_GET['action'] != '') {
    $action = $_GET['action'];
}

?>


<div class="container">

    <div class="featimage">
        <img src="<?= $imagine_cover; ?>" alt="">
    </div>
    <div class="row">
        <div class="col-8">
            <?php if ($action == 'preview_app'):


                $profil_candidat = get_post_meta($job_id, 'jobsearch_job_profil_atasat', true);


                $companie_id = $_GET['employer_id'];


                $job_name = $date_job['post_job'];


                $companie = $date_job['nume_companie'];

                $link = get_the_permalink($job_id);


                echo "<h1 class='page-title-top'>Aplicatie:  <a href='$link'>$job_name - $companie</a></h1>";
            else:
                ?>
                <h1 class="page-title-top">Profilul candidat</h1>
            <?php endif; ?>
        </div>
        <div class="col-4">
            <?php
            if ($action == 'preview_app'):

            else:
                $user_id = get_current_user_id();
                if ($candidate_user_id == $user_id):
                    ?>
                    <a href="http://managero.ro/user-dashboard/?tab=profil-candidat" class="adaugaJob" target="_blank">
                        <i class="fa fa-edit" aria-hidden="true"></i>
                        Editeaza </a>
                <?php endif; endif; ?>

        </div>
    </div>
</div>

<div class="jobsearch-main-content whitebg">

    <!-- Main Section -->
    <div class="jobsearch-main-section">
        <div class="jobsearch-plugin-default-container" <?php echo force_balance_tags($plugin_default_view_with_str); ?>>
            <div class="jobsearch-row">
                <?php
                if ($view_candidate === false) {

                    die(' aici vine  restrictie vizualizare ');

                    $restrict_img = isset($jobsearch_plugin_options['candidate_restrict_img']) ? $jobsearch_plugin_options['candidate_restrict_img'] : '';
                    $restrict_img_url = isset($restrict_img['url']) ? $restrict_img['url'] : '';
                    $restrict_cv_pckgs = isset($jobsearch_plugin_options['restrict_cv_packages']) ? $jobsearch_plugin_options['restrict_cv_packages'] : '';
                    $restrict_msg = isset($jobsearch_plugin_options['restrict_cand_msg']) && $jobsearch_plugin_options['restrict_cand_msg'] != '' ? $jobsearch_plugin_options['restrict_cand_msg'] : esc_html__('The Page is Restricted only for Subscribed Employers', 'wp-jobsearch');
                    ?>
                    <div class="jobsearch-column-12">
                        <div class="restrict-candidate-sec">
                            <img src="<?php echo($restrict_img_url) ?>" alt="">
                            <h2><?php echo($restrict_msg) ?></h2>

                            <?php
                            if ($is_employer) {
                                ?>
                                <p><?php esc_html_e('Please buy a C.V package to view this candidate.', 'wp-jobsearch') ?></p>
                                <?php
                            } else if (is_user_logged_in()) {
                                ?>
                                <p><?php esc_html_e('You are not an employer. Only an Employer can view a candidate.', 'wp-jobsearch') ?></p>
                                <?php
                            } else {
                                ?>
                                <p><?php esc_html_e('If you are employer just login to view this candidate or buy a C.V package to download His Resume.', 'wp-jobsearch') ?></p>
                                <?php
                            }
                            if (is_user_logged_in()) {
                                ?>
                                <div class="login-btns">
                                    <a href="<?php echo wp_logout_url(home_url('/')); ?>"><i
                                                class="jobsearch-icon jobsearch-logout"></i><?php esc_html_e('Logout', 'wp-jobsearch') ?>
                                    </a>
                                </div>
                                <?php
                            } else {
                                ?>
                                <div class="login-btns">
                                    <a href="javascript:void(0);" class="jobsearch-open-signin-tab"><i
                                                class="jobsearch-icon jobsearch-user"></i><?php esc_html_e('Login', 'wp-jobsearch') ?>
                                    </a>
                                    <a href="javascript:void(0);" class="jobsearch-open-register-tab"><i
                                                class="jobsearch-icon jobsearch-plus"></i><?php esc_html_e('Become a Employer', 'wp-jobsearch') ?>
                                    </a>
                                </div>
                                <?php
                            }
                            if (!empty($restrict_cv_pckgs) && is_array($restrict_cv_pckgs) && $restrict_candidates_for_users == 'register_resume') {
                                ?>
                                <div class="jobsearch-box-title">
                                    <span><?php esc_html_e('OR', 'wp-jobsearch') ?></span>
                                </div>
                                <?php
                            }
                            ?>
                        </div>
                        <?php
                        if (!empty($restrict_cv_pckgs) && is_array($restrict_cv_pckgs) && $restrict_candidates_for_users == 'register_resume') {
                            wp_enqueue_script('jobsearch-packages-scripts');
                            ?>
                            <div class="cv-packages-section">
                                <div class="packages-title">
                                    <h2><?php esc_html_e('Buy any CV Packages to get started', 'wp-jobsearch') ?></h2>
                                </div>
                                <?php
                                ob_start();
                                ?>
                                <div class="jobsearch-row">
                                    <?php
                                    foreach ($restrict_cv_pckgs as $restrict_cv_pckg) {
                                        $cv_pkg_obj = $restrict_cv_pckg != '' ? get_page_by_path($restrict_cv_pckg, 'OBJECT', 'package') : '';
                                        if (is_object($cv_pkg_obj) && isset($cv_pkg_obj->ID)) {
                                            $cv_pkg_id = $cv_pkg_obj->ID;
                                            $pkg_type = get_post_meta($cv_pkg_id, 'jobsearch_field_charges_type', true);
                                            $pkg_price = get_post_meta($cv_pkg_id, 'jobsearch_field_package_price', true);

                                            $num_of_cvs = get_post_meta($cv_pkg_id, 'jobsearch_field_num_of_cvs', true);
                                            $pkg_exp_dur = get_post_meta($cv_pkg_id, 'jobsearch_field_package_expiry_time', true);
                                            $pkg_exp_dur_unit = get_post_meta($cv_pkg_id, 'jobsearch_field_package_expiry_time_unit', true);

                                            $pkg_exfield_title = get_post_meta($cv_pkg_id, 'jobsearch_field_package_exfield_title', true);
                                            $pkg_exfield_val = get_post_meta($cv_pkg_id, 'jobsearch_field_package_exfield_val', true);
                                            $pkg_exfield_status = get_post_meta($cv_pkg_id, 'jobsearch_field_package_exfield_status', true);
                                            ?>
                                            <div class="jobsearch-column-4">
                                                <div class="jobsearch-classic-priceplane">
                                                    <h2><?php echo get_the_title($cv_pkg_id) ?></h2>
                                                    <div class="jobsearch-priceplane-section">
                                                        <?php
                                                        if ($pkg_type == 'paid') {
                                                            echo '<span>' . jobsearch_get_price_format($pkg_price) . ' <small>' . esc_html__('only', 'wp-jobsearch') . '</small></span>';
                                                        } else {
                                                            echo '<span>' . esc_html__('Free', 'wp-jobsearch') . '</span>';
                                                        }
                                                        ?>
                                                    </div>
                                                    <div class="grab-classic-priceplane">
                                                        <ul>
                                                            <?php
                                                            if (!empty($pkg_exfield_title)) {
                                                                $_exf_counter = 0;
                                                                foreach ($pkg_exfield_title as $_exfield_title) {
                                                                    $_exfield_val = isset($pkg_exfield_val[$_exf_counter]) ? $pkg_exfield_val[$_exf_counter] : '';
                                                                    $_exfield_status = isset($pkg_exfield_status[$_exf_counter]) ? $pkg_exfield_status[$_exf_counter] : '';
                                                                    if ($_exfield_val != '') {
                                                                        ?>
                                                                        <li<?php echo($_exfield_status == 'active' ? ' class="active"' : '') ?>>
                                                                            <i class="jobsearch-icon jobsearch-check-square"></i> <?php echo $_exfield_title . ' ' . $_exfield_val ?>
                                                                        </li>
                                                                        <?php
                                                                    }
                                                                    $_exf_counter++;
                                                                }
                                                            }
                                                            ?>
                                                        </ul>
                                                        <?php if (is_user_logged_in()) { ?>
                                                            <a href="javascript:void(0);"
                                                               class="jobsearch-classic-priceplane-btn jobsearch-subscribe-cv-pkg"
                                                               data-id="<?php echo($cv_pkg_id) ?>"><?php esc_html_e('Get Started', 'wp-jobsearch') ?> </a>
                                                            <span class="pkg-loding-msg" style="display:none;"></span>
                                                        <?php } else { ?>
                                                            <a href="javascript:void(0);"
                                                               class="jobsearch-classic-priceplane-btn jobsearch-open-signin-tab"><?php esc_html_e('Get Started', 'wp-jobsearch') ?> </a>
                                                        <?php } ?>
                                                    </div>
                                                </div>
                                            </div>
                                            <?php
                                        }
                                    }
                                    ?>
                                </div>
                                <?php
                                $pkgs_html = ob_get_clean();
                                echo apply_filters('jobsearch_restrict_candidate_pakgs_html', $pkgs_html, $restrict_cv_pckgs);
                                ?>
                            </div>
                            <?php
                        }
                        ?>
                    </div>
                    <?php
                } else {


                    // aici vine candidatul


                    // date canndidat
                    $sectors_enable_switch = isset($jobsearch_plugin_options['sectors_onoff_switch']) ? $jobsearch_plugin_options['sectors_onoff_switch'] : '';
                    $cand_dob_switch = isset($jobsearch_plugin_options['cand_dob_switch']) ? $jobsearch_plugin_options['cand_dob_switch'] : 'on';
                    $candidate_age = jobsearch_candidate_age($candidate_id);
                    $candidate_salary_switch = isset($jobsearch_plugin_options['cand_salary_switch']) ? $jobsearch_plugin_options['cand_salary_switch'] : 'on';
                    $candidate_salary = jobsearch_candidate_current_salary($candidate_id);
                    $sectors = wp_get_post_terms($candidate_id, 'sector');
                    $candidate_sector = isset($sectors[0]->name) ? $sectors[0]->name : '';


                    // date cv din aplicatie
                    $profil_candidat = get_post_meta($job_id, 'jobsearch_job_profil_atasat', true);

                    // get selected profile
                    $profil = $profil_candidat[$candidate_id];


                 // commentat    print_r($profil);
                    $nume = $profil['nume'];
                    $prenume = $profil['prenume'];
                    $cuvleg = $profil['particula'];
                    $titlu_academic = $profil['titlu'];
                    $formula_adresare = $profil['formula_adresare'];

                    $nume_complet = "$titlu_academic $prenume $cuvleg $nume ($formula_adresare)";
                    $nickname = $profil['nickaname'];

                    $anul_nasterii = $profil['anul_nasterii'];

                    $varsta = date('Y') - $anul_nasterii;
                    $sex = $profil['sex'];
                    $resedinta = $profil['judet_candidat'];


                    // educatie
                    $educatie = $profil['educatie'];  //array

                    $calificari = $profil['certificare'];  //text

                    $engleza = $profil['limba_engleza']; // array

                    $alte_limbi = $profil['limba_nivel'];

                    $relevant_skils = $profil['relevant_skils'];

                    $linkuri = $profil['link-uri']; //array

                    $salariu = $profil['salariu_minim_accepta'];

                    $experienta = $profil['experinta_full'];

                    // companie // post (post, de_la, pana_la, ierarhie, descriere_job, alte_detalii)

                    $comentarii_libere = $profil['note_comentarii'];

                    $alte_cerinte = $profil['alte_cerinte'];

                    $imagine_profil = $profil['imagine_profil'];

                    ?>

                    <aside class="jobsearch-column-3 jobsearch-typo-wrap">
                        <div class="widget widget_candidate_info">
                            <div class="jobsearch_candidate_info">
                                <div class="wrapAvatar">
                                    <img src="<?= wp_get_attachment_url($imagine_profil); ?>"/>
                                </div>
                                <div class="datePersonale">
                                    <p>Varsta: <?= date("Y") - $profil['anul_nasterii']; ?> ani</p>
                                    <p>Sex: <?= $profil['sex']; ?></p>
                                    <p>Nickname: <?= $profil['nickaname']; ?></p>
                                    <p>Resedinta: <?= $profil['judet_candidat']; ?></p>
                                    <p><strong>Link-uri</strong>:<br>
                                        <?php
                                        foreach ($profil['link-uri'] as $link => $key): ?>

                                            <a href="<?= $key; ?>"><?= $link; ?></a><br/>
                                        <?php endforeach;
                                        ?>
                                    </p>
                                    <p><strong><?php esc_html_e('Fisiere atasate'); ?></strong></p>



                                            <?php
                                            $get_job_files_attached = get_post_meta($job_id, 'jobsearch_job_files_attached', true);
                                            $atached_files = json_decode($get_job_files_attached[$candidate_id], true);

                                            foreach ($atached_files as $file) { ?>

                                                    <p><a href="<?= $file['link'] ?>" download><?= $file['nume']; ?></a></p>


                                            <?php }
                                            $fisier_nou = get_post_meta($job_id, 'jobsearch_job_file_new', true);
                                            foreach ($fisier_nou as $file) {
                                                $fileN = pathinfo($file); ?>
                                                    <p><a href="<?= $file ?>" download><?= $fileN['filename']; ?></a></p>
                                            <?php }
                                            ?>




                                </div>

                            </div>
                        </div>


                    </aside>
                    <div id="profil-candidat" class="jobsearch-column-7 jobsearch-typo-wrap">
                        <div class="container-wrapper">

                            <h1 class="numecandidat"><?= $profil['prenume'] . ' ' . $profil['particula'] . ' ' . $profil['nume']; ?></h1>

                                                        <div class="zonaCandidat" id="scrisoare-intentie">
                                                            <h2><?php esc_html_e('Scrisoare de intentie', 'managero'); ?></h2>
                            								<?php if ( $job_id = $_GET['job_id'] ):
                            									$get_job_text_attached = get_post_meta( $job_id, 'jobsearch_job_text_attached', true );
                            									echo $get_job_text_attached[ $candidate_id ];
                            								endif; ?>

                                                        </div>


                            <div class="zonaCandidat" id="educatie">
                                <h2><?php esc_html_e('Educatie', 'managero'); ?></h2>
                                <ul style="padding-left: 20px; list-style: square; "><?php
                                    foreach ($profil['educatie'] as $perioada):
                                        echo '<li>' . $perioada . '</li>';
                                    endforeach; ?>
                                </ul>
                            </div>

                            <div class="zonaCandidat" id="certificari">
                                <h2><?php esc_html_e('Certificari', 'managero'); ?></h2>
                                <?php foreach ($profil['certificare'] as $certificare):
                                    echo $certificare . '<br />';
                                endforeach; ?>
                            </div>

                            <div class="zonaCandidat" id="limbi">
                                <h2><?php esc_html_e('Limbi straine', 'managero'); ?></h2>
                                Engleza - <?= $profil['limba_engleza']; ?><br/>
                                <?php foreach ($profil['limba_nivel'] as $limba):
                                    echo $limba . '<br />';
                                endforeach; ?>
                            </div>

                            <div class="zonaCandidat" id="experienta">
                                <h2><?php esc_html_e('Experienta', 'managero'); ?></h2>
                                <ul>
                                    <?php
                                    foreach ($profil['experinta_full'] as $experientaS): ?>

                                        <li><span class="tcomapnie"><?= $experientaS['nume_companie']; ?></span>
                                            <ul class="posturi-companie">
                                                <?php foreach ($experientaS['post'] as $postx): ?>
                                                    <li>
                                                        <span><?= $postx['data_la'] ?></span> -
                                                        <span><?= $postx['pana_la'] ?></span><br/>
                                                        <strong><span><?= $postx['post'] ?></span>,
                                                            <span><?= $postx['ierarhie'] ?></span></strong> <br/>
                                                        <span><?= $postx['descriere_job'] ?></span><br/>
                                                        <span><?= $postx['alte_detalii'] ?></span><br/>

                                                    </li>
                                                <?php endforeach; ?>
                                            </ul>
                                        </li>
                                    <?php endforeach;
                                    ?>
                                </ul>

                            </div>
                            <div class="zonaCandidat" id="competente">
                                <h2><?php esc_html_e('Competente relevante', 'managero'); ?></h2>
                                <?= $profil['relevant_skils']; ?>
                            </div>

                            <div class="zonaCandidat" id="cerinte">
                                <h2><?php esc_html_e('Cerinte', 'managero'); ?></h2>
                                <b>Salariu: <?= $profil['salariu_minim_accepta']; ?>&euro;</b> <br/>
                                <?= $profil['alte_cerinte']; ?>
                            </div>

                            <div class="zonaCandidat" id="brief">
                                <h2><?php esc_html_e('Note si comentarii libere', 'managero'); ?></h2>
                                <?= $profil['note_comentarii']; ?>

                            </div>

                            <div id="scm-candidat-preview" >
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



                        </div>
                    </div>


                    <!--                    end date job -->

                    <div class="jobsearch-column-2">
                    <?php

                    if ($action == 'preview_app') { ?>

                        <a class="sterge-aplicatia" href="javascript:void(0);">
                            Sterge aplicatia
                        </a>

                    <?php } else {
                        $job_applicants_list = get_post_meta($job_id, 'jobsearch_job_applicants_list', true);
                        // print_r($job_applicants_list);
                        $job_applicants_list = jobsearch_is_post_ids_array($job_applicants_list, 'candidate');
                        if (empty($job_applicants_list)) {
                            $job_applicants_list = array();
                        }

                        $job_applicants_count = !empty($job_applicants_list) ? count($job_applicants_list) : 0;

                        $viewed_candidates = get_post_meta($job_id, 'jobsearch_viewed_candidates', true);
                        if (empty($viewed_candidates)) {
                            $viewed_candidates = array();
                        }
                        $viewed_candidates = jobsearch_is_post_ids_array($viewed_candidates, 'candidate');

                        $job_short_int_list = get_post_meta($job_id, '_job_short_interview_list', true);
                        $job_short_int_list = $job_short_int_list != '' ? explode(',', $job_short_int_list) : '';
                        if (empty($job_short_int_list)) {
                            $job_short_int_list = array();
                        }
                        $job_short_int_list = jobsearch_is_post_ids_array($job_short_int_list, 'candidate');
                        $job_short_int_list_c = !empty($job_short_int_list) ? count($job_short_int_list) : 0;


                        $job_reject_int_list = get_post_meta($job_id, '_job_reject_interview_list', true);
                        $job_reject_int_list = $job_reject_int_list != '' ? explode(',', $job_reject_int_list) : '';
                        if (empty($job_reject_int_list)) {
                            $job_reject_int_list = array();
                        }
                        $job_reject_int_list = jobsearch_is_post_ids_array($job_reject_int_list, 'candidate');
                        $job_reject_int_list_c = !empty($job_reject_int_list) ? count($job_reject_int_list) : 0;


                        $job_reserved_int_list = get_post_meta($job_id, '_job_reserved_interview_list', true);
                        $job_reserved_int_list = $job_reserved_int_list != '' ? explode(',', $job_reserved_int_list) : '';
                        if (empty($job_reserved_int_list)) {
                            $job_reserved_int_list = array();
                        }
                        $job_reserved_int_list = jobsearch_is_post_ids_array($job_reserved_int_list, 'candidate');
                        $job_reserved_int_list_c = !empty($job_reserved_int_list) ? count($job_reserved_int_list) : 0;
                        ?>

                        <?php if (in_array($candidate_id, $job_short_int_list)) { ?>


                        <?php } else { ?>
                            <a href="javascript:void(0);"
                               class="shortlist-cand-to-intrview ajax-enable companie-icons"
                               data-toggle="tooltip"
                               data-jid="<?php echo absint($job_id); ?>"
                               data-cid="<?php echo absint($candidate_id); ?>"
                               title="Adauga la candidati ok">
                                <i class="fa fa-check"></i>
                                <span class="app-loader"></span>
                            </a>

                        <?php }
                        if (in_array($candidate_id, $job_reserved_int_list)) { ?>

                        <?php } else { ?>

                            <a href="javascript:void(0);"
                               class="reserved-cand-to-intrview ajax-enable companie-icons"
                               data-toggle="tooltip"
                               data-jid="<?php echo absint($_job_id); ?>"
                               data-cid="<?php echo absint($_candidate_id); ?>"
                               title="Adauga la candidati rezervati">
                                <i class="fa fa-history"></i>
                                <span class="app-loader"></span></a>

                        <?php }
                        if (in_array($candidate_id, $job_reject_int_list)) { ?>


                        <?php } else { ?>

                            <a href="javascript:void(0);"
                               class="reject-cand-to-intrview ajax-enable companie-icons"
                               data-toggle="tooltip"
                               data-jid="<?php echo absint($_job_id); ?>"
                               data-cid="<?php echo absint($candidate_id); ?>"
                               title="Adauga la lista de candidati respinsi">
                                <i class="fa fa-close"></i>
                                <span class="app-loader"></span></a>

                        <?php }
                    ; ?>

                        <a href="javascript:void(0);"
                           class="delete-cand-from-job ajax-enable companie-icons"
                           data-toggle="tooltip"
                           data-jid="<?php echo absint($_job_id); ?>"
                           data-cid="<?php echo absint($candidate_id); ?>"
                           title="Sterge candidatul din liste">
                            <i class="fa fa-trash"></i>
                            <span class="app-loader"></span></a>


                        <?php
                        $send_message_form_rand = rand(100000, 999999);
                        $app_actbtns_html = ob_get_clean();
                        $companie_id = $_GET['employer_id'];
                        echo apply_filters('jobseacrh_dash_manag_apps_actbtns_html', $app_actbtns_html, $candidate_id, $_job_id, $companie_id, $send_message_form_rand);
                        ?>


                        </div>
                        <?php
                    }
                }
                ?>
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

</style>



