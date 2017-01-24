<?php
$servername = "pixel.ecn.purdue.edu:4444";
$username = "jieqiong";
$password = "zjq62750539";
$dbname = "VALET";

// Create connection
$conn = new mysqli($servername, $username, $password,$dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$username = $_GET["username"];
$password = $_GET["password"];

$sql = "SELECT Username, Pass from CopProj_Accounts Where Username = \"$username\" && Pass = \"$password\"";
$result = $conn->query($sql);
$row = $result->fetch_assoc();

if(mysqli_num_rows($result)==0 || mysqli_num_rows($result) > 1)
{
    // header("Location: index.html");
    $result = "error";
    echo $result;
}
else
{
    // header("Location: http://web.ics.purdue.edu/~nabraham/JAVAPHP/trial7.php");
    // header("Location: mainpage.html");
    $result = "correct";
    echo $result;
}

?>


