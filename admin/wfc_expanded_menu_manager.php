<?php
    /*
    ===============================
    ===============================
    */
    // function to list all pages are that are not shortcuts
    // two steps 1 query for all shortcut pages
    // query for ALL pages but exclude the shortcut ones
    /**
    @TODO need to save the shortcut links in a better way.  right now the url is
    getting saved. but they will break if the permalink structure changes.
    === try saving the item object maybe ===
     */
    function get_shortcut_pages(){
        global $wpdb;
        $args        = array(
            'post_type'      => 'page',
            'posts_per_page' => -1,
            'meta_query'     => array(
                array(
                    'key' => 'wfc_page_shortcut_url',
                )
            )
        );
        $query       = new WP_Query($args);
        $exclude_arr = array();
        if( $query->have_posts() ) :  while( $query->have_posts() ) : $query->the_post();
            $exclude_arr[] = get_the_ID();
        endwhile; else:
            //die('no pages');
        endif;
        wp_reset_query();
        return $exclude_arr;
    }

    function get_all_pages(){
        global $pagenow;
        if( $pagenow == 'media.php' || $pagenow == 'media-new.php' || $pagenow == 'async-upload.php' ){
            return '';
        }
        $exclude = get_shortcut_pages();
        global $wpdb;
        $args  = array(
            'post__not_in'   => $exclude,
            'post_type'      => 'page',
            'posts_per_page' => -1,
            'post_status'    => 'publish',
            'orderby'        => 'title',
            'order'          => 'ASC'
        );
        $query = new WP_Query($args);
        $arr   = array();
        if( $query->have_posts() ) :  while( $query->have_posts() ) : $query->the_post();
            $arr[get_permalink()] = get_the_title();
        endwhile;endif;
        wp_reset_query();
        $pdf_args    = array(
            'post_type'      => 'attachment',
            'numberposts'    => NULL,
            'post_status'    => NULL,
            'post_mime_type' => 'application/pdf'
        );
        $attachments = get_posts( $pdf_args );
        if( $attachments ){
            foreach( $attachments as $attachment ){
                $arr[wp_get_attachment_url( $attachment->ID )] = apply_filters( 'the_title', $attachment->post_title );
            }
        }
        return $arr;
    }

    /*
    ===============================
    ===============================
    */
    $page_shortcut_args = array(
        'cpt'      => 'page' /* CPT Name */,
        'meta_box' => array(
            'handler'   => '_additional_page_short_cut_options',
            'title'     => 'Shortcut Page',
            'cpt'       => 'page',
            'new_boxes' => array(
                array(
                    'field_title' => 'Shortcut URL: ',
                    'type_of_box' => 'text',
                    'desc'        => 'Enter the destination link'
                ),
                array(
                    'field_title' => 'Existing Pages: ',
                    'type_of_box' => 'select',
                    'desc'        => 'Select page to set as shortcut link',
                    'options'     => get_all_pages(),
                ),
                array(
                    'field_title' => 'New Tab Option: ',
                    'type_of_box' => 'checkbox',
                    'options'     => array('new_tab' => 'Open in a new tab'),
                    'desc'        => 'Check if you want the link to open in a new tab.'
                ),
            ),
        ),
    );
    $page_shortcut                    = new wfc_meta_box_class($page_shortcut_args);
    $page_internal_linking_check_args = array(
        'meta_box' => array(
            'handler'   => '_additional_page_internal_linking_check',
            'title'     => 'WFC Internal Linking Check',
            'post_type' => 'page',
            'new_boxes' => array(
                array(
                    'field_title' => 'Pages that link to this page:',
                    'id'          => '_page_toggle_internal_link_check',
                    'type_of_box' => 'content',
                    'desc'        => scf_internal_link_checker()
                ),
            ),
        ),
    );
    //$page_internal_linking_check = new wfc_meta_box_class($page_internal_linking_check_args);
    function scf_internal_link_checker(){
        $post_permalink = get_permalink( $_GET['post'] );
        global $wpdb;
        $args           = array(
            'post_type'  => 'page',
            'meta_query' => array(
                array(
                    'key'     => 'wfc_page_shortcut_url',
                    'value'   => $post_permalink,
                    'compare' => '='
                )
            )
        );
        $query          = new WP_Query($args);
        $str_permalinks = '';
        $i              = 0;
        if( $query->have_posts() ) :  while( $query->have_posts() ) : $query->the_post();
            $i++;
            $str_permalinks .= get_permalink( get_the_ID() ).'<br />';
        endwhile;endif;
        wp_reset_query();
        //echo $str_permalinks
        return $str_permalinks.' '.$i;
    }

    /*
    ===============================
    MENU MANAGEMENT ADDITION
    ===============================
    */
    function scf_intercept_add_new_page(){
        if( !$_GET ){
            return;
        }
        if( $_GET['post_type'] == 'page' && $_GET['shortcut'] == 'true' ){
            global $_wp_post_type_features;
            if( isset($_wp_post_type_features['page']['editor']) ){
                unset($_wp_post_type_features['page']['editor']);
            }
        }
    }

    add_action( 'admin_init', 'scf_intercept_add_new_page' );
    /*
    =========================================
    APPEND EDIT URL WITH SHORTCUT PARAMETER
    =========================================
    */
    function scf_page_link( $link ){
        global $post;
        global $wpdb;
        $short_cut = get_post_meta( $post->ID, 'wfc_page_shortcut_url', true );
        if( isset($short_cut) && !empty($short_cut) ){
            $link = $link.'&post_type=page&shortcut=true';
        }
        return $link;
    }

    add_filter( 'get_edit_post_link', 'scf_page_link' );
    /*
    ===============================
    REGISTER MENU MANAGER JQUERY
    ===============================
    */
    function scf_load_menu_manager_js(){
        global $post;
        global $pagenow;
        if( $post->post_type == 'page' &&
            ($pagenow == 'post-new.php' || $pagenow == 'edit.php' || $pagenow == 'post.php')
        ){
            wp_register_script( 'scf.jquery.menu.manager', WFC_ADM_JS_URI.'/scf.jquery.menu.manager.js' );
            wp_enqueue_script( 'scf.jquery.menu.manager' );
        }
    }

    add_action( 'admin_enqueue_scripts', 'scf_load_menu_manager_js' );
    /*
    ===============================
    ADDS THUMBNAIL IMAGES TO ADMIN LIST VIEW FOR
       -CAMPAIGN
    ===============================
    */
    function scf_add_shortcut_column( $cols ){
        $cols['scf_page_order']    = __( 'Order' );
        $cols['scf_shortcut_link'] = __( 'Shortcut Link' );
        return $cols;
    }

    add_filter( 'manage_page_posts_columns', 'scf_add_shortcut_column', 5 );
    function scf_display_shortcut_column( $col, $id ){
        global $post;
        $post_type = get_post_type( $id );
        switch( $col ){
            case 'scf_page_order':
                echo $post->menu_order;
                break;
            case 'scf_shortcut_link':
                echo get_post_meta( $post->ID, 'wfc_page_shortcut_url', true );
                break;
        }
    }

    add_action( 'manage_page_posts_custom_column', 'scf_display_shortcut_column', 5, 2 );