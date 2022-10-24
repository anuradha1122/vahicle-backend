<?php
include_once '../database.php';
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

$databaseService = new DatabaseService();
$conn = $databaseService->getConnection();

$data = json_decode(file_get_contents("php://input"));
$jwt = getBearerToken();
$key = getSecretKey();

if($jwt){
	try{
		$decoded = JWT::decode($jwt, new Key($key, 'HS256'));
		//echo $jwt;
                
        $response = array();
        $upload_dir = 'uploads/';
        $server_url = 'category';

        if($_FILES['file'])
        {
            $avatar_name = $_FILES["file"]["name"];
            $avatar_tmp_name = $_FILES["file"]["tmp_name"];
            $error = $_FILES["file"]["error"];

            if($error > 0){
                $response = array(
                    "status" => "error",
                    "error" => true,
                    "message" => "Error uploading the file!"
                );
            }else 
            {
                $random_name = uniqid()."-".$avatar_name;
                $upload_name = $upload_dir.strtolower($random_name);
                $upload_name = preg_replace('/\s+/', '-', $upload_name);

                if(move_uploaded_file($avatar_tmp_name , $upload_name)) {
                    $response = array(
                        "status" => "File uploaded successfully",
                        "error" => false,
                        "url" => $server_url."/".$upload_name
                    );
                }else
                {
                    $response = array(
                        "status" => "Error uploading the file!",
                        "error" => true
                    );
                }
            }    

        }else{
            $response = array(
                "status" => "No file was sent!",
                "error" => true
            );
        }

echo json_encode($response);
		
	}catch(Exception $e){
		//http_response_code(401);
		echo json_encode(array("status" => "Access denied. please re-login",
								"error" => true,
								"s_error" => $e->getMessage()
						));
	}

}else{
	//http_response_code(401);
	echo json_encode(array("status" => "Access denied."));
}
?>