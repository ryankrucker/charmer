<?php


include('Slim/Slim/Slim/Slim.php');
\Slim\Slim::registerAutoloader();


$app = new \Slim\Slim();

//getting user details 
 $app->map('/user/fbLogin/', function() use ($app) {
	try { 
	 include('connection.php'); //Connectig to db
	 include('function.php');

 $loginID=$app->request()->post('loginID');
 $type=$app->request()->post('type'); 
	 
 $fName=$app->request()->post('userName');
 $email=$app->request()->post('email');
 $token=$app->request()->post('devicetoken');

 $date=date('y-m-d H:i:s');
 $oauth_token  = sha1(str_shuffle(mt_rand(10000,99999).str_shuffle($date.str_shuffle($email))));
 
 if(!empty($loginID) && !empty($type))//Checking if loginID/type empty or not
 {
			
 
 	if($type=="fb")
			{
			$check_user = $conn->prepare('SELECT `userID`,`email`,`fbID` FROM tb_user where fbID=:fbID');
			$check_user->bindParam(':fbID',$loginID,PDO::PARAM_STR);
			$check_user->execute();
		
			$row_fb = $check_user->fetch(PDO::FETCH_ASSOC);
				if($row_fb)
				{			
				$fbuser=$row_fb['userID'];
				updateUser($conn,$fbuser,$oauth_token,$token,$date);
				$user_row=userDetails($conn,$fbuser);
				
				$post['result']="success";
				
				$post['user_data']=$user_row;
		
				}
				else
				{

					$fbuser_details = $conn->prepare('INSERT INTO tb_user(`fbID`,`fname`,`email`) VALUES(:fbID,:fname,:email)');
					$fbuser_details->bindParam(':fbID',$loginID,PDO::PARAM_STR);
					$fbuser_details->bindParam(':fname',$fName,PDO::PARAM_STR);
					$fbuser_details->bindParam(':email',$email,PDO::PARAM_STR);
					$fbuser_details->execute();
	
					$insertId = $conn->lastInsertId();
										
					updateUser($conn,$insertId,$oauth_token,$token,$date);		
					$user_row=userDetails($conn,$insertId);
					
					$post['result']="success";
					$post['user_data']=$user_row;
				
				}
			}
			
			
			
			else if($type=="twitter")
			{
				
			$check_user = $conn->prepare('SELECT `userID`,`email`,`twitter_ID` FROM tb_user where twitter_ID=:twitter_ID');
			$check_user->bindParam(':twitter_ID',$loginID,PDO::PARAM_STR);
			$check_user->execute();
		
			$row_twitter = $check_user->fetch(PDO::FETCH_ASSOC);
			if($row_twitter)
			{	
				$twitteruser=$row_twitter['userID'];		
				updateUser($conn,$twitteruser,$oauth_token,$token,$date);
				$user_row=userDetails($conn,$twitteruser);
				
				$post['result']="success";
				$post['user_data']=$user_row;
		
			}
			else
			{ 
				
					$user_twitter = $conn->prepare('INSERT INTO tb_user(`twitter_ID`,`fname`,`email`) VALUES(:twitter_ID,:fname,:email)');
					$user_twitter->bindParam(':twitter_ID',$loginID,PDO::PARAM_STR);
					$user_twitter->bindParam(':fname',$fName,PDO::PARAM_STR);
					$user_twitter->bindParam(':email',$email,PDO::PARAM_STR);
					$user_twitter->execute();
				
										
					$insertId = $conn->lastInsertId();
					updateUser($conn,$insertId,$oauth_token,$token,$date);		
					$user_row=userDetails($conn,$insertId);
					
					$post['result']="success";
					$post['user_data']=$user_row;
				
			}		
			}
			
			
			
			else if($type=="instagram")
			{
					
				
			$check_user = $conn->prepare('SELECT `userID`,`email`,`instagram_ID` FROM tb_user where instagram_ID=:instagram_ID');
			$check_user->bindParam(':instagram_ID',$loginID,PDO::PARAM_STR);
			$check_user->execute();
		
			$row_instagram = $check_user->fetch(PDO::FETCH_ASSOC);
			if($row_instagram)
			{	
			    $insta_user=$row_instagram['userID'];	
				updateUser($conn,$insta_user,$oauth_token,$token,$date);
				$user_row=userDetails($conn,$insta_user);
				
				$post['result']="success";
				$post['user_data']=$user_row;
		
			}
			else
			{ 
				$user_instagram= $conn->prepare('INSERT INTO tb_user(`instagram_ID`,`fname`,`email`) VALUES(:instagram_ID,:fname,:email)');
					$user_instagram->bindParam(':instagram_ID',$loginID,PDO::PARAM_STR);
					$user_instagram->bindParam(':fname',$fName,PDO::PARAM_STR);
					$user_instagram->bindParam(':email',$email,PDO::PARAM_STR);
					$user_instagram->execute();
					
	
										
					$insertId = $conn->lastInsertId();
					updateUser($conn,$insertId,$oauth_token,$token,$date);		
					$user_row=userDetails($conn,$insertId);
					
					$post['result']="success";
					$post['user_data']=$user_row;
					
							
			}		
			}
			 
 }
 else
 {
	$post['result']="failed";
	$post['error']="loginID/type empty";
	 
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