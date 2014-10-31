<?php
require_once 'dbConnection.class.php';
//require_once 'redisConnection.php';
require_once('ConnetAutoloader.php');

class thisLogger extends logger {
	
}

$day = date('Y-m-d');
thisLogger::setLogFilename("/var/log/connet/campaigns/pricecheck_signup$day.log");
thisLogger::setBacktrace();

define('ERROR_REPORTING',0);

//ENABLE LOGGING
if (ERROR_REPORTING) {
	clog::setLogLevel(0);
	thisLogger::setLogLevel(0);
	thisLogger::setVerbose();
}

/**
 *@author koketso mabuela
 * @descripton script that handles the signup of the pricecheck site
 */

$username = trim($_REQUEST["username"]);
$password = trim($_REQUEST["password"]);
$retypedPassword = trim($_REQUEST["retypedpassword"]);
$email = trim($_REQUEST["email"]);

if ($username != "" || $email != "" || $password != "" || $retypedPassword != "") {
	
	//compare the two passwords
	
	if (strcmp($password, $retypedPassword) === 0) {
	
		//check if there's a user with the same username as the submitted one
		if (dbConnection::checkUserExistence($username) === TRUE) {
			
			echo "Sorry that username is already in use, please enter a unique one";
			
		} else {
			
			//unique username provided, now registering the user
			if (dbConnection::createUser($username, $password, $email) === TRUE) {
				
				
				//get the user id
				$userID = dbConnection::getUserID($username);
				
				//generate random confirmation link, create a redis key and set the exipiry time as 10mins, then email the user the key
				$randomKey = mt_rand(100, 1000);
				$confirmationLink = "http://appstaging1.connet-systems.com/campaign/koketso-test/pricecheck/confirmSignUp.php?userid=$userID&key=$randomKey";
				
				if ($userID === FALSE) {
					
					mail("glenton92@gmail.com", "Price check User ID retrieval failed", "Script failed to retrieve user id from the dtabase, please urgently check what happened. Username is $username");
					
				} else {
					
					//create the redis key, using the user_id
					
					try {

						$redis = RedisConnection::getInstance('appstaging1;3');

						$confirmationKeySet = $redis->set("pricheckconfirmation:$userID",$randomKey);

						if ($confirmationKeySet == "OK") {

							//confirmation key has been set sucessfully.
							mail($email, "Splash Confirmation Email", "Hi\nPlease open this link $confirmationLink to confirm your email address.\nPlease note that this link can only be used once.\nRegards,\nSplash");
							echo "Thank you! We have sent you a confirmation link to your email address - please open it to complete your registration on Splash.";
							return TRUE;

						} else {

							//confirmation key could not be set 

							return FALSE;
						}

					} catch (Exception $ex) {

						echo "An error occured.";
					}	
					
					
				}
			}
			
		}
		
	} else {
		
		//when the passwords are not the same
		echo "Passwords do not match. Please make sure the passwords you submitted match.";
	}
	
} else {
	
	echo "Please make sure all fields are filled in.";
}