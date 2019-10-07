<aside class="jobsearch-column-2 jobsearch-typo-wrap" style="    display: none;">
	<div class="jobsearch-typo-wrap">
		<div class="jobsearch-employer-dashboard-nav">
			<ul>
				<?php
				if ($user_is_candidate) {
					ob_start();
					$dashmenu_links_cand = isset($jobsearch_plugin_options['cand_dashbord_menu']) ? $jobsearch_plugin_options['cand_dashbord_menu'] : '';
					?>
					<li<?php echo ($get_tab == '' ? ' class="active"' : '') ?>>
						<a href="<?php echo ($page_url) ?>">
							<i class="jobsearch-icon jobsearch-group"></i>
							<?php esc_html_e('Dashboard', 'wp-jobsearch') ?>
						</a>
					</li>
					<?php
					if (!empty($dashmenu_links_cand)) {
						foreach ($dashmenu_links_cand as $cand_menu_item => $cand_menu_item_switch) {
							if ($cand_menu_item == 'my_profile' && $cand_menu_item_switch == '1') {
								?>
								<li<?php echo ($get_tab == 'dashboard-settings' ? ' class="active"' : '') ?>>
									<a href="<?php echo add_query_arg(array('tab' => 'dashboard-settings'), $page_url) ?>">
										<i class="jobsearch-icon jobsearch-user"></i>
										<?php esc_html_e('My Profile', 'wp-jobsearch') ?>
									</a>
								</li>
								<?php
							} else if ($cand_menu_item == 'my_resume' && $cand_menu_item_switch == '1') {
								?>
								<li<?php echo ($get_tab == 'my-resume' ? ' class="active"' : '') ?>>
									<a href="<?php echo add_query_arg(array('tab' => 'my-resume'), $page_url) ?>">
										<i class="jobsearch-icon jobsearch-resume"></i>
										<?php esc_html_e('My Resume', 'wp-jobsearch') ?>
									</a>
								</li>
								<?php
							} else if ($cand_menu_item == 'fav_jobs' && $cand_menu_item_switch == '1') {
								?>
								<li<?php echo ($get_tab == 'favourite-jobs' ? ' class="active"' : '') ?>>
									<a href="<?php echo add_query_arg(array('tab' => 'favourite-jobs'), $page_url) ?>">
										<i class="jobsearch-icon jobsearch-heart"></i>
										<?php esc_html_e('Joburi pt aplicat', 'wp-jobsearch') ?>
									</a>
								</li>
                                <li<?php echo ($get_tab == 'reject-jobs' ? ' class="active"' : '') ?>>
                                    <a href="<?php echo add_query_arg(array('tab' => 'reject-jobs'), $page_url) ?>">
                                        <i class="jobsearch-icon jobsearch-heart"></i>
										<?php esc_html_e('Joburi neinteresante', 'wp-jobsearch') ?>
                                    </a>
                                </li>
								<?php
							} else if ($cand_menu_item == 'cv_manager' && $cand_menu_item_switch == '1') {
								?>
								<li<?php echo ($get_tab == 'cv-manager' ? ' class="active"' : '') ?>>
									<a href="<?php echo add_query_arg(array('tab' => 'cv-manager'), $page_url) ?>">
										<i class="jobsearch-icon jobsearch-id-card"></i>
										<?php esc_html_e('CV Manager', 'wp-jobsearch') ?>
									</a>
								</li>
                                <li<?php echo ($get_tab == 'text-manager' ? ' class="active"' : '') ?>>
                                    <a href="<?php echo add_query_arg(array('tab' => 'text-manager'), $page_url) ?>">
                                        <i class="jobsearch-icon jobsearch-id-card"></i>
										<?php esc_html_e('Texte Predefinite', 'wp-jobsearch') ?>
                                    </a>
                                </li>
								<?php
							} else if ($cand_menu_item == 'applied_jobs' && $cand_menu_item_switch == '1') {
								?>
								<li<?php echo ($get_tab == 'applied-jobs' ? ' class="active"' : '') ?>>
									<a href="<?php echo add_query_arg(array('tab' => 'applied-jobs'), $page_url) ?>">
										<i class="jobsearch-icon jobsearch-briefcase-1"></i>
										<?php esc_html_e('Applied Jobs', 'wp-jobsearch') ?>
									</a>
								</li>
								<?php
							} else if ($cand_menu_item == 'packages' && $cand_menu_item_switch == '1') {
								?>
								<li<?php echo ($get_tab == 'user-packages' ? ' class="active"' : '') ?>>
									<a href="<?php echo add_query_arg(array('tab' => 'user-packages'), $page_url) ?>">
										<i class="jobsearch-icon jobsearch-credit-card-1"></i>
										<?php esc_html_e('Packages', 'wp-jobsearch') ?>
									</a>
								</li>
								<?php
								if (class_exists('WC_Subscription')) {
									?>
									<li<?php echo ($get_tab == 'user-subscriptions' ? ' class="active"' : '') ?>>
										<a href="<?php echo add_query_arg(array('tab' => 'user-subscriptions'), $page_url) ?>">
											<i class="jobsearch-icon jobsearch-business"></i>
											<?php esc_html_e('Subscriptions', 'wp-jobsearch') ?>
										</a>
									</li>
									<?php
								}
							} else if ($cand_menu_item == 'transactions' && $cand_menu_item_switch == '1') {
								?>
								<li<?php echo ($get_tab == 'user-transactions' ? ' class="active"' : '') ?>>
									<a href="<?php echo add_query_arg(array('tab' => 'user-transactions'), $page_url) ?>">
										<i class="jobsearch-icon jobsearch-salary"></i>
										<?php esc_html_e('Transactions', 'wp-jobsearch') ?>
									</a>
								</li>
								<?php
							} else if ($cand_menu_item == 'change_password' && $cand_menu_item_switch == '1') {
								?>
								<li<?php echo ($get_tab == 'change-password' ? ' class="active"' : '') ?>>
									<a href="<?php echo add_query_arg(array('tab' => 'change-password'), $page_url) ?>">
										<i class="jobsearch-icon jobsearch-multimedia"></i>
										<?php esc_html_e('Change Password', 'wp-jobsearch') ?>
									</a>
								</li>
								<?php
							}
							echo apply_filters("jobsearch_cand_menudash_link_{$cand_menu_item}_item", '', $cand_menu_item, $get_tab, $page_url, $candidate_id);
						}
					} else {
						?>
						<li<?php echo ($get_tab == 'dashboard-settings' ? ' class="active"' : '') ?>>
							<a href="<?php echo add_query_arg(array('tab' => 'dashboard-settings'), $page_url) ?>">
								<i class="jobsearch-icon jobsearch-user"></i>
								<?php esc_html_e('My Profile', 'wp-jobsearch') ?>
							</a>
						</li>
						<li<?php echo ($get_tab == 'my-resume' ? ' class="active"' : '') ?>>
							<a href="<?php echo add_query_arg(array('tab' => 'my-resume'), $page_url) ?>">
								<i class="jobsearch-icon jobsearch-resume"></i>
								<?php esc_html_e('My Resume', 'wp-jobsearch') ?>
							</a>
						</li>
						<li<?php echo ($get_tab == 'applied-jobs' ? ' class="active"' : '') ?>>
							<a href="<?php echo add_query_arg(array('tab' => 'applied-jobs'), $page_url) ?>">
								<i class="jobsearch-icon jobsearch-briefcase-1"></i>
								<?php esc_html_e('Applied Jobs', 'wp-jobsearch') ?>
							</a>
						</li>
						<li<?php echo ($get_tab == 'cv-manager' ? ' class="active"' : '') ?>>
							<a href="<?php echo add_query_arg(array('tab' => 'cv-manager'), $page_url) ?>">
								<i class="jobsearch-icon jobsearch-id-card"></i>
								<?php esc_html_e('CV Manager', 'wp-jobsearch') ?>
							</a>
						</li>
						<li<?php echo ($get_tab == 'favourite-jobs' ? ' class="active"' : '') ?>>
							<a href="<?php echo add_query_arg(array('tab' => 'favourite-jobs'), $page_url) ?>">
								<i class="jobsearch-icon jobsearch-heart"></i>
								<?php esc_html_e('Favourite Jobs', 'wp-jobsearch') ?>
							</a>
						</li>
                        <li<?php echo ($get_tab == 'reject-jobs' ? ' class="active"' : '') ?>>
                            <a href="<?php echo add_query_arg(array('tab' => 'reject-jobs'), $page_url) ?>">
                                <i class="jobsearch-icon jobsearch-heart"></i>
								<?php esc_html_e('Joburi neinteresante', 'wp-jobsearch') ?>
                            </a>
                        </li>
						<?php
						ob_start();
						?>
						<li<?php echo ($get_tab == 'user-packages' ? ' class="active"' : '') ?>>
							<a href="<?php echo add_query_arg(array('tab' => 'user-packages'), $page_url) ?>">
								<i class="jobsearch-icon jobsearch-credit-card-1"></i>
								<?php esc_html_e('Packages', 'wp-jobsearch') ?>
							</a>
						</li>
						<?php
						if (class_exists('WC_Subscription')) {
							?>
							<li<?php echo ($get_tab == 'user-subscriptions' ? ' class="active"' : '') ?>>
								<a href="<?php echo add_query_arg(array('tab' => 'user-subscriptions'), $page_url) ?>">
									<i class="jobsearch-icon jobsearch-business"></i>
									<?php esc_html_e('Subscriptions', 'wp-jobsearch') ?>
								</a>
							</li>
							<?php
						}
						?>
						<li<?php echo ($get_tab == 'user-transactions' ? ' class="active"' : '') ?>>
							<a href="<?php echo add_query_arg(array('tab' => 'user-transactions'), $page_url) ?>">
								<i class="jobsearch-icon jobsearch-salary"></i>
								<?php esc_html_e('Transactions', 'wp-jobsearch') ?>
							</a>
						</li>
						<?php
						$pkgtrans_html = ob_get_clean();
						echo apply_filters('jobsearch_user_dash_links_pkgtrans_html', $pkgtrans_html, $get_tab, $page_url);
						?>
						<?php echo apply_filters('jobsearch_dashboard_menu_items_ext', '', $get_tab, $page_url) ?>
						<li<?php echo ($get_tab == 'change-password' ? ' class="active"' : '') ?>>
							<a href="<?php echo add_query_arg(array('tab' => 'change-password'), $page_url) ?>">
								<i class="jobsearch-icon jobsearch-multimedia"></i>
								<?php esc_html_e('Change Password', 'wp-jobsearch') ?>
							</a>
						</li>
						<?php
					}
					$menu_items_html = ob_get_clean();
					echo apply_filters('jobsearch_cand_dash_side_menulinks_html', $menu_items_html, $get_tab, $page_url, $candidate_id);
				}

				if (jobsearch_user_isemp_member($user_id)) {

					$membusr_perms = jobsearch_emp_accmember_perms($user_id);
					ob_start();
					?>
<!--					<li--><?php //echo ($get_tab == '' || $get_tab == 'dashboard-settings' ? ' class="active"' : '') ?><!-->-->
<!--						<a href="--><?php //echo ($page_url) ?><!--">-->
<!--							<i class="jobsearch-icon jobsearch-group"></i>-->
<!--							--><?php //esc_html_e('Dashboard', 'wp-jobsearch') ?>
<!--						</a>-->
<!--					</li>-->
					<?php

						if (is_array($membusr_perms) && in_array('u_manage_jobs', $membusr_perms)) {
						?>
                        <li<?php echo ($get_tab == 'manage-jobs' ? ' class="active"' : '') ?>>
                            <a href="<?php echo add_query_arg(array('tab' => 'manage-jobs'), $page_url) ?>">
                                <i class="jobsearch-icon jobsearch-briefcase-1"></i>
								<?php esc_html_e('Manage Jobs', 'wp-jobsearch') ?>
                            </a>
                        </li>
						<?php
					}


//					if (is_array($membusr_perms) && in_array('u_post_job', $membusr_perms)) {
//						?>
<!--						<li--><?php //echo ($get_tab == 'user-job' ? ' class="active"' : '') ?><!-->-->
<!--							<a href="--><?php //echo add_query_arg(array('tab' => 'user-job'), $page_url) ?><!--">-->
<!--								<i class="jobsearch-icon jobsearch-plus"></i>-->
<!--								--><?php //esc_html_e('Post a New Job', 'wp-jobsearch') ?>
<!--							</a>-->
<!--						</li>-->
<!--						--><?php
//					}

//					if (is_array($membusr_perms) && in_array('u_saved_cands', $membusr_perms)) {
//						?>
<!--						<li--><?php //echo ($get_tab == 'user-resumes' ? ' class="active"' : '') ?><!-->-->
<!--							<a href="--><?php //echo add_query_arg(array('tab' => 'user-resumes'), $page_url) ?><!--">-->
<!--								<i class="jobsearch-icon jobsearch-heart"></i>-->
<!--								--><?php //esc_html_e('Saved Candidates', 'wp-jobsearch') ?>
<!--							</a>-->
<!--						</li>-->
<!--						--><?php
//					}


//					if (is_array($membusr_perms) && in_array('u_packages', $membusr_perms)) {
//						?>
<!--						<li--><?php //echo ($get_tab == 'user-packages' ? ' class="active"' : '') ?><!-->-->
<!--							<a href="--><?php //echo add_query_arg(array('tab' => 'user-packages'), $page_url) ?><!--">-->
<!--								<i class="jobsearch-icon jobsearch-credit-card-1"></i>-->
<!--								--><?php //esc_html_e('Packages', 'wp-jobsearch') ?>
<!--							</a>-->
<!--						</li>-->
<!--						--><?php
//						if (class_exists('WC_Subscription')) {
//							?>
<!--							<li--><?php //echo ($get_tab == 'user-subscriptions' ? ' class="active"' : '') ?><!-->-->
<!--								<a href="--><?php //echo add_query_arg(array('tab' => 'user-subscriptions'), $page_url) ?><!--">-->
<!--									<i class="jobsearch-icon jobsearch-business"></i>-->
<!--									--><?php //esc_html_e('Subscriptions', 'wp-jobsearch') ?>
<!--								</a>-->
<!--							</li>-->
<!--							--><?php
//						}
//					}
//					if (is_array($membusr_perms) && in_array('u_transactions', $membusr_perms)) {
//						?>
<!--						<li--><?php //echo ($get_tab == 'user-transactions' ? ' class="active"' : '') ?><!-->-->
<!--							<a href="--><?php //echo add_query_arg(array('tab' => 'user-transactions'), $page_url) ?><!--">-->
<!--								<i class="jobsearch-icon jobsearch-salary"></i>-->
<!--								--><?php //esc_html_e('Transactions', 'wp-jobsearch') ?>
<!--							</a>-->
<!--						</li>-->
<!--						--><?php
//					}
//					?>
					<li<?php echo ($get_tab == 'change-password' ? ' class="active"' : '') ?>>
						<a href="<?php echo add_query_arg(array('tab' => 'change-password'), $page_url) ?>">
							<i class="jobsearch-icon jobsearch-multimedia"></i>
							<?php esc_html_e('Change Password', 'wp-jobsearch') ?>
						</a>
					</li>
					<?php
					$menu_items_html = ob_get_clean();
					echo apply_filters('jobsearch_emp_accmemb_dash_side_menulinks_html', $menu_items_html, $get_tab, $page_url);
				}
				if ($user_is_employer) {
					$dashmenu_links_emp = isset($jobsearch_plugin_options['emp_dashbord_menu']) ? $jobsearch_plugin_options['emp_dashbord_menu'] : '';
					ob_start();
					?>
<!--					<li--><?php //echo ($get_tab == '' ? ' class="active"' : '') ?><!-->
<!--						<a href="--><?php //echo ($page_url) ?><!--">-->
<!--							<i class="jobsearch-icon jobsearch-group"></i>-->
<!--							--><?php //esc_html_e('Dashboard', 'wp-jobsearch') ?>
<!--						</a>-->
<!--					</li>-->
					<?php
					if (!empty($dashmenu_links_emp)) {
						foreach ($dashmenu_links_emp as $emp_menu_item => $emp_menu_item_switch) {

							if ($emp_menu_item == 'manage_jobs' && $emp_menu_item_switch == '1') {
								?>
                                <li<?php echo ($get_tab == 'manage-jobs' ? ' class="active"' : '') ?>>
                                    <a href="<?php echo add_query_arg(array('tab' => 'manage-jobs'), $page_url) ?>">
                                        <i class="jobsearch-icon jobsearch-briefcase-1"></i>
										<?php esc_html_e('Manage Jobs', 'wp-jobsearch') ?>
                                    </a>
                                </li>
								<?php
							} else
							if ($emp_menu_item == 'company_profile' && $emp_menu_item_switch == '1') {
                                                                 ?>
                                <li<?php echo ($get_tab == 'dashboard-settings' ? ' class="active"' : '') ?>>
                                    <a href="<?php echo add_query_arg(array('tab' => 'dashboard-settings'), $page_url) ?>">
                                        <i class="jobsearch-icon jobsearch-user"></i>
										<?php esc_html_e('Company Profile', 'wp-jobsearch') ?>
                                    </a>
                                </li>
								<?php
							} else  if ($emp_menu_item == 'all_applicants' && $emp_menu_item_switch == '1') {
								?>
                                <li<?php echo ($get_tab == 'all-applicants' ? ' class="active"' : '') ?>>
                                    <a href="<?php echo add_query_arg(array('tab' => 'all-applicants'), $page_url) ?>">
                                        <i class="jobsearch-icon jobsearch-company-workers"></i>
										<?php esc_html_e('All Applicants', 'wp-jobsearch') ?>
                                    </a>
                                </li>
                                <li<?php echo ($get_tab == 'file-managero' ? ' class="active"' : '') ?>>
                                    <a href="<?php echo add_query_arg(array('tab' => 'file-manager'), $page_url) ?>">
                                        <i class="jobsearch-icon jobsearch-company-workers"></i>
										<?php esc_html_e('Manager Fisiere', 'wp-jobsearch') ?>
                                    </a>
                                </li>
								<?php
							} else if ($emp_menu_item == 'saved_candidates' && $emp_menu_item_switch == '1') {
								?>
<!--								<li--><?php //echo ($get_tab == 'user-resumes' ? ' class="active"' : '') ?><!-->
<!--									<a href="--><?php //echo add_query_arg(array('tab' => 'user-resumes'), $page_url) ?><!--">-->
<!--										<i class="jobsearch-icon jobsearch-heart"></i>-->
<!--										--><?php //esc_html_e('Saved Candidates', 'wp-jobsearch') ?>
<!--									</a>-->
<!--								</li>-->
								<?php
							}  else if ($emp_menu_item == 'change_password' && $emp_menu_item_switch == '1') {
								?>
								<li<?php echo ($get_tab == 'change-password' ? ' class="active"' : '') ?>>
									<a href="<?php echo add_query_arg(array('tab' => 'change-password'), $page_url) ?>">
										<i class="jobsearch-icon jobsearch-multimedia"></i>
										<?php esc_html_e('Change Password', 'wp-jobsearch') ?>
									</a>
								</li>
								<?php
							}
							echo apply_filters("jobsearch_emp_menudash_link_{$emp_menu_item}_item", '', $emp_menu_item, $get_tab, $page_url, $employer_id);
						}
					} else {
						?>

						<li<?php echo ($get_tab == 'dashboard-settings' ? ' class="active"' : '') ?>>
							<a href="<?php echo add_query_arg(array('tab' => 'dashboard-settings'), $page_url) ?>">
								<i class="jobsearch-icon jobsearch-user"></i>
								<?php esc_html_e('Company Profile', 'wp-jobsearch') ?>
							</a>
						</li>
						<li<?php echo ($get_tab == 'user-job' ? ' class="active"' : '') ?>>
							<a href="<?php echo add_query_arg(array('tab' => 'user-job'), $page_url) ?>">
								<i class="jobsearch-icon jobsearch-plus"></i>
								<?php esc_html_e('Post a New Job', 'wp-jobsearch') ?>
							</a>
						</li>
						<li<?php echo ($get_tab == 'manage-jobs' ? ' class="active"' : '') ?>>
							<a href="<?php echo add_query_arg(array('tab' => 'manage-jobs'), $page_url) ?>">
								<i class="jobsearch-icon jobsearch-briefcase-1"></i>
								<?php esc_html_e('Manage Jobs', 'wp-jobsearch') ?>
							</a>
						</li>
						<li<?php echo ($get_tab == 'all-applicants' ? ' class="active"' : '') ?>>
							<a href="<?php echo add_query_arg(array('tab' => 'all-applicants'), $page_url) ?>">
								<i class="jobsearch-icon jobsearch-company-workers"></i>
								<?php esc_html_e('All Applicants', 'wp-jobsearch') ?>
							</a>
						</li>
						<li<?php echo ($get_tab == 'user-resumes' ? ' class="active"' : '') ?>>
							<a href="<?php echo add_query_arg(array('tab' => 'user-resumes'), $page_url) ?>">
								<i class="jobsearch-icon jobsearch-heart"></i>
								<?php esc_html_e('Saved Candidates', 'wp-jobsearch') ?>
							</a>
						</li>
						<?php
						ob_start();
						?>
						<li<?php echo ($get_tab == 'user-packages' ? ' class="active"' : '') ?>>
							<a href="<?php echo add_query_arg(array('tab' => 'user-packages'), $page_url) ?>">
								<i class="jobsearch-icon jobsearch-credit-card-1"></i>
								<?php esc_html_e('Packages', 'wp-jobsearch') ?>
							</a>
						</li>
						<?php
						if (class_exists('WC_Subscription')) {
							?>
							<li<?php echo ($get_tab == 'user-subscriptions' ? ' class="active"' : '') ?>>
								<a href="<?php echo add_query_arg(array('tab' => 'user-subscriptions'), $page_url) ?>">
									<i class="jobsearch-icon jobsearch-business"></i>
									<?php esc_html_e('Subscriptions', 'wp-jobsearch') ?>
								</a>
							</li>
							<?php
						}
						?>
						<li<?php echo ($get_tab == 'user-transactions' ? ' class="active"' : '') ?>>
							<a href="<?php echo add_query_arg(array('tab' => 'user-transactions'), $page_url) ?>">
								<i class="jobsearch-icon jobsearch-salary"></i>
								<?php esc_html_e('Transactions', 'wp-jobsearch') ?>
							</a>
						</li>
						<?php
						$pkgtrans_html = ob_get_clean();
						echo apply_filters('jobsearch_user_dash_links_pkgtrans_html', $pkgtrans_html, $get_tab, $page_url);
						?>
						<?php echo apply_filters('jobsearch_dashboard_menu_items_ext', '', $get_tab, $page_url) ?>
						<li<?php echo ($get_tab == 'change-password' ? ' class="active"' : '') ?>>
							<a href="<?php echo add_query_arg(array('tab' => 'change-password'), $page_url) ?>">
								<i class="jobsearch-icon jobsearch-multimedia"></i>
								<?php esc_html_e('Change Password', 'wp-jobsearch') ?>
							</a>
						</li>
						<?php
					}
					$menu_items_html = ob_get_clean();
					echo apply_filters('jobsearch_emp_dash_side_menulinks_html', $menu_items_html, $get_tab, $page_url, $employer_id);
				}
				?>
				<li>
					<a href="<?php echo wp_logout_url(home_url('/')); ?>">
						<i class="jobsearch-icon jobsearch-logout"></i>
						<?php esc_html_e('Logout', 'wp-jobsearch') ?>
					</a>
				</li>
				<?php
				ob_start();
				?>
				<li class="profile-del-btnlink">
					<a class="jobsearch-userdel-profilebtn" href="javascript:void(0);"><i class="fa fa-trash-o"></i><?php esc_html_e('Delete Profile', 'wp-jobsearch') ?></a>
				</li>
				<?php
				$delbtn_html = ob_get_clean();
				echo apply_filters('jobsearch_user_dash_side_delprofile_btn', $delbtn_html);
				?>
			</ul>
			<?php
			$popup_args = array('p_user_type' => $user_type);
			add_action('wp_footer', function () use ($popup_args) {

				extract(shortcode_atts(array(
					'p_user_type' => '',
				), $popup_args));
				?>
				<div class="jobsearch-modal fade" id="JobSearchModalUserProfileDel">
					<div class="modal-inner-area">&nbsp;</div>
					<div class="modal-content-area">
						<div class="modal-box-area">
							<span class="modal-close"><i class="fa fa-times"></i></span>
							<div class="jobsearch-user-profiledel-pop">
								<p class="conf-msg"><?php esc_html_e('Are you sure! You want to delete your profile.', 'wp-jobsearch') ?></p>
								<p class="undone-msg"><?php esc_html_e('This can\'t be undone!', 'wp-jobsearch') ?></p>
								<div class="profile-del-con">
									<div class="pass-user-ara">
										<p><?php esc_html_e('Please enter your login Password to confirm', 'wp-jobsearch') ?>:</p>
										<input id="d_user_pass" type="password" placeholder="Password">
										<i class="jobsearch-icon jobsearch-multimedia"></i>
									</div>
									<div class="del-action-btns">
										<a class="jobsearch-userdel-profile" href="javascript:void(0);" data-type="<?php echo ($p_user_type) ?>"><?php esc_html_e('Delete Profile', 'wp-jobsearch') ?></a>
										<a class="jobsearch-userdel-cancel modal-close" href="javascript:void(0);"><?php esc_html_e('Cancel', 'wp-jobsearch') ?></a>
									</div>
									<span class="loader-con"></span>
									<span class="msge-con"></span>
								</div>
								<?php
								ob_start();
								jobsearch_terms_and_con_link_txt();
								$terms_html = ob_get_clean();
								echo apply_filters('jobsearch_dash_delprofile_terms_html', $terms_html);
								?>
							</div>
						</div>
					</div>
				</div>
				<?php
			}, 11, 1);
			?>
		</div>

	</div>
	<?php
	echo apply_filters('jobsearch_dash_aside_endext_html', '');
	?>
</aside>