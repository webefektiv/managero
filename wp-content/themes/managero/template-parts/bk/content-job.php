<?php
global $jobsearch_plugin_options;

$post_id = get_the_ID();
$job_id  = $post_id;


/// companie details
///
$companie_id   = get_post_meta( $job_id, 'jobsearch_field_job_posted_by', true );
$nume_companie    = get_the_title( $companie_id );
$desc_companie = apply_filters( 'the_content', get_post_field( 'post_content', $companie_id ) );




// $logo        = get_the_post_thumbnail_url( $companie_id );
// $companie    = get_the_title( $companie_id );
// $angajati    = get_post_meta( $companie_id, 'nr-agajati', true );


$job_employer_id = $companie_id;


$fisiere2 = get_field( 'fisiere', $companie_id );


$links = get_field( 'link-uri', $companie_id );

$niceLinks = '';

foreach ( $links as $link ):
	$linkt = $link['link'];

	$niceLinkt = str_replace( 'http://', '', $linkt );
	$niceLinkt = str_replace( 'https://', '', $niceLinkt );

	$niceLinks .= "<a href='$linkt' target='_blank'>$niceLinkt</a> | ";

endforeach;

$niceLinks = substr( $niceLinks, 0, - 3 );


//job details


$job = get_the_title();

$adresa  = get_post_meta( $job_id, 'jobsearch_field_location_address', true );
$adresa2 = get_post_meta( $companie_id, 'jobsearch_field_location_location1', true );
$adresa3 = get_post_meta( $companie_id, 'jobsearch_field_location_location2', true );


$nivel = get_post_meta( $job_id, 'nivel-ierarhic', true );

$salariu = get_post_meta( $job_id, 'jobsearch_field_job_salary', true );

$domenii = wp_get_post_terms( $job_id, 'sector' );

$listaDomenii = '';

foreach ( $domenii as $domeniu ) {
	$listaDomenii .= $domeniu->name . ', ';
}

$listaDomenii = substr( $listaDomenii, 0, - 2 );


$subordonat = get_post_meta( $job_id, 'subordonat', true );

$nrsubordonati = get_post_meta( $job_id, 'persone_subordine', true );
$alte_detalii  = get_post_meta( $job_id, 'alte_detalii', true );

$oferta  = get_post_meta( $job_id, 'oferta', true );
$cerinte = get_post_meta( $job_id, 'cerinte', true );


$application_deadline = get_post_meta( $post_id, 'jobsearch_field_job_application_deadline_date', true );
$jobsearch_job_posted = get_post_meta( $post_id, 'jobsearch_field_job_publish_date', true );

$job_apply_email = get_post_meta( $post_id, 'jobsearch_field_job_apply_email', true );
$contact_email   = get_post_meta( $post_id, 'persoana_contact', true );



// date companie noi

$date_companie_visibile = get_post_meta( $post_id, 'date_companie', true );


$desc_companie = get_post_meta( $companie_id, 'descriere_companie', true );
$tara_origine = get_post_meta( $companie_id, 'tara_origine', true );
$extindere = get_post_meta( $companie_id, 'extindere', true );
$website = get_post_meta( $companie_id, 'website', true );
$locarizare_romania = get_post_meta( $companie_id, 'locarizare-romania', true );
$head_office = get_post_meta( $companie_id, 'head-office', true );
$cifra_afaceri_ro = get_post_meta( $companie_id, 'cifra_afaceri_ro', true );
$numar_agajati = get_post_meta( $companie_id, 'numar_agajati', true );

$domeniu_de_activate_larg = get_post_meta( $companie_id, 'domeniu_de_activate_larg', true );
$domeniu_de_activate_sintetic = get_post_meta( $companie_id, 'domeniu_de_activate_sintetic', true );

$persoana_contact = get_post_meta( $companie_id, 'persoana-contact', true );
$telefon_conact = get_post_meta( $companie_id, 'telefon_conact', true );
$email_contact = get_post_meta( $companie_id, 'email_contact', true );




$jobsearch_field_user_facebook_url = get_post_meta( $companie_id, 'jobsearch_field_user_facebook_url', true );
$jobsearch_field_user_linkedin_url = get_post_meta( $companie_id, 'jobsearch_field_user_linkedin_url', true );
$jobsearch_field_location_location1 = get_post_meta( $companie_id, 'jobsearch_field_location_location1', true );
$jobsearch_field_location_location2 = get_post_meta( $companie_id, 'jobsearch_field_location_location2', true );
$jobsearch_field_location_address = get_post_meta( $companie_id, 'jobsearch_field_location_address', true );



?>


<div class="container">
    <div class="featimage">
        <?php  if(get_field('cover_image')): ?>
        <img src="<?php the_field( 'cover_image' ); ?>"/>
            <?php else: ?>
         <img src="http://managero.ro/wp-content/uploads/2019/04/bgheader-1.png"  />
        <?php endif; ?>


    </div>
    <div class="row">
        <div class="col-4">
            <h1 class="page-title-top" style="width: 700px;"><?php the_title(); ?> - <?= $nume_companie; ?> </h1>
        </div>
        <div class="col-8">

        </div>
    </div>
</div>

<div class="bg-white">
    <div class="container">
        <div class="row">
            <div class="col-sm-9">

                <div id="sectiune-companie">
                    <h2 class="job-title"><?php esc_html_e( 'Compania', 'managero' ); ?></h2>


                    <?php if(in_array('descriere', $date_companie_visibile)): ?>
                    <div class="row jobDetails">
                        <div class="col-sm-4"><h3><?php esc_html_e( 'Descriere', 'managero' ); ?></h3></div>
                        <div class="col-sm-8">
							<?= $desc_companie; ?>
                        </div>
                    </div>
                    <?php endif; ?>


	                <?php if(in_array('domeniu_larg', $date_companie_visibile)): ?>
                        <div class="row jobDetails">
                            <div class="col-sm-4"><h3><?php esc_html_e( 'Domeniu de activitate', 'managero' ); ?></h3></div>
                            <div class="col-sm-8">
				                <?= $domeniu_de_activate_larg; ?>
                            </div>
                        </div>
	                <?php endif; ?>

	                <?php if(in_array('domeniu-sintetic', $date_companie_visibile)): ?>
                        <div class="row jobDetails">
                            <div class="col-sm-4"><h3><?php esc_html_e( 'Domeniu de activitate', 'managero' ); ?></h3></div>
                            <div class="col-sm-8">
				                <?= $domeniu_de_activate_sintetic; ?>
                            </div>
                        </div>
	                <?php endif; ?>



                    <?php if(in_array('tara_origine', $date_companie_visibile)): ?>
                    <div class="row jobDetails">
                        <div class="col-sm-3"><h4><?php esc_html_e( 'Tara de origine', 'managero' ); ?></h4></div>
                        <div class="col-sm-3"><?= $tara_origine; ?></div>
                        <div class="col-sm-3"><h4><?php esc_html_e( 'Extindere', 'managero' ); ?></h4></div>
                        <div class="col-sm-3"><?= $extindere; ?></div>
                    </div>
	                <?php endif; ?>

	                <?php if(in_array('localizare', $date_companie_visibile)): ?>
                    <div class="row jobDetails">
                        <div class="col-sm-3"><h4><?php esc_html_e( 'Website', 'managero' ); ?></h4></div>
                        <div class="col-sm-3"><?= $website; ?></div>
                        <div class="col-sm-3"><h4><?php esc_html_e( 'Localizare', 'managero' ); ?></h4></div>
                        <div class="col-sm-3"><?= $website;  ?></div>
                    </div>
	                <?php endif; ?>

	                <?php if(in_array('descriere', $date_companie_visibile)): ?>
                    <div class="row jobDetails">
                        <div class="col-sm-3"><h4><?php esc_html_e( 'Localizare in Romania', 'managero' ); ?></h4></div>
                        <div class="col-sm-3"><?= $locarizare_romania; ?></div>
                        <div class="col-sm-3"><h4><?php esc_html_e( 'Head Office', 'managero' ); ?></h4></div>
                        <div class="col-sm-3"><?= $head_office; ?></div>
                    </div>
	                <?php endif; ?>

	                <?php if(in_array('angajati', $date_companie_visibile)): ?>
                    <div class="row jobDetails">
                        <div class="col-sm-3"><h4><?php esc_html_e( 'Numar angajati', 'managero' ); ?></h4></div>
                        <div class="col-sm-3"><?= $numar_agajati; ?></div>
                        <div class="col-sm-3"><h4><?php esc_html_e( 'Persoana de contact', 'managero' ); ?></h4></div>
                        <div class="col-sm-3"><?= $persoana_contact; ?></div>
                    </div>
	                <?php endif; ?>

	                <?php if(in_array('job_telefon', $date_companie_visibile)): ?>
                    <div class="row jobDetails">
                        <div class="col-sm-3"><h4><?php esc_html_e( 'Telefon', 'managero' ); ?></h4></div>
                        <div class="col-sm-3"><?= $telefon_conact; ?></div>
                        <div class="col-sm-3"><h4><?php esc_html_e( 'Email', 'managero' ); ?></h4></div>
                        <div class="col-sm-3"><?= $email_contact; ?></div>
                    </div>
	                <?php endif; ?>




                </div>

                <div id="sectiune-job">
                    <h2 class="job-title"><?php esc_html_e( 'Jobul', 'managero' ); ?></h2>
                    <div class="row jobDetails">
                        <div class="col-sm-4"><h3><?php esc_html_e( 'Descriere' ); ?></h3></div>
                        <div class="col-sm-8">
							<?php the_content(); ?>
                        </div>
                    </div>
                    <div class="row jobDetails">
                        <div class="col-sm-4"><h3><?php esc_html_e( 'Pozitie ierarhica', 'managero' ); ?></h3></div>
                        <div class="col-sm-8">
							<?= $nivel; ?>
                        </div>
                    </div>
                    <div class="row jobDetails">
                        <div class="col-sm-4"><h3><?php esc_html_e( 'Persoane in subordine', 'managero' ); ?></h3></div>
                        <div class="col-sm-8"><?= $nrsubordonati; ?></div>
                    </div>
                    <div class="row jobDetails">
                        <div class="col-sm-4"><h3><?php esc_html_e( 'Subordonat fata de', 'managero' ); ?></h3></div>
                        <div class="col-sm-8"><?= $subordonat; ?></div>
                    </div>

                    <div class="row jobDetails">
                        <div class="col-sm-4"><h3><?php esc_html_e( 'Alte detalii', 'managero' ); ?></h3></div>
                        <div class="col-sm-8"><?= $alte_detalii; ?></div>
                    </div>
                </div>

                <div id="sectiune-oferta">
                    <h2 class="job-title"><?php esc_html_e( 'CERINȚE', 'managero' ); ?></h2>
                    <div class="row jobDetails">
                        <div class="col-sm-4"></div>
                        <div class="col-sm-8"><?= $cerinte; ?></div>
                    </div>
                </div>
                <div id="sectiune-cerinta">
                    <h2 class="job-title"><?php esc_html_e( 'OFERTĂ', 'managero' ); ?></h2>
                    <div class="row jobDetails">
                        <div class="col-sm-4"></div>
                        <div class="col-sm-8"><?= $oferta; ?></div>
                    </div>
                    <div class="row jobDetails">
                        <div class="col-sm-4"><h3><?php esc_html_e( 'Salariu', 'managero' ); ?></h3></div>
                        <div class="col-sm-8"><?= $salariu; ?>&euro;</div>
                    </div>
                    <div class="row jobDetails">
                        <div class="col-sm-4"><h3><?php esc_html_e( 'Alte detalii', 'managero' ); ?></h3></div>
                        <div class="col-sm-8"><?= $alte_detalii; ?>&euro;</div>
                    </div>
                </div>


            </div>
            <div class="col-sm-3">

				<?php
				$email = [
					'email'   => $job_apply_email,
					'contact' => $contact_email
				];

				$args   = [
					'classes'           => 'jobsearch-applyjob-btn',
					'btn_before_label'  => esc_html__( 'Managero', 'wp-jobsearch' ),
					'btn_after_label'   => esc_html__( 'Aplicat', 'wp-jobsearch' ),
					'btn_applied_label' => esc_html__( 'Ai aplicat', 'wp-jobsearch' ),
					'job_id'            => $job_id
				];
				$widget = true;

				the_apply_button( $job_id, $application_deadline, $job_employer_id, $args, $widget, $email ) ?>
            </div>
        </div>

    </div>
</div>

<script>
    //for login popup
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
