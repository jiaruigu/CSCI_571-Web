<html>
    <head>
        <title>hw6</title>
        <meta charset="UTF-8"/>
        <style type = "text/css">
            #container{
                width: 70%;
                border-width: thick;
                border-color:#cccccc;
                border-style: solid;
                text-align: left;
                background-color: #fafafa;
            }
            #container h1{
                text-align: center;
                font-style: italic;
                border-bottom-width:medium;
                border-bottom-color:#b7b7b7;
                border-bottom-style: solid; 
            }
            #res table th img{
                width: 5%;
            }
            #res table {
                width: 70%;
                border-collapse: collapse;
            }

            #res table, #res td, #res th {
                border: 1px solid #b7b7b7;
            }
            .icon{
                text-align: center;
            }
            .icon img{
                width: 30px;
            }
            .photo{
                text-align: center;
            }
            .photo img{
                width: 90%;
                padding: 5%;
            }
            #showreviews, #showphotos, #hidereviews, #hidephotos, #reviewtable, #phototable{
                display: none;
            }
            #showreviews img, #showphotos img, #hidereviews img, #hidephotos img{
                width: 5%;
                cursor: pointer;
            }
            #p_norecord{
                width: 70%;
                background-color: #cccccc;
                border-style: solid;
                border-color: #b7b7b7;
                border-width: 2px;
                text-align: center;
            }
            #p_nophoto,#p_noreview{
                width: 70%;
                border-style: solid;
                border-color: #b7b7b7;
                border-width: 2px;
                text-align: center;
            }
            a{
                color: black;
                text-decoration: none;
            }
            #map {
                height: 30%;
                width: 30%;
                display: none;
                z-index: 1;
                position: absolute;
            }
            #travelModeSelector{
                width: 10%;
                display: none;
                z-index: 2;
                position: absolute;
                background-color: #d9d9d9;
            }
            #selectwalk, #selectbike, #selectdrive{
                padding: 5%;
            }
        </style>
    </head>
    <body>
        <center><div id = "container">
            <form action = "" method = "post" id = "form" onsubmit = "PlaceNearBy();return false;">
                <h1>Travel and Entertainment Search</h1>
                <b>Keyword </b><input type="text" name="Keyword" id = "keyword" required/><br/>
                <b>Category</b>
                <select name = "Category" id = "category">
                    <option id = "default" value="delfault" selected = "selected">default</option>
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
                </select>
                <table border="0"><tr>
                <td style="vertical-align:text-top;"><div id = "discription"><b>Distance(miles) </b><input type = "text" name="Distance" value = 10 id = "distance" pattern="^[0-9]+(.[0-9]*)?$" required/>
                    <b>from</b></div></td>
                <td><div id = "radiobutton">
                    <input type = "radio" id = "fromhere" name = "From" checked = "checked" onclick = "LocationRequired(false)"/>Here<br/>
                    <input type = "radio" name = "From" id = "fromlocation" onclick = "LocationRequired(true)"/><input type = "text" name = "Location" id = "location" placeholder="location" disabled/>
                </div></td><br/>
                </tr></table>
                <input id = "search" name = "Search" type = "submit" value = "Search" disabled/>
                <input id = "reset" name = "Clear" type = "button" value = "Clear" onclick = "ResetForm()"/>
            </form>
        </div><br/>
        <div id='res'></div></center>
        <script type = "text/javascript">
            function ResetForm(){
                document.getElementById("default").selected = true;
                document.getElementById("keyword").value = "";
                document.getElementById("distance").value = 10;
                document.getElementById("location").disabled = true;
                document.getElementById("location").value = "";
                document.getElementById("fromhere").checked = true;
            }
            function getElementLeft(ElementId){
                element = document.getElementById(ElementId);
                var actualLeft = element.offsetLeft;
                var current = element.offsetParent;
                while (current !== null){
                    actualLeft += current.offsetLeft;
                    current = current.offsetParent;
                }
                return actualLeft;
            }
            function getElementTop(ElementId){
                element = document.getElementById(ElementId)
                var actualTop = element.offsetTop;
                var current = element.offsetParent;

                while (current !== null){
                  actualTop += current.offsetTop;
                  current = current.offsetParent;
                }
                return actualTop;
            }
            function LocationRequired(boolval){
                var location = document.getElementById("location");
                location.required = boolval;
                location.disabled = !boolval;
                if(!boolval){
                    location.value = "";
                }
            }
            function loadJSON(url){
                var xmlhttp=new XMLHttpRequest();
                xmlhttp.open("GET",url,false);
                if (xmlhttp == null){
                    alert("Connection Failed.");
                    return null;
                }
                try{
                    xmlhttp.send();
                }
                catch(err){
                    return null;
                }
                var jsonDoc = xmlhttp.responseText;
                return jsonDoc;
            }
            function loadGeoInfo(){
                return ParseJson("http://ip-api.com/json");
            }
            function ParseJson(url){
                var jsonDoc = loadJSON(url);
                if(jsonDoc == null){
                    alert("No Such File.");
                    return;
                }
                try{
                    var jsonObj = JSON.parse(jsonDoc);
                    return jsonObj;
                }
                catch(err){
                    alert("Corrupted File.\nError Infomation: " + err.message);
                    return;
                }
            }
            function GetDom(){
                Keyword = document.getElementById("keyword").value.replace(/[ ]/g,"");
                Category = document.getElementById("category").value;
                Distance = (parseFloat(document.getElementById("distance").value)*1609.344).toString();
                if(document.getElementById("fromhere").checked){
                    Location = GeoInfo["lat"]+","+GeoInfo["lon"];
                    Flag_LatLon = true;
                }
                else{
                    Location = document.getElementById("location").value.replace(/[ ]/g,"");
                    Flag_LatLon = false;
                }
            }
            function PlaceNearBy(){
                GetDom();
                var url = window.location.pathname + "?Keyword=" + Keyword + "&Category=" + Category + "&Distance=" + Distance + "&Location=" + Location + "&Flag_LatLon=" + Flag_LatLon + "&QueryType=PlaceNearBy";
                Query(url,0);
            }
            function PlaceDetail(PlaceId){
                var url = window.location.pathname+"?PlaceId=" + PlaceId.replace(/[ ]/g,"") + "&QueryType=PlaceDetail";
                Query(url,1);
            }
            function initMap(lat, lng, i) {
                directionsService = new google.maps.DirectionsService();
                directionsDisplay = new google.maps.DirectionsRenderer();
                var mapOptions = {
                    zoom: 13,
                    center: {lat: lat, lng: lng}
                }
                var map = new google.maps.Map(document.getElementById('map'), mapOptions);
                directionsDisplay.setMap(map);
                document.getElementById("map").style.display = "block";
                document.getElementById("map").style.left = getElementLeft("entry"+i);
                document.getElementById("map").style.top = getElementTop("entry"+i)+20;
                document.getElementById("travelModeSelector").style.display = "block";
                document.getElementById("travelModeSelector").style.left = getElementLeft("entry"+i);
                document.getElementById("travelModeSelector").style.top = getElementTop("entry"+i)+20;
                marker = new google.maps.Marker({
                    position: {lat: lat, lng: lng},
                    map: map
                });
            }
            function TravelModeSelect(mode){
                document.getElementById("selectwalk").style.backgroundColor="#d9d9d9";
                document.getElementById("selectbike").style.backgroundColor="#d9d9d9";
                document.getElementById("selectdrive").style.backgroundColor="#d9d9d9";
                if(mode == 0){
                    travelMode = "WALKING";
                    document.getElementById("selectwalk").style.backgroundColor="#b7b7b7";
                }
                else if(mode == 1){
                    travelMode = "BICYCLING";
                    document.getElementById("selectbike").style.backgroundColor="#b7b7b7";
                }
                else{
                    travelMode = "DRIVING";
                    document.getElementById("selectdrive").style.backgroundColor="#b7b7b7";
                }
                marker.setMap(null);
                ShowRoute(travelMode);
            }
            function ShowRoute(travelMode){
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
            function ShowMap(lat, lng, i){
                if(document.getElementById("map").style.display == ""||document.getElementById("map").style.display == "none"||(document.getElementById("map").style.display == "block" && i!=PreviousEntry)){
                    NaviStart = document.getElementById('fromhere').checked? {lat: GeoInfo["lat"], lng: GeoInfo["lon"]} : document.getElementById('location').value;
                    NaviEnd = {lat: lat, lng: lng};
                    initMap(lat,lng,i);
                }
                else{
                    document.getElementById("map").style.display = "none";
                    document.getElementById("travelModeSelector").style.display = "none";
                    document.getElementById("selectwalk").style.backgroundColor="#d9d9d9";
                    document.getElementById("selectbike").style.backgroundColor="#d9d9d9";
                    document.getElementById("selectdrive").style.backgroundColor="#d9d9d9";
                }
                PreviousEntry = i;
            }
            function PlaceNearByJ2T(jsonDoc){
                jsonObj = JSON.parse(jsonDoc);
                var htmlDoc = "<div id = 'response'><div id = travelModeSelector><div id = 'selectwalk'><a onclick = 'TravelModeSelect(0);return false;' href = ''>Walk there</a></div><div id = 'selectbike'><a onclick = 'TravelModeSelect(1);return false;' href = ''>Bike there</a></div><div id = 'selectdrive'><a  onclick = 'TravelModeSelect(2);return false;' href = ''>Drive there</a></div></div><div id = 'map'></div><table id = 'placenearbytable'><tr><th>Category</th><th>Name</th><th>Address</th></tr>";
                if (jsonObj["status"] == "ZERO_RESULTS"||jsonObj["results"].length == 0){
                    return "<p id = 'p_norecord'>No records have been found</p>";
                }
                for(i = 0; i < jsonObj["results"].length; i++){
                    var icon = jsonObj["results"][i]["icon"];
                    var name = jsonObj["results"][i]["name"];
                    var vicinity = jsonObj["results"][i]["vicinity"];
                    var placeid = jsonObj['results'][i]['place_id'];
                    var lat = jsonObj['results'][i]["geometry"]["location"]["lat"];
                    var lng = jsonObj['results'][i]["geometry"]["location"]["lng"];
                    htmlDoc += "<tr><td><div class = 'icon'><img src = '" + icon + "'/></div></td><td><a onclick = 'PlaceDetail(&quot "+jsonObj['results'][i]['place_id']+"&quot);return false;' href = ''>" + name + "</a></td><td><a id = 'entry" + i + "' onclick = 'ShowMap(" + lat + "," + lng + "," + i + ");return false;' href = ''>" + vicinity + "</a></td></tr>";
                }
                htmlDoc += "</table></div>";
                return htmlDoc;
            }
            function ShowReviewTable(boolval){
                if(boolval){
                    document.getElementById("reviewtable").style.display = "table";
                    document.getElementById("showreviews").style.display = "none";
                    document.getElementById("hidereviews").style.display = "block";
                }
                else{
                    document.getElementById("reviewtable").style.display = "none";
                    document.getElementById("showreviews").style.display = "block";
                    document.getElementById("hidereviews").style.display = "none";
                }
            }
            function ShowPhotoTable(boolval){
                if(boolval){
                    document.getElementById("phototable").style.display = "table";
                    document.getElementById("showphotos").style.display = "none";
                    document.getElementById("hidephotos").style.display = "block";
                }
                else{
                    document.getElementById("phototable").style.display = "none";
                    document.getElementById("showphotos").style.display = "block";
                    document.getElementById("hidephotos").style.display = "none";
                }
            }
            function PlaceDetailJ2T(jsonDoc){
                jsonObj = JSON.parse(jsonDoc);
                //reviews
                var htmlDoc = "<div id = 'response'><b><p>" + jsonObj["result"]["name"] + "</p></b><div id='showreviews'><p>click to show reviews</p><img src='http://cs-server.usc.edu:45678/hw/hw6/images/arrow_down.png' onclick ='ShowReviewTable(true)'></div><div id='hidereviews'><p>click to hide reviews</p><img src='http://cs-server.usc.edu:45678/hw/hw6/images/arrow_up.png' onclick ='ShowReviewTable(false)'></div><table id = 'reviewtable'>";
                if (!("reviews" in jsonObj["result"])||jsonObj["result"]["reviews"].length == 0){
                    htmlDoc += "<tr><th>No Reviews Found</th></tr>";
                }
                else{
                    for(i = 0; i < Math.min(jsonObj["result"]["reviews"].length, 5); i++){
                        var icon = jsonObj["result"]["reviews"][i]["profile_photo_url"];
                        var name = jsonObj["result"]["reviews"][i]["author_name"];
                        var text = jsonObj["result"]["reviews"][i]["text"];
                        if(icon == null)
                            htmlDoc += "<tr><th>"+ name + "</th></tr><tr><td>" + text + "</td></tr>";
                        else
                            htmlDoc += "<tr><th><img src = '" + icon + "'/>"+ name + "</th></tr><tr><td>" + text + "</td></tr>";
                    }
                }
                //photos
                htmlDoc += "</table><div id='showphotos'><p>click to show photos</p><img src='http://cs-server.usc.edu:45678/hw/hw6/images/arrow_down.png' onclick ='ShowPhotoTable(true)'></div><div id='hidephotos'><p>click to hide photos</p><img src='http://cs-server.usc.edu:45678/hw/hw6/images/arrow_up.png' onclick ='ShowPhotoTable(false)'></div><table id = 'reviewtable'><table id = 'phototable'>";
                if (!("photos" in jsonObj["result"])||jsonObj["result"]["photos"].length == 0){
                    htmlDoc += "<tr><th>No Photos Found</th></tr>";
                }
                else{
                    for(i = 0; i < Math.min(jsonObj["result"]["photos"].length, 5); i++){
                        var imgurl = window.location.href.substring(0, window.location.href.lastIndexOf("/")) + "/" + jsonObj["result"]["place_id"] +"image" + i + ".jpg"
                        htmlDoc += "<tr><td><div class = 'photo'><a href = '" + imgurl + "'><img src = '" + imgurl + "'/></div></td></tr>";
                    }
                }
                htmlDoc += "</table></div>";
                return htmlDoc;  
            }
            function Query(url, flag){
                xhttp = new XMLHttpRequest();
                xhttp.onreadystatechange = function(){
                    if (this.readyState == 4 && this.status == 200) {
                        parser = new DOMParser();
                        htmlDoc = parser.parseFromString(this.responseText,"text/html");
                        var Response = htmlDoc.getElementById("response").innerHTML;
                        if(flag == 0){
                            res.innerHTML = PlaceNearByJ2T(Response);
                        }
                        else if(flag == 1){
                            res.innerHTML = PlaceDetailJ2T(Response);
                            document.getElementById("showreviews").style.display = "block";
                            document.getElementById("showphotos").style.display = "block";
                        }
                    }
                };
                xhttp.open("GET",url,true);
                xhttp.send();
            }
            var Keyword, Category, Distance, Location, Flag_LatLon, directionsService, directionsDisplay, NaviStart, NaviEnd, marker;
            var PreviousEntry = 0;
            var form = document.getElementById('form');
            var res = document.getElementById("res");
            var search = document.getElementById("search");
            var GeoInfo = loadGeoInfo();
            search.removeAttribute("disabled");
        </script>
        <script async defer
            src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDdA0wGx6eK9PxoLJBiamwAYyDj3bSfliY">
        </script>
        <?php
            if (isset($_REQUEST["QueryType"])){
                $QueryType = $_REQUEST["QueryType"];
                if($QueryType == "PlaceNearBy"){
                    $Keyword = $_REQUEST["Keyword"];
                    $Category = $_REQUEST["Category"];
                    $Distance = $_REQUEST["Distance"];
                    $Location = $_REQUEST["Location"];
                    $Flag_LatLon = $_REQUEST["Flag_LatLon"];
                    if($Keyword!="" && $Category!="" && $Distance!="" && $Location!="" && $Flag_LatLon!=""){
                        if($Flag_LatLon == "false"){
                            $jsonurl = "https://maps.googleapis.com/maps/api/geocode/json?address=$Location&key=AIzaSyDdA0wGx6eK9PxoLJBiamwAYyDj3bSfliY";
                            $json = file_get_contents($jsonurl);
                            $jsonObj = json_decode($json,true);
                            $Location = $jsonObj["results"][0]["geometry"]["location"]["lat"].",".$jsonObj["results"][0]["geometry"]["location"]["lng"];
                        }
                        if($Category == "default"){
                            $jsonurl = "https://maps.googleapis.com/maps/api/place/nearbysearch/json?location=$Location&radius=$Distance&keyword=$Keyword&key=AIzaSyDdA0wGx6eK9PxoLJBiamwAYyDj3bSfliY";
                        }
                        else{
                            $jsonurl = "https://maps.googleapis.com/maps/api/place/nearbysearch/json?location=$Location&radius=$Distance&type=$Category&keyword=$Keyword&key=AIzaSyDdA0wGx6eK9PxoLJBiamwAYyDj3bSfliY";
                        }
                        $json = file_get_contents($jsonurl);
                    }
                    $html = "<div id = 'response'>$json</div>";
                    echo $html;
                }
                else if($QueryType == "PlaceDetail"){
                    $PlaceId = $_REQUEST["PlaceId"];
                    if($PlaceId!=""){
                        $jsonurl = "https://maps.googleapis.com/maps/api/place/details/json?placeid=$PlaceId&key=AIzaSyDdA0wGx6eK9PxoLJBiamwAYyDj3bSfliY";
                        $json = file_get_contents($jsonurl);
                        $jsonObj = json_decode($json,true);
                        $PicNum = 0;
                        foreach(glob("*.jpg") as $file)
                            if(is_file($file))
                                @unlink($file);
                        foreach($jsonObj["result"]["photos"] as $photo){
                            if($PicNum>4){
                                break;
                            }
                            $PhotoReference = $photo["photo_reference"];
                            $img = file_get_contents("https://maps.googleapis.com/maps/api/place/photo?maxwidth=1250&photoreference=$PhotoReference&key=AIzaSyDdA0wGx6eK9PxoLJBiamwAYyDj3bSfliY");
                            file_put_contents($PlaceId."image".$PicNum.".jpg", $img);
                            $PicNum+=1;
                        }
                    }
                    $html = "<div id = 'response'>$json</div>";
                    echo $html;
                }
            }
        ?>
    </body>
</html>