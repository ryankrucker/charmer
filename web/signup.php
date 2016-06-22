<?php


include('Slim/Slim/Slim/Slim.php');
\Slim\Slim::registerAutoloader();


$app = new \Slim\Slim();

//getting user details 
 $app->map('/user/register/', function() use ($app) {
	try { 
	 
	 include('connection.php'); //Connectig to db
	 include('function.php');
	 
 $fName=$app->request()->post('firstName');
 $lName=$app->request()->post('lastName');
 $gender=$app->request()->post('gender');
 $pass=$app->request()->post('pass');
 $age=$app->request()->post('age');
 $email=$app->request()->post('email');
 $token=$app->request()->post('devicetoken');
 
 date_default_timezone_set('Asia/Kolkata');
$date = date('y-m-d H:i:s', time());
 //$date=date('y-m-d H:i:s');

 $oauth_token  = sha1(str_shuffle(mt_rand(10000,99999).str_shuffle($date.str_shuffle($email))));


if(!empty($email))//Checking if email empty or not
{
	
	
    $regUser=registerdUser($conn,$email);
	
	if($regUser==1)
	{

	$post['result']="failed";
	$post['error']="user exists";
	}

	else
	{
	//Inserting details into db if not an registered user
	
	$insert_stmt = $conn->prepare('INSERT INTO tb_user(`fname`,`lname`,`age`,`gender`,`email`,`password`) VALUES(:fname,:lname,:age,:gender,:email,:password)');
	
	$insert_stmt->bindParam(':fname',$fName,PDO::PARAM_STR);
	$insert_stmt->bindParam(':lname',$lName,PDO::PARAM_STR);
	$insert_stmt->bindParam(':age',$age,PDO::PARAM_INT);
	$insert_stmt->bindParam(':gender',$gender,PDO::PARAM_STR);
	$insert_stmt->bindParam(':email',$email,PDO::PARAM_STR);
	$insert_stmt->bindParam(':password',$pass,PDO::PARAM_STR);
	$insert_stmt->execute();
	
		 if(!empty($_FILES["picture"]["name"]))//Checking if userID/oauth_token empty or not
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
				
	$stmt_pic_update = $conn->prepare('UPDATE tb_user SET profilePic= :profilePic WHERE email=:email');// Updating user profile picture
	$stmt_pic_update->bindParam(':profilePic',$picLink, PDO::PARAM_LOB);
	$stmt_pic_update->bindParam(':email',$email, PDO::PARAM_STR);

	$base_url='http://'.$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']); // Getting the URL from server
	$rest = substr($base_url, 0, -16);  // removing "/user/profPic/" from the url
	$picLink=$rest."/profilePic/".$picture; // Adding folder name and picture name to url.
	
	$stmt_pic_update->execute();
	

	
	}
	updateUser($conn,$email,$oauth_token,$token,$date);
	$user_row=userDetails($conn,$email);
	
	$post['result']="success";
	$post['user_data']=$user_row;
	
	}

	
	
}
else
{
	
	$post['result']="failed";
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
