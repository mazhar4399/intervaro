<?php
	$limit = 10;
	$page_no = isset($_REQUEST['page_no']) && !empty($_REQUEST['page_no']) ? $_REQUEST['page_no'] : 1;
	$products = get_products_api($page_no,10);
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
				<h1> <?php _e('WooCommerece Products Importer') ?> </h1>
				</div>
			</div>
		</div>
	</div>
	<div class="row wpi_import_response_pre" style="margin-top:20px; display:none;">
			<div class="col-md-12" id="wpi_import_response">
				<div class="alert alert-success" id="wpi_import_response">
					<div class="new_added">
						<span class="records"></span>
						<span class="msg"></span>
					</div>
					<div class="already_added">
						<span class="records"></span>
						<span class="msg"></span>
					</div>
					<div class="import_failed">
						<span class="records"></span>
						<span class="msg"></span>
					</div>
				</div>
			</div>
	</div>
	<div class="row" style="margin-top:20px;">
		<div class="col-md-6">
			<div class="card">
				<div class="card-body">
				<h2 class="card-title text-center"> <?php echo __( 'Import Multiple Products', 'woo-product-api-import' ) ?> </h2>
				<p class="card-text"> <?php echo __( 'Import Products from API', 'woo-product-api-import' ) ?> </p>
				<form action="" method="post" name="import_woo_products">
					<div class="row">
						<div class="col-md-6">    
							<label>Limit</label>
								<select id="limit" name="limit" class="form-control">
								<option value="10">10</option>
								 <!-- <option value="0">1000</option> -->
						</select>
					</div>
					<div class="row">
					<div class="col-md-12" style="margin-top:20px;">
						<input type="hidden" name="action" value="import_woo_products" style="display: none; visibility: hidden; opacity: 0;">
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
	
	<div class="row">
		<div class="col-md-12">
			<div class="text-right">
				<?php
				if(isset($_REQUEST['page_no']) && $_REQUEST['page_no'] > 1){
					?>
					<a class="btn btn-success"  href="?page=wpi_settings&page_no=<?php echo $page_no-1; ?>">Previous Page</a>
					<?php
				}
				if(count($products) == $limit){
					?>
					<a class="btn btn-primary" href="?page=wpi_settings&page_no=<?php echo $page_no+1; ?>" > Next Page</a>
					<?php
				}

				?>
			</div>
		</div>
	</div>
	
</div>

		


<script type = "text/javascript" > 
(function($) {
	function get_products_loop(i){
		var page_no = i || 1; // uses i if it's set, otherwise uses 0
		limit = $('form[name="import_woo_products"] #limit').val();
		setTimeout(
			function() {
				$.ajax({
					url: ajaxurl, 
					type: 'post',
					data: {'page_no' : page_no, 'limit' : limit, 'action' : 'import_woo_products'},
					success: function(response) {
						$('.wpi_import_response_pre').show();
						//$.LoadingOverlay("hide");
						resp = JSON.parse(response);
						//swal(resp.title, resp.message, resp.status);
						//location.reload();
						if(resp.total_found == limit){
							get_products_loop(page_no + 1);
						}else{
							swal('Import Finished.', '', 'success');
						}
							if(resp.import_result.new_added >= 1){
								element = '#wpi_import_response .new_added';
								records = parseInt($(element + ' .records').text()) || 0;
								$(element + ' .records').html(parseInt(resp.import_result.new_added)+parseInt(records));
								$(element + ' .msg').html('Products has imported successfully.');
							}
							if(resp.import_result.already_added >= 1){
								element = '#wpi_import_response .already_added';
								records = parseInt($(element + ' .records').text()) || 0;
								$(element+' .records').html(parseInt(resp.import_result.already_added)+parseInt(records));
								$(element+' .msg').html('Products already imported.');
							}
							if(resp.import_result.import_failed >= 1){
								element = '#wpi_import_response .import_failed';
								records = parseInt($(element+' .records').text()) || 0;
								$(element+ ' .records').html(parseInt(resp.import_result.import_failed)+parseInt(records));
								$(element+ ' .msg').html('Products failed to Import.');
							}
					},
					fail: function(err) {
						$.LoadingOverlay("hide");
						alert("There was an error: " + err);
					},
					timeout: 7200000 // sets timeout to 3 seconds
				});

		}, 1000);
	}

	$('form[name="import_woo_products"]').on('submit', function() {
		$('#wpi_import_response span').empty();
		$('.wpi_import_response_pre').hide();
			get_products_loop(1);
		return false;
	}); 

})(jQuery);


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

</script>


