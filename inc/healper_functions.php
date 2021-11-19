<?php 


 function wpi_upload_image($imageUrl,$title='',$description='', $alt_text='',$post_id='')
 {
 
   global $wpdb;
   $url = $imageUrl;
   $title = $title;
   $alt_text = $alt_text;
 
   require_once(ABSPATH . 'wp-admin/includes/media.php');
   require_once(ABSPATH . 'wp-admin/includes/file.php');
   require_once(ABSPATH . 'wp-admin/includes/image.php');
 
   // sideload the image --- requires the files above to work correctly
   //this function return html ,src,id in the last parameter we can set the value
   $image_id = media_sideload_image( $url,  null, $title , 'id' );
              // media_sideload_image( $file, $post_id, $desc = null, $return = 'html' )
   // convert the url to image id
 
   if( $image_id) {
 
       // make sure the post exists
       $image = get_post( $image_id );
 
       if( $image) {
 
            update_post_meta($image->ID, '_wp_attachment_image_alt', $alt_text);
 
       }
   }
   return $image_id;
 
 }