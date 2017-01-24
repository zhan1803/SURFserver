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

$originalDate1 = "2016-01-01";
$originalDate2 = "2016-02-24";

$sql = "SELECT DISTINCT offcr_id FROM TESTING_V5 WhERE date_occu >= \"" .$originalDate1."\" AND date_occu <=\" ".$originalDate2."\"";
$result = $conn->query($sql);

if ($result->num_rows > 0)
{

	while($row = $result->fetch_assoc()) {
		$count=$count+1;
		$officers[$count] =$row["offcr_id"];
    }

    // print_r($officers);

}
else {
    echo "Error: Date wasn't right.\n";
}

$ids = $officers;

if(empty($ids))
{
    goto end;
}
$StartDate = $originalDate1;
$EndDate = $originalDate2;

// srand(time());
// $ranval = rand();
// echo $ranval;
// $fp = fopen("data/crunchbase-quarters".$ranval.".csv",'w');

$sql = "SELECT Offense, Weights FROM CopProj_Weights";
$result = $conn->query($sql);
$arr_off = array();
$arr_points = array();
if ($result->num_rows > 0)
{

    while($row = $result->fetch_assoc())
    {   
        $arr_points[$row['Offense']] = $row['Weights'];
        $arr_off[] = $row['Offense'];
    }
}
//print_r($arr_points);
// array_unshift($arr_off, "officerID");
// array_push($arr_off, "Total");
// fputcsv($fp, $arr_off);

$outputJsonArray = array();
array_push($outputJsonArray, $arr_off);
// var_dump(json_encode($outputJsonArray));

foreach($ids as $sel) {
    $arr_count = array();
    $arr_line = array();

    $arr_line['officerID'] = $sel;

    // $sql = "SELECT Category,count(*) FROM TESTING_V5
    // WHERE offcr_id =".$sel." AND date_occu >= '".$StartDate."' AND date_occu <='".$EndDate."'
    // group by Category";
    $sql = "SELECT Category,count(*) FROM TESTING_V5
    WHERE offcr_id = ".$sel." AND date_occu >= '".$StartDate."' AND date_occu <='".$EndDate."' 
    AND callsource <> 'SELF'
    GROUP BY Category"; //get number for dispatched cases
//     $sql = "SELECT Category,count(*) FROM TESTING_V2
// WHERE offcr_id = '356' AND date_occu >= '2014-01-01' AND date_occu <= '2014-01-10'
// GROUP BY Category;";
    $result = $conn->query($sql);
    $arr_line['dispatch'] = array();

    if ($result->num_rows > 0)
    {
        //echo $result->num_rows;

        while($row = $result->fetch_assoc())
        {
            $arr_count[$row['Category']] =  $row['count(*)'];
         
        }
        
     //print_r($arr_count);
        
        foreach($arr_points as $k => $v)
        {
            if( !empty($arr_count[$k])) {
                // $arr_line[$k] = $v * $arr_count[$k];
                $arr_line['dispatch'][$k] =  $arr_count[$k];        
                // if($min > $arr_line[$k])
                // {
                //  $min = $arr_line[$k];
                // }
                // else if($max < $arr_line[$k])
                // {
                //  $max = $arr_line[$k];
                // }
            } else {
                $arr_line['dispatch'][$k] = 0;
            }
            
            
        }
        
        
        // $TOTAL = array_sum($arr_line);
        //echo "TOTAL=".$TOTAL;
        // array_unshift($arr_line, $sel);
        // array_push($arr_line, $TOTAL);
        // fputcsv($fp, $arr_line);
    }  else {
        foreach($arr_points as $k => $v)
        {
            $arr_line['dispatch'][$k] = 0;        
        }
    }

    $sql = "SELECT Category,count(*) FROM TESTING_V5
    WHERE offcr_id = ".$sel." AND date_occu >= '".$StartDate."' AND date_occu <='".$EndDate."' 
    AND callsource = 'SELF'
    GROUP BY Category"; //get number for initiative cases

    $result = $conn->query($sql);
    $arr_line['self'] = array();

    if ($result->num_rows > 0)
    {
        //echo $result->num_rows;

        while($row = $result->fetch_assoc())
        {
            $arr_count[$row['Category']] =  $row['count(*)'];
         
        }
        
     //print_r($arr_count);
        
        foreach($arr_points as $k => $v)
        {
            if( !empty($arr_count[$k])) {
                // $arr_line[$k] = $v * $arr_count[$k];
                $arr_line['self'][$k] =  $arr_count[$k];        
                // if($min > $arr_line[$k])
                // {
                //  $min = $arr_line[$k];
                // }
                // else if($max < $arr_line[$k])
                // {
                //  $max = $arr_line[$k];
                // }
            } else {
                $arr_line['self'][$k] = 0;
            }
            
            
        }
        
        
        // $TOTAL = array_sum($arr_line);
        //echo "TOTAL=".$TOTAL;
        // array_unshift($arr_line, $sel);
        // array_push($arr_line, $TOTAL);
        // fputcsv($fp, $arr_line);
    }  else {
        foreach($arr_points as $k => $v)
        {
            $arr_line['self'][$k] = 0;        
        }
    }

    array_push($outputJsonArray, $arr_line);
}

echo json_encode($outputJsonArray, JSON_FORCE_OBJECT);

end:
$conn->close();
exit();
?>