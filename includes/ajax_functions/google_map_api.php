<script type=text/javascript src = 'https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=false'></script>
    
    <?php
       
        ?>
    <style>
    #map-canvas {
        height: 100%;
        width:100%;
        margin-top: 0px;
        padding: 0px
      }
    </style>
    <script>

 /* $(".card").each(function() {

    var lat = '';
    var lng = '';
    var geocoder = new google.maps.Geocoder();
    container =this;
    zip = $(container).data($("#zipcode").val());
    alert(zip);
      geocoder.geocode( { 'address': zip }, function(results, status) {
            if (status === google.maps.GeocoderStatus.OK) {
                         
                    lat = results[0].geometry.location.lat();
                     alert (lat);
                    lng = results[0].geometry.location.lng();
                    alert(lng);
                    var mapOptions = {
                                 zoom: 9,
                                 center: new google.maps.LatLng(lat,lng),
                                 mapTypeId: google.maps.MapTypeId.ROADMAP,                                 
                   };
                    var map = new google.maps.Map(document.getElementById('container'),
                     mapOptions);
                     map.setCenter(results[0].geometry.location);
                     var center = map.getCenter();
                      google.maps.event.addDomListener(window, 'resize', function() {
                           map.setCenter(center);
                       });
                      var marker = new google.maps.Marker({
                        map: map,
                        position: results[0].geometry.location
                        });
                    
                    } else {
                      alert("Geocode was not successful for the following reason: " + status);
                    }
            });
}); */    
    function initialize() {
     var lat = '';
            var lng = '';
            var zip = $(".zipcode").attr('rel');
            
            alert ("zipcode inside google map" +zip);
             var geocoder = new google.maps.Geocoder();
               geocoder.geocode( { 'address': zip}, function(results, status) {
                    if (status === google.maps.GeocoderStatus.OK) {
                       
                       lat = results[0].geometry.location.lat();
                       lng = results[0].geometry.location.lng();
                        alert (lat +lng);
                       var mapOptions = {
                                    zoom: 9,
                                    center: new google.maps.LatLng(lat,lng)
                      };
                      
                     var map = new google.maps.Map(document.getElementById('map-canvas'),
                     mapOptions);                      
                       
                     map.setCenter(results[0].geometry.location);
                     var center = map.getCenter();
                     google.maps.event.trigger(map, 'resize');
                     map.setCenter(center);
                     
                     var marker = new google.maps.Marker({
                        map: map,
                        position: results[0].geometry.location
                     });
                    
                    } else {
                      alert("Geocode was not successful for the following reason: " + status);
                    }
                });
                
}

function loadScript() {
  var script = document.createElement('script');
  script.type = 'text/javascript';
  script.src = 'https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=false&' +
      'callback=initialize';
  document.body.appendChild(script);
}

//window.onload = loadScript;
onload = setTimeout('initialize()',2000);

    </script>
	
  <body style="width:100%; height: 100%">
     
      <div id="map-canvas" >
         
      </div>