<?php
/**
 * Template Name: Homepage
 */

get_header();
?>

    <section id="home-top">
        <div class="home-despre" style="background-image: url('<?= the_field("background_image") ?>')">
            <div class="container">
                <div class="textWrap_head">

                    <h1><?php the_field('home_despre_title'); ?></h1>
                    <div class="home-despre-text">
                        <?php the_field('home_despre_text'); ?>
                    </div>
                    <a href="<?php the_field('home_despre_link'); ?>"><?php esc_html_e('Afla mai multe', 'managero'); ?></a>
                </div>
            </div>
        </div>


    </section>

    <section class="home-articole">
        <div class="container container-home">
            <h1><?php esc_html_e('Articole recente', 'managero'); ?></h1>
            <div class="row" id="recentPosts">

                <?php
                $args = [
                    'post_type' => 'post',
                    'posts_per_page' => 5,
                    'order' => 'DESC'
                ];
                $recentArticle = new WP_Query($args);
                if ($recentArticle->have_posts()):while ($recentArticle->have_posts()):$recentArticle->the_post(); ?>

                    <div class="home-articol-wrap">
                        <img src="<?php the_post_thumbnail_url(); ?>"/>
                        <h2><?php the_title(); ?></h2>
                        <a href="<?php get_the_permalink(); ?>"><?php esc_html_e('mai mult', 'managero'); ?></a>
                    </div>

                <?php endwhile;
                    wp_reset_query();
                else :
                    echo "<h1>Nu, exista articole recente!</h1>";
                endif;
                ?>

            </div>
        </div>
    </section>


    <section class="home-joburi">
        <div class="container container-joburi">
            <h1><?php esc_html_e('Joburi recente', 'managero'); ?></h1>
            <div class="row" id="recentJobs">

                <?php $args = [
                    'post_type' => 'job',
                    'posts_per_page' => 12,
                    'order' => 'DESC'
                ];

                if (is_user_logged_in()):
                    $user_id = get_current_user_id();
                    $user_obj = get_user_by('ID', $user_id);
                    $candidate_id = jobsearch_get_user_candidate_id($user_id);


                    //	var_dump($candidate_id);

                    $lista_joburi_excluse = '';

                    $candidate_rej_jobs_list = get_post_meta($candidate_id, 'jobsearch_rej_jobs_list', true);
                    $candidate_fav_jobs_list = get_post_meta($candidate_id, 'jobsearch_fav_jobs_list', true);
                    $candidate_apd_jobs_list = get_post_meta($candidate_id, 'jobsearch_apd_jobs_list', true);



//  pt viitor
//		$candidate_fav_jobs_list = array($candidate_fav_jobs_list);
//		$candidate_apd_jobs_list = array($candidate_apd_jobs_list);
//		$candidate_rej_jobs_list = array($candidate_rej_jobs_list);
//
//		$lista_joburi_excluse = array_merge($candidate_rej_jobs_list, $candidate_apd_jobs_list, $candidate_fav_jobs_list);
//
//
//		var_dump($lista_joburi_excluse);
//
//		if ( $candidate_id ):
//			$args['post__not-in'] = 22 ;
//		endif;

                endif;

                $recentJoburi = new WP_Query($args);
                if ($recentJoburi->have_posts()):
                    while ($recentJoburi->have_posts()):$recentJoburi->the_post();
                        $job_id = get_the_ID();


                        if (is_user_logged_in()) {
                            $user_id = get_current_user_id();
                            $user_obj = get_user_by('ID', $user_id);
                            $candidate_id = jobsearch_get_user_candidate_id($user_id);

                            if ($candidate_id > 0) {

                                $candidate_rej_jobs_list = get_post_meta($candidate_id, 'jobsearch_rej_jobs_list', true);
                                $candidate_fav_jobs_list = get_post_meta($candidate_id, 'jobsearch_fav_jobs_list', true);
                                $candidate_apd_jobs_list = get_post_meta($candidate_id, 'jobsearch_apd_jobs_list', true);


                                if ($candidate_rej_jobs_list != '') {
                                    $candidate_rej_jobs_list = explode(',', $candidate_rej_jobs_list);
                                    if (in_array($job_id, $candidate_rej_jobs_list)) {
                                        continue;
                                    }
                                }

                                if ($candidate_apd_jobs_list != '') {
                                    $candidate_apd_jobs_list = explode(',', $candidate_apd_jobs_list);
                                    if (in_array($job_id, $candidate_apd_jobs_list)) {
                                        continue;
                                    }
                                }

                                if ($candidate_fav_jobs_list != '') {
                                    $candidate_fav_jobs_list = explode(',', $candidate_fav_jobs_list);
                                    if (in_array($job_id, $candidate_fav_jobs_list)) {
                                        continue;
                                    }
                                }
                            }
                            $finded_result_list = jobsearch_find_index_user_meta_list($job_id, 'jobsearch-user-jobs-applied-list', 'post_id', jobsearch_get_user_id());
                            if (is_array($finded_result_list) && !empty($finded_result_list)) {
                                continue;
                            }
                        }
                        $job_id = get_the_ID();


                        $job_id = get_the_ID();
                        $post_id = $job_id;

                        // get job user
                        $companie_id = get_the_author_meta('ID');


                        // returneaza date job
                        $date_job = get_post_meta($post_id, 'job_data_set', true);
                        $imagineCompanie = $date_job['imagine_profil_companie'];

                        ?>


                        <a href="<?php the_permalink(); ?>">
                            <div class="home-job-wrap">
                                <div class="wrap_img_job">
                                    <div class="wrapper_img">
                                        <img src="<?= wp_get_attachment_url($imagineCompanie); ?>"/>
                                    </div>
                                </div>
                                <h3><?= $date_job['nume_companie']; ?></h3>
                                <div class="job-wrapper-title">
                                    <h4><?= $date_job['post_job']; ?></h4>
                                </div>
                                <p><?= $date_job['locatia_jobului'][1] ?></p>
                                <p><?php esc_html_e('salariu', 'managero') ?> :
                                    <?= $date_job['salariu_minim_scm']; ?>&euro;</p>
                            </div>
                        </a>

                    <?php endwhile;
                else :
                    echo "<h1>Nu, exista joburi recente!</h1>";
                endif;
                ?>

            </div>

        </div>
    </section>


    <script>
        jQuery('#recentPosts').slick({
            arrow: true,
            dots: false,
            infinite: true,
            speed: 300,
            slidesToShow: 4,
            slidesToScroll: 4,
            prevArrow: "<div class='next-arrow'><i class=\"fas fa-chevron-left\"></i></div>",
            nextArrow: "<div class='prev-arrow'><i class=\"fas fa-chevron-right\"></i></div>",
            responsive: [
                {
                    breakpoint: 1024,
                    settings: {
                        slidesToShow: 3,
                        slidesToScroll: 3,
                        infinite: true,
                        dots: true
                    }
                },
                {
                    breakpoint: 600,
                    settings: {
                        slidesToShow: 2,
                        slidesToScroll: 2
                    }
                },
                {
                    breakpoint: 480,
                    settings: {
                        slidesToShow: 1,
                        slidesToScroll: 1
                    }
                }
            ]
        });
        jQuery('#recentJobs').slick({
            arrow: true,
            dots: false,
            infinite: true,
            speed: 300,
            slidesToShow: 6,
            slidesToScroll: 6,
            prevArrow: "<div class='next-arrow'><i class=\"fas fa-chevron-left\"></i></div>",
            nextArrow: "<div class='prev-arrow'><i class=\"fas fa-chevron-right\"></i></div>",
            responsive: [
                {
                    breakpoint: 1024,
                    settings: {
                        slidesToShow: 3,
                        slidesToScroll: 3,
                        infinite: true,
                        dots: true
                    }
                },
                {
                    breakpoint: 600,
                    settings: {
                        slidesToShow: 2,
                        slidesToScroll: 2
                    }
                },
                {
                    breakpoint: 480,
                    settings: {
                        slidesToShow: 1,
                        slidesToScroll: 1
                    }
                }
            ]
        });
    </script>

<?php
get_footer();
