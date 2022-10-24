<?php
include_once '../config_database.php';

header("Access-Control-Allow-Origin: * ");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

require "../vendor/autoload.php";
use \Firebase\JWT\JWT;

$userID = '';
$password = '';
$conn = null;

$databaseService = new DatabaseService();
$conn = $databaseService->getConnection();

$data = json_decode(file_get_contents("php://input"));

if($data){
	$userID = $data->userID;
	$password = $data->password;

	$table_name = 'user';

	$query = "SELECT 
					ID,
					NAME_WITH_INITIALS,
					BIRTHDAY,
					(CASE WHEN GENDER=0 THEN 'Male' WHEN GENDER=1 THEN 'Female' ELSE 'Other' END) AS GENDER,
					CONTACT_NO,
					EMAIL,
					PASSWORD 
				FROM " . $table_name . " WHERE EMAIL = ? LIMIT 0,1";

	$stmt = $conn->prepare( $query );
	$stmt->bindParam(1, $userID);
	$stmt->execute();
	$num = $stmt->rowCount();
	
	if($num > 0){
		$row = $stmt->fetch(PDO::FETCH_ASSOC);
		$DB_id = $row['ID'];
		$DB_name = $row['NAME_WITH_INITIALS'];
		$DB_birthday = $row['BIRTHDAY'];
		$DB_gender = $row['GENDER'];
		$DB_contactNo = $row['CONTACT_NO'];
		$DB_email = $row['EMAIL'];
		$DB_password = $row['PASSWORD'];
		
		if(password_verify($password, $DB_password)){
			$secret_key = "*$%43MVKJTKMN$#";
			$issuer_claim = "http://localhost.com/"; // this can be the servername
			$audience_claim = "THE_AUDIENCE";
			$issuedat_claim = time(); // issued at
			$notbefore_claim = $issuedat_claim + 10; //not before in seconds
			$expire_claim = $issuedat_claim + 60*60*24; // expire time in seconds
			$payload = array(
				"iss" => $issuer_claim,
				"aud" => $audience_claim,
				"iat" => $issuedat_claim,
				"nbf" => $notbefore_claim,
				"exp" => $expire_claim,
				"data" => array(
					"id" => $DB_id,
					"name" => $DB_name,
					"userID" => $DB_email,
			));
			
			http_response_code(200);
			$jwt = JWT::encode($payload, $secret_key, 'HS256');
			echo json_encode(
				array(
					"status" => "Successful login.",
					"error" => false,
					"accessToken" => $jwt,
					"userData" => array (
						"id" => $DB_id,
						"name" => $DB_name,
						"birthday" => $DB_birthday,
						"gender" => $DB_gender,
						"contactNo" => $DB_contactNo,
						"email" => $DB_email
					),
				));
		}else{
			//http_response_code(401);
			echo json_encode(array("status" => "Incorrect password.", 
									"error" => true,
									"user" => $userID));
		}
	}else{
		//http_response_code(401);
		echo json_encode(array("status" => "User not registed", 
								"error" => true,
								"user" => $userID));
	}
	
}else{
	//http_response_code(401);
	echo json_encode(array("error" => "Access denied."));
}
?>