<?php
/**
 *
 * @package scf-framework
 * @author Steve (6/11/2012)
 * @version 2.2
 */
?><!DOCTYPE html>
<!--
 __      __     _____   ______
/\ \  __/\ \  /\  ___\ /\  _  \
\ \ \/\ \ \ \ \ \ \__/ \ \ \/\_\
 \ \ \ \ \ \ \ \ \  __\ \ \ \/ /_
  \ \ \_/\ _\ \ \ \ \_/  \ \ \_\ \
   \ \__/ \___/  \ \_\    \ \____/
    \/__/ /__/    \/_/     \/___/
-->
<!--[if lt IE 7 ]><html class="ie ie6" <?php language_attributes(); ?>>
    <![endif]-->
<!--[if IE 7 ]><html class="ie ie7" <?php language_attributes(); ?>>
    <![endif]-->
<!--[if IE 8 ]><html class="ie ie8" <?php language_attributes(); ?>>
    <![endif]-->
<!--[if (gte IE 9)|!(IE)]><!--><html <?php language_attributes(); ?>> <!--<![endif]-->
   <head>
      <meta charset="<?php bloginfo( 'charset' ); ?>" />
      <title><?php bloginfo('name'); ?> <?php wp_title("",true); ?></title>
      <link rel="profile" href="http://gmpg.org/xfn/11" />
      <link rel="stylesheet" type="text/css" media="all" href="<?php bloginfo( 'stylesheet_url' ); ?>" />
      <link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />
      <?php wp_head(); ?>
   </head>
   <body <?php body_class(); ?>>
      <!-- Primary Page Layout -->
      <div id="container">
         <div id="nav-menu">
            <nav>
               <?php
                  wp_nav_menu(
                     array(
                        'theme_location' => 'Primary',
                        'menu_id' => 'menu-custom',
                        'menu_class' => 'menu',
                        'depth' => 0
                        )
                     );
               ?>
            </nav>
         </div><!--//#nav-menu-->
