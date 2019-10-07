<?php
acf_enqueue_uploader();
acf_form_head();

global $jobsearch_plugin_options, $Jobsearch_User_Dashboard_Settings;
$user_id  = get_current_user_id();
$user_obj = get_user_by( 'ID', $user_id );
$employer_id = jobsearch_get_user_employer_id( $user_id );

$user_id = get_current_user_id();

echo do_shortcode('[librarie_mededia_front_upload]');

if ( $employer_id > 0 ) { ?>
    <div class="jobsearch-employer-box-section">
        <h2 class="job-title"><?php esc_html_e( 'Documente', 'managero' ); ?></h2>

        <div class="file-wrap2">
            <table id="tabel-fisiere">
                <tr>
                    <th>Denumire publica</th>
                    <th>Denumire fisier</th>
                    <th>Format</th>
                    <th>Dimensiune</th>
                    <th>Detalii</th>
                </tr>

				<?php
				$the_query = new WP_Query( array(
					'post_type'      => 'attachment',
					'post_status'    => 'inherit',
					'author'         => $user_id,
					'posts_per_page' => -1,
					'post_mime_type' => array( 'application/doc', 'application/pdf', 'text/plain' ),
				) );
				if ( $the_query->have_posts() ) {
					while ( $the_query->have_posts() ) : $the_query->the_post();
						$url  = wp_get_attachment_url();
						$id   = get_the_ID();
						$size = filesize( get_attached_file( $id ) );
						?>
                        <tr>
                            <td><a href="<?= $url; ?>"
                                   target="_blank"><?= the_title(); ?></a></td>
                            <td><?= wp_basename( $url ); ?></td>
                            <td>
								<?= get_post_mime_type(); ?>
                            </td>
                            <td><?= size_format($size); ?></td>
                            <td><?php the_excerpt(); // the_content();  ?></td>
                        </tr>

						<?php
					endwhile;
				} ?>
            </table>
        </div>

        <div class="mediaWrap">
            <h2 class="job-title"><?php esc_html_e( 'Media', 'managero' ); ?></h2>
            <div class="galerieJob">
			<?php

            $media_crops = get_attached_media( 'image', $employer_id );
            $exlude_img = [];
            foreach($media_crops as $key=>$value){
                $exlude_img[] = $key;
            }

			$supported_mimes  = array(
				'image/jpeg',
				'image/gif',
				'image/png',
				'image/bmp',
				'image/tiff',
				'image/x-icon'
			);
			$attachment_query = new WP_Query( array(
			//	'post_parent' => $employer_id,
				'post_type'      => 'attachment',
				'post_status'    => 'any',
				'post_mime_type' => $supported_mimes,
				'posts_per_page' => -1,
				'author'         => $user_id,
				'post__not_in' => $exlude_img,
			    'exclude'   => $exlude_img
			) );
			if ( $attachment_query->have_posts() ) :
				while ( $attachment_query->have_posts() ) : $attachment_query->the_post();
					 $check = wp_get_attachment_image_src(get_the_ID(),'full');
					 $small = wp_get_attachment_image_src(get_the_ID());
					if (strpos($check[0], 'aspect-ratio') === false) {
					?>
                            <div class="imgGalWrap">
                                    <a class="fancybox"  data-fancybox-group="button" rel="gallery1" href="<?php echo wp_get_attachment_url(); ?>" >
                                        <img class="wrap-img"  src="<?php echo $small[0]; ?>" alt=""/>
                                    </a>
                            </div>

				<?php } endwhile;
			endif; ?>
            </div>
        </div>
    </div>

<style>
    .media-modal-content .media-toolbar-primary .media-button {
       display: none;
    }
</style>


	<?php
}