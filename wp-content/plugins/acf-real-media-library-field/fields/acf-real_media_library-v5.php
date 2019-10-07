<?php

// exit if accessed directly
if (!defined('ABSPATH'))
    exit;

// check if class already exists
if (!class_exists('acf_field_real_media_library') && defined('RML_VERSION') && version_compare(RML_VERSION, '2.7', '>=')) {
    
    class acf_field_real_media_library extends acf_field {
        
        private $rml_allow_multiple = false;
        
        function __construct($settings) {
            $this->rml_allow_multiple = version_compare(RML_VERSION, '3.1.2', '>=');
            
            $this->name     = 'real_media_library';
            $this->label    = __('Real Media Library', 'acf-real_media_library');
            $this->category = 'Relational';
            $this->defaults = array(
                'allow_null' => 1,
                'return_format' => 'ID',
                'multiple' => 0
            );
            $this->settings = $settings;
            
            // do not delete!
            parent::__construct();
        }
        
        function render_field_settings($field) {
            // disable_selection_types
            acf_render_field_setting( $field, array(
				'label' => __('Disable selection', 'acf-real_media_library'),
				'instructions'	=> __('Disable specific folder types for selection','acf-real_media_library'),
				'type'  =>  'checkbox',
				'name'  =>  'disable_selection_types',
				'choices' =>  array(
					RML_TYPE_FOLDER => __('Folders', 'acf-real_media_library'),
					RML_TYPE_COLLECTION => __('Collection', 'acf-real_media_library'),
					RML_TYPE_GALLERY => __('Galleries', 'acf-real_media_library'),
					RML_TYPE_ROOT => __('Unorganized folder', 'acf-real_media_library')
				),
				'layout' => 'horizontal'
			));
			
			// multiple
			if ($this->rml_allow_multiple) {
                acf_render_field_setting($field, array(
                    'label' => __('Allow multiple selection', 'acf-real_media_library'),
                    'message' => __('Check this to enable multiple folder selection', 'acf-real_media_library'),
                    'type' => 'radio',
        			'name' => 'multiple',
        			'choices'=> array(
        				1 => __( "Yes", 'acf-real_media_library' ),
        				0 => __( "No", 'acf-real_media_library' ),
        			),
        			'layout' => 'horizontal'
                ));
			}
            
            // return_format
            acf_render_field_setting($field, array(
                'label' => __('Return Format', 'acf-real_media_library'),
                'instructions' => '',
                'type' => 'radio',
                'name' => 'return_format',
                'choices' => array(
                    'RML Object' => __("RML Folder Object", 'acf-real_media_library'),
                    'ID' => __("RML Folder ID", 'acf-real_media_library')
                ),
                'layout' => 'horizontal'
            ));
            
        }
        
        function render_field($field) {
        	$parent = _wp_rml_root();
        	$multiple = isset($field['multiple']) ? $field['multiple'] : false;
        	$disabled = $field['disable_selection_types'] ?: array();
        	$atts = array(
    			'id'				=> $field['id'],
    			'class'				=> $field['class'] . " select2",
    			'name'				=> $field['name'],
    			'data-multiple'		=> $field['multiple'],
    			'data-allow_null'	=> $field['allow_null']
    		);
        	
            // force value to array
            $field['value'] = acf_get_array($field['value']);
            
            // parent as default
            if (count($field['value']) === 0) {
                $field['value'][] = $parent;
            }
            
            // iterate disallowd
            for ($i = 0; $i < count($field['value']); $i++) {
                if (in_array(RML_TYPE_ROOT, $disabled)) {
                    $field['value'][$i] = $field['value'][$i] == $parent ? "" : $field['value'][$i];
                }
            }
            
            // prepare options
            $options = wp_rml_dropdown($field['value'], $disabled, false);
            
            // multiple
            if ($multiple) {
                acf_hidden_input(array(
    				'type'	=> 'hidden',
    				'name'	=> $field['name'],
    			));
    			
    			$atts['multiple'] = 'multiple';
    			$atts['size'] = 5;
    			$atts['name'] .= '[]';
            }
            ?>
            <select style="width:100%!important;" <?php echo acf_esc_attr($atts); ?>><?php echo $options; ?></select>
            <?php
        }
        
        function format_value($value, $post_id, $field) {
            // force value to array
            $multiple = isset($field['multiple']) ? $field['multiple'] : false;
            
            // return single
            if (!$multiple) {
                return $this->resolve_single_value($value, $post_id, $field);
            }
            
            // process multiple
            $value = acf_get_array($value);
            $result = array();
            
            foreach ($value as $id) {
                $result[] = $this->resolve_single_value($id, $post_id, $field);
            }
            
            return $result;
        }
        
        function resolve_single_value($value, $post_id, $field) {
            if ($field['return_format'] == 'ID' || $value == "-1")
                return $value;
            if ($field['return_format'] == 'RML Object') {
                if (!function_exists('wp_rml_get_by_id')) {
                    return 'Real Media Library must be enabled';
                }
                return wp_rml_get_by_id($value, null, true);
            }
            return null;
        }
        
        /*
    	 *  input_admin_enqueue_scripts()
    	 *
    	 *  This action is called in the admin_enqueue_scripts action on the edit screen where your field is created.
    	 *  Use this action to add CSS + JavaScript to assist your render_field() action.
    	 *
    	 *  @type	action (admin_enqueue_scripts)
    	 *  @since	3.6
    	 *  @date	23/01/13
    	 *
    	 *  @param	n/a
    	 *  @return	n/a
    	 */
    	function input_admin_enqueue_scripts() {
    		
    		// vars
    		$url = $this->settings['url'];
    		$version = $this->settings['version'];
    		
    		
    		// register & include JS
    		wp_register_script( 'select2', "{$url}assets/js/select2.min.js", array('acf-input'), $version );
    		wp_enqueue_script('select2');
    		wp_register_script( 'acf-real_media_library', "{$url}assets/js/input.js", array('acf-input'), $version );
    		wp_enqueue_script('acf-real_media_library');
    		
    		
    		// register & include CSS
    		wp_register_style( 'select2', "{$url}assets/css/select2.min.css", array('acf-input'), $version );
    		wp_enqueue_style('select2');
    		
    	}
        
    }
    
    // initialize
    new acf_field_real_media_library($this->settings);
}
?>