<script src="/wp-content/plugins/acf-extended/assets/acf-extended.js"></script>
<link href="/wp-content/plugins/acf-extended/assets/acf-extended.css" >

<?php
acf_enqueue_uploader();
acf_form_head();


global $jobsearch_plugin_options, $Jobsearch_User_Dashboard_Settings;
$user_id      = get_current_user_id();
$user_obj     = get_user_by( 'ID', $user_id );
$candidate_id = jobsearch_get_user_candidate_id( $user_id );
//var_dump($user_obj);

if ( $candidate_id > 0 ) {

	?>
    <div class="jobEdit">
        <div class="jobsearch-employer-box-sections">
<!--            <div class="numePreview">-->
<!--                <span class="titlu"></span>-->
<!--                <span class="prenume"></span>-->
<!--                <span class="particula"></span>-->
<!--                <span class="nume"></span>-->
<!--                <span class="formula"></span>-->
<!--                <span class="nickname"></span>-->
<!--            </div>-->

			<?php

			$settings1 = array(

				/* (string) Unique identifier for the form. Defaults to 'acf-form' */
				'id'                    => 'acf-form-candidate-profile',

				/* (int|string) The post ID to load data from and save data to. Defaults to the current post ID.
				Can also be set to 'new_post' to create a new post on submit */
				'post_id'               => $candidate_id,

				/* (array) An array of post data used to create a post. See wp_insert_post for available parameters.
				The above 'post_id' setting must contain a value of 'new_post' */
				'new_post'              => false,

				/* (array) An array of field group IDs/keys to override the fields displayed in this form */
				'field_groups'          => [ 725 ],

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

			acf_form( $settings1 );

			?>
        </div>
    </div>
    <div class="jobPreview" style="display: none;">
		<?php

		// date personale

		$date_personale   = get_field( 'date_personale', $candidate_id );
		$nume             = $date_personale['nume'];
		$prenume          = $date_personale['prenume'];
		$cuvleg           = $date_personale['particula'];
		$titlu_academic   = $date_personale['titlu'];
		$formula_adresare = $date_personale['formula_adresare'];

		$nume_complet = "$titlu_academic $prenume $cuvleg $nume ($formula_adresare)";
		$nickname     = $date_personale['nickaname'];

		$anul_nasterii = $date_personale['anul_nasterii'];

		$varsta    = date( 'Y' ) - $anul_nasterii;
		$sex       = $date_personale['sex'];
		$resedinta = $date_personale['judet_candidat']['label'];


		// educatie

		$educatie   = get_field( 'educatie', $candidate_id ); //array
		$calificari = get_field( 'calificari', $candidate_id ); //text

		$engleza    = get_field( 'limba_engleza', $candidate_id ); //
		$alte_limbi = get_field( 'alte_limbi', $candidate_id );

		$relevant_skils = get_field( 'relevant_skils', $candidate_id );
		$linkuri        = get_field( 'link-uri', $candidate_id ); //array

		$salariu = get_field( 'salariu_minim_accepta', $candidate_id );

		$experienta = get_field( 'experienta', $candidate_id );
		// companie // post (post, de_la, pana_la, ierarhie, descriere_job, alte_detalii)

		$comentarii_libere = get_field( 'note_comentarii', $candidate_id );

		$alte_cerinte = get_field( 'alte_cerinte', $candidate_id );

		$imagine_profil = get_field( 'imagine_profil', $candidate_id );


		// date personale


		?>

        <aside class="jobsearch-column-3 jobsearch-typo-wrap">
            <div class="widget widget_candidate_info">
                <div class="jobsearch_candidate_info">
                    <div class="wrapAvatar">
                        <img class="field-imagineProfil" src=""/>
                    </div>
                    <div class="datePersonale">
                        <p>Varsta:
                            <target class="field-varsta"></target>
                            ani
                        </p>
                        <p>Sex:
                            <target class="field-sex"></target>
                        </p>
                        <p>Nickname:
                            <target class="field-nickname"></target>
                        </p>
                        <p>Resedinta:
                            <target class="field-resedinta"></target>
                        </p>
                        <p>Link-uri:
                            <target class="field-linkuri"></target>
                        </p>

                    </div>

                </div>
            </div>


        </aside>
        <div id="profil-candidat" class="jobsearch-column-7 jobsearch-typo-wrap">
            <div class="container-wrapper">
                <h1 class="numecandidat">
                    <target class="field-numecomplet"></target>
                </h1>

                <div class="zonaCandidat" id="scrisoare-intentie">
					<?php if ( $job_id = $_GET['job_id'] ):
						$get_job_text_attached = get_post_meta( $job_id, 'jobsearch_job_text_attached', true );
						echo $get_job_text_attached[ $candidate_id ];
					endif; ?>
                </div>

                <div class="zonaCandidat" id="educatie">
                    <h2><?php esc_html_e( 'Educatie', 'managero' ); ?></h2>
                    <ul style="padding-left: 20px; list-style: square; ">
                        <target class="field-educatie"></target>
                    </ul>
                </div>

                <div class="zonaCandidat" id="limbi">
                    <h2><?php esc_html_e( 'Limbi straine', 'managero' ); ?></h2>
                    Engleza -
                    <target class="field-engleza"></target>
                    <target class="field-alte-limbi"></target>
                </div>


                <div class="zonaCandidat" id="certificari">
                    <h2><?php esc_html_e( 'Competente relevante', 'managero' ); ?></h2>
                    <target class="field-skils"></target>
                </div>

                <div class="zonaCandidat" id="certificari">
                    <h2><?php esc_html_e( 'Certificari', 'managero' ); ?></h2>
                    <target class="field-calificari"></target>
                </div>

                <div class="zonaCandidat" id="experienta">
                    <h2><?php esc_html_e( 'Experienta', 'managero' ); ?></h2>
                    <target class="field-experienta"></target>

                </div>

                <div class="zonaCandidat" id="cerinte">
                    <h2><?php esc_html_e( 'Cerinte', 'managero' ); ?></h2>
                    <b>Salariu: <span class="field-salariu"></span>&euro;</b> <br/>
                    <target class="field-alte-cerinte"></target>
                </div>

                <div class="zonaCandidat" id="brief">
                    <h2><?php esc_html_e( 'Note si comentarii libere', 'managero' ); ?></h2>
                    <target class="field-comentarii"></target>

                </div>

				<?php if ( $job_id = $_GET['job_id'] ): ?>
                    <div class="zonaCandidat" id="zonaFisiere">
                        <h2><?php esc_html_e( 'Fisiere atasate', 'managero' ); ?></h2>
                        <div class="row">
							<?php
							$get_job_files_attached = get_post_meta( $job_id, 'jobsearch_job_files_attached', true );
							$atached_files          = json_decode( $get_job_files_attached[ $candidate_id ], true );

							foreach ( $atached_files as $file ) { ?>
                                <div class="col-md-3">
                                    <a href="<?= $file['link'] ?>" download><?= $file['nume']; ?></a>
                                </div>

							<?php } ?>
                        </div>
                    </div>
				<?php endif; ?>


            </div>
        </div>
        <div class="cloneSubmit">
            <input type="submit" class="updateProfil acf-button button button-primary button-large"
                   value="Actualizează"> <span
                    class="acf-spinner"></span>
        </div>
    </div>

    <script>

        //jobEdit
        //jobPreview
        var preview = '<i class="fa fa-eye" aria-hidden="true"></i>' + '<span>Vizualizeaza CV</span>';
        var edit = '<i class="fa fa-edit" aria-hidden="true"></i>' + '<span>Editeaza CV</span>';

        var coverImage = jQuery('.acf-field[data-name="imagine_cover"] img').attr('src');
        coverImage = coverImage.replace("-1024x278", "");
        var oldImg = jQuery('.dashboardImage img').attr('src');


        function get_field_data() {

            var input = {};

            input['prenume'] = jQuery('.acf-field[data-name="prenume"] input:enabled').val();
            input['nume'] = jQuery('.acf-field[data-name="nume"] input:enabled').val();
            input['particula'] = jQuery('.acf-field[data-name="particula"] input:enabled').val();
            input['formula_adresare'] = jQuery('.acf-field[data-name="formula_adresare"] select').val();
            input['titlu'] = jQuery('.acf-field[data-name="titlu"] select').val();

            if (input['titlu'] == 'Altul') {
                input['titlu'] = jQuery('.acf-field[data-name="titlu_altu"] input:enabled').val();
            }
            input['nickaname'] = jQuery('.acf-field[data-name="nickaname"] input:enabled').val();
            input['anul_nasterii'] = jQuery('.acf-field[data-name="anul_nasterii"] select').val();
            input['sex'] = jQuery('.acf-field[data-name="sex"] select').val();


            var numeComplet = '';
            var numeComplet = input['titlu'] + ' ' + input['prenume'] + ' ' + input['particula'] + ' ' + input['nume'] + ' ' + '(' + input['formula_adresare'] + ')';
            console.log(numeComplet);


            var calificari = [];
            jQuery('.acf-field[data-name="certificari"] input[type="text"]:enabled').each(function () {
                calificari.push(jQuery(this).val());
            });

            console.log(calificari);

            var salariu = jQuery('.acf-field[data-name="salariu_minim_accepta"] input[type="number"]:enabled').val();

            var engleza = jQuery('.acf-field[data-name="limba_engleza"] select').val();

            var resedinta = jQuery('.acf-field[data-name="judet_candidat"] select').val();

            var sex = jQuery('.acf-field[data-name="sex"] select:enabled').val();

            var varsta = jQuery('.acf-field[data-name="anul_nasterii"] select:enabled').val();

            varsta = 2019 - varsta;

            var skils = jQuery('.acf-field[data-name="relevant_skils"] textarea:enabled').text();

            var cerinte = jQuery('.acf-field[data-name="alte_cerinte"] textarea:enabled').text();

            var comentarii = jQuery('.acf-field[data-name="note_comentarii"] textarea:enabled').text();


            var limbi = [];
            jQuery('.acf-field[data-name="alte_limbi"] input[type="text"]:enabled').each(function () {
                limbi.push(jQuery(this).val());
            });


            var educatie = [];
            jQuery('.acf-field[data-name="educatie"] input[type="text"]:enabled').each(function () {
                educatie.push(jQuery(this).val());
            });

            var links = [];
            jQuery('.acf-field[data-name="link-uri"] .acf-row').each(function (index) {
                var link = {};
                link['titlu'] = jQuery('input[type="text"]', this).val();
                link['url'] = jQuery('input[type="url"]', this).val();
                links[index] = link;
            });

            var companiiNume = [];
            var companiiDescriere = [];

            jQuery('.companief input[type="text"]:enabled').each(function () {
                companiiNume.push(jQuery(this).val());
            });

            jQuery('.descriere-companie textarea').each(function () {
                companiiDescriere.push(jQuery(this).val());
            });

            var companii = [];


            jQuery('.postExperienta').each(function () {
                    var companie = {};
                    var post = {};
                    var posts = [];

                    post['name'] = jQuery('.acf-field[data-name="post"] input:enabled', this).val();
                    post['de-la'] = jQuery('.acf-field[data-name="de_la"] input.hasDatepicker:enabled', this).val();
                    post['pana-la'] = jQuery('.acf-field[data-name="pana_la"] input.hasDatepicker:enabled').val();
                    post['ultimul-job'] = jQuery('.acf-field[data-name="ultimul_job"] input:checked', this).val();
                    post['ierarhie'] = jQuery('.acf-field[data-name="ierarhie"] select', this).val();
                    post['descriere-job'] = jQuery('.acf-field[data-name="descriere_job"] textarea', this).val();
                    post['alte-detalii'] = jQuery('.acf-field[data-name="alte_detalii"] textarea', this).val();

                    posts.push(post);
                    post = {};

                    companie['post'] = posts;
                    companii.push(companie);

                }
            );

            var experinta = [];

            jQuery(companiiNume).each(function (index) {
                var lastCompanii = {};
                lastCompanii = companii[index];
                lastCompanii['nume'] = companiiNume[index];
                lastCompanii['descriere'] = companiiDescriere[index];
                experinta.push(lastCompanii);
            });


         //   console.log(experinta);

            var profileImage = jQuery('.acf-field[data-name="imagine_profil"] img').attr('src');

            input['educatie'] = educatie;
            input['comentarii'] = comentarii;
            input['cerinte'] = cerinte;
            input['salariu'] = salariu;
            input['sex'] = sex;
            input['varsta'] = varsta;
            input['resedinta'] = resedinta;
            input['calificari'] = calificari;
            input['engleza'] = engleza;
            input['limbi'] = limbi;
            input['skils'] = skils;
            input['links'] = links;
            input['experinta'] = experinta;
            input['imagine'] = profileImage;


            input['numecomplet'] = numeComplet;

            jQuery('.field-educatie').html('');
            jQuery.each(input['educatie'], function (key, value) {
                jQuery('.field-educatie').append('<li>' + value + '</li>');
            });


            jQuery('.field-imagineProfil').attr('src', input['imagine']);
            jQuery('.field-numecomplet').html(numeComplet);
            jQuery('.field-varsta').html(input['varsta']);
            jQuery('.field-sex').html(input['sex']);
            jQuery('.field-resedinta').html(input['resedinta']);
            jQuery('.field-nickname').html(input['nickname']);
            jQuery('.field-engleza').html(input['engleza']);
            jQuery('.field-skils').html(input['skils']);



            jQuery('.field-calificari').html('');
            jQuery.each(input['calificari'], function (key, value) {
                if(key == 0){
                    jQuery('.field-calificari').append(value);
                } else{
                    jQuery('.field-calificari').append(', ' + value);
                }

            });

            console.log(limbi);

            jQuery('.field-alte-limbi').html('');
            jQuery.each(input['limbi'], function (key, value) {
               // jQuery('.field-alte-limbi').append('<li>' + value + '</li>');
                jQuery('#limbi').append( ', ' + value );
            });




            jQuery('.field-salariu').html(input['salariu']);
            jQuery('.field-alte-cerinte').html(input['cerinte']);
            jQuery('.field-comentarii').html(input['comentarii']);


            jQuery('.field-experienta').html('');

            var experientaFinal = '';

            jQuery.each(input['experinta'], function (key, value) {
                experientaFinal = experientaFinal + '<ul>';
                experientaFinal = experientaFinal + '<li><span class="tcomapnie">' + value['nume'] + '</span></li>'
                experientaFinal = experientaFinal + '<ul>';
                jQuery.each(value['post'], function (key, value) {
                    experientaFinal = experientaFinal + '<li>';
                    experientaFinal = experientaFinal + '<span>' + value['de-la'] + '</span>' + ' - ' + '<span>' + value['pana-la'] + '</span><br />';
                    experientaFinal = experientaFinal + '<strong><span>' + value['name'] + '</span>, <span>' + value['ierarhie'] + '</span></strong><br />';
                    experientaFinal = experientaFinal + '<span>' + value['descriere-job'] + '</span><br />';
                    experientaFinal = experientaFinal + '<span>' + value['alte-detalii'] + '</span><br />';

                    experientaFinal = experientaFinal + '</li>';
                });
                experientaFinal = experientaFinal + '</ul>';
                experientaFinal = experientaFinal + '</ul>';
            });

            jQuery('.field-experienta').html(experientaFinal);

        }


        jQuery('.previewJob').toggle(function () {
            jQuery(this).html(edit);
            jQuery('.jobPreview').show();
            jQuery('.jobEdit').hide();
            jQuery('.dashboardImage img').attr('src', coverImage);
            get_field_data();


        }, function () {
            jQuery(this).html(preview);
            jQuery('.jobPreview').hide();
            jQuery('.jobEdit').show();
            jQuery('.dashboardImage img').attr('src', oldImg);
        });

        var fieldPrenume = jQuery('.acf-field[data-name=prenume] input');
        var fieldNume = jQuery('.acf-field[data-name=nume] input');
        var fieldParticula = jQuery('.acf-field[data-name="particula"] input:enabled');
        var fieldFormula = jQuery('.acf-field[data-name=formula_adresare] select');
        var fieldTitlu = jQuery('.acf-field[data-name=titlu] select');
        var fieldNickname = jQuery('.acf-field[data-name=nickaname] input');


        var prenume = fieldPrenume.val();
        var particula = fieldParticula.val();
        var nume = fieldNume.val();
        var formula = "(" + fieldFormula.val() + ")";
        var titlu = fieldTitlu.val();
        var nickname = "(" + fieldNickname.val() + ")";

        if (titlu == 'Altul') {
            titlu = jQuery('.acf-field[data-name="titlu_altu"] input:enabled').val();
        }

        jQuery('.numePreview .nume').html(nume);
        jQuery('.numePreview .prenume').html(prenume);
        jQuery('.numePreview .particula').html(particula);
        jQuery('.numePreview .formula').html(formula);
        jQuery('.numePreview .titlu').html(titlu);
        jQuery('.numePreview .nickname').html(nickname);

        fieldPrenume.keyup(function () {
            prenume = fieldPrenume.val();
            jQuery('.numePreview .prenume').html(prenume);
        });

        fieldParticula.keyup(function () {
            particula = fieldParticula.val();
            jQuery('.numePreview .particula').html(particula);
        });

        fieldNume.keyup(function () {
            nume = fieldNume.val();
            jQuery('.numePreview .nume').html(nume);
        });

        fieldFormula.change(function () {
            formula = fieldFormula.val();
            jQuery('.numePreview .formula').html(formula);
        });

        fieldTitlu.change(function () {
            titlu = fieldTitlu.val();
            if (titlu == 'Altul') {
                titlu = jQuery('.acf-field[data-name="titlu_altu"] input:enabled').val();
            }
            jQuery('.numePreview .titlu').html(titlu);
        });

        fieldNickname.keyup(function () {
            nickname = fieldNickname.val();
            jQuery('.numePreview .nickname').html(nickname);
        });


        var buttonClone = jQuery('.acf-form-submit').html();
        buttonClone = '<div class="cloneSubmit">' + buttonClone + '</div>';
        jQuery('#acf-form-candidate-profile').append(buttonClone);

        jQuery('.updateProfil').click(function () {
            jQuery('#acf-form-candidate-profile input[type="submit"]').click();
        });





            jQuery('.hasDatepicker').datepicker({
                        changeMonth: true,
                        changeYear: true,
                        dateFormat: 'MM yy',

                        onClose: function() {
                            var iMonth = jQuery("#ui-datepicker-div .ui-datepicker-month :selected").val();
                            var iYear = jQuery("#ui-datepicker-div .ui-datepicker-year :selected").val();
                            jQuery(this).datepicker('setDate', new Date(iYear, iMonth, 1));
                        },

                        beforeShow: function() {
                            if ((selDate = jQuery(this).val()).length > 0)
                            {
                                iYear = selDate.substring(selDate.length - 4, selDate.length);
                                iMonth = jQuery.inArray(selDate.substring(0, selDate.length - 5), $(this).datepicker('option', 'monthNames'));
                                jQuery(this).datepicker('option', 'defaultDate', new Date(iYear, iMonth, 1));
                                jQuery(this).datepicker('setDate', new Date(iYear, iMonth, 1));
                            }
                        }
                    });


    </script>

    <script type="text/javascript">

    </script>

    <style>
        /*.ui-datepicker table {*/
        /*width: 100%;*/
        /*font-size: .9em;*/
        /*border-collapse: collapse;*/
        /*margin: 0 0 .4em;*/
        /*display: none;*/
        /*}*/
        /*button.ui-datepicker-current.ui-state-default.ui-priority-secondary.ui-corner-all {*/
        /*display: none;*/
        /*}*/
    </style>

	<?php
}