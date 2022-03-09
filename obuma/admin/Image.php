<?php
/**
     * Attach images to product (feature/ gallery)
     */
    function attach_product_thumbnail($post_id, $url, $flag){
        /*
         * If allow_url_fopen is enable in php.ini then use this
         */
        //$image_url = $url;
        //$url_array = explode('/',$url);
        //$image_name = $url_array[count($url_array)-1];
        //$image_data = file_get_contents($image_url); // Get image data
      /*
       * If allow_url_fopen is not enable in php.ini then use this
       */
      $image_url = $url;
       $url_array = explode('/',$url);
       $image_name = $url_array[count($url_array)-1];
       $ch = curl_init();
       curl_setopt ($ch, CURLOPT_URL, $image_url);
       // Getting binary data
       curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
       curl_setopt($ch, CURLOPT_BINARYTRANSFER, 1);
       $image_data = curl_exec($ch);
       curl_close($ch);
      $upload_dir = wp_upload_dir(); // Set upload folder
        $unique_file_name = wp_unique_filename( $upload_dir['path'], $image_name ); //    Generate unique name
        $filename = basename( $unique_file_name ); // Create image file name
        // Check folder permission and define file location
        if( wp_mkdir_p( $upload_dir['path'] ) ) {
            $file = $upload_dir['path'] . '/' . $filename;
        } else {
            $file = $upload_dir['basedir'] . '/' . $filename;
        }
        // Create the image file on the server
        file_put_contents( $file, $image_data );
        // Check image file type
        $wp_filetype = wp_check_filetype( $filename, null );
        // Set attachment data
        $attachment = array(
            'post_mime_type' => $wp_filetype['type'],
            'post_title' => sanitize_file_name( $filename ),
            'post_content' => '',
            'post_status' => 'inherit'
        );
        // Create the attachment
        $attach_id = wp_insert_attachment( $attachment, $file, $post_id );
        // Include image.php
        require_once(ABSPATH . 'wp-admin/includes/image.php');
        // Define attachment metadata
        $attach_data = wp_generate_attachment_metadata( $attach_id, $file );
        // Assign metadata to attachment
        wp_update_attachment_metadata( $attach_id, $attach_data );
        // asign to feature image
        if( $flag == 0){
            // And finally assign featured image to post
            set_post_thumbnail( $post_id, $attach_id );
        }
        // assign to the product gallery
        if( $flag == 1 ){
            // Add gallery image to product
            $attach_id_array = get_post_meta($post_id,'_product_image_gallery', true);
            $attach_id_array .= ','.$attach_id;
            update_post_meta($post_id,'_product_image_gallery',$attach_id_array);
        }
    }    