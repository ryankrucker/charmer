<?php


include('Slim/Slim/Slim/Slim.php');
\Slim\Slim::registerAutoloader();


$app = new \Slim\Slim();

//getting user details 
 $app->map('/user/postCharm/', function() use ($app) {
	try { 
	 include('connection.php'); //Connectig to db
	 include('function.php');

 $userID=$app->request()->post('userID');
 $charms=$app->request()->post('charms');
 $uName=$app->request()->post('uName');
 $oauth_token=$app->request()->post('oauth_token'); 
 
date_default_timezone_set('Asia/Kolkata');
$date = date('y-m-d H:i:s', time());

  
$end=substr($charms, -1);


if(empty($charms))
{
$count_charms=0;
}
else if($charms==",")
{
$count_charms=0;

}

else
{
		$array_charms=explode(",",$charms);
		$count_charms=count($array_charms);
		//$post['cc']=$count_charms;

		if($end=="," && $count_charms==4)
		{
			$count_charms=3;

		}

}
		if($count_charms==1)
		{
		$charms1=$array_charms[0];
		$charms2="";
		$charms3="";	
		}
		
		else if($count_charms==2)
		{
		$charms1=$array_charms[0];
		$charms2=$array_charms[1];
		$charms3="";	
		}
		
		else if($count_charms==3)
		{
		$charms1=$array_charms[0];
		$charms2=$array_charms[1];
		$charms3=$array_charms[2];
		}
		
		else
		{
		$charms=11;	
		}


$end_uName=substr($uName, -1);
$array_uName=explode(",",$uName);
$count_uName=count($array_uName);


 if(!empty($userID) && !empty($oauth_token))
 {
	 
	 
	$valid_user=validUser($conn,$userID,$oauth_token);
    if($valid_user==1)
	{
		
		if(empty($uName))
		{
		$post['result']="failed";
		$post['error']="No users to post charm";
		}
	
		else
		{
			if($charms!=11)
			{
				
			
			
		
			
			
			$insert_feed = $conn->prepare('INSERT INTO tb_feed(`userID`,`desOne`,`desTwo`,`desThree`, `feed_date`) VALUES(:userID,:charm1,:charm2,:charm3,:feed_date)');
			

			$insert_feed->bindParam(':userID',$userID,PDO::PARAM_STR);
			$insert_feed->bindParam(':charm1',$charms1,PDO::PARAM_INT);
			$insert_feed->bindParam(':charm2',$charms2,PDO::PARAM_INT);
			$insert_feed->bindParam(':charm3',$charms3,PDO::PARAM_INT);
			$insert_feed->bindParam(':feed_date',$date,PDO::PARAM_STR);
			$insert_feed->execute();	
			$feed_insertId = $conn->lastInsertId();
			
			for($cUname=0;$cUname<$count_uName;$cUname++)
			{		
		$insert_charms = $conn->prepare('INSERT INTO tb_charms(`userID`,`feedID`,`desOne`,`desTwo`,`desThree`,`describe_uName`,`charm_post_date`) VALUES(:userID,:feedID,:charm1,:charm2,:charm3,:describe_uName,:charm_post_date)');
			
			$insert_charms->bindParam(':userID',$userID,PDO::PARAM_STR);
			$insert_charms->bindParam(':feedID',$feed_insertId,PDO::PARAM_STR);
			$insert_charms->bindParam(':charm1',$charms1,PDO::PARAM_INT);
			$insert_charms->bindParam(':charm2',$charms2,PDO::PARAM_INT);
			$insert_charms->bindParam(':charm3',$charms3,PDO::PARAM_INT);
			$insert_charms->bindParam(':describe_uName',$array_uName[$cUname],PDO::PARAM_STR);
			$insert_charms->bindParam(':charm_post_date',$date,PDO::PARAM_STR);
			$insert_charms->execute();	
			
			$insertId = $conn->lastInsertId();
			}
			
			$post['result']="success";
			
				
			}
			else
			{
					$post['result']="failed";
					$post['error']="no/more than 3 charms";
			}
		
			
		}
		
		
		
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
    echo "PDO Exception Caught : ".$app->response()->header('X-Status-Reason', $e->getMessage());
  }
  catch (Exception $ex) {
    $app->response()->status(400);
    echo "Exception Caught : ".$app->response()->header('X-Status-Reason', $ex->getMessage());
  }
   
})->via('POST');
$app->run();