<?php
add_shortcode("show_bookings_all", "show_bookings_all");
add_shortcode("show_bookings_single", "show_bookings_single");
add_shortcode("show_information_message", "show_information_message");
function show_bookings_all($attrs)
{
    ob_start(); ?>
<div class="row rms-bookings-main" style="text-align: center;">
  <div class="row-col-12 rms-title">
    <h1> 
      <?php echo get_option("rms_booking_title", true); ?>
    </h1>
  </div>
  <div class="row-col-12 rms-booking-row">
    <?php
    global $wpdb;
    $rms_booked_rooms = $wpdb->prefix . "rms_booked_rooms";
    $rms_rooms = $wpdb->prefix . "rms_rooms";
    $customPagHTML = "";
    $query = "SELECT rooms.room_name,rooms.room_location,booked_room.room_id,booked_room.start_dt,booked_room.organisation,booked_room.end_dt FROM $rms_booked_rooms as booked_room  join $rms_rooms as rooms ON `rooms`.`id`=`booked_room`.`room_id` where CURDATE() >=  DATE(booked_room.start_dt) &&  CURDATE() <=  DATE(booked_room.end_dt)  ";

    $total_query = "SELECT COUNT(1) FROM (${query}) AS combined_table";
    $total = $wpdb->get_var($total_query);
    $items_per_page = get_option('rms_bookings_per_page') != null && get_option('rms_bookings_per_page') !=0 ?  intval(get_option('rms_bookings_per_page', true)) : 100000;
    $page = isset($_GET["sida"]) ? abs((int)sanitize_text_field( $_GET["sida"])) : 1;
    $offset = $page * $items_per_page - $items_per_page;
    $result = $wpdb->get_results($query ." order by rooms.room_priority ASC ,booked_room.start_dt ASC LIMIT ${offset}, ${items_per_page}");
    $totalPage = ceil($total / $items_per_page);
    $bookings = [];
    foreach ($result as $key => $value) {
        $bookings[$value->room_id][] = [
            "room_name" => $value->room_name,
            "room_location" => $value->room_location,
            "organisation" => $value->organisation,
            "start_dt" => $value->start_dt,
            "end_dt" => $value->end_dt,
        ];
    }
    if (isset($bookings) && !empty($bookings) && count($bookings) >= 1) {
        foreach ($bookings as $key => $r_room) { ?>
		    <div style="margin:20px 0px" class="rms-booking-row">
		      <h3>
		        <?php echo $r_room[0]["room_name"]; ?>
		      </h3>
		      <h4>
		        <?php echo $r_room[0]["room_location"]; ?>
		      </h4>
		      <?php foreach ($r_room as $key => $booked) { ?>
		      <span>
		        <?php echo date("H:i", strtotime($booked["start_dt"])) .
		            " - " . date("H:i", strtotime($booked["end_dt"])) . "  |  Booked By " .$booked["organisation"]; ?>
		      </span>
		      <br>
		      <?php } ?>
		    </div> 
   <?php }
    }
    ?>
  </div>

<div id="">
	<input type="hidden" name="rms_current_page" id="rms_current_page" value="<?php echo $page; ?>">
	<input type="hidden" name="rms_total_page" id="rms_total_page" value="<?php echo $totalPage; ?>">
	<input type="hidden" name="rms_has_pages" id="rms_has_pages" value="<?php echo $totalPage > 1 ? 'yes' : 'no'; ?>">
</div>

<div class="rms-pages">
<?php
if ($totalPage > 1) {
    $customPagHTML =
        '<div class="pagination"><span style="margin:0px 20px;width:90%;text-align: center; ">Sida ' .
        $page .
        " av " .
        $totalPage .
        " </span>" .
        '</div><div class="pagination"></div>';
}
echo $customPagHTML;

?>
</div>

</div>
<script type="text/javascript">
    jQuery( document ).ready(function($) {

    	 var rms_seconds = <?php echo get_option('rms_refresh_page') != null && get_option('rms_refresh_page') !=0  ?  intval(get_option('rms_refresh_page', true)) : 3000000 ?>;
    	 var timer2 = "0:"+rms_seconds;

       if($('#rms_has_pages').val() == 'yes' && rms_seconds != 3000000){

      	 	 const queryString = window.location.search;

      			rms_current_page = parseInt($('#rms_current_page').val());
      			rms_total_page = parseInt($('#rms_total_page').val());
      			 var delay = parseInt(rms_seconds)*1000; 
            
          	if( rms_current_page>= rms_total_page){
          		var url = '?sida=1'
          		 setTimeout(function(){ location.replace(url); }, delay);
          	}else{
          		rms_current_page = rms_current_page+1
          		var url = '?sida='+rms_current_page;
          		 setTimeout(function(){ location.replace(url); }, delay);
          	}
       }   	 
});
</script>

<?php
return ob_get_clean();
}


function show_bookings_single($attrs = [], $content = null, $tag = "")
{
    extract(
        shortcode_atts(
            [
                "room_id" => 1,
            ],
            $attrs
        )
    );
    ob_start();
    ?>
<div class="row row rms-main" style="text-align: center;">
  <div class="row-col-12 rms-title">
    <h1> 
      <?php echo get_option("rms_booking_title", true); ?>
    </h1>
  </div>
  <div class="row-col-12 rms-booking">
    <?php
    global $wpdb;
    $rms_booked_rooms = $wpdb->prefix . "rms_booked_rooms";
    $rms_rooms = $wpdb->prefix . "rms_rooms";
    $booked_data = $wpdb->get_results(
        "SELECT * FROM $rms_booked_rooms as booked_room join $rms_rooms as rooms ON `rooms`.`id`=`booked_room`.`room_id` where booked_room.room_id=$room_id  GROUP by rooms.id order by booked_room.start_dt ASC "
    );
    if (
        isset($booked_data) &&
        !empty($booked_data) &&
        count($booked_data) >= 1
    ) {
        foreach ($booked_data as $key => $r_room) { ?>
    <div style="margin:20px 0px" class="rms-booking-title">
      <h3>
        <?php echo $r_room->room_name; ?>
      </h3>
      <h4>
        <?php echo $r_room->room_location; ?>
      </h4>
      <?php
      $room_data = $wpdb->get_results(
          "SELECT * FROM $rms_booked_rooms as booked_room join $rms_rooms as rooms ON `rooms`.`id`=`booked_room`.`room_id` where booked_room.room_id=$r_room->room_id  order by booked_room.start_dt ASC"
      );
      foreach ($room_data as $key => $booked) { ?>
      <span>
        <?php echo date("H:i", strtotime($booked->start_dt)) .
            " - " . date("H:i", strtotime($booked->end_dt)) ."  |  Booked By " . $booked->organisation; ?>
      </span>
      <br>
      <?php }
      ?>
    </div> 
    <?php }
    }
    ?>
  </div>
</div>
<?php return ob_get_clean();
}
function show_information_message()
{
    ob_start();
    date_default_timezone_set("Europe/Stockholm");
    $message = get_option("rms_imsg", true);
    $today_date = date("Y-m-d H:i");
    $today_date = date("Y-m-d H:i", strtotime($today_date));
    if (
        $today_date >=
            date("Y-m-d H:i", strtotime($message["rms_imsg_start_dt"])) &&
        $today_date <= date("Y-m-d H:i", strtotime($message["rms_imsg_end_dt"]))
    ) { ?>
<div class="row rms-footer-main" style="text-align: center;">
  <div class="rms-footer-msg">
    <h5>
      <?php echo $message["rms_info_msg"]; ?>
    </h5>
  </div>
</div>
<?php }
    return ob_get_clean();
}
