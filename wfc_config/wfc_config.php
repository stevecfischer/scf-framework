<?php
    /**
     *
     * @package scf-framework
     * @author Steve (6/11/2012)
     * @since 0.1
     */
    // WFC STANDARD FUNCTIONS //
    // ---------------------- //
    define('WFC_SITE_URL', get_bloginfo( 'url' ).'/');
    define('WFC_ADMIN_URL', admin_url());
    define('WFC_PT', dirname( __DIR__ ).'/');
    define('WFC_CONFIG', WFC_PT.'wfc_config');
    define('WFC_THEME_FUNCTIONS', WFC_PT.'theme_functions');
    define('WFC_WIDGETS', WFC_PT.'widgets');
    define('WFC_SHORTCODE', WFC_PT.'admin/shortcode');
    define('WFC_GLOBAL', WFC_PT.'admin/global');
    define('WFC_URI', get_template_directory_uri());
    define('WFC_ADM', WFC_PT.'admin');
    define('WFC_CSS_URI', WFC_URI.'/css');
    define('WFC_JS_URI', WFC_URI.'/js');
    define('WFC_IMG_URI', WFC_URI.'/images');
    define('WFC_ADM_CSS_URI', WFC_URI.'/admin/css');
    define('WFC_ADM_JS_URI', WFC_URI.'/admin/js');
    define('WFC_ADM_IMG_URI', WFC_URI.'/admin/images');
    /*
    ===============================
    TOGGLE STANDARD CPT's

     * @since 2.2
    ==============================
    */
    define('CAMPAIGN_CPT', 0);
    define('SUBPAGE_BANNER_CPT', 0);
    define('HOME_BOXES_CPT', 0);
    define('NEWS_CPT', 0);
    define('TESTIMONIAL_CPT', 0);
    /*
    ===============================
    MAIN INCLUDE FILE

     * @since 2.2
    ==============================
    */
    require_once(WFC_ADM.'/wfc_admin_config.php');