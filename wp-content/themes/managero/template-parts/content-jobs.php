<?php
/**
 * Template part for displaying page content in page.php
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package managero
 */

global $jobsearch_plugin_options;

?>

<section id="joburi">

    <div class="container">
        <div class="row">
            <div class="col-sm-3" id="filterWrap">
				<?php get_template_part('template-parts/filters', 'jobs'); ?>

            </div>
            <div class="col-sm-9">
                <div class="filter-wrapper-box" style="display:none;">
                    <div class="row">
                        <div id="filter-choises" class="filter-choises-1">
                            <h4 class="title-filter-box">Filtre aplicate</h4>
                            <div class="choice-locatie" style="display: none">
                                <div class="name"> Locatie:</div>
                                <div  class="rezultat">
                                </div>

                            </div>
                            <div class="choice-nivel" style="display:none;">
                                <div class="name"> Nivel:</div>
                                <div class="rezultat">

                                </div>
                            </div>
                            <div class="choice-domeniu" style="display: none">
                                <div class="name"> Domeniu:</div>
                                <div class="rezultat">

                                </div>
                            </div>
                            <div class="choice-salariu" style="display:none">
                                <div class="name"> Salariu:</div>
                                <div class="rezultat">
                                </div>
                            </div>
                            <a href="javascript:location.reload()">Reseteaza filtre</a>
                        </div>
                    </div>
                </div>
                <div class="col-sm-12" id="filter-results">

                    <div class="">

						<?php

						$args = [
							'post_type' => 'job',
							'posts_per_page' => 20,
							'order' => 'DESC'
						];


                        $jobs = new WP_Query($args);
                        $job_count = 0;
						if ($jobs->have_posts()):while ($jobs->have_posts()):$jobs->the_post();

						    // get job id
							$job_id = get_the_ID();
							$post_id = $job_id;

                            // get job user
							$companie_id = get_the_author_meta('ID');

                            // get user post
                            $employer_id = jobsearch_get_user_employer_id($companie_id);

                            // returneaza date job
                            $date_job = get_post_meta($post_id,'job_data_set', true);

                            // returneza id imagine
							$logo = $date_job['imagine_profil_companie'];
							// returneaza url imagine
							$logo = wp_get_attachment_url($logo);

							// date companie
							$companie = $date_job['nume_companie'];
							$extindere = $date_job['extindere'];
							$adresa2 = $date_job['origine'][1];
							$angajati = $date_job['numar_angajati'];

                             // titlu job
							$job = $date_job['post_job'];

							$nivel = $date_job['pozitia_ierarhica'];

							// locatie job
							$adresa4 = $date_job['locatia_jobului'][1];

							// nivel ierarhic
                            $subordonat = $date_job['cui_este_subordonat'];
                            $subordonati = $date_job['subordonati_total'];
							$nivelierarhic = $date_job['nivelul_ierarhic'];

							// salariu oferit job
							$salariu = $date_job['salariu_oferit_job'];

							// domenii job
							$domenii = $date_job['domeniu'];

							$listaDomenii = '';

							foreach ($domenii as $id) {
                            $domeniu = get_term_by('id', $id, 'sector');
								$listaDomenii .= $domeniu->name . ', ';
							}

							$listaDomenii = substr($listaDomenii, 0, -2);



							// work in progress
							$application_deadline = get_post_meta($post_id, 'jobsearch_field_job_application_deadline_date', true);
							$jobsearch_job_posted = get_post_meta($post_id, 'jobsearch_field_job_publish_date', true);
							$current_date = strtotime(current_time('d-m-Y H:i:s'));

							$job_employer_id = $companie_id;

							$user_id = get_current_user_id();
							$user_is_candidate = jobsearch_user_is_candidate($user_id);

							if ($application_deadline != '' && $application_deadline <= $current_date) {
								continue;
							}

							if ($user_is_candidate) {

								$candidate_id = jobsearch_get_user_candidate_id($user_id);
								$candidate_rej_jobs_list = get_post_meta($candidate_id, 'jobsearch_rej_jobs_list', true);
								$candidate_fav_jobs_list = get_post_meta($candidate_id, 'jobsearch_fav_jobs_list', true);
								$candidate_apd_jobs_list = get_post_meta($candidate_id, 'jobsearch_apd_jobs_list', true);

								if ($candidate_rej_jobs_list != '') {
									$candidate_rej_jobs_list = explode(',', $candidate_rej_jobs_list);
									if (in_array($job_id, $candidate_rej_jobs_list)) {
										continue;
									}
								}

								if ($candidate_apd_jobs_list != '') {
									$candidate_apd_jobs_list = explode(',', $candidate_apd_jobs_list);
									if (in_array($job_id, $candidate_apd_jobs_list)) {
										continue;
									}
								}

								if ($candidate_fav_jobs_list != '') {
									$candidate_fav_jobs_list = explode(',', $candidate_fav_jobs_list);
									if (in_array($job_id, $candidate_fav_jobs_list)) {
										continue;
									}
								}

							}


							if (is_user_logged_in()) {
								$finded_result_list = jobsearch_find_index_user_meta_list($job_id, 'jobsearch-user-jobs-applied-list', 'post_id', jobsearch_get_user_id());
								if (is_array($finded_result_list) && !empty($finded_result_list)) {
									continue;
								}
							}

							$job_count++;

							?>

                            <div class="col-12 jobslist-job" data-job="<?= $job_id; ?>">
                                <div class="row custom-height">
                                    <div class="col-sm-2">
                                        <div class="wrapper-logo-company">
                                            <img src="<?= $logo; ?>"/>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="company-name">
                                            <a href="<?php the_permalink(); ?>"><h2><?= $companie ?>
                                                </h2>
                                                <h3 class="job-position"><?= $job; ?></h3></a>
                                        </div>
                                    </div>
                                    <div class="col-sm-4">

										<?php
										$current_user = wp_get_current_user();
										$user_id = get_current_user_id();
										$user_obj = get_user_by('ID', $user_id);

										$employer_id = jobsearch_get_user_employer_id($user_id);
										if ($employer_id > 0):

										else:
											the_apply_button($job_id, $application_deadline, $job_employer_id);
										endif;
										?>

										<?php
										if ($job_type_str != '') {
											echo force_balance_tags($job_type_str);
										}
										?>

                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-2">
                                        <div class="wrapper-company-details">
											<?php esc_html_e('Detalii companie', 'managero'); ?>
                                        </div>
                                    </div>
                                    <div class="col-sm-7">
                                        <div class="wrapper-text-details-company">
                                            <b>Orgine</b>: <?= $adresa2 ?>
                                            |  <b>Extindere</b>: <?= $extindere; ?>
                                            |  <b>Nr angajati</b>: <?= $angajati; ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-2">
                                        <div class="wrapper-company-details">
											<?php esc_html_e('Detalii job', 'managero'); ?>
                                        </div>
                                    </div>
                                    <div class="col-sm-7">
                                        <div class="wrapper-text-details-company">
                                            <b>Locatie</b>: <?= $adresa4; ?> | <b>Domeniu de activitate</b>:  <?= $listaDomenii; ?><br>
                                            <b>Nivel ierarhic</b>: <?= $nivelierarhic; ?> | <b>Subordonat lui</b>: <?= $subordonat; ?><br>
                                            <b>Subordonati</b>:  <?= $subordonati; ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-2">
                                        <div class="wrapper-company-details">
											<?php esc_html_e('Salariu', 'managero'); ?>
                                        </div>
                                    </div>
                                    <div class="col-sm-7">
                                        <div class="wrapper-text-details-company">
											<?= $salariu; ?> &euro;
                                        </div>
                                    </div>
                                </div>
                            </div>


						<?php endwhile; endif; ?>
                    </div>
                </div>
            </div>


        </div>
    </div>


</section><!-- .no-results -->

<script>
    jQuery('.nojobs').html('<?= $job_count ?>');
</script>


<script>
    var x, i, j, selElmnt, a, b, c;
    /* Look for any elements with the class "custom-select": */
    x = document.getElementsByClassName("custom-select2");
    for (i = 0; i < x.length; i++) {
        selElmnt = x[i].getElementsByTagName("select")[0];
        /* For each element, create a new DIV that will act as the selected item: */
        a = document.createElement("DIV");
        a.setAttribute("class", "select-selected");
        a.innerHTML = selElmnt.options[selElmnt.selectedIndex].innerHTML;
        x[i].appendChild(a);
        /* For each element, create a new DIV that will contain the option list: */
        b = document.createElement("DIV");
        b.setAttribute("class", "select-items select-hide");
        for (j = 1; j < selElmnt.length; j++) {
            /* For each option in the original select element,
			create a new DIV that will act as an option item: */
            c = document.createElement("DIV");
            c.innerHTML = selElmnt.options[j].innerHTML;
            c.addEventListener("click", function (e) {
                /* When an item is clicked, update the original select box,
				and the selected item: */
                var y, i, k, s, h;
                s = this.parentNode.parentNode.getElementsByTagName("select")[0];
                h = this.parentNode.previousSibling;
                for (i = 0; i < s.length; i++) {
                    if (s.options[i].innerHTML == this.innerHTML) {
                        s.selectedIndex = i;
                        h.innerHTML = this.innerHTML;
                        y = this.parentNode.getElementsByClassName("same-as-selected");
                        for (k = 0; k < y.length; k++) {
                            y[k].removeAttribute("class");
                        }
                        this.setAttribute("class", "same-as-selected");
                        break;
                    }
                }
                h.click();
            });
            b.appendChild(c);
        }
        x[i].appendChild(b);
        a.addEventListener("click", function (e) {
            /* When the select box is clicked, close any other select boxes,
			and open/close the current select box: */
            e.stopPropagation();
            closeAllSelect(this);
            this.nextSibling.classList.toggle("select-hide");
            this.classList.toggle("select-arrow-active");
        });
    }

    function closeAllSelect(elmnt) {
        /* A function that will close all select boxes in the document,
		except the current select box: */
        var x, y, i, arrNo = [];
        x = document.getElementsByClassName("select-items");
        y = document.getElementsByClassName("select-selected");
        for (i = 0; i < y.length; i++) {
            if (elmnt == y[i]) {
                arrNo.push(i)
            } else {
                y[i].classList.remove("select-arrow-active");
            }
        }
        for (i = 0; i < x.length; i++) {
            if (arrNo.indexOf(i)) {
                x[i].classList.add("select-hide");
            }
        }
    }

    /* If the user clicks anywhere outside the select box,
	then close all select boxes: */
    document.addEventListener("click", closeAllSelect)
</script>
<script>
    //for login popup
    jQuery(document).on('click', '.jobsearch-sendmessage-popup-btn', function () {
        jobsearch_modal_popup_open('JobSearchModalSendMessage');
    });
    jQuery(document).on('click', '.jobsearch-sendmessage-messsage-popup-btn', function () {
        jobsearch_modal_popup_open('JobSearchModalSendMessageWarning');
    });
    jQuery(document).on('click', '.jobsearch-applyjob-msg-popup-btn', function () {
        jobsearch_modal_popup_open('JobSearchModalApplyJobWarning');
    });

</script>