<?php

/**

 * Woo Products API Import

 */

class WPI_Woo_Product_Import {

    function __construct() {

        /***********************************************************************************************/

        /* Admin Menus, Settings, Scripts */

        /***********************************************************************************************/

        // Actions

        add_action('wp_ajax_import_bulk_products', array($this, 'import_bulk_products')); // This is for authenticated users

        add_action('wp_ajax_update_product_by_id', array($this, 'update_product_by_id')); // This is for authenticated users

        add_action('wp_ajax_import_single_product_by_id', array($this, 'import_single_product_by_id')); // This is for authenticated users
        
        add_action('wp_ajax_import_woo_products', array($this, 'import_woo_products')); // This is for authenticated users

    }

    function import_woo_products(){
        
        $resp = array('status' => '', 'title' => '', 'message' => '', 'import_result' => '');
        $resp['import_result'] = array('new_added' => 0, 'already_added' => 0, 'import_failed' => 0);

        $response = "";

        $all_post_ids = array();

        $the_query = new WP_Query(array('post_type' => 'product','posts_per_page' => - 1));

        $post_ids = wp_list_pluck( $the_query->posts, 'ID' );
       
       foreach ($post_ids as $key => $post_id) {

            $wpi_id = get_post_meta($post_id, '_wpi_id', true);
            
            if(!empty($wpi_id)){
                array_push($all_post_ids, $wpi_id);
            }
        
        }
        if(isset($_POST['page_no']) && isset($_POST['limit'])){
            $page_no = isset($_POST['page_no']) && !empty($_POST['page_no']) ? $_POST['page_no'] : 1;
            $limit = isset($_POST['limit']) && !empty($_POST['limit']) ? $_POST['limit'] : 1;
            $products = get_products_api($page_no,$limit);
        }else{
            $products = get_all_products();
        }
        
    

        
        $count_imported = 0;
        $count_already = 0;
        $count_failed = 0;
        $count_failed_estatedID = 0;
        $concld = "";
        $totla_products = count($products);
        //$concld .=  "Total Products are $totla_products". ".\n";;
        
        if ($products && count($products) >= 1) {
            foreach ($products as $product) {
             
                    $mainProdId = $product['id'];
                    if (!IN_ARRAY($mainProdId, $all_post_ids)) {
                        $respp = import_products($product);
                        if ($respp == 1) {
                            $count_imported++;
                            $resp['import_result']['new_added'] = $resp['import_result']['new_added']+1;
                            $response .=  $mainProdId. __("Product added Successfully.", 'woo-product-api-import');
                            $response .=  "\n";
                        } else {
                            $count_failed++;
                            $response .=  $respp['message'];
                            $response .=  "\n";
                            $resp['import_result']['import_failed'] = $resp['import_result']['import_failed']+1;
                        }
                    } else {
                        $count_already++;
                        $resp['import_result']['already_added'] = $resp['import_result']['already_added']+1;
                        $response .=   $mainProdId. "  " .__("Product already exist.", 'woo-product-api-import');
                        $response .=  "\n";
                    }
                
            }
        }else{
            $concld .= "No Product found.";
        }

        if($count_imported > 0){
            $concld .= $count_imported." ". __( "New Products imported successfully.", 'woo-product-api-import' ). "\n";
        }
        if($count_already > 0){
            $concld .= $count_already." ". __( "Products already has imported.", 'woo-product-api-import' ). "\n";
        }
        
        if($count_failed > 0){
            $concld .= $count_failed." ". __( "Products faild to import.", 'woo-product-api-import' ). "\n";
        }

        $resp['status'] = 'info';

        $resp['title'] = __('', 'woo-product-api-import');

        $total_found = count($products);
        
        $resp['total_found'] = $total_found ;

       

        $resp['message'] = $concld;

        echo json_encode($resp);

        wp_die();


    }

    
    function import_single_product_by_id(){
        
            $product_id = $_POST['product_id'];
            
            $resp = array('status' => '', 'title' => '', 'message' => '');

            $product = get_single_product_api($product_id);

            $respp = import_products($product);
            
            if ($respp == 1) {

                $resp['status'] = 'success';

                $resp['title'] = __('Successfully Imported.', 'woo-product-api-import');

                $resp['message'] = __('Product Imported Successfully.', 'woo-product-api-import');

            

            }else{

                $resp['status'] = 'failed';

                $resp['title'] = __('Error', 'woo-product-api-import');

                $resp['message'] = __('Something went worng.', 'woo-product-api-import');

            }

            echo json_encode($resp);

            wp_die();
                        
    }   



    

    function update_product_by_id($product_id, $action_type = "")
    {
        $resp = array(
            'status' => '',
            'title' => '',
            'message' => ''
        );

        require_once (WPI_PATH . '/inc/healper_functions.php');

        $product_id = "";
        if (isset($_POST['product_id']))
        {
            $product_id = $_POST['product_id'];
        }
        elseif (isset($product_id) && !empty($product_id))
        {
            $product_id = $product_id;
        }
        else
        {
            return _e('Woo Product ID Not Found', 'woo-product-api-import');
        }

        $pro_api_id = get_post_meta($product_id, '_wpi_id', true);

        if (empty($pro_api_id))
        {
            return _e('Product API ID Not Found', 'woo-product-api-import');
        }

        $product = get_single_product_api($pro_api_id);

        if (!empty($product))
        {

            if (!empty($product['images']))
            {
                $images = explode(',', $product['images']);
                foreach ($images as $image)
                {
                    $topMainImage_id = wpi_upload_image($image);
                    //array_push($allImgages_ids, $topMainImage_id);
                    
                }

            }
            //Post Data
            $post = [
                'ID' => $product_id, 
                'post_author' => '', 
                'post_content' => $product['description'], 
                'post_excerpt' => $product['short_description'], 
                'post_status' => "publish", 
                'post_title' => $product['name'], 
                'post_name' => $product['name'], 
                'post_parent' => '', 
                'post_type' => "product"
            ];

            //Update Post
            wp_update_post($post);

            add_post_meta($product_id, '_wpi_id', $product['id']);

            //set Product Category
            $categories = explode(',', $product['categories']);
            wp_set_object_terms($product_id, $categories, 'product_cat');

            //set product type
            wp_set_object_terms($product_id, 'simple', 'product_type');

            update_post_meta($product_id, '_sku', $product['sku']);
            update_post_meta($product_id, '_price', $product['price']);
            update_post_meta($product_id, '_regular_price', $product['price']);
            update_post_meta($product_id, '_sale_price', $product['sale_price']);
            update_post_meta($product_id, '_weight', $product['weight']);
            update_post_meta($product_id, '_length', $product['length']);
            update_post_meta($product_id, '_height', $product['height']);
            update_post_meta($product_id, '_width', $product['width']);

            $resp['status'] = 'success';
            $resp['title'] = __('Updated Successfully!', 'woo-product-api-import');
            $resp['message'] = __('Product Updated Successfully from API.', 'woo-product-api-import');

            if ($action_type == "non_ajax")
            {
                return "success";
                wp_die();
            }
            else
            {
                echo json_encode($resp);
                wp_die();
            }

        }

    }


    static function wpi_activated(){
        //nothing
    }


 }
