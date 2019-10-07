<?php

// Register store.
acf_register_store( 'fields' )->prop( 'multisite', true );

/**
 * acf_get_field
 *
 * Retrieves a field for the given identifier.
 *
 * @date    17/1/19
 *
 * @param    (int|string) $id The field ID, key or name.
 *
 * @return    (array|false) The field array.
 * @since    5.7.10
 *
 */
function acf_get_field( $id = 0 ) {

	// Allow WP_Post to be passed.
	if ( is_object( $id ) ) {
		$id = $id->ID;
	}

	// Check store.
	$store = acf_get_store( 'fields' );
	if ( $store->has( $id ) ) {
		return $store->get( $id );
	}

	// Check local fields first.
	if ( acf_is_local_field( $id ) ) {
		$field = acf_get_local_field( $id );

		// Then check database.
	} else {
		$field = acf_get_raw_field( $id );
	}

	// Bail early if no field.
	if ( ! $field ) {
		return false;
	}

	// Validate field.
	$field = acf_validate_field( $field );

	// Set input prefix.
	$field['prefix'] = 'acf';

	/**
	 * Filters the $field array after it has been loaded.
	 *
	 * @date    12/02/2014
	 *
	 * @param array The field array.
	 *
	 * @since    5.0.0
	 *
	 */
	$field = apply_filters( "acf/load_field", $field );

	// Store field using aliasses to also find via key, ID and name.
	$store->set( $field['key'], $field );
	$store->alias( $field['key'], $field['ID'], $field['name'] );

	// Return.
	return $field;
}

// Register variation.
acf_add_filter_variations( 'acf/load_field', array( 'type', 'name', 'key' ), 0 );

/**
 * acf_get_raw_field
 *
 * Retrieves raw field data for the given identifier.
 *
 * @date    18/1/19
 *
 * @param    (int|string) $id The field ID, key or name.
 *
 * @return    (array|false) The field array.
 * @since    5.7.10
 *
 */
function acf_get_raw_field( $id = 0 ) {

	// Get raw field from database.
	$post = acf_get_field_post( $id );
	if ( ! $post ) {
		return false;
	}

	// Bail early if incorrect post type.
	if ( $post->post_type !== 'acf-field' ) {
		return false;
	}

	// Unserialize post_content.
	$field = (array) maybe_unserialize( $post->post_content );

	// update attributes
	$field['ID']         = $post->ID;
	$field['key']        = $post->post_name;
	$field['label']      = $post->post_title;
	$field['name']       = $post->post_excerpt;
	$field['menu_order'] = $post->menu_order;
	$field['parent']     = $post->post_parent;

	// Return field.
	return $field;
}

/**
 * acf_get_field_post
 *
 * Retrieves the field's WP_Post object.
 *
 * @date    18/1/19
 *
 * @param    (int|string) $id The field ID, key or name.
 *
 * @return    (array|false) The field array.
 * @since    5.7.10
 *
 */
function acf_get_field_post( $id = 0 ) {

	// Get post if numeric.
	if ( is_numeric( $id ) ) {
		return get_post( $id );

		// Search posts if is string.
	} elseif ( is_string( $id ) ) {

		// Determine id type.
		$type = acf_is_field_key( $id ) ? 'key' : 'name';

		// Try cache.
		$cache_key = acf_cache_key( "acf_get_field_post:$type:$id" );
		$post_id   = wp_cache_get( $cache_key, 'acf' );
		if ( $post_id === false ) {

			// Query posts.
			$posts = get_posts( array(
				'posts_per_page'         => 1,
				'post_type'              => 'acf-field',
				'orderby'                => 'menu_order title',
				'order'                  => 'ASC',
				'suppress_filters'       => false,
				'cache_results'          => true,
				'update_post_meta_cache' => false,
				'update_post_term_cache' => false,
				"acf_field_$type"        => $id
			) );

			// Update $post_id with a non false value.
			$post_id = $posts ? $posts[0]->ID : 0;

			// Update cache.
			wp_cache_set( $cache_key, $post_id, 'acf' );
		}

		// Check $post_id and return the post when possible.
		if ( $post_id ) {
			return get_post( $post_id );
		}
	}

	// Return false by default.
	return false;
}

/**
 * acf_is_field_key
 *
 * Returns true if the given identifier is a field key.
 *
 * @date    6/12/2013
 *
 * @param string $id The identifier.
 *
 * @return    bool
 * @since    5.0.0
 *
 */
function acf_is_field_key( $id = '' ) {

	// Check if $id is a string starting with "field_".
	if ( is_string( $id ) && substr( $id, 0, 6 ) === 'field_' ) {
		return true;
	}

	/**
	 * Filters whether the $id is a field key.
	 *
	 * @date    23/1/19
	 *
	 * @param bool $bool The result.
	 * @param string $id The identifier.
	 *
	 * @since    5.7.10
	 *
	 */
	return apply_filters( 'acf/is_field_key', false, $id );
}

/**
 * acf_validate_field
 *
 * Ensures the given field valid.
 *
 * @date    18/1/19
 *
 * @param array $field The field array.
 *
 * @return    array
 * @since    5.7.10
 *
 */
function acf_validate_field( $field = array() ) {

	// Bail early if already valid.
	if ( is_array( $field ) && ! empty( $field['_valid'] ) ) {
		return $field;
	}

	// Apply defaults.
	$field = wp_parse_args( $field, array(
		'ID'                => 0,
		'key'               => '',
		'label'             => '',
		'name'              => '',
		'prefix'            => '',
		'type'              => 'text',
		'value'             => null,
		'menu_order'        => 0,
		'instructions'      => '',
		'required'          => false,
		'id'                => '',
		'class'             => '',
		'conditional_logic' => false,
		'parent'            => 0,
		'wrapper'           => array()
		//'attributes'		=> array()
	) );

	// Add backwards compatibility for wrapper attributes.
	// Todo: Remove need for this.
	$field['wrapper'] = wp_parse_args( $field['wrapper'], array(
		'width' => '',
		'class' => '',
		'id'    => ''
	) );

	// Store backups.
	$field['_name']  = $field['name'];
	$field['_valid'] = 1;

	/**
	 * Filters the $field array to validate settings.
	 *
	 * @date    12/02/2014
	 *
	 * @param array $field The field array.
	 *
	 * @since    5.0.0
	 *
	 */
	$field = apply_filters( "acf/validate_field", $field );

	// return
	return $field;
}

// Register variation.
acf_add_filter_variations( 'acf/validate_field', array( 'type' ), 0 );

/**
 * acf_get_valid_field
 *
 * Ensures the given field valid.
 *
 * @date        28/09/13
 *
 * @param array $field The field array.
 *
 * @return    array
 * @since        5.0.0
 *
 */
function acf_get_valid_field( $field = false ) {
	return acf_validate_field( $field );
}

/**
 * acf_translate_field
 *
 * Translates a field's settings.
 *
 * @date    8/03/2016
 *
 * @param array $field The field array.
 *
 * @return    array
 * @since    5.3.2
 *
 */
function acf_translate_field( $field = array() ) {

	// Get settings.
	$l10n            = acf_get_setting( 'l10n' );
	$l10n_textdomain = acf_get_setting( 'l10n_textdomain' );

	// Translate field settings if textdomain is set.
	if ( $l10n && $l10n_textdomain ) {

		$field['label']        = acf_translate( $field['label'] );
		$field['instructions'] = acf_translate( $field['instructions'] );

		/**
		 * Filters the $field array to translate strings.
		 *
		 * @date    12/02/2014
		 *
		 * @param array $field The field array.
		 *
		 * @since    5.0.0
		 *
		 */
		$field = apply_filters( "acf/translate_field", $field );
	}

	// Return field.
	return $field;
}

// Register variation.
acf_add_filter_variations( 'acf/translate_field', array( 'type' ), 0 );

// Translate fields passing through validation.
add_action( 'acf/validate_field', 'acf_translate_field' );

/**
 * acf_get_fields
 *
 * Returns and array of fields for the given $parent.
 *
 * @date    30/09/13
 *
 * @param    (int|string|array) $parent The field group or field settings. Also accepts the field group ID or key.
 *
 * @return    array
 * @since    5.0.0
 *
 */
function acf_get_fields( $parent ) {

	// Allow field group selector as $parent.
	if ( ! is_array( $parent ) ) {
		$parent = acf_get_field_group( $parent );
		if ( ! $parent ) {
			return array();
		}
	}

	// Vars.
	$fields = array();

	// Check local fields first.
	if ( acf_have_local_fields( $parent['key'] ) ) {
		$raw_fields = acf_get_local_fields( $parent['key'] );
		foreach ( $raw_fields as $raw_field ) {
			$fields[] = acf_get_field( $raw_field['key'] );
		}

		// Then check database.
	} else {
		$raw_fields = acf_get_raw_fields( $parent['ID'] );
		foreach ( $raw_fields as $raw_field ) {
			$fields[] = acf_get_field( $raw_field['ID'] );
		}
	}

	/**
	 * Filters the $fields array.
	 *
	 * @date    12/02/2014
	 *
	 * @param array $fields The array of fields.
	 *
	 * @since    5.0.0
	 *
	 */
	$fields = apply_filters( 'acf/load_fields', $fields, $parent );

	// Return fields
	return $fields;
}

/**
 * acf_get_raw_fields
 *
 * Returns and array of raw field data for the given parent id.
 *
 * @date    18/1/19
 *
 * @param int $id The field group or field id.
 *
 * @return    array
 * @since    5.7.10
 *
 */
function acf_get_raw_fields( $id = 0 ) {

	// Try cache.
	$cache_key = acf_cache_key( "acf_get_field_posts:$id" );
	$post_ids  = wp_cache_get( $cache_key, 'acf' );
	if ( $post_ids === false ) {

		// Query posts.
		$posts = get_posts( array(
			'posts_per_page'         => - 1,
			'post_type'              => 'acf-field',
			'orderby'                => 'menu_order',
			'order'                  => 'ASC',
			'suppress_filters'       => true, // DO NOT allow WPML to modify the query
			'cache_results'          => true,
			'update_post_meta_cache' => false,
			'update_post_term_cache' => false,
			'post_parent'            => $id,
			'post_status'            => array( 'publish', 'trash' ),
		) );

		// Update $post_ids with a non false value.
		$post_ids = array();
		foreach ( $posts as $post ) {
			$post_ids[] = $post->ID;
		}

		// Update cache.
		wp_cache_set( $cache_key, $post_ids, 'acf' );
	}

	// Loop over ids and populate array of fields.
	$fields = array();
	foreach ( $post_ids as $post_id ) {
		$fields[] = acf_get_raw_field( $post_id );
	}

	// Return fields.
	return $fields;
}

/**
 * acf_get_field_count
 *
 * Return the number of fields for the given field group.
 *
 * @date    17/10/13
 *
 * @param array $parent The field group or field array.
 *
 * @return    int
 * @since    5.0.0
 *
 */
function acf_get_field_count( $parent ) {

	// Check local fields first.
	if ( acf_have_local_fields( $parent['key'] ) ) {
		$raw_fields = acf_get_local_fields( $parent['key'] );

		// Then check database.
	} else {
		$raw_fields = acf_get_raw_fields( $parent['ID'] );
	}

	/**
	 * Filters the counted number of fields.
	 *
	 * @date    12/02/2014
	 *
	 * @param int $count The number of fields.
	 * @param array $parent The field group or field array.
	 *
	 * @since    5.0.0
	 *
	 */
	return apply_filters( 'acf/get_field_count', count( $raw_fields ), $parent );
}

/**
 * acf_clone_field
 *
 * Allows customization to a field when it is cloned. Used by the clone field.
 *
 * @date    8/03/2016
 *
 * @param array $field The field being cloned.
 * @param array $clone_field The clone field.
 *
 * @return    array
 * @since    5.3.2
 *
 */
function acf_clone_field( $field, $clone_field ) {

	// Add reference to the clone field.
	$field['_clone'] = $clone_field['key'];

	/**
	 * Filters the $field array when it is being cloned.
	 *
	 * @date    12/02/2014
	 *
	 * @param array $field The field array.
	 * @param array $clone_field The clone field array.
	 *
	 * @since    5.0.0
	 *
	 */
	$field = apply_filters( "acf/clone_field", $field, $clone_field );

	// Return field.
	return $field;
}

// Register variation.
acf_add_filter_variations( 'acf/clone_field', array( 'type' ), 0 );

/**
 * acf_prepare_field
 *
 * Prepare a field for input.
 *
 * @date    20/1/19
 *
 * @param array $field The field array.
 *
 * @return    array
 * @since    5.7.10
 *
 */
function acf_prepare_field( $field ) {

	// Bail early if already prepared.
	if ( ! empty( $field['_prepare'] ) ) {
		return $field;
	}

	// Use field key to override input name.
	if ( $field['key'] ) {
		$field['name'] = $field['key'];
	}

	// Use field prefix to modify input name.
	if ( $field['prefix'] ) {
		$field['name'] = "{$field['prefix']}[{$field['name']}]";
	}

	// Generate id attribute from name.
	$field['id'] = acf_idify( $field['name'] );

	// Add state to field.
	$field['_prepare'] = true;

	/**
	 * Filters the $field array.
	 *
	 * Allows developers to modify field settings or return false to remove field.
	 *
	 * @date    12/02/2014
	 *
	 * @param array $field The field array.
	 *
	 * @since    5.0.0
	 *
	 */
	$field = apply_filters( "acf/prepare_field", $field );

	// return
	return $field;
}

// Register variation.
acf_add_filter_variations( 'acf/prepare_field', array( 'type', 'name', 'key' ), 0 );

/**
 * acf_render_fields
 *
 * Renders an array of fields. Also loads the field's value.
 *
 * @date    8/10/13
 *
 * @param array $fields An array of fields.
 * @param    (int|string) $post_id The post ID to load values from.
 * @param string $element The wrapping element type.
 * @param string $instruction The instruction render position (label|field).
 *
 * @return    void
 * @since    5.6.9 Changed parameter order.
 *
 * @since    5.0.0
 */
function acf_render_fields( $fields, $post_id = 0, $el = 'div', $instruction = 'label', $template_load = false, $fields_type = false, $temp_fields = false ) {

	// Parameter order changed in ACF 5.6.9.
	if ( is_array( $post_id ) ) {
		$args    = func_get_args();
		$fields  = $args[1];
		$post_id = $args[0];
	}

	/**
	 * Filters the $fields array before they are rendered.
	 *
	 * @date    12/02/2014
	 *
	 * @param array $fields An array of fields.
	 * @param    (int|string) $post_id The post ID to load values from.
	 *
	 * @since    5.0.0
	 *
	 */
	$fields = apply_filters( 'acf/pre_render_fields', $fields, $post_id );

	// Filter our false results.
	$fields = array_filter( $fields );

	// Loop over and render fields.
	if ( $fields ) {


		if ( $template_load == ! false && $template_load == ! null ) {
			switch ( $fields_type ) {
				case "job_profile":

					$fields[0]['value'] = $template_load['post_job'];
					$fields[1]['value'] = [
						'field_5d4167a94ce32' => $template_load['nivelul_ierarhic'],
						'field_5d4167d14ce33' => $template_load['cui_este_subordonat'],
						'field_5d4167ff4ce34' => $template_load['subordonati_total']
					];

					$fields[2]['value'] = $template_load['domeniu'];
					$fields[3]['value'] = $template_load['locatia_jobului'][1];
					$fields[4]['value'] = $template_load['descrierea_sintetica_a_jobului'];
					$fields[5]['value'] = $template_load['cerinte_job'];


					$fields[6]['value'] = [
						'field_5d4168c24ce3a' => $template_load['salariu_oferit_job'],
						'field_5d4168d04ce3b' => $template_load['descriere_oferta']
					];

					$fields[7]['value'] = [
						'field_5d41690c4ce3d' => $template_load['mod_aplicare'],
						'field_5d4aab48406bb' => $template_load['instructiuni_aplicare']
					];

					$fisiere = [];
					$x       = 0;

					foreach ( $template_load['fisiere'] as $key => $fisier ) {
						$fisiere[ $x ] = [
							'field_5d416a7129f86' => $fisier,
							'field_5d416a8729f87' => $key
						];
						$x ++;
					}
					$fields[8]['value'] = $fisiere;

					$fields[9]['value']  = $template_load['galerie'];
					$fields[10]['value'] = $template_load['comentarii_job'];
					$fields[11]['value'] = $template_load['imagine_cover'];
					$fields[12]['value'] = [
						'field_5d416acac61e8' => $template_load['nume_template'],
						'field_5d416ad5c61e9' => $template_load['descriere_template']
					];
					break;
				case "company_profile":

					$fields[0]['value'] = [
						'field_5d09e5f9a082d' => $template_load['nume_companie'],
						'field_5d09e5f9a0876' => $template_load['nume_alternativ'],
						'field_5d09e5f9a08b8' => $template_load['origine'][1],
						'field_5d09e5f9a093b' => $template_load['localizare'][1],
						'field_5d09e5f9a08fa' => $template_load['extindere'],
						'field_5d09e5f9a097d' => $template_load['head_office'],
						'field_5d09e5f9a09be' => $template_load['cifra_de_afaceri'],
						'field_5d09e5f9a0a00' => $template_load['nr_de_angajati'],
					];

					$fields[1]['value'] = $template_load['date_visibile'];
					$fields[2]['value'] = $template_load['descriere_companie'];
					$fields[3]['value'] = $template_load['descriere_domeniu_sintetic'];
					$fields[4]['value'] = $template_load['descriere_domeniu_larg'];
					$fields[5]['value'] = $template_load['note_comentarii'];


					$links = [];
					$x     = 0;

					foreach ( $template_load['link-uri'] as $key => $link ) {
						$links[ $x ] = [
							'field_5d09e5f9edc83' => $link,
							'field_5d09e5f9edcd4' => $key
						];
						$x ++;
					}


					$fields[6]['value'] = $links;

					$fields[7]['value'] = $template_load['imagine_profil_companie'];

					$fields[8]['value'] = $template_load['imagine_principala'];

					$fields[9]['value'] = [
						'field_5d35a47a35468' => $template_load['nume_template'],
						'field_5d35a9dc3b621' => $template_load['descriere_template']
					];


					break;
				case "anunt_template":

					$profile_companie_t = get_post_meta( $post_id, 'companie_company_profiles', true );
					$profile_job_t      = get_post_meta( $post_id, 'companie_job_profiles', true );

					$profile_companie[0] = 'alege un profil de companie';
					$profile_job[0]      = 'alege un profil de job';

					$profile_companie      = array_merge( $profile_companie, array_keys( unserialize(base64_decode($profile_companie_t)) ) );
					$profile_job           = array_merge( $profile_job, array_keys(unserialize(base64_decode($profile_job_t)) ) );

					$profile_companie_last = [];
					$profile_job_last      = [];

					foreach ( $profile_companie as $ceva ) {
						$profile_companie_last[ $ceva ] = $ceva;

					}
					foreach ( $profile_job as $ceva ) {
						$profile_job_last[ $ceva ] = $ceva;
					}

					$fields[1]['sub_fields'][0]['choices']  = $profile_companie_last;
					$fields[14]['sub_fields'][0]['choices'] = $profile_job_last;


					$fields[2]['value'] = $template_load['date_visibile'];

					$fields[3]['value'] = [
						'field_5d2ccd9f3cd81' => $template_load['nume_companie'],
						'field_5d2ccdd53cd82' => $template_load['nume_alternativ'],
						'field_5d2ccddd3cd83' => $template_load['origine'][1],
						'field_5d2cd0f8f5198' => $template_load['localizare'][1],
						'field_5d2ccdfa3cd84' => $template_load['extindere'],
						'field_5d2cce0e3cd85' => $template_load['head_office'],
						'field_5d2cce2b3cd86' => $template_load['cifra_de_afaceri'],
						'field_5d2cd3623855a' => $template_load['numar_angajati'],
					];

					$fields[4]['value'] = $template_load['descriere_companie'];

					$fields[5]['value'] = $template_load['descriere_domeniu_sintetic'];

					$fields[6]['value'] = $template_load['descriere_domeniu_larg'];

					$fields[7]['value'] = $template_load['note_comentarii'];

					$links = [];
					$x     = 0;

					foreach ( $template_load['link-uri'] as $key => $link ) {
						$links[ $x ] = [
							'field_5d3a1832927bb' => $link,
							'field_5d3a183f927bc' => $key
						];
						$x ++;
					}


					$fields[8]['value'] = $links;

					$fields[11]['value'] = [
						'field_5d48264f251ea' => $template_load['nume_profil_companie'],
						'field_5d482658251eb' => $template_load['descriere_profil_companie']
					];


// end companie start job

					$fields[15]['value'] = $template_load['post_job'];
					$fields[16]['value'] = [
						'field_5d0a55154bb02' => $template_load['nivelul_ierarhic'],
						'field_5d0a55504bb03' => $template_load['cui_este_subordonat'],
						'field_5d0a55f14bb04' => $template_load['subordonati_total'],
					];

					$fields[17]['value'] = $template_load['domeniu'];

					$fields[18]['value'] = $template_load['locatia_jobului'][1];

					$fields[19]['value'] = $template_load['descrierea_sintetica_a_jobului'];

					$fields[20]['value'] = $template_load['cerinte_job'];


					$fields[21]['value'] = [
						'field_5d0a5ddaf57ba' => $template_load['salariu_oferit_job'],
						'field_5d0a5dfaf57bb' => $template_load['descriere_oferta']
					];

					$fields[22]['value'] = [
						'field_5d0a603338bf9' => $template_load['mod_aplicare'],
						'field_5d0a607a38bfa' => $template_load['instructiuni_aplicare']
					];

					$fields[23]['value'] = $template_load['comentarii'];

					$fields[26]['value'] = [
						'field_5d4823fd22a1e' => $template_load['nume_profil_job'],
						'field_5d48241722a1f' => $template_load['descriere_profil_job']
					];

					$fields[29]['value'] = $template_load['utilizeaza_profil_minimal'];
					$fields[30]['value'] = $template_load['afiseaza_profil__minimal'];
					$fields[31]['value'] = [
						'field_5d78aa8db9810' => $template_load['varsta_scm'],
						'field_5d78aa9db9811' => $template_load['sex'],
						'field_5d78ab18b9812' => $template_load['salariu_minim_scm']

					];


					$domenii    = get_field( 'domeniu', '1648' );
					$x          = 0;
					$sub_fields = [];

					foreach ( $domenii as $domeniu ) {
						$tempid = mt_rand( 1111111111111, 9999999999999 );

						$special_name = str_replace( ' ', '_', preg_replace( "/[^a-zA-Z0-9\s]/", "", $domeniu['nume_domeniu'] ) );

						$sub_fields[ $x ] = array(
							'ID'                => $tempid,
							'key'               => 'field_' . $special_name,
							'label'             => $domeniu['nume_domeniu'],
							'name'              => $domeniu['nume_domeniu'],
							'prefix'            => 'acf',
							'type'              => 'radio',
							'value'             => $template_load['profil_scm'][ $special_name ],
							'menu_order'        => 0,
							'instructions'      => '',
							'required'          => 0,
							'id'                => '',
							'class'             => 'domeniu',
							'conditional_logic' => 0,
							'parent'            => 1682,
							'wrapper'           =>
								array(
									'width' => '',
									'class' => 'domeniu_wrap',
									'id'    => '',
								),
							'acfe_permissions'  => '',
							'choices'           =>
								array(
									1 => '1',
									2 => '2',
									3 => '3',
									4 => '4',
									5 => '5',
								),
							'allow_null'        => 1,
							'other_choice'      => 0,
							'default_value'     => '',
							'layout'            => 'horizontal',
							'return_format'     => 'value',
							'acfe_validate'     => '',
							'acfe_update'       => '',
							'save_other_choice' => 0,
							'_name'             => $special_name,
							'_valid'            => 1,
						);

						$x ++;
						if ( count( $domeniu['sub_domeniu'] ) > 0 ) {
							foreach ( $domeniu['sub_domeniu'] as $subdomeniu ) {

								$tempid       = mt_rand( 1111111111111, 9999999999999 );
								$special_name = str_replace( ' ', '_', preg_replace( "/[^a-zA-Z0-9\s]/", "", $subdomeniu['nume_subdomeniu'] ) );

								$sub_fields[ $x ] = array(
									'ID'                => $tempid,
									'key'               => 'field_' . $special_name,
									'label'             => $subdomeniu['nume_subdomeniu'],
									'name'              => $subdomeniu['nume_domeniu'],
									'prefix'            => 'acf',
									'type'              => 'radio',
									'value'             => $template_load['profil_scm'][ $special_name ],
									'menu_order'        => 0,
									'instructions'      => '',
									'required'          => 0,
									'id'                => '',
									'class'             => 'subdomeniu',
									'conditional_logic' => 0,
									'parent'            => 1682,
									'wrapper'           =>
										array(
											'width' => '',
											'class' => 'subdomeniu_wrap',
											'id'    => '',
										),
									'acfe_permissions'  => '',
									'choices'           =>
										array(
											1 => '1',
											2 => '2',
											3 => '3',
											4 => '4',
											5 => '5',
										),
									'allow_null'        => 1,
									'other_choice'      => 0,
									'default_value'     => '',
									'layout'            => 'horizontal',
									'return_format'     => 'value',
									'acfe_validate'     => '',
									'acfe_update'       => '',
									'save_other_choice' => 0,
									'_name'             => $special_name,
									'_valid'            => 1,
								);


								$x ++;
							}
						}
					}

					$valori = [];


					$user_id      = get_current_user_id();
					$candidate_id = jobsearch_get_user_candidate_id( $user_id );

					$fields_candidat = get_post_meta( $candidate_id, "profil_scm", true );
					$fields_candidat = unserialize( $fields_candidat );


					foreach ( $sub_fields as $field ) {
						$valori[ $field['key'] ] = $fields_candidat[ $field['_name'] ];
					}
					$f_value = [];
					foreach ( $template_load['profil_scm'] as $field => $value ) {
						$f_value[ 'field_' . $field ] = $value;
					}
					$fields[32]['sub_fields'] = $sub_fields;
					$fields[32]['value']      = $f_value;

					$fields[35]['value'] = $template_load['imagine_cover'];

					$fields[36]['value'] = $template_load['galerie'];

					$fisiere = [];
					$x       = 0;

					foreach ( $template_load['fisiere'] as $key => $fisier ) {
						$fisiere[ $x ] = [
							'field_5d8b4305671eb' => $fisier,
							'field_5d8b4321671ec' => $key
						];
						$x ++;
					}


					$fields[37]['value'] = $fisiere;

					$fields[38]['value'] = $template_load['logo_companie'];

					$fields[40]['value'] = [
						'field_5d496a2e7165d' => $template_load['nume_template'],
						'field_5d496ab97165e' => $template_load['descriere_template']
					];

					break;
				case "job_profile_combo":
					$profile_companie_t = get_post_meta( $post_id, 'companie_company_profiles', true );
					$profile_job_t      = get_post_meta( $post_id, 'companie_job_profiles', true );

					$profile_companie[0] = 'alege un profil de companie';
					$profile_job[0]      = 'alege un profil de job';

					$profile_companie      = array_merge( $profile_companie, array_keys( unserialize(base64_decode($profile_companie_t)) ) );
					$profile_job           = array_merge( $profile_job, array_keys(unserialize(base64_decode($profile_job_t)) ) );

					$profile_companie_last = [];
					$profile_job_last      = [];

					foreach ( $profile_companie as $ceva ) {
						$profile_companie_last[ $ceva ] = $ceva;
					}
					foreach ( $profile_job as $ceva ) {
						$profile_job_last[ $ceva ] = $ceva;
					}

					$fields[1]['sub_fields'][0]['choices']  = $profile_companie_last;
					$fields[14]['sub_fields'][0]['choices'] = $profile_job_last;

					$fields[2]['value'] = $temp_fields['date_visibile'];

					$fields[3]['value'] = [
						'field_5d2ccd9f3cd81' => $temp_fields['nume_companie'],
						'field_5d2ccdd53cd82' => $temp_fields['nume_alternativ'],
						'field_5d2ccddd3cd83' => $temp_fields['origine'][1],
						'field_5d2cd0f8f5198' => $temp_fields['localizare'][1],
						'field_5d2ccdfa3cd84' => $temp_fields['extindere'],
						'field_5d2cce0e3cd85' => $temp_fields['head_office'],
						'field_5d2cce2b3cd86' => $temp_fields['cifra_de_afaceri'],
						'field_5d2cd3623855a' => $temp_fields['numar_angajati'],
					];

					$fields[4]['value'] = $temp_fields['descriere_companie'];

					$fields[5]['value'] = $temp_fields['descriere_domeniu_sintetic'];

					$fields[6]['value'] = $temp_fields['descriere_domeniu_larg'];

					$fields[7]['value'] = $temp_fields['note_comentarii'];

					$links = [];
					$x     = 0;

					foreach ( $temp_fields['link-uri'] as $key => $link ) {
						$links[ $x ] = [
							'field_5d3a1832927bb' => $link,
							'field_5d3a183f927bc' => $key
						];
						$x ++;
					}


					$fields[8]['value'] = $links;


					$fields[11]['value'] = [
						'field_5d48264f251ea' => $temp_fields['nume_profil_companie'],
						'field_5d482658251eb' => $temp_fields['descriere_profil_companie']
					];


					// end job tab


					$fields[15]['value'] = $template_load['post_job'];
					$fields[16]['value'] = [
						'field_5d0a55154bb02' => $template_load['nivelul_ierarhic'],
						'field_5d0a55504bb03' => $template_load['cui_este_subordonat'],
						'field_5d0a55f14bb04' => $template_load['subordonati_total'],
					];

					$fields[17]['value'] = $template_load['domeniu'];

					$fields[18]['value'] = $template_load['locatia_jobului'][1];

					$fields[19]['value'] = $template_load['descrierea_sintetica_a_jobului'];

					$fields[20]['value'] = $template_load['cerinte_job'];


					$fields[21]['value'] = [
						'field_5d0a5ddaf57ba' => $template_load['salariu_oferit_job'],
						'field_5d0a5dfaf57bb' => $template_load['descriere_oferta']
					];

					$fields[22]['value'] = [
						'field_5d0a603338bf9' => $template_load['mod_aplicare'],
						'field_5d0a607a38bfa' => $template_load['instructiuni_aplicare']
					];

					$fields[23]['value'] = $template_load['comentarii'];



					$fields[26]['value'] = [
						'field_5d4823fd22a1e' => $template_load['nume_profil_job'],
						'field_5d48241722a1f' => $template_load['descriere_profil_job']
					];



					$fields[29]['value'] = $template_load['utilizeaza_profil_minimal'];
					$fields[30]['value'] = $template_load['afiseaza_profil__minimal'];

					$fields[31]['value'] = [
						'field_5d78aa8db9810' => $template_load['varsta_scm'],
						'field_5d78aa9db9811' => $template_load['sex'],
						'field_5d78ab18b9812' => $template_load['salariu_minim_scm']
					];


					$domenii    = get_field( 'domeniu', '1648' );
					$x          = 0;
					$sub_fields = [];

					foreach ( $domenii as $domeniu ) {
						$tempid = mt_rand( 1111111111111, 9999999999999 );

						$special_name = str_replace( ' ', '_', preg_replace( "/[^a-zA-Z0-9\s]/", "", $domeniu['nume_domeniu'] ) );

						$sub_fields[ $x ] = array(
							'ID'                => $tempid,
							'key'               => 'field_' . $special_name,
							'label'             => $domeniu['nume_domeniu'],
							'name'              => $domeniu['nume_domeniu'],
							'prefix'            => 'acf',
							'type'              => 'radio',
							'value'             => $template_load['profil_scm'][ $special_name ],
							'menu_order'        => 0,
							'instructions'      => '',
							'required'          => 0,
							'id'                => '',
							'class'             => 'domeniu',
							'conditional_logic' => 0,
							'parent'            => 1682,
							'wrapper'           =>
								array(
									'width' => '',
									'class' => 'domeniu_wrap',
									'id'    => '',
								),
							'acfe_permissions'  => '',
							'choices'           =>
								array(
									1 => '1',
									2 => '2',
									3 => '3',
									4 => '4',
									5 => '5',
								),
							'allow_null'        => 1,
							'other_choice'      => 0,
							'default_value'     => '',
							'layout'            => 'horizontal',
							'return_format'     => 'value',
							'acfe_validate'     => '',
							'acfe_update'       => '',
							'save_other_choice' => 0,
							'_name'             => $special_name,
							'_valid'            => 1,
						);

						$x ++;
						if ( count( $domeniu['sub_domeniu'] ) > 0 ) {
							foreach ( $domeniu['sub_domeniu'] as $subdomeniu ) {

								$tempid       = mt_rand( 1111111111111, 9999999999999 );
								$special_name = str_replace( ' ', '_', preg_replace( "/[^a-zA-Z0-9\s]/", "", $subdomeniu['nume_subdomeniu'] ) );

								$sub_fields[ $x ] = array(
									'ID'                => $tempid,
									'key'               => 'field_' . $special_name,
									'label'             => $subdomeniu['nume_subdomeniu'],
									'name'              => $subdomeniu['nume_domeniu'],
									'prefix'            => 'acf',
									'type'              => 'radio',
									'value'             => $template_load['profil_scm'][ $special_name ],
									'menu_order'        => 0,
									'instructions'      => '',
									'required'          => 0,
									'id'                => '',
									'class'             => 'subdomeniu',
									'conditional_logic' => 0,
									'parent'            => 1682,
									'wrapper'           =>
										array(
											'width' => '',
											'class' => 'subdomeniu_wrap',
											'id'    => '',
										),
									'acfe_permissions'  => '',
									'choices'           =>
										array(
											1 => '1',
											2 => '2',
											3 => '3',
											4 => '4',
											5 => '5',
										),
									'allow_null'        => 1,
									'other_choice'      => 0,
									'default_value'     => '',
									'layout'            => 'horizontal',
									'return_format'     => 'value',
									'acfe_validate'     => '',
									'acfe_update'       => '',
									'save_other_choice' => 0,
									'_name'             => $special_name,
									'_valid'            => 1,
								);


								$x ++;
							}
						}
					}

					$valori = [];


					$user_id         = get_current_user_id();
					$candidate_id    = jobsearch_get_user_candidate_id( $user_id );
					$fields_candidat = get_post_meta( $candidate_id, "profil_scm", true );
					$fields_candidat = unserialize( $fields_candidat );


					foreach ( $sub_fields as $field ) {
						$valori[ $field['key'] ] = $fields_candidat[ $field['_name'] ];
					}
					$f_value = [];
					foreach ( $template_load['profil_scm'] as $field => $value ) {
						$f_value[ 'field_' . $field ] = $value;
					}
					$fields[32]['sub_fields'] = $sub_fields;
					$fields[32]['value']      = $f_value;

					$fields[35]['value'] = $template_load['imagine_cover'];

					$fields[36]['value'] = $template_load['galerie'];

					$fisiere = [];
					$x       = 0;

					foreach ( $template_load['fisiere'] as $key => $fisier ) {
						$fisiere[ $x ] = [
							'field_5d8b4305671eb' => $fisier,
							'field_5d8b4321671ec' => $key
						];
						$x ++;
					}


					$fields[37]['value'] = $fisiere;

					$fields[38]['value'] = $template_load['logo_companie'];

					$fields[40]['value'] = [
						'field_5d496a2e7165d' => $template_load['nume_template'],
						'field_5d496ab97165e' => $template_load['descriere_template']
					];


					break;
				case "company_profile_combo":

					$profile_companie_t = get_post_meta( $post_id, 'companie_company_profiles', true );
					$profile_job_t      = get_post_meta( $post_id, 'companie_job_profiles', true );

					$profile_companie[0] = 'alege un profil de companie';

					$profile_job[0] = 'alege un profil de job';

					$profile_companie      = array_merge( $profile_companie, array_keys( unserialize(base64_decode($profile_companie_t)) ) );
					$profile_job           = array_merge( $profile_job, array_keys(unserialize(base64_decode($profile_job_t)) ) );
					$profile_companie_last = [];
					$profile_job_last      = [];

					foreach ( $profile_companie as $ceva ) {
						$profile_companie_last[ $ceva ] = $ceva;

					}
					foreach ( $profile_job as $ceva ) {
						$profile_job_last[ $ceva ] = $ceva;
					}

					$fields[1]['sub_fields'][0]['choices']  = $profile_companie_last;
					$fields[14]['sub_fields'][0]['choices'] = $profile_job_last;


					$fields[2]['value'] = $template_load['date_visibile'];

					$fields[3]['value'] = [
						'field_5d2ccd9f3cd81' => $template_load['nume_companie'],
						'field_5d2ccdd53cd82' => $template_load['nume_alternativ'],
						'field_5d2ccddd3cd83' => $template_load['origine'][1],
						'field_5d2cd0f8f5198' => $template_load['localizare'][1],
						'field_5d2ccdfa3cd84' => $template_load['extindere'],
						'field_5d2cce0e3cd85' => $template_load['head_office'],
						'field_5d2cce2b3cd86' => $template_load['cifra_de_afaceri'],
						'field_5d2cd3623855a' => $template_load['numar_angajati'],
					];

					$fields[4]['value'] = $template_load['descriere_companie'];

					$fields[5]['value'] = $template_load['descriere_domeniu_sintetic'];

					$fields[6]['value'] = $template_load['descriere_domeniu_larg'];

					$fields[7]['value'] = $template_load['note_comentarii'];

					$links = [];
					$x     = 0;

					foreach ( $template_load['link-uri'] as $key => $link ) {
						$links[ $x ] = [
							'field_5d3a1832927bb' => $link,
							'field_5d3a183f927bc' => $key
						];
						$x ++;
					}


					$fields[8]['value'] = $links;



					$fields[15]['value'] = $temp_fields['post_job'];
					$fields[16]['value'] = [
						'field_5d0a55154bb02' => $temp_fields['nivelul_ierarhic'],
						'field_5d0a55504bb03' => $temp_fields['cui_este_subordonat'],
						'field_5d0a55f14bb04' => $temp_fields['subordonati_total'],
					];

					$fields[17]['value'] = $temp_fields['domeniu'];

					$fields[18]['value'] = $temp_fields['locatia_jobului'][1];

					$fields[19]['value'] = $temp_fields['descrierea_sintetica_a_jobului'];

					$fields[20]['value'] = $temp_fields['cerinte_job'];


					$fields[21]['value'] = [
						'field_5d0a5ddaf57ba' => $temp_fields['salariu_oferit_job'],
						'field_5d0a5dfaf57bb' => $temp_fields['descriere_oferta']
					];

					$fields[22]['value'] = [
						'field_5d0a603338bf9' => $temp_fields['mod_aplicare'],
						'field_5d0a607a38bfa' => $temp_fields['instructiuni_aplicare']
					];


					$fisiere = [];
					$x       = 0;

					foreach ( $temp_fields['fisiere'] as $key => $fisier ) {
						$fisiere[ $x ] = [
							'field_5d0a6a151cadc' => $fisier,
							'field_5d0a6a301cadd' => $key
						];
						$x ++;
					}

					$fields[23]['value'] = $temp_fields['comentarii'];

					$fields[26]['value'] = [
						'field_5d4823fd22a1e' => $temp_fields['nume_profil_job'],
						'field_5d48241722a1f' => $temp_fields['descriere_profil_job']
					];

					$fields[29]['value'] = $temp_fields['utilizeaza_profil_minimal'];
					$fields[30]['value'] = $temp_fields['afiseaza_profil__minimal'];
					$fields[31]['value'] = [
						'field_5d78aa8db9810' => $temp_fields['varsta_scm'],
						'field_5d78aa9db9811' => $temp_fields['sex'],
						'field_5d78ab18b9812' => $temp_fields['salariu_minim_scm']
					];


					$domenii    = get_field( 'domeniu', '1648' );
					$x          = 0;
					$sub_fields = [];

					foreach ( $domenii as $domeniu ) {
						$tempid = mt_rand( 1111111111111, 9999999999999 );

						$special_name = str_replace( ' ', '_', preg_replace( "/[^a-zA-Z0-9\s]/", "", $domeniu['nume_domeniu'] ) );

						$sub_fields[ $x ] = array(
							'ID'                => $tempid,
							'key'               => 'field_' . $special_name,
							'label'             => $domeniu['nume_domeniu'],
							'name'              => $domeniu['nume_domeniu'],
							'prefix'            => 'acf',
							'type'              => 'radio',
							'value'             => $temp_fields['profil_scm'][ $special_name ],
							'menu_order'        => 0,
							'instructions'      => '',
							'required'          => 0,
							'id'                => '',
							'class'             => 'domeniu',
							'conditional_logic' => 0,
							'parent'            => 1682,
							'wrapper'           =>
								array(
									'width' => '',
									'class' => 'domeniu_wrap',
									'id'    => '',
								),
							'acfe_permissions'  => '',
							'choices'           =>
								array(
									1 => '1',
									2 => '2',
									3 => '3',
									4 => '4',
									5 => '5',
								),
							'allow_null'        => 1,
							'other_choice'      => 0,
							'default_value'     => '',
							'layout'            => 'horizontal',
							'return_format'     => 'value',
							'acfe_validate'     => '',
							'acfe_update'       => '',
							'save_other_choice' => 0,
							'_name'             => $special_name,
							'_valid'            => 1,
						);

						$x ++;
						if ( count( $domeniu['sub_domeniu'] ) > 0 ) {
							foreach ( $domeniu['sub_domeniu'] as $subdomeniu ) {

								$tempid       = mt_rand( 1111111111111, 9999999999999 );
								$special_name = str_replace( ' ', '_', preg_replace( "/[^a-zA-Z0-9\s]/", "", $subdomeniu['nume_subdomeniu'] ) );

								$sub_fields[ $x ] = array(
									'ID'                => $tempid,
									'key'               => 'field_' . $special_name,
									'label'             => $subdomeniu['nume_subdomeniu'],
									'name'              => $subdomeniu['nume_domeniu'],
									'prefix'            => 'acf',
									'type'              => 'radio',
									'value'             => $temp_fields['profil_scm'][ $special_name ],
									'menu_order'        => 0,
									'instructions'      => '',
									'required'          => 0,
									'id'                => '',
									'class'             => 'subdomeniu',
									'conditional_logic' => 0,
									'parent'            => 1682,
									'wrapper'           =>
										array(
											'width' => '',
											'class' => 'subdomeniu_wrap',
											'id'    => '',
										),
									'acfe_permissions'  => '',
									'choices'           =>
										array(
											1 => '1',
											2 => '2',
											3 => '3',
											4 => '4',
											5 => '5',
										),
									'allow_null'        => 1,
									'other_choice'      => 0,
									'default_value'     => '',
									'layout'            => 'horizontal',
									'return_format'     => 'value',
									'acfe_validate'     => '',
									'acfe_update'       => '',
									'save_other_choice' => 0,
									'_name'             => $special_name,
									'_valid'            => 1,
								);


								$x ++;
							}
						}
					}

					$valori = [];


					$user_id         = get_current_user_id();
					$candidate_id    = jobsearch_get_user_candidate_id( $user_id );
					$fields_candidat = get_post_meta( $candidate_id, "profil_scm", true );
					$fields_candidat = unserialize( $fields_candidat );


					foreach ( $sub_fields as $field ) {
						$valori[ $field['key'] ] = $fields_candidat[ $field['_name'] ];
					}
					$f_value = [];
					foreach ( $template_load['profil_scm'] as $field => $value ) {
						$f_value[ 'field_' . $field ] = $value;
					}
					$fields[32]['sub_fields'] = $sub_fields;
					$fields[32]['value']      = $f_value;


					$fields[35]['value'] = $temp_fields['imagine_cover'];

					$fields[36]['value'] = $temp_fields['galerie'];


					$fisiere = [];
					$x       = 0;

					foreach ( $temp_fields['fisiere'] as $key => $fisier ) {
						$fisiere[ $x ] = [
							'field_5d8b4305671eb' => $fisier,
							'field_5d8b4321671ec' => $key
						];
						$x ++;
					}


					$fields[37]['value'] = $fisiere;

					$fields[38]['value'] = $temp_fields['logo_companie'];

					$fields[40]['value'] = [
						'field_5d496a2e7165d' => $temp_fields['nume_template'],
						'field_5d496ab97165e' => $temp_fields['descriere_template']
					];


					break;
				case "candidat_profile" :
					$fields[0]['value'] = [
						'field_5d02a1b577752' => $template_load['prenume'],
						'field_5d02a408b02bc' => $template_load['particula'],
						'field_5d02a1aa77751' => $template_load['nume'],
						'field_5d02a41eb02bd' => $template_load['formula_adresare'],
						'field_5d02a44908555' => $template_load['titlu_date'],
						'field_5d02a47508556' => $template_load['nickaname'],
						'field_5d02a48b08557' => $template_load['anul_nasterii'],
						'field_5d02a5d4574b3' => $template_load['sex'],
						'field_5d02b633ed249' => $template_load['judet_candidat']
					];
					$educatie           = [];
					foreach ( $template_load['educatie'] as $key => $value ) {
						$educatie[ $key ] = [
							'field_5d02a76f33a01' => $value
						];
					}

					$certificari = [];

					if ( is_array( $template_load['certificare'] ) ):
						foreach ( $template_load['certificare'] as $key => $value ) {
							$certificari[ $key ] = [
								'field_5d19bb78093bd' => $value
							];
						}
					else:
						$certificari[0] = [
							'field_5d19bb78093bd' => $template_load['certificare']
						];
					endif;

					$limbi = [];

					if ( is_array( $template_load['limba_nivel'] ) ):

					foreach ( $template_load['limba_nivel'] as $key => $value ) {
						$limbi[ $key ] = [
							'field_5d02a94684b56' => $value
						];
					}
					else:
						$limbi[0] = [
							'field_5d02a94684b56' => $template_load['limba_nivel']
						];
					endif;

					$fields[1]['value'] = $educatie;
					$fields[2]['value'] = $certificari;

					$fields[3]['value'] = $template_load['limba_engleza'];

					$fields[4]['value'] = $limbi;

					$fields[5]['value'] = $template_load['relevant_skils'];

					$links = [];
					$x     = 0;
					foreach ( $template_load['link-uri'] as $key => $link ) {
						$links[ $x ] = [
							'field_5d02a9d32698b' => $link,
							'field_5d02a9e52698c' => $key
						];
						$x ++;
					}

					$fields[6]['value'] = $links;

					$experinta = [];

					$x = 0;
					foreach ( $template_load['experinta_full'] as $key => $companie ) {

						$posturi = [];
						$y       = 0;
						foreach ( $companie['post'] as $post => $date ) {
							$posturi[ $y ] = [
								'field_5d0360c5c0b3f' => $date['post'],
								'field_5d02ab66a0a01' => $date['data_la'],
								'field_5d02ab6aa0a02' => $date['pana_la'],
								'field_5d02ab74a0a03' => $date['ierarhie'],
								'field_5d1081428c2a1' => $date['ultimul_job'],
								'field_5d36f94951820' => $date['departament'],
								'field_5d02ab7fa0a04' => $date['descriere_job'],
								'field_5d02ab92a0a05' => $date['alte_detalii'],
							];
							$y ++;
						}
						$experinta[ $x ] = [
							'field_5d02ab22a09ff' => $companie['nume_companie'],
							'field_5d19d26878f51' => $companie['descriere_companie'],
							'field_5d02ab5ea0a00' => $posturi

						];
						$x ++;
					}


					$fields[7]['value']  = $experinta;
					$fields[8]['value']  = $template_load['salariu_minim_accepta'];
					$fields[9]['value']  = $template_load['alte_cerinte'];
					$fields[10]['value'] = $template_load['note_comentarii'];
					$fields[11]['value'] = $template_load['imagine_profil'];
					$fields[12]['value'] = $template_load['imagine_cover'];
					$fields[13]['value'] = [
						'field_5d36f440d0989' => $template_load['nume_template'],
						'field_5d36f44dd098a' => $template_load['descriere_template']
					];

					break;
				case "filtre_candidat":
					$fields[0]['value'] = $template_load['locatie'];
					$fields[1]['value'] = $template_load['nivel'];
					$fields[2]['value'] = $template_load['domeniu'];
					$fields[3]['value'] = $template_load['salariu_minim'];
					$fields[4]['value'] = [
						'field_5d5a947d7d546' => $template_load['nume_template'],
						'field_5d5a94877d547' => $template_load['descriere_template']
					];
					break;
				case "candidat_texte":

					// nice_print_r($template_load);

					$fields[0]['value'] = $template_load['text'];
					$fields[1]['value'] = [
						'field_5d532728f4626' => $template_load['nume_template'],
						'field_5d532742f4627' => $template_load['descriere_template']
					];
					break;
				case "profil_scm":

					$domenii    = get_field( 'domeniu', '1648' );
					$x          = 0;
					$sub_fields = [];

					foreach ( $domenii as $domeniu ) {
						$tempid = rand( 1111111111111, 9999999999999 );

						$special_name = str_replace( ' ', '_', preg_replace( "/[^a-zA-Z0-9\s]/", "", $domeniu['nume_domeniu'] ) );


						$field[ $x ] = array(
							'ID'                => $tempid,
							'key'               => 'field_' . $special_name,
							'label'             => $domeniu['nume_domeniu'],
							'name'              => $domeniu['nume_domeniu'],
							'prefix'            => 'acf',
							'type'              => 'radio',
							'value'             => null,
							'menu_order'        => 0,
							'instructions'      => '',
							'required'          => 0,
							'id'                => '',
							'class'             => 'domeniu',
							'conditional_logic' => 0,
							'parent'            => 1682,
							'wrapper'           =>
								array(
									'width' => '',
									'class' => 'domeniu_wrap',
									'id'    => '',
								),
							'acfe_permissions'  => '',
							'choices'           =>
								array(
									1 => '1',
									2 => '2',
									3 => '3',
									4 => '4',
									5 => '5',
								),
							'allow_null'        => 1,
							'other_choice'      => 0,
							'default_value'     => '',
							'layout'            => 'horizontal',
							'return_format'     => 'value',
							'acfe_validate'     => '',
							'acfe_update'       => '',
							'save_other_choice' => 0,
							'_name'             => $special_name,
							'_valid'            => 1,
						);


						$x ++;
						if ( count( $domeniu['sub_domeniu'] ) > 0 ) {
							foreach ( $domeniu['sub_domeniu'] as $subdomeniu ) {


								$special_name = str_replace( ' ', '_', preg_replace( "/[^a-zA-Z0-9\s]/", "", $subdomeniu['nume_subdomeniu'] ) );

								$field[ $x ] = array(
									'ID'                => $tempid,
									'key'               => 'field_' . $special_name,
									'label'             => $subdomeniu['nume_subdomeniu'],
									'name'              => $subdomeniu['nume_domeniu'],
									'prefix'            => 'acf',
									'type'              => 'radio',
									'value'             => null,
									'menu_order'        => 0,
									'instructions'      => '',
									'required'          => 0,
									'id'                => '',
									'class'             => 'domeniu',
									'conditional_logic' => 0,
									//     'parent' => 1682,
									'wrapper'           =>
										array(
											'width' => '',
											'class' => 'domeniu_wrap',
											'id'    => '',
										),
									'acfe_permissions'  => '',
									'choices'           =>
										array(
											1 => '1',
											2 => '2',
											3 => '3',
											4 => '4',
											5 => '5',
										),
									'allow_null'        => 1,
									'other_choice'      => 0,
									'default_value'     => '',
									'layout'            => 'horizontal',
									'return_format'     => 'value',
									'acfe_validate'     => '',
									'acfe_update'       => '',
									'save_other_choice' => 0,
									'_name'             => $special_name,
									'_valid'            => 1,
								);


								$x ++;
							}
						}

					}

					$fields[0] = [
						'ID'                => 1671,
						'key'               => 'field_5d70d03f40b49',
						'label'             => 'Profil scm',
						'name'              => 'profil_scm',
						'prefix'            => 'acf',
						'type'              => 'group',
						'value'             => [

						],
						'menu_order'        => 0,
						'instructions'      => '',
						'required'          => 0,
						'id'                => '',
						'class'             => '',
						'conditional_logic' => 0,
						'parent'            => 1660,
						'wrapper'           =>
							array(
								'width' => '',
								'class' => '',
								'id'    => '',
							),
						'acfe_permissions'  => '',
						'layout'            => 'block',
						'sub_fields'        => $sub_fields,
						'_name'             => 'profil_scm',
						'_valid'            => 1,
					];

					$fields[1]['value'] = [
						'field_5d70ceb76df9b' => $template_load['nume_template'],
						'field_5d70cec86df9c' => $template_load['descriere_template']
					];

					break;
				default:
					break;
			}
		} else {
			switch ( $fields_type ) {
				case "anunt_template_new":
					// nice_print_r($fields);

//                    foreach($fields as $key => $value){
//                        nice_print_r("$key =>" . $value['name'] );
//                    }

					$current_user = wp_get_current_user();
					$user_id      = get_current_user_id();
					$user_obj     = get_user_by( 'ID', $user_id );

					$employer_id = jobsearch_get_user_employer_id( $user_id );

					if ( $post_id == '' || empty( $post_id ) ) {
						$post_id = $employer_id;
					}

					$profile_companie_t = get_post_meta( $post_id, 'companie_company_profiles', true );
					$profile_job_t      = get_post_meta( $post_id, 'companie_job_profiles', true );

					$profile_companie[0] = 'alege un profil de companie';
					$profile_job[0]      = 'alege un profil de job';

					$profile_companie      = array_merge( $profile_companie, array_keys( unserialize(base64_decode($profile_companie_t)) ) );
					$profile_job           = array_merge( $profile_job, array_keys(unserialize(base64_decode($profile_job_t)) ) );

					$profile_companie_last = [];
					$profile_job_last      = [];


					foreach ( $profile_companie as $ceva ) {
						$profile_companie_last[ $ceva ] = $ceva;

					}
					foreach ( $profile_job as $ceva ) {
						$profile_job_last[ $ceva ] = $ceva;
					}

					//nice_print_r($profile_job_last);

					$fields[1]['sub_fields'][0]['choices']  = $profile_companie_last;
					$fields[14]['sub_fields'][0]['choices'] = $profile_job_last;


					$domenii    = get_field( 'domeniu', '1648' );
					$x          = 0;
					$sub_fields = [];

					foreach ( $domenii as $domeniu ) {
						$tempid = mt_rand( 1111111111111, 9999999999999 );

						$special_name = str_replace( ' ', '_', preg_replace( "/[^a-zA-Z0-9\s]/", "", $domeniu['nume_domeniu'] ) );

						$sub_fields[ $x ] = array(
							'ID'                => $tempid,
							'key'               => 'field_' . $special_name,
							'label'             => $domeniu['nume_domeniu'],
							'name'              => $domeniu['nume_domeniu'],
							'prefix'            => 'acf',
							'type'              => 'radio',
							'value'             => null,
							'menu_order'        => 0,
							'instructions'      => '',
							'required'          => 0,
							'id'                => '',
							'class'             => 'domeniu',
							'conditional_logic' => 0,
							'parent'            => 1682,
							'wrapper'           =>
								array(
									'width' => '',
									'class' => 'domeniu_wrap',
									'id'    => '',
								),
							'acfe_permissions'  => '',
							'choices'           =>
								array(
									1 => '1',
									2 => '2',
									3 => '3',
									4 => '4',
									5 => '5',
								),
							'allow_null'        => 1,
							'other_choice'      => 0,
							'default_value'     => '',
							'layout'            => 'horizontal',
							'return_format'     => 'value',
							'acfe_validate'     => '',
							'acfe_update'       => '',
							'save_other_choice' => 0,
							'_name'             => $special_name,
							'_valid'            => 1,
						);

						$x ++;
						if ( count( $domeniu['sub_domeniu'] ) > 0 ) {
							foreach ( $domeniu['sub_domeniu'] as $subdomeniu ) {

								$tempid       = mt_rand( 1111111111111, 9999999999999 );
								$special_name = str_replace( ' ', '_', preg_replace( "/[^a-zA-Z0-9\s]/", "", $subdomeniu['nume_subdomeniu'] ) );

								$sub_fields[ $x ] = array(
									'ID'                => $tempid,
									'key'               => 'field_' . $special_name,
									'label'             => $subdomeniu['nume_subdomeniu'],
									'name'              => $subdomeniu['nume_domeniu'],
									'prefix'            => 'acf',
									'type'              => 'radio',
									'value'             => null,
									'menu_order'        => 0,
									'instructions'      => '',
									'required'          => 0,
									'id'                => '',
									'class'             => 'subdomeniu',
									'conditional_logic' => 0,
									'parent'            => 1682,
									'wrapper'           =>
										array(
											'width' => '',
											'class' => 'subdomeniu_wrap',
											'id'    => '',
										),
									'acfe_permissions'  => '',
									'choices'           =>
										array(
											1 => '1',
											2 => '2',
											3 => '3',
											4 => '4',
											5 => '5',
										),
									'allow_null'        => 1,
									'other_choice'      => 0,
									'default_value'     => '',
									'layout'            => 'horizontal',
									'return_format'     => 'value',
									'acfe_validate'     => '',
									'acfe_update'       => '',
									'save_other_choice' => 0,
									'_name'             => $special_name,
									'_valid'            => 1,
								);


								$x ++;
							}
						}
					}

					$valori = [];


					$user_id         = get_current_user_id();
					$candidate_id    = jobsearch_get_user_candidate_id( $user_id );
					$fields_candidat = get_post_meta( $candidate_id, "profil_scm", true );
					$fields_candidat = unserialize( $fields_candidat );


					foreach ( $sub_fields as $field ) {
						$valori[ $field['key'] ] = $fields_candidat[ $field['_name'] ];
					}

					$fields[32]['sub_fields'] = $sub_fields;


					//  nice_print_r($fields);

					break;
				case "profil_scm":

					$domenii    = get_field( 'domeniu', '1648' );
					$x          = 0;
					$sub_fields = [];

					foreach ( $domenii as $domeniu ) {
						$tempid = mt_rand( 1111111111111, 9999999999999 );

						$special_name = str_replace( ' ', '_', preg_replace( "/[^a-zA-Z0-9\s]/", "", $domeniu['nume_domeniu'] ) );

						$sub_fields[ $x ] = array(
							'ID'                => $tempid,
							'key'               => 'field_' . $special_name,
							'label'             => $domeniu['nume_domeniu'],
							'name'              => $domeniu['nume_domeniu'],
							'prefix'            => 'acf',
							'type'              => 'radio',
							'value'             => null,
							'menu_order'        => 0,
							'instructions'      => '',
							'required'          => 0,
							'id'                => '',
							'class'             => 'domeniu',
							'conditional_logic' => 0,
							'parent'            => 1682,
							'wrapper'           =>
								array(
									'width' => '',
									'class' => 'domeniu_wrap',
									'id'    => '',
								),
							'acfe_permissions'  => '',
							'choices'           =>
								array(
									1 => '1',
									2 => '2',
									3 => '3',
									4 => '4',
									5 => '5',
								),
							'allow_null'        => 1,
							'other_choice'      => 0,
							'default_value'     => '',
							'layout'            => 'horizontal',
							'return_format'     => 'value',
							'acfe_validate'     => '',
							'acfe_update'       => '',
							'save_other_choice' => 0,
							'_name'             => $special_name,
							'_valid'            => 1,
						);

						$x ++;
						if ( count( $domeniu['sub_domeniu'] ) > 0 ) {
							foreach ( $domeniu['sub_domeniu'] as $subdomeniu ) {

								$tempid       = mt_rand( 1111111111111, 9999999999999 );
								$special_name = str_replace( ' ', '_', preg_replace( "/[^a-zA-Z0-9\s]/", "", $subdomeniu['nume_subdomeniu'] ) );

								$sub_fields[ $x ] = array(
									'ID'                => $tempid,
									'key'               => 'field_' . $special_name,
									'label'             => $subdomeniu['nume_subdomeniu'],
									'name'              => $subdomeniu['nume_domeniu'],
									'prefix'            => 'acf',
									'type'              => 'radio',
									'value'             => null,
									'menu_order'        => 0,
									'instructions'      => '',
									'required'          => 0,
									'id'                => '',
									'class'             => 'subdomeniu',
									'conditional_logic' => 0,
									'parent'            => 1682,
									'wrapper'           =>
										array(
											'width' => '',
											'class' => 'subdomeniu_wrap',
											'id'    => '',
										),
									'acfe_permissions'  => '',
									'choices'           =>
										array(
											1 => '1',
											2 => '2',
											3 => '3',
											4 => '4',
											5 => '5',
										),
									'allow_null'        => 1,
									'other_choice'      => 0,
									'default_value'     => '',
									'layout'            => 'horizontal',
									'return_format'     => 'value',
									'acfe_validate'     => '',
									'acfe_update'       => '',
									'save_other_choice' => 0,
									'_name'             => $special_name,
									'_valid'            => 1,
								);


								$x ++;
							}
						}
					}

					$valori = [];


					$user_id         = get_current_user_id();
					$candidate_id    = jobsearch_get_user_candidate_id( $user_id );
					$fields_candidat = get_post_meta( $candidate_id, "profil_scm", true );

					$fields_candidat = unserialize( $fields_candidat );


					foreach ( $sub_fields as $field ) {
						$valori[ $field['key'] ] = $fields_candidat[ $field['_name'] ];
					}

					$fields[0] = [
						'ID'                => 1682,
						'key'               => 'field_5d76159332e59',
						'label'             => 'Autoevalure',
						'name'              => 'autoevalure',
						'prefix'            => 'acf',
						'type'              => 'group',
						'value'             => $valori,
						'menu_order'        => 0,
						'instructions'      => '',
						'required'          => 0,
						'id'                => '',
						'class'             => '',
						'conditional_logic' => 0,
						'parent'            => 1678,
						'wrapper'           =>
							array(
								'width' => '',
								'class' => '',
								'id'    => '',
							),
						'acfe_permissions'  => '',
						'layout'            => 'block',
						'sub_fields'        => $sub_fields,
						'_name'             => 'profil_scm',
						'_valid'            => 1,
					];

					break;
				case "select_template":

					$current_user = wp_get_current_user();
					$user_id      = get_current_user_id();
					$user_obj     = get_user_by( 'ID', $user_id );

					$employer_id = jobsearch_get_user_employer_id( $user_id );

					if ( $post_id == '' || empty( $post_id ) ) {
						$post_id = $employer_id;
					}

					$profile_companie_t = get_post_meta( $employer_id, "companie_anunt_template", true );

					$profile_companie[0] = 'alege un profil de companie';

					$profile_companie = array_merge( $profile_companie, array_keys( unserialize(base64_decode($profile_companie_t)) ) );

					foreach ( $profile_companie as $ceva ) {
						$profile_companie_last[ $ceva ] = $ceva;
					}

					$fields[0]['sub_fields'][0]['choices'] = $profile_companie_last;

					break;
				default:
					break;
			}
		}


		foreach ( $fields as $key => $field ) {

			if ( $fields_type == 'anunt_template_new' ) {


			} else {
				if ( $field['value'] === null ) {
					$field['value'] = acf_get_value( $post_id, $field );
				}
			}

			acf_render_field_wrap( $field, $el, $instruction );
		}
	}


	/**
	 *
	 * post -> date_anunt (profil + date)
	 *  post -> profil companie, postul(X) -> date_job
	 *  Fires after fields have been rendered.
	 *
	 * @date    12/02/2014
	 *
	 * @param array $fields An array of fields.
	 * @param    (int|string) $post_id The post ID to load values from.
	 *
	 * @since    5.0.0
	 *
	 */
	do_action( 'acf/render_fields', $fields, $post_id );
}

/**
 * acf_render_field_wrap
 *
 * Render the wrapping element for a given field.
 *
 * @date    28/09/13
 *
 * @param array $field The field array.
 * @param string $element The wrapping element type.
 * @param string $instruction The instruction render position (label|field).
 *
 * @return    void
 * @since    5.0.0
 *
 */
function acf_render_field_wrap( $field, $element = 'div', $instruction = 'label' ) {

	// Ensure field is complete (adds all settings).
	$field = acf_validate_field( $field );

	// Prepare field for input (modifies settings).
	$field = acf_prepare_field( $field );

	// Allow filters to cancel render.
	if ( ! $field ) {
		return;
	}

	// Determine wrapping element.
	$elements = array(
		'div' => 'div',
		'tr'  => 'td',
		'td'  => 'div',
		'ul'  => 'li',
		'ol'  => 'li',
		'dl'  => 'dt',
	);

	if ( isset( $elements[ $element ] ) ) {
		$inner_element = $elements[ $element ];
	} else {
		$element = $inner_element = 'div';
	}

	// Generate wrapper attributes.
	$wrapper = array(
		'id'        => '',
		'class'     => 'acf-field',
		'width'     => '',
		'style'     => '',
		'data-name' => $field['_name'],
		'data-type' => $field['type'],
		'data-key'  => $field['key'],
	);

	// Add field type attributes.
	$wrapper['class'] .= " acf-field-{$field['type']}";

	// add field key attributes
	if ( $field['key'] ) {
		$wrapper['class'] .= " acf-field-{$field['key']}";
	}

	// Add required attributes.
	// Todo: Remove data-required
	if ( $field['required'] ) {
		$wrapper['class']         .= ' is-required';
		$wrapper['data-required'] = 1;
	}

	// Clean up class attribute.
	$wrapper['class'] = str_replace( '_', '-', $wrapper['class'] );
	$wrapper['class'] = str_replace( 'field-field-', 'field-', $wrapper['class'] );

	// Merge in field 'wrapper' setting without destroying class and style.
	if ( $field['wrapper'] ) {
		$wrapper = acf_merge_attributes( $wrapper, $field['wrapper'] );
	}

	// Extract wrapper width and generate style.
	// Todo: Move from $wrapper out into $field.
	$width = acf_extract_var( $wrapper, 'width' );
	if ( $width ) {
		$width = acf_numval( $width );
		if ( $element !== 'tr' && $element !== 'td' ) {
			$wrapper['data-width'] = $width;
			$wrapper['style']      .= " width:{$width}%;";
		}
	}

	// Clean up all attributes.
	$wrapper = array_map( 'trim', $wrapper );
	$wrapper = array_filter( $wrapper );

	/**
	 * Filters the $wrapper array before rendering.
	 *
	 * @date    21/1/19
	 *
	 * @param array $wrapper The wrapper attributes array.
	 * @param array $field The field array.
	 *
	 * @since    5.7.10
	 *
	 */
	$wrapper = apply_filters( 'acf/field_wrapper_attributes', $wrapper, $field );

	// Append conditional logic attributes.
	if ( ! empty( $field['conditional_logic'] ) ) {
		$wrapper['data-conditions'] = $field['conditional_logic'];
	}
	if ( ! empty( $field['conditions'] ) ) {
		$wrapper['data-conditions'] = $field['conditions'];
	}

	// Vars for render.
	$attributes_html = acf_esc_attr( $wrapper );

	// Render HTML
	echo "<$element $attributes_html>" . "\n";
	if ( $element !== 'td' ) {
		echo "<$inner_element class=\"acf-label\">" . "\n";
		acf_render_field_label( $field );
		if ( $instruction == 'label' ) {
			acf_render_field_instructions( $field );
		}
		echo "</$inner_element>" . "\n";
	}
	echo "<$inner_element class=\"acf-input\">" . "\n";
	acf_render_field( $field );
	if ( $instruction == 'field' ) {
		acf_render_field_instructions( $field );
	}
	echo "</$inner_element>" . "\n";
	echo "</$element>" . "\n";
}

/**
 * acf_render_field
 *
 * Render the input element for a given field.
 *
 * @date    21/1/19
 *
 * @param array $field The field array.
 *
 * @return    void
 * @since    5.7.10
 *
 */
function acf_render_field( $field ) {

	// Ensure field is complete (adds all settings).
	$field = acf_validate_field( $field );

	// Prepare field for input (modifies settings).
	$field = acf_prepare_field( $field );

	// Allow filters to cancel render.
	if ( ! $field ) {
		return;
	}

	/**
	 * Fires when rendering a field.
	 *
	 * @date    12/02/2014
	 *
	 * @param array $field The field array.
	 *
	 * @since    5.0.0
	 *
	 */
	do_action( "acf/render_field", $field );
}

// Register variation.
acf_add_action_variations( 'acf/render_field', array( 'type', 'name', 'key' ), 0 );

/**
 * acf_render_field_label
 *
 * Renders the field's label.
 *
 * @date    19/9/17
 *
 * @param array $field The field array.
 *
 * @return    void
 * @since    5.6.3
 *
 */
function acf_render_field_label( $field ) {

	// Get label.
	$label = acf_get_field_label( $field );

	// Output label.
	if ( $label ) {
		echo '<label' . ( $field['id'] ? ' for="' . esc_attr( $field['id'] ) . '"' : '' ) . '>' . acf_esc_html( $label ) . '</label>';
	}
}

/**
 * acf_get_field_label
 *
 * Returns the field's label with appropriate required label.
 *
 * @date    4/11/2013
 *
 * @param array $field The field array.
 * @param string $context The output context (admin).
 *
 * @return    void
 * @since    5.0.0
 *
 */
function acf_get_field_label( $field, $context = '' ) {

	// Get label.
	$label = $field['label'];

	// Display empty text when editing field.
	if ( $context == 'admin' && $label === '' ) {
		$label = __( '(no label)', 'acf' );
	}

	// Add required HTML.
	if ( $field['required'] ) {
		$label .= ' <span class="acf-required">*</span>';
	}

	/**
	 * Filters the field's label HTML.
	 *
	 * @date    21/1/19
	 *
	 * @param string The label HTML.
	 * @param array $field The field array.
	 * @param string $context The output context (admin).
	 *
	 * @since    5.7.10
	 *
	 */
	$label = apply_filters( "acf/get_field_label", $label, $field, $context );

	// Return label.
	return $label;
}

/**
 * acf_render_field_instructions
 *
 * Renders the field's instructions.
 *
 * @date    19/9/17
 *
 * @param array $field The field array.
 *
 * @return    void
 * @since    5.6.3
 *
 */
function acf_render_field_instructions( $field ) {

	// Output instructions.
	if ( $field['instructions'] ) {
		echo '<p class="description">' . acf_esc_html( $field['instructions'] ) . '</p>';
	}
}

/**
 * acf_render_field_setting
 *
 * Renders a field setting used in the admin edit screen.
 *
 * @date    21/1/19
 *
 * @param array $field The field array.
 * @param array $setting The settings field array.
 * @param bool $global Whether this setting is a global or field type specific one.
 *
 * @return    void
 * @since    5.7.10
 *
 */
function acf_render_field_setting( $field, $setting, $global = false ) {

	// Validate field.
	$setting = acf_validate_field( $setting );

	// Add custom attributes to setting wrapper.
	$setting['wrapper']['data-key'] = $setting['name'];
	$setting['wrapper']['class']    .= ' acf-field-setting-' . $setting['name'];
	if ( ! $global ) {
		$setting['wrapper']['data-setting'] = $field['type'];
	}

	// Copy across prefix.
	$setting['prefix'] = $field['prefix'];

	// Find setting value from field.
	if ( $setting['value'] === null ) {

		// Name.
		if ( isset( $field[ $setting['name'] ] ) ) {
			$setting['value'] = $field[ $setting['name'] ];

			// Default value.
		} elseif ( isset( $setting['default_value'] ) ) {
			$setting['value'] = $setting['default_value'];
		}
	}

	// Add append attribute used by JS to join settings.
	if ( isset( $setting['_append'] ) ) {
		$setting['wrapper']['data-append'] = $setting['_append'];
	}

	// Render setting.
	acf_render_field_wrap( $setting, 'tr', 'label' );
}

/**
 * acf_update_field
 *
 * Updates a field in the database.
 *
 * @date    21/1/19
 *
 * @param array $field The field array.
 * @param array $specific An array of specific field attributes to update.
 *
 * @return    void
 * @since    5.7.10
 *
 */
function acf_update_field( $field, $specific = array() ) {

	// Validate field.
	$field = acf_validate_field( $field );

	// May have been posted. Remove slashes.
	$field = wp_unslash( $field );

	// Parse types (converts string '0' to int 0).
	$field = acf_parse_types( $field );

	// Clean up conditional logic keys.
	if ( $field['conditional_logic'] ) {

		// Remove empty values and convert to associated array.
		$field['conditional_logic'] = array_filter( $field['conditional_logic'] );
		$field['conditional_logic'] = array_values( $field['conditional_logic'] );
		$field['conditional_logic'] = array_map( 'array_filter', $field['conditional_logic'] );
		$field['conditional_logic'] = array_map( 'array_values', $field['conditional_logic'] );
	}

	// Parent may be provided as a field key.
	if ( $field['parent'] && ! is_numeric( $field['parent'] ) ) {
		$parent          = acf_get_field_post( $field['parent'] );
		$field['parent'] = $parent ? $parent->ID : 0;
	}

	/**
	 * Filters the $field array before it is updated.
	 *
	 * @date    12/02/2014
	 *
	 * @param array $field The field array.
	 *
	 * @since    5.0.0
	 *
	 */
	$field = apply_filters( "acf/update_field", $field );

	// Make a backup of field data and remove some args.
	$_field = $field;
	acf_extract_vars( $_field, array(
		'ID',
		'key',
		'label',
		'name',
		'prefix',
		'value',
		'menu_order',
		'id',
		'class',
		'parent',
		'_name',
		'_prepare',
		'_valid'
	) );

	// Create array of data to save.
	$save = array(
		'ID'           => $field['ID'],
		'post_status'  => 'publish',
		'post_type'    => 'acf-field',
		'post_title'   => $field['label'],
		'post_name'    => $field['key'],
		'post_excerpt' => $field['name'],
		'post_content' => maybe_serialize( $_field ),
		'post_parent'  => $field['parent'],
		'menu_order'   => $field['menu_order'],
	);

	// Reduce save data if specific key list is provided.
	if ( $specific ) {
		$specific[] = 'ID';
		$save       = acf_get_sub_array( $save, $specific );
	}

	// Unhook wp_targeted_link_rel() filter from WP 5.1 corrupting serialized data.
	remove_filter( 'content_save_pre', 'wp_targeted_link_rel' );

	// Slash data.
	// WP expects all data to be slashed and will unslash it (fixes '\' character issues).
	$save = wp_slash( $save );

	// Update or Insert.
	if ( $field['ID'] ) {
		wp_update_post( $save );
	} else {
		$field['ID'] = wp_insert_post( $save );
	}

	// Flush field cache.
	acf_flush_field_cache( $field );

	/**
	 * Fires after a field has been updated, and the field cache has been cleaned.
	 *
	 * @date    24/1/19
	 *
	 * @param array $field The field array.
	 *
	 * @since    5.7.10
	 *
	 */
	do_action( "acf/updated_field", $field );

	// Return field.
	return $field;
}

// Register variation.
acf_add_filter_variations( 'acf/update_field', array( 'type', 'name', 'key' ), 0 );

/**
 * _acf_apply_unique_field_slug
 *
 * Allows full control over 'acf-field' slugs.
 *
 * @date    21/1/19
 *
 * @param string $slug The post slug.
 * @param int $post_ID Post ID.
 * @param string $post_status The post status.
 * @param string $post_type Post type.
 * @param int $post_parent Post parent ID
 * @param string $original_slug The original post slug.
 *
 * @since    5.7.10
 *
 */
function _acf_apply_unique_field_slug( $slug, $post_ID, $post_status, $post_type, $post_parent, $original_slug ) {

	// Check post type and reset to original value.
	if ( $post_type === 'acf-field' ) {
		return $original_slug;
	}

	// Return slug.
	return $slug;
}

// Hook into filter.
add_filter( 'wp_unique_post_slug', '_acf_apply_unique_field_slug', 999, 6 );

/**
 * acf_flush_field_cache
 *
 * Deletes all caches for this field.
 *
 * @date    22/1/19
 *
 * @param array $field The field array.
 *
 * @return    void
 * @since    5.7.10
 *
 */
function acf_flush_field_cache( $field ) {

	// Delete stored data.
	acf_get_store( 'fields' )->remove( $field['key'] );

	// Flush cached post_id for this field's name and key.
	wp_cache_delete( acf_cache_key( "acf_get_field_post:name:{$field['name']}" ), 'acf' );
	wp_cache_delete( acf_cache_key( "acf_get_field_post:key:{$field['key']}" ), 'acf' );

	// Flush cached array of post_ids for this field's parent.
	wp_cache_delete( acf_cache_key( "acf_get_field_posts:{$field['parent']}" ), 'acf' );
}

/**
 * acf_delete_field
 *
 * Deletes a field from the database.
 *
 * @date    21/1/19
 *
 * @param    (int|string) $id The field ID, key or name.
 *
 * @return    bool True if field was deleted.
 * @since    5.7.10
 *
 */
function acf_delete_field( $id = 0 ) {

	// Get the field.
	$field = acf_get_field( $id );

	// Bail early if field was not found.
	if ( ! $field || ! $field['ID'] ) {
		return false;
	}

	// Delete post.
	wp_delete_post( $field['ID'], true );

	// Flush field cache.
	acf_flush_field_cache( $field );

	/**
	 * Fires immediately after a field has been deleted.
	 *
	 * @date    12/02/2014
	 *
	 * @param array $field The field array.
	 *
	 * @since    5.0.0
	 *
	 */
	do_action( "acf/delete_field", $field );

	// Return true.
	return true;
}

// Register variation.
acf_add_action_variations( 'acf/delete_field', array( 'type', 'name', 'key' ), 0 );

/**
 * acf_trash_field
 *
 * Trashes a field from the database.
 *
 * @date    2/10/13
 *
 * @param    (int|string) $id The field ID, key or name.
 *
 * @return    bool True if field was trashed.
 * @since    5.0.0
 *
 */
function acf_trash_field( $id = 0 ) {

	// Get the field.
	$field = acf_get_field( $id );

	// Bail early if field was not found.
	if ( ! $field || ! $field['ID'] ) {
		return false;
	}

	// Trash post.
	wp_trash_post( $field['ID'], true );

	/**
	 * Fires immediately after a field has been trashed.
	 *
	 * @date    12/02/2014
	 *
	 * @param array $field The field array.
	 *
	 * @since    5.0.0
	 *
	 */
	do_action( 'acf/trash_field', $field );

	// Return true.
	return true;
}

/**
 * acf_untrash_field
 *
 * Restores a field from the trash.
 *
 * @date    2/10/13
 *
 * @param    (int|string) $id The field ID, key or name.
 *
 * @return    bool True if field was trashed.
 * @since    5.0.0
 *
 */
function acf_untrash_field( $id = 0 ) {

	// Get the field.
	$field = acf_get_field( $id );

	// Bail early if field was not found.
	if ( ! $field || ! $field['ID'] ) {
		return false;
	}

	// Untrash post.
	wp_untrash_post( $field['ID'], true );

	// Flush field cache.
	acf_flush_field_cache( $field );

	/**
	 * Fires immediately after a field has been trashed.
	 *
	 * @date    12/02/2014
	 *
	 * @param array $field The field array.
	 *
	 * @since    5.0.0
	 *
	 */
	do_action( 'acf/untrash_field', $field );

	// Return true.
	return true;
}

/**
 * acf_prefix_fields
 *
 * Changes the prefix for an array of fields by reference.
 *
 * @date    5/9/17
 *
 * @param array $fields An array of fields.
 * @param string $prefix The new prefix.
 *
 * @return    void
 * @since    5.6.0
 *
 */
function acf_prefix_fields( &$fields, $prefix = 'acf' ) {

	// Loopover fields.
	foreach ( $fields as &$field ) {

		// Replace 'acf' with $prefix.
		$field['prefix'] = $prefix . substr( $field['prefix'], 3 );
	}
}

/**
 * acf_get_sub_field
 *
 * Searches a field for sub fields matching the given selector.
 *
 * @date    21/1/19
 *
 * @param    (int|string) $id The field ID, key or name.
 * @param array $field The parent field array.
 *
 * @return    (array|false)
 * @since    5.7.10
 *
 */
function acf_get_sub_field( $id, $field ) {

	// Vars.
	$sub_field = false;

	// Search sub fields.
	if ( isset( $field['sub_fields'] ) ) {
		$sub_field = acf_search_fields( $id, $field['sub_fields'] );
	}

	/**
	 * Filters the $sub_field found.
	 *
	 * @date    12/02/2014
	 *
	 * @param array $sub_field The found sub field array.
	 * @param string $selector The selector used to search.
	 * @param array $field The parent field array.
	 *
	 * @since    5.0.0
	 *
	 */
	$sub_field = apply_filters( "acf/get_sub_field", $sub_field, $id, $field );

	// return
	return $sub_field;

}

// Register variation.
acf_add_filter_variations( 'acf/get_sub_field', array( 'type' ), 2 );

/**
 * acf_search_fields
 *
 * Searches an array of fields for one that matches the given identifier.
 *
 * @date    12/2/19
 *
 * @param    (int|string) $id The field ID, key or name.
 * @param array $haystack The array of fields.
 *
 * @return    (int|false)
 * @since    5.7.11
 *
 */
function acf_search_fields( $id, $fields ) {

	// Loop over searchable keys in order of priority.
	// Important to search "name" on all fields before "_name" backup.
	foreach ( array( 'key', 'name', '_name', '__name' ) as $key ) {

		// Loop over fields and compare.
		foreach ( $fields as $field ) {
			if ( isset( $field[ $key ] ) && $field[ $key ] === $id ) {
				return $field;
			}
		}
	}

	// Return not found.
	return false;
}

/**
 * acf_is_field
 *
 * Returns true if the given params match a field.
 *
 * @date    21/1/19
 *
 * @param array $field The field array.
 * @param mixed $id An optional identifier to search for.
 *
 * @return    bool
 * @since    5.7.10
 *
 */
function acf_is_field( $field = false, $id = '' ) {
	return (
		is_array( $field )
		&& isset( $field['key'] )
		&& isset( $field['name'] )
	);
}

/**
 * acf_get_field_ancestors
 *
 * Returns an array of ancestor field ID's or keys.
 *
 * @date    22/06/2016
 *
 * @param array $field The field array.
 *
 * @return    array
 * @since    5.3.8
 *
 */
function acf_get_field_ancestors( $field ) {

	// Vars.
	$ancestors = array();

	// Loop over parents.
	while ( $field['parent'] && $field = acf_get_field( $field['parent'] ) ) {
		$ancestors[] = $field['ID'] ? $field['ID'] : $field['key'];
	}

	// return
	return $ancestors;
}

/**
 * acf_duplicate_fields
 *
 * Duplicate an array of fields.
 *
 * @date    16/06/2014
 *
 * @param array $fields An array of fields.
 * @param int $parent_id The new parent ID.
 *
 * @return    array
 * @since    5.0.0
 *
 */
function acf_duplicate_fields( $fields = array(), $parent_id = 0 ) {

	// Vars.
	$duplicates = array();

	// Loop over fields and pre-generate new field keys (needed for conditional logic).
	$keys = array();
	foreach ( $fields as $field ) {

		// Delay for a microsecond to ensure a unique ID.
		usleep( 1 );
		$keys[ $field['key'] ] = uniqid( 'field_' );
	}

	// Store these keys for later use.
	acf_set_data( 'duplicates', $keys );

	// Duplicate fields.
	foreach ( $fields as $field ) {
		$field_id     = $field['ID'] ? $field['ID'] : $field['key'];
		$duplicates[] = acf_duplicate_field( $field_id, $parent_id );
	}

	// Return.
	return $duplicates;
}

/**
 * acf_duplicate_field
 *
 * Duplicates a field.
 *
 * @date    16/06/2014
 *
 * @param    (int|string) $id The field ID, key or name.
 * @param int $parent_id The new parent ID.
 *
 * @return    bool True if field was duplicated.
 * @since    5.0.0
 *
 */
function acf_duplicate_field( $id = 0, $parent_id = 0 ) {

	// Get the field.
	$field = acf_get_field( $id );

	// Bail early if field was not found.
	if ( ! $field ) {
		return false;
	}

	// Remove ID to avoid update.
	$field['ID'] = 0;

	// Generate key.
	$keys         = acf_get_data( 'duplicates' );
	$field['key'] = isset( $keys[ $field['key'] ] ) ? $keys[ $field['key'] ] : uniqid( 'field_' );

	// Set parent.
	if ( $parent_id ) {
		$field['parent'] = $parent_id;
	}

	// Update conditional logic references because field keys have changed.
	if ( $field['conditional_logic'] ) {

		// Loop over groups
		foreach ( $field['conditional_logic'] as $group_i => $group ) {

			// Loop over rules
			foreach ( $group as $rule_i => $rule ) {
				$field['conditional_logic'][ $group_i ][ $rule_i ]['field'] = isset( $keys[ $rule['field'] ] ) ? $keys[ $rule['field'] ] : $rule['field'];
			}
		}
	}

	/**
	 * Filters the $field array after it has been duplicated.
	 *
	 * @date    12/02/2014
	 *
	 * @param array $field The field array.
	 *
	 * @since    5.0.0
	 *
	 */
	$field = apply_filters( "acf/duplicate_field", $field );

	// Update and return.
	return acf_update_field( $field );
}

// Register variation.
acf_add_filter_variations( 'acf/duplicate_field', array( 'type' ), 0 );

/**
 * acf_prepare_fields_for_export
 *
 * Returns a modified array of fields ready for export.
 *
 * @date    11/03/2014
 *
 * @param array $fields An array of fields.
 *
 * @return    array
 * @since    5.0.0
 *
 */
function acf_prepare_fields_for_export( $fields = array() ) {

	// Map function and return.
	return array_map( 'acf_prepare_field_for_export', $fields );
}

/**
 * acf_prepare_field_for_export
 *
 * Returns a modified field ready for export.
 *
 * @date    11/03/2014
 *
 * @param array $field The field array.
 *
 * @return    array
 * @since    5.0.0
 *
 */
function acf_prepare_field_for_export( $field ) {

	// Remove args.
	acf_extract_vars( $field, array(
		'ID',
		'prefix',
		'value',
		'menu_order',
		'id',
		'class',
		'parent',
		'_name',
		'_prepare',
		'_valid'
	) );

	/**
	 * Filters the $field array before being returned to the export tool.
	 *
	 * @date    12/02/2014
	 *
	 * @param array $field The field array.
	 *
	 * @since    5.0.0
	 *
	 */
	$field = apply_filters( "acf/prepare_field_for_export", $field );

	// Return field.
	return $field;
}

// Register variation.
acf_add_filter_variations( 'acf/prepare_field_for_export', array( 'type' ), 0 );

/**
 * acf_prepare_field_for_import
 *
 * Returns a modified array of fields ready for import.
 *
 * @date    11/03/2014
 *
 * @param array $fields An array of fields.
 *
 * @return    array
 * @since    5.0.0
 *
 */
function acf_prepare_fields_for_import( $fields = array() ) {

	// Ensure array indexes are clean.
	$fields = array_values( $fields );

	// Loop through fields allowing for growth.
	$i = 0;
	while ( $i < count( $fields ) ) {

		// Prepare for import.
		$field = acf_prepare_field_for_import( $fields[ $i ] );

		// Allow multiple fields to be returned (parent + children).
		if ( is_array( $field ) && ! isset( $field['key'] ) ) {

			// Replace this field ($i) with all returned fields.
			array_splice( $fields, $i, 1, $field );
		}

		// Iterate.
		$i ++;
	}

	/**
	 * Filters the $fields array before being returned to the import tool.
	 *
	 * @date    12/02/2014
	 *
	 * @param array $field The field array.
	 *
	 * @since    5.0.0
	 *
	 */
	$fields = apply_filters( 'acf/prepare_fields_for_import', $fields );

	// Return.
	return $fields;
}

/**
 * acf_prepare_field_for_import
 *
 * Returns a modified field ready for import.
 * Allows parent fields to modify themselves and also return sub fields.
 *
 * @date    11/03/2014
 *
 * @param array $field The field array.
 *
 * @return    array
 * @since    5.0.0
 *
 */
function acf_prepare_field_for_import( $field ) {

	/**
	 * Filters the $field array before being returned to the import tool.
	 *
	 * @date    12/02/2014
	 *
	 * @param array $field The field array.
	 *
	 * @since    5.0.0
	 *
	 */
	$field = apply_filters( "acf/prepare_field_for_import", $field );

	// Return field.
	return $field;
}

// Register variation.
acf_add_filter_variations( 'acf/prepare_field_for_import', array( 'type' ), 0 );