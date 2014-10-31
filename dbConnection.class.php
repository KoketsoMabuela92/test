<?php
require_once('ConnetAutoloader.php');
/**
 *@author koketso mabuela
 * @descripton class that all the database functionality 
 */

class dbConnection {
	
	
	/**
	 * @description function opens connection to the MySQL server
	 *@return mysqlii connection object
	 */
	static function openConnection() {
		
		$con = new mysqli ("appdb1", "wayne", "equ4pulie0ooGie", "koketso_test");
		
		if (mysqli_connect_errno()) {
			
			thisLogger::debug("Could not connect to the database.");
			return FALSE;
		} else {
			return $con;
		}
	}
	
	
	/**
	 *@description function that authenticates user credentials
	 * @param username,, password
	 *@return boolean TRUE or FALSE
	 */
	static function authenticateuser ($username,$password) {
		
		$con = self::openConnection();
		$uname = mysqli_real_escape_string($con,$username);
		//$password = mysqli_real_escape_string($con,$password);
		
		$checkUser = sprintf("SELECT password FROM users WHERE username = '%s'",$uname);
		$res = $con->query($checkUser);
		
		if ($con->affected_rows > 0) {
			
			//password has been found
			$currentRow = $res->fetch_assoc();
			$dbPassword = stripslashes($currentRow["password"]);
			
			if (strcasecmp($password, $dbPassword) === 0) {
				
				return TRUE;
				
			}
		}
		
		return FALSE;
	}
	
	/**
	 *@description function that checks if a user exists
	 * @param username
	 *@return boolean TRUE or FALSE
	 */
	static function checkUserExistence ($username) {
		
		$con = self::openConnection();
		$uname = mysqli_real_escape_string($con,$username);
		//$password = mysqli_real_escape_string($con,$password);
		
		$checkUser = sprintf("SELECT * FROM users WHERE username = '%s'",$uname);
		$con->query($checkUser);
		
		if ($con->affected_rows > 0) {
			
			//username exists
			return TRUE;
		}
		
		//unique username
		return FALSE;
	}
	
	
	/**
	 *@description function that retrieves the user's id
	 * @param username
	 *@return int user id
	 */
	static function getUserID ($username) {
		
		$con = self::openConnection();
		$uname = mysqli_real_escape_string($con,$username);
		
		$checkUser = sprintf("SELECT user_id FROM users WHERE username = '%s'",$uname);
		$res = $con->query($checkUser);
		
		if ($con->affected_rows > 0) {
			
			//username exists
			$currentRow = $res->fetch_assoc();
			$userID = stripslashes($currentRow["user_id"]);
			
			return $userID;
		}
		
		//unique username
		return FALSE;
	}
	
	
	
	/**
	 *@description function that checks if the user has confirmed their email address
	 * @param userid
	 *@return boolean TRUE or FALSE
	 */
	static function checkUserConfirmation ($userID) {
		
		$con = self::openConnection();
		$user_id = mysqli_real_escape_string($con,$userID);
		
		$checkConfirmaion = sprintf("SELECT confirmed FROM users WHERE user_id = '%s'",$user_id);
		$res = $con->query($checkConfirmaion);
		
		if ($con->affected_rows > 0) {
			
			//username exists
			$currentRow = $res->fetch_assoc();
			$isConfirmed = stripslashes($currentRow["confirmed"]);
			
			if (strcasecmp($isConfirmed, "NO") === 0) {
				
				//user has not yet confirmed their email address.
				
				return FALSE;
				
			}  else {
				
				//user has already confrimed their email address
				return TRUE;
			}
		
		}
	
	}
	
	/**
	 *@description function that checks if the user has confirmed their email address
	 * @param username
	 *@return boolean TRUE or FALSE
	 */
	static function checkUserConfirmationWithUsername ($uname) {
		
		$con = self::openConnection();
		$username = mysqli_real_escape_string($con,$uname);
		
		$checkConfirmaion = sprintf("SELECT confirmed FROM users WHERE username = '%s'",$username);
		$res = $con->query($checkConfirmaion);
		
		if ($con->affected_rows > 0) {
			
			//username exists
			$currentRow = $res->fetch_assoc();
			$isConfirmed = stripslashes($currentRow["confirmed"]);
			
			if (strcasecmp($isConfirmed, "NO") === 0) {
				
				//user has not yet confirmed their email address.
				
				return FALSE;
				
			}  else {
				
				//user has already confrimed their email address
				return TRUE;
			}
		
		}
	
	}
	
	
	
	static function updateConfirmation ( $userID) {
		
		$con = self::openConnection();
		$user_id= mysqli_real_escape_string($con,$userID);
		$confirmed= mysqli_real_escape_string($con,"YES");
		$updateQuery = sprintf("UPDATE users SET confirmed = '%s' WHERE user_id = '%s'",$confirmed,$user_id);
		$con->query($updateQuery);
		
		if ($con->affected_rows > 0) {
			
			//user confirmation update successful.
			return TRUE;
		}
		
		//user confirmation update failed.
		return FALSE;
	}
	
	/**
	 *@description function that creates a new user
	 * @param username, password, email
	 *@return boolean TRUE or FALSE
	 */
	static function createUser ($uname,$pass,$eMail) {
		
		$con = self::openConnection();
		$date = date('Y-m-d H:i:s');
		$username = mysqli_real_escape_string($con,$uname);
		$password = mysqli_real_escape_string($con,$pass);
		$email = mysqli_real_escape_string($con,$eMail);
		$created_timestamp = mysqli_real_escape_string($con,$date);
		$confirmed = mysqli_real_escape_string($con,"NO");
		
		$createUser = sprintf("INSERT INTO users(username,password,created_timestamp,useremail,confirmed) VALUES('%s','%s','%s','%s','%s')", $username,$password,$created_timestamp,$email,$confirmed);
		$con->query($createUser);
		
		if ($con->affected_rows > 0) {
			
			//user created successfully
			return TRUE;
		}
		
		//user could not be created successfully
		return FALSE;
	}
	
	
	/**
	 *@description function that gets the user's password.
	 * @param username
	 *@return user password
	 */
	static function getUserPassword ($uName) {
		
		$con = self::openConnection();
		$username = mysqli_real_escape_string($con,$uName);
		
		$getPassword = sprintf("SELECT password FROM users WHERE username = '%s'",$username);
		$res = $con->query($getPassword);
		
		if ($con->affected_rows > 0) {
			
			//username exists
			$currentRow = $res->fetch_assoc();
			$password = stripslashes($currentRow["password"]);
			
			return $password;
		
		} else {
			
			return FALSE;
		}
	}
	
	
	/**
	 *@description function that gets the user's email address.
	 * @param username
	 *@return user password
	 */
	static function getUserEmail ($uName) {
		
		$con = self::openConnection();
		$username = mysqli_real_escape_string($con,$uName);
		
		$getEmail = sprintf("SELECT useremail FROM users WHERE username = '%s'",$username);
		$res = $con->query($getEmail);
		
		if ($con->affected_rows > 0) {
			
			//username exists
			$currentRow = $res->fetch_assoc();
			$email = stripslashes($currentRow["useremail"]);
			
			return $email;
		
		} else {
			
			return FALSE;
		}
	}
	
	/**
	 *@description function that gets the product data from the database
	 * @param sem3_id
	 *@return array products data
	 */
	static function getProduct ($semID) {
		
		$con = self::openConnection();
		$sem_id = mysqli_real_escape_string($con,$semID);
		$productData = array();
		$getProducts = sprintf("SELECT * FROM products WHERE sem3_id = '%s'",$sem_id);
		$res = $con->query($getProducts);
		
		if ($con->affected_rows > 0) {
			
			//product exists
			
			for ($x = 0; $x < $con->affected_rows; $x++) {
				
				$currentRow = $res->fetch_assoc();
				$product_name = stripslashes($currentRow["product_name"]);
				$product_model = stripslashes($currentRow["product_model"]);
				$product_price = stripslashes($currentRow["product_price"]);
				$product_color = stripslashes($currentRow["product_color"]);
				$seller = stripslashes($currentRow["seller"]);
				
				$productData[$x] = "$product_name,$product_model,$product_price,$product_color,$seller";
			}
			
			$numProducts = count($productData);
			thisLogger::debug("This porduct is sold at  $numProducts different stores.");
			return $productData;
		
		} else {
			
			thisLogger::debug("$sem_id could not be recognised");
			return FALSE;
		}
	}
}


