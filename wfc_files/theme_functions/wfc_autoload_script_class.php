<?php

    if( isset($_GET['wfc_renew_cache']) && $_GET['wfc_renew_cache'] == 'renew' ){
        $wfc_cache = new wfc_auto_load_assets();
        $wfc_cache->renew_cache();
    }

    /**
     * This class loads the specified CSS or JS
     */
    class wfc_auto_load_assets
    {
        /**
         * @var string
         */
        public $ext;

        /**
         * @param string $ext (js|css)
         *
         * @return array
         */
        function wfc_auto_load_assets(){
            if( !$this->create_compress_directory() ){
                $debug_arr = debug_backtrace();
                var_dump( $debug_arr );
            }
        }

        public function autoload( $ext ){
            $this->ext = $ext;
            $dir       = WFC_PT.'/../'.$ext;
            if( !is_dir( $dir ) ){
                return "Directory does not exist.";
            }
            $directory = dir( $dir );
            $include   = array();
            while( false !== ($item = $directory->read()) ){
                if( $item != '.' && $item != '..' && !preg_match( '/excl/', $item ) ){
                    $info = pathinfo( $dir."/".$item );
                    if( isset($info['extension']) && strtolower( $info['extension'] ) == $ext ){
                        if( AUTOLOAD_MINIFY === false ){
                            if( !preg_match( '/min/', $item ) ){
                                $include[$info['filename']] = $item;
                            }
                        } else{
                            if( preg_match( '/min/', $item ) ){
                                $include[$info['filename']] = $item;
                            }
                        }
                    }
                }
            }
            ksort( $include );
            if( isset($_GET['wfc_renew_cache']) && $_GET['wfc_renew_cache'] == 'renew' ){
                $this->autocompress( $include );
            }
            return $include;
        }

        /**
         * @param array $include files in css|js folder
         */
        public function autocompress( $include ){
            $buffer = "";
            foreach( $include as $k => $v ){
                $buffer .= file_get_contents( WFC_PT."/".$this->ext."/".$v );
            }
            if( !$fp = fopen( WFC_THEME_ROOT."/comp_assets/extended_assets_compressed.".$this->ext, 'w+' ) ){
                die("Error opening file ".WFC_THEME_ROOT."/comp_assets/extended_assets_compressed.".$this->ext);
            }
            fwrite( $fp, $buffer );
            fclose( $fp );
        }

        /**
         * Refresh cache
         */
        public function renew_cache(){
            if( !$this->create_compress_directory() ){
                $debug_arr = debug_backtrace();
                var_dump( $debug_arr );
                die();
            }
            $this->autoload( 'css' );
            $this->autoload( 'js' );
        }

        /**
         * Create the comp_assets folder if it doesn't exist
         *
         * @return bool
         */
        public function create_compress_directory(){
            $dirname = WFC_THEME_ROOT."/comp_assets/";
            if( !file_exists( $dirname ) ){
                if( !mkdir( $dirname, 0777, true ) ){
                    return false;
                }
            }
            return true;
        }
    }