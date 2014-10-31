<?php
require_once 'dbConnection.class.php';

require_once('ConnetAutoloader.php');

class thisLogger extends logger {
	
}

$day = date('Y-m-d');
thisLogger::setLogFilename("/var/log/connet/campaigns/pricecheck_signin$day.log");
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
 * @descripton script that handles the signin of the pricecheck site
 */

$username = trim($_REQUEST['username']);
$password = trim($_REQUEST['password']);

//first check if the user has confirmed their email address before trying to sign them in
if (dbConnection::checkUserConfirmationWithUsername($username) === FALSE){
	
	thisLogger::debug("User has not yet confirmed their password.");
	echo "Sorry we cannot sign you in due to security measures, because you have not yet confirmed your email address.";
	die();
}

if (dbConnection::authenticateuser($username,$password) === TRUE) {
	
	thisLogger::debug("Authentication successful");
	//route to the categories page, when user crdentials are valid
	header('Location: categories.html');

} else {

	//invalid user credentials
	thisLogger::debug("Authentication unsuccessful");
	header('Location: invalidLogIn.php');
}
