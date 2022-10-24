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
        $server_url = 'file_upload';

        if($_FILES['file'])
			{
    $count = count($_FILES['file']['name']);
    for ($i = 0; $i < $count; $i++) {

        $file_name = $_FILES["file"]["name"][$i];
        $file_tmp_name = $_FILES["file"]["tmp_name"][$i];
        $error = $_FILES["file"]["error"][$i];
    
        if($error > 0){
            array_push($response,array(
                "status" => "error",
                "error" => true,
                "message" => "Error uploading the file!"
            ));
        }else 
        {
            $random_name = rand(1000,1000000)."-".$file_name;
            $upload_name = $upload_dir.strtolower($random_name);
            $upload_name = preg_replace('/\s+/', '-', $upload_name);

            if(move_uploaded_file($file_tmp_name , $upload_name)) {

                // Convert uploaded file into Base64
                $path = './'.$upload_name;
                $type = pathinfo($path, PATHINFO_EXTENSION);
                $data = file_get_contents($path);
                $base64URL = 'data:image/' . $type . ';base64,' . base64_encode($data);

                  array_push($response,array(
                    "status" => "success",
                    "error" => false,
                    "message" => "File uploaded successfully",
                    "url" => $server_url."/".$upload_name,
                    "base64" => $base64URL,
                    "total" => $count
                  ));
            }else
            {
                array_push($response,array(
                    "status" => "danger",
                    "error" => true,
                    "url" =>  $file_name,
                    "message" => "Error uploading the file!"
                ));
            }
        }
    }

}else{
    $response = array(
        "status" => "error",
        "error" => true,
        "message" => print_r($_FILES['file'])
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