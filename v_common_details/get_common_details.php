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
		$query =   "SELECT BRAND_ID, BRAND, IMG_URL FROM vehicle_brands WHERE ACTIVE = 1;
                    SELECT MODEL_ID, MAKE, V_TYPE, MODEL FROM vehicle_model WHERE ACTIVE = 1;
                    SELECT COLOR_ID, COLOUR FROM vehicle_colour WHERE ACTIVE = 1;
                    SELECT SF_ID, FEATURE FROM standard_features WHERE ACTIVE = 1;
                    SELECT VTYPE_ID, V_TYPE FROM vehicle_type WHERE ACTIVE = 1; 
					SELECT CITY_ID, DISTRICT_ID, NAME_EN FROM `cities` WHERE 1; 
					SELECT DISTRICT_ID, PROVINCE_ID, NAME_EN FROM districts WHERE 1;
					SELECT PROVINCE_ID, NAME_EN FROM provinces WHERE 1;";

       // $conn->setAttribute(PDO::ATTR_EMULATE_PREPARES, 1);
        $stmt = $conn->prepare($query);
        $stmt->execute();
        $rows = array();

        do {
            $rows[] = $stmt->fetchAll(PDO::FETCH_ASSOC);
            } while ($stmt->nextRowset());

        http_response_code(200);
        echo json_encode($rows);

        $conn = null;
		
	}catch(Exception $e){
		//http_response_code(401);
		echo json_encode(array("status" => "Access denied. please re-login",
								"error" => true,
								"s_error" => $e->getMessage()
						));
	}
?>