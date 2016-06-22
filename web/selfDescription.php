<?php


include('Slim/Slim/Slim/Slim.php');
\Slim\Slim::registerAutoloader();


$app = new \Slim\Slim();

//getting user details 
 $app->map('/user/selfdscptn/', function() use ($app) {
	 
	try { 
	 include('connection.php'); //Connectig to db
	 include('function.php');
	 
 $userID=$app->request()->post('userID');
 $oauth_token=$app->request()->post('oauth_token'); 
 $desOne=$app->request()->post('desOne');
 $desTwo=$app->request()->post('desTwo');
 $desThree=$app->request()->post('desThree');

 date_default_timezone_set('Asia/Kolkata');
$date = date('y-m-d H:i:s', time());

 if(!empty($userID) && !empty($oauth_token))//Checking if userID/oauth_token empty or not
 {

    $valid_user=validUser($conn,$userID,$oauth_token);
    if($valid_user==1)
	{
	$user_row=userDetails($conn,$userID);
	    
		
				
				
		$insert_feed = $conn->prepare('INSERT INTO tb_feed(`userID`,`desOne`,`desTwo`,`desThree`,`feed_date`) VALUES(:userID,:charm1,:charm2,:charm3,:feed_date)');
			
			
			$insert_feed->bindParam(':userID',$userID,PDO::PARAM_STR);
			$insert_feed->bindParam(':charm1',$desOne,PDO::PARAM_INT);
			$insert_feed->bindParam(':charm2',$desTwo,PDO::PARAM_INT);
			$insert_feed->bindParam(':charm3',$desThree,PDO::PARAM_INT);
			$insert_feed->bindParam(':feed_date',$date,PDO::PARAM_STR);
			$insert_feed->execute();	
		
			$feedinsertId = $conn->lastInsertId();
		//-------------------------------------------
		  
			$insert_charms = $conn->prepare('INSERT INTO tb_charms(`feedID`,`userID`,`desOne`,`desTwo`,`desThree`,`charm_post_date`) VALUES(:feedID,:userID,:charm1,:charm2,:charm3,:charm_post_date)');
			
			$insert_charms->bindParam(':feedID',$feedinsertId,PDO::PARAM_STR);
			$insert_charms->bindParam(':userID',$userID,PDO::PARAM_STR);
			$insert_charms->bindParam(':charm1',$desOne,PDO::PARAM_INT);
			$insert_charms->bindParam(':charm2',$desTwo,PDO::PARAM_INT);
			$insert_charms->bindParam(':charm3',$desThree,PDO::PARAM_INT);
			$insert_charms->bindParam(':charm_post_date',$date,PDO::PARAM_STR);
			$insert_charms->execute();	
			
		$post['result']="success";
		//$post['user_data']=$select;
		
		

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


