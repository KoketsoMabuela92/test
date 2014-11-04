<?php

require_once 'dbConnection.class.php';
require_once('ConnetAutoloader.php');
require_once 'semantics3-php/lib/Semantics3.php';
//require_once 'semantics3-php/lib/oauth-php';

/*class thisLogger extends logger {
	
}*/

$day = date('Y-m-d');
thisLogger::setLogFilename("/var/log/connet/campaigns/pricecheck_updateProductInfo$day.log");
thisLogger::setBacktrace();

//define('ERROR_REPORTING',0);

//ENABLE LOGGING
if (ERROR_REPORTING) {
	clog::setLogLevel(0);
	thisLogger::setLogLevel(0);
	thisLogger::setVerbose();
}

/**
 *@author koketso mabuela
 * @descripton script that is invoke by a cron deamon daily at 00:00:00, to update the products info
 */

$key = 'SEM33E2E4446B67C43489943691902411B3F';
$secret = 'NDY0YjE0MzZkNTE4MDFkM2QwNzFmNDQ0ODZhODJiYzY';



//echo $json_response;

$arrayOfProducts = dbConnection::getSem3Ids();
$sizeOfOriginalArray = count($arrayOfProducts);

thisLogger::debug("The size of the original array is; ",$sizeOfOriginalArray);

for ($x = 0; $x < $sizeOfOriginalArray; $x++) {
	
	$individualProducts = explode(",", $arrayOfProducts[$x]);
	$sem3_id = $individualProducts[0];
	$seller = $individualProducts[1];
	$db_product_id = $individualProducts[2];
	
	
	//------------------------------------------------------------
	//Now getting the latest price from the API
	//-----------------------------------------------------------
	$params = array(
		
		'sem3_id'=> $sem3_id,
		'seller'=>$seller
	);

	$jsonparams = json_encode($params);

	$url = "https://api.semantics3.com/test/v1/products?q=$jsonparams";
	$curl = curl_init($url);
	curl_setopt($curl, CURLOPT_HEADER, false);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($curl, CURLOPT_HTTPHEADER,
			array("Content-type: application/json","api_key: SEM33E2E4446B67C43489943691902411B3F"));

	$json_response = curl_exec($curl);
		
	$decodeJson = json_decode($json_response,TRUE);
	$apiresultsCode = $decodeJson["code"];
	
	//print_r($decodeJson);
	
	if (strcmp($apiresultsCode, "OK") === 0) {
		
		thisLogger::debug("API call was successful.");
		
		//now retriving the product price and updating the database
		$resultsArray = $decodeJson["results"];
		
		print_r($resultsArray);
		//thisLogger::debug("THE RESULTS ARE: ",$resultsArray);
		
		
	/* if (dbConnection::updatePrice($db_product_id, $latesPrice) === TRUE) {
		 
		 thisLogger::debug("Update was successful");
		 
	 } else {
		 
		 //sendign an error mail tpo the admin when the app cannot update the updte the product price.
		mail("koketso@connet-systems.com", "PRICE CHECK ERROR", "Price Check application could not update product price.\nPlease urgently check this isssue.\nThe product ID: $db_product_id");
		 
	 }*/
		
	} else {
		
		thisLogger::debug("API call was unsuccessful.");
		die();
	}
}

die();

