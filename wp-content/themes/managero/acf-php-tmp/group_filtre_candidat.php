<?php 

if( function_exists('acf_add_local_field_group') ):

acf_add_local_field_group(array(
	'key' => 'group_filtre_candidat',
	'title' => 'Filtre salvate',
	'fields' => array(
		array(
			'key' => 'field_5d08e5ab78666',
			'label' => 'Filtre',
			'name' => 'filtru_salvat',
			'type' => 'repeater',
			'instructions' => '',
			'required' => 0,
			'conditional_logic' => 0,
			'wrapper' => array(
				'width' => '',
				'class' => '',
				'id' => '',
			),
			'collapsed' => 'field_5d05e83b060cf',
			'min' => 0,
			'max' => 0,
			'layout' => 'table',
			'button_label' => '',
			'sub_fields' => array(
				array(
					'key' => 'field_5d08e5ab7d950',
					'label' => 'Titlu filtru',
					'name' => 'titlu_filtru',
					'type' => 'text',
					'instructions' => '',
					'required' => 0,
					'conditional_logic' => 0,
					'wrapper' => array(
						'width' => '',
						'class' => '',
						'id' => '',
					),
					'default_value' => '',
					'placeholder' => '',
					'prepend' => '',
					'append' => '',
					'maxlength' => '',
				),
				array(
					'key' => 'field_5d08e5ab7d999',
					'label' => 'Locatie',
					'name' => 'locatie',
					'type' => 'checkbox',
					'instructions' => '',
					'required' => 0,
					'conditional_logic' => 0,
					'wrapper' => array(
						'width' => '30',
						'class' => '',
						'id' => '',
					),
					'choices' => array(
						12 => 'Caras-Severin',
						20 => 'Harghita',
						'ABA' => 'Alba',
						'ARD' => 'Arad',
						'ARG' => 'Arges',
						'BAC' => 'Bacau',
						'BIH' => 'Bihor',
						'BIS' => 'Bistrita-Nasaud',
						'BOT' => 'Botosani',
						'BRA' => 'Brasov',
						'BRL' => 'Braila',
						'BUC' => 'Bucuresti',
						'BUZ' => 'Buzau',
						'CAL' => 'Calarasi',
						'CLJ' => 'Cluj',
						'COV' => 'Covasna',
						'CRS' => 'Caras Severin',
						'CST' => 'Constanta',
						'DAM' => 'Dambovita',
						'DLJ' => 'Dolj',
						'GAL' => 'Galati',
						'GIU' => 'Giurgiu',
						'GOR' => 'Gorj',
						'HRG' => 'Hargita',
						'HUN' => 'Hunedoara',
						'IAL' => 'Ialomita',
						'IAS' => 'Iasi',
						'ILF' => 'Ilfov',
						'MAR' => 'Maramures',
						'MEH' => 'Mehedinti',
						'MUR' => 'Mures',
						'NEM' => 'Neamt',
						'OLT' => 'Olt',
						'PRA' => 'Prahova',
						'SAL' => 'Salaj',
						'SAT' => 'Satu Mare',
						'SIB' => 'Sibiu',
						'SUC' => 'Suceava',
						'TEL' => 'Teleorman',
						'TIM' => 'Timis',
						'TUL' => 'Tulcea',
						'VAL' => 'Valcea',
						'VAS' => 'Vaslui',
						'VRA' => 'Vrancea',
					),
					'allow_custom' => 0,
					'default_value' => array(
					),
					'layout' => 'vertical',
					'toggle' => 1,
					'return_format' => 'label',
					'save_custom' => 0,
				),
				array(
					'key' => 'field_5d08e5ab7d9db',
					'label' => 'Nivel Ierarhic',
					'name' => 'nivel_ierarhic',
					'type' => 'checkbox',
					'instructions' => '',
					'required' => 0,
					'conditional_logic' => 0,
					'wrapper' => array(
						'width' => '18',
						'class' => '',
						'id' => '',
					),
					'choices' => array(
						'ceo' => 'ceo',
						'ceo -1' => 'ceo -1',
						'ceo -2' => 'ceo -2',
						'middle management' => 'middle management',
						'board executive' => 'board executive',
						'board non-executive' => 'board non-executive',
						'alt nivel' => 'alt nivel',
					),
					'allow_custom' => 0,
					'default_value' => array(
					),
					'layout' => 'vertical',
					'toggle' => 1,
					'return_format' => 'value',
					'save_custom' => 0,
				),
				array(
					'key' => 'field_5d08e5ab7da1b',
					'label' => 'Domeniu',
					'name' => 'domeniu',
					'type' => 'taxonomy',
					'instructions' => '',
					'required' => 0,
					'conditional_logic' => 0,
					'wrapper' => array(
						'width' => '20',
						'class' => '',
						'id' => '',
					),
					'taxonomy' => 'sector',
					'field_type' => 'checkbox',
					'add_term' => 1,
					'save_terms' => 0,
					'load_terms' => 0,
					'return_format' => 'id',
					'multiple' => 0,
					'allow_null' => 0,
				),
				array(
					'key' => 'field_5d08e5ab7da5b',
					'label' => 'Salariu minim',
					'name' => 'salariu_minim',
					'type' => 'number',
					'instructions' => '',
					'required' => 0,
					'conditional_logic' => 0,
					'wrapper' => array(
						'width' => '15',
						'class' => '',
						'id' => '',
					),
					'default_value' => '',
					'placeholder' => 'salariu minim in euro',
					'prepend' => '',
					'append' => '',
					'min' => '',
					'max' => '',
					'step' => '',
				),
			),
		),
	),
	'location' => array(
		array(
			array(
				'param' => 'post_type',
				'operator' => '==',
				'value' => 'candidate',
			),
		),
	),
	'menu_order' => 0,
	'position' => 'normal',
	'style' => 'default',
	'label_placement' => 'top',
	'instruction_placement' => 'label',
	'hide_on_screen' => '',
	'active' => 1,
	'description' => '',
	'acfe_display_title' => '',
	'acfe_autosync' => array(
		0 => 'php',
	),
	'acfe_permissions' => '',
	'acfe_note' => '',
	'acfe_meta' => '',
	'modified' => 1564046784,
));

endif;