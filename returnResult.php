<?php
include "includes/database/connection.php";

//execute the SQL query and return records
$result = mysqli_query($conn,"SELECT * FROM sites");

if (!$result) {
    printf("Error: %s\n", mysqli_error($conn));
    exit();
}
/*//fetch tha data from the database
while ($row = mysqli_fetch_assoc($result)) {
    $encoded[]=$row;
}
//header('Content-Type: application/json');
$encodedResult =  json_encode($encoded,JSON_NUMERIC_CHECK);
 echo $encodedResult;*/

$geojson = array(
    'type' => 'FeatureCollection',
    'features' => array());

while($row = mysqli_fetch_assoc($result)){

    $marker = array(
        'type' => 'Feature',
        'features' => array(
            'type' => 'Feature',
            'properties' => array(
                'name' => "".$row['site_name']."",
                'marker-color' => '#f00',
                'marker-size' => 'small',
                'LATITUDE'=>$row['latitude'],
                'LONGITUDE'=>$row['longitude'],
                "LOCATION_ZIPCODE"=> '2121',
                "Location"=> "83-85 Cresthill Rd  Brighton  MA  02135",
                "patient_no"=> $row['patient_no'],
                "score_report"=> $row['score_report'],
                "site_id"=>$row['site_id'],

                
                //'url' =>
            ),
            "geometry" => array(
                'type' => 'Point',
                'coordinates' => array(
                    $row['longitude'],
                    $row['latitude']

                )
            )
        )
    );
    array_push($geojson['features'], $marker['features']);
}
echo json_encode($geojson,JSON_NUMERIC_CHECK);

