<?php
$servername = "pixel.ecn.purdue.edu:4444";
$username = "nabraham";
$password = "y6FtaaDa98qGZTX8";
$dbname = "SpecialDB";

// Create connection
$conn = new mysqli($servername, $username, $password,$dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);

    //echo "connection failed";
} 

$sql = "SELECT Name,Value FROM Variables WHERE Name = \"Officers\"";
$result = $conn->query($sql);
$row = $result->fetch_assoc();
$arr_off = unserialize($row["Value"]);

$counter = 1;

//print_r($arr_off);

// if(isset($_POST['submit']))
// {
    $ids = $_POST['ids'];
    //print_r($ids);
    $values = array();
    foreach($arr_off as $selection)
    {
        if(in_array($selection, $ids))
        {
            $values[$counter] = $selection;
            $counter = $counter + 1;
        }
       
    }
    
// }


if(empty($values))
{
   echo "Officers werent checked";
    goto end;
}

//print_r($values);
srand(time());
$ranval = rand();
echo $ranval;
$fp = fopen("data/crunchbase-quarters".$ranval.".csv",'w');

$sql = "SELECT Offense,Points FROM Points";
$result = $conn->query($sql);
$arr_off = array();
$arr_points = array();
if ($result->num_rows > 0)
{

    while($row = $result->fetch_assoc())
    {   
        $arr_points[$row['Offense']] = $row['Points'];
        $arr_off[] = $row['Offense'];
    }
}
//print_r($arr_points);
array_unshift($arr_off, "weekending");
array_push($arr_off, "Total");
fputcsv($fp, $arr_off);

$sql = "SELECT Name,Value FROM Variables WHERE Name = \"StartDate\"";
$result = $conn->query($sql);
$row = $result->fetch_assoc();
$StartDate = $row["Value"];
//echo $StartDate;

$sql = "SELECT Name,Value FROM Variables WHERE Name = \"EndDate\"";
$result = $conn->query($sql);
$row = $result->fetch_assoc();
$EndDate = $row["Value"];
//echo $EndDate;
$min = 0;
$max = 0;


foreach($values as $sel)
{
    
$arr_count = array();
$arr_line = array();

$sql = "SELECT Category,count(*) FROM Officerdata
WHERE offcr_id =".$sel."&& date_rept >=\"".$StartDate."\"&& date_rept <=\"".$EndDate."\"
group by Category";
$result = $conn->query($sql);

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
            $arr_line[$k] = $v * $arr_count[$k];        
    		if($min > $arr_line[$k])
    		{
    		 $min = $arr_line[$k];
    		}
    		else if($max < $arr_line[$k])
    		{
    		 $max = $arr_line[$k];
    		}
        } else {
            $arr_line[$k] = 0;
        }
		
        
    }
    
    
    
$TOTAL = array_sum($arr_line);
//echo "TOTAL=".$TOTAL;
array_unshift($arr_line, $sel);
array_push($arr_line, $TOTAL);
fputcsv($fp, $arr_line);
}

}
$sql = "INSERT into Variables(Name, Value) VALUES('max','$max')";
$result = $conn->query($sql);

$sql = "INSERT into Variables(Name, Value) VALUES('min','$min')";
$result = $conn->query($sql);
end:
$conn->close();
//header('Location: http://web.ics.purdue.edu/~nabraham/JAVAPHP/DualCharts%20bug%20fixed/FQC/heatmap.html');
exit();

?>


