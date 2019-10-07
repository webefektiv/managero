<?php
/**
 * Template Name: Jobs
 */
wp_enqueue_style( 'jobsearch-datetimepicker-style' );
wp_enqueue_script( 'jobsearch-datetimepicker-script' );
wp_enqueue_script( 'jquery-ui' );
wp_enqueue_script( 'jobsearch-job-functions-script' );

get_header();
?>


    <div class="container">
        <div class="featimage">
            <img src="<?php the_post_thumbnail_url(); ?>"/>
        </div>
        <div class="row">
            <div class="col-6">
                <h1 class="page-title-top page-title-top-1">
                    <?php esc_html_e( 'LISTA JOBURI', 'managero' ); ?>
                </h1>
            </div>
            <div class="col-6">
                <div class="date-top">
                    <span class="nojobs"></span> joburi neprocesate
                </div>
            </div>
        </div>
    </div>

<?php
while ( have_posts() ) :
	the_post();

	get_template_part( 'template-parts/content', 'jobs' );

endwhile; // End of the loop.
?>

<?php
get_footer();
