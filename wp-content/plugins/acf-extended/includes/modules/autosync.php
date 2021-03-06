<?php

if(!defined('ABSPATH'))
    exit;

/**
 * Auto Sync: Includes
 */
$acfe_php = acf_get_setting('acfe_php');
$acfe_php_load = acf_get_setting('acfe_php_load');

if(!empty($acfe_php) && !empty($acfe_php_load)){
    foreach($acfe_php_load as $path){
        if(!is_readable($path))
            continue;
        
        acf_update_setting('acfe_php_found', true);
        
        $files = glob($path . '/*.php');
        if(empty($files))
            continue;
        
        foreach($files as $file){
            require_once($file);
        }
        
    }
}

/**
 * Auto Sync: Disable json
 */
add_action('acf/update_field_group', 'acfe_autosync_json_update_field_group', 9);
function acfe_autosync_json_update_field_group($field_group){

    // Validate
    if(!acf_get_setting('json'))
        return;
    
    // Disable json sync for this field group
    if(!acfe_has_field_group_autosync($field_group, 'json'))
        add_filter('acf/settings/json', 'acfe_autosync_temp_disable_json');
        
}

/**
 * Auto Sync: Re-enable json
 */
add_action('acf/update_field_group', 'acfe_autosync_json_after_update_field_group', 11);
function acfe_autosync_json_after_update_field_group($field_group){
    
    // Validate
    if(!acf_get_setting('json'))
        return;
    
    remove_filter('acf/settings/json', 'acfe_autosync_temp_disable_json');
    
}

/**
 * Auto Sync: Disable json (function)
 */
function acfe_autosync_temp_disable_json(){
    return false;
}

/**
 * Auto Sync: PHP
 */
add_action('acf/update_field_group', 'acfe_autosync_php_update_field_group');
function acfe_autosync_php_update_field_group($field_group){
    
    // Validate
    if(!acf_get_setting('acfe_php'))
        return;
    
    if(!acfe_has_field_group_autosync($field_group, 'php'))
        return;
    
    $field_group['fields'] = acf_get_fields($field_group);
    acfe_autosync_write_php($field_group);
    
}

/**
 * Auto Sync: Write PHP
 */
function acfe_autosync_write_php($field_group){
    
    $path = acf_get_setting('acfe_php_save');
    if(empty($path))
        return false;
	
	// vars
    $path = untrailingslashit($path);
	$file = $field_group['key'] . '.php';
	
	// bail early if dir does not exist
	if(!is_writable($path)) 
        return false;
	
	// prepare for export
	$id = acf_extract_var($field_group, 'ID');
	$field_group = acf_prepare_field_group_for_export($field_group);
	
	// add modified time
	$field_group['modified'] = get_post_modified_time('U', true, $id, true);
    
    
    // Prepare
    $str_replace = array(
        "  "			=> "\t",
        "'!!__(!!\'"	=> "__('",
        "!!\', !!\'"	=> "', '",
        "!!\')!!'"		=> "')",
        "array ("		=> "array("
    );
    
    $preg_replace = array(
        '/([\t\r\n]+?)array/'	=> 'array',
        '/[0-9]+ => array/'		=> 'array'
    );
    
    ob_start();
    
        echo "<?php " . "\r\n" . "\r\n";
        echo "if( function_exists('acf_add_local_field_group') ):" . "\r\n" . "\r\n";
                        
        // code
        $code = var_export($field_group, true);
        
        
        // change double spaces to tabs
        $code = str_replace( array_keys($str_replace), array_values($str_replace), $code );
        
        
        // correctly formats "=> array("
        $code = preg_replace( array_keys($preg_replace), array_values($preg_replace), $code );
        
        
        // esc_textarea
        $code = $code;
        
        // echo
        echo "acf_add_local_field_group({$code});" . "\r\n" . "\r\n";
        
        echo "endif;";
    
    $output = ob_get_clean();
	
	// write file
	$f = fopen("{$path}/{$file}", 'w');
	fwrite($f, $output);
	fclose($f);
	
	// return
	return true;
	
}

/**
 * Auto Sync: Helper - is field group json desync
 */
function acfe_is_field_group_json_desync($field_group){
    
    acf_enable_filter('local');
    $group = acf_get_local_field_group($field_group['key']);
    acf_disable_filter('local');
    
    $private = acf_maybe_get($group, 'private', false);
    $local = acf_maybe_get($group, 'local', false);
    $modified = acf_maybe_get($group, 'modified', 0);
    
    if($private){
        return false;
    }
    
    elseif($local !== 'json'){
        return false;
    }
    
    elseif($modified && $modified > get_post_modified_time('U', true, $field_group['ID'], true)){
        return true;
    }
    
    return false;
    
}

/**
 * Auto Sync: Helper - Has field group autosync
 */
function acfe_has_field_group_autosync($field_group, $type = false){
    $acfe_autosync = acf_maybe_get($field_group, 'acfe_autosync', array());
    
    if(!$type)
        return acf_is_array($acfe_autosync);
    
    if($type == 'json')
        return is_array($acfe_autosync) && in_array('json', $acfe_autosync);
    
    elseif($type == 'php')
        return is_array($acfe_autosync) && in_array('php', $acfe_autosync);
        
    return false;
}

/**
 * Auto Sync: Helper - Has field group autosync found register/file
 */
function acfe_has_field_group_autosync_file($field_group, $type = 'json'){
    
    if($type == 'json'){
        
        // acf_is_local_field_group = true if json file found
        $found = false;
        if(acf_is_local_field_group($field_group['key'])){
            
            $local_field_group = acf_get_local_field_group($field_group['key']);
            $get_local = acf_maybe_get($local_field_group, 'local', false);
            
            if($get_local == 'json'){
                
                $found = true;
                
            }else{
                
                $paths = acf_get_setting('load_json');
            
                if(!empty($paths)){
                    foreach($paths as $path){
                        
                        $path = untrailingslashit($path);
                        $file = $field_group['key'] . '.json';
                        
                        if(is_readable("{$path}/{$file}")){
                            $found = true;
                            break;
                        }
                        
                    }
                }
                
            }
            
        }
        
        else{
            
            $paths = acf_get_setting('load_json');
            
            if(!empty($paths)){
                foreach($paths as $path){
                    
                    $path = untrailingslashit($path);
                    $file = $field_group['key'] . '.json';
                    
                    if(is_readable("{$path}/{$file}")){
                        $found = true;
                        break;
                    }
                    
                }
            }
            
        }
        
        return $found;
        
    }
    
    elseif($type == 'php'){
        
        // acf_is_local_field_group = true if php registered
        $found = false;
        if(acf_is_local_field_group($field_group['key'])){
            
            $local_field_group = acf_get_local_field_group($field_group['key']);
            $get_local = acf_maybe_get($local_field_group, 'local', false);
            if($get_local == 'php')
                $found = true;
            
        }
        
        return $found;
        
    }
        
    return false;
    
}