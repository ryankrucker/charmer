<?php


include('Slim/Slim/Slim/Slim.php');
\Slim\Slim::registerAutoloader();


$app = new \Slim\Slim();

//getting user details 
 $app->map('/user/allfeeds/', function() use ($app) {
	try { 
	 include('connection.php'); //Connectig to db
 	 include('function.php');
$userID=$app->request()->post('userID');
$oauth_token=$app->request()->post('oauth_token'); 
 $nextIndex=$app->request()->post('nextIndex');
$count=$nextIndex;
			$index=$nextIndex*10;
 if(!is_numeric($nextIndex))
 {
	 $post['result']="failed";    
	 $post['error']="invalid nextIndex";    
 }
 else
 {
	 $charm_view=$conn->prepare('SELECT * FROM tb_charms ORDER BY charm_post_date DESC ');
	$charm_view->execute();
			 
	
				
		while($charm_view_row = $charm_view->fetch(PDO::FETCH_ASSOC))
		{
					
				$userID=$charm_view_row['userID'];
				$charmID=$charm_view_row['charmID'];
				$feedID=$charm_view_row['feedID'];
				
				$from_user=$conn->prepare('SELECT * FROM tb_user WHERE userID=:userid');
				$from_user->bindParam(':userid',$userID,PDO::PARAM_STR);
				$from_user->execute();
				$from_user_row = $from_user->fetch(PDO::FETCH_ASSOC);
				
				
				$fromUserID=$from_user_row['userID'];
				$fromUserName=$from_user_row['fname'];
				$fromUserImageUrl=$from_user_row['profilePic'];
				
				$uNameID=$charm_view_row['describe_uName'];
								
				$to_user=$conn->prepare('SELECT * FROM tb_user WHERE userID=:userid');
				$to_user->bindParam(':userid',$uNameID,PDO::PARAM_STR);
				$to_user->execute();
				$to_user_row = $to_user->fetch(PDO::FETCH_ASSOC);
				
				if($to_user_row['userID']=="")
				{
					$toUserID="";
				}
				else
				{
					$toUserID=$to_user_row['userID'];
				}
				
				if($to_user_row['fname']=="")
				{
					$toUserName="";
				}
				else
				{
					$toUserName=$to_user_row['fname'];
				}
				
				if($to_user_row['profilePic']=="")
				{
					$toUserImageUrl="";
				}
				else
				{
					$toUserImageUrl=$to_user_row['profilePic'];
				}
				
				$desOne=$charm_view_row['desOne'];
				$desTwo=$charm_view_row['desTwo'];
				$desThree=$charm_view_row['desThree'];	
				
				$likedStatus=$charm_view_row['liked_status'];
				
				$like_count=$conn->prepare('SELECT * FROM tb_feed WHERE feedID=:feedid');
				$like_count->bindParam(':feedid',$feedID,PDO::PARAM_STR);
				$like_count->execute();
				$like_count_row = $like_count->fetch(PDO::FETCH_ASSOC);
				
				
				$likeCount=$like_count_row['like_count'];
				$commentCount=$like_count_row['comment_count'];
				$charm_post_date=$like_count_row['feed_date'];
				
			
				
				
				$user_row[]=array("feedID"=>$feedID,"charmID"=>$charmID,"fromUserID"=>$fromUserID,"fromUserName"=>$fromUserName,"fromUserImageUrl"=>$fromUserImageUrl,"toUserID"=>$toUserID,"toUserName"=>$toUserName,"toUserImageUrl"=>$toUserImageUrl,"desOne"=>$desOne,"desTwo"=>$desTwo,"desThree"=>$desThree,"date"=>$charm_post_date,"likedStatus"=>$likedStatus,"likeCount"=>$likeCount,"commentCount"=>$commentCount);
			
		}
			
			$output1 = array_slice($user_row, $index, 10);
		
 $post['result']="success";
 $post['nextIndex']=$nextIndex+1;
 $post['feedList']=$output1;  
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