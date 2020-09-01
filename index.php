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

		// returns parsed array
		// cleaned up adresses, checked zip codes etc.
		public function parseCSVarray($csvArray){
			$parsedArray = array();
			foreach($csvArray as $source){
				// only add source if active or inactive
				if($source['source_status'] == 'active' || $source['source_status'] == 'inactive'){
					// only add source if address if valid
					if($this->isValidAddress($source['source_address_road'], $source['source_address_zip'], $source['source_address_city'])){
						array_push($parsedArray, $source);
					}
				}
			}

			print_r($parsedArray);
			return $parsedArray;
		}

		// writes array to database
		public function writeToDatabase($dbConnection, $parsedArray){
			foreach($parsedArray as &$row){
				$insertStatement = "INSERT INTO php_opgave (source_id, source_status, source_name, source_desc, source_address_road, source_address_zip, source_address_city, source_external_id, source_latitude, source_longitude) VALUES('$row[source_id]', '$row[source_status]', '$row[source_name]', '$row[source_desc]', '$row[source_address_road]', '$row[source_address_zip]', '$row[source_address_city]', '$row[source_external_id]', '$row[source_latitude]', '$row[source_longitude]')";
				$dbConnection->exec($insertStatement);
			}
		}

		// returns true if address is valid according to ruleset
		public function isValidAddress($roadname, $zipcode, $city){
			
			// no house number ?
			if(preg_match('/\\d/', $roadname) == 0){
				return false;
			}

			// is zipcode 4 characters and ciphers only ?
			if(strlen($zipcode) != 4 || preg_match_all("/\d/", $zipcode) != 4){
				return false;
			}

			// is city name less than 3 character ?
			if(strlen($city) < 3){
				return false;
			}

			return true;
		}
	}

	// program execution

	// database conection
	$dbConnection = require('./database/db-connection.php');

	// instantiation of class
	$classInstance = new ParseCSVFile();

	// read CSV file into array
	$csvFileAsArray = $classInstance->readCSVFile('./data/datafile.csv');

	// parse CSV array according to rules
	$parsedArray = $classInstance->parseCSVarray($csvFileAsArray);

	// print result to screen - not in task
	var_dump($parsedArray);

	// write to database
	$classInstance->writeToDatabase($dbConnection, $parsedArray);
?>