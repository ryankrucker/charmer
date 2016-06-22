<?php


include('Slim/Slim/Slim/Slim.php');
\Slim\Slim::registerAutoloader();


$app = new \Slim\Slim();

 $app->map('/user/profPic/', function() use ($app) {
	try { 
	 include('connection.php'); //Connectig to db
	 include('function.php');

 //getting user details 
 $userID=$app->request()->post('userID');
 $oauth_token=$app->request()->post('oauth_token'); 

	
	 if(!empty($userID) && !empty($oauth_token) && !empty($_FILES["picture"]["name"]))//Checking if userID/oauth_token empty or not
 	{

   	$valid_user=validUser($conn,$userID,$oauth_token);// Validating user
	
    if($valid_user==1)
	{
		$picture = trim(str_replace("%","_", $_FILES['picture']['name'])); // removing white spaces from picture name.
		$tmp=$_FILES["picture"]["tmp_name"];
		
		if(file_exists("./profilePic/".$picture))
		{
			$randomNum=rand();
			$image=$randomNum.$picture; //If picture exists, change the name and save
			move_uploaded_file($tmp,"./profilePic/".$picture);

		}
		else
		{
			move_uploaded_file($tmp,"./profilePic/".$picture);
		}
		
	$stmt_update = $conn->prepare('UPDATE tb_user SET profilePic= :profilePic WHERE userID=:userID');// Updating user profile picture
	$stmt_update->bindParam(':profilePic',$picLink, PDO::PARAM_LOB);
	$stmt_update->bindParam(':userID',$userID, PDO::PARAM_STR);

	$base_url='http://'.$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']); // Getting the URL from server
	$rest = substr($base_url, 0, -16);  // removing "/user/profPic/" from the url
	$picLink=$rest."profilePic/".$picture; // Adding folder name and picture name to url.
	
	$stmt_update->execute();
	
	
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
	$post['error']="userID/oauth_token/picture empty";
	 
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


