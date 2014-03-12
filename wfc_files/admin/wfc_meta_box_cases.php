<?php

    /**
     *
     * @package scf-framework
     * @author Steve (6/11/2012)
     * @version 2.2
     */
    class  wfc_meta_box_class
    {
        private $new_meta_boxes = array();
        private $_cpt = "";

        public function __construct( $obj ){
            $this->_cpt                                                                           = str_replace( ' ', '', strtolower( $obj['cpt'] ) );
            $this->new_meta_boxes[$this->clean_handles( $obj['meta_box']['title'], $this->_cpt )] = $obj;
            add_action( 'add_meta_boxes', array(&$this, 'register_meta_box') );
            add_action( 'save_post', array(&$this, 'save_meta_box'), 10, 2 );
        }

        public function clean_handles( $term, $cpt, $id = true ){
            $field_id_cleaning = preg_replace( "/[^A-Za-z0-9 ]/", '', trim( $term ) );
            $field_id_cleaning = strtolower( str_replace( " ", "_", $field_id_cleaning ) );
            if( $id === true ){
                return 'wfc_'.$cpt.'_'.$field_id_cleaning;
            } else{
                return 'wfc-'.$cpt.'-'.$field_id_cleaning;
            }
        }

        public function types_meta_box( $var ){
            global $post;
            wp_nonce_field( 'wfc_meta_box_nonce', 'meta_box_nonce' );
            if( is_array( $var['meta_box']['new_boxes'] ) ){
                foreach( $var['meta_box']['new_boxes'] as $field ){
                    // = preg_replace( "/[^A-Za-z0-9 ]/", '', trim( $field['field_title'] ) );
                    //$field_id_cleaning = strtolower( str_replace( " ", "_", $field_id_cleaning ) );
                    /*if($field['type_of_box'] == 'text_repeater'){
                        foreach($field['options'] as $column){
                            echo '<br />';
                            echo $this->clean_handles($column, $var['cpt']);
                            echo '<br />';
                        }
                    }else{
                        echo $this->clean_handles($field['field_title'], $var['cpt']);
                        echo $this->clean_handles($field['field_title'], $var['cpt'],false);

                    }*/
                    $field['id']    = $this->clean_handles( $field['field_title'], $var['cpt'] );
                    $field['class'] = $this->clean_handles( $field['field_title'], $var['cpt'], false );
                    $field['desc']  = empty($field['desc']) ? '' : $field['desc'];
                    if( $field['type_of_box'] == 'wysiwyg' ){
                        $meta = get_post_meta( $post->ID, strtolower( $field['id'] ), true );
                    } else{
                        $meta = get_post_meta( $post->ID, $field['id'], true );
                    }
                    if( empty($meta) ){
                        $field['options'] = empty($field['options']) ? array() : $field['options'];
                        $meta             = is_array( $field['options'] ) ? array() : '';
                    }
                    echo '
                  <div id="'.$field['id'].'" class="wfc-meta-block wfc-input-'.$field['type_of_box'].'">
                  <p class="wfc-meta-label">
                     <strong>'.$field['field_title'].'</strong>
                  </p>';
                    if( $field['desc'] != '' ){
                        echo '
                     <div class="description-wrap">
                     <a class="switch" href="#">[+] more info</a>
                     <p class="description">'.$field['desc'].'</p>
                     </div>';
                    }
                    echo '<p class="add_margin wfc-add-margin"> </p>';
                    switch( $field['type_of_box'] ){
                        case 'text':
                            echo '<input class="'.$field['class'].'" type="text" name="'.$field['id'].'" value="'.
                                ($meta ? $meta : '').
                                '"  />';
                            break;
                        case 'textarea':
                            echo '<textarea cols="40" rows="2" name="'.$field['id'].'">'.($meta ? $meta : '').
                                '</textarea>';
                            break;
                        case 'select':
                            echo '
                        <select name="'.$field['id'].'" id="'.$field['id'].'">
                           <option value="none" >None</option>';
                            foreach( $field['options'] as $option_k => $option_v ){
                                // @scftodo: make this better.  I needed this if statement to fix the shortcut module
                                if( $field['id'] == 'wfc_page_existing_pdfs' || $field['id'] == 'wfc_page_existing_pages' ){
                                    $val = $option_k;
                                } else{
                                    $val = is_int( $option_k ) ? $option_v : $option_k;
                                }
                                echo '<option value="'.$val.'" '.($val == $meta ? ' selected="selected"' : '').' >'.
                                    $option_v.'</option>';
                            }
                            echo '</select>';
                            break;
                        case 'radio':
                            foreach( $field['options'] as $option ){
                                echo '
                        <label>
                           <input type="radio" name="'.$field['id'].'" value="'.$option.'" '.
                                    ($option == $meta ? ' checked="checked"' : '').' />&nbsp;'
                                    .$option.'
                        </label><br />';
                            }
                            break;
                        case 'text_repeater':
                            $columns       = count( $field['options'] );
                            $plus_one_html = '<span class="wfc-text-repeater wfc-plus-one-col .col-xs-4">';
                            $plus_one_html .= '<a class="wfc-text-repeater wfc-plus-one">&nbsp;</a>';
                            $plus_one_html .= '</span>';
                            $minus_one_html = '<span class="wfc-text-repeater wfc-minus-one-col .col-xs-4">';
                            $minus_one_html .= '<a class="wfc-text-repeater wfc-minus-one">&nbsp;</a>';
                            $row_open  = '<span class="wfc-text-repeater wfc-text-repeater-row wfc-row-%d .col-xs-4">';
                            $col_open  = '<span class="wfc-text-repeater wfc-text-repeater-col-%d .col-xs-4">';
                            $grid_open = '<span class="wfc-meta-field-grid">';
                            $close_tag = '</span>';
                            foreach( $field['options'] as $option ){
                                $meta_array[$option] = get_post_meta( $post->ID, $this->clean_handles( $option, $var['cpt'] ), true );
                                $rows                = count( $meta_array[$option] );
                            }
                            echo $grid_open;
                            for( $i = 0; $i < $rows; $i++ ){
                                if( $this->validate_repeater_row( $i, $field['options'], $meta_array ) ){
                                    //at least one field has a value so display the row
                                    echo sprintf( $row_open, ($i + 1) );
                                    echo $plus_one_html;
                                    foreach( $meta_array as $meta_k => $meta_v ){
                                        echo sprintf( $col_open, ($i + 1) );
                                        echo '<input class="'.$field['class'].
                                            '" type="text" name="'.$this->clean_handles( $meta_k, $var['cpt'] ).'[]" value="'.
                                            ($meta_v[$i] ? $meta_v[$i] : '').
                                            '"  />';
                                        echo $close_tag;
                                    }
                                    echo $minus_one_html.$close_tag;
                                    echo $close_tag;
                                    //print_r($meta_array[$keys[1]]);
                                    //print_r($meta_array[$keys[2]]);
                                }
                            }
                            echo $close_tag;
                            break;
                        case 'checkbox':
                            foreach( $field['options'] as $option ){
                                echo '
                        <label>
                           <input type="checkbox" class="wfc-metabox-checkbox" name="'.$field['id'].'[]" value="'.$option.'" '.
                                    (in_array( $option, $meta ) ? ' checked="checked"' : '').' />&nbsp;'
                                    .$option.'
                        </label><br />';
                            }
                            break;
                        case 'wysiwyg':
                            $args = array("textarea_name" => strtolower( $field['id'] ).'[]');
                            wp_editor( $meta[0] ? $meta[0] : '', strtolower( $field['id'] ).'[]', $args );
                            break;
                        default:
                            echo $field['options'];
                            break;
                    }
                    echo '</div>';
                }
            }
        }

        public function validate_repeater_row( $row_index, $meta_keys_array, $meta_vals_array ){
            foreach( $meta_keys_array as $meta_key ){
                if( !empty($meta_vals_array[$meta_key][$row_index]) ){
                    return true;
                }
            }
            return false;
        }

        //EOF
        public function register_meta_box(){
            $vars = $this->new_meta_boxes;
            foreach( $vars as $var ){
                $filtered_handle = $this->clean_handles( $var['meta_box']['title'], $this->_cpt );
                add_meta_box(
                    $filtered_handle.'_metabox',
                    $var['meta_box']['title'],
                    array(&$this, 'display_meta_box_content'),
                    $var['cpt'],
                    'advanced',
                    'high'
                );
            }
        }

        //EOF
        public function display_meta_box_content( $post_obj ){
            $vars              = $this->new_meta_boxes;
            $current_post_type = $post_obj->post_type;
            foreach( $vars as $var ){
                if( str_replace( ' ', '', strtolower( $var['cpt'] ) ) == $current_post_type ){
                    $meta_box = $var['meta_box']['new_boxes'];
                    $this->types_meta_box( $var );
                }
            }
        }

        //EOF
        public function save_meta_box(){
            global $post;
            //print_r($this->_cpt);
            if( $post->post_type != $this->_cpt ){
                return false;
            }
            if( !is_object( $post ) ){
                return false;
            }
            $post_id = $post->ID;
            $vars    = $this->new_meta_boxes;
            //print_r($vars['wfc_page_subpage_banner_options']['cpt']);
            foreach( $vars as $var ){
                $meta_box = $var['meta_box'];
            }
            // verify nonce
            if( !isset($_POST['meta_box_nonce']) || !wp_verify_nonce( $_POST['meta_box_nonce'], 'wfc_meta_box_nonce' )
            ){
                return $post_id;
            }
            // custom meta boxes are immune to auto saving for some reason
            if( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ){
                return $post_id;
            }
            // check permissions
            if( !current_user_can( 'edit_page', $post_id ) ){
                return $post_id;
            }
            print_r( $_POST );
            foreach( $meta_box['new_boxes'] as $field ){
                print_r( $field );
                $field['id']         = $this->clean_handles( $field['field_title'], $var['cpt'] );
                $old                 = get_post_meta( $post_id, $field['id'], true );
                $trim_fields         = preg_replace( '/\[\]/', '', $field['id'] );
                $_POST[$trim_fields] = empty($_POST[$trim_fields]) ? array() : $_POST[$trim_fields];
                $new                 = $_POST[$trim_fields];
                if( $new && $new != $old && $field['type_of_box'] != 'checkbox' && $field['type_of_box'] != 'wysiwyg' ){
                    update_post_meta( $post_id, $field['id'], $new );
                } elseif( $field['type_of_box'] == 'checkbox' ){
                    update_post_meta( $post_id, $field['id'], $_POST[$field['id']] );
                } elseif( $field['type_of_box'] == 'text_repeater' ){
                    /*
                     * @scftodo: I'm saving these the wrong way. they should be save by row Ex. name - email - phone. NOT name - name - name; email-email-email...
                     * @scftodo: but if i change the way it saves update the the output too.
                     */
                    foreach( $field['options'] as $option ){
                        echo $this->clean_handles( $option, $var['cpt'] );
                        update_post_meta( $post_id, $this->clean_handles( $option, $var['cpt'] ), $_POST[$this->clean_handles( $option, $var['cpt'] )] );
                    }
                    update_post_meta( $post_id, 'wfc_Provider_key', $_POST['wfc_Provider_key'] );
                } elseif( $field['type_of_box'] == 'wysiwyg' ){
                    update_post_meta( $post_id, strtolower( $field['id'] ), $_POST[strtolower( $field['id'] )] );
                } elseif( ('' == $new || empty($new)) && $old ){
                    delete_post_meta( $post_id, $field['id'], $old );
                }
            }
            //die();
        }
    }

    //EOC
