<?php

    /**
     * Class to contact github API
     * Allows you to easly fecth infos from a github repo
     *
     * @package WFC-framework
     * @author Thibault Miclo
     * @version 1.2
     * @since 5.2
     */
    class wfc_git_repo
    {
        /**
         * Definitions
         *
         * @since 1.0
         */
        protected
            $src_userRepos = 'https://api.github.com/users/%s/repos',
            $src_userRepoDetails = 'https://api.github.com/repos/%s/%s',
            $src_userRepoTags = 'https://api.github.com/repos/%s/%s/tags',
            $src_userRepoContents = 'https://api.github.com/repos/%s/%s/contents',
            $responseCode, $responseText,
            $user, $repo;

        /**
         * Classe Constructeur
         *
         * @since 1.0
         *
         * @param string $user a valid github user
         * @param string $repo a valid github repo
         */
        public function __construct( $user, $repo ){
            $this->user = $user;
            $this->repo = $repo;
        }

        /**
         * Lists all the repos of the user
         *
         * @since 1.0
         * @return Object $obj api response
         */
        public function listRepos(){
            $this->_request( sprintf( $this->src_userRepos, $this->user ) );
            if( $this->responseCode != 200 ){
                echo 'GitHub server error...'; // e.g
            }
            return json_decode( $this->responseText );
        }

        /**
         * Gets some infos about the repo
         *
         * @since 1.0
         * @return Object $obj api response
         */
        public function getRepoDetails(){
            $this->_request( sprintf( $this->src_userRepoDetails, $this->user, $this->repo ) );
            if( $this->responseCode != 200 ){
                echo 'GitHub server error...'; // e.g
            }
            return json_decode( $this->responseText );
        }

        /**
         * Makes the request to the API using file_get_contents
         * Could be improved with curl
         *
         * @since 1.0
         *
         * @param string $url url to query
         */
        private function _request( $url ){
            $options            = array('http' => array('user_agent' => $_SERVER['HTTP_USER_AGENT']));
            $context            = stream_context_create( $options );
            $contents           = @ file_get_contents( $url, false, $context );
            $this->responseCode = (false === $contents) ? 400 : 200;
            $this->responseText = $contents;
        }

        /**
         * Lists repo tags from the release section in github
         *
         * @since 1.1
         * @return Object $obj api response
         */
        public function getRepoTags(){
            $this->_request( sprintf( $this->src_userRepoTags, $this->user, $this->repo ) );
            if( $this->responseCode != 200 ){
                echo 'GitHub server error...'; // e.g
            }
            return json_decode( $this->responseText );
        }

        /**
         * Gets the content of the repo root
         *
         * @since 1.0
         * @return Object $obj api response
         */
        public function getRepoContents(){
            $this->_request( sprintf( $this->src_userRepoContents, $this->user, $this->repo ) );
            if( $this->responseCode != 200 ){
                echo 'GitHub server error...'; // e.g
            }
            return json_decode( $this->responseText );
        }
    }
