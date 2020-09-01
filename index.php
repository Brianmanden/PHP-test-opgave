<?php
	class ParseCSVFile
	{
		//returns parsed CSV file as and array
		public function readCSVFile($csvFilename){

			$header = NULL;
			$returnArray = array();

			if(!file_exists($csvFilename) || !is_readable($csvFilename)){
				return FALSE;
			}

			if(($handle = fopen($csvFilename, 'r')) != FALSE){
				while($row = fgetcsv($handle, 1000, ',')){
					// excluding header
					if(!$header){
						$header = $row;
					}else{
						// $returnArray[] = array_combine($header, $row);
						array_push($returnArray, $row);
					}
				}
			}

			return $returnArray;
		}

		// TODO - clean up adresses etc.
		// returns parsed array
		public function parseCSVarray($csvArray){
			return $csvArray;
		}

		// writes array to database
		public function writeToDatabase($dbConnection, $parsedArray){
			foreach($parsedArray as &$source){
				$insertStatement = "INSERT INTO php_opgave (source_id, source_status, source_name, source_desc, source_address_road, source_address_zip, source_address_city, source_external_id, source_latitude, source_longitude) VALUES('$source[0]', '$source[1]', '$source[2]', '$source[3]', '$source[4]', '$source[5]', '$source[6]', '$source[7]', '$source[8]', '$source[9]')";
				$dbConnection->exec($insertStatement);
			}
		}
	}

	
	$classInstance = new ParseCSVFile();
	$csvFileAsArray = $classInstance->readCSVFile('./data/datafile.csv');
	$parsedArray = $classInstance->parseCSVarray($csvFileAsArray);
	$dbConnection = require('./database/db-connection.php');
	$classInstance->writeToDatabase($dbConnection, $parsedArray);
?>