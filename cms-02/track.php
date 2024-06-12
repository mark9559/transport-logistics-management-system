<?php include 'db_connect.php' ?>
<div class="col-lg-12">
	<div class="card card-outline card-primary">
		<div class="card-body">
			<div class="d-flex w-100 px-1 py-2 justify-content-center align-items-center">
				<label for="">Enter Tracking Number</label>
				<div class="input-group col-sm-5">
                    <input type="search" id="ref_no" class="form-control form-control-sm" placeholder="Type the tracking number here">
                    <div class="input-group-append">
                        <button type="button" id="track-btn" class="btn btn-sm btn-primary btn-gradient-primary">
                            <i class="fa fa-search"></i>
                        </button>
                    </div>
                </div>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-md-8 offset-md-2">
			<div class="timeline" id="parcel_history">
				
			</div>
		</div>
	</div>
</div>
<!-- Google Maps Container -->
<div id="map" style="height: 550px; width: 100%;"></div>

<div id="clone_timeline-item" class="d-none">
	<div class="iitem">
	    <i class="fas fa-box bg-blue"></i>
	    <div class="timeline-item">
	      <span class="time"><i class="fas fa-clock"></i> <span class="dtime">12:05</span></span>
	      <div class="timeline-body">
	      	asdasd
	      </div>
	    </div>
	  </div>
</div>

<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBKwn3cxd_DSeCyioIIWvfymMHezwyccXA&callback=initMap" async defer></script>
<script>
	function initMap() {
		window.map = new google.maps.Map(document.getElementById('map'), {
			center: {lat: -1.286389, lng: 36.817223}, // Default center set to Nairobi, Kenya
			zoom: 8
		});
	}
	
	function addMarker(location, map, info) {
		var marker = new google.maps.Marker({
			position: location,
			map: map
		});
		var infowindow = new google.maps.InfoWindow({
			content: info
		});
		marker.addListener('click', function() {
			infowindow.open(map, marker);
		});
	}
	
	function track_now(){
		start_load()
		var tracking_num = $('#ref_no').val()
		if(tracking_num == ''){
			$('#parcel_history').html('')
			end_load()
		}else{
			$.ajax({
				url:'ajax.php?action=get_parcel_history',
				method:'POST',
				data:{ref_no:tracking_num},
				error:err=>{
					console.log(err)
					alert_toast("An error occured",'error')
					end_load()
				},
				success:function(resp){
					if(typeof resp === 'object' || Array.isArray(resp) || typeof JSON.parse(resp) === 'object'){
						resp = JSON.parse(resp)
						if(Object.keys(resp).length > 0){
							$('#parcel_history').html('')
							resp.forEach(function(data){
								var tl = $('#clone_timeline-item .iitem').clone()
								tl.find('.dtime').text(data.date_created)
								tl.find('.timeline-body').text(data.status)
								$('#parcel_history').append(tl)

								// Add marker to the map
								var location = {lat: parseFloat(data.latitude), lng: parseFloat(data.longitude)};
								addMarker(location, window.map, data.status);
							})
							// Center the map on the first location
							window.map.setCenter({lat: parseFloat(resp[0].latitude), lng: parseFloat(resp[0].longitude)});
						}
					}else if(resp == 2){
						alert_toast('Unknown Tracking Number.',"error")
					}
				},
				complete:function(){
					end_load()
				}
			})
		}
	}
	$('#track-btn').click(function(){
		track_now()
	})
	$('#ref_no').on('search',function(){
		track_now()
	})
</script>
