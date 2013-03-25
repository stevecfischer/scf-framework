<?php
    /**
     *
     * @package scf-framework
     * @author Steve (9/02/2012)
     * @version 2.2
     */
    class wfc_pointers_class
    {

        private $_current_help_boxes = true;

        function __construct(){
            add_action( 'admin_enqueue_scripts', array(&$this, 'enqueue') );
            add_action( 'admin_init', array(&$this, 'wfc_get_pointer_status') );
            add_action( 'show_user_profile', array(&$this, 'wfc_pointers_add_user_profile_fields') );
            add_action( 'edit_user_profile', array(&$this, 'wfc_pointers_add_user_profile_fields') );
            add_action( 'personal_options_update', array(&$this, 'wfc_pointers_save_user_profile_fields') );
            add_action( 'edit_user_profile_update', array(&$this, 'wfc_pointers_save_user_profile_fields') );
        }

        function wfc_pointers_add_user_profile_fields( $user ){
            ?>
        <h3><?php _e( 'Extra Profile Information', 'your_textdomain' ); ?></h3>
        <table class="form-table">
            <tr>
                <th>
                    <label for="address">WFC Help Boxes</label></th>
                <td>
                    <input type="checkbox" name="wfctogglepointers" value="wfc-pointers" <?php echo
                    get_the_author_meta( 'wfctogglepointers', $user->ID ) == 'wfc-pointers' ? ' checked="checked" ' :
                        ''; ?> />
                    <span class="description">Check box to toggle the display of help boxes.</span>
                </td>
            </tr>
        </table>
        <?php
        }

        function wfc_pointers_save_user_profile_fields( $user_id ){
            if( !current_user_can( 'edit_user', $user_id ) ){
                return FALSE;
            }
            update_user_meta( $user_id, 'wfctogglepointers', $_POST['wfctogglepointers'] );
        }

        function wfc_get_pointer_status(){
            if( get_the_author_meta( 'wfctogglepointers', get_current_user_id() ) ){
                $this->_current_help_boxes = true;
            } else{
                $this->_current_help_boxes = false;
            }
        }

        function enqueue(){
            if( $this->_current_help_boxes ){
                wp_enqueue_style( 'wp-pointer' );
                wp_enqueue_script( 'wp-pointer' );
                add_action( 'admin_print_footer_scripts', array(&$this, 'print_scripts') );
            }
        }

        function print_scripts(){
            global $pagenow, $current_user;
            $ad         = 'scf-framework'; //$ad = 'Admin Directory'
            $adminpages = array(
                '/'.$ad.'/wp-admin/'                                         => array(
                    'content'   => '<h3>WFC First</h3><p>Dashboard</p>',
                    'button2'   => 'Next',
                    'anchor'    => '#menu-dashboard',
                    'function2' => 'window.location="'.admin_url( '/profile.php' ).'";'
                ),
                '/'.$ad.'/wp-admin/profile.php'                              => array(
                    'content'   => '<h3>WFC Second</h3><p>User Profile: Change password, email, bio</p>',
                    'button2'   => 'Next',
                    'anchor'    => '#menu-users',
                    'function2' => 'window.location="'.admin_url( '/edit.php?post_type=page' ).'";'
                ),
                '/'.$ad.'/wp-admin/edit.php?post_type=page'                  => array(
                    'content'   => '<h3>WFC Forth</h3><p>Edit Pages</p>',
                    'button2'   => 'Next',
                    'anchor'    => '#menu-pages',
                    'function2' => 'window.location="'.admin_url( '/post-new.php?post_type=page#title' ).'";'
                ),
                '/'.$ad.'/wp-admin/post-new.php?post_type=page#title'        => array(
                    'content'           => '<h3>WFC 7</h3><p>Enter title for page</p>',
                    'anchor'            => '#titlewrap #title',
                    'button2'           => 'Next',
                    'function2'         => "$('#titlewrap #title').pointer('close');$('#postdivrich').pointer('open');",
                    'position_at_edge'  => 'top',
                    'position_at_align' => 'center'
                ),
                '/'.$ad.'/wp-admin/post-new.php?post_type=page#postdivrich'  => array(
                    'content'           => '<h3>WFC 7</h3><p>Enter Content on your page.</p>',
                    'anchor'            => '#postdivrich',
                    'button2'           => 'Next',
                    'function2'         => "$('#postdivrich').pointer('close');$('#postimagediv').pointer('open');",
                    'position_at_edge'  => 'top',
                    'position_at_align' => 'center'
                ),
                '/'.$ad.'/wp-admin/post-new.php?post_type=page#postimagediv' => array(
                    'content'           => '<h3>WFC 5</h3><p>Upload a new image</p>',
                    'anchor'            => '#postimagediv',
                    'button2'           => 'Next',
                    'function2'         => "$('#postimagediv').pointer('close');$('#publish').pointer('open');",
                    'position_at_edge'  => 'bottom',
                    'position_at_align' => 'left'
                ),
                '/'.$ad.'/wp-admin/post-new.php?post_type=page#publish'      => array(
                    'content'           => '<h3>WFC 6</h3><p>When you are done click Publish here. If you want to continue working on this page and do not want it visible to the public click the "Save Draft" above. </p>',
                    'anchor'            => '#publish',
                    'button2'           => 'Finished',
                    'function2'         => "$('#publish').pointer('close');",
                    'position_at_edge'  => 'right',
                    'position_at_align' => 'center'
                ),
            );
            $page = '';
            $page = $_SERVER['REQUEST_URI'];
            if( 'admin.php' != $pagenow || !array_key_exists( $page, $adminpages ) ){
                /**
                 * add loop so a page can have multiple boxes
                 */
                $counter = 0;
                foreach( $adminpages as $k => $adminpage ){
                    $a = explode( "#", $k );
                    if( '' != $page && $page == $a[0] ){
                        $anchor            = $adminpage['anchor'];
                        $id                = 'wpseo_content_top'.$a[1];
                        $content           = $adminpage['content'];
                        $position_at_edge  =
                            isset($adminpage['position_at_edge']) ? $adminpage['position_at_edge'] : 'left';
                        $position_at_align =
                            isset($adminpage['position_at_align']) ? $adminpage['position_at_align'] : 'right';
                        $button2           = $adminpage['button2'];
                        $button2_function  = $adminpage['function2'];
                        $this->print_buttons(
                            $anchor,
                            $id,
                            $content,
                            $position_at_edge,
                            $position_at_align,
                            $button2,
                            $button2_function,
                            $counter
                        );
                        $counter++;
                    }
                }
            }
        }

        function admin_head(){
            ?>
        <style type="text/css" media="screen">
            #pointer-primary, #tour-close{
                margin :0 5px 0 0;
            }
        </style>
        <?php
        }

        function print_buttons(
            $anchor,
            $id,
            $content,
            $position_at_edge,
            $position_at_align,
            $button2 = false,
            $button2_function = "t.element.pointer('close');",
            $counter
        ){
            ?>
        <script type="text/javascript">
            //<![CDATA[
            jQuery(function ($) {
                $('<?php echo $anchor;?>').pointer({
                    content :'<?php echo addslashes( $content ); ?>',
                    position:{
                        edge :'<?php echo $position_at_edge; ?>',
                        align:'<?php echo $position_at_align; ?>'
                    },
                    buttons :function (event, t) {
                        button = jQuery('<a id="pointer-close" class="button-secondary"></a><a id="pointer-primary" class="button-primary">Close</a>');
                        button.bind('click.pointer', function () {
                            <?php echo $button2_function; ?>
                        });
                        return button;
                    },
                    close   :function () {
                        // Once the close button is hit
                    }
                })<?php

                if( $counter > 0 ){
                    echo ".pointer('close');";
                } else{
                    echo ".pointer('open');";
                }

                ?>
                $('#titlewrap #title').pointer('open');

                jQuery('#wp-pointer-<?php echo $counter;?> #pointer-primary')
         .bind( 'click.pointer', function(event, t) {
             $.pointer('close');
      });

            });
            //]]>
        </script>
        <?php
        }
    }