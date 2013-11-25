<?php
    /**
    * Creates a disclamer with JS to prevent user modifications
    * in the plugins page
    *
    */
    function wfc_show_disclaimer(){
        ?>
        <script>
            jQuery(function ($) {
                $('.wfc-plugin-warning').dialog({autoOpen: true, modal: true});
                var updateNag = $(".update-nag");
                var wfcPluginWarning = $('<div class="wfc-plugin-warning"></div>');
                $(wfcPluginWarning).insertAfter(updateNag);
                $('.wfc-plugin-warning').text('Do not delete, update, or install plugins.');
            });
        </script>
    <?php
    }
    add_action( 'admin_footer-plugins.php', 'wfc_show_disclaimer' );

    /**
     * Class to manage the seal
     * Add a seal on the plugins page
     * It can be broke by the user and it sends an email to WFC
     */
    class Wfc_Plugin_Seal
    {
        /**
         * Constructor
         * Initialize the plugin
         */
        function Wfc_Plugin_Seal(){
            add_action( 'admin_init', array(&$this, 'Wfc_Admin_Init') );
            add_action( 'views_plugins', array(&$this, 'Wfc_Get_Seal_Form') );
            //add_filter( 'wp_mail', array( &$this, 'hijack_mail' ), 1 );
        }

        /**
         * Receive the form and do some verifications
         * Then update the seal status 
         * And send the email
         * 
         * @global $current_user
         */ 
        function Wfc_Admin_Init(){
            global $current_user;
            $wfc_plugin_seal         =
                (isset($_POST['wfc_plugin_seal']) && $_POST['wfc_plugin_seal'] == 'true') ? true : false;
            $wfc_plugin_seal_confirm =
                (isset($_POST['wfc_plugin_seal_confirm']) && $_POST['wfc_plugin_seal_confirm'] == 'letmein') ?
                    true : false;
            $valid_nonce             =
                (isset($_POST['_wpnonce']) && wp_verify_nonce( $_POST['_wpnonce'], 'wfc_plugin_seal' )) ? true :
                    false;
            if( $wfc_plugin_seal && $wfc_plugin_seal_confirm && $valid_nonce ){
                $wfc_plugin_seal_option = get_option( 'wfc_plugin_seal' );
                //print_r( $wfc_plugin_seal_option );
                if( $current_user->user_login != 'admin' ){
                    $user = get_user_by( 'login', 'admin' );
                }
                if( !isset($user) || $user->user_level < 10 ){
                    $user = $current_user;
                }
                //print_r($user);
                $this->Wfc_Update_Seal( $user );
                $this->Wfc_Email_Broken_Seal();
            }
        }

        /**
         * Creates the seal into the website options
         */
        public function Wfc_Create_Seal(){
            add_option( 'wfc_plugin_seal' );
        }
        /**
         * Gives the current state of the seal
         * - 0 if the seal is still in place
         * - >0, the id of the user who broke the seal
         * 
         * @return intger $state seal's state
         */
        public function Wfc_Read_Seal(){
            return get_option( 'wfc_plugin_seal' );
        }
        /**
         * Updates the value of the seal
         * 
         * @param intger $user id of the user
         */
        public function Wfc_Update_Seal( $user ){
            update_option( 'wfc_plugin_seal', $user->ID );
        }
        /**
         * Deletes the seal (NOT USED)
         */
        public function Wfc_Delete_Seal(){
        }
        /**
         * Checks if the seal is still present (NOT USED)
         */
        public function Wfc_Check_Seal(){
        }
        /**
         * Displays the HTML form for the seal
         */
        public function Wfc_Get_Seal_Form(){
            global $current_user;
            if($this->Wfc_Read_Seal()!=$current_user->ID)
            {
            ?>
            <h3>Plugin Disclaimer</h3>
            <p>Warning - Please contact Web Full Circle before upgrading, installing, or deleting any plugins. Changing settings on these pages may cause problems with your website's functionality.</p>
            <p>If you are confident you want to proceed without Web Full Circle Assistance please type
                <strong>letmein</strong> in the confirmation field below. Then click submit and you'll have access to the plugins section.
            </p>
            <form id="wfc_plugin_seal" action="" method="post">
                <?php wp_nonce_field( 'wfc_plugin_seal' ); ?>
                <input id="wfc_plugin_seal" type="hidden" name="wfc_plugin_seal" value="true"/>
                <input id="wfc_plugin_seal_confirm" type="text" name="wfc_plugin_seal_confirm" value=""/>
                <p class="submit">
                    <input id="wfc_plugin_seal_submit" style="width: 80px;" type="submit" name="Submit" class="button-primary" value="Reset"/>
                </p>
            </form>
            <?php
            exit();
            }
        }

        /**
         * Send email to Steve to notify him that a seal has been broken
         * With infos, date, time, user
         * 
         */
        private function Wfc_Email_Broken_Seal(){
            global $current_user;
            $headers[] = 'From: Website Name <me@example.net>';
            $headers[] = 'Cc: steve fischer <steve.fischer@webfullcircle.com>';
            $to        = 'stevecfischer@gmail.com';
            $subject   = 'Website Plugin Seal Broken';
            $message   = 'The user '.$current_user->user_login.' broke the plugin seal on the website : '.get_option('home').'\n
                            \rDate : '.date('l jS \of F Y h:i:s A').'\n
                            \rIP: '.$_SERVER['REMOTE_ADDR'].' ('.$_SERVER['HTTP_USER_AGENT'].')';
            wp_mail( $to, $subject, $message, $headers );
        }
    }

    /**
     * Instanciate the class to load the plugin into the framework
     */
    $WfcPluginSeal = new Wfc_Plugin_Seal();