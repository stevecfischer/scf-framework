<?php
    // Small frontend functions.
    /**
     * Returning featured image without height and width attributes set.
     *
     * Helps to fix images of responsive layouts
     *
     * @param null $post_id
     * @param string $size
     *
     * @return string
     */
    function wfc_the_post_thumbnail( $post_id = NULL, $size = 'thumbnail' ){
        $post_id        = (NULL === $post_id) ? get_the_ID() : $post_id;
        $feature_img_id = get_post_thumbnail_id( $post_id );
        $content        = sprintf(
            apply_filters( "wfc_the_post_thumbnail", '<img src="%s" class="" alt="" title="">' ), "steve ", "dave"
        );
        $image_attributes = wp_get_attachment_image_src( $feature_img_id, $size );
        $image_str        = '<img src="%1$s" class="" alt="" title="">';
        $image            = sprintf( apply_filters( "wfc_the_post_thumbnail", $image_str ), $image_attributes[0] );
        return $image;
    }
