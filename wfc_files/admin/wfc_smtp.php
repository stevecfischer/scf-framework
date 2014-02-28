<?php
    /**
     *
     * @package scf-framework
     * @author Steve
     * @date 11/19/13
     * @version 5.2
     */
    //add_action( 'admin_init', 'wfc_test_smtp' );
    class wfc_email
    {

        protected $WFC_SMTP_DEBUG = 2;
        protected $WFC_SMTP_DEBUGOUTPUT = 'html';
        protected $FROM_NAME = 'forms';
        protected $ADD_EMAIL = 'steve.fischer@webfullcircle.com';
        protected $smtp_settings;

        function __construct(){
            if( !$phpmailer = $this->wfc_set_phpmailer() ){
                die("PHPMAILER not set");
            }
            $this->wfc_get_smtp_settings();
            $phpmailer->Mailer     = "smtp";
            $phpmailer->SMTPSecure = $this->smtp_settings['wfc_mail_smtp_smtpsecure'];
            $phpmailer->Host       = $this->smtp_settings['wfc_mail_smtp_host'];
            $phpmailer->Port       = $this->smtp_settings['wfc_mail_smtp_port'];
            $phpmailer->SMTPAuth   = $this->smtp_settings['wfc_mail_smtp_smtpauth'];
            $phpmailer->Username   = $this->smtp_settings['wfc_mail_smtp_user'];
            $phpmailer->Password   = $this->smtp_settings['wfc_mail_smtp_password'];
            $phpmailer             = apply_filters( 'wp_mail_smtp_custom_options', $phpmailer );
            add_filter( 'wp_mail_from', array($this, 'wfc_smtp_mail_from') );
            add_filter( 'wp_mail_from_name', array($this, 'wfc_smtp_mail_from_name') );
            if( isset($_GET['wfc_smtp_action']) && $_GET['wfc_smtp_action'] == "sendtest" ){
                $this->wfc_test_smtp( $phpmailer );
            }
        }

        protected function wfc_set_phpmailer(){
            global $phpmailer;
            if( !is_object( $phpmailer ) || !is_a( $phpmailer, 'PHPMailer' ) ){
                require_once ABSPATH.WPINC.'/class-phpmailer.php';
                require_once ABSPATH.WPINC.'/class-smtp.php';
                $phpmailer = new PHPMailer();
                return $phpmailer;
            }
            return false;
        }

        private function wfc_get_smtp_settings(){
            $smptauth            = get_option( 'wfc_mail_smtp_smtpauth' ) ? get_option( 'wfc_mail_smtp_smtpauth' ) != "None" : "";
            $array               = array(
                'wfc_mail_smtp_host'       => get_option( 'wfc_mail_smtp_host' ),
                'wfc_mail_smtp_port'       => get_option( 'wfc_mail_smtp_port' ),
                'wfc_mail_smtp_smtpsecure' => get_option( 'wfc_mail_smtp_smtpsecure' ),
                'wfc_mail_smtp_smtpauth'   => $smptauth,
                'wfc_mail_smtp_user'       => get_option( 'wfc_mail_smtp_user' ),
                'wfc_mail_smtp_pass'       => get_option( 'wfc_mail_smtp_pass' )
            );
            $this->smtp_settings = $array;
        }

        function wfc_smtp_mail_from( $orig ){
            $sitename = strtolower( $_SERVER['SERVER_NAME'] );
            if( substr( $sitename, 0, 4 ) == 'www.' ){
                $sitename = substr( $sitename, 4 );
            }
            $default_from = 'wordpress@'.$sitename;
            if( $orig != $default_from ){
                return $orig;
            }
            return get_option( 'wfc_mail_from_email' );
        }

        function wfc_smtp_mail_from_name( $orig ){
            if( $orig == 'WordPress' ){
                return get_option( 'wfc_mail_from_name' );
            }
            return $orig;
        }

        function wfc_test_smtp( $phpmailer ){
            $to                   = get_option( 'wfc_test_email_address' );
            $subject              = 'WP Mail SMTP: '.__( 'Test mail to ', 'wp_mail_smtp' ).$to;
            $message              = __( 'This is a test email generated by the WP Mail SMTP WordPress plugin.', 'wp_mail_smtp' );
            $phpmailer->SMTPDebug = 2;
            ob_start();
            $result     = wp_mail( $to, $subject, $message );
            $smtp_debug = ob_get_clean();
            ?>
            <div id="message" class="updated fade"><p><strong>Test Message Sent</strong></p>
                <p>The result was:</p>
                <pre><?php var_dump( $result ); ?></pre>
                <p>The full debugging output is shown below:</p>
                <pre><?php var_dump( $phpmailer ); ?></pre>
                <p>The SMTP debugging output is shown below:</p>
                <pre><?php echo $smtp_debug ?></pre>
            </div>
            <?php

            unset($phpmailer);
        }
    }