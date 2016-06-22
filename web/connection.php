<?php
try {	
			$hostname = 'localhost';            
            $dbname   = 'app_charmer';
            $username = 'root';
            $password = 'Polo#321';

    $conn = new PDO("mysql:host=$hostname;dbname=$dbname",$username,$password);

    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

	} catch(PDOException $e) {
    echo 'ERROR: ' . $e->getMessage();
	}
?>