<?php
    /**
     *
     * @package scf-framework
     * @author Steve
     * @date 3/12/13
     */
    define('WFC_MINUTE_IN_SECONDS', 60);
    define('WFC_HOUR_IN_SECONDS', 60 * WFC_MINUTE_IN_SECONDS);
    define('WFC_DAY_IN_SECONDS', 24 * WFC_HOUR_IN_SECONDS);
    define('WFC_WEEK_IN_SECONDS', 7 * WFC_DAY_IN_SECONDS);
    define('WFC_YEAR_IN_SECONDS', 365 * WFC_DAY_IN_SECONDS);
    function wfc_cookie_management(){
        if( !isset($_COOKIE['wfc_browser_check']) ){
            return;
        }
        if( !$_COOKIE['wfc_browser_check'] ){
            add_action( 'wp_footer', 'wfc_check_browser_version' );
        }
    }

    wfc_cookie_management();
    // Display Browser Nag Meta Box
    function wfc_dashboard_browser_nag(){
        $notice   = '';
        $response = wfc_check_browser_version();
        if( $response ){
            if( $response['insecure'] ){
                $msg =
                    sprintf( __( "It looks like you're using an insecure version of <a href='%s'>%s</a>. Using an outdated browser makes your computer unsafe. For the best WordPress experience, please update your browser." ), esc_attr( $response['update_url'] ), esc_html( $response['name'] ) );
            } else{
                $msg =
                    sprintf( __( "It looks like you're using an old version of <a href='%s'>%s</a>. For the best WordPress experience, please update your browser." ), esc_attr( $response['update_url'] ), esc_html( $response['name'] ) );
            }
            $browser_nag_class = '';
            if( !empty($response['img_src']) ){
                $img_src =
                    (is_ssl() && !empty($response['img_src_ssl'])) ? $response['img_src_ssl'] : $response['img_src'];
                $notice .=
                    '<div class="alignright browser-icon"><a href="'.esc_attr( $response['update_url'] ).'"><img src="'.
                        esc_attr( $img_src ).'" alt="" /></a></div>';
                $browser_nag_class = ' has-browser-icon';
            }
            $notice .= "<p class='browser-update-nag{$browser_nag_class}'>{$msg}</p>";
            $browsehappy = 'http://browsehappy.com/';
            $locale      = get_locale();
            if( 'en_US' !== $locale ){
                $browsehappy = add_query_arg( 'locale', $locale, $browsehappy );
            }
            $notice .= '<p>'.
                sprintf( __( '<a href="%1$s" class="update-browser-link">Update %2$s</a> or learn how to <a href="%3$s" class="browse-happy-link">browse happy</a>' ), esc_attr( $response['update_url'] ), esc_html( $response['name'] ), esc_url( $browsehappy ) ).
                '</p>';
            $notice .= '<p class="hide-if-no-js"><a href="" class="dismiss">'.__( 'Dismiss' ).'</a></p>';
            $notice .= '<div class="clear"></div>';
        }
        echo apply_filters( 'browse-happy-notice', $notice, $response );
    }

    function wfc_dashboard_browser_nag_class( $classes ){
        $response = wfc_check_browser_version();
        if( $response && $response['insecure'] ){
            $classes[] = 'browser-insecure';
        }
        return $classes;
    }

    /**
     * Check if the user needs a browser update
     *
     * @since 3.2.0
     */
function wfc_check_browser_version(){
    if( empty($_SERVER['HTTP_USER_AGENT']) ){
        return false;
    }
    $key = md5( $_SERVER['HTTP_USER_AGENT'] );
    if( false === ($response = get_site_transient( 'browser_'.$key )) ){
        global $wp_version;
        $options  = array(
            'body'       => array('useragent' => $_SERVER['HTTP_USER_AGENT']),
            'user-agent' => 'WordPress/'.$wp_version.'; '.home_url()
        );
        $response = wp_remote_post( 'http://api.wordpress.org/core/browse-happy/1.0/', $options );
        if( is_wp_error( $response ) || 200 != wp_remote_retrieve_response_code( $response ) ){
            return false;
        }
        /**
         * Response should be an array with:
         *  'name' - string - A user friendly browser name
         *  'version' - string - The most recent version of the browser
         *  'current_version' - string - The version of the browser the user is using
         *  'upgrade' - boolean - Whether the browser needs an upgrade
         *  'insecure' - boolean - Whether the browser is deemed insecure
         *  'upgrade_url' - string - The url to visit to upgrade
         *  'img_src' - string - An image representing the browser
         *  'img_src_ssl' - string - An image (over SSL) representing the browser
         */
        $response = maybe_unserialize( wp_remote_retrieve_body( $response ) );
        if( !is_array( $response ) ){
            return false;
        }
        set_site_transient( 'browser_'.$key, $response, WFC_WEEK_IN_SECONDS );
    }
    //print_r($response);
    if( $response['version'] >= $response['current_version'] ){
        return false;
    }
    $wfc_response            = array();
    $wfc_response['name']    = $response['name'];
    $wfc_response['version'] = $response['version'];
    switch( $wfc_response['name'] ){
        case 'Internet Explorer':
            $wfc_response['url'] = "http://windows.microsoft.com/en-US/internet-explorer/download-ie";
            break;
        case 'Chrome':
            $wfc_response['url'] = "https://www.google.com/intl/en/chrome/browser/";
            break;
        case 'Firefox':
            $wfc_response['url'] = "http://www.mozilla.org/en-US/firefox/new/";
            break;
        default:
            # code...
            break;
    }
    print_r( $_COOKIE );
if($wfc_response){
    ?>
    <!--echo '<div id="wfc-old-browser">';
    echo 'Our website is built for newer browsers. For the best experience, update your <a target="_blank" href="'.$response['url'].'">'.$response['name'].' browser here</a>.';
    echo '</div>';-->
    <script>
        jQuery(function ($) {
            function getCookie(c_name) {
                var i, x, y, ARRcookies = document.cookie.split(";");
                for (i = 0; i < ARRcookies.length; i++) {
                    x = ARRcookies[i].substr(0, ARRcookies[i].indexOf("="));
                    y = ARRcookies[i].substr(ARRcookies[i].indexOf("=") + 1);
                    x = x.replace(/^\s+|\s+$/g, "");
                    if (x == c_name) {
                        return unescape(y);
                    }
                }
            }

            function deleteCookie(key) {
                // Delete a cookie by setting the date of expiry to yesterday
                date = new Date();
                date.setDate(date.getDate() - 1);
                document.cookie = escape(key) + '=;expires=' + date;
            }

            if (!getCookie("wfc_browser_check")) {
                warningMessage = '<div id="wfc-old-browser">Our website is built for newer browsers. For the best experience, update your <a target="_blank" href="<?php echo $wfc_response['url']; ?>"><?php echo $wfc_response['name'];?> browser here</a><div id="close"> XXX </div></div>';
                $("body").prepend(warningMessage);
                $('#close').on('click', function () {
                    $('#wfc-old-browser').slideUp();
                    document.cookie = 'wfc_browser_check=checkme;expires=Tue, 04 Mar 2014 22:10:26 GMT;path=/'
                });
            }
        });
    </script>
<?php
}
}

//print_r($response);
