<?php


include('Slim/Slim/Slim/Slim.php');
\Slim\Slim::registerAutoloader();


$app = new \Slim\Slim();

//getting user details 
 $app->map('/user/view/', function() use ($app) {
	try { 
	 include('connection.php'); //Connectig to db
	 include('function.php');
	 
 $userID=$app->request()->post('userID');
 $oauth_token=$app->request()->post('oauth_token'); 

 if(!empty($userID) && !empty($oauth_token))//Checking if userID/oauth_token empty or not
 {

    $valid_user=validUser($conn,$userID,$oauth_token);
    if($valid_user==1)
	{
	$user_row=userDetails($conn,$userID);
	    $post['result']="success";
		$post['user_data']=$user_row;

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
	$post['error']="no userID/oauth_token found";
	 
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
?>


