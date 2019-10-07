<?php
/**
 * managero functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package managero
 */

if ( ! function_exists( 'managero_setup' ) ) :
	/**
	 * Sets up theme defaults and registers support for various WordPress features.
	 *
	 * Note that this function is hooked into the after_setup_theme hook, which
	 * runs before the init hook. The init hook is too late for some features, such
	 * as indicating support for post thumbnails.
	 */
	function managero_setup() {
		/*
		 * Make theme available for translation.
		 * Translations can be filed in the /languages/ directory.
		 * If you're building a theme based on managero, use a find and replace
		 * to change 'managero' to the name of your theme in all the template files.
		 */
		load_theme_textdomain( 'managero', get_template_directory() . '/languages' );

		// Add default posts and comments RSS feed links to head.
		add_theme_support( 'automatic-feed-links' );

		/*
		 * Let WordPress manage the document title.
		 * By adding theme support, we declare that this theme does not use a
		 * hard-coded <title> tag in the document head, and expect WordPress to
		 * provide it for us.
		 */
		add_theme_support( 'title-tag' );

		/*
		 * Enable support for Post Thumbnails on posts and pages.
		 *
		 * @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
		 */
		add_theme_support( 'post-thumbnails' );

		// This theme uses wp_nav_menu() in one location.
		register_nav_menus( array(
			'menu-1' => esc_html__( 'Primary', 'managero' ),
		) );

		/*
		 * Switch default core markup for search form, comment form, and comments
		 * to output valid HTML5.
		 */
		add_theme_support( 'html5', array(
			'search-form',
			'comment-form',
			'comment-list',
			'gallery',
			'caption',
		) );

		// Set up the WordPress core custom background feature.
		add_theme_support( 'custom-background', apply_filters( 'managero_custom_background_args', array(
			'default-color' => 'ffffff',
			'default-image' => '',
		) ) );

		// Add theme support for selective refresh for widgets.
		add_theme_support( 'customize-selective-refresh-widgets' );

		/**
		 * Add support for core custom logo.
		 *
		 * @link https://codex.wordpress.org/Theme_Logo
		 */
		add_theme_support( 'custom-logo', array(
			'height'      => 250,
			'width'       => 250,
			'flex-width'  => true,
			'flex-height' => true,
		) );
	}
endif;
add_action( 'after_setup_theme', 'managero_setup' );

/**
 * Set the content width in pixels, based on the theme's design and stylesheet.
 *
 * Priority 0 to make it available to lower priority callbacks.
 *
 * @global int $content_width
 */
function managero_content_width() {
	// This variable is intended to be overruled from themes.
	// Open WPCS issue: {@link https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards/issues/1043}.
	// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
	$GLOBALS['content_width'] = apply_filters( 'managero_content_width', 640 );
}

add_action( 'after_setup_theme', 'managero_content_width', 0 );

/**
 * Register widget area.
 *
 * @link https://developer.wordpress.org/themes/functionality/sidebars/#registering-a-sidebar
 */
function managero_widgets_init() {
	register_sidebar( array(
		'name'          => esc_html__( 'Sidebar', 'managero' ),
		'id'            => 'sidebar-1',
		'description'   => esc_html__( 'Add widgets here.', 'managero' ),
		'before_widget' => '<section id="%1$s" class="widget %2$s">',
		'after_widget'  => '</section>',
		'before_title'  => '<h2 class="widget-title">',
		'after_title'   => '</h2>',
	) );
}

add_action( 'widgets_init', 'managero_widgets_init' );

/**
 * Enqueue scripts and styles.
 */
function managero_scripts() {
	wp_enqueue_style( 'managero-style', get_template_directory_uri() . '/sass/style.css' );
//    wp_enqueue_style( 'managero-style-1', get_template_directory_uri() . '/assets/home1.css' );
	wp_enqueue_script( 'managero-navigation', get_template_directory_uri() . '/js/navigation.js', array(), '20151215', true );

	wp_enqueue_script( 'managero-skip-link-focus-fix', get_template_directory_uri() . '/js/skip-link-focus-fix.js', array(), '20151215', true );

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}

add_action( 'wp_enqueue_scripts', 'managero_scripts' );

/**
 * Implement the Custom Header feature.
 */
require get_template_directory() . '/inc/custom-header.php';

/**
 * Custom template tags for this theme.
 */
require get_template_directory() . '/inc/template-tags.php';

/**
 * Functions which enhance the theme by hooking into WordPress.
 */
require get_template_directory() . '/inc/template-functions.php';

/**
 * Customizer additions.
 */
require get_template_directory() . '/inc/customizer.php';

/**
 * Load Jetpack compatibility file.
 */
if ( defined( 'JETPACK__VERSION' ) ) {
	require get_template_directory() . '/inc/jetpack.php';
}

/**
 * Load WooCommerce compatibility file.
 */
if ( class_exists( 'WooCommerce' ) ) {
	require get_template_directory() . '/inc/woocommerce.php';
}


function replace_spaces_with_dash( $string ) {
	$string = str_replace( " ", "-", $string );
	$string = strtolower( $string );

	return $string;
}

//lista state
function list_states() { ?>

    <script>

        var jobsearch_custm_getJSON = function (url, callback) {
            var xhr = new XMLHttpRequest();
            xhr.open('GET', url, true);
            xhr.responseType = 'json';
            xhr.onload = function () {
                var status = xhr.status;
                if (status === 200) {
                    callback(null, xhr.response);
                } else {
                    callback(status, xhr.response);
                }
            };
            xhr.send();
        };


        jobsearch_custm_getJSON('http://geodata.solutions/api/api.php?type=getStates&countryId=RO&addClasses=order-alpha', function (err, data) {
            if (typeof data.result !== 'undefined') {
                var toStatesData = '';
                var listjudete = '';
                jQuery.each(data.result, function (cnrty_indx, cntry_elm) {
                    console.log(cnrty_indx + ' : ' + cntry_elm);
                    toStatesData += '<div class="state-wrap"><input id=' + cnrty_indx + ' type="checkbox"   class="judet" value="' + cntry_elm + '" stateid=' + cnrty_indx + '/> <label for=' + cnrty_indx + '>' + cntry_elm + '</label><br /></div>';


                });
                jQuery('#statelist').prepend(toStatesData);
            }
        });


    </script>


	<?php
}

// lista domenii
function list_sectors() {
	$domenii = get_terms( array(
		'taxonomy'   => 'sector',
		'parent'     => 0,
		'hide_empty' => false
	) );
	foreach ( $domenii as $domeniu ) {

		echo '<div class="domeniu-cat">';
		echo "<div class='domeniu-wrap'><input id='$domeniu->name' type='checkbox'  class='domeniu' value='$domeniu->name' termid='$domeniu->term_id' /> <label for='$domeniu->name'>$domeniu->name</label></div>";

		$lastTerms = get_terms( array(
			'taxonomy'   => 'sector',
			'parent'     => $domeniu->term_id,
			'hide_empty' => false
		) );

		foreach ( $lastTerms as $term ) {
			echo "<div class='domeniu-wrap-child'><input id='$term->name' type='checkbox'  class='domeniu' value='$term->name' termid='$term->term_id' /> <label for='$term->name'>$term->name</label><br /></div>";
		}

		echo '</div>';
	}

	echo "<a href='javascript:void(0)' class='close'>Inchide</a>";
}

// return nivel
function get_niveluri() {
	$custom_fields = get_option( 'jobsearch_custom_field_job' );

	$nivel = [];


	foreach ( $custom_fields as $field_key => $field_data ) {

		// print_r($field_data);
		//die();

		if ( $field_data['name'] == 'nivel-ierarhic' ) {
			$nivel['name']    = $field_data['name'];
			$nivel['options'] = $field_data['options'];
		}
	}

	return $nivel;

}

// return list
function list_nivele() {
	?>

    <div class='nivele-wrap'><input id='ceo1' type='checkbox' class='nivel' value='ceo -1'> <label
                for='ceo1'>ceo-1</label><br/></div>
    <div class='nivele-wrap'><input id='ceo2' type='checkbox' class='nivel' value='ceo -2'> <label
                for='ceo2'>ceo-2</label><br/></div>
    <div class='nivele-wrap'><input id='middle_mang' type='checkbox' class='nivel' value='middle management'> <label
                for='middle_mang'>middle
            management</label><br/></div>
    <div class='nivele-wrap'><input id='board_ex' type='checkbox' class='nivel' value='board executive'> <label
                for='board_ex'>board
            executive</label><br/></div>
    <div class='nivele-wrap'><input id='board_nex' type='checkbox' class='nivel' value='board non-executive'> <label
                for='board_nex'>board
            non-executive</label><br/></div>
    <div class='nivele-wrap'><input id='alt_niv' type='checkbox' class='nivel' value='alt nivel'> <label for='alt_niv'>alt
            nivel</label><br/></div>

	<?php

	echo "<a href='javascript:void(0)' class='close'>Inchide</a>";

}
//  enqueue script
function filter_ajax_enqueue() {
	// Enqueue javascript on the frontend.
	wp_enqueue_script(
		'filter-ajax-request',
		get_template_directory_uri() . '/js/filter-ajax-request.js',
		array( 'jquery' )
	);
	// The wp_localize_script allows us to output the ajax_url path for our script to use.
	wp_localize_script(
		'filter-ajax-request',
		'filter_ajax_obj',
		array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) )
	);
}

add_action( 'wp_enqueue_scripts', 'filter_ajax_enqueue' );

//  enqueue script
function job_save_note_enqueue() {
	// Enqueue javascript on the frontend.
	// The wp_localize_script allows us to output the ajax_url path for our script to use.
	wp_localize_script(
		'job-save-note-request',
		'job_save_note_obj',
		array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) )
	);
}

add_action( 'wp_enqueue_scripts', 'job_save_note_enqueue' );
//  enqueue script
function job_template_save_enqueue() {
	// Enqueue javascript on the frontend.
	// The wp_localize_script allows us to output the ajax_url path for our script to use.
	wp_localize_script(
		'job-template-save-request',
		'job_template_save_obj',
		array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) )
	);
}

add_action( 'wp_enqueue_scripts', 'job_template_save_enqueue' );

//  enqueue script
function profil_scm_save_enqueue() {
	// Enqueue javascript on the frontend.
	// The wp_localize_script allows us to output the ajax_url path for our script to use.
	wp_localize_script(
		'profil-scm-save-request',
		'profil_scm_save_obj',
		array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) )
	);
}

add_action( 'wp_enqueue_scripts', 'profil_scm__save_enqueue' );

//  enqueue script
function add_new_profile_enqueue() {
	// Enqueue javascript on the frontend.
	// The wp_localize_script allows us to output the ajax_url path for our script to use.
	wp_localize_script(
		'add-new-profile-request',
		'add_new_profile_obj',
		array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) )
	);
}

add_action( 'wp_enqueue_scripts', 'add_new_profile_enqueue' );

//  enqueue script
function job_profile_apply_enqueue() {
	// Enqueue javascript on the frontend.
	// The wp_localize_script allows us to output the ajax_url path for our script to use.
	wp_localize_script(
		'job-profile-apply--request',
		'job_profile_apply__obj',
		array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) )
	);
}

add_action( 'wp_enqueue_scripts', 'job_profile_apply__enqueue' );

//  enqueue script
function job_profile_apply_partial_enqueue() {
	// Enqueue javascript on the frontend.
	// The wp_localize_script allows us to output the ajax_url path for our script to use.
	wp_localize_script(
		'job-profile-apply-partial-request',
		'job_profile_apply_partial__obj',
		array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) )
	);
}

add_action( 'wp_enqueue_scripts', 'job_profile_apply_partial__enqueue' );

//  enqueue script
function job_profile_delete_enqueue() {
	// Enqueue javascript on the frontend.
	// The wp_localize_script allows us to output the ajax_url path for our script to use.
	wp_localize_script(
		'job-profile-delete-request',
		'job_profile_delete__obj',
		array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) )
	);
}
add_action( 'wp_enqueue_scripts', 'job_profile_delete__enqueue' );

//  enqueue script
function candidate_save_note_enqueue() {
	// Enqueue javascript on the frontend.
	// The wp_localize_script allows us to output the ajax_url path for our script to use.
	wp_localize_script(
		'candidate-save-note-request',
		'candidate_save_note_obj',
		array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) )
	);
}

add_action( 'wp_enqueue_scripts', 'candidate_save_note_enqueue' );

// enqueue file
function new_file_aplicatie_enqueue() {
	// Enqueue javascript on the frontend.
	// The wp_localize_script allows us to output the ajax_url path for our script to use.
	wp_localize_script(
		'new-file-aplicatie-request',
		'new_file_aplicatie_obj',
		array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) )
	);
}

add_action( 'wp_enqueue_scripts', 'new_file_aplicatie_enqueue' );


//functie filtrare joburi ajax
function the_apply_button( $job_id, $application_deadline, $job_employer_id, $arg = [], $widget = false, $email = [] ) {

	?>

    <aside class="jobsearch-typo-wrap">

		<?php
		echo apply_filters( 'jobsearch_job_detail_sidebar_bef4_apply', '', $job_id );
		ob_start();
		?>
        <div class="widget widget_apply_job">
			<?php
			ob_start();
			?>
            <div class="widget_apply_job_wrap">
				<?php
				if ( $widget ): ?>

                    <span class="apply-call-text"><?php esc_html_e( 'aplica prin', 'managero' ); ?> </span>

				<?php endif;

				$current_date = strtotime( current_time( 'd-m-Y H:i:s' ) );

				if ( $application_deadline != '' && $application_deadline <= $current_date ) {
					?>
                    <span class="deadline-closed"><?php esc_html_e( 'Application deadline closed.', 'wp-jobsearch' ); ?></span>
					<?php
				} else {

					if ( ! $arg ):
						$arg = array(
							'classes'           => 'jobsearch-applyjob-btn',
							'btn_before_label'  => esc_html__( 'Aplica acum', 'wp-jobsearch' ),
							'btn_after_label'   => esc_html__( 'Aplicat', 'wp-jobsearch' ),
							'btn_applied_label' => esc_html__( 'Ai aplicat', 'wp-jobsearch' ),
							'job_id'            => $job_id
						);

					endif;

					$apply_filter_btn = apply_filters( 'jobsearch_job_applications_btn', '', $arg );


					// var_dump($arg);
					echo( $apply_filter_btn );

				}


				// social login and apply end
				if ( $email ): ?>
                    <div class="email-wrap">
                        <a href="mailto:<?= $email['email']; ?>" class="email-apply">Email</a>
						<?php esc_html_e( 'in atentia', '' ); ?>
                        <br/>
						<?= $email['contact']; ?>
                    </div>
				<?php endif; ?>
            </div>
			<?php

			$apply_bbox = ob_get_clean();

			echo apply_filters( 'jobsearch_job_defdet_applybtn_boxhtml', $apply_bbox, $job_id );

			//
			$popup_args = array(
				'job_employer_id' => $job_employer_id,
				'job_id'          => $job_id,
			);
			$popup_html = apply_filters( 'jobsearch_job_send_message_html_filter', '', $popup_args );
			echo force_balance_tags( $popup_html );

			?>


        </div>
		<?php
		$sidebar_apply_output = ob_get_clean();
		// var_dump($sidebar_apply_output);
		echo apply_filters( 'jobsearch_job_detail_sidebar_apply_btns', $sidebar_apply_output, $job_id );
		// map
		?>
    </aside>

	<?php
}

// genereaza filtre job
function filtre_jobs() {

	$sectors  = $_REQUEST['filtre']['domenii'];
	$niveluri = $_REQUEST['filtre']['niveluri'];
	$judete   = $_REQUEST['filtre']['judete'];
	$salariu  = $_REQUEST['filtre']['salariu'];
	?>
    <div class="">
		<?php

		$args = [
			'post_type'      => 'job',
			'posts_per_page' => 20,
			'order'          => 'DESC'
		];

		if ( $sectors ) {
			$args['tax_query'] = [// (array) - use taxonomy parameters (available with Version 3.1).
				'relation' => 'AND',
				// (string) - The logical relationship between each inner taxonomy array when there is more than one. Possible values are 'AND', 'OR'. Do not use with a single inner taxonomy array. Default value is 'AND'.
				[
					'taxonomy'         => 'sector',
					// (string) - Taxonomy.
					'field'            => 'name',
					// (string) - Select taxonomy term by Possible values are 'term_id', 'name', 'slug' or 'term_taxonomy_id'. Default value is 'term_id'.
					'terms'            => $sectors,
					// (int/string/array) - Taxonomy term(s).
					'include_children' => true,
					// (bool) - Whether or not to include children for hierarchical taxonomies. Defaults to true.
					'operator'         => 'IN'
					// (string) - Operator to test. Possible values are 'IN', 'NOT IN', 'AND', 'EXISTS' and 'NOT EXISTS'. Default value is 'IN'.
				]
			];
		}

		$q_judete   = '';
		$q_niveluri = '';

		if ( $judete ) {
			$q_judete = [
				'key'     => 'locatia_jobului',
				// (string) - Custom field key.
				'value'   => $judete,
				// (string/array) - Custom field value (Note: Array support is limited to a compare value of 'IN', 'NOT IN', 'BETWEEN', or 'NOT BETWEEN') Using WP < 3.9? Check out this page for details: http://codex.wordpress.org/Class_Reference/WP_Query#Custom_Field_Parameters
				'type'    => 'CHAR',
				// (string) - Custom field type. Possible values are 'NUMERIC', 'BINARY', 'CHAR', 'DATE', 'DATETIME', 'DECIMAL', 'SIGNED', 'TIME', 'UNSIGNED'. Default value is 'CHAR'. The 'type' DATE works with the 'compare' value BETWEEN only if the date is stored at the format YYYYMMDD and tested with this format.
				//NOTE: The 'type' DATE works with the 'compare' value BETWEEN only if the date is stored at the format YYYYMMDD and tested with this format.
				'compare' => 'IN',
				// (string) - Operator to test. Possible values are '=', '!=', '>', '>=', '<', '<=', 'LIKE', 'NOT LIKE', 'IN', 'NOT IN', 'BETWEEN', 'NOT BETWEEN', 'EXISTS' (only in WP >= 3.5), and 'NOT EXISTS' (also only in WP >= 3.5). Default value is '='.
			];
		}


		if ( $niveluri ) {
			$q_niveluri = [
				'key'     => 'pozitia_ierarhica_nivelul_ierarhic',
				// (string) - Custom field key.
				'value'   => $niveluri,
				// (string/array) - Custom field value (Note: Array support is limited to a compare value of 'IN', 'NOT IN', 'BETWEEN', or 'NOT BETWEEN') Using WP < 3.9? Check out this page for details: http://codex.wordpress.org/Class_Reference/WP_Query#Custom_Field_Parameters
				'type'    => 'CHAR',
				// (string) - Custom field type. Possible values are 'NUMERIC', 'BINARY', 'CHAR', 'DATE', 'DATETIME', 'DECIMAL', 'SIGNED', 'TIME', 'UNSIGNED'. Default value is 'CHAR'. The 'type' DATE works with the 'compare' value BETWEEN only if the date is stored at the format YYYYMMDD and tested with this format.
				//NOTE: The 'type' DATE works with the 'compare' value BETWEEN only if the date is stored at the format YYYYMMDD and tested with this format.
				'compare' => 'IN',
				// (string) - Operator to test. Possible values are '=', '!=', '>', '>=', '<', '<=', 'LIKE', 'NOT LIKE', 'IN', 'NOT IN', 'BETWEEN', 'NOT BETWEEN', 'EXISTS' (only in WP >= 3.5), and 'NOT EXISTS' (also only in WP >= 3.5). Default value is '='.
			];
		}

		if ( $salariu ) {
			$q_salariu = [
				'key'     => 'oferta_salariu_oferit_job',
				// (string) - Custom field key.
				'value'   => $salariu,
				// (string/array) - Custom field value (Note: Array support is limited to a compare value of 'IN', 'NOT IN', 'BETWEEN', or 'NOT BETWEEN') Using WP < 3.9? Check out this page for details: http://codex.wordpress.org/Class_Reference/WP_Query#Custom_Field_Parameters
				'type'    => 'NUMERIC',
				// (string) - Custom field type. Possible values are 'NUMERIC', 'BINARY', 'CHAR', 'DATE', 'DATETIME', 'DECIMAL', 'SIGNED', 'TIME', 'UNSIGNED'. Default value is 'CHAR'. The 'type' DATE works with the 'compare' value BETWEEN only if the date is stored at the format YYYYMMDD and tested with this format.
				//NOTE: The 'type' DATE works with the 'compare' value BETWEEN only if the date is stored at the format YYYYMMDD and tested with this format.
				'compare' => '>=',
				// (string) - Operator to test. Possible values are '=', '!=', '>', '>=', '<', '<=', 'LIKE', 'NOT LIKE', 'IN', 'NOT IN', 'BETWEEN', 'NOT BETWEEN', 'EXISTS' (only in WP >= 3.5), and 'NOT EXISTS' (also only in WP >= 3.5). Default value is '='.
			];
		}


		$args['meta_query'] = [ // (array) - Custom field parameters (available with Version 3.1).
			'relation' => 'AND',
			// (string) - Possible values are 'AND', 'OR'. The logical relationship between each inner meta_query array when there is more than one. Do not use with a single inner meta_query array.
		];

		if ( $q_judete != '' ) {
			$args['meta_query'][] = $q_judete;
		}

		if ( $q_niveluri != '' ) {
			$args['meta_query'][] = $q_niveluri;
		}

		if ( $q_salariu != '' ) {
			$args['meta_query'][] = $q_salariu;
		}


		$jobs      = new WP_Query( $args );
		$job_count = 0;

		if ( $jobs->have_posts() ):while ( $jobs->have_posts() ):$jobs->the_post();

			$job_id      = get_the_ID();
			$post_id     = $job_id;
			$companie_id = get_post_meta( $job_id, 'jobsearch_field_job_posted_by', true );

			$logo          = get_field( 'imagine_profil_companie', $job_id );
			$logo          = wp_get_attachment_url( $logo );
			$data_companie = get_field( 'date_companie_2', $job_id );

			$companie  = $data_companie['nume_companie'];
			$extindere = $data_companie['extindere'];
			$adresa2   = $data_companie['origine'];
			$angajati  = $data_companie['numar_angajati'];


			$job     = get_the_title();
			$nivel   = get_field( 'pozitia_ierarhica', $job_id );
			$oferta  = get_field( 'oferta', $job_id );
			$adresa4 = get_field( 'locatia_jobului', $job_id );

			$nivel         = get_field( 'pozitia_ierarhica', $job_id );
			$subordonat    = $nivel['cui_este_subordonat'];
			$subordonati   = $nivel['subordonati_total'];
			$nivelierarhic = $nivel['nivelul_ierarhic'];

			$salariu = $oferta['salariu_oferit_job'];

			$domenii = get_field( 'domeniu', $job_id );

			$listaDomenii = '';

			foreach ( $domenii as $domeniu ) {
				$listaDomenii .= $domeniu->name . ', ';
			}

			$listaDomenii = substr( $listaDomenii, 0, - 2 );


			$application_deadline = get_post_meta( $post_id, 'jobsearch_field_job_application_deadline_date', true );
			$jobsearch_job_posted = get_post_meta( $post_id, 'jobsearch_field_job_publish_date', true );
			$current_date         = strtotime( current_time( 'd-m-Y H:i:s' ) );

			$job_employer_id = $companie_id;

			$user_id           = get_current_user_id();
			$user_is_candidate = jobsearch_user_is_candidate( $user_id );

			if ( $application_deadline != '' && $application_deadline <= $current_date ) {
				continue;
			}

			if ( $user_is_candidate ) {

				$candidate_id            = jobsearch_get_user_candidate_id( $user_id );
				$candidate_rej_jobs_list = get_post_meta( $candidate_id, 'jobsearch_rej_jobs_list', true );
				$candidate_fav_jobs_list = get_post_meta( $candidate_id, 'jobsearch_fav_jobs_list', true );
				$candidate_apd_jobs_list = get_post_meta( $candidate_id, 'jobsearch_apd_jobs_list', true );

				if ( $candidate_rej_jobs_list != '' ) {
					$candidate_rej_jobs_list = explode( ',', $candidate_rej_jobs_list );
					if ( in_array( $job_id, $candidate_rej_jobs_list ) ) {
						continue;
					}
				}

				if ( $candidate_apd_jobs_list != '' ) {
					$candidate_apd_jobs_list = explode( ',', $candidate_apd_jobs_list );
					if ( in_array( $job_id, $candidate_apd_jobs_list ) ) {
						continue;
					}
				}

				if ( $candidate_fav_jobs_list != '' ) {
					$candidate_fav_jobs_list = explode( ',', $candidate_fav_jobs_list );
					if ( in_array( $job_id, $candidate_fav_jobs_list ) ) {
						continue;
					}
				}

			}


			if ( is_user_logged_in() ) {
				$finded_result_list = jobsearch_find_index_user_meta_list( $job_id, 'jobsearch-user-jobs-applied-list', 'post_id', jobsearch_get_user_id() );
				if ( is_array( $finded_result_list ) && ! empty( $finded_result_list ) ) {
					continue;
				}
			}

			$job_count ++;

			?>

            <div class="col-12 jobslist-job" data-job="<?= $job_id; ?>">
                <div class="row custom-height">
                    <div class="col-sm-2">
                        <div class="wrapper-logo-company">
                            <img src="<?= $logo; ?>"/>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="company-name">
                            <a href="<?php the_permalink(); ?>"><h2><?= $companie ?>
                                </h2>
                                <h3 class="job-position"><?= $job; ?></h3></a>
                        </div>
                    </div>
                    <div class="col-sm-4">

						<?php
						$current_user = wp_get_current_user();
						$user_id      = get_current_user_id();
						$user_obj     = get_user_by( 'ID', $user_id );

						$employer_id = jobsearch_get_user_employer_id( $user_id );
						if ( $employer_id > 0 ):

						else:
							the_apply_button( $job_id, $application_deadline, $job_employer_id );
						endif;
						?>

						<?php
						if ( $job_type_str != '' ) {
							echo force_balance_tags( $job_type_str );
						}
						?>

                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-2">
                        <div class="wrapper-company-details">
							<?php esc_html_e( 'Detalii companie', 'managero' ); ?>
                        </div>
                    </div>
                    <div class="col-sm-7">
                        <div class="wrapper-text-details-company">
                            <b>Orgine</b>: <?= $adresa2 ?>
                            | <b>Extindere</b>: <?= $extindere; ?>
                            | <b>Nr angajati</b>: <?= $angajati; ?>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-2">
                        <div class="wrapper-company-details">
							<?php esc_html_e( 'Detalii job', 'managero' ); ?>
                        </div>
                    </div>
                    <div class="col-sm-7">
                        <div class="wrapper-text-details-company">
                            <b>Locatie</b>: <?= $adresa4; ?> | <b>Domeniu de activitate</b>: <?= $listaDomenii; ?><br>
                            <b>Nivel ierarhic</b>: <?= $nivelierarhic; ?> | <b>Subordonat lui</b>: <?= $subordonat; ?>
                            <br>
                            <b>Subordonati</b>: <?= $subordonati; ?>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-2">
                        <div class="wrapper-company-details">
							<?php esc_html_e( 'Salariu', 'managero' ); ?>
                        </div>
                    </div>
                    <div class="col-sm-7">
                        <div class="wrapper-text-details-company">
							<?= $salariu; ?> &euro;
                        </div>
                    </div>
                </div>
            </div>

		<?php endwhile;
		else:
			echo "<h2>Nu sunt joburi ce corespund criteriilor de filtrare</h2>";

		endif; ?>
    </div>

    <script>
        jQuery('.nojobs').html('<?= $job_count ?>');
    </script>

	<?php
	die();
}

add_action( "wp_ajax_filtre_jobs", "filtre_jobs" );
add_action( "wp_ajax_nopriv_filtre_jobs", "filtre_jobs" );


// salveaza template
function job_save_note() {
	$lista        = $_REQUEST['note'];
	$candidate_id = $_REQUEST['candidate'];
	foreach ( $lista as $nota ) {
		$job_id  = $nota['job_id'];
		$notaJob = $nota['nota'];
		update_post_meta( $candidate_id, "jobsearch-user-job_note_$job_id", $notaJob );
	}

	echo 'true';

	die();
}

add_action( "wp_ajax_job_save_note", "job_save_note" );
add_action( "wp_ajax_nopriv_job_save_note", "job_save_note" );


// template save
function profil_scm_save() {
	$id_candidat = $_REQUEST['candidat'];
	$data        = $_REQUEST['dataload'];

	$data = serialize( $data );
	update_post_meta( $id_candidat, "profil_scm", $data );

	die();
}

add_action( "wp_ajax_profil_scm_save", "profil_scm_save" );
add_action( "wp_ajax_nopriv_profil_scm_save", "profil_scm_save" );

// save the job template
function job_template_save() {

	$template     = $_REQUEST['template'];
	$companie_id  = $_REQUEST['companie'];
	$new_template = $_REQUEST['new'];

    $template['date']['undefined'] = '';

//	if ( $new_template == true ) {
//
//	    $template['nume_template']      = $template['nume_template_as'];
//		$template['descriere_template'] = $template['descriere_template_as'];
//
//        $template['date']['nume_template']      = $template['date']['nume_template_as'];
//        $template['date']['descriere_template'] = $template['date']['descriere_template_as'];
//
//	} else{
//         $template['nume_template']  =  $template['date']['nume_template'];
//         $template['descriere_template'] = $template['date']['descriere_template'];
//    }

	if ( $template['tip'] == 'job_profile' ) {
		$lista_curenta = get_post_meta( $companie_id, "companie_job_profiles", true );
	} else if ( $template['tip'] == 'company_profile' ) {
		$lista_curenta = get_post_meta( $companie_id, "companie_company_profiles", true );
	} else if ( $template['tip'] == 'anunt_template' ) {
		$lista_curenta = get_post_meta( $companie_id, "companie_anunt_template", true );
	} else if ( $template['tip'] == 'candidat_profile' ) {
		$lista_curenta = get_post_meta( $companie_id, "candidat_profiles", true );
	} else if ( $template['tip'] == 'candidat_texte' ) {
		$lista_curenta = get_post_meta( $companie_id, "candidat_texte_predefinite", true );
	} else if ( $template['tip'] == 'filtre_candidat' ) {
		$lista_curenta = get_post_meta( $companie_id, "filtre_candidat", true );
	} else if ( $template['tip'] == 'company_scm' ) {
		$lista_curenta = get_post_meta( $companie_id, "companie_company_scm", true );
	} else if ( $template['tip'] == 'profil_scm' ) {
		$lista_curenta = get_post_meta( $companie_id, "profil_scm", true );
	}

   //	nice_print_r($template);

	$lista_template = [];

	if ( isset( $lista_curenta ) && ! empty( $lista_curenta ) ) {
		$lista_curenta  = unserialize(base64_decode($lista_curenta));
		$lista_template = $lista_curenta;
	}
	unset( $template['date']['undefined'] );

	$nume                    = $template['date']['nume_template'];
	$lista_template[ $nume ] = $template['date'];


	$lista_template =  base64_encode(serialize($lista_template));

	if ( $template['tip'] == 'job_profile' ) {
		update_post_meta( $companie_id, "companie_job_profiles", $lista_template );
		echo 'template profile saved';
	} else if ( $template['tip'] == 'company_profile' ) {
		update_post_meta( $companie_id, "companie_company_profiles", $lista_template );
		echo 'template companie saved';
	} else if ( $template['tip'] == 'anunt_template' ) {
		update_post_meta( $companie_id, "companie_anunt_template", $lista_template );
		echo 'template anunt saved';
	} else if ( $template['tip'] == 'candidat_profile' ) {
		update_post_meta( $companie_id, "candidat_profiles", $lista_template );
		echo 'template candidat saved';
	} else if ( $template['tip'] == 'candidat_texte' ) {
		update_post_meta( $companie_id, "candidat_texte_predefinite", $lista_template );
		echo 'template candidat saved';
	} else if ( $template['tip'] == 'filtre_candidat' ) {
		update_post_meta( $companie_id, "filtre_candidat", $lista_template );
		echo 'filtre candidat saved';
	} else if ( $template['tip'] == 'company_scm' ) {
		update_post_meta( $companie_id, "companie_company_scm", $lista_template );
		echo 'profil scm saved';
	} else if ( $template['tip'] == 'profil_scm' ) {
		update_post_meta( $companie_id, "profil_scm", $lista_template );
		echo 'profil scm saved';
	}

	die();
}

add_action( "wp_ajax_job_template_save", "job_template_save" );
add_action( "wp_ajax_nopriv_job_template_save", "job_template_save" );


// template delete
function job_profile_delete() {

	$template    = $_REQUEST['template'];
	$companie_id = $_REQUEST['companie'];
	$tip         = $_REQUEST['tip'];
	print_r($template);

	if ( $tip == 'job_profile' ) {
		$lista_curenta = get_post_meta( $companie_id, "companie_job_profiles", true );
	} else if ( $tip == 'company_profile' ) {
		$lista_curenta = get_post_meta( $companie_id, "companie_company_profiles", true );
	} else if ( $tip == 'anunt_template' ) {
		$lista_curenta = get_post_meta( $companie_id, "companie_anunt_template", true );
	} else if ( $tip == 'candidat_profile' ) {
		$lista_curenta = get_post_meta( $companie_id, "candidat_profiles", true );
	} else if ( $tip == 'candidat_texte' ) {
		$lista_curenta = get_post_meta( $companie_id, "candidat_texte_predefinite", true );
	} else if ( $tip == 'filtre_candidat' ) {
		$lista_curenta = get_post_meta( $companie_id, "filtre_candidat", true );
	} else if ( $tip == 'company_scm' ) {
		$lista_curenta = get_post_meta( $companie_id, "companie_company_scm", true );
	}


	if ( isset( $lista_curenta ) && ! empty( $lista_curenta ) ) {
		$lista_curenta = unserialize(base64_decode($lista_curenta));

	}

	if ( count( $lista_curenta ) == 1 ) {
		$lista_curenta = [];
	} else {
		unset( $lista_curenta[ $template ] );
	}


//	print_r($lista_curenta);

	$lista_template = base64_encode(serialize($lista_curenta));

	// print_r($lista_curenta);

	if ( $tip == 'job_profile' ) {
		update_post_meta( $companie_id, "companie_job_profiles", $lista_template );
	} else if ( $tip == 'company_profile' ) {
		update_post_meta( $companie_id, "companie_company_profiles", $lista_template );
	} else if ( $tip == 'anunt_template' ) {
		update_post_meta( $companie_id, "companie_anunt_template", $lista_template );
		echo "aici intra 2";
	} else if ( $tip == 'candidat_profile' ) {
		update_post_meta( $companie_id, "candidat_profiles", $lista_template );
	} else if ( $tip == 'candidat_texte' ) {
		update_post_meta( $companie_id, "candidat_texte_predefinite", $lista_template );
	} else if ( $tip == 'filtre_candidat' ) {
		update_post_meta( $companie_id, "filtre_candidat", $lista_template );
	} else if ( $tip == 'company_scm' ) {
		update_post_meta( $companie_id, "companie_company_scm", $lista_template );
	}

	echo 'template deleted';
	die();
}

add_action( "wp_ajax_job_profile_delete", "job_profile_delete" );
add_action( "wp_ajax_nopriv_job_profile_delete", "job_profile_delete" );


// template aplica
function add_new_profile() {

	$companie_id = $_REQUEST['companie'];
	$tip         = $_REQUEST['tip'];

	if ( $tip == 'job_profile' ) {
		$date_acf = [
			'titlu'        => 'Profil Job - Profil nou',
			'id'           => 'acf-form-job-profile',
			'field_groups' => [ 1427 ]
		];
	} else if ( $tip == 'company_profile' ) {
		$date_acf = [
			'titlu'        => 'Profil Companie - Profil nou',
			'id'           => 'acf-form-company-profile',
			'field_groups' => [ 837 ]
		];
	} else if ( $tip == 'anunt_template_new' ) {
		$date_acf = [
			'titlu'        => 'Template anunt - Template nou',
			'id'           => 'acf-form-job-profile',
			'field_groups' => [ 881 ]
		];
	} else if ( $tip == 'candidat_profile' ) {
		$date_acf = [
			'titlu'        => 'Profil candidat - Profil nou',
			'id'           => 'acf-form-candidat-profile',
			'field_groups' => [ 725 ]
		];
	} else if ( $tip == 'company_scm' ) {
		$date_acf = [
			'titlu'        => 'Profil SCM - Profil nou',
			'id'           => 'acf-form-company-scm',
			'field_groups' => [ 1660 ]
		];
	} else if ( $tip == 'candidat_texte' ) {
		$date_acf = [
			'titlu'        => 'Text predefinit - Text nou',
			'id'           => 'acf-form-candidat-profile',
			'field_groups' => [ 605 ]
		];
	} else if ( $tip == 'filtre_candidat' ) {
		$date_acf = [
			'titlu'        => 'Text predefinit - Text nou',
			'id'           => 'acf-form-candidat-profile',
			'field_groups' => [ 823 ]
		];
	}


	?>
    <!-- Modal -->
    <div class="modal fade" id="formModal" tabindex="-1" role="dialog" aria-labelledby="formModalLabel"
         aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="formModalLabel"><?= $date_acf['titlu']; ?></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="rezultatModal">

					<?php

					$settings1 = array(

						/* (string) Unique identifier for the form. Defaults to 'acf-form' */
						'id'                    => $date_acf['id'],

						/* (int|string) The post ID to load data from and save data to. Defaults to the current post ID.
						Can also be set to 'new_post' to create a new post on submit */
						'post_id'               => 'new_post',

						/* (array) An array of post data used to create a post. See wp_insert_post for available parameters.
						The above 'post_id' setting must contain a value of 'new_post' */
				//		'new_post'              => 'new_post',

						/* (array) An array of field group IDs/keys to override the fields displayed in this form */
						'field_groups'          => $date_acf['field_groups'],
//				'field_groups'          => [ 1366 ],

						/* (array) An array of field IDs/keys to override the fields displayed in this form */
						'fields'                => false,

						/* (boolean) Whether or not to show the post title text field. Defaults to false */
						'post_title'            => false,

						/* (boolean) Whether or not to show the post content editor field. Defaults to false */
						'post_content'          => false,

						/* (boolean) Whether or not to create a form element. Useful when a adding to an existing form. Defaults to true */
						'form'                  => true,

						/* (array) An array or HTML attributes for the form element */
						'form_attributes'       => array(),

						/* (string) The URL to be redirected to after the form is submit. Defaults to the current URL with a GET parameter '?updated=true'.
						A special placeholder '%post_url%' will be converted to post's permalink (handy if creating a new post)
						A special placeholder '%post_id%' will be converted to post's ID (handy if creating a new post) */
						'return'                => '',

						/* (string) Extra HTML to add before the fields */
						'html_before_fields'    => '',

						/* (string) Extra HTML to add after the fields */
						'html_after_fields'     => '',

						/* (string) The text displayed on the submit button */
						'submit_value'          => __( "Update", 'acf' ),

						/* (string) A message displayed above the form after being redirected. Can also be set to false for no message */
						'updated_message'       => __( "Post updated", 'acf' ),

						/* (string) Determines where field labels are places in relation to fields. Defaults to 'top'.
						Choices of 'top' (Above fields) or 'left' (Beside fields) */
						'label_placement'       => 'top',

						/* (string) Determines where field instructions are places in relation to fields. Defaults to 'label'.
						Choices of 'label' (Below labels) or 'field' (Below fields) */
						'instruction_placement' => 'label',

						/* (string) Determines element used to wrap a field. Defaults to 'div'
						Choices of 'div', 'tr', 'td', 'ul', 'ol', 'dl' */
						'field_el'              => 'div',

						/* (string) Whether to use the WP uploader or a basic input for image and file fields. Defaults to 'wp'
						Choices of 'wp' or 'basic'. Added in v5.2.4 */
						'uploader'              => 'wp',

						/* (boolean) Whether to include a hidden input field to capture non human form submission. Defaults to true. Added in v5.3.4 */
						'honeypot'              => true,

						/* (string) HTML used to render the updated message. Added in v5.5.10 */
						'html_updated_message'  => '<div id="message" class="updated"><p>%s</p></div>',

						/* (string) HTML used to render the submit button. Added in v5.5.10 */
						'html_submit_button'    => '<input type="submit" class="acf-button button button-primary button-large" value="%s"  style="display: none;"/>',

						/* (string) HTML used to render the submit button loading spinner. Added in v5.5.10 */
						'html_submit_spinner'   => '<span class="acf-spinner"></span>',

						/* (boolean) Whether or not to sanitize all $_POST data with the wp_kses_post() function. Defaults to true. Added in v5.6.5 */
						'kses'                  => true,

						'load_fields' => null,

						'fields_type' => $tip

					);
					acf_form( $settings1 );

					?>
                </div>

            </div>
        </div>
    </div>
    <style>
        #wrap-salveaza-ca {
            display: none;
        }
    </style>

	<?php
}

add_action( "wp_ajax_add_new_profile", "add_new_profile" );
add_action( "wp_ajax_nopriv_add_new_profile", "add_new_profile" );


// aplica job template in job template
function job_profile_apply_2() {

	$template    = $_REQUEST['template'];
	$companie_id = $_REQUEST['companie'];
	$tip         = $_REQUEST['tip'];

if ( $tip == 'anunt_template' ) {
		$date_acf      = [
			'titlu'        => 'Template anunt ' . $template,
			'id'           => 'acf-form-job-profile',
			'field_groups' => [ 881 ]
		];
		$lista_curenta = get_post_meta( $companie_id, "companie_anunt_template", true );
	}

	$lista_curenta = 	unserialize(base64_decode($lista_curenta));
	// print_r($lista_curenta);

					$settings1 = array(

						/* (string) Unique identifier for the form. Defaults to 'acf-form' */
						'id'                    => $date_acf['id'],

						/* (int|string) The post ID to load data from and save data to. Defaults to the current post ID.
						Can also be set to 'new_post' to create a new post on submit */
						'post_id'               => $companie_id,

						/* (array) An array of post data used to create a post. See wp_insert_post for available parameters.
						The above 'post_id' setting must contain a value of 'new_post' */
						'new_post'              => false,

						/* (array) An array of field group IDs/keys to override the fields displayed in this form */
						'field_groups'          => $date_acf['field_groups'],
//				'field_groups'          => [ 1366 ],

						/* (array) An array of field IDs/keys to override the fields displayed in this form */
						'fields'                => false,

						/* (boolean) Whether or not to show the post title text field. Defaults to false */
						'post_title'            => false,

						/* (boolean) Whether or not to show the post content editor field. Defaults to false */
						'post_content'          => false,

						/* (boolean) Whether or not to create a form element. Useful when a adding to an existing form. Defaults to true */
						'form'                  => true,

						/* (array) An array or HTML attributes for the form element */
						'form_attributes'       => array(),

						/* (string) The URL to be redirected to after the form is submit. Defaults to the current URL with a GET parameter '?updated=true'.
						A special placeholder '%post_url%' will be converted to post's permalink (handy if creating a new post)
						A special placeholder '%post_id%' will be converted to post's ID (handy if creating a new post) */
						'return'                => '',

						/* (string) Extra HTML to add before the fields */
						'html_before_fields'    => '',

						/* (string) Extra HTML to add after the fields */
						'html_after_fields'     => '',

						/* (string) The text displayed on the submit button */
						'submit_value'          => __( "Update", 'acf' ),

						/* (string) A message displayed above the form after being redirected. Can also be set to false for no message */
						'updated_message'       => __( "Post updated", 'acf' ),

						/* (string) Determines where field labels are places in relation to fields. Defaults to 'top'.
						Choices of 'top' (Above fields) or 'left' (Beside fields) */
						'label_placement'       => 'top',

						/* (string) Determines where field instructions are places in relation to fields. Defaults to 'label'.
						Choices of 'label' (Below labels) or 'field' (Below fields) */
						'instruction_placement' => 'label',

						/* (string) Determines element used to wrap a field. Defaults to 'div'
						Choices of 'div', 'tr', 'td', 'ul', 'ol', 'dl' */
						'field_el'              => 'div',

						/* (string) Whether to use the WP uploader or a basic input for image and file fields. Defaults to 'wp'
						Choices of 'wp' or 'basic'. Added in v5.2.4 */
						'uploader'              => 'wp',

						/* (boolean) Whether to include a hidden input field to capture non human form submission. Defaults to true. Added in v5.3.4 */
						'honeypot'              => true,

						/* (string) HTML used to render the updated message. Added in v5.5.10 */
						'html_updated_message'  => '<div id="message" class="updated"><p>%s</p></div>',

						/* (string) HTML used to render the submit button. Added in v5.5.10 */
						'html_submit_button'    => '<input type="submit" class="acf-button button button-primary button-large" value="%s"  style="display: none;"/>',

						/* (string) HTML used to render the submit button loading spinner. Added in v5.5.10 */
						'html_submit_spinner'   => '<span class="acf-spinner"></span>',

						/* (boolean) Whether or not to sanitize all $_POST data with the wp_kses_post() function. Defaults to true. Added in v5.6.5 */
						'kses'                  => true,

						'load_fields' => $lista_curenta[ $template ],

						'fields_type' => $tip
					,

					);
					acf_form( $settings1 );

					?>
    <style>
        #wrap-salveaza-ca, #wrap-salveaza-new {
            display: none;
        }
    </style>

	<?php
	die();
}

add_action( "wp_ajax_job_profile_apply_2", "job_profile_apply_2" );
add_action( "wp_ajax_nopriv_job_profile_apply_2", "job_profile_apply_2" );



// aplica job template in job template
function job_profile_apply() {

    $template    = $_REQUEST['template'];
    $companie_id = $_REQUEST['companie'];
    $tip         = $_REQUEST['tip'];

    if ( $tip == 'job_profile' ) {
        $date_acf      = [
            'titlu'        => 'Profil Job ' . $template,
            'id'           => 'acf-form-job-profile',
            'field_groups' => [ 1427 ],
        ];
        $lista_curenta = get_post_meta( $companie_id, "companie_job_profiles", true );
    } else if ( $tip == 'company_profile' ) {
        $date_acf      = [
            'titlu'        => 'Profil Companie ' . $template,
            'id'           => 'acf-form-company-profile',
            'field_groups' => [ 837 ]
        ];
        $lista_curenta = get_post_meta( $companie_id, "companie_company_profiles", true );
    } else if ( $tip == 'anunt_template' ) {
        $date_acf      = [
            'titlu'        => 'Template anunt ' . $template,
            'id'           => 'acf-form-job-profile',
            'field_groups' => [ 881 ]
        ];
        $lista_curenta = get_post_meta( $companie_id, "companie_anunt_template", true );


    } else if ( $tip == 'candidat_profile' ) {
        $date_acf      = [
            'titlu'        => 'Profil candidat ' . $template,
            'id'           => 'acf-form-candidat-profile',
            'field_groups' => [ 725 ]
        ];
        $lista_curenta = get_post_meta( $companie_id, "candidat_profiles", true );
   //     nice_print_r($lista_curenta);

    } else if ( $tip == 'candidat_texte' ) {
        $date_acf      = [
            'titlu'        => 'Profil candidat ' . $template,
            'id'           => 'acf-form-candidat-profile',
            'field_groups' => [ 605 ]
        ];
        $lista_curenta = get_post_meta( $companie_id, "candidat_texte_predefinite", true );
    } else if ( $tip == 'filtre_candidat' ) {
        $date_acf      = [
            'titlu'        => 'Text predefinit - Text nou',
            'id'           => 'acf-form-candidat-profile',
            'field_groups' => [ 823 ]
        ];
        $lista_curenta = get_post_meta( $companie_id, "filtre_candidat", true );
    } else if ( $tip == 'company_scm' ) {
        $date_acf      = [
            'titlu'        => 'Text predefinit - Text nou',
            'id'           => 'acf-form-candidat-profile',
            'field_groups' => [ 1660 ]
        ];
        $lista_curenta = get_post_meta( $companie_id, "companie_company_scm", true );
    }

    $lista_curenta = unserialize(base64_decode($lista_curenta));
    // print_r($lista_curenta);

    ?>


    <!-- Modal -->
    <div class="modal fade" id="formModal" tabindex="-1" role="dialog" aria-labelledby="formModalLabel"
         aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"
                        id="formModalLabel"<?php echo $date_acf['titlu'] . " - " . $template; ?></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="rezultatModal">

                    <?php

                    $settings1 = array(

                        /* (string) Unique identifier for the form. Defaults to 'acf-form' */
                        'id'                    => $date_acf['id'],

                        /* (int|string) The post ID to load data from and save data to. Defaults to the current post ID.
                        Can also be set to 'new_post' to create a new post on submit */
                        'post_id'               => $companie_id,

                        /* (array) An array of post data used to create a post. See wp_insert_post for available parameters.
                        The above 'post_id' setting must contain a value of 'new_post' */
                        'new_post'              => false,

                        /* (array) An array of field group IDs/keys to override the fields displayed in this form */
                        'field_groups'          => $date_acf['field_groups'],
//				'field_groups'          => [ 1366 ],

                        /* (array) An array of field IDs/keys to override the fields displayed in this form */
                        'fields'                => false,

                        /* (boolean) Whether or not to show the post title text field. Defaults to false */
                        'post_title'            => false,

                        /* (boolean) Whether or not to show the post content editor field. Defaults to false */
                        'post_content'          => false,

                        /* (boolean) Whether or not to create a form element. Useful when a adding to an existing form. Defaults to true */
                        'form'                  => true,

                        /* (array) An array or HTML attributes for the form element */
                        'form_attributes'       => array(),

                        /* (string) The URL to be redirected to after the form is submit. Defaults to the current URL with a GET parameter '?updated=true'.
                        A special placeholder '%post_url%' will be converted to post's permalink (handy if creating a new post)
                        A special placeholder '%post_id%' will be converted to post's ID (handy if creating a new post) */
                        'return'                => '',

                        /* (string) Extra HTML to add before the fields */
                        'html_before_fields'    => '',

                        /* (string) Extra HTML to add after the fields */
                        'html_after_fields'     => '',

                        /* (string) The text displayed on the submit button */
                        'submit_value'          => __( "Update", 'acf' ),

                        /* (string) A message displayed above the form after being redirected. Can also be set to false for no message */
                        'updated_message'       => __( "Post updated", 'acf' ),

                        /* (string) Determines where field labels are places in relation to fields. Defaults to 'top'.
                        Choices of 'top' (Above fields) or 'left' (Beside fields) */
                        'label_placement'       => 'top',

                        /* (string) Determines where field instructions are places in relation to fields. Defaults to 'label'.
                        Choices of 'label' (Below labels) or 'field' (Below fields) */
                        'instruction_placement' => 'label',

                        /* (string) Determines element used to wrap a field. Defaults to 'div'
                        Choices of 'div', 'tr', 'td', 'ul', 'ol', 'dl' */
                        'field_el'              => 'div',

                        /* (string) Whether to use the WP uploader or a basic input for image and file fields. Defaults to 'wp'
                        Choices of 'wp' or 'basic'. Added in v5.2.4 */
                        'uploader'              => 'wp',

                        /* (boolean) Whether to include a hidden input field to capture non human form submission. Defaults to true. Added in v5.3.4 */
                        'honeypot'              => true,

                        /* (string) HTML used to render the updated message. Added in v5.5.10 */
                        'html_updated_message'  => '<div id="message" class="updated"><p>%s</p></div>',

                        /* (string) HTML used to render the submit button. Added in v5.5.10 */
                        'html_submit_button'    => '<input type="submit" class="acf-button button button-primary button-large" value="%s"  style="display: none;"/>',

                        /* (string) HTML used to render the submit button loading spinner. Added in v5.5.10 */
                        'html_submit_spinner'   => '<span class="acf-spinner"></span>',

                        /* (boolean) Whether or not to sanitize all $_POST data with the wp_kses_post() function. Defaults to true. Added in v5.6.5 */
                        'kses'                  => true,

                        'load_fields' => $lista_curenta[ $template ],

                        'fields_type' => $tip
                    ,

                    );
                    acf_form( $settings1 );

                    ?>
                </div>

            </div>
        </div>
    </div>
    <style>
        #wrap-salveaza-ca, #wrap-salveaza-new {
            display: none;
        }
    </style>

    <?php
    die();
}

add_action( "wp_ajax_job_profile_apply", "job_profile_apply" );
add_action( "wp_ajax_nopriv_job_profile_apply", "job_profile_apply" );


// aplica job template in template anunt, salveaza datele temporar
function job_profile_apply_partial() {
	$template               = $_REQUEST['template'];
	$companie_id            = $_REQUEST['companie'];
	$template_temp          = $_REQUEST['template_temp'];
	$tip                    = $_REQUEST['tip'];
	$active_job_profile     = $_REQUEST['profil_job'];
	$active_company_profile = $_REQUEST['profil_companie'];


	if ( $tip == 'job_profile_combo' ) {
		$date_acf           = [
			'titlu'        => 'Template anunt',
			'id'           => 'acf-form-job-profile',
			'field_groups' => [ 881 ]
		];
		$lista_curenta      = get_post_meta( $companie_id, "companie_job_profiles", true );
		$active_job_profile = $template;

	} else if ( $tip == 'company_profile_combo' ) {
		$date_acf               = [
			'titlu'        => 'Template anunt',
			'id'           => 'acf-form-job-profile',
			'field_groups' => [ 881 ]
		];
		$active_company_profile = $template;
		$lista_curenta          = get_post_meta( $companie_id, "companie_company_profiles", true );
	}


	$lista_curenta = 	unserialize(base64_decode($lista_curenta));

	?>

    <!-- Modal -->
    <div class="modal fade" id="formModal" tabindex="-1" role="dialog" aria-labelledby="formModalLabel"
         aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"
                        id="formModalLabel"<?php echo $date_acf['titlu'] . " - " . $template; ?></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="modalZone" data-job="<?= $active_job_profile; ?>"
                     data-companie="<?= $active_company_profile; ?>">

					<?php

					$settings1 = array(

						/* (string) Unique identifier for the form. Defaults to 'acf-form' */
						'id'                    => $date_acf['id'],

						/* (int|string) The post ID to load data from and save data to. Defaults to the current post ID.
						Can also be set to 'new_post' to create a new post on submit */
						'post_id'               => $companie_id,

						/* (array) An array of post data used to create a post. See wp_insert_post for available parameters.
						The above 'post_id' setting must contain a value of 'new_post' */
						'new_post'              => false,

						/* (array) An array of field group IDs/keys to override the fields displayed in this form */
						'field_groups'          => $date_acf['field_groups'],
//				'field_groups'          => [ 1366 ],

						/* (array) An array of field IDs/keys to override the fields displayed in this form */
						'fields'                => false,

						/* (boolean) Whether or not to show the post title text field. Defaults to false */
						'post_title'            => false,

						/* (boolean) Whether or not to show the post content editor field. Defaults to false */
						'post_content'          => false,

						/* (boolean) Whether or not to create a form element. Useful when a adding to an existing form. Defaults to true */
						'form'                  => true,

						/* (array) An array or HTML attributes for the form element */
						'form_attributes'       => array(),

						/* (string) The URL to be redirected to after the form is submit. Defaults to the current URL with a GET parameter '?updated=true'.
						A special placeholder '%post_url%' will be converted to post's permalink (handy if creating a new post)
						A special placeholder '%post_id%' will be converted to post's ID (handy if creating a new post) */
						'return'                => '',

						/* (string) Extra HTML to add before the fields */
						'html_before_fields'    => '',

						/* (string) Extra HTML to add after the fields */
						'html_after_fields'     => '',

						/* (string) The text displayed on the submit button */
						'submit_value'          => __( "Update", 'acf' ),

						/* (string) A message displayed above the form after being redirected. Can also be set to false for no message */
						'updated_message'       => __( "Post updated", 'acf' ),

						/* (string) Determines where field labels are places in relation to fields. Defaults to 'top'.
						Choices of 'top' (Above fields) or 'left' (Beside fields) */
						'label_placement'       => 'top',

						/* (string) Determines where field instructions are places in relation to fields. Defaults to 'label'.
						Choices of 'label' (Below labels) or 'field' (Below fields) */
						'instruction_placement' => 'label',

						/* (string) Determines element used to wrap a field. Defaults to 'div'
						Choices of 'div', 'tr', 'td', 'ul', 'ol', 'dl' */
						'field_el'              => 'div',

						/* (string) Whether to use the WP uploader or a basic input for image and file fields. Defaults to 'wp'
						Choices of 'wp' or 'basic'. Added in v5.2.4 */
						'uploader'              => 'wp',

						/* (boolean) Whether to include a hidden input field to capture non human form submission. Defaults to true. Added in v5.3.4 */
						'honeypot'              => true,

						/* (string) HTML used to render the updated message. Added in v5.5.10 */
						'html_updated_message'  => '<div id="message" class="updated"><p>%s</p></div>',

						/* (string) HTML used to render the submit button. Added in v5.5.10 */
						'html_submit_button'    => '<input type="submit" class="acf-button button button-primary button-large" value="%s"  style="display: none;"/>',

						/* (string) HTML used to render the submit button loading spinner. Added in v5.5.10 */
						'html_submit_spinner'   => '<span class="acf-spinner"></span>',

						/* (boolean) Whether or not to sanitize all $_POST data with the wp_kses_post() function. Defaults to true. Added in v5.6.5 */
						'kses'                  => true,

						'load_fields' => $lista_curenta[ $template ],

						'temp_fields' => $template_temp,

						'fields_type' => $tip
					,

					);
					acf_form( $settings1 );

					?>
                </div>

            </div>
        </div>
    </div>
    <style>
        #wrap-salveaza-ca, #wrap-salveaza-new {
            display: none;
        }

        .acf-field-5d4824cbea89d {
            display: none;
        }

        .acf-field-5d4824e9ea89e {
            display: none;
        }

    </style>

	<?php
	die();
}

add_action( "wp_ajax_job_profile_apply_partial", "job_profile_apply_partial" );
add_action( "wp_ajax_nopriv_job_profile_apply_partial", "job_profile_apply_partial" );


// aplica job template in template anunt, salveaza datele temporar
function job_profile_apply_partial_2() {
    $template               = $_REQUEST['template'];
    $companie_id            = $_REQUEST['companie'];
    $template_temp          = $_REQUEST['template_temp'];
    $tip                    = $_REQUEST['tip'];
    $active_job_profile     = $_REQUEST['profil_job'];
    $active_company_profile = $_REQUEST['profil_companie'];


    if ( $tip == 'job_profile_combo' ) {
        $date_acf           = [
            'titlu'        => 'Template anunt',
            'id'           => 'acf-form-job-profile',
            'field_groups' => [ 881 ]
        ];
        $lista_curenta      = get_post_meta( $companie_id, "companie_job_profiles", true );
        $active_job_profile = $template;

    } else if ( $tip == 'company_profile_combo' ) {
        $date_acf               = [
            'titlu'        => 'Template anunt',
            'id'           => 'acf-form-job-profile',
            'field_groups' => [ 881 ]
        ];
        $active_company_profile = $template;
        $lista_curenta          = get_post_meta( $companie_id, "companie_company_profiles", true );
    }



    $lista_curenta = unserialize(base64_decode($lista_curenta));


    $settings1 = array(

                        /* (string) Unique identifier for the form. Defaults to 'acf-form' */
                        'id'                    => $date_acf['id'],

                        /* (int|string) The post ID to load data from and save data to. Defaults to the current post ID.
                        Can also be set to 'new_post' to create a new post on submit */
                        'post_id'               => $companie_id,

                        /* (array) An array of post data used to create a post. See wp_insert_post for available parameters.
                        The above 'post_id' setting must contain a value of 'new_post' */
                        'new_post'              => false,

                        /* (array) An array of field group IDs/keys to override the fields displayed in this form */
                        'field_groups'          => $date_acf['field_groups'],
//				'field_groups'          => [ 1366 ],

                        /* (array) An array of field IDs/keys to override the fields displayed in this form */
                        'fields'                => false,

                        /* (boolean) Whether or not to show the post title text field. Defaults to false */
                        'post_title'            => false,

                        /* (boolean) Whether or not to show the post content editor field. Defaults to false */
                        'post_content'          => false,

                        /* (boolean) Whether or not to create a form element. Useful when a adding to an existing form. Defaults to true */
                        'form'                  => true,

                        /* (array) An array or HTML attributes for the form element */
                        'form_attributes'       => array(),

                        /* (string) The URL to be redirected to after the form is submit. Defaults to the current URL with a GET parameter '?updated=true'.
                        A special placeholder '%post_url%' will be converted to post's permalink (handy if creating a new post)
                        A special placeholder '%post_id%' will be converted to post's ID (handy if creating a new post) */
                        'return'                => '',

                        /* (string) Extra HTML to add before the fields */
                        'html_before_fields'    => '',

                        /* (string) Extra HTML to add after the fields */
                        'html_after_fields'     => '',

                        /* (string) The text displayed on the submit button */
                        'submit_value'          => __( "Update", 'acf' ),

                        /* (string) A message displayed above the form after being redirected. Can also be set to false for no message */
                        'updated_message'       => __( "Post updated", 'acf' ),

                        /* (string) Determines where field labels are places in relation to fields. Defaults to 'top'.
                        Choices of 'top' (Above fields) or 'left' (Beside fields) */
                        'label_placement'       => 'top',

                        /* (string) Determines where field instructions are places in relation to fields. Defaults to 'label'.
                        Choices of 'label' (Below labels) or 'field' (Below fields) */
                        'instruction_placement' => 'label',

                        /* (string) Determines element used to wrap a field. Defaults to 'div'
                        Choices of 'div', 'tr', 'td', 'ul', 'ol', 'dl' */
                        'field_el'              => 'div',

                        /* (string) Whether to use the WP uploader or a basic input for image and file fields. Defaults to 'wp'
                        Choices of 'wp' or 'basic'. Added in v5.2.4 */
                        'uploader'              => 'wp',

                        /* (boolean) Whether to include a hidden input field to capture non human form submission. Defaults to true. Added in v5.3.4 */
                        'honeypot'              => true,

                        /* (string) HTML used to render the updated message. Added in v5.5.10 */
                        'html_updated_message'  => '<div id="message" class="updated"><p>%s</p></div>',

                        /* (string) HTML used to render the submit button. Added in v5.5.10 */
                        'html_submit_button'    => '<input type="submit" class="acf-button button button-primary button-large" value="%s"  style="display: none;"/>',

                        /* (string) HTML used to render the submit button loading spinner. Added in v5.5.10 */
                        'html_submit_spinner'   => '<span class="acf-spinner"></span>',

                        /* (boolean) Whether or not to sanitize all $_POST data with the wp_kses_post() function. Defaults to true. Added in v5.6.5 */
                        'kses'                  => true,

                        'load_fields' => $lista_curenta[ $template ],

                        'temp_fields' => $template_temp,

                        'fields_type' => $tip
                    ,

                    );
                    acf_form( $settings1 );

                    ?>

    <style>
        #wrap-salveaza-ca, #wrap-salveaza-new {
            display: none;
        }

        .acf-field-5d4824cbea89d {
            display: none;
        }

        .acf-field-5d4824e9ea89e {
            display: none;
        }

    </style>

    <?php
    die();
}

add_action( "wp_ajax_job_profile_apply_partial_2", "job_profile_apply_partial_2" );
add_action( "wp_ajax_nopriv_job_profile_apply_partial_2", "job_profile_apply_partial_2" );

// salveaza
function candidate_save_note() {
	$lista     = $_REQUEST['note'];
	$job_id    = $_REQUEST['job'];
	$candidate = $_REQUEST['candidate'];
	foreach ( $lista as $nota ) {
		$job_id       = $nota['job_id'];
		$notaCandidat = $nota['nota'];
		update_post_meta( $job_id, "jobsearch-user-candidate_note_$candidate", $notaCandidat );
	}

	echo 'true';

	die();
}

add_action( "wp_ajax_job_save_note", "job_save_note" );
add_action( "wp_ajax_nopriv_job_save_note", "job_save_note" );


//function new_file_aplicatie(){
//
//	$fisier = $_REQUEST['new_ap_file_nonce'];
//	$candidate_id = $_REQUEST['candidate'];
//
//	if (
//		isset( $fisier, $candidate_id )
//	//	&& wp_verify_nonce( $_POST["new_ap_file_nonce_$job_id"], "new_ap_file_$job_id" )
//		&& current_user_can( 'edit_post', $candidate_id )
//	) {
//
//		// These files need to be included as dependencies when on the front end.
//		require_once( ABSPATH . 'wp-admin/includes/image.php' );
//		require_once( ABSPATH . 'wp-admin/includes/file.php' );
//		require_once( ABSPATH . 'wp-admin/includes/media.php' );
//
//		$attachment_id = media_handle_upload( $fisier, $candidate_id );
//
//		if ( is_wp_error( $attachment_id ) ) {
//
//			      echo 'error';
//			// There was an error uploading the image.
//		} else {
//			// The image was uploaded successfully!
//           //  echo  "image ok";
//			 echo wp_get_attachment_url($attachment_id);
//
//
//		}
//
//	} else {
//		 echo 'check fail kkd';
//		// The security check failed, maybe show the user an error.
//	}
//
//	die();
//}
//
//add_action( "wp_ajax_new_file_aplicatie", "new_file_aplicatie" );
//add_action( "wp_ajax_nopriv_new_file_aplicatie", "new_file_aplicatie" );
//


add_action( 'wp_ajax_questiondatahtml', 'questiondatahtml_update' );
add_action( 'wp_ajax_nopriv_questiondatahtml', 'questiondatahtml_update' );
function questiondatahtml_update() {
	if ( $_FILES ) {
		require_once( ABSPATH . 'wp-admin/includes/image.php' );
		require_once( ABSPATH . 'wp-admin/includes/file.php' );
		require_once( ABSPATH . 'wp-admin/includes/media.php' );
		$file_handler = 'updoc';
		$pid          = $_REQUEST['id'];
		$attach_id    = media_handle_upload( $file_handler, $pid );
		echo $attach_id;

	}


	wp_die();
}


add_action( 'admin_init', 'customize_meta_boxes' );
function customize_meta_boxes() {
	remove_meta_box( 'postcustom', 'post', 'normal' );
}

add_action( 'admin_head', 'showhiddencustomfields' );


// hide custom
function showhiddencustomfields() {
	echo "<style type='text/css'>.profile-view,a.thickbox.button.jobsearch-builder-thicbox { display: none !important;}</style>
";
}

//add_action('after_setup_theme','remove_core_updates');
//function remove_core_updates()
//{
//	if(! current_user_can('update_core')){return;}
//	add_action('init', create_function('$a',"remove_action( 'init', 'wp_version_check' );"),2);
//	add_filter('pre_option_update_core','__return_null');
//	add_filter('pre_site_transient_update_core','__return_null');
//}

// stop core updates
function remove_core_updates() {
	global $wp_version;

	return (object) array( 'last_checked' => time(), 'version_checked' => $wp_version, );
}

add_filter( 'pre_site_transient_update_core', 'remove_core_updates' );
add_filter( 'pre_site_transient_update_plugins', 'remove_core_updates' );
add_filter( 'pre_site_transient_update_themes', 'remove_core_updates' );


// modificare editor
function PREFIX_apply_acf_modifications() {
	?>
    <style>
        .acf-editor-wrap iframe {
            min-height: 0;
        }
    </style>
    <script>
        (function ($) {

            acf.add_filter('wysiwyg_tinymce_settings', function (mceInit, id, $field) {
                // enable autoresizing of the WYSIWYG editor
                mceInit.wp_autoresize_on = true;
                return mceInit;
            });

            acf.add_action('wysiwyg_tinymce_init', function (ed, id, mceInit, $field) {
                // reduce tinymce's min-height settings
                ed.settings.autoresize_min_height = 100;
                // reduce iframe's 'height' style to match tinymce settings
                $('.acf-editor-wrap iframe').css('height', '100px');
                $('.acf-field-wysiwyg').css('min-height', '200px');

            });
        })(jQuery)
    </script>
	<?php
}
add_action( 'acf/input/admin_footer', 'PREFIX_apply_acf_modifications' );


add_action( 'acf/save_post', 'adauga_job_lista' );
// adauga un job in lista de joburi
function adauga_job_lista( $post_id ) {

	// bail early if not a contact_form post
	if ( get_post_type( $post_id ) !== 'job' ) {

		return;

	}


	// bail early if editing in admin
	if ( is_admin() ) {
		return;
	}


	$user_id     = get_current_user_id();
	$employer_id = jobsearch_get_user_employer_id( $user_id );
	update_post_meta( $post_id, 'jobsearch_field_job_posted_by', $employer_id );
}

// remove admin bar
add_action( 'get_header', 'remove_admin_login_header' );
function remove_admin_login_header() {
	remove_action( 'wp_head', '_admin_bar_bump_cb' );
}


// extend default wp editor
add_filter( 'acf/fields/wysiwyg/toolbars', 'my_toolbars' );
function my_toolbars( $toolbars ) {

	$toolbars['Complet'][1] = [
		'formatselect',
		'bold',
		'italic',
		'strikethrough',
		'forecolor',
		'backcolor',
		'permanentpen',
		'formatpainter',
		'link',
		'image',
		'media',
		'pageembed',
		'alignleft',
		'aligncente',
		'alignright',
		'alignjustify',
		'numlist',
		'bullist',
		'outdent',
		'indent',
		'removeformat',
		'addcomment'
	];


	$toolbars['Full']    = array();
	$toolbars['Full'][1] = array(
		'bold',
		'italic',
		'underline',
		'bullist',
		'numlist',
		'alignleft',
		'aligncenter',
		'alignright',
		'alignjustify',
		'link',
		'unlink',
		'hr',
		'spellchecker',
		'wp_more',
		'wp_adv'
	);
	$toolbars['Full'][2] = array(
		'styleselect',
		'formatselect',
		'fontselect',
		'fontsizeselect',
		'forecolor',
		'pastetext',
		'removeformat',
		'charmap',
		'outdent',
		'indent',
		'undo',
		'redo',
		'wp_help'
	);


	// return $toolbars - IMPORTANT!
	return $toolbars;
}

// adauga titlu in tab
function my_acf_flexible_content_layout_title( $title, $field, $layout, $i ) {

	// remove layout title from text
	$title = '';

	// load text sub field
	if ( $date = get_sub_field( 'date_profil' ) ) {
		$text      = $date['nume_profil'];
		$descriere = $date['descriere_profil'];
		$title     .= '<strong>' . $text . '</strong>  -  ' . $descriere;

	}

	// return
	return $title;

}

// name
add_filter( 'acf/fields/flexible_content/layout_title', 'my_acf_flexible_content_layout_title', 10, 4 );

// return a file size
function getSize( $file ) {
	$bytes = filesize( $file );
	$s     = array( 'b', 'Kb', 'Mb', 'Gb' );
	$e     = floor( log( $bytes ) / log( 1024 ) );

	return sprintf( '%.2f ' . $s[ $e ], ( $bytes / pow( 1024, floor( $e ) ) ) );
}

add_action( 'wp_enqueue_scripts', 'librarie_mededia_enqueue_scripts' );
add_filter( 'ajax_query_attachments_args', 'librarie_mededia_filter_media' );
add_shortcode( 'librarie_mededia_front_upload', 'librarie_mededia_front_upload' );


 // incarca scripturi necesare
function librarie_mededia_enqueue_scripts() {
	wp_enqueue_media();
	wp_enqueue_script(
		'some-script',
		get_template_directory_uri() . '/js/media-uploader.js',
		array( 'jquery' ),
		null
	);
	wp_enqueue_script( 'media-grid' );
	wp_enqueue_script( 'media' );
}

// filter insures users only see their own media
function librarie_mededia_filter_media( $query ) {
	// admins get to see everything
	if ( ! current_user_can( 'manage_options' ) ) {
		$query['author'] = get_current_user_id();
	}

	return $query;
}

// initiate media library ( fisiere companie si candidat)
function librarie_mededia_front_upload( $args ) {
	// check if user can upload files
	if ( current_user_can( 'upload_files' ) ) {
		$str = __( 'Librarie fisiere', 'text-domain' );

		return '<a href="javascript:void(0);" id="frontend-button"  style="position: relative; z-index: 1; float: right;">Librarie fisiere</a>';
	}

	return __( 'Please Login To Upload', 'text-domain' );
}

// get fields for labels
function my_acf_get_fields_in_group( $group_id ) {
	$acf_meta   = get_post_custom( $group_id );
	$acf_fields = array();

	foreach ( $acf_meta as $key => $val ) {
		if ( preg_match( "/^field_/", $key ) ) {
			$acf_fields[ $key ] = $val;
		}
	}

	return $acf_fields;
}

// debugging functions

function nice_var_export( $var ) {
	echo "<pre>";
	var_export( $var );
	echo "</pre>";
}

// debugging functions

function nice_var_dump( $var ) {
	echo "<pre>";
	var_dump( $var );
	echo "</pre>";
}

// debugging functions
function nice_print_r( $var ) {
	echo "<pre>";
	print_r( $var );
	echo "</pre>";
}

// define new frontend admin role
add_role(
	'managero_admin',
	__( 'Managero Admin' ),
	array(
		'read'       => true,  // true allows this capability
		'edit_posts' => true,
	)
);

// insert job
function single_job_insert() {
    $data = [];

    $job_data = $_REQUEST['template'];

    $new_job = [
        'post_title'    => $job_data['post_job'],
       'post_status'   => 'publish',
        'post_type'     => 'job'
    ];

    //insert the the job into database
    //store post id for job data
    $pid = wp_insert_post($new_job);

    // save job data
    update_post_meta($pid,'job_data_set',$job_data);

    $data['url'] = get_the_permalink($pid);

    echo json_encode($data);

    die;
}
add_action( 'wp_ajax_single_job_insert', 'single_job_insert' );    // If called from admin panel
add_action( 'wp_ajax_nopriv_single_job_insert', 'single_job_insert' );


