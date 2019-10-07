<?php
/**
 * Template Name: labels
 * temporar
 * de mutat in admin mananagero
 */

get_header();
$user_id = get_current_user_id();
$candidate_id = jobsearch_get_user_candidate_id($user_id);
//$labelsENG = get_post_meta(1676, "label_eng", true);
//$labelsENG = unserialize($labelsENG);
// nice_print_r($labelsENG);


if ($_POST) {

    $labelsENG = get_post_meta(1676, "label_eng", true);
    $labelsENG = unserialize($labelsENG);

    foreach ($_POST as $key => $content) {
        $tasks = 0;
        $field = get_field_object($key);
        if (array_key_exists('label', $field) && $content['label'] != '' && $content['label'] != null) {
            $field['label'] = $content['label'];
            $tasks++;
        }
        if (array_key_exists('instructions', $field) && $content['instructions'] != '' && $content['instructions'] != null) {
            $field['instructions'] = $content['instructions'];
            $tasks++;
        }
        if (array_key_exists('placeholder', $field) && $content['placeholder'] != '' && $content['placeholder'] != null) {
            $field['placeholder'] = $content['placeholder'];
            $tasks++;
        }
        if ($tasks > 0) {
            acf_update_field($field);
        }

        if($content['engleza'] != '' && $content['engleza'] != null){
            $labelsENG[$field['key']] = $content['engleza'];
        }

        unset($_POST[$key]);
    }


    $labelsENG = serialize($labelsENG);

    update_post_meta('1676','label_eng', $labelsENG);

}

// get field groups

$profil_companie = acf_get_fields('group_profil_companie');

$profil_job = acf_get_fields('group_profil_job');

$template_anunt = acf_get_fields('group_job_complet');

$profil_candidat = acf_get_fields('group_profil_candidat');

$texte_predefinite = acf_get_fields('group_texte-predefinite_acf');

$filtre_salvate = acf_get_fields('group_filtre_candidat');


// set group campuri
$campuri = [
    'Profil companie' => $profil_companie,
    'Profil job' => $profil_job,
    'Template anunt' => $template_anunt,
    'Profil candidat' => $profil_candidat,
    'Texte predefinite' => $texte_predefinite,
    'Filtre salvate' => $filtre_salvate
];

// fielduri fara placeholder
$no_placeholder = [
    'group',
    'checkbox',
    'radio',
    'select'
];

?>
<div class="container">
    <div class="featimage">
        <img src="http://managero.ro/wp-content/uploads/2019/08/managero_feat_joburi.png">
    </div>
    <div class="row">
        <div class="col-6">
            <h1 class="page-title-top page-title-top-1">
                LISTA LABELS, TOOLTIPS, PLACEHOLDERS                </h1>
        </div>
        <div class="col-6">

        </div>
    </div>
</div>

<div class="whitebg" style="padding-bottom: 40px" id="LabelsRO">
    <div class="container">

        <ul class="nav nav-tabs" id="myTab" role="tablist">
            <?php
            $x = 0;
            foreach ($campuri as $key => $camp):
                $id = str_replace(' ', '-', strtolower($key));
                ?>

                <li class="nav-item">
                    <a class="nav-link <?= ($x == 0) ? 'active' : '' ?>" id="<?= $id ?>-tab" data-toggle="tab"
                       href="#<?= $id ?>" role="tab"
                       aria-controls="<?= $id ?>" aria-selected="<?= ($x == 0) ? true : false ?>">
                        <?= $key ?>
                    </a>
                </li>

                <?php $x++; endforeach; ?>
        </ul>
        <div class="tab-content" id="myTabContent">
            <?php
            $x = 0;
            foreach ($campuri as $key => $camp):
                $id = str_replace(' ', '-', strtolower($key));
                $class = ($x == 0) ? "tab-pane fade show active" : "tab-pane fade";
                ?>

                <div class="<?= $class ?>" id="<?= $id ?>" role="tabpanel" aria-labelledby="<?= $id ?>-tab">

                    <div class="jobsearch-employer-box-sections">
                        <form id="form-<?= $id ?>" action="" method="POST">
                            <input type="hidden" name="limba" value="romana">
                            <table id="profil_companie">
                                <tr>
                                    <th style="display: none">Key</th>
                                    <th>Label actual (ro)</th>
                                    <th>Label nou (ro)</th>
                                    <th>Placeholder (ro)</th>
                                    <th>Tooltip (ro)</th>
                                    <th>Label EN</th>
                                </tr>

                                <?php
                                foreach ($camp as $field) :
                                    ?>
                                    <tr class="fields-row" data-field="<?= $field['key'] ?>">
                                        <td style="display: none"><?= $field['key'] ?></td>
                                        <td><?= $field['label'] ?></td>
                                        <td>
                                            <input type="text" name="<?= $field['key'] ?>[label]" value=""
                                                   placeholder="label nou in limba romana">
                                        </td>
                                        <td>
                                            <?php if (!in_array($field['type'], $no_placeholder)) : ?>
                                                <input type="text" name="<?= $field['key'] ?>[placeholder]" value=""
                                                       placeholder="placeholder in romana">
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <input type="text" name="<?= $field['key'] ?>[instructions]" value=""
                                                   placeholder="tooltip in romana">
                                        </td>
                                        <td>
                                            <input type="text" name="<?= $field['key'] ?>[engleza]" value=""
                                                   placeholder="label in engleza">
                                        </td>
                                    </tr>

                                    <?php
                                    if (count($field['sub_fields'])):
                                        foreach ($field['sub_fields'] as $sub_field): ?>
                                            <tr class="fields-row" data-field="<?= $field['key'] ?>">
                                                <td style="display: none"><?= $sub_field['key'] ?></td>
                                                <td><?= $sub_field['label'] ?></td>
                                                <td><input type="text" name="<?= $sub_field['key'] ?>[label]" value=""
                                                           placeholder="label nou in limba romana"></td>
                                                <td><input type="text" name="<?= $sub_field['key'] ?>[placeholder]" value=""
                                                           placeholder="placeholder in romana"></td>
                                                <td><input type="text" name="<?= $sub_field['key'] ?>[instructions]" value=""
                                                           placeholder="tooltip in romana"></td>
                                                <td><input type="text" name="<?= $sub_field['key'] ?>[engleza]" value=""
                                                           placeholder="label in engleza"></td>
                                            </tr>
                                        <?php endforeach;
                                    endif;
                                endforeach; ?>
                            </table>

                            <input type="submit" value="Salveaza modificarile">
                        </form>
                    </div>
                </div>

                <?php $x++; endforeach; ?>
        </div>

    </div>
</div>




<style>
    .subdomeniu {
        margin-left: 50px !important;
    }

    .subdomeniu_wrap {
        margin-left: 50px !important;
        border-left: 1px solid #000 !important;
    }

    input[type=text] {
        padding-left: 10px;
        width: 100%;
    }

    table {
        margin-top: 30px;
    }

    table, th, td {
        border: 1px solid gray;
        padding: 3px 10px;
    }

    th {
        background-color: #4c6d85;
        color: #fff;
        padding: 3px 10px;
    }

    input[type=submit] {
        border: 1px solid;
        border-color: #ccc #ccc #bbb;
        border-radius: 3px;
        background: #e6e6e6;
        color: rgba(0, 0, 0, 0.8);
        font-size: 12px;
        font-size: 0.75rem;
        line-height: 1;
        padding: 0.6em 1em 0.4em;
        background-color: orange;
        padding: 10px 20px;
        color: #fff;
        font-size: 14px;
        margin: auto;
        display: block;
        margin-bottom: 50px;
    }
    input[type=submit]:hover{
        background-color: #000;
    }

    .nav-tabs .nav-link.active {
        color: #000000;
        background-color: #fff;
        border-color: #dee2e6 #dee2e6 #fff;
        font-weight: bold;
    }
    .nav-tabs .nav-link {
        border: 1px solid transparent;
        border-top-left-radius: .25rem;
        border-top-right-radius: .25rem;
        color: #000;
    }


    input[type=text] {
        padding-left: 10px;
        width: 100%;
        font-size: 12px;
        color:#000;
    }

    ::-webkit-input-placeholder { /* Edge */
        color: #a5a5a5;
    }

    :-ms-input-placeholder { /* Internet Explorer 10-11 */
        color: #a5a5a5;
    }

    ::placeholder {
        color: #a5a5a5;
    }

</style>
<?php
get_footer();
?>
