<div id="dashboard-candidati-wrapper" class="col-12">

    <div class="wrapper-dashboard-profile">
        <a href="/user-dashboard/?tab=profil-candidat">
            <div class="image-dashboard-profile">
                <img src="<?php echo get_template_directory_uri() ?>/assets/profilul-meu.png" alt=""/>
            </div>
            <div class="text-wrapper-profile-dasboard">
                <h4>Profil candidat</h4>
            </div>
        </a>

    </div>
    <div class="wrapper-dashboard-profile">
        <a href="/user-dashboard/?tab=all-jobs">
            <div class="image-dashboard-profile">
                <img src="<?php echo get_template_directory_uri() ?>/assets/lista-joburi.png" alt=""/>
            </div>
            <div class="text-wrapper-profile-dasboard">
                <h4>Lista joburi</h4>
            </div>
        </a>
    </div>
    <div class="wrapper-dashboard-profile">
        <a href="/user-dashboard/?tab=aplicatii">
            <div class="image-dashboard-profile">
                <img src="<?php echo get_template_directory_uri() ?>/assets/lista-aplicatii.png" alt=""/>
            </div>
            <div class="text-wrapper-profile-dasboard">
                <h4>Aplicatiile mele</h4>
            </div>
        </a>
    </div>
    <div class="wrapper-dashboard-profile">
        <a href="/user-dashboard/?tab=fisiere">
            <div class="image-dashboard-profile">
                <img src="<?php echo get_template_directory_uri() ?>/assets/lista-aplicatii.png" alt=""/>
            </div>
            <div class="text-wrapper-profile-dasboard">
                <h4>Texte predifinite</h4>
            </div>
        </a>
    </div>
    <div class="wrapper-dashboard-profile">
        <a href="/user-dashboard/?tab=fisiere">
            <div class="image-dashboard-profile">
                <img src="<?php echo get_template_directory_uri() ?>/assets/texte_si_fisiere.png" alt=""/>
            </div>
            <div class="text-wrapper-profile-dasboard">
                <h4>Fisiere candidat</h4>
            </div>
        </a>
    </div>

    <div class="wrapper-dashboard-profile">
        <a href="/user-dashboard/?tab=alerte-joburi">
            <div class="image-dashboard-profile">
                <img src="<?php echo get_template_directory_uri() ?>/assets/alerte-joburi.png" alt=""/>
            </div>
            <div class="text-wrapper-profile-dasboard">
                <h4>Alerte si filtre</h4>
            </div>
        </a>
    </div>

    <div class="wrapper-dashboard-profile">
        <div class="image-dashboard-profile">
            <img src="<?php echo get_template_directory_uri() ?>/assets/setari-cont.png" alt=""/>
        </div>
        <div class="text-wrapper-profile-dasboard">
            <h4>Setari cont</h4>
        </div>
    </div>
</div>
<?php
$user_id      = get_current_user_id();
$user_obj     = get_user_by( 'ID', $user_id );
$candidate_id = jobsearch_get_user_candidate_id( $user_id );



$the_query = new WP_Query( array(
	'post_type'      => 'attachment',
	'post_status'    => 'inherit',
	'author'         => $user_id,
	'posts_per_page' => -1,
	'post_mime_type' => array( 'application/doc', 'application/pdf', 'text/plain' ),
) );
$fisiere = 0;
if ( $the_query->have_posts() ) {
	while ( $the_query->have_posts() ) : $the_query->the_post();
		$fisiere++;
	endwhile;
}


$texte = get_post_meta( $candidate_id, "candidat_texte_predefinite", true );
//		print_r( $templates );
$texte = unserialize(base64_decode($texte));




$alerte  = 0;

$filtre = get_post_meta($candidate_id, "filtre_candidat", true);
//     	print_r( $templates );
$filtre = unserialize(base64_decode($filtre));


$candidate_fav_jobs_liste = get_post_meta( $candidate_id, 'jobsearch_fav_jobs_list', true );
$candidate_fav_jobs_liste = $candidate_fav_jobs_liste != '' ? explode( ',', $candidate_fav_jobs_liste ) : array();


$candidate_rej_jobs_liste = get_post_meta($candidate_id, 'jobsearch_rej_jobs_list', true);

// print_r($candidate_rej_jobs_liste);

$candidate_apd_jobs_liste = get_post_meta( $candidate_id, 'jobsearch_apd_jobs_list', true );
$candidate_apd_jobs_liste = $candidate_apd_jobs_liste != '' ? explode( ',', $candidate_apd_jobs_liste ) : array();



$aplicatii = get_field( 'alte_aplicatii', $candidate_id );




// print_r($candidate_apd_jobs_liste);

$user_applied_jobs_list  = array();
$user_applied_jobs_liste = get_user_meta( $candidate_id, 'jobsearch-user-jobs-applied-list', true );

//$user_applied_jobs_liste = get_user_meta( $user_id, 'jobsearch-user-jobs-applied-list', true );

$stats = [];

$stats['fisiere'] = $fisiere ;
$stats['texte']   = count( $texte );
$stats['alerte']  = count( $alerte );
$stats['filtre']  = count( $filtre );

$stats['fav']     = count( $candidate_fav_jobs_liste );

$stats['rej']     = count( $candidate_rej_jobs_liste );

$stats['app']     = count( $user_applied_jobs_liste );

$stats['apd']     = count( $candidate_apd_jobs_liste );

$stats['alte'] = count($aplicatii);


?>
<div id="box-bottom" class="col-12">
    <div class="wapper-boxuri-bottom">
            <h6 class="info-boxuri">texte<br>predefinite</h6>
            <p class="large-info-about"><span><?= $stats['texte']; ?></span></p>
    </div>
    <div class="wapper-boxuri-bottom">
            <h6 class="info-boxuri">fisiere<br>incarcate</h6>
            <p class="large-info-about"><span><?= $stats['fisiere']; ?></span></p>
    </div>
    <div class="wapper-boxuri-bottom">
            <h6 class="info-boxuri">filtre<br>predefinite</h6>
            <p class="large-info-about"><span><?= $stats['filtre']; ?></span></p>
    </div>
    <div class="wapper-boxuri-bottom">
            <h6 class="info-boxuri">alerte<br>joburi</h6>
            <p class="large-info-about"><span><?= $stats['alerte']; ?></span></p>
    </div>
    <div class="wapper-boxuri-bottom">
            <h6 class="info-boxuri">joburi<br>de aplicat</h6>
            <p class="large-info-about"><span><?= $stats['fav']; ?></span></p>
    </div>
    <div class="wapper-boxuri-bottom">
            <h6 class="info-boxuri">aplicatii<br>joburi</h6>
            <p class="large-info-about"><span><?= $stats['app']; ?></span></p>
    </div>
    <div class="wapper-boxuri-bottom">
        <h6 class="info-boxuri">joburi<br>neinteresante</h6>
        <p class="large-info-about"><span><?= $stats['rej']; ?></span></p>
    </div>
</div>
<div class="left-profile-user-dashboard">
	<?php
	$data = get_the_date( 'd F Y', $candidate_id ); ?>
    <p class="aplicatii">Aplicatii(incepand cu data de <?= $data; ?>)</p>
    <span class="strong"><?= $stats['app']; ?></span>
</div>
<div class="right-profile-user-dashboard">
    <table class="right-table">
        <tr>
            <th>Joburile Managero</th>
            <th>prin sistem <b><?= $stats['app']; ?></b></th>
            <th>prin e-mail <b><?= $stats['apd'] ?></b></th>
        </tr>
        <tr>
            <td><b>Alte joburi</b></td>
            <td><?= count($aplicatii) ?></td>
            <td></td>
        </tr>
    </table>
</div>
