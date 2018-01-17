<?php

/*Connet MySQL */
$dsn = "mysql:host=localhost:3306;dbname=s402410091_hw4";
$db = new PDO($dsn, "root","");
$db->query("set names utf8");

/*Create trainstation tabel */

  $db->query( 'CREATE TABLE IF NOT EXISTS trainstation (
  X FLOAT(8) NULL,
  Y FLOAT(13) NULL,
  landmarkid DOUBLE NULL,
  landmarkco DOUBLE NULL,
  landmarkna VARCHAR(45) CHARACTER SET utf8 NULL,
  landmarkad INT NULL,
  address VARCHAR(70) CHARACTER SET utf8 NULL)DEFAULT CHARACTER SET = utf8;');
/* Input csv data*/

$csvfile = "trainstation.csv";
$stmt = $db->prepare('SELECT * FROM trainstation');
$stmt->execute();
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$row){
    $files = fopen($csvfile,"r");
    $data = fgetcsv($files);

    while(! feof($files)){
        $data = fgetcsv($files);
        $sql = "INSERT INTO trainstation
        (X,Y,landmarkid,landmarkco,landmarkna,landmarkad,address)
        VALUES (?,?,?,?,?,?,?)";
        $stmt = $db->prepare($sql);
        $stmt -> execute($data);
    }
}

/*Write your SQL code*/


    $buffersize = (int)$_POST['Buffersize'];
    $lat =$_POST['lat'];
    $lng =$_POST['lng'];

    $sql="SELECT X,Y,address,landmarkna ,(6371* acos(cos( radians(?) ) * cos( radians(Y)) * cos( radians(X) - radians(?)) +
     sin( radians(?)) * sin(radians(Y) ) ) ) AS distance
          FROM trainstation
          HAVING distance <= ?
          ORDER BY distance
          LIMIT 0,10;";
    $stmt = $db->prepare($sql);
    $stmt -> execute(array($lat,$lng,$lat,$buffersize));
    $datas = $stmt->fetchall(PDO::FETCH_ASSOC);

    $results = [
    'length' => count($datas),
    'landmarkna' => [
        'type' => 'FeatureCollection',
        'features' => array_map(function ($data) {
          return [
              'type' => 'Feature',
              'properties' => [
                  'landmarkna' => $data['landmarkna'],
                  'address' => $data['address'],
                  'distance' => round($data['distance'],5)
              ],
              'geometry' => [
                  'type' => 'Point',
                  'coordinates' => [(float)$data['X'], (float)$data['Y']]
              ],
          ];
        }, $datas),
    ],
];

echo json_encode($results,384);






/*Write json type*/

/*$result=[];





echo json_encode($result,384);*/
?>
