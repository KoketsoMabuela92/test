<?php
require_once 'dbConnection.class.php';
//require_once 'redisConnection.php';
require_once('ConnetAutoloader.php');

class thisLogger extends logger {
	
}

$day = date('Y-m-d');
thisLogger::setLogFilename("/var/log/connet/campaigns/pricecheck_getPass$day.log");
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
 * @descripton script that handles the retrieval of a Splash user's password
 */

$username = trim($_REQUEST["username"]);


if (!is_null($username)) {
	
	thisLogger::debug("Username submitted is not null. Now getting the user's password.");
	
	//first check if the user has confirmed their email
	if (dbConnection::checkUserConfirmationWithUsername($username) === FALSE) {
		
		echo "Sorry we cannot retrieve your password due to security measures, because you have not yet confirmed your email address.";
		die();
	}
	
	
	$password = dbConnection::getUserPassword($username);
	
	if ($password === FALSE) {
		
		echo "Sorry your password could not be retrieved. Please make sure you provide use with the correct email. or contact the Webmaster on (+27)789585591";
		
	} else {
		
		thisLogger::debug("Getting the user's email.");
		$email = dbConnection::getUserEmail($username);
		
		mail($email, "Your Splash password", "Hi $username,\nThis is your Splash password: $password\nRegards,\nSplash Team");
		
		echo "Thank you $username, your password has been sent to your email.";
		die();
	}
	
} else {
	
	echo "Invalid username, please sure you provide us with your username.";
	die();
}


