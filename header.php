<?php
/**
 * The header for our theme
 *
 * This is the template that displays all of the <head> section and everything up until <div id="content">
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package Custome_Theme
 */

?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="profile" href="https://gmpg.org/xfn/11">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.2/css/all.css" integrity="sha384-oS3vJWv+0UjzBfQzYUhtDYW+Pj2yciDJxpsK1OYPAYjqT085Qq/1cq5FLXAZQ7Ay" crossorigin="anonymous">

    <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<div id="page" class="site">
    <header id="masthead" class="site-header">
        <div class="top-bar">
            <div class="container">
                    <div class="left-top-bar">
                        <p class="top-bar-p">
                            <a class="top-bar-a" href="#"><i class="fab fa-facebook-f"></i></a>
                            <a class="top-bar-a" href="#"><i class="fab fa-twitter"></i></a>
                            <a class="top-bar-a" href="#"><i class="fab fa-instagram"></i></a>
                            <a class="top-bar-a" href="#"><i class="fab fa-pinterest"></i></a>
                            <a class="top-bar-a" href="#"><i class="fas fa-heart"></i></a>
                        </p>
                    </div>
                    <div class="right-top-bar">
                        <p class="top-bar-p-text">cauta reteta sau articol</p>
                        <span class="top-bar-seach" href="#"><i class="fas fa-search"></i></span>

                    </div>
            </div>
        </div>
        <div class="container">
            <div class="row">
                <div class="col-12 col-md-12">
                    <nav id="site-navigation" class="main-navigation">
                        <button class="menu-toggle" aria-controls="primary-menu"
                                aria-expanded="false"><?php esc_html_e('Primary Menu', 'custome-theme'); ?></button>
                        <?php
                        wp_nav_menu(array(
                            'theme_location' => 'menu-1',
                            'menu_id' => 'primary-menu',
                        ));
                        ?>
                    </nav><!-- #site-navigation -->


                </div>
            </div>
        </div>
        <?php if(is_page('homepage')): ?>

        <div id="slider-home">
            <?php

            $imagini_slider = get_field('slider-home');
            foreach ($imagini_slider as $imagine):
                ?>

                    <img src="<?php echo $imagine['url']; ?>"/>

            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </header><!-- #masthead -->
    <script>
        jQuery('#slider-home').slick({
            dots: false,
            infinite: true,
            speed: 800,
            centerMode: true,
            adaptiveHeight: false,
            slidesToShow: 1,
            arrows: false,

        });
    </script>


    <div id="content" class="site-content">
