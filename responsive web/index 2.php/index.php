<?php
  if (isset($_REQUEST["QueryType"])){
    $QueryType = $_REQUEST["QueryType"];
    if($QueryType == "PlaceNearBy"){
      $Keyword = $_REQUEST["Keyword"];
      $Category = $_REQUEST["Category"];
      $Distance = $_REQUEST["Distance"]*1609.344;
      $Location = $_REQUEST["Location"];
      $Flag_LatLon = $_REQUEST["Flag_LatLon"];
      if($Keyword!="" && $Category!="" && $Distance!="" && $Location!="" && $Flag_LatLon!=""){
        if($Flag_LatLon == "false"){
          $Location = str_replace(' ', '+', $Location);
          $jsonurl = "https://maps.googleapis.com/maps/api/geocode/json?address=$Location&key=AIzaSyDdA0wGx6eK9PxoLJBiamwAYyDj3bSfliY";
          $json = file_get_contents($jsonurl);
          $jsonObj = json_decode($json,true);
          $Location = $jsonObj["results"][0]["geometry"]["location"]["lat"].",".$jsonObj["results"][0]["geometry"]["location"]["lng"];
        }
        if($Category == "default"){
          $Keyword = str_replace(' ', '+', $Keyword);
          $jsonurl = "https://maps.googleapis.com/maps/api/place/nearbysearch/json?location=$Location&radius=$Distance&keyword=$Keyword&key=AIzaSyDdA0wGx6eK9PxoLJBiamwAYyDj3bSfliY";
        }
        else{
          $Keyword = str_replace(' ', '+', $Keyword);
          $jsonurl = "https://maps.googleapis.com/maps/api/place/nearbysearch/json?location=$Location&radius=$Distance&type=$Category&keyword=$Keyword&key=AIzaSyDdA0wGx6eK9PxoLJBiamwAYyDj3bSfliY";
        }
        $json = file_get_contents($jsonurl);
        $jsonObjs = array(json_decode($json,true));
        $idx=0;
        while(isset($jsonObjs[$idx]["next_page_token"])){
          $PageToken=$jsonObjs[$idx]["next_page_token"];
          $jsonurl="https://maps.googleapis.com/maps/api/place/nearbysearch/json?pagetoken=$PageToken&key=AIzaSyDdA0wGx6eK9PxoLJBiamwAYyDj3bSfliY";
          $status="INVALID_REQUEST";
          while($status=="INVALID_REQUEST"){
            $json = file_get_contents($jsonurl);
            $jsonObj = json_decode($json,true);
            $status = $jsonObj["status"];
          }
          array_push($jsonObjs,$jsonObj);
          $idx+=1;
        }
        echo json_encode($jsonObjs);
      }
    }
    else if($QueryType == "PlaceDetail"){
      $PlaceId = $_REQUEST["PlaceId"];
      if($PlaceId!=""){
        $jsonurl = "https://maps.googleapis.com/maps/api/place/details/json?placeid=$PlaceId&key=AIzaSyDdA0wGx6eK9PxoLJBiamwAYyDj3bSfliY";
        $json = file_get_contents($jsonurl);
        $jsonObj = json_decode($json,true);
        for($i=0;$i<sizeof($jsonObj["result"]["photos"]);$i++){
          $PhotoReference = $jsonObj["result"]["photos"][$i]["photo_reference"];
          $jsonObj["result"]["photos"][$i]["photo_reference"]="https://maps.googleapis.com/maps/api/place/photo?maxwidth=250&photoreference=$PhotoReference&key=AIzaSyDdA0wGx6eK9PxoLJBiamwAYyDj3bSfliY";
        }
        echo json_encode($jsonObj);
      }
    }
    else if($QueryType == "YelpReview"){
      /*
      $name = $_REQUEST["name"];
      $city = $_REQUEST["city"];
      $state = $_REQUEST["state"];
      $country = $_REQUEST["country"];
      header("Authorization: Bearer [Gj1gcE48E0JXTIvjppMes25CYmv_sKiRj-2z4ru8WpmrbUb2NLrXKRf9KErwy8MrHGZmY7HW17iq2KJ-o0jT_zBbf0JtC5lbvDH2XBIHEFeTsVSC7_U55fxjd7vGWnYx]");
      $options = array(
        'accessToken' => 'YOUR ACCESS TOKEN', // Required, unless apiKey is provided
        'apiHost' => 'api.yelp.com', // Optional, default 'api.yelp.com',
        'apiKey' => 'YOUR ACCESS TOKEN', // Required, unless accessToken is provided
      );
      $client = \Stevenmaguire\Yelp\ClientFactory::makeWith(
          $options,
          \Stevenmaguire\Yelp\Version::THREE
      );
      $responseBody = $e->getResponseBody(); // string from Http request
      $responseBodyObject = json_decode($responseBody);
      $json=file_get_contents("https://api.yelp.com/v3/businesses/matches/best?name=$name&city=$city&state=$state&country=$country");
      echo "$json";
      $jsonObj=json_decode($json,true);
      $placeid=$jsonobj["id"];
      echo $placeid;
      $json=file_get_contents("https://api.yelp.com/v3/businesses/$placeid/reviews");
      echo $json;*/
    }
    return false;
  }
?>
<html>
	<head>
		<title>hw8</title>
		  <meta charset="UTF-8">
      <meta name="viewport" content="width=device-width, initial-scale=1">
      <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
      <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
      <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
      <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
      <script src="https://apis.google.com/js/api.js"></script>
      <script src="https://code.angularjs.org/snapshot/angular.min.js"></script>
      <script src="https://code.angularjs.org/snapshot/angular-animate.js"></script>
      <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDdA0wGx6eK9PxoLJBiamwAYyDj3bSfliY&libraries=places"></script>
      <script src="https://momentjs.com/downloads/moment.js"></script>
      <style type = "text/css">
        h2{
          text-align: center;
        }
        .form-horizontal .control-label{
          text-align: left;
          font-weight: bold;
          padding: 0;
          margin: 0;
        }
        #querybox{
          border-radius: 10px;
          border-color: #cdcdcd;
          border-style: solid;
          background-color: #f8f8f8;
          margin-top: 10px;
        }
        .icon{
          text-align: center;
        }
        .icon img{
          width: 30px;
        }
        td p{
          font-size: 70%;
        }
        .glyphicon-star{
          color: #ffa700;
        }
        th{
          font-size: 70%;
        }
        td{
          font-size: 70%;
        }
        .res_detail{
          transition: all linear 0.5s;
          position: relative;
          top: 0;
          left: 0;
        }
        .res_detail.ng-hide{
          left: -120%;
        }
        #locationField, #controls {
          position: relative;
        }
      </style>
  </head>
  <body ng-app="ngAnimate">
    <div class="container" id="querybox">
      <h3 style="text-align:center;"><b>Travel and Entertainment Search</b></h3>
      <form class="form-horizontal">
        <div class="form-group"><span class="col-sm-1"></span>
          <label class="control-label col-sm-2" for="keyword">Keyword</label>
          <div class="col-sm-8"><input type="text" class="form-control" id="keyword" required></div>
        <span class="col-sm-1"></span></div>
        <div class="form-group"><span class="col-sm-1"></span>
          <label class="control-label col-sm-2" for="category">Category</label>
          <div class="col-sm-6"><select class="form-control" id="category">
            <option value="default" selected = "selected">default</option>
            <option value="cafe">cafe</option>
            <option value="airport">airport</option>
            <option value="bakery">bakery</option>
            <option value="restaurant">restaurant</option>
            <option value="beauty">beauty</option>
            <option value="salon">salon</option>
            <option value="casino">casino</option>
            <option value="movie_theater">movie theater</option>
            <option value="lodging">lodging</option>
            <option value="airport">airport</option>
            <option value="train_station">train station</option>
            <option value="subway_station">subway station</option>
            <option value="bus_station">bus station</option>
          </select></div>
        <span class="col-sm-3"></span></div>
        <div class="form-group"><span class="col-sm-1"></span>
          <label class="control-label col-sm-2" for = distance>Distance (miles)</label>
          <div class="col-sm-6"><input type="text" class="form-control" id="distance" placeholder="10" value="10"></div>
        <span class="col-sm-3"></span></div>
        <div class="form-group"><span class="col-sm-1"></span>
          <label class="control-label col-sm-2">From</label>
          <div class="col-sm-8">
            <div class="radio"><label><input type="radio" name="from" id="fromhere" checked>Current location</label></div>
            <div class="radio"><label><input type="radio" name="from" id="fromlocation">Other. Please specify:</label></div>
            <div class="locationField"><input type="text" class="form-control" id="location" placeholder="Enter a location" disabled></div>
          </div>
        <span class="col-sm-1"></span></div>
        <div class="form-group"><span class="col-sm-1"></span>
          <button type="submit" class="btn btn-primary" id="search" disabled ng-click='place_page=true;fav_page=false;detail_page=false;'><span class="glyphicon glyphicon-search" aria-hidden="true"></span> Search</button>
          <button type="clear" class="btn btn-default">Clear</button>
        <span class="col-sm-9"></span></div>
      </form>
    </div><br>
    <div style="text-align:center;">
      <button type="button" class="btn btn-primary" id="results" ng-click="place_page=true;fav_page=false;detail_page=false;">Results</button>
      <button type="button" class="btn btn-link" id="favorites" ng-click="place_page=false;fav_page=true;detail_page=false;">Favorites</button>
    </div>
    <center><div style="width:80%;text-align:left;"><button type="button" class="btn btn-default" id="list" style="display:none;" ng-click="place_page=true;fav_page=false;detail_page=false;" disabled><span class='glyphicon glyphicon-chevron-left'></span>List</button></div></center>
    <center><div style="width:80%;text-align:right;"><button type="button" class="btn btn-default" id="details" style="display:none;" ng-click='place_page=false;fav_page=false;detail_page=true;' disabled>Details<span class='glyphicon glyphicon-chevron-right'></span></button></div></center>
    <br/>
    <center><div style="width:80%">
      <div class="progress" style="display:none;">
        <div class="progress-bar progress-bar-striped progress-bar-animated" id="bar" role="progressbar" style="width: 0%" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" ></div>
      </div>
    </div></center>
    <div id='res_place_info' ng-show="place_page"></div>
    <div style='text-align:center;' ng-show="place_page">
      <button type="button" class='btn btn-default previous' id='previous' style="display:none;">Previous</button>
      <button type="button" class='btn btn-default next' id='next' style="display:none;">Next</button>
    </div>
    <div id='res_fav_info' ng-show="fav_page"></div>
    <div id='res_place_detail' ng-show="detail_page" class="res_detail">
      <div id='detail_nav' style="display:block; height:50px;"></div>
      <div id='detail_info'></div>
      <div id='detail_photos'></div>
      <div id='detail_map'>
        <form>
          <div class="form-row">
            <div class="form-group col-sm-4 col-xs-12">
              <label for="map_from">From</label>
              <input type="text" class="form-control" id="map_from" value="Your location">
            </div>
            <div class="form-group col-sm-4 col-xs-12">
              <label for="map_to">To</label>
              <input type="text" class="form-control" id="map_to" disabled>
            </div>
            <div class="form-group col-sm-2 col-xs-12">
              <label for="travel_mode">Travel Mode</label>
              <select class="form-control" id="travel_mode">
                <option value="DRIVING" selected = "selected">Driving</option>
                <option value="BICYCLING">Bicycling</option>
                <option value="TRANSIT">Transit</option>
                <option value="WALKING">Walking</option>
              </select>
            </div>
            <div class="form-group col-sm-2 col-xs-12">
              <label for="get_direction">Direction</label>
              <button class="form-control btn btn-primary" id="get_direction">Get Directions</button>
            </div>
          </div>
          <div class="form-row" style="width:34px;"><button class="form-control btn btn-default" style="padding:0;margin:0;position:relative;left:30px;" id="streetview_switch"><img style="width:100%;" id="map_toggle" src="pegman.png"></button></div>
          <div class="form-row col-sm-11 col-xs-11" id="map" style="height:300px;margin:5%">
          </div>
          <div class="form-row col-sm-11 col-xs-11" id="panel" style="margin:5%"></div>
        </form>
      </div>
      <div id='detail_reviews'>
        <div class="container">
          <div id="review_ctrl">
            <form>
              <div class="form-group  col-sm-3 col-xs-6">
                <select class="form-control" id="review_type">
                  <option value="google_reviews" selected = "selected">Google Reviews</option>
                  <option value="yelp_reviews">Yelp Reviews</option>
                </select>
              </div>
              <div class="form-group  col-sm-3 col-xs-6">
                <select class="form-control col-sm-2 col-xs-6" id="sort_type">
                  <option value="default_order" selected = "selected">Default Order</option>
                  <option value="highest_rating">Highest Rating</option>
                  <option value="lowest_rating">Lowest Rating</option>
                  <option value="most_recent">Most Recent</option>
                  <option value="least_recent">Least Recent</option>
                </select>
              </div>
            </form>
          </div>
          <div class="form-group col-sm-12 col-xs-12" id="reviews">
          </div>
        </div>
      </div>
    </div>
    <script type="text/javascript">
      $(document).ready(function(){
        var GeoInfo;
        var PlaceInfo=[];
        var DetailInfo=[];
        var pagenum=0;
        var pageidx=0;
        var directionsService = new google.maps.DirectionsService();
        var directionsDisplay = new google.maps.DirectionsRenderer();
        var panorama;
        var placeSearch, autocomplete1,autocomplete2;
        var detailselected;
        var componentForm = {
          street_number: 'short_name',
          route: 'long_name',
          locality: 'long_name',
          administrative_area_level_1: 'short_name',
          country: 'long_name',
          postal_code: 'short_name'
        };
        initAutocomplete();
        if(localStorage.getItem("favorites")==null){
          localStorage.setItem("favorites","[]");
        }
        $.ajax({url: "http://ip-api.com/json", success: function(result){
          GeoInfo = result;
          $("#search").prop("disabled", false);
        }});
        function initAutocomplete() {
          autocomplete1 = new google.maps.places.Autocomplete((document.getElementById('location')),{types: ['geocode']});
          autocomplete2 = new google.maps.places.Autocomplete((document.getElementById('map_from')),{types: ['geocode']});
        }
        $("#location").focus(function(){
          if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(function(position) {
              var geolocation = {
                lat: position.coords.latitude,
                lng: position.coords.longitude
              };
              var circle = new google.maps.Circle({
                center: geolocation,
                radius: position.coords.accuracy
              });
              autocomplete1.setBounds(circle.getBounds());
              autocomplete2.setBounds(circle.getBounds());
            });
          }
        });
        function PlaceNearByJ2T(jsonDoc){
          jsonObj=JSON.parse(jsonDoc);
          PlaceInfo=jsonObj;
          var htmlDoc="";
          for(idx=0;idx<jsonObj.length;idx++){
            htmlDoc+="<div class='container' id='page_"+idx+"' style='display:none;'><center><table class='table' style='width:80%;'><thead><tr><th>#</th><th>Category</th><th>Name</th><th>Address</th><th>Favorite</th><th>Details</th></tr></thead><tbody>";
            for(i=0;i<jsonObj[idx]["results"].length;i++){
              var icon = jsonObj[idx]["results"][i]["icon"];
              var name = jsonObj[idx]["results"][i]["name"];
              var vicinity = jsonObj[idx]["results"][i]["vicinity"];
              var placeid = jsonObj[idx]["results"][i]["place_id"];
              var lat = jsonObj[idx]["results"][i]["geometry"]["location"]["lat"];
              var lng = jsonObj[idx]["results"][i]["geometry"]["location"]["lng"];
              var flag=false;
              for(j=0;j<JSON.parse(localStorage.getItem("favorites")).length;j++){
                if(placeid==JSON.parse(localStorage.getItem("favorites"))[j]["place_id"]){
                  flag=true;
                }
              }
              htmlDoc+="<tr><td>"+(i+1)+"</td><td><div class = 'icon'><img src = '"+icon+"'/></div></td><td><p class='placename'>"+name+"</p></td><td><p>"+vicinity+"</p></td><td><button type='button' class='btn btn-default btn-star"+(flag?"":"-empty")+"' id='favorite_"+idx+"_"+i+"'><span class='glyphicon glyphicon-star"+(flag?"":"-empty")+"' id='star_"+idx+"_"+i+"'></span></button></td><td><button type='button' class='btn btn-default btn_detail' id='detail_"+idx+"_"+i+"' ng-click='place_page=false;fav_page=false;detail_page=true;'><span class='glyphicon glyphicon-chevron-right' id='right_"+idx+"_"+i+"'></span></button></td></tr>";
            }
            pageidx=0;
            pagenum=jsonObj.length;
            htmlDoc+="</tbody></table></center></div>";
          }
          return htmlDoc;
        }
        function PlaceDetailJ2T(jsonDoc){
          jsonObj = JSON.parse(jsonDoc);
          //reviews
          var htmlDoc = "<div style='width:85%'><ul class='nav nav-tabs navbar-right'><li class='active'><a id='nav_detail_info'>Info</a></li><li><a id='nav_detail_photos'>Photos</a></li><li><a id='nav_detail_map'>Map</a></li><li><a id='nav_detail_reviews'>Reviews</a></li></ul></div>";
          DetailInfo=jsonObj;
          $("#detail_nav").html(htmlDoc);
          return detailinfo();
        }
        function detailinfo(){
          htmlDoc="<div class='container'><table class='table table-striped'><tbody>";
          if("formatted_address" in DetailInfo["result"])
            htmlDoc+="<tr><th scope='row'>Address</th><td>"+DetailInfo["result"]["formatted_address"]+"</td></tr>";
          if("international_phone_number" in DetailInfo["result"])
            htmlDoc+="<tr><th scope='row'>Phone Number</th><td>"+DetailInfo["result"]["international_phone_number"]+"</td></tr>";
          if("price_level" in DetailInfo["result"]){
            htmlDoc+="<tr><th scope='row'>Price Level</th><td>"+"$".repeat(DetailInfo["result"]["price_level"])+"</td></tr>";
          }
          if("rating" in DetailInfo["result"])
            htmlDoc+="<tr><th scope='row'>Rating</th><td>"+DetailInfo["result"]["rating"]+"<div class='glyphicon glyphicon-star' style='width:13px;'></div>".repeat(Math.round(DetailInfo["result"]["rating"]))+"</td></tr>";
          if("url" in DetailInfo["result"])
            htmlDoc+="<tr><th scope='row'>Google Page</th><td><a href='"+DetailInfo["result"]["url"]+"'>"+DetailInfo["result"]["url"]+"</a></td></tr>";
          if("website" in DetailInfo["result"])
            htmlDoc+="<tr><th scope='row'>Website</th><td><a href='"+DetailInfo["result"]["website"]+"'>"+DetailInfo["result"]["website"]+"</a></td></tr>";
          if("opening_hours" in DetailInfo["result"])
            if(DetailInfo["result"]["opening_hours"]["open_now"])
              
              htmlDoc+="<tr><th scope='row'>Hours</th><td>Open Now: "+DetailInfo["result"]["opening_hours"]["weekday_text"][getDay()]+"   <a class='openhours'>Daily open hours</a></td></tr></tbody></table></div>";
            else
              htmlDoc+="<tr><th scope='row'>Hours</th><td>Closed   <a class='openhours'>Daily open hours</a></td></tr></tbody></table></div>";
          return htmlDoc;
        }
        $("body").on("click",".openhours",function(){
          alert(DetailInfo["result"]["opening_hours"]["weekday_text"]);
        });
        function showpage(idx){
          $("#details").prop("style","display:init;");
          for(i=0;i<pagenum;i++){
            if(i==idx)
              $("#page_"+i).prop("style","display:initial;");
            else
              $("#page_"+i).prop("style","display:none;");
          }
          $("#previous").prop("style","display:initial;");
          $("#next").prop("style","display:initial;");
          if(idx==0)
            $("#previous").prop("style","display:none;");
          if(idx==(pagenum-1))
            $("#next").prop("style","display:none;");
        }
        function showtab(flag){
          $("#detail_info").prop("style","display:none;");
          $("#detail_photos").prop("style","display:none;");
          $("#detail_map").prop("style","display:none;");
          $("#detail_reviews").prop("style","display:none;");
          $("#nav_detail_info").parent().prop("class","");
          $("#nav_detail_photos").parent().prop("class","");
          $("#nav_detail_map").parent().prop("class","");
          $("#nav_detail_reviews").parent().prop("class","");
          if(flag==0){
            $("#detail_info").prop("style","display:init;");
            $("#nav_detail_info").parent().prop("class","active");
          }
          else if(flag==1){
            $("#detail_photos").prop("style","display:init;");
            $("#nav_detail_photos").parent().prop("class","active");
          }
          else if(flag==2){
            $("#detail_map").prop("style","display:init;");
            $("#nav_detail_map").parent().prop("class","active");
          }
          else{
            $("#detail_reviews").prop("style","display:init;");
            $("#nav_detail_reviews").parent().prop("class","active");
          }
        }
        function ShowRoute(NaviStart,NaviEnd,travelMode){
          var request = {
              origin: NaviStart,
              destination: NaviEnd,
              travelMode: travelMode
          };
          directionsService.route(request,function(response, status) {
              if (status == 'OK') {
                  directionsDisplay.setDirections(response);
              } 
              else {
                  window.alert('Directions request failed due to ' + status);
              }
          });
        }
        function ShowMap(lat, lng){
          $("#map_toggle").prop("src","pegman.png");
          var mapOptions = {
            zoom: 13,
            center: {lat: lat, lng: lng}
          }
          var map = new google.maps.Map(document.getElementById('map'), mapOptions);
          directionsDisplay.setMap(map);
          directionsDisplay.setPanel(document.getElementById('panel'));
          marker = new google.maps.Marker({
              position: {lat: lat, lng: lng},
              map: map
          });
          
          panorama = map.getStreetView();
          panorama.setPosition({lat: lat, lng: lng});
          panorama.setPov(({
            heading: 265,
            pitch: 0
          }));
        }
        function sortreviews(reviews,flag){
          if(flag=="highest_rating"){
            reviews.sort(function(a, b){return b["rating"] - a["rating"]});
          }
          else if(flag=="lowest_rating"){
            reviews.sort(function(a, b){return a["rating"] - b["rating"]});
          }
          else if(flag=="most_recent"){
            reviews.sort(function(a, b){return b["time"] - a["time"]});
          }
          else if(flag=="least_recent"){
            reviews.sort(function(a, b){return a["rating"] - b["rating"]});
          }
          return reviews;
        }
        $("#sort_type").change(function(){
          $("#nav_detail_reviews").click();
        });
        $("#review_type").change(function(){
          $("#nav_detail_reviews").click();
        });
        $("#fromhere").click(function(){
          $("#location").prop("required", false);
          $("#location").prop("disabled", true);
        });
        $("#fromlocation").click(function(){
          $("#location").prop("required", true);
          $("#location").prop("disabled", false);
        });
        $(".btn.btn-default.previous").click(function(){
          pageidx-=1;
          showpage(pageidx);
        });
        $(".btn.btn-default.next").click(function(){
          pageidx+=1;
          showpage(pageidx);
        });
        $("#favorites").click(function(){
          $("#details").prop("style","display:init;");
          $("#details").prop("disabled",true);
          $("#list").prop("style","display:none;");
          $("#favorites").prop("class","btn btn-primary");
          $("#results").prop("class","btn btn-link");
          var jsonObj=JSON.parse(localStorage.getItem("favorites"));
          htmlDoc="<div class='container' id='page_favorite'><table class='table'><thead><tr><th>#</th><th>Category</th><th>Name</th><th>Address</th><th>Favorite</th><th>Details</th></tr></thead><tbody>";
          for(i=0;i<jsonObj.length;i++){
            var icon = jsonObj[i]["icon"];
            var name = jsonObj[i]["name"];
            var vicinity = jsonObj[i]["vicinity"];
            var placeid = jsonObj[i]["place_id"];
            htmlDoc+="<tr><td>"+(i+1)+"</td><td><div class = 'icon'><img src = '"+icon+"'/></div></td><td><p class='placename'>"+name+"</p></td><td><p>"+vicinity+"</p></td><td><button type='button' class='btn btn-default btn-del' id='del_fav_"+i+"'><span class='glyphicon glyphicon-trash' id='trash_fav_"+i+"'></span></button></td><td><button type='button' class='btn btn-default btn_detail' id='detail_"+i+"' ng-click='place_page=false;fav_page=false;detail_page=true;'><span class='glyphicon glyphicon-chevron-right' id='right_"+i+"'></span></button></td></tr>";
          }
          $("#res_fav_info").html(htmlDoc);
        });
        $("#list").click(function(){
          $("#details").prop("style","display:init;");
          $("#list").prop("style","display:none;");
          $("#favorites").prop("class","btn btn-link");
          $("#results").prop("class","btn btn-primary");
        });
        $("#details").click(function(){
          $("#details").prop("style","display:none;");
          $("#list").prop("style","display:init;");
        });
        $("#res_place_detail").on("click","#nav_detail_info",function(){
          showtab(0);
        });
        $("#res_place_detail").on("click","#nav_detail_photos",function(){
          showtab(1);
          if("photos" in DetailInfo["result"]){
            var htmlDoc="<div class='container' id='gallery'><div class='row masonry-grid'>";
            for(i=0;i<DetailInfo["result"]["photos"].length;i++){
              var photourl=DetailInfo["result"]["photos"][i]["photo_reference"];
              htmlDoc+="<div class='col-sm-3 col-xs-6 masonry-column' style='padding:2px;margin:0;'><a href = '"+photourl+"' target='_blank'><img src='"+photourl+"' style='width:100%;' class='img-thumbnail'/></a></div>";
            }
            htmlDoc+="</div></div>";
            $("#detail_photos").html(htmlDoc);
          }
        });
        $("#res_place_detail").on("click","#nav_detail_map",function(){
          showtab(2);
          var addr=DetailInfo["result"]["formatted_address"];
          var lat=DetailInfo["result"]["geometry"]["location"]["lat"];
          var lng=DetailInfo["result"]["geometry"]["location"]["lng"];
          $("#map_from").val( $('#fromhere').prop("checked")?"Your location": $('#location').val());
          $("#map_to").val(addr);
          ShowMap(lat,lng);
        });
        $("#res_place_detail").on("click","#nav_detail_reviews",function(){
          showtab(3);
          if($("#review_type").val()=="google_reviews"){
            var reviews=DetailInfo["result"]["reviews"];
            htmlDoc="";
            sortreviews(reviews,$("#sort_type").val());
            for(i=0;i<reviews.length;i++){
              htmlDoc+="<div class='card' style='border-color:#cdcdcd;border-style:solid;border-width:1px;background-color:#fbfbfb;margin:2px;'><div class='card-body'><table><tr><td style='vertical-align:top;'><img style='width:35px;margin:15px;' src='"+reviews[i]["profile_photo_url"]+"'></td><td><div style='margin:15px;'><div><a class='text-primary' href='"+reviews[i]["author_url"]+"' target='_blank'>"+reviews[i]["author_name"]+"</a></div><div>"+"<div class='glyphicon glyphicon-star' style='width:13px;'></div>".repeat(Math.round(reviews[i]["rating"]))+"<p class='text-muted' style='display:inline;'> "+moment.unix(reviews[i]["time"]).format("YYYY-MM-DD HH:mm:ss")+"</p></div><div><p>"+reviews[i]["text"]+"</p></div></div></td></tr></table></div></div>"
            }
          }
          else{
            for(i=0;i<DetailInfo["result"]["address_components"].length;i++){
              if(DetailInfo["result"]["address_components"][i]["types"][0]=="locality")
                 var city=DetailInfo["result"]["address_components"][i]["short_name"];
              if(DetailInfo["result"]["address_components"][i]["types"][0]=="administrative_area_level_1")
                 var state=DetailInfo["result"]["address_components"][i]["short_name"];
              if(DetailInfo["result"]["address_components"][i]["types"][0]=="country")
                 var country=DetailInfo["result"]["address_components"][i]["short_name"];
            }
            var name=DetailInfo["result"]["name"];
            var url = window.location.pathname+"?name="+name+"&city="+city+"&state="+state+"&country="+country+"&QueryType=YelpReview";
            $.ajax({url: url, success: function(jsonDoc){
              var a=1;
            }});
          }
          $("#reviews").html(htmlDoc);
        });
        $("#results").click(function(){
          $("#favorites").prop("class","btn btn-link");
          $("#results").prop("class","btn btn-primary");
        });
        $("#res_fav_info").on("click",'.btn-del',function(){
          var favoriteid=event.target.id;
          var x=favoriteid.split("_")[2];
          var oldstorage=JSON.parse(localStorage.getItem("favorites"));
          for(idx=0;idx<PlaceInfo.length;idx++){
            for(i=0;i<PlaceInfo[idx]["results"].length;i++){
              if(PlaceInfo[idx]["results"][i]["place_id"]==oldstorage[x]["place_id"]){
                $("#favorite_"+idx+"_"+i).prop("class","btn btn-default btn-star-empty");
                $("#star_"+idx+"_"+i).prop("class","glyphicon glyphicon-star-empty");
                break;
              }
            }
          }
          oldstorage.splice(x,1);
          localStorage.setItem("favorites", JSON.stringify(oldstorage));
          $("#favorites").click();
        });
        $("#res_place_info").on("click",'.btn-star-empty',function(){
          var favoriteid=event.target.id;
          var x=favoriteid.split("_")[1];
          var y=favoriteid.split("_")[2];
          var favoriteplace=PlaceInfo[x]["results"][y];
          if(typeof(Storage)!=="undefined") {
            var oldstorage=JSON.parse(localStorage.getItem("favorites"));
            oldstorage.push(favoriteplace);
            localStorage.setItem("favorites", JSON.stringify(oldstorage));
          }
          $("#favorite_"+x+"_"+y).prop("class","btn btn-default btn-star");
          $("#star_"+x+"_"+y).prop("class","glyphicon glyphicon-star");
        });
        $("#res_place_info").on("click",'.btn-star',function(){
          var favoriteid=event.target.id;
          var x=favoriteid.split("_")[1];
          var y=favoriteid.split("_")[2];
          var placeid=PlaceInfo[x]["results"][y]["place_id"];
          if(typeof(Storage)!=="undefined") {
            var oldstorage=JSON.parse(localStorage.getItem("favorites"));
            for(i=0;i<oldstorage.length;i++){
              if(oldstorage[i]["place_id"]==placeid){
                oldstorage.splice(i,1);
                break;
              }
            }
            localStorage.setItem("favorites", JSON.stringify(oldstorage));
          }
          $("#favorite_"+x+"_"+y).prop("class","btn btn-default btn-star-empty");
          $("#star_"+x+"_"+y).prop("class","glyphicon glyphicon-star-empty");
        });
        $("#detail_map").on("click",'#get_direction',function(){
          var NaviStart=$('#map_from').val()=="Your location"?{lat: GeoInfo["lat"], lng: GeoInfo["lon"]}:$('#map_from').val();
          var NaviEnd = {lat: DetailInfo["result"]["geometry"]["location"]["lat"], lng: lat=DetailInfo["result"]["geometry"]["location"]["lng"]};
          var travelMode=$("#travel_mode").val();
          ShowRoute(NaviStart,NaviEnd,travelMode);
        });
        $("#streetview_switch").click(function(){
          var toggle = panorama.getVisible();
          if (toggle == false) {
            $("#map_toggle").prop("src","mapicon.jpg");
            panorama.setVisible(true);
          } else {
            $("#map_toggle").prop("src","pegman.png");
            panorama.setVisible(false);
          }
        });
        $("body").on("click",'.btn_detail',function(){
          $("#details").prop("disabled",false);
          $("#details").prop("style","display:none;");
          showtab(0);
          $(".progress").prop("style","display:init;");
          setTimeout(function(){
            $("#bar").prop("aria-valuenow","25");
            $("#bar").prop("style","width:25%");}, 500);
          var favoriteid=event.target.id;
          $("#"+detailselected).parent().parent().prop("style","background-color:white;");
          if(favoriteid.split("_").length==3){
            var x=favoriteid.split("_")[1];
            var y=favoriteid.split("_")[2];
            var placeid=PlaceInfo[x]["results"][y]["place_id"];
            detailselected="detail_"+x+"_"+y;
          }
          else{
            var x=favoriteid.split("_")[1];
            var placeid=JSON.parse(localStorage.getItem("favorites"))[x]["place_id"];
            detailselected="detail_"+x;
          }
          var url = window.location.pathname+"?PlaceId="+placeid+"&QueryType=PlaceDetail";
          $.ajax({url: url, success: function(jsonDoc){
            $("#bar").prop("aria-valuenow","25");
            $("#bar").prop("style","width:25%");
            var htmlDoc=PlaceDetailJ2T(jsonDoc);
            setTimeout(function(){
              $("#bar").prop("aria-valuenow","75");
              $("#bar").prop("style","width:75%");}, 500);
            setTimeout(function(){
              $("#bar").prop("aria-valuenow","100");
              $("#bar").prop("style","width:100%");
              $("#list").prop("style","display:init;");
              $("#list").prop("disabled",false);
              $(".progress").prop("style","display:none;");
              $("#bar").prop("aria-valuenow","0");
              $("#bar").prop("style","width:0%");
              $("#"+detailselected).parent().parent().prop("style","background-color:#ffa700;");
              $("#detail_info").html(htmlDoc);
              $("#details").click();}, 1000);
          }});
        });
        $("#search").click(function(){
          $(".progress").prop("style","display:init;");
          setTimeout(function(){
              $("#bar").prop("aria-valuenow","25");
              $("#bar").prop("style","width:25%");}, 500);
          if($("#fromhere").prop("checked")){
            Location=GeoInfo["lat"]+","+GeoInfo["lon"];
          }
          else{
            Location=$("#location").val().replace(/[ ]/g,"+");
          }
          Flag_LatLon = $("#fromhere").prop("checked");
          var url = window.location.pathname+"?Keyword="+$("#keyword").val().replace(/[ ]/g,"+")+"&Category="+$("#category").val()+"&Distance="+$("#distance").val()+"&Location="+Location+"&Flag_LatLon="+Flag_LatLon+"&QueryType=PlaceNearBy";
          $.ajax({url: url, success: function(jsonDoc){
            $("#bar").prop("aria-valuenow","75");
            $("#bar").prop("style","width:75%");
            var htmlDoc=PlaceNearByJ2T(jsonDoc);
            $("#res_place_info").html(htmlDoc);
            setTimeout(function(){
              $("#bar").prop("aria-valuenow","100");
              $("#bar").prop("style","width:100%");}, 500);
            setTimeout(function(){
              $(".progress").prop("style","display:none;");
              $("#bar").prop("aria-valuenow","0");
              $("#bar").prop("style","width:0%");
              showpage(0);}, 1000);
          }});
          return false;
        });
      });    
    </script>
  </body>
</html>