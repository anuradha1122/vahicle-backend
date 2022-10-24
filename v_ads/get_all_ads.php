<?php
include_once '../config_database.php';
include_once '../hearder_authorization.php';

header("Access-Control-Allow-Origin: * ");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

require "../vendor/autoload.php";
use \Firebase\JWT\JWT;
use Firebase\JWT\Key;
JWT::$leeway = 60;

$conn = null;

$databaseService = new DatabaseService();
$conn = $databaseService->getConnection();

	try{

		$query = "SELECT ads_table.ADS_ID, ads_table.USER_ID, vehicle_colour.COLOUR FROM ads_table INNER JOIN vehicle_colour ON ads_table.COLOUR=vehicle_colour.ID WHERE ads_table.ACTIVE=1 LIMIT 10";
		$stmt = $conn->prepare($query);
		$stmt->execute();
		$rows = array();
			while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
				$rows[] = $row;
				}
			http_response_code(200);
			echo json_encode($rows);
		
	}catch(Exception $e){
		//http_response_code(401);
		echo json_encode(array("status" => "Access denied. please re-login",
								"error" => true,
								"s_error" => $e->getMessage()
						));
	}

?>