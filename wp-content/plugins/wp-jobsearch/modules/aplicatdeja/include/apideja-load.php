<?php
/**
 * Directory Plus ShortlistLoads Module
 */
// Direct access not allowed.
if (!defined('ABSPATH')) {
    exit;
}

if (!class_exists('Jobsearch_ApidejaLoad')) {

    class Jobsearch_ApidejaLoad {

        public $admin_notices;
 
        
    }

    global $jobsearch_apideja_load;
    $jobsearch_apideja_load = new Jobsearch_ApidejaLoad();
}