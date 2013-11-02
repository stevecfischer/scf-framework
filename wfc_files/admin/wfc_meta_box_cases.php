<?php
    /**
     * Class to build custom metabox easily
     * On the edit pages
     *
     * @package scf-framework
     * @author Steve (6/11/2012)
     */
    class  wfc_meta_box_class
    {
        /**
         * @var array
         */
        private $new_meta_boxes = array();
        /**
         * @var string
         */
        private $_cpt = "";

        /**
         * Constructor
         * Add all the necessary actions
         * And clean the parameters
         *
         * @param array $obj a metabox to build
         */
        public function __construct( $obj ){
            $this->_cpt                                                              = strtolower( $obj['cpt'] );
            $this->new_meta_boxes[$this->clean_handles( $obj['meta_box']['title'] )] = $obj;
            add_action( 'add_meta_boxes', array(&$this, 'register_meta_box') );
            add_action( 'save_post', array(&$this, 'save_meta_box'), 10, 2 );
        }

        /**
         * Clean parameters
         *
         * @param string $term term you want to clean
         *
         * @return string cleaned term with correct name
         */
        public function clean_handles( $term ){
            $field_id_cleaning = preg_replace( "/[^A-Za-z0-9 ]/", '', trim( $term ) );
            $field_id_cleaning = strtolower( str_replace( " ", "_", $field_id_cleaning ) );
            return 'wfc_'.$this->_cpt.'_'.$field_id_cleaning;
        }

        /**
         * Echo the correct HTML code for
         * each type of meta box
         *
         * @global $post
         *
         * @param array $var all the metabox options
         */
        public function types_meta_box( $var ){
            global $post;
            wp_nonce_field( 'wfc_meta_box_nonce', 'meta_box_nonce' );
            if( is_array( $var['meta_box']['new_boxes'] ) ){
                foreach( $var['meta_box']['new_boxes'] as $field ){
                    $field_id_cleaning = preg_replace( "/[^A-Za-z0-9 ]/", '', trim( $field['field_title'] ) );
                    $field_id_cleaning = strtolower( str_replace( " ", "_", $field_id_cleaning ) );
                    $field['id']       = 'wfc_'.$var['cpt'].'_'.$field_id_cleaning;
                    $field['class']    = 'wfc-'.$var['cpt'].'-'.$field_id_cleaning;
                    $field['desc']     = empty($field['desc']) ? '' : $field['desc'];
                    if( $field['type_of_box'] == 'wysiwyg' ){
                        $meta = get_post_meta( $post->ID, strtolower( $field['id'] ), true );
                    } else{
                        $meta = get_post_meta( $post->ID, $field['id'], true );
                    }
                    if( empty($meta) ){
                        $field['options'] = empty($field['options']) ? array() : $field['options'];
                        $meta             = is_array( $field['options'] ) ? array() : '';
                    }
                    echo '<div id="'.$field['id'].'" class="wfc-meta-block"><p class="wfc-meta-label"><strong>'.
                        $field['field_title'].'</strong></p>';
                    if( $field['desc'] != '' ){
                        echo
                            '<div class="description-wrap"><a class="switch" href="#">[+] more info</a><p class="description">'.
                            $field['desc'].'</p></div>';
                    }
                    echo '<p class="add_margin">';
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
                                $val = is_int( $option_k ) ? $option_v : $option_k;
                                echo '<option value="'.$val.'" '.($val == $meta ? ' selected="selected"' : '').' >'.
                                    $option_v.'</option>';
                            }
                            echo '</select>';
                            break;
                        case 'radio':
                            foreach( $field['options'] as $option ){
                                echo '
                        <label>
                           <input type="radio" name="'.$field['id'].'[]" value="'.$option.'" '.
                                    (in_array( $option, $meta ) ? ' checked="checked"' : '').' />&nbsp;'
                                    .$option.'
                        </label><br />';
                            }
                            break;
                        case 'checkbox':
                            foreach( $field['options'] as $option ){
                                echo '
                        <label>
                           <input type="checkbox" class="wfc-metabox-checkbox" name="'.$field['id'].'[]" value="'.
                                    $option.'" '.
                                    (in_array( $option, $meta ) ? ' checked="checked"' : '').' />&nbsp;'
                                    .$option.'
                        </label><br />';
                            }
                            break;
                        case 'wysiwyg':
                            $args = array("textarea_name" => strtolower( $field['id'] ).'[]');
                            wp_editor( $meta[0] ? $meta[0] : '', strtolower( $field['id'] ).'[]', $args );
                            //wp_editor( $meta ? $meta : '', $field['id'], isset( $field['options'] ) ? $field['options'] : array() );
                            break;
                    }
                    echo '</p></div>';
                }
            }
        }

        /**
         * Add the metabox in the edit page
         *
         */
        public function register_meta_box(){
            $vars = $this->new_meta_boxes;
            foreach( $vars as $var ){
                $filtered_handle = $this->clean_handles( $var['meta_box']['title'] );
                add_meta_box(
                    $filtered_handle.'_metabox',
                    $var['meta_box']['title'],
                    array(&$this, 'display_meta_box_content'),
                    $this->_cpt,
                    'advanced',
                    'high'
                );
            }
        }

        /**
         * Displays the content of each metaboxes added
         *
         * @param object $post_obj a wordpress post
         */
        public function display_meta_box_content( $post_obj ){
            $vars              = $this->new_meta_boxes;
            $current_post_type = $post_obj->post_type;
            foreach( $vars as $var ){
                if( strtolower( $var['cpt'] ) == $current_post_type ){
                    $meta_box = $var['meta_box']['new_boxes'];
                    $this->types_meta_box( $var );
                }
            }
        }

        /**
         * Saves the metabox content when you save the page
         *
         * @return integer $post_id id of the post
         * @return boolean false if saving fails, else true
         */
        public function save_meta_box(){
            global $post;
            if( !is_object( $post ) ){
                return;
            }
            $post_id = $post->ID;
            $vars    = $this->new_meta_boxes;
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
            foreach( $meta_box['new_boxes'] as $field ){
                $field_id_cleaning   = preg_replace( "/[^A-Za-z0-9 ]/", '', trim( $field['field_title'] ) );
                $field_id_cleaning   = strtolower( str_replace( " ", "_", $field_id_cleaning ) );
                $field['id']         = 'wfc_'.$var['cpt'].'_'.$field_id_cleaning;
                $old                 = get_post_meta( $post_id, $field['id'], true );
                $trim_fields         = preg_replace( '/\[\]/', '', $field['id'] );
                $_POST[$trim_fields] = empty($_POST[$trim_fields]) ? array() : $_POST[$trim_fields];
                $new                 = $_POST[$trim_fields];
                if( $new && $new != $old && $field['type_of_box'] != 'checkbox' && $field['type_of_box'] != 'wysiwyg' ){
                    update_post_meta( $post_id, $field['id'], $new );
                } elseif( $field['type_of_box'] == 'checkbox' ){
                    update_post_meta( $post_id, $field['id'], $_POST[$field['id']] );
                } elseif( $field['type_of_box'] == 'wysiwyg' ){
                    update_post_meta( $post_id, strtolower( $field['id'] ), $_POST[strtolower( $field['id'] )] );
                } elseif( ('' == $new || empty($new)) && $old ){
                    delete_post_meta( $post_id, $field['id'], $old );
                }
            }
        }
    }
