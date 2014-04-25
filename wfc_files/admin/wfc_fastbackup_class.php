<?php
    /**
     * PHP Class to backup/restore/download database
     * Only works with PDO for now
     * Usage :
     *   - downloadDB('filename') : create a backup and send it to download as filename.sql
     *   - restoreDB('path/to/file.sql') : restore database with this file
     *   - backupDB('path/to/file') : create a backup named file.sql store it in path/to/
     *
     * @author Thibault Miclo
     * @version 1.0
     */
    ob_start();

    class wfc_fastbackup_class
    {
        protected
            $hostname,
            $user,
            $password,
            $database;

        private
            $db = false,
            $errors = array();

        public function __construct( $host = '', $user = '', $pass = '', $database = '' ){
            $this->hostname = $host;
            $this->user     = $user;
            $this->password = $pass;
            $this->database = $database;
        }

        public function __set( $name, $value ){
            $this->$name = $value;
        }

        public function __get( $name ){
            return $this->$name;
        }

        public function clearDB(){
            $this->db = false;
        }

        private function connectDB(){
            if( $this->db ){
                return true;
            }
            if( !empty($this->hostname) && !empty($this->user) && !empty($this->database) ){
                if( class_exists( 'PDO' ) ){
                    $dsn = 'mysql:dbname='.$this->database.';host='.$this->hostname;
                    try{
                        $this->db = new PDO( $dsn, $this->user, $this->password );
                        return true;
                    } catch( PDOException $e ){
                        $this->errors[] = 'Unable to connect to the database, PDO error :'.$e->getMessage();
                        $this->db       = false;
                    }
                } else{
                    $this->errors[] = 'FastBackup needs PDO to work.';
                }
            } else{
                $this->errors[] = 'Make sure to set hostname, user and database name properly.';
            }
            return false;
        }

        public function downloadDB( $name ){
            if( !empty($name) ){
                if( $this->connectDB() ){
                    $str = $this->generateString();
                    ob_clean();
                    header( 'Content-Disposition: attachment; filename="'.$name.'.sql"' );
                    header( 'Content-Type: text/plain' );
                    header( 'Content-Length: '.strlen( $str ) );
                    header( 'Connection: close' );
                    echo $str;
                    return true;
                }
            } else{
                $this->errors[] = 'Make sure to give a name to the file you want to download.';
            }
            return false;
        }

        public function backupDB( $path ){
            if( !empty($path) ){
                if( $this->connectDB() ){
                    $str = $this->generateString();
                    if( $f = fopen( $path, 'w+' ) ){
                        fwrite( $f, $str );
                        fclose( $f );
                        return true;
                    } else{
                        $this->errors[] = 'Unable to save the file.';
                    }
                }
            } else{
                $this->errors[] = 'FastBackup needs a path to store the file.';
            }
            return false;
        }

        public function restoreDB( $file ){
            if( !empty($file) && file_exists( $file ) ){
                if( $this->connectDB() ){
                    $db        = $this->db;
                    $sql_query = @fread( @fopen( $file, 'r' ), @filesize( $file ) );
                    $sql_query = $this->remove_remarks( $sql_query );
                    $sql_query = $this->split_sql_file( $sql_query, ';' );
                    foreach( $sql_query as $sql ){
                        $db->exec( $sql );
                    }
                    return true;
                }
            } else{
                $this->errors[] = 'FastBackup needs a file to restore the database.';
            }
            return false;
        }

        public function getDBObject(){
            return $this->db;
        }

        private function remove_remarks( $sql ){
            $lines     = explode( "\n", $sql );
            $sql       = "";
            $linecount = count( $lines );
            $output    = "";
            for( $i = 0; $i < $linecount; $i++ ){
                if( ($i != ($linecount - 1)) || (strlen( $lines[$i] ) > 0) ){
                    if( isset($lines[$i][0]) && $lines[$i][0] != "#" ){
                        $output .= $lines[$i]."\n";
                    } else{
                        $output .= "\n";
                    }
                    $lines[$i] = "";
                }
            }
            return $output;
        }

        private function split_sql_file( $sql, $delimiter ){
            $tokens      = explode( $delimiter, trim( $sql ) );
            $sql         = "";
            $output      = array();
            $matches     = array();
            $token_count = count( $tokens );
            for( $i = 0; $i < $token_count; $i++ ){
                if( ($i != ($token_count - 1)) || (strlen( $tokens[$i] > 0 )) ){
                    $total_quotes     = preg_match_all( "/'/", $tokens[$i], $matches );
                    $escaped_quotes   = preg_match_all( "/(?<!\\\\)(\\\\\\\\)*\\\\'/", $tokens[$i], $matches );
                    $unescaped_quotes = $total_quotes - $escaped_quotes;
                    if( ($unescaped_quotes % 2) == 0 ){
                        $output[]   = $tokens[$i];
                        $tokens[$i] = "";
                    } else{
                        $temp          = $tokens[$i].$delimiter;
                        $tokens[$i]    = "";
                        $complete_stmt = false;
                        for( $j = $i + 1; (!$complete_stmt && ($j < $token_count)); $j++ ){
                            $total_quotes     = preg_match_all( "/'/", $tokens[$j], $matches );
                            $escaped_quotes   = preg_match_all( "/(?<!\\\\)(\\\\\\\\)*\\\\'/", $tokens[$j], $matches );
                            $unescaped_quotes = $total_quotes - $escaped_quotes;
                            if( ($unescaped_quotes % 2) == 1 ){
                                $output[]      = $temp.$tokens[$j];
                                $tokens[$j]    = "";
                                $temp          = "";
                                $complete_stmt = true;
                                $i             = $j;
                            } else{
                                $temp .= $tokens[$j].$delimiter;
                                $tokens[$j] = "";
                            }
                        }
                    }
                }
            }
            return $output;
        }

        private function generateString(){
            $db = $this->db;
            foreach( $db->query( 'SHOW TABLES' ) as $row ){
                $tables[] = $row[0];
            }
            foreach( $tables as $table ){
                $result     = $db->query( 'SELECT * FROM `'.$table.'`' );
                $num_fields = $result->columnCount();
                $return .= 'DROP TABLE IF EXISTS `'.$table.'`;';
                $row2 = $db->query( 'SHOW CREATE TABLE `'.$table.'`' )->fetch( PDO::FETCH_NUM );
                $return .= "\n\n".$row2[1].";\n\n";
                for( $i = 0; $i < $num_fields; $i++ ){
                    while( $row = $result->fetch( PDO::FETCH_NUM ) ){
                        $return .= 'INSERT INTO `'.$table.'` VALUES(';
                        for( $j = 0; $j < $num_fields; $j++ ){
                            $row[$j] = addslashes( $row[$j] );
                            $row[$j] = str_replace( "\n", "\\n", $row[$j] );
                            if( isset($row[$j]) ){
                                $return .= '\''.$row[$j].'\'';
                            } else{
                                $return .= '\'\'';
                            }
                            if( $j < ($num_fields - 1) ){
                                $return .= ',';
                            }
                        }
                        $return .= ");\n";
                    }
                }
                $return .= "\n\n\n";
            }
            return $return;
        }

        public function getErrors(){
            foreach( $this->errors as $e ){
                echo $e.'<br />';
            }
        }
    }
