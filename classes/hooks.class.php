<?php

/**

 * Woo Products API Import

 */

Class WPI_Hooks{
     
    function __construct(){

        add_action( 'admin_enqueue_scripts', array($this,'add_admin_scripts'), 10, 1 );

        // ADDING A CUSTOM COLUMN TITLE TO ADMIN PRODUCTS LIST
        add_filter( 'manage_edit-product_columns', array($this,'custom_product_column'),11);

        // ADDING THE DATA FOR EACH PRODUCTS BY COLUMN (EXAMPLE)
        add_action( 'manage_product_posts_custom_column' , array($this,'custom_product_list_column_content'), 10, 2 );

        add_action('admin_head', array($this,'wpi_column_width'));
    }

        
    
    function custom_product_column($columns)
    {
    //add columns
    $columns['wpi_action'] = __( 'API Action','woocommerce'); // title
    return $columns;
    }

    
    function custom_product_list_column_content( $column, $product_id )
    {
        global $post;
        
        // HERE get the data from your custom field (set the correct meta key below)
    

        
        if ($column == 'wpi_action') {

            $wpi_id =  get_post_meta( $product_id, '_wpi_id', true );
    
            if(isset($wpi_id) && !empty($wpi_id)){
                echo "<input type = 'button' class='button' style='margin: 5px 0px;' onclick = 'updateProduct(\"$product_id\")' value = '".__( 'Update', 'woo-product-api-import' )."'>";
            }
            
        }
    }
    

    function wpi_column_width() {
        echo '<style type="text/css">';
        echo '.column-wpi_action { text-align: center; width:70px !important; overflow:hidden }';
        echo '</style>';
    }


    function add_admin_scripts( $hook ) {
        global $post;
        if ( $hook == 'post-new.php' || $hook == 'post.php' || $hook == 'edit.php') {
            if ( isset($post->post_type) && 'product' === $post->post_type ) {     
                wp_enqueue_script( 'loadingoverlay.min', WPI_URL . '/admin/assets/js/loadingoverlay.min.js', array('jquery'));
               ?>
                <script>
                    function updateProduct(Id,update_type)
                    {
                        var id=Id;
                        var form_data = jQuery( id ).serializeArray();
                        var ajax_url = '<?php echo admin_url("admin-ajax.php"); ?>';
                        var ajax_nonce = '<?php echo wp_create_nonce("secure_nonce_name"); ?>';
                        var data = {
                            action: 'update_product_by_id',
                            product_id: id,
                            update_type: update_type,
                            security: ajax_nonce,
                        };
                         jQuery.LoadingOverlay("show",{text : "Vänta!"});
                        jQuery.post(ajax_url, data, function(response) {
                            jQuery.LoadingOverlay("hide");
                                resp = JSON.parse(response);
                                alert(resp.message);
                                location.reload();
                        });
                    return false;
                    }
                </script>
               <?php
            }
        }
    }
    
}