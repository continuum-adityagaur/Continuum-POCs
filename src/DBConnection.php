<?php

//reading DB Connection details from ini file
$config = parse_ini_file('C:/xampp/htdocs/AutomationStatus/config/dbCon.ini');

//address of the server where db is installed
$servername = $config['serverName'];

//username to connect to the db
$username = $config['username'];

//password to connect to the db
$password = $config['password'];

//name of the db under which the table is created
$dbName = $config['dbName'];

//establishing the connection to the db.
$conn = new mysqli($servername, $username, $password, $dbName);
//checking if there were any error during the last connection attempt
if ($conn->connect_error) {
	die("Connection failed: " . $conn->connect_error);
}

$appSelected= $_POST["id"];
$range = $_POST["range"];

//the SQL query to be executed
if($appSelected == "All")
$query = "select testcase_status, count(*) as count from execution_status where DATE(execution_date) > (NOW() - INTERVAL '$range' DAY) GROUP BY testcase_status";
else 
$query = "select testcase_status, count(*) as count from execution_status where prod_name='$appSelected' and DATE(execution_date) > (NOW() - INTERVAL '$range' DAY)
																																			GROUP BY testcase_status;";
//storing the result of the executed query
$result = $conn->query($query);
//initialize the array to store the processed data
$jsonArray = array();
//check if there is any data returned by the SQL Query
if ($result->num_rows > 0) {
	//Converting the results into an associative array
	while($row = $result->fetch_assoc()) {
		$jsonArrayItem = array();
		
		$jsonArrayItem['testcase_status'] = $row['testcase_status'];
		$jsonArrayItem['Count'] = $row['count'];
		
		//append the above created object into the main array.
		array_push($jsonArray, $jsonArrayItem);

	}
}
//Closing the connection to DB
$conn->close();
//set the response content type as JSON
header('Content-type: application/json');
//output the return value of json encode using the echo function.
echo json_encode($jsonArray);
?>

