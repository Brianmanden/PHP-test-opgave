<?php
	class ParseCSVFile
	{
		//returns parsed CSV file as an associated array
		public function readCSVFile($csvFilename){

			$header = NULL;
			$returnArray = array();

			if(!file_exists($csvFilename) || !is_readable($csvFilename)){
				return FALSE;
			}

			if(($handle = fopen($csvFilename, 'r')) != FALSE){
				$sourceCounter = 0;
				while($source = fgetcsv($handle, 1000, ',')){
					if( 0 === $sourceCounter) {
						$headerRecord = $source;
					} else {
						foreach( $source as $key => $value) {
							$returnArray[ $sourceCounter - 1][ $headerRecord[ $key] ] = $value;  
						}
					}
					$sourceCounter++;
				}
			}

			return $returnArray;
		}

		// TODO - clean up adresses etc.
		// returns parsed array
		public function parseCSVarray($csvArray){
			$parsedArray = array();
			foreach($csvArray as $source){
				if($source['source_status'] == 'active' || $source['source_status'] == 'inactive'){
					array_push($parsedArray, $source);
				}
			}

			return $parsedArray;
		}

		// writes array to database
		public function writeToDatabase($dbConnection, $parsedArray){
			foreach($parsedArray as &$row){
				$insertStatement = "INSERT INTO php_opgave (source_id, source_status, source_name, source_desc, source_address_road, source_address_zip, source_address_city, source_external_id, source_latitude, source_longitude) VALUES('$row[source_id]', '$row[source_status]', '$row[source_name]', '$row[source_desc]', '$row[source_address_road]', '$row[source_address_zip]', '$row[source_address_city]', '$row[source_external_id]', '$row[source_latitude]', '$row[source_longitude]')";
				$dbConnection->exec($insertStatement);
			}
		}
	}

	// program execution

	// database conection
	$dbConnection = require('./database/db-connection.php');

	// instantiation
	$classInstance = new ParseCSVFile();
	// read CSV file into array
	$csvFileAsArray = $classInstance->readCSVFile('./data/datafile.csv');
	print_r("<br />");
	print_r("<br />");
	print_r("<br />");
	// parse CSV array according to rules
	$parsedArray = $classInstance->parseCSVarray($csvFileAsArray);
	$classInstance->writeToDatabase($dbConnection, $parsedArray);
?>