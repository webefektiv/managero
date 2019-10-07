<script src="/wp-content/plugins/acf-extended/assets/acf-extended.js"></script>
<link href="/wp-content/plugins/acf-extended/assets/acf-extended.css" >
<?php
acf_enqueue_uploader();
acf_form_head();

$id = get_the_ID();

global $jobsearch_plugin_options, $Jobsearch_User_Dashboard_Settings;

$current_user = wp_get_current_user();
$user_id      = get_current_user_id();
$user_obj     = get_user_by( 'ID', $user_id );

$employer_id = jobsearch_get_user_employer_id( $user_id );

if ( $employer_id > 0 ) { ?>

    <div class="jobEdit">
        <div class="jobsearch-employer-box-sectios">
            <div class="row">
                <div class="col-12 col-md-3">
                    <?php
                    $templates = get_post_meta( $employer_id, "companie_company_profiles", true );
                    $templates = unserialize(base64_decode($templates));
                    ?>

                    <ul class="lista-template lista-template-custom">
                        <h3 class="title-template-anunt">Alege template companie</h3>
                        <?php
                        $count = 0;
                        foreach ( $templates as $key => $template ) {
                            $activ = ( $count == 0 ) ? 'activlink' : '';
                            $max_length = 42;
                            $fullTemplatedescriere = $template['descriere_template'];
                            if (strlen($template['descriere_template']) > $max_length)
                            {
                                $offset = ($max_length - 3) - strlen($template['descriere_template']);
                                  $template['descriere_template'] = substr($template['descriere_template'], 0, strrpos($template['descriere_template'], ' ', $offset)) . '...';
                            }
                            $max_lengthTitle = 35;
                            if (strlen($key) > $max_lengthTitle) {
                                $offset = ($max_lengthTitle - 3) - strlen($key);
                                $key = substr($key, 0, strrpos($key, ' ', $offset)) . '...';
                            }
                            $id_template = wc_strtolower( str_replace( ' ', '', $key ) );
                            echo "<li class='template-name'><strong><a href='javascript:void(0);' id='link-template-$id_template' class='template-on  $activ'  data-template='$id_template'>" .
                                $key . "</a></strong><br />" . "<div class='text-description_template'>" . $template['descriere_template'] . "<div class='more_info'>" . $fullTemplatedescriere . "</div></div>"."</li>";
                            $count++;
                        }
                        ?>

                    </ul>

                </div>

                <div class="col-12 col-md-9">
                    <?php
                    $count = 0;
                    foreach ( $templates as $key => $template ) {
                        $id_template = wc_strtolower( str_replace( ' ', '', $key ) );

                        ?>

                        <div class="template-wrap <?php echo ( $count == 0 ) ? 'activ' : ''; ?>"
                             id="template-<?= $id_template; ?>">

                            <div id="sectiune-companie" class="sectiune-comp1">
                                <h2 class="job-title"><span>Ai selectat: <?= $template['nume_template' ]?></span><?php
                                    echo "<a href='javascript:void(0)' class='edit-template' data-template='$key' data-title='" . $template['nume'] . "'>EDITEAZA</a>";
                                    ?></h2>

                                <div class="row jobDetails">
                                    <div class="col-sm-12">
                                        <h4><?php esc_html_e( 'Nume', 'managero' ); ?></h4>
                                    </div>
                                    <div class="col-sm-8">
			                            <?= $template['nume_companie']; ?><br />
	                                    <?= $template['nume_alternativ']; ?>
                                    </div>
<!--                                    <div class="col-md-4">-->
<!--                                        <img src="--><?//= wp_get_attachment_url( $template['imagine_profil_companie'] ); ?><!--"-->
<!--                                             style=" height: 71px;margin-top: -20px; float: right;" />-->
<!--                                    </div>-->
                                </div>
                                <div class="row jobDetails">
                                    <div class="col-sm-12">
                                        <h4><?php esc_html_e( 'Descriere', 'managero' ); ?></h4>
                                    </div>
                                    <div class="col-sm-12">
			                            <?= $template['descriere_companie']; ?>
                                    </div>
                                </div>

                                    <div class="row jobDetails">
                                        <div class="col-sm-12">
                                            <h4><?php esc_html_e( 'Domeniu de activitate', 'managero' ); ?>
                                                <span>(pe larg)</span>
                                            </h4>
                                        </div>
                                        <div class="col-sm-12">
	                                        <?= $template['descriere_domeniu_larg']; ?>
                                        </div>
                                    </div>

                                    <div class="row jobDetails">
                                        <div class="col-sm-12">
                                            <h4><?php esc_html_e( 'Domeniu de activitate', 'managero' ); ?>
                                                <span>(sintetic)</span>
                                            </h4>
                                        </div>
                                        <div class="col-sm-12">
	                                        <?= $template['descriere_domeniu_sintetic']; ?>
                                        </div>
                                    </div>

                                <div class="row jobDetails">

                                        <div class="col-md-4">
                                            <h4><?php esc_html_e( 'Extindere', 'managero' ); ?></h4>
	                                        <?= $template['extindere']; ?>
                                        </div>


                                        <div class="col-md-4">
                                            <h4><?php esc_html_e( 'Tara de origine', 'managero' ); ?></h4>
	                                        <?= $template['origine'][1]; ?>
                                        </div>

                                        <div class="col-md-4">
                                            <h4><?php esc_html_e( 'Head of office', 'managero' ); ?></h4>
	                                        <?= $template['head_office']; ?>
                                        </div>

                                </div>
                                <div class="row jobDetails">
                                        <div class="col-md-4">
                                            <h4><?php esc_html_e( 'Localizare in Romania', 'managero' ); ?></h4>
	                                        <?= $template['localizare'][1]; ?>
                                        </div>
                                        <div class="col-md-4">
                                            <h4><?php esc_html_e( 'Cifra de afaceri in Romania', 'managero' ); ?></h4>
	                                        <?= $template['cifra_de_afaceri']; ?>&euro;
                                        </div>
                                        <div class="col-md-4">
                                            <h4><?php esc_html_e( 'Numar de angajati in Romania', 'managero' ); ?></h4>
	                                        <?= $template['numar_angajati']; ?> de angajati
                                        </div>

                                </div>

                                <div id="sectiune-oferta">
                                    <div class="row jobDetails">
                                        <div class="col-sm-12">
                                            <h4>Link-uri</h4>
                                            <ul class="listaFisiere">
					                            <?php
					                            foreach ( $template['link-uri'] as $nume => $lik ) {
						                            echo "<li><a href='" . wp_get_attachment_url( $lik ) . "'>" . $nume . "</a></li>";
					                            }
					                            ?>
                                            </ul>
                                        </div>
                                    </div>
                                </div>

<!--                                <div class="row jobDetails">-->
<!--                                    <div class="col-md-12">-->
<!--                                        --><?php //$imgPrincipala = $template['imagine_principala']; ?>
<!---->
<!--                                        <img class="imgCompanyCover" src="--><?//= wp_get_attachment_url($imgPrincipala); ?><!--" alt="">-->
<!--                                    </div>-->
<!--                                </div>-->

                            </div>
                        </div>

                        <?php
                        $count ++;
                    } ?>

                </div>
            </div>


        </div>
    </div>

    <div id="rezultatModal">

    </div>
    <script>

        (function ($, undefined) {
            '$:nomunge'; // Used by YUI compressor.

            $.fn.serializeObject = function () {
                var obj = {};

                $.each(this.serializeArray(), function (i, o) {
                    var n = o.name,
                        v = o.value;

                    obj[n] = obj[n] === undefined ? v
                        : $.isArray(obj[n]) ? obj[n].concat(v)
                            : [obj[n], v];
                });

                return obj;
            };

        })(jQuery);


        function get_template_data(){
            var obj_values = jQuery('#acf-form-company-profile').serializeObject();
            console.log(obj_values);
            var fields = Object.keys(obj_values);

            var object_new_label = [];

            jQuery(fields).each(function () {

                var string = this.replace('acf[', '');
                string = string.replace(']', '');
                var item = string.replace('[', ' ');
                item = item.replace(']', '');
                item = item.replace('[', '');
                item = item.replace(']', '');
                var field = item.substr(item.length - 19);

                var name = jQuery('*[data-key="' + field + '"]').attr('data-name');

                object_new_label[this] = name;

            });

        //    console.log(object_new_label);

            object_new_label['acf[field_5d4aa946caeb9][]'] = 'date_visibile';


            var format_obj = {};
            var id = 0;
            for (var key in object_new_label) {


                if (object_new_label.hasOwnProperty(key)) {

                    if (format_obj.hasOwnProperty(object_new_label[key]) && obj_values[key] !== '') {

                        if(typeof(format_obj[object_new_label[key]]) === 'object'){

                        } else{
                            var temp = format_obj[object_new_label[key]];
                            format_obj[object_new_label[key]] = [];
                            format_obj[object_new_label[key]].push(temp);
                        }

                        format_obj[object_new_label[key]].push(obj_values[key]);

                    } else if (obj_values[key] !== '') {

                        format_obj[object_new_label[key]] = obj_values[key];

                    }
                }


            }


            format_obj['link-uri'] = {};

            if( typeof(format_obj['titlu']) === 'string' ){
                format_obj['link-uri'][format_obj['titlu']] = format_obj['link'];
            } else {
                for (var key in format_obj['titlu']) {
                    format_obj['link-uri'][format_obj['titlu'][key]] = format_obj['link'][key];
                }
            }

        // console.log(format_obj)

            return format_obj;
        }

        function call_save_template() {

            jQuery('#salveaza_profil_job').click(function (event) {

                var format_obj = get_template_data();

                var nameProfilTemplate = jQuery('#acf-field_5d35aa533fae5-field_5d35a47a35468');

                    jQuery('#overwLoad').show();
                    jQuery.ajax({
                        type: "post",
                        url: "<?=  admin_url( 'admin-ajax.php' ); ?>",
                        data: {
                            'action': 'job_template_save',
                            'companie': <?= $employer_id; ?>,
                            'template': {
                                'nume': format_obj.nume_template,
                                'date': format_obj,
                                'tip': 'company_profile'
                            },
                            'new' : false
                        },
                        success: function (data) {
                            //  console.log(data);
                            location.reload();
                        }
                        ,
                        error: function (errorThrown) {
                            console.log(errorThrown);
                        }
                    });
            });

            jQuery('#salveaza_ca_job_profil').click(function () {
                jQuery('#wrap-salveaza-ca').slideToggle();
            });


            jQuery('#ok_save_as').click(function () {

                jQuery('#overwLoad').show();
                var format_obj = get_template_data();

                format_obj.nume_template = format_obj.nume_template_as;
                format_obj.descriere_template = format_obj.descriere_template_as;

                // console.log(format_obj);

                jQuery.ajax({
                    type: "post",
                    url: "<?=  admin_url( 'admin-ajax.php' ); ?>",
                    data: {
                        'action': 'job_template_save',
                        'companie': <?= $employer_id; ?>,
                        'template': {
                            'nume':   format_obj.nume_template,
                            'date': format_obj,
                            'tip': 'company_profile',
                            'new': true
                        },
                        'new' : true
                    },
                    success: function (data) {
                       //    console.log(data);
                         location.reload();
                    }
                    ,
                    error: function (errorThrown) {
                        console.log(errorThrown);
                    }
                });


            });


            jQuery('#delete_template').click(function () {
                jQuery('#overwLoad').show();
                if(confirm("Sigur doresti sa stergi acest template?")){
                    delete_profile();
                }
                else{
                    return false;
                }
                function delete_profile(){
                    var template = jQuery('#acf-field_5d35aa533fae5-field_5d35a47a35468').val();
                    //  console.log(template);
                    jQuery.ajax({
                        type: "post",
                        url: "<?=  admin_url( 'admin-ajax.php' ); ?>",
                        data: {
                            'action': 'job_profile_delete',
                            'companie': <?= $employer_id; ?>,
                            'template': template,
                            'tip' : 'company_profile'
                        },
                        success: function (data) {
                         //   console.log(data);
                            location.reload();
                            (function($) {
                                $(document).ready(function() {
                                    acf.unload.active = false;
                                });
                            })(jQuery);
                        }
                        ,
                        error: function (errorThrown) {
                            console.log(errorThrown);
                        }
                    });
                }

            });

        }


        // schimba tabul
        jQuery('.template-on').click(function () {
            jQuery('.template-on').removeClass('activlink');
            jQuery(this).addClass('activlink');

            var template = '#template-' + jQuery(this).attr('data-template');
            jQuery('.template-wrap').removeClass('activ');
            jQuery(template).addClass('activ');
            jQuery('.job-title span').html('Ai selectat: ' + jQuery(this).text());


        });

        jQuery('.edit-template').click(function () {
            jQuery('#overwLoad').show();
            var template = jQuery(this).attr('data-template');
            var numeProfil  = jQuery('#acf-field_5d35aa533fae5-field_5d35a47a35468');

            jQuery.ajax({
                type: "post",
                url: "<?=  admin_url( 'admin-ajax.php' ); ?>",
                data: {
                    'action': 'job_profile_apply',
                    'companie': <?= $employer_id; ?>,
                    'template': template,
                    'tip' : 'company_profile'
                },

                success: function (data) {
                    //   console.log(data);
                    jQuery('#rezultatModal').html(data);
                    jQuery('#overwLoad').hide();
                    jQuery('#formModal').modal('show');
                    jQuery('#acf-field_5d35aa533fae5-field_5d35a47a35468').attr('readonly', true);

                    (function ($) {
                        acf.do_action('append', $('#acf-form-company-profile'));
                    })(jQuery);

                    (function($) {
                        $(document).ready(function() {
                            acf.unload.active = false;
                        });
                    })(jQuery);

                    call_save_template();
                } 
                ,
                error: function (errorThrown) {
                    console.log(errorThrown);
                }
            });

        });

        (function ($) {
            acf.do_action('append', $('#rezultatModal-id'));
        })(jQuery);

        jQuery('.adaugaJob').attr('href','').html('Adauga template').click(function (event) {
            jQuery('#overwLoad').show();
            event.preventDefault();
            jQuery.ajax({
                type: "post",
                url: "<?=  admin_url( 'admin-ajax.php' ); ?>",
                data: {
                    'action': 'add_new_profile',
                    'companie': <?= $employer_id; ?>,
                    'tip' : 'company_profile'
                },

                success: function (data) {

                    //  console.log(data);
                    jQuery('#rezultatModal').html(data);

                    jQuery('#overwLoad').hide();

                    jQuery('#formModal').modal('show');

                    (function ($) {
                        acf.do_action('append', $('#acf-form-company-profile'));
                    })(jQuery);

                    $('.acf-field[data-name="delete_template"]').hide();
                    $('.acf-field[data-name="salveaza_ca"]').hide();

                    $('.acf-field[data-name="salveaza2"]').css({
                        'left': '50%',
                        'transform': 'translateX(-50%)',
                        'border-left': 'none'
                    });

                    (function($) {
                        $(document).ready(function() {
                            acf.unload.active = false;
                        });
                    })(jQuery);

                    call_save_template();
                }
                ,
                error: function (errorThrown) {
                    console.log(errorThrown);
                }
            });

        });

    </script>

    <style>
        .loader {
            border: 16px solid #f3f3f3;
            border-radius: 50%;
            border-top: 16px solid orange;
            border-bottom: 16px solid darkorange;
            width: 120px;
            height: 120px;
            -webkit-animation: spin 2s linear infinite;
            animation: spin 2s linear infinite;
            left:45%;
            top:50%;
            transform: translate(-50%,-50%);
            position: absolute;
        }

        @-webkit-keyframes spin {
            0% { -webkit-transform: rotate(0deg); }
            100% { -webkit-transform: rotate(360deg); }
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        #overwLoad{
            width: 100%;
            height: 100%;
            position: fixed;
            z-index: 9999999999;
            left: 0;
            top: 0;
            right: 0;
            bottom: 0;
            background-color: rgba(8, 8, 8, 0.8);
            display: none;
        }
        .acf-field-image-aspect-ratio-crop[data-name=imagine_cover] {
            position: relative;
            top: initial;
            right: initial;
            border-left: none !important;
        }

        .acf-field[data-name='imagine_principala'], .acf-field[data-name='imagine_profil_companie']{
            display:  none !important;
        }
    </style>
    <div id="overwLoad">
        <div class="loader"></div>
    </div>

    <style>
        .modal-dialog {
            max-width: 1000px;
        }

        #formModal {
            overflow-y: scroll;
        }
        h1 span{
           text-transform: none;
        }
    </style>

    <?php
}