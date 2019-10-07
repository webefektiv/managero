<?php 

if( function_exists('acf_add_local_field_group') ):

acf_add_local_field_group(array(
	'key' => 'group_profil_candidat',
	'title' => 'Profil Candidat',
	'fields' => array(
		array(
			'key' => 'field_5d36f300c398b',
			'label' => 'Profil candidat',
			'name' => 'profil_candidat',
			'type' => 'flexible_content',
			'instructions' => '',
			'required' => 0,
			'conditional_logic' => 0,
			'wrapper' => array(
				'width' => '',
				'class' => '',
				'id' => '',
			),
			'acfe_permissions' => '',
			'acfe_flexible_stylised_button' => 0,
			'acfe_flexible_hide_empty_message' => 1,
			'acfe_flexible_layouts_thumbnails' => 0,
			'acfe_flexible_layouts_templates' => 0,
			'acfe_flexible_close_button' => 0,
			'acfe_flexible_copy_paste' => 0,
			'acfe_flexible_modal_edition' => 1,
			'acfe_flexible_modal' => array(
				'acfe_flexible_modal_enabled' => '0',
			),
			'layouts' => array(
				'5d36f3a9effb7' => array(
					'key' => '5d36f3a9effb7',
					'name' => 'profil_candidat',
					'label' => 'Profil candidat',
					'display' => 'block',
					'sub_fields' => array(
						array(
							'key' => 'field_5d03586cc72e3',
							'label' => 'Date personale',
							'name' => 'date_personale',
							'type' => 'group',
							'instructions' => '',
							'required' => 0,
							'conditional_logic' => 0,
							'wrapper' => array(
								'width' => '50',
								'class' => '',
								'id' => '',
							),
							'layout' => 'row',
							'sub_fields' => array(
								array(
									'key' => 'field_5d02a1b577752',
									'label' => 'Prenume',
									'name' => 'prenume',
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
									'placeholder' => 'Prenume',
									'prepend' => '',
									'append' => '',
									'maxlength' => 40,
								),
								array(
									'key' => 'field_5d02a408b02bc',
									'label' => 'Particula',
									'name' => 'particula',
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
									'placeholder' => 'Particula',
									'prepend' => '',
									'append' => '',
									'maxlength' => 10,
								),
								array(
									'key' => 'field_5d02a1aa77751',
									'label' => 'Nume',
									'name' => 'nume',
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
									'placeholder' => 'Nume',
									'prepend' => '',
									'append' => '',
									'maxlength' => 40,
								),
								array(
									'key' => 'field_5d02a41eb02bd',
									'label' => 'Formula adresare',
									'name' => 'formula_adresare',
									'type' => 'select',
									'instructions' => '',
									'required' => 0,
									'conditional_logic' => 0,
									'wrapper' => array(
										'width' => '',
										'class' => '',
										'id' => '',
									),
									'choices' => array(
										'Mr.' => 'Mr.',
										'Mrs.' => 'Mrs.',
										'Ms.' => 'Ms.',
										'Miss' => 'Miss',
									),
									'default_value' => array(
									),
									'allow_null' => 0,
									'multiple' => 0,
									'ui' => 0,
									'return_format' => 'value',
									'ajax' => 0,
									'placeholder' => '',
								),
								array(
									'key' => 'field_5d02a44908555',
									'label' => 'Titlu',
									'name' => 'titlu',
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
									'placeholder' => 'Titlu',
									'prepend' => '',
									'append' => '',
									'maxlength' => '',
								),
								array(
									'key' => 'field_5d02a47508556',
									'label' => 'Nickaname',
									'name' => 'nickaname',
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
									'placeholder' => 'Nickaname',
									'prepend' => '',
									'append' => '',
									'maxlength' => 40,
								),
								array(
									'key' => 'field_5d02a48b08557',
									'label' => 'Anul nasterii',
									'name' => 'anul_nasterii',
									'type' => 'select',
									'instructions' => '',
									'required' => 0,
									'conditional_logic' => 0,
									'wrapper' => array(
										'width' => '',
										'class' => '',
										'id' => '',
									),
									'choices' => array(
										1930 => '1930',
										1931 => '1931',
										1932 => '1932',
										1933 => '1933',
										1934 => '1934',
										1935 => '1935',
										1936 => '1936',
										1937 => '1937',
										1938 => '1938',
										1939 => '1939',
										1940 => '1940',
										1941 => '1941',
										1942 => '1942',
										1943 => '1943',
										1944 => '1944',
										1945 => '1945',
										1946 => '1946',
										1947 => '1947',
										1948 => '1948',
										1949 => '1949',
										1950 => '1950',
										1951 => '1951',
										1952 => '1952',
										1953 => '1953',
										1954 => '1954',
										1955 => '1955',
										1956 => '1956',
										1957 => '1957',
										1958 => '1958',
										1959 => '1959',
										1960 => '1960',
										1961 => '1961',
										1962 => '1962',
										1963 => '1963',
										1964 => '1964',
										1965 => '1965',
										1966 => '1966',
										1967 => '1967',
										1968 => '1968',
										1969 => '1969',
										1970 => '1970',
										1971 => '1971',
										1972 => '1972',
										1973 => '1973',
										1974 => '1974',
										1975 => '1975',
										1976 => '1976',
										1977 => '1977',
										1978 => '1978',
										1979 => '1979',
										1980 => '1980',
										1981 => '1981',
										1982 => '1982',
										1983 => '1983',
										1984 => '1984',
										1985 => '1985',
										1986 => '1986',
										1987 => '1987',
										1988 => '1988',
										1989 => '1989',
										1990 => '1990',
										1991 => '1991',
										1992 => '1992',
										1993 => '1993',
										1994 => '1994',
										1995 => '1995',
										1996 => '1996',
										1997 => '1997',
										1998 => '1998',
										1999 => '1999',
										2000 => '2000',
										2001 => '2001',
										2002 => '2002',
										2003 => '2003',
										2004 => '2004',
										2005 => '2005',
										2006 => '2006',
										2007 => '2007',
										2008 => '2008',
										2009 => '2009',
										2010 => '2010',
									),
									'default_value' => array(
									),
									'allow_null' => 1,
									'multiple' => 0,
									'ui' => 0,
									'return_format' => 'value',
									'ajax' => 0,
									'placeholder' => '',
								),
								array(
									'key' => 'field_5d02a5d4574b3',
									'label' => 'Sex',
									'name' => 'sex',
									'type' => 'select',
									'instructions' => '',
									'required' => 0,
									'conditional_logic' => 0,
									'wrapper' => array(
										'width' => '',
										'class' => '',
										'id' => '',
									),
									'choices' => array(
										'masculin' => 'Masculin',
										'feminin' => 'Feminin',
									),
									'default_value' => array(
									),
									'allow_null' => 1,
									'multiple' => 0,
									'ui' => 0,
									'return_format' => 'value',
									'ajax' => 0,
									'placeholder' => '',
								),
								array(
									'key' => 'field_5d02b633ed249',
									'label' => 'Judetul de resedinta',
									'name' => 'judet_candidat',
									'type' => 'select',
									'instructions' => '',
									'required' => 0,
									'conditional_logic' => 0,
									'wrapper' => array(
										'width' => '',
										'class' => '',
										'id' => '',
									),
									'choices' => array(
										'AB' => 'Alba',
										'AR' => 'Arad',
										'AG' => 'Arges',
										'BC' => 'Bacau',
										'BH' => 'Bihor',
										'BN' => 'Bistrita-Nasaud',
										'BT' => 'Botosani',
										'BV' => 'Brasov',
										'BR' => 'Braila',
										'BUC' => 'Bucuresti',
										'BZ' => 'Buzau',
										'CS' => 'Caras-Severin',
										'CJ' => 'Cluj',
										'CT' => 'Constanta',
										'CV' => 'Covasna',
										'DB' => 'Dimbovita',
										'DJ' => 'Dolj',
										'GL' => 'Galati',
										'GJ' => 'Gorj',
										'HR' => 'Harghita',
										'HD' => 'Hunedoara',
										'IL' => 'Ialomita',
										'IS' => 'Iasi',
										'IF' => 'Ilfov',
										'MM' => 'Maramures',
										'MH' => 'Mehedinti',
										'MS' => 'Mures',
										'NT' => 'Neamt',
										'OT' => 'Olt',
										'PH' => 'Prahova',
										'SM' => 'Satu_Mare',
										'SJ' => 'Salaj',
										'SB' => 'Sibiu',
										'SV' => 'Suceava',
										'TR' => 'Teleorman',
										'TM' => 'Timis',
										'TL' => 'Tulcea',
										'VS' => 'Vaslui',
										'VL' => 'Vilcea',
										'VN' => 'Vrancea',
										'B' => 'Bucuresti',
										'CL' => 'Calarasi',
										'GR' => 'Giurgiu',
									),
									'default_value' => array(
									),
									'allow_null' => 0,
									'multiple' => 0,
									'ui' => 0,
									'return_format' => 'array',
									'ajax' => 0,
									'placeholder' => '',
								),
							),
						),
						array(
							'key' => 'field_5d02a70f33a00',
							'label' => 'Educatie',
							'name' => 'educatie',
							'type' => 'repeater',
							'instructions' => '',
							'required' => 0,
							'conditional_logic' => 0,
							'wrapper' => array(
								'width' => '',
								'class' => '',
								'id' => '',
							),
							'collapsed' => '',
							'min' => 0,
							'max' => 0,
							'layout' => 'table',
							'button_label' => 'Adauga educatie',
							'sub_fields' => array(
								array(
									'key' => 'field_5d02a76f33a01',
									'label' => 'Educatie',
									'name' => 'educatie',
									'type' => 'text',
									'instructions' => '- de la pana la (luna, anul) - titlul obtinut - institutia (universitatea etc.) - departamentul (facultatea etc.)',
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
							),
						),
						array(
							'key' => 'field_5d02a88ad9019',
							'label' => 'Certificari',
							'name' => 'certificari',
							'type' => 'repeater',
							'instructions' => '',
							'required' => 0,
							'conditional_logic' => 0,
							'wrapper' => array(
								'width' => '',
								'class' => '',
								'id' => '',
							),
							'collapsed' => '',
							'min' => 0,
							'max' => 0,
							'layout' => 'table',
							'button_label' => 'Adauga certificare',
							'sub_fields' => array(
								array(
									'key' => 'field_5d19bb78093bd',
									'label' => 'Certificare',
									'name' => 'certificare',
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
							),
						),
						array(
							'key' => 'field_5d02a8c66c6d6',
							'label' => 'Limba engleza',
							'name' => 'limba_engleza',
							'type' => 'select',
							'instructions' => '',
							'required' => 0,
							'conditional_logic' => 0,
							'wrapper' => array(
								'width' => '50',
								'class' => '',
								'id' => '',
							),
							'choices' => array(
								'A1' => 'A1 ( nivel începător )',
								'A2' => 'A2 (nivel elementar)',
								'B1' => 'B1 (nivel intermediar)',
								'B2' => 'B2 (nivel post-intermediar)',
								'C1' => 'C1 (nivel avansat)',
								'C2' => 'C2 (nivel profesionist)',
							),
							'default_value' => array(
							),
							'allow_null' => 0,
							'multiple' => 0,
							'ui' => 0,
							'ajax' => 0,
							'return_format' => 'value',
							'placeholder' => '',
						),
						array(
							'key' => 'field_5d02a93884b55',
							'label' => 'Alte Limbi',
							'name' => 'alte_limbi',
							'type' => 'repeater',
							'instructions' => '',
							'required' => 0,
							'conditional_logic' => 0,
							'wrapper' => array(
								'width' => '',
								'class' => '',
								'id' => '',
							),
							'collapsed' => '',
							'min' => 0,
							'max' => 0,
							'layout' => 'table',
							'button_label' => 'Adauga limba',
							'sub_fields' => array(
								array(
									'key' => 'field_5d02a94684b56',
									'label' => 'Limba + Nivel',
									'name' => 'limba_nivel',
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
									'placeholder' => 'Limba + Nivel',
									'prepend' => '',
									'append' => '',
									'maxlength' => 150,
								),
							),
						),
						array(
							'key' => 'field_5d02a9718c176',
							'label' => 'Relevant Skils',
							'name' => 'relevant_skils',
							'type' => 'textarea',
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
							'maxlength' => '',
							'rows' => 3,
							'new_lines' => '',
						),
						array(
							'key' => 'field_5d02a9a32698a',
							'label' => 'Link-uri',
							'name' => 'link-uri',
							'type' => 'repeater',
							'instructions' => '',
							'required' => 0,
							'conditional_logic' => 0,
							'wrapper' => array(
								'width' => '',
								'class' => '',
								'id' => '',
							),
							'collapsed' => '',
							'min' => 0,
							'max' => 0,
							'layout' => 'table',
							'button_label' => 'Adauga link nou',
							'sub_fields' => array(
								array(
									'key' => 'field_5d02a9d32698b',
									'label' => 'Link',
									'name' => 'link',
									'type' => 'url',
									'instructions' => '',
									'required' => 0,
									'conditional_logic' => 0,
									'wrapper' => array(
										'width' => '70',
										'class' => '',
										'id' => '',
									),
									'default_value' => '',
									'placeholder' => 'ex	https://www.facebook.com/managero',
								),
								array(
									'key' => 'field_5d02a9e52698c',
									'label' => 'Titlu',
									'name' => 'titlu',
									'type' => 'text',
									'instructions' => '',
									'required' => 0,
									'conditional_logic' => 0,
									'wrapper' => array(
										'width' => '31',
										'class' => '',
										'id' => '',
									),
									'default_value' => '',
									'placeholder' => 'ex facebook',
									'prepend' => '',
									'append' => '',
									'maxlength' => '',
								),
							),
						),
						array(
							'key' => 'field_5d02aa9d2b91d',
							'label' => 'Experienta',
							'name' => 'experienta',
							'type' => 'repeater',
							'instructions' => '',
							'required' => 0,
							'conditional_logic' => 0,
							'wrapper' => array(
								'width' => '',
								'class' => 'experintaRow',
								'id' => '',
							),
							'collapsed' => '',
							'min' => 0,
							'max' => 0,
							'layout' => 'block',
							'button_label' => 'Adauga companie',
							'sub_fields' => array(
								array(
									'key' => 'field_5d02ab22a09ff',
									'label' => 'Companie',
									'name' => 'companie',
									'type' => 'text',
									'instructions' => '',
									'required' => 0,
									'conditional_logic' => 0,
									'wrapper' => array(
										'width' => '',
										'class' => 'companief',
										'id' => '',
									),
									'default_value' => '',
									'placeholder' => '',
									'prepend' => '',
									'append' => '',
									'maxlength' => '',
								),
								array(
									'key' => 'field_5d19d26878f51',
									'label' => 'Descriere companie',
									'name' => 'descriere_companie',
									'type' => 'textarea',
									'instructions' => '',
									'required' => 0,
									'conditional_logic' => 0,
									'wrapper' => array(
										'width' => '',
										'class' => 'descriere-companie',
										'id' => '',
									),
									'default_value' => '',
									'placeholder' => '',
									'maxlength' => '',
									'rows' => 4,
									'new_lines' => '',
								),
								array(
									'key' => 'field_5d02ab5ea0a00',
									'label' => 'Post',
									'name' => 'post',
									'type' => 'repeater',
									'instructions' => '',
									'required' => 0,
									'conditional_logic' => 0,
									'wrapper' => array(
										'width' => '',
										'class' => 'postExperienta',
										'id' => '',
									),
									'collapsed' => '',
									'min' => 0,
									'max' => 0,
									'layout' => 'block',
									'button_label' => 'Adauga post',
									'sub_fields' => array(
										array(
											'key' => 'field_5d0360c5c0b3f',
											'label' => 'Post',
											'name' => 'post',
											'type' => 'text',
											'instructions' => '',
											'required' => 0,
											'conditional_logic' => 0,
											'wrapper' => array(
												'width' => '30',
												'class' => 'post-sub-field',
												'id' => 'post-titlu',
											),
											'default_value' => '',
											'placeholder' => 'postul',
											'prepend' => '',
											'append' => '',
											'maxlength' => '',
										),
										array(
											'key' => 'field_5d02ab66a0a01',
											'label' => 'De la',
											'name' => 'de_la',
											'type' => 'date_picker',
											'instructions' => '',
											'required' => 0,
											'conditional_logic' => 0,
											'wrapper' => array(
												'width' => '20',
												'class' => 'post-sub-field',
												'id' => 'de-la',
											),
											'display_format' => 'F Y',
											'return_format' => 'F Y',
											'first_day' => 1,
										),
										array(
											'key' => 'field_5d02ab6aa0a02',
											'label' => 'Pana la',
											'name' => 'pana_la',
											'type' => 'date_picker',
											'instructions' => '',
											'required' => 0,
											'conditional_logic' => array(
												array(
													array(
														'field' => 'field_5d1081428c2a1',
														'operator' => '!=',
														'value' => 'job curent',
													),
												),
											),
											'wrapper' => array(
												'width' => '20',
												'class' => 'post-sub-field',
												'id' => 'pana-la',
											),
											'display_format' => 'F Y',
											'return_format' => 'F Y',
											'first_day' => 1,
										),
										array(
											'key' => 'field_5d1081428c2a1',
											'label' => 'Job curent',
											'name' => 'ultimul_job',
											'type' => 'checkbox',
											'instructions' => '',
											'required' => 0,
											'conditional_logic' => 0,
											'wrapper' => array(
												'width' => '15',
												'class' => '',
												'id' => 'job-curent',
											),
											'choices' => array(
												'job curent' => 'job curent',
											),
											'allow_custom' => 0,
											'default_value' => array(
												0 => 'job-vechi',
											),
											'layout' => 'vertical',
											'toggle' => 0,
											'return_format' => 'value',
											'save_custom' => 0,
										),
										array(
											'key' => 'field_5d02ab74a0a03',
											'label' => 'Ierarhie',
											'name' => 'ierarhie',
											'type' => 'select',
											'instructions' => '',
											'required' => 0,
											'conditional_logic' => 0,
											'wrapper' => array(
												'width' => '15',
												'class' => 'post-sub-field',
												'id' => 'post-ierarhie',
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
											'default_value' => array(
											),
											'allow_null' => 0,
											'multiple' => 0,
											'ui' => 0,
											'return_format' => 'value',
											'ajax' => 0,
											'placeholder' => '',
										),
										array(
											'key' => 'field_5d36f94951820',
											'label' => 'Departament',
											'name' => 'departament',
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
											'key' => 'field_5d02ab7fa0a04',
											'label' => 'Descriere Job',
											'name' => 'descriere_job',
											'type' => 'textarea',
											'instructions' => '',
											'required' => 0,
											'conditional_logic' => 0,
											'wrapper' => array(
												'width' => '50',
												'class' => 'post-sub-field',
												'id' => 'descriere-job',
											),
											'default_value' => '',
											'placeholder' => 'descriere job',
											'maxlength' => '',
											'rows' => 4,
											'new_lines' => '',
										),
										array(
											'key' => 'field_5d02ab92a0a05',
											'label' => 'Alte detalii',
											'name' => 'alte_detalii',
											'type' => 'textarea',
											'instructions' => '',
											'required' => 0,
											'conditional_logic' => 0,
											'wrapper' => array(
												'width' => '50',
												'class' => 'post-sub-field',
												'id' => 'alte-detalii',
											),
											'default_value' => '',
											'placeholder' => 'alte detalii',
											'maxlength' => '',
											'rows' => 4,
											'new_lines' => '',
										),
									),
								),
							),
						),
						array(
							'key' => 'field_5d02aa4957202',
							'label' => 'Salariu minim acceptat',
							'name' => 'salariu_minim_accepta',
							'type' => 'number',
							'instructions' => '',
							'required' => 0,
							'conditional_logic' => 0,
							'wrapper' => array(
								'width' => '33',
								'class' => '',
								'id' => '',
							),
							'default_value' => '',
							'placeholder' => 'suma in euro',
							'prepend' => '',
							'append' => '',
							'min' => '',
							'max' => '',
							'step' => '',
						),
						array(
							'key' => 'field_5d07990135009',
							'label' => 'Alte cerinte',
							'name' => 'alte_cerinte',
							'type' => 'textarea',
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
							'maxlength' => '',
							'rows' => 3,
							'new_lines' => 'br',
						),
						array(
							'key' => 'field_5d07976b65f78',
							'label' => 'Note si comentarii libere',
							'name' => 'note_comentarii',
							'type' => 'textarea',
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
							'maxlength' => '',
							'rows' => 3,
							'new_lines' => 'br',
						),
						array(
							'key' => 'field_5d038a4dc9faf',
							'label' => 'Imagine	profil',
							'name' => 'imagine_profil',
							'type' => 'image_aspect_ratio_crop',
							'instructions' => '',
							'required' => 0,
							'conditional_logic' => 0,
							'wrapper' => array(
								'width' => '50',
								'class' => '',
								'id' => '',
							),
							'aspect_ratio_width' => 300,
							'aspect_ratio_height' => 300,
							'return_format' => 'url',
							'preview_size' => 'medium',
							'library' => 'uploadedTo',
							'min_width' => '',
							'min_height' => '',
							'min_size' => '',
							'max_width' => '',
							'max_height' => '',
							'max_size' => '',
							'mime_types' => '',
						),
						array(
							'key' => 'field_5d03c7b9904f5',
							'label' => 'Imagine cover',
							'name' => 'imagine_cover',
							'type' => 'image_aspect_ratio_crop',
							'instructions' => '',
							'required' => 0,
							'conditional_logic' => 0,
							'wrapper' => array(
								'width' => '50',
								'class' => '',
								'id' => '',
							),
							'aspect_ratio_width' => 1100,
							'aspect_ratio_height' => 299,
							'return_format' => 'url',
							'preview_size' => 'large',
							'library' => 'uploadedTo',
							'min_width' => 1100,
							'min_height' => 300,
							'min_size' => '',
							'max_width' => '',
							'max_height' => '',
							'max_size' => '',
							'mime_types' => '',
						),
						array(
							'key' => 'field_5d36f3aad0988',
							'label' => 'Date profil',
							'name' => 'date_profil',
							'type' => 'group',
							'instructions' => '',
							'required' => 0,
							'conditional_logic' => 0,
							'wrapper' => array(
								'width' => '',
								'class' => '',
								'id' => '',
							),
							'layout' => 'block',
							'sub_fields' => array(
								array(
									'key' => 'field_5d36f440d0989',
									'label' => 'Nume profil',
									'name' => 'nume_profil',
									'type' => 'text',
									'instructions' => '',
									'required' => 0,
									'conditional_logic' => 0,
									'wrapper' => array(
										'width' => '50',
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
									'key' => 'field_5d36f44dd098a',
									'label' => 'Descriere profil',
									'name' => 'descriere_profil',
									'type' => 'text',
									'instructions' => '',
									'required' => 0,
									'conditional_logic' => 0,
									'wrapper' => array(
										'width' => '50',
										'class' => '',
										'id' => '',
									),
									'default_value' => '',
									'placeholder' => '',
									'prepend' => '',
									'append' => '',
									'maxlength' => '',
								),
							),
						),
						array(
							'key' => 'field_5d3838021e1d8',
							'label' => 'Preview',
							'name' => 'preview',
							'type' => 'button',
							'instructions' => '',
							'required' => 0,
							'conditional_logic' => 0,
							'wrapper' => array(
								'width' => '50',
								'class' => '',
								'id' => '',
							),
							'acfe_permissions' => '',
							'button_value' => 'Preview',
							'button_attributes' => array(
								'button_type' => 'button',
								'button_class' => '',
								'button_id' => '',
							),
							'button_wrapper' => array(
								'button_before' => '',
								'button_after' => '',
							),
							'acfe_validate' => '',
							'acfe_update' => '',
						),
						array(
							'key' => 'field_5d382d8c89697',
							'label' => 'Salveaza',
							'name' => 'salveaza',
							'type' => 'button',
							'instructions' => '',
							'required' => 0,
							'conditional_logic' => 0,
							'wrapper' => array(
								'width' => '50',
								'class' => '',
								'id' => '',
							),
							'acfe_permissions' => '',
							'button_value' => 'Salveaza',
							'button_attributes' => array(
								'button_type' => 'submit',
								'button_class' => '',
								'button_id' => '',
							),
							'button_wrapper' => array(
								'button_before' => '',
								'button_after' => '',
							),
							'acfe_validate' => '',
							'acfe_update' => '',
						),
					),
					'min' => '',
					'max' => '',
				),
			),
			'button_label' => 'Adauga profil',
			'min' => '',
			'max' => '',
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
	'modified' => 1564046724,
));

endif;