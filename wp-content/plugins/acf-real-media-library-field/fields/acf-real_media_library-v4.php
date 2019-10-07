<?php
/**
 * @deprecated since version 5
 * Only usable with version 5.
 */

// exit if accessed directly
if( ! defined( 'ABSPATH' ) ) exit;


// check if class already exists
if (!class_exists('acf_field_real_media_library') && defined('RML_VERSION') && version_compare(RML_VERSION, '2.7', '>=')) {

	class acf_field_real_media_library extends acf_field {
		
		// vars
		var $settings, // will hold info such as dir / path
			$defaults; // will hold default field options
			
		private $rml_allow_multiple = false;
			
			
		/*
		*  __construct
		*
		*  Set name / label needed for actions / filters
		*
		*  @since	3.6
		*  @date	23/01/13
		*/
		
		function __construct( $settings )
		{
			$this->rml_allow_multiple = version_compare(RML_VERSION, '3.1.2', '>=');
			
			// vars
			$this->name = 'real_media_library';
			$this->label = __('Real Media Library');
			$this->category = __('Relational', 'acf-real_media_library'); // Basic, Content, Choice, etc
			$this->defaults = array(
				'allow_null' => 1,
				'disable_selection_types' => array(),
                'return_format' => 'ID',
                'multiple' => 0
			);
			
			// do not delete!
	    	parent::__construct();
	    	
	    	// settings
			$this->settings = $settings;
		}
		
		
		/*
		*  create_options()
		*
		*  Create extra options for your field. This is rendered when editing a field.
		*  The value of $field['name'] can be used (like below) to save extra data to the $field
		*
		*  @type	action
		*  @since	3.6
		*  @date	23/01/13
		*
		*  @param	$field	- an array holding all the field's data
		*/
		
		function create_options( $field )
		{
			// defaults
			$field = array_merge($this->defaults, $field);
			
			// key is needed in the field names to correctly save the data
			$key = $field['name'];
			
			
			// Create Field Options HTML
			?>
	<tr class="field_option field_option_<?php echo $this->name; ?>">
		<td class="label">
			<label><?php _e('Disable selection', 'acf-real_media_library'); ?></label>
			<p class="description"><?php _e('Disable specific folder types for selection','acf-real_media_library'); ?></p>
		</td>
		<td>
			<?php
			do_action('acf/create_field', array(
				'name'	=>	'fields['.$key.'][disable_selection_types]',
				'value'	=>	$field['disable_selection_types'],
				'type'  =>  'checkbox',
				'choices' =>  array(
					RML_TYPE_FOLDER => __('Folders', 'acf-real_media_library'),
					RML_TYPE_COLLECTION => __('Collection', 'acf-real_media_library'),
					RML_TYPE_GALLERY => __('Galleries', 'acf-real_media_library'),
					RML_TYPE_ROOT => __('Unorganized folder', 'acf-real_media_library')
				),
				'layout' => 'horizontal'
			));
			
			?>
		</td>
	</tr>
	<?php
	if ($this->rml_allow_multiple) {
	?>
	<tr class="field_option field_option_<?php echo $this->name; ?>">
		<td class="label">
			<label><?php _e('Allow multiple selection', 'acf-real_media_library'); ?></label>
			<p class="description"><?php _e('Check this to enable multiple folder selection','acf-real_media_library'); ?></p>
		</td>
		<td>
			<?php
			do_action('acf/create_field', array(
				'name'	=>	'fields['.$key.'][multiple]',
				'value'	=>	$field['multiple'],
				'type' => 'radio',
    			'choices'=> array(
    				1 => __( "Yes", 'acf-real_media_library' ),
    				0 => __( "No", 'acf-real_media_library' ),
    			),
    			'layout' => 'horizontal'
			));
			
			?>
		</td>
	</tr>
	<?php
	}
	?>
	<tr class="field_option field_option_<?php echo $this->name; ?>">
		<td class="label">
			<label><?php _e('Return Format', 'acf-real_media_library'); ?></label>
		</td>
		<td>
			<?php
			do_action('acf/create_field', array(
				'name'	=>	'fields['.$key.'][return_format]',
				'value'	=>	$field['return_format'],
				'type' => 'radio',
                'choices' => array(
                    'RML Object' => __("RML Folder Object", 'acf-real_media_library'),
                    'ID' => __("RML Folder ID", 'acf-real_media_library')
                ),
                'layout' => 'horizontal'
			));
			
			?>
		</td>
	</tr>
			<?php
			
		}
		
		
		/*
		*  create_field()
		*
		*  Create the HTML interface for your field
		*
		*  @param	$field - an array holding all the field's data
		*
		*  @type	action
		*  @since	3.6
		*  @date	23/01/13
		*/
		
		function create_field( $field )
		{
			// defaults
			$field = array_merge($this->defaults, $field);
			$disabled = $field['disable_selection_types'] ?: array();
			
			$parent = _wp_rml_root();
        	$multiple = isset($field['multiple']) ? $field['multiple'] : false;
        	$atts = array(
    			'id'				=> $field['id'],
    			'class'				=> $field['class'] . " select2",
    			'name'				=> $field['name'],
    			'data-multiple'		=> $field['multiple'],
    			'data-allow_null'	=> $field['allow_null']
    		);
        	
            // force value to array
            $field['value'] = $this->acf_get_array($field['value']);
            
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
    			echo '<input type="hidden" name="' . $field['name'] . '" />';
    			
    			$atts['multiple'] = 'multiple';
    			$atts['size'] = 5;
    			$atts['name'] .= '[]';
            }
            echo '<div>';
            ?>
            <select style="width:100%!important;" <?php echo $this->acf_esc_attr($atts); ?>><?php echo $options; ?></select>
            <?php
            echo '</div>';
		}
		
		/*
		*  format_value_for_api()
		*
		*  This filter is applied to the $value after it is loaded from the db and before it is passed back to the API functions such as the_field
		*
		*  @type	filter
		*  @since	3.6
		*  @date	23/01/13
		*
		*  @param	$value	- the value which was loaded from the database
		*  @param	$post_id - the $post_id from which the value was loaded
		*  @param	$field	- the field array holding all the field options
		*
		*  @return	$value	- the modified value
		*/
		
		function format_value_for_api( $value, $post_id, $field )
		{
			// defaults
			$field = array_merge($this->defaults, $field);
			
			// force value to array
            $multiple = isset($field['multiple']) ? $field['multiple'] : false;
            
            // return single
            if (!$multiple) {
                return $this->resolve_single_value($value, $post_id, $field);
            }
            
            // process multiple
            $value = $this->acf_get_array($value);
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
		
		function acf_get_array( $var = false, $delimiter = ',' ) {
	
			// is array?
			if( is_array($var) ) {
			
				return $var;
			
			}
			
			
			// bail early if empty
			if( empty($var) && !is_numeric($var) ) {
				
				return array();
				
			}
			
			
			// string 
			if( is_string($var) && $delimiter ) {
				
				return explode($delimiter, $var);
				
			}
			
			
			// place in array
			return array( $var );
			
		} 
		
		function acf_esc_attr( $atts ) {
	
			// is string?
			if( is_string($atts) ) {
				
				$atts = trim( $atts );
				return esc_attr( $atts );
				
			}
			
			
			// validate
			if( empty($atts) ) {
				
				return '';
				
			}
			
			
			// vars
			$e = array();
			
			
			// loop through and render
			foreach( $atts as $k => $v ) {
				
				// object
				if( is_array($v) || is_object($v) ) {
					
					$v = json_encode($v);
				
				// boolean	
				} elseif( is_bool($v) ) {
					
					$v = $v ? 1 : 0;
				
				// string
				} elseif( is_string($v) ) {
					
					$v = trim($v);
					
				}
				
				
				// append
				$e[] = $k . '="' . esc_attr( $v ) . '"';
			}
			
			
			// echo
			return implode(' ', $e);
			
		}
	
	}
	
	
	// initialize
	new acf_field_real_media_library( $this->settings );
}

?>