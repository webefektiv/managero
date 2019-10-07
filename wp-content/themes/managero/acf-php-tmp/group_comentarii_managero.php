<?php 

if( function_exists('acf_add_local_field_group') ):

acf_add_local_field_group(array(
	'key' => 'group_comentarii_managero',
	'title' => 'Comentarii managero',
	'fields' => array(
		array(
			'key' => 'field_5ce51b95ed5ea',
			'label' => 'Comentari',
			'name' => 'comentarii_mangero',
			'type' => 'textarea',
			'instructions' => 'zona de comentarii managero ( visibile doar de catre admin )',
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
			'rows' => '',
			'new_lines' => '',
		),
	),
	'location' => array(
		array(
			array(
				'param' => 'post_type',
				'operator' => '==',
				'value' => 'job',
			),
		),
		array(
			array(
				'param' => 'post_type',
				'operator' => '==',
				'value' => 'employer',
			),
		),
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
	'hide_on_screen' => array(
		0 => 'the_content',
		1 => 'excerpt',
		2 => 'revisions',
		3 => 'author',
		4 => 'tags',
		5 => 'send-trackbacks',
	),
	'active' => 1,
	'description' => '',
	'acfe_display_title' => '',
	'acfe_autosync' => array(
		0 => 'php',
	),
	'acfe_permissions' => '',
	'acfe_note' => '',
	'acfe_meta' => '',
	'modified' => 1564046814,
));

endif;