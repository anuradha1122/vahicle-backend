<?php
include_once '../config_database.php';
include_once '../hearder_authorization.php';

header("Access-Control-Allow-Origin: * ");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

require "../vendor/autoload.php";
use \Firebase\JWT\JWT;
use Firebase\JWT\Key;
JWT::$leeway = 60;

$conn = null;

$userId = '';
$name = '';
$phone = '';
$province = '';
$distric = '';
$city = '';
$make = '';
$model = '';
$type = '';
$colour = '';
$fuel = '';
$engine = '';
$bodyType = '';
$doors = '';
$transmission = '';
$manuYear = '';
$regYear = '';
$vCondition = '';
$maileage = '';
$stdFeaturs = '';
$otherD = '';
$price = '';
$leasing = '';
$biding = '';
$imgUrl = '';
$updateTime = '';
$active = '';

$databaseService = new DatabaseService();
$conn = $databaseService->getConnection();

$data = json_decode(file_get_contents("php://input"),true);
$jwt = getBearerToken();
$key = getSecretKey();

if($jwt){
	try{
		$decoded = JWT::decode($jwt, new Key($key, 'HS256'));
		//echo $jwt;
		if($data){
			$userId = $data["personalData"]["id"];
			$name = $data["personalData"]["name"];
			$phone = $data["personalData"]["phone"];
			$province = $data["personalData"]["province"];
			$distric = $data["personalData"]["distric"];
			$city = $data["personalData"]["city"];
			$make = $data["vehicleData"]["vehicleMake"];
			$model = $data["vehicleData"]["vehicleModelNo"];
			$type = $data["vehicleData"]["vehicleType"];
			$colour = $data["vehicleData"]["vehicleColor"];
			$fuel = $data["vehicleData"]["fuelType"];
			$engine = $data["vehicleData"]["engineCapacity"];
			$bodyType = $data["vehicleData"]["vehicleBodyType"];
			$doors = '0';
			$transmission = $data["vehicleData"]["transmission"];
			$manuYear = $data["vehicleData"]["vehicleMfYear"];
			$regYear = $data["vehicleData"]["vehicleRegYear"];
			$vCondition = $data["vehicleData"]["vehicleCondition"];
			$maileage = $data["vehicleData"]["mileage"];
			$otherD = $data["vehicleData"]["otherD"];
			$stdFeaturs = $data["standardFeatures"];
			$imgUrl = $data["imgURLS"];
			$price = $data["pricing"]["price"];
			$leasing = $data["pricing"]["leasing"];
			$biding = '0';
			$updateTime = date('Y-m-d H:i:s');
			$active = 1;
			$adsRefNo = uniqid();

			$stringSTDF = implode(',',$stdFeaturs);
			
			//echo $adsRefNo;
			echo json_encode(array($imgUrl));
			//echo json_encode(count($imgUrl));
			echo json_encode(implode(',',$stdFeaturs));
			
			$table1_name = 'ads_table';
			$table2_name = 'ads_stdf';

				$query = "INSERT INTO " . $table1_name . "
								SET ADS_ID = NULL,
									ADS_REF_NO = :ads_ref_no,
									USER_ID = :user_id,
									NAME = :name,
									PHONE = :phone,
									PROVINCE = :province,
									DISTRIC = :distric,
									CITY = :city,
									MAKE = :make,
									MODEL = :model,
									TYPE = :type,
									COLOUR = :colour,
									FUEL = :fuel,
									ENGINE = :engine,
									BODY_TYPE = :body_type,
									DOORS = :doors,
									TRANSMISSION = :trasmission,
									MANU_YEAR = :manu_year,
									REG_YEAR = :reg_year,
									V_CONDITION = :v_condition,
									MILEAGE = :maileage,
									OTHER_D = :other_d,
									PRICE = :price,
									LEASING = :leasing,
									BIDING = :biding,
									UPDATE_TIME = :update_time,
									ACTIVE = :active ;
					INSERT INTO " . $table2_name . "
									SET STDF_ID  = NULL,
									ADS_REF_NO = :ads_ref_no,
									SF_ID = :stdFeaturs,
									ACTIVE = :active ; ";

				$stmt = $conn->prepare($query);					
				
				$stmt->bindParam(':user_id', $userId);
				$stmt->bindParam(':ads_ref_no', $adsRefNo);
				$stmt->bindParam(':name', $name);
				$stmt->bindParam(':phone', $phone);
				$stmt->bindParam(':province', $province);
				$stmt->bindParam(':distric', $distric);
				$stmt->bindParam(':city', $city);
				$stmt->bindParam(':make', $make);
				$stmt->bindParam(':model', $model);
				$stmt->bindParam(':type', $type);
				$stmt->bindParam(':colour', $colour);
				$stmt->bindParam(':fuel', $fuel);
				$stmt->bindParam(':engine', $engine);
				$stmt->bindParam(':body_type', $bodyType);
				$stmt->bindParam(':doors', $doors);
				$stmt->bindParam(':trasmission', $transmission);
				$stmt->bindParam(':manu_year', $manuYear);
				$stmt->bindParam(':reg_year', $regYear);
				$stmt->bindParam(':v_condition', $vCondition);
				$stmt->bindParam(':maileage', $maileage);
				$stmt->bindParam(':other_d', $otherD);
				$stmt->bindParam(':price', $price);
				$stmt->bindParam(':leasing', $leasing);
				$stmt->bindParam(':biding', $biding);
				$stmt->bindParam(':update_time', $updateTime);
				$stmt->bindParam(':active', $active);
				
				$stmt->bindParam(':stdFeaturs', $stringSTDF);
			
				if($stmt->execute()){

						http_response_code(200);
						echo json_encode(array("status" => "Data successfully submitted.",
												"error" => false
												));
					}
					else{
						http_response_code(400);
						echo json_encode(array("status" => "Unable to insert data.",
												"error" => true
												));
					}
		}
			
	}catch(Exception $e){
		http_response_code(401);
		echo json_encode(array("status" => "Access denied. please re-login",
								"error" => true,
								"s_error" => $e->getMessage()
						));
	}

}else{
	http_response_code(401);
	echo json_encode(array("error" => "Access denied."));
}
?>