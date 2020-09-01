<?php
	// require hidden database credentials
	require('./secret/db-credentials.php');

	// setting variables
	$servername = $credentials['hostname'];
	$database = $credentials['base'];
	$username = $credentials['user'];
	$password = $credentials['password'];

	try {
		$conn = new PDO("mysql:host=$servername;dbname=$database", $username, $password);
		// set the PDO error mode to exception
		$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		//echo "Connected successfully";
	} catch(PDOException $e) {
		echo "Connection failed: " . $e->getMessage();
	}

	class ParseCSVFile
	{
		// returns CSV file as and array
		public function readCSVFile($csvFilename){

			$header = NULL;
			$returnArray = array();

			if(!file_exists($csvFilename) || !is_readable($csvFilename)){
				return FALSE;
			}

			if(($handle = fopen($csvFilename, 'r')) != FALSE){
				while($row = fgetcsv($handle, 1000, ',')){
					if(!$header){
						$header = $row;
					}else{
						$returnArray[] = array_combine($header, $row);
					}
				}
			}

			return $returnArray;
		}

		// returns parsed array
		// addresses cleaned up etc.
		public function parseCSVarray($csvArray){
			return array();
		}

		// writes array to database
		public function writeToDatabase(){
		}

	}

	$classInstance = new ParseCSVFile();
	$csvFileAsArray = $classInstance->readCSVFile('./data/datafile.csv');
	
	// test output
	print_r($csvFileAsArray);
	echo '<br /><br /><br />';
	echo $csvFileAsArray[0]['source_address_road'];
	echo '<br /><br /><br />';
	echo $csvFileAsArray[1]['source_address_road'];
?>