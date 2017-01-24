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

$fp = fopen("data/officierCases.csv",'w');

$sql = "SELECT inci_id, lwchrgid, Category, date_occu, offcr_id, callsource from TESTING_V5";
$result = $conn->query($sql);

if ($result->num_rows > 0)
{
	$arr_off = array();
	$k = 0;
    while($row = $result->fetch_assoc())
    {   
        $arr_off[$k]['inci_id'] = $row['inci_id'];
        $arr_off[$k]['lwchrgid'] = $row['lwchrgid'];
        $arr_off[$k]['Category'] = $row['Category'];
        $arr_off[$k]['date_occu'] = $row['date_occu'];
        $arr_off[$k]['offcr_id'] = $row['offcr_id'];
        $arr_off[$k]['callsource'] = $row['callsource'];
        print_r($row);
        // fputcsv($fp, $arr_off[$k]);
        $k = $k + 1;
    }
    // print_r($arr_off);
    fputcsv($fp, array('inci_id','lwchrgid','Category','date_occu','offcr_id','callsource'));
    foreach($arr_off as $arr_line) {
    	// print_r($arr_line);
    	fputcsv($fp, $arr_line);
	}
   	
   	fclose($fp);
   	$result->free();
}



end:
$conn->close();
exit();

?>