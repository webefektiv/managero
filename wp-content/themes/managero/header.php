<?php
/**
 * The header for our theme
 *
 * This is the template that displays all of the <head> section and everything up until <div id="content">
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package managero
 */

?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="profile" href="https://gmpg.org/xfn/11">
	<?php wp_head(); ?>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css"
          integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.5.0/css/all.css"
          integrity="sha384-B4dIYHKNBt8Bc12p+WXckhzcICo0wtJAoU8YZTY5qE0Id1GSseTk6S+L3BlXeVIU" crossorigin="anonymous">
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <link rel="stylesheet" type="text/css" href="//cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css"/>
    <script type="text/javascript" src="//cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js"></script>
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.1/css/all.css"
          integrity="sha384-50oBUHEmvpQ+1lW4y57PTFmhCaXp0ML5d60M1M7uH2+nqUivzIebhndOJK28anvf" crossorigin="anonymous">
    <!--    <script src="https://code.jquery.com/jquery-3.3.1.min.js" crossorigin="anonymous"></script>-->


</head>
<style>
   #wpadminbar{
       display:none;
   }
</style>


<body <?php body_class(); ?>>
<div id="page" class="site">
    <a class="skip-link screen-reader-text" href="#content"><?php esc_html_e( 'Skip to content', 'managero' ); ?></a>

    <header id="masthead" class="site-header">
        <div class="container">
            <div class="topbar">
                <div class="wrap-top-menu">
                    <ul class="menuWrapUser">
						<?php
						$user_id        = get_current_user_id();
						$user_obj       = get_user_by( 'ID', $user_id );
						$candidate_id   = jobsearch_get_user_candidate_id( $user_id );
						$employer_id    = jobsearch_get_user_employer_id( $user_id );
						$user_nice_name = $user_obj->data->user_nicename;

						if ( ! is_user_logged_in() ): ?>
                            <a href="<?php if ( ! current_user_can( 'administrator' ) ): echo "/user-dashboard/"; else: echo "/wp-admin/"; endif; ?>">
								<?php esc_html_e( 'Inregistrare', 'managero' ); ?>
                            </a>
						<?php else: ?>
                            <li class="menuItem">
                                <a class="menuI" href="<?php if ( ! current_user_can( 'administrator' ) ): echo "/user-dashboard/"; else: echo "/wp-admin/"; endif; ?>">
									<?php esc_html_e( 'Dashboard', 'managero' ); ?>
                                </a>
								<?php if ( $candidate_id ): ?>
                                    <ul class="meniuUser">

                                        <li class="menuLink">
                                            <a href="<?php echo home_url() . '/user-dashboard/?tab=profil-candidat'; ?>">
                                                Profil candidat
                                            </a>
                                        </li>
                                        <li class="menuLink">
                                            <a href="<?php echo home_url() . '/user-dashboard/?tab=autoevaluare'; ?>">
                                               Autoevaluare
                                            </a>
                                        </li>
                                        <li class="menuLink">
                                            <a href="<?php echo home_url() . '/user-dashboard/?tab=all-jobs'; ?>">
                                                Lista joburi
                                            </a>
                                        </li>

                                        <li class="menuLink">
                                            <a href="<?php echo home_url() . '/user-dashboard/?tab=aplicatii'; ?>">
                                                Aplicatiile mele
                                            </a>
                                        </li>


                                        <li class="menuLink">
                                            <a href="<?php echo home_url() . '/user-dashboard/?tab=texte'; ?>">
                                                Texte predefinite
                                            </a>
                                        </li>

                                        <li class="menuLink">
                                            <a href="<?php echo home_url() . '/user-dashboard/?tab=fisiere'; ?>">
                                                Fisiere candidat
                                            </a>
                                        </li>

                                        <li class="menuLink">
                                            <a href="<?php echo home_url() . '/user-dashboard/?tab=alerte-joburi'; ?>">
                                                Alerte si filtre
                                            </a>
                                        </li>
                                        <li class="menuLink">
                                            <a href="<?php echo home_url() . '/user-dashboard/?tab=setari-cont'; ?>">
                                                Setari cont
                                            </a>
                                        </li>
                                    </ul>
								<?php elseif ( $employer_id ): ?>
                                    <ul class="meniuUser">

                                        <li class="menuLink">
                                            <a href="<?php echo home_url() . '/user-dashboard/?tab=profil-companie'; ?>">
                                                Profil companie
                                            </a>
                                        </li>

                                        <li class="menuLink">
                                            <a href="<?php echo home_url() . '/user-dashboard/?tab=lista-joburi'; ?>">
                                               Anunturile mele
                                            </a>
                                        </li>

                                        <li class="menuLink">
                                            <a href="<?php echo home_url() . '/user-dashboard/?tab=job-template'; ?>">
                                                Template job
                                            </a>
                                        </li>

                                        <li class="menuLink">
                                            <a href="<?php echo home_url() . '/user-dashboard/?tab=template-anunt'; ?>">
                                                Template Anunt
                                            </a>
                                        </li>

                                        <li class="menuLink">
                                            <a href="<?php echo home_url() . '/user-dashboard/?tab=file-manager'; ?>">
                                                Fisierele companiei
                                            </a>
                                        </li>

                                        <li class="menuLink">
                                            <a href="<?php echo home_url() . '/user-dashboard/?tab=adauga-job'; ?>">
                                               Adauga job
                                            </a>
                                        </li>

                                        <li class="menuLink">
                                            <a href="<?php echo home_url() . '/user-dashboard/?tab=setari-cont'; ?>">
                                              Setari cont
                                            </a>
                                        </li>




                                    </ul>
								<?php endif; ?>
                            </li>
						<?php endif; ?>
                    </ul>
					<?php if ( ! is_user_logged_in() ):
						echo '<a href="/user-login/"> Login</a>';
					else :
						$url = wp_logout_url( home_url( '/' ) );
						echo "<a href='$url'>Logout</a>";
					endif; ?>
                </div>

                <div class="welcome">
					<?php if (is_user_logged_in() ):
						echo "welcome, $user_nice_name";
					endif;
					?>

                </div>
            </div>
            <div class="topbar-2">
                <div class="logo-wrap">
                    <div class="site-branding">
						<?php // the_custom_logo(); ?>
                        <a href="<?= home_url(); ?>">
                            Managero
                        </a>

                    </div>
                </div>
                <div class="main-menu-wrap">
                    <nav id="site-navigation" class="main-navigation">
                        <button class="menu-toggle" aria-controls="primary-menu"
                                aria-expanded="false"><?php esc_html_e( 'Primary Menu', 'managero' ); ?></button>
						<?php
						wp_nav_menu( array(
							'theme_location' => 'menu-1',
							'menu_id'        => 'primary-menu',
						) );
						?>
                    </nav>
                </div>
 
            </div>
        </div>


    </header><!-- #masthead -->

    <div id="content" class="site-content">
