<?php
	$products = get_all_products();
	$total_pages = count($products);
	global $wpdb;

	$all_post_ids = array();

	$the_query = new WP_Query(array('post_type' => 'product','posts_per_page' => - 1));
	$post_ids = wp_list_pluck( $the_query->posts, 'ID' );

	foreach ($post_ids as $key => $post_id) {

		$wpi_id = get_post_meta($post_id, '_wpi_id', true);

		//$dealId = empty($dealId) ? $post_id : $dealId;

		if(!empty($wpi_id)){
			$all_post_ids[] = ['wpi_id'=>$wpi_id, 'post_id'=>$post_id];
		}
	
	}

?>

<div class="wrap">
	<div class="row">
		<div class="col-md-12">
		<div class="card">
			<div class="card-body pr-0 pl-0">
			<h1> <?php _e('WooCommerece Products Importer', 'room-booking-management') ?> </h1>
			</div>
		</div> <?php if(isset($value_msg) && !empty($value_msg)){ ?> <div class="alert alert-
					<?php echo $msg_status; ?>" style="margin: 20px 0px 0px 0px;">
			<p style="margin: 0px;font-size: 18px;"> <?php echo $value_msg; ?> </p>
		</div>
		<script type="text/javascript">
			toastr.options.positionClass = "toast-bottom-right";
			toastr.success(" < ? php echo $value_msg; ? > ")
		</script> <?php }?>
		</div>
	</div>
	<div class="row" style="margin-top:20px;">
		<div class="col-md-6">
			<div class="card">
				<div class="card-body">
				<h2 class="card-title text-center"> <?php echo __( 'Import Multiple Products', 'woo-product-api-import' ) ?> </h2>
				<p class="card-text"> <?php echo __( 'Import Products from API', 'woo-product-api-import' ) ?> </p>
				<form action="" method="post" name="import_bulk_products">
					<div class="row">
						<div class="col-md-6">    
								<label>Pages No </label>
								<select id="import_object_type" name="page_no" class="form-control">
									<?php
									$page_no =1;
										for($i=1; $i<= $total_pages; $i+=10){
											?>
												<option value="<?php echo $page_no; ?>"><?php echo $page_no; ?></option>
											<?php
											$page_no++;
										}
									?>
								</select>
						</div>
						<div class="col-md-6">    
							<label>Limit</label>
								<select id="limit" name="limit" class="form-control">
								<option value="10">10</option>
								<!-- <option value="0">All</option> -->
						</select>
					</div>
					<div class="row">
					<div class="col-md-12" style="margin-top:20px;">
						<input type="hidden" name="action" value="import_bulk_products" style="display: none; visibility: hidden; opacity: 0;">
					</div>
					<div class="col-md-12 text-center">
						<button type="submit" class="btn btn-success">Start to Import!</button>
					</div>
					</div>
				</form>
				</div>
			</div>
		</div>
	</div>
	<div class="col-md-6">
			<div class="card">
				<div class="card-body">
				<h2 class="card-title text-center"> <?php echo __( 'Import Bulk Products', 'woo-product-api-import' ) ?> </h2>
				<p class="card-text"> <?php echo __( 'Import Products from API', 'woo-product-api-import' ) ?> </p>
				<form action="" method="post" name="import_bulk_products">
					
					<div class="row">
					<div class="col-md-12">
						<input type="hidden" name="action" value="import_bulk_products" style="display: none; visibility: hidden; opacity: 0;">
					</div>
					<div class="col-md-12 text-center">
						<button type="submit" class="btn btn-success">Start to Import!</button>
					</div>
					</div>
				</form>
				</div>
			</div>
		</div>
	</div>
	<div class="row">
		
	</div>
	<div class="row">
		<div class="col-md-12">
			<div class="card">
			<div class="card-body">
				<h4 class="card-title" style="margin-bottom: 10px;"> <?php echo __( 'List of Products on API', 'woo-product-api-import' ) ?> </h4>
				<p class="card-title" style="margin-bottom: 40px;"> <?php echo __( 'Total Products are on APIs', 'woo-product-api-import' )." : ". count($products); ?> </p>
				<p class="card-text"></p>
				<table class="table table-striped table-bordered" id="table_id">
				<thead>
					<tr>
					<th>
						<input type="checkbox" id="checkAll">
					</th>
					<th>Image</th>
					<th>Id</th>
					<th>sku</th>
					<th>name</th>
					<th>price</th>
					<th>width</th>
					<th>height</th>
					<th>length</th>
					<th>weight</th>
					<th>categories</th>
					<th>sale_price</th>
					<th>description</th>
					<th>short_description</th>
					<th style="min-width:90px !important">action</th>
					</tr>
				</thead>
				<tbody>
					<tr> 
						<?php if(count($products) >= 1) :
								foreach ($products as $product) :
									?> 
						<td></td>
						<td><img src="<?php echo $image = !empty($product['images']) ?explode(',',$product['images'])[0] : ''; ?>" width="80px"></td>
						<td> <?php echo $product['id'] ?> </td>
						<td> <?php echo $product['sku'] ?> </td>
						<td> <?php echo $product['name'] ?> </td>
						<td> <?php echo $product['price'] ?> </td>
						<td> <?php echo $product['width'] ?> </td>
						<td> <?php echo $product['height'] ?> </td>
						<td> <?php echo $product['length'] ?> </td>
						<td> <?php echo $product['weight'] ?> </td>
						<td> <?php echo $product['categories'] ?> </td>
						<td> <?php echo $product['sale_price'] ?> </td>
						<td> <?php echo substr($product['description'],0,40); ?> </td>
						<td> <?php echo substr($product['short_description'],0,20); ?> </td>
						<td> 
						<?php
							if($k = array_search($product['id'], array_column($all_post_ids, 'wpi_id')) !== false ){
								?>
								<button style="margin:5px 0px" type='button' onclick="update_product_object(<?php echo $all_post_ids[$k]['post_id'] ?>)" class="btn btn-primary btn-xs">Update From API </button>
							<?php
							}else{
							?> <button style="margin:5px 0px" type='button' onclick="import_single_product(<?php echo $product['id']?>)" class="btn btn-success btn-xs">Import From API </button>
							<?php
							}
							?>
						</td>  
					</tr> 
					<?php 
						endforeach;
						endif; ?>
				</tbody>
				</table>
			</div>
			</div>
		</div>
	</div>
</div>
		


<script type = "text/javascript" > 

function update_product_object(id) {
	jQuery.LoadingOverlay("show");
	jQuery.ajax({
		url: ajaxurl, 
		type: 'post',
		data: {
			action: 'update_product_by_id',
			product_id: id
		},
		success: function(response) {
			jQuery.LoadingOverlay("hide");
			resp = JSON.parse(response);
			alert(resp.message);
			location.reload();
			//swal(respp.title, respp.message, respp.status);
		},
		fail: function(err) {
			jQuery.LoadingOverlay("hide");
			alert("There was an error: " + err);
		}
	});
}

function import_single_product(id) {
	jQuery.LoadingOverlay("show");
	jQuery.ajax({
		url: ajaxurl, 
		type: 'post',
		data: {
			action: 'import_single_product_by_id',
			product_id: id
		},
		success: function(response) {
			jQuery.LoadingOverlay("hide");
			resp = JSON.parse(response);
			alert(resp.message);
			location.reload();
			//swal(respp.title, respp.message, respp.status);
		},
		fail: function(err) {
			jQuery.LoadingOverlay("hide");
			alert("There was an error: " + err);
		}
	});
}
jQuery('form[name="import_bulk_products"]').on('submit', function() {
	var form_data = jQuery(this).serializeArray();
	jQuery.LoadingOverlay("show", {
		text: "Vänta!"
	});
	jQuery.ajax({
		url: ajaxurl, // Here goes our WordPress AJAX endpoint.
		type: 'post',
		data: form_data,
		success: function(response) {
			console.log(response);
			jQuery.LoadingOverlay("hide");
			resp = JSON.parse(response);
			swal(resp.title, resp.message, resp.status);
			//location.reload();
		},
		fail: function(err) {
			jQuery.LoadingOverlay("hide");
			alert("There was an error: " + err);
		},
		timeout: 7200000 // sets timeout to 3 seconds
	});
	// This return prevents the submit event to refresh the page.
	return false;
}); 
</script>