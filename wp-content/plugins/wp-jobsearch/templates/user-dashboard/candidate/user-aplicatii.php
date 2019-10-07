<?php
global $jobsearch_plugin_options, $Jobsearch_User_Dashboard_Settings;
$user_id  = get_current_user_id();
$user_obj = get_user_by( 'ID', $user_id );

$page_id  = $user_dashboard_page = isset( $jobsearch_plugin_options['user-dashboard-template-page'] ) ? $jobsearch_plugin_options['user-dashboard-template-page'] : '';
$page_id  = $user_dashboard_page = jobsearch__get_post_id( $user_dashboard_page, 'page' );
$page_url = jobsearch_wpml_lang_page_permalink( $page_id, 'page' ); //get_permalink($page_id);

$candidate_id = jobsearch_get_user_candidate_id( $user_id );

$reults_per_page = isset( $jobsearch_plugin_options['user-dashboard-per-page'] ) && $jobsearch_plugin_options['user-dashboard-per-page'] > 0 ? $jobsearch_plugin_options['user-dashboard-per-page'] : 10;

$page_num = isset( $_GET['page_num'] ) ? $_GET['page_num'] : 1;
global $wp;


?>
<div class="jobsearch-row  dashboardjobs" id="joburi" style="padding: 0">
    <div class="jobsearch-column-12">

<!--        // prin managero-->
        <div class="jobsearch-employer-box-section">
            <div class="fields-directe">
                <div class="visual-header">
                    <h3>Aplicatii prin managero</h3>
                    <a href="javascript:void(0);" class="edit-preview" data-field="fields-aplicatii-managero"
                       data-visual="tabel-aplicatii">editeaza sectiunea</a>
                </div>
                <div class="fields-aplicatii-managero" style="display: none">
                    <table id="tabel-fisiere">
                        <tr>
                            <th>Companie</th>
                            <th>Job</th>
                            <th>Aplicatie</th>
                            <th>Data aplicare</th>
                            <th>Note personale</th>
                            <th>Actiuni</th>
                        </tr>
						<?php
						if ( $candidate_id > 0 ) {
							$user_applied_jobs_list  = array();
							$user_applied_jobs_liste = get_user_meta( $user_id, 'jobsearch-user-jobs-applied-list', true );
							if ( ! empty( $user_applied_jobs_liste ) ) {
								foreach ( $user_applied_jobs_liste as $er_applied_jobs_list_key => $er_applied_jobs_list_val ) {
									$job_id = isset( $er_applied_jobs_list_val['post_id'] ) ? $er_applied_jobs_list_val['post_id'] : 0;
									if ( get_post_type( $job_id ) == 'job' ) {
										$user_applied_jobs_list[ $er_applied_jobs_list_key ] = $er_applied_jobs_list_val;
									}
								}
							}

							foreach ( $user_applied_jobs_list as $job_key => $job_val ) {

								$post_id     = $job_id = $job_val['post_id'];

                                $date_job = get_post_meta($job_id, 'job_data_set', true);


								$companie_id = get_post_meta( $job_id, 'jobsearch_field_job_posted_by', true );

                                $job           = $date_job['post_job'];
                                $companie      = $date_job['nume_companie'];

                                $post = get_post($candidate_id);
                                $user_name = $post->post_name;

								$url = home_url() . "/candidat/$user_name?job_id=$job_id&employer_id=$companie_id&action=preview_app";


                                $data          = get_the_time('d-m-Y', $post_id);

								$note          = get_post_meta($candidate_id,"jobsearch-user-job_note_$job_id", true);


								?>
                                <tr class="row-<?= $job_id; ?>">
                                    <td><?= $companie; ?></td>
                                    <td><a href="<?= get_the_permalink($post_id); ?>"><?= $job; ?></a></td>
                                    <td><a href="<?= $url; ?>" style="font-weight: normal;"><?= "vezi aplicatia"; ?></a></td>
                                    <td><?= $data; ?></td>
                                    <td style=" padding: 10px 15px;">
                                        <input type="text" data-job="<?= $job_id; ?>" value="<?= $note; ?>" style="width: 100%" class="notajob" />
                                    </td>
                                    <td>
                                        <a href="javascript:void(0);"
                                           class="jobsearch-savedjobs-links jobsearch-delete-applied-job"
                                           data-id="<?php echo( $job_id ) ?>"
                                           data-key="<?php echo( $job_key ) ?>"
                                           style="float:right; width: auto;"><i
                                                    class="jobsearch-icon jobsearch-rubbish"></i>
                                        </a>
                                    </td>
                                </tr>
							<?php }
						} ?>
                    </table>
                    <div class="acf-form-submit" style="float:left; width: 100%">
                        <input type="submit" class="acf-button button button-primary button-large" value="Actualizează" id="saveNota">			<span class="acf-spinner loadAplicatii" style="margin-top: 15px;"></span>
                    </div>
<script>
    var unsaved = false;
    jQuery('#saveNota').click(function () {
        var listaJobs = [];
        jQuery('.loadAplicatii').css('display','block');
       jQuery('.notajob').each(function () {
          var nota = jQuery(this).val();
          var job = jQuery(this).attr('data-job');
          var item = {
              'job_id' : job,
               'nota' : nota
          };
           listaJobs.push(item);
       });
            console.log(listaJobs);
        jQuery.ajax({
            type: "post",
            url:  "<?=  admin_url( 'admin-ajax.php' ); ?>",
            data: {
                'action': 'job_save_note',
                'note' : listaJobs,
                'candidate' : <?= $candidate_id; ?>
            },
            success:function(data) {
                unsaved = false;
                console.log(data);
                location.reload();
            },
            error: function(errorThrown){
                console.log(errorThrown);
            }
        });
    });
    jQuery('.notajob').change(function () {
       unsaved = true;
    });

    function unloadPage() {
        if (unsaved) {
            return "You have unsaved changes on this page.";
        }
    }
    window.onbeforeunload = unloadPage;

</script>
                </div>



                <div class="tabel-aplicatii">
                    <table id="tabel-fisiere">
                        <tr>
                            <th>Companie</th>
                            <th>Job</th>
                            <th>Aplicatie</th>
                            <th>Data aplicare</th>
                            <th>Note personale</th>
                        </tr>
						<?php
						if ( $candidate_id > 0 ) {
							$user_applied_jobs_list  = array();
							$user_applied_jobs_liste = get_user_meta( $user_id, 'jobsearch-user-jobs-applied-list', true );
							if ( ! empty( $user_applied_jobs_liste ) ) {
								foreach ( $user_applied_jobs_liste as $er_applied_jobs_list_key => $er_applied_jobs_list_val ) {
									$job_id = isset( $er_applied_jobs_list_val['post_id'] ) ? $er_applied_jobs_list_val['post_id'] : 0;
									if ( get_post_type( $job_id ) == 'job' ) {
										$user_applied_jobs_list[ $er_applied_jobs_list_key ] = $er_applied_jobs_list_val;
									}
								}
							}
							//	var_dump($user_applied_jobs_list);
							foreach ( $user_applied_jobs_list as $job_key => $job_val ) {

							    $job_id = $job_val['post_id'];

                                $date_job = get_post_meta($job_id, 'job_data_set', true);

								$post_id     = $job_id = $job_val['post_id'];

                                $autor = get_the_author($job_id);

								$companie_id = get_post_meta( $job_id, 'jobsearch_field_job_posted_by', true );

								$job           = $date_job['post_job'];
								$companie      = $date_job['nume_companie'];

								$post = get_post($candidate_id);
								$user_name = $post->post_name;

								$url = home_url() . "/candidat/$user_name?job_id=$job_id&employer_id=$companie_id&action=preview_app";

                                $data          = get_the_time('d-m-Y', $post_id);

								$note          = get_post_meta($candidate_id,"jobsearch-user-job_note_$job_id", true);

								?>

                                <tr>
                                    <td><?= $companie; ?></td>
                                    <td><a href="<?= get_the_permalink($post_id); ?>" target="_blank"><?= $job; ?></a></td>
                                    <td><a href="<?= $url; ?>" target="_blank"><?= 'vezi aplicatia'; ?></a></td>
                                    <td><?= $data; ?></td>
                                    <td><?= $note; ?></td>
                                </tr>

							<?php }
						} ?>
                    </table>
                </div>
            </div>
        </div>

        <!--        // prin email-->

        <div class="jobsearch-employer-box-section">
            <div class="fields-directe">
                <div class="visual-header">
                    <h3>Aplicatii prin email</h3>
                    <a href="javascript:void(0);" class="edit-preview" data-field="fields-aplicatii-managero"
                       data-visual="tabel-aplicatii">editeaza sectiunea</a>
                </div>
                <div class="fields-aplicatii-managero" style="display: none">
                    <table id="tabel-fisiere">
                        <tr>
                            <th>Companie</th>
                            <th>Job</th>
                            <th>Data aplicare</th>
                            <th>Note personale</th>
                            <th>Actiuni</th>
                        </tr>
                        <?php

                        if ( $candidate_id > 0 ) {


                            $candidate_apd_jobs_list  = array();
                            $candidate_apd_jobs_liste = get_post_meta( $candidate_id, 'jobsearch_apd_jobs_list', true );
                            $candidate_apd_jobs_liste = $candidate_apd_jobs_liste != '' ? explode( ',', $candidate_apd_jobs_liste ) : array();



                            foreach ( $candidate_apd_jobs_liste as $job_val ) {

                            //    nice_print_r($job_val);

                                $post_id = $job_id = $job_val;

                                $date_job = get_post_meta($job_val, 'job_data_set', true);

                                $companie_id = get_post_meta( $job_val, 'jobsearch_field_job_posted_by', true );

                                $job           = $date_job['post_job'];
                                $companie      = $date_job['nume_companie'];

                                $post = get_post($candidate_id);
                                $user_name = $post->post_name;

                                $url = home_url() . "/candidat/$user_name?job_id=$job_id&employer_id=$companie_id&action=preview_app";

                                $data          = get_the_time('d-m-Y', $job_val);

                                $note          = get_post_meta($candidate_id,"jobsearch-user-job_note_$job_id", true);
                                ?>

                                <tr class="row-<?= $job_id; ?>">
                                    <td><?= $companie; ?></td>
                                    <td><a href="<?= get_the_permalink($post_id); ?>"><?= $job; ?></a></td>
                                    <td><?= $data; ?></td>
                                    <td style=" padding: 10px 15px;">
                                        <input type="text" data-job="<?= $job_id; ?>" value="<?= $note; ?>" style="width: 100%" class="notajob" />
                                    </td>
                                    <td>
                                        <a href="javascript:void(0);"
                                           class="jobsearch-savedjobs-links jobsearch-delete-applied-job-2"
                                           data-id="<?php echo( $job_id ) ?>"
                                           data-key="<?php echo( $job_key ) ?>"
                                           style="float:right; width: auto;"><i
                                                    class="jobsearch-icon jobsearch-rubbish"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php }
                        } ?>
                    </table>
                    <div class="acf-form-submit" style="float:left; width: 100%">
                        <input type="submit" class="acf-button button button-primary button-large" value="Actualizează" id="saveNota">			<span class="acf-spinner loadAplicatii" style="margin-top: 15px;"></span>
                    </div>
                    <script>
                        var unsaved = false;
                        jQuery('#saveNota').click(function () {
                            var listaJobs = [];
                            jQuery('.loadAplicatii').css('display','block');
                            jQuery('.notajob').each(function () {
                                var nota = jQuery(this).val();
                                var job = jQuery(this).attr('data-job');
                                var item = {
                                    'job_id' : job,
                                    'nota' : nota
                                };
                                listaJobs.push(item);
                            });
                            console.log(listaJobs);
                            jQuery.ajax({
                                type: "post",
                                url:  "<?=  admin_url( 'admin-ajax.php' ); ?>",
                                data: {
                                    'action': 'job_save_note',
                                    'note' : listaJobs,
                                    'candidate' : <?= $candidate_id; ?>
                                },
                                success:function(data) {
                                    unsaved = false;
                                    console.log(data);
                                    location.reload();
                                },
                                error: function(errorThrown){
                                    console.log(errorThrown);
                                }
                            });
                        });
                        jQuery('.notajob').change(function () {
                            unsaved = true;
                        });

                        function unloadPage() {
                            if (unsaved) {
                                return "You have unsaved changes on this page.";
                            }
                        }
                        window.onbeforeunload = unloadPage;

                    </script>
                </div>

                <div class="tabel-aplicatii">
                    <table id="tabel-fisiere">
                        <tr>
                            <th>Companie set </th>
                            <th>Job</th>
                            <th>Data aplicare</th>
                            <th>Note personale</th>
                        </tr>
                        <?php
                        if ( $candidate_id > 0 ) {
                            $candidate_apd_jobs_list  = array();
                            $candidate_apd_jobs_liste = get_post_meta( $candidate_id, 'jobsearch_apd_jobs_list', true );
                            $candidate_apd_jobs_liste = $candidate_apd_jobs_liste != '' ? explode( ',', $candidate_apd_jobs_liste ) : array();

                            foreach ( $candidate_apd_jobs_liste as $job_val ) {
                                ;

                                $date_job = get_post_meta($job_val, 'job_data_set', true);

                             //   nice_print_r($date_job);

                                $companie_id = get_post_meta( $job_id, 'jobsearch_field_job_posted_by', true );

                                $job           = $date_job['post_job'];
                                $companie      = $date_job['nume_companie'];

                                $post = get_post($candidate_id);
                                $user_name = $post->post_name;

                                $url = home_url() . "/candidat/$user_name?job_id=$job_id&employer_id=$companie_id&action=preview_app";

                                $data          = get_the_time('d-m-Y', $post_id);
                                $note          = get_post_meta($candidate_id,"jobsearch-user-job_note_$job_id", true);

                                ?>

                                <tr>
                                    <td><?= $companie; ?></td>
                                    <td><a href="<?= get_the_permalink($post_id); ?>"><?= $job; ?></a></td>
                                    <td><a href="<?= $url; ?>"><?= "vezi aplicatia"; ?></a></td>
                                    <td><?= $data; ?></td>
                                    <td><?= $note; ?></td>
                                </tr>

                            <?php }
                        } ?>
                    </table>
                </div>
            </div>
        </div>



        <div class="jobsearch-employer-box-section">
            <div class="fields-directe">
                <div class="visual-header">
                    <h3>Alte aplicatii</h3>
                    <a href="javascript:void(0);" class="edit-preview" data-field="fields-directe2"
                       data-visual="tabel-directe">editeaza sectiunea</a>
                </div>
                <div class="fields-directe2" style="display: none">
					<?php
					//   acf_enqueue_uploader();
					//  acf_form_head();
					$settings2 = array(

						/* (string) Unique identifier for the form. Defaults to 'acf-form' */
						'id'                    => 'acf-form',

						/* (int|string) The post ID to load data from and save data to. Defaults to the current post ID.
						Can also be set to 'new_post' to create a new post on submit */
						'post_id'               => $candidate_id,

						/* (array) An array of post data used to create a post. See wp_insert_post for available parameters.
						The above 'post_id' setting must contain a value of 'new_post' */
						'new_post'              => true,

						/* (array) An array of field group IDs/keys to override the fields displayed in this form */
						'field_groups'          => [ 830 ],

						/* (array) An array of field IDs/keys to override the fields displayed in this form */
						'fields'                => false,

						/* (boolean) Whether or not to show the post title text field. Defaults to false */
						'post_title'            => false,

						/* (boolean) Whether or not to show the post content editor field. Defaults to false */
						'post_content'          => false,

						/* (boolean) Whether or not to create a form element. Useful when a adding to an existing form. Defaults to true */
						'form'                  => true,

						/* (array) An array or HTML attributes for the form element */
						'form_attributes'       => array(),

						/* (string) The URL to be redirected to after the form is submit. Defaults to the current URL with a GET parameter '?updated=true'.
						A special placeholder '%post_url%' will be converted to post's permalink (handy if creating a new post)
						A special placeholder '%post_id%' will be converted to post's ID (handy if creating a new post) */
						'return'                => '',

						/* (string) Extra HTML to add before the fields */
						'html_before_fields'    => '',

						/* (string) Extra HTML to add after the fields */
						'html_after_fields'     => '',

						/* (string) The text displayed on the submit button */
						'submit_value'          => __( "Update", 'acf' ),

						/* (string) A message displayed above the form after being redirected. Can also be set to false for no message */
						'updated_message'       => __( "Post updated", 'acf' ),

						/* (string) Determines where field labels are places in relation to fields. Defaults to 'top'.
						Choices of 'top' (Above fields) or 'left' (Beside fields) */
						'label_placement'       => 'top',

						/* (string) Determines where field instructions are places in relation to fields. Defaults to 'label'.
						Choices of 'label' (Below labels) or 'field' (Below fields) */
						'instruction_placement' => 'label',

						/* (string) Determines element used to wrap a field. Defaults to 'div'
						Choices of 'div', 'tr', 'td', 'ul', 'ol', 'dl' */
						'field_el'              => 'div',

						/* (string) Whether to use the WP uploader or a basic input for image and file fields. Defaults to 'wp'
						Choices of 'wp' or 'basic'. Added in v5.2.4 */
						'uploader'              => 'wp',

						/* (boolean) Whether to include a hidden input field to capture non human form submission. Defaults to true. Added in v5.3.4 */
						'honeypot'              => true,

						/* (string) HTML used to render the updated message. Added in v5.5.10 */
						'html_updated_message'  => '<div id="message" class="updated"><p>%s</p></div>',

						/* (string) HTML used to render the submit button. Added in v5.5.10 */
						'html_submit_button'    => '<input type="submit" class="acf-button button button-primary button-large" value="%s" />',

						/* (string) HTML used to render the submit button loading spinner. Added in v5.5.10 */
						'html_submit_spinner'   => '<span class="acf-spinner"></span>',

						/* (boolean) Whether or not to sanitize all $_POST data with the wp_kses_post() function. Defaults to true. Added in v5.6.5 */
						'kses'                  => true

					);
					// acf_form($settings1);
					acf_form( $settings2 );
					?>
                </div>
                <div class="tabel-directe">
                    <table id="tabel-fisiere">
                        <tr>
                            <th>Companie</th>
                            <th>Job</th>
                            <th>Data aplicare</th>
                            <th>Note personale</th>
                        </tr>
						<?php
						$aplicatii = get_field( 'alte_aplicatii', $candidate_id );
						foreach ( $aplicatii as $aplicatie ):
							?>
                            <tr>
                                <td><?= $aplicatie['companie']; ?></td>
                                <td><?= $aplicatie['job']; ?></td>
                                <td><?= $aplicatie['data']; ?></td>
                                <td><?= $aplicatie['note']; ?></td>
                            </tr>
						<?php endforeach; ?>
                    </table>
                </div>
            </div>
        </div>

    </div>
</div>


<script>

    jQuery('.edit-preview').toggle(function () {
        var fields = jQuery(this).attr('data-field');
        var visual = jQuery(this).attr('data-visual');
        jQuery('.' + fields).show();
        jQuery('.' + visual).hide();
        jQuery(this).html('mod visual');
    }, function () {
        var fields = jQuery(this).attr('data-field');
        var visual = jQuery(this).attr('data-visual');
        jQuery('.' + fields).hide();
        jQuery('.' + visual).show();
        jQuery(this).html('editeaza sectiunea');
    });


</script>