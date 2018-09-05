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
          $jsonObj["result"]["photos"][$i]["photo_reference"]="https://maps.googleapis.com/maps/api/place/photo?maxwidth=1250&photoreference=$PhotoReference&key=AIzaSyDdA0wGx6eK9PxoLJBiamwAYyDj3bSfliY";
        }
        echo json_encode($jsonObj);
      }
    }
    return false;
  }
?>