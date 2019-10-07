<?php
/**
 * Directory Plus ShortlistLoads Module
 */
// Direct access not allowed.
if (!defined('ABSPATH')) {
    exit;
}

if (!class_exists('Jobsearch_RejectLoad')) {

    class Jobsearch_RejectLoad {

        public $admin_notices;
 
        
    }

    global $jobsearch_reject_load;
    $jobsearch_reject_load = new Jobsearch_RejectLoad();
}