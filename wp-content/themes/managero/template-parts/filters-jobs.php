<?php
//sectiune filtre
?>
<h2><?php esc_html_e('Quick Filter', 'managero'); ?></h2>

<div class="filters-body">
    <form id="filter-form">

        <?php list_states(); ?>

        <div class="input-fake" id="locatie" data-target="statelist">
            Locatie <i class="far fa-plus-square"></i>
        </div>
        <div id="statelist" class="triger-div">
            <a href='javascript:void(0)' class='close'>Inchide</a>
        </div>


        <div class="input-fake" id="nivel" data-target="nivellist">
            Nivel Ierarhic <i class="far fa-plus-square"></i>
        </div>
        <div id="nivellist" class="triger-div">
            <?php list_nivele(); ?>
        </div>


        <div class="input-fake" id="domeniu" data-target="domeniulist">
            Domeniu de activitate <i class="far fa-plus-square"></i>
        </div>
        <div id="domeniulist" class="triger-div">
            <?php list_sectors(); ?>
        </div>

        <input type="text" value="" class="input-text salariu" placeholder="Salariu minim($)"/>

        <a href="javascript:void(0)" class="filter-apply">APLICA FILTRE</a>


    </form>
    <div id="ceva">

    </div>

    <script>
        // quick filters
        //define selectedCountry = '';

        jQuery(document).ready(function () {


            (function ($) {
                $.fn.inputFilter = function (inputFilter) {
                    return this.on("input keydown keyup mousedown mouseup select contextmenu drop", function () {
                        if (inputFilter(this.value)) {
                            this.oldValue = this.value;
                            this.oldSelectionStart = this.selectionStart;
                            this.oldSelectionEnd = this.selectionEnd;
                        } else if (this.hasOwnProperty("oldValue")) {
                            this.value = this.oldValue;
                            this.setSelectionRange(this.oldSelectionStart, this.oldSelectionEnd);
                        }
                    });
                };
            }(jQuery));

            jQuery(".input-text").inputFilter(function (value) {
                return /^\d*$/.test(value);
            });

        });


    </script>


</div>


<div>
    <h2><?php esc_html_e('Filtrele tale', 'managero'); ?></h2>
        <?php
        $user_id = get_current_user_id();
        $user_obj = get_user_by('ID', $user_id);
        $candidate_id = jobsearch_get_user_candidate_id($user_id);

        if ($candidate_id) {
            $filtre = get_post_meta($candidate_id, 'filtre_candidat', true);
            $filtre = unserialize($filtre); ?>

            <ul class="lista-template" style="padding: 0; margin: 0; padding-top: 20px;width: 100%; float: left;">
                <?php
                $count = 0;
                foreach ($filtre as $key => $template) {
                    $max_length = 120;
                    if (strlen($template['descriere_template']) > $max_length) {
                        $offset = ($max_length - 3) - strlen($template['descriere_template']);
                        $template['descriere_template'] = substr($template['descriere_template'], 0, strrpos($template['descriere_template'], ' ', $offset)) . '...';
                    }
                    $id_template = wc_strtolower(str_replace(' ', '', $key));
                    echo "<li class='template-name'><strong><a href='javascript:void(0);' id='link-template-$id_template' class='template-on'  data-template='$id_template'>" .
                        $key . "</a></strong><br />" . $template['descriere_template'] .
                        "</li>";
                    $count++;
                }
                ?>
            </ul>
            <a href="javascript:void(0)" class="filter-apply-2">APLICA FILTRU</a>

        <?php } else { ?>
            <div class="widget_apply_job_wrap" style="max-width: 100%; width: 100%;">
                <a href="javascript:void(0);" id="button-apply" class="jobsearch-open-signin-tab" style="float:left; width: 100%; line-height: 25px;">
                    <strong style="text-align: center; font-size-adjust: 20px">Login</strong><br>pentru a aplica filtre personale </a>
            </div>

      <?php  } ?>

</div>

<style>
    .lista-template li {
        font-size: 12px;
        font-weight: 400;
        font-family: "Open Sans", sans-serif;
        border-bottom: 2px solid #e1e1e1;
        line-height: 24px;
        margin-bottom: 15px;
        padding-bottom: 15px;
        float: left;
        width: 100%;
        height: 66px;
    }
    a.filter-apply-2 {
        width: 180px;
        height: 35px;
        text-align: center;
        background-color: #fcb13c;
        line-height: 35px;
        display: block;
        font-family: "Oswald", sans-serif;
        font-weight: 700;
        color: white !important;
        border-radius: 0;
        border: 0;
        transition: 0.3s;
        font-size: 14px;
        float: left;
        margin-bottom: 20px;
        text-decoration: none;
    }
</style>

<script>
    var locatie1 = document.getElementById('locatie');
    var nivel1 = document.getElementById('nivel');
    var domeniu1 = document.getElementById('domeniu');
    var popuoHideLocatie = document.getElementById('statelist');
    var popuoHideNivel = document.getElementById('nivellist');
    var popuoHideDomeniu = document.getElementById('domeniulist');

    locatie1.addEventListener('click', function () {

        popuoHideNivel.style.display = 'none';
        popuoHideDomeniu.style.display = 'none';
    });
    nivel1.addEventListener('click', function () {

        popuoHideLocatie.style.display = 'none';
        popuoHideDomeniu.style.display = 'none';
    });
    domeniu1.addEventListener('click', function () {

        popuoHideNivel.style.display = 'none';
        popuoHideLocatie.style.display = 'none';
    });

    jQuery('.template-on').click(function () {
        jQuery('.template-on').removeClass('activlink');
        jQuery(this).addClass('activlink');
        // var template = '#template-' + jQuery(this).attr('data-template');
        // jQuery('.template-wrap').removeClass('activ');
        // jQuery(template).addClass('activ');
    });

</script>