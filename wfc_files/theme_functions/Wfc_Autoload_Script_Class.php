<?php

    if(isset($_GET['wfc_renew_cache']) && $_GET['wfc_renew_cache'] == 'renew'){
        $wfc_cache = new wfc_auto_load_assets();
        $wfc_cache->renew_cache();
    }
    /**
     * Toggle if site minifies and compresses js|css
     */
    define('AUTOLOAD_MINIFY', false);

    /**
     * This class loads the specified CSS or JS
     *
     * s
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
        public function autoload( $ext ){
            $this -> ext = $ext;
            $dir       = WFC_PT.'/'.$ext;
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
            if(isset($_GET['wfc_renew_cache']) && $_GET['wfc_renew_cache'] == 'renew'){
                $this->autocompress($include);
            }
            return $include;
        }

        /**
         * @param array $include files in css|js folder
         */
        public function autocompress( $include ){
            if($this -> ext == 'css'){
                $buffer = "";
                foreach( $include as $k => $v ){
                    $buffer .= file_get_contents( WFC_PT."/css/".$v );
                }
                echo "Compression Started";
                if(!$fp = fopen (WFC_PT."/comp_assets/extended_assets_compressed.css",'w+')){
                    die('Error opening file');
                }
                fwrite($fp,$buffer);
                echo "Compression Finished";
                fclose($fp);
            }

            if($this -> ext == 'js'){
                $buffer = "";
                foreach( $include as $k => $v ){
                    $buffer .= file_get_contents( WFC_PT."/js/".$v );
                }
                if($fp = fopen (WFC_PT."/comp_assets/extended_assets_compressed.js",'w+')){
                    die('Error opening file');
                }
                fwrite($fp,$buffer);
                fclose($fp);
            }
        }

        /**
         * Refresh cache
         */
        public function renew_cache(){
            $this->autoload('js');
            $this->autoload('css');
        }
    }

