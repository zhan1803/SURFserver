<?php

$servername = "pixel.ecn.purdue.edu:4444";
$username = "jieqiong";
$password = "zjq62750539";
$dbname = "VALET";

	// Create connection
$conn = new mysqli($servername, $username, $password,$dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);

    // echo "connection failed";
} 

$sql = "SELECT Offense,Weights FROM CopProj_Weights";
$result = $conn->query($sql);
// $arr_off = array();
$arr_points = array();
if ($result->num_rows > 0)
{

    while($row = $result->fetch_assoc())
    {   
        $arr_points[$row['Offense']] = $row['Weights'];
        // $arr_off[] = $row['Offense'];
    }

    echo json_encode($arr_points, JSON_FORCE_OBJECT);
}

?>