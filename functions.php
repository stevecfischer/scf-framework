<?php
/**
 *
 * @package scf-framework
 * @author Steve (6/11/2012)
 * @version 2.2
 */
require_once('wfc_config/wfc_config.php');

/***********************/
/*
=== Add theme specific functions below.
=== If you feel you need to edit the framework files consult a manager first.
*/


function god(){
   print_r(current_filter());
   echo '<br />';
}
//add_action('all', 'god');


