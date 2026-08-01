<?php 
$server = "localhost";
$username = "root";
$password = "Admin@123";
$database = "c2";   // ← your database name here

$con = mysqli_connect($server, $username, $password, $database);

if(!$con) {
    die("Connection failed: " . mysqli_connect_error());
}


$sql = "SELECT * FROM user";
$result = mysqli_query($con, $sql);

// Check if records exist
if(mysqli_num_rows($result) > 0) {

    while($row = mysqli_fetch_assoc($result)) {
        echo "ID: " . $row['id'] . "<br>";
        echo "Name: " . $row['name'] . "<br>";
        echo "Email: " . $row['email'] . "<br>";
        echo "--------------------------------<br>";
    }

} else {
    echo "No users found!";
}

mysqli_close($con);
?>
