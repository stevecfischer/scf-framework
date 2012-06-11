<?php
/**
 *
 * @package scf-framework
 * @author Steve (6/11/2012)
 * @version 0.1
 */

/*
===============================
CREATE CAMPAIGN POST TYPE FOR SLIDESHOW
===============================
*/
$campaign_module_args = array(
   'cpt' => 'Campaign', 'menu_name' => 'Campaign',
   'tax' => array( array('tax_label' => 'steve', 'menu_name' => 'sssss')
      ),
   'supports' => array('title', 'page-attributes', 'thumbnail'),
   );
$campaign_module = new wfcfw($campaign_module_args);

/*
===============================
CREATE IMAGE POOL FOR SUB PAGES TO USE
===============================
*/
$subpage_banner_args = array(
   'cpt' => 'Subpage Banner', 'menu_name' => 'Subpage Img Pool',
   'supports' => array('title','thumbnail'),
   );
$subpage_banner = new wfcfw($subpage_banner_args);
