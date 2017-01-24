<?php
date_default_timezone_set('America/New_York');

$servername = "pixel.ecn.purdue.edu:4444";
$username = "jieqiong";
$password = "zjq62750539";
$dbname = "VALET";
$count = 0;
$officers = array();

// Create connection
$conn = new mysqli($servername, $username, $password,$dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} 


$originalDate1=$_GET["from"];
$newdate1=date("Y/m/d ",strtotime($originalDate1));
// echo $newdate1; 

$originalDate2=$_GET["to"];
$newdate2=date("Y/m/d ",strtotime($originalDate2));
// echo $newdate2; 

// $sql = "TRUNCATE TABLE Variables";
// $result = $conn->query($sql);

// $sql = "INSERT INTO Variables (Name, Value) VALUES (\"StartDate\",\"".$originalDate1."\"),(\"EndDate\",\"". $originalDate2."\")";
// $result = $conn->query($sql);

// $sql = "SELECT DISTINCT offcr_id FROM TESTING WHERE date_occu >= \"" .$originalDate1."\" AND date_occu <=\" ".$originalDate2."\" GROUP BY inci_id, lwchrgid";
$sql = "SELECT DISTINCT offcr_id FROM TESTING_V5 WhERE date_occu >= \"" .$originalDate1."\" AND date_occu <=\" ".$originalDate2."\"";
$result = $conn->query($sql);



if ($result->num_rows > 0)
{
	// while($row = $result->fetch_assoc()) {

	// }
	$tmp_arr = array();

	while($row = $result->fetch_assoc()) {
		$count=$count+1;
		$officers[$count] =$row["offcr_id"];
		array_push($tmp_arr, $row["offcr_id"]);
    }

	echo json_encode($tmp_arr, JSON_FORCE_OBJECT);
}

else {
    echo "Error: Date wasn't right.\n";
}

// // $sql = "INSERT INTO Variables (Name, Value) VALUES (\"Count\",\"".$result->num_rows."\")";
// // $result = $conn->query($sql);
// // $arraystring = serialize($officers);
// // //echo $arraystring;

// // $sql = "INSERT into Variables(Name, Value) VALUES('Officers','$arraystring')";
// // $result = $conn->query($sql);

?>


