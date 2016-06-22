<?php


include('Slim/Slim/Slim/Slim.php');
\Slim\Slim::registerAutoloader();


$app = new \Slim\Slim();

//getting user details 
 $app->map('/user/login/', function() use ($app) {
	
	try{ 
	 include('connection.php'); //Connectig to db
	 include('function.php');
	 
 $email=$app->request()->post('email');
 $password=$app->request()->post('password');
 $token=$app->request()->post('devicetoken');


date_default_timezone_set('Asia/Kolkata');
$date = date('y-m-d H:i:s', time());
//$date=date('y-m-d H:i:s');
//$post['tst']=$dat;
$post['tt']=$date;
$oauth_token  = sha1(str_shuffle(mt_rand(10000,99999).str_shuffle($date.str_shuffle($email))));

if(!empty($email))//Checking if email is empty or not
{
   
    $stmt = $conn->prepare('select * from tb_user where email= :email and password= :password');// Validating user
	$stmt->bindParam(':email',$email,PDO::PARAM_STR);
	$stmt->bindParam(':password',$password, PDO::PARAM_STR);

	$stmt->execute();
 
	$row = $stmt->fetch(PDO::FETCH_ASSOC);
    if($row)
	{
	 	

		updateUser($conn,$email,$oauth_token,$token,$date);
		$user_row=userDetails($conn,$email);
		
			
	$post['result']="success";
	$post['user_data']=$user_row;
		
	}
	else
	{
	$post['result']="failed";
	$post['error']="invalid email or password";
	}



}
else
{
	
	$post['result']="failed"; //If email empty, set result as failed.
	$post['error']="no email found";
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


