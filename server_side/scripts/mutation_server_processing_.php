<?php

/*
 * DataTables example server-side processing script.
 *
 * Please note that this script is intentionally extremely simple to show how
 * server-side processing can be implemented, and probably shouldn't be used as
 * the basis for a large complex system. It is suitable for simple use cases as
 * for learning.
 *
 * See http://datatables.net/usage/server-side for full details on the server-
 * side processing requirements of DataTables.
 *
 * @license MIT - http://datatables.net/license_mit
 */

/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 * Easy set variables
 */

 $config = include( 'config.php' );

// DB table to use
$table = 'mutation';

// Table's primary key
$primaryKey = 'ID';

// Array of database columns which should be read and sent back to DataTables.
// The `db` parameter represents the column name in the database, while the `dt`
// parameter represents the DataTables column identifier. In this case simple
// indexes

$columns = array(
	array( 'db' => 'ID', 'dt'=>0),
	array( 'db' => 'CHROM', 'dt'=>1),
	array( 'db' => 'POS', 'dt'=>2),
	array( 'db' => 'Uploaded_variation', 'dt'=>3),
	array( 'db' => 'REF', 'dt'=>4),
	array( 'db' => 'ALT', 'dt'=>5),
	array( 'db' => 'CLNREVSTAT', 'dt'=>6),
	array( 'db' => 'clin_sig', 'dt'=>7),
	array( 'db' => 'Consequence', 'dt'=>8),
	array( 'db' => 'Existing_variation', 'dt'=>9),
	array( 'db' => 'SWISSPROT', 'dt'=>10),
	array( 'db' => 'TREMBL', 'dt'=>11),
	array( 'db' => 'Protein_position', 'dt'=>12),
	array( 'db' => 'Amino_acids', 'dt'=>13),
	array( 'db' => 'canonical', 'dt'=>14),
	array( 'db' => 'SIFT_type', 'dt'=>15),
	array( 'db' => 'SIFT_score', 'dt'=>16),
	array( 'db' => 'Polyphen_type', 'dt'=>17),
	array( 'db' => 'Polyphen_score', 'dt'=>18),
	array( 'db' => 'PPI', 'dt'=>19),
	array( 'db' => '3Dmapper_result', 'dt'=>20),
	array( 'db' => 'Gene', 'dt'=>21),
	array( 'db' => 'Feature', 'dt'=>22),
	array( 'db' => 'Gene_symbol', 'dt'=>23),
);

// SQL server connection information
$sql_details = array(
	'user' => $config->dbUser,
	'pass' => $config->dbPass,
	'db'   => $config->dbName,
	'host' => $config->dbHost
);


/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 * If you just want to use the basic configuration for DataTables with PHP
 * server-side, there is no need to edit below this line.
 */

require( 'ssp.class.php' );

echo json_encode(
	SSP::simple( $_GET, $sql_details, $table, $primaryKey, $columns )
);


