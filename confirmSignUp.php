<?php
require_once 'dbConnection.class.php';
//require_once 'redisConnection.php';
require_once('ConnetAutoloader.php');

class thisLogger extends logger {
	
}

$day = date('Y-m-d');
thisLogger::setLogFilename("/var/log/connet/campaigns/pricecheck_confirmsignup$day.log");
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
 * @descripton script that handles the completion of the registration process
 */

$userid = trim($_REQUEST["userid"]);
$confirmationkey = trim($_REQUEST["key"]);

if (!is_null($confirmationkey) || !is_null($userid)) {
	
	//check if the redis key exists
	try {			
			$redis = RedisConnection::getInstance('appstaging1;3');
			$key = $redis->Get("pricheckconfirmation:$userid");
			
			if (strcasecmp($key, $confirmationkey) === 0) {
				
				$lifeSpan = 15;
				//key exists, now lets expire it
				try {
					$expireDownloadKey = $redis->expire("pricheckconfirmation:$userid",$lifeSpan);

					if ($expireDownloadKey == 1) {

						thisLogger::debug('The download key has been set to expire');

						//return TRUE;

					} else {

						thisLogger::debug('The download key could not be set to expire');

						die();
						//return FALSE;
					}

				} catch (Exception $ex) {

					thisLogger::debug('Redis Exception Message : ', $ex->getMessage());
				}
				
				thisLogger::debug("check if the user has confirmed their email before.");
				//$isEmailConfirmed = dbConnection::checkUserConfirmation($userid);
				
				if (dbConnection::checkUserConfirmation($userid) === FALSE) {
					
					if (dbConnection::updateConfirmation($userid) === TRUE) {
						
						thisLogger::debug("Now updating the confirmation field in the user's table.");
						//when all went well
						header("Location: signin.html");
						
					} else {
						
						thisLogger::debug("Could not update confrimation email.");
						echo "An error has occurred, please contact the Webmaster on (+27)789585591 for further assistance with your registration process.";
					}
					
				} else {
					
					//when all went well
					header("Location: signin.html");
				}				
				
			} else {
				
				echo "Sorry the link is invalid and cannot be used.";
				
			}
			
		} catch (Exception $ex) {
			
			thisLogger::debug('Redis Exception Message : ', $ex->getMessage());
		}
	
} else {
	
	echo "Invalid confirmation link. Please make sure that you open the link that was sent to you email.";
}
