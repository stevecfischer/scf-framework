<?php
    /*
    ===============================
    WFC Plugin Protection

     * @since 3.0
    ===============================
    */
    if( is_admin() ) :
        class Wfc_Plugin_Seal
        {

            public $_user_broken_seal = false;
            public $_current_user;
            public $_wfc_seal_option;

            function Wfc_Plugin_Seal(){
                add_action( 'admin_init', array(&$this, 'Wfc_Admin_Init') );
                add_action( 'views_plugins', array(&$this, 'Wfc_Get_Seal_Form') );
            }

            function Wfc_Admin_Init(){
                global $current_user;
                $this->_current_user    = $current_user;
                $this->_wfc_seal_option = $this->Wfc_Read_Seal();
                $this->Wfc_Check_User_Broke_Seal();
                $wfc_plugin_seal         =
                    (isset($_POST['wfc_plugin_seal']) && $_POST['wfc_plugin_seal'] == 'true') ? true : false;
                $wfc_plugin_seal_confirm =
                    (isset($_POST['wfc_plugin_seal_confirm']) && $_POST['wfc_plugin_seal_confirm'] == 'letmein') ?
                        true : false;
                $valid_nonce             =
                    (isset($_POST['_wpnonce']) && wp_verify_nonce( $_POST['_wpnonce'], 'wfc_plugin_seal' )) ? true :
                        false;
                if( $wfc_plugin_seal && $wfc_plugin_seal_confirm && $valid_nonce ){
                    if( $current_user->user_login != 'admin' ){
                        $user = get_user_by( 'login', 'admin' );
                    }
                    if( !isset($user) || $user->user_level < 10 ){
                        $user = $current_user;
                    }
                    $this->Wfc_Update_Seal();
                    $this->Wfc_Email_Broken_Seal();
                    wp_redirect( admin_url().'plugins.php' );
                    exit;
                }
                $this->Wfc_Reset_Seal_Option();
            }

            public function Wfc_Read_Seal(){
                return get_option( 'wfc_plugin_seal' );
            }

            public function Wfc_Update_Seal(){
                $data[$this->_current_user->ID] = array(
                    'user' => $this->_current_user->data->user_email,
                    'time' => time()
                );
                update_option( 'wfc_plugin_seal', $data );
            }

            public function Wfc_Delete_Seal(){
                delete_option( 'wfc_plugin_seal' );
            }

            public function  Wfc_Check_User_Broke_Seal(){
                // option doesn't exist so show form
                if( !is_array( ($this->_wfc_seal_option) ) ){
                    return false;
                }
                return true;
            }

            private function Wfc_Reset_Seal_Option(){
                if( $_GET['reset_plugin_seal'] == 'reset' ){
                    $this->Wfc_Delete_Seal();
                    wp_redirect( admin_url().'plugins.php' );
                }
            }

            public function Wfc_Get_Seal_Form(){
                if( !$this->Wfc_Check_User_Broke_Seal() ){
                    ?>
                <h3>Plugin Disclaimer</h3>
                <p>Warning - Please contact Web Full Circle before upgrading, installing, or deleting any plugins. Changing settings on these pages may cause problems with your website's functionality.</p>
                <p>If you are confident you want to proceed without Web Full Circle Assistance please type
                    <strong>letmein</strong> in the confirmation field below. Then click submit and you'll have access to the plugins section.
                </p>
                <form id="wfc_plugin_seal" action="" method="post" autocomplete="off">
                    <?php wp_nonce_field( 'wfc_plugin_seal' ); ?>
                    <input id="wfc_plugin_seal" type="hidden" name="wfc_plugin_seal" value="true"/>
                    <input id="wfc_plugin_seal_confirm" type="text" name="wfc_plugin_seal_confirm" value=""/>
                    <p class="submit">
                        <input id="wfc_plugin_seal_submit" style="width: 80px;" type="submit" name="Submit" class="button-primary" value="Submit"/>
                    </p>
                </form>
                <?php
                    die();
                }
            }

            private function Wfc_Email_Broken_Seal(){
                $brokenUserData = $this->Wfc_Read_Seal();
                $message = '<html><body>';
                $message .= '<table rules="all" style="border-color: #666;" cellpadding="10">';
                $message .= '<tr><td>Plugin Seal Has been Broken</td><td></td></tr>';
                $headers[] = 'From: '.get_option( 'blogname' ).' <support@webfullcircle.com>';
                $headers[] = 'Cc: steve fischer <steve.fischer@webfullcircle.com>';
                $to        = 'stevecfischer@gmail.com';
                $subject   = 'Website Plugin Seal Broken';
                $message .= '<tr><td>Domain:</td><td> '.home_url().'</td></tr>';
                $message .= '<tr><td>User Id:</td><td> '.key( $brokenUserData ).'</td></tr>';
                $message .=
                    '<tr><td>User Email:</td><td> '.$brokenUserData[key( $brokenUserData )]['user'].'</td></tr>';
                $message .=
                    '<tr><td>Timestamp:</td><td> '.date( "Ymd", $brokenUserData[key( $brokenUserData )]['time'] ).
                        '</td></tr>';
                $message .= '</table></body></html>';
                add_filter( 'wp_mail_content_type', create_function( '$a', "return 'text/html';" ) );
                wp_mail( $to, $subject, $message, $headers );
            }
        }

        // Instantiate the class
        $WfcPluginSeal = new Wfc_Plugin_Seal();

        // End if for is_admin
    endif;