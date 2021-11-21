<?php

        function get_products_api($page=1, $limit=10){

            if($limit == 0){
                $data = wp_remote_get(WPI_API_BASE_URL);
            }else{
                $data = wp_remote_get(WPI_API_BASE_URL."?_page=$page&_limit=$limit");
            }

            $data = wp_remote_retrieve_body($data);

            $data = json_decode($data,true);

            return  $data;

        }

        function get_single_product_api($product_id){

            $data = wp_remote_get(WPI_API_BASE_URL.$product_id);

            $data = wp_remote_retrieve_body($data);

            $data = json_decode($data,true);

            return  $data;

        }

        function get_all_products(){
                $all_products = get_products_api(1, 0);
             return $all_products;
        }

        


        function import_products($product){
        
                require_once(WPI_PATH . '/inc/healper_functions.php');

                if(!empty($product)){

                        //Post Data
                        $post = [
                            'post_author' => '',
                            'post_content' => $product['description'],
                            'post_excerpt' => $product['short_description'],
                            'post_status' => "publish",
                            'post_title' => $product['name'],
                            'post_name' => $product['name'],
                            'post_parent' => '',
                            'post_type' => "product",
                        ];
                        //Create Post
                        $product_id = wp_insert_post($post);

                        if(!empty($product['images'])){
                            $images = explode(',',$product['images']);
                            foreach ($images  as $key =>  $image) {
                                
                                //array_push($allImgages_ids, $topMainImage_id);
                                $topMainImage_id = wpi_upload_image($image . '?ext=.jpeg',$product['name'],$product['name'],'feature-image',$product_id);
                                
                                if($key == 0){
                                    set_post_thumbnail( $product_id, $topMainImage_id );
                                }
                                
                            }
                            
                        }

                        add_post_meta($product_id, '_wpi_id', $product['id']);

                        //set Product Category
                        $categories = explode(',',$product['categories']);
                        wp_set_object_terms($product_id, $categories, 'product_cat');

                        //set product type
                        wp_set_object_terms($product_id, 'simple', 'product_type');

                        update_post_meta($product_id, '_sku', $product['sku']);
                        update_post_meta($product_id, '_price',$product['price'] );
                        update_post_meta($product_id, '_regular_price', $product['price']);
                        update_post_meta($product_id, '_sale_price', $product['sale_price']);
                        update_post_meta($product_id, '_weight', $product['weight']);
                        update_post_meta($product_id, '_length', $product['length']);
                        update_post_meta($product_id, '_height', $product['height']);
                        update_post_meta($product_id, '_width', $product['width']);

                    if( $product_id ){
                        return 1;
                    }else{
                        return $resp = ['status'=>'error','code'=>'3','message_code'=> '3', 'message'=> __( "Error during insert into DB on deal ID: ".$product['id'], 'woo-product-api-import' )];
                    }
            }else{
                
                return $resp = ['status'=>'warning','code'=>'2','message_code'=> '1', 'message'=> __( 'Deal id not found.', 'woo-product-api-import' )];
            }

        }
