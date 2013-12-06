<?php
    /**
     * Class to manage shortcuts
     * Creating an instance of this class will enable the shortcut feature
     *
     * Supports 3 types of shortcuts, see $shortcut_medias
     *
     * @author Thibault Miclo
     * @version 1.1
     * @package wfc-framework
     * @subpackage wfc-admin
     * @since 5.2
     */
    class shortcutManager
    {
        /**
         * List of supported medias
         */
        protected $shortcut_medias = array(
            1 => 'Page',
            2 => 'External Link',
            3 => 'PDF'
        );

        /**
         * Constructor to initialize the shortcuts
         * No real code to explain there
         *
         * @access public
         */
        public function __construct(){
            add_action( 'admin_init', array($this, 'scf_intercept_add_new_page') );
            add_filter( 'get_edit_post_link', array($this, 'scf_page_link') );
            add_action( 'admin_enqueue_scripts', array($this, 'scf_load_menu_manager_js') );
            add_filter( 'manage_page_posts_columns', array($this, 'scf_add_shortcut_column'), 5 );
            add_action( 'manage_page_posts_custom_column', array($this, 'scf_display_shortcut_column'), 5, 2 );
            $this->build_shortcut_meta_box();
        }

        /**
         * Return all the pages with shortcut on it
         * To avoid display in the select
         *
         * @access private
         * @global $wpdb
         * @return array pages with a shortcut
         */
        private function get_shortcut_pages(){
            $args        = array(
                'post_type'  => 'page',
                'meta_query' => array(
                    array(
                        'key'     => 'wfc_page_type_shortcut',
                        'value'   => 1, //page
                        'compare' => '='
                    )
                )
            );
            $query       = new WP_Query($args);
            $exclude_arr = array();
            if( $query->have_posts() ) :  while( $query->have_posts() ) : $query->the_post();
                $exclude_arr[] = get_the_ID();
            endwhile;
            else:
                //die('no pages');
            endif;
            wp_reset_query();
            return $exclude_arr;
        }

        /**
         * Get the all the items with the post_type $type
         *
         * @access private
         *
         * @param int $type the post_type
         *
         * @return array all the needed items
         */
        private function get_all_by_type( $type ){
            $arr = array();
            switch( $type ){
                case 1: //Page
                    $exclude = $this->get_shortcut_pages();
                    $exclude =
                        array_merge( $exclude, array(@intval( $_REQUEST['post'] )) ); //Exclude edited page to avoid loops
                    $args    = array(
                        'post__not_in'   => $exclude,
                        'post_type'      => 'page',
                        'posts_per_page' => -1,
                        'post_status'    => 'publish',
                        'orderby'        => 'title',
                        'order'          => 'ASC'
                    );
                    $query   = new WP_Query($args);
                    if( $query->have_posts() ) :  while( $query->have_posts() ) : $query->the_post();
                        $arr[get_the_ID()] = get_the_title();
                    endwhile;endif;
                    break;
                case 2: //External
                    break;
                case 3: //PDF
                    $args        = array(
                        'post_type'      => 'attachment',
                        'numberposts'    => NULL,
                        'post_status'    => NULL,
                        'post_mime_type' => 'application/pdf'
                    );
                    $attachments = get_posts( $args );
                    if( $attachments ){
                        foreach( $attachments as $attachment ){
                            $arr[$attachment->ID] = apply_filters( 'the_title', $attachment->post_title );
                        }
                    }
                    break;
            }
            return $arr;
        }

        /**
         * Prepare arguments to build meta_box
         * To add shortcuts on page edition
         * Then build it
         *
         * @access private
         * @return true
         */
        private function build_shortcut_meta_box(){
            if( $this->pages_with_shortcut() === 'No shortcut' ){
                $page_shortcut_args = array(
                    'cpt'      => 'page' /* CPT Name */,
                    'meta_box' => array(
                        'handler'   => '_additional_page_short_cut_options',
                        'title'     => 'Shortcut Page',
                        'cpt'       => 'page',
                        'new_boxes' => array(
                            array(
                                'field_title' => 'Inbound Shortcuts',
                                'type_of_box' => 'whatever',
                                'desc'        => 'Show how many pages have a shortcut on this page, and which pages.',
                                'options'     => $this->pages_with_shortcut(),
                            ),
                            array(
                                'field_title' => 'Type shortcut',
                                'type_of_box' => 'select',
                                'desc'        => 'Select the type of media you want to shortcut link',
                                'options'     => $this->shortcut_medias,
                            ),
                            array(
                                'field_title' => 'Existing Pages: ',
                                'type_of_box' => 'select',
                                'desc'        => 'Select page to set as shortcut link',
                                'options'     => $this->get_all_by_type( 1 ),
                            ),
                            array(
                                'field_title' => 'External link: ',
                                'type_of_box' => 'text',
                                'desc'        => 'Enter the external link',
                            ),
                            array(
                                'field_title' => 'Existing PDFs: ',
                                'type_of_box' => 'select',
                                'desc'        => 'Select PDF to set as shortcut link',
                                'options'     => $this->get_all_by_type( 3 ),
                            ),
                            array(
                                'field_title' => 'New Tab Option: ',
                                'type_of_box' => 'checkbox',
                                'options'     => array('new_tab' => 'Open in a new tab'),
                                'desc'        => 'Check if you want the link to open in a new tab.'
                            )
                        )
                    )
                );
            } else{
                $page_shortcut_args = array(
                    'cpt'      => 'page' /* CPT Name */,
                    'meta_box' => array(
                        'handler'   => '_additional_page_short_cut_options',
                        'title'     => 'Shortcut Page',
                        'cpt'       => 'page',
                        'new_boxes' => array(
                            array(
                                'field_title' => 'Inbound Shortcuts',
                                'type_of_box' => 'whatever',
                                'desc'        => 'Show how many pages have a shortcut on this page, and which pages.',
                                'options'     => $this->pages_with_shortcut(),
                            )
                        )
                    )
                );
            }
            $page_shortcut            = new wfc_meta_box_class($page_shortcut_args);
            $attachment_shortcut_args = array(
                'cpt'      => 'attachment' /* CPT Name */,
                'meta_box' => array(
                    'handler'   => '_additional_page_short_cut_options',
                    'title'     => 'Shortcuts',
                    'cpt'       => 'attachment',
                    'new_boxes' => array(
                        array(
                            'field_title' => 'Inbound Shortcuts',
                            'type_of_box' => 'whatever',
                            'desc'        => 'Show how many pages have a shortcut on this attachment, and which pages.',
                            'options'     => $this->attachments_with_shortcut(),
                        )
                    )
                )
            );
            $attachment_shortcut      = new wfc_meta_box_class($attachment_shortcut_args);
            return true;
        }

        /**
         * Get all the shortcuts going to a page
         * When you edit a page which has shortcuts linked on it
         * Diplay them
         *
         * @access public
         * @return string $str string displayed on page edited
         */
        public function pages_with_shortcut(){
            $args           = array(
                'post_type'  => 'page',
                'meta_query' => array(
                    array(
                        'key'     => 'wfc_page_type_shortcut',
                        'value'   => 1, //page
                        'compare' => '='
                    ),
                    array(
                        'key'     => 'wfc_page_existing_pages',
                        'value'   => @intval( $_GET['post'] ), //page
                        'compare' => '='
                    )
                )
            );
            $query          = new WP_Query($args);
            $str_permalinks = '';
            $i              = 0;
            if( $query->have_posts() ) :  while( $query->have_posts() ) : $query->the_post();
                $i++;
                $str_permalinks .= get_the_title().'<br />';
            endwhile;endif;
            wp_reset_query();
            if( $i > 0 )
                return $i.' page'.($i > 1 ? 's' : '').' ha'.($i > 1 ? 've' : 's').' a shortcut to this page :<br />'.
                $str_permalinks;
            else{
                return 'No shortcut';
            }
        }

        /**
         * Get all the shortcuts going to an attachment
         * When you edit an attachment which has shortcuts linked on it
         * Diplay them
         *
         * @access public
         * @return string $str string displayed on attachment edited
         */
        public function attachments_with_shortcut(){
            $str_permalinks = '';
            $i              = 0;
            $args           = array(
                'post_type'  => 'page',
                'meta_query' => array(
                    'relation' => 'AND',
                    array(
                        'key'     => 'wfc_page_type_shortcut',
                        'value'   => 3, //PDF
                        'compare' => '='
                    ),
                    array(
                        'key'     => 'wfc_page_existing_pdfs',
                        'value'   => @intval( $_GET['post'] ),
                        'compare' => '='
                    )
                )
            );
            $query          = new WP_Query($args);
            if( $query->have_posts() ) :  while( $query->have_posts() ) : $query->the_post();
                $i++;
                $str_permalinks .= get_the_title().'<br />';
            endwhile;endif;
            wp_reset_query();
            return $i.' pages have a shortcut to this attachment :<br />'.$str_permalinks;
        }

        /**
         * Intercept add new page to remove wysiwyg editor
         *
         * @access public
         */
        public function scf_intercept_add_new_page(){
            if( !isset($_GET['post_type']) || !isset($_GET['shortcut']) ){
                return;
            }
            if( $_GET['post_type'] == 'page' && $_GET['shortcut'] == 'true' ){
                global $_wp_post_type_features;
                if( isset($_wp_post_type_features['page']['editor']) ){
                    unset($_wp_post_type_features['page']['editor']);
                }
            }
        }

        /**
         * Add $_GET['shortcut']=true into url
         * Set post type to page
         * $_GET['shortcut'] will be used and tested to display the wysiwyg editor or not
         *
         * @access public
         *
         * @param string $link old url
         *
         * @return string $link new url
         */
        public function scf_page_link( $link ){
            global $post;
            $short_cut = intval( get_post_meta( $post->ID, 'wfc_page_type_shortcut', true ) [0]);
            if( $short_cut != 'none' ){
                $link = $link.'&post_type=page&shortcut=true';
            }
            return $link;
        }

        /**
         * Load js scripts on the pages that require it
         *
         * @access public
         * @global $post
         * @global $pagenow
         */
        public function scf_load_menu_manager_js(){
            global $post;
            global $pagenow;
            if( !is_object( $post ) ){
                return;
            }
            if( $post->post_type == 'page' &&
                ($pagenow == 'post-new.php' || $pagenow == 'edit.php' || $pagenow == 'post.php')
            ){
                wp_register_script( 'scf.jquery.menu.manager', WFC_ADM_JS_URI.'/scf.jquery.menu.manager.js' );
                wp_enqueue_script( 'scf.jquery.menu.manager' );
            }
        }

        /**
         * Add an order & shortcut column in manage page page
         *
         * @access public
         *
         * @param array $cols columns before
         *
         * @return array $cols columns after
         */
        public function scf_add_shortcut_column( $cols ){
            $cols['scf_page_order']    = __( 'Order' );
            $cols['scf_shortcut_link'] = __( 'Shortcut Link' );
            return $cols;
        }

        /**
         * Display the content of the new columns
         *
         * @access public
         *
         * @param int $col column name
         * @param int $id post ID
         */
        public function scf_display_shortcut_column( $col, $id ){
            global $post;
            $post_type = get_post_type( $id );
            switch( $col ){
                case 'scf_page_order':
                    echo $post->menu_order;
                    break;
                case 'scf_shortcut_link':
                    $metas = get_post_meta( $post->ID );
                    if( !empty($metas['wfc_page_type_shortcut']) )
                        switch( $metas['wfc_page_type_shortcut'][0] ){
                            case 1:
                                if( $metas['wfc_page_existing_pages'][0] != 'none' )
                                    echo 'Page: '.get_the_title( $metas['wfc_page_existing_pages'][0] );
                                break;
                            case 2:
                                if( $metas['wfc_page_external_link'][0] != '' )
                                    echo 'Link: '.$metas['wfc_page_external_link'][0];
                                break;
                            case 3:
                                if( $metas['wfc_page_existing_pdfs'][0] != 'none' )
                                    echo 'PDF: '.get_the_title( $metas['wfc_page_existing_pdfs'][0] );
                                break;
                        }
                    break;
            }
        }
    }

    $shortcuts = new shortcutManager();