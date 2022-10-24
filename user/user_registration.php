<?php
include_once '../config_database.php';

header("Access-Control-Allow-Origin: * ");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$nameWithInitias = '';
$birthDay = '';
$gender = '';
$contactNo = '';
$email = '';
$password = '';
$regDate = '';
$conn = null;

$databaseService = new DatabaseService();
$conn = $databaseService->getConnection();

$data = json_decode(file_get_contents("php://input"));
	if($data){
		try{
			$nameWithInitias = $data->nameWithInitias;
			$birthDay = $data->birthDay;
			$gender = $data->gender;
			$contactNo = $data->contactNo;
			$email = $data->email;
			$password = $data->password;
			$regDate = date('Y-m-d H:i:s');

			$table_name = 'user';

			$query = "INSERT INTO " . $table_name . "
							SET NAME_WITH_INITIALS = :name_with_initials,
								BIRTHDAY = :birthday,
								GENDER = :gender,
								CONTACT_NO = :contact_no,
								EMAIL = :email,
								PASSWORD = :password,
								REG_DATE = :reg_date";

			$stmt = $conn->prepare($query);

			$stmt->bindParam(':name_with_initials', $nameWithInitias);
			$stmt->bindParam(':birthday', $birthDay);
			$stmt->bindParam(':gender', $gender);
			$stmt->bindParam(':contact_no', $contactNo);
			$stmt->bindParam(':email', $email);
			$stmt->bindParam(':reg_date', $regDate);

			$password_hash = password_hash($password, PASSWORD_BCRYPT);

			$stmt->bindParam(':password', $password_hash);


			if($stmt->execute()){

				http_response_code(200);
				echo json_encode(array("status" => "User was successfully registered.",
										"error" => false
										));
			}
			else{
				//http_response_code(400);
				echo json_encode(array("status" => "Unable to register the user.",
										"error" => true
										));
			}
			
		}catch(Exception $e){
					//http_response_code(401);
					echo json_encode(array(
						"status" => "This email or contact no. is already registed, Try another.",
						"error" => true,
						"s_error" => $e->getMessage()
					));
		}
		
	}else{
		http_response_code(401);
		echo json_encode(array("error" => "Access denied."));
	}
?>