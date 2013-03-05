<?php
    /**
     *
     * @package scf-framework
     * @author Steve (08/16/2012)
     * @added since 2.3
     * @version 2.3
     */
    /*
    ===============================
    A TO Z INDEX TABS
    ===============================
    */
    function Wfc_a_to_z_tabs(){
        global $wpdb;
        $get_id         =
            $wpdb->get_results( "SELECT DISTINCT post_title,post_name,ID FROM $wpdb->posts WHERE post_status = 'publish' and post_type='page' order by trim(post_title) ASC" );

        $log_letters    = array();
        $log_num        = array();
        $return_tabs    = '';
        $return_tabs .= '<div id="az_tabs-wrapper">';
        $return_tabs .= '<ul id="az_tabs">';
        foreach( $get_id as $this_post ){
            $current_char = ucfirst( substr( trim( $this_post->post_title ), 0, 1 ) );
            if( is_numeric( $current_char ) ){
                if( !in_array( $current_char, $log_num ) ){
                    $log_num[] = $current_char;
                }
            }
            if( !is_numeric( $current_char ) ){
                if( !in_array( $current_char, $log_letters ) ){
                    $log_letters[] = $current_char;
                }
            }
        }
        $log_letters;
        if( !empty($log_num) ){
            $return_tabs .= '<li><a href="#" id="list_0">0-9</a></li>';
        }
        foreach( $log_letters as $log_letter ){
            $return_tabs .= '<li><a href="#" id="list_'.$log_letter.'">'.$log_letter.'</a></li>';
        }
        $return_tabs .= '</ul></div>';
        $return_pages = Wfc_a_to_z_pages($log_letters,$log_num,$get_id);
        return $return_tabs.$return_pages;
    }

    /*
    ===============================
    A TO Z INDEX PAGES
    ===============================
    */
    function Wfc_a_to_z_pages($lettersArr,$numsArr,$allPosts){
        global $wpdb;
        $log_letters    = array();
        $current_letter = '';
        $current_number = '';
        $open_list      = false;
        $column_flag    = 0;
        $counter        = 0;
        $return_pages   = '';
        $return_pages .= '<div id="atoz">';

        $get_page_titles = $wpdb->get_results(
            "SELECT post_title, post_name, ID
            FROM $wpdb->posts
            WHERE post_status = 'publish'
            AND post_type = 'page'
            GROUP BY trim(post_title)
            ORDER BY trim(post_title) ASC" );

        if( !empty($numsArr) ){
            $return_pages .= '<ul id="list_0" class="inactive">';
            foreach( $allPosts as $this_post ){
                $current_number = substr( trim( $this_post->post_title ), 0, 1 );
                if( is_numeric( $current_number ) ){
                    $return_pages .= '<li><a href="'.$this_post->post_title.'" >'.$this_post->post_title.'</a></li>';
                }
            }
            $return_pages .= '</ul><!-- //.list_0 -->';
        }
        foreach( $get_page_titles as $this_post ){
            $current_letter        = ucfirst(substr( trim( $this_post->post_title ), 0, 1 ));
            $get_page_letter_tally =
                $wpdb->get_results( "SELECT count(*) as tally FROM $wpdb->posts WHERE post_title LIKE '$current_letter%' AND post_status = 'publish' and post_type='page' order by trim(post_title) ASC" );

            $column_flag           = ceil( $get_page_letter_tally[0]->tally / 2 );
            if( !is_numeric( $current_letter ) ){
                if( !in_array( $current_letter, $log_letters ) ){
                    $log_letters[] = $current_letter;
                    if( $open_list == false ){
                        $open_list = true;
                    } else{
                        // this is where we close tags from the previous letter
                        $return_pages .= '</div></ul>';
                        $counter = 0;
                    }
                    $return_pages .= '<ul id="list_'.$current_letter.'" class="inactive"><div class="left-col">';
                }
                if( $counter == $column_flag ){
                    $return_pages .= '</div> <div class="right-col">';
                }
                $scfLink = Wfc_fix_atoz_url( $this_post->ID );
                $return_pages .= '<li><a href="'.$scfLink.'" >'.$this_post->post_title.'</a></li>';
                $counter++;
            }
        }
        $return_pages .= '</ul>';
        $return_pages .= '</div><!-- //#atoz -->';
        return $return_pages;
    }

    function Wfc_fix_atoz_url( $post_id ){
        $shortCutUrl = get_post_meta( $post_id, 'wfc_page_shortcut_url', true );
        if( $shortCutUrl == '' || empty($shortCutUrl) ){
            return get_permalink( $post_id );
        } else{
            return $shortCutUrl;
        }
    }

    function wfc_get_atozindex(){
        $return = '<div id="wfc-atoz">';
        $return .= Wfc_a_to_z_tabs();
        //$return .= Wfc_a_to_z_pages();
        $return .= '</div><!-- //#wfc-atoz -->';
        return $return;
    }

    add_shortcode( 'wfc_atozindex', 'wfc_get_atozindex' );