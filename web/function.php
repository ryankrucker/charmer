 <?php
 

	function userDetails($conn,$id)
    {
	$user_detail=$conn->prepare('SELECT `userID`,`fbID`,`twitter_ID`,`instagram_ID`,`fname`,`lname`,`age`,`gender`,`email`,`profilePic`,`oauth_token`,`login_status` FROM tb_user WHERE userID=:id or fbID=:id or twitter_ID=:id or instagram_ID=:id or email=:id');
		$user_detail->bindParam(':id',$id,PDO::PARAM_STR);
		$user_detail->execute();
		$user_row = $user_detail->fetch(PDO::FETCH_ASSOC);
		
		$count=0;
		$charm_details=$conn->prepare('SELECT * FROM tb_feed WHERE userID=:id');
		$charm_details->bindParam(':id',$id,PDO::PARAM_STR);
		$charm_details->execute();
	
		while($charm_row = $charm_details->fetch(PDO::FETCH_ASSOC))
		{
		$count=$count+1;
	
		
		$user_row['charmsCount']=$count;
		$desOne=$charm_row['desOne'];
		$desTwo=$charm_row['desTwo'];
		$desThree=$charm_row['desThree'];
		
		
		$user_row['desOne']=$desOne;
		$user_row['desTwo']=$desTwo;
		$user_row['desThree']=$desThree;
	}
	if($count<=0)
	{
	$user_row['charmsCount']=0;
	$user_row['desOne']="";
		$user_row['desTwo']="";
		$user_row['desThree']="";
	}

		$count_followingID=0;
		$count_followersID=0;
		$follow_details=$conn->prepare('SELECT `followersID`,`followingID` FROM tb_follow where userID=:id');
		$follow_details->bindParam(':id',$id,PDO::PARAM_STR);
		$follow_details->execute();
	
		while($follow_row = $follow_details->fetch(PDO::FETCH_ASSOC))
		{
			if($follow_row['followersID']!="")
			{
			$count_followersID=$count_followersID+1;	
			}
			
			if($follow_row['followingID']!="")
			{
			$count_followingID=$count_followingID+1;	
			}
		}
					
		$user_row['followingCount']=$count_followingID;
		$user_row['followersCount']=$count_followersID;
	
		$user_row['nickName']="@".strtoupper($user_row['fname']).strtoupper ($user_row['lname']);
		
			
		
		if($user_row)
		{
		return $user_row;	
		}
		else
		{
		return "";	
		}
		
	}


//-----------------------------------------------------------------------------------

	function feeds($conn,$userID)
	{
		$charm_view=$conn->prepare('SELECT * FROM tb_charms WHERE userID=:id ORDER BY charm_post_date DESC ');
		$charm_view->bindParam(':id',$userID,PDO::PARAM_STR);
		$charm_view->execute();
		
		
		while($charm_view_row = $charm_view->fetch(PDO::FETCH_ASSOC))
		{
			$uNameID=$charm_view_row['describe_uName'];
			$charm_post_date=$charm_view_row['charm_post_date'];
			//$array_charmsuNameID=explode(",",$uNameID);
 				
				$toUserID=$uNameID;
				//$array_charmsuNameID[$uID];
				$desOne=$charm_view_row['desOne'];
				$desTwo=$charm_view_row['desTwo'];
				$desThree=$charm_view_row['desThree'];	
				
				$likeCount=$charm_view_row['like_count'];	
				$commentCount=$charm_view_row['comment_count'];	
				
				$user_row[]=array("toUserID"=>$toUserID,"desOne"=>$desOne,"desTwo"=>$desTwo,"desThree"=>$desThree,"date"=>$charm_post_date,"likeCount"=>$likeCount,"commentCount"=>$commentCount);
				
				
		}
		return $user_row;
	}
	
//------------------------------------------------------------------------------------	

	function descrb_uName($conn,$describe_uName)
	{
		$uNameID=$describe_uName;
		$array_charmsuNameID=explode(",",$uNameID);
		
	}
//------------------------------------------------------------------------------------
	function updateUser($conn,$email,$oauth_token,$token,$date)
	{
		
		$update_user = $conn->prepare('UPDATE tb_user SET login_status = :login_status,login_time=:login_time,devicetoken=:devicetoken, oauth_token=:oauth_token WHERE email=:email or userID=:email');
		
		$update_user->bindParam(':login_status',$login_status,PDO::PARAM_STR);
		$update_user->bindParam(':login_time',$date);
		$update_user->bindParam(':devicetoken',$token,PDO::PARAM_STR);
		$update_user->bindParam(':oauth_token',$oauth_token,PDO::PARAM_STR);
		$update_user->bindParam(':email',$email,PDO::PARAM_STR);				
		$login_status="true";
		
		$update_user->execute();			
	}
	
//--------------------------------------------------------------------------------------
	
	function registerdUser($conn,$email)
	{
	$email_exist = $conn->prepare('select `email` from tb_user where email= :email');//Checking if email exist
	$email_exist->bindParam(':email',$email,PDO::PARAM_STR);
	$email_exist->execute();
 
  	$row_email_exist = $email_exist->fetch(PDO::FETCH_ASSOC);	
	
	if($row_email_exist)
		{
		return 1;	
		}
		else
		{
		return 0;	
		}	
	}

//--------------------------------------------------------------------------------------------------
	
	function validUser($conn,$userID,$oauth_token)
	{
	
	if(!is_numeric($userID))	
	{
		return 0;
	}
	else
	{
	$valid_user = $conn->prepare('SELECT * FROM tb_user WHERE userID= :userID and oauth_token= :oauth_token');// Validating user
	$valid_user->bindParam(':userID',$userID,PDO::PARAM_INT);
	$valid_user->bindParam(':oauth_token',$oauth_token, PDO::PARAM_STR);
	$valid_user->execute();

	$row_valid_user= $valid_user->fetch(PDO::FETCH_ASSOC);
	
	if($row_valid_user)
		{
		return 1;	
		}
		else
		{
		return 0;	
		}
	}
	}
	?>