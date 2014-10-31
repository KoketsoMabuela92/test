<html>
	
	<head>
		<title></title>
	</head>
	<body>
<?php
require_once 'dbConnection.class.php';
require_once('ConnetAutoloader.php');

class thisLogger extends logger {
	
}

$day = date('Y-m-d');
thisLogger::setLogFilename("/var/log/connet/campaigns/pricecheck_comparePrices$day.log");
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
	$sem_id = trim($_REQUEST["id"]);
	$productName = trim($_REQUEST["name"]);
	
	thisLogger::debug("Now getting the products for price comparisons");
	$productDataArray = dbConnection::getProduct($sem_id);
	$numOfStores = count($productDataArray);
?>
		<p>This product is currently available at <?php echo $numOfStores?> different stores.</p>
		
		<p><h2><b><?php echo $productName?> Info</b></h2> 
			
			<?php 
			
				for ($x = 0; $x < $numOfStores; $x++) {

					//extract the data from its original array, and store it in its own separate array.
					$individualStore = explode(",", $productDataArray[$x]);
										
					$data = "<table><tr></td></tr>
								<tr></td></tr>
								<tr></td></tr>
								<tr></td></tr>
								<tr></td></tr>
								<tr><td><b>Store</b></td><td>" . htmlentities($individualStore[4]) . "</td></tr>
								<tr><td><b>Model</b></td><td>" . htmlentities($individualStore[1]) . "</td></tr>
								<tr><td><b>Price</b></td><td>$" . htmlentities($individualStore[2]) . "</td></tr>
								<tr><td><b>Colour</b></td><td>" . htmlentities($individualStore[3]) . "</td></tr>
							</table>";
					
					echo $data;
				}
			
			?>
		
		</p>
		<a href="categories.html">Back</a> | <a href="index.html">Home</a>
	</body>
</html>

