<?php
acf_form_head();

global $jobsearch_plugin_options, $Jobsearch_User_Dashboard_Settings;
$user_id = get_current_user_id();
$user_obj = get_user_by('ID', $user_id);

$page_id = $user_dashboard_page = isset($jobsearch_plugin_options['user-dashboard-template-page']) ? $jobsearch_plugin_options['user-dashboard-template-page'] : '';
$page_id = $user_dashboard_page = jobsearch__get_post_id($user_dashboard_page, 'page');
$page_url = jobsearch_wpml_lang_page_permalink($page_id, 'page'); //get_permalink($page_id);

$candidate_id = jobsearch_get_user_candidate_id($user_id);

$reults_per_page = isset($jobsearch_plugin_options['user-dashboard-per-page']) && $jobsearch_plugin_options['user-dashboard-per-page'] > 0 ? $jobsearch_plugin_options['user-dashboard-per-page'] : 10;

$page_num = isset($_GET['page_num']) ? $_GET['page_num'] : 1;

if ($candidate_id > 0) {
    $candidate_cv_file = get_post_meta($candidate_id, 'candidate_cv_file', true);
    $multiple_cv_files_allow = isset($jobsearch_plugin_options['multiple_cv_uploads']) ? $jobsearch_plugin_options['multiple_cv_uploads'] : '';
    $max_cvs_allow = isset($jobsearch_plugin_options['max_cvs_allow']) && absint($jobsearch_plugin_options['max_cvs_allow']) > 0 ? absint($jobsearch_plugin_options['max_cvs_allow']) : 5;
    
    $ca_at_cv_files = get_post_meta($candidate_id, 'candidate_cv_files', true);
    
    $cand_files_types = isset($jobsearch_plugin_options['cand_cv_types']) ? $jobsearch_plugin_options['cand_cv_types'] : '';
    if (empty($cand_files_types)) {
        $cand_files_types = array(
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/pdf',
        );
    }
    $cand_files_types_json = json_encode($cand_files_types);
    $sutable_files_arr = array();
    $file_typs_comarr = array(
        'text/plain' => __('text', 'wp-jobsearch'),
        'image/jpeg' => __('jpeg', 'wp-jobsearch'),
        'image/png' => __('png', 'wp-jobsearch'),
        'application/msword' => __('doc', 'wp-jobsearch'),
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => __('docx', 'wp-jobsearch'),
        'application/vnd.ms-excel' => __('xls', 'wp-jobsearch'),
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => __('xlsx', 'wp-jobsearch'),
        'application/pdf' => __('pdf', 'wp-jobsearch'),
    );
    foreach ($file_typs_comarr as $file_typ_key => $file_typ_comar) {
        if (in_array($file_typ_key, $cand_files_types)) {
            $sutable_files_arr[] = '.' . $file_typ_comar;
        }
    }
    $sutable_files_str = implode(', ', $sutable_files_arr);
    ?>
    <div class="jobsearch-employer-box-section">

	    <?php


	    $settings2 = array(

		    /* (string) Unique identifier for the form. Defaults to 'acf-form' */
		    'id' => 'acf-form',

		    /* (int|string) The post ID to load data from and save data to. Defaults to the current post ID.
			Can also be set to 'new_post' to create a new post on submit */
		    'post_id' => $candidate_id,

		    /* (array) An array of post data used to create a post. See wp_insert_post for available parameters.
			The above 'post_id' setting must contain a value of 'new_post' */
		    'new_post' => false,

		    /* (array) An array of field group IDs/keys to override the fields displayed in this form */
		    'field_groups' => [602],

		    /* (array) An array of field IDs/keys to override the fields displayed in this form */
		    'fields' => false,

		    /* (boolean) Whether or not to show the post title text field. Defaults to false */
		    'post_title' => false,

		    /* (boolean) Whether or not to show the post content editor field. Defaults to false */
		    'post_content' => false,

		    /* (boolean) Whether or not to create a form element. Useful when a adding to an existing form. Defaults to true */
		    'form' => true,

		    /* (array) An array or HTML attributes for the form element */
		    'form_attributes' => array(),

		    /* (string) The URL to be redirected to after the form is submit. Defaults to the current URL with a GET parameter '?updated=true'.
			A special placeholder '%post_url%' will be converted to post's permalink (handy if creating a new post)
			A special placeholder '%post_id%' will be converted to post's ID (handy if creating a new post) */
		    'return' => '',

		    /* (string) Extra HTML to add before the fields */
		    'html_before_fields' => '',

		    /* (string) Extra HTML to add after the fields */
		    'html_after_fields' => '',

		    /* (string) The text displayed on the submit button */
		    'submit_value' => __("Update", 'acf'),

		    /* (string) A message displayed above the form after being redirected. Can also be set to false for no message */
		    'updated_message' => __("Post updated", 'acf'),

		    /* (string) Determines where field labels are places in relation to fields. Defaults to 'top'.
			Choices of 'top' (Above fields) or 'left' (Beside fields) */
		    'label_placement' => 'top',

		    /* (string) Determines where field instructions are places in relation to fields. Defaults to 'label'.
			Choices of 'label' (Below labels) or 'field' (Below fields) */
		    'instruction_placement' => 'label',

		    /* (string) Determines element used to wrap a field. Defaults to 'div'
			Choices of 'div', 'tr', 'td', 'ul', 'ol', 'dl' */
		    'field_el' => 'div',

		    /* (string) Whether to use the WP uploader or a basic input for image and file fields. Defaults to 'wp'
			Choices of 'wp' or 'basic'. Added in v5.2.4 */
		    'uploader' => 'wp',

		    /* (boolean) Whether to include a hidden input field to capture non human form submission. Defaults to true. Added in v5.3.4 */
		    'honeypot' => true,

		    /* (string) HTML used to render the updated message. Added in v5.5.10 */
		    'html_updated_message'	=> '<div id="message" class="updated"><p>%s</p></div>',

		    /* (string) HTML used to render the submit button. Added in v5.5.10 */
		    'html_submit_button'	=> '<input type="submit" class="acf-button button button-primary button-large" value="%s" />',

		    /* (string) HTML used to render the submit button loading spinner. Added in v5.5.10 */
		    'html_submit_spinner'	=> '<span class="acf-spinner"></span>',

		    /* (boolean) Whether or not to sanitize all $_POST data with the wp_kses_post() function. Defaults to true. Added in v5.6.5 */
		    'kses'	=> true

	    );

	    $settings1 = array(

		    /* (string) Unique identifier for the form. Defaults to 'acf-form' */
		    'id' => 'acf-form',

		    /* (int|string) The post ID to load data from and save data to. Defaults to the current post ID.
			Can also be set to 'new_post' to create a new post on submit */
		    'post_id' => $candidate_id,

		    /* (array) An array of post data used to create a post. See wp_insert_post for available parameters.
			The above 'post_id' setting must contain a value of 'new_post' */
		    'new_post' => false,

		    /* (array) An array of field group IDs/keys to override the fields displayed in this form */
		    'field_groups' => [605],

		    /* (array) An array of field IDs/keys to override the fields displayed in this form */
		    'fields' => false,

		    /* (boolean) Whether or not to show the post title text field. Defaults to false */
		    'post_title' => false,

		    /* (boolean) Whether or not to show the post content editor field. Defaults to false */
		    'post_content' => false,

		    /* (boolean) Whether or not to create a form element. Useful when a adding to an existing form. Defaults to true */
		    'form' => true,

		    /* (array) An array or HTML attributes for the form element */
		    'form_attributes' => array(),

		    /* (string) The URL to be redirected to after the form is submit. Defaults to the current URL with a GET parameter '?updated=true'.
			A special placeholder '%post_url%' will be converted to post's permalink (handy if creating a new post)
			A special placeholder '%post_id%' will be converted to post's ID (handy if creating a new post) */
		    'return' => '',

		    /* (string) Extra HTML to add before the fields */
		    'html_before_fields' => '',

		    /* (string) Extra HTML to add after the fields */
		    'html_after_fields' => '',

		    /* (string) The text displayed on the submit button */
		    'submit_value' => __("Update", 'acf'),

		    /* (string) A message displayed above the form after being redirected. Can also be set to false for no message */
		    'updated_message' => __("Post updated", 'acf'),

		    /* (string) Determines where field labels are places in relation to fields. Defaults to 'top'.
			Choices of 'top' (Above fields) or 'left' (Beside fields) */
		    'label_placement' => 'top',

		    /* (string) Determines where field instructions are places in relation to fields. Defaults to 'label'.
			Choices of 'label' (Below labels) or 'field' (Below fields) */
		    'instruction_placement' => 'label',

		    /* (string) Determines element used to wrap a field. Defaults to 'div'
			Choices of 'div', 'tr', 'td', 'ul', 'ol', 'dl' */
		    'field_el' => 'div',

		    /* (string) Whether to use the WP uploader or a basic input for image and file fields. Defaults to 'wp'
			Choices of 'wp' or 'basic'. Added in v5.2.4 */
		    'uploader' => 'wp',

		    /* (boolean) Whether to include a hidden input field to capture non human form submission. Defaults to true. Added in v5.3.4 */
		    'honeypot' => true,

		    /* (string) HTML used to render the updated message. Added in v5.5.10 */
		    'html_updated_message'	=> '<div id="message" class="updated"><p>%s</p></div>',

		    /* (string) HTML used to render the submit button. Added in v5.5.10 */
		    'html_submit_button'	=> '<input type="submit" class="acf-button button button-primary button-large" value="%s" />',

		    /* (string) HTML used to render the submit button loading spinner. Added in v5.5.10 */
		    'html_submit_spinner'	=> '<span class="acf-spinner"></span>',

		    /* (boolean) Whether or not to sanitize all $_POST data with the wp_kses_post() function. Defaults to true. Added in v5.6.5 */
		    'kses'	=> true

	    );

	    $settings3 = array(

		    /* (string) Unique identifier for the form. Defaults to 'acf-form' */
		    'id' => 'acf-form',

		    /* (int|string) The post ID to load data from and save data to. Defaults to the current post ID.
			Can also be set to 'new_post' to create a new post on submit */
		    'post_id' => $candidate_id,

		    /* (array) An array of post data used to create a post. See wp_insert_post for available parameters.
			The above 'post_id' setting must contain a value of 'new_post' */
		    'new_post' => false,

		    /* (array) An array of field group IDs/keys to override the fields displayed in this form */
		    'field_groups' => [611],

		    /* (array) An array of field IDs/keys to override the fields displayed in this form */
		    'fields' => false,

		    /* (boolean) Whether or not to show the post title text field. Defaults to false */
		    'post_title' => false,

		    /* (boolean) Whether or not to show the post content editor field. Defaults to false */
		    'post_content' => false,

		    /* (boolean) Whether or not to create a form element. Useful when a adding to an existing form. Defaults to true */
		    'form' => true,

		    /* (array) An array or HTML attributes for the form element */
		    'form_attributes' => array(),

		    /* (string) The URL to be redirected to after the form is submit. Defaults to the current URL with a GET parameter '?updated=true'.
			A special placeholder '%post_url%' will be converted to post's permalink (handy if creating a new post)
			A special placeholder '%post_id%' will be converted to post's ID (handy if creating a new post) */
		    'return' => '',

		    /* (string) Extra HTML to add before the fields */
		    'html_before_fields' => '',

		    /* (string) Extra HTML to add after the fields */
		    'html_after_fields' => '',

		    /* (string) The text displayed on the submit button */
		    'submit_value' => __("Update", 'acf'),

		    /* (string) A message displayed above the form after being redirected. Can also be set to false for no message */
		    'updated_message' => __("Post updated", 'acf'),

		    /* (string) Determines where field labels are places in relation to fields. Defaults to 'top'.
			Choices of 'top' (Above fields) or 'left' (Beside fields) */
		    'label_placement' => 'top',

		    /* (string) Determines where field instructions are places in relation to fields. Defaults to 'label'.
			Choices of 'label' (Below labels) or 'field' (Below fields) */
		    'instruction_placement' => 'label',

		    /* (string) Determines element used to wrap a field. Defaults to 'div'
			Choices of 'div', 'tr', 'td', 'ul', 'ol', 'dl' */
		    'field_el' => 'div',

		    /* (string) Whether to use the WP uploader or a basic input for image and file fields. Defaults to 'wp'
			Choices of 'wp' or 'basic'. Added in v5.2.4 */
		    'uploader' => 'wp',

		    /* (boolean) Whether to include a hidden input field to capture non human form submission. Defaults to true. Added in v5.3.4 */
		    'honeypot' => true,

		    /* (string) HTML used to render the updated message. Added in v5.5.10 */
		    'html_updated_message'	=> '<div id="message" class="updated"><p>%s</p></div>',

		    /* (string) HTML used to render the submit button. Added in v5.5.10 */
		    'html_submit_button'	=> '<input type="submit" class="acf-button button button-primary button-large" value="%s" />',

		    /* (string) HTML used to render the submit button loading spinner. Added in v5.5.10 */
		    'html_submit_spinner'	=> '<span class="acf-spinner"></span>',

		    /* (boolean) Whether or not to sanitize all $_POST data with the wp_kses_post() function. Defaults to true. Added in v5.6.5 */
		    'kses'	=> true

	    );




	    acf_form($settings1);

	    acf_form($settings2);

	    acf_form($settings3);

	    ?>

        <div class="jobsearch-profile-title">
            <h2><?php esc_html_e('CV Manager', 'wp-jobsearch') ?></h2>
        </div>
        <?php
        if ($multiple_cv_files_allow == 'on') {
            ?>
            <div id="com-file-holder">
                <?php
                if (!empty($ca_at_cv_files)) {
                    $cv_files_count = count($ca_at_cv_files);
                    foreach ($ca_at_cv_files as $cv_file_key => $cv_file_val) {
                        $file_attach_id = isset($cv_file_val['file_id']) ? $cv_file_val['file_id'] : '';
                        $file_url = isset($cv_file_val['file_url']) ? $cv_file_val['file_url'] : '';
                        $cv_primary = isset($cv_file_val['primary']) ? $cv_file_val['primary'] : '';

                        $cv_file_title = get_the_title($file_attach_id);
                        $attach_post = get_post($file_attach_id);

                        $attach_date = isset($attach_post->post_date) ? $attach_post->post_date : '';
                        $attach_mime = isset($attach_post->post_mime_type) ? $attach_post->post_mime_type : '';

                        if ($attach_mime == 'application/pdf') {
                            $attach_icon = 'fa fa-file-pdf-o';
                        } else if ($attach_mime == 'application/msword' || $attach_mime == 'application/vnd.openxmlformats-officedocument.wordprocessingml.document') {
                            $attach_icon = 'fa fa-file-word-o';
                        } else {
                            $attach_icon = 'fa fa-file-word-o';
                        }

                        if ($file_attach_id > 0) {
                            ?>
                            <div class="jobsearch-cv-manager-list">
                                <ul class="jobsearch-row">
                                    <li class="jobsearch-column-12">
                                        <div class="jobsearch-cv-manager-wrap">
                                            <a class="jobsearch-cv-manager-thumb"><i class="<?php echo ($attach_icon) ?>"></i></a>
                                            <div class="jobsearch-cv-manager-text">
                                                <div class="jobsearch-cv-manager-left">
                                                    <h2><a href="<?php echo ($file_url) ?>" download="<?php echo ($cv_file_title) ?>"><?php echo (strlen($cv_file_title) > 40 ? substr($cv_file_title, 0, 40) . '...' : $cv_file_title) ?></a></h2>
                                                    <?php
                                                    if ($attach_date != '') {
                                                        ?>
                                                        <ul>
                                                            <li><i class="fa fa-calendar"></i> <?php echo date_i18n(get_option('date_format'), strtotime($attach_date)) . ' ' . date_i18n(get_option('time_format'), strtotime($attach_date)) ?></li>
                                                        </ul>
                                                        <?php
                                                    }
                                                    ?>
                                                </div>
                                                <a href="javascript:void(0);" class="jobsearch-cv-manager-link jobsearch-del-user-cv" data-id="<?php echo ($file_attach_id) ?>"><i class="jobsearch-icon jobsearch-rubbish"></i></a>
                                                <a href="<?php echo ($file_url) ?>" class="jobsearch-cv-manager-link jobsearch-cv-manager-download" download="<?php echo ($cv_file_title) ?>"><i class="jobsearch-icon jobsearch-download-arrow"></i></a>
                                            </div>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                            <?php
                        }
                    }
                }
                ?>
            </div>
            <?php
            if (isset($cv_files_count) && $cv_files_count >= $max_cvs_allow) {
                ?>
                <div id="jobsearch-upload-cv-reached" class="jobsearch-upload-cv">
                    <p><?php esc_html_e('You have uploaded maximum CV files. Remove one of your CV files to upload new file.', 'wp-jobsearch') ?></p>
                </div>
                <?php
            } else {
                ?>
                <div id="jobsearch-upload-cv-main" class="jobsearch-upload-cv">
                    <small><?php esc_html_e('Curriculum Vitae', 'wp-jobsearch') ?></small>
                    <input class="jobsearch-disabled-input" id="jobsearch-uploadfile" placeholder="<?php esc_html_e('Sample_CV.pdf', 'wp-jobsearch') ?>" disabled="disabled">
                    <div class="jobsearch-cvupload-file">
                        <span><i class="jobsearch-icon jobsearch-arrows-2"></i> <?php esc_html_e('Upload CV', 'wp-jobsearch') ?></span>
                        <input id="jobsearch-uploadbtn" type="file" name="candidate_cv_file" class="jobsearch-upload-btn">
                        <div class="fileUpLoader"></div>
                    </div>
                    <p><?php printf(esc_html__('Suitable files are %s.', 'wp-jobsearch'), $sutable_files_str) ?></p>
                </div>
                <?php
            }
        } else {
            ?>
            <div id="jobsearch-upload-cv-main" class="jobsearch-upload-cv" style="display: <?php echo (empty($candidate_cv_file) ? 'block' : 'none') ?>;">
                 <small><?php esc_html_e('Curriculum Vitae', 'wp-jobsearch') ?></small>
                 <input class="jobsearch-disabled-input" id="jobsearch-uploadfile" placeholder="<?php esc_html_e('Sample_CV.pdf', 'wp-jobsearch') ?>" disabled="disabled">
                 <div class="jobsearch-cvupload-file">
                    <span><i class="jobsearch-icon jobsearch-arrows-2"></i> <?php esc_html_e('Upload CV', 'wp-jobsearch') ?></span>
                    <input id="jobsearch-uploadbtn" type="file" name="candidate_cv_file" class="jobsearch-upload-btn">
                    <div class="fileUpLoader"></div>
                </div>
                <p><?php printf(esc_html__('Suitable files are %s.', 'wp-jobsearch'), $sutable_files_str) ?></p>
            </div>
            <div id="com-file-holder">
                <?php
                if (!empty($candidate_cv_file)) {
                    $file_attach_id = isset($candidate_cv_file['file_id']) ? $candidate_cv_file['file_id'] : '';
                    $file_url = isset($candidate_cv_file['file_url']) ? $candidate_cv_file['file_url'] : '';

                    $cv_file_title = get_the_title($file_attach_id);
                    $attach_post = get_post($file_attach_id);

                    $attach_date = isset($attach_post->post_date) ? $attach_post->post_date : '';
                    $attach_mime = isset($attach_post->post_mime_type) ? $attach_post->post_mime_type : '';

                    if ($attach_mime == 'application/pdf') {
                        $attach_icon = 'fa fa-file-pdf-o';
                    } else if ($attach_mime == 'application/msword' || $attach_mime == 'application/vnd.openxmlformats-officedocument.wordprocessingml.document') {
                        $attach_icon = 'fa fa-file-word-o';
                    } else {
                        $attach_icon = 'fa fa-file-word-o';
                    }

                    if ($file_attach_id > 0) {
                        ?>
                        <div class="jobsearch-cv-manager-list">
                            <ul class="jobsearch-row">
                                <li class="jobsearch-column-12">
                                    <div class="jobsearch-cv-manager-wrap">
                                        <a class="jobsearch-cv-manager-thumb"><i class="<?php echo ($attach_icon) ?>"></i></a>
                                        <div class="jobsearch-cv-manager-text">
                                            <div class="jobsearch-cv-manager-left">
                                                <h2><a href="<?php echo ($file_url) ?>" download="<?php echo ($cv_file_title) ?>"><?php echo ($cv_file_title) ?></a></h2>
                                                <?php
                                                if ($attach_date != '') {
                                                    ?>
                                                    <ul>
                                                        <li><i class="fa fa-calendar"></i> <?php echo date_i18n(get_option('date_format'), strtotime($attach_date)) . ' ' . date_i18n(get_option('time_format'), strtotime($attach_date)) ?></li>
                                                    </ul>
                                                    <?php
                                                }
                                                ?>
                                            </div>
                                            <a href="javascript:void(0);" class="jobsearch-cv-manager-link jobsearch-del-user-cv" data-id="<?php echo ($file_attach_id) ?>"><i class="jobsearch-icon jobsearch-rubbish"></i></a>
                                            <a href="<?php echo ($file_url) ?>" class="jobsearch-cv-manager-link jobsearch-cv-manager-download" download="<?php echo ($cv_file_title) ?>"><i class="jobsearch-icon jobsearch-download-arrow"></i></a>
                                        </div>
                                    </div>
                                </li>
                            </ul>
                        </div>
                        <?php
                    }
                }
                ?>
            </div>
            <?php
        }
        echo apply_filters('jobsearch_dashboard_after_cv_upload_files', '');
        ?>
    </div>
    <?php
}    