<?php


include('Slim/Slim/Slim/Slim.php');
\Slim\Slim::registerAutoloader();


$app = new \Slim\Slim();

//getting user details 
 $app->map('/user/logout/', function() use ($app) {
	try { 
	 include('connection.php'); //Connectig to db
	 include('function.php');
	 
 $userID=$app->request()->post('userID');
 $oauth_token=$app->request()->post('oauth_token'); 

date_default_timezone_set('Asia/Kolkata');
$date = date('y-m-d H:i:s', time());

 if(!empty($userID) && !empty($oauth_token))//Checking if userID/oauth_token empty or not
 {

    $valid_user=validUser($conn,$userID,$oauth_token);
    if($valid_user==1)
	{
	$stmt_update = $conn->prepare('UPDATE tb_user SET login_status= :login_status, logout_time= :logout_time, devicetoken=:devicetoken, oauth_token=:oauth_token  WHERE userID=:userID');// Validating user
	$stmt_update->bindParam(':login_status',$login_status,PDO::PARAM_STR);
	$stmt_update->bindParam(':logout_time',$logout_time,PDO::PARAM_STR);
	$stmt_update->bindParam(':devicetoken',$devicetoken,PDO::PARAM_STR);
	$stmt_update->bindParam(':oauth_token',$oauth_token, PDO::PARAM_STR);
	$stmt_update->bindParam(':userID',$userID, PDO::PARAM_INT);

	$login_status='false';
	$logout_time=$date;
	$devicetoken='';
	$oauth_token='';
	$stmt_update->execute();
	
	    $post['result']="success";
		//$post['message']="logout sucessfully.";

	}
	else
	{
		$post['result']="failed";
		$post['error']="invalid userID / oauth_token";
	}
 }
 else
 {
	$post['result']="failed";
	$post['error']="userID/oauth_token empty";
	 
 }
$app->response()->header('Content-Type', 'application/json');
echo json_encode($post);

$conn = null;


 } catch (PDOException $e) {
    $app->response()->status(400);
    echo($app->response()->header('X-Status-Reason', $e->getMessage()));
  }
   
})->via('POST');
$app->run();



